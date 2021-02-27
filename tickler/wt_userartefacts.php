<?php
//mit fügt mit einer chance von x prozent x-y userartefakte in den handel ein, wenn es für die artefakte kein angebot gibt
//das ganze passiert jedoch nur dann, wenn mindestens x% der spieler einen cyborg haben

/*
//gesamtspielerzahl
$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE npc=0",$db);
$gesamzanzahl = mysql_num_rows($db_daten);
//spieler mit cyborg
$db_daten=mysql_query("SELECT user_id FROM de_cyborg_data WHERE 1",$db);
$cyborganzahl = mysql_num_rows($db_daten);

//wenn genug spieler mit cyborg vorhandne sind anfangen
if($cyborganzahl>=$gesamanzahl/10)
{
  //alle artefakte durchgehen
  for($i=1;$i<=7;$i++)
  {
    //wenn w zutrifft, dann schauen ob man einfügen muß
    if(mt_rand(1,100)<=1)
    {
      $db_daten=mysql_query("SELECT id FROM de_trade_artefact WHERE id='$i' AND level=1",$db);
      $anz = mysql_num_rows($db_daten);
      //wenn keine artefakte vorhanden sind x artefakte einfügen
      if($anz==0)
      {
        $anz=mt_rand(1,2);
        for($j=1;$j<=$anz;$j++)
        {
          mysql_query("INSERT INTO de_trade_artefact (id, level) VALUES ('$i', 1)",$db);
        }
      }
    }
  }
}
*/
?>
