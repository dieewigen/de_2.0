<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

$uid=$_REQUEST['uid'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Info</title>
<?php include "cssinclude.php";?>

<script language="JavaScript" type="text/javascript">

function AddText(AddTxT) {
 document.getElementById("kommentartext").value = document.getElementById("kommentartext").value + AddTxT ;
 document.getElementById("kommentartext").focus();
}

function AddMsg(NewMsg) {
 var sNow = new Date();
 var sDay = ((sNow.getDate() < 10) ? "0" + sNow.getDate() : sNow.getDate());
 var sMont = sNow.getMonth() + 1;
 var sMonth = ((sMont < 10) ? "0" + sMont : sMont);

 var sHours = ((sNow.getHours() < 10) ? "0" + sNow.getHours() : sNow.getHours());
 var sMinutes = ((sNow.getMinutes() < 10) ? "0" + sNow.getMinutes() : sNow.getMinutes());
 var sSeconds = ((sNow.getSeconds() < 10) ? "0" + sNow.getSeconds() : sNow.getSeconds());

 var sDateTime = sNow.getFullYear() + "-" + sMonth + "-" + sDay + " " + sHours + ":" + sMinutes + ":" + sSeconds;

 with ( document.getElementById("kommentartext").value ) {
  switch(NewMsg) {
   case "Multi":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen Multi\r\n");
    break;
   case "Farming":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen Farming\r\n");
    break;
   case "PWS":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen PW-Sharing\r\n");
    break;
   case "FUD":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen falschen Userdaten\r\n");
    break;
   case "SBeleid":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen extremer Beleidigung\r\n");
    break;
   case "ZUBeleid":
    AddText(sDateTime + " <? echo $det_username; ?> - Zwangsurlaub wegen Beleidigung\r\n");
    break;
   case "AccWG":
    AddText(sDateTime + " <? echo $det_username; ?> - gesperrt wegen (Verdacht auf) Accountweitergabe\r\n");
    break;
   case "UserHSG":
    AddText(sDateTime + " <? echo $det_username; ?> - User hat sich gemeldet\r\n");
    break;
   case "REAKT":
    AddText(sDateTime + " <? echo $det_username; ?> - Account (nach Absprache) wieder aktiviert\r\n");
    break;
   case "ZID":
    AddText("zugeh�rige ID(s): ");
    break;
   case "MailRD":
    AddText(sDateTime + " <? echo $det_username; ?> - Mail wegen Userdaten wurde gesendet\r\n");
    break;
  }
 }
 document.getElementById("kommentartext").focus();
}
</script>

</head>
<body>
<form action="info.php?uid=<?=$uid?>" method="post">
<?php

$rasse = array("Error","Ewiger","Isthar","K`Tharr","Z`tah-ara");

//alle angelegten user auslesen
$det_anz=0;
if ($handle = opendir('user'))
{
  /* This is the correct way to loop over the directory. */
  while (false !== ($file = readdir($handle)))
  {
    if($file!='.' AND $file!='..')
    {
      //echo "$file\n";
      $fp = fopen ('user/'.$file, 'r');
      $det_mail=fgets($fp, 1024);
      fclose($fp);
      $det_name = str_replace(".txt","",$file);
      $det_userlist[$det_anz][0]=$det_mail;
      $det_userlist[$det_anz][1]=$det_name;
      $det_anz++;
    }
  }


  closedir($handle);
}


function insertemp($spieler)
{
          $empfa=mysql_query("SELECT sector, system, spielername FROM de_user_data WHERE user_id=$spieler");
          $rowemp = mysql_fetch_array($empfa);

          $namekoords = "$rowemp[sector]:$rowemp[system] ($rowemp[spielername])";

          return $namekoords;
}

if ($sendhyperfunk) {
  $hyperfunktext = str_replace("\r\n", "<br>",$hyperfunktext);
  $hyperfunktext = str_replace("\n", "<br>",$hyperfunktext);
  $hftime=strftime("%Y%m%d%H%M%S");
  mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, betreff, text, time) VALUES ('$uid', '0', '0', '1', 'Die-Ewigen-Team', '$hyperfunkbetreff', '$hyperfunktext','$hftime')");
  mysql_query("UPDATE de_user_data SET newtrans=1 WHERE user_id='$uid'",$db);
  echo "&nbsp; &nbsp;<font color=\"#ffffff\">Hyperfunk wurde gespeichert</font><br>";
}

