<?php

use DieEwigen\DE2\View\RealTime;

include 'inc/header.inc.php';
include 'lib/transaction.lib.php';
include 'functions.php';

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Technologien</title>
<?php
include "cssinclude.php";
?>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<script type="text/javascript" src="js/ang_techs.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_techs.js');?>"></script>
</head>
<?php
echo '<body class="tc-body theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
echo '<div class="tc-fenster">';

$content='';

if(!isset($sv_deactivate_vsystems)){
	$sv_deactivate_vsystems=0;
}

//transaktionsbeginn
if(setLock($_SESSION['ums_user_id'])){
	$pd=loadPlayerData($_SESSION['ums_user_id']);
	$pt=loadPlayerTechs($_SESSION['ums_user_id']);

	$ps=loadPlayerStorage($_SESSION['ums_user_id']);

	$row=$pd;
	$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
	$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
	$dailyallygift=$row['dailyallygift'];$allytag=$row['allytag'];$allystatus=$row['status'];

	$newtrans=$row["newtrans"];$newnews=$row["newnews"];

	//Anzeige aus den Cookies (js/ang_techs.js setzt sie ohne Neuladen):
	//Ansicht 0 = Baum (Spalten je Stufe), 1 = Liste; ohne Cookie mobil die Liste
	$tech_anordnung=isset($_COOKIE['tech_anordnung']) ? intval($_COOKIE['tech_anordnung']) : ($_SESSION['ums_mobi']==1 ? 1 : 0);
	$tech_sound=intval($_COOKIE['tech_sound'] ?? 0);
	$tech_filter_typ=isset($_COOKIE['tech_filter_typ']) ? intval($_COOKIE['tech_filter_typ']) : -1;
	//Statusfilter; ohne Cookie wie die frühere Einstellung "erledigte Technologien nicht ausblenden"
	$tech_status=$_COOKIE['tech_status'] ?? (intval($_COOKIE['tech_erledigte_techs'] ?? 0)==1 ? 'alle' : 'offen');
	if(!in_array($tech_status, array('baubar', 'offen', 'erledigt', 'alle'))){
		$tech_status='offen';
	}

	///////////////////////////////////////////////////////////////////
	//alle Technologien aus der DB auslesen und in ein Array packen
	///////////////////////////////////////////////////////////////////
	$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_tech_data WHERE tech_sort_id < 1000 ORDER BY tech_level ASC, tech_sort_id ASC");
	$tech_daten=array();
	while($row = mysqli_fetch_array($db_daten)){
		$tech_daten[$row['tech_id']]=$row;
	}

	///////////////////////////////////////////////////////////////////
	//auslesen, welche Technologietypen aktuell in Bearbeitung sind sind
	///////////////////////////////////////////////////////////////////
	$active_tech_types=array();
	$active_tech_types_row=array();
	$anzahl_tech_types=4;
	for($i=0;$i<$anzahl_tech_types;$i++){
		$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_techs LEFT JOIN de_tech_data ON (de_user_techs.tech_id=de_tech_data.tech_id)
			WHERE user_id='".$_SESSION['ums_user_id']."' AND tech_typ='$i' AND time_finished>='".time()."';");
		$num=mysqli_num_rows($db_daten);
		$active_tech_types[$i] = $num;
		if($num>0){
			$row = mysqli_fetch_array($db_daten);
			$active_tech_types_row[$i]=$row;
		}
	}

	///////////////////////////////////////////////////////////////////
	//Konfigurationsbereich
	///////////////////////////////////////////////////////////////////

	//die Rohstoffleiste steht im großen Fenster der Desktop-Version links statt zentriert
	$flag_ang_big_iframe=true;

	//close-button
	if($_SESSION['ums_mobi']!=1 && $_SESSION['desktop_version']==0){
		$content.='<img onclick="closeIframeMain();" src="gp/g/close_icon.png" style="position: absolute; right: 1px; height: 26px; margin-top: 2px; width: auto; cursor: pointer;" alt="Fenster schlie&szlig;en" title="Fenster schlie&szlig;en">';
	}

	///////////////////////////////////////////////////////////////////
	// soll eine Technologie abgebrochen werden?
	///////////////////////////////////////////////////////////////////
	if(isset($_REQUEST['cancel_tech']) && $_REQUEST['cancel_tech']>0){
		$tech_id=intval($_REQUEST['cancel_tech']);
		$has_all=true;
		$need_storage_res=array();

		//überprüfen ob diese Technologie gerade läuft
		if(isset($pt[$tech_id]) && $pt[$tech_id]['time_finished']>time()){
			//test auf ausreichende Rohstoffe
			$einzelkosten=explode(';', $tech_daten[$tech_id]['tech_build_cost']);
			//print_r($einzelkosten);
			$ben_restyp01=0;
			$ben_restyp02=0;
			$ben_restyp03=0;
			$ben_restyp04=0;
			$ben_restyp05=0;
			foreach ($einzelkosten as $value) {
				$parts=explode("x", $value);

				//5 Grundrohstoffe
				if($value[0]=='R'){
					if($value[1]==1){
						if($pd['restyp01']<$parts[1]){$has_all=false;}
						$ben_restyp01=$parts[1];
					}elseif($value[1]==2){
						if($pd['restyp02']<$parts[1]){$has_all=false;}
						$ben_restyp02=$parts[1];
					}elseif($value[1]==3){
						if($pd['restyp03']<$parts[1]){$has_all=false;}
						$ben_restyp03=$parts[1];
					}elseif($value[1]==4){
						if($pd['restyp04']<$parts[1]){$has_all=false;}
						$ben_restyp04=$parts[1];
					}elseif($value[1]==5){
						if($pd['restyp05']<$parts[1]){$has_all=false;}
						$ben_restyp05=$parts[1];
					}
				}
				//Storage-Res
				elseif($value[0]=='I'){
					if($sv_deactivate_vsystems!=1){
						//V-Systeme sind aktiv
						//genug im storage vorhanden?
						$value1=str_replace('I','',$parts[0]);
						if($ps[$value1]['item_amount']<$parts[1]){$has_all=false;}
						//speichern wie viel man aus dem storage benötigt
						$need_storage_res[$value1]=$parts[1];
					}else{
						//V-Systeme sind inaktiv
						$value1=str_replace('I','',$parts[0]);
						if(!in_array($value1, array(3,4,5,6,7,8,9,10,11,12))){
							//V-Systeme sind aktiv
							//genug im storage vorhanden?

							if($ps[$value1]['item_amount']<$parts[1]){$has_all=false;}
							//speichern wie viel man aus dem storage benötigt
							$need_storage_res[$value1]=$parts[1];
						}
					}
				}
			}

			//Kosten gutschreiben
			$sql="UPDATE de_user_data SET
				restyp01=restyp01+'".$ben_restyp01."',
				restyp02=restyp02+'".$ben_restyp02."',
				restyp03=restyp03+'".$ben_restyp03."',
				restyp04=restyp04+'".$ben_restyp04."',
				restyp05=restyp05+'".$ben_restyp05."'
				WHERE user_id='".$_SESSION['ums_user_id']."';";

			//echo $sql;
			mysqli_query($GLOBALS['dbi'], $sql);

			//Item-Kosten gutschreiben
			foreach ($need_storage_res as $key => $value){
				change_storage_amount($_SESSION['ums_user_id'], $key, $value);
			}


			//Technologie in der DB hinterlegen
			//
			$time_finished=time()+floor($tech_daten[$tech_id]['tech_build_time']*$GLOBALS['tech_build_time_faktor']);
			$sql="DELETE FROM de_user_techs WHERE user_id='".$_SESSION['ums_user_id']."' AND tech_id='".$tech_id."';";
			//echo $sql;
			mysqli_query($GLOBALS['dbi'], $sql);
			$msg='<div class="mod-meldung mod-meldung-ok">Der Auftrag wurde abgebrochen, die Kosten wurden erstattet.</div>';

			//Daten erneut auslesen
			$pd=loadPlayerData($_SESSION['ums_user_id']);
			$pt=loadPlayerTechs($_SESSION['ums_user_id']);
			$row=$pd;
			$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];

		}else{
			$msg='<div class="mod-meldung mod-meldung-fehler">Der Auftrag wurde bereits abgeschlossen.</div>';
		}
	}

	///////////////////////////////////////////////////////////////////
	// soll eine Technologie erforscht/gebaut werden?
	///////////////////////////////////////////////////////////////////
	if(isset($_REQUEST['start_tech']) && $_REQUEST['start_tech']>0){
		//ist die Runde bereits gestartet?
		$result  = mysqli_query($GLOBALS['dbi'], "SELECT wt FROM de_system LIMIT 1");
		$row     = mysqli_fetch_array($result);
		$max_wt = $row["wt"];

		//nur baubare Technologien: unbekannte IDs hätten keine Kosten und keine Bauzeit
		if($max_wt>1 && isset($tech_daten[intval($_REQUEST['start_tech'])])){

			$tech_id=intval($_REQUEST['start_tech']);
			$has_all=true;

			$need_storage_res=array();

			//überprüfen ob schon eine Technologie dieses Typs in Bearbeitung ist
			$tech_typ=$tech_daten[$tech_id]['tech_typ'];
			if($active_tech_types[$tech_typ]==0){
				//überprüfen ob man diese Technologie bereits hat
				if(!isset($pt[$tech_id])){
					//Voraussetzungen

					if(!empty($tech_daten[$tech_id]['tech_vor'])){
						$vors=explode(";", $tech_daten[$tech_id]['tech_vor']);
						for($i=0;$i<count($vors);$i++){
							//Technologie als Voraussetzungen
							if($vors[$i][0]=='T'){
								$ben_tech_id=str_replace("T","",$vors[$i]);
								//erfüllt man die Voraussetzung?
								if(isset($pt[$ben_tech_id]) && $pt[$ben_tech_id]['time_finished']<=time()){
									//man hat es
								}else{
									//man hat es nicht
									$has_all=false;
								}
							}elseif($vors[$i][0]=='B'){ //Besondere Bedingungen
								//EH-Teilsiege
								if($vors[$i][1]=='1'){
									$parts=explode("x", $vors[$i]);
									if($sv_hardcore==1){
										if($pd['eh_siege']<$parts[1]){$has_all=false;}
									}
								}
							}

						}
					}

					//wenn die V-Systeme deaktiviert sind, dann sind die Techs auch nicht nutzbar
					if($sv_deactivate_vsystems==1 && $tech_typ==3){
						$has_all=false;
					}

					if($has_all){
						//test auf ausreichende Rohstoffe
						$einzelkosten=explode(';', $tech_daten[$tech_id]['tech_build_cost']);
						//print_r($einzelkosten);
						$ben_restyp01=0;
						$ben_restyp02=0;
						$ben_restyp03=0;
						$ben_restyp04=0;
						$ben_restyp05=0;
						foreach ($einzelkosten as $value) {
							$parts=explode("x", $value);

							//5 Grundrohstoffe
							if($value[0]=='R'){
								if($value[1]==1){
									if($pd['restyp01']<$parts[1]){$has_all=false;}
									$ben_restyp01=$parts[1];
								}elseif($value[1]==2){
									if($pd['restyp02']<$parts[1]){$has_all=false;}
									$ben_restyp02=$parts[1];
								}elseif($value[1]==3){
									if($pd['restyp03']<$parts[1]){$has_all=false;}
									$ben_restyp03=$parts[1];
								}elseif($value[1]==4){
									if($pd['restyp04']<$parts[1]){$has_all=false;}
									$ben_restyp04=$parts[1];
								}elseif($value[1]==5){
									if($pd['restyp05']<$parts[1]){$has_all=false;}
									$ben_restyp05=$parts[1];
								}
							}
							//Storage-Res
							elseif($value[0]=='I'){
								if($sv_deactivate_vsystems!=1){
									//V-Systeme sind aktiv
									//genug im storage vorhanden?
									$value1=str_replace('I','',$parts[0]);
									if($ps[$value1]['item_amount']<$parts[1]){$has_all=false;}
									//speichern wie viel man aus dem storage benötigt
									$need_storage_res[$value1]=$parts[1];
								}else{
									//V-Systeme sind inaktiv
									$value1=str_replace('I','',$parts[0]);
									if(!in_array($value1, array(3,4,5,6,7,8,9,10,11,12))){
										//V-Systeme sind aktiv
										//genug im storage vorhanden?
										if($ps[$value1]['item_amount']<$parts[1]){$has_all=false;}
										//speichern wie viel man aus dem storage benötigt
										$need_storage_res[$value1]=$parts[1];
									}
								}
							}elseif($value[0]=='B'){
								if($value[1]==1){
									if($sv_hardcore==1){
										if($pd['eh_siege']<$parts[1]){$has_all=false;}
									}
								}
							}
						}

						//test auf benötigte Technologien

						if($has_all){
							//Rohstoff-Kosten abziehen
							$sql="UPDATE de_user_data SET
								restyp01=restyp01-'".$ben_restyp01."',
								restyp02=restyp02-'".$ben_restyp02."',
								restyp03=restyp03-'".$ben_restyp03."',
								restyp04=restyp04-'".$ben_restyp04."',
								restyp05=restyp05-'".$ben_restyp05."'
								WHERE user_id='".$_SESSION['ums_user_id']."';";

							//echo $sql;
							mysqli_query($GLOBALS['dbi'], $sql);

							//Item-Kosten abziehen
							foreach ($need_storage_res as $key => $value){
								change_storage_amount($_SESSION['ums_user_id'], $key, $value*-1);
							}


							//Technologie in der DB hinterlegen
							//
							$time_finished=time()+floor($tech_daten[$tech_id]['tech_build_time']*$GLOBALS['tech_build_time_faktor']);
							$sql="INSERT INTO de_user_techs SET user_id='".$_SESSION['ums_user_id']."', tech_id='".$tech_id."', time_finished='".$time_finished."'";
							//echo $sql;
							mysqli_query($GLOBALS['dbi'], $sql);

							$tech_names=explode(";",$tech_daten[$tech_id]['tech_name']);
							$msg='<div class="mod-meldung mod-meldung-ok">'.$tech_names[$_SESSION['ums_rasse']-1].' l&auml;uft jetzt '.RealTime::until($time_finished).'.</div>';

							//Daten erneut auslesen
							$pd=loadPlayerData($_SESSION['ums_user_id']);
							$pt=loadPlayerTechs($_SESSION['ums_user_id']);
							$row=$pd;
							$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];


						}else{
							$msg='<div class="mod-meldung mod-meldung-fehler">Es sind nicht alle ben&ouml;tigten Rohstoffe/Voraussetzungen vorhanden.</div>';
						}
					}else{
						$msg='<div class="mod-meldung mod-meldung-fehler">Es sind nicht alle Voraussetzungen f&uuml;r diese Technologie erf&uuml;llt.</div>';
					}
				}else{
					$msg='<div class="mod-meldung mod-meldung-fehler">An einer Technologie dieser Art wird/wurde bereits gearbeitet.</div>';
				}

			}else{
				switch($tech_typ){
					case 0:
						$msg='<div class="mod-meldung mod-meldung-fehler">Es wird bereits an einem Geb&auml;ude gearbeitet.</div>';
					break;
					case 1:
						$msg='<div class="mod-meldung mod-meldung-fehler">Es wird bereits eine Technologie erforscht.</div>';
					break;
					default:
						$msg='<div class="mod-meldung mod-meldung-fehler">An einer Technologie dieser Art wird bereits gearbeitet.</div>';
					break;


				}
			}
		}else{
			$msg='<div class="mod-meldung mod-meldung-fehler">Die Runde l&auml;uft noch nicht.</div>';
		}
	}


	///////////////////////////////////////////////////////////////////
	//nochmal auslesen, welche Technologietypen aktuell in Bearbeitung sind sind
	///////////////////////////////////////////////////////////////////
	$active_tech_types=array();
	$active_tech_types_row=array();
	for($i=0;$i<$anzahl_tech_types;$i++){
		$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_techs LEFT JOIN de_tech_data ON (de_user_techs.tech_id=de_tech_data.tech_id)
			WHERE user_id='".$_SESSION['ums_user_id']."' AND tech_typ='$i' AND time_finished>='".time()."';");
		$num=mysqli_num_rows($db_daten);
		$active_tech_types[$i] = $num;
		if($num>0){
			$row = mysqli_fetch_array($db_daten);
			$active_tech_types_row[$i]=$row;
		}
	}

	//je Technologietyp ein Bauplatz; Texte der Status, die js/ang_techs.js beim Nachziehen ebenso setzt
	$tc_plaetze=array(0 => 'Geb&auml;ude', 1 => 'Forschung', 2 => 'Basisschiff', 3 => 'V-Systeme');
	$tc_reiter=array(-1 => 'Alle', 0 => 'Geb&auml;ude', 1 => 'Forschungen', 2 => 'Basisschiffe', 3 => 'V-Systeme');
	if($sv_deactivate_vsystems==1){
		unset($tc_plaetze[3], $tc_reiter[3]);
	}
	$tc_texte=array('baubar' => 'baubar', 'res' => 'Rohstoffe fehlen', 'platz' => 'Bauplatz belegt', 'vor' => 'Voraussetzung fehlt', 'laeuft' => 'läuft', 'erledigt' => 'erledigt');
	$tc_chips=array('baubar' => ' mod-chip-gruen', 'res' => ' mod-chip-warn', 'platz' => '', 'vor' => '', 'laeuft' => '', 'erledigt' => '');

	$content.='<div class="mod tc tc-'.($tech_anordnung==1 ? 'liste' : 'baum').'" id="tc" data-typ="'.$tech_filter_typ.'" data-status="'.$tech_status.'" data-ton="'.($tech_sound==0 ? 1 : 0).'" data-texte="'.htmlspecialchars(json_encode($tc_texte), ENT_QUOTES, 'UTF-8').'">';
	$content.='<div class="tc-oben">';

	//Kopf: Ansicht und Ton
	$content.='<div class="tc-kopf"><b class="tc-titel">Technologien</b><div class="tc-schalter">';
	$content.='<span class="mod-typ">Ansicht</span><span class="tc-seg"><button type="button" data-ansicht="0">Baum</button><button type="button" data-ansicht="1">Liste</button></span>';
	$content.='<span class="mod-typ">Ton</span><span class="tc-seg"><button type="button" data-ton="0">an</button><button type="button" data-ton="1">aus</button></span>';
	$content.='</div></div>';

	if(!empty($msg)){
		$content.=$msg;
	}

	///////////////////////////////////////////////////////////////////
	//Bauplätze: laufender Auftrag oder frei
	///////////////////////////////////////////////////////////////////
	$content.='<div class="tc-plaetze">';
	foreach($tc_plaetze as $i => $platz_name){
		if(isset($active_tech_types[$i]) && $active_tech_types[$i]>0){
			$tech_names=explode(";",$active_tech_types_row[$i]['tech_name']);
			$tech_name=$tech_names[$_SESSION['ums_rasse']-1];

			$content.='<div class="tc-platz tc-typ-'.$i.'" data-platz="'.$i.'"><span class="mod-typ">'.$platz_name.'</span><div class="tc-platz-inhalt">';
			$content.='<b>'.$tech_name.'</b><small><span id="tech_ende'.$i.'">'.RealTime::until($active_tech_types_row[$i]['time_finished']).'</span><span id="tech_counter'.$i.'" hidden></span></small>';
			$content.='<a href="ang_techs.php?cancel_tech='.$active_tech_types_row[$i]['tech_id'].'" class="mod-btn mod-btn-gefahr ally-btn-klein" data-bestaetigen="Wirklich abbrechen?" title="Die Kosten werden erstattet.">Abbrechen</a>';
			$content.='</div></div>';

			//nach Ablauf die Technologie in der Liste unten wie eine erledigte behandeln; angezeigt wird die Endzeit
			//(Forschung läuft in Echtzeit), der Countdown läuft dafür unsichtbar mit, den Ton spielt tc_fertig()
			$content.='<script type="text/javascript">ang_countdown('.($active_tech_types_row[$i]['time_finished']-time()).',"tech_counter'.$i.'",0,function(){ tc_fertig('.intval($active_tech_types_row[$i]['tech_id']).', '.$i.'); })</script>';
		}else{
			$content.='<div class="tc-platz tc-typ-'.$i.'" data-platz="'.$i.'" data-frei="1"><span class="mod-typ">'.$platz_name.'</span><div class="tc-platz-inhalt"><span class="tc-frei">frei</span></div></div>';
		}
	}
	$content.='</div>';

	//Filter: Typ und Status, ohne Neuladen (js/ang_techs.js)
	$content.='<div class="ally-navi tc-typen">';
	foreach($tc_reiter as $typ => $reiter_name){
		$content.='<button type="button" class="ally-reiter'.($typ>=0 ? ' tc-typ-'.$typ : '').'" data-filter-typ="'.$typ.'">'.$reiter_name.'</button>';
	}
	$content.='</div>';
	$content.='<div class="tc-filter"><span class="tc-seg">';
	foreach(array('baubar' => 'Baubar', 'offen' => 'Offen', 'erledigt' => 'Erledigt', 'alle' => 'Alle') as $status => $status_name){
		$content.='<button type="button" data-filter-status="'.$status.'">'.$status_name.' <b data-anzahl="'.$status.'"></b></button>';
	}
	$content.='</span></div>';
	$content.='</div>';//tc-oben

	///////////////////////////////////////////////////////////////////
	//die Technologien durchgehen: Status, Kosten, fehlende Voraussetzungen
	///////////////////////////////////////////////////////////////////
	$tc_stufen=array();
	foreach($tech_daten as $row){
		//wenn die V-Systeme deaktiviert sind, dann sind die Techs auch nicht nutzbar
		if($sv_deactivate_vsystems==1 && $row['tech_typ']==3){
			continue;
		}

		$bereits_fertig=isset($pt[$row['tech_id']]) && $pt[$row['tech_id']]['time_finished']<=time();
		$laeuft=isset($pt[$row['tech_id']]) && !$bereits_fertig;

		//Kosten, rot wenn ein Posten nicht reicht
		$res_ok=true;
		$kosten='';
		foreach (explode(';', $row['tech_build_cost']) as $value) {
			$parts=explode("x", $value);
			$posten='';
			$fehlt=false;

			//5 Grundrohstoffe
			if($value[0]=='R'){
				$kurz=array(1 => 'M', 2 => 'D', 3 => 'I', 4 => 'E', 5 => 'T');
				if(isset($kurz[$value[1]])){
					$fehlt=$pd['restyp0'.$value[1]]<$parts[1];
					$posten=number_format($parts[1],0,",",".").' '.$kurz[$value[1]];
				}
			}elseif($value[0]=='I'){
				$value1=str_replace('I','',$parts[0]);
				//bei deaktivierten V-Systemen zählen deren Waren (3-12) nicht
				if($sv_deactivate_vsystems!=1 || !in_array($value1, array(3,4,5,6,7,8,9,10,11,12))){
					$fehlt=$ps[$value1]['item_amount']<$parts[1];
					$posten=number_format($parts[1],0,",",".").' '.$ps[$value1]['item_name'];
				}
			}

			if($posten!=''){
				if($fehlt){
					$res_ok=false;
				}
				$kosten.='<span'.($fehlt ? ' class="tc-fehlt"' : '').'>'.$posten.'</span>';
			}
		}

		//fehlende Voraussetzungen; data-vor-id, damit tc_fertig() sie entfernen kann
		$vor='';
		$fehlt_ids=array();
		$sonder=false;
		if(!empty($row['tech_vor'])){
			foreach(explode(";", $row['tech_vor']) as $v){
				//Technologie als Voraussetzungen
				if($v[0]=='T'){
					$ben_tech_id=str_replace("T","",$v);
					if(!(isset($pt[$ben_tech_id]) && $pt[$ben_tech_id]['time_finished']<=time())){
						$tech_names=explode(";",$tech_daten[$ben_tech_id]['tech_name']);
						$vor.='<span class="tc-fehlt" data-vor-id="'.intval($ben_tech_id).'">'.$tech_names[$_SESSION['ums_rasse']-1].'</span>';
						$fehlt_ids[]=intval($ben_tech_id);
					}
				}elseif($v[0]=='B'){ //Besondere Bedingungen
					//EH-Teilsiege
					if($v[1]=='1'){
						$parts=explode("x", $v);
						if($sv_hardcore==1 && $pd['eh_siege']<$parts[1]){
							$vor.='<span class="tc-fehlt">'.$parts[1].' EH-Teilsieg(e)</span>';
							$sonder=true;
						}
					}
				}
			}
		}

		//Status wie beim Start oben geprüft; js/ang_techs.js (tc_neu) bestimmt ihn genauso neu
		if($bereits_fertig){
			$status='erledigt';
		}elseif($laeuft){
			$status='laeuft';
		}elseif(!empty($fehlt_ids) || $sonder){
			$status='vor';
		}elseif(!$res_ok){
			$status='res';
		}elseif($active_tech_types[$row['tech_typ']]>0){
			$status='platz';
		}else{
			$status='baubar';
		}

		$tech_names=explode(";",$row['tech_name']);
		$tech_name=$tech_names[$_SESSION['ums_rasse']-1];

		$kachel='<div class="tc-tech tc-typ-'.$row['tech_typ'].' tc-st-'.$status.'" data-tech-id="'.$row['tech_id'].'" data-typ="'.$row['tech_typ'].'" data-status="'.$status.'" data-res="'.($res_ok ? 1 : 0).'" data-sonder="'.($sonder ? 1 : 0).'" data-fehlt="'.implode(',', $fehlt_ids).'">';
		$kachel.='<div class="tc-tech-kopf"><span class="tc-name">'.$tech_name.'</span><span class="mod-chip tc-chip'.$tc_chips[$status].'">'.$tc_texte[$status].'</span></div>';
		if(!$bereits_fertig && !$laeuft){
			$kachel.='<div class="tc-kosten">'.$kosten.'<small>Dauer '.formatTime($row['tech_build_time']*$GLOBALS['tech_build_time_faktor']).'</small></div>';
			if($vor!=''){
				$kachel.='<div class="tc-vor">Ben&ouml;tigt: '.$vor.'</div>';
			}
		}
		//unten: Beschreibung zum Aufklappen und Starten bei erfüllten Voraussetzungen; ob Rohstoffe und Bauplatz
		//reichen, prüft der Start (nach einem WT oder einem fertigen Auftrag kann es inzwischen klappen)
		$kachel.='<div class="tc-unten">';
		if(!empty($row['tech_desc'])){
			$tech_descs=explode(";",$row['tech_desc']);
			$kachel.='<details class="tc-info"><summary>Beschreibung</summary>'.$tech_descs[$_SESSION['ums_rasse']-1].'</details>';
		}
		if(in_array($status, array('baubar', 'res', 'platz'))){
			$kachel.='<div class="tc-fuss"><a href="ang_techs.php?start_tech='.$row['tech_id'].'" class="mod-btn ally-btn-klein'.($status=='baubar' ? '' : ' mod-btn-leise').'">Starten</a></div>';
		}
		$kachel.='</div></div>';

		$tc_stufen[$row['tech_level']]=($tc_stufen[$row['tech_level']] ?? '').$kachel;
	}

	//je Stufe eine Spalte (Baum) bzw. ein Abschnitt (Liste)
	ksort($tc_stufen);
	$content.='<div class="tc-stufen">';
	foreach($tc_stufen as $stufe => $kacheln){
		$content.='<section class="tc-stufe"><span class="mod-typ tc-stufe-titel">Stufe '.$stufe.'</span><div class="tc-stufe-techs">'.$kacheln.'</div></section>';
	}
	$content.='</div>';
	$content.='<div class="mod-leer tc-leer" hidden>F&uuml;r diesen Filter gibt es keine Technologien.</div>';
	$content.='</div>';//tc

	$content.='<script type="text/javascript">$(function(){ tc_init(); });</script>';


	$erg = releaseLock($_SESSION['ums_user_id']); //L&ouml;sen des Locks und Ergebnisabfrage
	if ($erg){
		  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
	}else{
		  print('Transaction: Dataset could not be unlocked!<br><br><br>');
	}
}else{
	print('Transaction: Dataset could not be locked!<br><br><br>');
}

include "resline.php";

echo $content;

?>
</div>
</body>
</html>
