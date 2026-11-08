<?php
namespace DieEwigen\DE2\View\Geheimdienst;

//Karten für Sonden- und Agentenberichte in secret.php, gleiche Optik wie der Kampfbericht (gp/de-main.scss, Block gb-).
//Die Karten sind selbst .mod, damit Knöpfe und Eingabefelder der mod-Bausteine darin greifen.
class Bericht
{
    //Karte: Art des Berichts klein, Ziel groß, dann der Inhalt und optional ein Fuß (z. B. die Links)
    public static function karte(string $art, string $ziel, string $inhalt, string $fuss = ''): string
    {
        return '<div class="mod gb"><div class="gb-kopf"><span class="mod-typ">'.trim($art).'</span><b class="gb-ziel">'.$ziel.'</b></div>'
            .$inhalt.($fuss !== '' ? '<div class="gb-fuss">'.$fuss.'</div>' : '').'</div>';
    }

    //Zwischenüberschrift, rechts optional der Zeitpunkt der Daten
    public static function abschnitt(string $titel, string $zeit = ''): string
    {
        return '<div class="gb-abschnitt"><span>'.$titel.'</span>'.($zeit !== '' ? '<small>'.$zeit.'</small>' : '').'</div>';
    }

    //Wertepaare: Bezeichnung links, Wert rechts; ein drittes Element steht als Differenz daneben (Sondenhistorie)
    public static function werte(array $paare): string
    {
        $html = '<div class="gb-werte">';
        foreach ($paare as $p) {
            $html .= '<div class="gb-wert"><span>'.$p[0].'</span><b>'.$p[1].'</b>'
                .(isset($p[2]) ? '<small class="gb-diff">'.$p[2].'</small>' : '').'</div>';
        }

        return $html.'</div>';
    }

    //Tabelle: erste Spalte Text, weitere Spalten Zahlen; Kopf und Fußzeile optional
    public static function tabelle(array $kopf, array $zeilen, ?array $fuss = null): string
    {
        $html = '<table class="gb-tabelle">';
        if ($kopf) {
            $html .= '<thead><tr>';
            foreach ($kopf as $k) {
                $html .= '<th>'.$k.'</th>';
            }
            $html .= '</tr></thead>';
        }
        $html .= '<tbody>';
        foreach ($zeilen as $z) {
            $html .= '<tr>';
            foreach ($z as $w) {
                $html .= '<td>'.$w.'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        if ($fuss) {
            $html .= '<tfoot><tr>';
            foreach ($fuss as $w) {
                $html .= '<td>'.$w.'</td>';
            }
            $html .= '</tr></tfoot>';
        }

        return $html.'</table>';
    }

    //einfache Liste, bei vielen Einträgen zweispaltig
    public static function liste(array $eintraege, bool $spalten = false): string
    {
        if (!$eintraege) {
            return '<div class="gb-leer">Keine Angaben.</div>';
        }
        $html = '<ul class="gb-liste'.($spalten ? ' gb-liste-spalten' : '').'">';
        foreach ($eintraege as $e) {
            $html .= '<li>'.$e.'</li>';
        }

        return $html.'</ul>';
    }

    //Links zu den Flotten, zur Sektoransicht und zum Sektorstatus; außerhalb einer Karte in <div class="mod gb-allein"> setzen
    public static function links(int $sector, int $system, array $lang, bool $status = true): string
    {
        $html = '<div class="gb-links"><a href="military.php?se='.$sector.'&amp;sy='.$system.'" class="mod-btn mod-btn-leise">'.$lang['zummilitaer'].'</a>'
            .'<a href="sector.php?sf='.$sector.'" class="mod-btn mod-btn-leise">'.$lang['zursektoransicht'].'</a>';
        if ($status) {
            $html .= '<a href="secstatus.php" class="mod-btn mod-btn-leise">'.$lang['zumsektorstatus'].'</a>';
        }

        return $html.'</div>';
    }

    //Meldung: fehler (rot), ok (grün) oder info (Hinweis mit Akzentlinie)
    public static function meldung(string $text, string $typ = 'fehler'): string
    {
        if ($typ === 'info') {
            return '<div class="mod-hinweis">'.$text.'</div>';
        }

        return '<div class="mod-meldung mod-meldung-'.$typ.'">'.$text.'</div>';
    }
}
