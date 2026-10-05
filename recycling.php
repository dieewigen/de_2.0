<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include 'inc/sabotage.inc.php';
include "tickler/kt_einheitendaten.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;
$spec1=$row['spec1'];$defenseexp=$row["defenseexp"];$mysc2=$row["sc2"];

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'],"SELECT wt AS tick FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//feststellen ob die raumwerft sabotiert ist
if($maxtick<$mysc2+$sv_sabotage[8][0] AND $mysc2>$sv_sabotage[8][0])$sabotage=1;else $sabotage=0;

//test auf spezialisierung bauzeit t�rme
if($spec1==1)$defense_bonus_buildtime+=50;


$fleetabzug=0.05;
$defabzug=0.20;

///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
//alle einheitendaten für die ausgabe auslesen
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
unset($technames);
unset($techcost);
$techselect='<option value="0">Bitte w&auml;hlen</option>';
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_tech_data WHERE tech_id>80 AND tech_id<105 ORDER BY tech_id");
while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
	if($row['tech_id']<100){
		$unit_index=$row['tech_id']-81;
	}else{
		$unit_index=$row['tech_id']-90;
	}	
	
	$technames[$row['tech_id']]=getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']);
	$techcost[$row['tech_id']][0]=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][0];
	$techcost[$row['tech_id']][1]=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][1];
	$techcost[$row['tech_id']][2]=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][2];
	$techcost[$row['tech_id']][3]=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][3];
	$techcost[$row['tech_id']][4]=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][4];
	$techscore[$row['tech_id']]=$unit[$_SESSION['ums_rasse']-1][$unit_index][4];
	$techdata_ticks[$row['tech_id']]=$unit[$_SESSION['ums_rasse']-1][$unit_index]['bz'];
	
	

	//schauen ob die technlogie bereits verf�gbar ist
	if(hasTech($pt,$row['tech_id'])){
		$tech_erlaubt[$row['tech_id']]=1;
	}else{
		$tech_erlaubt[$row['tech_id']]=0;
	}
	
	//Titanen lassen sich nicht per Recycling holen
	if($row['tech_id']==90){
		$tech_erlaubt[$row['tech_id']]=0;
	}
	
	//echo $tech_erlaubt[$row['tech_id']];

	//eine select-liste f�r alle einheitentypen erstellen
	if($tech_erlaubt[$row['tech_id']]==1){
		$techselect.='<option value="'.$row['tech_id'].'">'.getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'</option>';
	}
}

//print_r($techscore);

/*
	[81] => 155 
	[82] => 550 
	[83] => 2900 
	[84] => 5900 
	[85] => 13900 
	[86] => 200 
	[87] => 400 
	[88] => 16600 
	[89] => 850 
	[90] => 122000 
	
	[100] => 1500 
	[101] => 135 
	[102] => 75 
	[103] => 260 
	[104] => 525 ) 
	
*/

