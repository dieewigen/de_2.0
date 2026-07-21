<?php

session_start();
include "inc/sv.inc.php";
include "inc/links.inc.php";
include 'inc/lang/'.$sv_server_lang.'_botcheck.lang.php';
include 'inc/lang/'.$sv_server_lang.'_index.lang.php';
include 'inccon.php';

$_SESSION['ums_user_id'] = $_SESSION['ums_user_id'] ?? -1;

//antwortfenster: schneller als $minsekunden schafft kein mensch (bild laden,
//aufgabe lesen, rechnen, button suchen), aelter als $maxsekunden ist verfallen.
//die produktionswerte koennen in der nicht versionierten sv.inc.php
//abweichend gesetzt werden
$minsekunden = $GLOBALS['sv_botcheck_minsec'] ?? 2;
$maxsekunden = $GLOBALS['sv_botcheck_maxsec'] ?? 600;

//ziel fuer alle redirects
$ziel = $_SESSION['ums_bot_protection_filename'] ?? '';
if ($ziel == '') {
    $ziel = 'overview.php';
}

/************************************************************
*                                                           *
*   Token pruefen: aufrufe ohne gueltigen einmal-token      *
*   (fremdseiten-aufruf/CSRF, doppelklick, veralteter       *
*   tab, bild nie geladen) werden OHNE strafe               *
*   zurueckgeleitet; die laufende abfrage bleibt dabei      *
*   unangetastet.                                           *
*                                                           *
*************************************************************/

$token = $_SESSION['botcheck_token'] ?? '';
$uebergeben = $_REQUEST['t'] ?? '';

//kein aktiver check, bild nie geladen (keine antwort in der session) oder
//t ist kein string -> neutral. wichtig: leerer token darf nie gegen einen
//leeren parameter "passen" (hash_equals('','') waere true)
if ($token == '' || !isset($_SESSION['botcheck_answer']) || !is_string($uebergeben)) {
    header("Location: ".$ziel);
    exit;
}

//fremder oder alter token -> neutral, session NICHT anfassen, sonst koennte
//eine fremdseite die laufende abfrage des spielers invalidieren
if (!hash_equals($token, $uebergeben)) {
    header("Location: ".$ziel);
    exit;
}

//gueltiger versuch: aufgabe ist ab jetzt verbraucht (einmalgebrauch)
$antwort = $_SESSION['botcheck_answer'];
$pagetime = (int)($_SESSION['botcheck_page_time'] ?? 0);
unset($_SESSION['botcheck_token']);
unset($_SESSION['botcheck_answer']);
unset($_SESSION['botcheck_task']);
unset($_SESSION['botcheck_page_time']);

//abgelaufene abfrage: keine strafe, es erscheint eine neue aufgabe
$alter = time() - $pagetime;
if ($alter > $maxsekunden) {
    header("Location: ".$ziel);
    exit;
}

//zu schnelle antworten sind ein botsignal: admin informieren und wie eine
//falsche antwort behandeln
$zuschnell = ($alter < $minsekunden);
if ($zuschnell) {
    @mail($GLOBALS['env_admin_email'], $sv_server_tag.'botcheck zu schnell ('.$alter.'s) user_id '.$_SESSION['ums_user_id'], time(), 'FROM: '.$GLOBALS['env_admin_email']);
}

if (!$zuschnell && (int)($_REQUEST['nummer'] ?? -1) === (int)$antwort) {
    //die sessionzeit aktualisieren
    $_SESSION['ums_session_start'] = time();
    //für den serverübergreifenden botschutz den wert in eine datei schreiben
    $botfilename = '../botcheck/'.$_SESSION["ums_owner_id"].'.txt';
    $botfile = fopen($botfilename, 'w');
    fputs($botfile, $_SESSION['ums_session_start']);
    fclose($botfile);

    //den botaccess counter zurücksetzen
    $_SESSION['botaccesscounter'] = 0;

    //die Daten GET/POST/REQUEST zurücksetzen
    $_SESSION['restore_botcheck_data'] = 1;

    //points zurücksetzen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = 0 WHERE user_id=?", [$_SESSION['ums_user_id']]);

    //zurück auf die ursprungsdatei weiterleiten
    header("Location: ".$ziel);
    exit;
} else { //botschutz falsch beantwortet

    //fehlercounter erhöhen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = points + 1 WHERE user_id=?", [$_SESSION['ums_user_id']]);

    //test ob man schon die maximale fehleranzahl erreicht hat
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT points FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $row = mysqli_fetch_array($db_daten);
    if (isset($row['points']) && $row['points'] >= 10) {
        $fehlermsg = $index_lang['falschesergebnisgesperrt'];
        $time = date("Y-m-d H:i:s");
        $comment = mysqli_execute_query($GLOBALS['dbi'], "SELECT kommentar FROM de_user_info WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $rowz = mysqli_fetch_array($comment);
        $eintrag = "$rowz[kommentar]\nAutomatische Sperrung wegen Botverdacht. Botgrafik zu oft falsch gelöst. \n$time";
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_info SET kommentar=? WHERE user_id=?", [$eintrag, $_SESSION['ums_user_id']]);
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET status=2, points=0 WHERE user_id=?", [$_SESSION['ums_user_id']]);

        //Spieler informieren
        echo '<!DOCTYPE html>
<html lang="de">
<head>';
        include "cssinclude.php";
        echo '</head>';
        echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
        echo '<br><center><div class="info_box text2">Ihr Ergebnis war mehrfach nicht richtig und der Acounnt wurde aus Sicherheitsgr&uuml;nden gesperrt.<br><br>
  		Nehmen sie bitte Kontakt mit dem Support auf.</div>';
        echo '</body></html>';
        session_destroy();
        exit;
    }

    //logout
echo '<!DOCTYPE html>
<html lang="de">
<head>';
    include "cssinclude.php";
    echo '</head>';
    echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

    echo '<br><center><div class="info_box text2">'.$botcheck_lang['error1'].'<br>'.$botcheck_lang['error2'].':<br><br><a href="'.$sv_link[1].'">Login</a></div>';
    echo '</body></html>';
    @session_destroy();
    exit;
}
