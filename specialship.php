<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "lib/map_system.class.php";
include "lib/bg_defs.inc.php";
include 'lib/special_ship.class.php';
include "functions.php";

use DieEwigen\DE2\Model\Battleground\Haltung;
use DieEwigen\DE2\Session\CsrfToken;

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

//Aktionen per POST mit Token, danach Umleitung (Post/Redirect/Get), damit Neuladen nichts wiederholt
if(hasTech($pt,159) && ($_SERVER['REQUEST_METHOD'] ?? '')==='POST'){
	$ss_ziel='specialship.php';
	if(!CsrfToken::check($_POST['token'] ?? '')){
		$ss_ziel.='?fehler=token';
	}elseif(isset($_POST['upgrade_ship'])){
		if(setLock($_SESSION['ums_user_id'])){
			$ship=loadSpecialShip($_SESSION['ums_user_id']);
			$ps=loadPlayerStorage($_SESSION['ums_user_id']);
			$ship_upgrade_cost=($ship->ship_level+1)*100;
			if($ps[1]['item_amount']>=$ship_upgrade_cost){
				$ship->ship_level++;
				saveSpecialShip($_SESSION['ums_user_id'], $ship);

				//Rohstoffe abziehen
				change_storage_amount($_SESSION['ums_user_id'], 1, $ship_upgrade_cost*-1, false);
				$ss_ziel.='?ok=upgrade';
			}else{
				$ss_ziel.='?fehler=palenium';
			}
			releaseLock($_SESSION['ums_user_id']);
		}else{
			$ss_ziel.='?fehler=sperre';
		}
	}elseif(isset($_POST['haltung']) && is_array($_POST['haltung'])){
		$ship=loadSpecialShip($_SESSION['ums_user_id']);
		foreach($_POST['haltung'] as $ss_bg => $ss_wert){
			if(isset($sv_bg[$ss_bg]) && Haltung::gueltig($ss_wert)){
				$ship->setHaltung($ss_bg, (int)$ss_wert);
			}
		}
		saveSpecialShip($_SESSION['ums_user_id'], $ship);
		$ss_ziel.='?ok=haltung';
	}

	header('Location: '.$ss_ziel, true, 303);
	exit;
}