///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// recyclingaufruf
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['recyclingbutton']) AND hasTech($pt,129) AND $sabotage==0){
	//transaktionsbeginn
	if (setLock($_SESSION['ums_user_id'])){
		unset($rec_gesamt);
		//zerst alle vorhandenen einheiten auslesen
		unset($einheiten);
		$fleetid=$_SESSION['ums_user_id'].'-0';
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fleetid'");
		$row = mysqli_fetch_array($db_daten);
		for($i=81;$i<=90;$i++){
			$einheiten[$i]=$row['e'.$i];
		}
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='".$_SESSION['ums_user_id']."'");
		$row = mysqli_fetch_array($db_daten);
		for($i=100;$i<=104;$i++){
			$einheiten[$i]=$row['e'.$i];
		}

		//test auf zu recycelnde flotteneinheiten
		for($i=81;$i<=90;$i++){
			$rec_amount=intval($_REQUEST["e".$i]);
			if($rec_amount>0){
				//test ob man soviel �berhaupt hat
				if($rec_amount>$einheiten[$i]){
					$rec_amount=$einheiten[$i];
				}

				//�berpr�fen in was er recyceln m�chte
				$rec_target=intval($_REQUEST["t".$i]);
				if((($rec_target>=81 AND $rec_target<=90) OR ($rec_target>=100 AND $rec_target<=104)) AND $rec_target!=$i){
					//�berpr�fen, ob man diese technologie schon nutzen kann

					if($tech_erlaubt[$rec_target]==1){
						//zielmenge berechnen
						$target_amount=floor($rec_amount*$techscore[$i]*(1-$fleetabzug)/$techscore[$rec_target]);
						$score=$target_amount*$techscore[$rec_target];
						if($target_amount>0){
							//die schiffe abziehen
							$sql="UPDATE de_user_fleet SET e".$i."=e".$i."-".$rec_amount." WHERE user_id='$fleetid'";
							mysqli_query($GLOBALS['dbi'],$sql);

							//bauauftrag hinterlegen
							//bauzeit berechnen
							if($rec_target<100){
								$tech_ticks=$techdata_ticks[$rec_target];
								if($spec1==2)$tech_ticks=round($tech_ticks/2);
							}else{
								$tech_ticks=$techdata_ticks[$rec_target];
								$tech_ticks=ceil($tech_ticks-($tech_ticks*$defense_bonus_buildtime/100));
							}

							//gibt schiffe in auftrag
							if($tech_ticks<1){
								$tech_ticks=1;
							}
							mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score, recycling) VALUES 
							(".$_SESSION['ums_user_id'].", $rec_target, $target_amount, $tech_ticks, $score, 1)");
						}
					}
				}
			}
		}

		//test auf zu recycelnde verteidigungseinheiten
		for($i=100;$i<=104;$i++){
			$rec_amount=intval($_REQUEST["e".$i]);
			if($rec_amount>0)			{
				//test ob man soviel �berhaupt hat
				if($rec_amount>$einheiten[$i]){
					$rec_amount=$einheiten[$i];
				}

				//überprüfen in was er recyceln m�chte
				$rec_target=intval($_REQUEST["t".$i]);
				if((($rec_target>=81 AND $rec_target<=90) OR ($rec_target>=100 AND $rec_target<=104)) AND $rec_target!=$i){
					//überprüfen, ob man diese technologie schon nutzen kann

					if($tech_erlaubt[$rec_target]==1){
						//zielmenge berechnen
						//echo '<br>A: '.$rec_amount.'*'.$techscore[$i].'*(1-'.$defabzug.')/'.$techscore[$rec_target];
						//235 
						$target_amount=floor($rec_amount*$techscore[$i]*(1-$defabzug)/$techscore[$rec_target]);
						$score=$target_amount*$techscore[$rec_target];
						if($target_amount>0){
							//die türme abziehen
							$sql="UPDATE de_user_data SET e".$i."=e".$i."-".$rec_amount." WHERE user_id='".$_SESSION['ums_user_id']."'";
							mysqli_query($GLOBALS['dbi'],$sql);

							//bauauftrag hinterlegen
							//bauzeit berechnen
							if($rec_target<100){
								$tech_ticks=$techdata_ticks[$rec_target];
								if($spec1==2)$tech_ticks=round($tech_ticks/2);
							}else{
								$tech_ticks=$techdata_ticks[$rec_target];
								$tech_ticks=ceil($tech_ticks-($tech_ticks*$defense_bonus_buildtime/100));
							}								

							//gibt schiffe in auftrag
							if($tech_ticks<1){
								$tech_ticks=1;
							}
							$sql="INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score, recycling) VALUES (".$_SESSION['ums_user_id'].", $rec_target, $target_amount, $tech_ticks, $score, 1)";
							//echo $sql;
							mysqli_query($GLOBALS['dbi'],$sql);
						}
					}
				}
			}
		}			

		//transaktionsende
		$erg = releaseLock($_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
		if ($erg)
		{
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}
		else
		{
			echo 'Fehler bei der Transaktion.';
		}
	}// if setlock-ende
	else echo 'Fehler bei der Transaktion.';
}


?>
<!DOCTYPE HTML>
<html>
<head>
<title>Recycling</title>
<?php include "cssinclude.php";?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include 'resline.php';

echo einheiten_navi('recycling');

//feststellen ob eine sabotage vorliegt und dann abbrechen
if($sabotage==1){
	echo '<div class="prod mod prod-sabotage"><div class="mod-meldung mod-meldung-fehler">Durch eine Sabotageaktion ist kein Recycling m&ouml;glich. Mehr Informationen sind im Geheimdienst abrufbar.</div></div>';
	die('</body></html>');
}

