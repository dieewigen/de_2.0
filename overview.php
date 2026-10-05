<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_overview.lang.php';
include 'inc/achievement.inc.php';
include "functions.php";

?>
<!DOCTYPE HTML>
<html>
<head>
<title>&Uuml;bersicht</title>
<?php include "cssinclude.php";
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi'] == 1) ? 'mobile' : 'desktop').'">';

//logincounter zurücksetzen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = 0 WHERE user_id=?", [$_SESSION['ums_user_id']]);

$pt = loadPlayerTechs($_SESSION['ums_user_id']);
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, ehscore, tick, techs, sector, `system`, newtrans, newnews, allytag, col, col_build, agent, sonde, status, tradesystemscore, platz, rang, credits, actpoints, roundpoints, kartefakt, kgget, npcartefact, ally_tronic, eh_counter FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$ehpunkte = $row["ehscore"];
$own_tick = $row["tick"];
$techs = $row["techs"];
$newtrans = $row["newtrans"];
$tradescore = $row["tradesystemscore"];
$allytag = $row["allytag"];
$newnews = $row["newnews"];
$agent = $row["agent"];
$sonde = $row["sonde"];
$col = $row["col"];
$col_build = $row["col_build"];
$status = $row["status"];
$sector = $row["sector"];
$system = $row["system"];
$platz = $row["platz"];
$rang_nr = $row["rang"];
$credits = $row["credits"];
$actpoints = $row["actpoints"];
$rundenpunkte = $row["roundpoints"];
$kartefakt = $row["kartefakt"];
$kgget = $row["kgget"];
$npcartefact = $row['npcartefact'];
$ally_tronic = $row['ally_tronic'];
$eh_counter = $row['eh_counter'];

$ovopt = $row["ovopt"] = '1;2;5;8;9;4';
$ovoptfelder = explode(";", $ovopt);

$rang = $rangnamen[$rang_nr];

//die Anzahl von erfoschen "Vergessenen Systemen" auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_map WHERE user_id=?", [$_SESSION['ums_user_id']]);
$anz_vergessene_systeme_erforscht = mysqli_num_rows($db_daten);


//zu den Agenten kommen noch die Agenten dazu die auf einer Mission sind
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(need_agents) AS anzahl FROM de_user_mission WHERE user_id=? AND get_reward=0", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$agent += $row['anzahl'];

if ($status == 0) {
    $allytag = ' ';
}

if ($_SESSION['ums_rasse'] == 1) {
    $rasse = 'Ewiger';
} elseif ($_SESSION['ums_rasse'] == 2) {
    $rasse = 'Ishtar';
} elseif ($_SESSION['ums_rasse'] == 3) {
    $rasse = 'K&#180;Tharr';
} elseif ($_SESSION['ums_rasse'] == 4) {
    $rasse = 'Z&#180;tah-ara';
}

$sel_news = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_news_overview where typ=1 order by id desc Limit 0,5");
$det_news = '';
while ($rew = mysqli_fetch_array($sel_news)) {
    $t = $rew['time'];
    $time = $t[8].$t[9].'.'.$t[5].$t[6].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[11].$t[12].':'.$t[14].$t[15];

    $det_news .= '<a href="newspaper.php?id='.$rew['id'].'" class="ov-news-zeile"><span class="ov-news-datum">'.$time.'</span><span class="ov-news-betreff">'.$rew['betreff'].'</span></a>';
}

//stelle die ressourcenleiste dar
include "resline.php";

