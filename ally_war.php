<?php
$bestehende_kriege[] = '';
$an = '';
$selected = '';

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.war.lang.php');
include('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag
     FROM de_user_data
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$allytag=$row['allytag'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allywar_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
include('resline.php');
include('ally/ally.menu.inc.php');

//Meldungen unter den Reitern; Abbrüche (die) schließen den Kasten selbst
function krieg_meldung($text, $art)
{
	return '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-'.$art.'">'.$text.'</div></div>';
}
function krieg_abbruch($text)
{
	return '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$text.'</div><div class="ally-aktionen"><a href="ally_war.php" class="mod-btn mod-btn-leise">Zur&uuml;ck</a></div></div>';
}

if($isleader || $iscoleader)
{
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys
		 WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
		[$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);
	$allyid = $row["id"];
}
else
{
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys ally, de_user_data user
		 WHERE ally.allytag=user.allytag AND user_id=?",
		[$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);
	$allyid = $row["id"];
}
$peaceto=$_GET['peaceto'] ?? null;
if(isset($peaceto) && ($isleader || $iscoleader))
{
	$result = mysqli_execute_query($GLOBALS['dbi'],"SELECT id FROM de_allys WHERE allytag=?",[$peaceto]);
	$row = mysqli_fetch_assoc($result);
	$peaceto_id = $row["id"];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT friedensangebot, kriegsstart FROM de_ally_war
		 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?)
		 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
		[$allyid, $peaceto_id, $peaceto_id, $allyid]);
	$alreadyinXallys = mysqli_num_rows($result);
	if ($alreadyinXallys == 0)
		die (krieg_abbruch("$allywar_lang[msg_1].."));

	$row = mysqli_fetch_assoc($result);
	$friedensangebot = $row["friedensangebot"];
	$kriegsstart = strtotime($row["kriegsstart"]);

	$now = time();

	$vergangen = $now - $kriegsstart;

	$ultimatum = (60*60*72);



	if ($vergangen<=$ultimatum)
	{
		$uebrig = $ultimatum-$vergangen;
		$stunden = floor($uebrig / (60*60));

		//Rest der angefangenen Stunde; das frühere "% ($stunden*3600)" brach in der letzten Stunde mit Modulo 0 ab
		$minuten = floor(($uebrig % (60*60))/60);


		die(krieg_abbruch("$allywar_lang[msg_2_1] $stunden $allywar_lang[msg_2_2] $minuten $allywar_lang[msg_2_3]"));
	}

	$peaceto_html = htmlspecialchars($peaceto, ENT_QUOTES, 'UTF-8');
	if ($friedensangebot == $peaceto_id)
	{
		mysqli_execute_query($GLOBALS['dbi'],
			"DELETE FROM de_ally_war
			 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?)
			 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
			[$allyid, $peaceto_id, $peaceto_id, $allyid]);
		echo krieg_meldung("$allywar_lang[msg_3] $peaceto_html!", 'ok');
		include("ally/allyfunctions.inc.php");
		writeHistory($allytag, "$allywar_lang[msg_4_1] <i>$peaceto</i> $allywar_lang[msg_4_2]",true);
		writeHistory($peaceto, "$allywar_lang[msg_4_1] <i>$allytag</i> $allywar_lang[msg_4_2]",true);

	}
	elseif ($friedensangebot == 0)
	{
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_ally_war SET friedensangebot=?
			 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?)
			 OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
			[$allyid, $allyid, $peaceto_id, $peaceto_id, $allyid]);
		echo krieg_meldung($allywar_lang['msg_5_1'].' '.$peaceto_html.' '.$allywar_lang['msg_5_2'].' '.$peaceto_html.' '.$allywar_lang['msg_5_3'], 'ok');
		include('ally/allyfunctions.inc.php');
		writeHistory($allytag, "$allywar_lang[msg_6_1] <i>$peaceto</i> $allywar_lang[msg_6_2]",true);
		writeHistory($peaceto, "$allywar_lang[msg_7_1] <i>$allytag</i> $allywar_lang[msg_7_2]",true);

	}
	elseif ($friedensangebot == $allyid)
	{
		//die Allianzinfo zeigt den Leader der Gegenseite, dort kann man ihm schreiben
		echo krieg_meldung($allywar_lang['msg_8_1'].' '.$peaceto_html.' '.$allywar_lang['msg_8_2'].' <a href="ally_detail.php?allytag='.urlencode($peaceto).'">'.$allywar_lang['msg_8_3'].'</a>', 'fehler');
	}
	else
	{
		echo krieg_meldung($allywar_lang['msg_1'].'.. ?', 'fehler');
	}
}

