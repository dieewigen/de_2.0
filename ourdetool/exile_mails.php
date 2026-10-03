<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Exil-Mails';
include "inc.layout.top.php";

//Mails von Fluxurion (src/Model/Exile), Reihenfolge wie im Ablauf
$mail_types = [
    'vorab'      => 'Vorwarnung',
    'bericht1'   => '1. Lagebericht',
    'bericht2'   => '2. Lagebericht',
    'bericht3'   => 'Letzter Lagebericht',
    'neue_runde' => 'Neue Runde',
];

$type_filter = req_str('type');
if (!isset($mail_types[$type_filter])) {
    $type_filter = '';
}
$login_filter = req_str('login');
if (!in_array($login_filter, ['ja', 'nein'], true)) {
    $login_filter = '';
}

function em_date(?string $dt): string
{
    return $dt ? date('d.m.Y H:i', strtotime($dt)) : '–';
}

function em_pct(int $part, int $total): string
{
    return $total > 0 ? number_format($part * 100 / $total, 0, ',', '.') . ' %' : '–';
}

function em_days(float $minutes): string
{
    return number_format($minutes / 1440, 1, ',', '.') . ' Tage';
}

function em_num(int|string|null $value): string
{
    return number_format((int)$value, 0, ',', '.');
}

function em_user(int $uid, ?string $name): string
{
    $link = '<a href="idinfo.php?UID=' . $uid . '" target="_blank" rel="noopener">' . $uid . '</a>';
    $name = $name === null ? '<span class="dim">gelöscht</span>' : htmlspecialchars($name);
    return '<td>' . $link . '</td><td>' . $name . '</td>';
}

$res = mysqli_query($GLOBALS['dbi'], "SHOW TABLES LIKE 'de_user_exile_mail'");
$has_log = $res && mysqli_num_rows($res) > 0;

