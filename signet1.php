<?php
include "inc/header.inc.php";
include("lib/transaction.lib.php");
include "inc/artefakt.inc.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, newtrans, newnews, sector, system FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$techs=$row["techs"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];

include "functions.php";
?>
<!doctype html>
<html>
<head>
<title>Siegel</title>
<?php include "cssinclude.php"; ?>
<style type="text/css">
  div { font-weight: bold; }
  #penta { background-image: url(<?=$ums_gpfad.'g/q/'?>rpenta.gif); width: 390px; height: 355px; }
  #dol { float: left; width: 49%; height: 40px; line-height: 18px; }
  #dor { float: right; width: 49%; height: 40px; line-height: 18px; }
  #clo { clear: both; height: 160px; }
  #dml { float: left; width: 49%; height: 20px; line-height: 20px; text-align: left; }
  #dmr { float: right; width: 49%; height: 20px; line-height: 20px; text-align: right; }
  #clm { clear: both; height: 100px; }
 </style>
</head>
</head>
<body>
<div align="center">
<?php

if ($_POST["itr"] AND $techs[4]!=0)
{

  //transaktionsbeginn
  if (setLock($ums_user_id))
  {

  if(validDigit($itr))//alle werte sind ok
  {

    //hat man auch soviele rohstoffe?
    if ($itr=='') $itr=0;
    if ($itr>$restyp03) $itr=(int)$restyp03;

    if($itr>0)
    {
      mt_srand((double)microtime()*10000);
      $p=mt_rand (20, 35)/100;
      $rasse5res=$itr*$p;
      //rohstofftransfer
      mysql_query("UPDATE de_user_data SET restyp03 = restyp03 - $itr WHERE user_id = '$ums_user_id'",$db);
      mysql_query("UPDATE de_system SET s1res$ums_rasse = s1res$ums_rasse + '$itr', s1res5=s1res5+'$rasse5res'",$db);
      $restyp03=$restyp03-$itr;
    }
  }
  //transaktionsende
  $erg = releaseLock($ums_user_id); //L&ouml;sen des Locks und Ergebnisabfrage
  if ($erg)
  {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
  }
  else
  {
        print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
  }
}// if setlock-ende
else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}

?>
<?//stelle die ressourcenleiste dar
include "resline.php";
if ($fehlermsg!='')echo '<br><font size="2" color="#FF0000"><b>'.$fehlermsg.'</b></font>';

echo '<form action="signet1.php" method="POST">';

