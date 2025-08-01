<?php
/**
 * Description of includeFromClass_cfg
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de
 * @copyright © Rainer Zerbe - 22.03.2009
 *
 */
// Direkter Zugriff auf die Datenbankeinstellungen ohne auf $GLOBALS zu vertrauen
// Hiermit werden die Werte hart kodiert, was in diesem Fall sicherer ist
cfg::getInstance()->sql[0]->host    = "127.0.0.1";
cfg::getInstance()->sql[0]->user    = "root";
cfg::getInstance()->sql[0]->pass    = "GhzLjR";
cfg::getInstance()->sql[0]->db      = "gameserverlogdata";
cfg::getInstance()->sql[0]->table_prefix = "";
cfg::getInstance()->sql[0]->goOffline = 1;

?>
