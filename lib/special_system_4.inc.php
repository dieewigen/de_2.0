<?php
////////////////////////////////////////////////////////////
// Hephaistos, startet eine Allianzmission
////////////////////////////////////////////////////////////

$init_data=array();
$init_data['special_system']=4;
$init_data['phase']=0;

//Specialsystem-Daten laden
$data=getUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id);
if(empty($data)){
	$data=$init_data;
	//setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id, $data);
}

if(hasTech($GLOBALS['pt'],145)){
	switch($data['phase']){

		case 0:
			$show_action=true;
	
			$content.='<br><br>Auf dem Planeten befindet sich ein bisher unbekanntes Volk. Soll Kontakt aufgenommen werden?';
	
			//Test auf Aktion
			if($_REQUEST['action']==1){
				//nächste Phase freischalten
				$data['phase']++;
				setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);

				//weiter-link
				$content.='<br><br><a href="?id='.$this->system_id.'">Das Botschaftsgeb&auml;ude wurde bezogen. Weiter.</a><br><br>';
				$show_action=false;
			}
	
			//Aktionen anbieten
			if($show_action){
				$content.='<br><br><a href="?id='.$this->system_id.'&action=1">Kontakt aufnehmen und ein Botschaftsgeb&auml;ude beziehen</a><br><br>';
			}
		break;
	
		case 1:
			$content.='<br><br>Es wurde Kontakt aufgenommen und es steht eine neue Mission zur Verf&uuml;gung. Diese ist im Men&uuml;punkt Missionen zu finden.<br><br>';
		break;
	}
}else{
	$content.='<div>Diese Welt ist bewohnt und mit dem entsprechendem Wissen ist eine Kontaktaufnahme m&ouml;glich.</div>';
}


?>