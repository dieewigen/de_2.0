<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_hyperfunk.lang.php');
require_once('lib/phpmailer/class.phpmailer.php');
require_once('lib/phpmailer/class.smtp.php');

$db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, newtrans, newnews, sector, `system` FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row['score'];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$asec = $row['sector'];
$asys = $row['system'];
$sector = $asec;
$system = $asys;

if ($newtrans == 1) { //wenn einen neue nachricht vorlag, den indikator wieder auf 0 setzen
    mysql_query("UPDATE de_user_data SET newtrans = 0 WHERE user_id='$ums_user_id'", $db);
}
$newtrans = 0;

$l=$_REQUEST['l'] ?? '';

include_once 'functions.php';
// Sperre f&uuml;r den Fall, dass user das Script abbrechen.
@ignore_user_abort();
// eine Funktion, die die Statusmeldungen anzeigt.
function insertmessage($message, $color, $lang_systemnachricht)
{
    if ($color == "r") {
        $col = "FF0000";
    }
    if ($color == "g") {
        $col = "00FF00";
    }
    if ($color == "b") {
        $col = "3399FF";
    }
    $nachricht = '<br><br>
    <table border="0" cellpadding="0" cellspacing="0" width="586">
    <tr>
    <td width="13" height="35" class="rol"></td>
    <td align=center height="35" class="ro"><font size=3><div class="cellu">'.$lang_systemnachricht.'</div></font></td>
    <td width="13" height="35" class="ror"></td>
    </tr>
    <tr><td width="13" class="rl" height=35></td>
    <td align="center" nowrap class="c"><font color='. $col .'>'. $message.'</font></td>
    <td width="13" class="rr" height=35></td></tr>
    <tr>
    <td width="13" class="rul">&nbsp;</td>
    <td class="ru">&nbsp;</td>
    <td width="13" class="rur">&nbsp;</td>
    </tr>
    </table><br><br>';
    return $nachricht;
}

function insertemp($spieler)
{
    $empfa = mysql_query("SELECT sector, `system`, spielername FROM de_user_data WHERE user_id=$spieler");
    $rowemp = mysql_fetch_array($empfa);

    $namekoords = $rowemp[sector].':'.$rowemp['system'].' ('.$rowemp['spielername'].')';

    return $namekoords;
}
?>
<!doctype html>
<html>
<head>
<title><?php echo $hyperfunk_lang['headtitle']?></title>
<?php include "cssinclude.php";

