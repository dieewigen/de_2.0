<?php
ob_start();

require_once __DIR__ . '/../vendor/autoload.php';
use DieEwigen\DE2\Model\Alliance\AllyMemberLimitCalc;
use DieEwigen\DE2\Model\Tick\TickSpendCollectorFromSector1;
use DieEwigen\DE2\Model\Tick\TickGiveSecBuildingsToNPC2;

set_time_limit(240);
$directory = '../';
include_once $directory."inc/sv.inc.php";
include_once $directory."inc/env.inc.php";
//überprüfen ob es Zeit für den Tick ist
if ($sv_debug == 0) {
    if (!in_array(intval(date("i")), $GLOBALS['wts'][date("G")])) {
        die('<br>WT: NO TICK TIME<br>');
    }
}

include_once $directory."inccon.php";
include_once $directory."inc/artefakt.inc.php";
include_once $directory."inc/lang/".$sv_server_lang."_wt.lang.php";
include_once $directory."inc/lang/".$sv_server_lang."_wt_zufallmsg.lang.php";
include_once $directory."inc/sabotage.inc.php";
include_once $directory."inc/allyjobs.inc.php";
include_once $directory."lib/map_system_defs.inc.php";
include_once $directory."lib/map_system.class.php";
include_once $directory."functions.php";
include_once "kt_einheitendaten.php";

