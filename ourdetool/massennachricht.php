<?php
include "../inccon.php";
include "det_userdata.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Massennachricht</title>
<?php include "cssinclude.php";?>
</head>
<body>
<center>
<?php
if (!$send)
{
		?>
		<h1>Nachrichten an alle User</h1><br><br>
		Bitte als HTML formatieren:<br>
		<form action="massennachricht.php" method=post>
		<textarea name="body" rows=30 cols=80></textarea><br><br>
		<input type="submit" name="send" value="send">
		<?
}
else
{
		include('../outputlib.php');
		$time=strftime("%Y%m%d%H%M%S");
		$result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=0");
		$count = mysql_num_rows($result);
        $time=strftime("%Y%m%d%H%M%S");
        while($row = mysql_fetch_array($result))
        {

          $uid=$row["user_id"];
          mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$body')",$db);
          mysql_query("update de_user_data set newnews = 1 where user_id = $uid",$db);

        }
		print("$count Spieler wurden benachrichtigt!");
}

?>
</body>
</html>


		
