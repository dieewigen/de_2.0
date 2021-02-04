<?php
////////////////////////////////////////////////////////////
// shaker, Agentensystem
////////////////////////////////////////////////////////////

$init_data=array();
$init_data['special_system']=2;
$init_data['phase']=0;

//Specialsystem-Daten laden
$data=getUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id);
if(empty($data)){
	$data=$init_data;
	//setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'], $this->system_id, $data);
}

//$content.='<br>A:'.print_r($data,true);

//Description
$content.='<div>Laut der Inschrift eines uralten Obelisken ist diese Welt nach einem ERHABENEN namens shaKer benannt.</div>';


switch($data['phase']){

	case 0:
		$cost=array();
		$cost[0]=array('I', 20, 1);
		$show_action=true;
		//$cost[1]=array('I', 15, 12);

		$content.='<br><br>Auf dem Planeten wurde ein riesiges versiegeltes Tor entdeckt. Mit dem passenden Werkzeug k&ouml;nnen deine Wissenschaftler das Tor &ouml;ffnen.';
		$content.='<br><br>Ben&ouml;tigt und verbraucht werden:<br>'.showSpecialsystemCost($cost);

		//Test auf Aktion
		if($_REQUEST['action']==1){
			if(hasSpecialsystemNeeds($cost)){
				//Kosten abziehen
				substractSpecialsystemNeeds($cost);

				//nächste Phase freischalten
				$data['phase']++;
				setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);

				//weiter-link
				$content.='<br><br><a href="?id='.$this->system_id.'">Das Tor wurde ge&ouml;ffnet. Weiter.</a><br><br>';
				$show_action=false;

			}else{
				$content.='<div style="color: #FF0000;">Du hast nicht alles was ben&ouml;tigt wird.</div>';
			}

		}

		//Aktionen anbieten
		if($show_action){
			$content.='<br><br><a href="?id='.$this->system_id.'&action=1">Tor &ouml;ffnen</a><br><br>';
		}
	break;

	case 1:
		$cost=array();
		$cost[0]=array('I', 2, 10);
		$show_action=true;

		$content.='<br><br>Hinter dem Tor liegt eine verlassene technische Anlage, die jedoch deaktiviert ist, da die Energiequelle ersch&ouml;pft ist. Mit einer passenden Ersatzquelle k&ouml;nnte man versuchen die Anlage in Betrieb zu nehmen.';
		$content.='<br><br>Ben&ouml;tigt und verbraucht werden:<br>'.showSpecialsystemCost($cost);

		//Test auf Aktion
		if($_REQUEST['action']==1){
			if(hasSpecialsystemNeeds($cost)){
				//Kosten abziehen
				substractSpecialsystemNeeds($cost);
				//nächste Phase freischalten
				$data['phase']++;
				setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);

				//weiter-link
				$content.='<br><br><a href="?id='.$this->system_id.'">Die Anlage wird jetzt mit Energie versorgt. Weiter.</a><br><br>';
				$show_action=false;

			}else{
				$content.='<div style="color: #FF0000;">Du hast nicht alles was ben&ouml;tigt wird.</div>';
			}

		}

		//Aktionen anbieten
		if($show_action){
			$content.='<br><br><a href="?id='.$this->system_id.'&action=1">Anlage mit Energie versorgen</a><br><br>';
		}
	break;

	case 2:
		$cost=array();
		$cost[0]=array('U', 'A', 1000);
		$show_action=true;

		$content.='<br><br>Die Anlage ist betriebsbereit, aber es ist nicht klar, welche Funktion sie hat. Die Wissenschaftler vermuten, dass man damit Agenten zu Ultra-Agenten verbessern kann und stimmen f&uuml;r einen Versuch, bei dem 1.000 Agenten der Anlage zugef&uuml;hrt werden.';
		$content.='<br><br>Ben&ouml;tigt und verbraucht werden:<br>'.showSpecialsystemCost($cost);

		//Test auf Aktion
		if($_REQUEST['action']==1){
			if(hasSpecialsystemNeeds($cost)){
				//Kosten abziehen
				substractSpecialsystemNeeds($cost);

				//nächste Phase freischalten
				$data['phase']++;
				setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);

				//weiter-link
				$content.='<br><br><a href="?id='.$this->system_id.'">Die Agenten befinden sich jetzt in der Anlage. Weiter.</a><br><br>';
				$show_action=false;

			}else{
				$content.='<div style="color: #FF0000;">Du hast nicht alles was ben&ouml;tigt wird.</div>';
			}

		}

		//Aktionen anbieten
		if($show_action){
			$content.='<br><br><a href="?id='.$this->system_id.'&action=1">Agenten in die Anlage schicken</a><br><br>';
		}
	break;

	case 3:
		$content.='<br><br>Die Anlage beginnt damit die Agenten zu scannen und pl&ouml;tzlich werden die Agenten von hochenergetischer Hyperstrahlung zersetzt. Kein einziger Agent überlebt den Vorgang. Scheinbar haben sich die Wissenschaftler geirrt was die Funktionsweise angeht.';
		$content.='<br><br>Eine weitere Analyse ergibt jedoch, dass sich bisher inaktive Teile der Anlage aktivieren. In diesen werden Agenten produziert und alle 100 WT werden 100 Agenten geliefert.';

	break;	

	default:
		$content.='FEHLER PHx01';
	break;
}

?>