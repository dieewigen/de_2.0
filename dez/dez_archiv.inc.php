<?php
         include 'inc/lang/'.$sv_server_lang.'_dezarchivinc.lang.php';
?>
<center>
  <br>
	<span style="font-size: 14pt; font-weight: bold;"><?=$dezarchivinc_lang[title_1]?></span><br>
	<?=$dezarchivinc_lang[title_2]?><br><br>

	<?php
		echo '<br><a href="'.$PHP_SELF.'?site" class="btn">'.$dezindex_lang["uebersicht"].'</a>';
		echo '<br><br><br>';

		$DBData = mysql_query("SELECT * FROM ".TABLE_ZDATA." WHERE `id` = ".$_GET["zid"]);
		if (mysql_num_rows($DBData)) {
			$ZData = mysql_fetch_assoc($DBData);
			echo '<table border="0" cellspacing="0" cellpadding="5">';
			echo ' <tr><td><span style="font-size: 10pt; font-weight: bold;">'.$dezarchivinc_lang[zeitung].':</span></td><td><span style="font-size: 10pt; font-weight: bold;">'.$ZData["name"].'</span></td></tr>';
			echo ' <tr><td><span style="font-size: 10pt; font-weight: bold;">'.$dezarchivinc_lang[herausgeber].':</span></td><td><span style="font-size: 10pt; font-weight: bold;">'.$ZData["nick"].'</span></td></tr>';
			echo '</table><br><br>';
		}
		else {
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezarchivinc_lang[msg_1].'</span><br><br><br>';
		}

		$DBData = mysql_query("SELECT * FROM ".TABLE_ADATA." WHERE `zid` = ".$_GET["zid"]." AND `frei` = 1 ORDER BY `datum`");
		if (mysql_num_rows($DBData)) {
			echo '<form method="post" action="'.$PHP_SELF.'?site">';
			echo '<table border="0" cellspacing="0" cellpadding="5" style="border: 1px solid #BBBBBB; width: 600px;">';
			echo ' <tr><td class="cell1" align="center"><b>'.$dezarchivinc_lang[ausgabe].'</b></td><td class="cell" align="center"><b>'.$dezarchivinc_lang[titel].'</b></td><td class="cell1" align="center"><b>'.$dezarchivinc_lang[preis].'</b></td><td class="cell" align="center"><b>'.$dezarchivinc_lang[verkauft].'</b></td><td class="cell">&nbsp;</td></tr>';
			while ($AData = mysql_fetch_assoc($DBData)) {
				$AnzBuy = substr_count($AData["gekauft"], '#') / 2;

				echo ' <tr><td class="cell1" align="center" width="70px;">'.date('d.m.Y' ,$AData['datum']).'</td>';
				echo '     <td class="cell" align="center">'.$AData["titel"].'</td>';
				echo '     <td class="cell1" align="center" width="70px;">'.number_format($AData['preis'], 0, '', '.').' M</td>';
				echo '     <td class="cell" align="center" width="70px;">'.$AnzBuy.'</td>';
				echo '     <td class="cell" align="center" width="55px;">';
				if ((substr_count($AData["gekauft"], '#'.$ums_user_id.'#') == 0) && ($ums_user_id != $ZData["userid"])) {
					echo '   <input type="submit" name="buy'.$AData['id'].'" value="'.$dezarchivinc_lang[kaufen].'">'; }
				else { echo '   <input type="submit" name="read'.$AData['id'].'" value="'.$dezarchivinc_lang[lesen].'">'; }
				echo '</td></tr>';
			}
			echo '</table>';
			echo '</form>';
		}
		else {
			echo '<br><span style="color: #FF2828; font-weight: bold;">'.$dezarchivinc_lang[msg_2].'</span><br><br><br>';
		}
	?>

	<br><br>
</center>