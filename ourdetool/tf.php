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
<form action="tf.php" method="get">
<a name="top"></a>
<br><center>
<a href="#eingang">Eingang</a> | <a href="#ausgang">Ausgang</a> | <a href="#archiv">Archiv</a>
<?php
include "det_userdata.inc.php";
function insertemp($spieler)
{
          $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, system, spielername FROM de_user_data WHERE user_id=?", [$spieler]);
          $rowemp = mysqli_fetch_array($result);

          $namekoords = "$rowemp[sector]:$rowemp[system] ($rowemp[spielername])";

          return $namekoords;
}

if ($uid>0)
{
echo "<br><br><h1><a name=\"eingang\">Eingang</h1></a><br><br>";
$db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger=? and sender=0 and archiv=0 ORDER BY time DESC", [$uid]);

  while($row = mysqli_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {

    $row['text'] = str_replace(":)","<img src=\"../g/smilies/sm1.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":D","<img src=\"../g/smilies/sm2.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(";)","<img src=\"../g/smilies/sm3.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":x","<img src=\"../g/smilies/sm4.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":(","<img src=\"../g/smilies/sm5.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("x(","<img src=\"../g/smilies/sm6.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":p","<img src=\"../g/smilies/sm7.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("(?)","<img src=\"../g/smilies/sm8.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("(!)","<img src=\"../g/smilies/sm9.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":{","<img src=\"../g/smilies/sm10.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":}","<img src=\"../g/smilies/sm11.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":L","<img src=\"../g/smilies/sm12.gif\" alt=\"Smilie\">",$row['text']);
  
    $row['text'] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row['text']);
    $row['text'] = str_replace("[CROT]","<font color=\"#F10505\">",$row['text']);
    $row['text'] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row['text']);
    $row['text'] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row['text']);


	$row['text'] = nl2br($row['text']);

    echo '<table border="0" cellpadding="0" cellspacing="2" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="40%">Absender: '.$row["fromsec"].":".$row["fromsys"].' - '.$row["fromnic"].'</td>';
    $t=(string)$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    echo '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">'.$row["text"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br><br>';
  }

echo '<a href="#eingang">Eingang</a> | <a href="#ausgang">Ausgang</a> | <a href="#archiv">Archiv</a> | <a href="#top">Top</a><br>';

echo "<br><br><h1><a name=\"ausgang\">Ausgang</a></h1><br><br>";

$db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT empfaenger, time, betreff, text FROM de_user_hyper WHERE absender=? and sender='1' ORDER BY time DESC", [$uid]);

  while($row = mysqli_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {
    $row['text'] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row['text']);
    $row['text'] = str_replace("[CROT]","<font color=\"#F10505\">",$row['text']);
    $row['text'] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row['text']);
    $row['text'] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row['text']);


	$row['text'] = nl2br($row['text']);

    echo '<table border="0" cellpadding="0" cellspacing="2" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="40%">Empf&auml;nger: '.insertemp($row['empfaenger']).'</td>';
    $t=(string)$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    echo '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">'.$row["text"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br><br>';
  }
echo '<br><a href="#eingang">Eingang</a> | <a href="#ausgang">Ausgang</a> | <a href="#archiv">Archiv</a> | <a href="#top">Top</a><br>';
echo "<br><br><h1><a name=\"archiv\">Archiv</a></h1><br><br>";

$db_tfn=mysqli_execute_query($GLOBALS['dbi'], "SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger=? and archiv='1' ORDER BY time DESC", [$uid]);

  while($row = mysqli_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {

    $row['text'] = str_replace(":)","<img src=\"../g/smilies/sm1.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":D","<img src=\"../g/smilies/sm2.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(";)","<img src=\"../g/smilies/sm3.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":x","<img src=\"../g/smilies/sm4.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":(","<img src=\"../g/smilies/sm5.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("x(","<img src=\"../g/smilies/sm6.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":p","<img src=\"../g/smilies/sm7.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("(?)","<img src=\"../g/smilies/sm8.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace("(!)","<img src=\"../g/smilies/sm9.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":{","<img src=\"../g/smilies/sm10.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":}","<img src=\"../g/smilies/sm11.gif\" alt=\"Smilie\">",$row['text']);
    $row['text'] = str_replace(":L","<img src=\"../g/smilies/sm12.gif\" alt=\"Smilie\">",$row['text']);

    $row['text'] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row['text']);
    $row['text'] = str_replace("[CROT]","<font color=\"#F10505\">",$row['text']);
    $row['text'] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row['text']);
    $row['text'] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row['text']);


	$row['text'] = nl2br($row['text']);

    echo '<table border="0" cellpadding="0" cellspacing="2" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="40%">Absender: '.$row["fromsec"].":".$row["fromsys"].' - '.$row["fromnic"].'</td>';
    $t=(string)$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    echo '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    echo '<tr>';
    echo '<td class="cl" width="100%">'.$row["text"].'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br><br>';
  }
echo '<br><a href="#eingang">Eingang</a> | <a href="#ausgang">Ausgang</a> | <a href="#archiv">Archiv</a> | <a href="#top">Top</a><br>';
}
else echo 'Kein User ausgew&auml;hlt.';
?>
</form>
</body>
</html>
