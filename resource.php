<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "inc/artefakt.inc.php";
include 'inc/lang/'.$sv_server_lang.'_resource.lang.php';
include 'inc/lang/'.$sv_server_lang.'_siegel.lang.php';
include 'inc/sabotage.inc.php';
include "functions.php";

//Meldungen der Aktionen, ausgegeben unter der Rohstoffleiste
$res_meldungen = array();
function res_meldung($text, $typ = 'fehler')
{
    global $res_meldungen;
    $res_meldungen[] = '<div class="mod-meldung mod-meldung-'.$typ.'">'.$text.'</div>';
}

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
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1");
$rowx = mysqli_fetch_assoc($result);
$maxtick = $rowx["tick"];

//spezialisierung bzgl. der baukostenreduzierung �berpr�fen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec1=3", [$sector]);
$baukostenreduzierung = mysqli_num_rows($db_daten) * 2;
if ($baukostenreduzierung > 20) {
    $baukostenreduzierung = 20;
}
$baukostenreduzierung = $baukostenreduzierung / 100;

//spezialisierung bzgl. des erh�hten planetaren ertrages
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec3=3", [$sector]);
$planertragbonus = mysqli_num_rows($db_daten) * 10;
if ($planertragbonus > 100) {
    $planertragbonus = 100;
}
$planertragbonus = $planertragbonus / 100;

//Bonus durch das Siegel von Basranur, wirkt wie im Wirtschaftstick additiv zur Spezialisierung
$siegelbonus = 0;
if ($pd['npc'] == 0) {
    try {
        $siegelbonus = (new \DieEwigen\DE2\Model\Siegel\SiegelService($GLOBALS['dbi']))->getBonusPercent() / 100;
    } catch (\Throwable $e) {
        error_log('Siegel von Basranur: '.$e->getMessage());
    }
}

//schauen welche sektorartefakte in dem sektor sind
$sartefakt = 0;
$sa_grund = array(0,0,0,0);
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_artefakt WHERE sector = ?", [$sector]);
while ($row2 = mysqli_fetch_assoc($result)) { //jeder gefundene datensatz wird geprueft
    $sartefakt = $sartefakt + $sv_artefakt[$row2["id"] - 1][0];
    $sa_grund[0] = $sa_grund[0] + $sv_artefakt[$row2["id"] - 1][1];
    $sa_grund[1] = $sa_grund[1] + $sv_artefakt[$row2["id"] - 1][2];
    $sa_grund[2] = $sa_grund[2] + $sv_artefakt[$row2["id"] - 1][3];
    $sa_grund[3] = $sa_grund[3] + $sv_artefakt[$row2["id"] - 1][4];
}

////////////////////////////////////////////////
//grundertrag
////////////////////////////////////////////////
//Grundertragbonus für die BR, gibt es nie in der Ewigen Runde
if (($maxtick > 2500000 && $sv_ewige_runde != 1 && $sv_hardcore != 1)) {
    $grundertragmultiplikator = 200;
} else {
    $grundertragmultiplikator = 1;
}

if (!hasTech($pt, 4)) {//keine gilde
    $grundm = $sv_plan_grundertrag[0] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grundd = $sv_plan_grundertrag[1] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grundi = $sv_plan_grundertrag[2] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grunde = $sv_plan_grundertrag[3] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);

    //bonus durch spezialisierung
    $spezim = $sv_plan_grundertrag[0] * $grundertragmultiplikator * $planertragbonus;
    $spezid = $sv_plan_grundertrag[1] * $grundertragmultiplikator * $planertragbonus;
    $spezii = $sv_plan_grundertrag[2] * $grundertragmultiplikator * $planertragbonus;
    $spezie = $sv_plan_grundertrag[3] * $grundertragmultiplikator * $planertragbonus;

    //bonus durch das siegel von basranur
    $siegelm = $sv_plan_grundertrag[0] * $grundertragmultiplikator * $siegelbonus;
    $siegeld = $sv_plan_grundertrag[1] * $grundertragmultiplikator * $siegelbonus;
    $siegeli = $sv_plan_grundertrag[2] * $grundertragmultiplikator * $siegelbonus;
    $siegele = $sv_plan_grundertrag[3] * $grundertragmultiplikator * $siegelbonus;
} else {  //mit gilde
    $grundm = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grundd = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grundi = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);
    $grunde = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * (1 + $planertragbonus + $siegelbonus);

    //bonus durch spezialisierung
    $spezim = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * $planertragbonus;
    $spezid = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * $planertragbonus;
    $spezii = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * $planertragbonus;
    $spezie = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * $planertragbonus;

    //bonus durch das siegel von basranur
    $siegelm = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * $siegelbonus;
    $siegeld = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * $siegelbonus;
    $siegeli = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * $siegelbonus;
    $siegele = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * $siegelbonus;
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
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT anzahl FROM de_user_build WHERE user_id = ? AND tech_id=80", [$_SESSION['ums_user_id']]);
while ($row2 = mysqli_fetch_assoc($result)) { //jeder gefundene datensatz wird geprueft
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
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE allytag=?", [$ownally]);
$row = mysqli_fetch_assoc($db_daten);
$num = mysqli_num_rows($db_daten);
if ($num == 1) {
    $allyid = $row['id'];

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyid]);
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) {
        $row = mysqli_fetch_assoc($db_daten);

        if ($row['bldg6'] > 0) {
            $ally_has_notfallkonverter = true;
            $handelssteuersatz -= $row['bldg6'];
        }
    }
}