if ($sendmailregdaten) {
  $db_daten = mysql_query("SELECT de_user_data.spielername, de_login.reg_mail FROM de_user_data, de_login WHERE de_user_data.user_id = '".$uid."' AND de_login.user_id = '".$uid."'",$db);
  $mail_data = mysql_fetch_array($db_daten);
  $det_email='noreply@die-ewigen.com';
  $emailtext = "Hallo, \r\n";
  $emailtext .= "dein Account bei Die-Ewigen (".$mail_data["spielername"].") wurde wegen der Angabe falscher Userdaten bei der Registrierung gesperrt. \r\n";
  $emailtext .= "Bitte erstelle in der Accountverwaltung ein Ticket mit den Daten (Vorname, Nachname, PLZ, Ort, Land).\r\n";
  $emailtext .= "Falls Du Dich nicht meldest, wird dein Account nach Ablauf der Inaktivenfrist gel�scht. \r\n \r\n";
  $emailtext .= "Mit freundlichen Gr��en \r\n";
  $emailtext .= "Dein DE-Team";

  @mail($mail_data["reg_mail"], 'Die Ewigen - Accountsperrung', utf8_encode($emailtext), 'FROM: '.$det_email);

  echo "&nbsp; &nbsp;<font color=\"#ffffff\">eMail an ".$mail_data["spielername"]." (".$mail_data["reg_mail"].") wurde versandt. (�ber: ".$det_email.")</font><br>";
  $savedata=1;
}

if ($activateaccount)
{
  mysql_query("UPDATE de_user_data SET system=0 WHERE user_id='$uid'",$db);
  $savedata=1;
}
if ($stataktiv)
{
  mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid'",$db);
  //$savedata=1;
}
if ($statgesperrt)
{
  mysql_query("UPDATE de_login SET status=2, supporter='$det_email' WHERE user_id='$uid'",$db);
  //$savedata=1;
}
if ($staturlaub)
{
  mysql_query("UPDATE de_login SET status=3 WHERE user_id='$uid'",$db);
  //$savedata=1;
}
if ($locktrade)
{
	mysql_query("UPDATE de_user_data SET trade_forbidden='1' WHERE user_id='$uid'",$db);
	//$savedata=1;
}
if ($unlocktrade)
{
	mysql_query("UPDATE de_user_data SET trade_forbidden='0' WHERE user_id='$uid'",$db);
	//$savedata=1;
}
if ($statbk)
{
  //sektor rausfinden
  $db_daten = mysql_query("SELECT sector,system FROM de_user_data WHERE user_id='$uid'",$db);
  $db_daten = mysql_fetch_array($db_daten);
  $sector=$db_daten["sector"];
  $system=$db_daten["system"];

  //zum bk machen und vote zum sk entfernen
  mysql_query("UPDATE de_user_data SET votefor=0 WHERE sector='$sector'",$db);
  mysql_query("UPDATE de_sector SET bk='$system' WHERE sec_id='$sector'",$db);

  $savedata=1;
}

if($observationgo) 
{
  //Beobachtungsdaten auslesen
  $untbeo_daten = mysql_query("SELECT observation_stat, observation_by FROM de_user_info WHERE user_id='$uid'", $db);
  $observation_daten = mysql_fetch_array($untbeo_daten);

  
  //status �ndern
  if($observation_daten[observation_stat] == 0) {
    mysql_query("UPDATE de_user_info SET observation_stat = 1, observation_by = '$det_username' WHERE user_id='$uid'", $db);
    
  } 
  //$savedata=1;
}

