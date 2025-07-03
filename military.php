<?php
include('inc/header.inc.php');
include('lib/transaction.lib.php');
include('inc/schiffsdaten.inc.php');
include('functions.php');
include('tickler/kt_einheitendaten.php');
include('inc/lang/'.$sv_server_lang.'_military.lang.php');
$military_lang['klassennamen']=array('J&auml;ger','Jagdboot','Zerst&ouml;rer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Tr&auml;ger','Frachter','Titan',
'Orbitalj&auml;ger-Basis','Flugk&ouml;rper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');

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
for($i=0;$i<25;$i++)
{
  $counter=($i*($i-1)*30000)+30000;
  if($i==0)$counter=0;
  $ranginfo.=$rangnamen[24-$i].': '.number_format($counter, 0,",",".").'<br>';
}

//sichtbarkeit des flottenziels
$showfleettarget=array(1,1,1);//standardm��ig sichtbar
if (isset($_POST['befehle'])){
	if(!isset($_POST['showfleet1'])) $showfleettarget[0]=0;
	if(!isset($_POST['showfleet2'])) $showfleettarget[1]=0;
	if(!isset($_POST['showfleet3'])) $showfleettarget[2]=0;
}

//spezialisierung tr�gerkapazit�t
if($spec3==2){	
	for($i=0;$i<count($schiffsdaten[$ums_rasse]);$i++){
		$schiffsdaten[$ums_rasse-1][$i][1]= floor($schiffsdaten[$ums_rasse-1][$i][1] * 1.2);
	}	
}

