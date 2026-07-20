<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

$uid = req_int('uid');

// Aktions-Parameter (Truthiness wie früher: leer = keine Aktion)
$sendhyperfunk = req_str('sendhyperfunk');
$sendmailregdaten = req_str('sendmailregdaten');
$activateaccount = req_str('activateaccount');
$stataktiv = req_str('stataktiv');
$statgesperrt = req_str('statgesperrt');
$staturlaub = req_str('staturlaub');
$locktrade = req_str('locktrade');
$unlocktrade = req_str('unlocktrade');
$statbk = req_str('statbk');
$observationgo = req_str('observationgo');
$kommentar = req_str('kommentar');
$infostomail = req_str('infostomail');
$mail = req_str('mail', 'none');
$savedata = 0; // Initialisiere $savedata

// Initialisiere weitere Variablen aus Formulardaten
$email = (string)($_POST['email'] ?? '');
$loginname = (string)($_POST['loginname'] ?? '');
$spielername = (string)($_POST['spielername'] ?? '');
$lastlogin = (string)($_POST['lastlogin'] ?? '');
$comsperre = (string)($_POST['comsperre'] ?? '');
$vorname = (string)($_POST['vorname'] ?? '');
$nachname = (string)($_POST['nachname'] ?? '');
$strasse = (string)($_POST['strasse'] ?? '');
$plz = (string)($_POST['plz'] ?? '');
$ort = (string)($_POST['ort'] ?? '');
$land = (string)($_POST['land'] ?? '');
$telefon = (string)($_POST['telefon'] ?? '');
$kommentartext = (string)($_POST['kommentartext'] ?? '');
$hyperfunkbetreff = (string)($_POST['hyperfunkbetreff'] ?? '');
$hyperfunktext = (string)($_POST['hyperfunktext'] ?? '');

$rasse = array("Error", "Ewiger", "Isthar", "K`Tharr", "Z`tah-ara");

function modpass($pass)
{
    $pass[0] = "*";
    $pass[1] = "*";
    $pass[2] = "*";
    $pass[3] = "*";
    return ($pass);
}

function insertemp($spieler)
{
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, system, spielername FROM de_user_data WHERE user_id=?", [$spieler]);
    $rowemp = mysqli_fetch_array($result);

    $namekoords = "$rowemp[sector]:$rowemp[system] ($rowemp[spielername])";

    return $namekoords;
}

//alle angelegten user auslesen
$det_anz = 0;
$det_userlist = array();
if ($handle = opendir('user')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' and $file != '..') {
            $fp = fopen('user/' . $file, 'r');
            $det_mail = fgets($fp, 1024);
            fclose($fp);
            $det_name = str_replace(".txt", "", $file);
            $det_userlist[$det_anz][0] = $det_mail;
            $det_userlist[$det_anz][1] = $det_name;
            $det_anz++;
        }
    }
    closedir($handle);
}

/////////////////////////////////////////////////////
// Aktionen verarbeiten (vor jeglicher Ausgabe)
/////////////////////////////////////////////////////
$flash = array();

if ($sendhyperfunk) {
    csrf_require();
    $hyperfunktext = str_replace("\r\n", "<br>", $hyperfunktext);
    $hyperfunktext = str_replace("\n", "<br>", $hyperfunktext);
    $hftime = date("YmdHis");
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, betreff, text, time) VALUES (?, '0', '0', '1', 'Die-Ewigen-Team', ?, ?,?)", [$uid, $hyperfunkbetreff, $hyperfunktext, $hftime]);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newtrans=1 WHERE user_id=?", [$uid]);
    $flash[] = '<div class="flash flash-ok">Hyperfunk wurde gespeichert</div>';
}

