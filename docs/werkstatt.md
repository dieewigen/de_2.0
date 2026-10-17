# Die Werkstatt des Basissterns

**Regelwerk, Stand 17. Oktober 2026, umgesetzt (siehe „Stand der Umsetzung“ am Ende).** Alle Zahlen stehen als Konstanten in `src/Model/Battleground/Werkstatt.php` und lassen sich dort ändern. Güten, Farben und das Veredeln stammen aus dem Modulsystem der alten erweiterten Archäologie (`soudata/`, 2021 bis 2025).

## Worum es geht

Wer den Basisstern erforscht hat, bekommt auf dessen Seite unter Produktion den Abschnitt **Werkstatt**. Dort schmiedet man aus VS-Rohstoffen und Handelswaren Ausrüstung für den Basisstern: Stücke mit zufälliger Güte und zufälligen Werten, die in den Battlegrounds wirken. Je mehr man schmiedet, desto besser wird die Werkstatt. Nichts ist zu bauen, nichts liegt auf der Karte, es gibt keinen neuen Menüpunkt.

Die Gewinne der Battlegrounds bleiben, wie sie sind: 1 Tronic, 1 Kriegsartefakt, 1 Quantenglimmer. Die Ausrüstung entscheidet nur mit, wer sie holt. Stücke sind an den Spieler gebunden, verschenken oder tauschen geht nicht. Mit der neuen Runde beginnt die Werkstatt von vorn, wie der Basisstern selbst.

Für den Spieler sind es drei Sätze: Die Stufe Deiner Werkstatt steigt mit jedem Wurf. Die Güte eines Stücks ist Glück, das mit dem Rang besser wird. Mit Staub aus zerlegten Stücken veredelst Du, was Du behältst.

## Vier Plätze, vier Rezepte

Der Basisstern hat vier Ausrüstungsplätze. Für jeden gibt es ein Rezept, jedes Rezept kostet feste Mengen, das Stück ist sofort fertig.

| Platz | Hauptwert | Kosten je Wurf | Erfahrung |
|---|---|---|---|
| Panzerung | Hülle | 3.000 Eisen, 1.500 Titan, 5 Handelswaren I | 1 |
| Schildkern | Schild | 3.000 Mexit, 1.500 Dulexit, 5 Handelswaren II | 2 |
| Waffenphalanx | Schaden | 3.000 Tekranit, 1.500 Ylesenium, 5 Handelswaren III | 3 |
| Zielrechner | Kritische Treffer | 3.000 Serodium, 1.500 Rowalganium, 5 Handelswaren IV | 4 |

Anlegen und Ablegen kostet nichts und geht jederzeit. Je Platz ist ein Stück angelegt.

## Rang der Werkstatt

Die Werkstatt beginnt auf Rang 1 und steigt mit der Erfahrung aus jedem Wurf bis Rang 10. Ein höherer Rang bringt bessere Chancen auf höhere Güte, bessere Mindestwerte und mehr Veredelungen je Stück.

| Rang | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 |
|---|---|---|---|---|---|---|---|---|---|
| Erfahrung gesamt | 10 | 30 | 60 | 100 | 160 | 240 | 340 | 460 | 600 |

Rang 10 sind rund 240 Würfe, wenn man alle Rezepte mischt. Ab Rang 4, 7 und 10 steigt die Untergrenze jeder Wertespanne um je einen Punkt.

## Fünf Güten

Namen und Farben wie in der alten erweiterten Archäologie. Die Güte bestimmt die Spanne des Hauptwerts, die Zahl der Nebenwerte und den Staub.

| Güte | Farbe | Nebenwerte | Staub beim Zerlegen | Staub je Veredelung |
|---|---|---|---|---|
| Massenware | weiß | 0 | 1 | 2 |
| Gute Ware | gelb | 1 | 3 | 4 |
| Seltene Ware | grün | 2 | 10 | 8 |
| Mystische Ware | lila | 3 | 30 | 16 |
| Artefakt | blau | 4 | 100 | 32 |

Chancen je Rang in Prozent, Massenware / Gute Ware / Seltene Ware / Mystische Ware / Artefakt:

