<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.allymain.lang.php');
include 'inc/lang/'.$sv_server_lang.'_ally.settings.lang.php';
include('lib/basefunctions.lib.php');
include('inc/allyjobs.inc.php');
include_once('functions.php');

$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, status, ally_id, dailyallygift FROM de_user_data WHERE user_id=?", [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$dailyallygift=$row['dailyallygift'];

if ($row['status']==1) {
	$ownally = $row['allytag'];
	$own_ally_id = $row['ally_id'];
}else{
	$ownally='';
	$own_ally_id = -1;
}


?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $allyallymain_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
/*
	Die Function getLink($user) erzeugt einen Link auf die Spielerdetails (dort kann man dem User
	mit der ID $user eine Hyperfunknachricht schreiben). Als Linktext wird der Name des Users angezeigt.
	Existiert die ID nicht, wird ein Leerwert zurückgegeben, bei einem Datenbankfehler ein Hinweis.
*/
function getLink($user){
	global $allyallymain_lang;
	$lnk="";
	//Prüfen, ob eine gültige UserID übergeben wurde
	if ($user > -1){
			//Ermitteln des Benutzerdatensatzes
			$result_userlink = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id=?", [$user]);
			//Prüfen, ob ein gültiges Resultset zurückgegeben wurde
			if ($result_userlink){
				if (mysqli_num_rows($result_userlink) == 1){
					//Feldwerte ermitteln
					$row_userlink = mysqli_fetch_assoc($result_userlink);
					$name = $row_userlink['spielername'];
					$sector = $row_userlink['sector'];
					$system = $row_userlink['system'];
					//Link auf die Spielerdetails, dort kann man Hyperfunk schreiben
					$lnk = '<a href="details.php?se='.$sector.'&sy='.$system.'" class="ally-person">'.$name.' <small>'.$allyallymain_lang['sendhf'].'</small></a>';
				}
			}else{
					//Fehlermeldung, wenn das Resultset ungültig ist
					$lnk = "$allyallymain_lang[error]!";
			}
	}else{
			//Wurde eine ungültige BenutzerID übergeben, wird ein Leerwert zurückgegeben
			$lnk = "-";
	}
	//Rückgeben des erzeugten Links
	return $lnk;
}

function getName($user){

	$name="";
	//Prüfen, ob eine gültige UserID übergeben wurde
	if ($user >- 1)
	{
			//Ermitteln des Benutzerdatensatzes
			$result_userlink = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername FROM de_user_data WHERE user_id=?", [$user]);
			//Prüfen, ob ein gültiges Resultset zurückgegeben wurde
			if ($result_userlink)
			{
					//Feldwerte ermitteln
					$row_userlink = mysqli_fetch_assoc($result_userlink);
					$name = $row_userlink['spielername'];
			}
	}
	return $name;
}

function formatString($string){
	$allowed_tags="<br><i></i><b></b><strong></strong><u></u><ul></ul><li></li><p></p><font></font>";
	$result = strip_tags($string, $allowed_tags);
	return $result;
}

include('resline.php');
include('ally/ally.menu.inc.php');

//ohne Allianz: erklären, wie man zu einer kommt (eine offene Bewerbung zeigt schon das Menü)
if (!$ismember and !$isleader and !$iscoleader){
	if(empty($GLOBALS['ally_bewerbung_offen'])){
		rahmen_oben($allyallymain_lang['title']);
		echo '
		<div class="ally mod">
			<div class="mod-leer">
				<b>Du geh&ouml;rst keiner Allianz an.</b><br><br>
				Als Mitglied holst Du jeden Tag den Allianzbonus ab, l&ouml;st gemeinsam Allianzaufgaben und baut Allianzprojekte.
				Gr&uuml;nde eine eigene Allianz oder bewirb Dich bei einer bestehenden.
				<div class="ally-aktionen ally-aktionen-mitte">
					<a href="ally_register.php" class="mod-btn">Allianz gr&uuml;nden</a>
					<a href="toplist.php?&s=3" class="mod-btn mod-btn-leise">Allianz suchen</a>
				</div>
			</div>
		</div>';
		rahmen_unten();
	}
	die(include("ally/ally.footer.inc.php"));
}