if ($techs[4]==0)
{
  $techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=4";
  $db_tech=mysql_query($techcheck,$db);
  $row_techcheck = mysql_fetch_array($db_tech);
  echo "Um auf das Siegel zugreifen zu k�nnen wird ein(e) ".$row_techcheck[tech_name]." ben�tigt.<br><br>";
}
else
{
  $db_daten=mysql_query("SELECT siegel1, s1res1, s1res2, s1res3, s1res4, s1res5, s1history FROM de_system",$db);
  $row = mysql_fetch_array($db_daten);
  $siegel1=$row["siegel1"];
  $res1=$row["s1res1"];
  $res2=$row["s1res2"];
  $res3=$row["s1res3"];
  $res4=$row["s1res4"];
  $res5=$row["s1res5"];
  $history=$row["s1history"];

  //kollektoren der rassen auslesen
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=1",$db);
  $row = mysql_fetch_array($db_daten);
  $col1=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=2",$db);
  $row = mysql_fetch_array($db_daten);
  $col2=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=3",$db);
  $row = mysql_fetch_array($db_daten);
  $col3=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=4",$db);
  $row = mysql_fetch_array($db_daten);
  $col4=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=5",$db);
  $row = mysql_fetch_array($db_daten);
  $col5=$row["sumcol"];

  $gesamtcol=$col1+$col2+$col3+$col4+$col5;

  //schaue welche rasse es erwischt
  $q1=$col1/$gesamtcol;
  $q2=$col2/$gesamtcol;
  $q3=$col3/$gesamtcol;
  $q4=$col4/$gesamtcol;
  $q5=$col5/$gesamtcol;

  $res1=$res1-($res1*$q1);
  $res2=$res2-($res2*$q2);
  $res3=$res3-($res3*$q3);
  $res4=$res4-($res4*$q4);
  $res5=$res5-($res5*$q5);

  $rasse=5;
  if($res4<=$res5)$rasse=4;
  if($res3<=$res5 AND $res3<$res4)$rasse=3;
  if($res2<=$res5 AND $res2<$res4 AND $res2<=$res3)$rasse=2;
  if($res1<=$res5 AND $res1<$res4 AND $res1<=$res3 AND $res1<$res2)$rasse=1;

  $gesamtres=$res1+$res2+$res3+$res4+$res5;
  if($gesamtres==0)$gesamtres=1;
  $p1=$res1/$gesamtres*100;
  $p2=$res2/$gesamtres*100;
  $p3=$res3/$gesamtres*100;
  $p4=$res4/$gesamtres*100;
  $p5=$res5/$gesamtres*100;

  //siegel darstellen

  echo'
  <div id="penta">
  <div id="dol"><br>&nbsp; &nbsp; '.number_format($p1, 2,",",".").'%</div>
  <div id="dor"><br>'.number_format($p2, 2,",",".").'% &nbsp; &nbsp;</div>
  <div id="clo">&nbsp;</div>
  <div id="dml">&nbsp;'.number_format($p3, 2,",",".").'%</div>
  <div id="dmr">'.number_format($p4, 2,",",".").'%&nbsp;</div>
  <div id="clm">&nbsp;</div>
  <div>'.number_format($p5, 2,",",".").'%</div>
  </div>';

  echo 'Unterst&uuml;tze deine Rasse in der Festigung des Siegels mit <input type="text" name="itr" value="" size="8" maxlength="10"> Einheiten Iradium.<br><br>';
  echo '<input type="Submit" name="trans" value="Siegeltransfer"><br><br>';

  $laufzeit=$sv_siegel1[1]-$siegel1;
  echo 'Verbleibende Ticks: '.$laufzeit.'<br><br>';

  if($info==1)
  {
    echo '<table width=600>';
    echo '<tr class="cc"><td>Das Siegel von Basranur bezeichnet ein Sonnensystem in dem durch die Aktivit&auml;t der DX61a23 eine uralte Anlage wieder zum Leben erweckt worden ist, die vermutlich auf die Erbauerrasse zur&uuml;ckgeht, wobei jedoch Modifikationen durchgef&uuml;hrt worden sind. Die Anlage diente vermutlich fr&uuml;her der Verst�rkung von Kollektorenergie, jetzt jedoch verursacht sie nach einer Aufladungsphase eine Schockwelle, welche zu einer Kollabierung und somit zur Zerst&ouml;rung von Kollektoren f&uuml;hrt. Die Anlage orientiert sich an der Tr�gerenergie der Umwandler und da diese bei den Rassen leicht voneinander abweichen, kann die Schockwelle nur immer eine Rasse treffen. Durch die richtige Positionierung von Iradium als St&uuml;tzmasse k&ouml;nnen die Rassen die Frequenz zu ihren Gunsten modifizieren und somit die Gefahr, dass sie die Schockwelle trifft abwenden.</td></tr>';
    echo '</table>';
  }
  else
  {
    echo '<a href="signet1.php?info=1">Siegelinfo</a>';
  }

  if ($history!='')
  echo '<br><br>Bisherige Siegelopfer:<br>'.$history;

  //echo $q1.'<br>'.$q2.'<br>'.$q3.'<br>'.$q4.'<br>'.$q5.'<br>';
/*
0.16517555797484
9546261

0.071441374685872
4129235

0.11781834028472
6807885

0.092635311002888
5350341

0.55292941605168
31947346
*/
}

?>
</div>
</form>
<?php include "fooban.php"; ?>
</body>
</html>
