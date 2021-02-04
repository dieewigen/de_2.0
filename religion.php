<?php
include 'inc/header.inc.php';
include "lib/transaction.lib.php";
include "lib/religion.lib.php";
include 'inc/lang/'.$sv_server_lang.'_religion.lang.php';
include 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, techs, premium, credittransfer FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$credittransfer=$row['credittransfer'];
$premium=$row['premium'];

//owner id/accountalter auslesen
$db_daten=mysql_query("SELECT owner_id, register FROM de_login WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$owner_id=intval($row["owner_id"]);

$accountalter=floor((time()-strtotime($row['register']))/(24*3600*30*6));

$religionspunkte=0;

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?=$religion_lang[religion]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

if($ums_cooperation==0)$btip = $religion_lang['informationen'].'&'.$religion_lang['religiondesc1'].'<br><br>'.$religion_lang['religiondesc'];
else $btip = $religion_lang['informationen'].'&'.$religion_lang['religiondesc1'].'<br><br>'.$religion_lang['premiumaccount'];

//stelle die ressourcenleiste dar
include "resline.php";

//religion deaktiviert?
if($sv_deactivate_religion==1)
{

  echo '<br><div class="info_box text2">Auf diesem Server sind die Bonuspunkte deaktiviert.</div>';

 die('</body></html>');
}

//zur�ck zur letzten seite
if(isset($_REQUEST['from']))
{
	//schwarzmarkt
	if($_REQUEST['from']==1)
	echo '<div class="info_box text1" style="margin-bottom: 5px; font-size: 14px;">Zur&uuml;ck zur <a href="blackmarket.php">vorherigen Seite</a>.</div>';
	
	//allybonus
	if($_REQUEST['from']==2)
	echo '<div class="info_box text1" style="margin-bottom: 5px; font-size: 14px;">Zur&uuml;ck zur <a href="ally_dailygift.php">vorherigen Seite</a>.</div>';
}


//$rangvorbedingungen=array(1,2,3,4,5,10,15,25,50,100);
$creditvorbedingungen=array(1500,4500,7500,15000,30000,45000,75000,105000);

$relpunktevorbedingungen=array(10,20,30,40,50,60,70,80,200,400);

$relrang=get_religion_level($owner_id);

rahmen_oben($religion_lang[religioeseraenge].' <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$btip.'"');

echo '<table width="572" border="0" cellpadding="0" cellspacing="1">
    <tr class="cell" align="center">
    <td><b>'.$religion_lang['rang'].'</b></td>';
	echo '<td><b>'.$religion_lang['benoetigte'].' '.$religion_lang['religionspunkte'].'</b></td><td><b>'.$religion_lang[vorteile].'</b></td>
	</tr>';

for($i=0;$i<count($religion_lang[religionraenge]);$i++)
{
  if($relrang>=$i)$textclass='text3';else $textclass='text2';
  //<img src="'.$ums_gpfad.'g/r/rrang'.$i.'.png"> 
  echo '<tr class="cell '.$textclass.'" align="center">
    <td align="center">'.$religion_lang[religionraenge][$i].' </td>';
  echo '<td>'.number_format($relpunktevorbedingungen[$i-1], 0,"",".").'</td><td>'.$religion_lang[vorteiledesc][$i].'</td>
	</tr>';
}

echo '</table>';

rahmen_unten();



rahmen_oben($religion_lang['religionspunkte']);

 echo '<table width="572" border="0" cellpadding="0" cellspacing="1">
 	<tr><td class="cell1" colspan="4" align="center"><b>'.$religion_lang[geworbenespieler].'</b></td></tr>
    <tr class="cell" align="center">
    <td><b>'.$religion_lang[spielername].'</b></td><td><b>'.$religion_lang[koordinaten].'</b></td><td><b>'.$religion_lang[status].'</b></td><td><b>'.$religion_lang['religionspunkte'].'</b></td>
	</tr>';


//geworbene spieler auslesen
$db_daten=mysql_query("SELECT de_user_data.spielername, de_user_data.sector, de_user_data.system, de_login.status, de_login.register, de_login.last_click FROM de_login LEFT JOIN de_user_data ON(de_login.user_id=de_user_data.user_id) WHERE de_user_data.werberid='$owner_id' AND de_user_data.werberid>0 ORDER BY de_user_data.spielername",$db);
while($row = mysql_fetch_array($db_daten))
{
  //�berpr�fen ob der spieler evtl. inaktiv/noch nicht aktiv ist
  //status auf 1 setzen, erstmal ist damit alles ok
  $status=1;
  
  //testen ob der account in den letzten x tagen aktiv war
  if(strtotime($row[last_click])+5*24*3600<time())$status=3;
  
  //auf urlaubsmodus testen
  if($row[status]==3)$status=4;
  
  //testen ob er aus sektor 1 raus ist
  if($row[sector]==1)$status=6;
  
  //testen ob der account bereits x tage alt ist
  if(strtotime($row[register])+30*24*3600>time())$status=2;

  //auf gesperrt status testen
  if($row[status]==2)$status=5;
  
  
  $statustext=$religion_lang['statustext'.$status];


  echo '<tr class="cell" align="center">
    	<td><a href="details.php?se='.$row[sector].'&sy='.$row[system].'">'.$row[spielername].'</a></td><td>'.$row[sector].':'.$row[system].'</td><td>'.$statustext.'</td>';
  if($status==1){$religionspunkte+=5; echo '<td>5</td>';}else echo '<td>-</td>';
  echo '</tr>'; 
}

echo '<tr><td class="cell1" colspan="4" align="center"><b>'.$religion_lang['transferiertecredits'].'</b></td></tr>';

for($i=0;$i<count($creditvorbedingungen);$i++)
{

  if($credittransfer>=$creditvorbedingungen[$i])$textclass='text3';else $textclass='text2';
  echo '<tr class="cell" align="center"><td colspan="3" class="'.$textclass.'">'.number_format($credittransfer, 0,"",".").' / '.number_format($creditvorbedingungen[$i], 0,"",".").'</td>';
  
  if($credittransfer>=$creditvorbedingungen[$i]){$religionspunkte+=10; echo '<td>10</td>';}else echo '<td>-</td>';
  
  echo '</tr>';
}


echo '<tr><td class="cell1" colspan="4" align="center"><b>'.$religion_lang['bonuspunkte'].'</b></td></tr>';

//premiumaccount beachten
/*
if($premium==1)
{
  $religionspunkte+=10;
  $fc='text3';
}
else 
{
  $fc='text2';
}
*/

/*
echo '<tr class="cell" align="center">
    	<td colspan="3"><font class="'.$fc.'">'.$religion_lang['premiumaccount'].'</font></td>';
if($premium==1){echo '<td>10</td>';}else echo '<td>-</td>';
echo '</tr>';
*/

//accountalter
echo '<tr class="cell" align="center">
    	<td colspan="3"><font>'.$religion_lang['accountalter'].'</font></td>';
if($accountalter>0){$religionspunkte+=$accountalter; echo '<td>'.$accountalter.'</td>';}else echo '<td>-</td>';
echo '</tr>';



echo '<tr class="cell" align="left"><td colspan="4">'.$religion_lang['religionspunkte'].' ('.$religion_lang['gesamt'].'): '.$religionspunkte.'</td></tr>';
echo '</table>';

rahmen_unten();

echo '<div class="bgpic4">';
echo '</div><br>';

?>
</body>
</html>