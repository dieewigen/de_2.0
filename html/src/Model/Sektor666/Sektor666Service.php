<?php
namespace DieEwigen\DE2\Model\Sektor666;

/**
 * Sektor 666 erwacht: ein Server-Boss für die Sektorflotten.
 *
 * Ab sv_s666_start % des Rundenfortschritts (WT / sv_winscore) erwachen „die Schläfer“ in Sektor 666. Ihre Hülle
 * richtet sich nach den Sektorflotten der teilnehmenden Sektoren beim Erwachen und wächst mit jeder Stufe. Die Sektoren greifen sie mit ihren Sektorflotten an
 * (bkmenu.php, Abrechnung in tickler/kt_sectorkampf.php): jedes ankommende Schiff macht 1 Schaden, das Abwehrfeuer
 * vernichtet sv_s666_abwehr % der Angreifer. Die Schläfer greifen selbst nie an.
 *
 * Ist die Hülle zerstört, bekommt jedes menschliche Mitglied eines Sektors mit mindestens 1 % des Schadens Beute,
 * sofern es schon beim Erwachen im Sektor war (de_sektor666_mitglied, Schutz gegen Zuzug kurz vor dem Sieg).
 * Die Plätze 1–3 bekommen einen Bonus, jeder Sektor einen Teil seiner Schiffsverluste zurück ins Sektorlager.
 * Danach ruhen die Schläfer sv_s666_pause % des Rundenfortschritts und kehren eine Stufe stärker zurück.
 * Mit Beginn des Erhabenenkampfs erwachen sie nicht mehr, eine wache Stufe schläft ohne Beute ein.
 *
 * Die Ticks laufen über runtick.php in eigenen Closures. Texte kommen deshalb als Parameter, Artefaktnamen lädt die
 * Klasse selbst, statt sich auf globale Variablen zu verlassen.
 */
class Sektor666Service
{
    public const SEKTOR = 666;
    public const STATUS_SCHLAEFT = 0;
    public const STATUS_WACH = 1;

    //Kosten eines Sektorschiffs je Rohstoff (bkmenu.php), Grundlage für den Ausgleich ins Sektorlager
    public const SCHIFFSKOSTEN = [1 => 2000, 2 => 500, 3 => 500, 4 => 2000];

