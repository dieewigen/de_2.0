<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/' . $sv_server_lang . '_details.lang.php';
include 'functions.php';

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$allytag = $row["allytag"];
$sector = $row["sector"];
$system = $row["system"];

if ($row['status'] != 1) {
    $allytag = '';
}

//***************************************
$se = intval($_REQUEST['se'] ?? 0);
$sy = intval($_REQUEST['sy'] ?? 0);

if (!isset($zuser_id)) {
    $zuser_id = 0;
}
if (!isset($zowner_id)) {
    $zowner_id = 0;
}

//wenn ein spielername übergeben wird, dann anhand dessen die koordinaten und die user_id ermitteln
if (isset($_REQUEST['sn'])) {

    $sn = $_REQUEST['sn'];
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, sector, `system` FROM de_user_data WHERE spielername=?",
        [$sn]
    );
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) {
        $row = mysqli_fetch_assoc($db_daten);
        $se = $row['sector'];
        $sy = $row['system'];
        $zuser_id = $row['user_id'];
    }
    if ($zuser_id > 0) {
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT owner_id FROM de_login WHERE user_id=?",
            [$zuser_id]
        );
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $row = mysqli_fetch_assoc($db_daten);
            $zowner_id = $row['owner_id'];
        }
    }
}

//Analysieren der Koordinaten, um userid vom ZIEL herauszubekommen
$db_da = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT user_id, allytag, sector, spielername, status, npc FROM de_user_data WHERE sector=? AND `system`=?",
    [$se, $sy]
);
$rew = mysqli_fetch_assoc($db_da);
if ($rew['user_id'] > 0) {
    $zuser_id = $rew['user_id'];
    $znpc = $rew['npc'];
}

//ggf. noch die owner_id auslesen
if ($zuser_id > 0 && $zowner_id < 1) {
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT owner_id FROM de_login WHERE user_id=?",
        [$zuser_id]
    );
    $num = mysqli_num_rows($db_daten);
    if ($num == 1) {
        $row = mysqli_fetch_assoc($db_daten);
        $zowner_id = $row['owner_id'];
    }
}

?>
<!doctype html>
<html>

<head>
    <title><?php echo $details_lang['title']; ?></title>
    <?php include "cssinclude.php"; ?>
</head>

<?php
echo '<body class="theme-rasse' . $_SESSION['ums_rasse'] . ' ' . (($_SESSION['ums_mobi'] == 1) ? 'mobile' : 'desktop') . '">';

include "resline.php";

