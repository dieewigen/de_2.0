<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.leader.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]
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
<title><?=$allyleader_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<?php
include('resline.php');
include('ally/ally.menu.inc.php');
$allys = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid=?",
    [$ums_user_id]
);

if (mysqli_num_rows($allys) < 1) {
    echo $allyleader_lang['msg_1'];
} else {
    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_allys WHERE leaderid=?",
        [$ums_user_id]
    );
    $row = mysqli_fetch_assoc($result);

    $clanid = $row['id'];
    $clantag = $row['allytag'];



    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_user_data WHERE user_id=?",
        [$userid]
    );
    $row = mysqli_fetch_assoc($result);
    $clan = $row['allytag'];



    if ($clantag == $clan) {

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET status=1 WHERE user_id=?",
            [$ums_user_id]
        );

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_allys SET leaderid=? WHERE id=?",
            [$userid, $clanid]
        );

        echo $allyleader_lang['msg_2'];

    } else {

        echo $allyleader_lang['msg_3'];

    }



}

?>
<?php include('ally/ally.footer.inc.php'); ?>