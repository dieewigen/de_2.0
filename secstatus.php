<?php

use DieEwigen\DE2\View\RealTime;

$GLOBALS['deactivate_old_design'] = true;
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_secstatus.lang.php";
include "functions.php";
include "tickler/kt_einheitendaten.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, secstatdisable, status, allytag FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row["restyp01"];
$restyp02 = $row["restyp02"];
$restyp03 = $row["restyp03"];
$restyp04 = $row["restyp04"];
$restyp05 = $row["restyp05"];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$allytag = $row["allytag"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$secstatdisable = $row["secstatdisable"];

if ($row["status"] == 1) {
    $ownally = $row["allytag"];
} else {
    $ownally = '';
}

if (!isset($sv_hide_fp_in_secstatus)) {
    $sv_hide_fp_in_secstatus = 0;
}

//----------- Ally Feinde/Freunde
$allypartner = array();
$allyfeinde = array();
$query = "SELECT id FROM de_allys WHERE allytag='$ownally'";
$allyresult = mysqli_execute_query($GLOBALS['dbi'], $query);
$at = mysqli_num_rows($allyresult);
if ($at != 0) {
    $row_ally = mysqli_fetch_assoc($allyresult);
    $allyid = $row_ally["id"];

    $allyresult = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_partner, de_allys WHERE (ally_id_1=? OR ally_id_2=?) AND (ally_id_1=id OR ally_id_2=id)", [$allyid, $allyid]);
    while ($row = mysqli_fetch_assoc($allyresult)) {
        if ($ownally != $row["allytag"]) {
            $allypartner[] = $row["allytag"];
        }
    }

    $allyresult = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_war, de_allys WHERE (ally_id_angreifer=? OR ally_id_angegriffener=?) AND (ally_id_angreifer=id OR ally_id_angegriffener=id)", [$allyid, $allyid]);
    while ($row = mysqli_fetch_assoc($allyresult)) {
        if ($ownally != $row["allytag"]) {
            $allyfeinde[] = $row["allytag"];
        }
    }
}
//------------

//die länge eines KT in Minuten berechnen
//tickgeschwindigkeit auslesen
$filename = "tickler/runtick.sh";
$cachefile = fopen($filename, 'r');
$wticks = trim(fgets($cachefile, 1024));
$kticks = trim(fgets($cachefile, 1024));
$anzkticksprostunde = 0;
for ($i = 1;$i <= 60;$i++) {
    if ($kticks[$i] == 1) {
        $anzkticksprostunde++;
    }
}

//Missionsende checken
checkMissionEnd();

//beim aufruf der seite alle sichtbaren flotten automatisch für den gesamten sektor sichtbar machen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET entdecktsec = 1 WHERE zielsec=? AND zielsys=? AND entdeckt=1 AND entdecktsec=0", [$sector, $system]);

//Rassenbild
function ss_rasse($rasse_id)
{
    $bilder = array(1 => array('raceE.png', 'Die Ewigen'), 2 => array('raceI.png', 'Ishtar'), 3 => array('raceK.png', 'K&#180;Tharr'), 4 => array('raceZ.png', 'Z&#180;tah-ara'), 5 => array('raceD.png', 'DX61a23'));
    if (!isset($bilder[$rasse_id])) {
        return '';
    }
    return '<img src="gp/g/r/'.$bilder[$rasse_id][0].'" title="'.$bilder[$rasse_id][1].'" width="16" height="16" alt="">';
}

//Sonde, Agenten, Flotte und Hyperfunk für ein System
function ss_links($sec, $sys)
{
    return '<span class="ss-links"><a href="secret.php?a=s&amp;zsec1='.$sec.'&amp;zsys1='.$sys.'" title="Sonde">S</a><a href="secret.php?a=a&amp;zsec2='.$sec.'&amp;zsys2='.$sys.'" title="Agenteneinsatz">A</a><a href="military.php?se='.$sec.'&amp;sy='.$sys.'" title="Flotte">F</a><a href="details.php?se='.$sec.'&amp;sy='.$sys.'" title="Hyperfunk">H</a></span>';
}

//Auftrag als Chip; $cl ist die bisherige Einfärbung: ccr Angriff, ccg Verteidigung im Anflug, ccy verteidigt vor Ort, cc Rückflug/Mission
function ss_auftrag($cl, $as1, $at1)
{
    global $ss_lang;
    if ($cl == 'ccr') {
        return '<span class="mil-status mil-status-angriff">'.$ss_lang['angriff'].'</span>';
    }
    if ($cl == 'ccg') {
        return '<span class="mil-status mil-status-verteidigung">'.$ss_lang['verteidigung'].'</span><small>bleibt '.$at1.' KT</small>';
    }
    if ($cl == 'ccy') {
        return '<span class="mil-status mil-status-verteidigung">verteidigt</span>';
    }
    if ($as1 == 4) {
        return '<span class="mil-status mil-status-mission">'.$ss_lang['archaeologie'].'</span>';
    }
    return '<span class="mil-status mil-status-rueckflug">'.$ss_lang['rueckflug'].'</span>';
}


//Zeile einer anfliegenden Flotte (eigener Sektor und Allianzmitglieder)
function ss_flottenzeile($hsec, $hsys, $rasse, $allytagscan, $cl, $as1, $at1, $t1, $ge, $fp)
{
    global $sv_hide_fp_in_secstatus;
    $html = '<div class="ss-zeile ss-flotte">';
    $html .= '<span class="ss-herkunft"><b>'.$hsec.':'.$hsys.'</b></span>';
    $html .= '<span class="ss-rasse">'.$rasse.'</span>';
    //Allianztags sind in der DB UTF-8, utf8_encode_fix würde sie doppelt kodieren (aus Z² wurde ZÂ²)
    $html .= '<span class="ss-ally">'.$allytagscan.'</span>';
    $html .= '<span class="ss-auftrag">'.ss_auftrag($cl, $as1, $at1).'</span>';
    $html .= '<span class="ss-zahl">'.$t1.' KT</span>';
    $html .= '<span class="ss-zahl">'.number_format($ge, 0, "", ".").'</span>';
    if ($sv_hide_fp_in_secstatus != 1) {
        $html .= '<span class="ss-zahl" title="'.number_format($fp, 0, "", ".").'">'.formatMasseinheit($fp, 2).'</span>';
    } else {
        $html .= '<span class="ss-zahl">N/A</span>';
    }
    $html .= ss_links($hsec, $hsys);
    $html .= '</div>';
    return $html;
}

//Kopfzeile der Flottenliste
function ss_flottenkopf()
{
    global $ss_lang;
    return '<div class="ss-zeile ss-flotte ss-kopfzeile"><span>'.$ss_lang['herkunft'].'</span><span></span><span>'.$ss_lang['allianz'].'</span><span>'.$ss_lang['status'].'</span><span>'.$ss_lang['zeit'].'</span><span>'.$ss_lang['schiffe'].'</span><span title="Flottenpunkte. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet.">FP</span><span></span></div>';
}

//ETA-Übersicht eines Systems: Schiffe und Flottenpunkte je Kampftick, Balken für das Verhältnis
function ss_etazeile($j, $inc, $def, $fp_atter, $fp_deffer, $angreifer, $verteidiger)
{
    $gesamt = $fp_atter + $fp_deffer;
    $anteil = $gesamt > 0 ? round($fp_atter * 100 / $gesamt, 1) : 0;
    $html = '<div class="ss-zeile ss-eta">';
    $html .= '<span class="ss-zahl"><b>'.$j.'</b></span>';
    $html .= '<span class="ss-zahl ss-rot">'.number_format($inc, 0, "", ".").'</span>';
    $html .= '<span class="ss-zahl ss-gruen">'.number_format($def, 0, "", ".").'</span>';
    $html .= '<span class="ss-kraft"><span class="ss-kraft-werte"><span class="ss-rot" title="'.number_format($fp_atter, 0, "", ".").'">'.formatMasseinheit($fp_atter, 2).'</span><span class="ss-gruen" title="'.number_format($fp_deffer, 0, "", ".").'">'.formatMasseinheit($fp_deffer, 2).'</span></span>';
    if ($gesamt > 0) {
        $html .= '<span class="ss-anteil"><i style="width: '.$anteil.'%;"></i></span>';
    }
    $html .= '</span>';
    $html .= '<span class="ss-liste ss-rot">'.$angreifer.'</span>';
    $html .= '<span class="ss-liste ss-gruen">'.$verteidiger.'</span>';
    $html .= '</div>';
    return $html;
}

function ss_etakopf()
{
    global $ss_lang;
    return '<div class="ss-zeile ss-eta ss-kopfzeile"><span>'.$ss_lang['eta'].'</span><span>'.$ss_lang['inc'].'</span><span>'.$ss_lang['def'].'</span><span title="Flottenpunkte Angreifer / Verteidiger. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet.">FP Angr. / Vert.</span><span>'.$ss_lang['angreifer'].'</span><span>'.$ss_lang['verteidiger'].'</span></div>';
}

//Text/WhatsApp für ein System; deirc() ersetzt diesen Bereich durch das Textfeld
function ss_export($sector, $sys, $jsirc1, $jsirc2, $jsirc3)
{
    global $ss_lang;
    $html = '<div class="ss-export" id="s'.$sector.'_'.$sys.'">';
    $html .= "<button type=\"button\" class=\"mod-btn mod-btn-leise ally-btn-klein\" onclick=\"deirc(1,$sector,$sys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\">Als ".$ss_lang['text']."</button>";
    $html .= "<button type=\"button\" class=\"mod-btn mod-btn-leise ally-btn-klein\" onclick=\"deirc(3,$sector,$sys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\">WhatsApp</button>";
    $html .= '<label class="ss-haken"><input type="checkbox" name="a'.$sector.'_'.$sys.'" checked>'.$ss_lang['angreiferanzeigen'].'</label>';
    $html .= '<label class="ss-haken"><input type="checkbox" name="d'.$sector.'_'.$sys.'">Verteidiger anzeigen</label>';
    $html .= '</div>';
    return $html;
}

?>
<!doctype html>
<html>
<head>
<title><?php echo $ss_lang['title']?></title>
<?php include "cssinclude.php"; ?>
<script>
function deirc(f,se,sy,a1,a2,a3) {
	var lb="\n";
	var t="<?php echo $ss_lang['systemstatusvon']?> "+se+":"+sy+lb;var j=0;
	for (i=0; i<a1.length/8; i++){
		t=t+"ETA: "+a1[j]+" INC: "+a1[j+1]+" (FP: "+a1[j+6]+") DEF: "+a1[j+4]+" (FP: "+a1[j+7]+") "+lb;
		var j=j+8;
	}
	if(document.getElementsByName("a"+se+"_"+sy)[0].checked == true){t=t+"<?php echo $ss_lang['angreifer']?>: "+a2+lb; i++;}
	if(document.getElementsByName("d"+se+"_"+sy)[0].checked == true){t=t+"<?php echo $ss_lang['verteidiger']?>: "+a3+lb; i++;}

	if(f==3){
		//offizieller Teilen-Link: öffnet am Handy die App, am PC WhatsApp Web bzw. die Desktop-App;
		//whatsapp:// lief ohne installierte App oder aus dem Spiel-iframe heraus ins Leere
		var url="https://wa.me/?text="+encodeURIComponent(t);
		var wa=window.open(url, "_blank");
		if(wa){
			wa.opener=null;
		}else{
			//Popup blockiert: im ganzen Fenster öffnen
			window.top.location.href=url;
		}
	}else{
		var feld=document.createElement("textarea");
		feld.id="k"+se+"_"+sy;
		feld.className="mod-eingabe ss-text";
		feld.rows=i+1;
		feld.wrap="off";
		feld.value=t;
		var ziel=document.getElementById("s"+se+"_"+sy);
		ziel.innerHTML="";
		ziel.appendChild(feld);
		feld.select();
	}
}
</script>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar
include "resline.php";

echo '<div class="mod ss-leiste"><span>'.$ss_lang['title'].' <b>'.$ss_lang['sektor'].' '.$sector.'</b></span><a href="secstatus.php" class="mod-btn mod-btn-leise ally-btn-klein">Aktualisieren</a></div>';

if ($secstatdisable == 1) {
    echo '<div class="mod pol-meldungen"><div class="mod-meldung mod-meldung-warn">'.$ss_lang['secstatdisable'].'</div></div>';
}

//////////////////////////////////////////////////////////////////////////////
//angreifer - verteidiger
//////////////////////////////////////////////////////////////////////////////

$eta = -1;
$ssc = 0;
if ($secstatdisable == 1) {
    $flotten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE zielsec = ? AND zielsys= ? AND (aktion = 1 OR aktion = 2) AND entdeckt > 0 AND entdecktsec > 0 ORDER BY zielsys, zeit, hsec, hsys ASC", [$sector, $system]);
} else {
    $flotten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE zielsec = ? AND (aktion = 1 OR aktion = 2) AND entdeckt > 0 AND entdecktsec > 0 ORDER BY zielsys, zeit, hsec, hsys ASC", [$sector]);
}
$zsecold = 0;
$zsysold = 0;
$sc = array();
$ss_zeilen = array();
$fa = mysqli_num_rows($flotten);

//alle gefunden Atter/Deffer-Flotten in ein Array laden
$fleet_data = [];
while ($fleet_row = mysqli_fetch_assoc($flotten)) {
    $fleet_data[] = $fleet_row;
}

//alle gefunden Atter/Deffer-Flotten durchgehen
for ($i = 0; $i < $fa; $i++) {
    $row_fleet = $fleet_data[$i];
    $user_id = $row_fleet["user_id"];
    $zsec1 = $sector;
    $zsys1 = $row_fleet["zielsys"];
    $a1 = $row_fleet["aktion"];
    $t1 = $row_fleet["zeit"];
    $at1 = $row_fleet["aktzeit"];
    $hsec = $row_fleet["hsec"];
    $hsys = $row_fleet["hsys"];
    $ge = $row_fleet["fleetsize"];

    if ($zsec1 == $zsecold and $zsys1 == $zsysold) {
        //es ist noch das gleiche system
        $eta = $t1;
        if (isset($sc[$ssc][1][1]) && $eta > $sc[$ssc][1][1]) {
            $sc[$ssc][1][1] = $eta;//maxeta
        }

        //angreiferliste
        if ($a1 == 1) {
            if (isset($sc[$ssc][0][$eta][0])) {
                $sc[$ssc][0][$eta][0] += $ge;//atter
            } else {
                $sc[$ssc][0][$eta][0] = $ge;//atter
            }

            if (!isset($sc[$ssc][0][$eta][2])) {
                $sc[$ssc][0][$eta][2] = '';
            }

            $pos = strpos($sc[$ssc][0][$eta][2], $hsec.':'.$hsys);
            if ($pos === false) {// nicht gefunden...
                if ($sc[$ssc][0][$eta][2] != '') {
                    $sc[$ssc][0][$eta][2] .= ', ';
                }
                $sc[$ssc][0][$eta][2] .= $hsec.':'.$hsys;
            }

        } else {
            //defferliste

            //Einheitenanzahl
            if (isset($sc[$ssc][0][$eta][1])) {
                $sc[$ssc][0][$eta][1] += $ge;//deffer
            } else {
                $sc[$ssc][0][$eta][1] = $ge;
            }


            //deffer3 liste
            for ($j = 0;$j <= $at1;$j++) {
                if (isset($sc[$ssc][0][$eta + $j][4])) {
                    $sc[$ssc][0][$eta + $j][4] += $ge;
                } else {
                    $sc[$ssc][0][$eta + $j][4] = $ge;
                }


                if ($eta + $j > $sc[$ssc][1][1]) {
                    $sc[$ssc][1][1] = $eta + $j;
                }
            }

            if (!isset($sc[$ssc][0][$eta][3])) {
                $sc[$ssc][0][$eta][3] = '';
            }

            $pos = strpos($sc[$ssc][0][$eta][3], $hsec.':'.$hsys);
            if ($pos === false) {// nicht gefunden...
                if ($sc[$ssc][0][$eta][3] != '') {
                    $sc[$ssc][0][$eta][3] .= ', ';
                }
                $sc[$ssc][0][$eta][3] .= $hsec.':'.$hsys;
            }
        }
    } else {
        //es ist ein neues system
        //counter für die anzahl der angegriffenen systeme im sektor
        if ($zsecold > 0) {
            $ssc++;
        }

        $eta = $t1;
        if ($a1 == 1) {
            $sc[$ssc][0][$eta][0] = $ge;
        }//atter
        if ($a1 == 2) {
            $sc[$ssc][0][$eta][1] = $ge;
        }//deffer
        $sc[$ssc][1][0] = $zsys1;//system

        if (!isset($sc[$ssc][1][1]) || $eta > $sc[$ssc][1][1]) {
            $sc[$ssc][1][1] = $eta;//maxeta
        }

        if ($a1 == 1) {
            $sc[$ssc][0][$eta][2] = $hsec.':'.$hsys;
        } else { //defferliste
            $sc[$ssc][0][$eta][3] = $hsec.':'.$hsys;

            for ($j = 0;$j <= $at1;$j++) {

                if (isset($sc[$ssc][0][$eta + $j][4])) {
                    $sc[$ssc][0][$eta + $j][4] += $ge;
                } else {
                    $sc[$ssc][0][$eta + $j][4] = $ge;
                }


                if (isset($sc[$ssc][1][1]) && ($eta + $j) > $sc[$ssc][1][1]) {
                    $sc[$ssc][1][1] = $eta + $j;
                }
            }
        }
    }

    $as1 = $a1;
    if ($a1 == 0) {
        $a1 = $ss_lang['systemverteidigung'];
    } elseif ($a1 == 1) {
        $a1 = $ss_lang['angriff'];
        $cl = 'ccr';
    } elseif ($a1 == 2) {
        $a1 = $ss_lang['verteidigung'].' ('.$at1.')';
        $cl = 'ccg';
    } elseif ($a1 == 3) {
        $a1 = $ss_lang['rueckflug'];
        $cl = 'cc';
    } elseif ($a1 == 4) {
        $a1 = $ss_lang['archaeologie'];
        $cl = 'cc';
    }

    if ($a1[0] == $ss_lang['verteidigung'][0] && $t1 == 0) {
        $a1 = $ss_lang['verteidige'];
        $t1 = $at1;
        $cl = 'ccy';
    }

    //rasse und allytag auslesen
    $allytagscan = '';
    $zally = '';
    $hv = explode("-", $user_id);
    $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
    if ($uid != $_SESSION['ums_user_id']) {
        //allygegner/-verbündete
        //allytag des deffers/atters auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag, rasse, status FROM de_user_data WHERE user_id=?", [$uid]);
        $row = mysqli_fetch_assoc($db_daten);
        if ($row["status"] == 1) {
            $zally = $row["allytag"];
        }
        $rasse_id = $row['rasse'];


        if (in_array($zally, $allyfeinde) or in_array($zally, $allypartner)) {
            $allytagscan = $zally;
        }

        //eigene ally
        if ($zally == $ownally) {
            $allytagscan = $zally;
        }

        //geheimdienst
        //daten aus der db holen, wenn es nicht der spieler selbst ist
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT rasse, allytag FROM de_user_scan WHERE user_id=? AND zuser_id=?", [$_SESSION['ums_user_id'], $uid]);
        $scan_vorhanden = mysqli_num_rows($db_daten);
        if ($scan_vorhanden == 1) {
            $row = mysqli_fetch_assoc($db_daten);
            //allytag zuweisen, wenn noch nichts vorliegt, sonst sind die daten veraltet
            if ($allytagscan == '') {
                $allytagscan = $row["allytag"];
            }
        }
    } else {//der spieler selbst soll angezeigt werden
        $rasse_id = $_SESSION['ums_rasse'];
        $allytagscan = $ownally;
    }

    //die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
    $fp = 0;
    for ($s = 81;$s <= 90;$s++) {

        if ($as1 == 1) { //Atter
            if ($rasse_id == 4 && $s == 83) {
                //getarnte Einheiten
            } else {
                $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_fleet['e'.$s];
            }
        } else { //Deffer
            $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_fleet['e'.$s];
        }
    }

    //die Flottenpunkte für die Einzelausgabe sichern
    if ($as1 == 1) {
        //Angriff
        if (!isset($sc[$ssc][0][$eta]['fp_atter'])) {
            $sc[$ssc][0][$eta]['fp_atter'] = 0;
        }

        $sc[$ssc][0][$eta]['fp_atter'] += $fp;
    } elseif ($as1 == 2) {
        //Verteidigung
        if (!isset($sc[$ssc][0][$eta]['fp_deffer'])) {
            $sc[$ssc][0][$eta]['fp_deffer'] = 0;
        }

        $sc[$ssc][0][$eta]['fp_deffer'] += $fp;

        //evtl. bleibt er ein paar KT vor Ort beim Deffen, also ggf. die Folgeticks berechnen
        for ($j = 1;$j <= $at1;$j++) {

            if (isset($sc[$ssc][0][$eta + $j]['fp_deffer_3'])) {
                $sc[$ssc][0][$eta + $j]['fp_deffer_3'] += $fp;
            } else {
                $sc[$ssc][0][$eta + $j]['fp_deffer_3'] = $fp;
            }
        }

    }

    $ss_zeilen[$ssc][] = ss_flottenzeile($hsec, $hsys, ss_rasse($rasse_id), $allytagscan, $cl, $as1, $at1, $t1, $ge, $fp);

    $zsecold = $zsec1;
    $zsysold = $zsys1;
}

//////////////////////////////////////////////////////////////////////////////
//ankommende sektorflotten (BK)
//////////////////////////////////////////////////////////////////////////////

$flotten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE zielsec = ? AND (aktion = 1 OR aktion = 2)", [$sector]);
$fa = mysqli_num_rows($flotten);

// Alle Flotten in ein Array laden
$sector_fleet_data = [];
while ($sector_row = mysqli_fetch_assoc($flotten)) {
    $sector_fleet_data[] = $sector_row;
}

$ss_sektoranflug = '';
for ($i = 0; $i < $fa; $i++) {
    $row_sector = $sector_fleet_data[$i];
    $sec_id = $row_sector["sec_id"];
    $a1 = $row_sector["aktion"];
    $t1 = $row_sector["zeit"];
    $at1 = $row_sector["aktzeit"];

    $as1 = $a1;
    if ($a1 == 0) {
        $a1 = $ss_lang['systemverteidigung'];
    } elseif ($a1 == 1) {
        $a1 = $ss_lang['angriff'];
        $cl = 'ccr';
    } elseif ($a1 == 2) {
        $a1 = $ss_lang['verteidigung'].' ('.$at1.')';
        $cl = 'ccg';
    } elseif ($a1 == 3) {
        $a1 = $ss_lang['rueckflug'];
        $cl = 'cc';
    } elseif ($a1 == 4) {
        $a1 = $ss_lang['archaeologie'];
        $cl = 'cc';
    }

    if ($a1[0] == $ss_lang['verteidigung'][0] && $t1 == 0) {
        $a1 = $ss_lang['verteidige'];
        $t1 = $at1;
        $cl = 'ccy';
    }

    //einheiten zählen
    $ge = $row_sector["e2"];

    $ss_sektoranflug .= '<div class="ss-zeile ss-sektorflug"><span><b>'.$ss_lang['sektor'].' '.$sec_id.'</b></span><span class="ss-auftrag">'.ss_auftrag($cl, $as1, $at1).'</span><span class="ss-zahl">'.$t1.' KT</span><span class="ss-zahl">'.number_format($ge, 0, "", ".").'</span></div>';
}

//////////////////////////////////////////////////////////////////////////////
//je angegriffenem bzw. verteidigtem System eine Karte: Flotten, ETA-Übersicht, Text/WhatsApp
//////////////////////////////////////////////////////////////////////////////
rahmen_oben($ss_lang['angreifer'].' &ndash; '.$ss_lang['verteidiger']);
echo '<div class="mod ss">';

if (count($fleet_data) == 0 && count($sector_fleet_data) == 0) {
    echo '<div class="mod-leer">Es wurden keine anfliegenden Angreifer oder Verteidiger entdeckt.</div>';
}

if (count($sc) > 0) {
    for ($i = 0;$i <= $ssc;$i++) {
        $jsirc1 = '';
        $jsirc2 = '';
        $jsirc3 = '';

        //inc soll an die allianz meldbar sein, wenn der spieler in einer allianz ist und es noch nicht gemeldet worden ist
        //test auf ally
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, allytag, ally_id, status, show_ally_secstatus FROM de_user_data WHERE sector=? AND system=?", [$sector, $sc[$i][1][0]]);
        $row = mysqli_fetch_assoc($db_daten);
        $ss_name = $row['spielername'] ?? '';
        if ($row["status"] == 1) {
            $ally_id = $row['ally_id'];
            $allytag = $row['allytag'];
        } else {
            $ally_id = '';
            $allytag = '';
        }
        $show_ally_secstatus = $row['show_ally_secstatus'];
        $ss_ai = '';
        if ($ally_id > 0) {

            //test ob der status aktuell bereits übermittelt wird
            if ($show_ally_secstatus > time()) {//wird übermittelt
                $ss_ai = '<span class="mod-chip">Allianzeinsicht '.RealTime::until($show_ally_secstatus).'</span>';
            } else {//wird nicht übermittelt, melden link einblenden/überprüfen
                //test auf aktivierung
                if (isset($_REQUEST['sassys']) && $_REQUEST['sassys'] == $sc[$i][1][0]) {
                    //Sichtbarkeit berechnen
                    $sichtbarkeit = $GLOBALS['sv_show_ally_secstatus'];
                    //Sichtbarkeit um Allianzgebäude verlängern
                    $allybldg = get_allybldg($ally_id);
                    $geb_stufe = $allybldg[7];
                    //boni durch allianzpartner
                    $allyidpartner = get_allyid_partner($ally_id);
                    if ($allyidpartner > 0) {
                        $allybldgpartner = get_allybldg($allyidpartner);
                        $geb_stufe += $allybldgpartner[7] / 100 * $allybldgpartner[1];
                    }

                    $sichtbarkeit = $sichtbarkeit + ($sichtbarkeit / 100 * $geb_stufe);

                    $show_ally_secstatus = time() + $sichtbarkeit;

                    $ss_ai = '<span class="mod-chip mod-chip-gruen">Allianzeinsicht '.RealTime::until($show_ally_secstatus).'</span>';
                    //db updaten
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET show_ally_secstatus=? WHERE sector=? AND `system`=?", [$show_ally_secstatus, $sector, $sc[$i][1][0]]);
                    //eintrag im allianzchat
                    $chattext = '<font color="#ff0101">Status&uuml;bermittlung von ('.$sector.':'.$sc[$i][1][0].') durch '.$_SESSION['ums_spielername'].'</font>';
                    insert_chat_msg($ally_id, 1, '', $chattext);


                } else {
                    //aktivierungslink anzeigen
                    $ss_ai = '<a href="secstatus.php?sassys='.$sc[$i][1][0].'" class="mod-btn mod-btn-leise ally-btn-klein"
							title="Die Allianz '.$allytag.' f&uuml;r einige Zeit &uuml;ber den Status ihres Mitgliedes informieren.">Allianz informieren</a>';
                }
            }
        }

        echo '<div class="ss-system">';
        echo '<div class="ss-system-kopf"><a href="military.php?se='.$sector.'&amp;sy='.$sc[$i][1][0].'" class="ss-koords" title="Flotten">'.$sector.':'.$sc[$i][1][0].'</a>';
        echo '<span class="ss-name">'.$ss_name.'</span>'.$ss_ai.ss_links($sector, $sc[$i][1][0]).'</div>';

        //anfliegende Flotten
        echo ss_flottenkopf().'<div class="ss-liste-zeilen">'.implode('', $ss_zeilen[$i] ?? array()).'</div>';

        //die einzelne etas ausgeben
        $etazeilen = '';
        for ($j = 0; $j <= $sc[$i][1][1];$j++) {
            //wenn es schiffe bei der eta gibt, dann eine zeile ausgeben
            if ((isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0] > 0) || (isset($sc[$i][0][$j][1]) && $sc[$i][0][$j][1] > 0) || (isset($sc[$i][0][$j][4]) && $sc[$i][0][$j][4] > 0) || (isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][2] > 0) || (isset($sc[$i][0][$j][3]) && $sc[$i][0][$j][3] > 0)) {
                //verhältniss atter/deffer berechnen
                //nur berechnen, wenn es atter gibt

                if (!isset($sc[$i][0][$j][1])) {
                    $sc[$i][0][$j][1] = 0;
                }

                if (!isset($sc[$i][0][$j][4])) {
                    $sc[$i][0][$j][4] = 0;
                }

                if (!isset($sc[$i][0][$j][6])) {
                    $sc[$i][0][$j][6] = 0;
                }

                if (isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0] > 0) {
                    $sc[$i][0][$j][5] = $sc[$i][0][$j][1] / $sc[$i][0][$j][0];
                    $sc[$i][0][$j][6] = $sc[$i][0][$j][4] / $sc[$i][0][$j][0];
                } else {
                    $sc[$i][0][$j][5] = 0;//deffer
                    $sc[$i][0][$j][6] = 0;//deffer3
                }

                if ($sc[$i][0][$j][5] > 0) {
                    $v1 = ' (1:'.number_format($sc[$i][0][$j][5], 1, ",", ".").')';
                } else {
                    $v1 = '';
                }
                if ($sc[$i][0][$j][6] > 0) {
                    $v3 = ' (1:'.number_format($sc[$i][0][$j][6], 1, ",", ".").')';
                } else {
                    $v3 = '';
                }

                if (!isset($sc[$i][0][$j][0])) {
                    $sc[$i][0][$j][0] = 0;
                }

                if (!isset($sc[$i][0][$j][2])) {
                    $sc[$i][0][$j][2] = '';
                }

                if (!isset($sc[$i][0][$j][3])) {
                    $sc[$i][0][$j][3] = '';
                }

                if (!isset($sc[$i][0][$j]['fp_atter'])) {
                    $sc[$i][0][$j]['fp_atter'] = 0;
                }

                if (!isset($sc[$i][0][$j]['fp_deffer'])) {
                    $sc[$i][0][$j]['fp_deffer'] = 0;
                }

                if (!isset($sc[$i][0][$j]['fp_deffer_3'])) {
                    $sc[$i][0][$j]['fp_deffer_3'] = 0;
                }

                $etazeilen .= ss_etazeile($j, $sc[$i][0][$j][0], $sc[$i][0][$j][4], $sc[$i][0][$j]['fp_atter'], $sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'], $sc[$i][0][$j][2], $sc[$i][0][$j][3]);

                //Text/WA JS-Daten
                //Array: ETA,Atter, Deffer, Einheiten-Verhältnis, Deffer3, Einheiten3-Verhältnis
                if ($sc[$i][0][$j][0] > 0 || $sc[$i][0][$j][2] > 0 || $sc[$i][0][$j][3] > 0) {
                    $gesamt_fp = $sc[$i][0][$j]['fp_atter'] + $sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'];

                    if ($gesamt_fp > 0) {
                        $atter_percent = $sc[$i][0][$j]['fp_atter'] * 100 / $gesamt_fp;
                        $deffer_percent = ($sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3']) * 100 / $gesamt_fp;
                    } else {
                        $atter_percent = '0.00';
                        $deffer_percent = '0.00';
                    }

                    $fp_atter = formatMasseinheit($sc[$i][0][$j]['fp_atter'], 2).' / '.number_format($atter_percent, 2, ",", ".").'%';

                    $fp_deffer = formatMasseinheit($sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'], 2).' / '.number_format($deffer_percent, 2, ",", ".").'%';

                    if ($jsirc1 != '') {
                        $jsirc1 .= ",";
                    }
                    $jsirc1 .= "'".$j."','".number_format($sc[$i][0][$j][0], 0, "", ".")."','".number_format($sc[$i][0][$j][1], 0, "", ".")."','".
                    $v1."','".number_format($sc[$i][0][$j][4], 0, "", ".")."','".$v3."','".$fp_atter."','".$fp_deffer."'";
                }

                //String: Atter
                if ($sc[$i][0][$j][2] > 0) {
                    if ($jsirc2 != '') {
                        $jsirc2 .= ",";
                    }
                    $jsirc2 .= "'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][2]."'";
                }

                //String: Deffer
                if ($sc[$i][0][$j][3] > 0) {
                    if ($jsirc3 != '') {
                        $jsirc3 .= ",";
                    }
                    $jsirc3 .= "'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][3]."'";
                }
            }
        }

        echo '<div class="ss-abschnitt"><div class="mod-typ">Verlauf je Kampftick</div>'.ss_etakopf().'<div class="ss-liste-zeilen">'.$etazeilen.'</div></div>';
        echo ss_export($sector, $sc[$i][1][0], $jsirc1, $jsirc2, $jsirc3);
        echo '</div>';
    }
}

if ($ss_sektoranflug != '') {
    echo '<div class="ss-system"><div class="ss-system-kopf"><span class="ss-name">Sektorflotten im Anflug</span></div>';
    echo '<div class="ss-zeile ss-sektorflug ss-kopfzeile"><span>'.$ss_lang['herkunft'].'</span><span>'.$ss_lang['status'].'</span><span>'.$ss_lang['zeit'].'</span><span>'.$ss_lang['schiffe'].'</span></div>';
    echo '<div class="ss-liste-zeilen">'.$ss_sektoranflug.'</div></div>';
}

echo '</div>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
//alle flotten des sektors selbst anzeigen
//////////////////////////////////////////////////////////////////////////////
if ($secstatdisable == 1) {
    $flotten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE hsec=? AND hsys=? AND aktion>0 ORDER BY hsys, aktion, zeit ASC", [$sector, $system]);
} else {
    $flotten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE hsec=? AND aktion>0 ORDER BY hsys, aktion, zeit ASC", [$sector]);
}

// Alle Flotten in ein Array laden
$outgoing_fleet_data = [];
while ($outgoing_row = mysqli_fetch_assoc($flotten)) {
    $outgoing_fleet_data[] = $outgoing_row;
}

$ss_eigene = '';
$fa = count($outgoing_fleet_data);
for ($i = 0; $i < $fa; $i++) {
    $row_fleet = $outgoing_fleet_data[$i];
    $zsec1 = $row_fleet["zielsec"];
    $zsys1 = $row_fleet["zielsys"];
    $a1 = $row_fleet["aktion"];
    $t1 = $row_fleet["zeit"];
    $at1 = $row_fleet["aktzeit"];
    $hsec = $row_fleet["hsec"];
    $hsys = $row_fleet["hsys"];
    $showft = $row_fleet["showfleettarget"];
    $mission_time = $row_fleet["mission_time"];

    //wenn man es nicht selbst ist, dann sieht man die koordinaten von anderen spielern die angreifen nicht mehr, wenn sie versteckt sind
    if ($hsys != $system) {
        //nur bei angriff/verteidigung/mission bei bedarf ausblenden
        if ($a1 == 1 or $a1 == 2 or $a1 == 4) {
            if ($showft == 0) {
                $zsec1 = '?';
                $zsys1 = '?';
            }
        }
    }

    $mission_aktiv = false;
    $as1 = $a1;
    if ($a1 == 0) {
        $a1 = $ss_lang['systemverteidigung'];
    } elseif ($a1 == 1) {
        $a1 = $ss_lang['angriff'];
        $cl = 'ccr';
    } elseif ($a1 == 2) {
        $a1 = $ss_lang['verteidigung'].' ('.$at1.')';
        $cl = 'ccg';
    } elseif ($a1 == 3) {
        $a1 = $ss_lang['rueckflug'];
        $cl = 'cc';
    } elseif ($a1 == 4) {
        $a1 = $ss_lang['archaeologie'];
        $cl = 'cc';
        $mission_aktiv = true;
    }

    if ($a1[0] == $ss_lang['verteidigung'][0] && $t1 == 0) {
        $a1 = $ss_lang['verteidige'];
        $t1 = $at1;
        $cl = 'ccy';
    }

    //einheiten zählen
    $ge = 0;
    for ($z = 81;$z <= 90;$z++) {
        $erg = $row_fleet["e$z"];
        $ez[$z - 81] = $erg;
        $ge = $ge + $erg;
    }

    $hv = explode("-", $row_fleet["user_id"]);
    $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
    if ($uid != $_SESSION['ums_user_id']) {
        $sql = "SELECT allytag, rasse, status FROM de_user_data WHERE user_id=?";
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid]);
        $row = mysqli_fetch_assoc($db_daten);
        if ($row["status"] == 1) {
            $zally = $row["allytag"];
        }
        $rasse_id = $row['rasse'];
    } else {
        $rasse_id = $_SESSION['ums_rasse'];
    }

    //die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
    $fp = 0;
    for ($s = 81;$s <= 90;$s++) {

        if ($as1 == 1) { //Atter
            if ($rasse_id == 4 && $s == 83) {
                //getarnte Einheiten
            } else {
                $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_fleet['e'.$s];
            }
        } else { //Deffer
            $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_fleet['e'.$s];
        }
    }

    $fp_html = ($sv_hide_fp_in_secstatus != 1) ? '<span class="ss-zahl" title="'.number_format($fp, 0, "", ".").'">'.formatMasseinheit($fp, 2).'</span>' : '<span class="ss-zahl">N/A</span>';

    if ($mission_aktiv) {
        $ss_eigene .= '<div class="ss-zeile ss-eigen"><span><b>'.$hsec.':'.$hsys.'</b></span><span class="ss-auftrag">'.ss_auftrag($cl, $as1, $at1).'</span><span class="ss-leise">&ndash;</span>';
        $ss_eigene .= '<span class="ss-zahl ss-uhrzeit" title="Ende der Mission">'.RealTime::until($mission_time).'</span><span class="ss-zahl">'.number_format($ge, 0, "", ".").'</span>'.$fp_html.'</div>';
    } else {
        $ss_eigene .= '<div class="ss-zeile ss-eigen"><span><b>'.$hsec.':'.$hsys.'</b></span><span class="ss-auftrag">'.ss_auftrag($cl, $as1, $at1).'</span>';
        //beim Rückflug ist das Ziel die Heimat
        $ss_eigene .= '<span>'.($as1 == 3 ? '<span class="ss-leise">Heimat</span>' : $zsec1.':'.$zsys1).'</span>';
        $ss_eigene .= '<span class="ss-zahl">'.$t1.' KT</span><span class="ss-zahl">'.number_format($ge, 0, "", ".").'</span>'.$fp_html.'</div>';
    }
}

