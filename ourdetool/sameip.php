<?php
include "../inccon.php";
include "../inc/sv.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Gleiche IP</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
include "det_userdata.inc.php";
if($ipstomail)
{
$ips = $ips . '<table border="1" cellpadding="0" cellspacing="1" width="200">
    <tr><td align="center">IP: '.$lip.'</td></tr></table>
    <table border="1" cellpadding="0" cellspacing="1">
    <tr>
    <td width="50">User ID</td>
    <td width="150">Name</td>
    <td width="200">E-Mail</td>
    <td width="150">Passwort</td>
    <td width="140">Registriert</td>
    <td width="140">Letzter Login</td>
    <td width="70">Status</td>
    <td width="40">Logins</td>
    </tr>';


    $result=mysql_query("select * from de_login where last_ip='$lip' order by pass",$db);
    while($user = mysql_fetch_array($result))
    {
$ips = $ips . '<tr>
      <td>'.$user["user_id"].'</td>
      <td>'.$user["nic"].'</td>
      <td>'.$user["reg_mail"].'</td>
      <td>'.modpass($user["pass"]).'</td>
      <td>'.$user["register"].'</td>
      <td>'.$user["last_login"].'</td>';
      if ($user["status"]==0) $status='Inaktiv';
      if ($user["status"]==1) $status='Aktiv';
      if ($user["status"]==2) $status='Gesperrt';
      if ($user["status"]==3) $status='Urlaub';
$ips = $ips . '<td>'.$status.'</td>
      <td>'.$user["logins"].'</td>
      </tr>';
      $gesuser++;
    }
$ips = $ips . '</table><br><br> ' . $gesuser .' Spieler mit der selben IP gefunden';


$header="From:OurDETool <Support@Die-Ewigen.com>\n";
$header .= "Content-Type: text/html";

$betreff = "$sv_server_name:  $lip";

if(@mail($mail,$betreff,$ips,$header))
{
echo "<center><font color=#FFFFFF><b>Infos erfolgreich an " . $mail ." &uuml;bermittelt.</b></font></center>";

}
else
{
echo "<h1>Es ist ein Fehler beim Versenden aufgetreten.</h1>";
}



}

if($suspendall) {
  $DBData = mysql_query("select user_id from de_login where last_ip='$lip'",$db)
            or die ("Fehler beim Auslesen der Daten: " . mysql_error());

  while($IPData = mysql_fetch_array($DBData)) {
   mysql_query("UPDATE de_login set status=2 where user_id='".$IPData["user_id"]."'",$db);

   $time=strftime("%Y-%m-%d %H:%M:%S");
   $comment = mysql_query("select kommentar from de_user_info WHERE user_id='".$IPData["user_id"]."'");
   $row = mysql_fetch_array($comment);
   $eintrag = "$row[kommentar]\nDirektsperrung von $det_username über die Multiliste! \n$time";
   mysql_query("UPDATE de_user_info SET kommentar='$eintrag' WHERE user_id='".$IPData["user_id"]."'");
  }

  echo '<font color="#FF0000">Alle User mit der IP '.$lip.' wurden gesperrt!</font><br><br>';
}

    //kopf mit ip und anzahl
    echo '<table border="1" cellpadding="0" cellspacing="1" width="200">';
    echo '<tr>';
    echo '<td align="center">IP: '.$lip.'</td>';
    echo '</tr>';
    echo '</table>';

    echo '<table border="1" cellpadding="0" cellspacing="1">';
    echo '<tr>';
    echo '<td width="50">User ID</td>';
    echo '<td width="150">Name</td>';
    echo '<td width="200">E-Mail</td>';
    echo '<td width="150">Passwort</td>';
    echo '<td width="140">Registriert</td>';
    echo '<td width="140">Letzter Login</td>';
    echo '<td width="70">Status</td>';
    echo '<td width="40">Logins</td>';
    echo '</tr>';


    $result=mysql_query("select * from de_login where last_ip='$lip' order by pass",$db);
    while($user = mysql_fetch_array($result))
    {
      echo '<tr>';
      echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
      echo '<td>'.$user["nic"].'</td>';
      echo '<td>'.$user["reg_mail"].'</td>';
      echo '<td>'.modpass($user["pass"]).'</td>';
      echo '<td>'.$user["register"].'</td>';
      echo '<td>'.$user["last_login"].'</td>';
      if ($user["status"]==0) $status='Inaktiv';
      if ($user["status"]==1) $status='Aktiv';
      if ($user["status"]==2) $status='Gesperrt';
      if ($user["status"]==3) $status='Urlaub';
      $status .= ' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
      echo '<td>'.$status.'</td>';
      echo '<td>'.$user["logins"].'</td>';
      echo '</tr>';
      $gesuser++;
    }
    echo '</table><br><br> ' . $gesuser .' Spieler mit der selben IP gefunden';

    echo '<br><form action="'.$PHP_SELF.'?lip='.$lip.'" method="post">';
    echo '<input type="submit" name="suspendall" value="Alle User mit der IP Sperren">';
    echo '</form>';

  echo '<form action="sameip.php" method="post">
  <br><center><select name="mail" size="1">
  <option value="'.$det_email.'">'.$det_username.'</option>
  <option value="Ascendant@Die-Ewigen.com">Ascendant</option>
  <option value="coldan@Die-Ewigen.com">DJ16EL</option>
  <option value="Issomad@Die-Ewigen.com">Issomad</option>
  <option value="dj16el@Die-Ewigen.com">DJ16EL</option>
  <option value="NeVaR@die-ewigen.com">Der User</option>
  <option value="ThE_GuArDiAn@Die-Ewigen.com">ThE_GuArDiAn</option>
  </select>
  <input type="hidden" name="lip" value="'. $lip. '">
  <input type="Submit" name="ipstomail" value="IP\'s anfordern"></center></form>';

mysql_close($db);

function modpass($pass)
{
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}
?>


</body>
</html>
