<?php
include "inc/header.inc.php";
include "lib/transactioni.lib.php";
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
	if (setLock($ums_user_id)){
		unset($rec_gesamt);
		//zerst alle vorhandenen einheiten auslesen
		unset($einheiten);
		$fleetid=$ums_user_id.'-0';
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fleetid'");
		$row = mysqli_fetch_array($db_daten);
		for($i=81;$i<=90;$i++){
			$einheiten[$i]=$row['e'.$i];
		}
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='$ums_user_id'");
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
							($ums_user_id, $rec_target, $target_amount, $tech_ticks, $score, 1)");
						}
					}
				}
				//else $recyclingmessage='<div class="info_box text2">Dieser Zieltyp ist ung&uuml;ltig.</div>';
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
							$sql="UPDATE de_user_data SET e".$i."=e".$i."-".$rec_amount." WHERE user_id='$ums_user_id'";
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
							$sql="INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score, recycling) VALUES ($ums_user_id, $rec_target, $target_amount, $tech_ticks, $score, 1)";
							//echo $sql;
							mysqli_query($GLOBALS['dbi'],$sql);
						}
					}
				}
				//else $recyclingmessage='<div class="info_box text2">Dieser Zieltyp ist ung&uuml;ltig.</div>';

			}

			/*
			$recyclingmessage='<div class="info_box text3" style="text-align: left;">Erhaltene Rohstoffe:
			<br>'.number_format($rec_gesamt[0] ,0,",",".").' M
			<br>'.number_format($rec_gesamt[1] ,0,",",".").' D
			<br>'.number_format($rec_gesamt[2] ,0,",",".").' I
			<br>'.number_format($rec_gesamt[3] ,0,",",".").' E
			<br>'.number_format($rec_gesamt[4] ,0,",",".").' T
			</div>';
			*/
		}			

		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
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
<body>
<?php
include 'resline.php';

echo '
<a href="production.php" title="Einheitenproduktion"><img src="'.$ums_gpfad.'g/symbol19.png" border="0" width="64px" heigth="64px"></a> 
<a href="recycling.php" title="Recycling&Hier k&ouml;nnen Einheiten der Heimatflotte und Verteidigungseinheiten recycelt werden."><img src="'.$ums_gpfad.'g/symbol24.png" border="0" width="64px" heigth="64px"></a>';
if($sv_deactivate_vsystems!=1){
	echo '<a href="specialship.php" title="Basisstern"><img src="'.$ums_gpfad.'g/symbol27.png" border="0" width="64px" heigth="64px"></a>';
}
echo'
<a href="unitinfo.php" title="Einheiteninformationen"><img src="'.$ums_gpfad.'g/symbol26.png" border="0" width="64px" heigth="64px"></a>
';

//feststellen ob eine sabotage vorliegt und dann abbrechen
if($sabotage==1)
{
  $emsg.='<table width=600><tr><td class="ccr">';
  $emsg.='Durch eine Sabotageaktion ist kein Recycling m&ouml;glich. Mehr Informationen sind im Geheimdienst abrufbar.';
  $emsg.='</td></tr></table>';
  echo $emsg;
  
  die('</body></html>');
}

if($recyclingmessage!=''){
	echo $recyclingmessage;
	echo '<br>';
}

