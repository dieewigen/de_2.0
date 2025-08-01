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
<title>Suche</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
// Prüfe, ob die Variable existiert und gesetzt ist
if(isset($_GET['delete']))
{
  $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
  
  if($uid > 0) {
    // Umwandlung der MySQL-Abfragen zu MySQLi mit prepared statements
    mysqli_execute_query($GLOBALS['dbi'], 
      "UPDATE de_login SET status=2, last_login='0000-00-00 00:00:00' WHERE user_id=?", 
      [$uid]
    );
    
    mysqli_execute_query($GLOBALS['dbi'], 
      "UPDATE de_user_data SET premium=0 WHERE user_id=?", 
      [$uid]
    );
    
    die('<center><br>Der Spieler wurde dem Inaktivenscript zur Löschung übergeben.</body></html>');
  } else {
    echo '<center><br>Fehler: Keine gültige Benutzer-ID angegeben.</center>';
  }
}
?>
<form action="de_user_delete.php" method="get">
<center>Durch das Bestätigen des Buttons wird der Spieler gelöscht. Voraussetzung für die Löschung ist ein aktives Inaktivenscript.<br><br>
<input type="Submit" name="delete" value="Spieler löschen">
<input type="hidden" name="uid" value="<?php echo isset($_GET['uid']) ? htmlspecialchars($_GET['uid']) : ''; ?>">
</form>
</body>
</html>
