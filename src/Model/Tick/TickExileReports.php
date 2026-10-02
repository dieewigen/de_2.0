<?php
namespace DieEwigen\DE2\Model\Tick;

use DieEwigen\DE2\Model\Exile\ExileMail;
use DieEwigen\DE2\Model\Exile\ExileService;

/**
 * Lageberichte von Fluxurion an Spieler im Exil (Sektor 1), gedrosselt verschickt.
 *
 * Pro Wirtschaftstick höchstens eine Mail, nur im Versandfenster und bis zum Stundenlimit,
 * damit der Versand beim Hoster nicht wie Spam aussieht.
 * Priorität: Bericht zur neuen Runde vor dem fälligen Lagebericht.
 */
class TickExileReports
{
    private \mysqli $db;
    private ExileService $service;
    private ExileMail $mail;

    public function __construct(\mysqli $db, ExileService $service, ExileMail $mail)
    {
        $this->db = $db;
        $this->service = $service;
        $this->mail = $mail;
    }

    /**
     * @return array{sent:bool, reason?:string, user_id?:int, type?:string}
     */
    public function run(): array
    {
        //ohne Serveradresse gäbe es keinen Abmeldelink, dann wird nichts verschickt
        $serverUrl = rtrim(trim($GLOBALS['sv_server_url'] ?? ''), '/');
        if ($serverUrl === '') {
            return ['sent' => false, 'reason' => 'no_server_url'];
        }

        [$hourFrom, $hourTo] = $GLOBALS['sv_exile_mail_hours'] ?? [9, 21];
        $hour = (int)date('G');
        if ($hour < (int)$hourFrom || $hour >= (int)$hourTo) {
            return ['sent' => false, 'reason' => 'outside_hours'];
        }

        //Stundenbeginn in SQL berechnen, damit PHP- und MySQL-Zeitzone nicht auseinanderlaufen können
        $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS anz FROM de_user_exile WHERE last_mail_at >= DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')", []);
        if ((int)(mysqli_fetch_assoc($res)['anz'] ?? 0) >= (int)($GLOBALS['sv_exile_mails_per_hour'] ?? 10)) {
            return ['sent' => false, 'reason' => 'hour_limit'];
        }

        //in der Rundenpause wären Reserve und Rundenstand überholt
        if ($this->service->isRoundOver()) {
            return ['sent' => false, 'reason' => 'round_over'];
        }

        $row = $this->findCandidate();
        if ($row === null) {
            return ['sent' => false, 'reason' => 'no_candidate'];
        }

        $uid = (int)$row['user_id'];
        $offsets = $this->offsets();
        $passed = (int)$row['passed'];
        if ((int)$row['newround_due'] === 1) {
            $type = 'neue_runde';
        } else {
            //höchster fälliger Bericht: der erste Termin ist bericht1, der letzte bericht3, alle dazwischen bericht2
            $type = $passed >= count($offsets) ? 'bericht3' : ($passed <= 1 ? 'bericht1' : 'bericht2');
        }

        $data = $this->service->buildMailData($row);
        $data['optout_url'] = $serverUrl.'/exile_optout.php?u='.$uid.'&t='.$this->service->ensureToken($uid);
        $sent = $this->mail->send($row['reg_mail'], $this->mail->render($type, $data));

        if ($sent) {
            $sys = $this->service->getSystem();
            //der Rundenbericht ersetzt fällige Lageberichte, statt sie zusätzlich auszulösen
            mysqli_execute_query($this->db, "UPDATE de_user_exile SET last_mail_at = NOW(), reports_sent = GREATEST(reports_sent, ?),
                newround_sent = IF(?, ?, newround_sent) WHERE user_id = ?", [$passed, $type === 'neue_runde' ? 1 : 0, $sys['rundenstart_datum'], $uid]);
        } else {
            //Fehlversuch zählt zum Stundenlimit, der Bericht wird nach der Sperrfrist erneut versucht
            mysqli_execute_query($this->db, "UPDATE de_user_exile SET last_mail_at = NOW() WHERE user_id = ?", [$uid]);
        }

        return ['sent' => $sent, 'user_id' => $uid, 'type' => $type];
    }

    /**
     * Nächstes Konto mit fälliger Mail: zuerst nie angeschriebene, dann die am längsten nicht angeschriebenen,
     * innerhalb davon die zuletzt geparkten (sie kehren am ehesten zurück).
     */
    private function findCandidate(): ?array
    {
        $sys = $this->service->getSystem();
        $offsets = $this->offsets();
        $maxNewroundDays = (int)($GLOBALS['sv_exile_newround_maxdays'] ?? 365);

        //Anzahl der verstrichenen Berichtstermine, Werte sind durch offsets() bereits Ganzzahlen
        $passed = '0';
        foreach ($offsets as $offset) {
            $passed .= ' + (DATEDIFF(NOW(), e.since) >= '.$offset.')';
        }
        //wer länger als 14 Tage über dem letzten Termin liegt, bekommt keinen nachgeholten Bericht mehr
        $staleDays = end($offsets) + 14;
        $newroundDue = "((e.newround_sent IS NULL OR e.newround_sent <> ?) AND e.since >= NOW() - INTERVAL ? DAY)";

        $sql = "SELECT e.*, l.reg_mail, d.spielername, d.col, d.score, d.allytag, d.status AS ally_status,
                  ($passed) AS passed, $newroundDue AS newround_due
                FROM de_user_exile e
                LEFT JOIN de_login l ON (l.user_id = e.user_id)
                LEFT JOIN de_user_data d ON (d.user_id = e.user_id)
                WHERE l.status = 3 AND l.delmode = 2 AND l.lageberichte = 1 AND l.reg_mail <> '' AND d.npc = 0
                  AND e.returned_at IS NULL
                  AND (e.last_mail_at IS NULL OR e.last_mail_at < NOW() - INTERVAL 3 DAY)
                  AND ($newroundDue OR (($passed) > e.reports_sent AND DATEDIFF(NOW(), e.since) <= $staleDays))
                ORDER BY (e.last_mail_at IS NOT NULL), e.last_mail_at, e.since DESC
                LIMIT 1";
        $round = $sys['rundenstart_datum'];
        $res = mysqli_execute_query($this->db, $sql, [$round, $maxNewroundDays, $round, $maxNewroundDays]);
        $row = $res ? mysqli_fetch_assoc($res) : null;

        return $row ?: null;
    }

    /**
     * @return int[] aufsteigend sortierte Berichtstermine in Tagen
     */
    private function offsets(): array
    {
        $offsets = array_map('intval', $GLOBALS['sv_exile_report_days'] ?? [3, 14, 45]);
        $offsets = array_values(array_filter($offsets, fn (int $o): bool => $o > 0));
        sort($offsets);

        return $offsets ?: [3, 14, 45];
    }
}
