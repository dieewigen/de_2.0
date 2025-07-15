<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'inc/lang/'.$sv_server_lang.'_resline.lang.php';

unset($_SESSION["de_frameset"]);

//zeigt an, dass die neue Desktopversion verwendet wird, wird z.B. für das Infocenter benötigt
$_SESSION['new_desktop_version']=1;
$_SESSION['ic_last_refresh']=0;
?>
<!doctype html>
<html lang="de-de">
  <head>
    
    <title><?php echo $sv_server_tag;?> Die Ewigen - Desktopversion</title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.ico" />

	<link rel="stylesheet" href="js/jquery-ui-1.14.1/jquery-ui.min.css">
	<link rel="stylesheet" href="g/style.css?<?php echo filemtime('g/style.css');?>">	
	
	<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.14.1/jquery-ui.min.js"></script>
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
		<img src="g/close_icon.png" style="height: 26px; width: auto; margin-left: 4px; margin-top: 4px;" alt="Fenster schlie&szlig;en" title="Fenster schlie&szlig;en" rel="tooltip">
	</div>

	<div id="iframe_main_container_big" style="position: absolute; display: none; width: 100%; height: calc(100% - 64px); left: 0px; top:64px; z-index: 100;"></div>
	
	<div id="chat_popup" title="Chat" style="overflow: hidden; height: 500px;">
		<iframe src="chat.php" id="iframe_chat" name="c" height="100%" width="100%" frameBorder="0"></iframe>
	</div>
	
	<div id="topbar" style="z-index: 1000;">
		<?php 
		//Rassenlogo
		echo '
		<div class="dropdown">
			<img src="g/derassenlogo'.$_SESSION['ums_rasse'].'.png" style="position: absolute; left: -31px; top: 0px; width: auto; height: 71px; cursor: pointer;" onclick="switch_iframe_main_container(\'overview.php\')">
			<div class="dropdown-content" style="z-index: 200;">
			
				<span onclick="switch_iframe_main_container(\'options.php\')" class="btn">'.$menu_lang['eintrag_24'].'</span>';

		if(!isset($sv_deactivate_efta) || $sv_deactivate_efta==0){
			echo '<br><a href="eftaindex.php" target="_blank" class="btn">EFTA</a>';
		}

		if(!isset($sv_deactivate_sou) || $sv_deactivate_sou==0){
			echo '<br><a href="sou_index.php" target="_blank" class="btn">EA</a>';
		}

		//echo '<br><a href="'.$sv_link[2].'" target="_blank" class="btn">'.$menu_lang['eintrag_26'].'</a>';
		echo '<br><a href="index.php?logout=1" class="btn">'.$menu_lang['eintrag_29'].'</a>';		
		
		echo '
			</div>
		</div>';		
		
		//Rohstoffe/Credits
		//Multiplex
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="g/icon1.png" style="cursor: pointer; position: absolute; left: 40px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres01desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res1" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 66px;" rel="tooltip"></div>';

		//Dyharra
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="g/icon2.png" style="cursor: pointer; position: absolute; left: 140px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres02desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res2" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 166px;" rel="tooltip"></div>';
		
		//Iradium
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="g/icon3.png" style="cursor: pointer; position: absolute; left: 240px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres03desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res3" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 266px;" rel="tooltip"></div>';
		
		//Eternium
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="g/icon4.png" style="cursor: pointer; position: absolute; left: 340px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres04desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res4" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 366px;" rel="tooltip"></div>';
		
		//Tronic
		echo '<img onclick="switch_iframe_main_container(\'resource.php\')" src="g/icon5.png" style="cursor: pointer; position: absolute; left: 440px; top: 4px; width: 24px; height: auto;" title="'.$resline_lang['restipres05desc'].'" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'resource.php\')" id="tb_res5" class="topbar_textfield" style="cursor: pointer; top: 8px; left: 466px;" rel="tooltip"></div>';

		//Deffer
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer_img_grey" src="g/icon6_grey.png" style="display: none; cursor: pointer; position: absolute; left: 40px; top: 32px; width: 24px; height: auto;" title="zum Sektorstatus" rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer_img" src="g/icon6.png" style="display: none; cursor: pointer; position: absolute; left: 40px; top: 32px; width: 24px; height: auto;" title="Du wirst von diesen Einheiten verteidigt." rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" class="topbar_textfield" style="cursor: pointer; top: 36px; left: 66px;" title="zum Sektorstatus" rel="tooltip">&nbsp;</div>';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_deffer" class="topbar_textfield" style="color: rgba(40,112,53,1); display: none; cursor: pointer; top: 36px; left: 66px;" rel="tooltip"></div>';
		
		//Atter
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter_img_grey" src="g/icon7_grey.png" style="display: none; cursor: pointer; position: absolute; left: 140px; top: 36px; width: 24px; height: auto;" title="zum Sektorstatus" rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter_img" src="g/icon7.png" style="animation: shake 0.5s; display: none; cursor: pointer; position: absolute; left: 140px; top: 36px; width: 24px; height: auto;" title="Du wirst von diesen Einheiten angegriffen." rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" class="topbar_textfield" style="cursor: pointer; top: 36px; left: 166px;" title="zum Sektorstatus" rel="tooltip">&nbsp;</div>';
		echo '<div onclick="switch_iframe_main_container(\'secstatus.php\')" id="tb_atter" class="topbar_textfield" style="color: rgba(215,45,45,1); display: none; cursor: pointer; top: 36px; left: 166px;" rel="tooltip"></div>';

		//Punkte
		echo '<img onclick="switch_iframe_main_container(\'toplist.php\')" id="tb_score_img" src="g/icon8.png" style="cursor: pointer;position: absolute; left: 240px; top: 36px; width: 24px; height: auto;" rel="tooltip">';
		echo '<div onclick="switch_iframe_main_container(\'toplist.php\')" id="tb_score" class="topbar_textfield" style="cursor: pointer; top: 36px; left: 266px;" rel="tooltip"></div>';

		//Hyperfunk
		echo '<img onclick="switch_iframe_main_container(\'hyperfunk.php\')" src="g/hyper.png" style="cursor: pointer; position: absolute; left: 440px; top: 36px; width: 40px; height: auto;" title="Es liegen keine neuen Hyperfunknachrichten vor." rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'hyperfunk.php?l=new\')" id="tb_hyper_img" src="g/'.$_SESSION['ums_rasse'].'_hyper.png" style="display: none;  cursor: pointer; position: absolute; left: 440px; top: 36px; width: 40px; height: auto;" title="'.$resline_lang['restipnewhyperdesc'].'" rel="tooltip">';
		
		//Nachrichten
		echo '<img onclick="switch_iframe_main_container(\'sysnews.php\')" src="g/news.png" style="cursor: pointer; position: absolute; left: 490px; top: 36px; width: 40px; height: auto;" title="Es liegen keine neuen Nachrichten vor." rel="tooltip">';
		echo '<img onclick="switch_iframe_main_container(\'sysnews.php\')" id="tb_news_img" src="g/'.$_SESSION['ums_rasse'].'_news.png" style="display: none; cursor: pointer; position: absolute; left: 490px; top: 36px; width: 40px; height: auto;" title="'.$resline_lang['restipnewnewsdesc'].'" rel="tooltip">';
		
		//daily gift
		echo '<img onclick="switch_iframe_main_container(\'ally_dailygift.php\')" id="tb_daily_img" src="g/symbol1.png" style="display: none;  cursor: pointer; position: absolute; left: 540px; top: 36px; width: 24px; height: auto;" title="'.$resline_lang['dailyallygiftdesc'].'" rel="tooltip">';
		

		//serverzeit
		echo '<div onclick="switch_iframe_main_container(\'sinfo.php\')" style="position: absolute; right: 31px; top:0; height:66px; width: 84px; cursor: pointer;">
				<img src="g/tb_timedata.png" style="position: absolute; width: 100%; height: 100%;">
				<div id="tb_time1" style="position: absolute; top: 1px; left: 34px;"></div>
				<div id="tb_time2" style="position: absolute; top: 24px; left: 34px;"></div>
				<div id="tb_time3" style="position: absolute; top: 47px; left: 34px;"></div>
			</div>';
		
		//Menüpunkte
		echo '<div style="position: absolute; left:0; top: 0px; padding-top: 0px; height: calc(100% - 0px); z-index: 2000; padding-right: 6px;
			margin-left: 568px; margin-right: 112px; background-color: #111111; border-left: 1px solid #666666; border-right: 1px solid #666666;">';

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
		//VS
		/*
		if(!isset($sv_deactivate_vsystems) || $sv_deactivate_vsystems!=1){
			echo '<span onclick="switch_iframe_main_container(\'map_mobile.php\')" class="btn">V-Systeme</span>'; 
		}
		*/
		
		//Produktion
		echo '<span onclick="switch_iframe_main_container(\'production.php\')" class="btn">Produktion</span>';

		echo '<span onclick="switch_iframe_main_container(\'military.php\')" class="btn">Flotten</span>';
		
		echo '<span onclick="switch_iframe_main_container(\'secret.php\')" class="btn">'.$menu_lang['eintrag_11'].'</span>';

		echo '<span onclick="switch_iframe_main_container(\'sector.php\')" class="btn">'.$menu_lang['eintrag_12'].'</span>';
		
		echo '<span onclick="switch_iframe_main_container(\'allymain.php\')" class="btn">'.$menu_lang['eintrag_16'].'</span>';



		

		echo '<span onclick="switch_iframe_main_container(\'statistics.php\')" class="btn">'.$menu_lang['eintrag_21'].'</span>';
		echo '<span onclick="switch_iframe_main_container(\'toplist.php\')" class="btn">'.$menu_lang['eintrag_22'].'</span>';
			

		echo '</div>
		</div>';

		////////////////////////////////////////////////////////
		//Infocenter
		////////////////////////////////////////////////////////
		echo '<div id="ic-button" onclick="$(\'#ic\').toggle()">Infocenter</div>';
		echo '<div id="ic">Daten werden geladen...</div>';

		
		////////////////////////////////////////////////////////
		//Icons direkt auf der Karte
		////////////////////////////////////////////////////////
	
		//Reload-Button
		echo '<img onclick="document.getElementById(\'iframe_map\').contentDocument.location.reload(true);" style="width: 40px; height: auto; position: absolute; right: 6px; top: 74px; cursor: pointer;" src="g/icon9.png" title="Karte aktualisieren" rel="tooltip">';

		//VS Listenansicht
		echo '<img onclick="switch_iframe_main_container(\'map_mobile.php\')" style="width: 40px; height: auto; position: absolute; right: 60px; top: 74px; cursor: pointer;" src="g/icon13.png" title="Vergessene System (VS) &Uuml;bersicht" rel="tooltip">';	

		//go-home-Button
		echo '<img onclick="reset_map()" style="width: 40px; height: auto; position: absolute; right: 6px; top: 124px; cursor: pointer;" src="g/icon10.png" title="zum Heimatsektor" rel="tooltip">';

		?>
	
	
	
	<?php
	//<div onclick="switch_iframe_main_container_big('ang_techs.php')" style="position: absolute; left: 4px; bottom: 4px;"><img src="g/button_tech.png" style="height: 48px; width: auto;" title="Technologien"></div>
	//<a href="production.php" target="h" style="position: absolute; left: 56px; bottom: 4px;"><img src="g/button_production.png" style="height: 48px; width: auto;" title="Produktion"></a>
	//<a href="specialization.php" target="h" style="position: absolute; left: 108px; bottom: 4px;"><img src="g/button_specialization.png" style="height: 48px; width: auto;" title="Spezialisierung"></a>
	
	//<div onclick="switch_iframe_main_container_big('ang_production.php')" style="position: absolute; left: 56px; bottom: 4px;"><img src="g/button_production.png" style="height: 48px; width: auto;" title="Produktion"></div>
	/*
	<script type='text/javascript'>
	var Module = {
		TOTAL_MEMORY: 268435456,
		errorhandler: null,			// arguments: err, url, line. This function must return 'true' if the error is handled, otherwise 'false'
		compatibilitycheck: null,
		dataUrl: "Release/Build.data",
		codeUrl: "Release/Build.js",
		memUrl: "Release/Build.mem",
	};
</script>
<script src="Release/UnityLoader.js"></script>
*/

//gibt es eine Chatgröße im Cookie?
$chat_width=400;
$chat_height=400;

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

	$("#chat_popup").dialog({
		closeOnEscape: false,
		width: <?php echo $chat_width; ?>,
		height: <?php echo $chat_height; ?>,
		draggable: false,	
		open: function(event, ui) {
			$(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
		},
		resize: function(event, ui) {
			setCookie('chat_width', $('.ui-dialog').css('width'));
			setCookie('chat_height', $('.ui-dialog').css('height'));
		}
	});
	$('#chat_popup').dialog({position: {my: 'right bottom', at: 'right bottom', of: window}});
});

window.setInterval(function(){
	$("#iframe_menu").contents().find("body").css("background-color", "transparent");
	$("#iframe_menu").contents().find("body").css("background-image", "none");
	$("#iframe_main").contents().find("body").css("background-color", "transparent");
	$("#iframe_main").contents().find("body").css("background-image", "url(<?php echo $_SESSION['ums_gpfad'] ?>/g/cellblack.png)");
	$("#iframe_chat").contents().find("body").css("background-color", "transparent");
	$("#iframe_chat").contents().find("body").css("background-image", "none");
}, 100);
</script>
</body>
</html>
