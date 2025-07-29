<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/achievement.inc.php';
include "functions.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, buildgnr, buildgtime, newtrans, newnews, design1 AS design, efta_user_id, tick, specreset, spec1, spec2, spec3, spec4, spec5 FROM de_user_data WHERE user_id=?", [$ums_user_id]);
$row = mysqli_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];
$punkte=$row["score"];$techs=$row["techs"];$buildgnr=$row["buildgnr"];
$verbtime=$row["buildgtime"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];$efta_user_id=$row["efta_user_id"];
$tick=$row['tick'];

$specreset=$row['specreset'];
$spec[0]=$row['spec1'];
$spec[1]=$row['spec2'];
$spec[2]=$row['spec3'];
$spec[3]=$row['spec4'];
$spec[4]=$row['spec5'];

$resettime=480;

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Spezialisierung</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

//stelle die ressourcenleiste dar
include "resline.php";

if(isset($_REQUEST['reset'])){
	$verbtime=$resettime-($tick-$specreset);
	if($verbtime<1)
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET specreset=?, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0 WHERE user_id=?", [$tick, $ums_user_id]);
	$spec[0]=0;
	$spec[1]=0;
	$spec[2]=0;
	$spec[3]=0;
	$spec[4]=0;
}

//grenzen für die einzelnen stufen anhand der möglichen errungenschaften berechnen
//echo $max_achievement_points;	
$needa=array(
round($max_achievement_points/30),
round($max_achievement_points/12.3),
round($max_achievement_points/6.16),
round($max_achievement_points/3.08),
round($max_achievement_points/1.54));
//beschreibungen der einzelnen auswahlmäglichkeiten
$specdesc[0][0]='Verringert die Bauzeit von Verteidigungseinheiten um 50%. Erg&auml;nzt sich mit der Erfahrungspunkte-Bauzeitreduzierung, wobei die Bauzeit nicht kleiner als 1 WT sein kann.';
$specdesc[0][1]='Verteidigungsanlagen erhalten bei K&auml;mpfen 50% mehr Erfahrungspunkte (wirkt sich auch auf den Erhalt von Kriegsartefakten aus).';
$specdesc[0][2]='Der planetare Schutzschild und dessen Erweiterung werden um 10% st&auml;rker.';
$specdesc[0][3]='Die Chance, dass feindliche Agenten Erfolg haben, sinkt um absolut 5%. Z.B. wird aus einer Erfolgschance von 78% eine Erfolgschance von 73%.';
$specdesc[0][4]='Deine Technologien sind vor Sabotageaktionen gesch&uuml;tzt.';

$specdesc[1][0]='Verringert die Bauzeit von Flotteneinheiten um 50%, wobei die Bauzeit nicht kleiner als 1 WT sein kann.';
$specdesc[1][1]='Flotteneinheiten erhalten 10% mehr Erfahrungspunkte (wirkt sich auch auf den Erhalt von Kriegsartefakten aus).';
$specdesc[1][2]='Flotteneinheiten erhalten eine 20% erh&ouml;hte Tr&auml;gerkapazit&auml;t.';
$specdesc[1][3]='Die Dauer von Missionen wird um 10% verk&uuml;rzt.';
$specdesc[1][4]='Die R&uuml;ckreisezeit der Flotte beim Befehl Heimkehr wird um einen Kampftick verringert.';

$specdesc[2][0]='Kollektoren kosten f&uuml;r alle Sektormitglieder 2% weniger Rohstoffe. Summiert sich wenn mehr Spieler im Sektor diese Auswahl treffen (Maximum: 20%).';
$specdesc[2][1]='Sektorraumschiffe kosten 2% weniger Rohstoffe. Summiert sich wenn mehr Spieler im Sektor diese Auswahl treffen (Maximum: 20%).';
$specdesc[2][2]='Der planetare Rohstoffertrag aller Sektormitglieder wird um 10% erh&ouml;ht. Summiert sich wenn mehr Spieler im Sektor diese Auswahl treffen (Maximum: 100%).';
$specdesc[2][3]='Das Recycling im Heimatsystem der Sektormitglieder ist um 1% erh&ouml;ht. Summiert sich wenn mehr Spieler im Sektor diese Auswahl treffen (Maximum: 10%).';
$specdesc[2][4]='Die Sektorraumbasis erh&auml;lt permanent den Rohstoffertrag von 10 Kollektoren. Summiert sich wenn mehr Spieler im Sektor diese Auswahl treffen (Maximum: 100 Sektorkollektoren).';
//errungenschaften auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT (ac1+ac2+ac3+ac4+ac5+ac6+ac7+ac8+ac9+ac10+ac11+ac12+ac13+ac14+ac15+ac16+ac17+ac18+ac19+ac20+ac21+ac22+ac23+ac24+ac25) AS wert FROM de_user_achievement WHERE user_id=?", [$ums_user_id]);
$num = mysqli_num_rows($db_daten);
if($num==1){
  $row = mysqli_fetch_array($db_daten);
  $achievements=$row["wert"];
}
else{
	$achievements=0;
} 

