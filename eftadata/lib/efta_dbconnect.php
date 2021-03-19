<?php
$eftadb = @mysql_connect($GLOBALS['env_db_efta_host'], $GLOBALS['env_db_efta_user'], $GLOBALS['env_db_efta_password'], true) or die("Keine Verbindung zur Datenbank möglich.");
mysql_select_db($GLOBALS['env_db_efta_database'], $eftadb);
?>