if ($kommentar) //daten speichern
{
  $savedata=1;
}
if ($savedata==1)
{
   $db_daten = mysql_query("SELECT user_id FROM de_login WHERE reg_mail='$email'",$db);
   $de_login = mysql_fetch_array($db_daten);
   if (($de_login["user_id"] != $uid) AND (intval($de_login["user_id"]) != 0)) { echo '&nbsp; &nbsp;<font color="#ff0000">eMail: '.$email.' ist bereits vergeben! ID: '.$de_login["user_id"].'</font><br>'; }

   mysql_query("UPDATE de_login SET last_login='$lastlogin', com_sperre='$comsperre' WHERE user_id='$uid'",$db);
   mysql_query("UPDATE de_user_info SET kommentar='$kommentartext', vorname='$vorname', nachname='$nachname', strasse='$strasse', plz='$plz', ort='$ort', land='$land', telefon='$telefon' WHERE user_id='$uid'",$db);
   mysql_query("UPDATE de_login SET nic='$loginname', reg_mail='$email' WHERE user_id='$uid'",$db);

   $trade_ins = intval($trade_ins);
   $trade_acc = intval($trade_acc);
   mysql_query("UPDATE de_user_data SET spielername='$spielername' WHERE user_id='$uid'",$db);
}

if($mail=="none" && $infostomail==true)
{
echo "<center><font color=#FFFFFF><b>Kein DET ausgew&auml;hlt.</b></font></center>";
}

