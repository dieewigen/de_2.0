<?php
//Geisterschiff „Van der Decken“ zu Halloween (src/Model/GhostShip/GhostShipEvent.php); für Spieler keine Uhrzeiten.
//Die gestörten Schreibweisen entstehen durch Kombinationszeichen (\u{0337}, \u{0338}, \u{0335}).

$geist_lang['name']="V\u{0337}an der D\u{0338}ecken";
//Tooltip in der Sektoransicht als „Kopf&Text“ (de_fn.js)
$geist_lang['zeile_tooltip']='Van der Decken&Ein Basisschiff ohne Lebenszeichen. Der Transponder sendet eine Kennung, die seit Äonen gelöscht ist. Kein Hafen hat es je wieder aufgenommen.';

//Funkfetzen im Serverchat, einer je Station; {SEK} ist der Sektor in gestörter Schreibweise
$geist_lang['funk']=[
	'… an alle Stationen … hier ist die Van der Decken … erbitten Andockerlaubnis … [Peilung: Sektor {SEK}]',
	'… Kurs verloren … die Sterne stehen falsch … [Peilung: Sektor {SEK}]',
	'… die Mannschaft schläft … sie wacht nicht mehr auf … [Peilung: Sektor {SEK}]',
	'… kein Hafen nimmt uns auf … keiner … [Peilung: Sektor {SEK}]',
	'… wer uns sieht, wendet den Blick ab … [Peilung: Sektor {SEK}]',
	'… Logbuch, letzter Eintrag … wiederholt … wiederholt … [Peilung: Sektor {SEK}]',
	'… etwas ruft uns … aus der Tiefe … [Peilung: Sektor {SEK}]',
	'… wir fahren weiter … immer weiter … [Peilung: Sektor {SEK}]',
];
$geist_lang['funk_ziel']='… Ziel erreicht … Sektor {SEK} … die Schläfer träumen von uns …';
$geist_lang['funk_ende']='▮▮ Signal verloren ▮▮';

//Ansicht von Sektor 666
$geist_lang['s666_kopf']='▮▮ Messwerte instabil ▮▮';
$geist_lang['s666_schlaf']='Die Schläfer regen sich im Schlaf. Etwas antwortet auf ein Signal, das niemand gesendet hat.';
$geist_lang['s666_wach']='Die Schläfer lauschen. Etwas antwortet auf ein Signal, das niemand gesendet hat.';
$geist_lang['s666_angekommen']='Vor Sektor 666 treibt ein Basisschiff ohne Lebenszeichen: die Van der Decken.';
$geist_lang['s666_abspielen']='Aufgefangenen Funkspruch erneut anzeigen';

//Einblendung des Funkspruchs (js/geisterschiff.js)
$geist_lang['signal_kopf']='EINGEHENDES SIGNAL';
$geist_lang['signal_zeilen']=['Kennung: VDD-1 VAN DER DECKEN', 'Ursprung: unbekannt', "Peilung: Sektor 6\u{0337}6\u{0338}6\u{0335}"];
$geist_lang['signal_beenden']='Klicken oder Tippen schließt den Funkspruch';
$geist_lang['nachricht']=[
	'Logbuch der Van der Decken, letzter Eintrag.',
	'Wir haben den Kurs verloren. Die Sterne stehen falsch.',
	'Die Mannschaft schläft. Sie wacht nicht mehr auf.',
	'Kein Hafen nimmt uns auf. Wir fahren weiter …',
	'Wer dies empfängt: Wir sind näher, als ihr glaubt.',
];
