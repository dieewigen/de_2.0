<?php
include "../inccon.php";
include "../inc/sv.inc.php";

$ums_user_id=$uid;
$ums_rasse=1;

// Stelle sicher, dass eine Datenbankverbindung vorhanden ist
if (!isset($GLOBALS['dbi'])) {
    die('Keine Datenbankverbindung vorhanden');
}

//daten laden mit prepared statement
$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT sector, allytag FROM de_user_data WHERE user_id = ?", 
    [$ums_user_id]
);
$row = mysqli_fetch_array($result, MYSQLI_BOTH);
$sector=$row["sector"];$allytag=$row["allytag"];

//allyid auslesen mit prepared statement
$resultx = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT id FROM de_allys WHERE allytag = ?", 
    [$allytag]
);
$rowx = mysqli_fetch_array($resultx, MYSQLI_BOTH);
$allyid = isset($rowx["id"]) ? $rowx["id"] : 0;

//hintergund laden
$im = imagecreatefrompng("smilies/statvorl2.png");

//statistische daten auslesen mit prepared statements
$result = null;
$typ = isset($_GET["typ"]) ? (int)$_GET["typ"] : 1;

//spieler
if($typ == 1) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT score FROM de_user_stat WHERE user_id = ? ORDER BY datum ASC", 
        [$ums_user_id]
    );
} elseif($typ == 2) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT col FROM de_user_stat WHERE user_id = ? ORDER BY datum ASC", 
        [$ums_user_id]
    );
} elseif($typ == 3) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT cybexp FROM de_user_stat WHERE user_id = ? ORDER BY datum ASC", 
        [$ums_user_id]
    );
//sektor
} elseif($typ == 11) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT score FROM de_sector_stat WHERE sec_id = ? ORDER BY datum ASC", 
        [$sector]
    );
} elseif($typ == 12) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT col FROM de_sector_stat WHERE sec_id = ? ORDER BY datum ASC", 
        [$sector]
    );
} elseif($typ == 13) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT platz FROM de_sector_stat WHERE sec_id = ? ORDER BY datum ASC", 
        [$sector]
    );
//allianz
} elseif($typ == 21) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT score FROM de_ally_stat WHERE id = ? ORDER BY datum ASC", 
        [$allyid]
    );
} elseif($typ == 22) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT col FROM de_ally_stat WHERE id = ? ORDER BY datum ASC", 
        [$allyid]
    );
} elseif($typ == 23) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT platz FROM de_ally_stat WHERE id = ? ORDER BY datum ASC", 
        [$allyid]
    );
} elseif($typ == 24) {
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT member FROM de_ally_stat WHERE id = ? ORDER BY datum ASC", 
        [$allyid]
    );
}

$i=0;
$wert[0]=0;$wert[1]=0;
$maxwert=1;

// Überprüfe, ob ein gültiges Ergebnis vorliegt
if($result) {
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
      $i++;
      $wert[$i]=$row[0];
      if ($wert[$i]>$maxwert)$maxwert=$wert[$i];
    }
}

//werte berechnen
for ($i=0;$i<count($wert);$i++)
{
  $wert[$i]=floor($wert[$i]*100/$maxwert)*3;
}

//schrittweite berechnen
$schrittweite=floor(500/(count($wert)-1));

//farbe ausw�hlen
$farben[] = array (  51, 153, 255);
$farben[] = array ( 166, 166, 166);
$farben[] = array ( 222,  57,  57);
$farben[] = array (  57, 162,  54);
$color=ImageColorAllocate ($im, $farben[$ums_rasse-1][0], $farben[$ums_rasse-1][1], $farben[$ums_rasse-1][2]);

//statistik zeichnen
$x1=51;
$yw=348;
for ($i=1;$i<count($wert);$i++)
{

  $x2=$x1+$schrittweite;

  $y1=$yw-$wert[$i-1];
  $y2=$yw-$wert[$i];
  
  imageline ( $im, $x1, $y1, $x2, $y2, $color );
  $x1=$x1+$schrittweite;
}




header("Content-type: image/png");
imagePng($im);

?>

