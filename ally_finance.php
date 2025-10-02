<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.finance.lang.php');
include_once('functions.php');

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, 
            newtrans, newnews, allytag, ally_tronic 
     FROM de_user_data WHERE user_id=?", 
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($result);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];
$t_level = $row["ally_tronic"];

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT COUNT(*) as count FROM de_allys WHERE leaderid=?",
    [$_SESSION['ums_user_id']]);
$count = mysqli_fetch_assoc($result);
$isleader = ($count['count'] >= 1);

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT COUNT(*) as count FROM de_allys WHERE coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
    [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
$count = mysqli_fetch_assoc($result);
$iscoleader = ($count['count'] >= 1);
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyfinance_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('lib/basefunctions.lib.php');

$message='';

$transfer=intval($_POST['transfer'] ?? 0);
$t_transfer=intval($_POST['t_transfer'] ?? 0);

if ($transfer=="1" && $restyp05 >= $t_transfer && $t_transfer > 0){
	mysqli_execute_query($GLOBALS['dbi'], 
	    "UPDATE de_user_data SET ally_tronic=ally_tronic+?, restyp05=restyp05-? WHERE user_id=?",
	    [$t_transfer, $t_transfer, $_SESSION['ums_user_id']]);
	mysqli_execute_query($GLOBALS['dbi'], 
	    "UPDATE de_allys SET t_depot=t_depot+? WHERE allytag=?",
	    [$t_transfer, $allytag]);
	$message = "$allyfinance_lang[msg_1_1] $t_transfer $allyfinance_lang[msg_1_2]";
	$result = mysqli_execute_query($GLOBALS['dbi'], 
	    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, 
	            newtrans, newnews, allytag, ally_tronic 
	     FROM de_user_data WHERE user_id=?",
	    [$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_array($result);
	$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
	$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
	$allytag=$row["allytag"];
	$t_level = $row["ally_tronic"];
}elseif ($transfer == "1"){
	$message = $allyfinance_lang['msg_2_1'].' ('.$t_transfer.' '.$allyfinance_lang['msg_2_2'].')';
}

if(isset($_POST['changetzz']))
{
	$tronic_zahlungsziel=intval($_POST['tzz']);
	mysqli_execute_query($GLOBALS['dbi'], 
	    "UPDATE de_allys SET tronic_zahlungsziel=? WHERE allytag=?",
	    [$tronic_zahlungsziel, $allytag]);
}

if (isset($memberid) && $memberid > 0)
{
	if ($isleader || $iscoleader)
	{
		$result = mysqli_execute_query($GLOBALS['dbi'], 
		    "SELECT spielername FROM de_user_data WHERE user_id=? AND allytag=?",
		    [$memberid, $allytag]);
		if ($result)
		{
			$m_numrows = mysqli_num_rows($result);
			if ($m_numrows == 1)
			{
				$m_data = mysqli_fetch_array($result);
				$gemahnt_name = $m_data["spielername"];
				notifyUser($memberid, $allyfinance_lang['msg_7'], 6);
				$message = "$allyfinance_lang[msg_8_1] $gemahnt_name $allyfinance_lang[msg_8_2]";
				notifyUser($_SESSION['ums_user_id'], "$allyfinance_lang[msg_9_1] $gemahnt_name $allyfinance_lang[msg_9_2]",6);
				$result = mysqli_execute_query($GLOBALS['dbi'], 
				    "SELECT newtrans, newnews FROM de_user_data WHERE user_id=?",
				    [$_SESSION['ums_user_id']]);
				$row = mysqli_fetch_array($result);$newtrans=$row["newtrans"];$newnews=$row["newnews"];
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
	print("<td width=30 align=left valign=top><img src=\"gp/g/".$_SESSION['ums_rasse']."_arz.gif\" alt=Information border=0> </td><td align=left><font size=1> $message</font><br>");
	print("</td></tr></table>");
}

// Abfrage auf $iscoleader hinzugef&uuml;gt von Ascendant (01.09.2002)
if (!$ismember and !$isleader and !$iscoleader) die(include("ally/ally.footer.inc.php"));

if($isleader || $iscoleader)
{
        $result = mysqli_execute_query($GLOBALS['dbi'], 
            "SELECT * FROM de_allys WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
            [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
}
else
{
        $result = mysqli_execute_query($GLOBALS['dbi'], 
            "SELECT ally.* FROM de_allys ally, de_user_data user WHERE user.allytag=ally.allytag AND user.user_id=?",
            [$_SESSION['ums_user_id']]);
}
$row = mysqli_fetch_assoc($result);
$clanid = $row["id"];
$clanname = $row["allyname"];
$clankuerzel = $row["allytag"];
$homepageurl = $row["homepage"];
$leaderid = $row["leaderid"];
$coleaderid1 = $row["coleaderid1"];
$coleaderid2 = $row["coleaderid2"];
$t_depot = $row["t_depot"];
$tronic_zahlungsziel = $row["tronic_zahlungsziel"];

print('<div align="center" class="cell" style="width: 600px;"><table width="100%" class="cell">');
print('<tr><td><h2>'.$allyfinance_lang['welcome'].', '.$_SESSION['ums_spielername'].'</h2></td></tr>');
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
	print('<tr><td><strong>Tronic Zahlungsziel</strong></td></tr>');
	print('<tr><td><form action="ally_finance.php" method="post" name="tax">');

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
	$member_result = mysqli_execute_query($GLOBALS['dbi'], 
	    "SELECT user_id, spielername, col, sector, `system`, ally_tronic 
	     FROM de_user_data WHERE allytag=? AND status='1' 
	     ORDER BY ally_tronic, sector, `system` ASC",
	    [$allytag]);
	if ($member_result)
	{
		$member_numrows = mysqli_num_rows($member_result);
		for ($m = 0;$m<$member_numrows; $m++)
		{
			$member_data = mysqli_fetch_array($member_result);
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

</body>
</html>