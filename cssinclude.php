<?php
echo '<meta charset="UTF-8">';

//behandlung außerhalb der session
if(!isset($_SESSION['ums_rasse'])){
	$_SESSION['ums_rasse']=1;
}

if(!isset($_SESSION['ums_mobi'])){
	$_SESSION['ums_mobi']=0;
}

if($_SESSION['ums_mobi']==1){
	echo '<meta name="viewport" content="width=620">';
}

//Browser-Zubehör: Favicon, Startbildschirm-Symbol, Farbe der Browserleiste am Handy (src/View/Seitenkopf.php)
require_once __DIR__.'/vendor/autoload.php';
echo \DieEwigen\DE2\View\Seitenkopf::zubehoer((int)$_SESSION['ums_rasse']);

echo '<link rel="stylesheet" type="text/css" href="gp/de-main.css?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-main.css').'">';

echo '<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>';
echo '<script type="text/javascript" src="js/de_fn.js?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js').'"></script>';

//in der Spielspalte der Desktop-Ansicht (dm.php, Rahmen "h") liegt die Seite auf der Karte: html.dm-rahmen gibt ihr in
//gp/de-main.scss das Zellmuster statt des Hintergrundbilds; per Script, weil jede Seite ihr <html> selbst schreibt
if(!empty($_SESSION['new_desktop_version']) && empty($_SESSION['de_frameset']) && $_SESSION['ums_mobi']!=1){
	echo '<script>if(window.parent!==window && window.name==="h"){document.documentElement.classList.add("dm-rahmen");}</script>';
}

//Geisterschiff zu Halloween: Stil und Script nur für angemeldete Spieler und nur am Event-Tag (src/Model/GhostShip/GhostShipEvent.php)
if(!empty($_SESSION['ums_user_id']) && isset($GLOBALS['dbi'])){
	echo \DieEwigen\DE2\View\GhostShip\GhostShipView::pageHead(new \DieEwigen\DE2\Model\GhostShip\GhostShipEvent($GLOBALS['dbi']));
}
