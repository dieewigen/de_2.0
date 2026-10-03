<?php
namespace DieEwigen\DE2\Model\Hekate;

use DieEwigen\DE2\Model\SpecialSystem\SpecialSystemData;
use DieEwigen\DE2\Model\SpecialSystem\Storage;
use DieEwigen\DE2\Model\VsBonus\VsBonusService;

/**
 * Der Kreuzweg der Hekate (Spezialsystem 6 der Vergessenen Systeme): wechselnde Aufträge.
 *
 * Alle sv_hekate_dauer Wirtschaftsticks schlagen Hekates Händler neue Aufträge an, je einen für Handelswaren III,
 * IV und V, jeweils mit einer zufälligen VS-Rohware als Beigabe. Wer einen Auftrag erfüllt, bekommt Palenium oder
 * Hekates Gunst (zeitlich begrenzte VS-Boni, VsBonusService). Jeder Auftrag kann pro Periode einmal erfüllt werden.
 * Die Perioden laufen in Wirtschaftsticks. Beim Rundenwechsel setzt sich der Zustand selbst zurück (wie beim
 * SiegelService) und leert dabei auch de_vs_bonus.
 *
 * Kein setLock(): map_system.php hält die Spielersperre für die ganze Seite.
 */
class HekateService
{
    public const SPECIAL_SYSTEM_ID = 6;
    public const PALENIUM_ITEM_ID = 1;

    /**
     * Standard-Aufträge, überschreibbar mit $sv_hekate_auftraege in sv.inc.php.
     * ware/min/max: Hauptware und Menge; beigabe: Bereich der Item-IDs für die Beigabe (1000-3000 Stück);
     * palenium: Belohnung, wenn Palenium gewürfelt wird, sonst die VS-Boni aus boni für dauer Wirtschaftsticks.
     */
    private const AUFTRAEGE = [
        ['ware' => 16, 'min' => 10, 'max' => 20, 'beigabe' => [5, 8], 'palenium' => 50, 'boni' => [VsBonusService::TYP_INDUSTRIE], 'dauer' => 96],
        ['ware' => 17, 'min' => 8, 'max' => 16, 'beigabe' => [7, 10], 'palenium' => 100, 'boni' => [VsBonusService::TYP_BAUZEIT], 'dauer' => 96],
        ['ware' => 18, 'min' => 5, 'max' => 10, 'beigabe' => [9, 12], 'palenium' => 200, 'boni' => [VsBonusService::TYP_INDUSTRIE, VsBonusService::TYP_BAUZEIT], 'dauer' => 192],
    ];

