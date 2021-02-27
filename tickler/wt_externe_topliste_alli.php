<?php
//Alli
$filename = "../ranglisten/alli.txt";
$cachefile = fopen($filename, "w");

$result = mysql_query("SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.system, de_user_data.allytag, de_user_data.status, de_user_data.platz_last_day,de_user_data.platz  FROM de_user_data WHERE sector > 0 ORDER BY score DESC",$db);

$gesamtuser = mysql_num_rows($result);



$db_daten=mysql_query("SELECT allytag, sum(score) as score, sum(col) as col, count(allytag) as am from de_user_data where allytag<>'' AND status=1 group by allytag order by score DESC",$db);
$platz=1;
while($row = mysql_fetch_array($db_daten))
{
  $schnitt=$row["score"]/$row["am"];
  $schnitt=round($schnitt);
  $schnitt2=$row["col"]/$row["am"];
  $schnitt2=round($schnitt2);
  $row["allytag"]=str_replace("|","",$row["allytag"]);

  $query.=$platz.'|'.$row["am"].'|'.$row["score"].'|'.$schnitt.'|'.$row["col"].'|'.$schnitt2.'|'.$row["allytag"];
  $query="$query\r\n";


  $platz++;
}
$platz--;

$query="$platz\r\n$query";

fwrite($cachefile,$query);
fclose($cachefile);

$query="";

gzcompressfile($filename,$level=false);

unlink($filename);

?>





