<?php
include('inc/header.inc.php');
include('lib/transactioni.lib.php');
include('outputlib.php');
require_once('lib/phpmailer/class.phpmailer.php');
require_once('lib/phpmailer/class.smtp.php');
include('functions.php');
include('inc/lang/'.$sv_server_lang.'_options.lang.php');

$errmsg = '';
$getpamsg = '';


$ehlockfaktor = 4;

$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
  "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tick, score, sector, `system`, newtrans, newnews, allytag, hide_secpics, nrrasse, nrspielername, ovopt, credits, chatoff, chatoffallg, chatoffglobal, helper, trade_reminder, patime FROM de_user_data WHERE user_id=?", 
  [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row['score'];
$newtrans = $row['newtrans'];
$allytag = $row['allytag'];
$newnews = $row['newnews'];
$hidepic = $row['hide_secpics'];
$sector = $row['sector'];
$system = $row['system'];
$nrrasse = $row['nrrasse'];
$nrspielername = $row['nrspielername'];
$tick = $row['tick'];
$ovopt = $row['ovopt'];
$credits = $row['credits'];
$chatoff = $row['chatoff'];
$chatoffallg = $row['chatoffallg'];
$chatoffglobal = $row['chatoffglobal'];
$helperon = $row['helper'];
$patime = $row['patime'];
$trade_reminder = $row['trade_reminder'];

//owner id auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
  "SELECT owner_id FROM de_login WHERE user_id=?", 
  [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$owner_id = intval($row['owner_id']);

//maximalen tick auslesen
//$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data");
$result = mysqli_execute_query($GLOBALS['dbi'], 
  "SELECT wt AS tick FROM de_system LIMIT 1");
$row = mysqli_fetch_assoc($result);
$maxtick = $row['tick'];

//einstellungen für die nächste runde speichern
if (isset($_POST['donr'])) {
    $spielername = $_POST['spielername'];
    $rasse = $_POST['rasse'];
    if ($spielername != '') {
        if (!preg_match("/^[[:alpha:]0-9äöü_=-]*$/i", $spielername)) {
            $errmsg .= 'Im Spielernamen d&uuml;rfen keine Sonderzeichen sein (Ausnahmen sind nur: _-=).';
        } else {
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], 
              "SELECT user_id FROM de_user_data WHERE (spielername=? OR nrspielername=?) AND spielername!=?", 
              [$spielername, $spielername, $ums_spielername]);
            $vorhanden = mysqli_num_rows($db_daten);
            if ($vorhanden > 0) {
                $errmsg .= '<br>'.$options_lang['fehler5'];
            }
        }
    } else {
        $errmsg = $options_lang['fehler2'];
    }

    switch ($rasse) {
        case 1:
            $gewrasse = 1;
            break;
        case 2:
            $gewrasse = 2;
            break;
        case 3:
            $gewrasse = 3;
            break;
        case 4:
            $gewrasse = 4;
            break;
        default:
            $errmsg .= '<br>'.$options_lang['fehler3'];
            break;
    }

    //wenn alles ok ist, daten in der db ablegen
    if ($errmsg == '') {
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_user_data SET nrspielername=?, nrrasse=? WHERE user_id=?", 
          [$spielername, $gewrasse, $ums_user_id]);
        $nrrasse = $gewrasse;
        $nrspielername = $spielername;
    }
}

if (isset($_POST['graop'])) {
    if ($errmsg == '') {
        $chat = intval($_POST['chat'] ?? 0);
        $chatallg = intval($_POST['chatallg'] ?? 0);
        $chatglobal = intval($_POST['chatglobal'] ?? 0);
        $helper = intval($_POST['helper'] ?? 0);
        $traderem = intval($_POST['traderem'] ?? 0);

        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_user_data SET chatoff=?, chatoffallg=?, chatoffglobal=?, helper=?, trade_reminder=? WHERE user_id=?", 
          [$chat, $chatallg, $chatglobal, $helper, $traderem, $ums_user_id]);
        $errmsg .= $options_lang['uebernommen'];
        $chatoff = $chat;
        $chatoffallg = $chatallg;
        $_SESSION['ums_chatoffallg'] = $chatoffallg;
        $chatoffglobal = $chatglobal;
        $_SESSION['ums_chatoffglobal'] = $chatoffglobal;
        $helperon = $helper;
        $trade_reminder = $traderem;
    }
}

