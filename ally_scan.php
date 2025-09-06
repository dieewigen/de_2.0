<?php
//        --------------------------------- ally_members.php ---------------------------------
//        Funktion der Seite:                Anzeigen der Allianzmitglieder
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der Adminrechte bis auf Leader ernennen
//                                                          auf Co-Leader
//  --------------------------------------------------------------------------------
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.scan.lang.php';
include 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, spielername 
     FROM de_user_data 
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$spielername=$row["spielername"]
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allyscan_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";
include ("ally/ally.menu.inc.php");

$scancost = 200;
$full_access = false;
$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT allytag FROM de_user_data WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($result);
$clankuerzel = $row["allytag"];

$ally_result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE allytag=?",
    [$clankuerzel]);
if ($ally_result)
{
	$ally_data = mysqli_fetch_assoc($ally_result);
	$ally_tronic = $ally_data["t_depot"];
	$clanid = $ally_data["id"];
	//scannerphalanx
	$bldg=$ally_data['bldg2'];
}
//test auf vorhandenes allianzprojekt scannerphalax
if($bldg<1)
{
  die('<br><div class="info_box text2">F&uuml;r das Scannen anderer Allianzen wird die Scannerphalanx ben&ouml;tigt.</div></body></html>');
}

//$source = createScanList($clankuerzel);

if (has_position("leaderid", $clankuerzel, $_SESSION['ums_user_id']) || has_position("coleaderid1", $clankuerzel, $_SESSION['ums_user_id']) || has_position("coleaderid2", $clankuerzel, $_SESSION['ums_user_id']) || has_position("coleaderid3", $clankuerzel, $_SESSION['ums_user_id']) || has_position("tacticalofficer1", $clankuerzel, $_SESSION['ums_user_id']) || has_position("leaderid", $clankuerzel, $_SESSION['ums_user_id']))
{
	$full_access = true;
}

if ($action == "do_scan" && $full_access && isset($ally_id) && !empty($ally_id))
{
	if ($ally_tronic < $scancost)
	{
		print("$allyscan_lang[msg_1_1] $scancost $allyscan_lang[msg_1_2]");
	}
	else
	{
		$scan_ally_result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT * FROM de_allys WHERE id=?",
			[$ally_id]);
		if ($scan_ally_result)
		{
			$scan_ally_data = mysqli_fetch_assoc($scan_ally_result);
			$scan_ally_tag = $scan_ally_data["allytag"];
			$scan_ally_name = html_entity_decode($scan_ally_data["allyname"]);

		}
		$timestamp = time();
		$datum = date("d.m.Y - G:i", $timestamp);
		$scanlist = createScanList($scan_ally_tag, $scan_ally_name, $datum);
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_allys SET t_depot = t_depot - ? WHERE id = ?",
			[$scancost, $clanid]);
		mysqli_execute_query($GLOBALS['dbi'],
			"INSERT INTO de_ally_scans SET owner_allytag=?, target_allytag=?, target_allyname=?, target_memberlist=?, scandate=?, timestamp=?",
			[$clankuerzel, $scan_ally_tag, $scan_ally_name, $scanlist, $datum, $timestamp]);
		print("$allyscan_lang[msg_2_1] $scan_ally_tag $allyscan_lang[msg_2_2] $scancost $allyscan_lang[msg_2_3]");
		include("ally/allyfunctions.inc.php");
		writeHistory($clankuerzel, "$allyscan_lang[msg_3_1] <i>$scan_ally_name</i> $allyscan_lang[msg_3_2]", true);
	}
}

print("<div align=center class=\"cell\" style=\"width: 600px;\"><table width=\"100%\">");
print("<tr><td><h2>$allyscan_lang[msg_4], $spielername</h2></td></tr>");
print("<tr><td><hr></td></tr>");
print("</table>");

