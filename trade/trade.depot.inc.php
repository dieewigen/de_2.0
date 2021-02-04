<script>
	function openWindow( url, width, height, left, top, winname )
	{
		var trails = "width=" + width + ",height=" + height + ",left=" + left +",top=" + top +",toolbar=no,directories=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no";
		newWindow = window.open( url, winname, trails);
	}
</script>

<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.depot.lang.php';
if ($ums_rasse != "2")
{
	print("
			<table width=600>
			<tr>
				<td align=left>$tradedepot_lang[msg_1]</td>
			</tr>
		</table>
		");
}
else
{
		$dp_res = mysql_query("SELECT * FROM de_trade_depot WHERE user_id='$ums_user_id'");
		if ($dp_res)
		{
			$dp_numrows = mysql_numrows($dp_res);
			if ($dp_numrows < 4)
			{
				mysql_query("INSERT INTO de_trade_depot (user_id, race, e81, e82, e83, e84, e85, e86, e87, e88, e89) VALUES ('$ums_user_id', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
				mysql_query("INSERT INTO de_trade_depot (user_id, race, e81, e82, e83, e84, e85, e86, e87, e88, e89) VALUES ('$ums_user_id', '2', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
				mysql_query("INSERT INTO de_trade_depot (user_id, race, e81, e82, e83, e84, e85, e86, e87, e88, e89) VALUES ('$ums_user_id', '3', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
				mysql_query("INSERT INTO de_trade_depot (user_id, race, e81, e82, e83, e84, e85, e86, e87, e88, e89) VALUES ('$ums_user_id', '4', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
			}
		}

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
			case "m"	:	$m_menutext = "<strong>>> $tradedepot_lang[multiplex] <<</strong>";
							break;
			case "d"	:	$d_menutext = "<strong>>> $tradedepot_lang[dyharra] <<</strong>";
							break;
			case "i"	:	$i_menutext = "<strong>>> $tradedepot_lang[iradium] <<</strong>";
							break;
			case "e"	:	$e_menutext = "<strong>>> $tradedepot_lang[eternium] <<</strong>";
							break;
		}
		print("<br>");
		print("<table width=600><tr><td align=left colspan=4><h2>$tradedepot_lang[depot] $restext</h2></td></tr>");
		print("<tr><td colspan=4><hr></td></tr><tr>");
		print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=m_fleetdepot\">$m_menutext</a></td>");
		print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=d_fleetdepot\">$d_menutext</a></td>");
		print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=i_fleetdepot\">$i_menutext</a></td>");
		print("<td width=\"25%\" bgcolor=#1c1c1c align=center><a href=\"trade.php?viewmode=e_fleetdepot\">$e_menutext</a></td>");
		print("</tr><tr><td colspan=4><hr></td></tr></table>");

		$options1 = "";
		$options2 = "";
		$options3 = "";
		$options4 = "";

		if ($restype =="m")
		{
			$div_factor = 1;
		}
		elseif ($restype =="d")
		{
			$div_factor = 2;
		}
		elseif ($restype =="i")
		{
			$div_factor = 3;
		}
		elseif ($restype =="e")
		{
			$div_factor = 4;
		}

		$fleetconfig = ${"fleet_"."1"};
		$script1 = "<script>values_1 = new Array();";
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options1.= "<option value=\"$shiptype\">$shipname</option>";
			$scr_index=$i-81;
			$s_val = round($fleetconfig["$shiptype"]["value"] / $div_factor,2);
			$script1 .= "values_1[$scr_index] = $s_val;";
		}
		$script1 .= "function recalc_form1(){document.buyform1.pricepu.value=values_1[document.buyform1.shiptype.selectedIndex];document.buyform1.pricesum.value=document.buyform1.pricepu.value*document.buyform1.tradeamount.value;}</script>";
		$fleetconfig = ${"fleet_"."2"};
		$script2 = "<script>values_2 = new Array();";
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options2.= "<option value=\"$shiptype\">$shipname</option>";
			$scr_index=$i-81;
			$s_val = round($fleetconfig["$shiptype"]["value"] / $div_factor,2);
			$script2 .= "values_2[$scr_index] = $s_val;";
		}
		$script2 .= "function recalc_form2(){document.buyform2.pricepu.value=values_2[document.buyform2.shiptype.selectedIndex];document.buyform2.pricesum.value=document.buyform2.pricepu.value*document.buyform2.tradeamount.value;}</script>";
		$fleetconfig = ${"fleet_"."3"};
		$script3 = "<script>values_3 = new Array();";
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options3.= "<option value=\"$shiptype\">$shipname</option>";
			$scr_index=$i-81;
			$s_val = round($fleetconfig["$shiptype"]["value"] / $div_factor,2);
			$script3 .= "values_3[$scr_index] = $s_val;";
		}
		$script3 .= "function recalc_form3(){document.buyform3.pricepu.value=values_3[document.buyform3.shiptype.selectedIndex];document.buyform3.pricesum.value=document.buyform3.pricepu.value*document.buyform3.tradeamount.value;}</script>";
		$fleetconfig = ${"fleet_"."4"};
		$script4 = "<script>values_4 = new Array();";
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options4.= "<option value=\"$shiptype\">$shipname</option>";
			$scr_index=$i-81;
			$s_val = round($fleetconfig["$shiptype"]["value"] / $div_factor,2);
			$script4 .= "values_4[$scr_index] = $s_val;";
		}
		$script4 .= "function recalc_form4(){document.buyform4.pricepu.value=values_4[document.buyform4.shiptype.selectedIndex];document.buyform4.pricesum.value=document.buyform4.pricepu.value*document.buyform4.tradeamount.value;}</script>";
		print($script1);
		print($script2);
		print($script3);
		print($script4);

		print("<table width=600>");
		print("<tr><td colspan=6 bgcolor=#1c1c1c><strong>&nbsp; $tradedepot_lang[msg_2_1] $restext $tradedepot_lang[msg_2_2]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c><strong>$tradedepot_lang[anzahl]</strong></td><td bgcolor=#1c1c1c><strong>$tradedepot_lang[schiff]</strong></td><td bgcolor=#1c1c1c><strong>$tradedepot_lang[einzelpreis]</strong></td><td bgcolor=#1c1c1c><strong>$tradedepot_lang[sesamtpreis]</strong></td><td bgcolor=#1c1c1c><strong>$tradedepot_lang[waehrung]</strong></td><td bgcolor=#1c1c1c><strong>&nbsp;</strong></td></tr>");
		//print("<tr><td colspan=6><hr></td></tr>");

		print("<tr><form name=buyform1 action=\"trade.php\" method=post>");
		print("<td align=center bgcolor=\"#1c1c1c\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=1>");
		print("<input type=hidden name=action value=depotbuy>");
		print("<input size=15 type=text name=tradeamount value=\"\" onKeyUp=\"javascript:recalc_form1();\">");
		print("</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><select onChange=\"javascript:recalc_form1();\" style=\"width:130px\" name=\"shiptype\">$options1</select></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricepu value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricesum value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\">$restext</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input type=submit name=submit value=\"$tradedepot_lang[kaufen]\"></td>");
		print("</form></tr>");

		print("<tr><form name=buyform2 action=\"trade.php\" method=post>");
		print("<td align=center bgcolor=\"#1c1c1c\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=2>");
		print("<input type=hidden name=action value=depotbuy>");
		print("<input size=15 type=text name=tradeamount value=\"\" onKeyUp=\"javascript:recalc_form2();\">");
		print("</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><select onChange=\"javascript:recalc_form2();\" style=\"width:130px\" name=\"shiptype\">$options2</select></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricepu value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricesum value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\">$restext</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input type=submit name=submit value=\"$tradedepot_lang[kaufen]\"></td>");
		print("</form></tr>");

		print("<tr><form name=buyform3 action=\"trade.php\" method=post>");
		print("<td align=center bgcolor=\"#1c1c1c\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=3>");
		print("<input type=hidden name=action value=depotbuy>");
		print("<input size=15 type=text name=tradeamount value=\"\" onKeyUp=\"javascript:recalc_form3();\">");
		print("</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><select onChange=\"javascript:recalc_form3();\" style=\"width:130px\" name=\"shiptype\">$options3</select></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricepu value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricesum value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\">$restext</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input type=submit name=submit value=\"$tradedepot_lang[kaufen]\"></td>");
		print("</form></tr>");

		print("<tr><form name=buyform4 action=\"trade.php\" method=post>");
		print("<td align=center bgcolor=\"#1c1c1c\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=4>");
		print("<input type=hidden name=action value=depotbuy>");
		print("<input size=15 type=text name=tradeamount value=\"\" onKeyUp=\"javascript:recalc_form4();\">");
		print("</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><select onChange=\"javascript:recalc_form4();\" style=\"width:130px\" name=\"shiptype\">$options4</select></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricepu value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input readonly size=15 type=text name=pricesum value=\"\"></td>");
		print("<td align=center bgcolor=\"#1c1c1c\">$restext</td>");
		print("<td align=center bgcolor=\"#1c1c1c\"><input type=submit name=submit value=\"$tradedepot_lang[kaufen]\"></td>");
		print("</form></tr>");


		print("<tr><td colspan=6><hr></td></tr>");
		print("</table>");

		print("<table width=600>");
		print("<tr><td colspan=8 bgcolor=#1c1c1c><strong>&nbsp; $tradedepot_lang[msg_3]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[kaufgesuche]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[ware]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[dp]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[wert]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[zeit]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[minpreis]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[instock]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[maxgebot]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[gesuchsmenge]</strong></td></tr>");

		$options1 = "";
		$options2 = "";
		$options3 = "";
		$options4 = "";

		$fleetconfig = ${"fleet_"."1"};
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options1.= "<option value=\"$shiptype\">$shipname</option>";
			generateResline($shiptype, $restype, $ums_user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, "1");
		}
		print("<tr><td colspan=12><hr></td></tr>");
		print("<tr><td colspan=12>");
		print("<form name=\"tradeform1\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=1>");
		print("<input type=hidden name=action value=depotsell>");
		print("$tradedepot_lang[msg_4_1] <input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 60;\"> <select name=\"shiptype\">$options1</select> $tradedepot_lang[msg_4_2] <input type=\"text\" name=\"price\" value=\"\" style=\"width : 60;\"> $restext $tradedepot_lang[msg_4_3] <input type=\"submit\" name=\"submit\" value=\"$tradedepot_lang[ausfuehren]\">");
		print("</form>");
		print("</td></tr>");
		print("<tr><td colspan=12><hr></td></tr>");
		print("<tr><td colspan=8 bgcolor=#1c1c1c><strong>&nbsp; $tradedepot_lang[msg_5]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[kaufgesuche]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[ware]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[dp]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[wert]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[zeit]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[minpreis]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[instock]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[maxgebot]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[gesuchsmenge]</strong></td></tr>");

		$fleetconfig = ${"fleet_"."2"};
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options2.= "<option value=\"$shiptype\">$shipname</option>";
			generateResline($shiptype, $restype, $ums_user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, "2");
		}
		print("<tr><td colspan=12><hr></td></tr>");
		print("<tr><td colspan=12>");
		print("<form name=\"tradeform2\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=2>");
		print("<input type=hidden name=action value=depotsell>");
		print("$tradedepot_lang[verkaufe] <input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 60;\"> <select name=\"shiptype\">$options2</select> $tradedepot_lang[msg_4_2] <input type=\"text\" name=\"price\" value=\"\" style=\"width : 60;\"> $restext $tradedepot_lang[msg_4_3] <input type=\"submit\" name=\"submit\" value=\"$tradedepot_lang[ausfuehren]\">");
		print("</form>");
		print("</td></tr>");
		print("<tr><td colspan=12><hr></td></tr>");
				print("<tr><td colspan=8 bgcolor=#1c1c1c><strong>&nbsp; $tradedepot_lang[msg_6]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[kaufgesuche]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[ware]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[dp]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[wert]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[zeit]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[minpreis]<br>Preis</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[instock]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[maxgebot]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[gesuchsmenge]</strong></td></tr>");

		$fleetconfig = ${"fleet_"."3"};
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options3.= "<option value=\"$shiptype\">$shipname</option>";
			generateResline($shiptype, $restype, $ums_user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, "3");
		}
		print("<tr><td colspan=12><hr></td></tr>");
		print("<tr><td colspan=12>");
		print("<form name=\"tradeform3\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=3>");
		print("<input type=hidden name=action value=depotsell>");
		print("$tradedepot_lang[verkaufe] <input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 60;\"> <select name=\"shiptype\">$options3</select> $tradedepot_lang[msg_4_2] <input type=\"text\" name=\"price\" value=\"\" style=\"width : 60;\"> $restext $tradedepot_lang[msg_4_3] <input type=\"submit\" name=\"submit\" value=\"$tradedepot_lang[ausfuehren]\">");
		print("</form>");
		print("</td></tr>");
		print("<tr><td colspan=12><hr></td></tr>");
				print("<tr><td colspan=8 bgcolor=#1c1c1c><strong>&nbsp; $tradedepot_lang[msg_7]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[verkaufsangebote]</strong></td><td colspan=2 bgcolor=#1c1c1c align=center><strong>$tradedepot_lang[kaufgesuche]</strong></td></tr>");
		print("<tr><td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[ware]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[dp]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[wert]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>&nbsp;</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[zeit]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[minpreis]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[instock]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[maxgebot]</strong></td>
				<td bgcolor=#1c1c1c align=center style=\"font-size: 11px\" valign=bottom><strong>$tradedepot_lang[gesuchsmenge]</strong></td></tr>");

		$fleetconfig = ${"fleet_"."4"};
		for ($i=81;$i<89;$i++)
		{
			$shiptype = "e".$i;
			$shipname = $fleetconfig["$shiptype"]["name"];
			$options4.= "<option value=\"$shiptype\">$shipname</option>";
			generateResline($shiptype, $restype, $ums_user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, "4");
		}

		print("<tr><td colspan=12><hr></td></tr>");
		print("<tr><td colspan=12>");
		print("<form name=\"tradeform4\" action=\"trade.php\" method=\" post\" id=\"tradeform\">");
		print("<input type=hidden name=currency value=$restype>");
		print("<input type=hidden name=viewmode value=$viewmode>");
		print("<input type=hidden name=race value=4>");
		print("<input type=hidden name=action value=depotsell>");
		print("$tradedepot_lang[verkaufe] <input type=\"text\" name=\"tradeamount\" value=\"\" style=\"width : 60;\"> <select name=\"shiptype\">$options4</select> $tradedepot_lang[msg_4_2] <input type=\"text\" name=\"price\" value=\"\" style=\"width : 60;\"> $restext $tradedepot_lang[msg_4_3] <input type=\"submit\" name=\"submit\" value=\"$tradedepot_lang[ausfuehren]\">");
		print("</form>");
		print("</td></tr>");
		print("</table>");
}

	function generateResline($shiptype, $currency, $user_id, $fleetconfig, $restyp01, $restyp02, $restyp03, $restyp04, $race)
	{
                 global $tradedepot_lang;
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
			$minpricemenge = $minprice = $tradedepot_lang[keine];
		}
		if ($maxpricemenge == 0)
		{
			$maxpricemenge = $maxprice = $tradedepot_lang[keine];
		}
		$shipname = $fleetconfig["$shiptype"]["name"];
		$shipvalue = $fleetconfig["$shiptype"]["value"];
		$shipmax05value = ($shipvalue + ($shipvalue/100*5));
		$shipmax10value = ($shipvalue + ($shipvalue/100*10));
		$shipmax15value = ($shipvalue + ($shipvalue/100*15));
		$shipmax20value = ($shipvalue + ($shipvalue/100*20));
		$shiptransfer = $fleetconfig["$shiptype"]["trade_time"];
		$f_query = "select $shiptype from de_trade_depot WHERE user_id='$user_id' AND race='$race'";
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
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform.price.value='$shipmax05value';\">$shipname</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipmax05value';\"><font color=green>".number_format($depot_count,0,",",".")."</font></td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipvalue';\">".number_format(floatval($shipvalue),0,",",".")."</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipmax05value';\">+5%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipmax10value';\">+10%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipmax15value';\">+15%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex='$index'; document.tradeform{$race}.tradeamount.value='$depot_count'; document.tradeform{$race}.price.value='$shipmax20value';\">+20%</td>
				<td bgcolor=#222222 align=right style=\"font-size: 10px\">$shiptransfer</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex=$index; document.tradeform{$race}.tradeamount.value='$maxbuyamount'; document.tradeform{$race}.price.value='$minprice';\">".number_format(floatval($minprice),2,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex=$index; document.tradeform{$race}.tradeamount.value='$maxbuyamount'; document.tradeform{$race}.price.value='$minprice';\">".number_format(floatval($minpricemenge),0,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex=$index; document.tradeform{$race}.tradeamount.value='$maxsellamount'; document.tradeform{$race}.price.value='$maxprice';\">".number_format(floatval($maxprice),2,",",".")."</td>
				<td bgcolor=#222222 align=center style=\"font-size: 10px\" onclick=\"document.tradeform{$race}.shiptype.selectedIndex=$index; document.tradeform{$race}.tradeamount.value='$maxsellamount'; document.tradeform{$race}.price.value='$maxprice';\">".number_format(floatval($maxpricemenge),0,",",".")."</td></tr>");


	}
?>