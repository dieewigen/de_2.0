<?php
include "../inccon.php";

$dir = "../cache/creditlogs/";

// Öffnen eines bekannten Verzeichnisses und danach seinen Inhalt einlesen
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {

            
if($file!='.' AND $file!='..')
{
  $uid=str_replace('.txt', '', $file);
  echo "User-ID: $uid";
  //in der db schauen ob es den user gibt
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);
  if($row["user_id"]>0)
  { 
    echo ' user found';
    $found++;
  }
  else
  {
    echo ' delete';
    $delete++;
    unlink ($dir.$file);
  }
  

  echo '<br>';
}




        }
        closedir($dh);
    }
}
echo '<br>found: '.$found;
echo '<br>delete: '.$delete;
?>