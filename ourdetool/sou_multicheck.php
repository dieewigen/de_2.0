<?php
$disablegz=1;
include "../inccon.php";
include "../soudata/lib/sou_dbconnect.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>T</title>
<?php include "cssinclude.php";?>
<style type="text/css">
<!--
.buttons {background-color:#000000;border:1;border-color:#3399FF;border-style:solid;color:#3399FF}
.fett{color: #FFFFFF;font-weight: bold;}
-->
</style>
</head>
<body>
<center>
<br><br>
<?
include "det_userdata.inc.php";

echo '<table width="800px">';
echo '<tr><td>owner_id</td><td>Spielername</td><td>Fraktion</td><td>Zuletzt aktiv</td><td>last_ip</td></tr>';
$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE owner_id>0 AND last_ip<>'' ORDER BY last_ip, owner_id",$soudb);
$sharer=0;
unset($old_last_ip); 	
while($row = mysql_fetch_array($db_daten))
{
  $str1='';$str2='';
  if($row['last_ip']==$old_last_ip) $c++;
  else $c=0; 

  if($c>3)
  {
    $str1='<font color="#FF0000">';
    $str2='</font>';
    $sharer++;
  }

  
  echo '<tr>';
  echo '<td align="right">'.$row['owner_id'].'</td>';
  echo '<td>'.$str1.$row['spielername'].' {'.$row['sn_ext1'].'}'.$str2.'</td>';
  echo '<td align="center">'.$row['fraction'].'</td>';
  echo '<td align="right">'.date("Y-m-d H:i:s",$row['lastclick']).'</td>';
  echo '<td align="right">'.$row['last_ip'].'</td>';
  echo '</tr>';
  $old_last_ip=$row['last_ip'];
}

echo '</table>';

echo 'Sharer: '.$sharer;

?>
</center>
</body>
</html>
