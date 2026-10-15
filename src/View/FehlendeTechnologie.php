<?php
namespace DieEwigen\DE2\View;

//Hinweis „Fehlende Technologie“: Rahmen mit Karte, links das Technologiebild (gp/g/t/{rasse}_{id}.jpg, bleibt bewusst),
//rechts Einleitung, Name und die Knöpfe zur Hilfe und zu den Technologien. Nutzen alle Seiten, die eine Technologie
//voraussetzen. Stil: Block "fehlt" in gp/de-main.scss.
class FehlendeTechnologie
{
    //$satz: Einleitung über dem Namen; $bildId, wenn das Bild einer anderen Technologie gezeigt werden soll (Recycling)
    public static function karte(int $techId, string $satz = '', int $bildId = 0): string
    {
        $rasse = (int)($_SESSION['ums_rasse'] ?? 1);
        $name = '';
        $r = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=?", [$techId]);
        if ($row = mysqli_fetch_assoc($r)) {
            $name = \getTechNameByRasse($row['tech_name'], $rasse);
        }
        $bild = 'gp/g/t/'.$rasse.'_'.($bildId > 0 ? $bildId : $techId).'.jpg';

        $html = \rahmen_oben('Fehlende Technologie', false).'<div class="mod fehlt">';
        if (is_file($bild)) {
            $html .= '<a href="help.php?t='.$techId.'" class="fehlt-bild"><img src="'.$bild.'" alt=""></a>';
        }
        $html .= '<div class="fehlt-text"><span class="mod-typ">'.($satz !== '' ? $satz : 'Du ben&ouml;tigst folgende Technologie').'</span>'
            .'<b class="fehlt-name">'.$name.'</b>'
            .'<div class="fehlt-knoepfe"><a href="help.php?t='.$techId.'" class="mod-btn mod-btn-leise">Was ist das?</a>'
            .\link_technologien('Zu den Technologien', 'mod-btn').'</div></div></div>';

        return $html.\rahmen_unten(false);
    }
}
