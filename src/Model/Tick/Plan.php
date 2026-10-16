<?php
namespace DieEwigen\DE2\Model\Tick;

//Tickplan aus sv.inc.php ($wts bzw. $kts: je Stunde die Minuten, zu denen ein Tick läuft), Zeiten nach der Serveruhr.
//Genutzt von der Serverinfo (sinfo.php), dem Countdown der Desktop-Kopfzeile (dm.php) und der Sondenhistorie (secret.php).
class Plan
{
    //läuft zur Minute $zeit ein Tick?
    private static function tick(array $plan, int $zeit): bool
    {
        return in_array((int)date('i', $zeit), array_map('intval', $plan[(int)date('G', $zeit)] ?? array()), true);
    }

    //nächster planmäßiger Tick ab der Minute nach $ab (Unix-Zeit), 0 ohne Tick im Plan
    public static function naechster(array $plan, int $ab = 0): int
    {
        $start = (int)(floor(($ab > 0 ? $ab : time()) / 60) * 60) + 60;
        for ($i = 0; $i < 24 * 60; $i++) {
            if (self::tick($plan, $start + $i * 60)) {
                return $start + $i * 60;
            }
        }
        return 0;
    }

    //Anzahl der Ticks nach $von bis einschließlich $bis (Unix-Zeiten), z. B. die WT zwischen zwei Sondenberichten
    public static function anzahlZwischen(array $plan, int $von, int $bis): int
    {
        $anzahl = 0;
        $zeit = self::naechster($plan, $von);
        while ($zeit > 0 && $zeit <= $bis) {
            $anzahl++;
            $zeit = self::naechster($plan, $zeit);
        }
        return $anzahl;
    }
}
