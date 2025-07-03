<?php
include 'inc/header.inc.php';
include 'lib/transactioni.lib.php';
include 'functions.php';

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Technologien</title>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
	<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>

	<link rel="stylesheet" href="js/jquery-ui.min-1.12.0.css">
	<link rel="stylesheet" href="g/style.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/g/style.css');?>">

<?php
//include "cssinclude.php";
?>
</head>
<body style="margin: 0; padding:0; color: #FFFFFF;">
	<div style="position: absolute; background-color: rgba(10,10,10,0.95); height: 100%; width: 100%; min-width: 100%; overflow:auto;">	
		
<?php
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

	$tech_anordnung=					0;
	$tech_erledigte_techs=				0;
	$tech_techs_ohne_voraussetzung =	0;
	$tech_kosten=						0;
	$tech_vor=							0;
	$tech_desc=							0;
	$tech_sound=						0;

	
	//Cookie-Daten laden
	if(isset($_COOKIE['tech_anordnung'])){
		$tech_anordnung=					intval($_COOKIE['tech_anordnung']);
	}
	
	if(isset($_COOKIE['tech_erledigte_techs'])){
		$tech_erledigte_techs=				intval($_COOKIE['tech_erledigte_techs']);
	}
	
	if(isset($_COOKIE['tech_techs_ohne_voraussetzung'])){
		$tech_techs_ohne_voraussetzung =	intval($_COOKIE['tech_techs_ohne_voraussetzung']);
	}
	
	if(isset($_COOKIE['tech_kosten'])){
		$tech_kosten=						intval($_COOKIE['tech_kosten']);
	}

	if(isset($_COOKIE['tech_vor'])){
		$tech_vor=							intval($_COOKIE['tech_vor']);
	}
	
	if(isset($_COOKIE['tech_desc'])){
		$tech_desc=							intval($_COOKIE['tech_desc']);
	}

	if(isset($_COOKIE['tech_sound'])){
		$tech_sound=						intval($_COOKIE['tech_sound']);
	}

	//print_r($pt);
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

	$flag_ang_big_iframe=true;

	include "cssinclude.php";
	
	$deactivate_touch_menu=0;
	if($_SESSION['ums_mobi']==1 && $tech_anordnung==0){
		$deactivate_touch_menu=1;
	}

	//close-button
	if($_SESSION['ums_mobi']!=1 && $_SESSION['desktop_version']==0){
		$content.='<img onclick="closeIframeMain();" src="g/close_icon.png" style="position: absolute; right: 1px; height: 26px; margin-top: 2px; width: auto; cursor: pointer;" alt="Fenster schlie&szlig;en" title="Fenster schlie&szlig;en">';
	}

	$class='';

	$content.='
		<div id="tech_config" class="invisible" style="z-index: 100; position: fixed; top: 20px; left: 3px; background-color: #000000; width: 400px; border: 1px solid #EEEEEE; color: #EEEEEE; padding: 5px;">
			<table style="width: 100%;">
			<tr>
				<td>Anordnung:</td>
				<td style="text-align: right;">'.sf('tech_anordnung', array(0=>'mehrere Spalten', 1=>'eine Spalte'), $tech_anordnung, $class, 'onChange="onchange_select(\'tech_anordnung\')"').'</td>
			</tr>
			<tr>
				<td>Erledigte Technologien:</td>
				<td style="text-align: right;">'.sf('tech_erledigte_techs', array(0=>'ausblenden', 1=>'nicht ausblenden'), $tech_erledigte_techs, $class, 'onChange="onchange_select(\'tech_erledigte_techs\')"').'</td></td>
			</tr>
			<tr>
				<td>Technologien mit fehlenden Voraussetzungen:</td>
				<td style="text-align: right;">'.sf('tech_techs_ohne_voraussetzung', array(0=>'nicht ausblenden', 1=>'ausblenden'), $tech_techs_ohne_voraussetzung, $class, 'onChange="onchange_select(\'tech_techs_ohne_voraussetzung\')"').'</td></td>
			</tr>
			<tr>
				<td>Anzeige Kosten:</td>
				<td style="text-align: right;">'.sf('tech_kosten', array(0=>'Mouseover', 1=>'direkt'), $tech_kosten, $class, 'onChange="onchange_select(\'tech_kosten\')"').'</td></td>
			</tr>
			<tr>
				<td>Anzeige ben&ouml;tigte Technologien:</td>
				<td style="text-align: right;">'.sf('tech_vor', array(0=>'Mouseover', 1=>'direkt'), $tech_vor, $class, 'onChange="onchange_select(\'tech_vor\')"').'</td></td>
			</tr>
			<tr>
				<td>Anzeige Beschreibung:</td>
				<td style="text-align: right;">'.sf('tech_desc', array(0=>'Mouseover', 1=>'direkt', 2=>'gar nicht'), $tech_desc, $class, 'onChange="onchange_select(\'tech_desc\')"').'</td></td>
			</tr>

			<tr>
				<td>Sound:</td>
				<td style="text-align: right;">'.sf('tech_sound', array(0=>'an', 1=>'aus'), $tech_sound, $class, 'onChange="onchange_select(\'tech_sound\')"').'</td></td>
			</tr>
			
			</table>
			
			<span style="font-size: 12px;">Um die &Auml;nderungen zu sehen, bitte die Seite aktualisieren.</span>
		</div>
		';

	/////////////////////////////////////////////////////
	/////////////////////////////////////////////////////
	//Konfiguration und laufende Technologien
	$content.='
	<div style="display: flex; min-height: 32px; margin-top: 5px; margin-bottom: 8px;">
		<div><img onclick="$(\'#tech_config\').toggleClass(\'invisible\');" src="g/button_config.png" style="margin-left: 8px; margin-right: 8px; height: 32px; width: auto; cursor: pointer;"></div>
	';		

	///////////////////////////////////////////////////////////////////
	// soll eine Technologie abgebrochen werden?
	///////////////////////////////////////////////////////////////////
	if(isset($_REQUEST['cancel_tech']) && $_REQUEST['cancel_tech']>0){
		$tech_id=intval($_REQUEST['cancel_tech']);
		$has_all=true;
		$need_storage_res=array();
		
		//überprüfen ob diese Technologie gerade l�uft
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
			$msg='<span class="text_green">Der Auftrag wurde abgebrochen.</span>';

			//Daten erneut auslesen
			$pd=loadPlayerData($_SESSION['ums_user_id']);
			$pt=loadPlayerTechs($_SESSION['ums_user_id']);
			$row=$pd;
			$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
			
		}else{
			$msg='<span class="text_red">Der Auftrag wurde bereits abgeschlossen.</span>';
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

		if($max_wt>1){

			$tech_id=intval($_REQUEST['start_tech']);
			$has_all=true;
			
			$need_storage_res=array();

			//�berpr�fen ob schon eine Technologie dieses Typs in Bearbeitung ist
			$tech_typ=$tech_daten[$tech_id]['tech_typ'];
			if($active_tech_types[$tech_typ]==0){
				//�berpr�fen ob man diese Technologie bereits hat
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

							//Daten erneut auslesen
							$pd=loadPlayerData($_SESSION['ums_user_id']);
							$pt=loadPlayerTechs($_SESSION['ums_user_id']);
							$row=$pd;
							$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
							

						}else{
							$msg='<span class="text_red">Es sind nicht alle ben&ouml;tigten Rohstoffe/Voraussetzungen vorhanden.</span>';
						}
					}else{
						$msg='<span class="text_red">Es sind nicht alle Voraussetzungen f&uuml;r diese Technologie erf&uuml;llt.</span>';
					}
				}else{
					$msg='<span class="text_red">An einer Technologie dieser Art wird/wurde bereits gearbeitet.</span>';
				}

			}else{
				switch($tech_typ){
					case 0:
						$msg='<span class="text_red">Es wird bereits an einem Geb&auml;ude gearbeitet.</span>';
					break;
					case 1:
						$msg='<span class="text_red">Es wird bereits eine Technologie erforscht.</span>';
					break;
					default:
						$msg='<span class="text_red">An einer Technologie dieser Art wird bereits gearbeitet.</span>';
					break;


				}
			}
		}else{
			$msg='<span class="text_red">Die Runde l&auml;uft noch nicht.</span>';
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
	
	///////////////////////////////////////////////////////////////////
	//aktive Prozesse ausgeben
	///////////////////////////////////////////////////////////////////
	//print_r($active_tech_types);
	for($i=0;$i<$anzahl_tech_types;$i++){
		if(isset($active_tech_types[$i]) && $active_tech_types[$i]>0){
			$tech_names=explode(";",$active_tech_types_row[$i]['tech_name']);
			$tech_name=$tech_names[$_SESSION['ums_rasse']-1];
			$tech_typ=$active_tech_types_row[$i]['tech_typ'];

			$content.='<div class="tech tech_typ_'.$tech_typ.'" style="width: initial;">
			<div class="tech_bg'.$tech_typ.'"></div>
			';

			$content.='<div class="tech_name uppercase" rel="tooltip" title="Fertigstellung: '.date("H:i:s d.m.Y", $active_tech_types_row[$i]['time_finished']).'">'.$tech_name.' (<span id="tech_counter'.$i.'"></span>) <a href="ang_techs.php?cancel_tech='.$active_tech_types_row[$i]['tech_id'].'" class="btn2" onclick="return confirm(unescape(\'Auftrag abbrechen? Die Rohstoffkosten werden erstattet.\'))" >Abbruch</a></div>';
			if($tech_sound==0){
				$sound_id=1;
			}else{
				$sound_id=0;
			}


			$content.='</div>';

			$content.='<script type="text/javascript">ang_countdown('.($active_tech_types_row[$i]['time_finished']-time()).',"tech_counter'.$i.'",'.$sound_id.')</script>';
		}
	}

	//flex-box Konfiguration / laufende Techs schließen
	$content.='</div>';
	
	if(!empty($msg)){
		$content.='<div style="margin: 20px;">'.$msg.'</div>';
	}


	//////////////////////////////////////////////////////////////////////
	// nach Technologietyp filtern
	//////////////////////////////////////////////////////////////////////

	$content.='
	<div style="display: flex; padding-bottom: 10px;">
		<div style="margin-left: 10px;"><a href="javascript: show_tech_typ(-1);" class="btn">alle</a></div>
		<div style="margin-left: 10px;"><a href="javascript: show_tech_typ(0);" class="btn">Gebäude</a></div>
		<div style="margin-left: 10px;"><a href="javascript: show_tech_typ(1);" class="btn">Forschungen</a></div>';
		if($_SESSION['ums_mobi']==1 || $_SESSION['desktop_version']==1){
			$content.='</div><div style="display: flex; padding-bottom: 10px;">';
	}
	$content.='
		<div style="margin-left: 10px;"><a href="javascript: show_tech_typ(2);" class="btn">Basisschiffe</a></div>';
	if($sv_deactivate_vsystems!=1){
		$content.='
		<div style="margin-left: 10px;"><a href="javascript: show_tech_typ(3);" class="btn">V-Systeme</a></div>';
	}
	$content.='
	</div>
	';

	///////////////////////////////////////////////////////////////////
	//die Technlogien durchgehen und entsprechend ausgeben
	///////////////////////////////////////////////////////////////////
	$tech_output=array();
	$db_daten=mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_tech_data WHERE tech_sort_id < 1000 ORDER BY tech_level ASC, tech_sort_id ASC");
	while($row = mysqli_fetch_array($db_daten)){
		//alle Voraussetzungen vorhanden-Flag setzen
		$has_all=true;


		if(isset($pt[$row['tech_id']]) && $pt[$row['tech_id']]['time_finished']<=time()){
			//man hat es
			$bereits_fertig=true;
			//echo '<br>1: '.$pt[$row['tech_id']]['time_finished'];
		}else{
			//man hat es nicht
			$bereits_fertig=false;
			//echo '<br>2: '.$pt[$row['tech_id']]['time_finished'];
		}


		//Kosten
		$kosten='';

		$kosten.='<br><br>Kosten:';
		$einzelkosten=explode(';', $row['tech_build_cost']);
		foreach ($einzelkosten as $value) {
			/*
			if($kosten!='<span style="color: #00AA00;">'){
				$kosten.='<br>';
			}*/
			$kosten.='<br>';

			$parts=explode("x", $value);

			//5 Grundrohstoffe
			if($value[0]=='R'){
				if($value[1]==1){
					if($pd['restyp01']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' M';
					if($pd['restyp01']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==2){
					if($pd['restyp02']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' D';
					if($pd['restyp02']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==3){
					if($pd['restyp03']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' I';
					if($pd['restyp03']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==4){
					if($pd['restyp04']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' E';
					if($pd['restyp04']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==5){
					if($pd['restyp05']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' T';
					if($pd['restyp05']<$parts[1]){$kosten.='</span>';}
				}
			}elseif($value[0]=='I'){
				if($sv_deactivate_vsystems!=1){
					//V-Systeme sind aktiv
					$value1=str_replace('I','',$parts[0]);
					if($ps[$value1]['item_amount']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' '.$ps[$value1]['item_name'];
					if($ps[$value1]['item_amount']<$parts[1]){$kosten.='</span>';}
				}else{
					//V-Systeme sind inaktiv
					$value1=str_replace('I','',$parts[0]);
					if(!in_array($value1, array(3,4,5,6,7,8,9,10,11,12))){
						//V-Systeme sind aktiv
						if($ps[$value1]['item_amount']<$parts[1]){$kosten.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;}
						$kosten.=number_format($parts[1],0,",",".");
						$kosten.=' '.$ps[$value1]['item_name'];
						if($ps[$value1]['item_amount']<$parts[1]){$kosten.='</span>';}
					}
				}
			}
		}

		//Bauzeit bzw. fertig gestellt

		$bauzeit='<br><br><span style=\'color: rgba(255,255,255, 0.4)\'>Dauer: '.formatTime($row['tech_build_time']*$GLOBALS['tech_build_time_faktor']).'</span>';
		//$bauzeit_gesamt+=$row['tech_build_time'];

		//Voraussetzungen
		$voraussetzungen='';
		$has_voraussetzungen=true;
		if(!empty($row['tech_vor'])){
			$voraussetzungen.='<br><br><span>Voraussetzungen: ';
			$vors=explode(";", $row['tech_vor']);
			for($i=0;$i<count($vors);$i++){
				//Technologie als Voraussetzungen
				if($vors[$i][0]=='T'){
					$ben_tech_id=str_replace("T","",$vors[$i]);
				
					$tech_names=explode(";",$tech_daten[$ben_tech_id]['tech_name']);
					$tech_name=$tech_names[$_SESSION['ums_rasse']-1];

					//erfüllt man die Voraussetzung?
					if(isset($pt[$ben_tech_id]) && $pt[$ben_tech_id]['time_finished']<=time()){
						//man hat es
						$voraussetzungen.='<br>'.$tech_name;
					}else{
						//man hat es nicht
						$voraussetzungen.='<br><span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>'.$tech_name.'</span>';
						$has_all=false;
						$has_voraussetzungen=false;
					}
				}elseif($vors[$i][0]=='B'){ //Besondere Bedingungen
					//EH-Teilsiege
					if($vors[$i][1]=='1'){
						$parts=explode("x", $vors[$i]);
						if($sv_hardcore==1){
							$voraussetzungen.='<br>';
							if($pd['eh_siege']<$parts[1]){$voraussetzungen.='<span style=\'color: #FFFFFF; background-color: #AA0000; padding: 0 3px 0 3px;\'>';$has_all=false;$has_voraussetzungen=false;}
							$voraussetzungen.=$parts[1].' EH-Teilsieg(e)';
							if($pd['eh_siege']<$parts[1]){$voraussetzungen.='</span>';}
						}
					}
				}


			}
			$voraussetzungen.='</span>';
		}

		//Beschreibung
		$beschreibung='';
		if(!empty($row['tech_desc'])){
			$beschreibung.='<br><br>Beschreibung:</br>';
			$tech_descs=explode(";",$row['tech_desc']);
			$beschreibung.=$tech_descs[$_SESSION['ums_rasse']-1];
		}	

		//tooltip je nach Einstellungen zusammensetzen
		$title='';
		if($tech_kosten==0){
			$title.=$kosten;
			$title.=$bauzeit;
		}		
		if($tech_vor==0){
			$title.=$voraussetzungen;
		}
		if($tech_desc==0){
			$title.=$beschreibung;
		}
		
		//$title=$kosten.$bauzeit.$voraussetzungen.$beschreibung;

		//Ausgabe
		$tech_names=explode(";",$row['tech_name']);
		$tech_name=$tech_names[$_SESSION['ums_rasse']-1];

		/*
		if($has_all){
			$tech_name_class='tech_name';
			$baulink[0]='<a style="text-decoration: none;" href="ang_techs.php?start_tech='.$row['tech_id'].'">';
			$baulink[1]='</a>';
		}else{
			$tech_name_class='tech_name';
			$baulink[0]='<a style="text-decoration: none;" href="ang_techs.php?start_tech='.$row['tech_id'].'">';
			$baulink[1]='</a>';
		}*/
		$hide_tech=false;
		if(!$bereits_fertig){
			$tech_name_class='tech_name';
			//$baulink[0]='<a style="text-decoration: none;" href="ang_techs.php?start_tech='.$row['tech_id'].'">';
			//$baulink[1]='</a>';
			$baulink[0]='';
			$baulink[1]='';
			$baujs='onclick="window.location.href=\'ang_techs.php?start_tech='.$row['tech_id'].'\';"';
		}else{
			//fertig, test ob man es generell ausblenden soll
			if($tech_erledigte_techs==0){
				$hide_tech=true;
			}
			
			$tech_name_class='tech_name_grey';
			$baulink[0]='';
			$baulink[1]='';
			$baujs='';
		}	

		if($tech_anordnung==0){
			$tech_class='tech';
		}else{
			$tech_class='tech_einspaltig';
		}

		//noch nicht erforschte Techs ausblenden?
		//echo 'A: '.$tech_techs_ohne_voraussetzung;
		if($tech_techs_ohne_voraussetzung==1 && $has_voraussetzungen==false){
			$hide_tech=true;
		}


		//wenn die V-Systeme deaktiviert sind, dann sind die Techs auch nicht nutzbar
		if($sv_deactivate_vsystems==1 && $row['tech_typ']==3){
			$hide_tech=true;
		}
		
		if(!$hide_tech){

			if(strpos($title, '<br><br>')===0){
				//str_replace('<br><br>', '<br>', $title);
				
				$title=preg_replace('/<br><br>/', '<br>', $title, 1);

			}

			if(!empty($title)){
				$title='<div class=\'uppercase\'>'.$tech_name.'</div>'.$title;
			}

			$tech_field='
				<div class="'.$tech_class.' tech_typ_'.$row['tech_typ'].'" '.$baujs.' title="'.$title.'" rel="tooltip">
					<div class="tech_bg'.$row['tech_typ'].'"></div>
					'.$baulink[0].'<div class="'.$tech_name_class.'"><span class="uppercase">'.$tech_name.'</span>';
			
					
			//ggf. die direkt anzuzeigenden Daten ausgeben
			if($tech_kosten==1){
				$tech_field.=$kosten;
				$tech_field.=$bauzeit;
			}		
			if($tech_vor==1){
				$tech_field.=$voraussetzungen;
			}
			if($tech_desc==1){
				$tech_field.=$beschreibung;
			}					
					
			$tech_field.=$baulink[1];
			
			$tech_field.=
					'</div>';
			
			$tech_field.='
				</div>';
			
			if(isset($tech_output[$row['tech_level']])){
				$tech_output[$row['tech_level']].=$tech_field;
			}else{
				$tech_output[$row['tech_level']]=$tech_field;
			}

			
		}
		//echo $tech_field;

		

	}

	//print_r($tech_output);
	if($tech_anordnung==0){$content.='<table><tr style="vertical-align: top;">';}
	for($i=0;$i<=100;$i++){
		if(!empty($tech_output[$i])){
			if($tech_anordnung==0){$content.='<td class="font1">';}
			$content.=$tech_output[$i];
			if($tech_anordnung==0){$content.='</td>';}
		}
	}
	if($tech_anordnung==0){$content.='</tr></table>';}


	if(isset($_COOKIE['tech_filter_typ'])){
		$tech_filter_typ=intval($_COOKIE['tech_filter_typ']);
	}else{
		$tech_filter_typ=-1;
	}

	$content.='
	<script>
	$( document ).ready(function() {
		show_tech_typ('.$tech_filter_typ.');
	});	
	</script>
	';


	$erg = releaseLock($ums_user_id); //L&ouml;sen des Locks und Ergebnisabfrage
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