//Konten im Exil
$exile = mysqli_fetch_assoc(mysqli_query($GLOBALS['dbi'], "SELECT SUM(e.returned_at IS NULL) AS im_exil, SUM(e.returned_at IS NOT NULL) AS zurueck,
    SUM(e.returned_at IS NULL AND (l.lageberichte = 0 OR l.reg_mail = '')) AS ohne_berichte
    FROM de_user_exile e LEFT JOIN de_login l ON (l.user_id = e.user_id)"));

if (!$has_log) {
    echo '<div class="flash flash-danger">Die Tabelle <code>de_user_exile_mail</code> fehlt noch. Bitte den Abschnitt dazu aus <code>database/de.sql</code> einspielen, erst dann werden Mails protokolliert.</div>';
} else {
    $tot = mysqli_fetch_assoc(mysqli_query($GLOBALS['dbi'], "SELECT SUM(ok = 1) AS sent, SUM(ok = 0) AS failed,
        COUNT(DISTINCT IF(ok = 1, user_id, NULL)) AS empf,
        COUNT(DISTINCT IF(ok = 1 AND login_at IS NOT NULL, user_id, NULL)) AS empf_login,
        COUNT(DISTINCT IF(ok = 1 AND login_at <= sent_at + INTERVAL 7 DAY, user_id, NULL)) AS empf_login7,
        MIN(sent_at) AS seit
        FROM de_user_exile_mail"));

    echo '<div class="cards">';
    echo '<div class="card"><h2>Verschickt</h2><div class="stat-value">' . em_num($tot['sent']) . '</div>'
        . '<div class="stat-sub">an ' . em_num($tot['empf']) . ' Spieler, ' . em_num($tot['failed']) . ((int)$tot['failed'] === 1 ? ' Fehlversuch' : ' Fehlversuche')
        . ($tot['seit'] ? '<br>seit ' . em_date($tot['seit']) : '') . '</div></div>';
    echo '<div class="card"><h2>Danach eingeloggt</h2><div class="stat-value">' . em_num($tot['empf_login']) . ' <span class="dim">von ' . em_num($tot['empf']) . '</span></div>'
        . '<div class="stat-sub">' . em_pct((int)$tot['empf_login'], (int)$tot['empf']) . ' der Empfänger, '
        . em_num($tot['empf_login7']) . ' davon innerhalb von 7 Tagen</div></div>';
    echo '<div class="card"><h2>Im Exil</h2><div class="stat-value">' . em_num($exile['im_exil']) . '</div>'
        . '<div class="stat-sub">' . em_num($exile['ohne_berichte']) . ' davon ohne Lageberichte (abbestellt oder ohne Mailadresse)</div></div>';
    echo '<div class="card"><h2>Zurückgekehrt</h2><div class="stat-value">' . em_num($exile['zurueck']) . '</div>'
        . '<div class="stat-sub">Konten mit Exil-Akte, die sich wieder eingeloggt haben</div></div>';
    echo '</div>';

    //Auswertung nach Mailart
    $by_type = [];
    $res = mysqli_query($GLOBALS['dbi'], "SELECT type, SUM(ok = 1) AS sent, SUM(ok = 0) AS failed,
        SUM(ok = 1 AND login_at IS NOT NULL) AS login,
        SUM(ok = 1 AND login_at <= sent_at + INTERVAL 7 DAY) AS login7,
        SUM(IF(ok = 1 AND login_at IS NOT NULL, TIMESTAMPDIFF(MINUTE, sent_at, login_at), 0)) AS minuten
        FROM de_user_exile_mail GROUP BY type");
    while ($row = mysqli_fetch_assoc($res)) {
        $by_type[$row['type']] = $row;
    }

    echo '<h2>Nach Mailart</h2>';
    echo '<table>';
    echo '<tr><th>Mailart</th><th>Verschickt</th><th>Fehlversuche</th><th>Login danach</th><th>Quote</th><th>davon in 7 Tagen</th><th>Ø bis Login</th></tr>';
    $sum = ['sent' => 0, 'failed' => 0, 'login' => 0, 'login7' => 0, 'minuten' => 0];
    foreach ($mail_types as $type => $label) {
        $row = $by_type[$type] ?? [];
        foreach ($sum as $key => $value) {
            $sum[$key] += (int)($row[$key] ?? 0);
        }
        $sent = (int)($row['sent'] ?? 0);
        $login = (int)($row['login'] ?? 0);
        echo '<tr>';
        echo '<td><a href="?type=' . $type . '">' . $label . '</a></td>';
        echo '<td class="num">' . em_num($sent) . '</td>';
        echo '<td class="num">' . em_num($row['failed'] ?? 0) . '</td>';
        echo '<td class="num">' . em_num($login) . '</td>';
        echo '<td class="num">' . em_pct($login, $sent) . '</td>';
        echo '<td class="num">' . em_num($row['login7'] ?? 0) . '</td>';
        echo '<td class="num">' . ($login > 0 ? em_days((int)$row['minuten'] / $login) : '–') . '</td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<th>Summe</th>';
    echo '<th class="num">' . em_num($sum['sent']) . '</th>';
    echo '<th class="num">' . em_num($sum['failed']) . '</th>';
    echo '<th class="num">' . em_num($sum['login']) . '</th>';
    echo '<th class="num">' . em_pct($sum['login'], $sum['sent']) . '</th>';
    echo '<th class="num">' . em_num($sum['login7']) . '</th>';
    echo '<th class="num">' . ($sum['login'] > 0 ? em_days($sum['minuten'] / $sum['login']) : '–') . '</th>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="dim">„Login danach“ ist der erste Login nach der jeweiligen Mail. Bei Mails der letzten 7 Tage kann die Quote noch steigen. '
        . 'Ein Login nach der Vorwarnung verhindert die Verlegung nach Sektor 1.</p>';

    //Versandprotokoll
    $where = [];
    $params = [];
    if ($type_filter !== '') {
        $where[] = 'm.type = ?';
        $params[] = $type_filter;
    }
    if ($login_filter === 'ja') {
        $where[] = 'm.login_at IS NOT NULL';
    } elseif ($login_filter === 'nein') {
        $where[] = 'm.login_at IS NULL';
    }
    $sql = "SELECT m.*, d.spielername FROM de_user_exile_mail m LEFT JOIN de_user_data d ON (d.user_id = m.user_id)"
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
        . " ORDER BY m.sent_at DESC, m.id DESC LIMIT 200";
    $res = mysqli_execute_query($GLOBALS['dbi'], $sql, $params);

    echo '<h2>Versandprotokoll</h2>';
    echo '<form method="get" class="inline">';
    echo '<select name="type"><option value="">alle Mailarten</option>';
    foreach ($mail_types as $type => $label) {
        echo '<option value="' . $type . '"' . ($type === $type_filter ? ' selected' : '') . '>' . $label . '</option>';
    }
    echo '</select> ';
    echo '<select name="login">';
    foreach (['' => 'mit und ohne Login', 'ja' => 'mit Login danach', 'nein' => 'ohne Login danach'] as $value => $label) {
        echo '<option value="' . $value . '"' . ($value === $login_filter ? ' selected' : '') . '>' . $label . '</option>';
    }
    echo '</select> ';
    echo '<button type="submit">Anzeigen</button>';
    echo '</form>';
    echo '<p class="dim">Die neuesten 200 Mails.</p>';

    echo '<table>';
    echo '<tr><th>Verschickt</th><th>UserID</th><th>Spieler</th><th>Mailart</th><th>Status</th><th>Login danach</th><th>nach</th></tr>';
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<tr>';
        echo '<td>' . em_date($row['sent_at']) . '</td>';
        echo em_user((int)$row['user_id'], $row['spielername']);
        echo '<td>' . ($mail_types[$row['type']] ?? htmlspecialchars($row['type'])) . '</td>';
        echo '<td>' . ((int)$row['ok'] === 1 ? '<span class="badge badge-ok">verschickt</span>' : '<span class="badge badge-danger">fehlgeschlagen</span>') . '</td>';
        echo '<td>' . em_date($row['login_at']) . '</td>';
        echo '<td class="num">' . ($row['login_at'] ? em_days((strtotime($row['login_at']) - strtotime($row['sent_at'])) / 60) : '') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

//Exil-Akten
$mails_sql = $has_log ? "(SELECT COUNT(*) FROM de_user_exile_mail m WHERE m.user_id = e.user_id AND m.ok = 1 AND m.sent_at >= e.since)" : "NULL";
$res = mysqli_query($GLOBALS['dbi'], "SELECT e.*, d.spielername, d.col, l.lageberichte, l.reg_mail, DATEDIFF(NOW(), e.since) AS tage, $mails_sql AS mails
    FROM de_user_exile e LEFT JOIN de_login l ON (l.user_id = e.user_id) LEFT JOIN de_user_data d ON (d.user_id = e.user_id)
    ORDER BY (e.returned_at IS NULL) DESC, e.since DESC");

echo '<h2>Exil-Akten</h2>';
echo '<table>';
echo '<tr><th>UserID</th><th>Spieler</th><th>im Exil seit</th><th>Tage</th><th>aus Sektor</th><th>Kollektoren</th><th>Mails</th><th>letzte Mail</th><th>Status</th></tr>';
while ($row = mysqli_fetch_assoc($res)) {
    if ($row['returned_at'] !== null) {
        $status = '<span class="badge badge-ok">zurück am ' . date('d.m.Y', strtotime($row['returned_at'])) . '</span>';
    } elseif ((int)$row['lageberichte'] === 0) {
        $status = '<span class="badge badge-warn">abbestellt</span>';
    } elseif ((string)$row['reg_mail'] === '') {
        $status = '<span class="badge badge-warn">keine Mailadresse</span>';
    } else {
        $status = '<span class="badge">im Exil</span>';
    }

    echo '<tr>';
    echo em_user((int)$row['user_id'], $row['spielername']);
    echo '<td>' . em_date($row['since']) . '</td>';
    echo '<td class="num">' . em_num($row['tage']) . '</td>';
    echo '<td class="num">' . ((int)$row['from_sector'] > 0 ? (int)$row['from_sector'] : '–') . '</td>';
    echo '<td class="num">' . em_num($row['col_at_exile']) . ' → ' . em_num($row['col']) . '</td>';
    echo '<td class="num">' . ($row['mails'] === null ? '–' : em_num($row['mails'])) . '</td>';
    echo '<td>' . em_date($row['last_mail_at']) . '</td>';
    echo '<td>' . $status . '</td>';
    echo '</tr>';
}
echo '</table>';

include "inc.layout.bottom.php";
