<?php
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_sector.lang.php';
include "functions.php";
include_once "inc/artefakt.inc.php";

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


$rangnamen = array("Der Erhabene", "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");

if ($row["status"] == 1) {
    $ownally = $row["allytag"];
}
//schauen ob er die whg hat und dann die attgrenze anpassen
if ($techs[4] == 0) {
    $sv_attgrenze_whg_bonus = 0;
}

//owner id auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT owner_id FROM de_login WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$owner_id = intval($row["owner_id"]);

//spieler sortiert auslesen
$orderby = '`system`';
if ($secsort == '1') {
    $orderby = 'col';
} elseif ($secsort == '2') {
    $orderby = 'score';
}

//maximale anzahl von kollektoren auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0");
$row = mysqli_fetch_array($db_daten);
$maxcol = $row['maxcol'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Karte</title>
  <link rel="stylesheet" type="text/css" href="/gp/de-map.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-map.css'); ?>">
  <script>
    var ownSector = <?php echo ($ownsector > 1) ? $ownsector : 3; ?>;
  </script>
  <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="js/ang_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/ang_fn.js');?>"></script>
</head>
<body>

<div id="viewport">
  <div id="map">

<?php
//////////////////////////////////////////////////////////////////////////////
//links oben Spielname, Servertag und Servername
//////////////////////////////////////////////////////////////////////////////
echo '<div id="gamename" style="top:40000px; left:40000px;">die ewigen</div>';
echo '<div id="serverdesc" style="top:40512px; left:40000px;">'.$sv_server_name.' '.$sv_server_tag.'</div>';

//////////////////////////////////////////////////////////////////////////////
//rechts oben die struktur
//////////////////////////////////////////////////////////////////////////////
//es werden die Sektorartefakte angezeigt die in Sektor -1 sind
$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector=?", [-1]);
$artstr = '';
while ($row = mysqli_fetch_array($res)) {
    //artefakttooltip bauen
    $desc = $row["artdesc"];
    $desc = str_replace("{WERT1}", number_format($sv_artefakt[$row["id"] - 1][0], 2, ",", "."), $desc);
    $desc = str_replace("{WERT2}", number_format($sv_artefakt[$row["id"] - 1][1], 0, "", "."), $desc);
    $desc = str_replace("{WERT3}", number_format($sv_artefakt[$row["id"] - 1][2], 0, "", "."), $desc);
    $desc = str_replace("{WERT4}", number_format($sv_artefakt[$row["id"] - 1][3], 0, "", "."), $desc);
    $desc = str_replace("{WERT5}", number_format($sv_artefakt[$row["id"] - 1][4], 0, "", "."), $desc);
    $desc = str_replace("{WERT6}", number_format($sv_artefakt[$row["id"] - 1][5], 2, ",", "."), $desc);


    $atip = '<font color=#'.$row["color"].'>'.$row["artname"].'</font><br>'.$desc;

    $artstr .= '
    <div onclick="switch_iframe_main_container(\'help.php?a=1\')" title="'.umlaut($atip).'">
        <img src="'.'gp/'.'g/sa'.$row["picid"].'.gif" style="width: 50px; height: 50px;">
    </div>';
}

if(!empty($artstr)) {
    $artstr = '<div style="position: absolute; bottom: 18px; left: 22px; display: flex; gap: 4px; z-index: 10;">'.$artstr.'</div>';
}

//Ausgabe structure_override_code
$structureOverrideCode = '';
$deSystemResult=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system LIMIT 1");
$deSystem=mysqli_fetch_assoc($deSystemResult);

echo '
<script>
const socRaenge = ["alpha","beta","gamma","delta","epsilon","zeta","eta","theta","iota","kappa","lambda","my","ny","xi","omikron","pi","rho","sigma","tau","ypsilon","phi","chi","psi","omega"];
const structureOverrideCode = [5,17,0,23,11,3,19,8,14,22,1,6,12,20,9,4,16,7,2,15,13,21,18,10,0,23,5,11,17,3,19,8,14,22,1,6,12,20,9,4,16,2];

// HTML5 Audio Player für Greek Letters
class GreekLetterPlayer {
  constructor() {
    this.audio = new Audio();
    this.currentIndex = 0;
    this.isPlaying = false;
    this.playlist = this.buildPlaylist();
    
    console.log("GreekLetterPlayer initialized");
    console.log("structureOverrideCode:", structureOverrideCode);
    console.log("Playlist:", this.playlist);
    
    this.audio.addEventListener("ended", () => this.playNext());
    this.audio.addEventListener("error", (e) => {
      console.error("Audio error:", e);
      console.error("Failed to load:", this.audio.src);
    });
    this.audio.addEventListener("loadstart", () => console.log("Loading:", this.audio.src));
    this.audio.addEventListener("canplay", () => console.log("Can play:", this.audio.src));
  }
  
  buildPlaylist() {
    const playlist = structureOverrideCode.map(index => {
      const filename = socRaenge[index] || "alpha"; // Fallback zu alpha
      return `/sound/greek_letters/${filename}.mp3`;
    });
    
    // Fallback wenn structureOverrideCode leer ist
    if (playlist.length === 0) {
      console.log("No structureOverrideCode found, using test playlist");
      return ["/sound/greek_letters/alpha.mp3", "/sound/greek_letters/beta.mp3"];
    }
    
    return playlist;
  }
  
  play() {
    console.log("Play button pressed");
    console.log("Playlist length:", this.playlist.length);
    
    if (this.playlist.length === 0) {
      console.log("No playlist available");
      return;
    }
    
    this.isPlaying = true;
    this.currentIndex = 0;
    this.loadAndPlay();
  }
  
  stop() {
    console.log("Stop button pressed");
    this.isPlaying = false;
    this.audio.pause();
    this.audio.currentTime = 0;
    this.currentIndex = 0;
  }
  
  playNext() {
    console.log("Playing next track");
    if (!this.isPlaying) return;
    
    this.currentIndex++;
    if (this.currentIndex >= this.playlist.length) {
      this.currentIndex = 0; // Loop zurück zum Anfang
    }
    
    this.loadAndPlay();
  }
  
  loadAndPlay() {
    if (this.playlist[this.currentIndex]) {
      console.log("Loading and playing:", this.playlist[this.currentIndex]);
      this.audio.src = this.playlist[this.currentIndex];
      this.audio.load();
      
      // User interaction ist erforderlich für autoplay
      this.audio.play().then(() => {
        console.log("Playback started successfully");
      }).catch(e => {
        console.error("Play error:", e);
        console.error("This might be due to browser autoplay policy");
        alert("Audio playback failed. This might be due to browser autoplay restrictions. Please interact with the page first.");
      });
    }
  }
  
  getCurrentTrack() {
    return this.playlist[this.currentIndex] || null;
  }
}

// Globalen Player erstellen
window.greekPlayer = new GreekLetterPlayer();

</script>
';

//die Struktur darstellen
echo '<div style="position: absolute; top:40000px; right:40000px;">
    <div style="
        background: linear-gradient(45deg, #00ff41, #0099ff, #ff0080, #00ff41);
        background-size: 400% 400%;
        animation: sci-fi-border 3s ease-in-out infinite;
        padding: 8px;
        border-radius: 15px;
        box-shadow: 
            0 0 20px rgba(0, 255, 65, 0.5),
            0 0 40px rgba(0, 153, 255, 0.3),
            inset 0 0 20px rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    ">
        <div style="
            background: rgba(0, 20, 40, 0.9);
            padding: 4px;
            border-radius: 10px;
            border: 2px solid rgba(0, 255, 65, 0.8);
            position: relative;
            overflow: hidden;
        ">
            <div style="
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(90deg, 
                    transparent, 
                    rgba(0, 255, 65, 0.3), 
                    transparent
                );
                animation: scan-line 2s linear infinite;
                pointer-events: none;
            "></div>
            
            <a href="https://hilfe.die-ewigen.com/index.php?thread=de_de&post=68" target="_blank">
                <img src="gp/g/die_struktur.jpg" style="
                    width: 2028px; 
                    height: 2028px; 
                    border-radius: 8px;
                    display: block;
                    transition: all 0.3s ease;
                " onmouseover="this.style.transform=\'scale(1.02)\'; this.style.filter=\'brightness(1.2) contrast(1.1)\';" 
                   onmouseout="this.style.transform=\'scale(1)\'; this.style.filter=\'brightness(1) contrast(1)\';">
                
                

                '.$artstr.'
            </a>
            
            <!-- Sci-Fi Corner Decorations -->
            <div style="
                position: absolute;
                top: 10px;
                left: 10px;
                width: 30px;
                height: 30px;
                border-left: 3px solid #00ff41;
                border-top: 3px solid #00ff41;
                opacity: 0.8;
            "></div>
            <div style="
                position: absolute;
                top: 10px;
                right: 10px;
                width: 30px;
                height: 30px;
                border-right: 3px solid #0099ff;
                border-top: 3px solid #0099ff;
                opacity: 0.8;
            "></div>

            <div id="greek-player-controls" style="position: absolute; top: 18px; right: 18px; background: rgba(0,0,0,0.8); padding: 8px; border-radius: 5px; color: white; font-family: Arial; font-size: 11px; z-index: 1000;">
              <button onclick="window.greekPlayer.play()" style="margin-right: 3px; padding: 2px 6px; background: #3399FF; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 9px;">παίζω</button>
              <button onclick="window.greekPlayer.stop()" style="padding: 2px 6px; background: #999999; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 9px;">σταμάτα</button>
            </div>

            <div style="
                position: absolute;
                bottom: 10px;
                left: 10px;
                width: 30px;
                height: 30px;
                border-left: 3px solid #ff0080;
                border-bottom: 3px solid #ff0080;
                opacity: 0.8;
            "></div>
            <div style="
                position: absolute;
                bottom: 10px;
                right: 10px;
                width: 30px;
                height: 30px;
                border-right: 3px solid #00ff41;
                border-bottom: 3px solid #00ff41;
                opacity: 0.8;
            "></div>
        </div>
    </div>
</div>

<style>
@keyframes sci-fi-border {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes scan-line {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>';

$sector_width = 1300;
$sector_height = 150;

$mapcontent_width = 100000;
$mapcontent_height = 100000;

//Karte generieren
//die Aliens verteilen, sind alle in Sektor 2
$output = '';

////////////////////////////////////////////////////////////////////////
//npc in Sektor 2 anzeigen
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
    //hinweistext für npc-sektoren
    $npchint = '<img src="'.'gp/'.'g/symbol12.png" border="0" style="margin-bottom: -4px; width: 20px; height: 20px;" title="'.
            $sec_lang['npsecinfo1'].' '.$sec_lang['npsecinfo2'].get_free_artefact_places($_SESSION['ums_user_id']).'<br>'.$sec_lang['npsecinfo3'].
            '<br>'.$sec_lang['npsecinfo4'].
            $sec_lang['angriffsgrenze'].': '.number_format($sec_angriffsgrenze * 100, 2, ",", ".").' / '.
            number_format($col_angriffsgrenze_final * 100, 2, ",", ".").'%'.
            '">';

}

$sf = 2;
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE sector=? ORDER BY score ASC, col ASC", [$sf]);
$alien_anz = mysqli_num_rows($db_daten);
$alien_nr = 0;
$planet_id = 1;
while ($row = mysqli_fetch_array($db_daten)) {

    //Position auf der Karte berechnen
    $kradius = 9800;
    $alien_anzahl = 200;

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
		background: url('.'gp/'.'g/s/p'.$planet_id.'.png);
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

    $output .= '<div class="tac'.$csstag.'" style="background-color: rgba(10,10,10,0.8); font-size: 12px; position: absolute; bottom: 0px; width: 50%;" title="'.$tooltip.'">'.formatMasseinheit($row['score']).'</div>';

    //kollektoren
    if ($col * $col_angriffsgrenze_final <= $row['col']) {
        $csstag = ' text3';
    } else {
        $csstag = ' text2';
    }
    $tooltip = wellenrechner($row['col'], $maxcol, 1);

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

echo $output;

////////////////////////////////////////////////////////
//die einzelnen Spieler-Sektoren-Container erstellen
////////////////////////////////////////////////////////
//eine Liste aller Sektoren mit Spielern erstellen
$sectorList = array();
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_user_data WHERE sector > 2 GROUP BY sector", []);
$anzahlSpielerSektoren = mysqli_num_rows($db_daten);
while ($row = mysqli_fetch_assoc($db_daten)) {
    $sectorList[] = $row['sector'];
}

//zuerst anzahl der pc-sektoren auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id FROM de_sector WHERE npc=0 AND platz > 1");
$anzahlSpielerSektoren = mysqli_num_rows($db_daten);
if ($anzahlSpielerSektoren < 1) {
    $anzahlSpielerSektoren = 1;
}


//Container um alle Sektoren aufzunehmen, aktuell links von den VS
$containerPositionX = 46000;
$containerPositionY = 51000 - $anzahlSpielerSektoren * $sector_height;
echo '<div id="sectorcontainer" style="position: absolute; left: '.$containerPositionX.'px; top: '.$containerPositionY.'px; z-index: 2;">';

foreach ($sectorList as $sf) {

    //die daten des sektors auslesen
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_sector WHERE sec_id=?", [$sf]);
    $sec_data = mysqli_fetch_array($db_daten);

    // CSS-Klasse für eigenen Sektor
    $sectorClass = ($sf == $ownsector) ? 'sector-container own-sector' : 'sector-container';

    echo '<div id="sector_'.$sf.'" class="'.$sectorClass.'" 
		style="width: '.($sector_width).'px; height: auto; min-height: '.($sector_height).'px;
		padding: 0px; margin: 0 0 30px 0;">';

    $rzadd = 0;
    if ($ownsector <> $sf) {
        $rzadd = 2;
    }

    if ($sec_data['npc'] == 1) {

    } else {

        $output = '';
        ////////////////////////////////////////////////////////////////////////
        //spieler anzeigen
        ////////////////////////////////////////////////////////////////////////
        //sektorkommandant feststellen
        $sector = $sf;
        $sksystem = issectorcommander();

        //die scandaten des spielers auslesen
        unset($scandaten);
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT zuser_id, rasse, allytag, ps FROM de_user_scan WHERE user_id=?", [$_SESSION['ums_user_id']]);
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
        $query = "SELECT id FROM de_allys WHERE allytag=?";
        $allyresult = mysqli_execute_query($GLOBALS['dbi'], $query, [$ownally]);
        $at = mysqli_num_rows($allyresult);
        if ($at != 0) {
            //$allyid = mysqli_result($allyresult,0,"id");
            $row = mysqli_fetch_array($allyresult);
            $allyid = $row['id'];

            $allyresult = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=? or ally_id_2=?) and (ally_id_1=id or ally_id_2=id)", [$allyid, $allyid]);
            while ($row = mysqli_fetch_array($allyresult)) {
                if ($ownally != $row["allytag"]) {
                    $allypartner[] = $row["allytag"];
                }
            }

            $allyresult = mysqli_execute_query($GLOBALS['dbi'], "SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=? or ally_id_angegriffener=?) and (ally_id_angreifer=id or ally_id_angegriffener=id)", [$allyid, $allyid]);
            while ($row = mysqli_fetch_array($allyresult)) {
                if ($ownally != $row["allytag"]) {
                    $allyfeinde[] = $row["allytag"];
                }
            }
        }

        //sektormalus bei der attgrenze berechnen

        //eigenen sektorplatz auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT platz FROM de_sector WHERE sec_id=?", [$ownsector]);
        $row = mysqli_fetch_array($db_daten);
        $ownsectorplatz = $row["platz"];

        //sektorplatzunterschied berechnen
        $secplatz = $sec_data['platz'];
        $secplatzunterschied = $secplatz - $ownsectorplatz;
        if ($secplatzunterschied < 0) {
            $secplatzunterschied = 0;
        }

        //secmalus berechnen
        $sec_malus = $sv_sector_attmalus / $anzahlSpielerSektoren * $secplatzunterschied;

        //secmalus darf nicht größer als maximum sein
        if ($sec_malus > $sv_sector_attmalus) {
            $sec_malus = $sv_sector_attmalus;
        }
        $sec_angriffsgrenze = $sv_attgrenze - $sv_attgrenze_whg_bonus + $sec_malus;

        //recyclotronbonus berechnen
        $rec_bonus = $sv_recyclotron_sector_bonus / $anzahlSpielerSektoren * ($secplatz - 1);
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

        $gesamtpunkte = 0;
        $anz = 0;

        //alles anzeigen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode, 
		de_login.last_login, de_login.user_id, de_user_data.score, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag, 
		de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.npc, 
		de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04 
		FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_user_data.sector=? ORDER BY $orderby ASC", [$sf]);

        $anz = mysqli_num_rows($db_daten);
        $gescol = 0;
        $gesamtpunkte = 0;
        //die einzelnen Spieler durchgehen
        while ($row = mysqli_fetch_array($db_daten)) {
            $gesamtpunkte += $row['score'];
            $gescol += $row['col'];
            $sector = $sf;

            ////////////////////////////////////////////////////////////////////////
            //system inkl sk/bk und accountstatus
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
                $systemstr = '<span class="tc3">^'.$systemstr.'^</span>';
            }

            ////////////////////////////////////////////////////////////////////////
            //rang
            ////////////////////////////////////////////////////////////////////////
            $userRang = '';
            if($row['npc']==0){
                $userRang = '<br>Rang: '.$rangnamen[$row['rang']];
            }

            ////////////////////////////////////////////////////////////////////////
            // Titel
            ////////////////////////////////////////////////////////////////////////

            $userTitle = '';
            if ($row['owner_id'] > 0) {
                $userTitle = '<br>';
                $sql = "SELECT * FROM ls_user_title LEFT JOIN ls_title ON (ls_user_title.title_id=ls_title.title_id) WHERE ls_user_title.user_id = '".$row['owner_id']."' ORDER BY ls_title.title ASC";
                $db_datenx = mysqli_query($GLOBALS['dbi_ls'], $sql);
                if (mysqli_num_rows($db_datenx) > 0) {
                    while ($rowx = mysqli_fetch_assoc($db_datenx)) {
                        $userTitle .= $rowx['title'].'<br>';
                    }
                }
            }

            ////////////////////////////////////////////////////////////////////////
            //spielername, details, im sektor online
            ////////////////////////////////////////////////////////////////////////
            $playername = umlaut($row['spielername']);
            if (strtotime($row["last_login"]) + 1800 > time() && $row["lstatus"] == 1) {
                $os = '&nbsp;*';
            } else {
                $os = '';
            }
            if ($ownsector == $sf && $secstatdisable == 0) {
                $osown = $os;
            } else {
                $osown = '';
            }
            $csstag = 'tc1';
            $playertooltip = '';
            $playercsstag = $csstag;
            ////////////////////////////////////////////////////////////////////////
            //rasse
            ////////////////////////////////////////////////////////////////////////

            $knowrasse = 0;
            $playerstatus = 0;
            $playerStatusClass = '';
            //hat man scandaten über die rasse/allianz?
            unset($allytagscan);
            if (isset($scandaten)) {
                for ($i = 0;$i < count($scandaten);$i++) {
                    if ($scandaten[$i]['zuser_id'] == $row['user_id']) {
                        if ($scandaten[$i]['rasse'] > 0) {
                            $knowrasse = 1;
                        }
                        $playerstatus = $scandaten[$i]['ps'];
                        $allytagscan = $scandaten[$i]['allytag'];

                        if ($playerstatus == 1) {
                            $playerStatusClass = ' player-actions-secret-info-friend';
                        } elseif ($playerstatus == 2) {
                            $playerStatusClass = ' player-actions-secret-info-enemy';
                        }

                    }
                }
            }

            //im eigenen sektor sieht man alle rassen, außer in sektor 1
            if ($sector == $ownsector and $ownsector > 1) {
                $knowrasse = 1;
            }


            $planet_id = 0;
            if ($knowrasse == 1) {
                $planet_id = $row['rasse'];
            }

            ////////////////////////////////////////////////////////////////////////
            //allianz, sichtbarkeit durch ally, allybündnis, scandaten, sektor
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
            //ganze andere ally, nichts anzeigen, außer es gibt scandaten, oder es ist der eigene sektor
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

            if ($showallytag != '&nbsp;') {
                $showallytag = '<br><span class="'.$csstag.'" title="Allianz">'.$showallytag.'</span>';
            } else {
                $showallytag = '';
            }

            $output .= '<div class="player-card" style="
				background: url(gp/g/derassenlogo'.$planet_id.'.png);
				background-size: 95% auto;
				background-position: 5px 0px;
				background-repeat: no-repeat;
				" title="'.umlaut($row['spielername']).' ('.$sf.':'.$row['system'].')'.$userRang.$userTitle.'"">';


            ////////////////////////////////////////////////////////////////////////
            //Name Ally
            ////////////////////////////////////////////////////////////////////////

            $output .= '
            <div class="player-name">
				<div onclick="switch_iframe_main_container(\'details.php?se='.$sector.'&sy='.$row['system'].'\')" class="'.$playercsstag.' word-break" style="cursor:pointer !important;">'.$playername.$osown.$showallytag.'</div>
            </div>';

            

            ////////////////////////////////////////////////////////////////////////
            //punkte
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

            // Punkte und Kollektoren in einer Zeile
            $output .= '<div class="player-stats">';
            $output .= '<div class="tac'.$csstag.'" title="'.$tooltip.'">'.formatMasseinheit($row['score']).'</div>';

            ////////////////////////////////////////////////////////////////////////
            //kollektoren
            ////////////////////////////////////////////////////////////////////////
            if ($col * $col_angriffsgrenze_final <= $row['col']) {
                $csstag = ' text3';
            } else {
                $csstag = ' text2';
            }
            $tooltip = wellenrechner($row['col'], $maxcol, 0);
            $output .= '<div class="tac'.$csstag.'" title="'.$tooltip.'">'.number_format($row['col'], 0, "", ".").'</div>';
            $output .= '</div>';

            ////////////////////////////////////////////////////////////////////////
            //aktion
            ////////////////////////////////////////////////////////////////////////

            $aktion = '
				<div onclick="switch_iframe_main_container(\'secret.php?a=s&zsec1='.$sector.'&zsys1='.$row['system'].'\')" title="Sonde starten" class="action-link">
                    <div class="icon-sonde"></div>
                </div>

				<div onclick="switch_iframe_main_container(\'secret.php?a=a&zsec2='.$sector.'&zsys2='.$row['system'].'\')" class="action-link" title="Agenteneinsatz">
                    <div class="icon-agent"></div>
                </div>

				<div onclick="switch_iframe_main_container(\'military.php?se='.$sector.'&sy='.$row['system'].'\')" class="action-link" title="Flotteneinsatz">
                    <div class="icon-fleet"></div>
                </div>
				
                <div onclick="switch_iframe_main_container(\'secret.php?a=d&zsec1='.$sector.'&zsys1='.$row['system'].'\')" class="action-link'.$playerStatusClass.'" title="Geheimdienstinformationen">
                    <div class="icon-secret-info"></div>
                </div>';

            $output .= '<div class="player-actions">'.$aktion.'</div>';

            $output .= '</div>';//player-card
        }

        if ($rzadd == 0) {
            $style = 'margin-top: 2px; margin-left: 2px; border: 1px solid #444444; background-color: #00DD00; color: #000000; width: 16px; display: inline-block; text-align: center;';
        } else {
            $style = 'margin-top: 2px; margin-left: 2px; border: 1px solid #444444; background-color: #f05a00; color: #000000; width: 16px; display: inline-block; text-align: center;';
        }


        $sec_angriffsgrenze = number_format($sec_angriffsgrenze * 100, 2, ",", ".").'%';
        $rec_bonus = number_format($rec_bonus, 2, ",", ".").'%';

        if ($anz > 0) {
            $infostr = '<img src="'.'gp/'.'g/symbol12.png" border="0" style="margin-bottom: -5px; width: 20px; height: 20px;" title="'.
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
            $infostr = '<img src="'.'gp/'.'g/symbol12.png" border="0" style="margin-bottom: -5px; width: 20px; height: 20px;" title="freier Sektor">';
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
            $sektorinfo .= '<span title="Reisezeitmalus<br>Eigener Sektor: kein Malus<br>Andere Sektoren: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
            $sektorinfo .= ' '.$infostr.' freier Sektor';
            $sektorinfo .= '</span>';
        }

        // Neue Struktur mit verbessertem Design
        echo '<div class="sector-header" style="display: flex; justify-content: space-between; align-items: center;">';
        echo '<div>'.$sektorinfo.'</div>';
        
        // Button für Sektorpolitik nur im eigenen Sektor anzeigen
        if ($sf == $ownsector) {
            echo '<div>';
            echo '<div onclick="switch_iframe_main_container(\'politics.php\')" class="button" style="background-color: #4CAF50; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 12px; display: inline-block; cursor:pointer !important;">Sektorpolitik</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '<div class="sector-content">';

        $artstr = '';
        $basestr = '';
        if ($anz > 0) {
            //Artefakte im Sektor
            $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector=?", [$sf]);
            $artnum = mysqli_num_rows($res);

            while ($row = mysqli_fetch_array($res)) {
                //artefakttooltip bauen
                $desc = $row["artdesc"];
                $desc = str_replace("{WERT1}", number_format($sv_artefakt[$row["id"] - 1][0], 2, ",", "."), $desc);
                $desc = str_replace("{WERT2}", number_format($sv_artefakt[$row["id"] - 1][1], 0, "", "."), $desc);
                $desc = str_replace("{WERT3}", number_format($sv_artefakt[$row["id"] - 1][2], 0, "", "."), $desc);
                $desc = str_replace("{WERT4}", number_format($sv_artefakt[$row["id"] - 1][3], 0, "", "."), $desc);
                $desc = str_replace("{WERT5}", number_format($sv_artefakt[$row["id"] - 1][4], 0, "", "."), $desc);
                $desc = str_replace("{WERT6}", number_format($sv_artefakt[$row["id"] - 1][5], 2, ",", "."), $desc);


                $atip = '<font color=#'.$row["color"].'>'.$row["artname"].'</font><br>'.$desc;

                $artstr .= '
                <div onclick="switch_iframe_main_container(\'help.php?a=1\')" title="'.umlaut($atip).'">
                    <img src="'.'gp/'.'g/sa'.$row["picid"].'.gif" style="width: 25px; height: 25px;">
                </div>';
            }

            if ($artstr != '') {
                $artstr = '<div style="text-align: center;">'.$artstr.'</div>';
            }

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
                $basestr = '<a href="'.'gp/'.'g/big/'.strtoupper($bn).'" target="_blank"><img border="0" src="'.'gp/'.'g/'.$bn.'" name="sb" title="'.$stip.'" style="width: 100%; height: auto;"></a>';
                //wenn es keine sektorraumbasis gibt string mit einem leerzeichen belegen
                if ($bed == '000') {
                    $basestr = '&nbsp;';
                }

                $basestr = '<div class="sector-base">'.$basestr.$artstr.'</div>';

            }
        }

        // Sektorraumbasis und Artefakte anzeigen
        if ($basestr != '') {
            echo $basestr;
        }

        // Spieler in verbesserter Darstellung
        echo '<div class="sector-players">';
        echo $output;
        echo '</div>'; // sector-players

        echo '</div>'; // sector-content
    }
    echo '</div>';//sektor ende
}

echo '</div>';

///////////////////////////////////////////////////////////////////////////
// erforschbare/erforschte Systeme für Handel/Missionen/Events
///////////////////////////////////////////////////////////////////////////

$sichtbare_systeme = array();
$immer_sichtbare_systeme = array();
$erforschte_systeme = array();
$erforschte_systeme_koordinaten = array();

//Kanten laden
$kanten = array();
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_map_kanten");
while ($row = mysqli_fetch_array($db_daten)) {
    $kanten[] = array($row['knoten_id1'],$row['knoten_id2']);
}

//die erforschten Systeme laden
$sql = "SELECT map_id FROM de_user_map WHERE user_id=? AND known_since>0 AND known_since<?;";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id'], time()]);
while ($row = mysqli_fetch_array($db_daten)) {
    //sie sind sichtbar und erforscht
    $sichtbare_systeme[] = $row['map_id'];
    $erforschte_systeme[] = $row['map_id'];
}

//die sichtbaren Systeme um Systeme ergänzen, die immer sichtbar sind
$sql = "SELECT id FROM de_map_objects WHERE always_visible=1 OR system_typ=4;";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql);
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
$sql = "SELECT * FROM de_user_map_bldg WHERE user_id=?;";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
while ($row = mysqli_fetch_array($db_daten)) {
    $bldg[$row['map_id']][$row['field_id']]['bldg_id'] = $row['bldg_id'];
    $bldg[$row['map_id']][$row['field_id']]['bldg_level'] = $row['bldg_level'];
    $bldg[$row['map_id']][$row['field_id']]['bldg_time'] = $row['bldg_time'];
}


//Systeme laden
//$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects LEFT JOIN de_user_map ON(de_map_objects.id=de_user_map.map_id);");
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_map_objects");
$alien_anz = mysqli_num_rows($db_daten);

//Position auf der Karte berechnen
$alien_nr = 0;
$kradius = 500;
$planet_id = 1;
while ($row = mysqli_fetch_array($db_daten)) {
    //klasse restaurieren
    $data = unserialize($row['data']);

    $output = '';

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
            $bg_image = 'gp/'.'g/s/sym3.png';

        } else {
            $bg_image = 'gp/'.'g/s/p'.$planet_id.'.png';

            $planet_id++;
            if ($planet_id > 20) {
                $planet_id = 1;
            }
        }

        //$erforschte_systeme_koordinaten[$row['id']]=array($alienpos_x, $alienpos_y);
    } else {
        $bg_image = 'gp/g/de_vs_us.png';
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
                    $output .= '<div style="text-align:center; margin-right: 1px; font-size: 10px; line-height: 10px;"><img style="width: 18px; box-sizing: border-box;" src="gp/g/ele'.$filename_nr.'.gif" class="rounded-borders" title="'.$GLOBALS['map_field_typ'][$data->fields[$i][0]]['name'].'">'.$stufeninfo.'</div>';
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

                    $output .= '
						<div style="font-size: 10px; line-height: 10px; text-align:center;">
							<div style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #999999; text-align: center; box-sizing: border-box; border-radius: 5px;" title="'.$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['name'].'">'.$GLOBALS['greek_chars'][$GLOBALS['map_buildings'][$bldg[$row['id']][$i]['bldg_id']]['factory_id']].'</div>
							'.str_replace('<br>', '', $stufeninfo).'
						</div>';


                } else {
                    $output .= '<div title="keine Rohstoffe" class="rounded-borders" style=" margin-right: 1px; line-height: 18px; width: 18px; height: 18px; background-color: #666666; text-align: center; box-sizing: border-box;">-</div>';
                }




            }


            if (($i + 1) % 5 == 0) {
                $output .= '</div><div style="display: flex;">';
            }
        }
    }


    $output .= '</div>';

    $output .= '</div>';

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





