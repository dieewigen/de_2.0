<?php
//require 'det_userdata.inc.php';
require '../../inc/sv.inc.php';

#if(empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' )
#        header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") and exit();
/**
 * Description of index
 *
 * @author Rainer Zerbe - rz.php-projects@i-it-s.de

 *
 */
define('DIRECT',1);

require_once 'db.inc.php';
require_once 'database.php';
require_once 'dbExtend.php';
require_once 'logfile2database.php';
require_once 'viewer.php';

///////////////////////////////////////////////
///////////////////////////////////////////////
// festlegen wer überprüft werden soll
///////////////////////////////////////////////
///////////////////////////////////////////////
if(isset($_REQUEST['userid1']) OR isset($_REQUEST['userid2']) OR isset($_REQUEST['userid3']) OR isset($_REQUEST['userid4']) OR isset($_REQUEST['userid5']) 
OR isset($_REQUEST['dayOverview']) OR isset($_REQUEST['hourOverview'])){

}else{
	echo '<form action="index.php" method="POST" name="f">';
	echo '<br>User-IDs eintragen: <br>';
	echo '<input type="input" name="userid1" value="">';
	echo '<br><input type="input" name="userid2" value="">';
	echo '<br><input type="input" name="userid3" value="">';
	echo '<br><input type="input" name="userid4" value="">';
	echo '<br><input type="input" name="userid5" value="">';
	echo '<br><input type="Submit" name="weiter" value="weiter">';
	echo '</form>';
	die();
}

$uid1=isset($_REQUEST['userid1']) ? intval($_REQUEST['userid1']) : 0;
$uid2=isset($_REQUEST['userid2']) ? intval($_REQUEST['userid2']) : 0;
$uid3=isset($_REQUEST['userid3']) ? intval($_REQUEST['userid3']) : 0;
$uid4=isset($_REQUEST['userid4']) ? intval($_REQUEST['userid4']) : 0;
$uid5=isset($_REQUEST['userid5']) ? intval($_REQUEST['userid5']) : 0;

$v = new viewer($uid1, $uid2, $uid3, $uid4, $uid5);

if(isset($_GET['hourOverview']) && $_GET['hourOverview']) {
    die( $v->getHourOverview(isset($_GET['date']) ? $_GET['date'] : '', isset($_GET['hour']) ? $_GET['hour'] : '') );
}
if(isset($_GET['dayOverview']) && $_GET['dayOverview']) {
    die( $v->getDayTableRow(isset($_GET['date']) ? $_GET['date'] : '') );
}    

// hier die ID´s rein die im viewer selbst angezeigt werden sollen
// wenn weniger, einfach entfernen

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
    
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>DE LogViewer by Corwin</title>
        <link rel="stylesheet" href="css/all.css" type="text/css"
              media="print, projection, screen" />
        <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

        <script type="text/javascript" src="jq/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="jq/jquery-ui-1.7.custom.min.js"></script>
        <script type="text/javascript" src="jq/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="jq/jTPS.js"></script>
        <script type="text/javascript" src="jq/jquery.jHelperTip.1.0.min.js"></script>



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
                    modal:false,
                    //                buttons: {},
                    stack: true,
                    title:'Stundenansicht'
                } );


            })
            function loadHour(d,h) {
                $('#loading').show();
                $.get('?hourOverview=1&userid1=<?php echo $uid1?>&userid2=<?php echo $uid2?>&userid3=<?php echo $uid3?>&userid4=<?php echo $uid4?>&userid5=<?php echo $uid5?>&date='+d+'&hour='+h,function(d){
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
                $.get('?dayOverview=1&userid1=<?php echo $uid1?>&userid2=<?php echo $uid2?>&userid3=<?php echo $uid3?>&userid4=<?php echo $uid4?>&userid5=<?php echo $uid5?>&date='+$('#day2load').val(),function(d){
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
        <style>

            .jTPS {
                width:100%;

            }
            .jTPS tbody {
                /*overflow:scroll;
                height:500px;
                padding-right:30px;*/
            }
            .jTPS thead {
                /*width:780px;*/
            }
            .jTPS tr.row0 {
                background:#CCCCCC;
            }
            .jTPS tr.row1 {
                background:#EEE;
            }
            .jTPSDays tbody {
                background:#fff;
            }
            #loading {
                display:none;
            }
            #jHelperTipAttrContainer {
                border:1px solid;
                background:white;
                z-index:1100;
            }
        </style>

    </head>
    <body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">
    
 <?php    
echo 'Untersuche folgende User-IDs: '.$uid1.','.$uid2.','.$uid3.','.$uid4.','.$uid5
    ?>  
        <br>Datum in die Tagesübersicht  <input type="text" name="addDate" class="datepicker" id="day2load"> <button onclick="startLoadDay();">hinzufügen</button><br>
        <center><span id="loading"> <img src="sandclock.gif"> Lade Daten, ein Moment geduld bitte</span></center>
        <table class="jTPSDays" border="0">
            <thead><tr><th> Datum </th> <?php for($h=0;$h<=23;$h++) {echo "<th>$h</th>";}?></tr></thead>
            <tbody id="daysTBody">
            </tbody>
            <tfoot></tfoot>
        </table>

        <pre></pre>

        <div id="dialog" title="">
            <p id="hourTable">Daten werden gesichert</p>
        </div>



    </body>
</html>

