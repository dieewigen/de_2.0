<?php
include "../inccon.php";
include "../inc/sv.inc.php";
?>

<html>
<head>
<title>IPs</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>IPs</h1>
<?
include "det_userdata.inc.php";

$uid=(int)$uid;
$cg=(int)$cg;

//alle ips laden und ausgeben
echo '
<table border="0">
<colgroup>
<col width="150">
<col width="150">
</colgroup>
<tr>
<td class="cell1" align="center"><b>IP-Adresse</b></td>
<td class="cell1" align="center"><b>Loginzeit</b></td>
<td class="cell1" align="center"><b>Cookie</b></td>
<td class="cell1" align="center"><b>Browser</b></td>
</tr>';

$sel_user_ip = mysql_query("SELECT ip, time, browser, loginhelp FROM de_user_ip WHERE user_id='$uid' order by time desc");
while($row_user_ip = mysql_fetch_array($sel_user_ip))
echo '<tr align="center"><td>'.$row_user_ip[ip].'</td><td>'.$row_user_ip[time].'</td><td>'.$row_user_ip[loginhelp].'</td><td>'.$row_user_ip[browser].'</td></tr> ';

echo '</table>';
?>
</body>
</html>