if(isset($_REQUEST['level'])){
	$level=intval($_REQUEST['level']);
	$choose=intval($_REQUEST['choose']);	
	
	//hat man die benötigten Achievements?
	if($achievements>=$needa[$level-1]){

		if($level>0 AND $level<6 AND $choose>0 AND $choose<4)	{
			if($spec[$level-1]==0)		{
				$spec[$level-1]=$choose;
				//db updaten
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET spec".$level."=? WHERE user_id=?", [$choose, $ums_user_id]);
			}		
		}
	}
}

//echo '<div class="info_box" style="font-size: 20px;">Dies sind die vorl�ufig geplanten Spezialisierungen. Vor Einbau wird um Feedback gebeten, damit diese ggf. noch angepa�t werden k�nnen. Bitte die Feedback-Funktion bei den News verwenden, oder im Forum im Spezialisierungen-Diskussionsthread posten.</div><br>';

rahmen_oben('Spezialisierung <img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" title="Die einzelnen Spezialisierungen werden mit Hilfe von Errungenschaften freigeschaltet. Die Zahl gibt an wie viele Errungenschaften ben&ouml;tigt werden. Nach der Freischaltung kann eine von den jeweils drei Spezialisierungen gew&auml;hlt werden.">');
echo '<div class="bgpic3" style="width: 566px; height: 432px; position: relative;">';

$specboni=array(2,2,10,1,10);

$buttontexte=array('I','II','III','IV','V');

