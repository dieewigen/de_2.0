<?php
namespace DieEwigen\DE2\Model\Feldzug;

/**
 * Feldzug um die Vergessenen Systeme: Wettstreit der Allianzen um Brennpunkte (Regelwerk: docs/feldzug.md).
 *
 * Ablauf je Feldzug: Aufruf (sv_feldzug_aufruf_zuege Züge, Anmeldung und Kriegskasse), dann Kampf
 * (sv_feldzug_kampf_zuege Züge). In jedem Zug verteilt jede Allianz sv_feldzug_legionen Legionen auf die
 * Brennpunkte; die Auswertung (Auswertung::zug) vergibt Kontrollpunkte, die sofort als Questpunkte
 * (Rundensiegartefakte) gebucht werden. Danach beginnt sofort der nächste Aufruf. Am Rundenende werden die
 * Feldherren ermittelt und betitelt (rundenende()).
 *
 * Eingebunden in tickler/wt.php (processWt, rundenende), tickler/wt_manage_map.php (Mine), vs_bonus_info()
 * in functions.php (Werft) und feldzug.php. Die Ticks laufen über runtick.php in eigenen Closures: Texte kommen
 * als Parameter, Einstellungen aus $GLOBALS.
 */
class FeldzugService
{
    public const PHASE_AUFRUF = 1;
    public const PHASE_KAMPF = 2;
    public const PHASE_BEENDET = 3;
    public const PHASE_AUSGEFALLEN = 4;

    public const TEILNEHMER_ANGEMELDET = 1;
    public const TEILNEHMER_DABEI = 2;

    //VS-Rohstoffe Eisen (3) bis Octagium (12) für Eintrittspreis und Kriegskasse
    public const ITEMS = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    public const MINE_PROZENT = 10;
    public const WERFT_PROZENT = 10;

    //Spalten in de_allys, deren Inhaber Legionen verteilen und die Allianz anmelden dürfen
    public const POSTEN = ['leaderid', 'coleaderid1', 'coleaderid2', 'coleaderid3', 'fleetcommander1', 'fleetcommander2',
        'tacticalofficer1', 'tacticalofficer2', 'memberofficer1', 'memberofficer2'];

    private \mysqli $db;
    private ?array $system = null;
    private $aktuell = false;
    private static ?array $marker = null;
    private static ?array $rollenHalter = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    //Konfiguration, überschreibbar in sv.inc.php; ohne sv_feldzug_aktiv bleibt der Feldzug aus
    public static function istAktiv(): bool
    {
        return (bool)($GLOBALS['sv_feldzug_aktiv'] ?? false) && (int)($GLOBALS['sv_deactivate_vsystems'] ?? 0) !== 1;
    }

    public static function getZugWt(): int
    {
        return max(1, (int)($GLOBALS['sv_feldzug_zug_wt'] ?? 480));
    }

    public static function getStartZuege(): int
    {
        return max(0, (int)($GLOBALS['sv_feldzug_start_zuege'] ?? 7));
    }

    public static function getAufrufZuege(): int
    {
        return max(1, (int)($GLOBALS['sv_feldzug_aufruf_zuege'] ?? 2));
    }

    public static function getKampfZuege(): int
    {
        return max(1, (int)($GLOBALS['sv_feldzug_kampf_zuege'] ?? 12));
    }

    public static function getLegionen(): int
    {
        return max(1, (int)($GLOBALS['sv_feldzug_legionen'] ?? 12));
    }

    public static function getExtraBrennpunkte(): int
    {
        return max(2, (int)($GLOBALS['sv_feldzug_extra_brennpunkte'] ?? 3));
    }

    public static function getGrundpreis(): int
    {
        return max(0, (int)($GLOBALS['sv_feldzug_grundpreis'] ?? 2000));
    }

    /**
     * Questpunkte (Rundensiegartefakte) für Kontrollpunkte eines Zuges: Kontrollpunkte × Zuglänge ÷ 16.
     */
    public static function questpunkte(int $kontrollpunkte): int
    {
        return (int)round($kontrollpunkte * self::getZugWt() / 16);
    }

    public function getSystem(): array
    {
        if ($this->system === null) {
            $res = mysqli_execute_query($this->db, "SELECT wt, rundenstart_datum, doetick FROM de_system LIMIT 1", []);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $this->system = $row ?: ['wt' => 1, 'rundenstart_datum' => null, 'doetick' => 0];
        }
        return $this->system;
    }

    private function getWt(): int
    {
        return (int)$this->getSystem()['wt'];
    }

    /**
     * Neuester Feldzug der laufenden Runde oder null. Fehlen die Tabellen (de.sql noch nicht eingespielt),
     * gilt der Feldzug als aus.
     */
    public function getAktuell(): ?array
    {
        if ($this->aktuell === false) {
            $this->aktuell = null;
            try {
                $sys = $this->getSystem();
                $res = mysqli_execute_query($this->db, "SELECT * FROM de_feldzug WHERE round_start <=> ? AND start_wt <= ? ORDER BY id DESC LIMIT 1", [$sys['rundenstart_datum'], (int)$sys['wt']]);
                $this->aktuell = mysqli_fetch_assoc($res) ?: null;
            } catch (\mysqli_sql_exception $e) {
                error_log('Feldzug: '.$e->getMessage());
            }
        }
        return $this->aktuell;
    }

    private function neuLaden(): void
    {
        $this->aktuell = false;
        self::$marker = null;
        self::$rollenHalter = null;
    }

