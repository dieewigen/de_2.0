<?php
//Prüfwerkzeug für Umbauten: führt Spielseiten wie ein eingeloggter Spieler gegen Kopien der lokalen Datenbanken aus und
//vergleicht Ausgabe und Datenbankstand vor und nach einer Änderung. Nur für die lokale Entwicklung (z. B. XAMPP) und nur
//auf der Kommandozeile; über den Webserver liefert die Datei nichts, zusätzlich sperrt tests/.htaccess den Ordner.
//
//  php tests/testlauf.php aufnehmen vorher     alle Fälle ausführen, Ergebnis unter <Temp>/de2-testlauf/vorher
//  (Code ändern)
//  php tests/testlauf.php aufnehmen nachher
//  php tests/testlauf.php vergleich vorher nachher
//  php tests/testlauf.php seite 1 recycling.php POST recyclingbutton=1 e81=5 t81=82
//                                              eine Seite: Fehler, Warnungen, geänderte Tabellen, HTML zum Ansehen
//
//Ist der Code schon geändert, die Basis nach "git stash" aufnehmen und danach "git stash pop" ausführen.
//Jeder Fall beginnt mit frischen Kopien der Spiel- und der Login-Datenbank (TL_TESTDB, TL_TESTDB_LOGIN; sie bleiben zum
//Nachsehen stehen). Die Seite landet in den Kopien, weil das Werkzeug die Datenbanknamen aus inc/env.inc.php überschreibt,
//bevor die Seite die Datei mit include_once lädt; greift das nicht, bricht es ab. Mails gehen ins Leere, mt_rand ist fest
//eingestellt, damit zwei Läufe vergleichbar sind. Nicht abgedeckt: die Ticks (der WT verschickt Mails) und das
//Aktionsprotokoll (bei sv_log_player_actions=1 schreibt inccon.php in die echte Protokoll-Datenbank).

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('TL_WURZEL', dirname(__DIR__));
const TL_TESTDB = 'de_test';
const TL_TESTDB_LOGIN = 'de_test_login';
//CSRF-Token der Testsitzung; POST-Aktionen bekommen es als Feld "token", für GET gibt es den Platzhalter {token}
const TL_TOKEN = '0123456789abcdef0123456789abcdef';

//Testspieler der lokalen Entwicklungsdatenbank, bei einer anderen Datenbank anpassen: aktive Spieler mit verschiedenem Stand
//(mit und ohne Allianz, mit und ohne Vergessene Systeme). Die Aktionen laufen mit den ersten beiden, der dritte ist Flottenziel.
const TL_SPIELER = [1, 220, 212];

//Seiten, die jeder Testspieler einmal aufruft; {…} sind Werte aus der Testdatenbank (tl_platzhalter)
const TL_SEITEN = [
    'overview.php', 'production.php', 'recycling.php', 'military.php', 'missions.php', 'resource.php', 'ang_techs.php',
    'specialization.php', 'specialship.php', 'auction.php', 'artefacts.php', 'secret.php', 'sector.php', 'secstatus.php',
    'map.php', 'map_mobile.php', 'map_system.php?id={vs_planet}', 'politics.php', 'feldzug.php', 'sysnews.php', 'hyperfunk.php',
    'details.php', 'bkmenu.php', 'unitinfo.php', 'allymain.php', 'ally_members.php', 'ally_dailygift.php', 'toplist.php',
    'statistics.php', 'newspaper.php', 'options.php', 'sinfo.php',
];

