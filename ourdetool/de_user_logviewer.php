<?php
/**
 * Description of de_user_logviewer
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de
 * @copyright © Rainer Zerbe - 22.03.2009
 *
 * Hinweis: nutzt jQuery 1.3 / jQuery UI 1.7 aus logviewer/jq/ –
 * inkompatibel mit modernem jQuery, daher bewusst NICHT portiert.
 */
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

// Logging-DB (gameserverlogdata) zusätzlich zur Hauptverbindung aus inccon.php
if (!isset($GLOBALS['dbi_log'])) {
    $GLOBALS['dbi_log'] = mysqli_connect(
        $GLOBALS['env_db_logging_host'],
        $GLOBALS['env_db_logging_user'],
        $GLOBALS['env_db_logging_password'],
        $GLOBALS['env_db_logging_database']
    ) or die("Keine Verbindung zur Logging-Datenbank möglich: " . mysqli_connect_error());
    $GLOBALS['dbi_log']->set_charset("utf8mb4");
}

// Hilfsfunktion für sql_escape (nur noch für nicht-numerische Kontexte)
function sqlescape($value) {
    // Prüfe auf null oder nicht gesetzten Wert und ersetze ihn durch einen leeren String
    if ($value === null) {
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

$userID = req_int('uid');
$serverID = (int)$GLOBALS['sv_servid'];
$uid = $userID;

// Legacy-Assets des Logviewers unverändert in den <head> übernehmen
$page_head_extra = <<<'HTML'
<link rel="stylesheet" type="text/css" href="logviewer/css/embed.css">
<script type="text/javascript" src="logviewer/jq/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="logviewer/jq/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript" src="logviewer/jq/jquery.jHelperTip.1.0.min.js"></script>
<script type="text/javascript">
    $(function(){
        initGui.submit();
        initGui.tabs();
        initGui.tooltip();
        initGui.button();
    });
    var initGui = new function() {
        this.tooltip = function(){
            $(".tooltip").jHelperTip({
                 trigger: "click",
                source: "attribute",
                attrName: "alt",
                opacity: 1,
                autoClose:true

            });
        }
        this.button = function () {
            $('button').unbind('click').bind('click',function(){
                if(typeof submitter[$(this).attr('job')] == 'function') {
                    $('#job').val($(this).attr('job'));
                    $('form[name="allData"]').submit();
                }
                return false;
            });
        }
        this.submit = function() {
            $('form[name="allData"]').submit(function(){
                var data = {};
                $.each($('form[name="allData"] :input'),function(a,b) {
                    if((b.type=='checkbox') && (!b.checked)) return;
                    data[b.name] = b.value;
                });
                loading(true);
                $.get($(this).attr('action'), data, function(d) {
                    loading(false);
                    if(typeof submitter[data.job] == 'function') {
                        submitter[data.job](d);
                    }
                });

                return false;
            });
        }
        this.tabs = function() {
            $('#logViewerTabs').tabs({cache: false,height:'500px' });
        }
    }


    var submitter = new function() {
        this.loadDay = function(d) {
            $(d).appendTo('#daySelect .dayList');
            $('#daySelect .dayList td.remove').unbind('click').bind('click',function() {
                $(this).parent().remove();
            });
            $('#daySelect .dayList td.hour').unbind('click').bind('click',function() {
                $('input[name="startDate"]').val($(this).attr('hour'));
                $('#job').val('loadLog');
                $('form[name="allData"]').submit();
            });
        }
        this.loadLog = function(d) {
            $('#logs div.data').html(d);
            initGui.tooltip();
        }
        this.loadClicks = function(d) {
            $('#config div.data').html(d);
        }
        this.loadTopGenerator = function(d){
            $('#logOverview div.data').html(d);
        }
        this.loadTopSecstat = function(d){
            $('#logOverview div.data').html(d);
        }
        this.loadTopSysnews = function(d){
            $('#logOverview div.data').html(d);
        }
    }
    var loading = function(bool) {
        if(bool) $('#loading').slideDown();
        else $('#loading').stop().slideUp().css({'height':'20px'});
    }
</script>
<style>
    #daySelect .dayList td {
        cursor:pointer;
    }
    [alt] {
        cursor:pointer;
    }
    td.remove {
        cursor:pointer;
    }
    #logViewerTabs table {
        border:0;
    }
    #logViewerTabs table tr{

    }
    #logViewerTabs table tr.row0{

        background:#000;
    }
    #logViewerTabs table tr.row1{

        background:#222;
    }
    #logViewerTabs table td{
        border:0;

        padding:0 20px;

    }
    #jHelperTipAttrContainer{
