<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.leader.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row['score'];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$sector = $row['sector'];
$system = $row['system'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyleader_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

$userid = (int)($_GET['userid'] ?? -1);

//Ergebnis als Meldung unter den Reitern, darunter zurück zur Mitgliederliste
echo '<div class="mod ally-meldung">';

$allys = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid=?",
    [$_SESSION['ums_user_id']]
);

if (mysqli_num_rows($allys) < 1) {
    echo '<div class="mod-meldung mod-meldung-fehler">'.$allyleader_lang['msg_1'].'</div>';
} elseif (!\DieEwigen\DE2\Session\CsrfToken::check($_GET['token'] ?? '')) {
    //Aufruf kam nicht über den Knopf in der Mitgliederliste (z. B. untergeschobener Link)
    echo '<div class="mod-meldung mod-meldung-fehler">'.$allyleader_lang['msg_4'].'</div>';
} else {
    $row = mysqli_fetch_assoc($allys);

    $clanid = $row['id'];

    //neuer Leader muss aufgenommenes Mitglied dieser Allianz sein (status=1), Bewerber haben status=0
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id FROM de_user_data WHERE user_id=? AND ally_id=? AND status=1",
        [$userid, $clanid]
    );

    if (mysqli_num_rows($result) == 1 && $userid != $_SESSION['ums_user_id']) {

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET status=1 WHERE user_id=?",
            [$_SESSION['ums_user_id']]
        );

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_allys SET leaderid=? WHERE id=? AND leaderid=?",
            [$userid, $clanid, $_SESSION['ums_user_id']]
        );

        echo '<div class="mod-meldung mod-meldung-ok">'.$allyleader_lang['msg_2'].'</div>';

    } else {

        echo '<div class="mod-meldung mod-meldung-fehler">'.$allyleader_lang['msg_3'].'</div>';

    }



}
echo '<div class="ally-aktionen"><a href="ally_members.php" class="mod-btn mod-btn-leise">Zur Mitgliederliste</a></div></div>';

?>
<?php include('ally/ally.footer.inc.php'); ?>