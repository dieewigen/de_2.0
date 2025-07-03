<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.delete.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, ally_id FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];$ally_id=$row['ally_id'];
$allytag=$row['allytag'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allydelete_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php
include('resline.php');
include('ally/ally.menu.inc.php');

if(!$isleader){
	echo $allydelete_lang['msg_1'];
}
else
{
	if(isset($a) && $a == 1)
	{
		$query = "select id from de_allys where leaderid=$ums_user_id";
		if ($result = @mysql_query($query))
		{
			$allyid = mysql_result($result,0,"id");
		}
		$query = "select max(kriegsstart) \"kriegsstart\" from de_ally_war, de_allys where (ally_id_angreifer=$allyid or ally_id_angegriffener=$allyid)";
		if ($result = @mysql_query($query))
		{
			$kriegsstarttime = @mysql_result($result,0,"kriegsstart");
			if ($kriegsstarttime)
			{
				$kriegsstart = strtotime($kriegsstarttime);
				$now = time();

				$vergangen = $now - $kriegsstart;

				$ultimatum = (60*60*72);


				if ($vergangen<=$ultimatum)
				{
					$uebrig = $ultimatum-$vergangen;
					$stunden = floor($uebrig / (60*60));

					if($stunden>0){
						$minuten = floor(($uebrig%($stunden*(60*60)))/60);
					}else{
						$minuten=0;
					}

					die("$allydelete_lang[msg_2_1] $stunden $allydelete_lang[msg_2_2] $minuten $allydelete_lang[msg_2_3]");
				}
			}
		}
		mysql_query("DELETE FROM de_allys where leaderid='$ums_user_id'");
		mysql_query("DELETE FROM de_ally_partner where ally_id_1=$allyid or ally_id_2=$allyid");
		mysql_query("DELETE FROM de_ally_war where ally_id_angreifer=$allyid or ally_id_angegriffener=$allyid");
		mysql_query("DELETE FROM de_ally_buendniss_antrag where ally_id_antragsteller=$allyid or ally_id_partner=$allyid");
		mysql_query("DELETE FROM de_ally_antrag where ally_id=$allyid");


		if($ally_id>0){
			mysql_query("UPDATE de_user_data SET ally_id=0, allytag='', status=0 WHERE ally_id='$ally_id'");
		}else{
			mysql_query("UPDATE de_user_data SET ally_id=0, allytag='', status=0 WHERE allytag='$allytag'");
		}
		//echo $allydelete_lang[msg_3];
		header("Location: allymain.php");
	}
	else
	{
		echo '<div class="cell text2" style="width: 600px;">'.$allydelete_lang['msg_4'];
		echo '<a href="ally_delete.php?a=1"><font face="tahoma" style="font-size:14pt;"><b>'.$allydelete_lang['msg_5'].'</a><br />';
	}
}



?>
<?php include('ally/ally.footer.inc.php'); ?>