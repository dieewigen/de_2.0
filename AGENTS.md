# AGENTS.md - Die Ewigen 2.0

## Projektübersicht

**Die Ewigen 2.0** ist ein browserbasiertes Strategiespiel (Massively Multiplayer Online Game) mit Weltraum-Thematik. Spieler verwalten Kolonien, bauen Flotten, forschen Technologien und treten in Allianzen zusammen, um gemeinsam zu herrschen.

- **Repository:** https://github.com/dieewigen/de_2.0
- **Website:** www.die-ewigen.com
- **Technologie-Stack:** PHP, MySQL/MariaDB, JavaScript, SCSS

## Systemanforderungen

- **Webserver:** Apache/Nginx
- **PHP:** 7.4+ (kompatibel mit PHP 8.x)
- **Datenbank:** MySQL 5.7+ oder MariaDB 10.2+
- **Empfohlen:** XAMPP für lokale Entwicklung
- **Hosting:** Shared PHP-Hosting ohne Dauerprozesse und eigene Ports; Echtzeit (z. B. Chat) läuft über Polling, WebSockets sind nicht möglich

## Projektstruktur

```
de2/
├── api/                     # JSON-API mit eigenem Composer-Ordner (api/vendor)
├── cache/                   # Cache-Verzeichnis für generierte Daten
├── database/                # SQL-Schemas
│   ├── de.sql              # Hauptdatenbank-Schema
│   ├── br.sql              # Battle Report Schema
│   ├── reset.sql           # Reset-Skript
│   └── updates/            # datierte Schemaänderungen für laufende Server (JJJJ-MM-TT_thema.sql)
├── docs/                    # Texte: Regeln (feldzug.md), Bestandsaufnahmen und Übergaben (extension-uebernahme.md)
├── gp/                      # Grafiken, Styles (CSS/SCSS)
│   ├── de-main.scss        # Haupt-Stylesheet
│   ├── de-dm.scss          # Desktop-Ansicht (dm.php): Karten statt Bildzellen-Rahmen, Breite der Spielspalte
│   ├── de-chat.scss        # Chat-Stylesheet
│   ├── de-map.scss         # Karten-Stylesheet
│   └── g/                  # Grafik-Assets
├── inc/                     # Konfiguration und ältere Include-Dateien
│   ├── env.inc.php         # Umgebungsvariablen (DB-Zugänge, Mail), nicht im Git
│   ├── sv.inc.php          # Server-Variablen (Spielkonfiguration), nicht im Git
│   ├── session.inc.php     # Session-Management
│   ├── header.inc.php      # Standard-Header
│   ├── lang/               # Sprachdateien (Deutsch/Englisch)
│   ├── artefakt.inc.php    # Artefakt-Werte
│   ├── sabotage.inc.php    # Sabotage-Werte
│   └── allyjobs.inc.php    # Allianz-Aufgaben
├── js/                      # JavaScript-Dateien
│   ├── de_fn.js            # Hauptfunktionen
│   ├── ang_fn.js           # Funktionen der Spielseiten (u. a. Vergessene Systeme)
│   ├── de_chat.js          # Chat-Funktionen
│   └── jquery-3.7.1.min.js # jQuery-Bibliothek
├── ki/                      # KI/NPC-Systeme
├── lib/                     # Vergessene Systeme (map_system, Sonder-Systeme) und Basisstern
│                            # (special_ship); bleibt bis zum Rundenstart, siehe „Bekannte Issues“
├── npcx/                    # Erweiterte NPC-Logik
├── ourdetool/               # Admintool
├── src/                     # PSR-4 Autoload-Klassen (DieEwigen\DE2)
│   ├── Model/              # Spiellogik je Bereich (Alliance, Feldzug, Tick …), dazu
│   │                       # Sperre (Spielersperre) und Nachricht (an einen Spieler);
│   │                       # Tick/Plan (nächster Tick, WT zwischen zwei Zeiten), VSystem/Filter (VS-Filter)
│   ├── View/               # Darstellung (Kampfbericht, Berater, Alliance/Navi …)
│   └── Session/            # CsrfToken
├── tests/                   # Prüfwerkzeug für Umbauten (nur Kommandozeile)
├── tickler/                 # Tick-Skripte (Cron-Jobs)
│   ├── runtick.php         # startet ki_tick.php, kt.php und wt.php nacheinander
│   ├── ki_tick.php         # NPC-Tick
│   ├── kt.php              # Kampf-Tick (Flottenbewegungen, Schlachten)
│   └── wt.php              # Wirtschafts-Tick (Ressourcen, Punkte)
├── vendor/                  # Composer-Dependencies, nicht im Git
├── composer.json            # PHP-Abhängigkeiten
├── functions.php            # Gemeinsame Funktionen, lädt vendor/autoload.php
└── index.php               # Haupteinstiegspunkt
```

