<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'inc/lang/'.$sv_server_lang.'_resline.lang.php';

unset($_SESSION["de_frameset"]);

//zeigt an, dass die neue Desktopversion verwendet wird, wird z.B. für das Infocenter benötigt
$_SESSION['new_desktop_version']=1;
$_SESSION['ic_last_refresh']=0;

//Chat-Popup: Größe und Zustand (zugeklappt) aus den Cookies, nur Zahlen, die Werte landen im Markup
$chat_width = max(250, isset($_COOKIE['chat_width']) ? intval($_COOKIE['chat_width']) : 400);
$chat_height = max(250, isset($_COOKIE['chat_height']) ? intval($_COOKIE['chat_height']) : 400);
$chat_zu = isset($_COOKIE['chat_zu']) && $_COOKIE['chat_zu'] == '1';
//Breite der Spielspalte aus dem Cookie (Griff am rechten Rand, dm_spalte_start unten); ohne Cookie gelten die 620 px aus gp/de-dm.scss
$spalte_breite = isset($_COOKIE['spalte_breite']) ? min(1400, intval($_COOKIE['spalte_breite'])) : 0;

?>
<!doctype html>
<html lang="de-de">
  <head>
    
    <title><?php echo $sv_server_tag;?> - DIE EWIGEN - <?php echo $sv_server_name;?></title>
	<meta charset="utf-8">
	<?php require_once 'vendor/autoload.php'; echo \DieEwigen\DE2\View\Seitenkopf::zubehoer((int)$_SESSION['ums_rasse']); ?>
	<link rel="stylesheet" href="gp/de-main.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-main.css');?>">
	<?php //Optik der Desktop-Ansicht über dem Grundstil, die Spielseiten laden sie über cssinclude.php ?>
	<link rel="stylesheet" href="gp/de-dm.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-dm.css');?>">
	
	<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
	<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>

  </head>
  <body class="template rasse<?php echo $_SESSION['ums_rasse'];?>"<?php echo $spalte_breite >= 620 ? ' style="--dm-spalte: '.$spalte_breite.'px"' : ''; ?>>
	<div style="position: absolute; width: 100%; height: 100%; left: 0px; top:0px;"><iframe src="map.php" id="iframe_map" height="100%" width="100%" frameBorder="0"></iframe></div>
	<div id="iframe_main_container" style="position: absolute; height: calc(100% - 64px); left: 0px; top:64px;"><iframe src="overview.php" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe></div>
	<?php //Schließen-Knopf an der Spielspalte (Optik in de-main.scss), die Esc-Taste tut dasselbe (dm_esc unten) ?>
	<button type="button" id="iframe_main_container_closer" class="dm-knopf" data-icon="✕" onclick="closeIframeMain()" title="Spielseite schlie&szlig;en (Esc)"></button>
	<?php //Griff am rechten Rand der Spielspalte: Ziehen ändert ihre Breite, Doppelklick stellt die Standardbreite her (dm_spalte_start unten, Optik in gp/de-dm.scss) ?>
	<div id="dm_spalte_griff" title="Breite der Spielspalte &auml;ndern (Doppelklick: Standardbreite)"></div>

	<div id="iframe_main_container_big" style="position: absolute; display: none; width: 100%; height: calc(100% - 64px); left: 0px; top:64px; z-index: 100;"></div>

	<?php //Chat-Popup: der Kopf klappt ein und aus, Ränder und Griff ändern die Größe (Script unten, Optik in de-main.scss) ?>
	<div id="chat_popup"<?php echo $chat_zu ? ' class="dm-chat-zu"' : ''; ?> style="width: <?php echo $chat_width; ?>px; height: <?php echo $chat_height; ?>px;">
		<div id="chat_header" data-icon="💬" onclick="dm_chat_umschalten()" title="Chat ein- oder ausklappen">
			<span class="dm-chat-titel">Chat</span>
			<span class="dm-chat-pfeil" aria-hidden="true"></span>
		</div>
		<iframe src="chat.php" id="iframe_chat" name="c" width="100%" frameBorder="0"></iframe>
		<div class="dm-chat-rand dm-chat-rand-n" data-richtung="n"></div>
		<div class="dm-chat-rand dm-chat-rand-w" data-richtung="w"></div>
		<div class="dm-chat-griff" data-richtung="nw" title="Gr&ouml;&szlig;e &auml;ndern"></div>
	</div>
	
	<div id="topbar" style="z-index: 1000;">
		<?php 
		//Rassenlogo: Klick zur Übersicht, beim Überfahren das Aufklappmenü im Look der Menüleiste
		//(Sektor und Optionen wie die Reiter: normaler Klick in der Spielspalte, Mittel- oder Strg-Klick im neuen Tab)
		echo '
		<div class="dropdown">
			<img src="gp/g/derassenlogo'.$_SESSION['ums_rasse'].'.png" style="position: absolute; left: -31px; top: -1px; width: auto; height: 72px; cursor: pointer;" onclick="switch_iframe_main_container(\'overview.php\')">
			<nav class="dropdown-content dm-aufklapp">
				<a href="sector.php" class="dm-aufklapp-punkt" data-icon="⌬" onclick="return dm_menu_klick(event, \'sector.php\', false)">'.$menu_lang['eintrag_12'].'</a>
				<a href="options.php" class="dm-aufklapp-punkt" data-icon="⚙" onclick="return dm_menu_klick(event, \'options.php\', false)">'.$menu_lang['eintrag_24'].'</a>
				<a href="index.php?logout=1" class="dm-aufklapp-punkt dm-aufklapp-gefahr" data-icon="🚪">'.$menu_lang['eintrag_29'].'</a>
			</nav>
		</div>';

		//Rohstoffe/Credits
		//Multiplex
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="gp/g/icon1.png" class="rounded-borders" style="cursor: pointer; position: absolute; left: 40px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres01desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res1" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 66px;" rel="tooltip"></div>';

		//Dyharra
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="gp/g/icon2.png" class="rounded-borders" style="cursor: pointer; position: absolute; left: 140px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres02desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res2" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 166px;" rel="tooltip"></div>';
		
		//Iradium
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="gp/g/icon3.png" class="rounded-borders" style="cursor: pointer; position: absolute; left: 240px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres03desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res3" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 266px;" rel="tooltip"></div>';
		
		//Eternium
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="gp/g/icon4.png" class="rounded-borders" style="cursor: pointer; position: absolute; left: 340px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres04desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res4" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 366px;" rel="tooltip"></div>';
		
		//Tronic
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="gp/g/icon5.png" class="rounded-borders" style="cursor: pointer; position: absolute; left: 440px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres05desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res5" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 466px;" rel="tooltip"></div>';

		//Deffer
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer_img_grey" src="gp/g/icon6_grey.png" class="rounded-borders" style="display: none; cursor: pointer; position: absolute; left: 40px; top: 36px; width: 24px; height: auto;" title="zum Sektorstatus" rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer_img" src="gp/g/icon6.png" class="rounded-borders" style="display: none; cursor: pointer; position: absolute; left: 40px; top: 36px; width: 24px; height: auto;" title="Du wirst von diesen Einheiten verteidigt." rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" class="topbar_textfield" style="cursor: pointer; top: 39px; left: 66px;" title="zum Sektorstatus" rel="tooltip">&nbsp;</div>';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer" class="topbar_textfield" style="color: rgba(40,112,53,1); display: none; cursor: pointer; top: 39px; left: 66px;" rel="tooltip"></div>';
		
		//Atter
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter_img_grey" src="gp/g/icon7_grey.png" class="rounded-borders" style="display: none; cursor: pointer; position: absolute; left: 140px; top: 36px; width: 24px; height: auto;" title="zum Sektorstatus" rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter_img" src="gp/g/icon7.png" class="rounded-borders" style="animation: shake 0.5s; display: none; cursor: pointer; position: absolute; left: 140px; top: 36px; width: 24px; height: auto;" title="Du wirst von diesen Einheiten angegriffen." rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" class="topbar_textfield" style="cursor: pointer; top: 39px; left: 166px;" title="zum Sektorstatus" rel="tooltip">&nbsp;</div>';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter" class="topbar_textfield" style="color: rgba(215,45,45,1); display: none; cursor: pointer; top: 39px; left: 166px;" rel="tooltip"></div>';

		//Punkte
		echo '<img onclick="switch_iframe_main_container(\'toplist.php\')" id="tb_score_img" src="gp/g/icon8.png" class="rounded-borders" style="cursor: pointer;position: absolute; left: 240px; top: 36px; width: 24px; height: auto;" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'toplist.php\')" id="tb_score" class="topbar_textfield" style="cursor: pointer; top: 39px; left: 266px;" rel="tooltip"></div>';

		//Hyperfunk
		echo '<img onclick="switch_iframe_main_container(\'hyperfunk.php\')" src="gp/g/hyper.png" style="cursor: pointer; position: absolute; left: 440px; top: 36px; width: 40px; height: auto;" title="Es liegen keine neuen Hyperfunknachrichten vor." rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'hyperfunk.php?l=new\')" id="tb_hyper_img" src="gp/g/'.$_SESSION['ums_rasse'].'_hyper.png" style="display: none;  cursor: pointer; position: absolute; left: 440px; top: 36px; width: 40px; height: auto;" title="'.$resline_lang['restipnewhyperdesc'].'" rel="tooltip">';
		
		//Nachrichten
		echo '<img onclick="switch_iframe_main_container(\'sysnews.php\')" src="gp/g/news.png" style="cursor: pointer; position: absolute; left: 490px; top: 36px; width: 40px; height: auto;" title="Es liegen keine neuen Nachrichten vor." rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'sysnews.php\')" id="tb_news_img" src="gp/g/'.$_SESSION['ums_rasse'].'_news.png" style="display: none; cursor: pointer; position: absolute; left: 490px; top: 36px; width: 40px; height: auto;" title="'.$resline_lang['restipnewnewsdesc'].'" rel="tooltip">';
		
		//daily gift
		echo '<img onclick="switch_iframe_main_container(\'ally_dailygift.php\')" id="tb_daily_img" src="gp/g/icon15.png" class="rounded-borders pulse-icon" style="display: none; cursor: pointer; position: absolute; left: 340px; top: 36px; width: 24px; height: auto;" title="'.$resline_lang['dailyallygiftdesc'].'" rel="tooltip">';

		//infocenter Technologien
		echo '<img onclick="switch_iframe_main_container_big(\'ang_techs.php\')" id="tb_infocenter_technology" src="gp/g/icon16.png" class="rounded-borders" style="display: none; cursor: pointer; position: absolute; left: 373px; top: 36px; width: 24px; height: auto;" rel="tooltip">';
		
		//infocenter Missionen
		echo '<img onclick="switch_iframe_main_container(\'missions.php\')" id="tb_infocenter_missions" src="gp/g/icon14.png" class="rounded-borders" style="display: none; cursor: pointer; position: absolute; left: 406px; top: 36px; width: 24px; height: auto;" rel="tooltip">'; 


		//Serverzeit, letzter WT und KT (füllt resline.php), Klick zu den Serverinfos; der Punkt rechts oben schaltet auf den
		//Countdown bis zum nächsten WT und KT um (dm_zeit_umschalten unten)
		echo '<div onclick="switch_iframe_main_container(\'sinfo.php\')" class="dm-zeiten" title="Serverinfos" rel="tooltip">
				<span class="mod-typ">Zeit</span><b id="tb_time1"></b>
				<span class="mod-typ">WT</span><b id="tb_time2"></b>
				<span class="mod-typ">KT</span><b id="tb_time3"></b>
				<span class="dm-zeiten-modus" onclick="dm_zeit_umschalten(event)" title="Countdown bis zum n&auml;chsten Tick ein- oder ausschalten"></span>
			</div>';
		
		//Menüpunkte als Reiter: ein normaler Klick öffnet die Seite in der Spielspalte (Technologien im großen Fenster),
		//Mittel- oder Strg-Klick wie ein Link in einem neuen Tab; markiert wird die Seite, die gerade offen ist
		//(dm_menu_markieren() unten, Muster auf den Dateinamen)
		//Symbole wie im Mobilmenü (menu.php)
		$dm_menu = array(
			array('ang_techs.php', $menu_lang['eintrag_36'], '^ang_techs\.php$', true, '⚛'),
			array('specialization.php', 'Spezialisierung', '^specialization\.php$', false, '🧬'),
			array('artefacts.php', $menu_lang['eintrag_18'], '^artefacts\.php$', false, '✧'),
			array('auction.php', 'Auktion', '^auction\.php$', false, '⚖'),
			array('missions.php', 'Missionen', '^missions\.php$', false, '✪'),
			array('production.php', 'Produktion', '^production\.php$', false, '🏭'),
			array('military.php', 'Flotten', '^military\.php$', false, '🚀'),
			array('secret.php', $menu_lang['eintrag_11'], '^secret\.php$', false, '🕵️'),
			array('allymain.php', $menu_lang['eintrag_16'], '^ally', false, '∞'),
			array('statistics.php', $menu_lang['eintrag_21'], '^statistics\.php$', false, '📊'),
			array('toplist.php', $menu_lang['eintrag_22'], '^toplist\.php$', false, '★'),
		);
		echo '<nav class="dm-menu">';
		foreach ($dm_menu as [$seite, $text, $muster, $gross, $symbol]) {
			echo '<a href="'.$seite.'" class="dm-reiter" data-icon="'.$symbol.'" data-muster="'.$muster.'" onclick="return dm_menu_klick(event, \''.$seite.'\', '.($gross ? 'true' : 'false').')"><span class="dm-reiter-text">'.$text.'</span></a>';
		}
		echo '</nav>
		</div>';

		////////////////////////////////////////////////////////
		//Infocenter
		////////////////////////////////////////////////////////
		//unsichtbares Div, in das der Chat (de_ajaxrpc.php) die Scripte für das Infocenter lädt
		echo '<div id="infocenter"></div>';

		////////////////////////////////////////////////////////
		//Knöpfe rechts oben auf der Karte, Kacheln wie das Menü (Optik in de-main.scss)
		////////////////////////////////////////////////////////
		//der breiteste Knopf steht oben, darunter werden sie schmaler
		echo '<nav class="dm-karte-knoepfe">';
		echo '<button type="button" class="dm-knopf" data-icon="↻" onclick="document.getElementById(\'iframe_map\').contentWindow.location.reload()">Karte aktualisieren</button>';
		if (empty($GLOBALS['sv_deactivate_vsystems'])) {
			echo '<a href="map_mobile.php" class="dm-knopf" data-icon="✸" onclick="return dm_menu_klick(event, \'map_mobile.php\', false)" title="Vergessene Systeme (VS) als Liste">VS-&Uuml;bersicht</a>';
		}
		echo '<button type="button" class="dm-knopf" data-icon="⌂" onclick="reset_map()" title="Karte auf den Heimatsektor zentrieren">Heimatsektor</button>';
		echo '</nav>';

	?>
