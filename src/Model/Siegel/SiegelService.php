<?php
namespace DieEwigen\DE2\Model\Siegel;

/**
 * Das Siegel von Basranur: gemeinsames Serverprojekt in den Vergessenen Systemen (Spezialsystem 5).
 *
 * Spieler holen Resonanzkristalle (Item 21) über den Agenteneinsatz BASRANUR (missions.php) und setzen sie
 * pro Periode ein. Wer seinen vollen Anteil einsetzt, ist Mitwirkender. Am Periodenende ergeben je
 * sv_siegel_spieler_pro_stufe Mitwirkende eine Stufe. Jede Stufe bringt allen menschlichen Spielern in der
 * nächsten Periode +2 % planetaren Grundertrag (tickler/wt.php). Die Perioden laufen in Wirtschaftsticks.
 */
class SiegelService
{
    public const ITEM_ID = 21;
    public const SPECIAL_SYSTEM_ID = 5;
    public const SYSTEM_NAME = 'Siegel von Basranur';
    public const PROZENT_PRO_STUFE = 2;

    private \mysqli $db;
    private ?array $system = null;
    private ?array $state = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    //Konfiguration, überschreibbar in sv.inc.php
    public static function getDauer(): int
    {
        return max(1, (int)($GLOBALS['sv_siegel_dauer'] ?? 480));
    }

    public static function getAnteil(): int
    {
        return max(1, (int)($GLOBALS['sv_siegel_anteil'] ?? 3));
    }

    public static function getSpielerProStufe(): int
    {
        return max(1, (int)($GLOBALS['sv_siegel_spieler_pro_stufe'] ?? 4));
    }

    public static function getMaxStufe(): int
    {
        return max(0, (int)($GLOBALS['sv_siegel_max_stufe'] ?? 10));
    }

