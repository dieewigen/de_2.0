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
        Die Function getLink($user) erzeugt einen Hyperfunk-Link f&uuml;r den Benutzer mit der
        ID $user. Als Linktext wird der Name des Users angezeigt. Per Klick auf den
        Link kann der Benutzer dem User mit der ID $user eine Transfunknachricht schreiben.

        Parameterbeschreibung:

        $user    : Id des Users, f&uuml;r den der HF-Link erzeugt werden soll (Int)

        R&uuml;ckgabewert:

        $lnk                 : Hyperfunklink, Linktext ist der Name des Users, dessen ID an die
                                   Funktion &uuml;bergeben wurde. Existiert die ID nicht, wird der Text
                                   "Nicht belegt" anstelle des Links generiert. Tritt ein Datenbankfehler
                                   auf, wird der String "Datenbankfehler" zur&uuml;ckgegeben.
*/
function getLink($user){
	global $allyallymain_lang, $db;
	$lnk="";
	//Pr&uuml;fen, ob eine g&uuml;ltige UserID &uuml;bergeben wurde
	if ($user > -1){
			//Ermitteln des Benutzerdatensatzes
			$result_userlink = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id=?", [$user]);
			//Pr&uuml;fen, ob ein g&uuml;ltiges Resultset zur&uuml;ckgegeben wurde
			if ($result_userlink){
				if (mysqli_num_rows($result_userlink) == 1){
					//Feldwerte ermitteln
					$row_userlink = mysqli_fetch_assoc($result_userlink);
					$name = $row_userlink['spielername'];
					$sector = $row_userlink['sector'];
					$system = $row_userlink['system'];
					//Erzeugen des Transfunk-Links
					$lnk = "<a href=details.php?se=$sector&sy=$system>$name ($allyallymain_lang[sendhf])</a>";
				}
			}else{
					//Fehlermeldung, wenn das Resultset ung&uuml;ltig ist
					$lnk = "$allyallymain_lang[error]!";
			}
	}else{
			//Wurde eine ung&uuml;ltige BenutzerID &uuml;bergeben, wird ein Leerwert zur&uuml;ckgegeben
			$lnk = "-";
	}
	//R&uuml;ckgeben des erzeugten Links
	return $lnk;
}

