<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_bkmenu.lang.php';
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/lang/'.$sv_server_lang.'_politics.lang.php';
include 'functions.php';
include "issectork.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

//Maximale Tickanzahl auslesen
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//anzahl der spieler im sektor auslesen
$result  = mysql_query("SELECT COUNT(*) AS wert FROM de_user_data WHERE sector='$sector'",$db);
$row     = mysql_fetch_array($result);
$spielerimsektor = $row['wert'];

//sektordaten auslesen
$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, buildgnr, buildgtime, bk, techs, col, ekey FROM de_sector WHERE sec_id='$sector'",$db);
$row = mysql_fetch_array($db_daten);
$srestyp01=$row[0];$srestyp02=$row[1];$srestyp03=$row[2];$srestyp04=$row[3];
$srestyp05=$row[4];$buildgnr=$row["buildgnr"];$buildgtime=$row["buildgtime"];
$bk=$row["bk"];
$seccol=$row["col"];
$sekey=$row["ekey"];
$techs='sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'.$row["techs"];

//spezialisierung sektorkollektoren
$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND spec5=3;",$db);
$specseccol = mysql_num_rows($db_daten) * 10;
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
$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND spec2=3;",$db);
$baukostenreduzierung = mysql_num_rows($db_daten) * 2;
if($baukostenreduzierung>20)$baukostenreduzierung=20;
$baukostenreduzierung=$baukostenreduzierung/100;


//ggf. neuen verteilungsschl�sssel setzen
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
			mysql_query("UPDATE de_sector SET ekey = '$newkey' WHERE sec_id = '$sector'",$db);
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
<body>
<?php

//stelle die ressourcenleiste dar

include "resline.php";

echo '<table border="0" cellpadding="0" cellspacing="2" width="600">';
echo '<tr align="center">';
echo '<td><a href="politics.php?s=1" class="btn">'.$politics_lang["allgemein"].'</a></td>';
//echo '<td><a href="secforum.php" class="btn">'.$politics_lang["sektorforum"].'</a></td>';

if($system==issectorcommander()){
	echo '<td><a href="politics.php?s=2" class="btn">SK-Politik</a></td>';
	echo '<td><a href="bkmenu.php" class="btn">SK-Bau/Flotte</a></td>';
}else{

}
 
/*
if($spielerimsektor>1){
    if($system==issectorcommander())echo '<td><a href="politics.php?&s=2" class="btn">'.$politics_lang["skmenu"].'</a></td>';
    elseif($system==$bk)echo '<td><a href="bkmenu.php" class="btn">'.$politics_lang["bkmenu"].'</a></td>';
}
else  //in 1-mann-sektoren kann der sk auch der bk sein 
{
	if($system==issectorcommander())echo '<td><a href="politics.php?&s=2" class="btn">'.$politics_lang["skmenu"].'</a></td>';
    if($system==$bk)echo '<td><a href="bkmenu.php" class="btn">'.$politics_lang["bkmenu"].'</a></td>';
}

*/

echo '</tr>';
echo '</table>';
echo '<br>';


if($system!=issectorcommander()){
	echo '<div class="info_box text2">Fehlende Zugriffsrechte: Diese Seite ist nur f&uuml;r den Sektorkommandanten.</div>';

	exit;
}

echo '<form action="bkmenu.php" method="post">';

function attdef($ownsector, $zsec, $db, $akttyp, $aktzeit){
	global $bkmenu_lang;
	$sector=(int)$sector;
	$zsec=(int)$zsec;
	$akttyp=(int)$akttyp;
	$aktzeit=(int)$aktzeit;
	//teste ob die flotte bereit ist befehle zu bekommen
	$sql="SELECT aktion, e2 FROM de_sector WHERE sec_id = '$ownsector'";
	$db_daten=mysql_query($sql,$db);
	$akt=mysql_result($db_daten, 0, "aktion");
	$schiffe=mysql_result($db_daten, 0, "e2");

	$akt=mysql_result($db_daten, 0, "aktion");
	//echo $schiffe.':'.$akt;
	if ($schiffe==0) echo $bkmenu_lang['fleeterror1'];
	if ($schiffe>=1 and $akt<>0) echo $bkmenu_lang['fleeterror2'];
	if ($schiffe>=1 and $akt==0){ //flotte kann befehle bekommen
	  //teste ob die koordinaten ok sind
	  if ($zsec=='')$zsec=0;
	  $zk=$zsec;
	  $ak=$ownsector;
	  if ($zk==$ak) $zsec=0;

	  $db_daten=mysql_query("SELECT techs FROM de_sector WHERE sec_id=$zsec",$db);
	  $num = mysql_num_rows($db_daten);

	  if($num==1)//die koordinaten stimmen
	  {
		//zuerstmal schauen ob das ziel ne srb hat
		$rowx = mysql_fetch_array($db_daten);
		$ztechs = $rowx["techs"];

		if ($ztechs[1]==1) $ok=1;else $ok=0;//wenn srb dann hinflug m�glich

		if ($ok==1){
			$rz=12;
			//entfernungzuschlag
			//if ($zsec<$ownsector+5 and $zsec>$ownsector-5) $rz=$rz+0;else $rz=$rz+2;

			//wenn angriff akttyp=1 dann addiere sprungfeldbegrenzer
			if ($akttyp==1 && $rz>0 && $ztechs[2]==1){
        $rz++;
      }

			//nachricht an den account schicken
			//bk rausfinden
			//$db_daten=mysql_query("SELECT bk FROM de_sector WHERE sec_id='$zsec'",$db);
      //$bk=mysql_result($db_daten, 0, "bk");
	  	$bk=getSKSystemBySecID($zsec);

			//user_id vom bk rausfinden
			$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zsec' and system='$bk'",$db);
			$numbk = mysql_num_rows($db_daten);

			if ($numbk!=0){//nachricht an bk schicken
				$ge=$schiffe;
				$time=strftime("%Y%m%d%H%M%S");
				$uid=mysql_result($db_daten, 0, "user_id");

			if ($akttyp==1) $freind=$bkmenu_lang['feindliche'];else $freind=$bkmenu_lang['verbuendete'];
				if ($ge==1) $sb=$bkmenu_lang['schiff'];else $sb=$bkmenu_lang['schiffen'];
				$msg=$bkmenu_lang['attmsg1'].' '.$freind.' '.$bkmenu_lang['attmsg2'].' '.$ge.' '.$sb.' '.$bkmenu_lang['attmsg3'].'('.$ak.') '.$bkmenu_lang['reisezeit'].': '.$rz;
				mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 3,'$time','$msg')",$db);
				mysql_query("update de_user_data set newnews = 1 where user_id = $uid",$db);
			}

			//flotte losschicken
			$sql="update de_sector set aktion = $akttyp, zeit = $rz, gesrzeit = $rz, zielsec=$zsec, aktzeit = $aktzeit where sec_id = '$ownsector'";
			mysql_query($sql,$db);
		}else echo $bkmenu_lang['fehlerkeinesrb'].'<br>';
		//return $rz;
	  } //else return 0;
	} //else return 0;
	if ($rz==0) echo $bkmenu_lang['fehlerflottenbefehle'];
}

