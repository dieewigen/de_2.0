<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/achievement.inc.php';
include "functions.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, buildgnr, buildgtime, newtrans, newnews, design1 AS design, efta_user_id, tick, specreset, spec1, spec2, spec3, spec4, spec5 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
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
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar
include "resline.php";

if(isset($_REQUEST['reset'])){
	$verbtime=$resettime-($tick-$specreset);
	//nur zurücksetzen (und so anzeigen), wenn die Sperrzeit abgelaufen ist
	if($verbtime<1){
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET specreset=?, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0 WHERE user_id=?", [$tick, $_SESSION['ums_user_id']]);
		$spec=array(0,0,0,0,0);
		$specreset=$tick;
	}
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
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT (ac1+ac2+ac3+ac4+ac5+ac6+ac7+ac8+ac9+ac10+ac11+ac12+ac13+ac14+ac15+ac16+ac17+ac18+ac19+ac20+ac21+ac22+ac23+ac24+ac25) AS wert FROM de_user_achievement WHERE user_id=?", [$_SESSION['ums_user_id']]);
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
	$choose=intval($_REQUEST['choose'] ?? 0);

	//gültige Stufe/Auswahl und die benötigten Achievements
	if($level>0 AND $level<6 AND $choose>0 AND $choose<4 AND $achievements>=$needa[$level-1]){
		if($spec[$level-1]==0){
			//nur setzen, wenn in dieser Stufe noch nichts gewählt ist (auch bei zwei gleichzeitigen Anfragen)
			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET spec".$level."=? WHERE user_id=? AND spec".$level."=0", [$choose, $_SESSION['ums_user_id']]);
			if(mysqli_affected_rows($GLOBALS['dbi'])==1){
				$spec[$level-1]=$choose;
			}
		}
	}
}

//echo '<div class="info_box" style="font-size: 20px;">Dies sind die vorl�ufig geplanten Spezialisierungen. Vor Einbau wird um Feedback gebeten, damit diese ggf. noch angepa�t werden k�nnen. Bitte die Feedback-Funktion bei den News verwenden, oder im Forum im Spezialisierungen-Diskussionsthread posten.</div><br>';

rahmen_oben('Spezialisierung <img src="'.'gp/'.'g/'.$_SESSION['ums_rasse'].'_hilfe.gif" title="Die einzelnen Spezialisierungen werden mit Hilfe von Errungenschaften freigeschaltet. Die Zahl gibt an wie viele Errungenschaften ben&ouml;tigt werden. Nach der Freischaltung kann eine von den jeweils drei Spezialisierungen gew&auml;hlt werden.">');

$specboni=array(2,2,10,1,10);

$buttontexte=array('I','II','III','IV','V');

//Spalten und Kurzbezeichnungen für die Übersicht; die vollständige Beschreibung steht in $specdesc
$spectitel=array('Verteidigung', 'Flotte', 'Sektor');
$specbild=array('symbol21.png', 'symbol22.png', 'symbol23.png');
$speckurz[0]=array('Bauzeit Verteidigung &minus;50%', 'Erfahrung Verteidigung +50%', 'Schutzschild +10%', 'Agentenabwehr +5%', 'Schutz vor Sabotage');
$speckurz[1]=array('Bauzeit Flotte &minus;50%', 'Erfahrung Flotte +10%', 'Tr&auml;gerkapazit&auml;t +20%', 'Missionsdauer &minus;10%', 'Heimkehr 1 KT schneller');
$speckurz[2]=array('Kollektorkosten &minus;2%', 'Sektorschiffkosten &minus;2%', 'Planetarer Ertrag +10%', 'Recycling +1%', '+10 Sektorkollektoren');

//Auswahl erst über den Button in der Beschreibung, ein Tipp auf den Kreis zeigt nur die Beschreibung an
echo '<div class="spec">';
echo '<div class="spec-hinweis">Tippe auf eine Spezialisierung, um ihre Beschreibung zu sehen. Gew&auml;hlt wird erst mit dem Button darunter.</div>';
echo '<div class="spec-kopf"><div class="spec-need">Errungenschaften<br><span>du hast '.number_format($achievements, 0, '', '.').'</span></div>';
foreach($spectitel as $titel){
	echo '<div class="spec-spalte">'.$titel.'</div>';
}
echo '</div>';

