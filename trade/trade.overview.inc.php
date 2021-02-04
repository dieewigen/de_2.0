<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.overview.lang.php';

	$datestring = date("d.m.Y G:i");
	print("<br>");
	print("<div align=center><table width=600>");
	print("<tr><td><h2>$tradeoverview_lang[overviewfor] $ums_spielername - $datestring</h2></td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeoverview_lang[angeboteundgesuche]:</h3></td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_resoffer WHERE user_id='$ums_user_id'");
	$numoffers = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numoffers = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_1] $numoffers $tradeoverview_lang[msg_2]</td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_resrequest WHERE user_id='$ums_user_id'");
	$numrequests = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numrequests = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_1] $numrequests $tradeoverview_lang[msg_3]</td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_fleetoffer WHERE user_id='$ums_user_id'");
	$numoffers = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numoffers = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_1] $numoffers $tradeoverview_lang[msg_4]</td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_fleetrequest WHERE user_id='$ums_user_id'");
	$numrequests = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numrequests = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_1] $numrequests $tradeoverview_lang[msg_5]</td></tr>");
	print("<tr><td align=right><a href=\"trade.php?viewmode=view_own\">$tradeoverview_lang[zurverwaltungsseite]...</a></td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeoverview_lang[umsatzundperformance]:</h3></td></tr>");
	$result = @mysql_query("select tradescore, trade_sell_sum, trade_buy_sum from de_user_data WHERE user_id='$ums_user_id'");
	$numrequests = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$score = $values[0];
		$sellsum = $values[1];
		$buysum = $values[2];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[gesamtumsatzverkaeufe]: $sellsum $tradeoverview_lang[energieeinheiten]</td></tr>");
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[gesamtumsatzeinkaeufe]: $buysum $tradeoverview_lang[energieeinheiten]</td></tr>");
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[bishererzieltehandelspunkte]: $score $tradeoverview_lang[handelspunkte]</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeoverview_lang[sektorsteuer]:</h3></td></tr>");
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_6] $ssatz %</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeoverview_lang[allgemeinehandelsuebersicht]:</h3></td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_resoffer");
	$numoffers = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numoffers = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_7] $numoffers $tradeoverview_lang[msg_8]</td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_resrequest");
	$numrequests = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numrequests = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_7] $numrequests $tradeoverview_lang[msg_9]</td></tr>");
	print("<tr><td align=right><a href=\"trade.php?viewmode=m_res\">$tradeoverview_lang[zumresourcenhandel]...</a></td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_fleetoffer");
	$numoffers = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numoffers = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_7] $numoffers $tradeoverview_lang[msg_10]</td></tr>");
	$result = @mysql_query("select count(*) from $de_trade_fleetrequest");
	$numrequests = "0";
	if ($result)
	{
		$values = @mysql_fetch_array($result);
		$numrequests = $values[0];
	}
	print("<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$tradeoverview_lang[msg_7] $numrequests $tradeoverview_lang[msg_11]</td></tr>");
	print("<tr><td align=right><a href=\"trade.php?viewmode=m_fleet\">$tradeoverview_lang[zumschiffshandel]...</a></td></tr>");
	print("</table></div>");
?>