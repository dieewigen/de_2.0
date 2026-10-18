<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'inc/lang/'.$sv_server_lang.'_resline.lang.php';

//Vorschau der App-Oberfläche (Okt. 2026): Kopfleiste oben, Navigation links, Spielseite in der Mitte mit der Karte dahinter,
//Chat rechts angedockt. Dieselbe Sitzung und dieselben Haken wie dm.php: die Spielseiten laufen im Rahmen "h" in
//#iframe_main_container (ang_fn.js), resline.php schreibt Rohstoffe, Flotten, Punkte und Zeiten in die Elemente tb_*,
//der Chat lädt das Infocenter in #infocenter. Aufruf nach dem Login direkt als dm_app.php; index.php leitet weiter auf dm.php.
unset($_SESSION["de_frameset"]);
$_SESSION['new_desktop_version']=1;
$_SESSION['ic_last_refresh']=0;

//Zustand der Oberfläche aus den Cookies: Breite des Chats, Chat zugeklappt, Navigation schmal
$chat_breite = max(280, min(700, isset($_COOKIE['app_chat_breite']) ? intval($_COOKIE['app_chat_breite']) : 360));
$chat_zu = isset($_COOKIE['app_chat_zu']) && $_COOKIE['app_chat_zu'] == '1';
$nav_schmal = isset($_COOKIE['app_nav_schmal']) && $_COOKIE['app_nav_schmal'] == '1';
$vs_aktiv = empty($GLOBALS['sv_deactivate_vsystems']);

//Navigation: Seite, Text, Symbol (Sprite unten), Muster für die Markierung der offenen Seite; null ist eine Trennlinie
$app_nav = array(
	array('overview.php', '&Uuml;bersicht', 'uebersicht', '^overview\.php$'),
	array('karte', 'Karte', 'karte', ''),
	array('hyperfunk.php', $menu_lang['eintrag_2'], 'hyperfunk', '^hyperfunk\.php$'),
	array('sysnews.php', $menu_lang['eintrag_3'], 'nachrichten', '^sysnews\.php$'),
	null,
	array('ang_techs.php', $menu_lang['eintrag_36'], 'technologien', '^ang_techs\.php$'),
	array('specialization.php', 'Spezialisierung', 'spezialisierung', '^specialization\.php$'),
	array('resource.php', $menu_lang['eintrag_6'], 'ressourcen', '^resource\.php$'),
	array('artefacts.php', $menu_lang['eintrag_18'], 'artefakte', '^artefacts\.php$'),
	null,
	array('auction.php', 'Auktion', 'auktion', '^auction\.php$'),
	array('missions.php', 'Missionen', 'missionen', '^missions\.php$'),
);
if ($vs_aktiv) {
	$app_nav[] = array('map_mobile.php', 'V-Systeme', 'vsysteme', '^map_(mobile|system)\.php$');
}
$app_nav = array_merge($app_nav, array(
	null,
	array('production.php', $menu_lang['eintrag_8'], 'produktion', '^(production|recycling|unitinfo|specialship)\.php$'),
	array('military.php', 'Flotten', 'flotten', '^military\.php$'),
	array('secret.php', $menu_lang['eintrag_11'], 'geheimdienst', '^secret\.php$'),
	null,
	array('sector.php', $menu_lang['eintrag_12'], 'sektor', '^(sector|details|politics|bkmenu)\.php$'),
	array('secstatus.php', $menu_lang['eintrag_13'], 'sektorstatus', '^secstatus\.php$'),
	array('allymain.php', $menu_lang['eintrag_16'], 'allianz', '^ally'),
	null,
	array('statistics.php', $menu_lang['eintrag_21'], 'statistik', '^statistics\.php$'),
	array('toplist.php', $menu_lang['eintrag_22'], 'rangliste', '^toplist\.php$'),
	null,
	array('options.php', $menu_lang['eintrag_24'], 'optionen', '^(options|userdetails)\.php$'),
));

