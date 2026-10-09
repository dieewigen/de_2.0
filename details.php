<?php

use DieEwigen\DE2\View\Hyperfunk\ComposeForm;

include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/' . $sv_server_lang . '_details.lang.php';
include 'functions.php';

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$allytag = $row["allytag"];
$sector = $row["sector"];
$system = $row["system"];

if ($row['status'] != 1) {
    $allytag = '';
}

//***************************************
$se = intval($_REQUEST['se'] ?? 0);
$sy = intval($_REQUEST['sy'] ?? 0);

if (!isset($zuser_id)) {
    $zuser_id = 0;
}
if (!isset($zowner_id)) {
    $zowner_id = 0;
}

//wenn ein spielername übergeben wird, dann anhand dessen die koordinaten und die user_id ermitteln
if (isset($_REQUEST['sn'])) {

    $sn = $_REQUEST['sn'];
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, sector, `system` FROM de_user_data WHERE spielername=?",
        [$sn]
    );
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) {
        $row = mysqli_fetch_assoc($db_daten);
        $se = $row['sector'];
        $sy = $row['system'];
        $zuser_id = $row['user_id'];
    }
    if ($zuser_id > 0) {
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT owner_id FROM de_login WHERE user_id=?",
            [$zuser_id]
        );
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $row = mysqli_fetch_assoc($db_daten);
            $zowner_id = $row['owner_id'];
        }
    }
}

//Analysieren der Koordinaten, um userid vom ZIEL herauszubekommen
$db_da = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT user_id, allytag, sector, spielername, status, npc, ally_id FROM de_user_data WHERE sector=? AND `system`=?",
    [$se, $sy]
);
$rew = mysqli_fetch_assoc($db_da);
$znpc = 0;
if (($rew['user_id'] ?? 0) > 0) {
    $zuser_id = $rew['user_id'];
    $znpc = $rew['npc'];
}
//Meta or ally check
$zallyId = $rew['ally_id'] ?? 0;
$zIsMetaOrAlly = isMetaOrAlly($zallyId, $pd['ally_id']);

//ggf. noch die owner_id auslesen
if ($zuser_id > 0 && $zowner_id < 1) {
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT owner_id FROM de_login WHERE user_id=?",
        [$zuser_id]
    );
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) {
        $row = mysqli_fetch_assoc($db_daten);
        $zowner_id = $row['owner_id'];
    }
}

?>
<!doctype html>
<html>

<head>
    <title><?php echo $details_lang['title']; ?></title>
    <?php include "cssinclude.php"; ?>
</head>

<?php
echo '<body class="theme-rasse' . $_SESSION['ums_rasse'] . ' ' . (($_SESSION['ums_mobi'] == 1) ? 'mobile' : 'desktop') . '">';

include "resline.php";

