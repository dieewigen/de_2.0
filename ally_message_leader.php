<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.messageleader.lang.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];
include_once('functions.php');

/*$db_daten=mysql_query("SELECT nic FROM de_login WHERE user_id='$ums_user_id'");
$row = mysql_fetch_array($db_daten);
$nic=$row["nic"];*/

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allymessageleader_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?php
include('resline.php');
$leaderpage = true;
include('ally/ally.menu.inc.php');
$text=$_POST['text'] ?? '';
$select=$_POST['select'] ?? '';

if($text) {
	$text=nl2br($text);
	$betreff=$_POST['betreff'];

	$an=$_POST['an'];

	$resource=mysql_query("SELECT leaderid FROM de_allys  WHERE allytag='$an'");

	while($row=mysql_fetch_array($resource)) {
		mysql_query("update de_user_data set newtrans = 1 where user_id = $row[leaderid]");
		$time=strftime("%Y%m%d%H%M%S");
		mysql_query("insert into de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) values ('$row[leaderid]', '$ums_user_id', '0', '0', '$allytag Leader', '$time', '$betreff', '$text',0)",$db);


		mysql_query($query);
	}

	echo $allymessageleader_lang['msg_1'];
}else {
	echo '<form name="register" method="POST">
				<table border="0" width="602" cellspacing="1" cellpadding="0">
					<tr class="tc">
						<td width="50%" height="21" colspan=2>'.$allymessageleader_lang['msg_2'].'</td>
					</tr>
					<tr class="cl">
						<td width="50%">'.$allymessageleader_lang['an'].':</td>
						<td width="50%">
							<select name="an">';

					$query = "SELECT allytag FROM de_allys order by allytag";
					$result = mysql_query($query);
					while ($row = mysql_fetch_array($result))
					{
							echo '
								<option value="'.$row['allytag'].'"
							';
							
							if ($select == urldecode($row['allytag'])) echo " selected";
							
							echo '>'.$row['allytag'].'</option>
							';
					}

	echo '
							</select></td>
				</tr>
				<tr class="cl">
					<td width="50%">'.$allymessageleader_lang['betreff'].':</td>
					<td width="50%"><input name="betreff" size="20"></td>
				</tr>
				<tr class="cl">
					<td width="50%">'.$allymessageleader_lang['nachricht'].':</td>
					<td width="50%"><textarea rows="5" name="text" cols="30"></textarea></td>
				</tr>
					<tr class="tc">
					<td width="50%" height="21" colspan="2">'.$allymessageleader_lang['msg_3'].'</td>
					</tr>
				</table>
				<br />
				<input type="submit" value="'.$allymessageleader_lang['abschicken'].'" name="B1"><input type="reset" value="'.$allymessageleader_lang['zurueck'].'" name="B2">
			</form>';

}
?>
<?php include('ally/ally.footer.inc.php'); ?>