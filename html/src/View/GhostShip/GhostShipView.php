<?php
namespace DieEwigen\DE2\View\GhostShip;

use DieEwigen\DE2\Model\GhostShip\GhostShipEvent;

/**
 * Darstellung des Geisterschiffs (src/Model/GhostShip/GhostShipEvent.php). Außerhalb des Events liefert alles ''.
 */
class GhostShipView
{
    //Kopf jeder Spielseite (cssinclude.php): Stil, Daten und Script
    public static function pageHead(GhostShipEvent $event): string
    {
        if (!$event->isActive()) {
            return '';
        }
        $root = dirname(__DIR__, 3);
        return '<link rel="stylesheet" href="gp/geisterschiff.css?'.filemtime($root.'/gp/geisterschiff.css').'" data-geist>'
            .'<script>window.deGeist='.json_encode($event->clientConfig(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE).';</script>'
            .'<script src="js/geisterschiff.js?'.filemtime($root.'/js/geisterschiff.js').'" defer></script>';
    }

    //Zeile in der Sektoransicht (sector.php): blass, ohne Koordinaten und Aktionen, zählt nirgends mit
    public static function sectorRow(GhostShipEvent $event, int $secId): string
    {
        if ($event->getCurrentSector() !== $secId) {
            return '';
        }
        $t = $event->texte();
        $leer = '<td></td>';
        $wert = '<td class="sec-zahl">▮▮▮</td>';
        return '<tr class="geist-zeile"><td>∅</td>'.$leer
            .'<td class="sec-wrap"><span title="'.htmlspecialchars($t['zeile_tooltip']).'">'.$t['name'].'</span></td>'
            .'<td>?</td>'.$leer.$wert.$wert.$leer.'</tr>';
    }

    //abgefangener Funkspruch oben in der Karte von Sektor 666 (src/View/Sektor666/Karte.php)
    public static function sector666(GhostShipEvent $event, bool $wach): string
    {
        if (!$event->isActive()) {
            return '';
        }
        $t = $event->texte();
        $html = '<div class="geist-funk"><b>'.$t['s666_kopf'].'</b><p>'.($wach ? $t['s666_wach'] : $t['s666_schlaf']).'</p>';
        if ($event->hasArrived()) {
            $html .= '<p>'.$t['s666_angekommen'].'</p><button type="button" class="mod-btn" data-geist-abspielen>'.$t['s666_abspielen'].'</button>';
        }
        return $html.'</div>';
    }
}
