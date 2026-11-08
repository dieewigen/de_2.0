<?php
/**
 * Compare-Tool ("DE LogViewer by Corwin", Rainer Zerbe):
 * vergleicht die geloggten Aktionen von bis zu fünf User-IDs
 * in einer Tages-/Stundenübersicht aus der Logging-DB.
 * Nutzt jQuery 1.3 / jQuery UI 1.7 aus jq/ – nicht auf modernes jQuery portieren.
 */
require '../../inc/sv.inc.php';
require '../det_userdata.inc.php';

define('DIRECT', 1);

require_once 'db.inc.php';
require_once 'database.php';
require_once 'dbExtend.php';
require_once 'logfile2database.php';
require_once 'viewer.php';

$uid1 = req_int('userid1');
$uid2 = req_int('userid2');
$uid3 = req_int('userid3');
$uid4 = req_int('userid4');
$uid5 = req_int('userid5');

// AJAX-Fragmente (Tageszeile/Stundenansicht) vor jeglicher Layout-Ausgabe beantworten
if (!empty($_GET['hourOverview'])) {
    $v = new viewer($uid1, $uid2, $uid3, $uid4, $uid5);
    die($v->getHourOverview($_GET['date'] ?? '', $_GET['hour'] ?? ''));
}
if (!empty($_GET['dayOverview'])) {
    $v = new viewer($uid1, $uid2, $uid3, $uid4, $uid5);
    die($v->getDayTableRow($_GET['date'] ?? ''));
}

$hasids = isset($_REQUEST['userid1']) || isset($_REQUEST['userid2']) || isset($_REQUEST['userid3'])
    || isset($_REQUEST['userid4']) || isset($_REQUEST['userid5']);

$page_title = 'Compare-Tool';
$active_nav = 'compare';
$layout_base = '../';
if ($hasids) {
    $page_head_extra = <<<'HTML'
<link rel="stylesheet" href="css/embed.css" media="print, projection, screen">
<script src="jq/jquery-1.3.2.min.js"></script>
<script src="jq/jquery-ui-1.7.custom.min.js"></script>
<script src="jq/jquery.tablesorter.js"></script>
<script src="jq/jTPS.js"></script>
<script src="jq/jquery.jHelperTip.1.0.min.js"></script>
HTML;
}
include "../inc.layout.top.php";

if (!$hasids) {
    // Eingabemaske (früher ein ungestyltes echo-Formular ohne <head>)
    ?>
<div class="card">
  <h2>User-IDs vergleichen</h2>
  <form action="index.php" method="post">
    <p>
      <input type="text" name="userid1" value="" placeholder="User-ID 1" autofocus><br><br>
      <input type="text" name="userid2" value="" placeholder="User-ID 2"><br><br>
      <input type="text" name="userid3" value="" placeholder="User-ID 3"><br><br>
      <input type="text" name="userid4" value="" placeholder="User-ID 4"><br><br>
      <input type="text" name="userid5" value="" placeholder="User-ID 5">
    </p>
    <button type="submit" name="weiter">weiter</button>
  </form>
  <p class="dim">Vergleicht die geloggten Aktionen der eingetragenen Accounts in einer Tages-/Stundenübersicht (Multi-Erkennung).</p>
</div>
<?php
    include "../inc.layout.bottom.php";
    exit;
}

$v = new viewer($uid1, $uid2, $uid3, $uid4, $uid5);
?>
<style>
    /* Widget-Styles aus css/embed.css auf das dunkle Layout abstimmen */
    .jTPS { width: 100%; }
    main table.jTPS tr.row0 > td { background: #1b222c; }
    main table.jTPS tr.row1 > td { background: #232c38; }
    #loading { display: none; }
    #jHelperTipAttrContainer { border: 1px solid #30363d; background: #fff; color: #111; z-index: 1100; }
</style>
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
            monthNamesShort: ['Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'],
            changeMonth: true
        });

        $('#dialog').dialog({
            autoOpen: false,
            width: 900,
            height: 600,
            modal: false,
            stack: true,
            title: 'Stundenansicht'
        });
    })
    function loadHour(d,h) {
        $('#loading').show();
        $.get('?hourOverview=1&userid1=<?= $uid1 ?>&userid2=<?= $uid2 ?>&userid3=<?= $uid3 ?>&userid4=<?= $uid4 ?>&userid5=<?= $uid5 ?>&date='+d+'&hour='+h,function(d){
            $('#loading').hide();
            $('#hourTable').html(d);
            $('#dialog').dialog('open');
            $('.jTPS').jTPS({
                perPages:['ALL'],
                scrollStep:1,
                scrollDelay:30,
                fixedLayout:true
            });
            $(".tt").jHelperTip({
                trigger: "hover",
                source: "attribute",
                attrName: "alt",
                opacity: 0.8,
                autoClose:true
            });
        });
    }
    function startLoadDay() {
        $('#loading').show();
        $.get('?dayOverview=1&userid1=<?= $uid1 ?>&userid2=<?= $uid2 ?>&userid3=<?= $uid3 ?>&userid4=<?= $uid4 ?>&userid5=<?= $uid5 ?>&date='+$('#day2load').val(),function(d){
            $('#loading').hide();
            $(d).appendTo('#daysTBody');
            $('.jTPSdays').jTPS({
                perPages:['ALL'],
                scrollStep:1,
                scrollDelay:30,
                fixedLayout:true
            });
        });
    }
</script>

<p>Untersuche folgende User-IDs: <strong><?= $uid1 ?>, <?= $uid2 ?>, <?= $uid3 ?>, <?= $uid4 ?>, <?= $uid5 ?></strong>
&nbsp;&middot;&nbsp;<a href="index.php">andere IDs w&auml;hlen</a></p>

<p>Datum in die Tages&uuml;bersicht
<input type="text" name="addDate" class="datepicker" id="day2load">
<button onclick="startLoadDay();">hinzuf&uuml;gen</button>
<span id="loading"><img src="sandclock.gif" alt=""> Lade Daten, einen Moment Geduld bitte</span></p>

<table class="jTPSDays">
    <thead><tr><th> Datum </th> <?php for ($h = 0; $h <= 23; $h++) { echo "<th>$h</th>"; } ?></tr></thead>
    <tbody id="daysTBody">
    </tbody>
    <tfoot></tfoot>
</table>

<div id="dialog" title="">
    <p id="hourTable">Daten werden gesichert</p>
</div>

<?php include "../inc.layout.bottom.php"; ?>
