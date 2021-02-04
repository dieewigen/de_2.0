<?PHP
include 'inc/lang/'.$sv_server_lang.'_deznewinc.lang.php';
?>

<center>
  <br>
	<span style="font-size: 14pt; font-weight: bold;"><?=$deznewinc_lang[title_1]?></span><br>
	<?=$deznewinc_lang[title_2]?><br><br>

	<?php
		if ($accticks < $DEZ_PStTicks) { $MaxPreis = 100; }
		 else { $MaxPreis = 100 + (floor($accticks / $DEZ_PStTicks) * $DEZ_PStPreis); }
		if ($MaxPreis > $DEZ_MAXPreis) { $MaxPreis = $DEZ_MAXPreis; }

		echo '<table><tr><td>';
		echo '<a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
		echo '</td><td>';
		echo '<a href="'.$PHP_SELF.'?site=my" class="btn">'.$dezindex_lang["verwaltung"].'</a>';
		echo '</td></tr></table>';
		echo '<br><br>';

		if (isset($_GET['zid'])) { $ZID = $_GET['zid']; }
		if (isset($_POST['zid'])) { $ZID = $_POST['zid']; }

		$AHinweis = "";
		if ($ZID != 0) {
			$DBData = mysql_query("SELECT `aktuell`, `atemp` FROM ".TABLE_ZDATA." WHERE `userid` = ".$ums_user_id." AND `eingestellt` = 0 AND `id` = ".$ZID);
			if (mysql_num_rows($DBData)) {
				$ZData = mysql_fetch_assoc($DBData);
				if (time() < ($ZData["aktuell"] + $DEZ_NextAusgabe)) {
					$AHinweis = '<span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[msg_1].'<br><br>';
					$AHinweis .= $deznewinc_lang[msg_2].' '.date("d.m.Y H:i:s", ($ZData["aktuell"] + $DEZ_NextAusgabe)).'.';
					$AHinweis .= '</span><br><br><br>';
				}
			}
			else {
				echo '<br><span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[err_zugriff].'!</span><br><br><br>';
				include('dez/dez_footer.inc.php');
				die;
			}
		}
		else {
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[err_zugriff].'!</span><br><br><br>';
			include('dez/dez_footer.inc.php');
			die;
		}

		if (isset($_POST["a_savetmp"])) {
			$AText = $_POST["a_text"];
			$AText = htmlentities(trim($AText), ENT_NOQUOTES);
			$AText = stripslashes($AText);
			$AText = str_replace("'", "&#39;", $AText);
			$AText = str_replace("\"", "&#34;", $AText);
			mysql_query("UPDATE ".TABLE_ZDATA." SET `atemp` = '".$AText."' WHERE `id` = ".$ZID);
		}

		if (isset($_POST["a_save"])) {
			if ($AHinweis == "") {
				if ($_POST["a_titel"] == "") {
					echo '<br><span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[msg_3].'</span><br><br><br>';
				}
				else {
					if (($_POST["a_preis"] > $MaxPreis) || ($_POST["a_preis"] < $DEZ_MinPreis)) {
						echo '<br><span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[msg_4_1].' '.$DEZ_MinPreis.' '.$deznewinc_lang[msg_4_2].' '.$MaxPreis.' '.$deznewinc_lang[msg_4_3].'</span><br><br><br>';
						if ($_POST["a_preis"] > $MaxPreis) { $_POST["a_preis"] = $MaxPreis; }
						if ($_POST["a_preis"] < $DEZ_MinPreis) { $_POST["a_preis"] = $DEZ_MinPreis; }
					}
					else {
						$AText = $_POST["a_text"];
						$AText = htmlentities(trim($AText), ENT_NOQUOTES);
						$AText = stripslashes($AText);
						$AText = str_replace("'", "&#39;", $AText);
						$AText = str_replace("\"", "&#34;", $AText);

						mysql_query("INSERT INTO ".TABLE_ADATA." (zid, titel, datum, ausgabe, preis) VALUES ('".$ZID."', '".$_POST["a_titel"]."', '".time()."', '".$AText."', '".$_POST["a_preis"]."')");
						//mysql_query("UPDATE ".TABLE_ZDATA." SET `aktuell` = ".time().", `atemp` = '' WHERE `id` = ".$ZID);
						mysql_query("UPDATE ".TABLE_ZDATA." SET `atemp` = '' WHERE `id` = ".$ZID);
						echo '<br>'.$deznewinc_lang[msg_5].'<br><br><br>';
						include('dez/dez_footer.inc.php');
						die;
					}
				}
			}
			else {
				echo '<br><span style="color: #FF2828; font-weight: bold;">'.$deznewinc_lang[err_zugriff].'</span><br><br><br>';
				include('dez/dez_footer.inc.php');
				die;
			}
		}

		echo $AHinweis;
	?>

	<form name="a_form" method="post" action="<?php echo $PHP_SELF; ?>?site=new">
	<input type="hidden" name="zid" value="<?php echo $ZID; ?>">
	<table border="0" cellspacing="5" cellpadding="0">
	 <tr><td><?=$deznewinc_lang[titel]?>:</td><td><input type="text" name="a_titel" value="<?php if (isset($_POST["a_titel"])) { echo $_POST["a_titel"]; } ?>" maxlength="50" style="width: 250px;"></td></tr>
   <tr><td><?=$deznewinc_lang[preis]?>:</td><td><input type="text" name="a_preis" value="<?php if (isset($_POST["a_preis"])) { echo $_POST["a_preis"]; } else { echo '0'; } ?>" maxlength="5" style="width: 50px; text-align: center;"> <?=$deznewinc_lang[multiplex]?> &nbsp; &nbsp; (<?=$deznewinc_lang[maximal]?>: <?php echo $MaxPreis; ?> <?=$deznewinc_lang[kurz_Multiplex]?>)</td></tr>
  </table>
	<br><br>

  <script src="dez/dez_new.js" type="text/javascript"></script>

  <input type="button" value="BR" onclick="bbcode(document.a_form,'BR','')">

  <input type="button" value="CENTER" onclick="bbcode(document.a_form,'CENTER','')">
  <input type="button" value="<?=$deznewinc_lang[blocktext]?>" onclick="bbcode(document.a_form,'BLOCKTEXT','')">

  <input type="button" value="TABLE" onclick="bbcode(document.a_form,'TABLE','')">
  <input type="button" value="TR" onclick="bbcode(document.a_form,'TR','')">
  <input type="button" value="TD" onclick="bbcode(document.a_form,'TD','')">

  <input type="button" value="B" onclick="bbcode(document.a_form,'B','')">
  <input type="button" value="U" onclick="bbcode(document.a_form,'U','')">
  <input type="button" value="I" onclick="bbcode(document.a_form,'I','')">

  <input type="button" value="<?=$deznewinc_lang[color]?>" onclick="bbcode(document.a_form,'COLOR','')">
  <input type="button" value="<?=$deznewinc_lang[groesse]?>" onclick="bbcode(document.a_form,'SIZE','')">

  <input type="button" value="URL" onclick="namedlink(document.a_form,'URL')">

	<br><br>
	<textarea name="a_text" cols="70" rows="20" style="width: 585px;"><?php if (isset($_POST["a_text"])) { echo stripslashes($_POST["a_text"]); } else { echo stripslashes($ZData["atemp"]); } ?></textarea>
	<br><br>
	<input type="submit" name="a_savetmp" value="<?=$deznewinc_lang[savetext]?>"> &nbsp;
	<input type="submit" name="a_vorschau" value="<?=$deznewinc_lang[vorschau]?>"> &nbsp;
	<input type="submit" name="a_save" value="<?=$deznewinc_lang[veroeffentlichen]?>" <?php if ($AHinweis != "") { echo 'disabled style="border-color: #555555; color: #555555;"'; } ?>>
	</form>

	<?php
		if (isset($_POST["a_vorschau"])) {
			$AText = $_POST["a_text"];
			$AText = htmlentities(trim($AText), ENT_NOQUOTES);
			$AText = stripslashes($AText);
			$AText = str_replace("'", "&#39;", $AText);
			$AText = str_replace("\"", "&#34;", $AText);

			echo '<br><span style="font-size: 13px; font-weight: bold;">'.$deznewinc_lang[preview].':</span><br><br>';
			include('dez/dez_show.inc.php');
		}
	?>

	<br><br>
</center>