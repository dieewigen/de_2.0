<?php
include '../inc/lang/'.$sv_server_lang.'_trade.tick.lang.php';

function doTradeTick()
{
	global $tradetick_lang;
	global $sv_server_lang;
	global $sv_deactivate_trade;
	
	$intick = 1;
	include("trade.config.inc.php");
	list($usec, $sec) = explode(" ",microtime());
    $starttime = ((float)$usec + (float)$sec);
	print("<br><hr><br><strong>Beginning Tradetick...</strong><br><br>");

	/*
	$stat_result = mysql_query("select trade_active from de_system");
	$stat_val = mysql_fetch_array($stat_result);
	$active = $stat_val[0];
	if ($active == "0")
	{
		print("Handel ist deaktiviert. Abbruch!");
		return;
	}
	*/

	if ($sv_deactivate_trade == 1)
	{
		print("Handel ist deaktiviert. Abbruch!");
		return;
	}	
	
	//Ressourcenrequests abarbeiten
	$res_request_result = mysql_query("SELECT id FROM de_trade_resrequest ORDER BY timestamp ASC");
	if ($res_request_result)
	{
		$res_request_numrows = mysql_num_rows($res_request_result);
		print("$res_request_numrows active resource requests found.<br>");
		print("Using fast index (combined)<br>");
		if ($res_request_numrows > 0)
		{
			for ($res_request_run = 0; $res_request_run < $res_request_numrows; $res_request_run++)
			{
				$res_request_values = mysql_fetch_array($res_request_result);
				$res_request_id = $res_request_values["id"];
				execRessRequest($res_request_id);
			}
		}
	}

	//Flottenrequests abarbeiten
	$fleet_request_result = mysql_query("SELECT id FROM de_trade_fleetrequest ORDER BY timestamp ASC");
	if ($fleet_request_result)
	{
		$fleet_request_numrows = mysql_num_rows($fleet_request_result);
		print("$fleet_request_numrows active fleet requests found.<br>");
		print("Using fast index (combined)<br>");
		if ($fleet_request_numrows > 0)
		{
			for ($fleet_request_run = 0; $fleet_request_run < $fleet_request_numrows; $fleet_request_run++)
			{
				$fleet_request_values = mysql_fetch_array($fleet_request_result);
				$fleet_request_id = $fleet_request_values["id"];
				execFleetRequest($fleet_request_id);
			}
		}
	}

	//handelseinträge um eins zurücksetzen
	mysql_query("UPDATE de_trade_resoffer SET remaining_ticks = remaining_ticks-1 WHERE remaining_ticks > 0");
	mysql_query("UPDATE de_trade_fleetoffer SET remaining_ticks = remaining_ticks-1 WHERE remaining_ticks > 0");

	//mysql_query("UPDATE de_trade_resrequest SET remaining_ticks = remaining_ticks-1");

	//abgelaufene Ressourcen zurückbuchen
	$bkback_result = mysql_query("SELECT * FROM de_trade_resoffer WHERE remaining_ticks='0'");
	if ($bkback_result)
	{
		$bkback_numrows = mysql_numrows($bkback_result);
		if ($bkback_numrows > 0)
		{
			for ($bk_run=0; $bk_run<$bkback_numrows; $bk_run++)
			{
				$bk_values = mysql_fetch_array($bkback_result);
				$bk_id = $bk_values["id"];
				$bk_uid = $bk_values["user_id"];
				$bk_amount = $bk_values["amount"];
				$bk_res = $bk_values["sell_type"];
				mysql_query("DELETE FROM de_trade_resoffer WHERE id='$bk_id'");
				if ($bk_res == "m")
				{
					mysql_query("UPDATE de_user_data SET restyp01=restyp01+$bk_amount, newnews='1' WHERE user_id='$bk_uid'");
					$time=strftime("%Y%m%d%H%M%S");
					mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$bk_uid', '12', '$time', '$tradetick_lang[msg_1_1] $bk_amount $tradetick_lang[multiplex] $tradetick_lang[msg_1_2].', '0')");
				}
				elseif ($bk_res == "d")
				{
					mysql_query("UPDATE de_user_data SET restyp02=restyp02+$bk_amount, newnews='1' WHERE user_id='$bk_uid'");
					$time=strftime("%Y%m%d%H%M%S");
					mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$bk_uid', '12', '$time', '$tradetick_lang[msg_1_1] $bk_amount $tradetick_lang[dyharra] $tradetick_lang[msg_1_2].', '0')");
				}
				elseif ($bk_res == "i")
				{
					mysql_query("UPDATE de_user_data SET restyp03=restyp03+$bk_amount, newnews='1' WHERE user_id='$bk_uid'");
					$time=strftime("%Y%m%d%H%M%S");
					mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$bk_uid', '12', '$time', '$tradetick_lang[msg_1_1] $bk_amount $tradetick_lang[iradium] $tradetick_lang[msg_1_2].', '0')");
				}
				elseif ($bk_res == "e")
				{
					mysql_query("UPDATE de_user_data SET restyp04=restyp04+$bk_amount, newnews='1' WHERE user_id='$bk_uid'");
					$time=strftime("%Y%m%d%H%M%S");
					mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$bk_uid', '12', '$time', '$tradetick_lang[msg_1_1] Ihnen $bk_amount $tradetick_lang[eternium] $tradetick_lang[msg_1_2].', '0')");
				}
			}
		}
	}

	//abgelaufene Flotten zurückbuchen
	$flbkback_result = mysql_query("SELECT * FROM de_trade_fleetoffer WHERE remaining_ticks='0'");
	if ($flbkback_result)
	{
		$flbkback_numrows = mysql_numrows($flbkback_result);
		if ($flbkback_numrows > 0)
		{
			for ($flbk_run=0; $flbk_run<$flbkback_numrows; $flbk_run++)
			{
				$flbk_values = mysql_fetch_array($flbkback_result);
				$flbk_id = $flbk_values["id"];
				$flbk_uid = $flbk_values["user_id"];
				$flbk_amount = $flbk_values["amount"];
				$flbk_fleettype = $flbk_values["sell_type"];
				$flbk_race = $flbk_values["race"];
				$fromdepot = $flbk_values["fromdepot"];
				$fleetconfig = ${"fleet_"."$flbk_race"};

				//$m_value = $fleetconfig[$flbk_fleettype]["value"] * $flbk_amount;
				//$t_value = $fleetconfig[$flbk_fleettype]["t"] * $flbk_amount;
				//$m_value = $m_value - ($t_value * 5000);
				$flbk_shipname = $fleetconfig[$flbk_fleettype]["name"];

				mysql_query("DELETE FROM de_trade_fleetoffer WHERE id='$flbk_id'");
				
				if ($fromdepot == "1")
				{
					$sysnews = "$tradetick_lang[msg_2_1] ($flbk_amount $flbk_shipname) $tradetick_lang[msg_2_3]";
					mysql_query("UPDATE de_trade_depot SET $flbk_fleettype=$flbk_fleettype+$flbk_amount WHERE user_id='$flbk_uid}' AND race='$flbk_race'");
				}
				else 
				{
					$sysnews = "$tradetick_lang[msg_2_1] ($flbk_amount $flbk_shipname) $tradetick_lang[msg_2_2]";	
					mysql_query("UPDATE de_user_fleet SET $flbk_fleettype=$flbk_fleettype+$flbk_amount WHERE user_id='{$flbk_uid}-0'");
				}
				mysql_query("UPDATE de_user_data SET newnews='1' WHERE user_id='$flbk_uid'");
				$time=strftime("%Y%m%d%H%M%S");
				mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$flbk_uid', '12', '$time', '$sysnews', '0')");

			}
		}
	}

	//in Lieferung befindliche Schiffe um eins zurücksetzen
	mysql_query("UPDATE de_trade_fleettransit SET remaining_ticks = remaining_ticks-1 WHERE remaining_ticks > 0");

	//Ausgelieferte Schiffe buchen
	$trtback_result = mysql_query("SELECT * FROM de_trade_fleettransit WHERE remaining_ticks='0'");
	if ($trtback_result)
	{
		$trtback_numrows = mysql_numrows($trtback_result);
		if ($trtback_numrows > 0)
		{
			for ($trt_run=0; $trt_run<$trtback_numrows; $trt_run++)
			{
				$trt_values = mysql_fetch_array($trtback_result);
				$trt_id = $trt_values["id"];
				$trt_uid = $trt_values["target_id"];
				$trt_amount = $trt_values["amount"];
				$trt_fleettype = $trt_values["shiptype"];
				$trt_race = $trt_values["race"];
				$fleetconfig = ${"fleet_"."$trt_race"};

				$trt_shipname = $fleetconfig[$trt_fleettype]["name"];
				$trt_points = $fleetconfig[$trt_fleettype]["points"];
				$points = $trt_points * $trt_amount;

				$sysnews = "$tradetick_lang[msg_3_1] ($trt_amount $trt_shipname) $tradetick_lang[msg_3_2] $points $tradetick_lang[msg_3_3].";
				mysql_query("UPDATE de_user_data SET score=score+$points WHERE user_id='$trt_uid'");
				mysql_query("DELETE FROM de_trade_fleettransit WHERE id='$trt_id'");
				$bookquery = "UPDATE de_user_fleet SET `$trt_fleettype`=`$trt_fleettype`+$trt_amount where user_id = '".$trt_uid."-0'";
				mysql_query($bookquery);
				mysql_query("UPDATE de_user_data SET newnews='1' WHERE user_id='$trt_uid'");
				$time=strftime("%Y%m%d%H%M%S");
				mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$trt_uid', '12', '$time', '$sysnews', '0')");

			}
		}
	}


	list($usec, $sec) = explode(" ",microtime());
    $endtime = ((float)$usec + (float)$sec);
	$time = $endtime - $starttime;
	$exec_time = round($time, 3);
	print("<br><strong>Tradetick successfull done in $exec_time seconds</strong><br><hr><br>");
}