if ($sendmailregdaten) {
    csrf_require();
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_login.reg_mail FROM de_user_data, de_login WHERE de_user_data.user_id = ? AND de_login.user_id = ?", [$uid, $uid]);
    $mail_data = mysqli_fetch_array($result);
    $det_email = $GLOBALS['env_mail_noreply'];
    $emailtext = "Hallo, \r\n";
    $emailtext .= "dein Account bei Die-Ewigen (" . $mail_data["spielername"] . ") wurde wegen der Angabe falscher Userdaten bei der Registrierung gesperrt. \r\n";
    $emailtext .= "Bitte erstelle in der Accountverwaltung ein Ticket mit den Daten (Vorname, Nachname, PLZ, Ort, Land).\r\n";
    $emailtext .= "Falls Du Dich nicht meldest, wird dein Account nach Ablauf der Inaktivenfrist gelöscht. \r\n \r\n";
    $emailtext .= "Mit freundlichen Grüßen \r\n";
    $emailtext .= "Dein DE-Team";

    @mail($mail_data["reg_mail"], 'Die Ewigen - Accountsperrung', utf8_encode($emailtext), 'FROM: ' . $det_email);

    $flash[] = '<div class="flash flash-ok">eMail an ' . htmlspecialchars((string)$mail_data["spielername"]) . ' (' . htmlspecialchars((string)$mail_data["reg_mail"]) . ') wurde versandt. (über: ' . htmlspecialchars($det_email) . ')</div>';
    $savedata = 1;
}

if ($activateaccount) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET system=0 WHERE user_id=?", [$uid]);
    $savedata = 1;
}
if ($stataktiv) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status=1 WHERE user_id=?", [$uid]);
    //$savedata=1;
}
if ($statgesperrt) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status=2, supporter=? WHERE user_id=?", [$det_email, $uid]);
    //$savedata=1;
}
if ($staturlaub) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status=3 WHERE user_id=?", [$uid]);
    //$savedata=1;
}
if ($locktrade) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET trade_forbidden='1' WHERE user_id=?", [$uid]);
    //$savedata=1;
}
if ($unlocktrade) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET trade_forbidden='0' WHERE user_id=?", [$uid]);
    //$savedata=1;
}
if ($statbk) {
    csrf_require();
    //sektor rausfinden
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, system FROM de_user_data WHERE user_id=?", [$uid]);
    $db_daten = mysqli_fetch_array($result);
    $sector = $db_daten["sector"];
    $system = $db_daten["system"];

    //zum bk machen und vote zum sk entfernen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET votefor=0 WHERE sector=?", [$sector]);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET bk=? WHERE sec_id=?", [$system, $sector]);

    $savedata = 1;
}

if ($observationgo) {
    csrf_require();
    //Beobachtungsdaten auslesen
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT observation_stat, observation_by FROM de_user_info WHERE user_id=?", [$uid]);
    $observation_daten = mysqli_fetch_array($result);

    //status ändern
    if ($observation_daten['observation_stat'] == 0) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_info SET observation_stat = 1, observation_by = ? WHERE user_id=?", [$det_username, $uid]);
    }
    //$savedata=1;
}

if ($kommentar) { //daten speichern
    csrf_require();
    $savedata = 1;
}
if ($savedata == 1) {
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE reg_mail=?", [$email]);
    $de_login = mysqli_fetch_array($result);
    if (($de_login["user_id"] != $uid) and (intval($de_login["user_id"]) != 0)) {
        $flash[] = '<div class="flash flash-danger">eMail: ' . htmlspecialchars($email) . ' ist bereits vergeben! ID: ' . (int)$de_login["user_id"] . '</div>';
    }

    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_login=?, com_sperre=? WHERE user_id=?", [$lastlogin, $comsperre, $uid]);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_info SET kommentar=?, vorname=?, nachname=?, strasse=?, plz=?, ort=?, land=?, telefon=? WHERE user_id=?",
    [$kommentartext, $vorname, $nachname, $strasse, $plz, $ort, $land, $telefon, $uid]);
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET nic=?, reg_mail=? WHERE user_id=?", [$loginname, $email, $uid]);

    $trade_ins = req_int('trade_ins');
    $trade_acc = req_int('trade_acc');
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET spielername=? WHERE user_id=?", [$spielername, $uid]);
}

if ($mail == "none" && $infostomail == true) {
    $flash[] = '<div class="flash flash-warn"><b>Kein DET ausgew&auml;hlt.</b></div>';
}

