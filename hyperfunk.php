<?php

use DieEwigen\DE2\View\Hyperfunk\ComposeForm;

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_hyperfunk.lang.php');

//Max Anzahl dir HFN's  im Archiv | Eintr?ge in der Buddy/Ignoreliste
$sv_hf_buddie_p = 20;
$sv_hf_ignore_p = 20;
$sv_hf_archiv_p = 20;

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, newtrans, newnews, sector, `system` FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row['score'];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$asec = $row['sector'];
$asys = $row['system'];
$sector = $asec;
$system = $asys;

if ($newtrans == 1) { //wenn einen neue nachricht vorlag, den indikator wieder auf 0 setzen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newtrans = 0 WHERE user_id=?", [$_SESSION['ums_user_id']]);
}
$newtrans = 0;

$l = $_REQUEST['l'] ?? '';

include_once 'functions.php';
// Sperre f&uuml;r den Fall, dass user das Script abbrechen.
@ignore_user_abort();
// eine Funktion, die die Statusmeldungen anzeigt; $color: r = Fehler, g/b = erledigt
function insertmessage($message, $color, $lang_systemnachricht)
{
    $art = ($color == "r") ? 'fehler' : 'ok';
    return '<div class="mod hf-meldungen"><div class="mod-meldung mod-meldung-' . $art . '">' . $message . '</div></div>';
}

//offene Formatierungen (b, i, u, center, font, a) am Ende einer Nachricht schließen, sonst gelten sie für alles danach
function hf_schliessen($html)
{
    $offen = array();
    preg_match_all('#<(/?)(b|i|u|center|font|a)\b[^>]*>#i', $html, $m, PREG_SET_ORDER);
    foreach ($m as $tag) {
        $name = strtolower($tag[2]);
        if ($tag[1] == '') {
            $offen[] = $name;
        } else {
            $pos = array_search($name, array_reverse($offen, true), true);
            if ($pos !== false) {
                unset($offen[$pos]);
                $offen = array_values($offen);
            }
        }
    }
    foreach (array_reverse($offen) as $name) {
        $html .= '</' . $name . '>';
    }
    return $html;
}

?>
<!doctype html>
<html>
<head>
<title><?php echo $hyperfunk_lang['headtitle']?></title>
<?php include "cssinclude.php";
$action = $_REQUEST['action'] ?? 'eingang';
$se = $_REQUEST['se'] ?? '';
$sy = $_REQUEST['sy'] ?? '';
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');

//Meldungen der Aktionen sammeln, sie stehen unter den Reitern
ob_start();
// Insert abschnitt f&uuml;r normale HFNs mit s&auml;mtlichen Ueberpruefungen (Ignore Urlaub falsche koords)
if (isset($_POST['antbut'])) {
    $zielsek = intval($_POST['zielsek']);
    $zielsys = intval($_POST['zielsys']);

    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    //$nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);

    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT com_sperre FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $row = mysqli_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($nachricht == "") {
        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif (validdigit($zielsek) && validdigit($zielsys)) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$zielsek, $zielsys]);
        @$num = mysqli_num_rows($db_daten);

        $uid_row = mysqli_fetch_array($db_daten);
        @$uid = $uid_row ? $uid_row['user_id'] : null;

        $db_ignore = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id=? and `system`=? and sector=? and status=2", [$uid, $asys, $asec]);
        $numignore = mysqli_num_rows($db_ignore);


        $db_aktiv = mysqli_execute_query($GLOBALS['dbi'], "SELECT status FROM de_login WHERE user_id=?", [$uid]);
        $rowaktiv = mysqli_fetch_array($db_aktiv);


        if ($num == 1) {
            if ($numignore == "0") {
                if ($rowaktiv['status'] == "1") {

                    $time = date("YmdHis");

                    mysqli_execute_query($GLOBALS['dbi'], "INSERT into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values (?, ?, ?, ?, ?, ?, ?, ?, 0)", [$uid, $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, $betreff, $nachricht]);
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values (?, ?, ?, ?, ?, ?, ?, ?, 1)", [$uid, $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, $betreff, $nachricht]);

                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newtrans = 1 WHERE user_id=? and user_id!=?", [$uid, $_SESSION['ums_user_id']]);

                    echo insertmessage($hyperfunk_lang['msg_2'], "g", $hyperfunk_lang['systemnachricht']);

                    $action = "";
                } else {
                    echo insertmessage($hyperfunk_lang['msg_5'], "r", $hyperfunk_lang['systemnachricht']);
                }
            } else {
                echo insertmessage($hyperfunk_lang['msg_4'], "r", $hyperfunk_lang['systemnachricht']);
            }
        } else {
            echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
        }
    }
}
// Insert f&uuml;r die Sektornachricht
$sekmsg = $_POST['sekmsg'] ?? '';
if ($sekmsg && $asec == 1) {
    echo insertmessage($hyperfunk_lang['msg_6'], "r", $hyperfunk_lang['systemnachricht']);
}
if ($sekmsg && $asec != 1) {
    $time = date("YmdHis");

    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    //$nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);


    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT com_sperre FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $row = mysqli_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($nachricht == "") {

        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } else {
        $sekhfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=?", [$asec]);
        $igmsg = 0;
        while ($row = mysqli_fetch_array($sekhfn)) {

            $db_ignore = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_hfn_buddy_ignore WHERE user_id=? and `system`=? and sector=? and status=2", [$row['user_id'], $asys, $asec]);
            $numignore = mysqli_num_rows($db_ignore);

            if ($numignore == 0) {
                mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newtrans=1 where user_id=? and user_id!=?", [$row['user_id'], $_SESSION['ums_user_id']]);
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)", [$row['user_id'], $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, 'Sektorrundmail: '.$betreff, $nachricht]);
            } else {
                $igmsg++;
            }
        }
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)", [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, 'Sektorrundmail: '.$betreff, $nachricht]);


        if ($igmsg == 0) {
            echo insertmessage($hyperfunk_lang['msg_7'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_8'], "g", $hyperfunk_lang['systemnachricht']);
        }
    }
}