## Kernkonzepte und Spielmechaniken

### 1. Spieler-Accounts (User)
- **Tabelle:** `de_login`, `de_user_data`
- Jeder Spieler hat:
  - Koordinaten (Sektor:System)
  - Ressourcen (Multiplex, Dyharra, Iradium, Eternium, Tronic)
  - Kollektoren (Energiequellen)
  - Flotten (bis zu 4 Flotten pro Spieler)
  - Technologien
  - Punkte (Score)
  - Rang und Platzierung

### 2. Sektoren und Systeme
- **Tabelle:** `de_sector`
- Das Universum ist in Sektoren unterteilt (0-8+)
- Jeder Sektor hat mehrere Systeme
- Sektor 1 ist der Startsektor
- Spieler können zwischen Sektoren umziehen

### 3. Ressourcensystem
- **Multiplex** (restyp01): Basisressource
- **Dyharra** (restyp02): Mittlere Ressource
- **Iradium** (restyp03): Seltene Ressource
- **Eternium** (restyp04): Sehr seltene Ressource
- **Tronic** (restyp05): Premium-Ressource

Ressourcen werden durch Kollektoren generiert, die Energie in Materie umwandeln.

### 4. Technologiebaum
- **Tabelle:** `de_tech_data`
- Technologien schalten Gebäude, Schiffe und Fähigkeiten frei
- Forschung kostet Ressourcen und Zeit
- Spalte `tech_typ` (Reiter der Technologieseite): 0 Gebäude, 1 Forschungen und Einheiten, 2 Basisschiffe, 3 V-Systeme
- Tech-IDs (Namen je Rasse, Beispiele für Rasse 1):
  - 1-24, 26-30, 112-132, 139-141, 157, 158, 160: Gebäude (z. B. 1 Konstruktionszentrum I, 129 Raumwerft I)
  - 40-75: Forschungen
  - 80: Kollektoren
  - 81-90: Schiffe
  - 100-104: Verteidigungsanlagen
  - 110: Sonden
  - 111: Agenten
  - 133-138: Basisschiff-Erweiterungen (z. B. 133 BS-Artefaktplatz I)
  - 25, 142-156, 159: Vergessene Systeme (z. B. 25 Hyperraumpfad-Stabilisator, 159 Basisstern)

### 5. Flottensystem
- **Tabelle:** `de_user_fleet`
- Jeder Spieler hat 4 Flotten (fleet_id: user_id-0 bis user_id-3)
- Flottenkommandeure sammeln Erfahrung (Angriff/Verteidigung)
- Schiffsklassen (Spalten e81-e90, Namen je Rasse, z. B. Rasse 1: Hornisse, Guillotine, Schakal …):
  - Jäger (e81), Jagdboot (e82), Zerstörer (e83), Kreuzer (e84), Schlachtschiff (e85)
  - Bomber (e86), Transmitterschiff (e87), Trägerschiff (e88), Frachter (e89), Titan (e90)

### 6. Allianzen (Allys)
- **Tabelle:** `de_allys`, `de_ally_*`
- Spieler können Allianzen gründen oder beitreten
- Hierarchie:
  - Leader (leaderid)
  - Co-Leader (coleaderid1-3)
  - Fleet Commander (fleetcommander1-2)
  - Tactical Officer (tacticalofficer1-2)
  - Member Officer (memberofficer1-2)
- Allianz-Features:
  - Gemeinsame Aufgaben/Missionen
  - Allianz-Gebäude
  - Kriegserklärungen
  - Partner-Allianzen
  - Forum

