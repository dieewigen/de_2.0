<?php

use DieEwigen\DE2\View\RealTime;

include('inc/header.inc.php');
include('lib/transaction.lib.php');
include('inc/schiffsdaten.inc.php');
include('functions.php');
include('tickler/kt_einheitendaten.php');
include('inc/lang/'.$sv_server_lang.'_military.lang.php');
$military_lang['klassennamen']=array('J&auml;ger','Jagdboot','Zerst&ouml;rer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Tr&auml;ger','Frachter','Titan',
'Orbitalj&auml;ger-Basis','Flugk&ouml;rper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');

// Sicherstellen, dass $db vorhanden ist (einige Funktionen erwarten es), ansonsten aus globaler DB-Verbindung ableiten
if(!isset($db) && isset($GLOBALS['dbi'])){
	$db = $GLOBALS['dbi'];
}

if(isset($_GET['se'])){
	$se = $_GET['se'];
}

if(isset($_GET['sy'])){
	$sy = $_GET['sy'];
}

//Check ob die Missionen zu Ende sind
checkMissionEnd();

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row['score'];$newtrans=$row['newtrans'];$techs=$row['techs'];$newnews=$row['newnews'];
$sector=$row['sector'];$system=$row['system'];$erang_nr=$row['rang'];$col=$row['col'];
$spec3=$row['spec3'];$spec5=$row['spec5'];

$ownsector=$sector;
$ownsystem=$system;

$schiffsdaten=$sv_schiffsdaten;

$errmsg='';

if ($row['status']==1) $ownally = $row['allytag'];

$rangnamen=array($military_lang['dererhabene'], "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");

//rangwerte-liste erstellen
$ranginfo='Ben&ouml;tigte Erfahrungspunkte f&uuml;r verbesserte Formationen&';
//24 Formationen von Omega bis Alpha, wie in getfleetlevel()
for($i=0;$i<24;$i++)
{
  $counter=($i*($i-1)*30000)+30000;
  if($i==0)$counter=0;
  $ranginfo.=$rangnamen[24-$i].': '.number_format($counter, 0,",",".").'<br>';
}

//Meldungen über der Seite; Flottenbefehle nennen die Flotte, damit klar ist, welche gemeint ist
function mil_meldung($text, $art, $fleet_id=''){
	global $military_lang;
	$vorsatz='';
	if($fleet_id!=''){
		$nr=explode('-', $fleet_id);
		$vorsatz='<b>'.$military_lang['flotte'.($nr[1] ?? '')].':</b> ';
	}
	return '<div class="mod-meldung mod-meldung-'.$art.'">'.$vorsatz.$text.'</div>';
}

//sichtbarkeit des flottenziels
$showfleettarget=array(1,1,1);//standardmäßig sichtbar
if (isset($_POST['befehle'])){
	if(!isset($_POST['showfleet1'])) $showfleettarget[0]=0;
	if(!isset($_POST['showfleet2'])) $showfleettarget[1]=0;
	if(!isset($_POST['showfleet3'])) $showfleettarget[2]=0;
}

//spezialisierung trägerkapazität
if($spec3==2){
	for($i=0;$i<count($schiffsdaten[$_SESSION['ums_rasse']]);$i++){
		$schiffsdaten[$_SESSION['ums_rasse']-1][$i][1]= floor($schiffsdaten[$_SESSION['ums_rasse']-1][$i][1] * 1.2);
	}
}

