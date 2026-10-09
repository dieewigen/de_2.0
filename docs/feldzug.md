# Feldzug um die Vergessenen Systeme – Regelwerk

Stand: umgesetzt am 08.10.2026 (Entwurf vom 05.10.2026, überarbeitet zu Brennpunkten statt Karte). Spielerfassung: `docs/feldzug.html`. Freigeschaltet wird der Feldzug mit `sv_feldzug_aktiv` zu einem Rundenstart, weil er Questpunkte vergibt.

## Entscheidungen

- Gekämpft wird mit abstrakten **Legionen**, nicht mit echten Flotten.
- **Brennpunkte statt Karte:** Gekämpft wird um einzelne Vergessene Systeme, die Brennpunkte. Nachbarschaft und Wege auf der Karte spielen keine Rolle. Grund: Die meisten spielen mobil, und dort gibt es keine Karte, nur die Liste der V-Systeme. Die erste Fassung mit Konfliktzone, Engpässen und Versorgung war zu kompliziert.
- Der Eintrittspreis wird in **VS-Rohstoffen** bezahlt und dabei verbraucht. Er steigt im Laufe der Runde mit der VS-Produktion. Grundpreis am Rundenanfang: 2.000 auf Server 1, 600 auf Server 2.
- **Alle Allianzen** können teilnehmen, auch Einzelspieler mit eigener Allianz. Eine Obergrenze gibt es nicht.
- Titel gibt es wie bisher **nur für Spieler**. Allianzen werden am Rundenende gelöscht, die Historie speichert deshalb Kürzel und Namen als Text.
- Battlegrounds, ARES und HEPHAISTOS bleiben unverändert.
- Es wird das vollständige Regelwerk umgesetzt, keine Minimalversion.
- Belohnungen: Ansehen, Vorteile durch gehaltene Brennpunkte, Questpunkte. Keine Kollektoren, keine Allianzartefakte, kein Quantenglimmer.
- Alle Mitglieder sehen alles. Legionen verteilen nur Mitglieder mit Posten. Spenden dürfen alle Mitglieder.
- **Allianzwappen vorerst nicht.** Halter werden mit ihrem Allianzkürzel angezeigt.

Bei der Umsetzung am 08.10.2026 entschieden:
- **Questpunkte:** Die Kontrollpunkte fließen voll in `de_allys.questpoints`. Erfolgreiche Allianzen erreichen damit auch die Stufen des täglichen Allianzbonus schneller. Das ist bewusst so.
- **Markierung:** Brennpunkte werden in der Liste der V-Systeme und auf der Desktop-Karte nur markiert, wenn der Spieler das System dort ohnehin sieht. Alle Brennpunkte stehen auf der Feldzugseite.
- **Sparsame Meldungen:** Die Sammelmeldung je Zug geht nur in den Serverchat im Spiel. An Discord gehen nur Aufruf, Start, Ausfall, Sieg und Feldherren. Der Allianzchat bekommt nur bei gewonnenem oder verlorenem Brennpunkt eine Meldung.
- **Einstieg:** Reiter „Feldzug“ in der Allianz-Navigation (nur bei eingeschaltetem Feldzug), Zeile auf der Übersicht, Link in der Liste der V-Systeme. Das Menü bleibt unverändert.
- **Laufender Feldzug zum Rundenende:** Er hat keinen Sieger, nur abgeschlossene Feldzüge zählen für die Feldherren.
- **Mine und Werft** wirken nur während der Kampfphase.
- **Gelöschte Allianz:** Ihre Brennpunkte werden beim nächsten Zug neutral, ihre Verteilung entfällt, ihre Punkte bleiben im Punktestand.
- **„Seit Beginn des Aufrufs“:** Ein Beitrittsdatum gibt es nicht. Beim Aufruf werden alle Mitglieder festgehalten (`de_feldzug_mitglied`). Beim Kampfstart ist berechtigt, wer festgehalten wurde und noch in derselben Allianz ist.
- **Produktionsmedian:** aus `de_user_storage.item_wt_change` (Items 3–12) aktiver menschlicher Spieler außerhalb von Sektor 1, deren Summe über 0 liegt.

