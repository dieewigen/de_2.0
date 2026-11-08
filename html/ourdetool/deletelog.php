<?php
include "det_userdata.inc.php";

$id = req_str('id');
if (!preg_match('/^[A-Za-z0-9_.-]+$/', $id)) {
    die('Ungültige ID.');
}

$page_title = 'Logfile löschen';
include "inc.layout.top.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lösch-Zweig: nur mit gültigem CSRF-Token
    csrf_require();

    //zuerst schauen ob der user existiert
    $filename = 'user/' . $id . '.txt';
    if (file_exists($filename))
    {
      $filename = 'logs/' . $id . '.txt';
      if (file_exists($filename))
      {
        //logdatei löschen
        unlink($filename);
        echo '<div class="flash flash-ok">Logfile von ' . htmlspecialchars($id) . ' gel&ouml;scht.</div>';
      }
      else echo "Zu dem User existiert keine Logdatei.";
    }
    else echo "Datei nicht gefunden.";
} else {
    // GET: Bestätigungsseite anzeigen
    echo '<form action="deletelog.php" method="post" data-confirm="Logfile von ' . htmlspecialchars($id) . ' wirklich l&ouml;schen?">';
    echo csrf_field();
    echo '<input type="hidden" name="id" value="' . htmlspecialchars($id) . '">';
    echo '<p>Soll das Logfile von <b>' . htmlspecialchars($id) . '</b> wirklich gel&ouml;scht werden?</p>';
    echo '<button type="submit" class="btn-danger">Logfile l&ouml;schen</button>';
    echo '</form>';
}

include "inc.layout.bottom.php";
