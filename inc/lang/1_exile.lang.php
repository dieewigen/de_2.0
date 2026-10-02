<?php
//Exil & Heimkehr: Texte von Fluxurion für Spieler, die in Sektor 1 geparkt wurden
//Platzhalter werden in geschweiften Klammern ersetzt, alle Werte werden vorher escaped

//Allgemein
$exile_lang['absender']='Fluxurion der Berater';
$exile_lang['anrede']='Kommandant {NAME},';
$exile_lang['gruss']='Fluxurion<br>Berater Deines Systems';
$exile_lang['button']='Zurück ins Kommando';

//Betreffzeilen
$exile_lang['betreff_vorab']='Die Ewigen ({TAG}): Dein System wird morgen nach Sektor 1 verlegt';
$exile_lang['betreff_bericht']='Die Ewigen ({TAG}): Lagebericht aus Sektor 1';
$exile_lang['betreff_neue_runde']='Die Ewigen ({TAG}): Eine neue Runde hat begonnen';

//Überschriften
$exile_lang['titel_vorab']='Letzte Meldung vor der Verlegung';
$exile_lang['titel_bericht1']='Lagebericht aus Sektor 1';
$exile_lang['titel_bericht2']='Zweiter Lagebericht aus Sektor 1';
$exile_lang['titel_bericht3']='Letzter Lagebericht aus Sektor 1';
$exile_lang['titel_neue_runde']='Eine neue Runde hat begonnen';

//Einleitungen
$exile_lang['intro_vorab']='seit {DAYS} Tagen erreichen mich keine Befehle von Dir. Ich halte die Stellung, doch ohne Kommandant kann ich Dein System nicht auf Dauer im Spielgeschehen lassen.<br><br>Meldest Du Dich nicht innerhalb der nächsten 24 Stunden, verlege ich es in den Schutzraum von Sektor 1. Dort ist es vor Angriffen sicher und wird nicht gelöscht. Die Produktion ruht dann allerdings, bis Du zurückkehrst.';
$exile_lang['intro_vorab_kollektoren']='Außerdem gebe ich von Deinen {COL} Kollektoren nach und nach alle über 25 an aktive Kommandanten außerhalb von Sektor 1 ab.';
$exile_lang['outro_vorab']='Ein Login genügt, und alles bleibt, wie es ist.';

$exile_lang['intro_bericht1']='wie angekündigt habe ich Dein System vor {DAYS} Tagen in den Schutzraum von Sektor 1 verlegt. Hier ist mein erster Lagebericht.';
$exile_lang['intro_bericht2']='seit {DAYS} Tagen ruht Dein System in Sektor 1. Draußen dreht sich das Universum weiter. Hier ist, was ich beobachte.';
$exile_lang['intro_bericht3']='Dein System ruht seit {DAYS} Tagen in Sektor 1. Dies ist mein letzter regelmäßiger Lagebericht.';
$exile_lang['intro_neue_runde']='am {DATE} hat eine neue Runde begonnen. Alle Kommandanten haben wieder bei null angefangen, auch jene, die Dich zuletzt hinter sich gelassen haben. Wer jetzt einsteigt, ist noch mitten im Rennen.';

//Abschlüsse
$exile_lang['outro_bericht1']='Ich halte die Stellung, bis Du zurückkehrst.';
$exile_lang['outro_bericht2']='Ich halte weiter die Stellung.';
$exile_lang['outro_bericht3']='Danach behellige ich Dich nicht mehr mit regelmäßigen Berichten. Erst wenn eine neue Runde beginnt, melde ich mich noch einmal. Bis dahin bleibt Dein System sicher in Sektor 1.';
$exile_lang['outro_neue_runde']='Dein System liegt bereit. Es fehlt nur noch Dein Befehl.';