## 1. Grundsätze

Der Feldzug soll kluges Taktieren belohnen, nicht Aktivität oder Größe:

- **Gleiches Budget:** Jede Allianz hat gleich viele Legionen, egal wie viele Mitglieder sie hat.
- **Gleichzeitig und verdeckt:** Alle Verteilungen werden zugleich ausgewertet. Niemand sieht die aktuelle Verteilung der anderen.
- **Offene Ergebnisse:** Nach jedem Zug sieht jeder, wie alle verteilt hatten. Die Taktik liegt im Lesen des Gegners und im Bluffen.
- **Kein Zufall in der Auswertung:** Gleiche Verteilungen führen immer zum selben Ergebnis.
- **Lange Züge:** Ein Zug dauert 480 WT, auf Server 2 144 WT. Die Verteilung bleibt stehen, bis sie jemand ändert. Wer seltener vorbeischaut, verliert nichts.
- **Mobil zuerst:** Ein Zug ist eine Liste mit Plus und Minus auf einem Bildschirm. Eine Karte braucht man nicht.

Prüfung gegen die Vorgaben für Spielmechanik:

| Vorgabe | Umsetzung |
|---|---|
| PvP bleibt | Der Feldzug kommt dazu und ändert am normalen Kampf nichts. |
| Keine Kollektoren aus dem Nichts | Es gibt keine Kollektoren und nichts, was sich in Kollektoren umwandeln lässt (Allianzartefakte, Quantenglimmer). |
| Keine Echtzeit | Züge laufen in Wirtschaftsticks (WT). |
| Nicht schiebbar | Das Budget ist ein fester Wert pro Allianz. Der Eintrittspreis wird verbraucht, es gibt keinen Topf. Siehe Abschnitt 10. |

## 2. Ablauf in Kürze

1. Ein **Aufruf** beginnt. Allianzen melden sich an und füllen ihre Kriegskasse.
2. Zum Start werden einige Vergessene Systeme zu **Brennpunkten**, so viele wie Teilnehmer plus 3.
3. In **12 Zügen** verteilt jede Allianz ihre 12 Legionen auf die Brennpunkte.
4. Wer auf einem Brennpunkt am stärksten ist, hält ihn. Gehaltene Brennpunkte bringen **Kontrollpunkte**. Diese werden sofort in Questpunkte für den Allianz-Rundensieg umgerechnet.
5. Wer am Ende die meisten Kontrollpunkte hat, gewinnt den Feldzug. Danach beginnt der nächste Aufruf mit neuen Brennpunkten.

## 3. Ablauf über die Runde

- **Zuglänge:** einstellbar pro Server, weil die Server unterschiedlich schnell laufen. Der Wert ist so gewählt, dass ein Zug auf beiden Servern ähnlich lange dauert.
  - Server 1: 480 WT
  - Server 2: 144 WT
- **Erster Aufruf:** nach 7 Zügen Rundenlaufzeit, also nach 3.360 WT auf Server 1 und 1.008 WT auf Server 2. Bis dahin haben sich die Allianzen nach dem Reset neu gefunden.
- **Ein Feldzug** besteht aus 2 Zügen Aufruf und 12 Zügen Kampf, insgesamt 14 Züge, also 6.720 WT auf Server 1 und 2.016 WT auf Server 2. Danach beginnt sofort der nächste Aufruf. Bei der heutigen Rundenlänge ergibt das etwa 5 Feldzüge pro Runde auf Server 1 und etwa 8 auf Server 2.
- **Rundenende:** Ein laufender Feldzug endet mit der Runde. Seine Questpunkte sind schon gutgeschrieben, weil sie pro Zug vergeben werden. Danach werden die Feldherren der Runde ermittelt (Abschnitt 8).
- **Ewige Runde:** Feldzüge laufen ohne Ende weiter. Da es kein Rundenende gibt, entfällt der Feldherren-Titel.

## 4. Brennpunkte

### Anzahl und Auswahl

