<?php
/**
 * Layout-Kopf: Doctype, <head>, Sidebar-Navigation, öffnet <main>.
 *
 * Erwartet, dass det_userdata.inc.php (Auth) bereits eingebunden wurde.
 * Optionale Variablen vor dem Include setzen:
 *   $page_title      – Seitentitel (Plain-Text)
 *   $active_nav      – Nav-Schlüssel aus inc.nav.php (Default: per Script-Name)
 *   $page_head_extra – zusätzliches rohes HTML für den <head> (Scripte/Styles)
 *   $layout_base     – Pfad-Präfix zum ourdetool-Root für Seiten in
 *                      Unterordnern (z.B. '../' aus comparetool/)
 *
 * Abschluss der Seite mit include "inc.layout.bottom.php".
 */
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
$GLOBALS['__layout_t0'] = microtime(true);

$nav_groups = include __DIR__ . '/inc.nav.php';
$page_title = $page_title ?? 'Admintool';
$layout_base = $layout_base ?? '';

if (!isset($active_nav)) {
    $active_nav = '';
    $script_base = basename($_SERVER['SCRIPT_NAME'] ?? '');
    foreach ($nav_groups as $nav_items) {
        foreach ($nav_items as $nav_key => $nav_item) {
            if (strtok($nav_item[0], '?') === $script_base) {
                $active_nav = $nav_key;
                break 2;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($page_title) ?> – DE Admintool</title>
<link rel="stylesheet" href="<?= $layout_base ?>adm.css">
<script src="<?= $layout_base ?>adm.js" defer></script>
<?= $page_head_extra ?? '' ?>
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-brand"><a href="<?= $layout_base ?>index.php">DE&nbsp;<span>Admintool</span></a></div>
  <nav class="sidebar-nav">
<?php foreach ($nav_groups as $nav_group => $nav_items): ?>
    <div class="nav-group">
      <div class="nav-group-title"><?= $nav_group ?></div>
<?php foreach ($nav_items as $nav_key => $nav_item): ?>
      <a href="<?= $layout_base . $nav_item[0] ?>"<?= $nav_key === $active_nav ? ' class="active" aria-current="page"' : '' ?>><?= $nav_item[1] ?></a>
<?php endforeach; ?>
    </div>
<?php endforeach; ?>
  </nav>
  <div class="sidebar-user">
    <?= htmlspecialchars($det_username ?? '') ?><br>
    <span class="dim">Level <?= (int)($det_userlevel ?? 0) ?></span>
  </div>
</aside>
<main class="main">
<h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
