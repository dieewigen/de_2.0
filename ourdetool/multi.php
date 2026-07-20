<?php
include "../inccon.php";
include "det_userdata.inc.php";

$show_statistic = req_int('statistic');
$tage = req_int('tage', 14);

$page_title = 'Multiliste';
$active_nav = ($show_statistic >= 1 && $show_statistic <= 3) ? 'multi' . $show_statistic : 'multi1';
include "inc.layout.top.php";

$okt = 4;
$gesuser = 0;

function modpass($pass)
{
    $pass[0] = "*";
    $pass[1] = "*";
    $pass[2] = "*";
    $pass[3] = "*";
    return $pass;
}

if ($show_statistic == 1 || $show_statistic == 2) {
    //alle verdächtigen IP-Adressen auslesen, letzte IP
    $sql = "SELECT SUBSTRING_INDEX(last_ip, '.', ?) AS last_ip, COUNT(last_ip) 'zaehler' FROM de_login WHERE last_ip<>'127.0.0.1' GROUP BY SUBSTRING_INDEX(last_ip, '.', ?) ORDER BY `zaehler` DESC, `last_ip` ASC";

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$okt, $okt]);

    while ($row = mysqli_fetch_assoc($db_daten)) {
        if (($row["zaehler"] > 1) && ($row["last_ip"] <> '')) {
            $z = $row["zaehler"];
            $ip = $row["last_ip"];
            $ipz = $ip;
            if ($ipz == '212.227.110.246') {
                $ipz = '!!! 1&1 !!!';
            }
            //kopf mit ip und anzahl
            echo '<h3>IP: ' . htmlspecialchars($ipz) . ' Anzahl: ' . $z . '</h3>';

            echo '<table>';
            echo '<tr><th>UserID</th><th>Name</th><th>E-Mail</th><th>Passwort</th><th>Registriert</th><th>Letzter Login</th><th>Status</th><th>Logins</th><th>Sektor</th><th>Ort</th><th>IP</th></tr>';

            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.last_ip, de_login.user_id, de_login.nic, de_login.reg_mail, de_login.pass, de_login.register, de_login.last_login, de_login.logins, de_user_data.sector, de_login.status, de_user_info.ort FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) LEFT JOIN de_user_info ON(de_login.user_id = de_user_info.user_id) WHERE last_ip LIKE CONCAT(?, '%') ORDER BY pass", [$ip]);

            $oldpass = '';
            while ($user = mysqli_fetch_assoc($result)) {
                if ($oldpass == $user["pass"]) {
                    $str = ' class="r"';
                } else {
                    $str = '';
                }
                $oldpass = $user["pass"];

                // statistic=2: gesperrte Accounts ausblenden
                if ($show_statistic == 2 && $user["status"] == 2) {
                    continue;
                }

                $status = match ((int)$user["status"]) {
                    0 => 'Inaktiv',
                    1 => 'Aktiv',
                    2 => 'Gesperrt',
                    3 => 'Urlaub',
                    default => 'Status ' . (int)$user["status"],
                };
                $status .= ' <form class="inline" method="post" action="de_set_user_status.php" data-confirm="User ' . $user["user_id"] . ' wirklich sperren?">'
                    . csrf_field()
                    . '<input type="hidden" name="uid" value="' . $user["user_id"] . '">'
                    . '<input type="hidden" name="status" value="2">'
                    . '<input type="hidden" name="ret" value="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">'
                    . '<button type="submit" class="btn-danger btn-xs" title="User sperren">[S]</button>'
                    . '</form>';

                echo '<tr>';
                echo '<td><a href="idinfo.php?UID=' . $user["user_id"] . '" target="_blank" rel="noopener">' . $user["user_id"] . '</a></td>';
                echo '<td>' . htmlspecialchars((string)$user["nic"]) . '</td>';
                echo '<td>' . htmlspecialchars((string)$user["reg_mail"]) . '</td>';
                echo '<td' . $str . '>' . modpass($user["pass"]) . '</td>';
                echo '<td>' . $user["register"] . '</td>';
                echo '<td>' . $user["last_login"] . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td class="num">' . $user["logins"] . '</td>';
                echo '<td class="num">' . $user["sector"] . '</td>';
                echo '<td>' . htmlspecialchars((string)$user["ort"]) . '</td>';
                echo '<td>' . htmlspecialchars((string)$user["last_ip"]) . '</td>';
                echo '</tr>';
                $gesuser++;
            }
            echo '</table>';
        }
    }

    echo 'Verd&auml;chtige: ' . $gesuser;
} elseif ($show_statistic == 3) {
    //die IP-Adressen der letzten X Tage auswerten
    $time = date("Y-m-d H:i:s", time() - 3600 * 24 * $tage);

    //alle vorhandenen IP-Adressen in ein Array packen
    $sql = "SELECT * FROM de_user_ip WHERE time>? GROUP BY ip";

    $ip_adressen = array();

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$time]);
    while ($row = mysqli_fetch_assoc($db_daten)) {
        $ip_adressen[] = $row['ip'];
    }

    //für jede IP-Adresse überprüfen ob es mehrere user_id gibt, was normal nicht sein sollte

    echo '<h2>IP-Adressen der letzten ' . $tage . ' Tage die in mehreren Accounts auftreten.</h2>';

    $tage_array = array(3, 7, 14, 30, 50, 100);

    echo '<br>';
    for ($i = 0; $i < count($tage_array); $i++) {
        echo '<a href="multi.php?statistic=3&tage=' . $tage_array[$i] . '">' . $tage_array[$i] . ' Tage</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    echo '<br><br>';

    for ($i = 0; $i < count($ip_adressen); $i++) {
        $ip = $ip_adressen[$i];

        $sql = "SELECT COUNT(DISTINCT user_id) AS anzahl FROM de_user_ip WHERE time>? AND ip=?;";
        $result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$time, $ip]);
        $rowx = mysqli_fetch_assoc($result);
        $anzahl = $rowx['anzahl'];

        if ($rowx['anzahl'] > 1) {
            //die beteiligten Spieler ausgeben
            $sql = "SELECT * FROM de_user_ip LEFT JOIN de_login ON(de_user_ip.user_id=de_login.user_id) WHERE time>? AND ip=? GROUP BY de_user_ip.user_id;";
            $result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$time, $ip]);

            echo '<h3>IP: ' . htmlspecialchars($ip) . ' Anzahl: ' . $anzahl . '</h3>';

            echo '<table>';
            echo '<tr><th>UserID</th><th>Name</th><th>E-Mail</th><th>Passwort</th><th>Registriert</th><th>Letzter Login</th><th>Status</th><th>Logins</th></tr>';

            $oldpass = '';
            while ($user = mysqli_fetch_assoc($result)) {
                if ($oldpass == $user["pass"]) {
                    $str = ' class="r"';
                } else {
                    $str = '';
                }
                $oldpass = $user["pass"];

                $status = match ((int)$user["status"]) {
                    0 => 'Inaktiv',
                    1 => 'Aktiv',
                    2 => 'Gesperrt',
                    3 => 'Urlaub',
                    default => 'Status ' . (int)$user["status"],
                };
                $status .= ' <form class="inline" method="post" action="de_set_user_status.php" data-confirm="User ' . $user["user_id"] . ' wirklich sperren?">'
                    . csrf_field()
                    . '<input type="hidden" name="uid" value="' . $user["user_id"] . '">'
                    . '<input type="hidden" name="status" value="2">'
                    . '<input type="hidden" name="ret" value="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">'
                    . '<button type="submit" class="btn-danger btn-xs" title="User sperren">[S]</button>'
                    . '</form>';

                echo '<tr>';
                echo '<td><a href="idinfo.php?UID=' . $user["user_id"] . '" target="_blank" rel="noopener">' . $user["user_id"] . '</a></td>';
                echo '<td>' . htmlspecialchars((string)$user["nic"]) . '</td>';
                echo '<td>' . htmlspecialchars((string)$user["reg_mail"]) . '</td>';
                echo '<td' . $str . '>' . modpass($user["pass"]) . '</td>';
                echo '<td>' . $user["register"] . '</td>';
                echo '<td>' . $user["last_login"] . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td class="num">' . $user["logins"] . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            //eine Liste aller Logins
            echo '
            <details>
                <summary>Liste der Logins</summary>
                <p>
                    <table>
                        <tr>
                            <th>UserID</th>
                            <th>Zeit</th>
                            <th>Browser</th>
                            <th>Cookie</th>
                        </tr>
            ';

            $sql = "SELECT * FROM de_user_ip WHERE time>? AND ip=? ORDER BY time;";
            $result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$time, $ip]);
            while ($rowx = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $rowx['user_id'] . '</td>';
                echo '<td>' . $rowx['time'] . '</td>';
                echo '<td>' . htmlspecialchars((string)$rowx['browser']) . '</td>';
                echo '<td>' . htmlspecialchars((string)$rowx['loginhelp']) . '</td>';
                echo '</tr>';
            }

            echo '
                    </table>
                </p>
            </details>';
        }
    }
}

include "inc.layout.bottom.php";
