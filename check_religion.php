<?php
include 'inc/header.inc.php';
include "lib/transaction.lib.php";
include "lib/religion.lib.php";
include 'inc/lang/'.$sv_server_lang.'_religion.lang.php';
include 'functions.php';

?>

<!DOCTYPE HTML>
<html>
<head>
<title><?=$religion_lang[religion]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php

//alle Spieler durchgehen
$db_datenx=mysql_query("SELECT de_login.owner_id, de_user_data.spielername, de_user_data.sector, de_user_data.system, de_login.status, de_login.register, de_login.last_click FROM de_login LEFT JOIN de_user_data ON(de_login.user_id=de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 ORDER BY de_user_data.spielername",$db);
$maxlevel=-1;
while($rowx = mysql_fetch_array($db_datenx)){
  $owner_id=$rowx['owner_id'];

  $level=get_religion_level($owner_id);

  echo '<div style="font-size: 20px;">'.$rowx['spielername'].': '.$level.'</div>';

  if($maxlevel<$level){
    $maxlevel=$level;
  }

}

echo '<div style="font-size: 20px;">Max: '.$maxlevel.'</div>';

?>
</body>
</html>