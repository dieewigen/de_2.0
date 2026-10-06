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
	//Abbuchung nur, wenn das Tronic in diesem Moment noch da ist (zwei parallele Anfragen) und nur für Mitglieder
	mysqli_execute_query($GLOBALS['dbi'],
	    "UPDATE de_user_data SET ally_tronic=ally_tronic+?, restyp05=restyp05-? WHERE user_id=? AND restyp05>=? AND status=1",
	    [$t_transfer, $t_transfer, $_SESSION['ums_user_id'], $t_transfer]);
	if (mysqli_affected_rows($GLOBALS['dbi']) == 1) {
		mysqli_execute_query($GLOBALS['dbi'],
		    "UPDATE de_allys SET t_depot=t_depot+? WHERE allytag=?",
		    [$t_transfer, $allytag]);
	}
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
	//nur Leader/Co-Leader genau dieser Allianz
	mysqli_execute_query($GLOBALS['dbi'],
	    "UPDATE de_allys SET tronic_zahlungsziel=? WHERE allytag=? AND (leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?)",
	    [$tronic_zahlungsziel, $allytag, $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
}

//Mahnung aus der Liste (ally_finance.php?memberid=…); $memberid kam früher über register_globals,
//seitdem war der Knopf ohne Wirkung
$memberid = intval($_GET['memberid'] ?? 0);
if (isset($memberid) && $memberid > 0)
{
	if ($isleader || $iscoleader)
	{
		//nur Mitglieder der eigenen Allianz, keine Bewerber (gleicher allytag, status 0)
		$result = mysqli_execute_query($GLOBALS['dbi'],
		    "SELECT spielername FROM de_user_data WHERE user_id=? AND allytag=? AND status=1",
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
	//Erfolg (Überweisung, Mahnung zugestellt) grün, sonst rot
	$erfolg = strpos($message, $allyfinance_lang['msg_1_1']) === 0 || strpos($message, $allyfinance_lang['msg_8_1']) === 0;
	echo '<div class="mod ally-meldung"><div class="mod-meldung '.($erfolg ? 'mod-meldung-ok' : 'mod-meldung-fehler').'">'.$message.'</div></div>';
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

rahmen_oben('Finanzen');
echo '<div class="ally mod">';

//Allianzdepot, eigener Einzahlungsstand, Zahlungsziel
echo '
	<div class="ally-kacheln ally-kacheln-3">
		<div class="ov-wert"><span class="mod-typ">Allianzdepot</span><b>'.number_format($t_depot, 0, '', '.').'</b><small>'.$allyfinance_lang['tronic'].'</small></div>
		<div class="ov-wert"><span class="mod-typ">Dein Einzahlungsstand</span><b>'.number_format($t_level, 0, '', '.').'</b><small>'.$allyfinance_lang['tronic'].'</small></div>
		<div class="ov-wert"><span class="mod-typ">Zahlungsziel</span><b>'.number_format($tronic_zahlungsziel, 0, '', '.').'</b><small>'.$allyfinance_lang['tronic'].' je Mitglied</small></div>
	</div>';
if ($t_level < $tronic_zahlungsziel)
{
	$t_value = abs($t_level);
	$t_diff = $tronic_zahlungsziel-$t_level;
	echo '<div class="mod-meldung mod-meldung-fehler ally-abstand">'.$allyfinance_lang['msg_13_1'].' '.$t_diff.' '.$allyfinance_lang['tronic'].', '.$allyfinance_lang['msg_13_2'].'</div>';
}

//Überweisung ins Allianzdepot
echo '
	<div class="ally-abschnitt">
		<div class="mod-typ">'.$allyfinance_lang['tueberweisen'].'</div>
		<form action="ally_finance.php" method="post" name="transfer" class="ally-zeilenformular">
			<input type="text" name="t_transfer" value="0" size="6" class="mod-eingabe" title="'.$allyfinance_lang['ueberweisungssumme'].'">
			<span class="ally-leise-text">Tronic</span>
			<input type="submit" name="submit" value="'.$allyfinance_lang['ueberweisen'].'" class="mod-btn">
			<input type=hidden name=transfer value=1>
		</form>
	</div>';

if ($isleader || $iscoleader)
{
	echo '
	<div class="ally-abschnitt">
		<div class="mod-typ">Tronic-Zahlungsziel</div>
		<form action="ally_finance.php" method="post" name="tax" class="ally-zeilenformular">
			<input type="text" name="tzz" value="'.$tronic_zahlungsziel.'" size="8" maxlength="8" class="mod-eingabe">
			<span class="ally-leise-text">Tronic je Mitglied</span>
			<input type=submit name="changetzz" value="Zahlungsziel &auml;ndern" class="mod-btn mod-btn-leise">
		</form>
	</div>';
}

if ($isleader || $iscoleader)
{
	echo '
	<div class="ally-abschnitt">
		<div class="mod-typ">'.$allyfinance_lang['status'].'</div>
		<div class="ally-finanz ally-zeilenkopf">
			<span>'.$allyfinance_lang['name'].'</span>
			<span class="ally-rechts">'.$allyfinance_lang['kollektoren'].'</span>
			<span class="ally-rechts">'.$allyfinance_lang['koordinaten'].'</span>
			<span class="ally-rechts">'.$allyfinance_lang['kontostand'].'</span>
			<span></span>
		</div>
		<div class="ally-zeilen">';
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
			$member_koordinaten = $member_sector.":".$member_system;
			$member_kontostand = $member_data["ally_tronic"];
			$mahnlink = "";
			$rueckstand = '';
			if ($member_kontostand < $tronic_zahlungsziel)
			{
				$rueckstand = ' ally-rueckstand';
				$mahnlink = '<a href="ally_finance.php?memberid='.$member_id.'" class="mod-btn mod-btn-leise ally-btn-klein">'.$allyfinance_lang['mahnen'].'</a>';
			}

			echo '
			<div class="ally-finanz">
				<span class="ally-finanz-name">'.$member_spielername.'</span>
				<span class="ally-zahl">'.$member_kollektoren.'</span>
				<span class="ally-zahl">'.$member_koordinaten.'</span>
				<span class="ally-zahl'.$rueckstand.'">'.$member_kontostand.'</span>
				<span class="ally-rechts">'.$mahnlink.'</span>
			</div>';
		}
	}
	echo '</div></div>';
}
echo '</div>';
rahmen_unten();
?>
<?php include('ally/ally.footer.inc.php'); ?>

</body>
</html>