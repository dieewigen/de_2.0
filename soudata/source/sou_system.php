<?php 
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// �berpr�fen ob man vielleicht in einer hyperraumblase ist
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($player_in_hb>0)
{
  //zum richtigen script weiterleiten
  include "soudata/source/sou_hyperbubble.php";
}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// sonnensystem und sektorraumbasis
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
include "soudata/defs/resources.inc.php";
include "soudata/defs/buildings.inc.php";
//daten zur ansicht
echo '<br>';

echo '<div align="center">';

//rahmen0_oben();

//zuerst schauen wo man sich befindet: sonnensystem, sektorraumbasis, leerraum
$systemstatus=0; //0 leerraum, 1 sonnensystem, 2 sektorraumbasis
$searchx=$player_x;
$searchy=$player_y;

//test auf sonnensystem
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $systemstatus=1;
  $row = mysql_fetch_array($db_daten);
 
  $owner_id=$row["id"];
  $sysname=$row["sysname"];
  $owner_fraction=$row["fraction"];
  $system_worth=$row["worth"];
  $systempicid=$row["pic"];
  $prestige[0]=$row["prestige1"];
  $prestige[1]=$row["prestige2"];
  $prestige[2]=$row["prestige3"];
  $prestige[3]=$row["prestige4"];
  $prestige[4]=$row["prestige5"];
  $prestige[5]=$row["prestige6"];
  $pirates=$row['pirates'];
  $prestigemax=$prestige[0]+$prestige[1]+$prestige[2]+$prestige[3]+$prestige[4]+$prestige[5];
  if($prestigemax==0)$prestigemax=1;
  if($row["underattack"]>time()-3600*24*2){$prestigebordercolor='#FF0000';}else{$prestigebordercolor='#444444';}
  
  if($pirates>0)
  {
    //stufe der piraten bestimmen
    // die entfernung zum zentrum berechnen
    $radius=sqrt(($player_x*$player_x)+($player_y*$player_y));
    if($radius>1250)
    {
      $pirateslevel=0;
    }
    else 
    {
      $pirateslevel=51-ceil($radius/50*2);
    }
    
    if($pirateslevel>0 AND $_REQUEST["attackpirates"]==1 AND $player_atimer1time<time())
    {
      include 'soudata/source/sou_system_pirates.php';
    }
  }  
}

