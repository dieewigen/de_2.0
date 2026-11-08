<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

// Extra-Verbindung zur Logging-Datenbank (als erstes versuchen den Server zu
// erreichen um zu sehen ob er überhaupt antwortet)
$GLOBALS['dbi_log'] = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");
$GLOBALS['dbi_log']->set_charset("utf8mb4");

$page_title = 'IP-Anzahl';
include "inc.layout.top.php";

$abwann = date("Y-m-d H:i:s", time() - 3600 * 24 * 10);

$sql = "SELECT userid, COUNT(DISTINCT ip) AS ip_anz FROM gameserverlogdata WHERE serverid=? AND time>? GROUP BY userid ORDER BY ip_anz DESC";
$result = mysqli_execute_query($GLOBALS['dbi_log'], $sql, [$sv_servid, $abwann]);

echo '<b>Anzahl von IP-Adressen innerhalb der letzten 10 Tage</b>';
echo '<table>';
echo '<tr><th>User-ID</th><th>Anzahl</th></tr>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr><td><a href="idinfo.php?UID=' . $row['userid'] . '" target="_blank" rel="noopener">' . $row['userid'] . '</a></td><td class="num">' . $row['ip_anz'] . '</td></tr>';
}
echo '</table>';

include "inc.layout.bottom.php";