$meldung='';
if(($isleader || $iscoleader) && $own_ally_id >0){
	if(isset($_POST['B1'])){ //Settings speichern
		$hpurl=$_POST['hpurl'] ?? '';
		$bio=$_POST['bio'] ?? '';
		$openirc=$_POST['openirc'] ?? '';
		$internirc=$_POST['internirc'] ?? '';
		$metairc=$_POST['metairc'] ?? '';
		$keywords=$_POST['keywords'] ?? '';
		$leadermessage=$_POST['leadermessage'] ?? '';
		$bewerberinfo=$_POST['bewerberinfo'] ?? '';
		$showactivity=intval($_POST['showactivity'] ?? 0);
		$discord_bot=trim($_POST['discord_bot'] ?? '');

		mysqli_execute_query($GLOBALS['dbi'],
		"UPDATE de_allys
		SET homepage=?, besonderheiten=?, openirc=?, internirc=?, metairc=?,
			keywords=?, leadermessage=?, bewerberinfo=?, public_activity=?, discord_bot=?
		WHERE id=? AND (leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?)",
		[$hpurl, $bio, $openirc, $internirc, $metairc, $keywords, $leadermessage,
		$bewerberinfo, $showactivity, $discord_bot, $own_ally_id,
		//nur in der eigenen Allianz und nur als deren Leader/Co-Leader
		$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);

		$meldung='<div class="mod-meldung mod-meldung-ok">'.$allysettings_lang['msg_1'].'!</div>';
	}

	//Allianzdaten laden
	$query = "SELECT * FROM de_allys where leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?";
    $result = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);

}else{
        $query = "SELECT * FROM de_allys ally, de_user_data user where user.allytag=ally.allytag and user.user_id=?";
        $result = mysqli_execute_query($GLOBALS['dbi'], $query, [$_SESSION['ums_user_id']]);
}
$row_result = mysqli_fetch_assoc($result);
$clanid 		= $row_result["id"];
$clanname 		= html_entity_decode($row_result["allyname"]);
$t_depot		= $row_result["t_depot"];
$memberlimit 	= $row_result["memberlimit"];


$clankuerzel 	= $row_result["allytag"];
//Spielertexte: escaped für Formularfelder, geprüft für Links
$homepageurl 	= html_text($row_result["homepage"]);
$homepagelink 	= safe_http_url($row_result["homepage"]);
$leaderid 		= $row_result["leaderid"];
$coleaderid1 	= $row_result["coleaderid1"];
$coleaderid2 	= $row_result["coleaderid2"];
$coleaderid3 	= $row_result["coleaderid3"];
$fcid1 			= $row_result["fleetcommander1"];
$fcid2 			= $row_result["fleetcommander2"];
$toid1 			= $row_result["tacticalofficer1"];
$toid2 			= $row_result["tacticalofficer2"];
$moid1 			= $row_result["memberofficer1"];
$moid2 			= $row_result["memberofficer2"];
$leadername 	= html_text($row_result["leadername"]);
$coleadername1 	= html_text($row_result["coleadername1"]);
$coleadername2 	= html_text($row_result["coleadername2"]);
$coleadername3 	= html_text($row_result["coleadername3"]);
$fcname1 		= html_text($row_result["fcname1"]);
$fcname2 		= html_text($row_result["fcname2"]);
$toname1 		= html_text($row_result["toname1"]);
$toname2 		= html_text($row_result["toname2"]);
$moname1 		= html_text($row_result["moname1"]);
$moname2 		= html_text($row_result["moname2"]);
$openirc	 	= html_text($row_result["openirc"]);
$internirc 		= html_text($row_result["internirc"]);
$metairc 		= html_text($row_result["metairc"]);
$discord_bot	= html_text($row_result["discord_bot"]);
$keywords 		= html_text($row_result["keywords"]);
$leadermessage 	= formatString($row_result["leadermessage"]);
$bewerberinfo 	= formatString($row_result["bewerberinfo"]);
$publicactivity = $row_result["public_activity"];

