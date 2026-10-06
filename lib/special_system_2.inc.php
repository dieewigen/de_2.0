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

//Description
$content.='<div class="ms-sonder-text">Laut der Inschrift eines uralten Obelisken ist diese Welt nach einem ERHABENEN namens shaKer benannt.</div>';

//Phasen 0-2: Text, Kosten (werden verbraucht), Aktion und Meldung danach; ab Phase 3 liefert die Anlage Agenten (tickler/wt_manage_map.php)
$phasen=array(
	0 => array(
		'text' => 'Auf dem Planeten wurde ein riesiges versiegeltes Tor entdeckt. Mit dem passenden Werkzeug k&ouml;nnen deine Wissenschaftler das Tor &ouml;ffnen.',
		'cost' => array(array('I', 20, 1)),
		'aktion' => 'Tor &ouml;ffnen',
		'erfolg' => 'Das Tor wurde ge&ouml;ffnet.',
	),
	1 => array(
		'text' => 'Hinter dem Tor liegt eine verlassene technische Anlage, die jedoch deaktiviert ist, da die Energiequelle ersch&ouml;pft ist. Mit einer passenden Ersatzquelle k&ouml;nnte man versuchen die Anlage in Betrieb zu nehmen.',
		'cost' => array(array('I', 2, 10)),
		'aktion' => 'Anlage mit Energie versorgen',
		'erfolg' => 'Die Anlage wird jetzt mit Energie versorgt.',
	),
	2 => array(
		'text' => 'Die Anlage ist betriebsbereit, aber es ist nicht klar, welche Funktion sie hat. Die Wissenschaftler vermuten, dass man damit Agenten zu Ultra-Agenten verbessern kann und stimmen f&uuml;r einen Versuch, bei dem 1.000 Agenten der Anlage zugef&uuml;hrt werden.',
		'cost' => array(array('U', 'A', 1000)),
		'aktion' => 'Agenten in die Anlage schicken',
		'erfolg' => 'Die Agenten befinden sich jetzt in der Anlage.',
	),
);

$content.=vs_flash_html();

if(isset($phasen[$data['phase']])){
	$phase=$phasen[$data['phase']];

	//Test auf Aktion
	if(isset($_REQUEST['action']) && $_REQUEST['action']==1){
		if(hasSpecialsystemNeeds($phase['cost'])){
			//Kosten abziehen
			substractSpecialsystemNeeds($phase['cost']);

			//nächste Phase freischalten, danach neu laden (PRG), damit ein Neuladen nicht gleich die nächste Phase auslöst
			$data['phase']++;
			setUserSpecialsystemDataByMapID($_SESSION['ums_user_id'],$this->system_id, $data);
			vs_flash_set(true, $phase['erfolg']);
			vs_redirect($this->system_id);
		}else{
			$content.='<div class="mod-meldung mod-meldung-fehler">Du hast nicht alles, was ben&ouml;tigt wird.</div>';
		}
	}

	$content.='<div class="ms-abschnitt"><span class="mod-typ">Schritt '.($data['phase']+1).' von '.count($phasen).'</span><p>'.$phase['text'].'</p></div>';
	$content.='<div class="ms-abschnitt"><span class="mod-typ">Ben&ouml;tigt und verbraucht</span><div class="ms-kosten ms-kosten-liste">'.showSpecialsystemCost($phase['cost']).'</div></div>';

	//Aktion anbieten
	$content.='<div class="ms-aktion"><a href="?id='.$this->system_id.'&action=1" class="mod-btn">'.$phase['aktion'].'</a></div>';

}elseif($data['phase']==3){
	$content.='<p>Die Anlage beginnt damit die Agenten zu scannen und pl&ouml;tzlich werden die Agenten von hochenergetischer Hyperstrahlung zersetzt. Kein einziger Agent &uuml;berlebt den Vorgang. Scheinbar haben sich die Wissenschaftler geirrt was die Funktionsweise angeht.</p>';
	$content.='<div class="mod-hinweis ms-hinweis">Eine weitere Analyse ergibt jedoch, dass sich bisher inaktive Teile der Anlage aktivieren. In diesen werden Agenten produziert und alle 100 WT werden 100 Agenten geliefert.</div>';

}else{
	$content.='<div class="mod-meldung mod-meldung-fehler">FEHLER PHx01</div>';
}
