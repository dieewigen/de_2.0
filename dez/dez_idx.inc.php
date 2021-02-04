<?php
         include 'inc/lang/'.$sv_server_lang.'_dezidxinc.lang.php';
?>

<center>
  <br>

	<?php
		if (count($_POST) > 0) {
			$ID = -1;
			foreach($_POST as $var => $val) {
				if (substr($var, 0, 3) == 'buy') {
					$ID = str_replace('buy', '', $var);
					$DBData = mysql_query("SELECT `preis`, `gekauft`, `zid` FROM ".TABLE_ADATA." WHERE `id` = ".$ID);
					$AData = mysql_fetch_assoc($DBData);

					if ((substr_count($AData["gekauft"], '#'.$ums_user_id.'#') == 0)) {
						if ($restyp01 >= $AData['preis']) {
							$Tmp = $AData['gekauft'];
							$Tmp = $Tmp."#".$ums_user_id."#";
							mysql_query("UPDATE ".TABLE_ADATA." SET `gekauft` = '".$Tmp."' WHERE `id` = ".$ID);
							mysql_query("UPDATE `de_user_data` SET `restyp01` = `restyp01` - ".$AData['preis']." WHERE `user_id` = ".$ums_user_id);

							$ZStat = mysql_result(mysql_query("SELECT `eingestellt` FROM ".TABLE_ZDATA." WHERE `id` = ".$AData['zid']), 0);
							if ($ZStat != 1) {
								$ZUID = mysql_result(mysql_query("SELECT `userid` FROM ".TABLE_ZDATA." WHERE `id` = ".$AData['zid']), 0);
								mysql_query("UPDATE `de_user_data` SET `restyp01` = `restyp01` + ".$AData['preis']." WHERE `user_id` = ".$ZUID);
							}

							echo '<br>'.$dezidxinc_lang[msg_1].'<br><br><br>';
						}
						else { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_2].'</span><br><br><br>'; }
					}
					else { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_3].'</span><br><br><br>'; }
				}

				if (substr($var, 0, 4) == 'read') {
					$ID = str_replace('read', '', $var);
					$AData = mysql_fetch_assoc(mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `id` = ".$ID));
					$ZUID = mysql_result(mysql_query("SELECT `userid` FROM ".TABLE_ZDATA." WHERE `id` = ".$AData["zid"]), 0);
					if (($AData["frei"] == 0) && ($ums_user_id != $ZUID)) {
						echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_4].'</span><br><br><br>';
					}
					else {
						if ((substr_count($AData["gekauft"], '#'.$ums_user_id.'#') == 0) && ($ums_user_id != $ZUID) && ($AData["preis"] > 0)) {
							echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_5].'</span><br><br><br>';
						}
						else {
							echo '<span style="font-size: 14pt; font-weight: bold;">'.$dezidxinc_lang[title_1].'</span><br>';
							echo $dezidxinc_lang[title_2].'<br><br>';
							
							echo '<table><tr><td>';
							echo '<a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
							echo '</td><td>';
							echo '<a href="'.$PHP_SELF.'?site=my" class="btn">'.$dezindex_lang["verwaltung"].'</a>';
							echo '</td></tr></table>';
							echo '<br><br>';
							
							echo '<br><br><br>';
							echo '<span style="font-size: 12pt; font-weight: bold;"> '.$AData["titel"].' </span><br><br>';

							$AText = $AData["ausgabe"];
							include('dez/dez_show.inc.php');

							echo '<br><br>';
							include('dez/dez_footer.inc.php');
							die;
						}
					}
				}

				if (substr($var, 0, 6) == 'setabo') {
					$ID = str_replace('setabo', '', $var);

					$DBData = mysql_query("SELECT `name`, `abonenten` FROM ".TABLE_ZDATA." WHERE `id` = ".$ID);
					$ZData = mysql_fetch_assoc($DBData);

					if ((substr_count($ZData["abonenten"], '#'.$ums_user_id.'#') == 0)) {
						$ZData["abonenten"] = $ZData["abonenten"].'#'.$ums_user_id.'#';
						mysql_query("UPDATE ".TABLE_ZDATA." SET `abonenten` = '".$ZData["abonenten"]."' WHERE `id` = ".$ID);
						echo '<br>'.$dezidxinc_lang[msg_6_1].' "'.$ZData["name"].'" '.$dezidxinc_lang[msg_6_2].'<br><br><br>';
					}
					else {
						echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_4].'</span><br><br><br>';
					}
				}

				if (substr($var, 0, 6) == 'delabo') {
					$ID = str_replace('delabo', '', $var);

					$DBData = mysql_query("SELECT `name`, `abonenten` FROM ".TABLE_ZDATA." WHERE `id` = ".$ID);
					$ZData = mysql_fetch_assoc($DBData);

					if ((substr_count($ZData["abonenten"], '#'.$ums_user_id.'#') != 0)) {
						$ZData["abonenten"] = str_replace('#'.$ums_user_id.'#', '', $ZData["abonenten"]);
						mysql_query("UPDATE ".TABLE_ZDATA." SET `abonenten` = '".$ZData["abonenten"]."' WHERE `id` = ".$ID);
						echo '<br>'.$dezidxinc_lang[msg_7_1].' "'.$ZData["name"].'" '.$dezidxinc_lang[msg_7_2].'<br><br><br>';
					}
					else {
						echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_4].'</span><br><br><br>';
					}
				}
			}
		}
	?>

	<span style="font-size: 14pt; font-weight: bold;"><?=$dezidxinc_lang[title_2_1]?></span><br>
	<?=$dezidxinc_lang[title_2_2]?><br><br>

	<?php echo '<a href="'.$PHP_SELF.'?site=my" class="btn">'.$dezindex_lang["verwaltung"].'</a>'; ?>
	<br><br><br>

	<span style="font-size: 10pt; font-weight: bold;"><?=$dezidxinc_lang[aktangebot]?></span><br><br>

	<?php
		if (isset($_POST["z_kat"])) { $AZKat = $_POST["z_kat"]; };
		if (isset($_GET["z_kat"])) { $AZKat = $_GET["z_kat"]; };

		if (isset($_GET["sc"])) { $StartC = $_GET["sc"]; } else { $StartC = 0; }

		echo '<form method="post" action="'.$PHP_SELF.'?site&zk='.$AZKat.'&as='.$StartC.'"><select name="z_kat">';
		for ($i = 0; $i < count($DEZ_ZKat); $i++) {
			echo '<option value="'.$i.'"';
			if (isset($_POST["z_kat"])) {
				if ($_POST["z_kat"] == $i) {
					echo ' selected';
					$AZKat = $_POST["z_kat"];
				}
			}
			elseif (isset($_GET["z_kat"])) {
				if ($_GET["z_kat"] == $i) {
					echo ' selected';
					$AZKat = $_GET["z_kat"];
				}
			}
			else {
				if ($i == 0) { echo ' selected'; }
			}
			echo '>'.$DEZ_ZKat[$i].'</option>';
		}
	?>
	</select> <input type="submit" name="setkat" value="<?=$dezidxinc_lang[okbtn]?>"><br><br><br>

	<?php
		$KatSelect = "";
		if (isset($_POST["z_kat"])) { if (($_POST["z_kat"] != 0) && (is_numeric($_POST["z_kat"]))) { $KatSelect = " AND `kategorie` = ".$_POST["z_kat"]; } }
		if (isset($_GET["z_kat"])) { if (($_GET["z_kat"] != 0) && (is_numeric($_GET["z_kat"]))) { $KatSelect = " AND `kategorie` = ".$_GET["z_kat"]; } }

		$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `aktuell` <> ''".$KatSelect." ORDER BY `aktuell` DESC");
		$ACount = 0;
		if (mysql_num_rows($DBData)) {
			while ($ZData = mysql_fetch_assoc($DBData)) {
				$ADBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData['id']." AND `frei` = 1 ORDER BY `datum` DESC LIMIT 1");
				if (mysql_num_rows($ADBData)) { $AData = mysql_fetch_assoc($ADBData); }
				 else { $AData = array(); }

				if ($AData["frei"] == 1) {
					$ACount++;

					if (($ACount > $StartC) && ($ACount <= ($StartC + $DEZ_ZPerPage))) {
						$AnzBuy = substr_count($AData["gekauft"], '#') / 2;
						$AnzAbo = substr_count($ZData['abonenten'], '#') / 2;

						$AZData = mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData['id']." AND `frei` = 1");
						if (mysql_num_rows($AZData)) { $AnzAusgaben = mysql_result($AZData, 0); }

						if ($ZData['eingestellt'] == 0) { $ZNick = '<a href="'.$PHP_SELF.'?site=hf&zid='.$ZData['id'].'" class="link">'.$ZData['nick'].'</a>'; }
						 else { $ZNick = '<span class="ccr">Eingestellt</span>'; }

						if (($ZData['logo'] != "") && ($ZData['logofrei'] == 1)) { $ZLogo = '<img src="'.$ZData['logo'].'" alt="" style="margin: 5px;">'; }
						 else { $ZLogo = '<br>'.$dezidxinc_lang[msg_8].'<br><br>'; }

						echo '<table border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #BBBBBB;">';
						echo '<tr><td align="center">'.$ZLogo.'</td></tr>';
						echo '<td align="center">';
						echo ' <table border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; margin: 5px; width: 580px;">';
						echo '  <tr><td class="cell1" width="110px">'.$dezidxinc_lang[zeitung].':</td><td class="cell" width="280px"><b>'.$ZData['name'].'</b></td>';
						echo '      <td class="cell1" width="110px">'.$dezidxinc_lang[ausgaben].':</td><td class="cell" width="100px">'.$AnzAusgaben.' &nbsp; (<a href="'.$PHP_SELF.'?site=archiv&zid='.$ZData['id'].'" class="link">Archiv</a>)</td>';
						echo '  <tr><td class="cell1">'.$dezidxinc_lang[herausgeber].':</td><td class="cell">'.$ZNick.'</td>';
						echo '      <td class="cell1"><nobr>'.$dezidxinc_lang[aktausgabe].':</nobr></td><td class="cell">'.date('d.m.Y' ,$ZData['aktuell']).'</td></tr>';
						echo '  <tr><td class="cell1">'.$dezidxinc_lang[titel].':</td><td class="cell">'.$AData['titel'].'</td>';
						echo '      <td class="cell1">'.$dezidxinc_lang[preis].':</td><td class="cell">'.number_format($AData['preis'], 0, '', '.').' M</td></tr>';
						echo '  <tr><td class="cell1">'.$dezidxinc_lang[verkauft].':</td><td class="cell">'.$AnzBuy.'</td>';
						echo '      <td class="cell1">'.$dezidxinc_lang[abonnenten].':</td><td class="cell">'.$AnzAbo.'</td></tr>';
						echo '  <tr>';
						echo '      <th colspan="4" class="cell">&nbsp;';
						if ((substr_count($AData["gekauft"], '#'.$ums_user_id.'#') == 0) && ($ums_user_id != $ZData["userid"])) {
							echo '   <input type="submit" name="buy'.$AData['id'].'" value="'.$dezidxinc_lang[ausgabekaufen].'">'; }
						else { echo '   <input type="submit" name="read'.$AData['id'].'" value="'.$dezidxinc_lang[ausgabelesen].'">'; }
						if ($ums_user_id != $ZData["userid"]) {
							if (substr_count($ZData["abonenten"], '#'.$ums_user_id.'#') == 0) {
								echo '   &nbsp; &nbsp; <input type="submit" name="setabo'.$ZData['id'].'" value="'.$dezidxinc_lang[btnabo].'">'; }
							//else { echo '   &nbsp; &nbsp; <input type="submit" name="delabo'.$ZData['id'].'" value="Abonnement Kündigen">'; }
						}
						echo '  </th></tr>';
						echo ' </table>';
						echo '</td></tr>';
						echo '</table><br><br>';
					}
				}
			}
		}
		if ($ACount == 0) { echo '<br><br><span style="color: #FF2828; font-weight: bold;">'.$dezidxinc_lang[msg_9].'</span><br><br>'; }
		else {
			echo '<div style="width: 580px; text-align: right;">Seite: ';
			for ($i = 1; $i <= ceil($ACount / $DEZ_ZPerPage); $i++) {
				$SetSC = ($i * $DEZ_ZPerPage - $DEZ_ZPerPage);
				if ($SetSC == $StartC) {
					if ($i == 1) { echo '[ '.$i.' ]&nbsp;'; }
					 else { echo '[ '.$i.' ]&nbsp;'; }
				}
				else {
					if ($i == 1) { echo '[<a href="'.$PHP_SELF.'?site&z_kat='.$AZKat.'"><b> '.$i.' </b></a>]&nbsp;'; }
					 else { echo '[<a href="'.$PHP_SELF.'?site&sc='.$SetSC.'&z_kat='.$AZKat.'"><b> '.$i.' </b></a>]&nbsp;'; }
				}
			}
			echo '</div>';
		}
	?>
	</form>

	<br><br>
</center>