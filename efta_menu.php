<?php
//fix um die werbung von der botabfrage unabhängig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;
include 'inc/header.inc.php';
include "eftadata/source/efta_functions.php";
?>
<html>
<head>
<title>Menu</title>
<?php
$eftacss=1;
include 'cssinclude.php';
?>
</head>
<body>
<?php

//grafikpfad optimieren
$gpfad=$ums_gpfad.'e/';

//quicklink-string zusammenbauen
if($_SESSION["ums_chatoff"]) $qlstr="top.document.getElementById('gf').cols = '209, *, 0, 0';top.document.getElementById('gf').rows = '*';";
else $qlstr="top.document.getElementById('gf').cols = '209, 620, *, 0, 0';top.document.getElementById('gf').rows = '*';";

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//das menü anzeigen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


//$menutext.= '<div id="eftamenur">';
$menutext.= '<table width="100%">';
$menutext.= '<tr>';

//quicklink zu de, aber nur wenn efta nicht alleine steht
if($sv_efta_in_de==1)
{
  $menutext.= '<td align="center">';
  $menutext.= '<a href="#"><img src="'.$ums_gpfad.'g/ql2.gif" border="0" onclick="javascript:'.$qlstr.'" title="Kommandozentrale" title="Kommandozentrale"></a>';
  $menutext.= '</td>';
}

//kartenansicht
$menutext.= '<td align="center">';
$menutext.= '<a href="javascript:parent.e.document.k1.submit()"><img src="'.$gpfad.'b2.gif" border=0 title="Taste: m"></a>';
$menutext.= '</td>';
//rastplatz

$menutext.= '<td align="center">';
$menutext.= '<a href="javascript:parent.e.document.r1.submit()"><img src="'.$gpfad.'b1.gif" border=0 title="Taste: r"></a>';
$menutext.= '</td>';

//questseite
$menutext.= '<td align="center">';
$menutext.= '<a href="javascript:parent.e.document.q1.submit()"><img src="'.$gpfad.'b3.gif" border=0 title="Taste: q"></a>';
$menutext.= '</td>';

$menutext.= '</tr><tr>';

//cyborg mit rucksack und ausrüstung
$menutext.= '<td align="center">';
$menutext.= '<a href="javascript:parent.e.document.uk1.submit()"><img src="'.$gpfad.'c1.gif" border=0 title="Taste: b"></a>';
$menutext.= '</td>';

//hilfelink
$menutext.= '<td align="center">';
$menutext.= '<a href="http://help.bgam.es/index.php?thread=alu_de" target="_blank"><img src="'.$gpfad.'b4.gif" border=0 title="Hilfe"></a>';
$menutext.= '</td>';

//optionen link, jedoch nur dann, wenn efta allein steht
if($sv_efta_in_de==0)
{
  $menutext.= '<td align="center">';
  $menutext.= '<a href="eftamain.php?action=optionspage" target="e"><img src="'.$gpfad.'b6.gif" border=0 title="Optionen"></a>';
  $menutext.= '</td>';
}

//logout-link
$menutext.= '<td align="center">';
$menutext.= '<a href="index.php?logout=1"><img src="'.$gpfad.'b5.gif" border=0 title="Logout"></a>';
$menutext.= '</td>';

$menutext.= '</tr>';
$menutext.= '</table>';

//$menutext.= '</div>';

//menü ausgeben
//echo '<div align="center">';
//rahmen0_oben();
rahmen2_oben();
echo $menutext;
rahmen2_unten();
//rahmen0_unten();
//echo '<div>';

?>
</body>
</html>