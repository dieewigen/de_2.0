<?PHP
include "../inccon.php";
?>
<html>
<head>
<title>Lockfix</title>
<?php include "cssinclude.php";?>
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">
<div align="center">
<?
include "det_userdata.inc.php";
mysql_query("delete from de_user_locks");

echo '<br></b>Der Fix ist durchgelaufen und alle Transaktionssperren wurden entfernt.';
?>
</body>
</html>
