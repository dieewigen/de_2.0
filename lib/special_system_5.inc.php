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

$content .= '<div class="ms-sonder-text">'.$siegel_lang['geschichte'].'</div>';
$content .= vs_flash_html();

if (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div class="mod-hinweis ms-hinweis">'.$siegel_lang['kein_zugang_npc'].'</div>';
} elseif ($data['phase'] < 1) {
	//Verbindung herstellen, das schaltet den Agenteneinsatz BASRANUR in missions.php frei; danach neu laden (PRG)
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$data['phase'] = 1;
		setUserSpecialsystemDataByMapID($siegel_uid, $this->system_id, $data);
		vs_flash_set(true, $siegel_lang['verbindung_ok']);
		vs_redirect($this->system_id);
	}
	$content .= '<p>'.$siegel_lang['verbindung_frage'].'</p>';
	$content .= '<div class="ms-aktion"><a href="?id='.$this->system_id.'&action=1" class="mod-btn">'.$siegel_lang['verbindung_link'].'</a></div>';
} else {
	if (empty($_SESSION['siegel_token'])) {
		$_SESSION['siegel_token'] = bin2hex(random_bytes(16));
	}

	//Kristalle einsetzen
	if (isset($_POST['siegel_einsetzen'])) {
		if (!hash_equals($_SESSION['siegel_token'], (string)($_POST['siegel_token'] ?? ''))) {
			$content .= '<div class="mod-meldung mod-meldung-fehler">'.$siegel_lang['fehler_token'].'</div>';
		} else {
			$siegel_result = $siegel->donate($siegel_uid, (int)($_POST['siegel_menge'] ?? 0), $siegel_lang);
			$content .= '<div class="mod-meldung mod-meldung-'.($siegel_result['ok'] ? 'ok' : 'fehler').'">'.$siegel_result['msg'].'</div>';
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

	//Zustand: aktive Stufe, Aufladung für die nächste Periode, Ende der Periode
	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$siegel_lang['titel_status'].'</span>';
	$content .= '<div class="ms-kacheln ms-kacheln-3">';
	$content .= '<div class="ov-wert"><span class="mod-typ">'.$siegel_lang['kachel_stufe'].'</span><b>'.$siegel_level.'</b><small>'
		.($siegel_level > 0 ? strtr($siegel_lang['kachel_stufe_aktiv'], ['{PCT}' => $siegel_level * $siegel_pct]) : $siegel_lang['kachel_stufe_ruht']).'</small></div>';
	$content .= '<div class="ov-wert"><span class="mod-typ">'.$siegel_lang['kachel_aufladung'].'</span><b>'.$siegel_contributors.'</b><small>'
		.strtr($siegel_lang['kachel_aufladung_info'], ['{NEXT}' => $siegel_next, '{NEXTPCT}' => $siegel_next * $siegel_pct]).'</small></div>';
	$content .= '<div class="ov-wert"><span class="mod-typ">'.$siegel_lang['kachel_periode'].'</span><b>'
		.\DieEwigen\DE2\Model\Siegel\SiegelService::formatNumber($siegel->getRemainingTicks()).'</b><small>'.$siegel_lang['kachel_periode_info'].'</small></div>';
	$content .= '</div>';

	//Fortschrittsbalken: Mitwirkende bis zur höchsten Stufe
	$siegel_ratio = $siegel_max > 0 ? min(1, $siegel_contributors / ($siegel_step * $siegel_max)) : 1;
	$content .= '<div class="ms-fortschritt"><div class="ms-fortschritt-text"><span>'.strtr($siegel_lang['balken'], ['{N}' => $siegel_contributors, '{NEXT}' => $siegel_next]).'</span>';
	$content .= '<span>'.($siegel_missing > 0 ? strtr($siegel_lang['noch_bis'], ['{MISSING}' => $siegel_missing, '{STEP}' => $siegel_next + 1]) : $siegel_lang['max_erreicht']).'</span></div>';
	$content .= '<div class="mod-balken"><span style="width: '.round($siegel_ratio * 100, 1).'%"></span></div></div>';
	$content .= '<p class="ms-klein">'.strtr($siegel_lang['regel'], ['{SHARE}' => $siegel_anteil, '{STEP}' => $siegel_step, '{PCT}' => $siegel_pct]).'</p>';
	$content .= '</div>';

	//eigener Beitrag und Formular
	$siegel_own = $siegel->getOwnAmount($siegel_uid);
	$siegel_stock = $siegel->getStock($siegel_uid);
	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$siegel_lang['titel_einsetzen'].'</span>';
	$content .= '<p>'.strtr($siegel_lang['lager'], ['{STOCK}' => \DieEwigen\DE2\Model\Siegel\SiegelService::formatNumber($siegel_stock)]).'<br>';
	$content .= strtr($siegel_lang['eigener_beitrag'], ['{OWN}' => $siegel_own, '{SHARE}' => $siegel_anteil]).'</p>';

	$siegel_err = $siegel->checkDonate($siegel_uid);
	if ($siegel_own >= $siegel_anteil) {
		$content .= '<p><span class="mod-chip mod-chip-gruen">'.$siegel_lang['mitwirkender'].'</span></p>';
	} elseif ($siegel_err !== '') {
		$content .= '<div class="mod-meldung mod-meldung-warn">'.$siegel_lang[$siegel_err].'</div>';
	} elseif ($siegel_stock > 0) {
		$siegel_maxinput = min($siegel_anteil - $siegel_own, $siegel_stock);
		$content .= '
			<form method="post" action="?id='.$this->system_id.'" class="ms-formular">
				<input type="hidden" name="siegel_token" value="'.htmlspecialchars($_SESSION['siegel_token'], ENT_QUOTES, 'UTF-8').'">
				<input type="number" name="siegel_menge" id="siegel_menge" min="1" max="'.$siegel_maxinput.'" value="'.$siegel_maxinput.'" class="mod-eingabe">
				<button type="button" class="mod-btn mod-btn-leise" onclick="document.getElementById(\'siegel_menge\').value='.$siegel_maxinput.';">'.$siegel_lang['max'].'</button>
				<button type="submit" name="siegel_einsetzen" value="'.$siegel_lang['button'].'" class="mod-btn">'.$siegel_lang['button'].'</button>
			</form>';
	}
	$content .= '<div class="mod-hinweis ms-hinweis">'.$siegel_lang['mission_hinweis'].'</div>';
	$content .= '</div>';

	//Mitwirkende und Verlauf
	$siegel_names = array_map(fn ($n) => htmlspecialchars($n, ENT_QUOTES, 'UTF-8'), $siegel->getContributorNames());
	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$siegel_lang['titel_mitwirkende'].'</span>';
	if (empty($siegel_names)) {
		$content .= '<p class="ms-klein">'.$siegel_lang['keine_mitwirkenden'].'</p>';
	} else {
		$content .= '<div class="ms-namen"><span class="mod-chip">'.implode('</span><span class="mod-chip">', $siegel_names).'</span></div>';
	}
	$content .= '</div>';

	$siegel_history = trim((string)$siegel->getState()['history']);
	if ($siegel_history !== '') {
		$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$siegel_lang['titel_verlauf'].'</span><ul class="ms-liste">';
		foreach (preg_split('/\R/', $siegel_history) as $siegel_zeile) {
			$content .= '<li>'.htmlspecialchars($siegel_zeile, ENT_QUOTES, 'UTF-8').'</li>';
		}
		$content .= '</ul></div>';
	}
}
?>
