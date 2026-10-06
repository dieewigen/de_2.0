<?php
include('inc/header.inc.php');
include('functions.php');
include('inc/lang/'.$sv_server_lang.'_options.lang.php');
include('inc/lang/'.$sv_server_lang.'_exile.lang.php');

//Meldungen: $errmsg sammelt Fehler (und sperrt dann weitere Aktionen), $okmsg Bestätigungen
$errmsg = '';
$okmsg = '';

$ehlockfaktor = 4;

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tick, score, sector, `system`, newtrans, newnews, allytag, hide_secpics, nrrasse, nrspielername, ovopt, credits, chatoff, chatoffallg, chatoffglobal, helper, trade_reminder, patime FROM de_user_data WHERE user_id=?",
  [$_SESSION['ums_user_id']]);
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
  "SELECT owner_id, lageberichte FROM de_login WHERE user_id=?",
  [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$owner_id = intval($row['owner_id']);
$lageberichte = intval($row['lageberichte']);

//maximalen tick auslesen
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
            $errmsg .= '<div class="mod-meldung mod-meldung-fehler">Im Spielernamen d&uuml;rfen keine Sonderzeichen sein (Ausnahmen sind nur: _-=).</div>';
        } else {
            $db_daten = mysqli_execute_query($GLOBALS['dbi'],
              "SELECT user_id FROM de_user_data WHERE (spielername=? OR nrspielername=?) AND spielername!=?",
              [$spielername, $spielername, $_SESSION['ums_spielername']]);
            $vorhanden = mysqli_num_rows($db_daten);
            if ($vorhanden > 0) {
                $errmsg .= '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['fehler5'].'</div>';
            }
        }
    } else {
        $errmsg = '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['fehler2'].'</div>';
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
            $errmsg .= '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['fehler3'].'</div>';
            break;
    }

    //wenn alles ok ist, daten in der db ablegen
    if ($errmsg == '') {
        mysqli_execute_query($GLOBALS['dbi'],
          "UPDATE de_user_data SET nrspielername=?, nrrasse=? WHERE user_id=?",
          [$spielername, $gewrasse, $_SESSION['ums_user_id']]);
        $nrrasse = $gewrasse;
        $nrspielername = $spielername;
        $okmsg .= '<div class="mod-meldung mod-meldung-ok">Die Einstellungen f&uuml;r die n&auml;chste Runde sind gespeichert.</div>';
    }
}

