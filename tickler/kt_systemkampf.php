<?php

include "kt_einheitendaten.php";

//anzahl der maximalen kollektoren eines spielers
$db_daten = mysql_query("SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0", $db);
$row = mysql_fetch_array($db_daten);
$maxcol = $row['maxcol'];
if ($maxcol == 0) {
    $maxcol = 1;
}

//maximale tickzeit auslesen
$db_daten = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data", $db);
$row = mysql_fetch_array($db_daten);
$rtick = $row["tick"];

function wirkungsgrad($j, $k, $s, $t, $r) //kleine schiffe schlechter machen
{global $umatrix, $uanz;

    //die dx brauchen keinen support
    if ($r == 4) {
        return 1;
    }

    //bei jagdbooten muß man auf die unterstützung von großkampschiffen achten
    if ($j <= $uanz) {//es gibt eine Freimenge die nicht supportet werden muss
        $wgrad = 1;
    }//bei max. $uanz Jagdbooten Support = 100%
    else {
        $wirkungsgrad_n = (($k * $umatrix[$r][0] + $t * $umatrix[$r][2]) / ($j - $uanz));
        if ($wirkungsgrad_n > 0.9) {
            $wirkungsgrad_n = 0.9;
        } //Max. Support ohne Schlachtschiffe = 90%
        $wirkungsgrad_s = (($s * $umatrix[$r][1]) / ($j - $uanz)); //Schlachtschiff Support
        $wgrad = $wirkungsgrad_s + $wirkungsgrad_n;
        if ($wgrad > 1) {
            // Maximaler Support = 100%
            $wgrad = 1;
        } elseif ($wgrad < 0.5) {
            //Minimaler Support = 50%
            $wgrad = 0.5;
        }
    }

    if ($wgrad != 1) {
        $wgrad = ($uanz + ($j - $uanz) * $wgrad) / $j;
    }

    return($wgrad);
}

function wirkungsgrad2($anzahl_jaeger, $anzahl_kreuzer, $anzahl_schlachter, $r, $rtyp)  //große schiffe schlechter machen
{global $smatrix;

    //die dx brauchen keinen support
    if ($r == 4) {
        return 1;
    }

    // Kreuzer brauchen mindestens 15 Jaeger um optimal zu funktionieren
    if ($anzahl_jaeger >= $smatrix[$r][0] * $anzahl_kreuzer) {
        $mod_kreuzer = 1;
        $anzahl_jaeger -= $smatrix[$r][0] * $anzahl_kreuzer;
    } else {
        $mod_kreuzer = $anzahl_jaeger / ($smatrix[$r][0] * $anzahl_kreuzer);
        $anzahl_jaeger = 0;
        if ($mod_kreuzer < 0.2) {
            $mod_kreuzer = 0.2;
        }
    }

    // Schlachtschiffe benoetigen 50 Jaeger um optimal zu funktionieren
    if ($anzahl_jaeger >= $smatrix[$r][1] * $anzahl_schlachter) {
        $mod_schlachter = 1;
    } else {
        $mod_schlachter = $anzahl_jaeger / ($smatrix[$r][1] * $anzahl_schlachter);
        if ($mod_schlachter < 0.2) {
            $mod_schlachter = 0.2;
        }
    }
    if ($rtyp == 1) {
        return($mod_kreuzer);
    } elseif ($rtyp == 2) {
        return($mod_schlachter);
    } else {
        return(1);
    }
}

//in welchen systemen wird gek�mpft?
$res = mysql_query("SELECT zielsec, zielsys FROM de_user_fleet WHERE aktion = 1 AND zeit = 1 ORDER BY zielsec, zielsys", $db);

$num = mysql_num_rows($res);
//echo '<br>'.$num.' Kampfsysteme<br>';
$z = 0;
$oldsec = 0;
$oldsys = 0;
for ($i = 0; $i < $num; $i++) { //kampfsysteme auslesen und gleich verdichten
    $zielsec  = mysql_result($res, $i, "zielsec");
    $zielsys  = mysql_result($res, $i, "zielsys");
    if ($oldsec <> $zielsec or $oldsys <> $zielsys) {
        // �berpr�fen, ob es das zielsystem gibt
        $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zielsec' AND `system`='$zielsys'", $db);
        $exist = mysql_num_rows($db_daten);
        echo 'Exist: '.$exist.'<br>';
        if ($exist == 1) {
            $kampfsys[$z][0] = $zielsec;
            $kampfsys[$z][1] = $zielsys;
            echo $zielsec.':'.$zielsys.'<br>';
            $z++;
        }
    }
    $oldsec = $zielsec;
    $oldsys = $zielsys;
}
//kampsysteme wurden ermittelt

