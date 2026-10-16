# Übernahme der Browser-Extension ins Spiel (Oktober 2026)

Die „Die-Ewigen Extension“ (github.com/cJlD2ENp4PoPQ/de-tools-ext, zuletzt Version 0.12.9) hängte sich per Content-Script in `dm.php` und elf Spielseiten und las alles aus dem HTML (Tabellenspalten, Texte, Bildpfade). Jede Umstellung einer Seite auf die `mod`-Bausteine brach sie. Seit dem 15.10.2026 bietet das Spiel die Funktionen selbst an; die Extension kann abgekündigt werden.

Vorteil der Übernahme: Das Spiel rechnet mit den echten Werten (fleetscore, Tickplan `$wts`/`$kts`, Verlustquoten je Einsatzart, Sondendaten), die Extension schätzte sie aus dem HTML oder kopierte Konstanten aus dem Spielcode.

## Was die Extension konnte und was daraus wurde

| Extension | Im Spiel | Wo |
|---|---|---|
| Tick-Countdown mit Schalter, Tickpläne je Server fest einprogrammiert | Schalter (Balken oben im Zeitfeld der Desktop-Kopfzeile) zeigt Restzeit bis WT und KT, letzte Minute vor dem KT blinkt; Serverzeit und echter Tickplan, Stellung im Browser (`localStorage` `de_zeitmodus`) | `dm.php` (Script `dm_zeit_*`), `resline.php` (`data-jetzt`, `data-uhr`), `src/Model/Tick/Plan.php` |
| Flottenpunkte geschätzt (Punkte minus Kollektorpunkte) in Sektor und Karte | Spalte „FP“ in der Sektoransicht (auch NPC-Sektoren) und zweite Zeile in der Spielerkachel der Karte, exakt aus `de_user_data.fleetscore` | `sector.php`, `map.php` |
| Lokal gemerkte Allianztags, manuelle Zuordnung, eigener Beziehungsstatus, „Bekannte Mitglieder“ | nur „Bekannte Mitglieder“: Spieler, deren Tag in den eigenen Sondendaten steht (Einsatz „Allianztag“, mit Infophalanx auch die der Allianz) | `ally_detail.php` |
| Sondenhistorie im Browser, Differenz, Output je WT, Hochrechnung mit fester Rundenlänge | Tabelle `de_user_scan_history` (letzte 10 Berichte je Ziel, wie `de_user_scan` auch an Infophalanx-Empfänger), Differenzspalte im Sondenbericht, Wirtschaftsticks seit dem letzten Bericht, Rohstoffzuwachs je WT, Punkte daraus in 1.000 WT | `secret.php`, `src/View/Geheimdienst/Bericht.php` |
| Agentenschätzung in den News mit festen 2–6 % | Spanne im Nachrichtentext aus der echten Verlustquote der jeweiligen Einsatzart | `secret.php`, `inc/lang/1_secret.lang.php` |
| Ressourcenschlüssel zu den Baukosten | Zeile „Passender Kollektorschlüssel“ unter den Baukosten mit Link zu den Kollektoren | `production.php`, `js/produktion1-4.js` |
| Support-Hinweis auf der Produktionsseite | nicht übernommen, die Darstellung war unausgereift; Grundlage für eine bessere Lösung: `$smatrix`, `$umatrix`, `$uanz` in `tickler/kt_einheitendaten.php`, in `production.php` schon eingebunden | – |
| Auktionsfilter nach Währung und Ware | Filterfelder im Kopf der Auktionsseite, Auswahl bleibt im Browser (`localStorage` `de_auk_ware`, `de_auk_waehrung`) | `auction.php` |
| VS: Pfeile folgen der gefilterten Liste | Pfeile der Systemseite führen im Filter (Cookies `vsf0a/b/c`) nur durch passende Systeme; die Filterklassen legt die Übersicht in der Session ab | `map_mobile.php`, `lib/map_system.class.php` (`getFilterKlassen`), `functions.php` (`generate_vsystem_kopfzeile`), `src/Model/VSystem/Filter.php` |
| VS: Felder mit bekannten Fundstücken rot | kleine Marke „F“ an der Feldkachel | `lib/map_system.class.php` (`bekannteFunde`) |
| Artefaktschutz je Art in den Extension-Einstellungen | Schutz je Artefakt („Schützen“/„Schutz aufheben“ nach dem Antippen, Schloss an der Kachel); Zerstören und die Verschmelzung in ein neues Artefakt sind in der Oberfläche und auf dem Server gesperrt, die Stufen-Verschmelzung gleicher Art bleibt möglich | `artefacts.php`, Spalte `de_user_artefact.schutz` |
| Kampfsimulator DEKS (Fremdseite im iframe) | nicht übernommen, auf Wunsch des Extension-Entwicklers | – |
| Infokasten, Rundenwechsel-Erkennung, Speicher-Reset, Optionsseite | entfällt | – |

## Schema

`database/updates/2026-10-15_sondenhistorie_artefaktschutz.sql` legt `de_user_scan_history` an und ergänzt `de_user_artefact.schutz`. Es gehört auf jeden Server zusammen mit dem Code: Der Sondenbericht schreibt in die Historie, der Schutz-Knopf in die Spalte. Beides steht auch in `database/de.sql`; `database/reset.sql`, `tickler/wt_auto_reset.php` und die Spielerlöschung im WT leeren die Historie mit.

## Prüfung

- `php tests/testlauf.php seite 1 sector.php`, `… secret.php GET a=s zsec1=… zsys1=…`, `… auction.php`, `… production.php`, `… artefacts.php GET mergeartefacts=1 lid1=… lid2=…` (mit geschütztem Artefakt muss die Meldung „geschützt“ erscheinen), `… map_system.php?id=…`.
- Die Filterpfeile lassen sich ohne Browser über `generate_vsystem_kopfzeile()` mit gesetzten `$_COOKIE['vsf0a']` und `$_SESSION['vs_filterklassen']` prüfen; `Plan` und `Filter` sind reine Funktionen ohne Datenbank.
- Der Countdown läuft nur in der Desktop-Ansicht (`dm.php`); mobil gibt es keine Kopfzeile.
