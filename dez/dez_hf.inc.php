<?php
         include 'inc/lang/'.$sv_server_lang.'_dezhfinc.lang.php';
?>
<center>
  <br>
	<span style="font-size: 14pt; font-weight: bold;"><?=$dezhfinc_lang[title_1]?></span><br>
	<?=$dezhfinc_lang[title_2]?><br><br>

	<?php
		echo '<br><a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
		echo '<br><br><br>';

		if (isset($_GET["zid"])) { $aktZID = $_GET["zid"]; }
		if (isset($_POST["zid"])) { $aktZID = $_POST["zid"]; }

		$ZData = mysql_fetch_assoc(mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `id` = ".$aktZID));
		$SendErr = 0;

    $onIgnore = mysql_num_rows(mysql_query("SELECT `sector`, `system` FROM `de_hfn_buddy_ignore` WHERE `user_id` = '".$ZData["userid"]."' and `system` = '".$system."' and `sector` = '".$sector."' and `status` = 2"));
    $onUMode = mysql_result(mysql_query("SELECT `status` FROM `de_login` WHERE `user_id` = '".$ZData["userid"]."'"), 0);
		if ($onUMode != 1) { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezhfinc_lang[msg_1].'</span><br><br><br>'; $SendErr = 2; }
		if ($onIgnore != 0) { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezhfinc_lang[msg_2].'</span><br><br><br>'; $SendErr = 2; }

		if (isset($_POST["sendhf"])) {
			if (trim($_POST["hftext"]) == "") { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezhfinc_lang[msg_3].'</span><br><br><br>'; $SendErr = 1; }
		  else {
		  	if (trim($_POST["hfbetreff"]) == "") { echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezhfinc_lang[msg_4].'</span><br><br><br>'; $SendErr = 1; }
		  	else {
              $_POST["hftext"] = '<'.$dezhfinc_lang[msg_5_1].'>'."\r\n".'<'.$dezhfinc_lang[msg_5_2].': '.$ZData["name"].' - '.$ZData["nick"].'>'."\r\n\r\n".$_POST["hftext"];

		      $_POST["hftext"] = htmlspecialchars(stripslashes($_POST["hftext"]));
		      $_POST["hftext"] = str_replace('\"', '&quot;', $_POST["hftext"]);
		      $_POST["hftext"] = str_replace('\'', '&acute;', $_POST["hftext"]);
		      $_POST["hftext"] = nl2br($_POST["hftext"]);

		      $_POST["hftext"] = str_replace('script', 'schkript', $_POST["hftext"]);
		      $_POST["hftext"] = str_replace('Script', 'Schkript', $_POST["hftext"]);

					mysql_query("INSERT INTO `de_user_hyper` (empfaenger, absender, fromsec, fromsys, fromnic, time, betreff, text, sender) VALUES ('".$ZData["userid"]."', '".$ums_user_id."', '".$sector."', '".$system."', '".$ums_spielername."', '".strftime("%Y%m%d%H%M%S")."', '".$_POST["hfbetreff"]."', '".$_POST["hftext"]."', 0)");
					mysql_query("UPDATE `de_user_data` SET `newtrans` = 1 WHERE `user_id` = '".$ZData["userid"]."'");

					echo '<br>'.$dezhfinc_lang[msg_6_1].' "'.$ZData["name"].'" '.$dezhfinc_lang[msg_6_2].'!<br><br><br>';
				}
			}
		}

		if (((!isset($_POST["sendhf"])) && ($SendErr == 0)) ||
		    ((isset($_POST["sendhf"])) && ($SendErr == 1))) {
			echo '<form method="post" action="'.$PHP_SELF.'?site=hf">';
			echo '<b>'.$ZData["name"].'</b>: '.$ZData["nick"].'<br><br>';
			echo ' <input type="hidden" name="zid" value="'.$aktZID.'">';
			echo $dezhfinc_lang[betreff].': <input type="text" name="hfbetreff" value="';
			if (isset($_POST["hfbetreff"])) { echo stripslashes($_POST["hfbetreff"]); }
			echo '" style="width:538px;"><br>';
			echo ' <textarea name="hftext" cols="70" rows="20" style="width: 585px;">';
			if (isset($_POST["hftext"])) { echo stripslashes($_POST["hftext"]); }
			echo '</textarea><br>';
			echo ' <input type="submit" name="sendhf" value="'.$dezhfinc_lang[hfabschicken].'">';
			echo '</form>';
		}
	?>

	<br><br>
</center>