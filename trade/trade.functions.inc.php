<?php
include 'inc/lang/'.$sv_server_lang.'_trade.functions.lang.php';
function sellFleet($userid, $shiptype, $tradeamount, $price, $currency, $tablename, $stornoticks, $ssatz, $sector, $race, $fleetconfig)
{
	global $tradefunctions_lang;
	$message="";
	$disp_name_ok = false;
	$tr_res_ok = false;
	$curr_res_ok = false;
	$uid_ok = false;
	$price = str_replace(",",".",$price);

	$f_query = "select $shiptype from de_user_fleet WHERE user_id='".$userid."-0'";

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

	if ($currency == "m" || $currency == "d" || $currency == "i" || $currency == "e")
	{
		$curr_res_ok = true;
	}
	if ($shiptype == "e81" || $shiptype == "e82" || $shiptype == "e83" || $shiptype == "e84" || $shiptype == "e85" || $shiptype == "e86" || $shiptype == "e87" || $shiptype == "e88")
	{
		$tr_type_ok = true;
	}
	if (isset($userid) && strlen($userid) > 0)
	{
		$uid_ok = true;
	}
	if (!$uid_ok || !$tr_type_ok || !$curr_res_ok)
	{
		$message = $tradefunctions_lang[msg_1];
		return($message);
	}
	if (!validDecimal($price))
	{
		$message = "$tradefunctions_lang[msg_2_1] (".number_format($price, 2, ",", ".").") $tradefunctions_lang[msg_2_2].";
		return($message);
	}
	$shipname = $fleetconfig[$shiptype]["name"];
	$shipvalue = $fleetconfig[$shiptype]["value"];
	$shipmaxvalue = ($shipvalue/100*20) + $shipvalue;

	if ($price < $shipvalue)
	{
		$message = "$tradefunctions_lang[msg_3_1] $shipname $tradefunctions_lang[msg_3_2] (".number_format($shipvalue, 2, ",",".").") $tradefunctions_lang[msg_3_3].";
		return($message);
	}
	if ($price > $shipmaxvalue)
	{
		$message = "$tradefunctions_lang[msg_4_1] $shipname $tradefunctions_lang[msg_4_2] (".number_format($shipmaxvalue, 2, ",",".").") $tradefunctions_lang[msg_4_3].";
		return($message);
	}
	if (!validDigit($tradeamount))
	{
		$message = "$tradefunctions_lang[msg_5_1] (".number_format($tradeamount, 0, ",",".").") $tradefunctions_lang[msg_5_2].";
		return($message);
	}
	if ($tradeamount > $depot_count)
	{
		$message = "$tradefunctions_lang[msg_6_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_6_2].";
		return ($message);
	}
	if ($tradeamount == "0" || !isset($tradeamount) || strlen($tradeamount) == 0)
	{
		$message = "$tradefunctions_lang[msg_7_1] 0 $shipname $tradefunctions_lang[msg_7_2].";
		return ($message);
	}
	if ($tradeamount < 4)
	{
		$message = "$tradefunctions_lang[msg_8_1] $tradeamount $shipname $tradefunctions_lang[msg_8_2].";
		return ($message);
	}

	$timestamp = time();
	$query = "INSERT INTO $tablename (user_id, sell_type, amount, price, currency, locked, race, timestamp, remaining_ticks, ssatz, sector) VALUES ('$userid', '$shiptype', '$tradeamount', '$price', '$currency', '0', '$race', '$timestamp', '2500', '$ssatz', '$sector')";
	$result = mysql_query($query);
	if ($result)
	{
		$trt_points = $fleetconfig[$shiptype]["points"];
		$points = $trt_points * $tradeamount;
		mysql_query("UPDATE de_user_data SET score=score-$points WHERE user_id='$userid'");
		$query = "UPDATE de_user_fleet SET $shiptype = $shiptype - $tradeamount WHERE user_id='".$userid."-0'";
		$result = mysql_query($query);

		$message = "$tradefunctions_lang[msg_9_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_9_2] ".number_format($price, 2, ",",".")." $tradefunctions_lang[msg_9_3] ($currency) $tradefunctions_lang[msg_9_4] $points $tradefunctions_lang[msg_9_5].";
	}
	else
	{
		$message = "$tradefunctions_lang[msg_10].";
	}
	return ($message);
}

