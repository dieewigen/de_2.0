<?php
/**
 * Reiner POST-Endpoint ohne Layout: sperrt einen Account (status=2),
 * vermerkt die Direktsperrung im Kommentar und leitet zur aufrufenden
 * Liste zurück. Wird von den Listen-Seiten per Inline-Formular
 * (Muster siehe lastreg.php) mit CSRF-Token aufgerufen.
 */
include "../inccon.php";
include "det_userdata.inc.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Nur per POST aufrufbar.');
}
csrf_require();

$uid = req_int('uid');
if ($uid <= 0) {
    die('Fehler beim Scriptaufruf.');
}

// Benutzer sperren
mysqli_execute_query(
    $GLOBALS['dbi'],
    "UPDATE de_login SET status=2, supporter=? WHERE user_id=?",
    [$det_email, $uid]
);

// Direktsperrung im Kommentar vermerken
$time = date("Y-m-d H:i:s");
$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT kommentar FROM de_user_info WHERE user_id=?",
    [$uid]
);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $eintrag = $row['kommentar'] . "\nDirektsperrung von " . $det_username . " über die Multiliste! \n" . $time;
    mysqli_execute_query(
        $GLOBALS['dbi'],
        "UPDATE de_user_info SET kommentar=? WHERE user_id=?",
        [$eintrag, $uid]
    );
}

// Zurück zur aufrufenden Seite: aus ret nur Dateiname + Query übernehmen
$ret = req_str('ret');
$path = (string)parse_url($ret, PHP_URL_PATH);
$query = (string)parse_url($ret, PHP_URL_QUERY);
$base = basename($path);
if (preg_match('/^[a-z0-9_.]+\.php$/i', $base)) {
    header('Location: ' . $base . ($query !== '' ? '?' . $query : ''));
} else {
    header('Location: index.php');
}
exit;
