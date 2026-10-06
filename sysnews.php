<?php
include "inc/header.inc.php";
include "lib/kampfbericht.lib.php";
include 'inc/lang/'.$sv_server_lang.'_sysnews.lang.php';
include "functions.php";
include "tickler/kt_einheitendaten.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];

if ($newnews == 1) { //wenn einen neue nachricht vorlag, den indikator wieder auf 0 setzen
    $sql = "UPDATE de_user_data SET newnews = 0 WHERE user_id=?";
    mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
}
$newnews = 0;

//Schiffspunkte f�r die Kampfbericht-lib
for ($rasse = 1;$rasse <= 5;$rasse++) {
    $schiffspunkte[$rasse - 1][0] = $unit[$rasse - 1][0][4];//j�ger
    $schiffspunkte[$rasse - 1][1] = $unit[$rasse - 1][1][4];//jagdboot
    $schiffspunkte[$rasse - 1][2] = $unit[$rasse - 1][2][4];//zerst�rer
    $schiffspunkte[$rasse - 1][3] = $unit[$rasse - 1][3][4];//kreuzer
    $schiffspunkte[$rasse - 1][4] = $unit[$rasse - 1][4][4];//schlachtschiff
    $schiffspunkte[$rasse - 1][5] = $unit[$rasse - 1][5][4];//bomber
    $schiffspunkte[$rasse - 1][6] = $unit[$rasse - 1][6][4];//transmitterschiff
    $schiffspunkte[$rasse - 1][7] = $unit[$rasse - 1][7][4];//tr�gerschiff
    $schiffspunkte[$rasse - 1][8] = $unit[$rasse - 1][8][4];//frachter
    $schiffspunkte[$rasse - 1][9] = $unit[$rasse - 1][9][4];//titan
    //t�rme
    $schiffspunkte[$rasse - 1][10] = $unit[$rasse - 1][10][4];
    $schiffspunkte[$rasse - 1][11] = $unit[$rasse - 1][11][4];
    $schiffspunkte[$rasse - 1][12] = $unit[$rasse - 1][12][4];
    $schiffspunkte[$rasse - 1][13] = $unit[$rasse - 1][13][4];
    $schiffspunkte[$rasse - 1][14] = $unit[$rasse - 1][14][4];
}

//echo serialize($schiffspunkte);

//wurde ein button gedrueckt??
$sn_meldung = '';
if (isset($_GET["a"]) && $_GET["a"] == "d") {//alle gelesenen nachrichten löschen
    $sql = "DELETE FROM de_user_news WHERE user_id=? AND seen=1";
    mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
    $sn_meldung = '<div class="mod-meldung mod-meldung-ok">'.$sn_lang["geloescht"].'</div>';
}


