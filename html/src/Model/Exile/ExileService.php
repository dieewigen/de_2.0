<?php
namespace DieEwigen\DE2\Model\Exile;

/**
 * Exil-Akte für inaktive Spieler, die vom Wirtschaftstick in Sektor 1 geparkt wurden.
 *
 * Fluxurion der Berater hält während der Abwesenheit die Stellung: Er schreibt Lageberichte
 * (TickExileReports), sammelt eine Exilreserve und übergibt sie bei der Rückkehr.
 * Die Exilreserve folgt der Formel der Späteinsteigerhilfe (rpc.php): ein Fünftel des
 * planetaren Grundertrags je verpasstem Wirtschaftstick der laufenden Runde.
 */
class ExileService
{
    private \mysqli $db;
    private ?array $system = null;
    private ?array $universe = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Rundendaten aus de_system, einmal pro Instanz gelesen.
     */
    public function getSystem(): array
    {
        if ($this->system === null) {
            $res = mysqli_execute_query($this->db, "SELECT wt, rundenstart_datum, winid, winticks, doetick FROM de_system LIMIT 1", []);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $this->system = $row ?: ['wt' => 1, 'rundenstart_datum' => null, 'winid' => 0, 'winticks' => 0, 'doetick' => 1];
        }
        return $this->system;
    }

