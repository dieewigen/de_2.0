<?php
/**
 * Description of dbExtend
 *
 * @author Rainer Zerbe <rz.php-projects@i-it-s.de>
 * 
 *
 */
class dbExtend extends database {
    private static $aryRef = array();
    public static function getInstance($num=0) {
        if(!isset(self::$aryRef[$num]) || self::$aryRef[$num] == null) {
            dbExtend::$aryRef[$num] = new self();
        }
        return dbExtend::$aryRef[$num];
    }
    /**
     * Constructor
     *
     * @param Host $host
     * @param User $user
     * @param Password $pass
     * @param Databse $db
     * @param Table prefix $table_prefix
     * @param GoOffline if no Database Aviable $goOffline
     * @return dbExtend
     */
    private function __construct(  ) {
        $cfg = cfg::getInstance();
        
        // Standardwerte setzen, falls die Eigenschaften nicht definiert sind
        if (!isset($cfg->sql[0]->goOffline)) $cfg->sql[0]->goOffline = 1;
        if (!isset($cfg->sql[0]->table_prefix)) $cfg->sql[0]->table_prefix = '';
        
        parent::database($cfg->sql[0]->host
            , $cfg->sql[0]->user
            , $cfg->sql[0]->pass
            , $cfg->sql[0]->db
            , $cfg->sql[0]->table_prefix
            , $cfg->sql[0]->goOffline);
        if ($this->getErrorNum())  die($this->getErrorMsg());

    }
    public function getErrorObj() {
        if ($this->getErrorNum()) {
            $ret->num = $this->getErrorNum();
            $ret->text = $this->getErrorMsg();
            $ret->msg = $this->getErrorMsg();
            return $ret;
        }
        return null;
    }

    /**
     * SQL Getter
     *
     * @param SQL Statement (SELECT) $sqlQuery
     * @param Feld nach dem sortiert wird $SortKey
     * @return Array mit oder einzelnes Object mit den gelesenen Daten
     */
    function get($sqlQuery, $SortKey = '') {
        $back0 = false; // Variable initialisieren
        if ($SortKey === 0) {
            $back0 = true;
            $SortKey = '';
        }
        $this->setQuery($sqlQuery);
        $d = $this->loadObjectList($SortKey);
        //         $d=$database->loadResultArray();

        if ($back0)        return $d[0];
        else        return $d;
    }
    /**
     * SQL Setter
     *
     * @param SQL Statement $sqlQuery
     * @return last_insert_id
     */
    function set($sqlQuery) {
        $this->setQuery($sqlQuery);
        $this->Query();
        return $this->insertid();

    }
}


?>