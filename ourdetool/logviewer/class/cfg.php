<?php
/**
 * Description of cfg
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de
 * @copyright © Rainer Zerbe - 22.03.2009
 *
 */


class cfg  {
    public $sql;

    static private $ref;
    private function __construct() { 
        // Initialisiere das sql-Array vor dem Laden der Konfigurationsdatei
        $this->sql = array();
        $this->sql[0] = new stdClass();
    }
    public static function getInstance() {
        global $GLOBALS;
        
        if(self::$ref == null) {
            self::$ref = new self();
            // Stelle sicher, dass die Umgebungsvariablen verfügbar sind
            if (!isset($GLOBALS['env_db_logging_host'])) {
                // Falls env.inc.php noch nicht eingebunden wurde, laden wir es
                if (file_exists('../inc/env.inc.php')) {
                    require_once '../inc/env.inc.php';
                } elseif (file_exists('../../inc/env.inc.php')) {
                    require_once '../../inc/env.inc.php';
                }
            }
            
            // Bestimme den korrekten Pfad zur Konfigurationsdatei
            $cfgPath = dirname(__FILE__) . '/../includeFromClass_cfg.php';
            if (file_exists($cfgPath)) {
                require_once $cfgPath;
            } else {
                die('Konfigurationsdatei nicht gefunden: ' . $cfgPath);
            }
        }
        return self::$ref;
    }

}

?>
