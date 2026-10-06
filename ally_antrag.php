<?php
//        --------------------------------- ally_antrag.php ---------------------------------
//        Funktion der Seite:                B&uuml;ndnisantr&auml;ge stellen
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der &Auml;nderungsbefugnis der B&uuml;ndnisantr&auml;ge
//                                                          auf Coleader
//  --------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.antrag.lang.php');
include_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag
     FROM de_user_data WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyantrag_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

//////////////////////////////////////////////////////////////////////////////
// Bewerbungen von Spielern
//////////////////////////////////////////////////////////////////////////////
rahmen_oben('Bewerbungen');
echo '<div class="ally mod">';

//Prüfung auf coleader hinzugefügt von Ascendant (4.9.2002)
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT allytag FROM de_allys
     WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($result);
$clankuerzel = $row["allytag"] ?? '';

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id, sector, `system` FROM de_user_data WHERE status = '0' AND allytag = ?",
    [$clankuerzel]
);

$nb = mysqli_num_rows($result);

$row = 0;

//fix gegen das anzeigen von allen allylosen
if ($clankuerzel=='') $row=$nb;

$karten = '';
while ($row < $nb){
        $userData = mysqli_fetch_assoc($result);
        $userid = $userData["user_id"];
        $se = $userData["sector"];
        $sy = $userData["system"];

        $result2 = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT spielername, tick, col, score, sector, `system`, rasse, actpoints, tick
             FROM de_user_data WHERE user_id = ?",
            [$userid]
        );
        $playerData = mysqli_fetch_assoc($result2);
        $name = $playerData["spielername"];
        $b_score = $playerData["score"];
        $b_sector = $playerData["sector"];
        $b_system = $playerData["system"];
        $b_actpoints = $playerData["actpoints"];
        $b_cols = $playerData["col"];
        $b_race = $playerData["rasse"];
        $m_actpoints = $playerData["actpoints"];
        $m_tick = $playerData["tick"];

    	$activity=$m_actpoints/$m_tick*1000;

        //Rasse als Symbol wie in der Mitgliederliste
        $rassen = array(
            1 => array('raceE', 'Die Ewigen'),
            2 => array('raceI', 'Ishtar'),
            3 => array('raceK', 'K&#180;Tharr'),
            4 => array('raceZ', 'Z&#180;tah-ara'),
            5 => array('raceD', 'DX61a23'),
        );
        $r_text = '?';
        if (isset($rassen[$b_race])) {
            $r_text = '<img src="gp/g/r/'.$rassen[$b_race][0].'.png" title="'.$rassen[$b_race][1].'" alt="" width="14" height="14">';
        }

        $result2 = mysqli_execute_query($GLOBALS['dbi'],
            "SELECT antrag FROM de_ally_antrag WHERE user_id = ?",
            [$userid]
        );
        $antragData = mysqli_fetch_assoc($result2);
        $antragstext = $antragData["antrag"] ?? '';

        //das Formular öffnet den Sektor des Bewerbers
        $karten .= '
			<div class="ally-karte">
				<form name="f'.$b_sector.'x'.$b_system.'" action="sector.php?sf='.$b_sector.'" method="POST"></form>
				<div class="ally-karte-kopf">
					<a href="details.php?se='.$se.'&sy='.$sy.'" class="ally-person">'.$name.'</a>
					<span class="mod-chip">'.$b_sector.':'.$b_system.'</span>
					<span class="ally-chips">
						<span class="mod-chip">'.$allyantrag_lang['punkte'].' <b>'.number_format($b_score, 0,'','.').'</b></span>
						<span class="mod-chip">'.$allyantrag_lang['kollektoren'].' <b>'.$b_cols.'</b></span>
						<span class="mod-chip">'.$allyantrag_lang['rasse'].' '.$r_text.'</span>
					</span>
				</div>
				<div class="ally-text">'.($antragstext != '' ? $antragstext : '<span class="ally-leise">Kein Bewerbungstext.</span>').'</div>
				<div class="ally-aktionen">
					<a href="javascript:document.f'.$b_sector.'x'.$b_system.'.submit()" class="mod-btn mod-btn-leise ally-btn-klein">'.$allyantrag_lang['showsec'].'</a>
					<a href="details.php?se='.$se.'&sy='.$sy.'" class="mod-btn mod-btn-leise ally-btn-klein">'.$allyantrag_lang['sendhfn'].'</a>
					<a href="ally_ablehnen.php?userid='.$userid.'" class="mod-btn mod-btn-gefahr ally-btn-klein">'.$allyantrag_lang['anablehnen'].'</a>
					<a href="ally_annehmen.php?userid='.$userid.'" class="mod-btn ally-btn-klein">'.$allyantrag_lang['annehmen'].'</a>
				</div>
			</div>';

        $row++;
}
echo ($karten != '') ? '<div class="ally-karten">'.$karten.'</div>' : '<div class="mod-leer">Es liegen keine Bewerbungen vor.</div>';
echo '</div>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
// Bündnisanfragen anderer Allianzen
//////////////////////////////////////////////////////////////////////////////
rahmen_oben('B&uuml;ndnisanfragen');
echo '<div class="ally mod">';

//Prüfung auf coleader hinzugefügt von Ascendant (4.9.2002)
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT id FROM de_allys
     WHERE leaderid = ? OR coleaderid1 = ? OR coleaderid2 = ? OR coleaderid3 = ?",
    [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($result);
$allyid = $row["id"] ?? 0;

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT allytag, antrag, ally_id_antragsteller
     FROM de_allys, de_ally_buendniss_antrag
     WHERE ally_id_antragsteller = id AND ally_id_partner = ?",
    [$allyid]
);

$karten = '';
while($row = mysqli_fetch_assoc($result)) {
        $name = $row['allytag'];
        $antragstext = $row['antrag'];
        $ally_id_antragsteller = $row['ally_id_antragsteller'];
        //die Allianzinfo zeigt den Leader, dort kann man ihm schreiben
		$karten .= '
			<div class="ally-karte">
				<div class="ally-karte-kopf">
					<a href="ally_detail.php?allyid='.$ally_id_antragsteller.'" class="ally-person">'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'</a>
				</div>
				<div class="ally-text">'.($antragstext != '' ? $antragstext : '<span class="ally-leise">Kein Antragstext.</span>').'</div>
				<div class="ally-aktionen">
					<a href="ally_detail.php?allyid='.$ally_id_antragsteller.'" class="mod-btn mod-btn-leise ally-btn-klein">Allianz ansehen</a>
					<a href="ally_ablehnen.php?allyid='.$ally_id_antragsteller.'" class="mod-btn mod-btn-gefahr ally-btn-klein">'.$allyantrag_lang['anablehnen'].'</a>
					<a href="ally_annehmen.php?allyid='.$ally_id_antragsteller.'" class="mod-btn ally-btn-klein">'.$allyantrag_lang['annehmen'].'</a>
				</div>
			</div>';
}
echo ($karten != '') ? '<div class="ally-karten">'.$karten.'</div>' : '<div class="mod-leer">Es liegen keine B&uuml;ndnisanfragen vor.</div>';
echo '</div>';
rahmen_unten();

?>
<?php include('ally/ally.footer.inc.php'); ?>

</body>
</html>