function getName($user){

	global $db;
	$name="";
	//Pr&uuml;fen, ob eine g&uuml;ltige UserID &uuml;bergeben wurde
	if ($user >- 1)
	{
			//Ermitteln des Benutzerdatensatzes
			$result_userlink = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername FROM de_user_data WHERE user_id=?", [$user]);
			//Pr&uuml;fen, ob ein g&uuml;ltiges Resultset zur&uuml;ckgegeben wurde
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

// Abfrage auf $iscoleader hinzugef&uuml;gt von Ascendant (01.09.2002)
if (!$ismember and !$isleader and !$iscoleader) die(include("ally/ally.footer.inc.php"));

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
		WHERE id=?",
		[$hpurl, $bio, $openirc, $internirc, $metairc, $keywords, $leadermessage, 
		$bewerberinfo, $showactivity, $discord_bot, $own_ally_id]);

		echo '<div class="info_box mt10 mb10">';

		echo "$allysettings_lang[msg_1]!";

		echo '</div>';
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
$homepageurl 	= $row_result["homepage"];
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
$leadername 	= $row_result["leadername"];
$coleadername1 	= $row_result["coleadername1"];
$coleadername2 	= $row_result["coleadername2"];
$coleadername3 	= $row_result["coleadername3"];
$fcname1 		= $row_result["fcname1"];
$fcname2 		= $row_result["fcname2"];
$toname1 		= $row_result["toname1"];
$toname2 		= $row_result["toname2"];
$moname1 		= $row_result["moname1"];
$moname2 		= $row_result["moname2"];
$openirc	 	= $row_result["openirc"];
$internirc 		= $row_result["internirc"];
$metairc 		= $row_result["metairc"];
$discord_bot	= $row_result["discord_bot"];
$keywords 		= $row_result["keywords"];
$leadermessage 	= formatString($row_result["leadermessage"]);
$bewerberinfo 	= formatString($row_result["bewerberinfo"]);
$publicactivity = $row_result["public_activity"];

// Rohdaten für Textareas (ohne formatString, um Zeilenumbrüche zu erhalten)
$bio_raw = $row_result["besonderheiten"];
$leadermessage_raw = $row_result["leadermessage"];
$bewerberinfo_raw = $row_result["bewerberinfo"];

$mission_counter[1]=	$row_result["mission_counter_1"];
$mission_counter[2]=	$row_result["mission_counter_2"];

if ($publicactivity)
{
	$activity_checked = "checked";
}

if ($leaderid > -1)
{
	$leaderlink 	= getLink($leaderid);
}
if ($coleaderid1 > -1)
{
	$coleader1link 	= getLink($coleaderid1);
}
if ($coleaderid2 > -1)
{
	$coleader2link 	= getLink($coleaderid2);
}
if ($coleaderid3 > -1)
{
	$coleader3link 	= getLink($coleaderid3);
}
if ($fcid1 > -1)
{
	$fclink1 	= getLink($fcid1);
}
if ($fcid2 > -1)
{
	$fclink2 	= getLink($fcid2);
}
if ($toid1 > -1)
{
	$tolink1 	= getLink($toid1);
}
if ($toid2 > -1)
{
	$tolink2 		= getLink($toid2);
}
if ($moid1 > -1)
{
	$molink1 		= getLink($moid1);
}
if ($moid2 > -1)
{
	$molink2 		= getLink($moid2);
}


$membercount_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE allytag=? AND status=1", [$clankuerzel]);
$membercount = mysqli_num_rows($membercount_result);
$bio = formatString($row_result["besonderheiten"]);
$ausrichtung = $row_result["ausrichtung"];
$regierungsform = $row_result["regierungsform"];
$allianzform = $row_result["allianzform"];

echo '<div class="cell" style="width: 600px;">';
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// aktuelle allianzaufgabe anzeigen
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//allydaten laden
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE allytag=?", [$ownally]);
$row = mysqli_fetch_array($db_daten);    
$allyid=$row['id'];
$questpoints=$row['questpoints'];
$ownallyid=$allyid;
echo '<br><div class="info_box" style="color: #FFFFFF;">';

//Allianzaufgaben: überprüfen ob schon eine aufgabe aktiv ist
if($row['questgoal']==0){
	echo '<font color="#00FF00">Euch wurde noch keine Aufgabe gestellt.</font>';
}else{
	echo 'Aktuelle Allianzaufgabe: <br>';
	echo '<font color="#00FF00">'.$allyjobs[$row['questtyp']][0].'<br>';
	echo 'Fortschritt: '.number_format($row['questreach'], 0,"",".").' von '.number_format($row['questgoal'], 0,"",".").'<br>';
	echo 'Verbleibende Zeit (WT): '.number_format($row['questtime'], 0,"",".").'<br>';
	echo 'Belohnung: 100 + '.round($row['questtime']/10).' (Zeitbonus) Allianz-Rundensiegartefakte<br></font>';
}

echo '</div><br>';

//////////////////////////////////////////////////////////////////////////////
// Allianzmissionen: Fortschritt anzeigen
//////////////////////////////////////////////////////////////////////////////
echo '
<div class="info_box" style="color: #FFFFFF;">
	<div style="text-align: center;">Erledigte Missionen</div>
	
	<div style="display: flex; justify-content: space-around; align-items: center; margin-top: 10px;">
		<div style="position: relative; width: 128px; height: 128px; text-align: center; background-image: url(\'gp/g/mission_ares.png\'); background-size: cover; background-position: center;">
			<div style="position:absolute; left:0; bottom:0; width: 100%; padding: 2px; text-align: center; background-color: rgba(0,0,0, 0.5);">ARES: '.$mission_counter[1].'</div>
		</div>
		
		<div style="position: relative; width: 128px; height: 128px; text-align: center; background-image: url(\'gp/g/mission_hephaistos.png\'); background-size: cover; background-position: center;">
			<div style="position:absolute; left:0; bottom:0; width: 100%; padding: 2px; text-align: center; background-color: rgba(0,0,0, 0.5);">HEPHAISTOS: '.$mission_counter[2].'</div>
		</div>

	</div>
</div>
<br>
';

//////////////////////////////////////////////////////////////////////////////
// allianzinformationen anzeigen
//////////////////////////////////////////////////////////////////////////////
//partnerallianz
$allyidpartner=get_allyid_partner($allyid);
$partnerallianz='';
if($allyidpartner>0){
  	$db_daten2=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$allyidpartner]);
	$row2 = mysqli_fetch_array($db_daten2);
    $partnerallianz=$row2['allyname'].' ('.$row2['allytag'].')<br>';
}

//platz nach rundensiegpunkten
$db_datenx=mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(*) AS wert FROM de_allys WHERE questpoints > ? ORDER BY id ASC", [$questpoints]);
$rowx = mysqli_fetch_array($db_datenx);
$platz=$rowx['wert']+1;
$rundensiegartefatke=number_format($questpoints, 0,"",".").' (Platz: '.$platz.')';

  