if ($infostomail == true && $mail != "none") {
    csrf_require();
    //de_login
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT nic, last_ip, reg_mail, pass, register, last_login, status, logins, last_ip, clicks, pass FROM de_login WHERE user_id=?", [$uid]);
    $de_login = mysqli_fetch_array($result);
    $loginname = $de_login['nic'];
    $email = $de_login['reg_mail'];
    $lastlogin = $de_login['last_login'];

    //de_user_data
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, allytag, sector, system, score, tradescore, sells, col, agent, sonde, rasse, eartefakt, kartefakt, dartefakt, werberid FROM de_user_data WHERE user_id=?", [$uid]);
    $de_user_data = mysqli_fetch_array($result);
    $spielername = $de_user_data['spielername'];

    //de_user_info
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar FROM de_user_info WHERE user_id=?", [$uid]);
    $de_user_info = mysqli_fetch_array($result);
    $vorname = $de_user_info['vorname'];
    $nachname = $de_user_info['nachname'];
    $strasse = $de_user_info['strasse'];
    $plz = $de_user_info['plz'];
    $ort = $de_user_info['ort'];
    $land = $de_user_info['land'];
    $telefon = $de_user_info['telefon'];

    $dse = $de_user_data["sector"];
    $dsy = $de_user_data["system"];

    $infos = '';
    $infos = $infos . '<table border="1" cellpadding="0" cellspacing="0">
  <tr><td>
  <table border="1" cellpadding="0" cellspacing="1">
  <tr>
  <td width="100" align="center">User ID</td>
  <td width="250" align="center">' . $uid . '</td>
  </tr>
  <tr>
  <td align="center">Loginname</td>
  <td align="center">' . $loginname . '</td>
  </tr>
  <tr>
  <td align="center">Spielername</td>
  <td align="center">' . $spielername . '</td>
  </tr>
  <tr>
  <td align="center">E-Mail</td>
  <td align="center" nowrap><A HREF="mailto:' . $email . '">' . $email . '</a></td>
  </tr>
  <tr>
  <td align="center">Letzte IP</td>
  <td align="center">' . $de_login["last_ip"] . '</td>
  </tr>
  <tr>
  <td align="center">Registriert</td>
  <td align="center">' . $de_login["register"] . '</td>
  </tr>
  <tr>
  <td align="center">Zuletzt online</td>
  <td align="center">' . $lastlogin . '</td>
  </tr>
  <tr>
  <td align="center">Logins</td>
  <td align="center">' . $de_login["logins"] . '</td>
  </tr>
  <tr>
  <td align="center">Clicks</td>
  <td align="center">' . $de_login["clicks"] . '</td>
  </tr>
  <tr>
  <td align="center">Allianz</td>
  <td align="center">' . $de_user_data["allytag"] . '</td>
  </tr>
  <tr>
  <td align="center">System</td>
  <td align="center">' . $de_user_data["sector"] . ':' . $de_user_data["system"] . '</td>
  </tr>
  <tr>
  <td align="center">Punkte</td>
  <td align="center">' . $de_user_data["score"] . '</td>
  </tr>
  <tr>
  <td align="center">Sonden</td>
  <td align="center">' . $de_user_data["sonde"] . '</td>
  </tr>
  <tr>
  <td align="center">Agenten/Z&ouml;llner</td>
  <td align="center">' . $de_user_data["agent"] . '</td>
  </tr>
  <tr>
  <td align="center">Kampf-Artefakte</td>
  <td align="center">' . $de_user_data["kartefakt"] . '</td>
  </tr>
  <tr>
  <td align="center">Diplomatie-Artefakte</td>
  <td align="center">' . $de_user_data["dartefakt"] . '</td>
  </tr>
  </table></td>
  <td><table border="1" cellpadding="0" cellspacing="1">
  <tr>
  <td width="100" align="center">Vorname</td>
  <td width="200" align="center">' . $vorname . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Nachname</td>
  <td width="200" align="center">' . $nachname . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Strasse</td>
  <td width="200" align="center">' . $strasse . '</td>
  </tr>
  <tr>
  <td width="100" align="center">PLZ</td>
  <td width="200" align="center">' . $plz . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Ort</td>
  <td width="200" align="center">' . $ort . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Land</td>
  <td width="200" align="center">' . $land . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Telefon</td>
  <td width="200" align="center">' . $telefon . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Geburtsdatum</td>
  <td width="200" align="center">' . $de_user_info["tag"] . '-' . $de_user_info["monat"] . '-' . $de_user_info["jahr"] . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Geschlecht</td>';

    if ($de_user_info["geschlecht"] == 1) $geschlecht = 'm&auml;nnlich';
    else $geschlecht = 'weiblich';

    $infos = $infos . '
  <td width="200" align="center">' . $geschlecht . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Status</td>';

    if ($de_login["status"] == 0) $status = 'Inaktiv';
    elseif ($de_login["status"] == 1) $status = 'Aktiv';
    elseif ($de_login["status"] == 2) $status = 'Gesperrt';
    elseif ($de_login["status"] == 3) $status = 'Urlaub';

    $infos = $infos . '
  <td width="200" align="center">' . $de_login["status"] . ' = ' . $status . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Handelspunkte</td>
  <td width="200" align="center">' . $de_user_data["tradescore"] . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Verk&auml;ufe</td>
  <td width="200" align="center">' . $de_user_data["sells"] . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Kollektoren</td>
  <td width="200" align="center">' . $de_user_data["col"] . '</td>
  </tr>
  <tr>
  <td width="100" align="center">Passwort</td>
  <td width="200" align="center">' . modpass($de_login["pass"]) . '</td>
  </tr>
  <tr>
  <td align="center">Rasse</td>
  <td align="center">' . $rasse[$de_user_data["rasse"]] . '</td>
  </tr>
  <tr>
  <td align="center">Werber ID</td>
  <td align="center">' . $de_user_data["werberid"] . '</td>
  </tr>
  </table></td></tr>
  <tr><td colspan="2"><b><u>Kommentar:</u></b>&nbsp;' . $de_user_info['kommentar'] . '</td></tr></table>';

    //hyperfunknachrichten
    $infos .= '<br>Eingang<br>';
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger=? and sender=0 and archiv=0 ORDER BY time DESC", [$uid]);

    while ($row = mysqli_fetch_array($result)) { //jeder gefundene datensatz wird ausgegeben
        $row['text'] = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $row['text']);
        $row['text'] = str_replace("[CROT]", "<font color=\"#F10505\">", $row['text']);
        $row['text'] = str_replace("[CW]", "<font color=\"#FFFFFF\">", $row['text']);
        $row['text'] = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $row['text']);

        $infos .= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="40%">Absender: ' . $row["fromsec"] . ":" . $row["fromsys"] . ' - ' . $row["fromnic"] . '</td>';
        $t = $row["time"];
        $time = $t[6] . $t[7] . '.' . $t[4] . $t[5] . '.' . $t[0] . $t[1] . $t[2] . $t[3] . ' - ' . $t[8] . $t[9] . ':' . $t[10] . $t[11] . ':' . $t[12] . $t[13];
        $infos .= '<td class="cr" width="60%">Zeit: ' . $time . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">Betreff: ' . $row["betreff"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">' . $row["text"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<br><br>';
    }
    $infos .= '<br>Ausgang<br>';
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT empfaenger, time, betreff, text FROM de_user_hyper WHERE absender=? and sender='1' ORDER BY time DESC", [$uid]);

    while ($row = mysqli_fetch_array($result)) { //jeder gefundene datensatz wird ausgegeben
        $row['text'] = preg_replace('/\[img\]([^\[]*)\[\/img\]/i', '<img src="$1" border=0>', $row['text']);

        $row['text'] = preg_replace('/\[b\]/i', '<b>', $row['text']);
        $row['text'] = preg_replace('/\[\/b\]/i', '</b>', $row['text']);

        $row['text'] = preg_replace('/\[i\]/i', '<i>', $row['text']);
        $row['text'] = preg_replace('/\[\/i\]/i', '</i>', $row['text']);

        $row['text'] = preg_replace('/\[u\]/i', '<u>', $row['text']);
        $row['text'] = preg_replace('/\[\/u\]/i', '</u>', $row['text']);

        $row['text'] = preg_replace('/\[center\]/i', '<center>', $row['text']);
        $row['text'] = preg_replace('/\[\/center\]/i', '</center>', $row['text']);

        $row['text'] = preg_replace('/\[pre\]/i', '<pre>', $row['text']);
        $row['text'] = preg_replace('/\[\/pre\]/i', '</pre>', $row['text']);

        $row['text'] = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $row['text']);
        $row['text'] = str_replace("[CROT]", "<font color=\"#F10505\">", $row['text']);
        $row['text'] = str_replace("[CW]", "<font color=\"#FFFFFF\">", $row['text']);
        $row['text'] = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $row['text']);

        $infos .= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="40%">Empf&auml;nger: ' . insertemp($row['empfaenger']) . '</td>';
        $t = $row["time"];
        $time = $t[6] . $t[7] . '.' . $t[4] . $t[5] . '.' . $t[0] . $t[1] . $t[2] . $t[3] . ' - ' . $t[8] . $t[9] . ':' . $t[10] . $t[11] . ':' . $t[12] . $t[13];
        $infos .= '<td class="cr" width="60%">Zeit: ' . $time . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">Betreff: ' . $row["betreff"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">' . $row["text"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<br><br>';
    }
    $infos .= '<br>Archiv<br>';
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT fromsec, fromsys, fromnic, time, betreff, text FROM de_user_hyper WHERE empfaenger=? and archiv='1' ORDER BY time DESC", [$uid]);

    while ($row = mysqli_fetch_array($result)) { //jeder gefundene datensatz wird ausgegeben
        $row['text'] = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $row['text']);
        $row['text'] = str_replace("[CROT]", "<font color=\"#F10505\">", $row['text']);
        $row['text'] = str_replace("[CW]", "<font color=\"#FFFFFF\">", $row['text']);
        $row['text'] = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $row['text']);

        $infos .= '<table border="0" cellpadding="0" cellspacing="2" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="40%">Absender: ' . $row["fromsec"] . ":" . $row["fromsys"] . ' - ' . $row["fromnic"] . '</td>';
        $t = $row["time"];
        $time = $t[6] . $t[7] . '.' . $t[4] . $t[5] . '.' . $t[0] . $t[1] . $t[2] . $t[3] . ' - ' . $t[8] . $t[9] . ':' . $t[10] . $t[11] . ':' . $t[12] . $t[13];
        $infos .= '<td class="cr" width="60%">Zeit: ' . $time . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">Betreff: ' . $row["betreff"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<table border="0" cellpadding="0" cellspacing="1" width="500">';
        $infos .= '<tr>';
        $infos .= '<td class="cl" width="100%">' . $row["text"] . '</td>';
        $infos .= '</tr>';
        $infos .= '</table>';
        $infos .= '<br><br>';
    }

    $infos .= 'Nachrichten:<br><br>';

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? ORDER BY time DESC";
    $th = 'Alle Nachrichten';
    $result = mysqli_execute_query($GLOBALS['dbi'], $query, [$uid]);
    $infos .= '<table>';

    while ($row = mysqli_fetch_array($result)) { //jeder gefundene datensatz wird ausgegeben
        $t = $row["time"];
        $n = $row["typ"];
        $time = $t[6] . $t[7] . '.' . $t[4] . $t[5] . '.' . $t[0] . $t[1] . $t[2] . $t[3] . ' - ' . $t[8] . $t[9] . ':' . $t[10] . $t[11] . ':' . $t[12] . $t[13];
        $infos .= '<tr>';
        $infos .= '<td class="cl">' . $time . '</td>';
        $infos .= '</tr>';
        $infos .= '<tr>';
        $infos .= '<td class="cl">' . $row["text"] . '</td>';
        $infos .= '</tr>';
    }
    $infos .= '</table>';

    $header = "From:OurDETool <Support@Die-Ewigen.com>\n";
    $header .= "Content-Type: text/html";

    $betreff = "$sv_server_name:  $dse : $dsy";

    if (@mail($mail, $betreff, $infos, $header)) {
        $flash[] = '<div class="flash flash-ok"><b>Infos erfolgreich an ' . htmlspecialchars($mail) . ' &uuml;bermittelt.</b></div>';
    } else {
        $flash[] = '<div class="flash flash-danger">Es ist ein Fehler beim Versenden aufgetreten.</div>';
    }
}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//daten ausgeben
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

