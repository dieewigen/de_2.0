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

?>
<!doctype html>
<html lang="de-de">
  <head>
    
    <title><?php echo $sv_server_tag;?> - DIE EWIGEN - <?php echo $sv_server_name;?></title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" href="gp/de-main.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-main.css');?>">	
	
	<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
	<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>

  </head>
  <body class="template rasse<?php echo $_SESSION['ums_rasse'];?>">
	<div style="position: absolute; width: 100%; height: 100%; left: 0px; top:0px;"><iframe src="map.php" id="iframe_map" height="100%" width="100%" frameBorder="0"></iframe></div>
	<div id="iframe_main_container" style="position: absolute; width: 620px; height: calc(100% - 64px); left: 0px; top:64px;"><iframe src="overview.php" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe></div>
	<?php //Schließen-Knopf an der Spielspalte (Optik in de-main.scss), die Esc-Taste tut dasselbe (dm_esc unten) ?>
	<button type="button" id="iframe_main_container_closer" class="dm-knopf" data-icon="✕" onclick="closeIframeMain()" title="Spielseite schlie&szlig;en (Esc)"></button>

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


		//Serverzeit, letzter WT und KT (füllt resline.php), Klick zu den Serverinfos
		echo '<div onclick="switch_iframe_main_container(\'sinfo.php\')" class="dm-zeiten" title="Serverinfos" rel="tooltip">
				<span class="mod-typ">Zeit</span><b id="tb_time1"></b>
				<span class="mod-typ">WT</span><b id="tb_time2"></b>
				<span class="mod-typ">KT</span><b id="tb_time3"></b>
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

//Größe des Chats ändern über die Ränder oben und links und den Griff in der Ecke; eine Fläche über der ganzen Seite
//fängt die Mausbewegung, weil die iframes sie sonst schlucken
var dm_resize = null;

function dm_chat_resize_start(e){
	var popup = $('#chat_popup');
	dm_resize = {
		richtung: $(this).attr('data-richtung'),
		x: e.clientX,
		y: e.clientY,
		breite: popup.outerWidth(),
		hoehe: popup.outerHeight()
	};
	$('#iframe_chat').css('pointer-events', 'none');
	$('body').css('user-select', 'none').append('<div id="resize-overlay" style="position: fixed; inset: 0; z-index: 9999; cursor: ' + $(this).css('cursor') + ';"></div>');
	e.preventDefault();
}

function dm_chat_resize_move(e){
	if(!dm_resize){
		return;
	}
	var breite = dm_resize.breite;
	var hoehe = dm_resize.hoehe;
	if(dm_resize.richtung !== 'n'){
		breite = Math.max(250, dm_resize.breite + dm_resize.x - e.clientX);
	}
	if(dm_resize.richtung !== 'w'){
		hoehe = Math.max(200, dm_resize.hoehe + dm_resize.y - e.clientY);
	}
	$('#chat_popup').css({width: breite + 'px', height: hoehe + 'px'});
}

//speichern=false (Esc) stellt die alte Größe wieder her
function dm_chat_resize_ende(speichern){
	if(!dm_resize){
		return;
	}
	var popup = $('#chat_popup');
	if(speichern){
		setCookie('chat_width', popup.outerWidth() + 'px');
		setCookie('chat_height', popup.outerHeight() + 'px');
	}else{
		popup.css({width: dm_resize.breite + 'px', height: dm_resize.hoehe + 'px'});
	}
	dm_resize = null;
	$('#resize-overlay').remove();
	$('#iframe_chat').css('pointer-events', '');
	$('body').css('user-select', '');
}

//Esc schließt die Spielspalte bzw. das große Fenster; läuft gerade eine Größenänderung des Chats, bricht es nur diese ab.
//Die Spielseiten und die Karte reichen die Taste aus ihren iframes hierher weiter (de_fn.js, map.php).
function dm_esc(){
	if(dm_resize){
		dm_chat_resize_ende(false);
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
	$(document).on('mousemove', '#resize-overlay', dm_chat_resize_move);
	$(document).on('mouseup', function(){ dm_chat_resize_ende(true); });
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
</script>
</body>
</html>