echo '<table width="100%">';
//print("<tr><td><h2>$allyallymain_lang[msg_4], $_SESSION['ums_spielername']</h2></td></tr>");
//print("<tr><td><hr></td></tr>");

echo '<tr><td>
		<table border="0" width="100%" cellspacing="1" cellpadding="0">
    		<tr>
      			<td height=21 class="cl" colspan="4"><h3>'.$allyallymain_lang['allyoverview'].':</h3></td>
    		</tr>
    		<tr class=cl>
      			<td height=21>Allianzname /-tag:</td>
      			<td height=21 colspan="3"><b>'.htmlspecialchars($clanname, ENT_QUOTES, 'UTF-8').' / '.htmlspecialchars($clankuerzel, ENT_QUOTES, 'UTF-8').'</b></td>
    		</tr>
    		<tr class=cl>
    		</tr>
    		<tr class=cl>
      			<td height=21>Mitglieder:</td>
      			<td height=21><b>'.$membercount.'/'.$memberlimit.'</b></td>
	   		';

if($dailyallygift==1){
	$grafikname='icon15.png';
}else{
 	$grafikname='icon15_grey.png'; 
}

echo '
      	<td height="32">Täglicher Bonus:</td>
      	<td height="32" colspan="3">
    		<a href="ally_dailygift.php"><img src="gp/g/'.$grafikname.'" style="width: 32px; height: 32px;" class="rounded-borders"></a>
 			
  			  	
	  	</td>
	</tr>';

$discord_open_link='';
$discord_intern_link='';
$discord_meta_link='';

if(!empty(trim($openirc))){
	$discord_open_link='<a href="https://discord.gg/'.$openirc.'" target="_blank">zu Discord</a>';
}

if(!empty(trim($internirc))){
	$discord_intern_link='<a href="https://discord.gg/'.$internirc.'" target="_blank">zu Discord</a>';
}

if(!empty(trim($metairc))){
	$discord_meta_link='<a href="https://discord.gg/'.$metairc.'" target="_blank">zu Discord</a>';
}

echo '<tr class="cl">
			<td height="21">'.$allyallymain_lang['regierungsform'].':</td>
			<td height="21"><b>'.$regierungsform.'</b></td>
			<td height="21">'.$allyallymain_lang['ausrichtung'].':</td>
			<td height="21"><b>'.$ausrichtung.'</b></td>

		</tr>
		<tr class="cl">
			<td height="21">'.$allyallymain_lang['irc'].':</td>
			<td height="21">'.$discord_open_link.'</td>
			<td height="21">'.$allyallymain_lang['intirc'].':</td>
			<td height="21">'.$discord_intern_link.'</td>
			
		</tr>
		<tr class=cl>
			<td height="21">Allianz-Rundensiegartefakte:</td>
			<td height="21"><b>'.$rundensiegartefatke.'</b></td>
			<td height="21">'.$allyallymain_lang['metairc'].':</td>
			<td height="21">'.$discord_meta_link.'</td>
		</tr>
		<tr class=cl>
			<td height="21">Partnerallianz:</td>
			<td height="21" colspan="3"><b>'.htmlspecialchars($partnerallianz, ENT_QUOTES, 'UTF-8').'</b></td>
		</tr>
		<tr class=cl>
			<td height="21">Website:</td>
			<td height="21" colspan="3"><b><a href="'.$homepageurl.'" target=_blank>'.$homepageurl.'</a></b></td>
		</tr>
		
		</table>
		<table width="100%">
		<tr>
			<td height="21" colspan="2"><hr></td>
		</tr>
		<tr>
			<td height="21" colspan="2" class="cl"><h3>'.$allyallymain_lang['allianzposten'].':</h3></td>
		</tr>
		<tr class="cl">
			<td height=21>'.$leadername.': </td>
			<td height=21><b>'.$leaderlink.'</b></td>
		</tr>
';
 	 
 	 
if ($coleaderid1 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$coleadername1.': </td>
      			<td height="21"><b>'.$coleader1link.'</b></td>
    		</tr>
	';
}
if ($coleaderid2 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$coleadername2.': </td>
      			<td height="21"><b>'.$coleader2link.'</b></td>
    		</tr>
	';
}
if ($coleaderid3 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$coleadername3.': </td>
      			<td height="21"><b>'.$coleader3link.'</b></td>
    		</tr>
	';
}
if ($fcid1 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$fcname1.': </td>
      			<td height="21"><b>'.$fclink1.'</b></td>
    		</tr>
	';
}
if ($fcid2 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$fcname2.': </td>
      			<td height="21"><b>'.$fclink2.'</b></td>
    		</tr>
	';
}
if ($toid1 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$toname1.': </td>
      			<td height="21"><b>'.$tolink1.'</b></td>
    		</tr>
	';
}