//Meldungen der Aktionen; früher erst nach dem Tausch geleert, dessen Fehlermeldungen gingen dabei verloren
$fehlermsg = '';

//Tausch durchführen
if (intval($_REQUEST['rh_amount'] ?? 0) > 0 && intval($_REQUEST['rh_cost'] > 0) && hasTech($pt, 4) && $ally_has_notfallkonverter) {
    //transaktionsbeginn
    if (setLock($_SESSION['ums_user_id'])) {
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
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
            $row = mysqli_fetch_assoc($db_daten);
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
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT ssteuer FROM de_sector WHERE sec_id=?", [$sector]);
                $row_steuer = mysqli_fetch_assoc($db_daten);
                //auf den erlaubten Bereich begrenzen (ältere, manipulierte Werte in der DB)
                $sektorsteuersatz = max(0, min(5, intval($row_steuer['ssteuer'])));

                $steueranteil = $res_cost / 100 * ($handelssteuersatz + $sektorsteuersatz);

                $steueranteil_sektor = ($handelssteuersatz + $sektorsteuersatz) > 0 ? $steueranteil * $sektorsteuersatz / ($handelssteuersatz + $sektorsteuersatz) : 0;

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
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET restyp0$res_source=restyp0$res_source+? WHERE sec_id=?", [$steueranteil_sektor, $sector]);

                    //rohstoffe und sektorspende gutschreiben
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET 
					restyp0$res_source=restyp0$res_source-?, 
					restyp0$res_target=restyp0$res_target+?,  
					spend0$res_source=spend0$res_source+? WHERE user_id=?", [$res_cost, $res_get, $steueranteil_sektor, $_SESSION['ums_user_id']]);


                    //aktuellen rohstoffwert für die resline auslesen
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
                    $row = mysqli_fetch_assoc($db_daten);
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
        $erg = releaseLock($_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            res_meldung('Fehler bei der Transaktion.');
        }
    }// if setlock-ende
    else {
        res_meldung('Fehler bei der Transaktion.');
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $resource_lang['ressourcen']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php

echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

////////////////////////////////////////////////////////////
// Sektorkasse/Sektorkosten
////////////////////////////////////////////////////////////
//Kostenfaktor
$avg_player = getAveragePlayerAmountInSectorOnServer();
$kostenfaktor = 10 - $avg_player;

//sektorgebäudekosten auslesen: je Zeile Name, M, D, I, E, T
$sektorkosten = array();
//gebäude
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name, restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_tech_data1 WHERE tech_id>119 AND tech_id<130 ORDER BY tech_id");
while ($row = mysqli_fetch_assoc($db_daten)) {
    $sektorkosten[] = array($row['tech_name'], $row['restyp01'] / $kostenfaktor, $row['restyp02'] / $kostenfaktor, $row['restyp03'] / $kostenfaktor, $row['restyp04'] / $kostenfaktor, $row['restyp05'] / $kostenfaktor);
}

//raumschiff
$sektorkosten[] = array($resource_lang['sektorraumschiff'], 2000, 500, 500, 2000, 0);

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// den verteilungsschlüssel ändern
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
//$fehlermsg wird schon vor dem Rohstofftausch geleert

$e_t1 = intval($_POST["e_t1"] ?? 0);
$e_t2 = intval($_POST["e_t2"] ?? 0);
$e_t3 = intval($_POST["e_t3"] ?? 0);
$e_t4 = intval($_POST["e_t4"] ?? 0);
if (!empty($e_t1) || !empty($e_t2) || !empty($e_t3) || !empty($e_t4)) {
    //keine negativen Anteile: sonst ließe sich mehr als 100 % auf einen Rohstoff legen
    if ($e_t1 >= 0 && $e_t2 >= 0 && $e_t3 >= 0 && $e_t4 >= 0) {
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
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET ekey = ? WHERE user_id = ?", [$newkey, $_SESSION['ums_user_id']]);
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
$ea = $col * ($sv_kollieertrag - $malus - $sabotagemalus);

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
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0");
$row = mysqli_fetch_assoc($db_daten);
$maxcol = $row['maxcol'];

$adebonus = 0;


$eages = $ea + $kartefaktenergie + $dartefaktenergie + $sartefaktenergie + $paleniumenergie + $adebonus;

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
    if (setLock($_SESSION['ums_user_id'])) {
        //Rohstoffe innerhalb der Sperre neu laden, sonst spendet eine zweite Anfrage mit veralteten Beständen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $row_res = mysqli_fetch_assoc($db_daten);
        $restyp01 = $row_res['restyp01'];
        $restyp02 = $row_res['restyp02'];
        $restyp03 = $row_res['restyp03'];
        $restyp04 = $row_res['restyp04'];
        $restyp05 = $row_res['restyp05'];

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
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data set restyp01 = restyp01 - ?, restyp02 = restyp02 - ?,
				restyp03 = restyp03 - ?, restyp04 = restyp04 - ?, restyp05 = restyp05 - ?,
				spend01 = spend01 + ?, spend02 = spend02 + ?, spend03 = spend03 + ?,
				spend04 = spend04 + ?, spend05 = spend05 + ? WHERE user_id = ?", 
                [$mtr, $dtr, $itr, $etr, $ttr, $mtr, $dtr, $itr, $etr, $ttr, $_SESSION['ums_user_id']]);

                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, restyp03 = restyp03 + ?, restyp04 = restyp04 + ?, restyp05 = restyp05 + ? WHERE sec_id = ?", 
                [$mtr, $dtr, $itr, $etr, $ttr, $sector]);
                $restyp01 = $restyp01 - $mtr;
                $restyp02 = $restyp02 - $dtr;
                $restyp03 = $restyp03 - $itr;
                $restyp04 = $restyp04 - $etr;
                $restyp05 = $restyp05 - $ttr;
                //für die Bestätigung unter der Rohstoffleiste
                $res_gespendet = array($mtr, $dtr, $itr, $etr, $ttr);
                //an den bk ne info schicken
                $bk = getSKSystemBySecID($sector);

                if ($bk > 0) { //bk vorhanden, dann dessen daten raussuchen und nachricht einf&uuml;gen
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND system=?", [$sector, $bk]);
                    $anz = mysqli_num_rows($db_daten);
                    if ($anz > 0) {//bk-system ist auch besetzt
                        $row_uid = mysqli_fetch_assoc($db_daten);
                        $uid = $row_uid['user_id'];
                        $time = date("YmdHis");
                        $nachricht = $resource_lang['sekeinzahlung'].$_SESSION['ums_spielername'].': '.number_format($mtr, 0, "", ".").' M -- '.number_format($dtr, 0, "", ".").' D -- '.number_format($itr, 0, "", ".").' I -- '.number_format($etr, 0, "", ".").' E -- '.number_format($ttr, 0, "", ".").' T';
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 7, ?, ?)", [$uid, $time, $nachricht]);
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uid]);
                    }
                }
            } else {
                $fehlermsg .= $resource_lang['sekresfehler'];
            }
        }
        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //L&ouml;sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            res_meldung($resource_lang['releaselock'].$_SESSION['ums_user_id'].$resource_lang['releaselock2']);
        }
    }// if setlock-ende
    else {
        res_meldung($resource_lang['releaselock3']);
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
    if (setLock($_SESSION['ums_user_id'])) {
        //nochmal vorher die rohstoffe und die Kollektoren auslesen (die Anzahl bestimmt den Preis)
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, col FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $row = mysqli_fetch_assoc($db_daten);
        $colanz = $row['col'];
        $result_colbuild = mysqli_execute_query($GLOBALS['dbi'], "SELECT anzahl FROM de_user_build WHERE user_id = ? AND tech_id=80", [$_SESSION['ums_user_id']]);
        while ($row_colbuild = mysqli_fetch_assoc($result_colbuild)) {
            $colanz += $row_colbuild["anzahl"];
        }
        $restyp01 = $row['restyp01'];
        $restyp02 = $row['restyp02'];
        $restyp03 = $row['restyp03'];
        $restyp04 = $row['restyp04'];
        $restyp05 = $row['restyp05'];
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
                    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT anzahl FROM de_user_build WHERE user_id = ? AND tech_id=80 AND verbzeit=4", [$_SESSION['ums_user_id']]);
                    $row = mysqli_fetch_assoc($result);
                    if ($z > 0) {
                        if(!isset($row['anzahl'])){
                            $row['anzahl'] = 0;
                        }
                        if ($row['anzahl'] == 0) { //es gibt keine kollektoren mit 4 ticks laenge in der queue
                            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES (?, 80, ?, 4)", [$_SESSION['ums_user_id'], $z]);
                        } else {
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_build SET anzahl = anzahl + ? WHERE user_id = ? AND tech_id=80 AND verbzeit=4", [$z, $_SESSION['ums_user_id']]);
                        }
                        //test auf allyaufgabe
                        if ($ownally != '') {
                            //allydaten laden
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE allytag=?", [$ownally]);
                            $row = mysqli_fetch_assoc($db_daten);
                            $allyid = $row['id'];
                            if ($row['questtyp'] == 0) {
                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach=questreach+? WHERE id=? AND questtyp=0", [$z, $allyid]);
                            }
                        }
                    }
                    //anzahl der gebauen kollektoren mitloggen
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col_build = col_build + ? WHERE user_id = ?", [$z, $_SESSION['ums_user_id']]);

                    //aktualisiert die rohstoffe
                    $gr01 = $gr01 - $restyp01;
                    $gr02 = $gr02 - $restyp02;
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01 = restyp01 - ?, restyp02 = restyp02 - ? WHERE user_id = ?", [$gr01, $gr02, $_SESSION['ums_user_id']]);
                    $anzahl = $anzahl + $z;
                    //echo "Sonnenkollektoren in Auftrag gegeben: ".$z."<br>";
                }
            }
        }
        //transaktionsende
        $erg = releaseLock($_SESSION['ums_user_id']); //L&ouml;sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            res_meldung($resource_lang['releaselock'].$_SESSION['ums_user_id'].$resource_lang['releaselock2']);
        }
    }// if setlock-ende
    else {
        res_meldung($resource_lang['releaselock3']);
    }

}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script>var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