//ein Link der Kopfleiste oder Navigation: normaler Klick öffnet die Seite in der Mitte (app_nav_klick unten)
function app_link($seite, $klasse, $inhalt, $titel = '')
{
	return '<a href="'.($seite === 'karte' ? 'map.php' : $seite).'" class="'.$klasse.'" data-seite="'.$seite.'"'
		.($titel !== '' ? ' title="'.$titel.'" rel="tooltip"' : '')
		.' onclick="return app_nav_klick(event, \''.$seite.'\')">'.$inhalt.'</a>';
}
?>
<!doctype html>
<html lang="de-de">
  <head>
	<title><?php echo $sv_server_tag;?> - DIE EWIGEN - <?php echo $sv_server_name;?></title>
	<meta charset="utf-8">
	<?php require_once 'vendor/autoload.php'; echo \DieEwigen\DE2\View\Seitenkopf::zubehoer((int)$_SESSION['ums_rasse']); ?>
	<link rel="stylesheet" href="gp/de-main.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-main.css');?>">
	<link rel="stylesheet" href="gp/de-app.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-app.css');?>">

	<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
	<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>
  </head>
  <body class="template rasse<?php echo $_SESSION['ums_rasse']; echo $nav_schmal ? ' app-nav-schmal' : ''; echo $chat_zu ? ' app-chat-zu' : ''; ?> app" style="--app-chat-breite: <?php echo $chat_breite; ?>px">
	<?php //Symbole der Oberfläche als Sprite: Linien in der Textfarbe, 24 x 24 ?>
	<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
		<symbol id="i-uebersicht" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
		<symbol id="i-karte" viewBox="0 0 24 24"><path d="M3 6.5 9 4l6 2.5L21 4v13.5L15 20l-6-2.5L3 20z"/><path d="M9 4v13.5M15 6.5V20"/></symbol>
		<symbol id="i-hyperfunk" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.8"/><path d="M8.5 15.5a5 5 0 0 1 0-7M15.5 8.5a5 5 0 0 1 0 7M5.6 18.4a9 9 0 0 1 0-12.8M18.4 5.6a9 9 0 0 1 0 12.8"/></symbol>
		<symbol id="i-nachrichten" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol>
		<symbol id="i-technologien" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.6"/><ellipse cx="12" cy="12" rx="9" ry="3.6"/><ellipse cx="12" cy="12" rx="9" ry="3.6" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="3.6" transform="rotate(120 12 12)"/></symbol>
		<symbol id="i-spezialisierung" viewBox="0 0 24 24"><circle cx="6" cy="5" r="2"/><circle cx="18" cy="5" r="2"/><circle cx="12" cy="19" r="2"/><path d="M6 7v2a4 4 0 0 0 4 4h4a4 4 0 0 0 4-4V7M12 13v4"/></symbol>
		<symbol id="i-ressourcen" viewBox="0 0 24 24"><ellipse cx="12" cy="6" rx="7" ry="2.6"/><path d="M5 6v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V6M5 10v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4M5 14v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4"/></symbol>
		<symbol id="i-artefakte" viewBox="0 0 24 24"><path d="M7 3h10l4 6-9 12L3 9z"/><path d="M3 9h18M9.5 9 12 21l2.5-12M7 3l2.5 6M17 3l-2.5 6"/></symbol>
		<symbol id="i-auktion" viewBox="0 0 24 24"><path d="M12 3v18M6 21h12M12 6 5 8M12 6l7 2"/><path d="M2.5 14 5 8l2.5 6a2.5 2.5 0 0 1-5 0zM16.5 14 19 8l2.5 6a2.5 2.5 0 0 1-5 0z"/></symbol>
		<symbol id="i-missionen" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4"/></symbol>
		<symbol id="i-vsysteme" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><ellipse cx="12" cy="12" rx="10.5" ry="3.2" transform="rotate(-22 12 12)"/></symbol>
		<symbol id="i-produktion" viewBox="0 0 24 24"><path d="M3 21V9l5 3.5V9l5 3.5V9l5 3.5V6h3v15z"/><path d="M7 17h2M11 17h2M15 17h2"/></symbol>
		<symbol id="i-flotten" viewBox="0 0 24 24"><path d="M12 2.5c3 2.6 4.2 6.3 4.2 10.5l2.8 3v3.5l-4-1.8H9l-4 1.8V16l2.8-3C7.8 8.8 9 5.1 12 2.5z"/><circle cx="12" cy="10" r="1.6"/><path d="m10 19.5 2 2 2-2"/></symbol>
		<symbol id="i-geheimdienst" viewBox="0 0 24 24"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></symbol>
		<symbol id="i-sektor" viewBox="0 0 24 24"><path d="M12 2.5 20.2 7v10L12 21.5 3.8 17V7z"/><circle cx="12" cy="12" r="2"/></symbol>
		<symbol id="i-sektorstatus" viewBox="0 0 24 24"><path d="M12 2.5 20 5.5v6c0 4.8-3.4 8.6-8 10.5-4.6-1.9-8-5.7-8-10.5v-6z"/><path d="m8.8 12 2.2 2.2 4.4-4.4"/></symbol>
		<symbol id="i-allianz" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.4"/><circle cx="17" cy="9" r="2.6"/><path d="M2.5 19.5c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M15.5 14.2c3.2 0 6 1.8 6 5.3"/></symbol>
		<symbol id="i-statistik" viewBox="0 0 24 24"><path d="M4 20v-7M10 20V5M16 20v-10M22 20H2"/></symbol>
		<symbol id="i-rangliste" viewBox="0 0 24 24"><path d="M8 4h8v5a4 4 0 0 1-8 0zM8 6H5a3 3 0 0 0 3 3.5M16 6h3a3 3 0 0 1-3 3.5M12 13v4M9 20h6M10 17h4"/></symbol>
		<symbol id="i-optionen" viewBox="0 0 24 24"><path d="M4 7h9M19 7h1M4 17h3M13 17h7"/><circle cx="16" cy="7" r="2.2"/><circle cx="10" cy="17" r="2.2"/></symbol>
		<symbol id="i-logout" viewBox="0 0 24 24"><path d="M14 4h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-5M10 8l-4 4 4 4M6 12h11"/></symbol>
		<symbol id="i-chat" viewBox="0 0 24 24"><path d="M4 5h16v10H9l-5 4z"/></symbol>
		<symbol id="i-einklappen" viewBox="0 0 24 24"><path d="m14 6-6 6 6 6"/></symbol>
		<symbol id="i-schliessen" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></symbol>
		<symbol id="i-aktualisieren" viewBox="0 0 24 24"><path d="M20 12a8 8 0 1 1-2.3-5.7M20 4v5h-5"/></symbol>
		<symbol id="i-heimat" viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5V20H3z"/><path d="M10 20v-6h4v6"/></symbol>
		<symbol id="i-liste" viewBox="0 0 24 24"><path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01"/></symbol>
		<symbol id="i-zurueck" viewBox="0 0 24 24"><path d="M10 6 4 12l6 6M4 12h16"/></symbol>
	</svg>

	<header class="app-kopf">
		<?php
		echo app_link('overview.php', 'app-marke', '<img src="gp/g/derassenlogo'.$_SESSION['ums_rasse'].'.png" alt=""><span><b>Die Ewigen</b><small>'.$sv_server_tag.' &middot; '.htmlspecialchars($sv_server_name, ENT_QUOTES, 'UTF-8').' &middot; Vorschau</small></span>');

		//Rohstoffe, Flotten im System und Punkte; die Werte schreibt resline.php bei jedem Seitenaufruf in tb_*
		echo '<div class="app-werte">';
		for ($i = 1; $i <= 5; $i++) {
			echo app_link('resource.php', 'app-wert', '<img src="gp/g/icon'.$i.'.png" alt=""><b id="tb_res'.$i.'"></b>', $resline_lang['restipres0'.$i.'desc']);
		}
		echo app_link('secstatus.php', 'app-wert app-wert-flotte', '<img id="tb_deffer_img_grey" src="gp/g/icon6_grey.png" alt=""><img id="tb_deffer_img" src="gp/g/icon6.png" alt="" style="display: none;"><b id="tb_deffer" class="app-gut" style="display: none;"></b>', 'Verteidiger im System, Klick zum Sektorstatus');
		echo app_link('secstatus.php', 'app-wert app-wert-flotte', '<img id="tb_atter_img_grey" src="gp/g/icon7_grey.png" alt=""><img id="tb_atter_img" src="gp/g/icon7.png" alt="" style="display: none;"><b id="tb_atter" class="app-gefahr" style="display: none;"></b>', 'Angreifer im Anflug, Klick zum Sektorstatus');
		echo app_link('toplist.php', 'app-wert', '<img id="tb_score_img" src="gp/g/icon8.png" alt="" rel="tooltip"><b id="tb_score"></b>', 'Punkte');
		echo '</div>';

		//Signale: neue Hyperfunknachrichten und Nachrichten, Allianzbonus, Infocenter zu Technologien und Missionen
		echo '<div class="app-signale">';
		echo app_link('hyperfunk.php?l=new', 'app-signal', '<img src="gp/g/hyper.png" alt=""><img id="tb_hyper_img" class="app-signal-neu" src="gp/g/'.$_SESSION['ums_rasse'].'_hyper.png" alt="" style="display: none;" title="'.$resline_lang['restipnewhyperdesc'].'">', 'Hyperfunk');
		echo app_link('sysnews.php', 'app-signal', '<img src="gp/g/news.png" alt=""><img id="tb_news_img" class="app-signal-neu" src="gp/g/'.$_SESSION['ums_rasse'].'_news.png" alt="" style="display: none;" title="'.$resline_lang['restipnewnewsdesc'].'">', 'Nachrichten');
		echo app_link('ally_dailygift.php', 'app-signal app-signal-leer', '<img id="tb_daily_img" class="rounded-borders pulse-icon" src="gp/g/icon15.png" alt="" style="display: none;">', $resline_lang['dailyallygiftdesc']);
		echo app_link('ang_techs.php', 'app-signal app-signal-leer', '<img id="tb_infocenter_technology" class="rounded-borders" src="gp/g/icon16.png" alt="" style="display: none;" rel="tooltip">');
		echo app_link('missions.php', 'app-signal app-signal-leer', '<img id="tb_infocenter_missions" class="rounded-borders" src="gp/g/icon14.png" alt="" style="display: none;" rel="tooltip">');
		echo '</div>';

		//Serverzeit, letzter WT und KT (füllt resline.php), Klick zu den Serverinfos; der Schalter oben wechselt zum
		//Countdown bis zum nächsten Tick (dm_zeit_umschalten unten, wie in dm.php)
		echo '<div onclick="switch_iframe_main_container(\'sinfo.php\'); app_nav_markieren(\'sinfo.php\');" class="dm-zeiten app-zeiten" title="Serverinfos" rel="tooltip">
				<span class="mod-typ">Zeit</span><b id="tb_time1"></b>
				<span class="mod-typ">WT</span><b id="tb_time2"></b>
				<span class="mod-typ">KT</span><b id="tb_time3"></b>
				<span class="dm-zeiten-modus" onclick="dm_zeit_umschalten(event)" title="Countdown bis zum n&auml;chsten Tick ein- oder ausschalten"></span>
			</div>';
		?>
		<button type="button" class="app-kopf-knopf app-kopf-knopf-chat" onclick="app_chat_umschalten()" title="Chat ein- oder ausblenden"><svg class="app-ico"><use href="#i-chat"/></svg></button>
	</header>

	<nav class="app-nav">
		<?php
		foreach ($app_nav as $punkt) {
			if ($punkt === null) {
				echo '<hr>';
				continue;
			}
			[$seite, $text, $symbol, $muster] = $punkt;
			echo '<a href="'.($seite === 'karte' ? 'map.php' : $seite).'" class="app-nav-punkt" data-seite="'.$seite.'" data-muster="'.$muster.'" onclick="return app_nav_klick(event, \''.$seite.'\')"><svg class="app-ico"><use href="#i-'.$symbol.'"/></svg><span class="app-nav-text">'.$text.'</span><em class="app-nav-hinweis"></em></a>';
		}
		?>
		<div class="app-nav-fuss">
			<?php //Rückweg für den Spielertest: die bisherige Desktop-Ansicht ?>
			<a href="dm.php" target="_top" class="app-nav-punkt" title="Zur bisherigen Desktop-Ansicht"><svg class="app-ico"><use href="#i-zurueck"/></svg><span class="app-nav-text">Bisherige Ansicht</span></a>
			<a href="index.php?logout=1" target="_top" class="app-nav-punkt app-nav-gefahr"><svg class="app-ico"><use href="#i-logout"/></svg><span class="app-nav-text"><?php echo $menu_lang['eintrag_29']; ?></span></a>
			<button type="button" class="app-nav-punkt app-nav-schalter" onclick="app_nav_umschalten()" title="Navigation schmal oder breit"><svg class="app-ico"><use href="#i-einklappen"/></svg><span class="app-nav-text">Einklappen</span></button>
		</div>
	</nav>

	<main class="app-inhalt">
		<?php //die Karte liegt immer hinter der Spielseite; "Karte" in der Navigation oder Esc schließt die Seite (closeIframeMain) ?>
		<iframe src="map.php" id="iframe_map" title="Karte"></iframe>
		<nav class="app-karte-knoepfe">
			<button type="button" class="app-karte-knopf" onclick="document.getElementById('iframe_map').contentWindow.location.reload()"><svg class="app-ico"><use href="#i-aktualisieren"/></svg>Karte aktualisieren</button>
			<?php if ($vs_aktiv) { echo app_link('map_mobile.php', 'app-karte-knopf', '<svg class="app-ico"><use href="#i-liste"/></svg>VS-&Uuml;bersicht', 'Vergessene Systeme (VS) als Liste'); } ?>
			<button type="button" class="app-karte-knopf" onclick="reset_map()" title="Karte auf den Heimatsektor zentrieren"><svg class="app-ico"><use href="#i-heimat"/></svg>Heimatsektor</button>
		</nav>
		<div id="iframe_main_container"><iframe src="overview.php" id="iframe_main" name="h" title="Spielseite"></iframe></div>
		<?php //bleibt für ang_fn.js vorhanden; Technologien laufen in der App in der normalen Mitte (switch_iframe_main_container_big unten) ?>
		<div id="iframe_main_container_big" style="display: none;"></div>
	</main>

	<aside class="app-chat">
		<div class="app-chat-kopf"><svg class="app-ico"><use href="#i-chat"/></svg><span>Chat</span><button type="button" class="app-chat-zu-knopf" onclick="app_chat_umschalten()" title="Chat ausblenden"><svg class="app-ico"><use href="#i-schliessen"/></svg></button></div>
		<iframe src="chat.php" id="iframe_chat" name="c" title="Chat"></iframe>
		<div class="app-chat-griff" title="Breite des Chats &auml;ndern"></div>
	</aside>

	<?php //unsichtbares Div, in das der Chat (de_ajaxrpc.php) die Scripte für das Infocenter lädt ?>
	<div id="infocenter"></div>

