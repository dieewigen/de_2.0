<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_buildings.lang.php';
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include "functions.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, buildgnr, buildgtime, newtrans, newnews, design1 AS design, efta_user_id FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];
$punkte=$row["score"];$techs=$row["techs"];$buildgnr=$row["buildgnr"];
$verbtime=$row["buildgtime"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];$efta_user_id=$row["efta_user_id"];

$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;


if($_REQUEST["setdesign"])
{
  $design=intval($_REQUEST["setdesign"]);
  mysql_query("UPDATE de_user_data SET design1='$design' WHERE user_id = '$ums_user_id'",$db);	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$buildings_lang[gebaeude]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

if(isset($_REQUEST['ida']))
{
	$t=intval($_REQUEST['ida']);
	if(!isset($_REQUEST['st']))
	{
		@mail($GLOBALS['env_admin_email'], 'Fehlendes Sectoken Gebaeude', $ums_user_id.' '.$ums_nic.' '.$ums_spielername);
		$fehlermsg='<div class="info_box text2">Es ist ein Fehler aufgetreten, versuche es bitte erneut.</div>';
	}
}

if ($t>0 && $buildgnr==0 && $_SESSION['build_sec_token']==$_REQUEST['st']){ //ja, es wurde ein button gedrueckt
	//transaktionsbeginn
	if (setLock($ums_user_id)){

		$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data$ums_rasse WHERE tech_id='$t'",$db);
		$row = mysql_fetch_array($db_daten);
		$benrestyp01=$row[0];$benrestyp02=$row[1];$benrestyp03=$row[2];$benrestyp04=$row[3];$benrestyp05=$row[4];
		$tech_ticks=$row[5];$tech_vor=$row["tech_vor"];
		//schauen obn man ihn bauen darf
		$z1=0;$z2=0;
		$vorb=explode(";",$tech_vor);
		foreach($vorb as $einzelb){ //jede einzelne bedingung checken
			$z1++;
			if ($techs[$einzelb]==1) $z2++;
			if ($einzelb==0) {$z1=0;$z2=0;}
		}
		if ($z1==$z2) $fehlermsg='';//echo "Vorbedingung erf�llt";
		else $fehlermsg='<font color="FF0000">'.$buildings_lang[vorbedingung];

		//schauen ob man es schon hat
		if ($techs[$t]==1) $fehlermsg='<font color="FF0000">'.$buildings_lang[schongebaut];

		//artefaktzentrum, schauen ob man die efta-quest bereits gemacht hat
		//abfrage nur, wenn efta auf dem server auch verf�gbar ist
		/*
		if($sv_deactivate_efta==0)
		{
		  if ($t==28)
		  {
			include "eftadata/lib/efta_dbconnect.php";
			$db_datenx=mysql_query("SELECT user_id FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ=101 AND erledigt=1",$eftadb);
			$numx = mysql_num_rows($db_datenx);
			if ($numx!=1)$errmsg.='<table width=600><tr><td class="ccr">'.$buildings_lang[jalenar].'</td></tr></table>';
		  }
		}*/

		//genug ressourcen vorhanden?
		if ($fehlermsg=='' && $errmsg==''){
			if ($fehlermsg=='' && $restyp01>=$benrestyp01 && $restyp02>=$benrestyp02 && $restyp03>=$benrestyp03 && $restyp04>=$benrestyp04 && $restyp05>=$benrestyp05){
				$restyp01=$restyp01-$benrestyp01;
				$restyp02=$restyp02-$benrestyp02;
				$restyp03=$restyp03-$benrestyp03;
				$restyp04=$restyp04-$benrestyp04;
				$restyp05=$restyp05-$benrestyp05;
				$gr01=$gr01-$restyp01;
				$gr02=$gr02-$restyp02;
				$gr03=$gr03-$restyp03;
				$gr04=$gr04-$restyp04;
				$gr05=$gr05-$restyp05;
				mysql_query("update de_user_data set restyp01 = restyp01 - $gr01,
				 restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
				 restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE user_id = '$ums_user_id'",$db);
				mysql_query("update de_user_data set buildgnr = $t WHERE user_id='$ums_user_id'",$db);
				mysql_query("update de_user_data set buildgtime = $tech_ticks WHERE user_id='$ums_user_id'",$db);
				$buildgnr=$t;
				$verbtime=$tech_ticks;
			}else $errmsg.='<div class="info_box text2">'.$buildings_lang[nichtgenugres].'</div><br>';
		}
		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
		  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		} else{
		  print($buildings_lang[releaselock].$ums_user_id.$buildings_lang[releaselock2]."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$buildings_lang[releaselock3].'</font><br><br>';
}


///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
// einen evtl. laufenden Bauauftrag abbrechen
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['cancel'])){
	//Geb�ude gilt f�r tech_id<40
	$cancel_id=intval($_REQUEST['cancel']);
	if($cancel_id<40 && $cancel_id==$buildgnr){
		//techdaten laden
		$db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id='$cancel_id'",$db);
		$row = mysql_fetch_array($db_daten);
		
		//transaktionsbeginn
		if (setLock($ums_user_id)){
			
			//Info bzgl. Abbruch
			$errmsg='<div class="info_box text3">Der Geb&auml;udebau wurde abgebrochen und die Rohstoffe gutgeschrieben.</div>';
			
			//Anzeige des Geb�udebaues entfernen		
			$buildgnr=0;
			$verbtime=0;

			//den Geb�udebau in der DB canceln

			mysql_query("UPDATE de_user_data SET 
				restyp01 = restyp01 + '".$row['restyp01']."',
				restyp02 = restyp02 + '".$row['restyp02']."',
				restyp03 = restyp03 + '".$row['restyp03']."',
				restyp04 = restyp04 + '".$row['restyp04']."',
				restyp05 = restyp05 + '".$row['restyp05']."',
				buildgnr = 0, buildgtime = 0 WHERE user_id = '$ums_user_id'",$db);
			
			//Rohstoffe f�r die Resline updaten
			$restyp01+=$row['restyp01'];
			$restyp02+=$row['restyp02'];
			$restyp03+=$row['restyp03'];
			$restyp04+=$row['restyp04'];
			$restyp05+=$row['restyp05'];
			
			//transaktionsende
			$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
			if ($erg){
			  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
			} else{
			  print($buildings_lang[releaselock].$ums_user_id.$buildings_lang[releaselock2]."<br><br><br>");
			}
		}// if setlock-ende
		else echo '<br><font color="#FF0000">'.$buildings_lang[releaselock3].'</font><br><br>';
	}
}

//stelle die ressourcenleiste dar
include "resline.php";

echo '
<a href="buildings.php" title="Geb&auml;ude"><img src="'.$ums_gpfad.'g/symbol17.png" border="0" width="64px" heigth="64px"></a> 
<a href="research.php" title="Forschung"><img src="'.$ums_gpfad.'g/symbol18.png" border="0" width="64px" heigth="64px"></a> 
<a href="specialization.php" title="Spezialisierung"><img src="'.$ums_gpfad.'g/symbol16.png" border="0" width="64px" heigth="64px"></a> 
<a href="techtree.php" target="'.$sv_server_tag.'techtree" title="Technologiebaum"><img src="'.$ums_gpfad.'g/symbol14.png" border="0" width="64px" heigth="64px"></a>';


if ($fehlermsg!='')
{
  @mail($GLOBALS['env_admin_email'], 'Gebaeude noch nicht erlaubt.', $ums_user_id.' '.$ums_nic.' '.$ums_spielername);
  echo $fehlermsg.'<br>';
}

if ($errmsg!='')echo $errmsg;

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
// infobox bzgl. aktuell laufendem auftrag
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

if($buildgnr>0){
	//geb�udedaten laden
	$db_daten=mysql_query("SELECT tech_name, tech_ticks, score FROM de_tech_data$ums_rasse WHERE tech_id='$buildgnr'",$db);
	$row = mysql_fetch_array($db_daten);

	//tickzeiten auslesen
	$filename="tickler/runtick.sh";
	$cachefile = fopen ($filename, 'r');
	$wticks=trim(fgets($cachefile, 1024));

	$anzticksprostunde=0;
	for($i=1;$i<=60;$i++)if($wticks[$i]==1)$anzticksprostunde++;

	//anzahl bauticks * 60 / anzahl der ticks pro stunde * 60 sekunden pro minute
	$bauzeitinsekunden=$verbtime*60/$anzticksprostunde*60;

	//letzter tick auslesen und in einen unix timestamp umrechnen
	include 'cache/lastedbtick.tmp';
	$lastticktimestamp=mktime(intval($t1[8].$t1[9]), intval($t1[10].$t1[11]), 0, intval($t1[4].$t1[5]), intval($t1[6].$t1[7]), intval($t1[0].$t1[1].$t1[2].$t1[3]));    

	//endzeitpunkt berechnet 
	$zieldatum=date("G:i d.m.Y", ($lastticktimestamp+$bauzeitinsekunden));

	//�berschrift
	rahmen_oben($buildings_lang[aktuellerauftrag]);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t='.$buildgnr.'" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_'.$buildgnr.'.jpg" border="0"></a></td>

	<td valign="top">&nbsp;'.$buildings_lang[gebaeude].': '.$row["tech_name"].'<br>&nbsp;'.$buildings_lang[punkte].': '.number_format($row["score"], 0,"",".").'<br>&nbsp;'.$buildings_lang[verbleibendezeit].': '.$verbtime.'/'.$row["tech_ticks"].' '.$buildings_lang[wirtschaftsticks]
  .' ('.$buildings_lang[wt].')<br>&nbsp;'.$buildings_lang[fertigstellungszeitpunktechtzeit].': '.$zieldatum.'
	(<span id="counterbox"><span id="counter"></span></span>)
	</td>

	</tr>';
	echo '</table>';
	echo '<script src="jssammlung.js" type="text/javascript"></script>';
	echo '<script type="text/javascript" language="javascript">';
	echo 'counter('.(($lastticktimestamp+$bauzeitinsekunden)-time()).',"counterbox", "counter", "-");';
	echo '</script>';
	rahmen_unten();
}


///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
// ausgabe der geb�udeliste
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

//f�r den auftrag ein sectoken in der session ablegen
$_SESSION['build_sec_token']=mt_rand(1,999);

?>
<form action="buildings.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="250" class="ro"><div class="cellu">&nbsp;<?=$buildings_lang[gebaeude]?>:</div></td>
<td width="50" align="center" class="ro"><div class="cellu">M</div></td>
<td width="50" align="center" class="ro"><div class="cellu">D</div></td>
<td width="50" align="center" class="ro"><div class="cellu">I</div></td>
<td width="50" align="center" class="ro"><div class="cellu">E</div></td>
<td width="30" align="center" class="ro"><div class="cellu">T</div></td>
<td width="20" align="center" class="ro"><div class="cellu"><?=$buildings_lang[dauer]?></div></td>
<td width="70" align="center" class="ro"><div class="cellu"><?=$buildings_lang[status]?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="8" valign="top">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="">
<col width="50">
<col width="50">
<col width="50">
<col width="50">
<col width="25">
<col width="25">
<col width="65">
</colgroup>
<?php

$db_daten=mysql_query("SELECT  tech_id, tech_name, restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data$ums_rasse WHERE tech_id<40 ORDER BY tech_id",$db);
$num = mysql_num_rows($db_daten);

$c1=0;$c2=0;

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

  //schauen ob die sph�re ben�tigt wird
  if(mysql_result($db_daten, $i, "tech_id")==26 AND $sv_max_secmoves==0){$z1=1;$z2=2;}

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
    if($design==1)
    {
      showtech2(mysql_result($db_daten, $i, "tech_name"), mysql_result($db_daten, $i, "tech_id"),
        mysql_result($db_daten, $i, "restyp01"), mysql_result($db_daten, $i, "restyp02"),
        mysql_result($db_daten, $i, "restyp03"), mysql_result($db_daten, $i, "restyp04"),
        mysql_result($db_daten, $i, "restyp05"),
        mysql_result($db_daten, $i, "tech_ticks"), $buildgnr, $techs, 0, $verbtime, $bg, 1);
    }
    else
    {
      showtech(mysql_result($db_daten, $i, "tech_name"), mysql_result($db_daten, $i, "tech_id"),
        mysql_result($db_daten, $i, "restyp01"), mysql_result($db_daten, $i, "restyp02"),
        mysql_result($db_daten, $i, "restyp03"), mysql_result($db_daten, $i, "restyp04"),
        mysql_result($db_daten, $i, "restyp05"),
        mysql_result($db_daten, $i, "tech_ticks"), $buildgnr, $techs, 0, $verbtime, $bg, 1);
    }    
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
</form>
<?php
  //designauswahl
  echo '<div class="cell" style="width: 250px;">';
  if($design==1){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="buildings.php?setdesign=1">'.$buildings_lang[design].' A</a>'.$str2.' - ';
  if($design==2){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="buildings.php?setdesign=2">'.$buildings_lang[design].' B</a>'.$str2.'<br><br>';
  echo '</div>';
  
/*  
  //rahmen oben
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro"><div class="cellu">'.$buildings_lang[information].'</div></td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table width="572">';
  echo '<tr><td class="cell" align="center"><a href="techtree.php" target="'.$sv_server_tag.'techtree">'.$buildings_lang[beschreibung].'</td></tr>';
  echo '</table>';


  //rahmen unten
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';
*/   
?>
<?php include "fooban.php"; ?>
</body>
</html>