// insert f&uuml;r die Alli HFN die bein Co/Leader landet
if (isset($_POST['allimsg'])) {

    $time = date("YmdHis");

    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    //$nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);


    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT com_sperre FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
    $row = mysqli_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($nachricht == "") {
        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } else {
        $holalli = mysqli_execute_query($GLOBALS['dbi'], "SELECT ally_id, status FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $row = mysqli_fetch_array($holalli);
        $ally_id = $row['ally_id'];

        //nur Mitglieder einer Allianz, nicht Bewerber (die haben ally_id schon gesetzt, aber status=0);
        //ohne Allianz würde die Empfängerliste über ally_id=0 alle allianzlosen Spieler treffen
        if ($row['status'] != 1 || $ally_id < 1) {
            echo insertmessage('Eine Allianzrundmail k&ouml;nnen nur Mitglieder einer Allianz senden.', "r", $hyperfunk_lang['systemnachricht']);
        } else {
        $resource = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE ally_id = ? AND status=1", [$ally_id]);
        $igmsg = 0;
        while ($rowa = mysqli_fetch_array($resource)) {

            $db_ignore = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_hfn_buddy_ignore WHERE user_id=? and `system`=? and sector=? and status=2", [$rowa['user_id'], $asys, $asec]);
            $numignore = mysqli_num_rows($db_ignore);

            if ($numignore == 0) {
                mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newtrans = 1 where user_id = ? and user_id!=?", [$rowa['user_id'], $_SESSION['ums_user_id']]);
                mysqli_execute_query($GLOBALS['dbi'], "insert into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values (?, ?, ?, ?, ?, ?, ?, ?, 0)", [$rowa['user_id'], $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, 'Allianzrundmail: '.$betreff, $nachricht]);
            } else {
                $igmsg++;
            }

        }
        mysqli_execute_query($GLOBALS['dbi'], "insert into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values (?, ?, ?, ?, ?, ?, ?, ?, 1)", [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $asec, $asys, $_SESSION['ums_spielername'], $time, 'Allianzrundmail: '.$betreff, $nachricht]);
        if ($igmsg == 0) {
            echo insertmessage($hyperfunk_lang['msg_9'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_10'], "g", $hyperfunk_lang['systemnachricht']);
        }
        }


    }

}

//Loeschen einzelner HFNs
if ($action == "del") {//nachricht l&ouml;schen
    $id = intval($_REQUEST['id']);

    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper WHERE (id=? AND empfaenger=? AND sender=0) or (id=? AND absender=? AND sender=1)", [$id, $_SESSION['ums_user_id'], $id, $_SESSION['ums_user_id']]);

    echo insertmessage($hyperfunk_lang['msg_14'], "g", $hyperfunk_lang['systemnachricht']);

    $action = "";

    $o = $_REQUEST['o'] ?? '';
    if ($o == "v") {
        $action = "archiv";
    }
    if ($o == "e") {
        $action = "eingang";
    }
    if ($o == "a") {
        $action = "ausgang";
    }
}
//Loeschen vieler HFNs aus einer Kategorie
if ($action == "da" and ($l == "e" or $l == "a" or $l == "r")) {
    // Eingang
    if ($action == "da" and $l == "e") {
        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper WHERE empfaenger=? and sender=0 and archiv=0 and gelesen=1", [$_SESSION['ums_user_id']]);
        echo insertmessage($hyperfunk_lang['msg_15'], "g", $hyperfunk_lang['systemnachricht']);
    }
    // Ausgang
    if ($action == "da" and $l == "a") {
        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper WHERE absender=? and sender=1 and archiv=0", [$_SESSION['ums_user_id']]);
        echo insertmessage($hyperfunk_lang['msg_16'], "g", $hyperfunk_lang['systemnachricht']);
    }
    // Archiv
    if ($action == "da" and $l == "r") {
        mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_user_hyper WHERE empfaenger=? and archiv=1", [$_SESSION['ums_user_id']]);
        echo insertmessage($hyperfunk_lang['msg_17'], "g", $hyperfunk_lang['systemnachricht']);
    }
}
//Move Funktion der HFNs ins Archiv
if ($action == "arc") {
    $id = intval($_REQUEST['id']);

    $db_archiv = mysqli_execute_query($GLOBALS['dbi'], "SELECT archiv FROM de_user_hyper WHERE empfaenger=? and archiv=1", [$_SESSION['ums_user_id']]);
    $num = mysqli_num_rows($db_archiv);

    $parchiv = $sv_hf_archiv_p;

    if ($num <= ($parchiv - 1)) {
        $se = (int)$se;
        $sy = (int)$sy;
        //if(!preg_match("/^[0-9]*$/i", $t))$t='';

        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_hyper SET archiv=1, absender=?, time=time WHERE fromsec=? AND fromsys=? AND id=? AND empfaenger=? AND sender=0", [$_SESSION['ums_user_id'], $se, $sy, $id, $_SESSION['ums_user_id']]);

        echo insertmessage($hyperfunk_lang['msg_18'], "g", $hyperfunk_lang['systemnachricht']);
    } else {
        echo insertmessage($hyperfunk_lang['msg_19_1'].' '.$parchiv.' '.$hyperfunk_lang['msg_19_2'], "r", $hyperfunk_lang['systemnachricht']);
    }

    $action = "eingang";
}
$hf_meldungen = ob_get_clean();

//Reiter: Ordner mit Zählern, darunter neue Nachricht an Spieler, Sektor oder Allianz
$db_zahl = mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anz FROM de_user_hyper WHERE empfaenger=? AND sender=0 AND archiv=0 AND gelesen=0", [$_SESSION['ums_user_id']]);
$hf_neu = (int)mysqli_fetch_assoc($db_zahl)['anz'];
$db_zahl = mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anz FROM de_user_hyper WHERE empfaenger=? AND archiv=1", [$_SESSION['ums_user_id']]);
$hf_archiv = (int)mysqli_fetch_assoc($db_zahl)['anz'];

$hf_aktiv = $action;
if ($hf_aktiv == '') {
    $hf_aktiv = 'eingang';
} elseif ($hf_aktiv == 'ant' || $hf_aktiv == 'weiter') {
    $hf_aktiv = 'spieler';
} elseif ($hf_aktiv == 'delbuddy' || $hf_aktiv == 'delene') {
    $hf_aktiv = 'optionen';
}
$hf_reiter = array(
    'eingang' => $hyperfunk_lang['eingang'] . ($hf_neu > 0 ? ' <span class="hf-zahl">' . $hf_neu . '</span>' : ''),
    'ausgang' => $hyperfunk_lang['ausgang'],
    'archiv' => $hyperfunk_lang['archiv'] . ' <small>' . $hf_archiv . '/' . $sv_hf_archiv_p . '</small>',
    'optionen' => 'Ignorierliste',
);
echo '<div class="mod ally-navi hf-navi">';
foreach ($hf_reiter as $ziel => $text) {
    echo '<a href="hyperfunk.php?action=' . $ziel . '" class="ally-reiter' . ($hf_aktiv == $ziel ? ' ally-reiter-aktiv' : '') . '">' . $text . '</a>';
}
echo '<span class="hf-navi-text">Neue Nachricht an</span>';
foreach (array('spieler' => $hyperfunk_lang['spieler'], 'sektor' => $hyperfunk_lang['sektor'], 'alli' => $hyperfunk_lang['allianz']) as $ziel => $text) {
    echo '<a href="hyperfunk.php?action=' . $ziel . '" class="ally-reiter' . ($hf_aktiv == $ziel ? ' ally-reiter-aktiv' : '') . '">' . $text . '</a>';
}
echo '</div>';

echo $hf_meldungen;
//Anzeige der s&auml;mtlichen HFNS der jeweiligen Kategorien
if ($action == "eingang"  || $action == "" || $action == "ausgang" || $action == "archiv") {
    if ($action == "eingang"  || $action == "") {
        $titel = $hyperfunk_lang['eingang'];
    } elseif ($action == "ausgang") {
        $titel = $hyperfunk_lang['ausgang'];
    } else {
        $titel = $hyperfunk_lang['archiv'];
    }

    if ($action == "eingang"  || $action == "") {
        if ($l == "new") {
            $db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_hyper WHERE empfaenger=? and sender=0 and archiv=0 and gelesen=0 ORDER BY time DESC", [$_SESSION['ums_user_id']]);
        } else {
            $db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_hyper WHERE empfaenger=? and sender=0 and archiv=0 ORDER BY time DESC", [$_SESSION['ums_user_id']]);
        }
        //nachrichten als gelesen markieren
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_hyper SET time=time, gelesen = 1 WHERE empfaenger=? and sender=0 and archiv=0", [$_SESSION['ums_user_id']]);
    } elseif ($action == "ausgang") {
        $db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_hyper WHERE absender=? and sender='1' ORDER BY time DESC", [$_SESSION['ums_user_id']]);
        //nachrichten als gelesen markieren
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_hyper SET time=time, gelesen = 1 WHERE absender=? AND sender=1", [$_SESSION['ums_user_id']]);
    } elseif ($action == "archiv") {
        $db_tfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_hyper WHERE empfaenger=? and archiv='1' ORDER BY time DESC", [$_SESSION['ums_user_id']]);
    }

    $anzahl = mysqli_num_rows($db_tfn);

    rahmen_oben($titel);
    echo '<div class="mod hf">';

    while ($row = mysqli_fetch_array($db_tfn)) {

        $row['betreff']=htmlspecialchars($row['betreff'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        //stripcslashes VOR dem Escaping: danach würde es aus \x3c wieder ein echtes < machen
        $row['text']=htmlspecialchars(stripcslashes($row['text']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        //ältere Nachrichten aus dem Admin-Tool enthalten gespeicherte <br>, nur diese wieder als Umbruch zulassen
        $row['text'] = preg_replace('/&lt;br\s*\/?&gt;/i', '<br>', $row['text']);

        $row['text'] = preg_replace("/\[b\]/i", "<b>", $row['text']);
        $row['text'] = preg_replace("/\[\/b\]/i", "</b>", $row['text']);

        $row['text'] = preg_replace("/\[i\]/i", "<i>", $row['text']);
        $row['text'] = preg_replace("/\[\/i]/i", "</i>", $row['text']);

        $row['text'] = preg_replace("/\[u]/i", "<u>", $row['text']);
        $row['text'] = preg_replace("/\[\/u]/i", "</u>", $row['text']);

        $row['text'] = preg_replace("/\[center\]/i", "<center>", $row['text']);
        $row['text'] = preg_replace("/\[\/center\]/i", "</center>", $row['text']);

        $row['text'] = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $row['text']);
        $row['text'] = str_replace("[CROT]", "<font color=\"#F10505\">", $row['text']);
        $row['text'] = str_replace("[CW]", "<font color=\"#FFFFFF\">", $row['text']);
        $row['text'] = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $row['text']);
        $row['text'] = str_replace("[CDE]", "<font color=\"#3399FF\">", $row['text']);

        $row['text'] = preg_replace("/\[email\]([^[]*)\[\/email\]/", "<a href=\"mailto:\\1\">\\1</a>", $row['text']);
        //nur http(s)-Links, sonst wäre z. B. javascript: möglich
        $row['text'] = preg_replace("/\[url\](https?:\/\/[^[]*)\[\/url\]/i", '<a href="\\1" target="_blank" rel="noopener">\\1</a>', $row['text']);
        $row['text'] = preg_replace("/\[color=#([^[]+)\]([^[]*)\[\/color\]/", "<font color=\"#\\1\" >\\2</font>", $row['text']);
        $row['text'] = preg_replace("/\[size=([^[]+)\]([^[]*)\[\/size\]/", "<font size=\"\\1\" >\\2</font>", $row['text']);


        $row['text'] = nl2br($row['text']);

        $t = (string)$row['time'];

        $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' '.$t[8].$t[9].':'.$t[10].$t[11];

        $neu = ($l != "new" && $row['gelesen'] == "0");

        //Absender bzw. Empfänger
        if ($action == "ausgang") {
            $empfa = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system`, spielername FROM de_user_data WHERE user_id=?", [$row['empfaenger']]);
            $rowemp = mysqli_fetch_array($empfa);

            $wer = '<span class="hf-richtung">an</span> <b>' . ($rowemp['spielername'] ?? '?') . '</b> <small>' . ($rowemp['sector'] ?? '') . ':' . ($rowemp['system'] ?? '') . '</small>';
            $row['fromsec'] = $rowemp['sector'] ?? null;
            $row['fromsys'] = $rowemp['system'] ?? null;
        } elseif ($row['fromsec'] == "0"  and $row['fromsys'] == "0") {
            $row['fromnic'] = str_replace("Leader", " ", $row['fromnic']);
            $row['fromnic'] = trim($row['fromnic']);
            $wer = '<span class="hf-richtung">von</span> <b>' . $row['fromnic'] . '</b>';
        } else {
            $wer = '<span class="hf-richtung">von</span> <b>' . $row['fromnic'] . '</b> <small>' . $row['fromsec'] . ':' . $row['fromsys'] . '</small>';
        }

        //Aktionen (Bedingungen wie bisher)
        $links = array();
        if ($action == "ausgang" and ($row['fromsec'] != "0" and $row['fromsys'] != "0")) {
            $links[] = '<a href="details.php?se=' . $row['fromsec'] . '&amp;sy=' . $row['fromsys'] . '" class="mod-btn mod-btn-leise ally-btn-klein">' . $hyperfunk_lang['hfn_nav_1'] . '</a>';
        }
        if ($row['fromsec'] == "0" and $row['fromsys'] == "0") {
            $links[] = '<a href="ally_message_leader.php?select=' . $row['fromnic'] . '" class="mod-btn mod-btn-leise ally-btn-klein">' . $row['fromnic'] . '-' . $hyperfunk_lang['hfn_nav_2'] . '</a>';
        }
        if ($action == "" or $action == "archiv" or $action == "eingang" and ($row['fromsec'] != "0" and $row['fromsys'] != "0")) {
            $links[] = '<a href="hyperfunk.php?action=ant&amp;se=' . $row['fromsec'] . '&amp;sy=' . $row['fromsys'] . '&amp;id=' . $row['id'] . '" class="mod-btn mod-btn-leise ally-btn-klein">' . $hyperfunk_lang['hfn_nav_3'] . '</a>';
        }
        if ($action == "" or $action == "archiv" or $action == "ausgang" or $action == "eingang" and ($row['fromsec'] != "0"  and $row['fromsys'] != "0")) {
            $links[] = '<a href="hyperfunk.php?action=weiter&amp;se=' . $row['fromsec'] . '&amp;sy=' . $row['fromsys'] . '&amp;id=' . $row['id'] . '" class="mod-btn mod-btn-leise ally-btn-klein">' . $hyperfunk_lang['hfn_nav_4'] . '</a>';
        }
        if ($action == "" or $action == "eingang") {
            $links[] = '<a href="hyperfunk.php?action=arc&amp;se=' . $row['fromsec'] . '&amp;sy=' . $row['fromsys'] . '&amp;id=' . $row['id'] . '" class="mod-btn mod-btn-leise ally-btn-klein">' . $hyperfunk_lang['hfn_nav_5'] . '</a>';
        }
        $loeschen = '';
        if ($action == "archiv" or $action == "" or $action == "eingang" or $action == "ausgang") {
            $o = '';
            if ($action == "archiv") {
                $o = "v";
            }
            if ($action == "" or $action == "eingang") {
                $o = "e";
            }
            if ($action == "ausgang") {
                $o = "a";
            }
            $loeschen = '<a href="hyperfunk.php?action=del&amp;se=' . $row['fromsec'] . '&amp;sy=' . $row['fromsys'] . '&amp;id=' . $row['id'] . '&amp;o=' . $o . '" class="mod-btn mod-btn-leise mod-btn-gefahr ally-btn-klein" data-bestaetigen="Wirklich l&ouml;schen?">' . $hyperfunk_lang['loeschen'] . '</a>';
        }

        echo '<div class="hf-nachricht' . ($neu ? ' hf-nachricht-neu' : '') . '">';
        echo '<div class="hf-nachricht-kopf"><span class="hf-wer">' . $wer . ($neu ? ' <span class="mod-chip mod-chip-gruen">neu</span>' : '') . '</span><span class="hf-zeit">' . $time . '</span></div>';
        echo '<div class="hf-betreff">' . ($row['betreff'] != '' ? $row['betreff'] : '<span class="hf-leise">ohne Betreff</span>') . '</div>';
        echo '<div class="hf-inhalt">' . hf_schliessen($row['text']) . '</div>';
        echo '<div class="hf-aktionen">' . implode('', $links) . '<span class="hf-aktionen-rechts">' . $loeschen . '</span></div>';
        echo '</div>';
    }

    if ($anzahl != "0") {
        if ($action == "eingang" or $action == "") {
            $l_alle = "e";
        }
        if ($action == "ausgang") {
            $l_alle = "a";
        }
        if ($action == "archiv") {
            $l_alle = "r";
        }
        echo '<div class="hf-fuss"><span class="hf-leise">' . $anzahl . ' ' . ($anzahl == 1 ? 'Nachricht' : 'Nachrichten') . '</span>';
        echo '<a href="hyperfunk.php?action=da&amp;l=' . $l_alle . '" class="mod-btn mod-btn-gefahr ally-btn-klein" data-bestaetigen="Wirklich alle l&ouml;schen?">' . $hyperfunk_lang['alle_loeschen'] . '</a></div>';
    } else {
        echo '<div class="mod-leer">' . $hyperfunk_lang['nohfn'] . '</div>';
    }

    echo '</div>';
    rahmen_unten();
}




// Formular f&uuml;r die saemtlichen Nachrichten
if ($action == "ant" or $action == "weiter" or $action == "spieler" or $action == "sektor" or $action == "alli" or $action == "freunde") {
    $check = 1;
    $allicheck = 1;
    $id = intval($_REQUEST['id'] ?? -1);

    if ($action == "freunde") {
        $db_friends = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system`, name FROM de_hfn_buddy_ignore WHERE user_id=? and status=1", [$_SESSION['ums_user_id']]);
        $num = mysqli_num_rows($db_friends);
        if ($num == "0") {
            $check = 0;
        }
    }

    if ($action == "alli") {
        $db_alli = mysqli_execute_query($GLOBALS['dbi'], "SELECT status FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
        $rowalli = mysqli_fetch_array($db_alli);
        if ($rowalli['status'] == "0") {
            $allicheck = 0;
        }
    }

    if ($allicheck == "1") {
        if ($check == "1") {
            if ($action == "ant" or $action == "weiter") {
                $anttfn = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_hyper WHERE (id=? AND empfaenger=?) or (id=? AND absender=? AND sender=1)", [$id, $_SESSION['ums_user_id'], $id, $_SESSION['ums_user_id']]);
                $rowtfn = mysqli_fetch_array($anttfn);

                $rowtfn['text'] = str_replace("<br />", " ", $rowtfn['text'] ?? '');
            }

            if ($action == "sektor") {
                $titel = 'Sektornachricht verfassen';
                $formular = (new ComposeForm('sekmsg', 'Sektornachricht absenden'))
                    ->withHint('Die Nachricht geht an alle Spieler deines Sektors.');
            } elseif ($action == "alli") {
                $titel = 'Allianznachricht verfassen';
                $formular = (new ComposeForm('allimsg', 'Allianznachricht absenden'))
                    ->withHint('Die Nachricht geht an alle Mitglieder deiner Allianz.');
            } elseif ($action == "freunde") {
                $titel = trim($hyperfunk_lang['freundenachricht']);
                $formular = (new ComposeForm('freundemsg', 'Nachricht absenden'))->withFriends($db_friends);
            } elseif ($action == "ant") {
                $titel = 'Hyperfunknachricht beantworten';
                $formular = (new ComposeForm('antbut', 'Hyperfunknachricht absenden'))->withCoordinates((string)intval($se), (string)intval($sy));
            } elseif ($action == "weiter") {
                $titel = 'Hyperfunknachricht weiterleiten';
                $formular = (new ComposeForm('antbut', 'Hyperfunknachricht absenden'))->withCoordinates();
            } else {
                $titel = 'Hyperfunknachricht verfassen';
                $formular = (new ComposeForm('antbut', 'Hyperfunknachricht absenden'))->withCoordinates();
            }

            if ($action == "ant") {
                $formular->withSubject($hyperfunk_lang['re'].' '.htmlspecialchars($rowtfn['betreff'] ?? '', ENT_QUOTES, 'UTF-8', false));
            } elseif ($action == "weiter") {
                $formular->withSubject($hyperfunk_lang['fw'].' '.htmlspecialchars($rowtfn['betreff'] ?? '', ENT_QUOTES, 'UTF-8', false));
            }
            if ($action == "ant" or $action == "weiter") {
                $formular->withMessage('[i][b]'.($rowtfn['fromnic'] ?? '').' '.$hyperfunk_lang['schrieb'].': [/b]'.umlaut($rowtfn['text']).'[/i]');
            }

            rahmen_oben($titel);
            echo $formular->render();
            rahmen_unten();
        } else {
            echo insertmessage($hyperfunk_lang['msg_20'], "r", $hyperfunk_lang['systemnachricht']);
        }
    } else {
        echo insertmessage($hyperfunk_lang['msg_21'], "r", $hyperfunk_lang['systemnachricht']);
    }
}
//Insert f&uuml;r die Buddyliste
if (isset($_POST['friendbtn'])) {

    $sector = intval($_REQUEST['freundsector'] ?? -1);
    $system = intval($_REQUEST['freundsystem'] ?? -1);

    $db_check = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
    $numcheck = mysqli_num_rows($db_check);

    $db_buddy_exist_check = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_hfn_buddy_ignore WHERE sector=? AND `system`=? and status=1 and user_id=?", [$sector, $system, $_SESSION['ums_user_id']]);
    $buddy_exist_check = mysqli_num_rows($db_buddy_exist_check);

    if ($buddy_exist_check >= 1) {
        echo insertmessage($hyperfunk_lang['msg_22'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($numcheck == 1) {
        $db_buddy = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id=? and status=1", [$_SESSION['ums_user_id']]);
        $num = mysqli_num_rows($db_buddy);

        $pbuddies = $sv_hf_buddie_p;

        if ($num <= ($pbuddies - 1)) {
            $buddyname = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
            $rowbuddy = mysqli_fetch_array($buddyname);

            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_hfn_buddy_ignore (user_id, sector, `system`, name, status) VALUES (?, ?, ?, ?, 1)", [$_SESSION['ums_user_id'], $sector, $system, $rowbuddy['spielername']]);

            echo insertmessage($hyperfunk_lang['msg_23'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_24_1'].' '.$pbuddies.' '.$hyperfunk_lang['msg_24_2'], "r", $hyperfunk_lang['systemnachricht']);
        }
    } else {
        echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
    }

    $action = "optionen";
}
//Insert f&uuml;r die Ignoreliste
if (isset($_POST['ignorebtn'])) {
    $sector = intval($_REQUEST['feindsector'] ?? -1);
    $system = intval($_REQUEST['feindsystem'] ?? -1);

    $db_check = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
    $numcheck = mysqli_num_rows($db_check);

    $db_buddy_exist_check = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_hfn_buddy_ignore WHERE sector=? AND `system`=? and status=2 and user_id=?", [$sector, $system, $_SESSION['ums_user_id']]);
    $buddy_exist_check = mysqli_num_rows($db_buddy_exist_check);

    if ($buddy_exist_check >= 1) {
        echo insertmessage($hyperfunk_lang['msg_25'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($numcheck == 1) {


        $db_enem = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id=? and status=2", [$_SESSION['ums_user_id']]);
        $num = mysqli_num_rows($db_enem);

        $pigno = $sv_hf_ignore_p;

        if ($num <= ($pigno - 1)) {
            $ignorename = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername FROM de_user_data WHERE sector=? AND `system`=?", [$sector, $system]);
            $rowignore = mysqli_fetch_array($ignorename);

            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_hfn_buddy_ignore (user_id, sector, `system`, name, status) VALUES (?, ?, ?, ?, 2)", [$_SESSION['ums_user_id'], $sector, $system, $rowignore['spielername']]);

            echo insertmessage($hyperfunk_lang['msg_26'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_27_1'].' '.$pigno.' '.$hyperfunk_lang['msg_27_2'], "r", $hyperfunk_lang['systemnachricht']);
        }

        $action = "optionen";
    } else {
        echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
    }
}
// Loeschen einzelner Personen aus der Buddyliste
if ($action == "delbuddy") {
    $sector = (int)$se;
    $system = (int)$sy;

    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_hfn_buddy_ignore WHERE user_id=? and sector=? and `system`=? and status=1", [$_SESSION['ums_user_id'], $sector, $system]);

    echo insertmessage($hyperfunk_lang['msg_28'], "g", $hyperfunk_lang['systemnachricht']);

    $action = "optionen";
}
// Loeschen einzelner Personen aus der Ignorelist
if ($action == "delene") {
    $sector = (int)$se;
    $system = (int)$sy;

    mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_hfn_buddy_ignore WHERE user_id=? and sector=? and `system`=? and status=2", [$_SESSION['ums_user_id'], $sector, $system]);

    echo insertmessage($hyperfunk_lang['msg_28'], "g", $hyperfunk_lang['systemnachricht']);

    $action = "optionen";
}
//Optionen: Ignorierliste
if ($action == "optionen") {
    $db_enemy = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, `system`, name FROM de_hfn_buddy_ignore WHERE user_id=? and status=2", [$_SESSION['ums_user_id']]);

    $nume = mysqli_num_rows($db_enemy);

    rahmen_oben('Ignorierliste');
    echo '<div class="mod hf">';
    echo '<div class="ally-hinweis">Von diesen Koordinaten nimmst du keine Hyperfunknachrichten an, auch keine Sektor- oder Allianznachrichten. Zieht ein Spieler um, gilt der Eintrag f&uuml;r ihn nicht mehr.</div>';

    if ($nume == "0") {
        echo '<div class="mod-leer hf-abstand">' . $hyperfunk_lang['keine_feinde'] . '</div>';
    } else {
        echo '<div class="ally-abschnitt"><div class="mod-typ">' . $nume . ' von ' . $sv_hf_ignore_p . ' Eintr&auml;gen</div><div class="hf-liste">';
        while ($row = mysqli_fetch_array($db_enemy)) {
            echo '<div class="hf-zeile"><span><b>' . $row['name'] . '</b> <small>' . $row['sector'] . ':' . $row['system'] . '</small></span>';
            echo '<a href="hyperfunk.php?se=' . $row['sector'] . '&amp;sy=' . $row['system'] . '&amp;action=delene" class="mod-btn mod-btn-leise ally-btn-klein">entfernen</a></div>';
        }
        echo '</div></div>';
    }

    echo '<form action="hyperfunk.php?action=optionen" method="post" class="ally-abschnitt">';
    echo '<div class="mod-typ">' . $hyperfunk_lang['feind_adden'] . '</div>';
    echo '<div class="hf-zeilenformular"><span class="hf-koords">';
    echo '<input type="text" name="feindsector" class="mod-eingabe" inputmode="numeric" autocomplete="off" placeholder="Sek.">';
    echo '<i>:</i>';
    echo '<input type="text" name="feindsystem" class="mod-eingabe" inputmode="numeric" autocomplete="off" placeholder="Sys."></span>';
    echo '<button type="submit" name="ignorebtn" value="' . $hyperfunk_lang['feind_adden'] . '" class="mod-btn">' . $hyperfunk_lang['feind_adden'] . '</button>';
    echo '</div></form>';

    echo '</div>';
    rahmen_unten();
}
?>
</body>
</html>