if ($toid2 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$toname2.': </td>
      			<td height="21"><b>'.$tolink2.'</b></td>
    		</tr>
	';
}
if ($moid1 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$moname1.': </td>
      			<td height="21"><b>'.$molink1.'</b></td>
    		</tr>
	';
}
if ($moid2 > -1)
{
	echo '
			<tr class="cl">
      			<td height="21">'.$moname2.': </td>
      			<td height="21"><b>'.$molink2.'</b></td>
    		</tr>
	';
}

echo '
			<tr>
      			<td height="21" colspan="2"><hr></td>
    		</tr>
    		<tr>
      			<td height="21" colspan="2" class="cl"><h3>'.$allyallymain_lang['allianzbiografie'].':</h3></td>
    		</tr>
    		<tr>
      			<td class="cl" height="21" colspan="2">'.nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')).'</td>
    		</tr>
			</table>
';

if ($isleader || $iscoleader)
{
	echo '

		<div onclick="$(this).hide();$(\'#allianzbearbeiten\').show();" class="btn mt10 mb10" style="margin-left: auto; margin-right: auto;">Daten bearbeiten</div>

		<form method="POST" action="allymain.php">
		<table id="allianzbearbeiten" border="0" width="100%" cellspacing="1" cellpadding="0" class="invisible">
			<tr>
      			<td height="21" colspan="2"><hr></td>
    		</tr>
    		<tr>
      			<td height="21" colspan="2" class="cl"><h3>'.$allyallymain_lang['changedaten'].':</h3></td>
    		</tr>
			
			<tr>
				<td colspan="2">'.$allyallymain_lang['homepage'].':<br>
      			<input type="text" name="hpurl" size="50" maxlength="50" value="'.$homepageurl.'"></td>
    		</tr>
    		<tr>
				<td colspan="2">
					'.$allyallymain_lang['pubirc'].':<br>
					https://discord.gg/<input type="text" name="openirc" size="30" maxlength="50" value="'.$openirc.'">
				</td>
    		</tr>
    		<tr>
				<td colspan="2">
					'.$allyallymain_lang['intirc'].':<br>
					https://discord.gg/<input type="text" name="internirc" size="30" maxlength="50" value="'.$internirc.'">
				</td>
    		</tr>
    		<tr>
				<td colspan="2">
					'.$allyallymain_lang['metairc'].':<br>
					https://discord.gg/<input type="text" name="metairc" size="30" maxlength="50" value="'.$metairc.'">
				</td>
			</tr>
    		<tr>
				<td colspan="2">
					Discord-Bot:<br>
					https://discordapp.com/api/webhooks/<input type="text" name="discord_bot" size="50" maxlength="100" value="'.$discord_bot.'">
				</td>
    		</tr>			
    		<tr>
				<td colspan="2">
					'.$allyallymain_lang['keywords'].':<br>
					<input type=text name=keywords size="50" maxlength="255" value="'.$keywords.'">
				</td>
    		</tr>
    		<tr>
    			<td colspan="2">'.$allyallymain_lang['allianzbiografie'].':<br>
      			<textarea rows="22" name="bio" cols="71">'.htmlspecialchars($bio_raw, ENT_QUOTES, 'UTF-8').'</textarea></td>
    		</tr>
    		<tr>
    			<td colspan="2">'.$allyallymain_lang['msgtoleader'].':<br>
      			<textarea rows="10" name="leadermessage" cols="71">'.htmlspecialchars($leadermessage_raw, ENT_QUOTES, 'UTF-8').'</textarea></td>
    		</tr>
    		<tr>
    			<td colspan="2">'.$allyallymain_lang['bewerberinfo'].':<br>
      			<textarea rows="10" name="bewerberinfo" cols="71">'.htmlspecialchars($bewerberinfo_raw, ENT_QUOTES, 'UTF-8').'</textarea></td>
    		</tr>
    		<tr><td><br></td></tr>
    		<tr>
    			<td colspan="2">
					<input type="submit" value="speichern" name="B1">
					<br><br>
				</td>
    		</tr>
			</form>
	';
}

echo '</table></td></tr></table></div><br><br></div>';

?>
<br>
<?php include('ally/ally.footer.inc.php'); ?>

</body>
</html>