//Zahl im Spielformat
function res_zahl($wert)
{
    return number_format((float)$wert, 0, "", ".");
}

//Beschriftung mit Hilfetext als Tooltip (Kopf&Text)
function res_hilfe($text, $hilfe)
{
    return '<span class="res-hilfe" title="'.strip_tags($text).'&'.$hilfe.'">'.$text.'</span>';
}

//Zeile der Rohstofftabelle: Beschriftung und je ein Wert für M, D, I, E
function res_rohstoffzeile($label, $werte, $klasse = 'bk-zahl', $labelklasse = 'bk-label')
{
    $html = '<span class="'.$labelklasse.'">'.$label.'</span>';
    foreach ($werte as $wert) {
        $html .= '<span class="'.$klasse.'">'.$wert.'</span>';
    }
    return $html;
}

//////////////////////////////////////////////////////////////
// Ergebnisse der Aktionen
//////////////////////////////////////////////////////////////
if ($fehlermsg != '') {
    res_meldung($fehlermsg, $fehlermsg == $resource_lang['reswarnung'] ? 'warn' : 'fehler');
}
if (isset($trademsg) && !empty($trademsg)) {
    res_meldung($trademsg, 'ok');
}
//Kollektorbau: $z = Anzahl in Auftrag gegeben (nur gesetzt, wenn der Bau versucht wurde)
if (isset($_POST['b_col']) && isset($z) && $fehlermsg == '') {
    if ($z > 0) {
        res_meldung(res_zahl($z).' '.($z == 1 ? 'Kollektor' : 'Kollektoren').' in Auftrag gegeben, fertig in 4 WT.', 'ok');
    } else {
        res_meldung('Die Rohstoffe reichen f&uuml;r keinen weiteren Kollektor.');
    }
}
if (isset($newkey) && $fehlermsg == '') {
    res_meldung('Der Energieverteilungsschl&uuml;ssel ist gespeichert.', 'ok');
}
if (isset($res_gespendet)) {
    $teile = array();
    foreach (array('M', 'D', 'I', 'E', 'T') as $i => $kurz) {
        if ($res_gespendet[$i] > 0) {
            $teile[] = res_zahl($res_gespendet[$i]).' '.$kurz;
        }
    }
    if (count($teile) > 0) {
        res_meldung('Ins Sektorlager eingezahlt: '.implode(' &middot; ', $teile).'.', 'ok');
    } else {
        res_meldung('Es wurde nichts eingezahlt.', 'warn');
    }
}
if (count($res_meldungen) > 0) {
    echo '<div class="mod pol-meldungen">'.implode('', $res_meldungen).'</div>';
}

