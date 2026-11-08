<?php
////////////////////////////////////////////////////////////
// Pfad des Thanatos, persönlicher Aufstieg über die Runde
// Logik in src/Model/Thanatos/ThanatosService.php, Boni in tickler/wt_manage_map.php und vs_duration_factor()
////////////////////////////////////////////////////////////

//bewusst include: die Datei wird innerhalb von map_system::showSpecialSystem() eingebunden
include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_thanatos.lang.php';

$thanatos = new \DieEwigen\DE2\Model\Thanatos\ThanatosService($GLOBALS['dbi']);
$thanatos_uid = (int)$_SESSION['ums_user_id'];

$content .= '<div class="ms-sonder-text">'.$thanatos_lang['geschichte'].'</div>';

if (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div class="mod-hinweis ms-hinweis">'.$thanatos_lang['kein_zugang_npc'].'</div>';
} elseif (!$thanatos->getSpecialSystemData()->isUnlocked($thanatos_uid)) {
	//vor die Wächter treten
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$thanatos->getSpecialSystemData()->unlock($thanatos_uid, ['special_system' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::SPECIAL_SYSTEM_ID, 'stufe' => 0]);
		vs_flash_set(true, $thanatos_lang['kontakt_ok']);
		vs_redirect($this->system_id);
	}
	$content .= '<p>'.$thanatos_lang['kontakt_frage'].'</p>';
	$content .= '<div class="ms-aktion"><a href="?id='.$this->system_id.'&action=1" class="mod-btn">'.$thanatos_lang['kontakt_link'].'</a></div>';
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

	//erreichte Stufe als Balken, darunter die Boni dieser Stufe
	$thanatos_ratio = min(1, $thanatos_stufe / $thanatos_max);
	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$thanatos_lang['titel_pfad'].'</span>';
	$content .= '<div class="ms-fortschritt"><div class="ms-fortschritt-text"><span>'.strtr($thanatos_lang['balken'], ['{STUFE}' => '<b>'.$thanatos_stufe.'</b>', '{MAX}' => $thanatos_max]).'</span></div>';
	$content .= '<div class="mod-balken"><span style="width: '.round($thanatos_ratio * 100, 1).'%"></span></div></div>';
	$content .= '<div class="ms-kacheln">';
	$content .= '<div class="ov-wert"><span class="mod-typ">'.$thanatos_lang['kachel_industrie'].'</span><b>+'
		.\DieEwigen\DE2\Model\Thanatos\ThanatosService::getIndustrieProzent($thanatos_stufe).' %</b><small>'.$thanatos_lang['kachel_info'].'</small></div>';
	$content .= '<div class="ov-wert"><span class="mod-typ">'.$thanatos_lang['kachel_bauzeit'].'</span><b>−'
		.\DieEwigen\DE2\Model\Thanatos\ThanatosService::getBauzeitProzent($thanatos_stufe).' %</b><small>'.$thanatos_lang['kachel_info'].'</small></div>';
	$content .= '</div>';
	$content .= '<p class="ms-klein">'.($thanatos_stufe > 0 ? $thanatos_lang['boni_dauer'] : $thanatos_lang['boni_keine']).'</p>';
	$content .= '</div>';

	//nächste Stufe
	if ($thanatos_stufe >= $thanatos_max) {
		$content .= '<div class="ms-abschnitt"><p><span class="mod-chip mod-chip-gruen">'.$thanatos_lang['max_erreicht'].'</span></p></div>';
	} else {
		$thanatos_neu = $thanatos_stufe + 1;
		$thanatos_kosten = \DieEwigen\DE2\Model\Thanatos\ThanatosService::getKosten($thanatos_neu);
		$thanatos_lager = $thanatos->getStock($thanatos_uid);
		$thanatos_ware = $GLOBALS['ps'][\DieEwigen\DE2\Model\Thanatos\ThanatosService::ITEM_ID]['item_name'] ?? 'Handelswaren V';

		$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$thanatos_lang['titel_naechste'].'</span>';
		$content .= '<p>'.strtr($thanatos_lang['naechste'], [
			'{STUFE}' => $thanatos_neu,
			'{KOSTEN}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber($thanatos_kosten),
			'{WARE}' => $thanatos_ware,
			'{LAGER}' => '<span'.($thanatos_lager >= $thanatos_kosten ? '' : ' class="ms-fehlt"').'>'.\DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber($thanatos_lager).'</span>',
			'{PALENIUM}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::formatNumber(\DieEwigen\DE2\Model\Thanatos\ThanatosService::getPalenium($thanatos_neu)),
			'{IND}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getIndustrieProzent($thanatos_neu),
			'{BAU}' => \DieEwigen\DE2\Model\Thanatos\ThanatosService::getBauzeitProzent($thanatos_neu),
		]).'</p>';

		if ($thanatos_lager >= $thanatos_kosten) {
			$content .= '
			<div class="ms-aktion"><form method="post" action="?id='.$this->system_id.'">
				<input type="hidden" name="vs_token" value="'.htmlspecialchars(vs_csrf_token(), ENT_QUOTES, 'UTF-8').'">
				<button type="submit" name="thanatos_darbringen" value="'.$thanatos_lang['button'].'" class="mod-btn">'.$thanatos_lang['button'].'</button>
			</form></div>';
		}
		$content .= '</div>';
	}
}
?>
