<?php
$GLOBALS['deactivate_old_design']=true;

include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";

include 'lib/map_system_defs.inc.php';
include "lib/map_system.class.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$mysc2=$row["sc2"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$spec1=$row['spec1'];$spec3=$row['spec3'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<title>Vergessene Systeme</title>
<?php 
include "cssinclude.php";
?>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

if(isset($sv_deactivate_vsystems) && $sv_deactivate_vsystems==1){
	include "resline.php";
	echo '<br><div class="info_box text2">Auf diesem Server sind die Vergessenen Systeme deaktiviert.</div>';

	die('</body></html>');
}

include "resline.php";

//hat man die benötigte Technologie?
if(!hasTech($pt,25)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=25";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	echo '<br>';
	rahmen_oben('Fehlende Technologie');
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=28" target="_blank"><img src="gp/g/t/'.$_SESSION['ums_rasse'].'_25.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();  
}else{
	rahmen_oben('Vergessene Systeme');

	///////////////////////////////////////////////////////////////////////////
	// automatisches erkunden, nur anzeigen, wenn man noch nicht alles erkundet hat
	///////////////////////////////////////////////////////////////////////////
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT COUNT(*) AS anzahl FROM de_map_objects");
	$row = mysqli_fetch_array($db_daten);
	$anzahl_systeme=$row['anzahl'];
	
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT COUNT(*) AS anzahl FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."';");
	$row = mysqli_fetch_array($db_daten);
	$anzahl_systeme_entdeckt=$row['anzahl'];	

	if($anzahl_systeme_entdeckt<$anzahl_systeme && $anzahl_systeme_entdeckt>0){

		//status ändern
		if(isset($_REQUEST['set_auto_explore'])){
			$sql="UPDATE de_user_data SET vs_auto_explore=".intval($_REQUEST['set_auto_explore'])." WHERE user_id='".$_SESSION['ums_user_id']."';";
			//echo $sql;
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			$pd['vs_auto_explore']=intval($_REQUEST['set_auto_explore']);
		}

		if($pd['vs_auto_explore']==1){
			$btn_text='deaktivieren';
			$target_value=0;
		}else{
			$btn_text='aktivieren';
			$target_value=1;
		}

		echo '
		<div class="cell fett">
			<div style="display: flex; padding-bottom: 20px;">
				<div style="width: 160px;">Automatische Erkundung </div>
				<div style="flex-grow: 1; text-align: left; margin-top: -4px;"><a href="?set_auto_explore='.$target_value.'" class="btn">'.$btn_text.'</a></div>
		</div>';
	}

	///////////////////////////////////////////////////////////////////////////
	// Filter für die einzelnen Systeme
	///////////////////////////////////////////////////////////////////////////
	echo '
	<div class="" style="display: flex; border-top: 1px solid #999999; padding-top: 10px;">
		<div style="flex-grow: 1; padding-top: 6px;">Filterkriterien:</div>
		<div style="flex-grow: 1;">
		<select name="vsf0a" id="vsf0a" onChange="vs_filter(1);">';
	for($i=0; $i<count($GLOBALS['map_buildings']);$i++){
		if(!empty($GLOBALS['map_buildings'][$i]['bldg_filter_tag'])){
			echo '<option value="'.$GLOBALS['map_buildings'][$i]['bldg_filter_tag'].'">'.$GLOBALS['map_buildings'][$i]['name'].'</option>';
		}
	}
	echo '<option value="f_unsy">Unerforschte Systeme</option>';
	echo'
	  	</select>		
		
		  <select name="vsf0b" id="vsf0b" onChange="vs_filter(1);">
			<option value="gg">Stufe gr&ouml;&szlig;er gleich</option>
			<option value="kg">Stufe kleiner gleich</option>
			<option value="g">Stufe gleich</option>
		  </select>

		  <select name="vsf0c" id="vsf0c" onChange="vs_filter(1);">
			<option value="0">0</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
		  	<option value="8">8</option>
		  	<option value="9">9</option>
			<option value="10">10</option>
		  </select>		  

		  <img src="gp/g/close_icon.png" style="height: 26px; width: auto; margin-left: 30px; margin-bottom: -7px;" onclick="vs_filter(0);" title="Filter zur&uuml;cksetzen">
		  <script>
		  $(document).ready(function() {
			vs_filter_init();
		  });		  
		  </script>
		</div>
	</div>


	<div style="border-bottom: 1px solid #999999; margin-bottom: 20px; margin-top: 13px;"></div>';

	$col_stolen=getStolenColByUID($_SESSION['ums_user_id']);
	$prozentwert=$col_stolen*5;
	if($prozentwert>500){
		$prozentwert=500;
	}

	echo '<div>Rohstoffbonus durch eroberte Kollektoren (pro Kollektor 5%, max 500% insgesamt): '.number_format($prozentwert, 2, ',' ,'.').'%</div>';

	//Hekates Gunst und Pfad des Thanatos, nur anzeigen, wenn etwas wirkt
	$vs_boni=vs_bonus_info($_SESSION['ums_user_id']);
	$vs_boni_teile=array();
	foreach($vs_boni['hekate'] as $vs_typ => $vs_rest){
		if($vs_typ==\DieEwigen\DE2\Model\VsBonus\VsBonusService::TYP_INDUSTRIE){
			$vs_boni_teile[]='Hekates Gunst: Industrie +'.\DieEwigen\DE2\Model\VsBonus\VsBonusService::getProzent($vs_typ).'% (noch '.$vs_rest.' WT)';
		}else{
			$vs_boni_teile[]='Hekates Gunst: Bauzeit -'.\DieEwigen\DE2\Model\VsBonus\VsBonusService::getProzent($vs_typ).'% (noch '.$vs_rest.' WT)';
		}
	}
	if($vs_boni['thanatos']>0){
		$vs_boni_teile[]='Pfad des Thanatos Stufe '.$vs_boni['thanatos'].': Industrie +'.\DieEwigen\DE2\Model\Thanatos\ThanatosService::getIndustrieProzent($vs_boni['thanatos']).'%, Bauzeit -'.\DieEwigen\DE2\Model\Thanatos\ThanatosService::getBauzeitProzent($vs_boni['thanatos']).'%';
	}
	if(!empty($vs_boni_teile)){
		echo '<div style="margin-top: 5px;">'.implode('<br>', $vs_boni_teile).'</div>';
	}

	echo '
	<div style="border-bottom: 1px solid #999999; margin-bottom: 20px; margin-top: 20px;"></div>
	';
	

	
	echo '<table width="572" border="0" cellpadding="0" cellspacing="1">';
	echo '<tr class="cell"><td>System</td><td style="text-align: center;">Aktion</td></tr>';


	
	///////////////////////////////////////////////////////////////////////////
	// erforschbare/erforschte Systeme für Handel/Missionen/Events
	///////////////////////////////////////////////////////////////////////////

	$sichtbare_systeme=array();
	$immer_sichtbare_systeme=array();
	$erforschte_systeme=array();
	$erforschte_systeme_koordinaten=array();
	
	//Kanten laden
	$kanten=array();
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_kanten");
	while($row = mysqli_fetch_array($db_daten)){
		$kanten[]=array($row['knoten_id1'],$row['knoten_id2']);
	}
	
	//die erforschten Systeme laden
	$sql="SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>0 AND known_since<'".time()."';";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		//sie sind sichtbar und erforscht
		$sichtbare_systeme[]=$row['map_id'];
		$erforschte_systeme[]=$row['map_id'];
	}
	
	//die sichtbaren Systeme um Systeme ergänzen, die immer sichtbar sind
	$sql="SELECT id FROM de_map_objects WHERE always_visible=1 OR system_typ=4;";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		if(!in_array($row['id'],$sichtbare_systeme)){
			$sichtbare_systeme[]=$row['id'];
		}
		$immer_sichtbare_systeme[]=$row['id'];
	}

	//print_r($immer_sichtbare_systeme);
	
	//die sichtbaren Systeme um die Systeme ergänzen, die über Kanten mit erforschten Systemen verknüpft sind
	for($i=0;$i<count($erforschte_systeme);$i++){
		//für jedes System alle Kanten durchgehen
		$map_id=$erforschte_systeme[$i];
		//echo 'map_id: '.$map_id;
		for($k=0; $k<count($kanten);$k++){
			//echo ' kanten_ids: '.$kanten[$k][0].'/'.$kanten[$k][1];
			//knoten1 testen
			if($map_id==$kanten[$k][0]){
				//echo 'gefunden 1';
				if(!in_array($kanten[$k][1],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][1];
					//echo 'gefunden 1a';
				}
			}
	
			//knoten2 testen
			if($map_id==$kanten[$k][1]){
				//echo 'gefunden 2';
				if(!in_array($kanten[$k][0],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][0];
					//echo 'gefunden 2a';
				}
			}		
		}
	}
	
	//Kanten Koordinaten bestimmen, nur erforschte Systeme haben Kanten
	$kanten_koordinaten=array();
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	// allge Gebäude der Karte laden und in ein Array packen
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$bldg=array();
	$sql="SELECT * FROM de_user_map_bldg WHERE user_id='".$_SESSION['ums_user_id']."';";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		$bldg[$row['map_id']][$row['field_id']]['bldg_id']=$row['bldg_id'];
		$bldg[$row['map_id']][$row['field_id']]['bldg_level']=$row['bldg_level'];
		$bldg[$row['map_id']][$row['field_id']]['bldg_time']=$row['bldg_time'];
	}

	//print_r($bldg);
	
	//Systeme laden
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects");
	while($row = mysqli_fetch_array($db_daten)){
		if(!in_array($row['id'], $sichtbare_systeme)){
			continue;
		}

		//klasse restaurieren
		$data=unserialize($row['data']);
		$data->system_id=$row['id'];

		echo $data->showOverviewRows($bldg[$row['id']] ?? array(), in_array($row['id'], $erforschte_systeme), in_array($row['id'], $immer_sichtbare_systeme));
	}

	echo '</table>';
	rahmen_unten();  
	
}
?>

<br>
</body>
</html>