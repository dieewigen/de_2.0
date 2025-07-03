<?php
include('inc/header.inc.php');
include('lib/basefunctions.lib.php');
include('inc/lang/'.$sv_server_lang.'_ally.join.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, col FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$col_count=$row['col'];

$ally_id=intval($_REQUEST['ally_id']);
$ally_data=getAllyByID($ally_id);
$a_name=$ally_data['allyname'];

$t_tojoin = round(($col_count / 4) -1, 0);
if ($t_tojoin < 0)
{
	$t_tojoin = 0;
}
if ($col_count == 0)
{
	$t_tojoin = 0;
}
if ($t_tojoin > 200)
{
	$t_tojoin=200;
}
$row=0;
$sum=0;
$transaction_result = mysql_query("SELECT * FROM de_transactions WHERE user_id='$ums_user_id' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
if ($transaction_result){
	if (mysql_num_rows($transaction_result)==1){
		$data = mysql_fetch_array($transaction_result);
		$sum = $data['amount'];
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyjoin_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php
include('resline.php');
include('ally/ally.menu.inc.php');


echo '<div align=center><div style="width: 592px" class="cell">';
//print("<tr><td><h2>$allyjoin_lang[msg_1], $ums_spielername</h2></td></tr>");
//print("<tr><td><hr></td></tr>");
if($ally_id<1){
	print('<tr><td><strong>'.$allyjoin_lang['msg_2_1'].' '.$t_tojoin.' '.$allyjoin_lang['msg_2_2'].'</strong></td></tr>');
	if ($sum > 0){
		$msg=str_replace('{VALUE1}', $sum, $allyjoin_lang['msg_3']);
		print('<tr><td><strong>'.$msg.'</strong></td></tr>');
	}
}

if (($restyp05 + $sum) < $t_tojoin){
	$t_missing = $t_tojoin - $restyp05 + $sum;
	print('<tr><td><font color="red"><strong>'.$allyjoin_lang['msg_5_1'].' '.$t_missing.' '.$allyjoin_lang['msg_5_2'].'</strong></font></tr></td>');
	$quit_script = true;
}

if ($quit_script){
	die(include('ally/ally.footer.inc.php'));
}

$ok=$_POST['ok'];
$warnung=$_POST['warnung'];
if($ok || $warnung){
	if(!$warnung)	{
		$query="SELECT * FROM de_user_data WHERE user_id='$ums_user_id'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		$user_ally_id = $row['ally_id'];
		$status = $row['status'];

		if(mysql_num_rows(mysql_query("SELECT id FROM de_allys WHERE leaderid='$ums_user_id'")))
		{
			die("$allyjoin_lang[msg_4]");
		}

		if($user_ally_id>0 AND $status==1)
		{
			$error=1;
		}
	}

	if($error){
		echo '
			<form name="register" method="POST" action="ally_join.php">'.$allyjoin_lang['msg_6'].'
				<input type="hidden" name="ally_id" value="'.$ally_id.'">
				<input type="submit" value="'.$allyjoin_lang['fertig'].'" name="warnung"></form>';
	}else{
		if($ally_id<1){
			echo $allyjoin_lang['msg_7'];
		}else{
			//echo 'A:'.$clan;
			$clan=str_replace('\\','',$clan);
			$clan=str_replace("'","\'",$clan);
			//$clan=str_replace("%20","+'",$clan);

			$query="SELECT * FROM de_allys WHERE id='$ally_id'";
			/*
			if($_SESSION['ums_user_id']==1){
				echo $query;
			}*/
			
			$result = mysql_query($query);
			$nb = mysql_num_rows($result);

			if($nb>0){
				$row     = mysql_fetch_array($result);

				$clanid = $row['id'];
				$allytag = $row['allytag'];
				$leaderid = $row['leaderid'];
				$coleaderid1 = $row['coleaderid1'];
				$coleaderid2 = $row['coleaderid2'];
				$coleaderid3 = $row['coleaderid3'];

				$sqlquery = "UPDATE de_user_data SET ally_id='$ally_id', allytag='$allytag', status=0 WHERE (user_id = '$ums_user_id')";
				$result = mysql_query($sqlquery);
				$antrag = htmlentities ($antrag,ENT_QUOTES);
				$antrag = str_replace("\n","<br>",$antrag);
				$sqlquery = "INSERT into de_ally_antrag (user_id, ally_id, antrag) VALUES ($ums_user_id, $clanid, '$antrag')";
				//echo $sqlquery;
				$result = @mysql_query($sqlquery);
				if (!$result){
					$sqlquery = "UPDATE de_ally_antrag SET ally_id=$clanid, antrag='$antrag' where user_id=$ums_user_id";
					$result = @mysql_query($sqlquery);
				}
				notifyUser($leaderid, $allyjoin_lang['msg_8'], "6");
				notifyUser($coleaderid1, $allyjoin_lang['msg_8'], "6");
				notifyUser($coleaderid2, $allyjoin_lang['msg_8'], "6");
				notifyUser($coleaderid3, $allyjoin_lang['msg_8'], "6");
				$transaction_result = mysql_query("SELECT * FROM de_transactions WHERE user_id='$ums_user_id' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
				if ($transaction_result){
					if (mysql_num_rows($transaction_result)==1){
						$data = mysql_fetch_array($transaction_result);
						$sum = $data['amount'];
						mysql_query("UPDATE de_user_data SET restyp05=restyp05+$sum WHERE user_id='$ums_user_id'");
						$transaction_result = mysql_query("UPDATE de_transactions SET amount='$t_tojoin' WHERE user_id='$ums_user_id' AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'");
						mysql_query("UPDATE de_user_data SET restyp05=restyp05-$t_tojoin WHERE user_id='$ums_user_id'");
						print('<strong>'.$allyjoin_lang['msg_9_1'].' '.$sum.' '.$allyjoin_lang['msg_9_2'].'</strong><br />');
					}else{
						mysql_query("INSERT INTO de_transactions (user_id, type, identifier, name, amount) VALUES($ums_user_id, 'C.A.R.S.', 'reg_fee', 'Tronic', '$t_tojoin')");
						mysql_query("UPDATE de_user_data SET restyp05=restyp05-$t_tojoin WHERE user_id='$ums_user_id'");
					}
				}
				echo '<strong>'.$allyjoin_lang['msg_10_1'].' '.$t_tojoin.' '.$allyjoin_lang['msg_10_2'].'</strong>';
			}
			else
			{
				echo $allyjoin_lang['msg_11'].' !';
			} // else $nb>0
		}  // else $clan


	} // $ok check
}else{ // else $ok
	$query = "SELECT allyname, antrag FROM de_ally_antrag antrag, de_allys allys where allys.id=antrag.ally_id and user_id = $ums_user_id";
	$result = mysql_query($query, $db);
	$row = mysql_fetch_array($result);

	$antrag_allyname = $row["allyname"];
	$antrag_antrag 	 = $row["antrag"];

	$query = "SELECT * FROM de_allys ORDER BY allyname ASC";
	$result = mysql_query($query, $db);
	$nb = mysql_num_rows($result);

	if ($antrag_allyname == "")
	{
		$active_a_request = $allyjoin_lang['msg_12'];
	}
	else
	{
		$active_a_request = $allyjoin_lang['msg_13_1'].' <strong>'.$antrag_allyname.'</strong> '.$allyjoin_lang['msg_13_2'].':<br /><br />'.$antrag_antrag;
	}

	print('<table width="600" class="cell">
				<tr>
					<td><h3>'.$allyjoin_lang['aktivebewerbung'].': </h3></td>
				</tr>
				<tr>
					<td>
						'.$active_a_request.'
					</td>
				</tr>
				<tr>
					<td><hr></td>
				</tr>
			</table>
	');

	print('
			<table width="600" class="cell">
				<tr>
					<td><h3>'.$allyjoin_lang['neuebewerbung'].': </h3></td>
				</tr>
				<tr>
					<td>
						<form name="register" method="POST" action="ally_join.php">
						<input type="hidden" name="ally_id" value="'.$ally_id.'">
						'.$allyjoin_lang['msg_14_1'].' <strong>'.$a_name.'</strong> '.$allyjoin_lang['msg_14_2'].':<br>
						<textarea name="antrag" cols="70" rows="7" wrap="virtual"></textarea>
						<br><br><input type="submit" value="'.$allyjoin_lang['bewerbungsenden'].'" name="ok">
					</td>
				</tr>
			</table>
	');


	/*
	echo 	"<script language=\"javascript\">\n".
		"function wechsel() {\n".
		"document.register.clan.value = document.register.clanlist.value+\"\";\n".
		"document.register.clan.focus();\n".
		"}\n".
		"</script>\n".
		"<form name=\"register\" method=\"POST\" action=\"ally_join.php\">\n".
		"Ich möchte Mitglied in der <input type=\"text\" name=\"clan\" value=\"$antrag_allyname\" size=\"20\"> Allianz werden. \n".
		"<br>\n".
		"<br>Clanliste:<br><select onChange=\"javascript:wechsel()\" size=\"6\" name=\"clanlist\">\n";


	$row = 0;

	while ($row < $nb)
	{
		$clanname 	= mysql_result($result,$row,"allyname");
		//$clankuerzel 	= mysql_result($result,$row,"allytag");

		echo "<option value=\"$clanname\">".$clanname."</option>";

		$row++;
	}

	echo 	"</select><BR><BR>".
		"Antragstext:<BR>".
		"<textarea name=\"antrag\" cols=\"40\" rows=\"7\" wrap=\"virtual\">$antrag_antrag</textarea><BR><BR>".
		"<input type=\"submit\" value=\"Fertig !\" name=\"ok\">".
		"</form>";
		*/
}

print('</div><br /><br />');

?>
<?php include('ally/ally.footer.inc.php'); ?>