| Rang | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 |
|---|---|---|---|---|---|---|---|---|---|---|
| Massenware | 70 | 62 | 55 | 48 | 42 | 36 | 31 | 26 | 22 | 18 |
| Gute Ware | 25 | 29 | 31 | 33 | 34 | 35 | 35 | 35 | 34 | 33 |
| Seltene Ware | 5 | 8 | 11 | 14 | 17 | 20 | 22 | 24 | 26 | 28 |
| Mystische Ware | 0 | 1 | 3 | 4 | 6 | 7 | 9 | 11 | 13 | 15 |
| Artefakt | 0 | 0 | 0 | 1 | 1 | 2 | 3 | 4 | 5 | 6 |

Gegen Pechsträhnen: Jeder zehnte Wurf ist mindestens Gute Ware, jeder fünfzigste mindestens Seltene Ware.

## Werte und Spannen

Acht Kennzahlen gibt es. Vier davon sind die Hauptwerte der Plätze, alle acht kommen als Nebenwerte vor, nur nicht der eigene Hauptwert, und jede höchstens einmal je Stück. Spannen je Güte in der Reihenfolge Massenware / Gute / Seltene / Mystische / Artefakt.

| Kennzahl | Wirkung | Hauptwert | Nebenwert |
|---|---|---|---|
| Hülle | mehr Hüllenstruktur | 4–7 / 6–10 / 9–13 / 12–16 / 15–20 % | 2–3 / 3–5 / 4–6 / 5–8 / 6–10 % |
| Schild | mehr Schildenergie | wie Hülle | wie Hülle |
| Schaden | mehr Waffenschaden | wie Hülle | wie Hülle |
| Kritische Treffer | Chance auf doppelten Schaden, heute 10 % | +2–3 / +3–5 / +5–7 / +7–9 / +9–12 Punkte | +1 / +1–2 / +2–3 / +3–4 / +4–6 Punkte |
| Trefferchance | Chance zu treffen, heute 80 % | nur Nebenwert | +1 / +1–2 / +2–3 / +3–4 / +4–5 Punkte |
| Dämpfung | weniger erlittener Schaden | nur Nebenwert | 1 / 1–2 / 2–3 / 3–5 / 4–7 % |
| Haltungsvorteil | mehr Schaden, wenn die eigene Haltung überlegen ist (heute ×1,5) | nur Nebenwert | +5 / +5–10 / +8–15 / +12–20 / +15–30 Punkte |
| Haltungsschutz | weniger Nachteil, wenn die eigene Haltung unterlegen ist (heute ×⅔) | nur Nebenwert | +5 / +5–10 / +8–15 / +12–20 / +15–33 Punkte |

Über alle vier angelegten Stücke zusammen gelten Deckel: Hülle, Schild und Schaden je 40 %, Kritische Treffer +30 Punkte, Trefferchance +15 Punkte, Dämpfung 30 %, Haltungsvorteil +50 Punkte, Haltungsschutz +33 Punkte. Was darüber liegt, verfällt. Ein Stern der Stufe 10 mit allem am Deckel entspricht etwa einem unausgerüsteten Stern der Stufe 20; die Deckel sind Konstanten und lassen sich später senken.

Beispiel: Eine Panzerung der Güte Seltene Ware hat Hülle +11 % und zwei Nebenwerte, etwa Dämpfung 3 % und Haltungsschutz +14 Punkte. Mit Haltungsschutz +14 fällt der Nachteil der unterlegenen Haltung von ×0,67 auf ×0,81.

## Zerlegen und Veredeln

- **Zerlegen** vernichtet ein Stück. Es gibt ein Zehntel der Rohstoffe des Rezepts zurück, keine Handelswaren, und Erbauerstaub nach Güte.
- **Veredeln** erhöht an einem Stück eine Eigenschaft nach Wahl um 0,65 % ihres Werts, wie im Upgrade-O-Modul der alten Archäologie. Jede Eigenschaft eines Stücks lässt sich höchstens dreimal je Werkstattrang veredeln, auf Rang 10 also dreißigmal, das sind zusammen gut 21 %. Eine Veredelung kostet Staub nach Güte, ab der elften Veredelung derselben Eigenschaft das Doppelte, ab der einundzwanzigsten das Vierfache. Veredelte Werte zählen weiter gegen die Deckel. Das Stück zeigt seine Veredelungen, etwa „Mystische Panzerung (+12 V)“.

Zerlegen und neu schmieden bleibt der Weg zu besseren Würfen, Veredeln der Weg, ein gutes Stück auszureizen. Beides frisst Rohstoffe, das zweite über den Staub auch die schlechten Würfe. Einen Neuwurf einzelner Werte gibt es bewusst nicht, dafür schmiedet man noch einmal.

