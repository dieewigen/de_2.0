<?php
//anzeige in der logdatei
//include "croninfo.inc.php";

set_time_limit(120);
/*
$directory=str_replace("\\\\","/",$HTTP_SERVER_VARS["SCRIPT_FILENAME"]);
$directory=str_replace("/tickler/kt.php","/",$directory);
if ($directory=='')$directory='../';
*/
$directory="../";
include $directory."inc/sv.inc.php";
if($sv_debug==0 && $sv_comserver==0){
	if(!in_array(intval(date("i")), $GLOBALS['kts'][date("G")]) && $sv_debug!=1){
		die('<br>KT: NO TICK TIME<br>');
	}
}

include_once $directory."inccon.php";
if($sv_comserver==1)include_once $directory.'inc/svcomserver.inc.php';
include_once $directory."inc/schiffsdaten.inc.php";
include_once $directory."inc/userartefact.inc.php";
include_once $directory."functions.php";
include_once $directory."issectork.php";
include_once $directory."inc/lang/".$sv_server_lang."_kt.lang.php";
include_once $directory."lib/special_ship.class.php";
include_once $directory.'lib/bg_defs.inc.php';
include_once $directory.'lib/bg_defs.inc.php';
?>

<html>
<head>

</head>
<body>
<?php

///////////////////////////////////////////////////////
// in einer normalen Runde mit 100% Recycling ist 
// dieses in einer BR jedoch nicht aktiv
///////////////////////////////////////////////////////
//ewige runde
if($sv_ewige_runde==1){
	//nichts	
}elseif($sv_hardcore==1){
	//nichts
  
}else{
	//normale runde
	
	//rundenstatus auslesen
	$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
	$row = mysql_fetch_array($db_daten);
	if($row["tick"]<=0)$ticks=1;else $ticks=$row["tick"];
	
	if ($ticks<2500000 OR $sv_comserver_roundtyp==1){
		if($sv_comserver_roundtyp==1)$ticks-=2500000;//fix für community-server in der BR
		//wenn die ticks kleiner als die maximale tickzahl sind, dann läuft die runde noch
		if($ticks<$sv_winscore){

		}else{ //erhabenenkampf, oder die runde ist zu ende

			//überprüfen ob der eh-kampf noch läuft, oder ab es schon rum ist
			$result = mysql_query("SELECT doetick, winid, winticks FROM de_system",$db);
			$row = mysql_fetch_array($result);
			$doetick=$row["doetick"];
			$winticks=$row["winticks"];
			$winid=$row["winid"];

			if($winticks<=1 AND $doetick==0 AND $winid>0){//rundenende

			}else{ //eh-kampf läuft
				$sv_oscar=0;
			}
		}
	}else{ //battleround

	}
}
///////////////////////////////////////////////////////
// Tickdate auslesen
///////////////////////////////////////////////////////
$result = mysql_query("SELECT domtick FROM de_system",$db);
$row = mysql_fetch_array($result);
$domtick=$row["domtick"];
if ($domtick==1){
echo '<br>Starte Tick';
echo date("d/m/Y - H:i:s");
echo '<br><br>';

//Kampfticks mitzählen
mysql_query("UPDATE de_system SET kt=kt+1;",$db);

//Check ob die Missionen zu Ende sind
checkMissionEnd();

//größter tick
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick, kt AS max_kt FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];
$max_kt = $row["max_kt"];

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// systemkampf
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

include_once "kt_systemkampf.php";

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//archäologie
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

//include "kt_archeology.php";

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//flottenbewegung - spielerflotten
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

