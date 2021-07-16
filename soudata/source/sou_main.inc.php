<?php
$thisissou=1;
$soucss=1;
include "soudata/lib/sou_functions.inc.php";
include "inc/header.inc.php";
include "inc/sv.inc.php";
include "soudata/defs/startpositionen.inc.php";
include "soudata/lib/transaction.lib.php";
include "soudata/defs/colors.inc.php";
include "soudata/lib/sou_dbconnect.php";

//grafikpfad optimieren
$gpfad=$sv_image_server_list[0].'s/';

//code um zu de zur�ckkehren zu k�nnen
if($_SESSION["ums_chatoff"]) $qlstr="top.document.getElementById('gf').cols = '209, *, 0, 0';top.document.getElementById('gf').rows = '*';";
else $qlstr="top.document.getElementById('gf').cols = '209, 620, *, 0, 0';top.document.getElementById('gf').rows = '*';";

if($sv_sou_in_de==1 AND $_SESSION['ums_mobi']==1)
{
	$qlstr="location.href='menu.php'";	
}


?>
<!DOCTYPE HTML>
<html>
<head>
<title>EA</title>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 
include "cssinclude.php"; 

echo '<script type="text/javascript" src="js/sou_fn.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-migrate.min.js"></script>
<script type="text/javascript" src="js/jquery.dimensions.min.js"></script>
<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>
';
//<script type="text/javascript" src="js/jquery.ui.touch.js"></script>
echo '<script language="javascript">';

include 'soudata/source/sou_js_functions.inc.php';
include 'soudata/source/sou_js_main.inc.php';

if($_REQUEST['action']!='showdatapage')
echo '
$(document).ready(function () {
$("div,span,img,a,tr,td").tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: "&",
    extraClass: "design1", 
    fixPNG: true,
    opacity: 1.00,
    left: 0
});
});
';
echo 'var gpfad="'.$gpfad.'";';
//if($ums_premium==0)echo 'parent.frames.aframe.location.href="sou_topban.php";';
echo '</script>';
//echo '<script src="js/'.$sv_server_template.'_map_r_'.$ums_language.'.js"></script>';
?>
</head>
<body bgcolor="#000000" style="overflow: auto">
<center style="width: 100%">
<?php

//grafik um zu de zur�ckkehren zu k�nnen
//if($sv_sou_in_de==1)echo '<div style="position: absolute; top: 0px; right: 0px; z-index: 10000;" title="zur&uuml;ck zu DE"><a href="javascript:void(0);"onclick="javascript:'.$qlstr.'"><img border="0" src="'.$gpfad.'delogo.jpg" width="48px" height="48px"></a></div>';

//tooltips f�r die seite
echo '<SCRIPT language="JavaScript1.2" >';
//back-to-de-code
if($sv_sou_in_de==1)echo 'function btde(){'.$qlstr.'}';
echo '</SCRIPT>';