<script type="text/javascript">
//Ziehen mit der Maus über den iframes (Breite des Chats): eine Fläche über der ganzen Seite fängt die Bewegung, weil die
//iframes sie sonst schlucken; Loslassen übernimmt, Esc (dm_esc) stellt den alten Stand her
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

//Chat rechts: ein- und ausblenden (Cookie app_chat_zu), Breite über den Griff am linken Rand (Cookie app_chat_breite)
function app_chat_umschalten(){
	var zu = !$('body').hasClass('app-chat-zu');
	$('body').toggleClass('app-chat-zu', zu);
	setCookie('app_chat_zu', zu ? '1' : '0');
}

function app_chat_breite(){
	return parseInt(getComputedStyle(document.body).getPropertyValue('--app-chat-breite'), 10) || 360;
}

function app_chat_start(e){
	var alt = app_chat_breite();
	dm_ziehen_start(e, 'ew-resize', {
		bewegen: function(e){
			document.body.style.setProperty('--app-chat-breite', Math.min(700, Math.max(280, window.innerWidth - e.clientX)) + 'px');
		},
		ende: function(speichern){
			if(speichern){
				setCookie('app_chat_breite', app_chat_breite());
			}else{
				document.body.style.setProperty('--app-chat-breite', alt + 'px');
			}
		}
	});
}

