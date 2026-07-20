<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

$lip = req_str('lip');
$mail = req_str('mail');

$page_title = 'Gleiche IP';
include "inc.layout.top.php";

if (isset($_POST['ipstomail'])) {
    csrf_require();

    $gesuser = 0;
    $ips = '<table border="1" cellpadding="0" cellspacing="1" width="200">
    <tr><td align="center">IP: ' . htmlspecialchars($lip) . '</td></tr></table>
    <table border="1" cellpadding="0" cellspacing="1">
    <tr>
    <td width="50">User ID</td>
    <td width="150">Name</td>
    <td width="200">E-Mail</td>
    <td width="140">Registriert</td>
    <td width="140">Letzter Login</td>
    <td width="70">Status</td>
    <td width="40">Logins</td>
    </tr>';

    $result = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT * FROM de_login WHERE last_ip = ? ORDER BY pass",
        [$lip]
    );
    while ($user = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $status = match ((int)$user["status"]) {
            0 => 'Inaktiv',
            1 => 'Aktiv',
            2 => 'Gesperrt',
            3 => 'Urlaub',
            default => 'Status ' . (int)$user["status"],
        };
        $ips = $ips . '<tr>
      <td>' . htmlspecialchars($user["user_id"]) . '</td>
      <td>' . htmlspecialchars($user["nic"]) . '</td>
      <td>' . htmlspecialchars($user["reg_mail"]) . '</td>
      <td>' . htmlspecialchars($user["register"]) . '</td>
      <td>' . htmlspecialchars($user["last_login"]) . '</td>
      <td>' . $status . '</td>
      <td>' . $user["logins"] . '</td>
      </tr>';
        $gesuser++;
    }
    $ips = $ips . '</table><br><br> ' . $gesuser . ' Spieler mit der selben IP gefunden';

    $header = "From:OurDETool <Support@Die-Ewigen.com>\n";
    $header .= "Content-Type: text/html";

    $betreff = "$sv_server_name:  $lip";

    if (@mail($mail, $betreff, $ips, $header)) {
        echo '<div class="flash flash-ok"><b>Infos erfolgreich an ' . htmlspecialchars($mail) . ' &uuml;bermittelt.</b></div>';
    } else {
        echo '<div class="flash flash-danger">Es ist ein Fehler beim Versenden aufgetreten.</div>';
    }
}

if (isset($_POST['suspendall'])) {
    csrf_require();

    $DBData = mysqli_execute_query(
        $GLOBALS['dbi'],
        "SELECT user_id FROM de_login WHERE last_ip = ?",
        [$lip]
    );

    if (!$DBData) {
        die("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));
    }

    while ($IPData = mysqli_fetch_array($DBData, MYSQLI_ASSOC)) {
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_login SET status=2 WHERE user_id = ?",
            [$IPData["user_id"]]
        );

        $time = date("Y-m-d H:i:s");

        $comment = mysqli_execute_query(
            $GLOBALS['dbi'],
            "SELECT kommentar FROM de_user_info WHERE user_id = ?",
            [$IPData["user_id"]]
        );

        if ($comment && mysqli_num_rows($comment) > 0) {
            $row = mysqli_fetch_array($comment, MYSQLI_ASSOC);
            $eintrag = $row['kommentar'] . "\nDirektsperrung von " . $det_username . " über die Multiliste! \n" . $time;

            mysqli_execute_query(
                $GLOBALS['dbi'],
                "UPDATE de_user_info SET kommentar = ? WHERE user_id = ?",
                [$eintrag, $IPData["user_id"]]
            );
        }
    }

    echo '<div class="flash flash-danger">Alle User mit der IP ' . htmlspecialchars($lip) . ' wurden gesperrt!</div>';
}

$gesuser = 0;

//kopf mit ip und anzahl
echo '<h3>IP: ' . htmlspecialchars($lip) . '</h3>';

echo '<table>';
echo '<tr>';
echo '<th>User ID</th>';
echo '<th>Name</th>';
echo '<th>E-Mail</th>';
echo '<th>Registriert</th>';
echo '<th>Letzter Login</th>';
echo '<th>Status</th>';
echo '<th>Logins</th>';
echo '</tr>';

$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_login WHERE last_ip = ? ORDER BY pass",
    [$lip]
);
while ($user = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $status = match ((int)$user["status"]) {
        0 => 'Inaktiv',
        1 => 'Aktiv',
        2 => 'Gesperrt',
        3 => 'Urlaub',
        default => 'Status ' . (int)$user["status"],
    };
    $status .= ' <form class="inline" method="post" action="de_set_user_status.php" data-confirm="User ' . $user["user_id"] . ' wirklich sperren?">'
        . csrf_field()
        . '<input type="hidden" name="uid" value="' . $user["user_id"] . '">'
        . '<input type="hidden" name="status" value="2">'
        . '<input type="hidden" name="ret" value="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">'
        . '<button type="submit" class="btn-danger btn-xs" title="User sperren">[S]</button>'
        . '</form>';

    echo '<tr>';
    echo '<td><a href="idinfo.php?UID=' . htmlspecialchars($user["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($user["user_id"]) . '</a></td>';
    echo '<td>' . htmlspecialchars($user["nic"]) . '</td>';
    echo '<td>' . htmlspecialchars($user["reg_mail"]) . '</td>';
    echo '<td>' . htmlspecialchars($user["register"]) . '</td>';
    echo '<td>' . htmlspecialchars($user["last_login"]) . '</td>';
    echo '<td>' . $status . '</td>';
    echo '<td class="num">' . $user["logins"] . '</td>';
    echo '</tr>';
    $gesuser++;
}
echo '</table>';
echo '<p>' . $gesuser . ' Spieler mit der selben IP gefunden</p>';

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?lip=' . htmlspecialchars(urlencode($lip)) . '" method="post" data-confirm="Wirklich ALLE User mit der IP ' . htmlspecialchars($lip) . ' sperren?">';
echo csrf_field();
echo '<input type="submit" name="suspendall" value="Alle User mit der IP Sperren" class="btn-danger">';
echo '</form>';

echo '<form action="sameip.php" method="post">
  <br><select name="mail" size="1"><option value="Issomad@Die-Ewigen.com">Issomad</option>
  <option value="' . htmlspecialchars($det_email) . '">' . htmlspecialchars($det_username) . '</option>
  <option value="Issomad@Die-Ewigen.com">Issomad</option>
  <option value="downfall@Die-Ewigen.com">Downfall</option>
  </select>
  ' . csrf_field() . '
  <input type="hidden" name="lip" value="' . htmlspecialchars($lip) . '">
  <input type="Submit" name="ipstomail" value="IP\'s anfordern"></form>';

include "inc.layout.bottom.php";
