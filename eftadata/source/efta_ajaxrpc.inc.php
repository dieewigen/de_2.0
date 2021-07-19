<?php
//$eftachatbotdefensedisable=1;
include "eftadata/source/efta_functions.php";
include "inc/header.inc.php";
include "eftadata/quests/queststart.php";
include "eftadata/lib/efta_dbconnect.php";
include 'outputlib.php';

//efta ist jetz immer aktiv
$_SESSION["ums_useefta"]=1;


mt_srand((double)microtime()*10000);

//konstanten definieren
$maxplayerlevel=70;
$maxbackpack=10;

//de-techdata nur auslesen, wenn efta in de integriert ist
if($sv_efta_in_de==1)
{
  $db_daten=mysql_query("SELECT restyp05, techs, palenium, artbldglevel  FROM de_user_data WHERE user_id='$ums_user_id'",$db);
  $row = mysql_fetch_array($db_daten);
  $techs=$row["techs"];
  $restyp05=$row["restyp05"];
  $palenium_anz=$row["palenium"];
  $artbldglevel=$row["artbldglevel"];

  if ($techs[27]==1)$has_palenium=1; else $has_palenium=0;
  if ($techs[28]==1)$has_artbldg=1; else $has_artbldg=0;
}

mt_srand((double)microtime()*10000);

$date_format='d.m.Y - H:i';

//grafikpfad optimieren
$gpfad=$sv_image_server_list[0].'s/';

if(!isset($ums_user_id) OR $ums_user_id<1) die('no session');

echo '<script language="javascript">';
if(!isset($_REQUEST['w']))echo '
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
});';
echo 'disablekeys=0;';
//echo "document.execCommand('BackgroundImageCache', false, true);";
echo '</script>';



if($show_activetime_msg==1) 
{
	echo '<br><br>';
	rahmen0_oben();
	rahmen2_oben();
	echo '<table width="100%"><tr><td align="center">Du erh&auml;ltst als Aktivit&auml;tsbonus einen Schwarzmarktcredit.</td></tr></table>';
	rahmen2_unten();
	rahmen0_unten();
}

//include "bannerunterbrechung.php";
//echo '<div id="stolen1"></div>';
//echo '<script>parent.parent.frames["eftamenu"].drawmenu();</script>';

$gpfad=$ums_gpfad.'e/';

if (isset($o) AND isset($a))
{
  echo '<font color="FF0000">Es ist nicht m&ouml;glich gleichzeitig 2 Aktionen durchzuf&uuml;hren.</font><br><br>';
  exit;
}

