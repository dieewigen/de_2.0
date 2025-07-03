<?php
set_time_limit(240);
//$directory=str_replace("\\\\","/",$HTTP_SERVER_VARS["SCRIPT_FILENAME"]);
//$directory=str_replace("/tickler/wt.php","/",$directory);
//if ($directory=='')$directory='../';
$directory = '../';
include_once $directory."inc/sv.inc.php";
include_once $directory."inc/env.inc.php";
//überprüfen ob es Zeit für den Tick ist
if ($sv_debug == 0 && $sv_comserver == 0) {
    if (!in_array(intval(date("i")), $GLOBALS['wts'][date("G")])) {
        die('<br>WT: NO TICK TIME<br>');
    }
}

include_once $directory."inccon.php";
include_once $directory."inc/db_ls_connect.inc.php";
//include_once $directory."eftadata/lib/efta_dbconnect.php";
include_once $directory."soudata/lib/sou_dbconnect.php";
if ($sv_comserver == 1) {
    include_once $directory.'inc/svcomserver.inc.php';
}

include_once $directory.'lib/phpmailer/class.phpmailer.php';
include_once $directory.'lib/phpmailer/class.smtp.php';

include_once $directory."inc/artefakt.inc.php";
include_once $directory."inc/lang/".$sv_server_lang."_wt.lang.php";
include_once $directory."inc/lang/".$sv_server_lang."_wt_zufallmsg.lang.php";
include_once $directory."inc/sabotage.inc.php";
include_once $directory."inc/allyjobs.inc.php";
include_once $directory."lib/map_system_defs.inc.php";
include_once $directory."lib/map_system.class.php";
include_once $directory."functions.php";
include_once $directory."issectork.php";
include_once "kt_einheitendaten.php";

//include $directory."cache/anz_user.tmp"; //$gesamtuser=anzahl, die in der datei steht
?>
<html>
<head>
</head>
<body>
<?php

$result = mysql_query("SELECT * FROM de_system", $db);
$row = mysql_fetch_array($result);
$doetick = $row["doetick"];
$winid = $row["winid"];
$winticks = $row["winticks"];
$nachtcron = $row["nachtcron"];
$roundpointsflag = $row["roundpointsflag"];
$rundenalter_wt = $row['wt'];

