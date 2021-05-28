<?php
//direkter connect zum Loginsystem, per MYSQLI 
$dbi_ls = mysqli_connect($GLOBALS['env_db_loginsystem_host'], $GLOBALS['env_db_loginsystem_user'], $GLOBALS['env_db_loginsystem_password'], $GLOBALS['env_db_loginsystem_database']);
$GLOBALS['dbi_ls']=$dbi_ls;
?>