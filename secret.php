<?php
include "inc/header.inc.php";
include 'lib/transactioni.lib.php';
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
    $result  = mysqli_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id='$own_ally_id'");
    $row     = mysqli_fetch_array($result);
    $ally_bldg2 = $row["bldg2"];
}

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$maxroundtick = $row["tick"];

//userartefakte auslesen
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=3 AND user_id='$ums_user_id'");
$artbonusatt = 0;
$artbonusdeff = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonusatt = $artbonusatt + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}

//userartefakte auslesen
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=4 AND user_id='$ums_user_id'");
$artbonusdeff = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonusdeff = $artbonusdeff + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}

//userartefakte auslesen
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=5 AND user_id='$ums_user_id'");
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
echo '<script src="produktion'.$ums_rasse.'.js" type="text/javascript"></script>';
echo '<script language="javascript">';
echo 'var eb = new Array();';

unset($tooltips);
$tooltips[0] = $secret_lang['sonde'].'&'.$secret_lang['hilfesonde'];
$tooltips[1] = $secret_lang['agent'].'&'.$secret_lang['hilfeagent'];

$bstr = $secret_lang['aggibonus'].$ua_name[2].$secret_lang['artefakte'].number_format($artbonusatt, 2, ",", ".").
    '%<br><br>'.$secret_lang['aggibonus2'].$ua_name[3].$secret_lang['artefakte'].number_format($artbonusdeff, 2, ",", ".").'%';


echo '</script>';

if ($agent > 0) {
    ?>
<script language="JavaScript">
<!--
function insertagent(sec,sys)
{
  if(document.agent.zsec2)
  {
     document.getElementById("zsec2").value = sec;
     document.getElementById("zsys2").value = sys;
  }
  else
  {
      alert("<?php echo $secret_lang['keineagenten'];?>");
  }
}
function insertsonde(sec,sys)
{
  if(document.sonde.zsec1)
  {
      document.getElementById("zsec1").value = sec;
      document.getElementById("zsys1").value = sys;
  }
  else
  {
      alert("<?php echo $secret_lang['keinesonden'];?>");
  }
}
//-->
</script>
<?php
}
?>
</head>
<body>
<?php

$zsec1 = isset($_REQUEST['zsec1']) ? $_REQUEST['zsec1'] : '';
$zsys1 = isset($_REQUEST['zsys1']) ? $_REQUEST['zsys1'] : '';

$zsec2 = isset($_REQUEST['zsec2']) ? $_REQUEST['zsec2'] : '';
$zsys2 = isset($_REQUEST['zsys2']) ? $_REQUEST['zsys2'] : '';

$copy_zsys2 = $zsys2;
$copy_zsec2 = $zsec2;

//Agenten/Sonden bauen
if (hasTech($pt, 9)) {
    if (isset($_POST["b110"]) || isset($_POST["b111"])) {//ja, es wurde ein button gedrueckt
        //transaktionsbeginn
        if (setLock($ums_user_id)) {
            for ($i = 110; $i <= 111; $i++) {
                $h = intval($_POST['b'.$i]);
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
                        mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, $i, $z, $tech_ticks)");
                        write2agentlog($_SESSION['ums_user_id'], 'build', $z);
                    }

                    /*
                    $sql="SELECT anzahl FROM de_user_build WHERE user_id = '$ums_user_id' AND tech_id='$i' AND verbzeit='$tech_ticks'";
                    //echo $sql;
                    $result = mysqli_query($GLOBALS['dbi'],$sql);
                    $row = mysqli_fetch_array($result);
                    if ($z>0){
                        if ($row[0]==0){ //es gibt keine schiffe mit tech_ticks laenge in der queue
                            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, $i, $z, $tech_ticks)");
                            write2agentlog($_SESSION['ums_user_id'], 'build', $z);
                        }else{
                            mysqli_query($GLOBALS['dbi'], "update de_user_build set anzahl = anzahl + $z WHERE user_id = '$ums_user_id' AND tech_id=$i AND verbzeit=$tech_ticks ");
                            write2agentlog($_SESSION['ums_user_id'], 'build', $z);
                        }
                        //echo "Schiffe in Auftrag gegeben: ".$z."<br>";
                    }
                    */
                }
            }

            //aktualisiert die rohstoffe
            $gr01 = $gr01 - $restyp01;
            $gr02 = $gr02 - $restyp02;
            $gr03 = $gr03 - $restyp03;
            $gr04 = $gr04 - $restyp04;
            mysqli_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 - $gr01,
			 restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
			 restyp04 = restyp04 - $gr04 WHERE user_id = '$ums_user_id'");

            //transaktionsende
            $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
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

/*
for($i=1;$i<1000;$i++){
    echo '<br>'.$i.':';
    echo (2*pow(M_E, 0.04*$i))/(pow(M_E,0.04*$i)+100);
}*/

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

//geheimdienst deaktiviert?
if (isset($sv_deactivate_secret) && $sv_deactivate_secret == 1) {
    echo '<br><div class="info_box text2">Auf diesem Server ist der Geheimdienst deaktiviert.</div>';
    die('</body></html>');
}


