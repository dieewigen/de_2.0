<?php
include "../inccon.php";
?>
<html>
<head>
<title>Lastreg</title>
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
<center>
<?php

include "det_userdata.inc.php";

 function getmicrotime(){
  list($usec, $sec) = explode(" ",microtime());
  return ((float)$usec + (float)$sec);
 }

 $time_start = getmicrotime();

    echo '<table border="0" cellpadding="2" cellspacing="0">';
    echo '<tr>';
    echo '<td width="50">UserID</td>';
    echo '<td width="150">Name</td>';
    echo '<td width="200">E-Mail</td>';
    echo '<td width="150">Passwort</td>';
    echo '<td width="140">Registriert</td>';
    echo '<td width="140">Letzter Login</td>';
    echo '<td width="140">letzte IP</td>';
    echo '<td width="70">Status</td>';
    echo '<td width="40">Logins</td>';
    echo '<td width="40">Sektor</td>';
    echo '<td width="40">Ort</td>';
    echo '</tr>';

$result=mysql_query("select de_login.user_id, de_login.nic, de_login.reg_mail, de_login.pass, de_login.register, de_login.last_login, de_login.logins, de_user_data.sector, de_login.status, de_user_info.ort, de_login.last_ip from de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) left join de_user_info on(de_login.user_id = de_user_info.user_id) ORDER BY `user_id` DESC limit 50",$db);
while($user = mysql_fetch_array($result))
{
  if ($user["status"]==0) $status='Inaktiv';
  if ($user["status"]==1) $status='Aktiv';
  if ($user["status"]==2) $status='Gesperrt';
  if ($user["status"]==3) $status='Urlaub';

  if (isset($stat2))
  {
    if ($user["status"]!=2)
    {
       echo '<tr>';
       echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
       echo '<td>'.$user["nic"].'</td>';
       echo '<td>'.$user["reg_mail"].'</td>';
       echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
       echo '<td>'.$user["register"].'</td>';
       echo '<td>'.$user["last_login"].'</td>';
       echo '<td>'.$user["last_ip"].'</td>';
       $status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
       echo '<td>'.$status.'</td>';
       echo '<td>'.$user["logins"].'</td>';
       echo '<td>'.$user["sector"].'</td>';
       echo '<td>'.$user["ort"].'</td>';
       echo '</tr>';
    }
  }
  else
  {
    echo '<tr>';
    echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
    echo '<td>'.$user["nic"].'</td>';
    echo '<td>'.$user["reg_mail"].'</td>';
    echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
    echo '<td>'.$user["register"].'</td>';
    echo '<td>'.$user["last_login"].'</td>';
    echo '<td>'.$user["last_ip"].'</td>';
    $status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
    echo '<td>'.$status.'</td>';
    echo '<td>'.$user["logins"].'</td>';
    echo '<td>'.$user["sector"].'</td>';
    echo '<td>'.$user["ort"].'</td>';
    echo '</tr>';
  }
}
  echo '</table><br><br>';


mysql_close($db);

  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");

function modpass($pass)
{
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}
?>
</center>
 <br>
 Seite in <? echo $ltime; ?> Sekunden erstellt.
</body>
</html>