## Lager und Ausbau

Geschmiedete Stücke liegen im Lager der Werkstatt, angelegte Stücke zählen nicht dazu. Das Lager fasst zu Beginn 6 Stücke und lässt sich beliebig oft um je 2 Plätze ausbauen, eine Obergrenze gibt es nicht. Ist das Lager voll, muss man zerlegen, anlegen oder ausbauen, bevor man weiter schmiedet.

Die ersten acht Schritte kosten linear, Schrittnummer mal Grundkosten. Ab dem neunten Schritt steigen die Kosten je Schritt um die Hälfte gegenüber dem vorigen.

| Ausbau | Grundkosten (× Schrittnummer bis Schritt 8) |
|---|---|
| 1 bis 3 | 5.000 Eisen, 2.500 Titan, 5 Handelswaren I |
| 4 bis 6 | 5.000 Mexit, 2.500 Dulexit, 5 Handelswaren II |
| 7 und 8 | 5.000 Tekranit, 2.500 Ylesenium, 5 Handelswaren III |
| 9 und 10 | Schritt 8 × 1,5 bzw. × 2,25 in Serodium, Rowalganium, Handelswaren IV |
| ab 11 | weiter × 1,5 je Schritt in Sextagit, Octagium, Handelswaren V |

Beispiele: Schritt 8 kostet 40.000 Tekranit, 20.000 Ylesenium und 40 Handelswaren III. Schritt 9 kostet 60.000 Serodium, 30.000 Rowalganium und 60 Handelswaren IV, Schritt 12 kostet 202.500 Sextagit, 101.250 Octagium und 203 Handelswaren V, Schritt 15 rund 683.000 Sextagit, 342.000 Octagium und 683 Handelswaren V. Die Grenze setzt die Förderung, nicht das Spiel.

## Im Kampf

Die angelegten Stücke wirken in allen drei Battlegrounds. Der Kampfbericht zeigt hinter jedem Namen die vier Plätze als Buchstaben P, S, W und Z in der Farbe der Güte (grau ohne Stück), die Summe der Werte steht im Tooltip der Buchstaben. Das Kampfprotokoll in der Tick-Ausgabe führt Hüllenstruktur, Schilde und Waffen mit Ausrüstung auf, dazu Trefferchance, kritische Treffer und Dämpfung. Wer die Berichte liest, weiß also, womit der nächste Gegner antritt, und die Paarungen richten sich weiter nach den Siegen.

In GAMMA kämpft die Allianz mit der besten Ausrüstung ihrer Teilnehmer: Je Platz gilt das Stück mit der höchsten Güte unter allen angelegten Stücken der teilnehmenden Mitglieder, mit seinen Werten und Veredelungen. Haben mehrere Stücke die höchste Güte, entscheidet in jedem Kampf das Los. Für die Allianz lohnt es sich also, wenn verschiedene Mitglieder verschiedene Plätze gut besetzen.

## Was sich nicht ändert

Stufe und Haltungen des Basissterns, Weltraumhäfen, Rhythmus und Gewinne der Battlegrounds, Paarungen nach Siegen. Niemand muss schmieden, ein Basisstern ohne Ausrüstung kämpft wie heute.

## Entschieden am 17. Oktober 2026

- Werkstatt als Abschnitt der Basisstern-Seite, kein Gebäude in den Vergessenen Systemen.
- Rang steigt nur durch eigene Würfe. Kein Schmieden in fremden Werkstätten, kein Tauschlager.
- Fünf Güten mit Namen und Farben der alten erweiterten Archäologie.
- Veredeln mit 0,65 % je Update und drei Updates je Rang und Eigenschaft statt eines Neuwurfs einzelner Werte.
- Lager ohne Obergrenze, Kosten ab dem neunten Schritt um die Hälfte je Schritt steigend.
- Zerlegen gibt ein Zehntel der Rohstoffe zurück.
- GAMMA kämpft mit der besten Ausrüstung der Teilnehmer je Platz, bei Gleichstand entscheidet das Los.
- Deckel in der Stufe „spürbar“: 40 % je Hülle, Schild und Schaden, später bei Bedarf senken.
- Das Artefakt hat vier Nebenwerte, der Hauptwert wird in seiner Spanne gewürfelt.

## Technik