    public function getRow(int $uid): ?array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_user_exile WHERE user_id = ?", [$uid]);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        return $row ?: null;
    }

    /**
     * Exil-Akte beim Parken anlegen oder für eine neue Exil-Phase zurücksetzen.
     * Das Abmelde-Token bleibt erhalten.
     */
    public function registerExile(int $uid, int $fromSector, int $col): void
    {
        $sys = $this->getSystem();
        $wt = max(1, (int)$sys['wt']);
        //wer erst jetzt geparkt wird, hat den Start der laufenden Runde selbst erlebt
        $sql = "INSERT INTO de_user_exile (user_id, since, from_sector, col_at_exile, wt_at_exile, round_start, reports_sent, newround_sent, last_mail_at, returned_at, reserve_m, reserve_d, closed)
                VALUES (?, NOW(), ?, ?, ?, ?, 0, ?, NULL, NULL, 0, 0, 0)
                ON DUPLICATE KEY UPDATE since = VALUES(since), from_sector = VALUES(from_sector), col_at_exile = VALUES(col_at_exile),
                  wt_at_exile = VALUES(wt_at_exile), round_start = VALUES(round_start), reports_sent = 0, newround_sent = VALUES(newround_sent),
                  last_mail_at = NULL, returned_at = NULL, reserve_m = 0, reserve_d = 0, closed = 0";
        mysqli_execute_query($this->db, $sql, [$uid, $fromSector, $col, $wt, $sys['rundenstart_datum'], $sys['rundenstart_datum']]);
    }

    public function deleteExile(int $uid): void
    {
        mysqli_execute_query($this->db, "DELETE FROM de_user_exile WHERE user_id = ?", [$uid]);
    }

    /**
     * Wurde der Server seit dem Parken zurückgesetzt? Erkennt reset.sql und wt_auto_reset.php ohne eigene Hooks.
     */
    public function roundChanged(array $row): bool
    {
        $sys = $this->getSystem();
        return (string)$row['round_start'] !== (string)$sys['rundenstart_datum'] || (int)$sys['wt'] < (int)$row['wt_at_exile'];
    }

    /**
     * Runde vorbei, Server wartet auf den Reset (gleiche Prüfung wie kt.php und overview.php).
     */
    public function isRoundOver(): bool
    {
        $sys = $this->getSystem();
        return (int)$sys['winticks'] <= 1 && (int)$sys['doetick'] == 0 && (int)$sys['winid'] > 0;
    }

    /**
     * Exilreserve: ein Fünftel des planetaren Grundertrags je verpasstem Wirtschaftstick der laufenden Runde.
     *
     * @return array{0:int, 1:int} Multiplex, Dyharra
     */
    public function computeReserve(array $row): array
    {
        //wie die Späteinsteigerhilfe nicht in der ewigen Runde
        if ((int)($GLOBALS['sv_ewige_runde'] ?? 0) === 1) {
            return [0, 0];
        }
        $sys = $this->getSystem();
        $base = $this->roundChanged($row) ? 1 : (int)$row['wt_at_exile'];
        $ticks = min(max(0, (int)$sys['wt'] - $base), 40000);
        $grundertrag = $GLOBALS['sv_plan_grundertrag'] ?? [1000, 125];

        return [(int)round($grundertrag[0] * $ticks / 5), (int)round($grundertrag[1] * $ticks / 5)];
    }

    /**
     * Rückkehr beim Login verbuchen: Exilreserve gutschreiben, Spieler und Allianz informieren.
     * Atomar über returned_at, damit die Reserve auch bei parallelen Logins nur einmal fließt.
     *
     * @return bool true, wenn die Rückkehr mit diesem Aufruf verbucht wurde
     */
    public function processReturn(int $uid, array $lang): bool
    {
        $row = $this->getRow($uid);
        //in der Rundenpause würde der Reset die Reserve wieder löschen, daher erst in der neuen Runde
        if ($row === null || $row['returned_at'] !== null || $this->isRoundOver()) {
            return false;
        }

        [$m, $d] = $this->computeReserve($row);
        mysqli_execute_query($this->db, "UPDATE de_user_exile SET returned_at = NOW(), reserve_m = ?, reserve_d = ? WHERE user_id = ? AND returned_at IS NULL", [$m, $d, $uid]);
        if (mysqli_affected_rows($this->db) !== 1) {
            return false;
        }

        $time = date('YmdHis');
        if ($m > 0 || $d > 0) {
            mysqli_execute_query($this->db, "UPDATE de_user_data SET restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, newnews = 1 WHERE user_id = ?", [$m, $d, $uid]);
            $text = strtr($lang['news_heimkehr_reserve'], ['{M}' => self::formatNumber($m), '{D}' => self::formatNumber($d)]);
        } else {
            mysqli_execute_query($this->db, "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uid]);
            $text = $lang['news_heimkehr'];
        }
        mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $text]);

        //Allianzmitglieder informieren
        $res = mysqli_execute_query($this->db, "SELECT spielername, ally_id, status FROM de_user_data WHERE user_id = ?", [$uid]);
        $user = $res ? mysqli_fetch_assoc($res) : null;
        if ($user && (int)$user['ally_id'] > 0 && (int)$user['status'] === 1) {
            $allyText = strtr($lang['news_allianz'], ['{NAME}' => htmlspecialchars($user['spielername'], ENT_QUOTES, 'UTF-8')]);
            $res = mysqli_execute_query($this->db, "SELECT user_id FROM de_user_data WHERE ally_id = ? AND status = 1 AND npc = 0 AND user_id <> ?", [(int)$user['ally_id'], $uid]);
            while ($res && $member = mysqli_fetch_assoc($res)) {
                mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 6, ?, ?)", [$member['user_id'], $time, $allyText]);
                mysqli_execute_query($this->db, "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$member['user_id']]);
            }
        }

        //Vermerk für den Support
        $note = "\n".date('Y-m-d H:i:s').' '.strtr($lang['kommentar'], ['{M}' => self::formatNumber($m), '{D}' => self::formatNumber($d)]);
        mysqli_execute_query($this->db, "UPDATE de_user_info SET kommentar = CONCAT(IFNULL(kommentar, ''), ?) WHERE user_id = ?", [$note, $uid]);

        return true;
    }

    /**
     * Text der Heimkehr-Box auf der Übersicht, leer wenn nichts anzuzeigen ist.
     * Nach der Verlegung in einen regulären Sektor oder spätestens nach 3 Tagen wird die Akte geschlossen.
     */
    public function overviewText(int $uid, int $sector, int $col, float $score, array $lang): string
    {
        $row = $this->getRow($uid);
        if ($row === null || (int)$row['closed'] === 1) {
            return '';
        }

        //falls die Rückkehr beim Login nicht verbucht wurde, z. B. weil sie in die Rundenpause fiel
        if ($row['returned_at'] === null && $this->processReturn($uid, $lang)) {
            $row = $this->getRow($uid);
        }

        if ($row['returned_at'] === null) {
            return $this->isRoundOver() ? $lang['box_rundenpause'] : '';
        }
        if ($sector > 1) {
            //Verlegung abgeschlossen, einmal begrüßen und die Akte schließen
            $this->close($uid);
            return strtr($lang['box_angekommen'], ['{SECTOR}' => $sector]);
        }
        if (strtotime($row['returned_at']) < time() - 3 * 86400) {
            $this->close($uid);
            return '';
        }

        $days = max(0, (int)floor((strtotime($row['returned_at']) - strtotime($row['since'])) / 86400));
        $parts = [strtr($lang['box_abwesend'], ['{DAYS}' => $days])];
        if ((int)$row['reserve_m'] > 0 || (int)$row['reserve_d'] > 0) {
            $parts[] = strtr($lang['box_reserve'], ['{M}' => self::formatNumber((int)$row['reserve_m']), '{D}' => self::formatNumber((int)$row['reserve_d'])]);
        }
        $lost = (int)$row['col_at_exile'] - $col;
        if ($lost > 0 && !$this->roundChanged($row)) {
            $parts[] = strtr($lang['box_verlust'], ['{LOST}' => self::formatNumber($lost)]);
        }
        //gleiche Grenze wie der Umzug aus Sektor 1 in tickler/wt.php
        $parts[] = ($col >= 10 || $score >= 5000000) ? $lang['box_schritt_umzug'] : $lang['box_schritt_sektor1'];

        return implode('<br><br>', $parts);
    }

    /**
     * Status der Exil-Akte schließen, die Heimkehr-Box auf der Übersicht erscheint dann nicht mehr.
     */
    public function close(int $uid): void
    {
        mysqli_execute_query($this->db, "UPDATE de_user_exile SET closed = 1 WHERE user_id = ?", [$uid]);
    }

    /**
     * Mail von Fluxurion für die Statistik im Admintool protokollieren.
     * Fehler werden nur geloggt, das Protokoll darf weder den Versand noch den Tick aufhalten.
     */
    public function logMail(int $uid, string $type, bool $ok): void
    {
        try {
            mysqli_execute_query($this->db, "INSERT INTO de_user_exile_mail (user_id, type, sent_at, ok) VALUES (?, ?, NOW(), ?)", [$uid, $type, $ok ? 1 : 0]);
        } catch (\Throwable $e) {
            error_log('Exil-Mailprotokoll fehlgeschlagen: '.$e->getMessage());
        }
    }

    /**
     * Beim Login vermerken, dass der Spieler nach seinen Mails von Fluxurion zurückgekommen ist.
     */
    public function recordLogin(int $uid): void
    {
        try {
            mysqli_execute_query($this->db, "UPDATE de_user_exile_mail SET login_at = NOW() WHERE user_id = ? AND login_at IS NULL", [$uid]);
        } catch (\Throwable $e) {
            error_log('Exil-Mailprotokoll (Login) fehlgeschlagen: '.$e->getMessage());
        }
    }

    /**
     * Abmelde-Token für die Lageberichte liefern und bei Bedarf erzeugen.
     */
    public function ensureToken(int $uid): string
    {
        $row = $this->getRow($uid);
        if ($row === null) {
            return '';
        }
        if ($row['optout_token'] !== '') {
            return $row['optout_token'];
        }
        mysqli_execute_query($this->db, "UPDATE de_user_exile SET optout_token = ? WHERE user_id = ? AND optout_token = ''", [bin2hex(random_bytes(16)), $uid]);
        $row = $this->getRow($uid);

        return $row['optout_token'] ?? '';
    }

    public function isValidToken(int $uid, string $token): bool
    {
        $row = $this->getRow($uid);
        return $row !== null && $row['optout_token'] !== '' && hash_equals($row['optout_token'], $token);
    }

    /**
     * Lageberichte abbestellen. Gilt auch für spätere Exil-Phasen.
     */
    public function optOut(int $uid, string $token): bool
    {
        if (!$this->isValidToken($uid, $token)) {
            return false;
        }
        mysqli_execute_query($this->db, "UPDATE de_login SET lageberichte = 0 WHERE user_id = ?", [$uid]);

        return true;
    }

    /**
     * Daten eines Kontos für einen Lagebericht laden (Exil-Akte, Login, Spielerdaten).
     */
    public function loadMailRow(int $uid): ?array
    {
        $sql = "SELECT e.*, l.reg_mail, d.spielername, d.col, d.score, d.allytag, d.status AS ally_status
                FROM de_user_exile e
                LEFT JOIN de_login l ON (l.user_id = e.user_id)
                LEFT JOIN de_user_data d ON (d.user_id = e.user_id)
                WHERE e.user_id = ?";
        $res = mysqli_execute_query($this->db, $sql, [$uid]);
        $row = $res ? mysqli_fetch_assoc($res) : null;

        return $row ?: null;
    }

    /**
     * Inhalte eines Lageberichts aus einer Zeile von loadMailRow() zusammenstellen.
     */
    public function buildMailData(array $row): array
    {
        $sameRound = !$this->roundChanged($row);
        [$m, $d] = $this->computeReserve($row);
        $col = (int)$row['col'];
        $fromSector = $sameRound ? (int)$row['from_sector'] : 0;

        $sectorActive = 0;
        if ($fromSector > 1) {
            $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS anz FROM de_login l LEFT JOIN de_user_data d ON (d.user_id = l.user_id) WHERE d.sector = ? AND l.status = 1 AND d.npc = 0", [$fromSector]);
            $sectorActive = (int)(mysqli_fetch_assoc($res)['anz'] ?? 0);
        }

        return [
            'name' => (string)$row['spielername'],
            'days' => max(0, (int)floor((time() - strtotime($row['since'])) / 86400)),
            'col' => $col,
            'col_lost' => $sameRound ? max(0, (int)$row['col_at_exile'] - $col) : 0,
            'from_sector' => $fromSector,
            'sector_active' => $sectorActive,
            'allytag' => ($row['allytag'] != '' && (int)$row['ally_status'] === 1) ? (string)$row['allytag'] : '',
            'reserve_m' => $m,
            'reserve_d' => $d,
            //gleiche Grenze wie der Umzug aus Sektor 1 in tickler/wt.php
            'movable' => $col >= 10 || (float)$row['score'] >= 5000000,
            'universe' => $this->getUniverse(),
        ];
    }

    /**
     * Lage im Universum für die Lageberichte, einmal pro Instanz ermittelt.
     */
    public function getUniverse(): array
    {
        if ($this->universe !== null) {
            return $this->universe;
        }
        $sys = $this->getSystem();

        //aktive Kommandanten im Spielgeschehen (wie rpc.php getaccountanz)
        $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS anz FROM de_login l LEFT JOIN de_user_data d ON (d.user_id = l.user_id) WHERE l.status = 1 AND d.npc = 0 AND d.sector > 1", []);
        $active = (int)(mysqli_fetch_assoc($res)['anz'] ?? 0);

        //Erhabenenkampf (wie overview.php)
        $eh = '';
        $ehDate = '';
        if ((int)($GLOBALS['sv_ewige_runde'] ?? 0) === 1) {
            $eh = 'laeuft';
        } elseif (!$this->isRoundOver()) {
            $res = mysqli_execute_query($this->db, "SELECT MAX(tick) AS tick FROM de_user_data", []);
            $ticks = max(1, (int)(mysqli_fetch_assoc($res)['tick'] ?? 1));
            $winscore = (int)($GLOBALS['sv_winscore'] ?? 0);
            if ($ticks < 2500000 && $winscore > 0) {
                if ($ticks < $winscore) {
                    $wtProTag = 0;
                    for ($i = 0; $i < 24; $i++) {
                        $wtProTag += count($GLOBALS['wts'][$i] ?? []);
                    }
                    if ($wtProTag > 0) {
                        $eh = 'bald';
                        $ehDate = date('d.m.Y', time() + (int)(($winscore - $ticks) / $wtProTag * 86400));
                    }
                } else {
                    $eh = 'laeuft';
                }
            }
        }

        $this->universe = [
            'round_date' => $sys['rundenstart_datum'] ? date('d.m.Y', strtotime($sys['rundenstart_datum'])) : '',
            'wt' => (int)$sys['wt'],
            'active' => $active,
            'eh' => $eh,
            'eh_date' => $ehDate,
        ];

        return $this->universe;
    }

    public static function formatNumber(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
