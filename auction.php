<?php
$GLOBALS['deactivate_old_design'] = true;

include "inc/header.inc.php";
include "lib/transactioni.lib.php";
include "functions.php";
include 'inc/userartefact.inc.php';
//include "lib/map_system.class.php";
$pt = loadPlayerTechs($_SESSION['ums_user_id']);

$ps = loadPlayerStorage($_SESSION['ums_user_id']);
$GLOBALS['ps'] = $ps;

$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$techs = $row["techs"];
$defenseexp = $row["defenseexp"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];
$mysc2 = $row["sc2"];
$gr01 = $restyp01;
$gr02 = $restyp02;
$gr03 = $restyp03;
$gr04 = $restyp04;
$gr05 = $restyp05;

//freie Artefaktplätze
$free_artefact_places = get_free_artefact_places($_SESSION['ums_user_id']);

//Maximale Tickanzahl auslesen
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1", $db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];


////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten = mysqli_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=22 AND user_id='$ums_user_id'");
$artbonus_auktion = 0;
while ($row = mysqli_fetch_array($db_daten)) {
    $artbonus_auktion = $artbonus_auktion + $ua_werte[$row["id"] - 1][$row["level"] - 1][0];
}

if ($artbonus_auktion > 50) {
    $artbonus_auktion = 50;
}

//Nachlass in Prozent definieren
$nachlass = 25;

$tradescore = 10000;

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Auktion</title>
<?php
include "cssinclude.php";
?>
<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>
</head>
<body>
<?php

$content = '';

