<?php
namespace DieEwigen\DE2\Model\Feldzug;

/**
 * Werte und Rollen der Brennpunkte eines Feldzugs (docs/feldzug.md, Abschnitt 4), ohne Datenbank.
 *
 * - Ein Kern mit 3 Kontrollpunkten, ein Drittel der übrigen (abgerundet) mit 2, der Rest mit 1.
 * - Rollen nur außerhalb des Kerns: Festung (ab 8 Brennpunkten zwei), Mine, Werft.
 */
class Brennpunkte
{
    public const ROLLE_KEINE = 0;
    public const ROLLE_FESTUNG = 1;
    public const ROLLE_MINE = 2;
    public const ROLLE_WERFT = 3;

    public const WERT_KERN = 3;
    public const WERT_WICHTIG = 2;
    public const WERT_NORMAL = 1;

    /**
     * @param int      $anzahl Zahl der Brennpunkte (mindestens 4, damit alle Rollen Platz haben)
     * @param callable $zufall fn(int $min, int $max): int, für Tests austauschbar
     * @return array Liste aus ['wert' => int, 'rolle' => int, 'kern' => bool], Index 0 ist der Kern
     */
    public static function werteUndRollen(int $anzahl, callable $zufall): array
    {
        $anzahl = max(1, $anzahl);
        $liste = [['wert' => self::WERT_KERN, 'rolle' => self::ROLLE_KEINE, 'kern' => true]];
        $uebrige = $anzahl - 1;
        $wichtig = intdiv($uebrige, 3);
        for ($i = 0; $i < $uebrige; $i++) {
            $liste[] = ['wert' => $i < $wichtig ? self::WERT_WICHTIG : self::WERT_NORMAL, 'rolle' => self::ROLLE_KEINE, 'kern' => false];
        }

        //Rollen zufällig auf verschiedene Nicht-Kerne verteilen
        $rollen = array_merge(array_fill(0, $anzahl >= 8 ? 2 : 1, self::ROLLE_FESTUNG), [self::ROLLE_MINE, self::ROLLE_WERFT]);
        $frei = range(1, $uebrige);
        foreach ($rollen as $rolle) {
            if (empty($frei)) {
                break;
            }
            $pos = (int)$zufall(0, count($frei) - 1);
            $index = $frei[$pos];
            array_splice($frei, $pos, 1);
            $liste[$index]['rolle'] = $rolle;
        }

        return $liste;
    }

    public static function rollenName(int $rolle, array $lang): string
    {
        switch ($rolle) {
            case self::ROLLE_FESTUNG:
                return $lang['rolle_festung'];
            case self::ROLLE_MINE:
                return $lang['rolle_mine'];
            case self::ROLLE_WERFT:
                return $lang['rolle_werft'];
        }
        return '';
    }
}
