<?php
 include "../inccon.php";

 // ***************************************
 // Script zum Auswerten der Userpasswörter
 // ***************************************

 //user-id in passwort umwandeln
 $spw=intval($_REQUEST["spw"]);
 $db_data=mysql_query("SELECT pass FROM de_login WHERE user_id='$spw'");
 $row = mysql_fetch_array($db_data);
 $spw=$row["pass"];


 function getmicrotime(){
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
 }

 $time_start = getmicrotime();

 $Tab1 = "";

 if (isset($spw)) {
  $DBData = mysql_query("SELECT de_login.user_id, de_login.nic, de_login.reg_mail, de_login.status, de_login.pass, de_login.last_ip, de_login.last_login, de_user_data.sector, de_user_data.allytag FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.pass = '".$spw."' ORDER BY de_login.pass, de_login.last_ip")
            or die ("Fehler beim Auslesen der Daten: " . mysql_error());
 }
 else {
  $DBData = mysql_query("SELECT de_login.user_id, de_login.nic, de_login.reg_mail, de_login.status, de_login.pass, de_login.last_ip, de_login.last_login , de_user_data.sector, de_user_data.allytag FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) ORDER BY de_login.pass, de_login.last_ip")
            or die ("Fehler beim Auslesen der Daten: " . mysql_error());
 }

 while($UData = mysql_fetch_array($DBData)) {
   $soundExW = soundex(substr($UData["reg_mail"],0,strpos($UData["reg_mail"],"@")));

   if ($UData["status"]==0) $status='Inaktiv';
   if ($UData["status"]==1) $status='Aktiv';
   if ($UData["status"]==2) $status='Gesperrt';
   if ($UData["status"]==3) $status='Urlaub';

   $PW_o = $PW_a;
   $PW_a = modpass($UData["pass"]);

   if ($PW_a == $PW_o) {
    if ($PWZ == 0) {
      if ($fcz == 0) { $fcz = 1; } else { $fcz = 0; }
      if (stristr($SExtmp, $soundExW)) { $fcs = 3; } else { $fcs = 2; }
      if (stristr($LIP, $UData["last_ip"])) { $fcip = 3; } else { $fcip = 2; }
      $Tab1 .= $tmp;
      $Tab1 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\">".$UData["user_id"]."</td><td class=\"".$fcz."\">".modpass($UData["pass"])."</td><td>".$UData["nic"]."</td><td class=\"".$fcs."\">".$soundExW."</td><td>".$UData["reg_mail"]."</td><td class=\"".$fcip."\">".$UData["last_ip"]."</td><td>".$status."</td><td>".$UData["last_login"]."</td><td>".$UData["sector"]."&nbsp;</td><td>".$UData["allytag"]."&nbsp;</td></tr>\r\n";
      $PWZ++;
      $FCounter[0]++;
    }
    else {
     if (stristr($SExtmp, $soundExW)) { $fcs = 3; } else { $fcs = 2; }
     if (stristr($LIP, $UData["last_ip"])) { $fcip = 3; } else { $fcip = 2; }
     $Tab1 .= "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\">".$UData["user_id"]."</td><td class=\"".$fcz."\">".modpass($UData["pass"])."</td><td>".$UData["nic"]."</td><td class=\"".$fcs."\">".$soundExW."</td><td>".$UData["reg_mail"]."</td><td class=\"".$fcip."\">".$UData["last_ip"]."</td><td>".$status."</td><td>".$UData["last_login"]."</td><td>".$UData["sector"]."&nbsp;</td><td>".$UData["allytag"]."&nbsp;</td></tr>\r\n";
     $PWZ++;
    }
    $FCounter[0]++;
    $SExtmp .= "#".$soundExW;
    $LIP .= "#".$UData["last_ip"];
   }
   else { $PWZ = 0; }

   if ($PWZ == 0) {
    $SExtmp = "#".$soundExW;
    $LIP = "#".$UData["last_ip"];
   }
   if ($fcz == 0) { $fczn = 1; } else { $fczn = 0; }
   $tmp = "  <tr><td><a href=\"idinfo.php?UID=".$UData["user_id"]."\" target=\"_blank\">".$UData["user_id"]."</td><td class=\"".$fczn."\">".modpass($UData["pass"])."</td><td>".$UData["nic"]."</td><td>".$soundExW."</td><td>".$UData["reg_mail"]."</td><td>".$UData["last_ip"]."</td><td>".$status."</td><td>".$UData["last_login"]."</td><td>".$UData["sector"]."&nbsp;</td><td>".$UData["allytag"]."&nbsp;</td></tr>\r\n";
 }

 if ((isset($spw)) AND ($FCounter[0] == 0)) {
  $Tab1 .= $tmp;
  $FCounter[0]++;
 }
?>

<html>
<head>
<?php include "cssinclude.php";?>
<style >
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 a { color: #3399ff; text-decoration: underline }
 a:hover { color: #3399ff; text-decoration: none }
 td.0 { color: #3fc800; }
 td.1 { color: #e8a0ff; }
 td.2 { color: #3399ff; }
 td.3 { color: #ff0000; }
</style>
</head>
<body>
<?php
include "det_userdata.inc.php";
?>
 <b>DE-PW-Prüfung (+ Mail-SoundEx + Multi-IP)</b> (Gefunden: <? echo intval($FCounter[0]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Passwort</td><td>Loginname</td><td>MSEx</td><td>eMail</td><td>Letzte IP</td><td>Status</td><td>Letzter Login</td><td>Sektor</td><td>Ally</td></tr>
<? echo $Tab1; ?>
 </table>
 <br><br>

 <?
  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");

function modpass($pass)
{
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}

?>

 <br>
 Seite in <? echo $ltime; ?> Sekunden erstellt.

</body>
</html>