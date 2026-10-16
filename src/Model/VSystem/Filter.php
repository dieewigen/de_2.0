<?php
namespace DieEwigen\DE2\Model\VSystem;

//Filter der VS-Übersicht (map_mobile.php): die Auswahl liegt in den Cookies vsf0a/b/c (vs_filter in js/ang_fn.js),
//die Filterklassen je System liefert map_system.class.php. Damit führen die Pfeile der Systemseite
//(generate_vsystem_kopfzeile in functions.php) im Filter nur durch die passenden Systeme.
class Filter
{
    //aktiver Filter aus den Cookies: array(Filtertag, Vergleich gg/kg/g, Stufe) oder null ohne Filter
    public static function ausCookies(array $cookies): ?array
    {
        $tag = trim((string)($cookies['vsf0a'] ?? ''));
        if ($tag === '' || !preg_match('/^f_[a-z0-9_]+$/', $tag)) {
            return null;
        }
        $vergleich = (string)($cookies['vsf0b'] ?? 'gg');
        if (!in_array($vergleich, array('gg', 'kg', 'g'), true)) {
            $vergleich = 'gg';
        }
        return array($tag, $vergleich, max(0, min(10, (int)($cookies['vsf0c'] ?? 0))));
    }

    //IDs der Systeme, die den Filter erfüllen, aufsteigend; $klassen: System-ID => Filterklassen des Systems
    public static function passendeIds(array $klassen, array $filter): array
    {
        [$tag, $vergleich, $stufe] = $filter;
        $ids = array();
        foreach ($klassen as $id => $liste) {
            $teile = preg_split('/\s+/', trim((string)$liste));
            if ($tag === 'f_unsy' || $tag === 'f_spez') {
                //unerforschte und Sonder-Systeme haben keine Stufe
                $passt = in_array($tag, $teile, true);
            } else {
                $passt = false;
                foreach ($teile as $klasse) {
                    //Gebäudeklassen heißen Filtertag_Stufe
                    if (strpos($klasse, $tag.'_') !== 0 || !ctype_digit(substr($klasse, strlen($tag) + 1))) {
                        continue;
                    }
                    $s = (int)substr($klasse, strlen($tag) + 1);
                    if (($vergleich === 'gg' && $s >= $stufe) || ($vergleich === 'kg' && $s <= $stufe) || ($vergleich === 'g' && $s == $stufe)) {
                        $passt = true;
                        break;
                    }
                }
            }
            if ($passt) {
                $ids[] = (int)$id;
            }
        }
        sort($ids);
        return $ids;
    }
}
