<?php
namespace DieEwigen\DE2\Model\Feldzug;

/**
 * Eintrittspreis eines Feldzugs (docs/feldzug.md, Abschnitt 5), ohne Datenbank.
 *
 * Preis je VS-Rohstoffart = Grundpreis × (Produktion jetzt ÷ Produktion beim ersten Aufruf der Runde),
 * mindestens der Grundpreis, gerundet auf volle Hundert. Ohne Messwert gilt der Grundpreis.
 */
class Preis
{
    public static function berechnen(int $grundpreis, float $jetzt, float $basis): int
    {
        $grundpreis = max(0, $grundpreis);
        if ($jetzt <= 0 || $basis <= 0) {
            return $grundpreis;
        }
        $preis = (int)(round($grundpreis * $jetzt / $basis / 100) * 100);
        return max($grundpreis, $preis);
    }

    /**
     * Median einer Liste von Zahlen, 0 bei leerer Liste.
     */
    public static function median(array $werte): float
    {
        $werte = array_values(array_map('floatval', $werte));
        $anzahl = count($werte);
        if ($anzahl === 0) {
            return 0.0;
        }
        sort($werte);
        $mitte = intdiv($anzahl, 2);
        if ($anzahl % 2 === 1) {
            return $werte[$mitte];
        }
        return ($werte[$mitte - 1] + $werte[$mitte]) / 2;
    }
}
