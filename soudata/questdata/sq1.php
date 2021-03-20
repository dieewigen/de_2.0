<?php
//schiffsfledderei
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
    //ist noch ein modulplatz vorhanden
 	//berechnen wieviele module man haben kann
    //$module_max=6+round(sqrt($player_ship_diameter));     
	//überprüfen wieviele man hat
    //$db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
    //$moduleinraumschiff = mysql_num_rows($db_daten);

    //wird jetzt immer im schiff aufgenommen und kann die maxgrenze übersteigen
	if($moduleinraumschiff<$module_max OR 1==1)
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
        
		//das modul im raumschiff hinterlegen
		//upgradestärke
		$canbldgupgrade=ceil($questlevel/10);
		//modulname
		$tech_name='Seltar Modul AUG '.$canbldgupgrade.'T'.($canbldgupgrade+1);
	    $sql="INSERT INTO sou_ship_module (user_id, fraction, name, needspace, canbldgupgrade, location, craftedby, quality) VALUES 
            ('$player_user_id', '$player_fraction', '$tech_name', '0', '$canbldgupgrade', 0, 'ERBAUER', 2)";
        mysql_query($sql,$soudb);
            
		//nachricht für den spieler / chat
		$quest_text='Die Aufgabe ist erf&uuml;llt und das geborgene Modul befindet sich an Bord.';
		

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
	else $quest_text='<font color="FF0000">Es wird ein freier Modulplatz im Raumschiff ben&ouml;tigt.';
  }
  else $quest_text='<font color="FF0000">Die Bergungskapazit&auml;t des Bergungsmoduls ist nicht hoch genug. Benötigt wird folgender Wert: '.number_format($needbk, 0,"",".").' BK';
}
else $quest_text='<font color="FF0000">Es wird ein Bergungsmodul ben&ouml;tigt.';

?>