//////////////////////////////////////////////////////////////
// Kollektorenbau
//////////////////////////////////////////////////////////////
if (!hasTech($pt, 7)) {
    $techcheck = "SELECT tech_name FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=7";
    $db_tech = mysqli_execute_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_assoc($db_tech);

    rahmen_oben($resource_lang['fehlendesgebaeude']);
    echo '<div class="mod res"><div class="res-kolli">';
    echo '<img src="gp/g/kollie.gif" alt="'.$resource_lang['kolli'].'" class="res-kolli-bild">';
    echo '<div class="mod-hinweis res-fehlt">'.$resource_lang['gebaeudeinfo'].': <a href="help.php?t=7">'.$row_techcheck['tech_name'].'</a></div>';
    echo '</div></div>';
    rahmen_unten();
} else {
    //Kosten wie beim Bau: der nächste Kollektor und wie viele die Rohstoffe hergeben (Sektor 1: höchstens 25)
    $res_kollis = $col + $anzahl;
    $res_naechster_m = floor((1000 + ($res_kollis * $res_kollis / 20 * 150)) * (1 - $baukostenreduzierung));
    $res_naechster_d = floor((100 + ($res_kollis * $res_kollis / 20 * 20)) * (1 - $baukostenreduzierung));
    $res_max = 0;
    $c = $res_kollis;
    $m_rest = $restyp01;
    $d_rest = $restyp02;
    while ($res_max < 99999 && floor((1000 + floor($c * $c / 20 * 150)) * (1 - $baukostenreduzierung)) <= $m_rest &&
            floor((100 + floor($c * $c / 20 * 20)) * (1 - $baukostenreduzierung)) <= $d_rest) {
        $m_rest -= floor((1000 + ($c * $c / 20 * 150)) * (1 - $baukostenreduzierung));
        $d_rest -= floor((100 + ($c * $c / 20 * 20)) * (1 - $baukostenreduzierung));
        $c++;
        $res_max++;
    }
    if ($sector == 1) {
        $res_max = max(0, min($res_max, 25 - $res_kollis));
    }

    rahmen_oben($resource_lang['kollibau3']);
    echo '<form action="resource.php" method="POST" class="mod res"><div class="res-kolli">';
    //der drehende Kollektor bleibt (Wunsch des Users: nostalgischer Wert)
    echo '<img src="gp/g/kollie.gif" alt="'.$resource_lang['kolli'].'" class="res-kolli-bild">';
    echo '<div class="res-kolli-rechts">';
    echo '<div class="res-kacheln">';
    echo '<div class="ov-wert"><span class="mod-typ">'.$resource_lang['vorhandenekollis'].'</span><b>'.res_zahl($col).'</b>'.($anzahl > 0 ? '<small>+'.res_zahl($anzahl).$resource_lang['imbau'].'</small>' : '').'</div>';
    echo '<div class="ov-wert"><span class="mod-typ">N&auml;chster Kollektor</span><b>'.res_zahl($res_naechster_m).' M</b><small>'.res_zahl($res_naechster_d).' D</small></div>';
    echo '</div>';
    echo '<div class="res-bau">';
    echo '<input type="text" id="b_col" name="b_col" value="" maxlength="5" inputmode="numeric" autocomplete="off" placeholder="Anzahl" class="mod-eingabe res-anzahl" data-kollis="'.$res_kollis.'">';
    if ($res_max > 0) {
        echo '<button type="button" class="mod-btn mod-btn-leise ally-btn-klein" id="res-max" data-max="'.$res_max.'">Max. '.res_zahl($res_max).'</button>';
    }
    echo '<button type="submit" name="build" value="'.$resource_lang['bauen'].'" class="mod-btn">Bauen</button>';
    echo '</div>';
    echo '<div class="res-kosten">'.$resource_lang['colbaukosten'].': <b id="colmcost">0</b> M + <b id="coldcost">0</b> D <span class="bk-leise">&middot; Bauzeit 4 WT</span></div>';
    echo '</div></div></form>';
    rahmen_unten();
}

