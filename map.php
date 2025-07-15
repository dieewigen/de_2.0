<?php
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_sector.lang.php';
//include "lib/religion.lib.php";
include "functions.php";
include "issectork.php";

include 'lib/map_system_defs.inc.php';
include "lib/map_system.class.php";

$pt = loadPlayerTechs($_SESSION['ums_user_id']);
$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$techs = $row["techs"];
$platz = $row["platz"];
$erang_nr = $row["rang"];
$secsort = $row["secsort"];
$secstatdisable = $row["secstatdisable"];
$col = $row['col'];

$rzadd = 0;

$ownsector = $sector;
$ownsystem = $system;
$hide_secpics = $row["hide_secpics"];

$secrelcounter = 0;

//die Reihenfolge der Spieleraccounts im Sektor
$player_std_pos[0] = array(0,0);
$player_std_pos[1] = array(2,0);
$player_std_pos[2] = array(4,0);
$player_std_pos[3] = array(0,1);
$player_std_pos[4] = array(4,1);
$player_std_pos[5] = array(0,2);
$player_std_pos[6] = array(2,2);
$player_std_pos[7] = array(4,2);
$player_std_pos[8] = array(1,0);
$player_std_pos[9] = array(3,0);
$player_std_pos[10] = array(1,2);
$player_std_pos[11] = array(3,2);


//kopfgeldprozentsatz kann nicht h�her als kollektorklaurate sein
//if($sv_bounty_rate>$sv_kollie_klaurate)$sv_bounty_rate=$sv_kollie_klaurate;

if ($row["status"] == 1) {
    $ownally = $row["allytag"];
}
//schauen ob er die whg hat und dann die attgrenze anpassen
if ($techs[4] == 0) {
    $sv_attgrenze_whg_bonus = 0;
}

//owner id auslesen
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT owner_id FROM de_login WHERE user_id='$ums_user_id'");
$row = mysqli_fetch_array($db_daten);
$owner_id = intval($row["owner_id"]);


/*
if($_REQUEST["sso"]){
  $sso=intval($_REQUEST["sso"]);
  $sso--;
  mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET secsort='$sso' WHERE user_id='$ums_user_id'");
  $secsort=$sso;
}
*/

//spieler sortiert auslesen
$orderby = '`system`';
if ($secsort == '1') {
    $orderby = 'col';
} elseif ($secsort == '2') {
    $orderby = 'score';
}

