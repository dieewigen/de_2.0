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
<title>User sperren</title>
<?php include "cssinclude.php";?>
</head>
<body bgcolor="#FFFFFF" text="#000000">
<div align="center">
<?php

include "det_userdata.inc.php";

if (isset($_GET['uid']))
{
  $uid = (int)$_GET['uid'];
  
  // Benutzer sperren
  mysqli_execute_query($GLOBALS['dbi'],
    "UPDATE de_login SET status=2, supporter=? WHERE user_id=?",
    [$det_email, $uid]
  );

  // Aktuelles Datum/Zeit mit DateTime-Objekt
  $dateTime = new DateTime();
  $time = $dateTime->format("Y-m-d H:i:s");

  // Kommentar auslesen
  $result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT kommentar FROM de_user_info WHERE user_id=?",
    [$uid]
  );
  
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    // Neuen Eintrag erstellen
    $eintrag = $row['kommentar'] . "\nDirektsperrung von " . $det_username . " über die Multiliste! \n" . $time;
    
    // Kommentar aktualisieren
    mysqli_execute_query($GLOBALS['dbi'],
      "UPDATE de_user_info SET kommentar=? WHERE user_id=?",
      [$eintrag, $uid]
    );
  }

  echo 'User gesperrt.';
}
else die ('Fehler beim Scriptaufruf.');
?>
</body>
</html>
