<?php
$GLOBALS['deactivate_old_design'] = true;

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.dailygift.lang.php');
include('inc/userartefact.inc.php');
include('lib/transaction.lib.php');
include_once('functions.php');

$pt = loadPlayerTechs($_SESSION['ums_user_id']);
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
$sector = $row["sector"];
$system = $row["system"];
$dailyallygift = $row['dailyallygift'];
$ally_id = $row['ally_id'];
$allytag = $row['allytag'];
$allystatus = $row['ally_status'];

//freien platz im Artefaktgebäude feststellen
$freeartefactplaces = get_free_artefact_places($_SESSION['ums_user_id']);



?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allydailygift_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
echo '<div style="width: 600px; margin-left: auto; margin-right: auto;">';

$allyrelverbreitung_need = array(0, 1000, 2000, 3000, 4000, 5000, 9999999);

//überprüfen ob man in einer allianz ist
if ($ally_id > 0 && $allystatus == 1) {

    //der Bonus hängt von den Rundensiegartefakten ab
    //allydaten laden
    $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, questpoints FROM de_allys WHERE id=?", [$ally_id]);
    $row = mysqli_fetch_array($result);
    $allyid = $row['id'];
    $allyrelverbreitung = $row['questpoints'];

    /////////////////////////////////////////////////////////////
    //überprüfen ob man einen bonus abholen möchte
    /////////////////////////////////////////////////////////////
    if (isset($_REQUEST['getdailybonus']) && $_REQUEST['getdailybonus'] == 1) {
        //transaktionsbeginn
        if (setLock($_SESSION["ums_user_id"])) {
            //auslesen ob er das geschenk schon bekommen hat
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT dailyallygift, npc FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
            $row = mysqli_fetch_array($result);
            if ($row['dailyallygift'] == 1) {
                $isPlayer = $row['npc'] == 0;
                //in der db und session den bonus für den tag deaktivieren
                $dailyallygift = 0;
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET dailyallygift=0 WHERE user_id=?", [$_SESSION['ums_user_id']]);

                //feststellen welchen bonus man bekommt
                $bonus_anzahl = 6;
                for ($bonus = 0;$bonus < $bonus_anzahl;$bonus++) {
                    if ($allyrelverbreitung >= $allyrelverbreitung_need[$bonus]) {
                        if ($allyrelverbreitung >= $allyrelverbreitung_need[$bonus + 1] && $bonus < $bonus_anzahl - 1) {
                            //grau
                        } else {
                            //grün
                            //schleife beenden, da das ziel gefunden worden ist
                            break;
                        }
                    } else {
                        //rot
                    }
                }

                //bonus hinterlegen
                $bonusstr = '';
                switch ($bonus) {
                    case 0: // Rang 0
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+1, kartefakt=kartefakt+1, defenseexp=defenseexp+1000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>1 Tronic<br>1.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>1 Titanen-Energiekern';

                        for ($i = 0;$i < 1;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+1 WHERE id=?", [$ally_id]);
                        $bonusstr .= '<br>1 Allianzartefakt';

                        //Titanen-Energiekern
                        $amount = 1;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);
                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }
                        break;

                    case 1: // Rang 1
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+2, kartefakt=kartefakt+1, defenseexp=defenseexp+2000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>2 Tronic<br>2.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';
                        for ($i = 0;$i < 2;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+2 WHERE id='$ally_id'");
                        $bonusstr .= '<br>2 Allianzartefakte';

                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }

                        //Titanen-Energiekern
                        $amount = 2;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

                        changeAllyStorageAmount($ally_id, 13, 1, false);
                        $bonusstr .= '<br>Allianz: 1 Quantenglimmer';
                        break;

                    case 2: // Rang 2
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+3, kartefakt=kartefakt+1, defenseexp=defenseexp+3000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>3 Tronic<br>3.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';

                        for ($i = 0;$i < 2;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+2 WHERE id=?", [$ally_id]);
                        $bonusstr .= '<br>2 Allianzartefakte';

                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }

                        //Titanen-Energiekern
                        $amount = 2;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

                        changeAllyStorageAmount($ally_id, 13, 2, false);
                        $bonusstr .= '<br>Allianz: 2 Quantenglimmer';
                        break;

                    case 3: // Rang 3
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+4, kartefakt=kartefakt+1, defenseexp=defenseexp+4000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>4 Tronic<br>4.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';

                        for ($i = 0;$i < 2;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+3 WHERE id='$ally_id'");
                        $bonusstr .= '<br>3 Allianzartefakte';

                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }

                        //Titanen-Energiekern
                        $amount = 3;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

                        changeAllyStorageAmount($ally_id, 13, 3, false);
                        $bonusstr .= '<br>Allianz: 3 Quantenglimmer';
                        break;

                    case 4: // Rang 4
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+5, kartefakt=kartefakt+1, defenseexp=defenseexp+5000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>5 Tronic<br>5.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';

                        for ($i = 0;$i < 3;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+3 WHERE id='$ally_id'");
                        $bonusstr .= '<br>3 Allianzartefakte';

                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }

                        //Titanen-Energiekern
                        $amount = 3;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

                        changeAllyStorageAmount($ally_id, 13, 4, false);
                        $bonusstr .= '<br>Allianz: 4 Quantenglimmer';
                        break;

                    case 5: // Rang 5
                        mysqli_execute_query($GLOBALS['dbi'], 
                            "UPDATE de_user_data SET restyp05=restyp05+6, kartefakt=kartefakt+1, defenseexp=defenseexp+6000 WHERE user_id=?", 
                            [$_SESSION['ums_user_id']]);

                        $bonusstr = '<br>1 Kriegsartefakt<br>6 Tronic<br>6.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>4 Titanen-Energiekern';

                        for ($i = 0;$i < 3;$i++) {
                            if ($freeartefactplaces > 0) {
                                $artid = mt_rand(1, 15);
                                mysqli_execute_query($GLOBALS['dbi'], 
                                    "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                    [$_SESSION['ums_user_id'], $artid]);
                                $bonusstr .= '<br>1 '.$ua_name[$artid - 1].'-Artefakt';
                                $freeartefactplaces--;
                            }
                        }

                        //Allianzartefakt
                        mysqli_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+4 WHERE id='$ally_id'");
                        $bonusstr .= '<br>4 Allianzartefakte';

                        if ($isPlayer) {
                            createAuction($_SESSION['ums_user_id']);
                        }

                        //Titanen-Energiekern
                        $amount = 4;
                        change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

                        changeAllyStorageAmount($ally_id, 13, 5, false);
                        $bonusstr .= '<br>Allianz: 1 Quantenglimmer';
                        break;

                    default:
                        echo 'Error 1';
                        break;
                }


                //info an den spieler, dass er den bonus erhalten hat
                $msg = '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok"><b>'.$allydailygift_lang['bonuserhalten'].'</b>'.$bonusstr.'</div></div>';
                //info für den allychat
                $allydailygift_lang['bonuserhaltenchat'] = '<font color="#802ec1">'.str_replace("{WERT1}", $_SESSION['ums_spielername'], $allydailygift_lang['bonuserhaltenchat']).'</font>';
                insert_chat_msg($ally_id, 1, '', $allydailygift_lang['bonuserhaltenchat']);
            }

            //lock wieder entfernen
            $erg = releaseLock($_SESSION['ums_user_id']); //Lösen des Locks und Ergebnisabfrage
            if ($erg) {
                //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
            } else {
                print("Datensatz Nr. ".$_SESSION['ums_user_id']." konnte nicht entsperrt werden!<br><br><br>");
            }
        }//lock ende
    }

    include "resline.php";

    if (!empty($msg)) {
        echo $msg;
    }

    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////
    // boni darstellen
    /////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////

    //Stufen: erreicht und überholt, aktuelle Stufe, noch nicht erreicht
    $stufe = array();
    for ($i = 0;$i <= 5;$i++) {
        if ($allyrelverbreitung >= $allyrelverbreitung_need[$i]) {
            if ($allyrelverbreitung >= $allyrelverbreitung_need[$i + 1] && $i < count($allydailygift_lang['bonusname']) - 1) {
                $stufe[] = 'ally-stufe-vorbei';
            } else {
                $stufe[] = 'ally-stufe-aktiv';
            }
        } else {
            $stufe[] = 'ally-stufe-offen';
        }
    }

    //Belohnungen je Stufe
    $belohnungen = array(
        'Kriegsartefakt' => array(1, 1, 1, 1, 1, 1),
        'Allianzartefakt' => array(1, 2, 2, 3, 3, 4),
        'Tronic' => array(1, 2, 3, 4, 5, 6),
        'Verteidigungsanlagen-XP' => array('1.000', '2.000', '3.000', '4.000', '5.000', '6.000'),
        'Zufallsartefakt<sup>1</sup>' => array(1, 2, 2, 2, 3, 3),
        'Auktion<sup>2</sup>' => array(1, 1, 1, 1, 1, 1),
        'Titanen-Energiekern' => array(1, 2, 2, 3, 3, 4),
        'Allianz-Quantenglimmer' => array(0, 1, 2, 3, 4, 5),
    );

    rahmen_oben('Allianzbonus');
    echo '<div class="ally mod">';

    echo '
	<div class="ally-kacheln ally-kacheln-2">
		<div class="ov-wert"><span class="mod-typ">Rundensiegartefakte der Allianz</span><b>'.number_format($allyrelverbreitung, 0, '', '.').'</b></div>
		<div class="ov-wert"><span class="mod-typ">'.$allydailygift_lang['freieartefaktplaetze'].'</span><b>'.$freeartefactplaces.'</b></div>
	</div>
	<div class="mod-hinweis ally-abstand">Die Gr&ouml;&szlig;e des t&auml;glichen Allianz-Bonus h&auml;ngt von der Anzahl der Rundensiegartefakte Deiner Allianz ab. Die hervorgehobene Spalte zeigt Euren aktuellen Bonus.</div>';

    //Raster: Kopf mit den Schwellen, je Zeile eine Belohnung
    echo '<div class="ally-bonusraster">';
    echo '<span class="ally-bonus-label mod-typ">ab Rundensiegartefakten</span>';
    for ($i = 0;$i <= 5;$i++) {
        echo '<span class="ally-bonus-kopf '.$stufe[$i].'">'.number_format($allyrelverbreitung_need[$i], 0, '', '.').'</span>';
    }
    foreach ($belohnungen as $name => $werte) {
        echo '<span class="ally-bonus-label">'.$name.'</span>';
        foreach ($werte as $i => $wert) {
            echo '<span class="ally-bonus-wert '.$stufe[$i].'">'.$wert.'</span>';
        }
    }
    echo '</div>';

    if ($dailyallygift == 0) {
        echo '<div class="ally-aktionen ally-aktionen-mitte"><span class="mod-feld">Der Bonus wurde heute bereits abgeholt.</span></div>';
    } else {
        echo '<div class="ally-aktionen ally-aktionen-mitte"><a class="mod-btn" href="?getdailybonus=1">Bonus abholen</a></div>';
    }

    echo '
	<div class="ally-fussnoten">
		<sup>1</sup> zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
		<br><sup>2</sup> gestartete Auktion im Auktionshaus mit einem Preisnachlass
	</div>';

    echo '</div>';
    rahmen_unten();


} else {
    include('resline.php');
    //ohne Allianz erklären, wofür der Bonus da ist und wie man zu einer Allianz kommt
    rahmen_oben('Allianzbonus');
    echo '
	<div class="ally mod">
		<div class="mod-leer">
			<b>'.$allydailygift_lang['keineally'].'</b><br><br>
			'.$allydailygift_lang['keineally_info'].'
			<div class="ally-aktionen ally-aktionen-mitte"><a href="allymain.php" class="mod-btn">'.$allydailygift_lang['zurallianz'].'</a></div>
		</div>
	</div>';
    rahmen_unten();
}

?>
<br>
</div>

</body>
</html>