<?php
include "inc/header.inc.php";
include 'lib/transaction.lib.php';
include "lib/kampfbericht.lib.php";

$kkollies = 0;
$exp = 0;
include 'inc/lang/'.$sv_server_lang.'_kampfbericht.lib.lang.php';
include "inc/userartefact.inc.php";
include 'inc/lang/'.$sv_server_lang.'_secret.lang.php';
include 'functions.php';
include 'inc/sabotage.inc.php';
include 'inc/artefakt.inc.php';
include "tickler/kt_einheitendaten.php";

$pt = loadPlayerTechs($_SESSION['ums_user_id']);

//print_r($pt);

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sonde = $row["sonde"];
$agent = $row["agent"];
$sector = $row["sector"];
$system = $row["system"];
$gr01 = $restyp01;
$gr02 = $restyp02;
$gr03 = $restyp03;
$gr04 = $restyp04;
$scanhistory = $row['scanhistory'];
$mysc1 = $row["sc1"];
$mysc2 = $row["sc2"];
$mysc3 = $row["sc3"];
$mysc4 = $row["sc4"];

$own_ally_id = -1;
$ownally = '';
if ($row["ally_id"] > 0 and $row["status"] == 1) {
    $own_ally_id = $row['ally_id'];
    $ownally = $row["allytag"];
}

$ownsector = $sector;

//Baukosten definieren
$einheiten_daten[110]['kosten'] = array(500,500,0,0,0);//Sonde
$einheiten_daten[110]['bz'] = 2;
$einheiten_daten[111]['kosten'] = array(500,500,200,100,0);//Agent
$einheiten_daten[111]['bz'] = 8;

//Schiffspunkte f�r die Kampfbericht-lib
for ($rasse = 1;$rasse <= 5;$rasse++) {
    $schiffspunkte[$rasse - 1][0] = $unit[$rasse - 1][0][4];//jäger
    $schiffspunkte[$rasse - 1][1] = $unit[$rasse - 1][1][4];//jagdboot
    $schiffspunkte[$rasse - 1][2] = $unit[$rasse - 1][2][4];//zerstörer
    $schiffspunkte[$rasse - 1][3] = $unit[$rasse - 1][3][4];//kreuzer
    $schiffspunkte[$rasse - 1][4] = $unit[$rasse - 1][4][4];//schlachtschiff
    $schiffspunkte[$rasse - 1][5] = $unit[$rasse - 1][5][4];//bomber
    $schiffspunkte[$rasse - 1][6] = $unit[$rasse - 1][6][4];//transmitterschiff
    $schiffspunkte[$rasse - 1][7] = $unit[$rasse - 1][7][4];//trägerschiff
    $schiffspunkte[$rasse - 1][8] = $unit[$rasse - 1][8][4];//frachter
    $schiffspunkte[$rasse - 1][9] = $unit[$rasse - 1][9][4];//titan
    //türme
    $schiffspunkte[$rasse - 1][10] = $unit[$rasse - 1][10][4];
    $schiffspunkte[$rasse - 1][11] = $unit[$rasse - 1][11][4];
    $schiffspunkte[$rasse - 1][12] = $unit[$rasse - 1][12][4];
    $schiffspunkte[$rasse - 1][13] = $unit[$rasse - 1][13][4];
    $schiffspunkte[$rasse - 1][14] = $unit[$rasse - 1][14][4];
}


//überprüfen ob die allianz-informationsphalanx verfügbar ist
$ally_bldg2 = -1;
if ($own_ally_id > 0) {
    $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id=?", [$own_ally_id]);
    $row     = mysqli_fetch_array($result);
    $ally_bldg2 = $row["bldg2"];
}

//maximalen tick auslesen
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
$maxroundtick = $row["tick"];

//userartefakte auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=3 AND user_id=?", [$_SESSION['ums_user_id']]);
$artbonusatt = 0;
$artbonusdeff = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonusatt = $artbonusatt + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}

//userartefakte auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=4 AND user_id=?", [$_SESSION['ums_user_id']]);
$artbonusdeff = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonusdeff = $artbonusdeff + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}

//userartefakte auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=5 AND user_id=?", [$_SESSION['ums_user_id']]);
$artbonusbuild = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonusbuild = $artbonusbuild + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}
if ($artbonusbuild > 6) {
    $artbonusbuild = 6;
}

$infostring = 'Kostenreduzierung '.$ua_name[4].'-Artefakte: '.number_format($artbonusbuild, 2, ",", ".").'% (max. 6,00%)';

//angriffskraft
//if($defense_bonus_feuerkraft[0]>0)$infostring.='- '.$defense_lang['angriffskraftbonus'].': '.$defense_bonus_feuerkraft[0].'% '.$defense_lang['wahrscheinlichkeit'].': '.$defense_bonus_feuerkraft[1].'%';
$buildstatus = $infostring;

////////////////////////////////////////////////////
////////////////////////////////////////////////////
//verluste definieren in prozent
//$angreifer_verlust_min=2;
//$angreifer_verlust_max=6;

//0 flottenaufstellung
$index = 0;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 6;

//1 flottenauftrag
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 6;

//2 verteidigungsanlagen
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 6;

//3 nachrichten
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 6;

//4 entwicklungen
$index++;
$angreifer_verlust_fail_min[$index] = 1;
$angreifer_verlust_fail_max[$index] = 4;

//5 allianztag
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 6;

//6 systemstatus
$index++;
$angreifer_verlust_fail_min[$index] = 4;
$angreifer_verlust_fail_max[$index] = 10;

//SABOTAGE
//7 weniger kollektoroutput
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 4;
$angreifer_verlust_win_min[$index] = 1;
$angreifer_verlust_win_max[$index] = 2;

//8 raumwerft
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 4;
$angreifer_verlust_win_min[$index] = 1;
$angreifer_verlust_win_max[$index] = 2;

//9 verteidigungszentrum
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 4;
$angreifer_verlust_win_min[$index] = 1;
$angreifer_verlust_win_max[$index] = 2;

//handel
$index++;
$angreifer_verlust_fail_min[$index] = 2;
$angreifer_verlust_fail_max[$index] = 4;
$angreifer_verlust_win_min[$index] = 1;
$angreifer_verlust_win_max[$index] = 2;

////////////////////////////////////////////////////
////////////////////////////////////////////////////
?>
<!doctype html>
<html>
<head>
<title><?php echo $secret_lang['geheimdienst'];?></title>
<?php include "cssinclude.php";
echo '<script type="text/javascript">var ab='.$artbonusbuild.';</script>';
echo '<script src="js/produktion'.$_SESSION['ums_rasse'].'.js" type="text/javascript"></script>';
echo '<script language="javascript">';
echo 'var eb = new Array();';

unset($tooltips);
$tooltips[0] = $secret_lang['sonde'].'&'.$secret_lang['hilfesonde'];
$tooltips[1] = $secret_lang['agent'].'&'.$secret_lang['hilfeagent'];

$bstr = $secret_lang['aggibonus'].$ua_name[2].$secret_lang['artefakte'].number_format($artbonusatt, 2, ",", ".").
    '%<br><br>'.$secret_lang['aggibonus2'].$ua_name[3].$secret_lang['artefakte'].number_format($artbonusdeff, 2, ",", ".").'%';


echo '</script>';

?>
<script>
function insertagent(sec,sys)
{
  if(document.agent.zsec2)
  {
     document.getElementById("zsec2").value = sec;
     document.getElementById("zsys2").value = sys;
     //zum Formular springen, auf dem Handy liegt es sonst außer Sicht
     document.getElementById("zsec2").scrollIntoView({block: "center", behavior: "smooth"});
  }
  else
  {
      alert("<?php echo $secret_lang['keineagenten'];?>");
  }
}

function insertsonde(sec,sys)
{
    if(document.sonde.zsec1) {
        document.getElementById("zsec1").value = sec;
        document.getElementById("zsys1").value = sys;
        document.getElementById("zsec1").scrollIntoView({block: "center", behavior: "smooth"});
    }else{
        alert("<?php echo $secret_lang['keinesonden'];?>");
    }
}
</script>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$zsec1 = isset($_REQUEST['zsec1']) ? $_REQUEST['zsec1'] : '';
$zsys1 = isset($_REQUEST['zsys1']) ? $_REQUEST['zsys1'] : '';

$zsec2 = isset($_REQUEST['zsec2']) ? $_REQUEST['zsec2'] : '';
$zsys2 = isset($_REQUEST['zsys2']) ? $_REQUEST['zsys2'] : '';

//nur Zahlen: die Koordinaten landen im Verlauf (scanhistory) und von dort im HTML/JavaScript
$copy_zsys2 = intval($zsys2);
$copy_zsec2 = intval($zsec2);

//Agenten/Sonden bauen
if (hasTech($pt, 9)) {
    if (isset($_POST["b110"]) || isset($_POST["b111"])) {//ja, es wurde ein button gedrueckt
        //transaktionsbeginn
        if (setLock($_SESSION['ums_user_id'])) {
            //Rohstoffe innerhalb der Sperre neu laden, sonst bezahlt ein paralleler Bauauftrag mit veralteten Beständen
            $row_res = mysqli_fetch_assoc(mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]));
            $gr01 = $restyp01 = $row_res['restyp01'];
            $gr02 = $restyp02 = $row_res['restyp02'];
            $gr03 = $restyp03 = $row_res['restyp03'];
            $gr04 = $restyp04 = $row_res['restyp04'];
            for ($i = 110; $i <= 111; $i++) {
                $h = intval($_POST['b'.$i] ?? 0);
                if ($h >= 1) { //es wurde ein wert eingegeben und er ist ok h=anzahl des auftrags
                    $tech_id = $i;
                    //baukosten
                    $benrestyp01 = $einheiten_daten[$tech_id]['kosten'][0] - round($einheiten_daten[$tech_id]['kosten'][0] * $artbonusbuild / 100);
                    $benrestyp02 = $einheiten_daten[$tech_id]['kosten'][1] - round($einheiten_daten[$tech_id]['kosten'][1] * $artbonusbuild / 100);
                    $benrestyp03 = $einheiten_daten[$tech_id]['kosten'][2] - round($einheiten_daten[$tech_id]['kosten'][2] * $artbonusbuild / 100);
                    $benrestyp04 = $einheiten_daten[$tech_id]['kosten'][3] - round($einheiten_daten[$tech_id]['kosten'][3] * $artbonusbuild / 100);
                    $benrestyp05 = $einheiten_daten[$tech_id]['kosten'][4] - round($einheiten_daten[$tech_id]['kosten'][4] * $artbonusbuild / 100);

                    $tech_ticks = $einheiten_daten[$tech_id]['bz'];
                    //schauen obn man ihn bauen darf
                    if (hasTech($pt, $tech_id)) {
                        $fehlermsg = '';
                    } else {
                        $h = 0;
                        $fehlermsg = '<font color="FF0000">'.$secret_lang['fehlervorbedingung'];
                    }

                    $z = 0;
                    for ($k = 1; $k <= $h; $k++) {
                        if ($fehlermsg == '' && $benrestyp01 <= $restyp01 && $benrestyp02 <= $restyp02 && $benrestyp03 <= $restyp03 && $benrestyp04 <= $restyp04 && $benrestyp05 <= $restyp05) {
                            $restyp01 = $restyp01 - $benrestyp01;
                            $restyp02 = $restyp02 - $benrestyp02;
                            $restyp03 = $restyp03 - $benrestyp03;
                            $restyp04 = $restyp04 - $benrestyp04;
                            $z++;
                        } else {
                            break;
                        }
                    }

                    //gibt $z sonden/agenten in auftrag
                    if ($z > 0) {
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES (?, ?, ?, ?)", [$_SESSION['ums_user_id'], $i, $z, $tech_ticks]);
                        write2agentlog($_SESSION['ums_user_id'], 'build', $z);
                    }
                }
            }

            //aktualisiert die rohstoffe
            $gr01 = $gr01 - $restyp01;
            $gr02 = $gr02 - $restyp02;
            $gr03 = $gr03 - $restyp03;
            $gr04 = $gr04 - $restyp04;
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01 = restyp01 - ?, restyp02 = restyp02 - ?, restyp03 = restyp03 - ?, restyp04 = restyp04 - ? WHERE user_id = ?", [$gr01, $gr02, $gr03, $gr04, $_SESSION['ums_user_id']]);
	
	

            //transaktionsende
            $erg = releaseLock($_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
            if ($erg) {
                //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
            } else {
                print("$secret_lang[transerror1]<br><br><br>");
            }
        }// if setlock-ende
        else {
            echo '<br><font color="#FF0000">'.$secret_lang['transerror2'].'</font><br><br>';
        }

    }
}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

//geheimdienst deaktiviert?
if (isset($sv_deactivate_secret) && $sv_deactivate_secret == 1) {
    echo '<br><div class="info_box text2">Auf diesem Server ist der Geheimdienst deaktiviert.</div>';
    die('</body></html>');
}