function recall($ownsector, $db){
	global $bkmenu_lang;
	//erstmal die daten der flotte holen und sichern
	$sql="select aktion, zeit, zielsec, e2 from de_sector where sec_id = '$ownsector'";
	$db_daten=mysql_query($sql,$db);
	$akttyp=mysql_result($db_daten, 0, "aktion");
	$zsec=mysql_result($db_daten, 0, "zielsec");
	$e2=mysql_result($db_daten, 0, "e2");

	//flotte zurückrufen
	//erstmal schauen, ob man sie überhaupt zurückrufen kann
	if ($akttyp==1 OR $akttyp==2){//also wenn sie hinfliegt
		$sql="update de_sector set aktion = 3, zeit = gesrzeit-zeit, zielsec = '$ownsector', aktzeit=0 where sec_id = '$ownsector'";
		mysql_query($sql,$db);


		//schon weit weg? wenn nicht, dann status wieder auf defence
		$sql="select zeit from de_sector where sec_id = '$ownsector'";
		$db_daten1=mysql_query($sql,$db);
		$zeit=mysql_result($db_daten1, 0, "zeit");
		if ($zeit==0)//setzte gleich wieder status verteidigen, da noch nicht soweit geflogen
		{
		$sql="update de_sector set aktion = 0, zielsec = 0 where sec_id = '$ownsector'";
		mysql_query($sql,$db);
		}

		//r�ckzugsnachricht schreiben
		$time=strftime("%Y%m%d%H%M%S");
		//einheiten z�hlen
		$ge=$e2;

		//bk rausfinden
		$bk=getSKSystemBySecID($zsec);

		//$db_daten=mysql_query("SELECT bk FROM de_sector WHERE sec_id='$zsec'",$db);
		//$bk=mysql_result($db_daten, 0, "bk");

		//user_id vom bk rausfinden
		$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zsec' and system='$bk'",$db);
		$numbk = mysql_num_rows($db_daten);
		//gibt es einen SK?
		if ($numbk!=0){//nachricht an bk schicken
			$uid=mysql_result($db_daten, 0, "user_id");

			if ($akttyp==1) $freind=$bkmenu_lang['feindliche'];else $freind=$bkmenu_lang['verbuendete'];
			if ($ge==1) $sb=$bkmenu_lang['schiff'];else $sb=$bkmenu_lang['schiffen'];
			mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 3,'$time','".$bkmenu_lang['recallmsg1']." $freind ".$bkmenu_lang['recallmsg2']." $ge $sb ".$bkmenu_lang['recallmsg3'].": $ownsector')",$db);
			mysql_query("update de_user_data set newnews = 1 where user_id = $uid",$db);
		}
	}//ende der if ($akttyp==1 OR $akttyp==2)
}

$befehle=isset($_POST['befehle']) ? $_POST['befehle'] : '';
$zsecf1=isset($_POST['zsecf1']) ? $_POST['zsecf1'] : 1;
$af1=isset($_POST['af1']) ? $_POST['af1'] : 0;
if(!empty($befehle)){

  /*
    Aktionen
    0: Verteidigung des Heimatsystems
    1: Angriff auf ein System
    2: Verteidigung eines anderen Systems
    3: R�chflug ins Heimatsystem
  */
  //fuer jede flotte eigene sektion
  //flotte 1
  switch($af1){
    case 0: //keine neuen befehle
      break;
    case 1: //heimkehr
      recall($sector, $db);
      break;
    case 2: //angreifen
      attdef($sector, $zsecf1, $db, 1, 0);
      break;
    default: //verteidigen
      attdef($sector, $zsecf1, $db, 2, $af1-2);
      break;
  }//switch af1 ende
}

