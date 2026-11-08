<?php
namespace DieEwigen\DE2\Model\Feldzug;

/**
 * Auswertung eines Feldzug-Zuges (docs/feldzug.md, Abschnitt 7), ohne Datenbank und ohne Zufall.
 *
 * Für jeden Brennpunkt gleichzeitig:
 * - Stärke jeder Allianz = ihre Legionen dort; der bisherige Halter bekommt +1, auf einer Festung +2,
 *   auch ohne Legionen.
 * - Die stärkste Allianz hält den Brennpunkt. Bei Gleichstand bleibt der bisherige Halter, auch wenn er selbst
 *   schwächer ist; ein neutraler Brennpunkt bleibt bei Gleichstand oder ohne Legionen neutral.
 * - Jeder gehaltene Brennpunkt bringt seinem Halter die Kontrollpunkte seines Wertes.
 * Allianzen, die nicht mehr teilnehmen (z. B. gelöscht), zählen nicht: ihre Brennpunkte werden neutral.
 */
class Auswertung
{
    public const HALTERBONUS = 1;
    public const HALTERBONUS_FESTUNG = 2;

    /**
     * @param array $brennpunkte  [brennpunkt_id => ['wert' => int, 'rolle' => int, 'halter' => ally_id oder 0]]
     * @param array $verteilungen [ally_id => [brennpunkt_id => Legionen]]
     * @param array $aktive       ally_ids, die noch am Feldzug teilnehmen
     * @return array [
     *     'brennpunkte' => [id => ['halter_vorher' => int, 'halter' => int, 'legionen' => [ally => n], 'staerken' => [ally => n]]],
     *     'kontrollpunkte' => [ally => int], 'gehalten' => [ally => int]
     * ]
     */
    public static function zug(array $brennpunkte, array $verteilungen, array $aktive): array
    {
        $aktiv = array_fill_keys(array_map('intval', $aktive), true);
        $ergebnis = ['brennpunkte' => [], 'kontrollpunkte' => [], 'gehalten' => []];
        foreach (array_keys($aktiv) as $ally) {
            $ergebnis['kontrollpunkte'][$ally] = 0;
            $ergebnis['gehalten'][$ally] = 0;
        }

        foreach ($brennpunkte as $id => $bp) {
            $id = (int)$id;
            $halter = (int)($bp['halter'] ?? 0);
            $halterVorher = $halter;
            //Halter, die nicht mehr teilnehmen, verlieren den Brennpunkt
            if ($halter > 0 && !isset($aktiv[$halter])) {
                $halter = 0;
            }

            $legionen = [];
            $staerken = [];
            foreach ($verteilungen as $ally => $verteilung) {
                $ally = (int)$ally;
                $n = (int)($verteilung[$id] ?? 0);
                if (!isset($aktiv[$ally]) || $n <= 0) {
                    continue;
                }
                $legionen[$ally] = $n;
                $staerken[$ally] = $n;
            }
            if ($halter > 0) {
                $bonus = ((int)($bp['rolle'] ?? 0) === Brennpunkte::ROLLE_FESTUNG) ? self::HALTERBONUS_FESTUNG : self::HALTERBONUS;
                $staerken[$halter] = ($staerken[$halter] ?? 0) + $bonus;
            }

            $neu = $halter;
            if (!empty($staerken)) {
                $max = max($staerken);
                $beste = array_keys($staerken, $max, true);
                if (count($beste) === 1) {
                    $neu = (int)$beste[0];
                }
                //Gleichstand: der bisherige Halter bleibt, ein neutraler Brennpunkt bleibt neutral
            }

            ksort($legionen);
            ksort($staerken);
            $ergebnis['brennpunkte'][$id] = [
                'halter_vorher' => $halterVorher,
                'halter' => $neu,
                'legionen' => $legionen,
                'staerken' => $staerken,
            ];
            if ($neu > 0) {
                $ergebnis['kontrollpunkte'][$neu] = ($ergebnis['kontrollpunkte'][$neu] ?? 0) + (int)($bp['wert'] ?? 1);
                $ergebnis['gehalten'][$neu] = ($ergebnis['gehalten'][$neu] ?? 0) + 1;
            }
        }

        return $ergebnis;
    }
}
