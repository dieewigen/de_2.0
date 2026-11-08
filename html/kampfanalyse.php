<?php
include 'inccon.php';
include 'functions.php';
include 'tickler/kt_einheitendaten.php';


echo '<!DOCTYPE HTML>
<html>
<head>
<title>Kampfanalyse</title>
</head>
<body>';

$einheitentypen=array('J&auml;ger','Jagdboot','Zerst&ouml;rer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff', 'Tr&auml;ger', 'Frachter', 'Titan', 'J&auml;gergarnison', 'Raketenturm', 'Laserturm', 'Autokanonenturm', 'Plasmaturm');

echo '<table border="1" width="100%">';

for($e=0;$e<count($einheitentypen);$e++){
	//einheitentyp
	echo '<tr style="background-color: #DDDDDD; font-weight: bold;"><td>'.$einheitentypen[$e].'</td><td>E</td><td>I</td><td>K</td><td>Z</td><td>D</td></tr>';

	//punkte
	echo '<tr align="right">
		<td align="left">Punkte</td>
		<td>'.$unit[0][$e][4].'</td>
		<td>'.$unit[1][$e][4].'</td>
		<td>'.$unit[2][$e][4].'</td>
		<td>'.$unit[3][$e][4].'</td>
		<td>'.$unit[4][$e][4].'</td>
	</tr>';
	//hitpoints
	echo '<tr align="right">
		<td align="left">Hitpoints</td>
		<td>'.$unit[0][$e][1].' ('.number_format($unit[0][$e][1]*100/$unit[0][$e][4],2,',','.').'%)</td>
		<td>'.$unit[1][$e][1].' ('.number_format($unit[1][$e][1]*100/$unit[1][$e][4],2,',','.').'%)</td>
		<td>'.$unit[2][$e][1].' ('.number_format($unit[2][$e][1]*100/$unit[2][$e][4],2,',','.').'%)</td>
		<td>'.$unit[3][$e][1].' ('.number_format($unit[3][$e][1]*100/$unit[3][$e][4],2,',','.').'%)</td>
		<td>'.$unit[4][$e][1].' ('.number_format($unit[4][$e][1]*100/$unit[4][$e][4],2,',','.').'%)</td>
	</tr>';	
	//feuerkraft
	echo '<tr align="right" style="background-color: #EE5555;">
		<td align="left">Feuerkraft</td>
		<td>'.$unit[0][$e][2].' ('.number_format($unit[0][$e][2]*100/$unit[0][$e][4],2,',','.').'%)</td>
		<td>'.$unit[1][$e][2].' ('.number_format($unit[1][$e][2]*100/$unit[1][$e][4],2,',','.').'%)</td>
		<td>'.$unit[2][$e][2].' ('.number_format($unit[2][$e][2]*100/$unit[2][$e][4],2,',','.').'%)</td>
		<td>'.$unit[3][$e][2].' ('.number_format($unit[3][$e][2]*100/$unit[3][$e][4],2,',','.').'%)</td>
		<td>'.$unit[4][$e][2].' ('.number_format($unit[4][$e][2]*100/$unit[4][$e][4],2,',','.').'%)</td>
	</tr>';
	//blockwert
	echo '<tr align="right" style="background-color: #8888EE;">
		<td align="left">Blockkraft</td>
		<td>'.$unit[0][$e][3].' ('.number_format($unit[0][$e][3]*100/$unit[0][$e][4],2,',','.').'%)</td>
		<td>'.$unit[1][$e][3].' ('.number_format($unit[1][$e][3]*100/$unit[1][$e][4],2,',','.').'%)</td>
		<td>'.$unit[2][$e][3].' ('.number_format($unit[2][$e][3]*100/$unit[2][$e][4],2,',','.').'%)</td>
		<td>'.$unit[3][$e][3].' ('.number_format($unit[3][$e][3]*100/$unit[3][$e][4],2,',','.').'%)</td>
		<td>'.$unit[4][$e][3].' ('.number_format($unit[4][$e][3]*100/$unit[4][$e][4],2,',','.').'%)</td>
	</tr>';	

}
echo '</table>';

echo '</body></html>';
?>