if($infostomail==true && $mail!="none")
{
  //de_login
  $db_daten=mysql_query("SELECT nic, last_ip, reg_mail, pass, register, last_login, status, logins, last_ip, clicks, pass FROM de_login WHERE user_id='$uid'",$db);
  $de_login = mysql_fetch_array($db_daten);
  $loginname=$de_login['nic'];
  $email=$de_login['reg_mail'];
  $lastlogin=$de_login['last_login'];

  //de_user_data
  $db_daten=mysql_query("SELECT spielername, allytag, sector, system, score, tradescore, sells, col, agent, sonde, rasse, eartefakt, kartefakt, dartefakt, werberid FROM de_user_data WHERE user_id='$uid'",$db);
  $de_user_data = mysql_fetch_array($db_daten);
  $spielername=$de_user_data['spielername'];

  //de_user_info
  $db_daten=mysql_query("SELECT vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar FROM de_user_info WHERE user_id='$uid'",$db);
  $de_user_info = mysql_fetch_array($db_daten);
  $vorname=$de_user_info['vorname'];
  $nachname=$de_user_info['nachname'];
  $strasse=$de_user_info['strasse'];
  $plz=$de_user_info['plz'];
  $ort=$de_user_info['ort'];
  $land=$de_user_info['land'];
  $telefon=$de_user_info['telefon'];

$dse = $de_user_data["sector"];
$dsy = $de_user_data["system"];



$infos = $infos . '<table border="1" cellpadding="0" cellspacing="0">
  <tr><td>
  <table border="1" cellpadding="0" cellspacing="1">
  <tr>
  <td width="100" align="center">User ID</td>
  <td width="250" align="center">'.$uid.'</td>
  </tr>
  <tr>
  <td align="center">Loginname</td>
  <td align="center">'.$loginname.'</td>
  </tr>
  <tr>
  <td align="center">Spielername</td>
  <td align="center">'.$spielername.'</td>
  </tr>
  <tr>
  <td align="center">E-Mail</td>
  <td align="center" nowrap><A HREF="mailto:'.$email.'">'.$email.'</a></td>
  </tr>
  <tr>
  <td align="center">Letzte IP</td>
  <td align="center">'.$de_login["last_ip"].'</td>
  </tr>
  <tr>
  <td align="center">Registriert</td>
  <td align="center">'.$de_login["register"].'</td>
  </tr>
  <tr>
  <td align="center">Zuletzt online</td>
  <td align="center">'.$lastlogin.'</td>
  </tr>
  <tr>
  <td align="center">Logins</td>
  <td align="center">'.$de_login["logins"].'</td>
  </tr>
  <tr>
  <td align="center">Clicks</td>
  <td align="center">'.$de_login["clicks"].'</td>
  </tr>
  <tr>
  <td align="center">Allianz</td>
  <td align="center">'.$de_user_data["allytag"].'</td>
  </tr>
  <tr>
  <td align="center">System</td>
  <td align="center">'.$de_user_data["sector"].':'.$de_user_data["system"].'</td>
  </tr>
  <tr>
  <td align="center">Punkte</td>
  <td align="center">'.$de_user_data["score"].'</td>
  </tr>
  <tr>
  <td align="center">Sonden</td>
  <td align="center">'.$de_user_data["sonde"].'</td>
  </tr>
  <tr>
  <td align="center">Agenten/Z&ouml;llner</td>
  <td align="center">'.$de_user_data["agent"].'</td>
  </tr>
  <tr>
  <td align="center">Kampf-Artefakte</td>
  <td align="center">'.$de_user_data["kartefakt"].'</td>
  </tr>
  <tr>
  <td align="center">Diplomatie-Artefakte</td>
  <td align="center">'.$de_user_data["dartefakt"].'</td>
  </tr>
  </table></td>
  <td><table border="1" cellpadding="0" cellspacing="1">
  <tr>
  <td width="100" align="center">Vorname</td>
  <td width="200" align="center">'.$vorname.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Nachname</td>
  <td width="200" align="center">'.$nachname.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Strasse</td>
  <td width="200" align="center">'.$strasse.'</td>
  </tr>
  <tr>
  <td width="100" align="center">PLZ</td>
  <td width="200" align="center">'.$plz.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Ort</td>
  <td width="200" align="center">'.$ort.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Land</td>
  <td width="200" align="center">'.$land.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Telefon</td>
  <td width="200" align="center">'.$telefon.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Geburtsdatum</td>
  <td width="200" align="center">'.$de_user_info["tag"].'-'.$de_user_info["monat"].'-'.$de_user_info["jahr"].'</td>
  </tr>
  <tr>
  <td width="100" align="center">Geschlecht</td>';

if ($de_user_info["geschlecht"]==1)$geschlecht='m&auml;nnlich';else $geschlecht='weiblich';

$infos = $infos . '
  <td width="200" align="center">'.$geschlecht.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Status</td>';


  if ($de_login["status"]==0)$status='Inaktiv';
  elseif ($de_login["status"]==1)$status='Aktiv';
  elseif ($de_login["status"]==2)$status='Gesperrt';
  elseif ($de_login["status"]==3)$status='Urlaub';

$infos = $infos . '
  <td width="200" align="center">'.$de_login["status"].' = '.$status.'</td>
  </tr>
  <tr>
  <td width="100" align="center">Handelspunkte</td>
  <td width="200" align="center">'.$de_user_data["tradescore"].'</td>
  </tr>
  <tr>
  <td width="100" align="center">Verk&auml;ufe</td>
  <td width="200" align="center">'.$de_user_data["sells"].'</td>
  </tr>
  <tr>
  <td width="100" align="center">Kollektoren</td>
  <td width="200" align="center">'.$de_user_data["col"].'</td>
  </tr>
  <tr>
  <td width="100" align="center">Passwort</td>
  <td width="200" align="center">'.modpass($de_login["pass"]).'</td>
  </tr>
  <tr>
  <td align="center">Rasse</td>
  <td align="center">'.$rasse[$de_user_data["rasse"]].'</td>
  </tr>
  <tr>
  <td align="center">Werber ID</td>
  <td align="center">'.$de_user_data["werberid"].'</td>
  </tr>
  </table></td></tr>
  <tr><td colspan="2"><b><u>Kommentar:</u></b>&nbsp;'.$de_user_info[kommentar].'</td></tr></table>';

//hyperfunknachrichten
$infos.='<br>Eingang<br>';
$db_tfn=mysql_query("SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger='$uid' and sender=0 and archiv=0 ORDER BY time DESC",$db);

  while($row = mysql_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {

    /*
    $row[text]=eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<img src=\"\\1\" border=0>",$row[text]);

    $row[text]= eregi_replace("\[b\]", "<b>",$row[text]);
    $row[text]= eregi_replace("\[/b\]", "</b>",$row[text]);

    $row[text]= eregi_replace("\[i\]", "<i>",$row[text]);
    $row[text]= eregi_replace("\[/i\]", "</i>",$row[text]);

    $row[text]= eregi_replace("\[u\]", "<u>",$row[text]);
    $row[text]= eregi_replace("\[/u\]", "</u>",$row[text]);

    $row[text]= eregi_replace("\[center\]", "<center>",$row[text]);
    $row[text]= eregi_replace("\[/center\]", "</center>",$row[text]);

    $row[text]= eregi_replace("\[pre\]", "<pre>",$row[text]);
    $row[text]= eregi_replace("\[/pre\]", "</pre>",$row[text]);
*/
    $row[text] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row[text]);
    $row[text] = str_replace("[CROT]","<font color=\"#F10505\">",$row[text]);
    $row[text] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row[text]);
    $row[text] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row[text]);

    /*
    $row[text]=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$row[text]);
    */


    $infos.= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="40%">Absender: '.$row["fromsec"].":".$row["fromsys"].' - '.$row["fromnic"].'</td>';
    $t=$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    $infos.= '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">'.$row["text"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<br><br>';
  }
  $infos.='<br>Ausgang<br>';
  $db_tfn=mysql_query("SELECT empfaenger, time, betreff, text FROM de_user_hyper WHERE absender='$uid' and sender='1' ORDER BY time DESC",$db);

  while($row = mysql_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {
    /*
    $row[text]=eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<img src=\"\\1\" border=0>",$row[text]);

    $row[text]= eregi_replace("\[b\]", "<b>",$row[text]);
    $row[text]= eregi_replace("\[/b\]", "</b>",$row[text]);

    $row[text]= eregi_replace("\[i\]", "<i>",$row[text]);
    $row[text]= eregi_replace("\[/i\]", "</i>",$row[text]);

    $row[text]= eregi_replace("\[u\]", "<u>",$row[text]);
    $row[text]= eregi_replace("\[/u\]", "</u>",$row[text]);

    $row[text]= eregi_replace("\[center\]", "<center>",$row[text]);
    $row[text]= eregi_replace("\[/center\]", "</center>",$row[text]);

    $row[text]= eregi_replace("\[pre\]", "<pre>",$row[text]);
    $row[text]= eregi_replace("\[/pre\]", "</pre>",$row[text]);
    */

    $row[text] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row[text]);
    $row[text] = str_replace("[CROT]","<font color=\"#F10505\">",$row[text]);
    $row[text] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row[text]);
    $row[text] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row[text]);

    /*
    $row[text]=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$row[text]);
    */


    $infos.= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="40%">Empf&auml;nger: '.insertemp($row[empfaenger]).'</td>';
    $t=$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    $infos.= '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">'.$row["text"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<br><br>';
  }
  $infos.='<br>Archiv<br>';
  $db_tfn=mysql_query("SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger='$uid' and archiv='1' ORDER BY time DESC",$db);

  while($row = mysql_fetch_array($db_tfn)) //jeder gefundene datensatz wird ausgegeben
  {
    /*
    $row[text]=eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<img src=\"\\1\" border=0>",$row[text]);

    $row[text]= eregi_replace("\[b\]", "<b>",$row[text]);
    $row[text]= eregi_replace("\[/b\]", "</b>",$row[text]);

    $row[text]= eregi_replace("\[i\]", "<i>",$row[text]);
    $row[text]= eregi_replace("\[/i\]", "</i>",$row[text]);

    $row[text]= eregi_replace("\[u\]", "<u>",$row[text]);
    $row[text]= eregi_replace("\[/u\]", "</u>",$row[text]);

    $row[text]= eregi_replace("\[center\]", "<center>",$row[text]);
    $row[text]= eregi_replace("\[/center\]", "</center>",$row[text]);

    $row[text]= eregi_replace("\[pre\]", "<pre>",$row[text]);
    $row[text]= eregi_replace("\[/pre\]", "</pre>",$row[text]);

    */

    $row[text] = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$row[text]);
    $row[text] = str_replace("[CROT]","<font color=\"#F10505\">",$row[text]);
    $row[text] = str_replace("[CW]","<font color=\"#FFFFFF\">",$row[text]);
    $row[text] = str_replace("[CGELB]","<font color=\"#FDFB59\">",$row[text]);


    /*
    $row[text]=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$row[text]);
    $row[text]=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$row[text]);
    */


    $infos.= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="40%">Absender: '.$row["fromsec"].":".$row["fromsys"].' - '.$row["fromnic"].'</td>';
    $t=$row["time"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    $infos.= '<td class="cr" width="60%">Zeit: '.$time.'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">Betreff: '.$row["betreff"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
    $infos.= '<tr>';
    $infos.= '<td class="cl" width="100%">'.$row["text"].'</td>';
    $infos.= '</tr>';
    $infos.= '</table>';
    $infos.= '<br><br>';
  }

  $infos.='Nachrichten:<br><br>';

  $query="SELECT time, typ, text FROM de_user_news WHERE user_id='$uid' ORDER BY time DESC";
  $th='Alle Nachrichten';
  $db_daten=mysql_unbuffered_query($query,$db);
  $infos.= '<table>';

  while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird ausgegeben
  {
    $t=$row["time"];$n=$row["typ"];
    $time=$t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    $infos.= '<tr>';
    $infos.= '<td class="cl">'.$time.'</td>';
    $infos.= '</tr>';
    $infos.= '<tr>';
    $infos.= '<td class="cl">'.$row["text"].'</td>';
    $infos.= '</tr>';
  }
  $infos.= '</table>';

$header="From:OurDETool <Support@Die-Ewigen.com>\n";
$header .= "Content-Type: text/html";

$betreff = "$sv_server_name:  $dse : $dsy";

if(@mail($mail,$betreff,$infos,$header))
{
echo "<center><font color=#FFFFFF><b>Infos erfolgreich an " . $mail ." &uuml;bermittelt.</b></font></center>";

}
else
{
echo "<h1>Es ist ein Fehler beim Versenden aufgetreten.</h1>";
}
}
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//daten ausgeben
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