//////////////////////////////////////////////////
// Nachrichten als HTML-Datei herunterladen
// vor jeder Ausgabe, damit die Download-Header gesetzt werden können
//////////////////////////////////////////////////
if (isset($_REQUEST["mailnews"]) && $_REQUEST["mailnews"]) {
    $bodyTag = '<body class="theme-rasse'.$_SESSION['ums_rasse'].' mobile">';

    $serverPath='https://'.$_SERVER['SERVER_NAME'].'/gp/';

    //html dateiinhalt
    $allenachrichten = '
<html> 
<head>
<title>Die Ewigen - Nachrichten Export</title>
<link rel="stylesheet" type="text/css" href="'.$serverPath.'/de-main.css">
<meta charset="UTF-8">
</head>
'.$bodyTag.'
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="background-color: #000000;">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="560" class="ro" align="center">Die Ewigen - Nachrichten Export</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="1">
<table width="560" border="0" cellpadding="0" cellspacing="1" width="100%">
';


    $sql = "SELECT time, typ, text FROM de_user_news WHERE user_id=? ORDER BY time DESC";
    $query = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
    $hrstr='';
    while ($row = mysqli_fetch_assoc($query)) {
        $t = (string)$row["time"];
        $n = $row["typ"];
        $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];

        switch ($n) {
            case 8:
                if ($n == 8) {
                    $n = 3;
                }
                $werte = explode(";", $row["text"]);
                $tronic = $werte[0];
                unset($na);
                include "inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";
                $nanr = mt_rand(0, count($na) - 1);

                $nachricht = $na[$werte[1]];

                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$serverPath.'g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$nachricht.'<br><br></td>';
                $allenachrichten .= '</tr>';
                break;
            case 50:
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$serverPath.'g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.showkampfberichtV0($row["text"], $_SESSION['ums_rasse'], $_SESSION['ums_spielername'], $sector, $system, $schiffspunkte).'</td>';
                $allenachrichten .= '</tr>';
                break;
            case 57:
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$serverPath.'g/'.$_SESSION['ums_rasse'].'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.showkampfberichtV1($row["text"], $_SESSION['ums_rasse'], $_SESSION['ums_spielername'], $sector, $system, $schiffspunkte).'</td>';
                $allenachrichten .= '</tr>';
                break;
            case 70: //Battleground
                $allenachrichten .= '<tr style="text-align: left;">';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$serverPath.'g/'.$_SESSION['ums_rasse'].'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr style="text-align: left;">';
                $allenachrichten .= '<td>'.showkampfberichtBG($row["text"]).'</td>';
                $allenachrichten .= '</tr>';
                break;
            default:
                //sektorkampfsymbol setzen, wenn nötigt
                if ($n == 56) {
                    $n = 50;
                }
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$serverPath.'g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$row["text"].'<br><br></td>';
                $allenachrichten .= '</tr>';
                break;
        }
        $hrstr = '<hr>';
    }

    $allenachrichten .= '
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table></div>
</body></html>';

    // HTML-Datei zum Download bereitstellen
    $filename = 'nachrichten_'.date('Ymd_His').'.html';

    // Header für Download setzen
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Content-Length: ' . strlen($allenachrichten));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // HTML-Inhalt ausgeben und Script beenden
    echo $allenachrichten;
    exit();
}//mailnews ende

?>
<!DOCTYPE HTML>
<html>
<head>
<?php
echo '<title>'.$sn_lang['nachrichten'].'</title>';

include "cssinclude.php"; ?>
</head>
<?php

echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar
include "resline.php";

if (!isset($_GET["option"])) {
    $_GET["option"] = 0;
}

$typ='';

