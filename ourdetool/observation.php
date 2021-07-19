<?php
include "../inccon.php";
?>
<html>
<head>
<title>Beobachtungsliste</title>
<?php include "cssinclude.php";?>
<style >
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 td.r { color: #ff0000; }
 a { color: #3399ff; text-decoration: underline }
 a:hover { color: #3399ff; text-decoration: none }
</style>

</head>
<body>

<?php
include "det_userdata.inc.php";

function getmicrotime(){
list($usec, $sec) = explode(" ",microtime());
return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

// Oberservation_tag entfernen
if ($_GET[uid]) {
  @mysql_query("UPDATE de_user_info SET observation_stat = 0 WHERE user_id = $_GET[uid]", $db);
}

// table start
echo '
  <form action="observation.php?" method="post">
  <table border="1" cellspacing="1" cellpadding="1">
    <tr>
      <td>Account-ID</td>
      <td>Spielername</td>
      <td>Koordinaten</td>
      <td>Allianz-TAG</td>
      <td>letzte IP</td>
      <td>Status</td>
      <td>Beobachter</td>
    </tr>
';

//abfrage ob es fälle zur beobachtung gibt
$db_daten = mysql_query("
  SELECT A.user_id, B.spielername, B.sector, B.system, B.allytag, A.observation_by, A.observation_stat
  FROM de_user_info AS A
  LEFT JOIN de_user_data AS B ON A.user_id = B.user_id
  WHERE A.observation_stat =1
  ORDER BY A.observation_by, A.user_id", $db);
while ($de_user_data_obs = mysql_fetch_array($db_daten)) {
  if(!$de_user_data_obs[allytag]) { $de_user_data_obs[allytag]="&nbsp;"; }
  $de_login_db = mysql_query("
    SELECT status, last_ip  
    FROM de_login 
    WHERE user_id = '$de_user_data_obs[user_id]'  
  ", $db);
  $data_de_login = mysql_fetch_array($de_login_db);
  switch ($data_de_login[status]) {
    case 0:
      $status = "vor Aktivierung";
    break;
    case 1:
      $status = "Aktiv";
    break;
    case 2:
      $status = "gesperrt";
    break;
    case 3:
      $status = "Urlaub";
    break;
    default:
      $status = "Aktiv";
    break;
  }
  echo '
    <tr>
      <td align="center"><a href="idinfo.php?UID='.$de_user_data_obs[user_id].'" target="_blank">'.$de_user_data_obs[user_id].'</a></td>
      <td align="center"><a href="idinfo.php?UID='.$de_user_data_obs[user_id].'" target="_blank">'.$de_user_data_obs[spielername].'</a></td>
      <td align="center">'.$de_user_data_obs[sector].':'.$de_user_data_obs[system].'</td>
      <td align="center">'.$de_user_data_obs[allytag].'</td>
      <td align="right">'.$data_de_login[last_ip].'</td>
      <td align="center">'.$status.'</td>
      <td align="center">'.$de_user_data_obs[observation_by].'</td>
      <td align="center"><a href="observation.php?uid='.$de_user_data_obs[user_id].'">entfernen</a></td>
    </tr>
  ';
}

// table close
echo '
  </table>
  </form>
';


//$exist_observation = mysql_query("SELECT")

mysql_close($db);

  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");

?>

 <br>
 Seite in <? echo $ltime; ?> Sekunden erstellt.
</body>
</html>