//wenn bekannt ist, dass der account existiert, dann die daten laden
if($_SESSION["sou_user_id"]>0)
{
  $db_daten=mysql_query("SELECT * FROM sou_user_data WHERE user_id='$_SESSION[sou_user_id]'",$soudb);  	
  $row = mysql_fetch_array($db_daten);
  $player_user_id=$_SESSION['sou_user_id'];
  $_SESSION["sou_spielername"]=$row["spielername"].' {'.$row["sn_ext1"].'}';
  $player_name=$row["spielername"];
  $player_age=$row['playerage'];
  $_SESSION["sou_fraction"]=$row["fraction"];
  $player_fraction=$row["fraction"];
  $_SESSION["sou_shipname"]=$row["shipname"];
  $player_ship_name=$row["shipname"];
  $player_x=$row["x"];
  $player_y=$row["y"];
  $player_rhx=$row["rhx"];
  $player_rhy=$row["rhy"];
  $player_rhuse=$row["rhuse"];
  $player_money=$row["money"];
  $player_darkmatter=$row['darkmatter'];
  $player_baosin=$row['baosin'];
  $player_destroy=$row["destroy"];
  $player_destroyed=$row["destroyed"];
  $player_ship_diameter=$row["shipdiameter"];
  $player_ship_material=$row["shipmaterial"];
  $player_shipnotready=$row["shipnotready"];
  $player_donate=$row["donate"];
  $player_donatelastday=$row["donatelastday"];
  $player_lastclick=$row["lastclick"];
  $player_in_hb=$row["inhb"];
  $player_atimer1typ=$row["atimer1typ"];//laufende aktion
  $player_atimer1time=$row["atimer1time"];
  $player_atimer2typ=$row["atimer2typ"];//laufende forschung
  $player_atimer2flag=$row["atimer2flag"];
  $player_atimer2time=$row["atimer2time"];
  $player_atimer3time=$row["atimer3time"];
  
  $player_specialres[1]=$row['specialres1'];
  $player_specialres[2]=$row['specialres2'];
  $player_specialres[3]=$row['specialres3'];
  $player_specialres[4]=$row['specialres4'];
  $player_specialres[5]=$row['specialres5'];
  $player_specialres[6]=$row['specialres6'];
  $player_specialres[7]=$row['specialres7'];
  $player_specialres[8]=$row['specialres8'];
  $player_specialres[9]=$row['specialres9'];
  $player_specialres[10]=$row['specialres10'];
  $player_owner_id=$row['owner_id'];
  $player_werberid=$row['werberid'];
  $player_dailygift=$row['dailygift'];
  $player_sound=$row['soundenable'];
  $player_animation=$row['animationenable'];
  $player_squad=$row['squad'];
  $player_hb_ap=$row['hb_ap'];
}

//�berpr�fen ob die werberid in der db gesetzt ist
if($_SESSION['ums_werberid']!=$player_werberid)
{
  $player_werberid=$_SESSION['ums_werberid'];
  mysql_query("UPDATE sou_user_data SET werberid='$player_werberid' WHERE user_id = '$player_user_id'",$soudb);
}

//�berpr�fen ob die owner_id in der db gesetzt ist
if($_SESSION['ums_owner_id']!=$player_owner_id)
{
  $player_owner_id=$_SESSION['ums_owner_id'];
  mysql_query("UPDATE sou_user_data SET owner_id='$player_owner_id' WHERE user_id = '$player_user_id'",$soudb);
}


//aktivit�t und weitere daten mitloggen
if($player_lastclick+300 < time())
{
  $player_lastclick=time();
  $ip=getenv("REMOTE_ADDR");
  mysql_query("UPDATE sou_user_data SET lastclick='$player_lastclick', last_ip='$ip' WHERE user_id = '$player_user_id'",$soudb);
}

//schauen ob alle daten im account richtig angelegt, sind, bzw. erst noch ein account angelegt werden muss
include "soudata/source/sou_create_account.php";

//das alter des spielers �berpr�fen und bei bedarf das klonen starten
if($player_age>120)
{
  include 'soudata/source/sou_clone.php';
}

//men� darstellen
//rahmen0_oben();
/*
if($_REQUEST["action"]!="stratmappage")
{
?>
<div class="menurahmen">
<div class="menu">
  <ul>
    <li><a style="cursor:default;" href="javascript:void(0);" onmouseover="montre('smenu2');" onmouseout="cache('smenu2');"><div class="b1">Fraktionsdaten</div></a>
      <ul id="smenu2" onmouseover="montre('smenu2');" onmouseout="cache('smenu2');">
        <li><div class="btnspacer"></div><a href="sou_main.php?action=toplistpage"><div class="b1">Rangliste</div></a><div class="btnspacer"></div></li>
        <li><a href="sou_main.php?action=statisticspage"><div class="b1">Statistiken</div></a><div class="btnspacer"></div></li>
        <li><a href="sou_main.php?action=showdatapage&styp=3"><div class="b1">Siegbedingungen</div></a><div class="btnspacer"></div></li>
        <li><a href="sou_main.php?action=showdatapage&styp=0"><div class="b1">Kolonien</div></a><div class="btnspacer"></div></li>
        <li><a href="sou_main.php?action=showdatapage&styp=1"><div class="b1">Geb&auml;ude</div></a><div class="btnspacer"></div></li>
        <li><a href="sou_main.php?action=showdatapage&styp=2"><div class="b1">Bao-Nada-Skala</div></a></li>
      </ul>
    </li>
  </ul>
</div>
</div>
<?php
}
*/

