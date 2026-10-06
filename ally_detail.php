<?php
//        --------------------------------- ally_detail.php ---------------------------------
//        Funktion der Seite:                Öffentliche Informationen zu einer Allianz
//                                           (aus der Rangliste und der Sektoransicht)
//  --------------------------------------------------------------------------------
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.detail.lang.php';
include_once 'functions.php';


$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, status
     FROM de_user_data WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($result);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];$status=$row["status"];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allydetail_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

function formatString($string)
{
	$allowed_tags="<br><i></i><b></b><strong></strong><u></u><ul></ul><li></li><p></p><font></font>";
	$result = strip_tags($string, $allowed_tags);
	return $result;
}

include "resline.php";
include ("ally/ally.menu.inc.php");

$allytag=$_REQUEST['allytag'] ?? '';
$allyid=$_REQUEST['allyid'] ?? '';

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE id=? OR allytag=? LIMIT 0,1",
    [$allyid, $allytag]);
$num = mysqli_num_rows($result);
if($num==1)
{
	$row = mysqli_fetch_assoc($result);

	$clanid 		= $row["id"];
	//alle Texte stammen von Spielern und werden für die Ausgabe escaped bzw. gesäubert
	$clanname 		= html_text($row["allyname"]);
	$clankuerzel 	= html_text($row["allytag"]);
	$leaderid		= $row["leaderid"];
	$homepageurl 	= safe_http_url($row["homepage"]);
	$memberlimit 	= $row["memberlimit"];
	$openirc	 	= discord_invite_code($row["openirc"]);
	$bewerberinfo 	= safe_basic_html($row["bewerberinfo"]);

	$result2 = mysqli_execute_query($GLOBALS['dbi'],
	    "SELECT COUNT(*) as count FROM de_user_data WHERE allytag=? AND status=1",
	    [$clankuerzel]);
	$count_row = mysqli_fetch_assoc($result2);
	$membercount = $count_row['count'];

	$bio = formatString($row["besonderheiten"]);
	$ausrichtung = html_text($row["ausrichtung"]);
	$regierungsform = html_text($row["regierungsform"]);
	$allianzform = $row["allianzform"];

	rahmen_oben('Allianzinformationen');
	echo '<div class="ally mod">';

	//allyleader inkl. hf-möglichkeit
	$leader='';
	$result = mysqli_execute_query($GLOBALS['dbi'],
	    "SELECT spielername, sector, system FROM de_user_data WHERE user_id=?",
	    [$leaderid]);
	$num = mysqli_num_rows($result);
	if($num==1)
	{
		$row = mysqli_fetch_array($result);
		$leader='<span class="mod-chip">Allianzleader <a href="details.php?se='.$row['sector'].'&sy='.$row['system'].'" class="ally-person">'.$row['spielername'].'</a></span>';
	}

	echo '
	<div class="ally-kopf">
		<div class="ally-tag">'.$clankuerzel.'</div>
		<div class="ally-kopf-text">
			<div class="ally-name">'.$clanname.'</div>
			<div class="ally-chips">
				'.$leader.'
				<span class="mod-chip">'.$allydetail_lang['regierungsform'].' <b>'.$regierungsform.'</b></span>
				<span class="mod-chip">'.$allydetail_lang['politischeausrichtung'].' <b>'.$ausrichtung.'</b></span>
			</div>
		</div>
	</div>
	<div class="ally-kacheln ally-kacheln-2">
		<div class="ov-wert"><span class="mod-typ">'.$allydetail_lang['mitglieder'].'</span><b>'.$membercount.' / '.$memberlimit.'</b></div>
		<div class="ov-wert"><span class="mod-typ">Kontakt</span><span class="ally-links">'.
			(!empty(trim($openirc)) ? '<a href="https://discord.gg/'.$openirc.'" target="_blank" class="mod-btn mod-btn-leise">Discord</a>' : '').
			($homepageurl != '' ? '<a href="'.$homepageurl.'" target="_blank" class="mod-btn mod-btn-leise" title="'.$homepageurl.'">'.$allydetail_lang['homepage'].'</a>' : '').
			((empty(trim($openirc)) && $homepageurl == '') ? '<small>keine Angaben</small>' : '').'</span></div>
	</div>';

	echo '
	<div class="ally-abschnitt">
		<div class="mod-typ">'.$allydetail_lang['allianzbiografie'].'</div>
		'.(trim($bio) != '' ? '<div class="ally-text">'.nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')).'</div>' : '<div class="ally-hinweis">Keine Angaben.</div>').'
	</div>
	<div class="ally-abschnitt">
		<div class="mod-typ">'.$allydetail_lang['bewerberinfo'].'</div>
		'.(trim($bewerberinfo) != '' ? '<div class="ally-text">'.$bewerberinfo.'</div>' : '<div class="ally-hinweis">Keine Angaben.</div>').'
	</div>';

	//bewerben, falls möglich
	$join_link = '';
	if ($status != 1 && $memberlimit>$membercount){
		$join_link='<a href="ally_join.php?ally_id='.$clanid.'" class="mod-btn">'.rtrim($allydetail_lang['msg_1'], '.').'</a>';
	}
	elseif ($status == 1)
	{
		$join_link = '<span class="mod-feld">'.$allydetail_lang['msg_2'].'</span>';
	}
	elseif ($memberlimit<=$membercount)
	{
		$join_link = '<span class="mod-feld mod-feld-grund">'.$allydetail_lang['msg_3'].'</span>';
	}
	echo '
	<div class="ally-aktionen">
		<a href="javascript:history.back()" class="mod-btn mod-btn-leise">'.$allydetail_lang['msg_4'].'</a>
		'.$join_link.'
	</div>';

	echo '</div>';
	rahmen_unten();
}
else echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Diese Allianz konnte nicht gefunden werden.</div></div>';
?>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>
