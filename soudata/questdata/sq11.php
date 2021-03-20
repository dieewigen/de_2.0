<?php
//omega-key
include "soudata/questdata/questname".$quest_id.".php";

//questdaten laden
$db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE fraction='$player_fraction' AND id='$quest_id'",$soudb);
$rowquest = mysql_fetch_array($db_daten);
$questlevel=$rowquest["questlevel"];

//überprüfen ob er ein bergungsmodul hat
$db_daten=mysql_query("SELECT MAX(canrecover) AS canrecover FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);
$canrecover=$row["canrecover"];

if($canrecover>0)
{
  //überprüfen, ob das bergungsmodul gut genug ist
  $needbk=15000;
  if($canrecover>=$needbk)
  {
	  //es sind alle voraussetzungen erfüllt
	  //questdatensatz locken
	  $result = mysql_query("UPDATE sou_frac_quests SET aupdate=1 WHERE aupdate=0 AND fraction='$player_fraction' AND id='$quest_id'", $soudb);
      $num = mysql_affected_rows();
      if($num==1)
      {
          $done=1;
          
          $quest_text='Du findest einen wichtigen Datenkristall und stellst ihn Deiner Fraktion zur Verf&uuml;gung.';
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname.' - Die Fraktion verfügt jetzt &uuml;ber den entsprechenden Datenkristall.';
        		
		//nachricht für den chat
      	$time=time();
        insert_chat_msg('^Der Reporter^', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
        //datensatz wieder freigeben und updaten
        mysql_query("UPDATE sou_frac_quests SET questlevel=1, x='$target_x', y='$target_y', done='$done', aupdate=0 WHERE fraction='$player_fraction' AND id='$quest_id'", $soudb);
      }	  
	  else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';      
  }
  else $quest_text='<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Benötigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK';
}
else $quest_text='<font color="FF0000">Es wird ein Bergungsmodul ben&ouml;tigt.';

/*
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=1, x=0,  y = -2250, hidexy=1;
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=2, x=0,  y = -2250, hidexy=1;
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=3, x=0,  y = -2250, hidexy=1;
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=4, x=0,  y = -2250, hidexy=1;
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=5, x=0,  y = -2250, hidexy=1;
INSERT INTO sou_frac_quests SET id=11, questlevel=0, fraction=6, x=0,  y = -2250, hidexy=1;
*/

?>