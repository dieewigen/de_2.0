<?php
include_once "soudata/defs/resources.inc.php";

//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

echo '<br>';

rahmen1_oben('<div align="center"><b>Klonmodul</b></div>');

echo '<div class="cell1">';

//klonkapazität auslesen
$db_daten=mysql_query("SELECT MAX(canclone) AS canclone FROM `sou_ship_module` WHERE user_id='$player_user_id' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);
$canclone=$row["canclone"];

//minumwert ist x, daher daraufhin überprüfen
if($canclone<100)$canclone=100;

$clonegrundwert=60000; //entspricht 100% clonerfolg

$cloneabzug=(100-(100*$canclone/$clonegrundwert))/2;

//echo $cloneabzug;

for($i=0;$i<count($r_def);$i++)
{
  $skill=get_skill($i);
  if($skill>0)
  {
    //fähigkeiten abziehen
  }
  change_skill($i, ($skill/100*$cloneabzug)*-1);
  //echo $skill.' - '.$skill/100*$cloneabzug.'<br>';
}

echo 'Dein K&ouml;rper hat seine Schuldigkeit getan und du ben&ouml;tigst einen Neuen. 
Im Klonmodul wurde ein neuer K&ouml;rper geschaffen und deine Erinnerungen und die damit einhergehende F&auml;higkeiten auf diesen &uuml;bertragen.<br><br>
Je besser ein Klonmodul ist, desto weniger Sch&auml;den treten bei diesem Transfer auf.<br>
Klonkapazit&auml;t: '.number_format($canclone, 0,"",".").' ZQ ('.number_format($cloneabzug, 2,",",".").'% Verlust)';

echo '<br><br><a href="sou_main.php?action=systempage"><div class="b1">weiter</div></a><br>';

//alter auf 20 jahre setzen

mysql_query("UPDATE sou_user_data SET playerage=20 WHERE user_id='$player_user_id'",$soudb);


echo '</div>';

rahmen1_unten();

echo '<br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>