<?php
//Feldzug um die Vergessenen Systeme (feldzug.php, src/Model/Feldzug/FeldzugService.php, Regelwerk docs/feldzug.md)
//Platzhalter in geschweiften Klammern werden ersetzt; Zeiten nur in WT

$feldzug_lang['titel']='Feldzug';
$feldzug_lang['aus']='Der Feldzug ist auf diesem Server derzeit nicht aktiv.';
$feldzug_lang['vor_erstem_aufruf']='Der erste Aufruf zum Feldzug beginnt in etwa {WT} WT. Dann können sich die Allianzen anmelden.';
$feldzug_lang['einleitung']='Im Feldzug ringen die Allianzen um Brennpunkte in den Vergessenen Systemen. Jede Allianz verteilt in jedem Zug gleich viele Legionen, egal wie groß sie ist: Es zählt kluges Taktieren, nicht Größe. Gehaltene Brennpunkte bringen Kontrollpunkte und damit Rundensiegartefakte.';

//Kopf
$feldzug_lang['kachel_feldzug']='Feldzug';
$feldzug_lang['kachel_phase']='Phase';
$feldzug_lang['kachel_zug']='Zug';
$feldzug_lang['kachel_schritt']='Nächster Schritt';
$feldzug_lang['phase_1']='Aufruf';
$feldzug_lang['phase_2']='Kampf';
$feldzug_lang['phase_3']='beendet';
$feldzug_lang['phase_4']='ausgefallen';
$feldzug_lang['schritt_aufruf']='Start in {WT} WT';
$feldzug_lang['schritt_kampf']='Auswertung in {WT} WT';

//Aufruf
$feldzug_lang['preis_titel']='Eintrittspreis';
$feldzug_lang['preis_text']='<b>{PREIS}</b> von jedem der zehn VS-Rohstoffe (Eisen bis Octagium) aus der Kriegskasse. Er wird beim Start abgebucht und verbraucht.';
$feldzug_lang['angemeldet_titel']='Angemeldete Allianzen';
$feldzug_lang['angemeldet_keine']='Noch hat sich keine Allianz angemeldet.';
$feldzug_lang['eigene_titel']='Deine Allianz';
$feldzug_lang['eigene_angemeldet']='Deine Allianz ist angemeldet (von {NAME}).';
$feldzug_lang['eigene_nicht_angemeldet']='Deine Allianz ist noch nicht angemeldet.';
$feldzug_lang['eigene_berechtigt']='Berechtigte Mitglieder: <b>{N}</b>';
$feldzug_lang['eigene_kasse_ok']='Die Kriegskasse reicht für den Eintritt.';
$feldzug_lang['eigene_kasse_fehlt']='In der Kriegskasse fehlen noch: {LISTE}';
$feldzug_lang['eigene_posten']='Anmelden können Mitglieder mit Posten: Leader, Co-Leader, Fleet Commander, Tactical Officer und Member Officer.';
$feldzug_lang['eigene_spaet']='Du bist erst nach Beginn des Aufrufs beigetreten und zählst bei diesem Feldzug nicht als Teilnehmer.';
$feldzug_lang['knopf_anmelden']='Allianz anmelden';
$feldzug_lang['knopf_abmelden']='Anmeldung zurückziehen';
$feldzug_lang['bedingungen']='Dabei ist am Ende des Aufrufs jede angemeldete Allianz mit mindestens einem berechtigten Mitglied und genug in der Kriegskasse. Berechtigt ist, wer schon zu Beginn des Aufrufs Mitglied war, ein aktives Konto hat und nicht in Sektor 1 steht. Mit weniger als zwei Allianzen fällt der Feldzug aus.';

//Kampf
$feldzug_lang['brennpunkte_titel']='Brennpunkte';
$feldzug_lang['kp']='{N} Kontrollpunkt(e) je Zug';
$feldzug_lang['neutral']='neutral';
$feldzug_lang['rolle_festung']='Festung';
$feldzug_lang['rolle_mine']='Mine';
$feldzug_lang['rolle_werft']='Werft';
$feldzug_lang['rolle_festung_info']='Verteidigung +1 zusätzlich zum Halterbonus';
$feldzug_lang['rolle_mine_info']='Mitglieder des Halters: +10 % Ertrag der VS-Industrie';
$feldzug_lang['rolle_werft_info']='Mitglieder des Halters: −10 % Bauzeit in den VS';
$feldzug_lang['kern']='Kern';
$feldzug_lang['rest']='Noch zu verteilen: <b>{REST}</b> von {GESAMT} Legionen';
$feldzug_lang['knopf_verteilen']='Verteilung speichern';
$feldzug_lang['geaendert']='Zuletzt geändert von {NAME} im WT {WT}.';
$feldzug_lang['nur_posten']='Legionen verteilen dürfen Mitglieder mit Posten. Du siehst die Verteilung Deiner Allianz.';
$feldzug_lang['nicht_dabei']='Deine Allianz nimmt an diesem Feldzug nicht teil.';
$feldzug_lang['stehend']='Die Verteilung bleibt stehen, bis sie jemand ändert. Ausgewertet wird am Ende jedes Zuges.';

