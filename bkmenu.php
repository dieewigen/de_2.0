<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_bkmenu.lang.php';
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/lang/'.$sv_server_lang.'_politics.lang.php';
include 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews FROM de_user_data WHERE user_id=?", 
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
$restyp05=$row["restyp05"];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

//Maximale Tickanzahl auslesen
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT wt AS tick FROM de_system LIMIT 1");
$row = mysqli_fetch_assoc($result);
$maxtick = $row["tick"];

//anzahl der spieler im sektor auslesen
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT COUNT(*) AS wert FROM de_user_data WHERE sector=?",
    [$sector]);
$row = mysqli_fetch_assoc($result);
$spielerimsektor = $row['wert'];

//sektordaten auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, buildgnr, buildgtime, bk, techs, col, ekey FROM de_sector WHERE sec_id=?",
    [$sector]);
$row = mysqli_fetch_assoc($db_daten);
$srestyp01=$row["restyp01"];$srestyp02=$row["restyp02"];$srestyp03=$row["restyp03"];$srestyp04=$row["restyp04"];
$srestyp05=$row["restyp05"];$buildgnr=$row["buildgnr"];$buildgtime=$row["buildgtime"];
$bk=$row["bk"];
$seccol=$row["col"];
$sekey=$row["ekey"];
$techs='sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'.$row["techs"];

//spezialisierung sektorkollektoren
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id FROM de_user_data WHERE sector=? AND spec5=3",
    [$sector]);
$specseccol = mysqli_num_rows($db_daten) * 10;
if($specseccol>100)$specseccol=100;

$seccol=$seccol+$specseccol;


//Kostenfaktor
$avg_player=getAveragePlayerAmountInSectorOnServer();
$kostenfaktor=10-$avg_player;

//ekey aufsplitten
$hv=explode(";",$row["ekey"]);
if ($hv[0]=='') $hv[0]=0;
if ($hv[1]=='') $hv[1]=0;
if ($hv[2]=='') $hv[2]=0;
if ($hv[3]=='') $hv[3]=0;
$keym=$hv[0];$keyd=$hv[1];$keyi=$hv[2];$keye=$hv[3];

//spezialisierung bzgl. der baukostenreduzierung überprüfen
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id FROM de_user_data WHERE sector=? AND spec2=3",
    [$sector]);
$baukostenreduzierung = mysqli_num_rows($db_daten) * 2;
if($baukostenreduzierung>20)$baukostenreduzierung=20;
$baukostenreduzierung=$baukostenreduzierung/100;


//ggf. neuen Verteilungsschlüsssel setzen
$fehlermsg='';
$e_t1=isset($_POST["e_t1"]) ? intval($_POST["e_t1"]) : 0;
$e_t2=isset($_POST["e_t2"]) ? intval($_POST["e_t2"]) : 0;
$e_t3=isset($_POST["e_t3"]) ? intval($_POST["e_t3"]) : 0;
$e_t4=isset($_POST["e_t4"]) ? intval($_POST["e_t4"]) : 0;

if((isset($_POST["e_t1"]) ||isset($_POST["e_t2"]) ||isset($_POST["e_t3"]) || isset($_POST["e_t4"])) && $system==issectorcommander() && ($e_t1 >= 0 && $e_t2 >= 0 && $e_t3 >= 0 && $e_t4 >= 0)){
	if(validDigit($e_t1)&&validDigit($e_t2)&&validDigit($e_t3)&&validDigit($e_t4)){

		$e_t1=(int)$e_t1;$e_t2=(int)$e_t2;$e_t3=(int)$e_t3;$e_t4=(int)$e_t4;
		if (($e_t1+$e_t2+$e_t3+$e_t4)<=100){  //key ist ok und wird aktualisiert
		$newkey=$e_t1.";".$e_t2.";".$e_t3.";".$e_t4;

		//wenn key kleiner als 100 dann warnung ausgeben
		if (($e_t1+$e_t2+$e_t3+$e_t4)<100){
			$fehlermsg=$bkmenu_lang['reswarnung'];
		}else{
			$keym=$e_t1;$keyd=$e_t2;$keyi=$e_t3;$keye=$e_t4;
			$ekey_gespeichert=true;
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_sector SET ekey = ? WHERE sec_id = ?",
				[$newkey, $sector]);
			//keys aktualisieren
		$hv=explode(";",$newkey);
		$keym=$hv[0];$keyd=$hv[1];$keyi=$hv[2];$keye=$hv[3];
		}
		}
		else
		$fehlermsg=$bkmenu_lang['resfehler'];
	}
	if ($keym=='')$keym=0;
	if ($keyd=='')$keyd=0;
	if ($keyi=='')$keyi=0;
	if ($keye=='')$keye=0;
}

//gesamtenergie pro tick, energieausbeute
$eages=$seccol*$sv_kollieertrag;

//energieinput pro rohstoff
$em=ceil($eages/100*$keym);
$ed=ceil($eages/100*$keyd);
$ei=ceil($eages/100*$keyi);
$ee=ceil($eages/100*$keye);

//energie->materie verhaeltnis
if($techs[120]==0)
{
  $emvm=2;
  $emvd=4;
  $emvi=6;
  $emve=8;
}
else 
{
  $emvm=1;
  $emvd=2;
  $emvi=3;
  $emve=4;
}

//rohstoffoutput
$rm=ceil($em/$emvm);
$rd=ceil($ed/$emvd);
$ri=ceil($ei/$emvi);
$re=ceil($ee/$emve);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Basiskommandantenmen&uuml;</title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
//stelle die ressourcenleiste dar

include "resline.php";

if($system!=issectorcommander()){
	echo '<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-fehler">Fehlende Zugriffsrechte: Diese Seite ist nur f&uuml;r den Sektorkommandanten.</div>';
	echo '<a href="politics.php?s=1" class="mod-btn mod-btn-leise">Zur Sektorpolitik</a></div>';

	exit;
}