if($_REQUEST["action"]!="sectorpage")
{
  echo '<div id="infobar1" style="position:absolute; overflow:hidden; background-image: url('.$gpfad.'bgpic7.png);left: 0px; top: 0px; width: 100%; height: 24px; z-index:3;"></div>';
  $zeit=$player_atimer1time-time();
?>
<script type="text/javascript">
load_infobar();
</script>
<?php
}

?>
<script type="text/javascript">
jQuery.data(document.body, 'player_soundenable', <?php echo $player_sound; ?>);
jQuery.data(document.body, 'player_animationenable', <?php echo $player_animation; ?>);
</script>
<?php

//auf bezahlserver �berpr�fen
if($sv_payserver==1)
{
  if($ums_premium==0)
  { 
  	echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();
    echo '<table width="100%"><tr><td align="center">Dieses ist ein kostenpflichter Server der nur mit einem Premium-Account nutzbar ist. Solltest du kein Interesse an einer Geb&uuml;hr haben, so kannst du auch auf einem der kostenlosen Server spielen. In der Server&uuml;bersicht ist gekennzeichnet, ob ein Server kostenlos ist.<br><br>

Der Erwerb eines Premiumaccounts ist &uuml;ber die zentrale Accountverwaltung beim Men&uuml;punkt Premium m&ouml;glich.</td></tr></table>';
    rahmen2_unten();
    echo '<br>';
    rahmen0_unten();
    die('');
  }
}


if(isset($show_activetime_msg) && $show_activetime_msg==1) 
{
  echo '<br>';
  rahmen0_oben();
  echo '<br>';
  rahmen2_oben();
  echo '<table width="100%"><tr><td align="center">Du erh&auml;ltst als Aktivit&auml;tsbonus einen Schwarzmarktcredit.</td></tr></table>';
  rahmen2_unten();
  echo '<br>';
  rahmen0_unten();
  echo '<br>';
}

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// credit boost zum beschleunigen der offenen aktion
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
/*
if($_REQUEST["cboost"])
{
  //�berpr�fen ob etwas l�uft
  if($player_atimer1time>time())
  {
  	//transaktionsbeginn
    if (setLock($_SESSION["sou_user_id"]))
    {
 	  //�berpr�fen ob es der richtige typ ist. geht nur bei folgenden typen
      if($player_atimer1typ==1 OR $player_atimer1typ==5)
  	  {
        //�berpr�fen wieviel credits es kosten wird
        $needcredits=ceil(($player_atimer1time-time())/60/5);
  	
  	    //�berpr�fen ob man genug credits hat
        $has_credits=has_credits($ums_user_id);
  	    if($has_credits>=$needcredits)
  	    {
  	      //timer auf 0 setzen
  	      mysql_query("UPDATE sou_user_data SET atimer1typ='0', atimer1time='0' WHERE user_id='$player_user_id'",$soudb);
  	      $player_atimer1time=0;
  	      $player_atimer1typ=0;
  	    
  	      //msg ausgeben
  	      $msg='Die Aktion wurde abgeschlossen. Creditkosten: '.$needcredits;
  	  	
  	  	  //credits abziehen
  		  change_credits($ums_user_id, $needcredits*(-1), 'EA-Aktionsbeschleunigung');
  		
		  //den kauf im logfile hinterlegen
      	  //@mail($GLOBALS['env_admin_email'], 'EA Beschleunigung: '.$needcredits.' - '.$_SESSION["sou_spielername"].' - Fraktion '.$player_fraction, '');
      	  if($ums_user_id>1)
      	  {
  		    $datum=date("Y-m-d H:i:s",time());
  		    $clog="EA Beschleunigung: ".$needcredits." - ".$_SESSION[sou_spielername]." - Fraktion ".$player_fraction." - $datum\n";
  		    $fp=fopen("soudata/cron/logs/creditbuy.txt", "a");
  		    fputs($fp, $clog);
  		    fclose($fp);
      	  }
  	    }
	    else $msg='Es sind nicht genug Credits vorhanden.';
  	  }
      //lock wieder entfernen
      $erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
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
  else 
  {
	$msg='Es gibt keine aktive Aktion.';
  }
  echo '<br>';
  rahmen0_oben();
  echo '<br>';
  rahmen2_oben();
  echo '<table width="100%"><tr><td align="center">'.$msg.'</td></tr></table>';
  rahmen2_unten();
  echo '<br>';
  rahmen0_unten();
  $_REQUEST['action']='systempage';
}
*/

