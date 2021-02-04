<?php
// basefunctions.inc.php

$newstable = "de_user_news";
$userdatatable = "de_user_data";

function notifyUser($id, $text, $type)
{
	global $newstable;
	global $userdatatable;
	 $time=strftime("%Y%m%d%H%M%S");
	if ($id > 0)
	{
		$result = mysql_query("INSERT INTO $newstable (user_id, typ, time, text) VALUES ('$id', '$type', '$time', '$text')");
		$result2 = mysql_query("UPDATE $userdatatable SET newnews = 1 WHERE user_id = '$id'");
	}
}
?>