//Reiter wie in der Sektorpolitik (politics.php)
echo '<div class="mod ally-navi pol-navi">';
echo '<a href="politics.php?s=1" class="ally-reiter">'.$politics_lang["allgemein"].'</a>';
echo '<a href="politics.php?s=2" class="ally-reiter">SK-Politik</a>';
echo '<a href="politics.php?s=3" class="ally-reiter">'.$politics_lang['npc_config_page_btn'].'</a>';
echo '<a href="bkmenu.php" class="ally-reiter ally-reiter-aktiv">SK-Bau/Flotte</a>';
echo '</div>';

//Meldungen der Aktionen, ausgegeben unter den Reitern; $art: ok, fehler, warn
$meldungen = array();
function bk_meldung($text, $art = 'fehler'){
	global $meldungen;
	$meldungen[] = '<div class="mod-meldung mod-meldung-'.$art.'">'.$text.'</div>';
}

function bk_zahl($wert){
	return number_format($wert, 0, ",", ".");
}

function attdef($ownsector, $zsec, $akttyp, $aktzeit){
	global $bkmenu_lang;

	$rz = 0;
  //teste ob die flotte bereit ist befehle zu bekommen
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT aktion, e2 FROM de_sector WHERE sec_id = ?",
		[$ownsector]);
	$row = mysqli_fetch_assoc($db_daten);
	$akt = $row["aktion"];
	$schiffe = $row["e2"];

	// $akt wurde bereits gesetzt
	if ($schiffe==0) bk_meldung($bkmenu_lang['fleeterror1']);
	if ($schiffe>=1 and $akt<>0) bk_meldung($bkmenu_lang['fleeterror2']);
	if ($schiffe>=1 and $akt==0){ //flotte kann befehle bekommen
	  //teste ob die koordinaten ok sind
	  if ($zsec=='')$zsec=0;
	  $zk=$zsec;
	  $ak=$ownsector;
	  if ($zk==$ak) $zsec=0;

	  $db_daten = mysqli_execute_query($GLOBALS['dbi'],
	    "SELECT techs FROM de_sector WHERE sec_id=?",
		[$zsec]);
	  $num = mysqli_num_rows($db_daten);

	  if($num==1)//die koordinaten stimmen
	  {
		//zuerstmal schauen ob das ziel ne srb hat
		$rowx = mysqli_fetch_assoc($db_daten);
		$ztechs = $rowx["techs"];

		if ($ztechs[1]==1) $ok=1;else $ok=0;//wenn srb dann hinflug möglich

		$rz = 0; // Initialisierung der Variable $rz

		if ($ok==1){
			$rz=12;

			//wenn angriff akttyp=1 dann addiere sprungfeldbegrenzer
			if ($akttyp==1 && $rz>0 && $ztechs[2]==1){
        $rz++;
      }

			//nachricht an den account schicken
			//bk rausfinden
	  	$bk=getSKSystemBySecID($zsec);

			//user_id vom bk rausfinden
			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT user_id FROM de_user_data WHERE sector=? and system=?",
				[$zsec, $bk]);
			$numbk = mysqli_num_rows($db_daten);

			if ($numbk!=0){//nachricht an bk schicken
				$row = mysqli_fetch_assoc($db_daten);
				$ge=$schiffe;
				$time=date("YmdHis");
				$uid=$row["user_id"];

			if ($akttyp==1) $freind=$bkmenu_lang['feindliche'];else $freind=$bkmenu_lang['verbuendete'];
				if ($ge==1) $sb=$bkmenu_lang['schiff'];else $sb=$bkmenu_lang['schiffen'];
				$msg=$bkmenu_lang['attmsg1'].' '.$freind.' '.$bkmenu_lang['attmsg2'].' '.$ge.' '.$sb.' '.$bkmenu_lang['attmsg3'].'('.$ak.') '.$bkmenu_lang['reisezeit'].': '.$rz;
				mysqli_execute_query($GLOBALS['dbi'],
					"INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
					[$uid, $time, $msg]);
				mysqli_execute_query($GLOBALS['dbi'],
					"UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
					[$uid]);
			}

			//flotte losschicken
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_sector SET aktion = ?, zeit = ?, gesrzeit = ?, zielsec = ?, aktzeit = ? WHERE sec_id = ?",
				[$akttyp, $rz, $rz, $zsec, $aktzeit, $ownsector]);
			bk_meldung('Die Sektorflotte fliegt '.($akttyp==1 ? 'zum Angriff auf' : 'zur Verteidigung von').' Sektor '.$zsec.', Reisezeit '.$rz.' KT.', 'ok');
		}else bk_meldung($bkmenu_lang['fehlerkeinesrb']);
	  }
	}
	if ($rz==0) bk_meldung($bkmenu_lang['fehlerflottenbefehle']);
}

