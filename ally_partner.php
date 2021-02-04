<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.partner.lang.php';
include_once 'functions.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, newtrans, newnews, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$allytag=$row["allytag"];

$maxbuendnis=1;

/*$db_daten=mysql_query("SELECT nic FROM de_login WHERE user_id='$ums_user_id'");
$row = mysql_fetch_array($db_daten);
$nic=$row["nic"];*/

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allypartner_lang[title];?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>

<font face="tahoma" style="font-size:8pt;">

<?php
include "resline.php";
include ("ally/ally.menu.inc.php");

//test auf passendes gebäude
$ally_result = mysql_query("SELECT * FROM de_allys WHERE allytag='$allytag'");
if ($ally_result){
	$ally_data = mysql_fetch_array($ally_result);
	//diplomatiezentrum
	$bldg=$ally_data['bldg1'];
}

//test auf vorhandenes allianzprojekt Diplomatiezentrum
if($bldg<1){
  die('<br><div class="info_box text2">F&uuml;r ein Allianzb&uuml;ndnis wird ein Diplomatiezentrum ben&ouml;tigt.</div></body></html>');
}

//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
if($delallyid1 and $delallyid2 and ($isleader || $iscoleader))
{
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	$query = "select count(*) from de_ally_partner, de_allys where ally_id_1 = $delallyid1 and ally_id_2 = $delallyid2 and ((ally_id_1 = id) or (ally_id_2 = id)) and (leaderid=$ums_user_id OR coleaderid1=$ums_user_id OR coleaderid2=$ums_user_id OR coleaderid3=$ums_user_id)";
	$result = @mysql_query($query);
	$alreadyinXallys = 0;
	$alreadyinXallys = @mysql_result($result,0,0);
	if ($alreadyinXallys == 0)
		die ("$allypartner_lang[msg_1]");

	$query="DELETE FROM de_ally_partner WHERE ally_id_1=$delallyid1 and ally_id_2=$delallyid2";
	$result = mysql_query($query);
	echo $allypartner_lang[msg_2];
	include("ally/allyfunctions.inc.php");
	$delallyid1_tag = getAllyTag($delallyid1);
	$delallyid2_tag = getAllyTag($delallyid2);

	writeHistory($delallyid1_tag, "$allypartner_lang[msg_3_1] <i>$delallyid2_tag</i> $allypartner_lang[msg_3_2]",true);
	writeHistory($delallyid2_tag, "$allypartner_lang[msg_3_1] <i>$delallyid1_tag</i> $allypartner_lang[msg_3_2]",true);

}
//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
if($antrag and ($isleader || $iscoleader)) {
	$antrag = htmlentities ($antrag,ENT_QUOTES);
	$antrag = str_replace("\n","<br>",$antrag);

	//---------------
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	$query="SELECT id FROM de_allys WHERE leaderid=$ums_user_id OR coleaderid1=$ums_user_id OR coleaderid2=$ums_user_id OR coleaderid3=$ums_user_id";
	$result = mysql_query($query);
	$allyid = mysql_result($result,0,"id");

	$query = "select count(*) from de_ally_partner where ally_id_1=$allyid or ally_id_2=$allyid";
	$result = mysql_query($query);
	$alreadyinXallys = mysql_result($result,0,0);
	if ($alreadyinXallys >= $maxbuendnis)
		die ("$allypartner_lang[msg_4_1] $alreadyinXallys $allypartner_lang[msg_4_2] $alreadyinXallys $allypartner_lang[msg_4_3]");
	//---------------

	//---------------
	$query="SELECT id FROM de_allys WHERE allytag='$an'";
	$result = mysql_query($query);
	$allyid_partner = mysql_result($result,0,"id");

	$query = "select count(*) from de_ally_partner where ally_id_1='$allyid_partner' or ally_id_2='$allyid_partner'";
	$result = mysql_query($query);
	$alreadyinXallys = mysql_result($result,0,0);
	if ($alreadyinXallys >= $maxbuendnis)
		die ("$allypartner_lang[msg_5_1] $alreadyinXallys $allypartner_lang[msg_5_2]");
	//---------------
	
	//�berpr�fen ob man mit dem gew�nschten b�ndnispartner evtl. im krieg ist
	$query = "SELECT * FROM de_ally_war WHERE (ally_id_angreifer = '$allyid' AND ally_id_angegriffener = '$allyid_partner') OR (ally_id_angreifer = '$allyid_partner' AND ally_id_angegriffener = '$allyid')";
	$db_daten = mysql_query($query);
	$num = mysql_num_rows($db_daten);
	if ($num>0)
		die ('<div class="info_box text2">Mit dieser Allianz herrscht Krieg und ein B&uuml;ndnis ist nicht m&ouml;glich.</div></body></html>');

	$sqlquery = "INSERT into de_ally_buendniss_antrag (ally_id_antragsteller, ally_id_partner, antrag) VALUES ($allyid, $allyid_partner, '$antrag')";
	//if ($ums_user_id == 6267) echo $sqlquery;
	$result = @mysql_query($sqlquery);
	//if ($ums_user_id == 6267) mysql_error();
	if (!$result)
	{
		$sqlquery = "UPDATE de_ally_buendniss_antrag SET ally_id_partner=$allyid_partner, antrag='$antrag' where ally_id_antragsteller=$allyid";
		$result = @mysql_query($sqlquery);

	}
	echo "$allypartner_lang[msg_6_1] $an $allypartner_lang[msg_6_2] $an $allypartner_lang[msg_6_3]";
	include("ally/allyfunctions.inc.php");
	writeHistory($allytag, "$allypartner_lang[msg_7_1] <i>$an</i> $allypartner_lang[msg_7_2]",true);
	writeHistory($an, "$allypartner_lang[msg_8_1] <i>$allytag</i> $allypartner_lang[msg_8_2]",true);

}
else {
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
  	if ($isleader || $iscoleader)
  		$query = "SELECT ally_id_1, ally_id_2  FROM de_allys, de_ally_partner where ((ally_id_1=id) or (ally_id_2=id)) and (leaderid=$ums_user_id || coleaderid1=$ums_user_id || coleaderid2=$ums_user_id || coleaderid3=$ums_user_id)";
  	else
  		$query = "SELECT ally_id_1, ally_id_2  FROM de_allys, de_ally_partner where ((ally_id_1=id) or (ally_id_2=id)) and allytag='$allytag'";
	$result = @mysql_query($query);
	if (mysql_num_rows($result))
	{
		echo 	"<table border=\"0\" width=\"600\" cellspacing=\"0\" cellpadding=\"0\">\n".
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
							//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
							if ($isleader || $iscoleader)
								echo "<td><b>$allypartner_lang[delbuendnis]</td>";
						echo "</tr>";

		while ($row = @mysql_fetch_array($result))
		{
			$query = "SELECT allytag FROM de_allys where id=$row[ally_id_1]";
			$result2 = @mysql_query($query);
			$antragsteller = @mysql_result($result2,0,"allytag");

			$query = "SELECT allytag FROM de_allys where id=$row[ally_id_2]";
			$result2 = @mysql_query($query);
			$allypartner = @mysql_result($result2,0,"allytag");

			echo 	"<tr class=\"cl\">".
					"<td>".$antragsteller."</td><td>".$allypartner."</td>";
					//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
					if ($isleader || $iscoleader)
						echo "<td><a href=\"$PHP_SELF?delallyid1=".$row[ally_id_1]."&delallyid2=".$row[ally_id_2]."\">$allypartner_lang[delbuendnis]</a></td>";
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
	//Pr�fung auf coleader hinzugef�gt von Ascendant (4.9.2002)
	if($isleader || $iscoleader)
	{
		$query = "select antrag, ally_id_partner from de_ally_buendniss_antrag, de_allys where ally_id_antragsteller=id and (leaderid=$ums_user_id OR coleaderid1=$ums_user_id OR coleaderid2=$ums_user_id OR coleaderid3=$ums_user_id)";
		//if ($ums_user_id == 6267) echo $query;
		$result = @mysql_query($query);
		$laufenderantrag = @mysql_result($result,0,"antrag");
		$selected = @mysql_result($result,0,"ally_id_partner");
		if ($laufenderantrag) echo "<br>$allypartner_lang[msg_10]<br>";

		echo	"<form name=\"buendniss\" method=\"POST\" action=\"$PHP_SELF\">\n".
			  "<table border=\"0\" width=\"602\" cellspacing=\"1\" cellpadding=\"0\">\n".
			     "<tr><td colspan=2 class=\"tc\">$allypartner_lang[choosepartner]</td></tr>".
			     "<tr class=\"cl\">\n".
			      "<td width=\"50%\">$allypartner_lang[mit]:</td>\n".
			      "<td width=\"50%\">".
			      "<select name=\"an\">\n";

			      	$query = "SELECT allytag, id FROM de_allys order by allytag";
				$result = mysql_query($query);
				while ($row = mysql_fetch_array($result))
				{
					if (!in_array($row[allytag],$bestehende_buendnisse) and $allytag != $row[allytag])
					{
						echo "<option value=\"".$row[allytag]."\"";
						if ($selected==$row[id]) echo " selected";
						echo ">".$row[allytag]."</option>\n";
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
<?php include "fooban.php"; ?>
</body>
</html>