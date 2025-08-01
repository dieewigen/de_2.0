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

<html>
<head>
<title>IPs</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>IPs</h1>
<?php
include "det_userdata.inc.php";

// Sicherstellen, dass Variablen definiert und typisiert sind
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
$cg = isset($_GET['cg']) ? (int)$_GET['cg'] : 0;

//alle ips laden und ausgeben
echo '
<table border="0">
<colgroup>
<col width="150">
<col width="150">
</colgroup>
<tr>
<td class="cell1" align="center"><b>IP-Adresse</b></td>
<td class="cell1" align="center"><b>Loginzeit</b></td>
<td class="cell1" align="center"><b>Cookie</b></td>
<td class="cell1" align="center"><b>Browser</b></td>
</tr>';

// Verwendung von prepared statement mit mysqli_execute_query
$result = mysqli_execute_query($GLOBALS['dbi'], 
  "SELECT ip, time, browser, loginhelp FROM de_user_ip WHERE user_id = ? ORDER BY time DESC",
  [$uid]
);

// Überprüfen des Ergebnisses
if ($result) {
  while($row_user_ip = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo '<tr align="center">
      <td>'.htmlspecialchars($row_user_ip['ip']).'</td>
      <td>'.htmlspecialchars($row_user_ip['time']).'</td>
      <td>'.htmlspecialchars($row_user_ip['loginhelp']).'</td>
      <td>'.htmlspecialchars($row_user_ip['browser']).'</td>
    </tr>';
  }
} else {
  echo '<tr><td colspan="4" align="center">Keine IP-Adressen gefunden oder Fehler bei der Abfrage.</td></tr>';
}

echo '</table>';
?>
</body>
</html>

