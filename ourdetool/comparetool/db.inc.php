<?
class cfg {
    public $sql;
    private static $ref;
    public static function getInstance(){
        if(self::$ref == null) self::$ref = new self();
        return self::$ref;
    }
    private function  __construct() {

        $this->sql[0]->host = 'localhost';
        $this->sql[0]->user = 'user';
        $this->sql[0]->pass = 'password';
        $this->sql[0]->db = 'gameserverlogdata';
       
    }
}
?>