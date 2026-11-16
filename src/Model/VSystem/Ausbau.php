<?php
namespace DieEwigen\DE2\Model\VSystem;

//Fertige Stufe der Gebäude in den Vergessenen Systemen. In de_user_map_bldg steht beim Bau schon die Zielstufe (bldg_level)
//und als bldg_time das Ende des letzten angehängten Ausbaus. Der Ausbau auf Stufe k dauert k Bauzeiten je Stufe
//(map_system.class.php), deshalb wird von bldg_time aus rückwärts gerechnet. Die Bauzeit je Stufe nimmt den heutigen
//Bauzeitbonus; hat er sich während eines Ausbaus geändert, verschiebt sich eine Stufe um wenige Minuten.
class Ausbau
{
    //$dauer: Bauzeit je Stufe in Sekunden (Grundbauzeit × tech_build_time_faktor × vs_duration_factor)
    public static function fertigeStufe(int $stufe, int $fertig, float $dauer): int
    {
        $jetzt = time();
        while ($stufe > 0 && $fertig > $jetzt) {
            $fertig -= round($dauer * $stufe);
            $stufe--;
        }

        return $stufe;
    }

    //Gebäude des Spielers ab der Zielstufe $mindestStufe, je Zeile bldg_id und die fertige Stufe ('stufe');
    //braucht lib/map_system_defs.inc.php und inc/userartefact.inc.php ($ua_werte für vs_duration_factor)
    public static function gebaeude(\mysqli $db, int $uid, int $mindestStufe = 1): array
    {
        $faktor = null;
        $liste = array();
        $res = mysqli_execute_query($db, "SELECT bldg_id, bldg_level, bldg_time FROM de_user_map_bldg WHERE user_id=? AND bldg_level>=?", [$uid, $mindestStufe]);
        while ($row = mysqli_fetch_assoc($res)) {
            $stufe = (int)$row['bldg_level'];
            if ($row['bldg_time'] > time()) {
                $faktor ??= $GLOBALS['tech_build_time_faktor'] * vs_duration_factor($uid, $GLOBALS['ua_werte']);
                $stufe = self::fertigeStufe($stufe, (int)$row['bldg_time'], ($GLOBALS['map_buildings'][$row['bldg_id']]['bldg_time'] ?? 0) * $faktor);
            }
            $liste[] = array('bldg_id' => (int)$row['bldg_id'], 'stufe' => $stufe);
        }

        return $liste;
    }
}
