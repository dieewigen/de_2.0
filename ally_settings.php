<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.settings.lang.php';
include 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allysettings_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<?php
function formatString($string){
	$allowed_tags="<br><i></i><b></b><strong></strong><u></u><ul></ul><li></li><p></p><font></font>";
	$result = strip_tags($string, $allowed_tags);
	return $result;
}

include "resline.php";
include ("ally/ally.menu.inc.php");

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys 
     WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
    [$ums_user_id, $ums_user_id, $ums_user_id, $ums_user_id]);

$row = mysqli_fetch_assoc($result);
$clanid = $row["id"];

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

mysqli_execute_query($GLOBALS['dbi'],
    "UPDATE de_allys 
     SET homepage=?, besonderheiten=?, openirc=?, internirc=?, metairc=?, 
         keywords=?, leadermessage=?, bewerberinfo=?, public_activity=?, discord_bot=? 
     WHERE id=?",
    [$hpurl, $bio, $openirc, $internirc, $metairc, $keywords, $leadermessage, 
     $bewerberinfo, $showactivity, $discord_bot, $clanid]);

echo '<br><div class="info_box">';

echo "$allysettings_lang[msg_1]!";
//print($showactivity);
print("<br><br><a href=\"allymain.php\">$allysettings_lang[msg_2]</a>");

echo '</div>';
?>
<?php include("ally/ally.footer.inc.php") ?>