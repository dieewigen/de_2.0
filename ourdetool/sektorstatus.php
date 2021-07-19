<?php
include "../inccon.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Info</title>
<?php include "cssinclude.php";?>
</head>
<body>
<br><center>
<?php
include "det_userdata.inc.php";
if ($uid>0)
{
  $query="SELECT sector, system FROM de_user_data WHERE user_id='$uid'";
  $db_daten=mysql_query($query,$db);
  $row = mysql_fetch_array($db_daten);
  $sector=$row["sector"];
  $system=$row["system"];
  echo 'Sektor: '.$sector.'<br>';

  if($recall)//flotte nach daheim verlegen
  {
    mysql_query("UPDATE de_user_fleet set zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0 WHERE
     hsec='$sector' AND hsys='$system'",$db);
    //nachricht an den account schicken
    $time=strftime("%Y%m%d%H%M%S");
    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 2,'$time','Alle Flotten wurden vom DET ins Heimatsystem verlegt.')",$db);
    mysql_query("update de_user_data set newnews = 1 where user_id = $uid",$db);
  }

  //recall-button für alle flotten des spielers
  echo '<form action="sektorstatus.php" method="get">';
  echo '<input type="hidden" name="uid" value="'.$uid.'">';
  echo '<input type="Submit" name="recall" value="Alle Flotten von ('.$sector.':'.$system.') in das Heimatsystem verlegen"><br>';
  echo '</form>';

?>
<h4>Angreifer - Verteidiger</h4>
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="70" class="ro" align="center">Ziel</td>
<td width="80" align="center" class="ro">System</td>
<td width="200" align="center" class="ro">Status</td>
<td width="50" align="center" class="ro">Zeit</td>
<td width="100" align="center" class="ro">Schiffe</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="115">
<col width="45">
<col width="45">
<col width="45">
<col width="45">
</colgroup>
<?php
$flotten=mysql_query("SELECT zielsec, zielsys, aktion, aktzeit, hsec, hsys, zeit, e81, e82, e83, e84, e85, e86, e87 FROM de_user_fleet WHERE zielsec = $sector AND (aktion = 1 OR aktion = 2) ORDER BY zielsys, aktion ASC",$db);
$zsecold=0;$zsysold=0;
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++)
{
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $zsys1=mysql_result($flotten, $i, "zielsys");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");
  $hsec=mysql_result($flotten, $i, "hsec");
  $hsys=mysql_result($flotten, $i, "hsys");

  if ($a1==0) $a1='Systemverteidigung';
  elseif ($a1==1) {$a1='Angriff'; $cl='ccr';}
  elseif ($a1==2) {$a1='Verteidigung'; $cl='ccg';}
  elseif ($a1==3) {$a1='Rückflug'; $cl='cc';}

  if ($a1[0]=='V' && $t1==0) {$a1='Verteidige';$t1=$at1;}

  //einheiten zählen
  $ge=0;
  for ($z=81;$z<=87;$z++)
  {
    $erg=mysql_result($flotten, $i, "e$z");
    $ez[$z-81]=$erg;
    $ge=$ge+$erg;
  }
  if ($zsec1==$zsecold and $zsys1==$zsysold) $sss='&nbsp;';
  else $sss=$zsec1.':'.$zsys1;



  echo '<tr>';
  echo '<td class="'.$cl.'" width="14%">'.$sss.'</td>';
  echo '<td class="'.$cl.'" width="16%">'.$hsec.':'.$hsys.'</td>';
  echo '<td class="'.$cl.'" width="40%">'.$a1.'</td>';
  echo '<td class="'.$cl.'" width="10%">'.$t1.'</td>';
  echo '<td class="'.$cl.'" width="20%">'.number_format($ge, 0,"",".").'</td>';
  echo '</tr>';
  $zsecold=$zsec1;$zsysold=$zsys1;
}
//ankommende sektorflotten anzeigen
$flotten=mysql_query("SELECT sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE zielsec = $sector",$db);
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++)
{
  //$zsec1=mysql_result($flotten, $i, "zielsec");
  $sec_id=mysql_result($flotten, $i, "sec_id");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");

  if ($a1==0) $a1='Systemverteidigung';
  elseif ($a1==1) {$a1='Angriff'; $cl='ccr';}
  elseif ($a1==2) {$a1='Verteidigung'; $cl='ccg';}
  elseif ($a1==3) {$a1='Rückflug'; $cl='cc';}

  if ($a1[0]=='V' && $t1==0) {$a1='Verteidige';$t1=$at1;}

  //einheiten zählen
  $ge=mysql_result($flotten, $i, "e2");

  echo '<tr>';
  echo '<td class="'.$cl.'" width="14%">Sektor</td>';
  echo '<td class="'.$cl.'" width="16%">['.$sec_id.']</td>';
  echo '<td class="'.$cl.'" width="40%">'.$a1.'</td>';
  echo '<td class="'.$cl.'" width="10%">'.$t1.'</td>';
  echo '<td class="'.$cl.'" width="20%">'.number_format($ge, 0,"",".").'</td>';
  echo '</tr>';
}