//cyborgdaten auslesen
if($_SESSION["efta_user_id"]>0)
{
  $db_daten=mysql_query("SELECT * FROM de_cyborg_data WHERE user_id='$_SESSION[efta_user_id]'",$eftadb);
  $num = mysql_num_rows($db_daten);
  $row = mysql_fetch_array($db_daten);
  
  $hp=$row["hp"];
  $mp=$row["mp"];
  $mpmax=$row["mpmax"];

  $bewpunkte=$row["bewpunkte"];
  $exp=$row["exp"];
  $level=$row["level"];
  $player_level=$row["level"];
  $str=$row["str"];
  $dex=$row["dex"];
  $konst=$row["konst"];
  $map=$row["map"];
  $x=$row["x"];
  $y=$row["y"];
  $fixenm=$row["fixenm"];
  //$city=$row["city"];
  $arena=$row["arena"];
  $arenawon=$row["arenawon"];
  $arenalost=$row["arenalost"];
  $player_fame=$row["fame"];
  $mapviewsize=$row["mapviewsize"];
  $showmsg=$row["showmsg"];
  $showmsg2=$row["showmsg2"];
  $lastap=$row["lastap"];
  $inbldg=$row["inbldg"];
  $player_buff1=$row["buff1"];
  $player_cooldown1=$row["cooldown1"];
  $player_admin=$row["admin"];
  $player_killcounter=$row["killcounter"];
  if($row["backpacksize"]>$maxbackpack)$maxbackpack=$row["backpacksize"];
  $_SESSION["efta_spielername"]=$row["spielername"];
  
  //wenn noch kein name vergeben worden ist, dann verzweigen, damit der spieler einen vergibt
  if($_SESSION["efta_spielername"]=='')
  {
    //spielernamen setzen?
    if($_REQUEST["newspielername"])
	{
  	  $spielername=$_REQUEST["newspielername"];
  	  if(strlen($_REQUEST["newspielername"])>2 AND strlen($_REQUEST["newspielername"])<=20)
      {
    	//testen ob er nur aus buchstaben besteht
  		if(!preg_match ("#^[äöüÄÖÜa-z-]+$#i", $_REQUEST["newspielername"]))$fehlermsg.='Der Spielername ist ungültig.<br>';
  		else 
  		{
  	  	  //schauen ob er schon vergeben ist
      	  $db_daten=mysql_query("SELECT user_id FROM de_cyborg_data WHERE spielername='$_REQUEST[newspielername]'",$eftadb);
      	  $vorhanden = mysql_num_rows($db_daten);
      	  if ($vorhanden>0)$fehlermsg.='Dieser Spielername ist bereits vergeben.<br>';
	  	  else
	  	  {
	  		//namen hinterlegen
  			$_SESSION["efta_spielername"]=$_REQUEST["newspielername"];
  			mysql_query("UPDATE de_cyborg_data SET spielername='$_REQUEST[newspielername]' WHERE user_id = '$efta_user_id'",$eftadb);
	  		
        	echo '<script>lnk("");</script>';
        	exit;
	  	  }  
  		}
  	  }
  	  else $fehlermsg.='Der Spielername hat nicht die richtige L&auml;nge.<br>';
	}
  	
    echo '<script language="javascript">disablekeys=1;</script>';  	  
    rahmen0_oben();
    rahmen2_oben();
  
    ?>
    <script>
    function set_spielername()
    {
    	var v1=$("#newspielername").val();
    		
   		lnk('newspielername='+v1);
    }
   	</script>
   	<?php
    
    
  	//echo '<form action="eftamain.php" method="POST">';
  	echo '<br><br><table width="600" border="0"> ';
  	echo '<tr><td width="30%">Spielername vergeben:</td><td width="70%"><input type="text" id="newspielername" name="newspielername" size="25" maxlength="20" value="'.$spielername.'"></td></tr>';
  	echo '<tr><td colspan="2"><font color="#FF0000">'.$fehlermsg.'</font>
  Hier kannst du den Namen Deines Charakters vergeben, den Du auf die Reise in unbekannte L&auml;nder schickst. F&uuml;r den Namen gelten die folgende Regeln:<br>
  - erlaubt sind nur Buchstaben<br>
  - erlaubt sind 3-20 Buchstaben<br>
  - er mu&szlig; zu einem Rollenspiel passen<br>
  - er darf nicht durch ein Copyright gesch&uuml;tzt sein<br>
  - er darf nicht rassistisch/diskriminierend/pornografisch sein<br>
  - er darf kein Stafflername sein, au&szlig;er man ist selbst der entsprechende Staffler<br><br>
  Eine Chatnutzung ist erst nach Vergabe des Spielernamens m&ouml;glich.
  </td></tr>';
  	echo '<tr align="center"><td colspan="2"><a class="gwaren" href="#" onClick="set_spielername()">&nbsp;weiter&nbsp;</a></td></tr>';
  
  	echo '</table>';
  
  	//echo '<form>';

  	rahmen2_unten();
  	rahmen0_unten();
  
  	die('</body></html>');
  }
  
  //maximale hitpoints berechnen
  //$hpmax=$row["hpmax"];
  $hpmax=$level*100+(($konst-10)*20);
}
else  //neuen char anlegen
{
  //sicherheitshalber nochhmal den sessioninhalt gegenchecken
  $db_daten=mysql_query("SELECT efta_user_id FROM de_user_data WHERE user_id='$ums_user_id'",$db);
  $row = mysql_fetch_array($db_daten);
  $euid=$row["efta_user_id"];
  
  //wenn größer null, die session berichtigen
  if($euid>0)
  {
  	$_SESSION["efta_user_id"]=$euid;
  }
  else
  {
    //werte vorbelegen
    $bewpunkte=5000;
    $exp=0;
    $level=1;
    $hp=100;
    $hpmax=100;
    $mp=0;
  	$mpmax=0;
  	$str=10;
  	$dex=10;
  	$konst=10;
  	$map=0;
  	$x=0;
  	$y=0;
  	$mapviewsize=64;
  	$player_killcounter=0;
  	//cyborgdatensatz anlegen
  	mysql_query("INSERT INTO de_cyborg_data (ext_user_id, spielername, sn_ext1, str, dex, konst, hp, hpmax, mp, mpmax, bewpunkte, exp, level, map, x, y, oldx, oldy, showinfo, fixenm) VALUES ('$ums_user_id', '$spielername', '$sv_server_tag','$str', '$dex', '$konst', '$hp', '$hpmax', '$mp', '$mpmax', '$bewpunkte', '$exp', '$level', '$map', '$x', '$y','$x', '$y','s0','s0')",$eftadb);
  	$efta_user_id=mysql_insert_id($eftadb);
  
  	//items vorblegen
  	//helm 3
  	add_item(2, 1);
  	//brust 5
  	add_item(3, 1);
  	//hose 11
  	add_item(4, 1);
  	//stiefel 12
  	add_item(5, 1);
  	//handschuhe 7
  	add_item(6, 1);
  	//schild 2
  	add_item(7, 1);
  	//waffenhand 1
  	add_item(8, 1);
  
  	//nachdem die gegenstände hinterlegt wurden diese auch anlegen
  
  	mysql_query("UPDATE de_cyborg_item SET equip=1 WHERE user_id='$efta_user_id'",$eftadb);
  
  	//geldbeutel
  	mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, equip) VALUES ('$efta_user_id', 1, 20, 0, 0)",$eftadb);
  
  	//den efta-account mit dem de-account verknüpfen
  	$sql="UPDATE de_user_data SET efta_user_id='$efta_user_id' WHERE user_id='$ums_user_id'";
  	mysql_query($sql, $db);
  }
}

//aktionspunkte verteilen
$zeitintervall=10;
$punkteprozeitintervall=10;
if(($lastap+$zeitintervall)<time())
{
  $punkte=(time()-$lastap)/$zeitintervall*$punkteprozeitintervall;
  
  $mpunkte=$sv_max_efta_bew_punkte-$bewpunkte;
  if ($punkte>$mpunkte)$punkte=$mpunkte;
  if ($punkte<0)$punkte=0;

  $bewpunkte+=$punkte;
  //if ($mp<$mpmax) $addmp=1;else $addmp=0;
  $lastap=time();
  mysql_query("UPDATE de_cyborg_data SET lastclick='$lastap' ,lastap='$lastap', bewpunkte = bewpunkte + '$punkte' WHERE user_id = '$efta_user_id'",$eftadb);
}

//nachricht nach dem lesen löschen
if($_REQUEST["showmsgread"]==1)
{
  mysql_query("UPDATE de_cyborg_data SET showmsg='' WHERE user_id='$efta_user_id';",$eftadb);
  $showmsg='';
}
elseif($_REQUEST["showmsgread"]==2)
{
  mysql_query("UPDATE de_cyborg_data SET showmsg2='' WHERE user_id='$efta_user_id';",$eftadb);
  $showmsg2='';
}