$verlegen=$_POST['verlegen'];
if ($verlegen){
	$einheiten_daten=mysql_query("SELECT aktion, e1, e2 FROM de_sector WHERE sec_id='$sector'",$db);
	$h=0;
	$b1=intval($_POST['b1']);
	$from=intval($_POST['from1']);
	$to=intval($_POST['to1']);
	if($b1>0){
		$h=$b1;
	}

	/*
	$str="if (\$b$i<>'') \$h= \$b$i;";
	eval($str);
	$str="if (\$b$i<>'') \$from= \$from$i;";
	eval($str);
	$str="if (\$b$i<>'') \$to= \$to$i;";
	eval($str);
	*/
	//echo $str;
	//echo $h.$from.$to.'<br>';
	if ($h>=1){ //es wurde ein wert eingegeben und er ist ok h=anzahl des auftrags
		//echo 'von'.$from.' nach '.$to;
		$from=intval($from+1);
		$to=intval($to+1);
		//echo 'mach was<br>';
		$ea=mysql_result($einheiten_daten, 0, "e$from");//schauen wieviele einheiten vorhanden sind
		if ($ea>=$h) $ta=$h;else $ta=$ea;
		$ta=(int)$ta;
		//quellflotte aktualisieren

		//schauen ob beide flotten daheim sind
		$aktion=mysql_result($einheiten_daten, 0, "aktion");
		if ($aktion==0 && $from!=$to){ //wenn beide null (flotten daheim) verlegen
			mysql_query("update de_sector set e$from = e$from - $ta WHERE sec_id = '$sector'",$db);
			mysql_query("update de_sector set e$to = e$to + $ta WHERE sec_id = '$sector'",$db);
		}else{
			echo $bkmenu_lang['verlegenwarnung'];
		}
	}
}

//sektordaten auslesen
$db_daten=mysql_query("SELECT name, url, bk, e1, e2, aktion, zeit, aktzeit, zielsec FROM de_sector WHERE sec_id=$sector",$db);
$secname=mysql_result($db_daten, 0,0);
$url=mysql_result($db_daten, 0,1);
$bk=mysql_result($db_daten, 0,2);
$showe1=mysql_result($db_daten, 0,3);
$showe2=mysql_result($db_daten, 0,4);
$a1=mysql_result($db_daten, 0,5);
$t1=mysql_result($db_daten, 0,6);
$at1=mysql_result($db_daten, 0,7);
$zsec1=mysql_result($db_daten, 0,8);

//wurde der produzieren-button gedrückt?
$prod=$_POST['prod'];
$prodanz=$_POST['prodanz'];
if ($prod)//ja, es wurde ein button gedrueckt
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    $prodanz=(int)$prodanz;
    if($techs[122]==1 AND $prodanz>=1)
    {
      //schiffskosten
      $benrestyp01=2000*(1-$baukostenreduzierung);$benrestyp02=500*(1-$baukostenreduzierung);$benrestyp03=500*(1-$baukostenreduzierung);$benrestyp04=2000*(1-$baukostenreduzierung);$benrestyp05=0;$tech_ticks=16;

      //rohstoffe im sektorlager
      $db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id='$sector'",$db);
      $row = mysql_fetch_array($db_daten);
      $srestyp01=$row[0];$srestyp02=$row[1];$srestyp03=$row[2];$srestyp04=$row[3];
      $srestyp05=$row[4];
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
      $result = mysql_query("SELECT anzahl FROM de_sector_build WHERE sector_id = '$sector' AND tech_id=1 AND verbzeit=$tech_ticks",$db);
      $row = mysql_fetch_array($result);
      if ($z>0)
      if ($row[0]==0) //es gibt keine schiffe mit tech_ticks laenge in der queue
        mysql_query("INSERT INTO de_sector_build (sector_id, tech_id, anzahl, verbzeit) VALUES ($sector, 1, $z, $tech_ticks)",$db);
      else mysql_query("update de_sector_build set anzahl = anzahl + $z WHERE sector_id = '$sector' AND tech_id=1 AND verbzeit=$tech_ticks ",$db);

      //aktualisiert die rohstoffe
      $gr01=$gr01-$srestyp01;
      $gr02=$gr02-$srestyp02;
      $gr03=$gr03-$srestyp03;
      $gr04=$gr04-$srestyp04;
      $gr05=$gr05-$srestyp05;
      mysql_query("update de_sector set restyp01 = restyp01 - $gr01,
       restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
       restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE sec_id = '$sector'",$db);

    }

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$bkmenu_lang['transactionactive'].'</font><br><br>';

}//submit ende

//wurde ein button bau-gedrueckt??
/*
$t=0;$str='if ($b1)$t=1;';
for ($i=120;$i<=129;$i++) $str = $str."elseif (\$b$i)\$t=$i;";
eval($str);
*/

if(isset($_REQUEST['ida'])){
	$t=intval($_REQUEST['ida']);
}

