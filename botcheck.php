<?php

session_start();
$ergebnis = $_SESSION['loginzahl'] ?? -1;
include "inc/sv.inc.php";
include 'inc/lang/'.$sv_server_lang.'_botcheck.lang.php';
include 'inc/lang/'.$sv_server_lang.'_index.lang.php';
include 'inccon.php';

$_SESSION['ums_user_id'] = $_SESSION['ums_user_id'] ?? -1;

if ($ergebnis == md5('night'.$_REQUEST['nummer'].'fall')) {
    //$_SESSION['ums_one_way_bot_protection']=0;
    //die sessionzeit aktualisieren
    $_SESSION['ums_session_start'] = time();
    //für den serverübergreifenden botschutz den wert in eine datei schreiben
    $botfilename = '../botcheck/'.$_SESSION["ums_owner_id"].'.txt';
    $botfile = fopen($botfilename, 'w');
    fputs($botfile, $_SESSION['ums_session_start']);
    fclose($botfile);

    //das ergebnis aus sicherheitsgr�nden l�schen
    $_SESSION['loginzahl'] = md5(mt_rand(1000000, 2000000));

    //den botaccess counter zur�cksetzen
    $_SESSION['botaccesscounter'] = 0;

    //die Daten GET/POST/REQUEST zur�cksetzen
    $_SESSION['restore_botcheck_data'] = 1;

    //points zurücksetzen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = 0 WHERE user_id=?", [$_SESSION['ums_user_id']]);

    $sekundenbiszumlogout = ($_SESSION['ums_session_start'] + $sv_session_lifetime) - time();
    $restminuten = floor($sekundenbiszumlogout / 60);
    $restsekunden = $sekundenbiszumlogout - ($restminuten * 60);

    //zur�ck auf die ursprungsdatei weiterleiten
    if ($_SESSION['ums_bot_protection_filename'] == '') {
        $_SESSION['ums_bot_protection_filename'] = 'overview.php';

        //wenn nur efta aktiv ist, dann auch nur efta-inhalte anzeigen
        if ($sv_efta_in_de == 0) {
            $_SESSION['ums_bot_protection_filename'] = 'eftaindex.php';
        }

        //wenn nur sou aktiv ist, dann auch nur sou-inhalte anzeigen
        if ($sv_sou_in_de == 0) {
            $_SESSION['ums_bot_protection_filename'] = 'sou_main.php';
        }
    }

    //efta-ajax-rpc
    //$_SESSION['ums_bot_protection_filename'] = str_replace('efta_ajaxrpc.php', 'eftamain.php', $_SESSION['ums_bot_protection_filename']);

    header("Location: ".$_SESSION['ums_bot_protection_filename']);
} else { //botschutz falsch beantwortet

    //fehlercounter erh�hen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = points + 1 WHERE user_id=?", [$_SESSION['ums_user_id']]);

    //test ob man schon ie maximale fehleranzahl erreicht hat
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT points FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $row = mysqli_fetch_array($db_daten);
    if (isset($row['points']) && $row['points'] >= 10) {
        $fehlermsg = $index_lang['falschesergebnisgesperrt'];
        $time = strftime("%Y-%m-%d %H:%M:%S");
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

    echo '<br><center><div class="info_box text2">'.$botcheck_lang['error1'].'<br>'.$botcheck_lang['error2'].':<br><br><a href="index.php">Login</a></div>';
    echo '</body></html>';
    @session_destroy();
    exit;
}