    private \mysqli $db;
    private SpecialSystemData $data;
    private ?array $system = null;
    private ?array $state = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
        $this->data = new SpecialSystemData($db, self::SPECIAL_SYSTEM_ID);
    }

    //Konfiguration, überschreibbar in sv.inc.php
    public static function getDauer(): int
    {
        return max(1, (int)($GLOBALS['sv_hekate_dauer'] ?? 192));
    }

    public static function getVorlagen(): array
    {
        $vorlagen = $GLOBALS['sv_hekate_auftraege'] ?? null;
        return is_array($vorlagen) && !empty($vorlagen) ? array_values($vorlagen) : self::AUFTRAEGE;
    }

    /**
     * Würfelt die Aufträge einer Periode.
     */
    public static function generateAuftraege(): array
    {
        $auftraege = [];
        foreach (self::getVorlagen() as $v) {
            $waren = [[(int)$v['ware'], mt_rand((int)$v['min'], (int)$v['max'])]];
            if (!empty($v['beigabe'])) {
                $waren[] = [mt_rand((int)$v['beigabe'][0], (int)$v['beigabe'][1]), mt_rand(10, 30) * 100];
            }
            if (mt_rand(0, 1) === 0) {
                $belohnung = ['palenium' => (int)$v['palenium']];
            } else {
                $belohnung = ['boni' => array_map('intval', $v['boni']), 'dauer' => (int)$v['dauer']];
            }
            $auftraege[] = ['waren' => $waren, 'belohnung' => $belohnung];
        }
        return $auftraege;
    }

    public function getSpecialSystemData(): SpecialSystemData
    {
        return $this->data;
    }

    /**
     * Rundendaten aus de_system, einmal pro Instanz gelesen.
     */
    public function getSystem(): array
    {
        if ($this->system === null) {
            $res = mysqli_execute_query($this->db, "SELECT wt, rundenstart_datum, doetick FROM de_system LIMIT 1", []);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $this->system = $row ?: ['wt' => 1, 'rundenstart_datum' => null, 'doetick' => 0];
        }
        return $this->system;
    }

    /**
     * Zustand der Auftragstafel. Legt die Zeile bei Bedarf an und setzt sie nach einem Rundenwechsel zurück,
     * ohne Hooks in reset.sql oder wt_auto_reset.php.
     */
    public function getState(): array
    {
        if ($this->state !== null) {
            return $this->state;
        }
        $sys = $this->getSystem();
        $wt = max(1, (int)$sys['wt']);
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_hekate (id, round_start, period_nr, period_start_wt, auftraege) VALUES (1, ?, 1, ?, ?)", [$sys['rundenstart_datum'], $wt, json_encode(self::generateAuftraege())]);
        $row = $this->readState();

        if ((string)$row['round_start'] !== (string)$sys['rundenstart_datum'] || $wt < (int)$row['period_start_wt']) {
            mysqli_execute_query($this->db, "UPDATE de_hekate SET round_start = ?, period_nr = period_nr + 1, period_start_wt = ?, auftraege = ? WHERE id = 1 AND period_nr = ?", [$sys['rundenstart_datum'], $wt, json_encode(self::generateAuftraege()), $row['period_nr']]);
            if (mysqli_affected_rows($this->db) === 1) {
                mysqli_execute_query($this->db, "DELETE FROM de_hekate_lieferung WHERE period_nr <= ?", [$row['period_nr']]);
                //Hekates Gunst gilt nur in der Runde, in der sie erworben wurde
                mysqli_execute_query($this->db, "DELETE FROM de_vs_bonus", []);
            }
            $row = $this->readState();
        }

        $this->state = $row;
        return $row;
    }

    private function readState(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_hekate WHERE id = 1", []);
        return mysqli_fetch_assoc($res) ?: ['round_start' => null, 'period_nr' => 1, 'period_start_wt' => 1, 'auftraege' => '[]'];
    }

    public function getPeriodNr(): int
    {
        return (int)$this->getState()['period_nr'];
    }

    public function getAuftraege(): array
    {
        $auftraege = json_decode((string)$this->getState()['auftraege'], true);
        return is_array($auftraege) ? $auftraege : [];
    }

    public function getRemainingTicks(): int
    {
        return max(0, (int)$this->getState()['period_start_wt'] + self::getDauer() - (int)$this->getSystem()['wt']);
    }

    /**
     * Nummern der Aufträge, die der Spieler in der laufenden Periode schon erfüllt hat.
     */
    public function getDelivered(int $uid): array
    {
        $res = mysqli_execute_query($this->db, "SELECT auftrag FROM de_hekate_lieferung WHERE period_nr = ? AND user_id = ?", [$this->getPeriodNr(), $uid]);
        $nr = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $nr[] = (int)$row['auftrag'];
        }
        return $nr;
    }

    /**
     * @return string leer, wenn erlaubt, sonst der Schlüssel der Fehlermeldung in $hekate_lang
     */
    public function checkDeliver(int $uid): string
    {
        //doetick sperrt Rundenpause, Admin-Pause und einen gerade laufenden Wirtschaftstick
        if ((int)$this->getSystem()['doetick'] !== 1) {
            return 'fehler_gesperrt';
        }
        $res = mysqli_execute_query($this->db, "SELECT l.status, d.npc FROM de_login l LEFT JOIN de_user_data d ON (d.user_id = l.user_id) WHERE l.user_id = ?", [$uid]);
        $row = mysqli_fetch_assoc($res);
        if (!$row || (int)$row['status'] !== 1 || (int)$row['npc'] !== 0) {
            return 'kein_zugang_npc';
        }
        if (!$this->data->isUnlocked($uid)) {
            return 'fehler_verbindung';
        }
        return '';
    }

    /**
     * Einen Auftrag erfüllen: Waren abbuchen und die Belohnung gutschreiben.
     *
     * @return array{ok:bool, msg:string}
     */
    public function deliver(int $uid, int $nr, int $periodNr, array $lang): array
    {
        $err = $this->checkDeliver($uid);
        if ($err !== '') {
            return ['ok' => false, 'msg' => $lang[$err]];
        }

        $this->state = null;
        $period = $this->getPeriodNr();
        if ($period !== $periodNr) {
            return ['ok' => false, 'msg' => $lang['fehler_periode']];
        }
        $auftraege = $this->getAuftraege();
        if (!isset($auftraege[$nr])) {
            return ['ok' => false, 'msg' => $lang['fehler_auftrag']];
        }
        $auftrag = $auftraege[$nr];

        //die Lieferzeile sichert, dass jeder Auftrag pro Periode nur einmal bezahlt wird
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_hekate_lieferung (period_nr, user_id, auftrag) VALUES (?, ?, ?)", [$period, $uid, $nr]);
        if (mysqli_affected_rows($this->db) !== 1) {
            return ['ok' => false, 'msg' => $lang['fehler_geliefert']];
        }

        $taken = [];
        foreach ($auftrag['waren'] as [$itemId, $menge]) {
            if (!Storage::take($this->db, $uid, (int)$itemId, (int)$menge)) {
                foreach ($taken as [$backId, $backMenge]) {
                    Storage::give($uid, $backId, $backMenge);
                }
                mysqli_execute_query($this->db, "DELETE FROM de_hekate_lieferung WHERE period_nr = ? AND user_id = ? AND auftrag = ?", [$period, $uid, $nr]);
                return ['ok' => false, 'msg' => $lang['fehler_lager']];
            }
            $taken[] = [(int)$itemId, (int)$menge];
        }

        $belohnung = $auftrag['belohnung'];
        if (isset($belohnung['palenium'])) {
            Storage::give($uid, self::PALENIUM_ITEM_ID, (int)$belohnung['palenium']);
        } else {
            $bonus = new VsBonusService($this->db);
            $wt = max(1, (int)$this->getSystem()['wt']);
            foreach ($belohnung['boni'] as $typ) {
                $bonus->grant($uid, (int)$typ, (int)$belohnung['dauer'], $wt);
            }
        }

        return ['ok' => true, 'msg' => strtr($lang['erfolg'], ['{BELOHNUNG}' => self::formatBelohnung($belohnung, $lang)])];
    }

    public static function formatBelohnung(array $belohnung, array $lang): string
    {
        if (isset($belohnung['palenium'])) {
            return strtr($lang['belohnung_palenium'], ['{N}' => self::formatNumber((int)$belohnung['palenium'])]);
        }
        $teile = [];
        foreach ($belohnung['boni'] as $typ) {
            $key = (int)$typ === VsBonusService::TYP_INDUSTRIE ? 'belohnung_industrie' : 'belohnung_bauzeit';
            $teile[] = strtr($lang[$key], ['{PCT}' => VsBonusService::getProzent((int)$typ)]);
        }
        return strtr($lang['belohnung_gunst'], ['{TEILE}' => implode($lang['und'], $teile), '{DAUER}' => (int)$belohnung['dauer']]);
    }

    /**
     * Aufruf im Wirtschaftstick vor der Spielerschleife: Rundenreset, Periodenwechsel, Serverchat.
     */
    public function processTick(array $lang): void
    {
        $state = $this->getState();
        $wt = (int)$this->getSystem()['wt'];

        if ($wt >= (int)$state['period_start_wt'] + self::getDauer()) {
            $period = (int)$state['period_nr'];
            $auftraege = self::generateAuftraege();
            mysqli_execute_query($this->db, "UPDATE de_hekate SET period_nr = period_nr + 1, period_start_wt = ?, auftraege = ? WHERE id = 1 AND period_nr = ?", [$wt, json_encode($auftraege), $period]);
            if (mysqli_affected_rows($this->db) === 1) {
                mysqli_execute_query($this->db, "DELETE FROM de_hekate_lieferung WHERE period_nr <= ?", [$period]);

                //die Beigaben machen den Unterschied zur letzten Periode, daher nennt der Chat sie
                $namen = Storage::getItemNames($this->db);
                $beigaben = [];
                foreach ($auftraege as $auftrag) {
                    foreach (array_slice($auftrag['waren'], 1) as [$itemId]) {
                        $beigaben[] = $namen[$itemId] ?? ('#'.$itemId);
                    }
                }
                $this->chat(strtr($lang['chat_periode'], ['{BEIGABEN}' => implode(', ', array_unique($beigaben)), '{DAUER}' => self::getDauer()]));
            }
            $this->state = null;
        }
    }

    /**
     * Systemmeldung im Serverchat, nur Datenbank (kein Discord), wie beim SiegelService.
     */
    private function chat(string $text): void
    {
        \insert_chat_msg_admin(0, 2, '', '<span style="color: #802ec1;">'.$text.'</span>', 0, $GLOBALS['sv_server_tag'] ?? '');
    }

    public static function formatNumber(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
