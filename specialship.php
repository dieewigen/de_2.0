<?php
use DieEwigen\DE2\Model\Battleground\Battleground;
use DieEwigen\DE2\Model\Sperre;
include "inc/header.inc.php";
include "lib/map_system.class.php";
include 'lib/special_ship.class.php';
include "functions.php";
$sv_bg = Battleground::DEFS;

use DieEwigen\DE2\Model\Battleground\Haltung;
use DieEwigen\DE2\Model\Battleground\Werkstatt;
use DieEwigen\DE2\View\WerkstattKarte;
use DieEwigen\DE2\Session\CsrfToken;
include 'inc/lang/'.$sv_server_lang.'_werkstatt.lang.php';

//Werte eines Stücks als Text, z. B. "Hülle +11 %, Dämpfung +3 %"
function ws_werte_text($it, $lang){
	$teile=array();
	foreach($it['werte'] as $k => $eintrag){
		$teile[]=$lang['kennzahl'][$k].' '.Werkstatt::wertText($k, $eintrag);
	}
	return implode(', ', $teile);
}

//Kosten als Text mit Lagerbestand, rot wenn er nicht reicht; $ps: loadPlayerStorage()
function ws_kosten_text($kosten, $ps, &$reicht){
	$reicht=true;
	$teile=array();
	foreach($kosten as $item => $menge){
		$hat=(int)($ps[$item]['item_amount'] ?? 0);
		if($hat<$menge){
			$reicht=false;
		}
		$teile[]='<span class="'.($hat<$menge ? 'ms-fehlt' : '').'">'.number_format($menge, 0, ',', '.').' '.($ps[$item]['item_name'] ?? '#'.$item).'</span>';
	}
	return implode(' &middot; ', $teile);
}

$pt=loadPlayerTechs($_SESSION['ums_user_id']);

$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps']=$ps;

$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$spec4=$row['spec4'];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;

//maximalen tick auslesen
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT kt FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
$max_kt = $row["kt"];

