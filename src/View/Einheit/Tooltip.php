<?php
namespace DieEwigen\DE2\View\Einheit;

//Tooltip einer Einheit in Produktion (production.php) und Flotten (military.php): Klasse, Punkte, Reisezeit und Kapazitäten,
//Waffengattungen mit Zielen, Besonderheit. Die Ausgabe landet in title="…" und wird von setTooltip() (js/de_fn.js) als
//HTML gezeigt, darum stehen Attribute hier in einfachen Anführungszeichen. Stil: Block "#tt .ei" in gp/de-main.scss.
class Tooltip
{
    //$d: klasse [Bezeichnung, Name], punkte [Bezeichnung, Wert], werte [[Bezeichnung, Wert], …],
    //waffen [[Bezeichnung, vorhanden, Text ja/nein, Ziele [[Bezeichnung, Klasse], …]], …], besonderheit Text oder leer
    public static function html(array $d): string
    {
        $h = "<div class='ei'>";
        $h .= "<div class='ei-zeile'><span class='ei-klasse'>".$d['klasse'][0]."</span><b>".$d['klasse'][1]."</b></div>";
        $h .= "<div class='ei-zeile'><span class='ei-punkte'>".$d['punkte'][0]."</span><b>".$d['punkte'][1]."</b></div>";
        if (!empty($d['werte'])) {
            $h .= "<div class='ei-werte'>";
            foreach ($d['werte'] as $w) {
                $h .= "<span>".$w[0]." <b>".$w[1]."</b></span>";
            }
            $h .= "</div>";
        }
        foreach ($d['waffen'] as $i => $w) {
            $h .= "<div class='ei-waffe ei-waffe-".$i."'><span class='ei-waffe-name'>".$w[0]."</span><span class='".($w[1] ? 'ei-ja' : 'ei-nein')."'>".$w[2]."</span>";
            if ($w[1] && !empty($w[3])) {
                $h .= "<div class='ei-ziele'>";
                foreach ($w[3] as $z) {
                    $h .= "<span>".$z[0]." <b>".$z[1]."</b></span>";
                }
                $h .= "</div>";
            }
            $h .= "</div>";
        }
        if (!empty($d['besonderheit'])) {
            $h .= "<div class='ei-besonderheit'>".$d['besonderheit']."</div>";
        }

        return $h."</div>";
    }

    //Statusinformationen (production.php): Abschnitte [Titel, Zeilen [Bezeichnung, Wert], Hinweis]
    public static function status(array $abschnitte): string
    {
        $h = "<div class='ei'>";
        foreach ($abschnitte as $a) {
            $h .= "<div class='ei-abschnitt'><span class='ei-titel'>".$a[0]."</span>";
            foreach ($a[1] as $z) {
                $h .= "<div class='ei-zeile'><span>".$z[0]."</span><b>".$z[1]."</b></div>";
            }
            if (!empty($a[2])) {
                $h .= "<div class='ei-hinweis'>".$a[2]."</div>";
            }
            $h .= "</div>";
        }

        return $h."</div>";
    }
}
