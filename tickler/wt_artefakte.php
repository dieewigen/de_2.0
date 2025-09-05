<?php
echo 'Sektorartefakte berechnen:<br>';

// kleine Helper
function fetchRowAssoc($sql, $params = []) {
    $res = mysqli_execute_query($GLOBALS['dbi'], $sql, $params);
    if (!$res) return null;
    $row = mysqli_fetch_assoc($res);
    return $row ?: null;
}
function safeSplitEkey($ekey) {
    $hv = explode(";", (string)$ekey);
    while (count($hv) < 4) { $hv[] = 0; }
    return array_map('floatval', array_slice($hv, 0, 4));
}
function fetchArtefaktSectorById($id) {
    $row = fetchRowAssoc("SELECT sector FROM de_artefakt WHERE id=?", [$id]);
    if (!$row || $row['sector'] === null) return null;
    return (int)$row['sector'];
}

//größter tick
$row = fetchRowAssoc("SELECT wt AS tick FROM de_system LIMIT 1", []);
$maxtick = $row ? (int)$row['tick'] : 0;

// ===== ID=1 Die Schale von Sabrulia =====
$artsec = fetchArtefaktSectorById(1);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid  = (int)$row["user_id"];
        $col  = (float)$row["col"];
        $ekey = $row["ekey"];
        $pt   = loadPlayerTechs($uid);

        [$keym, $keyd, $keyi, $keye] = safeSplitEkey($ekey);

        // 5% Energie fürs Artefakt
        $ea = $col * $sv_artefakt[0][0];

        // Energieinput pro Rohstoff
        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        // Energie -> Materie Verhältnis
        $emvm = hasTech($pt, 18) ? 1 : 2;
        $emvd = hasTech($pt, 19) ? 2 : 4;
        $emvi = hasTech($pt, 20) ? 3 : 6;
        $emve = hasTech($pt, 21) ? 4 : 8;

        // Output
        $rm = (int)($em / $emvm);
        $rd = (int)($ed / $emvd);
        $ri = (int)($ei / $emvi);
        $re = (int)($ee / $emve);

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data
                SET restyp01 = restyp01 + ?,
                    restyp02 = restyp02 + ?,
                    restyp03 = restyp03 + ?,
                    restyp04 = restyp04 + ?
              WHERE user_id=?",
            [$rm, $rd, $ri, $re, $uid]
        );
    }
}

// ===== ID=2 Der Spiegel von Calderan =====
$artsec = fetchArtefaktSectorById(2);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.col>0
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid = (int)$row["user_id"];
        $r = rand(1, 100);
        if ($r <= $sv_artefakt[1][5]) {
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col = col + 1 WHERE user_id=?", [$uid]);
            $time = date("YmdHis");
            $nachricht = $wt_lang['kollektorspiegelcalderan'];
            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $nachricht]);
        }
    }
}

// ===== ID=3 Der Spiegel von Coltassa =====
$artsec = fetchArtefaktSectorById(3);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.col>0
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid = (int)$row["user_id"];
        $r = rand(1, 100);
        if ($r <= $sv_artefakt[2][5]) {
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col = col + 1 WHERE user_id=?", [$uid]);
            $time = date("YmdHis");
            $nachricht = $wt_lang['kollektorspiegelcoltassa'];
            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $nachricht]);
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uid]);
        }
    }
}

// ===== ID=4 Die Schale von Kesh-Ha =====
$artsec = fetchArtefaktSectorById(4);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid  = (int)$row["user_id"];
        $col  = (float)$row["col"];
        $ekey = $row["ekey"];
        $pt   = loadPlayerTechs($uid);

        [$keym, $keyd, $keyi, $keye] = safeSplitEkey($ekey);

        // 3% Energie fürs Artefakt
        $ea = $col * $sv_artefakt[3][0];

        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        $emvm = hasTech($pt, 18) ? 1 : 2;
        $emvd = hasTech($pt, 19) ? 2 : 4;
        $emvi = hasTech($pt, 20) ? 3 : 6;
        $emve = hasTech($pt, 21) ? 4 : 8;

        $rm = (int)($em / $emvm);
        $rd = (int)($ed / $emvd);
        $ri = (int)($ei / $emvi);
        $re = (int)($ee / $emve);

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data
                SET restyp01 = restyp01 + ?,
                    restyp02 = restyp02 + ?,
                    restyp03 = restyp03 + ?,
                    restyp04 = restyp04 + ?
              WHERE user_id=?",
            [$rm, $rd, $ri, $re, $uid]
        );
    }
}