function recall($ownsector){
	global $bkmenu_lang;
	//erstmal die daten der flotte holen und sichern
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT aktion, zeit, zielsec, e2 FROM de_sector WHERE sec_id = ?",
		[$ownsector]);
	$row = mysqli_fetch_assoc($db_daten);
	$akttyp = $row["aktion"];
	$zsec = $row["zielsec"];
	$e2 = $row["e2"];

	//flotte zurückrufen
	//erstmal schauen, ob man sie überhaupt zurückrufen kann
	if ($akttyp==1 OR $akttyp==2){//also wenn sie hinfliegt
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_sector SET aktion = 3, zeit = gesrzeit-zeit, zielsec = ?, aktzeit = 0 WHERE sec_id = ?",
			[$ownsector, $ownsector]);


		//schon weit weg? wenn nicht, dann status wieder auf defence
		$db_daten1 = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT zeit FROM de_sector WHERE sec_id = ?",
			[$ownsector]);
		$row1 = mysqli_fetch_assoc($db_daten1);
		$zeit = $row1["zeit"];
		if ($zeit==0)//setzte gleich wieder status verteidigen, da noch nicht soweit geflogen
		{
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_sector SET aktion = 0, zielsec = 0 WHERE sec_id = ?",
				[$ownsector]);
		}

		//rückzugsnachricht schreiben
		$time=date("YmdHis");
		//einheiten zählen
		$ge=$e2;

		//bk rausfinden
		$bk=getSKSystemBySecID($zsec);

    //user_id vom bk rausfinden
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT user_id FROM de_user_data WHERE sector=? AND system=?",
			[$zsec, $bk]);
		$numbk = mysqli_num_rows($db_daten);
		//gibt es einen SK?
		if ($numbk!=0){//nachricht an bk schicken
			$row = mysqli_fetch_assoc($db_daten);
			$uid = $row["user_id"];

			if ($akttyp==1) $freind=$bkmenu_lang['feindliche'];else $freind=$bkmenu_lang['verbuendete'];
			if ($ge==1) $sb=$bkmenu_lang['schiff'];else $sb=$bkmenu_lang['schiffen'];
			mysqli_execute_query($GLOBALS['dbi'],
				"INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
				[$uid, $time, $bkmenu_lang['recallmsg1']." $freind ".$bkmenu_lang['recallmsg2']." $ge $sb ".$bkmenu_lang['recallmsg3'].": $ownsector"]);
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
				[$uid]);
		}
		bk_meldung('Die Sektorflotte kehrt heim'.($zeit==0 ? ' und verteidigt wieder den Sektor.' : ', Reisezeit '.$zeit.' KT.'), 'ok');
	}//ende der if ($akttyp==1 OR $akttyp==2)
	else bk_meldung('Die Sektorflotte ist nicht unterwegs.');
}

$befehle=isset($_POST['befehle']) ? $_POST['befehle'] : '';
$zsecf1=intval($_POST['zsecf1'] ?? 1);
//nur die Befehle aus dem Auswahlfeld (0-5)
$af1=max(0, min(5, intval($_POST['af1'] ?? 0)));
if(!empty($befehle)){

  /*
    Aktionen
    0: Verteidigung des Heimatsystems
    1: Angriff auf ein System
    2: Verteidigung eines anderen Systems
    3: Rüchflug ins Heimatsystem
  */
  //fuer jede flotte eigene sektion
  //flotte 1
  switch($af1){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($sector);
      break;
    case 2: //angreifen
      attdef($sector, $zsecf1, 1, 0);
      break;
    default: //verteidigen
      attdef($sector, $zsecf1, 2, $af1-2);
      break;
  }//switch af1 ende
}

$verlegen=$_POST['verlegen'] ?? false;
if ($verlegen){
	$einheiten_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT aktion, e1, e2 FROM de_sector WHERE sec_id=?",
		[$sector]);
	$h=0;
	$b1=intval($_POST['b1']);
	$from=intval($_POST['from1']);
	$to=intval($_POST['to1']);
	if($b1>0){
		$h=$b1;
	}

	if ($h>=1){ //es wurde ein wert eingegeben und er ist ok h=anzahl des auftrags
		$from=intval($from+1);
		$to=intval($to+1);
		$row = mysqli_fetch_assoc($einheiten_daten);
		$ea=$row["e$from"];//schauen wieviele einheiten vorhanden sind
		if ($ea>=$h) $ta=$h;else $ta=$ea;
		$ta=(int)$ta;
		//quellflotte aktualisieren

		//schauen ob beide flotten daheim sind
		$aktion=$row["aktion"];
		if ($aktion==0 && $from!=$to){ //wenn beide null (flotten daheim) verlegen
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_sector SET e$from = e$from - ? WHERE sec_id = ?",
				[$ta, $sector]);
			mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_sector SET e$to = e$to + ? WHERE sec_id = ?",
				[$ta, $sector]);
			bk_meldung(bk_zahl($ta).' '.($ta==1 ? 'Sektorraumschiff' : 'Sektorraumschiffe').' in die '.($to==2 ? $bkmenu_lang['sektorflotte'] : $bkmenu_lang['wachflotte']).' verlegt.', 'ok');
		}elseif ($from==$to){
			bk_meldung('Quelle und Ziel sind dieselbe Flotte.');
		}else{
			bk_meldung($bkmenu_lang['verlegenwarnung']);
		}
	}
}

//sektordaten auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
	"SELECT name, url, bk, e1, e2, aktion, zeit, aktzeit, zielsec FROM de_sector WHERE sec_id=?",
	[$sector]);
$row = mysqli_fetch_assoc($db_daten);
$secname=$row["name"];
$url=$row["url"];
$bk=$row["bk"];
$showe1=$row["e1"];
$showe2=$row["e2"];
$a1=$row["aktion"];
$t1=$row["zeit"];
$at1=$row["aktzeit"];
$zsec1=$row["zielsec"];