if (!hasTech($pt, 9)) {
    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=9";
    $db_tech = mysqli_execute_query($GLOBALS['dbi'], $techcheck, []);
    $row_techcheck = mysqli_fetch_array($db_tech);

    //echo $secret_lang[eswirdeine].$row_techcheck[tech_name].$secret_lang[benoetigt];
    echo '<br>';
    rahmen_oben('Fehlende Technologie');
    echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=9" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_9.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'</td>
	</tr>';
    echo '</table>';
    rahmen_unten();
} else {
    //zielkoordinaten trimmen
    $zsec1 = intval(trim($zsec1));
    $zsys1 = intval(trim($zsys1));
    $zsec2 = intval(trim($zsec2));
    $zsys2 = intval(trim($zsys2));

    if (!empty($zsec1)) {
        if ($zsec1 == 1 || $ownsector == 1) {
            $zsec1 = '-1';
        }
    }

    if (!empty($zsec2)) {
        if ($zsec2 == 1 or $ownsector == 1) {
            $zsec2 = '-1';
        }
    }

    //playerstatus setzen
    $showscanhistory = 0;
    if (isset($zsec1) and isset($zsys1) and isset($_POST['ps'])) {
        //playerstatus festellen
        $psstr = trim($_POST['ps']);
        if ($psstr == $secret_lang['ps0']) {
            $ps = 0;
        } elseif ($psstr == $secret_lang['ps1']) {
            $ps = 1;
        } elseif ($psstr == $secret_lang['ps2']) {
            $ps = 2;
        } else {
            $ps = 0;
        }

        //zieluserid rausfinden
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, spielername, rasse FROM de_user_data WHERE sector=? AND `system`=?", [$zsec1, $zsys1]);
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {//die koordinaten stimmen, gib die daten aus
            $row = mysqli_fetch_array($db_daten);
            $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
            //db updaten
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET ps=? WHERE user_id=? AND zuser_id=?", [$ps, $_SESSION['ums_user_id'], $uid]);
            //nach dem update die scanhistory anzeigen
            $showscanhistory = 1;
        }
    }

    //beim agentenziel bei herkunft vom sektor auch bei den sonden die koordinaten hinterlegen
    if (isset($_REQUEST['a']) && $_REQUEST['a'] == 'a') {
        $zsec1 = $zsec2;
        $zsys1 = $zsys2;
    }


    //alle Berichte (Scanverlauf, Sonde, Agenten) in einer Hülle mit eigenen Stilen
    echo '<div class="geh-berichte">';

    //scanhistory ausgeben
    if ((isset($_GET["a"]) && $_GET["a"] == 'd') || $showscanhistory == 1) {
        //agentenkoordinaten vorbelegen
        $zsec2 = $zsec1;
        $zsys2 = $zsys1;
        //user_id des ziels auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, spielername, rasse FROM de_user_data WHERE sector=? AND `system`=?", [$zsec1, $zsys1]);
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {//die koordinaten stimmen, gib die daten aus
            echo '<form action="secret.php" method="POST">';

            $row = mysqli_fetch_array($db_daten);
            $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
            $spielername = $row["spielername"];
            $zrasse = $row["rasse"];
            //scandaten aus der db holen
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_scan WHERE user_id=? AND zuser_id=?", [$_SESSION['ums_user_id'], $uid]);
            $num = mysqli_num_rows($db_daten);
            if ($num != 1) {//datensatz vorhanden, falls nicht einen anlegen und es nochmal versuchen
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_scan (user_id, zuser_id) VALUES (?, ?)", [$_SESSION['ums_user_id'], $uid]);
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_scan WHERE user_id=? AND zuser_id=?", [$_SESSION['ums_user_id'], $uid]);
            }
            $row = mysqli_fetch_array($db_daten);
            //playerstatus
            $ps = $row["ps"];
            //rahmen oben
            echo '<table border="0" cellpadding="0" cellspacing="0">
			  <tr>
			  <td width="13" height="37" class="rol">&nbsp;</td>
			  <td align="center" class="ro">Geheimdienstinformationen ['.$spielername.' ('.$zsec1.':'.$zsys1.')]</td>
			  <td width="13" class="ror">&nbsp;</td>
			  </tr>
			  <tr>
			  <td class="rl">&nbsp;</td><td>';
            //die daten aufbereiten
            //rasse
            $rasse = '?';
            if ($row["rasse"] == 1) {
                $rasse = 'Ewiger';
            } elseif ($row["rasse"] == 2) {
                $rasse = 'Ishtar';
            } elseif ($row["rasse"] == 3) {
                $rasse = 'K&#180;Tharr';
            } elseif ($row["rasse"] == 4) {
                $rasse = 'Z&#180;tah-ara';
            } elseif ($row["rasse"] == 5) {
                $rasse = 'DX61a23';
            }
            //allianz
            $allianz = '?';
            if ($row["allytag"] != '') {
                $allianz = $row["allytag"];
            }
            if ($row["atime"] > 0) {
                $allianz .= ' ('.date("d.m.Y - G:i", $row["atime"]).')';
            }
            //schiffs�bersicht
            $ftime = '?';
            if ($row["ftime"] > 0) {
                $ftime = date("d.m.Y - G:i", $row["ftime"]);
            }
            $e81 = '?';
            if ($row["ftime"] > 0) {
                $e81 = number_format($row["e81"], 0, "", ".");
            }
            $e82 = '?';
            if ($row["ftime"] > 0) {
                $e82 = number_format($row["e82"], 0, "", ".");
            }
            $e83 = '?';
            if ($row["ftime"] > 0) {
                $e83 = number_format($row["e83"], 0, "", ".");
            }
            $e84 = '?';
            if ($row["ftime"] > 0) {
                $e84 = number_format($row["e84"], 0, "", ".");
            }
            $e85 = '?';
            if ($row["ftime"] > 0) {
                $e85 = number_format($row["e85"], 0, "", ".");
            }
            $e86 = '?';
            if ($row["ftime"] > 0) {
                $e86 = number_format($row["e86"], 0, "", ".");
            }
            $e87 = '?';
            if ($row["ftime"] > 0) {
                $e87 = number_format($row["e87"], 0, "", ".");
            }
            $e88 = '?';
            if ($row["ftime"] > 0) {
                $e88 = number_format($row["e88"], 0, "", ".");
            }
            $e89 = '?';
            if ($row["ftime"] > 0) {
                $e89 = number_format($row["e89"], 0, "", ".");
            }
            $e90 = '?';
            if ($row["ftime"] > 0) {
                $e90 = number_format($row["e90"], 0, "", ".");
            }

            //turmübersicht
            $dtime = '?';
            if ($row["dtime"] > 0) {
                $dtime = date("d.m.Y - G:i", $row["dtime"]);
            }
            $e100 = '?';
            if ($row["dtime"] > 0) {
                $e100 = number_format($row["e100"], 0, "", ".");
            }
            $e101 = '?';
            if ($row["dtime"] > 0) {
                $e101 = number_format($row["e101"], 0, "", ".");
            }
            $e102 = '?';
            if ($row["dtime"] > 0) {
                $e102 = number_format($row["e102"], 0, "", ".");
            }
            $e103 = '?';
            if ($row["dtime"] > 0) {
                $e103 = number_format($row["e103"], 0, "", ".");
            }
            $e104 = '?';
            if ($row["dtime"] > 0) {
                $e104 = number_format($row["e104"], 0, "", ".");
            }

            //sondenbericht
            $stime = '?';
            if ($row["stime"] > 0) {
                $stime = date("d.m.Y - G:i", $row["stime"]);
            }
            $score = '?';
            if ($row["stime"] > 0) {
                $score = number_format($row["score"], 0, "", ".");
            }
            $fleet = '?';
            if ($row["stime"] > 0) {
                $fleet = number_format($row["fleet"], 0, "", ".");
            }
            $defense = '?';
            if ($row["stime"] > 0) {
                $defense = number_format($row["defense"], 0, "", ".");
            }
            $build = '?';
            if ($row["stime"] > 0) {
                $build = number_format($row["build"], 0, "", ".");
            }
            $col = '?';
            if ($row["stime"] > 0) {
                $col = number_format($row["col"], 0, "", ".");
            }
            $buildings = '?';
            if ($row["stime"] > 0) {
                $buildings = number_format($row["buildings"], 0, "", ".");
            }
            $restyp01 = '?';
            if ($row["stime"] > 0) {
                $restyp01 = number_format($row["restyp01"], 0, "", ".");
            }
            $restyp02 = '?';
            if ($row["stime"] > 0) {
                $restyp02 = number_format($row["restyp02"], 0, "", ".");
            }
            $restyp03 = '?';
            if ($row["stime"] > 0) {
                $restyp03 = number_format($row["restyp03"], 0, "", ".");
            }
            $restyp04 = '?';
            if ($row["stime"] > 0) {
                $restyp04 = number_format($row["restyp04"], 0, "", ".");
            }
            $restyp05 = '?';
            if ($row["stime"] > 0) {
                $restyp05 = number_format($row["restyp05"], 0, "", ".");
            }

            //pa-check

            //die daten ausgeben
            echo '<table width=570 border="0" cellpadding="0" cellspacing="0">';
            //spalte 1
            echo '<tr><td width="50%" valign="top">';
            echo '<table border="0" cellpadding="0">';
            //rasse
            echo '<tr class="cell1"><td colspan="2"><b>'.$secret_lang['rasse'].':</b> '.$rasse.'</td></tr>';
            //schiffs�bersicht
            echo '<tr class="cell"><td width="25%"><b>'.$secret_lang['schiffsuebersicht'].'</b></td><td align="center" width="25%">'.$ftime.'</td>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][0].'</td><td align="center">'.$e81.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][1].'</td><td align="center">'.$e82.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][2].'</td><td align="center">'.$e83.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][3].'</td><td align="center">'.$e84.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][4].'</td><td align="center">'.$e85.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][5].'</td><td align="center">'.$e86.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][6].'</td><td align="center">'.$e87.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][7].'</td><td align="center">'.$e88.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][8].'</td><td align="center">'.$e89.'</td></tr>';
            echo '<tr class="cell"><td>'.$rassennamen[$zrasse - 1][9].'</td><td align="center">'.$e90.'</td></tr>';
            //turm�bersicht
            echo '<tr class="cell"><td><b>'.$secret_lang['turmuebersicht'].'</b></td><td align="center">'.$dtime.'</td></tr>';
            echo '<tr class="cell"><td>'.$turmnamen[$zrasse - 1][0].'</td><td align="center">'.$e100.'</td></tr>';
            echo '<tr class="cell"><td>'.$turmnamen[$zrasse - 1][1].'</td><td align="center">'.$e101.'</td></tr>';
            echo '<tr class="cell"><td>'.$turmnamen[$zrasse - 1][2].'</td><td align="center">'.$e102.'</td></tr>';
            echo '<tr class="cell"><td>'.$turmnamen[$zrasse - 1][3].'</td><td align="center">'.$e103.'</td></tr>';
            echo '<tr class="cell"><td>'.$turmnamen[$zrasse - 1][4].'</td><td align="center">'.$e104.'</td></tr>';
            echo '</table>';
            //spalte 2
            echo '</td><td width="50%" valign="top">';
            echo '<table border="0" cellpadding="0">';
            //allianz
            echo '<tr class="cell1"><td colspan="2"><b>'.$secret_lang['allytag'].':</b> '.$allianz.'</td></tr>';
            //sondenbericht
            echo '<tr class="cell1"><td width="25%"><b>Sondenbericht</b></td><td align="center" width="25%">'.$stime.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['punkte'].'</td><td align="center">'.$score.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['schiffseinheiten'].'</td><td align="center">'.$fleet.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['verteidigungsanlagen'].'</td><td align="center">'.$defense.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['einheitenimbau'].'</td><td align="center">'.$build.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['kollektoren'].'</td><td align="center">'.$col.'</td></tr>';
            //echo '<tr class="cell1"><td>'.$secret_lang[gebaeude].'</td><td align="center">'.$buildings.'</td></tr>';
            echo '<tr class="cell1"><td><b>'.$secret_lang['rohstoffe'].'</b></td><td>&nbsp;</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['multiplex'].'</td><td align="center">'.$restyp01.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['dyharra'].'</td><td align="center">'.$restyp02.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['iradium'].'</td><td align="center">'.$restyp03.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['eternium'].'</td><td align="center">'.$restyp04.'</td></tr>';
            echo '<tr class="cell1"><td>'.$secret_lang['tronic'].'</td><td align="center">'.$restyp05.'</td></tr>';
            echo '</table>';
            echo '</td></tr>';

            //playerstatus anzeigen und �nderbar machen
            $ps_checked = '<option selected>'.$secret_lang["ps$ps"].'</option>';

            $hidden = '<input type="hidden" name="zsec1" value="'.$zsec1.'"><input type="hidden" name="zsys1" value="'.$zsys1.'">';

            $button = '<input type="submit" name="pschange" value="'.$secret_lang['pschange'].'">';

            echo '<tr class="cell" align="center"><td colspan="2">'.$hidden.'Spielerstatus: <select name="ps">'.$ps_checked.'<option>'.$secret_lang['ps0'].'</option>
        <option>'.$secret_lang['ps1'].'</option><option>'.$secret_lang['ps2'].'</option></select> '.$button.'</td></tr>';

            //weiter-zu-links
            echo '<tr class="cell" align="center"><td colspan="2">';
            echo '</form>';
            echo '<form name="f'.$zsec1.'" action="sector.php?sf='.$zsec1.'" method="POST">';
            echo '<a href="military.php?se='.$zsec1.'&sy='.$zsys1.'">'.$secret_lang['zummilitaer'].'</a> - ';
            echo '<a href="javascript:document.f'.$zsec1.'.submit()">'.$secret_lang['zursektoransicht'].'</a> - ';
            echo '<a href="secstatus.php">'.$secret_lang['zumsektorstatus'].'</a>';
            echo '</form></td></tr>';

            echo '</table>';

            //rahmen unten
            echo '</td><td width="13" class="rr">&nbsp;</td>
			  </tr>
			  <tr>
			  <td width="13" class="rul">&nbsp;</td>
			  <td class="ru">&nbsp;</td>
			  <td width="13" class="rur">&nbsp;</td>
			  </tr>
			  </table><br>';
        }
    }

    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    //spionage sonde benutzen / sondeneinsatz
    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    if ((isset($_POST["startsonde"]) || isset($_GET["a"]) && $_GET["a"] == 's') && ($zsec1 != '' && $zsys1 != '') && !isset($_POST["b110"]) && !isset($_POST["b111"])
     && hasTech($pt, 9) && hasTech($pt, 110)) {

        if (validDigit($zsec1) && validDigit($zsys1) && $sonde > 0) {

            $zk = $zsec1.':'.$zsys1;
            $ak = $sector.':'.$system;
            if ($zk == $ak) {
                $zsec1 = 0;
            }

            //überprüfen ob evtl. der schild des herakles vorhanden ist
            $herakles = 0;
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector=? AND id=?", [$zsec1, 22]);
            if (mysqli_num_rows($db_daten) == 1) {
                $herakles = 1;
            }


            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, col, score, e100, e101, e102, e103, e104, techs, rasse, spielername, restyp01, restyp02, restyp03, restyp04, restyp05, npc FROM de_user_data WHERE sector=? AND `system`=?", [$zsec1, $zsys1]);
            $num = mysqli_num_rows($db_daten);
            if ($num == 1 && $herakles == 0) {//die koordinaten stimmen, gib die daten aus
                $row = mysqli_fetch_array($db_daten);
                //test auf npc
                if ($row['npc'] == 0 || $row['npc'] == 2) {
                    $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
                    $zpunkte = $row["score"];
                    $vertanz = $row["e100"] + $row["e101"] + $row["e102"] + $row["e103"] + $row["e104"];
                    $ztechs = $row["techs"];
                    $zcol = $row["col"];
                    $zres = array($row["restyp01"],$row["restyp02"],$row["restyp03"],$row["restyp04"],$row["restyp05"]);
                    $npc = $row["npc"];

                    $zpt = loadPlayerTechs($uid);

                    $zname = $row["spielername"];
                    $zrasse = $row["rasse"];
                    if ($row["rasse"] == 1) {
                        $rasse = 'Ewiger';
                    } elseif ($row["rasse"] == 2) {
                        $rasse = 'Ishtar';
                    } elseif ($row["rasse"] == 3) {
                        $rasse = 'K&#180;Tharr';
                    } elseif ($row["rasse"] == 4) {
                        $rasse = 'Z&#180;tah-ara';
                    } elseif ($row["rasse"] == 5) {
                        $rasse = 'DX61a23';
                    }

                    //zaehle alle schiffe, die schon vorhanden sind - anfang
                    $fid0 = $uid.'-0';
                    $fid1 = $uid.'-1';
                    $fid2 = $uid.'-2';
                    $fid3 = $uid.'-3';
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
                    $ec81 = 0;
                    $ec82 = 0;
                    $ec83 = 0;
                    $ec84 = 0;
                    $ec85 = 0;
                    $ec86 = 0;
                    $ec87 = 0;
                    $ec88 = 0;
                    $ec89 = 0;
                    $ec90 = 0;
                    while ($row = mysqli_fetch_array($db_daten)) {
                        for ($i = 81; $i <= 90; $i++) {
                            ${"ec$i"} += $row["e$i"];
                        }
                    }
                    $zeinheiten = $ec81 + $ec82 + $ec83 + $ec84 + $ec85 + $ec86 + $ec87 + $ec88 + $ec89 + $ec90;
                    //zaehle alle schiffe, die schon vorhanden sind - ende

                    $anzgeb = 0;
                    //for ($i=1;$i<=39;$i++) if ($ztechs[$i]==1) $anzgeb++;

                    //schiffe im bau
                    $eimbau = 0;
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT anzahl FROM de_user_build WHERE user_id=?", [$uid]);
                    while ($row = mysqli_fetch_array($db_daten)) {
                        $eimbau = $eimbau + $row["anzahl"];
                    }
                    //schauen ob der spieler in den letzten 12 stunden online war
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT last_login FROM de_login WHERE user_id=?", [$uid]);
                    $row = mysqli_fetch_array($db_daten);
                    if (strtotime($row["last_login"]) + 43200 > time()) {
                        $isonline = $secret_lang['ja'];
                    } else {
                        $isonline = $secret_lang['nein'];
                    }
                    if ($npc == 1) {
                        $isonline = $secret_lang['unbekannt'];
                    }


                    //die sondendaten in de_user_scan hinterlegen
                    $savelist = array();
                    $savelist[] = $_SESSION['ums_user_id'];
                    //////////////////////////////////////////////
                    //Allianz-Infophalax Stufe 1
                    //////////////////////////////////////////////
                    //wenn man in einer allianz ist und das passende gebäude vorhanden ist, die daten an die allianzmitglieder weiterleiten
                    if ($own_ally_id > 0 and $ally_bldg2 > 0) {
                        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id=? AND status=1 AND user_id<>?", [$own_ally_id, $_SESSION['ums_user_id']]);
                        while ($row = mysqli_fetch_array($db_daten)) {
                            $savelist[] = $row['user_id'];
                        }

                    }

                    //////////////////////////////////////////////
                    //Allianz-Infophalax Stufe 2
                    //////////////////////////////////////////////
                    //wenn man in einer Allianz ist und das passende Gebäude bei beiden Allianzen vorhanden ist, die Daten an die Meta weiterleiten
                    if ($own_ally_id > 0 and $ally_bldg2 > 1) {
                        //auf meta checken
                        $partner_ally_id = get_allyid_partner($own_ally_id);
                        if ($partner_ally_id > 0) {
                            //Gebäude Stufe vom Metapartner auslesen
                            $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id=?", [$partner_ally_id]);
                            $row     = mysqli_fetch_array($result);
                            $partner_ally_bldg2 = $row["bldg2"];

                            if ($partner_ally_bldg2 > 1) {
                                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id=? AND status=1", [$partner_ally_id]);
                                while ($row = mysqli_fetch_array($db_daten)) {
                                    $savelist[] = $row['user_id'];
                                }
                            }
                        }
                    }

                    //////////////////////////////////////////////
                    //alle user_id der savelist durchgehen
                    //////////////////////////////////////////////
                    for ($i = 0;$i < count($savelist);$i++) {
                        $save_uid = $savelist[$i];
                        //schauen, ob es schon einen eintrag in der scanliste von dem spieler gibt
                        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT rasse FROM de_user_scan WHERE user_id=? AND zuser_id=?", [$save_uid, $uid]);
                        $scan_vorhanden = mysqli_num_rows($db_daten);
                        if ($scan_vorhanden == 0) {
                            //wenn es noch gar keinen scan gibt, dann muß einer in die db
                            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_scan (stime, score, fleet, defense, build, col, buildings, rasse, restyp01, restyp02, restyp03, restyp04, restyp05, user_id, zuser_id) VALUES (UNIX_TIMESTAMP(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$zpunkte, $zeinheiten, $vertanz, $eimbau, $zcol, $anzgeb, $zrasse, $zres[0], $zres[1], $zres[2], $zres[3], $zres[4], $save_uid, $uid]);
                        } else { //daten aktualisieren
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET stime=UNIX_TIMESTAMP(), score=?, fleet=?, defense=?, build=?, col=?, buildings=?, rasse=?, restyp01=?, restyp02=?, restyp03=?, restyp04=?, restyp05=? WHERE user_id=? AND zuser_id=?", [$zpunkte, $zeinheiten, $vertanz, $eimbau, $zcol, $anzgeb, $zrasse, $zres[0], $zres[1], $zres[2], $zres[3], $zres[4], $save_uid, $uid]);
                        }
                    }

                    echo '<br><table border="0" cellpadding="0" cellspacing="1" width="400px">';
                    echo '<tr>';
                    echo '<td colspan="2" class="tc" width="100%"><b>'.$secret_lang['sondenberichtueber'].$zname.' ('.$zsec1.':'.$zsys1.')</b></td>';
                    echo '</tr>';
                    if ($GLOBALS['sv_ang'] != 1) {
                        echo '<tr>';
                        echo '<td class="cc" width="40%">'.$secret_lang['onlineinnerhalb'].'</td>';
                        echo '<td class="cc" width="60%">'.$isonline.'</td>';
                        echo '</tr>';
                    }
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['punkte'].'</td>';
                    echo '<td class="cc">'.number_format($zpunkte, 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['schiffseinheiten'].'</td>';
                    echo '<td class="cc">'.number_format($zeinheiten, 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['verteidigungsanlagen'].'</td>';
                    echo '<td class="cc">'.number_format($vertanz, 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['einheitenimbau'].'</td>';
                    echo '<td class="cc">'.number_format($eimbau, 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['kollektoren'].'</td>';
                    echo '<td class="cc">'.number_format($zcol, 0, "", ".").'</td>';
                    echo '</tr>';
                    //echo '<tr>';
                    //echo '<td class="cc">'.$secret_lang[gebaeude].'</td>';
                    //echo '<td class="cc">'.$anzgeb.'</td>';
                    //echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['rasse'].'</td>';
                    echo '<td class="cc">'.$rasse.'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td colspan="2" class="tc" width="100%">'.$secret_lang['rohstoffe'].'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['multiplex'].'</td>';
                    echo '<td class="cc">'.number_format($zres[0], 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['dyharra'].'</td>';
                    echo '<td class="cc">'.number_format($zres[1], 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['iradium'].'</td>';
                    echo '<td class="cc">'.number_format($zres[2], 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['eternium'].'</td>';
                    echo '<td class="cc">'.number_format($zres[3], 0, "", ".").'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td class="cc">'.$secret_lang['tronic'].'</td>';
                    echo '<td class="cc">'.number_format($zres[4], 0, "", ".").'</td>';
                    echo '</tr>';

                    echo '<tr class="cell" align="center"><td colspan="2">';
                    echo '<form name="f'.$zsec1.'" action="sector.php?sf='.$zsec1.'" method="POST">';
                    echo '<a href="military.php?se='.$zsec1.'&sy='.$zsys1.'">'.$secret_lang['zummilitaer'].'</a> - ';
                    echo '<a href="javascript:document.f'.$zsec1.'.submit()">'.$secret_lang['zursektoransicht'].'</a> - ';
                    echo '<a href="secstatus.php">'.$secret_lang['zumsektorstatus'].'</a>';
                    echo '</form></td></tr>';

                    echo '</table><br><br>';

                    if (hasTech($zpt, 12)) {
                        $w = 45;
                    } elseif (hasTech($zpt, 11)) {
                        $w = 30;
                    } elseif (hasTech($zpt, 10)) {
                        $w = 15;
                    } else {
                        $w = 0;
                    }

                    $r = mt_rand(1, 100);
                    //echo $w.' '.$r;
                    if ($r <= $w) { //sonde wurde entdeckt
                        //nachricht an den account schicken
                        $time = date("YmdHis");
                        $textscanner = $secret_lang['diescannerhaben'].$sector.$secret_lang['diescannerhaben2'];
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 4, ?, ?)", [$uid, $time, $textscanner]);
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uid]);
                    }
                    //eine sonde abziehen
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET sonde = sonde - 1 WHERE user_id = ?", [$_SESSION['ums_user_id']]);
                    $sonde = $sonde - 1;
                    $zsec2 = $zsec1;
                    $zsys2 = $zsys1;
                } else {
                    echo '<div class="info_box text2">Die Technologie der DX61a23 ist zu weit fortgeschritten um dort Informationen sammeln zu k&ouml;nnen.</div><br>';
                }
            } else {
                echo '<div class="info_box text2">'.$secret_lang['falschezielkoords'].'</div><br>';
            }

        } else {
            echo '<<div class="info_box text2">'.$secret_lang['falschewerte'].'</div><br>';
        }
    }

    //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////
    //agenteneinsatz
    //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////
    //nur die Einsatzarten aus dem Auswahlfeld (9 und 11 sind abgeschaltet) und nur als Ganzzahl:
    //ein Wert wie "7.0" träfe im switch case 7, fände aber keine Wartezeit/Verluste in $sv_sabotage
    $etyp = intval($_POST['etyp'] ?? -1);
    $etyp_ok = in_array($etyp, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 10), true);
    $az = isset($_POST['az']) ? intval($_POST['az']) : '';

    if ($etyp_ok && isset($_POST["zsec2"]) && isset($_POST["zsys2"]) && !isset($_POST["b110"]) && !isset($_POST["b111"]) && hasTech($pt, 111)) {

        if (validDigit($zsec2) && validDigit($zsys2) && validDigit($az)) {

            if ($az == '') {
                $az = 0;
            }
            $az = (int)$az;
            if ($az > $agent) {
                $az = $agent;
            }

            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, techs, agent, spielername, rasse, techs, npc, sc1, sc2, sc3, sc4, spec4 FROM de_user_data WHERE sector=? AND `system`=?", [$zsec2, $zsys2]);
            $num = mysqli_num_rows($db_daten);
            $zk = $zsec2.':'.$zsys2;
            $ak = $sector.':'.$system;

            //fix damit man nicht 0:x scannen kann
            if ($zsec2 == 0) {
                $num = 0;
            }

            if ($num == 1 && $az > 0 && $zk <> $ak) { //die koordinaten stimmen
                $row = mysqli_fetch_array($db_daten);
                $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
                $zuid = $uid;
                $zname = $row["spielername"];
                $rasse = $row["rasse"];
                $zrasse = $rasse;
                $zagent = $row["agent"];
                $ztechs = $row["techs"];
                $sc1 = $row["sc1"];
                $sc2 = $row["sc2"];
                $sc3 = $row["sc3"];
                $sc4 = $row["sc4"];
                $ztechs = $row["techs"];
                $zspec4 = $row['spec4'];

                $zpt = loadPlayerTechs($zuid);
                //$zpt=loadPlayerTechs(1);//debug

                ////////////////////////////////////////////////
                //testen der transmitterstrecke
                ////////////////////////////////////////////////

                if ($sector == $zsec2) {//gleicher sektor und man benoetigt nur plan. boerse
                    if (hasTech($pt, 3) and hasTech($zpt, 3)) {
                        $ok = 1;
                    } else {
                        $ok = 3;
                    }
                }

                if ($sector <> $zsec2) {//anderer sektor, beide benötigen gilde
                    if (hasTech($pt, 4) and hasTech($zpt, 4)) {
                        $ok = 1;
                    } else {
                        $ok = 3;
                    }
                }

                //testen ob man die sabotage durchführen kann
                if ($etyp == 7 or $etyp == 8 or $etyp == 9 or $etyp == 10) {
                    if (sabotageallowed($row["user_id"]) == 0) {
                        $ok = 2;
                    }
                }

                //test auf npc, wenn sonst alles ok ist
                if ($ok == 1) {
                    if ($row['npc'] == 1) {
                        $ok = 4;
                        echo '<div class="info_box text2">Deine Agenten kehren unbeschadet vom Einsatz zur&uuml;ck, k&ouml;nnen sich aber an nichts mehr von dem Einsatz erinnern.</div><br>';
                    }
                }
            } else {
                $ok = 2;
            }

            //$ok= 1; //debug

            //überprüfen auf sabotagemöglichkeit
            if ($ok == 2) {
                echo '<div class="info_box text2">'.$secret_lang['einsatzfehlerhaft'].'</div><br>';
            }
            if ($ok == 3) {
                echo '<div class="info_box text2">'.$secret_lang['keinetransverbindung'].'<br>';

                echo '<form name="f'.$zsec2.'" action="sector.php?sf='.$zsec2.'" method="POST">';
                echo '<a href="military.php?se='.$zsec2.'&sy='.$zsys2.'">'.$secret_lang['zummilitaer'].'</a> - ';
                echo '<a href="javascript:document.f'.$zsec2.'.submit()">'.$secret_lang['zursektoransicht'].'</a>';
                echo '</form></div><br>';

                $showmenu = 1;
            }

            if ($ok == 1) {//die koordinaten stimmen und es werden agenten geschickt

                //artefaktabwehr des angegriffenen auslesen
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=4 AND user_id=?", [$uid]);
                $zartbonusdeff = 0;
                while ($row = mysqli_fetch_array($db_daten)) {
                    $zartbonusdeff = $zartbonusdeff + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
                }


                //rassenboni-mali verteilen
                if ($_SESSION['ums_rasse'] == 1 and $rasse == 1) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 1 and $rasse == 2) {
                    $bomalus = 5;
                }
                if ($_SESSION['ums_rasse'] == 1 and $rasse == 3) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 1 and $rasse == 4) {
                    $bomalus = -5;
                }
                if ($_SESSION['ums_rasse'] == 1 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($_SESSION['ums_rasse'] == 2 and $rasse == 1) {
                    $bomalus = -5;
                }
                if ($_SESSION['ums_rasse'] == 2 and $rasse == 2) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 2 and $rasse == 3) {
                    $bomalus = -5;
                }
                if ($_SESSION['ums_rasse'] == 2 and $rasse == 4) {
                    $bomalus = -10;
                }
                if ($_SESSION['ums_rasse'] == 2 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($_SESSION['ums_rasse'] == 3 and $rasse == 1) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 3 and $rasse == 2) {
                    $bomalus = 5;
                }
                if ($_SESSION['ums_rasse'] == 3 and $rasse == 3) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 3 and $rasse == 4) {
                    $bomalus = -5;
                }
                if ($_SESSION['ums_rasse'] == 3 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($_SESSION['ums_rasse'] == 4 and $rasse == 1) {
                    $bomalus = 5;
                }
                if ($_SESSION['ums_rasse'] == 4 and $rasse == 2) {
                    $bomalus = 10;
                }
                if ($_SESSION['ums_rasse'] == 4 and $rasse == 3) {
                    $bomalus = 5;
                }
                if ($_SESSION['ums_rasse'] == 4 and $rasse == 4) {
                    $bomalus = 0;
                }
                if ($_SESSION['ums_rasse'] == 4 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($_SESSION['ums_rasse'] == 5 and $rasse == 1) {
                    $bomalus = 10;
                }
                if ($_SESSION['ums_rasse'] == 5 and $rasse == 2) {
                    $bomalus = 10;
                }
                if ($_SESSION['ums_rasse'] == 5 and $rasse == 3) {
                    $bomalus = 10;
                }
                if ($_SESSION['ums_rasse'] == 5 and $rasse == 4) {
                    $bomalus = 10;
                }
                if ($_SESSION['ums_rasse'] == 5 and $rasse == 5) {
                    $bomalus = 10;
                }

                //test ob der einsatz erfolgreich ist
                if ($zagent == 0) {
                    $w = 98;
                } else {
                    $w = ($az + ($az * $artbonusatt / 100)) / ($zagent + ($zagent * $zartbonusdeff / 100)) * (100 + $bomalus);
                }
                if ($w > 98) {
                    $w = 98;
                }

                //test spezialisierung -x% chance
                if ($zspec4 == 1) {
                    $w = $w - 5;
                }

                //�berpr�fen ob das grab des ra im zielsector ist
                if ($w > $sv_artefakt[20][5]) {
                    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector=? AND id=?", [$zsec2, 21]);
                    if (mysqli_num_rows($db_daten) == 1) {
                        $w = $sv_artefakt[20][5];
                    }
                }

                $r = mt_rand(1, 100);
                //echo '<br>W: '.$w.' R: '.$r.' AZ: '.$az.' ZAGENT: '.$bomalus.'<br>';
                if ($w >= $r) { //der einsatz klappt
                    //die rasse immer in de_user_scan hinterlegen
                    $savelist = array();
                    $savelist[] = $_SESSION['ums_user_id'];
                    //////////////////////////////////////////////
                    //Allianz-Infophalax Stufe 1
                    //////////////////////////////////////////////
                    //wenn man in einer allianz ist und das passende gebäude vorhanden ist, die daten an die allianzmitglieder weiterleiten
                    if ($own_ally_id > 0 and $ally_bldg2 > 0) {
                        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id=? AND status=1 AND user_id<>?", [$own_ally_id, $_SESSION['ums_user_id']]);
                        while ($row = mysqli_fetch_array($db_daten)) {
                            $savelist[] = $row['user_id'];
                        }

                    }

                    //////////////////////////////////////////////
                    //Allianz-Infophalax Stufe 2
                    //////////////////////////////////////////////
                    //wenn man in einer Allianz ist und das passende Gebäude bei beiden Allianzen vorhanden ist, die Daten an die Meta weiterleiten
                    if ($own_ally_id > 0 and $ally_bldg2 > 1) {
                        //auf meta checken
                        $partner_ally_id = get_allyid_partner($own_ally_id);
                        if ($partner_ally_id > 0) {
                            //Gebäude Stufe vom Metapartner auslesen
                            $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id=?", [$partner_ally_id]);
                            $row     = mysqli_fetch_array($result);
                            $partner_ally_bldg2 = $row["bldg2"];

                            if ($partner_ally_bldg2 > 1) {
                                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id=? AND status=1", [$partner_ally_id]);
                                while ($row = mysqli_fetch_array($db_daten)) {
                                    $savelist[] = $row['user_id'];
                                }
                            }
                        }
                    }

                    //////////////////////////////////////////////
                    //alle user_id der savelist durchgehen
                    //////////////////////////////////////////////
                    for ($i = 0;$i < count($savelist);$i++) {
                        $save_uid = $savelist[$i];
                        //schauen, ob es schon einen eintrag in der scanliste von dem spieler gibt
                        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_user_scan WHERE user_id=? AND zuser_id=?", [$save_uid, $uid]);
                        $scan_vorhanden = mysqli_num_rows($db_daten);
                        if ($scan_vorhanden == 0) {
                            //wenn es noch gar keinen scan gibt, dann mu� einer in die db
                            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_scan (user_id, zuser_id) VALUES (?, ?)", [$save_uid, $uid]);
                        }
                        //rasse im geheimdienstbericht hinterlegen, da man diese nach einem agenteneinsatz auf jeden fall kennt
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET rasse=? WHERE user_id=? AND zuser_id=? AND rasse=0", [$rasse, $save_uid, $uid]);
                    }

                    $emsg = '';
                    switch ($etyp) {
                        case 0: //schiffsübersicht
                            //zaehle alle schiffe, die schon vorhanden sind - anfang
                            $fid0 = $uid.'-0';
                            $fid1 = $uid.'-1';
                            $fid2 = $uid.'-2';
                            $fid3 = $uid.'-3';
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
                            $counter = 0;
                            $e81 = 0;
                            $e82 = 0;
                            $e83 = 0;
                            $e84 = 0;
                            $e85 = 0;
                            $e86 = 0;
                            $e87 = 0;
                            $e88 = 0;
                            $e89 = 0;
                            $e90 = 0;
                            while ($row = mysqli_fetch_array($db_daten)) {
                                $schiffe[$counter][0] = $row["e81"];
                                $schiffe[$counter][1] = $row["e82"];
                                $schiffe[$counter][2] = $row["e83"];
                                $schiffe[$counter][3] = $row["e84"];
                                $schiffe[$counter][4] = $row["e85"];
                                $schiffe[$counter][5] = $row["e86"];
                                $schiffe[$counter][6] = $row["e87"];
                                $schiffe[$counter][7] = $row["e88"];
                                $schiffe[$counter][8] = $row["e89"];
                                $schiffe[$counter][9] = $row["e90"];
                                $counter++;
                                //bereite schiffzahlen f�r den scanspeicher vor
                                $e81 += $row["e81"];
                                $e82 += $row["e82"];
                                $e83 += $row["e83"];
                                $e84 += $row["e84"];
                                $e85 += $row["e85"];
                                $e86 += $row["e86"];
                                $e87 += $row["e87"];
                                $e88 += $row["e88"];
                                $e89 += $row["e89"];
                                $e90 += $row["e90"];
                            }
                            //zaehle alle schiffe, die schon vorhanden sind - ende

                            //die daten in de_user_scan hinterlegen
                            for ($i = 0;$i < count($savelist);$i++) {
                                $save_uid = $savelist[$i];
                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET ftime=UNIX_TIMESTAMP(), e81=?, e82=?, e83=?, e84=?, e85=?, e86=?, e87=?, e88=?, e89=?, e90=? WHERE user_id=? AND zuser_id=?", [$e81, $e82, $e83, $e84, $e85, $e86, $e87, $e88, $e89, $e90, $save_uid, $uid]);
                            }

                            //ueberschrift ausgeben
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="560">';
                            echo '<tr>';
                            echo '<td class="tc" colspan=5 width="100%"><b>'.$secret_lang['flottenaufstellungvon'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td class="tc" width="160">'.$secret_lang['schiffstypen'].'</td>';
                            echo '<td class="tc" width="100">'.$secret_lang['heimatflotte'].'</td>';
                            echo '<td class="tc" width="100">'.$secret_lang['flotte'].' I</td>';
                            echo '<td class="tc" width="100">'.$secret_lang['flotte'].' II</td>';
                            echo '<td class="tc" width="100">'.$secret_lang['flotte'].' III</td>';
                            echo '</tr>';

                            //lade einheitentypen
                            $fleetpoints = array();
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_id, tech_name FROM de_tech_data WHERE tech_id>80 AND tech_id<100", []);
                            while ($row = mysqli_fetch_array($db_daten)) { //jeder gefundene datensatz wird geprueft
                                echo '<tr>';
                                echo '<td class="cc">'.getTechNameByRasse($row["tech_name"], $zrasse)."</td>";
                                echo '<td class="cc">'.number_format($schiffe[0][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[1][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[2][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[3][$row["tech_id"] - 81], 0, "", ".")."</td>";

                                if (!isset($fleetpoints[0])) $fleetpoints[0] = 0;
                                if (!isset($fleetpoints[1])) $fleetpoints[1] = 0;
                                if (!isset($fleetpoints[2])) $fleetpoints[2] = 0;
                                if (!isset($fleetpoints[3])) $fleetpoints[3] = 0;

                                $fleetpoints[0] += $schiffe[0][$row["tech_id"] - 81] * $unit[$zrasse - 1][$row["tech_id"] - 81][4];
                                $fleetpoints[1] += $schiffe[1][$row["tech_id"] - 81] * $unit[$zrasse - 1][$row["tech_id"] - 81][4];
                                $fleetpoints[2] += $schiffe[2][$row["tech_id"] - 81] * $unit[$zrasse - 1][$row["tech_id"] - 81][4];
                                $fleetpoints[3] += $schiffe[3][$row["tech_id"] - 81] * $unit[$zrasse - 1][$row["tech_id"] - 81][4];

                                echo '</tr>';
                            }

                            //Flottenpunktewert

                            echo '<tr class="cc">
                            <td><i>'.$secret_lang['flottenpunktewert'].'</i></td>
                            <td>'.number_format($fleetpoints[0], 0, "", ".").'</td>
                            <td>'.number_format($fleetpoints[1], 0, "", ".").'</td>
                            <td>'.number_format($fleetpoints[2], 0, "", ".").'</td>
                            <td>'.number_format($fleetpoints[3], 0, "", ".").'</td>
                            </tr>';

                            echo '</table>';
                            break;
                        case 1: //flottenauftrag

                            checkMissionEnd();
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%"><b>'.$secret_lang['auftraegederflotte'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            $zsys2save = $zsys2;


                            $fid0 = $uid.'-0';
                            $fid1 = $uid.'-1';
                            $fid2 = $uid.'-2';
                            $fid3 = $uid.'-3';
                            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT zielsec, zielsys, aktion, aktzeit, zeit, mission_time FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
                            $ed_id = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $einheiten_daten[$ed_id] = $row;
                                $ed_id++;
                            }

                            //print_r($einheiten_daten);

                            $zsec1x = $einheiten_daten[1]["zielsec"];
                            $zsys1 = $einheiten_daten[1]["zielsys"];
                            $a1 = $einheiten_daten[1]["aktion"];
                            $t1 = $einheiten_daten[1]["zeit"];
                            $at1 = $einheiten_daten[1]["aktzeit"];
                            $mission_time1 = $einheiten_daten[1]["mission_time"];

                            $zsec2x = $einheiten_daten[2]["zielsec"];
                            $zsys2 = $einheiten_daten[2]["zielsys"];
                            $a2 = $einheiten_daten[2]["aktion"];
                            $t2 = $einheiten_daten[2]["zeit"];
                            $at2 = $einheiten_daten[2]["aktzeit"];
                            $mission_time2 = $einheiten_daten[2]["mission_time"];

                            $zsec3x = $einheiten_daten[3]["zielsec"];
                            $zsys3 = $einheiten_daten[3]["zielsys"];
                            $a3 = $einheiten_daten[3]["aktion"];
                            $t3 = $einheiten_daten[3]["zeit"];
                            $at3 = $einheiten_daten[3]["aktzeit"];
                            $mission_time3 = $einheiten_daten[3]["mission_time"];


                            if ($a1 == 0) {
                                $a1 = $secret_lang['systemverteidigung'];
                            } elseif ($a1 == 1) {
                                $a1 = $secret_lang['angriffreisezeit'].$t1;
                            } elseif ($a1 == 2) {
                                $a1 = $secret_lang['verteidigung'].$zsec1x.':'.$zsys1.$secret_lang['reisezeit'].$t1;
                            } elseif ($a1 == 3) {
                                $a1 = '&nbsp;&nbsp;'.$secret_lang['rueckflug'].'&nbsp;&nbsp;'.$secret_lang['reisezeit2'].$t1;
                            }
                            //elseif ($a1==4) $a1='&nbsp;&nbsp;'.$secret_lang[archaeologie].'&nbsp;&nbsp;'.$secret_lang[reisezeit2].$t1;
                            elseif ($a1 == 4) {
                                $a1 = '&nbsp;&nbsp;Mission bis: '.date("H:i:s d.m.Y", $mission_time1);
                            }

                            if ($a1[0] == 'V' && $t1 == 0) {
                                $a1 = $secret_lang['verteidige'].$zsec1x.':'.$zsys1.$secret_lang['zeit'].$at1;
                            }

                            if ($a2 == 0) {
                                $a2 = $secret_lang['systemverteidigung'];
                            } elseif ($a2 == 1) {
                                $a2 = $secret_lang['angriffreisezeit'].$t2;
                            } elseif ($a2 == 2) {
                                $a2 = $secret_lang['verteidigung'].$zsec2x.':'.$zsys2.$secret_lang['reisezeit'].$t2;
                            } elseif ($a2 == 3) {
                                $a2 = '&nbsp;&nbsp;'.$secret_lang['rueckflug'].'&nbsp;&nbsp;'.$secret_lang['reisezeit2'].$t2;
                            }
                            //elseif ($a2==4) $a2='&nbsp;&nbsp;'.$secret_lang[archaeologie].'&nbsp;&nbsp;'.$secret_lang[reisezeit2].$t2;
                            elseif ($a2 == 4) {
                                $a2 = '&nbsp;&nbsp;Mission bis: '.date("H:i:s d.m.Y", $mission_time2);
                            }

                            if ($a2[0] == 'V' && $t2 == 0) {
                                $a2 = $secret_lang['verteidige'].$zsec2x.':'.$zsys2.$secret_lang['zeit'].$at2;
                            }

                            if ($a3 == 0) {
                                $a3 = $secret_lang['systemverteidigung'];
                            } elseif ($a3 == 1) {
                                $a3 = $secret_lang['angriffreisezeit'].$t3;
                            } elseif ($a3 == 2) {
                                $a3 = $secret_lang['verteidigung'].$zsec3x.':'.$zsys3.$secret_lang['reisezeit'].$t3;
                            } elseif ($a3 == 3) {
                                $a3 = '&nbsp;&nbsp;'.$secret_lang['rueckflug'].'&nbsp;&nbsp;'.$secret_lang['reisezeit2'].$t3;
                            }
                            //elseif ($a3==4) $a3='&nbsp;&nbsp;'.$secret_lang[archaeologie].'&nbsp;&nbsp;'.$secret_lang[reisezeit2].$t3;
                            elseif ($a3 == 4) {
                                $a3 = '&nbsp;&nbsp;Mission bis: '.date("H:i:s d.m.Y", $mission_time3);
                            }

                            if ($a3[0] == 'V' && $t3 == 0) {
                                $a3 = $secret_lang['verteidige'].$zsec3x.':'.$zsys3.$secret_lang['zeit'].$at3;
                            }

                            echo '<tr>';
                            echo '<td class="cc" width="100%">'.$a1.'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td class="cc" width="100%">'.$a2.'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td class="cc" width="100%">'.$a3.'</td>';
                            echo '</tr>';
                            echo '</table>';
                            $zsys2 = $zsys2save;

                            break;
                        case 2: //verteidigungsanlagen
                            //zaehle alle verteidigungsanlagen, die schon vorhanden sind - anfang
                            for ($i = 100; $i <= 109; $i++) {
                                ${'ec'.$i} = 0;
                            }
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=?", [$uid]);
                            $row = mysqli_fetch_array($db_daten);
                            for ($i = 100; $i <= 109; $i++) {
                                ${'ec'.$i} += $row["e$i"];
                            }
                            //zaehle alle verteidigungsanlagen, die schon vorhanden sind - ende

                            //die daten in de_user_scan hinterlegen
                            for ($i = 0;$i < count($savelist);$i++) {
                                $save_uid = $savelist[$i];

                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET dtime=UNIX_TIMESTAMP(),
                                    e100=?, e101=?, e102=?, e103=?, e104=?
                                    WHERE user_id=? AND zuser_id=?", 
                                    [$row['e100'], $row['e101'], $row['e102'], $row['e103'], $row['e104'], $save_uid, $uid]);
                            }

                            //ueberschrift ausgeben
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%"><b>'.$secret_lang['uebersichtvanlagen'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            echo '</table>';

                            //lade einheitentypen
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_id, tech_name FROM de_tech_data WHERE tech_id>99 AND tech_id<110 ORDER BY tech_id", []);

                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            $gespunkte = 0;
                            while ($row = mysqli_fetch_array($db_daten)) { //jeder gefundene datensatz wird geprueft
                                $ec = ${'ec'.$row['tech_id']};

                                $gespunkte += $ec * $unit[$rasse - 1][$row["tech_id"] - 90][4];

                                echo '<tr>';
                                echo '<td class="cc" width="70%" align="left">'.getTechNameByRasse($row["tech_name"], $zrasse)."</td>";
                                echo '<td class="cc" width="30%" align="right">'.number_format($ec, 0, "", ".")."</td>";
                                echo '</tr>';

                                //}
                            }

                            //punktewert
                            echo '<tr class="cc"><td><i>'.$secret_lang['punktewert'].'</i></td><td>'.number_format($gespunkte, 0, "", ".").'</td></tr>';

                            echo "</table>";
                            break;
                        case 3: //nachrichten
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="600">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%"><b>'.$secret_lang['nachrichtenvon'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';

                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT time, typ, text FROM de_user_news WHERE user_id=? AND typ <> 60 ORDER BY time DESC", [$uid]);
                            while ($row = mysqli_fetch_array($db_daten)) { //jeder gefundene datensatz wird ausgegeben
                                $t = $row["time"];
                                $n = $row["typ"];
                                $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
                                switch ($n) {
                                    case 8:
                                        $werte = explode(";", $row["text"]);
                                        $tronic = $werte[0];
                                        unset($na);
                                        include "inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";
                                        $nanr = mt_rand(0, count($na) - 1);

                                        $nachricht = $na[$werte[1]];

                                        echo '<tr>';
                                        echo '<td class="cl">'.$time.'</td>';
                                        echo '</tr>';
                                        echo '<tr>';
                                        echo '<td class="cl">'.$nachricht.'</td>';
                                        echo '</tr>';

                                        break;
                                    case 50:
                                        echo '<tr>';
                                        echo '<td class="cl">'.$time.'</td>';
                                        echo '</tr>';
                                        echo '<tr>';
                                        echo '<td class="cc">'.showkampfberichtV0($row["text"], $rasse, $zname, $zsec2, $zsys2, $schiffspunkte).'</td>';
                                        echo '</tr>';
                                        break;
                                    case 57:
                                        echo '<tr>';
                                        echo '<td class="cl">'.$time.'</td>';
                                        echo '</tr>';
                                        echo '<tr>';
                                        echo '<td class="cc">'.showkampfberichtV1($row["text"], $rasse, $zname, $zsec2, $zsys2, $schiffspunkte).'</td>';
                                        echo '</tr>';
                                        break;
                                    case 70:
                                        echo '<tr>';
                                        echo '<td class="cl">'.$time.'</td>';
                                        echo '</tr>';
                                        echo '<tr>';
                                        echo '<td class="cc">'.showkampfberichtBG($row["text"]).'</td>';
                                        echo '</tr>';
                                        break;
                                    default:
                                        $dontshow = 0;
                                        if ($n == 51 and strpos($row["text"], "($sector:$system)") === false) {
                                            $dontshow = 1;
                                        }

                                        if ($dontshow == 0) {
                                            //atter/deffer farbig darstellen
                                            $hstr1 = '';
                                            $hstr2 = '';
                                            if ($n == 51) {
                                                $hstr1 = '<font color="#FF0000">';
                                                $hstr2 = '</font>';
                                            }
                                            if ($n == 52) {
                                                $hstr1 = '<font color="#FF0000">';
                                                $hstr2 = '</font>';
                                            }
                                            if ($n == 53) {
                                                $hstr1 = '<font color="#00FF00">';
                                                $hstr2 = '</font>';
                                            }
                                            if ($n == 54) {
                                                $hstr1 = '<font color="#00FF00">';
                                                $hstr2 = '</font>';
                                            }
                                            echo '<tr>';
                                            echo '<td class="cl">'.$time.'</td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo '<td class="cl">'.$hstr1.$row["text"].$hstr2.'</td>';
                                            echo '</tr>';
                                        }
                                        break;
                                }
                            }
                            echo '</table>';
                            break;
                        case 4: //entwicklungen
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%"><b>'.$secret_lang['entwicklungvon'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            echo '</table>';
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_id, tech_name FROM de_tech_data ORDER BY tech_level", []);
                            while ($row = mysqli_fetch_array($db_daten)) { //jeder gefundene datensatz wird gepr�ft
                                if (hasTech($zpt, $row["tech_id"])) {
                                    echo '<tr>';
                                    echo '<td class="cl" width="100%">'.getTechNameByRasse($row['tech_name'], $zrasse).'</td>';
                                    echo '</tr>';
                                }
                            }
                            echo '</table>';
                            break;
                        case 5: //allytag
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%">'.$secret_lang['allytagvon'].$zname.' ('.$zsec2.':'.$zsys2.')</td>';
                            echo '</tr>';
                            echo '</table>';
                            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag, status FROM de_user_data WHERE user_id=?", [$uid]);
                            $row = mysqli_fetch_array($db_daten);

                            $allytag = $row["allytag"];

                            //die daten in de_user_scan hinterlegen
                            if ($allytag == '' or $row["status"] != 1) {
                                $saveallytag = '';
                            } else {
                                $saveallytag = $allytag;
                            }
                            for ($i = 0;$i < count($savelist);$i++) {
                                $save_uid = $savelist[$i];

                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_scan SET atime=UNIX_TIMESTAMP(), allytag=? WHERE user_id=? AND zuser_id=?", [$saveallytag, $save_uid, $uid]);
                            }

                            if ($allytag == '' or $row["status"] != 1) {
                                $allytag = $secret_lang['keineally'];
                            }

                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="cc" width="100%">'.$allytag.'</td>';
                            echo '</tr>';
                            echo '</table>';

                            break;
                        case 6: //systemstatus
                            $eta1 = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(fleetsize) AS fleetsize FROM de_user_fleet WHERE zielsec = ? AND zielsys = ? AND aktion = 1 AND entdeckt > 0 AND zeit = 1", [$zsec2, $zsys2]);
                            $eta2 = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(fleetsize) AS fleetsize FROM de_user_fleet WHERE zielsec = ? AND zielsys = ? AND aktion = 1 AND entdeckt > 0 AND zeit = 2", [$zsec2, $zsys2]);
                            $eta1 = mysqli_fetch_array($eta1);
                            $eta1 = $eta1["fleetsize"];
                            $eta2 = mysqli_fetch_array($eta2);
                            $eta2 = $eta2["fleetsize"];
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" colspan="2"><b>Systemstatus von '.$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td class="cl" width="40%">Incoming ETA 1:</td>';
                            echo '<td class="cc" width="60%">'.number_format($eta1, 0, "", ".").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td class="cl">Incoming ETA 2:</td>';
                            echo '<td class="cc">'.number_format($eta2, 0, "", ".").'</td>';
                            echo '</tr>';
                            echo '</table>';
                            break;
                        case 11: //enttarnung
                            /*
                            //berechnen wie viele Agenten man beim Gegner enttart
                            $enttarnt=floor($az/100*22);

                            //wenn der Gegner nicht soviele Agenten hat, dann den Wert korrigieren
                            if($enttarnt>$zagent){
                                $enttarnt=$zagent;
                            }

                            //anhand der enttarnten Agenten die eigenen Verluste berechnen
                            $eigene_verluste=$enttarnt*100/22;

                            echo '<table border="0" cellpadding="0" cellspacing="1" width="596px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%">Enttarnte feindliche Agenten, die jetzt als Z&ouml;llner arbeiten: '.number_format($enttarnt, 0,",",".").'
                                  <br><br>Enttarnte eigene Agenten, die jetzt als Z&ouml;llner arbeiten: '.number_format($eigene_verluste, 0,",",".").'
                              </td>';
                            echo '</tr>';
                            echo '</table>';


                            //info an das ziel und ggf. agenten abziehen
                            $time=date("YmdHis");
                            if($enttarnt>0){
                                if($enttarnt>1){
                                    $msg='Bei einem feindlichen Agenteneinsatz von '.$_SESSION['ums_spielername'].' ('.$sector.':'.$system.') wurden '.number_format($enttarnt, 0,",",".").' Agenten enttarnt und arbeiten jetzt als Z&ouml;llner.';
                                }else{
                                    $msg='Bei einem feindlichen Agenteneinsatz von '.$_SESSION['ums_spielername'].' ('.$sector.':'.$system.') wurde '.number_format($enttarnt, 0,",",".").' Agente enttarnt und arbeitet jetzt als Z&ouml;llner.';
                                }
                                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$enttarnt, $enttarnt, $uid]);
                            }

                            //eigene agenten abziehen
                            if($eigene_verluste>0){
                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$eigene_verluste, $eigene_verluste, $_SESSION['ums_user_id']]);
                                $agent=$agent-$eigene_verluste;
                            }

                            break;
                            */
                        case 7: //sabotage weniger kollektorenergie
                            //überprüfen ob man das ziel überhaupt sabotieren kann
                            if (sabotageallowed($uid) == 1) {
                                //�berpr�fen ob man �berhaupt schon wieder einen einsatz dieser art starten kann
                                if ($maxroundtick > $sc1 + $sv_sabotage[$etyp][1]) {
                                    $emsg = '';
                                    $emsg .= '<table width=600p><tr><td class="ccg">';

                                    //erfolgsnachricht ausgeben
                                    $emsg .= $secret_lang['erfolgsnachricht_sabotage'];

                                    //schauen wieviel agenten es erwischt
                                    $aze = $az / 100 * rand($angreifer_verlust_win_min[$etyp], $angreifer_verlust_win_max[$etyp]);
                                    $aze = intval($aze);
                                    if ($aze > $zagent) {
                                        $aze = $zagent;
                                    }
                                    //eigene agenten abziehen
                                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$aze, $aze, $_SESSION['ums_user_id']]);
                                    write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);

                                    $agent = $agent - $aze;

                                    $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;

                                    //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                    $time = date("YmdHis");
                                    $msg = $secret_lang['erfolgsnachricht_sabotage_kollektoroutput'];
                                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                                    //sabotage counter setzen und dass er nen neue info hat
                                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, sc1 = ? WHERE user_id = ?", [$maxroundtick, $uid]);
                                } else {
                                    $emsg .= '<table width=600><tr><td class="ccr">';
                                    $emsg .= $secret_lang['sabotage_gehtnochnicht'];
                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;
                                }
                            } else {
                                $emsg .= '<table width=600><tr><td class="ccr">';
                                $emsg .= $secret_lang['sabotage_nichterlaubt'];
                                $emsg .= '</td></tr></table>';
                                echo $emsg;
                            }
                            break;
                            /////////////////////////////////////////////////////
                            /////////////////////////////////////////////////////
                        case 8: //sabotage raumwerft
                            //�berpr�fen ob man das ziel �berhaupt sabotieren kann
                            if (sabotageallowed($uid) == 1) {
                                //�berpr�fen ob man �berhaupt schon wieder einen einsatz dieser art starten kann
                                if ($maxroundtick > $sc2 + $sv_sabotage[$etyp][1]) {
                                    //überprüfen ob er eine raumwerft hat
                                    //if (hasTech($zpt,13)){
                                    if (hasTech($zpt, 129)) {
                                        $emsg = '';
                                        $emsg .= '<table width=600><tr><td class="ccg">';

                                        //erfolgsnachricht ausgeben
                                        $emsg .= $secret_lang['erfolgsnachricht_sabotage'];

                                        //schauen wieviel agenten es erwischt
                                        $aze = $az / 100 * rand($angreifer_verlust_win_min[$etyp], $angreifer_verlust_win_max[$etyp]);
                                        $aze = intval($aze);
                                        if ($aze > $zagent) {
                                            $aze = $zagent;
                                        }
                                        //eigene agenten abziehen
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost = agent_lost + ? WHERE user_id = ?", [$aze, $aze, $_SESSION['ums_user_id']]);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = date("YmdHis");
                                        $msg = $secret_lang['erfolgsnachricht_sabotage_raumwerft'];
                                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, sc2 = ? WHERE user_id = ?", [$maxroundtick, $uid]);
                                    } else { //er hat nicht die passende technologie
                                        $emsg .= '<table width=600><tr><td class="ccr">';
                                        $emsg .= $secret_lang['sabotage_keinziel'];
                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;
                                    }
                                } else {
                                    $emsg .= '<table width=600><tr><td class="ccr">';
                                    $emsg .= $secret_lang['sabotage_gehtnochnicht'];
                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;
                                }
                            } else {
                                $emsg .= '<table width=600><tr><td class="ccr">';
                                $emsg .= $secret_lang['sabotage_nichterlaubt'];
                                $emsg .= '</td></tr></table>';
                                echo $emsg;
                            }
                            break;
                            /////////////////////////////////////////////////////
                            /////////////////////////////////////////////////////
                        case 9: //sabotage verteidigungszentrum
                            //�berpr�fen ob man das ziel �berhaupt sabotieren kann
                            if (sabotageallowed($uid) == 1) {
                                //�berpr�fen ob man �berhaupt schon wieder einen einsatz dieser art starten kann
                                if ($maxroundtick > $sc3 + $sv_sabotage[$etyp][1]) {
                                    //�berpr�fen ob er eine raumwerft hat
                                    if (hasTech($zpt, 22)) {
                                        $emsg = '';
                                        $emsg .= '<table width=600><tr><td class="ccg">';

                                        //erfolgsnachricht ausgeben
                                        $emsg .= $secret_lang['erfolgsnachricht_sabotage'];

                                        //schauen wieviel agenten es erwischt
                                        $aze = $az / 100 * rand($angreifer_verlust_win_min[$etyp], $angreifer_verlust_win_max[$etyp]);
                                        $aze = intval($aze);
                                        if ($aze > $zagent) {
                                            $aze = $zagent;
                                        }
                                        //eigene agenten abziehen
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$aze, $aze, $_SESSION['ums_user_id']]);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = date("YmdHis");
                                        $msg = $secret_lang['erfolgsnachricht_sabotage_verteidigungszentrum'];
                                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, sc3 = ? WHERE user_id = ?", [$maxroundtick, $uid]);
                                    } else { //er hat nicht die passende technologie
                                        $emsg .= '<table width=600><tr><td class="ccr">';
                                        $emsg .= $secret_lang['sabotage_keinziel'];
                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;
                                    }
                                } else {
                                    $emsg .= '<table width=600><tr><td class="ccr">';
                                    $emsg .= $secret_lang['sabotage_gehtnochnicht'];
                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;
                                }
                            } else {
                                $emsg .= '<table width=600><tr><td class="ccr">';
                                $emsg .= $secret_lang['sabotage_nichterlaubt'];
                                $emsg .= '</td></tr></table>';
                                echo $emsg;
                            }
                            break;
                            /////////////////////////////////////////////////////
                            /////////////////////////////////////////////////////
                        case 10: //sabotage handel
                            //�berpr�fen ob man das ziel �berhaupt sabotieren kann
                            if (sabotageallowed($uid) == 1) {
                                //�berpr�fen ob man �berhaupt schon wieder einen einsatz dieser art starten kann
                                if ($maxroundtick > $sc4 + $sv_sabotage[$etyp][1]) {
                                    //�berpr�fen ob er eine raumwerft hat
                                    if (hasTech($zpt, 4)) {
                                        $emsg = '';
                                        $emsg .= '<table width=600><tr><td class="ccg">';

                                        //erfolgsnachricht ausgeben
                                        $emsg .= $secret_lang['erfolgsnachricht_sabotage'];

                                        //schauen wieviel agenten es erwischt
                                        $aze = $az / 100 * rand($angreifer_verlust_win_min[$etyp], $angreifer_verlust_win_max[$etyp]);
                                        $aze = intval($aze);
                                        if ($aze > $zagent) {
                                            $aze = $zagent;
                                        }
                                        //eigene agenten abziehen
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost = agent_lost + ? WHERE user_id = ?", [$aze, $aze, $_SESSION['ums_user_id']]);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = date("YmdHis");
                                        $msg = 'Ein Agenteneinsatz hat f&uuml;r Sch&auml;den am Missionssystem gesorgt. Mehr Informationen sind im Geheimdienst abrufbar.';
                                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, sc4 = ? WHERE user_id = ?", [$maxroundtick, $uid]);
                                    } else { //er hat nicht die passende technologie
                                        $emsg .= '<table width="600px"><tr><td class="ccr">';
                                        $emsg .= $secret_lang['sabotage_keinziel'];
                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;
                                    }
                                } else {
                                    $emsg .= '<table width="600px"><tr><td class="ccr">';
                                    $emsg .= $secret_lang['sabotage_gehtnochnicht'];
                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;
                                }
                            } else {
                                $emsg .= '<table width="600px"><tr><td class="ccr">';
                                $emsg .= $secret_lang['sabotage_nichterlaubt'];
                                $emsg .= '</td></tr></table>';
                                echo $emsg;
                            }
                            break;


                    }  //switch etyp ende
                    $showmenu = 1;
                } else { //versuch misslingt
                    //schauen wieviel agenten es erwischt
                    $aze = $az / 100 * rand($angreifer_verlust_fail_min[$etyp], $angreifer_verlust_fail_max[$etyp]);
                    $aze = intval($aze);
                    if ($aze > $zagent) {
                        $aze = $zagent;
                    }

                    //schauen wieviel agenten beim ziel draufgehen
                    //$zagentabz=round($aze/100*rand(8,10));
                    $zagentabz = 0;

                    $time = date("YmdHis");
                    if ($aze == 0) {
                        $aze = 1;
                    }
                    echo '<table width="600"><tr align="center"><td><div class="cell">'.$secret_lang['einsatzgescheitert'].$aze.$secret_lang['einsatzgescheitert2'].'</div></td></tr></table><br>';
                    if ($aze == 1) {
                        $msg = $secret_lang['einsatzentdeckt'].$_SESSION['ums_spielername'].' ('.$sector.':'.$system.$secret_lang['einsatzentdeckt2'].$aze.$secret_lang['einsatzentdeckt3'];
                    } else {
                        $msg = $secret_lang['einsatzentdeckt'].$_SESSION['ums_spielername'].' ('.$sector.':'.$system.$secret_lang['einsatzentdeckt2'].$aze.$secret_lang['einsatzentdeckt4'];
                    }
                    //msg um die eigenen verlust erweitern
                    /*
                    $msg.=$secret_lang[einsatzentdeckt5];
                    if($zagentabz==0) $msg.=$secret_lang[einsatzentdeckt8];
                    elseif($zagentabz==1) $msg.=$zagentabz.$secret_lang[einsatzentdeckt6];
                    else $msg.=$zagentabz.$secret_lang[einsatzentdeckt7];
                    */

                    //info an das ziel und ggf. agenten abziehen
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 5, ?, ?)", [$uid, $time, $msg]);
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1, agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$zagentabz, $zagentabz, $uid]);

                    //eigene agenten abziehen
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET agent = agent - ?, agent_lost=agent_lost + ? WHERE user_id = ?", [$aze, $aze, $_SESSION['ums_user_id']]);
                    write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                    $agent = $agent - $aze;

                    $showmenu = 1;
                }

                if ($showmenu == 1) {
                    echo '<form name="f'.$zsec2.'" action="sector.php?sf='.$zsec2.'" method="POST">';
                    echo '<div class="cell" style="width: 400px"><a href="military.php?se='.$zsec2.'&sy='.$zsys2.'">'.$secret_lang['zummilitaer'].'</a> - ';
                    echo '<a href="javascript:document.f'.$zsec2.'.submit()">'.$secret_lang['zursektoransicht'].'</a></div>';
                    echo '</form><br>';
                }

                //Scanhistorie anlegen
                if ($scanhistory == "") {
                    $entry = $copy_zsec2.':'.$copy_zsys2.':'.$zname.'|';
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET scanhistory=? WHERE user_id = ?", [$entry, $_SESSION['ums_user_id']]);
                } else { //Scanhistorie updaten
                    $drin = 0;
                    $i = 0;
                    $einsaetze = array(array());
                    // Einträge zerlegen; leere Elemente entfernen um Warnungen zu vermeiden
                    $scanhis = array_filter(explode("|", $scanhistory), 'strlen');
                    while ($i < Count($scanhis)) {
                        $daten = explode(":", $scanhis[$i]); // FIX: $daten aus $scanhis[$i] extrahieren
                        // Sicherstellen, dass fehlende Teile nicht zu Undefined-Index-Warnungen führen
                        $einsaetze[$i][0] = $daten[0] ?? '';
                        $einsaetze[$i][1] = $daten[1] ?? '';
                        $einsaetze[$i][2] = $daten[2] ?? '';
                        $i++;
                    }
                    $i = 0;
                    while ($i < Count($einsaetze)) {
                        if (($einsaetze[$i][0] == $copy_zsec2) && ($einsaetze[$i][1] == $copy_zsys2)) {
                            $drin = 1;
                            break; // Wenn gefunden, Schleife verlassen
                        }
                        $i++;
                    }
                    
                    if ($drin == "0") {
                        // Neue Entry an den Anfang setzen
                        $entry = $copy_zsec2.':'.$copy_zsys2.':'.$zname.'|';
                        // Alte Einträge bis maximal 4 weitere hinzufügen
                        $i = 0;
                        while ($i < Count($einsaetze) && $i < 4) {
                            if (!empty($einsaetze[$i][0])) { // Nur gültige Einträge hinzufügen
                                $entry = $entry.$einsaetze[$i][0].':'.$einsaetze[$i][1].':'.$einsaetze[$i][2].'|';
                            }
                            $i++;
                        }
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET scanhistory=? WHERE user_id = ?", [$entry, $_SESSION['ums_user_id']]);
                    }
                }
            }
        }
    }



    //Ende der Berichte
    echo '</div>';

    if ($sonde > 0 && hasTech($pt, 9) && hasTech($pt, 110)) {//Sondeneinsatz
        echo '<form action="secret.php" method="POST" name="sonde">';
        rahmen_oben($secret_lang['sondengeheimaktion']);
        echo '
        <div class="geh mod">
            <div class="geh-zeile">
                <div class="geh-feld geh-bestand"><span class="mod-typ">'.$secret_lang['vorhanden'].'</span><b>'.number_format($sonde, 0, "", ".").'</b></div>
                <div class="geh-feld"><span class="mod-typ">'.$secret_lang['zielkoordinaten'].'</span>
                    <span class="geh-koord"><input type="text" name="zsec1" id="zsec1" value="'.$zsec1.'" maxlength="5" inputmode="numeric" class="mod-eingabe"><span>:</span><input type="text" name="zsys1" id="zsys1" value="'.$zsys1.'" maxlength="3" inputmode="numeric" class="mod-eingabe"></span>
                </div>
                <div class="geh-feld geh-los"><input type="submit" name="startsonde" value="'.$secret_lang['sondestarten'].'" class="mod-btn"></div>
            </div>
        </div>';
        rahmen_unten();
        echo '</form>';
    }

    if (hasTech($pt, 111)) {//Agenteneinsatz
        $agenten_einsetzen = $agent;
        if (isset($_REQUEST['az'])) {
            $agenten_einsetzen = intval($_REQUEST['az']);
        }

        if (!isset($_REQUEST['etyp'])) {
            $_REQUEST['etyp'] = 3;
        }

        //Einsatzziele in der gewohnten Reihenfolge (9 und 11 sind abgeschaltet)
        $einsatzziele = array(
            3 => $secret_lang['nachrichten'],
            0 => $secret_lang['flottenaufstellung'],
            1 => $secret_lang['flottenauftrag'],
            2 => $secret_lang['verteidigungsanlagen'],
            4 => $secret_lang['entwicklungen'],
            5 => $secret_lang['allytag'],
            6 => $secret_lang['systemstatus'],
            7 => $secret_lang['sabotage_kollektoroutput'],
            8 => $secret_lang['sabotage_raumwerft'],
            10 => 'S: Missionsystem',
        );
        $optionen = '';
        foreach ($einsatzziele as $wert => $text) {
            $optionen .= '<option value="'.$wert.'"'.($_REQUEST['etyp'] == $wert ? ' selected' : '').'>'.$text.'</option>';
        }

        echo '<form action="secret.php" method="POST" name="agent">';
        rahmen_oben($secret_lang['aggigeheimaktion']);
        echo '
        <div class="geh mod">
            <div class="geh-zeile">
                <div class="geh-feld geh-bestand"><span class="mod-typ">'.$secret_lang['vorhanden'].'</span><b>'.number_format($agent, 0, "", ".").'</b></div>
                <div class="geh-feld"><span class="mod-typ">'.$secret_lang['einsetzen'].'</span><input type="text" name="az" value="'.$agenten_einsetzen.'" maxlength="10" autocomplete="off" inputmode="numeric" class="mod-eingabe geh-anzahl"></div>
                <div class="geh-feld geh-breit"><span class="mod-typ">'.$secret_lang['einsatzziel'].'</span><select name="etyp" onChange="sei(this.options[this.selectedIndex].value)" class="mod-eingabe">'.$optionen.'</select></div>
            </div>
            <div class="geh-zeile">
                <div class="geh-feld"><span class="mod-typ">'.$secret_lang['zielkoordinaten'].'</span>
                    <span class="geh-koord"><input type="text" name="zsec2" id="zsec2" value="'.$zsec2.'" maxlength="5" inputmode="numeric" class="mod-eingabe"><span>:</span><input type="text" name="zsys2" id="zsys2" value="'.$zsys2.'" maxlength="3" inputmode="numeric" class="mod-eingabe"></span>
                </div>
                <div class="geh-feld geh-breit"><span class="mod-chip" rel="tooltip" title="'.$secret_lang['boni'].'&'.$bstr.'">'.$secret_lang['boni'].': Einsatz +'.number_format($artbonusatt, 2, ",", ".").' %, Abwehr +'.number_format($artbonusdeff, 2, ",", ".").' %</span></div>
                <div class="geh-feld geh-los"><input type="submit" name="startagent" value="'.$secret_lang['einsatzstarten'].'" class="mod-btn"></div>
            </div>
            <div class="mod-hinweis geh-beschreibung" id="seii"></div>
        </div>';
        rahmen_unten();
        echo '</form>';


        rahmen_oben($secret_lang['letzteeinsaetze']);
        echo '<div class="geh mod">';
        if ($scanhistory == "") {
            echo '<div class="mod-leer">Noch keine Eins&auml;tze.</div>';
        } else {
            $i = 0;
            $scanhistory = explode("|", $scanhistory);

            while ($i < (Count($scanhistory) - 1)) {
                $daten = explode(":", $scanhistory[$i]);
                //ältere Einträge können noch rohe Eingaben enthalten
                $daten[0] = intval($daten[0]);
                $daten[1] = intval($daten[1]);
                $daten[2] = htmlspecialchars($daten[2] ?? '', ENT_QUOTES, 'UTF-8');
                echo '
                <div class="geh-verlauf">
                    <span class="geh-verlauf-ziel"><b>'.$daten[0].':'.$daten[1].'</b> '.$daten[2].'</span>
                    <a href="javascript:insertsonde('.$daten[0].','.$daten[1].')" class="mod-btn mod-btn-leise">'.$secret_lang['sondenzielproggen'].'</a>
                    <a href="javascript:insertagent('.$daten[0].','.$daten[1].')" class="mod-btn mod-btn-leise">'.$secret_lang['infiltrieren'].'</a>
                </div>';
                $i++;
            }
        }
        echo '</div>';
        rahmen_unten();

        echo '<script language="javascript">';
        //die einsatzbeschreibungen erstellen
        echo 'eb[0] = "'.$secret_lang['einsatzbeschreibung_flottenaufstellung'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[0].' - '.$angreifer_verlust_fail_max[0].'%";';
        echo 'eb[1] = "'.$secret_lang['einsatzbeschreibung_flottenauftrag'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[1].' - '.$angreifer_verlust_fail_max[1].'%";';

        echo 'eb[2] = "'.$secret_lang['einsatzbeschreibung_verteidigungsanlagen'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[2].' - '.$angreifer_verlust_fail_max[2].'%";';

        echo 'eb[3] = "'.$secret_lang['einsatzbeschreibung_nachrichten'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[3].' - '.$angreifer_verlust_fail_max[3].'%";';

        echo 'eb[4] = "'.$secret_lang['einsatzbeschreibung_entwicklungen'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[4].' - '.$angreifer_verlust_fail_max[4].'%";';

        echo 'eb[5] = "'.$secret_lang['einsatzbeschreibung_allianztag'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[5].' - '.$angreifer_verlust_fail_max[5].'%";';

        echo 'eb[6] = "'.$secret_lang['einsatzbeschreibung_systemstatus'].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[6].' - '.$angreifer_verlust_fail_max[6].'%";';
        echo 'eb[11] = "Die Enttarnung hat das Ziel feindliche Agenten auszuschalten. Der Einsatz gelingt immer.<br>Eigene Verluste: 100% der eingesetzten Agenten<br>Verluste beim Gegner: 22% der eingesetzten Agenten<br>Sollte das Ziel weniger Agenten haben, als man enttarnen kann, so kehren die nicht ben&ouml;tigten Agenten wohlbehalten zur&uuml;ck.";';

        echo 'eb[7] = "'.$secret_lang['einsatzbeschreibung_sabotage_kollektoroutput1'].'<br>'.$secret_lang['einsatzbeschreibung_sabotage_kollektoroutput2'].': '.$sv_sabotage[7][2].'<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.$sv_sabotage[7][0].'<br>'.$secret_lang['einsatzbeschreibung_anwendungshaeufigkeit'].': '.$sv_sabotage[7][1].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[7].' - '.$angreifer_verlust_fail_max[7].'%<br>'.$secret_lang['einsatzbeschreibung_verlust_win'].': '.$angreifer_verlust_win_min[7].' - '.$angreifer_verlust_win_max[7].'%";';
        $index = 8;
        echo 'eb['.$index.'] = "'.$secret_lang['einsatzbeschreibung_sabotage_raumwerft'].'<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.$sv_sabotage[$index][0].'<br>'.$secret_lang['einsatzbeschreibung_anwendungshaeufigkeit'].': '.$sv_sabotage[$index][1].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[$index].' - '.$angreifer_verlust_fail_max[$index].'%<br>'.$secret_lang['einsatzbeschreibung_verlust_win'].': '.$angreifer_verlust_win_min[$index].' - '.$angreifer_verlust_win_max[$index].'%";';
        $index = 9;
        echo 'eb['.$index.'] = "'.$secret_lang['einsatzbeschreibung_sabotage_verteidigungszentrum'].'<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.$sv_sabotage[$index][0].'<br>'.$secret_lang['einsatzbeschreibung_anwendungshaeufigkeit'].': '.$sv_sabotage[$index][1].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[$index].' - '.$angreifer_verlust_fail_max[$index].'%<br>'.$secret_lang['einsatzbeschreibung_verlust_win'].': '.$angreifer_verlust_win_min[$index].' - '.$angreifer_verlust_win_max[$index].'%";';
        $index = 10;
        echo 'eb['.$index.'] = "Sabotage: Diese Mission hat das Ziel das Missionssystem des Gegners f&uuml;r eine gewisse Zeit zu stören und die Missionsdauer von neuen Missionen zu verl&auml;ngern.<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.$sv_sabotage[$index][0].'<br>'.$secret_lang['einsatzbeschreibung_anwendungshaeufigkeit'].': '.$sv_sabotage[$index][1].'<br>'.$secret_lang['einsatzbeschreibung_verlust_fail'].': '.$angreifer_verlust_fail_min[$index].' - '.$angreifer_verlust_fail_max[$index].'%<br>'.$secret_lang['einsatzbeschreibung_verlust_win'].': '.$angreifer_verlust_win_min[$index].' - '.$angreifer_verlust_win_max[$index].'%";';

        echo '
	function sei(wert) {
	document.getElementById("seii").innerHTML = eb[wert];
	}
	';

        echo 'sei('.intval($_REQUEST['etyp']).');';
        echo '</script>';


    }

    //Ausbildung und Bau; js/produktion*.js braucht das Formular "produktion", die Felder b110/b111,
    //die rohe Agentenzahl in #va und die Summenfelder #m #d #i #e #t #p
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_tech_data WHERE tech_id>=110 AND tech_id<=111 ORDER BY tech_id", []);

    $ergebnis = mysqli_execute_query($GLOBALS['dbi'], "SELECT sonde, agent FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $rowe = mysqli_fetch_array($ergebnis);

    $geh_resnamen = array('Multiplex', 'Dyharra', 'Iradium', 'Eternium', 'Tronic');
    $einheiten_zeilen = '';
    while ($row = mysqli_fetch_array($db_daten)) {
        $tech_id = $row['tech_id'];
        if (!hasTech($pt, $tech_id)) {
            continue;
        }

        $vorhanden = ($tech_id == 110) ? $rowe["sonde"] : $rowe["agent"];

        //Kosten mit Artefaktnachlass, nur Rohstoffe, die tatsächlich gebraucht werden
        $kosten = '';
        for ($r = 0; $r < 5; $r++) {
            $betrag = $einheiten_daten[$tech_id]['kosten'][$r] - round($einheiten_daten[$tech_id]['kosten'][$r] * $artbonusbuild / 100);
            if ($betrag > 0) {
                $kosten .= '<span class="geh-kosten" title="'.$geh_resnamen[$r].'"><img src="gp/g/icon'.($r + 1).'.png" alt="">'.number_format($betrag, 0, "", ".").'</span>';
            }
        }

        $einheiten_zeilen .= '
            <div class="geh-einheit">
                <a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t='.$tech_id.'" target="_blank"><img src="gp/g/t/'.$_SESSION['ums_rasse'].'_'.$tech_id.'.jpg" class="geh-einheit-bild" alt="" rel="tooltip" title="'.$tooltips[$tech_id - 110].'"></a>
                <div class="geh-einheit-text">
                    <a href="help.php?t='.$tech_id.'" class="geh-einheit-name">'.getTechNameByRasse($row["tech_name"], $_SESSION['ums_rasse']).'</a>
                    <div class="geh-einheit-kosten">'.$kosten.'<span class="geh-kosten">'.$einheiten_daten[$tech_id]['bz'].' WT</span></div>
                </div>
                <div class="geh-feld geh-bestand"><span class="mod-typ">'.$secret_lang['vorhanden'].'</span><b>'.number_format($vorhanden, 0, "", ".").'</b>'.($tech_id == 111 ? '<span id="va" hidden>'.$vorhanden.'</span>' : '').'</div>
                <div class="geh-feld"><span class="mod-typ">'.$secret_lang['anzahl'].'</span><input type="text" name="b'.$tech_id.'" id="b'.$tech_id.'" value="" maxlength="9" autocomplete="off" inputmode="numeric" onKeyUp="berechnepreise();" class="mod-eingabe geh-anzahl"></div>
            </div>';
    }

    //Summe der ausgewählten Einheiten, füllt js/produktion*.js
    $summe = '';
    foreach (array('m', 'd', 'i', 'e', 't') as $r => $feld) {
        $summe .= '<span class="geh-kosten" title="'.$geh_resnamen[$r].'"><img src="gp/g/icon'.($r + 1).'.png" alt=""><b id="'.$feld.'">0</b></span>';
    }
    $summe .= '<span class="geh-kosten">'.$secret_lang['punkte'].' <b id="p">0</b></span>';

    echo '<form action="secret.php" method="POST" name="produktion">';
    rahmen_oben($secret_lang['sondenundaggis']);
    echo '
    <div class="geh mod">
        <div class="geh-einheiten">'.$einheiten_zeilen.'</div>
        <div class="geh-summe">
            <div class="geh-summe-text"><span class="mod-typ">'.$secret_lang['kostenaggis'].'</span><div class="geh-summe-werte">'.$summe.'</div></div>
            <input type="submit" name="trainbuild" value="'.$secret_lang['ausbildenbauen'].'" class="mod-btn">
        </div>
        <div class="geh-klein">'.$buildstatus.'</div>';

    //aktive Bauaufträge
    $sql = "SELECT SUM(de_user_build.anzahl) AS anzahl, de_user_build.verbzeit, de_tech_data".$_SESSION['ums_rasse'].".tech_name FROM de_user_build LEFT JOIN de_tech_data".$_SESSION['ums_rasse']." on(de_user_build.tech_id = de_tech_data".$_SESSION['ums_rasse'].".tech_id) WHERE user_id=? AND de_user_build.tech_id > 109 AND de_user_build.tech_id < 120 GROUP BY de_user_build.tech_id, de_user_build.verbzeit ORDER BY de_user_build.verbzeit ASC";
    $result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="geh-produktion"><div class="mod-typ">'.$secret_lang['aktiveproduktion'].'</div>';
        while ($row = mysqli_fetch_array($result)) {
            echo '<div class="geh-auftrag"><span>'.$row["tech_name"].'</span><b>'.number_format($row["anzahl"], 0, "", ".").'</b><span class="mod-chip">noch '.$row["verbzeit"].' WT</span></div>';
        }
        echo '</div>';
    }
    echo '</div>';
    rahmen_unten();
    echo '</form>';

    ///////////////////////////////////////////////////////////////
    // anzeige der aktionen die aktuell gegen einen laufen
    ///////////////////////////////////////////////////////////////
    $sabotagen = array();
    if ($maxroundtick < $mysc1 + $sv_sabotage[7][0]) {
        $sabotagen[] = array($secret_lang['einsatzbeschreibung_sabotage_kollektoroutput2'].': '.$sv_sabotage[7][2], $mysc1 + $sv_sabotage[7][0] - $maxroundtick);
    }
    if ($maxroundtick < $mysc2 + $sv_sabotage[8][0]) {
        $sabotagen[] = array($secret_lang['einsatzbeschreibung_sabotage_raumwerft2'], $mysc2 + $sv_sabotage[8][0] - $maxroundtick);
    }
    if ($maxroundtick < $mysc3 + $sv_sabotage[9][0]) {
        $sabotagen[] = array($secret_lang['einsatzbeschreibung_sabotage_verteidigungszentrum2'], $mysc3 + $sv_sabotage[9][0] - $maxroundtick);
    }
    if ($maxroundtick < $mysc4 + $sv_sabotage[10][0]) {
        $sabotagen[] = array('Das Missionssystem ist gest&ouml;rt.', $mysc4 + $sv_sabotage[10][0] - $maxroundtick);
    }
    if (count($sabotagen) > 0) {
        rahmen_oben($secret_lang['sabotageauswirkungen']);
        echo '<div class="geh mod">';
        foreach ($sabotagen as $sabotage) {
            echo '<div class="geh-sabotage"><span>'.$sabotage[0].'</span><span class="mod-chip mod-chip-warn">'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': noch '.$sabotage[1].'</span></div>';
        }
        echo '</div>';
        rahmen_unten();
    }

} //geheimnienst-abfrage ende

function sabotageallowed($zuid)
{
    global $ownsector, $ownally;

    $sabotageallowed = 1;

    //daten des ziels auslesen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, allytag, status, npc, spec5 FROM de_user_data WHERE user_id=?", [$zuid]);
    $row = mysqli_fetch_array($db_daten);
    $zsector = $row["sector"];
    $znpc = $row["npc"];
    $zspec5 = $row['spec5'];
    if ($row["allytag"] != '' and $row["status"] == 1) {
        $zallytag = $row["allytag"];
    } else {
        $zallytag = '';
    }

    //�berpr�fen ob es evtl. der eigene sektor ist
    if ($zsector == $ownsector) {
        $defenders = mysqli_fetch_array(mysqli_execute_query($GLOBALS['dbi'], "SELECT count(*) as cnt FROM de_user_data WHERE secatt=0 AND sector=?", [$zsector]));
        $attackers = mysqli_fetch_array(mysqli_execute_query($GLOBALS['dbi'], "SELECT count(*) as cnt FROM de_user_data WHERE secatt=1 AND sector=?", [$zsector]));
        if ($defenders['cnt'] >= $attackers['cnt']) {
            $sabotageallowed = 0;
        }
    }

    //auf ally/b�ndnis pr�fen
    //----------- Ally Feine/Freunde
    $allypartner = array();
    $query = "select id from de_allys where allytag='$ownally'";
    $allyresult = mysqli_execute_query($GLOBALS['dbi'], $query, []);
    $at = mysqli_num_rows($allyresult);
    if ($at != 0) {
        $row = mysqli_fetch_array($allyresult);
        $allyid = $row["id"];

        $allyresult = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_partner, de_allys WHERE (ally_id_1=? OR ally_id_2=?) AND (ally_id_1=id OR ally_id_2=id)", [$allyid, $allyid]);
        while ($row = mysqli_fetch_array($allyresult)) {
            if ($ownally != $row["allytag"]) {
                $allypartner[] = $row["allytag"];
            }
        }
    }

    if (($ownally != '') and (($ownally == $zallytag) or (in_array($zallytag, $allypartner)))) {
        $sabotageallowed = 0;
    }

    if ($znpc == 1) {
        $sabotageallowed = 0;
    }
    //spezialisierung, kann nicht sabotiert werden
    if ($zspec5 == 1) {
        $sabotageallowed = 0;
    }

    return($sabotageallowed);
}

?>
</div>
</form>

</body>
</html>