//test auf sektorraumbasis
$db_daten=mysql_query("SELECT * FROM sou_map_base WHERE x='$searchx' AND y='$searchy' AND special=0",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
	$systemstatus=2;
	//srb daten auslesen
	$row = mysql_fetch_array($db_daten);
	$srb_baujahr=$row["bldgyear"];
	$srb_fraction=$row["fraction"];
	$prestige[0]=$row["prestige1"];
	$prestige[1]=$row["prestige2"];
	$prestige[2]=$row["prestige3"];
	$prestige[3]=$row["prestige4"];
	$prestige[4]=$row["prestige5"];
	$prestige[5]=$row["prestige6"];
	$prestigemax=$prestige[0]+$prestige[1]+$prestige[2]+$prestige[3]+$prestige[4]+$prestige[5];
	if($prestigemax==0)$prestigemax=1;
  
  //$srb_takeover=$row["takeover"];
  //$preis=($srb_takeover+2)*100000;
}
	  /*
      if($_REQUEST["dotakeover"] AND $systemstatus==2 AND $srb_fraction!=$player_fraction AND  $player_atimer1time<time())
	  {
		//�berpr�fen ob der nachbarsektor eine �bernahme erm�glicht
  		$sbx=round($player_x/15);
  		$sby=round($player_y/15);
		if(get_sector_owner($sbx, $sby+1)==$player_fraction OR get_sector_owner($sbx+1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby-1)==$player_fraction OR get_sector_owner($sbx-1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==999 OR get_sector_owner($sbx+1, $sby)==999 OR get_sector_owner($sbx, $sby-1)==999 OR get_sector_owner($sbx-1, $sby)==999)
		{
	  	  //�berpr�fen ob man genug geld hat
	  	  $hasmoney=has_money($player_user_id);
	  	  if($hasmoney>=$preis)
	  	  {
	  	    //�berpr�fen ob man schon wieder in dem bereich aktiv werden kann
	  	    if($player_atimer3time<=time()-3600*2)
	  	    {
	  	  	  //srb �bernehmen
	  		  mysql_query("UPDATE sou_map_base SET fraction='$player_fraction', takeover=takeover+1 WHERE x='$searchx' AND y='$searchy'",$soudb);
	  		
	  		  //pers�nlichen counter setzen
	  		  $time=time();
	  		  mysql_query("UPDATE sou_user_data SET srbtakeover=srbtakeover+1, atimer3time='$time' WHERE user_id='$player_user_id'",$soudb);
	  		
	  		  //info f�r den chat
	  		  $text='<font color="#FF0000">F'.$srb_fraction.' hat eine Sektorraumbasis an F'.$player_fraction.' verloren. Koordinaten: '.$searchx.':'.$searchy.'</font>';
        	  insert_chat_msg('^Der Reporter^', $text, 0, 0);
        	  
        	  //geld abziehen
        	  change_money($player_user_id, $preis*-1);
        	  
        	  //dieses als spende notieren
		      mysql_query("UPDATE `sou_user_data` SET donate=donate+'$preis' WHERE user_id='$player_user_id'",$soudb);
			  
			  //logging f�r multisuche
				$datum=date("Y-m-d H:i:s",time());
				$ip=getenv("REMOTE_ADDR");
				$datenstring="Zeit: $datum\nIP: $ip\nuser_id: $ums_user_id\n".$text."\n--------------------------------------\n";
				$fpsrb=fopen("soudata/cron/logs/srbtakeover.txt", "a");
				fputs($fpsrb, $datenstring);
				fclose($fpsrb);
			  
			  
        	  rahmen0_oben();
        	  echo '<br>Die &Uuml;bernahme war erfolgreich und die Kontrolle geh&ouml;rt in K&uuml;rze deiner Fraktion.<br><br>';
			  rahmen0_unten();
			  echo '<br>';
	  	    }
	  	    else 
	  	    {
	  	      rahmen0_oben();
	  	      echo '<br>Das kannst Du noch nicht.';
	  	      echo '<br>N&auml;chste M&ouml;glichkeit: '.date("H:i:s d.m.Y", $player_atimer3time+3600*2).'<br><br>';
	  	      rahmen0_unten();
	  	      echo '<br>';
	  	    }
	  	  }
	  	  else echo 'Du hast nicht genug Zastari.';
	    }
	    else echo 'Du ben&ouml;tigst einen Nachbarsektor deiner Fraktion, oder einen neutralen Nachbarsektor.';
	  }*/


  //maindiv
  echo '<div style="position: relative; top: 11px; width: 980px; height: 450px; background-color: #000000; overflow: hidden; 
  margin-left: auto; margin-right: auto; border: 1px solid #FFFFFF; z-index:0;">';

  //24 stunden bonus
  if($player_dailygift==1)
  {
    $grafikname="sym5.png";
    $ttip='T&auml;gliches Geschenk&Der Tag hat 24 Stunden und es gibt jeden Tag ein Geschenk f&uuml;r Dich. Hole es Dir ab.';
  }  
  else 
  {
    $ttip='T&auml;gliches Geschenk&Du hast das Geschenk bereits abgeholt.';
    $grafikname="sym6.png";
  }

  echo '<div title="'.$ttip.'" style="position: absolute; right: 0px; bottom: 0px; z-index: 20; height: 96px; width: 96px;"><a href="sou_main.php?action=dailygiftpage"><img border="0" src="'.$gpfad.$grafikname.'" width="100%" height="100%"></a></div>';  

  
