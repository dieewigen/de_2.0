<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "inc/artefakt.inc.php";
include 'inc/lang/'.$sv_server_lang.'_resource.lang.php';
include 'inc/sabotage.inc.php';
include "functions.php";
include "issectork.php";

//$db_daten=mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'",$db);
//$row = mysql_fetch_array($db_daten);
$pt = loadPlayerTechs($_SESSION['ums_user_id']);
$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$col = $row["col"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$eartefakt = $row["eartefakt"];
$kartefakt = $row["kartefakt"];
$dartefakt = $row["dartefakt"];
$palenium = $row["palenium"];
$useefta = $row["useefta"];
$mysc1 = $row["sc1"];
$agent_lost = $row['agent_lost'];

if ($row["status"] == 1) {
    $ownally = $row["allytag"];
}

$gr01 = $restyp01;
$gr02 = $restyp02;
$gr03 = $restyp03;
$gr04 = $restyp04;

//maximalen tick auslesen
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1", $db);
$rowx     = mysql_fetch_array($result);
$maxtick = $rowx["tick"];

//spezialisierung bzgl. der baukostenreduzierung �berpr�fen
$db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND spec1=3;", $db);
$baukostenreduzierung = mysql_num_rows($db_daten) * 2;
if ($baukostenreduzierung > 20) {
    $baukostenreduzierung = 20;
}
$baukostenreduzierung = $baukostenreduzierung / 100;

//spezialisierung bzgl. des erh�hten planetaren ertrages
$db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND spec3=3;", $db);
$planertragbonus = mysql_num_rows($db_daten) * 10;
if ($planertragbonus > 100) {
    $planertragbonus = 100;
}
$planertragbonus = $planertragbonus / 100;


//schauen welche sektorartefakte in dem sektor sind
$sartefakt = 0;
$sa_grund = array(0,0,0,0);
$result = mysql_query("SELECT id FROM de_artefakt WHERE sector = '$sector'", $db);
while ($row2 = mysql_fetch_array($result)) { //jeder gefundene datensatz wird geprueft
    $sartefakt = $sartefakt + $sv_artefakt[$row2["id"] - 1][0];
    $sa_grund[0] = $sa_grund[0] + $sv_artefakt[$row2["id"] - 1][1];
    $sa_grund[1] = $sa_grund[1] + $sv_artefakt[$row2["id"] - 1][2];
    $sa_grund[2] = $sa_grund[2] + $sv_artefakt[$row2["id"] - 1][3];
    $sa_grund[3] = $sa_grund[3] + $sv_artefakt[$row2["id"] - 1][4];
}

////////////////////////////////////////////////
//grundertrag
////////////////////////////////////////////////
//grundertragbonus f�r die BR, gibt es nie in der Ewigen Runde
if (($maxtick > 2500000 && $sv_ewige_runde != 1 && $sv_hardcore != 1) || ($sv_comserver == 1 && (isset($sv_comserver_roundtyp) && $sv_comserver_roundtyp == 1))) {
    $grundertragmultiplikator = 200;
} else {
    $grundertragmultiplikator = 1;
}

if (!hasTech($pt, 4)) {//keine gilde
    $grundm = $sv_plan_grundertrag[0] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grundd = $sv_plan_grundertrag[1] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grundi = $sv_plan_grundertrag[2] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grunde = $sv_plan_grundertrag[3] * $grundertragmultiplikator * (1 + $planertragbonus);

    //bonus durch spezialisierung
    $spezim = $sv_plan_grundertrag[0] * $grundertragmultiplikator * $planertragbonus;
    $spezid = $sv_plan_grundertrag[1] * $grundertragmultiplikator * $planertragbonus;
    $spezii = $sv_plan_grundertrag[2] * $grundertragmultiplikator * $planertragbonus;
    $spezie = $sv_plan_grundertrag[3] * $grundertragmultiplikator * $planertragbonus;
} else {  //mit gilde
    $grundm = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grundd = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grundi = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * (1 + $planertragbonus);
    $grunde = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * (1 + $planertragbonus);

    //bonus durch spezialisierung
    $spezim = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * $planertragbonus;
    $spezid = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * $planertragbonus;
    $spezii = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * $planertragbonus;
    $spezie = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * $planertragbonus;
}


//planetarer grundertrag durch zollkontrolleure
$zollm = floor($agent_lost * $sv_zoellnerertrag[0]);
$zolld = floor($agent_lost * $sv_zoellnerertrag[1]);
$zolli = floor($agent_lost * $sv_zoellnerertrag[2]);
$zolle = floor($agent_lost * $sv_zoellnerertrag[3]);

//ekey aufsplitten
$hv = explode(";", $row["ekey"]);
if ($hv[0] == '') {
    $hv[0] = 0;
}
if ($hv[1] == '') {
    $hv[1] = 0;
}
if ($hv[2] == '') {
    $hv[2] = 0;
}
if ($hv[3] == '') {
    $hv[3] = 0;
}
$keym = $hv[0];
$keyd = $hv[1];
$keyi = $hv[2];
$keye = $hv[3];

//anzahl der kollektoren, die im bau sind ermitteln
$anzahl = 0;
$result = mysql_query("SELECT anzahl FROM de_user_build WHERE user_id = '$ums_user_id' AND tech_id=80", $db);
while ($row2 = mysql_fetch_array($result)) { //jeder gefundene datensatz wird geprueft
    $anzahl = $anzahl + $row2["anzahl"];
}
$colanz = $anzahl + $col;



/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//  rohstoffhandel
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//Grundsteuersatz
$handelssteuersatz = 50;
//Bonus durch Allianzgebäude

$ally_has_notfallkonverter = false;

//allydaten laden
$db_daten = mysql_query("SELECT * FROM de_allys WHERE allytag='$ownally'", $db);
$row = mysql_fetch_array($db_daten);
$num = mysql_num_rows($db_daten);
if ($num == 1) {
    $allyid = $row['id'];

    $db_daten = mysql_query("SELECT * FROM de_allys WHERE id='$allyid'", $db);
    $num = mysql_num_rows($db_daten);
    if ($num == 1) {
        $row = mysql_fetch_array($db_daten);

        if ($row['bldg6'] > 0) {
            $ally_has_notfallkonverter = true;
            $handelssteuersatz -= $row['bldg6'];
        }
    }
}

//Tausch durchführen
if (intval($_REQUEST['rh_amount'] ?? 0) > 0 && intval($_REQUEST['rh_cost'] > 0) && hasTech($pt, 4) && $ally_has_notfallkonverter) {
    //transaktionsbeginn
    if (setLock($ums_user_id)) {
        //zielrohstoff auslesen
        $res_target = intval($_REQUEST['rh_v1']);
        if ($res_target < 1 || $res_target > 4) {
            $res_target = 1;
        }

        //quellrohstoff auslesen
        $res_source = intval($_REQUEST['rh_v2']);
        if ($res_source < 1 || $res_source > 4) {
            $res_source = 1;
        }

        //menge die man bezahlt
        $res_cost = intval($_REQUEST['rh_cost']);

        //test ob quelle und ziel unterschiedlich sind
        if ($res_target != $res_source) {
            //aktuellen rohstoffstand auslesen
            $db_daten = mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'", $db);
            $row = mysql_fetch_array($db_daten);
            $hasres[1] = $row['restyp01'];
            $hasres[2] = $row['restyp02'];
            $hasres[3] = $row['restyp03'];
            $hasres[4] = $row['restyp04'];
            $hasres[5] = $row['restyp05'];

            //reichen die rohstoffe die man hat aus?
            if ($res_cost > $hasres[$res_source]) {
                $res_cost = $hasres[$res_source];
            }

            if ($res_cost > 0) {
                $uv = array(1,2,3,4,10000);
                $resnames = array('Multiplex', 'Dyharra', 'Iradium', 'Eternium', 'Tronic');

                //berechnen wie viel rohstoffe man bekommt
                //steuer berechnen
                //$steueranteil=$res_cost-($res_cost*100/(100+$handelssteuersatz+$sektorsteuersatz));

                //sektorsteuersatz auslesen
                $db_daten = mysql_query("SELECT ssteuer FROM de_sector WHERE sec_id='$sector'", $db);
                $sektorsteuersatz = mysql_result($db_daten, 0, 0);

                $steueranteil = $res_cost / 100 * ($handelssteuersatz + $sektorsteuersatz);

                $steueranteil_sektor = $steueranteil * $sektorsteuersatz / ($handelssteuersatz + $sektorsteuersatz);

                $res_get = ($res_cost - $steueranteil) * $uv[$res_source - 1];
                $res_get = $res_get / $uv[$res_target - 1];

                //bei tronic als ziel immer abrunden
                if ($res_target == 5) {
                    $res_get = floor($res_get);
                }

                if ($res_get > 0) {
                    $trademsg = 'Gewinn: '.number_format($res_get, 0, ",", ".").' '.$resnames[$res_target - 1].'<br>
					Verlust: '.number_format($res_cost, 0, ",", ".").' '.$resnames[$res_source - 1];

                    //sektorsteuer in der sektorkasse gutschreiben
                    mysql_query("UPDATE de_sector SET restyp0$res_source=restyp0$res_source+$steueranteil_sektor WHERE sec_id='$sector'", $db);

                    //rohstoffe und sektorspende gutschreiben
                    mysql_query("UPDATE de_user_data SET 
					restyp0$res_source=restyp0$res_source-$res_cost, 
					restyp0$res_target=restyp0$res_target+$res_get,  
					spend0$res_source=spend0$res_source+$steueranteil_sektor WHERE user_id='$ums_user_id'", $db);


                    //aktuellen rohstoffwert für die resline auslesen
                    $db_daten = mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'", $db);
                    $row = mysql_fetch_array($db_daten);
                    $restyp01 = $row['restyp01'];
                    $restyp02 = $row['restyp02'];
                    $restyp03 = $row['restyp03'];
                    $restyp04 = $row['restyp04'];
                    $restyp05 = $row['restyp05'];
                } else {
                    $fehlermsg = 'Du bezahlst nicht genug.';
                }
            }
        } else {
            $fehlermsg = 'Die Rohstoffarten d&uuml;rfen nicht gleich sein.';
        }

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            echo 'Fehler bei der Transaktion.';
        }
    }// if setlock-ende
    else {
        echo 'Fehler bei der Transaktion.';
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $resource_lang['ressourcen']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

////////////////////////////////////////////////////////////
// Sektorkasse/Sektorkosten
////////////////////////////////////////////////////////////
//Kostenfaktor
$avg_player = getAveragePlayerAmountInSectorOnServer();
$kostenfaktor = 10 - $avg_player;


//sektorgebäudekosten auslesen
$btipstr = '<table width=500px border=0 cellpadding=0 cellspacing=1><tr align=center><td>&nbsp;</td><td>M</td><td>D</td><td>I</td><td>E</td><td>T</td><tr>';
//gebäude
$db_daten = mysql_query("SELECT  tech_name, restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_tech_data1 WHERE tech_id>119 AND tech_id<130 ORDER BY tech_id", $db);
while ($row = mysql_fetch_array($db_daten)) {
    $btipstr .= '<tr align=center>';
    $btipstr .= '<td align=left>'.$row[0].'</td>';
    $btipstr .= '<td>'.number_format($row[1] / $kostenfaktor, 0, ",", ".").'</td>';
    $btipstr .= '<td>'.number_format($row[2] / $kostenfaktor, 0, ",", ".").'</td>';
    $btipstr .= '<td>'.number_format($row[3] / $kostenfaktor, 0, ",", ".").'</td>';
    $btipstr .= '<td>'.number_format($row[4] / $kostenfaktor, 0, ",", ".").'</td>';
    $btipstr .= '<td>'.number_format($row[5] / $kostenfaktor, 0, ",", ".").'</td>';
    $btipstr .= '</tr>';
}

//raumschiff
$btipstr .= '<tr align=center>';
$btipstr .= '<td align=left>'.$resource_lang['sektorraumschiff'].'</td>';
$btipstr .= '<td>'.number_format(2000, 0, ",", ".").'</td>';
$btipstr .= '<td>'.number_format(500, 0, ",", ".").'</td>';
$btipstr .= '<td>'.number_format(500, 0, ",", ".").'</td>';
$btipstr .= '<td>'.number_format(2000, 0, ",", ".").'</td>';
$btipstr .= '<td>'.number_format(0, 0, ",", ".").'</td>';
$btipstr .= '</tr>';

$btipstr .= '</table>';

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// den verteilungsschlüssel ändern
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
$fehlermsg = '';

$e_t1 = intval($_POST["e_t1"] ?? 0);
$e_t2 = intval($_POST["e_t2"] ?? 0);
$e_t3 = intval($_POST["e_t3"] ?? 0);
$e_t4 = intval($_POST["e_t4"] ?? 0);
if (!empty($e_t1) || !empty($e_t2) || !empty($e_t3) || !empty($e_t4)) {
    if (validDigit($e_t1) && validDigit($e_t2) && validDigit($e_t3) && validDigit($e_t4)) {
        if (($e_t1 + $e_t2 + $e_t3 + $e_t4) <= 100) {  //key ist ok und wird aktualisiert
            $newkey = $e_t1.";".$e_t2.";".$e_t3.";".$e_t4;

            //wenn key kleiner als 100 dann warnung ausgeben
            if (($e_t1 + $e_t2 + $e_t3 + $e_t4) < 100) {
                $fehlermsg = $resource_lang['reswarnung'];
            } else {
                $keym = $e_t1;
                $keyd = $e_t2;
                $keyi = $e_t3;
                $keye = $e_t4;
                mysql_query("UPDATE de_user_data SET ekey = '$newkey' WHERE user_id = '$ums_user_id'", $db);
                //keys aktualisieren
                $hv = explode(";", $newkey);
                $keym = $hv[0];
                $keyd = $hv[1];
                $keyi = $hv[2];
                $keye = $hv[3];
            }
        } else {
            $fehlermsg = $resource_lang['resfehler'];
        }
    }
    if ($keym == '') {
        $keym = 0;
    }
    if ($keyd == '') {
        $keyd = 0;
    }
    if ($keyi == '') {
        $keyi = 0;
    }
    if ($keye == '') {
        $keye = 0;
    }
}

//wenn efta in benutzung ist noch den malus verrechnen
//if($useefta==1)$malus=$sv_efta_col_malus;else $malus=0;
$malus = 0;
$sabotagemalus = 0;
//sabotagemalus
if ($maxtick < $mysc1 + $sv_sabotage[7][0] and $mysc1 > $sv_sabotage[7][0]) {
    $sabotagemalus += $sv_sabotage[7][2];
}

//gesamtenergie pro tick, energieausbeute
/*
if($ums_premium==0){
    $ea=$col*($sv_kollieertrag-$malus-$sabotagemalus);
}else{
    $ea=$col*($sv_kollieertrag_pa-$malus-$sabotagemalus);
}
*/

$ea = $col * ($sv_kollieertrag - $malus - $sabotagemalus);

//eftaartefakt
//$eartefaktenergie=floor($ea/10000*$eartefakt);
$eartefaktenergie = $sv_eftaartefaktertrag * $eartefakt;

//kriegsartefakt
//$kartefaktenergie=floor($ea/1000*$kartefakt);
$kartefaktenergie = $sv_kriegsartefaktertrag * $kartefakt;

$dartefaktenergie = floor($ea / 100 * $dartefakt);
$sartefaktenergie = floor($ea / 100 * $sartefakt);
$paleniumenergie = floor($ea / 10000 * $palenium);

//bei einer Hyperraumtunneletablierung geben die Kollies keine Energie
// (Artefakte wie die Gabe der Reichen aber schon, weil zu diesem Zeitpunkt der Hyperraumtunnel schon fertig ist)
$sql = "SELECT * FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>'".time()."' LIMIT 1";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
$num = mysqli_num_rows($db_daten);
if ($num > 0) {
    $ea = 0;
}

//maximale anzahl von kollektoren auslesen
$db_daten = mysql_query("SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0", $db);
$row = mysql_fetch_array($db_daten);
$maxcol = $row['maxcol'];

$adebonus = 0;


$eages = $ea + $eartefaktenergie + $kartefaktenergie + $dartefaktenergie + $sartefaktenergie + $paleniumenergie + $adebonus;

//energieinput pro rohstoff
$em = floor($eages / 100 * $keym);
$ed = floor($eages / 100 * $keyd);
$ei = floor($eages / 100 * $keyi);
$ee = floor($eages / 100 * $keye);

//energie->materie verhaeltnis
if (hasTech($pt, 18)) {
    $emvm = 1;
} else {
    $emvm = 2;
}
if (hasTech($pt, 19)) {
    $emvd = 2;
} else {
    $emvd = 4;
}
if (hasTech($pt, 20)) {
    $emvi = 3;
} else {
    $emvi = 6;
}
if (hasTech($pt, 21)) {
    $emve = 4;
} else {
    $emve = 8;
}

//rohstoffoutput
$rm = ceil($em / $emvm);
$rd = ceil($ed / $emvd);
$ri = ceil($ei / $emvi);
$re = ceil($ee / $emve);

//falls es keine materieumwandler gibt, erh�lt man keine res
if (!hasTech($pt, 14)) {
    $rm = 0;
}
if (!hasTech($pt, 15)) {
    $rd = 0;
}
if (!hasTech($pt, 16)) {
    $ri = 0;
}
if (!hasTech($pt, 17)) {
    $re = 0;
}


///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// rohstoffe spenden
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
if (isset($_POST["mtr"]) || isset($_POST["dtr"]) || isset($_POST["itr"]) || isset($_POST["etr"]) || isset($_POST["ttr"])) {
    $mtr = intval($_POST["mtr"]);
    $dtr = intval($_POST["dtr"]);
    $itr = intval($_POST["itr"]);
    $etr = intval($_POST["etr"]);
    $ttr = intval($_POST["ttr"]);

    //transaktionsbeginn
    if (setLock($ums_user_id)) {

        if (validDigit($mtr) && validDigit($dtr) && validDigit($itr) && validDigit($etr) && validDigit($ttr)) {//alle werte sind ok
            //hat man auch soviele rohstoffe?
            if ($mtr > $restyp01) {
                $mtr = (int)$restyp01;
            }
            if ($dtr > $restyp02) {
                $dtr = (int)$restyp02;
            }
            if ($itr > $restyp03) {
                $itr = (int)$restyp03;
            }
            if ($etr > $restyp04) {
                $etr = (int)$restyp04;
            }
            if ($ttr > $restyp05) {
                $ttr = (int)$restyp05;
            }

            if ($mtr >= 0 && $dtr >= 0 && $itr >= 0 && $etr >= 0 && $ttr >= 0) {

                //rohstofftransfer
                mysql_query("UPDATE de_user_data set restyp01 = restyp01 - $mtr, restyp02 = restyp02 - $dtr,
				restyp03 = restyp03 - $itr, restyp04 = restyp04 - $etr, restyp05 = restyp05 - $ttr,
				spend01 = spend01 + $mtr, spend02 = spend02 + $dtr, spend03 = spend03 + $itr,
				spend04 = spend04 + $etr, spend05 = spend05 + $ttr WHERE user_id = '$ums_user_id'", $db);

                mysql_query("UPDATE de_sector set restyp01 = restyp01 + $mtr, restyp02 = restyp02 + $dtr, restyp03 = restyp03 + $itr, restyp04 = restyp04 + $etr, restyp05 = restyp05 + $ttr WHERE sec_id = '$sector'", $db);
                $restyp01 = $restyp01 - $mtr;
                $restyp02 = $restyp02 - $dtr;
                $restyp03 = $restyp03 - $itr;
                $restyp04 = $restyp04 - $etr;
                $restyp05 = $restyp05 - $ttr;
                //an den bk ne info schicken
                ///zuerst schauen wer bk ist
                //$db_daten=mysql_query("SELECT bk FROM de_sector WHERE sec_id='$sector'",$db);
                //$bk=mysql_result($db_daten, 0,0);
                $bk = getSKSystemBySecID($sector);

                if ($bk > 0) { //bk vorhanden, dann dessen daten raussuchen und nachricht einf&uuml;gen
                    $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' and system='$bk'", $db);
                    $anz = mysql_num_rows($db_daten);
                    if ($anz > 0) {//bk-system ist auch besetzt
                        $uid = mysql_result($db_daten, 0, 0);
                        $time = date("YmdHis");
                        $nachricht = $resource_lang['sekeinzahlung'].$ums_spielername.': '.number_format($mtr, 0, "", ".").' M -- '.number_format($dtr, 0, "", ".").' D -- '.number_format($itr, 0, "", ".").' I -- '.number_format($etr, 0, "", ".").' E -- '.number_format($ttr, 0, "", ".").' T';
                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 7,'$time','$nachricht')", $db);
                        mysql_query("update de_user_data set newnews = 1 where user_id = $uid", $db);
                    }
                }
            } else {
                $fehlermsg .= $resource_lang['sekresfehler'];
            }
        }
        //transaktionsende
        $erg = releaseLock($ums_user_id); //L&ouml;sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($resource_lang['releaselock'].$ums_user_id.$resource_lang['releaselock2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$resource_lang['releaselock3'].'</font><br><br>';
    }

}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// kollektoren bauen
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
if (isset($_POST["b_col"])) {
    $b_col = intval($_POST["b_col"]);
    //transaktionsbeginn
    if (setLock($ums_user_id)) {
        //nochmal vorher die rohstoffe auslesen
        $db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($db_daten);
        $restyp01 = $row[0];
        $restyp02 = $row[1];
        $restyp03 = $row[2];
        $restyp04 = $row[3];
        $restyp05 = $row[4];
        $gr01 = $restyp01;
        $gr02 = $restyp02;
        $gr03 = $restyp03;
        $gr04 = $restyp04;

        //echo "Button gedr&uuml;ckt.\n <br>";
        if (validDigit($b_col)) {
            if ($b_col > 0 && hasTech($pt, 7)) {
                $z = 0;
                //echo (1000+$colanz*500>$rohstoffe);
                for ($i = 1; $i <= $b_col; $i++) {
                    //Kosten fuer ersten Kollektor 1000, fuer jeden folgenden 500 mehr
                    //noch rohstoffe vorhanden? wenn ja, dann kollektor kaufen
                    if (floor((1000 + floor($colanz * $colanz / 20 * 150)) * (1 - $baukostenreduzierung)) <= $restyp01 &&
                            floor((100 + floor($colanz * $colanz / 20 * 20)) * (1 - $baukostenreduzierung)) <= $restyp02) {
                        $restyp01 = $restyp01 - floor((1000 + ($colanz * $colanz / 20 * 150)) * (1 - $baukostenreduzierung));
                        $restyp02 = $restyp02 - floor((100 + ($colanz * $colanz / 20 * 20)) * (1 - $baukostenreduzierung));
                        $colanz++;
                        $z++;
                    } else {
                        break;
                    }
                }

                //in sektor 1 d�rfen maximal 25 kollektoren gebaut werden
                if ($sector == 1 and $colanz > 25) {
                    $fehlermsg = $resource_lang['maxcolwarnung'];
                    $restyp01 = $gr01;
                    $restyp02 = $gr02;
                    $restyp03 = $gr03;
                    $restyp04 = $gr04;
                } else {
                    //gibt $z kollektoren in auftrag
                    $result = mysql_query("SELECT anzahl FROM de_user_build WHERE user_id = '$ums_user_id' AND tech_id=80 AND verbzeit=4", $db);
                    $row = mysql_fetch_array($result);
                    if ($z > 0) {
                        if ($row[0] == 0) { //es gibt keine kollektoren mit 4 ticks laenge in der queue
                            mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, 80, '$z', 4)", $db);
                        } else {
                            mysql_query("UPDATE de_user_build SET anzahl = anzahl + '$z' WHERE user_id = '$ums_user_id' AND tech_id=80 AND verbzeit=4 ", $db);
                        }
                        //test auf allyaufgabe
                        if ($ownally != '') {
                            //allydaten laden
                            $db_daten = mysql_query("SELECT * FROM de_allys WHERE allytag='$ownally'", $db);
                            $row = mysql_fetch_array($db_daten);
                            $allyid = $row['id'];
                            if ($row['questtyp'] == 0) {
                                mysql_query("UPDATE de_allys SET questreach=questreach+'$z' WHERE id='$allyid' AND questtyp=0", $db);
                            }
                        }
                    }
                    //anzahl der gebauen kollektoren mitloggen
                    mysql_query("UPDATE de_user_data SET col_build = col_build + '$z' WHERE user_id = '$ums_user_id'", $db);

                    //aktualisiert die rohstoffe
                    $gr01 = $gr01 - $restyp01;
                    $gr02 = $gr02 - $restyp02;
                    mysql_query("UPDATE de_user_data SET restyp01 = restyp01 - $gr01, restyp02 = restyp02 - $gr02 WHERE user_id = '$ums_user_id'", $db);
                    $anzahl = $anzahl + $z;
                    //echo "Sonnenkollektoren in Auftrag gegeben: ".$z."<br>";
                }
            }
        }
        //transaktionsende
        $erg = releaseLock($ums_user_id); //L&ouml;sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($resource_lang['releaselock'].$ums_user_id.$resource_lang['releaselock2']."<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$resource_lang['releaselock3'].'</font><br><br>';
    }

}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

if ($fehlermsg != '') {
    echo '<div class="info_box"><span class="text2">'.$fehlermsg.'</span></div><br>';
}

if (isset($trademsg) && !empty($trademsg)) {
    echo '<div class="info_box"><span class="text1">'.$trademsg.'</span></div><br>';
}

//k�nnen Kollektoren gebaut werden?
if (!hasTech($pt, 7)) {
    $techcheck = "SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=7";
    $db_tech = mysql_query($techcheck, $db);
    $row_techcheck = mysql_fetch_array($db_tech);

    echo '<br>';
    rahmen_oben($resource_lang['fehlendesgebaeude']);
    echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=7" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_7.jpg" border="0"></a></td>
	<td valign="top">'.$resource_lang['gebaeudeinfo'].': '.$row_techcheck['tech_name'].'</td>
	</tr>';
    echo '</table>';
    rahmen_unten();
} else {
    $m = floor(1000 + ($colanz * $colanz / 20 * 150));
    $d = floor(100 + ($colanz * $colanz / 20 * 20));
    ?>
<form action="resource.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0" class="pctabs pctab1">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td align="center" class="ro"><div class="cellu"><?php echo $resource_lang['kollibau3'];?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr align="center">
<td class="rl">&nbsp;</td>
<td>

<table width="565" border="0" cellpadding="0" cellspacing="1">
<tr>
<td>
<?php
      $bg = 'cell';
    echo '<tr valign="middle" align="center" height="20">';
    echo '<td rowspan="3" class="cell"><img src="'.$ums_gpfad.'g/kollie.gif" border="0" alt="'.$resource_lang['kolli'].'"></td>';
    echo '<td class="'.$bg.'"><b>'.$resource_lang['vorhandenekollis'].'</b>:</td>';
    echo '<td class="'.$bg.'"><b>'.number_format($col, 0, "", ".").' ('.number_format($anzahl, 0, "", ".").$resource_lang['imbau'].')</b></td>';
    echo '</tr>';

    $bg = 'cell1';
    echo '<tr valign="middle" align="center" height="20">';
    echo '<td class="'.$bg.'"><b>'.$resource_lang['kollektorbau'].'</b>:</td>';
    echo '<td class="'.$bg.'"><input type="text" id="b_col" name="b_col" value="" size="4" maxlength="5" onkeyup="calccolcost('.($col + $anzahl).');"><b>'.$resource_lang['stueck'].'</b><input type="Submit" name="build" value="'.$resource_lang['bauen'].'"></td>';
    echo '</tr>';

    $bg = 'cell';

    echo '<tr valign="middle" align="center" height="20">';
    echo '<td class="'.$bg.'"><b>'.$resource_lang['colbaukosten'].'</b>:</td>';
    echo '<td class="'.$bg.'"><b><span id="colmcost">0</span> M + <span id="coldcost">0</span> D</b></td>';
    echo '</tr>';

    echo '</table>';
    ?>

</td>
<td class="rr">&nbsp;</td>
</tr>
<tr>
<td class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur">&nbsp;</td>
</tr>
</table>
<br>
</form>
<?php
}
?>
<form action="resource.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0" class="pctabs pctab2">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="560" class="ro" align="center"><div class="cellu"><?php echo $resource_lang['resertrag']?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td>

<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="200">
<col width="90">
<col width="90">
<col width="90">
<col width="90">
</colgroup>
<?php
  //kollektorenergieoutput
  //grundenergie
  $c1 = 0;
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe1'].'"> '.$resource_lang['kolliausbeute'].'</td>';
echo '<td class="'.$bg.'" colspan=4>'.number_format($ea, 0, "", ".").' ('.number_format($col, 0, "", ".").' '.$resource_lang['kollis'].')</td>';
echo '</tr>';

//sektorartefaktenergie
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe2'].'"> + '.$resource_lang['sekartibonus'].'</td>';
echo '<td class="'.$bg.'" colspan=4>'.number_format($sartefaktenergie, 0, "", ".").' ('.number_format($sartefakt, 2, ",", ".").' %)</td>';
echo '</tr>';

//kriegsartefaktenergie
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe6'].'"> + '.$resource_lang['kriegsartibonus'].'</td>';
echo '<td class="'.$bg.'" colspan=4>'.number_format($kartefaktenergie, 0, "", ".").' ('.$resource_lang['kriegsartefakte'].': '.$kartefakt.')</td>';
echo '</tr>';

//efta-artefaktenergie
/*
$bg='cell1';
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe3'].'"> + '.$resource_lang[eftaartibonus].'</td>';
echo '<td class="'.$bg.'" colspan=4>'.number_format($eartefaktenergie, 0,"",".").' ('.$resource_lang[eftaartefakte].': '.$eartefakt.')</td>';
echo '</tr>';
  */
//ade-rassenbonus
/*
  $bg='cell';
  echo '<tr valign="middle" align="center" height="25">';
  echo '<td class="'.$bg.'">+ Ablyon DEvolution Rassenbonus <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="Die Rohstoffe berechnen sich nach dem Rundenalter (Wirtschaftsicks) und der Sektorherrschaft der eigenen Rasse auf dem Ablyon DEvolution-Server."></td>';
  echo '<td class="'.$bg.'" colspan=4>'.number_format($adebonus, 0,"",".").' (Herrschaft: '.number_format($prozente[$ums_rasse-1], 2,",",".").'%)</td>';
  echo '</tr>';
 */

//gesamtenergie
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<b>'.$resource_lang['gesamtenergie'].'</b></td>';
echo '<td class="'.$bg.'" colspan=4><b>'.number_format($eages, 0, "", ".").'</b></td>';
echo '</tr>';

//energie-materieumwandlung
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<b>'.$resource_lang['energie'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.$resource_lang['multiplex'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.$resource_lang['dyharra'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.$resource_lang['iradium'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.$resource_lang['eternium'].'</b></td>';
echo '</tr>';

//energieverteilunsschlüssel
if (hasTech($pt, 14)) {
    $st[0] = '<input type="text" name="e_t1" value="'.$keym.'" size="3" maxlength="3">&nbsp;%';
} else {
    $st[0] = 'N/A';
}

if (hasTech($pt, 15)) {//wenn kollektor vorhanden zeige eingabefeld, ansonsten unsichtbar
    $st[1] = "<input type=\"text\" name=\"e_t2\" value=\"$keyd\" size=\"3\" maxlength=\"3\">&nbsp;%";
} else {
    $st[1] = 'N/A';
}

if (hasTech($pt, 16)) {//wenn kollektor vorhanden zeige eingabefeld, ansonsten unsichtbar
    $st[2] = "<input type=\"text\" name=\"e_t3\" value=\"$keyi\" size=\"3\" maxlength=\"3\">&nbsp;%";
} else {
    $st[2] = 'N/A';
}

if (hasTech($pt, 17)) {//wenn kollektor vorhanden zeige eingabefeld, ansonsten unsichtbar
    $st[3] = "<input type=\"text\" name=\"e_t4\" value=\"$keye\" size=\"3\" maxlength=\"3\">&nbsp;%";
} else {
    $st[3] = 'N/A';
}

if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe7'].'"> '.$resource_lang['energieschluessel'].'</td>';
echo '<td class="'.$bg.'">'.$st[0].'</td>';
echo '<td class="'.$bg.'">'.$st[1].'</td>';
echo '<td class="'.$bg.'">'.$st[2].'</td>';
echo '<td class="'.$bg.'">'.$st[3].'</td>';
echo '</tr>';

//Bestätigen-Button
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'"></td>';
echo '<td class="'.$bg.'" colspan="4"><input name="change" value="Schl&uuml;ssel &auml;ndern" type="Submit"></td>';
echo '</tr>';



//energie-inputmenge
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&Dieser Wert h&auml;ngt von der Gesamtenergie und dem Energieveteilungsschl&uuml;ssel ab. Diese Energiemenge wird in die entsprechende Materie umgewandelt."> '.$resource_lang['energieinput'].'</td>';
echo '<td class="'.$bg.'">'.number_format($em, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ed, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ei, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ee, 0, "", ".")."</td>";
echo '</tr>';

//umwandlungsverh�ltnis
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe8'].'"> '.$resource_lang['umwandlungsverh'].'</td>';
echo '<td class="'.$bg.'">'.$emvm.':1</td>';
echo '<td class="'.$bg.'">'.$emvd.':1</td>';
echo '<td class="'.$bg.'">'.$emvi.':1</td>';
echo '<td class="'.$bg.'">'.$emve.':1</td>';
echo '</tr>';

//umwandlungsertrag
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&Dieser Wert ist die Menge der Ressourcen, die durch die Umwandlung von Energie in Materie erhalten wurde."> '.$resource_lang['materieoutput'].'</td>';
echo '<td class="'.$bg.'">'.number_format($rm, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($rd, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ri, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($re, 0, "", ".")."</td>";
echo '</tr>';
$resges[0] = $rm;
$resges[1] = $rd;
$resges[2] = $ri;
$resges[3] = $re;

//planetarer rohstoffertrag
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" 
		border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe9'].'<br><br>Aus dem aktiven Dienst entlassene Geheimagenten ('.
      number_format($agent_lost, 0, "", ".").') werden als Zollkontrolleure eingesetzt und sorgen f&uuml;r ein zus&auml;tzliches Einkommen.<br>
		Grundwert: '.
      number_format($grundm - $spezim, 0, "", ".").' M / '.
      number_format($grundd - $spezid, 0, "", ".").' D / '.
      number_format($grundi - $spezii, 0, "", ".").' I / '.
      number_format($grunde - $spezie, 0, "", ".").' E
		<br>
		Zolleinnahmen: '.
      number_format($zollm, 0, "", ".").' M / '.
      number_format($zolld, 0, "", ".").' D / '.
      number_format($zolli, 0, "", ".").' I / '.
      number_format($zolle, 0, "", ".").' E
		<br>
		Einnahmen durch Spezialisierungen: '.
      number_format($spezim, 0, "", ".").' M / '.
      number_format($spezid, 0, "", ".").' D / '.
      number_format($spezii, 0, "", ".").' I / '.
      number_format($spezie, 0, "", ".").' E

		  "> '.$resource_lang['plusplanrohstoff'].'</td>';
echo '<td class="'.$bg.'">'.number_format($grundm + $zollm, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($grundd + $zolld, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($grundi + $zolli, 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($grunde + $zolle, 0, "", ".")."</td>";
echo '</tr>';
$resges[0] += $grundm;
$resges[1] += $grundd;
$resges[2] += $grundi;
$resges[3] += $grunde;
$resges[0] += $zollm;
$resges[1] += $zolld;
$resges[2] += $zolli;
$resges[3] += $zolle;

//Handel
/*
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  echo '<tr valign="middle" align="center" height="25">';
  echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="Handel&Einkommen aus Handelsrouten"> + Handel</td>';
  echo '<td class="'.$bg.'">'.number_format($ertrag_handel[0], 0,"",".")."</td>";
  echo '<td class="'.$bg.'">'.number_format($ertrag_handel[1], 0,"",".")."</td>";
  echo '<td class="'.$bg.'">'.number_format($ertrag_handel[2], 0,"",".")."</td>";
  echo '<td class="'.$bg.'">'.number_format($ertrag_handel[3], 0,"",".")."</td>";
  echo '</tr>';
  $resges[0]+=$ertrag_handel[0];
  $resges[1]+=$ertrag_handel[1];
  $resges[2]+=$ertrag_handel[2];
  $resges[3]+=$ertrag_handel[3];
*/

//sektorartefaktbonus
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['hilfe'].'&'.$resource_lang['hilfe10'].'"> '.$resource_lang['plussekartibonus'].'</td>';
echo '<td class="'.$bg.'">'.number_format($sa_grund[0], 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($sa_grund[1], 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($sa_grund[2], 0, "", ".")."</td>";
echo '<td class="'.$bg.'">'.number_format($sa_grund[3], 0, "", ".")."</td>";
echo '</tr>';
$resges[0] += $sa_grund[0];
$resges[1] += $sa_grund[1];
$resges[2] += $sa_grund[2];
$resges[3] += $sa_grund[3];

//gesamtrohstoffe
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" style="text-align: left;">&nbsp;<b>'.$resource_lang['gesamtrohstoff'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.number_format($resges[0], 0, "", ".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($resges[1], 0, "", ".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($resges[2], 0, "", ".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($resges[3], 0, "", ".")."</b></td>";
echo '</tr>';

?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
</form>
<br><br>
<?php

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// rohstoffhandel - eingabemöglichkeit
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
rahmen_oben('Allianz-Notfallrohstoffkonverter <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" 
title="Hier k&ouml;nnen Rohstoffe umgewandelt werden.&Verlustleistung: '.($handelssteuersatz).'%'.'">');

echo '<div class="cell" style="width: 560px; text-align: center;">';

if ($ally_has_notfallkonverter && hasTech($pt, 4)) {

    echo '<form action="resource.php" method="POST">';
    echo 'Ich ben&ouml;tige ';

    echo '<input type="text" id="rh_amount" name="rh_amount" value="" size="12" maxlength="16" onkeyup="javascript: rh_calc(0);"> ';

    echo '<select name="rh_v1" id="rh_v1" onchange="javascript: rh_calc(0);">
      <option value="1" selected>Multiplex</option>
      <option value="2">Dyharra</option>
      <option value="3">Iradium</option>
      <option value="4">Eternium</option>
    </select>';

    echo ' und bezahle mit ';

    echo '<input type="text" id="rh_cost" name="rh_cost" value="" size="12" maxlength="16" onkeyup="javascript: rh_calc(1);"> ';

    echo '<select name="rh_v2" id="rh_v2" onchange="javascript: rh_calc(0);">
      <option value="1">Multiplex</option>
      <option value="2" selected>Dyharra</option>
      <option value="3">Iradium</option>
      <option value="4">Eternium</option>
    </select>.';

    echo '<br><br><input type="Submit" name="startrestrade" value="Rohstoffe umwandeln">';
    echo '</form>';
} else {
    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=4";
    $db_tech = mysqli_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_array($db_tech);

    echo '<span class="text2">Du ben&ouml;tigst eine Allianz mit Notfallmateriekonverter und folgendes Geb&auml;ude: '.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'</span>';
}

echo '</div>';

rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// sektorlager
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
if (hasTech($pt, 3)) { //wenn planetare boerse vorhanden, dann ist eine einzahlung ins sektorlager m&ouml;glich
    $db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id='$sector'", $db);
    $row = mysql_fetch_array($db_daten);
    $srestyp01 = $row[0];
    $srestyp02 = $row[1];
    $srestyp03 = $row[2];
    $srestyp04 = $row[3];
    $srestyp05 = $row[4];
    ?>
<br>
<form action="resource.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0" class="pctabs pctab3">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="1" class="ro" align="center">&nbsp;</td>
<?php
    echo '<td width="550px" class="ro" align="center">
	
	<table width="100%"><tr>
	<td width="20px">&nbsp;</td>
	<td align="center">'.$resource_lang['uebersichtseklager'].'</td>
	<td width="20px"><img style="vertical-align: middle;" src="'.
        $ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$resource_lang['sektorkosten'].'&'.$btipstr.'"></td>
	</tr></table>
	
	</td>';
    ?>
<td width="1" class="ro" align="center">&nbsp;</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="3">
<div class="cell">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="100">
<col width="100">
<col width="100">
</colgroup>
<?php
      //echo '<table border="0" cellpadding="0" cellspacing="1" bgcolor="#000000">';
      echo '<tr align="center">';
    echo '<td><font color="28FF50">'.$resource_lang['rohstoff'].'</td>';
    echo '<td><font color="28FF50">'.$resource_lang['sektorlager'].'</td>';
    echo '<td><font color="28FF50">'.$resource_lang['transfer'].'</td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>Multiplex</td>';
    echo '<td align="right">'.number_format($srestyp01, 0, "", ".").'</td>';
    echo '<td><input type="text" name="mtr" value="" size="8" maxlength="8"></td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>Dyharra</td>';
    echo '<td align="right">'.number_format($srestyp02, 0, "", ".").'</td>';
    echo '<td><input type="text" name="dtr" value="" size="8" maxlength="8"></td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>Iradium</td>';
    echo '<td align="right">'.number_format($srestyp03, 0, "", ".").'</td>';
    echo '<td><input type="text" name="itr" value="" size="8" maxlength="8"></td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>Eternium</td>';
    echo '<td align="right">'.number_format($srestyp04, 0, "", ".").'</td>';
    echo '<td><input type="text" name="etr" value="" size="8" maxlength="8"></td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>Tronic</td>';
    echo '<td align="right">'.number_format($srestyp05, 0, "", ".").'</td>';
    echo '<td><input type="text" name="ttr" value="" size="8" maxlength="8"></td>';
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td>&nbsp;</td>';
    echo '<td><input type="Submit" name="trans" value="'.$resource_lang['transferieren'].'"></td>';
    echo '<td>&nbsp;</td>';
    echo '</tr>';
    ?>
</table>
</div>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>
</form>
<?php
}
?>
<input type="Submit" name="button" value="" style="visibility: hidden;">
</form>
<?php
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// Ressourcenlager
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
rahmen_oben('Dein Lager');
echo '<table cellspacing="1" cellpadding="0" style="width: 560px;" class="pctabs pctab4">';
$c1 = 0;
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'" style="font-weight: bold; text-align: center;"><td>Name</td><td>Anzahl</td></tr>';
//Multiplex
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'"><td>Multiplex</td><td style="text-align: right">'.number_format(floor($restyp01), 0, ",", ".").'</td></tr>';
//Dyharra
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'"><td>Dyharra</td><td style="text-align: right">'.number_format(floor($restyp02), 0, ",", ".").'</td></tr>';
//Iradium
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'"><td>Iradium</td><td style="text-align: right">'.number_format(floor($restyp03), 0, ",", ".").'</td></tr>';
//Eternium
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'"><td>Eternium</td><td style="text-align: right">'.number_format(floor($restyp04), 0, ",", ".").'</td></tr>';
//Tronic
$tronic_hinweis = '';
if (hasTech($pt, 160)) {
    $tronicertrag = 1;
    $tronicertrag += intval(getArtefactAmountByUserId($_SESSION['ums_user_id'], 21));
    $tronic_hinweis = '<span style="color: #00FF00;">(jeden 20. Wirtschaftstick +'.$tronicertrag.')</span> ';

}

if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'"><td>Tronic</td><td style="text-align: right">'.$tronic_hinweis.number_format(floor($restyp05), 0, ",", ".").'</td></tr>';

//weitere items aus der DB auslesen
$sql = "SELECT * FROM de_user_storage LEFT JOIN de_item_data ON(de_user_storage.item_id=de_item_data.item_id) 
	WHERE de_user_storage.user_id='".$_SESSION['ums_user_id']."' ORDER BY item_sort_order ASC, item_name ASC";
//echo $sql;
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);


while ($row = mysqli_fetch_array($db_daten)) {
    if ($c1 == 0) {
        $c1 = 1;
        $bg = 'cell1';
    } else {
        $c1 = 0;
        $bg = 'cell';
    }

    //check auf Änderung im Tick
    if ($row['item_wt_change'] > 0) {
        $item_change_wt = '<span style="color: #00FF00;">(+'.number_format($row['item_wt_change'], 0, ",", ".").')</span> ';
    } else {
        $item_change_wt = '';
    }

    echo '<tr class="'.$bg.'"><td>'.$row['item_name'].'</td><td style="text-align: right">'.$item_change_wt.number_format($row['item_amount'], 0, ",", ".").'</td></tr>';
}

echo '</table>';

rahmen_unten();

include "fooban.php"; ?>
<script>
<?php
echo 'var p='.($handelssteuersatz).';';
?>
var bkr=<?php echo $baukostenreduzierung;?>;
function number_format(s) {
	var tf,uf,i;
	uf="";
	s=Math.round(s);
	tf=s.toString();
	j=0;
	for(i=(tf.length-1);i>=0;i--)
	{
	   uf=tf.charAt(i)+uf;
	   j++;
	   if((j==3) && (i!=0))
	   {
	      j=0;
	      uf="."+uf;
	   }
	}
	return uf;
	}

function calccolcost(hascol){
	var build=parseInt($("#b_col").val());
	if(isNaN(build))build=0;
	var mcost=0;
	var dcost=0;

	for (i=1; i<=build; i++){
		mcost=mcost+(1000+((hascol*hascol/20*150)))*(1-bkr);
		dcost=dcost+(100+((hascol*hascol/20*20)))*(1-bkr);
		hascol++;
	}

	var color1="#FFFFFF";
	var color2="#FFFFFF";
	if(mcost>hasres[0])color1="#FF0000";
	if(dcost>hasres[1])color2="#FF0000";

	$("#colmcost").html('<font color="'+color1+'">'+number_format(Math.round(mcost))+'</font>');
	$("#coldcost").html('<font color="'+color2+'">'+number_format(Math.round(dcost))+'</font>');
}

function rh_calc(pos){
	uv=new Array(1,2,3,4,10000);
	
	if(pos==0)
	{
		target='#rh_cost';
		value=$("#rh_amount").val();
		rc1=($('#rh_v1 option:selected').val());
		rc2=($('#rh_v2 option:selected').val());
	}
	else 
	{
		target='#rh_amount';
		value=$("#rh_cost").val();
		rc2=($('#rh_v1 option:selected').val());
		rc1=($('#rh_v2 option:selected').val());
	}

	value=value*uv[rc1-1];
	value=value/uv[rc2-1];

	if(pos==0)
	{
		value=value*100/(100-p);
		value=Math.ceil(value);
	}
	else
	{
		value=value-(value/100*p);
		value=Math.floor(value);
	}
	
	$(target).val(value);
}

</script>
</body>
</html>
