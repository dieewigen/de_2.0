<?php
////////////////////////////////////////////////////////////
// ARES, startet eine Allianzmission
////////////////////////////////////////////////////////////

$init_data=array();
$init_data['special_system']=3;
$init_data['phase']=0;

//Specialsystem-Daten laden
$data=getUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id);
if(empty($data)){
	$data=$init_data;
	//setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id, $data);
}

$content.=vs_flash_html();

if(hasTech($GLOBALS['pt'],145)){
	switch($data['phase']){

		case 0:
			//Test auf Aktion
			if(isset($_REQUEST['action']) && $_REQUEST['action']==1){
				//nächste Phase freischalten, danach neu laden (PRG)
				$data['phase']++;
				setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);
				vs_flash_set(true, 'Das Botschaftsgeb&auml;ude wurde bezogen.');
				vs_redirect($this->system_id);
			}

			//Aktion anbieten
			$content.='<p>Auf dem Planeten befindet sich ein bisher unbekanntes Volk. Soll Kontakt aufgenommen werden?</p>';
			$content.='<div class="ms-aktion"><a href="?id='.$this->system_id.'&action=1" class="mod-btn">Kontakt aufnehmen und ein Botschaftsgeb&auml;ude beziehen</a></div>';
		break;

		case 1:
			$content.='<p>Es wurde Kontakt aufgenommen und es steht eine neue Mission zur Verf&uuml;gung. Diese ist im Men&uuml;punkt Missionen zu finden.</p>';
			$content.='<div class="ms-aktion"><a href="missions.php" class="mod-btn mod-btn-leise">Zu den Missionen</a></div>';
		break;
	}
}else{
	//die Botschaft (Technologie 145) mit dem Namen für die Rasse des Spielers
	$row_tech=mysqli_fetch_array(mysqli_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=145"));
	$content.='<p>Diese Welt ist bewohnt und mit dem entsprechenden Wissen ist eine Kontaktaufnahme m&ouml;glich.</p>';
	$content.='<div class="mod-hinweis ms-hinweis">Voraussetzung: <a href="help.php?t=145">'.getTechNameByRasse($row_tech['tech_name'], $_SESSION['ums_rasse']).'</a></div>';
}
?>