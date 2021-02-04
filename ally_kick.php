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
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.kick.lang.php';
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$kick_fee = 25;
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allykick_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">
<?
include "resline.php";
include ("ally/ally.menu.inc.php");
include ("lib/basefunctions.lib.php");

//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
$allys=mysql_query("SELECT * FROM de_allys where leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'");
if(mysql_num_rows($allys)<1)
{
	echo $allykick_lang[msg_1];
}
else
{
	//Erweiterung des Querys auf Abfrage von Coleadern von Ascendant (05.09.2002)
	$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
	$result=mysql_query($query);
	$clanid = mysql_result($result,0,"id");
	$clantag = mysql_result($result,0,"allytag");
	$leaderid = mysql_result($result,0,"leaderid");
	$coleaderid1 = mysql_result($result,0,"coleaderid1");
	$coleaderid2 = mysql_result($result,0,"coleaderid2");
	$coleaderid3 = mysql_result($result,0,"coleaderid3");
	$t_depot = mysql_result($result,0,"t_depot");
	$query="SELECT * FROM de_user_data WHERE user_id='$userid'";
	$result=mysql_query($query);
	$clan = mysql_result($result,0,"allytag");
	if("$leaderid"=="$userid")
	{
		echo $allykick_lang[msg_2];
	}
	else
	{
		if ($t_depot >= $kick_fee)
		{
			if($clantag==$clan)
			{
				$query = "UPDATE de_user_data SET ally_id=0, allytag='', status='0', ally_tronic='0' WHERE user_id='$userid'";
				$result = mysql_query($query);
				notifyUser($userid, "$allykick_lang[msg_3_1] <b>$clantag</b> $allykick_lang[msg_3_2]", 6);
				echo '<div class="info_box text3">'.$allykick_lang[msg_4_1].' '.$kick_fee.' '.$allykick_lang[msg_4_2].'</div>';
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
				mysql_query("UPDATE de_allys SET t_depot=t_depot-$kick_fee WHERE allytag='$clantag'");

				$u_data=mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$userid'",$db);
				$u_row = mysql_fetch_array($u_data);
				$u_name=$u_row["spielername"];

				include("ally/allyfunctions.inc.php");
				writeHistory($clantag, "$allykick_lang[msg_5_1] <i>$u_name</i> $allykick_lang[msg_5_2]", true);
			}
			else
			{
				echo $allykick_lang[msg_6];
			}
		}
		else
		{
			print("$allykick_lang[msg_7_1] ".($kick_fee - $t_depot)." $allykick_lang[msg_7_2]");
		}
	}
}
?>
<?php include("ally/ally.footer.inc.php") ?>