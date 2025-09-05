<?php
//fix um das men� von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include 'inc/header.inc.php';
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'functions.php';

//test auf mobile version
if ($_SESSION['ums_mobi'] == 1) {

    // Akzentfarbe je Rasse (optional anpassen)
    $accentMap = [
        1 => '#3399FF',
        2 => '#A6A6A6',
        3 => '#DE3939',
        4 => '#ED951E',
    ];
    $accent = $accentMap[$_SESSION['ums_rasse'] ?? 1];

    echo '<!DOCTYPE html>
<html lang="de">
<head>
<title>Menu</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{
  --accent: '.$accent.';
  --bg-panel: #0d1218;
  --bg-panel-grad: linear-gradient(145deg,#161f29,#0d1218 60%);
  --panel-border: rgba(160,200,255,0.18);
  --txt: #d8e9ff;
  --danger: #ff4a4a;
  --scan: rgba(255,255,255,0.04);
  --glow: 0 0 8px color-mix(in srgb,var(--accent) 60%, transparent),0 0 18px color-mix(in srgb,var(--accent) 35%, transparent);
  font-family: system-ui,Segoe UI,Roboto,Arial,sans-serif;
}
*{box-sizing:border-box;}
html,body{margin:0;padding:0;min-height:100dvh;background:
  repeating-linear-gradient(0deg,var(--scan) 0 2px,transparent 2px 6px),
  radial-gradient(circle at 18% 25%,#091019 0%,#030405 70%);
  color:var(--txt);
  -webkit-font-smoothing:antialiased;
}
.menu-wrap{max-width:960px;margin:0 auto;padding:clamp(12px,3vw,30px);}
.menu-title{
  margin:0 0 18px;
  font-size:clamp(1.3rem,4.2vw,2.1rem);
  letter-spacing:.08em;
  font-weight:600;
  text-transform:uppercase;
  background:linear-gradient(90deg,var(--accent),#fff);
  -webkit-background-clip:text;
  color:transparent;
  filter:drop-shadow(0 0 4px color-mix(in srgb,var(--accent) 55%, transparent));
}
.menu-grid{
  display:grid;
  gap:12px;
  grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
}
@media(max-width:560px){
  .menu-grid{grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;}
}
.menu-btn{
  position:relative;
  display:flex;
  flex-direction:column;
  gap:6px;
  text-decoration:none;
  padding:14px 14px 12px;
  background:var(--bg-panel-grad);
  border:1px solid var(--panel-border);
  border-radius:12px;
  color:var(--txt);
  font-size:.92rem;
  letter-spacing:.04em;
  line-height:1.25;
  overflow:hidden;
  isolation:isolate;
  transition:border-color .25s, color .25s, transform .18s, background .4s, box-shadow .25s;
  box-shadow:inset 0 0 0 1px rgba(255,255,255,0.04),0 2px 6px rgba(0,0,0,.65);
}
.menu-btn::before{
  content:attr(data-icon);
  font-size:1.7rem;
  line-height:1;
  color:var(--accent);
  filter:drop-shadow(0 0 6px rgba(255,255,255,0.25));
  transition:transform .45s;
}
.menu-btn::after{
  content:"";
  position:absolute;
  inset:0;
  background:
    linear-gradient(120deg,transparent 0 40%,rgba(255,255,255,.07) 45%,transparent 55% 100%),
    radial-gradient(circle at 85% 15%,rgba(255,255,255,.12),transparent 60%);
  mix-blend-mode:screen;
  opacity:.55;
  pointer-events:none;
}
.menu-btn:hover,
.menu-btn:focus-visible{
  outline:none;
  border-color:var(--accent);
  color:#fff;
  transform:translateY(-3px);
  box-shadow:var(--glow);
}
.menu-btn:hover::before{transform:scale(1.1) rotate(5deg);}
.menu-btn.danger{--accent:var(--danger);}
.menu-btn.danger:hover{box-shadow:0 0 10px rgba(255,80,80,.6),0 0 22px rgba(255,80,80,.35);}
@media(hover:none){
  .menu-btn:hover{transform:none;}
}
.swipe-hint{margin-top:16px;font-size:.7rem;opacity:.55;text-align:center;letter-spacing:.08em;}
</style>
</head>
<body>';

	$swipeHint = '';

	if(!isset($_COOKIE['deactivate_swipe'])){
		$_COOKIE['deactivate_swipe'] = 0;
	}

    if ($_COOKIE['deactivate_swipe'] != 1) {
		$swipeHint = '<div class="swipe-hint">Swipe ◀ ▶ zum Wechseln</div>';
        echo '<script>
function swipedetect(el, cb){
  let sx, sy, st;
  const thresh=120, restr=90, time=320;
  el.addEventListener("touchstart",e=>{
    const t=e.changedTouches[0]; sx=t.pageX; sy=t.pageY; st=Date.now();
  },{passive:true});
  el.addEventListener("touchend",e=>{
    const t=e.changedTouches[0];
    let dx=t.pageX-sx, dy=t.pageY-sy, dt=Date.now()-st, dir="none";
    if(dt<=time){
      if(Math.abs(dx)>=thresh && Math.abs(dy)<=restr) dir=dx<0?"left":"right";
      else if(Math.abs(dy)>=thresh && Math.abs(dx)<=restr) dir=dy<0?"up":"down";
    }
    cb(dir);
  },{passive:true});
}
document.addEventListener("DOMContentLoaded",()=>{
  swipedetect(document,dir=>{
    if(dir==="right") location.href="menu.php";
    if(dir==="left")  location.href="chat.php";
  });
});
</script>';
    }

    echo '<div class="menu-wrap">
  <h1 class="menu-title">Kommandokonsole</h1>
  <nav class="menu-grid">
    <a class="menu-btn" data-icon="💬" href="chat.php">DE-Chat</a>
    <a class="menu-btn" data-icon="🛰" href="overview.php">&Uuml;bersicht</a>
    <a class="menu-btn" data-icon="📡" href="hyperfunk.php">'.$menu_lang['eintrag_2'].'</a>
    <a class="menu-btn" data-icon="🗞" href="sysnews.php">'.$menu_lang['eintrag_3'].'</a>
    <a class="menu-btn" data-icon="⚙" href="ang_techs.php">Technologien</a>
    <a class="menu-btn" data-icon="🧬" href="specialization.php">Spezialisierung</a>
    <a class="menu-btn" data-icon="⛃" href="resource.php">'.$menu_lang['eintrag_6'].'</a>
    <a class="menu-btn" data-icon="✧" href="artefacts.php">'.$menu_lang['eintrag_18'].'</a>
    <a class="menu-btn" data-icon="⚖" href="auction.php">Auktion</a>
    <a class="menu-btn" data-icon="✪" href="missions.php">Missionen</a>'.
    ($sv_deactivate_vsystems!=1?'<a class="menu-btn" data-icon="✸" href="map_mobile.php">V-Systeme</a>':'').'
    <a class="menu-btn" data-icon="🏭" href="production.php">'.$menu_lang['eintrag_8'].'</a>
    <a class="menu-btn" data-icon="🚀" href="military.php">Flotten</a>
    <a class="menu-btn" data-icon="🕵️" href="secret.php">'.$menu_lang['eintrag_11'].'</a>
    <a class="menu-btn" data-icon="⌬" href="sector.php">'.$menu_lang['eintrag_12'].'</a>
    <a class="menu-btn" data-icon="🛸" href="secstatus.php">'.$menu_lang['eintrag_13'].'</a>
    <a class="menu-btn" data-icon="∞" href="allymain.php">'.$menu_lang['eintrag_16'].'</a>
    <a class="menu-btn" data-icon="📊" href="statistics.php">'.$menu_lang['eintrag_21'].'</a>
    <a class="menu-btn" data-icon="★" href="toplist.php">'.$menu_lang['eintrag_22'].'</a>
    <a class="menu-btn" data-icon="⚙" href="options.php">'.$menu_lang['eintrag_24'].'</a>
    <a class="menu-btn danger" data-icon="🚪" href="index.php?logout=1">'.$menu_lang['eintrag_29'].'</a>
  </nav>
  '.$swipeHint.'
</div>
</body></html>';

    die();

}
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<title>Menu</title><title><?php echo $menu_lang['title']?></title>
<?php


echo '<link rel="stylesheet" type="text/css" href="gp/m'.$_SESSION['ums_rasse'].'.css">';
//<link rel="stylesheet" type="text/css" href="gp/f'.$_SESSION['ums_rasse'].'.css">

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