if ($doetick == 1) {
    echo '<br>Starte Tick';
    print date("d/m/Y - H:i:s");

    //maximalen tick auslesen
    //$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
    $result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1", $db);
    $row     = mysql_fetch_array($result);
    $maxtick = $row["tick"];

    //zuerstmal tick sperren, damit die sich niemals �berholen k�nnen
    mysql_query("update de_system set doetick=0", $db);

    //maximale anzahl von kollektoren auslesen
    $db_daten = mysql_query("SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0", $db);
    $row = mysql_fetch_array($db_daten);
    $maxcol = $row['maxcol'];

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    // beim rundenstart (tick 1), die efta-transmitterquests entfernen
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    if ($maxtick == 1) {
        $db_daten = mysql_query("SELECT efta_user_id FROM de_user_data WHERE efta_user_id>0", $db);

        while ($row = mysql_fetch_array($db_daten)) {
            $efta_user_id = $row["efta_user_id"];
            //datensatz in efta l�schen
            mysql_query("DELETE FROM de_cyborg_quest WHERE typ=1 AND user_id='$efta_user_id'", $eftadb);
        }
        //delete FROM de_cyborg_quest WHERE typ=1 AND user_id IN (select user_id from de_cyborg_data where sn_ext1='RDE' OR sn_ext1='QDE')
    }

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    // alle x ticks erhält eine allianz ein allianzartefakt
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    if ($maxtick % 96 == 0 and $maxtick > 0) {
        mysql_query("UPDATE de_allys SET artefacts=artefacts+1", $db);
    }

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    // allianz-mitglieder-maximum bestimmen
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    //spieler außerhalb von Sektor 1
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anzahl FROM de_user_data WHERE npc=0 AND sector>1;");
    $row = mysqli_fetch_array($db_daten);
    $user = $row['anzahl'];

    //aktueller Maximalwert
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT MAX(memberlimit) AS max FROM de_allys;");
    $row = mysqli_fetch_array($db_daten);
    $max = $row['max'];

    $memberlimit = round(5 + $user / 20);
    if ($memberlimit < $max) {
        $memberlimit = $max;
    }

    //memberlimit in der DB hinterlegen
    mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET memberlimit='$memberlimit';");

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////


    //urlaubszeit abgelaufen?
    $time = time();
    mysql_query("UPDATE de_login SET status = 1 WHERE status = 3 AND $time>UNIX_TIMESTAMP(last_login) AND delmode=0", $db);

    //spieler umziehen, die zu gro� f�r den startsektor sind, au�er die funktion ist deaktiviert
    if ($sv_deactivate_sec1moveout == 0) {
        $db_daten = mysql_query("SELECT de_user_data.user_id, de_user_data.sector, de_user_data.`system` FROM de_login left join de_user_data 
		on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND delmode=0 AND sector=1 AND (col>=10 OR score>=5000000)", $db);

        $num = mysql_num_rows($db_daten);
        echo "<br>$num Spieler aus dem Startsektor geholt.<br>";
        while ($row = mysql_fetch_array($db_daten)) {
            $uid = $row["user_id"];
            $sector = $row["sector"];
            $system = $row["system"];

            //account in den umzugsmodus versetzen
            mysql_query("UPDATE de_login set status = 4, savestatus=1 WHERE user_id = '$uid'", $db);
            //umzug hinterlegen
            mysql_query("INSERT INTO de_sector_umzug set user_id='$uid', typ=0, sector='$sector', `system`='$system'", $db);
        }
    }

    //////////////////////////////////////////////////////////
    //EWIGE RUNDE - Kollektoren aus Sektor 1 transferieren
    //////////////////////////////////////////////////////////
    //if($sv_ewige_runde==1){
    //gibt es einen spieler in sektor 1 mit mehr als X Kollektoren?
    $db_daten = mysql_query("SELECT * FROM `de_user_data` LEFT JOIN `de_login` ON(de_login.user_id=de_user_data.user_id) WHERE de_user_data.sector=1 AND de_user_data.col>25 AND de_login.status=3 AND de_login.delmode=2 ORDER BY de_user_data.col DESC LIMIT 1", $db);
    $num = mysql_num_rows($db_daten);
    if ($num == 1) {
        echo "<br>$num Spieler aus dem Startsektor geholt.<br>";
        $row = mysql_fetch_array($db_daten);

        //Spieler mit den wenigsten Kollektoren suchen und ihm einen Kollektor �bertragen
        $db_datenx = mysql_query("SELECT * FROM `de_user_data` WHERE sector>1 AND npc=0 ORDER BY col ASC LIMIT 1", $db);
        $numx = mysql_num_rows($db_datenx);
        if ($numx == 1) {
            echo "<br>$num Spieler aus dem Startsektor geholt.<br>";
            $rowx = mysql_fetch_array($db_datenx);

            //abziehen und informieren
            mysql_query("UPDATE de_user_data SET col=col-1, newnews=1 WHERE user_id='".$row['user_id']."'", $db);
            $time = strftime("%Y%m%d%H%M%S");
            $msg = 'Du verlierst einen Kollektor an einen anderen Spieler au&szlig;erhalb von Sektor 1.';
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES (".$row["user_id"].", 3,'$time','$msg')", $db);

            //draupacken und informieren
            mysql_query("UPDATE de_user_data SET col=col+1, newnews=1 WHERE user_id='".$rowx['user_id']."'", $db);
            $msg = 'Du erh&auml;ltst einen Kollektor von einem anderen Spieler aus Sektor 1.';
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES (".$rowx["user_id"].", 3,'$time','$msg')", $db);
            echo 'Kollektortransfer von '.$row['user_id'].' an '.$rowx['user_id'];
        }
    }
    //}

    //votetimer für den sektor um 1 verringern
    mysql_query("UPDATE de_sector SET votetimer=votetimer-1 WHERE votetimer>0", $db);
    mysql_query("UPDATE de_sector SET votecounter=votecounter-1 WHERE votecounter>0", $db);

    //archäologische events durchführen
    //include "wt_archeology.php";

    //manage map data
    include_once "wt_manage_map.php";

    //sektorgebauede bauen
    $res = mysql_query("select sec_id, techs, buildgnr, buildgtime from de_sector where buildgtime <= 1  AND buildgnr > 0", $db);
    $num = mysql_num_rows($res);
    echo "<br>$num Sektor-Gebäude-Datensätze gefunden<br>";
    mysql_query("UPDATE de_sector SET buildgtime = buildgtime - 1 WHERE buildgtime > 0", $db);
    for ($i = 0; $i < $num; $i++) {
        $uid   = mysql_result($res, $i, "sec_id");
        $techs = mysql_result($res, $i, "techs");
        $bnr   = mysql_result($res, $i, "buildgnr");
        $techs[$bnr - 119] = 1;
        $bnr = 0;

        mysql_query("update de_sector set techs = '$techs' where sec_id = $uid", $db);
        mysql_query("update de_sector set buildgnr = $bnr where sec_id = $uid", $db);
    }

    echo '<br>Gebäude fertig ';
    echo date("d/m/Y - H:i:s");

    //Sektor-Einheiten bauen
    $tech_id = 1;
    $res = mysql_query("SELECT sector_id, anzahl FROM de_sector_build WHERE verbzeit<=1", $db);
    $num = mysql_num_rows($res);

    echo "<br>$num Sektor-Einheiten-Datensätze gefunden<br>";
    for ($i = 0; $i < $num; $i++) {
        $uid      = mysql_result($res, $i, "sector_id");
        $anzahl   = mysql_result($res, $i, "anzahl");
        $sql = "update de_sector set e1 = e1 + $anzahl where sec_id = $uid";
        mysql_query($sql, $db);
        mysql_query("DELETE FROM de_sector_build where sector_id = $uid AND tech_id=$tech_id AND verbzeit=1", $db);
    }
    //datens�tze mit geringerer zeit aktualisieren
    mysql_query("update de_sector_build set verbzeit = verbzeit-1", $db);



    //Schiffs-Einheiten, Kollektoren, Agenten, Sonden bauen
    $res = mysql_query("SELECT user_id, tech_id, anzahl FROM de_user_build WHERE verbzeit<=1", $db);
    $num = mysql_num_rows($res);
    mysql_query("UPDATE de_user_build SET verbzeit = verbzeit-1", $db);
    echo "<br>$num Schiffs-Einheiten, Kollektoren, Agenten, Sonden-Datensätze gefunden<br>";
    for ($i = 0; $i < $num; $i++) {

        $uid      = mysql_result($res, $i, "user_id");
        $tech_id  = mysql_result($res, $i, "tech_id");
        $anzahl   = mysql_result($res, $i, "anzahl");
        //es ist ein kollektor
        if ($tech_id == 80) {
            mysql_query("UPDATE de_user_data SET col = col + $anzahl where user_id = $uid", $db);
        }

        //es ist ein raumschiff
        if ($tech_id >= 81 && $tech_id <= 99) {
            $fleet_id = $uid.'-0';
            $sql = "update de_user_fleet set e$tech_id = e$tech_id + $anzahl where user_id = '$fleet_id'";
            mysql_query($sql, $db);
        }

        //es ist eine verteidigungsanlage
        if ($tech_id >= 100 && $tech_id <= 109) {
            $sql = "update de_user_data set e$tech_id = e$tech_id + $anzahl where user_id = $uid";
            mysql_query($sql, $db);
        }

        //es ist eine spionagesonde
        if ($tech_id == 110) {
            mysql_query("update de_user_data set sonde = sonde + $anzahl where user_id = $uid", $db);
        }

        //es ist ein geheimagent
        if ($tech_id == 111) {
            mysql_query("update de_user_data set agent = agent + $anzahl where user_id = $uid", $db);
        }

        //artefaktgebäudeupgrade
        if ($tech_id == 1000) {
            //Rasse und Gebäudelevel auslesen
            $db_daten = mysql_query("SELECT rasse,artbldglevel FROM de_user_data WHERE user_id=$uid", $db);
            $row = mysql_fetch_array($db_daten);
            $artbldglevel = $row["artbldglevel"] + 1;
            $player_rasse = $row["rasse"];

            //punkte berechnen
            $score = $artbldglevel * 1000;

            //nachricht an den account schicken
            $db_daten = mysql_query("SELECT tech_name FROM de_tech_data WHERE tech_id=28", $db);
            $row = mysql_fetch_array($db_daten);
            $tech_name = getTechNameByRasse($row["tech_name"], $player_rasse);
            $msg = $wt_lang['gebaeudeausbau'].': '.$tech_name.'<br>'.$wt_lang['gebaeudelevel'].': '.$artbldglevel;

            //$time = strftime("%Y%m%d%H%M%S");
            $time = date("YmdHis");
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 2,'$time','$msg')", $db);

            //levelupgrade und fixpunkte in der db vermerken
            $sql = "UPDATE de_user_data SET fixscore = fixscore + '$score', newnews = '1', artbldglevel = artbldglevel + 1 WHERE user_id = $uid";
            mysql_query($sql, $db);
        }

        //itemdata
        //echo 'A: '.$tech_id;
        if ($tech_id >= 10000 && $tech_id < 20000) {
            //echo '<br>csa: '.$uid.'/'.($tech_id-10000).'/'.$anzahl;
            change_storage_amount($uid, $tech_id - 10000, $anzahl);
        }

        //archäologen
        //if ($tech_id==1001)mysql_query("UPDATE de_user_data SET archi = archi + $anzahl WHERE user_id = $uid",$db);

        //archäologieprojekte
        /*
        if ($tech_id>=2001 && $tech_id<=2050)
        {
            //wenn es erforscht wurde das projekt in de_user_quest einf�gen
            $pid=$tech_id-2000;
            mysql_query("INSERT INTO de_user_quest (user_id, pid) VALUES ($uid, $pid)",$db);
            //info an den account, dass ein datenpaket erforscht worden ist
            $time=strftime("%Y%m%d%H%M%S");
            $msg=$wt_lang[archaeologischeanalyse];
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 2,'$time','$msg')",$db);
            mysql_query("UPDATE de_user_data SET newnews = 1 WHERE user_id = $uid",$db);

        }
        */

        //datensatz l�schen, da abgearbeitet
        //mysql_query("DELETE FROM de_user_build where user_id = $uid AND tech_id=$tech_id AND verbzeit<1",$db);
    }
    //abgearbeitete bauaufträge löschen
    mysql_query("DELETE FROM de_user_build WHERE verbzeit<1", $db);

    //datensatz mit geringerer zeit aktualisieren
    echo '<br>Einheiten fertig ';
    echo date("d/m/Y - H:i:s");

    //tronicverteilung noch initiieren wenn es keinen datensatz mehr gibt, in den tronic verteilt werden muss
    $db_daten = mysql_query("SELECT tcount FROM de_user_data WHERE tcount>0", $db);
    $globtcount = mysql_num_rows($db_daten);
    //wenn $globtcount null dann schauen ob man die t-verteilung nochmal anstößt
    $tronicverteilen = 0;
    if ($globtcount == 0) {
        $r = mt_rand(1, 100);
        //wenn $globtcount auf 1 gesetzt wird, dann beginnt die tronicverteilung von vorne
        if ($r < $sv_globalw_tronic) {
            $tronicverteilen = 1;
        } else {
            $tronicverteilen = 0;
        }
        if ($tronicverteilen > 0) {
            echo 'TROOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOONIC';
        }
    }

    //Zufallsereignisverteilung noch initiieren wenn es keien datens�tze mehr gibt, in den Zufallsereignisse verteilt werden m�ssen
    $db_daten_zufall = mysql_query("SELECT zcount FROM de_user_data WHERE zcount>0", $db);
    $globzcount = mysql_num_rows($db_daten_zufall);

    //wenn $globzcount null dann schauen ob man die Zufalls-verteilung nochmal anstößt
    $zufallverteilen = 0;
    if ($globzcount == 0) {
        $r = mt_rand(1, 100);
        //wenn $globzcount auf 1 gesetzt wird, dann beginnt die zufallsverteilung von vorne
        if ($r < $sv_globalw_zufall) {
            $zufallverteilen = 1;
        } else {
            $zufallverteilen = 0;
        }
        if ($zufallverteilen > 0) {
            echo 'ZUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUFALL';
        }
    }

    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    //Rohstoffe + Punkte(Gebaeude, Forschungen)
    //für jede rasse einzeln durchlaufen
    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    //vorher alle bauauftr�ge bzgl. punkten auslesen
    unset($user_buildscore);
    //bauaufträge ohne recycling
    $result = mysql_query("SELECT user_id, SUM(score) AS score FROM `de_user_build` WHERE recycling=0 GROUP BY user_id", $db);
    while ($row = mysql_fetch_array($result)) {
        if (!isset($user_buildscore[$row['user_id']])) {
            $user_buildscore[$row['user_id']] = 0;
        }
        $user_buildscore[$row['user_id']] += round($row['score'] / 10);
    }

    //bauaufträge mit recycling
    $result = mysql_query("SELECT user_id, SUM(score) AS score FROM `de_user_build` WHERE recycling=1 GROUP BY user_id", $db);
    while ($row = mysql_fetch_array($result)) {
        $user_buildscore[$row['user_id']] += $row['score'];
    }


    //spezialisierungscache für planetaren grundertrag erzeugen
    for ($i = 0;$i <= 2000;$i++) {
        $spec3cache[$i] = -1;
    }

    //ADE-Rassenbonus
    /*
    $filename='../../div_server_data/ade_debonus/data.txt';
    $fp = fopen ($filename, 'r');
    $data=trim(fgets($fp, 1024));
    $adeprozente=explode(";", $data);
    fclose($fp);
    */

    ////////////////////////////////////////////////
    // die Spieler nach Rassen durchgehen
    ////////////////////////////////////////////////
    for ($rasse = 1; $rasse <= $sv_anz_rassen; $rasse++) {
        echo '<br>Rasse: '.$rasse;

        $res = mysql_query("SELECT de_user_data.user_id, de_user_data.col, de_user_data.sector, de_user_data.agent, de_user_data.agent_lost, de_user_data.techs, de_user_data.ekey, de_user_data.e100, de_user_data.e101, de_user_data.e102, de_user_data.e103, de_user_data.e104,  de_user_data.tcount, de_user_data.zcount, de_user_data.eartefakt, de_user_data.kartefakt, de_user_data.dartefakt, de_user_data.tick , de_user_data.palenium, de_user_data.archi, de_user_data.npc, de_user_data.useefta, de_user_data.sc1, de_user_data.vs_auto_explore FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.rasse=$rasse", $db);
        $num = mysql_num_rows($res);

        //tronic-meldungen einbinden, damit man weiß wieviele man verwenden kann
        $tronic = '';//verhindert Warnings beim include, wird hier aber nicht benötigt, weil nur die id und anzahl in der DB gespeichert wird
        include $directory."inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";

        echo "<br>$num Rohstoff/Punkte-Datensätze gefunden<br>";
        $time = date("YmdHis");

        while ($irow = mysql_fetch_array($res)) {
            $uid     = $irow["user_id"];
            //$techs   = $irow["techs"];
            $col     = $irow["col"];
            $sector  = $irow["sector"];
            $agenten = $irow["agent"];
            $agent_lost = $irow["agent_lost"];
            $ekey    = $irow["ekey"];
            $e100    = $irow["e100"];
            $e101    = $irow["e101"];
            $e102    = $irow["e102"];
            $e103    = $irow["e103"];
            $e104    = $irow["e104"];
            $tcount  = $irow["tcount"];
            $zcount  = $irow["zcount"];
            $eartefakt  = $irow["eartefakt"];
            $kartefakt  = $irow["kartefakt"];
            $dartefakt  = $irow["dartefakt"];
            $tick    = $irow["tick"];
            $palenium = $irow["palenium"];
            $archi   = $irow["archi"];
            $npc     = $irow["npc"];
            //$useefta = $irow["useefta"];
            $mysc1   = $irow["sc1"];
            $vs_auto_explore = $irow["vs_auto_explore"];

            $pt = loadPlayerTechs($uid);

            //ekey aufsplitten
            $hv = explode(";", $ekey);
            $keym = $hv[0];
            $keyd = $hv[1];
            $keyi = $hv[2];
            $keye = $hv[3];

            //wenn efta in benutzung ist noch den malus verrechnen
            //if($useefta==1)$malus=$sv_efta_col_malus;else $malus=0;
            $malus = 0;
            $sabotagemalus = 0;

            //sabotagemalus
            if ($maxtick < $mysc1 + $sv_sabotage[7][0] and $mysc1 > $sv_sabotage[7][0]) {
                $sabotagemalus += $sv_sabotage[7][2];
            }

            //überprüfen ob eine Erkundung läuft
            if ($vs_auto_explore == 1) {
                $ea = 0;

                $sql = "SELECT * FROM de_user_map WHERE user_id='".$uid."' AND known_since>'".time()."' LIMIT 1";
                $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
                $num = mysqli_num_rows($db_daten);
                if ($num > 0) {
                    //vom WT werden alle known_since des Users auf 1000 gesetzt, damit ist das System erforscht
                    mysqli_query($GLOBALS['dbi'], "UPDATE de_user_map SET known_since=1000 WHERE user_id='".$uid."';");
                }
            } else {
                $sql = "SELECT * FROM de_user_map WHERE user_id='".$uid."' AND known_since>'".time()."' LIMIT 1";
                $db_daten = mysqli_query($GLOBALS['dbi'], $sql);
                $num = mysqli_num_rows($db_daten);
                if ($num == 0) {
                    $ea = $col * ($sv_kollieertrag - $malus - $sabotagemalus);
                } else {
                    $ea = 0;
                    //vom WT werden alle known_since des Users auf 1000 gesetzt, damit ist das System erforscht
                    mysqli_query($GLOBALS['dbi'], "UPDATE de_user_map SET known_since=1000 WHERE user_id='".$uid."';");
                }
            }

            //eftaartefakt
            //$eartefaktenergie=floor($ea/10000*$eartefakt);
            $eartefaktenergie = $sv_eftaartefaktertrag * $eartefakt;

            //kriegsartefakt
            //$kartefaktenergie=floor($ea/1000*$kartefakt);
            $kartefaktenergie = $sv_kriegsartefaktertrag * $kartefakt;

            $dartefaktenergie = floor($ea / 100 * $dartefakt);
            $paleniumenergie = floor($ea / 10000 * $palenium);

            //ade-rassenbonus
            $adebonus = 0;

            $ea = $ea + $eartefaktenergie + $kartefaktenergie + $dartefaktenergie + $paleniumenergie + $adebonus;

            //energieinput pro rohstoff
            $em = floor($ea / 100 * $keym);
            $ed = floor($ea / 100 * $keyd);
            $ei = floor($ea / 100 * $keyi);
            $ee = floor($ea / 100 * $keye);

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

            //////////////////////////////////////////
            //////////////////////////////////////////
            //grundertrag
            //////////////////////////////////////////
            //////////////////////////////////////////
            //spezialisierung
            if ($spec3cache[$sector] == -1) {
                $db_datenspec = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND spec3=3;", $db);
                $planertragbonus = mysql_num_rows($db_datenspec) * 10;
                if ($planertragbonus > 100) {
                    $planertragbonus = 100;
                }
                $spec3cache[$sector] = $planertragbonus / 100;
            }

            //grundertragbonus für die BR, gibt es nie in der Ewigen Runde und nicht bei Hardcore
            if ((($maxtick > 2500000 && $sv_ewige_runde != 1 && $sv_hardcore != 1) or ($sv_comserver == 1 and $sv_comserver_roundtyp == 1)) and $sector > 1) {
                $grundertragmultiplikator = 200;
            } else {
                $grundertragmultiplikator = 1;
            }


            if (!hasTech($pt, 4)) {//keine gilde
                $grundm = $sv_plan_grundertrag[0] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grundd = $sv_plan_grundertrag[1] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grundi = $sv_plan_grundertrag[2] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grunde = $sv_plan_grundertrag[3] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
            } else {  //mit gilde
                $grundm = $sv_plan_grundertrag_whg[0] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grundd = $sv_plan_grundertrag_whg[1] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grundi = $sv_plan_grundertrag_whg[2] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
                $grunde = $sv_plan_grundertrag_whg[3] * $grundertragmultiplikator * (1 + $spec3cache[$sector]);
            }

            $grundm = $grundm + floor($agent_lost * $sv_zoellnerertrag[0]);
            $grundd = $grundd + floor($agent_lost * $sv_zoellnerertrag[1]);
            $grundi = $grundi + floor($agent_lost * $sv_zoellnerertrag[2]);
            $grunde = $grunde + floor($agent_lost * $sv_zoellnerertrag[3]);


            //Tronicator
            $tronicertrag = 0;
            if (hasTech($pt, 160) && $rundenalter_wt % 20 == 0) {
                //ein Tronic bekommt man immer
                $tronicertrag++;

                //pro Troniccelerator-Artefakt bekommt man zusätzlich ein Tronic
                $tronicertrag+=intval(getArtefactAmountByUserId($uid, 21));
            }

            //tronicverteilung
            $sqltcount = '';
            $sqltronic = '';
            $sqlnews = '';
            if ($tronicverteilen > 0) {
                //niemand hat mehr tronic in der queue, also neu bei jedem verteilen
                $r = mt_rand(2, 15);
                $sqltcount = ', tcount = '.$r;
            } else {
                //wenn der counter auf 1 ist, dann gibts tronic
                if ($tcount == 1 and $npc == 0) {
                    //festellen wieviel tronic man bekommt
                    $r = mt_rand(1, 100);
                    if ($r <= 1) {
                        $tronic = 5;
                    } elseif ($r <= 2) {
                        $tronic = 4;
                    } elseif ($r <= 6) {
                        $tronic = 3;
                    } elseif ($r <= 27) {
                        $tronic = 2;
                    } else {
                        $tronic = 1;
                    }

                    //nachricht an account schicken
                    $nanr = mt_rand(0, count($na) - 1);
                    //$nachricht=$na[$nanr];
                    $nachricht = $tronic.';'.$nanr;
                    if ($npc == 0) {
                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 8,'$time','$nachricht')", $db);
                    }
                    $sqlnews = ', newnews = 1';
                    $tronicertrag += $tronic;
                    //$sqltronic=', restyp05 = restyp05 +'.$tronic;
                } else {
                    $tronic = 0;
                }
            }

            if ($tronicertrag > 0) {
                $sqltronic = ', restyp05 = restyp05 +'.$tronicertrag;
            }

            //rohstoffe - ende


            //Zufallsereigbnisverteilung
            $sqlzcount = '';
            $sqlzufall = '';
            $sqlnewszufall = '';
            $sql_zufallskollies = '';
            $zufallsagenten = '';
            $zufallssonden = '';
            $zufallsturm = '';
            $sql_zufallsturm = '';
            $sql_zufallsagent = '';
            $sql_zufallssonden = '';

            if ($zufallverteilen > 0 && $tick > $sv_global_start_zufall) {
                //niemand hat mehr Zufälle in der Queue, also neu bei jedem verteilen
                $r = mt_rand(4, 240);
                $sqlzcount = ', zcount = '.$r;
            } else {
                //wenn der counter auf 1 ist, dann gibts etwas
                if ($zcount == 1 && $sector > 1 && $npc == 0) {
                    //festellen welches ereignis es gibt
                    $rz = mt_rand(0, 100);


                    if ($rz <= 7) {
                        $r = 5;
                    } elseif ($rz <= 15) {
                        $r = 2;
                    } elseif ($rz <= 24) {
                        $r = 4;
                    } elseif ($rz <= 35) {
                        $r = 0;
                    } elseif ($rz <= 49) {
                        $r = 3;
                    } elseif ($rz <= 64) {
                        $r = 1;
                    } else {
                        $r = 6;
                    }


                    switch ($r) {
                        case 0:
                            //Kollies
                            $zufallskollies = 0;
                            $zufallskollies = ceil(($tick) / 2000);
                            $sql_zufallskollies = ',col=col + '.$zufallskollies;
                            $zufallsmsg = mt_rand(0, count($kolliemsg) - 1);
                            
                            $nachricht = str_replace("{VALUE1}", $zufallskollies, $kolliemsg[$zufallsmsg]);

                        break;

                        case 1:
                            //schiffe
                            $fleet_zufall_id = $uid.'-0';
                            $schiffsart = mt_rand(0, 6);
                            switch ($schiffsart) {
                                case 0:
                                    //nisse
                                    $anzahl = ceil(($tick / 4000) * 20);
                                    $sql_zufall_schiff = "update de_user_fleet set e81 = e81 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 1:
                                    //jabo
                                    $anzahl = ceil(($tick / 4000) * 10);
                                    $sql_zufall_schiff = "update de_user_fleet set e82 = e82 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 2:
                                    //zerri
                                    $anzahl = ceil(($tick / 4000) * 5);
                                    $sql_zufall_schiff = "update de_user_fleet set e83 = e83 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 3:
                                    //kreuzer
                                    $anzahl = ceil(($tick / 4000) * 3);
                                    $sql_zufall_schiff = "update de_user_fleet set e84 = e84 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 4:
                                    //schlachter
                                    $anzahl = ceil($tick / 4000);
                                    $sql_zufall_schiff = "update de_user_fleet set e85 = e85 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 5:
                                    //tr�ger
                                    $anzahl = ceil($tick / 4000);
                                    $sql_zufall_schiff = "update de_user_fleet set e88 = e88 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;

                                case 6:
                                    //transe
                                    $anzahl = ceil(($tick / 4000) * 7);
                                    $sql_zufall_schiff = "update de_user_fleet set e87 = e87 + $anzahl where user_id = '$fleet_zufall_id'";
                                    break;
                            }


                            mysql_query($sql_zufall_schiff, $db);
                            $zufallsmsg = mt_rand(0, count($schiffmsg) - 1);


                            $zufallsrasse = $rasse;
                            if ($anzahl != "1") {
                                $zufallsrasse = $rasse + 4;
                            }

                            $msg = str_replace('{VALUE1}', $anzahl, $schiffmsg[$zufallsmsg]);
                            $nachricht = str_replace('{VALUE2}', $schiffsname_wt[$zufallsrasse][$schiffsart], $msg);
                        break;

                        case 2:
                            //defense

                            $turmart = mt_rand(0, 4);
                            switch ($turmart) {
                                case 0:
                                    //Turm1
                                    $zufallsturm = ceil(($tick / 1000) * 2);
                                    $sql_zufallsturm = ',e100=e100 + '.$zufallsturm;
                                    break;

                                case 1:
                                    //Turm2
                                    $zufallsturm = ceil(($tick / 1000) * 15);
                                    $sql_zufallsturm = ',e101=e101 + '.$zufallsturm;
                                    break;

                                case 2:
                                    //Turm3
                                    $zufallsturm = ceil(($tick / 1000) * 24);
                                    $sql_zufallsturm = ',e102=e102 + '.$zufallsturm;
                                    break;

                                case 3:
                                    //Turm4
                                    $zufallsturm = ceil(($tick / 1000) * 9);
                                    $sql_zufallsturm = ',e103=e103 + '.$zufallsturm;
                                    break;

                                case 4:
                                    //Turm4
                                    $zufallsturm = ceil(($tick / 1000) * 6);
                                    $sql_zufallsturm = ',e104=e104 + '.$zufallsturm;
                                    break;
                            }

                            $zufallsrasse = $rasse;
                            if ($zufallsturm != "1") {
                                $zufallsrasse = $rasse + 4;
                            }

                            $zufallsmsg = mt_rand(0, count($deffmsg) - 1);

                            $msg = str_replace('{VALUE1}', $zufallsturm, $deffmsg[$zufallsmsg]);
                            $nachricht = str_replace('{VALUE2}', $turm[$zufallsrasse][$turmart], $msg);
                            break;

                        case 3:
                            //Erfahrung

                            $commander = mt_rand(1, 3);
                            $off_oder_deff = mt_rand(0, 1);
                            $fleet_zufall_id = $uid.'-'.$commander;

                            $zufallerfahrung = (ceil($tick)); // noch mit Tino besprechen

                            if ($off_oder_deff == "1") {
                                $sql_zufall_exp = "update de_user_fleet set komatt  = komatt  + $zufallerfahrung where user_id = '$fleet_zufall_id'";
                            } else {
                                $sql_zufall_exp = "update de_user_fleet set komdef  = komdef  + $zufallerfahrung where user_id = '$fleet_zufall_id'";
                            }
                            mysql_query($sql_zufall_exp, $db);
                            
                            $zufallsmsg = mt_rand(0, count($erfahrungsmsg) - 1);
                            $nachricht = str_replace("{VALUE1}", $zufallerfahrung, $erfahrungsmsg[$zufallsmsg]);

                        break;

                        case 4:
                            //Agenten
                            $zufallsagenten = (ceil(($tick) / 1000)) * mt_rand(4, 35);
                            $sql_zufallsagent = ',agent=agent + '.$zufallsagenten;
                            $zufallsmsg = mt_rand(0, count($agentenmsg) - 1);
                            $nachricht = str_replace("{VALUE1}", $zufallsagenten, $agentenmsg[$zufallsmsg]);
                        break;

                        case 5:
                            //Sonden
                            $zufallssonden = (ceil(($tick) / 2000)) * 2;
                            $sql_zufallssonden = ',sonde=sonde + '.$zufallssonden;
                            $zufallsmsg = mt_rand(0, count($sondenmsg) - 1);
                            $nachricht = str_replace("{VALUE1}", $zufallssonden, $sondenmsg[$zufallsmsg]);
                        break;

                        case 6:
                            //Ress
                            $ressart = mt_rand(0, 3);
                            switch ($ressart) {
                                case 0:
                                    //Multiplex
                                    $anzahl = $tick * 12;
                                    $rm = $rm + ($tick * 12);
                                    break;

                                case 1:
                                    //Dyharra
                                    $anzahl = $tick * 6;
                                    $rd = $rd + ($tick * 6);
                                    break;

                                case 2:
                                    //Iradium
                                    $anzahl = $tick * 5;
                                    $ri = $ri + ($tick * 5);
                                    break;

                                case 3:
                                    //Eternium
                                    $anzahl = $tick * 3;
                                    $re = $re + ($tick * 3);
                                    break;
                            }

                            $zufallsmsg = mt_rand(0, count($ressmsg) - 1);
                            $nachricht = str_replace("{VALUE1}", $anzahl, $ressmsg[$zufallsmsg]);
                            break;
                    }

                    if ($npc == 0) {
                        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 3,'$time','$nachricht')", $db);
                    }

                    $sqlnews = ', newnews = 1';
                }
            }



            //punkte - anfang
            $punkte = 0;

            //technologiepunkte z�hlen
            //punkte fuer schiffe zaehlen
            $str = '';
            for ($z = 81;$z <= 90;$z++) {
                $ec[$z] = 0; //zuerst mal alles auf null setzen
            }

            //zaehle alle schiffe, die schon vorhanden sind - anfang
            $fid0 = $uid.'-0';
            $fid1 = $uid.'-1';
            $fid2 = $uid.'-2';
            $fid3 = $uid.'-3';
            $db_daten = mysql_query("SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC", $db);
            while ($row = mysql_fetch_array($db_daten)) {
                for ($z = 81;$z <= 90;$z++) {
                    $ec[$z] += $row['e'.$z];
                }
            }


            //Punkte der Schiffe berechnen
            for ($p = 0;$p < $sv_anz_schiffe;$p++) {
                $punkte = $punkte + $ec[$p + 81] * $unit[$rasse - 1][$p][4];
            }
            //zaehle alle schiffe, die schon vorhanden sind - ende


            //punkte fuer verteidigungseinheiten zaehlen - anfang

            $ec[100] = $e100;
            $ec[101] = $e101;
            $ec[102] = $e102;
            $ec[103] = $e103;
            $ec[104] = $e104;

            for ($p = 0;$p < $sv_anz_tuerme;$p++) {
                $punkte = $punkte + $ec[$p + 100] * $unit[$rasse - 1][$p + $sv_anz_schiffe][4];
            }
            //punkte fuer verteidigungseinheiten zaehlen - ende

            //einheitenpunkte speichern
            $fleetscore = $punkte;

            //punkte f�r kollies festlegen
            if ($col < 100) {
                $colscore = 10000;
            } elseif ($col < 150) {
                $colscore = 10500;
            } elseif ($col < 200) {
                $colscore = 11000;
            } elseif ($col < 250) {
                $colscore = 11500;
            } elseif ($col < 300) {
                $colscore = 12000;
            } elseif ($col < 350) {
                $colscore = 12500;
            } elseif ($col < 400) {
                $colscore = 13000;
            } elseif ($col < 450) {
                $colscore = 13500;
            } elseif ($col < 500) {
                $colscore = 14000;
            } elseif ($col < 550) {
                $colscore = 14500;
            } elseif ($col < 600) {
                $colscore = 15000;
            } elseif ($col < 650) {
                $colscore = 15500;
            } elseif ($col < 700) {
                $colscore = 16000;
            } elseif ($col < 750) {
                $colscore = 16500;
            } elseif ($col < 800) {
                $colscore = 17000;
            } elseif ($col < 850) {
                $colscore = 17500;
            } elseif ($col < 900) {
                $colscore = 18000;
            } elseif ($col < 950) {
                $colscore = 18500;
            } elseif ($col < 1000) {
                $colscore = 19000;
            } else {
                $colscore = 20000;
            }

            //punke f�r agenten
            //if ($agenten>50000) $agenten=50000;
            $agentenpunkte = $agenten * 250;

            //punkte f�r arch�ologen
            if ($archi > 50000) {
                $archi = 50000;
            }
            $archipunkte = $archi * 250;

            //punkte für bauaufträge dazuaddieren
            if (isset($user_buildscore[$uid])) {
                $punkte += $user_buildscore[$uid];
            }

            //punkte - ende (bis auf punkte fuer ressourcen)

            //daten in die db schreiben
            if ($sv_ang == 1) {
                $sql = "UPDATE de_user_data SET restyp01 = restyp01 + $rm + $grundm, restyp02 = restyp02 + $rd + $grundd,
			  restyp03 = restyp03 + $ri + $grundi, restyp04 = restyp04 + $re + $grunde".$sqltronic.
                  ", fleetscore='$fleetscore', 
			  score = '$punkte'+ col * '$colscore' ".
                  $sqlnews.$sqltcount.$sqlzcount.$sql_zufallskollies.$sql_zufallsturm.$sql_zufallsagent.$sql_zufallssonden.
                  " WHERE user_id=$uid";
            } else {
                $sql = "UPDATE de_user_data SET restyp01 = restyp01 + $rm + $grundm, restyp02 = restyp02 + $rd + $grundd,
			  restyp03 = restyp03 + $ri + $grundi, restyp04 = restyp04 + $re + $grunde".$sqltronic.
                  ", fleetscore='$fleetscore', 
			  score = fixscore + $punkte + (restyp01+restyp02*2+restyp03*3+restyp04*4+restyp05*10000)/100 + col * $colscore + sonde * 0 + '$agentenpunkte' + '$archipunkte'".
                  $sqlnews.$sqltcount.$sqlzcount.$sql_zufallskollies.$sql_zufallsturm.$sql_zufallsagent.$sql_zufallssonden.
                  " WHERE user_id=$uid";
            }
            mysql_query($sql, $db);
            //palenium um eins verringern
            mysql_query("UPDATE de_user_data SET palenium = palenium - 1 WHERE palenium>0 AND user_id=$uid", $db);
        }
    }//ende for rassen...

    //troniccounter, zufallscounter und palenium um eins verringern
    if ($globtcount > 0) {
        mysql_query("UPDATE de_user_data SET tcount = tcount - 1 WHERE tcount>0", $db);
    }
    if ($globzcount > 0) {
        mysql_query("UPDATE de_user_data SET zcount = zcount - 1 WHERE zcount>0", $db);
    }

    echo '<br>Rohstoffe und Punkte fertig ';

    //sektorkollektoren
    $db_daten = mysql_query("SELECT * FROM de_sector WHERE tempcol>0 AND npc=0", $db);
    while ($row = mysql_fetch_array($db_daten)) {
        $sec_id = $row["sec_id"];
        $col = $row["col"];
        $ekey = $row["ekey"];
        $techs = $row['techs'];

        //auf spezialisierung �berpr�fen
        $db_datenspec = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sec_id' AND spec5=3;", $db);
        $specseccol = mysql_num_rows($db_datenspec) * 10;
        if ($specseccol > 100) {
            $specseccol = 100;
        }

        $col = $col + $specseccol;

        //ekey aufsplitten
        $hv = explode(";", $ekey);
        $keym = $hv[0];
        $keyd = $hv[1];
        $keyi = $hv[2];
        $keye = $hv[3];


        //gesamtenergie pro tick, energieausbeute
        $ea = $col * 100;

        //energieinput pro rohstoff
        $em = ceil($ea / 100 * $keym);
        $ed = ceil($ea / 100 * $keyd);
        $ei = ceil($ea / 100 * $keyi);
        $ee = ceil($ea / 100 * $keye);


        if ($techs[1] == 0) {
            $emvm = 2;
            $emvd = 4;
            $emvi = 6;
            $emve = 8;
        } else {
            $emvm = 1;
            $emvd = 2;
            $emvi = 3;
            $emve = 4;
        }

        //rohstoffoutput
        $rm = ceil($em / $emvm);
        $rd = ceil($ed / $emvd);
        $ri = ceil($ei / $emvi);
        $re = ceil($ee / $emve);

        //db updaten
        mysql_query("UPDATE de_sector SET restyp01 = restyp01 + '$rm', restyp02 = restyp02 + '$rd', restyp03 = restyp03 + '$ri', restyp04 = restyp04 + '$re'  
	 WHERE sec_id='$sec_id'", $db);
    }

    //schauen ob ein geworbener soweit ist, dass der werber nen bonus erh�lt
    /*
    $db_daten=mysql_query("SELECT user_id, tick, werberid FROM de_user_data WHERE tick >= 1000 AND score >= 1000000 AND werberid > 0",$db);

    while ($row = mysql_fetch_array($db_daten))
    {
      $uid=$row["user_id"];
      $werberid=$row["werberid"];
      //beim geworbenen die werberid entfernen
      mysql_query("UPDATE de_user_data SET werberid = 0 WHERE user_id='$uid'",$db);
      //beim werber ein diplomatieartefakt hinzuf�gen, bzw. credits gutschreiben
      $db_datenx=mysql_query("SELECT tick, dartefakt FROM de_user_data WHERE user_id='$werberid'",$db);
      $rowx = mysql_fetch_array($db_datenx);
      if($rowx["dartefakt"]<$sv_max_dartefakt)
      {
        //diplomatieartefakt gutschreiben
        mysql_query("UPDATE de_user_data SET dartefakt = dartefakt + 1, geworben=geworben+1 WHERE user_id='$werberid'",$db);
        //nachricht an den account schicken
        $time=strftime("%Y%m%d%H%M%S");
        $text='F&uuml;r das Werben eines Spielers erhalten Sie ein Diplomatieartefakt.';
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($werberid, 60,'$time','$text')",$db);
        mysql_query("update de_user_data set newnews = 1 where user_id = $werberid",$db);

      }
      else
      {
        //credits, aber nur dann, wenn der account des geworbenen 1000 ticks j�nger ist als der des werbers
        if(($rowx["tick"]-$row["tick"])>=1000)
        {
          mysql_query("UPDATE de_user_data SET credits = credits + 10, geworben=geworben+1 WHERE user_id='$werberid'",$db);
          //nachricht an den account schicken
          $time=strftime("%Y%m%d%H%M%S");
          $text='F&uuml;r das Werben eines Spielers erhalten Sie 10 Credits.';
          mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($werberid, 60,'$time','$text')",$db);
          mysql_query("update de_user_data set newnews = 1 where user_id = $werberid",$db);
        }
      }
    }
    */

    echo date("d/m/Y - H:i:s");

    //questsystem einbinden
    echo '<hr>Serverquests:<br>';
    include_once "wt_quest.php";
    echo '<br><hr>';

    //sektorartefaktaktionen durchführen
    if (!isset($sv_deactivate_sectorartefacts)) {
        $sv_deactivate_sectorartefacts = 0;
    }
    if ($sv_deactivate_sectorartefacts != 1) {
        include_once "wt_artefakte.php";
    }

    //spielerartefaktaktionen durchf�hren
    include_once "wt_userartefacts.php";

    //Tick hochzählen
    mysql_query("UPDATE de_user_data SET tick = tick + 1", $db);
    mysql_query("UPDATE de_system SET wt=wt+1;", $db);


    //alte nachrichten l�schen
    $tis = time() - (86400 * $sv_nachrichten_deldays);
    $datum = date("YmdHis", $tis);
    mysql_query("DELETE FROM de_user_news where time < $datum", $db);

    //alte hyperfunk l�schen
    $tis = time() - (86400 * $sv_hf_deldays);
    $datum = date("YmdHis", $tis);
    mysql_query("DELETE FROM de_user_hyper where time < $datum and archiv=0", $db);

    //inaktive accounts 24 stunden vor l�schung per mail benachrichtigen
    //emaitext
    $betreff = $wt_lang['inaktivenmailbetreff'].$sv_server_tag.' - '.$sv_server_name;
    $emailtext = $wt_lang['inaktivenmailbody'];

    $tis = time() - (86400 * ($sv_inactiv_deldays - 1));
    $datum = date("Y-m-d H:i:s", $tis);
    $db_daten = mysql_query("SELECT de_login.user_id, de_login.reg_mail FROM de_login, de_user_data WHERE de_login.last_login < '$datum' AND de_login.last_ip<>'127.0.0.1' AND de_user_data.user_id=de_login.user_id AND inaktmail = 0 AND de_login.status=1", $db);
    while ($row = mysql_fetch_array($db_daten)) {
        $uid = $row["user_id"];
        $reg_mail = $row["reg_mail"];

        @mail_smtp($reg_mail, $betreff, $emailtext);
        //damit er nur einmal die mail bekommt inaktmail auf 1 setzen
        mysql_query("UPDATE de_login SET inaktmail = 1 WHERE user_id = '$uid'", $db);
    }

    //inaktive accounts l�schen
    $result = mysql_query("SELECT dodelinactiv FROM de_system", $db);
    $row = mysql_fetch_array($result);
    $dodel = $row["dodelinactiv"];

    if ($dodel == 1) {
        //�berpr�fen ob es account im l�sch-umode gibt und diese mit kicken
        $tis = time();
        $datum = date("Y-m-d H:i:s", $tis);
        $time = strftime("%Y%m%d%H%M%S");
        //$db_daten=mysql_query("UPDATE de_login SET status=2, last_login='0000-00-00 00:00:00' WHERE last_login < '$datum' AND status=3 AND delmode=1",$db);
        $db_daten = mysql_query("UPDATE de_login SET last_login='0000-00-00 00:00:00' WHERE last_login < '$datum' AND status=3 AND delmode=1", $db);


        //inaktive account suchen
        $tis = time() - (86400 * $sv_inactiv_deldays);
        $datum = date("Y-m-d H:i:s", $tis);
        $time = strftime("%Y%m%d%H%M%S");

        $db_daten = mysql_query("SELECT de_login.user_id, de_login.nic, de_login.last_login, de_login.status, de_login.delmode, de_user_data.spielername, de_user_data.col, de_user_data.sector, de_user_data.`system`, de_user_data.sou_user_id, de_user_data.efta_user_id FROM de_login, de_user_data WHERE de_login.last_login < '$datum' AND de_user_data.npc < 1 AND de_user_data.user_id=de_login.user_id", $db);
        while ($row = mysql_fetch_array($db_daten)) {
            $uid = $row["user_id"];
            $sector = $row["sector"];
            $system = $row["system"];
            $sou_user_id = $row["sou_user_id"];
            $efta_user_id = $row["efta_user_id"];
            $delmode = $row["delmode"];
            $status = $row["status"];
            $spielername = $row["spielername"];
            $col = $row["col"];

            //votetimer/votecounter f�r den sektor setzen
            mt_srand((float)microtime() * 10000);
            $votetimer = mt_rand(16, 96);
            mysql_query("UPDATE de_sector SET votetimer='$votetimer' WHERE sec_id='$sector'", $db);

            //account l�schen, oder in den umode schicken
            if ($sector > 0 and $delmode == 0 and $status == 1) {//spieler kommt in den umode und wird nach 0:0 verschoben um danach wieder in Sektor 1 zu landen
                $urltage = 4000;
                $tis = time() + 86400 * $urltage;
                $datum = date("Y-m-d H:i:s", $tis);
                mysql_query("UPDATE de_login SET last_login='$datum', status=3, delmode=2 WHERE user_id=$uid", $db);

                //den account danach in sektor 1 stecken
                mysql_query("UPDATE de_user_data SET sector=0, system=0, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, votefor=0, secstatdisable=0 WHERE user_id = '$uid'", $db);

                //dem sektor die kollektoren gutschreiben
                if ($col > 75) {
                    $col = 75;
                }
                if ($col > 0 and $sector > 1) {
                    mysql_query("UPDATE de_sector SET col=col+'$col' WHERE sec_id='$sector'", $db);
                    //info in die sektorhistorie packen - sektorkollektoren
                    mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '4', '$sector', '$col');", $db);
                }

                //info in die sektorhistorie packen - komplette spielerl�schung
                mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '3', '$sector', '$spielername');", $db);

                //wenn er BK war, den Posten zur�cksetzen
                mysql_query("UPDATE de_sector SET bk=0 WHERE sec_id='$sector' AND bk='$system'", $db);
            } else { //spieler löschen
                //mail an den spieler
                $betreff = $wt_lang['loeschmailbetreff'].$sv_server_tag.' - '.$sv_server_name;
                $emailtext = $wt_lang['loeschmailbody'];
                @mail_smtp($reg_mail, $betreff, $emailtext);

                //sollten noch flotten zum account unterwegs sein diese zur�ckschicken
                $result = mysql_query("SELECT user_id FROM de_user_fleet WHERE zielsec='$sector' AND zielsys='$system' AND zielsec > 0", $db);
                while ($rowx = mysql_fetch_array($result)) {
                    $fleet_id = $rowx["user_id"];
                    $hv = explode("-", $fleet_id);
                    $ruid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
                    $flottennummer = $hv[1];

                    mysql_query("UPDATE de_user_fleet set zielsec=hsec, zielsys=hsys, aktion=3, zeit = gesrzeit-zeit+1, aktzeit=0, entdeckt=0 WHERE
				user_id='$fleet_id'", $db);
                    //nachricht an den account schicken

                    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($ruid, 3,'$time','Im Zielsystem kam es zu einer kosmischen Katastrophe und Flotte Nr. $flottennummer kehrte um.')", $db);
                    mysql_query("update de_user_data set newnews = 1 where user_id = $ruid", $db);
                }

                $result_user = mysql_query("Select allytag FROM de_user_data WHERE user_id='$uid'", $db);
                // Ermitteln des Clantags des Users
                $clantag = @mysql_result($result_user, 0, "allytag");
                //Laden des Allianzdatensatzes
                $result = mysql_query("SELECT * FROM de_allys WHERE allytag='$clantag'", $db);
                // Ermitteln der ID�s des Leaders und der Coleader
                $leaderid = @mysql_result($result, 0, "leaderid");
                $coleaderid1 = @mysql_result($result, 0, "coleaderid1");
                $coleaderid2 = @mysql_result($result, 0, "coleaderid2");
                // Pr�fen, ob der Allianzleader seinen Account l�schen will
                if ($uid == $leaderid) {
                    //Pr�fen, ob im Feld coleaderid1 ein g�ltiger User eingetragen ist
                    if ($coleaderid1 > -1) {
                        //Coleader1 zum Leader machen
                        $result_updateally = mysql_query("UPDATE de_allys SET leaderid=$coleaderid1, coleaderid1=-1 WHERE allytag='$clantag'", $db);
                    }
                    //Wenn in coleaderid1 kein g�ltiger User eingetragen ist pr�fen, ob im Feld coleaderid2 ein g�ltiger User eingetragen ist
                    elseif ($coleaderid2 > -1) {
                        //Coleader2 zum Leader machen
                        $result_updateally = mysql_query("UPDATE de_allys SET leaderid=$coleaderid2, coleaderid2=-1 WHERE allytag='$clantag'", $db);
                    }
                    //Falls kein Coleader im Allianzdatensatz eingetragen ist
                    else {
                        //Ermitteln des ersten Users der Allianz in der Datenbank
                        $result_userlist = mysql_query("Select user_id FROM de_user_data WHERE (allytag='$clantag' AND user_id!='$uid') LIMIT 1", $db);
                        //Pr�fen, ob ein g�ltiges Resultset erzeugt werden konnte
                        if ($result_userlist) {
                            //ID des zur�ckgelieferten Userdatensatzes ermitteln
                            $newleaderid = @mysql_result($result_userlist, 0, "user_id");
                            //User zum Allianzleader machen
                            $result_updateally = mysql_query("UPDATE de_allys SET leaderid=$newleaderid WHERE allytag='$clantag'", $db);
                        }
                    }
                }
                //Pr�fen, ob Coleader1 seinen Account l�schen will
                elseif ($uid == $coleaderid1) {
                    //�nderung im Allianzdatensatz eintragen
                    $result_updateally = mysql_query("UPDATE de_allys SET coleaderid1=-1 WHERE allytag='$clantag'", $db);
                }
                //Pr�fen, ob Coleader2 seinen Account l�schen will
                elseif ($uid == $coleaderid2) {
                    //�nderung im Allianzdatensatz eintragen
                    $result_updateally = mysql_query("UPDATE de_allys SET coleaderid2=-1 WHERE allytag='$clantag'", $db);
                }


                mysql_query("DELETE FROM de_login WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_user_data WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_user_info WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_hfn_buddy_ignore WHERE user_id=$uid or (sector=$sector and `system`=$system)", $db);
                mysql_query("DELETE FROM de_user_hyper WHERE empfaenger = $uid", $db);

                $fleet_id = $uid.'-0';
                mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
                $fleet_id = $uid.'-1';
                mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
                $fleet_id = $uid.'-2';
                mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
                $fleet_id = $uid.'-3';
                mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);

                mysql_query("DELETE FROM de_user_build WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_user_comserver WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_user_news WHERE user_id=$uid", $db);
                mysql_query("DELETE FROM de_user_scan WHERE user_id=$uid", $db);

                //falls er SK war werden die votes entfernt
                mysql_query("UPDATE de_user_data SET votefor=0 WHERE sector='$sector' AND votefor='$system'", $db);
                //buddy liste anpassen
                mysql_query("DELETE FROM de_hfn_buddy_ignore WHERE user_id='$uid' or (sector='$sector' and `system`='$system')", $db);
                //statistik leeren
                mysql_query("DELETE FROM de_user_stat WHERE user_id='$uid'", $db);
                //cyborg entfernen
                /*
                mysql_query("DELETE FROM de_cyborg_data WHERE user_id='$efta_user_id'", $eftadb);
                mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$efta_user_id'", $eftadb);
                mysql_query("DELETE FROM de_cyborg_flags WHERE user_id='$efta_user_id'", $eftadb);
                mysql_query("DELETE FROM de_cyborg_ht WHERE user_id='$efta_user_id'", $eftadb);
                mysql_query("DELETE FROM de_cyborg_item WHERE user_id='$efta_user_id'", $eftadb);
                mysql_query("DELETE FROM de_cyborg_quest WHERE user_id='$efta_user_id'", $eftadb);
                */

                //dem sektor die kollektoren gutschreiben
                if ($col > 75) {
                    $col = 75;
                }
                if ($col > 0 and $sector > 1) {
                    mysql_query("UPDATE de_sector SET col=col+'$col' WHERE sec_id='$sector'", $db);
                    //info in die sektorhistorie packen - sektorkollektoren
                    mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '4', '$sector', '$col');", $db);
                }

                //info in die sektorhistorie packen - der spieler verl��t den sektor
                mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '3', '$sector', '$spielername');", $db);

                //sou-daten l�schen
                if ($sou_user_id > 0) {
                    mysql_select_db($sv_database_sou, $db);

                    mysql_query("DELETE FROM sou_ship_module WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_buffs WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_data WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_enm WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_hyper WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_politics WHERE user_id='$sou_user_id' OR wahlstimme='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_skill WHERE user_id='$sou_user_id' OR wahlstimme='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_systemhold WHERE user_id='$sou_user_id'", $db);
                    mysql_query("DELETE FROM sou_user_tech_updates WHERE user_id='$sou_user_id'", $db);

                    mysql_select_db($sv_database_de, $db);
                }
            }
        }

        //nicht aktivierte account nach 2 tagen l�schen
        $tis = time() - (86400 * $sv_not_activated_deldays);
        $datum = date("Y-m-d H:i:s", $tis);
        $db_daten = mysql_query("SELECT user_id, nic, last_login FROM de_login WHERE status=0 AND last_login < '$datum'", $db);

        while ($row = mysql_fetch_array($db_daten)) {
            $uid = $row["user_id"];
            mysql_query("DELETE FROM de_login WHERE user_id=$uid", $db);
            mysql_query("DELETE FROM de_user_data WHERE user_id=$uid", $db);
            mysql_query("DELETE FROM de_user_info WHERE user_id=$uid", $db);

            $fleet_id = $uid.'-0';
            mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
            $fleet_id = $uid.'-1';
            mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
            $fleet_id = $uid.'-2';
            mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);
            $fleet_id = $uid.'-3';
            mysql_query("DELETE FROM de_user_fleet WHERE user_id='$fleet_id'", $db);

            mysql_query("DELETE FROM de_user_build WHERE user_id=$uid", $db);
            //mysql_query("DELETE FROM de_user_hf WHERE user_id=$uid", $db);
            mysql_query("DELETE FROM de_user_news WHERE user_id=$uid", $db);
        }
    }
    //--------------- pl�tze und r�nge vorbelegen begin --------------------------
    echo '<br><br>Plätze und Ränge bestimmen - A: '.date("d/m/Y - H:i:s");

    $db_daten = mysql_query("SELECT count(user_id) FROM de_user_data WHERE npc=0 AND sector > 1", $db);
    $gesamtuser = mysql_result($db_daten, 0, 0);
    if ($gesamtuser == 0) {
        $gesamtuser = 1;
    }

    $result = mysql_query("SELECT de_user_data.user_id, de_user_data.score, de_user_data.sector FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY score DESC", $db);
    $rang_schritt = $gesamtuser * 0.042;
    $platz = 1;

    while ($row = mysql_fetch_array($result)) {
        $uid = $row["user_id"];
        $rang_nr = 1;
        $rang_zaehler = $rang_schritt;
        while ($platz > $rang_zaehler) {
            $rang_nr++;
            $rang_zaehler = $rang_zaehler + $rang_schritt;
        }

        if ($rang_nr > 24) {
            $rang_nr = 24;
        }

        if ($platz == 1) {
            $rang_nr = 1;
        }

        if ($row['sector'] == 1) {
            mysql_query("UPDATE de_user_data SET platz='$platz', rang='24' WHERE user_id='$uid'", $db);
        } else {
            mysql_query("UPDATE de_user_data SET platz='$platz', rang='$rang_nr' WHERE user_id='$uid'", $db);
        }

        $platz++;
    }

    //npcs generell auf einen fixen wert setzen
    mysql_query("UPDATE de_user_data SET platz='9999', rang='24' WHERE npc=1", $db);

    echo '<br>Plätze und Ränge bestimmen - E: '.date("d/m/Y - H:i:s").'<br>';
    //--------------- pl�tze und r�nge vorbelegen end --------------------------

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // die erhabenenpunkte berechnen
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    $res = mysql_query("SELECT de_user_data.user_id, de_user_data.tick, de_user_data.sector, de_user_data.agent_lost, de_user_data.col, de_user_data.fleetscore, de_user_data.roundpoints FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0", $db);

    echo "<br>$num Erhabenenpunkte berechnen<br>";
    $time = date("YmdHis");

    while ($irow = mysql_fetch_array($res)) {
        $uid = $irow["user_id"];
        $fleetscore = $irow["fleetscore"];
        $col = $irow["col"];
        $roundpoints = $irow["roundpoints"];
        $tick = $irow["tick"];
        $sector = $irow["sector"];
        $agent_lost = $irow["agent_lost"];
        //errungenschaften auslesen
        $db_daten = mysql_query("SELECT (ac1+ac2+ac3+ac4+ac5+ac6+ac7+ac8+ac9+ac10+ac11+ac12+ac13+ac14+ac15+ac16+ac17+ac18+ac19) AS wert FROM de_user_achievement WHERE user_id='$uid'", $db);
        $num = mysql_num_rows($db_daten);
        if ($num == 1) {
            $row = mysql_fetch_array($db_daten);
            $achievements = $row["wert"];
        } else {
            $achievements = 0;
        }

        //die flottenerfahrung auslesen
        $db_daten = mysql_query("SELECT SUM(komatt) AS komatt, SUM(komdef) AS komdef FROM de_user_fleet WHERE user_id='$uid-0' OR user_id='$uid-1' OR user_id='$uid-2' OR user_id='$uid-3'", $db);
        $row = mysql_fetch_array($db_daten);

        //formel: punkte=erfahrung*100(entspricht dann dem punktewert)/250000(der teiler f�r die punkte die man hat)/5(der teiler f�r die gewichtung)
        $komatt = $row['komatt'] * 100 / 250000 / 5;
        $komdef = $row['komdef'] * 100 / 250000 / 4;

        //wert berechnen
        if (isset($sv_ewige_runde) && $sv_ewige_runde == 1) {
            //echo '<br>'.$uid.':'.$sector;
            $ehscore = floor($col * 0.75 + $fleetscore / 250000 + $achievements + $komatt + $komdef + ($agent_lost / 10000));

        } else {
            $ehscore = floor($col + $fleetscore / 250000 + $achievements + $roundpoints + $komatt + $komdef);
        }
        mysql_query("UPDATE de_user_data SET ehscore='$ehscore' WHERE user_id='$uid'", $db);
    }

    //in der ewigen Runde haben alle Spieler in Sektor 1 keine EH-Punkte
    if ($sv_ewige_runde == 1) {
        mysql_query("UPDATE de_user_data SET ehscore=0 WHERE sector=1;", $db);
    }


    //--------------- erhabenentest begin --------------------------
    $erhabenenstop = 0;
    ///////////////////////////////////////////////////////
    //Ewige Runde, oder nicht?
    ///////////////////////////////////////////////////////
    if ($sv_ewige_runde == 1) {
        if ($maxtick >= 1000) {
            ////////////////////////////////////////////////////////////////////////////
            // eh counter
            ////////////////////////////////////////////////////////////////////////////
            //beim Spieler mit den meisten EH-Punkten wird der eh_counter hochgez�hlt
            $db_daten = mysql_query("SELECT * FROM de_user_data LEFT JOIN de_login ON (de_user_data.user_id=de_login.user_id) 
			WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY ehscore DESC LIMIT 2", $db);
            $data = array();
            while ($row = mysql_fetch_array($db_daten)) {
                $data[] = $row;
            }
            if ($data[0]['ehscore'] != $data[1]['ehscore']) {
                mysql_query("UPDATE de_user_data SET eh_counter=eh_counter+1 WHERE user_id='".$data[0]['user_id']."'", $db);
            }

            ////////////////////////////////////////////////////////////////////////////
            // eh-reset
            ////////////////////////////////////////////////////////////////////////////
            $db_daten = mysql_query("SELECT * FROM de_user_data WHERE eh_counter>='".$sv_eh_counter."' LIMIT 1", $db);
            $num = mysql_num_rows($db_daten);
            if ($num == 1) {
                $row = mysql_fetch_array($db_daten);
                $user_id = $row['user_id'];
                $spielername = $row['spielername'];
                $sector = $row['sector'];
                //account zurücksetzen, in sektor 1 (0:0) verschieben, eh_siege um 1 erh�hen
                $sql = array();
                //aktuellen Sektor speichern
                $sql[] = "UPDATE de_user_data set tick=1, score=0, restyp01=1000000, restyp02=500000, restyp03=250000, restyp04=125000, restyp05=100, 
				col=0, col_build=0, sonde=0, agent=0, agent_lost=0,
				buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, 
				tradescore=0, sells=0, secmoves=0, votefor=0, secmoves=0, tcount=0, zcount=0, eartefakt=0, kartefakt=0, 
				dartefakt=0, platz=1, rang=1, scanhistory='', platz_last_day=1, trade_sell_sum=0, trade_buy_sum=0, trade_forbidden=0, 
				spielername=nrspielername, rasse=nrrasse, sm_rboost=0, actpoints=0, palenium=0, sm_tronic=0, sm_kartefakt=0, sm_col=0, 
				artbldglevel=30, sm_art1=0, sm_art2=0, sm_art3=0, sm_art4=0, sm_art5=0, sm_art6=0, sm_art7=0, sm_art8=0, sm_art9=0, sm_art10=0, 
				sm_art11=0, sm_art12=0, sm_art13=0, sm_art14=0, sm_art15=0, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, npccol=0, archi=0, 
				geworben=0, useefta=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0, ehlock=0, 
				eftagetlastartefact=0, ehscore=0, defenseexp=0, geteftabonus=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, 
				npcartefact=0, specreset=0, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0, tradesystemscore=0, tradesystemtrades=0, tradesystem_mb_uid=0, 
				tradesystem_mb_tick=0, lastpcatt=0, fleetscore=0,bgscore0=0, bgscore1=1, bgscore2=0, bgscore3=0, bgscore4=0 WHERE user_id='".$user_id."';";

                $sql[] = "UPDATE de_user_data SET sector=0, `system`=0, eh_siege=eh_siege+1, eh_counter=0 WHERE user_id='".$user_id."'";
                $sql[] = "UPDATE de_user_fleet set komatt=0, komdef=0, zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0,
				e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0, e90=0, artid1=0, artlvl1=0, artid2=0, artlvl2=0, artid3=0, artlvl3=0, artid4=0, artlvl4=0 , artid5=0, artlvl5=0 , artid6=0, artlvl6=0  
				WHERE user_id LIKE'".$user_id."-%';";

                $sql[] = "DELETE FROM `de_user_achievement` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_artefact` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_build` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_locks` WHERE user_id='".$user_id."';";

                $sql[] = "DELETE FROM `de_user_map` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_map_bldg` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_map_loot` WHERE user_id='".$user_id."';";

                $sql[] = "DELETE FROM `de_user_mission` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_quest` WHERE user_id='".$user_id."';";
                //$sql[]="DELETE FROM `de_user_stat` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_special_ship` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_storage` WHERE user_id='".$user_id."';";
                $sql[] = "DELETE FROM `de_user_trade` WHERE user_id='".$user_id."';";

                for ($i = 0;$i < count($sql);$i++) {
                    mysql_query($sql[$i], $db);
                }

                //info in die sektorhistorie packen - spieler verläßt den sektor
                mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '3', '$sector', '$spielername');", $db);

                /*
                echo '<hr>Erhabenercreditgewinn:<br>';
                wt_change_credits($user_id, 50, 'Creditgewinn Erhabener');
                echo '<br>'.$user_id;
                */

                //mail bzgl. EH
                @mail_smtp($GLOBALS['env_admin_email'], $sv_server_tag.' Ewige Runde: Neuer EH: user_id: '.$user_id.' - Spielername: '.$spielername, ' ');

                //der ally den Sieg mit anrechnen
                $ally_id = get_player_allyid($user_id);
                if ($ally_id > 0) {
                    mysql_query("UPDATE de_allys SET eh_gestellt_anz=eh_gestellt_anz+1 WHERE id='".$ally_id."'", $db);
                }

                //meldung an den Chat
                $meldung = ''.$spielername.' ist der neue ERHABENE.';

                //im Sektor die Sektorflotten verkleiner
                $meldung .= ' Es wurden 2% der Sektorschiffe und 1% der Sektorkollektoren vernichtet.';

                mysql_query("UPDATE de_sector SET e1=e1*0.98, e2=e2*0.98, col=col*0.99", $db);

                //anderen allianzen etwas zerstören, außer dem meta-partner
                if ($ally_id > 0 && mt_rand(1, 100) > 75) {
                    $ally_id_partner = get_allyid_partner($ally_id);
                    $r = mt_rand(1, 4);
                    switch ($r) {
                        case 1:
                            mysql_query("UPDATE de_allys SET bldg1=bldg1-1 WHERE bldg1>0 AND id<>'".$ally_id."' AND id<>'".$ally_id_partner."';", $db);
                            $geb_name = 'Diplomatiezentrum';
                            break;
                        case 2:
                            mysql_query("UPDATE de_allys SET bldg3=bldg3-1 WHERE bldg3>0 AND id<>'".$ally_id."' AND id<>'".$ally_id_partner."';", $db);
                            $geb_name = 'Leitzentrale Feuroka';
                            break;
                        case 3:
                            mysql_query("UPDATE de_allys SET bldg4=bldg4-1 WHERE bldg4>0 AND id<>'".$ally_id."' AND id<>'".$ally_id_partner."';", $db);
                            $geb_name = 'Leitzentrale Bloroka';
                            break;
                        case 4:
                            mysql_query("UPDATE de_allys SET bldg5=bldg5-1 WHERE bldg5>0 AND id<>'".$ally_id."' AND id<>'".$ally_id_partner."';", $db);
                            $geb_name = 'Artefaktgeb&auml;udeerweiterung';
                            break;
                    }

                    $meldung .= ' Andere Allianzen, ein evtl. Allianz-Partner ausgenommen, haben schwere Sch&auml;den bei Ihren Allianzgeb&auml;uden ('.$geb_name.') hinnehmen m&uuml;ssen.';
                }



                //Meldung im allgemeinen Chat
                insert_chat_msg(0, 2, '', '<font color="#802ec1">'.$meldung.'</font>');
            }
        }

    } elseif ($sv_hardcore == 1) {
        if ($maxtick >= 1000) {
            ////////////////////////////////////////////////////////////////////////////
            // eh counter Hardcore
            ////////////////////////////////////////////////////////////////////////////
            //beim Spieler mit den meisten EH-Punkten wird der eh_counter hochgez�hlt
            $db_daten = mysql_query("SELECT * FROM de_user_data LEFT JOIN de_login ON (de_user_data.user_id=de_login.user_id) 
			WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY ehscore DESC LIMIT 2", $db);
            $data = array();
            while ($row = mysql_fetch_array($db_daten)) {
                $data[] = $row;
            }
            if ($data[0]['ehscore'] != $data[1]['ehscore']) {
                mysql_query("UPDATE de_user_data SET eh_counter=eh_counter+1 WHERE user_id='".$data[0]['user_id']."'", $db);
            }

            ////////////////////////////////////////////////////////////////////////////
            // eh-reset
            ////////////////////////////////////////////////////////////////////////////
            $db_daten = mysql_query("SELECT * FROM de_user_data WHERE eh_counter>='".$sv_eh_counter."' LIMIT 1", $db);
            $num = mysql_num_rows($db_daten);
            if ($num == 1) {
                $row = mysql_fetch_array($db_daten);
                $user_id = $row['user_id'];
                $spielername = $row['spielername'];
                $sector = $row['sector'];
                $eh_siege = $row['eh_siege'];


                //////////////////////////////////////////////////////////////////////
                //überprüfen ob er evtl. mit dem Teilsieg EH geworden ist
                //////////////////////////////////////////////////////////////////////
                if ($eh_siege + 1 >= $sv_hardcore_need_wins) {
                    //er ist erhabener

                    //er hat gewonnen, server anhalten
                    mysql_query("UPDATE de_system SET domtick=0, doetick=0", $db);
                    $erhabenenstop = 1;
                    //dem erhabenen die credits geben
                    /*
                    echo '<hr>Erhabenercreditgewinn:<br>';
                    wt_change_credits($user_id, 1000, 'Creditgewinn Erhabener');
                    echo '<br>'.$user_id;
                    */


                    //mail an den detverteiler, wenn es ein bezahlserver ist
                    //da pde-server wegfallen immer eine e-mail schicken
                    //if($sv_pcs_id>0)
                    @mail_smtp($GLOBALS['env_admin_email'], 'Die Runde auf '.$sv_server_tag.' ist vorbei.', 'Die Runde auf '.$sv_server_tag.' ist vorbei.');

                    //die rundenpunkte verteilen
                    //der erhabene bekommt 1 extra
                    mysql_query("UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE user_id=$user_id AND npc=0", $db);

                    //den rang des spielers in der DB updaten
                    mysql_query("UPDATE de_user_data SET rang = 0 WHERE user_id = $user_id", $db);
                } else {
                    ///////////////////////////////////////////////////////////////////////////
                    //account zurücksetzen, in sektor 1 (0:0) verschieben, eh_siege um 1 erhöhen
                    ///////////////////////////////////////////////////////////////////////////
                    //die Titanen-Energiekerne beim Teilreset zurückerstatten
                    $titanen_sql = "SELECT SUM(e90) AS anzahl FROM de_user_fleet WHERE user_id LIKE '".$user_id."-%';";
                    $db_titanen = mysqli_query($GLOBALS['dbi'], $titanen_sql);
                    $row_titanen = mysqli_fetch_array($db_titanen);
                    change_storage_amount($user_id, 2, $row_titanen['anzahl'], false);


                    $sql = array();
                    //aktuellen Sektor speichern
                    $sql[] = "UPDATE de_user_data set tick=1, score=0, restyp01=1000000, restyp02=500000, restyp03=250000, restyp04=125000, restyp05=100, 
					col=0, col_build=0, sonde=0, agent=0, agent_lost=0,
					buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, 
					tradescore=0, sells=0, secmoves=0, votefor=0, secmoves=0, tcount=0, zcount=0, eartefakt=0, kartefakt=0, 
					dartefakt=0, platz=1, rang=1, scanhistory='', platz_last_day=1, trade_sell_sum=0, trade_buy_sum=0, trade_forbidden=0, 
					spielername=nrspielername, rasse=nrrasse, sm_rboost=0, actpoints=0, palenium=0, sm_tronic=0, sm_kartefakt=0, sm_col=0, 
					sm_art1=0, sm_art2=0, sm_art3=0, sm_art4=0, sm_art5=0, sm_art6=0, sm_art7=0, sm_art8=0, sm_art9=0, sm_art10=0, 
					sm_art11=0, sm_art12=0, sm_art13=0, sm_art14=0, sm_art15=0, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, npccol=0, archi=0, 
					geworben=0, useefta=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0, ehlock=0, 
					eftagetlastartefact=0, ehscore=0, defenseexp=0, geteftabonus=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, 
					npcartefact=0, specreset=0, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0, tradesystemscore=0, tradesystemtrades=0, tradesystem_mb_uid=0, 
					tradesystem_mb_tick=0, lastpcatt=0, fleetscore=0 WHERE user_id='".$user_id."';";

                    $sql[] = "UPDATE de_user_data SET sector=0, `system`=0, eh_siege=eh_siege+1, eh_counter=0 WHERE user_id='".$user_id."'";
                    $sql[] = "UPDATE de_user_fleet set komatt=0, komdef=0, zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0,
					e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0, e90=0 WHERE user_id LIKE'".$user_id."-%';";

                    $sql[] = "DELETE FROM `de_user_achievement` WHERE user_id='".$user_id."';";
                    $sql[] = "DELETE FROM `de_user_build` WHERE user_id='".$user_id."';";
                    $sql[] = "DELETE FROM `de_user_locks` WHERE id='".$user_id."';";
                    $sql[] = "DELETE FROM `de_user_mission` WHERE user_id='".$user_id."';";
                    $sql[] = "DELETE FROM `de_user_quest` WHERE user_id='".$user_id."';";
                    //$sql[]="DELETE FROM `de_user_stat` WHERE user_id='".$user_id."';";
                    $sql[] = "DELETE FROM `de_user_trade` WHERE user_id='".$user_id."';";

                    for ($i = 0;$i < count($sql);$i++) {
                        mysql_query($sql[$i], $db);
                    }

                    //info in die sektorhistorie packen - spieler verläßt den sektor
                    mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '3', '$sector', '$spielername');", $db);

                    /*
                    echo '<hr>Erhabenercreditgewinn:<br>';
                    wt_change_credits($user_id, 100, 'Creditgewinn Erhabenen-Teilsieg');
                    echo '<br>'.$user_id;
                    */

                    //mail bzgl. EH
                    @mail_smtp($GLOBALS['env_admin_email'], $sv_server_tag.' Hardcore: Neuer Teil-EH: user_id: '.$user_id.' - Spielername: '.$spielername, ' ');

                    //meldung an den Chat
                    $meldung = ''.$spielername.' hat einen ERHABENEN-Teilsieg errungen.';

                    //Meldung im allgemeinen Chat
                    insert_chat_msg(0, 2, '', '<font color="#802ec1">'.$meldung.'</font>');
                }
            }
        }
    } else {//normaler Rundentyp
        //maximale tickgröße laden
        $db_daten = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data", $db);
        $row = mysql_fetch_array($db_daten);
        $maxtick = $row["tick"];
        if ($maxtick < 2500000 or $sv_comserver_roundtyp == 1) {
            /*
            //daten vom besten spieler auslesen
            //$result = mysql_query("SELECT user_id, score FROM de_user_data WHERE statusORDER BY score DESC LIMIT 1",$db);
            $result = mysql_query("SELECT de_user_data.score, de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) where de_login.status=1 ORDER BY score DESC LIMIT 1",$db);
            $row = mysql_fetch_array($result);
            $uid=$row["user_id"];
            $score=$row["score"];*/

            //$sv_winscore wird in zukunft die rundendauer in ticks angeben
            //$source=mysql_query("SELECT MAX(tick) FROM de_user_data");
            //$score=mysql_result($source,0,0);
            $score = $maxtick;

            if ($sv_comserver_roundtyp == 1) {
                $sv_winscore += 2500000;//fix f�r community-server in der BR
            }

            //$sv_winscore=0;
            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            //rundenpunkte und creditgewinne beim start des eh-kampfes verteilen
            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            if ($score >= $sv_winscore and $roundpointsflag == 0) {
                //info bzgl. start eh-kampf
                @mail_smtp($GLOBALS['env_admin_email'], 'Bei der Runde auf '.$sv_server_tag.' hat der Erhabenenkampf begonnen.', 'Bei der Runde auf '.$sv_server_tag.' hat der Erhabenenkampf begonnen.');

                //flag setzen, dass nur einmal pro runde die verteilung stattfindet
                mysql_query("UPDATE de_system SET roundpointsflag=1", $db);


                //rundenpunkte zuerst
                //jeder bekommt einen
                mysql_query("UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE npc=0", $db);
                //die alphas bekommen zus�tzlich einen
                mysql_query("UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE rang=1 AND npc=0", $db);

                /*
                //creditgewinne
                //spieler mit den meisten punkten
                echo '<hr>Punktecreditgewinne:<br>';
                $db_daten = mysql_query("SELECT de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 ORDER BY score DESC LIMIT 3", $db);
                $platz = 0;
                while ($row = mysql_fetch_array($db_daten)) {
                    //credits gutschreiben
                    wt_change_credits($row["user_id"], $sv_credit_win[0][$platz], 'Creditgewinn '.($platz + 1).'. Platz Punkte');
                    echo '<br>'.$row["user_id"];
                    $platz++;
                }

                //spieler mit den meisten Executorpunkten
                if ($sv_deactivate_trade == 0) {
                    echo '<br><br>Executor-Creditgewinne:<br>';
                    $db_daten = mysql_query("SELECT de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1  AND de_user_data.npc=0 ORDER BY pve_score DESC LIMIT 3", $db);
                    $platz = 0;
                    while ($row = mysql_fetch_array($db_daten)) {
                        //credits gutschreiben
                        wt_change_credits($row["user_id"], $sv_credit_win[1][$platz], 'Creditgewinn '.($platz + 1).'. Platz Executorpunkte');
                        echo '<br>'.$row["user_id"];
                        $platz++;
                    }
                }
                */
                /*
                //erfolgreichster kopfgeldj�nger
                echo '<br><br>Kopfgeldj�gercreditgewinne:<br>';
                $db_daten = mysql_query("SELECT de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 ORDER BY kgget DESC LIMIT 3",$db);
                $platz=0;
                while($row = mysql_fetch_array($db_daten))
                {
                  //credits gutschreiben
                  wt_change_credits($row["user_id"], $sv_credit_win[2][$platz], 'Creditgewinn '.($platz+1).'. Platz Kopfgeldj�ger');
                  echo '<br>'.$row["user_id"];
                  $platz++;
                }
                */
                //spieler mit der meisten cyborgerfahrung in efta
                /*
                if($sv_server_lang==1)
                {
                  echo '<br><br>EFTA-Creditgewinne:<br>';
                  $db_daten = mysql_query("SELECT de_login.user_id FROM de_login LEFT JOIN de_cyborg_data ON(de_login.user_id = de_cyborg_data.user_id) WHERE de_login.status=1 ORDER BY exp DESC LIMIT 3",$db);
                  $platz=0;
                  while($row = mysql_fetch_array($db_daten))
                  {
                    //credits gutschreiben
                    wt_change_credits($row["user_id"], $sv_credit_win[3][$platz], 'Creditgewinn '.($platz+1).'. Platz EFTA-Erfahrungspunkte');
                    echo '<br>'.$row["user_id"];
                    $platz++;
                  }
                }
                */
            }

            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            // auf den erhabenen pr�fen
            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            if ($score >= $sv_winscore) {
                //testen wer erhabener ist
                //$result = mysql_query("SELECT de_user_data.score, de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) where de_login.status=1 ORDER BY score DESC LIMIT 1",$db);
                $result = mysql_query("SELECT de_user_data.ehscore, de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) where de_login.status=1 AND de_user_data.ehlock < $maxtick ORDER BY ehscore DESC LIMIT 1", $db);
                $row = mysql_fetch_array($result);
                $uid = $row["user_id"];

                //er ist erhabener
                if ($winid == $uid) {
                    //er war auch letzten tick schon erhabener
                    if ($winticks == 1) {
                        //er hat gewonnen, server anhalten
                        mysql_query("UPDATE de_system SET domtick=0, doetick=0", $db);
                        $erhabenenstop = 1;

                        /*
                        //dem erhabenen die credits geben
                        echo '<hr>Erhabenercreditgewinn:<br>';
                        wt_change_credits($uid, $sv_credit_win[4][0], 'Creditgewinn Erhabener');
                        echo '<br>'.$uid;
                        */

                        //mail an den detverteiler, wenn es ein bezahlserver ist
                        //da pde-server wegfallen immer eine e-mail schicken
                        //if($sv_pcs_id>0)
                        @mail_smtp($GLOBALS['env_admin_email'], 'Die Runde auf '.$sv_server_tag.' ist vorbei.', 'Die Runde auf '.$sv_server_tag.' ist vorbei.');

                        //die rundenpunkte verteilen
                        //der erhabene bekommt 1 extra
                        mysql_query("UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE user_id=$uid AND npc=0", $db);
                    }

                    //verbleibenden ticks bis zum sieg um eins verringern
                    mysql_query("UPDATE de_system SET winticks=winticks-1", $db);
                    $winticks--;
                } else {
                    //es gibt einen neuen erhabenen
                    mysql_query("UPDATE de_system SET winid = $uid, winticks=$sv_benticks", $db);
                    $winticks = $sv_benticks;
                    $winid = $uid;
                }
                //den rang des spielers in der dp updaten
                mysql_query("UPDATE de_user_data SET rang = 0 WHERE user_id = $uid", $db);
            } else {
                //evtl. erhabenen l�schen
                mysql_query("update de_system set winid = 0, winticks=0", $db);
                $winid = 0;
            }
        }
    }
    //--------------- erhabenentest end -----------------------------

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //erstelle die toplist
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    include_once "wt_create_toplist.php";

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    // Allianzaufgaben/Allianzmissionen
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    include_once "wt_allyjobs.inc.php";


    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //lege in der datenbank die zeit des letzten ticks ab
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    $time=date("YmdHis");

    $sql = "UPDATE de_system set lasttick = '$time'";
    mysql_query($sql, $db);

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //lege in einer datei die zeit des letzten ticks ab
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    $filename = $directory."cache/lastedbtick.tmp";

    $cachefile = fopen($filename, 'w');

    xecho('<?php $t1='."'".$time."';");

    xecho("?>");

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //erstelle datei mit der useranzahl
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    $db_daten = mysql_query("SELECT count(user_id) FROM de_user_data", $db);

    $gesamtuser = mysql_result($db_daten, 0, 0);
    if ($gesamtuser == 0) {
        $gesamtuser = 1;
    }

    $filename = $directory."cache/anz_user.tmp";

    $cachefile = fopen($filename, 'w');

    xecho('<?php $gesamtuser='.$gesamtuser.';');

    xecho('?>');

    //erstelle datei mit der zeit des letzten wirtschaftsticks-ticks

    $filename = $directory."cache/lasttick.tmp";

    $cachefile = fopen($filename, 'w');

    $zeit = date("H:i");
    xecho('<?php $lasttick="'.$zeit.'";');

    xecho('?>');

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //erstelle die loginseiten-statistik
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    include_once "wt_create_status.php";

    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //efta
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //include_once "wt_efta.php";

    //rausvoten
    mysql_query("UPDATE de_sector_voteout SET ticks = ticks - 1", $db);
    mysql_query("DELETE FROM de_sector_voteout WHERE ticks<=0", $db);

    //cron - sachen die zu einem bestimmten zeitpunkt bearbeitet werden m�ssen
    include_once "wt_cron.php";


    //tick wieder aktivieren nachdem alles abgearbeitet worden ist, au�er es gibt nen erhabenen
    if ($erhabenenstop != 1) {
        $sql = "UPDATE de_system set doetick=1";
        echo $sql;
        mysql_query($sql, $db);
    } else { //die runde ist zu ende
        //verteilungsflag zurücksetzen
        mysql_query("UPDATE de_system SET roundpointsflag=0", $db);

        //email mit gewinnerdaten verschicken
        //spieler - name, koordinaten, kollektoren, punkte, rasse, rundelaufzeit
        $db_daten = mysql_query("SELECT * FROM de_user_data LEFT JOIN de_login ON (de_login.user_id=de_user_data.user_id) WHERE de_user_data.rang=0 LIMIT 1", $db);
        $row = mysql_fetch_array($db_daten);
        $ranglistendaten = "Spielername: ".$row["spielername"]."\n".
        "Koordinaten: ".$row["sector"].":".$row["system"]."\n".
        "Kollektoren: ".$row["col"]."\n".
        "Punkte: ".$row["score"]."\n".
        "Rasse: ".$row["rasse"]."\n";

        $player_owner_id = $row["owner_id"];
        $player_spielername = $row["spielername"];
        $player_sector = $row["sector"];
        $player_system = $row["system"];
        $player_col = $row["col"];
        $player_score = $row["score"];
        $player_rasse = $row["rasse"];

        $db_daten = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data", $db);
        $row = mysql_fetch_array($db_daten);
        $ranglistendaten .= "Rundenlaufzeit: ".$row["tick"]."\n\n\n";
        $round_wt = $row['tick'];


        //sektor - sektor, name, punkte
        $db_daten = mysql_query("SELECT * FROM de_sector WHERE sec_id>1 AND npc=0 AND platz>0 OR sec_id=5 ORDER BY platz ASC LIMIT 1", $db);
        $row = mysql_fetch_array($db_daten);
        $sec_id = $row["sec_id"];
        $ranglistendaten .= "Sektor: ".$row["sec_id"]."\n".
        "Name: ".$row["name"]."\n";
        $sector_id = $row['sec_id'];
        $sector_name = $row['name'];

        /*
        CREATE TABLE `de_server_round_toplist` (
            `round_id` mediumint(8) UNSIGNED NOT NULL,
            `player_owner_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
            `player_spielername` varchar(20) CHARACTER SET utf8mb4 NOT NULL,
            `player_sector` mediumint(9) NOT NULL,
            `player_system` smallint(5) UNSIGNED NOT NULL,
            `player_col` int(10) UNSIGNED NOT NULL,
            `player_score` bigint(20) UNSIGNED NOT NULL,
            `player_rasse` smallint(6) NOT NULL,
            `round_wt` int(11) NOT NULL,
            `sector_id` mediumint(8) UNSIGNED NOT NULL,
            `sector_name` int(10) UNSIGNED NOT NULL,
            `sector_score` bigint(20) UNSIGNED NOT NULL,
            `ally_id` smallint(5) UNSIGNED NOT NULL,
            `ally_tag` varchar(7) CHARACTER SET utf8mb4 NOT NULL,
            `ally_roundpoints` int(10) UNSIGNED NOT NULL
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

          --
          -- Indizes der exportierten Tabellen
          --

          --
          -- Indizes für die Tabelle `de_server_round_toplist`
          --
          ALTER TABLE `de_server_round_toplist`
            ADD PRIMARY KEY (`round_id`);

          --

*/

        //Sektorpunkte
        $db_daten = mysql_query("SELECT SUM(score) AS gespunkte FROM de_user_data WHERE sector='$sec_id'", $db);
        $row = mysql_fetch_array($db_daten);
        $ranglistendaten .= "Punkte: ".$row["gespunkte"]."\n\n\n";
        $sector_score = $row['gespunkte'];

        //allianz siegartefakte
        $db_daten = mysql_query("SELECT id, allytag, questpoints FROM de_allys ORDER BY questpoints DESC, id ASC LIMIT 1", $db);
        $row = mysql_fetch_array($db_daten);
        $ranglistendaten .= "Allianz: ".$row['allytag']." Roundpoints: ".$row['questpoints']."\n".
        $ally_id = $row['id'];
        $ally_tag = $row['allytag'];
        $ally_roundpoints = $row['questpoints'];

        //sql-befehl für das löschen der logdaten
        $logtime = date('Y-m-d H:i:s', time());
        $ranglistendaten .= "SQL-Logfilelöschung: DELETE FROM gameserverlogdata WHERE serverid=$sv_servid AND time<'$logtime'\n";

        //Ranglistendaten per E-Mail an den Admin schicken
        @mail_smtp($GLOBALS['env_admin_email'], 'Die Runde auf '.$sv_server_tag.' ist vorbei - Ranglistendaten', $ranglistendaten);

        //Ranglistendaten in der DB speichern
        $sql = "INSERT INTO de_server_round_toplist SET 
	player_owner_id='$player_owner_id', 
	player_spielername='$player_spielername', 
	player_sector='$player_sector', 
	player_system='$player_system', 
	player_col='$player_col', 
	player_score='$player_score', 
	player_rasse='$player_rasse', 
	round_wt='$round_wt', 
	sector_id='$sector_id', 
	sector_name='$sector_name', 
	sector_score='$sector_score', 
	ally_id='$ally_id', 
	ally_tag='$ally_tag',
	ally_roundpoints='$ally_roundpoints' 
	";
        echo $sql;

        mysqli_query($GLOBALS['dbi'], $sql);

        //überprüfen ob es einen automatischen reset geben soll
        if ($sv_auto_reset == 1) {
            include_once "wt_auto_reset.php";
            //ticks wieder starten
            mysql_query("UPDATE de_system SET doetick=1, domtick=1", $db);
        }
    }

    //beim communityserver ggf. das dazugeh�rige script einbinden
    if ($sv_comserver == 1) {
        include_once 'wt_comserver.php';
    }

    //soll die Karte neu generiert werden?
    include_once 'wt_create_map.php';

    print '<br><br>Letzter Tick: '.date("d/m/Y - H:i:s");

} else {
    echo '<br>Wirtschaftsticks deaktiviert.<br>';
} //doetick

function wt_change_credits($uid, $amount, $reason)
{
    /*
    global $db;

    //zuerst auslesen wieviel man hat
    $db_daten = mysql_query("SELECT credits FROM de_user_data WHERE user_id='$uid'", $db);
    $row = mysql_fetch_array($db_daten);
    $hascredits = $row["credits"];
    //wert in der db �ndern
    mysql_query("UPDATE de_user_data SET credits=credits+'$amount' WHERE user_id='$uid'", $db);

    //creditanzahl �ndern
    $hascredits = $hascredits + $amount;

    //die creditausgabe im billing-logfile hinterlegen
    $datum = date("Y-m-d H:i:s", time());
    $ip = getenv("REMOTE_ADDR");
    $clog = "Zeit: $datum\nIP: $ip\n".$reason."- Neuer Creditstand: $hascredits ($amount)\n--------------------------------------\n";
    $fp = fopen("../cache/creditlogs/$uid.txt", "a");
    fputs($fp, $clog);
    fclose($fp);
    */
}

?>
</body>
</html>
