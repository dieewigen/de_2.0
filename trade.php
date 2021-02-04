<?php
die();
include("lib/transaction.lib.php");
include_once('functions.php');
	$starttime = getStartTime();
    include "inc/header.inc.php";
    include 'inc/lang/'.$sv_server_lang.'_trade.trade.lang.php';
	//21.01.2004, für den schwarzmarkt müssen die credits und der pa-laufzeit ausgelesen werden, Isso
    $db_daten  	  = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, tick, techs, sector, system, newtrans, newnews, allytag, trade_forbidden, credits, sm_rboost, sm_col, sm_kartefakt, sm_tronic, patime, col, sc4 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row 		  = mysql_fetch_array($db_daten);
	$restyp01	  = $row[0];
	$restyp02	  = $row[1];
	$restyp03	  = $row[2];
	$restyp04	  = $row[3];
	$restyp05	  = $row[4];
	$punkte		  = $row["score"];
	$newtrans	  = $row["newtrans"];
	$newnews	  = $row["newnews"];
	$sector		  = $row["sector"];
	$system		= $row["system"];
	$techs		= $row["techs"];
	$tmf 		= $techs[65];
	$whg 		= $techs[4];
	$trade_forbidden=$row["trade_forbidden"];
	//21.01.2004, für den schwarzmarkt müssen die credits und die ticks ausgelesen werden, Isso
	//06.05.2004, für den schwarzmarkt müssen noch mehr felder ausgelesen werden, Isso
    $credits    = $row["credits"];
    $tick       = $row["tick"];
    $sm_rboost  = $row["sm_rboost"];
    $sm_col     = $row["sm_col"];
    $sm_kartefakt = $row["sm_kartefakt"];
    $sm_tronic  = $row["sm_tronic"];
    $palaufzeit = $row["patime"];
    $col        = $row["col"];

    $ssdaten=mysql_query("SELECT ssteuer FROM de_sector WHERE sec_id='$sector'");
	$ssdaten_fetch = mysql_fetch_array($ssdaten);
	$ssatz=$ssdaten_fetch[0];

	print("<!DOCTYPE HTML><html><head><title>$tradetrade_lang[title]</title>");
	include("cssinclude.php");
	print("</head><body>");
	
	if (isset($help_id) && strlen($help_id) > 0)
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.help.php");
		exit();
	}
	include("trade/trade.functions.inc.php");
	include("inc/sabotage.inc.php");
	$trade_sabotage = false;
	$trade_locked = getTradeStatus();
	//SABOTAGE
	
	//maximalen tick auslesen
	$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
	$trow     = mysql_fetch_array($result);
	$maxtick = $trow["tick"];
	
	$mysc4=$row["sc4"];
	
	//feststellen ob der handel sabotiert ist
	if($maxtick<$mysc4+$sv_sabotage[10][0] AND $mysc4>$sv_sabotage[10][0])
	{
		$trade_sabotage = true;
	}
	
	
	if (isset($action) && strlen($action)>0 && $trade_locked!=0 && !$trade_forbidden && !$trade_sabotage)
	{
		include("trade/trade.config.inc.php");
		$fleetconfig = ${"fleet_"."$ums_rasse"};
		if ($action == "res_sell")
		{
			$message = sellRes($ums_user_id, $displayname, $traderes, $tradeamount, $price, $currency, $de_trade_resoffer, $stornoticks, $ssatz, $ums_rasse);
		}
		elseif ($action == "res_buy")
		{
			$message = buyRes($ums_user_id, $displayname, $traderes, $tradeamount, $price, $currency, $de_trade_resrequest, $stornoticks, $ssatz, $ums_rasse);
		}
		elseif ($action == "fleet_sell")
		{
			$message = sellFleet($ums_user_id, $shiptype, $tradeamount, $price, $currency, $de_trade_fleetoffer, $stornoticks, $ssatz, $sector, $ums_rasse, $fleetconfig);
		}
		elseif ($action == "fleet_buy")
		{
			$message = buyFleet($ums_user_id, $shiptype, $tradeamount, $price, $currency, $de_trade_fleetrequest, $stornoticks, $ssatz, $sector, $ums_rasse, $fleetconfig);
		}
		elseif ($action == "tronic_sell")
		{
			$message = sellTronic($ums_user_id, $tradeamount);
		}
		elseif ($action == "depotbuy")
		{
			$message = depotbuy($ums_user_id, $shiptype, $tradeamount, $currency, $ums_rasse, $race, $restyp01, $restyp02, $restyp03, $restyp04, ${"fleet_"."$race"});
		}
		elseif ($action == "depotsell")
		{
			$message = depotsell($ums_user_id, $shiptype, $tradeamount, $price, $currency, $de_trade_fleetoffer, $stornoticks, $ssatz, $sector, $race, ${"fleet_"."$race"});
		}
		elseif ($action == "storno")
		{
			if ($type == "resoffer")
			{
				$message = stornoResoffer($ums_user_id, $id, $de_trade_resoffer, $stornotax, $traderstornotax);
			}
			elseif ($type == "resrequest")
			{
				$message = stornoResrequest($ums_user_id, $id, $de_trade_resrequest, $req_stornotax, $req_traderstornotax);
			}
			elseif ($type == "fleetoffer")
			{
				$result	= mysql_query("SELECT race FROM $de_trade_fleetoffer WHERE id='$id'");
				if ($result)
				{
					$numrows	= mysql_num_rows($result);
					if ($numrows == 1)
					{
						$values 	= mysql_fetch_array($result);
						$tmp_race = $values["race"];
					}
				}
				$tmp_fleetconfig = ${"fleet_"."$tmp_race"};
				$message = stornoFleetoffer($ums_user_id, $id, $de_trade_fleetoffer, $tmp_fleetconfig, $offer_stornotax, $offer_traderstornotax, $ums_rasse);
			}
			elseif ($type == "fleetrequest")
			{
				$message = stornofleetrequest($ums_user_id, $id, $de_trade_fleetrequest, $fleetconfig, $req_stornotax, $req_traderstornotax);
			}
			elseif ($type == "tronic")
			{
				$message = stornoTronic($ums_user_id, $id);
			}
			else
			{
				$message = "$tradetrade_lang[msg_1_1] <i>$type</i> $tradetrade_lang[msg_1_2].";
			}
		}
		else
		{
			$message = "$tradetrade_lang[msg_2]";
		}
		$u_values	= mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_user_data WHERE user_id='$ums_user_id'");
		$u_row 		= mysql_fetch_array($u_values);
		$restyp01	= $u_row[0];
		$restyp02	= $u_row[1];
		$restyp03	= $u_row[2];
		$restyp04	= $u_row[3];
		$restyp05	= $u_row[4];

	}
	include("resline.php");
	include("trade/trade.menu.inc.php");
	
	 echo '<div class="cell" style="width: 600px;">';
	
	if (strlen($message) > 0)
	{
		print("<br><table width=600><tr>");
		print("<td width=30 align=left valign=top><img src=\"".$ums_gpfad."g/trade/".$ums_rasse."_arz.gif\" alt=Information border=0> </td><td align=left><font size=1> $message</font><br>");
		print("</td></tr></table>");
	}
	if ($trade_locked == 0 && $viewmode != "blackmarket" && $viewmode != "artsell" && $viewmode != "artbuy")
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.msg.locked.php");
	}
	elseif ($trade_forbidden && $viewmode != "blackmarket")
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.msg.forbidden.php");
	}
	elseif($tmf==0 && $viewmode != "blackmarket")
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.msg.no_tmf.php");

	}
	elseif($whg==0 && $viewmode != "blackmarket")
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.msg.no_whg.php");
	}
	elseif ($trade_sabotage && $viewmode != "blackmarket")
	{
		include("trade/trade.config.inc.php");
		include("trade/trade.msg.sabotage.php");
	}
	else
	{
		include("trade/trade.config.inc.php");
		$fleetconfig = ${"fleet_"."$ums_rasse"};
		if (!in_array($viewmode, $modearray))
		{
			if (in_array($defaultmode, $modearray))
			{
				$viewmode = $defaultmode;
			}
			else
			{
				$viewmode = "overview";
			}
		}
		if ($viewmode == "overview")
		{
			include("trade/trade.overview.inc.php");
		}
		elseif($viewmode == "view_own")
		{
			include("trade/trade.view_own.inc.php");
		}
		elseif($viewmode == "config")
		{
			include("trade/trade.configuration.inc.php");
		}
		elseif($viewmode == "m_res")
		{
			$restype = "m";
			include("trade/trade.resform.inc.php");
		}
		elseif($viewmode == "d_res")
		{
			$restype = "d";
			include("trade/trade.resform.inc.php");
		}
		elseif($viewmode == "i_res")
		{
			$restype = "i";
			include("trade/trade.resform.inc.php");
		}
		elseif($viewmode == "e_res")
		{
			$restype = "e";
			include("trade/trade.resform.inc.php");
		}
		elseif($viewmode == "t_res")
		{
			$restype = "t";
			include("trade/trade.resform.inc.php");
		}
		elseif($viewmode == "m_fleet")
		{
			$restype = "m";
			include("trade/trade.fleetform.inc.php");
		}
		elseif($viewmode == "d_fleet")
		{
			$restype = "d";
			include("trade/trade.fleetform.inc.php");
		}
		elseif($viewmode == "i_fleet")
		{
			$restype = "i";
			include("trade/trade.fleetform.inc.php");
		}
		elseif($viewmode == "e_fleet")
		{
			$restype = "e";
			include("trade/trade.fleetform.inc.php");
		}
		elseif($viewmode == "depot")
		{
			$restype = "m";
			include("trade/trade.depot.inc.php");
		}
		elseif($viewmode == "m_fleetdepot")
		{
			$restype = "m";
			include("trade/trade.depot.inc.php");
		}
		elseif($viewmode == "d_fleetdepot")
		{
			$restype = "d";
			include("trade/trade.depot.inc.php");
		}
		elseif($viewmode == "i_fleetdepot")
		{
			$restype = "i";
			include("trade/trade.depot.inc.php");
		}
		elseif($viewmode == "e_fleetdepot")
		{
			$restype = "e";
			include("trade/trade.depot.inc.php");
		}
        //28.07.2005, artefaktverkauf laden
        /*
        elseif($viewmode == "artsell")
		{
			$dontshowfooter=1;
            	include("trade/trade.artsell.inc.php");
		}
        //28.07.2005, artefaktkauf laden
        elseif($viewmode == "artbuy")
		{
			$dontshowfooter=1;
            	include("trade/trade.artbuy.inc.php");
 		}
        //21.01.2005, schwarzmarkt laden Isso
        elseif($viewmode == "blackmarket")
		{
			$dontshowfooter=1;
            include("trade/trade.blackmarket.inc.php");
		}
		*/
	}
	$exec_time = getExecutionTime($starttime);
	//21.01.2004 nicht auf der schwarzmarktseite anzeigen, Isso
    if($dontshowfooter!=1)include("trade/trade.footer.inc.php");

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
    }
?>