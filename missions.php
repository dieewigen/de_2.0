<?php

use DieEwigen\DE2\View\RealTime;

include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include "inc/sabotage.inc.php";
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
//auf den erlaubten Bereich begrenzen (ältere, manipulierte Werte in der DB)
$sektorsteuersatz = max(0, min(5, intval($row['ssteuer'])));


//ally_id holen, wird für Allianzmissionen benötigt
$allyid=get_player_allyid($_SESSION['ums_user_id']);


//Frachtkapazität in Frachter umrechnen
function fk2frachter($fk, $ship_fk){
	//$sv_schiffsdaten[3][8]= array(4,0,0,95);//frachter	
	//fk2frachter($fk, $sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3])

	$frachter=ceil($fk/$ship_fk);

	return $frachter;
}

//Posten einer Belohnung oder Kosten mit Bild und Menge; beim Rohstoff-Handel anteilig zur Frachtkapazität
function mission_posten($liste, $prozent=100){
	global $ua_name;
	$resnamen=array('Multiplex','Dyharra','Iradium','Eternium','Tronic');
	$faktor=min($prozent, 100)/100;
	$html='';
	foreach($liste as $posten){
		switch($posten[0]){
			case 'A':
				if($posten[1]=='?'){
					$html.='<span class="mis-posten"><span class="mis-bild mis-bild-artefakt">?</span><span class="mis-name">Zuf&auml;lliges Artefakt</span></span>';
				}else{
					$html.='<span class="mis-posten"><img src="gp/g/arte'.$posten[1].'.gif" class="mis-bild mis-bild-artefakt" alt=""><span class="mis-name">'.$ua_name[$posten[1]-1].'</span></span>';
				}
			break;

			case 'R':
				$html.='<span class="mis-posten"><img src="gp/g/icon'.$posten[1].'.png" class="mis-bild" alt=""><span><b>'.number_format(floor($posten[2]*$faktor), 0,"",".").'</b> '.$resnamen[$posten[1]-1].'</span></span>';
			break;

			case 'I':
				//nicht jede Ware hat ein Bild, dann den Anfangsbuchstaben zeigen
				$name=$GLOBALS['ps'][$posten[1]]['item_name'];
				if(file_exists('gp/g/item'.$posten[1].'.png')){
					$bild='<img src="gp/g/item'.$posten[1].'.png" class="mis-bild" alt="">';
				}else{
					$bild='<span class="mis-bild mis-bild-ware">'.mb_substr($name, 0, 1).'</span>';
				}
				$html.='<span class="mis-posten">'.$bild.'<span><b>'.number_format(floor($posten[2]*$faktor), 0,"",".").'</b> '.$name.'</span></span>';
			break;
		}
	}
	return $html;
}

//Zeit im selben Format wie der Countdown in ang_fn.js (Tage und Stunden nur, wenn nötig), damit nichts springt
function mission_uhr($sekunden){
	$sekunden=max(0, (int)ceil($sekunden));
	$tage=floor($sekunden/86400);
	$stunden=floor($sekunden/3600)%24;
	return ($tage>0 ? $tage.':' : '').($tage>0 || $stunden>0 ? sprintf('%02d:', $stunden) : '').sprintf('%02d:%02d', floor($sekunden/60)%60, $sekunden%60);
}

//dasselbe als reiner Text für Tooltips
function mission_text($liste){
	$resnamen=array('Multiplex','Dyharra','Iradium','Eternium','Tronic');
	$teile=array();
	foreach($liste as $posten){
		if($posten[0]=='R'){
			$teile[]=number_format($posten[2], 0,"",".").' '.$resnamen[$posten[1]-1];
		}elseif($posten[0]=='I'){
			$teile[]=number_format($posten[2], 0,"",".").' '.$GLOBALS['ps'][$posten[1]]['item_name'];
		}
	}
	return implode(', ', $teile);
}

////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT id, level FROM de_user_artefact WHERE id=11 AND user_id='".$_SESSION['ums_user_id']."';");
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

