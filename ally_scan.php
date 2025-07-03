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
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, spielername FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$spielername=$row["spielername"]
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$allyscan_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<?
include "resline.php";

include ("ally/ally.menu.inc.php");

$scancost = 200;
$full_access = false;
$query = "SELECT allytag FROM de_user_data where user_id='$ums_user_id'";
$result = mysql_query($query);
$clankuerzel = mysql_result($result,0,"allytag");

$ally_result = mysql_query("SELECT * FROM de_allys WHERE allytag='$clankuerzel'");
if ($ally_result)
{
	$ally_data = mysql_fetch_array($ally_result);
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

if (has_position("leaderid", $clankuerzel, $ums_user_id) || has_position("coleaderid1", $clankuerzel, $ums_user_id) || has_position("coleaderid2", $clankuerzel, $ums_user_id) || has_position("coleaderid3", $clankuerzel, $ums_user_id) || has_position("tacticalofficer1", $clankuerzel, $ums_user_id) || has_position("leaderid", $clankuerzel, $ums_user_id))
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
		$scan_ally_result = mysql_query("SELECT * FROM de_allys WHERE id='$ally_id'");
		if ($scan_ally_result)
		{
			$scan_ally_data = mysql_fetch_array($scan_ally_result);
			$scan_ally_tag = $scan_ally_data["allytag"];
			$scan_ally_name = html_entity_decode($scan_ally_data["allyname"]);

		}
		$timestamp = time();
		$datum = date("d.m.Y - G:i", $timestamp);
		$scanlist = createScanList($scan_ally_tag, $scan_ally_name, $datum);
		mysql_query("UPDATE de_allys SET t_depot = t_depot - $scancost WHERE id = $clanid");
		mysql_query("INSERT INTO de_ally_scans SET owner_allytag='$clankuerzel', target_allytag='$scan_ally_tag', target_allyname='$scan_ally_name', target_memberlist='$scanlist', scandate='$datum', timestamp='$timestamp'");
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
		$list_result = mysql_query("SELECT id, allytag, allyname from de_allys order by allyname asc");
		if ($list_result)
		{
			$numrows = mysql_num_rows($list_result);
			print("<select name=ally_id><option value=\"\" selected>$allyscan_lang[plschoose]...</option>");
			for ($i=0;$i<$numrows;$i++)
			{
				$list_data = mysql_fetch_array($list_result);
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
$ml_result = mysql_query("SELECT * FROM de_ally_scans WHERE owner_allytag = '$clankuerzel' ORDER BY timestamp DESC");
if ($ml_result)
{
	$numrows_ml = mysql_num_rows($ml_result);
	for ($j=0;$j<$numrows_ml;$j++)
	{
		$ml_data = mysql_fetch_array($ml_result);
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
	$scanid_result = mysql_query("SELECT * FROM de_ally_scans WHERE id = '$showid'");
	if ($scanid_result)
	{
		$scanid_data = mysql_fetch_array($scanid_result);
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

	$query = "SELECT *  FROM de_user_data WHERE status='1' AND allytag='$clankuerzel' ORDER BY sector, `system`";

	$result = mysql_query($query);

	$nb = mysql_num_rows($result);

	$row = 0;

	while ($row < $nb)
	{
        $userid = mysql_result($result,$row,"user_id");
        $sector = mysql_result($result,$row,"sector");
        $system = mysql_result($result,$row,"system");
        $score = mysql_result($result,$row,"score");
        $kollies = mysql_result($result,$row,"col");
        $m_rasse = mysql_result($result,$row,"rasse");
        $m_actpoints = mysql_result($result,$row,"actpoints");
        $m_tick = mysql_result($result,$row,"tick");

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

        $tquery= mysql_fetch_array(mysql_query("SELECT * FROM de_user_data where user_id='$userid'"));
        $name = $tquery[spielername];
        $de_login_result = mysql_query("select status from de_login WHERE user_id='$userid'");
        $de_login_data = mysql_fetch_array($de_login_result);
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
	    //$scanlist.= "<td class=\"cr\">".number_format($activity, 2,",",".")."</td>\n";
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
<?php include "fooban.php"; ?>
</body>
</html>