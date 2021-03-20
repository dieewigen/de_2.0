<?php
//Arodnap
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
  $needbk=17000;
  if($canrecover>=$needbk)
  {
	  //es sind alle voraussetzungen erfüllt
	  //questdatensatz locken
	  $result = mysql_query("UPDATE sou_frac_quests SET aupdate=1 WHERE aupdate=0 AND id='$quest_id'", $soudb);
      $num = mysql_affected_rows();
      if($num==1)
      {
		  //belohnung
		  $spende=10000000;
	
		  //die kasse vergrößern
		  $feldname='f'.$player_fraction.'money';
		  mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$spende'",$soudb);
      
      
          $done=1;
          
          $quest_text='Du findest in dem uralten Raumschiffwrack viele wertvolle Artefakte im Werte von '.number_format($spende, 0,"",".").' Zastari. Diese werden der Fraktionskasse gutgeschrieben.';
          $quest_text.=' Bei der Bergung aktiviert sich ein Hyperfunkger&auml;t und sendet eine kurze Impulsfolge. Wart Ihr vielleicht zu gierig?';
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname;
          $chat_text.=' Bei der Bergung des Raumschiffwracks Arrodnap aktiviert sich ein Hyperfunkger&auml;t und sendet eine kurze Impulsfolge die nur folgendes enth&auml;lt "F'.$player_fraction.'". Waren sie vielleicht zu gierig?';
                  		
          //nachricht für den chat
      	  $time=time();
          insert_chat_msg('Der Reporter', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
          //datensatz wieder freigeben und updaten
          mysql_query("UPDATE sou_frac_quests SET questlevel=1, done='$done', aupdate=0, fraction='$player_fraction' WHERE id='$quest_id'", $soudb);
      }	  
	  else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';
  }
  else $quest_text='<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Benötigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK';
}
else $quest_text='<font color="FF0000">Es wird ein Bergungsmodul ben&ouml;tigt.';

/*
INSERT INTO sou_frac_quests SET id=24, questlevel=0, fraction=0, x = 199998,  y = 7, hidexy=1;
*/

?>