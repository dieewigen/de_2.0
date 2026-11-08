<?php
namespace DieEwigen\DE2\Model\Battleground;

//Die drei Battlegrounds: Untertitel und Startintervall in Kampfticks (früher $sv_bg aus lib/bg_defs.inc.php).
//Genutzt in specialship.php und tickler/kt.php (dort weiterhin als $sv_bg für kt_manage_bg.php).
class Battleground
{
    public const DEFS = array(
        0 => array('subname' => 'LAST MAN STANDING', 'start_interval' => 8),
        1 => array('subname' => 'RUN TO THE HILLS', 'start_interval' => 24),
        2 => array('subname' => 'BATTLE HYMNS', 'start_interval' => 24),
    );
}
