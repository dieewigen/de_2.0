<?php
include "../inccon.php";
include "det_userdata.inc.php";

$uid = req_int('uid');
$cg = req_int('cg'); // historischer Parameter, ungenutzt

$page_title = 'IPs';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

//alle ips laden und ausgeben
echo '<table>
<tr>
<th>IP-Adresse</th>
<th>Loginzeit</th>
<th>Cookie</th>
<th>Browser</th>
</tr>';

$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT ip, time, browser, loginhelp FROM de_user_ip WHERE user_id = ? ORDER BY time DESC",
    [$uid]
);

// Überprüfen des Ergebnisses
if ($result) {
    while ($row_user_ip = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
      <td>' . htmlspecialchars((string)$row_user_ip['ip']) . '</td>
      <td>' . htmlspecialchars((string)$row_user_ip['time']) . '</td>
      <td>' . htmlspecialchars((string)$row_user_ip['loginhelp']) . '</td>
      <td>' . htmlspecialchars((string)$row_user_ip['browser']) . '</td>
    </tr>';
    }
} else {
    echo '<tr><td colspan="4">Keine IP-Adressen gefunden oder Fehler bei der Abfrage.</td></tr>';
}

echo '</table>';

include "inc.layout.bottom.php";
