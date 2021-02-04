<?php
include "inc/header.inc.php";

mysql_query("UPDATE de_login set blocktime=blocktime-45 WHERE user_id='$ums_user_id'");
header("Location: $ref");
?>