//Aktionen (POST) der ersten beiden Testspieler
const TL_AKTIONEN = [
    'recycling' => ['recycling.php', ['recyclingbutton' => '1', 'e81' => '5', 't81' => '82']],
    'produktion' => ['production.php', ['submit' => '1', 'b81' => '3', 'b100' => '1']],
    'flotte_verlegen' => ['military.php', ['verlegen' => '1', 'm81_1' => '2']],
    //Flottenbefehle in military.php: 1 Heimkehr, 2 Angriff, 3-5 Verteidigung für 1-3 KT; je Fall eine andere Flotte
    'flotte_heimkehr' => ['military.php', ['befehle' => '1', 'af1' => '1']],
    'flotte_angriff' => ['military.php', ['befehle' => '1', 'af2' => '2', 'zsecf2' => '{ziel_sektor}', 'zsysf2' => '{ziel_system}']],
    'flotte_verteidigen' => ['military.php', ['befehle' => '1', 'af3' => '3', 'zsecf3' => '{ziel_sektor}', 'zsysf3' => '{ziel_system}']],
    'forschung' => ['ang_techs.php', ['start_tech' => '{tech_offen}']],
    'kollektor' => ['resource.php', ['b_col' => '1']],
    'vs_automatik' => ['map_mobile.php', ['set_auto_explore' => '1']],
    'vs_erkunden' => ['map_system.php', ['id' => '{vs_nachbar}', 'explore' => '1']],
    'vs_bauen' => ['map_system_ajax.php', ['action' => 'buildall', 'id' => '{vs_planet}']],
    'vs_upgrade' => ['map_system_ajax.php', ['action' => 'upgradeall', 'id' => '{vs_planet}']],
    'mission' => ['missions.php', ['start_mission' => '0', 'fleet_id' => '-1']],
    'allianzbonus' => ['ally_dailygift.php', ['getdailybonus' => '1']],
    'auktion' => ['auction.php', ['bid' => '{auktion}']],
    'artefakt_zerstoeren' => ['artefacts.php', ['destroyartefact' => '1', 'lid' => '{artefakt}']],
];

//nach jedem Fall festgehaltene Zeilen: "?" = user_id des Spielers, "LIKE ?" = seine Flotten, leer = ganze Tabelle
const TL_TABELLEN = [
    'de_user_data' => 'user_id=?', 'de_user_fleet' => 'user_id LIKE ?', 'de_user_build' => 'user_id=?', 'de_user_techs' => 'user_id=?',
    'de_user_mission' => 'user_id=?', 'de_user_storage' => 'user_id=?', 'de_user_map' => 'user_id=?', 'de_user_map_bldg' => 'user_id=?',
    'de_user_artefact' => 'user_id=?', 'de_user_news' => 'user_id=?', 'de_user_getcol' => 'user_id=?', 'de_user_special_ship' => 'user_id=?',
    'de_user_locks' => 'id=?', 'de_auction' => '', 'de_allys' => '', 'de_sector' => 'platz>0', 'de_chat_msg' => '',
    'de_user_scan_history' => 'user_id=?',
];

