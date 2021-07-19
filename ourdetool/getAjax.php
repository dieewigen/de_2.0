<?php
/**
 * Description of getAjax
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de
 * @copyright © Rainer Zerbe - 22.03.2009
 *
 */
require 'det_userdata.inc.php';
require '../inc/sv.inc.php';

define('DIRECT',1);
require_once 'logviewer/class/cfg.php';
require_once 'logviewer/class/database.php';
require_once 'logviewer/class/dbExtend.php';

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

if($_GET['job'] == 'loadClicks') {
    $d = dbExtend::getInstance()->get('select file, count(file) as clicks  '
        .' from gameserverlogdata '
        .' where userid = '.sqlescape($_REQUEST['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
        .' group by file order by clicks desc;');
    //    die("<pre>".print_r($d,1)."</pre>");

    if(!is_array($d)) die('no data found!');
    $times = dbExtend::getInstance()->get('select max(time) as "max", min(time) as "min" from gameserverlogdata'
        .' where userid = '.sqlescape($_REQUEST['uid'])).' and serverid = '.sqlescape($_REQUEST['sid']);

    echo "<br><h3>Klicks f&uuml;r den Zeitraum vom ".$times[0]->min." bis ".$times[0]->max.'</h3>';
    echo '<table><thead><th> Anzeigen</th><th> File</th><th> Clicks</th></thead><tbody>';
    foreach($d as $row) {
        echo '<tr><td><input type="checkbox" name="show['.$row->file.']" value="1" checked></td><td>'.$row->file.'</td><td>'.$row->clicks.'</td></tr>';
    }
    echo '</tbody></table>';
    //    echo "<pre>".print_r($d,1)."</pre>";
}



if($_GET['job'] == 'loadDay') {
    $d = dbExtend::getInstance()->get('select hour(time) as "hour", count(hour(time)) as clicks  '
        .'from gameserverlogdata '
        .' where time >= '.sqlescape($_GET['day'])
        .' and time <= adddate('.sqlescape($_GET['day']).',1)'
        .' and userid='.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
        .' group by hour(time) order by "hour" asc  limit 30;','hour');

    echo '<tr><td class="remove">remove</td><td>'.$_GET['day'].'</td>';
    for($h=0;$h<=23;$h++) {
        echo '<td class="hour" hour="'.$_GET['day'].' '.$h.':00:00">'.$d[$h]->clicks."</td>";
    }
    echo "</tr>";
}

if($_GET['job'] == 'loadLog') {
    if($_GET['logType'] == 'communication') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("hyperfunk","efta_chat","chat")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if($_GET['logType'] == 'military') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("military","militarybs")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if($_GET['logType'] == 'scan') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("secret")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if($_GET['logType'] == 'militaryscan') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("secret","military","militarybs")'
    .' and CHAR_LENGTH(getpost) > 22 '
    .' order by time asc'
    .' limit 30';

    if($_GET['logType'] == 'sekstatsek') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("sector","secstatus")'
    .' order by time asc'
    .' limit 30';

    if($_GET['logType'] == 'bk') $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid= '.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .' and file in("bkmenu")'
    .' order by time asc'
    .' limit 30';

    if(!$query) $query = 'select time, ip, file, getpost '
    .' from gameserverlogdata '
    .' where time >= '.sqlescape($_GET['startDate'])
    .' and userid='.sqlescape($_GET['uid']).' and serverid = '.sqlescape($_REQUEST['sid'])
    .((is_array($_GET['show']))?' and file in("'.implode('","',array_keys($_GET['show'])).'")':'')
    .' order by time asc'
    .' limit 30';

    $d = dbExtend::getInstance()->get($query);

    echo '<table><thead><tr><th>IP</th><th>Time</th><th>File</th><th>getpost</th></tr></thead><tbody>';
    $num =0;
    $index = (floor(strtotime($d[0]->time) / 60) *60) +60;
    $lastRow = $d[0];
    foreach($d as $row) {
        if(strtotime($row->time) > $index) {
            $num = abs($num-1);
            $index = (floor(strtotime($row->time) / 60) *60) +60;
        }
        echo '<tr class="row'.$num.'">'
        .'<td'.(($lastRow->ip != $row->ip)?' class="ipChanged"':'').'>'.$row->ip.'</td>'
        .'<td>'.$row->time.' ('.(strtotime($row->time)-strtotime($lastRow->time)).'s)'.'</td>'
        .'<td>'.$row->file.'</td>'
        .'<td class="tooltip" alt="<pre>'.$row->getpost.'</pre>">'.substr($row->getpost,0,70).((strlen($row->getpost)>70)?'...':'').'</td></tr>';
        $lastRow = $row;
    }
    echo '</tbody></table><input type="hidden" name="startDate" value="'.$row->time.'">';

}

?>