//wenn es der eigene Sektor und ein NPC Typ 2 ist, dann NPC-Details aller NPC im Sektor anzeigen
if ($se == $sector && $znpc == 2) {

    //alle NPC im Sektor auslesen
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_user_data WHERE sector=? AND npc=2 ORDER BY `system`",
        [$se]
    );

    $npcHTML='';

    while ($row = mysqli_fetch_assoc($db_daten)) {
        $npcHTML .= '
        <div class="npc-detail-card">
            <div class="header">
                ' . htmlspecialchars($row['spielername'], ENT_QUOTES, 'UTF-8') . ' [' . $row['sector'] . ':' . $row['system'] . ']
            </div>

            <div class="detail-row">
                <img src="gp/g/icon8.png" class="rounded-borders" style="width: 20px; height: auto;" title="Punkte"> ' . number_format($row['score'], 0, ',', '.') . '
            </div>
            <div class="detail-row">
                <img src="gp/g/icon18.png" class="rounded-borders" style="width: 20px; height: auto;" title="Kollektoren"> ' . number_format($row['col'], 0, ',', '.') . '
            </div>
            <div class="detail-row">
                <img src="gp/g/icon17.png" class="rounded-borders" style="width: 20px; height: auto;" title="Kriegsartefakte"> ' . number_format($row['kartefakt'], 0, ',', '.') . '
            </div>


        </div>';
    }

    echo '
    <div class="npc">
        <div class="npc-details">
            '.$npcHTML.'
        </div>
    </div>
    ';


    die('</body></html>');
}elseif ($znpc == 2) {
    //NPC vom anderen Sektor, keine Details anzeigen

    die('
            <div class="info_box font-size16">
                Es stehen keine Informationen zur Verfügung.
            </div>
        </body>
    </html>
    ');
}

//HF nur anzeigen, wenn es Spieler vom eigenen Server ist
if (!isset($_REQUEST['ctyp']) && !isset($_REQUEST['cid']) && $se > 0) {
?>
    <script>
        function zeichenundsmiliecheck() {

            var nachricht = document.getElementById("nachricht").value;
            var zeichen = document.getElementById("nachricht").value.length;

            if (document.getElementById("nachricht").value.length > 10000) alert("<?php echo $details_lang['err_zu_viele_zeichen'] ?>");
            if (document.getElementById("nachricht").value.length <= 10000) {
                var temp = 0;
            }
            if (temp == 0) {
                if (document.getElementById("zielsek").value == "" || document.getElementById("zielsys").value == "") {
                    alert("<?php echo $details_lang['err_fehlerhaftekoords'] ?>");
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }

        function check() {
            var nachricht = document.getElementById("nachricht").value;
            var zeichen = document.getElementById("nachricht").value.length;

            if (document.getElementById("nachricht").value.length >= 10000) {
                alert("<?php echo $details_lang['msg_zeichensmilie'] ?>");
            } else {
                alert("<?php echo $details_lang['msg_summezeichensmilie1'] ?> " + document.getElementById("nachricht").value.length + " <?php echo $details_lang['msg_summezeichensmilie2'] ?> " + (10000 - document.getElementById("nachricht").value.length) + " <?php echo $details_lang['msg_summezeichensmilie3'] ?> " + " <?php $details_lang['msg_summezeichensmilie4'] ?> " + " <?php echo $details_lang['msg_summezeichensmilie5'] ?>");
            }
        }

        function leeren() {
            (document.getElementById("nachricht").value) = "";
            document.getElementById("nachricht").focus();
        }

        function hilfe() {
            window.open("hfnlegende.php", "BitteBeachten", "width=572,height=314,left=34,top=75");
        }

        function cursor() {
            if ((navigator.appName == "Netscape") || (navigator.userAgent.indexOf("Opera") != -1) || (navigator.userAgent.indexOf("Netscape") != -1)) {
                text_before = document.getElementById("nachricht").value;
                text_after = "";
            } else {
                document.getElementById("nachricht").focus();
                var sel = document.selection.createRange();
                sel.collapse();
                var sel_before = sel.duplicate();
                var sel_after = sel.duplicate();
                sel.moveToElementText(document.getElementById("nachricht"));
                sel_before.setEndPoint("StartToStart", sel);
                sel_after.setEndPoint("EndToEnd", sel);
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
            with(document.getElementById("nachricht").value) {
                switch (thisCode) {

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
    </script>
    <br>
    <form action="hyperfunk.php" method="post">
        <table border="0" width="586" cellspacing="0" cellpadding="0">
            <tr>
                <td width="13" height="37" class="rol">&nbsp;</td>
                <td class="ro" align="center" colspan="2"><?php echo $details_lang['hfnverfassen'] ?></td>
                <td width="13" height="37" class="ror">&nbsp;</td>
            </tr>
            <tr class="cell">
                <td width="13" height="37" class="rl">&nbsp;</td>
                <td width="110px"><?php echo $details_lang['zielkoordinaten'] ?>:</td>
                <td>
                    <input name="zielsek" tabindex="1" id="zielsek" size="4" style="border-style:solid;height:21;" value="<?php echo $se; ?>"> <input name="zielsys" tabindex="2" id="zielsys" size="4" style="border-style:solid;height:21;" value="<?php echo $sy; ?>">
                </td>
                <td width="13" height="37" class="rr">&nbsp;</td>
            </tr>
            <tr class="cell">
                <td width="13" height="37" class="rl">&nbsp;</td>
                <td width="110px"><?php echo $details_lang['betreff'] ?>: </td>
                <td><input name="betreff" tabindex="3" size="30" style="border-style:solid;height:21;">
                </td>
                <td width="13" height="37" class="rr">&nbsp;</td>
            </tr>

            <tr class="cell">
                <td width="13" height="37" class="rl">&nbsp;</td>
                <td colspan=2 align=center height="30px">

                    <input type="button" value="&nbsp;b&nbsp;" onclick="init('fett')">
                    <input type="button" value="&nbsp;u&nbsp;" onclick="init('under')">
                    <input type="button" value="&nbsp;i&nbsp;" onclick="init('kursiv')">
                    <input type="button" value="<?php echo $details_lang['rot'] ?>" onclick="init('rot')">
                    <input type="button" value="<?php echo $details_lang['gelb'] ?>" onclick="init('gelb')">
                    <input type="button" value="<?php echo $details_lang['gruen'] ?>" onclick="init('gruen')">
                    <input type="button" value="<?php echo $details_lang['weiss'] ?>" onclick="init('weiss')">
                    <input type="button" value="<?php echo $details_lang['farbe'] ?>" onclick="init('farbe')">

                    <input type="button" value="<?php echo $details_lang['groesse'] ?>" onclick="init('size')">
                    <input type="button" value="center" onclick="init('center')">
                    <input type="button" value="pre" onclick="init('pre')">
                    <input type="button" value="Link" onclick="init('www')">
                    <input type="button" value="@" onclick="init('mail')">
                    <input type="button" value="&nbsp;?&nbsp;" onclick="hilfe()">
                    <input type="button" value="<?php echo $details_lang['leeren'] ?>" onclick="leeren()">
                </td>
                <td width='13' height='37' class='rr'>&nbsp;</td>
            </tr>

            <tr class="cell">
                <td width="13" height="37" class="rl">&nbsp;</td>
                <td colspan="2" align="center">
                    <textarea rows="10" cols="64" tabindex="4" name="nachricht" id="nachricht"></textarea>
                </td>
                <td width="13" height="37" class="rr">&nbsp;</td>
            </tr>

            <tr class="cell">
                <td width="13" height="37" class="rl">&nbsp;</td>
                <td colspan="2" align=center><input type="button" value="<?php echo $details_lang['laengepruefen'] ?>" onClick="check()"> <input type="submit" tabindex="5" onclick="return zeichenundsmiliecheck()" name="antbut" value="<?php echo $details_lang['hfnabsenden'] ?>"></td>
                <td width="13" height="37" class="rr">&nbsp;</td>
            </tr>

            <tr>
                <td width="13" class="rul">&nbsp;</td>
                <td class="ru" colspan="2">&nbsp;</td>
                <td width="13" class="rur">&nbsp;</td>
            </tr>
        </table>
    </form>
    <br><br>

<?php
}
////////////////////////////////////////////////////////
// Chat-Ignore verwalten
////////////////////////////////////////////////////////
//m�chte man einen Eintrag l�schen
$del_ignore = isset($_REQUEST['del_ignore']) ? intval($_REQUEST['del_ignore']) : 0;
if ($del_ignore > 0) {
    mysqli_execute_query(
        $GLOBALS['dbi_ls'],
        "DELETE FROM de_chat_ignore WHERE id=? AND owner_id=?",
        [$del_ignore, $_SESSION['ums_owner_id']]
    );
}

if (isset($_REQUEST['sn']) && $_REQUEST['sn'] !== '') {
    //unterscheiden zwischen Spieler auf eigenem Server und Spieler auf anderem Server
    echo '<form action="details.php" method="post">';
    if (!empty($_REQUEST['sn'])) {
        echo '<input type="hidden" name="sn" value="' . $_REQUEST['sn'] . '">';
    }
    if (!empty($_REQUEST['ctyp'])) {
        echo '<input type="hidden" name="ctyp" value="' . $_REQUEST['ctyp'] . '">';
    }
    if (!empty($_REQUEST['cid'])) {
        echo '<input type="hidden" name="cid" value="' . $_REQUEST['cid'] . '">';
    }

    rahmen_oben('Verwaltung von Spielern die im Chat ignoriert werden');
    if (isset($_REQUEST['ctyp']) && isset($_REQUEST['cid'])) { //anderer server
        //aus dem Chat die dazugeh�rige owner_id holen
        $db_daten = mysqli_execute_query(
            $GLOBALS['dbi_ls'],
            "SELECT * FROM de_chat_msg WHERE id=? AND channeltyp=?",
            [intval($_REQUEST['cid']), intval($_REQUEST['ctyp'])]
        );
        $num = mysqli_num_rows($db_daten);
        if ($num == 1) {
            $row = mysqli_fetch_assoc($db_daten);
            $zowner_id = $row['owner_id'];
            echo '<div class="cell" style="width: 560px; text-align: center;">';

            //man kann sich nicht selbst ignorieren
            if ($zowner_id != $_SESSION['ums_owner_id']) {

                //m�chte man einen Spieler zur Ignore-Liste hinzuf�gen?
                if (isset($_REQUEST['ignore_add']) && $zowner_id > 0) {
                    $ignore_until = time() + (3600 * 24 * intval($_REQUEST['ignore_time']));
                    $sql = "INSERT INTO de_chat_ignore SET owner_id='" . $_SESSION['ums_owner_id'] . "', owner_id_ignore='$zowner_id', score=1, ignore_until='$ignore_until', 
						spielername='" . ($_REQUEST['ignore_name'] ?? $_REQUEST['sn']) . "';";

                    mysqli_query($GLOBALS['dbi_ls'], $sql);
                }

                //�berpr�fen ob der Spieler bereits auf der Ignore-Liste ist
                $db_daten = mysqli_execute_query(
                    $GLOBALS['dbi_ls'],
                    "SELECT * FROM de_chat_ignore WHERE owner_id=? AND owner_id_ignore=? AND ignore_until>?",
                    [$_SESSION['ums_owner_id'], $zowner_id, time()]
                );
                $num = mysqli_num_rows($db_daten);


                if ($num == 1) {  // er steht schon drin
                    $row = mysqli_fetch_assoc($db_daten);
                    echo 'Dieser Spieler (' . $row['spielername'] . ') befindet sich aktuell auf der Chat-Ignore-Liste.';
                } elseif ($zowner_id > 0) { //er steht noch nicht drin
                    echo 'Den Spieler unter folgendem Namen zur Chat-Ignoreliste hinzuf&uuml;gen : ';
                    echo '<input name="ignore_name" maxlength="20" value="' . $_REQUEST['sn'] . '" autocomplete="off" type="text">';

                    echo '<br>Zeitdauer der Blockierung: ';

                    if (!isset($_REQUEST['ignore_time'])) {
                        $_REQUEST['ignore_time'] = 30;
                    }

                    $ignore_times = array(2, 10, 20, 30, 60, 90, 180, 360);

                    echo '<select name="ignore_time">';
                    for ($i = 0; $i < count($ignore_times); $i++) {
                        if ($ignore_times[$i] == $_REQUEST['ignore_time']) {
                            $selected = ' selected';
                        } else {
                            $selected = '';
                        }

                        echo '<option value="' . $ignore_times[$i] . '"' . $selected . '>' . $ignore_times[$i] . ' Tage</option>';
                    }
                    echo '</select>';


                    echo '<br><br><input name="ignore_add" value="hinzuf&uuml;gen" type="Submit"><br>';
                } else {
                    echo 'Es wurde kein Spieler ausgew&auml;hlt.';
                }
            } else {
                echo '<div class="cell" style="width: 560px;">Du kannst Dich nicht selbst ignorieren.</div>';
            }

            echo '</div>';
        } else {
            echo '<div class="cell" style="width: 560px;">Der Spieler konnte nicht gefunden werden.</div>';
        }
    } else { //eigener Server
        echo '<div class="cell" style="width: 560px; text-align: center;">';

        //man kann sich nicht selbst ignorieren
        if ($zowner_id != $_SESSION['ums_owner_id']) {

            //m�chte man einen Spieler zur Ignore-Liste hinzuf�gen?
            if (isset($_REQUEST['ignore_add']) && $zowner_id > 0) {
                $ignore_until = time() + (3600 * 24 * intval($_REQUEST['ignore_time']));
                $spielername = isset($_REQUEST['ignore_name']) ? $_REQUEST['ignore_name'] : $_REQUEST['sn'];
                mysqli_execute_query(
                    $GLOBALS['dbi_ls'],
                    "INSERT INTO de_chat_ignore SET owner_id=?, owner_id_ignore=?, score=1, ignore_until=?, spielername=?",
                    [$_SESSION['ums_owner_id'], $zowner_id, $ignore_until, $spielername]
                );
            }

            //überprüfen ob der Spieler bereits auf der Ignore-Liste ist
            $db_daten = mysqli_execute_query(
                $GLOBALS['dbi_ls'],
                "SELECT * FROM de_chat_ignore WHERE owner_id=? AND owner_id_ignore=? AND ignore_until>?",
                [$_SESSION['ums_owner_id'], $zowner_id, time()]
            );
            $num = mysqli_num_rows($db_daten);


            if ($num == 1) {  // er steht schon drin
                $row = mysqli_fetch_assoc($db_daten);
                echo 'Dieser Spieler (' . $row['spielername'] . ') befindet sich aktuell auf der Chat-Ignore-Liste.';
            } elseif ($zowner_id > 0) { //er steht noch nicht drin
                echo 'Den Spieler unter folgendem Namen zur Chat-Ignoreliste hinzuf&uuml;gen : ';
                echo '<input name="ignore_name" maxlength="20" value="' . $_REQUEST['sn'] . '" autocomplete="off" type="text">';

                echo '<br>Zeitdauer der Blockierung: ';

                if (!isset($_REQUEST['ignore_time'])) {
                    $_REQUEST['ignore_time'] = 30;
                }

                $ignore_times = array(2, 10, 20, 30, 60, 90, 180, 360);

                echo '<select name="ignore_time">';
                for ($i = 0; $i < count($ignore_times); $i++) {
                    if ($ignore_times[$i] == $_REQUEST['ignore_time']) {
                        $selected = ' selected';
                    } else {
                        $selected = '';
                    }

                    echo '<option value="' . $ignore_times[$i] . '"' . $selected . '>' . $ignore_times[$i] . ' Tage</option>';
                }
                echo '</select>';


                echo '<br><br><input name="ignore_add" value="hinzuf&uuml;gen" type="Submit"><br>';
            } else {
                echo 'Es wurde kein Spieler ausgew&auml;hlt.';
            }
        } else {
            echo '<div class="cell" style="width: 560px;">Du kannst Dich nicht selbst ignorieren.</div>';
        }

        echo '</div>';
    }
    //�berpr�fen ob der Spieler auf Ignore steht
    rahmen_unten();
    echo '</form>';
}
////////////////////////////////////////////////////////
// Eine Liste der im Chat ignorierten Spielern ausgeben
// darüber soll ebenfalls eine LÖschung möglich sein
////////////////////////////////////////////////////////
$db_daten = mysqli_execute_query(
    $GLOBALS['dbi_ls'],
    "SELECT * FROM de_chat_ignore WHERE owner_id=? AND ignore_until>?",
    [$_SESSION['ums_owner_id'], time()]
);
$num = mysqli_num_rows($db_daten);
if ($num >= 1) {
    rahmen_oben('Folgende Spieler sind auf Deiner Chat-Ignore-Liste');
    echo '<table class="cell" style="width: 560px;">';
    echo '<tr style="font-weight: bold;"><td>Spielername</td><td>Blockiert bis</td><td>Aktion</td></tr>';
    //aus dem Chat die dazugeh�rige owner_id holen
    while ($row = mysqli_fetch_assoc($db_daten)) {
        echo '
		<tr>
			<td>' . $row['spielername'] . '</td>
			<td>' . date("d.m.Y", $row['ignore_until']) . '</td>
			<td><a href="details.php?del_ignore=' . $row['id'] . '">Eintrag l&ouml;schen</a></td>
		</tr>';
    }
    echo '</table>';
    rahmen_unten();
}
////////////////////////////////////////////////////////
//Details nur anzeigen, wenn es der eigene Server ist
////////////////////////////////////////////////////////
if (!isset($_REQUEST['ctyp']) && !isset($_REQUEST['cid']) && !empty($rew["spielername"])) {

    //in Sektor 1, darf man nicht die Details einsehen
    if ($sector == 1) {
        exit();
    }
?>
    <table border="0" cellspacing="0" cellpadding="0" width="400px">
    <?php
    //Anhand der Userid werden hier die Userdetails aus der DB ausgelesen.
    $db_daten = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_user_info WHERE user_id=?",
        [$zuser_id]
    );
    $row = mysqli_fetch_assoc($db_daten);

    $ud_all = $row['ud_all'];
    if ($rew['sector'] == $sector && $sector > 1) {
        $ud_sector = $row['ud_sector'];
    } else {
        $ud_sector = 'keine Zugriffsrechte';
    }
    if ($rew['allytag'] == $allytag && $rew['status'] == 1 && $allytag != '') {
        $ud_ally = $row['ud_ally'];
    } else {
        $ud_ally = 'keine Zugriffsrechte';
    }

    rahmen_oben($details_lang['detailsvon'] . $rew["spielername"]);
    echo '<div class="cell" style="width: 560px;">';
    //alle
    echo '<div>Informationen für alle:</div>';
    echo '<div class="mt4 mb15">' . htmlspecialchars($ud_all, ENT_QUOTES, 'UTF-8') . '</div>';
    //sektor
    echo '<div>Sektorinformationen:</div>';
    echo '<div class="mt4 mb15">' . htmlspecialchars($ud_sector, ENT_QUOTES, 'UTF-8') . '</div>';
    //allianz/allianzpartner
    echo '<div>Allianzinformationen:</div>';
    echo '<div class="mt4">' . htmlspecialchars($ud_ally, ENT_QUOTES, 'UTF-8') . '</div>';
    echo '</div>';
    rahmen_unten();
}
echo '<br><br>';
    ?>

    </body>

</html>