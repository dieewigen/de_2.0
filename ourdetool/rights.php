<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="rights.php" method="post">
<div align="center">
<?php
include "det_userdata.inc.php";

if ($savedata)
{
  foreach($HTTP_POST_VARS as $name=> $value)
  {
    //echo "$name:$value\n";
    $name = str_replace("_lvl",".lvl",$name);
    //ist es auch wirklich eine .lvl-datei?
    $pos=0;
    $pos = strpos($name, ".lvl");
    
    //echo $name.'<br>';
    if($pos > 0 AND $name!='savedata' AND $name!='rights.lvl' AND $name!='browse.lvl' AND $name!='edit.lvl' AND $name!='add.lvl' AND $name!='deletelog.lvl' AND $name!='showlog.lvl')
    {
      $value=(int)$value;
      if ($value<1 OR $value>99)$value=99;
      $filename=$name;
      $cachefile = fopen ($filename, 'w');
      //echo $filename;
      fputs($cachefile, $value."\n");
      fclose($cachefile);
    }
  }
}
echo '<br><b>Einrichtung der Scriptzugriffsrechte</b><br>
Die Zahl gibt an welchen Userlevel man mindestens benötigt, je kleiner die Zahl desto höher der Level. 1= höchster Level, 99= kleiner Level
<br>';
echo '<br><table cellpadding="3" cellspacing="4">';
echo '<tr><td><b>Bereich</b></td><td><b>Level</b></td></tr>';
//alle angelegten user auslesen
if ($handle = opendir('.'))
{
  /* This is the correct way to loop over the directory. */
  while (false !== ($file = readdir($handle)))
  {
    if($file!='.' AND $file!='..')
    {

      //schauen ob es eine userlevel-datei ist
      $pos=0;
      $pos = strpos($file, ".lvl");
      if($pos>0 AND $file!='savedata' AND $file!='rights.lvl' AND $file!='browse.lvl' AND $file!='edit.lvl' AND $file!='add.lvl'
       AND $file!='deletelog.lvl' AND $file!='showlog.lvl')
      {
        $fp = fopen ($file, 'r');
        $filelevel=fgets($fp, 1024);
        fclose($fp);
        echo '<tr><td>'.$file.'</td><td><input type="text" name="'.$file.'" value="'.$filelevel.'"></td></tr>';

      }
    }
  }
  closedir($handle);
}
echo '</table>';
echo '<br><br><input type="Submit" name="savedata" value="Einstellungen speichern"><br><br><br>';
?>

</form>
</body>
</html>
