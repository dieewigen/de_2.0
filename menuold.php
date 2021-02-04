<?php
//fix um das menü von der botabfrage unabhängig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;
include "inc/sv.inc.php";
include "inc/session.inc.php";
include 'inc/lang/'.$sv_server_lang.'_menu.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';

@ob_start("ob_gzhandler");

if ($ums_gpfad!='')$url=$ums_gpfad.'g/m/';// else $url='http://ewiges-lager.de/de/g/m/';
if ($ums_gpfad!='')$bgurl=$ums_gpfad.'g/';// else $bgurl='http://ewiges-lager.de/de/g/';

$c=1;

//$ums_rasse=$rasse;
?>
<HTML>
<HEAD>
<TITLE><?=$menu_lang[title]?></TITLE>
<?php include "cssinclude.php"; ?>

<?php
/*
<style type="text/css">
<!--
body {scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
 scrollbar-arrow-color: #8CA0B4;}
-->
</style>*/
?>
<script language="JavaScript" type="text/javascript">
function setvis()
{
  if (document.getElementById("menu2").style.visibility == "visible")
  {
    document.getElementById("menu1").style.visibility = "visible";
    document.getElementById("menu2").style.visibility = "hidden";
  }
  else
  {
    document.getElementById("menu1").style.visibility = "hidden";
    document.getElementById("menu2").style.visibility = "visible";
  }
}