function buyFleet($userid, $shiptype, $tradeamount, $price, $currency, $tablename, $stornoticks, $ssatz, $sector, $race, $fleetconfig)
{
         global $tradefunctions_lang;
         $message="";
	$disp_name_ok = false;
	$tr_res_ok = false;
	$curr_res_ok = false;
	$uid_ok = false;
	$price = str_replace(",",".",$price);
	$u_values	= mysql_query("SELECT restyp01, restyp02, restyp03, restyp04 FROM de_user_data WHERE user_id='$userid'");
	$u_row 		= mysql_fetch_array($u_values);
	$restyp01	= $u_row[0];
	$restyp02	= $u_row[1];
	$restyp03	= $u_row[2];
	$restyp04	= $u_row[3];
	if ($currency == "m")
	{
		$instock = $restyp01;
		$to_pay = $price * $tradeamount;
	}
	elseif ($currency == "d")
	{
		$instock = $restyp02;
		$to_pay = ($price * $tradeamount)/2;
	}
	elseif ($currency == "i")
	{
		$instock = $restyp03;
		$to_pay = ($price * $tradeamount)/3;
	}
	elseif ($currency == "e")
	{
		$instock = $restyp04;
		$to_pay = ($price * $tradeamount)/4;
	}

	if ($shiptype == "e81" || $shiptype == "e82" || $shiptype == "e83" || $shiptype == "e84" || $shiptype == "e85" || $shiptype == "e86" || $shiptype == "e87" || $shiptype == "e88")
	{
		$tr_type_ok = true;
	}
	if ($currency == "m" || $currency == "d" || $currency == "i" || $currency == "e")
	{
		$curr_res_ok = true;
	}
	if (isset($userid) && strlen($userid) > 0)
	{
		$uid_ok = true;
	}
	if (!$uid_ok || !$tr_type_ok || !$curr_res_ok)
	{
		$message = $tradefunctions_lang[msg_1];
		return($message);
	}
	if (!validDecimal($price))
	{
		$message = "$tradefunctions_lang[msg_2_1] (".number_format($price, 2, ",", ".").") $tradefunctions_lang[msg_2_2].";
		return($message);
	}
	$shipname = $fleetconfig[$shiptype]["name"];
	$shipvalue = $fleetconfig[$shiptype]["value"];
	$shipmaxvalue = ($shipvalue/100*20) + $shipvalue;

	if ($price < $shipvalue)
	{
		$message = "$tradefunctions_lang[msg_11_1] $shipname $tradefunctions_lang[msg_11_2] (".number_format($shipvalue, 2, ",",".").") $tradefunctions_lang[msg_11_3].";
		return($message);
	}
	if ($price > $shipmaxvalue)
	{
		$message = "$tradefunctions_lang[msg_12_1] $shipname $tradefunctions_lang[msg_12_2] (".number_format($shipmaxvalue, 2, ",",".").") $tradefunctions_lang[msg_12_3].";
		return($message);
	}
	if (!validDigit($tradeamount))
	{
		$message = "$tradefunctions_lang[msg_5_1] ($tradeamount) $tradefunctions_lang[msg_5_2].";
		return($message);
	}
	if ($to_pay > $instock)
	{
		$message = "$tradefunctions_lang[msg_13_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_13_2] $currency ($to_pay $tradefunctions_lang[msg_13_3]), $tradefunctions_lang[msg_13_4].";
		return ($message);
	}
	if ($tradeamount == "0" || !isset($tradeamount) || strlen($tradeamount) == 0)
	{
		$message = "$tradefunctions_lang[msg_14_1] 0 $shipname $tradefunctions_lang[msg_14_2].";
		return ($message);
	}
	$timestamp = time();
	$query = "INSERT INTO $tablename (user_id, buy_type, amount, price, currency, locked, race, timestamp, ssatz, sector) VALUES ('$userid', '$shiptype', '$tradeamount', '$price', '$currency', '0', '$race', '$timestamp', '$ssatz', '$sector')";
	$result = mysql_query($query);
	if ($result)
	{
		if ($currency == "m")
		{
			$query = "UPDATE de_user_data SET restyp01=restyp01-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "d")
		{
			$query = "UPDATE de_user_data SET restyp02=restyp02-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "i")
		{
			$query = "UPDATE de_user_data SET restyp03=restyp03-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "e")
		{
			$query = "UPDATE de_user_data SET restyp04=restyp04-$to_pay WHERE user_id='$userid'";
		}
		$result = mysql_query($query);
		$message = "$tradefunctions_lang[msg_15_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_15_2] ".number_format($price, 2, ",",".")." $tradefunctions_lang[msg_15_3] ($currency) $tradefunctions_lang[msg_15_4] ".number_format($to_pay, 2, ",",".")." $currency $tradefunctions_lang[msg_15_5].";
	}
	else
	{
		$message = "$tradefunctions_lang[msg_16].";
	}
	return ($message);
}

function stornoFleetoffer($userid, $tradeid, $tablename, $fleetconfig, $offer_stornotax, $offer_traderstornotax, $user_race)
{
	$message = "";
         global $tradefunctions_lang;
	if (isset($tradeid) && strlen($tradeid) > 0)
	{
		$result	= mysql_query("SELECT * FROM $tablename WHERE id='$tradeid'");
		if ($result)
		{
			$numrows	= mysql_num_rows($result);
			if ($numrows == 1)
			{
				$values 	= mysql_fetch_array($result);
				$id = $values["id"];
				$user_id = $values["user_id"];
				$sell_type = $values["sell_type"];
				$amount = $values["amount"];
				$race = $values["race"];
				$fromdepot = $values["fromdepot"];
				$stornotax = 0;
				if ($amount > 3)
				{
					if ($user_race == 2)
					{
						$stornotax = round(($amount/100*$offer_traderstornotax),0);
					}
					else
					{
						$stornotax = round(($amount/100*$offer_stornotax),0);
					}
				}
				if ($fromdepot == "1")
				{
					$stornotax = 0;
				}
				$amount = $amount - $stornotax;
				if ($userid == $user_id)
				{
					if ($fromdepot == "1")
					{
						$trt_points = $fleetconfig[$sell_type]["value"] / 250;
						$points = round($trt_points * $amount, 0);
						$query = "UPDATE de_trade_depot SET $sell_type=$sell_type+$amount WHERE user_id='$userid' AND race=$race";
					}
					else
					{
						$trt_points = $fleetconfig[$sell_type]["points"];
						$points = round($trt_points * $amount, 0);
						$query = "UPDATE de_user_fleet SET $sell_type=$sell_type+$amount WHERE user_id='".$userid."-0'";
					}
					$result = mysql_query($query);

					mysql_query("UPDATE de_user_data SET score=score+$points WHERE user_id='$userid'");

					if ($result)
					{
						$result = mysql_query("DELETE FROM $tablename WHERE id='$tradeid'");
						$message = "$tradefunctions_lang[msg_17_1] ".number_format($amount,0,",",".")." ".$fleetconfig[$sell_type]["name"]." $tradefunctions_lang[msg_17_2] ".number_format($stornotax,0,",",".")." ".$fleetconfig[$sell_type]["name"]." $tradefunctions_lang[msg_17_3] $points $tradefunctions_lang[msg_17_4].";
					}
					else
					{
						$message = "$tradefunctions_lang[msg_18].";
					}
				}
				else
				{
					$message = "$tradefunctions_lang[msg_19]!";
					lockTrade($userid);
				}

			}
			else
			{
				$message = "$tradefunctions_lang[msg_20].";
			}
		}
		else
		{
			$message = "$tradefunctions_lang[msg_21].";
		}
	}
	else
	{
		$message = "$tradefunctions_lang[msg_22].";
	}
	return $message;
}

function stornoFleetrequest($userid, $tradeid, $tablename, $stornotax, $traderstornotax)
{
         global $tradefunctions_lang;
         $message = "";
	if (isset($tradeid) && strlen($tradeid) > 0)
	{
		$result	= mysql_query("SELECT * FROM $tablename WHERE id='$tradeid'");
		if ($result)
		{
			$numrows	= mysql_num_rows($result);
			if ($numrows == 1)
			{
				$values 	= mysql_fetch_array($result);
				$id = $values["id"];
				$user_id = $values["user_id"];
				$buy_type = $values["buy_type"];
				$amount = $values["amount"];
				$currency = $values["currency"];
				$price = $values["price"];
				$race = $values["race"];
				if ($userid == $user_id)
				{
					if ($currency == "m")
					{
						$pay_back = $price * $amount;
					}
					elseif ($currency == "d")
					{
						$pay_back = ($price * $amount) / 2;
					}
					elseif ($currency == "i")
					{
						$pay_back = ($price * $amount) / 3;
					}
					elseif ($currency == "e")
					{
						$pay_back = ($price * $amount) / 4;
					}

					$stornoamount = $pay_back;

					if ($currency == "m")
					{
						$query = "UPDATE de_user_data SET restyp01=restyp01+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "d")
					{
						$query = "UPDATE de_user_data SET restyp02=restyp02+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "i")
					{
						$query = "UPDATE de_user_data SET restyp03=restyp03+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "e")
					{
						$query = "UPDATE de_user_data SET restyp04=restyp04+$stornoamount WHERE user_id='$userid'";
					}
					$result = mysql_query($query);
					$result = mysql_query("DELETE FROM $tablename WHERE id='$tradeid'");
					$message = "$tradefunctions_lang[msg_23_1] ".number_format($stornoamount,2,",",".")." $currency $tradefunctions_lang[msg_23_2].";
				}
				else
				{
					$message = "$tradefunctions_lang[msg_19]!";
					lockTrade($userid);
				}

			}
			else
			{
				$message = "$tradefunctions_lang[msg_20].";
			}
		}
		else
		{
			$message = "$tradefunctions_lang[msg_21].";
		}
	}
	else
	{
		$message = "$tradefunctions_lang[msg_22].";
	}
	return $message;
}

function sellRes($userid, $displayname, $traderes, $tradeamount, $price, $currency, $tablename, $stornoticks, $ssatz, $race)
{
         global $tradefunctions_lang;
         $message="";
	$disp_name_ok = false;
	$tr_res_ok = false;
	$curr_res_ok = false;
	$uid_ok = false;
	$price = str_replace(",",".",$price);
	$u_values	= mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, sector FROM de_user_data WHERE user_id='$userid'");
	$u_row 		= mysql_fetch_array($u_values);
	$restyp01	= $u_row[0];
	$restyp02	= $u_row[1];
	$restyp03	= $u_row[2];
	$restyp04	= $u_row[3];
	$sector		= $u_row[4];

	if ($traderes == "m")
	{
		$instock = $restyp01;
	}
	elseif ($traderes == "d")
	{
		$instock = $restyp02;
	}
	elseif ($traderes == "i")
	{
		$instock = $restyp03;
	}
	elseif ($traderes == "e")
	{
		$instock = $restyp04;
	}
	if ($displayname=="Multiplex" || $displayname=="Dyharra" || $displayname=="Iradium" || $displayname=="Eternium")
	{
		$disp_name_ok = true;
	}
	if ($traderes == "m" || $traderes == "d" || $traderes == "i" || $traderes == "e")
	{
		$tr_res_ok = true;
	}
	if ($currency == "m" || $currency == "d" || $currency == "i" || $currency == "e")
	{
		$curr_res_ok = true;
	}
	if (isset($userid) && strlen($userid) > 0)
	{
		$uid_ok = true;
	}
	if (!$uid_ok || !$disp_name_ok || !$tr_res_ok || !$curr_res_ok)
	{
		$message = "$tradefunctions_lang[msg_1].";
		return($message);
	}
	if (!validDecimal($price))
	{
		$message = "$tradefunctions_lang[msg_2_1] ($price) $tradefunctions_lang[msg_2_2].";
		return($message);
	}
	if ((($traderes == "m") && ($price < 1)) || (($traderes == "d") && ($price < 2)) || (($traderes == "i") && ($price < 3)) || (($traderes == "e") && ($price < 4)))
	{
		$message = "$tradefunctions_lang[msg_24_1] $displayname $tradefunctions_lang[msg_24_2].";
		return($message);
	}
	if ((($traderes == "m") && ($price > 1.25)) || (($traderes == "d") && ($price > 2.5)) || (($traderes == "i") && ($price > 3.75)) || (($traderes == "e") && ($price > 5)))
	{
		$message = "$tradefunctions_lang[msg_25_1] $displayname $tradefunctions_lang[msg_25_2].";
		return($message);
	}
	if (!validDigit($tradeamount))
	{
		$message = "$tradefunctions_lang[msg_5_1] ($tradeamount) $tradefunctions_lang[msg_5_2].";
		return($message);
	}
	if ($tradeamount > $instock)
	{
		$message = "$tradefunctions_lang[msg_26_1] $tradeamount $displayname $tradefunctions_lang[msg_26_2] $displayname, $tradefunctions_lang[msg_26_3].";
		return ($message);
	}
	if ($tradeamount == "0" || !isset($tradeamount) || strlen($tradeamount) == 0)
	{
		$message = "$tradefunctions_lang[msg_27_1] 0 $displayname $tradefunctions_lang[msg_27_2].";
		return ($message);
	}
	$timestamp = time();
	$query = "INSERT INTO $tablename (user_id, sell_type, amount, price, currency, locked, race, timestamp, remaining_ticks, ssatz, sector) VALUES ('$userid', '$traderes', '$tradeamount', '$price', '$currency', '0', '$race', '$timestamp', '$stornoticks', '$ssatz', '$sector')";
	$result = mysql_query($query);
	if ($result)
	{
		if ($traderes == "m")
		{
			$query = "UPDATE de_user_data SET restyp01=restyp01-$tradeamount WHERE user_id='$userid'";
		}
		elseif ($traderes == "d")
		{
			$query = "UPDATE de_user_data SET restyp02=restyp02-$tradeamount WHERE user_id='$userid'";
		}
		elseif ($traderes == "i")
		{
			$query = "UPDATE de_user_data SET restyp03=restyp03-$tradeamount WHERE user_id='$userid'";
		}
		elseif ($traderes == "e")
		{
			$query = "UPDATE de_user_data SET restyp04=restyp04-$tradeamount WHERE user_id='$userid'";
		}
		$result = mysql_query($query);
		$message = "$tradefunctions_lang[msg_28_1] ($tradeamount $displayname $tradefunctions_lang[msg_28_2] $price, $tradefunctions_lang[msg_28_3] $currency) $tradefunctions_lang[msg_28_4].";
	}
	else
	{
		$message = "$tradefunctions_lang[msg_29].";
	}
	return ($message);
}

function buyRes($userid, $displayname, $traderes, $tradeamount, $price, $currency, $tablename, $stornoticks, $ssatz, $race)
{
	$message="";
         global $tradefunctions_lang;
	$disp_name_ok = false;
	$tr_res_ok = false;
	$curr_res_ok = false;
	$uid_ok = false;
	$price = str_replace(",",".",$price);
	$u_values	= mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, sector FROM de_user_data WHERE user_id='$userid'");
	$u_row 		= mysql_fetch_array($u_values);
	$restyp01	= $u_row[0];
	$restyp02	= $u_row[1];
	$restyp03	= $u_row[2];
	$restyp04	= $u_row[3];
	$sector		= $u_row[4];
	if ($currency == "m")
	{
		$instock = $restyp01;
		$to_pay = $price * $tradeamount;
	}
	elseif ($currency == "d")
	{
		$instock = $restyp02;
		$to_pay = ($price * $tradeamount)/2;
	}
	elseif ($currency == "i")
	{
		$instock = $restyp03;
		$to_pay = ($price * $tradeamount)/3;
	}
	elseif ($currency == "e")
	{
		$instock = $restyp04;
		$to_pay = ($price * $tradeamount)/4;
	}
	if ($displayname=="Multiplex" || $displayname=="Dyharra" || $displayname=="Iradium" || $displayname=="Eternium")
	{
		$disp_name_ok = true;
	}
	if ($traderes == "m" || $traderes == "d" || $traderes == "i" || $traderes == "e")
	{
		$tr_res_ok = true;
	}
	if ($currency == "m" || $currency == "d" || $currency == "i" || $currency == "e")
	{
		$curr_res_ok = true;
	}
	if (isset($userid) && strlen($userid) > 0)
	{
		$uid_ok = true;
	}
	if (!$uid_ok || !$disp_name_ok || !$tr_res_ok || !$curr_res_ok)
	{
		$message = $tradefunctions_lang[msg_1];
		return($message);
	}
	if (!validDecimal($price))
	{
		$message = "$tradefunctions_lang[msg_2_1] ($price) $tradefunctions_lang[msg_2_2].";
		return($message);
	}
	if ((($traderes == "m") && ($price < 1)) || (($traderes == "d") && ($price < 2)) || (($traderes == "i") && ($price < 3)) || (($traderes == "e") && ($price < 4)))
	{
		$message = "$tradefunctions_lang[msg_30_1] $displayname $tradefunctions_lang[msg_30_2].";
		return($message);
	}
	if ((($traderes == "m") && ($price > 1.25)) || (($traderes == "d") && ($price > 2.5)) || (($traderes == "i") && ($price > 3.75)) || (($traderes == "e") && ($price > 5)))
	{
		$message = "$tradefunctions_lang[msg_25_1] $displayname $tradefunctions_lang[msg_25_2].";
		return($message);
	}
	if (!validDigit($tradeamount))
	{
		$message = "$tradefunctions_lang[msg_31_1] ($tradeamount) $tradefunctions_lang[msg_31_2].";
		return($message);
	}
	if ($to_pay > $instock)
	{
		$message = "$tradefunctions_lang[msg_32_1] $tradeamount $displayname $tradefunctions_lang[msg_32_2] $currency ($to_pay $tradefunctions_lang[msg_32_3]), $tradefunctions_lang[msg_32_4].";
		return ($message);
	}
	if ($tradeamount == "0" || !isset($tradeamount) || strlen($tradeamount) == 0)
	{
		$message = "$tradefunctions_lang[msg_33_1] 0 $displayname $tradefunctions_lang[msg_33_2].";
		return ($message);
	}
	$timestamp = time();
	$query = "INSERT INTO $tablename (user_id, buy_type, amount, price, currency, locked, race, timestamp, remaining_ticks, ssatz, sector) VALUES ('$userid', '$traderes', '$tradeamount', '$price', '$currency', '0', '$race', '$timestamp', '$stornoticks', '$ssatz', '$sector')";
	$result = mysql_query($query);
	if ($result)
	{
		if ($currency == "m")
		{
			$query = "UPDATE de_user_data SET restyp01=restyp01-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "d")
		{
			$query = "UPDATE de_user_data SET restyp02=restyp02-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "i")
		{
			$query = "UPDATE de_user_data SET restyp03=restyp03-$to_pay WHERE user_id='$userid'";
		}
		elseif ($currency == "e")
		{
			$query = "UPDATE de_user_data SET restyp04=restyp04-$to_pay WHERE user_id='$userid'";
		}
		$result = mysql_query($query);
		$message = "$tradefunctions_lang[msg_34_1] ($tradeamount $displayname $tradefunctions_lang[msg_34_2] $price, $tradefunctions_lang[msg_34_3] $currency) $tradefunctions_lang[msg_34_4] $to_pay $currency $tradefunctions_lang[msg_34_5].";
	}
	else
	{
		$message = $tradefunctions_lang[msg_29];
	}
	return ($message);
}

function stornoResoffer($userid, $tradeid, $tablename, $stornotax, $traderstornotax)
{
	$message = "";
         global $tradefunctions_lang;
	if (isset($tradeid) && strlen($tradeid) > 0)
	{
		$result	= mysql_query("SELECT * FROM $tablename WHERE id='$tradeid'");
		if ($result)
		{
			$numrows	= mysql_num_rows($result);
			if ($numrows == 1)
			{
				$values 	= mysql_fetch_array($result);
				$id = $values["id"];
				$user_id = $values["user_id"];
				$sell_type = $values["sell_type"];
				$amount = $values["amount"];
				$race = $values["race"];
				if ($userid == $user_id)
				{
					$tax = $stornotax;
					if ($race == "2")
					{
						$tax = $traderstornotax;
					}
					$taxamount = (($amount / 100)*$tax);
					$stornoamount = $amount - $taxamount;
					if ($sell_type == "m")
					{
						$query = "UPDATE de_user_data SET restyp01=restyp01+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($sell_type == "d")
					{
						$query = "UPDATE de_user_data SET restyp02=restyp02+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($sell_type == "i")
					{
						$query = "UPDATE de_user_data SET restyp03=restyp03+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($sell_type == "e")
					{
						$query = "UPDATE de_user_data SET restyp04=restyp04+$stornoamount WHERE user_id='$userid'";
					}
					$result = mysql_query($query);
					$result = mysql_query("DELETE FROM $tablename WHERE id='$tradeid'");
					$message = "$tradefunctions_lang[msg_35_1] $amount $sell_type $tradefunctions_lang[msg_35_2] $tax% $tradefunctions_lang[msg_35_3] ($taxamount $sell_type).<br>$tradefunctions_lang[msg_35_4] $stornoamount $sell_type.";
				}
				else
				{
					$message = $tradefunctions_lang[msg_19];
					lockTrade($userid);
				}

			}
			else
			{
				$message = $tradefunctions_lang[msg_20];
			}
		}
		else
		{
			$message = $tradefunctions_lang[msg_21];
		}
	}
	else
	{
		$message = $tradefunctions_lang[msg_22];
	}
	return $message;
}

function stornoResrequest($userid, $tradeid, $tablename, $stornotax, $traderstornotax)
{
         global $tradefunctions_lang;
         $message = "";
	if (isset($tradeid) && strlen($tradeid) > 0)
	{
		$result	= mysql_query("SELECT * FROM $tablename WHERE id='$tradeid'");
		if ($result)
		{
			$numrows	= mysql_num_rows($result);
			if ($numrows == 1)
			{
				$values 	= mysql_fetch_array($result);
				$id = $values["id"];
				$user_id = $values["user_id"];
				$buy_type = $values["buy_type"];
				$amount = $values["amount"];
				$currency = $values["currency"];
				$price = $values["price"];
				$race = $values["race"];
				if ($userid == $user_id)
				{
					$tax = $stornotax;
					if ($race == "2")
					{
						$tax = $traderstornotax;
					}
					if ($currency == "m")
					{
						$pay_back = $price * $amount;
					}
					elseif ($currency == "d")
					{
						$pay_back = ($price * $amount) / 2;
					}
					elseif ($currency == "i")
					{
						$pay_back = ($price * $amount) / 3;
					}
					elseif ($currency == "e")
					{
						$pay_back = ($price * $amount) / 4;
					}
					$taxamount = (($pay_back / 100)*$tax);
					$stornoamount = $pay_back - $taxamount;
					if ($currency == "m")
					{
						$query = "UPDATE de_user_data SET restyp01=restyp01+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "d")
					{
						$query = "UPDATE de_user_data SET restyp02=restyp02+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "i")
					{
						$query = "UPDATE de_user_data SET restyp03=restyp03+$stornoamount WHERE user_id='$userid'";
					}
					elseif ($currency == "e")
					{
						$query = "UPDATE de_user_data SET restyp04=restyp04+$stornoamount WHERE user_id='$userid'";
					}
					$result = mysql_query($query);
					$result = mysql_query("DELETE FROM $tablename WHERE id='$tradeid'");
					$message = "$tradefunctions_lang[msg_36_1] $amount $buy_type $tradefunctions_lang[msg_36_2] $tax% $tradefunctions_lang[msg_36_3] ($taxamount $currency).<br>$tradefunctions_lang[msg_36_4] $stornoamount $currency.";
				}
				else
				{
					$message = $tradefunctions_lang[msg_19];
					lockTrade($userid);
				}

			}
			else
			{
				$message = $tradefunctions_lang[msg_20];
			}
		}
		else
		{
			$message = $tradefunctions_lang[msg_21];
		}
	}
	else
	{
		$message = $tradefunctions_lang[msg_22];
	}
	return $message;
}

function stornoTronic($user_id, $id)
{
	$message = "";
         global $tradefunctions_lang;
	$result = @mysql_query("SELECT * FROM de_tauction WHERE id = '$id'");
	if ($result)
	{
		$numrows = mysql_num_rows($result);
		if ($numrows == 1)
		{
			$values = mysql_fetch_array($result);
			$uid 		= $values["seller"];
			$amount		= $values["amount"];
			$countbids	= $values["bids"];
			if ($uid == $user_id)
			{
				if ($countbids == 0)
				{
					mysql_query("UPDATE de_user_data SET restyp05=restyp05 + $amount WHERE user_id = '$user_id'");
					mysql_query("DELETE FROM de_tauction WHERE id = '$id'");
					$message = "$tradefunctions_lang[msg_37_1] $amount $tradefunctions_lang[msg_37_2].";
				}
				else
				{
					$message = "$tradefunctions_lang[msg_38].";
				}
			}
			else
			{
				$message = "$tradefunctions_lang[msg_39].";
			}
		}
		else
		{
			$message = "$tradefunctions_lang[msg_40].";
		}
	}
	else
	{
		$message = "$tradefunctions_lang[msg_41]!";
	}
	return $message;
}

function sellTronic($user_id, $tradeamount)
{
	$message = "";
    global $tradefunctions_lang;
	$u_values	= mysql_query("SELECT restyp05 FROM de_user_data WHERE user_id='$user_id'");
	$u_row 		= mysql_fetch_array($u_values);
	$instock	= $u_row[0];
	$tradeamount = round($tradeamount);
	if ($tradeamount == "0" || $tradeamount == "")
	{
		$message = "$tradefunctions_lang[msg_42].";
	}
	elseif ($tradeamount < "0")
	{
		$message = "$tradefunctions_lang[msg_43].";
		//lockTrade($user_id, "$tradefunctions_lang[msg_44].");
	}
	else
	{
		if ($instock < $tradeamount)
		{
			$message = "$tradefunctions_lang[msg_45_1] $tradeamount $tradefunctions_lang[msg_45_2] $instock $tradefunctions_lang[msg_45_3].";
		}
		else
		{
			$bookamount = $tradeamount * 500;
			mysql_query("UPDATE de_user_data SET restyp04 = restyp04 + $bookamount, restyp05 = restyp05-$tradeamount WHERE user_id = '$user_id'");
			$message = "$tradefunctions_lang[msg_46_1] $tradeamount $tradefunctions_lang[msg_46_2] $bookamount $tradefunctions_lang[msg_46_3].";
		}
	}
	return $message;
}

function depotbuy($user_id, $shiptype, $tradeamount, $currency, $user_race, $race, $restyp01, $restyp02, $restyp03, $restyp04, $shipconfig)
{
	$message = "";
         global $tradefunctions_lang;
	$shipvalue = $shipconfig["$shiptype"]["value"];
	$value = "";
	$max_amount = 0;
	if (!validDigit($tradeamount) || $tradeamount == "0" || strlen($tradeamount)==0)
	{
		$message = "$tradefunctions_lang[msg_47_1] ($tradeamount) $tradefunctions_lang[msg_47_2].";
		return $message;
	}

	if ($currency == "m")
	{
		$resupdatefield = "restyp01";
		$value = $shipvalue;
		$maxamount = floor($restyp01 / $value);
	}
	elseif ($currency == "d")
	{
		$resupdatefield = "restyp02";
		$value = $shipvalue / 2;
		$maxamount = floor($restyp02 / $value);
	}
	elseif ($currency == "i")
	{
		$resupdatefield = "restyp03";
		$value = $shipvalue / 3;
		$maxamount = floor($restyp03 / $value);
	}
	elseif ($currency == "e")
	{
		$resupdatefield = "restyp04";
		$value = $shipvalue / 4;
		$maxamount = floor($restyp04 / $value);
	}
	else
	{
		$message = "$tradefunctions_lang[msg_48_1] ($currency) $tradefunctions_lang[msg_48_2].";
	}
	if ($tradeamount > $maxamount)
	{
		$tradeamount = $maxamount;
		$message .= "$tradefunctions_lang[msg_49_1] $tradeamount $tradefunctions_lang[msg_49_2].<br>";
	}
	$to_pay = round($tradeamount * $value, 2);
	$query1 = "UPDATE de_user_data SET $resupdatefield = $resupdatefield - $to_pay WHERE user_id = '$user_id'";
	$query2 = "UPDATE de_trade_depot SET $shiptype = $shiptype + $tradeamount WHERE user_id = '$user_id' AND race = '$race'";
	mysql_query($query1);
	mysql_query($query2);
	$shipname = $shipconfig["$shiptype"]["name"];
	$race_name = $shipconfig["race_name"];
	$message .= "$tradefunctions_lang[msg_50_1] $tradeamount $tradefunctions_lang[msg_50_2] $shipname. $tradefunctions_lang[msg_50_3]: $race_name.<br>$tradefunctions_lang[msg_50_4].<br>";
	$restext = getRestext($currency);
	$message .= "$tradefunctions_lang[msg_51_1] $to_pay $restext $tradefunctions_lang[msg_51_2].";
	$trt_points = $shipconfig[$shiptype]["value"] / 250;
	$points = round($trt_points * $tradeamount, 0);
	mysql_query("UPDATE de_user_data SET score=score+$points WHERE user_id='$user_id'");
	$message.="<br>$tradefunctions_lang[msg_52_1] $points $tradefunctions_lang[msg_52_2].";
	return $message;
}

function depotsell($userid, $shiptype, $tradeamount, $price, $currency, $tablename, $stornoticks, $ssatz, $sector, $race, $fleetconfig)
{
	$restext = getResText($currency);
         global $tradefunctions_lang;
	$message="";
	$disp_name_ok = false;
	$tr_res_ok = false;
	$curr_res_ok = false;
	$uid_ok = false;
	$price = str_replace(",",".",$price);

	$f_query = "select $shiptype from de_trade_depot WHERE user_id='$userid' AND race='$race'";

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

	if ($currency == "m" || $currency == "d" || $currency == "i" || $currency == "e")
	{
		$curr_res_ok = true;
	}
	if ($shiptype == "e81" || $shiptype == "e82" || $shiptype == "e83" || $shiptype == "e84" || $shiptype == "e85" || $shiptype == "e86" || $shiptype == "e87" || $shiptype == "e88")
	{
		$tr_type_ok = true;
	}
	if (isset($userid) && strlen($userid) > 0)
	{
		$uid_ok = true;
	}
	if (!$uid_ok || !$tr_type_ok || !$curr_res_ok)
	{
		$message = "$tradefunctions_lang[msg_53].";
		return($message);
	}
	if (!validDecimal($price))
	{
		$message = "$tradefunctions_lang[msg_54_1] (".number_format($price, 2, ",", ".").") $tradefunctions_lang[msg_54_2].";
		return($message);
	}
	$shipname = $fleetconfig[$shiptype]["name"];
	$shipvalue = $fleetconfig[$shiptype]["value"];
	$shipmaxvalue = ($shipvalue/100*20) + $shipvalue;

	if ($price < $shipvalue)
	{
		$message = "$tradefunctions_lang[msg_55_1] $shipname $tradefunctions_lang[msg_55_2] (".number_format($shipvalue, 2, ",",".").") $tradefunctions_lang[msg_55_3].";
		return($message);
	}
	if ($price > $shipmaxvalue)
	{
		$message = "$tradefunctions_lang[msg_56_1] $shipname $tradefunctions_lang[msg_56_2] (".number_format($shipmaxvalue, 2, ",",".").") $tradefunctions_lang[msg_56_3].";
		return($message);
	}
	if (!validDigit($tradeamount))
	{
		$message = "$tradefunctions_lang[msg_57_1] (".number_format($tradeamount, 0, ",",".").") $tradefunctions_lang[msg_57_2].";
		return($message);
	}
	if ($tradeamount > $depot_count)
	{
		$message = "$tradefunctions_lang[msg_58_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_58_2].";
		return ($message);
	}
	if ($tradeamount == "0" || !isset($tradeamount) || strlen($tradeamount) == 0)
	{
		$message = "$tradefunctions_lang[msg_59_1] 0 $shipname $tradefunctions_lang[msg_59_2].";
		return ($message);
	}
	if ($tradeamount < 4)
	{
		$message = "$tradefunctions_lang[msg_60_1] $tradeamount $shipname $tradefunctions_lang[msg_60_2].";
		return ($message);
	}

	$timestamp = time();
	$query = "INSERT INTO $tablename (user_id, sell_type, amount, price, currency, locked, race, timestamp, remaining_ticks, ssatz, sector, fromdepot) VALUES ('$userid', '$shiptype', '$tradeamount', '$price', '$currency', '0', '$race', '$timestamp', '2500', '$ssatz', '$sector', '1')";
	$result = mysql_query($query);
	if ($result)
	{
		$trt_points = $fleetconfig[$shiptype]["value"] / 250;
		$points = round($trt_points * $tradeamount, 0);
		mysql_query("UPDATE de_user_data SET score=score-$points WHERE user_id='$userid'");
		$query = "UPDATE de_trade_depot SET $shiptype = $shiptype - $tradeamount WHERE user_id='$userid' AND race='$race'";
		$result = mysql_query($query);

		$message = "$tradefunctions_lang[msg_61_1] ".number_format($tradeamount, 0, ",",".")." $shipname $tradefunctions_lang[msg_61_2] ".number_format($price, 2, ",",".")." $tradefunctions_lang[msg_61_3] ($restext) $tradefunctions_lang[msg_61_4] $points $tradefunctions_lang[msg_61_5].";
	}
	else
	{
		$message = "$tradefunctions_lang[msg_29].";
	}
	return ($message);
}

function getRestext($res)
{
	$restext = "";
         global $tradefunctions_lang;
	if ($res == "m")
	{
		$restext = "$tradefunctions_lang[msg_62]";
	}
	elseif ($res == "d")
	{
		$restext = "$tradefunctions_lang[msg_63]";
	}
	elseif ($res == "i")
	{
		$restext = "$tradefunctions_lang[msg_64]";
	}
	elseif ($res == "e")
	{
		$restext = "$tradefunctions_lang[msg_65]";
	}
	return $restext;
}

function lockTrade($userid, $note)
{
	@mysql_query("UPDATE de_user_data SET trade_forbidden = '1' WHERE user_id = '$userid'");
	$result = @mysql_query("SELECT kommentar FROM de_user_info WHERE user_id = '$userid'");
	$values = @mysql_fetch_array($result);
	$comment = $values["kommentar"];
	$time=strftime("%d%.%m.%Y, H:%M:%S");
	$comment = $comment."\n$time - ".$note;
	@mysql_query("UPDATE de_user_info SET kommentar = '$comment' WHERE user_id = '$userid'");
}

function validDecimal($digit)
{
	$isavalid = true;
    for ($i=0; $i<strlen($digit); $i++)
  	{
    	if (!ereg("[0-9]",$digit[$i]) && !ereg("[.]",$digit[$i]))
        {
        	$isavalid = false;
            break;
        }
     }
     return($isavalid);
}

function getTradeStatus()
 {
 	global $sv_deactivate_trade;
 	
 	if($sv_deactivate_trade==1) return(0);
 	else return(1);
 	
 	/*
 	$tradestat = 1;
 	$tradestat_result=mysql_query("SELECT trade_active FROM de_system LIMIT 1");
 	if(mysql_num_rows($tradestat_result) > 0);
 	{
 		$tradestat_array = mysql_fetch_array($tradestat_result);
 		$tradestat=$tradestat_array[trade_active];
 	}
	
 	return $tradestat;
 	*/
 }

function updateTradeScore($userid, $addscore)
{
	$query = "UPDATE de_user_data SET tradescore=tradescore+$addscore WHERE user_id=$userid";
	$result = mysql_query($query);
}
 /*
function getStartTime()
	{
		return getMicroTime();
	}

	function getExecutionTime($starttime)
	{
		$endtime = getmicrotime();
		$time = $endtime - $starttime;
		$exec_time = round($time, 3);
		return $exec_time;
	}

	function getMicroTime()
	{
    	list($usec, $sec) = explode(" ",microtime());
    	return ((float)$usec + (float)$sec);
    } */
?>