//maximale anzahl von kollektoren auslesen
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0");
$row = mysqli_fetch_array($db_daten);
$maxcol = $row['maxcol'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Karte</title>
<?php
$newcss = 1;
include "cssinclude.php";
?>

<link rel="stylesheet" href="/js/jquery-ui-1.14.1/jquery-ui.min.css">

<script src="/js/jquery-3.7.1.min.js"></script>
<script src="/js/jquery-ui-1.14.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>

<script>
$(function() {
$(document).tooltip({
          content: function () {
              return $(this).prop('title');
          }
      });
});
</script>
<?php
$sektor_width = 720;
$sektor_height = 720;

$npc_sec_counter = 0;
$pc_sec_counter = 0;

$mapcontent_width = 100000;
$mapcontent_height = 100000;

echo '</head><body>';
echo '<div id="mapcontainer" style="position:absolute; top: 0px; left: 0px; width:100%; height:100%; overflow:hidden; background-color: #000000; z-index:0;
background-image:    url(g/PurpleNebula2048_back.jpg);
background-size:     cover;
background-repeat:   no-repeat;
background-position: center center;
">
    <div id="mapcontent" style="transform-origin: 0 0; position:absolute; overflow:hidden; left: 0px; top: 0px; width: '.$mapcontent_width.'px; height: '.$mapcontent_height.'px;  z-index:1;">';

//links oben spielname
echo '<div id="gamename" style="top:40000px; left:40000px;" data-left="40000" data-top="40000">die ewigen</div>';
echo '<div id="serverdesc" style="top:40512px; left:40000px;" data-left="40000" data-top="40512">'.$sv_server_name.' '.$sv_server_tag.'</div>';

//die Aliens verteilen, sind alle in Sektor 2
$output = '';
$npc_sec_counter++;
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
//npc anzeigen
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////

//kein malus bei aliens
$sec_angriffsgrenze = $sv_attgrenze - $sv_attgrenze_whg_bonus;
$col_angriffsgrenze_final = $sv_min_col_attgrenze;
$rec_bonus = 0;

//sektoransicht darstellen
//reisezeit
if ($rzadd == 0) {
    $style = 'border: 1px solid #444444; background-color: #00DD00; color: #000000; width: 16px; display: inline-block; text-align: center;';
} else {
    $style = 'border: 1px solid #444444; background-color: #f05a00; color: #000000; width: 16px; display: inline-block; text-align: center;';
}

$sektorinfo = '<span title="Reisezeitmalus<br>Eigener Sektor: kein Malus<br>Anderer Sektor: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
if (!empty($sf)) {
    //hinweistext f�r npc-sektoren
    $npchint = '<img src="'.$ums_gpfad.'g/symbol12.png" border="0" style="margin-bottom: -4px; width: 20px; height: 20px;" title="'.
            $sec_lang['npsecinfo1'].' '.$sec_lang['npsecinfo2'].get_free_artefact_places($ums_user_id).'<br>'.$sec_lang['npsecinfo3'].
            '<br>'.$sec_lang['npsecinfo4'].
            $sec_lang['angriffsgrenze'].': '.number_format($sec_angriffsgrenze * 100, 2, ",", ".").' / '.
            number_format($col_angriffsgrenze_final * 100, 2, ",", ".").'%'.
            '">';
    //$output.='<div style="text-align: left; width: 100%; background-color: #FFFFFF; color: #000000;">'.$sektorinfo.' '.$npchint.' '.$sec_data['name'].' Sektor '.$sf.' (NPC)</div>';


}

$sf = 2;
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE sector='$sf' ORDER BY score ASC, col ASC");
$alien_anz = mysqli_num_rows($db_daten);
$alien_nr = 0;
$planet_id = 1;
while ($row = mysqli_fetch_array($db_daten)) {
    //$planet_id=$row['system'];
    /*
    if($npc_sec_counter % 2==0){
        $planet_id*=2;
    }*/



    //Position auf der Karte berechnen
    $kradius = 9800;
    $alien_anzahl = 200;
    //$winkel=2*pi()/360*(360/$player_sector*(360/$sec_anzahl));
    $alienpos_x = cos(2 * pi() * ($alien_nr + 1) / ($alien_anz)) * -1;
    $alienpos_y = sin(2 * pi() * ($alien_nr + 1) / ($alien_anz));

    //radius-skalierung
    $alienpos_x = round($alienpos_x * $kradius);
    $alienpos_y = round($alienpos_y * $kradius);

    //positionierung im zentrum des div
    $alienpos_x = $mapcontent_width / 2 + $alienpos_x + 100;
    $alienpos_y = $mapcontent_height / 2 + $alienpos_y + 100;


    $output .= '<div style="left: '.$alienpos_x.'px; top: '.$alienpos_y.'px; position: absolute; width: 98px; height: 104px; 
		border: 0px solid #cd02d9; color: #FFFFFF; font-size: 14px;
		background: url('.$ums_gpfad.'s/p'.$planet_id.'.png);
		background-size: 90% auto;
		background-position: 5px 0px;
		background-repeat: no-repeat;
		" title="'.$row['spielername'].' ('.$sf.':'.$row['system'].')">';
    //punkte
    if ($punkte * $sec_angriffsgrenze <= $row['score']) {
        $csstag = ' text3';
    } else {
        $csstag = ' text2';
    }
    $tooltip = 'Punkte: '.number_format($row['score'], 0, "", ".").'<br>Gr&uuml;ner Punktewert: Ziel ist angreifbar, da oberhalb der Punkteangriffsgrenze.<br><br>Roter Punktewert: Ziel ist nicht angreifbar, da unterhalb der Punkteangriffsgrenze.';
    //echo '<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['score'], 0,"",".").'</div></td>';
    $output .= '<div class="tac'.$csstag.'" style="background-color: rgba(10,10,10,0.8); font-size: 12px; position: absolute; bottom: 0px; width: 50%;" title="'.$tooltip.'">'.formatMasseinheit($row['score']).'</div>';

    //kollektoren
    if ($col * $col_angriffsgrenze_final <= $row['col']) {
        $csstag = ' text3';
    } else {
        $csstag = ' text2';
    }
    $tooltip = wellenrechner($row['col'], $maxcol, 1);
    //echo '<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['col'], 0,"",".").'</div></td>';
    $output .= '<div class="tac'.$csstag.'" style="background-color: rgba(10,10,10,0.8); font-size: 12px; text-align: right; position: absolute; right: 0px; bottom: 0px; width: 50%;" title="'.$tooltip.'">'.number_format($row['col'], 0, "", ".").'</div>';
    //aktion
    $aktion = '<a class="text1" target="h" href="military.php?se='.$sf.'&sy='.$row['system'].'" title="Flotteneinsatz">F</a>';


    $output .= '<div class="tac'.$csstag.'" style="font-size: 12px; position: absolute; right: 0px; text-align: right; bottom: 20px; width: 100%;">'.$aktion.'</div>';


    //echo '</tr>';
    $output .= '</div>';

    $alien_nr++;
    $planet_id++;
    if ($planet_id > 20) {
        $planet_id = 1;
    }
}

//sektorendaten
//echo '<tr><td colspan="5" class="cell" height="24px">&nbsp;'.$sec_lang['angriffsgrenze'].': '.number_format($sec_angriffsgrenze*100, 2,",",".").' / '.number_format($col_angriffsgrenze_final*100, 2,",",".").'%</td></tr>';

//echo '</table>';
//rahmen_unten();

echo $output;
//$output='';

////////////////////////////////////////////////////////
//die einzelnen Spieler-Sektoren-Container erstellen
////////////////////////////////////////////////////////
$divid = 0;
$sec_anzahl = 78;
//for($y=0;$y<$sektoren_y;$y++){
//	for($x=0;$x<$sektoren_x;$x++){
for ($player_sector = 0;$player_sector < $sec_anzahl;$player_sector++) {

    //$temp = mysqli_fetch_array($sec_daten);
    //$sf=$temp['sector'];

    $sf = $player_sector + 3;

    //die daten des sektors auslesen
    $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_sector WHERE sec_id='$sf'");
    $sec_data = mysqli_fetch_array($db_daten);

    //Position des Sektors auf der Karte bestimmen
    $kradius = 9200;
    //$winkel=2*pi()/360*(360/$player_sector*(360/$sec_anzahl));
    $secpos_x = cos(2 * pi() * ($player_sector + 1) / ($sec_anzahl)) * -1;
    $secpos_y = sin(2 * pi() * ($player_sector + 1) / ($sec_anzahl));

    //radius-skalierung
    $secpos_x = round($secpos_x * $kradius);
    $secpos_y = round($secpos_y * $kradius);

    //positionierung im zentrum des div
    $secpos_x = $mapcontent_width / 2 + $secpos_x;
    $secpos_y = $mapcontent_height / 2 + $secpos_y;

    //um die breite des containers nach links/oben verschieben
    $secpos_x = $secpos_x - round($sektor_width / 4);
    $secpos_y = $secpos_y - round($sektor_height / 4);


    //fokus sicherheitshalb setzen, Problem mit Sektor 1
    if (!isset($sector_x_focus) || !isset($sector_y_focus)) {
        $sector_x_focus = $secpos_x;
        $sector_y_focus = $secpos_y;
    }

    //Rahmenfarbe je nach Sektortyp
    //$sec_color='#FFFFFF';
    //$sec_color='rgba(255,255,255,0.9)';
    if ($sec_data['npc'] == 1) {
        //$sec_color='#cd02d9';
        //$sec_color='rgba(205,2,218,0.9)';
    } elseif ($sec_data['sec_id'] == $ownsector) {
        //$sec_color='#00FF00';
        //$sec_color='rgba(0,160,0,0.9)';
        //$sector_x_focus=$x*$sektor_width;
        //$sector_y_focus=$y*$sektor_height;
        $sector_x_focus = $secpos_x;
        $sector_y_focus = $secpos_y;

    }

    //sektor-div �ffnen
    /*
    echo '<div id="'.$divid.'" style="position: absolute; z-index: 10; left: '.($x*$sektor_width).'px; top: '.($y*$sektor_height).'px; width: '.($sektor_width-2).'px; height: '.($sektor_height-2).'px;
        border: 1px solid '.$sec_color.'; padding: 0px; margin:0px; background: rgba(10,10,10,0.8);">';
    */

    /*
    echo '<div id="sec'.$player_sector.'" data-left="'.($secpos_x).'" data-top="'.($secpos_y).'"
        style="position: absolute; z-index: 10; left: '.($secpos_x).'px; top: '.($secpos_y).'px;
        width: '.($sektor_width-2).'px; height: '.($sektor_height-2).'px;
        border: 4px solid '.$sec_color.'; padding: 0px; margin:0px; background: rgba(10,10,10,0.8);">';
     */

    echo '<div id="sec'.$player_sector.'" data-left="'.($secpos_x).'" data-top="'.($secpos_y).'"
		style="position: absolute; z-index: 10; left: '.($secpos_x).'px; top: '.($secpos_y).'px; 
		width: '.($sektor_width).'px; height: '.($sektor_height).'px;
		padding: 0px; margin:0px; background-image: url(g/sec_border1.png);">';

    $rzadd = 0;
    if ($ownsector <> $sf) {
        $rzadd = 2;
    }


    if ($sec_data['npc'] == 1) {


    } else {
        $output = '';
        //$npc_sec_counter++;
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //spieler anzeigen
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //sektorkommandant feststellen
        $sector = $sf;
        $sksystem = issectorcommander();

        //die scandaten des spielers auslesen
        unset($scandaten);
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT zuser_id, rasse, allytag, ps FROM de_user_scan WHERE user_id='$ums_user_id'");
        $index = 0;
        while ($row = mysqli_fetch_array($db_daten)) {
            $scandaten[$index]['zuser_id'] = $row['zuser_id'];
            $scandaten[$index]['rasse'] = $row['rasse'];
            $scandaten[$index]['allytag'] = $row['allytag'];
            $scandaten[$index]['ps'] = $row['ps'];
            $index++;
        }

        //----------- Ally Feine/Freunde
        $allypartner = array();
        $allyfeinde = array();
        $query = "SELECT id FROM de_allys WHERE allytag='$ownally'";
        $allyresult = mysqli_query($GLOBALS['dbi'], $query);
        $at = mysqli_num_rows($allyresult);
        if ($at != 0) {
            //$allyid = mysqli_result($allyresult,0,"id");
            $row = mysqli_fetch_array($allyresult);
            $allyid = $row['id'];

            $allyresult = mysqli_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=$allyid or ally_id_2=$allyid) and (ally_id_1=id or ally_id_2=id)");
            while ($row = mysqli_fetch_array($allyresult)) {
                if ($ownally != $row["allytag"]) {
                    $allypartner[] = $row["allytag"];
                }
            }

            $allyresult = mysqli_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=$allyid or ally_id_angegriffener=$allyid) and (ally_id_angreifer=id or ally_id_angegriffener=id)");
            while ($row = mysqli_fetch_array($allyresult)) {
                if ($ownally != $row["allytag"]) {
                    $allyfeinde[] = $row["allytag"];
                }
            }
        }

        //sektormalus bei der attgrenze berechnen
        //zuerst anzahl der pc-sektoren auslesen
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT sec_id FROM de_sector WHERE npc=0 AND platz>1");
        $num = mysqli_num_rows($db_daten);
        if ($num < 1) {
            $num = 1;
        }

        //eigenen sektorplatz auslesen
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT platz FROM de_sector WHERE sec_id='$ownsector'");
        $row = mysqli_fetch_array($db_daten);
        $ownsectorplatz = $row["platz"];

        //sektorplatzunterschied berechnen
        $secplatz = $sec_data['platz'];
        $secplatzunterschied = $secplatz - $ownsectorplatz;
        if ($secplatzunterschied < 0) {
            $secplatzunterschied = 0;
        }

        //secmalus berechnen
        $sec_malus = $sv_sector_attmalus / $num * $secplatzunterschied;

        //secmalus darf nicht größer als maximum sein
        if ($sec_malus > $sv_sector_attmalus) {
            $sec_malus = $sv_sector_attmalus;
        }
        $sec_angriffsgrenze = $sv_attgrenze - $sv_attgrenze_whg_bonus + $sec_malus;

        //recyclotronbonus berechnen
        $rec_bonus = $sv_recyclotron_sector_bonus / $num * ($secplatz - 1);
        //recyclotronbonus darf nicht größer als das maximum sein
        if ($rec_bonus > $sv_recyclotron_sector_bonus) {
            $rec_bonus = $sv_recyclotron_sector_bonus;
        }

        //angriffsgrenze für die kollektoren berechnen
        if ($maxcol == 0) {
            $maxcol = 1;
        }
        $col_angriffsgrenze = $col * 100 / $maxcol;
        $col_angriffsgrenze_final = $col_angriffsgrenze / 100 * $sv_max_col_attgrenze;
        if ($col_angriffsgrenze_final > $sv_max_col_attgrenze) {
            $col_angriffsgrenze_final = $sv_max_col_attgrenze;
        }
        if ($col_angriffsgrenze_final < $sv_min_col_attgrenze) {
            $col_angriffsgrenze_final = $sv_min_col_attgrenze;
        }

        //anzeige ob der sektorstatus deaktiviert worden ist
        //if($secstatdisable==1 AND $ownsector==$sf) echo '<div class="info_box text2">'.$sec_lang['secstatdisable'].'</div><br>';

        $gesamtpunkte = 0;
        $anz = 0;


        //alles anzeigen
        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode, 
		de_login.last_login, de_login.user_id, de_user_data.score, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag, 
		de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.werberid, 
		de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04 
		FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_user_data.sector='$sf' ORDER BY $orderby ASC");

        $anz = mysqli_num_rows($db_daten);
        $gescol = 0;
        $gesamtpunkte = 0;
        //die einzelnen Spieler durchgehen
        while ($row = mysqli_fetch_array($db_daten)) {
            $gesamtpunkte += $row['score'];
            $gescol += $row['col'];
            $sector = $sf;

            //$output.='<tr>';
            /*
            $planet_id=$row['system'];
            if($pc_sec_counter % 2==0){
                $planet_id*=2;
            }*/

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //system inkl sk/bk und accountstatus
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            $systemstr = $row['system'];
            if ($row["lstatus"] == 2) {
                $systemstr = '['.$systemstr.']';
            }//gesperrt
            if ($row["lstatus"] == 3 and $row["delmode"] > 0) {
                $systemstr = '{'.$systemstr.'}';
            }//l�schmode
            if ($row["lstatus"] == 3 and $row["delmode"] == 0) {
                $systemstr = '('.$systemstr.')';
            }//umode

            if ($sksystem == $row['system']) {
                //überprüfen ob der sk auch bk ist, ist in 1-mann-sektoren möglich
                /*
                if($row['system']==$sec_data['bk'] AND $anz>1){
                    mysqli_query($GLOBALS['dbi'],"UPDATE de_sector set bk = 0 WHERE sec_id='$sector'");
                    $sec_data['bk']=0;
                }
                */
                $systemstr = '<span class="tc3">^'.$systemstr.'^</span>';
            }

            /*
            if($row['system']==$sec_data['bk']){
                $systemstr='<span class="tc2">&deg;'.$systemstr.'&deg;</span>';
            }
            */
            //$output.='<td class="cell tac" style="font-size: 10pt;">'.$systemstr.'</td>';

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //rang
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            $rang = "<img src='".$ums_gpfad.'g/r/'.$row['rang']."_g.gif' title='".$rangnamen1[$row['rang']]."'>";
            //$output.='<td class="cell tac">'.$rang.'</td>';


            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //spielername, geworben, details, im sektor online
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            $playername = umlaut($row['spielername']);
            if (strtotime($row["last_login"]) + 1800 > time() and $row["lstatus"] == 1) {
                $os = '&nbsp;*';
            } else {
                $os = '';
            }
            if ($ownsector == $sf and $secstatdisable == 0) {
                $osown = $os;
            } else {
                $osown = '';
            }
            $csstag = 'tc1';
            $playertooltip = '';
            if ($row["werberid"] == $owner_id) {
                $csstag = 'tc3';
                $playertooltip = $sec_lang['spielergeworben'];
            }
            $playercsstag = $csstag;
            /*
            $output.='<td class="cell tac" style="font-size: 10pt;"><a href="details.php?se='.$sector.'&sy='.$row['system'].'">
            <span title="'.$playertooltip.'" class="'.$csstag.'">'.$playername.$osown.'</span></a></td>';
            */
            //$output.='<div style="width: 100%; word-wrap: break-word; border-bottom: 1px solid #666666;">'.$playername.'</div>';

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //rasse
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            $knowrasse = 0;
            $playerstatus = 0;
            $rasse = '';
            //hat man scandaten �ber die rasse/allianz?
            unset($allytagscan);
            if (isset($scandaten)) {
                for ($i = 0;$i < count($scandaten);$i++) {
                    if ($scandaten[$i]['zuser_id'] == $row['user_id']) {
                        if ($scandaten[$i]['rasse'] > 0) {
                            $knowrasse = 1;
                        }
                        $playerstatus = $scandaten[$i]['ps'];
                        $allytagscan = $scandaten[$i]['allytag'];
                    }
                }
            }

            //im eigenen sektor sieht man alle rassen, au�er in sektor 1
            if ($sector == $ownsector and $ownsector > 1) {
                $knowrasse = 1;
            }


            $planet_id = 0;
            if ($knowrasse == 1) {
                if ($row['rasse'] == 1) {
                    $rasse = '<img style="margin-bottom: -4px" src="'.$ums_gpfad.'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
                }
                if ($row['rasse'] == 2) {
                    $rasse = '<img style="margin-bottom: -4px" src="'.$ums_gpfad.'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
                }
                if ($row['rasse'] == 3) {
                    $rasse = '<img style="margin-bottom: -4px" src="'.$ums_gpfad.'g/r/raceK.png" title="K�Tharr" width="16px" height="16px">';
                }
                if ($row['rasse'] == 4) {
                    $rasse = '<img style="margin-bottom: -4px" src="'.$ums_gpfad.'g/r/raceZ.png" title="Z�tah-ara" width="16px" height="16px">';
                }
                $planet_id = $row['rasse'];
            }

            //$output.='<td class="cell tac">'.$rasse.'</td>';
            //$output.='<div style="width: 100%; border-bottom: 1px solid #666666;">'.$rasse.'</div>';

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //allianz, sichtbarkeit durch ally, allyb�ndnis, scandaten, sektor
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            if ($row['status'] == 1) {
                $allytag = $row['allytag'];
            } else {
                $allytag = '';
            }
            $showallytag = '';
            $csstag = '';

            //festellen welche farbe das allytag hat
            //allypartner
            if (in_array($allytag, $allypartner)) {
                $csstag = 'tc3';
                $showallytag = $allytag;
            }
            //allyfeinde
            elseif (in_array($allytag, $allyfeinde)) {
                $csstag = 'tc2';
                $showallytag = $allytag;
            }
            //ganze andere ally, nichts anzeigen, au�er es gibt scandaten, oder es ist der eigene sektor
            elseif (($ownally != $allytag) or ($allytag == '') or ($ownally == '')) {
                //anzeige im eigenen sektor
                if ($ownsector == $sf and $ownsector > 1) {
                    $csstag = 'tc4';
                    $showallytag = $allytag;
                }
                //allytag aus den scandaten
                elseif (isset($allytagscan) && $allytagscan != '') {
                    //es gibt scandaten der ally
                    $csstag = 'tc4';
                    $showallytag = $allytagscan;
                } else {
                    //es gibt keine daten vom allytag
                    $showallytag = '&nbsp;';
                }
            }
            //eigene ally, tag anzeigen
            else {
                $csstag = 'tc1';
                $showallytag = $allytag;
            }

            //allytag
            if ($showallytag == '') {
                $showallytag = '&nbsp;';
            }

            /*
            if($showallytag!='&nbsp;'){
                $showallytag='<a href="ally_detail.php?allytag='.urlencode($showallytag).'"><span class="'.$csstag.'">'.$showallytag.'</span></a>';
            }
            */

            //$output.='<td class="cell tac" style="font-size: 10pt;">'.$showallytag.'</td>';
            if ($showallytag != '&nbsp;') {
                $showallytag = '<br><span class="'.$csstag.'" title="Allianz">'.$showallytag.'</span>';
            } else {
                $showallytag = '';
            }

            $output .= '<div style="position: absolute; width: 98px; height: 104px; border: 0px solid #FFFFFF; color: #FFFFFF; font-size: 14px;
				left: '.($player_std_pos[$row['system'] - 1][0] * 98 + 110).'px;
				top: '.($player_std_pos[$row['system'] - 1][1] * 160 + 136).'px;
				background: url('.$ums_gpfad.'g/derassenlogo'.$planet_id.'.png);
				background-size: 90% auto;
				background-position: 5px 0px;
				background-repeat: no-repeat;
				" title="'.umlaut($row['spielername']).' ('.$sf.':'.$row['system'].')">';


            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //Name / Rasse / Ally
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////

            $output .= '<div style="margin-left: 5px; width: 90px; word-wrap: break-word; background: rgba(10,10,70,0.8);">
				<a href="details.php?se='.$sector.'&sy='.$row['system'].'" target="h" class="'.$playercsstag.'">'.$playername.$osown.'</a>'.$showallytag.'</div>';
            /*
            if(!empty($rasse)){
                $output.='<div style="position: absolute; right: 0px; bottom: 16px;">'.$rasse.'</div>';
            }
            */


            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //punkte
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            if ($punkte * $sec_angriffsgrenze <= $row['score']) {
                $csstag = ' text3';
            } else {
                $csstag = ' text2';
            }

            $tooltip = 'Punkte: '.number_format($row['score'], 0, "", ".").'<br>Gr&uuml;ner Punktewert: Ziel ist angreifbar, da oberhalb der Punkteangriffsgrenze.<br><br>Roter Punktewert: Ziel ist nicht angreifbar, da unterhalb der Punkteangriffsgrenze.';

            //fett darstellen, wenn es Kopfgeld gibt
            if ($row['kg01'] > 0 || $row['kg02'] > 0 || $row['kg03'] > 0 || $row['kg04'] > 0) {
                $csstag .= ' fett';
                $tooltip .= '<br><br>Kopfgeld:';
                $tooltip .= '<br>M: '.number_format($row['kg01'], 0, "", ".");
                $tooltip .= '<br>D: '.number_format($row['kg02'], 0, "", ".");
                $tooltip .= '<br>I: '.number_format($row['kg03'], 0, "", ".");
                $tooltip .= '<br>E: '.number_format($row['kg04'], 0, "", ".");
            }


            //$output.='<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['score'], 0,"",".").'</div></td>';
            $output .= '<div class="tac'.$csstag.'" style="font-size: 12px; position: absolute; bottom: 0px; width: 50%;" title="'.$tooltip.'">'.formatMasseinheit($row['score']).'</div>';

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //kollektoren
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            if ($col * $col_angriffsgrenze_final <= $row['col']) {
                $csstag = ' text3';
            } else {
                $csstag = ' text2';
            }
            $tooltip = wellenrechner($row['col'], $maxcol, 0);
            //$output.='<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['col'], 0,"",".").'</div></td>';
            $output .= '<div class="tac'.$csstag.'" style="font-size: 12px; position: absolute; right: 0px; text-align: right; bottom: 0px; width: 50%;" title="'.$tooltip.'">'.number_format($row['col'], 0, "", ".").'</div>';

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            //aktion
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////

            $aktion = '
				<a class="text1" target="h" href="secret.php?a=s&zsec1='.$sector.'&zsys1='.$row['system'].'" title="Sonde starten">S</a>&nbsp;
				<a class="text1" target="h" href="secret.php?a=a&zsec2='.$sector.'&zsys2='.$row['system'].'" title="Agenteneinsatz">A</a>&nbsp;
				<a class="text1" target="h" href="military.php?se='.$sector.'&sy='.$row['system'].'" title="Flotteneinsatz">F</a>&nbsp;
				<a class="text1" target="h" href="secret.php?a=d&zsec1='.$sector.'&zsys1='.$row['system'].'"><img src="'.$ums_gpfad.'g/ps_'.$playerstatus.'.gif" border="0" title="Geheimdienstinformationen"></a>';

            $output .= '<div class="tac'.$csstag.'" style="font-size: 12px; position: absolute; right: 0px; text-align: right; bottom: 20px; width: 100%;">'.$aktion.'</div>';

            //$output.='</tr>';
            $output .= '</div>';//player
        }


        //$output.='</table>';

        if ($rzadd == 0) {
            $style = 'border: 1px solid #444444; background-color: #00DD00; color: #000000; width: 16px; display: inline-block; text-align: center;';
        } else {
            $style = 'border: 1px solid #444444; background-color: #f05a00; color: #000000; width: 16px; display: inline-block; text-align: center;';
        }


        $sec_angriffsgrenze = number_format($sec_angriffsgrenze * 100, 2, ",", ".").'%';
        $rec_bonus = number_format($rec_bonus, 2, ",", ".").'%';

        if ($anz > 0) {
            $infostr = '<img src="'.$ums_gpfad.'g/symbol12.png" border="0" style="margin-bottom: -4px; width: 20px; height: 20px;" title="'.
            $sec_lang['angriffsgrenze'].': '.$sec_angriffsgrenze.' / '.number_format($col_angriffsgrenze_final * 100, 2, ",", ".").'%<br>'.
            $sec_lang['kollektoren'].': '.number_format($gescol, 0, "", ".").'<br>'.
            $sec_lang['kollektorendurchschnitt'].': '.number_format($gescol / $anz, 2, ",", ".").'<br>'.
            $sec_lang['punktedurchschnitt'].': '.number_format($gesamtpunkte / $anz, 0, ",", ".").'<br>'.
            $sec_lang['platz'].' ('.$sec_lang['jetzt'].'): '.number_format($secplatz, 0, "", ".").'<br>'.
            $sec_lang['platz'].' ('.$sec_lang['gestern'].'): '.number_format($sec_data['platz_last_day'], 0, "", ".").'<br>'.
            $sec_lang['bewohntesysteme'].': '.number_format($anz, 0, "", ".").'<br>'.
            $sec_lang['recyclingbonus'].': '.$rec_bonus.'<br>'.
            $sec_lang['sektorartefakthaltezeit'].': '.number_format($sec_data['arthold'], 0, "", ".").'">';
        } else {
            $infostr = '<img src="'.$ums_gpfad.'g/symbol12.png" border="0" style="margin-bottom: -4px; width: 20px; height: 20px;" title="freier Sektor">';
        }

        if ($anz > 0) {
            $sektorinfo = '';
            $sektorinfo .= '<span title="Reisezeitmalus<br>Eigener Sektor: kein Malus<br>Andere Sektoren: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
            $sektorinfo .= ' '.$infostr.' ';
            $sektorinfo .= ' <span title="Sektornummer">S:'.$sf.'</span> <span title="Platz in der Sektorwertung">P:'.$sec_data['platz'].'</span>';
            $sektorinfo .= ' <span title="Sektorpunkte">SP:'.number_format($gesamtpunkte, 0, ",", ".");
            if ($sec_data['name'] != '') {
                $sektorinfo .= ' - '.$sec_data['name'];
            }
            $sektorinfo .= '</span>';
        } else {
            $sektorinfo = '';
            $sektorinfo = '';
            $sektorinfo .= '<span title="Reisezeitmalus<br>Eigener Sektor: kein Malus<br>Andere Sektoren: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
            $sektorinfo .= ' '.$infostr.' freier Sektor';
            $sektorinfo .= '</span>';
        }


        echo '<div style="text-align: left; width: 480px; height: 24px; margin-left: 120px; margin-top: 110px; 
			background-color: rgba(0,68,124,0.5); color: #DDDDDD; overflow: hidden;">'.$sektorinfo.'</div>';

        echo $output;


        if ($anz > 0) {
            //bild von der sternenbasis anzeigen

            $bed = $sec_data['techs'][1].$sec_data['techs'][2].$sec_data['techs'][3];
            $std = date("H");
            //1=sternenbasis 2=begrenzer 3=werft

            if ($bed == '100') {
                if ($std > 6 and $std < 20) {
                    $bn = 'sbtag.gif';
                } else {
                    $bn = 'sbnacht.gif';
                }
            } elseif ($bed == '110') {
                if ($std > 6 and $std < 20) {
                    $bn = 'sbtagsfb.gif';
                } else {
                    $bn = 'sbnachtsfb.gif';
                }
            } elseif ($bed == '101') {
                if ($std > 6 and $std < 20) {
                    $bn = 'sbtagw.gif';
                } else {
                    $bn = 'sbnachtw.gif';
                }
            } elseif ($bed == '111') {
                if ($std > 6 and $std < 20) {
                    $bn = 'sbtagsfbw.gif';
                } else {
                    $bn = 'sbnachtsfbw.gif';
                }
            }

            //sektorraumbasistooltip erzeugen
            if ($bed != '000') {
                $srbstr = '<br>Erweiterungen:';
                if ($sec_data['techs'][2] > 0) {
                    $srbstr .= '<br>- '.$sec_lang['sekbldg1'];
                }
                if ($sec_data['techs'][3] > 0) {
                    $srbstr .= '<br>- '.$sec_lang['sekbldg2'];
                }
                if ($sec_data['techs'][4] > 0) {
                    $srbstr .= '<br>- '.$sec_lang['sekbldg3'];
                }
                if ($sec_data['techs'][5] > 0) {
                    $srbstr .= '<br>- '.$sec_lang['sekbldg4'];
                }

                $stip = 'Sektorraumbasis'.$srbstr;
                $basestr = '<a href="'.$ums_gpfad.'g/big/'.strtoupper($bn).'" target="_blank"><img border="0" src="'.$ums_gpfad.'g/'.$bn.'" name="sb" title="'.$stip.'"></a>';
                //wenn es keine sektorraumbasis gibt string mit einem leerzeichen belegen
                if ($bed == '000') {
                    $basestr = '&nbsp;';
                }

                echo '<div style="position: absolute; width: 98px; height: 120px; border: 0px solid #FFFFFF; color: #FFFFFF; font-size: 14px;
					left: 260px;
					top: 260px;">'.$basestr.'</div>';


            }

            //Artefakte im Sektor

            //schauen ob es artefakte gibt
            $res = mysqli_query($GLOBALS['dbi'], "SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector='$sf'");
            $artnum = mysqli_num_rows($res);
            //if ($artnum>0 OR $bed!='000')//artefakt vorhanden, oder raumbasis gebaut
            //{

            $artstr = '';
            $c = 0;

            if ($artnum > 0) {
                include_once "inc/artefakt.inc.php";
            }


            while ($row = mysqli_fetch_array($res)) {
                //artefakttooltip bauen
                $desc = $row["artdesc"];
                $desc = str_replace("{WERT1}", number_format($sv_artefakt[$row["id"] - 1][0], 2, ",", "."), $desc);
                $desc = str_replace("{WERT2}", number_format($sv_artefakt[$row["id"] - 1][1], 0, "", "."), $desc);
                $desc = str_replace("{WERT3}", number_format($sv_artefakt[$row["id"] - 1][2], 0, "", "."), $desc);
                $desc = str_replace("{WERT4}", number_format($sv_artefakt[$row["id"] - 1][3], 0, "", "."), $desc);
                $desc = str_replace("{WERT5}", number_format($sv_artefakt[$row["id"] - 1][4], 0, "", "."), $desc);
                $desc = str_replace("{WERT6}", number_format($sv_artefakt[$row["id"] - 1][5], 2, ",", "."), $desc);


                $atip[$c] = '<font color=#'.$row["color"].'>'.$row["artname"].'</font><br>'.$desc;

                $artstr .= '<a href="help.php?a=1" target="_blank" title="'.umlaut($atip[$c]).'"><img src="'.$ums_gpfad.'g/sa'.$row["picid"].'.gif" border="0"></a>&nbsp;';
                $c++;
            }
            if ($artstr != '') {
                echo '<div style="position: absolute; width: 98px; height: 120px; border: 0px solid #FFFFFF; color: #FFFFFF; font-size: 14px;
					left: 430px;
					top: 280px;">'.$artstr.'</div>';
            }
        }
    }
    echo '</div>';//sektor ende
}

