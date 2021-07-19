<?php
 include "../inccon.php";

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

 $Tab1 = ""; $Tab2 = ""; $Tab3 = ""; $Tab4 = ""; $Tab5 = "";

 $DBData = mysql_query("SELECT de_user_info.user_id, de_user_info.vorname, de_user_info.nachname, de_user_info.strasse, de_user_info.ort, de_user_info.land, de_login.nic, de_login.reg_mail, de_login.status, de_user_data.spielername FROM de_user_info, de_login, de_user_data WHERE de_user_info.user_id = de_login.user_id AND de_user_info.user_id = de_user_data.user_id ORDER BY de_user_info.user_id")
           or die ("Fehler beim Auslesen der Daten: " . mysql_error());

 while($UData = mysql_fetch_array($DBData)) {
  $bGo = 1;

  if ($UData["status"]!=2) {

   if ($UData["status"]==0) $status='Inaktiv';
   if ($UData["status"]==1) $status='Aktiv';
   if ($UData["status"]==2) $status='Gesperrt';
   if ($UData["status"]==3) $status='Urlaub';

   for ($j = 0; $j < $AnzBadWords; $j++) {
    if ((stristr($UData["nic"], $BadWord[$j])) or (stristr($UData["spielername"], $BadWord[$j])) or (stristr($UData["reg_mail"], $BadWord[$j])) or (stristr($UData["vorname"], $BadWord[$j])) or (stristr($UData["nachname"], $BadWord[$j])) or (stristr($UData["strasse"], $BadWord[$j])) or (stristr($UData["ort"], $BadWord[$j]))) {
     $Tab4 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\" class=\"4\">".$UData["user_id"]."</td><td>".$UData["nic"]."</td><td>".$UData["spielername"]."</td><td>".$UData["reg_mail"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td><td>".$status."</td></tr>\r\n";
     $FCounter[3]++;
     $bGo = 0;
     break;
    } 
   } 
 
   if ($bGo == 1) {
    for ($j = 0; $j < $AnzCheckData; $j++) {
     if ((stristr($UData["vorname"], $CheckData[$j])) or (stristr($UData["nachname"], $CheckData[$j])) or (stristr($UData["ort"], $CheckData[$j]))) {
      $Tab1 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\" class=\"1\">".$UData["user_id"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td><td>".$status."</td></tr>\r\n";
      $FCounter[0]++;
      $bGo = 0;
      break;
     } 
    }
   } 

   if ($bGo == 1) {
    if ((strlen($UData["vorname"]) == 1) and (strlen($UData["nachname"]) == 1)) { 
     $Tab2 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\" class=\"2\">".$UData["user_id"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td><td>".$status."</td></tr>\r\n";
     $FCounter[1]++;
     $bGo = 0;
    }
   }

   if ($bGo == 1) {
    if ((substr($UData["vorname"],0,1) == substr($UData["vorname"],1,1)) or (substr($UData["nachname"],0,1) == substr($UData["nachname"],1,1)) or (substr($UData["ort"],0,1) == substr($UData["ort"],1,1))) { 
     $Tab3 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\" class=\"3\">".$UData["user_id"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td><td>".$status."</td></tr>\r\n";
     $FCounter[2]++;
     $bGo = 0;
    }
   }

   if ($bGo == 1) {
    if (($UData["land"] != "Deutschland") AND ($UData["land"] != "÷sterreich") AND ($UData["land"] != "Schweiz")) { 
     $Tab5 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\" class=\"5\">".$UData["user_id"]."</td><td>".$UData["vorname"]."</td><td>".$UData["nachname"]."</td><td>".$UData["strasse"]."</td><td>".$UData["ort"]."</td><td>".$UData["land"]."</td><td>".$status."</td></tr>\r\n";
     $FCounter[4]++;
     $bGo = 0;
    }
   }
  }
 }
?>

<html>
<head>
<?php include "cssinclude.php";?>
<style>
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 a.1 { color: #FFFFFF; text-decoration: underline; }
 a.2 { color: #F8E300; text-decoration: underline; }
 a.3 { color: #3fc800; text-decoration: underline; }
 a.4 { color: #ff0000; text-decoration: underline; }
 a.5 { color: #e8a0ff; text-decoration: underline; }
 a:hover { text-decoration: none; }
</style>
</head>
<body>
<?php
include "det_userdata.inc.php";
?>
 <b><a name="TOP">DE-Userpr¸fung</a></b> (Gefunden: <? echo (intval($FCounter[0]) + intval($FCounter[1]) + intval($FCounter[2]) + intval($FCounter[3]) + intval($FCounter[4])); ?>) <br><br>
 <a href="#TAB1" class="4">X</a> = Badwords (Gefunden: <? echo intval($FCounter[3]); ?>) <br>
 <a href="#TAB2" class="1">X</a> = Buchstabenkombinationssuche (Gefunden: <? echo intval($FCounter[0]); ?>) <br>
 <a href="#TAB3" class="2">X</a> = Angabe mit nur einem Buchstabe - Vorname und Nachname (Gefunden: <? echo intval($FCounter[1]); ?>) <br>
 <a href="#TAB4" class="3">X</a> = gleiche Buchstaben - aa, bb, cc, dd, ... (Gefunden: <? echo intval($FCounter[2]); ?>) <br>
 <a href="#TAB5" class="5">X</a> = Ungewˆhnliches Land - ungleich DE, ÷sterreich, Schweiz (Gefunden: <? echo intval($FCounter[4]); ?>) <br>
 <br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td><a href="#TOP" style="font-family: Wingdings 3; color: #3399FF; text-decoration: none;">X</a>&nbsp;<a name="TAB1">ID</a></td><td>Loginname</td><td>Spielername</td><td>eMail</td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td><td>Status</td></tr>
<? echo $Tab4; ?>
 </table>
 <br><br>

 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td><a href="#TOP" style="font-family: Wingdings 3; color: #3399FF; text-decoration: none;">X</a>&nbsp;<a name="TAB2">ID</a></td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td><td>Status</td></tr>
<? echo $Tab1; ?>
 </table>
 <br><br>

 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td><a href="#TOP" style="font-family: Wingdings 3; color: #3399FF; text-decoration: none;">X</a>&nbsp;<a name="TAB3">ID</a></td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td><td>Status</td></tr>
<? echo $Tab2; ?>
 </table>
 <br><br>

 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td><a href="#TOP" style="font-family: Wingdings 3; color: #3399FF; text-decoration: none;">X</a>&nbsp;<a name="TAB4">ID</a></td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td><td>Status</td></tr>
<? echo $Tab3; ?>
 </table>
 <br><br>

 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td><a href="#TOP" style="font-family: Wingdings 3; color: #3399FF; text-decoration: none;">X</a>&nbsp;<a name="TAB5">ID</a></td><td>Vorname</td><td>Nachname</td><td>Straﬂe</td><td>Ort</td><td>Land</td><td>Status</td></tr>
<? echo $Tab5; ?>
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
