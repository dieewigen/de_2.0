<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_server_tick.php" method="post">
<div align="center">
<?php

include "det_userdata.inc.php";

$sw = isset($_GET['sw']) ? (int)$_GET['sw'] : 0;

if ($sw) {
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT doetick, domtick, dodelinactiv, dodeloldtrade, trade_active, winid, winticks FROM de_system");
    $row = mysqli_fetch_assoc($result);
    $doetick = $row["doetick"];
    $domtick = $row["domtick"];
    $trade_active = $row["trade_active"];
    $dodelinactiv = $row["dodelinactiv"];
    $dodeloldtrade = $row["dodeloldtrade"];


    switch ($sw) {
        case 1: //wirthschaft
            if ($doetick == 1) {
                $newwert = 0;
            } else {
                $newwert = 1;
            }
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET doetick=?", [$newwert]);
            break;

        case 2: //milit&auml;r
            if ($domtick == 1) {
                $newwert = 0;
            } else {
                $newwert = 1;
            }
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET domtick=?", [$newwert]);
            break;

        case 3: //inaktiv
            if ($dodelinactiv == 1) {
                $newwert = 0;
            } else {
                $newwert = 1;
            }
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET dodelinactiv=?", [$newwert]);
            break;
        default:
            echo 'Fehler.';
            break;
    }//switch sw ende
}

//trigger auslesen
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT lasttick, lastmtick, doetick, domtick, dodelinactiv, dodeloldtrade, trade_active, winid, winticks FROM de_system");
$row = mysqli_fetch_assoc($result);
$lwt = $row["lasttick"];
$lwt = $lwt[0].$lwt[1].$lwt[2].$lwt[3].' - '.$lwt[4].$lwt[5].' - '.$lwt[6].$lwt[7].' - '.$lwt[8].$lwt[9].':'.$lwt[10].$lwt[11];
$lmt = $row["lastmtick"];
$lmt = $lmt[0].$lmt[1].$lmt[2].$lmt[3].' - '.$lmt[4].$lmt[5].' - '.$lmt[6].$lmt[7].' - '.$lmt[8].$lmt[9].':'.$lmt[10].$lmt[11];
$doetick = $row["doetick"];
$domtick = $row["domtick"];
$dodelinactiv = $row["dodelinactiv"];
$dodeloldtrade = $row["dodeloldtrade"];
$trade_active = $row["trade_active"];
$winid = $row["winid"];
$winticks = $row["winticks"];

echo '<br><table cellpadding="3" cellspacing="4">';
echo '<tr>';
echo '<td width="300" align="center">Wirtschaftstick ('.$lwt.')</td>';
if ($doetick == 1) {
    $str = 'Aktiv';
} else {
    $str = 'Inaktiv';
}
echo '<td width="100" align="center"><a href="'.$_SERVER['PHP_SELF'].'?sw=1">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Milit&auml;rtick ('.$lmt.')</td>';
if ($domtick == 1) {
    $str = 'Aktiv';
} else {
    $str = 'Inaktiv';
}
echo '<td width="100" align="center"><a href="'.$_SERVER['PHP_SELF'].'?sw=2">'.$str.'</a></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="300" align="center">Inaktive L&ouml;schen</td>';
if ($dodelinactiv == 1) {
    $str = 'Aktiv';
} else {
    $str = 'Inaktiv';
}
echo '<td width="100" align="center"><a href="'.$_SERVER['PHP_SELF'].'?sw=3">'.$str.'</a></td>';
echo '</tr></table>';


?>
</form>
</body>
</html>