$res = mysql_query("select user_id, aktion, zeit, aktzeit, e81, e82, e83, e84,
e85, e86, e87, e88, e89, e90, hsec, hsys, zielsec from de_user_fleet where aktion > 0",$db);

$num = mysql_num_rows($res);

echo "<br>$num Flotten-Datens&auml;tze gefunden<br>";

for ($i=0; $i<$num; $i++){
	$uid     = mysql_result($res, $i, "user_id");
	$aktion  = mysql_result($res, $i, "aktion");
	$zeit    = mysql_result($res, $i, "zeit");
	$aktzeit = mysql_result($res, $i, "aktzeit");



	//flottenbewegung
	if ($zeit==0 && $aktzeit>0) $aktzeit--; //wenn er nicht fliegt, dann verteidigt er gerade

	if ($zeit > 0) $zeit--; //er fliegt noch, also rz verkürzen

	if ($zeit==0 && $aktzeit==0){ //er ist mit allem fertig
		//if ($aktion==1 OR $aktion==2 OR $aktion==4){ //gekämpft oder verteidigt-> heimflug
		if ($aktion==1 OR $aktion==2){ //gekämpft oder verteidigt-> heimflug
			$ez[0]  = mysql_result($res, $i, "e81");
			$ez[1]  = mysql_result($res, $i, "e82");
			$ez[2]  = mysql_result($res, $i, "e83");
			$ez[3]  = mysql_result($res, $i, "e84");
			$ez[4]  = mysql_result($res, $i, "e85");
			$ez[5]  = mysql_result($res, $i, "e86");
			$ez[6]  = mysql_result($res, $i, "e87");
			$ez[7]  = mysql_result($res, $i, "e88");
			$ez[8]  = mysql_result($res, $i, "e89");
			$ez[9]  = mysql_result($res, $i, "e90");
			$zsec   = mysql_result($res, $i, "zielsec");
			$sector = mysql_result($res, $i, "hsec");
			$system = mysql_result($res, $i, "hsys");
			//rasse laden
			$db_daten=mysql_query("SELECT rasse FROM de_user_data WHERE sector='$sector' AND `system`='$system'",$db);
			$db_daten = mysql_fetch_array($db_daten);
			$rasse=$db_daten["rasse"];
			echo '<br>Rasse: '.$rasse;

			//reisezeit berechnen
			$rz=get_fleet_ground_speed($ez, $rasse, $uid);

			//entfernungszuschlag
			//schauen wieviele artefakte man, die die nahen sektoren vergr��ern
			//$db_datennah=mysql_query("SELECT id FROM de_user_artefact WHERE id=5 AND user_id='$uid'",$db);
			$wert = 5;// + mysql_num_rows($db_datennah);

			if ($zsec<>$sector){
				/*
				if ($zsec<$sector + $wert and $zsec > $sector - $wert) $rz++;
				else $rz=$rz+2;
				 */
				$rz=$rz+2;
			}

			$sql="UPDATE de_user_fleet SET aktion = 3, zeit = $rz, aktzeit = $aktzeit, zielsec=hsec, zielsys=hsys WHERE user_id = '$uid'";
			//fix für archäologie
			//if($aktion==4)$sql="UPDATE de_user_fleet SET aktion = 3, zeit = gesrzeit, zielsec=hsec, zielsys=hsys WHERE user_id = '$uid'";

			mysql_query($sql,$db);
		}
		else //ist daheim angekommen->verteidige heimatsystem
		{
			//Kein Update bei Missionen, die werden über den Timestamp gesteuert
			if($aktion!=4){
				$sql="UPDATE de_user_fleet SET aktion = 0, zeit = 0, gesrzeit = 0, aktzeit = 0, zielsec = 0, zielsys = 0 where user_id = '$uid'";
				mysql_query($sql,$db);
			}
		}
	}else{ //er ist noch unterwegs, bzw. verteidigt-> update der zeiten
		$sql="update de_user_fleet set zeit = $zeit, aktzeit = $aktzeit where	user_id = '$uid'";
		mysql_query($sql,$db);
	}
}

//nach dem kampf die fleetsize für deffende flotten neu berechnen
mysql_query("UPDATE de_user_fleet SET fleetsize = e81+e82+e83+e84+e85+e86+e87+e88+e89+e90 WHERE zeit=0 AND aktzeit>0",$db);

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//sektorkampf
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

include_once "kt_sectorkampf.php";

//flottenbewegung - sektorflotten
$res = mysql_query("select sec_id, aktion, zeit, aktzeit, zielsec from de_sector where aktion > 0",$db);

$num = mysql_num_rows($res);

echo "<br>$num Sektor-Flotten-Datens�tze gefunden<br>";

for ($i=0; $i<$num; $i++)
{
  $sec_id     = mysql_result($res, $i, "sec_id");
  $aktion  = mysql_result($res, $i, "aktion");
  $zeit    = mysql_result($res, $i, "zeit");
  $aktzeit = mysql_result($res, $i, "aktzeit");

  //flottenbewegung
  if ($zeit==0 && $aktzeit>0) $aktzeit--; //wenn er nicht fliegt, dann verteidigt er gerade

  if ($zeit > 0) $zeit--; //er fliegt noch, also rz verk�rzen

  if ($zeit==0 && $aktzeit==0) //er ist mit allem fertig
  {
    if ($aktion==1 OR $aktion==2) //gek�mpft oder verteidigt-> heimflug
    {
      $zsec   = mysql_result($res, $i, "zielsec");
      $rz=12;
      //entfernungzuschlag
      //if ($zsec<$sec_id+5 and $zsec>$sec_id-5) $rz=$rz+0;else $rz=$rz+2;
        
      $sql="update de_sector set aktion = 3, zeit = $rz, aktzeit =
      $aktzeit, zielsec=$sec_id where sec_id = '$sec_id'";
      mysql_query($sql,$db);
    }
    else //ist daheim angekommen->verteidige heimatsystem
    {
      $sql="update de_sector set aktion = 0, zeit = 0, gesrzeit = 0,
      aktzeit = 0, zielsec = 0 where sec_id = '$sec_id'";
      mysql_query($sql,$db);
    }
  }
  else //er ist noch unterwegs, bzw. verteidigt-> update der zeiten
  {
    $sql="update de_sector set zeit = $zeit, aktzeit = $aktzeit where
  sec_id = '$sec_id'";
    mysql_query($sql,$db);
  }
}


////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// Battlegrounds
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
include_once 'kt_manage_bg.php';

//lege in der datenbank die zeit des letzten ticks ab
$time=date("YmdHis");
mysql_query("update de_system set lastmtick = '$time'",$db);
print '<br><br>Letzter Tick: '.date("d/m/Y - H:i:s");

function xecho($str){
	global $cachefile;
	//echo $str;
	if ($cachefile) fwrite ($cachefile, $str);
}

//erstelle datei mit der zeit des letzten kampf-ticks
$filename = "../cache/lastmtick.tmp";

$cachefile = fopen ($filename, 'w');

$zeit=date("H:i");
echo $zeit;
xecho('<?php $lastmtick="'.$zeit.'";');

xecho('?>');


} else {
	echo '<br>Kampfticks deaktiviert.<br>'; //doetick
}
?>

</body>
</html>
