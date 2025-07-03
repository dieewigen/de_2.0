<?php
//	--------------------------------- ally_war.php ---------------------------------
//	Funktion der Seite:		�ndern und Anzeigen von verfeindeten Allianzen
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der Änderungsbefugnis der Kriege
//							  auf Coleader
//  --------------------------------------------------------------------------------

$bestehende_kriege[] = '';
$an = '';
$selected = '';

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.war.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$allytag=$row['allytag'];

/*$db_daten=mysql_query("SELECT nic FROM de_login WHERE user_id='$ums_user_id'");
$row = mysql_fetch_array($db_daten);
$nic=$row['nic'];*/

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allywar_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php
include('resline.php');

include('ally/ally.menu.inc.php');

if($isleader || $iscoleader)
{
	$query = "SELECT id FROM de_allys WHERE leaderid=$ums_user_id  OR coleaderid1=$ums_user_id OR coleaderid2=$ums_user_id OR coleaderid3=$ums_user_id";
	$result = mysql_query($query);
	$allyid = mysql_result($result,0,"id");
}
else
{
	$query = "SELECT id FROM de_allys ally, de_user_data user WHERE ally.allytag=user.allytag and user_id=$ums_user_id";
	$result = mysql_query($query);
	$allyid = mysql_result($result,0,"id");
}

if(isset($peaceto) && (isset($isleader) || isset($iscoleader)))
{
	$query = "SELECT id FROM de_allys WHERE allytag='$peaceto'";
	$result = mysql_query($query);
	$peaceto_id = mysql_result($result,0,"id");

	$query = "select friedensangebot, kriegsstart from de_ally_war where (ally_id_angreifer=$allyid and ally_id_angegriffener=$peaceto_id) or (ally_id_angreifer=$peaceto_id and ally_id_angegriffener=$allyid)";
	$result = @mysql_query($query);
	$alreadyinXallys = 0;
	$alreadyinXallys = mysql_num_rows($result);
	if ($alreadyinXallys == 0)
		die ("$allywar_lang[msg_1]..");

	$friedensangebot = @mysql_result($result,0,"friedensangebot");
	$kriegsstart = strtotime(@mysql_result($result,0,"kriegsstart"));

	$now = time();

	$vergangen = $now - $kriegsstart;

	$ultimatum = (60*60*72);



	if ($vergangen<=$ultimatum)
	{
		$uebrig = $ultimatum-$vergangen;
		$stunden = floor($uebrig / (60*60));

		$minuten = floor(($uebrig%($stunden*(60*60)))/60);


		die("$allywar_lang[msg_2_1] $stunden $allywar_lang[msg_2_2] $minuten $allywar_lang[msg_2_3]");
	}

	if ($friedensangebot == $peaceto_id)
	{
		$query="DELETE FROM de_ally_war WHERE (ally_id_angreifer=$allyid and ally_id_angegriffener=$peaceto_id) or (ally_id_angreifer=$peaceto_id and ally_id_angegriffener=$allyid)";
		$result = mysql_query($query);
		echo "$allywar_lang[msg_3] $peaceto!";
		include("ally/allyfunctions.inc.php");
		writeHistory($allytag, "$allywar_lang[msg_4_1] <i>$peaceto</i> $allywar_lang[msg_4_2]",true);
		writeHistory($peaceto, "$allywar_lang[msg_4_1] <i>$allytag</i> $allywar_lang[msg_4_2]",true);

	}
	elseif ($friedensangebot == 0)
	{
		$query="UPDATE de_ally_war set friedensangebot = $allyid where (ally_id_angreifer=$allyid and ally_id_angegriffener=$peaceto_id) or (ally_id_angreifer=$peaceto_id and ally_id_angegriffener=$allyid)";
		$result = mysql_query($query);
		echo $allywar_lang['msg_5_1'].' '.$peaceto.' '.$allywar_lang['msg_5_2'].' '.$peaceto.' '.$allywar_lang['msg_5_3'];
		include('ally/allyfunctions.inc.php');
		writeHistory($allytag, "$allywar_lang[msg_6_1] <i>$peaceto</i> $allywar_lang[msg_6_2]",true);
		writeHistory($peaceto, "$allywar_lang[msg_7_1] <i>$allytag</i> $allywar_lang[msg_7_2]",true);

	}
	elseif ($friedensangebot == $allyid)
	{
		echo $allywar_lang['msg_8_1'].' '.$peaceto.' '.$allywar_lang['msg_8_2'].' <a href="ally_message_leader.php?select='.urlencode($peaceto).'">'.$allywar_lang['msg_8_3'].'</a>';
	}
	else
	{
		echo $allywar_lang['msg_1'].'.. ?';
	}
}

