<?php
////////////////////////////////////////////////////////////
// Pfad des Thanatos, persönlicher Aufstieg über die Runde
// Logik in src/Model/Thanatos/ThanatosService.php, Boni in tickler/wt_manage_map.php und vs_duration_factor()
////////////////////////////////////////////////////////////

//bewusst include: die Datei wird innerhalb von map_system::showSpecialSystem() eingebunden
include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_thanatos.lang.php';

$thanatos = new \DieEwigen\DE2\Model\Thanatos\ThanatosService($GLOBALS['dbi']);
$thanatos_uid = (int)$_SESSION['ums_user_id'];

$content .= '<div style="text-align: left; padding: 5px;">'.$thanatos_lang['geschichte'].'</div>';

if (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div style="padding: 5px;">'.$thanatos_lang['kein_zugang_npc'].'</div>';
} elseif (!$thanatos->getSpecialSystemData()->isUnlocked($thanatos_uid)) {
	//vor die Wächter treten
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$thanatos->getSpecialSystemData()->unlock($thanatos_uid, ['special_system' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::SPECIAL_SYSTEM_ID, 'stufe' => 0]);
		vs_flash_set(true, $thanatos_lang['kontakt_ok']);
		vs_redirect($this->system_id);
	}
	$content .= '<br>'.$thanatos_lang['kontakt_frage'].'<br><br><a href="?id='.$this->system_id.'&action=1">'.$thanatos_lang['kontakt_link'].'</a><br><br>';
} else {
	//Handelswaren V darbringen, danach neu laden (PRG), damit ein Neuladen nicht doppelt aufsteigt
	if (isset($_POST['thanatos_darbringen'])) {
		if (!vs_csrf_check($_POST['vs_token'] ?? '')) {
			vs_flash_set(false, $thanatos_lang['fehler_token']);
		} else {
			$thanatos_result = $thanatos->levelUp($thanatos_uid, $thanatos_lang);
			vs_flash_set($thanatos_result['ok'], $thanatos_result['msg']);
		}
		vs_redirect($this->system_id);
	}

	$content .= vs_flash_html();

	$thanatos_stufe = $thanatos->getStufe($thanatos_uid);
	$thanatos_max = \DieEwigen\DE2\Model\Thanatos\ThanatosService::getMaxStufe();

	$content .= '<div style="text-align: left; padding: 5px;">';
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 10px 0 5px 0;">'.$thanatos_lang['titel_pfad'].'</div>';
	$content .= strtr($thanatos_lang['status'], ['{STUFE}' => $thanatos_stufe, '{MAX}' => $thanatos_max]).'<br>';
	if ($thanatos_stufe > 0) {
		$content .= strtr($thanatos_lang['boni'], [
			'{IND}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getIndustrieProzent($thanatos_stufe),
			'{BAU}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getBauzeitProzent($thanatos_stufe),
		]);
	} else {
		$content .= $thanatos_lang['boni_keine'];
	}
	$content .= '</div>';

	//Fortschrittsbalken wie beim Siegel von Basranur
	$thanatos_ratio = min(1, $thanatos_stufe / $thanatos_max);
	$thanatos_class = $thanatos_ratio > 0.66 ? 'progress-high' : ($thanatos_ratio > 0.33 ? 'progress-medium' : 'progress-normal');
	$content .= '
		<div id="gameProgressBar">
			<div class="scifi-progress-container">
				<div class="scifi-corner top-left"></div>
				<div class="scifi-corner top-right"></div>
				<div class="scifi-corner bottom-left"></div>
				<div class="scifi-corner bottom-right"></div>
				<div class="scifi-progress-bar '.$thanatos_class.'" style="width: '.round($thanatos_ratio * 100, 1).'%"></div>
				<div class="scifi-text">'.strtr($thanatos_lang['balken'], ['{STUFE}' => $thanatos_stufe, '{MAX}' => $thanatos_max]).'</div>
			</div>
		</div>';

	//nächste Stufe
	$content .= '<div style="text-align: left; padding: 5px;">';
	if ($thanatos_stufe >= $thanatos_max) {
		$content .= '<span style="color: #00FF00;">'.$thanatos_lang['max_erreicht'].'</span>';
	} else {
		$thanatos_neu = $thanatos_stufe + 1;
		$thanatos_kosten = \DieEwigen\DE2\Model\Thanatos\ThanatosService::getKosten($thanatos_neu);
		$thanatos_lager = $thanatos->getStock($thanatos_uid);
		$thanatos_ware = $GLOBALS['ps'][\DieEwigen\DE2\Model\Thanatos\ThanatosService::ITEM_ID]['item_name'] ?? 'Handelswaren V';

		$content .= '<div style="font-weight: bold; font-size: 14px; margin: 10px 0 5px 0;">'.$thanatos_lang['titel_naechste'].'</div>';
		$content .= strtr($thanatos_lang['naechste'], [
			'{STUFE}' => $thanatos_neu,
			'{KOSTEN}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber($thanatos_kosten),
			'{WARE}' => $thanatos_ware,
			'{LAGER}' => '<span'.($thanatos_lager >= $thanatos_kosten ? '' : ' style="color: #FF0000;"').'>'.\DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber($thanatos_lager).'</span>',
			'{PALENIUM}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber(\DieEwigen\DE2\Model\Thanatos\ThanatosService::getPalenium($thanatos_neu)),
			'{IND}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getIndustrieProzent($thanatos_neu),
			'{BAU}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getBauzeitProzent($thanatos_neu),
		]);

		if ($thanatos_lager >= $thanatos_kosten) {
			$content .= '
			<form method="post" action="?id='.$this->system_id.'" style="margin-top: 8px;">
				<input type="hidden" name="vs_token" value="'.htmlspecialchars(vs_csrf_token(), ENT_QUOTES, 'UTF-8').'">
				<input type="submit" name="thanatos_darbringen" value="'.$thanatos_lang['button'].'">
			</form>';
		}
	}
	$content .= '</div>';
}
?>