// Rohdaten für Textareas (ohne formatString, um Zeilenumbrüche zu erhalten)
$bio_raw = $row_result["besonderheiten"];
$leadermessage_raw = $row_result["leadermessage"];
$bewerberinfo_raw = $row_result["bewerberinfo"];

$mission_counter[1]=	$row_result["mission_counter_1"];
$mission_counter[2]=	$row_result["mission_counter_2"];

$membercount_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE allytag=? AND status=1", [$clankuerzel]);
$membercount = mysqli_num_rows($membercount_result);
$bio = formatString($row_result["besonderheiten"]);
$ausrichtung = html_text($row_result["ausrichtung"]);
$regierungsform = html_text($row_result["regierungsform"]);
$allianzform = $row_result["allianzform"];

//allydaten laden
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE allytag=?", [$ownally]);
$row = mysqli_fetch_array($db_daten);
$allyid=$row['id'];
$questpoints=$row['questpoints'];
$ownallyid=$allyid;

//partnerallianz
$allyidpartner=get_allyid_partner($allyid);
$partnerallianz='';
if($allyidpartner>0){
  	$db_daten2=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyidpartner]);
	$row2 = mysqli_fetch_array($db_daten2);
    $partnerallianz=$row2['allyname'].' ('.$row2['allytag'].')';
}

//platz nach rundensiegpunkten
$db_datenx=mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(*) AS wert FROM de_allys WHERE questpoints > ? ORDER BY id ASC", [$questpoints]);
$rowx = mysqli_fetch_array($db_datenx);
$platz=$rowx['wert']+1;

//////////////////////////////////////////////////////////////////////////////
// Allianzübersicht: Kopf, Kennzahlen, täglicher Bonus, Allianzaufgabe, Links
//////////////////////////////////////////////////////////////////////////////
rahmen_oben($allyallymain_lang['allyoverview']);
echo '<div class="ally mod">'.$meldung;

$chips='<span class="mod-chip">'.$allyallymain_lang['regierungsform'].' <b>'.$regierungsform.'</b></span>';
$chips.='<span class="mod-chip">'.$allyallymain_lang['ausrichtung'].' <b>'.$ausrichtung.'</b></span>';
if($partnerallianz!=''){
	$chips.='<span class="mod-chip mod-chip-gruen">Partnerallianz <b>'.htmlspecialchars($partnerallianz, ENT_QUOTES, 'UTF-8').'</b></span>';
}
echo '
	<div class="ally-kopf">
		<div class="ally-tag">'.htmlspecialchars($clankuerzel, ENT_QUOTES, 'UTF-8').'</div>
		<div class="ally-kopf-text">
			<div class="ally-name">'.htmlspecialchars($clanname, ENT_QUOTES, 'UTF-8').'</div>
			<div class="ally-chips">'.$chips.'</div>
		</div>
	</div>';

//Kennzahlen und erledigte Missionen
echo '
	<div class="ally-kacheln">
		<div class="ov-wert"><span class="mod-typ">'.$allyallymain_lang['mitglieder'].'</span><b>'.$membercount.' / '.$memberlimit.'</b></div>
		<div class="ov-wert"><span class="mod-typ">Rundensiegartefakte</span><b>'.number_format($questpoints, 0,"",".").'</b><small>Platz '.$platz.'</small></div>
		<div class="ov-wert ally-mission"><img src="gp/g/mission_ares.png" alt=""><div><span class="mod-typ">ARES</span><b>'.$mission_counter[1].'</b><small>erledigt</small></div></div>
		<div class="ov-wert ally-mission"><img src="gp/g/mission_hephaistos.png" alt=""><div><span class="mod-typ">HEPHAISTOS</span><b>'.$mission_counter[2].'</b><small>erledigt</small></div></div>
	</div>';

