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
(+ ID, * Spielername, % Mail, ksec:sys) (?[-*%~|#] wildcard)
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

  // Stellen Sie sicher, dass $sstr definiert ist
  $sstr = isset($_GET['sstr']) ? $_GET['sstr'] : '';

  if ($sstr!='')
  switch($sstr[0]){
    case '+': //user_id
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=?", [$sstr]);
      $row = mysqli_fetch_array($result);
      $sstr = $row["user_id"] ?? '';
      break;
    case '*': //spielername
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE spielername=?", [$sstr]);
      $row = mysqli_fetch_array($result);
      $sstr = $row["user_id"] ?? '';
      break;
    case '%': //email-adresse
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE reg_mail=?", [$sstr]);
      $row = mysqli_fetch_array($result);
      $sstr = $row["user_id"] ?? '';
      break;
    case 'k': //koordinaten
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      list($sec, $sys)=explode(":",$sstr);
      $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND system=?", [$sec, $sys]);
      $row = mysqli_fetch_array($result);
      $sstr = $row["user_id"] ?? '';
      break;
    case '?': //wildcard suche
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $sstr = str_replace("%","$",$sstr);
      echo '<script type="text/javascript">'."\r\n".'<!--'."\r\n".'parent.frames["de_user_anzeige"].location.href = "wildcard.php?sstr='.$sstr.'";'."\r\n".'//-->'."\r\n".'</script>'."\r\n";
      $sstr = '';
      break;
    default: //user_id
      $sstr = str_replace($sstr[0].$sstr[1],$sstr[1],$sstr);
      $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=?", [$sstr]);
      $row = mysqli_fetch_array($result);
      $sstr = $row["user_id"] ?? '';
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
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_logviewer.php?uid='.$sstr.'" target="de_user_anzeige">Logviewer</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_logsearch.php?uid='.$sstr.'" target="de_user_anzeige">Logsuche</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_stat.php?uid='.$sstr.'" target="de_user_anzeige">Statistik</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_chat.php?uid='.$sstr.'" target="de_user_anzeige">Chat</a>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="de_user_delete.php?uid='.$sstr.'" target="de_user_anzeige">L&ouml;schen</a>';

// Sektor des Benutzers abrufen
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_user_data WHERE user_id=?", [$sstr]);
$row = mysqli_fetch_array($result);
$sektor = $row["sector"] ?? '';

// Alle Benutzer im selben Sektor abrufen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, sector, system FROM de_user_data WHERE sector=? ORDER BY system", [$sektor]);

echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;Sektor: '.$sektor.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Spieler: ';

while($UData = mysqli_fetch_array($db_daten)) {
 echo '<a href="idinfo.php?UID='.$UData["user_id"].'" target="_blank">&nbsp;&nbsp;'.$UData["system"].'&nbsp;&nbsp;</a>';
}
echo '</form>';

echo '<script type="text/javascript">'."\r\n".'<!--'."\r\n".'parent.frames["de_user_anzeige"].location.href = "info.php?uid='.$sstr.'";'."\r\n".'//-->'."\r\n".'</script>'."\r\n";
}
?>

</body>
</html>