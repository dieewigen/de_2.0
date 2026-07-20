<?php
include "../inccon.php";
include "det_userdata.inc.php";

$uid = req_int('uid');
$show = req_int('show');

$page_title = 'Kollektoren';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

echo '<p><a href="de_user_getcol.php?uid=' . $uid . '&amp;show=1">zeige alle Kollektoren die dem Spieler gestohlen wurden</a><br>
<a href="de_user_getcol.php?uid=' . $uid . '&amp;show=2">zeige alle Kollektoren die der Spieler gestohlen hat</a></p>';

//alle kollektoren die dem spieler gestohlen worden sind
if ($show == 1 && $uid) {
    echo '<table>';
    echo '<tr><th>Zeitpunkt</th><th>Kollektoren</th><th>Diebes-User-ID</th><th>Spielername</th><th>Aktuelle Allianz</th></tr>';
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_getcol WHERE zuser_id=?", [$uid]);
    while ($row = mysqli_fetch_array($db_daten)) {
        $time = date("Y-m-d H:i:s", $row["time"]);
        //spielername des diebes
        $duid = $row["user_id"];
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, status, allytag FROM de_user_data WHERE user_id=?", [$duid]);
        $num = mysqli_num_rows($result);
        if ($num == 1) {
            $rowx = mysqli_fetch_array($result);
            $spielername = $rowx["spielername"];
            if ($rowx['status'] == 1) $allytag = $rowx['allytag'];
            else $allytag = '';
        } else {
            $spielername = 'gelöscht';
            $allytag = '';
        }
        echo '<tr><td>' . $time . '</td><td class="num">' . $row["colanz"] . '</td><td><a href="idinfo.php?UID=' . $duid . '" target="_blank" rel="noopener">' . $duid . '</a></td><td>' . htmlspecialchars((string)$spielername) . '</td><td>' . htmlspecialchars((string)$allytag) . '</td></tr>';
    }
    echo '</table>';
}

//alle kollektoren die er selbst gestohlen hat
if ($show == 2 && $uid) {
    echo '<table>';
    echo '<tr><th>Zeitpunkt</th><th>Kollektoren</th><th>Erfahrungspunkte</th><th>Bestohlener-User-ID</th><th>Spielername</th><th>Aktuelle Allianz</th></tr>';
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_getcol WHERE user_id=?", [$uid]);
    while ($row = mysqli_fetch_array($db_daten)) {
        $time = date("Y-m-d H:i:s", $row["time"]);
        //spielername des bestohlenen
        $duid = $row["zuser_id"];
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, status, allytag FROM de_user_data WHERE user_id=?", [$duid]);
        $num = mysqli_num_rows($result);
        if ($num == 1) {
            $rowx = mysqli_fetch_array($result);
            $spielername = $rowx["spielername"];
            if ($rowx['status'] == 1) $allytag = $rowx['allytag'];
            else $allytag = '';
        } else {
            $spielername = 'gelöscht';
            $allytag = '';
        }
        echo '<tr><td>' . $time . '</td><td class="num">' . $row["colanz"] . '</td><td class="num">' . $row["getexp"] . '</td><td><a href="idinfo.php?UID=' . $duid . '" target="_blank" rel="noopener">' . $duid . '</a></td><td>' . htmlspecialchars((string)$spielername) . '</td><td>' . htmlspecialchars((string)$allytag) . '</td></tr>';
    }
    echo '</table>';
}

include "inc.layout.bottom.php";
