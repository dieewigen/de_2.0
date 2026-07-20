<?php
/**
 * User-Kontext-Toolbar für alle Detailseiten eines Users
 * (Ersatz für das frühere idinfo.php-Frameset mit Suchleiste).
 *
 * Erwartet: $uid (int) – die betrachtete User-ID; $GLOBALS['dbi'] offen.
 * Optional: $active_usertab – Tab-Schlüssel (Default: Script-Basename).
 * Einbinden direkt nach inc.layout.top.php.
 */
$uid = (int)($uid ?? 0);
$user_tabs = [
    'info'              => ['info.php', 'Info'],
    'tf'                => ['tf.php', 'Hyperfunk'],
    'nachrichten'       => ['nachrichten.php', 'Nachrichten'],
    'de_user_getcol'    => ['de_user_getcol.php', 'Kollektoren'],
    'de_user_ip'        => ['de_user_ip.php', 'IPs'],
    'techs'             => ['techs.php', 'Technologien'],
    'sektorstatus'      => ['sektorstatus.php', 'Sektorstatus'],
    'de_user_logviewer' => ['de_user_logviewer.php', 'Logviewer'],
    'de_user_logsearch' => ['de_user_logsearch.php', 'Logsuche'],
    'de_user_stat'      => ['de_user_stat.php', 'Statistik'],
    'de_user_chat'      => ['de_user_chat.php', 'Chat'],
    'mails'             => ['mails.php', 'Mail'],
    'de_user_delete'    => ['de_user_delete.php', 'L&ouml;schen'],
];
$active_usertab = $active_usertab ?? basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
?>
<div class="usertoolbar">
  <form action="de_user_search.php" method="get" class="usersearch">
    <input type="text" name="sstr" placeholder="+ID &nbsp;*Name &nbsp;%Mail &nbsp;ksec:sys &nbsp;?Wildcard"
           title="+ ID, * Spielername, % Mail, ksec:sys, ?[-*%~|#] Wildcard">
    <button type="submit">Suchen</button>
  </form>
<?php if ($uid > 0): ?>
<?php
    $utb_name = '';
    $utb_status = null;
    $utb_sector = null;
    if (isset($GLOBALS['dbi'])) {
        $utb_res = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT d.spielername, d.sector, l.status FROM de_user_data d LEFT JOIN de_login l ON l.user_id=d.user_id WHERE d.user_id=?",
            [$uid]
        );
        if ($utb_res && ($utb_row = mysqli_fetch_array($utb_res))) {
            $utb_name = (string)$utb_row['spielername'];
            $utb_sector = $utb_row['sector'];
            $utb_status = $utb_row['status'];
        }
    }
    $utb_badge = '';
    if ($utb_status !== null) {
        $utb_badge = match ((int)$utb_status) {
            0 => '<span class="badge">inaktiv</span>',
            1 => '<span class="badge badge-ok">aktiv</span>',
            2 => '<span class="badge badge-danger">gesperrt</span>',
            3 => '<span class="badge badge-warn">Urlaub</span>',
            default => '<span class="badge">Status ' . (int)$utb_status . '</span>',
        };
    }
?>
  <div class="userident">
    <strong>UID <?= $uid ?></strong>
    <?php if ($utb_name !== ''): ?>&middot; <?= htmlspecialchars($utb_name) ?><?php endif; ?>
    <?= $utb_badge ?>
  </div>
  <nav class="usertabs">
<?php foreach ($user_tabs as $utb_key => $utb_item): ?>
    <a href="<?= $utb_item[0] ?>?uid=<?= $uid ?>"<?= $utb_key === $active_usertab ? ' class="active"' : '' ?><?= $utb_key === 'de_user_delete' ? ' data-confirm="User ' . $uid . ' wirklich zum L&ouml;schen aufrufen?"' : '' ?>><?= $utb_item[1] ?></a>
<?php endforeach; ?>
  </nav>
<?php if ($utb_sector !== null && isset($GLOBALS['dbi'])): ?>
  <div class="usersector">
    Sektor <?= (int)$utb_sector ?> &ndash; Spieler:
<?php
    $utb_res = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id, system FROM de_user_data WHERE sector=? ORDER BY system",
        [$utb_sector]
    );
    while ($utb_res && ($utb_row = mysqli_fetch_array($utb_res))) {
        $utb_cls = ((int)$utb_row['user_id'] === $uid) ? ' class="active"' : '';
        echo '<a' . $utb_cls . ' href="info.php?uid=' . (int)$utb_row['user_id'] . '" target="_blank" rel="noopener">' . (int)$utb_row['system'] . '</a> ';
    }
?>
  </div>
<?php endif; ?>
<?php endif; ?>
</div>