if (isset($_POST['befehle'])){
  $zsecf1=(int)$_POST['zsecf1'];
  $zsysf1=(int)$_POST['zsysf1'];
  $zsecf2=(int)$_POST['zsecf2'];
  $zsysf2=(int)$_POST['zsysf2'];
  $zsecf3=(int)$_POST['zsecf3'];
  $zsysf3=(int)$_POST['zsysf3'];

  //sektor 1 unangreifbar machen und man kann auch nicht aus sektor 1 angegriffen werden
  if($zsecf1==1 OR $sector==1)$zsecf1='-1';
  if($zsecf2==1 OR $sector==1)$zsecf2='-1';
  if($zsecf3==1 OR $sector==1)$zsecf3='-1';
  /*
    Aktionen
    0: Verteidigung des Heimatsystems
    1: Angriff auf ein System
    2: Verteidigung eines anderen Systems
    3: R�ckflug ins Heimatsystem
    4: Questhinflug
  */
  //fuer jede flotte eigene sektion
  //flotte 1
  $af1=intval($_POST['af1']);
  switch($af1){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($ums_user_id.'-1', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($ums_user_id.'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($ums_user_id.'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
    case 4: //verteidigen
      attdef($ums_user_id.'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
    case 5: //verteidigen
      attdef($ums_user_id.'-1', $sector, $system, $pt, $zsecf1, $zsysf1, $db, 2, $af1-2);
      break;
    case 6: //arch�ologie
      quest($ums_user_id.'-1', $zsecf1, $zsysf1);
      break;
  }//switch af1 ende
  //flotte 2
  $af2=intval($_POST['af2']);
  switch($af2){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($ums_user_id.'-2', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($ums_user_id.'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($ums_user_id.'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
    case 4: //verteidigen
      attdef($ums_user_id.'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
    case 5: //verteidigen
      attdef($ums_user_id.'-2', $sector, $system, $pt, $zsecf2, $zsysf2, $db, 2, $af2-2);
      break;
    case 6: //arch�ologie
      quest($ums_user_id.'-2', $zsecf2, $zsysf2);
      break;  }//switch af2 ende
  //flotte 3
  $af3=intval($_POST['af3']);
  switch($af3){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($ums_user_id.'-3', $sector, $system, $db);
      break;
    case 2: //angreifen
      attdef($ums_user_id.'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 1, 0);
      break;
    case 3: //verteidigen
      attdef($ums_user_id.'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
    case 4: //verteidigen
      attdef($ums_user_id.'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
    case 5: //verteidigen
      attdef($ums_user_id.'-3', $sector, $system, $pt, $zsecf3, $zsysf3, $db, 2, $af3-2);
      break;
    case 6: //arch�ologie
      quest($ums_user_id.'-3', $zsecf3, $zsysf3);
      break;  }//switch af3 ende
}

function quest($fleet_id, $zsec, $zsys){
	global $db, $errmsg, $ums_user_id, $military_lang, $showfleettarget;

	//teste ob die flotte bereit ist befehle zu bekommen
	$sql="SELECT aktion, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id = '$fleet_id'";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$row = mysqli_fetch_array($db_daten);
	$akt=$row['aktion'];
	$schiffe=0;
	//schauen ob schiffe in der flotte sind
	for ($i=81;$i<=90;$i++){
		$erg=$row['e'.$i];
		if ($erg>0) $schiffe=1;
	}

	//echo $schiffe.':'.$akt;
	if ($schiffe>0)$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['error'].'<br>'.$military_lang['error2'].'</td></tr></table>';
	if ($akt<>0)$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['error3'].'</td></tr></table>';
	if ($schiffe==0 and $akt==0){ //flotte kann befehle bekommen
		//teste ob die koordinaten ok sind
		//man kann die einheiten nur in npc-systeme schicken
		$zsec=(int)$zsec;
		$zsys=(int)$zsys;
		$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT user_id FROM de_user_data WHERE sector='$zsec' and system='$zsys' AND npc=1");
		$num = mysqli_num_rows($db_daten);

		if($num==1){//die koordinaten stimmen
			//showfleetstatus auslesen
			$hv=explode('-',$fleet_id);
			$showft=$showfleettarget[$hv[1]-1];

			//flotte auf die reise schicken
			$aktzeit=0;
			$akttyp=4;
			$rz=5;
			$sql="UPDATE de_user_fleet SET aktion = '$akttyp', zeit = '$rz', gesrzeit = '$rz', zielsys = '$zsys', zielsec='$zsec', entdeckt=0, showfleettarget='$showft', aktzeit = '$aktzeit', fleetsize = 0 WHERE user_id = '$fleet_id'";
			mysqli_query($GLOBALS['dbi'],$sql);
			//meldung dass alles ok ist
			$errmsg.='<table width=600><tr><td class="ccg">'.$military_lang['fleetrausack'].'</td></tr></table>';
		}
		else $errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['fleetrausnack'].'</td></tr></table>';
	}
}


//schauen ob er die whg hat und dann die attgrenze anpassen
if ($techs[4]==0)$sv_attgrenze_whg_bonus=0;

if (isset($_POST['verlegen'])){
	//transaktionsbeginn
	if (setLock($ums_user_id)){
		//zuerst alle übergebenen felder in nen array packen
		$c=0;
		for ($i=81; $i<=80+$sv_anz_schiffe; $i++){
			str_replace(".","", $_POST['m".$i."_1'] ?? 0);

			if($_POST['m'.$i.'_1']>0)$sa[$c][0]=(int)str_replace(".","",$_POST['m'.$i.'_1']);else $sa[$c][0]=0;
			if($_POST['m'.$i.'_2']>0)$sa[$c][1]=(int)str_replace(".","",$_POST['m'.$i.'_2']);else $sa[$c][1]=0;
			if($_POST['m'.$i.'_3']>0)$sa[$c][2]=(int)str_replace(".","",$_POST['m'.$i.'_3']);else $sa[$c][2]=0;
			/*echo 'A:'.$_POST['m".$i."_1'].' - '.'B: '.$sa[$c][0].'<br>';
			echo 'C:'.$_POST['m".$i."_2'].' - '.'D: '.$sa[$c][1].'<br>';
			echo 'E:'.$_POST['m".$i."_3'].' - '.'F: '.$sa[$c][2].'<br><br>';*/
			$c++;
		}
		//flottendaten laden
		$fid0=$ums_user_id.'-0';$fid1=$ums_user_id.'-1';$fid2=$ums_user_id.'-2';$fid3=$ums_user_id.'-3';
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

			//des weiteren d�rfen keine flotten die unterwegs sind ge�ndert werden, deshalb zuerst dieses �berpr�fen
			for($j=1;$j<=3;$j++)
			{
			  //if($sa[$i-81][$j-1]!=$fleet[$j] AND $fleet_a[$j]!=0)$error=2;
			  //if($error==2) echo 'A: '.$sa[$i-81][$j-1].' - '.$fleet[$j].'<br>';
			  if($fleet_a[$j]!=0)$sa[$i-81][$j-1]=$fleet[$j];
			}

			//schauen ob die zahlen soweit ok sind
			$error=0;
			if(($sa[$i-81][0]+$sa[$i-81][1]+$sa[$i-81][2])<=$gesamt){
			  //man darf keine absoluten zahlen setzen, da es ansonsten mit dem bautick zu problemen kommen k�nnte
			  //wenn das ok ist weiter und die flottenzahlen anpassen
			  if($error==0){
					//schiffsanzahl in der heimatflotte berechnen
					$saheim=$gesamt-$sa[$i-81][0]-$sa[$i-81][1]-$sa[$i-81][2];
					//werte f�r den sql-befehl berechnen
					$fw0=$saheim-$fleet[0];
					$fw1=$sa[$i-81][0]-$fleet[1];
					$fw2=$sa[$i-81][1]-$fleet[2];
					$fw3=$sa[$i-81][2]-$fleet[3];
					//schiffsart updaten, wenn die flotte auch daheim ist
					$sql ="UPDATE de_user_fleet SET e$i = e$i + '$fw0' WHERE user_id = '$ums_user_id-0';";
					mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw1' WHERE user_id = '$ums_user_id-1';";
					if($fleet_a[1]==0)mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw2' WHERE user_id = '$ums_user_id-2';";
					if($fleet_a[2]==0)mysqli_query($GLOBALS['dbi'],$sql);
					$sql="UPDATE de_user_fleet SET e$i = e$i + '$fw3' WHERE user_id = '$ums_user_id-3';";
					if($fleet_a[3]==0)mysqli_query($GLOBALS['dbi'],$sql);
				}
			}
			else $error=1;
			//echo 'Fehler: '.$error.'<br>';
			//schauen welche fehlermeldungen man ausgeben mu�
			if($error==1)$showerror[0]=1;
			if($error==2)$showerror[1]=1;
		}

		//fehlermessage erstellen
		if($showerror[0]==1)
		$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['allgfehler'].'</td></tr></table><br>';
		if($showerror[1]==1)
		$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['allgfehler2'].'</td></tr></table>';
		//wenn kein fehler aufgetrten ist, ok info ausgeben
		if($showerror[0]==0 AND $showerror[1]==0)
		$errmsg.='<table width=600><tr><td class="ccg">'.$military_lang['fleetumgestellt'].'</td></tr></table>';

		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
			print($military_lang['releaselock'].$ums_user_id.$military_lang['releaselock2']."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$military_lang['setlock'].'</font><br><br>';
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
//alle werte auslesen, die man f�r die seite ben�tigt
//lade die anzahl der einheiten
$fid0=$ums_user_id.'-0';$fid1=$ums_user_id.'-1';$fid2=$ums_user_id.'-2';$fid3=$ums_user_id.'-3';
$einheiten_result=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
$einheiten_daten=array();
while($row = mysqli_fetch_array($einheiten_result)){ //jeder gefundene datensatz wird geprueft
	$einheiten_daten[]=$row;
}
//echo '</script>A:';
//print_r($einheiten_daten);
//echo '<script type="text/javascript">';
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
	//echo $row['tech_id']-81;
    echo 'shipscore['.$c1.'] = '.$unit[$_SESSION['ums_rasse']-1][$row['tech_id']-81][4].';';
    //zerlege vorbedinguns-string
	/*
    $z1=0;$z2=0;
    $vorb=explode(";",$row['tech_vor']);
    foreach($vorb as $einzelb) //jede einzelne bedingung checken
    {
      $z1++;
      if ($techs[$einzelb]==1) $z2++;
      if ($einzelb==0) {$z1=0;$z2=0;}
    }*/

	//////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////
	//js-tooltip-daten generieren
	//klasse
	$zstr='<font color=#D265FF>'.$military_lang['klasse'].': '.$military_lang['klassennamen'][$i-81].'</font>';
	//punkte
	$zstr.='<br><font color=#FFFA65>'.$military_lang['punkte'].': '.number_format($unit[$_SESSION['ums_rasse']-1][$i-81][4], 0,"",".").'</font>';

	//reiszeit
	$zstr.='<br><br>'.$military_lang['reisezeit'].': '.$schiffsdaten[$ums_rasse-1][$i-81][0];
	//transportkapazität
	if ($schiffsdaten[$ums_rasse-1][$i-81][1]>0)$zstr.='<br>'.$military_lang['kapazitaet'].': '.$schiffsdaten[$ums_rasse-1][$i-81][1];
	//ben. transportkapazität
	if ($schiffsdaten[$ums_rasse-1][$i-81][2]>0)$zstr.='<br>'.$military_lang['kapazitaet2'].': '.$schiffsdaten[$ums_rasse-1][$i-81][2];

	//frachtkapazität
	if (isset($unit[$ums_rasse-1][$i-81]['fk']) && $unit[$ums_rasse-1][$i-81]['fk'] > 0)$zstr.='<br>Frachtkapazit&auml;t: '.$unit[$_SESSION['ums_rasse']-1][$i-81]['fk'];

	//waffenarten
	//konventionell
	if($unit[$ums_rasse-1][$i-81][2]>0)$wv='<font color=#2DFF11>'.$military_lang['waffenvorhandenja'].'</font>';
		else $wv='<font color=#ED0909>'.$military_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#9D4B15>'.$military_lang['waffengattung1'].':</font> '.$wv;

	//klassenziel
	if($unit[$ums_rasse-1][$i-81][2]>0){
		$zstr.='<br><font color=#ED9409>-'.$military_lang['klasseziel1'].': '.$military_lang['klassennamen'][$kampfmatrix[$i-81][0]].'</font>';
		$zstr.='<br><font color=#F0BA66>-'.$military_lang['klasseziel2'].': '.$military_lang['klassennamen'][$kampfmatrix[$i-81][2]].'</font>';
	}

	//emp
	if($unit[$ums_rasse-1][$i-81][3]>0)$wv='<font color=#2DFF11>'.$military_lang['waffenvorhandenja'].'</font>';
		else $wv='<font color=#ED0909>'.$military_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#15629D>'.$military_lang['waffengattung2'].':</font> '.$wv;

	//klassenziel
	if($unit[$ums_rasse-1][$i-81][3]>0){
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
	echo 'reisez['.$c1.'] = new Array('.$schiffsdaten[$ums_rasse-1][$i-81][0].', '.($schiffsdaten[$ums_rasse-1][$i-81][0]+1).', '.($schiffsdaten[$ums_rasse-1][$i-81][0]+2).');';

	//transportdaten �berpr�fen
	//j�ger
	if($i==81)$bentid[0]=$c1;
	//bomber
	if($i==86)$bentid[1]=$c1;
	//kreuzer
	if($i==84)$tid[0]=$c1;
	//schlachter
	if($i==85)$tid[1]=$c1;
	//tr�ger
	if($i==88)$tid[2]=$c1;
	//zerst�rer
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
 var tragers = new Array(<?=$tid[0]?>, <?=$tid[1]?>, <?=$tid[2]?>, <?=$schiffsdaten[$ums_rasse-1][3][1]?>, <?=$schiffsdaten[$ums_rasse-1][4][1]?>, <?=$schiffsdaten[$ums_rasse-1][7][1]?>);
 var trager = new Array(2);

 // [0] = ID des Feldes für Nissen [1] = ID des Feldes für Bomber [2] = Nötiger Platz für Nissen [3] = N�tige Platz f�r Bomber
 var tragbars = new Array(<?=$bentid[0]?>, <?=$bentid[1]?>, <?=$schiffsdaten[$ums_rasse-1][0][2]?>, <?=$schiffsdaten[$ums_rasse-1][5][2]?>);
 var tragbar = new Array(2);
 tragbar[0] = new Array(0, 0);
 tragbar[1] = new Array(0, 0);
 tragbar[2] = new Array(0, 0);

 // [0] = ID Zerstoerer [1] = ID Kreuzer [2] = ben. Zerstoerer [3] = ben. Kreuzer
 var bsspeedup = new Array(<?=$tid[3]?>, <?=$tid[0]?>, <?=$sv_bs_speedup[$ums_rasse-1][0]?>, <?=$sv_bs_speedup[$ums_rasse-1][1]?>);
 var bsspeedupmod = 0;

 var fk_per_ship=<?php echo $unit[$ums_rasse-1][8]['fk'];?>;

</script>
<?php
echo '<script language="javascript" type="text/javascript" src="/military.js?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/military.js').'"></script>';
?>
</head>
<body>
<?php
//stelle die ressourcenleiste dar
include "resline.php";
if ($errmsg!='')echo $errmsg;

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
	
	//�berpr�fen ob es evtl. der eigene sektor ist
	if($sector==$ownsector){
		return(0);
	}

	/*if ($erang_nr <=1)//ausnahmeregelung bei erhabenem und alphas
	{
	  if ($rang_nr<=2 OR $punkte*($sv_attgrenze-$sv_attgrenze_whg_bonus)<=$zscore) return(1); else return(0);
	}
	elseif ($rang_nr<$erang_nr+2 OR $punkte*($sv_attgrenze-$sv_attgrenze_whg_bonus)<=$zscore) return(1); else return(0);*/
	//if ($rang_nr<$erang_nr+2 OR $punkte*($sv_attgrenze-$sv_attgrenze_whg_bonus)<=$zscore) return(1); else return(0);
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

		//secmalus darf nicht gr��er als maximum sein
		if($sec_malus>$sv_sector_attmalus)$sec_malus=$sv_sector_attmalus;
		//echo $sec_malus;
		$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus+$sec_malus;
		
		//angriffsgrenze f�r die kollektoren berechnen
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
	

	//$col_angriffsgrenze_final=$sec_angriffsgrenze*$col_angriffsgrenze/100;
	/*
	$col_angriffsgrenze_final=$col_angriffsgrenze/100*$sv_max_col_attgrenze;
	if($col_angriffsgrenze_final>$sv_max_col_attgrenze)$col_angriffsgrenze_final=$sv_max_col_attgrenze;
	if($col_angriffsgrenze_final<$sv_min_col_attgrenze)$col_angriffsgrenze_final=$sv_min_col_attgrenze;
	if ($punkte*$sec_angriffsgrenze<=$zscore AND $col*$col_angriffsgrenze_final<=$zcol) return(1); else return(0);*/
	
	/*
	if($sv_oscar==1){
		//echo 'A: '.$col*$col_angriffsgrenze_final.' B: '.$zcol;
		if($punkte*$sec_angriffsgrenze<=$zscore && $col*$col_angriffsgrenze_final<=$zcol) return(1); else return(0);
	}else{
		//Unterscheidung bei Kriegsgegnern, diese können Kollektoren zerstören / Aliens lassen sich auch angreifen
		if($kriegsgegner || $counter || $npcsec==1){
			//echo 'Kriegsgegner';
			if ($punkte*$sec_angriffsgrenze<=$zscore) return(1); else return(0);
		}else{
			//echo 'Kein Kriegsgegner';
			if($punkte*$sec_angriffsgrenze<=$zscore && $col*$col_angriffsgrenze_final<=$zcol) return(1); else return(0);
		}
	}*/

	//Unterscheidung bei Kriegsgegnern, diese können Kollektoren zerstören / Aliens lassen sich auch angreifen
	if($kriegsgegner || $counter || $npcsec==1){
		//echo 'Kriegsgegner';
		if ($punkte*$sec_angriffsgrenze<=$zscore) return(1); else return(0);
	}else{
		//echo 'Kein Kriegsgegner';
		if($punkte*$sec_angriffsgrenze<=$zscore && $col*$col_angriffsgrenze_final<=$zcol) return(1); else return(0);
	}
}


function attdef($fleet_id, $sector, $system, $pt, $zsec, $zsys, $db, $akttyp, $aktzeit){
	global $ownally, $ums_rasse, $schiffsdaten, $errmsg, $ums_user_id, $col, $military_lang, $sv_npcatt_col_grenze, $ownsector, $showfleettarget;

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
		if($ums_rasse==4 && $i==83 && $akttyp==1){$deftarn=$erg; $erg=0;}
		$ge=$ge+$erg;
	}

	//echo $schiffe.':'.$akt;
	if ($schiffe==0)$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['error10'].'</td></tr></table>';
	if ($schiffe==1 and $akt<>0)$errmsg.='<table width=600><tr><td class="ccr">'.$military_lang['error11'].'</td></tr></table>';
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
				$atter_ally_id=get_player_allyid($ums_user_id);
				$target_ally_id=get_player_allyid($uid);
				
				$ok=rangok($zscore, $rang_nr, $zsec, $zsys, $zcol, 
				checkForKriegsgegner($atter_ally_id, $target_ally_id), checkForCounter($ums_user_id, $uid));
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
				$fleet_id_1=$ums_user_id.'-1';
				$fleet_id_2=$ums_user_id.'-2';
				$fleet_id_3=$ums_user_id.'-3';
				$sql="SELECT aktion FROM de_user_fleet WHERE aktion=2 AND zielsec='$zsec' AND zielsys='$zsys' AND (user_id = '$fleet_id_1' OR user_id = '$fleet_id_2' OR user_id = '$fleet_id_3');";
				//echo $sql;
				$db_datenf=mysqli_query($GLOBALS['dbi'],$sql);
				$num = mysqli_num_rows($db_datenf);
				if($num>0){
					$ok=0;
					$errmsg.='<div class="info_box text2">Es ist nicht erlaubt ein Ziel gleichzeitig anzugreifen und zu verteidigen.</div>';
				}
			}
			elseif ($akttyp==2){//beim Deffen testen ob man das Ziel angreift
				$fleet_id_1=$ums_user_id.'-1';
				$fleet_id_2=$ums_user_id.'-2';
				$fleet_id_3=$ums_user_id.'-3';
				$sql="SELECT aktion FROM de_user_fleet WHERE aktion=1 AND zielsec='$zsec' AND zielsys='$zsys' AND (user_id = '$fleet_id_1' OR user_id = '$fleet_id_2' OR user_id = '$fleet_id_3');";
				$db_datenf=mysqli_query($GLOBALS['dbi'],$sql);
				$num = mysqli_num_rows($db_datenf);
				if($num>0){
					$ok=0;
					$errmsg.='<div class="info_box text2">Es ist nicht erlaubt ein Ziel gleichzeitig anzugreifen und zu verteidigen.</div>';
				}
			}

			if ($ok==1){ //wenn soweit alles ok, schauen ob man angreifen/deffen kann aufgrund von allys/b�ndnissen/krieg usw.
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
				if ($akttyp==1){//beim angriff schauen ob es ein verb�ndeter ist
				   if (($ownally!='') && (($ownally==$zallytag) || (in_array($zallytag, $allypartner)))) $ok=0;
				}
				elseif ($akttyp==2) //bei der verteidigung schauen ob es ein gegner ist
				{
				  //jemand von einer feindlichen allianz kann nicht gedefft werden
				  //au�er jemand, der im eigenen sektor ist

				  if($ownsector!=$zsec)
				  {

					if(($ownally!='') && (in_array($zallytag, $allyfeinde))) $ok=0;
				  }
				}
			}

			if ($ok==1 && $attverbot==0){
				$rz=get_fleet_ground_speed($ez, $ums_rasse, $ums_user_id);

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

				//und noch pr�fen, ob der gegner die flotte bemerkt hat
				if (hasTech($ztechs,12)) $w=100;
				else if (hasTech($ztechs,11)) $w=66;
				else if (hasTech($ztechs,10)) $w=33;
				else $w=0;
				//mt_srand(10000);
				$r=mt_rand (1, 100);
				//echo $w.' '.$r;
				$entdeckt=0;
				$entdecktsec=0;
				if ($akttyp==2) $r=0;  //wenn die flotte verteidigt ist sie immer sichtbar
				if ($r<=$w){ //flotte wurde entdeckt
					//�berpr�fen, ob der sektor die flotte sieht
					if(mt_rand (1, 100) <= 100) $entdecktsec=1;
					if ($akttyp==2) $entdecktsec=1;//wenn die flotte verteidigt ist sie immer sichtbar

					//nachricht an den account schicken
					$entdeckt=1;
					$time=strftime("%Y%m%d%H%M%S");
					//$uid=mysqli_result($db_daten, 0, "user_id");
					if ($akttyp==1){$freind=$military_lang['feindliche'];$newsid=51;}
					else {$freind=$military_lang['verbuendete'];$newsid=53; $ge=$ge+$deftarn;}
					if ($ge==1) $sb=$military_lang['schiff'];else $sb=$military_lang['schiffen'];
					mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, $newsid,'$time','$military_lang[sendmsg1] $freind $military_lang[sendmsg2] ".number_format($ge, 0,"",".")." $sb $military_lang[sendmsg3]: ($ak), $military_lang[reisezeit]: $rz')");
					mysqli_query($GLOBALS['dbi'],"update de_user_data set newnews = 1 where user_id = $uid");
				}

				if ($rz>0){//zielkoordinaten und flotte ok? flotte starten
					//wenn man ohne transen angreift, dann meldung ausgeben
					if($akttyp==1 AND $ez[6]==0)
					$errmsg.='<div class="info_box text2">'.$military_lang['notranseninfo'].'</div>';

					//wenn man andere spieler angreift, handelsinfo
					/*
					  if($akttyp==1 AND $npc==0)
					  {
						  $errmsg.='<div class="info_box text2">Achtung: Angriffe auf andere Spieler wirken sich negativ auf den Handelsbonus aus.</div>';
					  }
					  */
					  //showfleettarget auslesen
					  $hv=explode('-',$fleet_id);
					  $showft=$showfleettarget[$hv[1]-1];

					//flotte losschicken
					if ($aktzeit<1 OR $aktzeit>3)$aktzeit=0;
					if ($akttyp==1)$aktzeit=0;
					$sql="UPDATE de_user_fleet SET aktion = '$akttyp', zeit = '$rz', gesrzeit = '$rz', zielsys = '$zsys', zielsec='$zsec', entdeckt='$entdeckt', entdecktsec='$entdecktsec', showfleettarget='$showft', aktzeit = '$aktzeit', fleetsize = '$ge' WHERE user_id = '$fleet_id'";
					mysqli_query($GLOBALS['dbi'],$sql);
				}
			}else $errmsg.='<div class="info_box text2">'.$military_lang['attunwuerdig'].'</div>';

			//return $rz;
		} //else return 0;
	} //else return 0;
	
	if (isset($rz) && $rz==0)$errmsg.='<div class="info_box text2">'.$military_lang['befehlefehlerhaft'].'</div>';
}

function recall($fleet_id, $sector, $system, $db){
	global $ums_rasse, $military_lang, $spec5;

	//erstmal die daten der flotte holen und sichern
	$sql="select aktion, zeit, entdeckt, zielsec, zielsys, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 from de_user_fleet where user_id = '$fleet_id'";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$row = mysqli_fetch_array($db_daten);
	$entdeckt=$row['entdeckt'];
	$akttyp=$row['aktion'];
	$zsec=$row['zielsec'];
	$zsys=$row['zielsys'];

	//flotte zurückrufen
	//erstmal schauen, ob man sie �berhaupt zur�ckrufen kann
	//if ($akttyp==1 OR $akttyp==2 OR $akttyp==4){//also wenn sie hinfliegt
	if ($akttyp==1 OR $akttyp==2){//also wenn sie hinfliegt
		if($spec5!=2)$sql="UPDATE de_user_fleet set aktion = 3, zeit = gesrzeit-zeit, entdeckt = 0, zielsec = hsec, zielsys = hsys, aktzeit=0 WHERE user_id = '$fleet_id'";
		else $sql="UPDATE de_user_fleet set aktion = 3, zeit = gesrzeit-zeit-1, entdeckt = 0, zielsec = hsec, zielsys = hsys, aktzeit=0 WHERE user_id = '$fleet_id'";
		mysqli_query($GLOBALS['dbi'],$sql);
		//echo $sql;

		//schon weit weg? wenn nicht, dann status wieder auf defence
		$sql="SELECT zeit FROM de_user_fleet WHERE user_id = '$fleet_id'";
		$db_daten1=mysqli_query($GLOBALS['dbi'],$sql);
		$rowx = mysqli_fetch_array($db_daten1);
		$zeit=$rowx['zeit'];
		if ($zeit<=0 OR $zeit >250){//setzte gleich wieder status verteidigen, da noch nicht soweit geflogen
			$sql="UPDATE de_user_fleet SET aktion = 0, zielsec = 0, zielsys = 0, zeit = 0 WHERE user_id = '$fleet_id'";
			mysqli_query($GLOBALS['dbi'],$sql);
		}

		if ($entdeckt==1){ //r�ckzugsnachricht schreiben
			$time=strftime("%Y%m%d%H%M%S");
			$ak=$sector.':'.$system;

			//einheiten z�hlen
			$ge=0;
			for ($i=81;$i<=90;$i++){
				$erg=$row['e'.$i];
				$ez[$i-81]=$erg;
				//fix um die zerst�rer der 4. rasse unsichtbar zu machen
				if($ums_rasse==4 AND $i==83 AND $akttyp==1)$erg=0;
				$ge=$ge+$erg;
			}

			$db_datenz=mysqli_query($GLOBALS['dbi'],"SELECT user_id FROM de_user_data WHERE sector='$zsec' and system='$zsys'");
			if(mysqli_num_rows($db_datenz)>0){
				$rowz = mysqli_fetch_array($db_datenz);
				$uid=$rowz['user_id'];

				if ($akttyp==1){$freind=$military_lang['feindlich'];$newsid=52;}
				else {$freind=$military_lang['verbuendet'];$newsid=54;}
				if ($ge==1) $sb=$military_lang['schiff'];else $sb=$military_lang['schiffe'];
				//nachricht f�r den r�ckzug zusammenbauen
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
	$techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=13";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);
	//echo $military_lang[techcheck].$row_techcheck[tech_name].$military_lang[techcheck2];

	echo '<br>';
	rahmen_oben($military_lang['fehlendesgebaeude']);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=13" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_13.jpg" border="0"></a></td>
	<td valign="top">'.$military_lang['gebaeudeinfo'].': '.$row_techcheck['tech_name'].'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{
echo '
<form action="military.php" method="POST" name="milform1" onsubmit="return savekoord();">

<input type="hidden" name="zsecf1save" value="">
<input type="hidden" name="zsecf2save" value="">
<input type="hidden" name="zsecf3save" value="">
<input type="hidden" name="zsysf1save" value="">
<input type="hidden" name="zsysf2save" value="">
<input type="hidden" name="zsysf3save" value="">

<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td align="center" class="ro"><div class="cellu">'.$military_lang['fleetaufstellung'].' <img title="'.$ranginfo.'" src="'.
  $ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td>
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="144">
<col width="90">
<col width="110">
<col width="110">
<col width="110">
</colgroup>';

  //tooltips f�r flotteninfos generieren
  unset($flottentooltip);
  for($flotte=0;$flotte<=3;$flotte++){
  $fleetid=$ums_user_id.'-'.$flotte;
  $result=mysqli_query($GLOBALS['dbi'],"SELECT komatt, komdef, aktion, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3  FROM de_user_fleet WHERE user_id='$fleetid'");
  $row = mysqli_fetch_array($result);
  
  $flottentooltip[$flotte] ='&<b>Angriffsformation '.$rangnamen[getfleetlevel($row['komatt'])].' ('.number_format($row['komatt'], 0,"",".").')</b><br> 
  							Feuerkraftbonus: '.number_format(((24-getfleetlevel($row['komatt']))*0.4), 2,",",".").'%<br>
  							L&auml;hmkraftbonus: '.number_format(((24-getfleetlevel($row['komatt']))*0.4), 2,",",".").'%<br><br>
							<b>Verteidigungsformation '.$rangnamen[getfleetlevel($row['komdef'])].' ('.number_format($row['komdef'], 0,"",".").')</b><br> 
  							Feuerkraftbonus: '.number_format(((24-getfleetlevel($row['komdef']))*0.4), 2,",",".").'%<br>
  							L&auml;hmkraftbonus: '.number_format(((24-getfleetlevel($row['komdef']))*0.4), 2,",",".").'%';  							
  }
  
  
  //echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
  echo '<tr align="center">';
  echo '<td class="tc">&nbsp;</td>';
  echo '<td class="tc">'.$military_lang['heimatflotte'].' <img title="'.$flottentooltip[0].'" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
  echo '<td class="tc">'.$military_lang['flotte1'].' <img title="'.$flottentooltip[1].'" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
  echo '<td class="tc">'.$military_lang['flotte2'].' <img title="'.$flottentooltip[2].'" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
  echo '<td class="tc">'.$military_lang['flotte3'].' <img title="'.$flottentooltip[3].'" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
  echo '</tr>';
  //echo '</table>';

  //lade die anzahl der einheiten
  $fid0=$ums_user_id.'-0';$fid1=$ums_user_id.'-1';$fid2=$ums_user_id.'-2';$fid3=$ums_user_id.'-3';
  $einheiten_result=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
  $einheiten_daten=array();
  while($row = mysqli_fetch_array($einheiten_result)){ //jeder gefundene datensatz wird geprueft
	  $einheiten_daten[]=$row;
  }
  //print_r($einheiten_daten);
	//echo "SELECT e81, e82, e83, e84, e85, e86, e87 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY fleet_id ASC";
  //lade einheitentypen
  $db_daten=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, tech_name, tech_vor FROM de_tech_data WHERE tech_id>80 AND tech_id<100 ORDER BY tech_id");
  $i=81;$ez1=Array(0,0,0,0,0,0,0,0,0,0);$ez2=Array(0,0,0,0,0,0,0,0,0,0);$ez3=Array(0,0,0,0,0,0,0,0,0,0);
  $c1=1;
  while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
    //zerlege vorbedinguns-string
	  /*
    $z1=0;$z2=0;
    $vorb=explode(";",$row['tech_vor']);
    foreach($vorb as $einzelb) //jede einzelne bedingung checken
    {
      $z1++;
      if ($techs[$einzelb]==1) $z2++;
      if ($einzelb==0) {$z1=0;$z2=0;}
    }
	   */

	$e0=$einheiten_daten[0]['e'.$i];//anzahl der einheiten auslesen
	$e1=$einheiten_daten[1]['e'.$i];//anzahl der einheiten auslesen
	$e2=$einheiten_daten[2]['e'.$i];//anzahl der einheiten auslesen
	$e3=$einheiten_daten[3]['e'.$i];//anzahl der einheiten auslesen

	//daten für die befehlsanzeige auslesen
	if($c1==1){
		$fleet_a[1]=$einheiten_daten[1]['aktion'];
		$fleet_a[2]=$einheiten_daten[2]['aktion'];
		$fleet_a[3]=$einheiten_daten[3]['aktion'];

		$fleet_mission_time[1]=$einheiten_daten[1]['mission_time'];
		$fleet_mission_time[2]=$einheiten_daten[2]['mission_time'];
		$fleet_mission_time[3]=$einheiten_daten[3]['mission_time'];
	}

	$ez1[$i-81]=$e1;//sammele die flottenanzahl fuer die rz-berechnung
	$ez2[$i-81]=$e2;
	$ez3[$i-81]=$e3;

	echo '<tr>';
	//heimatflotte
	echo '<td class="cl">&nbsp;<img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$mtip[$c1-1].'">&nbsp;'.getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'</td>';
	echo '<td class="cc" id="m'.$c1.'_0">'.number_format($e0, 0,"",".").'</td>';
	//flotte 1
	if($fleet_a[1]!=0){$h1=number_format($e1, 0,"",".");$h2='style="display: none;"';}else{$h1='';$h2='';}
	echo '<td class="cc" id="mn'.$c1.'_1">'.$h1.'<input class="mil1" type="text" id="m'.$c1.'_1" name="m'.$i.'_1" value="0" size="4" maxlength="10" onKeyup="SetMil(this)" onFocus="vt=this.value=delPkt(this.value); this.className=\'mil2\'" onBlur="SetMil(this); this.className=\'mil1\'; vt=this.value=addPkt(this.value)" '.$h2.'></td>';
	//flotte 2
	if($fleet_a[2]!=0){$h1=number_format($e2, 0,"",".");$h2='style="display: none;"';}else{$h1='';$h2='';}
	echo '<td class="cc" id="mn'.$c1.'_2">'.$h1.'<input class="mil1" type="text" id="m'.$c1.'_2" name="m'.$i.'_2" value="0" size="4" maxlength="10" onKeyup="SetMil(this)" onFocus="vt=this.value=delPkt(this.value); this.className=\'mil2\'" onBlur="SetMil(this); this.className=\'mil1\'; vt=this.value=addPkt(this.value)" '.$h2.'></td>';
	//flotte 3
	if($fleet_a[3]!=0){$h1=number_format($e3, 0,"",".");$h2='style="display: none;"';}else{$h1='';$h2='';}
	echo '<td class="cc" id="mn'.$c1.'_3">'.$h1.'<input class="mil1" type="text" id="m'.$c1.'_3" name="m'.$i.'_3" value="0" size="4" maxlength="10" onKeyup="SetMil(this)" onFocus="vt=this.value=delPkt(this.value); this.className=\'mil2\'" onBlur="SetMil(this); this.className=\'mil1\'; vt=this.value=addPkt(this.value)" '.$h2.'></td>';
	echo '</tr>';
	$c1++;

    $i++;
  }

  echo '<tr align="center">
 <td class="cc">&nbsp;</td>
 <td class="cc"><input type="Button" value="'.$military_lang['alle'].'" onclick="DoFleetAction(0,\'0:-1\');"> </td>';
 if($fleet_a[1]!=0){$h1='&nbsp';}else{$h1='
 <select style="width: 100px;" id="fs_1" onChange="DoFleetAction(1, document.getElementById(\'fs_1\').options[document.getElementById(\'fs_1\').options.selectedIndex].value)">
   <option value="-1:-1">- '.$military_lang['aktion'].' -</option>
   <option value="-1:-1">------------------------</option>
   <option value="0:-1">'.$military_lang['aktion2'].'</option>
   <option value="1:-1">+ '.$military_lang['heimatflotte'].'</option>
   <option value="2:2">+ '.$military_lang['flotte2'].'</option>
   <option value="3:3">+ '.$military_lang['flotte3'].'</option>
   <option value="4:-1">'.$military_lang['zuheimatflotte'].'</option>
   <option value="-1:-1">------------------------</option>
   <option value="5:10">+ 10% '.$military_lang['hflotte'].'</option>
   <option value="5:20">+ 20% '.$military_lang['hflotte'].'</option>
   <option value="5:30">+ 30% '.$military_lang['hflotte'].'</option>
   <option value="5:40">+ 40% '.$military_lang['hflotte'].'</option>
   <option value="5:50">+ 50% '.$military_lang['hflotte'].'</option>
   <option value="5:60">+ 60% '.$military_lang['hflotte'].'</option>
   <option value="5:70">+ 70% '.$military_lang['hflotte'].'</option>
   <option value="5:80">+ 80% '.$military_lang['hflotte'].'</option>
   <option value="5:90">+ 90% '.$military_lang['hflotte'].'</option>
  </select>';}
 echo '<td class="cc">'.$h1.'</td>';
 if($fleet_a[2]!=0){$h1='&nbsp';}else{$h1='
<select style="width: 100px;" id="fs_2" onChange="DoFleetAction(2, document.getElementById(\'fs_2\').options[document.getElementById(\'fs_2\').options.selectedIndex].value)">
   <option value="-1:-1">- '.$military_lang['aktion'].' -</option>
   <option value="-1:-1">------------------------</option>
   <option value="0:-1">'.$military_lang['aktion2'].'</option>
   <option value="1:-1">+ '.$military_lang['heimatflotte'].'</option>
   <option value="2:1">+ '.$military_lang['flotte1'].'</option>
   <option value="3:3">+ '.$military_lang['flotte3'].'</option>
   <option value="4:-1">'.$military_lang['zuheimatflotte'].'</option>
   <option value="-1:-1">------------------------</option>
   <option value="5:10">+ 10% '.$military_lang['hflotte'].'</option>
   <option value="5:20">+ 20% '.$military_lang['hflotte'].'</option>
   <option value="5:30">+ 30% '.$military_lang['hflotte'].'</option>
   <option value="5:40">+ 40% '.$military_lang['hflotte'].'</option>
   <option value="5:50">+ 50% '.$military_lang['hflotte'].'</option>
   <option value="5:60">+ 60% '.$military_lang['hflotte'].'</option>
   <option value="5:70">+ 70% '.$military_lang['hflotte'].'</option>
   <option value="5:80">+ 80% '.$military_lang['hflotte'].'</option>
   <option value="5:90">+ 90% '.$military_lang['hflotte'].'</option>
  </select>';}
 echo '<td class="cc">'.$h1.'</td>';
 if($fleet_a[3]!=0){$h1='&nbsp';}else{$h1='
 <select style="width: 100px;" id="fs_3" onChange="DoFleetAction(3, document.getElementById(\'fs_3\').options[document.getElementById(\'fs_3\').options.selectedIndex].value)">
   <option value="-1:-1">- '.$military_lang['aktion'].' -</option>
   <option value="-1:-1">------------------------</option>
   <option value="0:-1">'.$military_lang['aktion2'].'</option>
   <option value="1:-1">+ '.$military_lang['heimatflotte'].'</option>
   <option value="2:1">+ '.$military_lang['flotte1'].'</option>
   <option value="3:2">+ '.$military_lang['flotte2'].'</option>
   <option value="4:-1">'.$military_lang['zuheimatflotte'].'</option>
   <option value="-1:-1">------------------------</option>
   <option value="5:10">+ 10% '.$military_lang['hflotte'].'</option>
   <option value="5:20">+ 20% '.$military_lang['hflotte'].'</option>
   <option value="5:30">+ 30% '.$military_lang['hflotte'].'</option>
   <option value="5:40">+ 40% '.$military_lang['hflotte'].'</option>
   <option value="5:50">+ 50% '.$military_lang['hflotte'].'</option>
   <option value="5:60">+ 60% '.$military_lang['hflotte'].'</option>
   <option value="5:70">+ 70% '.$military_lang['hflotte'].'</option>
   <option value="5:80">+ 80% '.$military_lang['hflotte'].'</option>
   <option value="5:90">+ 90% '.$military_lang['hflotte'].'</option>
  </select>';}

 echo '<td class="cc">'.$h1.'</td>';
 echo '</tr>';

  $attexp0=$einheiten_daten[0]['komatt'];
  $defexp0=$einheiten_daten[0]['komdef'];
  $zsec1=$einheiten_daten[1]['zielsec'];
  $zsys1=$einheiten_daten[1]['zielsys'];
  $a1=$einheiten_daten[1]['aktion'];
  $t1=$einheiten_daten[1]['zeit'];
  $at1=$einheiten_daten[1]['aktzeit'];
  $attexp1=$einheiten_daten[1]['komatt'];
  $defexp1=$einheiten_daten[1]['komdef'];
  $showft1=$einheiten_daten[1]['showfleettarget'];
  $zsec2=$einheiten_daten[2]['zielsec'];
  $zsys2=$einheiten_daten[2]['zielsys'];
  $a2=$einheiten_daten[2]['aktion'];
  $t2=$einheiten_daten[2]['zeit'];
  $at2=$einheiten_daten[2]['aktzeit'];
  $attexp2=$einheiten_daten[2]['komatt'];
  $defexp2=$einheiten_daten[2]['komdef'];
  $showft2=$einheiten_daten[2]['showfleettarget'];
  $zsec3=$einheiten_daten[3]['zielsec'];
  $zsys3=$einheiten_daten[3]['zielsys'];
  $a3=$einheiten_daten[3]['aktion'];
  $t3=$einheiten_daten[3]['zeit'];
  $at3=$einheiten_daten[3]['aktzeit'];
  $attexp3=$einheiten_daten[3]['komatt'];
  $defexp3=$einheiten_daten[3]['komdef'];
  $showft3=$einheiten_daten[3]['showfleettarget'];

  /*
    Aktionen
    0: Verteidigung des Heimatsystems
    1: Angriff auf ein System
    2: Verteidigung eines anderen Systems
    3: R�chflug ins Heimatsystem
    4: Questhinflug
  */

  //flotte 1
  //showfleetstatus
  if($showft1==1){$hs1='<span class="text2" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors eingesehen werden.">';$hs2='</span>';} else {$hs1='<span class="text3" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors nicht eingesehen werden.">';$hs2='</span>';} 
  if ($a1==0) $a1=$military_lang['status'];
  elseif ($a1==1) $a1=$military_lang['status2'].' ('.$hs1.$zsec1.':'.$zsys1.$hs2.') '.$military_lang['reisezeit'].': '.$t1;
  elseif ($a1==2) $a1=$military_lang['status3'].' ('.$hs1.$zsec1.':'.$zsys1.$hs2.') '.$military_lang['reisezeit'].': '.$t1;
  elseif ($a1==3) $a1='&nbsp;&nbsp;'.$military_lang['status4'].'&nbsp;&nbsp; '.$military_lang['reisezeit'].': '.$t1;
  elseif ($a1==4) $a1=$military_lang['status5'].' ('.$hs1.$zsec1.':'.$zsys1.$hs2.') '.$military_lang['reisezeit'].': '.$t1;

  if ($a1[0]==$military_lang['status3'][0] && $t1==0) $a1=$military_lang['status6'].' ('.$hs1.$zsec1.':'.$zsys1.$hs2.') '.$military_lang['zeit'].': '.$at1;

  //flotte 2
  //showfleetstatus
  if($showft2==1){$hs1='<span class="text2" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors eingesehen werden.">';$hs2='</span>';} else {$hs1='<span class="text3" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors nicht eingesehen werden.">';$hs2='</span>';} 
  if ($a2==0) $a2=$military_lang['status'];
  elseif ($a2==1) $a2=$military_lang['status2'].' ('.$hs1.$zsec2.':'.$zsys2.$hs2.') '.$military_lang['reisezeit'].': '.$t2;
  elseif ($a2==2) $a2=$military_lang['status3'].' ('.$hs1.$zsec2.':'.$zsys2.$hs2.') '.$military_lang['reisezeit'].': '.$t2;
  elseif ($a2==3) $a2='&nbsp;&nbsp;'.$military_lang['status4'].'&nbsp;&nbsp; '.$military_lang['reisezeit'].': '.$t2;
  elseif ($a2==4) $a2=$military_lang['status5'].' ('.$hs1.$zsec2.':'.$zsys2.$hs2.') '.$military_lang['reisezeit'].': '.$t2;

  if ($a2[0]==$military_lang['status3'][0] && $t2==0) $a2=$military_lang['status6'].' ('.$hs1.$zsec2.':'.$zsys2.$hs2.') '.$military_lang['zeit'].': '.$at2;

  //flotte 3
  //showfleetstatus
  if($showft3==1){$hs1='<span class="text2" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors eingesehen werden.">';$hs2='</span>';} else {$hs1='<span class="text3" title="Die Zielkoordinaten k&ouml;nnen von den Spielern Deines Sektors nicht eingesehen werden.">';$hs2='</span>';} 
  if ($a3==0) $a3=$military_lang['status'];
  elseif ($a3==1) $a3=$military_lang['status2'].' ('.$hs1.$zsec3.':'.$zsys3.$hs2.') '.$military_lang['reisezeit'].': '.$t3;
  elseif ($a3==2) $a3=$military_lang['status3'].' ('.$hs1.$zsec3.':'.$zsys3.$hs2.') '.$military_lang['reisezeit'].': '.$t3;
  elseif ($a3==3) $a3='&nbsp;&nbsp;'.$military_lang['status4'].'&nbsp;&nbsp; '.$military_lang['reisezeit'].': '.$t3;
  elseif ($a3==4) $a3=$military_lang['status5'].' ('.$hs1.$zsec3.':'.$zsys3.$hs2.') '.$military_lang['reisezeit'].': '.$t3;

  if ($a3[0]==$military_lang['status3'][0] && $t3==0) $a3=$military_lang['status6'].' ('.$hs1.$zsec3.':'.$zsys3.$hs2.') '.$military_lang['zeit'].': '.$at3;

  //reisezeiten ausgeben
  $rz1='';$rz2='';$rz3='';
  if ($a1==$military_lang['status']) // Hier stand Systemverteidigung vorher statt lang-variable
  {
    $rz1=get_fleet_ground_speed($ez1, $ums_rasse, $ums_user_id);
  }

  if ($a2==$military_lang['status']) // Hier stand Systemverteidigung vorher statt lang-variable
  {
    $rz2=get_fleet_ground_speed($ez2, $ums_rasse, $ums_user_id);
  }

  if ($a3==$military_lang['status']) // Hier stand Systemverteidigung vorher statt lang-variable
  {
    $rz3=get_fleet_ground_speed($ez3, $ums_rasse, $ums_user_id);
  }
  //echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
  if ($rz1=='')$rz1='&nbsp';if ($rz2=='')$rz2='&nbsp';if ($rz3=='')$rz3='&nbsp';
  echo '<tr align="center">';
  echo '<td class="cl" colspan="2">&nbsp;<font color="28FF50">Reisezeit eigener Sektor:</td>';
  //echo '<td class="cc">&nbsp;</td>';
  echo '<td class="cc"><font color="28FF50" id="rz1_1">0</td>';
  echo '<td class="cc"><font color="28FF50" id="rz2_1">0</td>';
  echo '<td class="cc"><font color="28FF50" id="rz3_1">0</td>';
  echo "</tr>";
  echo '<tr align="center">';
  if ($rz1>0) $rz1++;
  if ($rz2>0) $rz2++;
  if ($rz3>0) $rz3++;
  if ($rz1=='')$rz1='&nbsp';if ($rz2=='')$rz2='&nbsp';if ($rz3=='')$rz3='&nbsp';
  echo '<td class="cl" colspan="2">&nbsp;<font color="#FDFB59">Reisezeit andere Sektoren:</td>';
  //echo '<td class="cc">&nbsp;</td>';
  echo '<td class="cc"><font color="#FDFB59" id="rz1_2">0</td>';
  echo '<td class="cc"><font color="#FDFB59" id="rz2_2">0</td>';
  echo '<td class="cc"><font color="#FDFB59" id="rz3_2">0</td>';
  echo "</tr>";
  
  /*
  if ($rz1>0) $rz1=$rz1+1;
  if ($rz2>0) $rz2=$rz2+1;
  if ($rz3>0) $rz3=$rz3+1;
  if ($rz1=='')$rz1='&nbsp';if ($rz2=='')$rz2='&nbsp';if ($rz3=='')$rz3='&nbsp';
  echo '<tr align="center">';
  echo '<td class="cc"><font color="#F10505">'.$military_lang[fernesek].'</td>';
  echo '<td class="cc">&nbsp;</td>';
  echo '<td class="cc"><font color="#F10505" id="rz1_3">0</td>';
  echo '<td class="cc"><font color="#F10505" id="rz2_3">0</td>';
  echo '<td class="cc"><font color="#F10505" id="rz3_3">0</td>';
  echo "</tr>";*/

  //ausgabe der trägerkapazität
  echo '<tr align="center">';
  echo '<td class="cl" colspan="2">&nbsp;Tr&auml;gerkapazit&auml;t ben&ouml;tigt:</td>';
  echo '<td class="cc"><font id="m1_t">0</td>';
  echo '<td class="cc"><font id="m2_t">0</td>';
  echo '<td class="cc"><font id="m3_t">0</td>';

  echo '<tr align="center">';
  echo '<td class="cl" colspan="2">&nbsp;Tr&auml;gerkapazit&auml;t vorhanden:</td>';
  echo '<td class="cc"><font id="m1_t_max">0</td>';
  echo '<td class="cc"><font id="m2_t_max">0</td>';
  echo '<td class="cc"><font id="m3_t_max">0</td>';

  //Frachtkapazität
  echo '<tr align="center">';
  echo '<td class="cl" colspan="2">&nbsp;Frachtkapazit&auml;t:</td>';
  echo '<td class="cc"><font id="m1_fk">0</td>';
  echo '<td class="cc"><font id="m2_fk">0</td>';
  echo '<td class="cc"><font id="m3_fk">0</td>';

  //ausgabe des flottenpunktewertes
  //echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
  echo '<tr align="center">';
  echo '<td class="cl">&nbsp;'.$military_lang['flottenpunktewert'].':</td>';
  echo '<td class="cc" id="fp0"></td>';
  echo '<td class="cc" id="fp1"></td>';
  echo '<td class="cc" id="fp2"></td>';
  echo '<td class="cc" id="fp3"></td>';  
  
  echo "</tr>";
  echo '<tr><td align="center" colspan="5" height="37"><input type="submit" name="verlegen" value="'.$military_lang['flottenumstellen'].'"></td></tr>';
  
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
</form>
<form action="military.php" method="POST" name="milform2">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37px">
<td width="13px" height="37px" class="rml">&nbsp;</td>
<td class="ro" align="center"><div class="cellu"><?=$military_lang['flottenbefehle']?></div></td>
<td width="13px" class="rmr">&nbsp;</td>
</tr>
<tr>
<td width="13px" class="rl">&nbsp;</td>
<td>
<table border="0" cellpadding="0" cellspacing="1" width="570px">
<?php
	if(isset($se) OR isset($sy))
	{
		$zsecf1=intval($se);
		$zsecf2=intval($se);
		$zsecf3=intval($se);
		$zsysf1=intval($sy);
		$zsysf2=intval($sy);
		$zsysf3=intval($sy);
	}
	//Flottenbefehle
	echo '<tr>';
	echo '<td width="55px" class="tc">'.$military_lang['flotte'].'</td>';
	echo '<td width="170px" class="tc">'.$military_lang['aktbefehl'].'</td>';
	echo '<td width="220px" class="tc">'.$military_lang['befehl'].'</td>';
	echo '<td width="120px" class="tc">'.$military_lang['zielkoords'].'</td>';
	echo "</tr>";
	//////////////////////////////////////////////////////////
	//Flotte I
	//////////////////////////////////////////////////////////
	echo '<tr>';
	echo '<td class="c">'.$military_lang['flotte1'].'</td>';
	//Mission aktiv? läßt sich nicht abbrechen
	if($fleet_a[1]==4){
		echo '<td class="c" colspan="3">Auf Mission bis: '.date("H:i:s d.m.Y",$fleet_mission_time[1]).'</td>';
	}else{
		echo '<td class="c">'.$a1.'</td>';
		if($fleet_a[1]!=0)$hs='<option value=0>'.$military_lang['befehl1'].'</option><option value=1>'.$military_lang['befehl2'].'</option>';
		else $hs='<option value=0>'.$military_lang['befehl3'].'</option><option value=2>'.$military_lang['befehl4'].'</option><option value=3>'.$military_lang['befehl5'].'</option><option value=4>'.$military_lang['befehl6'].'</option><option value=5>'.$military_lang['befehl7'].'</option>';
		echo '<td class="c"><select name="af1" size=0>'.$hs.'</select></td>';
		if($showfleettarget[0]==1)$checked='checked';else $checked='';
		echo '<td class="c"><input '.$checked.' type="checkbox" name="showfleet1" value="1" title="Die Zielkoordinaten k&ouml;nnen im Sektorstatus von anderen Spielern im Sektor gesehen werden.">&nbsp;<input type="text" name="zsecf1" value="'.($zsecf1 ?? '').'" size="3" maxlength="5">&nbsp;&nbsp;<input type="text" name="zsysf1" value="'.($zsysf1 ?? '').'" size="3" maxlength="3"></td>';
	}
	echo "</tr>";
	//////////////////////////////////////////////////////////
	//Flotte II 
	//////////////////////////////////////////////////////////
	echo '<tr>';
	echo '<td class="c">'.$military_lang['flotte2'].'</td>';
	if($fleet_a[2]==4){
		echo '<td class="c" colspan="3">Auf Mission bis: '.date("H:i:s d.m.Y",$fleet_mission_time[2]).'</td>';
	}else{
		echo '<td class="c">'.$a2.'</td>';
		if($fleet_a[2]!=0)$hs='<option value=0>'.$military_lang['befehl1'].'</option><option value=1>'.$military_lang['befehl2'].'</option>';
		else $hs='<option value=0>'.$military_lang['befehl3'].'</option><option value=2>'.$military_lang['befehl4'].'</option><option value=3>'.$military_lang['befehl5'].'</option><option value=4>'.$military_lang['befehl6'].'</option><option value=5>'.$military_lang['befehl7'].'</option>';
		echo '<td class="c"><select name="af2" size=0>'.$hs.'</select></td>';
		if($showfleettarget[1]==1)$checked='checked';else $checked='';
		echo '<td class="c"><input '.$checked.' type="checkbox" name="showfleet2" value="1" title="Die Zielkoordinaten k&ouml;nnen im Sektorstatus von anderen Spielern im Sektor gesehen werden.">&nbsp;<input type="text" name="zsecf2" value="'.($zsecf2 ?? '').'" size="3" maxlength="5">&nbsp;&nbsp;<input type="text" name="zsysf2" value="'.($zsysf2 ?? '').'" size="3" maxlength="5"></td>';
	}
	echo "</tr>";

	//////////////////////////////////////////////////////////
	//Flotte III 
	//////////////////////////////////////////////////////////
	echo '<tr>';
	echo '<td class="c">'.$military_lang['flotte3'].'</td>';
	if($fleet_a[3]==4){
		echo '<td class="c" colspan="3">Auf Mission bis: '.date("H:i:s d.m.Y",$fleet_mission_time[3]).'</td>';
	}else{
		echo '<td class="c">'.$a3.'</td>';
		if($fleet_a[3]!=0){
			$hs='<option value=0>'.$military_lang['befehl1'].'</option><option value=1>'.$military_lang['befehl2'].'</option>';
		}else{
			$hs='<option value=0>'.$military_lang['befehl3'].'</option><option value=2>'.$military_lang['befehl4'].'</option><option value=3>'.$military_lang['befehl5'].'</option><option value=4>'.$military_lang['befehl6'].'</option><option value=5>'.$military_lang['befehl7'].'</option>';
		}
		echo '<td class="c"><select name="af3" size=0>'.$hs.'</select></td>';
		if($showfleettarget[2]==1)$checked='checked';else $checked='';
		echo '<td class="c"><input '.$checked.' type="checkbox" name="showfleet3" value="1" title="Die Zielkoordinaten k&ouml;nnen im Sektorstatus von anderen Spielern im Sektor gesehen werden.">&nbsp;<input type="text" name="zsecf3" value="'.($zsecf3 ?? '').'" size="3" maxlength="5">&nbsp;&nbsp;<input type="text" name="zsysf3" value="'.($zsysf3 ?? '').'" size="3" maxlength="5"></td>';
	}
	echo "</tr>";
	//echo "</table>";
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="570" align="center"><input type="Submit" name="befehle" value="<?=$military_lang['dobefehl']?>"></td>
<td width="13" class="rr">&nbsp;</td>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
<br>
<?php
} //raumwerftbedinung ende
?>
</div>
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
</form>
<?php include "fooban.php"; ?>
</body>
</html>
