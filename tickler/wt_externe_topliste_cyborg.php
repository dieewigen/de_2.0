<?
//Cyborg
$filename = "../ranglisten/cyborg.txt";
$cachefile = fopen($filename, "w");

$platz_i=0;

$result = mysql_query("SELECT de_user_data.spielername, de_cyborg_data.level, de_cyborg_data.exp, de_cyborg_data.questpoints FROM de_user_data left join de_cyborg_data on(de_user_data.user_id = de_cyborg_data.user_id) ORDER BY exp DESC",$db);
while($row = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $platz_i++;

  if ($row["level"]=='')
  $row["level"]='0';
  if ($row["questpoints"]=='')
  $row["questpoints"]='0';
  if ($row["exp"]=='')
  $row["exp"]='0';
  $row["spielername"]=str_replace("|","",$row["spielername"]);

  $query = $query.$platz_i.'|'.$row["level"].'|'.$row["questpoints"].'|'.$row["exp"].'|'.$row["spielername"];
  $query="$query\r\n";


}

$query="$platz_i\r\n$query";


fwrite($cachefile,$query);
fclose($cachefile);

$query="";

gzcompressfile($filename,$level=false);

unlink($filename);

?>