$delacc = $_POST['delacc'] ?? false;
if ($delacc) { //account l�schen
    $delpass = $_POST['delpass'];
    $delcheck1 = $_POST['delcheck1'];
    $delcheck2 = $_POST['delcheck2'];

    $db_datenx = mysqli_execute_query($GLOBALS['dbi'], 
      "SELECT * FROM de_login WHERE user_id=?", 
      [$ums_user_id]);
    $rowx = mysqli_fetch_assoc($db_datenx);

    $passwordOK = false;
    if (password_verify(trim($delpass), $rowx['pass'])) {
        $passwordOK = true;
    }

    if ($passwordOK) { //oldpass ist korrekt
        if ($delcheck1 == "1" and $delcheck2 == "1") {//l�sche
            //�berpr�fen ob man evtl. allianzleader ist, da ist es notwendig den posten aufzugeben
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], 
              "SELECT * FROM de_allys WHERE leaderid=?", 
              [$ums_user_id]);
            $num = mysqli_num_rows($db_daten);
            if ($num == 0) {//man ist kein leader
                $uid = $ums_user_id;

                //3 tage umode und dann killen, wenn er sich nicht mehr einloggt
                $urltage = 3;
                $tis = time() + 86400 * $urltage;
                $datum = date("Y-m-d H:i:s", $tis);

                mysqli_execute_query($GLOBALS['dbi'], 
                  "UPDATE de_login SET last_login=?, status=3, inaktmail=1, delmode=1 WHERE user_id=?", 
                  [$datum, $uid]);

                //ehlock, damit man f�r eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                mysqli_execute_query($GLOBALS['dbi'], 
                  "UPDATE de_user_data SET ehlock=? WHERE user_id=?", 
                  [$newtick, $uid]);

                //mail an den accountinhaber schicken
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], 
                  "SELECT reg_mail FROM de_login WHERE user_id=?", 
                  [$ums_user_id]);
                $row = mysqli_fetch_assoc($db_daten);
                $reg_mail = $row['reg_mail'];
                @mail_smtp($reg_mail, $options_lang['emailgeloeschtbetreff'].' - '.$sv_server_name, $options_lang['emailgeloeschtbody'], 'FROM: noreply@die-ewigen.com');

                session_destroy();
                header("Location: geloescht.php");
            } else {
                $errmsg = '<div class="info_box text2">Gib bitte zuerst Deinen Posten als Allianzleiter auf. Du kannst den Posten &uuml;bertragen, oder die Allianz l&ouml;schen.</div>';
            }
        } else {
            $errmsg = '<div class="info_box text2">Setze bitte beide H&auml;kchen um den Account zu l&ouml;schen.</div>';
        }
    } else {
        $errmsg .= '<font color="FF0000">'.$options_lang['umodefehler2'].'</font>';
    }
}

function writetocreditlog($clog)
{
    global $ums_user_id;
    $datum = date("Y-m-d H:i:s", time());
    $ip = getenv("REMOTE_ADDR");
    $clog = "Zeit: $datum\nIP: $ip\n".$clog."\n--------------------------------------\n";
    $fp = fopen("cache/creditlogs/$ums_user_id.txt", "a");
    fputs($fp, $clog);
    fclose($fp);
}

//Logout anzeige
$sekundenbiszumlogout = ($ums_session_start + $sv_session_lifetime) - time();
$restminuten = floor($sekundenbiszumlogout / 60);
$restsekunden = $sekundenbiszumlogout - ($restminuten * 60);
$color='';
if ($restminuten < 5) {
    $color = 'color="#FF0000" size="4"';
}
$logoutmsg = '<font '.$color.'>'.$restminuten.' '.$options_lang['logountmin'].' '.$restsekunden.' '.$options_lang['logoutsec'].'</font>';
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $options_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php