<script type="text/javascript">
//Chat ein- und ausklappen (Cookie chat_zu); zugeklappt bleibt nur der Kopf sichtbar, der Chat läuft weiter
function dm_chat_umschalten(){
	var zu = !$('#chat_popup').hasClass('dm-chat-zu');
	$('#chat_popup').toggleClass('dm-chat-zu', zu);
	setCookie('chat_zu', zu ? '1' : '0');
}

//Ziehen mit der Maus über den iframes (Größe des Chats, Breite der Spielspalte): eine Fläche über der ganzen Seite fängt
//die Bewegung, weil die iframes sie sonst schlucken; Loslassen übernimmt, Esc (dm_esc) stellt den alten Stand her
var dm_zug = null;

function dm_ziehen_start(e, cursor, zug){
	dm_zug = zug;
	$('iframe').css('pointer-events', 'none');
	$('body').addClass('dm-zieht').css('user-select', 'none').append('<div id="resize-overlay" style="position: fixed; inset: 0; z-index: 9999; cursor: ' + cursor + ';"></div>');
	e.preventDefault();
}

function dm_ziehen_ende(speichern){
	if(!dm_zug){
		return;
	}
	dm_zug.ende(speichern);
	dm_zug = null;
	$('#resize-overlay').remove();
	$('iframe').css('pointer-events', '');
	$('body').removeClass('dm-zieht').css('user-select', '');
}