- **Anzahl:** Teilnehmer + 3. Bei 2 Allianzen sind es 5 Brennpunkte, bei 6 Allianzen 9. So gibt es immer mehr Brennpunkte als Allianzen, und nicht jeder Brennpunkt kann stark besetzt werden.
- **Auswahl:** Zum Start der Kampfphase werden zufällig gewöhnliche Vergessene Systeme gewählt. Battlegrounds und Sondersysteme sind ausgeschlossen. Der Brennpunkt trägt den Namen des Systems.
- Bei jedem Feldzug gibt es neue Brennpunkte. Das sorgt für Abwechslung.

### Werte

| Art | Anzahl | Kontrollpunkte pro Zug |
|---|---|---|
| Kern | 1 | 3 |
| Wichtiger Brennpunkt | ein Drittel der übrigen, abgerundet | 2 |
| Brennpunkt | der Rest | 1 |

Beispiele:
- 5 Brennpunkte: 1 × 3, 1 × 2, 3 × 1, zusammen 8 Kontrollpunkte pro Zug.
- 9 Brennpunkte: 1 × 3, 2 × 2, 6 × 1, zusammen 13 Kontrollpunkte pro Zug.

### Rollen

Einige Brennpunkte außer dem Kern bekommen zusätzlich eine Rolle:

| Rolle | Anzahl | Wirkung für den Halter |
|---|---|---|
| Festung | 1, ab 8 Brennpunkten 2 | Verteidigung +1 zusätzlich zum Halterbonus |
| Mine | 1 | Alle Mitglieder: +10 % Ertrag der VS-Industrie |
| Werft | 1 | Alle Mitglieder: −10 % Bauzeit in den VS |

- Am Anfang sind alle Brennpunkte **neutral**. Neutrale Brennpunkte haben keine Verteidigung.
- **Sichtbarkeit:** Die Feldzugseite listet alle Brennpunkte mit Wert, Rolle und Kürzel des Halters, für alle Spieler. In der Liste der V-Systeme und auf der Desktop-Karte sind Brennpunkte markiert, mit dem Kürzel des Halters. Zum Bauen muss man ein System weiterhin erkunden, am Bauen und an den Gebäuden ändert der Feldzug nichts.
- Nach dem Ende eines Feldzugs bleiben die Kürzel der letzten Halter stehen, bis der nächste Feldzug beginnt.

## 5. Teilnahme

### Voraussetzungen

Eine Allianz kann teilnehmen, wenn am Ende des Aufrufs drei Dinge erfüllt sind:

1. Ein Mitglied mit Posten hat die Allianz angemeldet.
2. Die Allianz hat mindestens **1 berechtigtes Mitglied**. Einzelspieler mit eigener Allianz können also mitmachen. Berechtigt ist, wer ein Mensch ist (kein NPC), ein aktives Konto hat, in einem Sektor größer als 1 steht und **seit Beginn des Aufrufs** Mitglied der Allianz ist. Eine Allianz, die erst während des Aufrufs gegründet wird, kann deshalb erst beim nächsten Feldzug mitmachen.
3. Die **Kriegskasse** enthält den Eintrittspreis.

### Kriegskasse und Eintrittspreis

- Die Kriegskasse ist das Allianzlager, das es schon gibt. Aus dem Allianzlager gibt es keine Auszahlung, damit lassen sich darüber keine Rohstoffe zwischen Spielern verschieben.
- **Jedes Mitglied** kann VS-Rohstoffe aus dem eigenen Lager in die Kriegskasse spenden. Eine Spende lässt sich nicht zurückholen.
- **Eintrittspreis:** pro Feldzug von jeder der zehn Rohstoffarten (Eisen bis Octagium) derselbe Betrag. Er steigt im Laufe der Runde mit der VS-Produktion:
  - **Grundpreis** beim ersten Aufruf der Runde: 2.000 auf Server 1, 600 auf Server 2.
  - **Wachstum:** Beim ersten Aufruf wird die mittlere VS-Produktion pro WT gemessen. Gemeint ist der Median über alle aktiven menschlichen Spieler, die überhaupt produzieren, summiert über alle zehn Rohstoffarten. Bei jedem weiteren Aufruf wird erneut gemessen.
  - **Eintrittspreis = Grundpreis × (Produktion jetzt ÷ Produktion beim ersten Aufruf)**, mindestens der Grundpreis, gerundet auf volle Hundert.
  - Produziert beim ersten Aufruf noch niemand, gilt der Grundpreis, bis es einen ersten Messwert gibt.
  - Der Preis wird zu Beginn des Aufrufs festgelegt und angezeigt. Er ändert sich während des Aufrufs nicht.
