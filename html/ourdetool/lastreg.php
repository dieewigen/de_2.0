<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Letzte Registrierungen';
include "inc.layout.top.php";

// stat2 gesetzt: gesperrte Accounts ausblenden (historischer Parameter)
$stat2 = isset($_REQUEST['stat2']);

function modpass($pass)
{
    for ($i = 0; $i < 4 && $i < strlen($pass); $i++) {
        $pass[$i] = '*';
    }
    return $pass;
}

echo '<table>';
echo '<tr><th>UserID</th><th>Name</th><th>E-Mail</th><th>Passwort</th><th>Registriert</th><th>Letzter Login</th><th>letzte IP</th><th>Status</th><th>Logins</th><th>Sektor</th></tr>';

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.user_id, de_login.nic, de_login.reg_mail, de_login.pass, de_login.register, de_login.last_login, de_login.logins, de_user_data.sector, de_login.status, de_user_info.ort, de_login.last_ip FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) LEFT JOIN de_user_info ON(de_login.user_id = de_user_info.user_id) ORDER BY `user_id` DESC LIMIT 50");
while ($user = mysqli_fetch_assoc($result)) {
    if ($stat2 && $user["status"] == 2) {
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
    echo '<td>' . modpass($user["pass"]) . '</td>';
    echo '<td>' . $user["register"] . '</td>';
    echo '<td>' . $user["last_login"] . '</td>';
    echo '<td>' . $user["last_ip"] . '</td>';
    echo '<td>' . $status . '</td>';
    echo '<td class="num">' . $user["logins"] . '</td>';
    echo '<td class="num">' . $user["sector"] . '</td>';
    echo '</tr>';
}
echo '</table>';

include "inc.layout.bottom.php";
