<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Multi-Cookie';
include "inc.layout.top.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.*, de_user_ip.*, de_user_data.*, de_login.status AS loginstatus FROM de_login LEFT JOIN de_user_ip ON(de_login.user_id = de_user_ip.user_id)
	LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id)
 WHERE de_user_ip.loginhelp<>'' ORDER BY de_user_ip.loginhelp, de_user_ip.user_id");

$gesuser = 0;

echo '<table>';
echo '<tr><th>UserID</th><th>IP</th><th>Cookie-Zeit</th><th>Registriert</th><th>Letzte Aktivit&auml;t</th><th>Status</th><th>Sektor</th><th>Allianz</th><th>Cookie</th><th>Browser</th></tr>';

unset($olduserid);
unset($oldloginhelp);

while ($row = mysqli_fetch_assoc($db_daten)) {
    if (!isset($olduserid)) {
        $olduserid = $row["user_id"];
    }
    if (!isset($oldloginhelp)) {
        $oldloginhelp = $row["loginhelp"];
    }

    if ($olduserid != $row['user_id'] && $oldloginhelp == $row['loginhelp']) {
        echo '<tr>';
        echo '<td><a href="idinfo.php?UID=' . $olduserid . '" target="_blank" rel="noopener">' . $olduserid . '</a></td>';
        echo '<td>' . htmlspecialchars((string)$oldip) . '</td>';
        echo '<td>' . $oldtime . '</td>';
        echo '<td>' . $registertime . '</td>';
        echo '<td>' . $lastactivetime . '</td>';
        echo '<td>' . $oldstatus . '</td>';
        echo '<td class="num">' . $oldsector . '</td>';
        echo '<td>' . htmlspecialchars((string)$oldallytag) . '</td>';
        echo '<td>' . htmlspecialchars((string)$oldloginhelp) . '</td>';
        echo '<td>' . htmlspecialchars((string)$oldbrowser) . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><a href="idinfo.php?UID=' . $row["user_id"] . '" target="_blank" rel="noopener">' . $row["user_id"] . '</a></td>';
        echo '<td>' . htmlspecialchars((string)$row['ip']) . '</td>';
        echo '<td>' . $row['time'] . '</td>';
        echo '<td>' . $row['register'] . '</td>';
        echo '<td>' . $row['last_click'] . '</td>';
        echo '<td>' . $row['loginstatus'] . '</td>';
        echo '<td class="num">' . $row['sector'] . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['allytag']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['loginhelp']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$row['browser']) . '</td>';
        echo '</tr>';
        $gesuser++;
    }
    $olduserid = $row["user_id"];
    $oldloginhelp = $row["loginhelp"];
    $oldip = $row["ip"];
    $oldtime = $row['time'];
    $oldbrowser = $row["browser"];
    $oldstatus = $row["loginstatus"];
    $oldsector = $row["sector"];
    $oldallytag = $row["allytag"];
    $registertime = $row['register'];
    $lastactivetime = $row['last_click'];
}
echo '</table>';
echo 'Verdächtige: ' . $gesuser;

include "inc.layout.bottom.php";
