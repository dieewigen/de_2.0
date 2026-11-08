<?php
include "../inccon.php";
include "det_userdata.inc.php";

$uid = req_int('uid');

$page_title = 'Beobachtungsliste';
include "inc.layout.top.php";

// Beobachtungs-Markierung entfernen
if ($uid > 0) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_info SET observation_stat = 0 WHERE user_id = ?", [$uid]);
}

// table start
echo '
  <table>
    <tr>
      <th>Account-ID</th>
      <th>User-ID</th>
      <th>Spielername</th>
      <th>Koordinaten</th>
      <th>Allianz-TAG</th>
      <th>letzte IP</th>
      <th>E-Mail</th>
      <th>letzte Aktivit&auml;t</th>
      <th>Status</th>
      <th>Beobachter</th>
      <th></th>
    </tr>
';

//abfrage ob es fälle zur beobachtung gibt
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "
  SELECT A.user_id, B.spielername, B.sector, B.system, B.allytag, A.observation_by, A.observation_stat
  FROM de_user_info AS A
  LEFT JOIN de_user_data AS B ON A.user_id = B.user_id
  WHERE A.observation_stat = 1
  ORDER BY A.observation_by, A.user_id");
while ($de_user_data_obs = mysqli_fetch_assoc($db_daten)) {
    if (!$de_user_data_obs['allytag']) {
        $allytag = "&nbsp;";
    } else {
        $allytag = htmlspecialchars($de_user_data_obs['allytag']);
    }
    $de_login_db = mysqli_execute_query($GLOBALS['dbi'], "
    SELECT status, last_ip, reg_mail, last_click, owner_id
    FROM de_login
    WHERE user_id = ?
  ", [$de_user_data_obs['user_id']]);
    $data_de_login = mysqli_fetch_assoc($de_login_db);

    $reg_mail = trim((string)$data_de_login['reg_mail']);
    $mail_anzeige = $reg_mail !== ''
        ? '<a href="mailto:' . htmlspecialchars($reg_mail) . '">' . htmlspecialchars($reg_mail) . '</a>'
        : '&nbsp;';

    // last_click wird vom Spiel bei Aktivität aktualisiert (5-Minuten-Raster)
    $last_click = (string)$data_de_login['last_click'];
    $aktivitaet_anzeige = ($last_click !== '' && !str_starts_with($last_click, '0000'))
        ? htmlspecialchars($last_click)
        : '&nbsp;';

    //unmaskierte IPs (gültige IP-Adresse, maskierte enthalten ".x.") zum Whois-Service verlinken
    $last_ip = (string)$data_de_login['last_ip'];
    if (filter_var($last_ip, FILTER_VALIDATE_IP)) {
        $ip_anzeige = '<a href="https://www.whois.com/whois/' . htmlspecialchars($last_ip) . '" target="_blank" rel="noopener">' . htmlspecialchars($last_ip) . '</a>';
    } else {
        $ip_anzeige = htmlspecialchars($last_ip);
    }

    switch ($data_de_login['status']) {
        case 0:
            $status = "vor Aktivierung";
            break;
        case 1:
            $status = "Aktiv";
            break;
        case 2:
            $status = "gesperrt";
            break;
        case 3:
            $status = "Urlaub";
            break;
        default:
            $status = "Aktiv";
            break;
    }
    echo '
    <tr>
      <td align="center">' . ((int)$data_de_login['owner_id'] > 0 ? (int)$data_de_login['owner_id'] : '&nbsp;') . '</td>
      <td align="center"><a href="idinfo.php?UID=' . $de_user_data_obs['user_id'] . '" target="_blank" rel="noopener">' . $de_user_data_obs['user_id'] . '</a></td>
      <td align="center"><a href="idinfo.php?UID=' . $de_user_data_obs['user_id'] . '" target="_blank" rel="noopener">' . htmlspecialchars((string)$de_user_data_obs['spielername']) . '</a></td>
      <td align="center">' . $de_user_data_obs['sector'] . ':' . $de_user_data_obs['system'] . '</td>
      <td align="center">' . $allytag . '</td>
      <td class="num">' . $ip_anzeige . '</td>
      <td>' . $mail_anzeige . '</td>
      <td align="center">' . $aktivitaet_anzeige . '</td>
      <td align="center">' . $status . '</td>
      <td align="center">' . htmlspecialchars((string)$de_user_data_obs['observation_by']) . '</td>
      <td align="center"><a href="' . csrf_url('observation.php?uid=' . $de_user_data_obs['user_id']) . '" data-confirm="User ' . $de_user_data_obs['user_id'] . ' von der Beobachtungsliste entfernen?">entfernen</a></td>
    </tr>
  ';
}

// table close
echo '
  </table>
';

include "inc.layout.bottom.php";
