<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_server_tick.php" method="post">
<div align="center">
<?php

include "det_userdata.inc.php";

//sicherheitshalber mal die arraya vorbelegen
$wticks="word";$kticks="word";//macht einen string aus der variable
for ($i=0;$i<=59;$i++)$wticks[$i]="0";
for ($i=0;$i<=59;$i++)$kticks[$i]="0";
//echo count($wmins);
//echo $wticks;

if(isset($_REQUEST['settickzeiten'])){
	//zuerst das verzeichnis auslesen wohin man muß
	$filename="../tickler/runtick.sh";
	$cachefile = fopen ($filename, 'r');
	$xticks=trim(fgets($cachefile, 1024));
	$xticks=trim(fgets($cachefile, 1024));
	$wticks = str_replace("#","",$wticks);
	$kticks = str_replace("#","",$kticks);
	$cdpfad=trim(fgets($cachefile, 1024));
	//echo $wticks;
	if ($cachefile) fwrite ($cachefile, $str);
	fclose($cachefile);

	$filename="../tickler/runtick.sh";
	$cachefile = fopen ($filename, 'w');

	//felder in den string einarbeiten
	//var_dump($_REQUEST['wmins']);
	for ($i=0;$i<count($_REQUEST['wmins']);$i++){
		$wticks[$_REQUEST['wmins'][$i]]=1;
		//echo '<br>'.$i.': '.$_REQUEST['wmins'][$i];
	}
	for ($i=0;$i<count($_REQUEST['kmins']);$i++){
		$kticks[$_REQUEST['kmins'][$i]]=1;
	}

	$str='#'.$wticks."\n";
	$str.='#'.$kticks."\n";
	$str.=$cdpfad."\n";
	$str.='minute=`date "+%M"`'."\n";
	//immer user registrieren
	$str.="./register.sh\n";
	//if-abfragen bauen die die ticks starten
	for($i=0;$i<=59;$i++){
		if($i<10)$w="0".$i;else $w=$i;
		if($wticks[$i]=="1" AND $kticks[$i]=="0"){$str.="if [ \$minute = \"$w\" ]; then\n./wt.sh\nfi\n";}
		if($wticks[$i]=="0" AND $kticks[$i]=="1"){$str.="if [ \$minute = \"$w\" ]; then\n./kt.sh\nfi\n";}
		if($wticks[$i]=="1" AND $kticks[$i]=="1"){$str.="if [ \$minute = \"$w\" ]; then\n./kt_wt.sh\nfi\n";}
	}

	if ($cachefile) fwrite ($cachefile, $str);
	fclose($cachefile);
}
?>
<?php

if ($sw)
{
  $result = mysql_query("SELECT doetick, domtick, dodelinactiv, dodeloldtrade, trade_active, winid, winticks FROM de_system",$db);
  $row = mysql_fetch_array($result);
  $doetick=$row["doetick"];
  $domtick=$row["domtick"];
  $trade_active=$row["trade_active"];
  $dodelinactiv=$row["dodelinactiv"];
  $dodeloldtrade=$row["dodeloldtrade"];


  switch($sw){
    case 1: //wirthschaft
      if ($doetick==1)$newwert=0; else $newwert=1;
      mysql_query("UPDATE de_system SET doetick=$newwert",$db);
      break;

    case 2: //milit&auml;r
      if ($domtick==1)$newwert=0; else $newwert=1;
      mysql_query("UPDATE de_system SET domtick=$newwert",$db);
      break;

    case 3: //inaktiv
      if ($dodelinactiv==1)$newwert=0; else $newwert=1;
      mysql_query("UPDATE de_system SET dodelinactiv=$newwert",$db);
      break;
    case 4: //alte handelsangebote
      if ($dodeloldtrade==1)$newwert=0; else $newwert=1;
      mysql_query("UPDATE de_system SET dodeloldtrade=$newwert",$db);
      break;

    case 5: //handel activity
      if ($trade_active==1)$newwert=0; else $newwert=1;
      mysql_query("UPDATE de_system SET trade_active=$newwert",$db);
      break;

    default:
      echo 'Fehler.';
      break;
  }//switch sw ende
}
//tickzeiten auslesen
  $filename="../tickler/runtick.sh";
  $cachefile = fopen ($filename, 'r');
  $wticks=trim(fgets($cachefile, 1024));
  $kticks=trim(fgets($cachefile, 1024));
  $wticks = str_replace("#","",$wticks);
  $kticks = str_replace("#","",$kticks);
  //echo $wticks;
  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);


