<?php
namespace DieEwigen\DE2\Model\Verteidigung;

//Boni der Verteidigungsanlagen nach Erfahrungsstufe 0 bis 24 (früher lib/defenseboni.lib.php): Feuerkraftbonus mit
//Chance und Größe in Prozent, Stärkung des planetaren Schilds und Bauzeitreduzierung in Prozent. Genutzt in
//production.php (Anzeige, Bauzeit) und tickler/kt_systemkampf.php (Kampf).
class Boni
{
    private const BAUZEIT = [0, 10, 20, 25, 30, 35, 40, 43, 46, 49, 52, 55, 58, 61, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84];

    //Ergebnis: feuerkraft [Chance, Größe], ps, bauzeit
    public static function fuerStufe(int $stufe): array
    {
        return array(
            'feuerkraft' => array($stufe + 1, $stufe + 1),
            'ps' => $stufe,
            'bauzeit' => self::BAUZEIT[$stufe] ?? 0,
        );
    }
}
