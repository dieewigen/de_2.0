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
<body>
<?php
/*
if($sv_server_tag=='SDE'){

    echo '
    <div class="info_box text2" style="margin-bottom: 5px; margin-top: 10px; font-size: 18px; margin-left: auto; margin-right: auto; font-weight: bold;">
    Die "Ewige Runde" hat einen eigenen Server erhalten, in der Accountverwaltung ist er unter "EDE" zu finden.
    </div>';

}

if($sv_server_tag=='EDE'){

    echo '
    <div class="info_box text2" style="margin-bottom: 5px; margin-top: 10px; font-size: 18px; margin-left: auto; margin-right: auto; font-weight: bold;">
    Der EDE-Server basiert auf einer duplizierten Datenbank von SDE vom 05.10.2015 15 Uhr.
    <br>Tickstart: 18:00 Uhr (05.10.2015)
    </div>';

}
*/

if ($sv_server_tag == 'DDE') {

    echo '
	<div class="info_box text2" style="margin-bottom: 5px; margin-top: 10px; font-size: 14px; margin-left: auto; margin-right: auto;">
	Der DDE-Server ist ein reiner Entwicklerserver und bietet keinen regul&auml;ren Spielbetrieb. 
	Hier werden komplett neue/einschneidende Sachen ausprobiert, die nicht im normalen Spielablauf getestet werden k&ouml;nnen. Es kann jederzeit gro&szlig;e &Auml;nderungen/Resets geben.
	Wer damit nicht klarkommen kann/will, sollte auf einem anderen Server spielen. Spieler welche die Entwicklung st&ouml;ren werden entfernt.
	Ansonsten sind alle Spieler gerne gesehen, die hier testen und ggf. die Zukunft von DE mitgestalten m&ouml;chten.
	</div>';

}

if (!isset($sv_comserver_roundtyp)) {
    $sv_comserver_roundtyp = 0;
}

//rohstoffe für die battleround auf communityservern
if ($sv_comserver == 1 and $sv_comserver_roundtyp == 1) {
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET tick=tick+2500000, sm_rboost=0, restyp01=restyp01+9000000000,
restyp02=restyp02+4500000000, restyp03=restyp03+1000000000, restyp04=restyp04+500000000, restyp05=restyp05+100000, col=col+10000 
WHERE user_id=? AND tick<1000000", [$ums_user_id]);
}

//logincounter zurücksetzen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET points = 0 WHERE user_id=?", [$ums_user_id]);

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, ehscore, tick, techs, sector, `system`, newtrans, newnews, allytag, col, col_build, agent, sonde, status, tradesystemscore, platz, rang, credits, actpoints, roundpoints, kartefakt, kgget, sou_user_id, geteacredits, geteftabonus, npcartefact, ally_tronic, eh_counter FROM de_user_data WHERE user_id=?", [$ums_user_id]);
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
$sou_user_id = $row["sou_user_id"];
$geteacredits = $row["geteacredits"];
$geteftabonus = $row["geteftabonus"];
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

if ($ums_rasse == 1) {
    $rasse = 'Ewiger';
} elseif ($ums_rasse == 2) {
    $rasse = 'Ishtar';
} elseif ($ums_rasse == 3) {
    $rasse = 'K&#180;Tharr';
} elseif ($ums_rasse == 4) {
    $rasse = 'Z&#180;tah-ara';
}

$sel_news = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_news_overview where typ=1 order by id desc Limit 0,5");
$det_news = '';
while ($rew = mysqli_fetch_array($sel_news)) {
    $t = $rew['time'];
    $time = $t[8].$t[9].'.'.$t[5].$t[6].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[11].$t[12].':'.$t[14].$t[15];

    $det_news .= '<a href="newspaper.php?id='.$rew['id'].'"><span class="text1">'.$time.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rew['betreff'].'</span></a><br>';
}

//stelle die ressourcenleiste dar
include "resline.php";