////////////////////////////////////////////////////////////////
// Sektorgebäude
////////////////////////////////////////////////////////////////
if ($t>=120 && $buildgnr==0){//ja, es wurde ein button gedrueckt
	$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id='$sector'",$db);
	$row = mysql_fetch_array($db_daten);
	$srestyp01=$row[0];$srestyp02=$row[1];$srestyp03=$row[2];$srestyp04=$row[3];
	$srestyp05=$row[4];
	$gr01=$srestyp01;$gr02=$srestyp02;$gr03=$srestyp03;$gr04=$srestyp04;$gr05=$srestyp05;
	
	$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor, tech_name FROM de_tech_data1 WHERE tech_id=$t",$db);
	$row = mysql_fetch_array($db_daten);
	
	$benrestyp01=floor($row[0]/$kostenfaktor);
	$benrestyp02=floor($row[1]/$kostenfaktor);
	$benrestyp03=floor($row[2]/$kostenfaktor);
	$benrestyp04=floor($row[3]/$kostenfaktor);
	$benrestyp05=floor($row[4]/$kostenfaktor);

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
	if ($z1==$z2) $fehlermsg='';//echo "Vorbedingung erf�llt";
	else $fehlermsg='<font color="FF0000">'.$bkmenu_lang['fehlendevorbedingung'];


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
		mysql_query("update de_sector set restyp01 = restyp01 - $gr01, restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03, restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE sec_id = '$sector'",$db);
		mysql_query("update de_sector set buildgnr = $t WHERE sec_id = '$sector'",$db);
		mysql_query("update de_sector set buildgtime = $tech_ticks WHERE sec_id = '$sector'",$db);
		$buildgnr=$t;
		$verbtime=$tech_ticks;
		$buildgtime=$tech_ticks;

		//Nachricht an den Sektor-Chat
		insert_chat_msg($sector, 0, '', 'Folgendes Sektorgeb&auml;ude wurde in Auftrag gegeben: '.$row['tech_name'].'  (BZ: '.$tech_ticks.' WT)');

		//Nachricht in der Sektorstatistik hinterlegen
		$sql="INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', 7, $sector, '".$row['tech_name']."');";
		//echo $sql;
		mysql_query($sql ,$db);


  	}
}