### 7. Artefakte
- **Tabelle:** `de_artefakt`, `de_user_artefact`
- Spezielle Items mit besonderen Effekten
- Können von Spielern oder Sektoren gehalten werden
- Beispiele:
  - Schale von Sabrulia (Energiebonus)
  - Spiegel von Calderan (Kollektorduplikation)
  - Grab des Ra (Anti-Agentenabwehr)

### 8. Tick-System
Das Spiel läuft mit zwei Tick-Typen; die Skripte prüfen selbst anhand von `$wts`/`$kts` in `sv.inc.php`, ob Tickzeit ist. Dazu kommt der NPC-Tick (`tickler/ki_tick.php`) im Takt des WT.

#### Wirtschaftstick (WT)
- **Datei:** `tickler/wt.php`
- Ausführung: Alle 15 Minuten (konfigurierbar)
- Funktionen:
  - Ressourcenproduktion
  - Punkteberechnung
  - Gebäude fertigstellen
  - Einheiten produzieren
  - Zufallsereignisse
  - Inaktive Spieler löschen
  - Erhabenen-Prüfung

#### Kampftick (KT)
- **Datei:** `tickler/kt.php`
- Bewegt Flotten (Flugzeiten, Ankünfte, Rückkehr)
- Berechnet Kämpfe
- Generiert Kampfberichte, seit Okt. 2026 mit dem Ablauf der Schüsse (`ablauf`) für „Kampf abspielen“ im Bericht
- Verarbeitet Sabotageaktionen

### 9. Spezialisierungen
- **Spalten:** `spec1` bis `spec5` in `de_user_data`
- Spieler können sich spezialisieren:
  - spec1: Kampfspezialisierung
  - spec2: Wirtschaftsspezialisierung
  - spec3: Planetarer Ertrag
  - spec4: Forschungsspezialisierung
  - spec5: Sektorkollektoren

### 10. Erhabenen-System
Das ultimative Spielziel:
- Spieler mit den meisten Erhabenen-Punkten (ehscore)
- Muss Position mehrere Ticks halten (sv_benticks)
- Bei Sieg: Runden-Reset oder Spieler-Reset
- Modi:
  - **Normale Runde:** Erhabener gewinnt
  - **Ewige Runde:** Reset des Erhabenen, Spiel läuft weiter
  - **Hardcore:** Mehrere Teilsiege nötig

## Datenbankstruktur (Wichtigste Tabellen)

### Spieler und Login
- `de_login`: Account-Daten (user_id, nic, password, email, status)
- `de_user_data`: Spieler-Hauptdaten (Ressourcen, Punkte, Position, Technologien)
- `de_user_info`: Zusatzinformationen
- `de_user_build`: Bauaufträge
- `de_user_fleet`: Flotten (4 pro Spieler)
- `de_user_news`: Nachrichten/Ereignisse
- `de_user_hyper`: Hyperfunk-Nachrichten
- `de_user_scan`: letzter Sondenbericht je Spieler und Ziel (mit Infophalanx an die Allianz verteilt)
- `de_user_scan_history`: Sondenhistorie, die letzten 10 Berichte je Spieler und Ziel (Differenz im Sondenbericht)

### Allianzen
- `de_allys`: Allianz-Hauptdaten
- `de_ally_antrag`: Beitrittsanträge
- `de_ally_history`: Allianz-Historie
- `de_ally_partner`: Partner-Allianzen
- `de_ally_war`: Kriegserklärungen
- `de_ally_storage`: Allianz-Lager
- `de_alliforum_threads`: Forum-Threads
- `de_alliforum_posts`: Forum-Posts

### Spielwelt
- `de_sector`: Sektoren (Eigenschaften, Ressourcen, Gebäude)
- `de_basedata_map_knoten`: Karten-Knoten
- `de_basedata_map_kanten`: Karten-Verbindungen
- `de_basedata_map_sector`: Sektor-Typen

### Technologie und Items
- `de_tech_data`: Technologie-Definitionen
- `de_artefakt`: Artefakt-Definitionen
- `de_user_artefact`: Spieler-Artefakte
- `de_item_data`: Item-Definitionen
- `de_user_storage`: Spieler-Inventar

