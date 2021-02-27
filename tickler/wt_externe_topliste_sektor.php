<?
//Sektor
$filename = "../ranglisten/sektor.txt";
$cachefile = fopen($filename, "w");

$db_daten=mysql_query("SELECT sector, sum(score) as score from de_user_data group by sector order by score DESC",$db);
$platz=1;
while($row = mysql_fetch_array($db_daten))
{
  $sec=$row["sector"];
  //mysql_query("UPDATE de_sector set platz='$platz' where sec_id='$sec'",$db);
  $platz++;
}

$query="$platz\r\n";


$db_daten=mysql_query("SELECT sec_id, name, platz, platz_last_day from de_sector where platz>0 order by platz",$db);
while($row = mysql_fetch_array($db_daten)){
	
	$get_score=mysql_query("SELECT sum(score) as score from de_user_data WHERE sector='$row[sec_id]'");
	$row_score=mysql_fetch_array($get_score);

	if($row["platz_last_day"]==$row["platz"])$varalterplatz='#~';
	if($row["platz_last_day"]>$row["platz"])$varalterplatz='+~'.($row["platz_last_day"]-$row["platz"]);
	if($row["platz_last_day"]<$row["platz"])$varalterplatz='-~'.($row["platz"]-$row["platz_last_day"]);$row[name]=str_replace("|","",$row[name]);

	$query = $query.$row[platz].'|'.$row[sec_id].'|'.$row_score["score"].'|'.$varalterplatz.'|'.$row[name];
	$query="$query\r\n";
}

fwrite($cachefile,$query);
fclose($cachefile);

$query="";

gzcompressfile($filename,$level=false);

unlink($filename);

?>





