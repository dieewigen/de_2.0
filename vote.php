<?php
include "inc/header.inc.php";
include('inc/lang/'.$sv_server_lang.'_vote.lang.php');
include "functions.php";

// Request Parameter absichern
$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
$bar = !empty($_REQUEST['bar']) ? $_REQUEST['bar'] : '';
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';

// User-Daten holen
$db_daten_vote = mysql_query("SELECT submit FROM de_user_info WHERE user_id='$ums_user_id'", $db);
$row_vote = mysql_fetch_array($db_daten_vote);
$db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, tick FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);

$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$gespielteticks = $row['tick'];
$sector = $row['sector'];
$system = $row['system'];

// Newtrans zurücksetzen
if ($newtrans == 1) {
    mysql_query("UPDATE de_user_data SET newtrans = 0 WHERE user_id='$ums_user_id'", $db);
}
$newtrans = 0;

?>
<!doctype html>
<html>
<head>
<title><?php echo $vote_lang['title']; ?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<?php if ($bar == "yes") {
    include('resline.php');
} ?>

<?php
// Wenn Formular abgesendet wurde
if (!empty($subform)) {

    $db_check = mysql_query("SELECT vote_id FROM de_vote_stimmen WHERE user_id='$ums_user_id' AND vote_id='$id'");
    $vote_aktiv = mysql_query("SELECT id, status FROM de_vote_umfragen WHERE id='$id'");
    $aktiv = mysql_fetch_array($vote_aktiv);
    $menge = mysql_num_rows($db_check);

    if ($menge == 0 && $aktiv['status'] == 1) {
        if ($vote != "0" && $vote != "") {
            echo '<h2>'.$vote_lang['msg_3'].'</h2>';
            mysql_query("INSERT INTO de_vote_stimmen (user_id, vote_id, votefor) VALUES ('$ums_user_id','$id', '$vote')", $db);
            $_SESSION['ums_vote'] = 0;
        } else {
            echo $vote_lang['msg_4'];
        }
    } else {
        echo '<h1>'.$vote_lang['msg_5'].'</h1>';
    }
}

// Übersicht anzeigen
if ($action == "" || $action == "uebersicht") {
    ?>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $vote_lang['aktuelleumfragen']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>

<?php
    $schonabgestimmt = mysql_query("SELECT vote_id FROM de_vote_stimmen WHERE user_id='$ums_user_id'");
    $gevotetevotes = [];
    while ($rew = mysql_fetch_array($schonabgestimmt)) {
        $gevotetevotes[] = $rew['vote_id'];
    }

    $votevorhanden = 0;
    $db_umfrage = mysql_query("SELECT de_vote_umfragen.id, de_vote_umfragen.frage, de_vote_umfragen.startdatum FROM de_vote_umfragen, de_login WHERE de_vote_umfragen.status=1 AND UNIX_TIMESTAMP(de_login.register)<UNIX_TIMESTAMP(de_vote_umfragen.startdatum) AND de_login.user_id='$ums_user_id' ORDER BY de_vote_umfragen.id");

    while ($row = mysql_fetch_array($db_umfrage)) {
        if (!in_array($row['id'], $gevotetevotes)) {
            echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
            echo '<td class="cell"><a href="vote.php?action=abstimmen&id='.$row['id'].'">'.utf8_encode_fix($row['frage']).'</a></td><td class="rr" width="13">&nbsp;</td></tr>';
            $votevorhanden = 1;
        }
    }

    if ($votevorhanden == 0) {
        echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cell">'.$vote_lang['msg_2'].'<br><a href="overview.php">weiter</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
    }
    ?>
<tr height="20">
<td class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>

<br><br><br>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $vote_lang['alteumfragen']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>

<?php
    $alteumfragenvorhanden = 0;
    $db_alteumfragen = mysql_query("SELECT id, frage FROM de_vote_umfragen WHERE status=2");

    while ($row = mysql_fetch_array($db_alteumfragen)) {
        echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cellu"><a href="vote.php?action=show&id='.$row['id'].'">'.utf8_encode_fix($row['frage']).'</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
        $alteumfragenvorhanden++;
    }

    if ($alteumfragenvorhanden == 0) {
        echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td>'.$vote_lang['msg_1'].'</td><td class="rr" width="13">&nbsp;</td></tr>';
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
    $db_checkobende = mysql_query("SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id='$id'");

    if (mysql_num_rows($db_checkobende) > 0) {
        $row = mysql_fetch_array($db_checkobende);
        ?>
<br><br>
<a href="javascript:history.back()"><div class="cellu"><b><?php echo $vote_lang['zzu']; ?></b></div></a>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="600">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo utf8_encode_fix($row['frage']); ?></div></td>
<td class="ror">&nbsp;</td>
</tr>
<tr><td class="rl" width="13">&nbsp;</td><td class="cell">

<table border="0" width="100%" cellspacing="2" cellpadding="0">
<tr><td class="cell">&nbsp;<?php echo $vote_lang['start']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['startdatum']); ?></td></tr>
<tr><td class="cell">&nbsp;<?php echo $vote_lang['ende']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['enddatum']); ?></td></tr>
<tr><td class="cell">&nbsp;<?php echo $vote_lang['hinweis']; ?>: <?php echo nl2br(utf8_encode_fix($row['hinweis'])); ?></td></tr>

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
    <td height="25">&nbsp;'.utf8_encode_fix($antworten[$i]).'</td>
    <td>&nbsp;<img src="'.$ums_gpfad.'g/vote/l'.$farbe.'.gif" border="0"><img src="'.$ums_gpfad.'g/vote/m'.$farbe.'.gif" border="0" width="'.$prozente.'" height="9"><img src="'.$ums_gpfad.'g/vote/r'.$farbe.'.gif"></td>
    <td width="40" nowrap>&nbsp;'.$anzahl.'</td>
    <td width="50" nowrap>&nbsp;'.$prozente.'%</td></tr>';

            $stimmengesamt += $anzahl;
            $farbe = ($farbe == 0) ? 1 : 0;
        }
        ?>
