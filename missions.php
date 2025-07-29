<?php
include "inc/header.inc.php";
include "lib/transactioni.lib.php";
include "functions.php";
include("inc/sabotage.inc.php");
include 'inc/userartefact.inc.php';
include "tickler/kt_einheitendaten.php";
include "inc/schiffsdaten.inc.php";

//Check ob die Missionen zu Ende sind
checkMissionEnd();

$pt=loadPlayerTechs($_SESSION['ums_user_id']);

$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps']=$ps;

$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$spec4=$row['spec4'];$mysc4=$row["sc4"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;


//sektorsteuersatz auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT ssteuer FROM de_sector WHERE sec_id=?", [$sector]);
$row = mysqli_fetch_array($db_daten);
$sektorsteuersatz = $row['ssteuer'];


//ally_id holen, wird für Allianzmissionen benötigt
$allyid=get_player_allyid($_SESSION['ums_user_id']);


//Frachtkapazität in Frachter umrechnen
function fk2frachter($fk, $ship_fk){
	//$sv_schiffsdaten[3][8]= array(4,0,0,95);//frachter	
	//fk2frachter($fk, $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3])

	$frachter=ceil($fk/$ship_fk);

	return $frachter;
}

////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT id, level FROM de_user_artefact WHERE id=11 AND user_id='$ums_user_id'");
$artbonus_duration=0;
while($row = mysqli_fetch_array($db_daten)){
	$artbonus_duration=$artbonus_duration+$ua_werte[$row["id"]-1][$row["level"]-1][0];
}

if($artbonus_duration>50){
	$artbonus_duration=50;
}

///////////////////////////////////////////////////////////////////////
// Median der Agenten auf dem Server berechnen
///////////////////////////////////////////////////////////////////////
/*
unset($agent_list);
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT agent FROM de_user_data WHERE sector>1 AND npc=0");
//alle daten in ein array packen
while($row = mysqli_fetch_array($db_daten)){
	$agent_list[]=$row['agent'];
}
$agent_median=median($agent_list);
*/
///////////////////////////////////////////////////////////////////////
// wird die Spezialisierung für die Missionsdauer verwendet?
///////////////////////////////////////////////////////////////////////
if($spec4==2){
	$duration_factor=0.9-($artbonus_duration/100);
}else{
	$duration_factor=1-($artbonus_duration/100);
}

//echo 'A: '.$duration_factor.'/'.$artbonus_duration;

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//feststellen ob der handel sabotiert ist
$mission_sabotage = false;
if($maxtick<$mysc4+$sv_sabotage[10][0] AND $mysc4>$sv_sabotage[10][0]){
	$mission_sabotage = true;
}

if($mission_sabotage){
	$duration_factor+=$sv_sabotage[10][2];
}

///////////////////////////////////////////////////////////////////////
// Durchschnittswert der Agenten auf dem Server berechnen
///////////////////////////////////////////////////////////////////////
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT AVG(agent) AS agent_avg FROM de_user_data WHERE sector>1 AND npc=0 LIMIT 1");
$row = mysqli_fetch_array($db_daten);
$agent_avg=$row['agent_avg'];

//Agentengrundwert
$rohstoffwert=($pd['restyp01']+$pd['restyp02']*2+$pd['restyp03']*3+$pd['restyp04']*4+$pd['restyp05']*1000)/10/250000*5;
$max_agent_avg=round($pd['ehscore']*5+($rohstoffwert));

if($agent_avg>$max_agent_avg){
	$agent_avg=$max_agent_avg;
}
//echo 'A: '.$max_agent_avg;

///////////////////////////////////////////////////////////////////////
// Missions-Definitionen
///////////////////////////////////////////////////////////////////////
// Typ
// 0 Agenteneinsatz
// 1 Flotteneinsatz
// Reward
// wie definiert

///////////////////////////////////////////////////////////////////////
// Standardmissionen generieren
///////////////////////////////////////////////////////////////////////

//Geheimdienst zufälligesArtefakt
$md_index=0;
$md[$md_index]['typ']=0;
$md[$md_index]['reward'][0]=array('A', '?');
$md[$md_index]['time']=27000*$duration_factor;
$need_agents=round($agent_avg*0.75);
if($need_agents<500){
	$need_agents=500;
}
$md[$md_index]['need_agents']=$need_agents;

//Geheimdienst Tronic
$md_index++;
$md[$md_index]['typ']=0;
$md[$md_index]['reward'][0]=array('R', 5, 10);
$md[$md_index]['time']=54000*$duration_factor;
$need_agents=round($agent_avg*0.25);
if($need_agents<500){
	$need_agents=500;
}
$md[$md_index]['need_agents']=$need_agents;

//Geheimdienst Titanen-Energiekern
$md_index++;
$md[$md_index]['typ']=0;
$md[$md_index]['reward'][0]=array('I', 2, 2);
$md[$md_index]['time']=81000*$duration_factor;
$need_agents=round($agent_avg*0.75);
if($need_agents<500){
	$need_agents=500;
}
$md[$md_index]['need_agents']=$need_agents;

//Handel - man zahlt Multiplex und erhält Dyharra
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp01/10);
$res_erhalten=round($restyp01/10/2+($restyp01/10/2*0.01));
$md[$md_index]['cost'][0]=array('R', 1, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 2, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Multiplex und erhält Iradium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp01/10);
$res_erhalten=round($restyp01/10/3+($restyp01/10/3*0.01));
$md[$md_index]['cost'][0]=array('R', 1, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 3, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Multiplex und erhält Eternium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp01/10);
$res_erhalten=round($restyp01/10/4+($restyp01/10/4*0.01));
$md[$md_index]['cost'][0]=array('R', 1, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 4, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Dyharra und erhält Multiplex
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp02/10);
$res_erhalten=round($restyp02/10*2/1+($restyp02/10*2/1*0.01));
$md[$md_index]['cost'][0]=array('R', 2, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 1, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Dyharra und erhält Iradium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp02/10);
$res_erhalten=round($restyp02/10*2/3+($restyp02/10*2/3*0.01));
$md[$md_index]['cost'][0]=array('R', 2, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 3, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Dyharra und erhält Eternium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp02/10);
$res_erhalten=round($restyp02/10*2/4+($restyp02/10*2/4*0.01));
$md[$md_index]['cost'][0]=array('R', 2, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 4, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Iradium und erhält Multiplex
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp03/10);
$res_erhalten=round($restyp03/10*3/1+($restyp03/10*3/1*0.01));
$md[$md_index]['cost'][0]=array('R', 3, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 1, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Iradium und erhält Dyharra
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp03/10);
$res_erhalten=round($restyp03/10*3/2+($restyp03/10*3/2*0.01));
$md[$md_index]['cost'][0]=array('R', 3, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 2, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Iradium und erhält Eternium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp03/10);
$res_erhalten=round($restyp03/10*3/4+($restyp03/10*3/4*0.01));
$md[$md_index]['cost'][0]=array('R', 3, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 4, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Eternium und erhält Multiplex
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp04/10);
$res_erhalten=round($restyp04/10*4/1+($restyp04/10*4/1*0.01));
$md[$md_index]['cost'][0]=array('R', 4, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 1, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Eternium und erhält Dyharra
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp04/10);
$res_erhalten=round($restyp04/10*4/2+($restyp04/10*4/2*0.01));
$md[$md_index]['cost'][0]=array('R', 4, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 2, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;

//Handel - man zahlt Eternium und erhält Iradium
$md_index++;
$md[$md_index]['typ']=1;
$res_bezahlen=round($restyp04/10);
$res_erhalten=round($restyp04/10*4/3+($restyp04/10*4/3*0.01));
$md[$md_index]['cost'][0]=array('R', 4, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 3, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;


//Handel - man zahlt Handelswaren I und erhält Drasogi Kristall
$md_index++;
$md[$md_index]['typ']=2;
$res_bezahlen=100;
$res_erhalten=1;
$md[$md_index]['cost'][0]=array('I', 14, $res_bezahlen);
$md[$md_index]['reward'][0]=array('I', 19, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;
$md[$md_index]['storage_capacity']=$res_bezahlen*10;

//Handel - 	 man zahlt Palenium und erhält Tronic
$md_index++;
$md[$md_index]['typ']=2;
$md[$md_index]['subtyp']=1;//Ares
$res_bezahlen=20;
$res_erhalten=1;
$md[$md_index]['cost'][0]=array('I', 1, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 5, $res_erhalten);
$md[$md_index]['time']=81000*$duration_factor;
$md[$md_index]['need_agents']=0;
$md[$md_index]['storage_capacity']=$res_bezahlen*10;
$md[$md_index]['special_system_phase_need']=array(3,1);
$md[$md_index]['ally_mission_counter_id']=1;

//Handel - HEPHAISTOS man zahlt Handelswaren II und erhält Tronic
$md_index++;
$md[$md_index]['typ']=2;
$md[$md_index]['subtyp']=2;//Hephaistos
$res_bezahlen=50;
$res_erhalten=1000;
$md[$md_index]['cost'][0]=array('I', 15, $res_bezahlen);
$md[$md_index]['reward'][0]=array('R', 1, $res_erhalten);
$md[$md_index]['time']=27000*$duration_factor;
$md[$md_index]['need_agents']=0;
$md[$md_index]['storage_capacity']=$res_bezahlen*10;
$md[$md_index]['special_system_phase_need']=array(4,1);
$md[$md_index]['ally_mission_counter_id']=2;

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Missionen</title>

<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
//$newcss=1;
include "cssinclude.php";
?>
</head>
<body>
<?php

if(isset($sv_deactivate_missions) && $sv_deactivate_missions==1){
	include "resline.php";
	echo '<br><div class="info_box text2">Auf diesem Server sind Missionen deaktiviert.</div>';

	die('</body></html>');
}

$content='';

//hat man die benötigte Technologie?
if(!hasTech($pt,29)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=29";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	$content.='<br>';
	$content.=rahmen_oben('Fehlende Technologie',false);
	$content.='
	<table width="572" border="0" cellpadding="0" cellspacing="0">
		<tr align="left" class="cell">
			<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=29" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_29.jpg" border="0"></a></td>
			<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
		</tr>
	</table>';
	$content.=rahmen_unten(false);
}else{

	///////////////////////////////////////////////////////////
	//die Missionsdatensätze auslesen
	///////////////////////////////////////////////////////////
	$um=array();
	$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_mission WHERE user_id=".intval($_SESSION['ums_user_id']).";");
	//alle daten in ein array packen
	while($row = mysqli_fetch_array($db_daten)){
		$um[$row['mission_id']]=$row;
		//reward direkt deserialisizeren
		$um[$row['mission_id']]['reward']=unserialize($um[$row['mission_id']]['reward']);
	}

	//Missionen anzeigen
	$content.=rahmen_oben('Missionen <img style="margin-bottom: 2px;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="ACHTUNG: Missionen k&ouml;nnen nicht abgebrochen werden.<br><br>Eingesetzte Agenten sind während der Mission nicht verfügbar. Nach der Beendiung der Mission werden diese jedoch wieder zurückerstattet.">',false);
	$content.='<div style="width: 572px;">';

	///////////////////////////////////////////////////////////
	//die statischen/wiederholbaren Missionen durchgehen
	///////////////////////////////////////////////////////////
	$err_msg='';
	for($m=0;$m<count($md);$m++){
		$vorbedingungen_erfuellt=true;

		//Überprüfen ob alle Bedingungen für die Mission erfüllt sind
		//getUserSpecialsystemDataByMapID
		if(isset($md[$m]['special_system_phase_need'])){
			$map_id=getMapIDBySpecialsystemID($md[$m]['special_system_phase_need'][0]);
			$special_data=getUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $map_id);
			if(isset($special_data['phase']) && $special_data['phase']>=$md[$m]['special_system_phase_need'][1]){
				//Vorbedingung erfüllt
			}else{
				//Vorbedingung nicht erfüllt, also Mission nicht anzeigen
				$vorbedingungen_erfuellt=false;
			}
		}
		

		if($vorbedingungen_erfuellt){
			//Missionstyp
			switch($md[$m]['typ']){
				case 0: //Agenten
					$missionstyp='Agenteneinsatz';
					$storage_capacity=0;
				break;

				case 1: //R-Handel
					$missionstyp='Rohstoff-Handel';
					$storage_capacity=calcMissionStorageCapacity($md[$m]['reward'], $md[$m]['cost']);
				break;

				case 2: //W-Handel
					$missionstyp='Waren-Handel';
					$storage_capacity=$md[$m]['storage_capacity'];
				break;
			}

			//Missions-Subtyp
			if(isset($md[$m]['subtyp'])){
				switch($md[$m]['subtyp']){
					case 0: //kein spezielle Subtyp

					break;

					case 1: //ARES
						$missionstyp.=' (ARES)';
					break;

					case 2: //HEPHAISTOS
						$missionstyp.=' (HEPHAISTOS)';
					break;
		
				}
			}

			if($md[$m]['typ']==0 || $md[$m]['typ']==1 || $md[$m]['typ']==2){
				$err_msg='';
				$success_msg='';
				////////////////////////////////////////////
				//Test ob man die Mission starten möchte
				////////////////////////////////////////////
				if(isset($_REQUEST['start_mission']) && $_REQUEST['start_mission']==$m){
					//hat man genug Agenten?
					if($pd['agent']>=$md[$m]['need_agents']){
						
						//benötigten Frachtraum berechnen
						$has_storage=0;
						$fleet_id=intval($_REQUEST['fleet_id'] ?? -1);
						if($fleet_id<1 || $fleet_id>3){
							$fleet_id=1;
						}
						
						if($md[$m]['typ']==1 || $md[$m]['typ']==2){
							//Flotten-Daten laden
							$fleet_data=getFleetData($_SESSION['ums_user_id']);

							//Flotten-Frachtkapazität laden
							$fleet_fk=getFleetFK($_SESSION['ums_user_id']);
							
							if($fleet_data[$fleet_id]['aktion']==0){
								//Flottenfrachtkapazität
								$has_storage=$fleet_fk[$fleet_id];
								
							}
						}

						//Frachtkapazität überprüfen, wenn man welche braucht
						$storage_is_ok=true;
						$reward_percentage=100;
						if($storage_capacity>0){
							//Typ 1
							if($md[$m]['typ']==1){
								//Belohnung in Prozent berechnen
								$reward_percentage=$has_storage * 100 / $storage_capacity;
								if($reward_percentage>100){
									$reward_percentage=100;
								}

								//Hat man Frachtkapazität
								if($has_storage==0){
									$storage_is_ok=false;
								}

							}if($md[$m]['typ']==2){//Typ 2
								//hat man genug Schiffe
								$reward_percentage=$has_storage * 100 / $storage_capacity;
								//echo 'A: '.$reward_percentage.'/'.$has_storage.'/'.$storage_capacity;
								if($reward_percentage>=100){
									$reward_percentage=100;

									//checken ob man über alle benötigte Waren verfügt
									if(!hasMissionNeeds($md[$m]['cost'])){
										$storage_is_ok=false;
										//echo 'hna';
									}
								}else{
									$storage_is_ok=false;
								}
							}

						}

						//hat man genug Frachtraum?
						if($storage_is_ok){

							//läuft die Mission bereits?
							if( (isset($um[$m]['end_time']) && $um[$m]['end_time']<time()) && ( (isset($um[$m]['get_reward']) && $um[$m]['get_reward']==1) || (isset($um[$m]['get_reward']) && $um[$m]['get_reward']=='')) || !isset($um[$m]) ){


								//Agenten abziehen
								$sql="UPDATE de_user_data SET agent=agent-".$md[$m]['need_agents']." WHERE user_id=".$_SESSION['ums_user_id'].";";
								write2agentlog($_SESSION['ums_user_id'], 'mission-need', $md[$m]['need_agents']);
								mysqli_query($GLOBALS['dbi'],$sql);

								//Kosten abziehen
								if(isset($md[$m]['cost']) && count($md[$m]['cost'])>0){
									substractMissionCost($md[$m]['cost'], $reward_percentage);
								}

								//infocenter zum schnelleren Reload vormerken
								$_SESSION['ic_last_refresh']=0;

								//Mission-Datensatz generieren
								$end_time=round(time()+$md[$m]['time']*$GLOBALS['tech_build_time_faktor']);
								$sql="
								INSERT INTO de_user_mission (
									user_id,
									mission_id,
									reward,
									reward_percentage,
									need_agents,
									end_time,
									get_reward
								)VALUES(
									'".$_SESSION['ums_user_id']."',
									'".$m."',
									'".serialize($md[$m]['reward'])."',
									'".$reward_percentage."',
									'".$md[$m]['need_agents']."',
									'".$end_time."',
									'0'
								) ON DUPLICATE KEY UPDATE 
									reward='".serialize($md[$m]['reward'])."',
									reward_percentage='".$reward_percentage."',
									end_time='".$end_time."',
									need_agents='".$md[$m]['need_agents']."',
									get_reward='0'
								";

								//Agentenzahl für die Anzeige aktualisieren
								$um[$m]['need_agents']=$md[$m]['need_agents'];
							
								//echo $sql;
								mysqli_query($GLOBALS['dbi'],$sql);

								$um[$m]['end_time']=$end_time;
								$um[$m]['get_reward']=0;

								//bei einer Handelsmission Flotte updaten und Handelspunkte gutschrieben
								if($md[$m]['typ']==1 || $md[$m]['typ']==2){
									//Handelspunkte
									$tradescore=$md[$m]['time']*$GLOBALS['tech_build_time_faktor']*$reward_percentage/100;
									$sql="UPDATE de_user_data SET tradesystemscore=tradesystemscore+'".$tradescore."', tradesystemtrades=tradesystemtrades+1 WHERE user_id='".$_SESSION['ums_user_id']."'";
									mysqli_query($GLOBALS['dbi'], $sql);

									//Flotte
									unset($mission_data);
									$mission_data['action_typ']=0;
									startFleetMission($_SESSION['ums_user_id'].'-'.$fleet_id, $end_time, $mission_data);
								}

								//Allianz: Aufgabe/Mission updaten
								if($allyid>0){

									//wenn die Missionsaufgabe aktiv ist, den Wert um eins erhöhen
									mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET questreach = questreach + 1 WHERE id='$allyid' AND questtyp=4");

									//Allianzmission aktualisieren
									if(isset($md[$m]['ally_mission_counter_id'])){
										$mcid=intval($md[$m]['ally_mission_counter_id']);
										mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET mission_counter_$mcid = mission_counter_$mcid + 1 WHERE id='$allyid'");
									}
								}

							}else{
								$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Mission l&auml;uft bereits.</div>';	
							}
						}else{
							$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Flotte ist nicht bereit.</div>';	
						}
					}else{
						$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Es stehen nicht genug Agenten zur Verf&uuml;gung.</div>';
					}
				}

				////////////////////////////////////////////
				//Test ob man die Mission beenden möchte
				////////////////////////////////////////////
				if(isset($_REQUEST['end_mission']) && $_REQUEST['end_mission']==$m){
					//zeit abgelaufen?
					if($um[$m]['end_time']<time()){
						//Belohnung schon bekommen
						if($um[$m]['get_reward']==0){
							//genug Platz im Artefaktgebäude?
							$need_artefact_places=0;
							for($b=0;$b<count($md[$m]['reward']);$b++){
								switch($md[$m]['reward'][$b][0]){
									//Artefakt
									case 'A':
										$need_artefact_places++;
									break;
								}
							}

							$free_artefact_places=get_free_artefact_places($_SESSION['ums_user_id']);
							if($free_artefact_places<=0){
								$free_artefact_places=0;
							}

							if($free_artefact_places>=$need_artefact_places){
								//Agenten wieder gutschreiben
								$sql="UPDATE de_user_data SET agent=agent+".$um[$m]['need_agents']." WHERE user_id=".$_SESSION['ums_user_id'].";";
								write2agentlog($_SESSION['ums_user_id'], 'mission-getback', $md[$m]['need_agents']);
								mysqli_query($GLOBALS['dbi'],$sql);

								//bekommt man nur einen prozentuellen Wert?
								$reward_percentage=$um[$m]['reward_percentage'];
								$reward_percentage=$reward_percentage/100;

								///////////////////////////////
								//Belohnung
								///////////////////////////////

								$success_msg='<div style="color: #00FF00; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">';	
								for($b=0;$b<count($um[$m]['reward']);$b++){

									switch($um[$m]['reward'][$b][0]){
										//Artefakt
										case 'A':
											//Zufallsartefakt hinterlegen
											$artid=mt_rand(1, count($ua_name));
											$sql="INSERT INTO de_user_artefact (user_id, id, level) VALUES ('".$_SESSION['ums_user_id']."', '$artid', '1')";
											mysqli_query($GLOBALS['dbi'],$sql);

											//Erfolgs-Nachricht
											$success_msg.='<br>Erhaltenes Artefakt: '.$ua_name[$artid-1];

										break;

										//Standardrohstoffe
										case 'R':
											$restyp=$um[$m]['reward'][$b][1];
											
											$amount=ceil($um[$m]['reward'][$b][2]*$reward_percentage);

											/////////////////////////
											//Steuer berechnen
											/////////////////////////
											//zu versteuernde Rohstoffe 1%
											$tax_amount=$amount-$amount*100/101;
											//steuern
											$tax_amount=round($tax_amount/100*$sektorsteuersatz);
											//Steuer an die Sektorkasse abführen
											$sql="UPDATE de_sector SET restyp0".$restyp."=restyp0".$restyp."+'".$tax_amount."' WHERE sec_id='$sector'";
											mysqli_query($GLOBALS['dbi'],$sql);

											//Rohstoffe und Spende hinterlegen
											$sql="UPDATE de_user_data SET restyp0".$restyp."=restyp0".$restyp."+'".($amount-$tax_amount)."', spend0".$restyp."=spend0".$restyp."+'".$tax_amount."' WHERE user_id='".$_SESSION['ums_user_id']."';";
											mysqli_query($GLOBALS['dbi'],$sql);
											
											$resnamen=array('Multiplex','Dyharra','Iradium','Eternium','Tronic');
											//Erfolgs-Nachricht
											$success_msg.='<br>Erhaltene Rohstoffe: '.number_format($amount, 0,"",".").'x '.$resnamen[$restyp-1];

											//bei Handelsaktionen Steuer abziehen und ausgeben
											if($md[$m]['typ']==1){
												$success_msg.=' (abzgl. einer Steuer von: '.number_format($tax_amount, 0,"",".").'x '.$resnamen[$restyp-1].')';
											}
											

										break;
										//Items
										case 'I':
											$item_id=$um[$m]['reward'][$b][1];
											$amount=ceil($um[$m]['reward'][$b][2]);

											//Item hinterlegen
											change_storage_amount($_SESSION['ums_user_id'], $item_id, $amount, false);
											
											//Erfolgs-Nachricht
											$success_msg.='<br>Erhaltene Belohnung: '.number_format($amount, 0,"",".").'x '.$GLOBALS['ps'][$item_id]['item_name'];

										break;

									}
								}
								$success_msg.='</div>';

								//den Missionsdatensatz auf erledigt stellen und den counter erhöhen
								$sql="UPDATE de_user_mission SET get_reward=1, counter=counter+1 WHERE mission_id=".$m." AND user_id=".$_SESSION['ums_user_id'].";";
								mysqli_query($GLOBALS['dbi'],$sql);
								
								$um[$m]['end_time']=0;
								$um[$m]['get_reward']=1;

								//die Spielerdaten neu für die Ressourcenleiste laden
								$pd=loadPlayerData($_SESSION['ums_user_id']);
								$row=$pd;
								$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
								$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
								$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
								$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
															
								//infocenter zum schnelleren Reload vormerken
								$_SESSION['ic_last_refresh']=0;

							}else{
								$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Im Artefaktgeb&auml;ude ist kein freier Platz.</div>';	
							}
						}else{
							$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Mission wurde bereits beendet.</div>';	
						}


					}else{
						$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Missionszeit ist noch nicht abgelaufen.</div>';	
					}
				}


				///////////////////////////////////////////////////////////
				//JS-Menü um die Missionstypen zu filtern
				///////////////////////////////////////////////////////////
				if($m==0){
					//$content.='<div style="color: red;" class="cell">ACHTUNG: Missionen k&ouml;nnen nicht abgebrochen werden.<br><br></div>';

					$content.='
					<div class="cell">
						<div style="display: flex; width: 572px; padding-bottom: 10px;">
							<div style="margin-right: 10px;"><a href="javascript: void(0)" onclick="show_mission(-1)" class="btn">alle</a></div>
							<div style="margin-right: 10px;"><a href="javascript: void(0)" onclick="show_mission(0)" class="btn">Agenten</a></div>
							<div style="margin-right: 10px;"><a href="javascript: void(0)" onclick="show_mission(1)" class="btn">R-Handel</a></div>
							<div style="margin-right: 10px;"><a href="javascript: void(0)" onclick="show_mission(2)" class="btn">W-Handel</a></div>
						</div>
						

						<span id="trade_menu" style="display: none">
							Ich ben&ouml;tige 
							<select name="res_need" id="res_need" onchange="javascript: show_mission_trade();">
								<option value="-1" selected>alles</option>
								<option value="1">Multiplex</option>
								<option value="2">Dyharra</option>
								<option value="3">Iradium</option>
								<option value="4">Eternium</option>
							</select>
						
							und biete 
						
							<select name="res_offer" id="res_offer" onchange="javascript: show_mission_trade();">
								<option value="-1" selected>alles</option>
								<option value="1">Multiplex</option>
								<option value="2">Dyharra</option>
								<option value="3">Iradium</option>
								<option value="4">Eternium</option>
							</select>.
				

						</span>
					</div>

					<script>
						function show_mission(typ){

							if(typ==-1){
								$(".mission_tag").css("display", "");
							}

							if(typ==0 || typ==1 || typ==2){
								$(".mission_tag").css("display", "none");
								$(".mission_typ"+typ).css("display", "");
							}

							if(typ==1){
								$("#trade_menu").css("display", "");
							}else{
								$("#trade_menu").css("display", "none");
							}
						}

						function show_mission_trade(){
							var need=$("#res_need").val();
							var offer=$("#res_offer").val();

							$(".mission_tag").css("display", "none");
							
							if(need==-1 && offer==-1){
								$("[class*=need_res][class*=offer_res]").css("display", "");
							}
						
							if(need==-1 && offer>0){
								$(".offer_res_"+offer+"[class*=need_res]").css("display", "");
							}

							if(need>0 && offer==-1){
								$(".need_res_"+need+"[class*=offer_res]").css("display", "");
							}						

							if(need>0 && offer>0){
								$(".need_res_"+need+".offer_res_"+offer).css("display", "");
							}						

						}

					</script>
					';
				}

				////////////////////////////////////////////////////////////
				// Missionsliste anzeigen
				////////////////////////////////////////////////////////////
				
				//class-tags für den Handel erstellen
				if($md[$m]['typ']==1){
					$trade_class='';
					
					for($r=0; $r<count($md[$m]['cost']); $r++){
						if($md[$m]['cost'][$r][0]=='R'){
							$trade_class.=' offer_res_'.$md[$m]['cost'][$r][1];
						}
					}

					for($r=0; $r<count($md[$m]['reward']); $r++){
						if($md[$m]['reward'][$r][0]=='R'){
							$trade_class.=' need_res_'.$md[$m]['reward'][$r][1];
						}
					}

				}else{
					$trade_class='';
				}

				$content.='<div class="cell mission_tag mission_typ'.$md[$m]['typ'].$trade_class.'" style="padding-bottom: 10px;">';

				$content.='<div style="height: 0; border: 1px solid #666666; width: 100%; margin-bottom: 10px;"></div>';
				
				$content.='Missionstyp: '.$missionstyp;
				//Mission läuft aktuell
				if(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1)){
					$content.=' (Dauer: <span id="mission_counter'.$m.'">'.formatTime($md[$m]['time']*$GLOBALS['tech_build_time_faktor']).'</span>)'; 
				}else{
					$content.=' (Dauer: <span id="mission_counter'.$m.'"></span>)'; 
				}
				$content.='<table style="width: 100%;">';

				

				$belohnung='';
				$kosten='';
				$voraussetzung='';

				//werden agenten benötigt?
				if($md[$m]['need_agents']>0){
					if(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1)){
						$voraussetzung.=number_format($md[$m]['need_agents'], 0,"",".");
					}else{
						//wenn die Mission läuft die Anzahl der Agenten anzeigen, die unterwegs sind
						$voraussetzung.=number_format($um[$m]['need_agents'], 0,"",".");
					}
				}

				//gibt es eine Belohnung
				if(count($md[$m]['reward'])>0){
					if(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1)){
						$belohnung.=generateMissionReward($md[$m]['reward']);
					}else{
						$belohnung.=generateMissionReward($um[$m]['reward'], $um[$m]['reward_percentage']);
						/*
						if($_SESSION['ums_user_id']==1){
							$belohnung.='<br>'.print_r($um[$m],true).'<br>A: '.$um[$m]['reward_percentage'].'<br>';
							//138.874
							//1.000.257.696
						}
						*/
					}
				}

				//gibt es Kosten?
				if(isset($md[$m]['cost']) && is_array($md[$m]['cost']) && count($md[$m]['cost'])>0){
					if(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1)){
						$kosten.=generateMissionReward($md[$m]['cost']);
					}else{
						//$kosten.='';
					}

					
				}

				//benötigte Transportkapazität
				if($storage_capacity>0){
					$voraussetzung.='Frachter: '.number_format(fk2frachter($storage_capacity, $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3]), 0,"",".");
				}

				if($md[$m]['typ']==1){
					$tradescore=$md[$m]['time']*$GLOBALS['tech_build_time_faktor'];
					//$belohnung_tradescore=number_format($tradescore, 0,"",".").' Handelspunkte';
					$belohnung_tradescore='<img src="g/icon11.png" style="height: 20px; width: auto; margin-bottom: -5px;" rel="tooltip" title="Handelspunkte"> <div style="display: inline-block; margin-bottom: 5px;">'.number_format($tradescore, 0,"",".").'</div><br>';
				}else{
					$belohnung_tradescore='';
				}

				//Ausgabe
				if($md[$m]['typ']==0){//Agentenmission
					$content.='<tr style="font-weight: bold;"><td style="width: 21%;"></td><td style="width: 39%;">Belohnung</td><td style="width: 40%;" colspan="2">Ben&ouml;tigte/Eingesetzte Agenten</td></tr>';
					$content.='<tr class="cell1"><td id="agent_mission_link'.$m.'" style="padding: 5px;"></td><td style="vertical-align: top; padding: 5px;">'.$belohnung.$belohnung_tradescore.'</td><td style="vertical-align: top; padding: 5px;" colspan="2">'.$voraussetzung.'</td></tr>';
				}elseif($md[$m]['typ']==1){ //R-Handelsmission
					//Mission läuft aktuell bzw. Belohnung wurde noch nicht abgeholt
					if(isset($um[$m]) && ($um[$m]['end_time']>time() || $um[$m]['get_reward']==0)){
						$content.='<tr style="font-weight: bold;"><td style="width: 19%; padding: 5px;"></td><td style="width: 27%; padding: 5px;">Belohnung</td><td style="width: 27%; padding: 5px;"></td><td style="width: 27%; padding: 5px;"></td></tr>';
						$content.='<tr class="cell1"><td id="agent_mission_link'.$m.'" style="padding: 5px;"></td><td style="vertical-align: top; padding: 5px;" colspan="3">'.$belohnung.$belohnung_tradescore.'</td></tr>';
					}else{
						$content.='<tr style="font-weight: bold;"><td style="width: 19%; padding: 5px;"></td><td style="width: 27%; padding: 5px;">Belohnung</td><td style="width: 27%; padding: 5px;">Kosten</td><td style="width: 27%; padding: 5px;">Voraussetzung</td></tr>';
						$content.='<tr><td style="vertical-align: top; padding: 5px;">Maximum</td><td style="vertical-align: top; padding: 5px;">'.$belohnung.$belohnung_tradescore.'</td><td style="vertical-align: top; padding: 5px;">'.$kosten.'</td><td style="vertical-align: top; padding: 5px;">'.$voraussetzung.'</td></tr>';
					}
				}elseif($md[$m]['typ']==2){ //W-Handelsmission
					//wenn die Mission läuft, die Belohnung anzeigen
					if(isset($um[$m]) && ($um[$m]['end_time']>time() || $um[$m]['get_reward']==0)){
						$content.='<tr style="font-weight: bold;"><td style="width: 19%; padding: 5px;"></td><td style="width: 27%; padding: 5px;">Belohnung</td><td style="width: 27%; padding: 5px;"></td><td style="width: 27%; padding: 5px;"></td></tr>';
						$content.='<tr class="cell1"><td id="agent_mission_link'.$m.'" style="padding: 5px;"></td><td style="vertical-align: top; padding: 5px;" colspan="3">'.$belohnung.$belohnung_tradescore.'</td></tr>';
					}else{
						$content.='<tr style="font-weight: bold;"><td style="width: 19%; padding: 5px;"></td><td style="width: 27%; padding: 5px;">Belohnung</td><td style="width: 27%; padding: 5px;">Kosten</td><td style="width: 27%; padding: 5px;">Voraussetzung</td></tr>';
					}
				}

				if(!empty($err_msg) || !empty($success_msg)){
					$content.=$err_msg;
					$content.=$success_msg;
				}

				//wenn die Mission zu Ende ist, oder noch nicht existiert, den START-Button anzeigen
				if(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1)){
					if($md[$m]['typ']==0){
						//$content.='<tr><td colspan="4"><a href="?start_mission='.$m.'" class="btn" style="margin-left: auto; margin-right: auto;">START</a></td></tr>';
						$content.='
						<script>
						$("#agent_mission_link'.$m.'").html(\'<a href="?start_mission='.$m.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">Mission starten</a>\');
						</script>
						';
						
					}elseif($md[$m]['typ']==1){ //R-Handelsmission

						//Handelspunkte
						if(!empty($belohnung)){
							$belohnung.='<br>';
						}
						$tradescore=
						$belohnung.=$tradescore.' Handelspunkte';


						//Flotten-Frachtkapazität laden
						$fleet_fk=getFleetFK($_SESSION['ums_user_id']);

						//////////////////////////////////////////////////
						//Flotte I-III
						//////////////////////////////////////////////////
						$fleet_names=array('Heimatflotte','Flotte I', 'Flotte II', 'Flotte III');
						//ist die Flotte bereit
						for($fleet_id=1;$fleet_id<=3;$fleet_id++){

							//Berechnung Frachtkapazität in Prozent
							/*
							if($storage_capacity<=$fleet_fk[$fleet_id]){
								$fleet_fk_bel=100;
							}else{
								$fleet_fk_percent=$storage_capacity*$fleet_fk[$fleet_id] / 100;
							}*/

							if($storage_capacity>0){
								$fleet_fk_percent=$fleet_fk[$fleet_id] * 100 / $storage_capacity;
							}else{
								$fleet_fk_percent=0;
							}

							$belohnung_fleet=generateMissionReward($md[$m]['reward'],$fleet_fk_percent);

							//Handelspunkte
							if($fleet_fk_percent>100){
								$p=100;
							}else{
								$p=$fleet_fk_percent;
							}

							$tradescore=$md[$m]['time']*$GLOBALS['tech_build_time_faktor']*$p/100;
							//$belohnung_tradescore=number_format($tradescore, 0,"",".").' Handelspunkte';					
							$belohnung_tradescore='<img src="g/icon11.png" style="height: 20px; width: auto; margin-bottom: -5px;" rel="tooltip" title="Handelspunkte"> <div style="display: inline-block; margin-bottom: 5px;">'.number_format($tradescore, 0,"",".").'</div><br>';


							$kosten_fleet=generateMissionReward($md[$m]['cost'],$fleet_fk_percent);

							//benötigte Transportkapazität
							//$voraussetzung_fleet='Frachtkapazit&auml;t: '.number_format($fleet_fk[$fleet_id], 0,"",".").' ('.number_format($fleet_fk_percent, 2,",",".").'%)';
							$voraussetzung_fleet='Frachter: '.number_format(fk2frachter($fleet_fk[$fleet_id], $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3]), 0,"",".").' ('.number_format($fleet_fk_percent, 2,",",".").'%)';


							if($fleet_fk[$fleet_id]>0){
								$content.='<tr class="cell1"><td style="padding: 5px;"><a href="?start_mission='.$m.'&fleet_id='.$fleet_id.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">'.$fleet_names[$fleet_id].' starten</a></td><td style="padding: 5px;">'.$belohnung_fleet.$belohnung_tradescore.'</td><td style="padding: 5px;">'.$kosten_fleet.'</td><td style="padding: 5px;">'.$voraussetzung_fleet.'</td></tr>';
							}else{
								$content.='<tr class="cell1"><td style="padding: 5px;">'.$fleet_names[$fleet_id].'</td><td colspan="3" style="padding: 5px; text-align: center; color: #FFFF00;">Die Flotte ist nicht bereit.</td></tr>';
							}
						}
						

						//////////////////////////////////////////////////

						//Welche Flotte entsehen?
						/*
						$content.='<tr><td colspan="4" style="text-align: center; font-weight: bold;">Welche Flotte m&ouml;chtest Du entsenden?</td></tr>';
						$content.='<tr>';
						$content.='<td></td>';
						$content.='<td><a href="?start_mission='.$m.'&fleet_id=1" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE I</a></td>';
						$content.='<td><a href="?start_mission='.$m.'&fleet_id=2" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE II</a></td>';
						$content.='<td><a href="?start_mission='.$m.'&fleet_id=3" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE III</a></td>';
						$content.='<tr>';
						*/
					}elseif($md[$m]['typ']==2){ //W-Handelsmission
						//Handelspunkte
						if(!empty($belohnung)){
							$belohnung.='<br>';
						}
						$tradescore=
						$belohnung.=$tradescore.' Handelspunkte';


						//Flotten-Frachtkapazität laden
						$fleet_fk=getFleetFK($_SESSION['ums_user_id']);

						//////////////////////////////////////////////////
						//Flotte I-III
						//////////////////////////////////////////////////
						$fleet_names=array('Heimatflotte','Flotte I', 'Flotte II', 'Flotte III');
						//ist die Flotte bereit
						for($fleet_id=1;$fleet_id<=3;$fleet_id++){

							if($storage_capacity>0){
								$fleet_fk_percent=$fleet_fk[$fleet_id] * 100 / $storage_capacity;
							}else{
								$fleet_fk_percent=0;
							}

							$belohnung_fleet=generateMissionReward($md[$m]['reward'],100);

							//Handelspunkte
							if($fleet_fk_percent>=100){
								$p=100;
								$fleet_ok=true;
							}else{
								$p=$fleet_fk_percent;
								$fleet_ok=false;
							}

							$tradescore=$md[$m]['time']*$GLOBALS['tech_build_time_faktor'];
							//$belohnung_tradescore=number_format($tradescore, 0,"",".").' Handelspunkte';					
							$belohnung_tradescore='<img src="g/icon11.png" style="height: 20px; width: auto; margin-bottom: -5px;" rel="tooltip" title="Handelspunkte"> <div style="display: inline-block; margin-bottom: 5px;">'.number_format($tradescore, 0,"",".").'</div><br>';


							$kosten_fleet=generateMissionReward($md[$m]['cost'],100);

							//benötigte Transportkapazität
							//$voraussetzung_fleet='Frachtkapazit&auml;t: '.number_format($fleet_fk[$fleet_id], 0,"",".").' ('.number_format($fleet_fk_percent, 2,",",".").'%)';
							$voraussetzung_fleet='Frachter: '.number_format(fk2frachter($fleet_fk[$fleet_id], $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3]), 0,"",".").' / '.number_format(fk2frachter($storage_capacity, $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3]), 0,",",".");

							if($fleet_fk[$fleet_id]>0){
								if($fleet_ok){
									$content.='<tr class="cell1"><td style="padding: 5px;"><a href="?start_mission='.$m.'&fleet_id='.$fleet_id.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">'.$fleet_names[$fleet_id].' starten</a></td><td style="padding: 5px;">'.$belohnung_fleet.$belohnung_tradescore.'</td><td style="padding: 5px;">'.$kosten_fleet.'</td><td style="padding: 5px;">'.$voraussetzung_fleet.'</td></tr>';
								}else{
									$content.='<tr class="cell1"><td style="padding: 5px;">'.$fleet_names[$fleet_id].'</td><td style="padding: 5px;">'.$belohnung_fleet.$belohnung_tradescore.'</td><td style="padding: 5px;">'.$kosten_fleet.'</td><td style="padding: 5px;"><span style="color: #FF0000;">'.$voraussetzung_fleet.'</span></td></tr>';
								}
							}else{
								$content.='<tr><td>'.$fleet_names[$fleet_id].'</td><td colspan="3" style="text-align: center; color: #FFFF00;">Die Flotte ist nicht bereit.</td></tr>';
							}
						}


						/*
							if($fleet_fk[$fleet_id]>0){
								$content.='<tr class="cell1"><td style="padding: 5px;"><a href="?start_mission='.$m.'&fleet_id='.$fleet_id.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">'.$fleet_names[$fleet_id].' starten</a></td><td style="padding: 5px;">'.$belohnung_fleet.$belohnung_tradescore.'</td><td style="padding: 5px;">'.$kosten_fleet.'</td><td style="padding: 5px;">'.$voraussetzung_fleet.'</td></tr>';
							}else{
								$content.='<tr class="cell1"><td style="padding: 5px;">'.$fleet_names[$fleet_id].'</td><td colspan="3" style="padding: 5px; text-align: center; color: #FFFF00;">Die Flotte ist nicht bereit.</td></tr>';
							}
						*/						

						//////////////////////////////////////////////////

						//Welche Flotte entsehen?
						/*
						$content.='<tr><td colspan="4" style="text-align: center; font-weight: bold;">Welche Flotte m&ouml;chtest Du entsenden?</td></tr>';
						$content.='<tr>';
						$content.='<td colspan="4"><div style="display: flex;">';
						$content.='<div style="flex-grow: 1;"><a href="?start_mission='.$m.'&fleet_id=1" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE I</a></div>';
						$content.='<div style="flex-grow: 1;"><a href="?start_mission='.$m.'&fleet_id=2" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE II</a></div>';
						$content.='<div style="flex-grow: 1;"><a href="?start_mission='.$m.'&fleet_id=3" class="btn" style="margin-left: auto; margin-right: auto;">FLOTTE III</a></div>';
						$content.='</div></td>';
						$content.='<tr>';
						*/

					}
				}else{
					//counter
					//$content.='<tr><td colspan="4"><div style="color: #00FF00;">Verbleibende Zeit: <span id="mission_counter'.$m.'"></span></div></td></tr>';
					$content.='
					<script>
					$("#mission_counter'.$m.'").css("color", "#00FF00");
					ang_countdown('.($um[$m]['end_time']-time()).',"mission_counter'.$m.'",0);
					$("#agent_mission_link'.$m.'").html(\'<a href="?end_mission='.$m.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">Mission beenden</a>\');
					</script>';

					/*
					if($md[$m]['typ']==0){
						$content.='
						<script>
						$("#agent_mission_link'.$m.'").html(\'<a href="?end_mission='.$m.'" class="btn2" style="display: inline-block; width: 96px; text-align: center;">Mission beenden</a>\');
						</script>
						';
					}else{
						//button
						$content.='<tr><td colspan="4"><a href="?end_mission='.$m.'" class="btn" style="margin-left: auto; margin-right: auto;">BEENDEN</a></td></tr>';
					}
					*/

				}

				$content.='</table>';
				
				$content.='</div>';
			}
		}
	}
	
	$content.='</div>';
	$content.=rahmen_unten(false);  
}

include "resline.php";

echo $content;


?>

<br>
<?php include "fooban.php"; ?>
</body>
</html>
