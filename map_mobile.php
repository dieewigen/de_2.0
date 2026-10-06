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
	rahmen_oben('Vergessene Systeme');
	echo '<div class="mod vs"><div class="mod-leer">Auf diesem Server sind die Vergessenen Systeme deaktiviert.</div></div>';
	rahmen_unten();

	die('</body></html>');
}

include "resline.php";

//hat man die benötigte Technologie?
if(!hasTech($pt,25)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=25";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);

	rahmen_oben('Fehlende Technologie');
	echo '<div class="mod vs"><div class="mod-hinweis vs-fehlt">F&uuml;r die Vergessenen Systeme ben&ouml;tigst Du folgende Technologie: <a href="help.php?t=25">'.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</a></div></div>';
	rahmen_unten();
}else{
	rahmen_oben('Vergessene Systeme');
	echo '<div class="mod vs">';

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

		echo '<div class="vs-zeile"><span>Automatische Erkundung '.($pd['vs_auto_explore']==1 ? '<span class="mod-chip mod-chip-gruen">aktiv</span>' : '<span class="mod-chip">aus</span>').'</span>';
		echo '<a href="?set_auto_explore='.$target_value.'" class="mod-btn mod-btn-leise ally-btn-klein">'.ucfirst($btn_text).'</a></div>';
	}

	///////////////////////////////////////////////////////////////////////////
	// Boni auf die Vergessenen Systeme
	///////////////////////////////////////////////////////////////////////////
	$col_stolen=getStolenColByUID($_SESSION['ums_user_id']);
	$prozentwert=$col_stolen*5;
	if($prozentwert>500){
		$prozentwert=500;
	}

	echo '<div class="vs-boni">';
	echo '<div class="ov-wert"><span class="mod-typ">Rohstoffbonus durch eroberte Kollektoren</span><b>'.number_format($prozentwert, 2, ',' ,'.').' %</b><small>5 % je Kollektor, h&ouml;chstens 500 %</small></div>';

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
		echo '<div class="ov-wert"><span class="mod-typ">Weitere Boni</span><span class="vs-boni-liste">'.implode('<br>', $vs_boni_teile).'</span></div>';
	}
	echo '</div>';

	///////////////////////////////////////////////////////////////////////////
	// Filter für die einzelnen Systeme (vs_filter in ang_fn.js, Auswahl bleibt per Cookie erhalten)
	///////////////////////////////////////////////////////////////////////////
	echo '<div class="vs-filter"><span class="mod-typ">Filter</span>';
	echo '<select name="vsf0a" id="vsf0a" onChange="vs_filter(1);" class="mod-eingabe">';
	for($i=0; $i<count($GLOBALS['map_buildings']);$i++){
		if(!empty($GLOBALS['map_buildings'][$i]['bldg_filter_tag'])){
			echo '<option value="'.$GLOBALS['map_buildings'][$i]['bldg_filter_tag'].'">'.$GLOBALS['map_buildings'][$i]['name'].'</option>';
		}
	}
	echo '<option value="f_unsy">Unerforschte Systeme</option>';
	echo '</select>';
	echo '<select name="vsf0b" id="vsf0b" onChange="vs_filter(1);" class="mod-eingabe">
			<option value="gg">Stufe gr&ouml;&szlig;er gleich</option>
			<option value="kg">Stufe kleiner gleich</option>
			<option value="g">Stufe gleich</option>
		</select>';
	echo '<select name="vsf0c" id="vsf0c" onChange="vs_filter(1);" class="mod-eingabe">';
	for($i=0; $i<=10; $i++){
		echo '<option value="'.$i.'">'.$i.'</option>';
	}
	echo '</select>';
	echo '<button type="button" class="mod-btn mod-btn-leise ally-btn-klein" onclick="vs_filter(0);">Zur&uuml;cksetzen</button>';
	echo '<script>
		$(document).ready(function() {
			vs_filter_init();
		});
		</script>';
	echo '</div>';

	echo '<table class="vs-tabelle">';

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

	//die sichtbaren Systeme um die Systeme ergänzen, die über Kanten mit erforschten Systemen verknüpft sind
	for($i=0;$i<count($erforschte_systeme);$i++){
		//für jedes System alle Kanten durchgehen
		$map_id=$erforschte_systeme[$i];
		for($k=0; $k<count($kanten);$k++){
			//knoten1 testen
			if($map_id==$kanten[$k][0]){
				if(!in_array($kanten[$k][1],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][1];
				}
			}

			//knoten2 testen
			if($map_id==$kanten[$k][1]){
				if(!in_array($kanten[$k][0],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][0];
				}
			}
		}
	}

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
	echo '</div>';
	rahmen_unten();

}
?>
</body>
</html>
