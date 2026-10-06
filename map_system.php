<?php
//Ausgabe puffern, damit nach Aktionen per vs_redirect() umgeleitet werden kann
ob_start();

$GLOBALS['deactivate_old_design']=true;

include "inc/header.inc.php";
include "lib/transaction.lib.php";
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

//Bauzeitverkürzung durch Artefakte
$GLOBALS['duration_factor']=vs_duration_factor($_SESSION['ums_user_id'], $ua_werte);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Systeminformationen</title>
<?php 
include "cssinclude.php";
?>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
</head>
<?php 
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$GLOBALS['ally_fundbuero_level']=0;
$GLOBALS['allyid']=-1;
if(!empty($pd['allytag']) && $pd['ally_status']==1){
	$GLOBALS['allyid']=getAllyIDByAllytag($pd['allytag']);

	$allybldg=get_allybldg($GLOBALS['allyid']);
	$GLOBALS['ally_fundbuero_level']=$allybldg[8];

	//echo 'AAAAAAAAAAAA'.$GLOBALS['ally_fundbuero_level'];
}

if(isset($sv_deactivate_vsystems) && $sv_deactivate_vsystems==1){
	include "resline.php";
	rahmen_oben('Vergessene Systeme');
	echo '<div class="mod ms"><div class="mod-leer">Auf diesem Server sind die Vergessenen Systeme deaktiviert.</div></div>';
	rahmen_unten();

	die('</body></html>');
}

echo '<div id="vs-resline">';
include "resline.php";
echo '</div>';

echo '<div id="vs-main">';

//transaktionsbeginn, verhindert doppelte Aktionen durch schnelles Klicken oder mehrere Tabs
$vs_locked=setLock($_SESSION['ums_user_id']);
if($vs_locked){
	//innerhalb der Sperre aktuelle Werte verwenden
	$ps=loadPlayerStorage($_SESSION['ums_user_id']);
	$GLOBALS['ps']=$ps;
	$pd=loadPlayerData($_SESSION['ums_user_id']);
	$GLOBALS['pd']=$pd;
}

if(!$vs_locked){
	echo '<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-warn">Es wird noch eine Aktion ausgef&uuml;hrt. Bitte <a href="map_system.php?id='.intval($_REQUEST['id'] ?? 1).'">kurz warten und neu laden</a>.</div></div>';
}elseif(!hasTech($pt,25)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=25";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);

	rahmen_oben('Fehlende Technologie');
	echo '<div class="mod ms"><div class="mod-hinweis vs-fehlt">F&uuml;r die Vergessenen Systeme ben&ouml;tigst Du folgende Technologie: <a href="help.php?t=25">'.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</a></div></div>';
	rahmen_unten();
}else{
	//welche ID will man sich ansehen?
	$id=intval($_REQUEST['id'] ?? 1);
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

	//die vorhandenen Daten laden, die man davon hat
	$sql="SELECT * FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND map_id='$id' LIMIT 1";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$user_map_data = mysqli_fetch_array($db_daten);
	if(isset($user_map_data['known_since']) && $user_map_data['known_since'] > 0 && $user_map_data['known_since']<time()){//man hat schon Infos
		echo $data->showSystem($ps);
	}else{//man hat noch keine Infos, Sonden starten um diese zu bekommen

		//////////////////////////////////////////////////////////////////////////////////////////////////////////
		//checken ob man eine Verbindung zu dem System hat um es zu erforschen, bzw. ob es immer sichtbar ist
		//////////////////////////////////////////////////////////////////////////////////////////////////////////
		$system_erreichbar=false;
		//immer sichtbar?
		if(isset($system_daten['always_visible']) && $system_daten['always_visible']==1){
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
			rahmen_oben('Nicht erreichbares System <span class="ms-nummer">#'.$id.'</span>');
			echo '<div class="mod ms">'.generate_vsystem_kopfzeile($id, 'Nicht erreichbares System');
			echo '<div class="mod-leer">Keine Verbindung zum System vorhanden.</div></div>';
			rahmen_unten();
		}else{
			//////////////////////////////////////////////////////////////////////////////////////////////////////////
			//es gibt eine Verbindung zum System, also etwas anzeigen
			//////////////////////////////////////////////////////////////////////////////////////////////////////////

			rahmen_oben('Unerforschtes System <span class="ms-nummer">#'.$id.'</span>');
			echo '<div class="mod ms">'.generate_vsystem_kopfzeile($id, 'Unerforschtes System');
			//checken ob bereits ein system erforscht wird
			$sql="SELECT * FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>'".time()."' LIMIT 1";
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			$num = mysqli_num_rows($db_daten);

			//wird eine Erkundung gestartet?
			echo '<div class="ms-erkundung">';
			echo '<div class="ms-zeile">&Uuml;ber dieses System liegen noch keine Informationen vor. Du kannst aber eine Erkundung starten und einen Hyperraumtunnel etablieren.</div>';
			echo '<div class="ms-kacheln">';
			echo '<div class="ov-wert"><span class="mod-typ">Ben&ouml;tigte Sonden</span><b'.($pd['sonde']<$kosten_sonden ? ' class="ms-fehlt"' : '').'>'.number_format($kosten_sonden,0,",",".").'</b><small>vorhanden: '.number_format($pd['sonde'],0,",",".").'</small></div>';
			echo '<div class="ov-wert"><span class="mod-typ">Erforschung</span><b>im n&auml;chsten WT</b></div>';
			echo '</div>';
			echo '<div class="mod-meldung mod-meldung-warn ms-warnung">ACHTUNG: W&auml;hrend der Hyperraumtunnel etabliert wird, geben Deine Kollektoren durch diese St&ouml;rung keine Energie ab.</div>';

			if($num==1){//man erforscht schon etwas
				$row = mysqli_fetch_array($db_daten);
				//unterscheiden ob dieses System oder ein anderes System erforscht wird
				if($row['map_id']==$id){
					echo '<div class="mod-meldung mod-meldung-ok">Dieses System wird aktuell erforscht.</div>';
				}else{
					echo '<div class="mod-meldung mod-meldung-warn">Es wird bereits ein <a href="map_system.php?id='.$row['map_id'].'">anderes System</a> erforscht.</div>';
				}
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
						echo '<div class="mod-meldung mod-meldung-fehler">Es sind nicht genug Sonden vorhanden.</div>';
					}

				}

				if(!$explore_starting){
					if($vs_auto_explore==1){
						echo '<div class="mod-hinweis ms-hinweis">Die automatische Erkundung ist aktiv.</div>';
					}else{
						echo '<div class="ms-aktion"><a class="mod-btn" href="map_system.php?id='.$id.'&amp;explore=1">System erkunden</a></div>';
					}
				}else{
					echo '<div class="mod-meldung mod-meldung-ok">Die Erforschung hat begonnen.'.($_SESSION['ums_mobi']!=1 ? ' Aktualisiere die Karte nach erfolgter Erforschung, um mehr Informationen zu erhalten.' : '').'</div>';
				}
			}

			echo '</div></div>';
			rahmen_unten();
		}
	}
}

echo '</div>';//vs-main

if($vs_locked){
	releaseLock($_SESSION['ums_user_id']);
}

echo '<script>vs_system_init(); vs_ajax_init();</script>';
?>
</body>
</html>