//echo '</table><br><br>';
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
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>

<?php
//sektorflotten
?>
<h4>Sektorflotten</h4>
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="70" class="ro" align="center">Ziel</td>
<td width="80" align="center" class="ro">System</td>
<td width="200" align="center" class="ro">Status</td>
<td width="50" align="center" class="ro">Zeit</td>
<td width="100" align="center" class="ro">Schiffe</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="115">
<col width="45">
<col width="45">
<col width="45">
<col width="45">
</colgroup>
<?php
$flotten=mysql_query("SELECT zielsec, zielsys, aktion, aktzeit, hsec, hsys, zeit, e81, e82, e83, e84, e85, e86, e87 FROM de_user_fleet WHERE hsec=$sector AND aktion>0 ORDER BY hsys, aktion ASC",$db);
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++)
{
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $zsys1=mysql_result($flotten, $i, "zielsys");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");
  $hsec=mysql_result($flotten, $i, "hsec");
  $hsys=mysql_result($flotten, $i, "hsys");

  if ($a1==0) $a1='Systemverteidigung';
  elseif ($a1==1) {$a1='Angriff'; $cl='ccr';}
  elseif ($a1==2) {$a1='Verteidigung'; $cl='ccg';}
  elseif ($a1==3) {$a1='Rückflug'; $cl='cc';}

  if ($a1[0]=='V' && $t1==0) {$a1='Verteidige';$t1=$at1;}

  //einheiten zählen
  $ge=0;
  for ($z=81;$z<=87;$z++)
  {
    $erg=mysql_result($flotten, $i, "e$z");
    $ez[$z-81]=$erg;
    $ge=$ge+$erg;
  }


  echo '<tr>';
  echo '<td class="'.$cl.'" width="14%">'.$zsec1.':'.$zsys1.'</td>';
  echo '<td class="'.$cl.'" width="16%">'.$hsec.':'.$hsys.'</td>';
  echo '<td class="'.$cl.'" width="40%">'.$a1.'</td>';
  echo '<td class="'.$cl.'" width="10%">'.$t1.'</td>';
  echo '<td class="'.$cl.'" width="20%">'.number_format($ge, 0,"",".").'</td>';
  echo '</tr>';

}
//sektorflotte in bewegung anzeigen
$flotten=mysql_query("SELECT zielsec, sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE aktion<>0 AND sec_id=$sector",$db);
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++)
{
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $sec_id=mysql_result($flotten, $i, "sec_id");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");

  if ($a1==0) $a1='Systemverteidigung';
  elseif ($a1==1) {$a1='Angriff'; $cl='ccr';}
  elseif ($a1==2) {$a1='Verteidigung'; $cl='ccg';}
  elseif ($a1==3) {$a1='Rückflug'; $cl='cc';}

  if ($a1[0]=='V' && $t1==0) {$a1='Verteidige';$t1=$at1;}

  //einheiten zählen
  $ge=mysql_result($flotten, $i, "e2");

  echo '<tr>';
  echo '<td class="'.$cl.'" width="14%">['.$zsec1.']</td>';
  echo '<td class="'.$cl.'" width="16%">Sektor</td>';
  echo '<td class="'.$cl.'" width="40%">'.$a1.'</td>';
  echo '<td class="'.$cl.'" width="10%">'.$t1.'</td>';
  echo '<td class="'.$cl.'" width="20%">'.number_format($ge, 0,"",".").'</td>';
  echo '</tr>';
  $zsecold=$zsec1;$zsysold=$zsys1;
}

//echo '</table>';
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
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>

<?php
}
else echo 'Kein User ausgewählt.';
?>
</form>
</body>
</html>
