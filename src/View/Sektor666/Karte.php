<?php
namespace DieEwigen\DE2\View\Sektor666;

use DieEwigen\DE2\Model\Sektor666\Sektor666Service;

/**
 * Karte „Sektor 666“ in der Sektoransicht (sector.php?sf=666): Zustand der Schläfer, Rangliste der Sektoren,
 * Anleitung und Beute. Schlafen sie, stehen dort der Kulissentext, das nächste Erwachen in WT und der Verlauf.
 *
 * Verwendung:
 *   echo Karte::render($s666, $s666_lang, $uid, $ownsector);
 */
class Karte
{
    /**
     * @param Sektor666Service $s666      Zustand der Schläfer
     * @param array            $lang      Texte aus inc/lang/*_sektor666.lang.php
     * @param int              $uid       angemeldeter Spieler
     * @param int              $ownSector Sektor des Spielers
     */
    public static function render(Sektor666Service $s666, array $lang, int $uid, int $ownSector): string
    {
        $html = rahmen_oben($lang['titel'], false);
        $html .= '<div class="mod s666">';

        if ($s666->isWach()) {
            $html .= self::wach($s666, $lang, $uid, $ownSector);
        } else {
            $html .= self::schlafend($s666, $lang);
        }

        $verlauf = $s666->getVerlauf();
        if (!empty($verlauf)) {
            $html .= '<details class="s666-verlauf"><summary>'.$lang['verlauf_titel'].'</summary><ul>';
            foreach ($verlauf as $zeile) {
                $html .= '<li>'.$zeile.'</li>';
            }
            $html .= '</ul></details>';
        }

        $html .= '</div>';
        $html .= rahmen_unten(false);
        return $html;
    }