//trigger auslesen
$result = mysql_query("SELECT lasttick, lastmtick, doetick, domtick, dodelinactiv, dodeloldtrade, trade_active, winid, winticks FROM de_system",$db);
$row = mysql_fetch_array($result);
$lwt=$row["lasttick"];
$lwt=$lwt[0].$lwt[1].$lwt[2].$lwt[3].' - '.$lwt[4].$lwt[5].' - '.$lwt[6].$lwt[7].' - '.$lwt[8].$lwt[9].':'.$lwt[10].$lwt[11];
$lmt=$row["lastmtick"];
$lmt=$lmt[0].$lmt[1].$lmt[2].$lmt[3].' - '.$lmt[4].$lmt[5].' - '.$lmt[6].$lmt[7].' - '.$lmt[8].$lmt[9].':'.$lmt[10].$lmt[11];
$doetick=$row["doetick"];
$domtick=$row["domtick"];
$dodelinactiv=$row["dodelinactiv"];
$dodeloldtrade=$row["dodeloldtrade"];
$trade_active=$row["trade_active"];
$winid=$row["winid"];
$winticks=$row["winticks"];

echo '<br><table cellpadding="3" cellspacing="4">';
echo '<tr>';
echo '<td width="300" align="center">Wirtschaftstick ('.$lwt.')</td>';
if ($doetick==1) $str='Aktiv';else $str='Inaktiv';
echo '<td width="100" align="center"><a href="'.$PHP_SELF.'?sw=1">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Milit&auml;rtick ('.$lmt.')</td>';
if ($domtick==1) $str='Aktiv';else $str='Inaktiv';
echo '<td width="100" align="center"><a href="'.$PHP_SELF.'?sw=2">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Inaktive L&ouml;schen</td>';
if ($dodelinactiv==1) $str='Aktiv';else $str='Inaktiv';
echo '<td width="100" align="center"><a href="'.$PHP_SELF.'?sw=3">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Alte Handelsangebote zur&uuml;ckbuchen</td>';
if ($dodeloldtrade==1) $str='Aktiv';else $str='Inaktiv';
echo '<td width="100" align="center"><a href="'.$PHP_SELF.'?sw=4">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Handel</td>';
if ($trade_active==1) $str='Aktiv';else $str='Inaktiv';
echo '<td width="100" align="center"><a href="'.$PHP_SELF.'?sw=5">'.$str.'</a></td>';
echo '</tr></table>';
//select felder für die ticks generieren
$z=0;
echo '<br><b>Zu welchen Minuten sollen die Wirtschaftsticks ausgeführt werden?<br><br></b>';
echo '<table><tr>';
for ($i=0;$i<5;$i++)
{
  echo '<td valign=top>';
  echo '<select multiple="multiple" size="12" name="wmins[]">';
  for ($j=0;$j<12;$j++)
  {
    if($wticks[$z]==1)$selected='selected';else $selected='';
    echo '<option value="'.$z.'" '.$selected.'>'.$z.'</option>';
    $z++;
  }
  echo '</select></td>';
}
echo '</table>';


$z=0;
echo '<br><b>Zu welchen Minuten sollen die Kampfticks ausgeführt werden?<br><br></b>';
echo '<table><tr>';
for ($i=0;$i<5;$i++)
{
  echo '<td valign=top>';
  echo '<select multiple="multiple" size="12" name="kmins[]">';
  for ($j=0;$j<12;$j++)
  {
    if($kticks[$z]==1)$selected='selected';else $selected='';
    echo '<option value="'.$z.'" '.$selected.'>'.$z.'</option>';

    $z++;
  }
  echo '</select></td>';
}
echo '</table>';


echo '<br>(Mehrfachauswahl mit: Strg+linke Maustaste)<br><br><input type="Submit" name="settickzeiten" value="Tickzeiten setzen">';

?>
</form>
</body>
</html>
