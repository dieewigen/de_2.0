<?php
use DieEwigen\DE2\Model\Sperre;
//////////////////////////////////////////////////////////////////////////////
// AJAX-Aktionen der VS-Übersicht (map_mobile.php), Antwort als JSON
// action=upgradeall&id=X: alle Gebäude eines normalen Systems upgraden
// action=buildall&id=X: auf allen freien Rohstofffeldern die passende Industrie bauen
//////////////////////////////////////////////////////////////////////////////
include "inc/header.inc.php";
include "functions.php";
include 'lib/map_system_defs.inc.php';
include 'lib/map_system.class.php';
include_once 'inc/userartefact.inc.php';

header('Content-Type: application/json; charset=utf-8');

function vs_ajax_response($ok, $msg, $extra = array()){
	echo json_encode(array_merge(array('ok' => $ok, 'msg' => $msg), $extra));
	exit;
}

$uid=$_SESSION['ums_user_id'];
$id=intval($_POST['id'] ?? 0);

$action=$_POST['action'] ?? '';
if(!in_array($action, array('upgradeall', 'buildall'), true) || $id<1){
	vs_ajax_response(false, 'Ung&uuml;ltige Anfrage.');
}

if(isset($sv_deactivate_vsystems) && $sv_deactivate_vsystems==1){
	vs_ajax_response(false, 'Auf diesem Server sind die Vergessenen Systeme deaktiviert.');
}

$pt=loadPlayerTechs($uid);
$GLOBALS['pt']=$pt;
if(!hasTech($pt,25)){
	vs_ajax_response(false, 'Die ben&ouml;tigte Technologie fehlt.');
}

//transaktionsbeginn
if(!Sperre::setzen($GLOBALS['dbi'], $uid)){
	vs_ajax_response(false, 'Es wird noch eine Aktion ausgef&uuml;hrt, bitte kurz warten.');
}

$GLOBALS['ps']=loadPlayerStorage($uid);
$GLOBALS['pd']=loadPlayerData($uid);
$GLOBALS['duration_factor']=vs_duration_factor($uid, $ua_werte);

//System laden
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_map_objects WHERE id=? LIMIT 1", [$id]);
$system_daten=mysqli_fetch_assoc($db_daten);
if(!$system_daten){
	Sperre::loesen($GLOBALS['dbi'], $uid);
	vs_ajax_response(false, 'Das System existiert nicht.');
}

$data=unserialize($system_daten['data']);
$data->system_id=$id;

//ist das System erforscht?
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT map_id FROM de_user_map WHERE user_id=? AND map_id=? AND known_since>0 AND known_since<?", [$uid, $id, time()]);
$is_explored=mysqli_num_rows($db_daten)>0;
$is_always_visible=$system_daten['always_visible']==1 || $system_daten['system_typ']==4;

//Gebäude in diesem System, Index = field_id
$data->playerBldg=loadPlayerBuildings($uid, $id);
$bldg=array();
foreach($data->playerBldg as $b){
	$bldg[$b['field_id']]=$b;
}

if(!$data->canUpgradeAllFromOverview($bldg, $is_explored, $is_always_visible)){
	Sperre::loesen($GLOBALS['dbi'], $uid);
	vs_ajax_response(false, 'In diesem System ist das nicht m&ouml;glich.');
}

$started=($action==='buildall') ? $data->buildMissingBuildings() : $data->upgradeAllBuildings();

//neuen Stand für die Zeile laden
$bldg=array();
foreach(loadPlayerBuildings($uid, $id) as $b){
	$bldg[$b['field_id']]=$b;
}

$pd=loadPlayerData($uid);

Sperre::loesen($GLOBALS['dbi'], $uid);

if($action==='buildall'){
	$msg=($started==0) ? 'Kein Bau m&ouml;glich' : ($started==1 ? '1 Geb&auml;ude im Bau' : $started.' Geb&auml;ude im Bau');
}elseif($started==0){
	$msg='Kein Upgrade m&ouml;glich';
}elseif($started==1){
	$msg='1 Upgrade gestartet';
}else{
	$msg=$started.' Upgrades gestartet';
}

$res=array();
for($r=1;$r<=5;$r++){
	$res[]=array(
		'full' => number_format(floor($pd['restyp0'.$r]), 0, "", "."),
		'short' => formatMasseinheit(floor($pd['restyp0'.$r]))
	);
}

vs_ajax_response(true, $msg, array(
	'started' => $started,
	'html' => $data->showOverviewRows($bldg, $is_explored, $is_always_visible),
	'res' => $res
));
