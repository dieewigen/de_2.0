<?php
include "../inccon.php";
include "../functions.php";
include "det_userdata.inc.php";

$page_title = 'Allianz-Listen';
include "inc.layout.top.php";

$Tab1 = "";
$Tab2 = "";
$FCounter = array(0, 0); // Initialisierung des Zähler-Arrays
$tagliste = array();

$DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
        or die("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

while ($AData = mysqli_fetch_assoc($DBData)) {
    //Anzahl der Bündnisse
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_ally_partner.ally_id_1) AS count_allies FROM de_ally_partner WHERE de_ally_partner.ally_id_1=? OR de_ally_partner.ally_id_2=?", [$AData["id"], $AData["id"]]);
    $row_count = mysqli_fetch_assoc($result);
    $iAnz = $row_count['count_allies'];
    $allytag = $AData["allytag"];

    //Mitgliederanzahl
    $member_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_user_data.allytag) AS member_count FROM de_user_data WHERE allytag=? AND status=1", [$allytag]);
    $member_row = mysqli_fetch_assoc($member_result);
    $mAnz = $member_row['member_count'];
    if ($iAnz > 2) {
        $sC = ' class="num r"';
    } else {
        $sC = ' class="num"';
    }

    //Anzahl geworbener Spieler
    $geworben = 0;
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE allytag=? AND status=1", [$allytag]);
    while ($rowx = mysqli_fetch_assoc($result)) {
        $uid = $rowx['user_id'];
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT owner_id FROM de_login WHERE user_id=?", [$uid]);
        $row = mysqli_fetch_assoc($db_daten);
        $owner_id = intval($row["owner_id"]);

        $geworben += getAnzahlGeworbeneSpielerByOwnerid($owner_id);
    }

    //Ausgabe zusammenbauen
    $Tab2 .= "  <tr><td>" . $AData["id"] . "</td><td>" . htmlspecialchars((string)$AData["allyname"]) . "</td><td>" . htmlspecialchars((string)$AData["allytag"]) . "</td><td" . $sC . ">" . $iAnz . "</td><td class=\"num\">" . $mAnz . "</td><td class=\"num\">" . $geworben . "</td></tr>\r\n";
    $FCounter[1]++;
    $tagliste[] = $AData["allytag"];
}
?>
 <b>DE-Allianzen</b> (Gefunden: <?php echo intval($FCounter[1]); ?>) <br><br>
 <table>
  <tr><th>ID</th><th>Allyname</th><th>AllyTag</th><th>B&uuml;ndnisse</th><th>Mitglieder</th><th>geworben</th></tr>
<?php echo $Tab2; ?>
 </table>
 <br><br>

<?php
for ($i = 0; $i < count($tagliste); $i++) {
    $clankuerzel = $tagliste[$i];

    echo '<h3>Mitgliederliste der Allianz: ' . htmlspecialchars((string)$tagliste[$i]) . '</h3>';
    echo '<table>';
    echo '<tr>' .
            '<th>User-ID</th>' .
            '<th>Name</th>' .
            '<th>Status</th>' .
            '<th>Letzte Aktivit&auml;t</th>' .
            '<th>Kollektoren</th>' .
            '<th>Punkte</th>' .
            '<th>Koordinaten</th>';
    echo '</tr>';

    $query = "SELECT * FROM de_user_data WHERE status='1' AND allytag=? ORDER BY sector, system";

    $result = mysqli_execute_query($GLOBALS['dbi'], $query, [$clankuerzel]);

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $nb = count($rows);

    $row = 0;

    while ($row < $nb) {
        $userid = $rows[$row]["user_id"];
        $sector = $rows[$row]["sector"];
        $system = $rows[$row]["system"];
        $score = $rows[$row]["score"];
        $kollies = $rows[$row]["col"];
        $spielername = $rows[$row]["spielername"];
        $sectorjump = explode(":", $sector);
        $sectorjump = $sectorjump[0];

        $login_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_login WHERE user_id=?", [$userid]);
        $logindata = mysqli_fetch_assoc($login_result);

        echo "<tr>" .
            "<td><a href=\"idinfo.php?UID=" . $logindata['user_id'] . "\" target=\"_blank\" rel=\"noopener\">" . $logindata['user_id'] . "</a></td>" .
            "<td>" . htmlspecialchars((string)$spielername) . "</td>" .
            "<td>" . $logindata['status'] . "</td>" .
            "<td>" . $logindata['last_click'] . "</td>" .
            "<td class=\"num\">" . $kollies . "</td>" .
            "<td class=\"num\">" . number_format($score, 0, '', '.') . "</td>" .
            "<td align=\"center\">" . $sector . ":" . $system . "</td>";
        echo "</tr>";
        $row++;
    }

    echo "</table>";
}


