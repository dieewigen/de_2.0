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

$db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tick, score, sector, `system`, newtrans, newnews, allytag, hide_secpics, nrrasse, nrspielername, ovopt, soundoff, credits, chatoff, chatoffallg, chatoffglobal, helper, trade_reminder, patime FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
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
$soundoff = $row['soundoff'];
$credits = $row['credits'];
$chatoff = $row['chatoff'];
$chatoffallg = $row['chatoffallg'];
$chatoffglobal = $row['chatoffglobal'];
$helperon = $row['helper'];
$patime = $row['patime'];
$trade_reminder = $row['trade_reminder'];


//irc-benutzername auslesen
$db_daten = mysql_query("SELECT ircname, gpfad, transparency FROM de_user_info WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$db_ircname = $row['ircname'];
$gpfaddb = $row['gpfad'];

//$transparency=$row['transparency'];

//owner id auslesen
$db_daten = mysql_query("SELECT owner_id FROM de_login WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$owner_id = intval($row['owner_id']);

//maximalen tick auslesen
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1", $db);
$row     = mysql_fetch_array($result);
$maxtick = $row['tick'];

//einstellungen für die nächste runde speichern
if (isset($_POST['donr'])) {
    $spielername = $_POST['spielername'];
    $rasse = $_POST['rasse'];
    if ($spielername != '') {
        if (!preg_match("/^[[:alpha:]0-9äöü_=-]*$/i", $spielername)) {
            $errmsg .= 'Im Spielernamen d&uuml;rfen keine Sonderzeichen sein (Ausnahmen sind nur: _-=).';
        } else {
            $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE (spielername='$spielername' OR nrspielername='$spielername') AND spielername!='$ums_spielername'", $db);
            $vorhanden = mysql_num_rows($db_daten);
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
        mysql_query("UPDATE de_user_data SET nrspielername='$spielername', nrrasse='$gewrasse' WHERE user_id = '$ums_user_id'", $db);
        $nrrasse = $gewrasse;
        $nrspielername = $spielername;
    }
}

if (isset($_POST['graop'])) {
    //<input type="hidden" name="dograop" value="1">
    $secpic = $_POST['secbild'];
    if ($secbild == $secanzeige[0]) {
        $secpic = 0;
    } elseif ($secbild == $secanzeige[1]) {
        $secpic = 1;
    } elseif ($secbild == $secanzeige[2]) {
        $secpic = 2;
    } else {
        $secpic = 0;
    }
    $hidepic = $secpic;
    //ircname
    if (!preg_match("/^[[:alpha:]0-9äöü_=-]*$/i", $_POST['ircname'])) {
        $errmsg .= $options_lang['fehler4'];
        $ircname = '';
    } else {
        $db_ircname = $_POST['ircname'];
        $ircname = $_POST['ircname'];
    }

    if ($errmsg == '') {
        //$gpfad = $_REQUEST['gpfad'];
        $gpfad = '';
        $sound = (int)$_POST['sound'];
        $chat = isset($_POST['chat']) ? intval($_POST['chat']) : 0;
        $chatallg = (int)$_POST['chatallg'];
        $chatglobal = (int)$_POST['chatglobal'];
        $helper = (int)$_POST['helper'];
        $traderem = (int)$_POST['traderem'];


        /*
        $transparency=intval($_POST['transparenz']);
        if($transparenz<40)$transparenz=40;
        elseif($transparenz>100)$transparenz=100;
        $_SESSION['ums_transparency']=$transparenz;
        */
        if ($_REQUEST['gpfad'] != '') {
            $ums_gpfad = $gpfad;
            $gpfaddb = $gpfad;
        } else {
            $ums_gpfad = $sv_image_server_list[0];
            $gpfaddb = '';
        }
        //$ums_gpfad=$gpfad;$gpfaddb=$gpfad;
        //mysql_query("update de_user_info set gpfad = '$gpfad', ircname='$ircname', transparency='$transparenz' WHERE user_id = '$ums_user_id'",$db);
        mysql_query("update de_user_info set gpfad = '$gpfad', ircname='$ircname' WHERE user_id = '$ums_user_id'", $db);
        mysql_query("update de_user_data set hide_secpics = '$secpic', soundoff='$sound', chatoff='$chat', chatoffallg='$chatallg', chatoffglobal='$chatglobal', helper='$helper', trade_reminder='$traderem' WHERE user_id = '$ums_user_id'", $db);
        $hidepic = $secpic;
        $errmsg .= $options_lang['uebernommen'];
        $soundoff = $sound;
        $chatoff = $chat;
        $chatoffallg = $chatallg;
        $_SESSION['ums_chatoffallg'] = $chatoffallg;
        $chatoffglobal = $chatglobal;
        $_SESSION['ums_chatoffglobal'] = $chatoffglobal;
        $helperon = $helper;
        $trade_reminder = $traderem;
    }
}

//wurde ein button gedrueckt??
$oldpass = $_POST['oldpass'] ?? '';
$pass1 = $_POST['pass1'] ?? '';
$pass2 = $_POST['pass2'] ?? '';
if (isset($_POST['newpass']) && $owner_id == 0) {
    //echo '<br>'.HTTP_REFERER.'<br>';
    $db_daten = mysql_query("SELECT user_id FROM de_login WHERE user_id = '$ums_user_id' AND pass=MD5('$oldpass')");
    $num = mysql_num_rows($db_daten);
    if ($num == 1) { //oldpass ist korrekt
        $pass1 = trim($pass1);
        $pass2 = trim($pass2);
        if ($pass1 == $pass2) {
            if (strlen($pass1) > 3) {
                mysql_query("update de_login set pass = MD5('$pass1'), newpass='' WHERE user_id = '$ums_user_id'", $db);
                $errmsg .= $options_lang['pwnew'];
            } else {
                $errmsg .= '<font color="FF0000">'.$options_lang['pw2short'].'</font>';
            }
        } else {
            $errmsg .= '<font color="FF0000">'.$options_lang['pwdifferent'].'</font>';
        }
    } else {
        $errmsg .= '<font color="FF0000">'.$options_lang['pwfalsch'].'</font>';
    }
}

$delacc = $_POST['delacc'] ?? false;
if ($delacc) { //account l�schen
    $delpass = $_POST['delpass'];
    $delcheck1 = $_POST['delcheck1'];
    $delcheck2 = $_POST['delcheck2'];

    $db_datenx = mysql_query("SELECT * FROM de_login WHERE user_id = '$ums_user_id'");
    $rowx = mysql_fetch_array($db_datenx);

    $passwordOK = false;
    if (password_verify(trim($delpass), $rowx['pass'])) {
        $passwordOK = true;
    }

    if ($passwordOK) { //oldpass ist korrekt
        if ($delcheck1 == "1" and $delcheck2 == "1") {//l�sche
            //�berpr�fen ob man evtl. allianzleader ist, da ist es notwendig den posten aufzugeben
            $db_daten = mysql_query("SELECT * FROM de_allys WHERE leaderid='$ums_user_id';", $db);
            $num = mysql_num_rows($db_daten);
            if ($num == 0) {//man ist kein leader
                $uid = $ums_user_id;

                //3 tage umode und dann killen, wenn er sich nicht mehr einloggt
                $urltage = 3;
                $tis = time() + 86400 * $urltage;
                $datum = date("Y-m-d H:i:s", $tis);

                mysql_query("UPDATE de_login SET last_login='$datum', status=3, inaktmail=1, delmode=1 WHERE user_id=$uid", $db);

                //ehlock, damit man f�r eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                mysql_query("UPDATE de_user_data SET ehlock='$newtick' WHERE user_id=$uid", $db);


                //mail an den accountinhaber schicken
                $db_daten = mysql_query("SELECT reg_mail FROM de_login WHERE user_id='$ums_user_id'", $db);
                $row = mysql_fetch_array($db_daten);
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
    $db_datenx = mysql_query("SELECT * FROM de_login WHERE user_id = '$ums_user_id'");
    $rowx = mysql_fetch_array($db_datenx);

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
                        mysql_query("UPDATE de_user_data SET credits=credits-'$creditkosten' WHERE user_id = '$ums_user_id'", $db);
                        writetocreditlog("Urlaubsmodus");
                    }
                    $uid = $ums_user_id;
                    $tis = time() + 86400 * $urltage;
                    $datum = date("Y-m-d H:i:s", $tis);

                    mysql_query("UPDATE de_login SET last_login='$datum', status=3 WHERE user_id=$uid", $db);

                    //ehlock, damit man f�r eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                    $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                    mysql_query("UPDATE de_user_data SET ehlock='$newtick' WHERE user_id=$uid", $db);

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

//strings der Übersichtsoptionen vorbelegen
/*
$ovoptfeld='
<option>'.$ovbes[0].'</option>
<option>'.$ovbes[1].'</option>
<option>'.$ovbes[2].'</option>
<option>'.$ovbes[3].'</option>
<option>'.$ovbes[4].'</option>
<option>'.$ovbes[5].'</option>
<option>'.$ovbes[6].'</option>
<option>'.$ovbes[7].'</option>';

$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[0]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[1]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[2]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[3]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[4]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[5]].'</option>';
$ovselected[]='<option selected>'.$ovbes[$ovoptfelder[6]].'</option>';
*/

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
</tr>';


/*
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[sounddeaktivieren]?></td>
<td><input type="Checkbox" name="sound" <? if($soundoff=="1") echo "checked"; ?> value="1"></td>
<td width="13" class="rr">&nbsp;</td>
*/
/*
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang['chatdeaktivieren'].'</td>
<td><input type="Checkbox" name="chat"';
if($chatoff=="1") echo "checked ";
echo 'value="1"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
*/

echo '
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
</table>';

/*

<td width="13" height="25" class="rl">&nbsp;</td>
<td>'.$options_lang[ircname].'</td>
<td><input type="Text" name="ircname" value="'.$db_ircname.'" maxlength="255"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>


<form action="options.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" align="center" class="ro"><?=$options_lang[einteilunguebersicht]?></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="200"><?=$options_lang[position]?> 1</td>
<td width="360"><select name="opt1"><?=$ovselected[0]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 2</td>
<td><select name="opt2"><?=$ovselected[1]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 3</td>
<td><select name="opt3"><?=$ovselected[2]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 4</td>
<td><select name="opt4"><?=$ovselected[3]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 5</td>
<td><select name="opt5"><?=$ovselected[4]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 6</td>
<td><select name="opt6"><?=$ovselected[5]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[position]?> 7</td>
<td><select name="opt7"><?=$ovselected[6]?><?=$ovoptfeld?></select></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560" colspan="2"><br><?=$options_lang[uebersichtinfo]?><br><br><input type="hidden" name="dooveinst" value="1"><input type="submit" name="oveinst" value="<?=$options_lang[einstellungenuebernehmen]?>"><br><br></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</form>
<form action="options.php" method="POST">
</table>


<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" align="center" class="ro"><?=$options_lang[accountdetails]?></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><?=$options_lang[accountid]?>: <?=$sv_server_tag.$ums_user_id?></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</form>
*/
/*
echo '
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td align="center" class="ro">'.$options_lang[lastlogins].'</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="560">
<table border="0">
<colgroup>
<col width="250">
<col width="250">
</colgroup>
<tr>
<td class="cell1" align="center"><b>'.$options_lang[ipadresse].'</b></td>
<td class="cell1" align="center"><b>'.$options_lang[loginzeit].'</b></td>
</tr>';

$sel_user_ip = mysql_query("SELECT ip, time FROM de_user_ip WHERE user_id='$ums_user_id' order by time desc limit 10");
while($row_user_ip = mysql_fetch_array($sel_user_ip))
{
$ip=explode(".",$row_user_ip[ip]);

echo '<tr align="center"><td>'.$ip[0].'.'.$ip[1].'.X.X</td><td>'.$row_user_ip[time].'</td></tr> ';
}
echo '
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
*/
echo '
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

/*
if ($sector!=1 AND 1==2)
{
?>
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="560" class="ro">Account resetten</td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td>Um den Account zu resetten das Passwort eingeben, beide Best�tigungen anklicken und dann mit "Account resetten" best�tigen. Durch den Reset werden alle Technologien auf den Startwert gesetzt, es gehen alle Schiffe/Kollektoren und sonstige Errungenschaften verloren. Durch den Reset wird das System Sektor 1 zugewiesen.</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="15" class="rl">&nbsp;</td>
<td>&nbsp;</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="280">Passwort</td>
<td width="280"><input type="password" name="umzpass" value=""></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td><input name="umzcheck1" type="checkbox" value="1"> Best&auml;tigung 1</td>
<td><input name="umzcheck2" type="checkbox" value="1"> Best&auml;tigung 2</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rl">&nbsp;</td>
<td width="560"><input type="Submit" name="selfvoteout" value="Account resetten"></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php
}
*/

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// account löschen
/////////////////////////////////////////////////////////////
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