$page_title = 'Info';
include "inc.layout.top.php";
$active_usertab = 'info';
include "inc.usertoolbar.php";

foreach ($flash as $flash_msg) {
    echo $flash_msg;
}
?>
<script type="text/javascript">
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

 switch(NewMsg) {
  case "Multi":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen Multi\r\n");
   break;
  case "Farming":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen Farming\r\n");
   break;
  case "PWS":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen PW-Sharing\r\n");
   break;
  case "FUD":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen falschen Userdaten\r\n");
   break;
  case "SBeleid":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen extremer Beleidigung\r\n");
   break;
  case "ZUBeleid":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - Zwangsurlaub wegen Beleidigung\r\n");
   break;
  case "AccWG":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - gesperrt wegen (Verdacht auf) Accountweitergabe\r\n");
   break;
  case "UserHSG":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - User hat sich gemeldet\r\n");
   break;
  case "REAKT":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - Account (nach Absprache) wieder aktiviert\r\n");
   break;
  case "ZID":
   AddText("zugehörige ID(s): ");
   break;
  case "MailRD":
   AddText(sDateTime + " <?= htmlspecialchars($det_username) ?> - Mail wegen Userdaten wurde gesendet\r\n");
   break;
 }
 document.getElementById("kommentartext").focus();
}
</script>
<form action="info.php?uid=<?= $uid ?>" method="post">
<?= csrf_field() ?>
<?php
if ($uid > 0) {
    //de_login
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_login WHERE user_id=?", [$uid]);
    $de_login = mysqli_fetch_array($result);

    //de_user_data
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$uid]);
    $de_user_data = mysqli_fetch_array($result);

    //de_user_info
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar, observation_stat, observation_by FROM de_user_info WHERE user_id=?", [$uid]);
    $de_user_info = mysqli_fetch_array($result);

    // Beobachtungsliste ?
    if ($de_user_info["observation_stat"] == 1) {
        $observation_status = '<span class="r">' . htmlspecialchars((string)$de_user_info['observation_by']) . '</span>';
    } else {
        $observation_status = "-";
    }

    echo '<table cellpadding="0" cellspacing="1">';
    echo '<tr>';
    echo '<td width="150px" align="center">Kooperation</td>';
    $coop = 'keine';
    echo '<td width="250px" align="center">' . $coop . '</td>';
    echo '<td width="150px" align="center">Status</td>';
    if ($de_login["status"] == 0) $status = 'Inaktiv';
    elseif ($de_login["status"] == 1) $status = 'Aktiv';
    elseif ($de_login["status"] == 2) $status = 'Gesperrt';
    elseif ($de_login["status"] == 3) $status = 'Urlaub';
    echo '<td width="250px" align="center">' . $status . '[' . $de_login["status"] . '] (<a href="' . csrf_url('info.php?stataktiv=1&uid=' . $uid) . '">aktiv</a>/<a href="' . csrf_url('info.php?statgesperrt=1&uid=' . $uid) . '" data-confirm="User ' . $uid . ' wirklich sperren?">gesperrt</a>/<a href="' . csrf_url('info.php?staturlaub=1&uid=' . $uid) . '">urlaub</a>)</td>';

    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">User ID</td>';
    echo '<td align="center">' . $uid . ' (<a href="' . csrf_url('info.php?observationgo=1&uid=' . $uid) . '">beobachten</a>)</td>';
    echo '<td align="center">Beobachter</td>';
    echo '<td align="center">' . $observation_status . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Hauptaccount ID</td>';
    echo '<td align="center"><a href="https://login.bgam.es/ourdetool/idinfo.php?UID=' . $de_login['owner_id'] . '" target="_blank" rel="noopener">' . $de_login["owner_id"] . '</a></td>';
    echo '<td align="center">Logins</td>';
    echo '<td align="center">' . $de_login["logins"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Loginname</td>';
    echo '<td align="center"><input type="text" name="loginname" value="' . htmlspecialchars((string)$de_login["nic"]) . '"></td>';
    echo '<td align="center">Clicks</td>';
    echo '<td align="center">' . $de_login["clicks"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Spielername</td>';
    echo '<td align="center"><input type="text" name="spielername" value="' . htmlspecialchars((string)$de_user_data["spielername"]) . '"></td>';
    echo '<td align="center">Punkte</td>';
    echo '<td align="center">' . $de_user_data["score"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">E-Mail</td>';
    echo '<td align="center"><input type="text" name="email" value="' . htmlspecialchars((string)$de_login["reg_mail"]) . '"> <a href="mailto:' . htmlspecialchars((string)$de_login["reg_mail"]) . '">anmailen</a></td>';
    echo '<td align="center">Sonden / Agenten / Z&ouml;llner</td>';
    echo '<td align="center">' . $de_user_data["sonde"] . ' / ' . $de_user_data["agent"] . ' / ' . $de_user_data["agent_lost"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Letzte IP</td>';

    echo '<td align="center"><a href="sameip.php?lip=' . $de_login["last_ip"] . '" target="_blank" rel="noopener">' . $de_login["last_ip"] . '</a>
            </td>';

    echo '<td align="center">Kriegs-Artefakte</td>';
    echo '<td align="center">' . $de_user_data["kartefakt"] . '</td>';

    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Registriert</td>';
    echo '<td align="center">' . $de_login["register"] . '</td>';
    echo '<td align="center">Koordinaten</td>';
    echo '<td align="center">' . $de_user_data["sector"] . ':' . $de_user_data["system"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Zuletzt online</td>';
    echo '<td align="center"><input type="text" name="lastlogin" value="' . htmlspecialchars((string)$de_login["last_login"]) . '"></td>';
    echo '<td align="center">Allianz</td>';
    echo '<td align="center">' . $de_user_data["allytag"] . '</td>';
    echo '</tr>';
    echo '<tr>';

    echo '<td align="center">Rasse</td>';
    echo '<td align="center">' . $rasse[$de_user_data["rasse"]] . '</td>';
    echo '<td align="center">Kollektoren</td>';
    echo '<td align="center">' . $de_user_data["col"] . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="center">Com-Sperre</td>';
    echo '<td align="center"><input type="text" name="comsperre" value="' . htmlspecialchars((string)$de_login["com_sperre"]) . '"></td>';
    echo '</tr>';
    echo '<tr>';

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
    echo '<textarea name="kommentartext" id="kommentartext" cols="130" rows="20">' . htmlspecialchars((string)$de_user_info["kommentar"]) . '</textarea>';

    echo '</td></tr>';
    echo '<tr><td align="center">';
    echo '<br><input type="Submit" name="kommentar" value="aktuelle Daten speichern" style="width:175px; color:#ff0000;"><br>';

    echo '<hr>eMail an User senden: ';
    echo "<input type=\"Submit\" name=\"sendmailregdaten\" value=\"gesperrt wegen Userdaten\" onclick=\"AddMsg('MailRD')\" style=\"width:150px;\"> ";
    echo "<input type=\"Button\" name=\"sendmailsonstig\" value=\"Sonstige Mails\" onclick=\"window.open('mails.php?uid=" . $uid . "','_blank')\" style=\"width:150px;\"> ";

    echo '</td></tr>';
    echo '<tr><td align="center">';

    echo '<hr>Hyperfunk an den aktuellen User senden:<br>';
    echo 'Betreff: <input type="text" name="hyperfunkbetreff" value="" style="width:535px;"><br>';
    echo '<textarea name="hyperfunktext" id="hyperfunktext" cols="90" rows="5"></textarea><br>';
    echo '<input type="Submit" name="sendhyperfunk" value="Hyperfunk senden" style="width:150px;"><hr>';

    echo '</td></tr>';
    echo '</table>';
    echo '</form>';
    echo '<form action="info.php?uid=' . $uid . '" method="post">';
    echo csrf_field();

    echo '<select name="mail" size="1" style="width:130px;">
  <option value="' . htmlspecialchars($det_email) . '">' . htmlspecialchars($det_username) . '</option>';
    for ($i = 0; $i < $det_anz; $i++)
        echo '<option value="' . htmlspecialchars($det_userlist[$i][0]) . '">' . htmlspecialchars($det_userlist[$i][1]) . '</option>';

    echo '</select>
  <input type="Submit" name="infostomail" value="Userinfos anfordern" style="width:130px;">';
} else {
    echo 'Kein User ausgew&auml;hlt.';
}
?>
</form>
<?php
include "inc.layout.bottom.php";