$Tab1 = "";
$Tab2 = "";

$DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.* FROM de_allys LEFT JOIN de_login ON (de_allys.leaderid = de_login.user_id) WHERE (de_login.nic) Is Null ORDER BY de_allys.id")
          or die("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

while ($AData = mysqli_fetch_assoc($DBData)) {
    $count_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_user_data.allytag) AS count_tags FROM de_user_data WHERE de_user_data.allytag=?", [$AData["allytag"]]);
    $count_row = mysqli_fetch_assoc($count_result);
    $iAnz = $count_row['count_tags'];
    if ($iAnz == 0) {
        $sC = ' class="num r"';
    } else {
        $sC = ' class="num"';
    }
    if ($AData["coleaderid1"] == -1) {
        $Co1 = "---";
    } else {
        $Co1 = $AData["coleaderid1"];
    }
    if ($AData["coleaderid2"] == -1) {
        $Co2 = "---";
    } else {
        $Co2 = $AData["coleaderid2"];
    }
    $Tab1 .= "  <tr><td>" . $AData["id"] . "</td><td>" . htmlspecialchars((string)$AData["allyname"]) . "</td><td>" . htmlspecialchars((string)$AData["allytag"]) . "</td><td>" . $Co1 . "</td><td>" . $Co2 . "</td><td" . $sC . ">" . $iAnz . "</td></tr>\r\n";
    $FCounter[0]++;
}

$DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
          or die("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

while ($AData = mysqli_fetch_assoc($DBData)) {
    $ally_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_ally_partner.ally_id_1) AS count_partners FROM de_ally_partner WHERE de_ally_partner.ally_id_1=? OR de_ally_partner.ally_id_2=?", [$AData["id"], $AData["id"]]);
    $ally_row = mysqli_fetch_assoc($ally_result);
    $iAnz = $ally_row['count_partners'];
    if ($iAnz > 2) {
        $sC = ' class="num r"';
    } else {
        $sC = ' class="num"';
    }
    $Tab2 .= "  <tr><td>" . $AData["id"] . "</td><td>" . htmlspecialchars((string)$AData["allyname"]) . "</td><td>" . htmlspecialchars((string)$AData["allytag"]) . "</td><td" . $sC . ">" . $iAnz . "</td></tr>\r\n";
    $FCounter[1]++;
}
?>

<b>DE-Allianzen ohne Leader</b> (Gefunden: <?php echo intval($FCounter[0]); ?>) <br><br>
 <table>
  <tr><th>ID</th><th>Allyname</th><th>AllyTag</th><th>Co1</th><th>Co2</th><th>Member</th></tr>
<?php echo $Tab1; ?>
 </table>
 <br><br>

 <b>DE-Allianzen mit Leader</b> (Gefunden: <?php echo intval($FCounter[1]); ?>) <br><br>
 <table>
  <tr><th>ID</th><th>Allyname</th><th>AllyTag</th><th>Bündnisse</th></tr>
<?php echo $Tab2; ?>
 </table>

<?php
include "inc.layout.bottom.php";
