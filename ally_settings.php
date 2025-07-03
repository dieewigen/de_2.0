<?php
//	--------------------------------- ally_settings.php ---------------------------------
//	Funktion der Seite:		�ndern der Allianzdaten
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
//
//	05.02.2002 (Ascendant)	- Erweiterung der �nderungsbefugnis der Allianzdaten
//							  auf Coleader
//  --------------------------------------------------------------------------------
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.settings.lang.php';
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allysettings_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?php
function formatString($string){
	$allowed_tags="<br><i></i><b></b><strong></strong><u></u><ul></ul><li></li><p></p><font></font>";
	$result = strip_tags($string, $allowed_tags);
	return $result;
}

include "resline.php";
include ("ally/ally.menu.inc.php");
// Erweiterung des Querys um Abfrage auf Coleader von Ascendant (05.09.2002)
$query="SELECT * FROM de_allys WHERE leaderid='$ums_user_id' OR coleaderid1='$ums_user_id' OR coleaderid2='$ums_user_id' OR coleaderid3='$ums_user_id'";

$result=mysql_query($query);

$clanid = mysql_result($result,0,"id");

$bio = formatString($bio);
$leadermessage = formatString($leadermessage);
$bewerberinfo = formatString($bewerberinfo);

$hpurl=$_POST['hpurl'];
$hpurl=utf8_decode(str_replace("'", '&#39;', $hpurl));

$bio=$_POST['bio'];
$bio=utf8_decode(str_replace("'", '&#39;', $bio));

$openirc=$_POST['openirc'];
$openirc=utf8_decode(str_replace("'", '&#39;', $openirc));

$internirc=$_POST['internirc'];
$internirc=utf8_decode(str_replace("'", '&#39;', $internirc));

$metairc=$_POST['metairc'];
$metairc=utf8_decode(str_replace("'", '&#39;', $metairc));

$keywords=$_POST['keywords'];
$keywords=utf8_decode(str_replace("'", '&#39;', $keywords));

$leadermessage=$_POST['leadermessage'];
$leadermessage=utf8_decode(str_replace("'", '&#39;', $leadermessage));

$bewerberinfo=$_POST['bewerberinfo'];
$bewerberinfo=utf8_decode(str_replace("'", '&#39;', $bewerberinfo));

$showactivity=intval($_POST['showactivity']);

$discord_bot=trim($_POST['discord_bot']);
$discord_bot=utf8_decode(str_replace("'", '&#39;', $discord_bot));

$query = "UPDATE de_allys SET homepage='$hpurl', besonderheiten='$bio', openirc='$openirc', internirc='$internirc', metairc='$metairc', keywords='$keywords', leadermessage='$leadermessage', bewerberinfo='$bewerberinfo', public_activity='$showactivity', discord_bot='$discord_bot' WHERE id='$clanid'";

$result = mysql_query($query);

echo '<br><div class="info_box">';

echo "$allysettings_lang[msg_1]!";
//print($showactivity);
print("<br><br><a href=\"allymain.php\">$allysettings_lang[msg_2]</a>");

echo '</div>';
?>
<?php include("ally/ally.footer.inc.php") ?>