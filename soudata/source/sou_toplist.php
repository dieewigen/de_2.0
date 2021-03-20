<?php
include "soudata/defs/resources.inc.php";

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
// fraktionsdatenmenü
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

echo '<div class="menurahmen" style="text-align: center; margin-top: 26px;">
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=systempage"><div class="b1">zur&uuml;ck</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=toplistpage"><div class="b1">Rangliste</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=statisticspage"><div class="b1">Statistiken</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=3"><div class="b1">Siegbedingungen</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=0"><div class="b1">Kolonien</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=1"><div class="b1">Geb&auml;ude</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=2"><div class="b1">Bao-Nada-Skala</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=4"><div class="b1">Systeminfo</div></a></div>
</div>';
rahmen0_unten();


//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

echo '<br>';
/*
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die größten raumschiffe
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Die gr&ouml;&szlig;ten Raumschiffe</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Fraktion</b></td>
<td><b>Durchmesser</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` ORDER BY shipdiameter DESC, user_id ASC LIMIT 10",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //fraktion
  $output.='<td>'.$row["fraction"].'</td>';
  //durchmesser
  $output.='<td align="right">'.number_format($row["shipdiameter"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die reichsten bürger
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Die reichsten B&uuml;rger</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Fraktion</b></td><td><b>Zastari</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` ORDER BY money DESC LIMIT 10",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //fraktion
  $output.='<td>'.$row["fraction"].'</td>';
  //geld
  $output.='<td align="right">'.number_format($row["money"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';
*/
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die die nobelsten spender
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
/*
rahmen1_oben('<div align="center"><b>Die nobelsten Spender von F'.$player_fraction.'</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Spendenwert</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` WHERE fraction='$player_fraction' ORDER BY donate DESC LIMIT 20",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //fraktion
  //$output.='<td>'.$row["fraction"].'</td>';
  //geld
  $output.='<td align="right">'.number_format($row["donate"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';
*/
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// raumtrümmer geborgen
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Raumtr&uuml;mmer in den Sektoren geborgen (alle Fraktionen)</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Fraktion</b></td><td><b>Bergungen</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` ORDER BY foundfind DESC LIMIT 20",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //fraktion
  $output.='<td>'.$row["fraction"].'</td>';
  //geld
  $output.='<td align="right">'.number_format($row["foundfind"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// raumtrümmer geborgen
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Piratenschiffe vernichtet (alle Fraktionen)</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Fraktion</b></td><td><b>Siege</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` ORDER BY pirateskill DESC LIMIT 20",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //fraktion
  $output.='<td>'.$row["fraction"].'</td>';
  //geld
  $output.='<td align="right">'.number_format($row["pirateskill"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';


///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// sektorraumbasen übernommen
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
/*
rahmen1_oben('<div align="center"><b>Sektorraumbasen erobert von F'.$player_fraction.'</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Eroberungen</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `sou_user_data` WHERE fraction='$player_fraction'ORDER BY srbtakeover DESC LIMIT 20",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //bergbauleistung
  $output.='<td align="right">'.number_format($row["srbtakeover"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';

*/
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die größten bergbaumodulkapazitäten
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Bergbaumodulleistung pro Raumschiff von F'.$player_fraction.'</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>B&uuml;rger</b></td><td><b>Schiffsgr&ouml;&szlig;e</b></td><td><b>Modulleistung</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT sou_user_data.spielername, sou_user_data.sn_ext1, sou_user_data.shipdiameter, SUM(sou_ship_module.canmine) AS canmine FROM sou_user_data LEFT JOIN sou_ship_module ON(sou_user_data.user_id = sou_ship_module.user_id) WHERE sou_user_data.fraction = '$player_fraction' AND sou_ship_module.location=0 GROUP BY sou_user_data.user_id ORDER BY canmine DESC LIMIT 50",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //schiffsgröße
  $output.='<td align="right">'.number_format($row["shipdiameter"], 0,",",".").'</td>';
  //bergbauleistung
  $output.='<td align="right">'.number_format($row["canmine"], 0,",",".").'</td>';
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;
rahmen1_unten();

echo '<br>';


rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>