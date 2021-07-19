<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Suche</title>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_user_search.php" method="get">
(+ ID, - Login, * Nick, % Mail, ksec:sys, ~ ip, | name, # ort) (?[-*%~|#] wildcard)
&nbsp;&nbsp;
<input type="text" name="sstr" value="">
<input type="Submit" name="search" value="Suchen">

<?php
//schauen wonach gesucht wird
//+ user_id
//- nic
//* spielername
//% Mail
//k Koords
//~ IP
//| Vor-/Nachname
//? Ort

  if ($sstr!='')
  switch($sstr[0]){
    case '+': //user_id
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_login WHERE user_id='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case '-': //nic
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_login WHERE nic='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case '*': //spielername
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE spielername='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case '%': //email-adresse
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_login WHERE reg_mail='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case '~': //ip-adresse
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_login WHERE last_ip='$sstr'",$db);
      $countmultiip = mysql_num_rows($db_daten);
      if($countmultiip>1) echo "<b><font color=\"#FF0000\">Multi</font></b>";
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case 'k': //koordinaten
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      list($sec, $sys)=explode(":",$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sec' AND system='$sys'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;

    case '|': //Vor-/Nachname
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_user_info WHERE  vorname='$sstr' or nachname='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
    case '#': //Ort
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_user_info WHERE ort like '$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;

    case '?': //wildcard suche
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $sstr = str_replace("%","$",$sstr);
      echo '<script type="text/javascript">'."\r\n".'<!--'."\r\n".'parent.frames["de_user_anzeige"].location.href = "wildcard.php?sstr='.$sstr.'";'."\r\n".'//-->'."\r\n".'</script>'."\r\n";
      $sstr = '';
      break;
    default: //user_id
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $db_daten=mysql_query("SELECT user_id FROM de_login WHERE user_id='$sstr'",$db);
      $row = mysql_fetch_array($db_daten);
      $sstr=$row["user_id"];
      break;
  }//switch sstr ende
  if ($sstr=='')die ('Kein User gefunden.');
  else
{
$sstr=trim($sstr);
echo '&nbsp;&nbsp;User ID: '.$sstr;
echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;<a href="info.php?uid='.$sstr.'" target="de_user_anzeige">Info</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="tf.php?uid='.$sstr.'" target="de_user_anzeige">Hyperfunk</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="nachrichten.php?uid='.$sstr.'" target="de_user_anzeige">Nachrichten</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_getcol.php?uid='.$sstr.'" target="de_user_anzeige">Kollektoren</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_ip.php?uid='.$sstr.'" target="de_user_anzeige">IPs</a>'; 
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="techs.php?uid='.$sstr.'" target="de_user_anzeige">Technologien</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="sektorstatus.php?uid='.$sstr.'" target="de_user_anzeige">Sektorstatus</a>';
//echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="sekforumourdetool.php?uid='.$sstr.'" target="de_user_anzeige">Sektorforum</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_logviewer.php?uid='.$sstr.'" target="de_user_anzeige">Logviewer</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_logsearch.php?uid='.$sstr.'" target="de_user_anzeige">Logsuche</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_stat.php?uid='.$sstr.'" target="de_user_anzeige">Statistik</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_chat.php?uid='.$sstr.'" target="de_user_anzeige">Chat</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_credits.php?uid='.$sstr.'" target="de_user_anzeige">Credits</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_delete.php?uid='.$sstr.'" target="de_user_anzeige">L&ouml;schen</a>';

$sektor = mysql_result(mysql_query("SELECT sector FROM de_user_data WHERE user_id='$sstr'",$db),0);
$db_daten=mysql_query("SELECT user_id, sector, system FROM de_user_data WHERE sector='$sektor' ORDER BY system",$db);

echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;Sektor: '.$sektor.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Spieler: ';

while($UData = mysql_fetch_array($db_daten)) {
 echo '<a href="idinfo.php?UID='.$UData["user_id"].'" target="_blank">&nbsp;&nbsp;'.$UData["system"].'&nbsp;&nbsp;</a>';
}
echo '</form>';

echo '<script type="text/javascript">'."\r\n".'<!--'."\r\n".'parent.frames["de_user_anzeige"].location.href = "info.php?uid='.$sstr.'";'."\r\n".'//-->'."\r\n".'</script>'."\r\n";
}
?>

</body>
</html>