//sektorflotte in bewegung anzeigen
$sql = "SELECT zielsec, sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE aktion<>0 AND sec_id=?";
$flotten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
$fa = mysqli_num_rows($flotten);
for ($i = 0; $i < $fa; $i++) {
    $row_sector = mysqli_fetch_assoc($flotten);
    $zsec1 = $row_sector["zielsec"];
    $sec_id = $row_sector["sec_id"];
    $a1 = $row_sector["aktion"];
    $t1 = $row_sector["zeit"];
    $at1 = $row_sector["aktzeit"];

    $as1 = $a1;
    if ($a1 == 0) {
        $a1 = $ss_lang['systemverteidigung'];
    } elseif ($a1 == 1) {
        $a1 = $ss_lang['angriff'];
        $cl = 'ccr';
    } elseif ($a1 == 2) {
        $a1 = $ss_lang['verteidigung'].' ('.$at1.')';
        $cl = 'ccg';
    } elseif ($a1 == 3) {
        $a1 = $ss_lang['rueckflug'];
        $cl = 'cc';
    } elseif ($a1 == 4) {
        $a1 = $ss_lang['archaeologie'];
        $cl = 'cc';
    }

    if ($a1[0] == $ss_lang['verteidigung'][0] && $t1 == 0) {
        $a1 = $ss_lang['verteidige'];
        $t1 = $at1;
        $cl = 'ccy';
    }

    //einheiten zählen
    $ge = $row_sector["e2"];

    $ss_eigene .= '<div class="ss-zeile ss-eigen"><span><b>Sektorflotte</b></span><span class="ss-auftrag">'.ss_auftrag($cl, $as1, $at1).'</span>';
    $ss_eigene .= '<span>'.($as1 == 3 ? '<span class="ss-leise">Heimat</span>' : $ss_lang['sektor'].' '.$zsec1).'</span><span class="ss-zahl">'.$t1.' KT</span><span class="ss-zahl">'.number_format($ge, 0, "", ".").'</span><span class="ss-zahl">&ndash;</span></div>';
}