N1 = new Image();
N1.src = "<?=$url; ?><?=$ums_rasse; ?>_1.gif";
H1 = new Image();
H1.src = "<?=$url; ?><?=$ums_rasse; ?>_1_h.gif";
N2 = new Image();
N2.src = "<?=$url; ?><?=$ums_rasse; ?>_2.gif";
H2 = new Image();
H2.src = "<?=$url; ?><?=$ums_rasse; ?>_2_h.gif";
N3 = new Image();
N3.src = "<?=$url; ?><?=$ums_rasse; ?>_3.gif";
H3 = new Image();
H3.src = "<?=$url; ?><?=$ums_rasse; ?>_3_h.gif";
N4 = new Image();
N4.src = "<?=$url; ?><?=$ums_rasse; ?>_4.gif";
H4 = new Image();
H4.src = "<?=$url; ?><?=$ums_rasse; ?>_4_h.gif";
N5 = new Image();
N5.src = "<?=$url; ?><?=$ums_rasse; ?>_5.gif";
H5 = new Image();
H5.src = "<?=$url; ?><?=$ums_rasse; ?>_5_h.gif";
N6 = new Image();
N6.src = "<?=$url; ?><?=$ums_rasse; ?>_6.gif";
H6 = new Image();
H6.src = "<?=$url; ?><?=$ums_rasse; ?>_6_h.gif";
N7 = new Image();
N7.src = "<?=$url; ?><?=$ums_rasse; ?>_7.gif";
H7 = new Image();
H7.src = "<?=$url; ?><?=$ums_rasse; ?>_7_h.gif";
N8 = new Image();
N8.src = "<?=$url; ?><?=$ums_rasse; ?>_8.gif";
H8 = new Image();
H8.src = "<?=$url; ?><?=$ums_rasse; ?>_8_h.gif";
N9 = new Image();
N9.src = "<?=$url; ?><?=$ums_rasse; ?>_9.gif";
H9 = new Image();
H9.src = "<?=$url; ?><?=$ums_rasse; ?>_9_h.gif";
N10 = new Image();
N10.src = "<?=$url; ?><?=$ums_rasse; ?>_10.gif";
H10 = new Image();
H10.src = "<?=$url; ?><?=$ums_rasse; ?>_10_h.gif";
N11 = new Image();
N11.src = "<?=$url; ?><?=$ums_rasse; ?>_11.gif";
H11 = new Image();
H11.src = "<?=$url; ?><?=$ums_rasse; ?>_11_h.gif";
N12 = new Image();
N12.src = "<?=$url; ?><?=$ums_rasse; ?>_12.gif";
H12 = new Image();
H12.src = "<?=$url; ?><?=$ums_rasse; ?>_12_h.gif";
N13 = new Image();
N13.src = "<?=$url; ?><?=$ums_rasse; ?>_13.gif";
H13 = new Image();
H13.src = "<?=$url; ?><?=$ums_rasse; ?>_13_h.gif";
N14 = new Image();
N14.src = "<?=$url; ?><?=$ums_rasse; ?>_14.gif";
H14 = new Image();
H14.src = "<?=$url; ?><?=$ums_rasse; ?>_14_h.gif";
N15 = new Image();
N15.src = "<?=$url; ?><?=$ums_rasse; ?>_15.gif";
H15 = new Image();
H15.src = "<?=$url; ?><?=$ums_rasse; ?>_15_h.gif";
N16 = new Image();
N16.src = "<?=$url; ?><?=$ums_rasse; ?>_16.gif";
H16 = new Image();
H16.src = "<?=$url; ?><?=$ums_rasse; ?>_16_h.gif";
N17 = new Image();
N17.src = "<?=$url; ?><?=$ums_rasse; ?>_17.gif";
H17 = new Image();
H17.src = "<?=$url; ?><?=$ums_rasse; ?>_17_h.gif";
N18 = new Image();
N18.src = "<?=$url; ?><?=$ums_rasse; ?>_18.gif";
H18 = new Image();
H18.src = "<?=$url; ?><?=$ums_rasse; ?>_18_h.gif";
N19 = new Image();
N19.src = "<?=$url; ?><?=$ums_rasse; ?>_19.gif";
H19 = new Image();
H19.src = "<?=$url; ?><?=$ums_rasse; ?>_19_h.gif";
N20 = new Image();
N20.src = "<?=$url; ?><?=$ums_rasse; ?>_20.gif";
H20 = new Image();
H20.src = "<?=$url; ?><?=$ums_rasse; ?>_20_h.gif";
N21 = new Image();
N21.src = "<?=$url; ?><?=$ums_rasse; ?>_21.gif";
H21 = new Image();
H21.src = "<?=$url; ?><?=$ums_rasse; ?>_21_h.gif";
N22 = new Image();
N22.src = "<?=$url; ?><?=$ums_rasse; ?>_22.gif";
H22 = new Image();
H22.src = "<?=$url; ?><?=$ums_rasse; ?>_22_h.gif";
N23 = new Image();
N23.src = "<?=$url; ?><?=$ums_rasse; ?>_23.gif";
H23 = new Image();
H23.src = "<?=$url; ?><?=$ums_rasse; ?>_23_h.gif";
N24 = new Image();
N24.src = "<?=$url; ?><?=$ums_rasse; ?>_24.gif";
H24 = new Image();
H24.src = "<?=$url; ?><?=$ums_rasse; ?>_24_h.gif";
N25 = new Image();
N25.src = "<?=$url; ?><?=$ums_rasse; ?>_25.gif";
H25 = new Image();
H25.src = "<?=$url; ?><?=$ums_rasse; ?>_25_h.gif";
N26 = new Image();
N26.src = "<?=$url; ?><?=$ums_rasse; ?>_26.gif";
H26 = new Image();
H26.src = "<?=$url; ?><?=$ums_rasse; ?>_26_h.gif";
N27 = new Image();
N27.src = "<?=$url; ?><?=$ums_rasse; ?>_27.gif";
H27 = new Image();
H27.src = "<?=$url; ?><?=$ums_rasse; ?>_27_h.gif";
N28 = new Image();
N28.src = "<?=$url; ?><?=$ums_rasse; ?>_28.gif";
H28 = new Image();
H28.src = "<?=$url; ?><?=$ums_rasse; ?>_28_h.gif";
N29 = new Image();
N29.src = "<?=$url; ?><?=$ums_rasse; ?>_29.gif";
H29 = new Image();
H29.src = "<?=$url; ?><?=$ums_rasse; ?>_29_h.gif";
N30 = new Image();
N30.src = "<?=$url; ?><?=$ums_rasse; ?>_30.gif";
H30 = new Image();
H30.src = "<?=$url; ?><?=$ums_rasse; ?>_30_h.gif";
N31 = new Image();
N31.src = "<?=$url; ?><?=$ums_rasse; ?>_33.gif";
H31 = new Image();
H31.src = "<?=$url; ?><?=$ums_rasse; ?>_33_h.gif";
N32 = new Image();
N32.src = "<?=$url; ?><?=$ums_rasse; ?>_34.gif";
H32 = new Image();
H32.src = "<?=$url; ?><?=$ums_rasse; ?>_34_h.gif";
function B(Bildnr,Bildobjekt)
{
	window.document.images[Bildnr].src = Bildobjekt.src;
}

var aktScroll;
var aktMinTop = 0;
var scrollStep = 7;
var btnPic = new Array();
var btnStat = new Array();
var isAktive = "no";

btnPic["Up"] = 32;
btnPic["Down"] = 31;