//Größe des Chats über die Ränder oben und links und den Griff in der Ecke (Cookies chat_width, chat_height)
function dm_chat_resize_start(e){
	var popup = $('#chat_popup');
	var richtung = $(this).attr('data-richtung'), x = e.clientX, y = e.clientY, breite = popup.outerWidth(), hoehe = popup.outerHeight();
	dm_ziehen_start(e, $(this).css('cursor'), {
		bewegen: function(e){
			var b = breite, h = hoehe;
			if(richtung !== 'n'){
				b = Math.max(250, breite + x - e.clientX);
			}
			if(richtung !== 'w'){
				h = Math.max(200, hoehe + y - e.clientY);
			}
			popup.css({width: b + 'px', height: h + 'px'});
		},
		ende: function(speichern){
			if(speichern){
				setCookie('chat_width', popup.outerWidth() + 'px');
				setCookie('chat_height', popup.outerHeight() + 'px');
			}else{
				popup.css({width: breite + 'px', height: hoehe + 'px'});
			}
		}
	});
}

//Breite der Spielspalte: Griff am rechten Rand (Cookie spalte_breite, Standardbreite in gp/de-dm.scss), Doppelklick stellt
//die Standardbreite her; für Karte und Chat bleiben rechts immer 320 px frei
function dm_spalte_breite(){
	return $('#iframe_main_container').outerWidth();
}

