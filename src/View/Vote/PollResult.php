<?php
namespace DieEwigen\DE2\View\Vote;

/**
 * Ergebnis einer beendeten Umfrage (vote.php, vote_overview.php): Zeitraum, Hinweis,
 * je Antwort Anteil und Stimmen als Balken, darunter die Beteiligung.
 *
 * Verwendung:
 *   echo PollResult::render($row, $vote_lang, 'vote_overview.php', 'Zurück zur Übersicht');
 */
class PollResult
{
    /**
     * @param array  $row      Zeile aus de_vote_umfragen (frage, antworten, hinweis, stimmen, startdatum, enddatum, ergebnisse)
     * @param array  $lang     Texte aus inc/lang/*_vote.lang.php
     * @param string $backUrl  optionaler Zurück-Knopf im Fuß
     * @param string $backText Beschriftung des Zurück-Knopfs
     */
    public static function render(array $row, array $lang, string $backUrl = '', string $backText = ''): string
    {
        $antworten = explode('|', $row['antworten']);
        $ergebnisse = explode('|', $row['ergebnisse']);
        //abgegebene Stimmen|Spieler beim Beenden (ourdetool/umfragen.php)
        $stimmen = explode('|', $row['stimmen']);

        $html = rahmen_oben($row['frage'], false);
        $html .= '<div class="mod vt">';
        $html .= '<div class="vt-daten"><span>'.$lang['start'].': <b>'.self::datum($row['startdatum']).'</b></span>';
        $html .= '<span>'.$lang['ende'].': <b>'.self::datum($row['enddatum']).'</b></span></div>';
        if (trim((string)$row['hinweis']) != '') {
            $html .= '<div class="mod-hinweis vt-hinweis">'.nl2br($row['hinweis']).'</div>';
        }

        $stimmengesamt = 0;
        $html .= '<div class="vt-ergebnisse">';
        for ($i = 0; $i < count($antworten); $i++) {
            $anzahl = (int)($ergebnisse[$i] ?? 0);
            $prozente = ($stimmen[0] > 0) ? number_format(($anzahl * 100) / $stimmen[0], 2, ',', '.') : '0,00';
            $breite = ($stimmen[0] > 0) ? min(100, round(($anzahl * 100) / $stimmen[0], 1)) : 0;
            $html .= '<div class="vt-ergebnis">';
            $html .= '<div class="vt-ergebnis-kopf"><span>'.$antworten[$i].'</span><span class="vt-zahl"><b>'.$prozente.' %</b> &middot; '.$anzahl.'</span></div>';
            $html .= '<div class="mod-balken"><span style="width: '.$breite.'%;"></span></div>';
            $html .= '</div>';

            $stimmengesamt += $anzahl;
        }
        $html .= '</div>';

        $html .= '<div class="vt-fuss">';
        if ($backUrl != '') {
            $html .= '<a href="'.$backUrl.'" class="mod-btn mod-btn-leise ally-btn-klein vt-zurueck">'.$backText.'</a>';
        }
        $html .= '<span>'.$lang['insgesamt'].': <b>'.$stimmengesamt.'</b>';
        if (isset($stimmen[1]) && $stimmen[1] > 0) {
            $html .= ' von '.number_format((int)$stimmen[1], 0, '', '.').' Spielern';
        }
        $html .= '</span></div>';

        $html .= '</div>';
        $html .= rahmen_unten(false);

        return $html;
    }

    /**
     * Datum mit Uhrzeit, z. B. "01.09.2026 12:00 Uhr"; ohne Datum (0000-00-00) ein Strich.
     */
    private static function datum(string $wert): string
    {
        $zeit = strtotime($wert);
        if ($zeit === false || $zeit <= 0) {
            return '&ndash;';
        }
        return date('d.m.Y H:i', $zeit).' Uhr';
    }
}