//wurde der produzieren-button gedrückt?
$prod=$_POST['prod'] ?? false;
$prodanz=$_POST['prodanz'] ?? 0;
if ($prod)//ja, es wurde ein button gedrueckt
{
  //transaktionsbeginn
  if (setLock($_SESSION['ums_user_id']))
  {
    $prodanz=(int)$prodanz;
    if($techs[122]==1 AND $prodanz>=1)
    {
      //schiffskosten
      $benrestyp01=2000*(1-$baukostenreduzierung);$benrestyp02=500*(1-$baukostenreduzierung);$benrestyp03=500*(1-$baukostenreduzierung);$benrestyp04=2000*(1-$baukostenreduzierung);$benrestyp05=0;$tech_ticks=16;

      //rohstoffe im sektorlager
      $db_daten = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id=?",
        [$sector]);
      $row = mysqli_fetch_assoc($db_daten);
      $srestyp01=$row["restyp01"];$srestyp02=$row["restyp02"];$srestyp03=$row["restyp03"];$srestyp04=$row["restyp04"];
      $srestyp05=$row["restyp05"];
      $gr01=$srestyp01;$gr02=$srestyp02;$gr03=$srestyp03;$gr04=$srestyp04;$gr05=$srestyp05;

      $z=0;
      for ($k=1; $k<=$prodanz; $k++)
      {
        if ($benrestyp01<=$srestyp01 && $benrestyp02<=$srestyp02 &&$benrestyp03<=$srestyp03 &&$benrestyp04<=$srestyp04 &&$benrestyp05<=$srestyp05)
        {
          $srestyp01=$srestyp01-$benrestyp01;
          $srestyp02=$srestyp02-$benrestyp02;
          $srestyp03=$srestyp03-$benrestyp03;
          $srestyp04=$srestyp04-$benrestyp04;
          $srestyp05=$srestyp05-$benrestyp05;
          $z++;
        }
        else break;
      }
      //gibt $z schiffe in auftrag
      $result = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT anzahl FROM de_sector_build WHERE sector_id = ? AND tech_id=1 AND verbzeit=?",
        [$sector, $tech_ticks]);
      $row = mysqli_fetch_assoc($result);
      if ($z>0)
      if (empty($row["anzahl"])) //es gibt keine schiffe mit tech_ticks laenge in der queue
        mysqli_execute_query($GLOBALS['dbi'],
          "INSERT INTO de_sector_build (sector_id, tech_id, anzahl, verbzeit) VALUES (?, 1, ?, ?)",
          [$sector, $z, $tech_ticks]);
      else mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_sector_build SET anzahl = anzahl + ? WHERE sector_id = ? AND tech_id=1 AND verbzeit=?",
        [$z, $sector, $tech_ticks]);

      //aktualisiert die rohstoffe
      $gr01=$gr01-$srestyp01;
      $gr02=$gr02-$srestyp02;
      $gr03=$gr03-$srestyp03;
      $gr04=$gr04-$srestyp04;
      $gr05=$gr05-$srestyp05;
      mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_sector SET
         restyp01 = restyp01 - ?,
         restyp02 = restyp02 - ?,
         restyp03 = restyp03 - ?,
         restyp04 = restyp04 - ?,
         restyp05 = restyp05 - ?
         WHERE sec_id = ?",
        [$gr01, $gr02, $gr03, $gr04, $gr05, $sector]);

      if ($z>0) {
        bk_meldung(bk_zahl($z).' '.($z==1 ? 'Sektorraumschiff' : 'Sektorraumschiffe').' in Auftrag gegeben, Bauzeit '.$tech_ticks.' WT.'.($z<$prodanz ? ' Für mehr reichen die Rohstoffe nicht.' : ''), 'ok');
      } else {
        bk_meldung($bkmenu_lang['nichtgenugres']);
      }
    }

    //transaktionsende
    $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
    if (!$erg)
    {
        bk_meldung("Datensatz Nr. ".$_SESSION['ums_user_id']." konnte nicht entsperrt werden!");
    }
  }// if setlock-ende
  else bk_meldung($bkmenu_lang['transactionactive']);

}//submit ende

//wurde ein Gebäudebaubutton gedrueckt??
if(isset($_REQUEST['ida'])){
	$t=intval($_REQUEST['ida']);
}else{
  $t=-1;
}

////////////////////////////////////////////////////////////////
// Sektorgebäude
////////////////////////////////////////////////////////////////
if ($t>=120 && $buildgnr==0){//ja, es wurde ein button gedrueckt
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id=?",
		[$sector]);
	$row = mysqli_fetch_assoc($db_daten);
	$srestyp01=$row["restyp01"];$srestyp02=$row["restyp02"];$srestyp03=$row["restyp03"];$srestyp04=$row["restyp04"];
	$srestyp05=$row["restyp05"];
	$gr01=$srestyp01;$gr02=$srestyp02;$gr03=$srestyp03;$gr04=$srestyp04;$gr05=$srestyp05;

	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor, tech_name FROM de_tech_data1 WHERE tech_id=?",
		[$t]);
	$row = mysqli_fetch_assoc($db_daten);

	$benrestyp01=floor($row['restyp01']/$kostenfaktor);
	$benrestyp02=floor($row['restyp02']/$kostenfaktor);
	$benrestyp03=floor($row['restyp03']/$kostenfaktor);
	$benrestyp04=floor($row['restyp04']/$kostenfaktor);
	$benrestyp05=floor($row['restyp05']/$kostenfaktor);

	$tech_ticks=$row["tech_ticks"];$tech_vor=$row["tech_vor"];

	//schauen obn man ihn bauen darf
	$z1=0;$z2=0;
	$vorb=explode(";",$tech_vor);
	foreach($vorb as $einzelb) //jede einzelne bedingung checken
	{
		$z1++;
		if ($techs[$einzelb]==1) $z2++;
		if ($einzelb==0) {$z1=0;$z2=0;}
	}
	if ($z1==$z2) $fehlermsg='';//echo "Vorbedingung erfüllt";
	else $fehlermsg=$bkmenu_lang['fehlendevorbedingung'];


	//genug ressourcen vorhanden?
	if ($fehlermsg=='' && $srestyp01>=$benrestyp01 && $srestyp02>=$benrestyp02 && $srestyp03>=$benrestyp03 && $srestyp04>=$benrestyp04 && $srestyp05>=$benrestyp05){
		$srestyp01=$srestyp01-$benrestyp01;
		$srestyp02=$srestyp02-$benrestyp02;
		$srestyp03=$srestyp03-$benrestyp03;
		$srestyp04=$srestyp04-$benrestyp04;
		$srestyp05=$srestyp05-$benrestyp05;
		$gr01=$gr01-$srestyp01;
		$gr02=$gr02-$srestyp02;
		$gr03=$gr03-$srestyp03;
		$gr04=$gr04-$srestyp04;
		$gr05=$gr05-$srestyp05;
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_sector SET
			restyp01 = restyp01 - ?,
			restyp02 = restyp02 - ?,
			restyp03 = restyp03 - ?,
			restyp04 = restyp04 - ?,
			restyp05 = restyp05 - ?
			WHERE sec_id = ?",
			[$gr01, $gr02, $gr03, $gr04, $gr05, $sector]);
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_sector SET buildgnr = ? WHERE sec_id = ?",
			[$t, $sector]);
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_sector SET buildgtime = ? WHERE sec_id = ?",
			[$tech_ticks, $sector]);
		$buildgnr=$t;
		$verbtime=$tech_ticks;
		$buildgtime=$tech_ticks;

		//Nachricht an den Sektor-Chat
		insert_chat_msg($sector, 0, '', 'Folgendes Sektorgeb&auml;ude wurde in Auftrag gegeben: '.$row['tech_name'].'  (BZ: '.$tech_ticks.' WT)');

		//Nachricht in der Sektorstatistik hinterlegen
		mysqli_execute_query($GLOBALS['dbi'],
			"INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, 7, ?, ?)",
			[$maxtick, $sector, $row['tech_name']]);

		bk_meldung('Bau gestartet: '.$row['tech_name'].', Bauzeit '.$tech_ticks.' WT.', 'ok');
  	}elseif ($fehlermsg==''){
		bk_meldung($bkmenu_lang['nichtgenugres']);
	}
}elseif ($t>=120){
	bk_meldung('Es wird bereits ein Sektorgeb&auml;ude gebaut.');
}

