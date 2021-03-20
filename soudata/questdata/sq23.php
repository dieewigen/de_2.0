<?php
//schatz von elrindis
include "soudata/questdata/questname".$quest_id.".php";
//kampfbibliothek laden
include_once 'soudata/lib/sou_fight.lib.php';


//questdaten laden
$db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE fraction='$player_fraction' AND id='$quest_id'",$soudb);
$rowquest = mysql_fetch_array($db_daten);
$questlevel=$rowquest["questlevel"];

$pirateslevel=$questlevel+1;

//spielerschiffsdaten
  $enm1['name']=$player_ship_name;
  $enm1['ship_diameter']=$player_ship_diameter;
  $enm1['hp']=$player_ship_diameter*1000;
  	  
  $db_daten=mysql_query("SELECT SUM(giveweapon) AS giveweapon, SUM(giveshield) AS giveshield FROM sou_ship_module WHERE user_id='$player_user_id' AND location=0",$soudb);
  $row = mysql_fetch_array($db_daten);
  $enm1['att']=$row["giveweapon"];
  $enm1['shield']=$row["giveshield"];

  //alle schiffsdurchmesser berechnen die es geben kann
  unset($shipsizes);
  $ga=2;$d=-2;
  for($i=0;$i<=60;$i++)
  {
	$d=$d+$ga;
	$ga=$ga*1.1019;
	$shipsizes[]=floor($d+10);
  }  
  	  
  //$enm1['level']=$hb_def[$player_in_hb-1][2][$cid][0];
  $enm2['name']='Spionageraumschiff '.$pirateslevel;
  $enm2['ship_diameter']=$shipsizes[$pirateslevel-1];
  //hitpoints aus schiffgröße berechnen
  $enm2['hp']=$enm2['ship_diameter']*1000;
  //anzahl der module für waffen/schilde reaktoren (-3 module für grundversorgung)
  $enm_fight_module=(2+round(sqrt($enm2['ship_diameter'])))*1.8;
  //feuerkraft/schilde berechnen, reaktoren werden aufgrund der überlegenen technologie nicht benötigt, diese sind bereits integriert
  $enm2['att']=$enm_fight_module/2*1000*($pirateslevel-1)*0.6;
  $enm2['shield']=$enm_fight_module/2*6000*($pirateslevel-1)*0.6;

  	  
  //kampf starten
  $fightdata=do_fight($enm1, $enm2);

  //überprüfen wer gewonnen/verloren hat
  if($fightdata['haswon']==1)//man hat gewonnen
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
		$target_x=cos($winkel)*$questlevel*25;
		$target_y=sin($winkel)*$questlevel*25;
		$target_x=round($target_x+$sv_sou_startposition[$player_fraction-1][0]);
		$target_y=round($target_y+$sv_sou_startposition[$player_fraction-1][1]);

        //überprüfen ob die quest abgeschlossen ist
        if($questlevel>=25)
        {
		  //die spendengröße berechnen
		  $spende=20000000;
	
		  //die kasse vergrößern
		  $feldname='f'.$player_fraction.'money';
		  mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$spende'",$soudb);
		  
		  //diese spende dem ansehen im heimatsystem zurechnen
    	  $sx=$sv_sou_startposition[$player_fraction-1][0];
    	  $sy=$sv_sou_startposition[$player_fraction-1][1];
          
          $done=1;
          
          $quest_text='Du hast das letzte Spionageraumschiff zerst&ouml;rt und findest '.number_format($spende, 0,"",".").' Zastari. Diese werden der Fraktionskasse gutgeschrieben.';
          $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
          $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname.' (Stufe '.$questlevel.')';
        }
        else
        {
		  //nachricht für den spieler / chat
		  $quest_text='Du vernichtest das Raumschiff und findest Spuren, die auf andere Koordinaten hinweisen.';
		  $chat_text='Fraktion '.$player_fraction.' erf&uuml;llt folgende Aufgabe: '.$questname.' (Stufe '.$questlevel.')';
          $done=0;
        }
		//kampfbericht ausgeben
        $quest_text.='<br>'.$fightdata['fightlog'];		
		//nachricht für den chat
      	$time=time();
        insert_chat_msg('Der Reporter', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
        //datensatz wieder freigeben und updaten
        mysql_query("UPDATE sou_frac_quests SET questlevel='$questlevel', x='$target_x', y='$target_y', done='$done', aupdate=0 WHERE fraction='$player_fraction' AND id='$quest_id'", $soudb);
        //mysql_query("UPDATE sou_frac_quests SET aupdate=0 WHERE fraction='$player_fraction' AND id='$quest_id'", $soudb);
      }	  
	  else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';    
  }
  else  //kampf verloren
  {
    //reparaturzeit
	$time=time()+60;
  	mysql_query("UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time' WHERE user_id='$player_user_id'",$soudb);
  	$quest_text.= '<b>Der Kampf wurde verloren.</b>&nbsp;<a href="sou_main.php?action=systempage"><div class="b1">weiter</div></a>';
  }
    

/*
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=1, x=0,  y = 1500;
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=2, x= 1245, y = 825;
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=3, x=1245, y = -825;
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=4, x=0, y = -1500;
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=5, x= -1245, y = -825;
INSERT INTO sou_frac_quests SET id=23, questlevel=0, fraction=6, x= -1245, y = 825;
*/

?>