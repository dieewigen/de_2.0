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

//zuerst schauen ob der user existiert
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
else echo "Datei nicht gefunden.";
?>
</body>
</html>
