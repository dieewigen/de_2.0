<?php
namespace DieEwigen\DE2\Model\Battleground;

use DieEwigen\DE2\Model\SpecialSystem\Storage;

/**
 * Die Werkstatt des Basissterns (Regeln: docs/werkstatt.md): Ausrüstung für die Battlegrounds schmieden, anlegen,
 * zerlegen und veredeln, dazu das ausbaubare Lager.
 *
 * Die Stücke liegen in de_user_bs_item (werte als JSON: Kennzahl => [Wert, Veredelungen]), Erfahrung, Würfe,
 * Erbauerstaub und Lagerstufe im serialisierten Basisstern (lib/special_ship.class.php, wie die Haltung).
 * Bedient wird die Werkstatt in specialship.php, die Wirkung rechnet tickler/kt_manage_bg.php vor jedem Kampf aus
 * (kampfwerte bzw. kampfwerteAllianz), View\Kampfbericht zeigt die Güten je Platz. Alle Zahlen sind Konstanten.
 */
class Werkstatt
{
    public const PANZERUNG = 1;
    public const SCHILDKERN = 2;
    public const WAFFENPHALANX = 3;
    public const ZIELRECHNER = 4;
    public const PLAETZE = [self::PANZERUNG, self::SCHILDKERN, self::WAFFENPHALANX, self::ZIELRECHNER];
    public const HAUPTWERT = [self::PANZERUNG => 'huelle', self::SCHILDKERN => 'schild', self::WAFFENPHALANX => 'schaden', self::ZIELRECHNER => 'krit'];
    //Kennzahlen: huelle, schild, schaden, daempfung in Prozent, krit, treffer, vorteil, schutz in Punkten
    public const KENNZAHLEN = ['huelle', 'schild', 'schaden', 'krit', 'treffer', 'daempfung', 'vorteil', 'schutz'];
    public const PROZENT = ['huelle', 'schild', 'schaden', 'daempfung'];

    //Rezepte: Kosten (item_id => Menge) und Erfahrung je Wurf
    public const REZEPT = [
        self::PANZERUNG => ['kosten' => [3 => 3000, 4 => 1500, 14 => 5], 'xp' => 1],
        self::SCHILDKERN => ['kosten' => [5 => 3000, 6 => 1500, 15 => 5], 'xp' => 2],
        self::WAFFENPHALANX => ['kosten' => [7 => 3000, 8 => 1500, 16 => 5], 'xp' => 3],
        self::ZIELRECHNER => ['kosten' => [9 => 3000, 10 => 1500, 17 => 5], 'xp' => 4],
    ];
    //Erfahrung, ab der ein Rang erreicht ist
    public const RANG_XP = [1 => 0, 2 => 10, 3 => 30, 4 => 60, 5 => 100, 6 => 160, 7 => 240, 8 => 340, 9 => 460, 10 => 600];
    public const MAX_RANG = 10;
    //ab diesem Rang steigt die Untergrenze jeder Spanne um so viele Punkte
    public const RANG_BONUS = [4 => 1, 7 => 2, 10 => 3];

    //Güten (Namen und Farben aus der alten erweiterten Archäologie)
    public const MASSENWARE = 0;
    public const GUT = 1;
    public const SELTEN = 2;
    public const MYSTISCH = 3;
    public const ARTEFAKT = 4;
    public const NEBENWERTE = [0, 1, 2, 3, 4];
    public const STAUB_ZERLEGEN = [1, 3, 10, 30, 100];
    public const STAUB_VEREDELN = [2, 4, 8, 16, 32];
    //Chancen je Rang in Prozent: Massenware, Gute Ware, Seltene Ware, Mystische Ware, Artefakt
    public const CHANCEN = [
        1 => [70, 25, 5, 0, 0],
        2 => [62, 29, 8, 1, 0],
        3 => [55, 31, 11, 3, 0],
        4 => [48, 33, 14, 4, 1],
        5 => [42, 34, 17, 6, 1],
        6 => [36, 35, 20, 7, 2],
        7 => [31, 35, 22, 9, 3],
        8 => [26, 35, 24, 11, 4],
        9 => [22, 34, 26, 13, 5],
        10 => [18, 33, 28, 15, 6],
    ];
    //jeder zehnte Wurf mindestens Gute Ware, jeder fünfzigste mindestens Seltene Ware
    public const PITY_GUT = 10;
    public const PITY_SELTEN = 50;