function dm_spalte_max(){
	return Math.max(620, window.innerWidth - 320);
}

function dm_spalte_setzen(breite){
	document.body.style.setProperty('--dm-spalte', breite + 'px');
}

function dm_spalte_start(e){
	var alt = dm_spalte_breite();
	dm_ziehen_start(e, 'ew-resize', {
		bewegen: function(e){
			dm_spalte_setzen(Math.min(dm_spalte_max(), Math.max(620, e.clientX)));
		},
		ende: function(speichern){
			if(speichern){
				setCookie('spalte_breite', dm_spalte_breite());
			}else{
				dm_spalte_setzen(alt);
			}
		}
	});
}

function dm_spalte_standard(){
	document.body.style.removeProperty('--dm-spalte');
	setCookie('spalte_breite', '');
}

//eine gespeicherte Breite darf das Fenster nicht sprengen, etwa auf einem kleineren Monitor
function dm_spalte_pruefen(){
	if(dm_spalte_breite() > dm_spalte_max()){
		dm_spalte_setzen(dm_spalte_max());
	}
}

//Esc schließt die Spielspalte bzw. das große Fenster; läuft gerade ein Ziehen (Chat, Spielspalte), bricht es nur dieses ab.
//Die Spielseiten und die Karte reichen die Taste aus ihren iframes hierher weiter (de_fn.js, map.php).
function dm_esc(){
	if(dm_zug){
		dm_ziehen_ende(false);
		return;
	}
	closeIframeMain();
}

