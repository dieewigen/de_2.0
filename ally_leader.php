<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.leader.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allyleader_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?php
include('resline.php');
include('ally/ally.menu.inc.php');
$allys=mysql_query("SELECT * FROM de_allys where leaderid='$ums_user_id'");


if(mysql_num_rows($allys)<1)

{

echo $allyleader_lang['msg_1'];

}

else

{

$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id'";

$result=mysql_query($query);

$clanid = mysql_result($result,0,"id");

$clantag = mysql_result($result,0,"allytag");



$query="SELECT * FROM de_user_data WHERE user_id='$userid'";

$result=mysql_query($query);

$clan = mysql_result($result,0,"allytag");



if($clantag==$clan)

{

$query = "UPDATE de_user_data SET status='1' WHERE user_id='$ums_user_id'";

$result = mysql_query($query);





$query = "UPDATE de_allys SET leaderid='$userid' WHERE id='$clanid'";

$result = mysql_query($query);









echo $allyleader_lang['msg_2'];

}

else

{

echo $allyleader_lang['msg_3'];

}



}



?>
<?php include('ally/ally.footer.inc.php'); ?>