if (!hasTech($pt, 9)) {
    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=9";
    $db_tech = mysqli_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_array($db_tech);

    //echo $secret_lang[eswirdeine].$row_techcheck[tech_name].$secret_lang[benoetigt];
    echo '<br>';
    rahmen_oben('Fehlende Technologie');
    echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=9" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_9.jpg" border="0"></a></td>
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
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id, spielername, rasse FROM de_user_data WHERE sector='$zsec1' and system='$zsys1'");
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {//die koordinaten stimmen, gib die daten aus
            $row = mysqli_fetch_array($db_daten);
            $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
            //db updaten
            mysqli_query($GLOBALS['dbi'], "UPDATE de_user_scan SET ps='$ps' WHERE user_id='$ums_user_id' AND zuser_id='$uid'");
            //nach dem update die scanhistory anzeigen
            $showscanhistory = 1;
        }
    }

    //beim agentenziel bei herkunft vom sektor auch bei den sonden die koordinaten hinterlegen
    if (isset($_REQUEST['a']) && $_REQUEST['a'] == 'a') {
        $zsec1 = $zsec2;
        $zsys1 = $zsys2;
    }


    //scanhistory ausgeben
    if ((isset($_GET["a"]) && $_GET["a"] == 'd') || $showscanhistory == 1) {
        //agentenkoordinaten vorbelegen
        $zsec2 = $zsec1;
        $zsys2 = $zsys1;
        //user_id des ziels auslesen
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id, spielername, rasse FROM de_user_data WHERE sector='$zsec1' and system='$zsys1'");
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {//die koordinaten stimmen, gib die daten aus
            echo '<form action="secret.php" method="POST">';

            $row = mysqli_fetch_array($db_daten);
            $uid = $row["user_id"]; //hole die user_id des users um die daten anfordern zu k�nnen
            $spielername = $row["spielername"];
            $zrasse = $row["rasse"];
            //scandaten aus der db holen
            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_scan WHERE user_id='$ums_user_id' AND zuser_id='$uid'");
            $num = mysqli_num_rows($db_daten);
            if ($num != 1) {//datensatz vorhanden, falls nicht einen anlegen und es nochmal versuchen
                mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_scan SET user_id='$ums_user_id', zuser_id='$uid'");
                $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_scan WHERE user_id='$ums_user_id' AND zuser_id='$uid'");
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
            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector='$zsec1' AND id=22");
            if (mysqli_num_rows($db_daten) == 1) {
                $herakles = 1;
            }


            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id, col, score, e100, e101, e102, e103, e104, techs, rasse, spielername, restyp01, restyp02, restyp03, restyp04, restyp05, npc FROM de_user_data WHERE sector='$zsec1' and system='$zsys1'");
            $num = mysqli_num_rows($db_daten);
            if ($num == 1 and $herakles == 0) {//die koordinaten stimmen, gib die daten aus
                $row = mysqli_fetch_array($db_daten);
                //test auf npc
                if ($row['npc'] == 0) {
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
                    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
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
                        $str = '';
                        for ($i = 81;$i <= 90;$i++) {
                            $str = $str."\$ec$i=\$ec$i+\$row[\"e$i\"];";
                        }
                        eval($str); //variablen -> ec81, ec82...
                    }
                    $zeinheiten = $ec81 + $ec82 + $ec83 + $ec84 + $ec85 + $ec86 + $ec87 + $ec88 + $ec89 + $ec90;
                    //zaehle alle schiffe, die schon vorhanden sind - ende

                    $anzgeb = 0;
                    //for ($i=1;$i<=39;$i++) if ($ztechs[$i]==1) $anzgeb++;

                    //schiffe im bau
                    $eimbau = 0;
                    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT anzahl FROM de_user_build WHERE user_id=$uid");
                    while ($row = mysqli_fetch_array($db_daten)) {
                        $eimbau = $eimbau + $row["anzahl"];
                    }
                    //schauen ob der spieler in den letzten 12 stunden online war
                    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT last_login FROM de_login WHERE user_id='$uid'");
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
                        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id='$own_ally_id' AND status=1 AND user_id<>'".$_SESSION['ums_user_id']."'");
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
                            $result  = mysqli_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id='$partner_ally_id'");
                            $row     = mysqli_fetch_array($result);
                            $partner_ally_bldg2 = $row["bldg2"];

                            if ($partner_ally_bldg2 > 1) {
                                $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id='$partner_ally_id' AND status=1");
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
                        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT rasse FROM de_user_scan WHERE user_id='$save_uid' AND zuser_id='$uid'");
                        $scan_vorhanden = mysqli_num_rows($db_daten);
                        if ($scan_vorhanden == 0) {
                            //wenn es noch gar keinen scan gibt, dann muß einer in die db
                            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_scan SET stime=UNIX_TIMESTAMP( ), score='$zpunkte', fleet='$zeinheiten', defense='$vertanz',
					build='$eimbau', col='$zcol', buildings='$anzgeb' ,rasse='$zrasse',
					restyp01='$zres[0]', restyp02='$zres[1]',restyp03='$zres[2]',restyp04='$zres[3]',restyp05='$zres[4]', user_id='$save_uid', zuser_id='$uid'");
                        } else { //daten aktualisieren
                            mysqli_query($GLOBALS['dbi'], "UPDATE de_user_scan SET stime=UNIX_TIMESTAMP( ), score='$zpunkte', fleet='$zeinheiten', defense='$vertanz',
					build='$eimbau', col='$zcol', buildings='$anzgeb' ,rasse='$zrasse',
					restyp01='$zres[0]', restyp02='$zres[1]',restyp03='$zres[2]',restyp04='$zres[3]',restyp05='$zres[4]'
					WHERE user_id='$save_uid' AND zuser_id='$uid'");
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
                        $time = strftime("%Y%m%d%H%M%S");
                        $textscanner = $secret_lang['diescannerhaben'].$sector.$secret_lang['diescannerhaben2'];
                        mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 4,'$time','$textscanner')");
                        mysqli_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = $uid");
                    }
                    //eine sonde abziehen
                    mysqli_query($GLOBALS['dbi'], "UPDATE de_user_data SET sonde = sonde - 1 WHERE user_id = $ums_user_id");
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
    $etyp = isset($_POST['etyp']) ? $_POST['etyp'] : '';
    $az = isset($_POST['az']) ? intval($_POST['az']) : '';

    if (isset($_POST["zsec2"]) && isset($_POST["zsys2"]) && !isset($_POST["b110"]) && !isset($_POST["b111"]) && hasTech($pt, 111)) {
        
        if (validDigit($zsec2) && validDigit($zsys2) && validDigit($az)) {
           
            if ($az == '') {
                $az = 0;
            }
            $az = (int)$az;
            if ($az > $agent) {
                $az = $agent;
            }

            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id, techs, agent, spielername, rasse, techs, npc, sc1, sc2, sc3, sc4, spec4 FROM de_user_data WHERE sector='$zsec2' and system='$zsys2'");
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
                $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=4 AND user_id='$uid'");
                $zartbonusdeff = 0;
                while ($row = mysqli_fetch_array($db_daten)) {
                    $zartbonusdeff = $zartbonusdeff + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
                }


                //rassenboni-mali verteilen
                if ($ums_rasse == 1 and $rasse == 1) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 1 and $rasse == 2) {
                    $bomalus = 5;
                }
                if ($ums_rasse == 1 and $rasse == 3) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 1 and $rasse == 4) {
                    $bomalus = -5;
                }
                if ($ums_rasse == 1 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($ums_rasse == 2 and $rasse == 1) {
                    $bomalus = -5;
                }
                if ($ums_rasse == 2 and $rasse == 2) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 2 and $rasse == 3) {
                    $bomalus = -5;
                }
                if ($ums_rasse == 2 and $rasse == 4) {
                    $bomalus = -10;
                }
                if ($ums_rasse == 2 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($ums_rasse == 3 and $rasse == 1) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 3 and $rasse == 2) {
                    $bomalus = 5;
                }
                if ($ums_rasse == 3 and $rasse == 3) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 3 and $rasse == 4) {
                    $bomalus = -5;
                }
                if ($ums_rasse == 3 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($ums_rasse == 4 and $rasse == 1) {
                    $bomalus = 5;
                }
                if ($ums_rasse == 4 and $rasse == 2) {
                    $bomalus = 10;
                }
                if ($ums_rasse == 4 and $rasse == 3) {
                    $bomalus = 5;
                }
                if ($ums_rasse == 4 and $rasse == 4) {
                    $bomalus = 0;
                }
                if ($ums_rasse == 4 and $rasse == 5) {
                    $bomalus = -10;
                }

                if ($ums_rasse == 5 and $rasse == 1) {
                    $bomalus = 10;
                }
                if ($ums_rasse == 5 and $rasse == 2) {
                    $bomalus = 10;
                }
                if ($ums_rasse == 5 and $rasse == 3) {
                    $bomalus = 10;
                }
                if ($ums_rasse == 5 and $rasse == 4) {
                    $bomalus = 10;
                }
                if ($ums_rasse == 5 and $rasse == 5) {
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
                    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector='$zsec2' AND id=21");
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
                        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id='$own_ally_id' AND status=1 AND user_id<>'".$_SESSION['ums_user_id']."'");
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
                            $result  = mysqli_query($GLOBALS['dbi'], "SELECT bldg2 FROM de_allys WHERE id='$partner_ally_id'");
                            $row     = mysqli_fetch_array($result);
                            $partner_ally_bldg2 = $row["bldg2"];

                            if ($partner_ally_bldg2 > 1) {
                                $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id='$partner_ally_id' AND status=1");
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
                        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT allytag FROM de_user_scan WHERE user_id='$save_uid' AND zuser_id='$uid'");
                        $scan_vorhanden = mysqli_num_rows($db_daten);
                        if ($scan_vorhanden == 0) {
                            //wenn es noch gar keinen scan gibt, dann mu� einer in die db
                            mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_scan SET user_id='$save_uid', zuser_id='$uid'");
                        }
                        //rasse im geheimdienstbericht hinterlegen, da man diese nach einem agenteneinsatz auf jeden fall kennt
                        mysqli_query($GLOBALS['dbi'], "UPDATE de_user_scan SET rasse='$rasse' WHERE user_id='$save_uid' AND zuser_id='$uid' AND rasse=0");
                    }

                    switch ($etyp) {
                        case 0: //schiffsübersicht
                            //zaehle alle schiffe, die schon vorhanden sind - anfang
                            $fid0 = $uid.'-0';
                            $fid1 = $uid.'-1';
                            $fid2 = $uid.'-2';
                            $fid3 = $uid.'-3';
                            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
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
                                mysqli_query($GLOBALS['dbi'], "UPDATE de_user_scan SET ftime=UNIX_TIMESTAMP( ),
					e81='$e81', e82='$e82', e83='$e83', e84='$e84', e85='$e85', e86='$e86', e87='$e87', e88='$e88', e89='$e89',e90='$e90'
					WHERE user_id='$save_uid' AND zuser_id='$uid'");
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
                            unset($fleetpoints);
                            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT  tech_id, tech_name FROM de_tech_data WHERE tech_id>80 AND tech_id<100");
                            while ($row = mysqli_fetch_array($db_daten)) { //jeder gefundene datensatz wird geprueft
                                echo '<tr>';
                                echo '<td class="cc">'.getTechNameByRasse($row["tech_name"], $zrasse)."</td>";
                                echo '<td class="cc">'.number_format($schiffe[0][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[1][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[2][$row["tech_id"] - 81], 0, "", ".")."</td>";
                                echo '<td class="cc">'.number_format($schiffe[3][$row["tech_id"] - 81], 0, "", ".")."</td>";

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
                            $result = mysqli_query($GLOBALS['dbi'], "SELECT zielsec, zielsys, aktion, aktzeit, zeit, mission_time FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
                            $ed_id = 0;
                            while ($row = mysql_fetch_array($result)) {
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
                            $str = '';
                            for ($i = 100;$i <= 109;$i++) {
                                $str = $str."\$ec$i=0;";
                            }
                            eval($str); //variablen -> ec100, ec101,...
                            $db_daten = mysql_query("SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=$uid", $db);
                            $row = mysql_fetch_array($db_daten);
                            $str = '';
                            for ($i = 100;$i <= 109;$i++) {
                                $str = $str."\$ec$i=\$ec$i+\$row[\"e$i\"];";
                            }
                            eval($str);
                            //zaehle alle verteidigungsanlagen, die schon vorhanden sind - ende

                            //die daten in de_user_scan hinterlegen
                            for ($i = 0;$i < count($savelist);$i++) {
                                $save_uid = $savelist[$i];

                                mysql_query("UPDATE de_user_scan SET dtime=UNIX_TIMESTAMP( ),
					e100='$row[e100]', e101='$row[e101]', e102='$row[e102]', e103='$row[e103]', e104='$row[e104]'
					WHERE user_id='$save_uid' AND zuser_id='$uid'", $db);
                            }

                            //ueberschrift ausgeben
                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            echo '<tr>';
                            echo '<td class="tc" width="100%"><b>'.$secret_lang['uebersichtvanlagen'].$zname.' ('.$zsec2.':'.$zsys2.')</b></td>';
                            echo '</tr>';
                            echo '</table>';

                            //lade einheitentypen
                            $db_daten = mysql_query("SELECT tech_id, tech_name FROM de_tech_data WHERE tech_id>99 AND tech_id<110 ORDER BY tech_id", $db);

                            echo '<table border="0" cellpadding="0" cellspacing="1" width="400px">';
                            $gespunkte = 0;
                            while ($row = mysql_fetch_array($db_daten)) { //jeder gefundene datensatz wird geprueft
                                $str = '$ec=$ec'.$row["tech_id"].';';
                                eval($str);

                                $gespunkte += $ec * $unit[$rasse - 1][$row["tech_id"] - 90][4];

                                echo '<tr>';
                                echo '<td class="cc" width="70%" align="left">'.utf8_encode(getTechNameByRasse($row["tech_name"], $zrasse))."</td>";
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

                            $db_daten = mysql_query("SELECT time, typ, text FROM de_user_news WHERE user_id='$uid' AND typ <> 60 ORDER BY time DESC", $db);
                            while ($row = mysql_fetch_array($db_daten)) { //jeder gefundene datensatz wird ausgegeben
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
                                        echo '<td class="cc">'.utf8_encode(showkampfberichtV1($row["text"], $rasse, $zname, $zsec2, $zsys2, $schiffspunkte)).'</td>';
                                        echo '</tr>';
                                        break;
                                    case 70:
                                        echo '<tr>';
                                        echo '<td class="cl">'.$time.'</td>';
                                        echo '</tr>';
                                        echo '<tr>';
                                        echo '<td class="cc">'.utf8_encode(showkampfberichtBG($row["text"])).'</td>';
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
                            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT tech_id, tech_name FROM de_tech_data ORDER BY tech_level");
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
                            $db_daten = mysql_query("SELECT allytag, status FROM de_user_data WHERE user_id='$uid'", $db);
                            $row = mysql_fetch_array($db_daten);

                            $allytag = $row["allytag"];

                            //die daten in de_user_scan hinterlegen
                            if ($allytag == '' or $row["status"] != 1) {
                                $saveallytag = '';
                            } else {
                                $saveallytag = $allytag;
                            }
                            for ($i = 0;$i < count($savelist);$i++) {
                                $save_uid = $savelist[$i];

                                mysql_query("UPDATE de_user_scan SET atime=UNIX_TIMESTAMP( ), allytag='$saveallytag' WHERE user_id='$save_uid' AND zuser_id='$uid'", $db);
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
                            $eta1 = mysql_query("SELECT SUM(fleetsize) AS fleetsize FROM de_user_fleet WHERE zielsec = '$zsec2' AND zielsys = '$zsys2' AND aktion = 1 AND entdeckt > 0 AND zeit = 1", $db);
                            $eta2 = mysql_query("SELECT SUM(fleetsize) AS fleetsize FROM de_user_fleet WHERE zielsec = '$zsec2' AND zielsys = '$zsys2' AND aktion = 1 AND entdeckt > 0 AND zeit = 2", $db);
                            $eta1 = mysql_fetch_array($eta1);
                            $eta1 = $eta1["fleetsize"];
                            $eta2 = mysql_fetch_array($eta2);
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
                            $time=strftime("%Y%m%d%H%M%S");
                            if($enttarnt>0){
                                if($enttarnt>1){
                                    $msg='Bei einem feindlichen Agenteneinsatz von '.$ums_spielername.' ('.$sector.':'.$system.') wurden '.number_format($enttarnt, 0,",",".").' Agenten enttarnt und arbeiten jetzt als Z&ouml;llner.';
                                }else{
                                    $msg='Bei einem feindlichen Agenteneinsatz von '.$ums_spielername.' ('.$sector.':'.$system.') wurde '.number_format($enttarnt, 0,",",".").' Agente enttarnt und arbeitet jetzt als Z&ouml;llner.';
                                }
                                mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')",$db);
                                mysql_query("update de_user_data set newnews = 1, agent = agent - '$enttarnt', agent_lost=agent_lost + '$enttarnt' where user_id = '$uid'",$db);
                            }

                            //eigene agenten abziehen
                            if($eigene_verluste>0){
                                mysql_query("UPDATE de_user_data SET agent = agent - '$eigene_verluste', agent_lost=agent_lost + '$eigene_verluste' WHERE user_id = $ums_user_id",$db);
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
                                    mysql_query("UPDATE de_user_data SET agent = agent - '$aze', agent_lost=agent_lost + '$aze' WHERE user_id = $ums_user_id", $db);
                                    write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);

                                    $agent = $agent - $aze;

                                    $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                    $emsg .= '</td></tr></table>';
                                    echo $emsg;

                                    //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                    $time = strftime("%Y%m%d%H%M%S");
                                    $msg = $secret_lang['erfolgsnachricht_sabotage_kollektoroutput'];
                                    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')", $db);
                                    //sabotage counter setzen und dass er nen neue info hat
                                    mysql_query("UPDATE de_user_data SET newnews = 1, sc1 = '$maxroundtick' WHERE user_id = '$uid'", $db);
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
                                        mysql_query("update de_user_data set agent = agent - '$aze', agent_lost=agent_lost + '$aze' where user_id = $ums_user_id", $db);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = strftime("%Y%m%d%H%M%S");
                                        $msg = $secret_lang['erfolgsnachricht_sabotage_raumwerft'];
                                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')", $db);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysql_query("UPDATE de_user_data SET newnews = 1, sc2 = '$maxroundtick' WHERE user_id = '$uid'", $db);
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
                                        mysql_query("UPDATE de_user_data SET agent = agent - '$aze', agent_lost=agent_lost + '$aze' WHERE user_id = $ums_user_id", $db);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = strftime("%Y%m%d%H%M%S");
                                        $msg = $secret_lang['erfolgsnachricht_sabotage_verteidigungszentrum'];
                                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')", $db);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysql_query("UPDATE de_user_data SET newnews = 1, sc3 = '$maxroundtick' WHERE user_id = '$uid'", $db);
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
                                        mysql_query("update de_user_data set agent = agent - '$aze', agent_lost=agent_lost + '$aze' where user_id = $ums_user_id", $db);
                                        write2agentlog($_SESSION['ums_user_id'], 'saboteage-lost', $aze);
                                        $agent = $agent - $aze;

                                        $emsg .= '<br>'.$secret_lang['agentenverluste'].': '.number_format($aze, 0, ",", ".");

                                        $emsg .= '</td></tr></table>';
                                        echo $emsg;

                                        //info an den account schicken, dass bei ihm ein agenteneinsatz gelungen ist
                                        $time = strftime("%Y%m%d%H%M%S");
                                        $msg = 'Ein Agenteneinsatz hat f&uuml;r Sch&auml;den am Missionssystem gesorgt. Mehr Informationen sind im Geheimdienst abrufbar.';
                                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')", $db);
                                        //sabotage counter setzen und dass er nen neue info hat
                                        mysql_query("UPDATE de_user_data SET newnews = 1, sc4 = '$maxroundtick' WHERE user_id = '$uid'", $db);
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

                    $time = strftime("%Y%m%d%H%M%S");
                    if ($aze == 0) {
                        $aze = 1;
                    }
                    echo '<table width="600"><tr align="center"><td><div class="cell">'.$secret_lang['einsatzgescheitert'].$aze.$secret_lang['einsatzgescheitert2'].'</div></td></tr></table><br>';
                    if ($aze == 1) {
                        $msg = $secret_lang['einsatzentdeckt'].$ums_spielername.' ('.$sector.':'.$system.$secret_lang['einsatzentdeckt2'].$aze.$secret_lang['einsatzentdeckt3'];
                    } else {
                        $msg = $secret_lang['einsatzentdeckt'].$ums_spielername.' ('.$sector.':'.$system.$secret_lang['einsatzentdeckt2'].$aze.$secret_lang['einsatzentdeckt4'];
                    }
                    //msg um die eigenen verlust erweitern
                    /*
                    $msg.=$secret_lang[einsatzentdeckt5];
                    if($zagentabz==0) $msg.=$secret_lang[einsatzentdeckt8];
                    elseif($zagentabz==1) $msg.=$zagentabz.$secret_lang[einsatzentdeckt6];
                    else $msg.=$zagentabz.$secret_lang[einsatzentdeckt7];
                    */

                    //info an das ziel und ggf. agenten abziehen
                    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 5,'$time','$msg')", $db);
                    mysql_query("update de_user_data set newnews = 1, agent = agent - '$zagentabz', agent_lost=agent_lost + '$zagentabz' where user_id = '$uid'", $db);

                    //eigene agenten abziehen
                    mysql_query("update de_user_data set agent = agent - '$aze', agent_lost=agent_lost + '$aze' where user_id = $ums_user_id", $db);
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
                    $entry = utf8_decode($entry);
                    mysql_query("update de_user_data set scanhistory='$entry' where user_id = $ums_user_id", $db);
                } else { //Scanhistorie updaten
                    $drin = 0;
                    $i = 0;
                    $einsaetze = array(array(),);
                    $scanhis = explode("|", $scanhistory);
                    while ($i < Count($scanhis)) {
                        $daten = explode(":", $scanhis[$i]);
                        $einsaetze[$i][0] = $daten[0];
                        $einsaetze[$i][1] = $daten[1];
                        $einsaetze[$i][2] = $daten[2];
                        $i++;
                    }
                    $i = 0;
                    while ($i < Count($einsaetze)) {
                        if (($einsaetze[$i][0] == $copy_zsec2) && ($einsaetze[$i][1] == $copy_zsys2)) {
                            $drin = 1;
                        }
                        $i++;
                    }
                    $entry = $copy_zsec2.':'.$copy_zsys2.':'.$zname.'|';
                    $i = 0;
                    if ($drin == "0") {
                        while ($i < Count($einsaetze) - 1) {
                            if ($i < 4) {
                                $entry = $entry.''.$einsaetze[$i][0].':'.$einsaetze[$i][1].':'.$einsaetze[$i][2].'|';
                            }
                            $i++;
                        }
                        $entry = utf8_decode($entry);
                        mysql_query("update de_user_data set scanhistory='$entry' where user_id = $ums_user_id", $db);
                    }
                }
            }
        }
    }



    if ($sonde > 0 && hasTech($pt, 9) && hasTech($pt, 110)) {//stelle sondenmen� dar
        echo '<form action="secret.php" method="POST" name="sonde">';
        rahmen_oben($secret_lang['sondengeheimaktion']);
        echo '<table border="0" cellpadding="0" cellspacing="1">';
        $bg = 'cell1';

        echo '<tr align="center">';
        echo '<td width="200" class="'.$bg.'">'.$secret_lang['vorhanden'].'</td>';
        echo '<td width="160" class="'.$bg.'">'.$secret_lang['zielkoordinaten'].'</td>';
        echo '<td width="200" class="'.$bg.'">'.$secret_lang['aktion'].'</td>';
        echo "</tr>";

        $bg = 'cell';
        echo '<tr align="center">';
        echo '<td class="'.$bg.'" align="center">'.$sonde.'</td>';
        echo '<td class="'.$bg.'" align="center"><input type="text" name="zsec1" id="zsec1" value="'.$zsec1.'" size="3" maxlength="5">&nbsp;&nbsp;<input type="text" name="zsys1" id="zsys1" value="'.$zsys1.'" size="3" maxlength="3"></td>';
        echo '<td class="'.$bg.'"><input type="Submit" name="startsonde" value="'.$secret_lang['sondestarten'].'"></td>';
        echo '</tr>';
        echo '</table>';
        rahmen_unten();
        echo '</form>';
    }

    if (hasTech($pt, 111)) {//stelle agentenmen� dar
        echo '<form action="secret.php" method="POST" name="agent">';
        rahmen_oben($secret_lang['aggigeheimaktion']);
        echo '<table border="0" cellpadding="0" cellspacing="1">';
        $bg = 'cell1';
        echo '<tr align="center">';
        echo '<td width="83" class="'.$bg.'">'.$secret_lang['vorhanden'].'</td>';
        echo '<td width="73" class="'.$bg.'">'.$secret_lang['einsetzen'].'</td>';
        echo '<td width="189" class="'.$bg.'">'.$secret_lang['einsatzziel'].'</td>';
        echo '<td width="100" class="'.$bg.'">'.$secret_lang['zielkoordinaten'].'</td>';
        echo '<td width="115" class="'.$bg.'">'.$secret_lang['aktion'].'</td>';
        echo '</tr>';

        $agenten_einsetzen = $agent;
        if (isset($_REQUEST['az'])) {
            $agenten_einsetzen = intval($_REQUEST['az']);
        }

        $bg = 'cell';
        echo '<tr align="center">';
        echo '<td class="'.$bg.'">'.number_format($agent, 0, "", ".").' <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$secret_lang['boni'].'&'.$bstr.'"></td>';
        echo '<td class="'.$bg.'"><input type="text" name="az" value="'.$agenten_einsetzen.'" size="5" maxlength="10" autocomplete="off"></td>';

        echo '<td class="'.$bg.'"><select name="etyp" size="0" onChange="sei(this.options[this.selectedIndex].value)">';

        if (!isset($_REQUEST['etyp'])) {
            $_REQUEST['etyp'] = 3;
        }

        if ($_REQUEST['etyp'] == 3) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="3"'.$selected.'>'.$secret_lang['nachrichten'].'</option>';
        if ($_REQUEST['etyp'] == 0) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="0"'.$selected.'>'.$secret_lang['flottenaufstellung'].'</option>';
        if ($_REQUEST['etyp'] == 1) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="1"'.$selected.'>'.$secret_lang['flottenauftrag'].'</option>';
        if ($_REQUEST['etyp'] == 2) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="2"'.$selected.'>'.$secret_lang['verteidigungsanlagen'].'</option>';
        if ($_REQUEST['etyp'] == 4) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="4"'.$selected.'>'.$secret_lang['entwicklungen'].'</option>';
        if ($_REQUEST['etyp'] == 5) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="5"'.$selected.'>'.$secret_lang['allytag'].'</option>';
        if ($_REQUEST['etyp'] == 6) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="6"'.$selected.'>'.$secret_lang['systemstatus'].'</option>';
        //if($_REQUEST['etyp']==11){$selected=' selected';}else{$selected='';}
        //echo '<option value="11"'.$selected.'>Enttarnung</option>';
        if ($_REQUEST['etyp'] == 7) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="7"'.$selected.'>'.$secret_lang['sabotage_kollektoroutput'].'</option>';
        if ($_REQUEST['etyp'] == 8) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="8"'.$selected.'>'.$secret_lang['sabotage_raumwerft'].'</option>';
        //if($_REQUEST['etyp']==9){$selected=' selected';}else{$selected='';}
        //echo '<option value="9"'.$selected.'>'.$secret_lang[sabotage_verteidigungszentrum].'</option>';
        if ($_REQUEST['etyp'] == 10) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        echo '<option value="10"'.$selected.'>S: Missionsystem</option>';

        echo '</select></td>';

        echo '<td class="'.$bg.'"><input type="text" name="zsec2" id="zsec2" value="'.$zsec2.'" size="3" maxlength="5">&nbsp;&nbsp;
			<input type="text" name="zsys2" id="zsys2" value="'.$zsys2.'" size="3" maxlength="3"></td>';
        echo '<td class="'.$bg.'"><input type="Submit" name="startagent" value="'.$secret_lang['einsatzstarten'].'"></td>';
        echo '</tr>';
        echo '<tr><td width="560" colspan="5" class="'.$bg.'"><div id="seii"></div></td></tr>';

        echo '</table>';

        rahmen_unten();
        echo '</form>';


        rahmen_oben($secret_lang['letzteeinsaetze']);

        echo '<table width="568" border="0" cellpadding="0" cellspacing="1">';
        $bg = 'cell';

        if ($scanhistory == "") {
            echo '<tr>
			   <td class="'.$bg.'" align="center" colspan="3"> - </td>
			 </tr>';
        } else {
            $i = 0;
            $scanhistory = explode("|", $scanhistory);

            while ($i < (Count($scanhistory) - 1)) {
                $daten = explode(":", $scanhistory[$i]);
                echo '
			<tr>
				<td  class="cell" nowrap align="center">&nbsp;&nbsp;&nbsp;<a href="javascript:insertsonde('.$daten[0].','.$daten[1].')">'.$secret_lang['sondenzielproggen'].'</a></td>
				<td  class="cell" nowrap><table border="0" cellpadding="0" cellspacing="0"><tr><td width="20">&nbsp;</td><td class="cell"><b>'.$daten[0].':'.$daten[1].'&nbsp;('.$daten[2].')</b></td></tr></table></td>
				<td  class="cell" nowrap align="center">&nbsp;&nbsp;&nbsp;<a href="javascript:insertagent('.$daten[0].','.$daten[1].')">'.$secret_lang['infiltrieren'].'</a></td>
			</tr>';
                $i++;
            }
        }

        echo '</table>';
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

        echo 'sei('.$_REQUEST['etyp'].');';
        echo '</script>';


    }

    echo '<form action="secret.php" method="POST" name="produktion">';
    echo
    '<table border="0" cellpadding="0" cellspacing="0">
	<tr height="37">
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td width="208" class="ro"><div class="cellu">&nbsp;&nbsp;'.$secret_lang['sondenundaggis'].': <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$buildstatus.'"></div></td>
	<td width="50" align="center" class="ro"><div class="cellu">M</div></td>
	<td width="50" align="center" class="ro"><div class="cellu">D</div></td>
	<td width="50" align="center" class="ro"><div class="cellu">I</div></td>
	<td width="50" align="center" class="ro"><div class="cellu">E</div></td>
	<td width="27" align="center" class="ro"><div class="cellu">T</div></td>
	<td width="33" align="center" class="ro"><div class="cellu">'.$secret_lang['wochen'].'</div></td>
	<td width="50" align="center" class="ro"><div class="cellu">'.$secret_lang['stueck'].'</div></td>
	<td width="50" align="center" class="ro"><div class="cellu">A/B</div></td>
	<td width="13" class="ror">&nbsp;</td>
	</tr>';
    ?>
	<tr>
	<td width="13" class="rl">&nbsp;</td>
	<td colspan="9">
	<table border="0" cellpadding="0" cellspacing="1" width="100%">
	<colgroup>
	<col width="">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="20">
	<col width="30">
	<col width="50">
	<col width="50">
	</colgroup>

	<?php
      $db_daten = mysql_query("SELECT * FROM de_tech_data WHERE tech_id>=110 AND tech_id<=111 ORDER BY tech_id", $db);

    $ergebnis = mysql_query("SELECT  sonde, agent  FROM de_user_data WHERE user_id='$ums_user_id'", $db);
    $rowe = mysql_fetch_array($ergebnis);

    //Check f�r Echtzeitrechner
    $techtrue = 0;

    $c1 = 1;
    $c2 = 0;
    $z = 0;
    //zerlege vorbedinguns-string
    while ($row = mysql_fetch_array($db_daten)) {
        $tech_id = $row['tech_id'];
        if (hasTech($pt, $tech_id)) { //echo "Vorbedingung erf�llt";
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell';
            } else {
                $c1 = 0;
                $bg = 'cell1';
            }

            $ec = 0;
            $z = 0;
            if ($tech_id == 110) {//Sonde
                $ec = $rowe["sonde"];
                $z = 0;
            } elseif (
                $tech_id == 111) {//Agent
                $ec = $rowe["agent"];
                $z = 1;
            }

            showeinheit2(
                getTechNameByRasse($row["tech_name"], $_SESSION['ums_rasse']),
                $tech_id,
                $einheiten_daten[$tech_id]['kosten'][0] - round($einheiten_daten[$tech_id]['kosten'][0] * $artbonusbuild / 100),
                $einheiten_daten[$tech_id]['kosten'][1] - round($einheiten_daten[$tech_id]['kosten'][1] * $artbonusbuild / 100),
                $einheiten_daten[$tech_id]['kosten'][2] - round($einheiten_daten[$tech_id]['kosten'][2] * $artbonusbuild / 100),
                $einheiten_daten[$tech_id]['kosten'][3] - round($einheiten_daten[$tech_id]['kosten'][3] * $artbonusbuild / 100),
                $einheiten_daten[$tech_id]['kosten'][4] - round($einheiten_daten[$tech_id]['kosten'][4] * $artbonusbuild / 100),
                $einheiten_daten[$tech_id]['bz'],
                $ec,
                $bg,
                $z
            );

            $techtrue++;
        }
    }
    ?>
	</table>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>

	<?php
    //oberer rahmen von der echtzeitrechnung
    echo '<table border="0" cellpadding="0" cellspacing="0">
		  <tr>
		  <td width="13" height="37" class="rml">&nbsp;</td>
		  <td align="left" class="ro"><div class="cellu">&nbsp;'.$secret_lang['kostenaggis'].'</div></td>
		  <td width="13" class="rmr">&nbsp;</td>
		  </tr>
		  <tr>
		  <td class="rl">&nbsp;</td><td>';

    echo '
	<table border="0" cellpadding="1" cellspacing="1">
	<tr height="20" class="cell1" align="center">
	<td width="70">&nbsp;</td>
	<td width="76">M</td>
	<td width="76">D</td>
	<td width="76">I</td>
	<td width="76">E</td>
	<td width="42">T</td>
	<td width="130">'.$secret_lang['punkte'].'</td>
	</tr>';
    echo '
	<tr height="20" class="cell" align="center">
	<td>&nbsp;'.$secret_lang['summe'].'</td>
	<td id="m">0</td>
	<td id="d">0</td>
	<td id="i">0</td>
	<td id="e">0</td>
	<td id="t">0</td>
	<td id="p">0</td>
	</tr>
	<tr class="cell1">
	<td align="center" colspan="7"><input type="Submit" name="trainbuild" value="'.$secret_lang['ausbildenbauen'].'"></td>
	</tr>

	</table>
	';

    //aktive bauauftr�ge
    //$result=mysql_query("SELECT  de_user_build.anzahl, de_user_build.verbzeit, de_tech_data.tech_name FROM de_tech_data, de_user_build WHERE user_id=$ums_user_id AND de_user_build.tech_id = de_tech_data.tech_id AND de_user_build.tech_id > 109 AND de_user_build.tech_id < 120 ORDER BY de_user_build.verbzeit ASC",$db);
    $sql = "SELECT SUM(de_user_build.anzahl) AS anzahl, de_user_build.verbzeit, de_tech_data$ums_rasse.tech_name FROM de_user_build LEFT JOIN de_tech_data$ums_rasse on(de_user_build.tech_id = de_tech_data$ums_rasse.tech_id) WHERE user_id=$ums_user_id AND de_user_build.tech_id > 109 AND de_user_build.tech_id < 120 GROUP BY de_user_build.tech_id, de_user_build.verbzeit ORDER BY de_user_build.verbzeit ASC";
    //echo $sql;
    $result = mysql_query($sql, $db);
    $num = mysql_num_rows($result);

    if ($num > 0) {
        echo '</td><td width="13" class="rr">&nbsp;</td></tr></table>
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td width="13" height="37" class="rml">&nbsp;</td>
			<td align="left" class="ro">&nbsp;'.$secret_lang['aktiveproduktion'].'</td>
			<td width="13" class="rmr">&nbsp;</td>
			</tr>
			<tr>
			<td class="rl">&nbsp;</td><td>';

        echo '<table border="0" cellpadding="0" cellspacing="1">';
        echo '<tr align="center">';
        echo '<td class="cell" width="379">'.$secret_lang['einheit2'].'</td>';
        echo '<td class="cell" width="80">'.$secret_lang['anzahl'].'</td>';
        echo '<td class="cell" width="105">'.$secret_lang['wochen'].'</td>';
        echo '</tr>';


        while ($row = mysql_fetch_array($result)) {
            echo '<tr align="center">';
            echo '<td class="cell">'.$row["tech_name"].'</td>';
            echo '<td class="cell">'.number_format($row["anzahl"], 0, "", ".").'</td>';
            echo '<td class="cell">'.$row["verbzeit"].'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</td><td width="13" class="rr">&nbsp;</td>
			</tr>
			<tr>
			<td width="13" class="rul">&nbsp;</td>
			<td class="ru">&nbsp;</td>
			<td width="13" class="rur">&nbsp;</td>
			</tr>
			</table><br>';
    } else { //nur unteren rahmen von der echtzeitrechnung
        echo '</td><td width="13" class="rr">&nbsp;</td>
			</tr>
			<tr>
			<td width="13" class="rul">&nbsp;</td>
			<td class="ru">&nbsp;</td>
			<td width="13" class="rur">&nbsp;</td>
			</tr>
			</table><br>';
    }

    echo '</form>';

    ///////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////
    // anzeige der aktionen die aktuell gegen einen laufen
    ///////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////
    if ($maxroundtick < $mysc1 + $sv_sabotage[7][0] or $maxroundtick < $mysc2 + $sv_sabotage[8][0] or $maxroundtick < $mysc3 + $sv_sabotage[9][0] or $maxroundtick < $mysc4 + $sv_sabotage[10][0]) {
        rahmen_oben($secret_lang['sabotageauswirkungen']);

        echo '<table width="568" border="0" cellpadding="0" cellspacing="1">';

        //kollektorenergieoutput verringern
        if ($maxroundtick < $mysc1 + $sv_sabotage[7][0]) {
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell';
            } else {
                $c1 = 0;
                $bg = 'cell1';
            }
            echo '<tr class="'.$bg.'" align="center"><td>';
            echo $secret_lang['einsatzbeschreibung_sabotage_kollektoroutput2'].': '.$sv_sabotage[7][2];
            echo '<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.($mysc1 + $sv_sabotage[7][0] - $maxroundtick);
            echo '</td></tr>';
        }
        if ($maxroundtick < $mysc2 + $sv_sabotage[8][0]) {
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell';
            } else {
                $c1 = 0;
                $bg = 'cell1';
            }
            echo '<tr class="'.$bg.'" align="center"><td>';
            echo $secret_lang['einsatzbeschreibung_sabotage_raumwerft2'];
            echo '<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.($mysc2 + $sv_sabotage[8][0] - $maxroundtick);
            echo '</td></tr>';
        }
        if ($maxroundtick < $mysc3 + $sv_sabotage[9][0]) {
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell';
            } else {
                $c1 = 0;
                $bg = 'cell1';
            }
            echo '<tr class="'.$bg.'" align="center"><td>';
            echo $secret_lang['einsatzbeschreibung_sabotage_verteidigungszentrum2'];
            echo '<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.($mysc3 + $sv_sabotage[9][0] - $maxroundtick);
            echo '</td></tr>';
        }
        if ($maxroundtick < $mysc4 + $sv_sabotage[10][0]) {
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell';
            } else {
                $c1 = 0;
                $bg = 'cell1';
            }
            echo '<tr class="'.$bg.'" align="center"><td>';
            echo 'Das Missionssystem ist gest&ouml;rt.';
            echo '<br>'.$secret_lang['einsatzbeschreibung_wirkungsdauer'].': '.($mysc4 + $sv_sabotage[10][0] - $maxroundtick);
            echo '</td></tr>';
        }
        echo '</table>';

        rahmen_unten();
    }

} //geheimnienst-abfrage ende

function sabotageallowed($zuid)
{
    global $ums_user_id, $ownsector, $ownally, $db;

    $sabotageallowed = 1;

    //daten des ziels auslesen
    $db_daten = mysql_query("SELECT sector, allytag, status, npc, spec5 FROM de_user_data WHERE user_id='$zuid'", $db);
    $row = mysql_fetch_array($db_daten);
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
        if (mysql_result(mysql_query("SELECT count(*) FROM de_user_data WHERE secatt=0 AND sector='$zsector'", $db), 0) >=
           mysql_result(mysql_query("SELECT count(*) FROM de_user_data WHERE secatt=1 AND sector='$zsector'", $db), 0)) {
            $sabotageallowed = 0;
        }
    }

    //auf ally/b�ndnis pr�fen
    //----------- Ally Feine/Freunde
    $allypartner = array();
    $query = "select id from de_allys where allytag='$ownally'";
    $allyresult = mysql_query($query);
    $at = mysql_num_rows($allyresult);
    if ($at != 0) {
        $allyid = mysql_result($allyresult, 0, "id");

        $allyresult = mysql_query("SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=$allyid or ally_id_2=$allyid) and (ally_id_1=id or ally_id_2=id)", $db);
        while ($row = mysql_fetch_array($allyresult)) {
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
<?php include "fooban.php"; ?>
</body>
</html>
