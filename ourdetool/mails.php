<?php
 include "../inc/sv.inc.php";
 include "../functions.php";
 include "../inc/env.inc.php";

 // Stelle sicher, dass eine Datenbankverbindung vorhanden ist
 if (!isset($GLOBALS['dbi'])) {
     $GLOBALS['dbi'] = mysqli_connect(
         $GLOBALS['env_db_dieewigen_host'], 
         $GLOBALS['env_db_dieewigen_user'], 
         $GLOBALS['env_db_dieewigen_password'], 
         $GLOBALS['env_db_dieewigen_database']
     );
 }

 include "det_userdata.inc.php";

 // Sicherstellen, dass uid gesetzt ist
 $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;

 // Benutzerdaten mit prepared statement abrufen
 $result = mysqli_execute_query($GLOBALS['dbi'], 
     "SELECT de_user_data.spielername, de_login.reg_mail, de_user_info.kommentar 
      FROM de_user_data, de_login, de_user_info 
      WHERE de_user_data.user_id = ? 
      AND de_login.user_id = ? 
      AND de_user_info.user_id = ?",
     [$uid, $uid, $uid]
 );
 
 $user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
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
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen Multi wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen Multiverstoß gesperrt.\r\nDa ein Multiaccount ein Verstoß gegen die Nutzungsbedingungen ist, bleiben die Accounts gesperrt, bis diese nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht werden.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dieser sonst erneut als Multi gesperrt und gelöscht werden würde.\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "AFarming":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen Aufruf zum Farming wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen aufruf zum Farming gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "Farming":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen Farming wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen Farmings mit dem Account '' gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "PWS":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen PW-Sharing wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen PW-Sharing mit dem Account '' gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "SperrBeleid":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen extremer Beleidigung (= Acclöschung) wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen extremer Beleidigung gesperrt.\r\nAuch in DE gilt die Netiquette, an die man sich bitte halten sollte.\r\nDer Account bleibt gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "UModeBeleid":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen Beleidigung (= ZwangsUrlaub) wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen Beleidigung für # Tage in den Zwangsurlaub gesetzt.\r\nAuch in DE gilt die Netiquette, an die man sich bitte halten sollte.\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "AWG":
    document.getElementById("acckommentar").value = sDateTime + " <?php echo htmlspecialchars($det_username); ?> - Mail wegen Accountweitergabe wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<?php echo htmlspecialchars($user_data["spielername"]); ?>) wurde wegen Accountweitergabe gesperrt.\r\nDa dies ein Verstoß gegen die Nutzungsbedingungen ist, bleibt der Account gesperrt, bis dieser nach Ablauf der Inaktivenfrist (xDE = 7 Tage, SDE = 7 Tage) gelöscht wird.\r\nBis dahin darf KEIN neuer Account erstellt werden, da dies sonst als Multi behandelt wird!\r\n\r\nMit freundlichen Grüßen\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
   case "ReAkt":
    document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen ReAktivierung wurde gesendet\r\n" ;
    document.getElementById("emailtext").value = "Hi,\r\n\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wieder aktiviert.\r\n\r\nMit freundlichen Gr��en\r\nDein DE-Team" ;
    document.getElementById("emailtext").focus();
    break;
  }
  switch(NewMsg) {
  case "autorefresh":
   document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen AutoRefresh wurde gesendet\r\n" ;
   document.getElementById("emailtext").value = "Hi,\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Nutzung eines Refresh-Skriptes f�r 3 Tage in den Zwangsurlaub gesetzt.\r\nDas Benutzen von Scripten oder Programmen jeglicher Art, zu Accountverwaltung oder Verbreitung von Accountspezifischen Informationen (Ausnahme: Offizieller DE-Browser), innerhalb oder au�erhalb des Spiels ist untersagt. Hierzu z�hlen auch automatische Abruf- und Refresh-Mechanismen wie sie z.B. der Opera-Browser verwendet.\r\nWir bitten dich dieses Problem zu beheben, ansonsten wird der Account im Wiederholungsfalle endg�ltig gesperrt.\r\n\Mit freundlichen Gr��en\r\nDein DE-Team";
   document.getElementById("emailtext").focus();
   break;
 }
  switch(NewMsg) {
  case "proxyserver":
   document.getElementById("acckommentar").value = sDateTime + " <? echo $det_username; ?> - Mail wegen Proxyserver wurde gesendet\r\n" ;
   document.getElementById("emailtext").value = "Hi,\r\ndein Account (<? echo $user_data["spielername"]; ?>) wurde wegen Nutzung von Proxyservern gesperrt. Da dies ein Versto� gegen die Nutzungsbedingungen ist, bleiben die Accounts gesperrt, bis diese nach Ablauf der Inaktivenfrist gel�scht werden. Bis dahin darf KEIN neuer Account erstellt werden, da dieser sonst als Multi gesperrt und gel�scht werden w�rde.\r\n\Mit freundlichen Gr��en\r\nDein DE-Team";
   document.getElementById("emailtext").focus();
   break;
 }  
 }
}
</script>

