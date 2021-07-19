<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

// This code demonstrates how to lookup the country by IP Address

include("geoip.inc");

// Uncomment if querying against GeoIP/Lite City.
include("geoipcity.inc");

//$gi = geoip_open("../../div_server_data/geoip/GeoIP.dat",GEOIP_STANDARD);
$gi = geoip_open("../../div_server_data/geoip/GeoLiteCity.dat",GEOIP_STANDARD);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>IP</title>
<?php include "cssinclude.php";?>
<?php 

echo 'Zuweisung der IP-Adressen zu Ländern/Orten anhand der Daten von maxmind.com. Es werden alle User angezeigt, deren IP-Adresse nicht aus D/A/CH kommen.<br><br>';

   echo '<table border="0" cellpadding="2" cellspacing="0">';
   echo '<tr>';
   echo '<td>UserID</td>';
   echo '<td>Loginname</td>';
   echo '<td>E-Mail</td>';
   //echo '<td>Passwort</td>';
   echo '<td>IP</td>';   
   echo '<td>Registriert</td>';
   echo '<td>Letzter Login</td>';
   echo '<td>Status</td>';
   echo '<td>Logins</td>';
   echo '<td>Geodaten</td>';
   echo '</tr>';


$db_daten=mysql_query("SELECT * FROM de_login WHERE last_ip<>'127.0.0.1' AND last_ip<>'' ORDER BY last_ip ASC",$db);

$gesuser=0;

while($user = mysql_fetch_array($db_daten))
{
  if ($user["status"]==0) $status='Inaktiv';
  if ($user["status"]==1) $status='Aktiv';
  if ($user["status"]==2) $status='Gesperrt';
  if ($user["status"]==3) $status='Urlaub';

  $record = geoip_record_by_addr($gi,$user["last_ip"]);
$ipinfo=''.
$record->country_code . " " . $record->country_code3 . " " . $record->country_name . "\n".
$record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n".
$record->city . "\n".
$record->postal_code . "\n".
$record->latitude . "\n".
$record->longitude . "\n".
$record->metro_code . "\n".
$record->area_code . "\n";
  
  if($record->country_code!='DE' AND $record->country_code!='AT' AND $record->country_code!='CH')
  {
    echo '<tr>';
    echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
    echo '<td>'.$user["nic"].'</td>';
    echo '<td>'.$user["reg_mail"].'</td>';
    //echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
    echo '<td>'.$user["last_ip"].'</td>';
    echo '<td>'.$user["register"].'</td>';
    echo '<td>'.$user["last_login"].'</td>';
    $status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
    echo '<td>'.$status.'</td>';
    echo '<td>'.$user["logins"].'</td>';
    echo '<td>'.$ipinfo.'</td>';
    echo '</tr>';
    $gesuser++;
  }
}

echo '</table>';

echo '<br>Gesamtzahl: '.$gesuser;

function modpass($pass)
{
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}

?>