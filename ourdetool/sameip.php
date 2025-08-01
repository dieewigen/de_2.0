<?php
include "../inc/sv.inc.php";
include "../functions.php";
include "../inc/env.inc.php";

// Stelle sicher, dass eine Datenbankverbindung vorhanden ist
if (!isset($GLOBALS['dbi'])) {
    $GLOBALS['dbi'] = mysqli_connect(
        $GLOBALS['env_db_dieewigen_host'],
        $GLOBALS['env_db_dieewigen_user'],
        $GLOBALS['env_db_dieewigen_password'],
        $GLOBALS['env_db_dieewigen_database']
    );
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Gleiche IP</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
include "det_userdata.inc.php";

// Prüfen der Parameter
$lip = isset($_GET['lip']) ? $_GET['lip'] : (isset($_POST['lip']) ? $_POST['lip'] : '');
$ipstomail = isset($_POST['ipstomail']);

if ($ipstomail) {
    $ips = $ips . '<table border="1" cellpadding="0" cellspacing="1" width="200">
    <tr><td align="center">IP: '.htmlspecialchars($lip).'</td></tr></table>
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
        $ips = $ips . '<tr>
      <td>'.htmlspecialchars($user["user_id"]).'</td>
      <td>'.htmlspecialchars($user["nic"]).'</td>
      <td>'.htmlspecialchars($user["reg_mail"]).'</td>
      <td>'.htmlspecialchars($user["register"]).'</td>
      <td>'.htmlspecialchars($user["last_login"]).'</td>';
        if ($user["status"] == 0) {
            $status = 'Inaktiv';
        }
        if ($user["status"] == 1) {
            $status = 'Aktiv';
        }
        if ($user["status"] == 2) {
            $status = 'Gesperrt';
        }
        if ($user["status"] == 3) {
            $status = 'Urlaub';
        }
        $ips = $ips . '<td>'.$status.'</td>
      <td>'.$user["logins"].'</td>
      </tr>';
        $gesuser++;
    }
    $ips = $ips . '</table><br><br> ' . $gesuser .' Spieler mit der selben IP gefunden';


    $header = "From:OurDETool <Support@Die-Ewigen.com>\n";
    $header .= "Content-Type: text/html";

    $betreff = "$sv_server_name:  $lip";

    if (@mail($mail, $betreff, $ips, $header)) {
        echo "<center><font color=#FFFFFF><b>Infos erfolgreich an " . $mail ." &uuml;bermittelt.</b></font></center>";

    } else {
        echo "<h1>Es ist ein Fehler beim Versenden aufgetreten.</h1>";
    }



}

if (isset($_POST['suspendall'])) {
    $lip = isset($_GET['lip']) ? $_GET['lip'] : '';

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

        // Datum mit DateTime statt strftime
        $dateTime = new DateTime();
        $time = $dateTime->format("Y-m-d H:i:s");

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

    echo '<font color="#FF0000">Alle User mit der IP '.$lip.' wurden gesperrt!</font><br><br>';
}

// Stelle sicher, dass lip definiert ist
$lip = isset($_GET['lip']) ? $_GET['lip'] : '';
$gesuser = 0; // Initialisieren des Zählers

//kopf mit ip und anzahl
echo '<table border="1" cellpadding="0" cellspacing="1" width="200">';
echo '<tr>';
echo '<td align="center">IP: '.htmlspecialchars($lip).'</td>';
echo '</tr>';
echo '</table>';

echo '<table border="1" cellpadding="0" cellspacing="1">';
echo '<tr>';
echo '<td width="50">User ID</td>';
echo '<td width="150">Name</td>';
echo '<td width="200">E-Mail</td>';
echo '<td width="140">Registriert</td>';
echo '<td width="140">Letzter Login</td>';
echo '<td width="70">Status</td>';
echo '<td width="40">Logins</td>';
echo '</tr>';


$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_login WHERE last_ip = ? ORDER BY pass",
    [$lip]
);
while ($user = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo '<tr>';
    echo '<td><a href="idinfo.php?UID='.htmlspecialchars($user["user_id"]).'" target="_blank">'.htmlspecialchars($user["user_id"]).'</a></td>';
    echo '<td>'.htmlspecialchars($user["nic"]).'</td>';
    echo '<td>'.htmlspecialchars($user["reg_mail"]).'</td>';
    echo '<td>'.htmlspecialchars($user["register"]).'</td>';
    echo '<td>'.htmlspecialchars($user["last_login"]).'</td>';
    if ($user["status"] == 0) {
        $status = 'Inaktiv';
    }
    if ($user["status"] == 1) {
        $status = 'Aktiv';
    }
    if ($user["status"] == 2) {
        $status = 'Gesperrt';
    }
    if ($user["status"] == 3) {
        $status = 'Urlaub';
    }
    $status .= ' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
    echo '<td>'.$status.'</td>';
    echo '<td>'.$user["logins"].'</td>';
    echo '</tr>';
    $gesuser++;
}
echo '</table><br><br> ' . $gesuser .' Spieler mit der selben IP gefunden';

echo '<br><form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'?lip='.htmlspecialchars($lip).'" method="post">';
echo '<input type="submit" name="suspendall" value="Alle User mit der IP Sperren">';
echo '</form>';

echo '<form action="sameip.php" method="post">
  <br><center><select name="mail" size="1"><option value="Issomad@Die-Ewigen.com">Issomad</option>
  <option value="'.htmlspecialchars($det_email).'">'.htmlspecialchars($det_username).'</option>
  <option value="Issomad@Die-Ewigen.com">Issomad</option>
  <option value="downfall@Die-Ewigen.com">Downfall</option>
  </select>
  <input type="hidden" name="lip" value="'. htmlspecialchars($lip). '">
  <input type="Submit" name="ipstomail" value="IP\'s anfordern"></center></form>';

function modpass($pass)
{
    $pass[0] = "*";
    $pass[1] = "*";
    $pass[2] = "*";
    $pass[3] = "*";
    return($pass);
}
?>


</body>
</html>