//benötigtes Gebäude: Recyclotron
if(!hasTech($pt,129)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=129";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);

	echo '<br>';
	rahmen_oben('Fehlende Technologie');
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=13" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_13.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{
	///////////////////////////////////////////////////////////////////////
	//  Oberfläche darstellen: kompakte Tabelle, eine Zeile je Einheit
	///////////////////////////////////////////////////////////////////////

	//Bestände: Heimatflotte und Verteidigungsanlagen
	$bestand=array();
	$fleetid=$_SESSION['ums_user_id'].'-0';
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fleetid'");
	$row = mysqli_fetch_array($db_daten);
	for($i=81;$i<=90;$i++){
		$bestand[$i]=(int)$row['e'.$i];
	}
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='".$_SESSION['ums_user_id']."'");
	$row = mysqli_fetch_array($db_daten);
	for($i=100;$i<=104;$i++){
		$bestand[$i]=(int)$row['e'.$i];
	}

	//eine Zeile: Name, Bestand, Menge, Zieleinheit, voraussichtliches Ergebnis
	$zeilen=array('flotte' => '', 'verteidigung' => '');
	foreach($bestand as $i => $vorhanden){
		$zeilen[$i<100 ? 'flotte' : 'verteidigung'].='
		<div class="rec-zeile'.($vorhanden>0 ? '' : ' rec-leer').'">
			<span class="prod-name"><span>'.$technames[$i].'</span></span>
			<span class="prod-zahl">'.number_format($vorhanden,0,",",".").'</span>
			<span><input name="e'.$i.'" id="e'.$i.'" type="text" maxlength="15" autocomplete="off" inputmode="numeric" class="mod-eingabe rec-menge" data-quelle="'.$i.'"></span>
			<span><select name="t'.$i.'" id="t'.$i.'" class="mod-eingabe" data-quelle="'.$i.'">'.$techselect.'</select></span>
			<span class="prod-zahl rec-ergebnis" id="r'.$i.'">&ndash;</span>
		</div>';
	}

	echo '<form action="recycling.php" method="POST">';
	rahmen_oben('Recycling');
	echo '
	<div class="rec mod">
		<div class="mod-hinweis">Die Einheiten werden sofort in ihre Bestandteile zerlegt und der Bau der neuen Einheiten beginnt. Dabei tritt Schwund auf: '.($fleetabzug*100).' % bei Einheiten der Heimatflotte, '.($defabzug*100).' % bei Verteidigungseinheiten. &Uuml;bersch&uuml;ssige Teile gehen verloren, also m&ouml;glichst gro&szlig;e Mengen recyceln. Titanen-Energiekerne werden nicht zur&uuml;ckerstattet.</div>
		<div class="rec-zeile prod-kopf">
			<span class="prod-name">Einheit</span>
			<span class="prod-zahl">Vorhanden</span>
			<span>Menge</span>
			<span>Zieleinheit</span>
			<span class="prod-zahl" title="Ergebnis&Voraussichtliche Anzahl der Zieleinheiten">Ergebnis</span>
		</div>
		<div class="prod-gruppe"><span class="mod-typ">Einheiten in der Heimatflotte</span><span class="mod-chip">Schwund <b>'.($fleetabzug*100).' %</b></span></div>
		<div class="prod-liste">'.$zeilen['flotte'].'</div>
		<div class="prod-gruppe"><span class="mod-typ">Verteidigungseinheiten</span><span class="mod-chip">Schwund <b>'.($defabzug*100).' %</b></span></div>
		<div class="prod-liste">'.$zeilen['verteidigung'].'</div>
		<div class="rec-los"><input type="Submit" name="recyclingbutton" value="Recycling starten" class="mod-btn"></div>';

	//zeige aktive bauaufträge an
	$result=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, SUM(anzahl) AS anzahl, verbzeit, SUM(score) AS score FROM `de_user_build`
		WHERE user_id='".$_SESSION['ums_user_id']."' AND tech_id>80 AND tech_id<110 GROUP BY tech_id, verbzeit ORDER BY verbzeit, tech_id ASC");
	if(mysqli_num_rows($result)>0){
		echo '<div class="geh-produktion"><div class="mod-typ">Aktive Bauauftr&auml;ge</div>';
		while($row = mysqli_fetch_array($result)){
			echo '<div class="geh-auftrag"><span>'.$technames[$row["tech_id"]].'</span><b>'.number_format($row["anzahl"], 0,"",".").'</b><span class="prod-punkte">'.number_format($row["score"], 0,"",".").' Punkte</span><span class="mod-chip">noch '.$row["verbzeit"].' WT</span></div>';
		}
		echo '</div>';
	}

	echo '</div>';
	rahmen_unten();
	echo '</form>';

	//voraussichtliches Ergebnis wie beim Recycling oben: floor(Menge * Punkte * (1 - Schwund) / Punkte des Ziels), Menge höchstens der Bestand
	echo '<script>
	(function(){
		var punkte = '.json_encode($techscore).', bestand = '.json_encode($bestand).';
		function rechnen(i){
			var menge = Math.min(parseInt(document.getElementById("e" + i).value, 10) || 0, bestand[i]);
			var ziel = parseInt(document.getElementById("t" + i).value, 10) || 0;
			var feld = document.getElementById("r" + i);
			if(menge <= 0 || ziel == 0 || ziel == i){
				feld.textContent = "–";
				feld.classList.remove("rec-null");
				return;
			}
			var anzahl = Math.floor(menge * punkte[i] * (1 - (i < 100 ? '.$fleetabzug.' : '.$defabzug.')) / punkte[ziel]);
			feld.textContent = anzahl.toLocaleString("de-DE");
			feld.classList.toggle("rec-null", anzahl <= 0);
		}
		[].forEach.call(document.querySelectorAll(".rec [data-quelle]"), function(f){
			var i = parseInt(f.getAttribute("data-quelle"), 10);
			f.addEventListener("input", function(){ rechnen(i); });
			f.addEventListener("change", function(){ rechnen(i); });
		});
	})();
	</script>';
}






?>

</body>
</html>