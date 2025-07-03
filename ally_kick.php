<?php
//	--------------------------------- ally_kick.php ---------------------------------
//	Funktion der Seite:		Kicken von Allianzmitgliedern
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der Kickbefugnis von Membern
//							  auf Coleader
//
//  --------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.kick.lang.php');
require_once('functions.php');

$db_daten = mysqli_query($db, "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'");
if($row = $db_daten->fetch_array(MYSQLI_BOTH)) {

	$restyp01=$row[0];
	$restyp02=$row[1];
	$restyp03=$row[2];
	$restyp04=$row[3];
	$restyp05=$row[4];
	$punkte=$row['score'];
	$newtrans=$row['newtrans'];
	$newnews=$row['newnews'];
	$sector=$row['sector'];
	$system=$row['system'];
	$kick_fee = 25;
	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allykick_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">
<?php

include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');

//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
$query = "SELECT * FROM de_allys where leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
$allys = mysqli_query($db, $query);
if(mysqli_num_rows($allys) < "1")
{
	echo $allykick_lang['msg_1'];
}
else
{
	//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
	$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
	if($result = mysqli_query($db, $query)) {
		if($row = $result->fetch_array(MYSQLI_BOTH)) {
	
			$clanid = $row['id'];
			$clantag = $row['allytag'];
			$leaderid = $row['leaderid'];
			$coleaderid1 = $row['coleaderid1'];
			$coleaderid2 = $row['coleaderid2'];
			$coleaderid3 = $row['coleaderid3'];
			$t_depot = $row['t_depot'];
			
		}
	}
	
	$query="SELECT * FROM de_user_data WHERE user_id='$userid'";
	if($result = mysqli_query($db, $query)) {
		if($row = $result->fetch_array(MYSQLI_BOTH)) {
	
			$clan = $row['allytag'];
			
		}
	}
	
	if($leaderid == $userid)
	{
		echo $allykick_lang['msg_2'];
	}
	else
	{
		
		
		if ($t_depot >= $kick_fee)
		{
			if($clantag == $clan)
			{
				$query = "UPDATE de_user_data SET ally_id=0, allytag='', status='0', ally_tronic='0' WHERE user_id='$userid'";
				mysqli_query($db, $query);
				
				notifyUser($userid, '$allykick_lang[\'msg_3_1\'] <b>$clantag</b> $allykick_lang[\'msg_3_2\']', 6);
				echo '<div class="info_box text3">'.$allykick_lang['msg_4_1'].' '.$kick_fee.' '.$allykick_lang['msg_4_2'].'</div>';
				
				//Wenn ein Coleader gekickt wird, wird diese �nderung zus�tzlich im Allianzdatensatz vermerkt
				if($coleaderid1 == $userid)
				{
					$result_updateally = mysql_query("UPDATE de_allys SET coleaderid1=-1 WHERE allytag='$clantag'");
				}
				elseif($coleaderid2 == $userid)
				{
					$result_updateally = mysql_query("UPDATE de_allys SET coleaderid2=-1 WHERE allytag='$clantag'");
				}
				elseif($coleaderid3 == $userid)
				{
					$result_updateally = mysql_query("UPDATE de_allys SET coleaderid3=-1 WHERE allytag='$clantag'");
				}
				mysqli_query($db, "UPDATE de_allys SET t_depot=t_depot-$kick_fee WHERE allytag='$clantag'");

				$u_data=mysqli_query($db, "SELECT spielername FROM de_user_data WHERE user_id='$userid'");
				$u_row = mysql_fetch_array($u_data);
				$u_name=$u_row["spielername"];

				include('ally/allyfunctions.inc.php');
				writeHistory($clantag, '$allykick_lang[\'msg_5_1\'] <i>$u_name</i> $allykick_lang[\'msg_5_2\']', true);
			}
			else
			{
				echo $allykick_lang['msg_6'];
			}
		}
		else
		{
			$t_need = $kick_fee-$t_depot;
			echo '<div class="info_box text2">'.$allykick_lang['msg_7_1'].' ('.$t_need.') '.$allykick_lang['msg_7_2'].'</div>';
		}
	}
}
?>
<?php include('ally/ally.footer.inc.php'); ?>