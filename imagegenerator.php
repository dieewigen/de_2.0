<?php

session_start();
header("Content-Type: image/png");
include "inc/sv.inc.php";
$givenocredit = 1;
include "inccon.php";
include 'inc/lang/'.$sv_server_lang.'_imagegenerator.lang.php';


$ix = 500;
$iy = 160;

/************************************************************
*                                                           *
*      Rechenaufgabe bestimmen (Ergebnis 1 bis 100)         *
*                                                           *
*************************************************************/

function imagegenerator_neue_aufgabe()
{
    //schwierigkeit: 1 = leicht (zweiter operand einstellig, kein zehneruebergang),
    //2 = mittel (einstellig mit uebertrag), 3 = schwer (voller bereich bis 100).
    //der produktionswert kann in der nicht versionierten sv.inc.php gesetzt werden
    $schwierigkeit = $GLOBALS['sv_botcheck_schwierigkeit'] ?? 1;

    do {
        $operator = (random_int(0, 1) == 1) ? 'plus' : 'minus';

        if ($schwierigkeit == 3) {
            //voller bereich, ergebnis gleichverteilt
            if ($operator == 'plus') {
                $ergebnis = random_int(2, 100);
                $a = random_int(1, $ergebnis - 1);
                $b = $ergebnis - $a;
            } else {
                $ergebnis = random_int(1, 99);
                $b = random_int(1, 100 - $ergebnis);
                $a = $ergebnis + $b;
            }
        } else {
            //zweiter operand einstellig
            $a = random_int(1, 99);
            $b = random_int(1, 9);
            $ergebnis = ($operator == 'plus') ? $a + $b : $a - $b;
        }

        $gueltig = ($ergebnis >= 1 && $ergebnis <= 100);

        //leicht: einerstellen muessen ohne zehneruebergang/borgen verrechenbar sein
        if ($gueltig && $schwierigkeit == 1) {
            if ($operator == 'plus') {
                $gueltig = (($a % 10) + $b <= 9);
            } else {
                $gueltig = (($a % 10) >= $b);
            }
        }

        //zwei sehr lange zahlwoerter zusammen passen nicht lesbar ins bild
        $textlaenge = strlen($GLOBALS['zahl'][$a - 1]) + strlen($GLOBALS['zahl'][$b - 1]);
    } while (!$gueltig || $textlaenge > 26);

    return array(
        'a' => $a,
        'operator' => $operator,
        'b' => $b,
        'ergebnis' => $ergebnis,
        'seed' => random_int(0, mt_getrandmax()),
    );
}

//die aufgabe ist an die laufende abfrage (token aus session.inc.php) gebunden:
//solange derselbe token aktiv ist, wird dieselbe aufgabe mit demselben seed
//gerendert. so kann eine fremdseite die laufende abfrage nicht per bildabruf
//ueberschreiben und mehrfachabrufe liefern keine neuen rausch-varianten
//derselben aufgabe zum herausfiltern.
if (isset($_SESSION['botcheck_token'])) {
    if (!isset($_SESSION['botcheck_task'])) {
        $aufgabe = imagegenerator_neue_aufgabe();
        $_SESSION['botcheck_task'] = $aufgabe;
        $_SESSION['botcheck_answer'] = $aufgabe['ergebnis'];
    }
    $aufgabe = $_SESSION['botcheck_task'];

    //ab hier laeuft das rendering deterministisch pro aufgabe
    mt_srand($aufgabe['seed']);
} else {
    //keine aktive abfrage (z.b. direktaufruf): nur ein rauschbild ohne
    //aufgabe ausliefern und nichts in die session schreiben
    $aufgabe = null;
    mt_srand(random_int(0, mt_getrandmax()));
}

/************************************************************
*                                                           *
*      Bild anlegen: opaker dunkler Hintergrund             *
*      (transparenz waere eine perfekte pixelmaske)         *
*                                                           *
*************************************************************/

$image = imagecreatetruecolor($ix, $iy);
$backgroundcolor = imagecolorallocate($image, 17, 17, 17);
imagefilledrectangle($image, 0, 0, $ix - 1, $iy - 1, $backgroundcolor);

/************************************************************
*                                                           *
*      Schriftzug: pro zeichen font, groesse, winkel        *
*      und grauton, mit ueberlappung und versatz            *
*                                                           *
*************************************************************/