### Kommunikation
- `de_chat_msg`: Chat-Nachrichten
- `de_dez_zeitung`: Zeitungen
- `de_dez_ausgaben`: Zeitungsausgaben

### System
- `de_system`: Globale Systemeinstellungen (Tick-Status, Rundenalter)
- `de_user_stat`: Spieler-Statistiken
- `de_ally_stat`: Allianz-Statistiken

## Konfiguration

### Umgebungsvariablen (inc/env.inc.php)
```php
// Spiel-Datenbank
$GLOBALS['env_db_dieewigen_host'] = 'localhost';
$GLOBALS['env_db_dieewigen_user'] = 'root';
$GLOBALS['env_db_dieewigen_password'] = '';
$GLOBALS['env_db_dieewigen_database'] = 'de_game';
// ebenso env_db_loginsystem_* (Accountverwaltung: globaler Chat, Titel) und env_db_logging_* (Aktionsprotokoll)
$GLOBALS['env_mail_server'] = 'smtp.example.com';   // SMTP für mail_smtp(), dazu env_mail_user/-password/-noreply
$GLOBALS['env_admin_email'] = 'admin@example.com';
// vollständige Liste: inc/env.inc.sample.php
```

### Server-Variablen (inc/sv.inc.php)
Wichtige Einstellungen:
```php
$sv_server_name = 'Die Ewigen';              // Servername
$sv_server_tag = '[DE]';                     // Server-Tag
$sv_maxsystem = 40;                          // Max Systeme pro Sektor
$sv_anz_rassen = 4;                          // Anzahl Rassen
$sv_kollieertrag = 100;                      // Kollektor-Ertrag
$sv_winscore = 10000;                        // Punkte für Erhabenen
$sv_benticks = 96;                           // Ticks zum Sieg
$sv_ewige_runde = 0;                         // Ewige Runde aktiv?
$sv_hardcore = 0;                            // Hardcore-Modus?
$wts = [                                     // WT-Ausführungszeiten
    0 => [0, 15, 30, 45],
    // ... weitere Stunden
];
```

## Installation und Setup

### 1. Repository klonen
```bash
git clone https://github.com/dieewigen/de_2.0.git
cd de_2.0
```

### 2. Composer-Dependencies installieren
```bash
composer install
```

### 3. Datenbank erstellen
```sql
CREATE DATABASE de_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Schema importieren
```bash
mysql -u root -p de_game < database/de.sql
```

### 5. Konfiguration anpassen
```bash
cp inc/env.inc.sample.php inc/env.inc.php
cp inc/sv.inc.sample.php inc/sv.inc.php
# Dateien bearbeiten und Zugangsdaten eintragen
```

### 6. NPC-Accounts generieren
```bash
php ki/generiereaccounts.php
```
Wichtig: `$sv_maxsystem` in `sv.inc.php` muss groß genug sein!

### 7. Cron-Jobs einrichten
Für Produktionsserver (Linux), minütlich; die Skripte prüfen selbst, ob Tickzeit ist. Sie binden ihre Dateien relativ ein und müssen deshalb im Ordner `tickler/` starten:
```bash
* * * * * cd /pfad/zu/de2/tickler && /usr/bin/php runtick.php
```
`runtick.php` startet `ki_tick.php`, `kt.php` und `wt.php` nacheinander; die drei lassen sich auch einzeln eintragen.

Für Windows (Task Scheduler):
- Task für `runtick.php`, Trigger: minütlich
- Aktion: `php.exe runtick.php`, Starten in: `C:\pfad\zu\de2\tickler`

## Entwicklung

### PSR-4 Autoloading
Moderne Klassen liegen unter `src/` und verwenden den Namespace `DieEwigen\DE2`:

```php
<?php
namespace DieEwigen\DE2\Model\Alliance;

class AllyMemberLimitCalc {
    private \mysqli $db;
    
    public function __construct(\mysqli $db) {
        $this->db = $db;
    }
    
    public function updateAlliesMemberLimit(): array {
        // Logik hier
    }
}
```

Verwendung:
```php
require_once 'vendor/autoload.php';
use DieEwigen\DE2\Model\Alliance\AllyMemberLimitCalc;