    //Spannen je Güte [min, max]: Hauptwert in Prozent (Hülle, Schild, Schaden) bzw. Punkten (Kritische Treffer)
    public const HAUPT_PROZENT = [[4, 7], [6, 10], [9, 13], [12, 16], [15, 20]];
    public const HAUPT_KRIT = [[2, 3], [3, 5], [5, 7], [7, 9], [9, 12]];
    //Nebenwerte je Güte
    public const NEBEN = [
        'huelle' => [[2, 3], [3, 5], [4, 6], [5, 8], [6, 10]],
        'schild' => [[2, 3], [3, 5], [4, 6], [5, 8], [6, 10]],
        'schaden' => [[2, 3], [3, 5], [4, 6], [5, 8], [6, 10]],
        'krit' => [[1, 1], [1, 2], [2, 3], [3, 4], [4, 6]],
        'treffer' => [[1, 1], [1, 2], [2, 3], [3, 4], [4, 5]],
        'daempfung' => [[1, 1], [1, 2], [2, 3], [3, 5], [4, 7]],
        'vorteil' => [[5, 5], [5, 10], [8, 15], [12, 20], [15, 30]],
        'schutz' => [[5, 5], [5, 10], [8, 15], [12, 20], [15, 33]],
    ];
    //Deckel über alle angelegten Stücke
    public const DECKEL = ['huelle' => 40, 'schild' => 40, 'schaden' => 40, 'krit' => 30, 'treffer' => 15, 'daempfung' => 30, 'vorteil' => 50, 'schutz' => 33];

    //Veredeln: Faktor je Update, Updates je Rang und Eigenschaft, Staub ab dem 11. bzw. 21. Update doppelt bzw. vierfach
    public const VEREDELN_FAKTOR = 1.0065;
    public const VEREDELN_JE_RANG = 3;
    public const VEREDELN_TEURER = [10 => 2, 20 => 4];

    //Lager: Startplätze, Plätze je Ausbau, linear bis Schritt 8, danach × 1,5 je Schritt; Grundkosten je Schrittbereich (0 = offen)
    public const LAGER_START = 6;
    public const LAGER_SCHRITT = 2;
    public const LAGER_LINEAR_BIS = 8;
    public const LAGER_FAKTOR = 1.5;
    public const LAGER_KOSTEN = [
        [3, [3 => 5000, 4 => 2500, 14 => 5]],
        [6, [5 => 5000, 6 => 2500, 15 => 5]],
        [8, [7 => 5000, 8 => 2500, 16 => 5]],
        [10, [9 => 5000, 10 => 2500, 17 => 5]],
        [0, [11 => 5000, 12 => 2500, 18 => 5]],
    ];
    //Zerlegen gibt diesen Anteil der Rohstoffe des Rezepts zurück (keine Handelswaren)
    public const ZERLEGEN_ANTEIL = 0.1;

    //Grundwerte des Kampfes, auf die die Kennzahlen wirken (tickler/kt_manage_bg.php)
    public const KAMPF_TREFFER = 80;
    public const KAMPF_TREFFER_MAX = 95;
    public const KAMPF_KRIT = 10;

    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    //////////////////////////////////////////////////////////////
    // Rang und Lager (Werte im Basisstern)
    //////////////////////////////////////////////////////////////

    public static function rang($ship): int
    {
        $xp = (int)($ship->erfahrung ?? 0);
        $rang = 1;
        foreach (self::RANG_XP as $r => $ab) {
            if ($xp >= $ab) {
                $rang = $r;
            }
        }
        return $rang;
    }

    /** Erfahrung bis zum nächsten Rang, 0 auf dem höchsten */
    public static function naechsterRangXp($ship): int
    {
        $rang = self::rang($ship);
        return $rang >= self::MAX_RANG ? 0 : self::RANG_XP[$rang + 1];
    }

    public static function lagerPlaetze($ship): int
    {
        return self::LAGER_START + self::LAGER_SCHRITT * (int)($ship->lager_stufe ?? 0);
    }

