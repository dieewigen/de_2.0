<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Suche</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
if($delete)
{
  $uid=(int)$uid;
  mysql_query("UPDATE de_login SET status=2, last_login='0000-00-00 00:00:00' WHERE user_id='$uid'",$db);
  mysql_query("UPDATE de_user_data SET premium=0 WHERE user_id='$uid'",$db);
  die('<center><br>Der Spieler wurde dem Inaktivenscript zur Löschung übergeben.</body></html>');
}
?>
<form action="de_user_delete.php" method="get">
<center>Durch das Bestätigen des Buttons wird der Spieler gelöscht. Voraussetzung für die Löschung ist ein aktives Inaktivenscript.<br><br>
<input type="Submit" name="delete" value="Spieler löschen">
<input type="hidden" name="uid" value="<?=$uid?>">
</form>
</body>
</html>
