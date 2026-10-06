<?php
namespace DieEwigen\DE2\View;

/**
 * Zeitpunkte von Vorgängen, die in Echtzeit laufen (Missionen, Allianzeinsicht, Forschung in den Vergessenen Systemen).
 *
 * Diese laufen nicht in Ticks, darum zeigen die Seiten die Uhrzeit statt WT/KT oder eines Countdowns;
 * an einem anderen Tag mit Datum.
 *
 * Verwendung:
 *   echo 'Mission '.RealTime::until($mission_time);   // Mission bis 16:49 Uhr
 */
class RealTime
{
    /**
     * Uhrzeit, z. B. "16:49 Uhr" oder "07.10. 16:49 Uhr".
     */
    public static function at(int $timestamp): string
    {
        if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
            return date('H:i', $timestamp).' Uhr';
        }
        return date('d.m. H:i', $timestamp).' Uhr';
    }

    /**
     * Ende eines Vorgangs, z. B. "bis 16:49 Uhr".
     */
    public static function until(int $timestamp): string
    {
        return 'bis '.self::at($timestamp);
    }
}
