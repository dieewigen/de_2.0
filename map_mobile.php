<?php
$GLOBALS['deactivate_old_design']=true;

include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";

include 'lib/map_system_defs.inc.php';
include "lib/map_system.class.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$mysc2=$row["sc2"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$spec1=$row['spec1'];$spec3=$row['spec3'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Vergessene Systeme</title>
<?php 
//$newcss=1;
include "cssinclude.php";
?>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>
</head>
<body>

<?php

if(isset($sv_deactivate_vsystems) && $sv_deactivate_vsystems==1){
	include "resline.php";
	echo '<br><div class="info_box text2">Auf diesem Server sind die Vergessenen Systeme deaktiviert.</div>';

	die('</body></html>');
}

include "resline.php";

//hat man die benötigte Technologie?
if(!hasTech($pt,25)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=25";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	echo '<br>';
	rahmen_oben('Fehlende Technologie');
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=28" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_25.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();  
}else{
	rahmen_oben('Vergessene Systeme');

	///////////////////////////////////////////////////////////////////////////
	// automatisches erkunden, nur anzeigen, wenn man noch nicht alles erkundet hat
	///////////////////////////////////////////////////////////////////////////
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT COUNT(*) AS anzahl FROM de_map_objects");
	$row = mysqli_fetch_array($db_daten);
	$anzahl_systeme=$row['anzahl'];
	
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT COUNT(*) AS anzahl FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."';");
	$row = mysqli_fetch_array($db_daten);
	$anzahl_systeme_entdeckt=$row['anzahl'];	

	if($anzahl_systeme_entdeckt<$anzahl_systeme && $anzahl_systeme_entdeckt>0){

		//status ändern
		if(isset($_REQUEST['set_auto_explore'])){
			$sql="UPDATE de_user_data SET vs_auto_explore=".intval($_REQUEST['set_auto_explore'])." WHERE user_id='".$_SESSION['ums_user_id']."';";
			//echo $sql;
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			$pd['vs_auto_explore']=intval($_REQUEST['set_auto_explore']);
		}

		if($pd['vs_auto_explore']==1){
			$btn_text='deaktivieren';
			$target_value=0;
		}else{
			$btn_text='aktivieren';
			$target_value=1;
		}

		echo '
		<div class="cell fett">
			<div style="display: flex; padding-bottom: 20px;">
				<div style="width: 160px;">Automatische Erkundung </div>
				<div style="flex-grow: 1; text-align: left; margin-top: -4px;"><a href="?set_auto_explore='.$target_value.'" class="btn">'.$btn_text.'</a></div>
		</div>';
	}

	///////////////////////////////////////////////////////////////////////////
	// Filter für die einzelnen Systeme
	///////////////////////////////////////////////////////////////////////////
	echo '
	<div class="" style="display: flex; border-top: 1px solid #999999; padding-top: 10px;">
		<div style="flex-grow: 1; padding-top: 6px;">Filterkriterien:</div>
		<div style="flex-grow: 1;">
		<select name="vsf0a" id="vsf0a" onChange="vs_filter(1);">';
	for($i=0; $i<count($GLOBALS['map_buildings']);$i++){
		if(!empty($GLOBALS['map_buildings'][$i]['bldg_filter_tag'])){
			echo '<option value="'.$GLOBALS['map_buildings'][$i]['bldg_filter_tag'].'">'.$GLOBALS['map_buildings'][$i]['name'].'</option>';
		}
	}
	echo '<option value="f_unsy">Unerforschte Systeme</option>';
	echo'
	  	</select>		
		
		  <select name="vsf0b" id="vsf0b" onChange="vs_filter(1);">
			<option value="gg">Stufe gr&ouml;&szlig;er gleich</option>
			<option value="kg">Stufe kleiner gleich</option>
			<option value="g">Stufe gleich</option>
		  </select>

		  <select name="vsf0c" id="vsf0c" onChange="vs_filter(1);">
			<option value="0">0</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
		  	<option value="8">8</option>
		  	<option value="9">9</option>
			<option value="10">10</option>
		  </select>		  

		  <img src="g/close_icon.png" style="height: 26px; width: auto; margin-left: 30px; margin-bottom: -7px;" onclick="vs_filter(0);" title="Filter zur&uuml;cksetzen">
		  <script>
		  $(document).ready(function() {
			vs_filter_init();
		  });		  
		  </script>
		</div>
	</div>


	<div style="border-bottom: 1px solid #999999; margin-bottom: 20px; margin-top: 13px;"></div>';

	$col_stolen=getStolenColByUID($_SESSION['ums_user_id']);
	$prozentwert=$col_stolen*5;
	if($prozentwert>500){
		$prozentwert=500;
	}

	echo '<div>Rohstoffbonus durch eroberte Kollektoren (pro Kollektor 5%, max 500% insgesamt): '.number_format($prozentwert, 2, ',' ,'.').'%</div>


	<div style="border-bottom: 1px solid #999999; margin-bottom: 20px; margin-top: 20px;"></div>
	';
	

	
	echo '<table width="572" border="0" cellpadding="0" cellspacing="1">';
	echo '<tr class="cell"><td>System</td><td style="text-align: center;">Aktion</td></tr>';


	
	///////////////////////////////////////////////////////////////////////////
	// erforschbare/erforschte Systeme für Handel/Missionen/Events
	///////////////////////////////////////////////////////////////////////////

	$sichtbare_systeme=array();
	$immer_sichtbare_systeme=array();
	$erforschte_systeme=array();
	$erforschte_systeme_koordinaten=array();
	
	//Kanten laden
	$kanten=array();
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_kanten");
	while($row = mysqli_fetch_array($db_daten)){
		$kanten[]=array($row['knoten_id1'],$row['knoten_id2']);
	}
	
	//die erforschten Systeme laden
	$sql="SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>0 AND known_since<'".time()."';";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		//sie sind sichtbar und erforscht
		$sichtbare_systeme[]=$row['map_id'];
		$erforschte_systeme[]=$row['map_id'];
	}
	
	//die sichtbaren Systeme um Systeme ergänzen, die immer sichtbar sind
	$sql="SELECT id FROM de_map_objects WHERE always_visible=1 OR system_typ=4;";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		if(!in_array($row['id'],$sichtbare_systeme)){
			$sichtbare_systeme[]=$row['id'];
		}
		$immer_sichtbare_systeme[]=$row['id'];
	}

	//print_r($immer_sichtbare_systeme);
	
	//die sichtbaren Systeme um die Systeme ergänzen, die über Kanten mit erforschten Systemen verknüpft sind
	for($i=0;$i<count($erforschte_systeme);$i++){
		//für jedes System alle Kanten durchgehen
		$map_id=$erforschte_systeme[$i];
		//echo 'map_id: '.$map_id;
		for($k=0; $k<count($kanten);$k++){
			//echo ' kanten_ids: '.$kanten[$k][0].'/'.$kanten[$k][1];
			//knoten1 testen
			if($map_id==$kanten[$k][0]){
				//echo 'gefunden 1';
				if(!in_array($kanten[$k][1],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][1];
					//echo 'gefunden 1a';
				}
			}
	
			//knoten2 testen
			if($map_id==$kanten[$k][1]){
				//echo 'gefunden 2';
				if(!in_array($kanten[$k][0],$sichtbare_systeme)){
					$sichtbare_systeme[]=$kanten[$k][0];
					//echo 'gefunden 2a';
				}
			}		
		}
	}
	
	//Kanten Koordinaten bestimmen, nur erforschte Systeme haben Kanten
	$kanten_koordinaten=array();
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	// allge Gebäude der Karte laden und in ein Array packen
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$bldg=array();
	$sql="SELECT * FROM de_user_map_bldg WHERE user_id='".$_SESSION['ums_user_id']."';";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	while($row = mysqli_fetch_array($db_daten)){
		$bldg[$row['map_id']][$row['field_id']]['bldg_id']=$row['bldg_id'];
		$bldg[$row['map_id']][$row['field_id']]['bldg_level']=$row['bldg_level'];
		$bldg[$row['map_id']][$row['field_id']]['bldg_time']=$row['bldg_time'];
	}

	//print_r($bldg);
	
	//Systeme laden
	//$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects LEFT JOIN de_user_map ON(de_map_objects.id=de_user_map.map_id);");
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects");

	//////////////////////////////////////////////////////////////////////////////////////////////////////
	//Position auf der Karte berechnen
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	$alien_nr=0;
	$kradius=500;
	$planet_id=1;
	while($row = mysqli_fetch_array($db_daten)){
		//klasse restaurieren
		$data=unserialize($row['data']);
		
		$output='';

		/*
		if(in_array($row['id'], $erforschte_systeme)){
			$bg_image=$ums_gpfad.'s/p'.$planet_id.'.png';
			$system_name=$data->getSystemName().' (#'.$row['id'].')';
			
			$planet_id++;
			if($planet_id>20){
				$planet_id=1;
			}
			//$erforschte_systeme_koordinaten[$row['id']]=array($alienpos_x, $alienpos_y);
		}else{
			$bg_image=$ums_gpfad.'g/derassenlogo0.png';
			$system_name='Unerforschtes System (#'.$row['id'].')';
		}*/
		$filter_class_unsy='';
		if(in_array($row['id'], $erforschte_systeme) || in_array($row['id'],$immer_sichtbare_systeme)){
			//$system_name=$data->getSystemName().' (#'.$row['id'].') - Stufe '.$data->getSystemLevel();
			$system_name='#'.$row['id'].' - '.$data->getSystemName();

			if(in_array($row['id'],$immer_sichtbare_systeme)){
				$bg_image=$ums_gpfad.'s/sym3.png';
			}else{
				$bg_image=$ums_gpfad.'s/p'.$planet_id.'.png';

			}
		}else{
			$bg_image=$ums_gpfad.'g/derassenlogo0.png';
			//$system_name='Unerforschtes System (#'.$row['id'].') - Stufe '.$data->getSystemLevel();
			$system_name='Unerforschtes System (#'.$row['id'].')';
			$filter_class_unsy =' f_unsy';
		}

		//Info zur System-Stufe
		$stufeninfo='';
		/*
		if(!in_array($row['id'],$immer_sichtbare_systeme)){
			if($bldg[$row['id']][0]['bldg_level']>0){
				$stufeninfo=' [Stufe: '.$bldg[$row['id']][0]['bldg_level'].']';

				//testen ob es gerade im Bau ist, dann die Farbe ändern
				if($bldg[$row['id']][0]['bldg_time']>time()){
					$stufeninfo='<span style="color: yellow;">'.$stufeninfo.'</span>';
				}
			}
		}
		*/

		//die Filterklassen zusammenbauen
		$filter_class=' f_system'.$filter_class_unsy;
		if($data->special_system < 1 && in_array($row['id'], $erforschte_systeme) && !in_array($row['id'],$immer_sichtbare_systeme)){
			for($i=0;$i<count($data->fields);$i++){
				if(!in_array($row['id'],$immer_sichtbare_systeme)){
					//$filter_class.=' f_lvl_'.$bldg[$row['id']][$i]['bldg_level'];
					$stufe=$bldg[$row['id']][$i]['bldg_level'] ?? 0;
								
					if($i>0){
						if($GLOBALS['map_field_typ'][$data->fields[$i][0]]['name']!='-'){
							//bei den Rohstoffen gibt es evtl. kein Gebäude, dann wird trotzdem das Feld mit Stufe 0 angezeigt
							if($stufe>0){
								//Gebäude
								//$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'];
								$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
							}else{
								//Rohstoffe ohne Gebäude
								
								//$filter_class.=' '.$GLOBALS['map_field_typ'][$data->fields[$i][0]]['filter_tag'];
								$filter_class.=' '.$GLOBALS['map_field_typ'][$data->fields[$i][0]]['filter_tag'].'_0';								
							}

						}else{
							//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
							if(isset($bldg[$row['id']][$i]['bldg_id']) && isset($GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id']) && $stufe > 0){
								//$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'];
								$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
							}else{
								//$output.='<div title="keine Rohstoffe" style="margin-left: 10px; line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; border-radius: 5px;'.$border.'">-</div>';
							}

						}
					}else{
						//Außenposten
						if(isset($bldg[$row['id']][$i]['bldg_level']) && $bldg[$row['id']][$i]['bldg_level'] > 0){
							//$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'];
							$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
						}else{
							$filter_class.=' f_plau_0';
						}
					}
				}
			}
		}

		$output='
		<tr class="cell'.$filter_class.'" style="height: 30px;"><td><img id="sysid'.$row['id'].'" src="'.$bg_image.'" style="vertical-align:middle; width: 16px; height: auto;"> '.$system_name.'</td>
			<td style="text-align: center;"><a href="map_system.php?id='.$row['id'].'">zum System</a></td>
		</tr>';
		
		$output.='
		<tr class="cell'.$filter_class.'">
			<td colspan="2">';
		
		///////////////////////////////////////////
		//Felder durchgehen und anzeigen
		///////////////////////////////////////////
		$output.='<div style="display: flex;">';
		if($data->special_system<1 && in_array($row['id'], $erforschte_systeme) && !in_array($row['id'],$immer_sichtbare_systeme)){
			for($i=0;$i<count($data->fields);$i++){

				///////////////////////////////////////////
				//Rahmenfarbe definieren
				///////////////////////////////////////////
				//Blocker
				if(isset($data->fields[$i][1])){
					$border='border: 1px solid #FF0000;';
				}else{
					$border='';
				}

				///////////////////////////////////////////
				//Feld anzeigen
				///////////////////////////////////////////
				//$output.='<div style="height: 50px; width: 300px; border: 1px solid '.$bordercolor.'; margin-bottom: 10px; box-sizing: border-box; padding: 5px; cursor: pointer;" onclick="location.href=\'map_system.php?id='.$data->system_id.'&fieldid='.$i.'\'">';
				///////////////////////////////////////////
				//Blocker anzeigen
				///////////////////////////////////////////
				/*
				if(isset($data->fields[$i][1])){
					$output.='Feldblocker: '.$data->fields[$i][1][1].'x '.$GLOBALS['map_field_blocker'][$data->fields[$i][1][0]]['name'].'<br>';
				}*/

				///////////////////////////////////////////
				//Gebäude anzeigen
				///////////////////////////////////////////
				/*
				for($b=0;$b<count($data->playerBldg);$b++){
					if($data->playerBldg[$b]['field_id']==$i){
						//wird das Gebäude gerade ausgebaut?
						if(time()<$data->playerBldg[$b]['bldg_time']){
							//Ausbau läuft
							$output.=$GLOBALS['map_buildings'][$data->playerBldg[$b]['bldg_id']]['name'].' (Ausbau auf Stufe '.($data->playerBldg[$b]['bldg_level']).': <span id="build_counter'.$i.'"></span>)';
							$output.='<script type="text/javascript">ang_countdown('.($data->playerBldg[$b]['bldg_time']-time()).',"build_counter'.$i.'",0)</script>';
							$output.='<br>';
						}else{
							//wird nicht ausgebaut
							$output.=$GLOBALS['map_buildings'][$data->playerBldg[$b]['bldg_id']]['name'].' (Stufe '.$data->playerBldg[$b]['bldg_level'].')<br>';
						}
					}
				}*/
				

				///////////////////////////////////////////
				//Feld-Ressource anzeigen
				///////////////////////////////////////////
				$stufeninfo='';
				if(!in_array($row['id'], $immer_sichtbare_systeme)){
					if(isset($bldg[$row['id']]) && isset($bldg[$row['id']][$i]['bldg_level'])){
						$stufeninfo='<br>'.$bldg[$row['id']][$i]['bldg_level'];
					}else{
						$stufeninfo='<br>0';
					}
					
					//testen ob es gerade im Bau ist, dann die Farbe ändern
					if(isset($bldg[$row['id']]) && isset($bldg[$row['id']][$i]['bldg_time']) && $bldg[$row['id']][$i]['bldg_time'] > time()){
						$stufeninfo='<span style="color: yellow;">'.$stufeninfo.'</span>';
					}
				}

				if($i>0){
					if($GLOBALS['map_field_typ'][$data->fields[$i][0]]['name']!='-'){
						//Gebäudestufe bestimmen
						//Grafik bestimmen
						$filename_nr=$data->fields[$i][0];
						if($filename_nr<10){
							$filename_nr='0'.$filename_nr;
						}
						$output.='<div style="text-align:center; padding-left: 10px; font-weight: bold; font-size: 20px;"><img style="width: 40px; border-radius: 5px;'.$border.'" src="'.$ums_gpfad.'g/ele'.$filename_nr.'.gif" title="'.$GLOBALS['map_field_typ'][$data->fields[$i][0]]['name'].'">'.$stufeninfo.'</div>';
					}else{
						//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
						if(isset($bldg[$row['id']][$i]['bldg_id']) && isset($GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id'])){

							//$GLOBALS['greek_chars']
							/*
							$output.='
							<div style="text-align:center; margin-right: 1px; font-size: 10px; line-height: 10px;">
								<img style="width: 18px; height: 18px; box-sizing: border-box; border-radius: 5px;'.$border.'" src="'.$ums_gpfad.'g/r/'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id'].'_g.gif" title="'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['name'].'">
								'.$stufeninfo.'
							</div>';
							*/

							$output.='
							<div style="font-size: 20px; line-height: 10px; text-align:center; margin-left: 10px;">
								<div style="line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;" title="'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['name'].'">'.$GLOBALS['greek_chars'][$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id']].'</div>
								'.$stufeninfo.'
							</div>';


						}else{
							$output.='<div title="keine Rohstoffe" style="margin-left: 10px; line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; border-radius: 5px;'.$border.'">-</div>';
						}

					}
				}else{

					//Außenposten
					$output.='
					<div style="font-size: 20px; line-height: 10px; text-align:center;">
						<div style="line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;" title="Au&szlig;enposten">A</div>
						'.$stufeninfo.'
					</div>';


				}


				//$output.='</div>';

				//Test auf Loot

			}
		}
		
		$output.='</div>';

		$output.='
			</td>
		</tr>
		';
	
		//if($row['user_id']<1 && $data->always_visible==0){
		if(!in_array($row['id'], $sichtbare_systeme)){
			$output='';
		}
	
		echo $output;
		
		$alien_nr++;
	}

	echo '</table>';
	rahmen_unten();  
	
}
?>

<br>
<?php include "fooban.php"; ?>
</body>
</html>
