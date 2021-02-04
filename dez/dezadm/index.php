<?php
	include "../../inccon.php";
	include "../../dez/dez_db.inc.php";
?>
<!doctype html>
<html>
<head>
	<title>DE-DigiPaper Administrator</title>
	
	<style type="text/css">
		body { background-color: #000000; color: #3399FF; font-family: Arial; font-size: 10pt; }
		td { color: #3399FF; font-family: Arial; font-size: 10pt; }
		td.ubt { font-weight: bold; text-decoration: underline; text-align: center; }
		td.bt { font-weight: bold; text-align: center; }
		a { color: #3399FF; font-family: Arial; font-size: 10pt; text-decoration: none; }
		a:hover { color: #3399FF; font-family: Arial; font-size: 10pt; text-decoration: none; }
		a:visited { color: #88DDFF; font-family: Arial; font-size: 10pt; text-decoration: none; }
		.sr { color: #FF0000; }
		.sg { color: #00FF00; }
		.sy { color: #FFFF00; }
		input { color: #3399FF; font-family: Arial; font-size: 10pt; border: 1px solid #3399FF; background-color: #000000; }
		textarea { color: #3399FF; font-family: Arial; font-size: 10pt; border: 1px solid #3399FF; background-color: #000000; }
	</style>
</head>
<body>

<center>

<?php
	if (isset($_GET["zenable"])) { mysql_query("UPDATE ".TABLE_ZDATA." SET `eingestellt` = 0 WHERE `id` = ".$_GET["zid"]); }
	if (isset($_GET["zdisable"])) { mysql_query("UPDATE ".TABLE_ZDATA." SET `eingestellt` = 1 WHERE `id` = ".$_GET["zid"]); }

	if (isset($_GET["lenable"])) { mysql_query("UPDATE ".TABLE_ZDATA." SET `logofrei` = 1 WHERE `id` = ".$_GET["zid"]); }
	if (isset($_GET["ldisable"])) { mysql_query("UPDATE ".TABLE_ZDATA." SET `logofrei` = 0 WHERE `id` = ".$_GET["zid"]); }

	if (isset($_GET["aenable"])) {
		mysql_query("UPDATE ".TABLE_ADATA." SET `frei` = 1 WHERE `id` = ".$_GET["aid"]);

		$AData = mysql_fetch_assoc(mysql_query("SELECT `titel`, `datum` FROM ".TABLE_ADATA." WHERE `id` = ".$_GET["aid"]));
		$ZData = mysql_fetch_assoc(mysql_query("SELECT `name`, `abonenten`, `aktuell` FROM ".TABLE_ZDATA." WHERE `id` = ".$_GET["zid"]));

		$NTime = strftime("%Y%m%d%H%M%S");
		$NText = 'Ihre Zeitungsausgabe mit dem Titel "'.$AData["titel"].'" wurde von der Redaktion der DigiPaper geprüft und für gut befunden. Die Ausgabe wird daher nun von DigiPaper vertrieben und steht der gesamten Galaxie zur Verfügung. Herzlichen Glückwunsch und Danke für die Verwendung von DigiPaper.';

		if ($AData["datum"] > $ZData["aktuell"]) { mysql_query("UPDATE ".TABLE_ZDATA." SET `aktuell` = ".$AData["datum"]." WHERE `id` = ".$_GET["zid"]); }

		mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('".$_GET["uid"]."', 60, '".$NTime."', '".$NText."')");
		mysql_query("UPDATE de_user_data SET newnews = 1 where user_id = ".$_GET["uid"]);
		
		//$DBData = mysql_query("SELECT `name`, `abonenten` FROM ".TABLE_ZDATA." WHERE `id` = ".$_GET["zid"]);
		//if (mysql_num_fields($DBData)) {
		if (trim($ZData["abonenten"]) != "") {
			//$ZData = mysql_fetch_assoc($DBData);
			$ZAbo = substr($ZData["abonenten"], 1, -1);
			$UAbo = explode("##", $ZAbo);
			
			$NText = 'Es ist eine neue Ausgabe der Zeitung "'.$ZData["name"].'" mit dem Titel "'.$AData["titel"].'" in DigiPaper verfügbar. Sie bekommen diese Nachricht, da Sie die Zeitung abonniert haben. Sollten Sie kein Interesse mehr daran haben, können Sie das Abonnement jederzeit in einer unserer Fillialen kündigen.';
			if (count($UAbo) > 0) {
				for ($i = 0; $i < count($UAbo); $i++) {
					mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('".$UAbo[$i]."', 60, '".$NTime."', '".$NText."')");
					mysql_query("UPDATE de_user_data SET newnews = 1 where user_id = ".$UAbo[$i]);
				}
			}
		}
	}
	if (isset($_GET["adisable"])) {
		mysql_query("UPDATE ".TABLE_ADATA." SET `frei` = 0 WHERE `id` = ".$_GET["aid"]);

		$DBData = mysql_query("SELECT `datum` FROM ".TABLE_ADATA." WHERE `zid` = ".$_GET["zid"]." AND `frei` = 1 ORDER BY `id` DESC");
		if (mysql_num_rows($DBData)) { $ADatum = mysql_result($DBData, 0); }
		 else { $ADatum = 0; }
		
		mysql_query("UPDATE ".TABLE_ZDATA." SET `aktuell` = ".$ADatum." WHERE `id` = ".$_GET["zid"]);
	}

	if (isset($_GET["ausgaben"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?site">[ Zurück zur Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;">Ausgaben zur Zeitungen mit der ID '.$_GET["zid"].':</span><br><br>';
		echo '<table border="1" cellspacing="0" cellpadding="5">';
		echo ' <tr><td class="ubt" style="text-align: left;">Ausgabe</td><td>&nbsp;</td><td class="ubt">Titel</td><td class="ubt">Status</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';

		$DBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$_GET["zid"]." ORDER BY `datum` DESC");
		while ($AData = mysql_fetch_assoc($DBData)) {
			echo ' <tr><td>'.date("d.m.Y" ,$AData["datum"]).'</td>';
			echo '<td>[<a href="'.$PHP_SELF.'?zid='.$_GET["zid"].'&aid='.$AData["id"].'&adelete">Löschen</a>]</td>';
			echo '<td>'.$AData["titel"].'</td><td align="center">';
			if ($AData["frei"] == 1) { echo '[<a href="'.$PHP_SELF.'?zid='.$_GET["zid"].'&ausgaben&uid='.$_GET["uid"].'&aid='.$AData["id"].'&adisable"><span class="sg">aktiviert</span></a>]'; }
			 else  { echo '[<a href="'.$PHP_SELF.'?zid='.$_GET["zid"].'&ausgaben&uid='.$_GET["uid"].'&aid='.$AData["id"].'&aenable"><span class="sr">gesperrt</span></a>]'; }
			echo '</td><td>[<a href="'.$PHP_SELF.'?aid='.$AData["id"].'&read&uid='.$_GET["uid"].'">Lesen</a>]</td>';
			echo '<td>[<a href="'.$PHP_SELF.'?aid='.$AData["id"].'&change&uid='.$_GET["uid"].'">Bearbeiten</a>]</td>';
			echo '<td>[<a href="'.$PHP_SELF.'?zid='.$_GET["zid"].'&aid='.$AData["id"].'&amove&uid='.$_GET["uid"].'">Verschieben</a>]</td></tr>';

		}

		echo '</table>';
		echo '</center></body></html>';
		die;
	}

	if (isset($_GET["amove"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?zid='.$_GET["zid"].'&ausgaben&uid='.$_GET["uid"].'">[ Zurück zur Ausgaben-Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;">Ausgabe mit der ID '.$_GET["aid"].' verschieben:</span><br><br>';

		echo '<form method="post" action="'.$PHP_SELF.'?ok">';
		echo ' <input type="hidden" name="aid" value="'.$_GET["aid"].'">';
		echo ' Verschieben nach Zeitung: <br><br>';
		echo ' <select name="newzid">';

		$DBData = mysql_query("SELECT `id`, `name` FROM ".TABLE_ZDATA." ORDER BY `userid`");
		while ($ZData = mysql_fetch_assoc($DBData)) {
			echo '  <option value="'.$ZData["id"].'">'.$ZData["name"].'</option>';
		}
		
		echo ' </select>';
		echo ' <br><br><input type="submit" name="moveausgabe" value="Verschieben">';
		echo '</form>';
		echo '</center></body></html>';
		die;
	}

	if ((isset($_GET["ok"])) && (isset($_POST["moveausgabe"]))) {
		mysql_query("UPDATE ".TABLE_ADATA." SET `zid` = ".$_POST["newzid"]." WHERE `id` = ".$_POST["aid"]);
	}

	if (isset($_GET["read"])) {
		$AData = mysql_fetch_assoc(mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `id` = ".$_GET["aid"]));

		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?zid='.$AData["zid"].'&ausgaben&uid='.$_GET["uid"].'">[ Zurück zur Ausgaben-Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;"> '.$AData["titel"].' </span><br><br>';
		$AText = $AData["ausgabe"];
		
		include('../../dez/dez_show.inc.php');

		echo '</center></body></html>';
		die;
	}

	if (isset($_GET["delete"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?site">[ Zurück zur Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 10pt; font-weight: bold; color: #FF0000;">Zeitung mit der ID '.$_GET["zid"].' wirklich komplett löschen?</span><br><br>';
		echo '<form method="post" action="'.$PHP_SELF.'?ok">';
		echo ' <input type="hidden" name="zid" value="'.$_GET["zid"].'">';
		echo ' <input type="submit" name="delzeitung" value="Ja, Zeitung löschen">';
		echo '</form>';
		echo '</center></body></html>';
		die;
	}

	if (isset($_GET["change"])) {
		$AData = mysql_fetch_assoc(mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `id` = ".$_GET["aid"]));

		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?zid='.$AData["zid"].'&ausgaben&uid='.$_GET["uid"].'">[ Zurück zur Ausgaben-Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;">Ausgabe mit der ID '.$_GET["aid"].' bearbeiten:</span><br><br>';

		echo '<form name="a_form" method="post" action="'.$PHP_SELF.'?site">';
		echo '<input type="hidden" name="aid" value="'.$_GET["aid"].'">';
		echo '<table border="0" cellspacing="5" cellpadding="0">';
		echo ' <tr><td>Titel der Ausgabe:</td><td><input type="text" name="a_titel" value="'.$AData["titel"].'" maxlength="50" style="width: 250px;"></td></tr>';
	  echo ' <tr><td>Preis der Ausgabe:</td><td><input type="text" name="a_preis" value="'.$AData["preis"].'" maxlength="5" style="width: 50px; text-align: center;" disabled></td></tr>';
	  echo '</table>';
		echo '<br><br>';
	
	  echo '<script src="../../dez/dez_new.js" type="text/javascript"></script>';
	
	  echo '<input type="button" value="BR" onclick="bbcode(document.a_form,\'BR\',\'\')">';
	
	  echo ' <input type="button" value="CENTER" onclick="bbcode(document.a_form,\'CENTER\',\'\')">';
	  echo ' <input type="button" value="BLOCKTEXT" onclick="bbcode(document.a_form,\'BLOCKTEXT\',\'\')">';
	
	  echo ' <input type="button" value="TABLE" onclick="bbcode(document.a_form,\'TABLE\',\'\')">';
	  echo ' <input type="button" value="TR" onclick="bbcode(document.a_form,\'TR\',\'\')">';
	  echo ' <input type="button" value="TD" onclick="bbcode(document.a_form,\'TD\',\'\')">';
	
	  echo ' <input type="button" value="B" onclick="bbcode(document.a_form,\'B\',\'\')">';
	  echo ' <input type="button" value="U" onclick="bbcode(document.a_form,\'U\',\'\')">';
	  echo ' <input type="button" value="I" onclick="bbcode(document.a_form,\'I\',\'\')">';
	
	  echo ' <input type="button" value="COLOR" onclick="bbcode(document.a_form,\'COLOR\',\'\')">';
	  echo ' <input type="button" value="SIZE" onclick="bbcode(document.a_form,\'SIZE\',\'\')">';
	
	  echo ' <input type="button" value="URL" onclick="namedlink(document.a_form,\'URL\')">';
		
		echo '<br><br>';
		echo '<textarea name="a_text" cols="70" rows="20" style="width: 585px;">'.$AData["ausgabe"].'</textarea>';
		echo '<br><br>';
		echo '<input type="submit" name="savechange" value="Änderungen Speichern">';
		echo '</form><br><br><br>';

		echo '</center></body></html>';
		die;
	}
	
	if (isset($_POST["savechange"])) {
		$AText = $_POST["a_text"];
		$AText = htmlentities(trim($AText), ENT_NOQUOTES);
		$AText = stripslashes($AText);
		$AText = str_replace("'", "&#39;", $AText);
		$AText = str_replace("\"", "&#34;", $AText);

		mysql_query("UPDATE ".TABLE_ADATA." SET `titel` = '".$_POST["a_titel"]."', `ausgabe` = '".$AText."' WHERE `id` = ".$_POST["aid"]);
	}	

	if (isset($_GET["adelete"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?site">[ Zurück zur Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 10pt; font-weight: bold; color: #FF0000;">Ausgabe mit der ID '.$_GET["aid"].' wirklich löschen?</span><br><br>';
		echo '<form method="post" action="'.$PHP_SELF.'?ok">';
		echo ' <input type="hidden" name="zid" value="'.$_GET["zid"].'">';
		echo ' <input type="hidden" name="aid" value="'.$_GET["aid"].'">';
		echo ' <input type="submit" name="delausgabe" value="Ja, Ausgabe löschen">';
		echo '</form>';
		echo '</center></body></html>';
		die;
	}
	
	if ((isset($_GET["ok"])) && (isset($_POST["delzeitung"]))) {
		mysql_query("DELETE FROM ".TABLE_ZDATA." WHERE `id` = ".$_POST["zid"]);
		mysql_query("DELETE FROM ".TABLE_ADATA." WHERE `zid` = ".$_POST["zid"]);
	}

	if ((isset($_GET["ok"])) && (isset($_POST["delausgabe"]))) {
		mysql_query("DELETE FROM ".TABLE_ADATA." WHERE `id` = ".$_POST["aid"]);
		
		$AAnz = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ADATA." WHERE `zid` = ".$_POST["zid"]), 0);
		if ($AAnz == 0) { mysql_query("UPDATE ".TABLE_ZDATA." SET `aktuell` = 0 WHERE `id` = ".$_POST["zid"]); }
		else {
			$ADate = mysql_result(mysql_query("SELECT `datum` FROM ".TABLE_ADATA." WHERE `zid` = ".$_POST["zid"]." ORDER BY `datum` DESC LIMIT 1"), 0);
			mysql_query("UPDATE ".TABLE_ZDATA." SET `aktuell` = ".$ADate." WHERE `id` = ".$_POST["zid"]);
		}
	}
	
	if (isset($_GET["createhf"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?site">[ Zurück zur Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;">Hyperfunk an User mit ID '.$_GET["uid"].' senden:</span><br><br>';
		echo '<form method="post" action="'.$PHP_SELF.'?site">';
		echo ' <input type="hidden" name="uid" value="'.$_GET["uid"].'">';
		echo ' Betreff: <input type="text" name="hfbetreff" value="" style="width:538px;"><br>';
		echo ' <textarea name="hftext" cols="70" rows="20" style="width: 585px;">'."\r\n\r\n\r\n\r\nMit freundlichen Grüßen\r\nIhre DigiPaper Redaktion\r\n\r\nBitte beachten Sie, das auf diese Hyperfunk nicht geantwortet werden kann!</textarea><br>";
		echo ' <input type="submit" name="sendhf" value="HF abschicken">';
		echo '</form>';
		echo '</center></body></html>';
		die;
	}

	if (isset($_POST["sendhf"])) {
		$_POST["hftext"] = htmlspecialchars(stripslashes($_POST["hftext"]));
		$_POST["hftext"] = str_replace('\"', '&quot;', $_POST["hftext"]);
		$_POST["hftext"] = str_replace('\'', '&acute;', $_POST["hftext"]);
		$_POST["hftext"] = nl2br($_POST["hftext"]);

		$_POST["hftext"] = str_replace('script', 'schkript', $_POST["hftext"]);
		$_POST["hftext"] = str_replace('Script', 'Schkript', $_POST["hftext"]);

		mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, betreff, text) VALUES ('".$_POST["uid"]."', '0', '0', '1', 'DigiPaper-Redaktion', '".$_POST["hfbetreff"]."', '".$_POST["hftext"]."')");
		mysql_query("UPDATE de_user_data SET newtrans=1 WHERE user_id = '".$_POST["uid"]."'");
	}

	if (isset($_GET["createdprhf"])) {
		echo '<span style="font-size: 10pt; font-weight: bold;"><a href="'.$PHP_SELF.'?site">[ Zurück zur Übersicht ]</a></span><br><br>';
		echo '<span style="font-size: 12pt; font-weight: bold;">Hyperfunk an einen Spieler senden:</span><br><br>';
		echo '<form method="post" action="'.$PHP_SELF.'?site">';
		echo ' <input type="hidden" name="uid" value="'.$_GET["uid"].'">';
		echo ' Sek: <input type="text" name="hfsek" value="" style="width:50px;"> ';
		echo ' Sys: <input type="text" name="hfsys" value="" style="width:50px;"> ';
		echo ' Betreff: <input type="text" name="hfbetreff" value="" style="width:370px;"><br>';
		echo ' <textarea name="hftext" cols="70" rows="20" style="width: 585px;">'."\r\n\r\n\r\n\r\nMit freundlichen Grüßen\r\nIhre DigiPaper Redaktion\r\n\r\nBitte beachten Sie, das auf diese Hyperfunk nicht geantwortet werden kann!</textarea><br>";
		echo ' <input type="submit" name="senddprhf" value="HF abschicken">';
		echo '</form>';
		echo '</center></body></html>';
		die;
	}

	if (isset($_POST["senddprhf"])) {
		$_POST["hftext"] = htmlspecialchars(stripslashes($_POST["hftext"]));
		$_POST["hftext"] = str_replace('\"', '&quot;', $_POST["hftext"]);
		$_POST["hftext"] = str_replace('\'', '&acute;', $_POST["hftext"]);
		$_POST["hftext"] = nl2br($_POST["hftext"]);

		$_POST["hftext"] = str_replace('script', 'schkript', $_POST["hftext"]);
		$_POST["hftext"] = str_replace('Script', 'Schkript', $_POST["hftext"]);

		$UserID = mysql_result(mysql_query("SELECT `user_id` FROM `de_user_data` WHERE `sector` = ".$_POST["hfsek"]." AND `system` = ".$_POST["hfsys"]), 0);

		mysql_query("INSERT INTO de_user_hyper (empfaenger, absender, fromsec, fromsys, fromnic, betreff, text) VALUES ('".$UserID."', '0', '0', '1', 'DigiPaper-Redaktion', '".$_POST["hfbetreff"]."', '".$_POST["hftext"]."')");
		mysql_query("UPDATE de_user_data SET newtrans=1 WHERE user_id = '".$UserID."'");
	}
	
	if (isset($_GET["disallnvsz"])) {
		$DCount = 0;
		$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA);
		while ($ZData = mysql_fetch_assoc($DBData)) {
			$UserOK = mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `de_user_data` WHERE user_id = ".$ZData["userid"],$db), 0);
			if ($UserOK <= 0) {
				mysql_query("UPDATE ".TABLE_ZDATA." SET `eingestellt` = 1 WHERE `id` = ".$ZData["id"]);
				$DCount++;
			}
		}
		echo 'Es wurden <span class="sr">'.$DCount.'</span> Zeitungen eingestellt!<br><br><br>';
	}

	if (isset($_POST["delmarked"])) {
    $DCount = 0;
    foreach ($_POST as $delname => $delwert) {
      $delid = str_replace("id", "", $delname);
      if (is_numeric($delid)) {
    		mysql_query("DELETE FROM ".TABLE_ZDATA." WHERE `id` = ".$delid);
    		mysql_query("DELETE FROM ".TABLE_ADATA." WHERE `zid` = ".$delid);
    		$DCount++;
      }
		}
		echo 'Es wurden '.$DCount.' Zeitungen inkl. deren Ausgaben gelöscht!<br><br><br>';
	}
?>

<span style="font-size: 12pt; font-weight: bold;">Aktuelle Zeitungen in DigiPaper:</span><br><br>

<a href="<?php echo $PHP_SELF; ?>?createdprhf"><b>[ HF an einen Spieler senden ]</b></a> &nbsp; &nbsp; &nbsp; &nbsp;
<a href="<?php echo $PHP_SELF; ?>?disallnvsz"><b>[ Zeitungen <span class="sr">nvS</span> einstellen ]</b></a><br><br>

<form method="post" action="<?php echo $PHP_SELF; ?>?site">

<input type="submit" name="refresh" value="Seite aktualisieren"><br><br>

<table border="1" cellspacing="0" cellpadding="5">
 <tr><td class="bt" style="text-align: left;"><u>Zeitung</u> [<a href="<?php echo $PHP_SELF; ?>?order=1">S</a>]</td><td class="ubt">Status</td><td>&nbsp;</td><td class="bt"><u>Herausgeber</u> [<a href="<?php echo $PHP_SELF; ?>?order=2">S</a>]</td><td>&nbsp;</td><td class="ubt">Logo</td><td class="ubt">Ausgaben</td><td class="bt"><u>lA</u>  [<a href="<?php echo $PHP_SELF; ?>?order=3">S</a>]</td><td>&nbsp;</td></tr>
<?php
	if (!isset($_GET["order"])) { $_GET["order"] = 2; }
	switch ($_GET["order"]) {
		case 1:
			$TOrder = "`id`";
			break;
		case 2:
			$TOrder = "`userid`";
			break;
		case 3:
			$TOrder = "`aktuell`";
			break;
	}

	$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." ORDER BY ".$TOrder);
	while ($ZData = mysql_fetch_assoc($DBData)) {
		$AAnz = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData["id"]), 0);
		$AAnzNF = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData["id"]." AND `frei` = 0"), 0);

		$UserOK = mysql_result(mysql_query("SELECT COUNT(`user_id`) FROM `de_user_data` WHERE user_id = ".$ZData["userid"],$db), 0);

		echo ' <tr><td><nobr>'.$ZData["name"].'</nobr></td>';
		echo '  <td align="center"><nobr>';
		if ($ZData["eingestellt"] == 1) { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&zenable">eingestellt</a>]'; }
		else {
			if ($AAnz == 0) { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&zdisable"><span class="sr">aktiv</span></a>]'; }
			else {
				if (($AAnz - $AAnzNF) != 0) { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&zdisable"><span class="sg">aktiv</span></a>]'; }
				 else { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&zdisable"><span class="sy">aktiv</span></a>]'; }
			}
		}
		echo '  <td><nobr>[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&delete">Löschen</a>]</nobr></td>';
		if ($UserOK > 0) { echo '  <td><nobr>'.$ZData["nick"].'</nobr></td>'; }
			else { echo '  <td><span class="sr"><nobr>'.$ZData["nick"].'</nobr></span></td>'; }
		echo '  <td>[<a href="'.$PHP_SELF.'?uid='.$ZData["userid"].'&createhf">HF</a>]</td>';
		echo '  <td><nobr>';
		if ($ZData["logo"] != "") {
			echo '[<a href="'.$ZData["logo"].'" target="_blank">Logo</a>] &nbsp;';
			if ($ZData["logofrei"] == 1) { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&ldisable"><span class="sg">aktiviert</span></a>]'; }
			 else  { echo '[<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&lenable"><span class="sr">gesperrt</span></a>]'; }
		}
		else { echo '&nbsp;'; }
		echo '  </nobr></td>';
		echo '  <td>';
		if ($AAnz > 0) {
			echo '<nobr>'.$AAnz.' | ';
			if ((($AAnz - $AAnzNF) != 0) && ($AAnzNF > 0)) { echo '<span class="sy">'.$AAnzNF.' offen</span>'; }
			 else { echo $AAnzNF.' offen'; }
			echo ' [<a href="'.$PHP_SELF.'?zid='.$ZData["id"].'&ausgaben&uid='.$ZData["userid"].'">Anzeigen</a>]';
		}
		else { echo '&nbsp;'; }
		echo '</td>';
		if ($ZData["aktuell"] == 0) { echo '  <td>&nbsp;</td>'; }
			else { echo '  <td>'.date("d.m.y", $ZData["aktuell"]).'</td>'; }
    echo '<td><input type="checkbox" name="id'.$ZData["id"].'" value="del"></td>';
    echo ' </tr>';
	}
?>
</table>

<br>
<input type="submit" name="delmarked" value="Markierte Zeitungen löschen">

</form>

<br><br><br>
&copy;2005 DJ16EL, René Schädlich

</center>

</body>
</html>