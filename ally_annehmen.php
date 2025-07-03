<?php
//	--------------------------------- ally_annehmen.php ---------------------------------
//	Funktion der Seite:		Annehmen eines Beitrittgesuchs oder eines Bündnisses
//  -------------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.annehmen.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allyablehnen_lang['title']?></title>
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
	$memberlimit = mysql_result($result,0,"memberlimit");


	if ($userid)
	{
		$query="SELECT * FROM de_user_data WHERE user_id='$userid'";
		$result=mysql_query($query);
		$clan = mysql_result($result,0,"allytag");
		$c_result = mysql_query("SELECT count(*) FROM de_user_data WHERE allytag='$clantag' AND status='1'");
		$m_counter = mysql_result($c_result,0,0);

		if ($memberlimit > $m_counter)
		{
			if($clantag==$clan)
			{
				$query = "UPDATE de_user_data SET status='1' WHERE user_id='$userid'";
				$result = mysql_query($query);

				$u_data=mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$userid'",$db);
				$u_row = mysql_fetch_array($u_data);
				$u_name=$u_row["spielername"];


				$query = "DELETE from de_ally_antrag WHERE user_id='$userid'";
				$result = mysql_query($query);
				$transaction_result = mysql_query("SELECT * FROM de_transactions WHERE user_id='$userid' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
				if ($transaction_result)
				{
					if (mysql_num_rows($transaction_result)==1)
					{
						$data = mysql_fetch_array($transaction_result);
						$sum = $data["amount"];
						mysql_query("UPDATE de_allys SET t_depot=t_depot+$sum WHERE id='$clanid'");
						$transaction_result = mysql_query("UPDATE de_transactions SET amount='0' WHERE user_id='$userid' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
					}
				}
				notifyUser($userid, "Die Allianz <b>$clantag</b> hat Ihrem Antrag zugestimmt und Sie aufgenommen. Die Registrierungsgeb&uuml;hr von $sum Tronic wurde dem Allianzdepot gutgeschrieben. Bitte beachten Sie, das Registrierungsgeb&uuml;hren nicht steuerlich absetzbar sind. <br>Herzlich Willkommen!", 6);

				echo '<div class="info_box text3">'.$allyablehnen_lang['msg_2_1'].' '.$sum.' '.$allyablehnen_lang['msg_2_2'].'.</div>';
				include('ally/allyfunctions.inc.php');
				writeHistory($clantag, $allyablehnen_lang['msg_3'].' <i>'.$u_name.'</i>',true);
			}
			else
			{
				echo '<div class="info_box text3">'.$allyablehnen_lang['msg_4'].'</div>';
			}
		}
		else
		{
			print('<div class="info_box text3">'.$allyablehnen_lang['msg_5'].'</div>');
		}
	}
	elseif($allyid)
	{
		$query = "select count(*) from de_ally_partner where ally_id_1 = '$allyid' OR ally_id_2 = '$clanid'";
		$result = mysql_query($query);
		$alreadyinXallys = mysql_result($result,0,0);
		if ($alreadyinXallys >= 2)
			die ($allyablehnen_lang['msg_6_1'].' '.$alreadyinXallys.' '.$allyablehnen_lang['msg_6_2'].' '.$alreadyinXallys.''.$allyablehnen_lang['msg_6_3']);


		$query = "select count(*) from de_ally_buendniss_antrag where ally_id_antragsteller='$allyid'";
		$result = mysql_query($query);
		$antragexists = 0;
		$antragexists = mysql_result($result,0,0);
		if ($antragexists == 0)
			die($allyablehnen_lang['msg_7']);
			
		//überprüfen ob man mit dem gewünschten bündnispartner evtl. im krieg ist
		$query = "SELECT * FROM de_ally_war WHERE (ally_id_angreifer = '$allyid' AND ally_id_angegriffener = '$allyid_partner') OR (ally_id_angreifer = '$allyid_partner' AND ally_id_angegriffener = '$allyid')";
		$db_daten = mysql_query($query);
		$num = mysql_num_rows($db_daten);
		if ($num>0){
			die ('<div class="info_box text2">Mit dieser Allianz herrscht Krieg und ein B&uuml;ndnis ist nicht m&ouml;glich.</div></body></html>');
		}

		//Test auf Diplomatiezentrum
		$ally_result = mysql_query("SELECT * FROM de_allys WHERE allytag='$allytag'");
		if ($ally_result){
			$ally_data = mysql_fetch_array($ally_result);
			//diplomatiezentrum
			$bldg=$ally_data['bldg1'];
		}

		//test auf vorhandenes allianzprojekt Diplomatiezentrum
		if($bldg<1){
		die('<br><div class="info_box text2">F&uuml;r ein Allianzb&uuml;ndnis wird ein Diplomatiezentrum ben&ouml;tigt.</div></body></html>');
		}
		

		//$query = "delete from de_ally_war where (ally_id_angreifer='$clanid' AND ally_id_angegriffener='$allyid) or (ally_id_angreifer=$allyid and ally_id_angegriffener=$clanid)";
		//$result = @mysql_query($query);

		$query = "INSERT INTO de_ally_partner (ally_id_1, ally_id_2) VALUES ($clanid,$allyid)";
		$result = mysql_query($query);
		$query = "DELETE FROM de_ally_buendniss_antrag WHERE (ally_id_antragsteller='$allyid' AND ally_id_partner='$clanid') OR (ally_id_antragsteller='$clanid' AND ally_id_partner='$allyid')";
		$result = mysql_query($query);
		echo $allyablehnen_lang['msg_8'];
		include("ally/allyfunctions.inc.php");
		$delallyid1_tag = getAllyTag($clanid);
		$delallyid2_tag = getAllyTag($allyid);

		writeHistory($delallyid1_tag, $allyablehnen_lang['msg_9_1'].' <i>'.$delallyid2_tag.'</i> '.$allyablehnen_lang['msg_9_2'], true);
		writeHistory($delallyid2_tag, $allyablehnen_lang['msg_9_1'].' <i>'.$delallyid1_tag.'</i> '.$allyablehnen_lang['msg_9_2'], true);

	}
}

?>
<?php include('ally/ally.footer.inc.php'); ?>