    public static function getMissionZeit(): int
    {
        return max(60, (int)($GLOBALS['sv_siegel_mission_zeit'] ?? 14400));
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
     * Zustand des Siegels. Legt die Zeile bei Bedarf an und setzt sie nach einem Rundenwechsel zurück,
     * ohne Hooks in reset.sql oder wt_auto_reset.php.
     */
    public function getState(): array
    {
        if ($this->state !== null) {
            return $this->state;
        }
        $sys = $this->getSystem();
        $wt = max(1, (int)$sys['wt']);
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_siegel (id, round_start, period_nr, period_start_wt, level, history) VALUES (1, ?, 1, ?, 0, '')", [$sys['rundenstart_datum'], $wt]);
        $row = $this->readState();

        if ((string)$row['round_start'] !== (string)$sys['rundenstart_datum'] || $wt < (int)$row['period_start_wt']) {
            mysqli_execute_query($this->db, "UPDATE de_siegel SET round_start = ?, period_nr = period_nr + 1, period_start_wt = ?, level = 0, history = '' WHERE id = 1 AND period_nr = ?", [$sys['rundenstart_datum'], $wt, $row['period_nr']]);
            if (mysqli_affected_rows($this->db) === 1) {
                mysqli_execute_query($this->db, "DELETE FROM de_siegel_beitrag WHERE period_nr <= ?", [$row['period_nr']]);
            }
            $row = $this->readState();
        }

        $this->state = $row;
        return $row;
    }

    private function readState(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_siegel WHERE id = 1", []);
        return mysqli_fetch_assoc($res) ?: ['round_start' => null, 'period_nr' => 1, 'period_start_wt' => 1, 'level' => 0, 'history' => ''];
    }

    public function getLevel(): int
    {
        return (int)$this->getState()['level'];
    }

    public function getBonusPercent(): int
    {
        return $this->getLevel() * self::PROZENT_PRO_STUFE;
    }

    public function getRemainingTicks(): int
    {
        return max(0, (int)$this->getState()['period_start_wt'] + self::getDauer() - (int)$this->getSystem()['wt']);
    }

    public function levelFor(int $contributors): int
    {
        return min(intdiv(max(0, $contributors), self::getSpielerProStufe()), self::getMaxStufe());
    }

    /**
     * Fehlende Mitwirkende bis zur nächsten Stufe, 0 wenn die höchste Stufe erreicht ist.
     */
    public function missingForNextLevel(int $contributors): int
    {
        $level = $this->levelFor($contributors);
        if ($level >= self::getMaxStufe()) {
            return 0;
        }
        return ($level + 1) * self::getSpielerProStufe() - $contributors;
    }

    public function countContributors(?int $periodNr = null): int
    {
        $periodNr = $periodNr ?? (int)$this->getState()['period_nr'];
        $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS anz FROM de_siegel_beitrag WHERE period_nr = ? AND amount >= ?", [$periodNr, self::getAnteil()]);
        return (int)(mysqli_fetch_assoc($res)['anz'] ?? 0);
    }

    /**
     * Namen der Mitwirkenden der laufenden Periode, alphabetisch und ohne Zeitpunkt.
     */
    public function getContributorNames(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT d.spielername FROM de_siegel_beitrag b LEFT JOIN de_user_data d ON (d.user_id = b.user_id) WHERE b.period_nr = ? AND b.amount >= ? ORDER BY d.spielername", [(int)$this->getState()['period_nr'], self::getAnteil()]);
        $names = [];
        while ($res && $row = mysqli_fetch_assoc($res)) {
            $names[] = (string)$row['spielername'];
        }
        return $names;
    }

    public function getOwnAmount(int $uid): int
    {
        $res = mysqli_execute_query($this->db, "SELECT amount FROM de_siegel_beitrag WHERE period_nr = ? AND user_id = ?", [(int)$this->getState()['period_nr'], $uid]);
        return (int)(mysqli_fetch_assoc($res)['amount'] ?? 0);
    }

    public function getStock(int $uid): int
    {
        $res = mysqli_execute_query($this->db, "SELECT SUM(item_amount) AS anz FROM de_user_storage WHERE user_id = ? AND item_id = ?", [$uid, self::ITEM_ID]);
        return (int)floor((float)(mysqli_fetch_assoc($res)['anz'] ?? 0));
    }

    public function getSealMapId(): int
    {
        $res = mysqli_execute_query($this->db, "SELECT id FROM de_map_objects WHERE system_typ = 5 AND system_subtyp = ?", [self::SPECIAL_SYSTEM_ID]);
        return (int)(mysqli_fetch_assoc($res)['id'] ?? 0);
    }

    public function isExplored(int $uid): bool
    {
        $res = mysqli_execute_query($this->db, "SELECT known_since FROM de_user_map WHERE user_id = ? AND map_id = ?", [$uid, $this->getSealMapId()]);
        $known = (int)(mysqli_fetch_assoc($res)['known_since'] ?? 0);
        return $known > 0 && $known < time();
    }

    /**
     * Verbindung zum Siegel hergestellt, also Spezialsystem-Phase 1 erreicht (wie bei Ares und Hephaistos).
     */
    public function isUnlocked(int $uid): bool
    {
        $res = mysqli_execute_query($this->db, "SELECT specialsystem_data FROM de_user_map WHERE user_id = ? AND map_id = ?", [$uid, $this->getSealMapId()]);
        $raw = (string)(mysqli_fetch_assoc($res)['specialsystem_data'] ?? '');
        $data = $raw !== '' ? unserialize($raw, ['allowed_classes' => false]) : [];
        return is_array($data) && (int)($data['phase'] ?? 0) >= 1;
    }

    /**
     * Prüft, ob ein Spieler Kristalle einsetzen darf.
     *
     * @return string leer, wenn erlaubt, sonst der Schlüssel der Fehlermeldung in $siegel_lang
     */
    public function checkDonate(int $uid): string
    {
        //doetick sperrt Rundenpause, Admin-Pause und einen gerade laufenden Wirtschaftstick
        if ((int)$this->getSystem()['doetick'] !== 1) {
            return 'fehler_gesperrt';
        }
        //de_user_data.status ist der Allianzstatus, daher den Loginstatus ausdrücklich aus de_login holen
        $res = mysqli_execute_query($this->db, "SELECT l.status, d.npc, d.sector FROM de_login l LEFT JOIN de_user_data d ON (d.user_id = l.user_id) WHERE l.user_id = ?", [$uid]);
        $row = mysqli_fetch_assoc($res);
        if (!$row || (int)$row['status'] !== 1 || (int)$row['npc'] !== 0) {
            return 'kein_zugang_npc';
        }
        if ((int)$row['sector'] <= 1) {
            return 'fehler_sektor1';
        }
        if (!$this->isUnlocked($uid)) {
            return 'fehler_verbindung';
        }
        return '';
    }

    /**
     * Resonanzkristalle einsetzen. Die Menge wird auf den Anteil und den Lagerbestand gekappt.
     *
     * @return array{ok:bool, msg:string}
     */
    public function donate(int $uid, int $wanted, array $lang): array
    {
        $err = $this->checkDonate($uid);
        if ($err !== '') {
            return ['ok' => false, 'msg' => $lang[$err]];
        }
        if ($wanted <= 0) {
            return ['ok' => false, 'msg' => $lang['fehler_menge']];
        }
        if (!\setLock($uid)) {
            return ['ok' => false, 'msg' => $lang['fehler_lock']];
        }

        try {
            $this->state = null;
            $period = (int)$this->getState()['period_nr'];
            $anteil = self::getAnteil();
            $own = $this->getOwnAmount($uid);
            if ($own >= $anteil) {
                return ['ok' => false, 'msg' => $lang['fehler_anteil']];
            }
            $amount = min($wanted, $anteil - $own, $this->getStock($uid));
            if ($amount <= 0) {
                return ['ok' => false, 'msg' => $lang['fehler_lager']];
            }

            //abbuchen, nur wenn der Bestand reicht
            mysqli_execute_query($this->db, "UPDATE de_user_storage SET item_amount = item_amount - ? WHERE user_id = ? AND item_id = ? AND item_amount >= ? LIMIT 1", [$amount, $uid, self::ITEM_ID, $amount]);
            if (mysqli_affected_rows($this->db) !== 1) {
                return ['ok' => false, 'msg' => $lang['fehler_lager']];
            }

            //gutschreiben, der Anteil bleibt die Obergrenze
            mysqli_execute_query($this->db, "INSERT IGNORE INTO de_siegel_beitrag (period_nr, user_id, amount) VALUES (?, ?, 0)", [$period, $uid]);
            mysqli_execute_query($this->db, "UPDATE de_siegel_beitrag SET amount = amount + ? WHERE period_nr = ? AND user_id = ? AND amount + ? <= ?", [$amount, $period, $uid, $amount, $anteil]);
            if (mysqli_affected_rows($this->db) !== 1) {
                $this->refundStock($uid, $amount);
                return ['ok' => false, 'msg' => $lang['fehler_anteil']];
            }

            //hat ein Wirtschaftstick die Periode inzwischen gewechselt, wäre der Beitrag verloren
            $this->state = null;
            if ((int)$this->getState()['period_nr'] !== $period) {
                mysqli_execute_query($this->db, "UPDATE de_siegel_beitrag SET amount = amount - ? WHERE period_nr = ? AND user_id = ? AND amount >= ?", [$amount, $period, $uid, $amount]);
                $this->refundStock($uid, $amount);
                return ['ok' => false, 'msg' => $lang['fehler_gesperrt']];
            }

            //neue Stufe für die nächste Periode erreicht? Dann im Serverchat melden
            if ($own + $amount >= $anteil) {
                $contributors = $this->countContributors($period);
                $level = $this->levelFor($contributors);
                if ($level > $this->levelFor($contributors - 1)) {
                    $res = mysqli_execute_query($this->db, "SELECT spielername FROM de_user_data WHERE user_id = ?", [$uid]);
                    $name = htmlspecialchars((string)(mysqli_fetch_assoc($res)['spielername'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $this->chat(strtr($lang['chat_stufe'], ['{NAME}' => $name, '{LEVEL}' => $level]));
                }
            }

            return ['ok' => true, 'msg' => strtr($lang['erfolg'], ['{AMOUNT}' => $amount])];
        } finally {
            \releaseLock($uid);
        }
    }

    private function refundStock(int $uid, int $amount): void
    {
        mysqli_execute_query($this->db, "UPDATE de_user_storage SET item_amount = item_amount + ? WHERE user_id = ? AND item_id = ? LIMIT 1", [$amount, $uid, self::ITEM_ID]);
    }

    /**
     * Aufruf im Wirtschaftstick vor der Spielerschleife: Rundenreset, Periodenwechsel, Serverchat.
     *
     * @return int aktueller Bonus in Prozent auf den planetaren Grundertrag
     */
    public function processTick(array $lang): int
    {
        $this->ensureSystemName();
        $state = $this->getState();
        $wt = (int)$this->getSystem()['wt'];

        if ($wt >= (int)$state['period_start_wt'] + self::getDauer()) {
            $period = (int)$state['period_nr'];
            $contributors = $this->countContributors($period);
            $level = $this->levelFor($contributors);
            $line = strtr($lang['verlauf_zeile'], ['{DATE}' => date('d.m.Y H:i'), '{WT}' => $wt, '{N}' => $contributors, '{LEVEL}' => $level]);

            mysqli_execute_query($this->db, "UPDATE de_siegel SET level = ?, period_nr = period_nr + 1, period_start_wt = ?, history = LEFT(CONCAT(?, history), 2000) WHERE id = 1 AND period_nr = ?", [$level, $wt, $line."\n", $period]);
            if (mysqli_affected_rows($this->db) === 1) {
                mysqli_execute_query($this->db, "DELETE FROM de_siegel_beitrag WHERE period_nr <= ?", [$period]);
                $text = $level > 0 ? $lang['chat_periode'] : $lang['chat_periode_null'];
                $this->chat(strtr($text, ['{N}' => $contributors, '{LEVEL}' => $level, '{PCT}' => $level * self::PROZENT_PRO_STUFE, '{DAUER}' => self::getDauer()]));
            }
            $this->state = null;
        }

        return $this->getBonusPercent();
    }

    /**
     * Benennt das Spezialsystem auf der laufenden Karte einmalig um. Künftige Karten bekommen den Namen
     * direkt in tickler/wt_create_map.php.
     */
    private function ensureSystemName(): void
    {
        if (!class_exists('map_system', false)) {
            return;
        }
        $res = mysqli_execute_query($this->db, "SELECT id, data FROM de_map_objects WHERE system_typ = 5 AND system_subtyp = ?", [self::SPECIAL_SYSTEM_ID]);
        $row = mysqli_fetch_assoc($res);
        if (!$row || strpos($row['data'], self::SYSTEM_NAME) !== false) {
            return;
        }
        $system = unserialize($row['data']);
        if (!$system instanceof \map_system) {
            return;
        }
        $system->setSystemName(self::SYSTEM_NAME);
        mysqli_execute_query($this->db, "UPDATE de_map_objects SET data = ? WHERE id = ?", [serialize($system), $row['id']]);
    }

    /**
     * Systemmeldung im Serverchat, nur Datenbank (kein Discord). owner_id 0 wie bei den Systemmeldungen
     * aus dem Wirtschaftstick, die Spalte ist in der Spiel-DB vorzeichenlos.
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