//sektorphalanx, nur mit Scannerphalanx (vorher fehlte die Prüfung, ohne Gebäude ließ sich per Formular scannen)
$sc1=$_POST['sc1'] ?? false;
$sc2=$_POST['sc2'] ?? false;
$scansec = isset($_POST['scansec']) ? trim($_POST['scansec']) : null;
$scanbericht = '';
if (($sc1 || $sc2) && $scansec && $techs[124]==1){
  $scansec=(int)$scansec;
  $db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_sector WHERE sec_id=?",
    [$scansec]);

  $num = mysqli_num_rows($db_daten);
  if($num==1)//die koordinaten stimmen, gib die daten aus
  {
    $row = mysqli_fetch_assoc($db_daten);

    if($sc1) //scanlevel 1
    if($srestyp05>=5)
    {
      //schiffe im bau
      $db_daten = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT SUM(anzahl) as anzahl FROM de_sector_build WHERE sector_id=?",
        [$scansec]);
      $row1 = mysqli_fetch_assoc($db_daten);

      //daten des zielsectors ausgeben
      $zgesschiffe=$row["e1"]+$row["e2"];
      $werte = array(
        $bkmenu_lang['sektorkollektoren'] => $row['col'],
        'Multiplex' => $row["restyp01"],
        'Dyharra' => $row["restyp02"],
        'Iradium' => $row["restyp03"],
        'Eternium' => $row["restyp04"],
        'Tronic' => $row["restyp05"],
        $bkmenu_lang['schiffe'] => $zgesschiffe,
        $bkmenu_lang['schiffeimbau'] => $row1["anzahl"] ?? 0,
      );
      $scanbericht .= rahmen_oben($bkmenu_lang['scannerbericht1'].' '.$scansec, false);
      $scanbericht .= '<div class="mod bk"><div class="bk-scan">';
      foreach ($werte as $name => $wert) {
        $scanbericht .= '<div class="ov-wert"><span class="mod-typ">'.$name.'</span><b>'.bk_zahl($wert).'</b></div>';
      }
      $scanbericht .= '</div></div>';
      $scanbericht .= rahmen_unten(false);

      //tronic für die aktion abziehen
      mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_sector SET restyp05 = restyp05 - 5 WHERE sec_id = ?",
        [$sector]);
      $srestyp05=$srestyp05-5;
    }
    else bk_meldung($bkmenu_lang['fehlerzuwenigtronic']);

    if($sc2) //scanlevel 2
    if($srestyp05>=10)
    {
      //Zeile eines Flugs: Ziel, Herkunft, Auftrag, Zeit, Schiffe (Bezeichnungen wie bisher)
      $flug = function ($row, $ziel, $herkunft) use ($bkmenu_lang) {
        $a1=$row["aktion"];
        $t1=$row["zeit"];
        $at1=$row["aktzeit"];
        $art='rueckflug';

        if ($a1==0) $a1=$bkmenu_lang['systemverteidigung'];
        elseif ($a1==1) {$a1=$bkmenu_lang['angriff']; $art='angriff';}
        elseif ($a1==2) {$a1=$bkmenu_lang['verteidigung']; $art='verteidigung';}
        elseif ($a1==3) {$a1=$bkmenu_lang['rueckflug']; $art='rueckflug';}

        if ($a1[0]=='V' && $t1==0) {$a1=$bkmenu_lang['Verteidige'];$t1=$at1;}

        return '<div class="bk-zeile bk-flug"><span>'.$ziel.'</span><span>'.$herkunft.'</span><span><span class="mil-status mil-status-'.$art.'">'.$a1.'</span></span><span class="bk-zahl">'.$t1.' KT</span><span class="bk-zahl"><b>'.bk_zahl($row["e2"]).'</b></span></div>';
      };

      $zeilen = '';
      //flotten die zu dem sektor hinfliegen
      $flotten = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE zielsec = ? AND sec_id <> ?",
        [$scansec, $scansec]);
      while ($row = mysqli_fetch_assoc($flotten))
      {
        $zeilen .= $flug($row, $bkmenu_lang['sektor'].' '.$scansec, $bkmenu_lang['sektor'].' '.$row["sec_id"]);
      }

      //flotten des gescannten sektors
      $flotten = mysqli_execute_query($GLOBALS['dbi'],
        "SELECT zielsec, sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE aktion <> 0 AND sec_id = ?",
        [$scansec]);
      while ($row = mysqli_fetch_assoc($flotten))
      {
        $zeilen .= $flug($row, $bkmenu_lang['sektor'].' '.$row["zielsec"], $bkmenu_lang['sektor'].' '.$scansec);
      }

      $scanbericht .= rahmen_oben($bkmenu_lang['scannerbericht2'].' '.$scansec, false);
      $scanbericht .= '<div class="mod bk">';
      if ($zeilen == '') {
        $scanbericht .= '<div class="mod-leer">Keine Flottenbewegungen.</div>';
      } else {
        $scanbericht .= '<div class="bk-zeile bk-flug bk-kopfzeile"><span>'.$bkmenu_lang['ziel'].'</span><span>'.$bkmenu_lang['herkunft'].'</span><span>'.$bkmenu_lang['aktion'].'</span><span>'.$bkmenu_lang['reisezeit'].'</span><span>'.$bkmenu_lang['schiffe'].'</span></div>';
        $scanbericht .= '<div class="bk-liste">'.$zeilen.'</div>';
      }
      $scanbericht .= '</div>';
      $scanbericht .= rahmen_unten(false);

      //tronic für die aktion abziehen
      mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_sector SET restyp05 = restyp05 - 10 WHERE sec_id = ?",
        [$sector]);
      $srestyp05=$srestyp05-10;
    }
    else bk_meldung($bkmenu_lang['fehlerzuwenigtronic']);
  }
  else bk_meldung($bkmenu_lang['keinedaten']);
}