$link=array();
for($i=0;$i<5;$i++){
	$link[0]='';
	$link[1]='';
	$link[2]='';
	$linkende='';
	
	//farbiger hintergrund/beschreibung
	if($achievements>=$needa[$i]){
		$bgcolor='#00AA00';
		$zeiledesc='Dieser Bereich ist freigeschaltet.';
		if($spec[$i]==0){
			$link[0]='<a href="specialization.php?level='.($i+1).'&choose=1">';
			$link[1]='<a href="specialization.php?level='.($i+1).'&choose=2">';
			$link[2]='<a href="specialization.php?level='.($i+1).'&choose=3">';
			$linkende='</a>';
		}
		//upgradeinfo
		if($spec[$i]==0) $upgradeinfo='<div style="bottom: 0px; position: absolute; margin-left: 125px;">Du kannst eine der drei Spezialisierungen ausw&auml;hlen.</div>';
		else $upgradeinfo='';
	}else{ 
		
		$bgcolor='#AA0000';
		$zeiledesc='Du hast leider erst '.$achievements.' von '.$needa[$i].' ben&ouml;tigten Errungenschaftspunkten um diesen Bereich freizuschalten.';
		
		$upgradeinfo='';
		$status='<br>Status: inaktiv';
	}
	
	$buttontext=$buttontexte[$i];
	
	echo '<div style="height: 82px; border: 1px solid '.$bgcolor.'; margin-bottom: 3px; text-align: center; position: relative;">';
	//zeile darstellen
	//echo '<div class="bgpic3" style="width: 100%; height: 100%;">';
	
	//info, dass man eins auswählen kann, wenn noch nichts gewählt wurde
	echo $upgradeinfo;
	
	//benötigte achievement-punkte
	echo '<div style="float: left; width: 130px; font-size: 20px; text-align: center; padding-top: 30px; color: '.$bgcolor.';" title="'.$zeiledesc.'">'.$needa[$i].'</div>';
	
	//1. spalte
	if(($spec[$i]==1 OR $spec[$i]==0) AND $achievements>=$needa[$i]){$picsize=50; $cssfontsize=26; $csspadding=9;}else {$picsize=30; $cssfontsize=10; $csspadding=9; $cssletterspacing=0;}
	if($spec[$i]==1 OR $spec[$i]==0 AND $i==2)$cssletterspacing=-4;else $cssletterspacing=0;
	if($spec[$i]==1){$status='<br>Status: aktiv';}else {$status='<br>Status: inaktiv';}
	echo '<div style="float: left; width: 140px; height: 66px; padding-top: 15px; position: relative;">
	'.$link[0].'<div style="position: absolute; width: 100%; height: 100%; z-index: 1;"><img src="'.$ums_gpfad.'g/symbol21.png" border="0" width="'.$picsize.'px" heigth="'.$picsize.'px"></div>
	<div title="'.$specdesc[0][$i].$status.'" style="left: -1px; position: absolute; width: '.$picsize.'px; height: '.$picsize.'px; margin-left: '.((140-$picsize)/2).'px; z-index: 2; font-weight: bold; font-size: '.$cssfontsize.'px; color: #000000; padding-top: '.$csspadding.'px; font-family: Courier New;letter-spacing: '.$cssletterspacing.'px;">'.$buttontext.'</div>
	'.$linkende.
	'</div>';
	
	//2. spalte
	if(($spec[$i]==2 OR $spec[$i]==0) AND $achievements>=$needa[$i]){$picsize=50; $cssfontsize=26; $csspadding=9;}else {$picsize=30; $cssfontsize=10; $csspadding=9; $cssletterspacing=0;}
	if($spec[$i]==2 OR $spec[$i]==0 AND $i==2)$cssletterspacing=-4;else $cssletterspacing=0;
	if($spec[$i]==2){$status='<br>Status: aktiv';}else {$status='<br>Status: inaktiv';}
	echo '<div style="float: left; width: 140px; height: 66px; padding-top: 15px; position: relative;">
	'.$link[1].'<div style="position: absolute; width: 100%; height: 100%; z-index: 1;"><img src="'.$ums_gpfad.'g/symbol22.png" border="0" width="'.$picsize.'px" heigth="'.$picsize.'px"></div>
	<div title="'.$specdesc[1][$i].$status.'" style="left: -1px; position: absolute; width: '.$picsize.'px; height: '.$picsize.'px; margin-left: '.((140-$picsize)/2).'px; z-index: 2; font-weight: bold;  font-size: '.$cssfontsize.'px; color: #000000; padding-top: '.$csspadding.'px; font-family: Courier New;letter-spacing: '.$cssletterspacing.'px;">'.$buttontext.'</div>
	'.$linkende.
	'</div>';
	
	//3. spalte
	//auslesen wie oft es gewählt worden ist
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec".($i+1)."=3", [$sector]);
	$bonuswert = ' Aktueller Wert: '.mysqli_num_rows($db_daten) * $specboni[$i];
	if($i!=4){
		$bonuswert.='%';	
	}

	if(($spec[$i]==3 OR $spec[$i]==0) AND $achievements>=$needa[$i]){$picsize=50; $cssfontsize=26; $csspadding=9;}else {$picsize=30; $cssfontsize=10; $csspadding=9; $cssletterspacing=0;}
	if($spec[$i]==3 OR $spec[$i]==0 AND $i==2)$cssletterspacing=-4;else $cssletterspacing=0;
	if($spec[$i]==3){$status='<br>Status: aktiv';}else {$status='<br>Status: inaktiv';}
	echo '<div style="float: left; width: 140px; height: 66px; padding-top: 15px; position: relative;">
	'.$link[2].'<div style="position: absolute; width: 100%; height: 100%; z-index: 1;"><img src="'.$ums_gpfad.'g/symbol23.png" border="0" width="'.$picsize.'px" heigth="'.$picsize.'px"></div>
	<div title="'.$specdesc[2][$i].$bonuswert.$status.'" style="left: -1px; position: absolute; width: '.$picsize.'px; height: '.$picsize.'px; margin-left: '.((140-$picsize)/2).'px; z-index: 2;  font-weight: bold; font-size: '.$cssfontsize.'px; color: #000000; padding-top: '.$csspadding.'px; font-family: Courier New;letter-spacing: '.$cssletterspacing.'px;">'.$buttontext.'</div>
	'.$linkende.
	'</div>';
	

	
	//echo '</div>';

	echo '</div>';
	
}
echo '</div>';

rahmen_unten();

//resetzeit berechnen
$verbtime=$resettime-($tick-$specreset);
if($verbtime<1){
	$verbtime='sofort';
	$resetlink='<br><a href="specialization.php?reset=1" onclick="return confirm(\'Bist Du Dir sicher?\')">Spezialisierungen zur&uuml;cksetzen</a>';
}else{
	$verbtime=$verbtime.' WT';
	$resetlink='';
}

echo '<div class="info_box text1" style="font-size: 12px;">Die Auswahl kann alle 480 Wirtschaftsticks kostenlos zur&uuml;ckgesetzt und danach neu vergeben werden.<br>
N&auml;chster m&ouml;glicher Resetzeitpunkt: '.$verbtime.$resetlink.' 
</div><br>';

?>
<?php include "fooban.php"; ?>
</body>
</html>