function execFleetRequest($id)
{
         global $tradetick_lang;
         $query = "SELECT * FROM de_trade_fleetrequest WHERE id = '$id'";
	$result = mysql_query($query);
	if ($result)
	{
		$numrows = mysql_num_rows($result);
		if ($numrows == 1)
		{
			//print("Fleet request #$id successfully loaded from database. Locking active row.<br>");
			$values 			= mysql_fetch_array($result);
			$request_id 		= $values["id"];
			$request_user_id 	= $values["user_id"];
			$request_buy_type	= $values["buy_type"];
			$request_amount		= $values["amount"];
			$request_price		= $values["price"];
			$request_currency	= $values["currency"];
			$request_locked		= $values["locked"];
			$request_race		= $values["race"];
			$request_timestamp	= $values["timestamp"];
			$request_r_ticks	= $values["remaining_ticks"];
			$request_ssatz		= $values["ssatz"];
			$request_sector		= $values["sector"];

			$query = "SELECT * FROM de_trade_fleetoffer WHERE sell_type = '$request_buy_type' AND currency='$request_currency' and race='$request_race' AND price <= '$request_price' AND user_id!='$request_user_id' ORDER BY price,timestamp ASC";
			$result = mysql_query($query);
			if ($result)
			{
				$numrows = mysql_num_rows($result);
				if ($numrows > 0)
				{
					//print("$numrows matching offers found for request #$id - executing #$id<br>");
					for ($id_run=0; $id_run < $numrows; $id_run++)
					{
						$values = mysql_fetch_array($result);
						$offer_id_array[$id_run] = $values["id"];
					}
					//print("Booking request #$id (User: $request_user_id, Wanted Fleet: $request_buy_type, Amount: $request_amount, Price: $request_price, Currency: $request_currency)<br>");
					for ($id_run=0; $id_run < $numrows; $id_run++)
					{
						$offer_id = $offer_id_array[$id_run];
						//print("Reading offer id $offer_id<br>");
						$query = "SELECT * FROM de_trade_fleetoffer WHERE id = '$offer_id'";
						$result = mysql_query($query);
						if ($result)
						{
							$offer_numrows = mysql_num_rows($result);
							if ($offer_numrows == 1)
							{
								$values 			= mysql_fetch_array($result);
								$offer_id 			= $values["id"];
								$offer_user_id 		= $values["user_id"];
								$offer_sell_type	= $values["sell_type"];
								$offer_amount		= $values["amount"];
								$offer_price		= $values["price"];
								$offer_currency		= $values["currency"];
								$offer_locked		= $values["locked"];
								$offer_race			= $values["race"];
								$offer_timestamp	= $values["timestamp"];
								$offer_r_ticks		= $values["remaining_ticks"];
								$offer_ssatz		= $values["ssatz"];
								$offer_sector		= $values["sector"];
								$fromdepot		    = $values["fromdepot"];
								

								//print("Booking offer #$id (User: $offer_user_id, Sold Fleet: $offer_sell_type, Amount: $offer_amount, Price: $offer_price, Currency: $request_currency, Timestamp: $offer_timestamp)<br>");
								$booked_amount = 0;
								if ($request_amount == $offer_amount)
								{
									$booked_amount = $request_amount;
									$request_amount = 0;
									$offer_amount = 0;
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Daten aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookFleetTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price, $fromdepot);
									mysql_query("DELETE FROM de_trade_fleetrequest WHERE id = '$request_id'");
									mysql_query("DELETE FROM de_trade_fleetoffer WHERE id = '$offer_id'");
									break;
								}
								elseif($request_amount > $offer_amount)
								{
									$booked_amount = $offer_amount;
									$request_amount = $request_amount - $booked_amount;
									$offer_amount = 0;
									//Angebot aus DB löschen, beide spieler benachrichtigen und Daten aktualisieren
									//print("$booked_amount Units booked. Still remaining $request_amount amount units of $request_buy_type to book<br>");
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Daten aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookFleetTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price, $fromdepot);
									mysql_query("DELETE FROM de_trade_fleetoffer WHERE id = '$offer_id'");
									mysql_query("UPDATE de_trade_fleetrequest SET amount = '$request_amount' WHERE id = '$request_id'");
								}
								elseif ($request_amount < $offer_amount)
								{
									$booked_amount = $request_amount;
									$request_amount = 0;
									$offer_amount = $offer_amount - $booked_amount;
									//request aus DB löschen, beide spieler benachrichtigen und Daten aktualisieren
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Daten aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookFleetTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price, $fromdepot);
									mysql_query("DELETE FROM de_trade_fleetrequest WHERE id = '$request_id'");
									mysql_query("UPDATE de_trade_fleetoffer SET amount = '$offer_amount' WHERE id = '$offer_id'");
									break;
								}
							}
						}
					}
				}
				else
				{
					//print("No matching offers found for request #$id - skipping #$id<br>");
				}
			}
		}
	}
}