//Hinweise zum Energieverteilungsschlüssel (oben gesetzt) und fehlende Vorbedingung
if ($fehlermsg!='') bk_meldung($fehlermsg, $fehlermsg==$bkmenu_lang['reswarnung'] ? 'warn' : 'fehler');
if (!empty($ekey_gespeichert)) bk_meldung('Der Energieverteilungsschl&uuml;ssel ist gespeichert.', 'ok');

if (count($meldungen) > 0) {
	echo '<div class="mod pol-meldungen">'.implode('', $meldungen).'</div>';
}
echo $scanbericht;

//Sektorlager
rahmen_oben($bkmenu_lang['sektorlagerbestand']);
echo '<div class="mod bk"><div class="bk-lager">';
foreach (array('Multiplex' => $srestyp01, 'Dyharra' => $srestyp02, 'Iradium' => $srestyp03, 'Eternium' => $srestyp04, 'Tronic' => $srestyp05) as $name => $wert) {
	echo '<div class="ov-wert"><span class="mod-typ">'.$name.'</span><b>'.bk_zahl($wert).'</b></div>';
}
echo '</div></div>';
rahmen_unten();

//Sektorkollektoren: Verteilung der Energie auf die Rohstoffe
rahmen_oben($bkmenu_lang['sektorkollektoren']);
echo '<form action="bkmenu.php" method="post" class="mod bk">';
echo '<div class="bk-kopf"><span><b>'.bk_zahl($seccol).'</b> '.$bkmenu_lang['kollektoren'].'</span><span><b>'.bk_zahl($eages).'</b> Energie je WT</span></div>';
if($techs[120]==0){
	$verhaeltnis = array('2:1', '4:1', '6:1', '8:1');
}else{
	$verhaeltnis = array('1:1', '2:1', '3:1', '4:1');
}
$schluessel = array($keym, $keyd, $keyi, $keye);
$energie = array($em, $ed, $ei, $ee);
$ertrag = array($rm, $rd, $ri, $re);
echo '<div class="bk-energie">';
echo '<span></span><span class="bk-spalte">Multiplex</span><span class="bk-spalte">Dyharra</span><span class="bk-spalte">Iradium</span><span class="bk-spalte">Eternium</span>';
echo '<span class="bk-label">Verteilung</span>';
for ($i = 0; $i < 4; $i++) {
	echo '<span class="bk-prozent"><input type="text" name="e_t'.($i+1).'" value="'.$schluessel[$i].'" maxlength="3" inputmode="numeric" autocomplete="off" class="mod-eingabe bk-schluessel">%</span>';
}
echo '<span class="bk-label">'.$bkmenu_lang['energieinput'].'</span>';
for ($i = 0; $i < 4; $i++) {
	echo '<span class="bk-zahl">'.bk_zahl($energie[$i]).'</span>';
}
echo '<span class="bk-label">'.$bkmenu_lang['umwandlungsverhaeltnis'].'</span>';
for ($i = 0; $i < 4; $i++) {
	echo '<span class="bk-zahl bk-leise">'.$verhaeltnis[$i].'</span>';
}
echo '<span class="bk-label"><b>Ertrag je WT</b></span>';
for ($i = 0; $i < 4; $i++) {
	echo '<span class="bk-zahl"><b>'.bk_zahl($ertrag[$i]).'</b></span>';
}
echo '</div>';
echo '<div class="bk-fuss"><span class="bk-summe">Summe <b id="bk-summe">'.($keym + $keyd + $keyi + $keye).'</b> % von 100 %</span>';
echo '<button type="submit" class="mod-btn">Verteilung speichern</button></div>';
echo '</form>';
rahmen_unten();

//Sektorgebäude
rahmen_oben('Sektorgeb&auml;ude');
echo '<div class="mod bk">';
echo '<div class="bk-zeile bk-geb bk-kopfzeile"><span>'.$bkmenu_lang['gebaeude'].'</span><span>M</span><span>D</span><span>I</span><span>E</span><span>T</span><span>'.$bkmenu_lang['wochen'].'</span><span></span></div>';
echo '<div class="bk-liste">';
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
	"SELECT tech_id, tech_name, restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data1 WHERE tech_id>119 AND tech_id<130 ORDER BY tech_id");