    private static function wach(Sektor666Service $s666, array $lang, int $uid, int $ownSector): string
    {
        $state = $s666->getState();
        $stufe = (int)$state['stufe'];
        $prozent = $s666->getHuelleProzent();
        $eigener = $s666->getEigenerRang($ownSector);

        $html = '<p class="s666-text">'.$lang['geschichte_wach'].'</p>';

        $html .= '<div class="ov-werte">';
        $html .= '<div class="ov-wert"><span class="mod-typ">'.$lang['kachel_stufe'].'</span><b>'.$stufe.'</b></div>';
        $html .= '<div class="ov-wert"><span class="mod-typ">'.$lang['kachel_huelle'].'</span><b>'.$prozent.' %</b><small>'.strtr($lang['balken'], ['{HP}' => Sektor666Service::zahl((int)$state['hp']), '{HPMAX}' => Sektor666Service::zahl((int)$state['hp_max'])]).'</small></div>';
        $html .= '<div class="ov-wert"><span class="mod-typ">'.$lang['kachel_eigener'].'</span><b>'
            .($eigener !== null ? strtr($lang['kachel_eigener_platz'], ['{PLATZ}' => $eigener['platz'], '{ANTEIL}' => Sektor666Service::anteil($eigener['anteil'])]) : $lang['kachel_eigener_keiner'])
            .'</b></div>';
        $html .= '</div>';
        $html .= '<div class="mod-balken s666-balken"><span style="width: '.$prozent.'%"></span></div>';

        //bekommt der Spieler Beute?
        $minanteil = Sektor666Service::getMindestanteil();
        if ($s666->istDabei($uid, $ownSector)) {
            $html .= '<div class="mod-hinweis">'.strtr($lang['eigene_dabei'], ['{SEK}' => $ownSector, '{MINANTEIL}' => $minanteil]).'</div>';
        } else {
            $html .= '<div class="mod-hinweis">'.$lang['eigene_nicht_dabei'].'</div>';
        }

        //Rangliste
        $html .= '<div class="ally-abschnitt"><div class="mod-typ">'.$lang['rang_titel'].'</div>';
        $rangliste = $s666->getRangliste($stufe);
        if (empty($rangliste)) {
            $html .= '<div class="mod-leer">'.$lang['rang_leer'].'</div>';
        } else {
            $html .= '<div class="einh-rang prod-kopf"><span class="prod-zahl">'.$lang['rang_platz'].'</span><span>'.$lang['rang_sektor'].'</span><span class="prod-zahl">'.$lang['rang_schaden'].'</span><span class="prod-zahl">'.$lang['rang_anteil'].'</span></div>';
            $html .= '<div class="prod-liste">';
            foreach ($rangliste as $zeile) {
                $eigen = $zeile['sec_id'] === $ownSector ? ' s666-eigen' : '';
                $html .= '<div class="einh-rang einh-platz'.min($zeile['platz'], 4).$eigen.'"><span class="prod-zahl">'.$zeile['platz'].'</span>'
                    .'<span class="einh-rang-name"><a href="sector.php?sf='.$zeile['sec_id'].'">'.$zeile['sec_id'].'</a></span>'
                    .'<span class="prod-zahl">'.Sektor666Service::zahl($zeile['schaden']).'</span>'
                    .'<span class="prod-zahl"><b>'.Sektor666Service::anteil($zeile['anteil']).' %</b></span></div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        //Anleitung
        $html .= '<div class="ally-abschnitt"><div class="mod-typ">'.$lang['anleitung_titel'].'</div>';
        $html .= '<p class="s666-text">'.strtr($lang['anleitung'], ['{RZ}' => $s666->getReisezeit(), '{ABWEHR}' => Sektor666Service::getAbwehrProzent()]).'</p></div>';

        //Beute dieser Stufe
        $html .= '<div class="ally-abschnitt"><div class="mod-typ">'.$lang['beute_titel'].'</div>';
        $html .= '<p class="s666-text">'.strtr($lang['beute_regel'], ['{MINANTEIL}' => $minanteil, '{AUSGLEICH}' => Sektor666Service::getAusgleichProzent()]).'</p>';
        $html .= '<ul class="s666-liste">';
        $grund = Sektor666Service::beuteFuer(0, $stufe);
        $html .= '<li><b>'.strtr($lang['beute_grund'], ['{MINANTEIL}' => $minanteil]).':</b> '.self::posten($grund, $lang).'</li>';
        for ($platz = 1; $platz <= 3; $platz++) {
            $mehr = self::differenz(Sektor666Service::beuteFuer($platz, $stufe), $grund);
            if (!empty(array_filter($mehr))) {
                $html .= '<li><b>'.strtr($lang['beute_platz'], ['{PLATZ}' => $platz]).':</b> '.self::posten($mehr, $lang).'</li>';
            }
        }
        $html .= '</ul>';
        $html .= '<p class="s666-text s666-leise">'.$lang['beute_artefakt_hinweis'].'</p></div>';

        return $html;
    }

    private static function schlafend(Sektor666Service $s666, array $lang): string
    {
        $html = '<p class="s666-text">'.$lang['kulisse'].'</p>';
        $wt = $s666->getWtBisErwachen();
        if ($wt === null) {
            $html .= '<div class="mod-hinweis">'.$lang['schlaf_runde_vorbei'].'</div>';
        } elseif ($s666->getStufe() > 0) {
            $html .= '<div class="mod-hinweis">'.strtr($lang['schlaf_erwacht_in'], ['{WT}' => Sektor666Service::zahl($wt)]).'</div>';
        } else {
            $html .= '<div class="mod-hinweis">'.strtr($lang['schlaf_erstes_mal'], ['{WT}' => Sektor666Service::zahl($wt)]).'</div>';
        }
        return $html;
    }

    private static function differenz(array $mehr, array $grund): array
    {
        $diff = [];
        foreach ($mehr as $posten => $menge) {
            $diff[$posten] = $menge - ($grund[$posten] ?? 0);
        }
        return $diff;
    }

    private static function posten(array $beute, array $lang): string
    {
        $teile = [];
        if ($beute['tronic'] > 0) {
            $teile[] = strtr($lang['posten_tronic'], ['{N}' => Sektor666Service::zahl($beute['tronic'])]);
        }
        if ($beute['kerne'] > 0) {
            $teile[] = strtr($lang['posten_kerne'], ['{N}' => Sektor666Service::zahl($beute['kerne'])]);
        }
        if ($beute['palenium'] > 0) {
            $teile[] = strtr($lang['posten_palenium'], ['{N}' => Sektor666Service::zahl($beute['palenium'])]);
        }
        if ($beute['kriegsartefakte'] > 0) {
            $teile[] = strtr($lang['posten_kriegsartefakte'], ['{N}' => $beute['kriegsartefakte']]);
        }
        if ($beute['artefakte'] > 0) {
            $teile[] = strtr($lang['posten_artefakte'], ['{N}' => $beute['artefakte']]);
        }
        return implode(', ', $teile);
    }
}