<tr class="cell" height="25">
<td colspan="2" align="right"><b><?php echo $vote_lang['insgesamt']; ?>:</b>&nbsp;&nbsp;</td>
<td>&nbsp;<?php echo $stimmengesamt; ?></td><td></td>
</tr>

</table>
</td><td class="rr" width="13">&nbsp;</td></tr>
<tr><td class="rul" width="13">&nbsp;</td><td class="ru">&nbsp;</td><td class="rur" width="13">&nbsp;</td></tr>
</table>

<?php
    } else {
        echo "<h1>".$vote_lang['msg_7']."</h1>";
        @$time = strftime("%Y-%m-%d %H:%M:%S");
        @$para = "Master Guardian, Spieler $ums_spielername ($asec:$asys)[UserID:$ums_user_id] hat am $zeit versucht, alte Umfragen aufzurufen.";
    }
}

// Abstimmen
elseif ($action == "abstimmen") {
    $db_umfrage = mysql_query("SELECT id, frage, hinweis, antworten FROM de_vote_umfragen WHERE id='$id' AND status=1");
    $row = mysql_fetch_array($db_umfrage);
    $vorhanden = mysql_num_rows($db_umfrage);

    if ($vorhanden > 0) {
        ?>
<a href="javascript:history.back()"><h4><?php echo $vote_lang['zzu']; ?></h4></a>
<form action="vote.php" method="post">
<table border="0" width="400" cellspacing="0" cellpadding="0">
<tr><td class="rol">&nbsp;</td><td class="ro"><?php echo utf8_encode_fix($row['frage']); ?></td><td class="ror"></td></tr>
<tr><td class="rl">&nbsp;</td><td class="cell">
<fieldset><legend><b><?php echo $vote_lang['hinweis']; ?></b></legend><?php echo nl2br(utf8_encode_fix($row['hinweis'])); ?></fieldset>
</td><td class="rr"></td></tr>

<?php
        $antworten = explode("|", $row['antworten']);
        foreach ($antworten as $index => $antwort) {
            echo '<tr><td class="rl"></td><td class="cell"><input type="radio" name="vote" value="'.($index + 1).'">&nbsp;'.utf8_encode_fix($antwort).'</td><td class="rr"></td></tr>';
        }
        ?>
<tr><td class="rl">&nbsp;</td><td class="cell" align="center"><input type="submit" name="subform" value="<?php echo $vote_lang['stimmeabgeben']; ?>" class="buttons"></td><td class="rr">&nbsp;</td></tr>
<tr><td class="rul">&nbsp;</td><td class="ru">&nbsp;</td><td class="rur">&nbsp;</td></tr>
</table>
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
</form>
<?php
    } else {
        echo "<h1>".$vote_lang['msg_7']."</h1>";
    }
}
?>

<?php include('fooban.php'); ?>
</body>
</html>
