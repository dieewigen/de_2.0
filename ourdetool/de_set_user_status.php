<?php
include "../inccon.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>User sperren</title>
<?php include "cssinclude.php";?>
</head>
<body bgcolor="#FFFFFF" text="#000000">
<div align="center">
<?php

include "det_userdata.inc.php";

if (isset($uid))
{
  mysql_query("UPDATE de_login set status=2, supporter='$det_email' where user_id='$uid'",$db);

  $time=strftime("%Y-%m-%d %H:%M:%S");

  $comment = mysql_query("select kommentar from de_user_info WHERE user_id='$uid'");
  $row = mysql_fetch_array($comment);

  $eintrag = "$row[kommentar]\nDirektsperrung von $det_username über die Multiliste! \n$time";

  mysql_query("UPDATE de_user_info SET kommentar='$eintrag' WHERE user_id='$uid'");

  echo 'User gesperrt.';
}
else die ('Fehler beim Scriptaufruf.');
?>
</body>
</html>