- Der Median ist Absicht: Einzelne sehr große oder sehr kleine Produzenten verschieben ihn kaum. Wer den Preis drücken wollte, müsste die eigene Produktion senken, und das lohnt sich nicht.
- Weil alle zehn Arten verlangt werden, muss die Allianz zusammenlegen. Felder sind zufällig verteilt, kaum ein Spieler hat alle Arten.
- **Einzelspieler** zahlen denselben Preis und bekommen dieselben 12 Legionen. Einen Rabatt für kleine Allianzen gibt es bewusst nicht. Sonst würde es sich lohnen, eine große Allianz für den Feldzug in viele kleine aufzuteilen, denn jede bekäme ein eigenes Budget.
- Der Eintrittspreis wird beim Start der Kampfphase abgebucht und verbraucht. Was darüber hinaus in der Kasse liegt, bleibt für den nächsten Feldzug.

### Teilnehmerzahl

- Ein Feldzug braucht **mindestens 2** Allianzen. Jede Allianz, die die Voraussetzungen erfüllt, ist dabei. Eine Obergrenze gibt es nicht, die Zahl der Brennpunkte wächst mit.
- Mit weniger als 2 Allianzen fällt der Feldzug aus. Es wird nichts abgebucht, und ein neuer Aufruf beginnt.
- Die berechtigten Mitglieder beim Start werden als **Teilnehmer** gespeichert. Das ist wichtig für den Titel.

## 6. Legionen verteilen

- Jede Allianz hat **12 Legionen pro Zug**. Sie verteilt sie beliebig auf die Brennpunkte, auch alle auf einen. Unverteilte Legionen bleiben ungenutzt.
- **Verteilen** dürfen alle Mitglieder mit Posten: Leader, Co-Leader, Fleet Commander, Tactical Officer und Member Officer.
- Es gibt keine Befehlsarten. Legionen auf einem eigenen Brennpunkt verteidigen ihn, Legionen auf einem fremden oder neutralen Brennpunkt greifen ihn an.
- **Stehende Verteilung:** Die Verteilung gilt, bis jemand sie ändert. Wer einen Zug verpasst, kämpft mit der alten Verteilung weiter.
- **Frist:** Ausgewertet wird im WT, mit dem der Zug endet. Bis dahin lässt sich die Verteilung beliebig ändern. Die Seite zeigt, wie viele WT bis zur Auswertung fehlen. Zeitangaben gibt es wie überall in DE nur in WT, nicht in Tagen oder Uhrzeiten.
- Bei jeder Änderung wird gespeichert, wer sie wann gemacht hat. Alle Mitglieder sehen das.
- Allianzen können sich absprechen, aber ihre Legionen nicht zusammenlegen. Jede Allianz kämpft auf jedem Brennpunkt für sich.

## 7. Auswertung eines Zuges

Für jeden Brennpunkt gleichzeitig:

1. **Stärke jeder Allianz** = ihre Legionen auf diesem Brennpunkt.
2. **Halterbonus:** Der bisherige Halter bekommt +1, auf einer Festung +2. Das gilt auch, wenn er keine Legionen dort hat.
3. **Ergebnis:** Die stärkste Allianz hält den Brennpunkt.
   - Bei Gleichstand bleibt der bisherige Halter, auch wenn zwei andere Allianzen gleichauf über ihm liegen.
   - Ein neutraler Brennpunkt bleibt bei Gleichstand neutral.
   - Ein neutraler Brennpunkt ohne Legionen bleibt neutral.