//wenn es der eigene Sektor und ein NPC Typ 2 ist, dann NPC-Details aller NPC im Sektor anzeigen
if (($se == $sector || $zIsMetaOrAlly ) && $znpc == 2) {

    // ---------------------------------------------------------------
    // Single-Alien communication mode: ?dialog=1&se=X&sy=Y
    // ---------------------------------------------------------------
    $dialogMode = isset($_REQUEST['dialog']) && intval($_REQUEST['dialog']) === 1;

    if ($dialogMode && $se > 0 && $sy > 0) {

        // Resolve the single target Alien
        $alienRow = null;
        $alienDb  = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT user_id, spielername, sector, `system`, score, col, kartefakt FROM de_user_data WHERE sector=? AND `system`=? AND npc=2",
            [$se, $sy]
        );
        if ($alienDb) {
            $alienRow = mysqli_fetch_assoc($alienDb);
        }

        if (!$alienRow) {
            echo '<div class="info_box font-size16">Alien nicht gefunden.</div>';
            die('</body></html>');
        }

        $alienId   = (int) $alienRow['user_id'];
        $alienName = htmlspecialchars($alienRow['spielername'], ENT_QUOTES, 'UTF-8');

        ?>
        <script>
        window.AlienDialogCfg = {
            alienId: <?php echo $alienId; ?>,
            strings: {
                errLoad:      '<?php echo addslashes($details_lang['alien_dialog_err_load']); ?>',
                connInit:     '<?php echo addslashes($details_lang['alien_dialog_conn_init']); ?>',
                waiting:      '<?php echo addslashes($details_lang['alien_dialog_waiting']); ?>',
                transmitting: '<?php echo addslashes($details_lang['alien_dialog_transmitting']); ?>',
                answered:     '<?php echo addslashes($details_lang['alien_dialog_answered']); ?>',
                expired:      '<?php echo addslashes($details_lang['alien_dialog_expired']); ?>',
                expiredMsg:   '<?php echo addslashes($details_lang['alien_dialog_expired_msg']); ?>',
                cancelled:    '<?php echo addslashes($details_lang['alien_dialog_cancelled']); ?>',
                errConflict:  '<?php echo addslashes($details_lang['alien_dialog_err_conflict']); ?>',
                errSend:      '<?php echo addslashes($details_lang['alien_dialog_err_send']); ?>',
                errSession:   '<?php echo addslashes($details_lang['alien_dialog_err_session']); ?>'
            }
        };
        </script>
        <script src="js/de_alien_dialog.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_alien_dialog.js'); ?>"></script>

        <div class="npc">
            <div class="alien-dialog-wrapper">

                <a href="details.php?se=<?php echo htmlspecialchars((string)$alienRow['sector'], ENT_QUOTES, 'UTF-8'); ?>&amp;sy=<?php echo htmlspecialchars((string)$alienRow['system'], ENT_QUOTES, 'UTF-8'); ?>" class="alien-dialog-back">
                    <?php echo $details_lang['alien_dialog_back']; ?>
                </a>

                <div class="alien-dialog-layout">

                    <!-- Top: Alien info bar -->
                    <div class="alien-dialog-info-bar">
                        <div class="alien-dialog-info-header">
                            <span class="alien-dialog-info-name"><?php echo $alienName; ?></span>
                            <span class="alien-dialog-info-coords">[<?php echo htmlspecialchars((string)$alienRow['sector'], ENT_QUOTES, 'UTF-8'); ?>:<?php echo htmlspecialchars((string)$alienRow['system'], ENT_QUOTES, 'UTF-8'); ?>]</span>
                        </div>
                        <div class="alien-dialog-info-stats">
                            <div class="alien-dialog-info-stat">
                                <img src="gp/g/icon8.png" class="rounded-borders" style="width:16px;height:auto;" title="Punkte">
                                <span class="alien-dialog-info-stat-label">Punkte</span>
                                <span class="alien-dialog-info-stat-value"><?php echo number_format((int)$alienRow['score'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="alien-dialog-info-stat">
                                <img src="gp/g/icon18.png" class="rounded-borders" style="width:16px;height:auto;" title="Kollektoren">
                                <span class="alien-dialog-info-stat-label">Kollektoren</span>
                                <span class="alien-dialog-info-stat-value"><?php echo number_format((int)$alienRow['col'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="alien-dialog-info-stat">
                                <img src="gp/g/icon17.png" class="rounded-borders" style="width:16px;height:auto;" title="Kriegsartefakte">
                                <span class="alien-dialog-info-stat-label">Kriegsartefakte</span>
                                <span class="alien-dialog-info-stat-value"><?php echo number_format((int)$alienRow['kartefakt'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Communication console -->
                    <div class="alien-dialog-console">
                        <div class="alien-dialog-console-title"><?php echo $details_lang['alien_dialog_title']; ?></div>

                        <div class="alien-dialog-status-bar">
                            <span class="alien-dialog-status-label"><?php echo $details_lang['alien_dialog_status_label']; ?></span>
                            <span id="alien-status" class="alien-dialog-status-value">&hellip;</span>
                        </div>

                        <div id="alien-error" class="alien-dialog-error" style="display:none;"></div>

                        <!-- View: select dialog type -->
                        <div id="view-select" style="display:none;">
                            <div class="alien-dialog-select-label"><?php echo $details_lang['alien_dialog_select']; ?></div>
                            <select id="dialog-type-select" class="alien-dialog-select"></select>
                            <div class="alien-dialog-actions">
                                <button class="alien-dialog-btn" onclick="alienDialog.send()"><?php echo $details_lang['alien_dialog_send']; ?></button>
                            </div>
                        </div>

                        <!-- View: transmitting (animation) -->
                        <div id="view-transmitting" style="display:none;">
                            <div class="alien-signal-box">
                                <div class="alien-signal-label"><?php echo $details_lang['alien_dialog_last_signal']; ?></div>
                                <div id="alien-signal-text" class="alien-signal-text"></div>
                            </div>
                        </div>

                        <!-- View: waiting for NPC -->
                        <div id="view-waiting" style="display:none;">
                            <div class="alien-signal-box">
                                <div class="alien-signal-label"><?php echo $details_lang['alien_dialog_last_signal']; ?></div>
                                <div id="waiting-plain" class="alien-signal-plain"></div>
                                <div id="waiting-signal" class="alien-signal-text alien-signal-frozen"></div>
                            </div>
                            <p class="alien-dialog-msg"><?php echo $details_lang['alien_dialog_waiting_msg']; ?></p>
                            <div class="alien-dialog-actions">
                                <button class="alien-dialog-btn" onclick="alienDialog.poll()"><?php echo $details_lang['alien_dialog_poll']; ?></button>
                                <button class="alien-dialog-btn alien-dialog-btn--secondary" onclick="alienDialog.cancel()"><?php echo $details_lang['alien_dialog_cancel']; ?></button>
                            </div>
                        </div>

                        <!-- View: answered / expired / cancelled -->
                        <div id="view-answered" style="display:none;">
                            <div class="alien-answer-box">
                                <div class="alien-answer-label"><?php echo $details_lang['alien_dialog_question_label']; ?></div>
                                <div id="answer-question" class="alien-signal-plain"></div>

                                <div id="answer-encrypted-label" class="alien-answer-label" style="display:none;"><?php echo $details_lang['alien_dialog_answer_encrypted_label']; ?></div>
                                <div id="answer-encrypted" class="alien-signal-text alien-answer-encrypted" style="display:none;"></div>

                                <div class="alien-answer-label"><?php echo $details_lang['alien_dialog_answer_label']; ?></div>
                                <div id="answer-text" class="alien-answer-text"></div>
                            </div>
                            <div class="alien-dialog-actions">
                                <button class="alien-dialog-btn" onclick="alienDialog.newReq()"><?php echo $details_lang['alien_dialog_new']; ?></button>
                            </div>
                        </div>

                        <!-- View: terminal scan lines (decorative overlay) -->
                        <div id="view-terminal" class="alien-terminal-overlay" style="display:none;"></div>

                    </div><!-- /.alien-dialog-console -->
                </div><!-- /.alien-dialog-layout -->
            </div><!-- /.alien-dialog-wrapper -->
        </div><!-- /.npc -->
        <?php
        die('</body></html>');
    }

    // ---------------------------------------------------------------
    // Default: Alien overview for the whole sector
    // ---------------------------------------------------------------

    //alle NPC im Sektor auslesen
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_user_data WHERE sector=? AND npc=2 ORDER BY `system`",
        [$se]
    );

    $npcHTML='';

    while ($row = mysqli_fetch_assoc($db_daten)) {
        if (!isMetaOrAlly($row['ally_id'], $pd['ally_id']) && $row['sector'] != $pd['sector']) {
            continue;
        }
        $npcHTML .= '
        <div class="npc-detail-card">
            <div class="header">
                ' . htmlspecialchars($row['spielername'], ENT_QUOTES, 'UTF-8') . ' [' . $row['sector'] . ':' . $row['system'] . ']
            </div>

            <div class="detail-row">
                <img src="gp/g/icon8.png" class="rounded-borders" style="width: 20px; height: auto;" title="Punkte"> ' . number_format($row['score'], 0, ',', '.') . '
            </div>
            <div class="detail-row">
                <img src="gp/g/icon18.png" class="rounded-borders" style="width: 20px; height: auto;" title="Kollektoren"> ' . number_format($row['col'], 0, ',', '.') . '
            </div>
            <div class="detail-row">
                <img src="gp/g/icon17.png" class="rounded-borders" style="width: 20px; height: auto;" title="Kriegsartefakte"> ' . number_format($row['kartefakt'], 0, ',', '.') . '
            </div>
            <div class="alien-dialog-entry">
                <a href="details.php?se=' . $row['sector'] . '&amp;sy=' . $row['system'] . '&amp;dialog=1" class="alien-dialog-link">'
                    . $details_lang['alien_dialog_link'] .
                '</a>
            </div>
        </div>';
    }

    echo '
    <div class="npc">
        <div class="npc-details">
            '.$npcHTML.'
        </div>
    </div>
    ';


    die('</body></html>');
}elseif ($znpc == 2) {
    //NPC vom anderen Sektor, keine Details anzeigen

    die('
            <div class="info_box font-size16">
                Es stehen keine Informationen zur Verfügung.
            </div>
        </body>
    </html>
    ');
}

////////////////////////////////////////////////////////
//Details nur anzeigen, wenn es der eigene Server ist; in Sektor 1 darf man die Details nicht einsehen
////////////////////////////////////////////////////////
if (!isset($_REQUEST['ctyp']) && !isset($_REQUEST['cid']) && !empty($rew["spielername"]) && $sector != 1) {
    //Anhand der Userid werden hier die Userdetails aus der DB ausgelesen.
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_user_info WHERE user_id=?",
        [$zuser_id]
    );
    $row = mysqli_fetch_assoc($db_daten);

    $ud_all = $row['ud_all'];
    $sektor_sichtbar = ($rew['sector'] == $sector && $sector > 1);
    $ally_sichtbar = ($rew['allytag'] == $allytag && $rew['status'] == 1 && $allytag != '');
    $ud_sector = $sektor_sichtbar ? $row['ud_sector'] : '';
    $ud_ally = $ally_sichtbar ? $row['ud_ally'] : '';

    //Text eines Abschnitts; leer oder ohne Berechtigung als leiser Hinweis
    $det_text = function ($text, $sichtbar, $grund) {
        if (!$sichtbar) {
            return '<div class="det-text det-leise">' . $grund . '</div>';
        }
        if (trim((string)$text) == '') {
            return '<div class="det-text det-leise">keine Angaben</div>';
        }
        return '<div class="det-text">' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</div>';
    };

    //Titel aus der Accountverwaltung (über die owner_id), dieselben wie das Abzeichen im Chat
    $det_titel = array();
    if ($zowner_id > 0) {
        $db_titel = mysqli_execute_query(
            $GLOBALS['dbi_ls'],
            "SELECT t.title FROM ls_user_title ut JOIN ls_title t ON t.title_id=ut.title_id WHERE ut.user_id=? ORDER BY t.title",
            [$zowner_id]
        );
        while ($zeile = mysqli_fetch_assoc($db_titel)) {
            $det_titel[] = $zeile['title'];
        }
    }

    rahmen_oben($details_lang['detailsvon'] . $rew["spielername"]);
    echo '<div class="mod det">';
    echo '<div class="det-kopf"><b>' . $rew["spielername"] . '</b><span class="mod-chip">' . $se . ':' . $sy . '</span>';
    if ($rew['status'] == 1 && $rew['allytag'] != '') {
        echo '<span class="mod-chip">' . $rew['allytag'] . '</span>';
    }
    echo '</div>';
    if (count($det_titel) > 0) {
        echo '<div class="det-abschnitt"><div class="mod-typ">Titel</div><div class="det-titel">';
        foreach ($det_titel as $t) {
            echo '<div>&#x265B; ' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8', false) . '</div>';
        }
        echo '</div></div>';
    }
    echo '<div class="det-abschnitt"><div class="mod-typ">Informationen f&uuml;r alle</div>' . $det_text($ud_all, true, '') . '</div>';
    echo '<div class="det-abschnitt"><div class="mod-typ">Sektorinformationen</div>' . $det_text($ud_sector, $sektor_sichtbar, 'Nur f&uuml;r Spieler aus demselben Sektor sichtbar.') . '</div>';
    echo '<div class="det-abschnitt"><div class="mod-typ">Allianzinformationen</div>' . $det_text($ud_ally, $ally_sichtbar, 'Nur f&uuml;r Mitglieder derselben Allianz sichtbar.') . '</div>';
    echo '</div>';
    rahmen_unten();
}

////////////////////////////////////////////////////////
// Chat-Ignore verwalten
////////////////////////////////////////////////////////
//möchte man einen Eintrag löschen
$del_ignore = isset($_REQUEST['del_ignore']) ? intval($_REQUEST['del_ignore']) : 0;
if ($del_ignore > 0) {
    mysqli_execute_query(
        $GLOBALS['dbi_ls'],
        "DELETE FROM de_chat_ignore WHERE id=? AND owner_id=?",
        [$del_ignore, $_SESSION['ums_owner_id']]
    );
}

//Formular zum Ignorieren, gleich für Spieler vom eigenen und von einem anderen Server
function det_ignorieren($zowner_id)
{
    //man kann sich nicht selbst ignorieren
    if ($zowner_id == $_SESSION['ums_owner_id']) {
        echo '<div class="mod-meldung mod-meldung-fehler">Du kannst Dich nicht selbst ignorieren.</div>';
        return;
    }

    //möchte man einen Spieler zur Ignore-Liste hinzufügen?
    if (isset($_REQUEST['ignore_add']) && $zowner_id > 0) {
        $ignore_until = time() + (3600 * 24 * intval($_REQUEST['ignore_time']));
        $spielername = mb_substr($_REQUEST['ignore_name'] ?? $_REQUEST['sn'], 0, 20);
        mysqli_execute_query(
            $GLOBALS['dbi_ls'],
            "INSERT INTO de_chat_ignore SET owner_id=?, owner_id_ignore=?, score=1, ignore_until=?, spielername=?",
            [$_SESSION['ums_owner_id'], $zowner_id, $ignore_until, $spielername]
        );
    }

    //überprüfen ob der Spieler bereits auf der Ignore-Liste ist
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi_ls'],
        "SELECT * FROM de_chat_ignore WHERE owner_id=? AND owner_id_ignore=? AND ignore_until>?",
        [$_SESSION['ums_owner_id'], $zowner_id, time()]
    );
    $num = mysqli_num_rows($db_daten);

    if ($num == 1) {  // er steht schon drin
        $row = mysqli_fetch_assoc($db_daten);
        echo '<div class="mod-meldung mod-meldung-ok">' . htmlspecialchars($row['spielername'], ENT_QUOTES, 'UTF-8') . ' steht auf deiner Chat-Ignorierliste, bis ' . date("d.m.Y", $row['ignore_until']) . '.</div>';
    } elseif ($zowner_id > 0) { //er steht noch nicht drin
        if (!isset($_REQUEST['ignore_time'])) {
            $_REQUEST['ignore_time'] = 30;
        }
        $ignore_times = array(2, 10, 20, 30, 60, 90, 180, 360);

        echo '<div class="ally-hinweis">Nachrichten dieses Spielers werden dir im Chat nicht mehr angezeigt.</div>';
        echo '<div class="ally-formular det-abstand">';
        echo '<label class="ally-feld"><span class="mod-typ">Name in deiner Liste</span><input name="ignore_name" maxlength="20" value="' . htmlspecialchars($_REQUEST['sn'], ENT_QUOTES, 'UTF-8') . '" autocomplete="off" type="text" class="mod-eingabe"></label>';
        echo '<label class="ally-feld"><span class="mod-typ">Dauer</span><select name="ignore_time" class="mod-eingabe">';
        for ($i = 0; $i < count($ignore_times); $i++) {
            $selected = ($ignore_times[$i] == $_REQUEST['ignore_time']) ? ' selected' : '';
            echo '<option value="' . $ignore_times[$i] . '"' . $selected . '>' . $ignore_times[$i] . ' Tage</option>';
        }
        echo '</select></label>';
        echo '</div>';
        echo '<div class="ally-aktionen"><button type="submit" name="ignore_add" value="hinzuf&uuml;gen" class="mod-btn">Ignorieren</button></div>';
    } else {
        echo '<div class="mod-leer">Es wurde kein Spieler ausgew&auml;hlt.</div>';
    }
}

if (isset($_REQUEST['sn']) && $_REQUEST['sn'] !== '') {
    //unterscheiden zwischen Spieler auf eigenem Server und Spieler auf anderem Server
    echo '<form action="details.php" method="post">';
    if (!empty($_REQUEST['sn'])) {
        echo '<input type="hidden" name="sn" value="' . htmlspecialchars($_REQUEST['sn'], ENT_QUOTES, 'UTF-8') . '">';
    }
    if (!empty($_REQUEST['ctyp'])) {
        echo '<input type="hidden" name="ctyp" value="' . intval($_REQUEST['ctyp']) . '">';
    }
    if (!empty($_REQUEST['cid'])) {
        echo '<input type="hidden" name="cid" value="' . intval($_REQUEST['cid']) . '">';
    }

    rahmen_oben('Im Chat ignorieren: ' . htmlspecialchars($_REQUEST['sn'], ENT_QUOTES, 'UTF-8'));
    echo '<div class="mod det">';
    if (isset($_REQUEST['ctyp']) && isset($_REQUEST['cid'])) { //anderer server
        //aus dem Chat die dazugehörige owner_id holen
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi_ls'],
            "SELECT * FROM de_chat_msg WHERE id=? AND channeltyp=?",
            [intval($_REQUEST['cid']), intval($_REQUEST['ctyp'])]
        );
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $row = mysqli_fetch_assoc($db_daten);
            $zowner_id = $row['owner_id'];
            det_ignorieren($zowner_id);
        } else {
            echo '<div class="mod-meldung mod-meldung-fehler">Der Spieler konnte nicht gefunden werden.</div>';
        }
    } else { //eigener Server
        det_ignorieren($zowner_id);
    }
    echo '</div>';
    rahmen_unten();
    echo '</form>';
}

//HF nur anzeigen, wenn es Spieler vom eigenen Server ist
if (!isset($_REQUEST['ctyp']) && !isset($_REQUEST['cid']) && $se > 0) {
    rahmen_oben($details_lang['hfnverfassen']);
    $formular = new ComposeForm('antbut', $details_lang['hfnabsenden']);
    echo $formular->withCoordinates((string)$se, (string)$sy)->render();
    rahmen_unten();
}

////////////////////////////////////////////////////////
// Eine Liste der im Chat ignorierten Spielern ausgeben
// darüber soll ebenfalls eine Löschung möglich sein
////////////////////////////////////////////////////////
$db_daten = mysqli_execute_query(
    $GLOBALS['dbi_ls'],
    "SELECT * FROM de_chat_ignore WHERE owner_id=? AND ignore_until>?",
    [$_SESSION['ums_owner_id'], time()]
);
$num = mysqli_num_rows($db_daten);
if ($num >= 1) {
    rahmen_oben('Deine Chat-Ignorierliste');
    echo '<div class="mod det"><div class="det-liste">';
    while ($row = mysqli_fetch_assoc($db_daten)) {
        echo '<div class="det-zeile"><b>' . htmlspecialchars($row['spielername'], ENT_QUOTES, 'UTF-8') . '</b>';
        echo '<span class="det-leise">bis ' . date("d.m.Y", $row['ignore_until']) . '</span>';
        echo '<a href="details.php?' . ($se > 0 ? 'se=' . $se . '&amp;sy=' . $sy . '&amp;' : '') . 'del_ignore=' . $row['id'] . '" class="mod-btn mod-btn-leise ally-btn-klein">entfernen</a></div>';
    }
    echo '</div></div>';
    rahmen_unten();
}
?>
</body>
</html>