/*
include "bannerunterbrechung.php";
*/
//rahmen0_unten();
//men� ende

//wenn nichts gew�hlt wurde die startseite nehmen
if(!isset($_REQUEST["action"]))$_REQUEST["action"]="systempage";

//auf die einzelnen unterseiten verzweigen
if($_REQUEST["action"]=="optionspage")
{
  include "soudata/source/sou_options.php";
}

if($_REQUEST["action"]=="toplistpage")
{
  running_action(1);	
  include "soudata/source/sou_toplist.php";
}

if($_REQUEST["action"]=="statisticspage")
{
  running_action(1);	
  include "soudata/source/sou_statistics.php";
}

if($_REQUEST["action"]=="sectorpage")
{
  //running_action(0);
  //shipnotready();
  ship_in_hb();
  include "soudata/source/sou_sector.php";
}

if($_REQUEST["action"]=="systempage")
{
  running_action(1);
  include "soudata/source/sou_system.php";
}

if($_REQUEST["action"]=="stratmappage")
{
  //running_action(1);
  include "soudata/source/sou_stratmap.php";
}

if($_REQUEST["action"]=="buildingpage")
{
  running_action(0);
  //shipnotready();
  check_population();
  ship_in_hb();
  include "soudata/source/sou_building.php";
}

if($_REQUEST["action"]=="researchpage")
{
  running_action(0);
  //shipnotready();
  ship_in_hb();
  include "soudata/source/sou_research.php";
}

if($_REQUEST["action"]=="shipyardpage")
{
  running_action(0);
  include "soudata/source/sou_shipyard.php";
}

if($_REQUEST["action"]=="tradepage")
{
  running_action(0);	
  //shipnotready();
  ship_in_hb();
  include "soudata/source/sou_trade.php";
}

if($_REQUEST["action"]=="auctionpage")
{
  running_action(1);	
  //shipnotready();
  ship_in_hb();
  include "soudata/source/sou_auction.php";
}

if($_REQUEST["action"]=="upgradeomodulpage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_upgradeomodul.php";
}

if($_REQUEST["action"]=="systemholdpage")
{
  running_action(0);
  ship_in_hb();	
  include "soudata/source/sou_system_hold.php";
}

if($_REQUEST["action"]=="shipoverpage")
{
  running_action(1);
  include "soudata/source/sou_ship_overview.php";
}

if($_REQUEST["action"]=="dailygiftpage")
{
  running_action(1);
  include "soudata/source/sou_dailygift.php";
}

if($_REQUEST["action"]=="findpage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_find.php";
}


