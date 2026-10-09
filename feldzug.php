<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_feldzug.lang.php';

use DieEwigen\DE2\Model\Feldzug\FeldzugService;
use DieEwigen\DE2\Model\Feldzug\Brennpunkte;
use DieEwigen\DE2\Session\CsrfToken;

$uid = (int)$_SESSION['ums_user_id'];
$fzs = new FeldzugService($GLOBALS['dbi']);

//Aktionen per POST mit Token, danach Umleitung (Post/Redirect/Get); Meldungen nur über feste Schlüssel
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $fz_ok = false;
    $fz_key = 'fehler_token';
    if (!CsrfToken::check($_POST['token'] ?? '')) {
        $fz_key = 'fehler_token';
    } elseif (!FeldzugService::istAktiv()) {
        $fz_key = 'fehler_aus';
    } elseif (isset($_POST['anmelden'])) {
        [$fz_ok, $fz_key] = $fzs->anmelden($uid);
    } elseif (isset($_POST['abmelden'])) {
        [$fz_ok, $fz_key] = $fzs->anmelden($uid, true);
    } elseif (isset($_POST['verteilen'])) {
        $fz_legionen = array();
        foreach ((array)($_POST['leg'] ?? array()) as $fz_bp => $fz_n) {
            $fz_legionen[$fz_bp] = trim((string)$fz_n) === '' ? '0' : (string)$fz_n;
        }
        [$fz_ok, $fz_key] = $fzs->verteilen($uid, $fz_legionen);
    } elseif (isset($_POST['spenden'])) {
        if (setLock($uid)) {
            [$fz_ok, $fz_key] = $fzs->spenden($uid, (array)($_POST['menge'] ?? array()));
            releaseLock($uid);
        } else {
            $fz_key = 'fehler_sperre';
        }
    }
    header('Location: feldzug.php?'.($fz_ok ? 'ok' : 'fehler').'='.$fz_key, true, 303);
    exit;
}

$fz_meldung = '';
foreach (array('ok', 'fehler') as $fz_art) {
    $fz_wert = $_GET[$fz_art] ?? '';
    if (is_string($fz_wert) && preg_match('/^(ok|fehler)_[a-z_]+$/', $fz_wert) && isset($feldzug_lang[$fz_wert])) {
        $fz_meldung = '<div class="mod-meldung mod-meldung-'.$fz_art.'">'.$feldzug_lang[$fz_wert].'</div>';
    }
}

//Daten für Rohstoffleiste und Allianzmenü
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, allytag, status FROM de_user_data WHERE user_id=?", [$uid]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01']; $restyp02 = $row['restyp02']; $restyp03 = $row['restyp03']; $restyp04 = $row['restyp04']; $restyp05 = $row['restyp05'];
$punkte = $row['score']; $newtrans = $row['newtrans']; $newnews = $row['newnews']; $sector = $row['sector']; $system = $row['system'];

$fz_eigene = $fzs->getEigeneAllianz($uid);

