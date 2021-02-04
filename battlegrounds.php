<?php
include "inc/header.inc.php";
include "lib/transactioni.lib.php";
include "functions.php";
include "lib/map_system.class.php";
include "lib/bg_defs.inc.php";
include 'lib/special_ship.class.php';

$pt=loadPlayerTechs($_SESSION['ums_user_id']);

$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps']=$ps;

$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];$spec4=$row['spec4'];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'], "SELECT kt FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$max_kt = $row["kt"];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Battlegrounds</title>
<meta charset="utf-8"/>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
//$newcss=1;
include "cssinclude.php";
?>
<link rel="stylesheet" href="g/style.css?<?php echo filemtime('g/style.css');?>">
</head>
<body>
<?php 

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
			<td valign="top">Du ben&ouml;tigst folgende Technologie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
		</tr>
	</table>';
	$content.=rahmen_unten(false); 
}else{

	$content.=rahmen_oben('BATTLEGROUNDS',false);

	$content.='
	<table width="572" border="0" cellpadding="0" cellspacing="0">
		<tr align="left" class="cell">
			<td valign="top">';

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

		$content.='
		<div class="tech" style="width: 100%; cursor: default;">
			<div class="tech_bg0"></div>
			<div class="tech_name">
				<div class="uppercase">'.$system_data->getSystemName().'</div>
				<div style="margin-top: 8px; font-size: 12px;">'.$sv_bg[$system_subtyp]['subname'].'</div>';

		
		if($has_access>0){
			$content.='<span style="margin-top: 8px; font-size: 12px;">Du hast einen Weltraumhafen in dem System und nimmst an den K&auml;mpfen teil.</span>';

			for($s=$max_kt;$s<$max_kt+$sv_bg[$system_subtyp]['start_interval'];$s++){
				if(($s+1) % $sv_bg[$system_subtyp]['start_interval'] == 0){
					$startet_in=$s-$max_kt+1;
					$content.='<div style="margin-top: 8px; font-size: 12px;">N&auml;chster Start in '.$startet_in.' Kampfticks.</div>';
					break;
				}
			}


			/*
			//versucht man sich anzumelden
			if($_REQUEST['register']==$system_subtyp){
				$sql="INSERT INTO `de_user_bg_register` SET user_id='".$_SESSION['ums_user_id']."', bg_id='".$system_subtyp."';";
				//echo $sql;
				mysqli_query($GLOBALS['dbi'],$sql);
			}

			//hat sich bereits zum BG angemeldet?
			$sql="SELECT * FROM `de_user_bg_register` WHERE user_id='".$_SESSION['ums_user_id']."' AND bg_id='".$system_subtyp."';";
			//echo $sql;
			$result=mysqli_query($GLOBALS['dbi'],$sql);
			$has_reservation = mysqli_num_rows($result);

			if($has_reservation>0){
				$content.='<div style="margin-top: 8px; font-size: 12px;">Du hast Dich angemeldet.</div>';
				for($s=$max_kt;$s<$max_kt+$sv_bg[$system_subtyp]['start_interval'];$s++){
					if(($s+1) % $sv_bg[$system_subtyp]['start_interval'] == 0){
						$startet_in=$s-$max_kt+1;
						$content.='<div style="margin-top: 8px; font-size: 12px;">N&auml;chster Start in '.$startet_in.' Kampfticks.</div>';
						break;
					}
				}

				
			}else{
				$content.='<div style="margin-top: 8px; font-size: 12px;">Du hast Dich noch nicht angemeldet.</div>';
				//Anmelde-Link
				if($system_subtyp==0){
					$content.='<a href="?register='.$system_subtyp.'" class="btn">anmelden</a>';
				}

			}
			*/


		}else{
			$content.='<span style="margin-top: 8px; font-size: 12px; background-color: #AA0000; padding: 0 3px 0 3px;">Du hast keinen Weltraumhafen in dem System.</span>';
		}

		$content.='
			</div>
		</div>';

	}


	//Anzahl der entdeckten Battlegrounds aus der DB holen

	$content.='
			</td>
		</tr>
	</table>';


	$content.=rahmen_unten(false);

}

////////////////////////////////////////
//Ranglisten anzeigen
////////////////////////////////////////

//die einzelnen BGs durchgehen
for($i=0;$i<3;$i++){
	$content.=rahmen_oben('BATTLEGROUND '.($i+1).' GEWINNER',false);

	$play_typ=0; //Spieler-BG

	if($i==2){
		$play_typ=1; //Ally-BG
	}

	if($play_typ==0){
		//Spieler-BG
		$content.='
		<table width="572" border="0" cellpadding="0" cellspacing="0">
			<tr align="left" class="cell fett" style="text-align: center;">
				<td>Platz</td>
				<td>Spieler</td>
				<td>BASISSTERN-Stufe</td>
				<td>Siege</td>
			</tr>';

		//Spielerdaten laden
		$sql="SELECT * FROM `de_user_data` WHERE bgscore".$i." > 1 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_query($GLOBALS['dbi'],$sql);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			//Schiffsdaten laden
			$ship=loadSpecialShip($row['user_id']);

			$content.='
			<tr align="left" class="cell" style="text-align: center;">
				<td>'.$platz.'</td>
				<td>'.$row['spielername'].'</td>
				<td>'.$ship->ship_level.'</td>
				<td>'.$row['bgscore'.$i].'</td>
			</tr>';

			$platz++;
		}

		$content.='</table>';
	}else{
		//Allianz-BG
		$content.='
		<table width="572" border="0" cellpadding="0" cellspacing="0">
			<tr align="left" class="cell fett" style="text-align: center;">
				<td>Platz</td>
				<td>Allianz</td>
				<td>Siege</td>
			</tr>';

		//Spielerdaten laden
		$sql="SELECT * FROM de_allys WHERE bgscore".$i." > 0 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_query($GLOBALS['dbi'],$sql);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			$content.='
			<tr align="left" class="cell" style="text-align: center;">
				<td>'.$platz.'</td>
				<td>'.$row['allytag'].'</td>
				<td>'.$row['bgscore'.$i].'</td>
			</tr>';

			$platz++;
		}

		$content.='</table>';
	}

	$content.=rahmen_unten(false);
	$content.='<br><br>';
}

//Resline ausgeben
include "resline.php";

echo $content;


?>

<br>
<?php include "fooban.php"; ?>
</body>
</html>
