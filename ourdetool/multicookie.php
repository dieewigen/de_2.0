<?php
include "../inccon.php";
?>
<html>
<head>
<title>Multiliste</title>
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

$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.*, de_user_ip.*, de_user_data.*, de_login.status AS loginstatus FROM de_login LEFT JOIN de_user_ip ON(de_login.user_id = de_user_ip.user_id) 
	LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id)
 WHERE de_user_ip.loginhelp<>'' ORDER BY de_user_ip.loginhelp, de_user_ip.user_id");

$gesuser=0;

  echo '<table border="0" cellpadding="2" cellspacing="0">';
  echo '<tr>';
  echo '<td width="50">UserID</td>';
  echo '<td width="150">IP</td>';
  echo '<td width="150">Cookie-Zeit</td>';
  echo '<td width="150">Registriert</td>';
  echo '<td width="150">Letzte Aktivit&auml;t</td>';
  echo '<td width="150">Status</td>';
  echo '<td>Sektor</td>';
  echo '<td>Allianz</td>';
  echo '<td width="150">Cookie</td>';
  echo '<td width="200">Browser</td>';
  echo '</tr>';


unset($olduserid);
unset($oldloginhelp);
  
while($row = mysqli_fetch_assoc($db_daten)){
	if(!isset($olduserid))$olduserid=$row["user_id"];
	if(!isset($oldloginhelp))$oldloginhelp=$row["loginhelp"];

	if($olduserid!=$row['user_id'] AND $oldloginhelp==$row['loginhelp']){
		echo '<tr>';
		echo '<td><a href="idinfo.php?UID='.$olduserid.'" target="_blank">'.$olduserid.'</a></td>';
		echo '<td>'.$oldip.'</td>';
		echo '<td>'.$oldtime.'</td>';
		echo '<td>'.$registertime.'</td>';
		echo '<td>'.$lastactivetime.'</td>';
		echo '<td>'.$oldstatus.'</td>';
		echo '<td>'.$oldsector.'</td>';
		echo '<td>'.$oldallytag.'</td>';
		echo '<td>'.$oldloginhelp.'</td>';
		echo '<td>'.$oldbrowser.'</td>';
		echo '</tr>';  	
		echo '<tr>';
		echo '<td><a href="idinfo.php?UID='.$row["user_id"].'" target="_blank">'.$row["user_id"].'</a></td>';
		echo '<td>'.$row['ip'].'</td>';
		echo '<td>'.$row['time'].'</td>';
		echo '<td>'.$row['register'].'</td>';
		echo '<td>'.$row['last_click'].'</td>';
		echo '<td>'.$row['loginstatus'].'</td>';
		echo '<td>'.$row['sector'].'</td>';
		echo '<td>'.$row['allytag'].'</td>';
		echo '<td>'.$row['loginhelp'].'</td>';
		echo '<td>'.$row['browser'].'</td>';
		echo '</tr>';
		$gesuser++;
	}
	$olduserid=$row["user_id"];
	$oldloginhelp=$row["loginhelp"];
	$oldip=$row["ip"];
	$oldtime=$row['time'];
	$oldbrowser=$row["browser"];
	$oldstatus=$row["loginstatus"];
	$oldsector=$row["sector"];
	$oldallytag=$row["allytag"];
	$registertime=$row['register'];
	$lastactivetime=$row['last_click'];
		
}
  echo '</table><br><br>';
echo 'Verdächtige: '.$gesuser;

// Keine mysqli_close hier notwendig, da die Verbindung global verwaltet wird

  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");

function modpass($pass){
  $pass[0]="*";
  $pass[1]="*";
  $pass[2]="*";
  $pass[3]="*";
  return($pass);
}
?>

 <br>
 Seite in <?php echo $ltime; ?> Sekunden erstellt.
</body>
</html>