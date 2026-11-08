<?php
use DieEwigen\DE2\Model\Sperre;
include "inc/header.inc.php";
include "functions.php";
include 'inc/userartefact.inc.php';

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
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
$maxtick = $row["tick"];


////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE id=22 AND user_id=?;", [$_SESSION['ums_user_id']]);
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

//Bild der Ware mit Tooltip, die Menge steht als Zähler in der Ecke (bei Artefakten nicht)
function auction_bild($img, $tooltip, $menge = 0)
{
    return '<div class="auk-bild" rel="tooltip" title="'.$tooltip.'"><img src="gp/g/'.$img.'" alt="">'
        .($menge > 0 ? '<span class="auk-menge">'.formatMasseinheit($menge).'</span>' : '').'</div>';
}

//Prozentwert ohne überflüssige Nachkommastellen
function auction_prozent($wert)
{
    return number_format($wert, (floor($wert) == $wert) ? 0 : 2, ",", ".");
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Auktion</title>
<?php
include "cssinclude.php";
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

$content = '';

//hat man die benötigte Technologie?
if (!hasTech($pt, 4)) {
    $content .= '<br>'.\DieEwigen\DE2\View\FehlendeTechnologie::karte(4);
} else {

    if (Sperre::setzen($GLOBALS['dbi'], $_SESSION['ums_user_id'])) {

        //Bestände erst innerhalb der Sperre laden, sonst prüft eine zweite Anfrage gegen veraltete Werte
        $pd = loadPlayerData($_SESSION['ums_user_id']);
        $ps = loadPlayerStorage($_SESSION['ums_user_id']);
        $GLOBALS['ps'] = $ps;

        //Karten der einzelnen Auktionen, Kopf und Statuszeile kommen nach der Schleife davor
        $karten = '';
        $anz_auktionen = 0;
        $anz_offen = 0;
        //vorkommende Waren und Währungen für den Filter im Kopf
        $filter_waren = array();
        $filter_waehrungen = array();

        //die einzelnen Auktionen ausgeben
        $resnamen = array('Multiplex','Dyharra','Iradium','Eternium','Tronic');

        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_auction WHERE bidder=0 ORDER BY start_wt ASC", []);
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

            //die Auktion vor dem Bezahlen für sich reservieren: bieten zwei Spieler gleichzeitig, bekommt sie nur einer
            if ($bid == $row['id']) {
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_auction SET bidder=? WHERE id=? AND bidder=0", [$_SESSION['ums_user_id'], $row['id']]);
                if (mysqli_affected_rows($GLOBALS['dbi']) != 1) {
                    $bid = -1;
                }
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
            $nachlass_str = '';
            $nachlass_teile = array();
            $nachlass_prozent = $creator ? $nachlass + $artbonus_auktion : $artbonus_auktion;
            if ($creator) {
                $reduzierung_in_prozent = ($nachlass + $artbonus_auktion) / 100;
                $amount = ceil($amount - ($amount * $reduzierung_in_prozent));
                $nachlass_teile[] = 'Eigene Auktion: '.auction_prozent($nachlass).' %';
            } else {
                $reduzierung_in_prozent = $artbonus_auktion / 100;
                $amount = ceil($amount - ($amount * $reduzierung_in_prozent));
            }
            if ($artbonus_auktion > 0) {
                $nachlass_teile[] = $ua_name[21].'-Artefakte: '.auction_prozent($artbonus_auktion).' %';
            }
            if ($nachlass_prozent > 0) {
                $nachlass_str = '<span class="mod-chip mod-chip-gruen" rel="tooltip" title="Preisnachlass&'.implode('<br>', $nachlass_teile).'">&minus;'.auction_prozent($nachlass_prozent).' %'.($creator ? ' eigene Auktion' : '').'</span>';
            }

            //Startpreis für diesen Spieler (mit Nachlass, ohne Preisverfall)
            $startpreis = ceil($cost[2] - ($cost[2] * $reduzierung_in_prozent));

            switch ($cost[0]) {
                case 'R': //Standard-Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $pd['restyp0'.$cost[1]]) {
                            //DB updaten
                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp0".$cost[1]."=restyp0".$cost[1]."-?, tradesystemscore=tradesystemscore+?, tradesystemtrades=tradesystemtrades+1 WHERE user_id=?", 
                                [$amount, $tradescore, $_SESSION['ums_user_id']]);
                            $bid_has_all = true;
                        }
                    }

                    $preis_img = 'icon'.$cost[1].'.png';
                    $preis_name = $resnamen[$cost[1] - 1];
                    $preis_lager = $pd['restyp0'.$cost[1]];
                    $preis_text = formatMasseinheit($amount);
                    break;
                case 'I': //neue Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $ps[$cost[1]]['item_amount']) {
                            //DB updaten
                            change_storage_amount($_SESSION['ums_user_id'], $cost[1], $amount * -1, false);

                            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET tradesystemscore=tradesystemscore+?, tradesystemtrades=tradesystemtrades+1 WHERE user_id=?", 
                                [$tradescore, $_SESSION['ums_user_id']]);

                            $bid_has_all = true;
                        }
                    }

                    $filename = 'item'.$cost[1].'.png';
                    //Item 3-10 verwendet die VS-Rohstoffe und dafür verwendet man einfach deren Grafiken
                    if($cost[1]>=3 && $cost[1]<=12){
                        $nummer=$cost[1]-2;
                        if($nummer<10){ $nummer='0'.$nummer;}
                        $filename = 'ele'.$nummer.'.gif';
                    }elseif($cost[1]>=14 && $cost[1]<=18){
                        //Handelswaren I-V: Symbol der Fabrik, die sie herstellt (Omega 24 bis Ypsilon 20, wie production.php)
                        $filename = 'r/'.(38-$cost[1]).'_g.gif';
                    }

                    $preis_img = $filename;
                    $preis_name = $ps[$cost[1]]['item_name'];
                    $preis_lager = $ps[$cost[1]]['item_amount'];
                    $preis_text = formatMasseinheit($amount);
                    break;
                case 'C': //Credits
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($amount <= $pd['credits']) {
                            //DB updaten
                            changeCredits($_SESSION['ums_user_id'], $amount * -1, 'Auktion: '.$bid);

                            $sql = "UPDATE de_user_data SET tradesystemscore=tradesystemscore+?, tradesystemtrades=tradesystemtrades+1 WHERE user_id=?";
                            mysqli_execute_query($GLOBALS['dbi'], $sql, [$tradescore, $_SESSION['ums_user_id']]);

                            $bid_has_all = true;
                        }
                    }

                    $preis_img = 'credits.gif';
                    $preis_name = 'Credits';
                    $preis_lager = $pd['credits'];
                    $preis_text = number_format($amount, 0, ",", ".");
                    break;
            }

            //wenn man nicht genug zum Bezahlen hat, rot einfärben und den Fehlbetrag nennen
            $preis_fehlt = $amount > $preis_lager;
            if ($preis_fehlt) {
                $lager_str = '<span class="auk-fehlt-text">Es fehlen '.formatMasseinheit($amount - $preis_lager).'</span>';
            } else {
                $lager_str = 'Lager '.formatMasseinheit($preis_lager);
            }
            $preis = '<div class="auk-preis'.($preis_fehlt ? ' auk-fehlt' : '').'" rel="tooltip" title="'.number_format($amount, 0, ",", ".").' '.$preis_name.'&Lagerbestand: '.number_format($preis_lager, 0, ",", ".").'">'
                .'<img src="gp/g/'.$preis_img.'" alt=""><span class="auk-betrag">'.$preis_text.'</span><span class="auk-einheit">'.$preis_name.'</span></div>'
                .'<div class="auk-lager">'.$lager_str.'</div>';

            //Preisverfall als Balken; der Startpreis steht im Tooltip
            $verfall = max(0, $reduzierung);
            $verfall_str = '<div class="auk-verfall" rel="tooltip" title="Preisverfall&Der Preis sinkt nach dem Start 1.000 WT lang gleichm&auml;&szlig;ig.<br>Bisher: '.number_format($verfall, 0, ",", ".").' / 1.000 WT<br>Startpreis: '.number_format($startpreis, 0, ",", ".").' '.$preis_name.'">'
                .'<div class="mod-balken"><span style="width: '.($verfall / 10).'%;"></span></div>'
                .'<div class="auk-verfall-text">'.($verfall >= 1000 ? 'Tiefstpreis erreicht' : 'Preis f&auml;llt noch '.number_format(1000 - $verfall, 0, ",", ".").' WT').'</div>'
                .'</div>';

            ////////////////////////////////////////////////////////////////
            //Belohnung
            ////////////////////////////////////////////////////////////////
            $artikel = '';
            $artikel_typ = '';
            $artikel_name = '';
            $artikel_info = '';
            $amount = $reward[2] ?? 0;
            switch ($reward[0]) {
                case 'A': //Artefakt
                    $artid = $reward[1];
                    //bietet man dafür und hat Platz im Artefaktgebäude?
                    if ($bid == $row['id'] && $free_artefact_places > 0) {
                        if ($bid_has_all) {
                            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_artefact (user_id, id, level) VALUES (?, ?, 1)", 
                                [$_SESSION['ums_user_id'], $artid]);
                            $free_artefact_places--;
                        }
                    } else {
                        $bid_has_all = false;
                    }

                    $artikel = auction_bild('arte'.$artid.'.gif', '1 '.$ua_name[$artid - 1].'-Artefakt (Stufe 1)&'.$ua_desc[$artid - 1]);
                    $artikel_typ = 'Artefakt &middot; Stufe 1';
                    $artikel_name = $ua_name[$artid - 1];
                    //Wirkung direkt anzeigen, auf dem Handy gibt es keinen Tooltip
                    $artikel_info = $ua_desc[$artid - 1];
                    break;
                case 'R': //Standard-Rohstoffe
                    //bietet man dafür?
                    if ($bid == $row['id']) {
                        if ($bid_has_all) {
                            //DB updaten
                            $sql = "UPDATE de_user_data SET restyp0".$reward[1]."=restyp0".$reward[1]."+? WHERE user_id=?";
                            //echo $sql;
                            mysqli_execute_query($GLOBALS['dbi'], $sql, [$amount, $_SESSION['ums_user_id']]);
                        }
                    }

                    $artikel_typ = 'Rohstoff';
                    $artikel_name = $resnamen[$reward[1] - 1];
                    $artikel = auction_bild('icon'.$reward[1].'.png', number_format($reward[2], 0, ",", ".").' '.$artikel_name, $reward[2]);
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
                    $artikel_typ = 'Ressource';
                    $artikel_name = $ps[$reward[1]]['item_name'];
                    $artikel = auction_bild('item'.$reward[1].'.png', number_format($reward[2], 0, ",", ".").' '.$artikel_name, $reward[2]);
                    break;
            }

            ////////////////////////////////////////////////////////////////
            //Auktion auf beendet setzen
            ////////////////////////////////////////////////////////////////

            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_auction SET bidder=? WHERE id=?",
                        [$_SESSION['ums_user_id'], $row['id']]);

                } else {
                    //nicht bezahlt: Reservierung wieder freigeben
                    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_auction SET bidder=0 WHERE id=? AND bidder=?",
                        [$row['id'], $_SESSION['ums_user_id']]);
                }
            }

            ////////////////////////////////////////////////////////////////
            //Aktion
            ////////////////////////////////////////////////////////////////
            $gekauft = ($bid == $row['id'] && $bid_has_all);
            if ($gekauft) {
                $aktion = '<div class="mod-feld mod-feld-ok">&#10003; Ersteigert</div>';
            } elseif ($is_artefact && $free_artefact_places < 1) {
                $aktion = '<div class="mod-feld mod-feld-grund">Kein freier Artefaktplatz</div>';
            } elseif ($preis_fehlt) {
                $aktion = '<div class="mod-feld mod-feld-grund">Zu wenig '.$preis_name.'</div>';
            } else {
                //bestätigt wird mit einem zweiten Klick auf den Knopf (data-bestaetigen, siehe js/de_fn.js)
                $aktion = '<a href="?bid='.$row['id'].'" class="mod-btn" data-bestaetigen="Best&auml;tigen">Bieten</a>';
            }

            //nach dem Bieten Spielerdaten neu laden
            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    //Spielerdaten nach Update neu auslesen
                    $ps = loadPlayerStorage($_SESSION['ums_user_id']);
                    $GLOBALS['ps'] = $ps;
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

            ////////////////////////////////////////////////////////////////
            //Auktion anzeigen
            ////////////////////////////////////////////////////////////////
            //Meldung beim Bieten
            $meldung = '';
            if ($bid == $row['id']) {
                if ($bid_has_all) {
                    $meldung = '<div class="mod-meldung mod-meldung-ok">Die Auktion wurde best&auml;tigt.</div>';
                } else {
                    $meldung = '<div class="mod-meldung mod-meldung-fehler">Du kannst Dir diese Auktion nicht leisten.</div>';
                }
            }

            //links die Ware, rechts Preis, Preisverfall und Knopf; data-ware und data-waehrung für den Filter (Script unten)
            $filter_waren[$artikel_name] = true;
            $filter_waehrungen[$preis_name] = true;
            $karten .= '
			<div class="auk-karte'.($is_artefact ? ' auk-artefakt' : '').($gekauft ? ' auk-gekauft' : '').'" data-ware="'.html_text($artikel_name).'" data-waehrung="'.html_text($preis_name).'">
				<div class="auk-ware">'.$artikel.'
					<div class="auk-ware-text">
						<div class="mod-typ">'.$artikel_typ.'</div>
						<div class="auk-name">'.$artikel_name.'</div>
						'.($artikel_info != '' ? '<div class="auk-info">'.$artikel_info.'</div>' : '').'
						<div class="auk-chips">'.$nachlass_str.'<span class="mod-chip">+'.number_format($tradescore, 0, ",", ".").' Handelspunkte</span></div>
					</div>
				</div>
				<div class="auk-kasse">'.$preis.$verfall_str.$aktion.'</div>
				'.$meldung.'
			</div>';
            $anz_auktionen++;
            if (!$gekauft) {
                $anz_offen++;
            }
        }

        $content .= rahmen_oben('Auktionen', false);
        $content .= '<div class="auk mod">';
        $content .= '<div class="auk-kopf">
			<div class="auk-kopf-zahl"><b>'.$anz_offen.'</b> '.($anz_offen == 1 ? 'offene Auktion' : 'offene Auktionen').'</div>
			<div class="auk-plaetze'.($free_artefact_places < 1 ? ' auk-plaetze-voll' : '').'">Freie Artefaktpl&auml;tze <b>'.max(0, $free_artefact_places).'</b></div>
		</div>';
        $content .= '<div class="mod-hinweis">Nach dem Start der Auktion sinkt 1.000 Wirtschaftsticks lang der Preis. Auktionen, die man selbst gestartet hat, haben einen Nachlass von '.$nachlass.'%.</div>';
        if ($anz_auktionen > 1) {
            //Filter nach Ware und Währung; die Auswahl merkt sich der Browser (localStorage)
            ksort($filter_waren);
            ksort($filter_waehrungen);
            $content .= '<div class="auk-filter"><span class="mod-typ">Filter</span><select id="auk-ware" class="mod-eingabe"><option value="">Alle Waren</option>';
            foreach (array_keys($filter_waren) as $name) {
                $content .= '<option value="'.html_text($name).'">'.html_text($name).'</option>';
            }
            $content .= '</select><select id="auk-waehrung" class="mod-eingabe"><option value="">Alle W&auml;hrungen</option>';
            foreach (array_keys($filter_waehrungen) as $name) {
                $content .= '<option value="'.html_text($name).'">'.html_text($name).'</option>';
            }
            $content .= '</select></div>';
        }
        if ($anz_auktionen > 0) {
            $content .= '<div class="auk-liste">'.$karten.'</div>';
            $content .= '<div class="mod-leer" id="auk-filter-leer" hidden>Keine Auktion passt zum Filter.</div>';
            $content .= '<script>
//Auktionsfilter nach Ware und Waehrung (data-ware, data-waehrung an den Karten), Auswahl bleibt im Browser erhalten
(function(){
	var felder = {ware: document.getElementById("auk-ware"), waehrung: document.getElementById("auk-waehrung")};
	if(!felder.ware || !felder.waehrung){
		return;
	}
	function anwenden(){
		var sichtbar = 0;
		document.querySelectorAll(".auk-karte").forEach(function(karte){
			var aus = (felder.ware.value !== "" && karte.getAttribute("data-ware") !== felder.ware.value)
				|| (felder.waehrung.value !== "" && karte.getAttribute("data-waehrung") !== felder.waehrung.value);
			karte.classList.toggle("auk-aus", aus);
			if(!aus){
				sichtbar++;
			}
		});
		document.getElementById("auk-filter-leer").hidden = sichtbar > 0;
	}
	["ware", "waehrung"].forEach(function(name){
		try{
			felder[name].value = localStorage.getItem("de_auk_" + name) || "";
		}catch(err){}
		felder[name].addEventListener("change", function(){
			try{
				localStorage.setItem("de_auk_" + name, felder[name].value);
			}catch(err){}
			anwenden();
		});
	});
	anwenden();
})();
</script>';
        } else {
            $content .= '<div class="mod-leer">Zurzeit gibt es keine offenen Auktionen.<br>Jeder abgeholte <a href="ally_dailygift.php">Allianzbonus</a> startet eine neue Auktion.</div>';
        }
        $content .= '</div>';
        $content .= rahmen_unten(false);

        //transaktionsende
        $erg = Sperre::loesen($GLOBALS['dbi'], $_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            $content = ($content ?? '').'<div class="mod-meldung mod-meldung-fehler">ERROR 17</div>';
        }
    }// if setlock-ende
    else {
        $content = ($content ?? '').'<div class="mod-meldung mod-meldung-fehler">ERROR 18</div>';
    }


}



include "resline.php";

echo $content;


?>
<br>

</body>
</html>