    //Artefakte als Beute: 1–15 wie beim Allianzbonus, dazu Sekkollus (20, vom User freigegeben), kein Kollimania (19)
    public const ARTEFAKTE = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 20];

    //Schwellen der Hülle in Prozent, bei denen der Serverchat informiert wird
    private const SCHWELLEN = [75, 50, 25];

    //Beute je Mitglied: Grundbeute für alle Sektoren ab dem Mindestanteil, dazu Bonus für die Plätze 1–3
    private const BEUTE = [
        'grund' => ['tronic' => 10, 'kerne' => 2, 'palenium' => 100, 'kriegsartefakte' => 1, 'artefakte' => 0],
        1 => ['tronic' => 10, 'artefakte' => 2],
        2 => ['tronic' => 5, 'artefakte' => 1],
        3 => ['artefakte' => 1],
    ];

    //diese Posten werden mit der Stufe multipliziert
    private const MIT_STUFE = ['tronic', 'kerne', 'palenium'];

    private \mysqli $db;
    private ?array $system = null;
    private ?array $state = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    //Konfiguration, überschreibbar in sv.inc.php
    public static function istAktiv(): bool
    {
        return (bool)($GLOBALS['sv_s666_aktiv'] ?? true);
    }

    public static function getStartProzent(): float
    {
        return max(0.0, (float)($GLOBALS['sv_s666_start'] ?? 50));
    }

    public static function getPauseProzent(): float
    {
        return max(0.0, (float)($GLOBALS['sv_s666_pause'] ?? 8));
    }

    //Mindesthülle je teilnehmendem Sektor, falls die Sektorflotten noch klein sind
    public static function getHuelleJeSektor(): int
    {
        return max(1, (int)($GLOBALS['sv_s666_hp_je_sektor'] ?? 500));
    }

    //Hülle je Sektorschiff (Wach- und Sektorflotte) der teilnehmenden Sektoren beim Erwachen
    public static function getHuelleJeSchiff(): float
    {
        return max(0.0, (float)($GLOBALS['sv_s666_hp_je_schiff'] ?? 2));
    }

    public static function getStufenfaktor(): float
    {
        return max(1.0, (float)($GLOBALS['sv_s666_stufenfaktor'] ?? 1.5));
    }

    public static function getAbwehrProzent(): int
    {
        return max(0, min(100, (int)($GLOBALS['sv_s666_abwehr'] ?? 10)));
    }

    public static function getAusgleichProzent(): int
    {
        return max(0, min(100, (int)($GLOBALS['sv_s666_ausgleich'] ?? 50)));
    }

    public static function getMindestanteil(): int
    {
        return max(0, (int)($GLOBALS['sv_s666_mindestanteil'] ?? 1));
    }

    /**
     * Beute-Tabelle; sv_s666_beute überschreibt einzelne Werte, z. B. ['grund' => ['tronic' => 20]].
     */
    public static function getBeuteTabelle(): array
    {
        $tabelle = self::BEUTE;
        $eigene = $GLOBALS['sv_s666_beute'] ?? [];
        if (is_array($eigene)) {
            foreach ($eigene as $zeile => $werte) {
                if (is_array($werte)) {
                    $tabelle[$zeile] = array_merge($tabelle[$zeile] ?? [], array_map('intval', $werte));
                }
            }
        }
        return $tabelle;
    }

    /**
     * Beute je Mitglied für einen Platz und eine Stufe: Grundbeute plus Platzbonus, Tronic/Kerne/Palenium mal Stufe.
     */
    public static function beuteFuer(int $platz, int $stufe): array
    {
        $tabelle = self::getBeuteTabelle();
        $beute = ['tronic' => 0, 'kerne' => 0, 'palenium' => 0, 'kriegsartefakte' => 0, 'artefakte' => 0];
        foreach ([$tabelle['grund'] ?? [], $tabelle[$platz] ?? []] as $teil) {
            foreach ($teil as $posten => $menge) {
                if (isset($beute[$posten])) {
                    $beute[$posten] += max(0, (int)$menge);
                }
            }
        }
        foreach (self::MIT_STUFE as $posten) {
            $beute[$posten] *= max(1, $stufe);
        }
        return $beute;
    }

    /**
     * Rundendaten aus de_system, einmal pro Instanz gelesen.
     */
    public function getSystem(): array
    {
        if ($this->system === null) {
            $res = mysqli_execute_query($this->db, "SELECT wt, rundenstart_datum, roundpointsflag FROM de_system LIMIT 1", []);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            $this->system = $row ?: ['wt' => 1, 'rundenstart_datum' => null, 'roundpointsflag' => 0];
        }
        return $this->system;
    }

    /**
     * Zustand der Schläfer. Legt die Zeile bei Bedarf an und setzt nach einem Rundenwechsel alles zurück,
     * ohne Hooks in reset.sql oder wt_auto_reset.php (wie SiegelService).
     */
    public function getState(): array
    {
        if ($this->state !== null) {
            return $this->state;
        }
        try {
            return $this->ladeState();
        } catch (\mysqli_sql_exception $e) {
            //Tabellen noch nicht eingespielt (de.sql): die Schläfer gelten als schlafend, die Seiten laufen weiter
            error_log('Sektor 666: '.$e->getMessage());
            $this->state = ['round_start' => null, 'stufe' => 0, 'status' => 0, 'hp_max' => 0, 'hp' => 0, 'marke' => 0, 'naechstes_erwachen_wt' => 0, 'stand_wt' => 1, 'history' => ''];
            return $this->state;
        }
    }

    private function ladeState(): array
    {
        $sys = $this->getSystem();
        $wt = max(1, (int)$sys['wt']);
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_sektor666 (id, round_start, stand_wt) VALUES (1, ?, ?)", [$sys['rundenstart_datum'], $wt]);
        $row = $this->readState();

        if ((string)$row['round_start'] !== (string)$sys['rundenstart_datum'] || $wt < (int)$row['stand_wt']) {
            mysqli_execute_query($this->db, "UPDATE de_sektor666 SET round_start = ?, stufe = 0, status = 0, hp_max = 0, hp = 0, marke = 0, naechstes_erwachen_wt = 0, stand_wt = ?, history = '' WHERE id = 1 AND stand_wt = ? AND round_start <=> ?", [$sys['rundenstart_datum'], $wt, $row['stand_wt'], $row['round_start']]);
            if (mysqli_affected_rows($this->db) === 1) {
                mysqli_execute_query($this->db, "DELETE FROM de_sektor666_schaden", []);
                mysqli_execute_query($this->db, "DELETE FROM de_sektor666_mitglied", []);
            }
            $row = $this->readState();
        }

        $this->state = $row;
        return $row;
    }

    private function readState(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_sektor666 WHERE id = 1", []);
        return mysqli_fetch_assoc($res) ?: ['round_start' => null, 'stufe' => 0, 'status' => 0, 'hp_max' => 0, 'hp' => 0, 'marke' => 0, 'naechstes_erwachen_wt' => 0, 'stand_wt' => 1, 'history' => ''];
    }

    public function isWach(): bool
    {
        return self::istAktiv() && (int)$this->getState()['status'] === self::STATUS_WACH;
    }

    public function getStufe(): int
    {
        return (int)$this->getState()['stufe'];
    }

    /**
     * Hülle in Prozent, aufgerundet: solange die Schläfer wach sind, nie 0 %.
     */
    public function getHuelleProzent(): int
    {
        $state = $this->getState();
        return self::prozent((int)$state['hp'], (int)$state['hp_max']);
    }

    private static function prozent(int $hp, int $hpMax): int
    {
        if ($hpMax <= 0 || $hp <= 0) {
            return 0;
        }
        return (int)min(100, ceil($hp * 100 / $hpMax));
    }

    public function istErhabenenkampf(): bool
    {
        return (int)$this->getSystem()['roundpointsflag'] === 1;
    }

    /**
     * WT, ab dem die nächste Stufe erwacht. Vor dem ersten Erwachen gilt sv_s666_start.
     */
    public function getNaechstesErwachenWt(): int
    {
        $state = $this->getState();
        if ((int)$state['naechstes_erwachen_wt'] > 0) {
            return (int)$state['naechstes_erwachen_wt'];
        }
        return (int)ceil(self::getStartProzent() * max(1, (int)($GLOBALS['sv_winscore'] ?? 1)) / 100);
    }

    /**
     * Wirtschaftsticks bis zum nächsten Erwachen; null, wenn die Schläfer in dieser Runde nicht mehr erwachen.
     */
    public function getWtBisErwachen(): ?int
    {
        if ($this->istErhabenenkampf()) {
            return null;
        }
        return max(0, $this->getNaechstesErwachenWt() - (int)$this->getSystem()['wt']);
    }

    /**
     * Reisezeit einer Sektorflotte nach 666 in KT, wie attdef() in bkmenu.php: 12, mit Sprungfeldbegrenzer 13.
     */
    public function getReisezeit(): int
    {
        $res = mysqli_execute_query($this->db, "SELECT techs FROM de_sector WHERE sec_id = ?", [self::SEKTOR]);
        $techs = (string)(mysqli_fetch_assoc($res)['techs'] ?? '');
        return 12 + ((($techs[2] ?? '0') === '1') ? 1 : 0);
    }

    /**
     * Sektoren, die beim Erwachen zählen: mindestens ein aktiver menschlicher Spieler, ohne Sektor 1 und 666.
     * de_sector.npc reicht dafür nicht: Sektoren, in denen nur Bots (npc=2) leben, stehen dort ebenfalls auf 0.
     */
    private function getTeilnahmeSektoren(): array
    {
        $res = mysqli_execute_query($this->db, "SELECT DISTINCT d.sector AS sec_id FROM de_user_data d JOIN de_login l ON l.user_id = d.user_id WHERE d.npc = 0 AND l.status = 1 AND d.sector > 1 AND d.sector <> ? ORDER BY d.sector", [self::SEKTOR]);
        return array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'sec_id'));
    }

    /**
     * Hülle vor dem Stufenfaktor: so viele Sektorschiffe, wie die teilnehmenden Sektoren beim Erwachen haben,
     * mal sv_s666_hp_je_schiff, mindestens sv_s666_hp_je_sektor je Sektor. Damit passt sie sich an Server und
     * Rundenstand an; die Server unterscheiden sich in der Flottengröße um ein Vielfaches.
     */
    private function getGrundhuelle(array $sektoren): float
    {
        $schiffe = 0;
        if (!empty($sektoren)) {
            $platzhalter = implode(',', array_fill(0, count($sektoren), '?'));
            $res = mysqli_execute_query($this->db, "SELECT SUM(e1 + e2) AS schiffe FROM de_sector WHERE sec_id IN (".$platzhalter.")", $sektoren);
            $schiffe = (int)(mysqli_fetch_assoc($res)['schiffe'] ?? 0);
        }
        return max(count($sektoren) * self::getHuelleJeSektor(), $schiffe * self::getHuelleJeSchiff());
    }

    /**
     * Rangliste einer Stufe nach Schaden, mit Platz und Anteil in Prozent (eine Nachkommastelle).
     */
    public function getRangliste(int $stufe): array
    {
        $state = $this->getState();
        $hpMax = (int)$state['stufe'] === $stufe ? (int)$state['hp_max'] : 0;
        $res = mysqli_execute_query($this->db, "SELECT sec_id, schaden, angriffe, verluste FROM de_sektor666_schaden WHERE stufe = ? ORDER BY schaden DESC, sec_id ASC", [$stufe]);
        $liste = [];
        $platz = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $platz++;
            $liste[] = [
                'platz' => $platz,
                'sec_id' => (int)$row['sec_id'],
                'schaden' => (int)$row['schaden'],
                'angriffe' => (int)$row['angriffe'],
                'verluste' => (int)$row['verluste'],
                'anteil' => $hpMax > 0 ? round((int)$row['schaden'] * 100 / $hpMax, 1) : 0.0,
            ];
        }
        return $liste;
    }

    /**
     * Platz und Anteil eines Sektors in der laufenden Stufe, null ohne Angriff.
     */
    public function getEigenerRang(int $secId): ?array
    {
        foreach ($this->getRangliste($this->getStufe()) as $zeile) {
            if ($zeile['sec_id'] === $secId) {
                return $zeile;
            }
        }
        return null;
    }

    /**
     * War der Spieler beim Erwachen der laufenden Stufe in diesem Sektor?
     */
    public function istDabei(int $uid, int $secId): bool
    {
        $res = mysqli_execute_query($this->db, "SELECT 1 FROM de_sektor666_mitglied WHERE stufe = ? AND user_id = ? AND sec_id = ?", [$this->getStufe(), $uid, $secId]);
        return mysqli_num_rows($res) > 0;
    }

    /**
     * Verlauf der Runde, neueste Zeile zuerst.
     */
    public function getVerlauf(): array
    {
        return array_values(array_filter(explode("\n", (string)$this->getState()['history'])));
    }

    /**
     * Aufruf im Wirtschaftstick: Erwachen, oder Einschlafen ohne Beute, sobald der Erhabenenkampf beginnt.
     */
    public function processWt(array $lang): void
    {
        if (!self::istAktiv()) {
            return;
        }
        $state = $this->getState();
        $wt = (int)$this->getSystem()['wt'];

        if ((int)$state['status'] === self::STATUS_WACH) {
            if ($this->istErhabenenkampf()) {
                $this->einschlafen($lang);
            }
            return;
        }

        if (!$this->istErhabenenkampf() && $wt >= $this->getNaechstesErwachenWt()) {
            $this->erwachen($lang);
        }
    }

    private function erwachen(array $lang): void
    {
        $state = $this->getState();
        $wt = (int)$this->getSystem()['wt'];
        $stufe = (int)$state['stufe'] + 1;
        $sektoren = $this->getTeilnahmeSektoren();
        if (count($sektoren) === 0) {
            return;
        }
        $hpMax = (int)max(1, round($this->getGrundhuelle($sektoren) * pow(self::getStufenfaktor(), $stufe - 1)));
        $zeile = strtr($lang['verlauf_erwacht'], ['{WT}' => $wt, '{STUFE}' => $stufe, '{HUELLE}' => self::zahl($hpMax)]);

        mysqli_execute_query($this->db, "UPDATE de_sektor666 SET stufe = ?, status = 1, hp_max = ?, hp = ?, marke = 100, stand_wt = ?, history = LEFT(CONCAT(?, history), 2000) WHERE id = 1 AND status = 0 AND stufe = ?", [$stufe, $hpMax, $hpMax, $wt, $zeile."\n", $stufe - 1]);
        if (mysqli_affected_rows($this->db) !== 1) {
            $this->state = null;
            return;
        }
        $this->state = null;

        //wer beim Erwachen in einem teilnehmenden Sektor ist, kann Beute bekommen (Schutz gegen Zuzug)
        mysqli_execute_query($this->db, "DELETE FROM de_sektor666_schaden WHERE stufe >= ?", [$stufe]);
        mysqli_execute_query($this->db, "DELETE FROM de_sektor666_mitglied WHERE stufe >= ?", [$stufe]);
        mysqli_execute_query($this->db, "INSERT INTO de_sektor666_mitglied (stufe, user_id, sec_id) SELECT ?, d.user_id, d.sector FROM de_user_data d WHERE d.npc = 0 AND d.sector > 1 AND d.sector <> ?", [$stufe, self::SEKTOR]);

        $ersetzen = ['{STUFE}' => $stufe, '{HUELLE}' => self::zahl($hpMax)];
        $this->chatServer(strtr($lang['chat_erwacht'], $ersetzen), true);

        //alle aktiven Spieler benachrichtigen
        $time = date('YmdHis');
        mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) SELECT d.user_id, 60, ?, ? FROM de_user_data d JOIN de_login l ON l.user_id = d.user_id WHERE d.npc = 0 AND l.status = 1", [$time, strtr($lang['news_erwacht'], $ersetzen)]);
        mysqli_execute_query($this->db, "UPDATE de_user_data d JOIN de_login l ON l.user_id = d.user_id SET d.newnews = 1 WHERE d.npc = 0 AND l.status = 1", []);
    }

    private function einschlafen(array $lang): void
    {
        $state = $this->getState();
        $wt = (int)$this->getSystem()['wt'];
        $stufe = (int)$state['stufe'];
        $prozent = $this->getHuelleProzent();
        $zeile = strtr($lang['verlauf_eingeschlafen'], ['{WT}' => $wt, '{STUFE}' => $stufe]);

        mysqli_execute_query($this->db, "UPDATE de_sektor666 SET status = 0, stand_wt = ?, history = LEFT(CONCAT(?, history), 2000) WHERE id = 1 AND status = 1 AND stufe = ?", [$wt, $zeile."\n", $stufe]);
        $this->state = null;
        if (mysqli_affected_rows($this->db) === 1) {
            $this->chatServer(strtr($lang['chat_eingeschlafen'], ['{STUFE}' => $stufe, '{PCT}' => $prozent]), true);
        }
    }

    /**
     * Angriff einer Sektorflotte im Kampftick. Zieht die Verluste von der Sektorflotte ab und schickt sie ohne
     * Überlebende sofort heim; den Rückflug der übrigen übernimmt kt.php.
     *
     * @return array ['kampf' => bool, 'schaden', 'verluste', 'hp', 'besiegt']
     */
    public function angriff(int $secId, int $schiffe, array $lang): array
    {
        $state = $this->getState();
        if (!$this->isWach() || $schiffe <= 0 || (int)$state['hp'] <= 0) {
            return ['kampf' => false];
        }
        $stufe = (int)$state['stufe'];
        $hp = (int)$state['hp'];
        $hpMax = (int)$state['hp_max'];

        $schaden = min($schiffe, $hp);
        $verluste = (int)min($schiffe, ceil($schiffe * self::getAbwehrProzent() / 100));

        mysqli_execute_query($this->db, "UPDATE de_sektor666 SET hp = hp - ? WHERE id = 1 AND status = 1 AND stufe = ? AND hp >= ?", [$schaden, $stufe, $schaden]);
        if (mysqli_affected_rows($this->db) !== 1) {
            $this->state = null;
            return ['kampf' => false];
        }
        $hpNeu = $hp - $schaden;
        $this->state = null;

        mysqli_execute_query($this->db, "INSERT INTO de_sektor666_schaden (stufe, sec_id, schaden, angriffe, verluste) VALUES (?, ?, ?, 1, ?) ON DUPLICATE KEY UPDATE schaden = schaden + VALUES(schaden), angriffe = angriffe + 1, verluste = verluste + VALUES(verluste)", [$stufe, $secId, $schaden, $verluste]);

        //Verluste der Sektorflotte, ohne Überlebende sofort heim (wie kt_sectorkampf.php)
        mysqli_execute_query($this->db, "UPDATE de_sector SET e2 = IF(e2 > ?, e2 - ?, 0) WHERE sec_id = ?", [$verluste, $verluste, $secId]);
        if ($schiffe - $verluste <= 0) {
            mysqli_execute_query($this->db, "UPDATE de_sector SET aktion = 0, zeit = 0, aktzeit = 0, zielsec = 0, gesrzeit = 0 WHERE sec_id = ?", [$secId]);
        }

        $prozent = self::prozent($hpNeu, $hpMax);
        $bericht = strtr($lang['bericht'], ['{SCHIFFE}' => self::zahl($schiffe), '{SCHADEN}' => self::zahl($schaden), '{VERLUSTE}' => self::zahl($verluste), '{HP}' => self::zahl($hpNeu), '{HPMAX}' => self::zahl($hpMax), '{PCT}' => $prozent]);
        if ($hpNeu <= 0) {
            $bericht .= $lang['bericht_besiegt'];
        }
        $this->sendeBericht($secId, $bericht);
        $this->chatSektor($secId, strtr($lang['sektorchat_angriff'], ['{SCHADEN}' => self::zahl($schaden), '{VERLUSTE}' => self::zahl($verluste), '{PCT}' => $prozent]));

        //Schwellen 75/50/25 %: nur die niedrigste neu erreichte melden
        if ($hpNeu > 0) {
            $marke = (int)$state['marke'];
            $neu = $marke;
            foreach (self::SCHWELLEN as $schwelle) {
                if ($schwelle < $neu && $hpNeu * 100 <= $schwelle * $hpMax) {
                    $neu = $schwelle;
                }
            }
            if ($neu < $marke) {
                mysqli_execute_query($this->db, "UPDATE de_sektor666 SET marke = ? WHERE id = 1 AND stufe = ?", [$neu, $stufe]);
                $this->chatServer(strtr($lang['chat_schwelle'], ['{PCT}' => $neu]), false);
            }
        } else {
            $this->besiegt($lang);
        }

        return ['kampf' => true, 'schaden' => $schaden, 'verluste' => $verluste, 'hp' => $hpNeu, 'besiegt' => $hpNeu <= 0];
    }

    /**
     * Hülle zerstört: Beute, Ausgleich ins Sektorlager, Meldungen, nächstes Erwachen nach der Pause.
     */
    private function besiegt(array $lang): void
    {
        $state = $this->getState();
        $stufe = (int)$state['stufe'];
        $hpMax = (int)$state['hp_max'];
        $wt = (int)$this->getSystem()['wt'];
        $rangliste = $this->getRangliste($stufe);
        $naechstes = $wt + (int)ceil(self::getPauseProzent() * max(1, (int)($GLOBALS['sv_winscore'] ?? 1)) / 100);
        $erster = $rangliste[0]['sec_id'] ?? 0;
        $zeile = strtr($lang['verlauf_besiegt'], ['{WT}' => $wt, '{STUFE}' => $stufe, '{SEK}' => $erster]);

        mysqli_execute_query($this->db, "UPDATE de_sektor666 SET status = 0, hp = 0, naechstes_erwachen_wt = ?, stand_wt = ?, history = LEFT(CONCAT(?, history), 2000) WHERE id = 1 AND status = 1 AND stufe = ?", [$naechstes, $wt, $zeile."\n", $stufe]);
        $this->state = null;
        if (mysqli_affected_rows($this->db) !== 1) {
            return;
        }

        $time = date('YmdHis');
        $top = [];
        foreach ($rangliste as $sektor) {
            //Ausgleich ins Sektorlager für alle Sektoren mit Verlusten
            $ausgleich = [];
            if ($sektor['verluste'] > 0 && self::getAusgleichProzent() > 0) {
                foreach (self::SCHIFFSKOSTEN as $res => $kosten) {
                    $ausgleich[$res] = (int)floor($sektor['verluste'] * $kosten * self::getAusgleichProzent() / 100);
                }
                mysqli_execute_query($this->db, "UPDATE de_sector SET restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE sec_id = ?", [$ausgleich[1], $ausgleich[2], $ausgleich[3], $ausgleich[4], $sektor['sec_id']]);
            }

            if ($hpMax <= 0 || $sektor['schaden'] * 100 < self::getMindestanteil() * $hpMax) {
                continue;
            }
            if (count($top) < 3) {
                $top[] = strtr($lang['top_eintrag'], ['{SEK}' => $sektor['sec_id'], '{ANTEIL}' => self::anteil($sektor['anteil'])]);
            }

            $beute = self::beuteFuer($sektor['platz'], $stufe);
            foreach ($this->getEmpfaenger($stufe, $sektor['sec_id']) as $uid) {
                $liste = $this->zahleAus($uid, $beute, $lang);
                $text = strtr($lang['news_beute'], ['{SEK}' => $sektor['sec_id'], '{ANTEIL}' => self::anteil($sektor['anteil']), '{PLATZ}' => $sektor['platz'], '{LISTE}' => '<br>'.implode('<br>', $liste)]);
                if (!empty($ausgleich)) {
                    $text .= strtr($lang['news_ausgleich'], ['{VERLUSTE}' => self::zahl($sektor['verluste']), '{RES}' => 'M '.self::zahl($ausgleich[1]).', D '.self::zahl($ausgleich[2]).', I '.self::zahl($ausgleich[3]).', E '.self::zahl($ausgleich[4])]);
                }
                mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 60, ?, ?)", [$uid, $time, $text]);
            }
        }

        $this->chatServer(strtr($lang['chat_besiegt'], ['{STUFE}' => $stufe, '{TOP}' => implode(', ', $top)]), true);
    }

    /**
     * Empfänger der Beute: beim Erwachen im Sektor, jetzt noch dort, menschlich und aktiv (kein Urlaub).
     */
    private function getEmpfaenger(int $stufe, int $secId): array
    {
        $res = mysqli_execute_query($this->db, "SELECT m.user_id FROM de_sektor666_mitglied m JOIN de_user_data d ON d.user_id = m.user_id JOIN de_login l ON l.user_id = m.user_id WHERE m.stufe = ? AND m.sec_id = ? AND d.sector = m.sec_id AND d.npc = 0 AND l.status = 1 ORDER BY m.user_id", [$stufe, $secId]);
        return array_map('intval', array_column(mysqli_fetch_all($res, MYSQLI_ASSOC), 'user_id'));
    }

    /**
     * Schreibt einem Spieler die Beute gut und liefert die Posten für die Nachricht.
     */
    private function zahleAus(int $uid, array $beute, array $lang): array
    {
        $posten = [];
        $kriegsartefakte = $beute['kriegsartefakte'];
        $ersatz = 0;

        if ($beute['artefakte'] > 0) {
            $frei = (int)\get_free_artefact_places($uid);
            for ($i = 0; $i < $beute['artefakte']; $i++) {
                if ($frei > 0) {
                    $id = self::ARTEFAKTE[mt_rand(0, count(self::ARTEFAKTE) - 1)];
                    mysqli_execute_query($this->db, "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", [$uid, $id]);
                    $posten[] = strtr($lang['posten_artefakt'], ['{NAME}' => self::artefaktName($id)]);
                    $frei--;
                } else {
                    $ersatz++;
                }
            }
        }

        mysqli_execute_query($this->db, "UPDATE de_user_data SET restyp05 = restyp05 + ?, kartefakt = kartefakt + ?, newnews = 1 WHERE user_id = ?", [$beute['tronic'], $kriegsartefakte + $ersatz, $uid]);
        if ($beute['kerne'] > 0) {
            \change_storage_amount($uid, 2, $beute['kerne'], false);
        }
        if ($beute['palenium'] > 0) {
            \change_storage_amount($uid, 1, $beute['palenium'], false);
        }

        if ($beute['tronic'] > 0) {
            array_unshift($posten, strtr($lang['posten_tronic'], ['{N}' => self::zahl($beute['tronic'])]));
        }
        if ($beute['kerne'] > 0) {
            $posten[] = strtr($lang['posten_kerne'], ['{N}' => self::zahl($beute['kerne'])]);
        }
        if ($beute['palenium'] > 0) {
            $posten[] = strtr($lang['posten_palenium'], ['{N}' => self::zahl($beute['palenium'])]);
        }
        if ($kriegsartefakte > 0) {
            $posten[] = strtr($lang['posten_kriegsartefakte'], ['{N}' => $kriegsartefakte]);
        }
        if ($ersatz > 0) {
            $posten[] = strtr($lang['posten_ersatz'], ['{N}' => $ersatz]);
        }
        return $posten;
    }

    private static function artefaktName(int $id): string
    {
        static $namen = null;
        if ($namen === null) {
            $namen = (static function (): array {
                include __DIR__.'/../../../inc/userartefact.inc.php';
                return $ua_name ?? [];
            })();
        }
        return $namen[$id - 1] ?? 'Artefakt';
    }

    /**
     * Bericht an den Sektorkommandanten (Nachricht Typ 56 wie die Sektorkampfberichte).
     */
    private function sendeBericht(int $secId, string $text): void
    {
        $system = (int)\getSKSystemBySecID($secId);
        if ($system <= 0) {
            return;
        }
        $res = mysqli_execute_query($this->db, "SELECT user_id FROM de_user_data WHERE sector = ? AND `system` = ?", [$secId, $system]);
        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            return;
        }
        mysqli_execute_query($this->db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 56, ?, ?)", [$row['user_id'], date('YmdHis'), $text]);
        mysqli_execute_query($this->db, "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$row['user_id']]);
    }

    /**
     * Serverchat. Wichtige Meldungen (Erwachen, Sieg, Einschlafen) gehen auch an Discord, Schwellen nur in den Chat.
     */
    private function chatServer(string $html, bool $discord): void
    {
        $html = '<span style="color: #d9534f;">'.$html.'</span>';
        if ($discord) {
            \insert_chat_msg(0, 2, '[SYSTEM]', $html);
        } else {
            \insert_chat_msg_admin(0, 2, '', $html, 0, $GLOBALS['sv_server_tag'] ?? '');
        }
    }

    private function chatSektor(int $secId, string $text): void
    {
        \insert_chat_msg_admin($secId, 0, '', $text, 0, $GLOBALS['sv_server_tag'] ?? '');
    }

    public static function zahl(int $wert): string
    {
        return number_format($wert, 0, ',', '.');
    }

    public static function anteil(float $wert): string
    {
        return number_format($wert, 1, ',', '.');
    }
}