    /**
     * Restliche WT bis zum nächsten Schritt (Ende des Aufrufs bzw. nächste Zugauswertung), null ohne Feldzug.
     */
    public function getWtBisSchritt(?array $fz = null): ?int
    {
        $fz = $fz ?? $this->getAktuell();
        if ($fz === null) {
            return null;
        }
        $wt = $this->getWt();
        if ((int)$fz['phase'] === self::PHASE_AUFRUF) {
            return max(0, (int)$fz['start_wt'] + self::getAufrufZuege() * self::getZugWt() - $wt);
        }
        if ((int)$fz['phase'] === self::PHASE_KAMPF) {
            return max(0, (int)$fz['kampf_start_wt'] + ((int)$fz['zug'] + 1) * self::getZugWt() - $wt);
        }
        return null;
    }

    /**
     * WT bis zum ersten Aufruf der Runde, solange es noch keinen Feldzug gibt.
     */
    public function getWtBisErsterAufruf(): int
    {
        return max(0, self::getStartZuege() * self::getZugWt() - $this->getWt());
    }

    ////////////////////////////////////////////////////////////////////
    // Wirtschaftstick
    ////////////////////////////////////////////////////////////////////

    /**
     * Aufruf im Wirtschaftstick: höchstens ein Schritt je WT (Aufruf eröffnen, Kampf starten, Zug auswerten).
     */
    public function processWt(array $lang): void
    {
        if (!self::istAktiv()) {
            return;
        }
        $this->aufraeumen();
        $wt = $this->getWt();
        $fz = $this->getAktuell();

        if ($fz === null) {
            if ($wt >= self::getStartZuege() * self::getZugWt()) {
                $this->aufrufEroeffnen(1, $lang);
            }
            return;
        }

        switch ((int)$fz['phase']) {
            case self::PHASE_AUFRUF:
                if ($wt >= (int)$fz['start_wt'] + self::getAufrufZuege() * self::getZugWt()) {
                    $this->kampfStarten($fz, $lang);
                }
                break;
            case self::PHASE_KAMPF:
                if ($wt >= (int)$fz['kampf_start_wt'] + ((int)$fz['zug'] + 1) * self::getZugWt()) {
                    $this->zugAuswerten($fz, $lang);
                }
                break;
            default:
                //beendet oder ausgefallen: sollte schon im selben WT weitergehen, sonst hier nachholen
                $this->aufrufEroeffnen((int)$fz['nr'] + 1, $lang);
        }
    }