if (isset($_POST['befehle'])){
	// Eingangsparameter defensiv lesen, um Undefined-Index-Warnings zu vermeiden
	$zsecf1=isset($_POST['zsecf1']) ? (int)$_POST['zsecf1'] : 0;
	$zsysf1=isset($_POST['zsysf1']) ? (int)$_POST['zsysf1'] : 0;
	$zsecf2=isset($_POST['zsecf2']) ? (int)$_POST['zsecf2'] : 0;
	$zsysf2=isset($_POST['zsysf2']) ? (int)$_POST['zsysf2'] : 0;
	$zsecf3=isset($_POST['zsecf3']) ? (int)$_POST['zsecf3'] : 0;
	$zsysf3=isset($_POST['zsysf3']) ? (int)$_POST['zsysf3'] : 0;

  //sektor 1 unangreifbar machen und man kann auch nicht aus sektor 1 angegriffen werden
  if($zsecf1==1 || $sector==1)$zsecf1='-1';
  if($zsecf2==1 || $sector==1)$zsecf2='-1';
  if($zsecf3==1 || $sector==1)$zsecf3='-1';
  /*
    Aktionen
    0: Verteidigung des Heimatsystems
    1: Angriff auf ein System
    2: Verteidigung eines anderen Systems
    3: Rückflug ins Heimatsystem
    4: Questhinflug
  */
  //fuer jede flotte eigene sektion
  //flotte 1
	$af1=isset($_POST['af1']) ? intval($_POST['af1']) : 0; // 0 = keine neuen Befehle
  switch($af1){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($_SESSION['ums_user_id'].'-1', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($_SESSION['ums_user_id'].'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($_SESSION['ums_user_id'].'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
    case 4: //verteidigen
      attdef($_SESSION['ums_user_id'].'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
    case 5: //verteidigen
      attdef($_SESSION['ums_user_id'].'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
  }//switch af1 ende
  //flotte 2
	$af2=isset($_POST['af2']) ? intval($_POST['af2']) : 0;
  switch($af2){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($_SESSION['ums_user_id'].'-2', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($_SESSION['ums_user_id'].'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($_SESSION['ums_user_id'].'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
    case 4: //verteidigen
      attdef($_SESSION['ums_user_id'].'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
    case 5: //verteidigen
      attdef($_SESSION['ums_user_id'].'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
	}//switch af2 ende
  //flotte 3
	$af3=isset($_POST['af3']) ? intval($_POST['af3']) : 0;
  switch($af3){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($_SESSION['ums_user_id'].'-3', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($_SESSION['ums_user_id'].'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($_SESSION['ums_user_id'].'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
    case 4: //verteidigen
      attdef($_SESSION['ums_user_id'].'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
    case 5: //verteidigen
      attdef($_SESSION['ums_user_id'].'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
	}//switch af3 ende
}

//schauen ob er die whg hat und dann die attgrenze anpassen
if ($techs[4]==0)$sv_attgrenze_whg_bonus=0;

if (isset($_POST['verlegen'])){
	//transaktionsbeginn
	if (setLock($_SESSION['ums_user_id'])){
		//zuerst alle übergebenen felder in nen array packen
		$c=0;
		for ($i=81; $i<=80+$sv_anz_schiffe; $i++){
			str_replace(".","", $_POST['m".$i."_1'] ?? 0);

			if(($_POST['m'.$i.'_1'] ?? 0)>0)$sa[$c][0]=(int)str_replace(".","",$_POST['m'.$i.'_1'] ?? 0);else $sa[$c][0]=0;
			if(($_POST['m'.$i.'_2'] ?? 0)>0)$sa[$c][1]=(int)str_replace(".","",$_POST['m'.$i.'_2'] ?? 0);else $sa[$c][1]=0;
			if(($_POST['m'.$i.'_3'] ?? 0)>0)$sa[$c][2]=(int)str_replace(".","",$_POST['m'.$i.'_3'] ?? 0);else $sa[$c][2]=0;
			$c++;
		}
		//flottendaten laden
		$fid0=$_SESSION['ums_user_id'].'-0';$fid1=$_SESSION['ums_user_id'].'-1';$fid2=$_SESSION['ums_user_id'].'-2';$fid3=$_SESSION['ums_user_id'].'-3';
		$einheiten_result=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
		$einheiten_daten=array();
		while($row = mysqli_fetch_array($einheiten_result)){ //jeder gefundene datensatz wird geprueft
			$einheiten_daten[]=$row;
		}

		//jeden schiffstyp einzeln durchgehen
		$showerror=array(0,0);
		for ($i=81; $i<=80+$sv_anz_schiffe; $i++){
			$fleet[0]=$einheiten_daten[0]['e'.$i];//anzahl der einheiten auslesen
			$fleet[1]=$einheiten_daten[1]['e'.$i];//anzahl der einheiten auslesen
			$fleet[2]=$einheiten_daten[2]['e'.$i];//anzahl der einheiten auslesen
			$fleet[3]=$einheiten_daten[3]['e'.$i];//anzahl der einheiten auslesen

			$fleet_a[0]=$einheiten_daten[0]['aktion'];
			$fleet_a[1]=$einheiten_daten[1]['aktion'];
			$fleet_a[2]=$einheiten_daten[2]['aktion'];
			$fleet_a[3]=$einheiten_daten[3]['aktion'];

			$gesamt=$fleet[0]+$fleet[1]+$fleet[2]+$fleet[3];

			//des weiteren dürfen keine flotten die unterwegs sind geändert werden, deshalb zuerst dieses überprüfen
			for($j=1;$j<=3;$j++)
			{
			  //if($sa[$i-81][$j-1]!=$fleet[$j] AND $fleet_a[$j]!=0)$error=2;
			  //if($error==2) echo 'A: '.$sa[$i-81][$j-1].' - '.$fleet[$j].'<br>';
			  if($fleet_a[$j]!=0)$sa[$i-81][$j-1]=$fleet[$j];
			}

			//schauen ob die zahlen soweit ok sind
			$error=0;
			if(($sa[$i-81][0]+$sa[$i-81][1]+$sa[$i-81][2])<=$gesamt){
			  //man darf keine absoluten zahlen setzen, da es ansonsten mit dem bautick zu problemen kommen könnte
			  //wenn das ok ist weiter und die flottenzahlen anpassen
			  if($error==0){
					//schiffsanzahl in der heimatflotte berechnen
					$saheim=$gesamt-$sa[$i-81][0]-$sa[$i-81][1]-$sa[$i-81][2];
					//werte für den sql-befehl berechnen
					$fw0=$saheim-$fleet[0];
					$fw1=$sa[$i-81][0]-$fleet[1];
					$fw2=$sa[$i-81][1]-$fleet[2];
					$fw3=$sa[$i-81][2]-$fleet[3];
					//schiffsart updaten, wenn die flotte auch daheim ist
					$sql ="UPDATE de_user_fleet SET e$i = e$i + '$fw0' WHERE user_id = '".$_SESSION['ums_user_id']."-0';";
					mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw1' WHERE user_id = '".$_SESSION['ums_user_id']."-1';";
					if($fleet_a[1]==0)mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw2' WHERE user_id = '".$_SESSION['ums_user_id']."-2';";
					if($fleet_a[2]==0)mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw3' WHERE user_id = '".$_SESSION['ums_user_id']."-3';";
					if($fleet_a[3]==0)mysqli_query($GLOBALS['dbi'],$sql);
				}
			}
			else $error=1;
			//echo 'Fehler: '.$error.'<br>';
			//schauen welche fehlermeldungen man ausgeben muß
			if($error==1)$showerror[0]=1;
			if($error==2)$showerror[1]=1;
		}

		//fehlermessage erstellen
		if($showerror[0]==1)
		$errmsg.=mil_meldung($military_lang['allgfehler'], 'fehler');
		if($showerror[1]==1)
		$errmsg.=mil_meldung($military_lang['allgfehler2'], 'fehler');
		//wenn kein fehler aufgetrten ist, ok info ausgeben
		if($showerror[0]==0 AND $showerror[1]==0)
		$errmsg.=mil_meldung($military_lang['fleetumgestellt'], 'ok');

		$erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
		if ($erg){
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
			$errmsg.=mil_meldung($military_lang['releaselock'].$_SESSION['ums_user_id'].$military_lang['releaselock2'], 'fehler');
		}
	}// if setlock-ende
	else $errmsg.=mil_meldung($military_lang['setlock'], 'fehler');
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Flotten</title>
<?php include "cssinclude.php"; ?>
<?php

if(isset($_REQUEST['zsecf1save']))$zsecf1=intval($_REQUEST['zsecf1save']);
if(isset($_REQUEST['zsecf2save']))$zsecf2=intval($_REQUEST['zsecf2save']);
if(isset($_REQUEST['zsecf3save']))$zsecf3=intval($_REQUEST['zsecf3save']);
if(isset($_REQUEST['zsysf1save']))$zsysf1=intval($_REQUEST['zsysf1save']);
if(isset($_REQUEST['zsysf2save']))$zsysf2=intval($_REQUEST['zsysf2save']);
if(isset($_REQUEST['zsysf3save']))$zsysf3=intval($_REQUEST['zsysf3save']);

echo '<script language="javascript">';
//sichern der koordinaten
echo '
function savekoord(){
	$("[name=zsecf1save]").val($("[name=zsecf1]").val());
	$("[name=zsecf2save]").val($("[name=zsecf2]").val());
	$("[name=zsecf3save]").val($("[name=zsecf3]").val());

	$("[name=zsysf1save]").val($("[name=zsysf1]").val());
	$("[name=zsysf2save]").val($("[name=zsysf2]").val());
	$("[name=zsysf3save]").val($("[name=zsysf3]").val());

	return true;
}';


//flotten umstellen
echo 'var aktf = new Array();';
echo 'var gesamtf = new Array();';
echo 'var reisez = new Array();';
echo 'var fleetna = new Array();';
echo 'var shipscore = new Array();';
//alle werte auslesen, die man für die seite benötigt
//lade die anzahl der einheiten
$fid0=$_SESSION['ums_user_id'].'-0';$fid1=$_SESSION['ums_user_id'].'-1';$fid2=$_SESSION['ums_user_id'].'-2';$fid3=$_SESSION['ums_user_id'].'-3';
$einheiten_result=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
$einheiten_daten=array();
while($row = mysqli_fetch_array($einheiten_result)){ //jeder gefundene datensatz wird geprueft
	$einheiten_daten[]=$row;
}
//lade einheitentypen
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_tech_data WHERE tech_id>80 AND tech_id<100 ORDER BY tech_id");
$i=81;
$ez1=Array(0,0,0,0,0,0,0,0,0,0,0,0);
$ez2=Array(0,0,0,0,0,0,0,0,0,0,0,0);
$ez3=Array(0,0,0,0,0,0,0,0,0,0,0,0);
$c1=0;
$tid[0]='-1';$tid[1]='-1';$tid[2]='-1';$tid[3]='-1';
$bentid[0]='-1';$bentid[1]='-1';
while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
    //schiffspunkte
    echo 'shipscore['.$c1.'] = '.$unit[$_SESSION['ums_rasse']-1][$row['tech_id']-81][4].';';

	//////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////
	//js-tooltip-daten generieren
	//klasse
	$zstr='<font color=#D265FF>'.$military_lang['klasse'].': '.$military_lang['klassennamen'][$i-81].'</font>';
	//punkte
	$zstr.='<br><font color=#FFFA65>'.$military_lang['punkte'].': '.number_format($unit[$_SESSION['ums_rasse']-1][$i-81][4], 0,"",".").'</font>';

	//reiszeit
	$zstr.='<br><br>'.$military_lang['reisezeit'].': '.$schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][0].' KT';
	//transportkapazität
	if ($schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][1]>0)$zstr.='<br>'.$military_lang['kapazitaet'].': '.$schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][1];
	//ben. transportkapazität
	if ($schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][2]>0)$zstr.='<br>'.$military_lang['kapazitaet2'].': '.$schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][2];

	//frachtkapazität
	if (isset($unit[$_SESSION['ums_rasse']-1][$i-81]['fk']) && $unit[$_SESSION['ums_rasse']-1][$i-81]['fk'] > 0)$zstr.='<br>Frachtkapazit&auml;t: '.$unit[$_SESSION['ums_rasse']-1][$i-81]['fk'];

	//waffenarten
	//konventionell
	if($unit[$_SESSION['ums_rasse']-1][$i-81][2]>0)$wv='<font color=#2DFF11>'.$military_lang['waffenvorhandenja'].'</font>';
		else $wv='<font color=#ED0909>'.$military_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#9D4B15>'.$military_lang['waffengattung1'].':</font> '.$wv;

	//klassenziel
	if($unit[$_SESSION['ums_rasse']-1][$i-81][2]>0){
		$zstr.='<br><font color=#ED9409>-'.$military_lang['klasseziel1'].': '.$military_lang['klassennamen'][$kampfmatrix[$i-81][0]].'</font>';
		$zstr.='<br><font color=#F0BA66>-'.$military_lang['klasseziel2'].': '.$military_lang['klassennamen'][$kampfmatrix[$i-81][2]].'</font>';
	}

	//emp
	if($unit[$_SESSION['ums_rasse']-1][$i-81][3]>0)$wv='<font color=#2DFF11>'.$military_lang['waffenvorhandenja'].'</font>';
		else $wv='<font color=#ED0909>'.$military_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#15629D>'.$military_lang['waffengattung2'].':</font> '.$wv;

	//klassenziel
	if($unit[$_SESSION['ums_rasse']-1][$i-81][3]>0){
		$zstr.='<br><font color=#ED9409>-'.$military_lang['klasseziel1'].': '.$military_lang['klassennamen'][$blockmatrix[$i-81][0]].'</font>';
		$zstr.='<br><font color=#F0BA66>-'.$military_lang['klasseziel2'].': '.$military_lang['klassennamen'][$blockmatrix[$i-81][2]].'</font>';
	}

	//besonderheiten
	if($i-81==1)$zstr.='<br><br><font color=#2DFF11>'.$military_lang['besonderheitjagdboot'].'</font>';
	if($i-81==3)$zstr.='<br><br><font color=#2DFF11>'.$military_lang['besonderheitkreuzer'].'</font>';
	if($i-81==4)$zstr.='<br><br><font color=#2DFF11>'.$military_lang['besonderheitschlachtschiff'].'</font>';
	if($i-81==6)$zstr.='<br><br><font color=#2DFF11>'.$military_lang['besonderheittransmitterschiff'].'</font>';


	$mtip[$c1] = getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'&'.$zstr;
	//////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////
	//anzahl der einheiten auslesen
	$e0=$einheiten_daten[0]['e'.$i];//anzahl der einheiten auslesen
	$e1=$einheiten_daten[1]['e'.$i];//anzahl der einheiten auslesen
	$e2=$einheiten_daten[2]['e'.$i];//anzahl der einheiten auslesen
	$e3=$einheiten_daten[3]['e'.$i];//anzahl der einheiten auslesen

	//Schiffe, die nicht bewegbar sind, da die flotte nicht daheim ist
	$fleet_a[0]=$einheiten_daten[1]['aktion'];
	$fleet_a[1]=$einheiten_daten[2]['aktion'];
	$fleet_a[2]=$einheiten_daten[3]['aktion'];
	if($fleet_a[0]!=0)$e1=0;
	if($fleet_a[1]!=0)$e2=0;
	if($fleet_a[2]!=0)$e3=0;

	// Gesamte Flotte
	echo 'gesamtf['.$c1.'] = '.($e0+$e1+$e2+$e3).';';

	// Aktuelle Flottenaufteilung
	echo 'aktf['.$c1.'] = new Array('.$e1.', '.$e2.', '.$e3.');';

	// Reisezeiten
	echo 'reisez['.$c1.'] = new Array('.$schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][0].', '.($schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][0]+1).', '.($schiffsdaten[$_SESSION['ums_rasse']-1][$i-81][0]+2).');';

	//transportdaten überprüfen
	//jäger
	if($i==81)$bentid[0]=$c1;
	//bomber
	if($i==86)$bentid[1]=$c1;
	//kreuzer
	if($i==84)$tid[0]=$c1;
	//schlachter
	if($i==85)$tid[1]=$c1;
	//träger
	if($i==88)$tid[2]=$c1;
	//zerstörer
	if($i==83)$tid[3]=$c1;
	$c1++;

    $i++;
  }
  if($fleet_a[0]!=0){echo 'fleetna[0] = true;';} else echo 'fleetna[0] = false;';
  if($fleet_a[1]!=0){echo 'fleetna[1] = true;';} else echo 'fleetna[1] = false;';
  if($fleet_a[2]!=0){echo 'fleetna[2] = true;';} else echo 'fleetna[2] = false;';

?>
 // Anzahl der aktuell vorhandenen Schiffe (dient als Grundlage für alle Ausführungen)
 var anzs = <?php echo $sv_anz_schiffe-1?>;

 var firstrun = true;
 var runagain = false;

 // Variable zum Zwischenspeichern des alten Feldwertes (bei onFocus)
 var vt = 0;
 var htmp = 0;

 if (anzs != -1) { var calcf = new Array(anzs); }

 // [0] = ID Kreuzer [1] = ID Schlachter [2] = ID Träger [3] = Kapazität Kreuzer [4] = Kapazität Schlachter [5] = Kapazität Trager
 var tragers = new Array(<?=$tid[0]?>, <?=$tid[1]?>, <?=$tid[2]?>, <?=$schiffsdaten[$_SESSION['ums_rasse']-1][3][1]?>, <?=$schiffsdaten[$_SESSION['ums_rasse']-1][4][1]?>, <?=$schiffsdaten[$_SESSION['ums_rasse']-1][7][1]?>);
 var trager = new Array(2);

 // [0] = ID des Feldes für Nissen [1] = ID des Feldes für Bomber [2] = Nötiger Platz für Nissen [3] = Nötige Platz für Bomber
 var tragbars = new Array(<?=$bentid[0]?>, <?=$bentid[1]?>, <?=$schiffsdaten[$_SESSION['ums_rasse']-1][0][2]?>, <?=$schiffsdaten[$_SESSION['ums_rasse']-1][5][2]?>);
 var tragbar = new Array(2);
 tragbar[0] = new Array(0, 0);
 tragbar[1] = new Array(0, 0);
 tragbar[2] = new Array(0, 0);

 // [0] = ID Zerstoerer [1] = ID Kreuzer [2] = ben. Zerstoerer [3] = ben. Kreuzer
 var bsspeedup = new Array(<?=$tid[3]?>, <?=$tid[0]?>, <?=$sv_bs_speedup[$_SESSION['ums_rasse']-1][0]?>, <?=$sv_bs_speedup[$_SESSION['ums_rasse']-1][1]?>);
 var bsspeedupmod = 0;

 var fk_per_ship=<?php echo $unit[$_SESSION['ums_rasse']-1][8]['fk'];?>;

</script>
<?php
echo '<script language="javascript" type="text/javascript" src="js/military.js?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/js/military.js').'"></script>';
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";
if ($errmsg!='')echo '<div class="mod mil-meldungen">'.$errmsg.'</div>';

function rangok($zscore, $rang_nr, $sector, $system, $zcol, $kriegsgegner, $counter){
	global $db, $punkte, $erang_nr, $sv_attgrenze, $sv_oscar, $sv_attgrenze_whg_bonus, $sv_sector_attmalus, $ownsector, $ownsystem, $col, $sv_min_col_attgrenze, $sv_max_col_attgrenze;

	//der erhabene ist immer angreifbar
	if($rang_nr==0){
		return(1);
	}

	//sich selbst kann man nich atten/deffen
	if($sector==$ownsector && $system==$ownsector){
		return(0);
	}

	//überprüfen ob es evtl. der eigene sektor ist
	if($sector==$ownsector){
		return(0);
	}

	//sektordaten auslesen
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT platz, npc FROM de_sector WHERE sec_id='$sector'");
	$row = mysqli_fetch_array($db_daten);
	$secplatz=$row['platz'];
	$npcsec=$row['npc'];

	//sektormalus berechnen
	if ($npcsec==0){//normaler spielersektor
		//sektormalus bei der attgrenze berechnen
		//zuerst anzahl der pc-sektoren auslesen
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT sec_id FROM de_sector WHERE npc=0 AND platz>0");
		$num = mysqli_num_rows($db_daten);
		if($num<1)$num=1;

		//eigenen sektorplatz auslesen
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT platz FROM de_sector WHERE sec_id='$ownsector'");
		$row = mysqli_fetch_array($db_daten);
		$ownsectorplatz=$row['platz'];

		//sektorplatzunterschied berechnen
		$secplatzunterschied=$secplatz-$ownsectorplatz;
		if($secplatzunterschied<0)$secplatzunterschied=0;

		//secmalus berechnen
		$sec_malus=$sv_sector_attmalus/$num*$secplatzunterschied;

		//secmalus darf nicht größer als maximum sein
		if($sec_malus>$sv_sector_attmalus)$sec_malus=$sv_sector_attmalus;
		$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus+$sec_malus;

		//angriffsgrenze für die kollektoren berechnen
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0");
		$row = mysqli_fetch_array($db_daten);
		$maxcol=$row['maxcol'];
		if($maxcol==0)$maxcol=1;
		$col_angriffsgrenze=$col*100/$maxcol;
		$col_angriffsgrenze_final=$col_angriffsgrenze/100*$sv_max_col_attgrenze;
		if($col_angriffsgrenze_final>$sv_max_col_attgrenze)$col_angriffsgrenze_final=$sv_max_col_attgrenze;
		if($col_angriffsgrenze_final<$sv_min_col_attgrenze)$col_angriffsgrenze_final=$sv_min_col_attgrenze;

	} else{//aliensektor
		//kein malus bei aliens
		$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus;
		$col_angriffsgrenze_final=0;//$sv_min_col_attgrenze;
	}

	//Unterscheidung bei Kriegsgegnern, diese können Kollektoren zerstören / Aliens lassen sich auch angreifen
	if($kriegsgegner || $counter || $npcsec==1){
		if ($punkte*$sec_angriffsgrenze<=$zscore) return(1); else return(0);
	}else{
		if($punkte*$sec_angriffsgrenze<=$zscore && $col*$col_angriffsgrenze_final<=$zcol) return(1); else return(0);
	}
}


function attdef($fleet_id, $sector, $system, $pt, $zsec, $zsys, $db, $akttyp, $aktzeit){
	global $ownally, $schiffsdaten, $errmsg, $col, $military_lang, $sv_npcatt_col_grenze, $ownsector, $showfleettarget;

	//teste ob die flotte bereit ist befehle zu bekommen
	$sql="SELECT aktion, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id = '$fleet_id'";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$row = mysqli_fetch_array($db_daten);
	$akt=$row['aktion'];
	$schiffe=0;$ge=0;$deftarn=0;
	for ($i=81;$i<=90;$i++){
		$erg=$row['e'.$i];
		if ($erg>0) $schiffe=1;
		$ez[$i-81]=$erg;
		//fix um die zerstörer der 4. rasse unsichtbar zu machen
		if($_SESSION['ums_rasse']==4 && $i==83 && $akttyp==1){$deftarn=$erg; $erg=0;}
		$ge=$ge+$erg;
	}

	if ($schiffe==0)$errmsg.=mil_meldung($military_lang['error10'], 'fehler', $fleet_id);
	if ($schiffe==1 and $akt<>0)$errmsg.=mil_meldung($military_lang['error11'], 'fehler', $fleet_id);
		if ($schiffe==1 and $akt==0){ //flotte kann befehle bekommen
		  //teste ob die koordinaten ok sind
		  if ($zsec=='')$zsec=0;
		  if ($zsys=='')$zsys=0;
		  $zk=$zsec.':'.$zsys;
		  $ak=$sector.':'.$system;
		  if ($zk==$ak) $zsec=0;
		  $db_daten=mysqli_query($GLOBALS['dbi'],"SELECT user_id, score, col, techs, status, allytag, rang, npc FROM de_user_data WHERE sector='$zsec' and system='$zsys'");
		  $num = mysqli_num_rows($db_daten);

		  if($num==1){//die koordinaten stimmen
			//zuerstmal die daten vom ziel auslesen
			$zallytag='';
			$rowx = mysqli_fetch_array($db_daten);
			$uid = $rowx['user_id'];
			$zscore = $rowx['score'];
			$zcol= $rowx['col'];
			$rang_nr = $rowx['rang'];
			$npc=$rowx['npc'];
			if ($rowx['status']==1) $zallytag = $rowx['allytag'];
			//Zieltechnologien laden
			$ztechs=loadPlayerTechs($uid);

			//schauen ob der rang ok ist
			//zuerstmal eigenen rang feststellen, aber nur bei kampfhandlung
			if ($akttyp==1){
				//sind es kriegsgegner?
				$kriegsgegner=false;
				$atter_ally_id=get_player_allyid($_SESSION['ums_user_id']);
				$target_ally_id=get_player_allyid($uid);

				$ok=rangok($zscore, $rang_nr, $zsec, $zsys, $zcol,
				checkForKriegsgegner($atter_ally_id, $target_ally_id), checkForCounter($_SESSION['ums_user_id'], $uid));
			}
			//npc-accounts sind nicht unbegrenzt angreifbar
			//if ($akttyp==1 AND $npc==1 AND $col>=$sv_npcatt_col_grenze) $attverbot=1;
			$attverbot=0;

			if ($akttyp==2) $ok=1;//bei der verteidigung spielt der rang keine rolle

			//status für urlaub bestimmen
			$db_datenx=mysqli_query($GLOBALS['dbi'],"SELECT status FROM de_login WHERE user_id='$uid'");
			$rowx = mysqli_fetch_array($db_datenx);
			if ($rowx['status']==3) $ok=0;
			if ($rowx['status']==2) $ok=0;

			//man kann ein Ziel nicht gleichzeitig angreifen und verteidigen
			if ($akttyp==1){//beim Angriff testen ob man das Ziel defft
				$fleet_id_1=$_SESSION['ums_user_id'].'-1';
				$fleet_id_2=$_SESSION['ums_user_id'].'-2';
				$fleet_id_3=$_SESSION['ums_user_id'].'-3';
				$sql="SELECT aktion FROM de_user_fleet WHERE aktion=2 AND zielsec='$zsec' AND zielsys='$zsys' AND (user_id = '$fleet_id_1' OR user_id = '$fleet_id_2' OR user_id = '$fleet_id_3');";
				$db_datenf=mysqli_query($GLOBALS['dbi'],$sql);
				$num = mysqli_num_rows($db_datenf);
				if($num>0){
					$ok=0;
					$errmsg.=mil_meldung('Es ist nicht erlaubt ein Ziel gleichzeitig anzugreifen und zu verteidigen.', 'fehler', $fleet_id);
				}
			}
			elseif ($akttyp==2){//beim Deffen testen ob man das Ziel angreift
				$fleet_id_1=$_SESSION['ums_user_id'].'-1';
				$fleet_id_2=$_SESSION['ums_user_id'].'-2';
				$fleet_id_3=$_SESSION['ums_user_id'].'-3';
				$sql="SELECT aktion FROM de_user_fleet WHERE aktion=1 AND zielsec='$zsec' AND zielsys='$zsys' AND (user_id = '$fleet_id_1' OR user_id = '$fleet_id_2' OR user_id = '$fleet_id_3');";
				$db_datenf=mysqli_query($GLOBALS['dbi'],$sql);
				$num = mysqli_num_rows($db_datenf);
				if($num>0){
					$ok=0;
					$errmsg.=mil_meldung('Es ist nicht erlaubt ein Ziel gleichzeitig anzugreifen und zu verteidigen.', 'fehler', $fleet_id);
				}
			}

			if ($ok==1){ //wenn soweit alles ok, schauen ob man angreifen/deffen kann aufgrund von allys/bündnissen/krieg usw.
				//----------- Ally Feinde/Freunde
				$allypartner = array();
				$allyfeinde = array();
				$query = "select id from de_allys where allytag='$ownally'";
				$allyresult = mysqli_query($GLOBALS['dbi'],$query);
				$at=mysqli_num_rows($allyresult);
				if ($at!=0){
					$row = mysqli_fetch_array($allyresult);
					$allyid = $row['id'];


					$allyresult = mysqli_query($GLOBALS['dbi'],"SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=$allyid or ally_id_2=$allyid) and (ally_id_1=id or ally_id_2=id)");
					while($row = mysqli_fetch_array($allyresult)){
							if ($ownally != $row['allytag'])
								  $allypartner[] = $row['allytag'];
					}

					$allyresult = mysqli_query($GLOBALS['dbi'],"SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=$allyid or ally_id_angegriffener=$allyid) and (ally_id_angreifer=id or ally_id_angegriffener=id)");
					while($row = mysqli_fetch_array($allyresult)){
						if ($ownally != $row['allytag']){
							$allyfeinde[] = $row['allytag'];
						}
					}
				}
				//------------
				if ($akttyp==1){//beim angriff schauen ob es ein verbündeter ist
				   if (($ownally!='') && (($ownally==$zallytag) || (in_array($zallytag, $allypartner)))) $ok=0;
				}
				elseif ($akttyp==2) //bei der verteidigung schauen ob es ein gegner ist
				{
				  //jemand von einer feindlichen allianz kann nicht gedefft werden
				  //außer jemand, der im eigenen sektor ist

				  if($ownsector!=$zsec)
				  {

					if(($ownally!='') && (in_array($zallytag, $allyfeinde))) $ok=0;
				  }
				}
			}

			if ($ok==1 && $attverbot==0){
				$rz=get_fleet_ground_speed($ez, $_SESSION['ums_rasse'], $_SESSION['ums_user_id']);

				//entfernungzuschlag
				if ($zsec<>$sector){
					$rz=$rz+2;
				}

				//wenn angriff akttyp=1 dann addiere sprungfeldbegrenzer

				if ($akttyp==1 && $rz>0){
					//sektor
					$db_datensec=mysqli_query($GLOBALS['dbi'],"SELECT techs FROM de_sector WHERE sec_id = '$zsec'");
					$rowsec = mysqli_fetch_array($db_datensec);
					$sectechs = $rowsec['techs'];
					if ($sectechs[2]==1) $rz++;

					//test auf Sprungfeldbegrenzer beim Ziel und beim SFM
					if (hasTech($ztechs,5) && !hasTech($pt,75)){
						$rz++;
					}
				}

				//und noch prüfen, ob der gegner die flotte bemerkt hat
				if (hasTech($ztechs,12)) $w=100;
				else if (hasTech($ztechs,11)) $w=66;
				else if (hasTech($ztechs,10)) $w=33;
				else $w=0;
				$r=mt_rand (1, 100);
				$entdeckt=0;
				$entdecktsec=0;
				if ($akttyp==2) $r=0;  //wenn die flotte verteidigt ist sie immer sichtbar
				if ($r<=$w){ //flotte wurde entdeckt
					//überprüfen, ob der sektor die flotte sieht
					if(mt_rand (1, 100) <= 100) $entdecktsec=1;
					if ($akttyp==2) $entdecktsec=1;//wenn die flotte verteidigt ist sie immer sichtbar

					//nachricht an den account schicken
					$entdeckt=1;
					$time=date("YmdHis");
					if ($akttyp==1){$freind=$military_lang['feindliche'];$newsid=51;}
					else {$freind=$military_lang['verbuendete'];$newsid=53; $ge=$ge+$deftarn;}
					if ($ge==1) $sb=$military_lang['schiff'];else $sb=$military_lang['schiffen'];
					mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, $newsid,'$time','$military_lang[sendmsg1] $freind $military_lang[sendmsg2] ".number_format($ge, 0,"",".")." $sb $military_lang[sendmsg3]: ($ak), $military_lang[reisezeit]: $rz')");
					mysqli_query($GLOBALS['dbi'],"update de_user_data set newnews = 1 where user_id = $uid");
				}

				if ($rz>0){//zielkoordinaten und flotte ok? flotte starten
					//wenn man ohne transen angreift, dann meldung ausgeben
					if($akttyp==1 AND $ez[6]==0)
					$errmsg.=mil_meldung($military_lang['notranseninfo'], 'warn', $fleet_id);

					//showfleettarget auslesen
					$hv=explode('-',$fleet_id);
					$showft=$showfleettarget[$hv[1]-1];

					//flotte losschicken
					if ($aktzeit<1 OR $aktzeit>3)$aktzeit=0;
					if ($akttyp==1)$aktzeit=0;
					$sql="UPDATE de_user_fleet SET aktion = '$akttyp', zeit = '$rz', gesrzeit = '$rz', zielsys = '$zsys', zielsec='$zsec', entdeckt='$entdeckt', entdecktsec='$entdecktsec', showfleettarget='$showft', aktzeit = '$aktzeit', fleetsize = '$ge' WHERE user_id = '$fleet_id'";
					mysqli_query($GLOBALS['dbi'],$sql);

					//Bestätigung mit Ziel und Reisezeit
					$errmsg.=mil_meldung(($akttyp==1 ? $military_lang['status2'].' auf ' : $military_lang['status3'].' von ').$zsec.':'.$zsys.' befohlen, '.$military_lang['reisezeit'].' '.$rz.' KT.', 'ok', $fleet_id);
				}
			}else $errmsg.=mil_meldung($military_lang['attunwuerdig'], 'fehler', $fleet_id);

			//return $rz;
		}else{
			//kein Spieler unter den Zielkoordinaten; eigenes System und Sektor 1 werden oben bewusst ungültig gemacht
			if($zk==$ak){
				$errmsg.=mil_meldung('Das eigene System ist kein Ziel.', 'fehler', $fleet_id);
			}elseif($zsec==-1){
				$errmsg.=mil_meldung('Flotteneins&auml;tze nach Sektor 1 oder aus Sektor 1 heraus sind nicht m&ouml;glich.', 'fehler', $fleet_id);
			}elseif($zsec==0 && $zsys==0){
				$errmsg.=mil_meldung('Bitte gib Zielkoordinaten an.', 'fehler', $fleet_id);
			}else{
				$errmsg.=mil_meldung('Unter den Koordinaten '.htmlspecialchars($zk, ENT_QUOTES, 'UTF-8').' gibt es kein Ziel.', 'fehler', $fleet_id);
			}
		}
	} //else return 0;

	if (isset($rz) && $rz==0)$errmsg.=mil_meldung($military_lang['befehlefehlerhaft'], 'fehler', $fleet_id);
}

function recall($fleet_id, $sector, $system, $db){
	global $military_lang, $spec5, $errmsg;

	//erstmal die daten der flotte holen und sichern
	$sql="select aktion, zeit, entdeckt, zielsec, zielsys, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 from de_user_fleet where user_id = '$fleet_id'";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$row = mysqli_fetch_array($db_daten);
	$entdeckt=$row['entdeckt'];
	$akttyp=$row['aktion'];
	$zsec=$row['zielsec'];
	$zsys=$row['zielsys'];

	//flotte zurückrufen
	//erstmal schauen, ob man sie überhaupt zurückrufen kann
	//if ($akttyp==1 OR $akttyp==2 OR $akttyp==4){//also wenn sie hinfliegt
	if ($akttyp==1 OR $akttyp==2){//also wenn sie hinfliegt
        //nur Flotten, die noch hinfliegen (aktion 1/2): sonst würde ein zweiter, gleichzeitiger Rückruf die Flugzeit nochmals umdrehen
        if($spec5!=2){
            $sql="UPDATE de_user_fleet set aktion = 3, zeit = gesrzeit - zeit, entdeckt = 0, zielsec = hsec, zielsys = hsys, aktzeit=0 WHERE user_id = '$fleet_id' AND aktion IN (1,2)";
        }else{
            // Saubere Lösung: Erst prüfen, dann berechnen
            $sql="UPDATE de_user_fleet set aktion = 3, zeit = CASE WHEN gesrzeit > zeit THEN gesrzeit - zeit - 1 ELSE 0 END, entdeckt = 0, zielsec = hsec, zielsys = hsys, aktzeit=0 WHERE user_id = '$fleet_id' AND aktion IN (1,2)";
        }

		mysqli_query($GLOBALS['dbi'],$sql);
		if (mysqli_affected_rows($GLOBALS['dbi']) != 1) {
			return;
		}
		$errmsg.=mil_meldung($military_lang['befehl2'].' befohlen.', 'ok', $fleet_id);

		//schon weit weg? wenn nicht, dann status wieder auf defence
		$sql="SELECT zeit FROM de_user_fleet WHERE user_id = '$fleet_id'";
		$db_daten1=mysqli_query($GLOBALS['dbi'],$sql);
		$rowx = mysqli_fetch_array($db_daten1);
		$zeit=$rowx['zeit'];
		if ($zeit<=0 OR $zeit >250){//setzte gleich wieder status verteidigen, da noch nicht soweit geflogen
			$sql="UPDATE de_user_fleet SET aktion = 0, zielsec = 0, zielsys = 0, zeit = 0 WHERE user_id = '$fleet_id'";
			mysqli_query($GLOBALS['dbi'],$sql);
		}

		if ($entdeckt==1){ //rückzugsnachricht schreiben
			$time=date("YmdHis");
			$ak=$sector.':'.$system;

			//einheiten zählen
			$ge=0;
			for ($i=81;$i<=90;$i++){
				$erg=$row['e'.$i];
				$ez[$i-81]=$erg;
				//fix um die zerstörer der 4. rasse unsichtbar zu machen
				if($_SESSION['ums_rasse']==4 AND $i==83 AND $akttyp==1)$erg=0;
				$ge=$ge+$erg;
			}

			$db_datenz=mysqli_query($GLOBALS['dbi'],"SELECT user_id FROM de_user_data WHERE sector='$zsec' and system='$zsys'");
			if(mysqli_num_rows($db_datenz)>0){
				$rowz = mysqli_fetch_array($db_datenz);
				$uid=$rowz['user_id'];

				if ($akttyp==1){$freind=$military_lang['feindlich'];$newsid=52;}
				else {$freind=$military_lang['verbuendet'];$newsid=54;}
				if ($ge==1) $sb=$military_lang['schiff'];else $sb=$military_lang['schiffe'];
				//nachricht für den rückzug zusammenbauen
				$newsmsg=$military_lang['eineflotteziehtsichzurueck'].'<br>'.
						 $military_lang['flottengesinnung'].': '.$freind.'<br>'.
						 $sb.': '.number_format($ge, 0,"",".").'<br>'.
						 $military_lang['ursprung'].': '.$ak;

				mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, $newsid,'$time','$newsmsg')");
				mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET newnews = 1 WHERE user_id = $uid");
			}
		}
	}//ende der if ($akttyp==1 OR $akttyp==2)
}

if ($techs[13]==0 AND 1==2){
	$techcheck="SELECT tech_name FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=13";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);

	echo '<br>';
	rahmen_oben($military_lang['fehlendesgebaeude']);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=13" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_13.jpg" border="0"></a></td>
	<td valign="top">'.$military_lang['gebaeudeinfo'].': '.$row_techcheck['tech_name'].'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{

  //tooltips für flotteninfos generieren
  unset($flottentooltip);
  for($flotte=0;$flotte<=3;$flotte++){
  $fleetid=$_SESSION['ums_user_id'].'-'.$flotte;
  $result=mysqli_query($GLOBALS['dbi'],"SELECT komatt, komdef, aktion, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3  FROM de_user_fleet WHERE user_id='$fleetid'");
  $row = mysqli_fetch_array($result);

  $flottentooltip[$flotte] ='&<b>Angriffsformation '.$rangnamen[getfleetlevel($row['komatt'])].' ('.number_format($row['komatt'], 0,"",".").')</b><br>
  							Feuerkraftbonus: '.number_format(((24-getfleetlevel($row['komatt']))*0.4), 2,",",".").'%<br>
  							L&auml;hmkraftbonus: '.number_format(((24-getfleetlevel($row['komatt']))*0.4), 2,",",".").'%<br><br>
							<b>Verteidigungsformation '.$rangnamen[getfleetlevel($row['komdef'])].' ('.number_format($row['komdef'], 0,"",".").')</b><br>
  							Feuerkraftbonus: '.number_format(((24-getfleetlevel($row['komdef']))*0.4), 2,",",".").'%<br>
  							L&auml;hmkraftbonus: '.number_format(((24-getfleetlevel($row['komdef']))*0.4), 2,",",".").'%';
  }

  //lade die anzahl der einheiten
  $fid0=$_SESSION['ums_user_id'].'-0';$fid1=$_SESSION['ums_user_id'].'-1';$fid2=$_SESSION['ums_user_id'].'-2';$fid3=$_SESSION['ums_user_id'].'-3';
  $einheiten_result=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
  $einheiten_daten=array();
  while($row = mysqli_fetch_array($einheiten_result)){ //jeder gefundene datensatz wird geprueft
	  $einheiten_daten[]=$row;
  }

  $fleet_a[1]=$einheiten_daten[1]['aktion'];
  $fleet_a[2]=$einheiten_daten[2]['aktion'];
  $fleet_a[3]=$einheiten_daten[3]['aktion'];

  $fleet_mission_time[1]=$einheiten_daten[1]['mission_time'];
  $fleet_mission_time[2]=$einheiten_daten[2]['mission_time'];
  $fleet_mission_time[3]=$einheiten_daten[3]['mission_time'];

  $flottennamen=array($military_lang['heimatflotte'], $military_lang['flotte1'], $military_lang['flotte2'], $military_lang['flotte3']);

  //////////////////////////////////////////////////////////
  // Flotten als Spalten: Kopf mit Auftrag und Punkten, je Schiffstyp eine Zeile, darunter Reisezeit, Träger,
  // Fracht, Befehl und Ziel. Die Felder gehören über das form-Attribut zu milform1 (Umstellung) bzw.
  // milform2 (Befehle); beide Abläufe bleiben getrennt wie bisher, auch beim Absenden mit Enter.
  // IDs (m*_0, m*_1..3, mn*_1..3, fs_*, rz*, m*_t, m*_t_max, m*_fk, fp*) nutzt js/military.js
  //////////////////////////////////////////////////////////
	if(isset($se) OR isset($sy))
	{
		$zsecf1=intval($se);
		$zsecf2=intval($se);
		$zsecf3=intval($se);
		$zsysf1=intval($sy);
		$zsysf2=intval($sy);
		$zsysf3=intval($sy);
	}
	$zsecf=array(1 => $zsecf1 ?? '', 2 => $zsecf2 ?? '', 3 => $zsecf3 ?? '');
	$zsysf=array(1 => $zsysf1 ?? '', 2 => $zsysf2 ?? '', 3 => $zsysf3 ?? '');

	//Kopf je Flotte: Auftrag als Farbe und Chip, darunter Ziel und Zeit
	$kopf=array(0 => array('heim', '<span class="mil-status">im System</span>', ''));
	for($f=1;$f<=3;$f++){
		$fd=$einheiten_daten[$f];
		if($fd['showfleettarget']==1){
			$ziel='<span class="mil-ziel" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors eingesehen werden.">'.$fd['zielsec'].':'.$fd['zielsys'].'</span>';
		}else{
			$ziel='<span class="mil-ziel mil-ziel-verdeckt" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors nicht eingesehen werden.">'.$fd['zielsec'].':'.$fd['zielsys'].'</span>';
		}
		switch($fleet_a[$f]){
			case 1:
				$kopf[$f]=array('angriff', '<span class="mil-status mil-status-angriff">'.$military_lang['status2'].'</span>', $ziel.' &middot; '.$fd['zeit'].' KT');
				break;
			case 2:
				//angekommen: verteidigt noch aktzeit KT
				if($fd['zeit']==0){
					$kopf[$f]=array('verteidigung', '<span class="mil-status mil-status-verteidigung">'.$military_lang['status6'].'</span>', $ziel.' &middot; noch '.$fd['aktzeit'].' KT');
				}else{
					$kopf[$f]=array('verteidigung', '<span class="mil-status mil-status-verteidigung">'.$military_lang['status3'].'</span>', $ziel.' &middot; '.$fd['zeit'].' KT');
				}
				break;
			case 3:
				$kopf[$f]=array('rueckflug', '<span class="mil-status mil-status-rueckflug">'.$military_lang['status4'].'</span>', $fd['zeit'].' KT');
				break;
			case 4:
				//Missionen laufen in Echtzeit: Ende als Uhrzeit
				$kopf[$f]=array('mission', '<span class="mil-status mil-status-mission">'.$military_lang['status5'].'</span>', RealTime::until($fleet_mission_time[$f]));
				break;
			default:
				$kopf[$f]=array('heim', '<span class="mil-status mil-status-heim">daheim</span>', '');
		}
	}

	rahmen_oben('Flotten');
	echo '
	<div class="mil mod">
	<form action="military.php" method="POST" name="milform1" id="milform1" onsubmit="return savekoord();">
		<input type="hidden" name="zsecf1save" value="">
		<input type="hidden" name="zsecf2save" value="">
		<input type="hidden" name="zsecf3save" value="">
		<input type="hidden" name="zsysf1save" value="">
		<input type="hidden" name="zsysf2save" value="">
		<input type="hidden" name="zsysf3save" value="">
	</form>
	<form action="military.php" method="POST" name="milform2" id="milform2"></form>

	<div class="mil-raster">
		<span class="mil-ecke"><span class="mod-chip mil-hilfe" title="'.$ranginfo.'">Formationen</span><span class="mil-ecke-punkte">'.$military_lang['flottenpunktewert'].'</span></span>';
	for($f=0;$f<=3;$f++){
		echo '
		<span class="mil-sp mil-kopf mil-kopf-'.$kopf[$f][0].($f==0 ? ' mil-sp-heim' : '').'" title="'.$flottennamen[$f].$flottentooltip[$f].'">
			<b>'.$flottennamen[$f].'</b>
			'.$kopf[$f][1].'
			<small class="mil-detail">'.$kopf[$f][2].'</small>
			<span class="mil-punktwert"><span id="fp'.$f.'"></span> <small>Pkt.</small></span>
		</span>';
	}

	//eine Zeile je Schiffstyp
	$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, tech_name, tech_vor FROM de_tech_data WHERE tech_id>80 AND tech_id<100 ORDER BY tech_id");
	$i=81;
	$c1=1;
	while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
		$e0=$einheiten_daten[0]['e'.$i];//anzahl der einheiten auslesen
		$e1=$einheiten_daten[1]['e'.$i];//anzahl der einheiten auslesen
		$e2=$einheiten_daten[2]['e'.$i];//anzahl der einheiten auslesen
		$e3=$einheiten_daten[3]['e'.$i];//anzahl der einheiten auslesen

		echo '
		<span class="mil-name" title="'.$mtip[$c1-1].'">'.getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'</span>
		<span class="mil-sp mil-sp-heim mil-zahl mil-heim'.($e0==0 ? ' mil-null' : '').'" id="m'.$c1.'_0">'.number_format($e0, 0,"",".").'</span>';
		//Flotte I-III: Eingabefeld, unterwegs nur die Anzahl (die Zahl muss am Anfang stehen, military.js liest sie aus)
		foreach(array(1 => $e1, 2 => $e2, 3 => $e3) as $f => $anzahl){
			if($fleet_a[$f]!=0){
				echo '<span class="mil-sp mil-zahl mil-unterwegs'.($anzahl==0 ? ' mil-null' : '').'" id="mn'.$c1.'_'.$f.'">'.number_format($anzahl, 0,"",".").'<input type="text" form="milform1" id="m'.$c1.'_'.$f.'" name="m'.$i.'_'.$f.'" value="0" style="display: none;"></span>';
			}else{
				echo '<span class="mil-sp mil-feld" id="mn'.$c1.'_'.$f.'"><input form="milform1" class="mil-eingabe" type="text" inputmode="numeric" id="m'.$c1.'_'.$f.'" name="m'.$i.'_'.$f.'" value="0" maxlength="10" onKeyup="SetMil(this)" onFocus="vt=this.value=delPkt(this.value);" onBlur="SetMil(this); vt=this.value=addPkt(this.value)"></span>';
			}
		}
		$c1++;
		$i++;
	}

	//Schnellwahl: alles in die Heimatflotte, je Flotte eine Aktion
	echo '
		<span class="mil-label mil-trenn mil-befehlzeile">Schnellwahl</span>
		<span class="mil-sp mil-sp-heim mil-trenn mil-befehlzeile"><button type="button" class="mod-btn mod-btn-leise mil-btn-voll" onclick="DoFleetAction(0,\'0:-1\');" title="Alle Schiffe in die Heimatflotte">'.$military_lang['alle'].'</button></span>';
	$andere=array(1 => array(2, 3), 2 => array(1, 3), 3 => array(1, 2));
	for($f=1;$f<=3;$f++){
		if($fleet_a[$f]!=0){
			echo '<span class="mil-sp mil-trenn mil-befehlzeile"></span>';
			continue;
		}
		$plus='';
		foreach($andere[$f] as $j => $g){
			$plus.='<option value="'.($j+2).':'.$g.'">+ '.$flottennamen[$g].'</option>';
		}
		$prozent='';
		for($p=10;$p<=90;$p+=10){
			$prozent.='<option value="5:'.$p.'">+ '.$p.'% '.$military_lang['hflotte'].'</option>';
		}
		echo '
		<span class="mil-sp mil-trenn mil-befehlzeile"><select class="mil-auswahl mil-gross" id="fs_'.$f.'" onChange="DoFleetAction('.$f.', document.getElementById(\'fs_'.$f.'\').options[document.getElementById(\'fs_'.$f.'\').options.selectedIndex].value)">
			<option value="-1:-1">- '.$military_lang['aktion'].' -</option>
			<option value="-1:-1">------------------------</option>
			<option value="0:-1">'.$military_lang['aktion2'].'</option>
			<option value="1:-1">+ '.$military_lang['heimatflotte'].'</option>
			'.$plus.'
			<option value="4:-1">'.$military_lang['zuheimatflotte'].'</option>
			<option value="-1:-1">------------------------</option>
			'.$prozent.'
		</select></span>';
	}

	//Werte je Flotte, berechnet von military.js; zwei Werte teilen sich eine Zeile
	echo '
		<span class="mil-label mil-trenn" title="Reisezeit&In KT, zuerst ins eigene, dann in andere Sektoren.">Reisezeit KT<small>eigener / anderer Sektor</small></span>
		<span class="mil-sp mil-sp-heim mil-trenn"></span>';
	//Flotte unterwegs: Träger und Fracht beziehen sich auf die Aufstellung daheim, deshalb ausgeblendet
	$aus=array();
	for($f=1;$f<=3;$f++){
		$aus[$f]=($fleet_a[$f]!=0) ? ' mil-aus' : '';
		echo '<span class="mil-sp mil-zahl mil-paar mil-trenn"><b id="rz'.$f.'_1">0</b><i>/</i><b id="rz'.$f.'_2">0</b></span>';
	}
	echo '
		<span class="mil-label">Tr&auml;ger<small>ben&ouml;tigt / vorhanden</small></span>
		<span class="mil-sp mil-sp-heim"></span>';
	for($f=1;$f<=3;$f++){
		echo '<span class="mil-sp mil-zahl mil-paar'.$aus[$f].'"><span id="m'.$f.'_t">0</span><i>/</i><span id="m'.$f.'_t_max">0</span></span>';
	}
	echo '
		<span class="mil-label">Fracht</span>
		<span class="mil-sp mil-sp-heim"></span>';
	for($f=1;$f<=3;$f++){
		echo '<span class="mil-sp mil-zahl'.$aus[$f].'"><span id="m'.$f.'_fk">0</span></span>';
	}

	//Befehl und Ziel; auf Mission sind keine Befehle möglich
	echo '
		<span class="mil-label mil-trenn mil-befehlzeile">Befehl</span>
		<span class="mil-sp mil-sp-heim mil-trenn mil-befehlzeile"></span>';
	//Befehl, Ziel und Sichtbarkeit je in eigener Zeile und groß genug zum Antippen auf dem Handy
	for($f=1;$f<=3;$f++){
		if($fleet_a[$f]==4){
			echo '<span class="mil-sp mil-trenn mil-befehlzeile mil-gesperrt">keine Befehle</span>';
			continue;
		}
		if($fleet_a[$f]!=0){
			$hs='<option value=0>Beibehalten</option><option value=1>'.$military_lang['befehl2'].'</option>';
		}else{
			$hs='<option value=0>Beibehalten</option><option value=2>'.$military_lang['befehl4'].'</option><option value=3>'.$military_lang['befehl5'].'</option><option value=4>'.$military_lang['befehl6'].'</option><option value=5>'.$military_lang['befehl7'].'</option>';
		}
		echo '<span class="mil-sp mil-trenn mil-befehlzeile"><select name="af'.$f.'" form="milform2" class="mil-auswahl mil-gross" title="'.$military_lang['befehl1'].'">'.$hs.'</select></span>';
	}
	echo '
		<span class="mil-label mil-befehlzeile">Ziel</span>
		<span class="mil-sp mil-sp-heim mil-befehlzeile"></span>';
	for($f=1;$f<=3;$f++){
		if($fleet_a[$f]==4){
			echo '<span class="mil-sp mil-befehlzeile"></span>';
			continue;
		}
		echo '
		<span class="mil-sp mil-befehlzeile mil-koords">
			<input type="text" form="milform2" inputmode="numeric" name="zsecf'.$f.'" value="'.$zsecf[$f].'" maxlength="5" class="mil-eingabe" placeholder="Sek." title="Sektor">
			<i>:</i>
			<input type="text" form="milform2" inputmode="numeric" name="zsysf'.$f.'" value="'.$zsysf[$f].'" maxlength="'.($f==1 ? 3 : 5).'" class="mil-eingabe" placeholder="Sys." title="System">
		</span>';
	}
	echo '
		<span class="mil-label mil-befehlzeile" title="Ziel sichtbar&Die Zielkoordinaten k&ouml;nnen im Sektorstatus von anderen Spielern im Sektor gesehen werden.">Sichtbar<small>f&uuml;r den Sektor</small></span>
		<span class="mil-sp mil-sp-heim mil-unten mil-befehlzeile"></span>';
	for($f=1;$f<=3;$f++){
		if($fleet_a[$f]==4){
			echo '<span class="mil-sp mil-unten mil-befehlzeile"></span>';
			continue;
		}
		if($showfleettarget[$f-1]==1)$checked='checked';else $checked='';
		echo '
		<span class="mil-sp mil-unten mil-befehlzeile">
			<label class="mil-sichtbar"><input '.$checked.' type="checkbox" form="milform2" name="showfleet'.$f.'" value="1">Ziel zeigen</label>
		</span>';
	}
	echo '
	</div>
	<div class="mil-fuss">
		<input type="submit" form="milform1" name="verlegen" value="'.$military_lang['flottenumstellen'].'" class="mod-btn mod-btn-leise" title="&Uuml;bernimmt die Verteilung der Schiffe auf die Flotten.">
		<input type="submit" form="milform2" name="befehle" value="'.$military_lang['dobefehl'].'" class="mod-btn" title="Schickt die Flotten mit Befehl und Ziel los.">
	</div>
	</div>';
	rahmen_unten();
} //raumwerftbedinung ende
?>
<script language="javascript">
SetMil();

$(document).ready(function () {
$("input").tooltip({
	      track: true,
	      delay: 0,
	      showURL: false,
	      showBody: "&",
	      extraClass: "design1",
	      fixPNG: true,
	      opacity: 1.00,
	      left: 0
	  });
	  });
</script>

</body>
</html>
