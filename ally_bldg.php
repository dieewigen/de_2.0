<?php
include('inc/header.inc.php');
include_once('functions.php');
include('lib/transaction.lib.php');
include('ally/allyfunctions.inc.php');

//include 'inc/lang/'.$sv_server_lang.'_community.lang.php';
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, 
            newtrans, newnews, allytag, status 
     FROM de_user_data WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row['score'];$newtrans=$row['newtrans'];$newnews=$row['newnews'];
$sector=$row['sector'];$system=$row['system'];
if ($row['status']==1) $ownally = $row['allytag'];

?>
<!DOCTYPE HTML>
<head>
<title>Allianzprojekte</title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
//stelle die ressourcenleiste dar
include('resline.php');
include('ally/ally.menu.inc.php');

//allydaten laden
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE allytag = ?",
    [$ownally]
);
$row = mysqli_fetch_assoc($db_daten);    
$allyid = $row['id'];
$ownallyid = $allyid;

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE id = ?",
    [$allyid]
);
$num = mysqli_num_rows($db_daten);
if($num==1){
	
    $row = mysqli_fetch_assoc($db_daten);
    $leaderid=$row['leaderid'];

	//Allianzgebäude definieren
	unset($def_allybldg);

	$def_allybldg[0]['name']='Allianzsektorraumbasis';
	$def_allybldg[0]['desc']='Die Allianzraumbasis dient als Grundstock aller weiteren Projekte.';
	$def_allybldg[0]['artpreis']=5;
	$def_allybldg[0]['tronicpreis']=20;
	$def_allybldg[0]['grafikfile']='sbtagsfbw.gif';
	$def_allybldg[0]['haslevel']=$row['bldg0'];
	$def_allybldg[0]['maxlevel']=1;

	$def_allybldg[1]['name']='Diplomatiezentrum';
	$def_allybldg[1]['desc']='Das Diplomatiezentrum wird f&uuml;r ein Allianzb&uuml;ndnis ben&ouml;tigt.<br>Pro Projektstufe erh&auml;lt man 1% der Projektboni des B&uuml;ndnispartners. (Ausgenommen davon: Notfallrohstoffkonverter, Fundb&uuml;ro)';
	$def_allybldg[1]['artpreis']=2;
	$def_allybldg[1]['tronicpreis']=1;
	$def_allybldg[1]['grafikfile']='symbol11.png';
	$def_allybldg[1]['haslevel']=$row['bldg1'];
	$def_allybldg[1]['maxlevel']=50;

	$def_allybldg[2]['name']='Informationsphalanx';
	$def_allybldg[2]['desc']='Die Informationsphalanx dient zur Weiterleitung von Geheimdienstinformationen.<br>Stufe 1: Nur Mitglieder der eigenen Allianz, jedoch nicht in einer Meta.<br>Stufe 2: Die Informationen werden auch an die Mitglieder der Allianzmeta weitergeleitet. Beide Allianzen ben&ouml;tigen Stufe 2.';
	$def_allybldg[2]['artpreis']=10;
	$def_allybldg[2]['tronicpreis']=100;
	$def_allybldg[2]['grafikfile']='t/1_75.jpg';
	$def_allybldg[2]['haslevel']=$row['bldg2'];
	$def_allybldg[2]['maxlevel']=2;
	for($i=0;$i<$def_allybldg[2]['maxlevel'];$i++){
		$def_allybldg[2]['bldg_cost'][$i]='I13x'.($i*10);
	}	
	
	$def_allybldg[3]['name']='Leitzentrale Feuroka';
	$def_allybldg[3]['desc']='Die Leitzentrale Feuroka verst&auml;rkt die Feuerkraft von Raumschiffen um 1% pro Stufe.';
	$def_allybldg[3]['artpreis']=10;
	$def_allybldg[3]['tronicpreis']=10;
	$def_allybldg[3]['grafikfile']='arte6.gif';
	$def_allybldg[3]['haslevel']=$row['bldg3'];
	$def_allybldg[3]['maxlevel']=10;	
    
	$def_allybldg[4]['name']='Leitzentrale Bloroka';
	$def_allybldg[4]['desc']='Die Leitzentrale Bloroka verst&auml;rkt die L&auml;hmkraft von Raumschiffen um 1% pro Stufe.';
	$def_allybldg[4]['artpreis']=5;
	$def_allybldg[4]['tronicpreis']=5;
	$def_allybldg[4]['grafikfile']='arte7.gif';
	$def_allybldg[4]['haslevel']=$row['bldg4'];
	$def_allybldg[4]['maxlevel']=10;

	$def_allybldg[5]['name']='Artefaktgeb&auml;udeerweiterung';
	$def_allybldg[5]['desc']='Die Artefaktgeb&auml;udeerweiterung erh&ouml;ht die Anzahl der vorhandenen Pl&auml;tze f&uuml;r Artefakte um 1 pro Stufe.';
	$def_allybldg[5]['artpreis']=10;
	$def_allybldg[5]['tronicpreis']=25;
	$def_allybldg[5]['grafikfile']='arte17.gif';
	$def_allybldg[5]['haslevel']=$row['bldg5'];
	$def_allybldg[5]['maxlevel']=10;
	
	$def_allybldg[6]['name']='Notfallrohstoffkonverter';
	$def_allybldg[6]['desc']='Der Notfallrohstoffkonverter kann Multiplex, Dyharra, Iradium und Eternium zueinander umwandeln. Je weiter er ausgebaut wird, desto geringer ist dabei der Verlust. ';
	$def_allybldg[6]['artpreis']=1;
	$def_allybldg[6]['tronicpreis']=100;
	$def_allybldg[6]['grafikfile']='t/1_6.jpg';
	$def_allybldg[6]['haslevel']=$row['bldg6'];
	$def_allybldg[6]['maxlevel']=40;

	$def_allybldg[7]['name']='Kommunikationsphalax';
	$def_allybldg[7]['desc']='Die Kommunikationsphalax verl&auml;ngert die &Uuml;bermittlungszeit des Flottenstatus &uuml;ber die AI-Funktion um 1% pro Stufe.';
	$def_allybldg[7]['artpreis']=1;
	$def_allybldg[7]['tronicpreis']=5;
	$def_allybldg[7]['grafikfile']='t/1_12.jpg';
	$def_allybldg[7]['haslevel']=$row['bldg7'];
	$def_allybldg[7]['maxlevel']=100;
	for($i=0;$i<$def_allybldg[7]['maxlevel'];$i++){
		$def_allybldg[7]['bldg_cost'][$i]='I13x'.($i+1);
	}

	$def_allybldg[8]['name']='Fundb&uuml;ro';
	$def_allybldg[8]['desc']='Das Fundb&uuml;ro teilt die Information &uuml;ber geborgene Vergessene-Systeme-Fundst&uuml;cke innerhalb der Allianz bis zur angegebenen Stufe.';
	$def_allybldg[8]['artpreis']=1;
	$def_allybldg[8]['tronicpreis']=5;
	$def_allybldg[8]['grafikfile']='t/1_8.jpg';
	$def_allybldg[8]['haslevel']=$row['bldg8'];
	$def_allybldg[8]['maxlevel']=10;
	for($i=0;$i<$def_allybldg[8]['maxlevel'];$i++){
		$def_allybldg[8]['bldg_cost'][$i]='I13x'.($i*2+1);
	}

	$def_allybldg[9]['name']='Kollektorensynthetisierer';
	$def_allybldg[9]['desc']='Bei jedem Ausbau erhalten alle Mitglieder, die zu diesem Zeitpunkt in der Allianz sind, einen Kollektor.';
	$def_allybldg[9]['artpreis']=1;
	$def_allybldg[9]['tronicpreis']=1;
	$def_allybldg[9]['grafikfile']='t/1_7.jpg';
	$def_allybldg[9]['haslevel']=$row['bldg9'];
	$def_allybldg[9]['maxlevel']=50;
	for($i=0;$i<$def_allybldg[9]['maxlevel'];$i++){
		$def_allybldg[9]['bldg_cost'][$i]='I13x'.($i*2+1);
	}	

    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    // gebäude bauen
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////    
    if(isset($_REQUEST['build'])){
  	  	if (setLock($_SESSION['ums_user_id'])){
			//Allystorage laden
			$as=loadAllyStorage($ownallyid);
			$GLOBALS['as']=$as;
		
			//test ob es der leader ist
			if($_SESSION['ums_user_id']==$leaderid){
				$build=intval($_REQUEST['build']);

				//hat man bei den späteren Gebäuden die Allianzsektorraumbasis als Vorbedingung erfüllt?
				if($build==0 || $def_allybldg[0]['haslevel']>0){

					//nochmal die gebäudestufe auslesen, damit alles konsistent ist
					$db_daten = mysqli_execute_query($GLOBALS['dbi'],
						"SELECT * FROM de_allys WHERE id = ?",
						[$allyid]
					);
					$row = mysqli_fetch_assoc($db_daten);
					$haslevel=$row['bldg'.$build];
					$hasartefacts=$row['artefacts'];
					$allytag=$row['allytag'];
					$hasallytronic=$row['t_depot'];
					//überprüfen ob es noch weiter ausbaubar ist
					if($haslevel<$def_allybldg[$build]['maxlevel']){
						//test ob genug allianzartefakte zum bezahlten vorhanden sind
						$artpreis=$def_allybldg[$build]['artpreis']*($def_allybldg[$build]['haslevel']+1);
						if($hasartefacts>=$artpreis){
							$tronicpreis=$def_allybldg[$build]['tronicpreis']*($def_allybldg[$build]['haslevel']+1);
							//test ob genug tronic zum bezahlen vorhanden ist
							if($hasallytronic>=$tronicpreis){
								//Itemdata-Kosten
								$has_all=true;
								$need_storage_res=array();
								
								//test auf ausreichende Rohstoffe
								$cost_string = isset($def_allybldg[$build]['bldg_cost'][$def_allybldg[$build]['haslevel']]) 
									? $def_allybldg[$build]['bldg_cost'][$def_allybldg[$build]['haslevel']] 
									: '';
								$einzelkosten = $cost_string ? explode(';', $cost_string) : array();
								//print_r($einzelkosten);
								foreach ($einzelkosten as $value) {
									$parts=explode("x", $value);
					
									if($value[0]=='I'){
										//genug im storage vorhanden?
										$value1=str_replace('I','',$parts[0]);
										if($as[$value1]['item_amount']<$parts[1]){$has_all=false;}
										//speichern wie viel man aus dem storage benötigt
										$need_storage_res[$value1]=$parts[1];
									}
								}
								
								if($has_all){
									//Item-Kosten abziehen
									foreach ($need_storage_res as $key => $value){
										changeAllyStorageAmount($ownallyid, $key, $value*-1);
									}

									//artefakte abziehen und das gebäude in der db hinterlegen, dazu questpoints gutschreiben
									mysqli_execute_query($GLOBALS['dbi'],
										"UPDATE de_allys SET t_depot = t_depot - ?, artefacts = artefacts - ?, 
										 bldg".$build." = bldg".$build." + 1, questpoints = questpoints + 10 
										 WHERE id = ?",
										[$tronicpreis, $artpreis, $allyid]);  
									$def_allybldg[$build]['haslevel']++;
									$row['artefacts']-=$artpreis;
									$row['t_depot']-=$tronicpreis;
							
									$entry='Allianzprojekt abgeschlossen: '.$def_allybldg[$build]['name'].' ('.$def_allybldg[$build]['haslevel'].'/'.$def_allybldg[$build]['maxlevel'].')';
									//meldung im chat hinterlegen
									//insert_chat_msg($entry, 1, $_SESSION['ums_user_id']);
									insert_chat_msg($allyid, 1, '', $allytag.'-'.$entry);
							
									//meldung in der allianzhistorie hinterlegen
									writeHistory($allytag, $entry);
							
									//meldung für den bauer
									echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok">'.$entry.'</div></div>';

									///////////////////////////////////////////////////////////////
									// das Gebäude kann eine Direktwirkung haben
									///////////////////////////////////////////////////////////////
									switch($build){
										case 9:
											//jedes Allianzmitgiled erhält einen Kollektor
											mysqli_execute_query($GLOBALS['dbi'],
												"UPDATE de_user_data SET col = col + 1 
												 WHERE status = 1 AND allytag = ?",
												[$allytag]);  

										break;

										default:
										break;
									}
									


								}else{
									//fehlermeldung zu wenig Rohstoffe
									echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Die Allianz hat nicht genug Rohstoffe.</div></div>';
								}
							}else{
							//fehlermeldung zu wenig artefakte
							echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Die Allianz hat nicht genug Tronic.</div></div>';
							}
						}else{
						//fehlermeldung zu wenig artefakte
						echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Die Allianz hat nicht genug Allianzartefakte.</div></div>';
						}
					}
				}
			}else{
				echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Nur der Allianzleiter kann Allianzprojekte in Auftrag geben.</div></div>';
			}    
			//transaktionsende
			$erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
			if ($erg){
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
			}
			else{
			print('Datensatz Nr. '.$_SESSION['ums_user_id'].' Konnte nicht entsperrt werden.<br><br>');
			}
		}// if setlock-ende
		else echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warte, bis die Transaktion abgeschlossen ist.</div></div>';
    }
    
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    // allianzprojekte darstellen
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
	//Allystorage laden
	$as=loadAllyStorage($ownallyid);
	$GLOBALS['as']=$as;

	//print_r($GLOBALS['as']);

	rahmen_oben('Allianzprojekte');
	echo '<div class="ally mod">';

	//Bestand der Allianz
	echo '
	<div class="ally-kacheln ally-kacheln-3">
		<div class="ov-wert"><span class="mod-typ">Allianzartefakte</span><b>'.number_format($row['artefacts'],0,",",".").'</b></div>
		<div class="ov-wert"><span class="mod-typ">Allianztronic</span><b>'.number_format($row['t_depot'],0,",",".").'</b></div>
		<div class="ov-wert"><span class="mod-typ">'.$as[13]['item_name'].'</span><b>'.number_format($as[13]['item_amount'],0,",",".").'</b></div>
	</div>';

	$ist_leader = ($_SESSION['ums_user_id']==$leaderid);
	if(!$ist_leader){
		echo '<div class="mod-hinweis ally-abstand">Nur der Allianzleiter kann Allianzprojekte in Auftrag geben.</div>';
	}

	echo '<div class="ally-projekte">';
	for($i=0;$i<count($def_allybldg);$i++){
		//Test ob man als Voraussetzung bereits die Allianzsektorraumbasis hat
		if($i==0 || ($def_allybldg[0]['haslevel']>0 && $i>0)){

			$haslevel=$def_allybldg[$i]['haslevel'];
			$maxlevel=$def_allybldg[$i]['maxlevel'];

			//////////////////////////////////////////
			//Kosten der nächsten Stufe, fehlende rot
			//////////////////////////////////////////
			$kosten='';
			$kann_bauen=true;
			if($haslevel<$maxlevel){
				$artpreis=$def_allybldg[$i]['artpreis']*($haslevel+1);
				$tronicpreis=$def_allybldg[$i]['tronicpreis']*($haslevel+1);
				$fehlt=($row['artefacts']<$artpreis);
				$kann_bauen=$kann_bauen && !$fehlt;
				$kosten.='<span class="mod-chip'.($fehlt ? ' ally-chip-fehlt' : '').'"><b>'.number_format($artpreis,0,",",".").'</b> Allianzartefakte</span>';
				$fehlt=($row['t_depot']<$tronicpreis);
				$kann_bauen=$kann_bauen && !$fehlt;
				$kosten.='<span class="mod-chip'.($fehlt ? ' ally-chip-fehlt' : '').'"><b>'.number_format($tronicpreis,0,",",".").'</b> Tronic</span>';
			}

			$has_all=true;

			//Item-Baukosten
			$cost_string = isset($def_allybldg[$i]['bldg_cost'][$def_allybldg[$i]['haslevel']])
				? $def_allybldg[$i]['bldg_cost'][$def_allybldg[$i]['haslevel']]
				: '';
			$einzelkosten = $cost_string ? explode(';', $cost_string) : array();
			foreach ($einzelkosten as $value) {
				$parts=explode("x", $value);

				if($value[0]=='I'){
					$value1=str_replace('I','',$parts[0]);
					$fehlt=false;
					if($as[$value1]['item_amount']<$parts[1]){
						$has_all=false;
						$fehlt=true;
					}
					$kosten.='<span class="mod-chip'.($fehlt ? ' ally-chip-fehlt' : '').'"><b>'.number_format($parts[1],0,",",".").'</b> '.$as[$value1]['item_name'].'</span>';
				}
			}
			$kann_bauen=$kann_bauen && $has_all;

			$allyidpartner=get_allyid_partner($ownallyid);
			if($allyidpartner>0){
				$allybldgpartner=get_allybldg($allyidpartner);
			}

			$partner='';
			if(isset($allybldgpartner)){
				$partner='<span class="mod-chip mod-chip-gruen">Partner <b>'.$allybldgpartner[$i].'/'.$maxlevel.'</b></span>';
			}

			//////////////////////////////////////////
			// Projekt ausgeben
			//////////////////////////////////////////
			if($sv_deactivate_vsystems==1 && $i==8){

			}
			else{
				if($haslevel>=$maxlevel){
					$aktion='<span class="mod-feld mod-feld-ok">Fertig</span>';
				}elseif(!$ist_leader){
					$aktion='';
				}elseif($kann_bauen){
					$aktion='<a href="ally_bldg.php?build='.$i.'" class="mod-btn">Ausbauen</a>';
				}else{
					$aktion='<span class="mod-feld mod-feld-grund">Zu wenig Mittel</span>';
				}

				echo '
				<div class="ally-projekt'.($haslevel>=$maxlevel ? ' ally-projekt-fertig' : '').'" id="bc'.$i.'">
					<img src="gp/g/'.$def_allybldg[$i]['grafikfile'].'" alt="">
					<div class="ally-projekt-text">
						<div class="ally-projekt-kopf">
							<span class="ally-projekt-name">'.$def_allybldg[$i]['name'].'</span>
							<span class="ally-projekt-stufe">Stufe <b>'.$haslevel.'</b> / '.$maxlevel.'</span>
						</div>
						<div class="mod-balken"><span style="width: '.round(min(100, $haslevel/$maxlevel*100), 1).'%;"></span></div>
						<div class="ally-projekt-desc">'.$def_allybldg[$i]['desc'].'</div>
						<div class="ally-chips">'.($kosten!='' ? '<span class="ally-leise-text">N&auml;chste Stufe:</span>'.$kosten : '').$partner.'</div>
					</div>
					<div class="ally-projekt-aktion">'.$aktion.'</div>
				</div>';
			}
		}
	}
	echo '</div>';
	if($def_allybldg[0]['haslevel']==0){
		echo '<div class="mod-hinweis ally-abstand">Weitere Projekte werden nach dem Bau der Allianzsektorraumbasis freigeschaltet.</div>';
	}
	echo '</div>';
	rahmen_unten();
}

/*
gebäude:allianzsektorraumbasis
scanner
diplomatie
feuerkraft
blockkraft
eh-punkte

kosten:
tronic
rohstoffe
credits
alle spielerartefakte
besondere spielerartefakte

 */





?>
</div>
<br>

</body>
</html>
