<?php
$disablegzip=1;
//error_reporting(E_ALL);
include "../inccon.php";
include "../inc/sv.inc.php";
include_once("../functions.php");
include "../lib/kampfbericht.lib.php";
include_once("../tickler/kt_einheitendaten.php");



//Schiffspunkte f�r die Kampfbericht-lib
for($rasse=1;$rasse<=5;$rasse++){
	$schiffspunkte[$rasse-1][0]=$unit[$rasse-1][0][4];//j�ger
	$schiffspunkte[$rasse-1][1]=$unit[$rasse-1][1][4];//jagdboot
	$schiffspunkte[$rasse-1][2]=$unit[$rasse-1][2][4];//zerst�rer
	$schiffspunkte[$rasse-1][3]=$unit[$rasse-1][3][4];//kreuzer
	$schiffspunkte[$rasse-1][4]=$unit[$rasse-1][4][4];//schlachtschiff
	$schiffspunkte[$rasse-1][5]=$unit[$rasse-1][5][4];//bomber
	$schiffspunkte[$rasse-1][6]=$unit[$rasse-1][6][4];//transmitterschiff
	$schiffspunkte[$rasse-1][7]=$unit[$rasse-1][7][4];//tr�gerschiff
	$schiffspunkte[$rasse-1][8]=$unit[$rasse-1][8][4];//frachter
	$schiffspunkte[$rasse-1][9]=$unit[$rasse-1][9][4];//titan
	//t�rme
	$schiffspunkte[$rasse-1][10]=$unit[$rasse-1][10][4];
	$schiffspunkte[$rasse-1][11]=$unit[$rasse-1][11][4];
	$schiffspunkte[$rasse-1][12]=$unit[$rasse-1][12][4];
	$schiffspunkte[$rasse-1][13]=$unit[$rasse-1][13][4];
	$schiffspunkte[$rasse-1][14]=$unit[$rasse-1][14][4];
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Info</title>
<?php include "cssinclude.php";?>
<style type="text/css">
<!--
.k1 {font-size:8pt;font-family:Tahoma;color:#3399FF;border-color:606060;background-color:#000000;}
.k2 {font-size:8pt;font-family:Tahoma;color:#3399FF;border-color:606060;background-color:#202020;}
-->
</style>
</head>
<body>
<br><center>
<?php
//$uid=292;
include "det_userdata.inc.php";
if ($uid>0){
  $query="SELECT time, typ, text FROM de_user_news WHERE user_id='$uid' ORDER BY time DESC";
  $th='Alle Nachrichten';
  $db_daten=mysql_query($query,$db);
  echo '<table>';

  $db_daten_sec=mysql_query("SELECT spielername, sector, system, rasse FROM de_user_data WHERE user_id='$uid'");
  $rew = mysql_fetch_array($db_daten_sec);

  while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird ausgegeben
  {
    $t=$row["time"];$n=$row["typ"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];

  if ($n==0) $typ='';
  if ($n==1) $typ='Geb&auml;ude';
  if ($n==2) $typ='Forschung';
  if ($n==3) $typ='Ereignis';
  if ($n==4) $typ='Sonde';
  if ($n==5) $typ='Agent';
  if ($n==6) $typ='Allianz';
  if ($n==7) $typ='Sektorspende';

  if ($n==10) $typ='eingekauft';
  if ($n==11) $typ='verkauft';
  if ($n==12) $typ='zur�ckgebucht';

  if ($n==50) $typ='Kampfbericht';
  if ($n==51) $typ='Angriff';
  if ($n==52) $typ='Angriff zieht ab';
  if ($n==53) $typ='Deff';
  if ($n==54) $typ='Deff zieht ab';
  if ($n==55) $typ='Recycling';


  if ($n!=50 && $n!=57 && $n!=70)//alles au�er kampfbericht
  {
    echo '<tr>';
    echo '<td>'.$hrstr.'<br>'.$typ.'<br><b> '.$time.'</b></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>'.$row["text"].'<br><br></td>';
    echo '</tr>';
  }
  else
  {
  	echo '<tr>';
    echo '<td>'.$hrstr.'<br>'.$typ.'<br><b> '.$time.'</b></td>';
    echo '</tr>';
    echo '<tr>';
	if($n==50){
		echo '<td>'.showkampfberichtV0($row["text"], $rew["rasse"], $rew["spielername"], $rew["sector"], $rew["system"], $schiffspunkte).'</td>';
	}elseif($n==57){
		echo '<td>'.'<br>'.showkampfberichtV1($row["text"], $rew["rasse"], $rew["spielername"], $rew["sector"], $rew["system"], $schiffspunkte).'</td>';
  }
  elseif($n==70){
		echo '<td>'.'<br>'.showkampfberichtBG($row["text"]).'</td>';
  }

    echo '</tr>';
  }


  }
  echo '</table>';
}
else echo 'Kein User ausgew�hlt.';
?>
</form>
</body>
</html>