if ($_GET["option"] == "7") {
    $nachrichten = array(70);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "6") {
    $nachrichten = array(6);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "5") {
    $nachrichten = array(3,7,60);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "4") {
    $nachrichten = array(1,2);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "3") {
    $nachrichten = array(10,11,12);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "2") {
    $nachrichten = array(4,5,50,51,52,53,54,55,56,57);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? and (".$typ.") ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif ($_GET["option"] == "1") {
    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
} elseif (empty($_GET["option"])) {
    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id=? AND seen=0 ORDER BY time DESC";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
    $sql = "UPDATE de_user_news set seen=1 WHERE user_id=? AND seen=0";
    mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
}

//Reiter in der bisherigen Reihenfolge, der gewählte hervorgehoben
$reiter = array(0 => $sn_lang["neue"], 1 => $sn_lang["alle"], 4 => $sn_lang["gebaeude"], 2 => $sn_lang["kampf"], 3 => $sn_lang["handel"], 6 => $sn_lang["allianz"], 5 => $sn_lang["sonstige"], 7 => 'BG');
echo '<div class="mod sn-navi">';
foreach ($reiter as $opt => $name) {
    echo '<a href="sysnews.php?option='.$opt.'" class="ally-reiter'.($_GET["option"] == $opt ? ' ally-reiter-aktiv' : '').'">'.$name.'</a>';
}
echo '</div>';

if ($sn_meldung != '') {
    echo '<div class="mod pol-meldungen">'.$sn_meldung.'</div>';
}

//Art einer Nachricht nach de_user_news.typ
$sn_arten = array(1 => 'Geb&auml;ude', 2 => 'Forschung', 3 => 'Ereignis', 4 => 'Sonde', 5 => 'Agent', 6 => 'Allianz', 7 => 'Sektorspende', 8 => 'Tronic', 9 => 'Zufallsereignis',
    10 => 'Handel: eingekauft', 11 => 'Handel: verkauft', 12 => 'Handel: zur&uuml;ckgebucht', 50 => 'Kampfbericht', 51 => 'Angriff', 52 => 'Angriff zieht ab', 53 => 'Verteidigung',
    54 => 'Verteidigung zieht ab', 55 => 'Recycling', 56 => 'Sektorkampf', 57 => 'Kampfbericht', 60 => 'Gro&szlig;es Ereignis', 70 => 'Battleground');

//Kopf einer Nachricht: das (alte) Symbol je Art, Art und Zeit
function sn_kopf($bild, $art, $time)
{
    return '<div class="sn-kopf"><img src="'.$bild.'" alt="" class="sn-bild"><div><span class="mod-typ">'.$art.'</span><span class="sn-zeit">'.$time.'</span></div></div>';
}

rahmen_oben(trim($sn_lang['nachrichten']));
echo '<div class="mod sn">';

$anzahl = 0;
if (isset($db_daten)) {
while ($row = mysqli_fetch_assoc($db_daten)) { //jeder gefundene datensatz wird ausgegeben
    $t = $row["time"];
    $n = $row["typ"];
    $art = $sn_arten[$n] ?? '';
    $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];

    echo '<div class="sn-eintrag">';
    switch ($n) {
        case 8:
            if ($n == 8) {
                $n = 3;
            }
            $werte = explode(";", $row["text"]);
            $tronic = $werte[0];
            unset($na);
            include "inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";
            $nanr = mt_rand(0, count($na) - 1);

            $nachricht = $na[$werte[1]];

            echo sn_kopf('gp/g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif', $art, $time);
            echo '<div class="sn-text">'.$nachricht.'</div>';
            break;
        case 50:
            echo sn_kopf('gp/g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif', $art, $time);
            echo '<div class="sn-bericht">'.showkampfberichtV0($row["text"], $_SESSION['ums_rasse'], $_SESSION['ums_spielername'], $sector, $system, $schiffspunkte).'</div>';
            break;
        case 57: //Kampfbericht V1
            echo sn_kopf('gp/g/'.$_SESSION['ums_rasse'].'_e50.gif', $art, $time);
            echo '<div class="sn-bericht">'.showkampfberichtV1($row["text"], $_SESSION['ums_rasse'], $_SESSION['ums_spielername'], $sector, $system, $schiffspunkte).'</div>';
            break;
        case 70: //Battleground
            echo sn_kopf('gp/g/'.$_SESSION['ums_rasse'].'_e50.gif', $art, $time);
            echo '<div class="sn-bericht">'.showkampfberichtBG($row["text"]).'</div>';
            break;
        default:
            //sektorkampfsymbol setzen, wenn nötigt
            if ($n == 56) {
                $n = 50;
            }
            echo sn_kopf('gp/g/'.$_SESSION['ums_rasse'].'_e'.$n.'.gif', $art, $time);
            echo '<div class="sn-text">'.$row["text"].'</div>';
            break;
    }
    echo '</div>';
    $anzahl++;
}
}

if ($anzahl == 0) {
    echo '<div class="mod-leer">'.($_GET["option"] == 0 ? 'Es gibt keine neuen Nachrichten.' : 'Hier gibt es keine Nachrichten.').'</div>';
}

//////////////////////////////////////////////
// Nachrichten herunterladen, gelesene löschen
//////////////////////////////////////////////
$sql = "SELECT user_id FROM de_user_news WHERE user_id=?";
$db_archiv = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$nummer = mysqli_num_rows($db_archiv);

echo '<div class="sn-fuss">';
echo '<form action="sysnews.php" method="post"><button type="submit" name="mailnews" value="Nachrichten herunterladen" class="mod-btn mod-btn-leise"'.($nummer == "0" ? ' disabled' : '').'>Nachrichten herunterladen</button></form>';
echo '<a href="sysnews.php?a=d" class="mod-btn mod-btn-gefahr" data-bestaetigen="Wirklich l&ouml;schen?">Gelesene Nachrichten l&ouml;schen</a>';
echo '</div>';

echo '</div>';
rahmen_unten();
?>
</body>
</html>