//unter 1280 px zeigen die Reiter nur ihr Symbol (de-main.scss), die Beschriftung wandert in den Tooltip
var dm_schmal = window.matchMedia('(max-width: 1279px)');

function dm_menu_schmal(schmal){
	$('.dm-reiter').each(function(){
		if(schmal){
			$(this).attr('title', $(this).find('.dm-reiter-text').text());
		}else{
			$(this).removeAttr('title');
		}
	});
	if(schmal){
		setTooltip();
	}
}

$(document).ready(function() {
	$('.dm-chat-rand, .dm-chat-griff').on('mousedown', dm_chat_resize_start);
	$('#dm_spalte_griff').on('mousedown', dm_spalte_start).on('dblclick', dm_spalte_standard);
	$(document).on('mousemove', '#resize-overlay', function(e){ if(dm_zug){ dm_zug.bewegen(e); } });
	$(document).on('mouseup', function(){ dm_ziehen_ende(true); });
	$(window).on('resize', dm_spalte_pruefen);
	dm_spalte_pruefen();
	dm_schmal.addEventListener('change', function(e){ dm_menu_schmal(e.matches); });
	dm_menu_schmal(dm_schmal.matches);
});

//Menü: normaler Klick öffnet die Seite in der Spielspalte, Strg-/Umschalt-Klick wie ein Link (der Mittelklick löst kein click aus)
function dm_menu_klick(e, seite, gross){
	if(e.ctrlKey || e.metaKey || e.shiftKey || e.button!==0){
		return true;
	}
	if(gross){
		switch_iframe_main_container_big(seite);
	}else{
		switch_iframe_main_container(seite);
	}
	dm_menu_markieren(seite);
	return false;
}

//markiert den Reiter der Seite, die gerade in der Spielspalte bzw. im großen Fenster steht; so stimmt die Markierung
//auch nach Links innerhalb der Seiten oder Klicks auf der Karte. Während eine Seite lädt (about:blank), bleibt sie stehen.
function dm_menu_markieren(seite){
	if(seite===undefined){
		var feld=$('#iframe_main_container_big').is(':visible') ? $('#iframe_main_big') : ($('#iframe_main_container').is(':visible') ? $('#iframe_main') : $());
		seite='';
		try{
			if(feld.length){
				seite=feld[0].contentWindow.location.pathname.split('/').pop();
			}
		}catch(err){}
		if(seite==='blank'){
			return;
		}
	}
	$('.dm-reiter').each(function(){
		$(this).toggleClass('dm-reiter-aktiv', seite!=='' && new RegExp($(this).attr('data-muster')).test(seite));
	});
}
window.setInterval(function(){ dm_menu_markieren(); }, 500);

