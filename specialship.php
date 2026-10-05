<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "lib/map_system.class.php";
include "lib/bg_defs.inc.php";
include 'lib/special_ship.class.php';
include "functions.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);

$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps']=$ps;

$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$spec4=$row['spec4'];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'], "SELECT kt FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$max_kt = $row["kt"];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Basisstern</title>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
include "cssinclude.php";
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$content='';

//hat man die benötigte Technologie?
if(!hasTech($pt,159)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=159";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	$content.='<br>';
	$content.=rahmen_oben('Fehlende Technologie',false);
	$content.='
	<table width="572" border="0" cellpadding="0" cellspacing="0">
		<tr align="left" class="cell">
			<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
		</tr>
	</table>';
	$content.=rahmen_unten(false); 
}else{

	$ship=loadSpecialShip($_SESSION['ums_user_id']);

	$ship_upgrade_cost=($ship->ship_level+1)*100;

	if(isset($_REQUEST['upgrade_ship']) && $_REQUEST['upgrade_ship']==1){
		if($ps[1]['item_amount']>=$ship_upgrade_cost){
			$ship->ship_level++;
			saveSpecialShip($_SESSION['ums_user_id'], $ship);

			//Rohstoffe abziehen
			change_storage_amount($_SESSION['ums_user_id'], 1, $ship_upgrade_cost*-1, false);

			//Daten updaten
			$ps=loadPlayerStorage($_SESSION['ums_user_id']);
			$GLOBALS['ps']=$ps;
			

			//neue Upgradekosten
			$ship_upgrade_cost=($ship->ship_level+1)*100;
		}
	}

	$content.=rahmen_oben('BASISSTERN',false);

	//Werte des Basissterns als Kacheln
	$content.='
	<div class="einh mod">
		<div class="einh-kacheln">
			<div class="ov-wert"><span class="mod-typ">Stufe</span><b>'.number_format($ship->ship_level, 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">H&uuml;llenstruktur</span><b>'.number_format($ship->get_hp_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Schutzschildenergie</span><b>'.number_format($ship->get_shield_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Waffenschaden</span><b>'.number_format($ship->get_wp_min(), 0, ',' ,'.').' &ndash; '.number_format($ship->get_wp_max(), 0, ',' ,'.').'</b></div>
		</div>';

	//Upgrade: Kosten in Palenium, Knopf nur wenn bezahlbar
	$palenium_str='<span class="mod-chip"><img src="gp/g/item1.png" alt="">Palenium <b>'.number_format($ps[1]['item_amount'], 0, ',' ,'.').'</b></span>';
	if($ps[1]['item_amount']>=$ship_upgrade_cost){
		$upgrade='<a href="?upgrade_ship=1" class="mod-btn">Auf Stufe '.number_format(($ship->ship_level+1), 0, ',' ,'.').' upgraden</a>';
	}else{
		$upgrade='<span class="mod-feld mod-feld-grund">Zu wenig Palenium</span>';
	}
	$content.='
		<div class="einh-upgrade">
			<div class="einh-upgrade-text">Upgrade auf Stufe '.number_format(($ship->ship_level+1), 0, ',' ,'.').': <b>'.number_format($ship_upgrade_cost, 0, ',' ,'.').' Palenium</b> '.$palenium_str.'</div>
			'.$upgrade.'
		</div>
	</div>';


	$content.=rahmen_unten(false);

}

//hat man die benötigte Technologie?
if(!hasTech($pt,159)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=159";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	$content.='<br>';
	$content.=rahmen_oben('Fehlende Technologie',false);
	$content.='
	<table width="572" border="0" cellpadding="0" cellspacing="0">
		<tr align="left" class="cell">
			<td valign="top">Du ben&ouml;tigst folgende Technologie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
		</tr>
	</table>';
	$content.=rahmen_unten(false); 
}else{

	$content.=rahmen_oben('BATTLEGROUNDS',false);

	$content.='<div class="einh mod">';

	//Battlegrounds aus der DB holen und darstellen
	$sql="SELECT * FROM `de_map_objects` WHERE system_typ=4 ORDER BY system_subtyp ASC;";
	$db_data=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_data)){
		$system_data=unserialize($row['data']);
		$system_subtyp=$row['system_subtyp'];
		$system_id=$row['id'];

		//Test auf Weltraumhafen
		$sql="SELECT * FROM `de_user_map_bldg` WHERE user_id='".$_SESSION['ums_user_id']."' AND map_id='".$system_id."' AND bldg_id='0';";
		$result=mysqli_query($GLOBALS['dbi'],$sql);
		$has_access = mysqli_num_rows($result);

		$status='';
		if($has_access>0){
			$status.='<span class="mod-chip mod-chip-gruen">Weltraumhafen vorhanden, du nimmst an den K&auml;mpfen teil</span>';

			for($s=$max_kt;$s<$max_kt+$sv_bg[$system_subtyp]['start_interval'];$s++){
				if(($s+1) % $sv_bg[$system_subtyp]['start_interval'] == 0){
					$startet_in=$s-$max_kt+1;
					$status.='<span class="mod-chip">N&auml;chster Start in <b>'.$startet_in.'</b> KT</span>';
					break;
				}
			}
		}else{
			$status.='<span class="mod-chip einh-chip-rot">Kein Weltraumhafen in dem System</span>';
		}

		$content.='
		<div class="einh-bg">
			<div class="einh-bg-name">'.$system_data->getSystemName().'</div>
			<div class="mod-typ">'.$sv_bg[$system_subtyp]['subname'].'</div>
			<div class="einh-bg-status">'.$status.'</div>
		</div>';
	}

	$content.='</div>';

	$content.=rahmen_unten(false);

}