background:#FFFFff;border:1px solid #FF3333; color:#000000; display:none; font-size:9pt;
}
.ui-widget-content {
 color:#fff;
    }

</style>
HTML;

$page_title = 'Logviewer';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

if ((!$userID) || (!$serverID)) {
    echo '<div class="flash flash-warn">Bitte uid(userId) UND sid(serverId) angeben</div>';
    include "inc.layout.bottom.php";
    exit;
}
?>
        <form name="allData" action="getAjax.php">
            <input type="hidden" name="job" value="" id="job">
            <input type="hidden" name="uid" value="<?php echo $userID?>">
            <input type="hidden" name="sid" value="<?php echo $serverID?>">
            <div id="daySelect">
<?php

// Initialisiere $startDate mit Standardwert
$startDate = date('Y-m-d');

try {
    // Verwenden von mysqli mit Prepared Statement auf der direkten Verbindung
    $query = "SELECT time FROM gameserverlogdata WHERE serverid = ? AND userid = ? ORDER BY time ASC LIMIT 1";
    $result = mysqli_execute_query($GLOBALS['dbi_log'], $query, [$serverID, $userID]);

    // Wenn Ergebnisse gefunden wurden, verwende diese
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object($result);
        if (isset($row->time)) {
            list($startDate, $dummy) = explode(' ', $row->time);
        }
    }
} catch (Exception $e) {
    // Fehlerbehandlung, aber Fortfahren der Seite
    echo '<div style="color: red">Fehler bei der Datenbankabfrage: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

?>
                <input type="text" name="day" value="<?php echo $startDate?>">
                <button job="loadDay">Tag Laden</button>
                <table> <thead> <tr><th>entfernen</th><th>Tag</th>
                            <?php for($i=0;$i<=23;$i++)echo "<th>$i</th>" ?>
                    </tr></thead>
                    <tbody class="dayList">

                </tbody></table>

            </div>

            <div id="logViewerTabs" style="float:left; width:90%;">
                <ul>
                    <li><a href="#logs"><span>Logs</span></a></li>
                    <li><a href="#config"><span>Konfiguration</span></a></li>
                    <li><a href="#logOverview"><span>Allgemeine Übersichten</span></a></li>
                </ul>


                <div id="logs">
                    Spezial Ansichten, teilweise nur die Aufrufe bei denen auch Daten eingegeben wurden, zB. bei Militär nur wenn Flotten verschickt werden<br>
                    <select name="logType">
                        <option value="all">alle(nach Konfiguration)</option>
                        <option value="communication">Kommunikation</option>
                        <option value="military">Militär</option>
                        <option value="scan">Agenten</option>
                        <option value="militaryscan">Militär und Scan</option>
                        <option value="sekstatsek">Sektor & Sekstatus</option>
                        <option value="bk">BK Menu</option>

                    </select>
                    <button job="loadLog">weiter</button>
                    <div class="data"></div>
                </div>
                <div id="logOverview">
                    <button job="loadTopGenerator"> Top /imagegenerator.php </button>
                    <button job="loadTopSecstat"> Top /secstatus.php </button>
                    <button job="loadTopSysnews"> Top /sysnews.php </button>
                    <div class="data"></div>
                </div>
                <div id="config">
                <button job="loadClicks"> Klicks anzeigen </button><br>
                    <button onclick="$('#config .data input[type=checkbox]').attr('checked','checked');"> alle anwählen</button>
                    <button onclick="$('#config .data input[type=checkbox]').removeAttr('checked');">alle abwählen</button>

                    <div class="data"></div>
                </div>
            </div>
        </form>
        <div id="loading" style="position:absolute; top:0; right:0; width:100px; display:none; background:white; color:black; text-align:center;">
            loading....
        </div>
<?php
include "inc.layout.bottom.php";