?>

  </div>
  <div id="custom-tooltip"></div>
</div>

<script>
  const map = document.getElementById('map');
  const viewport = document.getElementById('viewport');

  const mapWidth = <?php echo $mapcontent_width; ?>;
  const mapHeight = <?php echo $mapcontent_height; ?>;

  // Transformation State
  let offsetX = 0;
  let offsetY = 0;
  let zoom = 0.8;
  let maxZoom = 3.0;

  // Gespeicherte Werte aus localStorage laden
  function loadMapState() {
    const saved = localStorage.getItem('mapState');
    if (saved) {
      try {
        const state = JSON.parse(saved);
        offsetX = state.offsetX || 0;
        offsetY = state.offsetY || 0;
        zoom = state.zoom || 0.8;
        // Zoom-Grenzen prüfen
        zoom = Math.min(3, Math.max(0.1, zoom));
        return true;
      } catch (e) {
        console.log('Fehler beim Laden der Kartenposition:', e);
      }
    }
    return false;
  }

  // Aktuelle Werte in localStorage speichern
  function saveMapState() {
    const state = {
      offsetX: offsetX,
      offsetY: offsetY,
      zoom: zoom
    };
    localStorage.setItem('mapState', JSON.stringify(state));
  }

  // Drag State
  let isDragging = false;
  let hasDragged = false; // Flag um zu tracken, ob tatsächlich gedraggt wurde
  let dragStartX = 0;
  let dragStartY = 0;
  let dragOriginX = 0;
  let dragOriginY = 0;
  let lastMouseX = 0;
  let lastMouseY = 0;

  // Touch Pinch Zoom State
  let lastTouchDist = null;
  let lastTouchMid = null;

  // Update Map Transform
  function update() {
    map.style.transform = `translate(${offsetX}px, ${offsetY}px) scale(${zoom})`;
    // Nach jeder Änderung speichern
    saveMapState();
  }