//hat man die benötigte Technologie?
if (!hasTech($pt, 4)) {
    $techcheck = "SELECT tech_name FROM de_tech_data WHERE tech_id=4";
    $db_tech = mysqli_query($GLOBALS['dbi'], $techcheck);
    $row_techcheck = mysqli_fetch_array($db_tech);


    $content .= '<br>';
    $content .= rahmen_oben('Fehlende Technologie', false);
    $content .= '<table width="572" border="0" cellpadding="0" cellspacing="0">';
    $content .= '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=4" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_4.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'], $_SESSION['ums_rasse']).'</td>
	</tr>';
    $content .= '</table>';
    $content .= rahmen_unten(false);
} else {

    if (setLock($ums_user_id)) {

        $content .= '<div class="info_box">Nach dem Start der Auktion sinkt 1.000 Wirtschaftsticks lang der Preis. Auktionen, die man selbst gestartet hat, haben einen Nachlass von '.$nachlass.'%.</div><br>';

        //Rahmen oben
        $content .= rahmen_oben('Auktionen', false);
        //Auktionen Kopfzeile
        $content .= '<table width="572" border="0" cellpadding="0" cellspacing="0">';
        $content .= '
		<tr class="cell" style="font-weight: bold; text-align: center;">
			<td>Artikel</td>
			<td>Preis</td>
			<td>Aktion</td>
		</tr>';

        //Handelspunkte
        $tradesystemscore_str = '<br><span style="font-size: 10px;">+ '.number_format($tradescore, 0, ",", ".").' Handelspunkte</span>';


        //die einzelnen Auktionen ausgeben
        $resnamen = array('Multiplex','Dyharra','Iradium','Eternium','Tronic');

        $db_daten = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_auction WHERE bidder=0 ORDER BY start_wt ASC");
        while ($row = mysqli_fetch_array($db_daten)) {
            //möchte man bieten?
            $bid = intval($_REQUEST['bid'] ?? -1);
            $bid_has_all = false;

            //Ertrag der Auktion
            $reward = unserialize($row['reward']);
            $is_artefact = false;
            if($reward[0] == 'A'){
                $is_artefact = true;
            }

            //überprüfen ob es Platz im Artefaktgebäude gibt, wenn nötig und dann ggf. den Kauf verhindern
            if ($is_artefact && $free_artefact_places < 1) {
                $bid = -1;
            }

            //hat man sie selbst erstellt?
            if ($row['creator'] == $_SESSION['ums_user_id']) {
                $creator = true;
            } else {
                $creator = false;
            }

            ////////////////////////////////////////////////////////////////
            //Reduzierung
            ////////////////////////////////////////////////////////////////
            $reduzierung = $maxtick - $row['start_wt'];
            if ($reduzierung > 1000) {
                $reduzierung = 1000;
            }

            ////////////////////////////////////////////////////////////////
            //Kosten
            ////////////////////////////////////////////////////////////////
            $preis = '';
            $cost = unserialize($row['cost']);

            //Kosten verringern, wenn man der Creator ist
            $cost = unserialize($row['cost']);
            $amount = $cost[2];

            //Zeitreduziererung
            if ($reduzierung > 0) {
                $amount = round($amount - ($amount * $reduzierung) / 1001);
            }

            //selbst erstellte Auktion?
            if ($creator) {
                $reduzierung_in_prozent = ($nachlass + $artbonus_auktion) / 100;
                $amount = ceil($amount - ($amount * $reduzierung_in_prozent));
                $nachlass_str = ' / <span style="color: #00FF00;">'.number_format(($nachlass + $artbonus_auktion), 2, ",", ".").' % Preisnachlass</span>';
            } else {
                $reduzierung_in_prozent = $artbonus_auktion / 100;
                $amount = ceil($amount - ($amount * $reduzierung_in_prozent));
                $nachlass_str = ' / '.number_format($artbonus_auktion, 2, ",", ".").' % Preisnachlass';
            }

            switch ($cost[0]) {
                case 'R': //Standard-Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $pd['restyp0'.$cost[1]]) {
                            //DB updaten
                            $sql = "UPDATE de_user_data SET restyp0".$cost[1]."=restyp0".$cost[1]."-'".$amount."', tradesystemscore=tradesystemscore+'".$tradescore."', tradesystemtrades=tradesystemtrades+1 WHERE user_id='".$_SESSION['ums_user_id']."'";
                            mysqli_query($GLOBALS['dbi'], $sql);
                            $bid_has_all = true;
                        }
                    }

                    //wenn man nicht genug zum bezahlen hat, rot einfärben
                    $fehlende_res_color = '';
                    if ($amount > $pd['restyp0'.$cost[1]]) {
                        $fehlende_res_color = 'color: #ec0011;';
                    }


                    $preis .= '<div style="display: flex;">';
                    $preis .= '<div style="width: 50px;" rel="tooltip" title="'.number_format($amount, 0, ",", ".").' '.$resnamen[$cost[1] - 1].'<br>Lagerbestand: '.number_format($pd['restyp0'.$cost[1]], 0, ",", ".").'"><img src="g/icon'.$cost[1].'.png" style="width: 50px; height: auto;"></div>';
                    $preis .= '<div style="flex-grow: 1; padding-left: 10px; font-size: 18px; height: 100%; padding-top: 8px;'.$fehlende_res_color.'">'.formatMasseinheit($amount).' '.$resnamen[$cost[1] - 1].'<br><span style="font-size: 10px;">WT: '.number_format($reduzierung, 0, ",", ".").$nachlass_str.'</span></div>';
                    $preis .= '</div>';


                    break;
                case 'I': //neue Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $ps[$cost[1]]['item_amount']) {
                            //DB updaten
                            change_storage_amount($_SESSION['ums_user_id'], $cost[1], $amount * -1, false);

                            $sql = "UPDATE de_user_data SET tradesystemscore=tradesystemscore+'".$tradescore."', tradesystemtrades=tradesystemtrades+1 WHERE user_id='".$_SESSION['ums_user_id']."'";
                            mysqli_query($GLOBALS['dbi'], $sql);

                            $bid_has_all = true;
                        }
                    }

                    //wenn man nicht genug zum bezahlen hat, rot einfärben
                    $fehlende_res_color = '';
                    if ($amount > $ps[$cost[1]]['item_amount']) {
                        $fehlende_res_color = 'color: #ec0011;';
                    }

                    $preis .= '<div style="display: flex;">';
                    $preis .= '<div style="width: 50px;" rel="tooltip" title="'.number_format($amount, 0, ",", ".").' '.$ps[$cost[1]]['item_name'].'<br>Lagerbestand: '.number_format($ps[$cost[1]]['item_amount'], 0, ",", ".").'"><img src="g/item'.$cost[1].'.png" style="width: 50px; height: auto;"></div>';
                    $preis .= '<div style="flex-grow: 1; padding-left: 10px; font-size: 18px; height: 100%; padding-top: 8px;'.$fehlende_res_color.'">'.formatMasseinheit($amount).' '.$ps[$cost[1]]['item_name'].'<br><span style="font-size: 10px;">WT: '.number_format($reduzierung, 0, ",", ".").$nachlass_str.'</span></div>';
                    $preis .= '</div>';

                    break;
                case 'C': //Credits
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $pd['credits']) {
                            //DB updaten
                            changeCredits($_SESSION['ums_user_id'], $amount * -1, 'Auktion: '.$bid);

                            $sql = "UPDATE de_user_data SET tradesystemscore=tradesystemscore+'".$tradescore."', tradesystemtrades=tradesystemtrades+1 WHERE user_id='".$_SESSION['ums_user_id']."'";
                            mysqli_query($GLOBALS['dbi'], $sql);

                            $bid_has_all = true;
                        }
                    }

                    //wenn man nicht genug zum bezahlen hat, rot einfärben
                    $fehlende_res_color = '';
                    if ($amount > $pd['credits']) {
                        $fehlende_res_color = 'color: #ec0011;';
                    }

                    $preis .= '<div style="display: flex;">';
                    $preis .= '<div style="width: 50px;" rel="tooltip" title="'.number_format($amount, 0, ",", ".").' Credits<br>Lagerbestand: '.number_format($pd['credits'], 0, ",", ".").'"><img src="g/credits.gif" style="width: 50px; height: auto; margin-top: 11px;"></div>';
                    $preis .= '<div style="flex-grow: 1; padding-left: 10px; font-size: 18px; height: 100%; padding-top: 8px;'.$fehlende_res_color.'">'.number_format($amount, 0, ",", ".").' Credits<br><span style="font-size: 10px;">WT: '.number_format($reduzierung, 0, ",", ".").$nachlass_str.'</span></div>';
                    $preis .= '</div>';


                    break;
            }

            ////////////////////////////////////////////////////////////////
            //Belohnung
            ////////////////////////////////////////////////////////////////
            $artikel = '';
            $amount = $reward[2] ?? 0;
            switch ($reward[0]) {
                case 'A': //Artefakt
                    $artid = $reward[1];
                    //bietet man dafür und hat Platz im Artefaktgebäude?
                    if ($bid == $row['id'] && $free_artefact_places > 0) {
                        if ($bid_has_all) {
                            $sql = "INSERT INTO de_user_artefact (user_id, id, level) VALUES ('".$_SESSION['ums_user_id']."', '$artid', '1')";
                            mysqli_query($GLOBALS['dbi'], $sql);
                            $free_artefact_places--;
                        }
                    } else {
                        $bid_has_all = false;
                    }

                    $artikel .= '<div style="display: flex;">';
                    $artikel .= '<div style="width: 50px;" rel="tooltip" title="1 '.$ua_name[$artid - 1].'-Artefakt (Stufe 1)<br>'.$ua_desc[$artid - 1].'"><img src="'.$ums_gpfad.'g/arte'.$artid.'.gif"></div>';
                    $artikel .= '<div style="flex-grow: 1; padding: 8px 0 0 10px; font-size: 18px; vertical-align: middle;">'.$ua_name[$reward[1] - 1].$tradesystemscore_str.'</div>';
                    $artikel .= '</div>';
                    break;
                case 'R': //Standard-Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($bid_has_all) {
                            //DB updaten
                            $sql = "UPDATE de_user_data SET restyp0".$reward[1]."=restyp0".$reward[1]."+'".$amount."' WHERE user_id='".$_SESSION['ums_user_id']."'";
                            //echo $sql;
                            mysqli_query($GLOBALS['dbi'], $sql);
                        }
                    }

                    $artikel .= '<div style="display: flex;">';
                    $artikel .= '<div style="width: 50px;" rel="tooltip" title="'.number_format($reward[2], 0, ",", ".").' '.$resnamen[$reward[1] - 1].'"><img src="g/icon'.$reward[1].'.png" style="width: 50px; height: auto;"></div>';
                    $artikel .= '<div style="flex-grow: 1; padding: 8px 0 0 10px; font-size: 18px; vertical-align: middle;">'.number_format($reward[2], 0, ",", ".").' '.$resnamen[$reward[1] - 1].$tradesystemscore_str.'</div>';
                    $artikel .= '</div>';


                    break;
                case 'I': //neue Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($bid_has_all) {
                            //DB updaten
                            change_storage_amount($_SESSION['ums_user_id'], $reward[1], $amount, false);
                            $bid_has_all = true;
                        }
                    }
                    $artikel .= '<div style="display: flex;">';
                    $artikel .= '<div style="width: 50px;" rel="tooltip" title="'.number_format($reward[2], 0, ",", ".").' '.$ps[$reward[1]]['item_name'].'"><img src="g/item'.$reward[1].'.png" style="width: 50px; height: auto;"></div>';
                    $artikel .= '<div style="flex-grow: 1; padding: 8px 0 0 10px; font-size: 18px; vertical-align: middle;">'.number_format($reward[2], 0, ",", ".").' '.$ps[$reward[1]]['item_name'].$tradesystemscore_str.'</div>';
                    $artikel .= '</div>';

                    break;
                    /*
                    case 'C': //Credits
                        //bietet man dafür?
                        if($bid==$row['id']){
                            if($bid_has_all){
                                //DB updaten
                                changeCredits($_SESSION['ums_user_id'], $amount, 'Auktion: '.$bid);
                                $bid_has_all=true;
                            }
                        }
                        $artikel=number_format($reward[2], 0,",",".").' Credits';
                    break;
                    */
            }

            ////////////////////////////////////////////////////////////////
            //Auktion auf beendet setzen
            ////////////////////////////////////////////////////////////////

            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    $sql = "UPDATE de_auction SET bidder='".$_SESSION['ums_user_id']."' WHERE id='".$row['id']."'";
                    mysqli_query($GLOBALS['dbi'], $sql);

                }
            }

            ////////////////////////////////////////////////////////////////
            //Aktion
            ////////////////////////////////////////////////////////////////
            $aktion = '<a href="?bid='.$row['id'].'" onclick="return confirm(\'F&uuml;r diesen Artikel bieten?\')">bieten</a>';
            //nach dem Bieten Aktionsmöglichkeit entfernen und Spielerdaten neu laden
            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    $aktion = '';

                    //Spielerdaten nach Update neu auslesen
                    $pd = loadPlayerData($_SESSION['ums_user_id']);
                    $rowx = $pd;
                    $restyp01 = $rowx['restyp01'];
                    $restyp02 = $rowx['restyp02'];
                    $restyp03 = $rowx['restyp03'];
                    $restyp04 = $rowx['restyp04'];
                    $restyp05 = $rowx['restyp05'];
                    $punkte = $rowx["score"];
                    $techs = $rowx["techs"];
                    $defenseexp = $rowx["defenseexp"];
                    $newtrans = $rowx["newtrans"];
                    $newnews = $rowx["newnews"];
                    $sector = $rowx["sector"];
                    $system = $rowx["system"];
                    $mysc2 = $rowx["sc2"];
                    $gr01 = $restyp01;
                    $gr02 = $restyp02;
                    $gr03 = $restyp03;
                    $gr04 = $restyp04;
                    $gr05 = $restyp05;

                }
            }

            if ($is_artefact && $free_artefact_places < 1) {
                $aktion = '';
            }


            ////////////////////////////////////////////////////////////////
            //Auktion anzeigen
            ////////////////////////////////////////////////////////////////
            $c1 = 0;
            if ($c1 == 0) {
                $c1 = 1;
                $bg = 'cell1';
            } else {
                $c1 = 0;
                $bg = 'cell';
            }
            $content .= '
			<tr style="text-align: right; vertical-align: middle;">
				<td class="cell" style="text-align: left;">'.$artikel.'</td>
				<td class="cell" style="text-align: left;">'.$preis.'</td>
				<td class="cell" style="text-align: center;">'.$aktion.'</td>
			</tr>';

            //Meldung beim Bieten
            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    $content .= '
					<tr style="text-align: right;">
						<td class="cell" colspan="3" style="color: #00FF00;">Die Auktion wurde best&auml;tigt.</td>
					</tr>';
                } else {
                    $content .= '
					<tr style="text-align: right;">
						<td class="cell" colspan="3" style="color: #FF0000;">Du kannst Dir diese Auktion nicht leisten.</td>
					</tr>';
                }
            }

            //Trenner
            $content .= '<tr><td class="cell" colspan="3"><div style="border-top: 1px solid #666666; height: 3px;"></div></td></tr>';
        }


        $content .= '</table>';
        //Rahmen unten
        $content .= rahmen_unten(false);

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print("ERROR 17<br><br><br>");
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">ERROR 18</font><br><br>';
    }


}



include "resline.php";

echo $content;


?>
<br>
<?php include "fooban.php"; ?>
</body>
</html>
