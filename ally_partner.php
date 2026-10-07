<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.partner.lang.php';
include 'functions.php';

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag
     FROM de_user_data
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];

$maxbuendnis=1;

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allypartner_lang['title'];?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";
include ("ally/ally.menu.inc.php");

//Meldungen unter den Reitern; Abbrüche (die) schließen den Kasten selbst
function partner_abbruch($text)
{
	return '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$text.'</div><div class="ally-aktionen"><a href="ally_partner.php" class="mod-btn mod-btn-leise">Zur&uuml;ck</a></div></div></body></html>';
}

//test auf passendes gebäude
$ally_result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_allys WHERE allytag=?",
    [$allytag]);
if ($ally_result){
	$ally_data = mysqli_fetch_assoc($ally_result);
	//diplomatiezentrum
	$bldg=$ally_data['bldg1'];
}

//test auf vorhandenes allianzprojekt Diplomatiezentrum
if($bldg<1){
  die('<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">F&uuml;r ein Allianzb&uuml;ndnis wird ein Diplomatiezentrum ben&ouml;tigt.</div><div class="ally-aktionen"><a href="ally_bldg.php" class="mod-btn mod-btn-leise">Zu den Allianzprojekten</a></div></div></body></html>');
}

$delallyid1=isset($_REQUEST['delallyid1']) ? $_REQUEST['delallyid1'] : false;
$delallyid2=isset($_REQUEST['delallyid2']) ? $_REQUEST['delallyid2'] : false;
if($delallyid1 && $delallyid2 && ($isleader || $iscoleader)){

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT count(*) as count FROM de_ally_partner, de_allys
		 WHERE ally_id_1=? AND ally_id_2=? AND ((ally_id_1=id) OR (ally_id_2=id))
		 AND (leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?)",
		[$delallyid1, $delallyid2, $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys == 0)
		die (partner_abbruch($allypartner_lang['msg_1']));

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_partner WHERE ally_id_1=? AND ally_id_2=?",
		[$delallyid1, $delallyid2]);
	echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok">'.$allypartner_lang['msg_2'].'</div></div>';
	include("ally/allyfunctions.inc.php");
	$delallyid1_tag = getAllyTag($delallyid1);
	$delallyid2_tag = getAllyTag($delallyid2);

	writeHistory($delallyid1_tag, "$allypartner_lang[msg_3_1] <i>$delallyid2_tag</i> $allypartner_lang[msg_3_2]",true);
	writeHistory($delallyid2_tag, "$allypartner_lang[msg_3_1] <i>$delallyid1_tag</i> $allypartner_lang[msg_3_2]",true);

}