while ($row = mysqli_fetch_assoc($db_daten)) //jeder gefundene datensatz wird geprueft
{
  //zerlege vorbedinguns-string
  $z1=0;$z2=0;
  $tech_vor = $row["tech_vor"];
  $vorb=explode(";",$tech_vor);
  foreach($vorb as $einzelb) //jede einzelne bedingung checken
  {
    $z1++;
    if ($techs[$einzelb]==1) $z2++;
    if ($einzelb==0) {$z1=0;$z2=0;}
  }
  if ($z1==$z2) //Vorbedingung erfüllt
  {
    $kosten = array();
    $bezahlbar = true;
    for ($r = 1; $r <= 5; $r++) {
      $kosten[$r] = $row['restyp0'.$r]/$kostenfaktor;
      //wie beim Bauen: abgerundete Kosten gegen das Sektorlager
      if (${'srestyp0'.$r} < floor($kosten[$r])) $bezahlbar = false;
    }
    $gebnr = $row["tech_id"];
    if ($buildgnr == $gebnr) {
      $status = '<span class="mod-chip bk-imbau">'.$functions['imbau'].' ('.$buildgtime.' WT)</span>';
    } elseif ($techs[$gebnr] == 1) {
      $status = '<span class="mod-chip mod-chip-gruen">'.$functions['gebaut'].'</span>';
    } elseif ($buildgnr > 0) {
      $status = '<span class="bk-leise">ausgelastet</span>';
    } elseif (!$bezahlbar) {
      $status = '<span class="bk-leise">Rohstoffe fehlen</span>';
    } else {
      $status = '<a href="bkmenu.php?ida='.$gebnr.'" class="mod-btn ally-btn-klein" data-bestaetigen="Wirklich bauen?">'.$functions['bauen'].'</a>';
    }
    echo '<div class="bk-zeile bk-geb'.($techs[$gebnr] == 1 ? ' bk-gebaut' : '').'">';
    echo '<span class="bk-name"><a href="help.php?s='.$gebnr.'">'.$row["tech_name"].'</a></span>';
    for ($r = 1; $r <= 5; $r++) {
      echo '<span class="bk-zahl'.($kosten[$r] == 0 ? ' bk-null' : '').'">'.number_format($kosten[$r], 0, "", ".").'</span>';
    }
    echo '<span class="bk-zahl">'.$row["tech_ticks"].'</span>';
    echo '<span class="bk-status">'.$status.'</span>';
    echo '</div>';
  }
}
echo '</div>';
echo '</div>';
rahmen_unten();