////////////////////////////////////////////////////////////////////////////////
// eine Seite ausführen (eigener Prozess je Fall, von tl_fall gestartet)
////////////////////////////////////////////////////////////////////////////////
if (($argv[1] ?? '') === '_lauf') {
    //die Seite läuft im globalen Bereich wie beim Webserver, daher hier möglichst wenige eigene Variablen
    $tl = json_decode(base64_decode($argv[2] ?? ''), true);
    chdir(TL_WURZEL);
    require TL_WURZEL.'/inc/env.inc.php';
    $GLOBALS['env_db_dieewigen_database'] = TL_TESTDB;
    $GLOBALS['env_db_loginsystem_database'] = TL_TESTDB_LOGIN;
    $GLOBALS['env_mail_server'] = 'smtp.invalid';
    //die Seiten laden env.inc.php mit include_once; ist sie für PHP schon geladen, bleiben die Testnamen stehen
    include_once 'inc/env.inc.php';
    if ($GLOBALS['env_db_dieewigen_database'] !== TL_TESTDB || $GLOBALS['env_db_loginsystem_database'] !== TL_TESTDB_LOGIN) {
        fwrite(STDERR, "inc/env.inc.php wird erneut geladen, die Testdatenbank greift nicht.\n");
        exit(3);
    }
    $tl_db = new mysqli($GLOBALS['env_db_dieewigen_host'], $GLOBALS['env_db_dieewigen_user'], $GLOBALS['env_db_dieewigen_password'], TL_TESTDB);
    $tl_spieler = mysqli_fetch_assoc(mysqli_execute_query($tl_db, "SELECT spielername, rasse FROM de_user_data WHERE user_id=?", [$tl['uid']]));
    $tl_db->close();

    //Sitzung wie nach dem Login, ohne Botschutzabfrage und ohne Aktivitätsbonus (sonst ändert jeder Aufruf de_user_data)
    $_SESSION = ['ums_user_id' => $tl['uid'], 'ums_spielername' => $tl_spieler['spielername'] ?? '', 'ums_rasse' => (int)($tl_spieler['rasse'] ?? 1),
        'ums_mobi' => 0, 'de_frameset' => 0, 'ums_owner_id' => 0, 'ums_session_start' => time(), 'ums_zeitstempel' => time(),
        'aktivitaet_time' => time(), 'ums_vote' => 0, 'csrf_token' => TL_TOKEN];
    $givenocredit = 1;
    $_SERVER = array_merge($_SERVER, ['REMOTE_ADDR' => '127.0.0.1', 'REQUEST_METHOD' => $tl['methode'], 'SCRIPT_NAME' => '/'.$tl['seite'],
        'PHP_SELF' => '/'.$tl['seite'], 'REQUEST_URI' => '/'.$tl['seite'], 'QUERY_STRING' => http_build_query($tl['get']), 'HTTP_HOST' => 'localhost',
        'SERVER_NAME' => 'localhost', 'HTTPS' => 'on', 'HTTP_USER_AGENT' => 'testlauf', 'DOCUMENT_ROOT' => TL_WURZEL]);
    $_GET = $tl['get'];
    $_POST = $tl['post'];
    $_REQUEST = array_merge($_GET, $_POST);
    mt_srand(1);

    $GLOBALS['tl_warnungen'] = [];
    set_error_handler(function ($nr, $text, $datei, $zeile) {
        $GLOBALS['tl_warnungen'][] = $text.' @'.basename($datei).':'.$zeile;
        return true;
    });
    //Seiten enden oft mit die() oder exit, deshalb wird das Ergebnis beim Beenden geschrieben
    register_shutdown_function(function () use ($tl) {
        $out = '';
        while (ob_get_level() > 0) {
            $out = ob_get_clean().$out;
        }
        $e = error_get_last();
        $fatal = ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) ? $e['message'].' @'.basename($e['file']).':'.$e['line'] : null;
        if (($GLOBALS['env_db_dieewigen_database'] ?? '') !== TL_TESTDB) {
            $fatal = 'inc/env.inc.php wurde von der Seite erneut geladen, Zugriffe können an die echte Datenbank gegangen sein. '.$fatal;
        }
        file_put_contents($tl['ausgabe'], json_encode(['out' => $out, 'warnungen' => array_values(array_unique($GLOBALS['tl_warnungen'])), 'fatal' => $fatal],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR));
    });
    define('TL_SEITE', $tl['pfad']);
    unset($tl, $tl_db, $tl_spieler);
    ob_start();
    include TL_SEITE;
    exit;
}

////////////////////////////////////////////////////////////////////////////////
// Befehle
////////////////////////////////////////////////////////////////////////////////
require TL_WURZEL.'/inc/env.inc.php';
$befehl = $argv[1] ?? '';
if ($befehl === 'aufnehmen' && isset($argv[2])) {
    tl_aufnehmen($argv[2]);
} elseif ($befehl === 'vergleich' && isset($argv[3])) {
    exit(tl_vergleich($argv[2], $argv[3]) ? 0 : 1);
} elseif ($befehl === 'seite' && isset($argv[3])) {
    tl_einzeln(array_slice($argv, 2));
} else {
    //Hilfe: der Kopfkommentar dieser Datei
    preg_match('~\A<\?php\R((?://.*\R)+)~', file_get_contents(__FILE__), $kopf);
    echo preg_replace('~^//~m', '', $kopf[1] ?? '');
    exit(1);
}