//test auf com-sperre
$akttime = date("Y-m-d H:i:s", time());
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT com_sperre FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
if ($row['com_sperre'] > $akttime) {
    $sperrtime = strtotime($row['com_sperre']);
    echo('<div class="ov-sperre mod"><div class="mod-meldung mod-meldung-fehler">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - H:i", $sperrtime).'</div></div>');
}



@$filetime = filemtime("cache/overview.inc.php");
$aktdate = date("d.m. H:i", $filetime);

//ausgabe der einzelnen positionen

//Heimkehr aus dem Exil: Fluxurion meldet sich anstelle der Info über den Startsektor
include_once 'inc/lang/'.$sv_server_lang.'_exile.lang.php';
$exileText = '';
try {
    $exileService = new \DieEwigen\DE2\Model\Exile\ExileService($GLOBALS['dbi']);
    $exileText = $exileService->overviewText((int)$_SESSION['ums_user_id'], (int)$sector, (int)$col, (float)$punkte, $exile_lang);
} catch (\Throwable $e) {
    error_log('Exil-Box fehlgeschlagen: '.$e->getMessage());
}

if ($exileText !== '') {
    rahmen_oben($exile_lang['box_titel']);
    echo '<div class="ov ov-berater mod"><img src="gp/g/berater5.png" class="ov-berater-bild" alt=""><div class="ov-berater-text">'.$exileText.'</div></div>';
    rahmen_unten();
} elseif ($sector <= 1) {
    //Sektor 0 ist nur der Übergang: neue Konten stehen dort, bis register_user.php sie in Sektor 1 setzt (jede Minute)
    $sek0info = ($sector == 0) ? '<div class="mod-meldung mod-meldung-ok">'.$ov_lang['sek0info'].'</div>' : '';
    rahmen_oben($ov_lang['sek1welcome']);
    echo '<div class="ov ov-willkommen mod">'.$sek0info.$ov_lang['sek1info'].'</div>';
    rahmen_unten();
}

///////////////////////////////////////////////////////////////
//die rundencounteranzeige erstellen - anfang
///////////////////////////////////////////////////////////////
if ($sv_hardcore == 1) {

    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
    $row = mysqli_fetch_array($db_daten);
    $maxtick = $row['tick'];


    $rca = '<div class="ov-hc-ziel">Das Ziel beim Hardcore-Rundenmodus ist es als erster 5 Erhabenenteilsiege zu erreichen und somit zum vollwertigen ERHABENEN zu werden.</div>';

    //Top 3
    //die ersten drei Plätze anzeigen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=0 AND sector > 1 AND (eh_siege>0 OR eh_counter>0) ORDER BY eh_siege DESC, eh_counter DESC LIMIT 3", []);
    $num = mysqli_num_rows($db_daten);
    if ($num > 0) {
        $rca .= '<div class="ov-hc">';
        $platzc = 1;
        while ($row = mysqli_fetch_array($db_daten)) {
            if ($row['status'] == 1 && !empty($row['allytag'])) {
                $allianz = '<br>Allianz: '.$row['allytag'];
            } else {
                $allianz = '';
            }

            $rca .= '<div class="ov-hc-box">';
            $rca .= '<span class="mod-typ">Top '.$platzc.' - EH-Anw&auml;rter</span>'.
                    '<br>'.$row['spielername'].
                    $allianz.
                    '<br>EH-Teilsiege: '.$row['eh_siege'].'/'.$sv_hardcore_need_wins.
                    '<br>EH-Counter: '.$row['eh_counter'].'/'.$sv_eh_counter;

            $rca .= '</div>';
            $platzc++;
        }
        $rca .= '</div>';
    }


    $rca .= '<div class="ov-hc">';
    //aktueller EH-Counter
    if ($maxtick > 1000) {
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=0 AND sector > 1 ORDER BY ehscore DESC LIMIT 1", []);
        $num = mysqli_num_rows($db_daten);
        if ($num > 0) {

            $platzc = 1;
            while ($row = mysqli_fetch_array($db_daten)) {
                if ($row['status'] == 1 && !empty($row['allytag'])) {
                    $allianz = '<br>Allianz: '.$row['allytag'];
                } else {
                    $allianz = '';
                }

                $rca .= '<div class="ov-hc-box">';
                $rca .= '<span class="mod-typ">EH-Counter l&auml;uft f&uuml;r</span>'.
                        '<br>'.$row['spielername'].
                        $allianz.
                        '<br>EH-Teilsiege: '.$row['eh_siege'].'/'.$sv_hardcore_need_wins.
                        '<br>EH-Counter: '.$row['eh_counter'].'/'.$sv_eh_counter;

                $rca .= '</div>';
                $platzc++;
            }


        }
    }
    //man selbst
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=? LIMIT 1", [$_SESSION['ums_user_id']]);
    $num = mysqli_num_rows($db_daten);
    if ($num > 0) {

        $platzc = 1;
        while ($row = mysqli_fetch_array($db_daten)) {
            if ($row['status'] == 1 && !empty($row['allytag'])) {
                $allianz = '<br>Allianz: '.$row['allytag'];
            } else {
                $allianz = '';
            }

            $rca .= '<div class="ov-hc-box">';
            $rca .= '<span class="mod-typ">Deine Daten</span>'.
                    '<br>'.$row['spielername'].
                    $allianz.
                    '<br>EH-Teilsiege: '.$row['eh_siege'].'/'.$sv_hardcore_need_wins.
                    '<br>EH-Counter: '.$row['eh_counter'].'/'.$sv_eh_counter;

            $rca .= '</div>';
            $platzc++;
        }


    }

    $rca .= '</div>';

} else {
    //normale runde - rundenstatus auslesen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
    $row = mysqli_fetch_array($db_daten);
    if ($row["tick"] <= 0) {
        $ticks = 1;
    } else {
        $ticks = $row["tick"];
    }

    $p = 1;

    if ($ticks < 2500000) { //check auf BR

        //wenn die ticks kleiner als die maximale tickzahl sind, dann läuft die runde noch
        if ($ticks < $sv_winscore) {
            //wieviel prozent der runde sind rum
            $p = $ticks / $sv_winscore;

            //Datum des Start des EH-Kampfes berechnen
            $verbleibendeWT = $sv_winscore - $ticks;
            $anzahlWtProTag = 0;
            for($i=0; $i<24; $i++) {
                $anzahlWtProTag+=count($GLOBALS['wts'][$i]);
            }

            $ehkampfStartZeit=date("d.m. Y - H:i", time()+($verbleibendeWT/$anzahlWtProTag*60*60*24));
            

            //Tooltip bauen
            $ttip = $ov_lang['rundenfortschritt'].': '.number_format($ticks, 0, "", ".").'/'.number_format($sv_winscore, 0, "", ".").
            '<br>Startzeitpunkt des Erhabenenkampfes ca.: '.$ehkampfStartZeit.
            '<br>'.$ov_lang['rundenhalteticks'].': '.number_format($sv_benticks, 0, "", ".").
            '<br><br>'.$ov_lang['rundeninfo1'].' '.$ov_lang['rundeninfo2'];

        } else { //erhabenenkampf, oder die runde ist zu ende

            //überprüfen ob der eh-kampf noch läuft, oder ab er schon rum ist
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT doetick, winid, winticks FROM de_system", []);
            $row = mysqli_fetch_array($result);
            $doetick = $row["doetick"];
            $winticks = $row["winticks"];
            $winid = $row["winid"];

            if ($winticks <= 1 && $doetick == 0 && $winid > 0) {//rundenende
                $p = 1; // 100% für Rundenende
                $ttip = $ov_lang['rundenende'].' '.$ov_lang['rundeninfo3'];
            } else { //eh-kampf läuft
                $p = 1; // 100% für EH-Kampf
                $isEhKampf = true; // Flag für speziellen Text
                $ttip = $ov_lang['rundenendeeh'].'<br>'.
                $ov_lang['rundenhalteticks'].': '.number_format($sv_benticks, 0, "", ".").
                '<br><br>'.$ov_lang['rundeninfo2'];
            }
        }
    } else { //battleround
        $p = 1; // 100% für Battleround
        $isBattleround = true; // Flag für speziellen Text
        $ttip = $ov_lang['battleround'].' '.$ov_lang['rundeninfo3'];
    }

    //tooltip bauen
    $atip = $ov_lang['rundenstatus'].'&'.$ttip;

    // Science Fiction Progress Bar
    $percentage = round($p * 100, 1);
    $progressClass = 'progress-normal';

    if ($p > 0.66) {
        $progressClass = 'progress-high';
    } elseif ($p > 0.33) {
        $progressClass = 'progress-medium';
    }

    // Text bestimmen: EH-Kampf, Battleround oder normale Rundenlaufzeit
    if (isset($isEhKampf) && $isEhKampf) {
        $roundTimeText = 'ERHABENENKAMPF ('.$winticks.')';
    } elseif (isset($isBattleround) && $isBattleround) {
        $roundTimeText = 'BATTLEROUND';
    } else {
        $currentTicks = number_format($ticks, 0, "", ".");
        $maxTicks = number_format($sv_winscore, 0, "", ".");
        $roundTimeText = $currentTicks . ' / ' . $maxTicks;
    }

    $rca = '
        <div id="gameProgressBar">
            <div class="scifi-progress-container" title="'.$atip.'">
                <div class="scifi-corner top-left"></div>
                <div class="scifi-corner top-right"></div>
                <div class="scifi-corner bottom-left"></div>
                <div class="scifi-corner bottom-right"></div>
                
                <div class="scifi-progress-bar '.$progressClass.'" style="width: '.$percentage.'%">
                </div>
                
                <div class="scifi-text">'.$roundTimeText.'</div>
            </div>
        </div>';


}
///////////////////////////////////////////////////////////////
//die rundencounteranzeige erstellen - ende
///////////////////////////////////////////////////////////////

rahmen_oben('&Uuml;bersicht');
echo '<div class="ov mod">';

//Rundenfortschrittsanzeige
echo '<div class="ov-runde">'.$rca.'</div>';

//Siegel von Basranur, nur wenn die Vergessenen Systeme aktiv sind
if (($GLOBALS['sv_deactivate_vsystems'] ?? 0) != 1) {
    try {
        include_once 'inc/lang/'.$sv_server_lang.'_siegel.lang.php';
        $siegel = new \DieEwigen\DE2\Model\Siegel\SiegelService($GLOBALS['dbi']);
        $siegel_level = $siegel->getLevel();
        $siegel_contributors = $siegel->countContributors();
        $siegel_missing = $siegel->missingForNextLevel($siegel_contributors);
        $siegel_text = strtr($siegel_lang['ov_zeile'], [
            '{LEVEL}' => $siegel_level,
            '{PCT}' => $siegel_level * \DieEwigen\DE2\Model\Siegel\SiegelService::PROZENT_PRO_STUFE,
            '{N}' => $siegel_contributors,
        ]);
        if ($siegel_missing > 0) {
            $siegel_text .= strtr($siegel_lang['ov_noch_bis'], ['{MISSING}' => $siegel_missing, '{STEP}' => $siegel->levelFor($siegel_contributors) + 1]);
        }
        $siegel_explored = $siegel->isExplored((int)$_SESSION['ums_user_id']);
        $siegel_title = $siegel_explored
            ? '<a href="map_system.php?id='.$siegel->getSealMapId().'">'.$siegel_lang['ov_titel'].'</a>'
            : $siegel_lang['ov_titel'];
        echo '<div class="ov-siegel"><b>'.$siegel_title.':</b> '.$siegel_text.'.'.($siegel_explored ? '' : ' '.$siegel_lang['ov_hinweis']).'</div>';
    } catch (\Throwable $e) {
        error_log('Siegel von Basranur: '.$e->getMessage());
    }
}

//Links: Serverinfos, Hilfe, Umfragen, Community; als schlichte Knöpfe passen alle fünf in eine Zeile
echo '
    <div class="ov-links">
        <a href="sinfo.php?" class="mod-btn mod-btn-leise">Serverinfos</a>
        <a href="'.$sv_link[2].'" target="_blank" class="mod-btn mod-btn-leise">Hilfe</a>
        <a href="vote_overview.php?bar=yes" class="mod-btn mod-btn-leise">Umfragen</a>
        <a href="https://discord.gg/qBpCPx4" target="_blank" class="mod-btn mod-btn-leise">DE-Discord</a>
        <a href="https://chat.whatsapp.com/FmUiandWLPxHrrnolH5EuI" target="_blank" class="mod-btn mod-btn-leise">DE-WhatsApp</a>
    </div>

    <div class="ov-news">
        <div class="ov-kopf"><span class="mod-typ">'.$ov_lang['detkristueber'].'</span><a href="newspaper.php?action=archiv&typ=1" class="ov-kopf-link">'.$ov_lang['archiv'].'</a></div>
        '.$det_news.'
    </div>';
echo '</div>';
rahmen_unten();

//Systemübersicht: oben wer man ist, darunter die Kennzahlen als Kacheln
$allianz_str = (trim($allytag) != '') ? ' &middot; '.$ov_lang['allianz'].' '.$allytag : '';
$kennzahlen = array(
    array($ov_lang['kollektoren'], number_format($col, 0, "", ".")),
    array($ov_lang['sonden'], number_format($sonde, 0, "", ".")),
    array($ov_lang['agenten'], number_format($agent, 0, "", ".")),
    array($ov_lang['handelspunkte'], number_format($tradescore, 0, "", ".")),
    array($ov_lang['rundenpunkte'], number_format($rundenpunkte, 0, "", ".")),
    //tick zählt jeden Wirtschaftstick seit Bestehen des Kontos
    array('Spielzeit', number_format($own_tick, 0, "", ".").' <small>WT</small>'),
);
$kacheln = '';
foreach ($kennzahlen as $kennzahl) {
    $kacheln .= '<div class="ov-wert"><span class="mod-typ">'.$kennzahl[0].'</span><b>'.$kennzahl[1].'</b></div>';
}

rahmen_oben($ov_lang['systemuebersicht']);
echo '
<div class="ov mod">
    <div class="ov-profil">
        <img src="gp/g/derassenlogo'.$_SESSION['ums_rasse'].'.png" class="ov-logo" alt="">
        <div class="ov-profil-text">
            <div class="ov-name">'.$_SESSION['ums_spielername'].'</div>
            <div class="ov-unter">'.$rasse.$allianz_str.' &middot; '.$ov_lang['sys'].' '.$sector.':'.$system.'</div>
            <div class="ov-chips">
                <span class="mod-chip">'.$ov_lang['platz'].' <b>'.number_format($platz, 0, "", ".").'</b></span>
                <span class="mod-chip">'.$ov_lang['rang'].' <b>'.$rang.'</b></span>
            </div>
        </div>
    </div>
    <div class="ov-werte">'.$kacheln.'</div>
    <div class="ov-meta">'.$ov_lang['accountid'].' '.$sv_server_tag.$_SESSION['ums_user_id'].' &middot; '.$ov_lang['galaxie'].' '.$sv_server_name.'</div>
</div>';
rahmen_unten();

//schiffseinheiten/verteidigungsanlagen
//zaehle alle schiffe, die schon vorhanden sind - anfang
$ec = array();
$ec[81] = 0;
$ec[82] = 0;
$ec[83] = 0;
$ec[84] = 0;
$ec[85] = 0;
$ec[86] = 0;
$ec[87] = 0;
$ec[88] = 0;
$ec[89] = 0;
$ec[90] = 0;
$fid0 = $_SESSION['ums_user_id'].'-0';
$fid1 = $_SESSION['ums_user_id'].'-1';
$fid2 = $_SESSION['ums_user_id'].'-2';
$fid3 = $_SESSION['ums_user_id'].'-3';
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e81, e82, e83, e84, e85, e86, e87,e88,e89,e90 FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
while ($row = mysqli_fetch_array($db_daten)) {
    $ec[81] += $row['e81'];
    $ec[82] += $row['e82'];
    $ec[83] += $row['e83'];
    $ec[84] += $row['e84'];
    $ec[85] += $row['e85'];
    $ec[86] += $row['e86'];
    $ec[87] += $row['e87'];
    $ec[88] += $row['e88'];
    $ec[89] += $row['e89'];
    $ec[90] += $row['e90'];
}
//zaehle alle schiffe, die schon vorhanden sind - ende

//lade einheitentypen
$flag1 = 0;
$ik = 0;
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT  tech_id, tech_name FROM de_tech_data WHERE tech_id>80 AND tech_id<100 ORDER BY tech_id");
while ($row = mysqli_fetch_array($db_daten)) {
    $schiffe[$ik][0] = getTechNameByRasse($row["tech_name"], $_SESSION['ums_rasse']);
    $schiffe[$ik][1] = number_format($ec[$row["tech_id"]], 0, "", ".");
    $ik++;
}

//verteidigungsanlagen auslesen
//zaehle alle verteidigungsanlagen, die schon vorhanden sind - anfang
$ec[100] = 0;
$ec[101] = 0;
$ec[102] = 0;
$ec[103] = 0;
$ec[104] = 0;

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$ec[100] = $row['e100'];
$ec[101] = $row['e101'];
$ec[102] = $row['e102'];
$ec[103] = $row['e103'];
$ec[104] = $row['e104'];
//zaehle alle verteidigungsanlagen, die schon vorhanden sind - ende

//lade einheitentypen
$flag1 = 0;
$ik = 0;
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT tech_id, tech_name FROM de_tech_data WHERE tech_id>99 AND tech_id<110 ORDER BY tech_id");
while ($row = mysqli_fetch_array($db_daten)) {
    $defense[$ik][0] = getTechNameByRasse($row["tech_name"], $_SESSION['ums_rasse']);
    $defense[$ik][1] = number_format($ec[$row["tech_id"]], 0, "", ".");
    $ik++;
}

//Einheiten in zwei Spalten; Typen ohne Bestand blass
$einheiten_spalten = '';
foreach (array(array($ov_lang['schiffseinheiten'], $schiffe), array($ov_lang['verteidigungsanlagen'], $defense)) as $spalte) {
    $zeilen = '';
    foreach ($spalte[1] as $einheit) {
        $zeilen .= '<div class="ov-einheit'.($einheit[1] === '0' ? ' ov-einheit-null' : '').'"><span>'.$einheit[0].'</span><b>'.$einheit[1].'</b></div>';
    }
    $einheiten_spalten .= '<div class="ov-einheiten-spalte"><div class="mod-typ">'.$spalte[0].'</div>'.$zeilen.'</div>';
}

rahmen_oben($ov_lang['schiffseinheiten'].' &amp; '.$ov_lang['verteidigungsanlagen']);
echo '<div class="ov mod"><div class="ov-einheiten">'.$einheiten_spalten.'</div></div>';
rahmen_unten();

//errungenschaften: die Zeilen sammelt die Schleife in $ac_zeilen, ausgegeben werden sie danach
$ac_zeilen = '';

//alle errungenschaften auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_achievement WHERE user_id = ?", [$_SESSION['ums_user_id']]);
$num = mysqli_num_rows($db_daten);
//wenn es noch keinen datensatz gibt, diesen anlegen
if ($num == 0) {
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_achievement (user_id) VALUES (?)", [$_SESSION['ums_user_id']]);
    //jetzt nochmal auslesen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_achievement WHERE user_id = ?", [$_SESSION['ums_user_id']]);
}
//errungenschaften laden
$ac_daten = mysqli_fetch_array($db_daten);

//alle belohnungen in einer schleife �berpr�fen
//$ticks=30000;
//$col=20;
$ac_belohnung = 0;
$output = '';
$c1 = 0;
for ($ac = 0;$ac < $achievement_anz;$ac++) {

    //kopfgeld killen
    if ($ac == 10 && $sv_oscar == 1) {
        $ac++;
    }


    $do_calc = 0;
    $ac_akt = 0;
    $ac_max = 0;
    switch ($ac) {

        case 0: //besitze kollektoren
            $ac_table_field = 'ac1';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards1;
            $zielwert = $col;
            $do_calc = 1;
            $text1 = 'Besitze Kollektoren';
            $text2 = 'Besitze die angegebene Menge von Kollektoren.';
            break;
        case 1: //gestohlene kollektoren
            $ac_table_field = 'ac2';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards2;
            $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT SUM(colanz) AS anzahl FROM de_user_getcol WHERE user_id = '".$_SESSION['ums_user_id']."' AND colanz>0");
            $rowx = mysqli_fetch_array($db_datenx);
            $zielwert = $rowx['anzahl'];
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel2_1'];
            $text2 = $ov_lang['ac_ziel2_2'];
            break;
        case 2: //unterhalte agenten
            $ac_table_field = 'ac3';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards3;
            $zielwert = $agent;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel3_1'];
            $text2 = $ov_lang['ac_ziel3_2'];
            break;
        case 3: //lagere sonden
            $ac_table_field = 'ac4';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards4;
            $zielwert = $sonde;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel4_1'];
            $text2 = $ov_lang['ac_ziel4_2'];
            break;
        case 4: //erfülle missionen
            $ac_table_field = 'ac5';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards5;

            $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT SUM(counter) AS zielwert FROM de_user_mission WHERE user_id=".$_SESSION['ums_user_id'].";");
            $row = mysqli_fetch_array($db_daten);
            $zielwert = $row['zielwert'];

            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel5_1'];
            $text2 = $ov_lang['ac_ziel5_2'];
            break;
        case 5: //erhalte kriegsartefakte
            $ac_table_field = 'ac6';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards6;
            $zielwert = $kartefakt;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel6_1'];
            $text2 = $ov_lang['ac_ziel6_2'];
            break;
        case 6: //artefakte im artefaktgeb�ude
            $ac_table_field = 'ac8';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards8;

            $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anzahl FROM de_user_artefact WHERE user_id = '".$_SESSION['ums_user_id']."'");
            $rowx = mysqli_fetch_array($db_datenx);
            $zielwert = $rowx['anzahl'];

            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel8_1'];
            $text2 = $ov_lang['ac_ziel8_2'];
            break;
        case 7: //artefakte in den basisschiffen
            $ac_table_field = 'ac9';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards9;
            //die anzahl der artefakte in den basisschiffen auslesen
            $zielwert = 0;
            //flottendaten auslesen
            $fid0 = $_SESSION['ums_user_id'].'-0';
            $fid1 = $_SESSION['ums_user_id'].'-1';
            $fid2 = $_SESSION['ums_user_id'].'-2';
            $fid3 = $_SESSION['ums_user_id'].'-3';
            $einheiten_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id=? OR user_id=? OR user_id=? OR user_id=? ORDER BY user_id ASC", [$fid0, $fid1, $fid2, $fid3]);
            while ($row = mysqli_fetch_array($einheiten_daten)) { //jeder gefundene datensatz wird geprueft
                if ($row["artlvl1"] > 0) {
                    $zielwert++;
                }
                if ($row["artlvl2"] > 0) {
                    $zielwert++;
                }
                if ($row["artlvl3"] > 0) {
                    $zielwert++;
                }
                if ($row["artlvl4"] > 0) {
                    $zielwert++;
                }
                if ($row["artlvl5"] > 0) {
                    $zielwert++;
                }
                if ($row["artlvl6"] > 0) {
                    $zielwert++;
                }
            }
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel9_1'];
            $text2 = $ov_lang['ac_ziel9_2'];
            break;
        case 8: //sektorspenden
            $ac_table_field = 'ac10';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards10;

            $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT ((spend01+spend02*2+spend03*3+spend04*4)/10+spend05*1000) AS wert FROM de_user_data WHERE user_id = '".$_SESSION['ums_user_id']."'");
            $rowx = mysqli_fetch_array($db_datenx);
            $zielwert = $rowx['wert'];


            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel10_1'];
            $text2 = $ov_lang['ac_ziel10_2'];
            break;
        case 9: //tronic der allianz spenden
            $ac_table_field = 'ac11';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards11;
            $zielwert = $ally_tronic;
            $do_calc = 1;
            //$text1=$ov_lang['ac_ziel11_1'];
            //$text2=$ov_lang['ac_ziel11_2'];
            $text1 = 'Erreiche den gew&uuml;nschten Tronic-Einzahlungsstatus bei Deiner Allianz';
            $text2 = 'Den Wert kannst Du unter Allianz -> Finanzen einsehen und dort Tronic spenden. Du erh&auml;ltst Tronic per Zufall, &uuml;ber Artefakte, durch Missionen und in der Auktion.';
            break;
        case 10: //kopfgeld erbeuten
            $ac_table_field = 'ac12';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards12;
            $zielwert = $kgget;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel12_1'];
            $text2 = $ov_lang['ac_ziel12_2'];
            break;
        case 11: //handelspunkte
            $ac_table_field = 'ac14';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards14;
            $zielwert = $tradescore;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel14_1'];
            $text2 = $ov_lang['ac_ziel14_2'];
            break;
        case 12: //sektorartefakthaltezeit
            $ac_table_field = 'ac15';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards15;

            $db_datenx = mysqli_execute_query($GLOBALS['dbi'], "SELECT arthold FROM de_sector WHERE sec_id = ?", [$sector]);
            $rowx = mysqli_fetch_array($db_datenx);
            //Sektor 0 (Übergang beim Anlegen) hat keinen Eintrag in de_sector
            $zielwert = $rowx['arthold'] ?? 0;

            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel15_1'];
            $text2 = $ov_lang['ac_ziel15_2'];
            break;
        case 13: //artefakte in npc-sektoren erobert
            $ac_table_field = 'ac16';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards16;
            $zielwert = $npcartefact;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel16_1'];
            $text2 = $ov_lang['ac_ziel16_2'];
            break;
        case 14: //vergessene Systeme erkunden
            $ac_table_field = 'ac17';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards17;
            $zielwert = $anz_vergessene_systeme_erforscht;
            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel17_1'];
            $text2 = $ov_lang['ac_ziel17_2'];
            break;
        case 15: //vergessene Systeme: Gebäude Stufe 5 und höher
            $ac_table_field = 'ac18';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards18;

            $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(user_id) AS anzahl FROM de_user_map_bldg WHERE bldg_level >= 5 AND user_id='".$_SESSION['ums_user_id']."';");
            $rowx = mysqli_fetch_array($db_datenx);
            $zielwert = $rowx['anzahl'];

            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel18_1'];
            $text2 = $ov_lang['ac_ziel18_2'];
            break;
        case 16: //vergessene Systeme: Gebäude Stufe 10
            $ac_table_field = 'ac19';
            $ac_akt = $ac_daten[$ac_table_field];
            $rewards = $rewards19;

            $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(user_id) AS anzahl FROM de_user_map_bldg WHERE bldg_level >= 10 AND user_id='".$_SESSION['ums_user_id']."';");
            $rowx = mysqli_fetch_array($db_datenx);
            $zielwert = $rowx['anzahl'];


            $do_calc = 1;
            $text1 = $ov_lang['ac_ziel19_1'];
            $text2 = $ov_lang['ac_ziel19_2'];
            break;
    }

    //�berpr�fen, ob man schon vorbedingungen erf�llt
    //Belohnung und Nachrichten erst nach dem gesicherten Hochsetzen der Stufe gutschreiben (siehe unten)
    $ac_akt_alt = $ac_akt;
    $ac_belohnung_neu = 0;
    $ac_news = array();

    if ($do_calc == 1) {
        if ($zielwert == '') {
            $zielwert = 0;
        }
        //wieviel errungenschaften m�glich sind berechnen
        $ac_max = calculate_ac_max(count($rewards));
        //echo count($rewards).'/a';
        if ($ac_max > 0) {
            for ($i = $ac_akt;$i < $ac_max;$i++) {
                if ($zielwert >= $rewards[$i][0]) {
                    //aktuellen level hinterlegen
                    $ac_akt++;

                    //echo $ac_akt.'->';
                    //echo $rewards[$i][0].' ';

                    $ac_belohnung_neu += $rewards[$i][1];
                    //nachricht f�r jede gutschrift hinterlegen
                    $time = date("YmdHis");
                    $news = $ov_lang['errungenschaftenbonus'].' ('.$text1.' - '.$ov_lang['stufe'].' '.$ac_akt.'): '.number_format($rewards[$i][1], 0, "", ".").' M';
                    $ac_news[] = array($time, $news);
                }
            }
        }
    }

    //Fortschritt und Tooltip
    $ac_prozent = 0;
    if ($do_calc == 1 and $ac_max > 0) {
        //Fortschritt bis zur höchsten bereits freigeschalteten Stufe
        $ac_prozent = $zielwert / $rewards[$ac_max - 1][0];
        if ($ac_prozent > 1) {
            $ac_prozent = 1;
        }

        //tooltip bauen
        $actip[$ac] = $text1.'&'.$text2.'<br>'.$ov_lang['zielwert'].': '.number_format($zielwert, 0, "", ".").'/'.
        number_format($rewards[$ac_max - 1][0], 0, "", ".").
        '<br><br>Stufe: Zielwert (Belohnung in M)';

        for ($a = 0;$a < count($rewards);$a++) {
            $font = '#ff6b6b';
            if ($ac_max > $a) {
                $font = '#ffb45c';
            }
            if ($ac_akt > $a) {
                $font = '#5fe08a';
            }
            $actip[$ac] .= '<br><font color='.$font.'>'.($a + 1).': '.number_format($rewards[$a][0], 0, "", ".").' ('.number_format($rewards[$a][1], 0, "", ".").')</font>';
        }
        $actip[$ac] .= '<br>Farblegende:<br><font color=#5fe08a>Gr&uuml;n: erledigt</font>, <font color=#ffb45c>Gelb: freigeschaltet, aber noch nicht erledigt</font>, <font color=#ff6b6b>Rot: wird automatisch zu einem sp&auml;teren Rundenzeitpunkt freigeschalten</font>';

    }

    //ac_akt nur hochsetzen, wenn die Stufe noch den alten Wert hat: so gibt es jede Stufe nur einmal,
    //auch wenn die Übersicht in zwei Sitzungen gleichzeitig geladen wird
    if ($ac_akt != $ac_akt_alt) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_achievement SET {$ac_table_field}=? WHERE user_id=? AND {$ac_table_field}=?", [$ac_akt, $_SESSION['ums_user_id'], $ac_akt_alt]);
        if (mysqli_affected_rows($GLOBALS['dbi']) == 1) {
            $ac_belohnung += $ac_belohnung_neu;
            foreach ($ac_news as $ac_news_entry) {
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, '60', ?, ?)", [$_SESSION['ums_user_id'], $ac_news_entry[0], $ac_news_entry[1]]);
            }
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$_SESSION['ums_user_id']]);
        }
    }

    //Zeile: erledigte/freigeschaltete Stufen, Aufgabe, Fortschrittsbalken; alle Stufen stehen im Tooltip
    if ($ac_max == 0) {
        $ac_zustand = ' ov-ziel-gesperrt';
        $ac_wert = 'noch nicht freigeschaltet';
    } else {
        $ac_zustand = ($ac_akt < $ac_max) ? ' ov-ziel-offen' : ' ov-ziel-fertig';
        $ac_wert = number_format($zielwert, 0, "", ".").' / '.number_format($rewards[$ac_max - 1][0], 0, "", ".");
    }

    $ac_zeilen .= '
        <div class="ov-ziel'.$ac_zustand.'"'.(($do_calc == 1 and $ac_max > 0) ? ' rel="tooltip" title="'.$actip[$ac].'"' : '').'>
            <div class="ov-ziel-stufe">'.$ac_akt.'/'.$ac_max.'</div>
            <div class="ov-ziel-text">
                <div class="ov-ziel-kopf"><span class="ov-ziel-name">'.$text1.'</span><span class="ov-ziel-wert">'.$ac_wert.'</span></div>
                <div class="mod-balken"><span style="width: '.round($ac_prozent * 100, 1).'%;"></span></div>
            </div>
        </div>';
}