function StartScroll(sDir) {
	if (aktMinTop == 0) { aktMinTop = document.getElementById('menumain').clientHeight - document.getElementById('menubtns').clientHeight }
	aktScroll = window.setInterval("DoScroll('"+sDir+"')", 25);
	document.getElementById("div"+sDir).style.backgroundImage = "url(<?=$url?><?=$ums_rasse?>_"+btnPic[sDir]+"_h.gif)";
	isAktive = "yes";
}

function DoScroll(sDir) {
	var aktTop = document.getElementById("menubtns").style.top
	if (sDir == "Up") { var newTop = (aktTop.replace("px", "") * 1) + scrollStep; }
	 else { var newTop = (aktTop.replace("px", "") * 1) - scrollStep; }
	if (((sDir == "Down") && (newTop < aktMinTop)) || ((sDir == "Up") && (newTop > 0))) {
		newTop = (sDir == "Up") ? 0 : aktMinTop;
		document.getElementById("div"+sDir).style.backgroundImage = "url(<?=$url?><?=$ums_rasse?>_"+btnPic[sDir]+"_o.gif)";
		btnStat[sDir] = "off";
		window.clearInterval(aktScroll);
		isAktive = "no";
	}
	else { btnStat[sDir] = "on"; }
	var oppDir = (sDir == "Up") ? "Down" : "Up";
	document.getElementById("div"+oppDir).style.backgroundImage = "url(<?=$url?><?=$ums_rasse?>_"+btnPic[oppDir]+".gif)";
	document.getElementById("menubtns").style.top = newTop + "px";
}

function StopScroll() {
	if (isAktive == "yes") { window.clearInterval(aktScroll); }
	if (btnStat["Up"] == "on") { document.getElementById("divUp").style.backgroundImage = "url(<?=$url?><?=$ums_rasse?>_"+btnPic["Up"]+".gif)"; }
	if (btnStat["Down"] == "on") { document.getElementById("divDown").style.backgroundImage = "url(<?=$url?><?=$ums_rasse?>_"+btnPic["Down"]+".gif)"; }
}

function showefta()
{
  <?php
  if($_SESSION["ums_chatoff"]) echo "top.document.getElementById('gf').cols = '0, 0, *, 0'";
  else echo "top.document.getElementById('gf').cols = '0, 0, 0, *, 0'";
  ?>
}

function showsou()
{
  <?php
  if($_SESSION["ums_chatoff"]) echo "top.document.getElementById('gf').cols = '0, 0, 0, *'";
  else echo "top.document.getElementById('gf').cols = '0, 0, 0, 0, *'";
  ?>
}

</script>
</head>
<body style="background-image:url(<?=$bgurl; ?>bg.gif); background-attachment:fixed;" BGCOLOR="#000000" leftmargin="0" topmargin="0" TEXT="#3399FF" LINK="#3399FF" VLINK="#3399FF" ALINK="#3399FF" bgproperties="FIXED">
<img src="<?=$url; ?><?=$ums_rasse; ?>_m.gif" style="z-index: 0;">

<div id="divUp" onClick="document.getElementById('menubtns').style.top = '0px'" style="z-index: 1; position: absolute; left: 45px; top: 115px; height: 16px; width: 96px; background-image:url(<?=$url?><?=$ums_rasse?>_32_o.gif);" onMouseOver="StartScroll('Up')" onMouseOut="StopScroll()"></div>
<div id="menumain" style="z-index: 3; position: absolute; left: 45px; top: 139px; height: 358px; width: 96px; overflow: hidden;">
<div id="menubtns" style="z-index: 4; position: absolute; left: 0px; top: 0px; width: 96px;">
<font size="0">
<div align="center">
<?php
if($sv_server_lang==1)
{
  echo '<a href="#"><img src="'.$bgurl.'ql1.gif" onclick="showefta()" border="0" title="'.$menu_lang[eintrag_31].'" alt="'.$menu_lang[eintrag_31].'"></a>
<a href="#"><img src="'.$bgurl.'ql3.gif" onclick="showsou()" border="0" title="'.$menu_lang[eintrag_32].'" alt="'.$menu_lang[eintrag_32].'"></a>
  <br>';
  $c=3;
}
?>
<?php