$service = new AllyMemberLimitCalc($GLOBALS['dbi']);
$result = $service->updateAlliesMemberLimit();
```

### Datenbankzugriff
Das Projekt nutzt MySQLi mit prepared statements:

```php
// Modern (empfohlen)
$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT * FROM de_user_data WHERE user_id=?", 
    [$user_id]
);
```

- `mysqli_query` steht nur noch bei Befehlen ohne Parameter (LOCK/UNLOCK TABLES, SHOW), in `api/` und in den generischen Wrappern des Admintools.
- Spalten- und Tabellennamen (z. B. `"restyp0".$restyp`) bleiben verkettet, nur Werte werden Platzhalter; IN-Listen bekommen einen Platzhalter je Element.
- Prepared Statements liefern Zahlenspalten als int/float, nicht als String.
- Aktionen, die Rohstoffe oder Einheiten ändern, laufen in der Spielersperre: `Sperre::setzen($GLOBALS['dbi'], $uid)` … `Sperre::loesen($GLOBALS['dbi'], $uid)` (`DieEwigen\DE2\Model\Sperre`).

### SCSS-Kompilierung
CSS wird aus SCSS generiert (Dart Sass, `npm install -g sass`). Die eingecheckten CSS-Dateien werden aber nicht einfach neu erzeugt:
- `gp/de-main.css` weicht in der Farbschreibweise von einem frischen Sass-Lauf ab. Änderungen deshalb als Differenz einspielen: den Stand vor und nach der SCSS-Änderung kompilieren und nur den Unterschied übertragen.
- `gp/de-chat.css` entsteht komprimiert aus `gp/de-chat.scss` (`--style=compressed`).
- `gp/de-dm.css` (Desktop-Ansicht) wird frisch kompiliert: `sass --style=expanded --no-source-map gp/de-dm.scss gp/de-dm.css`. Nur dm.php und cssinclude.php in einer Desktop-Sitzung laden es; Classic und Mobil bleiben bei `de-main.css` und dürfen sich nicht ändern.
- `gp/de-map.css` stammt aus einem anderen Compiler (eine Zeile); neue Regeln nur anhängen.

```bash
sass --style=expanded --no-source-map gp/de-main.scss /tmp/vorher.css    # vor der Änderung
sass --style=expanded --no-source-map gp/de-main.scss /tmp/nachher.css   # nach der Änderung
diff -u /tmp/vorher.css /tmp/nachher.css | patch gp/de-main.css
```

### JavaScript
Die Seiten binden die unminifizierten Dateien ein (`js/de_fn.js`, `js/ang_fn.js` …) und hängen `filemtime` als Versionsparameter an. Die `*.min.js` im Ordner lädt keine Seite.

### Ablage von Texten und Schemaänderungen
- Texte zu Umbauten (Bestandsaufnahme, Übergabe, Regeln) nach `docs/` als Markdown, eine Datei je Thema.
- Schemaänderungen für laufende Server nach `database/updates/JJJJ-MM-TT_thema.sql` und zusätzlich in `database/de.sql`; die Datei wird auf jedem Server zusammen mit dem Code eingespielt.
- Keine weiteren Ablageorte anlegen, auch nicht im Wurzelverzeichnis.

## Wichtige Funktionen und Helper

### In functions.php
- `loadPlayerTechs($uid)`: Lädt Technologien eines Spielers
- `hasTech($techs, $tech_id)`: Prüft, ob Spieler Tech hat
- `change_storage_amount($uid, $item_id, $amount)`: Ändert Item-Menge
- `insert_chat_msg($channel, $type, $username, $message)`: Chat-Nachricht; Typ 3 (globaler Chat) landet in der Login-Datenbank, alle anderen in der Spiel-Datenbank
- `get_player_allyid($uid)`: Gibt Allianz-ID zurück
- `createTitleForUser($owner_id, $title)`: Erstellt Spieler-Titel
- `mail_smtp($to, $subject, $body)`: Versendet E-Mails
- `getArtefactAmountByUserId($uid, $art_id)`: Artefakt-Anzahl
- `rahmen_oben($titel)` / `rahmen_unten()`: Seitenrahmen (etwa 580 px Innenbreite; in der Desktop-Ansicht eine Karte in Spaltenbreite, `gp/de-dm.scss`); mit `false` als letztem Parameter geben sie HTML zurück statt es auszugeben

### In src/ (Auswahl)
- `Model\Sperre::setzen($db, $uid)` / `loesen($db, $uid)`: Spielersperre um Aktionen
- `Model\Nachricht::anSpieler($db, $uid, $text, $typ)`: Nachricht an einen Spieler (`de_user_news`)
- `View\FehlendeTechnologie::karte($techId)`: Hinweis „Fehlende Technologie“
- `View\Kampfbericht::v1(...)` / `bg(...)`: Kampfberichte der Flotten und Battlegrounds; Berichte mit `ablauf` bekommen den Knopf „Kampf abspielen“ (Kasten aus `ablauf()`, Abspiellogik in `js/de_fn.js`)
- `Model\Tick\Plan::naechster($plan)` / `anzahlZwischen($plan, $von, $bis)`: nächster Tick und Tickzahl zwischen zwei Zeiten aus `$wts`/`$kts` (Serverinfo, Countdown in dm.php, Sondenhistorie)
- `Model\VSystem\Filter::ausCookies($_COOKIE)` / `passendeIds(...)`: VS-Filter der Übersicht für die Pfeile der Systemseite
- `Session\CsrfToken`: siehe Sicherheit

### In inc/allyjobs.inc.php
- Allianz-Aufgaben-Definitionen
- `$allyjobs` Array mit Quest-Typen

### In inc/artefakt.inc.php
- `$sv_artefakt`: permanente Boni und Zufallswerte je Artefakt

## API-Endpunkte

Das Projekt verfügt über eine REST-API unter `api/`:
- `api/index.php`: API-Router
- Authentifizierung über Session oder Token
- JSON-Responses

## Runden-Management

### Reset-Arten

1. **Normaler Reset** (Ende der Runde)
   - Alle Spieler-Daten zurücksetzen
   - Neue Runde startet
   - Skript: `tickler/wt_auto_reset.php`

2. **Spieler-Reset** (Ewige Runde)
   - Nur Erhabener wird zurückgesetzt
   - Andere Spieler bleiben
   - Counter: `eh_siege`, `eh_counter`

3. **Manueller Reset**
   - `database/reset.sql` ausführen
   - Spielerdaten bleiben, Spielwelt wird zurückgesetzt

## Sicherheit

### Session-Management
- Sessions in `inc/session.inc.php`
- `$_SESSION['ums_user_id']`: Aktuelle User-ID
- `$_SESSION['ums_rasse']`: Spieler-Rasse
- `$_SESSION['ums_mobi']`: Mobile-Modus

### SQL-Injection-Schutz
- **Immer** prepared statements verwenden
- Nie direkt Benutzereingaben in Queries

### XSS-Schutz
- Ausgaben mit `htmlspecialchars($string, ENT_QUOTES, 'UTF-8')` escapen
- HTML-Eingaben mit `strip_tags()` filtern

### CSRF-Schutz
- `DieEwigen\DE2\Session\CsrfToken`: ein Token je Sitzung; `CsrfToken::get()` für ein verstecktes Formularfeld `token`, `CsrfToken::query()` für Links, `CsrfToken::check($_POST['token'] ?? '')` beim Auswerten
- bisher nur in feldzug.php, specialship.php und einigen Allianzseiten (ally_kick, ally_leader, ally_members); die übrigen Formulare sind noch ungeschützt
- das Admintool hat ein eigenes Verfahren (Secret in `ourdetool/.ht_csrf_secret`, nicht im Git)

## Testing

### Lokaler Test-Server
```bash
# PHP Built-in Server (nur für Entwicklung!)
php -S localhost:8000 -t .
```

### Debug-Modus
In `sv.inc.php`:
```php
$sv_debug = 1;  // Aktiviert Debug-Ausgaben
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Prüfwerkzeug für Umbauten
`tests/testlauf.php` führt Spielseiten und Aktionen (Bauen, Recycling, Flotten, Forschung, Vergessene Systeme …) wie ein eingeloggter Spieler gegen Kopien der lokalen Spiel- und Login-Datenbank aus und vergleicht Ausgabe und Datenbankstand:
```bash
php tests/testlauf.php aufnehmen vorher      # vor der Änderung (bei schon geändertem Code: git stash … git stash pop)
php tests/testlauf.php aufnehmen nachher     # nach der Änderung
php tests/testlauf.php vergleich vorher nachher
php tests/testlauf.php seite 1 recycling.php POST recyclingbutton=1 e81=5 t81=82   # eine Seite einzeln
```
- Jeder Fall beginnt mit frischen Kopien (`de_test`, `de_test_login`); die echten Datenbanken bleiben unberührt, Mails gehen ins Leere.
- Testspieler, Seiten und Aktionen stehen oben in der Datei (`TL_SPIELER`, `TL_SEITEN`, `TL_AKTIONEN`); bei einer anderen Entwicklungsdatenbank die Spieler-IDs anpassen.
- Nur Kommandozeile und lokal: Webaufrufe liefern nichts, `tests/.htaccess` sperrt den Ordner.
- Die Ticks sind nicht abgedeckt. `wt.php` nie komplett zum Testen starten, der WT verschickt Mails.