//stelle die ressourcenleiste dar
include('resline.php');

if (isset($_REQUEST['set_use_mobile_version'])) {
    $value = intval($_REQUEST['set_use_mobile_version']);
    echo'
<script>
let expires = new Date();
expires.setTime(expires.getTime() + (3600 * 24 * 360 * 1000));
document.cookie = "use_mobile_version='.$value.'; expires=" + expires.toUTCString() + "; path=/";
</script>';    
    $_COOKIE['use_mobile_version'] = $value;
}

if (isset($_REQUEST['set_deactivate_swipe'])) {
    $value = intval($_REQUEST['set_deactivate_swipe']);
    echo'
<script>
let expires = new Date();
expires.setTime(expires.getTime() + (3600 * 24 * 360 * 1000));
document.cookie = "deactivate_swipe='.$value.'; expires=" + expires.toUTCString() + "; path=/";
</script>';    

    $_COOKIE['deactivate_swipe'] = $value;
}

if (isset($_REQUEST['desktop_version'])) {
    $value = intval($_REQUEST['desktop_version']);
    echo'
<script>
let expires = new Date();
expires.setTime(expires.getTime() + (3600 * 24 * 360 * 1000));
document.cookie = "desktop_version='.$value.'; expires=" + expires.toUTCString() + "; path=/";
</script>';

    $_COOKIE['desktop_version'] = $value;
}
$desktop_version = intval($_COOKIE['desktop_version'] ?? 0);

$urlacc = $_POST['urlacc'] ?? false;
$showattumode=0;
if ($urlacc) { //account in urlaubsmodus versetzen
    $urlpass = $_POST['urlpass'];
    $db_datenx = mysqli_execute_query($GLOBALS['dbi'], 
      "SELECT * FROM de_login WHERE user_id=?", 
      [$ums_user_id]);
    $rowx = mysqli_fetch_assoc($db_datenx);

    $passwordOK = false;
    if (password_verify(trim($urlpass), $rowx['pass'])) {
        $passwordOK = true;
    }

    if ($passwordOK) { //oldpass ist korrekt
        $urltage = intval($_POST['urltage']);
        if ($urltage >= 1 and $urltage <= 21) {
            //schauen ob es credits kostet und man genug davon hat
            $creditkosten = 150;
            if ($credits < $creditkosten and $urltage < 3) {
                //zu wenig credits für umode
                $errmsg .= '<table width=600><tr><td class="ccr">'.$options_lang['umodezuwenigcredits1'].' '.$creditkosten.' '.$options_lang['umodezuwenigcredits2'].'</table>';
            }
            //schauen ob der account angegriffen wird
            if ($_POST['attumodecheck'] == 1) {
                $gea = '&nbsp;';
            }
            if ($gea == '&nbsp;') {
                //wenn keine fehler vorliegen, dann umode setzen
                if ($errmsg == '') {
                    //schauen ob es credits kostet
                    if ($urltage < 3) {
                        mysqli_execute_query($GLOBALS['dbi'], 
                          "UPDATE de_user_data SET credits=credits-? WHERE user_id=?", 
                          [$creditkosten, $ums_user_id]);
                        writetocreditlog("Urlaubsmodus");
                    }
                    $uid = $ums_user_id;
                    $tis = time() + 86400 * $urltage;
                    $datum = date("Y-m-d H:i:s", $tis);

                    mysqli_execute_query($GLOBALS['dbi'], 
                      "UPDATE de_login SET last_login=?, status=3 WHERE user_id=?", 
                      [$datum, $uid]);

                    //ehlock, damit man f�r eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                    $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                    mysqli_execute_query($GLOBALS['dbi'], 
                      "UPDATE de_user_data SET ehlock=? WHERE user_id=?", 
                      [$newtick, $uid]);

                    session_destroy();
                    header("Location: urlaub.php");
                }
            } else {
                $errmsg .= '<font color="FF0000">'.$options_lang['umodefehler3'].'</font>';
                $showattumode = 1;
            }
        } else {
            $errmsg .= '<font color="FF0000">'.$options_lang['umodefehler1'].'</font>';
        }
    } else {
        $errmsg .= '<font color="FF0000">'.$options_lang['umodefehler2'].'</font>';
    }
}

