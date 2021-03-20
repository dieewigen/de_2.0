<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_research.lang.php';
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include "functions.php";

//pagecounter
//mysql_query("UPDATE de_system set ph5 = ph5 + 1",$db);

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, resnr, restime, newtrans, newnews, design2 AS design FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];
$techs=$row["techs"];$resnr=$row["resnr"];$verbtime=$row["restime"];$newtrans=$row["newtrans"];
$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;

if($_REQUEST["setdesign"])
{
  $design=intval($_REQUEST["setdesign"]);
  mysql_query("UPDATE de_user_data SET design2='$design' WHERE user_id = '$ums_user_id'",$db);	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Forschung</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

if(isset($_REQUEST['ida']))
{
	$t=intval($_REQUEST['ida']);
	if(!isset($_REQUEST['st']))
	{
		@mail($GLOBALS['env_admin_email'], 'Fehlendes Sectoken Forschung', $ums_user_id.' '.$ums_nic.' '.$ums_spielername);
		$fehlermsg='<div class="info_box text2">Es ist ein Fehler aufgetreten, versuche es bitte erneut.</div>';
	}
}

if ($t>0 && $resnr==0 && $_SESSION['research_sec_token']==$_REQUEST['st'])//ja, es wurde ein button gedrueckt
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {


  $db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data$ums_rasse WHERE tech_id='$t'",$db);
  $row = mysql_fetch_array($db_daten);
  $benrestyp01=$row[0];$benrestyp02=$row[1];$benrestyp03=$row[2];$benrestyp04=$row[3];$benrestyp05=$row[4];
  $tech_ticks=$row[5];$tech_vor=$row["tech_vor"];

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
  else $fehlermsg='<font color="FF0000">'.$research_lang[vorbedingung];

  if ($techs[$t]==1) $fehlermsg='<font color="FF0000">'.$research_lang[schonerforscht];

  //genug ressourcen vorhanden?
  if ($fehlermsg=='')
  {
    if ($restyp01>=$benrestyp01 && $restyp02>=$benrestyp02 && $restyp03>=$benrestyp03 && $restyp04>=$benrestyp04 && $restyp05>=$benrestyp05)
    {
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
      mysql_query("update de_user_data set resnr = $t WHERE user_id='$ums_user_id'",$db);
      mysql_query("update de_user_data set restime = $tech_ticks WHERE user_id='$ums_user_id'",$db);
      $resnr=$t;
      $verbtime=$tech_ticks;
    }else $errmsg.='<div class="info_box text2">'.$research_lang[nichtgenugres].'</div><br>';
  }

  //transaktionsende
  $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
  if ($erg)
  {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
  }
  else
  {
        print($research_lang[releaselock].$ums_user_id.$research_lang[releaselock2]."<br><br><br>");
  }
}// if setlock-ende
else echo '<br><font color="#FF0000">'.$research_lang[releaselock3].'</font><br><br>';

}