4. **Punkte:** Jeder gehaltene Brennpunkt bringt seinem Halter die Kontrollpunkte seines Wertes.
5. **Ergebnis veröffentlichen:** Alle Spieler sehen für jeden Brennpunkt, welche Allianz wie viele Legionen gesetzt hatte und wer ihn jetzt hält. Der allgemeine Chat bekommt **eine** Sammelmeldung zu allen Besitzwechseln, der Allianzchat eine Kurzmeldung zum eigenen Ergebnis.

Legionen gehen nicht verloren, im nächsten Zug sind wieder 12 da. Verloren geht nur ein Brennpunkt.

### Beispiel

Zwei Allianzen, 5 Brennpunkte, beide verteilen ihre 12 Legionen:

| Brennpunkt | Wert | Halter vorher | A | B | Ergebnis |
|---|---|---|---|---|---|
| Nexus-7 (Kern) | 3 | A | 4, mit Halterbonus 5 | 5 | A hält, Gleichstand geht an den Halter |
| Arvo (Mine) | 2 | neutral | 4 | 0 | A erobert |
| Kelm (Festung) | 1 | B | 0 | 3, mit Festung 5 | B hält |
| Sirr | 1 | neutral | 4 | 2 | A erobert |
| Duun (Werft) | 1 | neutral | 0 | 2 | B erobert |

A bekommt 3 + 2 + 1 = 6 Kontrollpunkte, B bekommt 1 + 1 = 2. Hätte B eine Legion von Duun auf Nexus-7 verlegt, hätte B den Kern erobert und Duun trotzdem gehalten: 5 zu 3 für B.

## 8. Wertung und Belohnungen

### Questpunkte, pro Zug

- Die Kontrollpunkte jedes Zuges werden sofort in Questpunkte umgerechnet:
  **Questpunkte = Kontrollpunkte × Zuglänge in WT ÷ 16**
  - Server 1: × 30
  - Server 2: × 9
- So bringt ein gut gehaltener Anteil von etwa 5 Kontrollpunkten pro 480 WT so viel wie eine erfüllte Allianzaufgabe, rund 150 Questpunkte, auf beiden Servern gleich.
- Questpunkte entscheiden über den **Allianz-Rundensieg**, der Feldzug wird damit zu einem Hauptweg dorthin.

### Vorteile, solange man hält

- **Mine:** Alle Mitglieder bekommen +10 % Ertrag der VS-Industrie. Das wird zu den bestehenden Boni addiert.
- **Werft:** Alle Mitglieder bekommen −10 % Bauzeit in den VS. Das fällt unter die bestehende Obergrenze von 50 % für VS-Bauzeitboni.
- Beide Vorteile gelten für jeden Spieler gleich. Zusätzliche Konten helfen also keinem anderen Konto.

### Feldzugsieg

- Sieger ist die Allianz mit den meisten Kontrollpunkten über den ganzen Feldzug. Bei Gleichstand gewinnt, wer am Ende mehr Brennpunkte hält, danach entscheidet das Los.
- Der Sieg wird im allgemeinen Chat gemeldet und in die Feldzug-Historie eingetragen. Die Rangliste zeigt die Historie der Runde.

### Feldherren der Runde

- Am Rundenende werden die Feldherren ermittelt: Das ist die Allianz mit den meisten Feldzugsiegen in der Runde. Bei Gleichstand zählt die Summe der Kontrollpunkte.
- **Titel gibt es wie bisher nur für Spieler.** Alle **Teilnehmer** der Feldherren-Allianz aus gewonnenen Feldzügen bekommen einen dauerhaften Titel in ihrem Account: „[Servertag] FELDHERR/IN – Runde N“. Das funktioniert wie beim Erhabenen, über die owner_id in der Accountverwaltung. Die Allianz selbst bekommt keinen Titel.
- **Zeitpunkt:** im Rundenende-Block von wt.php, direkt nach dem Titel für den Erhabenen und vor dem automatischen Reset. Der Reset löscht die Allianzen.
- **Historie:** Weil Allianzen am Rundenende gelöscht werden, speichert die Feldzug-Historie Kürzel und Namen als Text, ohne Verweis auf die Allianz. Rundennummer und Feldherren erscheinen in der Rundenhistorie der Rangliste, so wie heute schon die beste Allianz.

