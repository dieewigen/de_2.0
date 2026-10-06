<?php
use DieEwigen\DE2\View\Vote\PollResult;

include "inc/header.inc.php";
include('inc/lang/'.$sv_server_lang.'_vote.lang.php');
include_once "functions.php";

// Request Parameter absichern
$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
$bar = !empty($_REQUEST['bar']) ? $_REQUEST['bar'] : '';
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

// User-Daten holen
$sql = "SELECT submit FROM de_user_info WHERE user_id=?";
$db_daten_vote = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row_vote = mysqli_fetch_assoc($db_daten_vote);

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, tick FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);

$restyp01 = $row["restyp01"];
$restyp02 = $row["restyp02"];
$restyp03 = $row["restyp03"];
$restyp04 = $row["restyp04"];
$restyp05 = $row["restyp05"];
$punkte = $row["score"];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$gespielteticks = $row['tick'];
$sector = $row['sector'];
$system = $row['system'];

// Newtrans zurücksetzen
if ($newtrans == 1) {
    $sql = "UPDATE de_user_data SET newtrans = 0 WHERE user_id=?";
    mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
}
$newtrans = 0;

?>
<!doctype html>
<html>
<head>
<title><?php echo $vote_lang['title']; ?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php 
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');

// Übersicht: beendete Umfragen, neueste zuerst
if ($action == "" || $action == "uebersicht") {
    rahmen_oben(ucfirst($vote_lang['alteumfragen']));
    echo '<div class="mod vt">';

    $alteumfragenvorhanden = 0;
    $sql = "SELECT id, frage, stimmen, enddatum FROM de_vote_umfragen WHERE status=2 ORDER BY id DESC";
    $db_alteumfragen = mysqli_execute_query($GLOBALS['dbi'], $sql);

    while ($row = mysqli_fetch_assoc($db_alteumfragen)) {
        $stimmen = explode("|", $row['stimmen']);
        $ende = strtotime($row['enddatum']);
        echo '<a href="vote_overview.php?action=show&amp;id='.$row['id'].'" class="vt-zeile vt-link">';
        echo '<span class="vt-frage">'.$row['frage'].'</span>';
        echo '<span class="vt-meta">'.(int)$stimmen[0].((int)$stimmen[0] == 1 ? ' Stimme' : ' Stimmen');
        echo ($ende > 0) ? ' &middot; '.date('d.m.Y', $ende) : '';
        echo '</span>';
        echo '</a>';
        $alteumfragenvorhanden++;
    }

    if ($alteumfragenvorhanden == 0) {
        echo '<div class="mod-leer">'.$vote_lang['msg_1'].'</div>';
    }

    echo '</div>';
    rahmen_unten();
}

// Ergebnis einer beendeten Umfrage
elseif ($action == "show") {
    $sql = "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?";
    $db_checkobende = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);

    if (mysqli_num_rows($db_checkobende) > 0) {
        $row = mysqli_fetch_assoc($db_checkobende);
        echo PollResult::render($row, $vote_lang, 'vote_overview.php', ucfirst($vote_lang['zzu']));
    } else {
        rahmen_oben(ucfirst($vote_lang['title']));
        echo '<div class="mod vt"><div class="mod-leer">Diese Umfrage gibt es nicht oder sie l&auml;uft noch.</div>';
        echo '<div class="vt-fuss"><a href="vote_overview.php" class="mod-btn mod-btn-leise ally-btn-klein">'.ucfirst($vote_lang['zzu']).'</a></div></div>';
        rahmen_unten();
    }
}
?>

</body>
</html>
