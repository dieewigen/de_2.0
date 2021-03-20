<?php
//daten zur ansicht
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
//überprüfen ob man kämpfen möchte
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
if($_REQUEST["attackpirates"]==1 AND $player_atimer1time<time())
{
  echo '<div align="center">';

  rahmen0_oben();
  echo '<br>';
  
  rahmen1_oben('<div align="center"><b>Kampfbericht</b></div>');
 
  //kampfbibliothek laden
  include_once 'soudata/lib/sou_fight.lib.php';
  	  
  //////////////////////
  //////////////////////
  //spielerdaten bestimmen, die es nicht in der session gibt, um sie beim gegner für den kampf zu hinterlegen
  //////////////////////
  //////////////////////
  $enm1['name']=$player_ship_name;
  $enm1['ship_diameter']=$player_ship_diameter;
  $enm1['hp']=$player_ship_diameter*1000;
  	  
  $db_daten=mysql_query("SELECT SUM(giveweapon) AS giveweapon, SUM(giveshield) AS giveshield FROM sou_ship_module WHERE user_id='$player_user_id' AND location=0",$soudb);
  $row = mysql_fetch_array($db_daten);
  $enm1['att']=$row["giveweapon"];
  $enm1['shield']=$row["giveshield"];
  	  
  	  
  ////////////////////// 
  //////////////////////
  //gegner erstellen
  //////////////////////
  //////////////////////
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
  $enm2['name']='Pirat';
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
    //kills mitloggen
 	mysql_query("UPDATE sou_user_data SET pirateskill=pirateskill+1 WHERE user_id='$player_user_id'",$soudb);
  	echo '<b>Der Kampf wurde gewonnen. Verbleibende Piraten: '.($pirates-1).'</b>&nbsp;
  	<a href="sou_main.php?action=systempage"><div class="b1">System</div></a>&nbsp;<a href="sou_main.php?action=systempage&attackpirates=1"><div class="b1">weiter angreifen</div></a>';
    
  	//überprüfen ob man ggf. einen besonderen rohstoff bekommen hat
    //überprüfen ob er ein bergungsmodul hat
    $db_daten=mysql_query("SELECT MAX(canrecover) AS canrecover FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
    $row = mysql_fetch_array($db_daten);
    $canrecover=$row["canrecover"];
    
    $needrecover=$pirateslevel*1000;
    if($canrecover>=$needrecover)
    {
  	  if(mt_rand(1,100)<=2)
  	  {
	    //überprüfen welche spezialrohstoffe es an der position gibt
    	unset($availableres);
    	for($i=0;$i<count($specialres_def);$i++)
    	{
	  	  if(specialres_is_available($i)==1)$availableres[]=$i;
    	}
  
    	//von den verfügbaren rohstoffen einen per zufall auswählen
    	$w=mt_rand(0, count($availableres)-1);
    
    	$dbfeld=$specialres_def[$w][0];
      
    	mysql_query("UPDATE sou_user_data SET $dbfeld=$dbfeld+1 WHERE user_id='$player_user_id'",$soudb);
      
 	    echo '<font color="#00FF00">Du findest folgendes: 1x '.$specialres_def[$w][1].'</font>';
  	  }
  	  else echo 'Du konntest in dem Raumschiffwrack nichts wertvolles finden.';
    }
    else echo '<font color="#00FF00">Um dieses Raumschiffwrack untersuchen zu k&ouml;nnen ben&ouml;tigst Du ein Bergungsmodul mit einer Kapazit&auml;t von '.$needrecover.' BK.</font>';
  	
  	
  	//pirat abziehen
  	mysql_query("UPDATE sou_map SET pirates=pirates-1 WHERE id='$owner_id'",$soudb);
  	$pirates--;
  }
  else //man hat verloren 
  {
    //reparaturzeit
	$time=time()+60;
  	mysql_query("UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time' WHERE user_id='$player_user_id'",$soudb);
  	echo '<b>Der Kampf wurde verloren. Verbleibende Piraten: '.($pirates).'</b>&nbsp;<a href="sou_main.php?action=systempage"><div class="b1">weiter</div></a>';
  }  	  
  	  
  //kampfbericht ausgeben
  echo $fightdata['fightlog'];
  	  
  rahmen1_unten();
  echo '<br>';
  rahmen0_unten();

  die('</body></html>');
}
else  //piratensymbol einblenden 
{
  $title='&Dieses Sonnensystem wird von Piraten belagert.
<br>Anzahl der Piraten: '.$pirates.'
<br>Stufe der Piraten: '.$pirateslevel.'
<br><br>Klicke zum angreifen.';
  
  echo '<a href="sou_main.php?action=systempage&attackpirates=1"><img id="pirates" src="'.$gpfad.'sym11.png" width="64" height="64" title="'.$title.'"></a>';
}
?>