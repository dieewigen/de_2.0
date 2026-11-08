<?php
use DieEwigen\DE2\View\Vote\PollResult;

include('inc/lang/' . $sv_server_lang . '_vote.lang.php');
include_once "functions.php";

////////////////////////////////////////////////////////////
// diese Datei wird von der resline.php eingebunden
// sie führt die Umfrage aus
////////////////////////////////////////////////////////////

// Request Parameter absichern
$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

// User-Daten holen
$sql = "SELECT submit FROM de_user_info WHERE user_id=?";
$db_daten_vote = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row_vote = mysqli_fetch_assoc($db_daten_vote);

$vote = intval($_REQUEST['vote'] ?? 0);

//Einleitung: erst nach der Abstimmung geht es im Spiel weiter; zentriert wie sonst über die Rohstoffleiste
echo '<div align="center" class="vt-seite">';
echo '<div class="mod pol-meldungen"><div class="mod-hinweis vt-einleitung">'.$resline_lang['vote'].'</div>';

// Wenn Formular abgesendet wurde
if (isset($_REQUEST['subform']) && $vote > 0) {
    $id = intval($id);

    $sql = "SELECT vote_id FROM de_vote_stimmen WHERE user_id=? AND vote_id=?";
    $db_check = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $id]);

    //nur berechtigte Spieler (registriert vor Umfragestart, wie in der Liste) und nur vorhandene Antworten
    $sql = "SELECT u.id, u.status, u.antworten FROM de_vote_umfragen u, de_login l WHERE u.id=? AND l.user_id=? AND UNIX_TIMESTAMP(l.register)<UNIX_TIMESTAMP(u.startdatum)";
    $vote_aktiv = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id, $_SESSION['ums_user_id']]);
    $aktiv = mysqli_fetch_assoc($vote_aktiv);
    $menge = mysqli_num_rows($db_check);
    $anzahl_antworten = $aktiv ? count(explode("|", $aktiv['antworten'])) : 0;

    if ($menge == 0 && $aktiv && $aktiv['status'] == 1 && $vote <= $anzahl_antworten) {
        if ($vote != "0" && $vote != "") {
            echo '<div class="mod-meldung mod-meldung-ok">'.$vote_lang['msg_3'].'</div>';

            //Prüfen und Eintragen in einer Anweisung (MyISAM sperrt dafür die Tabelle): zwei gleichzeitige
            //Anfragen ergeben so nur eine Stimme. Ein UNIQUE-Index geht nicht, das Admin-Tool legt pro Umfrage
            //mehrere Platzhalter mit user_id=0 an (ourdetool/umfragen.php).
            $sql = "INSERT INTO de_vote_stimmen (user_id, vote_id, votefor)
                    SELECT ?, ?, ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM de_vote_stimmen WHERE user_id=? AND vote_id=?)";
            mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $id, $vote, $_SESSION['ums_user_id'], $id]);
            $_SESSION['ums_vote'] = 0;
        } else {
            echo '<div class="mod-meldung mod-meldung-fehler">'.$vote_lang['msg_4'].'</div>';
        }
    } else {
        echo '<div class="mod-meldung mod-meldung-fehler">'.$vote_lang['msg_5'].'</div>';
    }
}
echo '</div>';

// Übersicht anzeigen
if ($action == "" || $action == "uebersicht") {
    rahmen_oben(ucfirst($vote_lang['aktuelleumfragen']));
    echo '<div class="mod vt">';

    $sql = "SELECT vote_id FROM de_vote_stimmen WHERE user_id=?";
    $schonabgestimmt = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
    $gevotetevotes = [];
    while ($rew = mysqli_fetch_assoc($schonabgestimmt)) {
        $gevotetevotes[] = $rew['vote_id'];
    }

    $votevorhanden = 0;
    $sql = "SELECT de_vote_umfragen.id, de_vote_umfragen.frage, de_vote_umfragen.startdatum FROM de_vote_umfragen, de_login WHERE de_vote_umfragen.status=1 AND UNIX_TIMESTAMP(de_login.register)<UNIX_TIMESTAMP(de_vote_umfragen.startdatum) AND de_login.user_id=? ORDER BY de_vote_umfragen.id";
    $db_umfrage = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);

    while ($row = mysqli_fetch_assoc($db_umfrage)) {
        if (!in_array($row['id'], $gevotetevotes)) {
            echo '<div class="vt-zeile"><span class="vt-frage">' . $row['frage'] . '</span>';
            echo '<a href="overview.php?action=abstimmen&amp;id=' . $row['id'] . '" class="mod-btn">Abstimmen</a></div>';
            $votevorhanden = 1;
        }
    }

    if ($votevorhanden == 0) {
        echo '<div class="mod-leer">' . $vote_lang['msg_2'] . '</div>';
        echo '<div class="vt-fuss"><a href="overview.php" class="mod-btn">Weiter</a></div>';
    }

    echo '</div>';
    rahmen_unten();
}

// Alte Umfrage anzeigen
elseif ($action == "show") {
    $sql = "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?";
    $db_checkobende = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);

    if (mysqli_num_rows($db_checkobende) > 0) {
        $row = mysqli_fetch_assoc($db_checkobende);
        echo PollResult::render($row, $vote_lang);
    }
}

// Abstimmen
elseif ($action == "abstimmen") {
    $sql = "SELECT id, frage, hinweis, antworten FROM de_vote_umfragen WHERE id=? AND status=1";
    $db_umfrage = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
    $row = mysqli_fetch_assoc($db_umfrage);
    $vorhanden = mysqli_num_rows($db_umfrage);

    if ($vorhanden > 0) {
        rahmen_oben($row['frage']);
        echo '<form action="overview.php" method="post" class="mod vt">';
        if (trim((string)$row['hinweis']) != '') {
            echo '<div class="mod-hinweis vt-hinweis">' . nl2br($row['hinweis']) . '</div>';
        }

        //Antworten als große Flächen; ohne Auswahl lässt sich nicht absenden
        echo '<div class="vt-antworten">';
        $antworten = explode("|", $row['antworten']);
        foreach ($antworten as $index => $antwort) {
            echo '<label class="vt-antwort"><input type="radio" name="vote" value="' . ($index + 1) . '" required><span>' . $antwort . '</span></label>';
        }
        echo '</div>';

        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<div class="vt-fuss"><button type="submit" name="subform" value="' . $vote_lang['stimmeabgeben'] . '" class="mod-btn">' . $vote_lang['stimmeabgeben'] . '</button></div>';
        echo '</form>';
        rahmen_unten();
    } else {
        echo '<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-fehler">' . $vote_lang['msg_7'] . '</div></div>';
    }
}
echo '</div>';
?>

</body>

</html>
