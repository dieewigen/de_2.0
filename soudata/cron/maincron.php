<?php
set_time_limit(240);
include 'mysql_wrapper.inc.php';
include "../defs/startpositionen.inc.php";
include "../defs/colors.inc.php";
include "../lib/sou_functions.inc.php";
$directory="../";

include "croninfo.inc.php";

include "../lib/sou_dbconnect.php";

?>
<html>
<head>
</head>
<body>
<?php
//startet den zufallsgenerator
srand((double)microtime()*1000000);
mt_srand((double)microtime()*10000);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    cronjob-auftr�ge aus sou_cronjobs abarbeiten
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll min�tlich arbeiten, also wie im cron

$time=time();
$db_data = mysql_query("SELECT * FROM sou_cronjobs WHERE time<'$time'", $soudb);
while($rowx = mysql_fetch_array($db_data))
{
  $job=$rowx["job"];
  $flag1=$rowx["flag1"];
  $crontime=$rowx["time"];
  
  switch($job){
  	case 1: //sonnensysteme anlegen
  	  $owner_id=$flag1;  
  	  echo '<br>SS anlegen '.$owner_id;
  	  
  	   //�berpr�fen ob es evtl. schon daten gibt
  	  $db_daten=mysql_query("SELECT * FROM sou_map_planets WHERE owner_id='$owner_id'",$soudb);
      $num = mysql_num_rows($db_daten);
      if($num==0) //die planetendaten erzeugen
      {
      	//wieviele planeten soll es geben
      	$anz_planets=mt_rand(3,12);
      	//alle startsystem gleichm��ig mit x planetensystemen belegen
      	//dazu die koordinaten von dem ss auslesen
      	$db_daten=mysql_query("SELECT x,y FROM sou_map WHERE id='$owner_id'",$soudb);
      	$row = mysql_fetch_array($db_daten);
      	$x=$row["x"];
      	$y=$row["y"];
      	for($i=0;$i<count($sv_sou_startposition);$i++)
      	{
      	  if($x==$sv_sou_startposition[$i][0] AND $y==$sv_sou_startposition[$i][1])
      	  $anz_planets=8;
      	}
      	//wo soll sich das asteroidenfeld befinden
      	$astrofeldpos=mt_rand(1, $anz_planets);
      	
      	//planeten in der db einf�gen
      	$distance=1;
      	unset($pp);//array mit den planetenbildern resetten
      	for($i=1;$i<=$anz_planets;$i++)
      	{
      	  //planetenbild bestimmen
      	  if($i==1)
      	  {
      	    $pic=mt_rand(1,20);
      	    $pp[]=$pic;
      	  }
      	  else 
      	  {
      	    while(in_array($pic,$pp))
      	    {
      	      $pic=mt_rand(1,20);
      	    }
      	    $pp[]=$pic;
      	  }
      	  
      	  //planetengr��e bestimmen
      	  $size=mt_rand(0,5);
      	  
      	  mysql_query("INSERT INTO sou_map_planets (owner_id, name, typ, distance, pic, size) VALUES ('$owner_id', '', 1, '$distance', $pic, $size)",$soudb);
      	  $distance++;
      	  
      	  //schauen ob jetzt das asteroidenfeld kommt
      	  if($i==$astrofeldpos)
      	  {      	  
      	    $pic=1;
      	  	mysql_query("INSERT INTO sou_map_planets (owner_id, name, typ, distance, pic) VALUES ('$owner_id', '', 2, '$distance', $pic)",$soudb);
      	    $distance++;
      	  
      	  }
      	}
      }
  	  else echo 'Das SS wurde bereits angelegt.'; 
  	  //den cronjob entfernen
  	  mysql_query("DELETE FROM sou_cronjobs WHERE job='$job' AND flag1='$flag1' AND time='$crontime' LIMIT 1",$soudb);
  	break;
  	default:
  	  echo 'Unbekannte Job-ID: '.$job;
  	break;
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//  hyperbubble-aktionspunkte
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//alle x minuten um ein punkt erh�hen
if(strftime("%M") % 5==0)
{
  mysql_query("UPDATE sou_user_data SET hb_ap=hb_ap+1 WHERE hb_ap<30",$soudb);
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    user-locks l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//st�ndlich
if(strftime("%M")==0)
{
  mysql_query("DELETE FROM sou_user_locks",$soudb);
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// piraten verteilen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede nacht um 0 uhr
if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==0)
{
  mysql_query("UPDATE sou_map SET pirates=pirates+10 WHERE pirates < 21",$soudb);
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    abgelaufene buffs l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//min�tlich
$time=time();
mysql_query("DELETE FROM sou_user_buffs WHERE time<'$time'",$soudb);
mysql_query("DELETE FROM sou_map_buffs WHERE time<'$time'",$soudb);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// ein jahr hochz�hlen/t�gliches geschenk
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede nacht um 0 uhr
if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==0)
{
  echo '<br>Jahrescounter';
  mysql_query("UPDATE sou_system SET year=year+1",$soudb);
  mysql_query("UPDATE sou_user_data SET playerage=playerage+1, dailygift=1",$soudb);
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    tagesspendenwert sichern
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede nacht um 0 uhr
if(intval(strftime("%M"))==2 AND intval(strftime("%H"))==1)
{
  echo '<br>donatelastday';
  mysql_query("UPDATE sou_user_data SET donatelastday=donate",$soudb);
}



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    fraktionszuweisungen der sonnensysteme
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jeden tag um 22 uhr
if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==22)
{
  echo '<br>Fraktionsberechnung';
  mysql_query("UPDATE sou_map SET fraction=1 WHERE prestige1 > prestige2 AND prestige1 > prestige3 AND prestige1 > prestige4 AND prestige1 > prestige5 AND prestige1 > prestige6",$soudb);
  mysql_query("UPDATE sou_map SET fraction=2 WHERE prestige2 > prestige1 AND prestige2 > prestige3 AND prestige2 > prestige4 AND prestige2 > prestige5 AND prestige2 > prestige6",$soudb);
  mysql_query("UPDATE sou_map SET fraction=3 WHERE prestige3 > prestige1 AND prestige3 > prestige2 AND prestige3 > prestige4 AND prestige3 > prestige5 AND prestige3 > prestige6",$soudb);
  mysql_query("UPDATE sou_map SET fraction=4 WHERE prestige4 > prestige1 AND prestige4 > prestige2 AND prestige4 > prestige3 AND prestige4 > prestige5 AND prestige4 > prestige6",$soudb);
  mysql_query("UPDATE sou_map SET fraction=5 WHERE prestige5 > prestige1 AND prestige5 > prestige2 AND prestige5 > prestige3 AND prestige5 > prestige4 AND prestige5 > prestige6",$soudb);
  mysql_query("UPDATE sou_map SET fraction=6 WHERE prestige6 > prestige1 AND prestige6 > prestige2 AND prestige6 > prestige3 AND prestige6 > prestige4 AND prestige6 > prestige5",$soudb);

  /*
UPDATE sou_map SET fraction=1 WHERE prestige1 > prestige2 AND prestige1 > prestige3 AND prestige1 > prestige4 AND prestige1 > prestige5 AND prestige1 > prestige6;
UPDATE sou_map SET fraction=2 WHERE prestige2 > prestige1 AND prestige2 > prestige3 AND prestige2 > prestige4 AND prestige2 > prestige5 AND prestige2 > prestige6;
UPDATE sou_map SET fraction=3 WHERE prestige3 > prestige1 AND prestige3 > prestige2 AND prestige3 > prestige4 AND prestige3 > prestige5 AND prestige3 > prestige6;
UPDATE sou_map SET fraction=4 WHERE prestige4 > prestige1 AND prestige4 > prestige2 AND prestige4 > prestige3 AND prestige4 > prestige5 AND prestige4 > prestige6;
UPDATE sou_map SET fraction=5 WHERE prestige5 > prestige1 AND prestige5 > prestige2 AND prestige5 > prestige3 AND prestige5 > prestige4 AND prestige5 > prestige6;
UPDATE sou_map SET fraction=6 WHERE prestige6 > prestige1 AND prestige6 > prestige2 AND prestige6 > prestige3 AND prestige6 > prestige4 AND prestige6 > prestige5;
  */  
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    alte hyperfunknachrichten l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede nacht um 3 uhr
if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==3)
{
  echo '<br>Hyperfunknachrichten l�schen';
  //30 tage l�schfrist
  $time=time()-3600*24*30;
  $datum=date("Y-m-d H:i:s",$time);
  mysql_query("DELETE FROM sou_user_hyper WHERE date<'$datum' AND status LIKE '__0__'",$soudb);
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    hyperraumblasenpunkte zur�cksetzen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jeden tag um 22 uhr
if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==22)
{
  echo '<br>Hyperraumblasenpunkte zur&uuml;cksetzen';
  mysql_query("UPDATE sou_hbpoints SET owner=0, points=0;",$soudb);
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    tempdaten f�r bao-nada-berechnung fxcanminehasspace
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//alle x minuten
if(strftime("%M") % 10 == 0)
{
	$zeitgrenze=time()-24*3600*7; 
	
	for($i=1;$i<=6;$i++)
	{ 
		//aktive spieler und deren ausr�stung/boni f�r die bao-nada-skala auslesen
		//aktive spieler auslesen
  		$db_daten=mysql_query("SELECT user_id FROM sou_user_data WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze';",$soudb);
  		$aktive_spieler = mysql_num_rows($db_daten);
		
		/*
		$db_daten=mysql_query("SELECT SUM(sou_ship_module.canmine) AS wert1, SUM(sou_ship_module.hasspace) AS wert2 FROM `sou_ship_module` 
  		LEFT JOIN sou_user_data ON(sou_user_data.user_id = sou_ship_module.user_id) WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze' 
  		AND sou_ship_module.location=0",$soudb);
  
  		$row = mysql_fetch_array($db_daten);
  		$summe=$row['wert1']+$row['wert2'];*/
		
		//bergbaumodulleistung
		$db_daten=mysql_query("SELECT SUM(sou_ship_module.canmine) AS wert1 FROM `sou_ship_module` 
  		LEFT JOIN sou_user_data ON(sou_user_data.user_id = sou_ship_module.user_id) WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze' 
  		AND sou_ship_module.location=0",$soudb);
  		$row = mysql_fetch_array($db_daten);
  		$summe=$row['wert1'];

  		/*
  		//charbonus
		$db_daten=mysql_query("SELECT SUM(sou_user_skill.value) AS wert FROM `sou_user_skill` 
  		LEFT JOIN sou_user_data ON(sou_user_data.user_id = sou_user_skill.user_id) WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze' 
  		AND sou_user_skill.typ=0",$soudb);
  		$row = mysql_fetch_array($db_daten);
  		$charbonus=1+($row['wert']/500000/$aktive_spieler);
  		
  		//bonusmodulbonus
		$db_daten=mysql_query("SELECT SUM(sou_user_buffs.value) AS wert FROM `sou_user_buffs` 
  		LEFT JOIN sou_user_data ON(sou_user_data.user_id = sou_user_buffs.user_id) WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze' 
  		AND sou_user_buffs.typ=1",$soudb);
  		$row = mysql_fetch_array($db_daten);
  		$modulbonus=1+($row['wert']/$aktive_spieler);
  		*/

  		//gesamtwert berechnen
  		//$summe=$summe*$charbonus*$modulbonus;
  		
  		//db updaten
  		mysql_query("UPDATE sou_system SET f".$i."canminehasspace='$summe'", $soudb);
	}
	
	/*
Fraktion 1: Bewahrer des Gleichgewichts 90,72%
Fraktion 2: Bewahrer des Gleichgewichts 82,21%
Fraktion 3: Bewahrer des Gleichgewichts 80,32%
Fraktion 4: St�rer des Gleichgewichts 130,20%
Fraktion 5: Bewahrer des Gleichgewichts 90,48%
Fraktion 6: St�rer des Gleichgewichts 126,08%

Fraktion 1: Bewahrer des Gleichgewichts 90,02%
Fraktion 2: Bewahrer des Gleichgewichts 81,97%
Fraktion 3: Bewahrer des Gleichgewichts 80,20%
Fraktion 4: St�rer des Gleichgewichts 130,52%
Fraktion 5: Bewahrer des Gleichgewichts 90,58%
Fraktion 6: St�rer des Gleichgewichts 126,70%

Fraktion 1: Bewahrer des Gleichgewichts 89,08%
Fraktion 2: Bewahrer des Gleichgewichts 80,62%
Fraktion 3: Bewahrer des Gleichgewichts 78,57%
Fraktion 4: St�rer des Gleichgewichts 133,30%
Fraktion 5: Bewahrer des Gleichgewichts 90,75%
Fraktion 6: St�rer des Gleichgewichts 127,68%

Fraktion 1: Bewahrer des Gleichgewichts 87,45%
Fraktion 2: Bewahrer des Gleichgewichts 76,90%
Fraktion 3: Bewahrer des Gleichgewichts 75,95%
Fraktion 4: St�rer des Gleichgewichts 135,63%
Fraktion 5: Bewahrer des Gleichgewichts 89,88%
Fraktion 6: St�rer des Gleichgewichts 134,20%
	*/
}



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    auktionszentrum
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll min�tlich arbeiten, also wie im cron
echo '<hr><br>Auktionen bearbeiten<br>';
//abgelaufene auktionen bearbeiten
$sql="SELECT * FROM sou_ship_module WHERE time < UNIX_TIMESTAMP() AND location=2";
$result = mysql_query($sql, $soudb);
$itemmenge = mysql_num_rows($result);
echo 'Auktionen: '.$itemmenge.'<br><br>';
while($row = mysql_fetch_array($result))
{
  $id=$row["id"];
  $seller=$row["user_id"];
  $bidder=$row["bidder"];
  $price=$row["price"];
  $dprice=$row["dprice"];
  $modulfraction=$row["fraction"];
  $auctioncurrency=$row["auctioncurrency"];
  $modulname=$row["name"];

  //nur was machen, wenn jemand geboten hat
  if($bidder>0){
    echo '<br>Auktions-ID: '.$id;
    echo '<br>es wurde geboten';
    //dem verk�ufer das geld gutschreiben, wenn price null ist, dann ist es ein sofortkauf
    if($price==0)$preis=$dprice;else $preis=$price;
    echo '<br>Verk�ufer: '.$seller;
    echo '<br>Preis: '.$preis;
    change_money($seller, $preis);
    
    //wenn das modul keine fraktion hat, diese w�hlen
    if($modulfraction==0){
      $result = mysql_query("SELECT fraction FROM sou_user_data WHERE user_id = '$bidder'", $soudb);
      $row = mysql_fetch_array($result);
      $modulfraction=$row["fraction"];
    }

    //dem k�ufer den gegenstand �bertragen
    echo '<br>K�ufer: '.$bidder;
    mysql_query("UPDATE sou_ship_module SET user_id='$bidder', price=0, dprice=0, time=0, location=1, bidder=0, fraction='$modulfraction', auctioncurrency=0 WHERE id='$id' AND location=2 AND user_id='$seller'", $soudb);
  }else{
    //es hat niemand geboten, also einfach wieder dem anbieter zur�ckgeben, bzw. die ERBAUER-Module l�schen
    echo '<br>es wurde nicht geboten';
    //dem verk�ufer den gegenstand zur�ckbuchen
    echo '<br>Verk�ufer: '.$seller;
    echo '<br>Auktions-ID: '.$id;
	if($seller>0){
		//zur�ck an den Verk�ufer
		mysql_query("UPDATE sou_ship_module SET price=0, dprice=0, time=0, location=1 WHERE id='$id' AND location=2 AND user_id='$seller'", $soudb);
	}else{
		//Modul l�schen, wenn es ein ERBAUER-Modul ist
		mysql_query("DELETE FROM sou_ship_module WHERE id='$id'", $soudb);
	}
  }
  
  //28.05.2021 issomad: deaktiviert da es seit Jahren fehlerfrei funktioniert hat
  /*
  //per e-mail info wie teuer das item verkauft wurde, wenn es als Währung um credits geht
  if($auctioncurrency==1){
  	@mail($GLOBALS['env_admin_email'], "$price C - $modulname - F$modulfraction - EA Modulverkauf - $bidder", "", 'FROM: noreply@die-ewigen.com');
  }elseif($auctioncurrency==3){
  	@mail($GLOBALS['env_admin_email'], "$price Baosin - $modulname - F$modulfraction - EA Modulverkauf - $bidder", "", 'FROM: noreply@die-ewigen.com');
  }
  */

  echo '<br><br>';
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// fraktions�bergreifende auktionen erstellen, wenn es keine gibt
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//zu einer bestimmten zeit
if(intval(strftime("%H"))==21 AND intval(strftime("%M"))==20){
	//alle module deren fraktion 0 sind, sind fraktions�bergreifende auktionen
	$sql="SELECT * FROM `sou_ship_module` WHERE fraction=0 AND quality=3";
	$result = mysql_query($sql, $soudb);
	$itemmenge = mysql_num_rows($result);
		if($itemmenge==0){
		  //item erzeugen
		  //m�glich: bonusmodule lager/reaktor/bergbau, reaktor, bergbaumodul 
		  //zeitlich begrenzt 1-6 monate
		  //auktionsdauer: 24 Stunden  
		  //auktionsende: 21:15

		  //maxdaten der erforschten technologien laden
		  $db_daten=mysql_query("SELECT MAX(giveenergy) AS giveenergy, MAX(canmine) AS canmine, MAX(hasspace) AS hasspace FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0",$soudb);
		  $moduldata = mysql_fetch_array($db_daten);

		  //modul zusammenbauen
		  $auctioncurrency=1;
		  $w=mt_rand(1,2);
		  switch ($w) {
			  case 1: //bonusmodul bergbau
			  $modul_name='Bergbaubonus Deluxe';
			  $modul_quality=3;
			  $modul_buff='1x'.mt_rand(21,30).'x'.mt_rand(30,60);
			  $modul_craftedby='ERBAUER';
			  $modul_canmine=0;
			  $modul_lifetime=0;
			  $auctiontime=time()+(24*3600)-300;

			  break;
			  /*  		
			  case 2: //bonusmodul lagerraum
			  $modul_name='Frachtraumbonus Deluxe';
			  $modul_quality=3;
			  $modul_buff='3x'.mt_rand(21,30).'x'.mt_rand(30,60);
			  $modul_craftedby='ERBAUER';
			  $modul_lifetime=0;
			  $auctiontime=time()+(24*3600)-300;

			  break;

			  case 3: //reaktor
			  $modul_name='Energymaster 2052 AUK-T';
			  $modul_quality=3;
			  $modul_craftedby='ERBAUER';
			  $modul_giveenergy=round($moduldata[giveenergy]*(mt_rand(20,30)/10));
			  $modul_needspace=100;
			  $modul_lifetime=time()+(24*3600*mt_rand(32,120));
			  $auctiontime=time()+(24*3600)-300;

			  break;
			  */
			  case 2: //bergbaumodul
			  $modul_name='Miningpower Deluxe 2052 AUK-T';
			  $modul_quality=3;
			  $modul_craftedby='ERBAUER';
			  $modul_needenergy=0;
			  $modul_needspace=0;
			  $modul_canmine=round($moduldata[canmine]*(mt_rand(20,30)/10));
			  $modul_lifetime=time()+(24*3600*mt_rand(32,120));
			  $auctiontime=time()+(24*3600)-300;
			  break;

		  }

		  //modul in der db hinterlegen
		  $sql="INSERT INTO `sou_ship_module` (`fraction` , `name` , `craftedby`, `lifetime`, `needspace` , `hasspace` , `needenergy` , 
			  `giveenergy` , `canmine` , `givelife` , `givesubspace` , `givecenter` , `givehyperdrive` , `canbldgupgrade` , `location` , `time` , 
			  `price`, `auctioncurrency`, `quality`, `buff`, `mapbuff`) VALUES ('0', '$modul_name', '$modul_craftedby', '$modul_lifetime','$modul_needspace', 
			  '$_REQUEST[ahasspace]', '$modul_needenergy', '$modul_giveenergy', '$modul_canmine', '$_REQUEST[agivelife]', 
			  '$_REQUEST[agivesubspace]', '$_REQUEST[agivecenter]', '$_REQUEST[agivehyperdrive]', '$_REQUEST[acanbldgupgrade]', '2', '$auctiontime', '1', 
			  '$auctioncurrency', '$modul_quality', '$modul_buff', '$_REQUEST[amapbuff]');";
		  mysql_query($sql, $soudb);
		  echo $sql;
	}
	
	//quality 4
	$sql="SELECT * FROM `sou_ship_module` WHERE fraction=0 AND quality=4";
	$result = mysql_query($sql, $soudb);
	$itemmenge = mysql_num_rows($result);
		if($itemmenge==0){
		  //item erzeugen
		  //m�glich: bonusmodule lager/reaktor/bergbau, reaktor, bergbaumodul 
		  //zeitlich begrenzt 1-6 monate
		  //auktionsdauer: 24 Stunden  
		  //auktionsende: 21:15

		  //maxdaten der erforschten technologien laden
		  $db_daten=mysql_query("SELECT MAX(giveenergy) AS giveenergy, MAX(canmine) AS canmine, MAX(hasspace) AS hasspace FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0",$soudb);
		  $moduldata = mysql_fetch_array($db_daten);

		  //modul zusammenbauen
		  $auctioncurrency=3;
		  $w=mt_rand(1,2);
		  switch ($w) {
			  case 1: //bonusmodul bergbau
			  $modul_name='Bergbaubonus Deluxe';
			  $modul_quality=4;
			  $modul_buff='1x'.mt_rand(25,35).'x'.mt_rand(40,70);
			  $modul_craftedby='ERBAUER';
			  $modul_canmine=0;
			  $modul_lifetime=0;
			  $auctiontime=time()+(24*3600)-300;

			  break;
			  /*  		
			  case 2: //bonusmodul lagerraum
			  $modul_name='Frachtraumbonus Deluxe';
			  $modul_quality=3;
			  $modul_buff='3x'.mt_rand(21,30).'x'.mt_rand(30,60);
			  $modul_craftedby='ERBAUER';
			  $modul_lifetime=0;
			  $auctiontime=time()+(24*3600)-300;

			  break;

			  case 3: //reaktor
			  $modul_name='Energymaster 2052 AUK-T';
			  $modul_quality=3;
			  $modul_craftedby='ERBAUER';
			  $modul_giveenergy=round($moduldata[giveenergy]*(mt_rand(20,30)/10));
			  $modul_needspace=100;
			  $modul_lifetime=time()+(24*3600*mt_rand(32,120));
			  $auctiontime=time()+(24*3600)-300;

			  break;
			  */
			  case 2: //bergbaumodul
			  $modul_name='Miningpower Deluxe 2052 AUK-T';
			  $modul_quality=4;
			  $modul_craftedby='ERBAUER';
			  $modul_buff='';
			  $modul_needenergy=0;
			  $modul_needspace=0;
			  $modul_canmine=round($moduldata[canmine]*(mt_rand(25,35)/10));
			  $modul_lifetime=time()+(24*3600*mt_rand(50,120));
			  $auctiontime=time()+(24*3600)-300;
			  break;

		  }

		  //modul in der db hinterlegen
		  $sql="INSERT INTO `sou_ship_module` (`fraction` , `name` , `craftedby`, `lifetime`, `needspace` , `hasspace` , `needenergy` , 
			  `giveenergy` , `canmine` , `givelife` , `givesubspace` , `givecenter` , `givehyperdrive` , `canbldgupgrade` , `location` , `time` , 
			  `price`, `auctioncurrency`, `quality`, `buff`, `mapbuff`) VALUES ('0', '$modul_name', '$modul_craftedby', '$modul_lifetime','$modul_needspace', 
			  '$_REQUEST[ahasspace]', '$modul_needenergy', '$modul_giveenergy', '$modul_canmine', '$_REQUEST[agivelife]', 
			  '$_REQUEST[agivesubspace]', '$_REQUEST[agivecenter]', '$_REQUEST[agivehyperdrive]', '$_REQUEST[acanbldgupgrade]', '2', '$auctiontime', '1', 
			  '$auctioncurrency', '$modul_quality', '$modul_buff', '$_REQUEST[amapbuff]');";
		  mysql_query($sql, $soudb);
		  echo $sql;
	}
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// fraktionsinterne auktion erstellen, wenn man die meisten hyperraumblasenstationen hat
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//per zufall
if(mt_rand(1,1000)==1)
{
  //feststellen wer die meisten basen hat
  $sql="SELECT owner, COUNT(*) AS anzahl FROM `sou_hbpoints` WHERE owner>0 GROUP BY owner ORDER BY anzahl DESC LIMIT 1";
  $db_daten = mysql_query($sql, $soudb);
  $row = mysql_fetch_array($db_daten);
  $zielfraction=$row['owner'];
  if($zielfraction>0 AND $zielfraction<7)
  {
    //item erzeugen
    //m�glich: bonusmodul bergbau, bergbaumodul 
    //zeitlich begrenzt 1-2 monate
    //auktionsdauer: 24 Stunden  
    
  	//maxdaten der erforschten technologien laden
  	$db_daten=mysql_query("SELECT MAX(giveenergy) AS giveenergy, MAX(canmine) AS canmine, MAX(hasspace) AS hasspace FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0",$soudb);
    $moduldata = mysql_fetch_array($db_daten);

  	//modul zusammenbauen
  	$auctioncurrency=2;
  	$w=mt_rand(1,2);
  	switch ($w) {
  		case 1: //bonusmodul bergbau
  		$modul_name='Bergbaubonus HYBL';
		$modul_quality=2;
		$modul_buff='1x'.mt_rand(5,15).'x'.mt_rand(15,30);
		$modul_craftedby='ERBAUER';
		$modul_lifetime=0;
		$auctiontime=time()+(24*3600);
		break;
		
  		case 2: //bergbaumodul
  		$modul_name='Miningpower HYBL AUK-T';
		$modul_quality=2;
		$modul_craftedby='ERBAUER';
  		$modul_needenergy=0;
  		$modul_needspace=0;
  		$modul_canmine=round($moduldata[canmine]*(mt_rand(20,25)/10));
  		$modul_lifetime=time()+(24*3600*mt_rand(16,30));
  		$auctiontime=time()+(24*3600);
  		break;
  		
  	}

  	//modul in der db hinterlegen
  	$sql="INSERT INTO `sou_ship_module` (`fraction` , `name` , `craftedby`, `lifetime`, `needspace` , `hasspace` , `needenergy` , 
  		`giveenergy` , `canmine` , `givelife` , `givesubspace` , `givecenter` , `givehyperdrive` , `canbldgupgrade` , `location` , `time` , 
  		`price`, `auctioncurrency`, `quality`, `buff`, `mapbuff`) VALUES ('$zielfraction', '$modul_name', '$modul_craftedby', '$modul_lifetime','$modul_needspace', 
  		'$_REQUEST[ahasspace]', '$modul_needenergy', '$modul_giveenergy', '$modul_canmine', '$_REQUEST[agivelife]', 
  		'$_REQUEST[agivesubspace]', '$_REQUEST[agivecenter]', '$_REQUEST[agivehyperdrive]', '$_REQUEST[acanbldgupgrade]', '2', '$auctiontime', '1', 
  		'$auctioncurrency', '$modul_quality', '$modul_buff', '$_REQUEST[amapbuff]');";
  	mysql_query($sql, $soudb);
  	echo $sql;

  	//meldung f�r die news
  	$text='<font color="#FFFF00">F'.$zielfraction.' erh&auml;lt f&uuml;r Ihre Vorherrschaft in den Hyperraumblasen eine Belohnung. Zu finden ist diese als Auktion im Auktionszentrum.</font>';
    $time=time();
    insert_chat_msg('^Der Reporter^', $text, 0, 0);
  }
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// im chat fraktions�bergreife auktionen anzeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//st�ndlich
if(intval(strftime("%M"))==0)
{
  //alle module deren fraktion 0 sind, sind fraktions�bergreifende auktionen
  $sql="SELECT * FROM `sou_ship_module` WHERE fraction=0";
  $result = mysql_query($sql, $soudb);
  $itemmenge = mysql_num_rows($result);
  if($itemmenge>0)
  {
  	//nachricht an den chat
	$time=time();
	$text='<font color="#b02ee0">Im Auktionszentrum befinden sich aktuell fraktions&uuml;bergreifende Auktionen.</font>';
    insert_chat_msg('^Der Reporter^', $text, 0, 0);
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// abgelaufene items l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll min�tlich arbeiten, also wie im cron
echo '<hr><br>Abgelaufene Items l�schen.<br>';
//abgelaufene auktionen bearbeiten
$sql="DELETE FROM sou_ship_module WHERE lifetime>0 AND lifetime < UNIX_TIMESTAMP() AND (location=0 OR location=1)";
mysql_query($sql, $soudb);

echo '<br><br>';


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// im chat umk�mpfte systeme anzeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//alle 3 stunden
if(intval(strftime("%M"))==0 AND (intval(strftime("%H")) % 3)  == 0)
{
  //f�r alle fraktionen auslesen ob es umk�mpfte sonnensysteme gibt
  $time=time()-3600*24*2;
  for($i=1;$i<=6; $i++)
  {
    $sql="SELECT COUNT(*) AS wert FROM `sou_map` WHERE fraction='$i' AND underattack>'$time'";
    $result = mysql_query($sql, $soudb);
    $row = mysql_fetch_array($result);
    $underattack[] = $row[wert];
  }
  
  if($underattack[0]>0 OR $underattack[1]>0 OR $underattack[2]>0 OR $underattack[3]>0 OR $underattack[4]>0 OR $underattack[5]>0)
  {
  	//nachricht an den chat
	$time=time();
	$text='<font color="#FF0000">Umk&auml;mpfte Sonnensysteme: </font>'; 
	for($i=0;$i<=5;$i++)
	{
	  if($underattack[$i]>0) $text.='<font color="#'.$colors_text[$i].'"> F'.($i+1).': '.$underattack[$i].'</font>';
	}
    insert_chat_msg('^Der Reporter^', $text, 0, 0);
  }
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//  fundst�cke in den sektoren hinterlegen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//f�r jede fraktion alle x minuten ein neues fundst�ck
if((strftime("%M") % 20)==0)
{
  for($i=1;$i<=6;$i++)
  {
    //zuf�llig einen bekannten sektor ausw�hlen
    $result = mysql_query("SELECT * FROM sou_map_known WHERE fraction='$i' ORDER BY RAND() LIMIT 1" , $soudb);
    $row = mysql_fetch_array($result);
    if(mt_rand(1,2)==1)$targetx=$row[x]-mt_rand(1,7);else $targetx=$row[x]+mt_rand(1,7); 
    if(mt_rand(1,2)==1)$targety=$row[y]-mt_rand(1,7);else $targety=$row[y]+mt_rand(1,7);
  
    //�berpr�fen, ob es in dem sektor schon ein fundst�ck gibt
    $brangexa=$row[x]-7;
    $brangexe=$row[x]+7;
    $brangeya=$row[y]-7;
    $brangeye=$row[y]+7;
    
    $db_daten=mysql_query("SELECT * FROM sou_map_find WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
    $finds = mysql_num_rows($db_daten);
    if($finds==0)
    {
      echo $targetx.':'.$targety.'<br>';
      mysql_query("INSERT INTO sou_map_find SET x='$targetx', y='$targety'",$soudb);
    }
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//  alte chateintr�ge l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if((strftime("%M"))==0)
{
  $time=time()-3600*24*2;
  mysql_query("DELETE FROM sou_chat_msg WHERE timestamp<'$time'",$soudb);
}



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//  ki-aktionen im chat
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//f�r jede fraktion alle x minuten ein neues fundst�ck
//if((strftime("%M") % 10)==0)
/*
if(1==1)
{
  //soll nur selten kommen
  if(mt_rand(1,120)<=1)
  {
  
    //entscheiden ob aktion (Reporter) oder sprache (Executor Karlath)
    if(mt_rand(1,3)==1) //aktion
    {
      $chattext[]='Eine Sektorraumbasis wird angegriffen. Betroffene Fraktion: '.mt_rand(1,6);
      $chattext[]='Feindliche Einheiten wurden in der N&auml;he eines Sonnensystems entdeckt, konnten aber fliehen bevor genauere Daten vorlagen.';
      $text='<font color="#00FF00">'.$chattext[mt_rand(0,count($chattext)-1)].'</font>';
	  insert_chat_msg('^Der Reporter^', $text, 0, 0);
	  echo 'aktion';
    }
    else //sprache
    {
 	  $chattext[]='Beugt Euch unserem Willen.';
 	  $chattext[]='Die Darnkristalle sind unser Eigentum, haltet Euch fern davon.';
      $chattext[]='Ihr habt keine Chance. Unterwerft Euch.';
      $chattext[]='Unsere Krieger werden Euch ausl&ouml;schen.';
      $chattext[]='Das Vergessen ist Eure Zukunft.';
      $chattext[]='Erzittert vor unserer Macht.';
      $chattext[]='Angst und Schrecken werden Eure Begleiter sein.';
      $chattext[]='Ergebt Euch und kehrt in Euer Heimatsystem zur&uuml;ck. Die Galaxie geh&ouml;rt uns.';
      $chattext[]='Es ist bekannt, dass Ihr keine Chance habt. Da wir noch andere Regionen zur&uuml;ckerobern, k&ouml;nnten wir jedoch Hilfe gebrauchen. Wir bieten einer Fraktion Immunit&auml;t an, wenn sie sich mit uns verb&uuml;ndet und hilft die anderen Fraktionen auszul�schen. Alle Fraktionsmitglieder k&ouml;nnen sich dazu mit einer Hyperfunknachricht und einer guten Begr&uuml;ndung, warum gerade Ihre Fraktion &uuml;berleben soll, an mich wenden.';
      $text=$chattext[mt_rand(0,count($chattext)-1)];
      
	  insert_chat_msg('Executor Karlath {666}', $text, 666, 0);
	  echo 'sprache';
    }
  
  }
}
*/
//Es ist bekannt, dass Ihr keine Chance habt. Da wir noch andere Regionen zur�ckerobern, k�nnten wir jedoch Hilfe gebrauchen. Wir bieten einer Fraktion Immunit�t an, wenn sie sich mit uns verb�ndet und hilft die anderen Fraktionen auszul�schen. Alle Fraktionsmitglieder k�nnen sich dazu mit einer Hyperfunknachricht und einer guten Begr�ndung, warum gerade Ihre Fraktion �berleben soll, an mich wenden. 

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    strategische karte erzeugen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede stunde
//if(intval(strftime("%M"))==0 AND intval(strftime("%H"))==3)
//st�ndlich


//große karte
if((strftime("%M") % 10)==0)
{
  function create_stratmap($filenamebig, $x1, $x2, $y1, $y2)
  {
    global $soudb, $colors_text;
  	//bild im speicher anlegen

    $im = imagecreatefrompng("../res/stratmap_ground_4500x4500.png");
    //farben definieren
	for($i=0;$i<=5;$i++)
	{
	  $farbe[$i]=imagecolorallocate($im, hexdec($colors_text[$i][0].$colors_text[$i][1]), hexdec($colors_text[$i][2].$colors_text[$i][3]), hexdec($colors_text[$i][4].$colors_text[$i][5]));
//echo hexdec($colors_text[$i][0].$colors_text[$i][1]).'<br>';
    }
    
    //dunklere farben definieren f�r die sektorraumbasen
    for($i=0;$i<=5;$i++)
	{
	  $farbedark[$i]=imagecolorallocate($im, round(hexdec($colors_text[$i][0].$colors_text[$i][1])/3), round(hexdec($colors_text[$i][2].$colors_text[$i][3])/3), round(hexdec($colors_text[$i][4].$colors_text[$i][5])/2));
    }
    
    
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
    //bekannte sektoren hervorheben
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////    
    $farbebg[0]=imagecolorallocate($im, hexdec('10'), hexdec('10'), hexdec('10'));
    $farbebg[1]=imagecolorallocate($im, hexdec('19'), hexdec('19'), hexdec('19'));
    
    $kx1=$x1;
    $kx2=$x2+1;
    $ky1=$y1;
    $ky2=$y2+1;
    
    $result = mysql_query("SELECT * FROM sou_map_known WHERE x>='$kx1' AND x<='$kx2' AND y>='$ky1' AND y<='$ky2'" , $soudb);
	while($row = mysql_fetch_array($result))
	{
  	  
  	  $x=$row["x"]-$x1;
  	  $y=4499-($row["y"]-$y1);
  	  
  	  $sx1=$x-7;
  	  $sx2=$x+7;

  	  $sy1=$y-7;
  	  $sy2=$y+7;  	  
  	  
  	  //if($x%2 == 0 AND $y%2 == 0)$f=0; else $f=1;
  	  $swx=$row["x"];
  	  $swy=$row["y"];
  	  if($swx<0)$swx=$swx*(-1);
  	  if($swy<0)$swy=$swy*(-1);
  	  
  	  if($swx%2 == $swy%2)$f=0; else $f=1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$swx.':'.$swy.' -> '.$f.'</font><br>';
  	  
  	  $mapknowncounter++;
  	  imagefilledrectangle($im, $sx1, $sy1, $sx2, $sy2, $farbebg[$f]);
	}
	
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
    //sektorraumbasen darstellen
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
	$result = mysql_query("SELECT * FROM sou_map_base WHERE x>='$x1' AND x<='$x2' AND y>='$y1' AND y<='$y2' AND special=0 AND fraction>0" , $soudb);
	while($row = mysql_fetch_array($result)){
  	  //daten auslesen
  	  //$x=$row["x"]+1000;
  	  //$y=$row["y"]*-1+1000;
  	  
  	  $x=$row["x"]-$x1;
  	  $y=4499-($row["y"]-$y1);
  	  
	  //$x=$x1-$row["x"];
  	  //$y=$y1-$row["y"];
  	  
  	  $f=$row["fraction"]-1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$x.'('.$row["x"].'):'.$y.'('.$row["y"].') - '.$f.'</font><br>';
  	  
  	  $starcounter++;
  	  imagesetpixel($im, $x, $y, $farbedark[$f]);  
  	  //imagesetpixel($im, 375, 375, $farbe[0]);
	}
	
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
	//mapdaten aus der db holen und auswerten
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
	$result = mysql_query("SELECT * FROM sou_map WHERE x>='$x1' AND x<='$x2' AND y>='$y1' AND y<='$y2' AND fraction>0" , $soudb);
	while($row = mysql_fetch_array($result)){
  	  //daten auslesen
  	  //$x=$row["x"]+1000;
  	  //$y=$row["y"]*-1+1000;
  	  
  	  $x=$row["x"]-$x1;
  	  $y=4499-($row["y"]-$y1);
  	  
	  //$x=$x1-$row["x"];
  	  //$y=$y1-$row["y"];
  	  
  	  $f=$row["fraction"]-1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$x.'('.$row["x"].'):'.$y.'('.$row["y"].') - '.$f.'</font><br>';
  	  
  	  $starcounter++;
  	  imagesetpixel($im, $x, $y, $farbe[$f]);  
  	  //imagesetpixel($im, 375, 375, $farbe[0]);
	}
	
	//bild auf die platte schreiben
	
    imagepng($im, "../cache/".$filenamebig);
    
    /*
    //bild verkleinern für minimap
    $new_width=250;
    $new_height=250;
	$image_p = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($image_p, $im, 0, 0, 0, 0, $new_width, $new_height, imagesx($im), imagesy($im));    

	//bildkonstrast verbessern
	if (imageistruecolor($image_p)) {
        imagetruecolortopalette($image_p, false, 256);
    }
    $total = imagecolorstotal( $image_p );
    for ( $i = 0; $i < $total; $i++ ) 
    {
      $c = ImageColorsForIndex( $image_p, $i );
      if($c["red"]>10 OR $c["green"]>10 OR $c["blue"]>10)
      {
        imagecolorset( $image_p, $i, 200, 200, 200 );
        
        //print_r($c);
        //echo '<br><br><br><br>';
      }
      //else
      //imagecolorset( $image_p, $i, 255, 255, 255 );
    }
	
	
	//bild auf die platte schreiben
    imagepng($image_p, "../cache/".$filenamesmall);
    */
    
    echo 'Starcounter: '.$starcounter;
    echo '<br>Sektorcounter: '.$mapknowncounter;
  }
	
  create_stratmap('stratmap0.png', -2250, 2250, -2250, 2250);
  

  
/*
  function create_stratmap($filename,$x1, $x2, $y1, $y2)
  {
    global $soudb, $colors_text;
  	//bild im speicher anlegen

    $im = imagecreatefrompng("../res/stratmap_ground.png");

    //farben definieren
	for($i=0;$i<=5;$i++)
	{
	  $farbe[$i]=imagecolorallocate($im, hexdec($colors_text[$i][0].$colors_text[$i][1]), hexdec($colors_text[$i][2].$colors_text[$i][3]), hexdec($colors_text[$i][4].$colors_text[$i][5]));
//echo hexdec($colors_text[$i][0].$colors_text[$i][1]).'<br>';
    }
	
    //bekannte sektoren hervorheben
    $farbebg[0]=imagecolorallocate($im, hexdec('10'), hexdec('10'), hexdec('10'));
    $farbebg[1]=imagecolorallocate($im, hexdec('19'), hexdec('19'), hexdec('19'));
    
    $kx1=$x1;
    $kx2=$x2+1;
    $ky1=$y1;
    $ky2=$y2+1;
    
    $result = mysql_query("SELECT * FROM sou_map_known WHERE x>='$kx1' AND x<='$kx2' AND y>='$ky1' AND y<='$ky2'" , $soudb);
	while($row = mysql_fetch_array($result))
	{
  	  
  	  $x=$row["x"]-$x1;
  	  $y=749-($row["y"]-$y1);
  	  
  	  $sx1=$x-7;
  	  $sx2=$x+7;

  	  $sy1=$y-7;
  	  $sy2=$y+7;  	  
  	  
  	  //if($x%2 == 0 AND $y%2 == 0)$f=0; else $f=1;
  	  $swx=$row["x"];
  	  $swy=$row["y"];
  	  if($swx<0)$swx=$swx*(-1);
  	  if($swy<0)$swy=$swy*(-1);
  	  
  	  if($swx%2 == $swy%2)$f=0; else $f=1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$swx.':'.$swy.' -> '.$f.'</font><br>';
  	  
  	  $mapknowncounter++;
  	  imagefilledrectangle($im, $sx1, $sy1, $sx2, $sy2, $farbebg[$f]);
	}
    
	//mapdaten aus der db holen und auswerten
	$result = mysql_query("SELECT * FROM sou_map WHERE x>='$x1' AND x<='$x2' AND y>='$y1' AND y<='$y2' AND fraction>0" , $soudb);
	while($row = mysql_fetch_array($result))
	{
  	  //daten auslesen
  	  //$x=$row["x"]+1000;
  	  //$y=$row["y"]*-1+1000;
  	  
  	  $x=$row["x"]-$x1;
  	  $y=749-($row["y"]-$y1);
  	  
	  //$x=$x1-$row["x"];
  	  //$y=$y1-$row["y"];
  	  
  	  $f=$row["fraction"]-1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$x.'('.$row["x"].'):'.$y.'('.$row["y"].') - '.$f.'</font><br>';
  	  
  	  $starcounter++;
  	  imagesetpixel($im, $x, $y, $farbe[$f]);  
  	  //imagesetpixel($im, 375, 375, $farbe[0]);
	}
    imagepng($im, "../cache/".$filename);
    echo 'Starcounter: '.$starcounter;
    echo '<br>Sektorcounter: '.$mapknowncounter;
  }
  //bilder erzeugen
  //create_stratmap('1.png', -10, 10, -10, 10);
  $picid=31;
  for($ya=-2250;$ya<=1500;$ya=$ya+750)
  {
  	for($xa=-2250;$xa<=1500;$xa=$xa+750)
  	{
  	  echo '<hr>'.$xa.' : '.$ya.'<br>';
  	  echo '<br>'.$picid.'. ';
  	  create_stratmap($picid.'.png', $xa, $xa+749, $ya, $ya+749);
  	  $picid++;
  	}
  	$picid-=12;
  }
*/
}


//kleine karte
if(intval(strftime("%M"))==37)
{
  function create_stratmapmini($filename, $x1, $x2, $y1, $y2)
  {
    global $soudb, $colors_text;
  	//bild im speicher anlegen

    $im = imagecreatefrompng("../res/stratmap_ground_300x300.png");

    //farben definieren
	for($i=0;$i<=5;$i++)
	{
	  $farbe[$i]=imagecolorallocate($im, hexdec($colors_text[$i][0].$colors_text[$i][1]), hexdec($colors_text[$i][2].$colors_text[$i][3]), hexdec($colors_text[$i][4].$colors_text[$i][5]));
//echo hexdec($colors_text[$i][0].$colors_text[$i][1]).'<br>';
    }
    
    //dunklere farben definieren f�r die sektorraumbasen
    for($i=0;$i<=5;$i++)
	{
	  $farbedark[$i]=imagecolorallocate($im, round(hexdec($colors_text[$i][0].$colors_text[$i][1])/3), round(hexdec($colors_text[$i][2].$colors_text[$i][3])/3), round(hexdec($colors_text[$i][4].$colors_text[$i][5])/2));
    }
    
    
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
    //bekannte sektoren hervorheben
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////    
    $farbebg[0]=imagecolorallocate($im, hexdec('10'), hexdec('10'), hexdec('10'));
    $farbebg[1]=imagecolorallocate($im, hexdec('19'), hexdec('19'), hexdec('19'));
    
    $kx1=$x1;
    $kx2=$x2+1;
    $ky1=$y1;
    $ky2=$y2+1;
    
    $result = mysql_query("SELECT * FROM sou_map_known WHERE x>='$kx1' AND x<='$kx2' AND y>='$ky1' AND y<='$ky2'" , $soudb);
	while($row = mysql_fetch_array($result))
	{
  	  
  	  $x=$row["x"]-$x1;
  	  $y=4499-($row["y"]-$y1);
  	  
  	  $sx1=$x-7;
  	  $sx2=$x+7;

  	  $sy1=$y-7;
  	  $sy2=$y+7;  	  
  	  
  	  //if($x%2 == 0 AND $y%2 == 0)$f=0; else $f=1;
  	  $swx=$row["x"];
  	  $swy=$row["y"];
  	  if($swx<0)$swx=$swx*(-1);
  	  if($swy<0)$swy=$swy*(-1);
  	  
  	  //if($swx%2 == $swy%2)$f=0; else $f=1;
  	  //kl�ren wem der sektor geh�rt
  	  $f=get_sector_owner(intval($row[x]/15), intval($row[y]/15))-1;
  	  
  	  //echo '<font color="'.$colors_text[$f].'">'.$swx.':'.$swy.' -> '.$f.'</font><br>';
  	  
  	  $mapknowncounter++;
  	  imagesetpixel($im, intval($x/15), intval($y/15), $farbe[$f]);
  	  //imagefilledrectangle($im, $sx1, $sy1, $sx2, $sy2, $farbebg[$f]);
	}
	
	
	//bild auf die platte schreiben
    imagepng($im, "../cache/".$filename);
    
    echo 'Starcounter: '.$starcounter;
    echo '<br>Sektorcounter: '.$mapknowncounter;
  }
	
  create_stratmapmini('stratmap1.png', -2250, 2250, -2250, 2250);
}
?>
</body>
</html>