if($_REQUEST["action"]=="shipresearchpage")
{
  running_action(1);
  //shipnotready();
  include "soudata/source/sou_ship_research.php";
}

if($_REQUEST["action"]=="modulholdpage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_system_modulhold.php";
}

if($_REQUEST["action"]=="factorypage")
{
  running_action(1);
  ship_in_hb();
  include "soudata/source/sou_factory.php";
}

if($_REQUEST["action"]=="baonadastationpage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_baonadastation.php";
}

if($_REQUEST["action"]=="hyperbubblepage")
{
  running_action(0);
  //shipnotready();
  ship_in_hb();
  include "soudata/source/sou_hyperbubble.php";
}

if($_REQUEST["action"]=="createcolonypage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_create_colony.php";
}

if($_REQUEST["action"]=="systemprestigepage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_system_prestige.php";
}

if($_REQUEST["action"]=="creatorbridgepage")
{
  running_action(0);
  ship_in_hb();
  include "soudata/source/sou_creatorbridge.php";
}

if($_REQUEST["action"]=="hyperfunk")
{
  running_action(1);
  include "soudata/source/sou_hyperfunk.php";
}

if($_REQUEST["action"]=="squad")
{
  running_action(1);
  include "soudata/source/sou_squad.php";
}

/*
if($_REQUEST["action"]=="politics")
{
  running_action(1);
  include "soudata/source/sou_politics.php";
}

if($_REQUEST["action"]=="politicscolonypage")
{
  running_action(1);
  include "soudata/source/sou_politics_colony.php";
}
*/

if($_REQUEST["action"]=="fracforumpage")
{
  running_action(1);
  include "soudata/source/sou_fracforum.php";
}
if($_REQUEST["action"]=="showdatapage")
{
  running_action(1);
  include "soudata/source/sou_showdata.php";
}
//�berpr�fen ob das system bereits kolonisiert worden ist
function check_population()
{
  global $player_x, $player_y, $soudb;
  
  $db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$player_x' AND y='$player_y' AND fraction>0",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==0)
  {
    echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();
	
    echo 'Achtung: Es existiert noch keine Kolonie.<br>';

    rahmen2_unten();
    echo '<br>';
    rahmen0_unten();
    die('</body></html>');
  }  
}