if ($full_access)
{
	print("<div align=center><table width=600>");
	print("<tr><td><h3>$allyscan_lang[memberlisteerscannen]</h3></td></tr>");
	print("<tr><td><hr></td></tr>");
	if ($ally_tronic < $scancost)
	{
		print("<tr><td>$allyscan_lang[msg_1_1] $scancost $allyscan_lang[msg_1_2]</td></tr>");
	}
	else
	{
		print("<tr><td>");
		print("$allyscan_lang[msg_5_1] $scancost $allyscan_lang[msg_5_2].<br><br>");
		print("<form action=\"ally_scan.php\" name=\"scan\" method=\"post\">");
		print("<input type=\"hidden\" name=\"action\" value=\"do_scan\">");
		$list_result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT id, allytag, allyname FROM de_allys ORDER BY allyname ASC");
		if ($list_result)
		{
			$numrows = mysqli_num_rows($list_result);
			print("<select name=ally_id><option value=\"\" selected>$allyscan_lang[plschoose]...</option>");
			for ($i=0;$i<$numrows;$i++)
			{
				$list_data = mysqli_fetch_assoc($list_result);
				$id = $list_data["id"];
				$tag = $list_data["allytag"];
				$name = $list_data["allyname"];

				print("<option value=\"$id\">$name &nbsp;&nbsp;&nbsp; $tag</option>\n");
			}
			print("</select>");
		}

		print("&nbsp;&nbsp;<input type=submit name=submit value=\"$allyscan_lang[memberlisteerscannen]\">");
		print("</form>");
		print("</td></tr>");
	}
	print("<tr><td><hr></td></tr>");
	print("</table></div>");
}

print("<div align=center><table width=600>");
print("<tr><td><h3>$allyscan_lang[archiviertememberlisten]</h3></td></tr>");
print("<tr><td><hr></td></tr>");
print("<tr><td><table width=100%>");
print("<tr><td><strong>$allyscan_lang[allianztag]</td><td><strong>$allyscan_lang[allianzname]</strong></td><td><strong>$allyscan_lang[scandatum]</strong></td><td></td></tr>");
$ml_result = mysqli_execute_query($GLOBALS['dbi'],
	"SELECT * FROM de_ally_scans WHERE owner_allytag = ? ORDER BY timestamp DESC",
	[$clankuerzel]);
if ($ml_result)
{
	$numrows_ml = mysqli_num_rows($ml_result);
	for ($j=0;$j<$numrows_ml;$j++)
	{
		$ml_data = mysqli_fetch_assoc($ml_result);
		$scan_id = $ml_data["id"];
		$target_allytag = $ml_data["target_allytag"];
		$target_allyname = $ml_data["target_allyname"];
		if (strlen($target_allyname) > 45)
		{
			$target_allyname = substr($target_allyname, 0, 42)."...";
		}
		$scandate = $ml_data["scandate"];
		print("<tr><td>$target_allytag</td><td>$target_allyname</td><td>$scandate</td><td><a href=\"ally_scan.php?showid=$scan_id\">$allyscan_lang[anzeigen]</a></td></tr>");
	}
}

print("</table></td></tr>");
print("<tr><td><hr></td></tr>");
print("</table></div>");

if (isset($scan_id) && !empty($scan_id))
{
	$scanid_result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_ally_scans WHERE id = ?",
		[$showid]);
	if ($scanid_result)
	{
		$scanid_data = mysqli_fetch_assoc($scanid_result);
		$owner_allytag = $scanid_data["owner_allytag"];
		$scan_list = $scanid_data["target_memberlist"];
		if ($clankuerzel == $owner_allytag)
		{
			print($scan_list);
		}
	}
}

