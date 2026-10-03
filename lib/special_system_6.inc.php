<?php
////////////////////////////////////////////////////////////
// Kreuzweg der Hekate, wechselnde Aufträge
// Logik in src/Model/Hekate/HekateService.php, Periodenwechsel in tickler/wt.php
////////////////////////////////////////////////////////////

//bewusst include: die Datei wird innerhalb von map_system::showSpecialSystem() eingebunden
include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_hekate.lang.php';

$hekate_uid = (int)$_SESSION['ums_user_id'];

$content .= '<div style="text-align: left; padding: 5px;">'.$hekate_lang['geschichte'].'</div>';

//fehlen die Tabellen noch (Update nicht eingespielt), bleibt das System geschlossen
try {
	$hekate = new \DieEwigen\DE2\Model\Hekate\HekateService($GLOBALS['dbi']);
	$hekate_auftraege = $hekate->getAuftraege();
} catch (\Throwable $e) {
	$hekate = null;
}

if ($hekate === null) {
	$content .= '<div style="padding: 5px;">'.$hekate_lang['fehler_nicht_bereit'].'</div>';
} elseif (($GLOBALS['pd']['npc'] ?? 0) != 0) {
	$content .= '<div style="padding: 5px;">'.$hekate_lang['kein_zugang_npc'].'</div>';
} elseif (!$hekate->getSpecialSystemData()->isUnlocked($hekate_uid)) {
	//Kontakt aufnehmen
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 1) {
		$hekate->getSpecialSystemData()->unlock($hekate_uid, ['special_system' => \DieEwigen\DE2\Model\Hekate\HekateService::SPECIAL_SYSTEM_ID]);
		vs_flash_set(true, $hekate_lang['kontakt_ok']);
		vs_redirect($this->system_id);
	}
	$content .= '<br>'.$hekate_lang['kontakt_frage'].'<br><br><a href="?id='.$this->system_id.'&action=1">'.$hekate_lang['kontakt_link'].'</a><br><br>';
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

	$content .= '<div style="text-align: left; padding: 5px;">';
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 10px 0 5px 0;">'.$hekate_lang['titel_auftraege'].'</div>';
	$content .= strtr($hekate_lang['periode'], ['{WT}' => \DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate->getRemainingTicks())]);
	$content .= '</div>';

	//die Aufträge als Karten nebeneinander
	$content .= '<div style="display: flex; flex-wrap: wrap; gap: 8px; padding: 5px;">';
	foreach ($hekate_auftraege as $hekate_nr => $hekate_auftrag) {
		$hekate_alles_da = true;
		$hekate_waren = '';
		foreach ($hekate_auftrag['waren'] as [$hekate_item, $hekate_menge]) {
			$hekate_lager = (int)($GLOBALS['ps'][$hekate_item]['item_amount'] ?? 0);
			$hekate_genug = $hekate_lager >= $hekate_menge;
			$hekate_alles_da = $hekate_alles_da && $hekate_genug;
			$hekate_waren .= '<div'.($hekate_genug ? '' : ' style="color: #FF0000;"').'>'
				.\DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate_menge).' '.($GLOBALS['ps'][$hekate_item]['item_name'] ?? '#'.$hekate_item)
				.' (Lager: '.\DieEwigen\DE2\Model\Hekate\HekateService::formatNumber($hekate_lager).')</div>';
		}

		$content .= '<div style="flex: 1 1 170px; border: 1px solid #FFFFFF; padding: 5px; box-sizing: border-box; text-align: left;">';
		$content .= '<div style="font-weight: bold; margin-bottom: 5px;">'.strtr($hekate_lang['auftrag'], ['{NR}' => $hekate_nr + 1]).'</div>';
		$content .= '<div>'.$hekate_lang['verlangt'].'</div>'.$hekate_waren;
		$content .= '<div style="margin-top: 5px;">'.$hekate_lang['belohnung'].'</div>';
		$content .= '<div>'.\DieEwigen\DE2\Model\Hekate\HekateService::formatBelohnung($hekate_auftrag['belohnung'], $hekate_lang).'</div>';

		if (in_array($hekate_nr, $hekate_geliefert)) {
			$content .= '<div style="color: #00FF00; margin-top: 8px;">'.$hekate_lang['geliefert'].'</div>';
		} elseif ($hekate_alles_da) {
			$content .= '
			<form method="post" action="?id='.$this->system_id.'" style="margin-top: 8px;">
				<input type="hidden" name="vs_token" value="'.htmlspecialchars(vs_csrf_token(), ENT_QUOTES, 'UTF-8').'">
				<input type="hidden" name="period_nr" value="'.$hekate->getPeriodNr().'">
				<input type="hidden" name="auftrag" value="'.$hekate_nr.'">
				<input type="submit" name="hekate_liefern" value="'.$hekate_lang['button'].'">
			</form>';
		} else {
			$content .= '<div style="color: #FF0000; margin-top: 8px;">'.$hekate_lang['fehlt'].'</div>';
		}
		$content .= '</div>';
	}
	$content .= '</div>';

	//eigene Gunst
	$hekate_info = vs_bonus_info($hekate_uid);
	$content .= '<div style="text-align: left; padding: 5px;">';
	$content .= '<div style="font-weight: bold; font-size: 14px; margin: 10px 0 5px 0;">'.$hekate_lang['titel_gunst'].'</div>';
	if (empty($hekate_info['hekate'])) {
		$content .= $hekate_lang['gunst_keine'];
	} else {
		foreach ($hekate_info['hekate'] as $hekate_typ => $hekate_rest) {
			$hekate_key = $hekate_typ == \DieEwigen\DE2\Model\VsBonus\VsBonusService::TYP_INDUSTRIE ? 'gunst_industrie' : 'gunst_bauzeit';
			$content .= '<div>'.strtr($hekate_lang[$hekate_key], ['{PCT}' => \DieEwigen\DE2\Model\VsBonus\VsBonusService::getProzent($hekate_typ), '{WT}' => $hekate_rest]).'</div>';
		}
	}
	$content .= '<br>'.strtr($hekate_lang['regel'], ['{MAX}' => \DieEwigen\DE2\Model\VsBonus\VsBonusService::getMaxVorlauf()]);
	$content .= '</div>';
}
?>
