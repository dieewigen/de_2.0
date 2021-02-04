<?php
////////////////////////////////////////////////////////////
// shaker, Agentensystem
////////////////////////////////////////////////////////////

$init_data=array();
$init_data['special_system']=7;
$init_data['phase']=0;

//Specialsystem-Daten laden
$data=getUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id);
if(empty($data)){
	$data=$init_data;
	//setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id, $data);
}

//$content.='<br>A:'.print_r($data,true);

//Description
$content.='<div>Diese Welt ist von einem undurchdringlichem Schutzschirm umgeben.</div>';

?>