//Navigation links: schmal (nur Symbole) oder breit (Cookie app_nav_schmal); schmal wandert die Beschriftung in den Tooltip
function app_nav_umschalten(){
	var schmal = !$('body').hasClass('app-nav-schmal');
	$('body').toggleClass('app-nav-schmal', schmal);
	setCookie('app_nav_schmal', schmal ? '1' : '0');
	app_nav_titel(schmal);
}

function app_nav_titel(schmal){
	$('.app-nav-punkt').each(function(){
		if(schmal){
			$(this).attr('title', $(this).find('.app-nav-text').text());
		}else{
			$(this).removeAttr('title');
		}
	});
}

//Technologien brauchen in der App kein eigenes großes Fenster, die Mitte ist groß genug (ersetzt die Fassung aus ang_fn.js)
function switch_iframe_main_container_big(file){
	switch_iframe_main_container(file);
}

//Klick in Kopfleiste oder Navigation: normaler Klick öffnet die Seite in der Mitte, "karte" schließt die Seite und zeigt die
//Karte; Strg-, Umschalt- oder Mittelklick lassen den Link wie gewohnt in einem neuen Tab aufgehen
function app_nav_klick(e, seite){
	if(e.ctrlKey || e.metaKey || e.shiftKey || e.button!==0){
		return true;
	}
	if(seite === 'karte'){
		closeIframeMain();
	}else{
		switch_iframe_main_container(seite);
	}
	app_nav_markieren(seite);
	return false;
}