//ein bisschen CSS f&uuml;r die Buttons
echo '
<style type="text/css">
<!--
.fett{color: #FFFFFF;font-weight: bold;}
-->
</style>';


if (!isset($action)) {
    $action = "eingang";
}

if ($action == "ant" || $action == "weiter" || $action == "spieler" || $action == "sektor" || $action == "freunde" || $action == "alli") {
    // Javascript, f&uuml;r die ueberpruefung, ob alle Felder ausgefuellt sind und um per klick den richtigen BB code in die Textarea einzufuegen

    ?>
<script language="JavaScript" type="text/javascript">
<!--

function zeichenundsmiliecheck(){

var nachricht =  document.getElementById("nachricht").value;
var zeichen = document.getElementById("nachricht").value.length;
var i=0;
var j=2;
var smilies=0;

while(i<zeichen){
    if(nachricht.substring(i,j)==":)" || nachricht.substring(i,j+1)=="(!)" || nachricht.substring(i,j+1)=="(?)" || nachricht.substring(i,j)==":L" || nachricht.substring(i,j)==":}" || nachricht.substring(i,j)==":{" || nachricht.substring(i,j)==":p" || nachricht.substring(i,j)=="x(" || nachricht.substring(i,j)==":(" || nachricht.substring(i,j)==":x" || nachricht.substring(i,j)==";)" || nachricht.substring(i,j)==":D")
    {
    smilies++;
    }
    j++;
    i++;
}

if(smilies>20)alert("<?php echo $hyperfunk_lang['err_zu_viele_smilies']?>");

if(document.getElementById("nachricht").value.length>10000)alert("<?php echo $hyperfunk_lang['err_zu_viele_zeichen']?>");

if(smilies<=20 && document.getElementById("nachricht").value.length<=10000)
{
var temp=0;

}



if(temp==0)
{



<?php
if ($action == "ant" || $action == "weiter" || $action == "spieler") {
    ?>

if(document.getElementById("zielsek").value==""||document.getElementById("zielsys").value=="")
{
alert("<?php echo $hyperfunk_lang['err_fehlerhaftekoords']?>");
return false;
}
else
{
return true;
}


<?php
} else {
    ?>
return true;
<?php
}
    ?>
}
else
{

return false;
}


}


function check()
{
var nachricht =  document.getElementById("nachricht").value;
var zeichen = document.getElementById("nachricht").value.length;
var i=0;
var j=2;
var smilies=0;

while(i<zeichen)
{
if(nachricht.substring(i,j)==":)" || nachricht.substring(i,j+1)=="(!)" || nachricht.substring(i,j+1)=="(?)" || nachricht.substring(i,j)==":L" || nachricht.substring(i,j)==":}" || nachricht.substring(i,j)==":{" || nachricht.substring(i,j)==":p" || nachricht.substring(i,j)=="x(" || nachricht.substring(i,j)==":(" || nachricht.substring(i,j)==":x" || nachricht.substring(i,j)==";)" || nachricht.substring(i,j)==":D")
{
smilies++;
}
j++;
i++;
}

if(document.getElementById("nachricht").value.length>=10000 || smilies>20)
{
alert("<?php echo $hyperfunk_lang['msg_zeichensmilie']?>");
}
else
{
alert("<?php echo $hyperfunk_lang['msg_summezeichensmilie1']?> " + document.getElementById("nachricht").value.length + " <?php echo $hyperfunk_lang['msg_summezeichensmilie2']?> "+ (10000 - document.getElementById("nachricht").value.length) +" <?php echo $hyperfunk_lang['msg_summezeichensmilie3']?> " + smilies + " <?php echo $hyperfunk_lang['msg_summezeichensmilie4']?> " + (20 - smilies) + " <?php echo $hyperfunk_lang['msg_summezeichensmilie5']?>");
}
}

function leeren() {(document.getElementById("nachricht").value) = "";document.getElementById("nachricht").focus();}

function hilfe()
{window.open("hfnlegende.php","BitteBeachten","width=572,height=314,left=34,top=75");}

function cursor()
{
if ((navigator.appName=="Netscape")||(navigator.userAgent.indexOf("Opera") != -1)||(navigator.userAgent.indexOf("Netscape") != -1)) {
text_before = document.getElementById("nachricht") .value;
text_after = "";
} else {
document.getElementById("nachricht").focus();
var sel = document.selection.createRange();
sel.collapse();
var sel_before = sel.duplicate();
var sel_after = sel.duplicate();
sel.moveToElementText(document.getElementById("nachricht"));
sel_before.setEndPoint("StartToStart",sel);
sel_after.setEndPoint("EndToEnd",sel);
text_before = sel_before.text;
text_after = sel_after.text;
}
}
function insert(AddCode) {
cursor();
document.getElementById("nachricht").value = text_before + AddCode + text_after;
document.getElementById("nachricht").focus();
}

function init(thisCode) {
with ( document.getElementById("nachricht").value ) {
switch(thisCode) {

case "fett":
insert("[b] [/b]");
break;

case "kursiv":
insert("[i] [/i]");
break;

case "under":
insert("[u] [/u]");
break;

case "center":
insert("[center] [/center]");
break;

case "mail":
insert("[email] [/email]");
break;

case "www":
insert("[url] [/url]");
break;

case "pre":
insert("[pre] [/pre]");
break;

case "rot":
insert("[CROT]");
break;

case "gelb":
insert("[CGELB]");
break;

case "gruen":
insert("[CGRUEN]");
break;

case "weiss":
insert("[CW]");
break;

case "farbe":
insert("[color=#] [/color]");
break;

case "size":
insert("[size=] [/size]");
break;

case "smile1":
insert(":)");
break;

case "smile2":
insert(":D");
break;

case "smile3":
insert(";)");
break;

case "smile4":
insert(":x");
break;

case "smile5":
insert(":(");
break;

case "smile6":
insert("x(");
break;

case "smile7":
insert(":p");
break;

case "smile8":
insert("(?)");
break;

case "smile9":
insert("(!)");
break;

case "smile10":
insert(":{");
break;

case "smile11":
insert(":}");
break;

case "smile12":
insert(":L");
break;

case "smile13":
insert(":nene:");
break;

case "smile14":
insert(":eek:");
break;

case "smile15":
insert(":applaus:");
break;

case "smile16":
insert(":cry:");
break;

case "smile17":
insert(":sleep:");
break;

case "smile18":
insert(":rolleyes:");
break;

case "smile19":
insert(":wand:");
break;

case "smile20":
insert(":dead:");
break;
}
document.getElementById("nachricht").focus();
}
}
//-->
</script>
<?php
}
?>
</head>
<body>
<center>
<?php
//stelle die ressourcenleiste dar
include('resline.php');
// das Menu

echo '<br>';
echo '<table border="0" cellspacing="0" cellpadding="0" width="586">
<tr>
<td width="13" height="25" class="rol"></td>
<td align="center" height="35" colspan="4" class="ro"><div class="cellu">'.$hyperfunk_lang['tabtitle1'].'</div></td>
<td width="13" height="25" class="ror"></td>
</tr>
<tr class="cell">
    <td width="13" class="rl" height="35"></td>
    <td align="center"><a href="hyperfunk.php?action=eingang" class="btn">'.$hyperfunk_lang['eingang'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=ausgang" class="btn">'.$hyperfunk_lang['ausgang'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=archiv" class="btn">'.$hyperfunk_lang['archiv'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=optionen" class="btn">'.$hyperfunk_lang['optionen'].'</a></td>
    <td width="13" height="25" class="rr"></td>
</tr>
</table>';

echo '<table border="0" cellspacing="0" cellpadding="0" width="586">
<tr>
<td width="13" height="25" class="rml"></td>
<td align="center" class="ro" colspan="4" height="35"><div class="cellu">'.$hyperfunk_lang['tabtitle2'].'</div></td>
<td width="13" height="25" class="rmr"></td>
</tr>
<tr class="cell">
    <td width="13" height="25" class="rl"></td>
    <td align="center"><a href="hyperfunk.php?action=spieler" class="btn">'.$hyperfunk_lang['spieler'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=sektor" class="btn">'.$hyperfunk_lang['sektor'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=alli" class="btn">'.$hyperfunk_lang['allianz'].'</a></td>
    <td align="center"><a href="hyperfunk.php?action=freunde" class="btn">'.$hyperfunk_lang['freunde'].'</a></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru" colspan="4">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>';

// Insert abschnitt f&uuml;r normale HFNs mit s&auml;mtlichen Ueberpruefungen (Ignore Urlaub falsche koords)
if (isset($_POST['antbut'])) {
    $zielsek = intval($_POST['zielsek']);
    $zielsys = intval($_POST['zielsys']);

    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    $nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);

    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'", $db);
    $row = mysql_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($nachricht == "") {
        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif (validdigit($zielsek) && validdigit($zielsys)) {
        $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zielsek' AND `system`='$zielsys'", $db);
        @$num = mysql_num_rows($db_daten);

        @$uid = mysql_result($db_daten, 0, "user_id");

        $db_ignore = mysql_query("SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id='$uid' and `system`='$asys' and sector='$asec' and status=2", $db);
        $numignore = mysql_num_rows($db_ignore);


        $db_aktiv = mysql_query("SELECT status FROM de_login WHERE user_id='$uid'", $db);
        $rowaktiv = mysql_fetch_array($db_aktiv);


        if ($num == 1) {
            if ($numignore == "0") {
                if ($rowaktiv['status'] == "1") {

                    $time = strftime("%Y%m%d%H%M%S");

                    $se = (int)$se;
                    $sy = (int)$sy;

                    include('outputlib.php');

                    mysql_query("INSERT into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values ('$uid', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', '$betreff', '$nachricht',0)", $db);
                    mysql_query("INSERT into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values ('$uid', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', '$betreff', '$nachricht',1)", $db);

                    mysql_query("UPDATE de_user_data SET newtrans = 1 WHERE user_id='$uid' and user_id!='$ums_user_id'", $db);

                    echo insertmessage($hyperfunk_lang['msg_2'], "g", $hyperfunk_lang['systemnachricht']);

                    $action = "";
                } else {
                    echo insertmessage($hyperfunk_lang['msg_5'], "r", $hyperfunk_lang['systemnachricht']);
                }
            } else {
                echo insertmessage($hyperfunk_lang['msg_4'], "r", $hyperfunk_lang['systemnachricht']);
            }
        } else {
            echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
        }
    }
}
// Insert f&uuml;r die Sektornachricht
$sekmsg = $_POST['sekmsg'] ?? '';
if ($sekmsg && $asec == 1) {
    echo insertmessage($hyperfunk_lang['msg_6'], "r", $hyperfunk_lang['systemnachricht']);
}
if ($sekmsg && $asec != 1) {
    $time = date("YmdHis");

    include_once 'outputlib.php';
    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    $nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);


    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'", $db);
    $row = mysql_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($nachricht == "") {

        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } else {
        $sekhfn = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$asec'");
        $igmsg = 0;
        while ($row = mysql_fetch_array($sekhfn)) {

            $db_ignore = mysql_query("SELECT * FROM de_hfn_buddy_ignore WHERE user_id='$row[user_id]' and `system`='$asys' and sector='$asec' and status=2", $db);
            $numignore = mysql_num_rows($db_ignore);

            if ($numignore == 0) {
                mysql_query("update de_user_data set newtrans=1 where user_id=$row[user_id] and user_id!='$ums_user_id'");
                mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES ('$row[user_id]', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', 'Sektorrundmail: $betreff', '$nachricht',0)", $db);
            } else {
                $igmsg++;
            }
        }
        mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES ('$ums_user_id', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', 'Sektorrundmail: $betreff', '$nachricht',1)", $db);


        if ($igmsg == 0) {
            echo insertmessage($hyperfunk_lang['msg_7'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_8'], "g", $hyperfunk_lang['systemnachricht']);
        }
    }
}

// insert f&uuml;r die Alli HFN die bein Co/Leader landet
if(isset($_POST['allimsg'])) {

    $time = date("YmdHis");
    
    include_once 'outputlib.php';
    $betreff = $_POST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_POST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    $nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);


    //test auf comsperre
    $akttime = date("Y-m-d H:i:s", time());
    $db_daten = mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'", $db);
    $row = mysql_fetch_array($db_daten);
    if ($row['com_sperre'] > $akttime) {
        $sperrtime = strtotime($row['com_sperre']);
        echo insertmessage('Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime), "r", $hyperfunk_lang[systemnachricht]);
    } elseif ($nachricht == "") {
        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } else {
        $holalli = mysql_query("SELECT allytag FROM de_user_data WHERE user_id=$ums_user_id");
        $row = mysql_fetch_array($holalli);
        $alli = $row[allytag];
        $hfnforward = mysql_query("SELECT hfn_forwarding FROM de_allys WHERE allytag='$alli'");
        $rowhfnforward = mysql_fetch_array($hfnforward);

        if ($rowhfnforward['hfn_forwarding'] == "1") {
            $resource = mysql_query("SELECT user_id FROM de_user_data WHERE allytag='$alli' AND status=1");
            $igmsg = 0;
            while ($rowa = mysql_fetch_array($resource)) {

                $db_ignore = mysql_query("SELECT * FROM de_hfn_buddy_ignore WHERE user_id='$rowa[user_id]' and `system`='$asys' and sector='$asec' and status=2", $db);
                $numignore = mysql_num_rows($db_ignore);

                if ($numignore == 0) {
                    mysql_query("update de_user_data set newtrans = 1 where user_id = $rowa[user_id] and user_id!='$ums_user_id'");
                    mysql_query("insert into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values ('$rowa[user_id]', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', 'Allianzrundmail: $betreff', '$nachricht',0)", $db);
                } else {
                    $igmsg++;
                }

            }
            mysql_query("insert into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values ('$ums_user_id', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', 'Allianzrundmail: $betreff', '$nachricht',1)", $db);
            if ($igmsg == 0) {
                echo insertmessage($hyperfunk_lang['msg_9'], "g", $hyperfunk_lang['systemnachricht']);
            } else {
                echo insertmessage($hyperfunk_lang['msg_10'], "g", $hyperfunk_lang['systemnachricht']);
            }

        } else {

            mysql_query("INSERT INTO de_hfn_usr_ally (allytag, absender, fromsec, fromsys, fromnic, time, betreff, text) VALUES ('$row[allytag]', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', 'Allianzrundmail: $betreff', '$nachricht')", $db);

            $holleader = mysql_query("SELECT leaderid,  coleaderid1,  coleaderid2 FROM de_allys WHERE allytag='$row[allytag]'");
            $rowleader = mysql_fetch_array($holleader);

            mysql_query("update de_user_data set  newnews=1 where user_id=$rowleader[leaderid]");
            mysql_query("INSERT INTO de_user_news(user_id ,typ,time,text,seen) VALUES ('$rowleader[leaderid]', '6','$time','Eines ihrer Allianzmitglieder hat eine Allianzrundmail abgeschickt. Diese kann <a href=ally_message.php><b>hier</b></a> weitergeleitet werden.',0)", $db);
            if ($rowleader['coleaderid1'] != "-1") {
                mysql_query("update de_user_data set  newnews=1 where user_id=$rowleader[coleaderid1]");
                mysql_query("INSERT INTO de_user_news(user_id ,typ,time,text,seen) VALUES ('$rowleader[coleaderid1]', '6','$time','Eines ihrer Allianzmitglieder hat eine Allianzrundmail abgeschickt. Diese kann <a href=ally_message.php><b>hier</b></a> weitergeleitet werden.',0)", $db);
            }
            if ($rowleader['coleaderid2'] != "-1") {
                mysql_query("update de_user_data set  newnews=1 where user_id=$rowleader[coleaderid2]");
                mysql_query("INSERT INTO de_user_news(user_id ,typ,time,text,seen) VALUES ('$rowleader[coleaderid2]', '6','$time','Eines ihrer Allianzmitglieder hat eine Allianzrundmail abgeschickt. Diese kann <a href=ally_message.php><b>hier</b></a> weitergeleitet werden.',0)", $db);
            }
            echo insertmessage($hyperfunk_lang['msg_11'], "g", $hyperfunk_lang['systemnachricht']);
        }
    }

}
// Insert fuer die Freunde
if (isset($_POST['freundemsg'])) {
    $db_freunde = mysql_query("SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id='$ums_user_id' AND status=1");

    $time = date("YmdHis");

    $anzahl_freunde = mysql_num_rows($db_freunde);

    include_once 'outputlib.php';

    $betreff = $_REQUEST['betreff'];
    $betreff = str_replace('<', '&lt;', $betreff);
    $betreff = str_replace('>', '&gt;', $betreff);
    $betreff = nl2br($betreff);
    $betreff = str_replace('\"', '&quot;', $betreff);
    $betreff = str_replace('\'', '&acute;', $betreff);
    $betreff = str_replace('script', 'schkript', $betreff);
    $betreff = str_replace('Script', 'Schkript', $betreff);

    $nachricht = $_REQUEST['nachricht'];
    $nachricht = str_replace('<', '&lt;', $nachricht);
    $nachricht = str_replace('>', '&gt;', $nachricht);
    $nachricht = nl2br($nachricht);
    $nachricht = str_replace('\"', '&quot;', $nachricht);
    $nachricht = str_replace('\'', '&acute;', $nachricht);
    $nachricht = str_replace('script', 'schkript', $nachricht);
    $nachricht = str_replace('Script', 'Schkript', $nachricht);

    if ($nachricht == "") {
        echo insertmessage($hyperfunk_lang['msg_1'], "r", $hyperfunk_lang['systemnachricht']);
    } else {

        $fc = 1;
        $fi = 0;
        $igmsg = 0;
        while ($fi < $anzahl_freunde) {
            $oldfriend = '\$freund$fc';
            eval("\$oldfriend = \"$oldfriend\";");
            eval("\$oldfriend = \"$oldfriend\";");
            if ($oldfriend != "") {
                $oldfriendcoords = explode(":", $oldfriend);

                $db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$oldfriendcoords[0]' AND `system`='$oldfriendcoords[1]' ", $db);
                $uid = mysql_result($db_daten, 0, "user_id");

                $db_ignore = mysql_query("SELECT * FROM de_hfn_buddy_ignore WHERE user_id='$uid' and `system`='$asys' and sector='$asec' and status=2", $db);
                $numignore = mysql_num_rows($db_ignore);

                if ($numignore == 0) {
                    mysql_query("update de_user_data set newtrans = 1 where user_id=$uid");
                    mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES ('$uid', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', '$betreff', '$nachricht',0)", $db);
                    mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES ('$uid', '$ums_user_id', '$asec', '$asys', '$ums_spielername', '$time', '$betreff', '$nachricht',1)", $db);
                } else {
                    $igmsg++;
                }
            }

            $fc++;
            $fi++;
        }

        if ($igmsg == 0) {
            echo insertmessage($hyperfunk_lang['msg_12'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_13'], "g", $hyperfunk_lang['systemnachricht']);
        }


    }
}
//Loeschen einzelner HFNs
if ($action == "del") {//nachricht l&ouml;schen
    $id = intval($_REQUEST['id']);
    $se = (int)$se;
    $sy = (int)$sy;

    mysql_query("DELETE FROM de_user_hyper WHERE (id='$id' AND empfaenger='$ums_user_id' AND sender=0) or (id='$id' AND absender=$ums_user_id AND sender=1)", $db);

    echo insertmessage($hyperfunk_lang['msg_14'], "r", $hyperfunk_lang['systemnachricht']);

    $action = "";

    if ($o == "v") {
        $action = "archiv";
    }
    if ($o == "e") {
        $action = "eingang";
    }
    if ($o == "a") {
        $action = "ausgang";
    }
}

//Loeschen vieler HFNs aus einer Kategorie
if ($action == "da" and ($l == "e" or $l == "a" or $l == "r")) {
    echo '<br><br><table border="0" cellpadding="0" cellspacing="0" width="400">

  <tr>
  <td width="13" height="35" class="rol"></td>
  <td align="center" height="35" class="ro"><font size="3"><div class="cellu">'.$hyperfunk_lang['systemnachricht'].'</div></font></td>
  <td width="13" height="35" class="ror"></td>
  </tr>

  <tr><td width="13" class="rl" height="35"></td>';
    // Eingang
    if ($action == "da" and $l == "e") {
        mysql_query("DELETE FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 and gelesen=1", $db);
        echo '<td align="center" nowrap class="cellu">'.$hyperfunk_lang['msg_15'].'</td>';
    }
    // Ausgang
    if ($action == "da" and $l == "a") {
        mysql_query("DELETE FROM de_user_hyper WHERE absender='$ums_user_id' and sender=1 and archiv=0", $db);
        echo '<td align="center" nowrap class="cellu">'.$hyperfunk_lang['msg_16'].'</td>';
    }
    // Archiv
    if ($action == "da" and $l == "r") {
        mysql_query("DELETE FROM de_user_hyper WHERE empfaenger='$ums_user_id' and archiv=1", $db);
        echo '<td align="center" nowrap class="cellu">'.$hyperfunk_lang['msg_17'].'</td>';
    }

    echo '<td width="13" class="rr" height=35></td></tr>
  <tr>
  <td width="13" class="rul">&nbsp;</td>
  <td class="ru">&nbsp;</td>
  <td width="13" class="rur">&nbsp;</td>
  </tr>
  </table><br>';
}
//Move Funktion der HFNs ins Archiv
if ($action == "arc") {
    $id = intval($_REQUEST['id']);

    $db_archiv = mysql_query("SELECT archiv FROM de_user_hyper WHERE empfaenger=$ums_user_id and archiv=1", $db);
    $num = mysql_num_rows($db_archiv);

    if ($ums_premium == 1) {
        $parchiv = $sv_hf_archiv_p;
    } else {
        $parchiv = $sv_hf_archiv;
    }

    if ($num <= ($parchiv - 1)) {
        $se = (int)$se;
        $sy = (int)$sy;
        //if(!preg_match("/^[0-9]*$/i", $t))$t='';

        mysql_query("UPDATE de_user_hyper SET archiv=1, absender='$ums_user_id', time=time WHERE fromsec='$se' AND fromsys='$sy' AND id='$id' AND empfaenger='$ums_user_id' AND sender=0", $db);

        echo insertmessage($hyperfunk_lang['msg_18'], "g", $hyperfunk_lang['systemnachricht']);
    } else {
        echo insertmessage($hyperfunk_lang['msg_19_1'].' '.$parchiv.' '.$hyperfunk_lang['msg_19_2'], "r", $hyperfunk_lang['systemnachricht']);
    }

    $action = "eingang";
}

//Anzeige der s&auml;mtlichen HFNS der jeweiligen Kategorien
if ($action == "eingang"  || $action == "" || $action == "ausgang" || $action == "archiv") {
    echo "<br><br><table width=\"586\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
    echo "<tr><td width=\"13\" height=\"37\" class=\"rol\">&nbsp;</td>";

    if ($action == "eingang"  || $action == "") {
        echo '<td class="ro" align="center"><div class="cellu">'.$hyperfunk_lang['eingang'].'</div></td>';
    } elseif ($action == "ausgang") {
        echo '<td class="ro" align="center"><div class="cellu">'.$hyperfunk_lang['ausgang'].'</div></td>';
    } elseif ($action == "archiv") {
        echo '<td class="ro" align="center"><div class="cellu">'.$hyperfunk_lang['archiv'].'</div></td>';
    }

    echo "<td width=\"13\" height=\"37\" class=\"ror\">&nbsp;</td></tr>
         <tr><td width=\"13\" class=\"rl\"></td><td>";

    if ($action == "eingang"  || $action == "") {
        if ($l == "new") {
            $db_tfn = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 and gelesen=0 ORDER BY time DESC", $db);
        } else {
            $db_tfn = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 ORDER BY time DESC", $db);
        }
        //nachrichten als gelesen markieren
        mysql_query("UPDATE de_user_hyper SET time=time, gelesen = 1 WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0", $db);
    } elseif ($action == "ausgang") {
        $db_tfn = mysql_query("SELECT * FROM de_user_hyper WHERE absender='$ums_user_id' and sender='1' ORDER BY time DESC", $db);
        //nachrichten als gelesen markieren
        mysql_query("UPDATE de_user_hyper SET time=time, gelesen = 1 WHERE absender='$ums_user_id' AND sender=1", $db);
    } elseif ($action == "archiv") {
        $db_tfn = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and archiv='1' ORDER BY time DESC", $db);
    }

    $anzahl = mysql_num_rows($db_tfn);


    while ($row = mysql_fetch_array($db_tfn)) {

        $row['text'] = str_replace(":)", "<img src=\"" . $ums_gpfad . "g/smilies/sm1.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":D", "<img src=\"" . $ums_gpfad . "g/smilies/sm2.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(";)", "<img src=\"" . $ums_gpfad . "g/smilies/sm3.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":x", "<img src=\"" . $ums_gpfad . "g/smilies/sm4.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":(", "<img src=\"" . $ums_gpfad . "g/smilies/sm5.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace("x(", "<img src=\"" . $ums_gpfad . "g/smilies/sm6.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":p", "<img src=\"" . $ums_gpfad . "g/smilies/sm7.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace("(?)", "<img src=\"" . $ums_gpfad . "g/smilies/sm8.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace("(!)", "<img src=\"" . $ums_gpfad . "g/smilies/sm9.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":{", "<img src=\"" . $ums_gpfad . "g/smilies/sm10.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":}", "<img src=\"" . $ums_gpfad . "g/smilies/sm11.gif\" alt=\"Smilie\">", $row['text']);
        $row['text'] = str_replace(":L", "<img src=\"" . $ums_gpfad . "g/smilies/sm12.gif\" alt=\"Smilie\">", $row['text']);

        $row['text'] = str_replace(":nene:", "<img src=\"" . $ums_gpfad . "g/smilies/sm13.gif\" alt=\"nene\">", $row['text']);
        $row['text'] = str_replace(":eek:", "<img src=\"" . $ums_gpfad . "g/smilies/sm14.gif\" alt=\"eek\">", $row['text']);
        $row['text'] = str_replace(":applaus:", "<img src=\"" . $ums_gpfad . "g/smilies/sm15.gif\" alt=\"applaus\">", $row['text']);
        $row['text'] = str_replace(":cry:", "<img src=\"" . $ums_gpfad . "g/smilies/sm16.gif\" alt=\"cry\">", $row['text']);
        $row['text'] = str_replace(":sleep:", "<img src=\"" . $ums_gpfad . "g/smilies/sm17.gif\" alt=\"sleep\">", $row['text']);
        $row['text'] = str_replace(":rolleyes:", "<img src=\"" . $ums_gpfad . "g/smilies/sm18.gif\" alt=\"Rolleyes\">", $row['text']);
        $row['text'] = str_replace(":wand:", "<img src=\"" . $ums_gpfad . "g/smilies/sm19.gif\" alt=\"Wand\">", $row['text']);
        $row['text'] = str_replace(":dead:", "<img src=\"" . $ums_gpfad . "g/smilies/sm20.gif\" alt=\"Dead\">", $row['text']);


        $row['text'] = preg_replace("/\[b\]/i", "<b>", $row['text']);
        $row['text'] = preg_replace("/\[\/b\]/i", "</b>", $row['text']);

        $row['text'] = preg_replace("/\[i\]/i", "<i>", $row['text']);
        $row['text'] = preg_replace("/\[\/i]/i", "</i>", $row['text']);

        $row['text'] = preg_replace("/\[u]/i", "<u>", $row['text']);
        $row['text'] = preg_replace("/\[\/u]/i", "</u>", $row['text']);

        $row['text'] = preg_replace("/\[center\]/i", "<center>", $row['text']);
        $row['text'] = preg_replace("/\[\/center\]/i", "</center>", $row['text']);

        #$row['text'] = preg_replace("/\[pre]/i", "<pre>",$row['text']);
        #$row['text'] = preg_replace("/\[\/pre]/i", "</pre>",$row['text']);


        $row['text'] = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $row['text']);
        $row['text'] = str_replace("[CROT]", "<font color=\"#F10505\">", $row['text']);
        $row['text'] = str_replace("[CW]", "<font color=\"#FFFFFF\">", $row['text']);
        $row['text'] = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $row['text']);
        $row['text'] = str_replace("[CDE]", "<font color=\"#3399FF\">", $row['text']);

        $row['text'] = preg_replace("/\[email\]([^[]*)\[\/email\]/", "<a href=\"mailto:\\1\">\\1</a>", $row['text']);
        $row['text'] = preg_replace("/\[url\]([^[]*)\[\/url\]/i", '<a href="\\1" target="_blank">\\1</a>', $row['text']);
        $row['text'] = preg_replace("/\[color=#([^[]+)\]([^[]*)\[\/color\]/", "<font color=\"#\\1\" >\\2</font>", $row['text']);
        $row['text'] = preg_replace("/\[size=([^[]+)\]([^[]*)\[\/size\]/", "<font size=\"\\1\" >\\2</font>", $row['text']);

        $row['text'] = nl2br($row['text']);

        $t = $row['time'];
        $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];

        if ($l != "new") {
            if ($row['gelesen'] == "0") {
                $neuemsg = '<img src="' . $ums_gpfad . 'g/hfn/DENeu.gif" alt="'.$hyperfunk_lang['logo_neue_hfn'].'">';
            } else {
                $neuemsg = "&nbsp;";
            }

        }
        ?>

    <table width="566" border="0" cellspacing="1" cellpadding="0">
          <tr><td width="80" class="cell1" style="text-align: left;">&nbsp;<?php if ($action == "ausgang") {
              echo $hyperfunk_lang['empfaenger'];
          } else {
              echo $hyperfunk_lang['absender'];
          }?></td><td><table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td class="cell" style="text-align: left;"><font face="Tahoma" size="2">
          <?php
          if ($action == "ausgang") {
              $empfa = mysql_query("SELECT sector, `system`, spielername FROM de_user_data WHERE user_id=$row[empfaenger]");
              $rowemp = mysql_fetch_array($empfa);

              $empfaenger = $rowemp['spielername'];
              $empsek = $rowemp['sector'];
              $empsys = $rowemp['system'];

              echo '&nbsp;'.$rowemp['sector'].':'.$rowemp['system'].' ('.$rowemp['spielername'].')';
              $row['fromsec'] = $rowemp['sector'];
              $row['fromsys'] = $rowemp['system'];
          } elseif ($row['fromsec'] == "0"  and $row['fromsys'] == "0") {
              $row['fromnic'] = str_replace("Leader", " ", $row['fromnic']);
              $row['fromnic'] = trim($row['fromnic']);
              echo '<a href="ally_message_leader.php?select='.$row['fromnic'].'">'.$row['fromnic'].' '.$hyperfunk_lang['leader'].'</a>';
          } else {
              echo '&nbsp;'.$row['fromsec'].':'.$row['fromsys'].' ('.$row['fromnic'].')';

          }
        ?></font></td><td align="right" class="cell" valign="middle"><?php echo "$neuemsg";?></td></tr></table></td>
          </tr><tr><td class="cell" style="text-align: left;">&nbsp;<?php echo $hyperfunk_lang['datum']?></td><td class="cell1" style="text-align: left;">&nbsp;<?php echo $time;?></td> </tr>
          <tr><td class="cell1" style="text-align: left;">&nbsp;<?php echo $hyperfunk_lang['betreff']?></td><td class="cell" style="text-align: left;">&nbsp;<?php echo utf8_encode(umlaut($row['betreff']));?></td></tr>
          <tr><td valign="top" class="cell" style="text-align: left;">&nbsp;<?php echo $hyperfunk_lang['nachricht']?></td><td class="cell1" style="text-align: left;">&nbsp;<?php echo utf8_encode(umlaut($row['text']));?></td></tr>
          <tr><td class="cell1">&nbsp;</td><td class="cell">&nbsp;
          <?php
        if ($action == "ausgang" and ($row['fromsec'] != "0" and $row['fromsys'] != "0")) {
            echo '<a href="details.php?se='.$row['fromsec'].'&sy='.$row['fromsys'].'">'.$hyperfunk_lang['hfn_nav_1'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if ($row['fromsec'] == "0" and $row['fromsys'] == "0") {
            echo '<a href="ally_message_leader.php?select='.$row['fromnic'].'">'.$row['fromnic'].'-'.$hyperfunk_lang['hfn_nav_2'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if ($action == "" or $action == "archiv" or $action == "eingang" and ($row['fromsec'] != "0" and $row['fromsys'] != "0")) {
            echo '<a href=hyperfunk.php?action=ant&se='.$row['fromsec'].'&sy='.$row['fromsys'].'&id='.$row['id'].'>'.$hyperfunk_lang['hfn_nav_3'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if ($action == "" or $action == "archiv" or $action == "ausgang" or $action == "eingang" and ($row['fromsec'] != "0"  and $row['fromsys'] != "0")) {
            echo '<a href=hyperfunk.php?action=weiter&se='.$row['fromsec'].'&sy='.$row['fromsys'].'&id='.$row['id'].'>'.$hyperfunk_lang['hfn_nav_4'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if ($action == "" or $action == "eingang") {
            echo '<a href=hyperfunk.php?action=arc&se='.$row['fromsec'].'&sy='.$row['fromsys'].'&id='.$row['id'].'>'.$hyperfunk_lang['hfn_nav_5'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if ($action == "archiv" or $action == "" or $action == "eingang" or $action == "ausgang") {
            echo '<a href=hyperfunk.php?action=del&se='.$row['fromsec'].'&sy='.$row['fromsys'].'&id='.$row['id'].'&o=';
            if ($action == "archiv") {
                echo "v";
            }
            if ($action == "" or $action == "eingang") {
                echo "e";
            }
            if ($action == "ausgang") {
                echo "a";
            }
            echo ' onclick="return confirm(\''.$hyperfunk_lang['hfn_nav_6'].'\')\">'.$hyperfunk_lang['loeschen'].'</a>';
        }
        ?>
         </td></tr>
    </table>
    <br>

    <?php
    }

    echo '<table border="0" cellpadding="0" cellspacing="1" width="566" bgcolor="#000000">';
    echo '<tr>';

    if ($anzahl != "0") {
        echo "<td class=\"c\" width=\"50%\"><a href=\"hyperfunk.php?action=da&l=";
        if ($action == "eingang" or $action == "") {
            echo "e";
        }
        if ($action == "ausgang") {
            echo "a";
        }
        if ($action == "archiv") {
            echo "r";
        }
        echo '" onclick="return confirm(unescape(\''.$hyperfunk_lang['hfn_nav_7'].'\'))"><font color="red">'.$hyperfunk_lang['alle_loeschen'].'</font></a></td>';
    } else {
        echo '<td class="c" width="50%" height="35">'.$hyperfunk_lang['nohfn'].'</td>';
    }
    echo '</tr>';
    echo '</table>';

    echo "</td><td width=\"13\" class=\"rr\"></td> </tr>
    <tr>
    <td width=\"13\" class=\"rul\">&nbsp;</td>
    <td class=\"ru\">&nbsp;</td>
    <td width=\"13\" class=\"rur\">&nbsp;</td>
    </tr>
    </table>";
}




// Formular f&uuml;r die saemtlichen Nachrichten
if ($action == "ant" or $action == "weiter" or $action == "spieler" or $action == "sektor" or $action == "alli" or $action == "freunde") {
    $check = 1;
    $allicheck = 1;
    $id = intval($_REQUEST['id'] ?? -1);

    if ($action == "freunde") {
        $db_friends = mysql_query("SELECT sector, `system`, name FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and status=1", $db);
        $num = mysql_num_rows($db_friends);
        if ($num == "0") {
            $check = 0;
        }
    }

    if ($action == "alli") {
        $db_alli = mysql_query("SELECT status FROM de_user_data WHERE user_id=$ums_user_id", $db);
        $rowalli = mysql_fetch_array($db_alli);
        if ($rowalli['status'] == "0") {
            $allicheck = 0;
        }
    }

    if ($allicheck == "1") {
        if ($check == "1") {
            if ($action == "ant" or $action == "weiter") {
                $anttfn = mysql_query("SELECT * FROM de_user_hyper WHERE (id='$id' AND empfaenger=$ums_user_id) or (id='$id' AND absender=$ums_user_id AND sender=1)", $db);
                $rowtfn = mysql_fetch_array($anttfn);

                $rowtfn['text'] = str_replace("<br />", " ", $rowtfn['text']);
            }
            ?>
              <br><br>
              <form action="hyperfunk.php" method="post">
              <table border="0" width="586" cellspacing="0" cellpadding="0">
              <tr>
              <td width="13" height="37" class="rol">&nbsp;</td>
              <td class="ro" align="center" colspan="2" nowrap><div class="cellu"><?php if ($action == "sektor") {
                  echo $hyperfunk_lang['sektornachricht'];
              } elseif ($action == "freunde") {
                  echo $hyperfunk_lang['freundenachricht'];
              } elseif ($action == "alli") {
                  echo $hyperfunk_lang['allianznachricht'];
              } else {
                  echo $hyperfunk_lang['hyperfunknachricht'];
              } if ($action == "ant") {
                  echo $hyperfunk_lang['beantworten'];
              } if ($action == "weiter") {
                  echo $hyperfunk_lang['hfn_nav_4'];
              } if ($action == "spieler" or $action == "sektor" or $action == "alli") {
                  echo $hyperfunk_lang['verfassen'];
              }?></div></td>
              <td width="13" height="37" class="ror">&nbsp;</td>
              </tr>



              <?php if ($action != "sektor" and $action != "alli") {
                  ?>
              <tr class="cellu">
              <td width='13' height='37' class='rl'>&nbsp;</td>
              <td width=100><?php echo $hyperfunk_lang['zielkoordinaten']; ?>:</td>
              <td>
              <?php
              if ($action == "freunde") {
                  $f_counter = 1;
                  while ($row = mysql_fetch_array($db_friends)) {
                      echo '<input type="Checkbox" name="freund'.$f_counter.'" value="'.$row['sector'].':'.$row['system'].'">&nbsp;'.$row['sector'].':'.$row['system'].'&nbsp;&nbsp;('.$row['name'].')<br>';
                      $f_counter++;
                  }
              } else {
                  ?>
              <input name="zielsek" id="zielsek" tabindex="1" size="4" style="border-style:solid;height:21;" <?php if ($action == "ant") {
                  echo "value=\"$se\"";
              }?>><input name="zielsys"  tabindex="2" id="zielsys" size="4" style="border-style:solid;height:21;" <?php if ($action == "ant") {
                  echo "value=\"$sy\"";
              }?>>
              <?php
              }
                  ?>
              </td>
              <td width='13' height='37' class='rr'>&nbsp;</td>
              </tr>
              <?php
              }
            ?>
              <tr class="cellu">
              <td width='13' height='37' class='rl'>&nbsp;</td>
              <td width=100><?php echo $hyperfunk_lang['betreff']?>: </td>
              <td><input name=betreff size=30 tabindex="3" style="border-style:solid;height:21;" <?php

              if ($action == "ant") {
                  echo 'value="'.$hyperfunk_lang['re'].' '.umlaut($rowtfn['betreff']).'">';
              } elseif ($action == "weiter") {
                  echo 'value="'.$hyperfunk_lang['fw'].' '.umlaut($rowtfn['betreff']).'">';
              }

            ?>
              </td>
              <td width='13' height='37' class='rr'>&nbsp;</td>
              </tr>

              <tr class="cellu">
              <td width='13' height='37' class='rl'>&nbsp;</td>
              <td colspan=2 align=center height=50>
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm1.gif" onclick="init('smile1')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm2.gif" onclick="init('smile2')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm3.gif" onclick="init('smile3')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm4.gif" onclick="init('smile4')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm5.gif" onclick="init('smile5')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm6.gif" onclick="init('smile6')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm7.gif" onclick="init('smile7')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm8.gif" onclick="init('smile8')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm9.gif" onclick="init('smile9')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm10.gif" onclick="init('smile10')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm11.gif" onclick="init('smile11')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm12.gif" onclick="init('smile12')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm13.gif" onclick="init('smile13')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm14.gif" onclick="init('smile14')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm15.gif" onclick="init('smile15')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm16.gif" onclick="init('smile16')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm17.gif" onclick="init('smile17')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm18.gif" onclick="init('smile18')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm19.gif" onclick="init('smile19')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <img src="<?php echo $ums_gpfad; ?>g/smilies/sm20.gif" onclick="init('smile20')" alt="<?php echo $hyperfunk_lang['altsmilie']?>">
              <br>



              <input type="button" value="&nbsp;b&nbsp;"  onclick="init('fett')">
              <input type="button" value="&nbsp;u&nbsp;"  onclick="init('under')">
              <input type="button" value="&nbsp;i&nbsp;"  onclick="init('kursiv')">
              <input type="button" value="<?php echo $hyperfunk_lang['rot']?>"  onclick="init('rot')">
              <input type="button" value="<?php echo $hyperfunk_lang['gelb']?>"  onclick="init('gelb')">
              <input type="button" value="<?php echo $hyperfunk_lang['gruen']?> "  onclick="init('gruen')">
              <input type="button" value="<?php echo $hyperfunk_lang['weiss']?>"  onclick="init('weiss')">
              <input type="button" value="<?php echo $hyperfunk_lang['Farbe']?> "  onclick="init('farbe')">
              
              <input type="button" value="<?php echo $hyperfunk_lang['groesse']?>"  onclick="init('size')">
              <input type="button" value="center"  onclick="init('center')">
              <!--<input type="button" value="pre"  onclick="init('pre')">-->
              <input type="button" value="Link"  onclick="init('www')">
              <input type="button" value="@"  onclick="init('mail')">
              <input type="button" value="&nbsp;?&nbsp;"  onclick="hilfe()">
              <!--<input type="button" value="<?php echo $hyperfunk_lang['leeren']?>"  onclick="leeren()">-->
              </td>
              <td width='13' height='37' class='rr'>&nbsp;</td>
              </tr>

              <tr>
              <td width='13' height='37' class='rl'>&nbsp;</td>
              <td colspan=2 align="center"><textarea rows="15" cols="64" tabindex="4" name="nachricht" id="nachricht"><?php if ($action == "ant" or $action == "weiter") {
                  echo '[i][b]'.$rowtfn['fromnic'].' '.$hyperfunk_lang['schrieb'].': [/b]'.umlaut($rowtfn['text']).'[/i]';
              }?></textarea></td>
              <td width='13' height='37' class='rr'>&nbsp;</td>
              </tr>

              <tr>
                  <td width='13' height='37' class='rl'>&nbsp;</td>
                  <td colspan=2 align=center><input type="button"  value="<?php echo $hyperfunk_lang['laengepruefen']?>" onClick="check()"> <input type=submit tabindex="5" onclick="return zeichenundsmiliecheck()"  name=<?php if ($action == "sektor") {
                      echo "sekmsg";
                  } elseif ($action == "alli") {
                      echo "allimsg";
                  } elseif ($action == "freunde") {
                      echo "freundemsg";
                  } else {
                      echo "antbut";
                  }?> value="<?php if ($action == "sektor") {
                      echo $hyperfunk_lang['sektornachricht'];
                  } elseif ($action == "alli") {
                      echo $hyperfunk_lang['allianznachricht'];
                  } elseif ($action == "freunde") {
                      echo $hyperfunk_lang['freundenachricht'];
                  } else {
                      echo $hyperfunk_lang['hyperfunknachricht'];
                  }?> <?php print($hyperfunk_lang['msg_31']) ?>" ></td>
                  <td width='13' height='37' class='rr'>&nbsp;</td>
              </tr>

                  <tr>
                  <td width='13' class='rul'>&nbsp;</td>
                  <td class='ru' colspan=2>&nbsp;</td>
                  <td width='13' class='rur'>&nbsp;</td>
                  </tr>
              </table>
              </form>
<?php

// if($action!="sektor" and $action!="alli" and $action!="freunde" ) echo " onclick=\"return checkobleer()\"";

        } else {
            echo insertmessage($hyperfunk_lang['msg_20'], "r", $hyperfunk_lang['systemnachricht']);
        }
    } else {
        echo insertmessage($hyperfunk_lang['msg_21'], "r", $hyperfunk_lang['systemnachricht']);
    }
}
//Insert f&uuml;r die Buddyliste
if(isset($_POST['friendbtn'])) {

    $sector = intval($_REQUEST['freundsector'] ?? -1);
    $system = intval($_REQUEST['freundsystem'] ?? -1);

    $db_check = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND `system`='$system'", $db);
    $numcheck = mysql_num_rows($db_check);

    $db_buddy_exist_check = mysql_query("SELECT user_id FROM de_hfn_buddy_ignore WHERE sector='$sector' AND `system`='$system' and status=1 and user_id='$ums_user_id'", $db);
    $buddy_exist_check = mysql_num_rows($db_buddy_exist_check);

    if ($buddy_exist_check >= 1) {
        echo insertmessage($hyperfunk_lang['msg_22'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($numcheck == 1) {
        $db_buddy = mysql_query("SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and status=1", $db);
        $num = mysql_num_rows($db_buddy);

        if ($ums_premium == 1) {
            $pbuddies = $sv_hf_buddie_p;
        } else {
            $pbuddies = $sv_hf_buddie;
        }

        if ($num <= ($pbuddies - 1)) {
            $buddyname = mysql_query("SELECT spielername FROM de_user_data WHERE sector='$sector' AND `system`='$system'");
            $rowbuddy = mysql_fetch_array($buddyname);

            mysql_query("INSERT INTO de_hfn_buddy_ignore (user_id, sector, `system`, name, status) VALUES ('$ums_user_id', '$sector', '$system','$rowbuddy[spielername]',1)", $db);

            echo insertmessage($hyperfunk_lang['msg_23'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_24_1'].' '.$pbuddies.' '.$hyperfunk_lang['msg_24_2'], "r", $hyperfunk_lang['systemnachricht']);
        }
    } else {
        echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
    }

    $action = "optionen";
}
//Insert f&uuml;r die Ignoreliste
if (isset($_POST['ignorebtn'])) {
    $sector = intval($_REQUEST['feindsector'] ?? -1);
    $system = intval($_REQUEST['feindsystem'] ?? -1);

    $db_check = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND `system`='$system'", $db);
    $numcheck = mysql_num_rows($db_check);

    $db_buddy_exist_check = mysql_query("SELECT user_id FROM de_hfn_buddy_ignore WHERE sector='$sector' AND `system`='$system' and status=2 and user_id='$ums_user_id'", $db);
    $buddy_exist_check = mysql_num_rows($db_buddy_exist_check);

    if ($buddy_exist_check >= 1) {
        echo insertmessage($hyperfunk_lang['msg_25'], "r", $hyperfunk_lang['systemnachricht']);
    } elseif ($numcheck == 1) {


        $db_enem = mysql_query("SELECT sector, `system` FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and status=2", $db);
        $num = mysql_num_rows($db_enem);


        if ($ums_premium == 1) {
            $pigno = $sv_hf_ignore_p;
        } else {
            $pigno = $sv_hf_ignore;
        }

        if ($num <= ($pigno - 1)) {
            $ignorename = mysql_query("SELECT spielername FROM de_user_data WHERE sector='$sector' AND `system`='$system'");
            $rowignore = mysql_fetch_array($ignorename);

            mysql_query("INSERT INTO de_hfn_buddy_ignore (user_id, sector, `system`, name, status) VALUES ('$ums_user_id', '$sector', '$system','$rowignore[spielername]',2)", $db);

            echo insertmessage($hyperfunk_lang['msg_26'], "g", $hyperfunk_lang['systemnachricht']);
        } else {
            echo insertmessage($hyperfunk_lang['msg_27_1'].' '.$pigno.' '.$hyperfunk_lang['msg_27_2'], "r", $hyperfunk_lang['systemnachricht']);
        }

        $action = "optionen";
    } else {
        echo insertmessage($hyperfunk_lang['msg_3'], "r", $hyperfunk_lang['systemnachricht']);
    }
}
// Loeschen einzelner Personen aus der Buddyliste
if ($action == "delbuddy") {
    $sector = (int)$se;
    $system = (int)$sy;

    mysql_query("DELETE FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and sector=$sector and `system`=$system and status=1", $db);

    echo insertmessage($hyperfunk_lang['msg_28'], "r", $hyperfunk_lang['systemnachricht']);

    $action = "optionen";
}
// Loeschen einzelner Personen aus der Ignorelist
if ($action == "delene") {
    $sector = (int)$se;
    $system = (int)$sy;

    mysql_query("DELETE FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and sector=$sector and `system`=$system and status=2", $db);

    echo insertmessage($hyperfunk_lang['msg_28'], "r", $hyperfunk_lang['systemnachricht']);

    $action = "optionen";
}
// Part zum Versenden der HFNs per Mails
if(isset($_POST['mailhfn'])){
    
  include_once "cache/overview.inc.php";

    $allenachrichten = $hyperfunk_lang['mail_1'].' '.$ums_spielername.''.$hyperfunk_lang['mail_2'].' '.$sv_server_name.''.$hyperfunk_lang['mail_3'];


    $db_eingang = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 ORDER BY time DESC", $db);
    $numein = mysql_num_rows($db_eingang);
    if ($numein == "0") {
        $allenachrichten = $allenachrichten.' '.$hyperfunk_lang['mail_4'];
    } else {
        while ($row = mysql_fetch_array($db_eingang)) {
            $t = $row['time'];
            $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
            $allenachrichten = $allenachrichten.''.$hyperfunk_lang['absender'].': '.$row['fromnic'].'('.$row['fromsec'].':'.$row['fromsys'].')\n'.$hyperfunk_lang['uhrzeit'].': '.$time.'\n'.$hyperfunk_lang['betreff'].': '.$row['betreff'].'\n'.$row['text'].'\n\n\n';
        }
    }

    $allenachrichten = $allenachrichten.' '.$hyperfunk_lang['mail_5'];

    $db_ausgang = mysql_query("SELECT * FROM de_user_hyper WHERE absender='$ums_user_id' and sender=1 ORDER BY time DESC", $db);
    $numaus = mysql_num_rows($db_ausgang);
    if ($numaus == "0") {
        $allenachrichten = $allenachrichten.' '.$hyperfunk_lang['mail_6'];
    } else {
        while ($row = mysql_fetch_array($db_ausgang)) {
            $t = $row['time'];
            $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
            $allenachrichten = $allenachrichten.''.$hyperfunk_lang['empfaenger'].': '.insertemp($row['empfaenger']).'\n'.$hyperfunk_lang['uhrzeit'].': '.$time.'\n'.$hyperfunk_lang['betreff'].': '.$row['betreff'].'\n'.$row['text'].'\n\n\n';
        }
    }

    $allenachrichten = $allenachrichten.' '.$hyperfunk_lang[mail_7];

    $db_archiv = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and archiv=1 ORDER BY time DESC", $db);
    $numar = mysql_num_rows($db_archiv);
    if ($numar == "0") {
        $allenachrichten = $allenachrichten.' '.$hyperfunk_lang['mail_8'];
    } else {
        while ($row = mysql_fetch_array($db_archiv)) {
            $t = $row['time'];
            $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
            $allenachrichten = $allenachrichten.''.$hyperfunk_lang['absender'].': '.$row['fromnic'].'('.$row['fromsec'].':'.$row['fromsys'].')\n'.$hyperfunk_lang['uhrzeit'].': '.$time.'\n'.$hyperfunk_lang['betreff'].': '.$row['betreff'].'\n'.$row['text'].'\n\n\n';
        }
    }
    $allenachrichten = $allenachrichten.' '.$hyperfunk_lang['mail_9'];

    $db_mail = mysql_query("SELECT reg_mail FROM de_login WHERE user_id='$ums_user_id'", $db);
    $rowmail = mysql_fetch_array($db_mail);
    $allenachrichten = str_replace("<br />", " ", $allenachrichten);

    $allenachrichten = str_replace("[b]", "<b>", $allenachrichten);
    $allenachrichten = str_replace("[/b]", "</b>", $allenachrichten);

    $allenachrichten = str_replace("[i]", "<i>", $allenachrichten);
    $allenachrichten = str_replace("[/i]", "</i>", $allenachrichten);

    $allenachrichten = str_replace("[u]", "<u>", $allenachrichten);
    $allenachrichten = str_replace("[/u]", "</u>", $allenachrichten);

    $allenachrichten = str_replace("[center]", "<center>", $allenachrichten);
    $allenachrichten = str_replace("[/center]", "</center>", $allenachrichten);

    $allenachrichten = str_replace("[pre]", "", $allenachrichten);
    $allenachrichten = str_replace("[/pre]", "", $allenachrichten);

    $allenachrichten = str_replace("[CGRUEN]", " ", $allenachrichten);
    $allenachrichten = str_replace("[CROT]", " ", $allenachrichten);
    $allenachrichten = str_replace("[CW]", " ", $allenachrichten);
    $allenachrichten = str_replace("[CGELB]", " ", $allenachrichten);
    $allenachrichten = str_replace('\n', "<br>", $allenachrichten);


    @mail_smtp($rowmail['reg_mail'], $hyperfunk_lang['mail_10'].' '.$sv_server_name, nl2br($allenachrichten));
    echo insertmessage($hyperfunk_lang['msg_29_1'].' '.$rowmail['reg_mail'].' '.$hyperfunk_lang['msg_29_2'], "g", $hyperfunk_lang['systemnachricht']);
    mysql_query("DELETE FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 and gelesen=1", $db);
    mysql_query("DELETE FROM de_user_hyper WHERE empfaenger='$ums_user_id' and archiv=1 ", $db);
    mysql_query("DELETE FROM de_user_hyper WHERE absender='$ums_user_id' and sender=1", $db);

    $action = "optionen";
}

//Optionen Menu
if ($action == "optionen") {
    ?>
<br><br>
<form action="hyperfunk.php?action=optionen" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="300">
<tr>
<td width="13" height="25" class="rol"></td>
<td align=center height="35" colspan="2" class="ro"><div class="cellu"><?php echo $hyperfunk_lang['mailservice']?></div></td>
<td width="13" height="25" class="ror"></td>
</tr>

<tr>
<td width="13" height="25" class="rl">&nbsp;</td>
<td align=center height="45" colspan="2" class="cell"><input type="submit" name="mailhfn" value="<?php echo $hyperfunk_lang['mailservicesend']?>"
<?php
    $db_eingang = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and sender=0 and archiv=0 and gelesen=1 ORDER BY time DESC", $db);
    $numein = mysql_num_rows($db_eingang);

    $db_ausgang = mysql_query("SELECT * FROM de_user_hyper WHERE absender='$ums_user_id' and sender=1 ORDER BY time DESC", $db);
    $numaus = mysql_num_rows($db_ausgang);

    $db_archiv = mysql_query("SELECT * FROM de_user_hyper WHERE empfaenger='$ums_user_id' and archiv=1 ORDER BY time DESC", $db);
    $numar = mysql_num_rows($db_archiv);

    if ($numein == "0" and $numaus == "0" and $numar == "0") {
        echo " disabled ";
    }
    ?>
></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
 <td width="13" class="rul">&nbsp;</td>
 <td colspan="2" class="ru">&nbsp;</td>
 <td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>
<br>
<form action=hyperfunk.php?action=optionen method=post>
<table border=0 cellspacing=0 cellpadding=0 width=300>
<tr>
<td width='13' height='25' class='rml'></td>
<td align=center height='35' colspan=2 class='ro'><div class="cellu"><?php echo $hyperfunk_lang['meine_freunde']?></div></td>
<td width='13' height='25' class='rmr'></td>
</tr>



    <?php
        $db_friends = mysql_query("SELECT sector, `system`, name FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and status=1", $db);

    $num = mysql_num_rows($db_friends);

    $counter = 1;

    if ($num == "0") {

        echo '<tr><td width="13" class="rl" height="25"></td><td align="center" colspan="2" class="cell"><div class="cellu">'.$hyperfunk_lang['keine_freunde'].'</div></td><td width="13" height="25" class="rr"></td></tr>';

    } else {
        while ($rowf = mysql_fetch_array($db_friends)) {
            if ($counter == 1) {
                echo '<tr><td width="13" class="rl" height="25"></td><td align="right" class="cell">'.$rowf['sector'].':'.$rowf['system'].'&nbsp;&nbsp;&nbsp;('.$rowf['name'].')&nbsp;&nbsp;&nbsp;</td><td class="cell">&nbsp;&nbsp;&nbsp;<a href="hyperfunk.php?se='.$rowf['sector'].'&sy='.$rowf['system'].'&action=delbuddy">'.$hyperfunk_lang['loeschen'].'</a></td><td width="13" height="25" class="rr"></td></tr>';
                $counter = 0;
            } else {
                echo '<tr><td width="13" class="rl" height="25"></td><td align="right" class="cell1">'.$rowf['sector'].':'.$rowf['system'].'&nbsp;&nbsp;&nbsp;('.$rowf['name'].')&nbsp;&nbsp;&nbsp;</td><td class="cell1">&nbsp;&nbsp;&nbsp;<a href="hyperfunk.php?se='.$rowf['sector'].'&sy='.$rowf['system'].'&action=delbuddy">'.$hyperfunk_lang['loeschen'].'</a></td><td width="13" height="25" class="rr"></td></tr>';
                $counter = 1;
            }
        }
    }

    ?>
<tr>
    <td width="13" class="rl" height="20"></td>
    <td class="cell" align="right" width="120" height="30"><div class="fett"><?php echo $hyperfunk_lang['koordinaten']?>&nbsp;&nbsp;&nbsp;</div></td><td class="cell">&nbsp;&nbsp;&nbsp;<input type="text" name="freundsector" size="3" style="border-style:solid;height:21;">&nbsp;:&nbsp;<input type="text" name="freundsystem" size="2" style="border-style:solid;height:21;"></td>
    <td width="13" height="20" class="rr"></td>
</tr>
<tr>
    <td width='13' class='rl' height='25'></td>
    <td colspan=2 align=center class=cell><input type="submit" name="friendbtn"  value="<?php echo $hyperfunk_lang['freund_adden']?>"></td>
    <td width='13' height='25' class='rr'></td>
</tr>
<tr>
 <td width="13" class="rul">&nbsp;</td>
 <td colspan="2" class="ru">&nbsp;</td>
 <td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>
<br>
<form action="hyperfunk.php?action=optionen" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="300">
<tr>
<td width="13" height="25" class="rml"></td>
<td align=center class="ro" colspan="2" height="35"><div class="cellu"><?php echo $hyperfunk_lang['ignorelist']?></div></td>
<td width="13" height="25" class="rmr"></td>
</tr>

    <?php
    $db_enemy = mysql_query("SELECT sector, `system`, name FROM de_hfn_buddy_ignore WHERE user_id=$ums_user_id and status=2", $db);

    $nume = mysql_num_rows($db_enemy);

    $counter = 1;

    if ($nume == "0") {
        echo '<tr><td width="13" class="rl" height="25"></td><td align="center" colspan="2" class="cell"><div class="fett">'.$hyperfunk_lang['keine_feinde'].'</div></td><td width="13" height="25" class="rr"></td></tr>';
    } else {
        while ($row = mysql_fetch_array($db_enemy)) {
            if ($counter == 1) {
                echo '<tr><td width="13" class="rl" height="25"></td><td align="right" class="cell">'.$row['sector'].':'.$row['system'].'&nbsp;&nbsp;&nbsp;('.$row['name'].')&nbsp;&nbsp;&nbsp;</td><td class="cell">&nbsp;&nbsp;&nbsp;<a href="hyperfunk.php?se='.$row['sector'].'&sy='.$row['system'].'&action=delene">'.$hyperfunk_lang['loeschen'].'</a></td><td width="13" height="25" class="rr"></td></tr>';
                $counter = 0;
            } else {
                echo '<tr><td width="13" class="rl" height="25"></td><td align="right" class="cell1">'.$row['sector'].':'.$row['system'].'&nbsp;&nbsp;&nbsp;('.$row['name'].')&nbsp;&nbsp;&nbsp;</td><td class="cell1">&nbsp;&nbsp;&nbsp;<a href="hyperfunk.php?se='.$row['sector'].'&sy='.$row['system'].'&action=delene">'.$hyperfunk_lang['loeschen'].'</a></td><td width="13" height="25" class="rr"></td></tr>';
                $counter = 1;
            }
        }
    }

    ?>
<tr>
    <td width="13" class="rl" height="20"></td>
    <td class="cell" align="right" width="120" height="30"><div class="fett"><?php echo $hyperfunk_lang['koordinaten']?>&nbsp;&nbsp;&nbsp;</div></td><td class="cell">&nbsp;&nbsp;&nbsp;<input type="text" name="feindsector" size="3" style="border-style:solid;height:21;">&nbsp;:&nbsp;<input type="text" name="feindsystem" size="2" style="border-style:solid;height:21;"></td>
    <td width="13" height="20" class="rr"></td>
</tr>
<tr>
    <td width="13" class="rl" height="25"></td>
    <td colspan="2" align="center" class="cell"><input type="submit" name="ignorebtn"  value="<?php echo $hyperfunk_lang['feind_adden']?>"></td>
    <td width="13" height="25" class="rr"></td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru" colspan="2">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>
<?php
}

//Check gegen Scriptkiddies

switch ($action) {
    case "delene":break;
    case "delbuddy":break;
    case "optionen":break;


    case "alli":break;
    case "freunde":break;
    case "arc":break;
    case "da":break;
    case "del":break;

    case "ant":break;
    case "weiter":break;
    case "ausgang":break;
    case "eingang":break;
    case "sektor":break;
    case "archiv":break;
    case "spieler":break;
    case "":break;

    default:
        @$time = strftime("%Y%m%d%H%M%S");
        @$param = "Der folgende Spieler $ums_spielername ($asec:$asys)[UserID:$ums_user_id] hat am $zeit an den Parametern rumgespielt. \n\n\n Der Parameter lautet: \n $action";
        @mail_smpt($GLOBALS['env_admin_email'], "Scriptkiddi auf Server $sv_server_name am Werk.", $param);
        break;
}

switch ($l) {
    case "e":break;
    case "a":break;
    case "r":break;
    case "new":break;
    case "":break;

    default:
        @$time = strftime("%Y%m%d%H%M%S");
        @$para = "Der folgende Spieler $ums_spielername ($asec:$asys)[UserID:$ums_user_id] hat am $zeit an den Parametern rumgespielt. \n\n\n Der Parameter lautet: \n $l";
        @mail_smtp($GLOBALS['env_admin_email'], "Scriptkiddi auf Server $sv_server_name am Werk.", $para);
        break;
}
?>
<br><br>
</center>
<?php include('fooban.php'); ?>
</body>
</html>