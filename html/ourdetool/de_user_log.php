<?php
include "../inccon.php";
include "det_userdata.inc.php";

$uid = req_int('uid');

$page_title = 'Logdatei';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

//schauen ob es die datei gibt
$filename = '../cache/logs/getpost_' . $uid . '.txt';
if (file_exists($filename) == 1)
{
  echo 'Erstelle Logdatei...';
  //datei holen und ins temp-verzeichnis kopieren, dabei Passwörter entfernen
  $fp = fopen($filename, 'rb');
  $fp_out = fopen('temp/' . $uid . '.txt', 'w');
  while (!feof($fp))
  {
    $buffer = fgets($fp, 2048);
    if (strstr($buffer, 'pass =>') OR strstr($buffer, 'newpass =>') OR strstr($buffer, 'oldpass =>') OR
    strstr($buffer, 'pass1 =>') OR strstr($buffer, 'pass2 =>')) $buffer = "(Passwort entfernt)\n";
    $buffer = trim($buffer) . "\n";
    fputs($fp_out, $buffer);
  }
  fclose($fp);
  fclose($fp_out);

  //datei packen
  gzcompressfile($level = false);
  echo 'fertig<br><br><a href="temp/' . $uid . '.zip">Dowload</a>';
}
else echo 'Keine Logdatei vorhanden.';


function gzcompressfile($level = false)
{
  global $uid;

  $dest = 'temp/' . $uid . '.zip';
  $source = 'temp/' . $uid . '.txt';
  $mode = 'w' . $level;
  $error = false;
  if ($fp_out = gzopen($dest, $mode))
  {
    if ($fp_in = fopen($source, 'rb'))
    {
      while (!feof($fp_in))
        gzputs($fp_out, fread($fp_in, 1024 * 512));
      fclose($fp_in);
    }
    else
      $error = true;
    gzclose($fp_out);
  }
  else
    $error = true;
  if ($error)
    return false;
  else
    return $dest;
}

include "inc.layout.bottom.php";
