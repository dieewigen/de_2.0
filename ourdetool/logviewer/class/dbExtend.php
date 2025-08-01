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
        // Initialisiere das Array, wenn es nicht existiert
        if(!isset(self::$aryRef[$num])) {
            self::$aryRef[$num] = null;
        }
        
        if(self::$aryRef[$num] == null) dbExtend::$aryRef[$num] = new self();
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
        // Stelle sicher, dass alle benötigten Eigenschaften existieren
        if(!isset(cfg::getInstance()->sql[0]->goOffline)) cfg::getInstance()->sql[0]->goOffline = 1;
        if(!isset(cfg::getInstance()->sql[0]->table_prefix)) cfg::getInstance()->sql[0]->table_prefix = '';
        
        // Hard-coded Verbindungsparameter, falls die cfg-Werte nicht gesetzt sind
        $user = "root";
        $pass = "GhzLjR";
        $host = "127.0.0.1";
        $db = "gameserverlogdata";
        $table_prefix = "";
        $goOffline = 1;
        
        // Wenn cfg-Werte gesetzt sind, verwende diese
        if(isset(cfg::getInstance()->sql[0]->user)) $user = cfg::getInstance()->sql[0]->user;
        if(isset(cfg::getInstance()->sql[0]->pass)) $pass = cfg::getInstance()->sql[0]->pass;
        if(isset(cfg::getInstance()->sql[0]->host)) $host = cfg::getInstance()->sql[0]->host;
        if(isset(cfg::getInstance()->sql[0]->db)) $db = cfg::getInstance()->sql[0]->db;
        if(isset(cfg::getInstance()->sql[0]->table_prefix)) $table_prefix = cfg::getInstance()->sql[0]->table_prefix;
        if(isset(cfg::getInstance()->sql[0]->goOffline)) $goOffline = cfg::getInstance()->sql[0]->goOffline;
        
        // Initialisiere die Datenbankverbindung
        parent::database($user, $pass, $host, $db, $table_prefix, $goOffline);
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