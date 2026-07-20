<?php
include "../inccon.php";
include "../functions.php";
include "det_userdata.inc.php";

$uid = req_int('uid');

$page_title = 'Technologien';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

if ($uid > 0) {
    // Techs am Anfang laden, damit sie für alle Bereiche verfügbar sind
    $ztechs = loadPlayerTechs($uid);

    echo '<h4>Schiffs&uuml;bersicht</h4>';
    //zaehle alle schiffe, die schon vorhanden sind - anfang
    $fid0 = $uid . '-0';
    $fid1 = $uid . '-1';
    $fid2 = $uid . '-2';
    $fid3 = $uid . '-3';

    // Initialisiere Array für Schiffswerte
    $ec_ships = array();
    for ($i = 81; $i <= 99; $i++) {
        $ec_ships[$i] = 0;
    }

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
    while ($row = mysqli_fetch_array($db_daten)) {
        for ($i = 81; $i <= 90; $i++) {
            $ec_ships[$i] += isset($row["e$i"]) ? $row["e$i"] : 0;
        }
    }
    //zaehle alle schiffe, die schon vorhanden sind - ende

    //lade einheitentypen
    echo '<table class="w320">';
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_id, tech_name, tech_vor FROM de_tech_data1 WHERE tech_id>80 AND tech_id<100");
    while ($row = mysqli_fetch_array($db_daten)) {
        if ($row["tech_id"] <> 86) {
            $tech_id = $row["tech_id"];
            $ec = isset($ec_ships[$tech_id]) ? $ec_ships[$tech_id] : 0;
            echo '<tr><td>' . $row["tech_name"] . '</td><td class="num">' . $ec . '</td></tr>';
        }
    }
    echo '</table>';

    echo '<h4>Verteidigungsanlagen</h4>';
    //zaehle alle verteidigungsanlagen, die schon vorhanden sind - anfang
    $ec_defense = array();
    for ($i = 100; $i <= 109; $i++) {
        $ec_defense[$i] = 0;
    }

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=?", [$uid]);
    while ($row = mysqli_fetch_array($db_daten)) {
        for ($i = 100; $i <= 104; $i++) {
            $ec_defense[$i] += isset($row["e$i"]) ? $row["e$i"] : 0;
        }
    }
    //zaehle alle verteidigungsanlagen, die schon vorhanden sind - ende

    //lade einheitentypen
    echo '<table class="w320">';
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_id, tech_name, tech_vor FROM de_tech_data1 WHERE tech_id>99 AND tech_id<110 ORDER BY tech_ticks");
    while ($row = mysqli_fetch_array($db_daten)) {
        $tech_id = $row["tech_id"];
        $ec = isset($ec_defense[$tech_id]) ? $ec_defense[$tech_id] : 0;
        echo '<tr><td>' . $row["tech_name"] . '</td><td class="num">' . $ec . '</td></tr>';
    }
    echo '</table>';

    echo '<h4>Entwicklungen</h4>';
    echo '<table class="w320">';
    for ($i = 1; $i < 500; $i++) {
        if (hasTech($ztechs, $i)) {
            $db_tech = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=?", [$i]);
            $row_techcheck = mysqli_fetch_array($db_tech);

            echo '<tr>';
            echo '<td>ID ' . $i . ': ' . str_replace(";", "<br>", $row_techcheck['tech_name']) . '</td><td>' . date("H:i:s d.m.Y", $ztechs[$i]['time_finished']) . '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
} else {
    echo 'Kein User ausgew&auml;hlt.';
}

include "inc.layout.bottom.php";