?>
<a href="overview.php" target="h" onMouseOver="B(<?=$c++?>,H1)" onMouseOut="B(<?=$c-1?>,N1)"><img src="<?=$url; ?><?=$ums_rasse; ?>_1.gif" border="0" alt="<?=$menu_lang[eintrag_1]?>"></a><br>
<a href="hyperfunk.php" target="h" onMouseOver="B(<?=$c++?>,H2)" onMouseOut="B(<?=$c-1?>,N2)"><img src="<?=$url; ?><?=$ums_rasse; ?>_2.gif" border="0" alt="<?=$menu_lang[eintrag_2]?>"></a><br>
<a href="sysnews.php" target="h" onMouseOver="B(<?=$c++?>,H3)" onMouseOut="B(<?=$c-1?>,N3)"><img src="<?=$url; ?><?=$ums_rasse; ?>_3.gif" border="0" alt="<?=$menu_lang[eintrag_3]?>"></a><br>
<br>
<a href="buildings.php" target="h" onMouseOver="B(<?=$c++?>,H5)" onMouseOut="B(<?=$c-1?>,N5)"><img src="<?=$url; ?><?=$ums_rasse; ?>_5.gif" border="0" alt="<?=$menu_lang[eintrag_4]?>"></a><br>
<a href="research.php" target="h" onMouseOver="B(<?=$c++?>,H6)" onMouseOut="B(<?=$c-1?>,N6)"><img src="<?=$url; ?><?=$ums_rasse; ?>_6.gif" border="0" alt="<?=$menu_lang[eintrag_5]?>"></a><br>
<a href="resource.php" target="h" onMouseOver="B(<?=$c++?>,H7)" onMouseOut="B(<?=$c-1?>,N7)"><img src="<?=$url; ?><?=$ums_rasse; ?>_7.gif" border="0" alt="<?=$menu_lang[eintrag_6]?>"></a><br>
<br>
<a href="trade.php" target="h" onMouseOver="B(<?=$c++?>,H8)" onMouseOut="B(<?=$c-1?>,N8)"><img src="<?=$url; ?><?=$ums_rasse; ?>_8.gif" border="0" alt="<?=$menu_lang[eintrag_7]?>"></a><br>
<br>
<a href="production.php" target="h" onMouseOver="B(<?=$c++?>,H9)" onMouseOut="B(<?=$c-1?>,N9)"><img src="<?=$url; ?><?=$ums_rasse; ?>_9.gif" border="0" alt="<?=$menu_lang[eintrag_8]?>"></a><br>
<a href="defense.php" target="h" onMouseOver="B(<?=$c++?>,H10)" onMouseOut="B(<?=$c-1?>,N10)"><img src="<?=$url; ?><?=$ums_rasse; ?>_10.gif" border="0" alt="<?=$menu_lang[eintrag_9]?>"></a><br>
<a href="military.php" target="h" onMouseOver="B(<?=$c++?>,H11)" onMouseOut="B(<?=$c-1?>,N11)"><img src="<?=$url; ?><?=$ums_rasse; ?>_11.gif" border="0" alt="<?=$menu_lang[eintrag_10]?>"></a><br>
<a href="secret.php" target="h" onMouseOver="B(<?=$c++?>,H12)" onMouseOut="B(<?=$c-1?>,N12)"><img src="<?=$url; ?><?=$ums_rasse; ?>_12.gif" border="0" alt="<?=$menu_lang[eintrag_11]?>"></a><br>
<br>
<a href="sector.php" target="h" onMouseOver="B(<?=$c++?>,H13)" onMouseOut="B(<?=$c-1?>,N13)"><img src="<?=$url; ?><?=$ums_rasse; ?>_13.gif" border="0" alt="<?=$menu_lang[eintrag_12]?>"></a><br>
<a href="secstatus.php" target="h" onMouseOver="B(<?=$c++?>,H14)" onMouseOut="B(<?=$c-1?>,N14)"><img src="<?=$url; ?><?=$ums_rasse; ?>_14.gif" border="0" alt="<?=$menu_lang[eintrag_13]?>"></a><br>
<a href="secforum.php" target="h" onMouseOver="B(<?=$c++?>,H15)" onMouseOut="B(<?=$c-1?>,N15)"><img src="<?=$url; ?><?=$ums_rasse; ?>_15.gif" border="0" alt="<?=$menu_lang[eintrag_14]?>"></a><br>
<a href="politics.php" target="h" onMouseOver="B(<?=$c++?>,H16)" onMouseOut="B(<?=$c-1?>,N16)"><img src="<?=$url; ?><?=$ums_rasse; ?>_16.gif" border="0" alt="<?=$menu_lang[eintrag_15]?>"></a><br>
<br>
<a href="allymain.php" target="h" onMouseOver="B(<?=$c++?>,H28)" onMouseOut="B(<?=$c-1?>,N28)"><img src="<?=$url; ?><?=$ums_rasse; ?>_28.gif" border="0" alt="<?=$menu_lang[eintrag_16]?>"></a><br>
<br>
<?php
/*
<a href="eftastart.php" target="h" onMouseOver="B(<?=$c++?>,H4)" onMouseOut="B(<?=$c-1?>,N4)"><img src="<?=$url; ?><?=$ums_rasse; ?>_4.gif" border="0" alt="<?=$menu_lang[eintrag_17]?>"></a><br>
*/
?>
<a href="artefacts.php" target="h" onMouseOver="B(<?=$c++?>,H26)" onMouseOut="B(<?=$c-1?>,N26)"><img src="<?=$url; ?><?=$ums_rasse; ?>_26.gif" border="0" alt="<?=$menu_lang[eintrag_18]?>"></a><br>
<a href="archeology.php" target="h" onMouseOver="B(<?=$c++?>,H31)" onMouseOut="B(<?=$c-1?>,N31)"><img src="<?=$url; ?><?=$ums_rasse; ?>_33.gif" border="0" alt="<?=$menu_lang[eintrag_19]?>"></a><br>
<br>
<a href="dezindex.php" target="h" onMouseOver="B(<?=$c++?>,H24)" onMouseOut="B(<?=$c-1?>,N24)"><img src="<?=$url; ?><?=$ums_rasse; ?>_24.gif" border="0" alt="<?=$menu_lang[eintrag_20]?>"></a><br>
<br>
<a href="statistics.php" target="h" onMouseOver="B(<?=$c++?>,H30)" onMouseOut="B(<?=$c-1?>,N30)"><img src="<?=$url; ?><?=$ums_rasse; ?>_30.gif" border="0" alt="<?=$menu_lang[eintrag_21]?>"></a><br>
<a href="toplist.php" target="h" onMouseOver="B(<?=$c++?>,H17)" onMouseOut="B(<?=$c-1?>,N17)"><img src="<?=$url; ?><?=$ums_rasse; ?>_17.gif" border="0" alt="<?=$menu_lang[eintrag_22]?>"></a><br>
<br>
<a href="irc/irc.php" target="_blank" onMouseOver="B(<?=$c++?>,H27)" onMouseOut="B(<?=$c-1?>,N27)"><img src="<?=$url; ?><?=$ums_rasse; ?>_27.gif" border="0" alt="<?=$menu_lang[eintrag_23]?>"></a><br>
<a href="options.php" target="h" onMouseOver="B(<?=$c++?>,H18)" onMouseOut="B(<?=$c-1?>,N18)"><img src="<?=$url; ?><?=$ums_rasse; ?>_18.gif" border="0" alt="<?=$menu_lang[eintrag_24]?>"></a><br>
<br>
<?php
/*
<a href="http://portal.die-ewigen.com/" target="_blank" onMouseOver="B(<?=$c++?>,H25)" onMouseOut="B(<?=$c-1?>,N25)"><img src="<?=$url; ?><?=$ums_rasse; ?>_25.gif" border="0" alt="<?=$menu_lang[eintrag_25]?>"></a><br>
*/
?>
<a href="<?=$sv_link[2]?>" target="_blank" onMouseOver="B(<?=$c++?>,H20)" onMouseOut="B(<?=$c-1?>,N20)"><img src="<?=$url; ?><?=$ums_rasse; ?>_20.gif" border="0" alt="<?=$menu_lang[eintrag_26]?>"></a><br>
<a href="<?php print($sv_link[4]); ?>" target="_blank" onMouseOver="B(<?=$c++?>,H21)" onMouseOut="B(<?=$c-1?>,N21)"><img src="<?=$url; ?><?=$ums_rasse; ?>_21.gif" border="0" alt="<?=$menu_lang[eintrag_27]?>"></a><br>
<br>

<a href="index.php?logout=1" target="h" onMouseOver="B(<?=$c++?>,H19)" onMouseOut="B(<?=$c-1?>,N19)"><img src="<?=$url; ?><?=$ums_rasse; ?>_19.gif" border="0" alt="<?=$menu_lang[eintrag_29]?>"></a>
</div>
</font>
</div>
</div>
<div id="divDown" onClick="document.getElementById('menubtns').style.top = aktMinTop+'px'" style="z-index: 2; position: absolute; left: 45px; top: 504px; height: 16px; width: 96px; background-image:url(<?=$url?><?=$ums_rasse?>_31.gif);" onMouseOver="StartScroll('Down')" onMouseOut="StopScroll()"></div>
</body>
</html>