//Aktionen per POST mit Token, danach Umleitung (Post/Redirect/Get), damit Neuladen nichts wiederholt
if(hasTech($pt,159) && ($_SERVER['REQUEST_METHOD'] ?? '')==='POST'){
	$ss_ziel='specialship.php';
	if(!CsrfToken::check($_POST['token'] ?? '')){
		$ss_ziel.='?fehler=token';
	}elseif(isset($_POST['upgrade_ship'])){
		if(Sperre::setzen($GLOBALS['dbi'], $_SESSION['ums_user_id'])){
			$ship=loadSpecialShip($_SESSION['ums_user_id']);
			$ps=loadPlayerStorage($_SESSION['ums_user_id']);
			$ship_upgrade_cost=($ship->ship_level+1)*100;
			if($ps[1]['item_amount']>=$ship_upgrade_cost){
				$ship->ship_level++;
				saveSpecialShip($_SESSION['ums_user_id'], $ship);

				//Rohstoffe abziehen
				change_storage_amount($_SESSION['ums_user_id'], 1, $ship_upgrade_cost*-1, false);
				$ss_ziel.='?ok=upgrade';
			}else{
				$ss_ziel.='?fehler=palenium';
			}
			Sperre::loesen($GLOBALS['dbi'], $_SESSION['ums_user_id']);
		}else{
			$ss_ziel.='?fehler=sperre';
		}
	}elseif(isset($_POST['haltung']) && is_array($_POST['haltung'])){
		$ship=loadSpecialShip($_SESSION['ums_user_id']);
		foreach($_POST['haltung'] as $ss_bg => $ss_wert){
			if(isset($sv_bg[$ss_bg]) && Haltung::gueltig($ss_wert)){
				$ship->setHaltung($ss_bg, (int)$ss_wert);
			}
		}
		saveSpecialShip($_SESSION['ums_user_id'], $ship);
		$ss_ziel.='?ok=haltung';
	}elseif(isset($_POST['ws_aktion']) && is_string($_POST['ws_aktion'])){
		//Werkstatt: Rohstoffe und Staub aendern sich, deshalb in der Spielersperre; Meldung in die Sitzung, die Seite zeigt sie einmal
		if(Sperre::setzen($GLOBALS['dbi'], $_SESSION['ums_user_id'])){
			$ws_uid=(int)$_SESSION['ums_user_id'];
			$ws=new Werkstatt($GLOBALS['dbi']);
			$ship=loadSpecialShip($ws_uid);
			$ws_id=(int)($_POST['ws_id'] ?? 0);
			$ws_it=$ws_id>0 ? $ws->item($ws_uid, $ws_id) : null;
			$ws_name=$ws_it ? Werkstatt::name($ws_it, $werkstatt_lang) : '';
			$ws_fehler='';
			$ws_text='';
			$ws_neu_id=0;
			switch($_POST['ws_aktion']){
				case 'schmieden':
					$ws_erg=$ws->schmieden($ws_uid, $ship, (int)($_POST['ws_platz'] ?? 0));
					$ws_fehler=$ws_erg['fehler'];
					if($ws_erg['ok']){
						$ws_neu_id=(int)$ws_erg['item']['id'];
						$ws_text=str_replace(array('{NAME}', '{WERTE}'), array(Werkstatt::name($ws_erg['item'], $werkstatt_lang), ws_werte_text($ws_erg['item'], $werkstatt_lang)), $werkstatt_lang['meldung']['geschmiedet']);
						if($ws_erg['rang_neu']){
							$ws_text.=str_replace('{RANG}', Werkstatt::rang($ship), $werkstatt_lang['meldung']['rang_neu']);
						}
					}
					break;
				case 'anlegen':
				case 'ablegen':
					$ws_fehler=$_POST['ws_aktion']==='anlegen' ? $ws->anlegen($ws_uid, $ws_id) : $ws->ablegen($ws_uid, $ws_id);
					$ws_text=str_replace('{NAME}', $ws_name, $werkstatt_lang['meldung'][$_POST['ws_aktion']==='anlegen' ? 'angelegt' : 'abgelegt']);
					break;
				case 'zerlegen':
					$ws_erg=$ws->zerlegen($ws_uid, $ship, $ws_id);
					$ws_fehler=$ws_erg['fehler'];
					if($ws_fehler===''){
						$ws_liste=array();
						foreach($ws_erg['rueck'] as $ws_item => $ws_menge){
							$ws_liste[]=number_format($ws_menge, 0, ',', '.').' '.($ps[$ws_item]['item_name'] ?? '#'.$ws_item);
						}
						$ws_rueck=$ws_liste ? str_replace('{LISTE}', implode(', ', $ws_liste), $werkstatt_lang['meldung']['zerlegt_rueck']) : '';
						$ws_text=str_replace(array('{NAME}', '{STAUB}', '{RUECK}'), array($ws_name, $ws_erg['staub'], $ws_rueck), $werkstatt_lang['meldung']['zerlegt']);
					}
					break;
				case 'veredeln':
					$ws_k=(string)($_POST['ws_kennzahl'] ?? '');
					$ws_fehler=$ws->veredeln($ws_uid, $ship, $ws_id, $ws_k);
					if($ws_fehler===''){
						$ws_it=$ws->item($ws_uid, $ws_id);
						$ws_text=str_replace(array('{NAME}', '{KENNZAHL}', '{WERT}'), array($ws_name, $werkstatt_lang['kennzahl'][$ws_k], Werkstatt::wertText($ws_k, $ws_it['werte'][$ws_k])), $werkstatt_lang['meldung']['veredelt']);
					}
					break;
				case 'lager':
					$ws_fehler=$ws->lagerAusbauen($ws_uid, $ship);
					$ws_text=str_replace('{PLAETZE}', Werkstatt::lagerPlaetze($ship), $werkstatt_lang['meldung']['lager']);
					break;
				default:
					$ws_fehler='unbekannt';
			}
			saveSpecialShip($ws_uid, $ship);
			Sperre::loesen($GLOBALS['dbi'], $ws_uid);
			$_SESSION['ws_meldung']=($ws_fehler==='') ? array('ok', $ws_text, $ws_neu_id) : array('fehler', $werkstatt_lang['fehler'][$ws_fehler] ?? $werkstatt_lang['fehler']['unbekannt']);
		}else{
			$ss_ziel.='?fehler=sperre';
		}
	}

	header('Location: '.$ss_ziel, true, 303);
	exit;
}