//////////////////////////////////////////////////////////////
// Ressourcenertrag und Energieverteilungsschlüssel
//////////////////////////////////////////////////////////////
rahmen_oben($resource_lang['resertrag']);
echo '<form action="resource.php" method="POST" class="mod res">';

//Energie aus Kollektoren und Artefakten
echo '<div class="res-energie">';
echo '<span>'.res_hilfe($resource_lang['kolliausbeute'], $resource_lang['hilfe1']).'</span><span><b>'.res_zahl($ea).'</b> <small>('.res_zahl($col).' '.$resource_lang['kollis'].')</small></span>';
echo '<span>'.res_hilfe('+ '.$resource_lang['sekartibonus'], $resource_lang['hilfe2']).'</span><span>'.res_zahl($sartefaktenergie).' <small>('.number_format($sartefakt, 2, ",", ".").' %)</small></span>';
echo '<span>'.res_hilfe('+ '.$resource_lang['kriegsartibonus'], $resource_lang['hilfe6']).'</span><span>'.res_zahl($kartefaktenergie).' <small>('.$resource_lang['kriegsartefakte'].': '.$kartefakt.')</small></span>';
echo '<span class="res-summe">'.$resource_lang['gesamtenergie'].'</span><span class="res-summe"><b>'.res_zahl($eages).'</b> Energie je WT</span>';
echo '</div>';

