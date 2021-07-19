<?php
 include "../inccon.php";
 include "det_userdata.inc.php";

 $db_daten = mysql_query("SELECT de_user_data.spielername, de_login.reg_mail, de_user_info.kommentar FROM de_user_data, de_login, de_user_info WHERE de_user_data.user_id = '".$uid."' AND de_login.user_id = '".$uid."' AND de_user_info.user_id = '".$uid."'",$db);
 $user_data = mysql_fetch_array($db_daten);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>eMail an User senden</title>
<?php include "cssinclude.php";?>

<script language="JavaScript" type="text/javascript">
function SetMsg(NewMsg) {
 var sNow = new Date();
 var sDay = ((sNow.getDate() < 10) ? "0" + sNow.getDate() : sNow.getDate());
 var sMont = sNow.getMonth() + 1;
 var sMonth = ((sMont < 10) ? "0" + sMont : sMont);

 var sHours = ((sNow.getHours() < 10) ? "0" + sNow.getHours() : sNow.getHours());
 var sMinutes = ((sNow.getMinutes() < 10) ? "0" + sNow.getMinutes() : sNow.getMinutes());
 var sSeconds = ((sNow.getSeconds() < 10) ? "0" + sNow.getSeconds() : sNow.getSeconds());

 var sDateTime = sNow.getFullYear() + "-" + sMonth + "-" + sDay + " " + sHours + ":" + sMinutes + ":" + sSeconds;

 with ( document.getElementById("emailtext").value ) {
  switch(NewMsg) {
   case "Multi":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Multi wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Multiverstoß gesperrt.\r\nDa ein Multiaccount ein Verstoß gegen die Nutzungsbedingungen ist, bleiben die Accounts gesperrt, bis diese nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht werden.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dieser sonst erneut als Multi gesperrt und gelöscht werden würde.\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "AFarming":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Aufruf zum Farming wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen aufruf zum Farming gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "Farming":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Farming wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Farmings mit dem Account '' gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "PWS":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen PW-Sharing wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen PW-Sharing mit dem Account '' gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "SperrBeleid":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen extremer Beleidigung (= Acclöschung) wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen extremer Beleidigung gesperrt.\r\nAuch in DE gilt die Netiquette, an die man sich bitte halten sollte.\r\nDer Account bleibt gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "UModeBeleid":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Beleidigung (= ZwangsUrlaub) wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Beleidigung für # Tage in den Zwangsurlaub gesetzt.\r\nAuch in DE gilt die Netiquette, an die man sich bitte halten sollte.\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "AWG":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Accountweitergabe wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Accountweitergabe gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "ReAkt":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen ReAktivierung wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wieder aktiviert.\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
  case "autorefresh":
   document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen AutoRefresh wurde gesendet\r\n" ;
   document.getElementById("emailtext").value = "Hi,\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Nutzung eines Refresh-Skriptes für 3 Tage in den Zwangsurlaub gesetzt.\r\nDas Benutzen von Scripten oder Programmen jeglicher Art, zu Accountverwaltung oder Verbreitung von Accountspezifischen Informationen (Ausnahme: Offizieller DE-Browser), innerhalb oder außerhalb des Spiels ist untersagt. Hierzu zählen auch automatische Abruf- und Refresh-Mechanismen wie sie z.B. der Opera-Browser verwendet.\r\nWir bitten dich dieses Problem zu beheben, ansonsten wird der Account im Wiederholungsfalle endgültig gesperrt.\r\n\Mit freundlichen Grüßen\r\nDein DE-Team";
   document.getElementById("emailtext").focus();
   break;
 }
  switch(NewMsg) {
  case "proxyserver":
   document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Proxyserver wurde gesendet\r\n" ;
   document.getElementById("emailtext").value = "Hi,\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Nutzung von Proxyservern gesperrt. Da dies ein Verstoß gegen die Nutzungsbedingungen ist, bleiben die Accounts gesperrt, bis diese nach Ablauf der Inaktivenfrist gelöscht werden. Bis dahin darf KEIN neuer Account erstellt werden, da dieser sonst als Multi gesperrt und gelöscht werden würde.\r\n\Mit freundlichen Grüßen\r\nDein DE-Team";
   document.getElementById("emailtext").focus();
   break;
 }  
 }
}
</script>

</head>
<body>
<center>
<form action="mails.php?uid=<?=$uid?>" method="post">
<?php
  if ($sendmail) {
   $kommentartext = $user_data["kommentar"].$acckommentar."\r\n";
   mysql_query("UPDATE de_user_info SET kommentar='$kommentartext' WHERE user_id='$uid'",$db);
   $det_email='noreply@die-ewigen.com';
   $emailtext=str_replace('\r\n',"\r\n", $emailtext);
   @mail($user_data["reg_mail"], 'Die Ewigen - Accountsperrung', utf8_encode($emailtext), 'FROM: '.$det_email);

   echo "<br><font color=\"#ffffff\">eMail an ".$user_data["spielername"]." (".$user_data["reg_mail"].") wurde versandt. (über: ".$det_email.") <br> Kommentar wurde dem Useraccount hinzugefügt.</font><br>";
  }

  echo '<br><u>eMail an <b>'.$user_data["spielername"].'</b> ('.$user_data["reg_mail"].') senden:</u><br><br>';

  echo "<input type=\"button\" value=\"Multi\" onclick=\"SetMsg('Multi')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Aufruf Farming\" onclick=\"SetMsg('AFarming')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Farming\" onclick=\"SetMsg('Farming')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"PW-Sharing\" onclick=\"SetMsg('PWS')\" style=\"width:130px;\"> <br>";
  echo "<input type=\"button\" value=\"Beleidigung (Löschung)\" onclick=\"SetMsg('SperrBeleid')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Beleidigung (UMode)\" onclick=\"SetMsg('UModeBeleid')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Accweitergabe\" onclick=\"SetMsg('AWG')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"AutoRefresh\" onclick=\"SetMsg('autorefresh')\" style=\"width:130px;\"> <br>";
  echo "<input type=\"button\" value=\"ReAktiviert\" onclick=\"SetMsg('ReAkt')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Proxyserver\" onclick=\"SetMsg('proxyserver')\" style=\"width:130px;\"> ";

  echo '<br><br><textarea name="emailtext" id="emailtext" cols="70" rows="10"></textarea><br><br>';
  echo 'Kommentar als Info bei Account hinzufügen:<br><input type="text" name="acckommentar" id="acckommentar" value="" style="width:580px;"><br><br>';
  echo '<input type="Submit" name="sendmail" value="eMail senden" style="width:175px"><br><br>';

?>
</form>
</center>
</body>
</html>
