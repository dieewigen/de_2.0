<?php
////////////////////////////////////////////////////////////
// Das Siegel von Basranur, gemeinsames Serverprojekt
// Logik in src/Model/Siegel/SiegelService.php, Mission in missions.php
////////////////////////////////////////////////////////////

//bewusst include: die Datei wird innerhalb von map_system::showSpecialSystem() eingebunden
include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_siegel.lang.php';

$siegel = new \DieEwigen\DE2\Model\Siegel\SiegelService($GLOBALS['dbi']);
$siegel_uid = (int)$_SESSION['ums_user_id'];

$init_data = array();
$init_data['special_system'] = 5;
$init_data['phase'] = 0;

//Specialsystem-Daten laden
$data = getUserSpecialsystemDataByMapID($siegel_uid, $this->system_id);
if (empty($data)) {
	$data = $init_data;
}

$content .= '<div style="text-align: left; padding: 5px;">'.$siegel_lang['geschichte'].'</div>';

if (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div style="padding: 5px;">'.$siegel_lang['kein_zugang_npc'].'</div>';
} elseif ($data['phase'] < 1) {
	//Verbindung herstellen, das schaltet den Agenteneinsatz BASRANUR in missions.php frei
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$data['phase'] = 1;
		setUserSpecialsystemDataByMapID($siegel_uid, $this->system_id, $data);
		$content .= '<br><a href="?id='.$this->system_id.'">'.$siegel_lang['verbindung_ok'].'</a><br><br>';
	} else {
		$content .= '<br>'.$siegel_lang['verbindung_frage'].'<br><br><a href="?id='.$this->system_id.'&action=1">'.$siegel_lang['verbindung_link'].'</a><br><br>';
	}
} else {
	if (empty($_SESSION['siegel_token'])) {
		$_SESSION['siegel_token'] = bin2hex(random_bytes(16));
	}

	//Kristalle einsetzen
	$siegel_msg = '';
	if (isset($_POST['siegel_einsetzen'])) {
		if (!hash_equals($_SESSION['siegel_token'], (string)($_POST['siegel_token'] ?? ''))) {
			$siegel_msg = '<div style="color: #FF0000; font-weight: bold; margin: 10px 0;">'.$siegel_lang['fehler_token'].'</div>';
		} else {
			$siegel_result = $siegel->donate($siegel_uid, (int)($_POST['siegel_menge'] ?? 0), $siegel_lang);
			$siegel_msg = '<div style="color: '.($siegel_result['ok'] ? '#00FF00' : '#FF0000').'; font-weight: bold; margin: 10px 0;">'.$siegel_result['msg'].'</div>';
		}
	}

	$siegel_level = $siegel->getLevel();
	$siegel_contributors = $siegel->countContributors();
	$siegel_next = $siegel->levelFor($siegel_contributors);
	$siegel_missing = $siegel->missingForNextLevel($siegel_contributors);
	$siegel_anteil = \DieEwigen\DE2\Model\Siegel\SiegelService::getAnteil();
	$siegel_step = \DieEwigen\DE2\Model\Siegel\SiegelService::getSpielerProStufe();
	$siegel_max = \DieEwigen\DE2\Model\Siegel\SiegelService::getMaxStufe();
	$siegel_pct = \DieEwigen\DE2\Model\Siegel\SiegelService::PROZENT_PRO_STUFE;

	//Zustand
	$content .= '<div style="text-align: left; padding: 5px;">';
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 10px 0 5px 0;">'.$siegel_lang['titel_status'].'</div>';
	$content .= strtr($siegel_level > 0 ? $siegel_lang['status_aktiv'] : $siegel_lang['status_ruht'], [
		'{LEVEL}' => $siegel_level,
		'{PCT}' => $siegel_level * $siegel_pct,
		'{WT}' => \DieEwigen\DE2\Model\Siegel\SiegelService::formatNumber($siegel->getRemainingTicks()),
	]);
	$content .= '<br><br>'.strtr($siegel_lang['aufladung'], ['{N}' => $siegel_contributors, '{NEXT}' => $siegel_next, '{NEXTPCT}' => $siegel_next * $siegel_pct]).' ';
	$content .= $siegel_missing > 0 ? strtr($siegel_lang['noch_bis'], ['{MISSING}' => $siegel_missing, '{STEP}' => $siegel_next + 1]) : $siegel_lang['max_erreicht'];
	$content .= '</div>';

	//Fortschrittsbalken wie bei der Rundenanzeige auf der Übersicht
	$siegel_ratio = $siegel_max > 0 ? min(1, $siegel_contributors / ($siegel_step * $siegel_max)) : 1;
	$siegel_class = $siegel_ratio > 0.66 ? 'progress-high' : ($siegel_ratio > 0.33 ? 'progress-medium' : 'progress-normal');
	$content .= '
		<div id="gameProgressBar">
			<div class="scifi-progress-container">
				<div class="scifi-corner top-left"></div>
				<div class="scifi-corner top-right"></div>
				<div class="scifi-corner bottom-left"></div>
				<div class="scifi-corner bottom-right"></div>
				<div class="scifi-progress-bar '.$siegel_class.'" style="width: '.round($siegel_ratio * 100, 1).'%"></div>
				<div class="scifi-text">'.strtr($siegel_lang['balken'], ['{N}' => $siegel_contributors, '{NEXT}' => $siegel_next]).'</div>
			</div>
		</div>';

	$content .= '<div style="text-align: left; padding: 5px;">';
	$content .= strtr($siegel_lang['regel'], ['{SHARE}' => $siegel_anteil, '{STEP}' => $siegel_step, '{PCT}' => $siegel_pct]);
	$content .= '<br><br>'.$siegel_lang['mission_hinweis'];

	//eigener Beitrag und Formular
	$siegel_own = $siegel->getOwnAmount($siegel_uid);
	$siegel_stock = $siegel->getStock($siegel_uid);
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 15px 0 5px 0;">'.$siegel_lang['titel_einsetzen'].'</div>';
	$content .= $siegel_msg;
	$content .= strtr($siegel_lang['lager'], ['{STOCK}' => \DieEwigen\DE2\Model\Siegel\SiegelService::formatNumber($siegel_stock)]).'<br>';
	$content .= strtr($siegel_lang['eigener_beitrag'], ['{OWN}' => $siegel_own, '{SHARE}' => $siegel_anteil]);

	$siegel_err = $siegel->checkDonate($siegel_uid);
	if ($siegel_own >= $siegel_anteil) {
		$content .= '<br><span style="color: #00FF00;">'.$siegel_lang['mitwirkender'].'</span>';
	} elseif ($siegel_err !== '') {
		$content .= '<br>'.$siegel_lang[$siegel_err];
	} elseif ($siegel_stock > 0) {
		$siegel_maxinput = min($siegel_anteil - $siegel_own, $siegel_stock);
		$content .= '
			<form method="post" action="?id='.$this->system_id.'" style="margin-top: 8px;">
				<input type="hidden" name="siegel_token" value="'.htmlspecialchars($_SESSION['siegel_token'], ENT_QUOTES, 'UTF-8').'">
				<input type="number" name="siegel_menge" id="siegel_menge" min="1" max="'.$siegel_maxinput.'" value="'.$siegel_maxinput.'" style="width: 60px;">
				<input type="button" value="'.$siegel_lang['max'].'" onclick="document.getElementById(\'siegel_menge\').value='.$siegel_maxinput.';">
				<input type="submit" name="siegel_einsetzen" value="'.$siegel_lang['button'].'">
			</form>';
	}

	//Mitwirkende und Verlauf
	$siegel_names = array_map(fn ($n) => htmlspecialchars($n, ENT_QUOTES, 'UTF-8'), $siegel->getContributorNames());
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 15px 0 5px 0;">'.$siegel_lang['titel_mitwirkende'].'</div>';
	$content .= empty($siegel_names) ? $siegel_lang['keine_mitwirkenden'] : implode(', ', $siegel_names);

	$siegel_history = trim((string)$siegel->getState()['history']);
	if ($siegel_history !== '') {
		$content .= '<div style="font-weight: bold; font-size: 14px; margin: 15px 0 5px 0;">'.$siegel_lang['titel_verlauf'].'</div>';
		$content .= nl2br(htmlspecialchars($siegel_history, ENT_QUOTES, 'UTF-8'));
	}
	$content .= '</div>';
}
?>