//täglicher Bonus
if($dailyallygift==1){
	echo '
	<a href="ally_dailygift.php" class="ally-bonus ally-bonus-bereit">
		<img src="gp/g/icon15.png" alt="">
		<span class="ally-bonus-text"><b>T&auml;glicher Bonus</b><small>Bereit zum Abholen</small></span>
		<span class="mod-btn">Abholen</span>
	</a>';
}else{
	echo '
	<a href="ally_dailygift.php" class="ally-bonus">
		<img src="gp/g/icon15_grey.png" alt="">
		<span class="ally-bonus-text"><b>T&auml;glicher Bonus</b><small>Bereits abgeholt</small></span>
		<span class="mod-btn mod-btn-leise">Ansehen</span>
	</a>';
}

//Allianzaufgabe
echo '<div class="ally-abschnitt"><div class="mod-typ">Allianzaufgabe</div>';
if($row['questgoal']==0){
	echo '<div class="ally-hinweis">Euch wurde noch keine Aufgabe gestellt.</div>';
}else{
	$fortschritt=min(100, max(0, $row['questreach']/$row['questgoal']*100));
	echo '
	<div class="ally-aufgabe">
		<div class="ally-aufgabe-text">'.$allyjobs[$row['questtyp']][0].'</div>
		<div class="mod-balken"><span style="width: '.round($fortschritt, 1).'%;"></span></div>
		<div class="ally-chips">
			<span class="mod-chip">Fortschritt <b>'.number_format($row['questreach'], 0,"",".").' / '.number_format($row['questgoal'], 0,"",".").'</b></span>
			<span class="mod-chip">Verbleibende Zeit <b>'.number_format($row['questtime'], 0,"",".").' WT</b></span>
			<span class="mod-chip mod-chip-gruen">Belohnung <b>100 + '.round($row['questtime']/10).'</b> (Zeitbonus) Allianz-Rundensiegartefakte</span>
		</div>
	</div>';
}
echo '</div>';

//Discord und Website; nur gültige Einladungscodes verlinken
$links='';
if(discord_invite_code($row_result["openirc"])!==''){
	$links.='<a href="https://discord.gg/'.discord_invite_code($row_result["openirc"]).'" target="_blank" class="mod-btn mod-btn-leise">Discord (&ouml;ffentlich)</a>';
}
if(discord_invite_code($row_result["internirc"])!==''){
	$links.='<a href="https://discord.gg/'.discord_invite_code($row_result["internirc"]).'" target="_blank" class="mod-btn mod-btn-leise">Discord (intern)</a>';
}
if(discord_invite_code($row_result["metairc"])!==''){
	$links.='<a href="https://discord.gg/'.discord_invite_code($row_result["metairc"]).'" target="_blank" class="mod-btn mod-btn-leise">Discord (Meta)</a>';
}
if($homepagelink!==''){
	$links.='<a href="'.$homepagelink.'" target="_blank" class="mod-btn mod-btn-leise" title="'.$homepageurl.'">Website</a>';
}
if($links!=''){
	echo '<div class="ally-abschnitt"><div class="mod-typ">Kontakt</div><div class="ally-links">'.$links.'</div></div>';
}

echo '</div>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
// Allianzposten; ohne eigene Bezeichnung steht die Funktion da
//////////////////////////////////////////////////////////////////////////////
$posten=array(
	array($leaderid, $leadername, 'Leader'),
	array($coleaderid1, $coleadername1, 'Co-Leader'),
	array($coleaderid2, $coleadername2, 'Co-Leader'),
	array($coleaderid3, $coleadername3, 'Co-Leader'),
	array($fcid1, $fcname1, 'Fleetcommander'),
	array($fcid2, $fcname2, 'Fleetcommander'),
	array($toid1, $toname1, 'Tactical Officer'),
	array($toid2, $toname2, 'Tactical Officer'),
	array($moid1, $moname1, 'Member Officer'),
	array($moid2, $moname2, 'Member Officer'),
);
rahmen_oben($allyallymain_lang['allianzposten']);
echo '<div class="ally mod"><div class="ally-posten">';
foreach($posten as $p){
	if($p[0] > -1){
		echo '<span class="ally-posten-name">'.($p[1]!='' ? $p[1] : $p[2]).'</span><span>'.getLink($p[0]).'</span>';
	}
}
echo '</div></div>';
rahmen_unten();