$an=isset($_POST['an']) ? $_POST['an'] : false;
if($an and ($isleader || $iscoleader)){
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(*) as count FROM de_ally_war
		 WHERE ally_id_angreifer=? OR ally_id_angegriffener=?",
		[$allyid, $allyid]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys >= 2)
		die (krieg_abbruch("$allywar_lang[msg_9_1] $alreadyinXallys $allywar_lang[msg_9_2] $alreadyinXallys $allywar_lang[msg_9_3]!"));

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$angegriffener = $row["id"] ?? 0;

	//nur gegen eine bestehende, fremde Allianz (das Auswahlfeld lässt die eigene aus, die Anfrage nicht)
	if(empty($angegriffener) || $angegriffener==$allyid){
		die(krieg_abbruch('Krieg kann nur einer anderen, bestehenden Allianz erklärt werden.'));
	}

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(user_id) as count, SUM(score) as sum FROM de_user_data WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$feindmitglieder = $row['count'];
	$feindpunkte = $row['sum'];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT COUNT(user_id) as count, SUM(score) as sum FROM de_user_data WHERE allytag=?",
		[$allytag]);
	$row = mysqli_fetch_assoc($result);
	$selbstmitglieder = $row['count'];
	$selbstpunkte = $row['sum'];

	//Name der Zielallianz für die Platzhalter in den Sprachtexten
	$an_html = htmlspecialchars($an, ENT_QUOTES, 'UTF-8');

	if (($selbstmitglieder/2)>$feindmitglieder)
		die(krieg_abbruch(str_replace('{ALLY}', $an_html, $allywar_lang['msg_10'])));

	if (($selbstpunkte/2)>$feindpunkte)
		die(krieg_abbruch(str_replace('{ALLY}', $an_html, $allywar_lang['msg_11'])));


	if (($feindmitglieder/3)>$selbstmitglieder)
		die(krieg_abbruch(htmlspecialchars($an, ENT_QUOTES, 'UTF-8')." $allywar_lang[msg_12]"));

	if (($feindpunkte/3)>$selbstpunkte)
		die(krieg_abbruch(htmlspecialchars($an, ENT_QUOTES, 'UTF-8')." $allywar_lang[msg_13]"));

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_buendniss_antrag
		 WHERE (ally_id_antragsteller=? AND ally_id_partner=?)
		 OR (ally_id_antragsteller=? AND ally_id_partner=?)",
		[$allyid, $angegriffener, $angegriffener, $allyid]);

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_partner
		 WHERE (ally_id_1=? AND ally_id_2=?)
		 OR (ally_id_1=? AND ally_id_2=?)",
		[$allyid, $angegriffener, $angegriffener, $allyid]);

	mysqli_execute_query($GLOBALS['dbi'],
		"INSERT INTO de_ally_war
		 (ally_id_angreifer, ally_id_angegriffener, kriegsstart, friedensangebot)
		 VALUES (?, ?, NOW(), 0)",
		[$allyid, $angegriffener]);

	echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok">'.$allywar_lang['msg_14'].' !</div><div class="ally-aktionen"><a href="ally_war.php" class="mod-btn mod-btn-leise">Zur&uuml;ck</a></div></div>';
	include('ally/allyfunctions.inc.php');
	writeHistory($allytag, "$allywar_lang[msg_15_1] <i>$an</i> $allywar_lang[msg_15_2]",true);
	writeHistory($an, "$allywar_lang[msg_16_1] <i>$allytag</i> $allywar_lang[msg_16_2]",true);

}else {
	rahmen_oben('Krieg');
	echo '<div class="ally mod">';

  	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT ally_id_angegriffener, ally_id_angreifer FROM de_ally_war
		 WHERE (ally_id_angegriffener=? OR ally_id_angreifer=?)",
		[$allyid, $allyid]);
	if (mysqli_num_rows($result))	{
		echo '<div class="mod-typ ally-typ-abstand">'.$allywar_lang['msg_17'].'</div><div class="ally-zeilen">';

		while ($row = mysqli_fetch_assoc($result))
		{
			if ($row['ally_id_angegriffener'] == $allyid)
				$showallyid = $row['ally_id_angreifer'];
			else
				$showallyid = $row['ally_id_angegriffener'];

			$result2 = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_allys WHERE id=?",
				[$showallyid]);
			$row2 = mysqli_fetch_assoc($result2);
			$angegriffener = $row2["allytag"];

			echo '
			<div class="ally-diplozeile">
				<span class="ally-diplo-paar"><span class="ally-diplo-zeichen ally-diplo-krieg">&#9876;</span><a href="ally_detail.php?allyid='.$showallyid.'" class="ally-person">'.htmlspecialchars($angegriffener, ENT_QUOTES, 'UTF-8').'</a></span>';

			if($isleader || $iscoleader) {

				echo '<a href="ally_war.php?peaceto='.urlencode($angegriffener).'" class="mod-btn mod-btn-leise ally-btn-klein" title="'.$allywar_lang['peace'].'">'.$allywar_lang['declarepeace'].'</a>';

			}

			echo '
			</div>';

			$bestehende_kriege[] = $angegriffener;
		}

		echo '</div>';
	}else{
		echo '<div class="mod-leer">Eure Allianz befindet sich zurzeit in keinem Krieg.</div>';
	}
	if($isleader || $iscoleader)
	{

		echo '
		<div class="ally-abschnitt">
			<div class="mod-typ">'.$allywar_lang['war'].'</div>
			<form name="krieg" method="POST" action="ally_war.php" class="ally-zeilenformular">
				<span class="ally-leise-text">'.$allywar_lang['an'].'</span>
				<select name="an" class="mod-eingabe ally-auswahl">
		';

				$result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT allytag, id FROM de_allys ORDER BY allytag");
				while ($row = mysqli_fetch_assoc($result))
				{
					if (!in_array($row['allytag'],$bestehende_kriege) and $allytag != $row['allytag'])
					{
						echo '<option value="'.$row['allytag'].'"';
						if ($selected==$row['id']) echo ' selected';
						echo '>'.$row['allytag'].'</option>';
					}
				}

		echo	    '</select>
				<button type="submit" name="B1" value="'.$allywar_lang['abschicken'].'" class="mod-btn mod-btn-gefahr" data-bestaetigen="Wirklich Krieg erkl&auml;ren?">'.$allywar_lang['war'].'</button>
			</form>
			<div class="mod-hinweis ally-abstand">Die gegnerische Allianz muss mindestens halb so viele Mitglieder und Punkte haben wie Eure Allianz und darf h&ouml;chstens dreimal so viele haben. Ein Krieg l&ouml;st ein bestehendes B&uuml;ndnis und offene B&uuml;ndnisantr&auml;ge mit dieser Allianz auf. Es sind h&ouml;chstens zwei Kriege gleichzeitig m&ouml;glich.</div>
		</div>';
	}

	echo '</div>';
	rahmen_unten();
}


?>
<?php include('ally/ally.footer.inc.php'); ?>

</body>
</html>