if ($uid>0)
{
  //de_login
  $db_daten=mysql_query("SELECT * FROM de_login WHERE user_id='$uid'",$db);
  $de_login = mysql_fetch_array($db_daten);

  //de_user_data
  $db_daten=mysql_query("SELECT * FROM de_user_data WHERE user_id='$uid'",$db);
  $de_user_data = mysql_fetch_array($db_daten);


  //de_user_info
  $db_daten=mysql_query("SELECT vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar, observation_stat, observation_by FROM de_user_info WHERE user_id='$uid'",$db);
  $de_user_info = mysql_fetch_array($db_daten);
  
  // Beobachtungsliste ?
  if($de_user_info["observation_stat"] == 1) {
    $observation_status = "<font style='color: red;'>".$de_user_info[observation_by]."</font>";
  } else {
    $observation_status = "-";
  }
  
  echo '<table cellpadding="0" cellspacing="1">';
      echo '<tr>';
      echo '<td width="150px" align="center">Kooperation</td>';
      $coop='keine';
      echo '<td width="250px" align="center">'.$coop.'</td>';
      echo '<td width="150px" align="center">Status</td>';
      if ($de_login["status"]==0)$status='Inaktiv';
      elseif ($de_login["status"]==1)$status='Aktiv';
      elseif ($de_login["status"]==2)$status='Gesperrt';
      elseif ($de_login["status"]==3)$status='Urlaub';
      echo '<td width="250px" align="center">'.$status.'['.$de_login["status"].'] (<a href="info.php?stataktiv=1&uid='.$uid.'">aktiv</a>/<a href="info.php?statgesperrt=1&uid='.$uid.'">gesperrt</a>/<a href="info.php?staturlaub=1&uid='.$uid.'">urlaub</a>)</td>';
      
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">User ID</td>';
      echo '<td align="center">'.$uid.' (<a href="info.php?observationgo=1&uid='.$uid.'">beobachten</a>)</td>';
      echo '<td align="center">Beobachter</td>';
      echo '<td align="center">'.$observation_status.'</td>';      
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Hauptaccount ID</td>';
      echo '<td align="center"><a href="https://login.bgam.es/ourdetool/idinfo.php?UID='.$de_login['owner_id'].'" target="_blank">'.$de_login["owner_id"].'</td>';
      echo '<td align="center">Logins</td>';
      echo '<td align="center">'.$de_login["logins"].'</td>';      
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Loginname</td>';
      echo '<td align="center"><input type="text" name="loginname" value="'.$de_login["nic"].'"></td>';
      echo '<td align="center">Clicks</td>';
      echo '<td align="center">'.$de_login["clicks"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Spielername</td>';
      echo '<td align="center"><input type="text" name="spielername" value="'.$de_user_data["spielername"].'"></td>';
      echo '<td align="center">Punkte</td>';
      echo '<td align="center">'.$de_user_data["score"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">E-Mail</td>';
      echo '<td align="center"><input type="text" name="email" value="'.$de_login["reg_mail"].'"> <A HREF="mailto:'.$de_login["reg_mail"].'">anmailen</td>';
      echo '<td align="center">Sonden / Agenten / Z&ouml;llner</td>';
      echo '<td align="center">'.$de_user_data["sonde"].' / '.$de_user_data["agent"].' / '.$de_user_data["agent_lost"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Letzte IP</td>';
      
      echo '<td align="center" wrap><a href="sameip.php?lip='.$de_login["last_ip"].'" target="_blank">'.$de_login["last_ip"].'</a>
            </td>';

      //<a href="http://ripe.net/perl/whois?form_type=simple&full_query_string=&searchtext='.$de_login["last_ip"].'" target="_blank">[Info]</a><br>'.$ipinfo.'

  	  echo '<td align="center">Kriegs-Artefakte</td>';
  	  echo '<td align="center">'.$de_user_data["kartefakt"].'</td>';
      
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Registriert</td>';
      echo '<td align="center">'.$de_login["register"].'</td>';
  	  echo '<td align="center">Diplomatie-Artefakte</td>';
  	  echo '<td align="center">'.$de_user_data["dartefakt"].'</td>';
      
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Zuletzt online</td>';
      echo '<td align="center"><input type="text" name="lastlogin" value="'.$de_login["last_login"].'"></td>';
      echo '<td align="center">Allianz</td>';
      echo '<td align="center">'.$de_user_data["allytag"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Passwort</td>';
      echo '<td align="center"><a href="samepw.php?spw='.$uid.'" target="_blank">'.modpass($de_login["pass"]).'</a></td>';
	  echo '<td align="center">Koordinaten</td>';
      echo '<td align="center">'.$de_user_data["sector"].':'.$de_user_data["system"].'</td>';      
      echo '</tr>';
                echo '<tr>';
      echo '<td align="center">Vorname</td>';
      echo '<td align="center"><input type="text" name="vorname" value="'.$de_user_info["vorname"].'"></td>';
      echo '<td align="center">Rasse</td>';
      echo '<td align="center">'.$rasse[$de_user_data["rasse"]].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Nachname</td>';
      echo '<td align="center"><input type="text" name="nachname" value="'.$de_user_info["nachname"].'"></td>';
      echo '<td align="center">Premium-Account</td>';
      if($de_user_data["patime"]>time())$palz=' ('.date("d.m.Y - G:i".')', $de_user_data["patime"]);else $palz='';
      echo  '<td align="center">'.$de_user_data["premium"].$palz.'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Strasse</td>';
      echo '<td align="center"><input type="text" name="strasse" value="'.$de_user_info["strasse"].'"></td>';
      echo '<td align="center">Kollektoren</td>';
      echo '<td align="center">'.$de_user_data["col"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">PLZ</td>';
      echo '<td align="center"><input type="text" name="plz" value="'.$de_user_info["plz"].'"></td>';
      echo '<td align="center">Werber ID</td>';
      echo '<td align="center">'.$de_user_data["werberid"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Ort</td>';
      echo '<td align="center"><input type="text" name="ort" value="'.$de_user_info["ort"].'"></td>';
      echo '<td align="center">Handelsstatus</td>';
      if ($de_user_data["trade_forbidden"] === "1")
      {
      		$tradestat = '<font color=red>Handeln verboten [1]</font> (<a href="info.php?unlocktrade=1&uid='.$uid.'">entsperren</a>))</font>';
      }
      else
      {
      		$tradestat = '<font color=green>Handeln erlaubt [0]</font> (<a href="info.php?locktrade=1&uid='.$uid.'">sperren</a>)</font>';
      }
      echo '<td align="center">'.$tradestat.'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Land</td>';
      echo '<td align="center"><input type="text" name="land" value="'.$de_user_info["land"].'"></td>';
      echo '<td align="center">Handelspunkte</td>';
      echo '<td align="center">'.$de_user_data["tradescore"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Telefon</td>';
      echo '<td align="center"><input type="text" name="telefon" value="'.$de_user_info["telefon"].'"></td>';
      echo '<td align="center">Verk&auml;ufe</td>';
      echo '<td align="center">'.$de_user_data["sells"].'</td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Geburtsdatum</td>';
      echo '<td align="center">'.$de_user_info["tag"].'-'.$de_user_info["monat"].'-'.$de_user_info["jahr"].'</td>';
      echo '<td align="center">Com-Sperre</td>';
      echo '<td align="center"><input type="text" name="comsperre" value="'.$de_login["com_sperre"].'"></td>';
      echo '</tr>';
      echo '<tr>';
      echo '<td align="center">Geschlecht</td>';
      if ($de_user_info["geschlecht"]==1)$geschlecht='m&auml;nnlich';else $geschlecht='weiblich';
      echo '<td align="center">'.$geschlecht.'</td>';
      echo '</tr>';

  echo '</table>';

  echo 'IP oder PW anklicken, um alle Accounts mit gleicher IP oder gleichem PW zu finden.';

  //echo 'Sonstiges:<br><input type="Submit" name="statbk" value="zum BK machen" style="width:130px;">';

  echo '<br><br>Kommentar:<br>';
  echo "<input type=\"button\" value=\"Multi\" onclick=\"AddMsg('Multi')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Farming\" onclick=\"AddMsg('Farming')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"PW-Sharing\" onclick=\"AddMsg('PWS')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"falsche Userdaten\" onclick=\"AddMsg('FUD')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Beleidigung (Sperrung)\" onclick=\"AddMsg('SBeleid')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"Beleidigung (Urlaub)\" onclick=\"AddMsg('ZUBeleid')\" style=\"width:130px;\"> <br>";
  echo "<input type=\"button\" value=\"Accweitergabe\" onclick=\"AddMsg('AccWG')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"zug. IDs\" onclick=\"AddMsg('ZID')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"User gemeldet\" onclick=\"AddMsg('UserHSG')\" style=\"width:130px;\"> ";
  echo "<input type=\"button\" value=\"ReAktiviert\" onclick=\"AddMsg('REAKT')\" style=\"width:130px;\"><br>";
  echo '<textarea name="kommentartext" id="kommentartext" cols="130" rows="20">'.$de_user_info["kommentar"].'</textarea>';

echo '</td></tr>';
echo '<tr><td align="center">';
  echo '<br><input type="Submit" name="kommentar" value="aktuelle Daten speichern" style="width:175px; color:#ff0000;"><br>';
  
  echo '<hr>eMail an User senden: ';
  echo "<input type=\"Submit\" name=\"sendmailregdaten\" value=\"gesperrt wegen Userdaten\" onclick=\"AddMsg('MailRD')\" style=\"width:150px;\"> ";
  echo "<input type=\"Button\" name=\"sendmailsonstig\" value=\"Sonstige Mails\" onclick=\"window.open('mails.php?uid=".$uid."','_blank')\" style=\"width:150px;\"> ";
  
  
echo '</td></tr>';
echo '<tr><td align="center">';

  echo '<hr>Hyperfunk an den aktuellen User senden:<br>';
  echo 'Betreff: <input type="text" name="hyperfunkbetreff" value="" style="width:535px;"><br>';
  echo '<textarea name="hyperfunktext" id="hyperfunktext" cols="90" rows="5"></textarea><br>';
  echo '<input type="Submit" name="sendhyperfunk" value="Hyperfunk senden" style="width:150px;"><hr>';

echo '</td></tr>';
echo '</table>';
echo '</form>';
echo '<form action="info.php?uid='.$uid.'" method="post">';

if ($de_user_data["sector"]!=0 OR $sv_efta_in_de==1 OR $sv_sou_in_de==1)//wenn account aktiv
{
  echo '<select name="mail" size="1" style="width:130px;">
  <option value="'.$det_email.'">'.$det_username.'</option>';
  for ($i=0;$i<$det_anz;$i++)
  echo '<option value="'.$det_userlist[$i][0].'">'.$det_userlist[$i][1].'</option>';

  echo '</select>
  <input type="Submit" name="infostomail" value="Userinfos anfordern" style="width:130px;">';
}
else
{
  if ($de_user_data["system"]==1)
  {
    echo '<br>Account aktivieren:<br>';
    echo '<input type="Submit" name="activateaccount" value="Account aktivieren">&nbsp;&nbsp;&nbsp;';
  }
}


}
else echo 'Kein User ausgew&auml;hlt.';

function modpass($pass)
{
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}
?>
</form>
</body>
</html>
