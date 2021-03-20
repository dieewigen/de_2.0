<?php
$soudb = @mysql_connect($GLOBALS['env_db_sou_host'], $GLOBALS['env_db_sou_user'], $GLOBALS['env_db_sou_password'], true) or die("Keine Verbindung zur Datenbank möglich.");
mysql_select_db($GLOBALS['env_db_sou_database'], $soudb);
?>