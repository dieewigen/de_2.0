<?php
include "inc/header.inc.php";
//daten laden
$db_daten = mysql_query("SELECT sector, allytag FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$sector = $row["sector"];
$allytag = $row["allytag"];
//allyid auslesen
$db_datenx = mysql_query("SELECT id FROM de_allys WHERE allytag='$allytag'", $db);
$rowx = mysql_fetch_array($db_datenx);
$allyid = $rowx["id"];

//rundenstart auslesen
//allyid auslesen
$db_datenx = mysql_query("SELECT rundenstart_datum FROM de_system", $db);
$rowx = mysql_fetch_array($db_datenx);
$rundenstart_datum = $rowx["rundenstart_datum"];

//hintergund laden
$im = imagecreatefrompng("smilies/statvorl2.png");

//statistische daten auslesen
//spieler
if ($_GET["typ"] == 1) {
    $db_daten = mysql_query("SELECT score FROM de_user_stat WHERE user_id='$ums_user_id' AND datum>'$rundenstart_datum' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 2) {
    $db_daten = mysql_query("SELECT col FROM de_user_stat WHERE user_id='$ums_user_id' AND datum>'$rundenstart_datum' ORDER BY datum ASC", $db);
}

//sektor
elseif ($_GET["typ"] == 11) {
    $db_daten = mysql_query("SELECT score FROM de_sector_stat WHERE sec_id='$sector' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 12) {
    $db_daten = mysql_query("SELECT col FROM de_sector_stat WHERE sec_id='$sector' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 13) {
    $db_daten = mysql_query("SELECT platz FROM de_sector_stat WHERE sec_id='$sector' ORDER BY datum ASC", $db);
}
//allianz
elseif ($_GET["typ"] == 21) {
    $db_daten = mysql_query("SELECT score FROM de_ally_stat WHERE id='$allyid' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 22) {
    $db_daten = mysql_query("SELECT col FROM de_ally_stat WHERE id='$allyid' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 23) {
    $db_daten = mysql_query("SELECT platz FROM de_ally_stat WHERE id='$allyid' ORDER BY datum ASC", $db);
} elseif ($_GET["typ"] == 24) {
    $db_daten = mysql_query("SELECT member FROM de_ally_stat WHERE id='$allyid' ORDER BY datum ASC", $db);
}

$i = 0;
$wert[0] = 0;
$wert[1] = 0;
$maxwert = 1;
while ($row = mysql_fetch_array($db_daten)) {
    $i++;
    $wert[$i] = $row[0];
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
$color = ImageColorAllocate($im, $farben[$ums_rasse - 1][0], $farben[$ums_rasse - 1][1], $farben[$ums_rasse - 1][2]);

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
