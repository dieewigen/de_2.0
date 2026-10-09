<?php
namespace DieEwigen\DE2\Model\Feldzug;

/**
 * Sieger eines Feldzugs und Feldherren der Runde (docs/feldzug.md, Abschnitt 8), ohne Datenbank.
 */
class Sieger
{
    /**
     * Sieger: meiste Kontrollpunkte, bei Gleichstand mehr gehaltene Brennpunkte am Ende, danach das Los.
     *
     * @param array    $teilnehmer [ally_id => ['kontrollpunkte' => int, 'gehalten' => int]]
     * @param callable $los        fn(int $min, int $max): int
     * @return int ally_id, 0 ohne Teilnehmer
     */
    public static function feldzug(array $teilnehmer, callable $los): int
    {
        return self::bester($teilnehmer, 'kontrollpunkte', 'gehalten', $los);
    }

    /**
     * Feldherren: meiste Feldzugsiege der Runde, bei Gleichstand die Summe der Kontrollpunkte, danach das Los.
     *
     * @param array $allys [ally_id => ['siege' => int, 'kontrollpunkte' => int]]
     */
    public static function feldherren(array $allys, callable $los): int
    {
        return self::bester($allys, 'siege', 'kontrollpunkte', $los);
    }

    /**
     * Platzierung nach Kontrollpunkten und gehaltenen Brennpunkten (gleiche Werte, gleicher Platz).
     *
     * @return array [ally_id => platz]
     */
    public static function plaetze(array $teilnehmer): array
    {
        $liste = $teilnehmer;
        uasort($liste, function ($a, $b) {
            return [(int)$b['kontrollpunkte'], (int)$b['gehalten']] <=> [(int)$a['kontrollpunkte'], (int)$a['gehalten']];
        });
        $plaetze = [];
        $platz = 0;
        $vorher = null;
        $i = 0;
        foreach ($liste as $ally => $werte) {
            $i++;
            $schluessel = [(int)$werte['kontrollpunkte'], (int)$werte['gehalten']];
            if ($schluessel !== $vorher) {
                $platz = $i;
                $vorher = $schluessel;
            }
            $plaetze[(int)$ally] = $platz;
        }
        return $plaetze;
    }

    private static function bester(array $liste, string $erst, string $dann, callable $los): int
    {
        if (empty($liste)) {
            return 0;
        }
        $bestWert = null;
        $kandidaten = [];
        foreach ($liste as $ally => $werte) {
            $wert = [(int)($werte[$erst] ?? 0), (int)($werte[$dann] ?? 0)];
            if ($bestWert === null || $wert > $bestWert) {
                $bestWert = $wert;
                $kandidaten = [(int)$ally];
            } elseif ($wert === $bestWert) {
                $kandidaten[] = (int)$ally;
            }
        }
        if (count($kandidaten) === 1) {
            return $kandidaten[0];
        }
        sort($kandidaten);
        return $kandidaten[(int)$los(0, count($kandidaten) - 1)];
    }
}
