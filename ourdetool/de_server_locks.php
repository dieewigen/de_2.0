<?php
include "../inc/sv.inc.php";
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

//server sperren
if ($s==1)
{
  $filename = '../.htaccess';
  copy('.htaccess', $filename);
}

//server entsperren
if ($s==2)
{
  $filename = '../.htaccess';
  if (file_exists($filename))unlink($filename);
}

//anmeldung sperren
if ($a==1)
{
  $filename = '../register/.htaccess';
  copy('.htaccess', $filename);
}
//anmeldung entsperren
if ($a==2)
{
  $filename = '../register/.htaccess';
  if (file_exists($filename))unlink($filename);
}

echo '<center><b>Serverlocks</b></center><br><br>';
//anzeige ob der server offen ist
echo '<b>&nbsp;&nbsp;Der Server ist: </b>';
$filename = '../.htaccess';
if (file_exists($filename))
echo 'gesperrt (nur Nutzer des DE-ACP haben Zugriff) - <a href="de_server_locks.php?s=2">entsperren</a>';
else echo 'offen - <a href="de_server_locks.php?s=1">sperren</a>';
echo '<br><br>';

//anzeige ob die anmeldung offen ist
echo '<b>&nbsp;&nbsp;Die Anmeldung ist: </b>';
$filename = '../register/.htaccess';
if (file_exists($filename))
echo 'gesperrt (nur Nutzer des DE-ACP haben Zugriff) - <a href="de_server_locks.php?a=2">entsperren</a>';
else echo 'offen - <a href="de_server_locks.php?a=1">sperren</a>';
echo '<br><br>';

//zuerst schauen ob der user existiert
/*
$filename = 'user/'.$id.'.txt';
if (file_exists($filename))
{
  $filename = 'logs/'.$id.'.txt';
  if (file_exists($filename))
  {
    //logdatei ausgeben
    $filename = str_replace("://","a",$filename);
    $filename = str_replace("php","a",$filename);
    echo '<br><b>Logfile von '.$id.'</b><br><br>';
    $fp = fopen($filename, 'rb');
    while (!feof($fp))
    {
     $buffer = fread($fp, 1024);
     $buffer = str_replace("\n","<br>",$buffer);
     echo $buffer;
    }
    fclose($fp);
  }
  else echo "Zu dem User existiert keine Logdatei.";
}
else echo "Datei nicht gefunden.";*/
?>
</body>
</html>