///////////////////////////////////////////////////////////////////////////
// erforschbare/erforschte Systeme für Handel/Missionen/Events
///////////////////////////////////////////////////////////////////////////

$sichtbare_systeme = array();
$immer_sichtbare_systeme = array();
$erforschte_systeme = array();
$erforschte_systeme_koordinaten = array();

//Kanten laden
$kanten = array();
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_map_kanten");
while ($row = mysqli_fetch_array($db_daten)) {
    $kanten[] = array($row['knoten_id1'],$row['knoten_id2']);
}

//die erforschten Systeme laden
$sql = "SELECT map_id FROM de_user_map WHERE user_id='".$_SESSION['ums_user_id']."' AND known_since>0 AND known_since<'".time()."';";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
while ($row = mysqli_fetch_array($db_daten)) {
    //sie sind sichtbar und erforscht
    $sichtbare_systeme[] = $row['map_id'];
    $erforschte_systeme[] = $row['map_id'];
}

//die sichtbaren Systeme um Systeme ergänzen, die immer sichtbar sind
$sql = "SELECT id FROM de_map_objects WHERE always_visible=1 OR system_typ=4;";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
while ($row = mysqli_fetch_array($db_daten)) {
    if (!in_array($row['id'], $sichtbare_systeme)) {
        $sichtbare_systeme[] = $row['id'];
    }
    $immer_sichtbare_systeme[] = $row['id'];
}