// ===== ID=5 Die Schale von Kesh-Na =====
$artsec = fetchArtefaktSectorById(5);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid  = (int)$row["user_id"];
        $col  = (float)$row["col"];
        $ekey = $row["ekey"];
        $pt   = loadPlayerTechs($uid);

        [$keym, $keyd, $keyi, $keye] = safeSplitEkey($ekey);

        $ea = $col * $sv_artefakt[4][0];

        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        $emvm = hasTech($pt, 18) ? 1 : 2;
        $emvd = hasTech($pt, 19) ? 2 : 4;
        $emvi = hasTech($pt, 20) ? 3 : 6;
        $emve = hasTech($pt, 21) ? 4 : 8;

        $rm = (int)($em / $emvm);
        $rd = (int)($ed / $emvd);
        $ri = (int)($ei / $emvi);
        $re = (int)($ee / $emve);

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data
                SET restyp01 = restyp01 + ?,
                    restyp02 = restyp02 + ?,
                    restyp03 = restyp03 + ?,
                    restyp04 = restyp04 + ?
              WHERE user_id=?",
            [$rm, $rd, $ri, $re, $uid]
        );
    }
}

// ===== ID=6 Die Schale von Kesh-Za =====
$artsec = fetchArtefaktSectorById(6);
if ($artsec !== null) {
    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid  = (int)$row["user_id"];
        $col  = (float)$row["col"];
        $ekey = $row["ekey"];
        $pt   = loadPlayerTechs($uid);

        [$keym, $keyd, $keyi, $keye] = safeSplitEkey($ekey);

        $ea = $col * $sv_artefakt[5][0];

        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        $emvm = hasTech($pt, 18) ? 1 : 2;
        $emvd = hasTech($pt, 19) ? 2 : 4;
        $emvi = hasTech($pt, 20) ? 3 : 6;
        $emve = hasTech($pt, 21) ? 4 : 8;

        $rm = (int)($em / $emvm);
        $rd = (int)($ed / $emvd);
        $ri = (int)($ei / $emvi);
        $re = (int)($ee / $emve);

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data
                SET restyp01 = restyp01 + ?,
                    restyp02 = restyp02 + ?,
                    restyp03 = restyp03 + ?,
                    restyp04 = restyp04 + ?
              WHERE user_id=?",
            [$rm, $rd, $ri, $re, $uid]
        );
    }
}

// ===== ID=7–10 Ströme =====
foreach ([7,8,9,10] as $id) {
    $colIdx = $id - 6; // 1..4
    $artsec = fetchArtefaktSectorById($id);
    if ($artsec === null) continue;

    // Mapping: 7->restyp01, 8->restyp02, 9->restyp03, 10->restyp04
    $restypCol = ['','restyp01','restyp02','restyp03','restyp04'][$colIdx];
    $wertIdx   = $id - 1; // 6..9 im $sv_artefakt
    $wert      = $sv_artefakt[$wertIdx][$colIdx] ?? 0;

    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND ".($id===7 || $id===8 ? "de_user_data.col>0 AND " : "")."de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid = (int)$row["user_id"];
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET $restypCol = $restypCol + ? WHERE user_id=?",
            [$wert, $uid]
        );
    }
}