$antrag=isset($_REQUEST['antrag']) ? $_REQUEST['antrag'] : false;
$an=isset($_REQUEST['an']) ? $_REQUEST['an'] : false;
if($antrag && $an && ($isleader || $iscoleader)) {
	$antrag = htmlentities ($antrag,ENT_QUOTES);
	$antrag = str_replace("\n","<br>",$antrag);

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?",
		[$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);
	$allyid = $row['id'];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT count(*) as count FROM de_ally_partner WHERE ally_id_1=? OR ally_id_2=?",
		[$allyid, $allyid]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys >= $maxbuendnis)
		die (partner_abbruch("$allypartner_lang[msg_4_1] $alreadyinXallys $allypartner_lang[msg_4_2] $alreadyinXallys $allypartner_lang[msg_4_3]"));
	//---------------

	//---------------
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$allyid_partner = $row['id'] ?? 0;
	if (!$allyid_partner || $allyid_partner == $allyid)
		die (partner_abbruch('Diese Allianz kann kein B&uuml;ndnisangebot erhalten.'));

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT count(*) as count FROM de_ally_partner WHERE ally_id_1=? OR ally_id_2=?",
		[$allyid_partner, $allyid_partner]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys >= $maxbuendnis)
		die (partner_abbruch("$allypartner_lang[msg_5_1] $alreadyinXallys $allypartner_lang[msg_5_2]"));
	//---------------

	//überprüfen ob man mit dem gewünschten bündnispartner evtl. im krieg ist
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_ally_war
		 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?) OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
		[$allyid, $allyid_partner, $allyid_partner, $allyid]);
	$num = mysqli_num_rows($db_daten);
	if ($num>0)
		die (partner_abbruch('Mit dieser Allianz herrscht Krieg und ein B&uuml;ndnis ist nicht m&ouml;glich.'));

	include_once("ally/allyfunctions.inc.php");

	//ein Angebot an eine andere Allianz zieht das laufende zurück; die bisher angefragte Allianz erfährt das,
	//sonst bleibt bei ihr nur die Meldung über ein Angebot, das es nicht mehr gibt
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT ally_id_partner FROM de_ally_buendniss_antrag WHERE ally_id_antragsteller=?",
		[$allyid]);
	$row = mysqli_fetch_assoc($result);
	$an_vorher = '';
	if ($row && $row['ally_id_partner'] != $allyid_partner) {
		$an_vorher = (string)getAllyTag($row['ally_id_partner']);
	}

	//je Allianz nur ein laufender Antrag (eindeutiger Schlüssel ally_id_antragsteller): ein neuer ersetzt den alten;
	//früher INSERT mit UPDATE als Ausweichweg, seit PHP 8.1 bricht der doppelte Schlüssel aber mit einer Exception ab
	mysqli_execute_query($GLOBALS['dbi'],
		"INSERT INTO de_ally_buendniss_antrag (ally_id_antragsteller, ally_id_partner, antrag) VALUES (?, ?, ?)
		 ON DUPLICATE KEY UPDATE ally_id_partner=VALUES(ally_id_partner), antrag=VALUES(antrag)",
		[$allyid, $allyid_partner, $antrag]);

	$meldung = $allypartner_lang['msg_6_1'].' <b>'.htmlspecialchars($an, ENT_QUOTES, 'UTF-8').'</b> '.$allypartner_lang['msg_6_2'].' '.htmlspecialchars($an, ENT_QUOTES, 'UTF-8').' '.$allypartner_lang['msg_6_3'];
	if ($an_vorher != '') {
		$meldung = $allypartner_lang['msg_11_1'].' <b>'.htmlspecialchars($an_vorher, ENT_QUOTES, 'UTF-8').'</b> '.$allypartner_lang['msg_11_2'].'<br><br>'.$meldung;
		writeHistory($allytag, "$allypartner_lang[msg_11_1] <i>$an_vorher</i> $allypartner_lang[msg_11_2]",true);
		writeHistory($an_vorher, "$allypartner_lang[msg_12_1] <i>$allytag</i> $allypartner_lang[msg_12_2]",true);
	}
	echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok">'.$meldung.'</div><div class="ally-aktionen"><a href="ally_partner.php" class="mod-btn mod-btn-leise">Zur&uuml;ck</a></div></div>';
	writeHistory($allytag, "$allypartner_lang[msg_7_1] <i>$an</i> $allypartner_lang[msg_7_2]",true);
	writeHistory($an, "$allypartner_lang[msg_8_1] <i>$allytag</i> $allypartner_lang[msg_8_2]",true);

}
else {
	$bestehende_buendnisse=array();

	rahmen_oben('B&uuml;ndnisse');
	echo '<div class="ally mod">';

  	if ($isleader || $iscoleader)
		$result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT ally_id_1, ally_id_2 FROM de_allys, de_ally_partner
			 WHERE ((ally_id_1=id) OR (ally_id_2=id))
			 AND (leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?)",
			[$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
  	else
		$result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT ally_id_1, ally_id_2 FROM de_allys, de_ally_partner
			 WHERE ((ally_id_1=id) OR (ally_id_2=id)) AND allytag=?",
			[$allytag]);
	if (mysqli_num_rows($result)){
		echo '<div class="mod-typ ally-typ-abstand">'.$allypartner_lang['msg_9'].'</div><div class="ally-zeilen">';

		while ($row = mysqli_fetch_assoc($result)){
			$result2 = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_allys WHERE id=?",
				[$row['ally_id_1']]);
			$row2 = mysqli_fetch_assoc($result2);
			$antragsteller = $row2['allytag'];

			$result2 = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_allys WHERE id=?",
				[$row['ally_id_2']]);
			$row2 = mysqli_fetch_assoc($result2);
			$allypartner = $row2['allytag'];

			echo '
			<div class="ally-diplozeile">
				<span class="ally-diplo-paar"><b>'.htmlspecialchars($antragsteller, ENT_QUOTES, 'UTF-8').'</b><span class="ally-diplo-zeichen ally-diplo-buendnis">&#8644;</span><b>'.htmlspecialchars($allypartner, ENT_QUOTES, 'UTF-8').'</b></span>';

					if ($isleader || $iscoleader)
						echo '<a href="ally_partner.php?delallyid1='.$row['ally_id_1'].'&delallyid2='.$row['ally_id_2'].'" class="mod-btn mod-btn-gefahr ally-btn-klein" data-bestaetigen="Wirklich l&ouml;sen?">'.$allypartner_lang['delbuendnis'].'</a>';
			echo '
			</div>';
			$bestehende_buendnisse[] = $allypartner;
			$bestehende_buendnisse[] = $antragsteller;
		}
		echo '</div>';
	}else{
		echo '<div class="mod-leer">Eure Allianz hat zurzeit kein B&uuml;ndnis.</div>';
	}

	if($isleader || $iscoleader){
		$result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT a.antrag, a.ally_id_partner, p.allytag AS partner_tag
			 FROM de_ally_buendniss_antrag a
			 JOIN de_allys s ON s.id=a.ally_id_antragsteller
			 LEFT JOIN de_allys p ON p.id=a.ally_id_partner
			 WHERE s.leaderid=? OR s.coleaderid1=? OR s.coleaderid2=? OR s.coleaderid3=?",
			[$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
		$row = mysqli_fetch_assoc($result);
		$laufenderantrag = $row['antrag'] ?? '';
		$selected = $row['ally_id_partner'] ?? '';

		echo '<div class="ally-abschnitt"><div class="mod-typ">'.$allypartner_lang['choosepartner'].'</div>';
		if ($row){
			echo '<div class="mod-hinweis">'.$allypartner_lang['msg_10_1'].' <b>'.htmlspecialchars($row['partner_tag'] ?? '', ENT_QUOTES, 'UTF-8').'</b>. '.$allypartner_lang['msg_10_2'].'</div>';
		}

		echo	'<form name="buendniss" method="POST" class="ally-formular">'.
			      '<label class="ally-feld">'.
			      '<span class="mod-typ">'.$allypartner_lang['mit'].'</span>'.
			      '<select name="an" class="mod-eingabe">';

			    $result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT allytag, id FROM de_allys ORDER BY allytag");
				while ($row = mysqli_fetch_assoc($result))
				{
					if (!in_array($row['allytag'],$bestehende_buendnisse) and $allytag != $row['allytag'])
					{
						echo "<option value=\"".$row['allytag']."\"";
						if ($selected==$row['id']) echo " selected";
						echo ">".$row['allytag']."</option>\n";
					}
				}

		echo	    '</select></label>'.
			      '<label class="ally-feld ally-feld-breit">'.
			      '<span class="mod-typ">'.$allypartner_lang['antrag'].'</span>'.
			      '<textarea rows="5" name="antrag" class="mod-eingabe">'.$laufenderantrag.'</textarea>'.
			      '</label>'.
			      '<div class="ally-aktionen ally-feld-breit"><input type="submit" value="'.$allypartner_lang['abschicken'].'" name="B1" class="mod-btn"></div>'.
			'</form></div>';
	}

	echo '</div>';
	rahmen_unten();
}


?>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>