///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
// einen evtl. laufenden Bauauftrag abbrechen
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
$buildgnr=$resnr;
if(isset($_REQUEST['cancel'])){
	//Geb�ude gilt f�r tech_id<40
	$cancel_id=intval($_REQUEST['cancel']);
	if($cancel_id>39 && $cancel_id<80 && $cancel_id==$buildgnr){
		//techdaten laden
		$db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id='$cancel_id'",$db);
		$row = mysql_fetch_array($db_daten);
		
		//transaktionsbeginn
		if (setLock($ums_user_id)){
			
			//Info bzgl. Abbruch
			$errmsg='<div class="info_box text3">Die Forschung wurde abgebrochen und die Rohstoffe gutgeschrieben.</div>';
			
			//Anzeige des Geb�udebaues entfernen		
			$buildgnr=0;
			$verbtime=0;
			$resnr=0;

			//den Geb�udebau in der DB canceln
			mysql_query("UPDATE de_user_data SET 
				restyp01 = restyp01 + '".$row['restyp01']."',
				restyp02 = restyp02 + '".$row['restyp02']."',
				restyp03 = restyp03 + '".$row['restyp03']."',
				restyp04 = restyp04 + '".$row['restyp04']."',
				restyp05 = restyp05 + '".$row['restyp05']."',
				resnr = 0, restime = 0 WHERE user_id = '$ums_user_id'",$db);
			
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
  @mail($GLOBALS['env_admin_email'], 'Forschung noch nicht erlaubt.', $ums_user_id.' '.$ums_nic.' '.$ums_spielername);
  echo $fehlermsg.'<br>';
}


if ($techs[8]==0)
{
  $techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=8";
  $db_tech=mysql_query($techcheck,$db);
  $row_techcheck = mysql_fetch_array($db_tech);

  //echo $research_lang[eswirdeine].$row_techcheck[tech_name].$research_lang[benoetigt];
  echo '<br>';
  rahmen_oben($research_lang[fehlendesgebaeude]);
  echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr align="left" class="cell">
  <td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=8" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_8.jpg" border="0"></a></td>
  <td valign="top">'.$research_lang[gebaeudeinfo].': '.$row_techcheck[tech_name].'</td>
  </tr>';
  echo '</table>';
  rahmen_unten();
}
else
{
	if ($errmsg!='')echo $errmsg;

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
// infobox bzgl. aktuell laufendem auftrag
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
$buildgnr=$resnr;
if($buildgnr>0)
{
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
  rahmen_oben($research_lang[aktuellerauftrag]);
  echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr align="left" class="cell">
  <td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t='.$buildgnr.'" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_'.$buildgnr.'.jpg" border="0"></a></td>
  
  <td valign="top">&nbsp;'.$research_lang[forschungsprojekt].': '.$row["tech_name"].'<br>&nbsp;'.$research_lang[punkte].': '.number_format($row["score"], 0,"",".").'<br>&nbsp;'.$research_lang[verbleibendezeit].': '.$verbtime.'/'.$row["tech_ticks"].' '.$research_lang[wirtschaftsticks]
.' ('.$research_lang[wt].')<br>&nbsp;'.$research_lang[fertigstellungszeitpunktechtzeit].': '.$zieldatum.'
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
// ausgabe der forschungsliste
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

//f�r den auftrag ein sectoken in der session ablegen
$_SESSION['research_sec_token']=mt_rand(1,999);

?>
<form action="research.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="250" class="ro"><div class="cellu">&nbsp;<?=$research_lang[forschungsprojekt]?>:</div></td>
<td width="50" align="center" class="ro"><div class="cellu">M</div></td>
<td width="50" align="center" class="ro"><div class="cellu">D</div></td>
<td width="50" align="center" class="ro"><div class="cellu">I</div></td>
<td width="50" align="center" class="ro"><div class="cellu">E</div></td>
<td width="30" align="center" class="ro"><div class="cellu">T</div></td>
<td width="20" align="center" class="ro"><div class="cellu"><?=$research_lang[dauer]?></div></td>
<td width="70" align="center" class="ro"><div class="cellu"><?=$research_lang[status]?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="8">
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
$c1=0;$c2=0;
$db_daten=mysql_query("SELECT  tech_id, tech_name, restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data$ums_rasse WHERE tech_id>39 AND tech_id<80",$db);
while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
{
  //zerlege vorbedinguns-string
  $z1=0;$z2=0;
  $vorb=explode(";",$row["tech_vor"]);
  foreach($vorb as $einzelb) //jede einzelne bedingung checken
  {
    $z1++;
    if ($techs[$einzelb]==1) $z2++;
    if ($einzelb==0) {$z1=0;$z2=0;}
  }
  if ($z1==$z2) //echo "Vorbedingung erf�llt";
  {
    if ($c2>4)
    {
      echo '<tr valign="middle" align="center" height="25">';
          echo '<td colspan="8" height="25"></td>';
      echo '</tr>';
      $c2=0;
      $c1=0;
    }
    $c2++;
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
    //showtech($row["tech_name"], $row["tech_id"], $row["restyp01"], $row["restyp02"], $row["restyp03"], $row["restyp04"], $row["restyp05"], $row["tech_ticks"], $resnr, $techs, 1, $verbtime, $bg);
    if($design==1){
      showtech2($row["tech_name"], $row["tech_id"], $row["restyp01"], $row["restyp02"], $row["restyp03"], $row["restyp04"], $row["restyp05"], 
      $row["tech_ticks"], $resnr, $techs, 1, $verbtime, $bg, 1);
    }
    else{
      showtech($row["tech_name"], $row["tech_id"], $row["restyp01"], $row["restyp02"], $row["restyp03"], $row["restyp04"], $row["restyp05"], 
      $row["tech_ticks"], $resnr, $techs, 1, $verbtime, $bg, 1);
    }
  }
}

?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
</form>
<?php
  //designauswahl
  echo '<div class="cell" style="width: 250px;">';
  if($design==1){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="research.php?setdesign=1">'.$research_lang[design].' A</a>'.$str2.' - ';
  if($design==2){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="research.php?setdesign=2">'.$research_lang[design].' B</a>'.$str2.'<br><br>';
  echo '</div>';

  /*
  //rahmen oben
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro"><div class="cellu">'.$research_lang[information].'</div></td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table width="572">';
  echo '<tr><td class="cell" align="center"><a href="techtree.php" target="'.$sv_server_tag.'techtree">'.$research_lang[beschreibung].'</td></tr>';
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
}
?>
<?php include "fooban.php"; ?>
</body>
</html>
