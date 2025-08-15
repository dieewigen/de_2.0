<?php
//fix um das men� von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include 'inc/header.inc.php';
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'functions.php';

if ($ums_gpfad != '') {
    $url = $ums_gpfad.'g/m/';
}
if ($ums_gpfad != '') {
    $bgurl = $ums_gpfad.'g/';
}

//test auf mobile version
if ($_SESSION['ums_mobi'] == 1) {
    echo '<!DOCTYPE html>
<html lang="de">
<head>
<title>Menu</title>
<meta charset="UTF-8">
</head>
<style>
body {background-color: #000000;}
a{color: #FFFFFF;}
.btn{border: 2px solid #666666; padding: 0px; margin-bottom: 5px; font-size: 40px; background-color: #111111; color: #FFFFFF; text-decoration: none; white-space:nowrap;}
</style>
<body>';

    if ($_COOKIE['deactivate_swipe'] != 1) {
        ?>
<script>
function swipedetect(el, callback){
  
    var touchsurface = el,
    swipedir,
    startX,
    startY,
    distX,
    distY,
    threshold = 150, //required min distance traveled to be considered swipe
    restraint = 100, // maximum distance allowed at the same time in perpendicular direction
    allowedTime = 300, // maximum time allowed to travel that distance
    elapsedTime,
    startTime,
    handleswipe = callback || function(swipedir){}
  
    touchsurface.addEventListener('touchstart', function(e){
        var touchobj = e.changedTouches[0]
        swipedir = 'none'
        dist = 0
        startX = touchobj.pageX
        startY = touchobj.pageY
        startTime = new Date().getTime() // record time when finger first makes contact with surface
        //e.preventDefault()
    }, false)
  
    touchsurface.addEventListener('touchmove', function(e){
        //e.preventDefault() // prevent scrolling when inside DIV
    }, false)
  
    touchsurface.addEventListener('touchend', function(e){
        var touchobj = e.changedTouches[0]
        distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
        distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
        elapsedTime = new Date().getTime() - startTime // get time elapsed
        if (elapsedTime <= allowedTime){ // first condition for awipe met
            if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
            }
            else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
            }
        }
        handleswipe(swipedir)
        //e.preventDefault()
    }, false)
}


document.addEventListener('DOMContentLoaded', function() {
	var el = document;//getElementById('document.body')
	swipedetect(el, function(swipedir){
		//swipedir contains either "none", "left", "right", "top", or "down"
		if (swipedir =='right'){
			document.location.href='menu.php';
		}
		
		if (swipedir =='left'){
			document.location.href='chat.php';
		}	
	});
}, false);


</script>

<?php
    }
    echo '<div align="center">';
    echo '<a href="chat.php"><div class="btn">DE-Chat</div></a>';
    echo '<a href="overview.php"><div class="btn">&Uuml;bersicht</div></a>';
    echo '<a href="hyperfunk.php"><div class="btn">'.$menu_lang['eintrag_2'].'</div></a>';
    echo '<a href="sysnews.php"><div class="btn">'.$menu_lang['eintrag_3'].'</div></a>';

    echo '<a href="ang_techs.php"><div class="btn">Technologien</div></a>';
    echo '<a href="specialization.php"><div class="btn">Spezialisierung</div></a>';

    echo '<a href="resource.php"><div class="btn">'.$menu_lang['eintrag_6'].'</div></a>';
    echo '<a href="artefacts.php"><div class="btn">'.$menu_lang['eintrag_18'].'</div></a>';
    echo '<a href="auction.php"><div class="btn">Auktion</div></a>';
    echo '<a href="missions.php"><div class="btn">Missionen</div></a>';


    echo '<a href="production.php"><div class="btn">'.$menu_lang['eintrag_8'].'</div></a>';
    echo '<a href="military.php"><div class="btn">Flotten</div></a>';
    echo '<a href="secret.php"><div class="btn">'.$menu_lang['eintrag_11'].'</div></a>';
    echo '<a href="sector.php"><div class="btn">'.$menu_lang['eintrag_12'].'</div></a>';
    echo '<a href="secstatus.php"><div class="btn">'.$menu_lang['eintrag_13'].'</div></a>';
    if ($sv_deactivate_vsystems != 1) {
        echo '<a href="map_mobile.php"><div class="btn">Vergessene Systeme</div></a>';
    }

    echo '<a href="allymain.php"><div class="btn">'.$menu_lang['eintrag_16'].'</div></a>';
    echo '<a href="statistics.php"><div class="btn">'.$menu_lang['eintrag_21'].'</div></a>';
    echo '<a href="toplist.php"><div class="btn">'.$menu_lang['eintrag_22'].'</div></a>';
    echo '<a href="options.php"><div class="btn">'.$menu_lang['eintrag_24'].'</div></a>';
    echo '<a href="index.php?logout=1"><div class="btn">'.$menu_lang['eintrag_29'].'</div></a>';

    die('</div></body></html>');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<title>Menu</title><title><?php echo $menu_lang['title']?></title>
<?php

echo '<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'m'.$_SESSION['ums_rasse'].'.css"><link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'f'.$_SESSION['ums_rasse'].'.css">';

?>
<script>
var aktScroll;
var aktMinTop = 0;
var scrollStep = 7;
var btnStat = new Array();
var isAktive = "no";

function StartScroll(sDir) {
	if(document.getElementById("menubtns").offsetHeight > document.getElementById("menumain").offsetHeight){
		if (aktMinTop == 0) { aktMinTop = document.getElementById('menumain').offsetHeight - document.getElementById('menubtns').offsetHeight }
		aktScroll = window.setInterval("DoScroll('"+sDir+"')", 25);
		isAktive = "yes";
	}
}

function DoScroll(sDir) {
	var aktTop = document.getElementById("menubtns").style.top;
	if (sDir == "Up") { var newTop = (aktTop.replace("px", "") * 1) + scrollStep; }
	else { var newTop = (aktTop.replace("px", "") * 1) - scrollStep;}
	
	if (((sDir == "Down") && (newTop < aktMinTop) ) || ((sDir == "Up") && (newTop > 0))){
		newTop = (sDir == "Up") ? 0 : aktMinTop;
		btnStat[sDir] = "off";
		window.clearInterval(aktScroll);
		isAktive = "no";
	}
	else { btnStat[sDir] = "on"; }
	
	document.getElementById("menubtns").style.top = newTop + "px";
}

function StopScroll() {
	if (isAktive == "yes") { window.clearInterval(aktScroll); }
}

</script>
</head>
<body onload="setsize()">
<?php

//hauptcontainer �ffnen
echo '<div id="content">';
//oberen men�teile �ffenen


echo '<div class="m1">';
//obere scrollbuttons hinterlegen
echo '<div id="btnup1" onClick="document.getElementById(\'menubtns\').style.top = \'0px\'" onMouseOver="StartScroll(\'Up\')" onMouseOut="StopScroll()"></div>';
echo '<div id="btnup2" onClick="document.getElementById(\'menubtns\').style.top = \'0px\'" onMouseOver="StartScroll(\'Up\')" onMouseOut="StopScroll()"></div>';
echo '</div>';
//mittleren men�teil hinterlegen der sich wiederholt
echo '<div class="m2"></div>';
//unteren men�teil �ffnen
echo '<div class="m3">';
//untere scrollbuttons hinterlegen
echo '<div id="btndown1" onClick="document.getElementById(\'menubtns\').style.top = aktMinTop+\'px\'" onMouseOver="StartScroll(\'Down\')" onMouseOut="StopScroll()"></div>';
echo '<div id="btndown2" onClick="document.getElementById(\'menubtns\').style.top = aktMinTop+\'px\'" onMouseOver="StartScroll(\'Down\')" onMouseOut="StopScroll()"></div>';
echo '</div>';

//scrollbarbereich hinterlegen
echo '<div id="menumain">';
echo '<div id="menubtns">';

//alle men�punkte hier anlegen
echo '<a href="overview.php" target="h" class="btn">&Uuml;bersicht</a>';
echo '<a href="hyperfunk.php" target="h" class="btn">'.$menu_lang['eintrag_2'].'</a>';
echo '<a href="sysnews.php" target="h" class="btn">'.$menu_lang['eintrag_3'].'</a>';
echo '<span class="btnspacer">&nbsp;</span>';

echo '<a href="ang_techs.php" target="h" class="btn"><div class="btn">Technologien</div></a>';
echo '<a href="specialization.php" target="h" class="btn"><div class="btn">Spezialisierung</div></a>';


echo '<a href="resource.php" target="h" class="btn">'.$menu_lang['eintrag_6'].'</a>';
echo '<a href="artefacts.php" target="h" class="btn">'.$menu_lang['eintrag_18'].'</a>';
echo '<span class="btnspacer">&nbsp;</span>';
echo '<a href="auction.php" target="h" class="btn">Auktion</a>';
echo '<a href="missions.php" target="h" class="btn">Missionen</a>';

if (!isset($sv_deactivate_vsystems)) {
    $sv_deactivate_vsystems = 0;
}

if ($sv_deactivate_vsystems != 1) {
    echo '<a href="map_mobile.php" target="h" class="btn">V-Systeme</a>';
}

echo '<span class="btnspacer">&nbsp;</span>';
echo '<a href="production.php" target="h" class="btn">'.$menu_lang['eintrag_8'].'</a>';
echo '<a href="military.php" target="h" class="btn">Flotten</a>';
echo '<a href="secret.php" target="h" class="btn">'.$menu_lang['eintrag_11'].'</a>';

echo '<span class="btnspacer">&nbsp;</span>';
echo '<a href="sector.php" target="h" class="btn">'.$menu_lang['eintrag_12'].'</a>';
echo '<a href="secstatus.php" target="h" class="btn">'.$menu_lang['eintrag_13'].'</a>';
echo '<a href="allymain.php" target="h" class="btn">'.$menu_lang['eintrag_16'].'</a>';
echo '<span class="btnspacer">&nbsp;</span>';

echo '<a href="statistics.php" target="h" class="btn">'.$menu_lang['eintrag_21'].'</a>';
echo '<a href="toplist.php" target="h" class="btn">'.$menu_lang['eintrag_22'].'</a>';
echo '<span class="btnspacer">&nbsp;</span>';
echo '<a href="options.php" target="h" class="btn">'.$menu_lang['eintrag_24'].'</a>';
echo '<a href="index.php?logout=1" target="h" class="btn">'.$menu_lang['eintrag_29'].'</a>';

echo '</div>';
echo '</div>';


echo '</div>';

//per javascript die gr��en dynamisch anpassen
echo '
<script language="JavaScript" type="text/javascript">

window.onresize = setsize;

function setsize(){ 
  document.getElementById("content").style.height="100%";
  if(document.getElementById("content").offsetHeight<710)
	document.getElementById("content").style.height="710px";
  document.getElementById("menumain").style.height=(document.getElementById("content").offsetHeight-345)+"px";
  aktMinTop=0;
  document.getElementById(\'menubtns\').style.top = \'0px\';

  if(document.getElementById("menumain").offsetHeight > document.getElementById("menubtns").offsetHeight) document.getElementById("menumain").style.height = document.getElementById("menubtns").offsetHeight+"px";
}

setsize();
  
</script>';

echo '</body></html>';
?>