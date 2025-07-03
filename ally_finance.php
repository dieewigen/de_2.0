<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.finance.lang.php');
include_once('functions.php');

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, ally_tronic FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];
$t_level = $row["ally_tronic"];

$allys=mysql_query("SELECT * FROM de_allys where leaderid='$ums_user_id'");
$allys2=mysql_query("SELECT * FROM de_allys where coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'");

if(mysql_num_rows($allys)>=1)
{
	$isleader = true;
}
if(mysql_num_rows($allys2)>=1)
{
	$iscoleader = true;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyfinance_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<body>



<?php
include('lib/basefunctions.lib.php');

$message='';

$transfer=intval($_POST['transfer'] ?? 0);
$t_transfer=intval($_POST['t_transfer'] ?? 0);

if ($transfer=="1" && $restyp05 >= $t_transfer && $t_transfer > 0){
	mysql_query("UPDATE de_user_data SET ally_tronic=ally_tronic+$t_transfer, restyp05=restyp05-$t_transfer WHERE user_id='$ums_user_id'");
	mysql_query("UPDATE de_allys SET t_depot=t_depot+$t_transfer WHERE allytag='$allytag'");
	$message = "$allyfinance_lang[msg_1_1] $t_transfer $allyfinance_lang[msg_1_2]";
	$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, ally_tronic FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
	$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
	$allytag=$row["allytag"];
	$t_level = $row["ally_tronic"];
}elseif ($transfer == "1"){
	$message = $allyfinance_lang['msg_2_1'].' ('.$t_transfer.' '.$allyfinance_lang['msg_2_2'].')';
}

/*
if ($tax=="1" && $t_tax > 0 && $t_tax <= 200)
{
	if ($isleader || $iscoleader)
	{
		mysql_query("UPDATE de_user_data SET ally_tronic=ally_tronic-$t_tax WHERE allytag='$allytag' AND status='1'");
		$message = "$allyfinance_lang[msg_3_1] $t_tax $allyfinance_lang[msg_3_2]";
		include("ally/allyfunctions.inc.php");
		writeHistory($allytag, "$allyfinance_lang[msg_4_1] $t_tax $allyfinance_lang[msg_4_2]");

		$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, newtrans, newnews, allytag, ally_tronic FROM de_user_data WHERE user_id='$ums_user_id'",$db);
		$row = mysql_fetch_array($db_daten);
		$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
		$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
		$allytag=$row["allytag"];
		$t_level = $row["ally_tronic"];
	}
	else
	{
		$message = $allyfinance_lang[msg_5];
	}
}
elseif ($tax == "1")
{
	$message = "$allyfinance_lang[msg_6_1] ($t_transfer $allyfinance_lang[msg_6_2]";
}*/

if(isset($_POST['changetzz']))
{
	$tronic_zahlungsziel=intval($_POST['tzz']);
	mysql_query("UPDATE de_allys SET tronic_zahlungsziel='$tronic_zahlungsziel' WHERE allytag='$allytag'");
}

if (isset($memberid) && $memberid > 0)
{
	if ($isleader || $iscoleader)
	{
		$m_result = mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$memberid' AND allytag='$allytag'");
		if ($m_result)
		{
			$m_numrows = mysql_num_rows($m_result);
			if ($m_numrows == 1)
			{
				$m_data = mysql_fetch_array($m_result);
				$gemahnt_name = $m_data["spielername"];
				notifyUser($memberid, $allyfinance_lang['msg_7'], 6);
				$message = "$allyfinance_lang[msg_8_1] $gemahnt_name $allyfinance_lang[msg_8_2]";
				notifyUser($ums_user_id, "$allyfinance_lang[msg_9_1] $gemahnt_name $allyfinance_lang[msg_9_2]",6);
				$db_daten=mysql_query("SELECT newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'",$db);
				$row = mysql_fetch_array($db_daten);$newtrans=$row["newtrans"];$newnews=$row["newnews"];
			}
			else
			{
				$message = $allyfinance_lang['msg_10'];
			}
		}
		else
		{
			$message = $allyfinance_lang['msg_11'];
		}
	}
	else
	{
		$message = $allyfinance_lang['msg_12'];
	}
}

include "resline.php";
include ("ally/ally.menu.inc.php");
if (strlen($message) > 0)
{
	print("<br><table width=600 class=\"cell\"><tr>");
	print("<td width=30 align=left valign=top><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_arz.gif\" alt=Information border=0> </td><td align=left><font size=1> $message</font><br>");
	print("</td></tr></table>");
}

// Abfrage auf $iscoleader hinzugef&uuml;gt von Ascendant (01.09.2002)
if (!$ismember and !$isleader and !$iscoleader) die(include("ally/ally.footer.inc.php"));

if($isleader || $iscoleader)
{
        $query = "SELECT * FROM de_allys where leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";
        $result = mysql_query($query);
}
else
{
        $query = "SELECT * FROM de_allys ally, de_user_data user where user.allytag=ally.allytag and user.user_id='$ums_user_id'";
        $result = mysql_query($query);
}
$clanid = mysql_result($result,0,"id");
$clanname = mysql_result($result,0,"allyname");
$clankuerzel = mysql_result($result,0,"allytag");
$homepageurl = mysql_result($result,0,"homepage");
$leaderid = mysql_result($result,0,"leaderid");
$coleaderid1 = mysql_result($result,0,"coleaderid1");
$coleaderid2 = mysql_result($result,0,"coleaderid2");

$t_depot = mysql_result($result,0,"t_depot");

$tronic_zahlungsziel = mysql_result($result,0,"tronic_zahlungsziel");

print('<div align="center" class="cell" style="width: 600px;"><table width="100%" class="cell">');
print('<tr><td><h2>'.$allyfinance_lang['welcome'].', '.$ums_spielername.'</h2></td></tr>');
print('<tr><td><hr></td></tr>');
print('<tr><td>'.$allyfinance_lang['aktuellerstand'].': '.$t_depot.' '.$allyfinance_lang['tronic'].'</strong></td></tr>');
print('<tr><td><hr></td></tr>');
print('<tr><td>'.$allyfinance_lang['ihraktuellerstand'].': '.$t_level.' '.$allyfinance_lang['tronic'].'</strong> (Dein Allianz-Zahlungsziel: '.$tronic_zahlungsziel.')</td></tr>');
if ($t_level < $tronic_zahlungsziel)
{
	$t_value = abs($t_level);
	$t_diff = $tronic_zahlungsziel-$t_level;
	print('<tr><td style="color: #FF0000;">'.$allyfinance_lang['msg_13_1'].' '.$t_diff.' '.$allyfinance_lang['tronic'].', '.$allyfinance_lang['msg_13_2'].'</td></tr>');
}
print('<tr><td><hr></td></tr>');
print('<tr><td><strong>'.$allyfinance_lang['tueberweisen'].'</strong></td></tr>');
print('<tr><td><form action="ally_finance.php" method="post" name="transfer">');
print($allyfinance_lang['ueberweisungssumme'].' <input type="text" name="t_transfer" value="0" size="6"> <input type="submit" name="submit" value="'.$allyfinance_lang['ueberweisen'].'">');
print('<input type=hidden name=transfer value=1>');
print('</form></td></tr>');
if ($isleader || $iscoleader)
{
	print('<tr><td><hr></td></tr>');
	//print("<tr><td><strong>$allyfinance_lang[msg_14]</strong></td></tr>");
	print('<tr><td><strong>Tronic Zahlungsziel</strong></td></tr>');
	print('<tr><td><form action="ally_finance.php" method="post" name="tax">');
	/*
	print("$allyfinance_lang[steuersumme]
		<select name=t_tax>
			<option value=5>5</option>
			<option value=10>10</option>
			<option value=15>15</option>
			<option value=20>20</option>
			<option value=25>25</option>
			<option value=30>30</option>
			<option value=35>35</option>
			<option value=40>40</option>
			<option value=45>45</option>
			<option value=50>50</option>
			<option value=75>75</option>
			<option value=100>100</option>
			<option value=150>150</option>
			<option value=200>200</option>
		</select>
	<input type=submit name=submit value=\"$allyfinance_lang[bescheidabsenden] \">");
	print("<input type=hidden name=tax value=1>");
	*/
	echo 'Zahlungsziel: <input type="text" name="tzz" value="'.$tronic_zahlungsziel.'" size="8" maxlength="8">&nbsp;';
	echo '<input type=submit name="changetzz" value="Zahlungsziel &auml;ndern">';
	
	print('</form></td></tr>');
}

if ($isleader || $iscoleader)
{
	print('<tr><td><hr></td></tr>');
	print('<tr><td><strong>'.$allyfinance_lang['status'].'</strong></td></tr>');
	print('<tr><td>');
	print('
			<table width="100%">
				<tr>
					<td align="center" bgcolor="#1c1c1c"><strong>'.$allyfinance_lang['name'].'</strong></td><td align="center" bgcolor="#1c1c1c"><strong>'.$allyfinance_lang['kollektoren'].'</strong></td><td align="center" bgcolor="#1c1c1c"><strong>'.$allyfinance_lang['koordinaten'].'</strong></td><td align="center" bgcolor="#1c1c1c"><strong>'.$allyfinance_lang['kontostand'].'</strong></td><td bgcolor="#1c1c1c">&nbsp;</td>
				</tr>

	');
	$member_result = mysql_query("SELECT user_id, spielername, col, sector, `system`, ally_tronic FROM de_user_data WHERE allytag='$allytag' AND status='1' ORDER BY ally_tronic, sector, `system` ASC");
	if ($member_result)
	{
		$member_numrows = mysql_num_rows($member_result);
		for ($m = 0;$m<$member_numrows; $m++)
		{
			$member_data = mysql_fetch_array($member_result);
			$member_id = $member_data["user_id"];
			$member_spielername = $member_data["spielername"];
			$member_kollektoren = $member_data["col"];
			$member_sector = $member_data["sector"];
			$member_system = $member_data["system"];
			$member_koordinaten = "[".$member_sector.":".$member_system."]";
			$member_kontostand = $member_data["ally_tronic"];
			$mahnlink = "";
			if ($member_kontostand < $tronic_zahlungsziel)
			{
				$member_kontostand = "<font color=red>$member_kontostand</font>";
				$mahnlink = "<a href=\"ally_finance.php?memberid=$member_id\">$allyfinance_lang[mahnen]</a>";
			}
			
			print('<tr><td align="center" bgcolor="#222222">'.$member_spielername.'</td><td align="center" bgcolor="#222222">'.$member_kollektoren.'</td><td align="center" bgcolor="#222222">'.$member_koordinaten.'</td><td align="center" bgcolor="#222222">'.$member_kontostand.'</td><td align="center" bgcolor="#222222">'.$mahnlink.'</td></tr>');
		}
	}
	print('</table></td></td>');
}
print('</table>');
?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>
<?php include('fooban.php'); ?>
</body>
</html>