//Handel - ARES - man zahlt Palenium und erhält Tronic
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

//Geheimdienst BASRANUR - Resonanzkristalle für das Siegel von Basranur (Spezialsystem 5), muss am Ende stehen, da die Missions-ID die Position ist
$md_index++;
$md[$md_index]['typ']=0;
$md[$md_index]['subtyp']=3;//Basranur
$md[$md_index]['reward'][0]=array('I', \DieEwigen\DE2\Model\Siegel\SiegelService::ITEM_ID, 1);
$md[$md_index]['time']=\DieEwigen\DE2\Model\Siegel\SiegelService::getMissionZeit()*$duration_factor;
$need_agents=round($agent_avg*0.25);
if($need_agents<500){
	$need_agents=500;
}
$md[$md_index]['need_agents']=$need_agents;
$md[$md_index]['special_system_phase_need']=array(\DieEwigen\DE2\Model\Siegel\SiegelService::SPECIAL_SYSTEM_ID,1);

//Handel - HADES - man zahlt Verteidigungsanlagen BNG 9000 und erhält Sektorkollektoren

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Missionen</title>

<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
include "cssinclude.php";
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

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
			<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=29" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_29.jpg" border="0"></a></td>
			<td valign="top">Du ben&ouml;tigst folgende Technologie: <b>'.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</b><br><br>'.link_technologien('Zu den Technologien').'</td>
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

	//Anzeige-Daten der Missionen, die Karten entstehen nach der Schleife
	$anzeige=array();

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

					case 3: //Siegel von Basranur
						$missionstyp.=' (BASRANUR)';
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

						//Handelsmissionen nur mit einer Flotte, die zu Hause ist, und nur mit echter Ware:
						//sonst gäbe es Handelspunkte ohne Kosten und eine angreifende/verteidigende Flotte würde umgesetzt
						$mission_cost_ok=true;
						foreach($md[$m]['cost'] ?? array() as $mission_cost){
							if($mission_cost[2]<=0){
								$mission_cost_ok=false;
							}
						}
						if(($md[$m]['typ']==1 || $md[$m]['typ']==2) && ($fleet_data[$fleet_id]['aktion']!=0 || $storage_capacity<=0 || !$mission_cost_ok)){
							$storage_is_ok=false;
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

							//den Missionsdatensatz zuerst auf erledigt stellen, damit parallele Anfragen die Belohnung nicht doppelt erhalten
							$mission_claimed=false;
							if($free_artefact_places>=$need_artefact_places){
								mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_mission SET get_reward=1, counter=counter+1 WHERE mission_id=? AND user_id=? AND get_reward=0", [$m, $_SESSION['ums_user_id']]);
								$mission_claimed=mysqli_affected_rows($GLOBALS['dbi'])==1;
							}

							if($mission_claimed){
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

							}elseif($free_artefact_places<$need_artefact_places){
								$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Im Artefaktgeb&auml;ude ist kein freier Platz.</div>';
							}else{
								//eine parallele Anfrage hat die Belohnung bereits abgeholt
								$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Mission wurde bereits beendet.</div>';
							}
						}else{
							$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Mission wurde bereits beendet.</div>';	
						}


					}else{
						$err_msg='<div style="color: #FF0000; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-align: center;">Die Missionszeit ist noch nicht abgelaufen.</div>';	
					}
				}


				///////////////////////////////////////////////////////////
				//für die Anzeige merken; die Karten entstehen nach der Schleife,
				//wenn alle Aktionen verarbeitet sind und Flotten/Agenten aktuell sind
				///////////////////////////////////////////////////////////
				$meldung='';
				if(!empty($err_msg)){
					$meldung.='<div class="mod-meldung mod-meldung-fehler">'.trim(strip_tags($err_msg)).'</div>';
				}
				if(!empty($success_msg)){
					$meldung.='<div class="mod-meldung mod-meldung-ok">'.preg_replace('/^(\s*<br>)+/i', '', trim(strip_tags($success_msg, '<br>'))).'</div>';
				}
				$anzeige[]=array('m' => $m, 'storage_capacity' => $storage_capacity, 'meldung' => $meldung);
			}
		}
	}
	
	///////////////////////////////////////////////////////////
	//Missionen anzeigen
	///////////////////////////////////////////////////////////

	//Flotten und Agenten erst jetzt laden, eine eben gestartete Mission hat sie verändert
	$flotten_daten=getFleetData($_SESSION['ums_user_id']);
	$flotten_fk=getFleetFK($_SESSION['ums_user_id']);
	$row=mysqli_fetch_assoc(mysqli_execute_query($GLOBALS['dbi'], "SELECT agent FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]));
	$agenten=$row['agent'];
	$frachter_fk=$sv_schiffsdaten[$_SESSION['ums_rasse']-1][8][3];
	$flottennamen=array('Heimatflotte','Flotte I','Flotte II','Flotte III');
	$laufend=array();

	//vorgewählte Flotte für den Handel: die mit der größten Frachtkapazität im Heimatsystem
	$flotte_wahl=1;
	$beste_fk=-1;
	for($f=1;$f<=3;$f++){
		if($flotten_daten[$f]['aktion']==0 && $flotten_fk[$f]>$beste_fk){
			$beste_fk=$flotten_fk[$f];
			$flotte_wahl=$f;
		}
	}

	//Statuszeile: Agenten, kürzere Missionen durch Spezialisierung/Artefakte, Sabotage
	$leiste='<span class="mod-chip">Agenten verf&uuml;gbar <b>'.number_format($agenten, 0,"",".").'</b></span>';
	$dauer_bonus=($spec4==2 ? 10 : 0)+$artbonus_duration;
	if($dauer_bonus>0){
		$bonus_title='K&uuml;rzere Missionen&';
		if($spec4==2){
			$bonus_title.='Spezialisierung: 10 %<br>';
		}
		if($artbonus_duration>0){
			$bonus_title.=$ua_name[10].'-Artefakte: '.str_replace('.', ',', round($artbonus_duration, 2)).' %';
		}
		$leiste.='<span class="mod-chip mod-chip-gruen" rel="tooltip" title="'.$bonus_title.'">Missionsdauer &minus;'.str_replace('.', ',', round($dauer_bonus, 2)).' %</span>';
	}
	if($mission_sabotage){
		$leiste.='<span class="mod-chip mod-chip-warn">Sabotiert: Missionsdauer +'.round($sv_sabotage[10][2]*100).' %</span>';
	}

	//Flottenwahl für Handelsmissionen
	$flottenwahl='';
	for($f=1;$f<=3;$f++){
		$daheim=($flotten_daten[$f]['aktion']==0);
		$frachter=fk2frachter($flotten_fk[$f], $frachter_fk);
		if(!$daheim){
			$status='unterwegs';
			if($flotten_daten[$f]['aktion']==4 && $flotten_daten[$f]['mission_time']>time()){
				//Missionen laufen in Echtzeit: Ende als Uhrzeit
				$status.=' &middot; '.RealTime::until($flotten_daten[$f]['mission_time']);
			}
		}elseif($frachter==0){
			$status='keine Frachter';
		}else{
			$status='bereit';
		}
		$flottenwahl.='<button type="button" class="mis-flotte'.($daheim && $frachter>0 ? '' : ' mis-flotte-aus').'" data-flotte="'.$f.'"><b>'.$flottennamen[$f].'</b><span>'.number_format($frachter, 0,"",".").' Frachter &middot; '.$status.'</span></button>';
	}

	//Karten
	$karten='';
	$typnamen=array('Agenteneinsatz','Rohstoff-Handel','Waren-Handel');
	$subtypnamen=array(1 => 'ARES', 2 => 'HEPHAISTOS', 3 => 'BASRANUR');
	foreach($anzeige as $a){
		$m=$a['m'];
		$typ=$md[$m]['typ'];
		$storage_capacity=$a['storage_capacity'];
		$dauer=$md[$m]['time']*$GLOBALS['tech_build_time_faktor'];
		$frei=(!isset($um[$m]) || ($um[$m]['end_time']<time() && $um[$m]['get_reward']==1));
		$rest=$frei ? 0 : $um[$m]['end_time']-time();

		$label=$typnamen[$typ];
		if(!empty($md[$m]['subtyp']) && isset($subtypnamen[$md[$m]['subtyp']])){
			$label.=' &middot; '.$subtypnamen[$md[$m]['subtyp']];
		}

		//Filter nach Rohstoffen beim Rohstoff-Handel
		$filter='';
		if($typ==1){
			$filter=' data-offer="'.$md[$m]['cost'][0][1].'" data-need="'.$md[$m]['reward'][0][1].'"';
		}

		if($frei){
			$zustand='';
			$dauer_text='Dauer '.mission_uhr($dauer);
		}elseif($rest>0){
			$zustand=' mis-karte-laeuft';
			$dauer_text='l&auml;uft';
		}else{
			$zustand=' mis-karte-fertig';
			$dauer_text='beendet';
		}

		$inhalt='';
		if($frei && $typ==0){
			//Agenteneinsatz
			$genug=$agenten>=$md[$m]['need_agents'];
			$inhalt.='<div class="mis-zeile"><div class="mis-tausch">'.mission_posten($md[$m]['reward']).'</div><div class="mis-aktion">'
				.($genug ? '<a href="?start_mission='.$m.'" class="mod-btn">Starten</a>' : '<span class="mod-feld mod-feld-grund">Zu wenig Agenten</span>')
				.'</div></div>';
			$inhalt.='<div class="mis-fuss"><span'.($genug ? '' : ' class="mis-rot"').'>Ben&ouml;tigt <b>'.number_format($md[$m]['need_agents'], 0,"",".").'</b> Agenten</span></div>';

		}elseif($frei){
			//Handel: je Flotte eine Variante, angezeigt wird die gewählte Flotte
			$maximum_title='Maximum&'.mission_text($md[$m]['cost']).' &rarr; '.mission_text($md[$m]['reward']).'<br>mit '.number_format(fk2frachter($storage_capacity, $frachter_fk), 0,"",".").' Frachtern';
			for($f=1;$f<=3;$f++){
				$fk=$flotten_fk[$f];
				$daheim=($flotten_daten[$f]['aktion']==0);
				$anteil=($storage_capacity>0) ? $fk*100/$storage_capacity : 0;

				if($typ==1){
					//Rohstoff-Handel: Kosten und Belohnung anteilig zur Frachtkapazität
					$prozent=min(100, $anteil);
					$ok=$daheim && $fk>0;
					$grund=$daheim ? 'Keine Frachter' : 'Flotte unterwegs';
					$frachter_text=number_format($prozent, 2,",",".").' % des Maximums &middot; '.number_format(fk2frachter($fk, $frachter_fk), 0,"",".").' von '.number_format(fk2frachter($storage_capacity, $frachter_fk), 0,"",".").' Frachtern';
				}else{
					//Waren-Handel: nur mit voller Frachtkapazität und vorhandener Ware
					$prozent=100;
					$genug_frachter=$anteil>=100;
					$ok=$daheim && $genug_frachter && hasMissionNeeds($md[$m]['cost']);
					if(!$daheim){
						$grund='Flotte unterwegs';
					}elseif(!$genug_frachter){
						$grund='Zu wenig Frachter';
					}else{
						$grund='Zu wenig Waren';
					}
					$frachter_text='<span'.($genug_frachter ? '' : ' class="mis-rot"').'>'.number_format(fk2frachter($fk, $frachter_fk), 0,"",".").' von '.number_format(fk2frachter($storage_capacity, $frachter_fk), 0,"",".").' Frachtern</span>';
				}
				$handelspunkte=$dauer*$prozent/100;

				$inhalt.='<div class="mis-variante" data-flotte="'.$f.'">
					<div class="mis-zeile">
						<div class="mis-tausch">'.mission_posten($md[$m]['cost'], $prozent).'<span class="mis-pfeil">&rarr;</span>'.mission_posten($md[$m]['reward'], $prozent).'</div>
						<div class="mis-aktion">'.($ok ? '<a href="?start_mission='.$m.'&fleet_id='.$f.'" class="mod-btn">Starten</a>' : '<span class="mod-feld mod-feld-grund">'.$grund.'</span>').'</div>
					</div>
					<div class="mis-fuss">
						<div class="mod-balken mis-anteil"><span style="width: '.round(min(100, $anteil)).'%;"></span></div>
						<span rel="tooltip" title="'.$maximum_title.'">'.$frachter_text.'</span>
						<span class="mod-chip">+'.number_format($handelspunkte, 0,"",".").' Handelspunkte</span>
					</div>
				</div>';
			}

		}else{
			//läuft oder wartet auf das Abholen der Belohnung
			$belohnung_liste=(isset($um[$m]['reward']) && is_array($um[$m]['reward'])) ? $um[$m]['reward'] : $md[$m]['reward'];
			$belohnung_prozent=isset($um[$m]['reward_percentage']) ? (float)$um[$m]['reward_percentage'] : 100;

			if($rest>0){
				//Ende als Uhrzeit; der Countdown läuft unsichtbar weiter und zeigt am Ende den Abholknopf
				$aktion='<span class="mod-feld" id="mis-laeuft'.$m.'">'.RealTime::until($um[$m]['end_time']).'<span id="mission_counter'.$m.'" hidden></span></span>'
					.'<a href="?end_mission='.$m.'" class="mod-btn" id="mis-abholen'.$m.'" hidden>Abholen</a>';
				$laufend[]=array('mission_counter'.$m, $rest, $m);
			}else{
				$aktion='<a href="?end_mission='.$m.'" class="mod-btn">Abholen</a>';
			}

			$fortschritt=($dauer>0) ? max(0, min(100, ($dauer-$rest)*100/$dauer)) : 100;
			$inhalt.='<div class="mis-zeile"><div class="mis-tausch"><span class="mis-label">Belohnung</span>'.mission_posten($belohnung_liste, $belohnung_prozent).'</div><div class="mis-aktion">'.$aktion.'</div></div>';
			if($typ==0){
				$inhalt.='<div class="mis-fuss"><span>Unterwegs: <b>'.number_format($um[$m]['need_agents'], 0,"",".").'</b> Agenten</span></div>';
			}
			$inhalt.='<div class="mod-balken mis-fortschritt"><span data-rest="'.max(0, $rest).'" style="width: '.round($fortschritt, 1).'%;"></span></div>';
		}

		$karten.='
		<div class="mis-karte'.$zustand.'" data-typ="'.$typ.'"'.$filter.'>
			<div class="mis-kopf"><span class="mod-typ">'.$label.'</span><span class="mis-dauer">'.$dauer_text.'</span></div>
			'.$inhalt.'
			'.$a['meldung'].'
		</div>';
	}

	$content.=rahmen_oben('Missionen',false);
	$content.='<div class="mis mod" data-typ="-1" data-flotte="'.$flotte_wahl.'">';
	$content.='<div class="mod-hinweis">Missionen k&ouml;nnen nicht abgebrochen werden. Eingesetzte Agenten sind w&auml;hrend der Mission nicht verf&uuml;gbar, nach dem Ende der Mission bekommst du sie zur&uuml;ck. Eine Flotte auf Handelsmission ist bis zu deren Ende unterwegs.</div>';
	$content.='<div class="mis-leiste">'.$leiste.'</div>';
	$content.='
	<div class="mis-tabs">
		<button type="button" class="mis-tab" data-typ="-1">Alle</button>
		<button type="button" class="mis-tab" data-typ="0">Agenten</button>
		<button type="button" class="mis-tab" data-typ="1">Rohstoff-Handel</button>
		<button type="button" class="mis-tab" data-typ="2">Waren-Handel</button>
	</div>
	<div class="mis-filter" hidden>
		Ich ben&ouml;tige
		<select id="res_need">
			<option value="-1" selected>alles</option>
			<option value="1">Multiplex</option>
			<option value="2">Dyharra</option>
			<option value="3">Iradium</option>
			<option value="4">Eternium</option>
		</select>
		und biete
		<select id="res_offer">
			<option value="-1" selected>alles</option>
			<option value="1">Multiplex</option>
			<option value="2">Dyharra</option>
			<option value="3">Iradium</option>
			<option value="4">Eternium</option>
		</select>
	</div>
	<div class="mis-flottenwahl"><span class="mis-flottenwahl-label">Handeln mit</span>'.$flottenwahl.'</div>';
	$content.='<div class="mis-liste">'.$karten.'</div>';
	$content.='</div>';
	$content.=rahmen_unten(false);

	//Filter, Flottenwahl, Countdowns und Fortschrittsbalken
	$content.='<script>
	(function(){
		var box=document.querySelector(".mis");
		var karten=[].slice.call(box.querySelectorAll(".mis-karte"));
		var filterbox=box.querySelector(".mis-filter");
		var need=document.getElementById("res_need"), offer=document.getElementById("res_offer");

		function merken(name, wert){
			try{ localStorage.setItem(name, wert); }catch(e){}
		}
		function laden(name){
			try{ return localStorage.getItem(name); }catch(e){ return null; }
		}

		function filtern(){
			var typ=box.getAttribute("data-typ");
			karten.forEach(function(k){
				var sicht=(typ=="-1" || k.getAttribute("data-typ")==typ);
				if(sicht && typ=="1"){
					if(need.value!="-1" && k.getAttribute("data-need")!=need.value){ sicht=false; }
					if(offer.value!="-1" && k.getAttribute("data-offer")!=offer.value){ sicht=false; }
				}
				k.hidden=!sicht;
			});
			filterbox.hidden=(typ!="1");
		}

		[].forEach.call(box.querySelectorAll(".mis-tab"), function(t){
			t.addEventListener("click", function(){
				box.setAttribute("data-typ", t.getAttribute("data-typ"));
				merken("mis_typ", t.getAttribute("data-typ"));
				filtern();
			});
		});
		need.addEventListener("change", filtern);
		offer.addEventListener("change", filtern);

		[].forEach.call(box.querySelectorAll(".mis-flotte"), function(b){
			b.addEventListener("click", function(){
				box.setAttribute("data-flotte", b.getAttribute("data-flotte"));
				merken("mis_flotte", b.getAttribute("data-flotte"));
			});
		});

		//zuletzt gewählter Reiter und eine bereite Flotte bleiben erhalten
		var typ=laden("mis_typ");
		if(typ!==null && box.querySelector(".mis-tab[data-typ=\""+typ+"\"]")){
			box.setAttribute("data-typ", typ);
		}
		var flotte=laden("mis_flotte");
		var knopf=flotte ? box.querySelector(".mis-flotte[data-flotte=\""+flotte+"\"]") : null;
		if(knopf && !knopf.classList.contains("mis-flotte-aus")){
			box.setAttribute("data-flotte", flotte);
		}
		filtern();

		//Countdowns; ist eine Mission fertig, erscheint der Abholknopf
		'.json_encode($laufend).'.forEach(function(l){
			ang_countdown(l[1], l[0], 0, function(){
				if(l[2]<0){ return; }
				var feld=document.getElementById("mis-laeuft"+l[2]), knopf=document.getElementById("mis-abholen"+l[2]);
				if(feld){ feld.hidden=true; }
				if(knopf){ knopf.hidden=false; }
			});
		});

		//Fortschrittsbalken laufen bis zum Ende der Mission voll
		[].forEach.call(box.querySelectorAll(".mis-fortschritt span[data-rest]"), function(s){
			var rest=parseFloat(s.getAttribute("data-rest"));
			if(rest>0){
				s.getBoundingClientRect();
				s.style.transition="width "+rest+"s linear";
				s.style.width="100%";
			}
		});
	})();
	</script>';
}

include "resline.php";

echo $content;


?>

<br>

</body>
</html>
