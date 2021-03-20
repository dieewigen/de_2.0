<?php
//Die Aganra-Sonde
include "soudata/questdata/questname".$quest_id.".php";

//questdaten laden
$db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE fraction='$player_fraction' AND id='$quest_id'",$soudb);
$rowquest = mysql_fetch_array($db_daten);
$questlevel=$rowquest["questlevel"];

//überprüfen ob er ein bergungsmodul hat
$db_daten=mysql_query("SELECT MAX(canrecover) AS canrecover FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);
$canrecover=$row["canrecover"];

if($canrecover>0)//kein bergungsmodul notwendig
{
  //überprüfen, ob das bergungsmodul gut genug ist
  $needbk=15000;
  if($canrecover>=$needbk)
  {
	  //es sind alle voraussetzungen erfüllt
	  //questdatensatz locken
	  $result = mysql_query("UPDATE sou_frac_quests SET aupdate=1 WHERE aupdate=0 AND id='$quest_id'", $soudb);
      $num = mysql_affected_rows();
      if($num==1)
      {
          $done=1;
          
          //allen spielern der fraktion ein bonusmodul geben
		  $ai=0;
		  $angebot[$ai][name]='Bergbaubonus Aganra';
		  $angebot[$ai][buff]='1x4x30';
		  $angebot[$ai][mapbuff]='';
		  $angebot[$ai][quality]=1;
          
		  $db_daten=mysql_query("SELECT user_id FROM `sou_user_data` WHERE fraction='$player_fraction'",$soudb);
		  while($row = mysql_fetch_array($db_daten))
		  {
            $user_id=$row[user_id];
            
            
            $sql="INSERT INTO sou_ship_module (user_id, fraction, name, craftedby, lifetime, hasspace, needspace, needenergy, giveenergy, canmine, 
            givelife, givesubspace, givecenter, givehyperdrive, giveresearch, location, uomlock, quality, buff, mapbuff) VALUES 
            ('$user_id', '$player_fraction', '".$angebot[$ai][name]."', 'Questbelohnung', '0', '0' , '0', '0', '0', '0', '0', '0', '0', '0', '0', 1,
             '1', '".$angebot[$ai][quality]."', '".$angebot[$ai][buff]."', '".$angebot[$ai][mapbuff]."')";
          
            //echo '<br><br>'.$sql.'<br><br>';
            mysql_query($sql,$soudb);
		  }
          
          
          $quest_text='Du konntest die Sonde und deren wertvollen Informationen bergen. Alle Spieler Deiner Fraktion haben eine Belohnung erhalten, welche Sie in Ihrem Modulkomplex finden k&ouml;nnen.';
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname.' - Die Fraktion konnte die Sonde bergen und alle Spieler der Fraktion haben eine Belohnung erhalten, welche Sie in Ihrem Modulkomplex finden k&ouml;nnen.';
        		
          //nachricht für den chat
      	  $time=time();
          insert_chat_msg('^Der Reporter^', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
          //datensatz wieder freigeben und updaten
          mysql_query("UPDATE sou_frac_quests SET questlevel=1, done='$done', aupdate=0, fraction='$player_fraction' WHERE id='$quest_id'", $soudb);
      }	  
	  else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';      
  }
  else $quest_text='<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Benötigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK';
}
else $quest_text='<font color="FF0000">Es wird ein Bergungsmodul ben&ouml;tigt.';

/*
INSERT INTO sou_frac_quests SET id=15, questlevel=0, fraction=0, x = 199995,  y = 1, hidexy=1;
*/

?>