//jetzt f�r jedes system die flotten auslesen und sie k�mpfen lassen
//$num = count($kampfsys);
echo '<br>insg:'.$z.'<br>';
for ($c = 0; $c < $z; $c++) {
    $defferliste = '';
    $atterliste = '';

    echo '<br>'.$c.'<br><br>';
    $zsec = $kampfsys[$c][0];
    $zsys = $kampfsys[$c][1];

    /////////////////////////////////////////////
    /////////////////////////////////////////////
    //angreifer laden
    /////////////////////////////////////////////
    /////////////////////////////////////////////

    $res = mysql_query("select user_id, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90, 
  komatt, komdef, hsec, hsys, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3, artid4, artlvl4, artid5, artlvl5, artid6, artlvl6
  FROM de_user_fleet WHERE zielsec = $zsec AND zielsys = $zsys
  AND aktion = 1 AND zeit = 1 ORDER BY hsec, hsys", $db);

    echo "select user_id, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90, 
  komatt, komdef, hsec, hsys, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3, artid4, artlvl4, artid5, artlvl5, artid6, artlvl6
  FROM de_user_fleet where zielsec = $zsec AND zielsys = $zsys
  AND aktion = 1 AND zeit = 1 ORDER BY hsec, hsys";

    $anz_atter = 0;
    $rsecold = -1;
    $rsysold = -1;
    $psextmalus = 0;
    $recmalus = 0;
    while ($row = mysql_fetch_array($res)) {//f�r jede flotte die daten auslesen
        $a_userdata[$anz_atter][0] = $row["user_id"];
        $a_userdata[$anz_atter][1] = $row["hsec"];
        $a_userdata[$anz_atter][2] = $row["hsys"];

        //rasse usw. auslesen
        $rsec = $a_userdata[$anz_atter][1];
        $rsys = $a_userdata[$anz_atter][2];
        $result = mysql_query("SELECT user_id, rasse, spielername, spec1, spec2, spec3, spec4, spec5 FROM de_user_data WHERE sector = '$rsec' and `system` = '$rsys'", $db);
        $db_data = mysql_fetch_array($result);
        if ($rsec != $rsecold or $rsys != $rsysold) {
            if ($db_data["rasse"] == 1) {
                $rflag = 'E';
            } elseif ($db_data["rasse"] == 2) {
                $rflag = 'I';
            } elseif ($db_data["rasse"] == 3) {
                $rflag = 'K';
            } elseif ($db_data["rasse"] == 4) {
                $rflag = 'Z';
            } elseif ($db_data["rasse"] == 5) {
                $rflag = 'D';
            }

            if ($atterliste != '') {
                $atterliste .= ', ';
            }
            $atterliste .= $db_data["spielername"].' ['.$rflag.']('.$rsec.':'.$rsys.')';
        }
        $rsecold = $rsec;
        $rsysold = $rsys;

        $a_userdata[$anz_atter][3] = $db_data["rasse"];
        $a_userdata[$anz_atter]['user_id'] = $db_data['user_id'];
        $a_userdata[$anz_atter]['allyid'] = get_player_allyid($db_data['user_id']);
        $a_userdata[$anz_atter]['spec1'] = $db_data['spec1'];
        $a_userdata[$anz_atter]['spec2'] = $db_data['spec2'];
        $a_userdata[$anz_atter]['spec3'] = $db_data['spec3'];
        $a_userdata[$anz_atter]['spec4'] = $db_data['spec4'];
        $a_userdata[$anz_atter]['spec5'] = $db_data['spec5'];
        //Technologien laden
        $a_userdata[$anz_atter]['techs'] = loadPlayerTechs($db_data['user_id']);
        //schauen ob die whg vorhanden ist
        if (hasTech($a_userdata[$anz_atter]['techs'], 4)) {
            $atter_whg[$anz_atter] = 1;
        } else {
            $atter_whg[$anz_atter] = 0;
        }
        echo '<br>Atter-WHG: '.$atter_whg[$anz_atter];

        $atter[$anz_atter][0] = $row["e81"];
        $atter[$anz_atter][1] = $row["e82"];
        $atter[$anz_atter][2] = $row["e83"];
        $atter[$anz_atter][3] = $row["e84"];
        $atter[$anz_atter][4] = $row["e85"];
        $atter[$anz_atter][5] = $row["e86"];
        $atter[$anz_atter][6] = $row["e87"];
        $atter[$anz_atter][7] = $row["e88"];
        $atter[$anz_atter][8] = $row["e89"];
        $atter[$anz_atter][9] = $row["e90"];
        $komatt[$anz_atter] = $row["komatt"];

        //artefakte, die den kampf beeinflussen auslesen
        $awartbonus = 0;
        $bwartbonus = 0;
        for ($i = 1;$i <= 6;$i++) {
            switch ($row["artid$i"]) {
                case 6: //feuerkraft
                    $awartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
                case 7: //blockkraft
                    $bwartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
                case 14: //planetare schilderweiterung st�ren
                    $psextmalus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
                case 15: //recyclotron st�ren
                    $recmalus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]);
                    break;
            }
        }//switch ende
        //erfahrungspunkte, die den kampf beeinflussen
        $awexpbonus = 0 + (((24 - getfleetlevel($row["komatt"])) * 0.4) / 100);
        $bwexpbonus = 0 + (((24 - getfleetlevel($row["komatt"])) * 0.4) / 100);

        //allianzgebäude die den kampf beeinflussen
        if ($a_userdata[$anz_atter]['allyid'] > 0) {
            $allybldg = get_allybldg($a_userdata[$anz_atter]['allyid']);
            //echo '<br>AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA: '.$allybldg[0].'<br>';
            //print_r($allybldg);
            $awallybonus = $allybldg[3] / 100;
            $bwallybonus = $allybldg[4] / 100;
            //boni durch allianzpartner
            $allyidpartner = get_allyid_partner($a_userdata[$anz_atter]['allyid']);
            if ($allyidpartner > 0) {
                $allybldgpartner = get_allybldg($allyidpartner);
                $awallybonus += $allybldgpartner[3] / 100 / 50 * $allybldg[1];
                $bwallybonus += $allybldgpartner[4] / 100 / 50 * $allybldg[1];

            }
        } else {
            $awallybonus = 0;
            $bwallybonus = 0;
        }


        //angriffskraft und blockkraft berechnen
        for ($i = 0; $i < $sv_anz_schiffe; $i++) {
            $wgrad = 1;
            //wirkungsgrad berechnen, wenn es ein jagdboot ist
            if ($i == 1) {
                $wgrad = wirkungsgrad($row["e82"], $row["e84"], $row["e85"], $row["e88"], $db_data["rasse"] - 1);
            }
            //wirkungsgrad berechnen, wenn es ein kreuzer ist
            if ($i == 3) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 1);
            }
            //wirkungsgrad berechnen, wenn es ein schlachtschiff ist
            if ($i == 4) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 2);
            }

            $awgesamtbonus = 1 + $awartbonus + $awexpbonus + $awallybonus;
            $bwgesamtbonus = 1 + $bwartbonus + $bwexpbonus + $bwallybonus;
            $atter_awges[$i] += $atter[$anz_atter][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][2] * $awgesamtbonus;
            $atter_bwges[$i] += $atter[$anz_atter][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][3] * $bwgesamtbonus;
            echo '<br>'.$i.'WGRAD: '.$wgrad;
        }
        echo 'AWEEEEEEEEEEEEEEEEEEEEEEEEEEEEE: ';
        print_r($atter_awges);
        echo '<br>BWEEEEEEEEEEEEEEEEEEEEEEEEEEEEE: ';
        print_r($atter_bwges);
        echo '<br>AWARTBONUS: '.$awartbonus;
        echo '<br>AWEXPBONUS: '.$awexpbonus;
        echo '<br>AWALLYBONUS: '.$awallybonus;
        echo '<br>BWARTBONUS: '.$bwartbonus;
        echo '<br>BWEXPBONUS: '.$bwexpbonus;
        echo '<br>BWALLYBONUS: '.$bwallybonus;

        $anz_atter++;
    }

    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    // Flotten des Angegriffenen Laden
    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    $res = mysql_query("SELECT user_id, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90,
  komatt, komdef, hsec, hsys, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3, artid4, artlvl4, artid5, artlvl5, artid6, artlvl6
  FROM de_user_fleet where hsec = $zsec AND hsys = $zsys AND (aktion = 0 OR (aktion = 3 AND zeit = 1)) ORDER BY user_id", $db);
    $anz_heimatflotten = mysql_num_rows($res);
    $anz_deffer = 0;
    while ($row = mysql_fetch_array($res)) {//f�r jede flotte die daten auslesen
        $d_userdata[$anz_deffer][0] = $row["user_id"];
        $d_userdata[$anz_deffer][1] = $row["hsec"];
        $d_userdata[$anz_deffer][2] = $row["hsys"];
        //rasse auslesen
        $rsec = $d_userdata[$anz_deffer][1];
        $rsys = $d_userdata[$anz_deffer][2];

        $result = mysql_query("SELECT rasse, spielername, npc, spec1, spec2, spec3, spec4, spec5 FROM de_user_data WHERE sector = '$rsec' and `system` = '$rsys'", $db);
        $db_data = mysql_fetch_array($result);
        $d_userdata[$anz_deffer][3] = $db_data["rasse"];
        $spielername = $db_data["spielername"];
        $npc = $db_data["npc"];
        $d_userdata[$anz_deffer]['spec1'] = $db_data['spec1'];
        $d_userdata[$anz_deffer]['spec2'] = $db_data['spec2'];
        $d_userdata[$anz_deffer]['spec3'] = $db_data['spec3'];
        $d_userdata[$anz_deffer]['spec4'] = $db_data['spec4'];
        $d_userdata[$anz_deffer]['spec5'] = $db_data['spec5'];
        //Technologien laden
        $hv = explode("-", $row["user_id"]);//so stellt man die user_id der flotte fest, einfach splitten
        $hsuid = $hv[0];

        $d_userdata[$anz_deffer]['techs'] = loadPlayerTechs($hsuid);
        //schauen ob die whg vorhanden ist
        if (hasTech($d_userdata[$anz_deffer]['techs'], 4)) {
            $deffer_whg[$anz_deffer] = 1;
        } else {
            $deffer_whg[$anz_deffer] = 0;
        }
        echo '<br>HS-WHG: '.$deffer_whg[$anz_deffer];

        $deffer[$anz_deffer][0] = $row["e81"];
        $deffer[$anz_deffer][1] = $row["e82"];
        $deffer[$anz_deffer][2] = $row["e83"];
        $deffer[$anz_deffer][3] = $row["e84"];
        $deffer[$anz_deffer][4] = $row["e85"];
        $deffer[$anz_deffer][5] = $row["e86"];
        $deffer[$anz_deffer][6] = $row["e87"];
        $deffer[$anz_deffer][7] = $row["e88"];
        $deffer[$anz_deffer][8] = $row["e89"];
        $deffer[$anz_deffer][9] = $row["e90"];
        $komdef[$anz_deffer] = $row["komdef"];
        //artefakte, die den kampf beeinflussen auslesen
        $awartbonus = 0;
        $bwartbonus = 0;
        for ($i = 1;$i <= 6;$i++) {
            switch ($row["artid$i"]) {
                case 6: //feuerkraft
                    $awartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
                case 7: //blockkraft
                    $bwartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
            }
        }//switch ende
        //erfahrungspunkte, die den kampf beeinflussen
        $awexpbonus = 0 + (((24 - getfleetlevel($row["komdef"])) * 0.4) / 100);
        $bwexpbonus = 0 + (((24 - getfleetlevel($row["komdef"])) * 0.4) / 100);
        //angriffskraft und blockkraft berechnen
        for ($i = 0; $i < $sv_anz_schiffe; $i++) {
            $wgrad = 1;
            //wirkungsgrad berechnen, wenn es ein jagdboot ist
            if ($i == 1) {
                $wgrad = wirkungsgrad($row["e82"], $row["e84"], $row["e85"], $row["e88"], $db_data["rasse"] - 1);
            }
            //wirkungsgrad berechnen, wenn es ein kreuzer ist
            if ($i == 3) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 1);
            }
            //wirkungsgrad berechnen, wenn es ein schlachtschiff ist
            if ($i == 4) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 2);
            }

            $awgesamtbonus = 1 + $awartbonus + $awexpbonus;
            $bwgesamtbonus = 1 + $bwartbonus + $bwexpbonus;
            $deffer_awges[$i] += $deffer[$anz_deffer][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][2] * $awgesamtbonus;
            $deffer_bwges[$i] += $deffer[$anz_deffer][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][3] * $bwgesamtbonus;
        }
        $anz_deffer++;
    }
    if ($db_data["rasse"] == 1) {
        $rflag = 'E';
    } elseif ($db_data["rasse"] == 2) {
        $rflag = 'I';
    } elseif ($db_data["rasse"] == 3) {
        $rflag = 'K';
    } elseif ($db_data["rasse"] == 4) {
        $rflag = 'Z';
    } elseif ($db_data["rasse"] == 5) {
        $rflag = 'D';
    }

    $defferliste = $spielername.' ['.$rflag.']('.$rsec.':'.$rsys.')';

    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    //verteidigerhilfsflotten laden
    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////

    $res = mysql_query("select user_id, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90,
  komatt, komdef, hsec, hsys, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3, artid4, artlvl4, artid5, artlvl5, artid6, artlvl6 
  FROM de_user_fleet where zielsec = $zsec AND zielsys = $zsys
  AND aktion = 2 AND (zeit = 1 OR (zeit = 0 AND aktzeit > 0)) ORDER BY hsec, hsys", $db);


    $rsecold = -1;
    $rsysold = -1;
    while ($row = mysql_fetch_array($res)) {//f�r jede flotte die daten auslesen
        $d_userdata[$anz_deffer][0] = $row["user_id"];
        $d_userdata[$anz_deffer][1] = $row["hsec"];
        $d_userdata[$anz_deffer][2] = $row["hsys"];

        //rasse auslesen
        $rsec = $d_userdata[$anz_deffer][1];
        $rsys = $d_userdata[$anz_deffer][2];
        $result = mysql_query("SELECT user_id, rasse, spielername, spec1, spec2, spec3, spec4, spec5 FROM de_user_data WHERE sector = '$rsec' and `system` = '$rsys'", $db);
        $db_data = mysql_fetch_array($result);
        $d_userdata[$anz_deffer][3] = $db_data["rasse"];
        $d_userdata[$anz_deffer]['spec1'] = $db_data['spec1'];
        $d_userdata[$anz_deffer]['spec2'] = $db_data['spec2'];
        $d_userdata[$anz_deffer]['spec3'] = $db_data['spec3'];
        $d_userdata[$anz_deffer]['spec4'] = $db_data['spec4'];
        $d_userdata[$anz_deffer]['spec5'] = $db_data['spec5'];

        $d_userdata[$anz_deffer]['techs'] = loadPlayerTechs($db_data['user_id']);

        if ($db_data["rasse"] == 1) {
            $rflag = 'E';
        } elseif ($db_data["rasse"] == 2) {
            $rflag = 'I';
        } elseif ($db_data["rasse"] == 3) {
            $rflag = 'K';
        } elseif ($db_data["rasse"] == 4) {
            $rflag = 'Z';
        } elseif ($db_data["rasse"] == 5) {
            $rflag = 'D';
        }

        if ($rsec != $rsecold or $rsys != $rsysold) {
            $defferliste .= ', '.$db_data["spielername"].' ['.$rflag.']('.$rsec.':'.$rsys.')';
        }
        $rsecold = $rsec;
        $rsysold = $rsys;

        //schauen ob die whg vorhanden ist
        if (hasTech($d_userdata[$anz_deffer]['techs'], 4)) {
            $deffer_whg[$anz_deffer] = 1;
        } else {
            $deffer_whg[$anz_deffer] = 0;
        }
        echo '<br>Deffer-WHG: '.$deffer_whg[$anz_deffer];

        $deffer[$anz_deffer][0] = $row["e81"];
        $deffer[$anz_deffer][1] = $row["e82"];
        $deffer[$anz_deffer][2] = $row["e83"];
        $deffer[$anz_deffer][3] = $row["e84"];
        $deffer[$anz_deffer][4] = $row["e85"];
        $deffer[$anz_deffer][5] = $row["e86"];
        $deffer[$anz_deffer][6] = $row["e87"];
        $deffer[$anz_deffer][7] = $row["e88"];
        $deffer[$anz_deffer][8] = $row["e89"];
        $deffer[$anz_deffer][9] = $row["e90"];
        $komdef[$anz_deffer] = $row["komdef"];
        //artefakte, die den kampf beeinflussen auslesen
        $awartbonus = 0;
        $bwartbonus = 0;
        for ($i = 1;$i <= 6;$i++) {
            switch ($row["artid$i"]) {
                case 6: //feuerkraft
                    $awartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
                case 7: //blockkraft
                    $bwartbonus += ($ua_werte[$row["artid$i"] - 1][$row["artlvl$i"] - 1][0]) / 100;
                    break;
            }
        }//switch ende
        //erfahrungspunkte, die den kampf beeinflussen
        $awexpbonus = 0 + (((24 - getfleetlevel($row["komdef"])) * 0.4) / 100);
        $bwexpbonus = 0 + (((24 - getfleetlevel($row["komdef"])) * 0.4) / 100);

        //angriffskraft und blockkraft berechnen
        for ($i = 0; $i < $sv_anz_schiffe; $i++) {
            $wgrad = 1;
            //wirkungsgrad berechnen, wenn es ein jagdboot ist
            if ($i == 1) {
                $wgrad = wirkungsgrad($row["e82"], $row["e84"], $row["e85"], $row["e88"], $db_data["rasse"] - 1);
            }
            //wirkungsgrad berechnen, wenn es ein kreuzer ist
            if ($i == 3) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 1);
            }
            //wirkungsgrad berechnen, wenn es ein schlachtschiff ist
            if ($i == 4) {
                $wgrad = wirkungsgrad2($row["e81"], $row["e84"], $row["e85"], $db_data["rasse"] - 1, 2);
            }

            $awgesamtbonus = 1 + $awartbonus + $awexpbonus;
            $bwgesamtbonus = 1 + $bwartbonus + $bwexpbonus;
            $deffer_awges[$i] += $deffer[$anz_deffer][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][2] * $awgesamtbonus;
            $deffer_bwges[$i] += $deffer[$anz_deffer][$i] * $wgrad * $unit[$db_data["rasse"] - 1][$i][3] * $bwgesamtbonus;
        }
        $anz_deffer++;
    }

    //t�rme, kollektoren, kopfgeld aus der db holen
    $res = mysql_query("SELECT user_id, e100, e101, e102, e103, e104, defenseexp, col, kg01, kg02, kg03, kg04 FROM
						de_user_data WHERE sector = '$zsec' AND `system` = '$zsys'", $db);
    $row = mysql_fetch_array($res);
    $uid = $row["user_id"];
    $zuid = $uid;


    //schauen ob es ein recyclotron gibt
    if (hasTech($d_userdata[0]['techs'], 6)) {
        $rec = 1;
    } else {
        $rec = 0;
    }

    //schauen ob es einen planeteren schild gibt
    if (hasTech($d_userdata[0]['techs'], 24)) {
        $ps = 1;
    } else {
        $ps = 0;
    }

    //schauen ob es eine whg gibt
    if (hasTech($d_userdata[0]['techs'], 4)) {
        $whg = 1;
    } else {
        $whg = 0;
    }

    //schauen ob es eine planetare schildwerweiterung gibt und berechne die st�rke
    if (hasTech($d_userdata[0]['techs'], 30) == 1) {
        //spielerartefakte auslesen die den erweiterten schild verst�rken
        $psextbonus = 0;
        $db_datenx = mysql_query("SELECT id, level FROM de_user_artefact WHERE id=13 AND user_id='$uid'", $db);
        while ($rowx = mysql_fetch_array($db_datenx)) {
            $psextbonus = $psextbonus + $ua_werte[$rowx["id"] - 1][$rowx["level"] - 1][0] / 100;
        }

        $psext = $sv_planetshieldext[1] + $psextbonus - $psextmalus;
        //spezialisierung
        if ($d_userdata[0]['spec3'] == 1) {
            $psext += 0.1;
        }

        if ($psext < $sv_planetshieldext[0]) {
            $psext = $sv_planetshieldext[0];
        }
        if ($psext > $sv_planetshieldext[2]) {
            $psext = $sv_planetshieldext[2];
        }

        echo '<br>PSEXT: '.$psext.'<br>';
    } else {
        $psext = 0;
    }

    //schnell noch die verteidigungsanlagen laden
    $turm[0] = $row["e100"];
    $turm[1] = $row["e101"];
    $turm[2] = $row["e102"];
    $turm[3] = $row["e103"];
    $turm[4] = $row["e104"];

    //kollektoranzahl
    $kollies = $row["col"];

    //kopfgeld
    $kg[0] = $row["kg01"];
    $kg[1] = $row["kg02"];
    $kg[2] = $row["kg03"];
    $kg[3] = $row["kg04"];

    //erfahrungspunkte verteidigungsanlagen
    $defenseexp = $row["defenseexp"];

    //turmartefakte laden
    //feuerkraft
    $db_daten = mysql_query("SELECT id, level FROM de_user_artefact WHERE id=8 AND user_id='$uid'", $db);
    $tawartbonus = 1;
    while ($row = mysql_fetch_array($db_daten)) {
        $tawartbonus = $tawartbonus + ($ua_werte[$row["id"] - 1][$row["level"] - 1][0] / 100);
    }

    //blockkraft
    $db_daten = mysql_query("SELECT id, level FROM de_user_artefact WHERE id=9 AND user_id='$uid'", $db);
    $tbwartbonus = 1;
    while ($row = mysql_fetch_array($db_daten)) {
        $tbwartbonus = $tbwartbonus + ($ua_werte[$row["id"] - 1][$row["level"] - 1][0] / 100);
    }

    //bonus f�r turmerfahrungspunkte
    $tawartbonus += (((24 - getfleetlevel($defenseexp)) * 0.4) / 100);
    $tbwartbonus += (((24 - getfleetlevel($defenseexp)) * 0.4) / 100);

    echo '<br>TAWARTBONUS: '.$tawartbonus.'<br>';
    //weitere boni berechnen
    $defense_level = 24 - getfleetlevel($defenseexp);
    include '../lib/defenseboni.lib.php';
    if ($defense_bonus_feuerkraft[0] > 0) {
        if (mt_rand(1, 100) <= $defense_bonus_feuerkraft[0]) {
            $tawartbonus += $defense_bonus_feuerkraft[1] / 100;
        }
    }

    echo 'TAWARTBONUS inkl Expbonus: '.$tawartbonus.'<br>';



    //zuerst array nullen
    for ($i = 0;$i < $sv_anz_rassen;$i++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $atterrassen[$i][$s] = 0;
        }
    }

    for ($i = 0;$i < $sv_anz_rassen;$i++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $defferrassen[$i][$s] = 0;
        }
    }

    //die einheiten nach rassen aufteilen
    $rassenvorhanden = array(0,0,0,0,0);
    for ($i = 0;$i < $anz_atter;$i++) {
        //$atterrassen[ingamerasse-1][schiffstyp]
        $rasse = $a_userdata[$i][3] - 1;
        $rassenvorhanden[$rasse]++;
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $atterrassen[$rasse][$s] += $atter[$i][$s];
            //echo 'AWert: '.$rasse.'<br>';
        }
    }

    for ($i = 0;$i < $anz_deffer;$i++) {
        //$defferrassen[inagemerasse-1][schiffstyp]
        $rasse = $d_userdata[$i][3] - 1;
        $rassenvorhanden[$rasse]++;
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $defferrassen[$rasse][$s] += $deffer[$i][$s];
            //echo 'DWert: '.$rasse.'<br>';
        }
    }

    //werte der angreifer/deffer bestimmen
    for ($i = 0; $i < ($sv_anz_schiffe + $sv_anz_tuerme); $i++) {
        //hitpoints
        $atter_hpges[$i] =
          $atterrassen[0][$i] * $unit[0][$i][1] +
          $atterrassen[1][$i] * $unit[1][$i][1] +
          $atterrassen[2][$i] * $unit[2][$i][1] +
          $atterrassen[3][$i] * $unit[3][$i][1] +
          $atterrassen[4][$i] * $unit[4][$i][1];

        if ($i < $sv_anz_schiffe) {
            $deffer_hpges[$i] =
              $defferrassen[0][$i] * $unit[0][$i][1] +
              $defferrassen[1][$i] * $unit[1][$i][1] +
              $defferrassen[2][$i] * $unit[2][$i][1] +
              $defferrassen[3][$i] * $unit[3][$i][1] +
              $defferrassen[4][$i] * $unit[4][$i][1];
        } else {
            $deffer_hpges[$i] = $turm[$i - $sv_anz_schiffe] * $unit[$d_userdata[0][3] - 1][$i][1];
        }

        if ($i < $sv_anz_schiffe) {
            //schiffen werden direkt beim laden berechnet
        } else {
            $deffer_awges[$i] = $turm[$i - $sv_anz_schiffe] * $unit[$d_userdata[0][3] - 1][$i][2] * $tawartbonus;
        }
        //echo '<br>awges:'.$deffer_awges[$i].' ';
    }
    $atter_hprest = $atter_hpges;
    $deffer_hprest = $deffer_hpges;
    $atter_awrest = $atter_awges;
    $deffer_awrest = $deffer_awges;

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    //1. kampfphase - emp-waffen treten in aktion

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //in der schleife alle einheitentypen durchgehen
    for ($i = 0; $i < ($sv_anz_schiffe + $sv_anz_tuerme); $i++) {
        //zuerst schauen ob es in der klasse schiffe gibt die blocken
        //in npc-systemen k�nnen die angreifer nicht blocken
        if ($npc == 0) {
            //angreifer schie�en
            //blockwert auslesen
            $bw = $atter_bwges[$i];
            //echo '<br>BW1:'.$bw.' ';
            if ($bw > 0) {
                for ($j = 0; $j < $sv_anz_schiffe + $sv_anz_tuerme; $j++) {
                    //die angriffswerte nach den vorgaben der blockmatrix verringern
                    //zuerst schauen ob es mali gibt
                    $bw = $bw - ($bw / 100 * $blockmatrix[$i][$j * 2 + 1]);
                    $bziel = $blockmatrix[$i][$j * 2];
                    //echo '<br>BW1:'.$bw.' ';

                    //wert abziehen
                    if (($bw - $bw * $psext) >= $deffer_awrest[$bziel]) {
                        //alle schiffe der klasse werden geblockt
                        $bw = $bw - $deffer_awrest[$bziel];
                        $deffer_awrest[$bziel] = 0;
                        //echo '<br>BW2:'.$bw.' ';
                        //echo '<br>all';

                    } else {
                        //bw reicht nicht zum blocken aller schiffe
                        $deffer_awrest[$bziel] = $deffer_awrest[$bziel] - ($bw - $bw * $psext);
                        $bw = 0;
                        //echo '<br>BW3:'.$bw.' ';
                        //echo '<br>not all';

                        //wenn bw=0 abbrechen, da eh nichts mehr passieren kann
                        break;
                    }
                }
            }
        }
        //ende atter

        //deffer schie�en
        //blockwert rassen�bergreifend berechnen
        if ($i < $sv_anz_schiffe) {
            //blockwert auslesen
            $bw = $deffer_bwges[$i];
        } else {
            $bw = $turm[$i - $sv_anz_schiffe] * $unit[$d_userdata[0][3] - 1][$i][3] * $tbwartbonus;
        }

        //echo '<br>BW1:'.$bw.' ';
        if ($bw > 0) {
            for ($j = 0; $j < ($sv_anz_schiffe + $sv_anz_tuerme); $j++) {
                //die angriffswerte nach den vorgaben der blockmatrix verringern
                //zuerst schauen ob es mali gibt
                $bw = $bw - ($bw / 100 * $blockmatrix[$i][$j * 2 + 1]);
                $bziel = $blockmatrix[$i][$j * 2];
                //echo '<br>BW2:'.$bw.' ';
                //echo '<br>all';

                //wert abziehen
                if ($bw >= $atter_awrest[$bziel]) {
                    //alle schiffe der klasse werden geblockt
                    $bw = $bw - $atter_awrest[$bziel];
                    $atter_awrest[$bziel] = 0;
                } else {
                    //bw reicht nicht zum blocken aller schiffe
                    $atter_awrest[$bziel] = $atter_awrest[$bziel] - $bw;
                    $bw = 0;

                    //echo '<br>BW3:'.$bw.' ';
                    //echo '<br>not all';

                    //wenn bw=0 abbrechen, da eh nichts mehr passieren kann
                    break;
                }
            }
        }
        //ende deffer schie�en
    }

    //berechnen wieviele einheiten insgesamt geblockt wurden und auf die rassen verteilen
    //atter
    for ($i = 0; $i < $sv_anz_schiffe; $i++) {
        //verteilung der geblockten angriffswerte der klasse auf die rasse
        $awgeblockt = $atter_awges[$i] - $atter_awrest[$i];

        //prozente der rassen festellen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($atter_awges[$i] > 0) {
                $anteil[$r] = $atterrassen[$r][$i] * $unit[$r][$i][2] / $atter_awges[$i];
            } else {
                $anteil[$r] = 0;
            }
        }

        //die prozente auf die rassen verteilen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($unit[$r][$i][2] > 0) {
                $atterrassen_geblockt[$r][$i] = floor($awgeblockt * $anteil[$r] / $unit[$r][$i][2]);
            } else {
                $atterrassen_geblockt[$r][$i] = 0;
            }
        }
    }
    //die gesamtgeblockten schiffe auf die einzelnen flotten verteilen
    for ($i = 0;$i < $anz_atter;$i++) {
        $rasse = $a_userdata[$i][3] - 1;
        for ($j = 0; $j < $sv_anz_schiffe; $j++) {
            if ($atter[$i][$j] > 0) {
                $prozent = $atter[$i][$j] / $atterrassen[$rasse][$j];
            } else {
                $prozent = 0;
            }
            $atter_geb[$i][$j] = floor($atterrassen_geblockt[$rasse][$j] * $prozent);
            if ($atter_geb[$i][$j] > $atter[$i][$j]) {
                $atter_geb[$i][$j] = $atter[$i][$j];
            }
            if ($atter_geb[$i][$j] > $atterrassen[$rasse][$j]) {
                $atter_geb[$i][$j] = $atterrassen[$rasse][$j];
            }
            if ($atter_geb[$i][$j] > $atterrassen_geblockt[$rasse][$j]) {
                $atter_geb[$i][$j] = $atterrassen_geblockt[$rasse][$j];
            }

            echo '<br>Atter '.($i).' - Anteil geblockter Einheiten ('.$j.'): '.$atter_geb[$i][$j];
        }
    }

    print_r($atter_geb);

    //deffer - schiffe
    for ($i = 0; $i < $sv_anz_schiffe; $i++) {
        //verteilung der geblockten angriffswerte der klasse auf die rasse
        $awgeblockt = $deffer_awges[$i] - $deffer_awrest[$i];

        //prozente der rassen festellen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($deffer_awges[$i] > 0) {
                $anteil[$r] = $defferrassen[$r][$i] * $unit[$r][$i][2] / $deffer_awges[$i];
            } else {
                $anteil[$r] = 0;
            }
        }

        //die prozente auf die rassen verteilen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($unit[$r][$i][2] > 0) {
                $defferrassen_geblockt[$r][$i] = floor($awgeblockt * $anteil[$r] / $unit[$r][$i][2]);
            } else {
                $defferrassen_geblockt[$r][$i] = 0;
            }
            //echo '<br>Paarung:'.$awgeblockt.' - '.$anteil[$r].' - '.$unit[$r][$i][2];
        }
    }
    //die gesamtgeblockten schiffe auf die einzelnen flotten verteilen
    for ($i = 0;$i < $anz_deffer;$i++) {
        $rasse = $d_userdata[$i][3] - 1;
        for ($j = 0; $j < $sv_anz_schiffe; $j++) {
            if ($deffer[$i][$j] > 0) {
                $prozent = $deffer[$i][$j] / $defferrassen[$rasse][$j];
            } else {
                $prozent = 0;
            }

            $deffer_geb[$i][$j] = floor($defferrassen_geblockt[$rasse][$j] * $prozent);
            //es k�nnen nicht mehr schiffe geblockt werden, als in der flotte sind
            if ($deffer_geb[$i][$j] > $deffer[$i][$j]) {
                $deffer_geb[$i][$j] = $deffer[$i][$j];
            }
            if ($deffer_geb[$i][$j] > $defferrassen[$rasse][$j]) {
                $deffer_geb[$i][$j] = $defferrassen[$rasse][$j];
            }
            if ($deffer_geb[$i][$j] > $defferrassen_geblockt[$rasse][$j]) {
                $deffer_geb[$i][$j] = $defferrassen_geblockt[$rasse][$j];
            }

            echo '<br>Deffer '.($i).' - Anteil geblockter Einheiten ('.$j.'): '.$deffer_geb[$i][$j];
        }
    }


    //deffer - tuerme
    for ($i = $sv_anz_schiffe; $i < $sv_anz_schiffe + $sv_anz_tuerme; $i++) {
        //verteilung der geblockten angriffswerte der klasse auf die rasse
        $awgeblockt = $deffer_awges[$i] - $deffer_awrest[$i];
        //echo '<br>AW:'.$deffer_awges[$i].' '.$deffer_awrest[$i].' '.$i;
        $r = $d_userdata[0][3] - 1;

        if ($unit[$r][$i][2] > 0) {
            $deffertuerme_geblockt[$i - $sv_anz_schiffe] = floor($awgeblockt / $unit[$r][$i][2]);
        } else {
            $deffertuerme_geblockt[$i - $sv_anz_schiffe] = 0;
        }

        if ($deffertuerme_geblockt[$i - $sv_anz_schiffe] > $turm[$i - $sv_anz_schiffe]) {
            $deffertuerme_geblockt[$i - $sv_anz_schiffe] = $turm[$i - $sv_anz_schiffe];
        }

        //if ($awgeblockt>0) echo '<br>TG:'.$deffertuerme_geblockt[$i-$sv_anz_schiffe].' ';
    }
    //emp-waffen ende

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    //2. kampfphase - zerst�rende waffen treten in aktion

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //in der schleife alle einheitentypen durchgehen
    for ($i = 0; $i < ($sv_anz_schiffe + $sv_anz_tuerme); $i++) {
        //zuerst schauen ob es in der klasse schiffe gibt die schie�en
        //angreifer schie�en
        if ($atter_awrest[$i] > 0) {
            for ($j = 0; $j < $sv_anz_schiffe + $sv_anz_tuerme; $j++) {
                //die angriffswerte nach den vorgaben der kampfmatrix verringern
                //zuerst schauen ob es mali gibt
                $atter_awrest[$i] = $atter_awrest[$i] - ($atter_awrest[$i] / 100 * $kampfmatrix[$i][$j * 2 + 1]);
                $aziel = $kampfmatrix[$i][$j * 2];
                //echo '<br>BW1:'.$bw.' ';

                //wert abziehen
                if ($atter_awrest[$i] >= $deffer_hprest[$aziel]) {
                    //alle schiffe der klasse werden geblockt
                    $atter_awrest[$i] = $atter_awrest[$i] - $deffer_hprest[$aziel];
                    $deffer_hprest[$aziel] = 0;
                    //echo '<br>BW2:'.$bw.' ';
                    //echo '<br>all';
                    echo '<br>HPREST1:'.$deffer_hprest[$aziel].' N '.$atter_awrest[$i];
                    ;
                } else {
                    //aw reicht nicht zum zerst�ren aller schiffe
                    $deffer_hprest[$aziel] = $deffer_hprest[$aziel] - $atter_awrest[$i];
                    $atter_awrest[$i] = 0;
                    //echo '<br>BW3:'.$bw.' ';
                    //echo '<br>not all';

                    //wenn bw=0 abbrechen, da eh nichts mehr passieren kann
                    break;
                }
            }
        }
        //ende atter
        //deffer schie�en
        //kampfwert rassen�bergreifend berechnen
        if ($deffer_awrest[$i] > 0) {
            for ($j = 0; $j < ($sv_anz_schiffe + $sv_anz_tuerme); $j++) {
                //die angriffswerte nach den vorgaben der kampfmatrix verringern
                //zuerst schauen ob es mali gibt
                $deffer_awrest[$i] = $deffer_awrest[$i] - ($deffer_awrest[$i] / 100 * $kampfmatrix[$i][$j * 2 + 1]);
                $aziel = $kampfmatrix[$i][$j * 2];

                //wert abziehen
                if ($deffer_awrest[$i] >= $atter_hprest[$aziel]) {
                    //alle schiffe der klasse werden geblockt
                    $deffer_awrest[$i] = $deffer_awrest[$i] - $atter_hprest[$aziel];
                    $atter_hprest[$aziel] = 0;
                } else {
                    //aw reicht nicht zum zerst�ren aller schiffe
                    $atter_hprest[$aziel] = $atter_hprest[$aziel] - $deffer_awrest[$i];
                    $deffer_awrest[$i] = 0;
                    //echo '<br>BW3:'.$bw.' ';
                    //echo '<br>not all';

                    //wenn bw=0 abbrechen, da eh nichts mehr passieren kann
                    break;
                }
            }
        }
        //ende deffer schie�en
    }

    //berechnen wieviele einheiten insgesamt zerst�rt wurden und auf die rassen verteilen
    //atter
    for ($i = 0; $i < $sv_anz_schiffe; $i++) {
        //verteilung der zerst�ren hitpoints der klasse auf die rasse
        $hpzer = $atter_hpges[$i] - $atter_hprest[$i];

        //prozente der rassen festellen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($atter_hpges[$i] > 0) {
                $anteil[$r] = $atterrassen[$r][$i] * $unit[$r][$i][1] / $atter_hpges[$i];
            } else {
                $anteil[$r] = 0;
            }
        }

        //die prozente auf die rassen verteilen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($unit[$r][$i][1] > 0) {
                $atterrassen_zer[$r][$i] = floor($hpzer * $anteil[$r] / $unit[$r][$i][1]);
            } else {
                $atterrassen_zer[$r][$i] = 0;
            }
        }
    }
    //die gesamtzerst�rten schiffe auf die einzelnen flotten verteilen und die erfahrungspunkte berechnen
    for ($i = 0;$i < $anz_atter;$i++) {
        $rasse = $a_userdata[$i][3] - 1;
        for ($j = 0; $j < $sv_anz_schiffe; $j++) {
            if ($atter[$i][$j] > 0) {
                $prozent = $atter[$i][$j] / $atterrassen[$rasse][$j];
            } else {
                $prozent = 0;
            }
            $atter_zer[$i][$j] = floor($atterrassen_zer[$rasse][$j] * $prozent);
            $atter_exp[$i] = floor($atter_exp[$i] + ($atter_zer[$i][$j] * $unit[$rasse][$j][4] / 100));
        }
        //spezialisierung mehr exp f�r die flotte
        if ($a_userdata[$i]['spec2'] == 2) {
            $atter_exp[$i] = floor($atter_exp[$i] * 1.1);
        }
    }
    //schauen wieviel die schlachtschiffe der angreifer recyceln
    for ($i = 0;$i < $anz_atter;$i++) {
        //nur recyceln wenn eine whg vorhanden ist
        if ($atter_whg[$i] == 1) {
            //maximal 10% der zerst�rten kleineinheiten werden recycelt, dazu kann ein schlachter maximal das f�nfache seiner punktezahl recyceln
            $rasse = $a_userdata[$i][3] - 1;
            $schlachter = $atter[$i][4] - $atter_zer[$i][4];
            //schauen wievie max. recycelt werden kann, berechnet sich aus den schiffspunkten f�r schlachtschiffe mal einen wert x
            $maxrecmenge = $schlachter * $unit[$rasse][4][4] * 6;

            //schauen wieviel rohstoffe von zerst�rten schiffen existieren
            $res1 =
             $atter_zer[$i][0] * $unit[$rasse][0][5][0] + //j�ger
             $atter_zer[$i][1] * $unit[$rasse][1][5][0] + //jagdboote
             $atter_zer[$i][5] * $unit[$rasse][5][5][0]; //bomber
            $res1 = floor($res1 / 10);

            $res2 =
             $atter_zer[$i][0] * $unit[$rasse][0][5][1] + //j�ger
             $atter_zer[$i][1] * $unit[$rasse][1][5][1] + //jagdboote
             $atter_zer[$i][5] * $unit[$rasse][5][5][1]; //bomber
            $res2 = floor($res2 / 10);

            //schauen wieviel man im endeffekt recyceln kann
            //zuerst multiplex
            if ($res1 <= $maxrecmenge) {
                $maxrecmenge = $maxrecmenge - $res1;
            } else {
                $res1 = $maxrecmenge;
                $maxrecmenge = 0;
            }

            if (($res2 * 2) <= $maxrecmenge) {
                //$maxrecmenge=$maxrecmenge-$res2;
            } else {
                $res2 = floor($maxrecmenge / 2);
                //$maxrecmenge=0;
            }


            $atter_rec[$i] = array($res1, $res2);
        } else {
            $atter_rec[$i] = array(0, 0);
        }//keine whg
    }




    //deffer - schiffe
    for ($i = 0; $i < $sv_anz_schiffe; $i++) {
        //verteilung der zerst�ren hitpoints der klasse auf die rasse
        $hpzer = $deffer_hpges[$i] - $deffer_hprest[$i];
        echo '<br>hpzer:'.$hpzer;
        //prozente der rassen festellen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($deffer_hpges[$i] > 0) {
                $anteil[$r] = $defferrassen[$r][$i] * $unit[$r][$i][1] / $deffer_hpges[$i];
            } else {
                $anteil[$r] = 0;
            }
        }

        //die prozente auf die rassen verteilen
        for ($r = 0; $r < $sv_anz_rassen; $r++) {
            if ($unit[$r][$i][1] > 0) {
                $defferrassen_zer[$r][$i] = floor($hpzer * $anteil[$r] / $unit[$r][$i][1]);
            } else {
                $defferrassen_zer[$r][$i] = 0;
            }
        }
    }
    //die gesamtzerst�rten schiffe auf die einzelnen flotten verteilen und die erfahrungspunkte berechnen
    for ($i = 0;$i < $anz_deffer;$i++) {
        $rasse = $d_userdata[$i][3] - 1;
        for ($j = 0; $j < $sv_anz_schiffe; $j++) {
            if ($deffer[$i][$j] > 0) {
                $prozent = $deffer[$i][$j] / $defferrassen[$rasse][$j];
            } else {
                $prozent = 0;
            }
            $deffer_zer[$i][$j] = floor($defferrassen_zer[$rasse][$j] * $prozent);
            $deffer_exp[$i] = floor($deffer_exp[$i] + ($deffer_zer[$i][$j] * $unit[$rasse][$j][4] / 100));
            //echo '<br>DEFFEXP:'.($deffer_zer[$i][$j]).' > '.$defferrassen_zer[$rasse][$j];
            //echo '<br>Defferrasse: '.$defferrassen[$rasse][$j].':'.$deffer[$i][$j];
            //echo '<br>Prozent von '.$i.':'.$j.': '.$prozent.'<br>';
        }
        //spezialisierung mehr exp f�r die flotte
        if ($d_userdata[$i]['spec2'] == 2) {
            $deffer_exp[$i] = floor($deffer_exp[$i] * 1.1);
        }
    }


    //deffer - tuerme
    $defenseexp = 0;
    for ($i = $sv_anz_schiffe; $i < $sv_anz_schiffe + $sv_anz_tuerme; $i++) {
        //verteilung der zerst�ren hitpoints der klasse auf die rasse
        $hpzer = $deffer_hpges[$i] - $deffer_hprest[$i];
        //echo '<br>AW:'.$deffer_awges[$i].' '.$deffer_awrest[$i].' '.$i;
        $r = $d_userdata[0][3] - 1;

        if ($unit[$r][$i][1] > 0) {
            $deffertuerme_zer[$i - $sv_anz_schiffe] = floor($hpzer / $unit[$r][$i][1]);
        } else {
            $deffertuerme_zer[$i - $sv_anz_schiffe] = 0;
        }

        //planetares schild vorhanden?
        if ($ps == 1) {
            //erfahrungspunktebonus des planetaren schildes berechnen
            $ps_bonus = $sv_ps_bonus + ($sv_ps_bonus * $defense_bonus_ps / 100);
            //bonus des planetaren schildes berechnen bzgl. der spezialisierung
            if ($d_userdata[0]['spec3'] == 1) {
                $ps_bonus += 10;
            }
            echo '<br>Planetarer Schild vorhanden: '.$ps_bonus.'<br>';
            $deffertuerme_zer[$i - $sv_anz_schiffe] =
             floor($deffertuerme_zer[$i - $sv_anz_schiffe] - ($deffertuerme_zer[$i - $sv_anz_schiffe] / 100 * $ps_bonus));
        }

        //erfahrungspunkte der t�rme der heimatflotte zurechnen - veraltet
        //$deffer_exp[0]=floor($deffer_exp[0]+($deffertuerme_zer[$i-$sv_anz_schiffe]*$unit[$r][$i][4]/100));
        //erfahrungspunkte der t�rme direkt bei den t�rmen hinterlegen
        $defenseexp += floor($deffertuerme_zer[$i - $sv_anz_schiffe] * $unit[$r][$i][4] / 100);
    }
    //test auf spezialisierung
    if ($d_userdata[0]['spec2'] == 1) {
        $defenseexp = floor($defenseexp * 1.5);
    }


    //////////////////////////////////////////////////////////////////
    // Schlachtschiffrecycling
    //////////////////////////////////////////////////////////////////
    for ($i = 0;$i < $anz_deffer;$i++) {
        //nur recyceln wenn eine whg vorhanden ist
        if ($deffer_whg[$i] == 1) {

            //maximal 10% der zerst�rten kleineinheiten werden recycelt, dazu kann ein schlachter maximal das f�nfache seiner punktezahl recyceln
            $rasse = $d_userdata[$i][3] - 1;
            $schlachter = $deffer[$i][4] - $deffer_zer[$i][4];
            //schauen wievie max. recycelt werden kann
            $maxrecmenge = $schlachter * $unit[$rasse][4][4] * 4;

            //schauen wieviel rohstoffe von zerst�rten schiffen existieren
            $res1 =
             $deffer_zer[$i][0] * $unit[$rasse][0][5][0] + //j�ger
             $deffer_zer[$i][1] * $unit[$rasse][1][5][0] + //jagdboote
             $deffer_zer[$i][5] * $unit[$rasse][5][5][0]; //bomber
            $res1 = floor($res1 / 10);

            $res2 =
             $deffer_zer[$i][0] * $unit[$rasse][0][5][1] + //j�ger
             $deffer_zer[$i][1] * $unit[$rasse][1][5][1] + //jagdboote
             $deffer_zer[$i][5] * $unit[$rasse][5][5][1]; //bomber
            $res2 = floor($res2 / 10);

            //schauen wieviel man im endeffekt recyceln kann
            //zuerst multiplex
            if ($res1 <= $maxrecmenge) {
                $maxrecmenge = $maxrecmenge - $res1;
            } else {
                $res1 = $maxrecmenge;
                $maxrecmenge = 0;
            }

            if (($res2 * 2) <= $maxrecmenge) {
                //$maxrecmenge=$maxrecmenge-$res2;
            } else {
                $res2 = floor($maxrecmenge / 2);
                //$maxrecmenge=0;
            }

            $deffer_rec[$i] = array($res1, $res2);
        } else {
            $deffer_rec[$i] = array(0, 0);
        }//keine whg

        //noch ein test einbauen ob der angegriffene das recyclotron hat, das h�tte in dem fall vorrang
        if ($i < $anz_heimatflotten and $rec == 1) {
            $deffer_rec[$i] = array(0, 0);
        }
    }

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    //3. kampfphase - gewinner ermitteln und kollektoren und kopfgeld verteilen

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    //gewonnen hat, wer mehr schiffe vom feind vernichtet hat, webei der angreifer nen bonus erh�lt um angriffe zu forcieren
    //hitpoints der Angreifer berechnen
    $atter_allhp = 0;
    $atter_resthp = 0;
    for ($i = 0; $i < ($sv_anz_schiffe); $i++) {
        //hitpoints
        $atter_allhp += $atter_hpges[$i];
        $atter_resthp += $atter_hprest[$i];
    }

    //hitpoints der Verteidiger berechnen
    $deffer_allhp = 0;
    $deffer_resthp = 0;
    for ($i = 0; $i < ($sv_anz_schiffe + $sv_anz_tuerme); $i++) {
        //hitpoints
        $deffer_allhp += $deffer_hpges[$i];
        $deffer_resthp += $deffer_hprest[$i];
    }

    $vernichtete_atter = $atter_allhp - $atter_resthp;
    $vernichtete_deffer = $deffer_allhp - $deffer_resthp;

    //verhältnis der zerstörung ausrechnen, je größer die zahl, desto weniger einheiten wurden vernichtet
    //if($vernichtete_deffer>0)$zrd=$deffer_allhp/$vernichtete_deffer;else {$zrd=1;$zra=0;}
    //if($vernichtete_atter>0)$zra=$atter_allhp/$vernichtete_atter;else {$zrd=0;$zra=1;}
    if ($vernichtete_deffer == 0) {
        $vernichtete_deffer = 0.000001;
    }
    if ($vernichtete_atter == 0) {
        $vernichtete_atter   = 0.000001;
    }
    $zrd = $deffer_allhp / $vernichtete_deffer;
    $zra = $atter_allhp / $vernichtete_atter;


    //gewinnabfrage
    //$vernichtete_atter = soviele hintpoints wurden verloren
    echo '<br>DEFFER-HP(verloren): '.$vernichtete_deffer.' - DEFFER-HP(gesamt):'.$deffer_allhp.' - ZRD:'.$zrd;
    echo '<br>ATTER-HP(verloren): '.$vernichtete_atter.' - ATTER-HP(gesamt):'.$atter_allhp.' - ZRA:'.$zra;

    //if ($vernichtete_deffer*1.1>=$vernichtete_atter)

    //schaue wieviel prozent der punkte jeder angreifer/deffer mit seiner flotte ausmacht
    //atter
    $atter_pktges = 0;
    for ($i = 0;$i < $anz_atter;$i++) {
        $rasse = $a_userdata[$i][3] - 1;
        $atter_pkt[$i] = 0;

        for ($j = 0;$j < $sv_anz_schiffe;$j++) {
            $atter_pkt[$i] = $atter_pkt[$i] + $atter[$i][$j] * $unit[$rasse][$j][4];//($unit[$rasse][$j][2]+$unit[$rasse][$j][3]);
        }

        $atter_pktges += $atter_pkt[$i];
    }

    /////////////////////////////////
    //deffer
    /////////////////////////////////
    $deffer_pktges = 0;
    $deffer_pktges_schiffe = 0;
    $deffer_pktges_tuerme = 0;
    $deffer_pktges_zer = 0;
    $deffer_pktges_zer_schiffe = 0;
    $deffer_pktges_zer_tuerme = 0;
    //schiffe
    for ($i = 0;$i < $anz_deffer;$i++) {
        $rasse = $d_userdata[$i][3] - 1;
        $deffer_pkt[$i] = 0;

        //schiffe
        for ($j = 0;$j < $sv_anz_schiffe;$j++) {
            $deffer_pkt[$i] = $deffer_pkt[$i] + $deffer[$i][$j] * $unit[$rasse][$j][4];//($unit[$rasse][$j][2]+$unit[$rasse][$j][3]);
            $deffer_pktges_zer += $deffer_zer[$i][$j] * $unit[$rasse][$j][4];
            $deffer_pktges_zer_schiffe += $deffer_zer[$i][$j] * $unit[$rasse][$j][4];
        }
        $deffer_pktges += $deffer_pkt[$i];
        $deffer_pktges_schiffe += $deffer_pkt[$i];
    }
    //Türme
    $rasse = $d_userdata[0][3] - 1;
    for ($s = 0;$s < $sv_anz_tuerme;$s++) {
        $deffer_pktges += $turm[$s] * $unit[$rasse][$sv_anz_schiffe + $s][4];
        $deffer_pktges_tuerme += $turm[$s] * $unit[$rasse][$sv_anz_schiffe + $s][4];
        $deffer_pktges_zer += $deffertuerme_zer[$s] * $unit[$rasse][$sv_anz_schiffe + $s][4];
        $deffer_pktges_zer_tuerme += $deffertuerme_zer[$s] * $unit[$rasse][$sv_anz_schiffe + $s][4];
    }


    //wenn $zra>$zrd also der atter weniger einheiten verloren hat, als der deffer auf die gesamtflotte gerechnet
    //if($zra*1.1>=$zrd){
    if ($zra >= $zrd) {
        /////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        // der angreifer erhält ein zufälliges artefakt bei den npc
        /////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        if ($npc == 1) {

            //den größten angreifer bestimmen
            $biggestatter = 0;
            $biggestanteil = 0;
            for ($i = 0;$i < $anz_atter;$i++) {
                $anteil = $atter_pkt[$i] / $atter_pktges;
                if ($anteil > $biggestanteil) {
                    $biggestatter = $i;
                    $biggestanteil = $anteil;
                }
            }

            $hv = explode("-", $a_userdata[$biggestatter][0]);
            $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten

            //auf freien platz im artefaktgeb�ude �berpr�fen
            if (get_free_artefact_places($uid) > 0) {
                //artefakt per zufall aussuchen
                $ai = mt_rand(1, $ua_index + 1);

                //artefakt dem spieler im geb�ude hinterlegen
                mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$uid', '$ai', '1')", $db);

                //artefakt info  für die news
                $text = $kt_lang['npcartget'].$ua_name[$ai - 1];

                //npc-artefact-counter erhöhen
                mysql_query("UPDATE de_user_data SET npcartefact = npcartefact+1 WHERE user_id = $uid", $db);
            } else { //newsinfo, dass das gebäude voll war
                $text = $kt_lang['npcartbldgfull'];
            }

            //allianzartefakt vergeben
            $allyid = get_player_allyid($uid);
            if ($allyid > 0) {
                mysql_query("UPDATE de_allys SET artefacts = artefacts+1 WHERE id = $allyid", $db);
                $text .= ', '.$kt_lang['allianzartefakt'];
            }

            //news in der db hinterlegen
            $time = strftime("%Y%m%d%H%M%S");
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')", $db);
            mysql_query("UPDATE de_user_data SET newnews = 1 WHERE user_id = $uid", $db);
        }

        //wenn der/die angreifer gewonnen haben findet die kollieverteilung statt
        //zuerstmal schauen wieviele kollies es insgesamt gibt
        $stehlbarekollies = floor($kollies * $sv_kollie_klaurate);
        echo '<br>SK: '.$stehlbarekollies;



        //////////////////////////////////////////////////////////////////////////
        //anhand der Punkte die Verteilung der Kollektoren berechnen
        //////////////////////////////////////////////////////////////////////////

        $stolenkollies = 0;
        for ($i = 0;$i < $anz_atter;$i++) {
            $rasse = $a_userdata[$i][3] - 1;
            $anteil = $atter_pkt[$i] / $atter_pktges;

            $atter_sk[$i] = floor($stehlbarekollies * $anteil);
            //schauen ob er genug transmitterschiffe hat
            if ($atter_sk[$i] >= ($atter[$i][6] - $atter_zer[$i][6])) {
                //zuwenig transmitterschiffe
                $atter_sk[$i] = ($atter[$i][6] - $atter_zer[$i][6]);
                $atter_zer[$i][6] = $atter[$i][6];
                //gesamtkb updaten
                echo 'A1: '.$atterrassen_zer[$rasse][6];
                $atterrassen_zer[$rasse][6] = $atterrassen_zer[$rasse][6] + $atter_sk[$i];//($atter[$i][6]-$atter_zer[$i][6]);
                echo 'A2: '.$atterrassen_zer[$rasse][6];

            } else {
                //genug transmitterschiffe
                $atter_zer[$i][6] = $atter_zer[$i][6] + $atter_sk[$i];
                //gesamtkb updaten
                echo 'A3: '.$atterrassen_zer[$rasse][6];
                $atterrassen_zer[$rasse][6] = $atterrassen_zer[$rasse][6] + $atter_sk[$i];
                echo 'A4: '.$atterrassen_zer[$rasse][6];
            }

            $stolenkollies += $atter_sk[$i];
            echo '<br>SKR: '.$stolenkollies;
        }
    } else {
        $stolenkollies = -1;
    }

    //wenn kollektoren gestohlen wurden, dem ziel direkt diese abziehen und bei der allianz vermerken
    if ($stolenkollies > 0) {
        mysql_query("UPDATE de_user_data SET col = col - '$stolenkollies' WHERE sector='$zsec' AND `system`='$zsys';", $db);
        //allianz
        //id auslesen
        $allyid = get_player_allyid(get_user_id_from_coord($zsec, $zsys));
        //ally gutschreiben
        if ($allyid > 0) {
            mysql_query("UPDATE de_allys SET collost = collost + '$stolenkollies' WHERE id='$allyid'", $db);
        }
    }

    //wenn es erfahrungspunkte für verteidigungsanlagen gibt, diese gutschreiben
    if ($defenseexp > 0 && $sv_oscar != 1) {
        mysql_query("UPDATE de_user_data SET defenseexp = defenseexp + '$defenseexp' WHERE sector='$zsec' AND `system`='$zsys'", $db);
    }

    //////////////////////////////////////////////////////////////////////////
    //anhand der Punkte die Verteilung vom Kopfgeld berechnen
    //////////////////////////////////////////////////////////////////////////
    if ($sv_oscar != 1) {
        //1. feststellen wie viel dem Ziel (nur das Ziel, keine weiteren Deffer an Punkten zerst�rt worden ist
        //Kopfgeld gibt es bei verlorenen Angriffen
        //Schiffe
        $target_score_lost = 0;
        for ($i = 0;$i < $anz_heimatflotten;$i++) {
            //zerst�rte Einheiten
            for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                $target_score_lost += $deffer_zer[$i][$s] * $unit[$d_userdata[0][3] - 1][$s][4];
            }
        }
        //T�rme

        for ($s = 0;$s < $sv_anz_tuerme;$s++) {
            $target_score_lost += $deffertuerme_zer[$s] * $unit[$d_userdata[0][3] - 1][$s + $sv_anz_schiffe][4];
        }

        for ($i = 0;$i < $anz_atter;$i++) {
            //feststellen wieviel kopfgeld man ausbezahlt bekommt
            //das ist abh�ngig davon, wie viel Einheiten beim Ziel und bei einem selbst zerst�rt worden sind
            if (($kg[0] > 0 or $kg[1] > 0 or $kg[2] > 0 or $kg[3] > 0) && $target_score_lost > 0) {
                $rasse = $a_userdata[$i][3] - 1;
                $anteil = $atter_pkt[$i] / $atter_pktges;

                //user_id des angreifers
                $auid = $a_userdata[$i]['user_id'];

                //die Punkteverluste dieses Atters berechnen, gelten als Maximalgrenze
                $atter_score_lost = 0;
                for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                    $atter_score_lost += $atter_zer[$i][$s] * $unit[$rasse][$s][4];
                }
                //dem Atter werden maximal X% erstattet
                $eigener_punkteverlust = $atter_score_lost;
                $atter_score_lost = $atter_score_lost * 0.25;

                //man bekommt maximal das KG in H�he der anteiligen Verluste beim Gegner
                //und man bekommt maximal das KG in H�he der eigenen Verluste
                if ($atter_score_lost > $target_score_lost * $anteil) {
                    $atter_score_lost = $target_score_lost * $anteil;
                }

                //pro Punkt gibt es X Energie
                $atter_get_energie = $atter_score_lost * 10;

                //dieses auf die 4 Ressourcentypen aufteilen
                $atter_get_energie / 4;

                //Endwert ausrechnen im Rohstoffverh�ltnis
                $getkg[0] = floor($atter_get_energie / 4);
                $getkg[1] = floor($atter_get_energie / 8);
                $getkg[2] = floor($atter_get_energie / 12);
                $getkg[3] = floor($atter_get_energie / 16);

                if ($getkg[0] > $kg[0] * $anteil) {
                    $getkg[0] = $kg[0] * $anteil;
                }
                if ($getkg[1] > $kg[1] * $anteil) {
                    $getkg[1] = $kg[1] * $anteil;
                }
                if ($getkg[2] > $kg[2] * $anteil) {
                    $getkg[2] = $kg[2] * $anteil;
                }
                if ($getkg[3] > $kg[3] * $anteil) {
                    $getkg[3] = $kg[3] * $anteil;
                }


                //dem ziel das kopfgeld abziehen
                mysql_query("UPDATE de_user_data SET 
					kg01 = kg01 - '$getkg[0]',
					kg02 = kg02 - '$getkg[1]',
					kg03 = kg03 - '$getkg[2]',
					kg04 = kg04 - '$getkg[3]'
					WHERE sector='$zsec' AND `system`='$zsys'", $db);

                //dem angreifer die rohstoffe gutschreiben und vermerken wie gut er ist
                $kgjaeger = $getkg[0] + $getkg[1] * 2 + $getkg[2] * 3 + $getkg[3] * 4;
                mysql_query("UPDATE de_user_data SET 
					restyp01 = restyp01 + '$getkg[0]',
					restyp02 = restyp02 + '$getkg[1]',
					restyp03 = restyp03 + '$getkg[2]',
					restyp04 = restyp04 + '$getkg[3]',
					kgget=kgget+'$kgjaeger'
					WHERE user_id='$auid'", $db);


                //kopfgeldinfo an den angreifer
                $time = strftime("%Y%m%d%H%M%S");
                //$nachricht.='<div style="color: #FF0000; font-weight: bold;">Achtung: Dies ist ein Test, die Rohstoffe werden nicht gutgeschrieben. Bitte die Werte kontrollieren, ob sie stimmen.</div><br>';
                $nachricht = 'Auf das Ziel ausgesetztes Kopfgeld: '.number_format($kg[0], 0, "", ".").' M -- '.number_format($kg[1], 0, "", ".").
                ' D -- '.number_format($kg[2], 0, "", ".").' I -- '.number_format($kg[3], 0, "", ".").' E';
                $nachricht .= '<br>Kopfgeldauszahlung: '.number_format($getkg[0], 0, "", ".").' M -- '.number_format($getkg[1], 0, "", ".").
                ' D -- '.number_format($getkg[2], 0, "", ".").' I -- '.number_format($getkg[3], 0, "", ".").' E';
                $nachricht .= '<br>Punkteverluste Ziel: '.number_format($target_score_lost, 0, "", ".");
                $nachricht .= '<br>Dein Anteil an den Punkteverlusten beim Ziel: '.number_format($target_score_lost * $anteil, 0, "", ".");
                $nachricht .= '<br>Deine eigenen Punkteverluste: '.number_format($eigener_punkteverlust, 0, "", ".");

                $nachricht .= '<br>Pro vernichtetem Punkt beim Ziel (weitere Deffer z&auml;hlen nicht) erh&auml;lt man, sofern verf&uuml;gbar, 10 Rohstoffeinheiten aufgeteilt auf M,D,I und E.';
                $nachricht .= '<br>Das erhaltene Kopfgeld kann nicht gr&ouml;&szlig;er als 25% der selbst verlorenen Punkte sein.';


                mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$auid', 3,'$time','$nachricht')", $db);
                echo $nachricht.'<br>';
                /*
                $text ='$auid: '.$auid;
                $text.="\r\n".'$target_score_lost: '.$target_score_lost;
                $text.="\r\n".'$eigener_punkteverlust: '.$eigener_punkteverlust;
                $text.="\r\n".'$atter_score_lost: '.$atter_score_lost;
                $text.="\r\n".'$atter_get_energie: '.$atter_get_energie;

                $text.="\r\n".'$kg[0]: '.$kg[0];
                $text.="\r\n".'$kg[1]: '.$kg[1];
                $text.="\r\n".'$kg[2]: '.$kg[2];
                $text.="\r\n".'$kg[3]: '.$kg[3];
                $text.="\r\n".'$getkg[0]: '.$getkg[0];
                $text.="\r\n".'$getkg[1]: '.$getkg[1];
                $text.="\r\n".'$getkg[2]: '.$getkg[2];
                $text.="\r\n".'$getkg[3]: '.$getkg[3];

                $text.="\r\n".'$anteil: '.$anteil;
                $text.="\r\n".'Zielrasse: '.($d_userdata[0][3]-1);

                @mail($GLOBALS['env_admin_email'], $sv_server_tag.' KG '.$uid, $text.'<br>'.$nachricht, 'FROM: '.$GLOBALS['env_admin_email']);
                */
            }
        }
    }

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    //4. kampfphase - kampfbericht erstellen, lastnpcatt-tick setzen

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    // KB zuerst den teil den alle bekommen
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////
    // Atter
    ///////////////////////////////////////////
    $atterstring = '';
    unset($kb_einheiten_atter);
    //Einheiten insgesamt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $atterstring = $atterstring.$atterrassen[$r][$s].';';
            $kb_einheiten_atter[0][$r][$s] = $atterrassen[$r][$s];
        }
    }

    //geblockt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            //fix f�r artefakte beim blocken
            $wert = $atterrassen_geblockt[$r][$s];
            if ($wert > $atterrassen[$r][$s]) {
                $wert = $atterrassen[$r][$s];
            }
            $atterstring = $atterstring.$wert.';';
            $kb_einheiten_atter[1][$r][$s] = $wert;
        }
    }

    //�berlebt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $atterstring = $atterstring.($atterrassen[$r][$s] - $atterrassen_zer[$r][$s]).';';
            $kb_einheiten_atter[2][$r][$s] = $atterrassen_zer[$r][$s];
        }
    }

    ///////////////////////////////////////////
    // Deffer
    ///////////////////////////////////////////


    $defferstring = '';
    unset($kb_einheiten_deffer);
    //Enheiten insgesamt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $defferstring = $defferstring.$defferrassen[$r][$s].';';
            $kb_einheiten_deffer[0][$r][$s] = $defferrassen[$r][$s];
        }
    }

    //geblockt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            //fix f�r artefakte beim blocken
            $wert = $defferrassen_geblockt[$r][$s];
            //echo '<br>defferrassen_geblockt['.$r.']['.$s.']: '.$defferrassen_geblockt[$r][$s];
            if ($wert > $defferrassen[$r][$s]) {
                $wert = $defferrassen[$r][$s];
            }
            $defferstring = $defferstring.$wert.';';
            $kb_einheiten_deffer[1][$r][$s] = $wert;
        }
    }


    //�berlebt
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $defferstring = $defferstring.($defferrassen[$r][$s] - $defferrassen_zer[$r][$s]).';';
            $kb_einheiten_deffer[2][$r][$s] = $defferrassen_zer[$r][$s];
        }
    }

    //////////////////////////////////////////////////////////////
    // T�rme
    //////////////////////////////////////////////////////////////
    unset($kb_tuerme);
    //insg
    for ($s = 0;$s < $sv_anz_tuerme;$s++) {
        $defferstring = $defferstring.$turm[$s].';';
        $kb_tuerme[0][$s] = $turm[$s];
    }

    //geblockt
    for ($s = 0;$s < $sv_anz_tuerme;$s++) {
        $defferstring = $defferstring.$deffertuerme_geblockt[$s].';';
        $kb_tuerme[1][$s] = $deffertuerme_geblockt[$s];
    }

    //�berlebt
    for ($s = 0;$s < $sv_anz_tuerme;$s++) {
        $defferstring = $defferstring.($turm[$s] - $deffertuerme_zer[$s]).';';
        $kb_tuerme[2][$s] = $deffertuerme_zer[$s];
    }



    $datenstring = '';
    unset($kb_daten);
    //kollieverluste
    $datenstring = $datenstring.$stolenkollies.';';
    $kb_daten['colstolen'] = $stolenkollies;

    //koordinaten
    $datenstring = $datenstring.$zsec.';'.$zsys.';';
    $kb_daten['sector'] = $zsec;
    $kb_daten['system'] = $zsys;

    //rassen die teilnehmen
    $kb_daten['rassen'] = $rassenvorhanden;
    for ($r = 0;$r < $sv_anz_rassen;$r++) {
        if ($rassenvorhanden[$r] > 1) {
            $rassenvorhanden[$r] = 1;
        }
        $datenstring = $datenstring.$rassenvorhanden[$r].';';
    }

    //rasse des deffers/t�rme
    $datenstring = $datenstring.($d_userdata[0][3] - 1).';';
    $kb_daten['target_rasse'] = $d_userdata[0][3] - 1;

    //die namen/systeme der atter/deffer
    $datenstring = $datenstring.$atterliste.';'.$defferliste.';';
    $kb_daten['atterliste'] = $atterliste;
    $kb_daten['defferliste'] = $defferliste;

    //zerstörte HP/maximale HP
    $kb_daten['deffer_hp'] = $deffer_allhp;
    $kb_daten['atter_hp'] = $atter_allhp;

    $kb_daten['deffer_hp_lost'] = $vernichtete_deffer;
    $kb_daten['atter_hp_lost'] = $vernichtete_atter;

    //echo '<br>DEFFER-HP(verloren): '.$vernichtete_deffer.' - DEFFER-HP(gesamt):'.$deffer_allhp.' - ZRD:'.$zrd;
    //echo '<br>ATTER-HP(verloren): '.$vernichtete_atter.' - ATTER-HP(gesamt):'.$atter_allhp.' - ZRA:'.$zra;


    //globalen KB speichern - START
    //atterliste erstellen
    //ATTER
    $global_atter = '';
    //user_id verdichten, soll nur einmal enthalten sein
    unset($global_user_id);
    for ($gi = 0; $gi < count($a_userdata);$gi++) {
        $global_user_id[] = $a_userdata[$gi]['user_id'];
    }
    $global_user_id = array_unique($global_user_id);

    foreach ($global_user_id as $value) {
        //owner_id anhand der user_id auslesen
        $db_dateng = mysql_query("SELECT owner_id FROM de_login WHERE user_id='".$value."'", $db);
        $rowg = mysql_fetch_array($db_dateng);
        $owner_id = $rowg["owner_id"];
        if ($global_atter != '') {
            $global_atter .= ';';
        }
        $global_atter .= $owner_id;
    }

    //DEFFER
    $global_deffer = '';
    //user_id verdichten, soll nur einmal enthalten sein
    unset($global_user_id);
    if (is_array($d_userdata)) {
        for ($gi = 0; $gi < count($d_userdata);$gi++) {
            $hv = explode("-", $d_userdata[$gi][0]);//so stellt man die user_id der flotte fest, einfach splitten
            $global_user_id[] = $hv[0];
        }
    }

    if (is_array($global_user_id)) {
        $global_user_id = array_unique($global_user_id);

        echo 'VARDUMP: ';
        var_dump($global_user_id);

        foreach ($global_user_id as $value) {
            //owner_id anhand der user_id auslesen
            $db_dateng = mysql_query("SELECT owner_id FROM de_login WHERE user_id='".$value."'", $db);
            echo "<br>SELECT owner_id FROM de_login WHERE user_id='".$value."'";
            $rowg = mysql_fetch_array($db_dateng);
            $owner_id = $rowg["owner_id"];
            if ($global_deffer != '') {
                $global_deffer .= ';';
            }
            $global_deffer .= $owner_id;
        }
    }

    $global_kb = $atterstring.$defferstring.$datenstring;

    unset($global_kb_array);

    $global_kb_array['daten'] = $kb_daten;
    $global_kb_array['daten']['atter'] = $global_atter;
    $global_kb_array['daten']['deffer'] = $global_deffer;
    $global_kb_array['einheiten_atter'] = $kb_einheiten_atter;
    $global_kb_array['einheiten_deffer'] = $kb_einheiten_deffer;
    $global_kb_array['tuerme'] = $kb_tuerme;

    mysqli_query($GLOBALS['dbi_ls'], "INSERT INTO ls_de_kb SET time=NOW(), server='$sv_server_tag', atter='$global_atter', deffer='$global_deffer', kb='".serialize($global_kb_array)."', kbversion=1");
    //}

    //globalen KB speichern - ENDE

    //hier f�ngt die schleife f�r die spieler an um den kb zu speichern

    $time = strftime("%Y%m%d%H%M%S");
    //////////////////////////////////////////////////////
    //zuerst die angreifer
    //////////////////////////////////////////////////////
    for ($i = 0;$i < $anz_atter;$i++) {
        $hv = explode("-", $a_userdata[$i][0]);
        $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten

        //allyid auslesen
        $allyid = get_player_allyid($uid);

        //lastpcatt-tick setzen
        if ($npc == 0) {
            mysql_query("UPDATE de_user_data SET lastpcatt='$rtick' WHERE user_id = '$uid'", $db);
        }

        //teil des spielers selbst vom kb machen
        $spielerstring = '';
        unset($kb_einheiten_spieler);
        unset($kb_daten_spieler);

        //Einheiten insgesamt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $spielerstring = $spielerstring.$atter[$i][$s].';';
            $kb_einheiten_spieler[0][$s] = $atter[$i][$s];
        }

        //geblockt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            //fix f�r artefakte beim blocken
            $wert = $atter_geb[$i][$s];
            echo '<br>Flotte '.$i.' - Einheit '.$s.': '.$wert;
            if ($wert > $atter[$i][$s]) {
                $wert = $atter[$i][$s];
            }
            $spielerstring = $spielerstring.$wert.';';
            $kb_einheiten_spieler[1][$s] = $wert;
        }

        //�berlebt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $spielerstring = $spielerstring.($atter[$i][$s] - $atter_zer[$i][$s]).';';
            $kb_einheiten_spieler[2][$s] = $atter_zer[$i][$s];
        }

        //anzahl der kollies die der spieler bekommen hat
        if ($atter_sk[$i] == '') {
            $atter_sk[$i] = '0';
        }

        //�berpr�fen ob der angreifer einen kollektor bekommt, oder ob sie zerst�rt werden
        if ($atter_sk[$i] > 0) {
            //angriffsgrenze für die kollektoren berechnen
            //kollektoren des atters auslesen
            $col = get_col_amount($uid);
            //kollektorenangriffsgrenze berechnen
            $col_angriffsgrenze = $col * 100 / $maxcol;
            $col_angriffsgrenze_final = $col_angriffsgrenze / 100 * $sv_max_col_attgrenze;
            if ($col_angriffsgrenze_final > $sv_max_col_attgrenze) {
                $col_angriffsgrenze_final = $sv_max_col_attgrenze;
            }
            if ($col_angriffsgrenze_final < $sv_min_col_attgrenze) {
                $col_angriffsgrenze_final = $sv_min_col_attgrenze;
            }
            //bei npc-zielen ist die kollektorenangriffsgrenze immer der minimumwert
            if ($npc == 1) {
                $col_angriffsgrenze_final = $sv_min_col_attgrenze;
            }

            //if ($col*$col_angriffsgrenze_final<=$kollies OR $npc==1)//man erh�lt kollektoren
            if ($col * $col_angriffsgrenze_final <= $kollies) {//man erh�lt kollektoren
                echo 'Kollektoren erbeutet.';
                mysql_query("UPDATE de_user_data SET col = col + '$atter_sk[$i]' WHERE user_id = '$uid'", $db);
                //fix für br, jeder kollektor gibt x M
                //24.09.2015 abgeschafft wegen pushing
                /*
                if($rtick>2500000){
                    $br_m_res=$atter_sk[$i]*1000000;
                    mysql_query("UPDATE de_user_data SET restyp01=restyp01+".$br_m_res." WHERE user_id = '$uid'",$db);
                    //nachricht an den account
                    $time=strftime("%Y%m%d%H%M%S");
                    $text='Battleround-Multiplex-Bonus f&uuml;r eroberte Kollektoren: '.number_format($br_m_res, 0, ",",".");
                    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);

                }*/

                if ($allyid > 0 and $npc == 0) {
                    mysql_query("UPDATE de_allys SET colstolen = colstolen + '".$atter_sk[$i]."' WHERE id='$allyid'", $db);
                }
                if ($allyid > 0 and $npc == 1) {
                    mysql_query("UPDATE de_allys SET colstolennpc = colstolennpc + '".$atter_sk[$i]."' WHERE id='$allyid'", $db);
                }
                //allyaufgabe erbeute kollektoren allgemein gutschreiben
                if ($allyid > 0 and $npc == 0) {
                    mysql_query("UPDATE de_allys SET questreach = questreach + '".$atter_sk[$i]."' WHERE id='$allyid' AND questtyp=1", $db);
                }
                if ($allyid > 0 and $npc == 1) {
                    mysql_query("UPDATE de_allys SET questreach = questreach + '".$atter_sk[$i]."' WHERE id='$allyid' AND questtyp=3", $db);
                }
            } else { //die kollektoren werden vernichtet
                echo 'Kollektoren zerstört.';
                if ($allyid > 0) {
                    mysql_query("UPDATE de_allys SET coldestroy = coldestroy + '".$atter_sk[$i]."' WHERE id='$allyid'", $db);
                }
                //kollektorwert negativ gestalten um die zerstörung ersichtlich zu machen
                $atter_sk[$i] = $atter_sk[$i] * (-1);
            }
        }
        $spielerstring = $spielerstring.$atter_sk[$i].';';
        $kb_daten_spieler['colstolen'] = $atter_sk[$i];

        //erfahrungspunkte
        $spielerstring = $spielerstring.$atter_exp[$i].';';
        $kb_daten_spieler['exp'] = $atter_exp[$i];

        //kriegsartefakte
        if ($sv_kartefakt_exp_atter > 0) {
            $kartefakte = floor($atter_exp[$i] / $sv_kartefakt_exp_atter);
        } else {
            $kartefakte = 0;
        }
        $spielerstring = $spielerstring.$kartefakte.';';
        $kb_daten_spieler['kartefakt'] = $kartefakte;

        //allyaufgabe kriegsartefakte
        if ($allyid > 0 and $kartefakte > 0) {
            mysql_query("UPDATE de_allys SET questreach = questreach + '".$kartefakte."' WHERE id='$allyid' AND questtyp=5", $db);
        }

        //schlachterrecycling
        $spielerstring = $spielerstring.$atter_rec[$i][0].';'.$atter_rec[$i][1].';';
        $kb_daten_spieler['recycling1'] = $atter_rec[$i][0];
        $kb_daten_spieler['recycling2'] = $atter_rec[$i][1];

        echo '<br>Out: '.$atterstring.'<br>Out: '.$defferstring.'<br>Out: '.$spielerstring.'<br>Out: '.$datenstring;
        echo '<br>L�nge: '.strlen($atterststring.$defferstring.$spielerstring.$datenstring);

        ///////////////////////////////////////////////////////
        // Kopfgeld aussetzen
        ///////////////////////////////////////////////////////
        // generell kopfgeld aussetzen um der funktion mehr interesse zu geben
        // das Kopfgeld das ausgesetzt wird, ist für die zerstörten Punkte bei allen Deffern/dem Zielsystem
        // gilt nur für echte Spieler, nicht für NPCs
        if ($npc == 0) {
            $anteil = $atter_pkt[$i] / $atter_pktges;
            $energiewert = $deffer_pktges_zer * $anteil * 10;
            //energiewert auf unterschiedliche rohstoffe aufteilen
            $kg1 = floor($energiewert / 4);
            $kg2 = floor($energiewert / 8);
            $kg3 = floor($energiewert / 12);
            $kg4 = floor($energiewert / 16);

            //das Kopfgeld in die DB schreiben
            mysql_query("UPDATE de_user_data SET kg01=kg01+'$kg1', kg02=kg02+'$kg2', kg03=kg03+'$kg3', kg04=kg04+'$kg4' WHERE user_id='$uid'", $db);

            //für den KB die Werte vermerken
            $kb_daten_spieler['kg_set_01'] = $kg1;
            $kb_daten_spieler['kg_set_02'] = $kg2;
            $kb_daten_spieler['kg_set_03'] = $kg3;
            $kb_daten_spieler['kg_set_04'] = $kg4;

            /*
            $text ='$energiewert: '.$energiewert;
            $text.="\r\n".'$deffer_pktges_zer: '.$deffer_pktges_zer;
            //$text.="\r\n".'$deffer_pktges_schiffe: '.$deffer_pktges_schiffe;
            //$text.="\r\n".'$deffer_pktges_tuerme: '.$deffer_pktges_tuerme;

            $text.="\r\n".'$deffer_pktges_zer_schiffe: '.$deffer_pktges_zer_schiffe;
            $text.="\r\n".'$deffer_pktges_zer_tuerme: '.$deffer_pktges_zer_tuerme;

            $text.="\r\n".'$anteil: '.$anteil;
            $text.="\r\n".'Turmrasse: '.$d_userdata[0][3];

            @mail($GLOBALS['env_admin_email'], $sv_server_tag.' KG '.$uid, $text, 'FROM: '.$GLOBALS['env_admin_email']);
            */
        }


        $nachricht = $atterstring.$defferstring.$spielerstring.$datenstring;
        unset($kb_array);

        $kb_array['daten'] = $kb_daten;
        $kb_array['einheiten_atter'] = $kb_einheiten_atter;
        $kb_array['einheiten_deffer'] = $kb_einheiten_deffer;
        $kb_array['tuerme'] = $kb_tuerme;
        $kb_array['daten_spieler'] = $kb_daten_spieler;
        $kb_array['einheiten_spieler'] = $kb_einheiten_spieler;

        /*
        echo '<br>ATTERSTRING: '.$atterstring;
        echo '<br>DEFFERSTRING: '.$defferstring;
        echo '<br>SPIELERSTRING: '.$spielerstring;
        echo '<br>DATENSTRING: '.$datenstring;
        */
        //kb an den account schicken
        //mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 50,'$time','$nachricht')",$db);
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 57,'$time','".serialize($kb_array)."')", $db);

        //nachrichten und kriegsartefakte updaten
        $res1 = $atter_rec[$i][0];
        $res2 = $atter_rec[$i][1];
        mysql_query("UPDATE de_user_data set newnews = 1, kartefakt = kartefakt + '$kartefakte', restyp01 = restyp01 + '$res1', restyp02 = restyp02 + '$res2' WHERE user_id = $uid", $db);

        //und noch zum testen extra bei account 1 ablegen
        //if ($uid!=1)mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES (1, 50,'$time','$nachricht')",$db);

        //mitloggen wieviele kollektoren man von npcs holt
        if ($atter_sk[$i] > 0 and $npc == 1) {
            mysql_query("UPDATE de_user_data SET npccol = npccol + '$atter_sk[$i]' WHERE user_id = '$uid'", $db);
        }
        //mitloggen wie viele kollektoren man von jemandem holt
        if ($atter_sk[$i] > 0 or $atter_sk[$i] < 0) {
            $zeit = time();
            if ($atter_sk[$i] > 0) {
                $colstolen = $atter_sk[$i];
            } else {
                $colstolen = $atter_sk[$i] * (-1);
            }
            if ($a_userdata[$i]['spec4'] == 2) {
                $energiewert = $colstolen * $rtick * 15;
            } else {
                $energiewert = $colstolen * $rtick * 30;
            }

            $anzcol = $atter_sk[$i];
            $getexp = $atter_exp[$i];
            mysql_query("INSERT INTO de_user_getcol (user_id, zuser_id, time, colanz, energiewert, getexp) VALUES ('$uid','$zuid','$zeit','$anzcol','$energiewert', '$getexp')", $db);
        }

        //altes system nach Kollektoren
        //bei zerst�rten kollektoren bekommt man mehr kopfgeld ausgesetzt
        /*
        if($atter_sk[$i]>0)$energiewert=floor($energiewert/5);else $energiewert=floor($energiewert/2.5);

        //energiewert auf unterschiedliche rohstoffe aufteilen
        $kg1=floor($energiewert/4);
        $kg2=floor($energiewert/8);
        $kg3=floor($energiewert/12);
        $kg4=floor($energiewert/16);

        mysql_query("UPDATE de_user_data SET kg01=kg01+'$kg1', kg02=kg02+'$kg2', kg03=kg03+'$kg3', kg04=kg04+'$kg4' WHERE user_id='$uid'",$db);
        */

        ///////////////////////////////////////////////////////
        //neue flottendaten in die db schreiben
        ///////////////////////////////////////////////////////
        $erges = 0;
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $er[$s] = ($atter[$i][$s] - $atter_zer[$i][$s]);
            $erges = $erges + ($atter[$i][$s] - $atter_zer[$i][$s]);
        }
        $updateid = $a_userdata[$i][0];
        mysql_query("UPDATE de_user_fleet SET
			e81='$er[0]',
			e82='$er[1]',
			e83='$er[2]',
			e84='$er[3]',
			e85='$er[4]',
			e86='$er[5]',
			e87='$er[6]',
			e88='$er[7]',
			e89='$er[8]',
			e90='$er[9]',
			komatt=komatt+'$atter_exp[$i]' WHERE user_id='$updateid'");

        //wenn alle schiffe vernichtet wurden flotte direkt heim schicken
        if ($erges <= 0) { // wenn flotte vernichtet
            echo ' ANULLFLOTTE ';
            mysql_query("UPDATE de_user_fleet SET aktion = 0, zeit = 0, aktzeit = 0, zielsec = 0, zielsys = 0, aktzeit = 0 WHERE user_id = '$updateid'", $db);
        }
    }
    ////////////////////////////////////////////////////////////////
    //deffer heimatflotten
    ////////////////////////////////////////////////////////////////
    for ($i = 0;$i < $anz_heimatflotten;$i++) {
        //teil des spielers selbst vom kb machen
        $spielerstring = '';
        unset($kb_einheiten_spieler);
        unset($kb_daten_spieler);

        //insg
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $deffer_kbsum[0][$s] += $deffer[$i][$s];
        }

        //geblockt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            //fix f�r artefakte beim blocken
            $wert = $deffer_geb[$i][$s];
            if ($wert > $deffer[$i][$s]) {
                $wert = $deffer[$i][$s];
            }
            $deffer_kbsum[1][$s] += $wert;
        }
        //�berlebt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $deffer_kbsum[2][$s] += ($deffer[$i][$s] - $deffer_zer[$i][$s]);
        }

        //erfahrungspunkte
        $deffer_kbsum[3][1] += $deffer_exp[$i].';';

        //schlachterrecycling
        $deffer_kbsum[3][3] += $deffer_rec[$i][0];
        $deffer_kbsum[3][3] += $deffer_rec[$i][1];

        $hv = explode("-", $d_userdata[$i][0]);
        $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten

        //allyid auslesen
        $allyid = get_player_allyid($uid);

        //nachrichten und updaten
        $res1 = $deffer_rec[$i][0];
        $res2 = $deffer_rec[$i][1];
        mysql_query("UPDATE de_user_data set newnews = 1, restyp01 = restyp01 + '$res1', restyp02 = restyp02 + '$res2' WHERE user_id = $uid", $db);

        //neue flottendaten in die db schreiben
        $erges = 0;
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $er[$s] = ($deffer[$i][$s] - $deffer_zer[$i][$s]);
            $erges = $erges + ($deffer[$i][$s] - $deffer_zer[$i][$s]);
        }
        $updateid = $d_userdata[$i][0];
        if ($sv_oscar != 1) {
            mysql_query("UPDATE de_user_fleet SET
				e81='$er[0]',
				e82='$er[1]',
				e83='$er[2]',
				e84='$er[3]',
				e85='$er[4]',
				e86='$er[5]',
				e87='$er[6]',
				e88='$er[7]',
				e89='$er[8]',
				e90='$er[9]',
				komdef=komdef+'$deffer_exp[$i]' WHERE user_id='$updateid'");
        }

        //wenn alle schiffe vernichtet wurden flotte direkt heim schicken
        /*
        if($erges<=0){
            // wenn flotte vernichtet
            echo ' ANULLFLOTTE ';
            mysql_query("UPDATE de_user_fleet SET aktion = 0, zeit = 0, aktzeit = 0,
            zielsec = 0, zielsys = 0, aktzeit = 0 WHERE user_id = '$updateid'",$db);
        }
        */

        //wenn die letzte heimatflotte durchlaufen wurde, dann den kb f�r das angegriffe system erstellen
        if ($i == $anz_heimatflotten - 1) {
            //insg
            for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                //$spielerstring=$spielerstring.$deffer_kbsum[0][$s].';';
                $kb_einheiten_spieler[0][$s] = $deffer_kbsum[0][$s];
            }

            //geblockt
            for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                //$spielerstring=$spielerstring.$deffer_kbsum[1][$s].';';
                $kb_einheiten_spieler[1][$s] = $deffer_kbsum[1][$s];
            }

            //�berlebt
            for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                //$spielerstring=$spielerstring.$deffer_kbsum[2][$s].';';
                $kb_einheiten_spieler[2][$s] = $deffer_kbsum[0][$s] - $deffer_kbsum[2][$s];
            }

            //anzahl der kollies die der spieler bekommen hat
            //$spielerstring=$spielerstring.'0;';//'-1;';

            //erfahrungspunkte
            //$spielerstring=$spielerstring.($deffer_kbsum[3][1]+$defenseexp).';';

            //kriegsartefakte f�r die heimatflotten / verteidigungsanlagen berechnen
            if ($sv_kartefakt_exp_deffer > 0) {
                $kartefakte = floor(($deffer_kbsum[3][1] + $defenseexp) / $sv_kartefakt_exp_deffer);
            } else {
                $kartefakte = 0;
            }
            $deffer_kbsum[3][2] = $kartefakte;
            if ($sv_oscar == 1) {
                $kb_daten_spieler['kartefakt'] = 0;
                $kb_daten_spieler['exp'] = 0;
            } else {
                $kb_daten_spieler['kartefakt'] = $kartefakte;
                $kb_daten_spieler['exp'] = $deffer_kbsum[3][1] + $defenseexp;
            }

            //allyaufgabe kriegsartefakte
            if ($sv_oscar != 1) {
                if ($allyid > 0 and $kartefakte > 0) {
                    mysql_query("UPDATE de_allys SET questreach = questreach + '".$kartefakte."' WHERE id='$allyid' AND questtyp=5", $db);
                }
            }

            //kriegsartefakte updaten
            if ($sv_oscar != 1) {
                mysql_query("UPDATE de_user_data SET kartefakt = kartefakt + '$kartefakte' WHERE user_id = $uid", $db);
            }

            //kriegsartefakte
            //$spielerstring=$spielerstring.$deffer_kbsum[3][2].';';

            //schlachterrecycling
            $spielerstring = $spielerstring.$deffer_kbsum[3][3].';'.$deffer_kbsum[3][4].';';

            echo '<br>Out: '.$atterstring.'<br>Out: '.$defferstring.'<br>Out: '.$spielerstring.'<br>Out: '.$datenstring;
            echo '<br>L�nge: '.strlen($defferststring.$defferstring.$spielerstring.$datenstring);

            //$nachricht=$atterstring.$defferstring.$spielerstring.$datenstring;
            unset($kb_array);

            $kb_array['daten'] = $kb_daten;
            $kb_array['einheiten_atter'] = $kb_einheiten_atter;
            $kb_array['einheiten_deffer'] = $kb_einheiten_deffer;
            $kb_array['tuerme'] = $kb_tuerme;
            $kb_array['daten_spieler'] = $kb_daten_spieler;
            $kb_array['einheiten_spieler'] = $kb_einheiten_spieler;

            /*
            echo '<br>ATTERSTRING: '.$atterstring;
            echo '<br>DEFFERSTRING: '.$defferstring;
            echo '<br>SPIELERSTRING: '.$spielerstring;
            echo '<br>DATENSTRING: '.$datenstring;
            */
            //kb an den account schicken
            //mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 50,'$time','$nachricht')",$db);
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 57,'$time','".serialize($kb_array)."')", $db);

            echo 'SQL-HEIMDEFFER: '."INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 57,'$time','".serialize($kb_array)."')";

            //und noch zum testen extra bei account 1 ablegen
            //if ($uid!=1)mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES (1, 50,'$time','$nachricht')",$db);
        }
    }

    ////////////////////////////////////////////////////////////////
    //deffer die zur hilfe gekommen sind
    ////////////////////////////////////////////////////////////////
    for ($i = $anz_heimatflotten;$i < $anz_deffer;$i++) {
        //teil des spielers selbst vom kb machen
        $spielerstring = '';
        unset($kb_einheiten_spieler);
        unset($kb_daten_spieler);

        //insg
        $kbdefinsg = 0;

        //deffer
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $spielerstring = $spielerstring.$deffer[$i][$s].';';
            $kb_einheiten_spieler[0][$s] = $deffer[$i][$s];
        }
        //if ($kbdefinsg>0 OR $i==0)
        //{

        //geblockt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $spielerstring = $spielerstring.$deffer_geb[$i][$s].';';
            $kb_einheiten_spieler[1][$s] = $deffer_geb[$i][$s];
        }

        //�berlebt
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $spielerstring = $spielerstring.($deffer[$i][$s] - $deffer_zer[$i][$s]).';';
            $kb_einheiten_spieler[2][$s] = $deffer_zer[$i][$s];
        }

        //anzahl der kollies die der spieler bekommen hat
        $spielerstring = $spielerstring.'0;';//'-1;';

        //erfahrungspunkte
        $spielerstring = $spielerstring.$deffer_exp[$i].';';

        //kriegsartefakte
        if ($sv_kartefakt_exp_deffer > 0) {
            $kartefakte = floor($deffer_exp[$i] / $sv_kartefakt_exp_deffer);
        } else {
            $kartefakte = 0;
        }
        $spielerstring = $spielerstring.$kartefakte.';';
        $kb_daten_spieler['kartefakt'] = $kartefakte;
        $kb_daten_spieler['exp'] = $deffer_exp[$i];

        //schlachterrecycling
        $spielerstring = $spielerstring.$deffer_rec[$i][0].';'.$deffer_rec[$i][1].';';
        $kb_daten_spieler['recycling1'] = $deffer_rec[$i][0];
        $kb_daten_spieler['recycling2'] = $deffer_rec[$i][1];


        echo '<br>Out: '.$atterstring.'<br>Out: '.$defferstring.'<br>Out: '.$spielerstring.'<br>Out: '.$datenstring;
        echo '<br>Länge: '.strlen($defferststring.$defferstring.$spielerstring.$datenstring);

        $hv = explode("-", $d_userdata[$i][0]);
        $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten

        //allyid auslesen
        $allyid = get_player_allyid($uid);

        //allyaufgabe kriegsartefakte
        if ($allyid > 0 and $kartefakte > 0) {
            mysql_query("UPDATE de_allys SET questreach = questreach + '".$kartefakte."' WHERE id='$allyid' AND questtyp=5", $db);
        }

        $nachricht = $atterstring.$defferstring.$spielerstring.$datenstring;
        unset($kb_array);

        $kb_array['daten'] = $kb_daten;
        $kb_array['einheiten_atter'] = $kb_einheiten_atter;
        $kb_array['einheiten_deffer'] = $kb_einheiten_deffer;
        $kb_array['tuerme'] = $kb_tuerme;
        $kb_array['daten_spieler'] = $kb_daten_spieler;
        $kb_array['einheiten_spieler'] = $kb_einheiten_spieler;

        /*
        echo '<br>ATTERSTRING: '.$atterstring;
        echo '<br>DEFFERSTRING: '.$defferstring;
        echo '<br>SPIELERSTRING: '.$spielerstring;
        echo '<br>DATENSTRING: '.$datenstring;
        */
        //kb an den account schicken
        //mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 50,'$time','$nachricht')",$db);
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 57,'$time','".serialize($kb_array)."')", $db);

        //nachrichten und kriegsartefakte updaten
        $res1 = $deffer_rec[$i][0];
        $res2 = $deffer_rec[$i][1];
        mysql_query("UPDATE de_user_data set newnews = 1, kartefakt = kartefakt + '$kartefakte', restyp01 = restyp01 + '$res1', restyp02 = restyp02 + '$res2' WHERE user_id = $uid", $db);

        //und noch zum testen extra bei account 1 ablegen
        //if ($uid!=1)mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES (1, 50,'$time','$nachricht')",$db);

        //neue flottendaten in die db schreiben
        $erges = 0;
        for ($s = 0;$s < $sv_anz_schiffe;$s++) {
            $er[$s] = ($deffer[$i][$s] - $deffer_zer[$i][$s]);
            $erges = $erges + ($deffer[$i][$s] - $deffer_zer[$i][$s]);
        }
        $updateid = $d_userdata[$i][0];
        mysql_query("UPDATE de_user_fleet SET
			e81='$er[0]',
			e82='$er[1]',
			e83='$er[2]',
			e84='$er[3]',
			e85='$er[4]',
			e86='$er[5]',
			e87='$er[6]',
			e88='$er[7]',
			e89='$er[8]',
			e90='$er[9]',
			komdef=komdef+'$deffer_exp[$i]' WHERE user_id='$updateid'");

        //wenn alle schiffe vernichtet wurden flotte direkt heim schicken
        if ($erges <= 0) { // wenn flotte vernichtet
            echo ' ANULLFLOTTE ';
            mysql_query("UPDATE de_user_fleet SET aktion = 0, zeit = 0, aktzeit = 0,
		  zielsec = 0, zielsys = 0, aktzeit = 0 WHERE user_id = '$updateid'", $db);
        }
        //}
    }


    //alle kampfberichte wurden generiert

    /////////////////////////////////////////////////////////////
    //t�rme updaten
    /////////////////////////////////////////////////////////////
    $erges = 0;
    for ($s = 0;$s < $sv_anz_tuerme;$s++) {
        $er[$s] = ($turm[$s] - $deffertuerme_zer[$s]);
    }
    if ($sv_oscar != 1) {
        mysql_query("UPDATE de_user_data SET
		 e100 = '$er[0]',
		 e101 = '$er[1]',
		 e102 = '$er[2]',
		 e103 = '$er[3]',
		 e104 = '$er[4]' WHERE sector = '$zsec' AND `system` = '$zsys'", $db);
    }

    ////////////////////////////////////////////////////////////
    //recyclotron
    ////////////////////////////////////////////////////////////
    if ($sv_oscar == 1) {
        $nachricht = $kt_lang['recyclotronertrag'].': alle verlorenen Schiffe/T&uuml;rme';

        $time = strftime("%Y%m%d%H%M%S");
        $hv = explode("-", $d_userdata[0][0]);
        $uid = $hv[0];

        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 55,'$time','$nachricht')", $db);
    }

    if ($rec == 1) {
        $time = strftime("%Y%m%d%H%M%S");
        $hv = explode("-", $d_userdata[0][0]);
        $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten

        if ($sv_oscar != 1) {
            $restyp01 = 0;
            $restyp02 = 0;
            $restyp03 = 0;
            $restyp04 = 0;
            $restyp05 = 0;
            $rasse = $d_userdata[0][3] - 1;
            if ($whg == 1) {
                $anteil = $sv_recyclotron_bonus_whg;
            } else {
                $anteil = $sv_recyclotron_bonus;
            }
            //schauen ob er evtl. recarion-artefakte hat, welche das recyclortorn verst�rken
            //userartefakte auslesen
            $db_daten = mysql_query("SELECT id, level FROM de_user_artefact WHERE id=10 AND user_id='$uid'", $db);
            $recbonus = 0;
            while ($row = mysql_fetch_array($db_daten)) {
                $recbonus = $recbonus + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
            }
            //artefaktbonus draufaddieren
            $anteil = $anteil + $recbonus - $recmalus;

            //sektorbonus draufaddieren
            if ($npc == 1) {
                $rec_secbonus = 0;
                echo 'secrecbonuns1: '.$rec_secbonus;
            } else {
                //zuerst anzahl der pc-sektoren auslesen
                $db_daten = mysql_query("SELECT sec_id FROM de_sector WHERE npc=0 AND platz>0", $db);
                $num = mysql_num_rows($db_daten);
                if ($num < 1) {
                    $num = 1;
                }
                echo 'num: '.$num;
                //eigenen sektorplatz auslesen
                $db_daten = mysql_query("SELECT platz FROM de_sector WHERE sec_id='$zsec'", $db);
                $row = mysql_fetch_array($db_daten);
                $ownsectorplatz = $row["platz"];

                echo 'ownsectorplatz: '.$ownsectorplatz;

                //recyclotronbonus berechnen
                $rec_secbonus = $sv_recyclotron_sector_bonus / $num * ($ownsectorplatz - 1);
                //recyclotronbonus darf nicht gr��er als das maximum sein
                if ($rec_secbonus > $sv_recyclotron_sector_bonus) {
                    $rec_secbonus = $sv_recyclotron_sector_bonus;
                }
                echo 'secrecbonuns2: '.$rec_secbonus;
            }

            $anteil = $anteil + $rec_secbonus;

            //spezialisierung sektorbonus recycling
            $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zsec' AND spec4=3", $db);
            $specrecbonus = mysql_num_rows($db_daten) * 1;
            if ($specrecbonus > 10) {
                $specrecbonus = 10;
            }
            $anteil += $specrecbonus;

            //h�chst/minamal-grenze �berpr�fen
            if ($anteil < $sv_recyclotron_min) {
                $anteil = $sv_recyclotron_min;
            }
            if ($anteil > $sv_recyclotron_max) {
                $anteil = $sv_recyclotron_max;
            }

            //schiffe
            for ($i = 0; $i < $anz_heimatflotten;$i++) {
                for ($s = 0;$s < $sv_anz_schiffe;$s++) {
                    $restyp01 += ($deffer_zer[$i][$s] * $unit[$rasse][$s][5][0]);
                    $restyp02 += ($deffer_zer[$i][$s] * $unit[$rasse][$s][5][1]);
                    $restyp03 += ($deffer_zer[$i][$s] * $unit[$rasse][$s][5][2]);
                    $restyp04 += ($deffer_zer[$i][$s] * $unit[$rasse][$s][5][3]);
                    $restyp05 += ($deffer_zer[$i][$s] * $unit[$rasse][$s][5][4]);
                }
            }

            //t�rme
            for ($s = $sv_anz_schiffe;$s < $sv_anz_schiffe + $sv_anz_tuerme;$s++) {
                $restyp01 += ($deffertuerme_zer[$s - $sv_anz_schiffe] * $unit[$rasse][$s][5][0]);
                $restyp02 += ($deffertuerme_zer[$s - $sv_anz_schiffe] * $unit[$rasse][$s][5][1]);
                $restyp03 += ($deffertuerme_zer[$s - $sv_anz_schiffe] * $unit[$rasse][$s][5][2]);
                $restyp04 += ($deffertuerme_zer[$s - $sv_anz_schiffe] * $unit[$rasse][$s][5][3]);
                $restyp05 += ($deffertuerme_zer[$s - $sv_anz_schiffe] * $unit[$rasse][$s][5][4]);
            }

            $restyp01 = floor($restyp01 / 100 * $anteil);
            $restyp02 = floor($restyp02 / 100 * $anteil);
            $restyp03 = floor($restyp03 / 100 * $anteil);
            $restyp04 = floor($restyp04 / 100 * $anteil);
            $restyp05 = floor($restyp05 / 100 * $anteil);

            mysql_query("UPDATE de_user_data SET
			 restyp01 = restyp01 + '$restyp01',
			 restyp02 = restyp02 + '$restyp02',
			 restyp03 = restyp03 + '$restyp03',
			 restyp04 = restyp04 + '$restyp04',
			 restyp05 = restyp05 + '$restyp05' WHERE sector = '$zsec' AND `system` = '$zsys'", $db);

            //eine nachricht an den account schicken

            $nachricht = $kt_lang['recyclotronertrag'].': '.number_format($restyp01, 0, "", ".").' M -- '.number_format($restyp02, 0, "", ".").
             ' D -- '.number_format($restyp03, 0, "", ".").' I -- '.number_format($restyp04, 0, "", ".").' E  -- '.number_format($restyp05, 0, "", ".").' T'.
             '<br>'.$kt_lang['wirkungsgrad'].': '.number_format($anteil, 2, ",", ".").'%';

            echo '<br>recycler->'.$nachricht.' A:'.$anteil.'<br>';

            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 55,'$time','$nachricht')", $db);
        }
    }//recyclotron ende


    // variablen nach jedem durchlauf resetten
    unset($a_userdata);
    unset($d_userdata);
    unset($atterrassen);
    unset($defferrassen);
    unset($atter_hpges);
    unset($deffer_hpges);
    unset($atter_awges);
    unset($deffer_awges);
    unset($atter_bwges);
    unset($deffer_bwges);
    unset($atter);
    unset($atter_geb);
    unset($atter_zer);
    unset($atter_exp);
    unset($atter_sk);
    unset($atter_rec);
    unset($atter_whg);
    unset($deffer);
    unset($deffer_geb);
    unset($deffer_zer);
    unset($deffer_exp);
    unset($atter_pkt);
    unset($atter_sk);
    unset($deffer_rec);
    unset($deffer_whg);
    unset($atterrassen_geb);
    unset($atterrassen_zer);
    unset($defferrassen_geb);
    unset($defferrassen_zer);
    unset($komatt);
    unset($komdef);
    unset($rassenvorhanden);
    unset($anteil);
    unset($deffertuerme_zer);
    unset($deffertuerme_geb);
    unset($deffer_kbsum);
}