//Verteilung auf die Rohstoffe, Umwandlung und Ertrag
$schluessel = array($keym, $keyd, $keyi, $keye);
$st = array();
for ($i = 0; $i < 4; $i++) {
    //ohne Materieumwandler kein Eingabefeld
    if (hasTech($pt, 14 + $i)) {
        $st[$i] = '<span class="bk-prozent"><input type="text" name="e_t'.($i + 1).'" value="'.$schluessel[$i].'" maxlength="3" inputmode="numeric" autocomplete="off" class="mod-eingabe res-schluessel">%</span>';
    } else {
        $st[$i] = '<span class="bk-leise" title="Materieumwandler fehlt&Ohne das Geb&auml;ude wird keine Energie in diesen Rohstoff umgewandelt.">&ndash;</span>';
    }
}

$resges[0] = $rm + $grundm + $zollm + $sa_grund[0];
$resges[1] = $rd + $grundd + $zolld + $sa_grund[1];
$resges[2] = $ri + $grundi + $zolli + $sa_grund[2];
$resges[3] = $re + $grunde + $zolle + $sa_grund[3];

$planet_hilfe = $resource_lang['hilfe9'].'<br><br>Aus dem aktiven Dienst entlassene Geheimagenten ('.res_zahl($agent_lost).') werden als Zollkontrolleure eingesetzt und sorgen f&uuml;r ein zus&auml;tzliches Einkommen.';

echo '<div class="bk-energie res-tabelle">';
echo '<span class="bk-label">'.str_replace('->', ' &rarr; ', $resource_lang['energie']).'</span><span class="bk-spalte">'.$resource_lang['multiplex'].'</span><span class="bk-spalte">'.$resource_lang['dyharra'].'</span><span class="bk-spalte">'.$resource_lang['iradium'].'</span><span class="bk-spalte">'.$resource_lang['eternium'].'</span>';
echo '<span class="bk-label">'.res_hilfe('Verteilung', $resource_lang['hilfe7']).'</span>';
foreach ($st as $feld) {
    echo $feld;
}
echo res_rohstoffzeile(res_hilfe($resource_lang['energieinput'], 'Dieser Wert h&auml;ngt von der Gesamtenergie und dem Energieverteilungsschl&uuml;ssel ab. Diese Energiemenge wird in die entsprechende Materie umgewandelt.'),
    array(res_zahl($em), res_zahl($ed), res_zahl($ei), res_zahl($ee)));
echo res_rohstoffzeile(res_hilfe($resource_lang['umwandlungsverh'], $resource_lang['hilfe8']),
    array($emvm.':1', $emvd.':1', $emvi.':1', $emve.':1'), 'bk-zahl bk-leise');
echo res_rohstoffzeile(res_hilfe($resource_lang['materieoutput'], 'Dieser Wert ist die Menge der Ressourcen, die durch die Umwandlung von Energie in Materie erhalten wurde.'),
    array(res_zahl($rm), res_zahl($rd), res_zahl($ri), res_zahl($re)));
echo res_rohstoffzeile(res_hilfe($resource_lang['plusplanrohstoff'], $planet_hilfe),
    array(res_zahl($grundm + $zollm), res_zahl($grundd + $zolld), res_zahl($grundi + $zolli), res_zahl($grunde + $zolle)));
echo res_rohstoffzeile(res_hilfe($resource_lang['plussekartibonus'], $resource_lang['hilfe10']),
    array(res_zahl($sa_grund[0]), res_zahl($sa_grund[1]), res_zahl($sa_grund[2]), res_zahl($sa_grund[3])));
echo res_rohstoffzeile('<b>Ertrag je WT</b>',
    array('<b>'.res_zahl($resges[0]).'</b>', '<b>'.res_zahl($resges[1]).'</b>', '<b>'.res_zahl($resges[2]).'</b>', '<b>'.res_zahl($resges[3]).'</b>'), 'bk-zahl res-ertrag', 'bk-label res-ertrag');
echo '</div>';

//planetarer Ertrag aufgeschlüsselt
echo '<details class="res-details"><summary>Planetarer Rohstoffertrag im Detail</summary><div class="bk-energie res-tabelle">';
echo res_rohstoffzeile('Grundwert', array(res_zahl($grundm - $spezim - $siegelm), res_zahl($grundd - $spezid - $siegeld), res_zahl($grundi - $spezii - $siegeli), res_zahl($grunde - $spezie - $siegele)));
echo res_rohstoffzeile('Zolleinnahmen <small>('.res_zahl($agent_lost).' Agenten)</small>', array(res_zahl($zollm), res_zahl($zolld), res_zahl($zolli), res_zahl($zolle)));
echo res_rohstoffzeile('Spezialisierungen', array(res_zahl($spezim), res_zahl($spezid), res_zahl($spezii), res_zahl($spezie)));
if ($siegelbonus > 0) {
    echo res_rohstoffzeile(strtr($siegel_lang['resource_tooltip'], ['{PCT}' => round($siegelbonus * 100)]), array(res_zahl($siegelm), res_zahl($siegeld), res_zahl($siegeli), res_zahl($siegele)));
}
echo '</div></details>';