//Meldung nach der Umleitung, nur feste Texte
$ss_meldungen=array(
	'ok' => array('upgrade' => 'Der Basisstern wurde ausgebaut.', 'haltung' => 'Die Haltungen wurden gespeichert.'),
	'fehler' => array('token' => 'Die Aktion konnte nicht ausgef&uuml;hrt werden. Bitte versuche es erneut.', 'palenium' => 'Du hast zu wenig Palenium.', 'sperre' => 'Die Aktion konnte nicht ausgef&uuml;hrt werden, es l&auml;uft bereits eine andere Aktion.')
);
$ss_meldung='';
foreach($ss_meldungen as $ss_art => $ss_texte){
	if(isset($_GET[$ss_art]) && is_string($_GET[$ss_art]) && isset($ss_texte[$_GET[$ss_art]])){
		$ss_meldung='<div class="mod-meldung mod-meldung-'.$ss_art.'">'.$ss_texte[$_GET[$ss_art]].'</div>';
	}
}
//Meldung der Werkstatt aus der Sitzung, einmal anzeigen
$ws_meldung='';
$ws_neu_id=0;
if(isset($_SESSION['ws_meldung']) && is_array($_SESSION['ws_meldung'])){
	$ws_meldung='<div class="mod-meldung mod-meldung-'.($_SESSION['ws_meldung'][0]==='ok' ? 'ok' : 'fehler').'">'.$_SESSION['ws_meldung'][1].'</div>';
	$ws_neu_id=(int)($_SESSION['ws_meldung'][2] ?? 0);
	unset($_SESSION['ws_meldung']);
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Basisstern</title>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
include "cssinclude.php";
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$content='';

//hat man die benötigte Technologie?
if(!hasTech($pt,159)){
	$content.='<br>'.\DieEwigen\DE2\View\FehlendeTechnologie::karte(159);
}else{

	$ship=loadSpecialShip($_SESSION['ums_user_id']);

	$ship_upgrade_cost=($ship->ship_level+1)*100;

	$content.=rahmen_oben('Basisstern',false);

	//Werte des Basissterns als Kacheln
	$content.='
	<div class="einh mod">
		'.$ss_meldung.'
		<div class="einh-kacheln">
			<div class="ov-wert"><span class="mod-typ">Stufe</span><b>'.number_format($ship->ship_level, 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">H&uuml;llenstruktur</span><b>'.number_format($ship->get_hp_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Schutzschildenergie</span><b>'.number_format($ship->get_shield_max(), 0, ',' ,'.').'</b></div>
			<div class="ov-wert"><span class="mod-typ">Waffenschaden</span><b>'.number_format($ship->get_wp_min(), 0, ',' ,'.').' &ndash; '.number_format($ship->get_wp_max(), 0, ',' ,'.').'</b></div>
		</div>';

	//Upgrade: Kosten in Palenium, Knopf nur wenn bezahlbar
	$palenium_str='<span class="mod-chip"><img src="gp/g/item1.png" alt="">Palenium <b>'.number_format($ps[1]['item_amount'], 0, ',' ,'.').'</b></span>';
	if($ps[1]['item_amount']>=$ship_upgrade_cost){
		$upgrade='<form method="post" action="specialship.php"><input type="hidden" name="token" value="'.CsrfToken::get().'"><button type="submit" name="upgrade_ship" value="1" class="mod-btn">Auf Stufe '.number_format(($ship->ship_level+1), 0, ',' ,'.').' upgraden</button></form>';
	}else{
		$upgrade='<span class="mod-feld mod-feld-grund">Zu wenig Palenium</span>';
	}
	$content.='
		<div class="einh-upgrade">
			<div class="einh-upgrade-text">Upgrade auf Stufe '.number_format(($ship->ship_level+1), 0, ',' ,'.').': <b>'.number_format($ship_upgrade_cost, 0, ',' ,'.').' Palenium</b> '.$palenium_str.'</div>
			'.$upgrade.'
		</div>
	</div>';


	$content.=rahmen_unten(false);

	////////////////////////////////////////
	//Werkstatt des Basissterns (docs/werkstatt.md)
	////////////////////////////////////////
	$ws=new Werkstatt($GLOBALS['dbi']);
	$ws_uid=(int)$_SESSION['ums_user_id'];
	$ws_items=$ws->items($ws_uid);
	$ws_rang=Werkstatt::rang($ship);
	$ws_naechster=Werkstatt::naechsterRangXp($ship);
	$ws_plaetze=Werkstatt::lagerPlaetze($ship);
	$ws_belegt=0;
	$ws_angelegt=array();
	foreach($ws_items as $ws_it){
		if($ws_it['angelegt']){
			$ws_angelegt[$ws_it['platz']]=$ws_it;
		}else{
			$ws_belegt++;
		}
	}
	$ws_summe=Werkstatt::summe($ws_angelegt);
	$ws_max_v=Werkstatt::veredelnMax($ship);
	$ws_token='<input type="hidden" name="token" value="'.CsrfToken::get().'">';

	$content.=rahmen_oben($werkstatt_lang['titel'],false);
	$content.='<div class="einh mod ws">'.$ws_meldung;
	$ws_ctx=array('lang' => $werkstatt_lang, 'token' => $ws_token, 'max_v' => $ws_max_v, 'angelegt' => $ws_angelegt, 'aktionen' => true);
	//frisch geschmiedetes Stueck gross zeigen, blendet sich ein (View\WerkstattKarte)
	$ws_neu=$ws_neu_id>0 ? $ws->item($ws_uid, $ws_neu_id) : null;
	if($ws_neu){
		$content.='<div class="ws-wurf ws-wurf-'.$ws_neu['guete'].'"><span class="mod-typ">'.$werkstatt_lang['wurf'].'</span>'.WerkstattKarte::html($ws_neu, $ws_ctx+array('neu' => true)).'</div>';
	}
	$content.='<div class="einh-kacheln">
		<div class="ov-wert"><span class="mod-typ">'.$werkstatt_lang['rang'].'</span><b>'.$ws_rang.'</b><small>'.($ws_naechster>0 ? number_format((int)$ship->erfahrung, 0, ',', '.').' / '.$ws_naechster.' '.$werkstatt_lang['erfahrung'] : 'h&ouml;chster Rang').'</small></div>
		<div class="ov-wert"><span class="mod-typ">'.$werkstatt_lang['staub'].'</span><b>'.number_format((int)$ship->staub, 0, ',', '.').'</b></div>
		<div class="ov-wert"><span class="mod-typ">'.$werkstatt_lang['lager'].'</span><b>'.$ws_belegt.' / '.$ws_plaetze.'</b><small>'.count($ws_angelegt).' angelegt</small></div>
		<div class="ov-wert"><span class="mod-typ">W&uuml;rfe</span><b>'.number_format((int)$ship->wuerfe, 0, ',', '.').'</b></div>
	</div>';
	if($ws_naechster>0){
		$content.='<div class="mod-balken ws-balken"><span style="width:'.min(100, round((int)$ship->erfahrung*100/$ws_naechster)).'%"></span></div>';
		//was der naechste Rang bringt: Chancen auf Seltene, Mystische Ware und Artefakt
		$ws_teaser=array();
		foreach(array(2, 3, 4) as $ws_g){
			$ws_teaser[]='<span class="ws-guete ws-guete-'.$ws_g.'">'.$werkstatt_lang['guete'][$ws_g].' '.Werkstatt::CHANCEN[$ws_rang][$ws_g].' &rarr; '.Werkstatt::CHANCEN[$ws_rang+1][$ws_g].' %</span>';
		}
		$content.='<div class="ws-teaser">'.strtr($werkstatt_lang['naechster_rang'], array('{RANG}' => $ws_rang+1)).': '.implode(' ', $ws_teaser).'</div>';
	}

	//Regeln, aufklappbar
	$content.='<details class="einh-regeln"><summary>So funktioniert die Werkstatt</summary>';
	foreach($werkstatt_lang['regeln'] as $ws_absatz){
		$content.='<p>'.$ws_absatz.'</p>';
	}
	$content.='<p>'.strtr($werkstatt_lang['veredeln_info'], array('{MAX}' => $ws_max_v)).'</p>';
	$content.='<p>'.$werkstatt_lang['chancen'].': ';
	$ws_chancen=array();
	foreach(Werkstatt::CHANCEN[$ws_rang] as $ws_g => $ws_p){
		$ws_chancen[]='<span class="ws-guete ws-guete-'.$ws_g.'">'.$werkstatt_lang['guete'][$ws_g].' '.$ws_p.' %</span>';
	}
	$content.=implode(' ', $ws_chancen).'</p></details>';

	//Ausrüstung im Kampf: Summen gegen die Deckel
	$content.='<div class="ws-abschnitt"><span class="mod-typ">'.$werkstatt_lang['im_kampf'].'</span><div class="ws-summe">';
	foreach(Werkstatt::KENNZAHLEN as $ws_k){
		$ws_einheit=in_array($ws_k, Werkstatt::PROZENT) ? ' %' : ' Pkt.';
		$content.='<span class="mod-chip'.($ws_summe[$ws_k]>0 ? ' mod-chip-gruen' : '').'" title="'.$werkstatt_lang['kennzahl'][$ws_k].'&'.$werkstatt_lang['kennzahl_hilfe'][$ws_k].'<br>'.$werkstatt_lang['deckel'].': '.Werkstatt::DECKEL[$ws_k].$ws_einheit.'">'.$werkstatt_lang['kennzahl'][$ws_k].' <b>+'.number_format($ws_summe[$ws_k], $ws_summe[$ws_k]==floor($ws_summe[$ws_k]) ? 0 : 1, ',', '.').$ws_einheit.'</b></span>';
	}
	$content.='</div></div>';

	//Rezepte
	$content.='<div class="ws-abschnitt"><span class="mod-typ">'.$werkstatt_lang['rezepte'].'</span>';
	$content.='<div class="bk-zeile ws-rezept bk-kopfzeile"><span>Platz</span><span>'.$werkstatt_lang['kosten'].'</span><span>Erf.</span><span></span></div><div class="bk-liste">';
	$ws_lager_voll=$ws_belegt>=$ws_plaetze;
	foreach(Werkstatt::REZEPT as $ws_platz => $ws_rezept){
		$ws_kosten=ws_kosten_text($ws_rezept['kosten'], $ps, $ws_reicht);
		$content.='<div class="bk-zeile ws-rezept"><span class="bk-name" title="'.$werkstatt_lang['platz'][$ws_platz].'&'.$werkstatt_lang['hauptwert'].': '.$werkstatt_lang['kennzahl'][Werkstatt::HAUPTWERT[$ws_platz]].'">'.$werkstatt_lang['platz'][$ws_platz].'<br><small class="bk-leise">'.$werkstatt_lang['kennzahl'][Werkstatt::HAUPTWERT[$ws_platz]].'</small></span>';
		$content.='<span>'.$ws_kosten.'</span><span class="bk-zahl">'.$ws_rezept['xp'].'</span>';
		if($ws_reicht && !$ws_lager_voll){
			$content.='<span class="bk-status"><form method="post" action="specialship.php">'.$ws_token.'<input type="hidden" name="ws_aktion" value="schmieden"><input type="hidden" name="ws_platz" value="'.$ws_platz.'"><button type="submit" class="mod-btn ally-btn-klein">'.$werkstatt_lang['schmieden'].'</button></form></span>';
		}else{
			$content.='<span class="bk-status bk-leise">'.($ws_lager_voll ? 'Lager voll' : 'Rohstoffe fehlen').'</span>';
		}
		$content.='</div>';
	}
	$content.='</div>';
	//Lager ausbauen
	$ws_lagerkosten=ws_kosten_text(Werkstatt::lagerKosten((int)$ship->lager_stufe+1), $ps, $ws_reicht);
	$content.='<div class="bk-zeile ws-rezept ws-lager"><span class="bk-name">'.$werkstatt_lang['lager_ausbauen'].'<br><small class="bk-leise">+'.Werkstatt::LAGER_SCHRITT.' Pl&auml;tze, Schritt '.((int)$ship->lager_stufe+1).'</small></span><span>'.$ws_lagerkosten.'</span><span></span>';
	if($ws_reicht){
		$content.='<span class="bk-status"><form method="post" action="specialship.php">'.$ws_token.'<input type="hidden" name="ws_aktion" value="lager"><button type="submit" class="mod-btn mod-btn-leise ally-btn-klein" data-bestaetigen="Wirklich?">'.$werkstatt_lang['lager_ausbauen'].'</button></form></span>';
	}else{
		$content.='<span class="bk-status bk-leise">Rohstoffe fehlen</span>';
	}
	$content.='</div></div>';

	//Stücke: angelegte zuerst, dann das Lager
	$content.='<div class="ws-abschnitt"><span class="mod-typ">Deine St&uuml;cke</span>';
	if(count($ws_items)==0){
		$content.='<div class="mod-leer">'.$werkstatt_lang['keine_stuecke'].'</div>';
	}else{
		$content.='<div class="ws-karten">';
		foreach($ws_items as $ws_it){
			$content.=WerkstattKarte::html($ws_it, $ws_ctx+array('neu' => $ws_it['id']==$ws_neu_id));
		}
		$content.='</div>';
	}
	$content.='</div>';

	$content.='</div>';
	$content.=rahmen_unten(false);

}

//hat man die benötigte Technologie?
if(!hasTech($pt,159)){
	$content.='<br>'.\DieEwigen\DE2\View\FehlendeTechnologie::karte(159);
}else{

	$content.=rahmen_oben('Battlegrounds',false);

	$ship=loadSpecialShip($_SESSION['ums_user_id']);

	//Regeln der Haltungen, Kreis aus der Klasse, damit Text und Kampf übereinstimmen
	$ss_kreis=array();
	foreach(array(Haltung::ANGRIFF, Haltung::MANOEVER, Haltung::SCHILD) as $ss_h){
		$ss_kreis[]='<b>'.Haltung::name($ss_h).'</b> schl&auml;gt '.Haltung::name(Haltung::schlaegt($ss_h));
	}

	$content.='<form method="post" action="specialship.php" class="einh mod">
		<input type="hidden" name="token" value="'.CsrfToken::get().'">
		<details class="einh-regeln">
			<summary>So wirken Haltungen</summary>
			<p>Vor jedem Kampf stellt sich dein Basisstern auf eine Haltung ein: '.implode(', ', $ss_kreis).'.</p>
			<p>Die &uuml;berlegene Haltung richtet 50 % mehr Schaden an und erleidet ein Drittel weniger. Damit schl&auml;gt sie Gegner bis etwa zur anderthalbfachen Stufe. Bei gleicher Haltung entscheidet die Stufe.</p>
			<p><b>Zufall</b> w&uuml;rfelt die Haltung f&uuml;r jeden Kampf neu aus. Welche Haltungen galten, steht im Kampfbericht unter [BG].</p>
			<p>Im Allianz-Battleground gilt die Haltung, f&uuml;r die die meisten Basisstern-Stufen der Allianz gestimmt haben. Zufall z&auml;hlt dabei nicht mit, bei Gleichstand gilt Zufall.</p>
		</details>';

	//Battlegrounds aus der DB holen und darstellen
	$ss_wahl_moeglich=false;
	$sql="SELECT * FROM `de_map_objects` WHERE system_typ=4 ORDER BY system_subtyp ASC;";
	$db_data=mysqli_execute_query($GLOBALS['dbi'], $sql, []);
	while($row = mysqli_fetch_array($db_data)){
		$system_data=unserialize($row['data']);
		$system_subtyp=$row['system_subtyp'];
		$system_id=$row['id'];

		//Test auf Weltraumhafen; teilnehmen kann man erst, wenn er fertig ist (wie im Kampftick)
		$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT bldg_time FROM `de_user_map_bldg` WHERE user_id=? AND map_id=? AND bldg_id=0", [$_SESSION['ums_user_id'], $system_id]);
		$hafen=mysqli_fetch_assoc($result);

		$status='';
		if($hafen && $hafen['bldg_time']<=time()){
			$status.='<span class="mod-chip mod-chip-gruen">Weltraumhafen vorhanden, du nimmst an den K&auml;mpfen teil</span>';

			for($s=$max_kt;$s<$max_kt+$sv_bg[$system_subtyp]['start_interval'];$s++){
				if(($s+1) % $sv_bg[$system_subtyp]['start_interval'] == 0){
					$startet_in=$s-$max_kt+1;
					$status.='<span class="mod-chip">N&auml;chster Start in <b>'.$startet_in.'</b> KT</span>';
					break;
				}
			}
		}elseif($hafen){
			$status.='<span class="mod-chip mod-chip-warn">Weltraumhafen im Bau '.\DieEwigen\DE2\View\RealTime::until((int)$hafen['bldg_time']).', danach nimmst du teil</span>';
		}else{
			$status.='<span class="mod-chip einh-chip-rot">Kein Weltraumhafen in dem System</span>';
		}

		//Haltung wählen, sobald man teilnimmt oder der Weltraumhafen im Bau ist
		$haltung_wahl='';
		if($hafen && isset($sv_bg[$system_subtyp])){
			$ss_wahl_moeglich=true;
			$ss_aktuell=$ship->getHaltung($system_subtyp);
			$optionen='';
			foreach(Haltung::alle() as $ss_wert => $ss_name){
				$optionen.='<label class="pol-option"><input type="radio" name="haltung['.intval($system_subtyp).']" value="'.$ss_wert.'"'.($ss_wert==$ss_aktuell ? ' checked' : '').'><span>'.$ss_name.'</span></label>';
			}
			$haltung_wahl='
			<div class="einh-haltung">
				<span class="mod-typ">'.($system_subtyp==2 ? 'Deine Stimme f&uuml;r die Haltung der Allianz' : 'Haltung').'</span>
				<div class="pol-optionen">'.$optionen.'</div>
			</div>';
		}

		$content.='
		<div class="einh-bg">
			<div class="einh-bg-name">'.$system_data->getSystemName().'</div>
			<div class="mod-typ">'.$sv_bg[$system_subtyp]['subname'].'</div>
			<div class="einh-bg-status">'.$status.'</div>
			'.$haltung_wahl.'
		</div>';
	}

	if($ss_wahl_moeglich){
		$content.='<div class="einh-haltung-fuss"><button type="submit" class="mod-btn">Haltungen speichern</button></div>';
	}

	$content.='</form>';

	$content.=rahmen_unten(false);

}

////////////////////////////////////////
//Ranglisten anzeigen
////////////////////////////////////////

//die einzelnen BGs durchgehen
for($i=0;$i<3;$i++){
	$content.=rahmen_oben('Battleground '.($i+1).': Gewinner',false);
	$content.='<div class="einh mod">';

	$play_typ=0; //Spieler-BG

	if($i==2){
		$play_typ=1; //Ally-BG
	}

	$zeilen='';
	if($play_typ==0){
		//Spieler-BG
		$kopf='<div class="einh-rang prod-kopf"><span class="prod-zahl">Platz</span><span>Spieler</span><span class="prod-zahl">Basisstern-Stufe</span><span class="prod-zahl">Siege</span></div>';

		//Spielerdaten laden
		$sql="SELECT * FROM `de_user_data` WHERE bgscore".$i." > 1 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_execute_query($GLOBALS['dbi'], $sql, []);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			//Schiffsdaten laden
			$ship=loadSpecialShip($row['user_id']);

			$zeilen.='<div class="einh-rang einh-platz'.min($platz, 4).'"><span class="prod-zahl">'.$platz.'</span><span class="einh-rang-name">'.$row['spielername'].'</span><span class="prod-zahl">'.$ship->ship_level.'</span><span class="prod-zahl"><b>'.$row['bgscore'.$i].'</b></span></div>';

			$platz++;
		}
	}else{
		//Allianz-BG
		$kopf='<div class="einh-rang einh-rang-ally prod-kopf"><span class="prod-zahl">Platz</span><span>Allianz</span><span class="prod-zahl">Siege</span></div>';

		//Allianzdaten laden
		$sql="SELECT * FROM de_allys WHERE bgscore".$i." > 0 ORDER BY bgscore".$i." DESC;";
		$db_data=mysqli_execute_query($GLOBALS['dbi'], $sql, []);
		$platz=1;
		while($row = mysqli_fetch_array($db_data)){
			$zeilen.='<div class="einh-rang einh-rang-ally einh-platz'.min($platz, 4).'"><span class="prod-zahl">'.$platz.'</span><span class="einh-rang-name">'.$row['allytag'].'</span><span class="prod-zahl"><b>'.$row['bgscore'.$i].'</b></span></div>';

			$platz++;
		}
	}

	if($zeilen==''){
		$content.='<div class="mod-leer">Noch keine Gewinner.</div>';
	}else{
		$content.=$kopf.'<div class="prod-liste">'.$zeilen.'</div>';
	}

	$content.='</div>';
	$content.=rahmen_unten(false);
}

include "resline.php";

echo einheiten_navi('specialship');

echo $content;


?>

<br>

</body>
</html>
