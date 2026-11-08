<?php
namespace DieEwigen\DE2\Model\Infocenter;

/**
 * Hinweise "hier gibt es etwas zu tun" für die Menüs: Desktop (getInfocenter() in functions.php, Symbole in dm.php)
 * und Mobil (Markierungen auf den Kacheln in menu.php).
 *
 * Verwendung:
 *   $info = new Infocenter($GLOBALS['dbi']);
 *   $m = $info->missionen($uid, loadPlayerTechs($uid));   // null ohne Missionszentrum
 */
class Infocenter
{
    private \mysqli $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Missionen, erst mit dem Missionszentrum (Technologie 29).
     *
     * @param array $pt Technologien des Spielers aus loadPlayerTechs()
     * @return array{abholbereit:int, laufen:int}|null
     */
    public function missionen(int $uid, array $pt): ?array
    {
        if (!\hasTech($pt, 29)) {
            return null;
        }

        $abholbereit = 0;
        $laufen = 0;
        $res = mysqli_execute_query($this->db, "SELECT end_time, get_reward FROM de_user_mission WHERE user_id=?", [$uid]);
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['end_time'] <= time() && $row['get_reward'] == 0) {
                $abholbereit++;
            }
            if ($row['end_time'] > time()) {
                $laufen++;
            }
        }

        return ['abholbereit' => $abholbereit, 'laufen' => $laufen];
    }

    /**
     * Laufende Technologien (Gebäude, Forschungen, Basisschiffe, V-Systeme).
     *
     * @param array $pt Technologien des Spielers aus loadPlayerTechs()
     * @return array{aktiv:int, offen:int}|null null, wenn es nichts mehr zu erforschen gibt
     */
    public function technologien(array $pt): ?array
    {
        //offen: Technologien der Technologieseite, die weder fertig sind noch laufen
        $offen = 0;
        $res = mysqli_execute_query($this->db, "SELECT tech_id FROM de_tech_data WHERE tech_sort_id < 1000", []);
        while ($row = mysqli_fetch_assoc($res)) {
            if (!isset($pt[$row['tech_id']])) {
                $offen++;
            }
        }
        if ($offen == 0) {
            return null;
        }

        $aktiv = 0;
        foreach ($pt as $tech) {
            if (is_array($tech) && $tech['time_finished'] > time()) {
                $aktiv++;
            }
        }

        return ['aktiv' => $aktiv, 'offen' => $offen];
    }

    /**
     * Entdeckte Flotten im Anflug auf das eigene System, wie in resline.php.
     *
     * @return array{angreifer:int, verteidiger:int} Anzahl der Flotten
     */
    public function flotten(int $sector, int $system): array
    {
        $erg = ['angreifer' => 0, 'verteidiger' => 0];
        $res = mysqli_execute_query($this->db, "SELECT aktion FROM de_user_fleet WHERE zielsec = ? AND zielsys = ? AND (aktion = 1 OR aktion = 2) AND entdeckt > 0", [$sector, $system]);
        while ($row = mysqli_fetch_assoc($res)) {
            $erg[$row['aktion'] == 1 ? 'angreifer' : 'verteidiger']++;
        }

        return $erg;
    }
}
