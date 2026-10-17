<?php
namespace DieEwigen\DE2\Model\Sektorprojekt;

/**
 * Sektorprojekte und Kampfmittel aus den Vergessenen Systemen (Regeln: docs/sektorprojekte.md).
 *
 * Jeder Sektor hat ein Sektor-VS-Lager (de_sector_vs_lager) für VS-Rohstoffe und Handelswaren, gefüllt durch
 * Einzahlungen der Mitglieder (de_sector_vs_einzahlung, resource.php). Daraus baut der SK in bkmenu.php
 * Sektorprojekte mit je drei Stufen und fertigt Kampfmittel für die Sektorflotte; der Zustand je Sektor steht in
 * de_sector_vs. Kampfmittel entstehen über de_sector_build (tech_id 2 bis 5, Sektorraumschiffe haben tech_id 1).
 *
 * Eingebunden in tickler/wt.php (processWt, kampfmittelFertig), tickler/wt_manage_map.php und vs_bonus_info() in
 * functions.php (Raffinerie, Relais), tickler/kt_sectorkampf.php (Gitter, Schild, Enterkommando), bkmenu.php
 * (Bau, Einsatz, Nachbrenner, Scannerphalanx), resource.php, politics.php, sector.php und map_mobile.php.
 * Fehlen die Tabellen noch, liefern die lesenden Methoden leere Werte, damit die Seiten weiter laufen.
 */
class SektorprojektService
{
    public const RELAIS = 1;
    public const RAFFINERIE = 2;
    public const WERFT = 3;
    public const GITTER = 4;
    public const PROJEKTE = [self::RELAIS => 'relais', self::RAFFINERIE => 'raffinerie', self::WERFT => 'werft', self::GITTER => 'gitter'];
    public const MAX_STUFE = 3;
    //Wirkung in Prozent, Index = Stufe
    public const PROZENT = [
        self::RELAIS => [0, 5, 10, 15],
        self::RAFFINERIE => [0, 5, 10, 15],
        self::WERFT => [0, 10, 20, 30],
        self::GITTER => [0, 10, 20, 30],
    ];
    //Kosten (item_id => Menge) und Bauzeit je Stufe, gleich für alle Projekte
    public const STUFEN = [
        1 => ['kosten' => [3 => 20000, 4 => 10000, 14 => 20], 'wt' => 96],
        2 => ['kosten' => [5 => 20000, 6 => 10000, 15 => 20], 'wt' => 144],
        3 => ['kosten' => [7 => 20000, 8 => 10000, 16 => 20], 'wt' => 192],
    ];

    //Kampfmittel; die ID ist zugleich tech_id in de_sector_build
    public const SCHILD = 2;
    public const NACHBRENNER = 3;
    public const STOERSENDER = 4;
    public const ENTER = 5;
    public const KAMPFMITTEL = [self::SCHILD => 'schild', self::NACHBRENNER => 'nachbrenner', self::STOERSENDER => 'stoersender', self::ENTER => 'enter'];
    public const KM_KOSTEN = [self::SCHILD => [15 => 20], self::NACHBRENNER => [14 => 20], self::STOERSENDER => [16 => 10], self::ENTER => [17 => 10]];
    public const KM_BAUZEIT = 16;
    public const KM_MAX = 3;
    public const SCHILD_KT = 24;
    public const SCHILD_FAKTOR = 0.5;
    public const NACHBRENNER_KT = 2;
    public const STOERSENDER_WT = 48;
    public const ENTER_FAKTOR = 1.5;

