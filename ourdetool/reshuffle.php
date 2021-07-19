<?php
include "../inccon.php";
?>

<html>
<head>
<title>reshuffle</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?
include "det_userdata.inc.php";

if($reshufflebtn)
{
  mysql_query("UPDATE de_user_data set sector=0, system=0 WHERE sector>0");
  mysql_query("UPDATE de_login SET status = 0 WHERE status = 1");
  mysql_query("UPDATE de_user_fleet SET aktion=0, entdeckt=0");

  echo '<h1>reshuffle veranlaßt<br> ==> <a href="de_server.php">zurück</a> <== </h1>';
}
else
{
?>
<form action="reshuffle.php" method="post">
<br><b>Reshuffle starten</b><br><br>
<input type="Submit" name="reshufflebtn" value="reshuffle">
</form>
<?
}
?>
</div>
</body>
</html>