?>
<html>
<head>
</head>
<body>
<?php

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system", []);
$row = mysqli_fetch_array($result);
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
    $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
    $row     = mysqli_fetch_array($result);
    $maxtick = $row["tick"];

    //zuerstmal tick sperren, damit die sich niemals �berholen k�nnen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET doetick=0", []);

    //maximale anzahl von kollektoren auslesen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0", []);
    $row = mysqli_fetch_array($db_daten);
    $maxcol = $row['maxcol'];

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    // alle x ticks erhält eine allianz ein allianzartefakt
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    if ($maxtick % 96 == 0 and $maxtick > 0) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+1", []);
    }

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    // allianz-mitglieder-maximum bestimmen
    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////
    $allyService = new AllyMemberLimitCalc($GLOBALS['dbi']);
    try {
        $affected = $allyService->updateAlliesMemberLimit();
        echo "<br>Allianz-memberlimit aktualisiert, neues Limit: {$affected['memberlimit']}<br>";
    } catch (\Throwable $e) {
        // Logging oder Fallback
        echo "<br>Fehler beim Aktualisieren des Allianz-Memberlimits: " . $e->getMessage() . "<br>";
    }

    ///////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////


    //urlaubszeit abgelaufen?
    $time = time();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status = 1 WHERE status = 3 AND ?>UNIX_TIMESTAMP(last_login) AND delmode=0", [$time]);

    //Spieler umziehen, die zu groß für den Startsektor sind, außer die Funktion ist deaktiviert
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.sector, de_user_data.`system` FROM de_login left join de_user_data 
    on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND delmode=0 AND sector=1 AND (col>=10 OR score>=5000000)", []);

    $num = mysqli_num_rows($db_daten);
    echo "<br>$num Spieler aus Sektor geholt / den Umzug in der DB hinterlegt<br>";
    while ($row = mysqli_fetch_array($db_daten)) {
        $uid = $row["user_id"];
        $sector = $row["sector"];
        $system = $row["system"];

        //account in den umzugsmodus versetzen
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login set status = 4, savestatus=1 WHERE user_id = ?", [$uid]);
        //umzug hinterlegen
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_sector_umzug set user_id=?, typ=0, sector=?, `system`=?", [$uid, $sector, $system]);
    }


    //////////////////////////////////////////////////////////
    // Kollektoren aus Sektor 1 transferieren
    //////////////////////////////////////////////////////////
    echo '<br>Kollektortransfer aus Sektor 1<br>';
    print_r(new TickSpendCollectorFromSector1($GLOBALS['dbi'])->run());

    //////////////////////////////////////////////////////////
    // bei X Ticks überprüfen ob die Sektoren in den nur 
    // NPC-Typ 2 sind alle Sektorgebäude bekommen
    //////////////////////////////////////////////////////////
    if($rundenalter_wt == 2000){
        echo '<br>Sektorgebäude an Sektoren mit nur NPC Typ 2<br>';
        $npc2SecBuildings = new TickGiveSecBuildingsToNPC2($GLOBALS['dbi']);
        $result = $npc2SecBuildings->run();
        print_r($result);
    }

    //////////////////////////////////////////////////////////
    //Kollektoren an die NPC Typ 2 verteilen
    //////////////////////////////////////////////////////////
    if($rundenalter_wt > 2000 && $rundenalter_wt % 60 == 0){
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data set col=col+1 WHERE npc=2 ", []);
    }

    //////////////////////////////////////////////////////////
    //votetimer für den sektor um 1 verringern
    //////////////////////////////////////////////////////////
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET votetimer=votetimer-1 WHERE votetimer>0", []);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET votecounter=votecounter-1 WHERE votecounter>0", []);

    //////////////////////////////////////////////////////////
    //manage map data
    //////////////////////////////////////////////////////////
    include_once "wt_manage_map.php";

    //sektorgebauede bauen
    $res = mysqli_execute_query($GLOBALS['dbi'], "select sec_id, techs, buildgnr, buildgtime from de_sector where buildgtime <= 1  AND buildgnr > 0", []);
    $num = mysqli_num_rows($res);
    echo "<br>$num Sektor-Gebäude-Datensätze gefunden<br>";
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET buildgtime = buildgtime - 1 WHERE buildgtime > 0", []);
    while ($row = mysqli_fetch_array($res)) {
        $uid   = $row["sec_id"];
        $techs = $row["techs"];
        $bnr   = $row["buildgnr"];
        $techs[$bnr - 119] = 1;
        $bnr = 0;

        mysqli_execute_query($GLOBALS['dbi'], "update de_sector set techs = ? where sec_id = ?", [$techs, $uid]);
        mysqli_execute_query($GLOBALS['dbi'], "update de_sector set buildgnr = ? where sec_id = ?", [$bnr, $uid]);
    }

    echo '<br>Gebäude fertig ';
    echo date("d/m/Y - H:i:s");

    //Sektor-Einheiten bauen
    $tech_id = 1;
    $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector_id, anzahl FROM de_sector_build WHERE verbzeit<=1", []);
    $num = mysqli_num_rows($res);

    echo "<br>$num Sektor-Einheiten-Datensätze gefunden<br>";
    while ($row = mysqli_fetch_array($res)) {
        $uid      = $row["sector_id"];
        $anzahl   = $row["anzahl"];
        $sql = "update de_sector set e1 = e1 + ? where sec_id = ?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$anzahl, $uid]);
        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_sector_build where sector_id = ? AND tech_id=? AND verbzeit=1", [$uid, $tech_id]);
    }
    //datens�tze mit geringerer zeit aktualisieren
    mysqli_execute_query($GLOBALS['dbi'], "update de_sector_build set verbzeit = verbzeit-1", []);



    //Schiffs-Einheiten, Kollektoren, Agenten, Sonden bauen
    $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, tech_id, anzahl FROM de_user_build WHERE verbzeit<=1", []);
    $num = mysqli_num_rows($res);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_build SET verbzeit = verbzeit-1", []);
    echo "<br>$num Schiffs-Einheiten, Kollektoren, Agenten, Sonden-Datensätze gefunden<br>";
    while ($row = mysqli_fetch_array($res)) {

        $uid      = $row["user_id"];
        $tech_id  = $row["tech_id"];
        $anzahl   = $row["anzahl"];
        //es ist ein kollektor
        if ($tech_id == 80) {
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET col = col + ? where user_id = ?", [$anzahl, $uid]);
        }

        //es ist ein raumschiff
        if ($tech_id >= 81 && $tech_id <= 99) {
            $fleet_id = $uid.'-0';
            $sql = "update de_user_fleet set e$tech_id = e$tech_id + ? where user_id = ?";
            mysqli_execute_query($GLOBALS['dbi'], $sql, [$anzahl, $fleet_id]);
        }

        //es ist eine verteidigungsanlage
        if ($tech_id >= 100 && $tech_id <= 109) {
            $sql = "update de_user_data set e$tech_id = e$tech_id + ? where user_id = ?";
            mysqli_execute_query($GLOBALS['dbi'], $sql, [$anzahl, $uid]);
        }

        //es ist eine spionagesonde
        if ($tech_id == 110) {
            mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set sonde = sonde + ? where user_id = ?", [$anzahl, $uid]);
        }

        //es ist ein geheimagent
        if ($tech_id == 111) {
            mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set agent = agent + ? where user_id = ?", [$anzahl, $uid]);
        }

        //artefaktgebäudeupgrade
        if ($tech_id == 1000) {
            //Rasse und Gebäudelevel auslesen
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT rasse,artbldglevel FROM de_user_data WHERE user_id=?", [$uid]);
            $row = mysqli_fetch_array($db_daten);
            $artbldglevel = $row["artbldglevel"] + 1;
            $player_rasse = $row["rasse"];

            //punkte berechnen
            $score = $artbldglevel * 1000;

            //nachricht an den account schicken
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=?", [28]);
            $row = mysqli_fetch_array($db_daten);
            $tech_name = getTechNameByRasse($row["tech_name"], $player_rasse);
            $msg = $wt_lang['gebaeudeausbau'].': '.$tech_name.'<br>'.$wt_lang['gebaeudelevel'].': '.$artbldglevel;

            $time = date("YmdHis");
            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 2, ?, ?)", [$uid, $time, $msg]);

            //levelupgrade und fixpunkte in der db vermerken
            $sql = "UPDATE de_user_data SET fixscore = fixscore + ?, newnews = '1', artbldglevel = artbldglevel + 1 WHERE user_id = ?";
            mysqli_execute_query($GLOBALS['dbi'], $sql, [$score, $uid]);
        }

        //itemdata
        //echo 'A: '.$tech_id;
        if ($tech_id >= 10000 && $tech_id < 20000) {
            //echo '<br>csa: '.$uid.'/'.($tech_id-10000).'/'.$anzahl;
            change_storage_amount($uid, $tech_id - 10000, $anzahl);
        }

    }
    //abgearbeitete bauaufträge löschen
    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_build WHERE verbzeit<1", []);

    //datensatz mit geringerer zeit aktualisieren
    echo '<br>Einheiten fertig ';
    echo date("d/m/Y - H:i:s");

    //tronicverteilung noch initiieren wenn es keinen datensatz mehr gibt, in den tronic verteilt werden muss
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tcount FROM de_user_data WHERE tcount>0", []);
    $globtcount = mysqli_num_rows($db_daten);
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
    $db_daten_zufall = mysqli_execute_query($GLOBALS['dbi'], "SELECT zcount FROM de_user_data WHERE zcount>0", []);
    $globzcount = mysqli_num_rows($db_daten_zufall);

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
    //Rohstoffe + Punkte(Gebaeude, Forschungen)
    //für jede rasse einzeln durchlaufen
    /////////////////////////////////////////////////////////////////////
    //vorher alle Bauaufträge bzgl. punkten auslesen
    unset($user_buildscore);
    //bauaufträge ohne recycling
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, SUM(score) AS score FROM `de_user_build` WHERE recycling=0 GROUP BY user_id", []);
    while ($row = mysqli_fetch_array($result)) {
        if (!isset($user_buildscore[$row['user_id']])) {
            $user_buildscore[$row['user_id']] = 0;
        }
        $user_buildscore[$row['user_id']] += round($row['score'] / 10);
    }

    //bauaufträge mit recycling
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, SUM(score) AS score FROM `de_user_build` WHERE recycling=1 GROUP BY user_id", []);
    while ($row = mysqli_fetch_array($result)) {
        // Falls der Benutzer in der ersten Abfrage (recycling=0) nicht vorkam, muss initialisiert werden
        if (!isset($user_buildscore[$row['user_id']])) {
            $user_buildscore[$row['user_id']] = 0;
        }
        $user_buildscore[$row['user_id']] += $row['score'];
    }


    //spezialisierungscache für planetaren grundertrag erzeugen
    for ($i = 0;$i <= 2000;$i++) {
        $spec3cache[$i] = -1;
    }

    ////////////////////////////////////////////////
    // die Spieler nach Rassen durchgehen
    ////////////////////////////////////////////////
    for ($rasse = 1; $rasse <= $sv_anz_rassen; $rasse++) {
        echo '<br>Rasse: '.$rasse;

        $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.col, de_user_data.sector, de_user_data.agent, de_user_data.agent_lost, de_user_data.techs, de_user_data.ekey, de_user_data.e100, de_user_data.e101, de_user_data.e102, de_user_data.e103, de_user_data.e104,  de_user_data.tcount, de_user_data.zcount, de_user_data.eartefakt, de_user_data.kartefakt, de_user_data.dartefakt, de_user_data.tick , de_user_data.palenium, de_user_data.archi, de_user_data.npc, de_user_data.sc1, de_user_data.vs_auto_explore FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.rasse=?", [$rasse]);
        $num = mysqli_num_rows($res);

        //tronic-meldungen einbinden, damit man weiß wieviele man verwenden kann
        $tronic = '';//verhindert Warnings beim include, wird hier aber nicht benötigt, weil nur die id und anzahl in der DB gespeichert wird
        include $directory."inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";

        echo "<br>$num Rohstoff/Punkte-Datensätze gefunden<br>";
        $time = date("YmdHis");

        while ($irow = mysqli_fetch_array($res)) {
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
            $mysc1   = $irow["sc1"];
            $vs_auto_explore = $irow["vs_auto_explore"];

            $pt = loadPlayerTechs($uid);

            //ekey aufsplitten
            $hv = explode(";", $ekey);
            $keym = (float)($hv[0] ?? 0);
            $keyd = (float)($hv[1] ?? 0);
            $keyi = (float)($hv[2] ?? 0);
            $keye = (float)($hv[3] ?? 0);

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

            //kriegsartefakt
            $kartefaktenergie = $sv_kriegsartefaktertrag * $kartefakt;

            $dartefaktenergie = floor($ea / 100 * $dartefakt);
            $paleniumenergie = floor($ea / 10000 * $palenium);

            //ade-rassenbonus
            $adebonus = 0;

            $ea = $ea + $kartefaktenergie + $dartefaktenergie + $paleniumenergie + $adebonus;

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
                $db_datenspec = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec3=3", [$sector]);
                $planertragbonus = mysqli_num_rows($db_datenspec) * 10;
                if ($planertragbonus > 100) {
                    $planertragbonus = 100;
                }
                $spec3cache[$sector] = $planertragbonus / 100;
            }

            //grundertragbonus für die BR, gibt es nie in der Ewigen Runde und nicht bei Hardcore
            if ((($maxtick > 2500000 && $sv_ewige_runde != 1 && $sv_hardcore != 1)) and $sector > 1) {
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
                if ($tcount == 1 && ($npc == 0 || $npc == 2)) {
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
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 8, ?, ?)", [$uid, $time, $nachricht]);
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


                            mysqli_execute_query($GLOBALS['dbi'], $sql_zufall_schiff, []);
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
                            mysqli_execute_query($GLOBALS['dbi'], $sql_zufall_exp, []);
                            
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
                            $resname = '';
                            switch ($ressart) {
                                case 0:
                                    //Multiplex
                                    $resname = 'Multiplex';
                                    $anzahl = $tick * 12;
                                    $rm = $rm + ($tick * 12);
                                    break;

                                case 1:
                                    //Dyharra
                                    $resname = 'Dyharra';
                                    $anzahl = $tick * 6;
                                    $rd = $rd + ($tick * 6);
                                    break;

                                case 2:
                                    //Iradium
                                    $resname = 'Iradium';
                                    $anzahl = $tick * 5;
                                    $ri = $ri + ($tick * 5);
                                    break;

                                case 3:
                                    //Eternium
                                    $resname = 'Eternium';
                                    $anzahl = $tick * 3;
                                    $re = $re + ($tick * 3);
                                    break;
                            }

                            //eine Zufallsnachricht aus der Liste auswählen
                            $zufallsmsg = mt_rand(0, count($ressmsg) - 1);

                            //die Nachricht mit den Werten füllen
                            $nachricht = str_replace("{VALUE1}", $anzahl, $ressmsg[$zufallsmsg]);
                            $nachricht = str_replace("{VALUE2}", $resname, $nachricht);
                            break;
                    }

                    if ($npc == 0) {
                        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $nachricht]);
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
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
            while ($row = mysqli_fetch_array($db_daten)) {
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
            mysqli_execute_query($GLOBALS['dbi'], $sql, []);
            //palenium um eins verringern
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET palenium = palenium - 1 WHERE palenium>0 AND user_id=?", [$uid]);
        }
    }//ende for rassen...

    //troniccounter, zufallscounter und palenium um eins verringern
    if ($globtcount > 0) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET tcount = tcount - 1 WHERE tcount>0", []);
    }
    if ($globzcount > 0) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET zcount = zcount - 1 WHERE zcount>0", []);
    }

    echo '<br>Rohstoffe und Punkte fertig ';

    //sektorkollektoren
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_sector WHERE tempcol>0 AND npc=0", []);
    while ($row = mysqli_fetch_array($db_daten)) {
        $sec_id = $row["sec_id"];
        $col = $row["col"];
        $ekey = $row["ekey"];
        $techs = $row['techs'];

        //auf spezialisierung �berpr�fen
        $db_datenspec = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec5=3", [$sec_id]);
        $specseccol = mysqli_num_rows($db_datenspec) * 10;
        if ($specseccol > 100) {
            $specseccol = 100;
        }

        $col = $col + $specseccol;

        //ekey aufsplitten
        $hv = explode(";", $ekey);
        $keym = (float)($hv[0] ?? 0);
        $keyd = (float)($hv[1] ?? 0);
        $keyi = (float)($hv[2] ?? 0);
        $keye = (float)($hv[3] ?? 0);


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
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE sec_id=?", [$rm, $rd, $ri, $re, $sec_id]);
    }
    echo date("d/m/Y - H:i:s");

    //sektorartefaktaktionen durchführen
    if (!isset($sv_deactivate_sectorartefacts)) {
        $sv_deactivate_sectorartefacts = 0;
    }
    if ($sv_deactivate_sectorartefacts != 1) {
        include_once "wt_artefakte.php";
    }

    //Tick hochzählen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET tick = tick + 1", []);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET wt=wt+1", []);


    //alte nachrichten l�schen
    $tis = time() - (86400 * $sv_nachrichten_deldays);
    $datum = date("YmdHis", $tis);
    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_news where time < ?", [$datum]);

    //alte hyperfunk l�schen
    $tis = time() - (86400 * $sv_hf_deldays);
    $datum = date("YmdHis", $tis);
    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper where time < ? and archiv=0", [$datum]);

    //inaktive accounts 24 stunden vor l�schung per mail benachrichtigen
    //emaitext
    $betreff = $wt_lang['inaktivenmailbetreff'].$sv_server_tag.' - '.$sv_server_name;
    $emailtext = $wt_lang['inaktivenmailbody'];

    $tis = time() - (86400 * ($sv_inactiv_deldays - 1));
    $datum = date("Y-m-d H:i:s", $tis);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.user_id, de_login.reg_mail FROM de_login, de_user_data WHERE de_login.last_login < ? AND de_login.last_ip<>'127.0.0.1' AND de_user_data.user_id=de_login.user_id AND inaktmail = 0 AND de_login.status=1", [$datum]);
    while ($row = mysqli_fetch_array($db_daten)) {
        $uid = $row["user_id"];
        $reg_mail = $row["reg_mail"];

        @mail_smtp($reg_mail, $betreff, $emailtext);
        //damit er nur einmal die mail bekommt inaktmail auf 1 setzen
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET inaktmail = 1 WHERE user_id = ?", [$uid]);
    }

    //inaktive accounts l�schen
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT dodelinactiv FROM de_system", []);
    $row = mysqli_fetch_array($result);
    $dodel = $row["dodelinactiv"];

    if ($dodel == 1) {
        //�berpr�fen ob es account im l�sch-umode gibt und diese mit kicken
        $tis = time();
        $datum = date("Y-m-d H:i:s", $tis);
        $time = strftime("%Y%m%d%H%M%S");
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_login='0000-00-00 00:00:00' WHERE last_login < ? AND status=3 AND delmode=1", [$datum]);


        //inaktive account suchen
        $tis = time() - (86400 * $sv_inactiv_deldays);
        $datum = date("Y-m-d H:i:s", $tis);
        $time = strftime("%Y%m%d%H%M%S");

        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.user_id, de_login.nic, de_login.last_login, de_login.status, de_login.delmode, de_user_data.spielername, de_user_data.col, de_user_data.sector, de_user_data.`system` FROM de_login, de_user_data WHERE de_login.last_login < ? AND de_user_data.npc < 1 AND de_user_data.user_id=de_login.user_id", [$datum]);

        while ($row = mysqli_fetch_array($db_daten)) {
            $uid = $row["user_id"];
            $sector = $row["sector"];
            $system = $row["system"];
            $delmode = $row["delmode"];
            $status = $row["status"];
            $spielername = $row["spielername"];
            $col = $row["col"];

            //votetimer/votecounter f�r den sektor setzen
            mt_srand((float)microtime() * 10000);
            $votetimer = mt_rand(16, 96);
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET votetimer=? WHERE sec_id=?", [$votetimer, $sector]);

            //account l�schen, oder in den umode schicken
            if ($sector > 0 and $delmode == 0 and $status == 1) {//spieler kommt in den umode und wird nach 0:0 verschoben um danach wieder in Sektor 1 zu landen
                $urltage = 4000;
                $tis = time() + 86400 * $urltage;
                $datum = date("Y-m-d H:i:s", $tis);
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_login=?, status=3, delmode=2 WHERE user_id=?", [$datum, $uid]);

                //den account danach in sektor 1 stecken
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET sector=0, system=0, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, votefor=0, secstatdisable=0 WHERE user_id = ?", [$uid]);

                //dem sektor die kollektoren gutschreiben
                if ($col > 75) {
                    $col = 75;
                }
                if ($col > 0 and $sector > 1) {
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET col=col+? WHERE sec_id=?", [$col, $sector]);
                    //info in die sektorhistorie packen - sektorkollektoren
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '4', ?, ?)", [$maxtick, $sector, $col]);
                }

                //info in die sektorhistorie packen - komplette spielerl�schung
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '3', ?, ?)", [$maxtick, $sector, $spielername]);

                //wenn er BK war, den Posten zur�cksetzen
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET bk=0 WHERE sec_id=? AND bk=?", [$sector, $system]);
            } else { //spieler löschen
                //mail an den spieler
                $betreff = $wt_lang['loeschmailbetreff'].$sv_server_tag.' - '.$sv_server_name;
                $emailtext = $wt_lang['loeschmailbody'];
                @mail_smtp($reg_mail, $betreff, $emailtext);

                //sollten noch flotten zum account unterwegs sein diese zur�ckschicken
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_fleet WHERE zielsec=? AND zielsys=? AND zielsec > 0", [$sector, $system]);
                while ($rowx = mysqli_fetch_array($result)) {
                    $fleet_id = $rowx["user_id"];
                    $hv = explode("-", $fleet_id);
                    $ruid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
                    $flottennummer = $hv[1];

                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet set zielsec=hsec, zielsys=hsys, aktion=3, zeit = gesrzeit-zeit+1, aktzeit=0, entdeckt=0 WHERE user_id=?", [$fleet_id]);
                    //nachricht an den account schicken

                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$ruid, $time, "Im Zielsystem kam es zu einer kosmischen Katastrophe und Flotte Nr. $flottennummer kehrte um."]);
                    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = ?", [$ruid]);
                }

                $result_user = mysqli_execute_query($GLOBALS['dbi'], "Select allytag FROM de_user_data WHERE user_id=?", [$uid]);
                // Ermitteln des Clantags des Users
                $row_user = mysqli_fetch_array($result_user);
                $clantag = $row_user["allytag"] ?? '';
                //Laden des Allianzdatensatzes
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE allytag=?", [$clantag]);
                // Ermitteln der ID�s des Leaders und der Coleader
                $ally_row = mysqli_fetch_array($result);
                $leaderid = $ally_row["leaderid"] ?? -1;
                $coleaderid1 = $ally_row["coleaderid1"] ?? -1;
                $coleaderid2 = $ally_row["coleaderid2"] ?? -1;
                // Pr�fen, ob der Allianzleader seinen Account l�schen will
                if ($uid == $leaderid) {
                    //Pr�fen, ob im Feld coleaderid1 ein g�ltiger User eingetragen ist
                    if ($coleaderid1 > -1) {
                        //Coleader1 zum Leader machen
                        $result_updateally = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET leaderid=?, coleaderid1=-1 WHERE allytag=?", [$coleaderid1, $clantag]);
                    }
                    //Wenn in coleaderid1 kein g�ltiger User eingetragen ist pr�fen, ob im Feld coleaderid2 ein g�ltiger User eingetragen ist
                    elseif ($coleaderid2 > -1) {
                        //Coleader2 zum Leader machen
                        $result_updateally = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET leaderid=?, coleaderid2=-1 WHERE allytag=?", [$coleaderid2, $clantag]);
                    }
                    //Falls kein Coleader im Allianzdatensatz eingetragen ist
                    else {
                        //Ermitteln des ersten Users der Allianz in der Datenbank
                        $result_userlist = mysqli_execute_query($GLOBALS['dbi'], "Select user_id FROM de_user_data WHERE (allytag=? AND user_id!=?) LIMIT 1", [$clantag, $uid]);
                        //Pr�fen, ob ein g�ltiges Resultset erzeugt werden konnte
                        if (mysqli_num_rows($result_userlist) > 0) {
                            //ID des zur�ckgelieferten Userdatensatzes ermitteln
                            $userlist_row = mysqli_fetch_array($result_userlist);
                            $newleaderid = $userlist_row["user_id"];
                            //User zum Allianzleader machen
                            $result_updateally = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET leaderid=? WHERE allytag=?", [$newleaderid, $clantag]);
                        }
                    }
                }
                //Pr�fen, ob Coleader1 seinen Account l�schen will
                elseif ($uid == $coleaderid1) {
                    //�nderung im Allianzdatensatz eintragen
                    $result_updateally = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET coleaderid1=-1 WHERE allytag=?", [$clantag]);
                }
                //Pr�fen, ob Coleader2 seinen Account l�schen will
                elseif ($uid == $coleaderid2) {
                    //�nderung im Allianzdatensatz eintragen
                    $result_updateally = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET coleaderid2=-1 WHERE allytag=?", [$clantag]);
                }


                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_login WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_data WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_info WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_hfn_buddy_ignore WHERE user_id=? or (sector=? and `system`=?)", [$uid, $sector, $system]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper WHERE empfaenger = ?", [$uid]);

                $fleet_id = $uid.'-0';
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
                $fleet_id = $uid.'-1';
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
                $fleet_id = $uid.'-2';
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
                $fleet_id = $uid.'-3';
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);

                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_build WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_comserver WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_news WHERE user_id=?", [$uid]);
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_scan WHERE user_id=?", [$uid]);

                //falls er SK war werden die votes entfernt
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET votefor=0 WHERE sector=? AND votefor=?", [$sector, $system]);
                //buddy liste anpassen
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_hfn_buddy_ignore WHERE user_id=? or (sector=? and `system`=?)", [$uid, $sector, $system]);
                //statistik leeren
                mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_stat WHERE user_id=?", [$uid]);

                //dem sektor die kollektoren gutschreiben
                if ($col > 75) {
                    $col = 75;
                }
                if ($col > 0 and $sector > 1) {
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET col=col+? WHERE sec_id=?", [$col, $sector]);
                    //info in die sektorhistorie packen - sektorkollektoren
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '4', ?, ?)", [$maxtick, $sector, $col]);
                }

                //info in die sektorhistorie packen - der spieler verl��t den sektor
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '3', ?, ?)", [$maxtick, $sector, $spielername]);

            }
        }

        //nicht aktivierte account nach 2 tagen l�schen
        $tis = time() - (86400 * $sv_not_activated_deldays);
        $datum = date("Y-m-d H:i:s", $tis);
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, nic, last_login FROM de_login WHERE status=0 AND last_login < ?", [$datum]);

        while ($row = mysqli_fetch_array($db_daten)) {
            $uid = $row["user_id"];
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_login WHERE user_id=?", [$uid]);
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_data WHERE user_id=?", [$uid]);
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_info WHERE user_id=?", [$uid]);

            $fleet_id = $uid.'-0';
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
            $fleet_id = $uid.'-1';
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
            $fleet_id = $uid.'-2';
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);
            $fleet_id = $uid.'-3';
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_fleet WHERE user_id=?", [$fleet_id]);

            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_build WHERE user_id=?", [$uid]);
            mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_news WHERE user_id=?", [$uid]);
        }
    }
    //--------------- pl�tze und r�nge vorbelegen begin --------------------------
    echo '<br><br>Plätze und Ränge bestimmen - A: '.date("d/m/Y - H:i:s");

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) FROM de_user_data WHERE npc=0 AND sector > 1", []);
    $row = mysqli_fetch_array($db_daten);
    $gesamtuser = $row[0];
    if ($gesamtuser == 0) {
        $gesamtuser = 1;
    }

    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.score, de_user_data.sector FROM de_user_data WHERE sector > 0 AND npc=0 ORDER BY score DESC", []);
    $rang_schritt = $gesamtuser * 0.042;
    $platz = 1;

    while ($row = mysqli_fetch_array($result)) {
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
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET platz=?, rang='24' WHERE user_id=?", [$platz, $uid]);
        } else {
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET platz=?, rang=? WHERE user_id=?", [$platz, $rang_nr, $uid]);
        }

        $platz++;
    }

    //npcs generell auf einen fixen wert setzen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET platz='9999', rang='24' WHERE npc=1", []);

    echo '<br>Plätze und Ränge bestimmen - E: '.date("d/m/Y - H:i:s").'<br>';
    //--------------- pl�tze und r�nge vorbelegen end --------------------------

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // die erhabenenpunkte berechnen
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.tick, de_user_data.sector, de_user_data.agent_lost, de_user_data.col, de_user_data.fleetscore, de_user_data.roundpoints FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0", []);

    echo "<br>$num Erhabenenpunkte berechnen<br>";
    $time = date("YmdHis");

    while ($irow = mysqli_fetch_array($res)) {
        $uid = $irow["user_id"];
        $fleetscore = $irow["fleetscore"];
        $col = $irow["col"];
        $roundpoints = $irow["roundpoints"];
        $tick = $irow["tick"];
        $sector = $irow["sector"];
        $agent_lost = $irow["agent_lost"];
        //errungenschaften auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT (ac1+ac2+ac3+ac4+ac5+ac6+ac7+ac8+ac9+ac10+ac11+ac12+ac13+ac14+ac15+ac16+ac17+ac18+ac19) AS wert FROM de_user_achievement WHERE user_id=?", [$uid]);
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $row = mysqli_fetch_array($db_daten);
            $achievements = $row["wert"];
        } else {
            $achievements = 0;
        }

        //die flottenerfahrung auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(komatt) AS komatt, SUM(komdef) AS komdef FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=?", [$uid.'-0', $uid.'-1', $uid.'-2', $uid.'-3']);
        $row = mysqli_fetch_array($db_daten);

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
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET ehscore=? WHERE user_id=?", [$ehscore, $uid]);
    }

    //in der ewigen Runde haben alle Spieler in Sektor 1 keine EH-Punkte
    if ($sv_ewige_runde == 1) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET ehscore=0 WHERE sector=1", []);
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
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data LEFT JOIN de_login ON (de_user_data.user_id=de_login.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY ehscore DESC LIMIT 2", []);
            $data = array();
            while ($row = mysqli_fetch_array($db_daten)) {
                $data[] = $row;
            }
            if ($data[0]['ehscore'] != $data[1]['ehscore']) {
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET eh_counter=eh_counter+1 WHERE user_id=?", [$data[0]['user_id']]);
            }

            ////////////////////////////////////////////////////////////////////////////
            // eh-reset
            ////////////////////////////////////////////////////////////////////////////
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE eh_counter>=? LIMIT 1", [$sv_eh_counter]);
            $num = mysqli_num_rows($db_daten);
            if ($num == 1) {
                $row = mysqli_fetch_array($db_daten);
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
				geworben=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0, ehlock=0, 
                ehscore=0, defenseexp=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, 
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
                    mysqli_execute_query($GLOBALS['dbi'], $sql[$i], []);
                }

                //info in die sektorhistorie packen - spieler verläßt den sektor
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '3', ?, ?)", [$maxtick, $sector, $spielername]);

                //mail bzgl. EH
                @mail_smtp($GLOBALS['env_admin_email'], $sv_server_tag.' Ewige Runde: Neuer EH: user_id: '.$user_id.' - Spielername: '.$spielername, ' ');

                //der ally den Sieg mit anrechnen
                $ally_id = get_player_allyid($user_id);
                if ($ally_id > 0) {
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET eh_gestellt_anz=eh_gestellt_anz+1 WHERE id=?", [$ally_id]);
                }

                //meldung an den Chat
                $meldung = ''.$spielername.' ist der neue ERHABENE.';

                //im Sektor die Sektorflotten verkleiner
                $meldung .= ' Es wurden 2% der Sektorschiffe und 1% der Sektorkollektoren vernichtet.';

                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET e1=e1*0.98, e2=e2*0.98, col=col*0.99", []);

                //anderen allianzen etwas zerstören, außer dem meta-partner
                if ($ally_id > 0 && mt_rand(1, 100) > 75) {
                    $ally_id_partner = get_allyid_partner($ally_id);
                    $r = mt_rand(1, 4);
                    switch ($r) {
                        case 1:
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET bldg1=bldg1-1 WHERE bldg1>0 AND id<>? AND id<>?", [$ally_id, $ally_id_partner]);
                            $geb_name = 'Diplomatiezentrum';
                            break;
                        case 2:
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET bldg3=bldg3-1 WHERE bldg3>0 AND id<>? AND id<>?", [$ally_id, $ally_id_partner]);
                            $geb_name = 'Leitzentrale Feuroka';
                            break;
                        case 3:
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET bldg4=bldg4-1 WHERE bldg4>0 AND id<>? AND id<>?", [$ally_id, $ally_id_partner]);
                            $geb_name = 'Leitzentrale Bloroka';
                            break;
                        case 4:
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET bldg5=bldg5-1 WHERE bldg5>0 AND id<>? AND id<>?", [$ally_id, $ally_id_partner]);
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
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data LEFT JOIN de_login ON (de_user_data.user_id=de_login.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY ehscore DESC LIMIT 2", []);
            $data = array();
            while ($row = mysqli_fetch_array($db_daten)) {
                $data[] = $row;
            }
            if ($data[0]['ehscore'] != $data[1]['ehscore']) {
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET eh_counter=eh_counter+1 WHERE user_id=?", [$data[0]['user_id']]);
            }

            ////////////////////////////////////////////////////////////////////////////
            // eh-reset
            ////////////////////////////////////////////////////////////////////////////
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE eh_counter>=? LIMIT 1", [$sv_eh_counter]);
            $num = mysqli_num_rows($db_daten);
            if ($num == 1) {
                $row = mysqli_fetch_array($db_daten);
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
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET domtick=0, doetick=0", []);
                    $erhabenenstop = 1;

                    //mail an den detverteiler, wenn es ein bezahlserver ist
                    //da pde-server wegfallen immer eine e-mail schicken
                    //if($sv_pcs_id>0)
                    @mail_smtp($GLOBALS['env_admin_email'], 'Die Runde auf '.$sv_server_tag.' ist vorbei.', 'Die Runde auf '.$sv_server_tag.' ist vorbei.');

                    //die rundenpunkte verteilen
                    //der erhabene bekommt 1 extra
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE user_id=? AND npc=0", [$user_id]);

                    //den rang des spielers in der DB updaten
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET rang = 0 WHERE user_id = ?", [$user_id]);
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
					geworben=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0, ehlock=0, 
					ehscore=0, defenseexp=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, 
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
                        mysqli_execute_query($GLOBALS['dbi'], $sql[$i], []);
                    }

                    //info in die sektorhistorie packen - spieler verläßt den sektor
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, '3', ?, ?)", [$maxtick, $sector, $spielername]);

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
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
        $row = mysqli_fetch_array($db_daten);
        $maxtick = $row["tick"];
        if ($maxtick < 2500000) {
            //$sv_winscore wird in zukunft die rundendauer in ticks angeben
            $score = $maxtick;

            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            //rundenpunkte und creditgewinne beim start des eh-kampfes verteilen
            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            if ($score >= $sv_winscore and $roundpointsflag == 0) {
                //info bzgl. start eh-kampf
                @mail_smtp($GLOBALS['env_admin_email'], 'Bei der Runde auf '.$sv_server_tag.' hat der Erhabenenkampf begonnen.', 'Bei der Runde auf '.$sv_server_tag.' hat der Erhabenenkampf begonnen.');

                //flag setzen, dass nur einmal pro runde die verteilung stattfindet
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET roundpointsflag=1", []);


                //rundenpunkte zuerst
                //jeder bekommt einen
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE npc=0", []);
                //die alphas bekommen zus�tzlich einen
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE rang=1 AND npc=0", []);
            }

            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            // auf den erhabenen prüfen
            //////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////
            if ($score >= $sv_winscore) {
                //testen wer erhabener ist
                $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.ehscore, de_user_data.user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) where de_login.status=1 AND de_user_data.ehlock < ? ORDER BY ehscore DESC LIMIT 1", [$maxtick]);
                $row = mysqli_fetch_array($result);
                $uid = $row["user_id"];

                //er ist erhabener
                if ($winid == $uid) {
                    //er war auch letzten tick schon erhabener
                    if ($winticks == 1) {
                        //er hat gewonnen, server anhalten
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET domtick=0, doetick=0", []);
                        $erhabenenstop = 1;

                        //mail bei Rundenende
                        @mail_smtp($GLOBALS['env_admin_email'], 'Die Runde auf '.$sv_server_tag.' ist vorbei.', 'Die Runde auf '.$sv_server_tag.' ist vorbei.');

                        //die rundenpunkte verteilen
                        //der erhabene bekommt 1 extra
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET roundpoints=roundpoints+1 WHERE user_id=? AND npc=0", [$uid]);
                    }

                    //verbleibenden ticks bis zum sieg um eins verringern
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET winticks=winticks-1", []);
                    $winticks--;
                } else {
                    //es gibt einen neuen erhabenen
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET winid = ?, winticks=?", [$uid, $sv_benticks]);
                    $winticks = $sv_benticks;
                    $winid = $uid;
                }
                //den rang des spielers in der dp updaten
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET rang = 0 WHERE user_id = ?", [$uid]);
            } else {
                //evtl. erhabenen l�schen
                mysqli_execute_query($GLOBALS['dbi'], "update de_system set winid = 0, winticks=0", []);
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
    //lege in der datenbank die zeit des letzten Wirtschafsticks ab
    //////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    $sql = "UPDATE de_system set lasttick = ?";
    mysqli_execute_query($GLOBALS['dbi'], $sql, [date("Y-m-d H:i:s")]);


    //////////////////////////////////////////////////////////////////////
    //rausvoten
    //////////////////////////////////////////////////////////////////////

    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector_voteout SET ticks = ticks - 1", []);
    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_sector_voteout WHERE ticks<=0", []);

    //cron - sachen die zu einem bestimmten zeitpunkt bearbeitet werden müssen
    include_once "wt_cron.php";


    //tick wieder aktivieren nachdem alles abgearbeitet worden ist, außer es gibt einen Erhabenen
    if ($erhabenenstop != 1) {
        $sql = "UPDATE de_system set doetick=1";
        echo $sql;
        mysqli_execute_query($GLOBALS['dbi'], $sql, []);
    } else { //die runde ist zu ende
        //verteilungsflag zurücksetzen
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET roundpointsflag=0", []);

        //email mit gewinnerdaten verschicken
        //spieler - name, koordinaten, kollektoren, punkte, rasse, rundelaufzeit
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data LEFT JOIN de_login ON (de_login.user_id=de_user_data.user_id) WHERE de_user_data.rang=0 LIMIT 1", []);
        $row = mysqli_fetch_array($db_daten);
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

        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
        $row = mysqli_fetch_array($db_daten);
        $ranglistendaten .= "Rundenlaufzeit: ".$row["tick"]."\n\n\n";
        $round_wt = $row['tick'];


        //sektor - sektor, name, punkte
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_sector WHERE sec_id>1 AND npc=0 AND platz>0 OR sec_id=5 ORDER BY platz ASC LIMIT 1", []);
        $row = mysqli_fetch_array($db_daten);
        $sec_id = $row["sec_id"];
        $ranglistendaten .= "Sektor: ".$row["sec_id"]."\n".
        "Name: ".$row["name"]."\n";
        $sector_id = $row['sec_id'];
        $sector_name = $row['name'];

        //Sektorpunkte
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(score) AS gespunkte FROM de_user_data WHERE sector=?", [$sec_id]);
        $row = mysqli_fetch_array($db_daten);
        $ranglistendaten .= "Punkte: ".$row["gespunkte"]."\n\n\n";
        $sector_score = $row['gespunkte'];

        //allianz siegartefakte
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, allytag, questpoints FROM de_allys ORDER BY questpoints DESC, id ASC LIMIT 1", []);
        $row = mysqli_fetch_array($db_daten);
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
        mysqli_query($GLOBALS['dbi'], $sql);

        $rundenNummer=mysqli_insert_id($GLOBALS['dbi']);

        //für den Erhabenen einen Titel erzeugen und für seine owner_id im Account hinterlegen
        createTitleForUser($player_owner_id, '['.$sv_server_tag.'] ERHABENE/R - Runde '.$rundenNummer);

        //überprüfen ob es einen automatischen reset geben soll
        if ($sv_auto_reset == 1) {
            include_once "wt_auto_reset.php";
            //ticks wieder starten
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET doetick=1, domtick=1", []);
        }
    }

    //soll die Karte neu generiert werden?
    include_once 'wt_create_map.php';

    print '<br><br>Letzter Tick: '.date("d/m/Y - H:i:s");

} else {
    echo '<br>Wirtschaftsticks deaktiviert.<br>';
} //doetick

?>
</body>
</html>
<?php
$log_content = ob_get_clean();
echo $log_content;
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/wt_' . date("Ymd") . '.log';
$log_entry = "\n" . str_repeat('=', 80) . "\n";
$log_entry .= "WT Tick: " . date("Y-m-d H:i:s") . "\n";
$log_entry .= str_repeat('=', 80) . "\n" . $log_content . "\n";
$result = file_put_contents($log_file, $log_entry, FILE_APPEND);
if ($result === false) {
    error_log("WT: Konnte Log-Datei nicht schreiben: " . $log_file);
}