rahmen_oben($ss_lang['sektorflotten']);
echo '<div class="mod ss">';
if (count($outgoing_fleet_data) == 0 && $fa == 0) {
    echo '<div class="mod-leer">'.($secstatdisable == 1 ? 'Zurzeit sind keine deiner Flotten unterwegs.' : 'Zurzeit sind keine Flotten deines Sektors unterwegs.').'</div>';
} else {
    echo '<div class="ss-zeile ss-eigen ss-kopfzeile"><span>'.$ss_lang['herkunft'].'</span><span>'.$ss_lang['status'].'</span><span>'.$ss_lang['ziel'].'</span><span>'.$ss_lang['zeit'].'</span><span>'.$ss_lang['schiffe'].'</span><span title="Flottenpunkte. Getarnte Einheiten werden mit eingerechnet.">FP</span></div>';
    echo '<div class="ss-liste-zeilen">'.$ss_eigene.'</div>';
}
echo '</div>';
rahmen_unten();

////////////////////////////////////////////////////////////////////////////////
//der sektorstatus der allianz(bündnis)mitglieder
////////////////////////////////////////////////////////////////////////////////
//nur anzeigen, wenn man selbst in einer allianz ist
if ($ownally != '') {
    $eta = -1;
    $ssc = 0;
    unset($sc);
    $ss_zeilen = array();

    //allytag von einer evtl. partnerallianz auslesen
    $allypartnertag = isset($allypartner[0]) ? $allypartner[0] : '';
    $time = time();

    // Erstelle einen dynamischen SQL-String für die Partner-Allianz-Bedingung
    $partnerCondition = $allypartnertag != '' ? " OR de_user_data.allytag=?" : "";

    $sql = "SELECT *, de_user_fleet.user_id, de_user_fleet.zielsec, de_user_fleet.zielsys, de_user_fleet.aktion, de_user_fleet.aktzeit, de_user_fleet.hsec,
de_user_fleet.hsys, de_user_fleet.zeit, de_user_fleet.fleetsize, de_user_data.show_ally_secstatus
FROM de_user_fleet LEFT JOIN de_user_data ON(de_user_data.sector=de_user_fleet.zielsec AND de_user_data.`system`=de_user_fleet.zielsys)
WHERE de_user_fleet.zielsec != ? AND (de_user_fleet.aktion = 1 OR de_user_fleet.aktion = 2) AND de_user_fleet.entdeckt > 0
AND de_user_fleet.entdecktsec > 0 AND de_user_data.show_ally_secstatus>? AND de_user_data.status=1 AND de_user_data.allytag<>'' AND
(de_user_data.allytag=?" . $partnerCondition . ")
ORDER BY de_user_fleet.zielsec, de_user_fleet.zielsys, de_user_fleet.zeit, de_user_fleet.hsec, de_user_fleet.hsys ASC";

    // Parameter für die Abfrage vorbereiten
    $params = [$sector, $time, $ownally];
    if ($allypartnertag != '') {
        $params[] = $allypartnertag;
    }

    $flotten = mysqli_execute_query($GLOBALS['dbi'], $sql, $params);
    $zsecold = 0;
    $zsysold = 0;
    $fa = mysqli_num_rows($flotten);
    for ($i = 0; $i < $fa; $i++) {
        $row_ally_fleet = mysqli_fetch_assoc($flotten);

        $user_id = $row_ally_fleet["user_id"];
        $zsec1 = $row_ally_fleet["zielsec"];
        $zsys1 = $row_ally_fleet["zielsys"];
        $a1 = $row_ally_fleet["aktion"];
        $t1 = $row_ally_fleet["zeit"];
        $at1 = $row_ally_fleet["aktzeit"];
        $hsec = $row_ally_fleet["hsec"];
        $hsys = $row_ally_fleet["hsys"];
        $ge = $row_ally_fleet["fleetsize"];
        $show_ally_secstatus = $row_ally_fleet["show_ally_secstatus"];


        if ($zsec1 == $zsecold and $zsys1 == $zsysold) {
            //es ist noch das gleiche system
            $eta = $t1;
            if ($eta > $sc[$ssc][1][1]) {
                $sc[$ssc][1][1] = $eta;
            }//maxeta

            //angreiferliste
            if ($a1 == 1) {
                // Sicherstellen, dass Zähler und String existieren
                if (!isset($sc[$ssc][0][$eta][0])) { $sc[$ssc][0][$eta][0] = 0; }
                if (!isset($sc[$ssc][0][$eta][2])) { $sc[$ssc][0][$eta][2] = ''; }

                $sc[$ssc][0][$eta][0] += $ge;//atter

                $pos = strpos($sc[$ssc][0][$eta][2], $hsec.':'.$hsys);
                if ($pos === false) {// nicht gefunden...
                    if ($sc[$ssc][0][$eta][2] != '') {
                        $sc[$ssc][0][$eta][2] .= ' - ';
                    }
                    $sc[$ssc][0][$eta][2] .= $hsec.':'.$hsys;
                }
            } else { //defferliste
                if (!isset($sc[$ssc][0][$eta][1])) { $sc[$ssc][0][$eta][1] = 0; }
                if (!isset($sc[$ssc][0][$eta][3])) { $sc[$ssc][0][$eta][3] = ''; }
                $sc[$ssc][0][$eta][1] += $ge;//deffer

                //deffer3 liste
                for ($j = 0;$j <= $at1;$j++) {
                    if (!isset($sc[$ssc][0][$eta + $j][4])) { $sc[$ssc][0][$eta + $j][4] = 0; }
                    $sc[$ssc][0][$eta + $j][4] += $ge;
                    if ($eta + $j > $sc[$ssc][1][1]) {
                        $sc[$ssc][1][1] = $eta + $j;
                    }
                }

                $pos = strpos($sc[$ssc][0][$eta][3], $hsec.':'.$hsys);
                if ($pos === false) {// nicht gefunden...
                    if ($sc[$ssc][0][$eta][3] != '') {
                        $sc[$ssc][0][$eta][3] .= ' - ';
                    }
                    $sc[$ssc][0][$eta][3] .= $hsec.':'.$hsys;
                }
            }
        } else {
            //es ist ein neues system
            //counter für die anzahl der angegriffenen systeme im sektor
            if ($zsecold > 0) {
                $ssc++;
            }

            $eta = $t1;
            if ($a1 == 1) {
                if (!isset($sc[$ssc][0][$eta][0])) { $sc[$ssc][0][$eta][0] = 0; }
                $sc[$ssc][0][$eta][0] = $ge;
            }//atter
            if ($a1 == 2) {
                if (!isset($sc[$ssc][0][$eta][1])) { $sc[$ssc][0][$eta][1] = 0; }
                $sc[$ssc][0][$eta][1] = $ge;
            }//deffer
            $sc[$ssc][1][0] = $zsys1;//system
            $sc[$ssc][1]['sector'] = $zsec1;//sector
            $sc[$ssc][1]['show_ally_secstatus'] = $show_ally_secstatus;//show_ally_secstatus
            $sc[$ssc][1]['spielername'] = $row_ally_fleet['spielername'] ?? '';

            if (!isset($sc[$ssc][1][1])) {
                $sc[$ssc][1][1] = 0;
            }

            if ($eta > $sc[$ssc][1][1]) {
                $sc[$ssc][1][1] = $eta;//maxeta
            }
            if ($a1 == 1) {
                if (!isset($sc[$ssc][0][$eta][2])) { $sc[$ssc][0][$eta][2] = ''; }
                $sc[$ssc][0][$eta][2] = $hsec.':'.$hsys;
            } else { //defferliste
                if (!isset($sc[$ssc][0][$eta][3])) { $sc[$ssc][0][$eta][3] = ''; }
                $sc[$ssc][0][$eta][3] = $hsec.':'.$hsys;
                for ($j = 0;$j <= $at1;$j++) {
                    if (!isset($sc[$ssc][0][$eta + $j][4])) { $sc[$ssc][0][$eta + $j][4] = 0; }
                    $sc[$ssc][0][$eta + $j][4] += $ge;
                    if ($eta + $j > $sc[$ssc][1][1]) {
                        $sc[$ssc][1][1] = $eta + $j;
                    }
                }
            }
        }

        $as1 = $a1;
        if ($a1 == 0) {
            $a1 = $ss_lang['systemverteidigung'];
        } elseif ($a1 == 1) {
            $a1 = $ss_lang['angriff'];
            $cl = 'ccr';
        } elseif ($a1 == 2) {
            $a1 = $ss_lang['verteidigung'].' ('.$at1.')';
            $cl = 'ccg';
        } elseif ($a1 == 3) {
            $a1 = $ss_lang['rueckflug'];
            $cl = 'cc';
        } elseif ($a1 == 4) {
            $a1 = $ss_lang['archaeologie'];
            $cl = 'cc';
        }

        if ($a1[0] == $ss_lang['verteidigung'][0] && $t1 == 0) {
            $a1 = $ss_lang['verteidige'];
            $t1 = $at1;
            $cl = 'ccy';
        }

        //rasse und allytag auslesen
        $allytagscan = '';
        $zally = '';
        $hv = explode("-", $user_id);
        $uid = $hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
        if ($uid != $_SESSION['ums_user_id']) {
            //allygegner/-verbündete
            //allytag des deffers/atters auslesen
            $sql = "SELECT allytag, rasse, status FROM de_user_data WHERE user_id=?";
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$uid]);
            $row = mysqli_fetch_assoc($db_daten);
            if ($row["status"] == 1) {
                $zally = $row["allytag"];
            }

            $rasse_id = $row['rasse'];

            if (in_array($zally, $allyfeinde) or in_array($zally, $allypartner)) {
                $allytagscan = $zally;
            }

            //eigene ally
            if ($zally == $ownally) {
                $allytagscan = $zally;
            }

            //geheimdienst
            //daten aus der db holen, wenn es nicht der spieler selbst ist
            $sql = "SELECT rasse, allytag FROM de_user_scan WHERE user_id=? AND zuser_id=?";
            $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], $uid]);

            $scan_vorhanden = mysqli_num_rows($db_daten);
            if ($scan_vorhanden == 1) {
                $row = mysqli_fetch_assoc($db_daten);
                //allytag zuweisen, wenn noch nichts vorliegt, sonst sind die daten veraltet
                if ($allytagscan == '') {
                    $allytagscan = $row["allytag"];
                }
            }
        } else { //der spieler selbst soll angezeigt werden
            $rasse_id = $_SESSION['ums_rasse'];
            $allytagscan = $ownally;
        }

        //die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
        $fp = 0;
        for ($s = 81;$s <= 90;$s++) {

            if ($as1 == 1) { //Atter
                if ($rasse_id == 4 && $s == 83) {
                    //getarnte Einheiten
                } else {
                    $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_ally_fleet['e'.$s];
                }
            } else { //Deffer
                $fp = $fp + $unit[$rasse_id - 1][$s - 81][4] * $row_ally_fleet['e'.$s];
            }
        }

        //die Flottenpunkte für die Einzelausgabe sichern
        if ($as1 == 1) {
            //Angriff
            if (!isset($sc[$ssc][0][$eta]['fp_atter'])) {
                $sc[$ssc][0][$eta]['fp_atter'] = 0;
            }

            $sc[$ssc][0][$eta]['fp_atter'] += $fp;
        } elseif ($as1 == 2) {
            //Verteidigung
            if (!isset($sc[$ssc][0][$eta]['fp_deffer'])) {
                $sc[$ssc][0][$eta]['fp_deffer'] = 0;
            }

            $sc[$ssc][0][$eta]['fp_deffer'] += $fp;

            //evtl. bleibt er ein paar KT vor Ort beim Deffen, also ggf. die Folgeticks berechnen
            for ($j = 1;$j <= $at1;$j++) {

                if (isset($sc[$ssc][0][$eta + $j]['fp_deffer_3'])) {
                    $sc[$ssc][0][$eta + $j]['fp_deffer_3'] += $fp;
                } else {
                    $sc[$ssc][0][$eta + $j]['fp_deffer_3'] = $fp;
                }
            }

        }

        $ss_zeilen[$ssc][] = ss_flottenzeile($hsec, $hsys, ss_rasse($rasse_id), $allytagscan, $cl, $as1, $at1, $t1, $ge, $fp);

        $zsecold = $zsec1;
        $zsysold = $zsys1;
    }

    rahmen_oben('Allianzmitglieder');
    echo '<div class="mod ss">';
    if ($fa == 0) {
        echo '<div class="mod-leer">Bei deinen Allianzmitgliedern wurden keine anfliegenden Flotten entdeckt.</div>';
    }

    //jetzt die Übersicht der einzelnen system ausgeben
    if (isset($sc) && $sc != '') {
        for ($i = 0;$i <= $ssc;$i++) {
            $jsirc1 = '';
            $jsirc2 = '';
            $jsirc3 = '';
            $ally_sec = $sc[$i][1]['sector'];
            $hzsys = $sc[$i][1][0];

            echo '<div class="ss-system">';
            echo '<div class="ss-system-kopf"><a href="military.php?se='.$ally_sec.'&amp;sy='.$hzsys.'" class="ss-koords" title="Flotten">'.$ally_sec.':'.$hzsys.'</a>';
            echo '<span class="ss-name">'.$sc[$i][1]['spielername'].'</span>';
            echo '<span class="mod-chip">Allianzeinsicht '.RealTime::until($sc[$i][1]['show_ally_secstatus']).'</span>'.ss_links($ally_sec, $hzsys).'</div>';

            echo ss_flottenkopf().'<div class="ss-liste-zeilen">'.implode('', $ss_zeilen[$i] ?? array()).'</div>';

            //die einzelne etas ausgeben
            $etazeilen = '';
            for ($j = 0; $j <= $sc[$i][1][1];$j++) {
                //wenn es schiffe bei der eta gibt, dann eine zeile ausgeben
                if ((isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0] > 0) ||
                    (isset($sc[$i][0][$j][1]) && $sc[$i][0][$j][1] > 0) ||
                    (isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][2] > 0) ||
                    (isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][3] > 0) ||
                    (isset($sc[$i][0][$j][4]) && $sc[$i][0][$j][4] > 0)
                ) {
                    //verhältniss atter/deffer berechnen
                    //nur berechnen, wenn es atter gibt

                    if (!isset($sc[$i][0][$j][1])) {
                        $sc[$i][0][$j][1] = 0;
                    }

                    if (!isset($sc[$i][0][$j][4])) {
                        $sc[$i][0][$j][4] = 0;
                    }

                    if (!isset($sc[$i][0][$j][5])) {
                        $sc[$i][0][$j][5] = 0;
                    }

                    if (!isset($sc[$i][0][$j][6])) {
                        $sc[$i][0][$j][6] = 0;
                    }


                    if(!isset($sc[$i][0][$j][0])) {
                        $sc[$i][0][$j][0] = 0;
                    }

                    if ($sc[$i][0][$j][0] > 0) {
                        $sc[$i][0][$j][5] = $sc[$i][0][$j][1] / $sc[$i][0][$j][0];
                        $sc[$i][0][$j][6] = $sc[$i][0][$j][4] / $sc[$i][0][$j][0];
                    } else {
                        $sc[$i][0][$j][5] = 0;//deffer
                        $sc[$i][0][$j][6] = 0;//deffer3
                    }

                    if ($sc[$i][0][$j][5] > 0) {
                        $v1 = ' (1:'.number_format($sc[$i][0][$j][5], 1, ",", ".").')';
                    } else {
                        $v1 = '';
                    }
                    if ($sc[$i][0][$j][6] > 0) {
                        $v3 = ' (1:'.number_format($sc[$i][0][$j][6], 1, ",", ".").')';
                    } else {
                        $v3 = '';
                    }

                    if (!isset($sc[$i][0][$j][3])) {
                        $sc[$i][0][$j][3] = 0;
                    }

                    if (!isset($sc[$i][0][$j][0])) {
                        $sc[$i][0][$j][0] = 0;
                    }

                    if (!isset($sc[$i][0][$j][2])) {
                        $sc[$i][0][$j][2] = '';
                    }

                    if (!isset($sc[$i][0][$j][3])) {
                        $sc[$i][0][$j][3] = '';
                    }

                    if (!isset($sc[$i][0][$j]['fp_atter'])) {
                        $sc[$i][0][$j]['fp_atter'] = 0;
                    }

                    if (!isset($sc[$i][0][$j]['fp_deffer'])) {
                        $sc[$i][0][$j]['fp_deffer'] = 0;
                    }

                    if (!isset($sc[$i][0][$j]['fp_deffer_3'])) {
                        $sc[$i][0][$j]['fp_deffer_3'] = 0;
                    }

                    $etazeilen .= ss_etazeile($j, $sc[$i][0][$j][0], $sc[$i][0][$j][4], $sc[$i][0][$j]['fp_atter'], $sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'], $sc[$i][0][$j][2], $sc[$i][0][$j][3]);

                    if ($sc[$i][0][$j][0] > 0 || $sc[$i][0][$j][2] > 0 || $sc[$i][0][$j][3] > 0) {

                        $gesamt_fp = $sc[$i][0][$j]['fp_atter'] + $sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'];

                        if ($gesamt_fp > 0) {
                            $atter_percent = $sc[$i][0][$j]['fp_atter'] * 100 / $gesamt_fp;
                            $deffer_percent = ($sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3']) * 100 / $gesamt_fp;
                        } else {
                            $atter_percent = '0.00';
                            $deffer_percent = '0.00';
                        }

                        $fp_atter = formatMasseinheit($sc[$i][0][$j]['fp_atter'], 2).' / '.number_format($atter_percent, 2, ",", ".").'%';

                        $fp_deffer = formatMasseinheit($sc[$i][0][$j]['fp_deffer'] + $sc[$i][0][$j]['fp_deffer_3'], 2).' / '.number_format($deffer_percent, 2, ",", ".").'%';

                        if ($jsirc1 != '') {
                            $jsirc1 .= ",";
                        }
                        $jsirc1 .= "'".$j."','".number_format($sc[$i][0][$j][0], 0, "", ".")."','".number_format($sc[$i][0][$j][1], 0, "", ".")."','".
                        $v1."','".number_format($sc[$i][0][$j][4], 0, "", ".")."','".$v3."','".$fp_atter."','".$fp_deffer."'";

                    }

                    //String: Atter
                    if ($sc[$i][0][$j][2] > 0) {
                        if ($jsirc2 != '') {
                            $jsirc2 .= ",";
                        }
                        $jsirc2 .= "'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][2]."'";
                    }

                    //string: Deffer
                    if ($sc[$i][0][$j][3] > 0) {
                        if ($jsirc3 != '') {
                            $jsirc3 .= ",";
                        }
                        $jsirc3 .= "'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][3]."'";
                    }

                }
            }

            echo '<div class="ss-abschnitt"><div class="mod-typ">Verlauf je Kampftick</div>'.ss_etakopf().'<div class="ss-liste-zeilen">'.$etazeilen.'</div></div>';
            echo ss_export($ally_sec, $hzsys, $jsirc1, $jsirc2, $jsirc3);
            echo '</div>';
        }
    }
    echo '</div>';
    rahmen_unten();
}

?>

</body>
</html>