    /**
     * Daten früherer Runden löschen (nicht die Historie). Erkennt die Runde am rundenstart_datum und, für zwei
     * Resets am selben Tag, an einem WT, der kleiner ist als der Start eines gespeicherten Feldzugs.
     */
    private function aufraeumen(): void
    {
        $sys = $this->getSystem();
        $res = mysqli_execute_query($this->db, "SELECT id FROM de_feldzug WHERE NOT (round_start <=> ?) OR start_wt > ?", [$sys['rundenstart_datum'], (int)$sys['wt']]);
        $alt = array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'id'));
        if (empty($alt)) {
            return;
        }
        $liste = implode(',', $alt);
        foreach (['de_feldzug_brennpunkt', 'de_feldzug_teilnehmer', 'de_feldzug_mitglied', 'de_feldzug_verteilung', 'de_feldzug_ergebnis'] as $tabelle) {
            mysqli_query($this->db, "DELETE FROM ".$tabelle." WHERE feldzug_id IN (".$liste.")");
        }
        mysqli_query($this->db, "DELETE FROM de_feldzug WHERE id IN (".$liste.")");
        $this->neuLaden();
    }

    /**
     * Median der VS-Produktion je WT über alle aktiven menschlichen Spieler, die überhaupt produzieren.
     * Quelle: item_wt_change (Ertrag des letzten WT je Rohstoff, gesetzt in tickler/wt_manage_map.php).
     */
    public function messeProduktion(): float
    {
        $res = mysqli_execute_query($this->db, "SELECT SUM(s.item_wt_change) AS summe FROM de_user_storage s JOIN de_user_data d ON d.user_id = s.user_id JOIN de_login l ON l.user_id = s.user_id
            WHERE s.item_id BETWEEN 3 AND 12 AND l.status = 1 AND d.npc = 0 AND d.sector > 1 GROUP BY s.user_id HAVING summe > 0", []);
        return Preis::median(array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'summe'));
    }

    private function aufrufEroeffnen(int $nr, array $lang): void
    {
        $sys = $this->getSystem();
        $wt = $this->getWt();
        $produktion = $this->messeProduktion();

        //Bezugswert: erste Messung der Runde, die über 0 liegt
        $res = mysqli_execute_query($this->db, "SELECT produktion_basis FROM de_feldzug WHERE round_start <=> ? AND produktion_basis > 0 ORDER BY id ASC LIMIT 1", [$sys['rundenstart_datum']]);
        $basis = (float)(mysqli_fetch_assoc($res)['produktion_basis'] ?? 0);
        if ($basis <= 0 && $produktion > 0) {
            $basis = $produktion;
        }
        $preis = Preis::berechnen(self::getGrundpreis(), $produktion, $basis);

        mysqli_execute_query($this->db, "INSERT INTO de_feldzug (round_start, nr, phase, start_wt, preis, produktion, produktion_basis) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$sys['rundenstart_datum'], $nr, self::PHASE_AUFRUF, $wt, $preis, $produktion, $basis]);
        $id = (int)mysqli_insert_id($this->db);

        //Mitglieder beim Aufruf festhalten: nur wer jetzt schon dabei ist, kann beim Start berechtigt sein
        mysqli_execute_query($this->db, "INSERT INTO de_feldzug_mitglied (feldzug_id, user_id, ally_id, teilnehmer) SELECT ?, user_id, ally_id, 0 FROM de_user_data WHERE status = 1 AND ally_id > 0 AND npc = 0", [$id]);
        $this->neuLaden();

        $this->chatServer(strtr($lang['chat_aufruf'], ['{NR}' => $nr, '{PREIS}' => self::zahl($preis), '{WT}' => self::zahl(self::getAufrufZuege() * self::getZugWt())]), true);
    }

    private function kampfStarten(array $fz, array $lang): void
    {
        $id = (int)$fz['id'];
        $preis = (int)$fz['preis'];
        $wt = $this->getWt();

        //wer erfüllt am Ende des Aufrufs alle Voraussetzungen?
        $dabei = [];
        foreach ($this->getTeilnehmer($id) as $t) {
            $ally = (int)$t['ally_id'];
            $res = mysqli_execute_query($this->db, "SELECT id, allytag, allyname FROM de_allys WHERE id = ?", [$ally]);
            $allyRow = mysqli_fetch_assoc($res);
            if (!$allyRow || $this->zaehleBerechtigte($id, $ally) < 1 || !$this->kasseReicht($ally, $preis)) {
                continue;
            }
            $dabei[$ally] = $allyRow;
        }

        $anzahl = count($dabei) + self::getExtraBrennpunkte();
        $systeme = count($dabei) >= 2 ? $this->waehleSysteme($anzahl) : [];

        if (count($dabei) < 2 || count($systeme) < $anzahl) {
            mysqli_execute_query($this->db, "UPDATE de_feldzug SET phase = ?, ende_wt = ? WHERE id = ? AND phase = ?", [self::PHASE_AUSGEFALLEN, $wt, $id, self::PHASE_AUFRUF]);
            if (mysqli_affected_rows($this->db) !== 1) {
                return;
            }
            $this->chatServer(strtr($lang['chat_ausgefallen'], ['{NR}' => (int)$fz['nr'], '{N}' => count($dabei)]), true);
            $this->neuLaden();
            $this->aufrufEroeffnen((int)$fz['nr'] + 1, $lang);
            return;
        }

        mysqli_execute_query($this->db, "UPDATE de_feldzug SET phase = ?, kampf_start_wt = ?, zug = 0 WHERE id = ? AND phase = ?", [self::PHASE_KAMPF, $wt, $id, self::PHASE_AUFRUF]);
        if (mysqli_affected_rows($this->db) !== 1) {
            return;
        }

        //Eintrittspreis abbuchen, Teilnehmer und berechtigte Mitglieder markieren
        foreach ($dabei as $ally => $allyRow) {
            foreach (self::ITEMS as $item) {
                $this->kasseBuchen($ally, $item, -$preis);
            }
            mysqli_execute_query($this->db, "UPDATE de_feldzug_teilnehmer SET status = ?, allytag = ?, allyname = ? WHERE feldzug_id = ? AND ally_id = ?", [self::TEILNEHMER_DABEI, $allyRow['allytag'], $allyRow['allyname'], $id, $ally]);
            mysqli_execute_query($this->db, "UPDATE de_feldzug_mitglied m JOIN de_user_data d ON d.user_id = m.user_id JOIN de_login l ON l.user_id = m.user_id
                SET m.teilnehmer = 1 WHERE m.feldzug_id = ? AND m.ally_id = ? AND d.ally_id = m.ally_id AND d.status = 1 AND d.npc = 0 AND d.sector > 1 AND l.status = 1", [$id, $ally]);
        }

        //Brennpunkte mit Werten und Rollen
        $werte = Brennpunkte::werteUndRollen($anzahl, 'mt_rand');
        foreach ($systeme as $i => $system) {
            mysqli_execute_query($this->db, "INSERT INTO de_feldzug_brennpunkt (feldzug_id, map_id, name, wert, rolle) VALUES (?, ?, ?, ?, ?)",
                [$id, $system['id'], mb_substr($system['name'], 0, 100), $werte[$i]['wert'], $werte[$i]['rolle']]);
        }
        $this->neuLaden();

        $tags = array_map(fn($a) => $a['allytag'], $dabei);
        $this->chatServer(strtr($lang['chat_start'], ['{NR}' => (int)$fz['nr'], '{ALLYS}' => implode(', ', $tags), '{N}' => $anzahl, '{ZUEGE}' => self::getKampfZuege()]), true);
    }

    /**
     * Zufällige gewöhnliche Vergessene Systeme: keine Battlegrounds, keine Sondersysteme, nicht DER EINGANG.
     */
    private function waehleSysteme(int $anzahl): array
    {
        $res = mysqli_query($this->db, "SELECT id, data FROM de_map_objects WHERE system_typ = 1 AND always_visible = 0 ORDER BY RAND() LIMIT ".(int)($anzahl * 3));
        $liste = [];
        while (($row = mysqli_fetch_assoc($res)) && count($liste) < $anzahl) {
            $name = '#'.$row['id'];
            if (class_exists('map_system', false)) {
                $system = @unserialize($row['data']);
                if (!$system instanceof \map_system || (int)($system->special_system ?? 0) > 0) {
                    continue;
                }
                $name = $system->getSystemName();
            }
            $liste[] = ['id' => (int)$row['id'], 'name' => $name];
        }
        return $liste;
    }

    private function zugAuswerten(array $fz, array $lang): void
    {
        $id = (int)$fz['id'];
        $zug = (int)$fz['zug'] + 1;

        mysqli_execute_query($this->db, "UPDATE de_feldzug SET zug = ? WHERE id = ? AND phase = ? AND zug = ?", [$zug, $id, self::PHASE_KAMPF, $zug - 1]);
        if (mysqli_affected_rows($this->db) !== 1) {
            return;
        }

        $brennpunkte = [];
        $namen = [];
        foreach ($this->getBrennpunkte($id) as $bp) {
            $brennpunkte[(int)$bp['id']] = ['wert' => (int)$bp['wert'], 'rolle' => (int)$bp['rolle'], 'halter' => (int)$bp['halter_ally_id']];
            $namen[(int)$bp['id']] = $bp['name'];
        }
        $verteilungen = [];
        $res = mysqli_execute_query($this->db, "SELECT ally_id, brennpunkt_id, legionen FROM de_feldzug_verteilung WHERE feldzug_id = ?", [$id]);
        while ($row = mysqli_fetch_assoc($res)) {
            $verteilungen[(int)$row['ally_id']][(int)$row['brennpunkt_id']] = (int)$row['legionen'];
        }

        //teilnehmende Allianzen, die es noch gibt; Kürzel für die Anzeige
        $tags = [];
        $aktive = [];
        foreach ($this->getTeilnehmer($id, self::TEILNEHMER_DABEI) as $t) {
            $tags[(int)$t['ally_id']] = $t['allytag'];
            $res = mysqli_execute_query($this->db, "SELECT id FROM de_allys WHERE id = ?", [(int)$t['ally_id']]);
            if (mysqli_num_rows($res) > 0) {
                $aktive[] = (int)$t['ally_id'];
            }
        }

        $ergebnis = Auswertung::zug($brennpunkte, $verteilungen, $aktive);

        $wechsel = [];
        $allyMeldungen = [];
        foreach ($ergebnis['brennpunkte'] as $bpId => $e) {
            foreach ($e['staerken'] as $ally => $staerke) {
                mysqli_execute_query($this->db, "INSERT INTO de_feldzug_ergebnis (feldzug_id, zug, brennpunkt_id, ally_id, legionen, staerke, halter) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$id, $zug, $bpId, $ally, $e['legionen'][$ally] ?? 0, $staerke, $ally === $e['halter'] ? 1 : 0]);
            }
            mysqli_execute_query($this->db, "UPDATE de_feldzug_brennpunkt SET halter_ally_id = ?, halter_tag = ? WHERE id = ?", [$e['halter'], $e['halter'] > 0 ? ($tags[$e['halter']] ?? '') : '', $bpId]);

            if ($e['halter'] !== $e['halter_vorher']) {
                $neu = $e['halter'] > 0 ? ($tags[$e['halter']] ?? '?') : $lang['neutral'];
                $wechsel[] = strtr($lang['wechsel'], ['{BP}' => $namen[$bpId], '{TAG}' => $neu]);
                if ($e['halter'] > 0) {
                    $allyMeldungen[$e['halter']][] = strtr($lang['ally_erobert'], ['{BP}' => $namen[$bpId]]);
                }
                if ($e['halter_vorher'] > 0 && isset($tags[$e['halter_vorher']])) {
                    $allyMeldungen[$e['halter_vorher']][] = strtr($lang['ally_verloren'], ['{BP}' => $namen[$bpId], '{TAG}' => $neu]);
                }
            }
        }

        //Kontrollpunkte sofort als Questpunkte (Rundensiegartefakte) buchen
        foreach ($ergebnis['kontrollpunkte'] as $ally => $kp) {
            if ($kp <= 0) {
                continue;
            }
            mysqli_execute_query($this->db, "UPDATE de_feldzug_teilnehmer SET kontrollpunkte = kontrollpunkte + ? WHERE feldzug_id = ? AND ally_id = ?", [$kp, $id, $ally]);
            mysqli_execute_query($this->db, "UPDATE de_allys SET questpoints = questpoints + ? WHERE id = ?", [self::questpunkte($kp), $ally]);
        }
        $this->neuLaden();

        //Meldungen: Sammelmeldung nur im Spiel, Allianzchat (mit Discord der Allianz) nur bei Besitzwechsel
        $text = strtr($lang['chat_zug'], ['{NR}' => (int)$fz['nr'], '{ZUG}' => $zug, '{ZUEGE}' => self::getKampfZuege()]);
        $text .= empty($wechsel) ? $lang['chat_zug_keine'] : implode(', ', $wechsel).'.';
        $this->chatServer($text, false);
        foreach ($allyMeldungen as $ally => $teile) {
            if (in_array($ally, $aktive, true)) {
                \insert_chat_msg($ally, 1, '', '<span style="color: #e67e22;">'.strtr($lang['ally_zug'], ['{ZUG}' => $zug]).' '.implode(' ', $teile).'</span>');
            }
        }

        if ($zug >= self::getKampfZuege()) {
            $this->beenden($fz, $ergebnis['gehalten'], $lang);
        }
    }

    private function beenden(array $fz, array $gehalten, array $lang): void
    {
        $id = (int)$fz['id'];
        $wt = $this->getWt();
        $teilnehmer = [];
        $daten = [];
        foreach ($this->getTeilnehmer($id, self::TEILNEHMER_DABEI) as $t) {
            $ally = (int)$t['ally_id'];
            $teilnehmer[$ally] = ['kontrollpunkte' => (int)$t['kontrollpunkte'], 'gehalten' => (int)($gehalten[$ally] ?? 0)];
            $daten[$ally] = $t;
        }
        $sieger = Sieger::feldzug($teilnehmer, 'mt_rand');

        mysqli_execute_query($this->db, "UPDATE de_feldzug SET phase = ?, sieger_ally_id = ?, ende_wt = ? WHERE id = ? AND phase = ?", [self::PHASE_BEENDET, $sieger, $wt, $id, self::PHASE_KAMPF]);
        if (mysqli_affected_rows($this->db) !== 1) {
            return;
        }
        foreach (Sieger::plaetze($teilnehmer) as $ally => $platz) {
            mysqli_execute_query($this->db, "UPDATE de_feldzug_teilnehmer SET platz = ?, gehalten_ende = ? WHERE feldzug_id = ? AND ally_id = ?", [$platz, $teilnehmer[$ally]['gehalten'], $id, $ally]);
        }

        if ($sieger > 0) {
            mysqli_execute_query($this->db, "INSERT INTO de_feldzug_historie (round_start, art, feldzug_nr, allytag, allyname, kontrollpunkte, wt) VALUES (?, 1, ?, ?, ?, ?, ?)",
                [$fz['round_start'], (int)$fz['nr'], $daten[$sieger]['allytag'], $daten[$sieger]['allyname'], $teilnehmer[$sieger]['kontrollpunkte'], $wt]);
            $this->chatServer(strtr($lang['chat_sieg'], ['{NR}' => (int)$fz['nr'], '{TAG}' => $daten[$sieger]['allytag'], '{KP}' => self::zahl($teilnehmer[$sieger]['kontrollpunkte'])]), true);
        }
        $this->neuLaden();
        $this->aufrufEroeffnen((int)$fz['nr'] + 1, $lang);
    }

    /**
     * Rundenende (tickler/wt.php, vor wt_auto_reset.php): Feldherren ermitteln, in die Historie eintragen und alle
     * Teilnehmer ihrer gewonnenen Feldzüge betiteln. Ein laufender Feldzug bringt keinen Sieger mehr.
     */
    public function rundenende(int $rundenNummer, string $serverTag, array $lang): void
    {
        $sys = $this->getSystem();
        $res = mysqli_execute_query($this->db, "SELECT sieger_ally_id AS ally, COUNT(*) AS siege FROM de_feldzug WHERE round_start <=> ? AND phase = ? AND sieger_ally_id > 0 GROUP BY sieger_ally_id",
            [$sys['rundenstart_datum'], self::PHASE_BEENDET]);
        $allys = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $allys[(int)$row['ally']] = ['siege' => (int)$row['siege'], 'kontrollpunkte' => 0];
        }
        if (empty($allys)) {
            return;
        }
        foreach (array_keys($allys) as $ally) {
            $res = mysqli_execute_query($this->db, "SELECT SUM(t.kontrollpunkte) AS kp FROM de_feldzug_teilnehmer t JOIN de_feldzug f ON f.id = t.feldzug_id WHERE f.round_start <=> ? AND t.ally_id = ?", [$sys['rundenstart_datum'], $ally]);
            $allys[$ally]['kontrollpunkte'] = (int)(mysqli_fetch_assoc($res)['kp'] ?? 0);
        }
        $feldherr = Sieger::feldherren($allys, 'mt_rand');

        $res = mysqli_execute_query($this->db, "SELECT t.allytag, t.allyname FROM de_feldzug_teilnehmer t JOIN de_feldzug f ON f.id = t.feldzug_id WHERE f.round_start <=> ? AND t.ally_id = ? ORDER BY f.id DESC LIMIT 1", [$sys['rundenstart_datum'], $feldherr]);
        $text = mysqli_fetch_assoc($res) ?: ['allytag' => '', 'allyname' => ''];
        mysqli_execute_query($this->db, "INSERT INTO de_feldzug_historie (round_start, round_id, art, allytag, allyname, kontrollpunkte, siege, wt) VALUES (?, ?, 2, ?, ?, ?, ?, ?)",
            [$sys['rundenstart_datum'], $rundenNummer, $text['allytag'], $text['allyname'], $allys[$feldherr]['kontrollpunkte'], $allys[$feldherr]['siege'], $this->getWt()]);

        //Titel für alle Teilnehmer der gewonnenen Feldzüge, über die owner_id der Accountverwaltung
        $res = mysqli_execute_query($this->db, "SELECT DISTINCT l.owner_id FROM de_feldzug_mitglied m JOIN de_feldzug f ON f.id = m.feldzug_id JOIN de_login l ON l.user_id = m.user_id
            WHERE f.round_start <=> ? AND f.phase = ? AND f.sieger_ally_id = ? AND m.ally_id = ? AND m.teilnehmer = 1 AND l.owner_id > 0",
            [$sys['rundenstart_datum'], self::PHASE_BEENDET, $feldherr, $feldherr]);
        $owner = array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'owner_id'));
        $ls = $GLOBALS['dbi_ls'] ?? null;
        if (!empty($owner) && $ls instanceof \mysqli) {
            mysqli_execute_query($ls, "INSERT INTO ls_title SET title = ?", ['['.$serverTag.'] FELDHERR/IN - Runde '.$rundenNummer]);
            $titleId = (int)mysqli_insert_id($ls);
            foreach ($owner as $ownerId) {
                mysqli_execute_query($ls, "INSERT INTO ls_user_title SET user_id = ?, title_id = ?", [$ownerId, $titleId]);
            }
        }

        $this->chatServer(strtr($lang['chat_feldherren'], ['{TAG}' => $text['allytag'], '{SIEGE}' => $allys[$feldherr]['siege']]), true);
    }

    ////////////////////////////////////////////////////////////////////
    // Aktionen der Spieler (feldzug.php)
    ////////////////////////////////////////////////////////////////////

    /**
     * Allianz des Spielers: ally_id, Kürzel, Name, ob Mitglied (status=1) und ob mit Posten; null ohne Allianz.
     */
    public function getEigeneAllianz(int $uid): ?array
    {
        $res = mysqli_execute_query($this->db, "SELECT d.ally_id, d.status, d.spielername, a.allytag, a.allyname, a.".implode(', a.', self::POSTEN)."
            FROM de_user_data d JOIN de_allys a ON a.id = d.ally_id WHERE d.user_id = ? AND d.status = 1 AND d.ally_id > 0", [$uid]);
        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            return null;
        }
        $posten = false;
        foreach (self::POSTEN as $spalte) {
            if ((int)$row[$spalte] === $uid) {
                $posten = true;
            }
        }
        return ['ally_id' => (int)$row['ally_id'], 'allytag' => $row['allytag'], 'allyname' => $row['allyname'], 'spielername' => $row['spielername'], 'posten' => $posten];
    }

    /**
     * Schreibaktionen sind gesperrt, solange ein Wirtschaftstick läuft (doetick wird dabei auf 0 gesetzt).
     */
    private function tickSperre(): bool
    {
        return (int)$this->getSystem()['doetick'] !== 1;
    }

    /**
     * @return array [bool ok, string Schlüssel der Meldung]
     */
    public function anmelden(int $uid, bool $abmelden = false): array
    {
        $fz = $this->getAktuell();
        if ($this->tickSperre()) {
            return [false, 'fehler_tick'];
        }
        if ($fz === null || (int)$fz['phase'] !== self::PHASE_AUFRUF) {
            return [false, 'fehler_kein_aufruf'];
        }
        $ally = $this->getEigeneAllianz($uid);
        if ($ally === null || !$ally['posten']) {
            return [false, 'fehler_posten'];
        }
        if ($abmelden) {
            mysqli_execute_query($this->db, "DELETE FROM de_feldzug_teilnehmer WHERE feldzug_id = ? AND ally_id = ? AND status = ?", [(int)$fz['id'], $ally['ally_id'], self::TEILNEHMER_ANGEMELDET]);
            return [true, 'ok_abgemeldet'];
        }
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_feldzug_teilnehmer (feldzug_id, ally_id, allytag, allyname, status, angemeldet_von) VALUES (?, ?, ?, ?, ?, ?)",
            [(int)$fz['id'], $ally['ally_id'], $ally['allytag'], $ally['allyname'], self::TEILNEHMER_ANGEMELDET, mb_substr((string)$ally['spielername'], 0, 30)]);
        return [true, 'ok_angemeldet'];
    }

    /**
     * Neue stehende Verteilung der eigenen Allianz speichern.
     *
     * @param array $legionen [brennpunkt_id => Legionen]
     */
    public function verteilen(int $uid, array $legionen): array
    {
        $fz = $this->getAktuell();
        if ($this->tickSperre()) {
            return [false, 'fehler_tick'];
        }
        if ($fz === null || (int)$fz['phase'] !== self::PHASE_KAMPF) {
            return [false, 'fehler_kein_kampf'];
        }
        $ally = $this->getEigeneAllianz($uid);
        if ($ally === null || !$ally['posten']) {
            return [false, 'fehler_posten'];
        }
        $id = (int)$fz['id'];
        if (!$this->istDabei($id, $ally['ally_id'])) {
            return [false, 'fehler_nicht_dabei'];
        }

        $gueltig = array_fill_keys(array_map('intval', array_column($this->getBrennpunkte($id), 'id')), true);
        $neu = [];
        $summe = 0;
        foreach ($legionen as $bp => $n) {
            if (!is_numeric($n) || (int)$n < 0 || (string)(int)$n !== trim((string)$n) || !isset($gueltig[(int)$bp])) {
                return [false, 'fehler_verteilung'];
            }
            if ((int)$n > 0) {
                $neu[(int)$bp] = (int)$n;
                $summe += (int)$n;
            }
        }
        if ($summe > self::getLegionen()) {
            return [false, 'fehler_zu_viele'];
        }

        mysqli_execute_query($this->db, "DELETE FROM de_feldzug_verteilung WHERE feldzug_id = ? AND ally_id = ?", [$id, $ally['ally_id']]);
        foreach ($neu as $bp => $n) {
            mysqli_execute_query($this->db, "INSERT INTO de_feldzug_verteilung (feldzug_id, ally_id, brennpunkt_id, legionen) VALUES (?, ?, ?, ?)", [$id, $ally['ally_id'], $bp, $n]);
        }
        mysqli_execute_query($this->db, "UPDATE de_feldzug_teilnehmer SET letzte_aenderung_name = ?, letzte_aenderung_wt = ? WHERE feldzug_id = ? AND ally_id = ?",
            [mb_substr((string)$ally['spielername'], 0, 30), $this->getWt(), $id, $ally['ally_id']]);
        return [true, 'ok_verteilt'];
    }

    /**
     * VS-Rohstoffe aus dem eigenen Lager in die Kriegskasse der Allianz spenden. Die Seite hält dabei die
     * Spielersperre (setLock); jede Abbuchung ist zusätzlich gegen zu wenig Bestand abgesichert.
     *
     * @param array $mengen [item_id => Menge]
     */
    public function spenden(int $uid, array $mengen): array
    {
        if ($this->tickSperre()) {
            return [false, 'fehler_tick'];
        }
        $ally = $this->getEigeneAllianz($uid);
        if ($ally === null) {
            return [false, 'fehler_keine_allianz'];
        }
        $gespendet = 0;
        foreach ($mengen as $item => $menge) {
            $item = (int)$item;
            if (!in_array($item, self::ITEMS, true) || !is_numeric($menge) || (int)$menge <= 0) {
                continue;
            }
            $menge = (int)$menge;
            mysqli_execute_query($this->db, "UPDATE de_user_storage SET item_amount = item_amount - ? WHERE user_id = ? AND item_id = ? AND item_amount >= ? LIMIT 1", [$menge, $uid, $item, $menge]);
            if (mysqli_affected_rows($this->db) !== 1) {
                return [false, $gespendet > 0 ? 'fehler_teilweise' : 'fehler_zu_wenig'];
            }
            $this->kasseBuchen($ally['ally_id'], $item, $menge);
            $gespendet++;
        }
        return $gespendet > 0 ? [true, 'ok_gespendet'] : [false, 'fehler_keine_menge'];
    }

    /**
     * Kriegskasse (Allianzlager) ändern. de_ally_storage hat keinen eindeutigen Schlüssel; die Sperre verhindert
     * doppelte Zeilen, und LIMIT 1 bucht bei Altlasten nur eine Zeile.
     */
    private function kasseBuchen(int $ally, int $item, int $delta): void
    {
        $sperre = 'feldzug_kasse_'.$ally;
        mysqli_execute_query($this->db, "SELECT GET_LOCK(?, 10)", [$sperre]);
        try {
            $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS n FROM de_ally_storage WHERE ally_id = ? AND item_id = ?", [$ally, $item]);
            if ((int)(mysqli_fetch_assoc($res)['n'] ?? 0) === 0) {
                mysqli_execute_query($this->db, "INSERT INTO de_ally_storage (ally_id, item_id, item_amount) VALUES (?, ?, ?)", [$ally, $item, $delta]);
            } else {
                mysqli_execute_query($this->db, "UPDATE de_ally_storage SET item_amount = item_amount + ? WHERE ally_id = ? AND item_id = ? LIMIT 1", [$delta, $ally, $item]);
            }
        } finally {
            mysqli_execute_query($this->db, "SELECT RELEASE_LOCK(?)", [$sperre]);
        }
    }

    ////////////////////////////////////////////////////////////////////
    // Abfragen für Anzeige und Boni
    ////////////////////////////////////////////////////////////////////

    public function getKasse(int $ally): array
    {
        $kasse = array_fill_keys(self::ITEMS, 0);
        $res = mysqli_execute_query($this->db, "SELECT item_id, SUM(item_amount) AS menge FROM de_ally_storage WHERE ally_id = ? AND item_id BETWEEN 3 AND 12 GROUP BY item_id", [$ally]);
        while ($row = mysqli_fetch_assoc($res)) {
            $kasse[(int)$row['item_id']] = (int)$row['menge'];
        }
        return $kasse;
    }

    public function kasseReicht(int $ally, int $preis): bool
    {
        foreach ($this->getKasse($ally) as $menge) {
            if ($menge < $preis) {
                return false;
            }
        }
        return true;
    }

    /**
     * Berechtigte Mitglieder: beim Aufruf festgehalten, noch in derselben Allianz, Mensch, aktiv, Sektor > 1.
     */
    public function zaehleBerechtigte(int $feldzugId, int $ally): int
    {
        $res = mysqli_execute_query($this->db, "SELECT COUNT(*) AS n FROM de_feldzug_mitglied m JOIN de_user_data d ON d.user_id = m.user_id JOIN de_login l ON l.user_id = m.user_id
            WHERE m.feldzug_id = ? AND m.ally_id = ? AND d.ally_id = m.ally_id AND d.status = 1 AND d.npc = 0 AND d.sector > 1 AND l.status = 1", [$feldzugId, $ally]);
        return (int)(mysqli_fetch_assoc($res)['n'] ?? 0);
    }

    public function istBerechtigt(int $feldzugId, int $uid): bool
    {
        $res = mysqli_execute_query($this->db, "SELECT 1 FROM de_feldzug_mitglied m JOIN de_user_data d ON d.user_id = m.user_id WHERE m.feldzug_id = ? AND m.user_id = ? AND d.ally_id = m.ally_id AND d.status = 1", [$feldzugId, $uid]);
        return mysqli_num_rows($res) > 0;
    }

    public function getTeilnehmer(int $feldzugId, ?int $status = null): array
    {
        $sql = "SELECT * FROM de_feldzug_teilnehmer WHERE feldzug_id = ?".($status !== null ? " AND status = ".(int)$status : "")." ORDER BY kontrollpunkte DESC, allytag ASC";
        $res = mysqli_execute_query($this->db, $sql, [$feldzugId]);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function istDabei(int $feldzugId, int $ally): bool
    {
        $res = mysqli_execute_query($this->db, "SELECT 1 FROM de_feldzug_teilnehmer WHERE feldzug_id = ? AND ally_id = ? AND status = ?", [$feldzugId, $ally, self::TEILNEHMER_DABEI]);
        return mysqli_num_rows($res) > 0;
    }

    public function getBrennpunkte(int $feldzugId): array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_feldzug_brennpunkt WHERE feldzug_id = ? ORDER BY wert DESC, id ASC", [$feldzugId]);
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    public function getVerteilung(int $feldzugId, int $ally): array
    {
        $verteilung = [];
        $res = mysqli_execute_query($this->db, "SELECT brennpunkt_id, legionen FROM de_feldzug_verteilung WHERE feldzug_id = ? AND ally_id = ?", [$feldzugId, $ally]);
        while ($row = mysqli_fetch_assoc($res)) {
            $verteilung[(int)$row['brennpunkt_id']] = (int)$row['legionen'];
        }
        return $verteilung;
    }

    /**
     * Ergebnis eines Zuges je Brennpunkt: [brennpunkt_id => [ally_id => ['legionen', 'staerke', 'halter']]]
     */
    public function getErgebnis(int $feldzugId, int $zug): array
    {
        $ergebnis = [];
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_feldzug_ergebnis WHERE feldzug_id = ? AND zug = ? ORDER BY staerke DESC", [$feldzugId, $zug]);
        while ($row = mysqli_fetch_assoc($res)) {
            $ergebnis[(int)$row['brennpunkt_id']][(int)$row['ally_id']] = ['legionen' => (int)$row['legionen'], 'staerke' => (int)$row['staerke'], 'halter' => (int)$row['halter']];
        }
        return $ergebnis;
    }

    /**
     * Halter vor einem Zug (aus dem Ergebnis des Vorzuges), [brennpunkt_id => ally_id].
     */
    public function getHalterNachZug(int $feldzugId, int $zug): array
    {
        $halter = [];
        if ($zug < 1) {
            return $halter;
        }
        $res = mysqli_execute_query($this->db, "SELECT brennpunkt_id, ally_id FROM de_feldzug_ergebnis WHERE feldzug_id = ? AND zug = ? AND halter = 1", [$feldzugId, $zug]);
        while ($row = mysqli_fetch_assoc($res)) {
            $halter[(int)$row['brennpunkt_id']] = (int)$row['ally_id'];
        }
        return $halter;
    }

    public function getHistorie(): array
    {
        try {
            $res = mysqli_execute_query($this->db, "SELECT * FROM de_feldzug_historie WHERE round_start <=> ? AND art = 1 ORDER BY id ASC", [$this->getSystem()['rundenstart_datum']]);
            return mysqli_fetch_all($res, MYSQLI_ASSOC);
        } catch (\mysqli_sql_exception $e) {
            return [];
        }
    }

    /**
     * Feldherren einer abgeschlossenen Runde (für die Rangliste), null ohne Eintrag.
     */
    public function getFeldherren(int $roundId): ?array
    {
        try {
            $res = mysqli_execute_query($this->db, "SELECT * FROM de_feldzug_historie WHERE round_id = ? AND art = 2 LIMIT 1", [$roundId]);
            return mysqli_fetch_assoc($res) ?: null;
        } catch (\mysqli_sql_exception $e) {
            return null;
        }
    }

    /**
     * Halter von Mine und Werft, nur während der Kampfphase: ['mine' => ally_id, 'werft' => ally_id].
     */
    public function getRollenHalter(): array
    {
        if (self::$rollenHalter === null) {
            self::$rollenHalter = ['mine' => 0, 'werft' => 0];
            $fz = self::istAktiv() ? $this->getAktuell() : null;
            if ($fz !== null && (int)$fz['phase'] === self::PHASE_KAMPF) {
                foreach ($this->getBrennpunkte((int)$fz['id']) as $bp) {
                    if ((int)$bp['rolle'] === Brennpunkte::ROLLE_MINE) {
                        self::$rollenHalter['mine'] = (int)$bp['halter_ally_id'];
                    } elseif ((int)$bp['rolle'] === Brennpunkte::ROLLE_WERFT) {
                        self::$rollenHalter['werft'] = (int)$bp['halter_ally_id'];
                    }
                }
            }
        }
        return self::$rollenHalter;
    }

    /**
     * Mitglieder (status=1) der Allianz, die die Mine hält, als Menge [user_id => true] für wt_manage_map.php.
     */
    public function getMineMitglieder(): array
    {
        $ally = $this->getRollenHalter()['mine'];
        if ($ally <= 0) {
            return [];
        }
        $res = mysqli_execute_query($this->db, "SELECT user_id FROM de_user_data WHERE ally_id = ? AND status = 1", [$ally]);
        return array_fill_keys(array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'user_id')), true);
    }

    /**
     * Vorteile eines Spielers durch die Brennpunkte seiner Allianz: ['mine' => bool, 'werft' => bool].
     */
    public function getVorteile(int $uid): array
    {
        $halter = $this->getRollenHalter();
        if ($halter['mine'] <= 0 && $halter['werft'] <= 0) {
            return ['mine' => false, 'werft' => false];
        }
        $res = mysqli_execute_query($this->db, "SELECT ally_id FROM de_user_data WHERE user_id = ? AND status = 1", [$uid]);
        $ally = (int)(mysqli_fetch_assoc($res)['ally_id'] ?? 0);
        return ['mine' => $ally > 0 && $ally === $halter['mine'], 'werft' => $ally > 0 && $ally === $halter['werft']];
    }

    /**
     * Brennpunkte des letzten Feldzugs mit Kampfphase für die Markierung in Liste und Karte:
     * [map_id => Kürzel des Halters oder '']. Nach dem Ende bleiben sie stehen, bis der nächste Kampf beginnt.
     */
    public static function getMarker(\mysqli $db): array
    {
        if (self::$marker === null) {
            self::$marker = [];
            if (!self::istAktiv()) {
                return self::$marker;
            }
            try {
                $res = mysqli_execute_query($db, "SELECT f.id FROM de_feldzug f JOIN de_system s ON f.round_start <=> s.rundenstart_datum WHERE f.phase IN (2, 3) ORDER BY f.id DESC LIMIT 1", []);
                $id = (int)(mysqli_fetch_assoc($res)['id'] ?? 0);
                if ($id > 0) {
                    $res = mysqli_execute_query($db, "SELECT map_id, halter_tag FROM de_feldzug_brennpunkt WHERE feldzug_id = ?", [$id]);
                    while ($row = mysqli_fetch_assoc($res)) {
                        self::$marker[(int)$row['map_id']] = (string)$row['halter_tag'];
                    }
                }
            } catch (\mysqli_sql_exception $e) {
                error_log('Feldzug: '.$e->getMessage());
            }
        }
        return self::$marker;
    }

    ////////////////////////////////////////////////////////////////////

    /**
     * Serverchat. Discord nur für Aufruf, Start, Ausfall, Sieg und Feldherren, Zugmeldungen nur im Spiel.
     */
    private function chatServer(string $html, bool $discord): void
    {
        $html = '<span style="color: #e67e22;">'.$html.'</span>';
        if ($discord) {
            \insert_chat_msg(0, 2, '[SYSTEM]', $html);
        } else {
            \insert_chat_msg_admin(0, 2, '', $html, 0, $GLOBALS['sv_server_tag'] ?? '');
        }
    }

    public static function zahl(int $wert): string
    {
        return number_format($wert, 0, ',', '.');
    }
}
