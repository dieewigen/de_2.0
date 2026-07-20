<?php
include "det_userdata.inc.php";

$id = req_str('id');
if (!preg_match('/^[A-Za-z0-9_.-]+$/', $id)) {
    die('Ungültige ID.');
}

$page_title = 'Adminlog';
include "inc.layout.top.php";

//zuerst schauen ob der user existiert
$filename = 'user/' . $id . '.txt';
if (file_exists($filename))
{
  $filename = 'logs/' . $id . '.txt';
  if (file_exists($filename))
  {
    //logdatei ausgeben
    echo '<br><b>Logfile von ' . htmlspecialchars($id) . '</b><br><br>';
    $buffer = (string)file_get_contents($filename);
    echo str_replace("\n", "<br>", htmlspecialchars($buffer, ENT_QUOTES | ENT_SUBSTITUTE));
  }
  else echo "Zu dem User existiert keine Logdatei.";
}
else echo "Datei nicht gefunden.";

include "inc.layout.bottom.php";