if ($errmsg != '') {
    echo '<table width=600><tr><td class="cc">'.$errmsg.'</td></tr></table>';
}

if ($patime > time()) {
    $palaufzeit = date("H:i:s d.m.Y", $patime);
} else {
    $palaufzeit = '-';
}

echo '
<div class="cell" style="width: 588px;">
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td colspan="2" width="560" align="center" class="ro">'.$options_lang['userdetails'].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td colspan="2"><a href="userdetails.php" target="h" class="btn">'.$options_lang['userdetails'].'</a></td>
<td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td align="center" colspan="2" class="ro">Desktopversion oder mobile Version / Wischgesten-Mobilversion</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td colspan="2">';

if(!isset($_COOKIE['use_mobile_version'])){
    $_COOKIE['use_mobile_version'] = 0;
}
if ($_COOKIE['use_mobile_version'] == 0) {
    echo '<br><a href="options.php?set_use_mobile_version=1" class="btn">zu Mobil</a><br>';
} else {
    echo '<br><a href="options.php?set_use_mobile_version=0" class="btn">zu Desktop</a><br>';
}

echo '<div>Wird erst nach dem n&auml;chsten Login wirksam.</div>';

if(!isset($_COOKIE['deactivate_swipe'])){
    $_COOKIE['deactivate_swipe'] = 0;
}
if ($_COOKIE['deactivate_swipe'] == 0) {
    echo '<br>Die Wischgesten sind <a href="options.php?set_deactivate_swipe=1" class="btn">an</a><br>';
} else {
    echo '<br>Die Wischgesten sind <a href="options.php?set_deactivate_swipe=0" class="btn">aus</a><br>';
}

echo'
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td align="center" colspan="2" class="ro">'.$options_lang['allgemeineeinstellungen'].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<form action="options.php" method="POST">
</table>
<table border="0" cellpadding="0" cellspacing="0">';

//welche Desktop-Version
echo'
<tr align="center">
  <td width="13" height="25" class="rl">&nbsp;</td>
  <td width="477px">Desktopversion<br>(Die Standardversion wird f&uuml;r Systeme ab einer horizontale Auflösung von 1280px aufw&auml;rts empfohlen. Die &Auml;nderung wird erst nach dem n&auml;chsten Login wirksam.)<br><br></td>
  <td>
    <select name="desktop_version">
      <option value="0"';
if ($desktop_version == 0) {
    echo ' selected';
}
echo '>Standard</option>
      <option value="1"';
if ($desktop_version == 1) {
    echo ' selected';
}
echo '>Classic</option>
  </td>
  <td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>Server-Chat-Channel deaktivieren</td>
<td><input type="Checkbox" name="chatallg"';
if ($chatoffallg == 1) {
    echo "checked ";
}
echo 'value="1"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>globaler Chat-Channel deaktivieren</td>
<td><input type="Checkbox" name="chatglobal"';
if ($chatoffglobal == 1) {
    echo "checked ";
}
echo 'value="1"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['helferaktivieren'].'</td>
<td><input type="Checkbox" name="helper"';
if ($helperon == 1) {
    echo "checked ";
}
echo 'value="1"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>


<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>Missionshilfe aktivieren</td>
<td><input type="Checkbox" name="traderem"';
if ($trade_reminder == 1) {
    echo "checked ";
}
echo 'value="1"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td colspan="2"><input type="Submit" name="graop" value="'.$options_lang['einstellungenspeichern'].'"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</form>
</table>

<form action="options.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" align="center" class="ro">'.$options_lang['einstellungennaechsterunde'].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="280">'.$options_lang['rasse'].'</td>
<td width="280">
<select name="rasse">';

if ($nrrasse == 1) {
    $rasse = 'Ewiger';
} elseif ($nrrasse == 2) {
    $rasse = 'Ishtar';
} elseif ($nrrasse == 3) {
    $rasse = 'K&#180;Tharr';
} elseif ($nrrasse == 4) {
    $rasse = 'Z&#180;tah-ara';
}