//die sichtbaren Systeme um die Systeme ergänzen, die über Kanten mit erforschten Systemen verknüpft sind
for ($i = 0;$i < count($erforschte_systeme);$i++) {
    //für jedes System alle Kanten durchgehen
    $map_id = $erforschte_systeme[$i];
    //echo 'map_id: '.$map_id;
    for ($k = 0; $k < count($kanten);$k++) {
        //echo ' kanten_ids: '.$kanten[$k][0].'/'.$kanten[$k][1];
        //knoten1 testen
        if ($map_id == $kanten[$k][0]) {
            //echo 'gefunden 1';
            if (!in_array($kanten[$k][1], $sichtbare_systeme)) {
                $sichtbare_systeme[] = $kanten[$k][1];
                //echo 'gefunden 1a';
            }
        }

        //knoten2 testen
        if ($map_id == $kanten[$k][1]) {
            //echo 'gefunden 2';
            if (!in_array($kanten[$k][0], $sichtbare_systeme)) {
                $sichtbare_systeme[] = $kanten[$k][0];
                //echo 'gefunden 2a';
            }
        }
    }
}

//Kanten Koordinaten bestimmen, nur erforschte Systeme haben Kanten
$kanten_koordinaten = array();

//////////////////////////////////////////////////////////////////////////////////////////////////////
// allge Gebäude der Karte laden und in ein Array packen
//////////////////////////////////////////////////////////////////////////////////////////////////////
$bldg = array();
$sql = "SELECT * FROM de_user_map_bldg WHERE user_id='".$_SESSION['ums_user_id']."';";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
while ($row = mysqli_fetch_array($db_daten)) {
    $bldg[$row['map_id']][$row['field_id']]['bldg_id'] = $row['bldg_id'];
    $bldg[$row['map_id']][$row['field_id']]['bldg_level'] = $row['bldg_level'];
    $bldg[$row['map_id']][$row['field_id']]['bldg_time'] = $row['bldg_time'];
}


