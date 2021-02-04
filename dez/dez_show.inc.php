<?php
	if (substr_count($AText, "[BR]") > 0) {
		$AText = str_replace("[BR]", "<br>", $AText);
	}
	else {
		$AText = str_replace("\r\n", "<br>", $AText);
		$AText = str_replace("\n", "<br>", $AText);
	}

	$AText = str_replace("[CENTER]", "<center>", $AText);
	$AText = str_replace("[/CENTER]", "</center>", $AText);
	$AText = str_replace("[BLOCKTEXT]", '<div style="text-align: justify;">', $AText);
	$AText = str_replace("[/BLOCKTEXT]", "</div>", $AText);

	$AText = str_replace("[TABLE]", '<table border="0" cellspacing="0" cellpadding="10">', $AText);
	$AText = str_replace("[/TABLE]", "</table>", $AText);
	$AText = str_replace("[TR]", "<tr>", $AText);
	$AText = str_replace("[/TR]", "</tr>", $AText);
	$AText = str_replace("[TD]", '<td valign="top">', $AText);
	$AText = str_replace("[/TD]", "</td>", $AText);

	$AText = str_replace("[B]", "<b>", $AText);
	$AText = str_replace("[/B]", "</b>", $AText);
	$AText = str_replace("[U]", "<u>", $AText);
	$AText = str_replace("[/U]", "</u>", $AText);
	$AText = str_replace("[I]", "<i>", $AText);
	$AText = str_replace("[/I]", "</i>", $AText);

	$AText = preg_replace("/\[COLOR=([^[]+)\]/", '<span style="color: \\1;">', $AText);
	$AText = str_replace("[/COLOR]", "</span>", $AText);
	$AText = preg_replace("/\[SIZE=([^[]+)\]/", '<span style="font-size: \\1pt;">', $AText);
	$AText = str_replace("[/SIZE]", "</span>", $AText);

	$AText = preg_replace("/\[A href=([^[]+)\]([^[]*)\[\/A\]/i", "<a href=\\1 target=\"_blank\">\\2</a>", $AText);

	echo '<div style="border: 1px solid #999999; padding: 10px; width: 600px; text-align: left;">'.$AText.'</div>';
?>