////////////////////////////////////////
//Ranglisten anzeigen
////////////////////////////////////////

//die einzelnen BGs durchgehen
for($i=0;$i<3;$i++){
	$content.=rahmen_oben('BATTLEGROUND '.($i+1).' GEWINNER',false);
	$content.='<div class="einh mod">';

	$play_typ=0; //Spieler-BG

	if($i==2){
		$play_typ=1; //Ally-BG
	}

	$zeilen='';
	if($play_typ==0){
		//Spieler-BG
		$kopf='<div class="einh-rang prod-kopf"><span class="prod-zahl">Platz</span><span>Spieler</span><span class="prod-zahl">BASISSTERN-Stufe</span><span class="prod-zahl">Siege</span></div>';

		//Spielerdaten laden
		$sql="SELECT * FROM `de_user_data` WHERE bgscore".$i." > 1 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_query($GLOBALS['dbi'],$sql);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			//Schiffsdaten laden
			$ship=loadSpecialShip($row['user_id']);

			$zeilen.='<div class="einh-rang einh-platz'.min($platz, 4).'"><span class="prod-zahl">'.$platz.'</span><span class="einh-rang-name">'.$row['spielername'].'</span><span class="prod-zahl">'.$ship->ship_level.'</span><span class="prod-zahl"><b>'.$row['bgscore'.$i].'</b></span></div>';

			$platz++;
		}
	}else{
		//Allianz-BG
		$kopf='<div class="einh-rang einh-rang-ally prod-kopf"><span class="prod-zahl">Platz</span><span>Allianz</span><span class="prod-zahl">Siege</span></div>';

		//Allianzdaten laden
		$sql="SELECT * FROM de_allys WHERE bgscore".$i." > 0 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_query($GLOBALS['dbi'],$sql);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			$zeilen.='<div class="einh-rang einh-rang-ally einh-platz'.min($platz, 4).'"><span class="prod-zahl">'.$platz.'</span><span class="einh-rang-name">'.$row['allytag'].'</span><span class="prod-zahl"><b>'.$row['bgscore'.$i].'</b></span></div>';

			$platz++;
		}
	}

	if($zeilen==''){
		$content.='<div class="mod-leer">Noch keine Gewinner.</div>';
	}else{
		$content.=$kopf.'<div class="prod-liste">'.$zeilen.'</div>';
	}

	$content.='</div>';
	$content.=rahmen_unten(false);
}

include "resline.php";

echo einheiten_navi('specialship');

echo $content;


?>

<br>

</body>
</html>
