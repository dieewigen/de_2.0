<?php
include "inc/header.inc.php";
//daten laden
$sql = "SELECT sector, ally_id FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$sector = $row["sector"];
$ally_id = $row["ally_id"];

//rundenstart auslesen
$sql = "SELECT rundenstart_datum FROM de_system";
$db_datenx = mysqli_execute_query($GLOBALS['dbi'], $sql);
$rowx = mysqli_fetch_assoc($db_datenx);
$rundenstart_datum = $rowx["rundenstart_datum"];

//hintergund laden
$im = imagecreatefrompng("lib/statvorl2.png");

//statistische daten auslesen
//spieler
if ($_GET["typ"] == 1) {
    $sql = "SELECT score FROM de_user_stat WHERE user_id=? AND datum>? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $rundenstart_datum]);
} elseif ($_GET["typ"] == 2) {
    $sql = "SELECT col FROM de_user_stat WHERE user_id=? AND datum>? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $rundenstart_datum]);
}

//sektor
elseif ($_GET["typ"] == 11) {
    $sql = "SELECT score FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
} elseif ($_GET["typ"] == 12) {
    $sql = "SELECT col FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
} elseif ($_GET["typ"] == 13) {
    $sql = "SELECT platz FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
}
//allianz
elseif ($_GET["typ"] == 21) {
    $sql = "SELECT score FROM de_ally_stat WHERE id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ally_id]);
} elseif ($_GET["typ"] == 22) {
    $sql = "SELECT col FROM de_ally_stat WHERE id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ally_id]);
} elseif ($_GET["typ"] == 23) {
    $sql = "SELECT platz FROM de_ally_stat WHERE id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ally_id]);
} elseif ($_GET["typ"] == 24) {
    $sql = "SELECT member FROM de_ally_stat WHERE id=? ORDER BY datum ASC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ally_id]);
}

$i = 0;
$wert[0] = 0;
$wert[1] = 0;
$maxwert = 1;
while ($row = mysqli_fetch_assoc($db_daten)) {
    $i++;
    // Hole den ersten (und einzigen) Wert aus dem assoziativen Array
    $wert[$i] = reset($row); // reset() gibt den ersten Wert eines Arrays zurück
    if ($wert[$i] > $maxwert) {
        $maxwert = $wert[$i];
    }
}

//werte berechnen
for ($i = 0;$i < count($wert);$i++) {
    $wert[$i] = floor($wert[$i] * 100 / $maxwert) * 3;
}

//schrittweite berechnen
$schrittweite = floor(500 / (count($wert) - 1));

//farbe ausw�hlen
$farben[] = array(  51, 153, 255);
$farben[] = array( 166, 166, 166);
$farben[] = array( 222,  57,  57);
$farben[] = array(  57, 162,  54);
$color = ImageColorAllocate($im, $farben[$_SESSION['ums_rasse'] - 1][0], $farben[$_SESSION['ums_rasse'] - 1][1], $farben[$_SESSION['ums_rasse'] - 1][2]);

//statistik zeichnen
$x1 = 51;
$yw = 348;
for ($i = 1;$i < count($wert);$i++) {

    $x2 = $x1 + $schrittweite;

    $y1 = $yw - $wert[$i - 1];
    $y2 = $yw - $wert[$i];

    imageline($im, $x1, $y1, $x2, $y2, $color);
    $x1 = $x1 + $schrittweite;
}

header("Content-type: image/png");
imagePng($im);