//exp für den nächsten level
$nextlevelexp=(($level+1)*500)*($level/2);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//obere leiste mit allen infos und aktionsmöglichkeiten
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function show_infobar()
{
	global $sv_efta_in_de, $gpfad, $ums_gpfad, $nextlevelexp, $level, $bewpunkte, $sv_max_efta_bew_punkte, $hp, $hpmax, $mp, $mpmax, $exp, $restyp05, $player_buff1; 

	echo '<div id="infobar1" style="font-size: 16px;color: #FFFFFF; position:absolute; overflow:hidden; background-image: url('.$gpfad.'bgpic1.png);left: 0px; top: 0px; width: 100%; height: 24px; z-index:10;">';
	//farbe für die bewegungspunkte bestimmen
	echo '<span style="position: absolute; left: 0px;">';
	$bcolor='#00FF00';
	if($bewpunkte<=$sv_max_efta_bew_punkte*0.75)$bcolor='yellow';
	if($bewpunkte<=$sv_max_efta_bew_punkte*0.50)$bcolor='orange';
	if($bewpunkte<=$sv_max_efta_bew_punkte*0.25)$bcolor='red';
	//farbe für die lebensenergie bestimmen
	$hpcolor='#00FF00';
	if($hp<=$hpmax*0.75)$hpcolor='yellow';
	if($hp<=$hpmax*0.50)$hpcolor='orange';
	if($hp<=$hpmax*0.25)$hpcolor='red';


	echo 'HP: <font color="'.$hpcolor.'">'.$hp.'/'.$hpmax.'</font>';
	//farbe für die psienergie bestimmen
	$hpcolor='#00FF00';
	if($mp<=$mpmax*0.75)$hpcolor='yellow';
	if($mp<=$mpmax*0.50)$hpcolor='orange';
	if($mp<=$mpmax*0.25)$hpcolor='red';
	echo '&nbsp;Psienergie: <font color="'.$hpcolor.'">'.$mp.'/'.$mpmax.'</font>';
	//erfahrungspunkte ausgeben
	echo '&nbsp;Exp: '.number_format($exp, "0",",",".").'/'.number_format($nextlevelexp, "0",",",".");
	//'</div></div></td></tr>';
	echo '&nbsp;BP: <font color="'.$bcolor.'">'.(number_format(floor($bewpunkte), "0",",",".")).'</font>';
	if ($player_buff1>time())echo '&nbsp;Lebensenergiefestigkeit: '.sec2min($player_buff1-time());
	//echo '<tr><td align="left">Andere Cyborgs: '.$cyborgs.'</td></tr>';
	if($sv_efta_in_de==1)echo '&nbsp;Tronic: '.number_format(floor($restyp05), "0",",",".");
	//echo '&nbsp;X: '.$x.' Y: '.$y;
	echo '</span>';



	echo '<span style="position: absolute; right: 0px;">';
	//quicklink-string zusammenbauen
	if($_SESSION["ums_chatoff"]) $qlstr="top.document.getElementById('gf').cols = '209, *, 0, 0';top.document.getElementById('gf').rows = '*';";
	else $qlstr="top.document.getElementById('gf').cols = '209, 620, *, 0, 0';top.document.getElementById('gf').rows = '*';";

	if($sv_efta_in_de==1 AND $_SESSION['ums_mobi']==1)
	{
		$qlstr="location.href='menu.php'";	
	}

//menüpunkte
$menutext= '';

//quicklink zu de, aber nur wenn efta nicht alleine steht
if($sv_efta_in_de==1)
{
  $menutext.= '&nbsp;<a href="#"><img src="'.$ums_gpfad.'g/ql2.gif" width="22" height="22" border="0" onclick="javascript:'.$qlstr.'" title="Kommandozentrale" title="Kommandozentrale"></a>';
}

//kartenansicht
$menutext.= '&nbsp;<a href="#" onClick="lnk(\'k=1\')"><img src="'.$gpfad.'b2.gif" width="22" height="22" border="0" title="Taste: m"></a>';
//rastplatz
$menutext.= '&nbsp;<a href="#" onClick="lnk(\'r=1\')"><img src="'.$gpfad.'b1.gif" width="22" height="22" border="0" title="Taste: r"></a>';
//questseite
$menutext.= '&nbsp;<a href="#" onClick="lnk(\'q=1\')"><img src="'.$gpfad.'b3.gif" width="22" height="22" border="0" title="Taste: q"></a>';
//cyborg mit rucksack und ausrüstung
$menutext.= '&nbsp;<a href="#" onClick="lnk(\'uk=1\')"><img src="'.$gpfad.'c1.gif" width="22" height="22" border="0" title="Taste: b"></a>';
//hilfelink
$menutext.= '&nbsp;<a href="http://help.bgam.es/index.php?thread=alu_de" target="_blank"><img src="'.$gpfad.'b4.gif" width="22" height="22" border="0" title="Hilfe"></a>';
//optionen link, jedoch nur dann, wenn efta allein steht
if($sv_efta_in_de==0)
{
  $menutext.= '&nbsp;<a href="#" onClick="lnk(\'action=optionspage\')"><img src="'.$gpfad.'b6.gif" width="22" height="22" border="0" title="Optionen"></a>';
}

//logout-link
$menutext.= '&nbsp;<a href="index.php?logout=1"><img src="'.$gpfad.'b5.gif" width="22" height="22" border="0" title="Logout"></a>';

echo $menutext;

echo '</span>';


echo '</div>';
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//eine evtl. nachricht anzeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($showmsg!='')
{
  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Neue Information&nbsp;</b></td></tr>';
  echo '</table><br>';*/
  
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Neue Information</b></div>');  

  echo '<table width="100%">
  <tr><td class="cell1">'.$showmsg.'</td></tr>
  <tr><td align="center"><br><a class="gwaren" href="#" onClick="lnk(\'showmsgread=1\')">&nbsp;weiter&nbsp;</a></td></tr>

  </table>';
  
  rahmen1_unten();
  rahmen0_unten();
  
  //infoleiste anzeigen
  show_infobar();
  
  die('</body></html>');
}
elseif($showmsg2!='')//kampfbericht
{
  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Kampfbericht&nbsp;</b></td></tr>';
  echo '</table><br>';*/
  
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Kampfbericht</b></div>');  

  echo '<table width="100%">
  <tr align="center"><td>'.show_efta_resline().$showmsg2.'</td></tr>
  <tr align="center">
  <td>weiter zu:
  <a href="#" onClick="lnk(\'showmsgread=2&r=2\')"><img src="'.$gpfad.'b1.gif" border=0 width="25" height="25" title="Rastplatz Regeneration"></a>
  <a href="#" onClick="lnk(\'showmsgread=2\')"><img src="'.$gpfad.'b2.gif" border=0 width="25" height="25" title="Karte"></a>
  <a href="#" onClick="lnk(\'showmsgread=2&uk=2\')"><img src="'.$gpfad.'c1.gif" border=0 width="25" height="25" title="Rucksack"></a>
  </td></tr>

  </table>';
  
  rahmen1_unten();
  rahmen0_unten();
  
  //infoleiste anzeigen
  show_infobar();
  
  
  die('</body></html>');
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//bildgröße der mapfiles vergrößern
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
/*
if($_REQUEST["mvs"]==1)
{
  //größer
  $mapviewsize+=16;
  if($mapviewsize>144)$mapviewsize=144;
  mysql_query("UPDATE de_cyborg_data SET mapviewsize='$mapviewsize' WHERE user_id='$efta_user_id';",$eftadb);
}
elseif($_REQUEST["mvs"]==2)
{
  //kleiner
  $mapviewsize-=16;
  if($mapviewsize<48)$mapviewsize=48;
  mysql_query("UPDATE de_cyborg_data SET mapviewsize='$mapviewsize' WHERE user_id='$efta_user_id';",$eftadb);
}
*/
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//levelup
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($exp>=$nextlevelexp AND $level<=$maxplayerlevel)
{
  include "eftadata/source/efta_levelup.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// optionsseite laden, wenn efta alleine steht
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($_REQUEST['action']=='optionspage' AND $sv_efta_in_de==0)
{
  include "eftadata/source/efta_options.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// übersichtskarte zeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($_REQUEST['action']=='showmappage')
{
  include "eftadata/source/efta_map.php";
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//schaue ob evtl gerade ein gegner in der db auf nen kampf wartet
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysql_query("SELECT * FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
$num = mysql_num_rows($db_daten);
if ($num>0)//kämpfen lassen
{
  //$gokampf=0;
  include "eftadata/source/efta_kampf.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//stele ansehen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["showstele"]))
{
  //schauen ob es dort ne info für den spieler gibt
  $db_daten=mysql_query("SELECT bldg FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map' AND bldg='4'",$eftadb);
  $anz = mysql_num_rows($db_daten);
  if($anz>0)
  {
    //id der nachricht auslesen
    $db_daten=mysql_query("SELECT flag1 FROM de_cyborg_struct WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
    $row = mysql_fetch_array($db_daten);
    $nachricht=$row["flag1"];
    //nachricht laden
    $filename='eftadata/msg/'.$nachricht.'.txt';
    if (file_exists($filename)==1)
    {
      $text=implode("",file("$filename"));
      //nachricht ausgeben
      mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
      echo '<script>lnk("");</script>';
      exit;
    }
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//baumenü und gebäude minen/ernten
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//if (isset($_REQUEST["bldgmenu"]))
include "eftadata/source/efta_area_mod.php";


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//portal benutzen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["useportal"]))
{
  //schauen ob es an der position ein portal gibt
  $db_daten=mysql_query("SELECT x FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map' AND bldg='2'",$eftadb);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    //zielkoordinaten aus der db holen
    $db_daten=mysql_query("SELECT tox, toy, toz FROM de_cyborg_struct WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
    $row = mysql_fetch_array($db_daten);
    $x=$row["tox"];
    $y=$row["toy"];
    $map=$row["toz"];
    //spieler zu den neuen koordinaten schicken
    mysql_query("UPDATE de_cyborg_data SET map='$map', x='$x', y='$y' WHERE user_id='$efta_user_id'",$eftadb);
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//stadt/heldentürme/sonstwas betreten
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if ($_REQUEST["enterbldg"]>0)
{
  //schauen ob es an der position eine stadt gibt
  $db_daten=mysql_query("SELECT bldg FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    $row = mysql_fetch_array($db_daten);
    $bldg=$row["bldg"];
    if($bldg==8)//heldenturm
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;
    }
    elseif($bldg==1)//stadt
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;
    }
    elseif($bldg==15)//fahrender magier
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;    	
    }
    elseif($bldg==16)//hafen
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;    	
    }
    elseif($bldg==17)//reisender mönch
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;    	
    }
    elseif($bldg==21)//taschenverkäufer bis stufe 20
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;    	
    }    
    elseif($bldg==22)//inkarnationsschrein
    {
      mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
      $inbldg=$bldg;    	
    }    
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//stadt/heldenturm verlassen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["leavebldg"]))
{
  $inbldg=0;
  //db updaten
  mysql_query("UPDATE de_cyborg_data SET inbldg='0' WHERE user_id='$efta_user_id';",$eftadb);
}
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//rastplatz aufrufen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["r"]))
{
  include "eftadata/source/efta_rast.php";
}
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//questseite aufrufen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["q"]) OR isset($_REQUEST["qdo"]))
{
  include "eftadata/source/efta_quest.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//ausrüstung/rucksack aufrufen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

if (isset($_REQUEST["uk"]))
{
  include "eftadata/source/efta_uk.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// stadt/heldenturm/gebäude betreten, wenn man in einem ist
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($inbldg>0)
{
  include "eftadata/source/efta_bldg.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// statistik aufrufen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

if($_REQUEST["action"]=='statisticpage')
{
  include "eftadata/source/efta_statistic.php";
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// man versucht sich zu bewegen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if($_REQUEST['w'])
{
	$w=intval($_REQUEST['w']);
  $xmod=0;$ymod=0;$go=0;
  switch($w){
    case 1: //norden
      $ymod=1;
      break;
    case 2: //osten
      $xmod=1;
      break;
    case 3: //sueden
      $ymod=-1;
      break;
    case 4: //westen
      $xmod=-1;
      break;
    default:
      break;
  }

  //daten aus der db holen
  $xmod=$x+$xmod;
  $ymod=$y+$ymod;
  $db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$xmod' AND y='$ymod' AND z='$map'",$eftadb);
  $num = mysql_num_rows($db_daten);
  
  if($num==1 AND $w>0 AND $w<5)
  {
  	$row = mysql_fetch_array($db_daten);
    $feldtyp=$row["groundtyp"];
    $fieldlevel=$row["fieldlevel"];
    $bldg=$row["bldg"];
    if ($feldtyp==1 OR $feldtyp==15)//man kann draufgehen
    {
      $benbewp=10;
      if($feldtyp==15)$benbewp=30;
      //schauen ob man eine vergünstigung durch eine straße hat
      if($bldg==19)$benbewp=$benbewp-$fieldlevel;
      
      //überprüfen ob man genug aktionspunkte hat
      if($bewpunkte>=$benbewp)
      {
        $x=$xmod;
        $y=$ymod;
        
        $go=1;
      }
      else 
      $msg='<font color="#FF0000">Der Cyborg hat keine Bewegungspunkte mehr.</font>';
    }
  }

  if ($go==1)//wenn man sich bewegt hat, dann ist es möglich mit etwas zu interagieren
  {
    //alte koordinaten speichern und neue koordinaten setzen, dazu noch einen bewegungspunkt abziehen
    $bewpunkte=$bewpunkte-$benbewp;
    mysql_query("UPDATE de_cyborg_data SET oldx=x, oldy=y WHERE user_id='$efta_user_id';",$eftadb);
    mysql_query("UPDATE de_cyborg_data SET map='$map', x='$x', y='$y', bewpunkte = bewpunkte - '$benbewp' WHERE user_id='$efta_user_id'",$eftadb);
    
    //schauen ob man auf eine dornschlinge trifft
    if($bldg==18 AND $player_buff1 <= time())
    {
	  $hp=$hp-round($hpmax/10)*($fieldlevel+1);
  	  if ($hp<=0) //man ist tot
  	  {
    	cyborg_die($efta_user_id, 1, 'Die Dornschlinge hat Dir den Rest gegeben und Du wirst von den Replikationsanlagen bei der n&auml;chstgelegen Stadt wiederbelebt.');
  	  }
  	  else mysql_query("UPDATE de_cyborg_data SET hp='$hp' WHERE user_id='$efta_user_id'",$eftadb);
	  $msg='<font color="FF0000">Die Dornschlinge zieht Dir '.number_format(round($hpmax/10*($fieldlevel+1)), 0,",",".").' Punkte Deiner Lebensenergie ab und geht in eine andere Existenzebene &uuml;ber.</font>';
	  //dornschlinge entfernen
	  mysql_query("UPDATE de_cyborg_map SET bldg=0, bldgpic=0, fieldlevel=0 WHERE x='$x' AND y='$y' AND z='$map' AND bldg=18", $eftadb);
    }
    
    //schauen ob man eine quest aus der statischen questliste annehmen kann
    //alle koordinaten parsen und schauen ob was paßt
    for($ik=0;$ik<count($map_quest_start);$ik++)
    {
      //echo $map.':'.$k.'<br>';
      if($map==$map_quest_start[$ik][0] AND ($x)==$map_quest_start[$ik][1] AND ($y)==$map_quest_start[$ik][2])
      {
        //man hat was gefunden
        //die quest jedoch nur dann annehmen, wenn man sie noch nicht hat
        $questid=$map_quest_start[$ik][3];
        $db_daten=mysql_query("SELECT user_id FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ='$questid'",$eftadb);
        $anz = mysql_num_rows($db_daten);
        //wenn anz 0 ist, dann hat man die quest noch nicht
        if($anz==0)
        {
          //quest in der db eintragen
          $filename="eftadata/quests/q$questid.php";
          include_once($filename);
          
          $zmap=$map_quest_start[$ik][4];
          $zx=$map_quest_start[$ik][5];
          $zy=$map_quest_start[$ik][6];

          mysql_query("INSERT INTO de_cyborg_quest SET user_id='$efta_user_id', typ='$questid', map='$zmap',
           x='$zx', y='$zy'",$eftadb);

          //kein kampf wenn quest
          $dokampf=1;
          //echo $q_text;
        }
      }
    }
    
    //schauen ob es ein event gibt
    if($bldg==20) 
    {
      include "eftadata/events/eventstart.php";
      for($ik=0;$ik<count($event_start);$ik++)
      {
        //echo $map.':'.$k.'<br>';
        if(($x)==$event_start[$ik][0] AND ($y)==$event_start[$ik][1] AND $map==$event_start[$ik][2])
        {
          include "eftadata/events/e".$event_start[$ik][3].".php";
		  
    	  //kein kampf wenn quest
          $dokampf=1;	
        }
      }
    }

    //schauen ob ein kampf stattfindet
    //1. methode, wenn es nen gegner in der map_db gibt
    $xvon=$x-1;
    $xbis=$x+1;
	$yvon=$y-1;
    $ybis=$y+1;    
    //auf der map schauen
    $result = mysql_query("SELECT * FROM de_cyborg_enm_map WHERE x>='$xvon' AND x<='$xbis' AND y>='$yvon' AND y<='$ybis' AND z='$z' ORDER BY RAND() LIMIT 0,1", $eftadb);
    $num = mysql_num_rows($result);
    if ($num>0)//kampf findet statt
    {
      //gegner nach entfernung zu 0,0 berechnen
      $row = mysql_fetch_array($result);
      $enm_id=$row["enm_id"];
      $enmx=$row["x"];
      $enmy=$row["y"];
      $enmz=$map;

      //gegner laden
      $result = mysql_query("SELECT * FROM de_cyborg_enm_list WHERE id='$enm_id' LIMIT 0,1", $eftadb);
      $num = mysql_num_rows($result);
      //echo $num;
      if($num==1)
      {
        //daten auslesen
        $row = mysql_fetch_array($result);
        $enm_id=$row["id"];
        $enm_level=$row["level"];
        $enm_hpmin=$row["hpmin"];
        $enm_hpmax=$row["hpmax"];
        $enm_attmin=$row["attmin"];
        $enm_attmax=$row["attmax"];
        $enm_armor=$row["armor"];
        $enm_lootid=$row["lootid"];
        $enm_name=$row["name"];

        //gegner in db packen
        $enm_hpmax=mt_rand($enm_hpmin, $enm_hpmax);
        $enm_hpakt=$enm_hpmax;
        $sql="INSERT INTO de_cyborg_enm (user_id, enm_id, level, name, hpakt, hpmax, attmin, attmax, armor, lootid, x, y, z)
         VALUES ('$efta_user_id','$enm_id', '$enm_level', '$enm_name','$enm_hpakt','$enm_hpmax.','$enm_attmin','$enm_attmax', '$enm_armor','$enm_lootid',
         '$enmx','$enmy','$enmz')";
        mysql_query($sql, $eftadb);
        //aufs efta-kampfscript verzweigen
        include "eftadata/source/efta_kampf.php";
      }
    }
  }
}

//questtext ausgeben
if ($q_text!='')
{
  mysql_query("UPDATE de_cyborg_data SET showmsg='$q_text' WHERE user_id='$efta_user_id';",$eftadb);
  echo '<script>lnk("");</script>';
  exit;
}



//eventtext ausgeben
if ($e_text!='')
{
  mysql_query("UPDATE de_cyborg_data SET showmsg='$e_text' WHERE user_id='$efta_user_id';",$eftadb);
  echo '<script>lnk("");</script>';
  exit;
}

//rahmen0_oben();

if($msg!='')
{
  rahmen2_oben();
  echo $msg;
  rahmen2_unten();
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//map zeichnen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//dazu erstmal mapdaten laden
//mapdaten laden
$mapx=31;$mapy=19;
unset($mapzeile);unset($maparray);
for($i=0;$i<$mapx;$i++)$mapzeile[]= array ( 0, 0, 0, 0, 0, 0, 0); //groundtyp, bild, bldg
for($i=0;$i<$mapy;$i++)$maparray[]=$mapzeile;

$brangexa=$x-16;
$brangexe=$x+16;

$brangeya=$y-10;
$brangeye=$y+10;

//daten aus der db holen
$db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND z='$map'",$eftadb);
while($row = mysql_fetch_array($db_daten))
{
  $groundtyp=$row["groundtyp"];
  $groundpic=$row["groundpic"];

  $xachse=$row["x"]-$x+floor($mapx/2);
  $yachse=$y-$row["y"]+floor($mapy/2);

  //y, x, groundtyp/bild/
  $maparray[$yachse][$xachse][0]=$groundtyp;
  $maparray[$yachse][$xachse][1]=$groundpic;
  $maparray[$yachse][$xachse][2]=$row["bldg"];
  $maparray[$yachse][$xachse][3]=$row["fieldlevel"];
  $maparray[$yachse][$xachse][4]=$row["fieldamount"];
  $maparray[$yachse][$xachse][5]=$row["groundpicext"];
  $maparray[$yachse][$xachse][6]=$row["bldgpic"];
}

//auf küste überprüfen
for($j=0;$j<$mapy;$j++)
{
  for($i=0;$i<$mapx;$i++)
  {
    if($maparray[$j][$i][0]==0)
    {
      //schauen ob das nachbarfeld land ist
      if($maparray[$j-1][$i][0]>0)$dn[0]=1;else $dn[0]=0;
      if($maparray[$j][$i+1][0]>0)$dn[1]=1;else $dn[1]=0;
      if($maparray[$j+1][$i][0]>0)$dn[2]=1;else $dn[2]=0;
      if($maparray[$j][$i-1][0]>0)$dn[3]=1;else $dn[3]=0;
      $grenze='g'.$dn[0].$dn[1].$dn[2].$dn[3];
      //echo $grenze.' '.$j.':'.$i.' -> ';
      switch ($grenze) {
      	case 'g0010':
      		$maparray[$j][$i][1]='2';
      		break;
      	case 'g0001':
      		$maparray[$j][$i][1]='3';
      		break;
      	case 'g1000':
      		$maparray[$j][$i][1]='4';
      		break;	
      	case 'g0100':
      		$maparray[$j][$i][1]='5';
      		break;
      	case 'g0101':
      		$maparray[$j][$i][1]='6';
      		break;
      	case 'g1010':
      		$maparray[$j][$i][1]='7';
      		break;
      	case 'g1011':
      		$maparray[$j][$i][1]='8';
      		break;
      	case 'g1101':
      		$maparray[$j][$i][1]='9';
      		break;
      	case 'g1110':
      		$maparray[$j][$i][1]='10';
      		break;
      	case 'g0111':
      		$maparray[$j][$i][1]='11';
      		break;
      	case 'g0011':
      		$maparray[$j][$i][1]='12';
      		break;
      	case 'g1001':
      		$maparray[$j][$i][1]='13';
      		break;
    	case 'g1100':
      		$maparray[$j][$i][1]='14';
      		break;
   		
      	case 'g0110':
      		$maparray[$j][$i][1]='15';
      		break;
      	case 'g1111':
      		$maparray[$j][$i][1]='16';
      		break;
      	default:
      		break;
      }
    }
  }
}


//auf straße überprüfen
for($j=0;$j<$mapy;$j++)
{
  for($i=0;$i<$mapx;$i++)
  {
    if($maparray[$j][$i][2]==19)//wenn gebäudetyp 19 = straße
    {
      //schauen ob das nachbarfeld auch ein straßenfeld ist
      if($maparray[$j-1][$i][2]==19)$dn[0]=1;else $dn[0]=0;
      if($maparray[$j][$i+1][2]==19)$dn[1]=1;else $dn[1]=0;
      if($maparray[$j+1][$i][2]==19)$dn[2]=1;else $dn[2]=0;
      if($maparray[$j][$i-1][2]==19)$dn[3]=1;else $dn[3]=0;
      $grenze='g'.$dn[0].$dn[1].$dn[2].$dn[3];
      //echo $grenze.' '.$j.':'.$i.' -> ';
      switch ($grenze) {
      	case 'g1010':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='51';
      	  else $maparray[$j][$i][6]='1';
      	break;
      	case 'g0101':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='52';
      	  else $maparray[$j][$i][6]='2';
      	break;
      	case 'g1000':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='53';
      	  else $maparray[$j][$i][6]='3';
      	break;
      	case 'g0100':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='54';
      	  else $maparray[$j][$i][6]='4';
      	break;
      	case 'g0010':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='55';
      	  else $maparray[$j][$i][6]='5';
      	break;
      	case 'g0001':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='56';
      	  else $maparray[$j][$i][6]='6';
      	break;
      	case 'g1111':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='57';
      	  else $maparray[$j][$i][6]='7';
      	break;
      	case 'g1100':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='58';
      	  else $maparray[$j][$i][6]='8';
      	break;
      	case 'g0110':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='59';
      	  else $maparray[$j][$i][6]='9';
      	break;
      	case 'g0011':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='60';
      	  else $maparray[$j][$i][6]='10';
      	break;
      	case 'g1001':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='61';
      	  else $maparray[$j][$i][6]='11';
      	break;
      	case 'g1110':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='62';
      	  else $maparray[$j][$i][6]='12';
      	break;
      	case 'g0111':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='63';
      	  else $maparray[$j][$i][6]='13';
      	break;
      	case 'g1011':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='64';
      	  else $maparray[$j][$i][6]='14';
      	break;
      	case 'g1101':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='65';
      	  else $maparray[$j][$i][6]='15';
      	break;
      	case 'g0000':
      	  if($maparray[$j][$i][3]==1)$maparray[$j][$i][6]='66';
      	  else $maparray[$j][$i][6]='16';
      	break;
 	
      	default:
      		break;
      }
    }
  }
}


//gegner auf der karte anzeigen
//daten aus der db holen
$db_daten=mysql_query("SELECT * FROM de_cyborg_enm_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND z='$map'",$eftadb);
while($row = mysql_fetch_array($db_daten))
{
  //koordinate bestimmen
  $xachse=$row["x"]-$x+floor($mapx/2);
  $yachse=$y-$row["y"]+floor($mapy/2);

  //flag setzen ob da ein gegner ist
  $maparray[$yachse][$xachse][7]=1;
}

//spieler auf der karte anzeigen
//anzeigezeitraum bestimmen
$anzeigezeitraum=time()-(3600*24);
//daten aus der db holen
$db_daten=mysql_query("SELECT * FROM de_cyborg_data WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND map='$map' AND lastclick > '$anzeigezeitraum'",$eftadb);
while($row = mysql_fetch_array($db_daten))
{
  //koordinate bestimmen
  $xachse=$row["x"]-$x+floor($mapx/2);
  $yachse=$y-$row["y"]+floor($mapy/2);

  //flag setzen ob da ein spieler ist
  $maparray[$yachse][$xachse][8]=$row["playerpic"];
}


//andere cyborgs anzeigen
//$cyborgs=mysql_query("SELECT count(*) FROM de_cyborg_data WHERE map='$map' AND x='$x' AND y='$y'", $db);
//$cyborgs=mysql_result($cyborgs,0,0)-1;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//linke leiste - gebietsinfo
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//typanzeige
$areastr='';
$groundtyp=$maparray[floor($mapy/2)][floor($mapx/2)][0];
$bldg=$maparray[floor($mapy/2)][floor($mapx/2)][2];
$fieldlevel=$maparray[floor($mapy/2)][floor($mapx/2)][3];
$fieldamount=$maparray[floor($mapy/2)][floor($mapx/2)][4];
if($groundtyp==1)$areatyp='Wiese';
elseif($groundtyp==15)$areatyp='Wald';

//bodentyp anzeigen
$areastr.='<tr><td align="left">Gel&auml;ndeart: '.$areatyp.'</td></tr>';

//baumenü anzeigen, wenn dort nichts steht
if($bldg==0)
{
  $areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'bldgmenu=1\')"><div class="b1">Baumen&uuml</div></a></td></tr>';
}
else //gebäudeaktionen
{
  //sonderbehandlung: portal, stadt, stele
  if($bldg==1)//stadt
  {
  	$areastr.='<tr><td align="left">Geb&auml;ude: Stadt</td></tr>';
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">betreten</div></a></td></tr>';
  }
  elseif($bldg==2)//portal
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'useportal=1\')"><div class="b1">Portal benutzen</div></a></td></tr>';
  }  
  elseif($bldg==3)//obelisk
  {
  	if($sv_efta_in_de==1)
  	{
  	  //questbutton anzeigen
      $areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'qdo=1\')"><div class="b1">T-Quest</div></a></td></tr>';
  	}
  }
  elseif($bldg==4)//stele
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'showstele=1\')"><div class="b1">Stele lesen</div></a></td></tr>';
  }  
  elseif($bldg==8)//heldenturm
  {
  	$areastr.='<tr><td align="left">Geb&auml;ude: Heldent&uuml;rme</td></tr>';
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">betreten</div></a></td></tr>';
  }
  elseif($bldg==15)//fahrender magier
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">Wagen betreten</div></a></td></tr>';
  }
  elseif($bldg==16)//hafen
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">Hafen betreten</div></a></td></tr>';
  }
  elseif($bldg==17)//reisender mönch
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">Wagen betreten</div></a></td></tr>';
  }
  elseif($bldg==18)//dornschlinge
  {
  	
  }
  elseif($bldg==19)//straße
  {
  	if($fieldlevel==1) $areastr.='<tr><td align="left">Geb&auml;ude: Weg</td></tr>';
  	else $areastr.='<tr><td align="left">Geb&auml;ude: Stra&szlig;e</td></tr>';
  }
  elseif($bldg==20)//event
  {
  	
  }
  elseif($bldg==21)//taschenverkäufer bis stufe 20
  {
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">Wagen betreten</div></a></td></tr>';
  }
  elseif($bldg==22)//inkarnationsschrein
  {
  	$areastr.='<tr><td align="left">Geb&auml;ude: Inkarnationsschrein</td></tr>';
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'enterbldg=1\')"><div class="b1">Schrein betreten</div></a></td></tr>';
  }
   
  else//alle anderen gebäude
  {
  	//gebäudename auslesen
    for($i=0;$i<count($bldgdef);$i++)
    {
      if($bldgdef[$i][4][0]==$bldg)
      {
        $gebname=$bldgdef[$i][0];
  	    break;
  	  }
    }
    $areastr.='<tr><td align="left">Geb&auml;ude: '.$gebname.'</td></tr>';
    $areastr.='<tr><td align="left">Kapazit&auml;t: '.$fieldamount.'/'.$fieldlevel.'</td></tr>';
  	$areastr.='<tr><td align="center"><a href="#" onClick="lnk(\'bldgmenu=1\')"><div class="b1">betreten</div></a></td></tr>';
  }
}


//gebietsinfo
echo '<div style="z-index: 100; position: absolute; top:23px; left: 0px;">';
rahmen1_oben('<div align="center" class="cell"><b>Gebietsinfo ('.$x.'/'.$y.')</b></div>');
echo '<table width="100%" border="0" cellpadding="1" cellspacing="1" class="cell">';
//echo '<tr><td>Gegnerlevel: '.get_enm_level($x,$y).$mapname[$map-1].'<br>'.$si.'</td></tr>';
if($areastr!='') echo $areastr;
echo '</table>';
rahmen1_unten();
echo '</div>';

//admin-menü
if($player_admin==1 AND 1==2)
{
  include "eftadata/source/efta_admintool.php";
}

//bildschirmaufteilung mit einer tabelle regeln
//echo '</td><td width="10"></td><td valign="top" width="'.($mapx*$mapviewsize+17).'">';
//echo '</td><td valign="top" width="100">';

//echo '</div>';

//mapbilder erstellen
$varnr='';$varnr2='';$varnr3='';$varnr4='';$varnr5='';
for($j=0;$j<$mapy;$j++)
{
  for($i=0;$i<$mapx;$i++)
  {
    //untergrund/boden
    if ($varnr!='')$varnr=$varnr.',';
    $varnr=$varnr.'"'.$maparray[$j][$i][1].'"';
    
    //landstruktur/berg/straße/
    if ($varnr3!='')$varnr3=$varnr3.',';
    if ($maparray[$j][$i][5]>0){$datei='s'.$maparray[$j][$i][5];$showstructlayer2=1;}else $datei='b';
    $varnr3=$varnr3.'"'.$datei.'"';

    //gebäudebild
    if ($varnr2!='')$varnr2=$varnr2.',';
    if ($maparray[$j][$i][6]>0)
    {
      //fix für die straßengrafiken
      if($maparray[$j][$i][2]==19){$datei='w'.$maparray[$j][$i][6];}
      else $datei='s'.$maparray[$j][$i][6];
      $showstructlayer1=1;
    }else $datei='b';
    $varnr2=$varnr2.'"'.$datei.'"';
    
    //gegnerbild
    if ($varnr4!='')$varnr4=$varnr4.',';
    if ($maparray[$j][$i][7]>0){$datei='e'.$maparray[$j][$i][7];$showenmlayer=1;}else $datei='b';
    $varnr4=$varnr4.'"'.$datei.'"';
    
    //spielerbild
    if ($varnr5!='')$varnr5=$varnr5.',';
    if ($maparray[$j][$i][8]>0){$datei='c'.$maparray[$j][$i][8];}else $datei='b';
    $varnr5=$varnr5.'"'.$datei.'"';
  }
}
$jsmapdata1=$varnr;
$jsmapdata3=$varnr2;
$jsmapdata4=$varnr3;
$jsmapdata5=$varnr4;
$jsmapdata6=$varnr5;

////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//questbilder erstellen
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

$brangexa=$x-16;
$brangexe=$x+16;

$brangeya=$y-10;
$brangeye=$y+10;


//zuerst die quests laden die man auf der karte abgeben kann
$db_daten=mysql_query("SELECT * FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND map='$map' AND erledigt=0",$eftadb);
while($row = mysql_fetch_array($db_daten))
{
  //koordinate bestimmen
  $xachse=$row["x"]-$x+floor($mapx/2);
  $yachse=$y-$row["y"]+floor($mapy/2);
  
  //flag setzen ob da eine abgebbare quest ist
  $maparray[$yachse][$xachse][9]=1;
}


$varnr='';
$mcy=0;
for($yc=($y+floor($mapy/2));$yc>=($y-floor($mapy/2));$yc--)
{
  $mcx=0;
  for($xc=($x-floor($mapx/2));$xc<=($x+floor($mapx/2));$xc++)
  {
    $found=0;
    for($ik=0;$ik<count($map_quest_start);$ik++)
    {
      if($map==$map_quest_start[$ik][0] AND ($xc)==$map_quest_start[$ik][1] AND ($yc)==$map_quest_start[$ik][2] OR $found==1)
      {
        $questid=$map_quest_start[$ik][3];
        $db_daten=mysql_query("SELECT user_id FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ=$questid",$eftadb);
        $anz = mysql_num_rows($db_daten);
        if($anz==0)
        {
          $found=1;
          break;
        }
      }
    }
    if($found==1){$datei='q1';$showquestlayer=1;}else $datei='b';
    if($found==0 AND $maparray[$mcy][$mcx][9]==1){$datei='q2';$showquestlayer=1;}
    if ($varnr!='')$varnr=$varnr.',';
    $varnr=$varnr.'"'.$datei.'"';
    
    $mcx++;
  }
  $mcy++;
}
$jsmapdata2=$varnr;


//kompletten div über die seite
echo '<div id="mapcontainer" style="position:absolute; top: 0px; left: 0px; width:100%; height:100%; overflow:hidden; background-color: #000000; z-index:2;">';
	//karteninhalt
    echo '<div id="mapcontent" style="background-size: 64px 64px; background-image: url('.$gpfad.'1.gif); background-repeat: repeat; position:absolute; overflow:hidden; left: 0px; top: 0px; width: 1984px; height: 1216px; z-index:3; ">';

echo '<script type="text/javascript">';

$picsizex=64;$picsizey=64;
//echo 'var j=0;var c=1;for(y=0;y<7;y++){document.write("<tr>");for(x=0;x<7;x++){if (q.charAt(c)=="2"){is="<img src=\"'.$gpfad.'s1.gif\">";}else {is="&nbsp;";}if (q.charAt(c)=="1"){is="<img src=\"'.$gpfad.'c1.gif\">";};document.write("<td style=\"width: 64px; height: 64px; background-image: url('.$gpfad.'"+n[j]+".gif)\" align=\"center\">"+is+"</td>");j=j+1;c=c+1;}document.write("</tr>");}';
echo 'var datenstring="";';
//untergrund
$layer=1;
echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata1.'));';
//gebirge, bäume
if($showstructlayer2==1)
{
  $layer++;
  echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata4.'));';
}
//gebäude usw.
if($showstructlayer1==1)
{
  $layer++;
  echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata3.'));';
}
//spieler
  $layer++;
  echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata6.'));';

//gegner
if($showenmlayer==1)
{
  $layer++;
  echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata5.'));';
}
//quest
if($showquestlayer==1)
{
  $layer++;
  echo 'datenstring+=showmap('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'", new Array('.$jsmapdata2.'));';
}

//cyborg und pfeile
$layer++;
echo 'datenstring+=showcybandstuff('.$mapy.', '.$mapx.', '.$picsizex.', '.$picsizey.', '.$layer.', "'.$gpfad.'");';

echo "$('#mapcontent').html(datenstring);";

echo 'setsize();';

//echo 'alert(1);';

echo '</script>';

//div der karte mit den maximal möglichen ausmaßen von x/y 10 mio
echo '</div>';//id mapcontent

echo '</div>';//id mapcontainer

//infoleiste anzeigen
show_infobar();
?>