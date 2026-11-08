<?php
/**
 * Kompatibilitäts-Wrapper: die eigentliche Logik (Auth, Levelprüfung,
 * Logging, Helfer) liegt in inc.bootstrap.php.
 * Die frühere Register-Globals-Emulation (foreach $_REQUEST => $$key)
 * wurde entfernt — Seiten lesen Parameter explizit via req_int()/req_str().
 */
require_once __DIR__ . '/inc.bootstrap.php';