//Systeme laden
//$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects LEFT JOIN de_user_map ON(de_map_objects.id=de_user_map.map_id);");
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_map_objects");
$alien_anz = mysqli_num_rows($db_daten);

//Position auf der Karte berechnen
$alien_nr = 0;
$kradius = 500;
$planet_id = 1;
while ($row = mysqli_fetch_array($db_daten)) {
    //klasse restaurieren
    $data = unserialize($row['data']);

    $output = '';

    /*
    //$winkel=2*pi()/360*(360/$player_sector*(360/$sec_anzahl));
    $alienpos_x=cos(2*pi()*($alien_nr+1)/($alien_anz))*-1;
    $alienpos_y=sin(2*pi()*($alien_nr+1)/($alien_anz));

    //radius-skalierung
    $alienpos_x=round($alienpos_x*$kradius);
    $alienpos_y=round($alienpos_y*$kradius);
    */

    /*
    $alienpos_x=round($data->getSystemPosX()*150);
    $alienpos_y=round($data->getSystemPosY()*150);
    */

    $alienpos_x = round($data->getSystemPosX() * 800);
    $alienpos_y = round($data->getSystemPosY() * 800);



    //positionierung im zentrum des div
    $alienpos_x = $mapcontent_width / 2 + $alienpos_x - 200;
    $alienpos_y = $mapcontent_height / 2 + $alienpos_y + 000;

    //hat man die benötigte Technologie?
    if (hasTech($pt, 25)) {
        $tech_info = '';
    } else {
        $tech_info = '<br>Dir fehlt die n&ouml;tige Technologie um zu diesem System eine Verbindung aufbauen zu k&ouml;nnen.';
    }

    //$system_name='Erforschtes System (#'.$row['id'].')';


    if (in_array($row['id'], $erforschte_systeme) || in_array($row['id'], $immer_sichtbare_systeme)) {
        $system_name = $data->getSystemName().' (#'.$row['id'].')';

        //immer sichtbare Systeme haben einen Sonderstatus
        if (in_array($row['id'], $immer_sichtbare_systeme)) {
            $bg_image = $ums_gpfad.'s/sym3.png';

        } else {
            $bg_image = $ums_gpfad.'s/p'.$planet_id.'.png';

            $planet_id++;
            if ($planet_id > 20) {
                $planet_id = 1;
            }
        }

        //$erforschte_systeme_koordinaten[$row['id']]=array($alienpos_x, $alienpos_y);
    } else {
        $bg_image = 'g/de_vs_us.png';
        $system_name = 'Unerforschtes System (#'.$row['id'].')';
    }

    $sichtbare_systeme_koordinaten[$row['id']] = array($alienpos_x, $alienpos_y);

    $output .= '<div style="left: '.$alienpos_x.'px; top: '.$alienpos_y.'px; position: absolute; width: 98px; height: 104px; 
		border: 0px solid #cd02d9; color: #FFFFFF; font-size: 14px;
		background: url('.$bg_image.');
		background-size: 90% auto;
		background-position: 5px 0px;
		background-repeat: no-repeat;
		cursor: pointer;
		" title="'.$system_name.$tech_info.'" onclick="switch_iframe_main_container(\'map_system.php?id='.$row['id'].'\')">';

    $output .= '<div style="background-color: rgba(0,0,0,0.6);">';
    ///////////////////////////////////////////
    //Felder durchgehen und anzeigen
    ///////////////////////////////////////////

    $output .= '<div style="display: flex;">';

    if (!isset($data->special_system)) {
        $data->special_system = 0;
    }

    if ($data->special_system < 1 && in_array($row['id'], $erforschte_systeme) && !in_array($row['id'], $immer_sichtbare_systeme)) {
        for ($i = 0;$i < count($data->fields);$i++) {

            ///////////////////////////////////////////
            //Rahmenfarbe definieren
            ///////////////////////////////////////////
            //Blocker
            if (isset($data->fields[$i][1])) {
                $border = 'border: 1px solid #FF0000;';
            } else {
                $border = '';
            }

            ///////////////////////////////////////////
            //Feld anzeigen
            ///////////////////////////////////////////
            //$output.='<div style="height: 50px; width: 300px; border: 1px solid '.$bordercolor.'; margin-bottom: 10px; box-sizing: border-box; padding: 5px; cursor: pointer;" onclick="location.href=\'map_system.php?id='.$data->system_id.'&fieldid='.$i.'\'">';
            ///////////////////////////////////////////
            //Blocker anzeigen
            ///////////////////////////////////////////
            /*
            if(isset($data->fields[$i][1])){
                $output.='Feldblocker: '.$data->fields[$i][1][1].'x '.$GLOBALS['map_field_blocker'][$data->fields[$i][1][0]]['name'].'<br>';
            }*/

            ///////////////////////////////////////////
            //Gebäude anzeigen
            ///////////////////////////////////////////
            /*
            for($b=0;$b<count($data->playerBldg);$b++){
                if($data->playerBldg[$b]['field_id']==$i){
                    //wird das Gebäude gerade ausgebaut?
                    if(time()<$data->playerBldg[$b]['bldg_time']){
                        //Ausbau läuft
                        $output.=$GLOBALS['map_buildings'][$data->playerBldg[$b]['bldg_id']]['name'].' (Ausbau auf Stufe '.($data->playerBldg[$b]['bldg_level']).': <span id="build_counter'.$i.'"></span>)';
                        $output.='<script type="text/javascript">ang_countdown('.($data->playerBldg[$b]['bldg_time']-time()).',"build_counter'.$i.'",0)</script>';
                        $output.='<br>';
                    }else{
                        //wird nicht ausgebaut
                        $output.=$GLOBALS['map_buildings'][$data->playerBldg[$b]['bldg_id']]['name'].' (Stufe '.$data->playerBldg[$b]['bldg_level'].')<br>';
                    }
                }
            }*/


            ///////////////////////////////////////////
            //Feld-Ressource anzeigen
            ///////////////////////////////////////////
            //Gebäudestufe bestimmen
            $stufeninfo = '';
            if (isset($bldg[$row['id']][$i]) && $bldg[$row['id']][$i] > 0) {
                $stufeninfo = '<br>'.$bldg[$row['id']][$i]['bldg_level'];
                //testen ob es gerade im Bau ist, dann die Farbe ändern
                if ($bldg[$row['id']][$i]['bldg_time'] > time()) {
                    $stufeninfo = '<span style="color: yellow;">'.$stufeninfo.'</span>';
                }
            }

            //if(!in_array($row['id'],$immer_sichtbare_systeme)){
            if ($GLOBALS['map_field_typ'][$data->fields[$i][0]]['name'] != '-' || $i == 0) {

                //Grafik bestimmen
                if ($i > 0) {
                    $filename_nr = $data->fields[$i][0];
                    if ($filename_nr < 10) {
                        $filename_nr = '0'.$filename_nr;
                    }
                    $output .= '<div style="text-align:center; margin-right: 1px; font-size: 10px; line-height: 10px;"><img style="width: 18px; box-sizing: border-box; border-radius: 5px;'.$border.'" src="g/ele'.$filename_nr.'.gif" title="'.$GLOBALS['map_field_typ'][$data->fields[$i][0]]['name'].'">'.$stufeninfo.'</div>';
                } else {
                    //außenposten
                    $output .= '
						<div style="font-size: 10px; line-height: 10px; text-align:center;">
							<div style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #999999; text-align: center; box-sizing: border-box; border-radius: 5px;" title="Au&szlig;enposten">A</div>
							'.str_replace('<br>', '', $stufeninfo).'
						</div>';
                }

            } else {
                //Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
                if (isset($bldg[$row['id']][$i]['bldg_id']) && isset($GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id'])) {

                    //$GLOBALS['greek_chars']
                    /*
                    $output.='
                    <div style="text-align:center; margin-right: 1px; font-size: 10px; line-height: 10px;">
                        <img style="width: 18px; height: 18px; box-sizing: border-box; border-radius: 5px;'.$border.'" src="'.$ums_gpfad.'g/r/'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id'].'_g.gif" title="'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['name'].'">
                        '.$stufeninfo.'
                    </div>';
                    */

                    $output .= '
						<div style="font-size: 10px; line-height: 10px; text-align:center;">
							<div style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #999999; text-align: center; box-sizing: border-box; border-radius: 5px;" title="'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['name'].'">'.$GLOBALS['greek_chars'][$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id']].'</div>
							'.str_replace('<br>', '', $stufeninfo).'
						</div>';


                } else {
                    //$output.='<div title="keine Rohstoffe" style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;'.$border.'">-'.$bldg[$row['id']][$i]['bldg_id'].'</div>';
                    $output .= '<div title="keine Rohstoffe" style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;'.$border.'">-</div>';
                }




            }


            if (($i + 1) % 5 == 0) {
                $output .= '</div><div style="display: flex;">';
            }
            //}

            //$output.='</div>';

            //Test auf Loot
            //$output.=print_r($immer_sichtbare_systeme,true);

        }
    }


    $output .= '</div>';

    $output .= '</div>';



    //Stufe ausgeben
    //$output.='<div>S'.$data->getSystemLevel().'</div>';

    //Cluster ausgeben
    //$output.='<div>C'.$row['cluster_x'].'/'.$row['cluster_y'].'</div>';

    //Kanten ausgeben
    //$output.='<div>K'.$data->getSystemLevel().'</div>';

    //echo '</tr>';
    $output .= '</div>';

    //if($row['user_id']<1 && $data->always_visible==0){
    if (!in_array($row['id'], $sichtbare_systeme)) {
        $output = '';
    }

    echo $output;

    $alien_nr++;
}

//alle Kanten einzeichnen

$output = '<svg data-svg="1" width="'.$mapcontent_width.'" height="'.$mapcontent_width.'">';

for ($i = 0;$i < count($erforschte_systeme);$i++) {
    //für jedes System alle Kanten durchgehen
    $map_id = $erforschte_systeme[$i];
    //echo 'map_id: '.$map_id;
    for ($k = 0; $k < count($kanten);$k++) {
        //$output.=' kanten_ids: '.$kanten[$k][0].'/'.$kanten[$k][1];
        //knoten testen
        if ($map_id == $kanten[$k][0] || $map_id == $kanten[$k][1]) {
            //Verbindungslinie zeichnen
            $x1 = $sichtbare_systeme_koordinaten[$kanten[$k][0]][0] + 50;
            $y1 = $sichtbare_systeme_koordinaten[$kanten[$k][0]][1] + 50;

            $x2 = $sichtbare_systeme_koordinaten[$kanten[$k][1]][0] + 50;
            $y2 = $sichtbare_systeme_koordinaten[$kanten[$k][1]][1] + 50;

            $output .= '<line data-svg="1" x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" style="stroke:rgb(102,51,153);stroke-width:1" />';
        }
    }
}


$output .= '</svg>';
echo $output;



echo '</div>';//mapcontent
echo '</div>';//mapcontainer

/*
if($_SESSION['ums_user_id']==99966){
    die();
}
*/

$showx = 1;
$showy = 1;

?>
<script type="text/javascript">
var inmove=0;
var mapdata=new Array();
var zoomfactor=1.0;
var neworigin='';
var newleft=<?php echo ($sector_x_focus - 500) * -1; ?>;
var newtop=<?php echo ($sector_y_focus - 500) * -1; ?>;
var map_left=<?php echo ($sector_x_focus - 500) * -1; ?>;
var map_top=<?php echo ($sector_y_focus - 500) * -1; ?>;
<?php
if (isset($_COOKIE['map_zoomfactor']) && !empty($_COOKIE['map_zoomfactor']) && isset($_COOKIE['map_newleft']) && !empty($_COOKIE['map_newleft']) && isset($_COOKIE['map_newtop']) && !empty($_COOKIE['map_newtop']) && isset($_COOKIE['map_neworigin']) && !empty($_COOKIE['map_neworigin'])) {
    echo 'zoomfactor='.$_COOKIE['map_zoomfactor'].';';
    echo 'newleft="'.$_COOKIE['map_newleft'].'";';
    echo 'newtop="'.$_COOKIE['map_newtop'].'";';
    echo 'neworigin="'.$_COOKIE['map_neworigin'].'";';

    echo 'set_map_data();';
} else {
    ?>
$('#mapcontent').css('left',<?php echo ($sector_x_focus - 500) * -1; ?>);
$('#mapcontent').css('top',<?php echo ($sector_y_focus - 250) * -1; ?>);
<?php
}
?>

$('#mapcontent').bind('mousewheel DOMMouseScroll', function (event) {
	if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
		if(isNaN(zoomfactor)){
			zoomfactor=1.0;
			console.log('NaN');
		}
		//zoom in
		zoomfactor=zoomfactor+0.02;
		
		if(zoomfactor>1.4){
			zoomfactor=1.4;
		}else{
			set_map_data();
		}
	}
	else {
		//zoom out
		zoomfactor=zoomfactor-0.02;
		
		if(zoomfactor<0.04){
			zoomfactor=0.04;
		}else{
			set_map_data();
		}
	}
	
	//console.log('ZF: '+zoomfactor);
	setCookie('map_zoomfactor', zoomfactor);
});

function set_map_data(){
	$('#mapcontent').css('left', newleft+"px");
	$('#mapcontent').css('top', newtop+"px");
	$('#mapcontent').css('transform-origin', neworigin);	

	$('#mapcontent').css({
	  'transform'         : 'scale(' + zoomfactor + ')'
	});		
}

jQuery(document).ready(function(){
	$('#mapcontent').mousemove(function(e){
		/*
		var mapcontent_top=$('#mapcontent').css('top');
		mapcontent_top=parseFloat(mapcontent_top.replace("px", ""))*-1;
		var top = (mapcontent_top+e.pageY - $(window).scrollTop())/1000;
		newtop=(mapcontent_top+e.pageY - $(window).scrollTop())*-1;
		
		var mapcontent_left=$('#mapcontent').css('left');
		mapcontent_left=parseFloat(mapcontent_left.replace("px", ""))*-1;
		var left = (mapcontent_left+e.pageX - $(window).scrollLeft())/1000;
		newleft=(mapcontent_left+e.pageX - $(window).scrollLeft());
		*/
		//left=(e.offsetX+e.pageX)/1000;
		//top=(e.offsetY+e.pageY)/1000;
		//console.log(e);
		//console.log(e.target.id);
		var dataleft=0;
		var datatop=0;
		
		if(e.target.id){
			//console.log($('#'+e.target.id).data('left'));
			if($('#'+e.target.id).data('left')){
				dataleft=parseInt($('#'+e.target.id).data('left'));
				datatop=parseInt($('#'+e.target.id).data('top'));
				//console.log('A: '+dataleft);
			}
		}
		//console.log(e.target.style['left']);
		if(e.offsetX>510 || dataleft>0){
			map_left=(e.offsetX+dataleft)/1000;
			newleft=(e.offsetX+dataleft-e.clientX)*-1;
		}
		
		if(e.offsetY>510 || datatop>0){
			map_top=(e.offsetY+datatop)/1000;
			newtop=(e.offsetY+datatop-e.clientY)*-1;
		}
		
		neworigin=map_left+"% "+map_top+"%";
		//console.log(neworigin);
		/*
		if(zoomfactor>=1){
			//console.log(map_left+"/"+map_top);
			console.log(newleft);
			//console.log(neworigin);
			//console.log(newleft+(newleft*(zoomfactor-1)/100));
			//console.log(e.offsetX);
		}else{
			//console.log(neworigin);
			//console.log(map_left+"/"+map_top);
			//console.log(e.offsetX);
			console.log(newleft);
			//console.log(newleft+(newleft/(100*zoomfactor)/100));
		}
		*/
		//console.log(newleft);
		//$('#mapcontent').css('transform-origin', neworigin);
	}); 
})

$(function(){

<?php
/*
  var transformFix_old1 = function(el, e) {
    var offset = el.offset(),
        x = e.pageX - offset.left,
        y = e.pageY - offset.top,
        transform = el.css('transform'),
        dx, dy;

    el.css('transform', '');
    offset = el.offset();
    dx = e.pageX - offset.left - x;
    dy = e.pageY - offset.top - y;
    el.css('transform', transform);

    return {
      left : x + dx,
      top : y + dy
    };
  };
*/
?>
  var transformFix = function(el, e) {
    return {
	  left: -parseInt(el.css("left")) + e.pageX,
	  top: -parseInt(el.css("top")) + e.pageY
	};
  }

  $('#mapcontent').draggable()
	.mousedown(function(e) {
		var el = $(this);
		el.draggable('option', { 
		  cursorAt: transformFix(el, e)
		});
	})
	.mouseup(function(e) {
		var el = $(this);
		el.draggable('option', { 
		  cursorAt: transformFix(el, e)
		});

		setCookie('map_newleft', parseInt($('#mapcontent').css('left'),10));
		setCookie('map_newtop',  parseInt($('#mapcontent').css('top'),10));
		setCookie('map_neworigin', $('#mapcontent').css('transform-origin'));
	});
});

$.extend($.support,{touch: "ontouchend" in document });
$.fn.addTouch = function() {if ($.support.touch){this.each(function(i,el){el.addEventListener("touchstart", iPadTouchHandler, false);                         el.addEventListener("touchmove", iPadTouchHandler, false);                         el.addEventListener("touchend", iPadTouchHandler, false);                         el.addEventListener("touchcancel", iPadTouchHandler, false);                 });         } }; 
var lastTap = null;

$('#mapcontent').addTouch();

window.onresize = setsize;

function setsize(){
	var height=document.getElementById("mapcontainer").offsetHeight-70;
	var left=(document.getElementById("mapcontainer").offsetWidth-880)/2;
	$('#mainarea').css('height', height+'px');  
	$('#mainarea').css('left', left+'px');
}

setsize();

</script>
<?php
die('</body></html>');

function wellenrechner($kol, $maxcol, $npcsec)
{
    global $sec_lang, $col, $sv_min_col_attgrenze, $sv_max_col_attgrenze, $ums_premium, $sv_kollie_klaurate;
    $str = 'Kollektoren-Wellenrechner';
    $str .= "<table width=200px border=0 cellpadding=0 cellspacing=1><tr align=center><td width=15%>&nbsp</td><td width=17%>".$sec_lang['kollektoren']."</td></tr>";
    $str .= "<tr align=center><td>&nbsp;</td><td>".number_format($kol, 0, ',', '.')."</td></tr>";
    if ($ums_premium == 1) {
        $owncol = $col;
        if ($maxcol == 0) {
            $maxcol = 1;
        }
        for ($we = 0; $we < 5; $we++) {
            if ($owncol > $maxcol) {
                $maxcol = $owncol;
            }

            if ($npcsec == 0) {

                $col_angriffsgrenze = $owncol * 100 / $maxcol;
                $col_angriffsgrenze = $col_angriffsgrenze / 100 * $sv_max_col_attgrenze;
                if ($col_angriffsgrenze > $sv_max_col_attgrenze) {
                    $col_angriffsgrenze = $sv_max_col_attgrenze;
                }
                if ($col_angriffsgrenze < $sv_min_col_attgrenze) {
                    $col_angriffsgrenze = $sv_min_col_attgrenze;
                }
            } else {
                $col_angriffsgrenze = $sv_min_col_attgrenze;
            }

            if ($kol < $col_angriffsgrenze * $owncol) {
                $colclass = "text2";
            } else {
                $colclass = "text3";
            }

            $str .= "<tr align=center><td nowrap>".($we + 1).". ".$sec_lang['welle']."</td><td class=".$colclass.">".
            number_format(floor($kol * $sv_kollie_klaurate), 0, ',', '.')."</td></tr>";
            $owncol = $owncol + floor($kol * $sv_kollie_klaurate);
            $kol = $kol - floor($kol * $sv_kollie_klaurate);
        }

    } else {
        $str .= "<tr><td colspan=2>".$sec_lang['painfo']."</td></tr>";
    }

    //info bzgl. erobern/zerst�ren von kollektoren
    $str .= "</table><br>Gr&uuml;ner Kollektorenwert: Kollektoren liegen &uuml;ber der Kollektorenangriffsgrenze und werden erobert.<br><br>Roter Kollektorenwert: Kollektoren liegen unter der Kollektorenangriffsgrenze und werden zerst&ouml;rt.";

    return ($str);
}
?>
</body>
</html>