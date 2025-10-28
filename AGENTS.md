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

## Projektstruktur

```
de2/
├── ally/                    # Allianz-spezifische Dateien und Funktionen
├── api/                     # API-Endpunkte (REST/JSON)
├── cache/                   # Cache-Verzeichnis für generierte Daten
├── controllers/             # (Leer) - Für zukünftige MVC-Struktur
├── database/                # SQL-Schemas und Migrations
│   ├── de.sql              # Hauptdatenbank-Schema
│   ├── br.sql              # Battle Report Schema
│   └── reset.sql           # Reset-Skript
├── docs/                    # Dokumentation
├── gp/                      # Grafiken, Styles (CSS/SCSS)
│   ├── de-main.scss        # Haupt-Stylesheet
│   ├── de-chat.scss        # Chat-Stylesheet
│   ├── de-map.scss         # Karten-Stylesheet
│   └── g/                  # Grafik-Assets
├── inc/                     # Include-Dateien (Konfiguration, Includes)
│   ├── env.inc.php         # Umgebungsvariablen (DB-Zugänge)
│   ├── sv.inc.php          # Server-Variablen (Spielkonfiguration)
│   ├── session.inc.php     # Session-Management
│   ├── header.inc.php      # Standard-Header
│   ├── lang/               # Sprachdateien (Deutsch/Englisch)
│   ├── artefakt.inc.php    # Artefakt-Funktionen
│   ├── sabotage.inc.php    # Sabotage-Mechaniken
│   └── allyjobs.inc.php    # Allianz-Aufgaben
├── js/                      # JavaScript-Dateien
│   ├── de_fn.js            # Hauptfunktionen
│   ├── de_chat.js          # Chat-Funktionen
│   └── jquery-3.7.1.min.js # jQuery-Bibliothek
├── ki/                      # KI/NPC-Systeme
├── lib/                     # Bibliotheken und Hilfsfunktionen
├── npcx/                    # Erweiterte NPC-Logik
├── src/                     # PSR-4 Autoload-Klassen (DieEwigen\DE2)
│   ├── Model/              # Business-Logik-Modelle
│   │   ├── Tick/           # Tick-Verarbeitungslogik
│   │   └── Alliance/       # Allianz-Funktionen
│   ├── Controller/         # (In Entwicklung)
│   ├── View/               # (In Entwicklung)
│   └── Database/           # Datenbankzugriff-Klassen
├── tickler/                 # Tick-Skripte (Cron-Jobs)
│   ├── wt.php              # Wirtschafts-Tick (Ressourcen, Punkte)
│   ├── mt.php              # Militär-Tick (Flottenbewegungen)
│   └── kt.php              # Kampf-Tick (Schlachten)
├── vendor/                  # Composer-Dependencies
├── views/                   # View-Templates
├── composer.json            # PHP-Abhängigkeiten
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
- **Tabelle:** `de_sector`, `de_sector_data`
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
- Tech-IDs:
  - 1-79: Forschungen und Gebäude
  - 80: Kollektoren
  - 81-90: Schiffe
  - 100-109: Verteidigungsanlagen
  - 110: Sonden
  - 111: Agenten

### 5. Flottensystem
- **Tabelle:** `de_user_fleet`
- Jeder Spieler hat 4 Flotten (fleet_id: user_id-0 bis user_id-3)
- Flottenkommandeure sammeln Erfahrung (Angriff/Verteidigung)
- Schiffstypen:
  - Fregatte (e81)
  - Jabo (e82)
  - Zerstörer (e83)
  - Kreuzer (e84)
  - Schlachtschiff (e85)
  - Titan (e90)

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
Das Spiel läuft mit drei verschiedenen Tick-Typen:

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

#### Militärtick (MT)
- **Datei:** `tickler/mt.php`
- Bewegt Flotten
- Aktualisiert Flottenzeiten
- Verarbeitet Ankünfte

#### Kampftick (KT)
- **Datei:** `tickler/kt.php`
- Berechnet Kämpfe
- Generiert Kampfberichte
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
- `de_sector_data`: Spieler in Sektoren
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
$GLOBALS['env_host'] = 'localhost';          // DB-Host
$GLOBALS['env_user'] = 'root';               // DB-User
$GLOBALS['env_pw'] = 'password';             // DB-Passwort
$GLOBALS['env_db'] = 'de_database';          // DB-Name
$GLOBALS['env_admin_email'] = 'admin@example.com';
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
Für Produktionsserver (Linux):
```bash
# Minütlich alle Tick-Skripte aufrufen
* * * * * /usr/bin/php /pfad/zu/de2/tickler/wt.php
* * * * * /usr/bin/php /pfad/zu/de2/tickler/mt.php
* * * * * /usr/bin/php /pfad/zu/de2/tickler/kt.php
```

Für Windows (Task Scheduler):
- Erstelle Tasks für wt.php, mt.php, kt.php
- Trigger: Minütlich
- Aktion: `php.exe C:\pfad\zu\tickler\wt.php`

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

// Legacy (noch vorhanden, aber zu vermeiden)
$result = mysqli_query($GLOBALS['dbi'], 
    "SELECT * FROM de_user_data WHERE user_id='$user_id'"
);
```

### SCSS-Kompilierung
CSS wird aus SCSS generiert:
```bash
# Installation von SASS (einmalig)
npm install -g sass

# Kompilierung
sass gp/de-main.scss gp/de-main.css
sass gp/de-chat.scss gp/de-chat.css
sass gp/de-map.scss gp/de-map.css
```

### JavaScript-Minifizierung
```bash
# Installation von UglifyJS (einmalig)
npm install -g uglify-js

# Minifizierung
uglifyjs js/de_fn.js -o js/de_fn.min.js
uglifyjs js/de_chat.js -o js/de_chat.min.js
```

## Wichtige Funktionen und Helper

### In functions.php
- `loadPlayerTechs($uid)`: Lädt Technologien eines Spielers
- `hasTech($techs, $tech_id)`: Prüft, ob Spieler Tech hat
- `change_storage_amount($uid, $item_id, $amount)`: Ändert Item-Menge
- `insert_chat_msg($channel, $type, $username, $message)`: Chat-Nachricht
- `get_player_allyid($uid)`: Gibt Allianz-ID zurück
- `createTitleForUser($owner_id, $title)`: Erstellt Spieler-Titel
- `mail_smtp($to, $subject, $body)`: Versendet E-Mails

### In inc/allyjobs.inc.php
- Allianz-Aufgaben-Definitionen
- `$allyjobs` Array mit Quest-Typen

### In inc/artefakt.inc.php
- `getArtefactAmountByUserId($uid, $art_id)`: Artefakt-Anzahl
- Artefakt-Effekt-Berechnungen

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
- (In Entwicklung) Token-basiertes System

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
- Schrittweise Umstellung auf OOP/PSR-4
- `controllers/` und `views/` Ordner noch leer

### Sicherheit
- Passwort-Hashing modernisieren (zu `password_hash()`)
- CSRF-Tokens implementieren
- Input-Validierung vereinheitlichen

### Features in Entwicklung
- REST-API vervollständigen
- WebSocket für Echtzeit-Chat
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

**Letzte Aktualisierung:** 28. Oktober 2025

Für weitere Fragen siehe README.md oder kontaktiere das Entwicklerteam.
