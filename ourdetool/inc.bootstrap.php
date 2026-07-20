<?php
/**
 * Zentraler Bootstrap des Admintools.
 *
 * Übernimmt (in dieser Reihenfolge):
 *  - Fehler-Konfiguration
 *  - Identität aus Basic Auth + Userlevel-Prüfung gegen <seite>.lvl
 *  - Request-Logging nach logs/<name>.txt (Format unverändert)
 *  - Parameter-Helfer req_int()/req_str() und CSRF-Funktionen
 *
 * Userlevel-Semantik (historisch): niedrigerer Wert = mehr Rechte.
 * Zugriff nur, wenn Userlevel <= Level aus der .lvl-Datei der Seite.
 */

error_reporting(E_ALL);
ini_set('display_errors', ($_SERVER['SERVER_NAME'] ?? '') === 'xde.bgam.es' ? '1' : '0');
ini_set('log_errors', '1');

$disablegzip = 1;

// Identität aus Basic Auth (Apache: REMOTE_USER, CGI-Fallback: PHP_AUTH_USER)
$det_username = $_SERVER['REMOTE_USER'] ?? ($_SERVER['PHP_AUTH_USER'] ?? '');
if ($det_username === ''
    || !preg_match('/^[A-Za-z0-9_.-]+$/', $det_username)
    || !is_file(__DIR__ . '/user/' . $det_username . '.txt')) {
    http_response_code(403);
    die('Unbekannter Benutzer.');
}

$det_userdaten = file(__DIR__ . '/user/' . $det_username . '.txt');
$det_email = trim($det_userdaten[0] ?? '');
$det_userlevel = (int)trim($det_userdaten[1] ?? '99');
unset($det_userdaten);

if (($_SERVER['SERVER_NAME'] ?? '') == 'xde.bgam.es') {
    if ($det_email == 'cannopio@die-ewigen.com') {
        $det_userlevel = 99;
    }
}

// Erforderlichen Level der aufgerufenen Seite aus <seite>.lvl lesen.
// Die .lvl-Datei liegt neben dem aufgerufenen Script (auch in Unterordnern
// wie comparetool/). Fehlende .lvl-Datei = Level 0 (nur Level-0-Benutzer).
$det_lvlfile = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . basename($_SERVER['SCRIPT_FILENAME'], '.php') . '.lvl';
$file_userlevel = is_file($det_lvlfile) ? (int)trim((string)file_get_contents($det_lvlfile)) : 0;
if ($det_userlevel > $file_userlevel) {
    http_response_code(403);
    die('<b>Du hast nicht den n&ouml;tigen Userlevel f&uuml;r diese Seite.</b>');
}

// Aktion protokollieren (Dateiformat identisch zum alten det_userdata.inc.php)
$det_logstring = '';
foreach (["Post:" => $_POST, "Get:" => $_GET] as $det_setname => $det_set) {
    $det_logstring .= $det_setname . "\n";
    foreach ($det_set as $det_key => $det_val) {
        if (is_array($det_val)) {
            $det_val = json_encode($det_val);
        }
        $det_logstring .= $det_key . " => " . $det_val . "\n";
    }
}
$det_logstring = "Zeit: " . date("Y-m-d H:i:s") . "\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? '')
    . "\nDatei: " . $_SERVER['PHP_SELF'] . "\n" . $det_logstring
    . "\n--------------------------------------\n";
@file_put_contents(__DIR__ . '/logs/' . $det_username . '.txt', $det_logstring, FILE_APPEND);
unset($det_logstring, $det_setname, $det_set, $det_key, $det_val);

/** Request-Parameter als Integer (GET/POST), Ersatz für die alte $$-Registrierung. */
function req_int(string $key, int $default = 0): int
{
    $v = $_REQUEST[$key] ?? $default;
    return is_scalar($v) ? (int)$v : $default;
}

/** Request-Parameter als String (GET/POST), Ersatz für die alte $$-Registrierung. */
function req_str(string $key, string $default = ''): string
{
    $v = $_REQUEST[$key] ?? $default;
    return is_scalar($v) ? (string)$v : $default;
}

/**
 * CSRF-Schutz: zustandsloses HMAC-Token, gebunden an den Basic-Auth-Benutzer.
 * Das Tool nutzt keine Sessions, daher ein dateibasiertes Secret
 * (.ht_csrf_secret wird von Apache nicht ausgeliefert und ist gitignored).
 */
function csrf_secret(): string
{
    static $secret = null;
    if ($secret === null) {
        $file = __DIR__ . '/.ht_csrf_secret';
        if (!is_file($file)) {
            file_put_contents($file, bin2hex(random_bytes(32)), LOCK_EX);
        }
        $secret = trim((string)file_get_contents($file));
    }
    return $secret;
}

function csrf_token(): string
{
    $user = $_SERVER['REMOTE_USER'] ?? ($_SERVER['PHP_AUTH_USER'] ?? '');
    return hash_hmac('sha256', 'ourdetool|' . $user, csrf_secret());
}

/** Hängt das CSRF-Token als t=... an eine URL an (für Aktions-Links). */
function csrf_url(string $url): string
{
    return $url . (str_contains($url, '?') ? '&' : '?') . 't=' . csrf_token();
}

/** Verstecktes Formularfeld mit dem CSRF-Token. */
function csrf_field(): string
{
    return '<input type="hidden" name="t" value="' . csrf_token() . '">';
}

/** Bricht mit 403 ab, wenn das CSRF-Token fehlt oder nicht stimmt. */
function csrf_require(): void
{
    if (!hash_equals(csrf_token(), (string)($_REQUEST['t'] ?? ''))) {
        http_response_code(403);
        die('Ung&uuml;ltiger oder fehlender Sicherheits-Token. Bitte die Aktion erneut &uuml;ber das Tool ausf&uuml;hren.');
    }
}