//Inhaltsblöcke
$exile_lang['block_lage']='Dein System liegt sicher in Sektor 1. Niemand kann es angreifen, und es wird nicht gelöscht. Ohne Deine Befehle ruht jedoch die Produktion.';
$exile_lang['block_verlust']='{LOST} Kollektoren haben Dein System seit der Verlegung verlassen und arbeiten jetzt für aktive Kommandanten. Dir sind {COL} geblieben, und unter 25 gebe ich keinen mehr ab.';
$exile_lang['block_sektor']='In Deinem früheren Sektor {SECTOR} sind noch {ACTIVE} Kommandanten aktiv.';
$exile_lang['block_sektor_leer']='Dein früherer Sektor {SECTOR} ist inzwischen verwaist.';
$exile_lang['block_allianz']='Deine Allianz [{ALLY}] führt Dich weiterhin als Mitglied.';
$exile_lang['block_runde']='Die aktuelle Runde läuft seit dem {DATE} und steht bei {WT} Wirtschaftsticks. {ACTIVE} Kommandanten sind im Spielgeschehen aktiv.';
$exile_lang['block_aktive']='Die Runde steht bei {WT} Wirtschaftsticks, {ACTIVE} Kommandanten sind bereits im Spielgeschehen aktiv.';
$exile_lang['block_eh_bald']='Der Kampf um den Titel des Erhabenen beginnt voraussichtlich am {EHDATE}.';
$exile_lang['block_eh_laeuft']='Der Kampf um den Titel des Erhabenen ist bereits entbrannt.';
$exile_lang['block_reserve']='Eine Notbesatzung hält Deinen planetaren Grundertrag zu einem Fünftel am Laufen. In Deiner Exilreserve liegen inzwischen <b>{M} Multiplex</b> und <b>{D} Dyharra</b>. Ich übergebe sie Dir, sobald Du zurückkehrst.';
$exile_lang['block_schritt_umzug']='Sobald Du Dich einloggst, verlege ich Dein System mit dem nächsten Wirtschaftstick zurück in einen regulären Sektor.';
$exile_lang['block_schritt_sektor1']='Wenn Du zurückkehrst, bleibt Dein System zunächst im Schutz von Sektor 1. Mit 10 Kollektoren oder 5.000.000 Punkten wechselt es ins reguläre Spielgeschehen.';

//Fußzeilen
$exile_lang['fuss_vorab']='Du erhältst diese Nachricht, weil Dein Konto auf dem Server {SERVER} ({TAG}) seit {DAYS} Tagen nicht genutzt wurde.';
$exile_lang['fuss_vorab_optionen']='Lageberichte aus dem Exil kannst Du in den Optionen des Spiels abbestellen.';
$exile_lang['fuss_bericht']='Du erhältst diese Lageberichte, weil Dein Konto auf dem Server {SERVER} ({TAG}) in Sektor 1 ruht.';
$exile_lang['fuss_abmelden']='Keine Lageberichte mehr erhalten';

//Heimkehr im Spiel
$exile_lang['news_heimkehr']='Fluxurion: Willkommen zurück aus dem Exil, Kommandant.';
$exile_lang['news_heimkehr_reserve']='Fluxurion: Willkommen zurück aus dem Exil, Kommandant. Deine Exilreserve von {M} Multiplex und {D} Dyharra wurde Deinem Lager gutgeschrieben.';
$exile_lang['news_allianz']='{NAME} ist aus dem Exil in Sektor 1 zurückgekehrt.';
$exile_lang['kommentar']='Rückkehr aus dem Exil, Exilreserve: {M} Multiplex, {D} Dyharra';

$exile_lang['box_titel']='Fluxurion der Berater';
$exile_lang['box_abwesend']='Willkommen zurück, Kommandant. Du warst {DAYS} Tage fort, und ich habe Dein System in dieser Zeit im Schutz von Sektor 1 gehalten.';
$exile_lang['box_reserve']='Deine Exilreserve ist eingetroffen: <b>{M} Multiplex</b> und <b>{D} Dyharra</b> wurden Deinem Lager gutgeschrieben.';
$exile_lang['box_verlust']='{LOST} Kollektoren haben Dein System während Deiner Abwesenheit verlassen.';
$exile_lang['box_schritt_umzug']='Mit dem nächsten Wirtschaftstick verlege ich Dein System in einen regulären Sektor. Dabei wird die Verbindung kurz unterbrochen. Melde Dich danach einfach erneut an.';
$exile_lang['box_schritt_sektor1']='Dein System bleibt vorerst im Schutz von Sektor 1. Mit 10 Kollektoren oder 5.000.000 Punkten wechselt es ins reguläre Spielgeschehen. Ab dann kannst Du angreifen und angegriffen werden.';
$exile_lang['box_rundenpause']='Willkommen zurück, Kommandant. Die Runde ist beendet. Deine Exilreserve übergebe ich Dir, sobald die neue Runde begonnen hat.';
$exile_lang['box_angekommen']='Die Verlegung ist abgeschlossen. Willkommen in Sektor {SECTOR}, Kommandant. Ab jetzt liegt das Kommando wieder bei Dir.';

//Optionen
$exile_lang['option_lageberichte']='Lageberichte von Fluxurion per E-Mail, wenn das Konto in Sektor 1 ruht';

//Abmeldeseite
$exile_lang['optout_titel']='Lageberichte abbestellen';
$exile_lang['optout_frage']='Möchtest Du keine Lageberichte von Fluxurion mehr per E-Mail erhalten?';
$exile_lang['optout_button']='Abbestellen';
$exile_lang['optout_fertig']='Erledigt. Du erhältst keine Lageberichte mehr. In den Optionen des Spiels kannst Du sie jederzeit wieder einschalten.';
$exile_lang['optout_ungueltig']='Dieser Link ist ungültig.';