// Zentrieren auf eigenen Sektor
function centerMap() {
    const vw = viewport.clientWidth;
    const vh = viewport.clientHeight;
    
    // Versuche auf eigenen Sektor zu zentrieren
    const targetElement = document.getElementById('sector_'+ownSector);
    const containerElement = document.getElementById('sectorcontainer');
    
    if (targetElement && containerElement) {
        // Position des Containers aus dem CSS-Style auslesen
        const containerX = parseFloat(containerElement.style.left) || 0;
        const containerY = parseFloat(containerElement.style.top) || 0;
        
        // Position des Ziel-Sektors innerhalb des Containers
        const targetOffsetTop = targetElement.offsetTop;
        
        // Absolute Position des Zielsektors auf der Karte (ohne Transform)
        const absoluteX = containerX;
        const absoluteY = containerY + targetOffsetTop;
        
        // Sektor in der Bildschirmmitte positionieren
        // Formel: viewport_mitte = absolute_position * zoom + offset + sektor_mitte
        // Nach offset auflösen: offset = viewport_mitte - absolute_position * zoom - sektor_mitte
        offsetX = (vw / 2) - (absoluteX * zoom) - (650 * zoom); // 650 = halbe Sektorbreite
        offsetY = (vh / 2) - (absoluteY * zoom) - (75 * zoom);  // 75 = halbe Sektorhöhe
        
        //console.log('Zentriert auf sector_' + ownSector + ' - Container:', containerX, containerY, 'Target Offset:', targetOffsetTop, 'Absolute:', absoluteX, absoluteY);
    } else {
        // Fallback: Standard-Zentrierung
        console.log('sector_' + ownSector + ' oder sectorcontainer nicht gefunden - verwende Standard-Zentrierung');
        offsetX = (vw - mapWidth * zoom) / 2;
        offsetY = (vh - mapHeight * zoom) / 2;
    }
    update();
}


  // Initial Laden: Erst gespeicherte Position laden, dann zentrieren falls nichts gespeichert
  if (!loadMapState()) {
    centerMap();
  } else {
    update();
  }

  // Drag starten
  function startDrag(clientX, clientY) {
    isDragging = true;
    hasDragged = false;
    dragStartX = clientX;
    dragStartY = clientY;
    dragOriginX = offsetX;
    dragOriginY = offsetY;
    lastMouseX = clientX;
    lastMouseY = clientY;
    viewport.style.cursor = 'grabbing';
  }

  // Drag bewegen - verbesserte Version
  function dragMove(clientX, clientY) {
    if (!isDragging) return;
    
    // Verwende die Differenz zur letzten Position statt zur Start-Position
    const deltaX = clientX - lastMouseX;
    const deltaY = clientY - lastMouseY;
    
    // Setze hasDragged auf true, wenn sich die Maus bewegt hat
    if (Math.abs(deltaX) > 2 || Math.abs(deltaY) > 2) {
      hasDragged = true;
    }
    
    offsetX += deltaX;
    offsetY += deltaY;
    
    // Aktuelle Position als neue letzte Position speichern
    lastMouseX = clientX;
    lastMouseY = clientY;
    
    update();
  }

  // Drag stoppen
  function stopDrag() {
    const wasDragged = hasDragged;
    isDragging = false;
    hasDragged = false;
    viewport.style.cursor = 'default';
    return wasDragged; // Rückgabe ob tatsächlich gedraggt wurde
  }

  // Hilfsfunktionen für Touch
  function getDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx*dx + dy*dy);
  }

  function getMidpoint(touches) {
    return {
      x: (touches[0].clientX + touches[1].clientX)/2,
      y: (touches[0].clientY + touches[1].clientY)/2
    };
  }

  // Maus-Events fürs Draggen - verbessert
  viewport.addEventListener('mousedown', (e) => {
    // Nur linke Maustaste
    if (e.button !== 0) return;
    
    e.preventDefault();
    startDrag(e.clientX, e.clientY);
  });

  document.addEventListener('mousemove', (e) => {
    if (isDragging) {
      e.preventDefault();
      dragMove(e.clientX, e.clientY);
    }
  });

  document.addEventListener('mouseup', (e) => {
    if (isDragging) {
      e.preventDefault();
      const wasDragged = stopDrag();
      
      // Wenn tatsächlich gedraggt wurde, click-Events für kurze Zeit blockieren
      if (wasDragged) {
        const clickBlocker = (clickEvent) => {
          clickEvent.preventDefault();
          clickEvent.stopPropagation();
          clickEvent.stopImmediatePropagation();
        };
        
        document.addEventListener('click', clickBlocker, true);
        setTimeout(() => {
          document.removeEventListener('click', clickBlocker, true);
        }, 10);
      }
    }
  });

  // Touch-Events - verbessert
  viewport.addEventListener('touchstart', (e) => {
    if (e.touches.length === 1) {
      const t = e.touches[0];
      
      // Verhindere Touch-Dragging auf klickbare Elemente
      if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a')) {
        return;
      }
      
      startDrag(t.clientX, t.clientY);
      lastTouchDist = null;
      lastTouchMid = null;
    } else if (e.touches.length === 2) {
      stopDrag();
      lastTouchDist = getDistance(e.touches);
      lastTouchMid = getMidpoint(e.touches);
    }
  }, { passive: false });

  viewport.addEventListener('touchmove', (e) => {
    if (e.touches.length === 1 && isDragging) {
      const t = e.touches[0];
      dragMove(t.clientX, t.clientY);
      e.preventDefault();
    } else if (e.touches.length === 2) {
      const newDist = getDistance(e.touches);
      const newMid = getMidpoint(e.touches);
      if (lastTouchDist) {
        const delta = newDist - lastTouchDist;
        const zoomFactor = 0.005;
        const oldZoom = zoom;
        zoom += delta * zoomFactor;
        zoom = Math.min(3, Math.max(0.1, zoom));
        const zoomChange = zoom / oldZoom;

        // Karte-Position so anpassen, dass Mittelpunkt gleich bleibt
        offsetX = newMid.x - zoomChange * (newMid.x - offsetX);
        offsetY = newMid.y - zoomChange * (newMid.y - offsetY);

        update();
      }
      lastTouchDist = newDist;
      lastTouchMid = newMid;
      e.preventDefault();
    }
  }, { passive: false });

  viewport.addEventListener('touchend', (e) => {
    if (e.touches.length === 0) {
      stopDrag();
      lastTouchDist = null;
      lastTouchMid = null;
    }
  }, { passive: false });

  // Mausrad zum Zoomen - verbessert
  viewport.addEventListener('wheel', (e) => {
    e.preventDefault();
    e.stopPropagation();

    const mouseX = e.clientX;
    const mouseY = e.clientY;

    // Position innerhalb der Karte (unskaliert)
    const relX = (mouseX - offsetX);
    const relY = (mouseY - offsetY);

    const oldZoom = zoom;

    // Scrollbeschleunigung dämpfen
    const maxDelta = 30;
    const delta = Math.sign(e.deltaY) * Math.min(Math.abs(e.deltaY), maxDelta);

    const zoomFactor = 0.002;

    zoom -= delta * zoomFactor;
    zoom = Math.min(3, Math.max(0.1, zoom));

    const zoomChange = zoom / oldZoom;

    // Karte so verschieben, dass der Punkt unter dem Mauszeiger gleich bleibt
    offsetX = mouseX - relX * zoomChange;
    offsetY = mouseY - relY * zoomChange;

    update();
  }, { passive: false });

  // Fenstergröße neu ausrichten und Karte zentrieren
  window.addEventListener('resize', () => {
    centerMap();
  });

  // Custom Tooltip Funktionalität
  const customTooltip = document.getElementById('custom-tooltip');
  let tooltipTimeout = null;

  // Alle Elemente mit title-Attribut finden und Tooltip-Events hinzufügen
  function initTooltips() {
    const elementsWithTitle = document.querySelectorAll('[title]');
    
    elementsWithTitle.forEach(element => {
      const originalTitle = element.getAttribute('title');
      
      // Originales title-Attribut entfernen, um Standard-Tooltip zu verhindern
      element.removeAttribute('title');
      element.setAttribute('data-tooltip', originalTitle);
      
      element.addEventListener('mouseenter', (e) => {
        showTooltip(e, originalTitle);
      });
      
      element.addEventListener('mouseleave', () => {
        hideTooltip();
      });
      
      element.addEventListener('mousemove', (e) => {
        updateTooltipPosition(e);
      });
    });
  }

  function showTooltip(event, text) {
    clearTimeout(tooltipTimeout);
    
    if (!text || text.trim() === '') return;
    
    // HTML-Entities dekodieren aber HTML-Tags beibehalten
    const decodedText = text
      .replace(/&uuml;/g, 'ü')
      .replace(/&auml;/g, 'ä')
      .replace(/&ouml;/g, 'ö')
      .replace(/&Uuml;/g, 'Ü')
      .replace(/&Auml;/g, 'Ä')
      .replace(/&Ouml;/g, 'Ö')
      .replace(/&szlig;/g, 'ß')
      .replace(/&amp;/g, '&')
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>')
      .replace(/&quot;/g, '"')
      .replace(/&nbsp;/g, ' ');
    
    // HTML direkt verwenden (Tabellen bleiben erhalten)
    customTooltip.innerHTML = decodedText;
    customTooltip.style.display = 'block';
    updateTooltipPosition(event);
  }

  function hideTooltip() {
    tooltipTimeout = setTimeout(() => {
      customTooltip.style.display = 'none';
    }, 100);
  }

  function updateTooltipPosition(event) {
    const tooltipRect = customTooltip.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    let x = event.clientX + 10;
    let y = event.clientY + 10;
    
    // Tooltip nicht über den rechten Rand hinaus
    if (x + tooltipRect.width > viewportWidth) {
      x = event.clientX - tooltipRect.width - 10;
    }
    
    // Tooltip nicht über den unteren Rand hinaus
    if (y + tooltipRect.height > viewportHeight) {
      y = event.clientY - tooltipRect.height - 10;
    }
    
    // Mindestabstände zu den Rändern
    x = Math.max(5, Math.min(x, viewportWidth - tooltipRect.width - 5));
    y = Math.max(5, Math.min(y, viewportHeight - tooltipRect.height - 5));
    
    customTooltip.style.left = x + 'px';
    customTooltip.style.top = y + 'px';
  }

  // Tooltips beim Laden der Seite initialisieren
  document.addEventListener('DOMContentLoaded', () => {
    initTooltips();
  });

  // Tooltips auch initialisieren, falls das DOM bereits geladen ist
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTooltips);
  } else {
    initTooltips();
  }

  // MutationObserver für dynamisch hinzugefügte Elemente
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1) { // Element node
          // Prüfen ob das Element selbst ein title-Attribut hat
          if (node.hasAttribute && node.hasAttribute('title')) {
            const originalTitle = node.getAttribute('title');
            node.removeAttribute('title');
            node.setAttribute('data-tooltip', originalTitle);
            
            node.addEventListener('mouseenter', (e) => {
              showTooltip(e, originalTitle);
            });
            
            node.addEventListener('mouseleave', () => {
              hideTooltip();
            });
            
            node.addEventListener('mousemove', (e) => {
              updateTooltipPosition(e);
            });
          }
          
          // Prüfen ob Kindelemente title-Attribute haben
          const childrenWithTitle = node.querySelectorAll && node.querySelectorAll('[title]');
          if (childrenWithTitle) {
            childrenWithTitle.forEach(child => {
              const originalTitle = child.getAttribute('title');
              child.removeAttribute('title');
              child.setAttribute('data-tooltip', originalTitle);
              
              child.addEventListener('mouseenter', (e) => {
                showTooltip(e, originalTitle);
              });
              
              child.addEventListener('mouseleave', () => {
                hideTooltip();
              });
              
              child.addEventListener('mousemove', (e) => {
                updateTooltipPosition(e);
              });
            });
          }
        }
      });
    });
  });

  // Observer starten
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
</script>

</body>
</html>

<?php
function wellenrechner($kol, $maxcol, $npcsec)
{
    global $sec_lang, $col, $sv_min_col_attgrenze, $sv_max_col_attgrenze, $sv_kollie_klaurate;
    $str = 'Kollektoren-Wellenrechner';
    $str .= "<table width=200px border=0 cellpadding=0 cellspacing=1><tr align=center><td width=15%>&nbsp</td><td width=17%>".$sec_lang['kollektoren']."</td></tr>";
    $str .= "<tr align=center><td>&nbsp;</td><td>".number_format($kol, 0, ',', '.')."</td></tr>";

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

    //info bzgl. erobern/zerst�ren von kollektoren
    $str .= "</table><br>Gr&uuml;ner Kollektorenwert: Kollektoren liegen &uuml;ber der Kollektorenangriffsgrenze und werden erobert.<br><br>Roter Kollektorenwert: Kollektoren liegen unter der Kollektorenangriffsgrenze und werden zerst&ouml;rt.";

    return ($str);
}
