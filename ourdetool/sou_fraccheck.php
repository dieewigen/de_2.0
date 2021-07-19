<?php
$disablegz=1;
include "../inccon.php";
include "../soudata/lib/sou_dbconnect.php";
include "../soudata/lib/sou_functions.inc.php";
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

if($_REQUEST['freset'])
{
  $uid=intval($_REQUEST['uid']);
  mysql_query("UPDATE sou_user_data SET fraction=0 WHERE user_id='$uid'",$soudb);
}

echo '<table width="800px">';
echo '<tr><td>owner_id</td><td>Spielername</td><td>Fraktion</td><td>Zuletzt aktiv</td><td>canmine</td><td>donate</td><td>Aktion</td></tr>';
$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE owner_id>0 ORDER BY owner_id",$soudb);
$sharer=0;
unset($old_owner_id, $old_fraction); 	
while($row = mysql_fetch_array($db_daten))
{
  $str1='';$str2='';
  if($row['owner_id']==$old_owner_id)
  {
    if($row['fraction']!=$old_fraction)
    {
      $str1='<font color="#FF0000">';
      $str2='</font>';
      $sharer++;
    }
  }
  
  
  
  echo '<tr>';
  echo '<td align="right">'.$row['owner_id'].'</td>';
  echo '<td>'.$str1.$row['spielername'].' {'.$row['sn_ext1'].'}'.$str2.'</td>';
  echo '<td align="center">'.$row['fraction'].'</td>';
  
  //grün, wenn er innerhalb der letzten 7 tage online war, ansonsten normal weiß
  if($row['lastclick']>=time()-3600*24*7)$color='#00FF00';else $color='#FFFFFF';
  echo '<td align="right"><font color="'.$color.'">'.date("Y-m-d H:i:s",$row['lastclick']).'</font></td>';
  
  //rot, wenn er aktiv ist, viel gemacht hat, aber nur sehr wenig canmine hat
  $canmine=get_canmine($row['user_id']);
  if($row['lastclick']>=time()-3600*24*7 AND $canmine<10000 AND $row['donate']>10000000)$color='#FF0000';else $color='#FFFFFF';
  echo '<td align="right"><font color="'.$color.'">'.number_format($canmine, 0,",",".").'</font></td>';
  
  echo '<td align="right">'.number_format($row['donate'], 0,",",".").'</td>';
  echo '<td><a href="sou_fraccheck.php?freset=1&uid='.$row['user_id'].'">FRESET</a></td>';
  echo '</tr>';
  $old_owner_id=$row['owner_id'];
  $old_fraction=$row['fraction'];
}

echo '</table>';

echo 'Sharer: '.$sharer;

?>
</center>
</body>
</html>
