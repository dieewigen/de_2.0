<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Kollektoren</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
$tage=7;
$plaetze=10;
$time=time()-3600*24*$tage;
$p=1;
//userauslesen
$db_datenx=mysql_query("SELECT * FROM de_user_data ORDER BY col DESC LIMIT $plaetze",$db);
while($rowx = mysql_fetch_array($db_datenx))
{
  $uid=$rowx[user_id];
  
  echo '<br>'.$p.'. '.$rowx[spielername].': ';
  
  //echo '<br><br><table width=600>';
  //echo '<tr align="center"><td>Zeitpunkt</td><td>Kollektoren</td><td>Bestohlener-User-ID</td><td>Spielername</td></tr>';
  $cols=0;
  $db_daten=mysql_query("SELECT * FROM de_user_getcol WHERE user_id='$uid' AND time>'$time' ORDER BY time DESC",$db);
  while($row = mysql_fetch_array($db_daten))
  {
    $cols+=$row[colanz];
  
/*  
    $time=strftime("%Y-%m-%d %H:%M:%S", $row["time"]);
  	//spielername des diebes
  	$duid=$row["zuser_id"];
  	$result=mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$duid'",$db);
  	$num = mysql_num_rows($result);
  	if($num==1)
  	{
  	  $rowx = mysql_fetch_array($result);
  	  $spielername=$rowx["spielername"];
  	}
  	else $spielername='gelöscht';
    echo '<tr align="center"><td>'.$time.'</td><td>'.$row["colanz"].'</td><td><a href="idinfo.php?UID='.$duid.'" target="_blank">'.$duid.'</a></td><td>'.$spielername.'</td></tr>';
*/    
  }
  $p++;
  echo $cols; 
  //echo '</table>';

}


?>
</body>
</html>
