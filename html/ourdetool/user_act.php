<?php
include "../inccon.php";
include "../inc/lang/1_statistics.lang.php";
include "det_userdata.inc.php";

$page_title = $stat_lang['title'];
include "inc.layout.top.php";

//Aktivitätsklassen (0 = inaktiv, 1 = nur Chat, 2 = aktiv), siehe adm.css
$actclasses = array('act-none', 'act-chat', 'act-active');

//daten auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_stat WHERE (h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23)>=42
 ORDER BY user_id, datum DESC");

$olduid = 0;

//daten ausgeben
while ($row = mysqli_fetch_assoc($db_daten)) {
    if ($olduid != $row['user_id'] && $olduid != 0) {
        echo '</table>';
    }

    if ($olduid != $row['user_id']) {
        //tabellenkopf
        //userdaten auslesen
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$row['user_id']]);
        $rowx = mysqli_fetch_assoc($result);
        echo '<br>User-ID: <a href="idinfo.php?UID=' . $row['user_id'] . '">' . $row['user_id'] . '</a> Spielername: ' . htmlspecialchars((string)$rowx['spielername']);
        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_login WHERE user_id=?", [$row['user_id']]);
        $de_login = mysqli_fetch_assoc($result);
        $status = match ((int)$de_login["status"]) {
            0 => 'Inaktiv',
            1 => 'Aktiv',
            2 => 'Gesperrt',
            3 => 'Urlaub',
            default => 'Status ' . (int)$de_login["status"],
        };
        echo ' Status: ' . $status . '<br>';

        echo '<table>';
        echo '<tr><th>' . $stat_lang['datum'] . '</th><th>00</th><th>01</th><th>02</th><th>03</th><th>04</th><th>05</th><th>06</th><th>07</th><th>08</th><th>09</th><th>10</th><th>11</th><th>12</th>
        <th>13</th><th>14</th><th>15</th><th>16</th><th>17</th><th>18</th><th>19</th><th>20</th><th>21</th><th>22</th><th>23</th>';
        echo '</tr>';
    }

    echo '<tr align="center">';
    echo '<td>' . $row["datum"] . '</td>';
    for ($i = 0; $i <= 23; $i++) {
        $actclass = $actclasses[(int)$row["h$i"]] ?? '';
        echo '<td class="' . $actclass . '">&nbsp;</td>';
    }
    echo '</tr>';
    $olduid = $row['user_id'];
}
//legende
echo '</table><br>Legende: <span class="act-none">' . $stat_lang['legende1'] . '</span> <span class="act-chat">' . $stat_lang['legende2'] . '</span> <span class="act-active">' . $stat_lang['legende3'] . '</span>';

include "inc.layout.bottom.php";