//markiert den Punkt der offenen Seite (Muster auf den Dateinamen); ist keine Seite offen, die Karte. Läuft alle 500 ms, so
//stimmt die Markierung auch nach Links innerhalb der Seiten; während eine Seite lädt (about:blank) bleibt sie stehen.
function app_nav_markieren(seite){
	if(seite===undefined){
		seite='karte';
		if($('#iframe_main_container').is(':visible')){
			seite='';
			try{
				seite=$('#iframe_main')[0].contentWindow.location.pathname.split('/').pop();
			}catch(err){}
			if(seite==='blank'){
				return;
			}
		}
	}
	seite = seite.split('?')[0];
	$('.app-nav-punkt[data-muster]').each(function(){
		var muster=$(this).attr('data-muster');
		var aktiv = seite==='karte' ? $(this).attr('data-seite')==='karte' : (muster!=='' && new RegExp(muster).test(seite));
		$(this).toggleClass('app-aktiv', aktiv);
	});
}

//Hinweise an den Navigationspunkten spiegeln die Signale der Kopfleiste, die resline.php und das Infocenter schalten;
//Signalfelder ohne sichtbares Bild werden ausgeblendet
function app_hinweise(){
	var sichtbar = function(id){ return $(id).is(':visible'); };
	var puls = function(id){ return sichtbar(id) && $(id).hasClass('pulse-icon'); };
	var hinweise = {
		'hyperfunk.php': sichtbar('#tb_hyper_img') ? ['app-neu', 'neu'] : null,
		'sysnews.php': sichtbar('#tb_news_img') ? ['app-neu', 'neu'] : null,
		'secstatus.php': sichtbar('#tb_atter_img') ? ['app-gefahr', 'Angriff'] : (sichtbar('#tb_deffer_img') ? ['app-gut', 'Verteidiger'] : null),
		'allymain.php': sichtbar('#tb_daily_img') ? ['app-gut', 'Geschenk'] : null,
		'missions.php': puls('#tb_infocenter_missions') ? ['app-warn', 'offen'] : null,
		'ang_techs.php': puls('#tb_infocenter_technology') ? ['app-warn', 'nichts läuft'] : null
	};
	$('.app-nav-punkt[data-seite]').each(function(){
		var h = hinweise[$(this).attr('data-seite')] || null;
		$(this).removeClass('app-neu app-gut app-warn app-gefahr');
		if(h){
			$(this).addClass(h[0]);
		}
		$(this).find('.app-nav-hinweis').text(h ? h[1] : '');
	});
	$('.app-signal-leer').each(function(){
		$(this).toggle($(this).find('img').css('display') !== 'none');
	});
}

//Esc aus den Spielseiten und der Karte (de_fn.js, map.php): laufendes Ziehen abbrechen, sonst die Spielseite schließen
function dm_esc(){
	if(dm_zug){
		dm_ziehen_ende(false);
		return;
	}
	closeIframeMain();
	app_nav_markieren();
}

$(document).ready(function() {
	$('.app-chat-griff').on('mousedown', app_chat_start);
	$(document).on('mousemove', '#resize-overlay', function(e){ if(dm_zug){ dm_zug.bewegen(e); } });
	$(document).on('mouseup', function(){ dm_ziehen_ende(true); });
	app_nav_titel($('body').hasClass('app-nav-schmal'));
	app_hinweise();
});
window.setInterval(function(){ app_nav_markieren(); app_hinweise(); }, 500);

//Countdown bis zum nächsten WT und KT statt der letzten Tickzeiten (wie in dm.php): Schalter oben in .dm-zeiten, Stellung in
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