//sektorphalanx
$sc1=$_POST['sc1'];
$sc2=$_POST['sc2'];
$scansec=$_POST['scansec'];
if (($sc1 OR $sc2) AND $scansec){
  $scansec=(int)$scansec;
  $db_daten=mysql_query("SELECT * FROM de_sector WHERE sec_id=$scansec",$db);

  $num = mysql_num_rows($db_daten);
  if($num==1)//die koordinaten stimmen, gib die daten aus
  {
    $row = mysql_fetch_array($db_daten);

    if($sc1) //scanlevel 1
    if($srestyp05>=5)
    {
      //schiffe im bau
      $db_daten=mysql_query("SELECT SUM(anzahl) as anzahl FROM de_sector_build WHERE sector_id='$scansec'",$db);
      $row1 = mysql_fetch_array($db_daten);

      //daten des zielsectors ausgeben
      $zgesschiffe=$row["e1"]+$row["e2"];
      echo '<br><table border="0" cellpadding="0" cellspacing="1" width="400">';
      echo '<tr>';
      echo '<td class="tc" width="100%">'.$bkmenu_lang['scannerbericht1'].' '.$scansec.'</td>';
      echo '</tr>';
      echo '</table>';
      echo '<table border="0" cellpadding="0" cellspacing="1" width="400">';
      echo '<tr>';
      echo '<td class="cc">'.$bkmenu_lang['sektorkollektoren'].'</td>';
      echo '<td class="cc">'.number_format($row[col], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc" width="40%">Multiplex</td>';
      echo '<td class="cc" width="60%">'.number_format($row["restyp01"], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">Dyharra</td>';
      echo '<td class="cc">'.number_format($row["restyp02"], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">Iradium</td>';
      echo '<td class="cc">'.number_format($row["restyp03"], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">Eternium</td>';
      echo '<td class="cc">'.number_format($row["restyp04"], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">Tronic</td>';
      echo '<td class="cc">'.number_format($row["restyp05"], 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">'.$bkmenu_lang['schiffe'].'</td>';
      echo '<td class="cc">'.number_format($zgesschiffe, 0,"",".").'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td class="cc">'.$bkmenu_lang['schiffeimbau'].'</td>';
      echo '<td class="cc">'.number_format($row1["anzahl"], 0,"",".").'</td>';
      echo '</tr>';
      echo '</table>';


      //tronic f�r die aktion abziehen
      mysql_query("update de_sector set restyp05 = restyp05 - 5 WHERE sec_id = '$sector'",$db);
      $srestyp05=$srestyp05-5;
    }
    else echo '<font color="#FF0000">'.$bkmenu_lang['fehlerzuwenigtronic'].'</font>';

    if($sc2) //scanlevel 2
    if($srestyp05>=10)
    {
      function showsecfleetstatus()
      {
      global $scansec, $db, $bkmenu_lang;
      //ankommende sektorflotten anzeigen
      echo '<br><table border="0" cellpadding="0" cellspacing="1" width="600">';
      echo '<tr>';
      echo '<td class="tc" width="100%">'.$bkmenu_lang['scannerbericht2'].' '.$scansec.'</td>';
      echo '</tr>';
      echo '</table>';

      echo '<table border="0" cellpadding="0" cellspacing="1" width="600">';
      echo '<tr align="center">';
      echo '<td class="cc" width="14%">'.$bkmenu_lang['ziel'].'</td>';
      echo '<td class="cc" width="16%">'.$bkmenu_lang['herkunft'].'</td>';
      echo '<td class="cc" width="40%">'.$bkmenu_lang['aktion'].'</td>';
      echo '<td class="cc" width="10%">'.$bkmenu_lang['reisezeit'].'</td>';
      echo '<td class="cc" width="20%">'.$bkmenu_lang['anzahl'].'</td>';
      echo '</tr>';
	  
      //flotten die zu dem sektor hinfliegen
      $flotten=mysql_query("SELECT sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE zielsec = '$scansec' AND sec_id<>'$scansec'",$db);
      $fa = mysql_num_rows($flotten);
      for ($i=0; $i<$fa; $i++)
      {
        //$zsec1=mysql_result($flotten, $i, "zielsec");
        $sec_id=mysql_result($flotten, $i, "sec_id");
        $a1=mysql_result($flotten, $i, "aktion");
        $t1=mysql_result($flotten, $i, "zeit");
        $at1=mysql_result($flotten, $i, "aktzeit");

        if ($a1==0) $a1=$bkmenu_lang['systemverteidigung'];
        elseif ($a1==1) {$a1=$bkmenu_lang['angriff']; $cl='ccr';}
        elseif ($a1==2) {$a1=$bkmenu_lang['verteidigung']; $cl='ccg';}
        elseif ($a1==3) {$a1=$bkmenu_lang['rueckflug']; $cl='cc';}

        if ($a1[0]=='V' && $t1==0) {$a1=$bkmenu_lang['Verteidige'];$t1=$at1;}

        //einheiten z�hlen
        $ge=mysql_result($flotten, $i, "e2");

        echo '<tr>';
        echo '<td class="'.$cl.'">'.$bkmenu_lang['sektor'].'</td>';
        echo '<td class="'.$cl.'">['.$sec_id.']</td>';
        echo '<td class="'.$cl.'">'.$a1.'</td>';
        echo '<td class="'.$cl.'">'.$t1.'</td>';
        echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
        echo '</tr>';
      }

      //flotten des gescannten sektors
      $flotten=mysql_query("SELECT zielsec, sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE aktion<>0 AND sec_id='$scansec'",$db);
      $fa = mysql_num_rows($flotten);
      for ($i=0; $i<$fa; $i++)
      {
        $zsec1=mysql_result($flotten, $i, "zielsec");
        $sec_id=mysql_result($flotten, $i, "sec_id");
        $a1=mysql_result($flotten, $i, "aktion");
        $t1=mysql_result($flotten, $i, "zeit");
        $at1=mysql_result($flotten, $i, "aktzeit");

        if ($a1==0) $a1=$bkmenu_lang['systemverteidigung'];
        elseif ($a1==1) {$a1=$bkmenu_lang['angriff']; $cl='ccr';}
        elseif ($a1==2) {$a1=$bkmenu_lang['verteidigung']; $cl='ccg';}
        elseif ($a1==3) {$a1=$bkmenu_lang['rueckflug']; $cl='cc';}

        if ($a1[0]=='V' && $t1==0) {$a1=$bkmenu_lang['Verteidige'];$t1=$at1;}

        //einheiten z�hlen
        $ge=mysql_result($flotten, $i, "e2");

        echo '<tr>';
        echo '<td class="'.$cl.'">['.$zsec1.']</td>';
        echo '<td class="'.$cl.'">'.$bkmenu_lang['sektor'].'</td>';
        echo '<td class="'.$cl.'">'.$a1.'</td>';
        echo '<td class="'.$cl.'">'.$t1.'</td>';
        echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
        echo '</tr>';

      }

      echo '</table>';
      }
      showsecfleetstatus();
      //tronic f�r die aktion abziehen
      mysql_query("update de_sector set restyp05 = restyp05 - 10 WHERE sec_id = '$sector'",$db);
      $srestyp05=$srestyp05-10;
    }
    else echo '<font color="#FF0000">'.$bkmenu_lang['fehlerzuwenigtronic'].'</font>';
  }
  else echo '<font color="#FF0000">'.$bkmenu_lang['keinedaten'].'</font>';
}

echo '<br>';
//echo '<div class="cellu" style="width: 450px;"><b>'.$bkmenu_lang[bkmenu].'</b></div><br>';

if ($fehlermsg!='')echo '<table width=600><tr><td class="ccr">'.$fehlermsg.'</td></tr></table><br>';

rahmen_oben($bkmenu_lang['sektorlagerbestand']);
?>
<table width="570" border="0" cellpadding="0" cellspacing="1">
<tr>
<td class="tc">Multiplex</td>
<td class="tc">Dyharra</td>
<td class="tc">Iradium</td>
<td class="tc">Eternium</td>
<td class="tc">Tronic</td>
</tr>
<tr align="center">

<?php
$bg='cell1';
echo '<td class="'.$bg.'">'.number_format($srestyp01, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($srestyp02, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($srestyp03, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($srestyp04, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($srestyp05, 0,"",".")."</td>";
echo '</tr></table>';
rahmen_unten();
echo '<br>';

rahmen_oben($bkmenu_lang['sektorkollektoren']);
echo '<table width="570" border="0" cellpadding="0" cellspacing="1">';
//kollektorenergieausbeute
$bg='cell';
echo '<tr height="25"><td width="35%" class="'.$bg.'" align="left">'.$bkmenu_lang['kollektorenergieausbeute'].'</td><td colspan="4" class="'.$bg.'" align="center"> '.number_format($eages, 0,"",".").' ('.$bkmenu_lang['kollektoren'].': '.number_format($seccol, 0,"",".").')</td></tr>';

//energie-materieumwandlung
$bg='cell1';
echo '<tr height="25" align="center"><td width="35%" class="'.$bg.'" align="left"><b>'.$bkmenu_lang['energiematerieumwandlung'].'</b></td>
<td class="'.$bg.'"><b>Multiplex</b></td>
<td class="'.$bg.'"><b>Dyharra</b></td>
<td class="'.$bg.'"><b>Iradium</b></td>
<td class="'.$bg.'"><b>Eternium</b></td>
</tr>';

//energieverteilungssch�ssel
$bg='cell';
echo '<tr height="25" align="center"><td class="'.$bg.'" align="left">'.$bkmenu_lang['energieverteilungsschluessel'].'</td>';
echo '<td class="'.$bg.'"><input type="text" name="e_t1" value="'.$keym.'" size="3" maxlength="3">&nbsp;%</td>';
echo '<td class="'.$bg.'"><input type="text" name="e_t2" value="'.$keyd.'" size="3" maxlength="3">&nbsp;%</td>';
echo '<td class="'.$bg.'"><input type="text" name="e_t3" value="'.$keyi.'" size="3" maxlength="3">&nbsp;%</td>';
echo '<td class="'.$bg.'"><input type="text" name="e_t4" value="'.$keye.'" size="3" maxlength="3">&nbsp;%</td>';
echo '</tr>';

//energieinput
$bg='cell1';
echo '<tr height="25" align="center"><td class="'.$bg.'" align="left">'.$bkmenu_lang['energieinput'].'</td>';
echo '<td class="'.$bg.'">'.number_format($em, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ed, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ei, 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format($ee, 0,"",".")."</td>";
echo '</tr>';

//umwandlungsverh�ltnis
$bg='cell';
if($techs[120]==0)
{
  echo '<tr height="25" align="center"><td class="'.$bg.'" align="left">'.$bkmenu_lang['umwandlungsverhaeltnis'].'</td>';
  echo '<td class="'.$bg.'">2:1</td>';
  echo '<td class="'.$bg.'">4:1</td>';
  echo '<td class="'.$bg.'">6:1</td>';
  echo '<td class="'.$bg.'">8:1</td>';
  echo '</tr>';
}
else
{
  echo '<tr height="25" align="center"><td class="'.$bg.'" align="left">'.$bkmenu_lang['umwandlungsverhaeltnis'].'</td>';
  echo '<td class="'.$bg.'">1:1</td>';
  echo '<td class="'.$bg.'">2:1</td>';
  echo '<td class="'.$bg.'">3:1</td>';
  echo '<td class="'.$bg.'">4:1</td>';
  echo '</tr>';
}

//gesamtertrag
$bg='cell1';
echo '<tr height="25" align="center"><td class="'.$bg.'" align="left"><b>'.$bkmenu_lang['gesamtrohstoffertrag'].'</b></td>';
echo '<td class="'.$bg.'"><b>'.number_format($rm, 0,"",".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($rd, 0,"",".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($ri, 0,"",".")."</b></td>";
echo '<td class="'.$bg.'"><b>'.number_format($re, 0,"",".")."</b></td>";
echo '</tr>';



echo '</table>';
rahmen_unten();


echo '<input type="image" src="'.$ums_gpfad.'g/e.gif" style="width:0; height=0; border:0px;">';
echo '</form>';
echo '<form action="bkmenu.php" method="post">';

?>


<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="159" class="ro"><div class="cellu">&nbsp;<?php echo $bkmenu_lang['gebaeude']?>:</div></td>
<td width="59" align="center" class="ro"><div class="cellu">M</div></td>
<td width="59" align="center" class="ro"><div class="cellu">D</div></td>
<td width="59" align="center" class="ro"><div class="cellu">I</div></td>
<td width="59" align="center" class="ro"><div class="cellu">E</div></td>
<td width="49" align="center" class="ro"><div class="cellu">T</div></td>
<td width="45" align="center" class="ro"><div class="cellu"><?php echo $bkmenu_lang['wochen']?></div></td>
<td width="70" align="center" class="ro"><div class="cellu"><?php echo $bkmenu_lang['status']?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="8">

<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="159">
<col width="59">
<col width="59">
<col width="59">
<col width="59">
<col width="49">
<col width="45">
<col width="70">
</colgroup>
<?php
$bg='cell1';

$c1=0;$c2=0;
$db_daten=mysql_query("SELECT  tech_id, tech_name, restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data1 WHERE tech_id>119 AND tech_id<130 ORDER BY tech_id",$db);
$num = mysql_num_rows($db_daten);

for ($i=0; $i<$num; $i++) //jeder gefundene datensatz wird geprueft
{
  //zerlege vorbedinguns-string
  $z1=0;$z2=0;
  $tech_vor = mysql_result($db_daten, $i, "tech_vor");
  $vorb=explode(";",$tech_vor);
  foreach($vorb as $einzelb) //jede einzelne bedingung checken
  {
    $z1++;
    if ($techs[$einzelb]==1) $z2++;
    if ($einzelb==0) {$z1=0;$z2=0;}
  }
  if ($z1==$z2) //echo "Vorbedingung erf�llt";
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell';
    }
    else
    {
      $c1=0;
      $bg='cell1';
    }
    showtech(mysql_result($db_daten, $i, "tech_name"), mysql_result($db_daten, $i, "tech_id"),
	  mysql_result($db_daten, $i, "restyp01")/$kostenfaktor, 
	  mysql_result($db_daten, $i, "restyp02")/$kostenfaktor,
	  mysql_result($db_daten, $i, "restyp03")/$kostenfaktor, 
	  mysql_result($db_daten, $i, "restyp04")/$kostenfaktor,
      mysql_result($db_daten, $i, "restyp05")/$kostenfaktor,
      mysql_result($db_daten, $i, "tech_ticks"), $buildgnr, $techs, 2, $buildgtime, $bg, 0);
  }
}
//echo "</table>";
?>

</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td colspan="8" class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<?php
if ($techs[122]==1) //raumwerft vorhanden?
{
?>
<br>
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="175" class="ro" align="left"><div class="cellu">&nbsp;&nbsp;<?php echo $bkmenu_lang['einheit']?>:</div></td>
<td width="50" class="ro"><div class="cellu">M</div></td>
<td width="50" class="ro"><div class="cellu">D</div></td>
<td width="50" class="ro"><div class="cellu">I</div></td>
<td width="50" class="ro"><div class="cellu">E</div></td>
<td width="50" class="ro"><div class="cellu">T</div></td>
<td width="45" class="ro"><div class="cellu"><?php echo $bkmenu_lang['wochen']?></div></td>
<td width="45" class="ro"><div class="cellu"><?php echo $bkmenu_lang['stueck']?></div></td>
<td width="35" class="ro"><div class="cellu"><?php echo $bkmenu_lang['bauen']?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="9">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="175">
<col width="50">
<col width="50">
<col width="50">
<col width="50">
<col width="50">
<col width="45">
<col width="45">
<col width="35">
</colgroup>
<?php
echo '<tr valign="middle" align="center" height="25">';
echo '<td class="'.$bg.'" height="25"><div align="left">'.$bkmenu_lang['sektorraumschiff'].'</a></div></td>';
echo '<td class="'.$bg.'">'.number_format(2000*(1-$baukostenreduzierung), 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format(500*(1-$baukostenreduzierung), 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format(500*(1-$baukostenreduzierung), 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format(2000*(1-$baukostenreduzierung), 0,"",".")."</td>";
echo '<td class="'.$bg.'">'.number_format(0, 0,"",".")."</td>";
echo '<td class="'.$bg.'">16</td>';
echo '<td class="'.$bg.'">'.($showe1+$showe2)."</td>";
echo '<td class="'.$bg.'"><input type="text" name="prodanz" value="" size="4" maxlength="5"></td>';
echo "</tr>";
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td colspan="9" class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<?php
echo '<br><input type="Submit" name="prod" value="'.$bkmenu_lang['bauen'].'"><br>';

//zeige aktive bauauftr�ge an
$result=mysql_query("SELECT  anzahl, verbzeit FROM de_sector_build WHERE sector_id=$sector AND tech_id=1 ORDER BY verbzeit ASC",$db);
$num = mysql_num_rows($result);
if ($num>0)
{
echo '<br><table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
echo '<tr>';
echo '<td class="tc" width="100%">'.$bkmenu_lang['aktivebauauftraege'].'</td>';
echo '</tr>';
echo '</table>';
echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
echo '<tr>';
echo '<td class="tc" width="60%">'.$bkmenu_lang['einheit'].'</td>';
echo '<td class="tc" width="20%">'.$bkmenu_lang['stueck'].'</td>';
echo '<td class="tc" width="20%">'.$bkmenu_lang['wochen'].'</td>';
echo '</tr>';
echo '</table>';

while($row = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
echo '<tr>';
echo '<td class="cc" width="60%" align="center">'.$bkmenu_lang['sektorraumschiff'].'</td>';
echo '<td class="cc" width="20%" align="center">'.$row["anzahl"].'</td>';
echo '<td class="cc" width="20%" align="center">'.$row["verbzeit"].'</td>';
echo '</tr>';
echo '</table>';
}
}//ende bauauftr�ge
echo '<br>';

rahmen_oben($bkmenu_lang['flottenaufstellung']);
?>
<table border="0" cellpadding="0" cellspacing="1">
<tr align="center">
<td class="tc">&nbsp;</td>
<td class="tc"><?php echo $bkmenu_lang['wachflotte']?></td>
<td class="tc"><?php echo $bkmenu_lang['sektorflotte']?></td>
</tr>
<?php
echo '<tr>';
echo '<td width="205" class="cc">'.$bkmenu_lang['sektorraumschiff'].'</td>';
echo '<td width="180" class="cc">'.number_format($showe1, 0,"",".").'</td>';
echo '<td width="180" class="cc">'.number_format($showe2, 0,"",".").'</td>';
echo "</tr>";
echo '<tr align="center">';
echo '<td class="cc">Reisezeit</td>';
echo '<td class="cc">&nbsp;</td>';
echo '<td class="cc">12</td>';
echo "</tr>";
/*
echo '<tr align="center">';
echo '<td class="cc"><font color="#FDFB59">'.$bkmenu_lang[nahesektoren].'</td>';
echo '<td class="cc">&nbsp;</td>';
echo '<td class="cc"><font color="#FDFB59">12</td>';
echo "</tr>";
echo '<tr align="center">';
echo '<td class="cc"><font color="#F10505">'.$bkmenu_lang[fernesektoren].'</td>';
echo '<td class="cc">&nbsp;</td>';
echo '<td class="cc"><font color="#F10505">14</td>';
*/
echo "</tr></table>";
echo rahmen_unten();

//einheiten verlegen

rahmen_oben($bkmenu_lang['einheitenverlegen']);
echo '<table border="0" cellpadding="0" cellspacing="1">';
echo '<tr>';
echo '<td width="175" class="tc">&nbsp;</td>';
echo '<td width="110" class="tc">'.$bkmenu_lang['anzahl'].'</td>';
echo '<td width="140" class="tc">'.$bkmenu_lang['von'].'</td>';
echo '<td width="140" class="tc">'.$bkmenu_lang['nach'].'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cc">'.$bkmenu_lang['sektorraumschiff'].'</td>';
echo '<td class="cc"><input type="text" name="b1" value="" size="5" maxlength="9"></td>';
echo '<td class="cc"><select name="from1" size=0><option value=0>'.$bkmenu_lang['wachflotte'].'</option><option value=1>'.$bkmenu_lang['sektorflotte'].'</option></select></td>';
echo '<td class="cc"><select name="to1" size=0><option value=0>'.$bkmenu_lang['wachflotte'].'</option><option value=1>'.$bkmenu_lang['sektorflotte'].'</option></select></td>';
echo '</tr>';
echo '<tr><td align="center" colspan="4"><input type="Submit" name="verlegen" value="'.$bkmenu_lang['verlegen'].'"></td></tr>';
echo '</table>';
rahmen_unten();

//flotten verlegen
rahmen_oben($bkmenu_lang['flottenbefehleerteilen']);
echo '<table border="0" cellpadding="0" cellspacing="1">';
echo '<tr>';

echo '<td width="145" class="tc">'.$bkmenu_lang['flotte'].'</td>';
echo '<td width="150" class="tc">'.$bkmenu_lang['aktuellebefehle'].'</td>';
echo '<td width="170" class="tc">'.$bkmenu_lang['befehl'].'</td>';
echo '<td width="100" class="tc">'.$bkmenu_lang['zielsektor'].'</td>';
echo "</tr>";
echo '<tr>';
//echo '<td class="cc">'.$bkmenu_lang[sflotte].'</td>';
echo '<td class="cc">Sektorflotte</td>';
//rausfinden, was die flotte gerade macht
if ($a1==0) $a1=$bkmenu_lang['sektorverteidigung'];
elseif ($a1==1) $a1=$bkmenu_lang['angriff'].' ('.$zsec1.') '.$bkmenu_lang['reisezeit'].': '.$t1;
elseif ($a1==2) $a1=$bkmenu_lang['verteidigung'].' ('.$zsec1.') '.$bkmenu_lang['reisezeit'].': '.$t1;
elseif ($a1==3) $a1='&nbsp;&nbsp;'.$bkmenu_lang['rueckflug'].'&nbsp;&nbsp; '.$bkmenu_lang['reisezeit'].': '.$t1;

if ($a1[0]=='V' && $t1==0) $a1=$bkmenu_lang['Verteidige'].' ('.$zsec1.') '.$bkmenu_lang['zeit'].': '.$at1;


echo '<td class="cc">'.$a1.'</td>';
echo '<td class="cc"><select name="af1" size=0><option value=0>'.$bkmenu_lang['befehlebeibehalten'].'</option><option value=1>'.$bkmenu_lang['heimkehr'].'</option><option value=2>'.$bkmenu_lang['angreifen'].'</option><option value=3>'.$bkmenu_lang['verteidige1woche'].'</option><option  value=4>'.$bkmenu_lang['verteidige2wochen'].'</option><option  value=5>'.$bkmenu_lang['verteidige3wochen'].'</option></select></td>';
echo '<td class="cc"><input type="text" name="zsecf1" value="" size="3" maxlength="5"></td>';
echo "</tr>";
echo '<tr><td colspan="4" align="center"><input type="Submit" name="befehle" value="'.$bkmenu_lang['befehleerteilen'].'"></td></tr>';
echo '</table>';
rahmen_unten();

}//ende if raumwerft vorhanden

if ($techs[124]==1) //scannerphalanx vorhanden?
{
  echo '<br>';
  rahmen_oben($bkmenu_lang['scannerphalanx']);
?>
<table border="0" cellpadding="0" cellspacing="1">
<tr align="center">
<td width="185" class="cell">Scanlevel:</td>
<td width="120" class="cell"><input type="Submit" name="sc1" value=" 1 "></td>
<td width="120" class="cell"><input type="Submit" name="sc2" value=" 2 "></td>
<td width="140" class="cell"><?php echo $bkmenu_lang['zielsektor']?>: <input type="text" name="scansec" value="" size="3" maxlength="5"></td>
</tr>
<tr align="center">

<td class="cell1"><?php echo $bkmenu_lang['tronickosten']?>:</td>
<td class="cell1">5</td>
<td class="cell1">10</td>
<td class="cell1">&nbsp;</td>
</tr>
</table>
<?php
rahmen_unten();
}//scannerphalanx ende

//echo '<br><div class="cell" style="width: 250px;"><a href="bkmenu.php">'.$bkmenu_lang[aktualisieren].'</a></div><br><br>';

?>
</div>
</form>
</body>
</html>