echo '<div class="bk-fuss"><span class="bk-summe">Summe <b id="res-summe">'.($keym + $keyd + $keyi + $keye).'</b> % von 100 %</span>';
echo '<button type="submit" name="change" value="Schl&uuml;ssel &auml;ndern" class="mod-btn">Verteilung speichern</button></div>';
echo '</form>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
// rohstoffhandel - eingabemöglichkeit
//////////////////////////////////////////////////////////////////////////////
rahmen_oben('Allianz-Notfallrohstoffkonverter');
echo '<div class="mod res">';

if ($ally_has_notfallkonverter && hasTech($pt, 4)) {
    //die Sektorsteuer kommt beim Tausch zur Verlustleistung hinzu (wie oben beim Tausch begrenzt)
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT ssteuer FROM de_sector WHERE sec_id=?", [$sector]);
    $row_steuer = mysqli_fetch_assoc($db_daten);
    $res_sektorsteuer = max(0, min(5, intval($row_steuer['ssteuer'] ?? 0)));

    $res_optionen = function ($gewaehlt) {
        $html = '';
        foreach (array(1 => 'Multiplex', 2 => 'Dyharra', 3 => 'Iradium', 4 => 'Eternium') as $wert => $name) {
            $html .= '<option value="'.$wert.'"'.($wert == $gewaehlt ? ' selected' : '').'>'.$name.'</option>';
        }
        return $html;
    };

    echo '<div class="res-text">Wandelt Rohstoffe in andere um. Verlust: <b>'.$handelssteuersatz.' %</b>'.($res_sektorsteuer > 0 ? ' + <b>'.$res_sektorsteuer.' %</b> Sektorsteuer (geht in die Sektorkasse)' : '').'.</div>';
    echo '<form action="resource.php" method="POST" class="res-konverter">';
    echo '<div class="res-wahl"><span>Ich ben&ouml;tige</span><input type="text" id="rh_amount" name="rh_amount" value="" maxlength="16" inputmode="numeric" autocomplete="off" class="mod-eingabe"><select name="rh_v1" id="rh_v1" class="mod-eingabe">'.$res_optionen(1).'</select></div>';
    echo '<div class="res-wahl"><span>und bezahle mit</span><input type="text" id="rh_cost" name="rh_cost" value="" maxlength="16" inputmode="numeric" autocomplete="off" class="mod-eingabe"><select name="rh_v2" id="rh_v2" class="mod-eingabe">'.$res_optionen(2).'</select></div>';
    echo '<div class="bk-fuss"><span></span><button type="submit" name="startrestrade" value="Rohstoffe umwandeln" class="mod-btn">Rohstoffe umwandeln</button></div>';
    echo '</form>';
} else {
    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=4";
    $db_tech = mysqli_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_array($db_tech);

    echo '<div class="mod-leer">Du ben&ouml;tigst eine Allianz mit Notfallmateriekonverter und folgendes Geb&auml;ude: '.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'</div>';
}

echo '</div>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
// sektorlager
//////////////////////////////////////////////////////////////////////////////
if (hasTech($pt, 3)) { //wenn planetare boerse vorhanden, dann ist eine einzahlung ins sektorlager möglich
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id=?", [$sector]);
    $row = mysqli_fetch_assoc($db_daten);

    rahmen_oben($resource_lang['uebersichtseklager']);
    echo '<form action="resource.php" method="POST" class="mod res">';
    echo '<div class="res-sektorlager">';
    echo '<span class="bk-spalte">'.$resource_lang['rohstoff'].'</span><span class="bk-spalte bk-zahl">'.$resource_lang['sektorlager'].'</span><span class="bk-spalte bk-zahl">Einzahlen</span>';
    foreach (array('Multiplex' => array('mtr', $row['restyp01']), 'Dyharra' => array('dtr', $row['restyp02']), 'Iradium' => array('itr', $row['restyp03']),
        'Eternium' => array('etr', $row['restyp04']), 'Tronic' => array('ttr', $row['restyp05'])) as $name => $feld) {
        echo '<span>'.$name.'</span><span class="bk-zahl">'.res_zahl($feld[1]).'</span>';
        echo '<span class="bk-zahl"><input type="text" name="'.$feld[0].'" value="" maxlength="8" inputmode="numeric" autocomplete="off" class="mod-eingabe res-einzahlung"></span>';
    }
    echo '</div>';
    echo '<div class="bk-fuss"><span class="bk-leise">Jede Einzahlung wird dem Sektorkommandanten gemeldet.</span>';
    echo '<button type="submit" name="trans" value="'.$resource_lang['transferieren'].'" class="mod-btn">'.$resource_lang['transferieren'].'</button></div>';

    //was der Sektor mit dem Lager bauen kann
    echo '<details class="res-details"><summary>'.$resource_lang['sektorkosten'].'</summary><div class="res-kosten-liste">';
    echo '<span class="bk-spalte"></span><span class="bk-spalte bk-zahl">M</span><span class="bk-spalte bk-zahl">D</span><span class="bk-spalte bk-zahl">I</span><span class="bk-spalte bk-zahl">E</span><span class="bk-spalte bk-zahl">T</span>';
    foreach ($sektorkosten as $kosten) {
        echo '<span>'.$kosten[0].'</span>';
        for ($i = 1; $i <= 5; $i++) {
            echo '<span class="bk-zahl'.($kosten[$i] == 0 ? ' bk-null' : '').'">'.number_format($kosten[$i], 0, ",", ".").'</span>';
        }
    }
    echo '</div></details>';
    echo '</form>';
    rahmen_unten();
}