### Bewusst nicht als Belohnung

- Kollektoren, Allianzartefakte, Quantenglimmer: Der Kollektorensynthetisierer macht aus den beiden letzten Kollektoren für jedes Mitglied.
- Auszahlungen pro Mitglied: Die Gesamtsumme würde mit der Mitgliederzahl wachsen.
- Ein Topf aus den Eintrittspreisen: Eine Strohmann-Allianz könnte absichtlich verlieren und so Rohstoffe verschieben.

## 9. Sichtbarkeit und Rechte

| | Alle Spieler | Mitglieder einer teilnehmenden Allianz | Mitglieder mit Posten |
|---|---|---|---|
| Brennpunkte mit Wert, Rolle und Halter | sehen | sehen | sehen |
| Ergebnisse aller vergangenen Züge, Punktestand, Historie | sehen | sehen | sehen |
| Aktuelle Verteilung der eigenen Allianz | – | sehen | sehen und ändern |
| VS-Rohstoffe in die Kriegskasse spenden | – | ja | ja |
| Allianz zum Feldzug anmelden | – | – | ja |

Weil alle Mitglieder die aktuelle Verteilung sehen, kann ein Mitglied sie an Gegner verraten. Das ist gewollt: Vertrauen in der Allianz soll etwas wert sein.

## 10. Schutz gegen Missbrauch

| Weg | Gegenmaßnahme |
|---|---|
| Viele Mitglieder oder Zweitkonten für mehr Stärke | Das Budget ist ein fester Wert pro Allianz. |
| Strohmann-Allianz, die heimlich mitspielt | Eine Allianz zählt nur, wenn ihr Mitglied schon vor dem Aufruf dabei war, spontane Gründungen fallen also weg. Jede Allianz zahlt den vollen Eintrittspreis in allen zehn Rohstoffarten, und der Preis steigt im Laufe der Runde. Legionen verschiedener Allianzen lassen sich nicht zusammenlegen. **Restrisiko:** Ein Strohmann kann einen Gleichstand erzwingen und so einen Brennpunkt für eine befreundete Allianz verteidigen. Weil alle Ergebnisse offen sind, fällt so ein Muster auf. Mehrfachkonten bleiben ein Fall für die Admins. |
| Rohstoffe über die Kriegskasse verschieben | Spenden lassen sich nicht zurückholen, das Allianzlager hat keine Auszahlung, der Eintrittspreis wird verbraucht. |
| Kurz vor dem Start beitreten | Berechtigt ist nur, wer seit Beginn des Aufrufs Mitglied ist. Der Titel geht nur an Teilnehmer. |
| Ständiges Nachsehen als Vorteil | Lange Züge, stehende Verteilung. Die Ergebnisse zeigen den letzten Zug, nicht die aktuelle Verteilung. |
| Vorsprung, der sich selbst verstärkt | Mehr Brennpunkte bringen keine zusätzlichen Legionen. Jeder Feldzug beginnt neu, mit neuen Brennpunkten. |
| Eintrittspreis drücken | Der Preis folgt dem Median der Produktion. Wer ihn senken wollte, müsste die eigene Produktion senken. |

## 11. Einstellungen (sv.inc.php)

