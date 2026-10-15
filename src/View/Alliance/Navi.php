<?php
namespace DieEwigen\DE2\View\Alliance;

use DieEwigen\DE2\Model\Alliance\Mitgliedschaft;
use DieEwigen\DE2\Model\Feldzug\FeldzugService;

//Reiterleiste der Allianzseiten je Rolle, früher die print_*-Funktionen in ally/ally.menu.inc.php: Unterseiten markieren
//ihren Reiter, Austreten und Löschen sind rot abgesetzt, der Feldzug erscheint nur, wenn er auf dem Server läuft.
//Ohne Allianz gibt es Gründen und Beitreten, dazu die offene Bewerbung mit dem Knopf zum Zurückziehen (allymain.php).
class Navi
{
    //$meldung: fertige mod-meldung unter den Reitern, z. B. nach dem Zurückziehen einer Bewerbung
    public static function fuer(Mitgliedschaft $m, string $meldung = ''): string
    {
        $lang = self::lang();
        if ($m->istLeader()) {
            $html = self::leiste(array(
                'allymain.php' => $lang['allgemein'],
                'ally_coleader.php' => $lang['coleader'],
                'ally_members.php' => $lang['mitglieder'],
                'ally_antrag.php' => $lang['antraege'],
                'ally_partner.php' => $lang['buendnis'],
                'ally_war.php' => $lang['krieg'],
                'ally_finance.php' => $lang['finanzen'],
                'ally_history.php' => $lang['allianzhistory'],
                'ally_fleet.php' => $lang['allianzflotten'],
                'ally_bldg.php' => 'Projekte',
                'feldzug.php' => 'Feldzug',
                'ally_delete.php' => $lang['loeschen'],
            ));
        } elseif ($m->istCoLeader()) {
            $html = self::leiste(array(
                'allymain.php' => $lang['allgemein'],
                'ally_members.php' => $lang['mitglieder'],
                'ally_antrag.php' => $lang['antraege'],
                'ally_partner.php' => $lang['buendnis'],
                'ally_war.php' => $lang['krieg'],
                'ally_finance.php' => $lang['finanzen'],
                'ally_history.php' => $lang['allianzhistory'],
                'ally_bldg.php' => 'Projekte',
                'feldzug.php' => 'Feldzug',
                'ally_fleet.php' => $lang['allianzflotten'],
                'ally_austritt.php' => $lang['austreten'],
            ));
        } elseif ($m->istMitglied()) {
            $html = self::leiste(array(
                'allymain.php' => $lang['allgemein'],
                'ally_members.php' => $lang['mitglieder'],
                'ally_partner.php' => $lang['buendnis'],
                'ally_war.php' => $lang['krieg'],
                'ally_finance.php' => $lang['finanzen'],
                'ally_history.php' => $lang['allianzhistory'],
                'ally_fleet.php' => $lang['allianzflotten'],
                'ally_bldg.php' => 'Projekte',
                'feldzug.php' => 'Feldzug',
                'ally_austritt.php' => $lang['austreten'],
            ));
        } else {
            $html = self::leiste(array(
                'ally_register.php' => $lang['gruenden'],
                'toplist.php?&s=3' => $lang['beitreten'],
            ), ' ally-navi-frei');
        }
        if ($meldung !== '') {
            $html .= '<div class="mod ally-meldung">'.$meldung.'</div>';
        }
        $bewerbung = $m->bewerbung();
        if ($bewerbung !== null) {
            $html .= '<div class="mod ally-meldung"><div class="ally-bewerbung"><div class="ally-bewerbung-text">'
                .'<span class="mod-typ">Offene Bewerbung</span>'
                .'<span>Du hast Dich bei der Allianz <b>'.htmlspecialchars($bewerbung['allytag'], ENT_QUOTES, 'UTF-8').'</b> beworben und wartest auf die Entscheidung.</span>'
                .'</div><a href="allymain.php?stornobewerbung=1" class="mod-btn mod-btn-leise">Bewerbung zur&uuml;ckziehen</a></div></div>';
        }

        return $html;
    }

    //Meldung auf Seiten der Führung für alle anderen; die Seite endet damit
    public static function zugriffVerweigert(): string
    {
        $lang = self::lang();

        return '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$lang['accessdenied'].'</div></div>';
    }

    private static function lang(): array
    {
        include 'inc/lang/'.$GLOBALS['sv_server_lang'].'_ally.menu.lang.php';

        return $allymenu_lang;
    }

    private static function leiste(array $eintraege, string $klasse = ''): string
    {
        $zuordnung = array(
            'ally_annehmen.php' => 'ally_antrag.php',
            'ally_ablehnen.php' => 'ally_antrag.php',
            'ally_kick.php' => 'ally_members.php',
            'ally_leader.php' => 'ally_members.php',
            'ally_register2.php' => 'ally_register.php',
        );
        $aktiv = basename($_SERVER['SCRIPT_NAME']);
        if (isset($zuordnung[$aktiv])) {
            $aktiv = $zuordnung[$aktiv];
        }
        $html = '<div class="mod ally-navi'.$klasse.'">';
        foreach ($eintraege as $datei => $text) {
            if ($datei == 'feldzug.php' && !FeldzugService::istAktiv()) {
                continue;
            }
            $css = 'ally-reiter';
            if ($datei == 'ally_delete.php' || $datei == 'ally_austritt.php') {
                $css .= ' ally-reiter-gefahr';
            }
            if ($datei == $aktiv) {
                $css .= ' ally-reiter-aktiv';
            }
            $html .= '<a href="'.$datei.'" class="'.$css.'">'.$text.'</a>';
        }

        return $html.'</div>';
    }
}
