<?php
//	--------------------------------- ally_ablehnen.php ---------------------------------
//	Funktion der Seite:		Ablehnen eines Beitrittgesuchs
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der �nderungsbefugnis
//							  auf Coleader
//  -------------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.ablehnen.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyablehnen_lang['title']; ?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<?php
include('resline.php');
include('ally/ally.menu.inc.php');
include('lib/basefunctions.lib.php');
//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
$allys=mysql_query("SELECT * FROM de_allys where leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'");


if(mysql_num_rows($allys)<1)
{
	echo $allyablehnen_lang['msg_1'];
}
else
{
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
	$result=mysql_query($query);
	$clanid = mysql_result($result,0,"id");
	$clantag = mysql_result($result,0,"allytag");

	if($userid)
	{
		$query="SELECT * FROM de_user_data WHERE user_id='$userid'";
		$result=mysql_query($query);
		$clan = mysql_result($result,0,"allytag");

		if($clantag==$clan)
		{
			$query = "UPDATE de_user_data SET allytag='' WHERE user_id='$userid'";
			$result = mysql_query($query);

			$query = "DELETE from de_ally_antrag WHERE user_id='$userid'";
			$result = mysql_query($query);
			$transaction_result = mysql_query("SELECT * FROM de_transactions WHERE user_id='$userid' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
			if ($transaction_result)
			{
				if (mysql_num_rows($transaction_result)==1)
				{
					$data = mysql_fetch_array($transaction_result);
					$sum = $data["amount"];
					mysql_query("UPDATE de_user_data SET restyp05=restyp05+$sum WHERE user_id='$userid'");
					$transaction_result = mysql_query("UPDATE de_transactions SET amount='0' WHERE user_id='$userid' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
				}
			}
			notifyUser($userid, $allyablehnen_lang['msg_2_1'].' '.$clantag.' '.$allyablehnen_lang['msg_2_2'].' '.$sum.' '.$allyablehnen_lang['msg_2_3'], 6);
			echo $allyablehnen_lang['msg_3'];
		}
		else
		{
			echo $allyablehnen_lang['msg_4'];
		}
	}
	elseif($allyid)
	{
		$query = "select count(*) from de_ally_buendniss_antrag where ally_id_antragsteller=$allyid";
		$result = mysql_query($query);
		$antragexists = 0;
		$antragexists = mysql_result($result,0,0);
		if ($antragexists == 0)
			die($allyablehnen_lang['msg_5']);

		$query = "delete from de_ally_buendniss_antrag where ally_id_antragsteller=$allyid and ally_id_partner=$clanid";
		$result = mysql_query($query);
		echo $allyablehnen_lang['msg_6'];
	}

}



?>
<?php include('ally/ally.footer.inc.php'); ?>