<?php
namespace DieEwigen\DE2\View;

//Neue mobile Darstellung (Okt. 2026): Kopfleiste mit Menü, Signalen, Rohstoffen und Zeiten statt der Bitmap-Rohstoffleiste,
//Seiten in Gerätebreite (gp/de-mobil.scss). Abschaltbar in den Optionen über das Cookie mobil_alt (bisherige Darstellung mit
//620-px-Viewport und Rohstoffleiste); cssinclude.php (Viewport, Stylesheet) und resline.php (Kopfleiste) fragen neu() ab.
class Mobil
{
    public static function neu(): bool
    {
        return (int)($_SESSION['ums_mobi'] ?? 0) === 1 && empty($_COOKIE['mobil_alt']);
    }

    //Kopfleiste; die Werte stellt resline.php zusammen: rasse, rohstoffe (fünf kurze Zahlen), namen (fünf Rohstoffnamen), punkte,
    //zeit, wt, kt, verteidiger und angreifer (kurze Zahl oder ''), hyperfunk, nachrichten und geschenk (true bei Neuem),
    //wischen (true, wenn die Wischgesten an sind: dann steht unten der Hinweis, wohin sie führen)
    public static function kopf(array $w): string
    {
        $h = Symbole::sprite();
        $h .= '<header class="mob-kopf">';
        $h .= '<div class="mob-zeile mob-oben">'
            .'<a href="menu.php" class="mob-knopf" aria-label="Men&uuml;"><svg class="app-ico"><use href="#i-menue"/></svg></a>'
            .'<a href="overview.php" class="mob-marke"><img src="gp/g/derassenlogo'.(int)$w['rasse'].'.png" alt=""><b>Die Ewigen</b></a>'
            .'<span class="mob-signale">'
            .'<a href="hyperfunk.php'.($w['hyperfunk'] ? '?l=new' : '').'" class="mob-signal'.($w['hyperfunk'] ? ' mob-neu' : '').'" aria-label="Hyperfunk"><svg class="app-ico"><use href="#i-hyperfunk"/></svg></a>'
            .'<a href="sysnews.php" class="mob-signal'.($w['nachrichten'] ? ' mob-neu' : '').'" aria-label="Nachrichten"><svg class="app-ico"><use href="#i-nachrichten"/></svg></a>'
            .($w['geschenk'] ? '<a href="ally_dailygift.php" class="mob-signal mob-geschenk" aria-label="Allianzbonus"><img src="gp/g/icon15.png" alt=""></a>' : '')
            .'<a href="chat.php" class="mob-knopf" aria-label="Chat"><svg class="app-ico"><use href="#i-chat"/></svg></a>'
            .'</span></div>';
        $h .= '<div class="mob-zeile mob-werte">';
        foreach ($w['rohstoffe'] as $i => $wert) {
            $h .= '<a href="resource.php" class="mob-wert" aria-label="'.$w['namen'][$i].'"><img src="gp/g/icon'.($i + 1).'.png" alt=""><b>'.$wert.'</b></a>';
        }
        $h .= '<a href="toplist.php" class="mob-wert" aria-label="Punkte"><img src="gp/g/icon8.png" alt=""><b>'.$w['punkte'].'</b></a>';
        $h .= '</div>';
        $h .= '<div class="mob-zeile mob-unten">'
            .'<a href="sinfo.php" class="mob-zeiten"><span><small>Zeit</small>'.$w['zeit'].'</span><span><small>WT</small>'.$w['wt'].'</span><span><small>KT</small>'.$w['kt'].'</span></a>'
            .($w['verteidiger'] !== '' ? '<a href="secstatus.php" class="mob-flotte mob-gut"><img src="gp/g/icon6.png" alt="">'.$w['verteidiger'].'</a>' : '')
            .($w['angreifer'] !== '' ? '<a href="secstatus.php" class="mob-flotte mob-gefahr"><img src="gp/g/icon7.png" alt="">'.$w['angreifer'].'</a>' : '')
            .($w['wischen'] ? '<span class="mob-wisch">Wischen: nach rechts Men&uuml;, nach links Chat</span>' : '')
            .'</div></header>';

        return $h;
    }
}