if($systemstatus==1)
{
  //die daten des sonnensystems auslesen  
  
  //sonnensystem-buffs auslesen
  $time=time();
  $sysbuffstr='';
  $c=0;
  $db_daten=mysql_query("SELECT * FROM `sou_map_buffs` WHERE owner_id='$owner_id' AND time>'$time'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    if($row['typ']==1) {$grafikname="sym4.png";$tooltip='Geistige St&auml;rke&Feindliche Fraktionen haben in diesem Sonnensystem keinen Einfluss auf das Ansehen.<br>Aktiv bis: '.date ("d.m.Y H:i", $row['time']);}
 
    $sysbuffstr.='<img src="'.$gpfad.$grafikname.'" width="64px" height="64px" title="'.$tooltip.'">';
    $c++;
    //if($c==2){$sysbuffstr.='<br>';$c=0;}
  }

  //planetendaten auslesen
  $db_daten=mysql_query("SELECT * FROM sou_map_planets WHERE owner_id='$owner_id'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num>0)
  {
  
  //////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////
  //  sonnensystem animieren
  //////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////
  
  //die sonne in die mitte packen
  echo '<div style="position: absolute; left: 458px; top: 193px;"><img src="'.$gpfad.'s'.$systempicid.'.png" width="64px" height="64px"></div>';
 
  $planetsize[0]=array (32,32);
  $planetsize[1]=array (28,28);
  $planetsize[2]=array (24,24);
  $planetsize[3]=array (20,20);
  $planetsize[4]=array (16,16);
  $planetsize[5]=array (12,12);
  
  
  $planetcounter=0;
  if($player_animation==1)
  while($row = mysql_fetch_array($db_daten))
  {
    //die einzelnen planetendivs setzen
    //typ 2 ist das asteroidenfeld
    if($row['typ']!=2)
    echo '<div style="position: absolute; z-index: 1;" id="P'.$planetcounter.'"><img src="'.$gpfad.'p'.$row["pic"].'.png"  width="'.$planetsize[$row["size"]][0].'" height="'.$planetsize[$row["size"]][1].'"></div>';
    else 
    echo '<div style="position: absolute; z-index: 1;" id="P'.$planetcounter.'"><img src="'.$gpfad.'px'.$row["pic"].'.gif"  width="'.$planetsize[$row["size"]][0].'" height="'.$planetsize[$row["size"]][1].'"></div>';
    $planetcounter++; 
  }
  echo '<script language="JavaScript">';
?>
	var laySyntax=new Array();
	if(jQuery.data(document.body, 'player_animationenable')==1)
	for(i=0;i<<?php echo $num ?>;i++) {
		if(document.getElementById) laySyntax[i]=document.getElementById("P"+i).style;
		else if(document.all) laySyntax[i]=document.all.tags("div")[i].style;
		else if(document.layers) laySyntax[i]=document.layers[i];
	}
<?php 
	// Kreisbewegung
	// 100 = Radius, 125/110 = Nullpunkt
	// 0.0628 = 2*Pi/Punkteanzahl pro Kreis = 2*Pi/100
	// = Abstand der Elemente zueinander in Radiant (rad)
	// Diese Angaben bestimmen die Gr��e des Kreises
?>
	var alpha=new Array();
	for(i=0;i<<?php echo $num ?>;i++) {
	  alpha[i]=Math.random()*360;
	}
	var nullx=480;
	var nully=225;
	function movea() {
		for(p=0;p<<?php echo $num ?>;p++) {
			radius=p*68+125;
			laySyntax[p].left=nullx+radius*Math.cos(alpha[p]+0.0628*p)+"px";
			laySyntax[p].top=nully+radius*Math.sin(alpha[p]+0.0628*p)/2+"px";
			alpha[p]=alpha[p]-0.01;
		}

		// Winkel-Geschwindigkeit
		
		setTimeout ('movea()',50);
	}
	if(jQuery.data(document.body, 'player_animationenable')==1)movea();
</script>
<?php
  }
  else
  {
    //es wurden noch keine planeten in der db angelegt
    echo '<br>&Uuml;ber dieses Sonnensystem liegen noch keine Informationen vor. Die gesammelten Daten werden aktuell ausgewertet und stehen in wenigen Augenblicken zur Verf&uuml;gung.<br>';	
    //auftrag f�r die anlegung in der db hinterlegen
    mysql_query("INSERT INTO sou_cronjobs (job, flag1, time) VALUES (1, '$owner_id', 0)",$soudb);
  }
}

if($systemstatus==2)
{
  //die srb in die mitte packen
  echo '<div style="position: absolute; left: 458px; top: 193px;"><img src="'.$gpfad.'ssrb1.png" width="64px" height="64px"></div>';
}
    echo '<br>';
    
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //seitenteiler
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  echo '<div style="position: relative;z-index: 10;">';
  echo '<table border="0" cellpadding="1" cellspacing="0">';
  echo '<tr><td width="140" valign="top">';
    
  
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  // hauptmen�
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  
  
  //wahlm�glichkeit f�r sektor/strategische karte
  echo '<div style="margin-left: 0px; margin-bottom: 4px; width: 142px; padding: 0px; float: left;">';
  echo '
<a title="&Zur strategischen Karte wechseln" href="sou_main.php?action=stratmappage"><img style="border: 1px solid #888888" src="'.$gpfad.'sym9.png" width="64px" height="64px"></a>
<a title="&Zur Sektoransicht wechseln" href="sou_main.php?action=sectorpage"><img style="border: 1px solid #888888" src="'.$gpfad.'sym8.png" width="64px" height="64px"></a>';
  echo '</div>';
  

  echo '<div style="width: 128px; text-align: center; border: 1px solid #888888; padding: 3px; line-height: 12; float: left;">';
  
  echo '<div title="Infocenter" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=systempage&underpage=1"><img border="0" src="'.$gpfad.'sym12.png" width="100%" height="100%"></a></div>';  
  echo '<div title="Fraktionsaufgaben" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=systempage&underpage=4"><img border="0" src="'.$gpfad.'sym13.png" width="100%" height="100%"></a></div>';

  //�berpr�fen ob die forschung aktiv ist, pa feature
  if($ums_premium==1 AND $player_atimer2time<time())
  {
    //nur anzeigen, wenn ein forschungsmodul vorhanden ist
    $db_daten=mysql_query("SELECT MAX(giveresearch) AS giveresearch FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
    $row = mysql_fetch_array($db_daten);

    if($row["giveresearch"]>0)
    {
      echo '<div title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym14.png" width="100%" height="100%"></a></div>';  
    }
    else echo '<div title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym15.png" width="100%" height="100%"></a></div>';
  }
  else echo '<div title="Forschungsmodul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipresearchpage"><img border="0" src="'.$gpfad.'sym15.png" width="100%" height="100%"></a></div>'; 
  
  echo '<div title="Schiffs&uuml;bersicht" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipoverpage"><img border="0" src="'.$gpfad.'sym16.png" width="100%" height="100%"></a></div>';  
  echo '<div title="Auktionszentrum" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=auctionpage"><img border="0" src="'.$gpfad.'sym17.png" width="100%" height="100%"></a></div>';
  echo '<div title="Fabriken" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=factorypage"><img border="0" src="'.$gpfad.'sym18.png" width="100%" height="100%"></a></div>';
  echo '<div title="Hyperfunk" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=hyperfunk"><img border="0" src="'.$gpfad.'sym19.png" width="100%" height="100%"></a></div>';  
  echo '<div title="Fraktionsforum" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=fracforumpage"><img border="0" src="'.$gpfad.'sym20.png" width="100%" height="100%"></a></div>';
  echo '<div title="Squad" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=squad"><img border="0" src="'.$gpfad.'sym21.png" width="100%" height="100%"></a></div>';
  echo '<div title="Fraktionsdaten" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=toplistpage"><img border="0" src="'.$gpfad.'sym22.png" width="100%" height="100%"></a></div>';
  echo '<div title="Hilfe" style="height: 42px; width: 42px; float: left;"><a href="http://help.bgam.es/index.php?thread=abl_de" target="_blank"><img border="0" src="'.$gpfad.'sym23.png" width="100%" height="100%"></a></div>';
  echo '<div title="Optionen" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=optionspage"><img border="0" src="'.$gpfad.'sym24.png" width="100%" height="100%"></a></div>';
  
  echo '</div>';
  
  //rpg-counter
  /*
  echo '<div title="Ein Bote der ERBAUER ist auf dem Weg." style="margin-top: 4px; width: 128px; text-align: center; border: 1px solid #888888; padding: 3px; float: left; color: #b02e95;">';
  echo '<div id="counterboxr"><div id="counterr"></div></div>';
  echo '<script type="text/javascript" language="javascript">';
  //echo 'counter('.$zeit.',"counter", "'.$target.'");';
  $zeit=1337774400-time();
  echo 'counter('.$zeit.',"counterboxr", "counterr", "-");';
  echo '</script>';
  echo '</div>';
  */
  //seitenteiler
  echo '</td><td width="500" valign="top">';
    
  //zieldiv f�r content
  //echo '<div id="maincontent"></div>';
  if(!isset($_REQUEST['underpage']))$_REQUEST['underpage']=1;
  if($_REQUEST['underpage']==1 OR $_REQUEST['underpage']==3 OR $_REQUEST['underpage']==4)
      include_once('soudata/source/sou_start.php');

  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////  
  //seitenteiler
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////
  echo '</td><td width="310" valign="top">';

  if($systemstatus==1){
	//rechte spalte mit daten �ber das sonnensystem
	rahmen1_oben('<div align="center"><b><a href="sou_main.php?action=showdatapage&styp=4&systemname='.utf8_encode($sysname).'">'.$sysname.'</a></b></div>');
    //echo '<table width="100%" border="0" cellpadding="0" cellspacing="2">';
	//ansehen der fraktionen im ss
	//echo '<tr align="left"><td>';
	$prestigetooltip ='&Sonnensystemwert: '.number_format($system_worth, 0,",",".").'<br>';
	$prestigetooltip.='Fraktionsansehen:<br>';
	
	for($i=1;$i<=6;$i++)
	{
	  $valuep=$prestige[$i-1]*100/$prestigemax;
	  $prestigetooltip.='<font color=#'.$colors_text[$i-1].'>'.$i.': '.number_format($prestige[$i-1], 0,",",".").
	  ' ('.number_format($valuep, 2,",",".").'%)</font><br>';
	}
	
	//ansehen-box
	echo '<div onclick="window.location.href=\'sou_main.php?action=systemprestigepage\'" class="link1" style="width: 64px; height: 64px; border: 1px solid '.$prestigebordercolor.'; float: right;" title="'.$prestigetooltip.'">';
	
	for($i=1;$i<=6;$i++)
	{
	  //die einzelnen balken-rahmen zeichnen
	  echo '<div style="height: 6px; width: 62px; margin-top: 2px; position: relative; border: 1px solid #'.$colors_text[$i-1].'">';
	    //den balen selbst zeichnen
	    $valuep=$prestige[$i-1]*100/$prestigemax;
	    echo '<div style="position: absolute; height: 100%; width: '.$valuep.'%; left: 0px; background-color: #'.$colors_text[$i-1].'"></div>';
	  
	  echo '</div>';
	}	
	echo '</div>';
	
	//sonnensystembuffs
	echo $sysbuffstr;
	
	//im heimatsystem k�nnte es evtl. eine virtuelle omegabr�cke geben
	if($sv_omega_position[0][$player_fraction-1][0]==$player_x AND $sv_omega_position[0][$player_fraction-1][1]==$player_y)
	{
	  $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60008 AND f".$player_fraction."lvl>0",$soudb);
	  $num = mysql_num_rows($db_daten);
	  if($num==1)
	  {
  	    echo '<span title="Virtuelle Omega-Br&uuml;cke&Diese Technologie erm&ouml;glicht den Zugang zur Omega-Br&uuml;cke"><a href="sou_main.php?action=creatorbridgepage"><img src="'.$gpfad.'ssrb2.gif" width="64px" height="64px" border="0"></a></span>';
	  }	
	}
	
	//im deep fraction-system k�nnte es auch eine virtuelle omega-br�cke geben
	if($sv_omega_position[1][$player_fraction-1][0]==$player_x AND $sv_omega_position[1][$player_fraction-1][1]==$player_y)
	{
	  $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60010 AND f".$player_fraction."lvl>0",$soudb);
	  $num = mysql_num_rows($db_daten);
	  if($num==1)
	  {
  	    echo '<span title="Virtuelle Omega-Br&uuml;cke&Diese Technologie erm&ouml;glicht den Zugang zur Omega-Br&uuml;cke"><a href="sou_main.php?action=creatorbridgepage"><img src="'.$gpfad.'ssrb2.gif" width="64px" height="64px" border="0"></a></span>';
	  }	
	}
	
	//verf�gbare rohstoffe im sonnensystem
    $title='Rohstoffvorkommen&';
	for($i=0;$i<count($r_def);$i++)
	{
  	  //�berpr�fen ob der rohstoff m�glich ist
 
  	  if(res_is_available($i)==1)
  	  {
  	    $title.=$r_def[$i][0].'<br>';
  	  }
	}
	echo '<img src="'.$gpfad.'px1.gif" width="64" height="64" title="'.$title.'">';
	
	//omega-br�cke
	echo check4creatorbridge();
	
	//fundst�cke
	echo check4find();

   //wenn es piraten gibt, ist kein zugriff m�glich, erst mu� man das system freik�mpfen
  if($pirates>0)
  {
    //stufe der piraten bestimmen
    // die entfernung zum zentrum berechnen
    $radius=sqrt(($player_x*$player_x)+($player_y*$player_y));
    if($radius>1250)
    {
      $pirateslevel=0;
    }
    else 
    {
      $pirateslevel=51-ceil($radius/50*2);
    }
    
    if($pirateslevel>0)
    {
      include 'soudata/source/sou_system_pirates.php';
    }
  }	
	
	//echo '</td></tr>';
	//echo '</table>';
	//rahmen1_unten();        
  }
  
  if($systemstatus==2){
	rahmen1_oben('<div align="center"><b>Sektorraumbasis-Informationen</b></div>');
	
	$prestigetooltip='&Fraktionsansehen:<br>';
	
	for($i=1;$i<=6;$i++)
	{
	  $valuep=$prestige[$i-1]*100/$prestigemax;
	  $prestigetooltip.='<font color=#'.$colors_text[$i-1].'>'.$i.': '.number_format($prestige[$i-1], 0,",",".").
	  ' ('.number_format($valuep, 2,",",".").'%)</font><br>';
	}
	
	//ansehen-box
	echo '<div onclick="window.location.href=\'sou_main.php?action=systemprestigepage\'" class="link1" style="width: 64px; height: 64px; border: 1px solid '.$prestigebordercolor.'; float: right;" title="'.$prestigetooltip.'">';
	
	for($i=1;$i<=6;$i++){
	  //die einzelnen balken-rahmen zeichnen
	  echo '<div style="height: 6px; width: 62px; margin-top: 2px; position: relative; border: 1px solid #'.$colors_text[$i-1].'">';
	    //den balen selbst zeichnen
	    $valuep=$prestige[$i-1]*100/$prestigemax;
	    echo '<div style="position: absolute; height: 100%; width: '.$valuep.'%; left: 0px; background-color: #'.$colors_text[$i-1].'"></div>';
	  
	  echo '</div>';
	}	
	echo '</div>';
	
	
	//omega-br�cke
	echo check4creatorbridge();	
	//fundst�cke
	echo check4find();
    echo '<table width="100%" border="0" cellpadding="0" cellspacing="2">';
	echo '<tr align="left"><td>Baujahr: '.number_format($srb_baujahr, 0,",",".").'</td></tr>';
	echo '<tr align="left"><td>Fraktion: '.$srb_fraction.'</td></tr>';
	echo '</table>';
	//rahmen1_unten();
	
	
	
  }
  
  if($systemstatus==0)
  {
	rahmen1_oben('<div align="center"><b>Informationen</b></div>');
	//omega-br�cke
	echo check4creatorbridge();	
	//fundst�cke
	echo check4find();
    echo '<table width="100%" border="0" cellpadding="0" cellspacing="2">';
	echo '<tr align="left"><td>Hier gibt es nur den leeren Raum.</td></tr>';
	echo '</table>';
  }  
  
  
  if($systemstatus==1)
  {
    //rechte spalte mit den m�glichen aktionen
    if($owner_fraction>0)
    {
      echo '<div style="margin-top: 4px;">';
      echo '<div title="Geb&auml;ude" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=buildingpage"><img border="0" src="'.$gpfad.'sym25.png" width="100%" height="100%"></a></div>';
      echo '<div title="Forschung" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=researchpage"><img border="0" src="'.$gpfad.'sym26.png" width="100%" height="100%"></a></div>';
      echo '<div title="Raumwerft" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=shipyardpage"><img border="0" src="'.$gpfad.'sym27.png" width="100%" height="100%"></a></div>';
      echo '<div title="Lagerkomplex" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=systemholdpage"><img border="0" src="'.$gpfad.'sym28.png" width="100%" height="100%"></a></div>';
      echo '<div title="Modulkomplex" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=modulholdpage"><img border="0" src="'.$gpfad.'sym29.png" width="100%" height="100%"></a></div>';
      echo '<div title="Handelskontor" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=tradepage"><img border="0" src="'.$gpfad.'sym30.png" width="100%" height="100%"></a></div>';
      echo '<div title="Upgrade-O-Modul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=upgradeomodulpage"><img border="0" src="'.$gpfad.'sym31.png" width="100%" height="100%"></a></div>';
      echo '<div title="Bao-Nada Station" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=baonadastationpage"><img border="0" src="'.$gpfad.'sym32.png" width="100%" height="100%"></a></div>';
      echo '<div title="Hyperraumblasen" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=hyperbubblepage&enter=1"><img border="0" src="'.$gpfad.'sym33.png" width="100%" height="100%"></a></div>';
      echo '</div>';
    }
    else 
    {
      echo '<div style="margin-top: 4px;">';
      echo '<div title="Kolonie gr&uuml;nden" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=createcolonypage"><img border="0" src="'.$gpfad.'sym34.png" width="100%" height="100%"></a></div>';
      echo '</div>';
    }
  }
  
  if($systemstatus==2)//srb
  {
      echo '<div style="margin-top: 4px;">';
      echo '<div title="Modulkomplex" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=modulholdpage"><img border="0" src="'.$gpfad.'sym29.png" width="100%" height="100%"></a></div>';
      echo '<div title="Upgrade-O-Modul" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=upgradeomodulpage"><img border="0" src="'.$gpfad.'sym31.png" width="100%" height="100%"></a></div>';
      echo '<div title="Bao-Nada Station" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=baonadastationpage"><img border="0" src="'.$gpfad.'sym32.png" width="100%" height="100%"></a></div>';
	  /*
	  if($srb_fraction!=$player_fraction)
	  {
        $preis=($srb_takeover+2)*100000;
	
	    $tooltip='Feindliche &Uuml;bernahme&Durch Bestechung kann die Sektorraumbasis von einer anderen Fraktion &uuml;bernommen werden. Eine &Uuml;bernahme ist nur m&ouml;glich, wenn ein benachbarter Sektor der Fraktion geh&ouml;rt, bzw. der Sektor neutral ist. Du kannst alle 2 Stunden eine &Uuml;bernahme starten, unabh&auml;hig von der Sektorraumbasis auf der Du Dich befindest.<br>Der Preis f&uuml;r die &Uuml;bernahme erh&ouml;ht sich immer weiter, je &ouml;fters die Sektorraumbasis die Fraktion wechselt.<br><br>';
 
	    $tooltip.='Preis in Zastari: '.number_format($preis, 0,",",".").'<br><br>';

	    if($player_atimer3time+3600*2>time())
	    {
	      $tooltip.='N&auml;chste m&ouml;gliche &Uuml;bernahme: '.date("H:i:s d.m.Y", $player_atimer3time+3600*2);
	      $tooltip.='<br><br>';
	    }
		
	    echo '<div title="'.$tooltip.'" style="height: 42px; width: 42px; float: left;"><a href="sou_main.php?action=systempage&dotakeover=1"><img border="0" src="'.$gpfad.'sym35.png" width="100%" height="100%"></a></div>';
      }
	  */
      echo '</div>';
  }
  rahmen1_unten();
  //seitenteiler
  echo '</td></tr></table>';
  
  //echo '</div>';//z-index-div
	if($player_owner_id==1){
		for($i=1;$i<=6;$i++){
			$db_daten=mysql_query("SELECT user_id FROM sou_user_data WHERE x='$searchx' AND y='$searchy' AND fraction='$i' AND donate<>donatelastday",$soudb);
			$num = mysql_num_rows($db_daten);

			echo '<span style="color: #'.$colors_text[$i-1].'">'.$num.'</span>';
		}	
	}

echo '</div>';//ende sonnensystem-div

echo '</div>';//center-div

function check4creatorbridge()
{
  global $player_x, $player_y, $soudb, $gpfad;
  $db_daten=mysql_query("SELECT * FROM sou_map_base WHERE x='$player_x' AND y='$player_y' AND special=1",$soudb);
  
  $str='';
  
  $anzahl_find = mysql_num_rows($db_daten);
  if($anzahl_find>0)
  {
    $str='<span title="Virtuelle Omega-Br&uuml;cke&Diese Technologie erm&ouml;glicht den Zugang zur Omega-Br&uuml;cke"><a href="sou_main.php?action=creatorbridgepage"><img src="'.$gpfad.'ssrb2.gif" width="64px" height="64px" border="0"></a></span>';  
  }

  return($str);
}

function check4find()
{
  global $player_atimer1time, $player_x, $player_y, $soudb, $gpfad;

  $str='';
  
  if($player_atimer1time<time())
  {
  
    $db_daten=mysql_query("SELECT * FROM sou_map_find WHERE x='$player_x' AND y='$player_y'",$soudb);
    $anzahl_find = mysql_num_rows($db_daten);
    if($anzahl_find>0)
    {
      $str='<span title="Fundst&uuml;ck&Dieses im Weltraum treibende Objekt kannst Du bergen."><a href="sou_main.php?action=findpage"><img src="'.$gpfad.'sq2.gif" width="64px" height="64px" border="0"></a></span>';  
    }  
  }
  return($str);
}

die('</body></html>');
?>