function execRessRequest($id)
{
         global $tradetick_lang;
         $query = "SELECT * FROM de_trade_resrequest WHERE id = '$id'";
	$result = mysql_query($query);
	if ($result)
	{
		$numrows = mysql_num_rows($result);
		if ($numrows == 1)
		{
			//print("Resource request #$id successfully loaded from database. Locking active row.<br>");
			$values 			= mysql_fetch_array($result);
			$request_id 		= $values["id"];
			$request_user_id 	= $values["user_id"];
			$request_buy_type	= $values["buy_type"];
			$request_amount		= $values["amount"];
			$request_price		= $values["price"];
			$request_currency	= $values["currency"];
			$request_locked		= $values["locked"];
			$request_race		= $values["race"];
			$request_timestamp	= $values["timestamp"];
			$request_r_ticks	= $values["remaining_ticks"];
			$request_ssatz		= $values["ssatz"];
			$request_sector		= $values["sector"];

			//print("Searching for matching offers for request #$id<br>");
			$query = "SELECT * FROM de_trade_resoffer WHERE sell_type = '$request_buy_type' AND currency='$request_currency' AND price <= '$request_price' AND user_id!='$request_user_id' ORDER BY price,timestamp ASC";
			$result = mysql_query($query);
			if ($result)
			{
				$numrows = mysql_num_rows($result);
				if ($numrows > 0)
				{
					//print("$numrows matching offers found for request #$id - executing #$id<br>");
					for ($id_run=0; $id_run < $numrows; $id_run++)
					{
						$values = mysql_fetch_array($result);
						$offer_id_array[$id_run] = $values["id"];
					}
					//print("Booking request #$id (User: $request_user_id, Wanted res: $request_buy_type, Amount: $request_amount, Price: $request_price, Currency: $request_currency)<br>");
					for ($id_run=0; $id_run < $numrows; $id_run++)
					{
						$offer_id = $offer_id_array[$id_run];
						//print("Reading offer id $offer_id<br>");
						$query = "SELECT * FROM de_trade_resoffer WHERE id = '$offer_id'";
						$result = mysql_query($query);
						if ($result)
						{
							$offer_numrows = mysql_num_rows($result);
							if ($offer_numrows == 1)
							{
								$values 			= mysql_fetch_array($result);
								$offer_id 			= $values["id"];
								$offer_user_id 		= $values["user_id"];
								$offer_sell_type	= $values["sell_type"];
								$offer_amount		= $values["amount"];
								$offer_price		= $values["price"];
								$offer_currency		= $values["currency"];
								$offer_locked		= $values["locked"];
								$offer_race			= $values["race"];
								$offer_timestamp	= $values["timestamp"];
								$offer_r_ticks		= $values["remaining_ticks"];
								$offer_ssatz		= $values["ssatz"];
								$offer_sector		= $values["sector"];

								//print("Booking offer #$id (User: $offer_user_id, Sold res: $offer_sell_type, Amount: $offer_amount, Price: $offer_price, Currency: $request_currency, Timestamp: $offer_timestamp)<br>");
								$booked_amount = 0;
								if ($request_amount == $offer_amount)
								{
									$booked_amount = $request_amount;
									$request_amount = 0;
									$offer_amount = 0;
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Ress aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price);
									mysql_query("DELETE FROM de_trade_resrequest WHERE id = '$request_id'");
									mysql_query("DELETE FROM de_trade_resoffer WHERE id = '$offer_id'");
									break;
								}
								elseif($request_amount > $offer_amount)
								{
									$booked_amount = $offer_amount;
									$request_amount = $request_amount - $booked_amount;
									$offer_amount = 0;
									//Angebot aus DB löschen, beide spieler benachrichtigen und ress aktualisieren
									//print("$booked_amount Units booked. Still remaining $request_amount amount units of $request_buy_type to book<br>");
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Ress aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price);
									mysql_query("DELETE FROM de_trade_resoffer WHERE id = '$offer_id'");
									mysql_query("UPDATE de_trade_resrequest SET amount = '$request_amount' WHERE id = '$request_id'");
								}
								elseif ($request_amount < $offer_amount)
								{
									$booked_amount = $request_amount;
									$request_amount = 0;
									$offer_amount = $offer_amount - $booked_amount;
									//request aus DB löschen, beide spieler benachrichtigen und ress aktualisieren
									//Angebot und request aus DB löschen, beide spieler benachrichtigen und Ress aktualisieren
									//berechnen, was käufer bezahlen muss
									$buyer_to_pay = $booked_amount * $offer_price;
									//berechnen was käufer geboten hat
									$buyer_max_pay = $booked_amount * $request_price;
									//berechnen, was er wegen eines ev. günstigeren gebots zurückbekommt
									$buyer_payback = $buyer_max_pay - $buyer_to_pay;
									bookTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price);
									mysql_query("DELETE FROM de_trade_resrequest WHERE id = '$request_id'");
									mysql_query("UPDATE de_trade_resoffer SET amount = '$offer_amount' WHERE id = '$offer_id'");
									break;
								}
							}
						}
					}
				}
				else
				{
					//print("No matching offers found for request #$id - skipping #$id<br>");
				}
			}
		}
	}
	//print("<br>");
}

function bookTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price)
{
         global $tradetick_lang;
         $m = $tradetick_lang[multiplex];
	$d = $tradetick_lang[dyharra];
	$i = $tradetick_lang[iradium];
	$e = $tradetick_lang[eternium];
	$c_displayname = $$request_currency;
	$buy_displayname = $$request_buy_type;

	$buyquery 	= "UPDATE de_user_data SET ";
	$sellquery 	= "UPDATE de_user_data SET ";
	$taxamount 	= (($buyer_to_pay/100) * $offer_ssatz);
	//print("$taxamount<br>");
	/*if ($offer_race != "2")
	{
		$buyer_to_pay = $buyer_to_pay - $taxamount;
	}*/
	$buyer_to_pay = $buyer_to_pay - $taxamount;
	if ($request_buy_type == "m")
	{
		$buyquery .= "restyp01=restyp01+$booked_amount ";
	}
	elseif ($request_buy_type == "d")
	{
		$buyquery .= "restyp02=restyp02+$booked_amount ";
	}
	elseif ($request_buy_type == "i")
	{
		$buyquery .= "restyp03=restyp03+$booked_amount ";
	}
	elseif ($request_buy_type == "e")
	{
		$buyquery .= "restyp04=restyp04+$booked_amount ";
	}
	if ($request_currency == "m")
	{
		$sellquery .= "restyp01=restyp01+$buyer_to_pay ";
		if ($buyer_payback > 0)
		{
			$buyquery.= ", restyp01=restyp01+$buyer_payback ";
		}
		$sekquery = "UPDATE de_sector SET restyp01=restyp01+$taxamount WHERE sec_id='$offer_sector'";
		$donationquery = "UPDATE de_user_data SET spend01=spend01+$taxamount WHERE user_id = '$offer_user_id'";
	}
	elseif ($request_currency == "d")
	{
		$taxamount = $taxamount / 2;
		$buyer_to_pay = $buyer_to_pay / 2;
		$sellquery .= "restyp02=restyp02+$buyer_to_pay ";
		if ($buyer_payback > 0)
		{
			$buyer_payback = $buyer_payback / 2;
			$buyquery.= ", restyp02=restyp02+$buyer_payback ";
		}
		$sekquery = "UPDATE de_sector SET restyp02=restyp02+$taxamount WHERE sec_id='$offer_sector'";
		$donationquery = "UPDATE de_user_data SET spend02=spend02+$taxamount WHERE user_id = '$offer_user_id'";
	}
	elseif ($request_currency == "i")
	{
		$taxamount = $taxamount / 3;
		$buyer_to_pay = $buyer_to_pay / 3;
		$sellquery .= "restyp03=restyp03+$buyer_to_pay ";
		if ($buyer_payback > 0)
		{
			$buyer_payback = $buyer_payback / 3;
			$buyquery.= ", restyp03=restyp03+$buyer_payback ";
		}
		$sekquery = "UPDATE de_sector SET restyp03=restyp03+$taxamount WHERE sec_id='$offer_sector'";
		$donationquery = "UPDATE de_user_data SET spend03=spend03+$taxamount WHERE user_id = '$offer_user_id'";
	}
	elseif ($request_currency == "e")
	{
		$taxamount = $taxamount / 4;
		$buyer_to_pay = $buyer_to_pay / 4;
		$sellquery .= "restyp04=restyp04+$buyer_to_pay ";
		if ($buyer_payback > 0)
		{
			$buyer_payback = $buyer_payback / 4;
			$buyquery.= ", restyp04=restyp04+$buyer_payback ";
		}
		$sekquery = "UPDATE de_sector SET restyp04=restyp04+$taxamount WHERE sec_id='$offer_sector'";
		$donationquery = "UPDATE de_user_data SET spend04=spend04+$taxamount WHERE user_id = '$offer_user_id'";
	}
	$buyquery.= ", newnews='1' WHERE user_id = '$request_user_id'";
	$sellquery.= ", newnews='1' WHERE user_id = '$offer_user_id'";
	//print("$buyquery<br>");
	//print("$sellquery<br>");
	$buymessage = "$tradetick_lang[msg_4_1] $booked_amount $buy_displayname $tradetick_lang[msg_4_2] $buyer_to_pay $c_displayname. ";
	if ($buyer_payback > 0)
	{
		$buymessage .= "$tradetick_lang[msg_5_1] $buyer_payback $c_displayname $tradetick_lang[msg_5_2].";
	}
	$sellmessage = "$tradetick_lang[msg_6_1] $booked_amount $buy_displayname $tradetick_lang[msg_6_2] $buyer_to_pay $c_displayname $tradetick_lang[msg_6_3]. ";
	if ($offer_race == "2")
	{
		$sellmessage.= "$tradetick_lang[msg_7_1] $taxamount $c_displayname $tradetick_lang[msg_7_2] ($offer_sector) $tradetick_lang[msg_7_3].";
	}
	else
	{
		$sellmessage.= "$tradetick_lang[msg_8_1] $taxamount $c_displayname $tradetick_lang[msg_8_2] ($offer_sector) $tradetick_lang[msg_8_3].";
	}
	//Handelspunkte ermitteln
	$trade_points = 0;
	$factor = 0;
	$pfactor = 0;
	if ($request_buy_type == "m")
	{
		$factor = 1;
	}
	elseif ($request_buy_type == "d")
	{
		$factor = 2;
	}
	elseif ($request_buy_type == "i")
	{
		$factor = 3;
	}
	elseif ($request_buy_type == "e")
	{
		$factor = 4;
	}
	if ($request_currency == "m")
	{
		$pfactor = 1;
	}
	elseif ($request_currency == "d")
	{
		$pfactor = 2;
	}
	elseif ($request_currency == "i")
	{
		$pfactor = 3;
	}
	elseif ($request_currency == "e")
	{
		$pfactor = 4;
	}
	$win = $booked_amount * ($offer_price - $factor);
	if ($win > 0)
	{
		$trade_points = ($win * $pfactor) / 10;
		$sellmessage.= "<br>$tradetick_lang[msg_9_1] {$win} {$$request_currency}. $tradetick_lang[msg_9_2] $trade_points $tradetick_lang[msg_9_3].";
		$query = "UPDATE de_user_data SET tradescore=tradescore+$trade_points WHERE user_id=$offer_user_id";
		$result = mysql_query($query);
	}
	//Userdatensätze aktualisieren
	mysql_query($sellquery);
	mysql_query($buyquery);
	//Nachrichten speichern
	$time=strftime("%Y%m%d%H%M%S");
	mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$request_user_id', '10', '$time', '$buymessage', '0')");
	mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$offer_user_id', '11', '$time', '$sellmessage', '0')");
	//sektorsteuer gutschreiben
	mysql_query($sekquery);
	mysql_query($donationquery);
	$logstamp = date("Y-m-d H:i:s");
	$logquery = "INSERT INTO de_trade_log  (timestamp, seller_id, seller_race, seller_sector, buyer_id, sell_type, sell_amount, sell_currency, sell_price, buy_price, seller_got, buyer_paid, buyer_bookback, seller_sectax)  VALUES ('$logstamp', '$offer_user_id', '$offer_race', '$offer_sector', '$request_user_id', '$request_buy_type', '$booked_amount', '$request_currency', '$offer_price', '$request_price', '$buyer_to_pay', '$buyer_to_pay', '$buyer_payback', '$taxamount')";
	mysql_query($logquery);
}