$feldzug_lang['ergebnis_titel']='Ergebnis von Zug {ZUG}';
$feldzug_lang['ergebnis_titel_leer']='Ergebnis des letzten Zuges';
$feldzug_lang['ergebnis_leer']='Noch kein Zug ausgewertet.';
$feldzug_lang['ergebnis_keine']='keine Legionen';
$feldzug_lang['ergebnis_staerke']='{TAG}: {LEG} Legionen, Stärke {ST}';
$feldzug_lang['ergebnis_neu']='neu';

$feldzug_lang['stand_titel']='Punktestand';
$feldzug_lang['stand_platz']='Platz';
$feldzug_lang['stand_allianz']='Allianz';
$feldzug_lang['stand_gehalten']='Brennpunkte';
$feldzug_lang['stand_kp']='Kontrollpunkte';

//Kriegskasse
$feldzug_lang['kasse_titel']='Kriegskasse';
$feldzug_lang['kasse_text']='Jedes Mitglied kann VS-Rohstoffe aus dem eigenen Lager in die Kriegskasse spenden. Eine Spende lässt sich nicht zurückholen.';
$feldzug_lang['kasse_rohstoff']='Rohstoff';
$feldzug_lang['kasse_bestand']='Kasse';
$feldzug_lang['kasse_lager']='Dein Lager';
$feldzug_lang['kasse_spende']='Spende';
$feldzug_lang['knopf_spenden']='Spenden';

//Historie und Regeln
$feldzug_lang['historie_titel']='Feldzüge dieser Runde';
$feldzug_lang['historie_zeile']='Feldzug {NR}: <b>{TAG}</b> mit {KP} Kontrollpunkten';
$feldzug_lang['historie_leer']='In dieser Runde wurde noch kein Feldzug entschieden.';
$feldzug_lang['regeln_titel']='So funktioniert der Feldzug';
$feldzug_lang['regeln']='<p>Ein Feldzug beginnt mit dem <b>Aufruf</b> ({AUFRUF} WT): Allianzen melden sich an und füllen ihre Kriegskasse. Danach folgen <b>{ZUEGE} Züge</b> Kampf zu je {ZUG} WT, dann beginnt sofort der nächste Aufruf.</p>
<p>Es gibt so viele Brennpunkte wie Teilnehmer plus {EXTRA}: einen Kern (3 Kontrollpunkte je Zug), einige wichtige (2) und den Rest (1). Eine Festung gibt ihrem Halter +1 Verteidigung zusätzlich, die Mine den Mitgliedern des Halters +10 % VS-Industrie, die Werft −10 % Bauzeit in den VS.</p>
<p>Jede Allianz hat in jedem Zug <b>{LEGIONEN} Legionen</b>. Auf jedem Brennpunkt zählen ihre Legionen als Stärke, der bisherige Halter bekommt +1 (auf einer Festung +2). Die stärkste Allianz hält den Brennpunkt; bei Gleichstand bleibt der Halter, ein neutraler Brennpunkt bleibt neutral. Legionen gehen nie verloren.</p>
<p>Jeder gehaltene Brennpunkt bringt in jedem Zug seine Kontrollpunkte, sie werden sofort als Rundensiegartefakte gutgeschrieben ({QP} je Kontrollpunkt). Wer am Ende die meisten Kontrollpunkte hat, gewinnt den Feldzug. Die Allianz mit den meisten Siegen der Runde stellt die Feldherren: Alle Teilnehmer ihrer gewonnenen Feldzüge erhalten einen dauerhaften Titel.</p>';

