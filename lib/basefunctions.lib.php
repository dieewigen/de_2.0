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
		$sql = "INSERT INTO $newstable (user_id, typ, time, text) VALUES (?, ?, ?, ?)";
		mysqli_execute_query($GLOBALS['dbi'], $sql, [$id, $type, $time, $text]);
		
		$sql = "UPDATE $userdatatable SET newnews = 1 WHERE user_id = ?";
		mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
	}
}
?>