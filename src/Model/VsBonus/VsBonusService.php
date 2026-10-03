<?php
namespace DieEwigen\DE2\Model\VsBonus;

/**
 * Zeitlich begrenzte Boni in den Vergessenen Systemen, z. B. als Belohnung für Hekates Aufträge.
 *
 * Die Boni wirken nur im VS-Kreislauf: Industrie-Ertrag (tickler/wt_manage_map.php) und Bau-/Upgradezeit
 * (vs_duration_factor() in functions.php). Laufzeiten zählen in Wirtschaftsticks, gespeichert wird der WT,
 * bis zu dem der Bonus gilt. Die Tabelle de_vs_bonus wird beim Rundenreset geleert.
 */
class VsBonusService
{
    public const TYP_INDUSTRIE = 1;
    public const TYP_BAUZEIT = 2;

    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    //Konfiguration, überschreibbar in sv.inc.php
    public static function getProzent(int $typ): int
    {
        if ($typ === self::TYP_INDUSTRIE) {
            return max(0, (int)($GLOBALS['sv_vs_bonus_industrie_prozent'] ?? 25));
        }
        return max(0, min(50, (int)($GLOBALS['sv_vs_bonus_bauzeit_prozent'] ?? 25)));
    }

    /**
     * So viele WT darf ein Bonus höchstens im Voraus laufen. Längere Restlaufzeiten gelten als Altlast
     * (z. B. aus einer früheren Runde) und werden ignoriert.
     */
    public static function getMaxVorlauf(): int
    {
        return max(1, (int)($GLOBALS['sv_vs_bonus_max_vorlauf'] ?? 384));
    }

    public function getCurrentWt(): int
    {
        $res = mysqli_execute_query($this->db, "SELECT wt FROM de_system LIMIT 1", []);
        return max(1, (int)(mysqli_fetch_assoc($res)['wt'] ?? 1));
    }

    /**
     * Bonus gewähren oder einen laufenden verlängern, höchstens bis wt + getMaxVorlauf().
     *
     * @return int WT, bis zu dem der Bonus jetzt gilt
     */
    public function grant(int $uid, int $typ, int $dauer, int $wt): int
    {
        $max = $wt + self::getMaxVorlauf();
        mysqli_execute_query($this->db,
            "INSERT INTO de_vs_bonus (user_id, typ, bis_wt) VALUES (?, ?, LEAST(?, ?))
             ON DUPLICATE KEY UPDATE bis_wt = LEAST(IF(bis_wt > ? AND bis_wt <= ?, bis_wt, ?) + ?, ?)",
            [$uid, $typ, $wt + $dauer, $max, $wt, $max, $wt, $dauer, $max]);

        $res = mysqli_execute_query($this->db, "SELECT bis_wt FROM de_vs_bonus WHERE user_id = ? AND typ = ?", [$uid, $typ]);
        return (int)(mysqli_fetch_assoc($res)['bis_wt'] ?? 0);
    }

    /**
     * Aktive Boni eines Spielers.
     *
     * @return array<int,int> typ => verbleibende WT
     */
    public function getActive(int $uid, int $wt): array
    {
        $res = mysqli_execute_query($this->db, "SELECT typ, bis_wt FROM de_vs_bonus WHERE user_id = ? AND bis_wt > ? AND bis_wt <= ?", [$uid, $wt, $wt + self::getMaxVorlauf()]);
        $active = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $active[(int)$row['typ']] = (int)$row['bis_wt'] - $wt;
        }
        return $active;
    }

    /**
     * Aktive Boni aller Spieler, für den Wirtschaftstick.
     *
     * @return array<int,array<int,int>> user_id => [typ => verbleibende WT]
     */
    public function loadAllActive(int $wt): array
    {
        $res = mysqli_execute_query($this->db, "SELECT user_id, typ, bis_wt FROM de_vs_bonus WHERE bis_wt > ? AND bis_wt <= ?", [$wt, $wt + self::getMaxVorlauf()]);
        $active = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $active[(int)$row['user_id']][(int)$row['typ']] = (int)$row['bis_wt'] - $wt;
        }
        return $active;
    }
}
