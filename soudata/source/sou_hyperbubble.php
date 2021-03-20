<?php
include "soudata/defs/buildings.inc.php";
//include "soudata/defs/hb.inc.php";

echo '<br>';

//maximalen gebäudelevel auslesen
$geb_level=get_max_frac_bldg_level(13);


//aktuellen punkte auslesen
unset($verteilung);
$db_daten=mysql_query("SELECT * FROM `sou_hbpoints`",$soudb);
while($row = mysql_fetch_array($db_daten))
{
	$hbpoints[$row['id']][0]=$row['owner'];	
	$hbpoints[$row['id']][1]=$row['points'];
	$verteilung[$row['owner']]++;
}

/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// man befindet sich in einer hyperraumblase
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
if($player_in_hb>0)
{
  //alle schiffsdurchmesser berechnen die es geben kann
  unset($shipsizes);
  $ga=2;$d=-2;
  for($i=0;$i<=70;$i++)
  {
	$d=$d+$ga;
	$ga=$ga*1.1019;
	$shipsizes[]=floor($d+10);
  }  

  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  //überprüfen ob man kämpfen möchte
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  if($_REQUEST["hbdo"]==1 AND $player_atimer1time<=time() AND $_REQUEST['st']==$_SESSION['sou_hb_token'])
  {
  	$id=intval($_REQUEST["id"]);
  	
  	//////////////////////////////////////////////////////////////
  	//////////////////////////////////////////////////////////////
  	//tokencheck
  	if(isset($_REQUEST['mmc']) OR isset($_REQUEST['mma']) OR isset($_REQUEST['mmb']) OR $_REQUEST['mmd']!=5)
  	{
  		$body='mma: '.$_REQUEST['mma'].' mmb: '.$_REQUEST['mmb'].' mmc: '.$_REQUEST['mmc'].' mmd: '.$_REQUEST['mmd'];
  		mail('issomad@die-ewigen.com', $sv_server_tag.' HB: '.$ums_user_id.' F'.$player_fraction.'('.$_REQUEST['st'].'/'.$_SESSION['sou_hb_token'].')', $body, 'FROM: issomad@die-ewigen.com');
  	}
  	//token unbrauchbar machen
  	$_SESSION['sou_hb_token']=mt_rand(1000000,9999999);
  	
    //überprüfen ob man auf der stufe zugreifen kann
  	if(($id+1)<=$geb_level OR $geb_level>=50)
  	{
    	//überprüfen ob man genug aktionspunkte hat
    	if($player_hb_ap>0)
    	{
    		$player_hb_ap--;
  		
  		echo '<br>';
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
  	  
  	  //$enm1['level']=$hb_def[$player_in_hb-1][2][$cid][0];
  	  $enm2['name']='Stationswachschiff';
  	  $enm2['ship_diameter']=$shipsizes[$id];
  	  //hitpoints aus schiffgröße berechnen
  	  $enm2['hp']=$enm2['ship_diameter']*1000;
  	  //anzahl der module für waffen/schilde reaktoren (-3 module für grundversorgung)
  	  $enm_fight_module=(2+round(sqrt($enm2['ship_diameter'])))*1.8;
  	  //feuerkraft/schilde berechnen, reaktoren werden aufgrund der überlegenen technologie nicht benötigt, diese sind bereits integriert
  	  $enm2['att']=$enm_fight_module/2*1000*($id+1)*0.6;
  	  $enm2['shield']=$enm_fight_module/2*6000*($id+1)*0.6;
  	    	  
  	  
  	  //kampf starten
  	  $fightdata=do_fight($enm1, $enm2);

  	  	  //überprüfen wer gewonnen/verloren hat
  	  if($fightdata['haswon']==1)//man hat gewonnen
  	  {
  	    //change_darkmatter($player_user_id, $id+1);
  	    change_darkmatter($player_user_id, 1);
        //reparaturzeit
  		$time=time()+10;
    	mysql_query("UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time', hb_ap=hb_ap-1 WHERE user_id='$player_user_id'",$soudb);
    	echo '<b>Der Kampf wurde gewonnen (+1 Eroberungspunkt f&uuml;r diese Station). Dunkle Materie erbeutet: 1 cm&sup3;</b><a href="sou_main.php?action=systempage&weiter=1"><div class="b1">weiter</div></a><br>';
		//eigene fraktion ist der eroberer
    	if($hbpoints[$id+1][0]==$player_fraction OR $hbpoints[$id+1][1]==0)
    	{
    		mysql_query("UPDATE sou_hbpoints SET owner='$player_fraction', points=points+1 WHERE id='".($id+1)."'",$soudb);
    	}
    	else//sie gehört jemand anderem 
    	{
    		mysql_query("UPDATE sou_hbpoints SET points=points-1 WHERE points>0 AND id='".($id+1)."'",$soudb);
    		//fraktion ggf zurücksetzen
    		mysql_query("UPDATE sou_hbpoints SET owner=0 WHERE points=0;",$soudb);
    	}
    	 
  	  }
  	  else //man hat verloren 
  	  {
        //reparaturzeit
  		$time=time()+60;
    	mysql_query("UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time', hb_ap=hb_ap-1 WHERE user_id='$player_user_id'",$soudb);
    	echo '<b>Der Kampf wurde verloren.</b><a href="sou_main.php?action=systempage"><div class="b1">weiter</div></a><br>';
  	  }  	  
  	  
  	  //kampfbericht ausgeben
  	  echo $fightdata['fightlog'];
  	  
  	  rahmen1_unten();
  	  
  	  echo '<br>';
  	  
  	  rahmen0_unten();
  	  
  	  die('</body></html>');
    	}
    	else
    	{
    		$msg='<font color="#FF0000"><b>Du hast nicht genug Aktionspunkte. Alle 5 Minuten erh&auml;ltst Du einen Punkt.</b></font>';
    	}
  	}
  	else 
  	{
  		if($id>49)$id=49;
  		$msg='<font color="#FF0000"><b>Ben&ouml;tigte Hyperraumaufrissprojektorfraktionsstufe: '.($id+1).'</b></font>';
  	}
  }
  
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  //überprüfen ob man vielleicht die hb verlassen möchte
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  if(isset($_REQUEST["leavehb"]) AND $player_atimer1time<=time())
  {
  	//$time=time()+10;
  	$chb++;
    mysql_query("UPDATE sou_user_data SET inhb=0 WHERE user_id='$player_user_id'",$soudb);
   	header("Location: sou_main.php");
  }
	
  echo '<br>';

  echo '<div align="center">';

  rahmen0_oben();
  
  if($msg!='')
  {
  	  echo '<br>';
	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
  }   

  //seitenteiler bauen
  echo '<br><table border="0" cellpadding="0" cellspacing="0">';

  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  //linker teil mit der übersicht
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  
  //token für eine erkennung der automatisierung
  $_SESSION['sou_hb_token']=mt_rand(1000000,9999999);
  
  echo '<tr valign="top"><td width="625">';
  rahmen1_oben('<div align="center"><b>Hyperraumblase (Hyperraumaufrissprojektorfraktionsstufe '.$geb_level.')</b></div>');
  //kartenstyle
  echo '<div id="hb_map">';
  $id=0;
  $top=0;
  $left=0;
  $itemsperline=15;
  for($y=1;$y<=8;$y++)
  {
  	for($i=1;$i<=$itemsperline;$i++)
  	{
  		if($hbpoints[$id+1][0]>0)
  		{
  			$bgcolor=$colors_text[$hbpoints[$id+1][0]-1];
  			$eroberer='<br>Eroberer: F'.$hbpoints[$id+1][0].' (Punkte: '.$hbpoints[$id+1][1].')';
  		}
  		else 
  		{
  			$bgcolor='000000';
  			$eroberer='';
  		}
		
  		echo '<div style="border: 1px solid #'.$bgcolor.';position: absolute;top: '.($top*40).'px; left: '.($left*40).'px;" 
		title="Stationswachschiff&Durchmesser: '.number_format($shipsizes[$id], 0,",",".").' m'.$eroberer.'">
		
		<a href="sou_main.php?action=systempage&mmb=5&hbdo=1&st='.$_SESSION['sou_hb_token'].'&id='.$id.'"><img style="visibility: hidden; position: absolute;" src="'.$gpfad.'ssrb1.png" width="38px" height="38px" border="0"></a>
		<a href="sou_main.php?action=systempage&mmc=5&hbdo=1&st='.$_SESSION['sou_hb_token'].'&id='.$id.'"><img style="visibility: hidden; position: absolute;" src="'.$gpfad.'ssrb1.png" width="38px" height="38px" border="0"></a>
		<a href="sou_main.php?action=systempage&mma=5&hbdo=1&st='.$_SESSION['sou_hb_token'].'&id='.$id.'"><img style="visibility: hidden; position: absolute;" src="'.$gpfad.'ssrb1.png" width="38px" height="38px" border="0"></a>
		<a href="sou_main.php?action=systempage&mmd=5&hbdo=1&st='.$_SESSION['sou_hb_token'].'&id='.$id.'"><img src="'.$gpfad.'ssrb1.png" width="38px" height="38px" border="0"></a>
		
		</div>';
		
		$id++;
		$left++;
  	}
  	$itemsperline=$itemsperline-2;
  	$top++;
  	$left=$y;
  }
  
  echo '</div>';
  rahmen1_unten();
  
  echo '<br>';
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  //seitenteiler rechter teil
  /////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////
  echo '</td><td width="300">';
  
  
  //aktionsmöglichkeiten
  rahmen1_oben('<div align="center"><b>Aktionsm&ouml;glichkeiten</b></div>');
  echo '<div align="left"><a href="sou_main.php?action=systempage&leavehb=1" class="btn">Blase verlassen</a>';
  echo '</div>';
  rahmen1_unten();
  
  //Informationen
  rahmen1_oben('<div align="center"><b>Informationen</b></div>');
  echo '<div align="left">Verbleibende HB-Aktionspunkte: '.number_format($player_hb_ap, 0, ",",".").'/30';
  echo '</div>';
  rahmen1_unten();
  
  
  //eroberte stationen
  echo '<br>';
  rahmen1_oben('<div align="center"><b>Eroberte Stationen</b></div>');
  echo '<div class="cell1" align="left">';
  for($i=1;$i<=6;$i++)
  {
  	echo '<font color="#'.$colors_text[$i-1].'">F'.$i.': '.round($verteilung[$i]).' (Handelskontorbonus: '.number_format($verteilung[$i]*0.2, 2, ",",".").')%</font><br>';	
  }
  
  echo '</div>';
  rahmen1_unten();
  
  echo '<br>';
    
  rahmen0_unten();

  die('</div></body></html>');
}

