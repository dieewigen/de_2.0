<?php
namespace DieEwigen\DE2\View;

//Browser-Zubehör im <head> jeder obersten Seite (Spielseiten über cssinclude.php, Classic-Frameset, dm.php, Chat, Menü):
//Favicon, Startbildschirm-Symbol (gp/g/touch-icon.png) und die Farbe der Browserleiste am Handy in der Rassenfarbe.
class Seitenkopf
{
    private const FARBEN = [1 => '#3399FF', 2 => '#A6A6A6', 3 => '#DE3939', 4 => '#ED951E'];

    public static function zubehoer(int $rasse): string
    {
        $farbe = self::FARBEN[$rasse] ?? self::FARBEN[1];

        return '<link rel="icon" href="favicon.ico">'
            .'<link rel="icon" type="image/png" sizes="180x180" href="gp/g/touch-icon.png">'
            .'<link rel="apple-touch-icon" href="gp/g/touch-icon.png">'
            .'<meta name="theme-color" content="'.$farbe.'">'
            .'<meta name="color-scheme" content="dark">';
    }
}
