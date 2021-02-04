<script>
	function openWindow( url, width, height, left, top, winname )
	{
		var trails = "width=" + width + ",height=" + height + ",left=" + left +",top=" + top +",toolbar=no,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no";
		newWindow = window.open( url, winname, trails);
	}
</script>
<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.resform.lang.php';

	if (!isset($restype) || strlen($restype) == 0)
	{
		$restype = "m";
	}



	$restext = $$restype;
	$m_menutext = $m;
	$d_menutext = $d;
	$i_menutext = $i;
	$e_menutext = $e;
	$t_menutext = $t;

	switch ($restype)
	{
		case "m"	:	$options = "<option value=\"d\">$d</option><option value=\"i\">$i</option><option value=\"e\">$e</option>";
						$m_menutext = "<strong>>> $traderesform_lang[multiplex]</strong>";
						break;
		case "d"	:	$options = "<option value=\"m\">$m</option><option value=\"i\">$i</option><option value=\"e\">$e</option>";
						$d_menutext = "<strong>>> $traderesform_lang[dyharra]</strong>";
						break;
		case "i"	:	$options = "<option value=\"m\">$m</option><option value=\"d\">$d</option><option value=\"e\">$e</option>";
						$i_menutext = "<strong>>> $traderesform_lang[iradium]</strong>";
						break;
		case "e"	:	$options = "<option value=\"m\">$m</option><option value=\"d\">$d</option><option value=\"i\">$i</option>";
						$e_menutext = "<strong>>> $traderesform_lang[eternium]</strong>";
						break;
		case "t"	:	$t_menutext = "<strong>>> $traderesform_lang[tronic]</strong>";
						break;
	}
	print("<br>");
	print("<table width=600><tr>");
	print("<td width=\"25%\" bgcolor=#1c1c1c><a href=\"trade.php?viewmode=m_res\">$m_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c><a href=\"trade.php?viewmode=d_res\">$d_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c><a href=\"trade.php?viewmode=i_res\">$i_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c><a href=\"trade.php?viewmode=e_res\">$e_menutext</a></td>");
	//print("<td width=\"20%\" bgcolor=#1c1c1c><a href=\"trade.php?viewmode=t_res\">$t_menutext</a></td>");
	print("</tr></table>");
	print("<br>");
	if ($restype == "t")
	{
		print("<div align=center><table width=600><tr><td align=left><h2>$traderesform_lang[resourcenhandel] - $restext $traderesform_lang[verkaufen]</h2></td></tr><tr><td><br>$traderesform_lang[msg_1].</td></tr></table></div>");
		print("<br>");
		print("<form name=\"tradeform\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=action value=tronic_sell>");
		print("<table width=600><tr><td valign=top align=left>");
		print("<table>");
		print("<tr><td>$traderesform_lang[verkaufsmenge]</td><td><input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 95;\"></td></tr>");
		print("<tr><td></td><td colspan=2><input type=\"submit\" name=\"submit\" value=\"$traderesform_lang[ausfuehren]\" style=\"width : 95;\"></td></tr>");
		print("</table>");
		print("</td><td valign=top align=right>");
		print("</td></tr></table></form>");
	}
	else
	{
		print("<div align=center><table width=600><tr><td align=left><h2>$traderesform_lang[resourcenhandel] - $restext $traderesform_lang[handeln]</h2></td></tr></table></div>");
		print("<br>");
		print("<table width=600>");
		print("<tr><td colspan=2 bgcolor=#1c1c1c align=center>&nbsp;</td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$traderesform_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$traderesform_lang[kaufgesuche]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[ware]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[waehrung]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[guenstigsterpreis]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[imlager]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[hoechstesgebot]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[gesuchsanzahl]</strong></td></tr>");
		if ($restype != "m")
		{
			generateResline($restype, "m", $ums_user_id, $traderesform_lang, $sv_server_lang);
		}
		if ($restype != "d")
		{
			generateResline($restype, "d", $ums_user_id, $traderesform_lang, $sv_server_lang);
		}
		if ($restype != "i")
		{
			generateResline($restype, "i", $ums_user_id, $traderesform_lang, $sv_server_lang);
		}
		if ($restype != "e")
		{
			generateResline($restype, "e", $ums_user_id, $traderesform_lang, $sv_server_lang);
		}
		print("</table>");

		print("<br><br>");
		print("<form name=\"tradeform\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=traderes value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<table width=600><tr><td valign=top align=left>");
		print("<table>");
		print("<tr><td>$traderesform_lang[gehandelteresource]</td><td><input type=text name=displayname value=$restext readonly style=\"width : 95;\"></td><td><a href=\"javascript:openWindow('trade.php?help_id=type_a',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=\"$traderesform_lang[hilfe]\" border=0></a></td></tr>");
		print("<tr><td>$traderesform_lang[gehandeltemenge]</td><td><input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 95;\"></td><td><a href=\"javascript:openWindow('trade.php?help_id=menge_a',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=\"$traderesform_lang[hilfe]\" border=0></a></td></tr>");
		print("<tr><td>$traderesform_lang[ordertyp]</td><td><select style=\"width : 95;\" name=\"action\"><option value=\"res_sell\">$traderesform_lang[verkaufen]</option><option value=\"res_buy\">$traderesform_lang[ankaufen]</option></select></td><td><a href=\"javascript:openWindow('trade.php?help_id=handelstyp',500,300,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=\"$traderesform_lang[hilfe]\" border=0></a></td></tr>");
		print("<tr><td>$traderesform_lang[waehrungfuerdiesenhandel]</td><td><select style=\"width : 95;\" name=\"currency\">$options</select></td><td><a href=\"javascript:openWindow('trade.php?help_id=waehrung',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=\"$traderesform_lang[hilfe]\" border=0></a></td></tr>");
		print("<tr><td>$traderesform_lang[ppe] $restext</td><td><input type=\"text\" name=\"price\" value=\"\" style=\"width : 95;\"></td><td><a href=\"javascript:openWindow('trade.php?help_id=preis',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=\"$traderesform_lang[hilfe]\" border=0></a></td></tr>");
		print("<tr><td></td><td colspan=2><input type=\"submit\" name=\"submit\" value=\"$traderesform_lang[ausfuehren]\" style=\"width : 95;\"></td></tr>");
		print("</table>");
		print("</td><td valign=top align=right>");
		print("<table>");
		print("<tr><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[resource]</strong></td><td bgcolor=#1c1c1c align=center><strong>$traderesform_lang[wert]</strong></td><td bgcolor=#1c1c1c align=center>+5%</td><td bgcolor=#1c1c1c align=center>+10%</td><td bgcolor=#1c1c1c align=center>+15%</td><td bgcolor=#1c1c1c align=center>+20%</td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center>$traderesform_lang[multiplex]</td><td bgcolor=#1c1c1c align=center>1</td><td bgcolor=#1c1c1c align=center>1.05</td><td bgcolor=#1c1c1c align=center>1.10</td><td bgcolor=#1c1c1c align=center>1.15</td><td bgcolor=#1c1c1c align=center>1.20</td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center>$traderesform_lang[dyharra]</td><td bgcolor=#1c1c1c align=center>2</td><td bgcolor=#1c1c1c align=center>2.10</td><td bgcolor=#1c1c1c align=center>2.20</td><td bgcolor=#1c1c1c align=center>2.30</td><td bgcolor=#1c1c1c align=center>2.40</td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center>$traderesform_lang[iradium]</td><td bgcolor=#1c1c1c align=center>3</td><td bgcolor=#1c1c1c align=center>3.15</td><td bgcolor=#1c1c1c align=center>3.30</td><td bgcolor=#1c1c1c align=center>3.45</td><td bgcolor=#1c1c1c align=center>3.60</td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center>$traderesform_lang[eternium]</td><td bgcolor=#1c1c1c align=center>4</td><td bgcolor=#1c1c1c align=center>4.20</td><td bgcolor=#1c1c1c align=center>4.40</td><td bgcolor=#1c1c1c align=center>4.60</td><td bgcolor=#1c1c1c align=center>4.80</td></tr>");
		print("</table>");
		print("</td></tr></table></form>");
	}

	function generateResline($res, $currency, $user_id, $traderesform_lang, $sv_server_lang)
	{
        //global $traderesform_lang;
        //global $sv_server_lang;
		include("trade/trade.config.inc.php");
		$minprice = "0";
		$maxprice = "0";
		$minpricemenge = "0";
		$maxpricemenge = "0";
		$query = "SELECT min(price) from $de_trade_resoffer WHERE sell_type='$res' AND currency='$currency' AND user_id!='$user_id'";
		$result = mysql_query($query);
		if ($result)
		{
			$numrows = mysql_numrows($result);
			if ($numrows == 1)
			{
				$val = mysql_fetch_array($result);
				$minprice = $val[0];
			}
		}

		if ($minprice > 0)
		{
			$query = "SELECT sum(amount) from $de_trade_resoffer WHERE sell_type='$res' AND currency='$currency' AND price='$minprice' AND user_id!='$user_id'";
			$result = mysql_query($query);
			if ($result)
			{
				$numrows = mysql_numrows($result);
				if ($numrows == 1)
				{
					$val = mysql_fetch_array($result);
					$minpricemenge = $val[0];
				}
			}
		}

		$query = "SELECT max(price) from $de_trade_resrequest WHERE buy_type='$res' AND currency='$currency' AND user_id!='$user_id'";
		$result = mysql_query($query);
		if ($result)
		{
			$numrows = mysql_numrows($result);
			if ($numrows == 1)
			{
				$val = mysql_fetch_array($result);
				$maxprice = $val[0];
			}
		}

		if ($maxprice > 0)
		{
			$query = "SELECT sum(amount) from $de_trade_resrequest WHERE buy_type='$res' AND currency='$currency' AND price='$maxprice' AND user_id!='$user_id'";
			$result = mysql_query($query);
			if ($result)
			{
				$numrows = mysql_numrows($result);
				if ($numrows == 1)
				{
					$val = mysql_fetch_array($result);
					$maxpricemenge = $val[0];
				}
			}
		}
		$resdisplayname = $$res;
		$currency = $$currency;
		if ($minpricemenge == 0)
		{
			$minpricemenge = $minprice = "$traderesform_lang[ausverkauft]";
		}
		if ($maxpricemenge == 0)
		{
			$maxpricemenge = $maxprice = "$traderesform_lang[keinegesuche]";
		}

		print("<tr><td bgcolor=#222222 align=center>$resdisplayname</td><td bgcolor=#222222 align=center>$currency</td><td bgcolor=#222222 align=center>$minprice</td><td bgcolor=#222222 align=center>$minpricemenge</td><td bgcolor=#222222 align=center>$maxprice</td><td bgcolor=#222222 align=center>$maxpricemenge</td></tr>");


	}
?>