function bookFleetTrade($request_user_id, $booked_amount, $request_buy_type, $buyer_payback, $request_currency, $offer_user_id, $buyer_to_pay, $offer_race, $offer_ssatz, $offer_sector, $offer_price, $request_price, $fromdepot)
{
	include "../inc/userartefact.inc.php";
	//userartefakte auslesen
	$db_daten=mysql_query("SELECT id, level FROM de_user_artefact WHERE id=1 AND user_id='$offer_user_id'");
	$artbonus=0;
	while($row = mysql_fetch_array($db_daten))
	{
	  $artbonus=$artbonus+$ua_werte[$row["id"]-1][$row["level"]-1][0];
	}
	 $intick = 1;
     global $tradetick_lang;
     global $sv_server_lang;
     include("trade.config.inc.php");
	//Flottenkonfiguration ermitteln
	$fleetconfig = ${"fleet_"."$offer_race"};
	//Schiffsdaten auslesen
	$ship_displayname = $fleetconfig["$request_buy_type"]["name"];
	$ship_value = $fleetconfig["$request_buy_type"]["value"];
	$ship_tradetime = $fleetconfig["$request_buy_type"]["trade_time"];

	/*print("Request-Userid: $request_user_id<br>
	Booked Amount: $booked_amount<br>
	Request-Buy-Type: $request_buy_type ($ship_displayname)<br>
	Buyer-Payback: $buyer_payback<br>
	Request-Currency: $request_currency<br>
	Offer-Userid: $offer_user_id<br>
	Buyer-To-Pay: $buyer_to_pay<br>
	Offer-Race: $offer_race<br>
	Offer-SSatz: $offer_ssatz<br>
	Offer-Sector: $offer_sector<br>
	Offer-Price: $offer_price<br>
	Request-Price: $request_price<br><hr>");*/
	$m = $tradetick_lang[multiplex];
	$d = $tradetick_lang[dyharra];
	$i = $tradetick_lang[iradium];
	$e = $tradetick_lang[eternium];
	$currency_displayname = $$request_currency;

	//Gewinn berechnen
	//Realer Schiffswert
	$ship_real_value = $booked_amount * $ship_value;
	//Verlangter Schiffswert
	$ship_sold_value = $booked_amount * $offer_price;
	//Erlös vor Steuerabzug (unbereinigter Gewinn)
	$trade_value = $ship_sold_value - $ship_real_value;
	//print("Echter Wert des Handels: $ship_real_value<br>Erzielter Wert des Handels: $ship_sold_value<br>Gewinn: $trade_value<br>");

	//Handelspunkte und Steuer berechnen
	$steuer = 0;
	$handelspunkte = 0;
	if ($trade_value > 0)
	{
		//Steuer
		$steuer = $trade_value / 100 * $offer_ssatz;
		//Handelspunkte
		$handelspunkte = $trade_value / 10;
	}
	//Verbleibende Gutschrift
	$trade_resultvalue = $ship_sold_value - $steuer;
	//Erzielter Gewinn nach Steuer
	$trade_result_profit = $trade_value - $steuer;
	//print("Ermittelte Steuer ($offer_ssatz %): $steuer<br>Erzielte Handelspunkte: $handelspunkte<br>Erreichte Gutschrift: $trade_resultvalue<br>Gewinn nach Steuer: $trade_result_profit<br>");

	//Ressourcen umrechnen
	if ($request_currency == "m")
	{
		$factor = 1;
		$resfieldname = "restyp01";
		$donationfieldname = "spend01";
	}
	elseif ($request_currency == "d")
	{
		$factor = 2;
		$resfieldname = "restyp02";
		$donationfieldname = "spend02";
	}
	elseif ($request_currency == "i")
	{
		$factor = 3;
		$resfieldname = "restyp03";
		$donationfieldname = "spend03";
	}
	elseif ($request_currency == "e")
	{
		$factor = 4;
		$resfieldname = "restyp04";
		$donationfieldname = "spend04";
	}

	//Echte Werte berechnen
	$book_ship_real_value = $ship_real_value / $factor;
	$book_ship_sold_value = $ship_sold_value / $factor;
	$book_trade_value = $trade_value / $factor;
	$book_trade_resultvalue = $trade_resultvalue / $factor;
	$book_trade_result_profit = $trade_result_profit / $factor;
	$book_tax = $steuer / $factor;
	$book_buyer_payback = $buyer_payback / $factor;
	$book_buyer_to_pay = $buyer_to_pay / $factor;
	
	if ($fromdepot != "1" && $artbonus > 0)
	{
		$art_minus = ($book_ship_real_value / 100) * $artbonus;
		$book_trade_resultvalue = $book_trade_resultvalue - $art_minus;
	}
	else 
	{
		$art_minus = 0;
	}

	//print("Echte Werte:<br>Echter Schiffswert: $book_ship_real_value $currency_displayname<br>Erzielter Schiffswert: $book_ship_sold_value $currency_displayname<br>Erzielter Erlös: $book_trade_value $currency_displayname<br>Erlös nach Steuer ($offer_ssatz %): $book_trade_resultvalue<br>Gewinn nach Steuer ($offer_ssatz %): $book_trade_result_profit<br>Bezahlte Steuer: $book_tax<br>Zu bezahlender Betrag: $book_buyer_to_pay $currency_displayname<br>Zu erstattender Betrag: $book_buyer_payback $currency_displayname<br><hr>");

	$buyer_message = "$tradetick_lang[msg_10_1] $booked_amount $ship_displayname $tradetick_lang[msg_10_2] $book_buyer_to_pay $currency_displayname. ";
	if ($book_buyer_payback > 0)
	{
		$buyer_message.="$tradetick_lang[msg_11_1] $book_buyer_payback $currency_displayname $tradetick_lang[msg_11_2]. ";
	}
	$buyer_message.="$tradetick_lang[msg_12_1] $ship_tradetime $tradetick_lang[msg_12_2].";

	$seller_message = "$tradetick_lang[msg_13_1] $booked_amount $ship_displayname $tradetick_lang[msg_13_2] $book_ship_sold_value $currency_displayname.
	$tradetick_lang[msg_13_3] $offer_ssatz % ($book_tax $currency_displayname) $book_trade_result_profit $currency_displayname. $tradetick_lang[msg_13_4] $book_trade_resultvalue $currency_displayname $tradetick_lang[msg_13_5] $handelspunkte Handelspunkte.";
	$seller_message .= "<br><br>".$tradetick_lang[msg_15]." $artbonus %. ";
	$seller_message .= $tradetick_lang[msg_16]." $art_minus $currency_displayname.<br><br>$tradetick_lang[msg_13_6].";
	//print("<hr>$buyer_message<hr>$seller_message<hr>");
	//Nachrichten eintragen
	mysql_query("UPDATE de_user_data SET newnews='1' WHERE user_id = '$request_user_id'");
	mysql_query("UPDATE de_user_data SET newnews='1' WHERE user_id = '$offer_user_id'");
	//Ressourcen beim Verkäufer aktualisieren
	mysql_query("UPDATE de_user_data SET `$resfieldname`=`$resfieldname`+$book_trade_resultvalue WHERE user_id = '$offer_user_id'");
	//Erstattung beim Käufer
	mysql_query("UPDATE de_user_data SET `$resfieldname`=`$resfieldname`+$book_buyer_payback WHERE user_id = '$request_user_id'");
	//Flotte zur Lieferung überstellen
	$timestamp = time();
	mysql_query("INSERT INTO $de_trade_fleettransit (timestamp, seller_id, target_id, amount, shiptype, remaining_ticks, race) VALUES ('$timestamp', '$offer_user_id', '$request_user_id', '$booked_amount', '$request_buy_type', '$ship_tradetime', '$offer_race')");
	//Handelspunkte gutschreiben
	mysql_query("UPDATE de_user_data SET tradescore=tradescore+$handelspunkte WHERE user_id=$offer_user_id");
	//Seksteuer gutschreiben
	mysql_query("UPDATE de_sector SET `$resfieldname`=`$resfieldname`+$book_tax WHERE sec_id='$offer_sector'");
	mysql_query("UPDATE de_user_data SET `$donationfieldname`=`$donationfieldname`+$book_tax WHERE user_id = '$offer_user_id'");
	//Sysnews speichern
	$time=strftime("%Y%m%d%H%M%S");
	mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$request_user_id', '10', '$time', '$buyer_message', '0')");
	mysql_query("INSERT INTO de_user_news (user_id, typ, time, text, seen) VALUES ('$offer_user_id', '11', '$time', '$seller_message', '0')");
	//tradelog schreiben
	$logstamp = date("Y-m-d H:i:s");
	//mysql_query("INSERT INTO de_trade_log  (timestamp, seller_id, seller_race, seller_sector, buyer_id, sell_type, sell_amount, sell_currency, sell_price, buy_price, seller_got, buyer_paid, buyer_bookback, seller_sectax)  VALUES ('$logstamp', '$offer_user_id', '$offer_race', '$offer_sector', '$request_user_id', '$request_buy_type', '$booked_amount', '$request_currency', '$offer_price', '$request_price', '$buyer_to_pay', '$buyer_to_pay', '$buyer_payback', '$taxamount')");

}
?>