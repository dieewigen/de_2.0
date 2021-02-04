<script>
	function openWindow( url, width, height, left, top, winname )
	{
		var trails = "width=" + width + ",height=" + height + ",left=" + left +",top=" + top +",toolbar=no,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no";
		newWindow = window.open( url, winname, trails);
	}
</script>
<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.fleetform.lang.php';
	if (!isset($restype) || strlen($restype) == 0)
	{
		$restype = "m";
	}

	//$fleetconfig = ${"fleet_"."$ums_rasse"};

	$restext = $$restype;
	$m_menutext = $m;
	$d_menutext = $d;
	$i_menutext = $i;
	$e_menutext = $e;
	$t_menutext = $t;



	switch ($restype)
	{
		case "m"	:	$m_menutext = "<strong>>> $tradefleetform_lang[multiplex] <<</strong>";
						break;
		case "d"	:	$d_menutext = "<strong>>> $tradefleetform_lang[dyharra] <<</strong>";
						break;
		case "i"	:	$i_menutext = "<strong>>> $tradefleetform_lang[iradium] <<</strong>";
						break;
		case "e"	:	$e_menutext = "<strong>>> $tradefleetform_lang[eternium] <<</strong>";
						break;
	}
	print("<br>");
	print("<table width=600><tr><td align=left colspan=4><h2>$tradefleetform_lang[flottenhandelwaehrung] $restext</h2></td></tr>");
	print("<tr><td colspan=4><hr></td></tr><tr>");
	print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=m_fleet\">$m_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=d_fleet\">$d_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=i_fleet\">$i_menutext</a></td>");
	print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=e_fleet\">$e_menutext</a></td>");
	print("</tr><tr><td colspan=4><hr></td></tr></table>");


	print("<table width=600>");
	print("<tr><td colspan=8 bgcolor=#1c1c1c align=center>&nbsp;</td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradefleetform_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradefleetform_lang[kaufgesuche]</strong></td></tr>");
	print("<tr><td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[ware]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[hf]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[wert]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[zeit]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[min]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[instock]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[max]</strong></td>
			<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradefleetform_lang[menge]</strong></td></tr>");

	$options = "";
	for ($i=81;$i<89;$i++)
	{
		$shiptype = "e".$i;
		$shipname = $fleetconfig["$shiptype"]["name"];
		$options.= "<option value=\"$shiptype\">$shipname</option>";
		generateResline($shiptype, $restype, $ums_user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, $ums_rasse);
	}
	print("<tr><td colspan=12><hr></td></tr>");
	print("<tr><td colspan=12 style=\"font-size:9px\">$tradefleetform_lang[msg_1]</td></tr>");
	print("<tr><td colspan=12><hr></td></tr>");
	print("</table>");

	print("<form name=\"tradeform\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
	print("<input type=hidden name=currency value=$restype>");
	print("<input type=hidden name=viewmode value=$viewmode>");
	print("<table width=600><tr><td valign=top align=left>");
	print("<table>");

	print("<tr><td>$tradefleetform_lang[gehandelterschiffstyp]</td>
				<td>
					<select style=\"width : 95;\" name=\"shiptype\">$options</select>
				</td>
				<td><a href=\"javascript:openWindow('trade.php?help_id=type_a',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=Hilfe border=0></a></td></tr>");
	print("<tr><td>$tradefleetform_lang[gehandeltemenge]</td><td><input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 95;\"></td><td><a href=\"javascript:openWindow('trade.php?help_id=menge_a',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=Hilfe border=0></a></td></tr>");
	print("<tr><td>$tradefleetform_lang[ordertyp]</td><td><select style=\"width : 95;\" name=\"action\"><option value=\"fleet_sell\">$tradefleetform_lang[verkaufen]</option><option value=\"fleet_buy\">$tradefleetform_lang[ankaufen]</option></select></td><td><a href=\"javascript:openWindow('trade.php?help_id=handelstyp',500,300,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=Hilfe border=0></a></td></tr>");
	print("<tr><td>$tradefleetform_lang[pps]</td><td><input type=\"text\" name=\"price\" value=\"\" style=\"width : 95;\"></td><td><a href=\"javascript:openWindow('trade.php?help_id=preis',500,330,10,10,'')\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_help.gif\" alt=Hilfe border=0></a></td></tr>");
	print("<tr><td></td><td colspan=2><input type=\"submit\" name=\"submit\" value=\"$tradefleetform_lang[ausfuehren]\" style=\"width : 95;\"></td></tr>");
	print("</table>");
	print("</td><td valign=top align=right>");
	print("<table width=300>");
	print("<tr><td colspan=2 bgcolor=#1c1c1c><strong>$tradefleetform_lang[legende]:</strong></td></tr>");
	print("<tr><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[schiffstyp]: </td><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[msg_2].</td></tr>");
	print("<tr><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[gehandeltemenge]: </td><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[msg_3].</td></tr>");
	print("<tr><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[ordertyp]: </td><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[msg_4].</td></tr>");
	print("<tr><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[pps]: </td><td valign=top bgcolor=#1c1c1c style=\"font-size:9px\">$tradefleetform_lang[msg_5].</td></tr>");
	print("</table>");
	print("</td></tr></table></form>");

	function generateResline($shiptype, $currency, $user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, $race)
	{
         	global $tradefleetform_lang;
         	global $sv_server_lang;
		include("trade/trade.config.inc.php");
		$minprice = "0";
		$maxprice = "0";
		$minpricemenge = "0";
		$maxpricemenge = "0";
		$query = "SELECT min(price) from $de_trade_fleetoffer WHERE sell_type='$shiptype' AND currency='$currency' AND race='$race' AND user_id!='$user_id'";
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
			$query = "SELECT sum(amount) from $de_trade_fleetoffer WHERE sell_type='$shiptype' AND currency='$currency' AND price='$minprice' AND race='$race' AND user_id!='$user_id'";
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

		$query = "SELECT max(price) from $de_trade_fleetrequest WHERE buy_type='$shiptype' AND currency='$currency' AND race='$race' AND user_id!='$user_id'";
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
			$query = "SELECT sum(amount) from $de_trade_fleetrequest WHERE buy_type='$shiptype' AND currency='$currency' AND price='$maxprice' AND race='$race' AND user_id!='$user_id'";
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
		$currencydisplayname = $$currency;
		//$currency = $$currency;
		if ($minpricemenge == 0)
		{
			$minpricemenge = $minprice = $tradefleetform_lang[keine];
		}
		if ($maxpricemenge == 0)
		{
			$maxpricemenge = $maxprice = $tradefleetform_lang[keine];
		}
		$shipname = $fleetconfig["$shiptype"]["name"];
		$shipvalue = $fleetconfig["$shiptype"]["value"];
		$shipmax05value = ($shipvalue + ($shipvalue/100*5));
		$shipmax10value = ($shipvalue + ($shipvalue/100*10));
		$shipmax15value = ($shipvalue + ($shipvalue/100*15));
		$shipmax20value = ($shipvalue + ($shipvalue/100*20));
		$shiptransfer = $fleetconfig["$shiptype"]["trade_time"];
		$f_query = "select $shiptype from de_user_fleet WHERE user_id='".$user_id."-0'";
		$f_result = mysql_query($f_query);
		if ($f_result)
		{
			$numrows = mysql_num_rows($f_result);
			if ($numrows == 1)
			{
				$values = mysql_fetch_array($f_result);
				$depot_count = $values["$shiptype"];
			}
		}
		$index = (substr($shiptype, 2,1) - 1);

		if ($minprice >0)
		{
			if ($currency == "m")
			{
				$maxbuyamount = floor($restyp01 / $minprice);
			}
			elseif ($currency == "d")
			{
				$maxbuyamount = floor($restyp02 * 2 / $minprice);
			}
			elseif ($currency == "i")
			{
				$maxbuyamount = floor($restyp03 * 3 / $minprice);
			}
			elseif ($currency == "e")
			{
				$maxbuyamount = floor($restyp04 * 4 / $minprice);
			}
		}
		else
		{
			$maxbuyamount = 0;
		}
		if ($maxbuyamount > $minpricemenge)
		{
			$maxbuyamount = $minpricemenge;
		}
		$maxsellamount = $depot_count;
		if ($maxsellamount > $maxpricemenge)
		{
			$maxsellamount = $maxpricemenge;
		}

		print("<tr>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax05value'; document.tradeform.action.selectedIndex=0\">$shipname</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax05value'; document.tradeform.action.selectedIndex=0\"><font color=green>".number_format($depot_count,0,",",".")."</font></td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipvalue'; document.tradeform.action.selectedIndex=0\">".number_format($shipvalue,0,",",".")."</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax05value'; document.tradeform.action.selectedIndex=0\">+5%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax10value'; document.tradeform.action.selectedIndex=0\">+10%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax15value'; document.tradeform.action.selectedIndex=0\">+15%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex='$index'; document.tradeform.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax20value'; document.tradeform.action.selectedIndex=0\">+20%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\">$shiptransfer</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex=$index; document.tradeform.tradeamount.value='$maxbuyamount'; document.tradeform.price.value='$minprice'; document.tradeform.action.selectedIndex=1\">".number_format(floatval($minprice),2,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex=$index; document.tradeform.tradeamount.value='$maxbuyamount'; document.tradeform.price.value='$minprice'; document.tradeform.action.selectedIndex=1\">".number_format(floatval($minpricemenge),0,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex=$index; document.tradeform.tradeamount.value='$maxsellamount'; document.tradeform.price.value='$maxprice'; document.tradeform.action.selectedIndex=0\">".number_format(floatval($maxprice),2,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform.shiptype.selectedIndex=$index; document.tradeform.tradeamount.value='$maxsellamount'; document.tradeform.price.value='$maxprice'; document.tradeform.action.selectedIndex=0\">".number_format(floatval($maxpricemenge),0,",",".")."</td></tr>");


	}
?>