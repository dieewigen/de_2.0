<?php
/**
 * Description of getAjax
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de
 * @copyright © Rainer Zerbe - 22.03.2009
 * @updated 2025 for mysqli compatibility
 */
require 'det_userdata.inc.php';
require '../inc/sv.inc.php';
require '../inc/env.inc.php';

// Stelle sicher, dass eine Datenbankverbindung hergestellt ist
if (!isset($GLOBALS['dbi'])) {
    // Verbindung zur Hauptdatenbank
    $GLOBALS['dbi'] = mysqli_connect(
        $GLOBALS['env_db_dieewigen_host'], 
        $GLOBALS['env_db_dieewigen_user'], 
        $GLOBALS['env_db_dieewigen_password'], 
        $GLOBALS['env_db_dieewigen_database']
    );
}

// Erstelle direkte Verbindung zur Logging-Datenbank
if (!isset($GLOBALS['dbi_log'])) {
    $GLOBALS['dbi_log'] = mysqli_connect(
        $GLOBALS['env_db_logging_host'], 
        $GLOBALS['env_db_logging_user'], 
        $GLOBALS['env_db_logging_password'], 
        $GLOBALS['env_db_logging_database']
    ) or die("Keine Verbindung zur Logging-Datenbank möglich: " . mysqli_connect_error());
}

// Hilfsfunktionen als Ersatz für dbExtend
function sqlescape($value) {
    global $GLOBALS;
    // Prüfe auf null oder nicht gesetzten Wert und ersetze ihn durch einen leeren String
    if ($value === null || !isset($value)) {
        $value = '';
    }
    return mysqli_real_escape_string($GLOBALS['dbi_log'], $value);
}

// Einfache Ersatzfunktionen für dbExtend
class dbHelperFunctions {
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
    
    public function get($sql, $index = null) {
        try {
            // SQL-Fehler abfangen und vor der Abfrage ausgeben
            if (!$sql) {
                echo '<div style="color: red;">SQL-Fehler: Leere Abfrage</div>';
                return array();
            }
            
            // Debug-Ausgabe der SQL-Abfrage für die Fehlersuche
            // echo '<div style="font-size:10px;color:#999;">DEBUG SQL: ' . htmlspecialchars($sql) . '</div>';
            
            $result = mysqli_query($GLOBALS['dbi_log'], $sql);
            if (!$result) {
                echo '<div style="color: red;">SQL-Fehler: ' . mysqli_error($GLOBALS['dbi_log']) . '</div>';
                return array();
            }
            
            $data = array();
            while ($row = mysqli_fetch_object($result)) {
                if ($index !== null && isset($row->$index)) {
                    $data[$row->$index] = $row;
                } else {
                    $data[] = $row;
                }
            }
            
            return $data;
        } catch (Exception $e) {
            echo '<div style="color: red;">Exception: ' . htmlspecialchars($e->getMessage()) . '</div>';
            return array();
        }
    }
}

// Kompatibilitätslayer für bestehenden Code
class dbExtend {
    public static function getInstance() {
        return dbHelperFunctions::getInstance();
    }
}

// Hilfsfunktion zum Formatieren von Zeit
function formatDateTime($dateTime) {
    if (empty($dateTime)) {
        return date('Y-m-d H:i:s');
    }
    
    // Prüfe, ob ein Datum mit Uhrzeit vorliegt
    if (strpos($dateTime, ':') === false) {
        // Nur ein Datum, füge Uhrzeit hinzu
        return $dateTime . ' 00:00:00';
    }
    
    return $dateTime;
}

if($_GET['job'] == 'loadTopGenerator') {
    $d = dbExtend::getInstance()->get('select serverid, userid, count(userid) as c from gameserverlogdata where file in("imagegenerator") and serverid = '.sqlescape($_REQUEST['sid']).' group by serverid, userid order by c desc limit 20;');
    echo '<table><thead><th> Server ID</th><th> User ID</th><th> Clicks </th></thead><tbody>';
    foreach($d as $row) {
        echo '<tr><td>'.$row->serverid.'</td><td>'.$row->userid.'</td><td>'.$row->c.'</td></tr>';
    }
    echo '</tbody></table>';
}
if($_GET['job'] == 'loadTopSecstat') {
    $d = dbExtend::getInstance()->get('select serverid, userid, count(userid) as c from gameserverlogdata where file in("secstatus") and serverid = '.sqlescape($_REQUEST['sid']).' group by serverid, userid order by c desc limit 20;');
    echo '<table><thead><th> Server ID</th><th> User ID</th><th> Clicks </th></thead><tbody>';
    foreach($d as $row) {
        echo '<tr><td>'.$row->serverid.'</td><td>'.$row->userid.'</td><td>'.$row->c.'</td></tr>';
    }
    echo '</tbody></table>';
}
if($_GET['job'] == 'loadTopSysnews') {
    $d = dbExtend::getInstance()->get('select serverid, userid, count(userid) as c from gameserverlogdata where file in("sysnews") and serverid = '.sqlescape($_REQUEST['sid']).' group by serverid, userid order by c desc limit 20;');
    echo '<table><thead><th> Server ID</th><th> User ID</th><th> Clicks </th></thead><tbody>';
    foreach($d as $row) {
        echo '<tr><td>'.$row->serverid.'</td><td>'.$row->userid.'</td><td>'.$row->c.'</td></tr>';
    }
    echo '</tbody></table>';
}

