<?php
//erreiche Punkt Aganra
include "soudata/questdata/questname".$quest_id.".php";

//questdaten laden
$db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE fraction='$player_fraction' AND id='$quest_id'",$soudb);
$rowquest = mysql_fetch_array($db_daten);
$questlevel=$rowquest["questlevel"];

//überprüfen ob er ein bergungsmodul hat
$db_daten=mysql_query("SELECT MAX(canrecover) AS canrecover FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);
$canrecover=$row["canrecover"];

if($canrecover>0 OR 1==1)//kein bergungsmodul notwendig
{
  //überprüfen, ob das bergungsmodul gut genug ist
  $needbk=0;
  if($canrecover>=$needbk)
  {
	  //es sind alle voraussetzungen erfüllt
	  //questdatensatz locken
	  $result = mysql_query("UPDATE sou_frac_quests SET aupdate=1 WHERE aupdate=0 AND fraction='$player_fraction' AND id='$quest_id'", $soudb);
      $num = mysql_affected_rows();
      if($num==1)
      {
        //questlevel erhöhen
        $questlevel++;
        
        //überprüfen ob die quest abgeschlossen ist
        if($questlevel>=2)
        {
          $done=1;
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname.' (Stufe '.$questlevel.')';
        }
        else
        {
          $done=0;
          $quest_text.=' Es liegen neue Koordinaten vor.';
      	  //die neuen koordinaten berechnen
      	  $target_x=0;
      	  $target_y=-1500;          
          
          $chat_text='Fraktion '.$player_fraction.' erf&uuml;llt folgende Aufgabe: '.$questname.' (Stufe '.$questlevel.')';
        }
		
		//nachricht für den chat
      	$time=time();
        insert_chat_msg('^Der Reporter^', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
        //datensatz wieder freigeben und updaten
        mysql_query("UPDATE sou_frac_quests SET questlevel='$questlevel', x='$target_x', y='$target_y', done='$done', aupdate=0 WHERE fraction='$player_fraction' AND id='$quest_id'", $soudb);
      }	  
	  else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';      
  }
  else $quest_text='<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Benötigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK';
}
else $quest_text='<font color="FF0000">Es wird ein Bergungsmodul ben&ouml;tigt.';

/*
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=1, x=199997,  y = -4, hidexy=0;
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=2, x=199997,  y = -4, hidexy=0;
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=3, x=199997,  y = -4, hidexy=0;
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=4, x=199997,  y = -4, hidexy=0;
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=5, x=199997,  y = -4, hidexy=0;
INSERT INTO sou_frac_quests SET id=20, questlevel=0, fraction=6, x=199997,  y = -4, hidexy=0;
*/

?>