function createScanList($clankuerzel, $clanname, $datum)
{
         global $allyscan_lang;
         $scanlist = "";
	$scanlist.= "<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	$scanlist.= "<tr align=\"center\">";
	$scanlist.= "<td width=\"13\" height=\"37\" class=\"rol\">&nbsp;</td>";
	$scanlist.= "<td width=\"*\" align=\"center\" class=\"ro\">$allyscan_lang[memberlist] $clankuerzel - $allyscan_lang[stand]: $datum</td>";
	$scanlist.= "<td width=\"13\" class=\"ror\">&nbsp;</td>";
	$scanlist.= "</tr>";
	$scanlist.= "<tr><td width=\"13\" class=\"rl\">&nbsp;</td><td>";
	$scanlist.= "<table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">";
	$scanlist.= "<tr>";
	$scanlist.= "<td width=\"*\" class=\"tc\" colspan=\"8\">$allyscan_lang[nickprofil]</font></td>";

	$scanlist.= "</tr>";

	$scanlist.= "<tr>";
	$scanlist.= "<td class=\"tc\">$allyscan_lang[name]</td>";
	$scanlist.= "<td class=\"tc\">$allyscan_lang[kollies]</td>";
	$scanlist.= "<td class=\"tc\">$allyscan_lang[punkte]</td>";
	$scanlist.= "<td class=\"tc\">$allyscan_lang[koords]</td>";
	$scanlist.= "<td class=\"tc\">$allyscan_lang[rasse]</td>";
	//$scanlist.= "<td class=\"tc\">Aktiv</td>";
	$scanlist.= "</tr>";

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_user_data WHERE status='1' AND allytag=? ORDER BY sector, `system`",
		[$clankuerzel]);

	$nb = mysqli_num_rows($result);

	$row = 0;
	$all_rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

	while ($row < $nb)
	{
        $user_data = $all_rows[$row];
        $userid = $user_data["user_id"];
        $sector = $user_data["sector"];
        $system = $user_data["system"];
        $score = $user_data["score"];
        $kollies = $user_data["col"];
        $m_rasse = $user_data["rasse"];
        $m_actpoints = $user_data["actpoints"];
        $m_tick = $user_data["tick"];

    	$activity=$m_actpoints/$m_tick*1000;

        $r_text = "?";

        if ($m_rasse == "1")
        {
        	$r_text = "E";
        }
        elseif ($m_rasse == "2")
        {
        	$r_text = "I";
        }
        elseif ($m_rasse == "3")
        {
        	$r_text = "K";
        }
        elseif ($m_rasse == "4")
        {

        	$r_text = "Z";
        }

        $sectorjump = explode(":",$sector);
        $sectorjump = $sectorjump[0];

        $tquery = mysqli_execute_query($GLOBALS['dbi'], 
            "SELECT * FROM de_user_data WHERE user_id=?", 
            [$userid]);
        $user_row = mysqli_fetch_assoc($tquery);
        $name = $user_row['spielername'];
        $de_login_result = mysqli_execute_query($GLOBALS['dbi'], 
            "SELECT status FROM de_login WHERE user_id=?", 
            [$userid]);
        $de_login_data = mysqli_fetch_assoc($de_login_result);
        $de_login_status = $de_login_data["status"];
        if ($de_login_status != 1)
        {
        	$name = "<i>(".$name.")</i>";
        }
	    $scanlist.= "<form name=\"f".$sector."x".$system."\" action=\"sector.php?sf=".$sectorjump."\" method=\"POST\">";
	    $scanlist.= "<tr>\n";
	    $scanlist.= "<td class=\"cl\"><a href=\"details.php?SID=$SID&a=s&se=$sector&sy=$system\">$name</a></td>\n";
	    $scanlist.= "<td class=\"cr\">$kollies</td>\n";
	    $scanlist.= "<td class=\"cr\">".number_format($score, 0,'','.')."</td>\n";
	    $scanlist.= "<td class=\"cc\"><a href=\"javascript:document.f".$sector."x".$system.".submit()\">[".$sector.":".$system."]</a></font></a></td>\n";
	    $scanlist.= "<td class=\"cc\">$r_text</td>\n";
	    $scanlist.= "</tr>";
	    $scanlist.= "</form>";
		$row++;
	}

	$scanlist.= "</table>";
	$scanlist.= "</td><td width=\"13\" class=\"rr\">&nbsp;</td></tr>";
	$scanlist.= "<tr><td width=\"13\" class=\"rul\">&nbsp;</td>";
	$scanlist.= "<td width=\"*\" class=\"ru\">&nbsp;</td>";
	$scanlist.= "<td width=\"13\" class=\"rur\">&nbsp;</td>";
	$scanlist.= "</tr>";
	$scanlist.= "</table>";
	return $scanlist;
}

?>
<br>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>