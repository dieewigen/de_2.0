<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.viewown.lang.php'; 
	$sortby = "timestamp";
	$ordertype = "ASC";
	$datestring = date("d.m.Y G:i");
	print("<br>");
	print("<div align=center><table width=600>");
	print("<tr><td><h2>$tradeviewown_lang[angebotsverwaltung] $ums_spielername - $datestring</h2></td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[aktiveressourcenangebote]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
	print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[erstellungsdatum]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[preis]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[waehrung]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[steuer]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[gebuehr]</strong></td><td bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_trade_resoffer WHERE user_id='$ums_user_id' ORDER BY $sortby $ordertype";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["user_id"];
				$sell_type  = $r1_values["sell_type"];
				$amount		= $r1_values["amount"];
				$price		= $r1_values["price"];
				$currency	= $r1_values["currency"];
				$timestamp	= $r1_values["timestamp"];
				$in_ticks	= $r1_values["remaining_ticks"];
				$ssatz		= $r1_values["ssatz"];
				$sell_type	= $$sell_type;
				$currency 	= $$currency;
				$created 	= date("d.m.Y G:i", $timestamp);
				if ($ums_rasse==2)
				{
					$stornoamount = round(($amount/100) * $traderstornotax);
				}
				else
				{
					$stornoamount = round(($amount/100) * $stornotax);
				}
				$number = $r_count+1;
				print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$created</td><td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$price</td><td align=center bgcolor=#222222>$currency</td><td align=center bgcolor=#222222>$ssatz %</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>$stornoamount</td><td align=center bgcolor=#222222><a onClick=\"return confirm('".$tradeviewown_lang[canceloffer]."')\" href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=resoffer\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
			}
		}
	}
	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[aktiveressourcenanfragen]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
         print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[erstellungsdatum]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[preis]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[waehrung]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[steuer]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[gebuehr]</strong></td><td bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_trade_resrequest WHERE user_id='$ums_user_id' ORDER BY $sortby $ordertype";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["user_id"];
				$buy_type  	= $r1_values["buy_type"];
				$amount		= $r1_values["amount"];
				$price		= $r1_values["price"];
				$currency	= $r1_values["currency"];
				$timestamp	= $r1_values["timestamp"];
				$in_ticks	= $r1_values["remaining_ticks"];
				$ssatz		= $r1_values["ssatz"];
				$buy_type	= $$buy_type;
				$currency 	= $$currency;
				$created 	= date("d.m.Y G:i", $timestamp);
				if ($ums_rasse==2)
				{
					$stornoamount = round(($amount/100) * $req_traderstornotax);
				}
				else
				{
					$stornoamount = round(($amount/100) * $req_stornotax);
				}
				$in_ticks = "--";
				$number = $r_count+1;
				print("<tr><td align=center bgcolor=#222222>{$number}</td><td align=center bgcolor=#222222>$created</td><td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$buy_type</td><td align=center bgcolor=#222222>$price</td><td align=center bgcolor=#222222>$currency</td><td align=center bgcolor=#222222>$ssatz %</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>$stornoamount</td><td align=center bgcolor=#222222><a href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=resrequest\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
			}
		}
	}
	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[aktiveflottenangebote]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
         print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[erstellungsdatum]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[preis]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[waehrung]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[steuer]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[gebuehr]</strong></td><td bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_trade_fleetoffer WHERE user_id='$ums_user_id' ORDER BY $sortby $ordertype";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["user_id"];
				$sell_type  = $r1_values["sell_type"];
				$amount		= $r1_values["amount"];
				$price		= $r1_values["price"];
				$currency	= $r1_values["currency"];
				$timestamp	= $r1_values["timestamp"];
				$in_ticks	= $r1_values["remaining_ticks"];
				$ssatz		= $r1_values["ssatz"];
				$race		= $r1_values["race"];
				$fromdepot  = $r1_values["fromdepot"];
				$tmp_fleetconfig = ${"fleet_"."$race"};
				$sell_type	= $tmp_fleetconfig[$sell_type]["name"];
				$currency 	= $$currency;
				$created 	= date("d.m.Y G:i", $timestamp);
				$stornoamount = 0;
				if ($amount > 3)
				{
					if ($ums_rasse == 2)
					{
						$stornoamount = round(($amount/100*$offer_traderstornotax),0);
					}
					else
					{
						$stornoamount = round(($amount/100*$offer_stornotax),0);
					}
				}
				$number = $r_count+1;
				if ($fromdepot == "1")
				{
					$stornoamount = 0;
				}
				print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$created</td><td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$price</td><td align=center bgcolor=#222222>$currency</td><td align=center bgcolor=#222222>$ssatz %</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>$stornoamount</td><td align=center bgcolor=#222222><a onClick=\"return confirm('".$tradeviewown_lang[canceloffer]."')\" href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=fleetoffer\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
			}
		}
	}
	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[aktiveflottenanfragen]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
         print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[erstellungsdatum]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[preis]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[waehrung]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[steuer]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[gebuehr]</strong></td><td bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_trade_fleetrequest WHERE user_id='$ums_user_id' ORDER BY $sortby $ordertype";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["user_id"];
				$buy_type  	= $r1_values["buy_type"];
				$amount		= $r1_values["amount"];
				$price		= $r1_values["price"];
				$currency	= $r1_values["currency"];
				$timestamp	= $r1_values["timestamp"];
				$in_ticks	= $r1_values["remaining_ticks"];
				$ssatz		= $r1_values["ssatz"];
				$buy_type	= $fleetconfig[$buy_type]["name"];;
				$currency 	= $$currency;
				$created 	= date("d.m.Y G:i", $timestamp);
				$stornoamount = 0;
				$in_ticks = "--";
				$number = $r_count+1;
				print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$created</td><td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$buy_type</td><td align=center bgcolor=#222222>$price</td><td align=center bgcolor=#222222>$currency</td><td align=center bgcolor=#222222>$ssatz %</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>$stornoamount</td><td align=center bgcolor=#222222><a href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=fleetrequest\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
			}
		}
	}
	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[schiffeimlieferanflug]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
	print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[lieferungsstart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[wert] ($tradeviewown_lang[multiplex])</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[lieferunginticks]</strong></td></tr>");
	$r2_query = "SELECT * FROM $de_trade_fleettransit WHERE target_id='$ums_user_id' ORDER BY id ASC";
	$r2_result = @mysql_query($r2_query);
	if ($r2_result)
	{
		$r2_numrows = mysql_num_rows($r2_result);
		if ($r2_numrows > 0)
		{
			$val_sum = 0;
			$amount_sum = 0;
			for ($r_count=0;$r_count<$r2_numrows;$r_count++)
			{
				$r2_values 	= mysql_fetch_array($r2_result);
				$id 		= $r2_values["id"];
				$uid 		= $r2_values["user_id"];
				$timestamp	= $r2_values["timestamp"];
				$shiptype  	= $r2_values["shiptype"];
				$amount		= $r2_values["amount"];
				$in_ticks	= $r2_values["remaining_ticks"];
				$shipname	= $fleetconfig[$shiptype]["name"];
				$created 	= date("d.m.Y G:i", $timestamp);
				$value = number_format($fleetconfig[$shiptype]["value"] * $amount, 0, "", ".");
				$number = $r_count+1;
				$val_sum = $val_sum + $fleetconfig[$shiptype]["value"] * $amount;
				$amount_sum = $amount_sum + $amount;
				print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$created</td><td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$shipname</td><td align=center bgcolor=#222222>$value</td><td align=center bgcolor=#222222>$in_ticks</td></tr>");
			}
			$point_sum = number_format($val_sum / 10, 0, "", ".");
			$val_sum = number_format($val_sum, 0, "", ".");

			print("<tr><td align=center bgcolor=#222222><td align=center bgcolor=#222222></td><td align=center bgcolor=#222222><strong>$amount_sum</strong></td><td align=center bgcolor=#222222></td><td align=center bgcolor=#222222><strong>$val_sum</strong></td><td align=center bgcolor=#222222><strong>$point_sum Punkte</strong></td></tr>");
		}
	}
	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[laufendeauktionen]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
	print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[anzahlgebote]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[hoechstgebot]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[hoechstbietender]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_tauction WHERE seller='$ums_user_id' ORDER BY ticks";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["seller"];
				$sell_type  = "$tradeviewown_lang[tronic]";
				$amount		= $r1_values["amount"];
				$maxbid		= $r1_values["maxbid"];
				$countbids	= $r1_values["bids"];
				$in_ticks	= $r1_values["ticks"];
				$biddername	= $r1_values["biddername"];
				$number = $r_count+1;
				if ($countbids == 0)
				{
					print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$countbids</td><td align=center bgcolor=#222222>$maxbid</td><td align=center bgcolor=#222222>$biddername</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222><a href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=tronic\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
				}
				else
				{
					print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$countbids</td><td align=center bgcolor=#222222>$maxbid</td><td align=center bgcolor=#222222>$biddername</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>&nbsp</td></tr>");
				}
			}
		}
	}


	print("</table>");
	print("</td></tr>");
	print("<tr><td><hr></td></tr>");
	print("<tr><td><h3>$tradeviewown_lang[hoechstbietender]:</h3></td></tr>");
	print("<tr><td>");
	print("<table width=600>");
	print("<tr><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[nr]</strong><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[menge]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[warenart]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[anzahlgebote]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[hoechstgebot]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[hoechstbietender]</strong></td><td align=center bgcolor=#1c1c1c><strong>$tradeviewown_lang[ablauf]</strong></td><td align=center bgcolor=#1c1c1c>&nbsp;</td></tr>");
	$r1_query = "SELECT * FROM $de_tauction WHERE bidder='$ums_user_id' ORDER BY ticks";
	$r1_result = @mysql_query($r1_query);
	if ($r1_result)
	{
		$r1_numrows = mysql_num_rows($r1_result);
		if ($r1_numrows > 0)
		{
			for ($r_count=0;$r_count<$r1_numrows;$r_count++)
			{
				$r1_values 	= mysql_fetch_array($r1_result);
				$id 		= $r1_values["id"];
				$uid 		= $r1_values["seller"];
				$sell_type  = "$tradeviewown_lang[tronic]";
				$amount		= $r1_values["amount"];
				$maxbid		= $r1_values["maxbid"];
				$countbids	= $r1_values["bids"];
				$in_ticks	= $r1_values["ticks"];
				$biddername	= $r1_values["biddername"];
				$number = $r_count+1;
				if ($countbids == 0)
				{
					print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$countbids</td><td align=center bgcolor=#222222>$maxbid</td><td align=center bgcolor=#222222>$biddername</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222><a href=\"trade.php?viewmode=view_own&action=storno&id=$id&type=tronic\"><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_storno.gif\" alt=\"$tradeviewown_lang[stornieren]\" border=0></a></td></tr>");
				}
				else
				{
					print("<tr><td align=center bgcolor=#222222>{$number}<td align=center bgcolor=#222222>$amount</td><td align=center bgcolor=#222222>$sell_type</td><td align=center bgcolor=#222222>$countbids</td><td align=center bgcolor=#222222>$maxbid</td><td align=center bgcolor=#222222>$biddername</td><td align=center bgcolor=#222222>$in_ticks</td><td align=center bgcolor=#222222>&nbsp</td></tr>");
				}
			}
		}
	}

	print("</table>");
	print("</td></tr>");
?>