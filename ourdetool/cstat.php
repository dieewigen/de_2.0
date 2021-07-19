<?php
include "../inccon.php";
include "../inc/sv.inc.php";
?>

<html>
<head>
<title>Creditstatistik<</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?
include "det_userdata.inc.php";

//anzeige wieviel credits er momentan hat
$db_data = mysql_query("SELECT * FROM de_system",$db);
$row = mysql_fetch_array($db_data);

//alle werte zusammenrechnen
$gesamt=$row[smstat1]+$row[smstat2]+$row[smstat3]+$row[smstat4]+$row[smstat5]+$row[smstat6]+$row[smstat7]+$row[smstat100]+$row[smstat101]+$row[smstat102]+$row[smstat103]+$row[smstat104]+$row[smstat105]+$row[smstat106]+$row[smstat107]+$row[smstat108]+$row[smstat109]+$row[smstat110]+$row[smstat111]+$row[smstat112]+$row[smstat113]+$row[smstat114]+$row[creditefta]+$row[creditea];

echo '<br>Kollektor: '.number_format($row[smstat1]*100/$gesamt, "2",",",".").'%';
echo '<br>Kriegsartefakt: '.number_format($row[smstat2]*100/$gesamt, "2",",",".").'%';
echo '<br>Rohstofflieferung: '.number_format($row[smstat3]*100/$gesamt, "2",",",".").'%';
echo '<br>Premiumaccount: '.number_format($row[smstat4]*100/$gesamt, "2",",",".").'%';
echo '<br>Diplomatieartefakt: '.number_format($row[smstat5]*100/$gesamt, "2",",",".").'%';
echo '<br>Palenium: '.number_format($row[smstat6]*100/$gesamt, "2",",",".").'%';
echo '<br>Tronic: '.number_format($row[smstat7]*100/$gesamt, "2",",",".").'%';
echo '<br>';

echo '<br>Pesara-Artefakt: '.number_format($row[smstat100]*100/$gesamt, "2",",",".").'%';
echo '<br>Vakara-Artefakt: '.number_format($row[smstat101]*100/$gesamt, "2",",",".").'%';
echo '<br>Geangrus-Artefakt: '.number_format($row[smstat102]*100/$gesamt, "2",",",".").'%';
echo '<br>Geabwus-Artefakt: '.number_format($row[smstat103]*100/$gesamt, "2",",",".").'%';
echo '<br>Nahelor-Artefakt: '.number_format($row[smstat104]*100/$gesamt, "2",",",".").'%';
echo '<br>Feuroka-Artefakt: '.number_format($row[smstat105]*100/$gesamt, "2",",",".").'%';
echo '<br>Bloroka-Artefakt: '.number_format($row[smstat106]*100/$gesamt, "2",",",".").'%';
echo '<br>Turak-Artefakt: '.number_format($row[smstat107]*100/$gesamt, "2",",",".").'%';
echo '<br>Turla-Artefakt: '.number_format($row[smstat108]*100/$gesamt, "2",",",".").'%';
echo '<br>Recarion-Artefakt: '.number_format($row[smstat109]*100/$gesamt, "2",",",".").'%';
echo '<br>Pekasch-Artefakt: '.number_format($row[smstat110]*100/$gesamt, "2",",",".").'%';
echo '<br>Pekek-Artefakt: '.number_format($row[smstat111]*100/$gesamt, "2",",",".").'%';
echo '<br>Empala-Artefakt: '.number_format($row[smstat112]*100/$gesamt, "2",",",".").'%';
echo '<br>Empdestro-Artefakt: '.number_format($row[smstat113]*100/$gesamt, "2",",",".").'%';
echo '<br>Recadesto-Artefakt: '.number_format($row[smstat114]*100/$gesamt, "2",",",".").'%';
//summe der artefakteinnahmen
$artsum=0;
for($i=100;$i<=114;$i++)$artsum+=$row['smstat'.$i];
echo '<br>Artefakt-Summe: '.number_format($artsum*100/$gesamt, "2",",",".").'%';



echo '<br><br>Erweiterte Arch&auml;ologie: '.number_format($row[creditea]*100/$gesamt, "2",",",".").'%';
echo '<br>EFTA: '.number_format($row[creditefta]*100/$gesamt, "2",",",".").'%';

?>
</div>
</body>
</html>

