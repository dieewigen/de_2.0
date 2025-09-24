<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'inc/lang/'.$sv_server_lang.'_resline.lang.php';

unset($_SESSION["de_frameset"]);

//zeigt an, dass die neue Desktopversion verwendet wird, wird z.B. für das Infocenter benötigt
$_SESSION['new_desktop_version']=1;
$_SESSION['ic_last_refresh']=0;

//gibt es eine Chatgröße im Cookie?
$chat_width=400;
$chat_height=400;

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
	<?php
	//<div style="position: absolute; width: 209px; height: calc(100% - 80px); left: 0px; top:24px;"><iframe src="menu.php" id="iframe_menu" height="100%" width="100%" frameBorder="0"></iframe></div>
	?>
	<div id="iframe_main_container" style="position: absolute; width: 620px; height: calc(100% - 64px); left: 0px; top:64px;"><iframe src="overview.php" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe></div>
	<div id="iframe_main_container_closer" style="position: absolute; left: 620px; top:64px; width: 29px; height: 31px; background-color: rgba(0,0,0, 0.8); border-bottom: 1px solid rgba(22,22,22, 0.8); border-right: 1px solid #222222; cursor: pointer;" onclick="closeIframeMain()">
		<img src="gp/g/close_icon.png" style="height: 26px; width: auto; margin-left: 4px; margin-top: 4px;" alt="Fenster schlie&szlig;en" title="Fenster schlie&szlig;en" rel="tooltip">
	</div>

	<div id="iframe_main_container_big" style="position: absolute; display: none; width: 100%; height: calc(100% - 64px); left: 0px; top:64px; z-index: 100;"></div>
	
	<div id="chat_popup" style="position: fixed; bottom: 0; right: 0; width: <?php echo $chat_width; ?>px; height: <?php echo $chat_height; ?>px; z-index: 1000; overflow: hidden;">
		<div id="chat_header" style="color: #FFFFFF; background: linear-gradient(to right, #222222, #303030); padding: 5px 10px; border-bottom: 1px solid #444444; font-weight: 500; user-select: none;">CHAT</div>
		<iframe src="chat.php" id="iframe_chat" name="c" width="100%" frameBorder="0"></iframe>
		<!-- Unsichtbare Resize-Bereiche -->
		<div class="resize-handle resize-n" style="position: absolute; top: 0; left: 0; right: 0; height: 3px; cursor: n-resize;"></div>
		<div class="resize-handle resize-w" style="position: absolute; top: 0; left: 0; bottom: 0; width: 3px; cursor: w-resize;"></div>
		<div class="resize-handle resize-nw" style="position: absolute; top: 0; left: 0; width: 10px; height: 10px; cursor: nw-resize;"></div>
	</div>
	
	<div id="topbar" style="z-index: 1000;">
		<?php 
		//Rassenlogo
		echo '
		<div class="dropdown">
			<img src="gp/g/derassenlogo'.$_SESSION['ums_rasse'].'.png" style="position: absolute; left: -31px; top: -1px; width: auto; height: 72px; cursor: pointer;" onclick="switch_iframe_main_container(\'overview.php\')">
			<div class="dropdown-content" style="z-index: 200;">
			
				<span onclick="switch_iframe_main_container(\'sector.php\')" class="btn">'.$menu_lang['eintrag_12'].'</span>
				<br>
				<span onclick="switch_iframe_main_container(\'options.php\')" class="btn">'.$menu_lang['eintrag_24'].'</span>
				<br><a href="index.php?logout=1" class="btn">'.$menu_lang['eintrag_29'].'</a>
			</div>
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


		//serverzeit
		echo '<div onclick="switch_iframe_main_container(\'sinfo.php\')" style="position: absolute; right: 31px; top:0; height:66px; width: 84px; cursor: pointer;">
				<img src="gp/g/tb_timedata.png" style="position: absolute; width: 100%; height: 100%;">
				<div id="tb_time1" style="position: absolute; top: 1px; left: 34px;"></div>
				<div id="tb_time2" style="position: absolute; top: 24px; left: 34px;"></div>
				<div id="tb_time3" style="position: absolute; top: 47px; left: 34px;"></div>
			</div>';
		
		//Menüpunkte
		echo '<div style="position: absolute; left:0; top: 0px; padding-top: 0px; height: calc(100% - 0px); z-index: 2000; padding-right: 6px;
			margin-left: 540px; margin-right: 112px; background-color: #111111; border-left: 1px solid #666666; border-right: 1px solid #666666;">';

		//Technologien
		echo '<span onclick="switch_iframe_main_container_big(\'ang_techs.php\')" class="btn">'.$menu_lang['eintrag_36'].'</span>';
		//Spezialisierung
		echo '<span onclick="switch_iframe_main_container(\'specialization.php\')" class="btn">Spezialisierung</span>';
		//Artefakte
		echo '<span onclick="switch_iframe_main_container(\'artefacts.php\')" class="btn">'.$menu_lang['eintrag_18'].'</span>';
		//Auktion
		echo '<span onclick="switch_iframe_main_container(\'auction.php\')" class="btn">Auktion</span>';
		//Missionen
		echo '<span onclick="switch_iframe_main_container(\'missions.php\')" class="btn">Missionen</span>';
		//Produktion
		echo '<span onclick="switch_iframe_main_container(\'production.php\')" class="btn">Produktion</span>';

		echo '<span onclick="switch_iframe_main_container(\'military.php\')" class="btn">Flotten</span>';
		
		echo '<span onclick="switch_iframe_main_container(\'secret.php\')" class="btn">'.$menu_lang['eintrag_11'].'</span>';
	
		echo '<span onclick="switch_iframe_main_container(\'allymain.php\')" class="btn">'.$menu_lang['eintrag_16'].'</span>';



		

		echo '<span onclick="switch_iframe_main_container(\'statistics.php\')" class="btn">'.$menu_lang['eintrag_21'].'</span>';
		echo '<span onclick="switch_iframe_main_container(\'toplist.php\')" class="btn">'.$menu_lang['eintrag_22'].'</span>';
			

		echo '</div>
		</div>';

		////////////////////////////////////////////////////////
		//Infocenter
		////////////////////////////////////////////////////////
		//echo '<div id="ic-button" onclick="$(\'#ic\').toggle()">Infocenter</div>';
		//unsichtbares Div, in das die Scripte für das Infocenter geladen werden
		echo '<div id="infocenter"></div>';
		
		////////////////////////////////////////////////////////
		//Icons direkt auf der Karte
		////////////////////////////////////////////////////////
	
		//Reload-Button
		echo '<img onclick="document.getElementById(\'iframe_map\').contentDocument.location.reload(true);" style="width: 40px; height: auto; position: absolute; right: 6px; top: 74px; cursor: pointer;" src="gp/g/icon9.png" class="rounded-borders" title="Karte aktualisieren" rel="tooltip">';

		//VS Listenansicht
		echo '<img onclick="switch_iframe_main_container(\'map_mobile.php\')" style="width: 40px; height: auto; position: absolute; right: 60px; top: 74px; cursor: pointer;" src="gp/g/icon13.png" class="rounded-borders" title="Vergessene System (VS) &Uuml;bersicht" rel="tooltip">';	

		//go-home-Button
		echo '<img onclick="reset_map()" style="width: 40px; height: auto; position: absolute; right: 6px; top: 124px; cursor: pointer;" src="gp/g/icon10.png" class="rounded-borders" title="zum Heimatsektor" rel="tooltip">';

