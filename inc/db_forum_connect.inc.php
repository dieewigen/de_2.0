<?php
//direkter connect zum Loginsystem, per MYSQLI 
$dbi_forum = mysqli_connect($GLOBALS['env_db_forum_host'], $GLOBALS['env_db_forum_user'], $GLOBALS['env_db_forum_password'], $GLOBALS['env_db_forum_database']);
?>