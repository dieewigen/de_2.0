<?php
         include 'inc/lang/'.$sv_server_lang.'_dezcreateinc.lang.php';
?>
<center>
  <br>
	<span style="font-size: 14pt; font-weight: bold;"><?=$dezcreateinc_lang[title1]?></span><br>
	<?=$dezcreateinc_lang[title2]?><br><br>

	<?php
		echo '<table><tr><td>';
		echo '<a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
		echo '</td><td>';
		echo '<a href="'.$PHP_SELF.'?site=my" class="btn">'.$dezindex_lang["verwaltung"].'</a>';
		echo '</td></tr></table>';
		echo '<br><br>';

		$ZAnz = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 0"), 0);
		$ZAnzE = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 1"), 0);

		if ($ZAnz >= $DEZ_MaxZeitungen) {
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezcreateinc_lang[msg_1_1].' '.$DEZ_MaxZeitungen.' '.$dezcreateinc_lang[msg_1_2].'</span><br><br><br>';
			include('dez/dez_footer.inc.php');
			die;
		}

		if (($ZAnz + $ZAnzE) >= ($DEZ_MaxZeitungen + $DEZ_MaxEingestellt)) {
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezcreateinc_lang[msg_2_1].' '.($DEZ_MaxZeitungen + $DEZ_MaxEingestellt).' '.$dezcreateinc_lang[msg_2_2].' ('.$ZAnz.' '.$dezcreateinc_lang[msg_2_3].', '.$ZAnzE.' '.$dezcreateinc_lang[msg_2_4].'), '.$dezcreateinc_lang[msg_2_5].'</span><br><br><br>';
			include('dez/dez_footer.inc.php');
			die;
		}

		if (isset($_POST['z_create'])) {
			if (trim($_POST['z_name'] != "")) {
				$ZExist = mysql_result(mysql_query("SELECT COUNT(`id`) FROM ".TABLE_ZDATA." WHERE `name` = '".$_POST['z_name']."'"), 0);
				if ($ZExist == 0) {
					if (trim($_POST['z_nick'] != "")) {
						if ($_POST['z_logo'] == "http://") { $_POST['z_logo'] = ""; }

						$_POST['z_name'] = htmlentities(trim($_POST['z_name']), ENT_NOQUOTES);
						$_POST['z_name'] = stripslashes($_POST['z_name']);
						$_POST['z_name'] = str_replace("'", "", $_POST['z_name']);
						$_POST['z_name'] = str_replace("\"", "", $_POST['z_name']);

						$_POST['z_nick'] = htmlentities(trim($_POST['z_nick']), ENT_NOQUOTES);
						$_POST['z_nick'] = stripslashes($_POST['z_nick']);
						$_POST['z_nick'] = str_replace("'", "", $_POST['z_nick']);
						$_POST['z_nick'] = str_replace("\"", "", $_POST['z_nick']);

						mysql_query("INSERT INTO ".TABLE_ZDATA." (name, kategorie, userid, nick, logo) VALUES ('".$_POST['z_name']."', '".$_POST['z_kat']."', '".$ums_user_id."', '".$_POST['z_nick']."', '".$_POST['z_logo']."')");
						echo '<br>Zeitung wurde erstellt!<br><br><br>';
						include('dez/dez_footer.inc.php');
						die;
					}
					else {
						echo '<br><span style="color: #FF2828;">'.$dezcreateinc_lang[msg_3].'</span><br><br><br>';
					}
				}
				else {
					echo '<br><span style="color: #FF2828;">'.$dezcreateinc_lang[msg_4].'</span><br><br><br>';
				}
			}
			else {
				echo '<br><span style="color: #FF2828;">'.$dezcreateinc_lang[msg_5].'</span><br><br><br>';
			}
		}
	?>

	<form method="post" action="<?php echo $PHP_SELF; ?>?site=create">
		<table border="0" cellspacing="5" cellpadding="0">
			<tr><td><?=$dezcreateinc_lang[zeitungsname]?>:</td><td><input type="text" name="z_name" value="<?php if (isset($_POST["z_name"])) { echo $_POST["z_name"]; } ?>" style="width: 250px;" maxlength="50"></td></tr>
			<tr><td><?=$dezcreateinc_lang[zeitungslogo]?>:</td><td><input type="text" name="z_logo" value="<?php if (isset($_POST["z_logo"])) { echo $_POST["z_logo"]; } else { echo 'http://'; } ?>" style="width: 250px;" maxlength="150"></td></tr>
			<tr><td><?=$dezcreateinc_lang[nameherausgeber]?>:</td><td><input type="text" name="z_nick" value="<?php if (isset($_POST["z_nick"])) { echo $_POST["z_nick"]; } else { echo $ums_spielername; } ?>" style="width: 250px;" maxlength="50"></td></tr>
			<tr><td><?=$dezcreateinc_lang[kategorie]?></td><td><select name="z_kat">
			<?php
				for ($i = 1; $i < count($DEZ_ZKat); $i++) {
					echo '<option value="'.$i.'"';
					if ($i == 1) { echo ' selected'; }
					echo '>'.$DEZ_ZKat[$i].'</option>';
				}
			?>
			</select></td></tr>
		</table><br>
		<?=$dezcreateinc_lang[logo_1]?><br><br>
		<?=$dezcreateinc_lang[logo_2]?>:<br><br>
		<table border="0" cellspacing="0" cellpadding="5">
		 <tr><td><?=$dezcreateinc_lang[groesse]?>:</td><td>600px * 80px</td></tr>
		 <tr><td><?=$dezcreateinc_lang[typ]?>:</td><td>gif / jpg / png</td></tr>
		 <tr><td><?=$dezcreateinc_lang[art_1]?>:</td><td><?=$dezcreateinc_lang[art_2]?></td></tr>
		 </table>
		 <br><br>
		<input type="submit" name="z_create" value="<?=$dezcreateinc_lang[zeitungsbutton]?>">
	</form>

	<br><br>
</center>