// ===== ID=11–20 Die Gabe der Reichen =====
for ($k = 11; $k <= 20; $k++) {
    $artsec = fetchArtefaktSectorById($k);
    if ($artsec === null) continue;

    $res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey
           FROM de_user_data, de_login
          WHERE de_login.status=1
            AND de_login.user_id = de_user_data.user_id
            AND de_user_data.sector=?",
        [$artsec]
    );
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $uid  = (int)$row["user_id"];
        $col  = (float)$row["col"];
        $ekey = $row["ekey"];
        $pt   = loadPlayerTechs($uid);

        [$keym, $keyd, $keyi, $keye] = safeSplitEkey($ekey);

        // Bonusressourcen durch Gabe
        $ea = $col * $sv_kollieertrag / 100 * $sv_artefakt[$k - 1][0];

        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        $emvm = hasTech($pt, 18) ? 1 : 2;
        $emvd = hasTech($pt, 19) ? 2 : 4;
        // FIX: hier war vermutlich 10 gemeint, korrekt ist 20
        $emvi = hasTech($pt, 20) ? 3 : 6;
        $emve = hasTech($pt, 21) ? 4 : 8;

        $rm = (int)($em / $emvm);
        $rd = (int)($ed / $emvd);
        $ri = (int)($ei / $emvi);
        $re = (int)($ee / $emve);

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data
                SET restyp01 = restyp01 + ?,
                    restyp02 = restyp02 + ?,
                    restyp03 = restyp03 + ?,
                    restyp04 = restyp04 + ?
              WHERE user_id=?",
            [$rm, $rd, $ri, $re, $uid]
        );
    }
}

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//  Artefakte springen zufällig zwischen Sektoren
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

// Artefakt- & Sektoranzahlen
$row = fetchRowAssoc("SELECT COUNT(id) AS anzahl FROM de_artefakt WHERE id<11 OR id>20", []);
$artefaktanzahl = $row ? (int)$row["anzahl"] : 0;

$row = fetchRowAssoc("SELECT COUNT(id) AS anzahl FROM de_artefakt WHERE (id<11 OR id>20) AND sector > 1", []);
$artefaktanzahl_verteilt = $row ? (int)$row["anzahl"] : 0;

$row = fetchRowAssoc("SELECT COUNT(sec_id) AS anzahl FROM de_sector WHERE techs LIKE 's1%' AND npc=0", []);
$srbanzahl = $row ? (int)$row["anzahl"] : 0;

$time = date("YmdHis");

