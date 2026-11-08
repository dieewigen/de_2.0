<?php
namespace DieEwigen\DE2\View;

use DieEwigen\DE2\Model\Battleground\Werkstatt;

/**
 * Beutekarte eines Werkstatt-Stücks (specialship.php): Farbe und Rahmen nach Güte, Zeichen des Platzes, Hauptwert groß,
 * Nebenwerte als Zeilen, leere Nebenwertplätze als Hinweis auf höhere Güten, Vergleich mit dem angelegten Stück, Knöpfe.
 * Stile .ws-karte* in gp/de-main.scss.
 */
class WerkstattKarte
{
    /** Zeichen je Platz als SVG-Pfade (24x24, Linie in currentColor) */
    private const IKON = [
        1 => '<path d="M12 2.5 4.5 6v6c0 4.8 3.2 8.2 7.5 9.5 4.3-1.3 7.5-4.7 7.5-9.5V6L12 2.5z"/><path d="M12 7v9"/>',
        2 => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="3.5"/><path d="M12 3.5V7M12 17v3.5M3.5 12H7M17 12h3.5"/>',
        3 => '<path d="M4 20 20 4M4 13l7 7M11 6l7 7M4 20h4M4 20v-4"/>',
        4 => '<circle cx="12" cy="12" r="7.5"/><circle cx="12" cy="12" r="1.5"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/>',
    ];

    public static function ikon(int $platz): string
    {
        return '<svg class="ws-ikon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.(self::IKON[$platz] ?? '').'</svg>';
    }