if(isset($_COOKIE['chat_width'])){
	$chat_width=str_replace("px", "", $_COOKIE['chat_width']);
}

if(isset($_COOKIE['chat_height'])){
	$chat_height=str_replace("px", "", $_COOKIE['chat_height']);
}


if($chat_width<250){
	$chat_width=250;
}

if($chat_height<250){
	$chat_height=250;
}

	?>
<script type="text/javascript">
window.onresize = setsize;

function setsize(){ 
    $("body").css("overflow","hidden");
}
	
$(document).ready(function() {
	setsize();

	// Chat-Popup Größe beim Laden setzen
	var chatPopup = $('#chat_popup');
	var iframe = $('#iframe_chat');
	
	// Größe aus PHP-Variablen setzen (falls Cookies vorhanden waren)
	chatPopup.css({
		width: '<?php echo $chat_width; ?>px',
		height: '<?php echo $chat_height; ?>px'
	});

	// Robuste Resize-Funktionalität
	var isResizing = false;
	var resizeDirection = '';
	var startX, startY, startWidth, startHeight;

	// Resize-Handles
	$('.resize-handle').on('mousedown', function(e) {
		isResizing = true;
		resizeDirection = $(this).attr('class').split(' ')[1];
		
		startX = e.clientX;
		startY = e.clientY;
		startWidth = chatPopup.width();
		startHeight = chatPopup.height();
		
		// Iframe deaktivieren während Resize
		iframe.css('pointer-events', 'none');
		
		// Overlay über gesamte Seite um Events zu fangen
		if (!$('#resize-overlay').length) {
			$('body').append('<div id="resize-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; cursor: ' + $(this).css('cursor') + ';"></div>');
		}
		
		e.preventDefault();
		e.stopPropagation();
		$('body').css('user-select', 'none');
	});

	// Resize über Overlay
	$(document).on('mousemove.chatresize', '#resize-overlay', function(e) {
		if (!isResizing) return;
		
		var deltaX = startX - e.clientX;
		var deltaY = startY - e.clientY;
		
		var newWidth = startWidth;
		var newHeight = startHeight;
		
		if (resizeDirection === 'resize-w' || resizeDirection === 'resize-nw') {
			newWidth = startWidth + deltaX;
		}
		if (resizeDirection === 'resize-n' || resizeDirection === 'resize-nw') {
			newHeight = startHeight + deltaY;
		}
		
		newWidth = Math.max(250, newWidth);
		newHeight = Math.max(200, newHeight);
		
		chatPopup.css({
			width: newWidth + 'px',
			height: newHeight + 'px'
		});
	});

	// Resize beenden
	$(document).on('mouseup.chatresize', function() {
		if (isResizing) {
			isResizing = false;
			resizeDirection = '';
			
			// Cleanup
			$('#resize-overlay').remove();
			iframe.css('pointer-events', '');
			$('body').css('user-select', '');
			
			// Cookies speichern
			setCookie('chat_width', chatPopup.width() + 'px');
			setCookie('chat_height', chatPopup.height() + 'px');
		}
	});

	// Sicherheits-Cleanup bei Escape
	$(document).on('keydown.chatresize', function(e) {
		if (e.key === 'Escape' && isResizing) {
			isResizing = false;
			$('#resize-overlay').remove();
			iframe.css('pointer-events', '');
			$('body').css('user-select', '');
		}
	});
});

window.setInterval(function(){
	$("#iframe_menu").contents().find("body").css("background-color", "transparent");
	$("#iframe_menu").contents().find("body").css("background-image", "none");
	$("#iframe_main").contents().find("body").css("background-color", "transparent");
	$("#iframe_main").contents().find("body").css("background-image", "url(gp/g/cellblack.png)");
	$("#iframe_chat").contents().find("body").css("background-color", "transparent");
	$("#iframe_chat").contents().find("body").css("background-image", "none");
}, 100);
</script>
</body>
</html>