//belohnungen mit einem mal in der db gutschreiben
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01 = restyp01 + ? WHERE user_id = ?", [$ac_belohnung, $_SESSION['ums_user_id']]);
//echo ($ac_belohnung);

rahmen_oben($ov_lang['errungenschaften']);
echo '<div class="ov mod">';
echo '<div class="ov-ziele">'.$ac_zeilen.'</div>';
echo '<div class="ov-legende">Stufe: erledigt / freigeschaltet. Weitere Stufen werden im Lauf der Runde freigeschaltet, Belohnungen automatisch gutgeschrieben. Alle Stufen zeigt der Tooltip.</div>';
echo '</div>';
rahmen_unten();
echo '<br>';

function calculate_ac_max($acs)
{
    global $ticks, $own_tick, $sv_winscore, $sv_ewige_runde, $sv_hardcore;

    if ($sv_ewige_runde == 1 || $sv_hardcore == 1) {//ewige runde
        $ticksegment = $sv_winscore / ($acs + 1);
        $ac_max = round($own_tick / $ticksegment);
        if ($ac_max > $acs) {
            $ac_max = $acs;
        }
    } else {//normale runde

        //zuerst test auf br
        if ($ticks < 2500000) {
            //keine br
            //ticksegment ist die zeit, nach der jeweils der nächste level möglich ist
            //$acs+1, damit das letzte level nicht erst im eh-kampf möglich ist
            $ticksegment = $sv_winscore / ($acs + 1);
            $ac_max = round($ticks / $ticksegment);
            if ($ac_max > $acs) {
                $ac_max = $acs;
            }
        } else {
            //es ist br
            $ac_max = $acs;
        }
    }

    return($ac_max);
}

?>

</body>
</html>