</head>
<body>
<center>
<form action="mails.php?uid=<?php echo $uid; ?>" method="post">
<?php
  if (isset($_POST['sendmail'])) {
   $acckommentar = isset($_POST['acckommentar']) ? $_POST['acckommentar'] : '';
   $emailtext = isset($_POST['emailtext']) ? $_POST['emailtext'] : '';
   
   $kommentartext = $user_data["kommentar"].$acckommentar."\r\n";
   
   // Update Kommentar mit prepared statement
   mysqli_execute_query($GLOBALS['dbi'], 
     "UPDATE de_user_info SET kommentar = ? WHERE user_id = ?",
     [$kommentartext, $uid]
   );
   
   $det_email=$GLOBALS['env_mail_noreply'];
   $emailtext=str_replace('\r\n',"\r\n", $emailtext);
   @mail($user_data["reg_mail"], 'Die Ewigen - Accountsperrung', utf8_encode($emailtext), 'FROM: '.$det_email);

   echo "<br><font color=\"#ffffff\">eMail an ".$user_data["spielername"]." (".$user_data["reg_mail"].") wurde versandt. (�ber: ".$det_email.") <br> Kommentar wurde dem Useraccount hinzugef�gt.</font><br>";
  }

  echo '<br><u>eMail an <b>'.$user_data["spielername"].'</b> ('.$user_data["reg_mail"].') senden:</u><br><br>';

  echo "<input type=\"button\" value=\"Multi\" onclick=\"SetMsg('Multi')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Aufruf Farming\" onclick=\"SetMsg('AFarming')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Farming\" onclick=\"SetMsg('Farming')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"PW-Sharing\" onclick=\"SetMsg('PWS')\" style=\"width:130px;\"> <br>";
  echo "<input type=\"button\" value=\"Beleidigung (L�schung)\" onclick=\"SetMsg('SperrBeleid')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Beleidigung (UMode)\" onclick=\"SetMsg('UModeBeleid')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Accweitergabe\" onclick=\"SetMsg('AWG')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"AutoRefresh\" onclick=\"SetMsg('autorefresh')\" style=\"width:130px;\"> <br>";
  echo "<input type=\"button\" value=\"ReAktiviert\" onclick=\"SetMsg('ReAkt')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Proxyserver\" onclick=\"SetMsg('proxyserver')\" style=\"width:130px;\"> ";

  echo '<br><br><textarea name="emailtext" id="emailtext" cols="70" rows="10"></textarea><br><br>';
  echo 'Kommentar als Info bei Account hinzuf�gen:<br><input type="text" name="acckommentar" id="acckommentar" value="" style="width:580px;"><br><br>';
  echo '<input type="Submit" name="sendmail" value="eMail senden" style="width:175px"><br><br>';

?>
</form>
</center>
</body>
</html>