    /** Kosten des Ausbauschritts $schritt (ab 1), item_id => Menge */
    public static function lagerKosten(int $schritt): array
    {
        $schritt = max(1, $schritt);
        $grund = [];
        foreach (self::LAGER_KOSTEN as [$bis, $kosten]) {
            $grund = $kosten;
            if ($bis === 0 || $schritt <= $bis) {
                break;
            }
        }
        $faktor = min($schritt, self::LAGER_LINEAR_BIS);
        if ($schritt > self::LAGER_LINEAR_BIS) {
            $faktor *= pow(self::LAGER_FAKTOR, $schritt - self::LAGER_LINEAR_BIS);
        }
        $erg = [];
        foreach ($grund as $item => $menge) {
            $erg[$item] = (int)ceil($menge * $faktor);
        }
        return $erg;
    }

    //////////////////////////////////////////////////////////////
    // Würfeln
    //////////////////////////////////////////////////////////////

    /** Güte nach den Chancen des Rangs */
    public static function gueteWuerfeln(int $rang): int
    {
        $chancen = self::CHANCEN[max(1, min(self::MAX_RANG, $rang))];
        $wurf = mt_rand(1, 100);
        $summe = 0;
        foreach ($chancen as $guete => $prozent) {
            $summe += $prozent;
            if ($wurf <= $summe) {
                return $guete;
            }
        }
        return self::MASSENWARE;
    }

    private static function spanneWuerfeln(array $spanne, int $bonus): int
    {
        $min = min($spanne[1], $spanne[0] + $bonus);
        return mt_rand($min, $spanne[1]);
    }

    /**
     * Ein Stück für $platz würfeln: Güte nach Rang und Pechsträhnen-Regel, Hauptwert und Nebenwerte in ihren Spannen.
     * @return array{guete:int, werte:array} werte: Kennzahl => [Wert, Veredelungen]
     */
    public static function wuerfeln(int $rang, int $platz, int $wurfNr): array
    {
        $guete = self::gueteWuerfeln($rang);
        if ($wurfNr > 0 && $wurfNr % self::PITY_SELTEN === 0) {
            $guete = max($guete, self::SELTEN);
        } elseif ($wurfNr > 0 && $wurfNr % self::PITY_GUT === 0) {
            $guete = max($guete, self::GUT);
        }
        $bonus = 0;
        foreach (self::RANG_BONUS as $ab => $b) {
            if ($rang >= $ab) {
                $bonus = $b;
            }
        }
        $haupt = self::HAUPTWERT[$platz];
        $werte = [$haupt => [self::spanneWuerfeln($haupt === 'krit' ? self::HAUPT_KRIT[$guete] : self::HAUPT_PROZENT[$guete], $bonus), 0]];
        $kandidaten = array_values(array_diff(self::KENNZAHLEN, [$haupt]));
        shuffle($kandidaten);
        foreach (array_slice($kandidaten, 0, self::NEBENWERTE[$guete]) as $k) {
            $werte[$k] = [self::spanneWuerfeln(self::NEBEN[$k][$guete], $bonus), 0];
        }
        return ['guete' => $guete, 'werte' => $werte];
    }

    //////////////////////////////////////////////////////////////
    // Stücke
    //////////////////////////////////////////////////////////////

    private function decode(array $row): array
    {
        $werte = json_decode((string)$row['werte'], true);
        $row['id'] = (int)$row['id'];
        $row['user_id'] = (int)$row['user_id'];
        $row['platz'] = (int)$row['platz'];
        $row['guete'] = (int)$row['guete'];
        $row['angelegt'] = (int)$row['angelegt'];
        $row['werte'] = is_array($werte) ? $werte : [];
        return $row;
    }

    /** Alle Stücke des Spielers: angelegte zuerst, dann nach Platz, Güte absteigend */
    public function items(int $uid): array
    {
        $liste = [];
        try {
            $res = mysqli_execute_query($this->db, "SELECT * FROM de_user_bs_item WHERE user_id=? ORDER BY angelegt DESC, platz, guete DESC, id", [$uid]);
            while ($row = mysqli_fetch_assoc($res)) {
                $liste[] = $this->decode($row);
            }
        } catch (\Throwable $e) {
        }
        return $liste;
    }

    public function item(int $uid, int $id): ?array
    {
        $res = mysqli_execute_query($this->db, "SELECT * FROM de_user_bs_item WHERE id=? AND user_id=?", [$id, $uid]);
        $row = mysqli_fetch_assoc($res);
        return $row ? $this->decode($row) : null;
    }

    /** Angelegte Stücke, platz => Stück */
    public function angelegt(int $uid): array
    {
        $erg = [];
        foreach ($this->items($uid) as $it) {
            if ($it['angelegt']) {
                $erg[$it['platz']] = $it;
            }
        }
        return $erg;
    }

