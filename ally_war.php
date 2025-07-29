<?php
$bestehende_kriege[] = '';
$an = '';
$selected = '';

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.war.lang.php');
include('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$allytag=$row['allytag'];

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
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys 
		 WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
		[$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]);
	$row = mysqli_fetch_assoc($result);
	$allyid = $row["id"];
}
else
{
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys ally, de_user_data user 
		 WHERE ally.allytag=user.allytag AND user_id=?",
		[$ums_user_id]);
	$row = mysqli_fetch_assoc($result);
	$allyid = $row["id"];
}

if(isset($peaceto) && (isset($isleader) || isset($iscoleader)))
{
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE allytag=?",
		[$peaceto]);
	$row = mysqli_fetch_assoc($result);
	$peaceto_id = $row["id"];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT friedensangebot, kriegsstart FROM de_ally_war 
		 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?) 
		 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
		[$allyid, $peaceto_id, $peaceto_id, $allyid]);
	$alreadyinXallys = mysqli_num_rows($result);
	if ($alreadyinXallys == 0)
		die ("$allywar_lang[msg_1]..");

	$row = mysqli_fetch_assoc($result);
	$friedensangebot = $row["friedensangebot"];
	$kriegsstart = strtotime($row["kriegsstart"]);

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
		mysqli_execute_query($GLOBALS['dbi'],
			"DELETE FROM de_ally_war 
			 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?) 
			 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
			[$allyid, $peaceto_id, $peaceto_id, $allyid]);
		echo "$allywar_lang[msg_3] $peaceto!";
		include("ally/allyfunctions.inc.php");
		writeHistory($allytag, "$allywar_lang[msg_4_1] <i>$peaceto</i> $allywar_lang[msg_4_2]",true);
		writeHistory($peaceto, "$allywar_lang[msg_4_1] <i>$allytag</i> $allywar_lang[msg_4_2]",true);

	}
	elseif ($friedensangebot == 0)
	{
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_ally_war SET friedensangebot=? 
			 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?) 
			 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
			[$allyid, $allyid, $peaceto_id, $peaceto_id, $allyid]);
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
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(*) as count FROM de_ally_war 
		 WHERE ally_id_angreifer=? OR ally_id_angegriffener=?",
		[$allyid, $allyid]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys >= 2)
		die ("$allywar_lang[msg_9_1] $alreadyinXallys $allywar_lang[msg_9_2] $alreadyinXallys $allywar_lang[msg_9_3]!");

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$angegriffener = $row["id"];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(user_id) as count, SUM(score) as sum FROM de_user_data WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$feindmitglieder = $row['count'];
	$feindpunkte = $row['sum'];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(user_id) as count, SUM(score) as sum FROM de_user_data WHERE allytag=?",
		[$allytag]);
	$row = mysqli_fetch_assoc($result);
	$selbstmitglieder = $row['count'];
	$selbstpunkte = $row['sum'];

	if (($selbstmitglieder/2)>$feindmitglieder)
		die("$allywar_lang[msg_10]");

	if (($selbstpunkte/2)>$feindpunkte)
		die("$allywar_lang[msg_11]");


	if (($feindmitglieder/3)>$selbstmitglieder)
		die("$an $allywar_lang[msg_12]");

	if (($feindpunkte/3)>$selbstpunkte)
		die("$an $allywar_lang[msg_13]");

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_buendniss_antrag 
		 WHERE (ally_id_antragsteller=? AND ally_id_partner=?) 
		 OR (ally_id_antragsteller=? AND ally_id_partner=?)",
		[$allyid, $angegriffener, $angegriffener, $allyid]);

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_partner 
		 WHERE (ally_id_1=? AND ally_id_2=?) 
		 OR (ally_id_1=? AND ally_id_2=?)",
		[$allyid, $angegriffener, $angegriffener, $allyid]);

	mysqli_execute_query($GLOBALS['dbi'],
		"INSERT INTO de_ally_war 
		 (ally_id_angreifer, ally_id_angegriffener, kriegsstart, friedensangebot) 
		 VALUES (?, ?, NOW(), 0)",
		[$allyid, $angegriffener]);

	echo $allywar_lang['msg_14'].' !';
	include('ally/allyfunctions.inc.php');
	writeHistory($allytag, "$allywar_lang[msg_15_1] <i>$an</i> $allywar_lang[msg_15_2]",true);
	writeHistory($an, "$allywar_lang[msg_16_1] <i>$allytag</i> $allywar_lang[msg_16_2]",true);

}else {
  	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT ally_id_angegriffener, ally_id_angreifer FROM de_ally_war 
		 WHERE (ally_id_angegriffener=? OR ally_id_angreifer=?)",
		[$allyid, $allyid]);
	if (mysqli_num_rows($result))	{
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

		while ($row = mysqli_fetch_assoc($result))
		{
			if ($row['ally_id_angegriffener'] == $allyid)
				$showallyid = $row['ally_id_angreifer'];
			else
				$showallyid = $row['ally_id_angegriffener'];
				
			$result2 = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_allys WHERE id=?",
				[$showallyid]);
			$row2 = mysqli_fetch_assoc($result2);
			$angegriffener = $row2["allytag"];

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

				$result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT allytag, id FROM de_allys ORDER BY allytag");
				while ($row = mysqli_fetch_assoc($result))
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