echo '<option selected value="'.$nrrasse.'">'.$rasse.'</option>';

echo '
<option value="1">Ewiger</option>
<option value="2">Ishtar</option>
<option value="3">K&#180;Tharr</option>
<option value="4">Z&#180;tah-ara</option>
</select>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['spielername'].' <img title="'.$options_lang['spielernamedesc'].'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
</td>
<td><input type="text" name="spielername" size="20" maxlength="20" value="'.$nrspielername.'"></td>
<td class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td height="25" class="rl">&nbsp;</td>
<td width="560" colspan="2"><input type="hidden" name="donr" value="1"><input type="submit" name="nrbu" value="'.$options_lang['datenspeichern'].'"></td>
<td class="rr">&nbsp;</td>
</tr>
</form>
</table>';

if ($owner_id == 0) {
    echo '
<table border="0" cellpadding="0" cellspacing="0">
<form action="options.php" method="POST">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" align="center" class="ro">'.$options_lang['passwortaendern'].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="280">'.$options_lang['pwold'].'</td>
<td width="280"><input type="password" name="oldpass" value=""></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['pwnew1'].'</td>
<td><input type="password" name="pass1" value="" ></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['pwnew2'].'</td>
<td><input type="password" name="pass2" value="" ></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560"><input type="Submit" name="newpass" value="'.$options_lang['pwchange'].'"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</form>';
}

/////////////////////////////////////////////////////////////
// account löschen
/////////////////////////////////////////////////////////////
echo '
<table border="0" cellpadding="0" cellspacing="0">
<form action="options.php" method="POST">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" class="ro">'.$options_lang['accountloeschen'].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560">'.$options_lang['accountloescheninfo1'];

//if ($ums_premium>0)echo '<br><font color="#FFFF00">'.$options_lang[accountloescheninfo2].'</font>';
echo '<br><font color="#FFFF00">'.$options_lang['accountloescheninfo3'].' '.number_format($sv_benticks * $ehlockfaktor, 0, "", ".").'</font>';

echo '
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="15" class="rl">&nbsp;</td>
<td width="560">&nbsp;</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>';
echo '
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>';
echo '<td width="280">'.$options_lang['passwort'].'</td>';
echo'
<td width="280"><input type="password" name="delpass" value=""></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><input name="delcheck1" type="checkbox" value="1"> '.$options_lang['bestaetigung'].' 1</td>
<td><input name="delcheck2" type="checkbox" value="1"> '.$options_lang['bestaetigung'].' 2</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560"><input type="Submit" name="delacc" value="'.$options_lang['accountloeschen'].'"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</form>
</table>';

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// urlaubsmodus
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
echo '
<table border="0" cellpadding="0" cellspacing="0">
<form action="options.php" method="POST">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td class="ro">'.$options_lang['urlaubsmodus'].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560">'.$options_lang['umodeinfo1'].'
<font color="00FF00"><br>'.$options_lang['umodeinfo2'].'</font>';
echo '<br><font color="#FFFF00">'.$options_lang['accountloescheninfo3'].' '.number_format($sv_benticks * $ehlockfaktor, 0, "", ".").'</font>';
//�berpr�fen ob man angegriffen wird
if ($showattumode == 1) {
    echo '<br><font color="FF0000"><input name="attumodecheck" type="checkbox" value="1"> '.$options_lang['umodefehler3desc'].'</font>';
}
echo '</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="15" class="rl">&nbsp;</td>
<td width="560">&nbsp;</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="280">'.$options_lang['urlaubstage'].' (1-21)</td>
<td width="280"><input type="text" name="urltage" value=""></td>
<td width="13" class="rr">&nbsp;</td>
</tr>';

echo '
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['passwort'].'</td>
<td><input type="password" name="urlpass" value=""></td>
<td width="13" class="rr">&nbsp;</td>
</tr>';

echo'
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560"><input type="Submit" name="urlacc" value="'.$options_lang['urlaubsmodusaktivieren'].'"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</div>
<br>
<br>


</table>
</form>';

include "fooban.php";

?>
</body>
</html>