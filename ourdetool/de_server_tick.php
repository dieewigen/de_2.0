<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Tickeinstellungen';
$active_nav = 'server';
include "inc.layout.top.php";

$sw = req_int('sw');

if ($sw) {
    csrf_require();

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

/** Status-Badge, als Link zum Umschalten der jeweiligen Funktion. */
function tick_status_link(int $sw, $aktiv): string
{
    $badge = ($aktiv == 1)
        ? '<span class="badge badge-ok">Aktiv</span>'
        : '<span class="badge badge-danger">Inaktiv</span>';
    return '<a href="' . csrf_url('de_server_tick.php?sw=' . $sw) . '" title="Umschalten">' . $badge . '</a>';
}

echo '<table>';
echo '<tr><th>Funktion</th><th>Status</th></tr>';
echo '<tr>';
echo '<td>Wirtschaftstick ('.$lwt.')</td>';
echo '<td>'.tick_status_link(1, $doetick).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td>Milit&auml;rtick ('.$lmt.')</td>';
echo '<td>'.tick_status_link(2, $domtick).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td>Inaktive L&ouml;schen</td>';
echo '<td>'.tick_status_link(3, $dodelinactiv).'</td>';
echo '</tr></table>';

include "inc.layout.bottom.php";
