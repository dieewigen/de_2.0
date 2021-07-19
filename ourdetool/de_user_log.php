<?php
include "../inccon.php";
include "../inc/sv.inc.php";
?>

<html>
<head>
<title>Logfile</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>Logdatei</h1>
<?
include "det_userdata.inc.php";

$uid=(int)$uid;

//schauen ob es die datei gibt
$filename='../cache/logs/getpost_'.$uid.'.txt';
if (file_exists($filename)==1)
{
  echo 'Erstelle Logdatei...';
  //datei holen und ins temp-verzeichnis kopieren, dabei passwörter entfernen
  $fp = fopen($filename, 'rb');
  $fp_out = fopen('temp/'.$uid.'.txt', 'w');
  while (!feof($fp))
  {
    $buffer = fgets($fp, 2048);
    if (strstr($buffer, 'pass =>') OR strstr($buffer, 'newpass =>') OR strstr($buffer, 'oldpass =>') OR
    strstr($buffer, 'pass1 =>') OR strstr($buffer, 'pass2 =>')) $buffer="(Passwort entfernt)\n";
    $buffer=trim($buffer)."\n";
    fputs($fp_out, $buffer);
  }
  fclose($fp);
  fclose($fp_out);
  //if($key=='pass' OR $key=='newpass' OR $key=='oldpass' OR $key=='pass1' OR $key=='pass2')$value='(passwort entfernt)'

  //datei packen
  gzcompressfile($level=false);
  echo 'fertig<br><br><a href="temp/'.$uid.'.zip">Dowload</a>';
}
else echo 'Keine Logdatei vorhanden.';


function gzcompressfile($level=false)
{
  global $uid;

  $dest='temp/'.$uid.'.zip';
  $source='temp/'.$uid.'.txt';
  $mode='w'.$level;
  $error=false;
  if($fp_out=gzopen($dest,$mode))
  {
    if($fp_in=fopen($source,'rb'))
    {
      while(!feof($fp_in))
        gzputs($fp_out,fread($fp_in,1024*512));
      fclose($fp_in);
    }
    else
      $error=true;
    gzclose($fp_out);
  }
  else
    $error=true;
  if($error)
    return false;
  else
    return $dest;
}

?>
</div>
</body>
</html>

