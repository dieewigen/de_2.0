<?PHP
include 'inc/lang/'.$sv_server_lang.'_dezmyinc.lang.php';
?>

<center>
  <br>
	<span style="font-size: 14pt; font-weight: bold;"><?=$dezmyinc_lang[title_1]?></span><br>
	<?=$dezmyinc_lang[title_2]?><br><br>

	<?php
		$ZID = 0;
		if (isset($_GET['zid'])) { $ZID = $_GET['zid']; }
		if (isset($_POST['zid'])) { $ZID = $_POST['zid']; }

		echo '<table><tr><td>';
		echo '<a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
		echo '</td><td>';
		echo '<a href="'.$PHP_SELF.'?site=my" class="btn">'.$dezindex_lang["verwaltung"].'</a>';
		echo '</td><td>';
		echo '<a href="'.$PHP_SELF.'?site=create" class="btn">'.$dezindex_lang["erstellen"].'</a>';
		echo '</td></tr></table>';
		echo '<br><br>';

		if (isset($_GET['einstellen'])) {
			echo '<form method="post" action="'.$PHP_SELF.'?site=my">';
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezmyinc_lang[zeitungkomplett].'</span><br>';
			echo '<span style="font-size: 8pt;">'.$dezmyinc_lang[msg_1].'</span><br><br>';
			echo '<input type="hidden" name="zid" value="'.$ZID.'">';
			echo '<input type="submit" name="z_einstellen" value="'.$dezmyinc_lang[btneinstellen].'">';
			echo '</form><br><br>';
		}

		if (isset($_POST['z_einstellen'])) {
			$AZData = mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ADATA." WHERE `zid` = ".$ZID);
			if (mysql_result($AZData, 0) > 0) {
				mysql_query("UPDATE ".TABLE_ZDATA." SET `eingestellt` = 1 WHERE `userid` = ".$ums_user_id." AND `id` = ".$ZID);
				echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezmyinc_lang[msg_2].'</span><br><br><br>';
			}
			else {
				mysql_query("DELETE FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `id` = ".$ZID);
				echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezmyinc_lang[msg_3].'</span><br><br><br>';
			}
			$ZID = 0;
		}

		if (isset($_POST['zc_save'])) {
			if (trim($_POST['zc_name'] != "")) {
				$ZExist = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ZDATA." WHERE `name` = '".$_POST['zc_name']."'"), 0);

				if ($ZExist != 0) {
					$VZID = mysql_result(mysql_query("SELECT `id` FROM ".TABLE_ZDATA." WHERE `name` = '".$_POST['zc_name']."'"), 0);
					if ($VZID == $ZID) { $ZExist = 0; } else { echo '<br><span style="color: #FF2828;">'.$dezmyinc_lang[msg_4].'</span><br><br><br>'; }
				}
				if ($ZExist == 0) {
					if (trim($_POST['zc_nick'] != "")) {
						if ($_POST['zc_logo'] == "http://") { $_POST['zc_logo'] = ""; }

						$_POST['zc_name'] = htmlentities(trim($_POST['zc_name']), ENT_NOQUOTES);
						$_POST['zc_name'] = stripslashes($_POST['zc_name']);
						$_POST['zc_name'] = str_replace("'", "", $_POST['zc_name']);
						$_POST['zc_name'] = str_replace("\"", "", $_POST['zc_name']);

						$_POST['zc_nick'] = htmlentities(trim($_POST['zc_nick']), ENT_NOQUOTES);
						$_POST['zc_nick'] = stripslashes($_POST['zc_nick']);
						$_POST['zc_nick'] = str_replace("'", "", $_POST['zc_nick']);
						$_POST['zc_nick'] = str_replace("\"", "", $_POST['zc_nick']);

						mysql_query("UPDATE ".TABLE_ZDATA." SET `name` = '".$_POST['zc_name']."', `nick` = '".$_POST['zc_nick']."', `logo` = '".$_POST['zc_logo']."', `logofrei` = 0 WHERE `id` = ".$ZID);
						echo '<br>'.$dezmyinc_lang[msg_5].'<br><br><br>';
					}
					else {
						echo '<br><span style="color: #FF2828;">'.$dezmyinc_lang[msg_6].'</span><br><br><br>';
					}
				}
			}
			else {
				echo '<br><span style="color: #FF2828;">'.$dezmyinc_lang[msg_7].'</span><br><br><br>';
			}
		}

		if ($ZID == 0) {
			echo '<br><br>';
			echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
			echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;">'.$dezmyinc_lang[eigenezeitungen].'</span></td>';
			$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 0");
			if (mysql_num_rows($DBData)) {
				echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_ez\', \'table\', \'inline\')" style="text-decoration: none;">'.$dezmyinc_lang[anzeigen].'...</a></td></tr></table><br>';

				echo '<table id="dez_ez" border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; margin: 5px; width: 600px; display: none;">';
				echo ' <tr><td class="cell1"><b>'.$dezmyinc_lang[zeitung].'</b></td><td class="cell1" width="110px"><b>'.$dezmyinc_lang[aausgabe].'</b></td><td class="cell1" width="100px">&nbsp;</td><td class="cell1" width="96px">&nbsp;</td></tr>';

				while ($ZData = mysql_fetch_assoc($DBData)) {
					$ADBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData['id']." ORDER BY `datum` DESC LIMIT 1");
					if (mysql_num_rows($ADBData)) { $AData = mysql_fetch_assoc($ADBData); }
					 else { $AData = array(); }

					$AStatus = "";
					if ($ZData['aktuell'] == 0) { $AAusgabe = '<span style="color: #FF2828;">'.$dezmyinc_lang[msg_8].'</span>'; }
					else {
						$AAusgabe = date('d.m.Y' ,$ZData['aktuell']);

						if ($AData["frei"] == 0) { $AStatus = '<span style="color: #FF2828;">'.$dezmyinc_lang[msg_9].'</span>'; }
						 else { $AStatus = 'freigegeben'; }
					}

					echo ' <tr><td class="cell">'.$ZData['name'].'</td><td class="cell">'.$AAusgabe.'</td><td class="cell">'.$AStatus.'</td><td align="center"><a href="'.$PHP_SELF.'?site=my&zid='.$ZData['id'].'" class="btn">'.$dezindex_lang["select"].'</a></td></tr>';
				}
				echo '</table><br><br>';
			}
			else {
				echo '  <td align="right"><span style="color: #FF2828; font-size: 8pt;;">'.$dezmyinc_lang[msg_10].'</span></td></tr></table><br><br><br>';
				//echo '  <td align="right">&nbsp;</td></tr></table><br>';
				//echo '<div style="width: 600px; text-align: right;"><span style="color: #FF2828; font-weight: bold;">Sie besitzen keine eigene Zeitung!</span></div><br>';
			}
		}
		else {
			echo '<table><tr><td>';
			echo '<a href="'.$PHP_SELF.'?site=new&zid='.$ZID.'" class="btn">'.$dezindex_lang["neueausgabe"].'</a>';
			echo '</td><td>';
			echo '<a href="'.$PHP_SELF.'?site=my&zid='.$ZID.'&einstellen" class="btn">'.$dezindex_lang["einstellen"].'</a>';
			echo '</td></tr></table>';
			echo '<br><br>';

			$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 0 AND `id` = ".$ZID);
			if (mysql_num_rows($DBData)) {
				$ZData = mysql_fetch_assoc($DBData);

				if (($ZData["logo"] != "") && ($ZData["logofrei"] == 0)) { $LogoInfo = '&nbsp; &nbsp; &nbsp;<span style="color: #FF2828;">Nicht freigegeben!</span>'; } else { $LogoInfo = '&nbsp;'; }

			echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
			echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;">'.$dezmyinc_lang[einstellungen].'</span></td>';
			echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_ze\', \'inline\', \'inline\')" style="text-decoration: none;">'.$dezmyinc_lang[anzeigen].'...</a></td></tr></table><br>';
			echo '<div id="dez_ze" style="display: none;">';
	?>

	<br><form method="post" action="<?php echo $PHP_SELF."?site=my&zid=".$ZID; ?>">
		<input type="hidden" name="zid" value="<?php echo $ZID; ?>">
		<table border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px;">
			<tr>
				<td class="cell1" width="300px"><b><?=$dezmyinc_lang[zeitungsname]?>:</b></td>
				<td class="cell1" width="300px">
				 <table border="0" cellspacing="0" cellpadding="0" width="290px">
				  <tr><td align="left" class="cell1"><b><?=$dezmyinc_lang[zeitungslogo]?>:</b></td><td align="right" class="cell1"><b><?php echo $LogoInfo; ?></b></td></tr>
				 </table>
				</td>
			</tr>
			<tr>
				<td class="cell" align="center"><input type="text" name="zc_name" value="<?php echo $ZData["name"] ?>" style="width: 285px; padding: 2px;" maxlength="50"></td>
				<td class="cell" align="center"><input type="text" name="zc_logo" value="<?php echo $ZData["logo"] ?>" style="width: 285px; padding: 2px;" maxlength="150"></td>
			</tr>
			<tr>
				<td class="cell1"><b><?=$dezmyinc_lang[herausgeber]?>:</b></td>
				<td class="cell1">&nbsp;</td>
			</tr>
			<tr>
				<td class="cell" align="center"><input type="text" name="zc_nick" value="<?php echo $ZData["nick"]; ?>" style="width: 285px; padding: 2px;" maxlength="50"></td>
				<td class="cell" align="center"><input type="submit" name="zc_save" value="<?=$dezmyinc_lang[savechanges]?>"></td>
			</tr>
		</table>
	</form>

	<?=$dezmyinc_lang[logodaten]?>:<br><br>
	<table border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px;">
	 <tr>
	 	<td class="cell1"><?=$dezmyinc_lang[groesse]?>:</td><td class="cell">600px * 80px</td>
	 	<td class="cell1"><?=$dezmyinc_lang[typ]?>:</td><td class="cell">gif / jpg / png</td>
	 	<td class="cell1"><?=$dezmyinc_lang[art_1]?>:</td><td class="cell"><?=$dezmyinc_lang[art_2]?></td>
	 </tr>
	</table>

	<?php
				echo '</div><br><br>';

				echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
				echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;">'.$dezmyinc_lang[ausgabenderzeitung].'</span></td>';

				if ($ZData['aktuell'] == 0) {
					echo '  <td align="right"><span style="color: #FF2828; font-size: 8pt;">'.$dezmyinc_lang[msg_11].'</span></td></tr></table><br><br><br>';
					//echo '  <td>&nbsp;</td></tr></table><br>';
					//echo '<br><span style="color: #FF2828;">Zu dieser Zeitung gibt es noch keine Ausgaben!</span>';
				}
				else {
					echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_za\', \'inline\', \'inline\')" style="text-decoration: none;">'.$dezmyinc_lang[anzeigen].'...</a></td></tr></table><br>';

					echo '<div id="dez_za" style="display: none;">';
					echo '<br><form method="post" action="'.$PHP_SELF.'?site">';
					$ADBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData['id']." ORDER BY `datum` DESC");
					if (mysql_num_rows($ADBData)) {
						echo '<table border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px;">';
						echo ' <tr><td class="cell1" align="center"><b><u>'.$dezmyinc_lang[ausgabe].'</u></b></td><td class="cell" align="center"><b><u>'.$dezmyinc_lang[titel].'</u></b></td><td class="cell1" align="center"><b><u>'.$dezmyinc_lang[preis].'</u></b></td><td class="cell" align="center"><b><u>'.$dezmyinc_lang[verkauft].'</u></b></td><td class="cell">&nbsp;</td></tr>';
						while ($AData = mysql_fetch_assoc($ADBData)) {
							$AnzBuy = substr_count($AData["gekauft"], '#') / 2;

							if ($AData["frei"] == 1) { $ADatumFrei = date('d.m.Y' ,$AData['datum']); }
							 else  { $ADatumFrei = '<span style="color: #FF2828;">'.date('d.m.Y' ,$AData['datum']).'</span>'; }

							echo ' <tr><td class="cell1" align="center" width="70px;">'.$ADatumFrei.'</td>';
							echo '     <td class="cell" align="center">'.$AData["titel"].'</td>';
							echo '     <td class="cell1" align="center" width="70px;">'.number_format($AData['preis'], 0, '', '.').' M</td>';
							echo '     <td class="cell" align="center" width="70px;">'.$AnzBuy.'</td>';
							echo '     <td class="cell" align="center" width="55px;"><input type="submit" name="read'.$AData['id'].'" value="'.$dezmyinc_lang[lesen].'"></td></tr>';
						}
						echo '</table>';
						echo '</form>';
						echo '</div>';
					}
				}
			}
			else {
				echo '<br><br><span style="color: #FF2828; font-weight: bold;">'.$dezmyinc_lang[msg_12].'</span><br><br>';
			}
		}

		if ($ZID == 0) {
			$KAnz = 0;
			$PKAnz = 0;
			$KZAnz = 0;

			// *******************************
			// **** Gekaufte Zeitungen
			// *******************************

			echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
			echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;">'.$dezmyinc_lang[gekauftezeitungen].'</span></td>';
			$DBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `gekauft` LIKE '%#".$ums_user_id."#%' AND `frei` = 1 ORDER BY `zid` ASC, `datum` DESC");
			if (mysql_num_rows($DBData)) {
				echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_gz\', \'table\', \'inline\')" style="text-decoration: none;">'.$dezmyinc_lang[anzeigen].'...</a></td></tr></table><br>';

				echo '<form method="post" action="'.$PHP_SELF.'?site">';
				echo '<table id="dez_gz" border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px; display: none;">';

				$AZID = 0;
				while ($AData = mysql_fetch_assoc($DBData)) {
					$KAnz++;
					$PKAnz = $PKAnz + $AData["preis"];

					if ($AZID != $AData["zid"]) {
						$KZAnz++;
						$AZID = $AData["zid"];
						$ZName = mysql_result(mysql_query("SELECT `name` FROM ".TABLE_ZDATA." WHERE `id` = ".$AData["zid"]), 0);
						echo ' <tr><td class="cell">&nbsp;</td><td align="center" class="cell1"><b>'.$ZName.'</b></td><td class="cell">&nbsp;</td><td class="cell">&nbsp;</td><tr>';
					}

					echo ' <tr><td class="cell1" align="center" width="70px;">'.date('d.m.Y' ,$AData['datum']).'</td>';
					echo '     <td class="cell" align="center">'.$AData["titel"].'</td>';
					echo '     <td class="cell1" align="center" width="70px;">'.number_format($AData['preis'], 0, '', '.').' M</td>';
					echo '     <td class="cell" align="center" width="55px;"><input type="submit" name="read'.$AData['id'].'" value="'.$dezmyinc_lang[lesen].'"></td></tr>';
				}

				echo '</table><br>';
				echo '</form>';

			}
			else {
				echo '  <td align="right"><span style="color: #FF2828; font-size: 8pt;">'.$dezmyinc_lang[msg_13].'</span></td></tr></table><br><br><br>';
				//echo '  <td align="right">&nbsp;</td></tr></table><br>';
				//echo '<div style="width: 600px; text-align: right;"><span style="color: #FF2828;">Es wurden noch keine Zeitungen gekauft!</span></div><br>';
			}

			// *******************************
			// **** Abonnierte Zeitungen
			// *******************************

			echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
			echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;">'.$dezmyinc_lang[zeitungsabos].'</span></td>';
			$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `abonenten` LIKE '%#".$ums_user_id."#%' ORDER BY `id` ASC");
			if (mysql_num_rows($DBData)) {
				echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_az\', \'table\', \'inline\')" style="text-decoration: none;">'.$dezmyinc_lang[anzeigen].'...</a></td></tr></table><br>';

				echo '<form method="post" action="'.$PHP_SELF.'?site">';
				echo '<table id="dez_az" border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px; display: none;">';
				echo ' <tr><td class="cell1"><b>'.$dezmyinc_lang[zeitung].'</b></td><td class="cell1"><b>'.$dezmyinc_lang[herausgeber_2].'</b></td><td class="cell1">&nbsp;</td><tr>';

				while ($ZData = mysql_fetch_assoc($DBData)) {
					echo ' <tr><td class="cell" width="250px;">'.$ZData["name"].'</td>';
					echo '     <td class="cell" width="150px;">'.$ZData["nick"].'</td>';
					echo '     <td class="cell" align="center" width="55px;"><input type="submit" name="delabo'.$ZData['id'].'" value="'.$dezmyinc_lang[abokuendigen].'"></td></tr>';
				}

				echo '</table><br>';
				echo '</form>';
			}
			else {
				echo '  <td align="right"><span style="color: #FF2828; font-size: 8pt;">'.$dezmyinc_lang[msg_14].'</span></td></tr></table><br><br><br>';
				//echo '  <td align="right">&nbsp;</td></tr></table><br>';
				//echo '<div style="width: 600px; text-align: right;"><span style="color: #FF2828;">Es wurden noch keine Zeitungen abonniert!</span></div><br>';
			}

			// *******************************
			// **** Statistik
			// *******************************

			echo '<table border="0" cellspcing="0" cellpadding="0" style="width: 600px;">';
			echo ' <tr><td align="left"><span style="font-size: 12pt; font-weight: bold;"> '.$dezmyinc_lang[statistik].': </span></td>';

			$ZAnz = 0;
			$EAnz = 0;
			$AAnz = 0;
			$NFAnz = 0;
			$VAnz = 0;
			$SPreis = 0;

			$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 0");
			if (mysql_num_rows($DBData)) {
				while ($ZData = mysql_fetch_assoc($DBData)) {
					$ZAnz++;
					if ($ZData["eingestellt"] == 1) { $EAnz++; }

					$ADBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$ZData['id']);
					if (mysql_num_rows($ADBData)) {
						while ($AData = mysql_fetch_assoc($ADBData)) {
							$AAnz++;
							if ($AData["frei"] == 0) { $NFAnz++; }
							$SPreis = $SPreis + ($AData["preis"] * (substr_count($AData["gekauft"], '#') / 2));
							$VAnz = $VAnz + (substr_count($AData["gekauft"], '#') / 2);
						}
					}
				}
			}

			if (($AAnz != 0) || ($KAnz != 0)) {
				echo '  <td align="right" style="border-bottom: 1px solid; width: 150px;"><a href="javascript:sov(\'dez_zs\', \'table\', \'inline\')" style="text-decoration: none;">Anzeigen...</a></td></tr></table><br>';

				echo '<table id="dez_zs" border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px; display: none;">';
				if ($AAnz != 0) {
					echo ' <tr><td class="cell1" width="140px">'.$dezmyinc_lang[eigenezeitungen].'</td><td class="cell" width="160px">'.$ZAnz.' &nbsp; ('.$EAnz.' '.$dezmyinc_lang[eingestellt].')</td>';
					echo '     <td class="cell1" width="140px">'.$dezmyinc_lang[eigeneausgaben].':</td><td class="cell" width="160px">'.$AAnz.' &nbsp; ('.$NFAnz.' '.$dezmyinc_lang[nichtfreigegeben].')</td></tr>';
					echo ' <tr><td class="cell1" width="140px">'.$dezmyinc_lang[verkaufteausgaben].':</td><td class="cell" width="160px">'.$VAnz.'</td>';
					echo '     <td class="cell1" width="140px">&oslash; '.$dezmyinc_lang[verkauftproausgabe].':</td><td class="cell" width="160px">'.round($VAnz / $AAnz, 1).' '.$dezmyinc_lang[kurz_multiplex].'</td></tr>';
					echo ' <tr><td class="cell1" width="140px">'.$dezmyinc_lang[einnahmengesamt].':</td><td class="cell" width="160px">'.$SPreis.' </td>';
					echo '     <td class="cell1" width="140px">&oslash; '.$dezmyinc_lang[einnahmenproausgabe].':</td><td class="cell" width="160px">'.round($SPreis / $AAnz, 1).' '.$dezmyinc_lang[kurz_multiplex].'</td></tr>';
				}

				if ($KAnz != 0) {
					if ($AAnz != 0) { echo ' <tr><th colspan="4" class="cell">&nbsp;</th></tr>'; }
					echo ' <tr><td class="cell1" width="140px">'.$dezmyinc_lang[gekaufteausgaben].':</td><td class="cell" width="160px">'.$KAnz.'</td>';
					echo '     <td class="cell1" width="140px">'.$dezmyinc_lang[verschiedenerzeitungen].':</td><td class="cell" width="160px">'.$KZAnz.'</td></tr>';
					echo ' <tr><td class="cell1" width="140px">'.$dezmyinc_lang[kostengesamt].':</td><td class="cell" width="160px">'.$PKAnz.' '.$dezmyinc_lang[kurz_multiplex].'</td>';
					echo '     <td class="cell1" width="140px">&oslash; '.$dezmyinc_lang[kostenproausgabe].':</td><td class="cell" width="160px">'.round($PKAnz / $KAnz, 1).' '.$dezmyinc_lang[kurz_multiplex].'</td></tr>';
				}
				echo '</table>';
			}
			else {
				echo '  <td align="right"><span style="color: #FF2828; font-size: 8pt;">'.$dezmyinc_lang[msg_15].'</span></td></tr></table><br><br><br>';
				//echo '  <td align="right">&nbsp;</td></tr></table><br>';
				//echo '<div style="width: 600px; text-align: right;"><span style="color: #FF2828;">Es ist keine Statistik verfügbar!</span></div>';
			}
		}
	?>

	<br><br>
</center>