function tl_abbruch(string $text): void
{
    fwrite(STDERR, $text."\n");
    exit(2);
}

//Verbindung mit den Zugangsdaten aus inc/env.inc.php ('spiel' oder 'login'), ohne gewählte Datenbank
function tl_verbindung(string $welche): mysqli
{
    $p = $welche === 'login' ? 'env_db_loginsystem_' : 'env_db_dieewigen_';
    $db = new mysqli($GLOBALS[$p.'host'], $GLOBALS[$p.'user'], $GLOBALS[$p.'password']);
    $db->set_charset('utf8mb4');

    return $db;
}

//frische Kopien von Spiel- und Login-Datenbank; sehr große Tabellen (Patchnotes der Accountverwaltung) nur als Struktur
function tl_kopieren(): void
{
    foreach (['spiel' => TL_TESTDB, 'login' => TL_TESTDB_LOGIN] as $welche => $ziel) {
        $quelle = $GLOBALS[$welche === 'login' ? 'env_db_loginsystem_database' : 'env_db_dieewigen_database'];
        if ($quelle === '' || $quelle === $ziel) {
            tl_abbruch("Die Testdatenbank $ziel darf nicht die eigentliche Datenbank sein.");
        }
        $db = tl_verbindung($welche);
        $db->query("DROP DATABASE IF EXISTS `$ziel`");
        $db->query("CREATE DATABASE `$ziel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $res = mysqli_execute_query($db, "SELECT TABLE_NAME AS name, DATA_LENGTH AS groesse FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_TYPE='BASE TABLE'", [$quelle]);
        foreach (mysqli_fetch_all($res, MYSQLI_ASSOC) as $t) {
            $db->query("CREATE TABLE `$ziel`.`{$t['name']}` LIKE `$quelle`.`{$t['name']}`");
            if ($t['groesse'] < 10485760) {
                $db->query("INSERT INTO `$ziel`.`{$t['name']}` SELECT * FROM `$quelle`.`{$t['name']}`");
            }
        }
        $db->close();
    }
}

//Werte aus der Testdatenbank für die Platzhalter {…} in Seiten und Aktionen
function tl_platzhalter(mysqli $db, int $uid, string $text): string
{
    $abfragen = [
        '{vs_planet}' => ["SELECT MIN(map_id) FROM de_user_map_bldg WHERE user_id=? AND field_id=0 AND bldg_id=1", [$uid]],
        '{vs_nachbar}' => ["SELECT MIN(k.knoten_id2) FROM de_map_kanten k JOIN de_user_map m ON m.map_id=k.knoten_id1 AND m.user_id=?
            WHERE k.knoten_id2 NOT IN (SELECT map_id FROM de_user_map WHERE user_id=?)", [$uid, $uid]],
        '{tech_offen}' => ["SELECT MIN(tech_id) FROM de_tech_data WHERE tech_id<80 AND tech_id NOT IN (SELECT tech_id FROM de_user_techs WHERE user_id=?)", [$uid]],
        '{ziel_sektor}' => ["SELECT sector FROM de_user_data WHERE user_id=?", [TL_SPIELER[2]]],
        '{ziel_system}' => ["SELECT `system` FROM de_user_data WHERE user_id=?", [TL_SPIELER[2]]],
        '{auktion}' => ["SELECT MIN(id) FROM de_auction WHERE bidder=0", []],
        '{artefakt}' => ["SELECT MIN(lid) FROM de_user_artefact WHERE user_id=?", [$uid]],
    ];

    return preg_replace_callback('~\{[a-z_]+\}~', function ($m) use ($db, $abfragen) {
        if ($m[0] === '{token}') {
            return TL_TOKEN;
        }
        if (!isset($abfragen[$m[0]])) {
            tl_abbruch('Unbekannter Platzhalter '.$m[0]);
        }
        $zeile = mysqli_fetch_row(mysqli_execute_query($db, $abfragen[$m[0]][0], $abfragen[$m[0]][1]));

        return (string)($zeile[0] ?? '');
    }, $text);
}

//ein Fall: frische Kopien, Seite in eigenem Prozess ausführen, Tabellen des Spielers vorher und nachher festhalten
function tl_fall(int $uid, string $seite, string $methode, array $daten): array
{
    tl_kopieren();
    $db = tl_verbindung('spiel');
    $db->select_db(TL_TESTDB);
    [$datei, $query] = array_pad(explode('?', tl_platzhalter($db, $uid, $seite), 2), 2, '');
    parse_str($query, $get);
    foreach ($daten as $feld => $wert) {
        $daten[$feld] = tl_platzhalter($db, $uid, (string)$wert);
    }
    $pfad = realpath(TL_WURZEL.'/'.$datei);
    if ($pfad === false || strpos($pfad, realpath(TL_WURZEL)) !== 0 || substr($pfad, -4) !== '.php') {
        tl_abbruch('Seite nicht gefunden: '.$datei);
    }
    //Seiten, die env.inc.php ohne include_once laden (z. B. rpc.php), würden an der Testdatenbank vorbei schreiben
    if (preg_match('~\b(include|require)\s*\(?\s*["\'][^"\']*env\.inc\.php~', file_get_contents($pfad))) {
        tl_abbruch($datei.' lädt inc/env.inc.php ohne include_once und lässt sich nicht gegen die Testdatenbank ausführen.');
    }
    if ($methode === 'POST') {
        $post = $daten + ['token' => TL_TOKEN];
    } else {
        $get = $daten + $get;
        $post = [];
    }

    $vorher = tl_tabellen($db, $uid);
    $ausgabe = tempnam(sys_get_temp_dir(), 'tl');
    $auftrag = base64_encode(json_encode(['uid' => $uid, 'seite' => $datei, 'pfad' => $pfad, 'methode' => $methode, 'get' => $get, 'post' => $post, 'ausgabe' => $ausgabe]));
    //Mails ins Leere: unter Windows über einen nicht erreichbaren SMTP-Server, sonst über ein sendmail, das nichts tut
    $mail = PHP_OS_FAMILY === 'Windows' ? '-d SMTP=smtp.invalid' : '-d sendmail_path=/bin/true';
    exec(escapeshellarg(PHP_BINARY).' '.$mail.' '.escapeshellarg(__FILE__).' _lauf '.$auftrag.' 2>&1', $meldungen, $rc);
    if ($rc === 3) {
        tl_abbruch(implode("\n", $meldungen));
    }
    $erg = json_decode((string)file_get_contents($ausgabe), true);
    unlink($ausgabe);
    if (!is_array($erg)) {
        $erg = ['out' => '', 'warnungen' => [], 'fatal' => 'kein Ergebnis (Rückgabe '.$rc.'): '.implode(' ', $meldungen)];
    }
    $erg['daten'] = ['get' => $get, 'post' => $post];
    $erg['db'] = tl_tabellen($db, $uid);
    $erg['geaendert'] = tl_aenderungen($vorher, $erg['db']);
    $db->close();

    return $erg;
}

function tl_tabellen(mysqli $db, int $uid): array
{
    $stand = [];
    foreach (TL_TABELLEN as $tabelle => $bedingung) {
        $werte = strpos($bedingung, 'LIKE') !== false ? [$uid.'-%'] : (strpos($bedingung, '?') !== false ? [$uid] : []);
        $sql = 'SELECT * FROM `'.$tabelle.'`'.($bedingung !== '' ? ' WHERE '.$bedingung : '');
        $stand[$tabelle] = mysqli_fetch_all(mysqli_execute_query($db, $sql, $werte), MYSQLI_ASSOC);
    }

    return $stand;
}

//je Tabelle die Zahl neuer und weggefallener Zeilen; eine geänderte Zeile zählt als beides
function tl_aenderungen(array $vorher, array $nachher): array
{
    $liste = [];
    foreach ($nachher as $tabelle => $zeilen) {
        $a = array_map('json_encode', $vorher[$tabelle]);
        $b = array_map('json_encode', $zeilen);
        $neu = count(array_diff($b, $a));
        $weg = count(array_diff($a, $b));
        if ($neu > 0 || $weg > 0) {
            $liste[] = $tabelle.' +'.$neu.'/-'.$weg;
        }
    }

    return $liste;
}

//alle Fälle: Name => [Spieler, Seite, Methode, Daten]
function tl_faelle(): array
{
    $faelle = [];
    foreach (TL_SPIELER as $uid) {
        foreach (TL_SEITEN as $seite) {
            $faelle['get_'.basename(explode('?', $seite)[0], '.php').'_'.$uid] = [$uid, $seite, 'GET', []];
        }
    }
    foreach (array_slice(TL_SPIELER, 0, 2) as $uid) {
        foreach (TL_AKTIONEN as $name => [$seite, $daten]) {
            $faelle['post_'.$name.'_'.$uid] = [$uid, $seite, 'POST', $daten];
        }
    }

    return $faelle;
}

function tl_ordner(string $name): string
{
    $ordner = sys_get_temp_dir().DIRECTORY_SEPARATOR.'de2-testlauf'.DIRECTORY_SEPARATOR.preg_replace('~[^A-Za-z0-9_-]~', '', $name);
    if (!is_dir($ordner)) {
        mkdir($ordner, 0777, true);
    }

    return $ordner;
}

function tl_aufnehmen(string $name): void
{
    $ordner = tl_ordner($name);
    array_map('unlink', glob($ordner.DIRECTORY_SEPARATOR.'*.json'));
    $start = time();
    $faelle = tl_faelle();
    foreach ($faelle as $fall => [$uid, $seite, $methode, $daten]) {
        $erg = tl_fall($uid, $seite, $methode, $daten);
        file_put_contents($ordner.DIRECTORY_SEPARATOR.$fall.'.json', json_encode($erg, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR));
        //Aktionen mit ihrer Wirkung zeigen: ohne geänderte Tabelle hat eine Aktion meist nicht gegriffen
        if ($erg['fatal'] !== null || $methode === 'POST') {
            echo $fall.': '.($erg['fatal'] !== null ? 'FEHLER '.$erg['fatal'] : (implode(', ', $erg['geaendert']) ?: 'keine Tabelle geändert'))."\n";
        }
    }
    printf("%d Fälle in %d s, abgelegt unter %s\n", count($faelle), time() - $start, $ordner);
}

//entfernt, was sich von Lauf zu Lauf ändert: Uhrzeiten, Daten, Zeitstempel, Tokens, Leerraum
function tl_normal(string $text): string
{
    return preg_replace(
        ['~\b\d{1,2}:\d{2}(:\d{2})?\b~', '~\b\d{2}\.\d{2}\.\d{4}\b~', '~\b\d{4}-\d{2}-\d{2}\b~', '~\b1[6-9]\d{8}\b~', '~\b20\d{12}\b~',
            '~[a-f0-9]{16,64}~', '~kb-quelle-\d+~', '~\s+~'],
        ['HH:MM', 'TT.MM.JJJJ', 'JJJJ-MM-TT', 'ZEIT', 'ZEIT', 'HEX', 'kb-quelle-N', ' '],
        $text
    );
}

//erste Abweichung zweier Texte mit etwas Umgebung, leer wenn gleich
function tl_abweichung(string $x, string $y): string
{
    if ($x === $y) {
        return '';
    }
    $p = 0;
    $n = min(strlen($x), strlen($y));
    while ($p < $n && $x[$p] === $y[$p]) {
        $p++;
    }

    return 'ab Zeichen '.$p.': vorher "'.substr($x, max(0, $p - 50), 140).'" nachher "'.substr($y, max(0, $p - 50), 140).'"';
}

//vergleicht zwei Aufnahmen: Ausgabe, Warnungen, Fehler und Tabellen; true, wenn alles gleich ist
function tl_vergleich(string $a, string $b): bool
{
    $gleich = 0;
    $verschieden = 0;
    foreach (glob(tl_ordner($a).DIRECTORY_SEPARATOR.'*.json') as $datei) {
        $fall = basename($datei, '.json');
        $x = json_decode(file_get_contents($datei), true);
        $y = json_decode((string)@file_get_contents(tl_ordner($b).DIRECTORY_SEPARATOR.$fall.'.json'), true);
        if (!is_array($y)) {
            echo $fall.": fehlt in $b\n";
            $verschieden++;
            continue;
        }
        $befund = [];
        if (($d = tl_abweichung(tl_normal($x['out']), tl_normal($y['out']))) !== '') {
            $befund[] = 'Ausgabe '.$d;
        }
        if ($x['warnungen'] !== $y['warnungen']) {
            $befund[] = 'Warnungen vorher '.json_encode($x['warnungen'], JSON_UNESCAPED_UNICODE).' nachher '.json_encode($y['warnungen'], JSON_UNESCAPED_UNICODE);
        }
        if ($x['fatal'] !== $y['fatal']) {
            $befund[] = 'Fehler vorher '.var_export($x['fatal'], true).' nachher '.var_export($y['fatal'], true);
        }
        foreach ($x['db'] as $tabelle => $zeilen) {
            if (($d = tl_abweichung(tl_normal(json_encode($zeilen)), tl_normal(json_encode($y['db'][$tabelle] ?? null)))) !== '') {
                $befund[] = $tabelle.' '.$d;
            }
        }
        if ($befund) {
            echo $fall.":\n   ".implode("\n   ", $befund)."\n";
            $verschieden++;
        } else {
            $gleich++;
        }
    }
    echo "$gleich gleich, $verschieden verschieden\n";

    return $verschieden === 0;
}

//eine Seite: <user_id> <seite.php[?a=b]> [GET|POST] [feld=wert ...]
function tl_einzeln(array $args): void
{
    $uid = (int)array_shift($args);
    $seite = (string)array_shift($args);
    $methode = in_array(strtoupper($args[0] ?? ''), ['GET', 'POST'], true) ? strtoupper(array_shift($args)) : 'GET';
    $daten = [];
    foreach ($args as $arg) {
        [$feld, $wert] = array_pad(explode('=', $arg, 2), 2, '');
        $daten[$feld] = $wert;
    }
    $erg = tl_fall($uid, $seite, $methode, $daten);
    //mit base-Tag, damit Stylesheets und Bilder beim Öffnen im Browser aus dem Projekt kommen
    $basis = 'file:///'.str_replace('\\', '/', realpath(TL_WURZEL)).'/';
    $html = tl_ordner('seite').DIRECTORY_SEPARATOR.basename(explode('?', $seite)[0], '.php').'.html';
    file_put_contents($html, preg_replace('~<head>~i', '<head><base href="'.$basis.'">', $erg['out'], 1));
    echo 'Ausgabe: '.strlen($erg['out']).' Zeichen, gespeichert unter '.$html."\n";
    echo 'Fehler: '.($erg['fatal'] ?? 'keiner')."\n";
    echo 'Geänderte Tabellen: '.(implode(', ', $erg['geaendert']) ?: 'keine')."\n";
    foreach ($erg['warnungen'] as $warnung) {
        echo '  Warnung: '.$warnung."\n";
    }
}