function zeichne_zeile($bild, $text, $basislinie, $maxbreite)
{
    $laenge = strlen($text);

    //pro zeichen font, groesse, winkel und grauton wuerfeln
    $fonts = array();
    $groessen = array();
    $winkel = array();
    $grau = array();
    for ($i = 0; $i < $laenge; $i++) {
        $fonts[$i] = getcwd().'/fonts/font'.mt_rand(0, 9).'.ttf';
        $groessen[$i] = mt_rand(26, 36);
        $w = mt_rand(3, 12);
        $winkel[$i] = (mt_rand(1, 2) == 1) ? $w : -$w;
        $grau[$i] = mt_rand(170, 225);
    }

    //breiten messen und die schriftgroesse an die bildbreite anpassen:
    //kurze zeilen werden vergroessert, zu lange verkleinert
    $nutzbreite = $maxbreite - 40;
    $faktor = 1.0;
    for ($pass = 0; $pass < 8; $pass++) {
        $gesamt = 0;
        $breiten = array();
        for ($i = 0; $i < $laenge; $i++) {
            $groesse = (int)round($groessen[$i] * $faktor);
            if ($text[$i] == ' ') {
                $breiten[$i] = (int)round($groesse * 0.6);
            } else {
                $box = imagettfbbox($groesse, 0, $fonts[$i], $text[$i]);
                $breiten[$i] = abs($box[2] - $box[0]) + 2;
            }
            $gesamt += $breiten[$i];
        }
        if ($gesamt > $nutzbreite && $faktor > 0.45) {
            $faktor -= 0.05;
        } elseif ($gesamt < $nutzbreite * 0.55 && $faktor < 1.35) {
            $faktor += 0.15;
        } else {
            break;
        }
    }

    //zeichnen, zentriert, mit leichter ueberlappung der zeichen
    $x = (int)(($maxbreite - $gesamt) / 2) + 10;
    for ($i = 0; $i < $laenge; $i++) {
        if ($text[$i] != ' ') {
            $groesse = (int)round($groessen[$i] * $faktor);
            $farbe = imagecolorallocate($bild, $grau[$i], $grau[$i], $grau[$i]);
            imagettftext($bild, $groesse, $winkel[$i], $x, $basislinie + mt_rand(-5, 5), $farbe, $fonts[$i], $text[$i]);
        }
        $x += $breiten[$i] - mt_rand(1, 3);
    }
}

if ($aufgabe !== null) {
    //den operator auf die zeile setzen, die die zeilenlaengen besser ausbalanciert
    $worta = $zahl[$aufgabe['a'] - 1];
    $wortb = $zahl[$aufgabe['b'] - 1];
    $wortop = $operator_wort[$aufgabe['operator']];
    if (max(strlen($worta), strlen($wortop.' '.$wortb)) <= max(strlen($worta.' '.$wortop), strlen($wortb))) {
        $zeile1 = $worta;
        $zeile2 = $wortop.' '.$wortb;
    } else {
        $zeile1 = $worta.' '.$wortop;
        $zeile2 = $wortb;
    }

    //zwischenbild fuer die schrift, wird wellenfoermig verzerrt uebernommen
    $textbild = imagecreatetruecolor($ix, $iy);
    imagefilledrectangle($textbild, 0, 0, $ix - 1, $iy - 1, imagecolorallocate($textbild, 17, 17, 17));

    zeichne_zeile($textbild, $zeile1, 60 + mt_rand(-4, 4), $ix);
    zeichne_zeile($textbild, $zeile2, 130 + mt_rand(-4, 4), $ix);

    /********************************************************
    *      Wellenfoermige Verzerrung des Schriftzugs        *
    *********************************************************/
    $amplitude = mt_rand(4, 7);
    $wellenlaenge = mt_rand(120, 180);
    $phase = mt_rand(0, 628) / 100;
    for ($x = 0; $x < $ix; $x++) {
        $dy = (int)round($amplitude * sin(2 * M_PI * $x / $wellenlaenge + $phase));
        imagecopy($image, $textbild, $x, $dy, $x, 0, 1, $iy);
    }
}

/************************************************************
*                                                           *
*      Stoergrafik: dicke linien und boegen durch die       *
*      schrift, rechtecke, in wechselnden grautoenen        *
*                                                           *
*************************************************************/

for ($k = 0; $k <= 2; $k++) {
    $g = mt_rand(150, 225);
    $decolor = imagecolorallocate($image, $g, $g, $g);
    imagesetthickness($image, mt_rand(2, 3));
    imageline($image, 0, mt_rand(20, $iy - 20), $ix, mt_rand(20, $iy - 20), $decolor);
}

for ($k = 0; $k <= 1; $k++) {
    $g = mt_rand(150, 225);
    $decolor = imagecolorallocate($image, $g, $g, $g);
    imagesetthickness($image, 2);
    imagearc($image, mt_rand(0, $ix), mt_rand(0, $iy), mt_rand(100, 400), mt_rand(60, 200), 0, 360, $decolor);
}

for ($k = 0; $k <= 4; $k++) {
    $g = mt_rand(60, 200);
    $decolor = imagecolorallocate($image, $g, $g, $g);
    imagesetthickness($image, 1);
    $ra = mt_rand(0, 250);
    $rb = mt_rand(0, 60);
    imagerectangle($image, $ra, $rb, $ra + mt_rand(30, 240), $rb + mt_rand(20, 90), $decolor);
}

imagesetthickness($image, 1);

/************************************************************
*                                                           *
*      Sterne/Pixel in hellen und dunklen toenen            *
*                                                           *
*************************************************************/

for ($k = 0; $k <= 3000; $k++) {
    //helle pixel in den grautoenen der schrift
    $g = mt_rand(140, 210);
    imagesetpixel($image, mt_rand(0, $ix - 1), mt_rand(0, $iy - 1), imagecolorallocate($image, $g, $g, $g));
}

for ($k = 0; $k <= 2200; $k++) {
    //dunkle pixel zum durchsieben der schrift
    $g = mt_rand(10, 60);
    imagesetpixel($image, mt_rand(0, $ix - 1), mt_rand(0, $iy - 1), imagecolorallocate($image, $g, $g, $g));
}

/************************************************************
*                                                           *
*                       Rendering ^^                        *
*                                                           *
*************************************************************/
ImagePNG($image);
