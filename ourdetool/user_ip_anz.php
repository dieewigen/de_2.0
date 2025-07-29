<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>T</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
require '../inc/sv.inc.php';
require '../inc/env.inc.php';
include "det_userdata.inc.php";
include '../functions.php';

//als erstes versuchen den server zu erreichen um zu sehen ob er �berhaupt antwortet
$dblog = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");



$abwann=date("Y-m-d H:i:s", time()-3600*24*10);

$sql="SELECT userid, COUNT(DISTINCT ip) AS ip_anz FROM gameserverlogdata WHERE serverid=? AND time>? GROUP BY userid ORDER BY ip_anz DESC";
//echo $sql;
$result = mysqli_execute_query($dblog, $sql, [$sv_servid, $abwann]);
//print_r($result);
echo '<b>Anzahl von IP-Adressen innerhalb der letzten 10 Tage</b>';
echo '<table>';
echo '<tr><td>User-ID</td><td>Anzahl</td></tr>';
while($row = mysqli_fetch_assoc($result)){
	echo '<tr><td><a href="idinfo.php?UID='.$row['userid'].'" target="_blank">'.$row['userid'].'</a></td><td>'.$row['ip_anz'].'</td></tr>';
}
echo '</table>';

?>
</body>
</html>

