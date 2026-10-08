<?php
namespace DieEwigen\DE2\Model\Battleground;

/**
 * Haltung des Basissterns in den Battlegrounds, nach dem Prinzip Stein-Schere-Papier.
 *
 * Angriff schlägt Manöver, Manöver schlägt Schild, Schild schlägt Angriff. Die überlegene Haltung richtet
 * 50 % mehr Schaden an und erleidet nur zwei Drittel des gegnerischen Schadens. Damit schlägt sie Gegner bis
 * etwa zur anderthalbfachen Stufe. Bei gleicher Haltung entscheidet wie bisher die Stufe.
 * „Zufall“ ist der Standard und würfelt die Haltung für jeden Kampf neu aus.
 *
 * Gewählt wird je Battleground in specialship.php. Gespeichert wird die Haltung im Basisstern
 * (lib/special_ship.class.php), ausgewertet in tickler/kt_manage_bg.php, angezeigt im Kampfbericht
 * (lib/kampfbericht.lib.php).
 */
class Haltung
{
    public const ZUFALL = 0;
    public const ANGRIFF = 1;
    public const SCHILD = 2;
    public const MANOEVER = 3;

    //Faktor auf den eigenen Schaden mit Vorteil bzw. mit Nachteil
    public const SCHADEN_VORTEIL = 1.5;
    public const SCHADEN_NACHTEIL = 2 / 3;

    private const NAMEN = [
        self::ZUFALL => 'Zufall',
        self::ANGRIFF => 'Angriff',
        self::SCHILD => 'Schild',
        self::MANOEVER => 'Man&ouml;ver',
    ];

    //Haltung => Haltung, die sie schlägt
    private const SCHLAEGT = [
        self::ANGRIFF => self::MANOEVER,
        self::MANOEVER => self::SCHILD,
        self::SCHILD => self::ANGRIFF,
    ];

    /**
     * Alle wählbaren Haltungen (Wert => Name als HTML), Zufall zuerst.
     */
    public static function alle(): array
    {
        return self::NAMEN;
    }

    /**
     * @param mixed $wert Wert aus der Anfrage oder dem gespeicherten Basisstern
     */
    public static function gueltig($wert): bool
    {
        return is_numeric($wert) && (string)(int)$wert === (string)$wert && isset(self::NAMEN[(int)$wert]);
    }

    public static function name(int $haltung): string
    {
        return self::NAMEN[$haltung] ?? self::NAMEN[self::ZUFALL];
    }

    /**
     * Haltung, die von $haltung geschlagen wird (für die Erklärung auf der Seite).
     */
    public static function schlaegt(int $haltung): int
    {
        return self::SCHLAEGT[$haltung] ?? self::ZUFALL;
    }

    /**
     * Haltung für einen Kampf: Zufall und ungültige Werte werden ausgewürfelt.
     */
    public static function aufloesen(int $wahl): int
    {
        if (isset(self::SCHLAEGT[$wahl])) {
            return $wahl;
        }
        return mt_rand(self::ANGRIFF, self::MANOEVER);
    }

    /**
     * 1, wenn $a die Haltung $b schlägt, -1 im umgekehrten Fall, sonst 0.
     */
    public static function vorteil(int $a, int $b): int
    {
        if ((self::SCHLAEGT[$a] ?? null) === $b) {
            return 1;
        }
        if ((self::SCHLAEGT[$b] ?? null) === $a) {
            return -1;
        }
        return 0;
    }

    /**
     * Faktor auf den Schaden, den die Seite mit Haltung $eigene gegen $gegner anrichtet.
     */
    public static function schadensfaktor(int $eigene, int $gegner): float
    {
        $vorteil = self::vorteil($eigene, $gegner);
        if ($vorteil > 0) {
            return self::SCHADEN_VORTEIL;
        }
        if ($vorteil < 0) {
            return self::SCHADEN_NACHTEIL;
        }
        return 1.0;
    }

    /**
     * Haltung einer Allianz im Allianz-Battleground: die Haltung mit den meisten Basisstern-Stufen.
     * Wer Zufall gewählt hat, stimmt nicht mit. Gleichstand oder keine Stimme ergibt Zufall.
     *
     * @param array $stimmen Liste aus [Haltung, Stufe] je Teilnehmer
     */
    public static function mehrheit(array $stimmen): int
    {
        $summe = [];
        foreach ($stimmen as $stimme) {
            $haltung = (int)($stimme[0] ?? self::ZUFALL);
            if (!isset(self::SCHLAEGT[$haltung])) {
                continue;
            }
            $summe[$haltung] = ($summe[$haltung] ?? 0) + max(0, (int)($stimme[1] ?? 0));
        }
        if (empty($summe)) {
            return self::ZUFALL;
        }

        arsort($summe);
        $werte = array_values($summe);
        if (count($werte) > 1 && $werte[0] === $werte[1]) {
            return self::ZUFALL;
        }
        return (int)array_key_first($summe);
    }
}