    //einzahlbare Items: VS-Rohstoffe Eisen bis Octagium und Handelswaren I bis V
    public const ITEMS = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 14, 15, 16, 17, 18];

    private \mysqli $db;
    private ?array $namen = null;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    public static function leer(int $secId): array
    {
        return ['sec_id' => $secId, 'relais' => 0, 'raffinerie' => 0, 'werft' => 0, 'gitter' => 0, 'bau_projekt' => 0, 'bau_rest_wt' => 0,
            'km_schild' => 0, 'km_nachbrenner' => 0, 'km_stoersender' => 0, 'km_enter' => 0, 'schild_bis_kt' => 0, 'stoersender_bis_wt' => 0, 'enter_aktiv' => 0];
    }

    public function getStatus(int $secId): array
    {
        $row = null;
        try {
            $res = mysqli_execute_query($this->db, "SELECT * FROM de_sector_vs WHERE sec_id=?", [$secId]);
            $row = mysqli_fetch_assoc($res);
        } catch (\Throwable $e) {
            $row = null;
        }
        return $row ? array_map('intval', $row) : self::leer($secId);
    }

    /** Zustand aller Sektoren mit Eintrag, sec_id => Zeile (für die Ticks) */
    public function loadAll(): array
    {
        $alle = [];
        try {
            $res = mysqli_execute_query($this->db, "SELECT * FROM de_sector_vs", []);
            while ($row = mysqli_fetch_assoc($res)) {
                $alle[(int)$row['sec_id']] = array_map('intval', $row);
            }
        } catch (\Throwable $e) {
        }
        return $alle;
    }

    private function anlegen(int $secId): void
    {
        mysqli_execute_query($this->db, "INSERT IGNORE INTO de_sector_vs (sec_id) VALUES (?)", [$secId]);
    }

    /** Aktueller Wirtschafts- und Kampftick */
    public function aktuelleTicks(): array
    {
        $row = mysqli_fetch_assoc(mysqli_execute_query($this->db, "SELECT wt, kt FROM de_system LIMIT 1", []));
        return ['wt' => (int)($row['wt'] ?? 0), 'kt' => (int)($row['kt'] ?? 0)];
    }

    //////////////////////////////////////////////////////////////
    // Sektor-VS-Lager
    //////////////////////////////////////////////////////////////

    /** Bestand des Sektors, item_id => Menge (nur Posten über 0) */
    public function getLager(int $secId): array
    {
        $lager = [];
        try {
            $res = mysqli_execute_query($this->db, "SELECT item_id, menge FROM de_sector_vs_lager WHERE sec_id=? AND menge>0 ORDER BY item_id", [$secId]);
            while ($row = mysqli_fetch_assoc($res)) {
                $lager[(int)$row['item_id']] = (int)$row['menge'];
            }
        } catch (\Throwable $e) {
        }
        return $lager;
    }

    /** Namen aller Items, item_id => Name */
    public function itemNamen(): array
    {
        if ($this->namen === null) {
            $this->namen = [];
            $res = mysqli_execute_query($this->db, "SELECT item_id, item_name FROM de_item_data ORDER BY item_sort_order", []);
            while ($row = mysqli_fetch_assoc($res)) {
                $this->namen[(int)$row['item_id']] = $row['item_name'];
            }
        }
        return $this->namen;
    }

    /** Posten als Text, z. B. "20.000 Eisen · 20 Handelswaren I" */
    public function postenText(array $posten): string
    {
        $namen = $this->itemNamen();
        $teile = [];
        foreach ($posten as $item => $menge) {
            $teile[] = number_format($menge, 0, ',', '.').' '.($namen[$item] ?? '#'.$item);
        }
        return implode(' &middot; ', $teile);
    }

    public function bezahlbar(array $lager, array $kosten): bool
    {
        foreach ($kosten as $item => $menge) {
            if (($lager[$item] ?? 0) < $menge) {
                return false;
            }
        }
        return true;
    }

    private function abbuchen(int $secId, array $kosten): void
    {
        foreach ($kosten as $item => $menge) {
            mysqli_execute_query($this->db, "UPDATE de_sector_vs_lager SET menge=menge-? WHERE sec_id=? AND item_id=? AND menge>=?", [$menge, $secId, $item, $menge]);
        }
    }

    /**
     * Einzahlung eines Spielers, je Item höchstens der eigene Bestand. Der Aufrufer hält die Spielersperre.
     * Ergebnis: item_id => tatsächlich eingezahlte Menge.
     */
    public function einzahlen(int $secId, int $uid, array $wunsch): array
    {
        $bestand = [];
        $res = mysqli_execute_query($this->db, "SELECT item_id, item_amount FROM de_user_storage WHERE user_id=?", [$uid]);
        while ($row = mysqli_fetch_assoc($res)) {
            $bestand[(int)$row['item_id']] = (int)$row['item_amount'];
        }
        $ergebnis = [];
        foreach (self::ITEMS as $item) {
            $menge = min((int)($wunsch[$item] ?? 0), $bestand[$item] ?? 0);
            if ($menge <= 0) {
                continue;
            }
            if (empty($ergebnis)) {
                $this->anlegen($secId);
            }
            change_storage_amount($uid, $item, -$menge);
            mysqli_execute_query($this->db, "INSERT INTO de_sector_vs_lager (sec_id, item_id, menge) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE menge=menge+?", [$secId, $item, $menge, $menge]);
            mysqli_execute_query($this->db, "INSERT INTO de_sector_vs_einzahlung (sec_id, user_id, item_id, menge) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE menge=menge+?", [$secId, $uid, $item, $menge, $menge]);
            $ergebnis[$item] = $menge;
        }
        return $ergebnis;
    }

    /** Einzahlungen der aktuellen Sektormitglieder: user_id => ['name' => Spielername, 'items' => [item_id => Menge]] */
    public function getEinzahlungen(int $secId): array
    {
        $liste = [];
        try {
            $res = mysqli_execute_query($this->db, "SELECT e.user_id, e.item_id, e.menge, u.spielername FROM de_sector_vs_einzahlung e
                JOIN de_user_data u ON (u.user_id=e.user_id AND u.sector=e.sec_id) WHERE e.sec_id=? AND e.menge>0 ORDER BY u.`system`, e.item_id", [$secId]);
            while ($row = mysqli_fetch_assoc($res)) {
                $liste[(int)$row['user_id']]['name'] = $row['spielername'];
                $liste[(int)$row['user_id']]['items'][(int)$row['item_id']] = (int)$row['menge'];
            }
        } catch (\Throwable $e) {
        }
        return $liste;
    }

    /** user_id des Sektorkommandanten, 0 wenn es keinen gibt */
    public function skUserId(int $secId): int
    {
        if (!function_exists('getSKSystemBySecID')) {
            return 0;
        }
        $system = (int)getSKSystemBySecID($secId);
        if ($system <= 0) {
            return 0;
        }
        $row = mysqli_fetch_assoc(mysqli_execute_query($this->db, "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$secId, $system]));
        return (int)($row['user_id'] ?? 0);
    }

    //////////////////////////////////////////////////////////////
    // Sektorprojekte
    //////////////////////////////////////////////////////////////

    /** Je Projekt: Stufe, nächste Stufe mit Kosten und Bauzeit, bezahlbar, im Bau */
    public function projektUebersicht(int $secId): array
    {
        $status = $this->getStatus($secId);
        $lager = $this->getLager($secId);
        $liste = [];
        foreach (self::PROJEKTE as $id => $spalte) {
            $stufe = $status[$spalte];
            $naechste = $stufe < self::MAX_STUFE ? $stufe + 1 : 0;
            $liste[$id] = [
                'stufe' => $stufe,
                'naechste' => $naechste,
                'kosten' => $naechste ? self::STUFEN[$naechste]['kosten'] : [],
                'wt' => $naechste ? self::STUFEN[$naechste]['wt'] : 0,
                'bezahlbar' => $naechste ? $this->bezahlbar($lager, self::STUFEN[$naechste]['kosten']) : false,
                'im_bau' => $status['bau_projekt'] == $id,
                'rest_wt' => $status['bau_projekt'] == $id ? $status['bau_rest_wt'] : 0,
                'laeuft_anderes' => $status['bau_projekt'] > 0 && $status['bau_projekt'] != $id,
            ];
        }
        return $liste;
    }

    /** Projekt beauftragen. Ergebnis: Schlüssel aus $lang['fehler'] oder '' bei Erfolg. */
    public function projektStarten(int $secId, int $projekt, int $wt, array $lang): string
    {
        if (!isset(self::PROJEKTE[$projekt])) {
            return 'unbekannt';
        }
        $status = $this->getStatus($secId);
        if ($status['bau_projekt'] > 0) {
            return 'im_bau';
        }
        $stufe = $status[self::PROJEKTE[$projekt]] + 1;
        if ($stufe > self::MAX_STUFE) {
            return 'maximal';
        }
        $kosten = self::STUFEN[$stufe]['kosten'];
        if (!$this->bezahlbar($this->getLager($secId), $kosten)) {
            return 'rohstoffe';
        }
        $this->anlegen($secId);
        $this->abbuchen($secId, $kosten);
        mysqli_execute_query($this->db, "UPDATE de_sector_vs SET bau_projekt=?, bau_rest_wt=? WHERE sec_id=?", [$projekt, self::STUFEN[$stufe]['wt'], $secId]);
        insert_chat_msg($secId, 0, '', strtr($lang['chat_projekt_start'], ['{NAME}' => $lang['projekt'][$projekt], '{STUFE}' => $stufe, '{WT}' => self::STUFEN[$stufe]['wt']]));
        mysqli_execute_query($this->db, "INSERT INTO de_news_sector (wt, typ, sector, text) VALUES (?, 7, ?, ?)", [$wt, $secId, $lang['projekt'][$projekt].' Stufe '.$stufe]);
        return '';
    }

    /** WT: Bauzeit herunterzählen, fertige Stufen gutschreiben und im Sektorchat melden (wie die Sektorgebäude in wt.php) */
    public function processWt(array $lang): void
    {
        $fertig = [];
        $res = mysqli_execute_query($this->db, "SELECT sec_id, bau_projekt, relais, raffinerie, werft, gitter FROM de_sector_vs WHERE bau_projekt>0 AND bau_rest_wt<=1", []);
        while ($row = mysqli_fetch_assoc($res)) {
            $fertig[] = $row;
        }
        mysqli_execute_query($this->db, "UPDATE de_sector_vs SET bau_rest_wt=bau_rest_wt-1 WHERE bau_projekt>0 AND bau_rest_wt>0", []);
        foreach ($fertig as $row) {
            $projekt = (int)$row['bau_projekt'];
            $spalte = self::PROJEKTE[$projekt] ?? '';
            if ($spalte === '') {
                mysqli_execute_query($this->db, "UPDATE de_sector_vs SET bau_projekt=0, bau_rest_wt=0 WHERE sec_id=?", [$row['sec_id']]);
                continue;
            }
            $stufe = min(self::MAX_STUFE, (int)$row[$spalte] + 1);
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET `$spalte`=?, bau_projekt=0, bau_rest_wt=0 WHERE sec_id=?", [$stufe, $row['sec_id']]);
            insert_chat_msg((int)$row['sec_id'], 0, '', strtr($lang['chat_projekt_fertig'], ['{NAME}' => $lang['projekt'][$projekt], '{STUFE}' => $stufe]));
            echo '<br>Sektor '.$row['sec_id'].': '.$lang['projekt'][$projekt].' Stufe '.$stufe.' fertig';
        }
    }

    //////////////////////////////////////////////////////////////
    // Kampfmittel
    //////////////////////////////////////////////////////////////

    /** Je Kampfmittel: Bestand, im Bau, Kosten, bezahlbar, aktive Restzeit (Schild in KT, Störsender in WT), einsetzbar */
    public function kampfmittelUebersicht(int $secId, int $wt, int $kt): array
    {
        $status = $this->getStatus($secId);
        $lager = $this->getLager($secId);
        $imBau = [];
        try {
            $res = mysqli_execute_query($this->db, "SELECT tech_id, SUM(anzahl) AS anzahl FROM de_sector_build WHERE sector_id=? AND tech_id>1 GROUP BY tech_id", [$secId]);
            while ($row = mysqli_fetch_assoc($res)) {
                $imBau[(int)$row['tech_id']] = (int)$row['anzahl'];
            }
        } catch (\Throwable $e) {
        }
        $liste = [];
        foreach (self::KAMPFMITTEL as $id => $spalte) {
            $bestand = $status['km_'.$spalte];
            //scharf bis einschließlich Tick "bis"; $kt und $wt sind die zuletzt gelaufenen Ticks, also bleiben bis-jetzt Ticks
            $aktiv = 0;
            if ($id == self::SCHILD && $status['schild_bis_kt'] > $kt) {
                $aktiv = $status['schild_bis_kt'] - $kt;
            }
            if ($id == self::STOERSENDER && $status['stoersender_bis_wt'] > $wt) {
                $aktiv = $status['stoersender_bis_wt'] - $wt;
            }
            $liste[$id] = [
                'bestand' => $bestand,
                'im_bau' => $imBau[$id] ?? 0,
                'kosten' => self::KM_KOSTEN[$id],
                'bezahlbar' => $this->bezahlbar($lager, self::KM_KOSTEN[$id]) && $bestand + ($imBau[$id] ?? 0) < self::KM_MAX,
                'aktiv_rest' => $aktiv,
                'einsetzbar' => in_array($id, [self::SCHILD, self::STOERSENDER]) && $bestand > 0 && $aktiv == 0,
            ];
        }
        return $liste;
    }

    /** Ein Stück in Auftrag geben (de_sector_build, KM_BAUZEIT WT). Ergebnis: Fehlerschlüssel oder ''. */
    public function kampfmittelFertigen(int $secId, int $km): string
    {
        if (!isset(self::KAMPFMITTEL[$km])) {
            return 'unbekannt';
        }
        $u = $this->kampfmittelUebersicht($secId, 0, 0)[$km];
        if ($u['bestand'] + $u['im_bau'] >= self::KM_MAX) {
            return 'maximal';
        }
        if (!$this->bezahlbar($this->getLager($secId), self::KM_KOSTEN[$km])) {
            return 'rohstoffe';
        }
        $this->anlegen($secId);
        $this->abbuchen($secId, self::KM_KOSTEN[$km]);
        $res = mysqli_execute_query($this->db, "SELECT anzahl FROM de_sector_build WHERE sector_id=? AND tech_id=? AND verbzeit=?", [$secId, $km, self::KM_BAUZEIT]);
        if (mysqli_fetch_assoc($res)) {
            mysqli_execute_query($this->db, "UPDATE de_sector_build SET anzahl=anzahl+1 WHERE sector_id=? AND tech_id=? AND verbzeit=?", [$secId, $km, self::KM_BAUZEIT]);
        } else {
            mysqli_execute_query($this->db, "INSERT INTO de_sector_build (sector_id, tech_id, anzahl, verbzeit) VALUES (?, ?, 1, ?)", [$secId, $km, self::KM_BAUZEIT]);
        }
        return '';
    }

    /** WT, Auftrag aus de_sector_build fertig: Bestand erhöhen, höchstens KM_MAX */
    public function kampfmittelFertig(int $secId, int $km, int $anzahl): void
    {
        if (!isset(self::KAMPFMITTEL[$km]) || $anzahl <= 0) {
            return;
        }
        $spalte = 'km_'.self::KAMPFMITTEL[$km];
        $this->anlegen($secId);
        mysqli_execute_query($this->db, "UPDATE de_sector_vs SET `$spalte`=LEAST(?, `$spalte`+?) WHERE sec_id=?", [self::KM_MAX, $anzahl, $secId]);
    }

    /**
     * Schildgenerator oder Störsender scharf schalten, verbraucht ein Stück. $wt und $kt sind die zuletzt gelaufenen Ticks,
     * die Wirkung gilt für die nächsten SCHILD_KT Kampfticks bzw. STOERSENDER_WT Wirtschaftsticks. Ergebnis: Fehlerschlüssel oder ''.
     */
    public function kampfmittelEinsetzen(int $secId, int $km, int $wt, int $kt): string
    {
        $status = $this->getStatus($secId);
        if ($km == self::SCHILD) {
            if ($status['km_schild'] < 1) {
                return 'bestand';
            }
            if ($status['schild_bis_kt'] > $kt) {
                return 'laeuft';
            }
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET km_schild=km_schild-1, schild_bis_kt=? WHERE sec_id=? AND km_schild>0", [$kt + self::SCHILD_KT, $secId]);
            return '';
        }
        if ($km == self::STOERSENDER) {
            if ($status['km_stoersender'] < 1) {
                return 'bestand';
            }
            if ($status['stoersender_bis_wt'] > $wt) {
                return 'laeuft';
            }
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET km_stoersender=km_stoersender-1, stoersender_bis_wt=? WHERE sec_id=? AND km_stoersender>0", [$wt + self::STOERSENDER_WT, $secId]);
            return '';
        }
        return 'unbekannt';
    }

    /** Start der Sektorflotte (bkmenu.php): Nachbrenner und Enterkommando verbrauchen. Ergebnis: was wirkt. */
    public function flugMittel(int $secId, bool $nachbrenner, bool $enter): array
    {
        $status = $this->getStatus($secId);
        $erg = ['nachbrenner' => false, 'enter' => false];
        if ($nachbrenner && $status['km_nachbrenner'] > 0) {
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET km_nachbrenner=km_nachbrenner-1 WHERE sec_id=? AND km_nachbrenner>0", [$secId]);
            $erg['nachbrenner'] = true;
        }
        if ($enter && $status['km_enter'] > 0) {
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET km_enter=km_enter-1, enter_aktiv=1 WHERE sec_id=? AND km_enter>0", [$secId]);
            $erg['enter'] = true;
        }
        return $erg;
    }

    public function enterZuruecksetzen(int $secId): void
    {
        try {
            mysqli_execute_query($this->db, "UPDATE de_sector_vs SET enter_aktiv=0 WHERE sec_id=?", [$secId]);
        } catch (\Throwable $e) {
        }
    }

    /** Sektorkampf: Gitterstufe, Gitterprozent und ob ein Schild scharf ist (verteidigter Sektor); $kt ist der laufende Kampftick */
    public function verteidigung(int $secId, int $kt): array
    {
        $s = $this->getStatus($secId);
        return ['gitter' => $s['gitter'], 'prozent' => self::PROZENT[self::GITTER][$s['gitter']], 'schild' => $s['schild_bis_kt'] >= $kt];
    }

    public function schildVerbrauchen(int $secId): void
    {
        mysqli_execute_query($this->db, "UPDATE de_sector_vs SET schild_bis_kt=0 WHERE sec_id=?", [$secId]);
    }

    /** Sektorkampf: Angreifer mit scharfem Enterkommando; es ist damit verbraucht */
    public function enterAngreifer(array $secIds): array
    {
        $treffer = [];
        foreach ($secIds as $id) {
            if ($this->getStatus((int)$id)['enter_aktiv'] > 0) {
                $treffer[] = (int)$id;
                $this->enterZuruecksetzen((int)$id);
            }
        }
        return $treffer;
    }

    /** Scannerphalanx: läuft im Sektor ein Störsender? $wt ist der zuletzt gelaufene Wirtschaftstick */
    public function istGestoert(int $secId, int $wt): bool
    {
        return $this->getStatus($secId)['stoersender_bis_wt'] > $wt;
    }

    //////////////////////////////////////////////////////////////
    // Wirkung der Projekte
    //////////////////////////////////////////////////////////////

    /** Stufen und Prozente aus einer Statuszeile */
    public static function boni(array $status): array
    {
        return [
            'relais' => $status['relais'], 'raffinerie' => $status['raffinerie'], 'werft' => $status['werft'], 'gitter' => $status['gitter'],
            'bauzeit' => self::PROZENT[self::RELAIS][$status['relais']],
            'industrie' => self::PROZENT[self::RAFFINERIE][$status['raffinerie']],
            'schiffe' => self::PROZENT[self::WERFT][$status['werft']],
        ];
    }

    /** Boni eines Spielers aus den Projekten seines Sektors (vs_bonus_info, map_mobile.php) */
    public function getBoniFuerSpieler(int $uid): array
    {
        $row = mysqli_fetch_assoc(mysqli_execute_query($this->db, "SELECT sector FROM de_user_data WHERE user_id=?", [$uid]));
        return self::boni($this->getStatus((int)($row['sector'] ?? 0)));
    }
}
