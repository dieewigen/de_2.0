<?php 
include "../inccon.php";
include "../inc/sv.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Sektordatengenerator</title>
</head>
<body>
<?php


for ($i=2; $i<=700; $i=$i+2)
{

  //sk und bk festlegen
  $ok=0;
  /*
  $sk=rand(1, 5);
  while ($ok==0)
  {
    $bk=rand(1, 5);
    if ($sk!=$bk) $ok=1;
  }*/
  $sk=1;
  $bk=2;
  
  //sektorname festellen und bk in die db schreiben
  $zz=rand(100000, 999999);
  $sektorname='Protektorat '.$zz;
  mysql_query("UPDATE de_sector SET name='$sektorname', bk='$bk' WHERE sec_id='$i'",$db);
  
  //sk in db schreiben
  mysql_query("UPDATE de_user_data SET votefor='$sk' WHERE sector='$i'",$db);
  
  //sektorbild
  //$url='http://die-ewigen.de/b/sb'.rand(1,4).'.gif';
  $url='';
  mysql_query("UPDATE de_sector SET url='$url' WHERE sec_id='$i'",$db);
}
echo 'Sektordaten eingetragen.';
?>
</body>
</html>
