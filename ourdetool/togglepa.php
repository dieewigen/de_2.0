<?php
include "../inccon.php";
?>

<html>
<head>
<title>Toggle PA</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>Premiumaccountverwaltung</h1>
(Achtung: Premiumaccounts werden nicht vom Inaktivenscript gelöscht)
<?
include "det_userdata.inc.php";

$uid=(int)$uid;

if($b1 AND $uid)
{
  //info über den account
  $db_daten=mysql_query("SELECT premium FROM de_user_data WHERE user_id='$uid'");
  $num = mysql_num_rows($db_daten);
  if ($num==1)
  {
    $row = mysql_fetch_array($db_daten);
    if ($row["premium"]==1)
    echo '<br>Der Account ist ein Premiumaccount.<br>';
    else
    echo '<br>Der Account ist kein Premiumaccount.<br>';
  }
  else echo '<br>Keinen Account gefunden.<br>';
}

if($b2 AND $uid)
{
  //info über den account
  $db_daten=mysql_query("SELECT premium FROM de_user_data WHERE user_id='$uid'");
  $num = mysql_num_rows($db_daten);
  if ($num==1)
  {
    mysql_query("update de_user_data set premium=1 where user_id = $uid");
    echo '<br>Premiumaccount aktiviert.<br>';
  }
  else echo '<br>Keinen Account gefunden.<br>';
}

if($b3 AND $uid)
{
  //info über den account
  $db_daten=mysql_query("SELECT premium FROM de_user_data WHERE user_id='$uid'");
  $num = mysql_num_rows($db_daten);
  if ($num==1)
  {
    mysql_query("update de_user_data set premium=0 where user_id = $uid");
    echo '<br>Premiumaccount deaktiviert.<br>';
  }
  else echo '<br>Keinen Account gefunden.<br>';
}

if($b4)
{
  //alle accounts pa status setzen
  mysql_query("update de_user_data set premium=1, patime=1300000000");
  echo '<br>Premiumaccount für alle Spieler aktiviert.<br>';
}

if($b5)
{
  //alle accounts pa status nehmen
  mysql_query("update de_user_data set premium=0, patime=0");
  echo '<br>Premiumaccount für alle Spieler deaktiviert.<br>';
}


?>
<form action="togglepa.php" method="post">

User ID: <input type="Text" name="uid" size="5" value="<?=$uid;?>"><br>
<input type="Submit" name="b1" value="Info">
<input type="Submit" name="b2" value="aktiviere PA">
<input type="Submit" name="b3" value="deaktiviere PA">
<br><br><b>Globales setzen:</b><br><br>
<input type="Submit" name="b4" value="PA allen Spielern geben">
<input type="Submit" name="b5" value="PA allen Spielern nehmen">
</form>
<?php
//liste der premiuemaccounts ausgeben
echo '<br><b>Liste der vorhandenen Premiumaccounts<b><br><br>';
echo '<table cellpadding="3" cellspacing="4">';
echo '<tr><td>UserID</td><td>Spielername</td><td>Koordinaten</td></tr>';
$pas = mysql_query("SELECT user_id, spielername, sector, system FROM de_user_data WHERE premium=1",$db);
while($row = mysql_fetch_array($pas))
{
    echo '<tr><td>'.$row["user_id"].'</td><td>'.$row["spielername"].'</td><td>'.$row["sector"].':'.$row["system"].'</td></tr>';;
}
?>
</div>
</body>
</html>