if(isset($_GET['job']) && $_GET['job'] == 'loadClicks') {
    // Debug Ausgabe der Anfrage
    //echo "UID: " . $_REQUEST['uid'] . ", SID: " . $_REQUEST['sid'] . "<br>";
    
    $d = dbExtend::getInstance()->get('select file, count(file) as clicks  '
        .' from gameserverlogdata '
        .' where userid = '.sqlescape($_REQUEST['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
        .' group by file order by clicks desc;');
    
    //die("<pre>".print_r($d,1)."</pre>");

    if(!is_array($d) || count($d) == 0) {
        echo "Keine Daten gefunden!";
        return;
    }
    
    // SQL Abfrage für Zeiträume
    $times = dbExtend::getInstance()->get('select max(time) as "max", min(time) as "min" from gameserverlogdata'
        .' where userid = '.sqlescape($_REQUEST['uid']).' and serverid = '.sqlescape($_REQUEST['sid']));

    // Fehlerprüfung für $times
    $min_time = (isset($times) && is_array($times) && count($times) > 0 && isset($times[0]->min)) ? $times[0]->min : 'unbekannt';
    $max_time = (isset($times) && is_array($times) && count($times) > 0 && isset($times[0]->max)) ? $times[0]->max : 'unbekannt';
    
    echo "<br><h3>Klicks f&uuml;r den Zeitraum vom ".$min_time." bis ".$max_time.'</h3>';
    echo '<table><thead><th> Anzeigen</th><th> File</th><th> Clicks</th></thead><tbody>';
    if(is_array($d)) {
        foreach($d as $row) {
            if(isset($row->file) && isset($row->clicks)) {
                echo '<tr><td><input type="checkbox" name="show['.$row->file.']" value="1" checked></td><td>'.$row->file.'</td><td>'.$row->clicks.'</td></tr>';
            }
        }
    }
    echo '</tbody></table>';
    //    echo "<pre>".print_r($d,1)."</pre>";
}



if(isset($_GET['job']) && $_GET['job'] == 'loadDay') {
    // Stelle sicher, dass day existiert
    if(!isset($_GET['day']) || empty($_GET['day'])) {
        echo "<tr><td colspan=\"25\">Fehler: Kein Datum angegeben</td></tr>";
    } else {
        $d = dbExtend::getInstance()->get('select hour(time) as "hour", count(hour(time)) as clicks  '
            .'from gameserverlogdata '
            ." where time >= '".sqlescape($_GET['day'])."'"
            ." and time <= adddate('".sqlescape($_GET['day'])."',1)"
            .' and userid='.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
            .' group by hour(time) order by hour(time) asc  limit 30;','hour');

        echo '<tr><td class="remove">remove</td><td>'.htmlspecialchars($_GET['day']).'</td>';
        
        // Initialisiere mit 0 Klicks für alle Stunden
        $hours = array();
        for($i=0; $i<=23; $i++) {
            $hours[$i] = (object)['clicks' => 0];
        }
        
        // Überschreibe mit tatsächlichen Werten wenn vorhanden
        if(is_array($d)) {
            foreach($d as $hour => $data) {
                if(is_numeric($hour) && $hour >= 0 && $hour <= 23) {
                    $hours[$hour] = $data;
                }
            }
        }
        
        // Ausgabe für alle Stunden
        for($h=0; $h<=23; $h++) {
            $clicks = isset($hours[$h]->clicks) ? $hours[$h]->clicks : 0;
            echo '<td class="hour" hour="'.htmlspecialchars($_GET['day']).' '.$h.':00:00">'.$clicks."</td>";
        }
        echo "</tr>";
    }
}

if(isset($_GET['job']) && $_GET['job'] == 'loadLog') {
    // Standard-Datum falls nicht gesetzt
    $defaultDate = date('Y-m-d');
    
    // Stelle sicher, dass startDate gesetzt ist und ordentlich formatiert ist
    $startDate = isset($_GET['startDate']) ? formatDateTime($_GET['startDate']) : $defaultDate;
    
    if(isset($_GET['logType']) && $_GET['logType'] == 'communication') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape($startDate)."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("hyperfunk","efta_chat","chat")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if(isset($_GET['logType']) && $_GET['logType'] == 'military') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape($startDate)."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("military","militarybs")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if(isset($_GET['logType']) && $_GET['logType'] == 'scan') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape($startDate)."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("secret")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if(isset($_GET['logType']) && $_GET['logType'] == 'militaryscan') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape($startDate)."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("secret","military","militarybs")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if(isset($_GET['logType']) && $_GET['logType'] == 'sekstatsek') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape($startDate)."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("sector","secstatus")'
    .' order by time asc'
    .' limit 30';

    if(isset($_GET['logType']) && $_GET['logType'] == 'bk') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape(isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d'))."'"
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("bkmenu")'
    .' order by time asc'
    .' limit 30';

    if(!isset($query)) $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    ." where time >= '".sqlescape(isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d'))."'"
    .' and userid='.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .((isset($_GET['show']) && is_array($_GET['show']))?' and file in("'.implode('","',array_keys($_GET['show'])).'")':'')
    .' order by time asc'
    .' limit 30';

    // Debug-Ausgabe der SQL-Abfrage für die Fehlersuche
    //echo '<div style="font-size:10px;color:#999;">DEBUG SQL: ' . htmlspecialchars($query) . '</div>';

    // Behandle Datumsformate und Zeitangaben richtig mit Anführungszeichen
    // Ersetze fehlerhafte Zeitangaben wie '2025-07-30 8:00:00' mit '2025-07-30 08:00:00'
    $query = preg_replace('/(time >= )([^\'"])([0-9]{4}-[0-9]{2}-[0-9]{2}\s+[0-9]{1,2}:[0-9]{2}:[0-9]{2})/', '$1\'$3\'', $query);
    
    $d = dbExtend::getInstance()->get($query);

    echo '<table><thead><tr><th>IP</th><th>Time</th><th>File</th><th>getpost</th></tr></thead><tbody>';
    
    // Prüfe, ob Datensätze vorhanden sind
    if (empty($d) || !is_array($d) || count($d) == 0) {
        echo '<tr><td colspan="4">Keine Daten für diesen Zeitraum gefunden.</td></tr>';
        echo '</tbody></table>';
        return;
    }
    
    $num = 0;
    $firstRow = $d[0];
    $lastRow = $firstRow;
    $index = isset($firstRow->time) ? (floor(strtotime($firstRow->time) / 60) * 60) + 60 : 0;
    
    foreach($d as $row) {
        if (isset($row->time) && strtotime($row->time) > $index) {
            $num = abs($num-1);
            $index = (floor(strtotime($row->time) / 60) * 60) + 60;
        }
        
        // Sicherheitsabfragen für alle Felder
        $ip = isset($row->ip) ? htmlspecialchars($row->ip) : '';
        $time = isset($row->time) ? htmlspecialchars($row->time) : '';
        $file = isset($row->file) ? htmlspecialchars($row->file) : '';
        $getpost = isset($row->getpost) ? htmlspecialchars($row->getpost) : '';
        
        // Zeitdifferenz berechnen, falls beide Zeitangaben vorhanden
        $timeDiff = '';
        if (isset($row->time) && isset($lastRow->time)) {
            $timeDiff = ' (' . (strtotime($row->time) - strtotime($lastRow->time)) . 's)';
        }
        
        // IP-Änderungen markieren
        $ipClass = '';
        if (isset($lastRow->ip) && isset($row->ip) && $lastRow->ip != $row->ip) {
            $ipClass = ' class="ipChanged"';
        }
        
        echo '<tr class="row' . $num . '">' 
            . '<td' . $ipClass . '>' . $ip . '</td>'
            . '<td>' . $time . $timeDiff . '</td>'
            . '<td>' . $file . '</td>'
            . '<td class="tooltip" alt="<pre>' . $getpost . '</pre>">' 
            . substr($getpost, 0, 70) . ((strlen($getpost) > 70) ? '...' : '') . '</td></tr>';
            
        $lastRow = $row;
    }
    
    // Verwende die Zeit des letzten Datensatzes, falls vorhanden
    $lastTime = isset($lastRow->time) ? $lastRow->time : date('Y-m-d H:i:s');
    echo '</tbody></table><input type="hidden" name="startDate" value="' . $lastTime . '">';

}

?>