//ben�tigtes geb�ude recyclotron
if(!hasTech($pt,129)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=129";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);

	echo '<br>';
	rahmen_oben('Fehlende Technologie');
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=13" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_13.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{
	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////
	//  Oberfläche darstellen
	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////
	
  	echo '<form action="recycling.php" method="POST">';
	
	//optische ausgabe
	rahmen_oben('Recycling  <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" 
	title="Informationen&Die Einheiten werden sofort in ihre Bestandteile zerlegt und der Bau der neuen Einheiten beginnt.<br><br>
	Dabei tritt ein gewisser Schwund auf:<br>Abzug bei Einheiten der 
	Heimatflotte: '.($fleetabzug*100).'%<br>Abzug bei Verteidigungseinheiten: '.($defabzug*100).'%<br><br>&Uuml;bersch&uuml;ssige Teile gehen verloren, also sollten m&ouml;glichst gro&szlig;e Mengen recycelt werden, da sonst der prozentuale Verlust zu gro&szlig; werden kann.<br><br>Titanen-Energiekerne werden nicht zur&uuml;ckerstattet.">');
	echo '<table border="0" cellpadding="0" cellspacing="1" width="580px">';
	//Heimatflotteneinheiten
	echo '<tr class="cell1"><td colspan="4"><b>Einheiten in der Heimatflotte:</b></td></tr>';
	echo '<tr class="cell"><td><b>Name</b></td><td align="center"><b>vorhanden</b></td><td align="center"><b>Recyclingmenge</b></td><td align="center"><b>Zieleinheit</td></tr>';

	//einheiten aus der db lesen
	$fleetid=$ums_user_id.'-0';
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fleetid'");
	$row = mysqli_fetch_array($db_daten);
	for($i=81;$i<=90;$i++){
		echo '<tr class="cell">
		<td>'.$technames[$i].'</td>
		<td align="right">'.number_format($row['e'.$i],0,",",".").'</td>
		<td align="center"><input name="e'.$i.'" id="e'.$i.'" type="text" size="15" maxlength="15"></td>
		<td align="center"><select name="t'.$i.'">'.$techselect.'</select></td>
		</tr>';
	}
	
	
	//Verteidigungseinheiten
	//echo '<div class="cell" style="width: 580px;">';
	
	echo '<tr class="cell1"><td colspan="4"><b>Verteidigungseinheiten:</b></td></tr>';
	echo '<tr class="cell"><td><b>Name</b></td><td align="center"><b>vorhanden</b></td><td align="center"><b>Recyclingmenge</b></td><td align="center"><b>Zieleinheit</td></tr>';
	
	//einheiten aus der db lesen
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='$ums_user_id'");
	$row = mysqli_fetch_array($db_daten);
	for($i=100;$i<=104;$i++){
		echo '<tr class="cell">
		<td>'.$technames[$i].'</td>
		<td align="right">'.number_format($row['e'.$i],0,",",".").'</td>
		<td align="center"><input name="e'.$i.'" id="e'.$i.'" type="text" size="15" maxlength="15"></td>
		<td align="center"><select name="t'.$i.'">'.$techselect.'</select></td>
		</tr>';
	}
	
	echo '<tr><td class="cell" colspan="4" align="center"><input type="Submit" name="recyclingbutton" value="Recycling starten"></td></tr>';

	/*
	echo '</table>';
	rahmen_unten();
	*/
	
//zeige aktive bauauftr�ge an
$result=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, SUM(anzahl) AS anzahl, verbzeit, SUM(score) AS score FROM `de_user_build` 
	WHERE user_id='$ums_user_id' AND tech_id>80 AND tech_id<110 GROUP BY tech_id, verbzeit ORDER BY verbzeit, tech_id ASC");
$num = mysqli_num_rows($result);
if ($num>0){
	echo '<tr class="cell1"><td colspan="4"><b>Aktive Bauauftr&auml;ge:</b></td></tr>';
	
	echo '<tr><td colspan="4">';
	echo '<table border="0" cellpadding="0" cellspacing="1">';
	echo '<tr align="center">';
	echo '<td class="cell" width="300"><b>Einheit</b></td>';
	echo '<td class="cell" width="80"><b>St&uuml;ck</b></td>';
	echo '<td class="cell" width="115"><b>Punkte</b></td>';
	echo '<td class="cell" width="78"><b>WT</b></td>';
	echo '</tr>';

	while($row = mysqli_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	{
	    echo '<tr align="center">';
	    echo '<td class="cell">'.$technames[$row["tech_id"]].'</td>';
	    echo '<td class="cell">'.number_format($row["anzahl"], 0,"",".").'</td>';
	    echo '<td class="cell">'.number_format($row["score"], 0,"",".").'</td>';
	    echo '<td class="cell">'.$row["verbzeit"].'</td>';
	    echo '</tr>';
	}
	echo '</table></td></tr>';
}

	echo '</table>';
	
	rahmen_unten();	
	
	echo '</form>';
}






?>
<?php include "fooban.php"; ?>
</body>
</html>