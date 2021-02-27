<?
//spieler
$filename = "../ranglisten/spieler.txt";
$cachefile = fopen($filename, "w");

$result = mysql_query("SELECT de_user_data.user_id, de_user_data.spielername, de_user_data.score, de_user_data.col, de_user_data.sector, de_user_data.system, de_user_data.allytag, de_user_data.status, de_user_data.platz_last_day,de_user_data.platz  FROM de_user_data WHERE sector > 0 ORDER BY score DESC",$db);

$gesamtuser = mysql_num_rows($result);

$query="$gesamtuser\r\n";

$rang_schritt = $gesamtuser*0.042;
$rangnamen=array("Der Erhabene", "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");
$platz_i=1;
$time=strftime("%Y%m%d%H%M%S");

while($row = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
{
  $rang_nr=1;
  $rang_zaehler=$rang_schritt;
  while ($platz_i>$rang_zaehler) //rang wird gesucht
  {
    $rang_nr++;
    $rang_zaehler=$rang_zaehler+$rang_schritt;
  }



  if($row["platz_last_day"]==$row["platz"])
       $varalterplatz='#~';
  if($row["platz_last_day"]>$row["platz"])
       $varalterplatz='+~'.($row["platz_last_day"]-$row["platz"]);
  if($row["platz_last_day"]<$row["platz"])
       $varalterplatz='-~'.($row["platz"]-$row["platz_last_day"]);

  $row["spielername"]=str_replace("|","",$row["spielername"]);

  $query = $query.$platz_i.'|'.$rang_nr.'|'.$row["score"].'|'.$varalterplatz.'|'.$row["spielername"];
  $query="$query\r\n";



  $platz_i++;
}
fwrite($cachefile,$query);
fclose($cachefile);

$query="";

gzcompressfile($filename,$level=false);

unlink($filename);

?>






