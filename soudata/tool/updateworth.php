<?php
include "../lib/sou_dbconnect.php";
include "../defs/buildings.inc.php";

//include "soudata/lib/sou_dbconnect.php";
//include "soudata/defs/buildings.inc.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Title</title>
</head>
<body bgcolor="#000000" text="#FFFFFF">
<?php

unset($kosten);
//die kosten aller gebäude, aller stufen berechnen
$gebaude=0;
for($gebaude=0;$gebaude<count($b_defs);$gebaude++)
{
  for($level=0;$level<50;$level++)
  {
    $need=explode(";",$b_defs[$gebaude][1][$level]);
    $stufenkosten=0;
    for($n=0;$n<count($need);$n++)
    {
      $einzelneed=explode("x",$need[$n]);
      //echo '<br>Gebäude '.$gebaude.' ('.$level.'): '.$einzelneed[0];
      $stufenkosten+=$einzelneed[0];
    }
    //echo '<br>Gebäude '.$gebaude.'('.$level.'): '.($kosten[$gebaude][$level-1]+$stufenkosten);
    $kosten[$gebaude][$level]=$kosten[$gebaude][$level-1]+$stufenkosten;
  }
}

echo '<hr>';

//alle sonnensystem laden die einer fraktion gehören
$db_daten=mysql_query("SELECT id FROM sou_map WHERE fraction>=1 and fraction <=6",$soudb);
$anz = mysql_num_rows($db_daten);
while($row = mysql_fetch_array($db_daten))
{
  //für jedes sonnensystem alle gebäude laden
  $id=$row["id"];
  $worth=0;
  $db_datenx=mysql_query("SELECT * FROM sou_map_buildings WHERE owner_id='$id'",$soudb);
  while($rowx = mysql_fetch_array($db_datenx))
  {
    //alle gebäude durchgehen und ihren wert berechnen
    if($rowx['b1']==99999999999)
    $worth+=$kosten[$rowx['bldg_id']][$rowx['level']];//+$rowx['b1']+$rowx['b2'];
    else  
    $worth+=$kosten[$rowx['bldg_id']][$rowx['level']]+$rowx['b1']+$rowx['b2'];
    
    //echo '<br>'.$id.':'.number_format($worth, 0,",",".");
  }
  echo '<br>'.number_format($worth, 0,",",".");
  mysql_query("UPDATE sou_map SET worth='$worth' WHERE id='$id'",$soudb);
}

echo $anz;

?>