//////////////////////////////////////////////////////////////////////////////
// Allianzbiografie, für Leader/Co-Leader mit dem Formular zum Bearbeiten
//////////////////////////////////////////////////////////////////////////////
rahmen_oben($allyallymain_lang['allianzbiografie']);
echo '<div class="ally mod">';
if(trim($bio)==''){
	echo '<div class="ally-hinweis">Es wurde noch keine Biografie hinterlegt.</div>';
}else{
	echo '<div class="ally-text">'.nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')).'</div>';
}
if ($isleader || $iscoleader){
	echo '
	<div class="ally-aktionen">
		<button type="button" class="mod-btn mod-btn-leise" onclick="this.hidden=true; document.getElementById(\'allianzbearbeiten\').hidden=false;">Daten bearbeiten</button>
	</div>';
}
echo '</div>';
rahmen_unten();

if ($isleader || $iscoleader)
{
	echo '<div id="allianzbearbeiten" hidden>';
	rahmen_oben($allyallymain_lang['changedaten']);
	echo '
	<form method="POST" action="allymain.php" class="ally mod">
		<div class="ally-formular">
			<label class="ally-feld">
				<span class="mod-typ">'.$allyallymain_lang['homepage'].'</span>
				<input type="text" name="hpurl" maxlength="50" value="'.$homepageurl.'" class="mod-eingabe">
			</label>
			<label class="ally-feld">
				<span class="mod-typ">'.$allyallymain_lang['keywords'].'</span>
				<input type="text" name="keywords" maxlength="255" value="'.$keywords.'" class="mod-eingabe">
			</label>
			<label class="ally-feld">
				<span class="mod-typ">'.$allyallymain_lang['pubirc'].'</span>
				<span class="ally-praefix"><span>discord.gg/</span><input type="text" name="openirc" maxlength="50" value="'.$openirc.'" class="mod-eingabe"></span>
			</label>
			<label class="ally-feld">
				<span class="mod-typ">'.$allyallymain_lang['intirc'].'</span>
				<span class="ally-praefix"><span>discord.gg/</span><input type="text" name="internirc" maxlength="50" value="'.$internirc.'" class="mod-eingabe"></span>
			</label>
			<label class="ally-feld">
				<span class="mod-typ">'.$allyallymain_lang['metairc'].'</span>
				<span class="ally-praefix"><span>discord.gg/</span><input type="text" name="metairc" maxlength="50" value="'.$metairc.'" class="mod-eingabe"></span>
			</label>
			<label class="ally-feld">
				<span class="mod-typ">Discord-Bot (Webhook)</span>
				<span class="ally-praefix"><span>&hellip;/api/webhooks/</span><input type="text" name="discord_bot" maxlength="100" value="'.$discord_bot.'" class="mod-eingabe" title="https://discordapp.com/api/webhooks/"></span>
			</label>
			<label class="ally-feld ally-feld-breit">
				<span class="mod-typ">'.$allyallymain_lang['allianzbiografie'].'</span>
				<textarea rows="10" name="bio" class="mod-eingabe">'.htmlspecialchars($bio_raw, ENT_QUOTES, 'UTF-8').'</textarea>
			</label>
			<label class="ally-feld ally-feld-breit">
				<span class="mod-typ">'.$allyallymain_lang['msgtoleader'].'</span>
				<textarea rows="5" name="leadermessage" class="mod-eingabe">'.htmlspecialchars($leadermessage_raw, ENT_QUOTES, 'UTF-8').'</textarea>
			</label>
			<label class="ally-feld ally-feld-breit">
				<span class="mod-typ">'.$allyallymain_lang['bewerberinfo'].'</span>
				<textarea rows="5" name="bewerberinfo" class="mod-eingabe">'.htmlspecialchars($bewerberinfo_raw, ENT_QUOTES, 'UTF-8').'</textarea>
			</label>
		</div>
		<div class="ally-aktionen">
			<input type="submit" value="Speichern" name="B1" class="mod-btn">
		</div>
	</form>';
	rahmen_unten();
	echo '</div>';
}

?>
<?php include('ally/ally.footer.inc.php'); ?>

</body>
</html>
