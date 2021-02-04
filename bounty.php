<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_bounty.lang.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, allytag, status FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row["restyp05"];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$bounty_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php
$se=intval($_REQUEST["se"]);
$sy=intval($_REQUEST["sy"]);
if($se==0)$se='-1';


if ($_POST["mtr"] OR $_POST["dtr"] OR $_POST["itr"] OR $_POST["etr"])
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {

  if(validDigit($mtr) AND validDigit($dtr) AND validDigit($itr) AND validDigit($etr) AND validDigit($ttr))//alle werte sind ok
  {
	//user_id des ziels auslesen
  	$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$se' AND system='$sy'",$db);
    $num = mysql_num_rows($db_daten);
    if($num>=1)
    {
      $row = mysql_fetch_array($db_daten);
      $zieluid=$row["user_id"];
      $db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
      $row = mysql_fetch_array($db_daten);
      $restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];

      //hat man auch soviele rohstoffe?
      if ($mtr=='' OR $mtr<0) $mtr=0;
      if ($dtr=='' OR $dtr<0) $dtr=0;
      if ($itr=='' OR $itr<0) $itr=0;
      if ($etr=='' OR $etr<0) $etr=0;

      if ($mtr>$restyp01) $mtr=(int)$restyp01;
      if ($dtr>$restyp02) $dtr=(int)$restyp02;
      if ($itr>$restyp03) $itr=(int)$restyp03;
      if ($etr>$restyp04) $etr=(int)$restyp04;

      if($mtr>0 OR $dtr>0 OR $itr>0 OR $etr>0)
      {
        //man kann nicht auf sich selbst setzen
        if($se!=$sector OR $sy!=$system)
        {
          //auslesen wieviel maximalwert des kopfgelds ist
  	      $db_daten=mysql_query("SELECT SUM(energiewert) AS energiewert FROM de_user_getcol WHERE user_id='$zieluid' AND zuser_id='$ums_user_id'",$db);
          $row = mysql_fetch_array($db_daten);
          $maxenergiewert=$row["energiewert"];

          //auslesen wieviel kopfgeld bereits durch den spieler auf das ziel ausgesetzt worden ist
  	      $db_daten=mysql_query("SELECT energiewert FROM de_user_setbounty WHERE user_id='$ums_user_id' AND zuser_id='$zieluid'",$db);
          $row = mysql_fetch_array($db_daten);
          $kgausgesetzt=$row["energiewert"];

          //überprüfen ob man soviel setzen darf
          if($maxenergiewert>=$kgausgesetzt+$mtr+$dtr*2+$itr*3+$etr*4)
          {
            //rohstoffe dem spieler abziehen
            mysql_query("UPDATE de_user_data set restyp01 = restyp01 - '$mtr', restyp02 = restyp02 - '$dtr',
            restyp03 = restyp03 - '$itr', restyp04 = restyp04 - '$etr' WHERE user_id = '$ums_user_id'",$db);

            //kopfgeld bei dem spieler hinterlegen
            mysql_query("UPDATE de_user_data SET kg01 = kg01 + '$mtr', kg02 = kg02 + '$dtr',
            kg03 = kg03 + '$itr', kg04 = kg04 + '$etr' WHERE user_id = '$zieluid'",$db);

            //gesetztes kopfgeld speichern, damit man nicht zuviel setzen kann
            $result = mysql_query("SELECT energiewert FROM de_user_setbounty WHERE user_id='$ums_user_id' AND zuser_id='$zieluid'",$db);
            $num = mysql_num_rows($result);
            $kgneugesetzt=$mtr+$dtr*2+$itr*3+$etr*4;
            if($num==1)
              mysql_query("UPDATE de_user_setbounty SET energiewert=energiewert+'$kgneugesetzt' WHERE user_id='$ums_user_id' AND zuser_id='$zieluid'",$db);
            else mysql_query("INSERT INTO de_user_setbounty (user_id, zuser_id, energiewert) VALUES ('$ums_user_id', '$zieluid', '$kgneugesetzt')",$db);


            //nachricht an den account schicken, dass kopfgeld auf ihn ausgesetzt wurde
            $time=strftime("%Y%m%d%H%M%S");
            $nachricht=$ums_spielername.' '.$bounty_lang[msg_1].': '.number_format($mtr, 0,"",".").' '.$bounty_lang[m].' -- '.number_format($dtr, 0,"",".").' '.$bounty_lang[d].' -- '.number_format($itr, 0,"",".").' '.$bounty_lang[i].' -- '.number_format($etr, 0,"",".").' '.$bounty_lang[e];
            mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$zieluid', 3,'$time','$nachricht')",$db);
          	mysql_query("UPDATE de_user_data SET newnews = 1 WHERE user_id = '$zieluid'",$db);

          	//statusmeldung generieren
          	$errmsg.='<table width=600><tr><td class="ccg">'.$bounty_lang[msg_2].'.</td></tr></table>';

          	//rohstoffanzeige korrigieren
          	$restyp01=$restyp01-$mtr;
          	$restyp02=$restyp02-$dtr;
          	$restyp03=$restyp03-$itr;
          	$restyp04=$restyp04-$etr;
          }else $errmsg.='<table width=600><tr><td class="ccr">'.$bounty_lang[msg_3].'.</td></tr></table>';
        }else $errmsg.='<table width=600><tr><td class="ccr">'.$bounty_lang[msg_4].'.</td></tr></table>';
      }
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
        print($resource_lang[releaselock].$ums_user_id.$resource_lang[releaselock2]."<br><br><br>");
  }
}// if setlock-ende
else echo '<br><font color="#FF0000">'.$resource_lang[releaselock3].'</font><br><br>';

}


//stelle die ressourcenleiste dar
include "resline.php";

if ($errmsg!='')echo $errmsg;

echo '<form action="bounty.php" method="POST">';

rahmen_oben($bounty_lang[welcome]);

$db_daten=mysql_query("SELECT user_id, spielername, kg01, kg02, kg03, kg04 FROM de_user_data WHERE sector='$se' AND system='$sy'",$db);
$num = mysql_num_rows($db_daten);
if($num>=1)
{
  $row = mysql_fetch_array($db_daten);
  $spielername=$row["spielername"];
  $zieluid=$row["user_id"];

  $kg[0]=$row["kg01"];
  $kg[1]=$row["kg02"];
  $kg[2]=$row["kg03"];
  $kg[3]=$row["kg04"];
  $gesamtwert=$kg[0]+$kg[1]*2+$kg[2]*3+$kg[3]*4;

  echo '<table width="565" border="0" cellpadding="0" cellspacing="1">';
  $bg='cell';
  echo '<tr>';
  echo '<td width="30%" class="'.$bg.'">'.$bounty_lang[spielername].'</td>';
  echo '<td align="center" width="70%" class="'.$bg.'">'.$spielername.'</td>';
  echo '</tr>';

  $bg='cell1';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[koordinaten].'</td>';
  echo '<td align="center" class="'.$bg.'">'.$se.':'.$sy.'</td>';
  echo '</tr>';

  $bg='cell';
  echo '<tr>';
  echo '<td colspan="2" align="center" class="'.$bg.'">'.$bounty_lang[aktuellerkopfgeldstand].'</td>';
  echo '</tr>';

  $bg='cell1';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[multiplex].'</td>';
  echo '<td align="center" class="'.$bg.'">'.number_format($kg[0], 0,"",".").'</td>';
  echo '</tr>';

  $bg='cell';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[dyharra].'</td>';
  echo '<td align="center" class="'.$bg.'">'.number_format($kg[1], 0,"",".").'</td>';
  echo '</tr>';

  $bg='cell1';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[iradium].'</td>';
  echo '<td align="center" class="'.$bg.'">'.number_format($kg[2], 0,"",".").'</td>';
  echo '</tr>';

  $bg='cell';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[eternium].'</td>';
  echo '<td align="center" class="'.$bg.'">'.number_format($kg[3], 0,"",".").'</td>';
  echo '</tr>';

  $bg='cell1';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[gesamtwert].'</td>';
  echo '<td align="center" class="'.$bg.'">'.number_format($gesamtwert, 0,"",".").'</td>';
  echo '</tr>';
  if($se!=$sector OR $sy!=$system)
  {
    //auslesen wieviele kollektoren derjenige einem gestehlen hat und wie der maximalwert des kopfgelds ist
  	$db_daten=mysql_query("SELECT SUM(colanz) AS colanz, SUM(energiewert) AS energiewert FROM de_user_getcol WHERE user_id='$zieluid' AND zuser_id='$ums_user_id'",$db);
  	//$num = mysql_num_rows($db_daten);if($num>=1)
    $row = mysql_fetch_array($db_daten);
    $colanz=$row["colanz"];
    $maxenergiewert=$row["energiewert"];

    //auslesen wieviel kopfgeld bereits durch den spieler auf das ziel ausgesetzt worden ist
  	$db_daten=mysql_query("SELECT energiewert FROM de_user_setbounty WHERE user_id='$ums_user_id' AND zuser_id='$zieluid'",$db);
    $row = mysql_fetch_array($db_daten);
    $kgausgesetzt=$row["energiewert"];

  	$bg='cell';
    echo '<tr>';
    echo '<td colspan="2" align="center" class="'.$bg.'">'.$bounty_lang[msg_5_1].' '.$spielername.' '.$bounty_lang[msg_5_2].'</td>';
    echo '</tr>';

    $bg='cell1';
    echo '<tr align="left">';
    echo '<td colspan="2" class="'.$bg.'">'.$bounty_lang[msg_6].': '.number_format($colanz, 0,"",".").'<br>
          '.$bounty_lang[msg_7].': '.number_format($kgausgesetzt, 0,"",".").'<br>
          '.$bounty_lang[msg_8].': '.number_format($maxenergiewert, 0,"",".").'<br>
          '.$bounty_lang[msg_9].'
          </td>';
    echo '</tr>';


    $bg='cell';
    echo '<tr align="center">';
    echo '<td class="'.$bg.'">'.$bounty_lang[multiplex].'</td>';
    echo '<td class="'.$bg.'"><input type="text" name="mtr" value="" size="10" maxlength="10"></td>';
    echo '</tr>';
    $bg='cell1';
    echo '<tr align="center">';
    echo '<td class="'.$bg.'">'.$bounty_lang[dyharra].'</td>';
    echo '<td class="'.$bg.'"><input type="text" name="dtr" value="" size="10" maxlength="10"></td>';
    echo '</tr>';
    $bg='cell';
    echo '<tr align="center">';
    echo '<td class="'.$bg.'">'.$bounty_lang[iradium].'</td>';
    echo '<td class="'.$bg.'"><input type="text" name="itr" value="" size="10" maxlength="10"></td>';
    echo '</tr>';
    $bg='cell1';
    echo '<tr align="center">';
    echo '<td class="'.$bg.'">'.$bounty_lang[eternium].'</td>';
    echo '<td class="'.$bg.'"><input type="text" name="etr" value="" size="10" maxlength="10"></td>';
    echo '</tr>';
    $bg='cell';
    echo '<tr align="center">';
    echo '<td class="'.$bg.'" colspan="2"><input type="Submit" name="trans" value="'.$bounty_lang[aussetzen].'"></td>';
    echo '</tr>';
  }

  echo '</table>';
}
else
{
  echo '<table width="565" border="0" cellpadding="0" cellspacing="1">';
  $bg='cell';
  echo '<tr>';
  echo '<td class="'.$bg.'">'.$bounty_lang[msg_10].'.</td>';
  echo '</tr>';
  echo '</table>';
}

rahmen_unten();

echo '<input type="hidden" name="se" value="'.$se.'">';
echo '<input type="hidden" name="sy" value="'.$sy.'">';

echo '</form>';
?>

<?php include "fooban.php"; ?>
</body>
</html>