if ($techs[122]==1) //raumwerft vorhanden?
{
	//Sektorraumschiffe bauen
	$schiffkosten = array(2000*(1-$baukostenreduzierung), 500*(1-$baukostenreduzierung), 500*(1-$baukostenreduzierung), 2000*(1-$baukostenreduzierung), 0);
	$lager = array($srestyp01, $srestyp02, $srestyp03, $srestyp04, $srestyp05);
	$max = PHP_INT_MAX;
	foreach ($schiffkosten as $i => $k) {
		if ($k > 0) $max = min($max, floor($lager[$i] / $k));
	}

	rahmen_oben('Sektorraumschiffe bauen');
	echo '<form action="bkmenu.php" method="post" class="mod bk">';
	echo '<div class="bk-zeile bk-schiff bk-kopfzeile"><span>'.$bkmenu_lang['einheit'].'</span><span>M</span><span>D</span><span>I</span><span>E</span><span>T</span><span>'.$bkmenu_lang['wochen'].'</span><span>'.$bkmenu_lang['stueck'].'</span></div>';
	echo '<div class="bk-zeile bk-schiff"><span class="bk-name">'.$bkmenu_lang['sektorraumschiff'].'</span>';
	foreach ($schiffkosten as $k) {
		echo '<span class="bk-zahl'.($k == 0 ? ' bk-null' : '').'">'.number_format($k, 0, "", ".").'</span>';
	}
	echo '<span class="bk-zahl">16</span><span class="bk-zahl">'.($showe1+$showe2).'</span></div>';
	echo '<div class="bk-fuss"><span class="bk-leise">Mit dem Sektorlager sind bis zu <b>'.bk_zahl($max).'</b> baubar.</span>';
	echo '<span class="bk-bauen"><input type="text" name="prodanz" value="" maxlength="5" inputmode="numeric" autocomplete="off" placeholder="Anzahl" class="mod-eingabe">';
	echo '<button type="submit" name="prod" value="'.$bkmenu_lang['bauen'].'" class="mod-btn">'.$bkmenu_lang['bauen'].'</button></span></div>';

	//zeige aktive bauaufträge an
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT anzahl, verbzeit FROM de_sector_build WHERE sector_id=? AND tech_id=1 ORDER BY verbzeit ASC",
		[$sector]);
	if (mysqli_num_rows($result) > 0)
	{
		echo '<div class="ally-abschnitt"><div class="mod-typ">'.$bkmenu_lang['aktivebauauftraege'].'</div><div class="bk-liste">';
		while($row = mysqli_fetch_assoc($result))
		{
			echo '<div class="bk-zeile bk-auftrag"><span>'.$bkmenu_lang['sektorraumschiff'].'</span><span class="bk-zahl"><b>'.bk_zahl($row["anzahl"]).'</b> '.$bkmenu_lang['stueck'].'</span><span class="bk-zahl bk-leise">noch '.$row["verbzeit"].' WT</span></div>';
		}
		echo '</div></div>';
	}
	echo '</form>';
	rahmen_unten();

	//Sektorflotte: Aufstellung, Verlegen, Befehle
	if ($a1==0) {
		$auftrag = array('heim', $bkmenu_lang['sektorverteidigung'], '');
	} elseif ($a1==1) {
		$auftrag = array('angriff', $bkmenu_lang['angriff'], 'Sektor '.$zsec1.' &middot; '.$t1.' KT');
	} elseif ($a1==2 && $t1==0) {
		$auftrag = array('verteidigung', $bkmenu_lang['Verteidige'], 'Sektor '.$zsec1.' &middot; noch '.$at1.' KT');
	} elseif ($a1==2) {
		$auftrag = array('verteidigung', $bkmenu_lang['verteidigung'], 'Sektor '.$zsec1.' &middot; '.$t1.' KT');
	} else {
		$auftrag = array('rueckflug', $bkmenu_lang['rueckflug'], $t1.' KT');
	}

	rahmen_oben($bkmenu_lang['flottenaufstellung']);
	echo '<div class="mod bk">';
	echo '<div class="bk-flotten">';
	echo '<div class="ov-wert"><span class="mod-typ">'.$bkmenu_lang['wachflotte'].'</span><b>'.bk_zahl($showe1).'</b><small>bleibt im Sektor</small></div>';
	echo '<div class="ov-wert"><span class="mod-typ">'.$bkmenu_lang['sektorflotte'].'</span><b>'.bk_zahl($showe2).'</b><small><span class="mil-status'.($auftrag[0] != 'heim' ? ' mil-status-'.$auftrag[0] : '').'">'.$auftrag[1].'</span> '.$auftrag[2].'</small></div>';
	echo '</div>';

	//einheiten verlegen
	echo '<form action="bkmenu.php" method="post" class="ally-abschnitt">';
	echo '<div class="mod-typ">'.$bkmenu_lang['einheitenverlegen'].'</div>';
	echo '<div class="bk-formzeile">';
	echo '<input type="text" name="b1" value="" maxlength="9" inputmode="numeric" autocomplete="off" placeholder="Anzahl" class="mod-eingabe bk-anzahl">';
	echo '<label class="bk-wahl"><span>'.$bkmenu_lang['von'].'</span><select name="from1" class="mod-eingabe"><option value="0">'.$bkmenu_lang['wachflotte'].'</option><option value="1">'.$bkmenu_lang['sektorflotte'].'</option></select></label>';
	echo '<label class="bk-wahl"><span>'.$bkmenu_lang['nach'].'</span><select name="to1" class="mod-eingabe"><option value="0">'.$bkmenu_lang['wachflotte'].'</option><option value="1" selected>'.$bkmenu_lang['sektorflotte'].'</option></select></label>';
	echo '<button type="submit" name="verlegen" value="'.$bkmenu_lang['verlegen'].'" class="mod-btn mod-btn-leise">Verlegen</button>';
	echo '</div></form>';

	//flottenbefehle
	echo '<form action="bkmenu.php" method="post" class="ally-abschnitt">';
	echo '<div class="mod-typ">'.$bkmenu_lang['flottenbefehleerteilen'].'</div>';
	echo '<div class="bk-formzeile">';
	echo '<select name="af1" class="mod-eingabe bk-befehl">';
	echo '<option value="0">'.$bkmenu_lang['befehlebeibehalten'].'</option><option value="1">'.$bkmenu_lang['heimkehr'].'</option><option value="2">'.$bkmenu_lang['angreifen'].'</option>';
	echo '<option value="3">Verteidige 1 KT</option><option value="4">Verteidige 2 KT</option><option value="5">Verteidige 3 KT</option>';
	echo '</select>';
	echo '<input type="text" name="zsecf1" value="" maxlength="5" inputmode="numeric" autocomplete="off" placeholder="'.$bkmenu_lang['zielsektor'].'" class="mod-eingabe bk-anzahl">';
	echo '<button type="submit" name="befehle" value="'.$bkmenu_lang['befehleerteilen'].'" class="mod-btn">'.$bkmenu_lang['befehleerteilen'].'</button>';
	echo '</div>';
	echo '<div class="bk-hinweis">Es fliegt nur die Sektorflotte, die Wachflotte bleibt zu Hause. Das Ziel braucht eine Sektorraumbasis. Reisezeit 12 KT, beim Angriff auf einen Sektor mit Sprungfeldbegrenzer 13 KT.</div>';
	echo '</form>';
	echo '</div>';
	rahmen_unten();

}//ende if raumwerft vorhanden

if ($techs[124]==1) //scannerphalanx vorhanden?
{
	rahmen_oben($bkmenu_lang['scannerphalanx']);
	echo '<form action="bkmenu.php" method="post" class="mod bk">';
	echo '<div class="bk-formzeile">';
	echo '<input type="text" name="scansec" value="" maxlength="5" inputmode="numeric" autocomplete="off" placeholder="'.$bkmenu_lang['zielsektor'].'" class="mod-eingabe bk-anzahl">';
	echo '<button type="submit" name="sc1" value=" 1 " class="mod-btn mod-btn-leise">Level 1 &middot; 5 Tronic</button>';
	echo '<button type="submit" name="sc2" value=" 2 " class="mod-btn mod-btn-leise">Level 2 &middot; 10 Tronic</button>';
	echo '</div>';
	echo '<div class="bk-hinweis">Level 1 zeigt Lager, Kollektoren und Schiffe des Sektors, Level 2 seine Flottenbewegungen. Bezahlt wird mit Tronic aus dem Sektorlager.</div>';
	echo '</form>';
	rahmen_unten();
}//scannerphalanx ende

?>
<script>
//Summe des Energieverteilungsschlüssels beim Tippen
document.querySelectorAll('.bk-schluessel').forEach(function(f){
	f.addEventListener('input', function(){
		var s = 0;
		document.querySelectorAll('.bk-schluessel').forEach(function(g){ s += parseInt(g.value, 10) || 0; });
		var z = document.getElementById('bk-summe');
		z.textContent = s;
		z.parentNode.classList.toggle('bk-summe-falsch', s != 100);
	});
});
</script>
</body>
</html>