//Meldungen nach Aktionen
$feldzug_lang['ok_angemeldet']='Deine Allianz ist für den Feldzug angemeldet.';
$feldzug_lang['ok_abgemeldet']='Die Anmeldung wurde zurückgezogen.';
$feldzug_lang['ok_verteilt']='Die Verteilung der Legionen wurde gespeichert.';
$feldzug_lang['ok_gespendet']='Die Spende ist in der Kriegskasse.';
$feldzug_lang['fehler_token']='Die Aktion konnte nicht ausgeführt werden. Bitte versuche es erneut.';
$feldzug_lang['fehler_sperre']='Die Aktion konnte nicht ausgeführt werden, es läuft bereits eine andere Aktion.';
$feldzug_lang['fehler_tick']='Gerade läuft ein Wirtschaftstick. Bitte versuche es gleich noch einmal.';
$feldzug_lang['fehler_aus']='Der Feldzug ist auf diesem Server derzeit nicht aktiv.';
$feldzug_lang['fehler_kein_aufruf']='Anmelden geht nur während des Aufrufs.';
$feldzug_lang['fehler_kein_kampf']='Legionen verteilen geht nur während der Kampfphase.';
$feldzug_lang['fehler_posten']='Dafür brauchst Du einen Posten in Deiner Allianz.';
$feldzug_lang['fehler_nicht_dabei']='Deine Allianz nimmt an diesem Feldzug nicht teil.';
$feldzug_lang['fehler_verteilung']='Die Verteilung ist ungültig.';
$feldzug_lang['fehler_zu_viele']='Das sind mehr Legionen, als Deine Allianz hat.';
$feldzug_lang['fehler_keine_allianz']='Du bist in keiner Allianz.';
$feldzug_lang['fehler_zu_wenig']='So viel hast Du nicht im Lager.';
$feldzug_lang['fehler_teilweise']='Ein Teil wurde gespendet, für den Rest hast Du nicht genug im Lager.';
$feldzug_lang['fehler_keine_menge']='Bitte gib eine Menge ein.';

//Übersicht und V-Systeme
$feldzug_lang['ov_aufruf']='<b><a href="feldzug.php">Aufruf zum Feldzug {NR}</a></b>: Anmeldung noch {WT} WT';
$feldzug_lang['ov_kampf']='<b><a href="feldzug.php">Feldzug {NR}</a></b>: Zug {ZUG} von {ZUEGE}, Auswertung in {WT} WT';
$feldzug_lang['ov_eigene']=', Deine Allianz Platz {PLATZ} ({KP} Kontrollpunkte)';
$feldzug_lang['ov_angemeldet']=', Deine Allianz ist angemeldet';
$feldzug_lang['vs_link']='Zum Feldzug';
$feldzug_lang['vs_hinweis']='Brennpunkte des Feldzugs sind markiert, alle stehen auf der Feldzugseite.';
$feldzug_lang['vs_boni_mine']='Feldzug: Mine, Industrie +10 %';
$feldzug_lang['vs_boni_werft']='Feldzug: Werft, Bauzeit −10 %';
$feldzug_lang['chip']='Brennpunkt';

//Chat (Server und Allianz)
$feldzug_lang['chat_aufruf']='<b>Aufruf zum Feldzug {NR}!</b> Allianzen können sich in den nächsten {WT} WT anmelden. Eintrittspreis: {PREIS} von jedem VS-Rohstoff.';
$feldzug_lang['chat_ausgefallen']='Feldzug {NR} fällt aus: Nur {N} Allianz(en) erfüllten die Voraussetzungen. Ein neuer Aufruf beginnt.';
$feldzug_lang['chat_start']='<b>Feldzug {NR} beginnt!</b> Es kämpfen {ALLYS} um {N} Brennpunkte, {ZUEGE} Züge lang.';
$feldzug_lang['chat_zug']='Feldzug {NR}, Zug {ZUG} von {ZUEGE}: ';
$feldzug_lang['chat_zug_keine']='keine Besitzwechsel.';
$feldzug_lang['wechsel']='{BP} an {TAG}';
$feldzug_lang['chat_sieg']='<b>{TAG} gewinnt Feldzug {NR}</b> mit {KP} Kontrollpunkten!';
$feldzug_lang['chat_feldherren']='<b>{TAG} stellt die Feldherren dieser Runde</b> ({SIEGE} gewonnene Feldzüge).';
$feldzug_lang['ally_zug']='Feldzug, Zug {ZUG}:';
$feldzug_lang['ally_erobert']='Wir halten jetzt {BP}.';
$feldzug_lang['ally_verloren']='{BP} ist an {TAG} gefallen.';
