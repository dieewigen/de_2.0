<?php
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

// Übersicht anzeigen
if ($action == "" || $action == "uebersicht") {
    ?>

<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $vote_lang['alteumfragen']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>

<?php
    $alteumfragenvorhanden = 0;
    $sql = "SELECT id, frage FROM de_vote_umfragen WHERE status=2 ORDER BY id DESC";
    $db_alteumfragen = mysqli_execute_query($GLOBALS['dbi'], $sql);

    while ($row = mysqli_fetch_assoc($db_alteumfragen)) {
        echo '<tr align="center"><td class="rl" width="13">&nbsp;</td><td><div class="cellu"><a href="vote_overview.php?action=show&id='.$row['id'].'">'.$row['frage'].'</a></div></td><td class="rr" width="13">&nbsp;</td></tr>';
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
    $sql = "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?";
    $db_checkobende = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);

    if (mysqli_num_rows($db_checkobende) > 0) {
        $row = mysqli_fetch_assoc($db_checkobende);
        ?>
<br><br>
<a href="javascript:history.back()"><div class="cellu"><b><?php echo $vote_lang['zzu']; ?></b></div></a>
<br><br>
<table border="0" cellpadding="0" cellspacing="0" width="600">
<tr height="37" align="center">
<td class="rol">&nbsp;</td>
<td class="ro"><div class="cellu"><?php echo $row['frage']; ?></div></td>
<td class="ror">&nbsp;</td>
</tr>
<tr><td class="rl" width="13">&nbsp;</td><td class="cell">

<table border="0" width="100%" cellspacing="2" cellpadding="0">
<tr><td class="cell">&nbsp;<?php echo $vote_lang['start']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['startdatum']); ?></td></tr>
<tr><td class="cell">&nbsp;<?php echo $vote_lang['ende']; ?>: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['enddatum']); ?></td></tr>
<tr><td class="cell">&nbsp;<?php echo $vote_lang['hinweis']; ?>: <?php echo nl2br($row['hinweis']); ?></td></tr>

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
    <td height="25">&nbsp;'.$antworten[$i].'</td>
    <td style="white-space:nowrap">&nbsp;<img src="'.'gp/'.'g/vote/l'.$farbe.'.gif" border="0"><img src="'.'gp/'.'g/vote/m'.$farbe.'.gif" border="0" width="'.$prozente.'" height="9"><img src="'.'gp/'.'g/vote/r'.$farbe.'.gif"></td>
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
<a href="javascript:history.back()"><h4><?php echo $vote_lang['zzu']; ?></h4></a>
<form action="vote.php" method="post">
<table border="0" width="400" cellspacing="0" cellpadding="0">
<tr><td class="rol">&nbsp;</td><td class="ro"><?php echo $row['frage']; ?></td><td class="ror"></td></tr>
<tr><td class="rl">&nbsp;</td><td class="cell">
<fieldset><legend><b><?php echo $vote_lang['hinweis']; ?></b></legend><?php echo nl2br($row['hinweis']); ?></fieldset>
</td><td class="rr"></td></tr>

<?php
        $antworten = explode("|", $row['antworten']);
        foreach ($antworten as $index => $antwort) {
            echo '<tr><td class="rl"></td><td class="cell"><input type="radio" name="vote" value="'.($index + 1).'">&nbsp;'.$antwort.'</td><td class="rr"></td></tr>';
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


</body>
</html>
