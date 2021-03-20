<?php
include "soudata/defs/resources.inc.php";

//hyperdrivedaten auslesen 
$db_daten=mysql_query("SELECT MAX(givehyperdrive) AS givehyperdrive FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);
//die reichweite entspricht dem modulwert in der rubrik
$reichweite=intval($row["givehyperdrive"]*2);
$speed=calc_hyperdrive_speed(intval($row["givehyperdrive"]));

//überprüfen ob man sich in einem sonnensystem der eigenen fraktion mit hyperraumblase befindet
$system1hb=0;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$player_x' AND y='$player_y' AND fraction='$player_fraction'",$soudb);
if(mysql_num_rows($db_daten)==1)
{
  $row = mysql_fetch_array($db_daten);
  $system1hb=get_bldg_level($row['id'], 13);
}

//koordinatentransformation
$showx=round($player_x/15)*15;
$showy=round($player_y/15)*15;

//daten des aktuellen sektors in dem man sich befindet auslesen
//sonnensysteme
$brangexa=$showx-7;
$brangexe=$showx+7;
$brangeya=$showy-7;
$brangeye=$showy+7;

$db_daten=mysql_query("SELECT * FROM sou_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
$anzahl_sonnensysteme = mysql_num_rows($db_daten);
//auf sektorraumbasis überprüfen
$db_daten=mysql_query("SELECT * FROM sou_map_base WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
$anzahl_srb = mysql_num_rows($db_daten);

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//überprüfen ob man eine srb bauen möchte
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
if($_REQUEST["buildbase"]==1)
{
	//baukosten berechnen
	$sbx=round($player_x/15);
	$sby=round($player_y/15);

	//berechnen wie teuer die expedition ist
	$ekosten = array (244400,306802,359255,417052,600925,688646,785306,1070179,1211014,1366201,1793402,2013231,2577670,2882713,3621196,4037873,4996676,5558812,6796051,7546840,9135420,10974045,12161167,14505352,17204583,19037192,22460312,26385887,30880233,36017965,41882995,48569662,56183973,64845010,74686495, 85858527,98529544,117069547,133759663,157718804,179566008,210410382,245680212,278430892,323593544,375070444,433678404,500335139,588328184,675555314);

	//entfernung zum nächsten nullpunkt berechnen
	$s1=$sv_sou_galcenter[0][0]-$sbx*15;
	$s2=$sv_sou_galcenter[0][1]-$sby*15;
	if($s1<0)$s1=$s1*(-1);
	if($s2<0)$s2=$s2*(-1);
	$s1=pow($s1,2);
	$s2=pow($s2,2);
	$w1=$s1+$s2;
	$entfernung=sqrt($w1);

	//die kosten in abhängigkeit zur entfernung berechnen
	$teiler=$sv_sou_galcenter[0][2]/50;

	$kostenstelle=round($entfernung/$teiler);
	if($kostenstelle>49)$kostenstelle=49;
	$kostenstelle=49-$kostenstelle;

	$gesamtkosten=round($ekosten[$kostenstelle]/2);	
  
	//überprüfen ob eine srb baubar ist: keine sonnensystem und keine andere srb dürfen vorhanden sein
	if($anzahl_sonnensysteme==0 AND $anzahl_srb==0)
	{
		//überprüfen ob der nachbarsektor einen bau ermöglicht

	if(get_sector_owner($sbx, $sby+1)==$player_fraction OR get_sector_owner($sbx+1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby-1)==$player_fraction OR get_sector_owner($sbx-1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==999 OR get_sector_owner($sbx+1, $sby)==999 OR get_sector_owner($sbx, $sby-1)==999 OR get_sector_owner($sbx-1, $sby)==999)
	{
	  //überprüfen ob man eine aktion bzlg. timer1 durchführen kann
	  if($player_atimer1time<time())
	  {
  	    //transaktionsbeginn
  	    if (setLock($_SESSION["sou_user_id"]))
  	    {
  	  	  //benötigte zastari
  	  	  $spende=$gesamtkosten;
  	  	  //auslesen wieviel er hat
  		  $hasmoney=has_money($player_user_id);
  		  
  		  if($hasmoney>=$spende)
  		  {
			//überprüfen ob man im sektor ist
			if($player_x>=$brangexa AND $player_x<=$brangexe AND $player_y>=$brangeya AND $player_y<=$brangeye)
			{
	          //geld vom konto abziehen
		      $player_money-=$spende;
		      change_money($_SESSION["sou_user_id"], $spende*-1);
		
		      //bezahlt updaten
		      $einbezahlt+=$spende;
		
	          //dieses als spende notieren
		      $player_donate+=$spende;
		      mysql_query("UPDATE `sou_user_data` SET donate=donate+'$spende' WHERE user_id='$player_user_id'",$soudb);
		
		      //die srb bauen
		      //zeit auslesen
		      $db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
		      $row = mysql_fetch_array($db_daten);
		      $jahr=$row["year"];
		      $time=time();
		  
		      mysql_query("INSERT INTO sou_map_base (fraction, x, y, pic, bldgyear, bldgtime, prestige$player_fraction) VALUES ($player_fraction, '$showx', '$showy', '1', '$jahr', '$time', '$gesamtkosten')",$soudb);
		  
	          //meldung für den chat hinzufügen
      	      $text='<font color="#00FF00">Fraktion '.$_SESSION["sou_fraction"].' hat in Sektor '.$showx.'/'.$showy.' eine Sektorraumbasis erbaut.</font>';
      	      $time=time();
      	      insert_chat_msg('^Der Reporter^', $text, 0, 0);
      
	  	      //meldung in der fractionsnews hinterlegen
	  	      $text='Neue Sektorraumbasis: '.$player_x.'/'.$player_y;
	  	      mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$player_fraction',5, '$text')",$soudb);
	  	    
	  	      $errmsg='Die Sektorraumbasis wurde erbaut.<br><br>';
			}
			
  		  }
  		  else 
  		  {
  		    $errmsg='Du hast nicht genug Zastari f&uuml;r den Bau der Sektorraumbasis.<br><br>';
  		  }
    	  //lock wieder entfernen
    	  $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    	  if ($erg)
    	  {
      	    //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    	  }
    	  else
    	  {
      	    print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    	  }

  	    }//lock ende
  	    else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
	  }
	  else $errmsg='Du f&uuml;hrst bereits eine andere Aktion durch.';
    }
    else $errmsg='Du ben&ouml;tigst einen Nachbar-/Sektor deiner Fraktion, oder einen der ERBAUER.';
  }
}


  /////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////
  //evtl. wurde ein reisebefehl gegeben
  /////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////
if($_GET["tx"]!='' AND$_GET["ty"]!='')
{
  //überprüfen ob man eine aktion bzlg. timer1 durchführen kann
  if($player_atimer1time<time())
  {
    $tx=(int)$_GET["tx"];
    $ty=(int)$_GET["ty"];
    //überprüfen ob der zielsektor bekannt ist
    $zielsekx=round($tx/15)*15;
    $zielseky=round($ty/15)*15;
	$db_daten=mysql_query("SELECT fraction FROM sou_map_known WHERE x='$zielsekx' AND y='$zielseky' AND fraction='$player_fraction'",$soudb);
	$num = mysql_num_rows($db_daten);
	if($num==1)
	{
      //reisezeit hyperraumflug berechnen
      $s1=$player_x-$tx;
      $s2=$player_y-$ty;
      if($s1<0)$s1=$s1*(-1);
      if($s2<0)$s2=$s2*(-1);
      $s1=pow($s1,2);
      $s2=pow($s2,2);
      $w1=$s1+$s2;
      $w3=sqrt($w1);
      if($w3>0)
      {
        $rz=round(120+$w3*$speed+120);
        //$rz=2;
      }
      else $rz=0;
      
      //überprüfen ob ggf. eine reise per hyperraumblase besser ist
	  $hashblevel=0;
	  $targethblevel=0;
	
      $db_daten=mysql_query("SELECT id FROM sou_map WHERE x='$player_x' AND y='$player_y' AND fraction='$player_fraction'",$soudb);
      $num = mysql_num_rows($db_daten);
      if($num==1)
      {
        $row = mysql_fetch_array($db_daten);
        $hashblevel=get_bldg_level($row['id'], 13);
      }
	  $db_daten=mysql_query("SELECT id FROM sou_map WHERE x='$tx' AND y='$ty' AND fraction='$player_fraction'",$soudb);
      $num = mysql_num_rows($db_daten);
      if($num==1)
      {
        $row = mysql_fetch_array($db_daten);
        $targethblevel=get_bldg_level($row['id'], 13);
      }
      $rzhb=9999999999;
	  if($targethblevel>0 AND $hashblevel>0)$rzhb=7200-$hashblevel*60-$targethblevel*60;

      //wenn reisezeit > 0 und kleiner als die antriebsreichweite, dann schiff starten
      if($reichweite>0)
      {
        if($rz>0 AND $reichweite>=$w3 AND $rz<$rzhb)
        {
          $time=time()+$rz;
          mysql_query("UPDATE sou_user_data SET atimer1typ=2, atimer1time='$time', x='$tx', y='$ty' WHERE user_id='$player_user_id'",$soudb);
   	      header("Location: sou_main.php");
        }
        //evtl. geht aber die hyperraumblase
        elseif($rzhb<>9999999999)
        {
          //reichweitenbeschränkung der hyperraumblasenreise überprüfen
          if($w3<5000)
          {
          	//überprüfen ob die neuen koordinaten sich auch woanders befinden
          	if($player_x!=$tx OR $player_y!=$ty)
          	{
              $time=time()+$rzhb;
              mysql_query("UPDATE sou_user_data SET atimer1typ=4, atimer1time='$time', x='$tx', y='$ty' WHERE user_id='$player_user_id'",$soudb);
   	          header("Location: sou_main.php");
          	}
          }
          else $errmsg= '<font color="#FF0000">Es konnte keine passende Hyperraumblase in Reichweite f&uuml;r einen Transfer lokalisiert werden.</font>';
        }
      }
      else $errmsg= '<font color="#FF0000">Ohne &Uuml;berlichtantrieb kann diese Technologie nicht genutzt werden.</font>';
	}
	else $errmsg= '<font color="#FF0000">Dieser Sektor ist Deiner Fraktion nicht bekannt.</font>';
  }
  else $errmsg= '<font color="#FF0000">Du f&uuml;hrst bereits eine andere Aktion durch.</font>';
}

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
// sektor erforschen
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['ex']) AND isset($_REQUEST['ey']) AND isset($_REQUEST['ecost']))
{
  //überprüfen ob man eine aktion bzlg. timer1 durchführen kann
  if($player_atimer1time<time())
  {

  //überprüfen ob sich der spieler im nachbarsektor aufhält
  $pbx=round($player_x/15);
  $pby=round($player_y/15);
  
  $sbx=round($_REQUEST['ex']/15);
  $sby=round($_REQUEST['ey']/15);
  $searchx=$sbx*15;
  $searchy=$sby*15;
  
  $erforscht=0;
  
  $isthere=0;
  if(($pbx>=$sbx-1 AND $pbx<=$sbx+1 AND $pby>=$sby-1 AND $pby<=$sby+1))$isthere=1;else $isthere=0;

  if($isthere==1)
  {
    //überprüfen ob eine expedition bzgl. nachbarsektor möglich ist
    if((get_sector_owner($sbx-1, $sby+1)==$player_fraction OR 
    	get_sector_owner($sbx, $sby+1)==$player_fraction OR 
    	get_sector_owner($sbx+1, $sby+1)==$player_fraction OR 
    	
    	get_sector_owner($sbx-1, $sby)==$player_fraction OR
        get_sector_owner($sbx+1, $sby)==$player_fraction OR

     	get_sector_owner($sbx-1, $sby-1)==$player_fraction OR 
    	get_sector_owner($sbx, $sby-1)==$player_fraction OR 
    	get_sector_owner($sbx+1, $sby-1)==$player_fraction OR 
        
        
    	get_sector_owner($sbx-1, $sby+1)==999 OR
    	get_sector_owner($sbx, $sby+1)==999 OR
    	get_sector_owner($sbx+1, $sby+1)==999 OR
    	
    	get_sector_owner($sbx-1, $sby)==999 OR
    	get_sector_owner($sbx+1, $sby)==999 OR
    	
    	get_sector_owner($sbx-1, $sby-1)==999 OR 
    	get_sector_owner($sbx, $sby-1)==999 OR 
    	get_sector_owner($sbx+1, $sby-1)==999))
    {
      //überprüfen ob der sektor evtl. schon erforscht worden ist
      $db_daten=mysql_query("SELECT * FROM sou_map_known WHERE x='$searchx' AND y='$searchy' AND fraction='$player_fraction'",$soudb);
      $anzahl_known = mysql_num_rows($db_daten);
    
      if($anzahl_known==0)
      {
        //berechnen wie teuer die expedition ist
      	$ekosten = array (244400,306802,359255,417052,600925,688646,785306,1070179,1211014,1366201,1793402,2013231,2577670,2882713,3621196,4037873,4996676,5558812,6796051,7546840,9135420,10974045,12161167,14505352,17204583,19037192,22460312,26385887,30880233,36017965,41882995,48569662,56183973,64845010,74686495, 85858527,98529544,117069547,133759663,157718804,179566008,210410382,245680212,278430892,323593544,375070444,433678404,500335139,588328184,675555314);
    
	    //entfernung zum nächsten nullpunkt berechnen
        $s1=$sv_sou_galcenter[0][0]-$sbx*15;
        $s2=$sv_sou_galcenter[0][1]-$sby*15;
        if($s1<0)$s1=$s1*(-1);
        if($s2<0)$s2=$s2*(-1);
        $s1=pow($s1,2);
        $s2=pow($s2,2);
        $w1=$s1+$s2;
        $entfernung=sqrt($w1);
    
        //die kosten in abhängigkeit zur entfernung berechnen
        $teiler=$sv_sou_galcenter[0][2]/50;
    
        $kostenstelle=round($entfernung/$teiler);
        if($kostenstelle>49)$kostenstelle=49;
        $kostenstelle=49-$kostenstelle;
    
        $gesamtkosten=round($ekosten[$kostenstelle]/2);
    
  	    //fraktionskasse auslesen
	    $feldname='f'.$player_fraction.'money';
	    $db_daten=mysql_query("SELECT $feldname AS wert FROM `sou_system`",$soudb);
	    $row = mysql_fetch_array($db_daten);
	    $fracmoney=$row['wert'];
	
	    if($_REQUEST['ecost']==1)//die expeditionskosten selbst zahlen
	    {
	      $needmoney=$gesamtkosten;
	    
	      if($player_money>=$needmoney)
	      {
	  	    $erforscht=1;
	  	  
  	  	    //dem spieler das geld abziehen
	  	    change_money($player_user_id, $needmoney*-1);
		  
	  	    //donate erhöhen
		    mysql_query("UPDATE `sou_user_data` SET donate=donate+'$needmoney' WHERE user_id='$player_user_id'",$soudb);
	  	  }
	    }
	    else //die expeditionskosten für den sektor aus der fraktionskasse bezahlen
	    {
	      $needmoney=$gesamtkosten;

	      if($fracmoney>=$needmoney)
	      {
		    $erforscht=1;    	
  	  	    //fraktionskasse updaten
	        $feldname='f'.$player_fraction.'money';
		    mysql_query("UPDATE `sou_system` SET $feldname=$feldname-'$needmoney'",$soudb);		
	      }
	    }
	
        //erforschen sektor in der db hinterlegen
        if($erforscht==1)
        {
          //datensatz für den sektor anlegen
          $time=time();
          mysql_query("INSERT INTO sou_map_known (fraction, x, y, expltime) VALUES ($player_fraction, '$searchx', '$searchy', '$time')",$soudb);
        
	      //meldung für den chat hinzufügen
          $text='<font color="#00FF00">Fraktion '.$_SESSION["sou_fraction"].' hat den Sektor '.$searchx.'/'.$searchy.' entdeckt.</font>';
          $time=time();
          insert_chat_msg('^Der Reporter^', $text, 0, 0);
      
          //meldung in der fractionsnews hinterlegen
	      $text='Neuer Sektor: '.$searchx.'/'.$searchy;
	      mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$player_fraction',4, '$text')",$soudb);
	  
          //tempfile löschen
  	      $filename='soudata/cache/showdata1_'.$player_fraction.'.tmp';
  	      if (file_exists($filename))unlink($filename);

  	      $errmsg='Der Sektor wurde erforscht.';
        }
        else $errmsg='Es sind nicht genug Zastari vorhanden.';
      }
      else $errmsg='Der Sektor wurde bereits erforscht.';
    }
    else $errmsg='Es fehlt ein Nachbarsektor Deiner Fraktion, bzw. ein neutraler Nachbarsektor um die Expedition starten zu k&ouml;nnen.';
  }
  else $errmsg='Du bist zu weit entfernt.';
  
  }
  else $errmsg= '<font color="#FF0000">Du f&uuml;hrst bereits eine andere Aktion durch.</font>';
}

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////  
// ggf. eine fehlermeldung ausgeben
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
if($errmsg!='')
{ 
  echo '<br>';
  rahmen2_oben();
  echo $errmsg.' <a href="sou_main.php?action=sectorpage"><div class="b1">Sektor</div></a>';
  rahmen2_unten();
  echo '<br>';

  echo '</div>';//center-div
  die('</body></html>');
} 
  
//man kommt von der strategischen karte, oder einer anderen seite die koordinaten übergibt
if(isset($_REQUEST['smx']) AND isset($_REQUEST['smy']))
{
  $showx=round($_REQUEST['smx']/15)*15;
  $showy=round($_REQUEST['smy']/15)*15;
}

//navigationskoordinaten
if($_GET["rhtx"]!='' AND$_GET["rhty"]!='')
{
  //zielkoordinaten
  $rhtx=(int)$_GET["rhtx"];
  $rhty=(int)$_GET["rhty"];
  
  //zielkoordinaten in der db hinterlegen
  mysql_query("UPDATE sou_user_data SET rhx='$rhtx', rhy='$rhty', rhuse=1 WHERE user_id='$player_user_id'",$soudb);
  $player_rhuse=1;
  $player_rhx=$rhtx;
  $player_rhy=$rhty;

  $showx=round($rhtx/15)*15;
  $showy=round($rhty/15)*15;
}	

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//reisehilfe
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if($player_rhuse==1)
{
  $rhtext='';
  if    ($player_rhx == $player_x AND $player_rhy >  $player_y)$rhtext=18;//N
  elseif($player_rhx >  $player_x AND $player_rhy >  $player_y)$rhtext=19;//NO
  elseif($player_rhx >  $player_x AND $player_rhy == $player_y)$rhtext=20;//O
  elseif($player_rhx >  $player_x AND $player_rhy <  $player_y)$rhtext=21;//SO
  elseif($player_rhx == $player_x AND $player_rhy <  $player_y)$rhtext=22;//S
  elseif($player_rhx <  $player_x AND $player_rhy <  $player_y)$rhtext=23;//SW
  elseif($player_rhx <  $player_x AND $player_rhy == $player_y)$rhtext=24;//W
  elseif($player_rhx <  $player_x AND $player_rhy >  $player_y)$rhtext=25;//NW
  else 
  {
    //$rhtext='DA';
    //man ist am ziel angekommen, reisehilfe deaktivieren
    mysql_query("UPDATE sou_user_data SET rhuse=0 WHERE user_id='$player_user_id'",$soudb);
    $player_rhuse=0;
  }

  //tooltip für die reisehilfe bauen
  if($player_rhuse==1)
  {
    //entfernung berechnen
    $s1=$player_x-$player_rhx;
    $s2=$player_y-$player_rhy;
    if($s1<0)$s1=$s1*(-1);
    if($s2<0)$s2=$s2*(-1);
    $s1=pow($s1,2);
    $s2=pow($s2,2);
    $w1=$s1+$s2;
    $w3=sqrt($w1);
  
    $title='Reisehilfe&Zielkoordinaten: '.$player_rhx.':'.$player_rhy.'<br>Entfernung zur aktuellen Position:<br>X: '.($player_rhx-$player_x).'<br>Y: '.($player_rhy-$player_y).'<br>Lichtjahre: '.number_format($w3, "2",",",".").'<br><br>Zur Deaktivierung der Funktion bei der Reisehilfe die eigenen Koordinaten als Ziel angeben."];';
    $rhtext='&nbsp;<img id="tt4" src="'.$gpfad.'a'.$rhtext.'.gif" width="16px" height="16px" title="'.$title.'">';
  }
}
else $rhtext='';

//ajax-loader-symbol
echo '<div id="ajaxloader" style="position:absolute; overflow:hidden; visibility: hidden; left: 48%; top: 40px; border: solid 1px #666666; background-color: #FFFFFF; width: 31px; height: 31px;  z-index:10;"><img src="'.$gpfad.'progress.gif" width="100%" height="100%"></div>';

//buttons für schiff/system/strategische Karte
echo '
<div style="position:absolute; overflow:hidden; left: 2px; top: 26px; width: 50px; z-index:11; background-color: #000000;">
<a title="&Zur Systemansicht wechseln" href="sou_main.php?action=systempage"><img style="border: 1px solid #888888" src="'.$gpfad.'sym7.png" width="48px" height="48px"></a>
<a title="&Zum Sektor in dem sich Dein Raumschiff befindet" onclick=\'show_map('.(round($player_x/15)*15).', '.(round($player_y/15)*15).');\'><img style="border: 1px solid #888888" src="'.$gpfad.'v1.png" width="48px" height="48px"></a>
<a title="&Zur strategischen Karte wechseln" href="sou_main.php?action=stratmappage"><img style="border: 1px solid #888888" src="'.$gpfad.'sym9.png" width="48px" height="48px"></a>';
echo '</div>';

//kompletten div über die seite
echo '<div id="mapcontainer" style="position:absolute; top: 0px; left: 0px; width:100%; height:100%; overflow:hidden; background-color: #000000; z-index:0;
background-image: url('.$gpfad.'bgpic9.jpg);background-repeat:repeat;">';

  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////  
  //obere infoleister
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////

  echo '<div id="infobar1" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'bgpic7.png);left: 0px; top: 0px; width: 100%; height: 24px; z-index:3;"></div>';
  
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////  
  //hauptfenster
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////
  
  echo '<div id="mainarea" class="rahmen0a" style="position:absolute; overflow:auto; width: 880px; top: 26px; z-index:20; visibility: hidden;"></div>';  

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
// kurzinformationen //reiseziel
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
echo '
<div style="position:absolute; overflow:hidden; right: 42px; top: 26px; width: 230px; z-index: 11;">';
rahmen1_oben('<div align="center"><b>'.$rhtext.' Kurzinformation</b></div>');
echo '<div id="kurzinfo" align="left" class="cell1">&nbsp;</div>';
rahmen1_unten();

echo '<br>';


rahmen1_oben('<div align="center"><b>Reiseziel</b></div>');
echo '<div id="rz" align="left" class="cell1">Auf die Karte klicken um ein Ziel auszuw&auml;hlen</div>';
rahmen1_unten();
echo '</div>';
  
  
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////  
  //untere menüleiste
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////

  echo '<div id="menubar1" style="position:absolute; overflow:hidden; left: 0px; bottom: 0px; z-index:3;">';
  echo '<div id="menu1" title="Infocenter" style="height: 42px; width: 42px; float: left;")><a OnClick="(lnk(\'action=infocenter\'));"><img border="0" src="'.$gpfad.'sym12.png" width="100%" height="100%"></a></div>';  
  echo '<div id="menu2" title="Fraktionsaufgaben" style="height: 42px; width: 42px; float: left;"><a OnClick="(lnk(\'action=fracquests\'));"><img border="0" src="'.$gpfad.'sym13.png" width="100%" height="100%"></a></div>';

  //überprüfen ob die forschung aktiv ist, pa feature
  if($ums_premium==1 AND $player_atimer2time<time())
  {
    //nur anzeigen, wenn ein forschungsmodul vorhanden ist
    $db_daten=mysql_query("SELECT MAX(giveresearch) AS giveresearch FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
    $row = mysql_fetch_array($db_daten);

    if($row["giveresearch"]>0)
    {
      echo '<div id="menu3" title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym14.png" width="100%" height="100%"></a></div>';  
    }
    else echo '<div id="menu3" title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym15.png" width="100%" height="100%"></a></div>';
  }
  else echo '<div id="menu3" title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym15.png" width="100%" height="100%"></a></div>'; 
  
  echo '<div id="menu4" title="Schiffs&uuml;bersicht" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipoverpage"><img border="0" src="'.$gpfad.'sym16.png" width="100%" height="100%"></a></div>';  
  echo '<div id="menu5" title="Auktionszentrum" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=auctionpage"><img border="0" src="'.$gpfad.'sym17.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu6" title="Fabriken" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=factorypage"><img border="0" src="'.$gpfad.'sym18.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu7" title="Hyperfunk" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=hyperfunk"><img border="0" src="'.$gpfad.'sym19.png" width="100%" height="100%"></a></div>';  
  echo '<div id="menu8" title="Fraktionsforum" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=fracforumpage"><img border="0" src="'.$gpfad.'sym20.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu9" title="Squad" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=squad"><img border="0" src="'.$gpfad.'sym21.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu10" title="Fraktionsdaten" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=toplistpage"><img border="0" src="'.$gpfad.'sym22.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu11" title="Hilfe" style="height: 42px; width: 42px; float: left;"><a href="http://help.bgam.es/index.php?thread=abl_de" target="_blank"><img border="0" src="'.$gpfad.'sym23.png" width="100%" height="100%"></a></div>';
  echo '<div id="menu12" title="Optionen" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=optionspage"><img border="0" src="'.$gpfad.'sym24.png" width="100%" height="100%"></a></div>';
  //srb baubar?
	if($anzahl_sonnensysteme==0 AND $anzahl_srb==0){
	
		//baukosten berechnen
		$sbx=round($player_x/15);
		$sby=round($player_y/15);

		//berechnen wie teuer die expedition ist
		$ekosten = array (244400,306802,359255,417052,600925,688646,785306,1070179,1211014,1366201,1793402,2013231,2577670,2882713,3621196,4037873,4996676,5558812,6796051,7546840,9135420,10974045,12161167,14505352,17204583,19037192,22460312,26385887,30880233,36017965,41882995,48569662,56183973,64845010,74686495, 85858527,98529544,117069547,133759663,157718804,179566008,210410382,245680212,278430892,323593544,375070444,433678404,500335139,588328184,675555314);

		//entfernung zum nächsten nullpunkt berechnen
		$s1=$sv_sou_galcenter[0][0]-$sbx*15;
		$s2=$sv_sou_galcenter[0][1]-$sby*15;
		if($s1<0)$s1=$s1*(-1);
		if($s2<0)$s2=$s2*(-1);
		$s1=pow($s1,2);
		$s2=pow($s2,2);
		$w1=$s1+$s2;
		$entfernung=sqrt($w1);

		//die kosten in abhängigkeit zur entfernung berechnen
		$teiler=$sv_sou_galcenter[0][2]/50;

		$kostenstelle=round($entfernung/$teiler);
		if($kostenstelle>49)$kostenstelle=49;
		$kostenstelle=49-$kostenstelle;

		$kosten=round($ekosten[$kostenstelle]/2);
 
		echo '<div id="tt5" title="Sektorraumbasis&F&uuml;r '.number_format($kosten, 0,",",".").' Zastari kannst Du in diesem Sektor eine Basis Deiner Fraktion errichten." style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=sectorpage&buildbase=1"><img border="0" src="'.$gpfad.'sym36.png" width="100%" height="100%"></a></div>';
  }

  echo '</div>';
  
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////
  
  //rahmen zeichnen
  /*
  echo '<div id="border1" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'/bo1.png);left: 0px; top: 0px; width: 40px; height: 100%;  z-index:2;"></div>';
  echo '<div id="border2" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'/bo1.png);right: 0px; top: 0px; width: 40px; height: 100%;  z-index:2;"></div>';
  
  echo '<div id="border3" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'/bo1.png);left: 0px; top: 0px; width: 100%; height: 30px;  z-index:2;"></div>';
  echo '<div id="border3" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'/bo1.png);left: 0px; bottom: 0px; width: 100%; height: 10px;  z-index:2;"></div>';
  */  
  
  echo '<div id="mapcontent" style="position:absolute; overflow:hidden; left: 0px; top: 0px; width: 9000px; height: 4200px;  z-index:1;"></div>';
  //div der karte mit den maximal möglichen ausmaßen von x/y 10 mio
  echo '</div>';

//kartendaten per ajax laden

echo '</div>';

//$x=(1500000+($sv_sou_startposition[$player_fraction-1][0]*40))*-1;
//$y=(1500000-($sv_sou_startposition[$player_fraction-1][1]*40))*-1;

//javascript
?>
<script type="text/javascript" src="js/jquery.ui.touch.js"></script>
<script type="text/javascript">
load_infobar();

var inmove=0;
var mapdata=new Array();

var playerx=<?php echo $player_x; ?>;
var playery=<?php echo $player_y; ?>;
var playerfraction=<?php echo $player_fraction; ?>;
var playermoney=<?php echo $player_money; ?>;
var reichweite=<?php echo $reichweite; ?>;
var speed=<?php echo $speed; ?>;
var system1hb=<?php echo $system1hb; ?>;

var posX = -4500+Math.round($("#mapcontainer").width()/2);
var posY = -2100+Math.round($("#mapcontainer").height()/2);

var showx=<?php echo $showx; ?>;
var showy=<?php echo $showy; ?>;

var startposx=posX;
var startposy=posY;

document.getElementById("mapcontent").style.left = posX+"px";
document.getElementById("mapcontent").style.top = posY+"px";


$(function() {
	$("#mapcontent").draggable();
});

$("#mapcontent").bind( "dragstart", function(event, ui) {
  inmove=1;
  position = $("#mapcontent").position();
  startposx=position.left;
  startposy=position.top;
});

$("#mapcontent").bind( "dragstop", function(event, ui) {
  inmove=0;
  $("#mapcontent").draggable( "option", "disabled", true );
  $("#ajaxloader").css('visibility','visible');
  var position = $("#mapcontent").position();
  startposx=startposx+((startposx-position.left)*-1);
  startposy=startposy-(startposy-position.top);
  if(startposx>posX+600)startposx=startposx-600;
  if(startposx>posX+600)startposx=startposx-600;
  if(startposx>posX+600)startposx=startposx-600;
  if(startposx<posX-600)startposx=startposx+600;
  if(startposx<posX-600)startposx=startposx+600;
  if(startposx<posX-600)startposx=startposx+600;
  if(startposx<posX-600)startposx=startposx+600;
  if(startposy>posY+600)startposy=startposy-600;
  if(startposy>posY+600)startposy=startposy-600;
  if(startposy>posY+600)startposy=startposy-600;
  if(startposy<posY-600)startposy=startposy+600;
  if(startposy<posY-600)startposy=startposy+600;
  if(startposy<posY-600)startposy=startposy+600;
  //if(startposx>=posX+600 || startposx<=posX-600)startposx=posX+((startposx-position.left)*-1);
  //if(startposy>=posY+600 || startposy<=posY-600)startposy=posY-(startposy-position.top);
  //alert(startposx+' : '+posX);
  showx=showx-Math.round(((startposx-position.left)*-1)/40);
  showy=showy-Math.round((startposy-position.top)/40);
  load_mapdata();
});

$.extend($.support, {         touch: "ontouchend" in document });
$.fn.addTouch = function() {         if ($.support.touch) {                 this.each(function(i,el){                         el.addEventListener("touchstart", iPadTouchHandler, false);                         el.addEventListener("touchmove", iPadTouchHandler, false);                         el.addEventListener("touchend", iPadTouchHandler, false);                         el.addEventListener("touchcancel", iPadTouchHandler, false);                 });         } }; 
var lastTap = null;

$('#mapcontent').addTouch();

$("#mapcontent").mouseup(function(e){
  if(inmove==0)
  {
	  var position = $("#mapcontent").position();
	  var text='';
	  var sysname='Leerraum';
	  var system2hb=0;
	  var hs1='';
	  var hs2='';
	  var systemfraction='-';
	  var x=Math.round((e.clientX-position.left-4500+(showx*40))/40);
	  var y=Math.round((2100-(e.clientY-position.top)+showy*40)/40);
	  text='Koordinaten: '+x+':'+y;

	  //stars
	  if(typeof(mapdata[0])!="undefined")	  
	  if(mapdata[0].systemname)
	  for(var j=0; j<mapdata[0].systemname.length; j++) 
	  {
		if(x==mapdata[0].systemx[j] && y==mapdata[0].systemy[j])
		{
		  if(mapdata[0].systemfraction[j]>0)systemfraction=mapdata[0].systemfraction[j];
		  sysname=mapdata[0].systemname[j]+' ('+systemfraction+')';
		  system2hb=mapdata[0].systemhb[j];
		}
	  }

	  //srb
	  if(typeof(mapdata[0])!="undefined")	  
	  if(mapdata[0].srbpic)
	  for(var j=0; j<mapdata[0].srbpic.length; j++) 
	  {
		if(x==mapdata[0].srbx[j] && y==mapdata[0].srby[j])
		{
		  if(mapdata[0].srbfraction[j]>0)systemfraction=mapdata[0].srbfraction[j];
		  if(systemfraction==999)systemfraction='ERBAUER';
		  sysname='Sektorraumbasis';
		  if(mapdata[0].srbspecial[j]==1)sysname='Omega Br&uuml;cke';
		  if(mapdata[0].srbspecial[j]==2)sysname='DER KERN';
		  if(mapdata[0].srbspecial[j]==3)sysname='Zentrumsgebiet';		  
		  if(mapdata[0].srbspecial[j]==4)sysname='Kernsteuerungsstation';
		  sysname=sysname+' ('+systemfraction+')';
		}
	  }
	  
	  text=text+'<br>'+sysname;

	  s1=playerx-x;
	  s2=playery-y;

	  if(s1<0)s1=s1*(-1);
	  if(s2<0)s2=s2*(-1);
	  s1=Math.pow(s1,2);
	  s2=Math.pow(s2,2);
	  w1=s1+s2;
	  w3=Math.sqrt(w1);

	  distance=Math.round(w3*100)/100;

	  text=text+hs1+'<br>Entfernung: '+distance+' LJ';
	  
	  if(w3>0)traveltime=Math.round(120+w3*speed+120);else traveltime=0;

	  if(distance>reichweite){hs1='<font color="#FF0000">';hs2='</font>';}else{hs1='';hs2=''}
		  
	  text=text+hs1+'<br>&Uuml;berlichtantrieb-Reisezeit: '+sec2time(traveltime)+hs2;

	  if(system1hb>0 && system2hb>0)
	  {
	  	traveltime=7200-((system1hb*60)+(system2hb*60));
	    text=text+'<br>Hyperraumblasentransfer: '+sec2time(traveltime);
	  }

	  text=text+"<br><a href=sou_main.php?action=sectorpage&tx="+x+"&ty="+y+"><div class=\"b1\" align=\"center\">Start</div></a>"+"<br><a href=sou_main.php?action=sectorpage&rhtx="+x+"&rhty="+y+"><div class=\"b1\" align=\"center\">Reisehilfe</div></a>";
    
    $("#rz").html(text);
  }
}
);

$("#mapcontent").mousemove(function(e){
  var position = $("#mapcontent").position();
  var text='';
  var sysname='Leerraum';
  var system2hb=0;
  var hs1='';
  var hs2='';
  var systemfraction='-';
  var x=Math.round((e.clientX-position.left-4500+(showx*40))/40);
  var y=Math.round((2100-(e.clientY-position.top)+showy*40)/40);
  text='Koordinaten: '+x+':'+y;

  //stars
  if(typeof(mapdata[0])!="undefined")	  
  if(mapdata[0].systemname)
  for(var j=0; j<mapdata[0].systemname.length; j++) 
  {
	if(x==mapdata[0].systemx[j] && y==mapdata[0].systemy[j])
	{
	  if(mapdata[0].systemfraction[j]>0)systemfraction=mapdata[0].systemfraction[j];
	  sysname=mapdata[0].systemname[j]+' ('+systemfraction+')';
	  system2hb=mapdata[0].systemhb[j];
	}
  }

  //srb
  if(typeof(mapdata[0])!="undefined")	  
  if(mapdata[0].srbpic)
  for(var j=0; j<mapdata[0].srbpic.length; j++) 
  {
	if(x==mapdata[0].srbx[j] && y==mapdata[0].srby[j])
	{
	  if(mapdata[0].srbfraction[j]>0)systemfraction=mapdata[0].srbfraction[j];
	  if(systemfraction==999)systemfraction='ERBAUER';
	  sysname='Sektorraumbasis';
	  if(mapdata[0].srbspecial[j]==1)sysname='Omega Br&uuml;cke';
	  if(mapdata[0].srbspecial[j]==2)sysname='DER KERN';
	  if(mapdata[0].srbspecial[j]==3)sysname='Zentrumsgebiet';
	  if(mapdata[0].srbspecial[j]==4)sysname='Kernsteuerungsstation';
	  sysname=sysname+' ('+systemfraction+')';
	}
  }
  
  text=text+'<br>'+sysname;

  s1=playerx-x;
  s2=playery-y;

  if(s1<0)s1=s1*(-1);
  if(s2<0)s2=s2*(-1);
  s1=Math.pow(s1,2);
  s2=Math.pow(s2,2);
  w1=s1+s2;
  w3=Math.sqrt(w1);

  distance=Math.round(w3*100)/100;

  text=text+hs1+'<br>Entfernung: '+distance+' LJ';
  
  if(w3>0)traveltime=Math.round(120+w3*speed+120);else traveltime=0;

  if(distance>reichweite){hs1='<font color="#FF0000">';hs2='</font>';}else{hs1='';hs2=''}
	  
  text=text+hs1+'<br>&Uuml;berlichtantrieb-Reisezeit: '+sec2time(traveltime)+hs2;

  if(system1hb>0 && system2hb>0)
  {
  	traveltime=7200-((system1hb*60)+(system2hb*60));
    text=text+'<br>Hyperraumblasentransfer: '+sec2time(traveltime);
  }
  
  $("#kurzinfo").html(text);
}
);

function load_mapdata()
{
  //var position = $("#mapcontent").position();
  $.getJSON('sou_ajaxrpc.php?loadmapdata=1&xpos='+showx+'&ypos='+showy,
  function(data)
  {
	$('#mapcontent').empty();
	mapdata=data;
	//BG
	var left=0;
	var top=0;
	var sectext='';
	var needfractions=new Array(playerfraction, 999);
	for(i=0;i<105;i++)
	{
	  if(data[0].knownflag[i]<0)var bgpic='-1';
	  if(data[0].knownflag[i]==0)var bgpic='0';
	  if(data[0].knownflag[i]==1)var bgpic='1';
	  if(data[0].knownflag[i]==2)var bgpic='2';
	  if(data[0].knownflag[i]==3)var bgpic='3';
	  if(data[0].knownflag[i]==4)var bgpic='4';
	  if(data[0].knownflag[i]==5)var bgpic='5';
	  if(data[0].knownflag[i]==6)var bgpic='6';
	  if(data[0].knownflag[i]==666)var bgpic='666';
	  if(data[0].knownflag[i]==999)var bgpic='999';

	  sectext='';

	  //explorable
	  if(data[0].knownflag[i]<0)
	  {
		var xa=data[0].knownx[i]-22;
		var xe=data[0].knownx[i]+22;
		var ya=data[0].knowny[i]-22;
		var ye=data[0].knowny[i]+22;

		if(playerx>=xa && playerx<=xe & playery>=ya && playery<=ye)
		{
		  if(in_array(data[0].knownflag[i-16], needfractions)==true ||
			 in_array(data[0].knownflag[i-15], needfractions)==true ||
			 in_array(data[0].knownflag[i-14], needfractions)==true ||
			 in_array(data[0].knownflag[i-1], needfractions)==true ||
			 in_array(data[0].knownflag[i+1], needfractions)==true ||
			 in_array(data[0].knownflag[i+14], needfractions)==true ||
			 in_array(data[0].knownflag[i+15], needfractions)==true ||
			 in_array(data[0].knownflag[i+16], needfractions)==true)
		     sectext='<div class="cell1" style="margin-right:auto;margin-left:auto; width: 400px; margin-top: 225px; padding: 5px; border: 2px solid #333333;">Dieser Sektor kann erforscht werden. Du kannst die Kosten selbst tragen, oder die Zastari aus der Fraktionskasse verwenden.<br><br>Expeditionskosten: '+number_format(data[0].knowncost[i])+' <img src="'+gpfad+'a9.gif" alt="Zastari" title="Zastari"><br><br>Dein Verm&ouml;gen: '+number_format(playermoney)+' <img src="'+gpfad+'a9.gif" alt="Zastari" title="Zastari"><a href="sou_main.php?action=sectorpage&ex='+data[0].knownx[i]+'&ey='+data[0].knowny[i]+'&ecost=1"><div class="b1" align="center">Start</div></a><br>Fraktionskasse: '+number_format(data[0].fractionmoney)+' <img src="'+gpfad+'a9.gif" alt="Zastari" title="Zastari"><a href="sou_main.php?action=sectorpage&ex='+data[0].knownx[i]+'&ey='+data[0].knowny[i]+'&ecost=2"><div class="b1" align="center">Start</div></a></div>';
		     else sectext='<div class="cell1" style="width: 400px; margin-top: 250px; padding: 5px; border: 2px solid #333333;">&Uuml;ber diesen Sektor liegen deiner Fraktion noch keine Informationen vor.<br><br>Eine Forschungsexpedition kann nur in Sektoren gestartet werden, die einen Nachbarsektor haben, der unter der Kontrolle der eigenen Fraktion steht und neben denen Du dich befindest.</div>';
		}
		else sectext='<div class="cell1" style="width: 400px; margin-top: 250px; padding: 5px; border: 2px solid #333333;">&Uuml;ber diesen Sektor liegen deiner Fraktion noch keine Informationen vor.<br><br>Eine Forschungsexpedition kann nur in Sektoren gestartet werden, die einen Nachbarsektor haben, der unter der Kontrolle der eigenen Fraktion steht und neben denen Du dich befindest.</div>';
		
	  }
 
	  //insert div 
	  $('#mapcontent').append('<div id="secbg'+data[0].knownx[i]+'x'+data[0].knowny[i]+'" style="position: absolute; z-index: 10; left: '+left+'px; top: '+top+'px; width: 600px; height: 600px; background-image: url(<?php echo $gpfad ?>secbg'+bgpic+'.png); border: none; padding: 0px; margin:0px;">'+sectext+'</div>');

		  
	  left=left+600;
	  if(left>8400){left=0; top=top+600;}
	}

	//zeropoint
	var zeroposx=data[0].knownx[0]-7;
	var zeroposy=data[0].knowny[0]+7;

	//stars
	if(data[0].systemname)
	for(var j=0; j<data[0].systemname.length; j++){
		var sysfrac=data[0].systemfraction[j];
		if(sysfrac==0)sysfrac="";
		left=(data[0].systemx[j]-zeroposx)*40;
		top=(zeroposy-data[0].systemy[j])*40;
		$('#mapcontent').append('<div id="sys'+data[0].systemx[j]+'x'+data[0].systemy[j]+'" style="position: absolute; z-index: 15; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>s'+data[0].systempic[j]+'.png" width="100%" height="100%"></div>');
		if(sysfrac!="")
		$('#mapcontent').append('<div id="sys'+data[0].systemx[j]+'x'+data[0].systemy[j]+'" style="position: absolute; z-index: 16; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px; text-align: left;">'+sysfrac+'</div>');
		if(data[0].systemua[j]==1)
		$('#mapcontent').append('<div id="sys'+data[0].systemx[j]+'x'+data[0].systemy[j]+'ua" style="position: absolute; z-index: 17; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>sym10.png" width="100%" height="100%"></div>');	  
	}

	//srb
	if(data[0].srbpic)
	for(var j=0; j<data[0].srbpic.length; j++) 
	{
	  left=(data[0].srbx[j]-zeroposx)*40;
	  top=(zeroposy-data[0].srby[j])*40;
	  $('#mapcontent').append('<div id="sys'+data[0].srbx[j]+'x'+data[0].srby[j]+'" style="position: absolute; z-index: 18; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>ssrb'+data[0].srbpic[j]+'.png" width="100%" height="100%"></div>');
	}

	//player	
	left=(playerx-zeroposx)*40;
	top=(zeroposy-playery)*40;
	$('#mapcontent').append('<div id="playerpic" style="position: absolute; z-index: 19; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>v1.png" width="100%" height="100%"></div>');
	
	//find
	if(data[0].findx)
	for(var j=0; j<data[0].findx.length; j++) 
	{
	  left=(data[0].findx[j]-zeroposx)*40;
	  top=(zeroposy-data[0].findy[j])*40;
	  $('#mapcontent').append('<div id="find'+data[0].findx[j]+'x'+data[0].findy[j]+'" style="position: absolute; z-index: 20; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>sq2.gif" width="100%" height="100%"></div>');
	}	

	//quest
	if(data[0].questx)
	for(var j=0; j<data[0].questx.length; j++) 
	{
	  left=(data[0].questx[j]-zeroposx)*40;
	  top=(zeroposy-data[0].questy[j])*40;
	  $('#mapcontent').append('<div id="quest'+data[0].questx[j]+'x'+data[0].questy[j]+'" style="position: absolute; z-index: 21; left: '+left+'px; top: '+top+'px; width: 40px; height: 40px; border: none; padding: 0px; margin:0px;"><img src="<?php echo $gpfad ?>sq1.gif" width="100%" height="100%"></div>');
	}	

    document.getElementById("mapcontent").style.left = startposx+"px";
	document.getElementById("mapcontent").style.top = startposy+"px";

	$("#mapcontent").draggable( "option", "disabled", false );
	$("#ajaxloader").css('visibility','hidden');
  });
}

function show_map(x, y)
{
  $("#ajaxloader").css('visibility','visible');	

  showx=x;
  showy=y;

  startposx=posX;
  startposy=posY;
  
  load_mapdata();
}

load_mapdata();

window.onresize = setsize;

function setsize()
{
	  var height=document.getElementById("mapcontainer").offsetHeight-70;
	  var left=(document.getElementById("mapcontainer").offsetWidth-880)/2;
	  $('#mainarea').css('height', height+'px');  
	  $('#mainarea').css('left', left+'px');
}

setsize();

function lnk(parameter)
{
  $.getJSON('sou_ajaxrpc.php?'+parameter,
  function(data)
  {
	$('#mainarea').html(data[0].output);
	$("#mainarea").css('visibility','visible');
	$('div, img').tooltip({ 
	    track: true, 
	    delay: 0, 
	    showURL: false, 
	    showBody: "&",
	    extraClass: "design1", 
	    fixPNG: true,
	    opacity: 0.15,
	    left: 0
	});
  });
}

function pay_frac_z()
{
  var inputfield=$("#zs").val();
  inputfield = escape(inputfield);
  lnk('action=infocenter&do=1&zs='+inputfield);
  return false;
}  

function hide_mainarea()
{
  $("#mainarea").css('visibility','hidden');
}

</script>
<?php
die('</body></html>');
?>