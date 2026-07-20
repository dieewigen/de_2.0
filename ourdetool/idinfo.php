<?php
/**
 * Redirect-Shim: idinfo.php war früher das Frameset (Suchleiste + Anzeige).
 * Bestehende Links idinfo.php?UID=... aus Listen und externe Links
 * landen jetzt direkt auf der Info-Seite des Users.
 */
$u = (int)($_REQUEST['UID'] ?? ($_REQUEST['uid'] ?? 0));
header('Location: ' . ($u > 0 ? 'info.php?uid=' . $u : 'index.php'));
exit;