if (isset($_POST['graop'])) {
    if ($errmsg == '') {
        $chat = intval($_POST['chat'] ?? 0);
        $chatallg = intval($_POST['chatallg'] ?? 0);
        $chatglobal = intval($_POST['chatglobal'] ?? 0);
        $helper = intval($_POST['helper'] ?? 0);
        $traderem = intval($_POST['traderem'] ?? 0);
        $lageberichte = intval($_POST['lageberichte'] ?? 0) === 1 ? 1 : 0;

        mysqli_execute_query($GLOBALS['dbi'],
          "UPDATE de_user_data SET chatoff=?, chatoffallg=?, chatoffglobal=?, helper=?, trade_reminder=? WHERE user_id=?",
          [$chat, $chatallg, $chatglobal, $helper, $traderem, $_SESSION['ums_user_id']]);
        mysqli_execute_query($GLOBALS['dbi'],
          "UPDATE de_login SET lageberichte=? WHERE user_id=?",
          [$lageberichte, $_SESSION['ums_user_id']]);
        $errmsg .= '<div class="mod-meldung mod-meldung-ok">'.$options_lang['uebernommen'].'</div>';
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
if ($delacc) { //account löschen
    $delpass = $_POST['delpass'];
    $delcheck1 = $_POST['delcheck1'] ?? '';
    $delcheck2 = $_POST['delcheck2'] ?? '';

    $db_datenx = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT * FROM de_login WHERE user_id=?",
      [$_SESSION['ums_user_id']]);
    $rowx = mysqli_fetch_assoc($db_datenx);

    $passwordOK = false;
    if (password_verify(trim($delpass), $rowx['pass'])) {
        $passwordOK = true;
    }

    if ($passwordOK) { //oldpass ist korrekt
        if ($delcheck1 == "1" and $delcheck2 == "1") {//lösche
            //überprüfen ob man evtl. allianzleader ist, da ist es notwendig den posten aufzugeben
            $db_daten = mysqli_execute_query($GLOBALS['dbi'],
              "SELECT * FROM de_allys WHERE leaderid=?",
              [$_SESSION['ums_user_id']]);
            $num = mysqli_num_rows($db_daten);
            if ($num == 0) {//man ist kein leader
                $uid = $_SESSION['ums_user_id'];

                //3 tage umode und dann killen, wenn er sich nicht mehr einloggt
                $urltage = 3;
                $tis = time() + 86400 * $urltage;
                $datum = date("Y-m-d H:i:s", $tis);

                mysqli_execute_query($GLOBALS['dbi'],
                  "UPDATE de_login SET last_login=?, status=3, inaktmail=1, delmode=1 WHERE user_id=?",
                  [$datum, $uid]);

                //ehlock, damit man für eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                mysqli_execute_query($GLOBALS['dbi'],
                  "UPDATE de_user_data SET ehlock=? WHERE user_id=?",
                  [$newtick, $uid]);

                //mail an den accountinhaber schicken
                $db_daten = mysqli_execute_query($GLOBALS['dbi'],
                  "SELECT reg_mail FROM de_login WHERE user_id=?",
                  [$_SESSION['ums_user_id']]);
                $row = mysqli_fetch_assoc($db_daten);
                $reg_mail = $row['reg_mail'];
                @mail_smtp($reg_mail, $options_lang['emailgeloeschtbetreff'].' - '.$sv_server_name, $options_lang['emailgeloeschtbody'], 'FROM: '.$GLOBALS['env_mail_noreply']);

                session_destroy();
                header("Location: geloescht.php");
            } else {
                $errmsg = '<div class="mod-meldung mod-meldung-fehler">Gib bitte zuerst Deinen Posten als Allianzleiter auf. Du kannst den Posten &uuml;bertragen, oder die Allianz l&ouml;schen.</div>';
            }
        } else {
            $errmsg = '<div class="mod-meldung mod-meldung-fehler">Setze bitte beide H&auml;kchen um den Account zu l&ouml;schen.</div>';
        }
    } else {
        $errmsg .= '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['umodefehler2'].'</div>';
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $options_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar
include('resline.php');

//Darstellung per Cookie (gilt je Gerät); die Ausgabe hat schon begonnen, darum per JavaScript gesetzt
function opt_cookie_skript($name, $value)
{
    return '<script>(function(){ var ablauf = new Date(); ablauf.setTime(ablauf.getTime() + (3600 * 24 * 360 * 1000));'
        .' document.cookie = "'.$name.'='.$value.'; expires=" + ablauf.toUTCString() + "; path=/"; })();</script>';
}

if (isset($_REQUEST['set_use_mobile_version'])) {
    $value = intval($_REQUEST['set_use_mobile_version']);
    echo opt_cookie_skript('use_mobile_version', $value);
    $_COOKIE['use_mobile_version'] = $value;
}

if (isset($_REQUEST['set_deactivate_swipe'])) {
    $value = intval($_REQUEST['set_deactivate_swipe']);
    echo opt_cookie_skript('deactivate_swipe', $value);
    $_COOKIE['deactivate_swipe'] = $value;
}

if (isset($_REQUEST['desktop_version'])) {
    $value = intval($_REQUEST['desktop_version']);
    echo opt_cookie_skript('desktop_version', $value);
    $_COOKIE['desktop_version'] = $value;
}
$desktop_version = intval($_COOKIE['desktop_version'] ?? 0);

$urlacc = $_POST['urlacc'] ?? false;
$showattumode=0;
if ($urlacc) { //account in urlaubsmodus versetzen
    $urlpass = $_POST['urlpass'];
    $db_datenx = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT * FROM de_login WHERE user_id=?",
      [$_SESSION['ums_user_id']]);
    $rowx = mysqli_fetch_assoc($db_datenx);

    $passwordOK = false;
    if (password_verify(trim($urlpass), $rowx['pass'])) {
        $passwordOK = true;
    }

    if ($passwordOK) { //oldpass ist korrekt
        $urltage = intval($_POST['urltage']);
        if ($urltage >= 3 and $urltage <= 21) {
            //schauen ob der account angegriffen wird
            if (($_POST['attumodecheck'] ?? 0) == 1) {
                $gea = '&nbsp;';
            }
            if ($gea == '&nbsp;') {
                //wenn keine fehler vorliegen, dann umode setzen
                if ($errmsg == '') {
                    $uid = $_SESSION['ums_user_id'];
                    $tis = time() + 86400 * $urltage;
                    $datum = date("Y-m-d H:i:s", $tis);

                    mysqli_execute_query($GLOBALS['dbi'],
                      "UPDATE de_login SET last_login=?, status=3 WHERE user_id=?",
                      [$datum, $uid]);

                    //ehlock, damit man für eine bestimmte zeitspanne vom eh-kampf ausgeschlossen ist
                    $newtick = $maxtick + ($sv_benticks * $ehlockfaktor);
                    mysqli_execute_query($GLOBALS['dbi'],
                      "UPDATE de_user_data SET ehlock=? WHERE user_id=?",
                      [$newtick, $uid]);

                    session_destroy();
                    header("Location: urlaub.php");
                }
            } else {
                $errmsg .= '<div class="mod-meldung mod-meldung-warn">'.$options_lang['umodefehler3'].'</div>';
                $showattumode = 1;
            }
        } else {
            $errmsg .= '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['umodefehler1'].'</div>';
        }
    } else {
        $errmsg .= '<div class="mod-meldung mod-meldung-fehler">'.$options_lang['umodefehler2'].'</div>';
    }
}

if ($errmsg != '' || $okmsg != '') {
    echo '<div class="mod pol-meldungen">'.$errmsg.$okmsg.'</div>';
}

//Kontrollkästchen als Zeile: Text links, Haken rechts, die ganze Zeile ist klickbar
function opt_option($name, $text, $an)
{
    return '<label class="opt-option"><span>'.$text.'</span><input type="checkbox" name="'.$name.'" value="1"'.($an == 1 ? ' checked' : '').'></label>';
}

$ehlock_hinweis = '<div class="mod-hinweis opt-hinweis">'.$options_lang['accountloescheninfo3'].' <b>'.number_format($sv_benticks * $ehlockfaktor, 0, "", ".").'</b></div>';

/////////////////////////////////////////////////////////////
// Konto und Darstellung
/////////////////////////////////////////////////////////////
rahmen_oben($options_lang['title']);
echo '<div class="mod opt">';

echo '<div class="opt-zeile"><span>Accountdaten, Logins und Einstellungen der &Uuml;bersicht</span><a href="userdetails.php" target="h" class="mod-btn mod-btn-leise ally-btn-klein">'.$options_lang['userdetails'].'</a></div>';

if(!isset($_COOKIE['use_mobile_version'])){
    $_COOKIE['use_mobile_version'] = 0;
}
if ($_COOKIE['use_mobile_version'] == 0) {
    echo '<div class="opt-zeile"><span>Version: <span class="mod-chip">Desktop</span> <small>(wird erst nach dem n&auml;chsten Login wirksam)</small></span><a href="options.php?set_use_mobile_version=1" class="mod-btn mod-btn-leise ally-btn-klein">Zu Mobil wechseln</a></div>';
} else {
    echo '<div class="opt-zeile"><span>Version: <span class="mod-chip">Mobil</span> <small>(wird erst nach dem n&auml;chsten Login wirksam)</small></span><a href="options.php?set_use_mobile_version=0" class="mod-btn mod-btn-leise ally-btn-klein">Zu Desktop wechseln</a></div>';
}

if(!isset($_COOKIE['deactivate_swipe'])){
    $_COOKIE['deactivate_swipe'] = 0;
}
if ($_COOKIE['deactivate_swipe'] == 0) {
    echo '<div class="opt-zeile"><span>Wischgesten (Mobilversion): <span class="mod-chip mod-chip-gruen">an</span></span><a href="options.php?set_deactivate_swipe=1" class="mod-btn mod-btn-leise ally-btn-klein">Ausschalten</a></div>';
} else {
    echo '<div class="opt-zeile"><span>Wischgesten (Mobilversion): <span class="mod-chip">aus</span></span><a href="options.php?set_deactivate_swipe=0" class="mod-btn mod-btn-leise ally-btn-klein">Einschalten</a></div>';
}

echo '</div>';
rahmen_unten();

/////////////////////////////////////////////////////////////
// allgemeine Einstellungen
/////////////////////////////////////////////////////////////
rahmen_oben($options_lang['allgemeineeinstellungen']);
echo '<form action="options.php" method="POST" class="mod opt">';

echo '<div class="opt-feld"><label for="opt_desktop">Desktopversion</label>';
echo '<select name="desktop_version" id="opt_desktop" class="mod-eingabe">';
echo '<option value="0"'.($desktop_version == 0 ? ' selected' : '').'>Standard</option>';
echo '<option value="1"'.($desktop_version == 1 ? ' selected' : '').'>Classic</option>';
echo '</select></div>';
echo '<div class="opt-klein">Die Standardversion wird ab einer horizontalen Aufl&ouml;sung von 1280px empfohlen. Die &Auml;nderung wird erst nach dem n&auml;chsten Login wirksam.</div>';

echo '<div class="opt-optionen">';
echo opt_option('chatallg', 'Server-Chat-Channel deaktivieren', $chatoffallg);
echo opt_option('chatglobal', 'globalen Chat-Channel deaktivieren', $chatoffglobal);
echo opt_option('helper', $options_lang['helferaktivieren'], $helperon);
echo opt_option('traderem', 'Missionshilfe aktivieren', $trade_reminder);
echo opt_option('lageberichte', $exile_lang['option_lageberichte'], $lageberichte);
echo '</div>';

echo '<div class="opt-fuss"><button type="submit" name="graop" value="'.$options_lang['einstellungenspeichern'].'" class="mod-btn">'.$options_lang['einstellungenspeichern'].'</button></div>';
echo '</form>';
rahmen_unten();

/////////////////////////////////////////////////////////////
// einstellungen für die nächste runde
/////////////////////////////////////////////////////////////
if ($nrrasse == 1) {
    $rasse = 'Ewiger';
} elseif ($nrrasse == 2) {
    $rasse = 'Ishtar';
} elseif ($nrrasse == 3) {
    $rasse = 'K&#180;Tharr';
} elseif ($nrrasse == 4) {
    $rasse = 'Z&#180;tah-ara';
}

rahmen_oben($options_lang['einstellungennaechsterunde']);
echo '<form action="options.php" method="POST" class="mod opt">';
echo '<input type="hidden" name="donr" value="1">';
echo '<div class="opt-feld"><label for="opt_rasse">'.$options_lang['rasse'].'</label><select name="rasse" id="opt_rasse" class="mod-eingabe">';
foreach (array(1 => 'Ewiger', 2 => 'Ishtar', 3 => 'K&#180;Tharr', 4 => 'Z&#180;tah-ara') as $wert => $name) {
    echo '<option value="'.$wert.'"'.($wert == $nrrasse ? ' selected' : '').'>'.$name.'</option>';
}
echo '</select></div>';
echo '<div class="opt-feld"><label for="opt_name">'.$options_lang['spielername'].'</label>';
echo '<input type="text" name="spielername" id="opt_name" maxlength="20" value="'.$nrspielername.'" class="mod-eingabe" autocomplete="off"></div>';
echo '<div class="opt-klein">Erlaubt sind Buchstaben, Ziffern und _-=. Sollte der Name in der neuen Runde nicht richtig angezeigt werden, dann logge Dich bitte aus und wieder ein.</div>';
echo '<div class="opt-fuss"><button type="submit" name="nrbu" value="'.$options_lang['datenspeichern'].'" class="mod-btn">'.$options_lang['datenspeichern'].'</button></div>';
echo '</form>';
rahmen_unten();

/////////////////////////////////////////////////////////////
// urlaubsmodus
/////////////////////////////////////////////////////////////
rahmen_oben($options_lang['urlaubsmodus']);
echo '<form action="options.php" method="POST" class="mod opt">';
echo '<div class="opt-text">Um den Account in den Urlaubsmodus zu versetzen, die Anzahl der Urlaubstage (mindestens 3, h&ouml;chstens 21) und das Passwort eingeben und dann mit &bdquo;'.$options_lang['urlaubsmodusaktivieren'].'&ldquo; best&auml;tigen.</div>';
echo $ehlock_hinweis;
//überprüfen ob man angegriffen wird
if ($showattumode == 1) {
    echo '<label class="opt-option opt-option-warn"><span>'.$options_lang['umodefehler3desc'].'</span><input name="attumodecheck" type="checkbox" value="1"></label>';
}
echo '<div class="opt-feld"><label for="opt_urltage">'.$options_lang['urlaubstage'].' (3&ndash;21)</label><input type="text" name="urltage" id="opt_urltage" value="" maxlength="2" inputmode="numeric" autocomplete="off" class="mod-eingabe opt-kurz"></div>';
echo '<div class="opt-feld"><label for="opt_urlpass">'.$options_lang['passwort'].'</label><input type="password" name="urlpass" id="opt_urlpass" value="" class="mod-eingabe" autocomplete="current-password"></div>';
echo '<div class="opt-fuss"><button type="submit" name="urlacc" value="'.$options_lang['urlaubsmodusaktivieren'].'" class="mod-btn">'.$options_lang['urlaubsmodusaktivieren'].'</button></div>';
echo '</form>';
rahmen_unten();

/////////////////////////////////////////////////////////////
// account löschen
/////////////////////////////////////////////////////////////
rahmen_oben($options_lang['accountloeschen']);
echo '<form action="options.php" method="POST" class="mod opt">';
echo '<div class="opt-text">'.$options_lang['accountloescheninfo1'].'</div>';
echo $ehlock_hinweis;
echo '<div class="opt-feld"><label for="opt_delpass">'.$options_lang['passwort'].'</label><input type="password" name="delpass" id="opt_delpass" value="" class="mod-eingabe" autocomplete="current-password"></div>';
echo '<div class="opt-optionen">';
echo '<label class="opt-option"><span>'.$options_lang['bestaetigung'].' 1</span><input name="delcheck1" type="checkbox" value="1"></label>';
echo '<label class="opt-option"><span>'.$options_lang['bestaetigung'].' 2</span><input name="delcheck2" type="checkbox" value="1"></label>';
echo '</div>';
echo '<div class="opt-fuss"><button type="submit" name="delacc" value="'.$options_lang['accountloeschen'].'" class="mod-btn mod-btn-gefahr">'.$options_lang['accountloeschen'].'</button></div>';
echo '</form>';
rahmen_unten();
?>
</body>
</html>
