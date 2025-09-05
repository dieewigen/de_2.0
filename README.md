
# Die Ewigen 2.0 Source Code

Das www.die-ewigen.com Github Repo ist unter https://github.com/dieewigen zu finden.


## Softwarevorraussetzungen 
      Webserver, PHP, MySQL/MariaDB
      alles direkt zusammen installierbar z.B. via     XAMPP(https://www.apachefriends.org/de/index.html)


## Erste Schritte zum Start

### Datenbank aufsetzen 
Voraussetzung: MySQL / MariaDB Datenbanksystem installiert

	- Schritt 1: Datenbank erstellen (eine pro Server) z.B. tde
	- Schritt 2: Datenbankschema / Datenbank durch die.sql files unter https://github.com/dieewigen/de_2.0/tree/master/database erstellen.
	- Schritt 3: de.sql Datei für die Hauptspieldatenbank verwenden

### Parameter setzen und DBs verlinken

Extrem viele der Spiel-Parameter können in den files in https://github.com/dieewigen/de_2.0/tree/master/inc gesetzt werden.

Insbesondere für für eine laufenden Server ist es aber wichtig die Datenbank Zugangsdaten und Adressen zu setzen dies ist in inc/env.inc.php zu machen, eine Beispieldatei findet man unter inc/env.inc.sample.php.


### Generiere Accounts
Im Ordner ki ist das Script: generiereaccounts.php Dort die Anzahl von NPC 1 festlegen, die in Sektor 2 landen sollen. Wichtig ist dabei, dass in der sv.inc.php der Wert $sv_maxsystem groß genug ist um alle Aliens in Sektor 2 unterbringen zu können, sonst landet er in einer Endlosschleife.

### Tickscripts starten
Die Ticks werden über cronjobs gesteuert, die jede Minute die shell scripts (.sh files) in https://github.com/dieewigen/de_2.0/tree/master/tickler/ aufrufen
 
Müssen die cronjobs entsprechend den Ticks getimed werden?
( DE Isso: früher ja, inzwischen einfach minütlich aufrufen und in inc/sv.inc.php die Zeiten setzen)




## Verschiedene Server 

Jeder Server muss mit der beschrieben Prozedur angelegt werden. Also erst DBs für Server erstellen, Parameter in PHP scripten einstellen, accs anlegen, cronjobs starten



## Runden Reset
Man kann sich an tickler/wt_auto_reset.php orientieren



## Verfügbare Repositories
	- Das eigentliche Spiel an sich: https://github.com/dieewigen/de_2.0
	- Techtree Editor für DE: https://github.com/dieewigen/techtree_editor
	- Editor um die initiale Karte des VS - Vergessenen Systeme Spieles zu erstellen 
	https://github.com/dieewigen/vs_starmap_editor


## Nutzung von composer
### Composer Dependencies installieren:###
   ```bash
   composer install
   ```
### Was passiert bei der Installation

- Alle in `composer.json` definierten Pakete werden heruntergeladen
- Das `vendor/` Verzeichnis wird erstellt
- Der Autoloader wird generiert (`vendor/autoload.php`)

### Bibliotheken aktualisieren

Um alle Pakete auf die neuesten Versionen zu aktualisieren:
```bash
composer update
```