    /**
     * @param array $it  Stück aus Werkstatt::item()
     * @param array $ctx lang (werkstatt_lang), token (verstecktes Feld), max_v (Veredelungen je Eigenschaft auf dem Rang),
     *                   angelegt (Platz => angelegtes Stück), neu (frisch geschmiedet), aktionen (Knöpfe zeigen)
     */
    public static function html(array $it, array $ctx): string
    {
        $lang = $ctx['lang'];
        $g = (int)$it['guete'];
        $platz = (int)$it['platz'];
        $haupt = Werkstatt::HAUPTWERT[$platz];
        $maxV = (int)($ctx['max_v'] ?? 0);
        $neu = !empty($ctx['neu']);

        //Vergleich des Hauptwerts mit dem angelegten Stück desselben Platzes
        $vergleich = '';
        $ang = $ctx['angelegt'][$platz] ?? null;
        if (!$it['angelegt'] && $ang && isset($ang['werte'][$haupt], $it['werte'][$haupt])) {
            $d = Werkstatt::wert($it['werte'][$haupt]) - Werkstatt::wert($ang['werte'][$haupt]);
            if (abs($d) >= 0.05) {
                $vergleich = '<span class="ws-delta '.($d > 0 ? 'ws-delta-plus' : 'ws-delta-minus').'" title="'.$lang['vergleich'].'">'
                    .($d > 0 ? '&#9650; +' : '&#9660; &minus;').self::zahl(abs($d)).(in_array($haupt, Werkstatt::PROZENT) ? ' %' : '').'</span>';
            }
        }

        $html = '<div class="ws-karte ws-karte-'.$g.($it['angelegt'] ? ' ws-angelegt' : '').($neu ? ' ws-neu' : '').'">';
        $html .= '<div class="ws-kopf">'.self::ikon($platz).'<div class="ws-titel"><b class="ws-name">'.Werkstatt::name($it, $lang).'</b><small>'.$lang['guete'][$g].' &middot; '.$lang['platz'][$platz].'</small></div>'
            .($it['angelegt'] ? '<span class="mod-chip mod-chip-gruen">'.$lang['angelegt'].'</span>' : '')
            .($neu ? '<span class="mod-chip ws-chip-neu">'.$lang['neu'].'</span>' : '').'</div>';
        $hw = $it['werte'][$haupt] ?? [0, 0];
        $html .= '<div class="ws-hauptwert" title="'.$lang['kennzahl'][$haupt].'&'.$lang['kennzahl_hilfe'][$haupt].'"><span>'.$lang['kennzahl'][$haupt].'</span><b>'.Werkstatt::wertText($haupt, $hw).'</b>'.self::veredelt((int)($hw[1] ?? 0), $lang).$vergleich.'</div>';

        //Nebenwerte, dazu leere Plätze bis zur Zahl beim Artefakt
        $html .= '<div class="ws-neben">';
        $n = 0;
        foreach ($it['werte'] as $k => $e) {
            if ($k === $haupt) {
                continue;
            }
            $n++;
            $html .= '<span class="ws-nebenwert" title="'.$lang['kennzahl'][$k].'&'.$lang['kennzahl_hilfe'][$k].'">'.$lang['kennzahl'][$k].' <b>'.Werkstatt::wertText($k, $e).'</b>'.self::veredelt((int)($e[1] ?? 0), $lang).'</span>';
        }
        for ($max = max(Werkstatt::NEBENWERTE); $n < $max; $n++) {
            $html .= '<span class="ws-nebenwert ws-nebenwert-frei" title="'.$lang['frei_hilfe'].'">'.$lang['frei'].'</span>';
        }
        $html .= '</div>';

        if (!empty($ctx['aktionen'])) {
            $t = $ctx['token'];
            $id = (int)$it['id'];
            $html .= '<div class="ws-aktionen"><form method="post" action="specialship.php">'.$t.'<input type="hidden" name="ws_id" value="'.$id.'"><button type="submit" name="ws_aktion" value="'.($it['angelegt'] ? 'ablegen' : 'anlegen').'" class="mod-btn ally-btn-klein'.($it['angelegt'] ? ' mod-btn-leise' : '').'">'.$lang[$it['angelegt'] ? 'ablegen' : 'anlegen'].'</button></form>';
            $optionen = '';
            foreach ($it['werte'] as $k => $e) {
                $v = (int)($e[1] ?? 0);
                if ($v < $maxV) {
                    $optionen .= '<option value="'.$k.'">'.$lang['kennzahl'][$k].' ('.$v.'/'.$maxV.', '.Werkstatt::veredelnKosten($g, $v).' Staub)</option>';
                }
            }
            if ($optionen !== '') {
                $html .= '<form method="post" action="specialship.php" class="ws-veredeln">'.$t.'<input type="hidden" name="ws_id" value="'.$id.'"><select name="ws_kennzahl" class="mod-eingabe">'.$optionen.'</select><button type="submit" name="ws_aktion" value="veredeln" class="mod-btn mod-btn-leise ally-btn-klein">'.$lang['veredeln'].'</button></form>';
            }
            //angelegte Stücke lassen sich nicht zerlegen, erst ablegen (wie Artefakte in Flotten)
            if (!$it['angelegt']) {
                $html .= '<form method="post" action="specialship.php">'.$t.'<input type="hidden" name="ws_id" value="'.$id.'"><button type="submit" name="ws_aktion" value="zerlegen" class="mod-btn mod-btn-leise ally-btn-klein" data-bestaetigen="Wirklich zerlegen?">'.$lang['zerlegen'].'</button></form>';
            }
            $html .= '</div>';
        }

        return $html.'</div>';
    }

    /** Leerer Platz in der Reihe der angelegten Stücke */
    public static function leer(int $platz, array $lang): string
    {
        return '<div class="ws-karte ws-karte-leer"><div class="ws-kopf">'.self::ikon($platz).'<div class="ws-titel"><b class="ws-name">'.$lang['platz'][$platz].'</b><small>'
            .$lang['leer'].' &middot; '.$lang['hauptwert'].': '.$lang['kennzahl'][Werkstatt::HAUPTWERT[$platz]].'</small></div></div></div>';
    }

    /** Zahl der Veredelungen einer Eigenschaft als kleine Marke */
    private static function veredelt(int $v, array $lang): string
    {
        return $v > 0 ? '<i class="ws-v" title="'.$v.' '.$lang['veredelungen'].'">+'.$v.' V</i>' : '';
    }

    private static function zahl(float $n): string
    {
        return number_format($n, $n == floor($n) ? 0 : 1, ',', '.');
    }
}