//test auf com-sperre
$akttime = date("Y-m-d H:i:s", time());
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT com_sperre FROM de_login WHERE user_id=?", [$ums_user_id]);
$row = mysqli_fetch_array($db_daten);
if ($row['com_sperre'] > $akttime) {
    $sperrtime = strtotime($row['com_sperre']);
    echo('<div class="info_box text2" style="margin-bottom: 5px; font-size: 14px;">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - H:i", $sperrtime).'</div>');
}



@$filetime = filemtime("cache/overview.inc.php");
$aktdate = date("d.m. H:i", $filetime);

//ausgabe der einzelnen positionen

//wenn sektor 1, dann info �ber startsektor

if ($sector == 1 and $sv_deactivate_sec1moveout == 0) {
    $text = '
	<table width="586" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td width="560" align="center" class="ro"><div class="cellu">'.$ov_lang['sek1welcome'].'</div></td>
	<td width="13" class="ror">&nbsp;</td>
	</tr>
	<tr>
	<td class="rl">&nbsp;</td>
	<td><div class="cell">'.$ov_lang['sek1info'].'</div></td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	</table>
	<table width="586" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="13" class="rul">&nbsp;</td>
	<td class="ru">&nbsp;</td>
	<td width="13" class="rur">&nbsp;</td>
	</tr>
	</table><br><br>';
    echo($text);
}

//erstmal schauen ob evtl. alle deaktiviert worden sind, dann werden nur die det-news angezeigt
if ($ovoptfelder[0] == 0 and $ovoptfelder[1] == 0 and $ovoptfelder[2] == 0 and $ovoptfelder[3] == 0 and $ovoptfelder[4] == 0 and $ovoptfelder[5] == 0 and $ovoptfelder[6] == 0) {
    $ovoptfelder[0] = 1;
}

$ovfirst = 1;

for ($j = 0;$j <= 6;$j++) {
    if (!isset($ovoptfelder[$j])) {
        $ovoptfelder[$j] = -1;
    }

    switch ($ovoptfelder[$j]) {
        case 0:
            //0 bedeutet inaktiv, mache also nichts
            break;
        case 1://det-meldung

            $ueberschrift = '
			<table width="586" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td width="13" height="37" class="rml">&nbsp;</td>
			<td width="560" align="center" class="ro"><div class="cellu">&Uuml;bersicht</div></td>
			<td width="13" class="rmr">&nbsp;</td>
			</tr>
			';

            ///////////////////////////////////////////////////////////////
            //die rundencounteranzeige erstellen - anfang
            ///////////////////////////////////////////////////////////////
            if ($sv_hardcore == 1) {

                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
                $row = mysqli_fetch_array($db_daten);
                $maxtick = $row['tick'];


                $rca = '<div class="fett text3">Das Ziel beim <a style="color: #00FF00; text-decoration: underline; font-size: 10pt;" href="http://forum.bgam.es/thread.php?threadid=23173" target="_blank">Hardcore-Rundenmodus</a> ist es als erster 5 Erhabenenteilsiege zu erreichen und somit zum vollwertigen ERHABENEN zu werden.</div>';

                //Top 3
                //die ersten drei Pl�tze anzeigen
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=0 AND sector > 1 AND (eh_siege>0 OR eh_counter>0) ORDER BY eh_siege DESC, eh_counter DESC LIMIT 3", []);
                $num = mysqli_num_rows($db_daten);
                if ($num > 0) {
                    $rca .= '<div style="width: 100%; height: 78px; margin-top: 8px;">';
                    $platzc = 1;
                    while ($row = mysqli_fetch_array($db_daten)) {
                        if ($row['status'] == 1 && !empty($row['allytag'])) {
                            $allianz = '<br>Allianz: '.$row['allytag'];
                        } else {
                            $allianz = '';
                        }

                        $rca .= '<div style="width: 31%; height: 100%; padding-top: 10px; border: 1px solid #FFFFFF; margin-right: 11px; float: left;">';
                        $rca .= '<span class="fett">Top '.$platzc.' - EH-Anw&auml;rter</span>'.
                                '<br>'.$row['spielername'].
                                $allianz.
                                '<br>EH-Teilsiege: '.$row['eh_siege'].'/'.$sv_hardcore_need_wins.
                                '<br>EH-Counter: '.$row['eh_counter'].'/'.$sv_eh_counter;

                        $rca .= '</div>';
                        $platzc++;
                    }
                    $rca .= '</div>';
                }


                $rca .= '<div style="width: 100%; height: 78px; margin-top: 24px; margin-bottom: 10px;">';
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

                            $rca .= '<div style="width: 31%; height: 100%; padding-top: 10px; border: 1px solid #FFFFFF; margin-right: 11px; float: left;">';
                            $rca .= '<span class="fett">EH-Counter l&auml;uft f&uuml;r</span>'.
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

                        $rca .= '<div style="width: 31%; height: 100%; padding-top: 10px; border: 1px solid #FFFFFF; margin-right: 11px; float: left;">';
                        $rca .= '<span class="fett">Deine Daten</span>'.
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
                //normale runde
                //gesamtbreite der inneren spalten
                $gessb = 520;
                //rundenstatus auslesen
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
                $row = mysqli_fetch_array($db_daten);
                if ($row["tick"] <= 0) {
                    $ticks = 1;
                } else {
                    $ticks = $row["tick"];
                }
                //$ticks=353333000;
                if ($ticks < 2500000 or $sv_comserver_roundtyp == 1) {
                    if ($sv_comserver_roundtyp == 1) {
                        $ticks -= 2500000;
                    }//fix für community-server in der BR
                    //wenn die ticks kleiner als die maximale tickzahl sind, dann l�uft die runde noch
                    if ($ticks < $sv_winscore) {
                        //spaltenbreite
                        //wieviel prozent der runde sind rum
                        $p = $ticks / $sv_winscore;
                        //echo $p;

                        $sb1 = round($gessb * $p);
                        $sb2 = round($gessb * (1 - $p));

                        //spaltenfarbe
                        $sc1 = 1;
                        $sc2 = 2;
                        if ($p > 0.33) {
                            $sc1 = 4;
                            $sc2 = 5;
                        }
                        if ($p > 0.66) {
                            $sc1 = 7;
                            $sc2 = 8;
                        }

                        $sc3 = 20;
                        $sc4 = 21;
                        $ttip = $ov_lang['rundenfortschritt'].': '.number_format($ticks, 0, "", ".").'/'.number_format($sv_winscore, 0, "", ".").
                        '<br>'.$ov_lang['rundenhalteticks'].': '.number_format($sv_benticks, 0, "", ".").
                        '<br><br>'.$ov_lang['rundeninfo1'].' '.$ov_lang['rundeninfo2'];

                    } else { //erhabenenkampf, oder die runde ist zu ende
                        $sb1 = round($gessb / 2);
                        $sb2 = round($gessb / 2);

                        //�berpr�fen ob der eh-kampf noch l�uft, oder ab es schon rum ist
                        $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT doetick, winid, winticks FROM de_system", []);
                        $row = mysqli_fetch_array($result);
                        $doetick = $row["doetick"];
                        $winticks = $row["winticks"];
                        $winid = $row["winid"];

                        if ($winticks <= 1 and $doetick == 0 and $winid > 0) {//rundenende
                            $sc1 = 13;
                            $sc2 = 14;
                            $sc3 = 14;
                            $sc4 = 15;
                            $ttip = $ov_lang['rundenende'].' '.$ov_lang['rundeninfo3'];
                        } else { //eh-kampf l�uft
                            //spaltenfarbe
                            $sc1 = 10;
                            $sc2 = 11;
                            $sc3 = 11;
                            $sc4 = 12;
                            $ttip = $ov_lang['rundenendeeh'].'<br>'.
                            $ov_lang['rundenhalteticks'].': '.number_format($sv_benticks, 0, "", ".").
                            '<br><br>'.$ov_lang['rundeninfo2'];
                        }
                    }
                } else { //battleround
                    $sb1 = round($gessb / 2);
                    $sb2 = round($gessb / 2);
                    $sc1 = 16;
                    $sc2 = 17;
                    $sc3 = 17;
                    $sc4 = 18;
                    $ttip = $ov_lang['battleround'].' '.$ov_lang['rundeninfo3'];
                }

                //tooltip bauen
                $atip = $ov_lang['rundenstatus'].'&'.$ttip;

                $rca = '<table width="531" border="0" cellpadding="0" cellspacing="0">';
                $rca .= '<tr height="9" title="'.$atip.'">';
                //linke grafik
                $rca .= '<td width="5" style="background-image:url('.$ums_gpfad.'g/rc'.$sc1.'.gif)"></td>';
                //mittelteil
                $rca .= '<td width="'.$sb1.'" style="background-image:url('.$ums_gpfad.'g/rc'.$sc2.'.gif); background-repeat: repeat-x;"></td>';
                $rca .= '<td width="'.$sb2.'" style="background-image:url('.$ums_gpfad.'g/rc'.$sc3.'.gif); background-repeat: repeat-x;"></td>';
                //rechte grafik
                $rca .= '<td width="6" style="background-image:url('.$ums_gpfad.'g/rc'.$sc4.'.gif)"></td>';
                $rca .= '</tr>';
                $rca .= '</table>';
            }
            ///////////////////////////////////////////////////////////////
            //die rundencounteranzeige erstellen - ende
            ///////////////////////////////////////////////////////////////

            echo($ueberschrift.'
       <tr>
       <td class="rl">&nbsp;</td>
	   <td align="center" class="text1 bgpic3">');

            //obere Buttons Hilfe/Discord/Umfragen
            echo('
		 <div style="display: flex; width: 100%; margin-bottom: 15px;">
			  <div style="flex-grow: 1;"><a href="'.$sv_link[2].'" target="_blank" class="btn">Hilfe</a></div>
			  <div style="flex-grow: 1;"><a href="vote.php?bar=yes" class="btn">Umfragen</a></div>
			  <div style="flex-grow: 1;"><a href="https://discord.gg/qBpCPx4" target="_blank" class="btn">DE-Discord</a></div>
		 </div>
	   ');



            if ($sv_comserver == 1) {
                $hs = '<div style="border: 1px solid #666666; background-image:url('.$ums_gpfad.'g/bgpic4.jpg); color: #FFFFFF; padding: 5px;">';

                $hs .= '<h1>Willkommen auf dem Community Server</h1>';
                //info
                $hs .= '<a href="optionscomserver.php?showhelp=1"><img id="comserv1" title="Community Server&Auf diesem Server bestimmen die Spieler die Regeln.<br><br>Klicke um mehr Informationen zu erhalten." src="'.$ums_gpfad.'g/symbol14.png" width="48px" height="48px"></a>';
                //server konfigurieren

                $hs .= '<a href="optionscomserver.php"><img id="comserv1" title="Community Server konfigurieren&Klicke um Deine Einstellungen zu w&auml;hlen."src="'.$ums_gpfad.'g/symbol15.png" width="48px" height="48px"></a>';
                $hs .= '</div><br>';

                echo($hs);
            }

            echo($rca.'<br>');
            if ($sv_comserver != 1) {
                echo('<a href="sinfo.php" target="h"><font color="lightgreen"> > > Informationen &uuml;ber den Server < < </a></font><br><br>');
            }

            echo('
		  <b style="font-size:14px;">'.$ov_lang['detkristueber'].' [<a href="newspaper.php?action=archiv&typ=1">'.$ov_lang['archiv'].'</a>]</b>
		  	<div align="left">
				<table border="0">
					<tr><td width="30">&nbsp;</td><td>'.$det_news.'</td></tr>
				</table>
			</div>');

            echo('
	   </td>
       <td width="13" class="rr">&nbsp;</td>
       </tr>
       </table>
       ');
            break;
        case 2://b-news (entfernt, da zusammen mit det-news in einem container
            break;
        case 3://faq
            break;
        case 4://meldung vom sk
            break;
        case 5://systemübersicht
            if ($ovfirst == 1) {
                $ovfirst = 0;
                $ueberschrift = '
         <table width="586" border="0" cellpadding="0" cellspacing="0">
         <tr>
         <td width="13" height="37" class="rol">&nbsp;</td>
         <td align="center" class="ro"><div class="cellu">'.$ov_lang['systemuebersicht'].'</div></td>
         <td width="13" class="ror">&nbsp;</td>
         </tr>
         ';
            } else {
                $ueberschrift = '
         <table width="586" border="0" cellpadding="0" cellspacing="0">
         <tr>
         <td width="13" height="37" class="rml">&nbsp;</td>
         <td align="center" class="ro"><div class="cellu">'.$ov_lang['systemuebersicht'].'</div></td>
         <td width="13" class="rmr">&nbsp;</td>
         </tr>
         ';
            }
            echo($ueberschrift.'<tr>
       <td width="13" class="rl">&nbsp;</td><td>
       
       <table width="100%" border="0" cellpadding="0" cellspacing="1">
       <tr align="left">
       <td width="140" class="cell">'.$ov_lang['accountid'].'</td>
       <td width="140" class="cell" align="center">'.$sv_server_tag.$ums_user_id.'</td>
       <td width="140" class="cell1">'.$ov_lang['rundenpunkte'].'</td>
       <td width="140" class="cell1" align="center">'.$rundenpunkte.'</td>
	   </tr>
       <tr align="left">
       <td class="cell">'.$ov_lang['name'].'</td>
       <td class="cell" align="center">'.$ums_spielername.'</td>
	   <td class="cell1">'.$ov_lang['allianz'].'</td>
       <td class="cell1" align="center">'.$allytag.'</td>
	   </tr>
       <tr align="left">
       <td class="cell">'.$ov_lang['sys'].'</td>
       <td class="cell" align="center">'.$sector.':'.$system.'</td>
	   <td class="cell1">'.$ov_lang['kollektoren'].'</td>
       <td class="cell1" align="center">'.number_format($col, 0, "", ".").'</td>
       </tr>
       <tr align="left">
       <td class="cell">'.$ov_lang['rang'].'</td>
       <td class="cell" align="center">'.$rang.'</td>
	   <td class="cell1">'.$ov_lang['sonden'].'</td>
       <td class="cell1" align="center">'.number_format($sonde, 0, "", ".").'</td>
       </tr>
       <tr align="left">
       <td class="cell">'.$ov_lang['platz'].'</td>
       <td class="cell" align="center">'.number_format($platz, 0, "", ".").'</td>
	   <td class="cell1">'.$ov_lang['agenten'].'</td>
       <td class="cell1" align="center">'.number_format($agent, 0, "", ".").'</td>
       </tr>
       <tr align="left">
       <td class="cell">'.$ov_lang['woche'].'</td>
       <td class="cell" align="center">'.number_format($own_tick, 0, "", ".").'</td>
	   <td class="cell1">'.$ov_lang['handelspunkte'].'</td>
       <td class="cell1" align="center">'.number_format($tradescore, 0, "", ".").'</td>
       </tr>

       <tr align="left">
       <td class="cell">'.$ov_lang['rasse'].'</td>
       <td class="cell" align="center">'.$rasse.'</td>
	   <td class="cell1">'.$ov_lang['galaxie'].'</td>
       <td class="cell1" align="center">'.$sv_server_name.'</td>
       </tr>
       
       </table>
       ');
            echo('</td><td width="13" class="rr">&nbsp;</td>
       </tr>
       </table>
       ');
            break;
        case 6://schiffseinheiten
            break;
        case 7://verteidigungsanlagen
            break;
        case 8://schiffseinheiten/verteidigungsanlagen
            if ($ovfirst == 1) {
                $ovfirst = 0;
                $ueberschrift = '
		   <table width="586" border="0" cellpadding="0" cellspacing="0">
		   <tr>
		   <td width="13" height="37" class="rol">&nbsp;</td>
		   <td width="280" align="center" class="ro"><div class="cellu">'.$ov_lang['schiffseinheiten'].'</div></td>
		   <td width="280" align="center" class="ro"><div class="cellu">'.$ov_lang['verteidigungsanlagen'].'</div></td>
		   <td width="13" class="ror">&nbsp;</td>
		   </tr>
		   ';
            } else {
                $ueberschrift = '
		   <table width="586" border="0" cellpadding="0" cellspacing="0">
		   <tr>
		   <td width="13" height="37" class="rml">&nbsp;</td>
		   <td width="280" align="center" class="ro"><div class="cellu">'.$ov_lang['schiffseinheiten'].'</div></td>
		   <td width="280" align="center" class="ro"><div class="cellu">'.$ov_lang['verteidigungsanlagen'].'</div></td>
		   <td width="13" class="rmr">&nbsp;</td>
		   </tr>
		   ';
            }
            echo($ueberschrift.'');
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
            $fid0 = $ums_user_id.'-0';
            $fid1 = $ums_user_id.'-1';
            $fid2 = $ums_user_id.'-2';
            $fid3 = $ums_user_id.'-3';
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
                $schiffe[$ik][0] = getTechNameByRasse($row["tech_name"], $ums_rasse);
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

            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=?", [$ums_user_id]);
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
                $defense[$ik][0] = getTechNameByRasse($row["tech_name"], $ums_rasse);
                $defense[$ik][1] = number_format($ec[$row["tech_id"]], 0, "", ".");
                $ik++;
            }

            //jetzt die daten komprimiert ausgeben
            echo('<tr align="center" height="25">');
            echo('<td width="13" class="rl">&nbsp;</td><td colspan=2>');

            $max = count($schiffe);
            if (count($defense) > $max) {
                $max = count($defense);
            }

            //2 spalten für schiffe/verteidigungsanlagen
            echo('<div class="cell"><table width="100%" border="0" cellpadding="0" cellspacing="0">');
            echo('<tr><td width="50%"><div class="bgpic1" style="height: 150px;">

		<table border="0" cellpadding="0" cellspacing="1">');
            for ($jk = 0;$jk < $max;$jk++) {
                if ($jk == 0) {
                    $w1 = ' width="200"';
                    $w2 = ' width="80"';
                } else {
                    $w1 = '';
                    $w2 = '';
                }

                if (isset($schiffe[$jk][0])) {
                    $out[0] = $schiffe[$jk][0];
                } else {
                    $out[0] = '';
                }

                if (isset($schiffe[$jk][1])) {
                    $out[1] = $schiffe[$jk][1];
                } else {
                    $out[1] = '';
                }

                if (isset($defense[$jk][0])) {
                    $out[2] = $defense[$jk][0];
                } else {
                    $out[2] = '';
                }

                if (isset($defense[$jk][1])) {
                    $out[3] = $defense[$jk][1];
                } else {
                    $out[3] = '';
                }

                if ($out[0] == '') {
                    $out[0] = '&nbsp;';
                }
                if ($out[1] == '') {
                    $out[1] = '&nbsp;';
                }
                if ($out[2] == '') {
                    $out[2] = '&nbsp;';
                }
                if ($out[3] == '') {
                    $out[3] = '&nbsp;';
                }

                echo('<tr>');
                echo('<td'.$w1.' class="text1">'.$out[0]."</td>");
                echo('<td'.$w2.' class="text1" align="center">'.$out[1]."</td>");
                echo('</tr>');
            }

            // die 2 spalten folgen hier aufeinander
            echo('</table></div></td><td width="50%"><div class="bgpic2">');

            echo('<table border="0" cellpadding="0" cellspacing="1">');

            for ($jk = 0;$jk < $max;$jk++) {
                if ($jk == 0) {
                    $w1 = ' width="200"';
                    $w2 = ' width="80"';
                } else {
                    $w1 = '';
                    $w2 = '';
                }

                if (isset($schiffe[$jk][0])) {
                    $out[0] = $schiffe[$jk][0];
                } else {
                    $out[0] = '';
                }

                if (isset($schiffe[$jk][1])) {
                    $out[1] = $schiffe[$jk][1];
                } else {
                    $out[1] = '';
                }

                if (isset($defense[$jk][0])) {
                    $out[2] = $defense[$jk][0];
                } else {
                    $out[2] = '';
                }

                if (isset($defense[$jk][1])) {
                    $out[3] = $defense[$jk][1];
                } else {
                    $out[3] = '';
                }

                if ($out[0] == '') {
                    $out[0] = '&nbsp;';
                }
                if ($out[1] == '') {
                    $out[1] = '&nbsp;';
                }
                if ($out[2] == '') {
                    $out[2] = '&nbsp;';
                }
                if ($out[3] == '') {
                    $out[3] = '&nbsp;';
                }

                echo('<tr>');
                //echo ('<td'.$w1.' class="cell">'.$out[0]."</td>");
                //echo ('<td'.$w2.' class="cell" align="center">'.$out[1]."</td>");
                echo('<td'.$w1.' class="text1">'.$out[2]."</td>");
                echo('<td'.$w2.' class="text1" align="center">'.$out[3]."</td>");
                echo('</tr>');
            }
            echo('</table></div></td></tr></table>');

            //tabelle schlie�en
            echo('</td><td width="13" class="rr">&nbsp;</td>');
            echo('</tr>');
            echo('</table>');

            break;

        case 9://errungenschaften
            if ($ovfirst == 1) {
                $ovfirst = 0;
                $ueberschrift = '
         <table width="586" border="0" cellpadding="0" cellspacing="0">
         <tr>
         <td width="13" height="37" class="rol">&nbsp;</td>
		 <td width="560" align="center" class="ro"><div class="cellu">'.$ov_lang['errungenschaften'].'</div></td>
         <td width="13" class="ror">&nbsp;</td>
         </tr>
         ';
            } else {
                $ueberschrift = '
         <table width="586" border="0" cellpadding="0" cellspacing="0">
         <tr>
         <td width="13" height="37" class="rml">&nbsp;</td>
         <td width="560" align="center" class="ro"><div class="cellu">'.$ov_lang['errungenschaften'].'</div></td>
         <td width="13" class="rmr">&nbsp;</td>
         </tr>
         ';
            }
            echo($ueberschrift.'');

            echo('<tr align="center" height="25">');
            echo('<td width="13" class="rl">&nbsp;</td><td>');

            echo('<table border="0" cellpadding="0" cellspacing="1">');
            //tabellen�berschrift ausgeben
            echo('<tr align="center"><td width="50" class="cell">'.$ov_lang['stufe'].'</td><td width="510" class="cell">'.$ov_lang['aufgabe'].'</td></tr>');

            //alle errungenschaften auslesen
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_achievement WHERE user_id = ?", [$ums_user_id]);
            $num = mysqli_num_rows($db_daten);
            //wenn es noch keinen datensatz gibt, diesen anlegen
            if ($num == 0) {
                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_achievement (user_id) VALUES (?)", [$ums_user_id]);
                //jetzt nochmal auslesen
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_achievement WHERE user_id = ?", [$ums_user_id]);
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
                //der letzte eintrag ist immer die zusammenrechnung aller aufgaben
                //if($ac==$achievement_anz-1)$ac=999;

                //deaktivierte spielelemente auslassen
                //efta jetzt immer entfernen
                //if($sv_deactivate_efta==1){
                if ($ac == 6) {
                    $ac++;
                }
                //}
                //kopfgeld killen
                if ($ac == 11 && $sv_oscar == 1) {
                    $ac++;
                }

                //EA Bonus
                //if($sv_deactivate_sou==1){
                if ($ac == 12) {
                    $ac++;
                }
                //}

                //handelsaufgabe killen
                if ($sv_deactivate_trade == 1) {
                    if ($ac == 13) {
                        $ac++;
                    }
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
                        $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT SUM(colanz) AS anzahl FROM de_user_getcol WHERE user_id = '$ums_user_id' AND colanz>0");
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
                    case 6: //efta boni erhalten
                        $ac_table_field = 'ac7';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards7;
                        $zielwert = $geteftabonus;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel7_1'];
                        $text2 = $ov_lang['ac_ziel7_2'];
                        break;
                    case 7: //artefakte im artefaktgeb�ude
                        $ac_table_field = 'ac8';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards8;

                        $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(*) AS anzahl FROM de_user_artefact WHERE user_id = '$ums_user_id'");
                        $rowx = mysqli_fetch_array($db_datenx);
                        $zielwert = $rowx['anzahl'];

                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel8_1'];
                        $text2 = $ov_lang['ac_ziel8_2'];
                        break;
                    case 8: //artefakte in den basisschiffen
                        $ac_table_field = 'ac9';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards9;
                        //die anzahl der artefakte in den basisschiffen auslesen
                        $zielwert = 0;
                        //flottendaten auslesen
                        $fid0 = $ums_user_id.'-0';
                        $fid1 = $ums_user_id.'-1';
                        $fid2 = $ums_user_id.'-2';
                        $fid3 = $ums_user_id.'-3';
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
                    case 9: //sektorspenden
                        $ac_table_field = 'ac10';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards10;

                        $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT ((spend01+spend02*2+spend03*3+spend04*4)/10+spend05*1000) AS wert FROM de_user_data WHERE user_id = '$ums_user_id'");
                        $rowx = mysqli_fetch_array($db_datenx);
                        $zielwert = $rowx['wert'];


                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel10_1'];
                        $text2 = $ov_lang['ac_ziel10_2'];
                        break;
                    case 10: //tronic der allianz spenden
                        $ac_table_field = 'ac11';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards11;
                        $zielwert = $ally_tronic;
                        $do_calc = 1;
                        //$text1=$ov_lang['ac_ziel11_1'];
                        //$text2=$ov_lang['ac_ziel11_2'];
                        $text1 = 'Erreiche den gew&uuml;nschten Tronic-Einzahlungsstatus bei Deiner Allianz';
                        $text2 = 'Den Wert kannst Du unter unter Allianz -> Finanzen einsehen und dort Tronic spenden. Du erh&auml;ltst Tronic per Zufall, &uuml;ber Artefakte, durch Missionen und im Handel.';
                        break;
                    case 11: //kopfgeld erbeuten
                        $ac_table_field = 'ac12';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards12;
                        $zielwert = $kgget;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel12_1'];
                        $text2 = $ov_lang['ac_ziel12_2'];
                        break;
                    case 12: //ea aktivit�t
                        $ac_table_field = 'ac13';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards13;
                        $zielwert = $geteacredits;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel13_1'];
                        $text2 = $ov_lang['ac_ziel13_2'];
                        break;
                    case 13: //handelspunkte
                        $ac_table_field = 'ac14';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards14;
                        $zielwert = $tradescore;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel14_1'];
                        $text2 = $ov_lang['ac_ziel14_2'];
                        break;
                    case 14: //sektorartefakthaltezeit
                        $ac_table_field = 'ac15';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards15;

                        $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT arthold FROM de_sector WHERE sec_id = '$sector'");
                        $rowx = mysqli_fetch_array($db_datenx);
                        $zielwert = $rowx['arthold'];

                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel15_1'];
                        $text2 = $ov_lang['ac_ziel15_2'];
                        break;
                    case 15: //artefakte in npc-sektoren erobert
                        $ac_table_field = 'ac16';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards16;
                        $zielwert = $npcartefact;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel16_1'];
                        $text2 = $ov_lang['ac_ziel16_2'];
                        break;
                    case 16: //vergessene Systeme erkunden
                        $ac_table_field = 'ac17';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards17;
                        $zielwert = $anz_vergessene_systeme_erforscht;
                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel17_1'];
                        $text2 = $ov_lang['ac_ziel17_2'];
                        break;
                    case 17: //vergessene Systeme: Gebäude Stufe 5 und höher
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
                    case 18: //vergessene Systeme: Gebäude Stufe 10
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
                    case 19: //Werbe neue Spieler
                        $ac_table_field = 'ac20';
                        $ac_akt = $ac_daten[$ac_table_field];
                        $rewards = $rewards20;

                        $db_datenx = mysqli_query($GLOBALS['dbi'], "SELECT COUNT(user_id) AS anzahl FROM de_user_data WHERE sector>1 AND werberid='".$_SESSION['ums_owner_id']."';");
                        $rowx = mysqli_fetch_array($db_datenx);
                        $zielwert = $rowx['anzahl'];

                        $do_calc = 1;
                        $text1 = $ov_lang['ac_ziel20_1'];
                        $text2 = $ov_lang['ac_ziel20_2'];
                        break;
                }

                //�berpr�fen, ob man schon vorbedingungen erf�llt
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

                                $ac_belohnung += $rewards[$i][1];
                                //nachricht f�r jede gutschrift hinterlegen
                                $time = strftime("%Y%m%d%H%M%S");
                                $news = $ov_lang['errungenschaftenbonus'].' ('.$text1.' - '.$ov_lang['stufe'].' '.$ac_akt.'): '.number_format($rewards[$i][1], 0, "", ".").' M';
                                mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, '60', ?, ?)", [$ums_user_id, $time, $news]);
                                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$ums_user_id]);
                            }
                        }
                    }
                }

                //grafischen fortschrittsbalken/tooltip erzeugen
                if ($do_calc == 1 and $ac_max > 0) {
                    $gessb = 500;

                    //spaltenbreite
                    //wieviel prozent hat man schon
                    //$p=$ticks/$sv_winscore;
                    $p = $zielwert / $rewards[$ac_max - 1][0];
                    if ($p > 1) {
                        $p = 1;
                    }

                    $sb1 = round($gessb * $p);
                    $sb2 = round($gessb * (1 - $p));

                    //spaltenfarbe
                    $sc1 = 10;
                    $sc2 = 11;

                    $sc3 = 20;
                    $sc4 = 21;

                    if ($p > 0.33) {
                        $sc1 = 7;
                        $sc2 = 8;
                    }
                    if ($p > 0.66) {
                        $sc1 = 4;
                        $sc2 = 5;
                    }
                    if ($p >= 1) {
                        $sc1 = 1;
                        $sc2 = 2;
                        $sc3 = 2;
                        $sc4 = 3;
                    }

                    $rca = '<table width="500" border="0" cellpadding="0" cellspacing="0">';
                    $rca .= '<tr height="9">';
                    //linke grafik
                    $rca .= '<td width="5" style="background-image:url('.$ums_gpfad.'g/rc'.$sc1.'.gif)"></td>';
                    //mittelteil
                    $rca .= '<td width="'.$sb1.'" style="background-image:url('.$ums_gpfad.'g/rc'.$sc2.'.gif); background-repeat: repeat-x;"></td>';
                    $rca .= '<td width="'.$sb2.'" style="background-image:url('.$ums_gpfad.'g/rc'.$sc3.'.gif); background-repeat: repeat-x;"></td>';
                    //rechte grafik
                    $rca .= '<td width="6" style="background-image:url('.$ums_gpfad.'g/rc'.$sc4.'.gif)"></td>';
                    $rca .= '</tr>';
                    $rca .= '</table>';


                    //tooltip bauen
                    $actip[$ac] = $text1.'&'.$text2.'<br>'.$ov_lang['zielwert'].': '.number_format($zielwert, 0, "", ".").'/'.
                    number_format($rewards[$ac_max - 1][0], 0, "", ".").
                    '<br><br>Stufe: Zielwert (Belohnung in M)';

                    for ($a = 0;$a < count($rewards);$a++) {
                        $font = '#FF0000';
                        if ($ac_max > $a) {
                            $font = '#FFFF00';
                        }
                        if ($ac_akt > $a) {
                            $font = '#00FF00';
                        }
                        $actip[$ac] .= '<br><font color='.$font.'>'.($a + 1).': '.number_format($rewards[$a][0], 0, "", ".").' ('.number_format($rewards[$a][1], 0, "", ".").')</font>';
                    }
                    $actip[$ac] .= '<br>Farblegende:<br><font color=#00FF00>Gr&uuml;n: erledigt</font>, <font color=#FFFF00>Gelb: freigeschaltet, aber noch nicht erledigt</font>, <font color=#FF0000>Rot: wird automatisch zu einem sp&auml;teren Rundenzeitpunkt freigeschalten</font>';

                }

                if ($ac_max == 0) {
                    $rca = '-';
                }
                //ac_akt updaten
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_achievement SET {$ac_table_field}=? WHERE user_id=?", [$ac_akt, $ums_user_id]);

                //ziele ausgeben ausgeben
                if ($c1 == 0) {
                    $c1 = 1;
                    $bg = 'cell1';
                } else {
                    $c1 = 0;
                    $bg = 'cell';
                }
                //farbe f�r den fortschritt
                $str1 = '</font>';
                if ($ac_akt < $ac_max) {
                    $str = '<font color="#FF0000">';
                } else {
                    $str = '<font color="#00FF00">';
                }

                if ($do_calc == 1 and $ac_max > 0) {
                    echo('<tr align="center" title="'.$actip[$ac].'">
          <td class="'.$bg.'">'.$str.$ac_akt.'/'.$ac_max.$str1.'</td><td class="'.$bg.'">'.$text1.' ('.number_format($zielwert, 0, "", ".").'/'.number_format($rewards[$ac_max - 1][0], 0, "", ".").')'.$rca.'</td>
          </tr>');
                } else {
                    if ($ac_max > 0) {
                        echo('<tr align="center">
          <td class="'.$bg.'">'.$str.$ac_akt.'/'.$ac_max.$str1.'</td><td class="'.$bg.'">'.$text1.' ('.number_format($zielwert, 0, "", ".").'/'.number_format($rewards[$ac_max - 1][0], 0, "", ".").')'.$rca.'</td>
          </tr>');
                    } else {
                        echo('<tr align="center">
          <td class="'.$bg.'">'.$str.$ac_akt.'/'.$ac_max.$str1.'</td><td class="'.$bg.'">'.$text1.'</td>
          </tr>');
                    }

                }
            }

            //ziele ausgeben
            echo($output);

            //belohnungen mit einem mal in der db gutschreiben
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01 = restyp01 + ? WHERE user_id = ?", [$ac_belohnung, $ums_user_id]);
            //echo ($ac_belohnung);

            echo('</table>');

            //tabelle schlie�en
            echo('</td><td width="13" class="rr">&nbsp;</td>');
            echo('</tr>');
            echo('</table>');

            break;

        default:
            //$errmsg.='<br>Fehler bei den Daten.';
            break;
    }
}

//fussleiste ausgeben
echo('
<table width="586" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table><br><br>');

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
            //ticksegment ist die zeit, nach der jeweils der n�chste level m�glich ist
            //$acs+1, damit das letzte level nicht erst im eh-kampf m�glich ist
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
<?php include "fooban.php";?>
</body>
</html>