- Werkstattdaten im serialisierten Basisstern (`lib/special_ship.class.php`, neue Eigenschaften `erfahrung`, `wuerfe`, `staub`, `lager_stufe`): alte Schiffe bekommen beim Laden die Standardwerte, keine Schemaänderung dafür.
- Stücke in der Tabelle `de_user_bs_item` (id, user_id, platz, guete, werte als JSON `{kennzahl: [wert, veredelungen]}`, angelegt, wt): `database/updates/2026-10-17_werkstatt.sql` und `de.sql`, geleert beim Rundenreset in `wt_auto_reset.php` und `reset.sql`.
- Logik in `src/Model/Battleground/Werkstatt.php`, alle Zahlen als Konstanten: `REZEPT`, `RANG_XP`, `RANG_BONUS`, `CHANCEN`, `PITY_GUT`/`PITY_SELTEN`, `HAUPT_PROZENT`/`HAUPT_KRIT`/`NEBEN`, `DECKEL`, `VEREDELN_*`, `LAGER_*`, `ZERLEGEN_ANTEIL`, `KAMPF_*`.
- Texte in `inc/lang/1_werkstatt.lang.php` (Seite, Meldungen, Kampfbericht).
- Seite: Abschnitt auf `specialship.php`. Formulare mit `ws_aktion` (schmieden, anlegen, ablegen, zerlegen, veredeln, lager), CSRF-Token, Spielersperre, Umleitung nach dem POST; die Meldung steht einmalig in der Sitzung. Eine Zeile je Stück mit Güte-Chip, Werte-Chips und den Knöpfen.
- Kampf: `tickler/kt_manage_bg.php` holt je Teilnehmer `kampfwerte()`, in GAMMA je Allianz `kampfwerteAllianz()`, und rechnet daraus Trefferchance (Deckel 95 %), kritische Treffer, Dämpfung, Schaden, Hüllen- und Schildpool und die Haltungsfaktoren (`haltungsfaktor()`). Der Spielerbericht (`src/View/Kampfbericht.php`) zeigt die Chips hinter dem Namen (`ausruestung1/2` im Kampfergebnis, Summe der Werte als Tooltip); das Kampfprotokoll in der Tick-Ausgabe zeigt die Werte mit Ausrüstung.
- Farben der Güten als Klassen `.ws-guete-0` bis `.ws-guete-4` in `gp/de-main.scss` und `de-main.css`: weiß, gelb, grün, lila, blau.
- Hilfe: `vs.html` Abschnitt „Werkstatt des Basissterns“, Glossar (Erbauerstaub, Güte, Veredeln, Werkstatt).

## Stand der Umsetzung (17. Oktober 2026)

Umgesetzt und lokal geprüft: Würfelverteilung je Rang, Pechsträhnen-Regel, Spannen und Deckel, Schmieden, Anlegen, Zerlegen, Veredeln, Lagerausbau, Auswahl in GAMMA, Kämpfe in ALPHA und GAMMA mit Bericht, Seite und alle POST-Aktionen.

Einspielen: Code und `database/updates/2026-10-17_werkstatt.sql`. Server 2 bekommt es mit seiner Folgerunde. Offen: Werte nach den ersten Kämpfen prüfen, die Deckel sind Konstanten.

Patch Notes (Vorschlag):

> **Werkstatt des Basissterns.** Auf der Basisstern-Seite gibt es jetzt die Werkstatt. Dort schmiedest Du aus VS-Rohstoffen und Handelswaren Ausrüstung für vier Plätze: Panzerung, Schildkern, Waffenphalanx und Zielrechner. Jedes Stück hat eine zufällige Güte von Massenware bis Artefakt und zufällige Werte für Hülle, Schild, Schaden, kritische Treffer, Trefferchance, Dämpfung, Haltungsvorteil und Haltungsschutz. Jeder Wurf bringt Erfahrung, mit dem Rang der Werkstatt (1 bis 10) steigen die Chancen auf hohe Güte. Angelegte Stücke wirken in allen Battlegrounds, in GAMMA zählt je Platz das beste Stück der Allianz. Zerlegen gibt ein Zehntel der Rohstoffe und Erbauerstaub zurück, mit Staub veredelst Du einzelne Werte. Das Lager fasst 6 Stücke und lässt sich beliebig ausbauen. Der Kampfbericht zeigt die Ausrüstung beider Seiten. Alles dazu in der Hilfe unter Vergessene Systeme.
