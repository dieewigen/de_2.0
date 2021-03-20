<?php
//verlorene zastari
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
  $needbk=ceil(($questlevel+1)/10)*1000;
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
        
      	//die neuen koordinaten berechnen
      	$winkel=2*pi()/360*mt_rand(1,360);
      	//$winkel=mt_rand(1,360);
		$target_x= cos($winkel)*$questlevel;
		$target_y= sin($winkel)*$questlevel;
		$target_x=round($target_x+$sv_sou_startposition[$player_fraction-1][0]);
		$target_y=round($target_y+$sv_sou_startposition[$player_fraction-1][1]);

		//die spendengröße berechnen
		$spende=ceil(($questlevel+1)/10)*100000;
	
		//die kasse vergrößern
		$feldname='f'.$player_fraction.'money';
		mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$spende'",$soudb);
		
		
		//die spende dem spieler gutschreiben
		mysql_query("UPDATE `sou_user_data` SET donate=donate+'$spende' WHERE user_id='$player_user_id'",$soudb);
	
		//diese spende dem ansehen im heimatsystem zurechnen
    	$sx=$sv_sou_startposition[$player_fraction-1][0];
    	$sy=$sv_sou_startposition[$player_fraction-1][1];
    	$feldname='prestige'.$player_fraction;
		mysql_query("UPDATE sou_map SET $feldname=$feldname+'$spende' WHERE x='$sx' AND y='$sy'", $soudb);	
		
		//nachricht für den spieler / chat
		$quest_text='Die Aufgabe ist erf&uuml;llt und die verlorenen '.number_format($spende, 0,"",".").' Zastari wurden der Fraktionskasse gutgeschrieben. Dein Ansehen steigt in H&ouml;he der Zastari.';

        //überprüfen ob die quest abgeschlossen ist
        if($questlevel>=100)
        {
          $done=1;
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname.' (Stufe '.$questlevel.')';
          
        }
        else
        {
          $done=0;
          $quest_text.=' Es liegen neue Koordinaten vor.';
          
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
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=1, x=0,  y = 1500;
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=2, x= 1245, y = 825;
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=3, x=1245, y = -825;
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=4, x=0, y = -1500;
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=5, x= -1245, y = -825;
INSERT INTO sou_frac_quests SET id=2, questlevel=0, fraction=6, x= -1245, y = 825;
*/

?>