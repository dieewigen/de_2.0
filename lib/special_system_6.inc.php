<?php
////////////////////////////////////////////////////////////
// Kreuzweg der Hekate, wechselnde Aufträge
// Logik in src/Model/Hekate/HekateService.php, Periodenwechsel in tickler/wt.php
////////////////////////////////////////////////////////////

//bewusst include: die Datei wird innerhalb von map_system::showSpecialSystem() eingebunden
include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_hekate.lang.php';

$hekate_uid = (int)$_SESSION['ums_user_id'];

$content .= '<div class="ms-sonder-text">'.$hekate_lang['geschichte'].'</div>';

//fehlen die Tabellen noch (Update nicht eingespielt), bleibt das System geschlossen
try {
	$hekate = new \DieEwigen\DE2\Model\Hekate\HekateService($GLOBALS['dbi']);
	$hekate_auftraege = $hekate->getAuftraege();
} catch (\Throwable $e) {
	$hekate = null;
}

if ($hekate === null) {
	$content .= '<div class="mod-hinweis ms-hinweis">'.$hekate_lang['fehler_nicht_bereit'].'</div>';
} elseif (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div class="mod-hinweis ms-hinweis">'.$hekate_lang['kein_zugang_npc'].'</div>';
} elseif (!$hekate->getSpecialSystemData()->isUnlocked($hekate_uid)) {
	//Kontakt aufnehmen
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$hekate->getSpecialSystemData()->unlock($hekate_uid, ['special_system' => \DieEwigen\DE2\Model\Hekate\HekateService::SPECIAL_SYSTEM_ID]);
		vs_flash_set(true, $hekate_lang['kontakt_ok']);
		vs_redirect($this->system_id);
	}
	$content .= '<p>'.$hekate_lang['kontakt_frage'].'</p>';
	$content .= '<div class="ms-aktion"><a href="?id='.$this->system_id.'&action=1" class="mod-btn">'.$hekate_lang['kontakt_link'].'</a></div>';
} else {
	//Auftrag erfüllen, danach neu laden (PRG), damit ein Neuladen nichts doppelt liefert
	if (isset($_POST['hekate_liefern'])) {
		if (!vs_csrf_check($_POST['vs_token'] ?? '')) {
			vs_flash_set(false, $hekate_lang['fehler_token']);
		} else {
			$hekate_result = $hekate->deliver($hekate_uid, (int)($_POST['auftrag'] ?? -1), (int)($_POST['period_nr'] ?? 0), $hekate_lang);
			vs_flash_set($hekate_result['ok'], $hekate_result['msg']);
		}
		vs_redirect($this->system_id);
	}

	$content .= vs_flash_html();

	$hekate_geliefert = $hekate->getDelivered($hekate_uid);

	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$hekate_lang['titel_auftraege'].'</span>';
	$content .= '<p class="ms-klein">'.strtr($hekate_lang['periode'], ['{WT}' => \DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate->getRemainingTicks())]).'</p>';

	//die Aufträge als Karten nebeneinander
	$content .= '<div class="ms-auftraege">';
	foreach ($hekate_auftraege as $hekate_nr => $hekate_auftrag) {
		$hekate_alles_da = true;
		$hekate_waren = '';
		foreach ($hekate_auftrag['waren'] as [$hekate_item, $hekate_menge]) {
			$hekate_lager = (int)($GLOBALS['ps'][$hekate_item]['item_amount'] ?? 0);
			$hekate_genug = $hekate_lager >= $hekate_menge;
			$hekate_alles_da = $hekate_alles_da && $hekate_genug;
			$hekate_waren .= '<span class="ms-posten'.($hekate_genug ? '' : ' ms-fehlt').'">'
				.\DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate_menge).' '.($GLOBALS['ps'][$hekate_item]['item_name'] ?? '#'.$hekate_item)
				.' <small>(Lager: '.\DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate_lager).')</small></span>';
		}
		$hekate_erledigt = in_array($hekate_nr, $hekate_geliefert);

		$content .= '<div class="ms-auftrag'.($hekate_erledigt ? ' ms-auftrag-erledigt' : '').'">';
		$content .= '<div class="ms-auftrag-kopf">'.strtr($hekate_lang['auftrag'], ['{NR}' => $hekate_nr + 1])
			.($hekate_erledigt ? '<span class="mod-chip mod-chip-gruen">'.$hekate_lang['geliefert'].'</span>' : '').'</div>';
		$content .= '<span class="mod-typ">'.$hekate_lang['verlangt'].'</span>'.$hekate_waren;
		$content .= '<span class="mod-typ">'.$hekate_lang['belohnung'].'</span>';
		$content .= '<div class="ms-belohnung">'.\DieEwigen\DE2\Model\Hekate\HekateService::formatBelohnung($hekate_auftrag['belohnung'], $hekate_lang).'</div>';

		if (!$hekate_erledigt) {
			$content .= '<div class="ms-auftrag-fuss">';
			if ($hekate_alles_da) {
				$content .= '
				<form method="post" action="?id='.$this->system_id.'">
					<input type="hidden" name="vs_token" value="'.htmlspecialchars(vs_csrf_token(), ENT_QUOTES, 'UTF-8').'">
					<input type="hidden" name="period_nr" value="'.$hekate->getPeriodNr().'">
					<input type="hidden" name="auftrag" value="'.$hekate_nr.'">
					<button type="submit" name="hekate_liefern" value="'.$hekate_lang['button'].'" class="mod-btn ally-btn-klein">'.$hekate_lang['button'].'</button>
				</form>';
			} else {
				$content .= '<span class="ms-fehlt">'.$hekate_lang['fehlt'].'</span>';
			}
			$content .= '</div>';
		}
		$content .= '</div>';
	}
	$content .= '</div></div>';

	//eigene Gunst
	$hekate_info = vs_bonus_info($hekate_uid);
	$content .= '<div class="ms-abschnitt"><span class="mod-typ">'.$hekate_lang['titel_gunst'].'</span>';
	if (empty($hekate_info['hekate'])) {
		$content .= '<p>'.$hekate_lang['gunst_keine'].'</p>';
	} else {
		$content .= '<ul class="ms-liste">';
		foreach ($hekate_info['hekate'] as $hekate_typ => $hekate_rest) {
			$hekate_key = $hekate_typ == \DieEwigen\DE2\Model\VsBonus\VsBonusService::TYP_INDUSTRIE ? 'gunst_industrie' : 'gunst_bauzeit';
			$content .= '<li>'.strtr($hekate_lang[$hekate_key], ['{PCT}' => \DieEwigen\DE2\Model\VsBonus\VsBonusService::getProzent($hekate_typ), '{WT}' => $hekate_rest]).'</li>';
		}
		$content .= '</ul>';
	}
	$content .= '<p class="ms-klein">'.strtr($hekate_lang['regel'], ['{MAX}' => \DieEwigen\DE2\Model\VsBonus\VsBonusService::getMaxVorlauf()]).'</p>';
	$content .= '</div>';
}
?>
