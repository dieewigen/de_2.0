<?php
//        --------------------------------- ally_members.php ---------------------------------
//        Funktion der Seite:                Anzeigen der Allianzmitglieder
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der Adminrechte bis auf Leader ernennen
//                                                          auf Co-Leader
//  --------------------------------------------------------------------------------

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.members.lang.php');
include_once('functions.php');

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$col = $row["col"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];



?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allymembers_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

$allyId = -1;
if ($pd['allytag'] != "" && $pd['ally_status'] == 1) {
    $allyId = $pd['ally_id'];
} else {
    // Wenn der Spieler keiner Allianz angehört, dann wird die Seite nicht angezeigt
    echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$allymembers_lang['msg_3'].'</div></div>';
    exit;
}

$ordermode = isset($_GET['ordermode']) ? $_GET['ordermode'] : '';

if (!empty($ordermode)) {

    if ($ordermode == "koords") {
        $orderstring = "sector, `system` ASC";
    } elseif ($ordermode == "name") {
        $orderstring = "spielername ASC";
    } elseif ($ordermode == "points") {
        $orderstring = "score DESC";
    } elseif ($ordermode == "cols") {
        $orderstring = "col DESC";
    } elseif ($ordermode == "race") {
        $orderstring = "rasse ASC";
    }

} else {
    $orderstring = "sector, `system` ASC";
}

//Spaltenköpfe sortieren die Liste, die aktive Sortierung ist hervorgehoben
$sortierung = ($ordermode == '') ? 'koords' : $ordermode;
function mitglieder_kopf($modus, $text, $sortierung, $klasse = '', $titel = '')
{
    return '<a href="ally_members.php?ordermode='.$modus.'" class="ally-sortieren'.($modus == $sortierung ? ' ally-sortieren-aktiv' : '').$klasse.'"'.($titel != '' ? ' title="'.$titel.'"' : '').'>'.$text.'</a>';
}

//Spalten je nach Rechten: Leader entlassen und ernennen, Co-Leader entlassen
$zeilenklasse = 'ally-mitglied';
if ($isleader) {
    $zeilenklasse .= ' ally-mitglied-leader';
} elseif ($iscoleader) {
    $zeilenklasse .= ' ally-mitglied-coleader';
}

$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_user_data WHERE status=1 AND ally_id= ? ORDER BY $orderstring",
    [$allyId]
);

$zeilen = '';
$anzahl = 0;
while ($data = mysqli_fetch_assoc($result)) {

    $userid = $data['user_id'];
    $sector = $data['sector'];
    $system = $data['system'];
    $score = $data['score'];
    $kollies = $data['col'];

    $rasse='';
    if ($data['rasse'] == 1) {
        $rasse='<img src="gp/g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
    } elseif ($data['rasse'] == 2) {
        $rasse='<img src="gp/g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
    } elseif ($data['rasse'] == 3) {
        $rasse='<img src="gp/g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
    } elseif ($data['rasse'] == 4) {
        $rasse='<img src="gp/g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';
    }elseif ($data['rasse'] == 5) {
        $rasse='<img src="gp/g/r/raceD.png" title="DX61a23" width="16px" height="16px">';
    }


    $sectorjump = explode(":", $sector);
    $sectorjump = $sectorjump[0];

    $tquery = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT spielername FROM de_user_data WHERE user_id=?",
        [$userid]
    );
    $row = mysqli_fetch_assoc($tquery);
    $name = $row['spielername'];

    $de_login_result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT status FROM de_login WHERE user_id=?",
        [$userid]
    );
    $de_login_data = mysqli_fetch_assoc($de_login_result);
    $de_login_status = $de_login_data["status"];
    //Namen sind bereits UTF-8 (früher hier ein zweites Mal umgewandelt: "TÃ¼rmchen")
    $name_html = $name;
    $inaktiv = '';
    if ($de_login_status != 1) {
        $name_html = "<i>(".$name.")</i>";
        $inaktiv = ' ally-mitglied-inaktiv';
    }

    //Entlassen/Leader übergeben mit Zwei-Klick-Bestätigung statt Rückfrage
    $aktionen = '';
    if ($isleader) {
        $aktionen = '
            <span class="ally-mitglied-aktionen">
                <a href="ally_kick.php?userid='.$userid.'" class="mod-btn mod-btn-gefahr ally-btn-klein" data-bestaetigen="Entlassen?" title="'.htmlspecialchars($allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'], ENT_QUOTES, 'UTF-8').'">'.ucfirst($allymembers_lang['entlassen']).'</a>
                <a href="ally_leader.php?userid='.$userid.'" class="mod-btn mod-btn-leise ally-btn-klein" data-bestaetigen="Abgeben?" title="'.htmlspecialchars($allymembers_lang['msg_2_1'].' '.$name.' '.$allymembers_lang['msg_2_2'], ENT_QUOTES, 'UTF-8').'">'.ucfirst($allymembers_lang['toleader']).'</a>
            </span>';
    }
    if ($iscoleader) {
        $aktionen = '
            <span class="ally-mitglied-aktionen">
                <a href="ally_kick.php?userid='.$userid.'" class="mod-btn mod-btn-gefahr ally-btn-klein" data-bestaetigen="Entlassen?" title="'.htmlspecialchars($allymembers_lang['msg_1_1'].' '.$name.' '.$allymembers_lang['msg_1_2'], ENT_QUOTES, 'UTF-8').'">'.ucfirst($allymembers_lang['entlassen']).'</a>
            </span>';
    }

    //je Zeile ein Formular: die Koordinaten öffnen den Sektor
    $zeilen .= '
        <form name="f'.$sector.'x'.$system.'" action="sector.php?sf='.$sectorjump.'" method="POST" class="'.$zeilenklasse.$inaktiv.'">
            <a href="details.php?a=s&se='.$sector.'&sy='.$system.'" class="ally-mitglied-name">'.$name_html.'</a>
            <span class="ally-zahl">'.$kollies.'</span>
            <span class="ally-zahl">'.number_format($score, 0, '', '.').'</span>
            <a href="javascript:document.f'.$sector.'x'.$system.'.submit()" class="ally-zahl">'.$sector.':'.$system.'</a>
            <span class="ally-rasse">'.$rasse.'</span>
            '.$aktionen.'
        </form>';
    $anzahl++;
}

rahmen_oben($allymembers_lang['mitgliederliste']);

echo '
<div class="ally mod">
    <div class="'.$zeilenklasse.' ally-zeilenkopf">
        '.mitglieder_kopf('name', $allymembers_lang['name'], $sortierung).'
        '.mitglieder_kopf('cols', $allymembers_lang['kollies'], $sortierung, ' ally-rechts').'
        '.mitglieder_kopf('points', $allymembers_lang['punkte'], $sortierung, ' ally-rechts').'
        '.mitglieder_kopf('koords', $allymembers_lang['koords'], $sortierung, ' ally-rechts').'
        '.mitglieder_kopf('race', 'R', $sortierung, ' ally-rasse', 'Rasse').'
        '.(($isleader || $iscoleader) ? '<span></span>' : '').'
    </div>
    <div class="ally-zeilen">'.$zeilen.'</div>
    <div class="ally-fuss"><span class="mod-chip">Mitglieder <b>'.$anzahl.'</b></span></div>
</div>';

rahmen_unten();


?>
<br>
</body>
</html>
