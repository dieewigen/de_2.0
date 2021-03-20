<?php
include "soudata/defs/resources.inc.php";
echo '<br>';

rahmen0_oben();
echo '<br>';

rahmen2_oben();

//überprüfen ob es hier ein fundstück gibt
$db_daten=mysql_query("SELECT * FROM sou_map_find WHERE x='$player_x' AND y='$player_y' LIMIT 1",$soudb);
$anzahl_find = mysql_num_rows($db_daten);
if($anzahl_find>0)
{
  $row = mysql_fetch_array($db_daten);
  $find_id=$row['id'];
  
  //überprüfen ob er ein bergungsmodul hat
  $db_daten=mysql_query("SELECT MAX(canrecover) AS canrecover FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
  $row = mysql_fetch_array($db_daten);
  $canrecover=$row["canrecover"];

  if($canrecover>0)
  {
    //überprüfen welche spezialrohstoffe es an der position gibt
    unset($availableres);
    for($i=0;$i<count($specialres_def);$i++)
    {
	  if(specialres_is_available($i)==1)$availableres[]=$i;
    }
  
    //von den verfügbaren rohstoffen einen per zufall auswählen
    $w=mt_rand(0, count($availableres)-1);
    
    //per zufall bestimmen wie viel man bekommt
    $getanz=mt_rand(1, 10);
    
    $dbfeld=$specialres_def[$w][0];
    //benötigte bergungskapazität
    $needbk=5000*($w+1);
    
    //überprüfen, ob das bergungsmodul gut genug ist
    if($canrecover>=$needbk)
    {
      mysql_query("UPDATE sou_user_data SET $dbfeld=$dbfeld+$getanz, foundfind=foundfind+1 WHERE user_id='$player_user_id'",$soudb);
      
 	  echo 'Du findest folgendes: '.$getanz.'x '.$specialres_def[$w][1];
      //find löschen
  	  mysql_query("DELETE FROM sou_map_find WHERE id='$find_id'",$soudb);
      
      echo '<br><br><a href="sou_main.php?action=systempage" class="btn">weiter</a><br><br>';
    }
    else echo '<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Ben&ouml;tigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK <br><br><a href="sou_main.php?action=systempage" class="btn">weiter</a><br><br>';
  }
  else echo '<font color="FF0000">Ohne Bergungsmodul ist kein Zugriff m&ouml;glich.<br><br><a href="sou_main.php?action=systempage" class="btn">weiter</a><br><br>';
}
else echo '<font color="FF0000">Hier gibt es nichts zu bergen.<br><br><a href="sou_main.php?action=systempage" class="btn">weiter</a><br><br>';

rahmen2_unten();
echo '<br>';

rahmen0_unten();
echo '<br>';
  
?>