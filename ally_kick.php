<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.kick.lang.php');
require_once('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
if($row = mysqli_fetch_assoc($db_daten)) {
    $restyp01=$row['restyp01'];
    $restyp02=$row['restyp02'];
    $restyp03=$row['restyp03'];
    $restyp04=$row['restyp04'];
    $restyp05=$row['restyp05'];
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

<?php

include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');

//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
$allys = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
    [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]);
if(mysqli_num_rows($allys) < 1)
{
	echo $allykick_lang['msg_1'];
}
else
{
	//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_allys WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
		[$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]);
	if($result) {
		if($row = mysqli_fetch_assoc($result)) {
	
			$clanid = $row['id'];
			$clantag = $row['allytag'];
			$leaderid = $row['leaderid'];
			$coleaderid1 = $row['coleaderid1'];
			$coleaderid2 = $row['coleaderid2'];
			$coleaderid3 = $row['coleaderid3'];
			$t_depot = $row['t_depot'];
			
		}
	}
	
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_user_data WHERE user_id=?",
		[$userid]);
	if($result) {
		if($row = mysqli_fetch_assoc($result)) {
	
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
				mysqli_execute_query($GLOBALS['dbi'],
					"UPDATE de_user_data SET ally_id=0, allytag='', status=0, ally_tronic=0 WHERE user_id=?",
					[$userid]);
				
				notifyUser($userid, $allykick_lang['msg_3_1'].' <b>'.$clantag.'</b> '.$allykick_lang['msg_3_2'], 6);
				echo '<div class="info_box text3">'.$allykick_lang['msg_4_1'].' '.$kick_fee.' '.$allykick_lang['msg_4_2'].'</div>';
				
				//Wenn ein Coleader gekickt wird, wird diese Änderung zusätzlich im Allianzdatensatz vermerkt
				if($coleaderid1 == $userid)
				{
					mysqli_execute_query($GLOBALS['dbi'],
						"UPDATE de_allys SET coleaderid1=-1 WHERE allytag=?",
						[$clantag]);
				}
				elseif($coleaderid2 == $userid)
				{
					mysqli_execute_query($GLOBALS['dbi'],
						"UPDATE de_allys SET coleaderid2=-1 WHERE allytag=?",
						[$clantag]);
				}
				elseif($coleaderid3 == $userid)
				{
					mysqli_execute_query($GLOBALS['dbi'],
						"UPDATE de_allys SET coleaderid3=-1 WHERE allytag=?",
						[$clantag]);
				}
				mysqli_execute_query($GLOBALS['dbi'],
					"UPDATE de_allys SET t_depot=t_depot-? WHERE allytag=?",
					[$kick_fee, $clantag]);

				$u_data = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT spielername FROM de_user_data WHERE user_id=?",
					[$userid]);
				$u_row = mysqli_fetch_assoc($u_data);
				$u_name = $u_row["spielername"];

				include('ally/allyfunctions.inc.php');
				writeHistory($clantag, $allykick_lang['msg_5_1'].' <i>'.$u_name.'</i> '.$allykick_lang['msg_5_2'], true);
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