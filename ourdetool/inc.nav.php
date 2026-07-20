<?php
/**
 * Navigationsstruktur des Admintools (einzige Quelle für Sidebar und Dashboard).
 * Format: Gruppe => [ nav-key => [href, Beschriftung] ]
 */
return [
    'Userverwaltung' => [
        'usersearch'  => ['index.php', 'User suchen'],
        'lastreg'     => ['lastreg.php', 'Letzte Registrierungen'],
        'observation' => ['observation.php', 'Beobachtungsliste'],
    ],
    'Multi-Erkennung' => [
        'multi1'      => ['multi.php?statistic=1', 'Multi-IP m. gesperrt'],
        'multi2'      => ['multi.php?statistic=2', 'Multi-IP o. gesperrt'],
        'multi3'      => ['multi.php?statistic=3', 'Multi-IP x Tage'],
        'multicookie' => ['multicookie.php', 'Multi-Cookie'],
        'ipanz'       => ['user_ip_anz.php', 'IP-Anzahl'],
        'useract'     => ['user_act.php', 'Extremaktivit&auml;t'],
        'compare'     => ['comparetool/index.php', 'Compare-Tool'],
    ],
    'Spiel' => [
        'allys'    => ['allys.php', 'Allianz-Listen'],
        'sektor'   => ['sektor.php', 'Sektor bearbeiten'],
        'umfragen' => ['umfragen.php', 'Umfragen'],
        'npctool'  => ['npctool.php', 'NPC-Tool'],
    ],
    'Server' => [
        'server' => ['de_server.php', 'Server'],
        'news'   => ['de_news.php', 'News'],
    ],
];
