<?php
 include("../inccon.php");

 // **********************************
 // Script zum Auswerten der Userdaten
 // **********************************

 function getmicrotime(){ 
  list($usec, $sec) = explode(" ",microtime()); 
  return ((float)$usec + (float)$sec); 
 } 

 $time_start = getmicrotime();

 $CheckData = file("http://www.mrcpu.de/DETool/DECheckdata.dat");
 //$CheckData = file("DECheckdata.dat");
 $AnzCheckData = count($CheckData);
 $ListCD = "";
 for ($i = 0; $i < $AnzCheckData; $i++) {
  $CheckData[$i] = trim($CheckData[$i]);
  $ListCD .= $CheckData[$i].", ";
 }

 $BadWord = file("http://www.mrcpu.de/DETool/DEBadWords.dat");
 //$BadWord = file("DEBadWords.dat");
 $AnzBadWords = count($BadWord);
 $ListBW = "";
 for ($i = 0; $i < $AnzBadWords; $i++) {
  $BadWord[$i] = trim($BadWord[$i]);
  $ListBW .= $BadWord[$i].", ";
 }

 $Tab1 = ""; $Tab2 = ""; $Tab3 = ""; $Tab4 = "";

 $DBData = mysql_query("SELECT de_user_info.user_id, de_user_info.vorname, de_user_info.nachname, de_user_info.strasse, de_user_info.ort, de_user_info.land, de_login.nic, de_login.reg_mail, de_user_data.spielername FROM de_user_info, de_login, de_user_data WHERE de_user_info.user_id = de_login.user_id AND de_user_info.user_id = de_user_data.user_id")
           or die ("Fehler beim Auslesen der Daten: " . mysql_error());

 while($UData = mysql_fetch_array($DBData)) {
  $bGo = 1;

  for ($j = 0; $j < $AnzCheckData; $j++) {
   if ((stristr($UData["vorname"], $CheckData[$j])) or (stristr($UData["nachname"], $CheckData[$j])) or (stristr($UData["ort"], $CheckData[$j]))) {
    $Tab1 .= "  <tr><td><font color=\"#001e96\">".$UData["user_id"]."</font></td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td></tr>\r\n";
    $FCounter[0]++;
    $bGo = 0;
    break;
   } 
  }

  if ($bGo == 1) {
   if ((strlen($UData["vorname"]) == 1) and (strlen($UData["nachname"]) == 1)) { 
    $Tab2 .= "  <tr><td><font color=\"#7e0081\">".$UData["user_id"]."</font></td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td></tr>\r\n";
    $FCounter[1]++;
    $bGo = 0;
   }
  }

  if ($bGo == 1) {
   if ((substr($UData["vorname"],0,1) == substr($UData["vorname"],1,1)) or (substr($UData["nachname"],0,1) == substr($UData["nachname"],1,1)) or (substr($UData["ort"],0,1) == substr($UData["ort"],1,1))) { 
    $Tab3 .= "  <tr><td><font color=\"#3fc800\">".$UData["user_id"]."</font></td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td></tr>\r\n";
    $FCounter[2]++;
    $bGo = 0;
   }
  }

  if ($bGo == 1) {
   for ($j = 0; $j < $AnzBadWords; $j++) {
    if ((stristr($UData["nic"], $BadWord[$j])) or (stristr($UData["spielername"], $BadWord[$j])) or (stristr($UData["reg_mail"], $BadWord[$j])) or (stristr($UData["vorname"], $BadWord[$j])) or (stristr($UData["nachname"], $BadWord[$j])) or (stristr($UData["ort"], $BadWord[$j]))) {
     $Tab4 .= "  <tr><td><font color=\"#ff0000\">".$UData["user_id"]."</font></td><td>".$UData["nic"]."</td><td>".$UData["spielername"]."</td><td>".$UData["reg_mail"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td></tr>\r\n";
     $FCounter[3]++;
     $bGo = 0;
     break;
    } 
   }
  }
 }
?>

<html>
<body>

 <b>DE-Userpr¸fung</b> (Gefunden: <? echo (intval($FCounter[0]) + intval($FCounter[1]) + intval($FCounter[2]) + intval($FCounter[3])); ?>) <br>
 <font color="#001e96">X</font> = Buchstabenkombinationssuche (Gefunden: <? echo intval($FCounter[0]); ?>) <br>
 <font color="#7e0081">X</font> = Angabe mit nur einem Buchstabe - Vorname und Nachname (Gefunden: <? echo intval($FCounter[1]); ?>) <br>
 <font color="#3fc800">X</font> = gleiche Buchstaben - aa, bb, cc, dd, ... (Gefunden: <? echo intval($FCounter[2]); ?>) <br>
 <font color="#ff0000">X</font> = Badwords (Gefunden: <? echo intval($FCounter[3]); ?>) <br><br>

 <table border="1">
  <tr><td>ID</td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td></tr>
<? echo $Tab1; ?>
 </table>
 <br><br>

 <table border="1">
  <tr><td>ID</td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td></tr>
<? echo $Tab2; ?>
 </table>
 <br><br>

 <table border="1">
  <tr><td>ID</td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td></tr>
<? echo $Tab3; ?>
 </table>
 <br><br>

 <table border="1">
  <tr><td>ID</td><td>Loginname</td><td>Spielername</td><td>eMail</td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td></tr>
<? echo $Tab4; ?>
 </table>
 <br><br>

 <b>Buchstabenkombinationssuche: </b><? echo $ListCD; ?><br><br>
 <b>BadWords: </b><? echo $ListBW; ?><br><br>

 <?
  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");
 ?>

 <br>
 Seite in <? echo $ltime; ?> Sekunden erstellt.

</body>
</html>