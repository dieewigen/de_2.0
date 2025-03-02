<?php
$GLOBALS['deactivate_old_design']=true;

include "inc/header.inc.php";
include "lib/transactioni.lib.php";
include "functions.php";
include 'lib/map_system.class.php';
include "tickler/kt_einheitendaten.php";
include_once 'inc/userartefact.inc.php';

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$GLOBALS['pt']=$pt;
$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps']=$ps;
$pd=loadPlayerData($_SESSION['ums_user_id']);
$GLOBALS['pd']=$pd;
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$mysc2=$row["sc2"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$spec1=$row['spec1'];$spec3=$row['spec3'];

$vs_auto_explore=$row['vs_auto_explore'];

////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT id, level FROM de_user_artefact WHERE id=12 AND user_id='".$_SESSION['ums_user_id']."'");
$artbonus_duration=0;
while($row = mysqli_fetch_array($db_daten)){
	$artbonus_duration=$artbonus_duration+$ua_werte[$row["id"]-1][$row["level"]-1][0];
}

if($artbonus_duration>50){
	$artbonus_duration=50;
}

$GLOBALS['duration_factor']=1-($artbonus_duration/100);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Systeminformationen</title>
<meta charset="utf-8"/>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
<?php 
//$newcss=1;
include "cssinclude.php";
?>
</head>
<body>
<?php 

$GLOBALS['ally_fundbuero_level']=0;
if(!empty($pd['allytag']) && $pd['ally_status']==1){
	$GLOBALS['allyid']=getAllyIDByAllytag($pd['allytag']);

	$allybldg=get_allybldg($GLOBALS['allyid']);
	$GLOBALS['ally_fundbuero_level']=$allybldg[8];

	//echo 'AAAAAAAAAAAA'.$GLOBALS['ally_fundbuero_level'];
}


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
	//welche ID will man sich ansehen?
	$id=intval($_REQUEST['id']);
	if($id<1){
		$id=1;
	}

	//Daten über das System aden
	$sql="SELECT * FROM de_map_objects WHERE id='$id' LIMIT 1";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$num = mysqli_num_rows($db_daten);
	if($num==1){
		$system_daten = mysqli_fetch_array($db_daten);

		//Klasse restaurieren
		$data=unserialize($system_daten['data']);
		$data->system_id=$id;

		//forschungkosten
		$level=$data->getSystemLevel();
		$kosten_sonden=$level*$level*10;
		$kosten_zeit=$level*15*60*$GLOBALS['tech_build_time_faktor']*2;
	}
	
	//der Zugriff auf die Systeme hängt von der Spielerzahlen auf dem Server ab
	/*
	$sql="SELECT COUNT(*) AS anzahl FROM de_user_data WHERE sector>1 AND npc=0;";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$row = mysqli_fetch_array($db_daten);
	$anzahl_spieler=$row['anzahl'];
	*/
	//if($anzahl_spieler<($level-1)*100){
	if(1==2){
		/*
		$system_name=$data->getSystemName();
		if(empty($system_name)){
			$system_name='Unerforschtes System';
		}
		rahmen_oben($system_name.' (#'.$id.') - Stufe '.$level);
		echo '<div class="text2 cell" style="width:572px; text-align: center;">Auf Systeme der Stufe '.$level.' ist erst ab '.(($level-1)*100).' aktiven Spielern (aktuell '.$anzahl_spieler.') ein Zugriff m&ouml;glich.</div>';
		rahmen_unten();
		*/
		
	}else{
		//die vorhandenen Daten laden, die man davon hat
		$sql="SELECT * FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND map_id='$id' LIMIT 1";
		$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
		//$num = mysqli_num_rows($db_daten);
		$user_map_data = mysqli_fetch_array($db_daten);
		if(isset($user_map_data['known_since']) && $user_map_data['known_since'] > 0 && $user_map_data['known_since']<time()){//man hat schon Infos
			echo $data->showSystem($ps);
		}else{//man hat noch keine Infos, Sonden starten um diese zu bekommen
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////////
			//checken ob man eine Verbindung zu dem System hat um es zu erforschen, bzw. ob es immer sichtbar ist
			//////////////////////////////////////////////////////////////////////////////////////////////////////////
			$system_erreichbar=false;
			//immer sichtbar?
			if($system_daten['always_visible']==1){
				$system_erreichbar=true;
			}

			//ist ein erforschtes System per Kante erreichbar?
			//alle erforschten  auslesen
			$erforschte_systeme=array();
			$sql="SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>0 AND known_since<'".time()."';";
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			while($row = mysqli_fetch_array($db_daten)){
				//sie sind sichtbar und erforscht
				$erforschte_systeme[]=$row['map_id'];
			}			

			//alle Kanten für dieses System hier auslesen und schauen ob es zu einem erforschten System passt
			$sql="SELECT * FROM de_map_kanten WHERE knoten_id1='$id' OR knoten_id2='$id';";
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			while($row = mysqli_fetch_array($db_daten)){

				if(in_array($row['knoten_id1'],$erforschte_systeme) ||  in_array($row['knoten_id2'],$erforschte_systeme)){
					$system_erreichbar=true;
				}

			}


			if(!$system_erreichbar){
				rahmen_oben(generate_vsystem_kopfzeile($id, 'Nicht erreichbares System'));
				echo '<div style="width: 572px;">Keine Verbindung zum System vorhanden.</div>';
				rahmen_unten();
			}else{
				//////////////////////////////////////////////////////////////////////////////////////////////////////////
				//es gibt eine Verbindung zum System, also etwas anzeigen
				//////////////////////////////////////////////////////////////////////////////////////////////////////////

				rahmen_oben(generate_vsystem_kopfzeile($id, 'Unerforschtes System'));
				echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
				echo '
				<tr class="cell">
					<td style="widht: 100%; text-align: center;">';
				//checken ob bereits ein system erforscht wird
				$sql="SELECT * FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>'".time()."' LIMIT 1";
				$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
				$num = mysqli_num_rows($db_daten);

				//wird eine Erkundung gestartet?
				echo '
						&Uuml;ber dieses System liegen noch keine Informationen vor. Du kannst aber eine Erkundung starten und einen Hyperraumtunnel etablieren.
						<br>Ben&ouml;tigte Sonden: '.$kosten_sonden.' (vorhanden: '.number_format($pd['sonde'],0,",",".").')
						<br>Die Erforschung erfolgt im nächsten Wirtschaftstick.
					
						<br><span class="text2">ACHTUNG: W&auml;hrend der Hyperraumtunnel etabliert wird, geben Deine Kollektoren durch diese St&ouml;rung keine Energie ab.</span>';

						//<br>Ben&ouml;tigte Zeit: '.gmdate("H:i:s", $kosten_zeit).'

				if($num==1){//man erforscht schon etwas
					$row = mysqli_fetch_array($db_daten);
					//unterscheiden ob dieses System oder ein anderes System erforscht wird
					if($row['map_id']==$id){
						echo '<br><br>Dieses System wird aktuell erforscht.';
					}else{
						echo '<br><br>Es wird bereits ein <a href="map_system.php?id='.$row['map_id'].'">anderes System</a> erforscht.';
					}
					
					//Counter
					//echo '<br><br>Verbleibende Zeit: <span id="explore_counter"></span>';
					
					echo '<script type="text/javascript">ang_countdown('.($row['known_since']-time()).',"explore_counter",0)</script>';
					
					
				}else{
					$explore_starting=false;
					if(isset($_REQUEST['explore']) && $_REQUEST['explore']==1 && $vs_auto_explore==0){
						//checken ob man genug Sonden hat
						if($pd['sonde']>=$kosten_sonden){
							//Datensatz hinzufügen
							$sql="INSERT INTO de_user_map SET user_id='".$_SESSION['ums_user_id']."', map_id='".$id."', known_since='".(intval(time()+$kosten_zeit))."', specialsystem_data='';";
							mysqli_query($GLOBALS['dbi'],$sql);
							//Sonden abziehen
							$sql="UPDATE de_user_data SET sonde=sonde-'".$kosten_sonden."' WHERE user_id='".$_SESSION['ums_user_id']."';";
							mysqli_query($GLOBALS['dbi'],$sql);
							$explore_starting=true;
						}else{
							echo '<br><br><span class="text2">Es sind nicht genug Sonden vorhanden.</span>';
						}
						
					}
					
					if(!$explore_starting){
						if($vs_auto_explore==1){
							echo '<br><br>Die automatische Erkundung ist aktiv.';
						}else{
							echo '<br><br><div style="width: 100%; text-align: center;"><a style="display: inline-block;" class="btn" href="map_system.php?id='.$id.'&explore=1" style="">System erkunden</a></div>';
						}
					}else{
						echo '<br><br><div class="text3">Die Erforschung hat begonnen.</div>';
						//Counter
						//echo '<br>Verbleibende Zeit: <span id="explore_counter"></span>';
						
						if($_SESSION['ums_mobi']!=1){
							echo '<br><br>Tipp: Aktualisiere die Karte nach erfolgter Erforschung um mehr Informationen zu erhalten.';
						}

						//echo '<script type="text/javascript">ang_countdown('.($kosten_zeit).',"explore_counter",0)</script>';
						
					}
				}

				echo '
					</td>
				</tr>';
				echo '</table>';
				rahmen_unten();  
			}
		}	
	}

	echo '<script>vs_system_init();</script>';
}



?>
	
	
<br>
<?php include "fooban.php"; ?>
</body>
</html>