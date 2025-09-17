<?php
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

$vote = $_REQUEST['vote'] ?? 0;

// Wenn Formular abgesendet wurde
if (isset($_REQUEST['subform']) && $vote > 0) {

    $sql = "SELECT vote_id FROM de_vote_stimmen WHERE user_id=? AND vote_id=?";
    $db_check = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $id]);

    $sql = "SELECT id, status FROM de_vote_umfragen WHERE id=?";
    $vote_aktiv = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
    $aktiv = mysqli_fetch_assoc($vote_aktiv);
    $menge = mysqli_num_rows($db_check);

    if ($menge == 0 && $aktiv['status'] == 1) {
        if ($vote != "0" && $vote != "") {
            echo '<div class="info_box mt20"><span class="text3">'.$vote_lang['msg_3'].'</span></div>';

            $sql = "INSERT INTO de_vote_stimmen (user_id, vote_id, votefor) VALUES (?, ?, ?)";
            mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $id, $vote]);
            $_SESSION['ums_vote'] = 0;
        } else {
            echo $vote_lang['msg_4'];
        }
    } else {
        echo '<h1>' . $vote_lang['msg_5'] . '</h1>';
    }
}

// Übersicht anzeigen
if ($action == "" || $action == "uebersicht") {
?>

    <br><br>
    <table border="0" cellpadding="0" cellspacing="0" width="500">
        <tr height="37" align="center">
            <td class="rol">&nbsp;</td>
            <td class="ro">
                <div class="cellu"><?php echo $vote_lang['aktuelleumfragen']; ?></div>
            </td>
            <td class="ror">&nbsp;</td>
        </tr>

        <?php
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
                echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
                echo '<td class="cell"><a href="overview.php?action=abstimmen&id=' . $row['id'] . '">' . $row['frage'] . '</a></td><td class="rr" width="13">&nbsp;</td></tr>';
                $votevorhanden = 1;
            }
        }

        if ($votevorhanden == 0) {
            echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cell">' . $vote_lang['msg_2'] . '<br><a href="overview.php">weiter</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
        }
        ?>
        <tr height="20">
            <td class="rul" width="13">&nbsp;</td>
            <td class="ru">&nbsp;</td>
            <td class="rur" width="13">&nbsp;</td>
        </tr>
    </table>

    <?php
}

// Alte Umfrage anzeigen
elseif ($action == "show") {
    $sql = "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?";
    $db_checkobende = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);

    if (mysqli_num_rows($db_checkobende) > 0) {
        $row = mysqli_fetch_assoc($db_checkobende);
    ?>
        <br><br>
        <table border="0" cellpadding="0" cellspacing="0" width="600">
            <tr height="37" align="center">
                <td class="rol">&nbsp;</td>
                <td class="ro">
                    <div class="cellu"><?php echo $row['frage']; ?></div>
                </td>
                <td class="ror">&nbsp;</td>
            </tr>
            <tr>
                <td class="rl" width="13">&nbsp;</td>
                <td class="cell">

                    <table border="0" width="100%" cellspacing="2" cellpadding="0">
                        <tr>
                            <td class="cell">&nbsp;<?php echo $vote_lang['start']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['startdatum']); ?></td>
                        </tr>
                        <tr>
                            <td class="cell">&nbsp;<?php echo $vote_lang['ende']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['enddatum']); ?></td>
                        </tr>
                        <tr>
                            <td class="cell">&nbsp;<?php echo $vote_lang['hinweis']; ?>: <?php echo nl2br($row['hinweis']); ?></td>
                        </tr>

                        <?php
                        $antworten = explode("|", $row['antworten']);
                        $ergebnisse = explode("|", $row['ergebnisse']);
                        $stimmen = explode("|", $row['stimmen']);

                        $stimmengesamt = 0;
                        $farbe = 0;

                        for ($i = 0; $i < count($antworten); $i++) {
                            $anzahl = $ergebnisse[$i];
                            $prozente = ($stimmen[0] > 0) ? number_format(($anzahl * 100) / $stimmen[0], 2, ",", ".") : '0,00';
                            echo '<tr class="cell">
    <td height="25">&nbsp;' . $antworten[$i] . '</td>
    <td>&nbsp;<img src="' . 'gp/' . 'g/vote/l' . $farbe . '.gif" border="0"><img src="' . 'gp/' . 'g/vote/m' . $farbe . '.gif" border="0" width="' . $prozente . '" height="9"><img src="' . 'gp/' . 'g/vote/r' . $farbe . '.gif"></td>
    <td width="40" nowrap>&nbsp;' . $anzahl . '</td>
    <td width="50" nowrap>&nbsp;' . $prozente . '%</td></tr>';

                            $stimmengesamt += $anzahl;
                            $farbe = ($farbe == 0) ? 1 : 0;
                        }
                        ?>
                        <tr class="cell" height="25">
                            <td colspan="2" align="right"><b><?php echo $vote_lang['insgesamt']; ?>:</b>&nbsp;&nbsp;</td>
                            <td>&nbsp;<?php echo $stimmengesamt; ?></td>
                            <td></td>
                        </tr>

                    </table>
                </td>
                <td class="rr" width="13">&nbsp;</td>
            </tr>
            <tr>
                <td class="rul" width="13">&nbsp;</td>
                <td class="ru">&nbsp;</td>
                <td class="rur" width="13">&nbsp;</td>
            </tr>
        </table>

    <?php
    }
}

// Abstimmen
elseif ($action == "abstimmen") {
    $sql = "SELECT id, frage, hinweis, antworten FROM de_vote_umfragen WHERE id=? AND status=1";
    $db_umfrage = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
    $row = mysqli_fetch_assoc($db_umfrage);
    $vorhanden = mysqli_num_rows($db_umfrage);

    if ($vorhanden > 0) {
    ?>
        <form action="overview.php" method="post">
            <table class="mt20" width="500" cellspacing="0" cellpadding="0">
                <tr height="37" align="center">
                    <td class="rol">&nbsp;</td>
                    <td class="ro"><?php echo $row['frage']; ?></td>
                    <td class="ror"></td>
                </tr>
                <tr>
                    <td class="rl">&nbsp;</td>
                    <td class="cell">
                        <fieldset>
                            <legend><b><?php echo $vote_lang['hinweis']; ?></b></legend><?php echo nl2br($row['hinweis']); ?>
                        </fieldset>
                    </td>
                    <td class="rr"></td>
                </tr>

                <?php
                $antworten = explode("|", $row['antworten']);
                foreach ($antworten as $index => $antwort) {
                    echo '<tr><td class="rl"></td><td class="cell"><input type="radio" name="vote" value="' . ($index + 1) . '">&nbsp;' . $antwort . '</td><td class="rr"></td></tr>';
                }
                ?>
                <tr>
                    <td class="rl">&nbsp;</td>
                    <td class="cell" align="center"><input type="submit" name="subform" value="<?php echo $vote_lang['stimmeabgeben']; ?>" class="buttons"></td>
                    <td class="rr">&nbsp;</td>
                </tr>
                <tr height="20">
                    <td class="rul" width="13">&nbsp;</td>
                    <td class="ru">&nbsp;</td>
                    <td class="rur" width="13">&nbsp;</td>
                </tr>
            </table>
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        </form>
<?php
    } else {
        echo "<h1>" . $vote_lang['msg_7'] . "</h1>";
    }
}
?>

    </center>
</body>

</html>