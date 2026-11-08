<?php
/**
 * Description of parseLog
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de

 *
 */
class logfile2database  {
    private $id;
    function __construct($id,$file) {
        $this->id = $id;
        $this->parse($file);
        flush();
    }


    function parse($file) {
        $ds = new Datensatz($this->id);
        $fp = fopen($file, "r");
        while (!feof($fp)){
            set_time_limit(60);
            if( $ds->addLine( fgets($fp,8000) ) ) $ds = new Datensatz($this->id);
        }

        fclose($fp);
    }
}
class Datensatz {
    private $id;
    private $Zeit;
    private $IP;
    private $Datei;
    private $Get;
    private $Post;

    private $lastField;
    function  __construct($id) {
        $this->id = $id;
    }
    function addLine($line) {
        if(strstr( $line, '------------')) return $this->save();

        list($field,$value) = explode(':', $line, 2);
        if(in_array($field, array('Zeit','IP','Datei','Get','Post'))) $this->lastField = $field;
        else $value = $line;

        if($this->lastField == 'Zeit')  $this->Zeit  .= $value;
        if($this->lastField == 'IP')    $this->IP    .= $value;
        if($this->lastField == 'Datei') $this->Datei .= $value;
        if($this->lastField == 'Get')   $this->Get   .= $value;
        if($this->lastField == 'Post')  $this->Post  .= $value;
        return false;
    }
    function save() {
        dbExtend::getInstance()->set('INSERT INTO log (`id`,`Zeit`,`IP`,`Datei`,`Get`,`Post`) VALUES ('
            .sqlescape(trim($this->id)) .','
            .sqlescape(trim($this->Zeit)) .','
            .sqlescape(trim($this->IP)).','
            .sqlescape(trim($this->Datei)).','
            .sqlescape(trim($this->Get)).','
            .sqlescape(trim($this->Post))
            .')');
        echo dbExtend::getInstance()->getErrorMsg();
        return true;
    }
}







?>