    /** Stücke im Lager (nicht angelegt) */
    public function lagerBelegt(int $uid): int
    {
        try {
            $row = mysqli_fetch_assoc(mysqli_execute_query($this->db, "SELECT COUNT(*) AS c FROM de_user_bs_item WHERE user_id=? AND angelegt=0", [$uid]));
            return (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function wt(): int
    {
        $row = mysqli_fetch_assoc(mysqli_execute_query($this->db, "SELECT wt FROM de_system LIMIT 1", []));
        return (int)($row['wt'] ?? 0);
    }

    /** Kosten abbuchen, bei Mangel alles zurück (wie Hekate) */
    private function abbuchen(int $uid, array $kosten): bool
    {
        $genommen = [];
        foreach ($kosten as $item => $menge) {
            if (!Storage::take($this->db, $uid, (int)$item, (int)$menge)) {
                foreach ($genommen as [$zItem, $zMenge]) {
                    Storage::give($uid, $zItem, $zMenge);
                }
                return false;
            }
            $genommen[] = [(int)$item, (int)$menge];
        }
        return true;
    }

    /**
     * Ein Stück schmieden. Der Aufrufer hält die Spielersperre und speichert danach den Basisstern.
     * @return array{ok:bool, fehler:string, item:?array, rang_neu:bool}
     */
    public function schmieden(int $uid, $ship, int $platz): array
    {
        $erg = ['ok' => false, 'fehler' => '', 'item' => null, 'rang_neu' => false];
        if (!isset(self::REZEPT[$platz])) {
            $erg['fehler'] = 'unbekannt';
            return $erg;
        }
        if ($this->lagerBelegt($uid) >= self::lagerPlaetze($ship)) {
            $erg['fehler'] = 'lager_voll';
            return $erg;
        }
        if (!$this->abbuchen($uid, self::REZEPT[$platz]['kosten'])) {
            $erg['fehler'] = 'rohstoffe';
            return $erg;
        }
        $rangVorher = self::rang($ship);
        $ship->wuerfe = (int)($ship->wuerfe ?? 0) + 1;
        $wurf = self::wuerfeln($rangVorher, $platz, (int)$ship->wuerfe);
        $ship->erfahrung = (int)($ship->erfahrung ?? 0) + self::REZEPT[$platz]['xp'];
        mysqli_execute_query($this->db, "INSERT INTO de_user_bs_item (user_id, platz, guete, werte, angelegt, wt) VALUES (?, ?, ?, ?, 0, ?)",
            [$uid, $platz, $wurf['guete'], json_encode($wurf['werte']), $this->wt()]);
        $erg['ok'] = true;
        $erg['item'] = $this->item($uid, (int)mysqli_insert_id($this->db));
        $erg['rang_neu'] = self::rang($ship) > $rangVorher;
        return $erg;
    }

    /** Stück anlegen; ein zuvor angelegtes Stück desselben Platzes geht ins Lager. Fehlerschlüssel oder ''. */
    public function anlegen(int $uid, int $id): string
    {
        $it = $this->item($uid, $id);
        if ($it === null) {
            return 'stueck';
        }
        mysqli_execute_query($this->db, "UPDATE de_user_bs_item SET angelegt=0 WHERE user_id=? AND platz=?", [$uid, $it['platz']]);
        mysqli_execute_query($this->db, "UPDATE de_user_bs_item SET angelegt=1 WHERE id=? AND user_id=?", [$id, $uid]);
        return '';
    }

    public function ablegen(int $uid, int $id): string
    {
        if ($this->item($uid, $id) === null) {
            return 'stueck';
        }
        mysqli_execute_query($this->db, "UPDATE de_user_bs_item SET angelegt=0 WHERE id=? AND user_id=?", [$id, $uid]);
        return '';
    }

    /**
     * Stück zerlegen: Staub nach Güte, ein Zehntel der Rohstoffe des Rezepts zurück (keine Handelswaren).
     * Angelegte Stücke müssen zuerst abgelegt werden (wie Artefakte in Flotten).
     * @return array{fehler:string, staub:int, rueck:array}
     */
    public function zerlegen(int $uid, $ship, int $id): array
    {
        $it = $this->item($uid, $id);
        if ($it === null) {
            return ['fehler' => 'stueck', 'staub' => 0, 'rueck' => []];
        }
        if ($it['angelegt']) {
            return ['fehler' => 'angelegt', 'staub' => 0, 'rueck' => []];
        }
        mysqli_execute_query($this->db, "DELETE FROM de_user_bs_item WHERE id=? AND user_id=?", [$id, $uid]);
        $staub = self::STAUB_ZERLEGEN[$it['guete']] ?? 1;
        $ship->staub = (int)($ship->staub ?? 0) + $staub;
        $rueck = [];
        foreach (self::REZEPT[$it['platz']]['kosten'] ?? [] as $item => $menge) {
            if ($item >= 3 && $item <= 12) {
                $r = (int)floor($menge * self::ZERLEGEN_ANTEIL);
                if ($r > 0) {
                    Storage::give($uid, (int)$item, $r);
                    $rueck[$item] = $r;
                }
            }
        }
        return ['fehler' => '', 'staub' => $staub, 'rueck' => $rueck];
    }

    /** Staubkosten der nächsten Veredelung einer Eigenschaft mit bisher $v Veredelungen */
    public static function veredelnKosten(int $guete, int $v): int
    {
        $faktor = 1;
        foreach (self::VEREDELN_TEURER as $ab => $f) {
            if ($v >= $ab) {
                $faktor = $f;
            }
        }
        return (self::STAUB_VEREDELN[$guete] ?? 2) * $faktor;
    }

    public static function veredelnMax($ship): int
    {
        return self::VEREDELN_JE_RANG * self::rang($ship);
    }

    /** Eine Eigenschaft um VEREDELN_FAKTOR verbessern. Fehlerschlüssel oder ''. */
    public function veredeln(int $uid, $ship, int $id, string $kennzahl): string
    {
        $it = $this->item($uid, $id);
        if ($it === null) {
            return 'stueck';
        }
        if (!isset($it['werte'][$kennzahl])) {
            return 'kennzahl';
        }
        $v = (int)$it['werte'][$kennzahl][1];
        if ($v >= self::veredelnMax($ship)) {
            return 'veredelt_max';
        }
        $kosten = self::veredelnKosten($it['guete'], $v);
        if ((int)($ship->staub ?? 0) < $kosten) {
            return 'staub';
        }
        $ship->staub = (int)$ship->staub - $kosten;
        $it['werte'][$kennzahl][1] = $v + 1;
        mysqli_execute_query($this->db, "UPDATE de_user_bs_item SET werte=? WHERE id=? AND user_id=?", [json_encode($it['werte']), $id, $uid]);
        return '';
    }

    /** Lager um einen Schritt ausbauen. Fehlerschlüssel oder ''. */
    public function lagerAusbauen(int $uid, $ship): string
    {
        $schritt = (int)($ship->lager_stufe ?? 0) + 1;
        if (!$this->abbuchen($uid, self::lagerKosten($schritt))) {
            return 'rohstoffe';
        }
        $ship->lager_stufe = $schritt;
        return '';
    }

    //////////////////////////////////////////////////////////////
    // Wirkung
    //////////////////////////////////////////////////////////////

    /** Wirksamer Wert einer Eigenschaft nach Veredelung */
    public static function wert(array $eintrag): float
    {
        return (float)$eintrag[0] * pow(self::VEREDELN_FAKTOR, (int)($eintrag[1] ?? 0));
    }

    public static function leereSumme(): array
    {
        return array_fill_keys(self::KENNZAHLEN, 0.0);
    }

    /** Summe der Kennzahlen über die übergebenen Stücke, je Kennzahl gedeckelt */
    public static function summe(array $stuecke): array
    {
        $summe = self::leereSumme();
        foreach ($stuecke as $it) {
            foreach ($it['werte'] ?? [] as $k => $eintrag) {
                if (isset($summe[$k])) {
                    $summe[$k] += self::wert($eintrag);
                }
            }
        }
        foreach ($summe as $k => $w) {
            $summe[$k] = min((float)self::DECKEL[$k], $w);
        }
        return $summe;
    }

    /** Ausrüstung eines Spielers im Kampf: Summe und die angelegten Stücke je Platz */
    public function kampfwerte(int $uid): array
    {
        $stuecke = $this->angelegt($uid);
        return ['summe' => self::summe($stuecke), 'stuecke' => $stuecke];
    }

    /**
     * Ausrüstung einer Allianz im Kampf: je Platz das angelegte Stück höchster Güte unter allen Mitgliedern,
     * bei Gleichstand per Zufall.
     */
    public function kampfwerteAllianz(array $uids): array
    {
        $uids = array_values(array_unique(array_map('intval', $uids)));
        $beste = [];
        if (count($uids) > 0) {
            try {
                $res = mysqli_execute_query($this->db, "SELECT * FROM de_user_bs_item WHERE angelegt=1 AND user_id IN (".implode(',', array_fill(0, count($uids), '?')).")", $uids);
                while ($row = mysqli_fetch_assoc($res)) {
                    $it = $this->decode($row);
                    $p = $it['platz'];
                    if (!isset($beste[$p]) || $it['guete'] > $beste[$p][0]['guete']) {
                        $beste[$p] = [$it];
                    } elseif ($it['guete'] == $beste[$p][0]['guete']) {
                        $beste[$p][] = $it;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        $stuecke = [];
        foreach ($beste as $p => $liste) {
            $stuecke[$p] = $liste[array_rand($liste)];
        }
        ksort($stuecke);
        return ['summe' => self::summe($stuecke), 'stuecke' => $stuecke];
    }

    /**
     * Schadensfaktor der Haltung mit Haltungsvorteil und Haltungsschutz: Vorteil hebt ×1,5 an,
     * Schutz hebt ×⅔ an, höchstens auf ×1.
     */
    public static function haltungsfaktor(int $eigene, int $gegner, array $summe): float
    {
        $vorteil = Haltung::vorteil($eigene, $gegner);
        if ($vorteil > 0) {
            return Haltung::SCHADEN_VORTEIL + ($summe['vorteil'] ?? 0) / 100;
        }
        if ($vorteil < 0) {
            return min(1.0, Haltung::SCHADEN_NACHTEIL + ($summe['schutz'] ?? 0) / 100);
        }
        return 1.0;
    }

    //////////////////////////////////////////////////////////////
    // Darstellung
    //////////////////////////////////////////////////////////////

    /** Wert mit Einheit, z. B. "+11 %" oder "+14 Pkt.", veredelte Werte mit einer Nachkommastelle */
    public static function wertText(string $k, array $eintrag): string
    {
        $w = self::wert($eintrag);
        $zahl = ((int)($eintrag[1] ?? 0) > 0) ? number_format($w, 1, ',', '.') : number_format($w, 0, ',', '.');
        return '+'.$zahl.(in_array($k, self::PROZENT) ? ' %' : ' Pkt.');
    }

    /** Name eines Stücks, z. B. "Mystische Panzerung (+12 V)" */
    public static function name(array $it, array $lang): string
    {
        $v = 0;
        foreach ($it['werte'] as $eintrag) {
            $v += (int)($eintrag[1] ?? 0);
        }
        return $lang['stueck'][$it['guete']][$it['platz']].($v > 0 ? ' (+'.$v.' V)' : '');
    }

    /**
     * Vier Buchstaben für den Kampfbericht, je Platz in der Farbe der Güte, ohne Stück grau. Ein Tooltip für alle vier:
     * je Buchstabe Platz und Güte, darunter die Summe der belegten Kennzahlen. Die Buchstaben selbst tragen keinen title,
     * weil das Tooltip-Skript (setTooltip in js/de_fn.js) bei title-Elementen in title-Elementen nur eines von beiden zeigt.
     */
    public static function berichtChips(array $stuecke, array $lang, array $summe = []): string
    {
        $html = '';
        $zeilen = [];
        foreach (self::PLAETZE as $p) {
            $g = isset($stuecke[$p]) ? (int)$stuecke[$p]['guete'] : null;
            $html .= '<span class="ws-guete ws-guete-'.($g === null ? 'leer' : $g).'">'.$lang['platz_kurz'][$p].'</span>';
            $zeilen[] = $lang['platz'][$p].': '.($g === null ? 'keine' : $lang['guete'][$g]);
        }
        foreach ($summe as $k => $v) {
            if ($v > 0 && isset($lang['kennzahl'][$k])) {
                $zeilen[] = $lang['kennzahl'][$k].' '.self::wertText($k, [(float)$v, 0]);
            }
        }
        return '<span class="ws-chips" title="'.$lang['bericht_ausruestung'].'&'.implode('<br>', $zeilen).'">'.$html.'</span>';
    }
}