function fz_zahl($wert)
{
    return number_format((int)$wert, 0, ',', '.');
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Feldzug</title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi'] == 1) ? 'mobile' : 'desktop').'">';
include "resline.php";
if ($fz_eigene !== null) {
    include 'ally/ally.menu.inc.php';
}

rahmen_oben($feldzug_lang['titel']);
echo '<div class="mod fz">';
echo $fz_meldung;

$fz = FeldzugService::istAktiv() ? $fzs->getAktuell() : null;

if (!FeldzugService::istAktiv()) {
    echo '<div class="mod-leer">'.$feldzug_lang['aus'].'</div>';
} elseif ($fz === null) {
    echo '<p class="fz-text">'.$feldzug_lang['einleitung'].'</p>';
    echo '<div class="mod-hinweis fz-abstand">'.strtr($feldzug_lang['vor_erstem_aufruf'], array('{WT}' => fz_zahl($fzs->getWtBisErsterAufruf()))).'</div>';
} else {
    $fz_id = (int)$fz['id'];
    $fz_phase = (int)$fz['phase'];
    $fz_dabei = $fz_eigene !== null && $fzs->istDabei($fz_id, $fz_eigene['ally_id']);
    $fz_bis = $fzs->getWtBisSchritt($fz);

    //Kopf
    echo '<div class="ov-werte fz-kopf">';
    echo '<div class="ov-wert"><span class="mod-typ">'.$feldzug_lang['kachel_feldzug'].'</span><b>'.(int)$fz['nr'].'</b><small>'.$feldzug_lang['phase_'.$fz_phase].'</small></div>';
    if ($fz_phase === FeldzugService::PHASE_KAMPF) {
        echo '<div class="ov-wert"><span class="mod-typ">'.$feldzug_lang['kachel_zug'].'</span><b>'.((int)$fz['zug'] + 1).' / '.FeldzugService::getKampfZuege().'</b></div>';
        echo '<div class="ov-wert"><span class="mod-typ">'.$feldzug_lang['kachel_schritt'].'</span><b>'.strtr($feldzug_lang['schritt_kampf'], array('{WT}' => fz_zahl($fz_bis))).'</b></div>';
    } elseif ($fz_phase === FeldzugService::PHASE_AUFRUF) {
        echo '<div class="ov-wert"><span class="mod-typ">'.$feldzug_lang['preis_titel'].'</span><b>'.fz_zahl($fz['preis']).'</b><small>je VS-Rohstoff</small></div>';
        echo '<div class="ov-wert"><span class="mod-typ">'.$feldzug_lang['kachel_schritt'].'</span><b>'.strtr($feldzug_lang['schritt_aufruf'], array('{WT}' => fz_zahl($fz_bis))).'</b></div>';
    }
    echo '</div>';

    ////////////////////////////////////////////////////////////
    // Aufruf: Preis, Anmeldungen, eigene Allianz
    ////////////////////////////////////////////////////////////
    if ($fz_phase === FeldzugService::PHASE_AUFRUF) {
        echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['preis_titel'].'</div><p class="fz-text">'.strtr($feldzug_lang['preis_text'], array('{PREIS}' => fz_zahl($fz['preis']))).'</p></div>';

        $fz_angemeldet = $fzs->getTeilnehmer($fz_id);
        echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['angemeldet_titel'].'</div>';
        if (empty($fz_angemeldet)) {
            echo '<div class="fz-leise">'.$feldzug_lang['angemeldet_keine'].'</div>';
        } else {
            echo '<div class="fz-chips">';
            foreach ($fz_angemeldet as $fz_t) {
                echo '<span class="mod-chip">'.html_text($fz_t['allytag']).'</span>';
            }
            echo '</div>';
        }
        echo '</div>';

        if ($fz_eigene !== null) {
            $fz_eigen_angemeldet = null;
            foreach ($fz_angemeldet as $fz_t) {
                if ((int)$fz_t['ally_id'] === $fz_eigene['ally_id']) {
                    $fz_eigen_angemeldet = $fz_t;
                }
            }
            echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['eigene_titel'].' '.html_text($fz_eigene['allytag']).'</div>';
            echo '<ul class="fz-liste-punkte">';
            echo '<li>'.($fz_eigen_angemeldet !== null ? strtr($feldzug_lang['eigene_angemeldet'], array('{NAME}' => html_text($fz_eigen_angemeldet['angemeldet_von']))) : $feldzug_lang['eigene_nicht_angemeldet']).'</li>';
            echo '<li>'.strtr($feldzug_lang['eigene_berechtigt'], array('{N}' => $fzs->zaehleBerechtigte($fz_id, $fz_eigene['ally_id']))).'</li>';
            $fz_fehlt = array();
            $fz_kasse = $fzs->getKasse($fz_eigene['ally_id']);
            $fz_namen = loadPlayerStorage($uid);
            foreach (FeldzugService::ITEMS as $fz_item) {
                if ($fz_kasse[$fz_item] < (int)$fz['preis']) {
                    $fz_fehlt[] = fz_zahl((int)$fz['preis'] - $fz_kasse[$fz_item]).' '.$fz_namen[$fz_item]['item_name'];
                }
            }
            echo '<li>'.(empty($fz_fehlt) ? $feldzug_lang['eigene_kasse_ok'] : strtr($feldzug_lang['eigene_kasse_fehlt'], array('{LISTE}' => implode(', ', $fz_fehlt)))).'</li>';
            if (!$fzs->istBerechtigt($fz_id, $uid)) {
                echo '<li>'.$feldzug_lang['eigene_spaet'].'</li>';
            }
            echo '</ul>';
            if ($fz_eigene['posten']) {
                echo '<form method="post" action="feldzug.php" class="fz-knopfzeile"><input type="hidden" name="token" value="'.CsrfToken::get().'">';
                if ($fz_eigen_angemeldet !== null) {
                    echo '<button type="submit" name="abmelden" value="1" class="mod-btn mod-btn-leise">'.$feldzug_lang['knopf_abmelden'].'</button>';
                } else {
                    echo '<button type="submit" name="anmelden" value="1" class="mod-btn">'.$feldzug_lang['knopf_anmelden'].'</button>';
                }
                echo '</form>';
            } else {
                echo '<div class="fz-leise">'.$feldzug_lang['eigene_posten'].'</div>';
            }
            echo '</div>';
        }
        echo '<div class="mod-hinweis fz-abstand">'.$feldzug_lang['bedingungen'].'</div>';
    }

    ////////////////////////////////////////////////////////////
    // Kampf: Brennpunkte und Verteilung, Ergebnis, Punktestand
    ////////////////////////////////////////////////////////////
    if ($fz_phase === FeldzugService::PHASE_KAMPF) {
        $fz_bps = $fzs->getBrennpunkte($fz_id);
        $fz_verteilung = $fz_dabei ? $fzs->getVerteilung($fz_id, $fz_eigene['ally_id']) : array();
        $fz_darf = $fz_dabei && $fz_eigene['posten'];
        $fz_gesamt = FeldzugService::getLegionen();

        echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['brennpunkte_titel'].'</div>';
        if ($fz_darf) {
            echo '<form method="post" action="feldzug.php" id="fz-verteilung"><input type="hidden" name="token" value="'.CsrfToken::get().'">';
        }
        echo '<div class="fz-brennpunkte">';
        foreach ($fz_bps as $fz_bp) {
            $fz_bp_id = (int)$fz_bp['id'];
            $fz_rolle = (int)$fz_bp['rolle'];
            echo '<div class="fz-bp'.((int)$fz_bp['halter_ally_id'] > 0 && $fz_eigene !== null && (int)$fz_bp['halter_ally_id'] === $fz_eigene['ally_id'] ? ' fz-bp-eigen' : '').'">';
            echo '<div class="fz-bp-info"><span class="fz-bp-name">'.html_text($fz_bp['name']).'</span>';
            echo '<span class="fz-bp-meta">';
            if ((int)$fz_bp['wert'] === Brennpunkte::WERT_KERN) {
                echo '<span class="mod-chip mod-chip-warn">'.$feldzug_lang['kern'].'</span>';
            }
            echo '<span class="mod-chip">'.strtr($feldzug_lang['kp'], array('{N}' => (int)$fz_bp['wert'])).'</span>';
            if ($fz_rolle > 0) {
                $fz_rolleninfo = array(Brennpunkte::ROLLE_FESTUNG => 'rolle_festung_info', Brennpunkte::ROLLE_MINE => 'rolle_mine_info', Brennpunkte::ROLLE_WERFT => 'rolle_werft_info');
                echo '<span class="mod-chip fz-rolle" title="'.$feldzug_lang[$fz_rolleninfo[$fz_rolle]].'">'.Brennpunkte::rollenName($fz_rolle, $feldzug_lang).'</span>';
            }
            echo '</span></div>';
            echo '<div class="fz-bp-halter">'.((int)$fz_bp['halter_ally_id'] > 0 ? '<b>'.html_text($fz_bp['halter_tag']).'</b>' : '<span class="fz-leise">'.$feldzug_lang['neutral'].'</span>').'</div>';
            if ($fz_darf) {
                $fz_n = $fz_verteilung[$fz_bp_id] ?? 0;
                echo '<div class="fz-steuer"><button type="button" class="fz-pm" data-schritt="-1" aria-label="weniger">&minus;</button>';
                echo '<input type="number" name="leg['.$fz_bp_id.']" value="'.$fz_n.'" min="0" max="'.$fz_gesamt.'" inputmode="numeric" class="mod-eingabe fz-zahl">';
                echo '<button type="button" class="fz-pm" data-schritt="1" aria-label="mehr">+</button></div>';
            } elseif ($fz_dabei) {
                echo '<div class="fz-steuer fz-nur-lesen"><b>'.($fz_verteilung[$fz_bp_id] ?? 0).'</b></div>';
            }
            echo '</div>';
        }
        echo '</div>';

        if ($fz_dabei) {
            $fz_summe = array_sum($fz_verteilung);
            echo '<div class="fz-fuss">';
            echo '<span class="fz-rest" id="fz-rest" data-gesamt="'.$fz_gesamt.'">'.strtr($feldzug_lang['rest'], array('{REST}' => $fz_gesamt - $fz_summe, '{GESAMT}' => $fz_gesamt)).'</span>';
            if ($fz_darf) {
                echo '<button type="submit" name="verteilen" value="1" class="mod-btn" id="fz-speichern">'.$feldzug_lang['knopf_verteilen'].'</button>';
            }
            echo '</div>';
            foreach ($fzs->getTeilnehmer($fz_id, FeldzugService::TEILNEHMER_DABEI) as $fz_t) {
                if ((int)$fz_t['ally_id'] === $fz_eigene['ally_id'] && $fz_t['letzte_aenderung_name'] !== '') {
                    echo '<div class="fz-leise">'.strtr($feldzug_lang['geaendert'], array('{NAME}' => html_text($fz_t['letzte_aenderung_name']), '{WT}' => fz_zahl($fz_t['letzte_aenderung_wt']))).'</div>';
                }
            }
            echo '<div class="fz-leise">'.($fz_darf ? $feldzug_lang['stehend'] : $feldzug_lang['nur_posten']).'</div>';
        } elseif ($fz_eigene !== null) {
            echo '<div class="fz-leise">'.$feldzug_lang['nicht_dabei'].'</div>';
        }
        if ($fz_darf) {
            echo '</form>';
        }
        echo '</div>';

        //Ergebnis des letzten Zuges, für alle sichtbar
        $fz_zug = (int)$fz['zug'];
        $fz_tags = array();
        foreach ($fzs->getTeilnehmer($fz_id) as $fz_t) {
            $fz_tags[(int)$fz_t['ally_id']] = $fz_t['allytag'];
        }
        echo '<div class="ally-abschnitt"><div class="mod-typ">'.($fz_zug > 0 ? strtr($feldzug_lang['ergebnis_titel'], array('{ZUG}' => $fz_zug)) : $feldzug_lang['ergebnis_titel_leer']).'</div>';
        if ($fz_zug < 1) {
            echo '<div class="fz-leise">'.$feldzug_lang['ergebnis_leer'].'</div>';
        } else {
            $fz_erg = $fzs->getErgebnis($fz_id, $fz_zug);
            $fz_vorher = $fzs->getHalterNachZug($fz_id, $fz_zug - 1);
            echo '<div class="fz-ergebnis">';
            foreach ($fz_bps as $fz_bp) {
                $fz_bp_id = (int)$fz_bp['id'];
                $fz_halter = (int)$fz_bp['halter_ally_id'];
                $fz_neu = $fz_halter !== ($fz_vorher[$fz_bp_id] ?? 0);
                $fz_teile = array();
                foreach ($fz_erg[$fz_bp_id] ?? array() as $fz_ally => $fz_e) {
                    $fz_teile[] = strtr($feldzug_lang['ergebnis_staerke'], array('{TAG}' => html_text($fz_tags[$fz_ally] ?? '?'), '{LEG}' => $fz_e['legionen'], '{ST}' => $fz_e['staerke']));
                }
                echo '<div class="fz-erg-zeile'.($fz_neu ? ' fz-erg-neu' : '').'">';
                echo '<span class="fz-erg-name">'.html_text($fz_bp['name']).'</span>';
                echo '<span class="fz-erg-halter">'.($fz_halter > 0 ? html_text($fz_bp['halter_tag']) : $feldzug_lang['neutral']).($fz_neu ? ' <span class="mod-chip mod-chip-gruen">'.$feldzug_lang['ergebnis_neu'].'</span>' : '').'</span>';
                echo '<span class="fz-erg-teile">'.(empty($fz_teile) ? $feldzug_lang['ergebnis_keine'] : implode(' &middot; ', $fz_teile)).'</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';

        //Punktestand
        $fz_gehalten = array();
        foreach ($fz_bps as $fz_bp) {
            if ((int)$fz_bp['halter_ally_id'] > 0) {
                $fz_gehalten[(int)$fz_bp['halter_ally_id']] = ($fz_gehalten[(int)$fz_bp['halter_ally_id']] ?? 0) + 1;
            }
        }
        echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['stand_titel'].'</div>';
        echo '<div class="einh-rang fz-stand prod-kopf"><span class="prod-zahl">'.$feldzug_lang['stand_platz'].'</span><span>'.$feldzug_lang['stand_allianz'].'</span><span class="prod-zahl">'.$feldzug_lang['stand_gehalten'].'</span><span class="prod-zahl">'.$feldzug_lang['stand_kp'].'</span></div>';
        echo '<div class="prod-liste">';
        $fz_platz = 0;
        $fz_letzte = null;
        $fz_i = 0;
        foreach ($fzs->getTeilnehmer($fz_id, FeldzugService::TEILNEHMER_DABEI) as $fz_t) {
            $fz_i++;
            if ($fz_letzte !== (int)$fz_t['kontrollpunkte']) {
                $fz_platz = $fz_i;
                $fz_letzte = (int)$fz_t['kontrollpunkte'];
            }
            $fz_eigen = $fz_eigene !== null && (int)$fz_t['ally_id'] === $fz_eigene['ally_id'] ? ' fz-eigen' : '';
            echo '<div class="einh-rang fz-stand einh-platz'.min($fz_platz, 4).$fz_eigen.'"><span class="prod-zahl">'.$fz_platz.'</span><span class="einh-rang-name">'.html_text($fz_t['allytag']).'</span>';
            echo '<span class="prod-zahl">'.($fz_gehalten[(int)$fz_t['ally_id']] ?? 0).'</span><span class="prod-zahl"><b>'.fz_zahl($fz_t['kontrollpunkte']).'</b></span></div>';
        }
        echo '</div></div>';
    }

    ////////////////////////////////////////////////////////////
    // Kriegskasse: Bestand und Spende, für Mitglieder
    ////////////////////////////////////////////////////////////
    if ($fz_eigene !== null) {
        $fz_kasse = $fzs->getKasse($fz_eigene['ally_id']);
        $fz_lager = loadPlayerStorage($uid);
        echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['kasse_titel'].' '.html_text($fz_eigene['allytag']).'</div>';
        echo '<p class="fz-text">'.$feldzug_lang['kasse_text'].'</p>';
        echo '<form method="post" action="feldzug.php"><input type="hidden" name="token" value="'.CsrfToken::get().'">';
        echo '<div class="fz-kasse fz-kasse-kopf"><span>'.$feldzug_lang['kasse_rohstoff'].'</span><span class="prod-zahl">'.$feldzug_lang['kasse_bestand'].'</span><span class="prod-zahl">'.$feldzug_lang['kasse_lager'].'</span><span class="prod-zahl">'.$feldzug_lang['kasse_spende'].'</span></div>';
        foreach (FeldzugService::ITEMS as $fz_item) {
            $fz_hat = (int)($fz_lager[$fz_item]['item_amount'] ?? 0);
            $fz_genug = $fz_phase === FeldzugService::PHASE_AUFRUF && $fz_kasse[$fz_item] >= (int)$fz['preis'];
            echo '<div class="fz-kasse"><span>'.html_text($fz_lager[$fz_item]['item_name'] ?? '#'.$fz_item).'</span>';
            echo '<span class="prod-zahl'.($fz_genug ? ' fz-gut' : '').'">'.fz_zahl($fz_kasse[$fz_item]).'</span>';
            echo '<span class="prod-zahl">'.fz_zahl($fz_hat).'</span>';
            echo '<span class="prod-zahl"><input type="number" name="menge['.$fz_item.']" value="" min="0" max="'.$fz_hat.'" inputmode="numeric" class="mod-eingabe fz-zahl" placeholder="0"></span></div>';
        }
        echo '<div class="fz-fuss"><span></span><button type="submit" name="spenden" value="1" class="mod-btn">'.$feldzug_lang['knopf_spenden'].'</button></div>';
        echo '</form></div>';
    }
}

////////////////////////////////////////////////////////////
// Historie der Runde und Regeln
////////////////////////////////////////////////////////////
if (FeldzugService::istAktiv()) {
    echo '<div class="ally-abschnitt"><div class="mod-typ">'.$feldzug_lang['historie_titel'].'</div>';
    $fz_historie = $fzs->getHistorie();
    if (empty($fz_historie)) {
        echo '<div class="fz-leise">'.$feldzug_lang['historie_leer'].'</div>';
    } else {
        echo '<ul class="fz-liste-punkte">';
        foreach ($fz_historie as $fz_h) {
            echo '<li>'.strtr($feldzug_lang['historie_zeile'], array('{NR}' => (int)$fz_h['feldzug_nr'], '{TAG}' => html_text($fz_h['allytag']), '{KP}' => fz_zahl($fz_h['kontrollpunkte']))).'</li>';
        }
        echo '</ul>';
    }
    echo '</div>';

    echo '<details class="einh-regeln fz-regeln"><summary>'.$feldzug_lang['regeln_titel'].'</summary>'.strtr($feldzug_lang['regeln'], array(
        '{AUFRUF}' => fz_zahl(FeldzugService::getAufrufZuege() * FeldzugService::getZugWt()),
        '{ZUEGE}' => FeldzugService::getKampfZuege(),
        '{ZUG}' => fz_zahl(FeldzugService::getZugWt()),
        '{EXTRA}' => FeldzugService::getExtraBrennpunkte(),
        '{LEGIONEN}' => FeldzugService::getLegionen(),
        '{QP}' => FeldzugService::questpunkte(1),
    )).'</details>';
}

echo '</div>';
rahmen_unten();
?>
<script>
//Plus/Minus und Restlegionen; ohne JavaScript bleiben die Zahlenfelder bedienbar
(function () {
    var form = document.getElementById('fz-verteilung');
    var rest = document.getElementById('fz-rest');
    if (!form || !rest) {
        return;
    }
    var gesamt = parseInt(rest.getAttribute('data-gesamt'), 10);
    var felder = form.querySelectorAll('.fz-zahl');
    var vorlage = rest.innerHTML;
    function summe() {
        var s = 0;
        for (var i = 0; i < felder.length; i++) {
            s += Math.max(0, parseInt(felder[i].value, 10) || 0);
        }
        return s;
    }
    function aktualisieren() {
        var r = gesamt - summe();
        rest.innerHTML = vorlage.replace(/<b>-?\d+<\/b>/, '<b>' + r + '</b>');
        rest.classList.toggle('fz-zu-viel', r < 0);
        document.getElementById('fz-speichern').disabled = r < 0;
    }
    var knoepfe = form.querySelectorAll('.fz-pm');
    for (var i = 0; i < knoepfe.length; i++) {
        knoepfe[i].addEventListener('click', function () {
            var feld = this.parentNode.querySelector('.fz-zahl');
            var neu = Math.max(0, (parseInt(feld.value, 10) || 0) + parseInt(this.getAttribute('data-schritt'), 10));
            if (neu > (parseInt(feld.value, 10) || 0) && summe() >= gesamt) {
                return;
            }
            feld.value = neu;
            aktualisieren();
        });
    }
    for (var j = 0; j < felder.length; j++) {
        felder[j].addEventListener('input', aktualisieren);
    }
})();
</script>
</body>
</html>