//////////////////////////////////////////////////////////////////
// Ressourcenlager
//////////////////////////////////////////////////////////////////
rahmen_oben('Dein Lager');
echo '<div class="mod res"><div class="res-bestand">';

//Tronic
$tronic_hinweis = '';
if (hasTech($pt, 160)) {
    $tronicertrag = 1;
    $tronicertrag += intval(getArtefactAmountByUserId($_SESSION['ums_user_id'], 21));
    $tronic_hinweis = '<span class="mod-chip mod-chip-gruen" title="Tronic&Jeden 20. Wirtschaftstick +'.$tronicertrag.'">+'.$tronicertrag.' alle 20 WT</span>';
}

foreach (array('Multiplex' => array($restyp01, ''), 'Dyharra' => array($restyp02, ''), 'Iradium' => array($restyp03, ''), 'Eternium' => array($restyp04, ''), 'Tronic' => array($restyp05, $tronic_hinweis)) as $name => $wert) {
    echo '<div class="res-posten"><span>'.$name.'</span>'.$wert[1].'<b>'.number_format(floor($wert[0]), 0, ",", ".").'</b></div>';
}

//weitere items aus der DB auslesen
$sql = "SELECT * FROM de_user_storage LEFT JOIN de_item_data ON(de_user_storage.item_id=de_item_data.item_id)
	WHERE de_user_storage.user_id='".$_SESSION['ums_user_id']."' ORDER BY item_sort_order ASC, item_name ASC";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);

while ($row = mysqli_fetch_array($db_daten)) {
    //check auf Änderung im Tick
    if ($row['item_wt_change'] > 0) {
        $item_change_wt = '<span class="mod-chip mod-chip-gruen" title="'.$row['item_name'].'&Jeden Wirtschaftstick +'.number_format($row['item_wt_change'], 0, ",", ".").'">+'.number_format($row['item_wt_change'], 0, ",", ".").' je WT</span>';
    } else {
        $item_change_wt = '';
    }

    echo '<div class="res-posten"><span>'.$row['item_name'].'</span>'.$item_change_wt.'<b>'.number_format($row['item_amount'], 0, ",", ".").'</b></div>';
}

echo '</div></div>';
rahmen_unten();
?>

<script>
<?php
//Verlust beim Konverter: Grundsatz und Sektorsteuer, wie beim Tausch
echo 'var p='.($handelssteuersatz + ($res_sektorsteuer ?? 0)).';';
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

//Kosten der eingegebenen Anzahl; rot, wenn die Rohstoffe nicht reichen
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

	$("#colmcost").text(number_format(Math.round(mcost))).toggleClass('res-zu-teuer', mcost>hasres[0]);
	$("#coldcost").text(number_format(Math.round(dcost))).toggleClass('res-zu-teuer', dcost>hasres[1]);
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

$(function(){
	//Kollektorbau: Kosten beim Tippen, Max. trägt die größte bezahlbare Anzahl ein
	$('#b_col').on('input', function(){ calccolcost($(this).data('kollis')); });
	$('#res-max').on('click', function(){ $('#b_col').val($(this).data('max')).trigger('input'); });
	//Konverter: die jeweils andere Menge mitrechnen
	$('#rh_amount').on('input', function(){ rh_calc(0); });
	$('#rh_cost').on('input', function(){ rh_calc(1); });
	$('#rh_v1, #rh_v2').on('change', function(){ rh_calc(0); });
	//Summe des Energieverteilungsschlüssels
	$('.res-schluessel').on('input', function(){
		var s = 0;
		$('.res-schluessel').each(function(){ s += parseInt(this.value, 10) || 0; });
		$('#res-summe').text(s).parent().toggleClass('bk-summe-falsch', s != 100);
	});
});
</script>
</body>
</html>
