<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>T</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
require '../inc/sv.inc.php';
include "det_userdata.inc.php";
include '../functions.php';

//als erstes versuchen den server zu erreichen um zu sehen ob er überhaupt antwortet
$dblog = mysqli_connect('localhost', 'dbuser', 'c0j9XIrL5Rwm')  or die ('Verbindung zum Datenbankserver fehlgeschlagen.'); 
mysqli_select_db($dblog, 'gameserverlogdata');

$abwann=date("Y-m-d H:i:s", time()-3600*24*10);

$sql="SELECT userid, COUNT(DISTINCT ip) AS ip_anz  FROM gameserverlogdata WHERE serverid='$sv_servid' AND time>'$abwann' GROUP BY userid ORDER BY ip_anz DESC";
//echo $sql;
$result = mysqli_query($dblog, $sql);
//print_r($result);
echo '<b>Anzahl von IP-Adressen innerhalb der letzten 10 Tage</b>';
echo '<table>';
echo '<tr><td>User-ID</td><td>Anzahl</td></tr>';
while($row = mysqli_fetch_array($result)){
	echo '<tr><td><a href="idinfo.php?UID='.$row['userid'].'" target="_blank">'.$row['userid'].'</a></td><td>'.$row['ip_anz'].'</td></tr>';
}
echo '</table>';

?>
</body>
</html>