// Artefakte-Sektorzusammenfassung
$res = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT sector, COUNT(id) AS artanz, SUM(wm) AS wm
       FROM de_artefakt
      WHERE id<11 OR id>20
   GROUP BY sector",
    []
);
while ($res && ($row = mysqli_fetch_assoc($res))) {
    $artanzahl = isset($row["artanz"]) ? (int)$row["artanz"] : 0;
    $wm        = isset($row["wm"]) ? (int)$row["wm"] : 0;

    // Wahrscheinlichkeit bestimmen
    if ($wm === 0) $wm = 1;

    if ($artanzahl > 1) {
        $wm += 1000; // pro Sektor nicht >1 Sektorartefakt
    }

    if ((int)$row["sector"] === -1) {
        // aus Wartepool nur ins Spiel, wenn genug Raumbasen vorhanden
        $wm = 0;
        if (floor($srbanzahl / 2) > $artefaktanzahl_verteilt && $artefaktanzahl > 0) {
            $wm += 1000;
        }
        $artefaktanzahl_verteilt++;
    }

    if ((int)$row["sector"] === -2) {
        $wm = 0; // -2 springt nicht
    }

    $w = sqrt(max($artanzahl, 0)) * 535 * $wm;
    $r = rand(1, 1000000);
    echo 'W: ' . $w . '<br>';
    echo 'R: ' . $r . '<br>';

    if ($r < $w) {
        $artsec = (int)$row["sector"]; // aktueller Sektor

        // IDs für News-Logging initialisieren
        $uidh = 0; // Herkunfts-SK
        $uidz = 0; // Ziel-SK

        // neuen Sektor robust auslesen
        $rowx = fetchRowAssoc(
            "SELECT sec_id, bk
               FROM de_sector
              WHERE techs LIKE 's1%'
                AND npc = 0
                AND sec_id > 1
                AND (? IS NULL OR sec_id <> ?)
           ORDER BY RAND()
              LIMIT 1",
            [$artsec, $artsec]
        );

        if (!$rowx || empty($rowx['sec_id'])) {
            // Kein Zielsektor gefunden -> Wartepool -1 (oder continue;)
            $zielsec = -1;
        } else {
            $zielsec = (int)$rowx["sec_id"];
        }

        // BK des Zielsektors
        $zielbk = (int)getSKSystemBySecID($zielsec);

        // Ziel-BK benachrichtigen (falls vorhanden)
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?",
            [$zielsec, $zielbk]
        );
        $numbk = $db_daten ? mysqli_num_rows($db_daten) : 0;
        if ($numbk === 1) {
            $rowy = mysqli_fetch_assoc($db_daten);
            if ($rowy && !empty($rowy['user_id'])) {
                $uidz = (int)$rowy["user_id"];
                $nachricht = $wt_lang['artefaktkommt'];
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 60, ?, ?)", [$uidz, $time, $nachricht]);
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uidz]);
            }
        }

        // Herkunfts-SK benachrichtigen
        if ($artsec > 0) {
            $herbk = (int)getSKSystemBySecID($artsec);
            echo '<br>$herbk: ' . $herbk;

            if ($herbk > 0) {
                $db_daten = mysqli_execute_query(
                    $GLOBALS['dbi'],
                    "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?",
                    [$artsec, $herbk]
                );
                $numbk = $db_daten ? mysqli_num_rows($db_daten) : 0;
                echo '<br>$numbk: ' . $numbk;

                if ($numbk === 1) {
                    $rowy = mysqli_fetch_assoc($db_daten);
                    if ($rowy && !empty($rowy['user_id'])) {
                        $uidh = (int)$rowy["user_id"];
                        $nachricht = $wt_lang['artefaktgeht'];
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 60, ?, ?)", [$uidh, $time, $nachricht]);
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uidh]);
                    }
                }
            }
        }

        // springendes Artefakt im Herkunftssektor auswählen
        $rowx = fetchRowAssoc(
            "SELECT id
               FROM de_artefakt
              WHERE sector=?
                AND (id<11 OR id>20)
           ORDER BY RAND()
              LIMIT 1",
            [$artsec]
        );
        if (!$rowx || empty($rowx['id'])) {
            // Nichts zu verschieben
            continue;
        }
        $artid = (int)$rowx["id"];

        // Sicherheitsgurt: Ziel darf nicht NULL sein
        if ($zielsec === null) {
            continue;
        }

        // Artefakt verschieben
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_artefakt SET sector=? WHERE id=?", [$zielsec, $artid]);

        echo 'Artefakttransfer: A-ID: ' . $artid . ', Herkunftssektor: ' . $artsec . ', Zielsektor: ' . $zielsec .
            ', News SK (Herkunft): ' . $uidh . ', News SK (Ziel): ' . $uidz . '<br>';

        // Umzug in Servernews loggen (typ: 0 = hypersturm)
        $text = $artid . ';' . $artsec . ';' . $zielsec;
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_server(wt, typ, text) VALUES (?, '0', ?)", [$maxtick, $text]);
        echo "INSERT INTO de_news_server(wt, typ, text) VALUES ('$maxtick', '0', '$text');";
    }
}

// Artefakte 11–20 in kleinste PC-Sektoren verteilen (nach Bedingungen)
if ($maxtick >= 2000 && $srbanzahl >= 11) {
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT sec_id
           FROM de_sector
          WHERE npc=0
            AND sec_id > 1
            AND platz > 0
       ORDER BY tempcol ASC
          LIMIT 5",
        []
    );

    // result kann weniger als 5 Reihen haben -> defensiv lesen
    $zielsektoren = [];
    while ($result && ($row = mysqli_fetch_assoc($result))) {
        $zielsektoren[] = (int)$row['sec_id'];
    }
    $i = 0;
    for ($id = 16; $id <= 20; $id++) {
        $zielsec = isset($zielsektoren[$i]) ? $zielsektoren[$i] : -1;
        $i++;
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_artefakt SET sector=? WHERE id=?", [$zielsec, $id]);
    }
}

//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// arthold hinterlegen: Zeit, in der Sektoren ein Sektorartefakt haben
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector>0", []);
while ($db_daten && ($row = mysqli_fetch_assoc($db_daten))) {
    $sector = (int)$row["sector"];
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET arthold = arthold + 1 WHERE sec_id=?", [$sector]);
}
echo '<hr>';
?>