//�berpr�fen ob der spieler gerade eine aktion durchf�hrt
function running_action($go)
{
  global $player_atimer1time, $player_atimer1typ, $player_x, $player_y;

  if($player_atimer1time>time()+1)
  {
  /*
    //sekunden in stunden/minuten/sekunden umrechnen
    $zeit=$player_atimer1time-time();
	$text='<div id="counterbox">';

	if($player_atimer1typ==1)//mining
    {
      $text.='Du suchst im Asteroidenfeld nach Rohstoffen.';
      //$target='sou_main.php?action=systempage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
      //creditbeschleuniger
      $text.='<br>F&uuml;r 1 Credit pro verbleibenden 5 Minuten kann die Aktion sofort abgeschlossen werden: <a href="sou_main.php?action=startpage&cboost=1" class="btn">beschleunigen</a><br>';      
    }
    elseif($player_atimer1typ==2)//hyperraumflug
    { 
      $text.='Das Raumschiff befindet sich gerade im Hyperraumflug.<br>Zielkoordinaten: '.$player_x.':'.$player_y;
      $text.='<br>Die Reisezeit berechnet sich aus Beschleunigungsphase, &Uuml;berlichtflug und Verz&ouml;gerungsphase. F&uuml;r die Beschleunigung und Verz&ouml;gerung werden jeweils 120 Sekunden ben&ouml;tigt.';
      //$target='sou_main.php?action=sectorpage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
    }
    elseif($player_atimer1typ==3)//br�cke der erbauer
    { 
      $text.='Das Raumschiff befindet sich gerade im Hyperraumflug zwischen 2 Stationen einer Br&uuml;cke der ERBAUER.<br>Zielkoordinaten: '.$player_x.':'.$player_y;
      //$target='sou_main.php?action=sectorpage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
    }
    elseif($player_atimer1typ==4)//hyperraumtunnel
    { 
      $text.='Das Raumschiff durchfliegt aktuell einen Hyperraumtunnel.';
      //$target='sou_main.php?action=sectorpage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
      //creditbeschleuniger
      //$text.='<br>F&uuml;r 1 Credit pro verbleibenden 5 Minuten kann die Aktion sofort abgeschlossen werden: <a href="sou_main.php?action=startpage&cboost=1" class="btn">beschleunigen</a><br>';      
    }
    elseif($player_atimer1typ==5)//schiffsreparatur
    { 
      $text.='Das Raumschiff wird aktuell repariert.';
      //$target='sou_main.php?action=sectorpage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
      //creditbeschleuniger
      $text.='<br>F&uuml;r 1 Credit pro verbleibenden 5 Minuten kann die Aktion sofort abgeschlossen werden:&nbsp;<a href="sou_main.php?action=startpage&cboost=1" class="btn">beschleunigen</a><br>';      
    }
    elseif($player_atimer1typ==6)//ansehen im sonnensystem erringen
    { 
      $text.='Das Raumschiff sammelt Rohstoffe und spendet diese um das Ansehen deiner Fraktion zu erh&ouml;hen.';
      //$target='sou_main.php?action=sectorpage';
      $endtext='Die Aktion wurde abgeschlossen. <a href=\"sou_main.php?action=systempage\" class=\"btn\">weiter</a>';
    }
    else $text='Was machst Du da nur?';
    $text.='<div id="counter" align="center">&nbsp;</div>';
    
    $text.='</div>';

    echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();
    echo '<table width="100%"><tr><td align="center">'.$text.'</td></tr></table>';
    //counter starten
    echo '<script type="text/javascript" language="javascript">';
    //echo 'counter('.$zeit.',"counter", "'.$target.'");';
    echo 'counter('.$zeit.',"counterbox", "counter", "'.$endtext.'");';
    echo '</script>';
 
    rahmen2_unten();
    echo '<br>';
 */   
    if($go==0)
    {
      echo '<br><br>';
      rahmen0_oben();
      echo '<br>';
	  rahmen2_oben();
      echo '<div align="center">W&auml;hrend einer laufenden Aktion ist hier kein Zugriff m&ouml;glich. <a href="sou_main.php?action=systempage" class="btn">System</a>';
      echo '</div>';
  	  rahmen2_unten();
  	  echo '<br>';
      rahmen0_unten();
      die('</body></html>');
    }
  }  
}

function shipnotready()
{
  global $player_shipnotready;
  
  if($player_shipnotready==1)
  {
    echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();
	
    echo 'Achtung: Das Raumschiff ist nicht funktionst&uuml;chtig. Eine �berpr�fung ist im Modulkomplex m&ouml;glich. Aktuell besteht nur Zugriff 
    auf den Modulkomplex, den Lagerkomplex, die Fabriken, die Upgrade-O-Modul und die Schiffsdaten.<br><br>';
    echo '<a href="sou_main.php?action=modulholdpage"><div class="b1">Modulkomplex</div></a><br>';

    rahmen2_unten();
    echo '<br>';
    rahmen0_unten();
    die('</body></html>');
  }	
}

function ship_in_hb()
{
  global $player_in_hb;
  
  if($player_in_hb>0)
  {
    echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();
	
    echo 'Das Raumschiff befindet sich in einer Hyperraumblase und daher ist ein Zugriff nicht m&ouml;glich.<br>';
    echo '<a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';
    rahmen2_unten();
    echo '<br>';
    rahmen0_unten();
    die('</body></html>');
  }	
}


die('</center></body></html>');
?>