| Einstellung | Server 1 | Server 2 | Bedeutung |
|---|---|---|---|
| `sv_feldzug_aktiv` | 1 | 1 | Feldzüge an oder aus (Standard ohne Eintrag: aus; zum Rundenstart einschalten) |
| `sv_feldzug_zug_wt` | 480 | 144 | Länge eines Zuges in WT |
| `sv_feldzug_start_zuege` | 7 | 7 | Rundenlaufzeit in Zügen bis zum ersten Aufruf |
| `sv_feldzug_aufruf_zuege` | 2 | 2 | Länge des Aufrufs |
| `sv_feldzug_kampf_zuege` | 12 | 12 | Länge der Kampfphase |
| `sv_feldzug_legionen` | 12 | 12 | Legionen pro Allianz und Zug |
| `sv_feldzug_extra_brennpunkte` | 3 | 3 | Brennpunkte = Teilnehmer + dieser Wert |
| `sv_feldzug_grundpreis` | 2000 | 600 | Eintrittspreis je VS-Rohstoffart beim ersten Aufruf, danach wächst er mit der Produktion |

Ohne Eintrag gelten die Werte von Server 1, außer `sv_feldzug_aktiv` (aus). Server 2 braucht also den Schalter und andere Werte für Zuglänge und Grundpreis.

## 12. Technische Umsetzung

- **Neue Tabellen** in `database/de.sql`. Sie sind abwärtskompatibel, Server 2 läuft mit ihnen auch, solange er das Update noch nicht hat.
  - `de_feldzug`: Feldzug, Phase, Start-WT, aktueller Zug, Eintrittspreis, gemessene Produktion. Die Produktion beim ersten Aufruf ist der Bezugswert der Runde.
  - `de_feldzug_brennpunkt`: Brennpunkte mit System (map_id), Name als Text, Wert, Rolle und Halter
  - `de_feldzug_teilnehmer`: Allianz, Kontrollpunkte, Platz, dazu Kürzel und Name als Text
  - `de_feldzug_mitglied`: Teilnehmer beim Start (user_id)
  - `de_feldzug_verteilung`: aktuelle Legionen je Allianz und Brennpunkt, mit Angabe, wer sie wann geändert hat
  - `de_feldzug_ergebnis`: je Zug, Brennpunkt und Allianz die gesetzten Legionen, die Stärke und der neue Halter. Daraus entstehen die öffentlichen Ergebnisse.
  - `de_feldzug_historie`: Sieger je Feldzug und Feldherren je Runde. Kürzel und Name als Text, damit der Eintrag die Löschung der Allianz am Rundenende überlebt. Diese Tabelle wird beim Reset nicht geleert.
- **Klassen** unter `src/Model/Feldzug/`. Die Zugauswertung ist eine reine Funktion ohne Datenbankzugriff, damit sie sich mit festen Szenarien testen lässt.
- **Tick:** `tickler/wt.php` ruft den Feldzug-Dienst in jedem WT auf, in `try/catch` wie bei Siegel und Hekate. Ein Fehler hält den Wirtschaftstick nicht an.
- **Reset:** Wenn sich `rundenstart_datum` ändert, setzt sich der Feldzug selbst zurück, wie Siegel und Hekate. Die Historie bleibt erhalten.
- **Rundenende:** Die Feldherren werden im Rundenende-Block von `tickler/wt.php` ermittelt und betitelt, nach `createTitleForUser()` für den Erhabenen und vor `wt_auto_reset.php`. Die owner_id kommt zu diesem Zeitpunkt aus `de_login`. Für Konten, die es nicht mehr gibt, entfällt der Titel.
- **Seiten:**
  - neue Feldzugseite, zuerst für Mobil gebaut: Liste der Brennpunkte mit Plus und Minus, Restlegionen, Frist, Ergebnis des letzten Zuges, Punktestand, Kriegskasse und Anmeldung. Am Desktop dieselbe Seite.
  - Markierung der Brennpunkte mit Kürzel des Halters in der Liste der V-Systeme (map_mobile.php) und auf der Desktop-Karte (dm.php)
  - Historie in der Rangliste
  - Abschnitt im Hilfetext
- **Mine und Werft:** Sie werden dort eingehängt, wo heute schon Hekate und Thanatos wirken (`tickler/wt_manage_map.php`, `vs_bonus_info()` in functions.php).

## 13. Offen und zu kalibrieren

- **Zahlen nach dem ersten Feldzug prüfen:** Legionen, Zahl der Brennpunkte, Halterbonus und Umrechnungsfaktor der Questpunkte.
