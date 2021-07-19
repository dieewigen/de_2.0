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
    private function __construct() { }
    public static function getInstance() {

        if(self::$ref == null) {
            self::$ref = new self();
            require_once 'logviewer/includeFromClass_cfg.php';
        }
        return self::$ref;
    }

}

?>