//Countdown bis zum nächsten WT und KT statt der letzten Tickzeiten: Schalter rechts oben in .dm-zeiten, Stellung in
//localStorage (de_zeitmodus). Uhr und Ticks laufen nach der Serverzeit: resline.php schreibt sie bei jedem Seitenaufruf
//in data-jetzt, der Tickplan ist $wts/$kts aus sv.inc.php (je Stunde die Minuten), tz der Versatz der Serveruhr zu UTC
<?php
$dm_tickplan = array('wt' => array(), 'kt' => array(), 'tz' => (int)date('Z'));
for ($h = 0; $h <= 23; $h++) {
	$dm_tickplan['wt'][] = array_map('intval', $GLOBALS['wts'][$h] ?? array());
	$dm_tickplan['kt'][] = array_map('intval', $GLOBALS['kts'][$h] ?? array());
}
echo 'var dm_tickplan = '.json_encode($dm_tickplan).';';
?>

var dm_zeit_sync = '', dm_zeit_versatz = 0;

function dm_zweistellig(n){
	return (n < 10 ? '0' : '') + n;
}

//nächster planmäßiger Tick nach der Serverzeit t (Sekunden), 0 ohne Tick im Plan; wie Model\Tick\Plan::naechster()
function dm_naechster_tick(plan, t){
	var start = Math.floor(t / 60) * 60 + 60;
	for(var i = 0; i < 24 * 60; i++){
		var d = new Date((start + i * 60 + dm_tickplan.tz) * 1000);
		if(plan[d.getUTCHours()] && plan[d.getUTCHours()].indexOf(d.getUTCMinutes()) >= 0){
			return start + i * 60;
		}
	}
	return 0;
}

function dm_zeit_rest(id, rest){
	var el = document.getElementById(id);
	el.textContent = rest > 0 ? Math.floor(rest / 60) + ':' + dm_zweistellig(rest % 60) : '–';
	//letzte Minute vor dem Tick blinkt
	el.classList.toggle('dm-zeit-bald', rest > 0 && rest <= 60);
}

function dm_zeit_tick(){
	var feld = document.querySelector('.dm-zeiten');
	var jetzt = feld.getAttribute('data-jetzt');
	if(!feld.classList.contains('dm-zeiten-countdown') || !jetzt){
		return;
	}
	if(jetzt !== dm_zeit_sync){
		//resline.php hat die Serverzeit neu geschrieben: Versatz zur Browseruhr neu bestimmen
		dm_zeit_sync = jetzt;
		dm_zeit_versatz = parseInt(jetzt, 10) - Math.floor(Date.now() / 1000);
	}
	var t = Math.floor(Date.now() / 1000) + dm_zeit_versatz;
	var d = new Date((t + dm_tickplan.tz) * 1000);
	document.getElementById('tb_time1').textContent = dm_zweistellig(d.getUTCHours()) + ':' + dm_zweistellig(d.getUTCMinutes());
	dm_zeit_rest('tb_time2', dm_naechster_tick(dm_tickplan.wt, t) - t);
	dm_zeit_rest('tb_time3', dm_naechster_tick(dm_tickplan.kt, t) - t);
}

function dm_zeit_umschalten(e){
	e.stopPropagation();
	var feld = document.querySelector('.dm-zeiten');
	var an = !feld.classList.contains('dm-zeiten-countdown');
	feld.classList.toggle('dm-zeiten-countdown', an);
	try{
		localStorage.setItem('de_zeitmodus', an ? '1' : '0');
	}catch(err){}
	if(an){
		dm_zeit_tick();
		return;
	}
	//zurück zu den Tickzeiten, die resline.php zuletzt geliefert hat
	var uhr = (feld.getAttribute('data-uhr') || '').split(' ');
	['tb_time1', 'tb_time2', 'tb_time3'].forEach(function(id, i){
		var el = document.getElementById(id);
		el.textContent = uhr[i] || '';
		el.classList.remove('dm-zeit-bald');
	});
}

try{
	if(localStorage.getItem('de_zeitmodus') === '1'){
		document.querySelector('.dm-zeiten').classList.add('dm-zeiten-countdown');
	}
}catch(err){}
window.setInterval(dm_zeit_tick, 1000);
</script>
</body>
</html>