## Performance-Optimierung

### Datenbank
- Indizes auf häufig abgefragte Spalten
- `user_id`, `sector`, `allytag` sind indexiert
- Bei langsamen Queries: `EXPLAIN` verwenden

### Caching
- Toplist-Cache: `cache/toplist/`
- Bilder-Cache für generierte Grafiken
- Bei Änderungen Cache leeren

### PHP-Optimierung
- OPcache aktivieren
- `memory_limit` auf mindestens 256M setzen
- `max_execution_time` für Ticks auf 240s

## Bekannte Issues und TODOs

### Legacy-Code
- Viele Dateien sind noch prozedural
- Schrittweise Umstellung auf OOP/PSR-4: gemeinsame Bausteine als Klasse unter `src/`, keine neuen `inc/*.inc.php`
- `lib/map_system.class.php` (mit den Sonder-Systemen) und `lib/special_ship.class.php` bleiben vorerst in `lib/`: Ihre Objekte liegen serialisiert in `de_map_objects.data` und `de_user_special_ship.data`, ein neuer Klassenname macht diese Daten unlesbar. Der Umzug nach `src/` geht deshalb nur zum Rundenstart, wenn beide Tabellen neu aufgebaut werden.

### Sicherheit
- Passwort-Hashing modernisieren (zu `password_hash()`)
- CSRF-Tokens auf alle Formulare ausweiten
- Input-Validierung vereinheitlichen