/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// man befindet sich NICHT in einer hyperraumblase
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////


echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $row = mysql_fetch_array($db_daten);
  $owner_id=$row["id"];
  $owner_fraction=$row["fraction"];
  
  //überprüfen ob das system zur eigenen fraktion gehört
  if($player_fraction!=$owner_fraction)
  {
  	rahmen2_oben();
  	echo 'Auf dieses Sonnensystem hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }
  
	
  $geb_level=get_bldg_level($owner_id, 13);
  if($geb_level>0)
  {
	
  	/////////////////////////////////////////////////////////////
  	/////////////////////////////////////////////////////////////
  	// einflug in eine hyperraumblase
  	/////////////////////////////////////////////////////////////
  	/////////////////////////////////////////////////////////////
  	
  	if($_REQUEST["enter"]==1)
  	{
        mysql_query("UPDATE sou_user_data SET inhb=1 WHERE user_id='$player_user_id'",$soudb);
   	    header("Location: sou_main.php");
 	}
  }
  else echo 'Ohne Hyperraumaufrissprojektor ist kein Zugriff m&ouml;glich. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
}
else echo 'Hier gibt es kein Sonnensystem. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';
rahmen0_unten();
echo '<br>';

echo '</div>';//center-div
echo '</form>';
die('</body></html>');
?>