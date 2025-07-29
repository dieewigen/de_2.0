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
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
	$row = mysqli_fetch_array($db_daten);
	if($row["tick"]<=0)$ticks=1;else $ticks=$row["tick"];
	
	if ($ticks<2500000 OR $sv_comserver_roundtyp==1){
		if($sv_comserver_roundtyp==1)$ticks-=2500000;//fix für community-server in der BR
		//wenn die ticks kleiner als die maximale tickzahl sind, dann läuft die runde noch
		if($ticks<$sv_winscore){

		}else{ //erhabenenkampf, oder die runde ist zu ende

			//überprüfen ob der eh-kampf noch läuft, oder ab es schon rum ist
			$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT doetick, winid, winticks FROM de_system", []);
			$row = mysqli_fetch_array($result);
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
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT domtick FROM de_system", []);
$row = mysqli_fetch_array($result);
$domtick=$row["domtick"];
if ($domtick==1){
echo '<br>Starte Tick';
echo date("d/m/Y - H:i:s");
echo '<br><br>';

//Kampfticks mitzählen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET kt=kt+1", []);

//Check ob die Missionen zu Ende sind
checkMissionEnd();

//größter tick
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick, kt AS max_kt FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
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
//flottenbewegung - spielerflotten
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, aktion, zeit, aktzeit, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90, hsec, hsys, zielsec FROM de_user_fleet WHERE aktion > 0", []);

$num = mysqli_num_rows($res);

echo "<br>$num Flotten-Datens&auml;tze gefunden<br>";

while ($fleet_row = mysqli_fetch_assoc($res)) {
	$uid     = $fleet_row['user_id'];
	$aktion  = $fleet_row['aktion'];
	$zeit    = $fleet_row['zeit'];
	$aktzeit = $fleet_row['aktzeit'];



	//flottenbewegung
	if ($zeit==0 && $aktzeit>0) $aktzeit--; //wenn er nicht fliegt, dann verteidigt er gerade

	if ($zeit > 0) $zeit--; //er fliegt noch, also rz verkürzen

	if ($zeit==0 && $aktzeit==0){ //er ist mit allem fertig
		//if ($aktion==1 OR $aktion==2 OR $aktion==4){ //gekämpft oder verteidigt-> heimflug
		if ($aktion==1 OR $aktion==2){ //gekämpft oder verteidigt-> heimflug
			$ez[0]  = $fleet_row['e81'];
			$ez[1]  = $fleet_row['e82'];
			$ez[2]  = $fleet_row['e83'];
			$ez[3]  = $fleet_row['e84'];
			$ez[4]  = $fleet_row['e85'];
			$ez[5]  = $fleet_row['e86'];
			$ez[6]  = $fleet_row['e87'];
			$ez[7]  = $fleet_row['e88'];
			$ez[8]  = $fleet_row['e89'];
			$ez[9]  = $fleet_row['e90'];
			$zsec   = $fleet_row['zielsec'];
			$sector = $fleet_row['hsec'];
			$system = $fleet_row['hsys'];
			//rasse laden
			$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT rasse FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
			$db_daten = mysqli_fetch_array($db_daten);
			$rasse=$db_daten["rasse"];
			echo '<br>Rasse: '.$rasse;

			//reisezeit berechnen
			$rz=get_fleet_ground_speed($ez, $rasse, $uid);

			//entfernungszuschlag
			$wert = 5;

			if ($zsec<>$sector){
				/*
				if ($zsec<$sector + $wert and $zsec > $sector - $wert) $rz++;
				else $rz=$rz+2;
				 */
				$rz=$rz+2;
			}

			//fix für archäologie
			//if($aktion==4)$sql="UPDATE de_user_fleet SET aktion = 3, zeit = gesrzeit, zielsec=hsec, zielsys=hsys WHERE user_id = '$uid'";

			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET aktion = 3, zeit = ?, aktzeit = ?, zielsec=hsec, zielsys=hsys WHERE user_id = ?", [$rz, $aktzeit, $uid]);
		}
		else //ist daheim angekommen->verteidige heimatsystem
		{
			//Kein Update bei Missionen, die werden über den Timestamp gesteuert
			if($aktion!=4){
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET aktion = 0, zeit = 0, gesrzeit = 0, aktzeit = 0, zielsec = 0, zielsys = 0 WHERE user_id = ?", [$uid]);
			}
		}
	}else{ //er ist noch unterwegs, bzw. verteidigt-> update der zeiten
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET zeit = ?, aktzeit = ? WHERE user_id = ?", [$zeit, $aktzeit, $uid]);
	}
}

//nach dem kampf die fleetsize für deffende flotten neu berechnen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET fleetsize = e81+e82+e83+e84+e85+e86+e87+e88+e89+e90 WHERE zeit=0 AND aktzeit>0", []);

////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//sektorkampf
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

include_once "kt_sectorkampf.php";

//flottenbewegung - sektorflotten
$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, aktion, zeit, aktzeit, zielsec FROM de_sector WHERE aktion > 0", []);

$num = mysqli_num_rows($res);

echo "<br>$num Sektor-Flotten-Datens�tze gefunden<br>";

while ($sector_row = mysqli_fetch_assoc($res))
{
  $sec_id     = $sector_row['sec_id'];
  $aktion  = $sector_row['aktion'];
  $zeit    = $sector_row['zeit'];
  $aktzeit = $sector_row['aktzeit'];

  //flottenbewegung
  if ($zeit==0 && $aktzeit>0) $aktzeit--; //wenn er nicht fliegt, dann verteidigt er gerade

  if ($zeit > 0) $zeit--; //er fliegt noch, also rz verk�rzen

  if ($zeit==0 && $aktzeit==0) //er ist mit allem fertig
  {
    if ($aktion==1 OR $aktion==2) //gek�mpft oder verteidigt-> heimflug
    {
      $zsec   = $sector_row['zielsec'];
      $rz=12;
      //entfernungzuschlag
      //if ($zsec<$sec_id+5 and $zsec>$sec_id-5) $rz=$rz+0;else $rz=$rz+2;
        
      mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET aktion = 3, zeit = ?, aktzeit = ?, zielsec = ? WHERE sec_id = ?", [$rz, $aktzeit, $sec_id, $sec_id]);
    }
    else //ist daheim angekommen->verteidige heimatsystem
    {
      mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET aktion = 0, zeit = 0, gesrzeit = 0, aktzeit = 0, zielsec = 0 WHERE sec_id = ?", [$sec_id]);
    }
  }
  else //er ist noch unterwegs, bzw. verteidigt-> update der zeiten
  {
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET zeit = ?, aktzeit = ? WHERE sec_id = ?", [$zeit, $aktzeit, $sec_id]);
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
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET lastmtick = ?", [$time]);
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