### Features in Entwicklung
- REST-API vervollständigen
- Mobile App (PWA)
- Grafische Verbesserungen

## Deployment

### Produktionsserver
1. **Sicherheit:**
   - `$sv_debug = 0` setzen
   - Error-Display ausschalten
   - Dateirechte prüfen (keine 777!)

2. **Performance:**
   - OPcache aktivieren
   - HTTPS erzwingen
   - CDN für statische Assets

3. **Backup:**
   - Regelmäßige DB-Backups
   - Backup vor jedem Update

4. **Monitoring:**
   - Server-Logs überwachen
   - Tick-Ausführung protokollieren
   - Fehler-Mails an Admin

## Community und Support

- **Forum:** Die Ewigen Community-Forum
- **Discord:** Server für Spieler und Entwickler
- **GitHub Issues:** Bug-Reports und Feature-Requests
- **E-Mail:** issomad@die-ewigen.com

## Lizenz

Das Projekt steht unter einer proprietären Lizenz. Details siehe LICENSE-Datei im Repository.

## Versionierung

Siehe Git-Tags für Release-Versionen:
- `v2.0.x`: Stable Releases
- `master`: Development Branch
- `feature/*`: Feature-Branches

## Credits

- **Hauptentwicklung:** Isso, Tino und das Die Ewigen Team
- **Community-Beiträge:** Zahlreiche Spieler und Tester
- **Dependencies:** PHPMailer, Mobile Detect

---

**Letzte Aktualisierung:** 17. Oktober 2026

Für weitere Fragen siehe README.md oder kontaktiere das Entwicklerteam.