for($i=0;$i<5;$i++){
	$frei=($achievements>=$needa[$i]);
	$offen=($frei && $spec[$i]==0);

	if(!$frei){
		$zeilenklasse='spec-gesperrt';
		$zeilenstatus='gesperrt';
		$zeiledesc='Du hast leider erst '.$achievements.' von '.$needa[$i].' ben&ouml;tigten Errungenschaftspunkten um diesen Bereich freizuschalten.';
	}elseif($offen){
		$zeilenklasse='spec-offen';
		$zeilenstatus='w&auml;hlbar';
		$zeiledesc='Dieser Bereich ist freigeschaltet. Du kannst eine der drei Spezialisierungen ausw&auml;hlen.';
	}else{
		$zeilenklasse='spec-frei';
		$zeilenstatus='gew&auml;hlt';
		$zeiledesc='Dieser Bereich ist freigeschaltet.';
	}

	echo '<div class="spec-row '.$zeilenklasse.'">';
	echo '<div class="spec-need" title="'.$zeiledesc.'">'.$needa[$i].'<br><span>'.$zeilenstatus.'</span></div>';

	//Sektor-Spalte: wie oft im Sektor gewählt
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND spec".($i+1)."=3", [$sector]);
	$bonuswert = ' Aktueller Wert: '.mysqli_num_rows($db_daten) * $specboni[$i];
	if($i!=4){
		$bonuswert.='%';
	}

	$details='';
	for($j=0;$j<3;$j++){
		$aktiv=($spec[$i]==$j+1);
		//klein und gedämpft: gesperrt, oder in dieser Stufe ist etwas anderes gewählt
		$aus=(!$frei || ($spec[$i]!=0 && !$aktiv));
		$klasse='spec-opt'.($aktiv ? ' spec-aktiv' : '').($aus ? ' spec-aus' : '');
		$kreisklasse='spec-kreis'.(strlen($buttontexte[$i])>2 ? ' spec-kreis-eng' : '');

		echo '<button type="button" class="'.$klasse.'" data-spec="'.$i.'_'.$j.'">
			<span class="'.$kreisklasse.'" style="background-image: url(gp/g/'.$specbild[$j].');">'.$buttontexte[$i].'</span>
			<span class="spec-kurz">'.$speckurz[$j][$i].'</span>
		</button>';

		if($aktiv){
			$status='<span class="text3">aktiv</span>';
		}elseif(!$frei){
			$status='gesperrt &ndash; du hast erst '.$achievements.' von '.$needa[$i].' ben&ouml;tigten Errungenschaften';
		}elseif($offen){
			$status='noch nicht gew&auml;hlt';
		}else{
			$status='nicht gew&auml;hlt, in dieser Stufe ist bereits eine andere Spezialisierung aktiv';
		}

		$details.='<div class="spec-detail" id="spec_detail_'.$i.'_'.$j.'" hidden>
			<b>'.$spectitel[$j].' '.$buttontexte[$i].': '.$speckurz[$j][$i].'</b><br>
			'.$specdesc[$j][$i].($j==2 ? $bonuswert : '').'<br>
			Status: '.$status;
		if($offen){
			$details.='<form method="post" action="specialization.php">
				<input type="hidden" name="level" value="'.($i+1).'">
				<input type="hidden" name="choose" value="'.($j+1).'">
				<input type="submit" value="Diese Spezialisierung w&auml;hlen">
			</form>';
		}
		$details.='</div>';
	}

	echo $details;
	echo '</div>';
}
echo '</div>';

//Beschreibung ein-/ausblenden, in einer Stufe immer nur eine
echo '<script>
document.querySelectorAll(".spec-opt").forEach(function(b){
	b.addEventListener("click", function(){
		var row=b.closest(".spec-row");
		var ziel=document.getElementById("spec_detail_"+b.getAttribute("data-spec"));
		var warOffen=!ziel.hidden;
		row.querySelectorAll(".spec-detail").forEach(function(d){ d.hidden=true; });
		row.querySelectorAll(".spec-opt").forEach(function(o){ o.classList.remove("spec-markiert"); });
		if(!warOffen){ ziel.hidden=false; b.classList.add("spec-markiert"); }
	});
});
</script>';

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

</body>
</html>