//Meldung nach der Umleitung, nur feste Texte
$ss_meldungen=array(
	'ok' => array('upgrade' => 'Der Basisstern wurde ausgebaut.', 'haltung' => 'Die Haltungen wurden gespeichert.'),
	'fehler' => array('token' => 'Die Aktion konnte nicht ausgef&uuml;hrt werden. Bitte versuche es erneut.', 'palenium' => 'Du hast zu wenig Palenium.', 'sperre' => 'Die Aktion konnte nicht ausgef&uuml;hrt werden, es l&auml;uft bereits eine andere Aktion.')
);
$ss_meldung='';
foreach($ss_meldungen as $ss_art => $ss_texte){
	if(isset($_GET[$ss_art]) && is_string($_GET[$ss_art]) && isset($ss_texte[$_GET[$ss_art]])){
		$ss_meldung='<div class="mod-meldung mod-meldung-'.$ss_art.'">'.$ss_texte[$_GET[$ss_art]].'</div>';
	}
}

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

	$content.=rahmen_oben('BASISSTERN',false);

	//Werte des Basissterns als Kacheln
	$content.='
	<div class="einh mod">
		'.$ss_meldung.'
		<div class="einh-kacheln">
			<div class="ov-wert"><span class="mod-typ">Stufe</span><b>'.number_format($ship->ship_level, 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">H&uuml;llenstruktur</span><b>'.number_format($ship->get_hp_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Schutzschildenergie</span><b>'.number_format($ship->get_shield_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Waffenschaden</span><b>'.number_format($ship->get_wp_min(), 0, ',' ,'.').' &ndash; '.number_format($ship->get_wp_max(), 0, ',' ,'.').'</b></div>
		</div>';

	//Upgrade: Kosten in Palenium, Knopf nur wenn bezahlbar
	$palenium_str='<span class="mod-chip"><img src="gp/g/item1.png" alt="">Palenium <b>'.number_format($ps[1]['item_amount'], 0, ',' ,'.').'</b></span>';
	if($ps[1]['item_amount']>=$ship_upgrade_cost){
		$upgrade='<form method="post" action="specialship.php"><input type="hidden" name="token" value="'.CsrfToken::get().'"><button type="submit" name="upgrade_ship" value="1" class="mod-btn">Auf Stufe '.number_format(($ship->ship_level+1), 0, ',' ,'.').' upgraden</button></form>';
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

	$ship=loadSpecialShip($_SESSION['ums_user_id']);

	//Regeln der Haltungen, Kreis aus der Klasse, damit Text und Kampf übereinstimmen
	$ss_kreis=array();
	foreach(array(Haltung::ANGRIFF, Haltung::MANOEVER, Haltung::SCHILD) as $ss_h){
		$ss_kreis[]='<b>'.Haltung::name($ss_h).'</b> schl&auml;gt '.Haltung::name(Haltung::schlaegt($ss_h));
	}

	$content.='<form method="post" action="specialship.php" class="einh mod">
		<input type="hidden" name="token" value="'.CsrfToken::get().'">
		<details class="einh-regeln">
			<summary>So wirken Haltungen</summary>
			<p>Vor jedem Kampf stellt sich dein Basisstern auf eine Haltung ein: '.implode(', ', $ss_kreis).'.</p>
			<p>Die &uuml;berlegene Haltung richtet 50 % mehr Schaden an und erleidet ein Drittel weniger. Damit schl&auml;gt sie Gegner bis etwa zur anderthalbfachen Stufe. Bei gleicher Haltung entscheidet die Stufe.</p>
			<p><b>Zufall</b> w&uuml;rfelt die Haltung f&uuml;r jeden Kampf neu aus. Welche Haltungen galten, steht im Kampfbericht unter [BG].</p>
			<p>Im Allianz-Battleground gilt die Haltung, f&uuml;r die die meisten Basisstern-Stufen der Allianz gestimmt haben. Zufall z&auml;hlt dabei nicht mit, bei Gleichstand gilt Zufall.</p>
		</details>';

	//Battlegrounds aus der DB holen und darstellen
	$ss_wahl_moeglich=false;
	$sql="SELECT * FROM `de_map_objects` WHERE system_typ=4 ORDER BY system_subtyp ASC;";
	$db_data=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_data)){
		$system_data=unserialize($row['data']);
		$system_subtyp=$row['system_subtyp'];
		$system_id=$row['id'];

		//Test auf Weltraumhafen; teilnehmen kann man erst, wenn er fertig ist (wie im Kampftick)
		$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT bldg_time FROM `de_user_map_bldg` WHERE user_id=? AND map_id=? AND bldg_id=0", [$_SESSION['ums_user_id'], $system_id]);
		$hafen=mysqli_fetch_assoc($result);

		$status='';
		if($hafen && $hafen['bldg_time']<=time()){
			$status.='<span class="mod-chip mod-chip-gruen">Weltraumhafen vorhanden, du nimmst an den K&auml;mpfen teil</span>';

			for($s=$max_kt;$s<$max_kt+$sv_bg[$system_subtyp]['start_interval'];$s++){
				if(($s+1) % $sv_bg[$system_subtyp]['start_interval'] == 0){
					$startet_in=$s-$max_kt+1;
					$status.='<span class="mod-chip">N&auml;chster Start in <b>'.$startet_in.'</b> KT</span>';
					break;
				}
			}
		}elseif($hafen){
			$status.='<span class="mod-chip mod-chip-warn">Weltraumhafen im Bau '.\DieEwigen\DE2\View\RealTime::until((int)$hafen['bldg_time']).', danach nimmst du teil</span>';
		}else{
			$status.='<span class="mod-chip einh-chip-rot">Kein Weltraumhafen in dem System</span>';
		}

		//Haltung wählen, sobald man teilnimmt oder der Weltraumhafen im Bau ist
		$haltung_wahl='';
		if($hafen && isset($sv_bg[$system_subtyp])){
			$ss_wahl_moeglich=true;
			$ss_aktuell=$ship->getHaltung($system_subtyp);
			$optionen='';
			foreach(Haltung::alle() as $ss_wert => $ss_name){
				$optionen.='<label class="pol-option"><input type="radio" name="haltung['.intval($system_subtyp).']" value="'.$ss_wert.'"'.($ss_wert==$ss_aktuell ? ' checked' : '').'><span>'.$ss_name.'</span></label>';
			}
			$haltung_wahl='
			<div class="einh-haltung">
				<span class="mod-typ">'.($system_subtyp==2 ? 'Deine Stimme f&uuml;r die Haltung der Allianz' : 'Haltung').'</span>
				<div class="pol-optionen">'.$optionen.'</div>
			</div>';
		}

		$content.='
		<div class="einh-bg">
			<div class="einh-bg-name">'.$system_data->getSystemName().'</div>
			<div class="mod-typ">'.$sv_bg[$system_subtyp]['subname'].'</div>
			<div class="einh-bg-status">'.$status.'</div>
			'.$haltung_wahl.'
		</div>';
	}

	if($ss_wahl_moeglich){
		$content.='<div class="einh-haltung-fuss"><button type="submit" class="mod-btn">Haltungen speichern</button></div>';
	}

	$content.='</form>';

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
