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
  die('<br><div class="info_box text2">F&uuml;r ein Allianzb&uuml;ndnis wird ein Diplomatiezentrum ben&ouml;tigt.</div></body></html>');
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
		die ("$allypartner_lang[msg_1]");

	mysqli_execute_query($GLOBALS['dbi'],
		"DELETE FROM de_ally_partner WHERE ally_id_1=? AND ally_id_2=?",
		[$delallyid1, $delallyid2]);
	echo $allypartner_lang[msg_2];
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
		die ("$allypartner_lang[msg_4_1] $alreadyinXallys $allypartner_lang[msg_4_2] $alreadyinXallys $allypartner_lang[msg_4_3]");
	//---------------

	//---------------
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE allytag=?",
		[$an]);
	$row = mysqli_fetch_assoc($result);
	$allyid_partner = $row['id'];

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT count(*) as count FROM de_ally_partner WHERE ally_id_1=? OR ally_id_2=?",
		[$allyid_partner, $allyid_partner]);
	$row = mysqli_fetch_assoc($result);
	$alreadyinXallys = $row['count'];
	if ($alreadyinXallys >= $maxbuendnis)
		die ("$allypartner_lang[msg_5_1] $alreadyinXallys $allypartner_lang[msg_5_2]");
	//---------------
	
	//überprüfen ob man mit dem gewünschten bündnispartner evtl. im krieg ist
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_ally_war 
		 WHERE (ally_id_angreifer=? AND ally_id_angegriffener=?) OR (ally_id_angreifer=? AND ally_id_angegriffener=?)",
		[$allyid, $allyid_partner, $allyid_partner, $allyid]);
	$num = mysqli_num_rows($db_daten);
	if ($num>0)
		die ('<div class="info_box text2">Mit dieser Allianz herrscht Krieg und ein B&uuml;ndnis ist nicht m&ouml;glich.</div></body></html>');

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"INSERT INTO de_ally_buendniss_antrag (ally_id_antragsteller, ally_id_partner, antrag) VALUES (?, ?, ?)",
		[$allyid, $allyid_partner, $antrag]);

	if (!$result){
		mysqli_execute_query($GLOBALS['dbi'],
			"UPDATE de_ally_buendniss_antrag SET ally_id_partner=?, antrag=? WHERE ally_id_antragsteller=?",
			[$allyid_partner, $antrag, $allyid]);

	}
	echo "<br><div class=\"info_box\">$allypartner_lang[msg_6_1] $an $allypartner_lang[msg_6_2] $an $allypartner_lang[msg_6_3]</div>";
	include("ally/allyfunctions.inc.php");
	writeHistory($allytag, "$allypartner_lang[msg_7_1] <i>$an</i> $allypartner_lang[msg_7_2]",true);
	writeHistory($an, "$allypartner_lang[msg_8_1] <i>$allytag</i> $allypartner_lang[msg_8_2]",true);

}
else {
	$bestehende_buendnisse=array();

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
		echo 	"<br><table border=\"0\" width=\"600\" cellspacing=\"0\" cellpadding=\"0\">\n".
				"<tr align=\"center\">\n".
					"<td width=\"13\" height=\"37\" class=\"rol\">&nbsp;</td>\n".
					"<td width=\"500\" align=\"center\" class=\"ro\">$allypartner_lang[msg_9]:</td>\n".
					"<td width=\"13\" class=\"ror\">&nbsp;</td>\n".
				"</tr>\n".
				"<tr>".
					"<td width=\"13\" class=\"rl\">&nbsp;</td>".
					"<td>\n".
					"<table border=\"0\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">\n".
						"<tr class=\"tc\">".
							"<td><b>$allypartner_lang[partnereins]</td>".
							"<td><b>$allypartner_lang[partnerzwei]</td>";

							if ($isleader || $iscoleader)
								echo "<td><b>$allypartner_lang[delbuendnis]</td>";
						echo "</tr>";

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

			echo 	"<tr class=\"cl\">".
					"<td>".$antragsteller."</td><td>".$allypartner."</td>";
					
					if ($isleader || $iscoleader)
						echo "<td><a href=\"ally_partner.php?delallyid1=".$row['ally_id_1']."&delallyid2=".$row['ally_id_2']."\">$allypartner_lang[delbuendnis]</a></td>";
				echo "</tr>";
			$bestehende_buendnisse[] = $allypartner;
			$bestehende_buendnisse[] = $antragsteller;
		}
		echo 			"</table>".
					"</td>".
					"<td width=\"13\" class=\"rr\">&nbsp;</td>\n".
				"</tr>".
				'<tr><td width="13" class="rul">&nbsp;</td>'.
				'<td width="13" class="ru">&nbsp;</td>'.
				'<td width="13" class="rur">&nbsp;</td>'.
				'</tr>'.
			"</table><BR>";
	}

	if($isleader || $iscoleader){
		$result = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT antrag, ally_id_partner FROM de_ally_buendniss_antrag, de_allys 
			 WHERE ally_id_antragsteller=id 
			 AND (leaderid=? OR coleaderid1=? OR coleaderid2=? OR coleaderid3=?)",
			[$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
		$row = mysqli_fetch_assoc($result);
		$laufenderantrag = $row['antrag'] ?? '';
		$selected = $row['ally_id_partner'] ?? '';
		if ($laufenderantrag){
			echo "<br><div class=\"info_box\">$allypartner_lang[msg_10]</div><br>";
		}

		echo	"<form name=\"buendniss\" method=\"POST\">\n".
			  "<table border=\"0\" width=\"602\" cellspacing=\"1\" cellpadding=\"0\">\n".
			     "<tr><td colspan=2 class=\"tc\">$allypartner_lang[choosepartner]</td></tr>".
			     "<tr class=\"cl\">\n".
			      "<td width=\"50%\">$allypartner_lang[mit]:</td>\n".
			      "<td width=\"50%\">".
			      "<select name=\"an\">\n";

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

		echo	    "</select></td>\n".
			    "</tr>\n".
			    "<tr class=\"cl\">\n".
			      "<td width=\"50%\">$allypartner_lang[antrag]:</td>\n".
			      "<td width=\"50%\"><textarea rows=\"5\" name=\"antrag\" cols=\"30\">".$laufenderantrag."</textarea></td>\n".
			      "</tr>\n".
			  "</table>\n".
			  "<input type=\"submit\" value=\"$allypartner_lang[abschicken]\" name=\"B1\">\n".
			"</form>\n";
	}
}


?>
<br>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>