$an=isset($_POST['an']) ? $_POST['an'] : false;
if($an and ($isleader || $iscoleader)){
	$query = "select count(*) from de_ally_war where ally_id_angreifer = $allyid or ally_id_angegriffener = $allyid";
	$result = mysql_query($query);
	$alreadyinXallys = mysql_result($result,0,0);
	if ($alreadyinXallys >= 2)
		die ("$allywar_lang[msg_9_1] $alreadyinXallys $allywar_lang[msg_9_2] $alreadyinXallys $allywar_lang[msg_9_3]!");

	$query = "SELECT id FROM de_allys WHERE allytag='$an'";
	$result = mysql_query($query);
	$angegriffener = mysql_result($result,0,"id");

	/*
	// Der Angegriffene hat schon zu viele Kriege ?!
	$query = "select count(*) from de_ally_war where ally_id_angreifer = $angegriffener or ally_id_angegriffener = $angegriffener";
	$result = mysql_query($query);
	$alreadyinXallys = mysql_result($result,0,0);
	if ($alreadyinXallys >= 2)
		die ("$an ist schon genug in Kämpfe verwickelt...");
	*/

	$query = "select count(user_id), sum(score) from de_user_data where allytag='$an'";
	$result = mysql_query($query);
	$feindmitglieder = mysql_result($result,0,0);
	$feindpunkte = mysql_result($result,0,1);

	$query = "select count(user_id), sum(score) from de_user_data where allytag='$allytag'";
	$result = mysql_query($query);
	$selbstmitglieder = mysql_result($result,0,0);
	$selbstpunkte = mysql_result($result,0,1);

	if (($selbstmitglieder/2)>$feindmitglieder)
		die("$allywar_lang[msg_10]");

	if (($selbstpunkte/2)>$feindpunkte)
		die("$allywar_lang[msg_11]");


	if (($feindmitglieder/3)>$selbstmitglieder)
		die("$an $allywar_lang[msg_12]");

	if (($feindpunkte/3)>$selbstpunkte)
		die("$an $allywar_lang[msg_13]");

	$query = "delete from de_ally_buendniss_antrag where (ally_id_antragsteller=$allyid and ally_id_partner=$angegriffener) or (ally_id_antragsteller=$angegriffener and ally_id_partner=$allyid)";
	$result = @mysql_query($query);

	$query = "delete from de_ally_partner where (ally_id_1=$allyid and ally_id_2=$angegriffener) or (ally_id_1=$angegriffener and ally_id_2=$allyid)";
	$result = @mysql_query($query);

	$sqlquery = "INSERT into de_ally_war (ally_id_angreifer , ally_id_angegriffener , kriegsstart, friedensangebot  ) VALUES ($allyid, $angegriffener, NOW(), 0)";
	$result = @mysql_query($sqlquery);

	echo $allywar_lang['msg_14'].' !';
	include('ally/allyfunctions.inc.php');
	writeHistory($allytag, "$allywar_lang[msg_15_1] <i>$an</i> $allywar_lang[msg_15_2]",true);
	writeHistory($an, "$allywar_lang[msg_16_1] <i>$allytag</i> $allywar_lang[msg_16_2]",true);

}else {
  	$query = "SELECT ally_id_angegriffener, ally_id_angreifer FROM de_ally_war where ((ally_id_angegriffener=$allyid) or (ally_id_angreifer=$allyid))";
	$result = mysql_query($query);
	if (mysql_num_rows($result))	{
		echo '
			<table border="0" width="600" cellspacing="0" cellpadding="0">
				<tr align="center">
					<td width="13" height="37" class="rol">&nbsp;</td>
					<td width="500" align="center" class="ro">'.$allywar_lang['msg_17'].':</td>
					<td width="13" class="ror">&nbsp;</td>
				</tr>
				<tr>
					<td width="13" class="rl">&nbsp;</td>
					<td>
						<table border="0" width="100%" cellspacing="1" cellpadding="0">
							<tr class="tc">
								<td><b>'.$allywar_lang['gegner'].'</td>
		';
		
		if($isleader || $iscoleader) {
			
			echo '
								<td><b>'.$allywar_lang['peace'].'</td>';
			
		}
		
		echo '
							</tr>
		';

		while ($row = @mysql_fetch_array($result))
		{
			if ($row['ally_id_angegriffener'] == $allyid)
				$showallyid = $row['ally_id_angreifer'];
			else
				$showallyid = $row['ally_id_angegriffener'];
			$query = "SELECT allytag FROM de_allys where id=$showallyid";
			$result2 = mysql_query($query);
			$angegriffener = @mysql_result($result2,0,"allytag");

			echo '
							<tr class="cl">
								<td>'.$angegriffener.'</td>
			';
			
			if($isleader || $iscoleader) {
			
				echo '
								<td><a href="'.$_SERVER['PHP_SELF'].'?peaceto='.urlencode($angegriffener).'">'.$allywar_lang['declarepeace'].'</a></td>
				';
			
			}
			
			echo '
							</tr>
			';
			
			$bestehende_kriege[] = $angegriffener;
		}

		echo '
						</table>
					<td width="13" class="rr">&nbsp;</td></tr>
					<tr><td width="13" class="rul">&nbsp;</td>
					<td width="13" class="ru">&nbsp;</td>
					<td width="13" class="rur">&nbsp;</td>
				</tr>
			</table>
			<br />
		';
	}
	if($isleader || $iscoleader)
	{

		echo '
			<form name="krieg" method="POST" action="'.$_SERVER['PHP_SELF'].'">
				<table border="0" width="600" cellspacing="0" cellpadding="0" class="cell">
				<tr class="tc"><td colspan="2"><br>'.$allywar_lang['war'].'</td></tr>
				<tr class="cl">
				<td width="50%"><br>'.$allywar_lang['an'].':</td>
				<td width="50%">
					<select name="an">
		';

				$query = "SELECT allytag, id FROM de_allys order by allytag";
				$result = mysql_query($query);
				while ($row = mysql_fetch_array($result))
				{
					if (!in_array($row['allytag'],$bestehende_kriege) and $allytag != $row['allytag'])
					{
						echo '<option value="'.$row['allytag'].'"';
						if ($selected==$row['id']) echo ' selected';
						echo '>'.$row['allytag'].'</option>\n';
					}
				}

		echo	    '</select></td>
			    </tr>';
		echo '<tr><td align="center" colspan="2"><input type="submit" value="'.$allywar_lang['abschicken'].'" name="B1"></td></tr>';
		echo '</table>';
			  
		echo '</form>';
	}
}


?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>