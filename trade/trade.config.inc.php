<?PHP
//error_reporting(E_ALL);
if ($intick === 1)
{
	include '../inc/lang/'.$sv_server_lang.'_trade.config.lang.php';
}
else 
{
	include 'inc/lang/'.$sv_server_lang.'_trade.config.lang.php';
}

	$m = $tradeconfig_lang["m"];
	$d = $tradeconfig_lang["d"];
	$i = $tradeconfig_lang["i"];
	$e = $tradeconfig_lang["e"];
	$t = $tradeconfig_lang["t"];
	//21.01.2004 feld blackmarket für den schwarzmarkt hinzugefügt, Isso
    $modearray = array("m_res", "d_res", "i_res", "e_res", "t_res", "m_fleet", "d_fleet", "i_fleet", "e_fleet", "view_own", "overview", "config", "depot", "m_fleetdepot", "d_fleetdepot", "i_fleetdepot", "e_fleetdepot", "artsell", "artbuy", "blackmarket", "depotbuy", "depotsell");
	$softwareversion = "TradeSoft Version V10.0.4b";
	$de_trade_resoffer = "de_trade_resoffer";
	$de_trade_resrequest = "de_trade_resrequest";
	$de_trade_fleetoffer = "de_trade_fleetoffer";
	$de_trade_fleetrequest = "de_trade_fleetrequest";
	$de_trade_fleettransit = "de_trade_fleettransit";
	$de_tauction = "de_tauction";
	$stornoticks = 288;
	$stornotax = 15;
	$traderstornotax = 10;
	$req_stornotax = 0;
	$req_traderstornotax = 0;
	$offer_stornotax = 25;
	$offer_traderstornotax = 25;


		// /10 + tronic * 1000

	$fleet_1 = array(
						"race_id" 	=> "1",
						"race_name" => $tradeconfig_lang["ewiger"],
						"e81" 	=> array(
												"type" => "jaeger",
												"name" => $tradeconfig_lang["hornisse"],
												"dbfield" => "e81",
												"m" => 1000,
												"d" => 250,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 150,
												"buildtime" => 8,
												"trade_time" => 2,
												"value" => 0
											),
						"e82" 	=> array(
												"type" => "jagdboot",
												"name" => $tradeconfig_lang["guillotine"],
												"dbfield" => "e82",
												"m" => 4000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 600,
												"buildtime" => 16,
												"trade_time" => 4,
												"value" => 0
											),
						"e83" => array(
												"type" => "zerstoerer",
												"name" => $tradeconfig_lang["schakal"],
												"dbfield" => "e83",
												"m" => 15000,
												"d" => 5000,
												"i" => 1000,
												"e" => 0,
												"t" => 0,
												"points" => 2800,
												"buildtime" => 32,
												"trade_time" => 8,
												"value" => 0
											),
						"e84" => array(
												"type" => "kreuzer",
												"name" => $tradeconfig_lang["marauder"],
												"dbfield" => "e84",
												"m" => 30000,
												"d" => 10000,
												"i" => 1000,
												"e" => 1500,
												"t" => 0,
												"points" => 5900,
												"buildtime" => 64,
												"trade_time" => 16,
												"value" => 0
											),
						"e85" => array(
												"type" => "schlachtschiff",
												"name" => $tradeconfig_lang["zerberus"],
												"dbfield" => "e85",
												"m" => 50000,
												"d" => 20000,
												"i" => 2000,
												"e" => 4000,
												"t" => 2,
												"points" => 13200,
												"buildtime" => 96,
												"trade_time" => 24,
												"value" => 0
											),
						"e86" => array(
												"type" => "bomber",
												"name" => $tradeconfig_lang["nachtmar"],
												"dbfield" => "e86",
												"m" => 1500,
												"d" => 500,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 250,
												"buildtime" => 10,
												"trade_time" => 2,
												"value" => 0
											),
						"e87" => array(
												"type" => "transmitter",
												"name" => $tradeconfig_lang["transmitterschiff"],
												"dbfield" => "e87",
												"m" => 2000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 400,
												"buildtime" => 12,
												"trade_time" => 3,
												"value" => 0
											),
						"e88" => array(
												"type" => "traeger",
												"name" => $tradeconfig_lang["hydra"],
												"dbfield" => "e88",
												"m" => 50000,
												"d" => 30000,
												"i" => 5000,
												"e" => 5000,
												"t" => 1,
												"points" => 15500,
												"buildtime" => 80,
												"trade_time" => 20,
												"value" => 0
											)
					);


	$fleet_2 = array(
						"race_id" 	=> "2",
						"race_name" => $tradeconfig_lang["ishtar"],
						"e81" 	=> array(
												"type" => "jaeger",
												"name" => $tradeconfig_lang["caesar"],
												"dbfield" => "e81",
												"m" => 1250,
												"d" => 150,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 155,
												"buildtime" => 8,
												"trade_time" => 2,
												"value" => 0
											),
						"e82" 	=> array(
												"type" => "jagdboot",
												"name" => $tradeconfig_lang["paladin"],
												"dbfield" => "e82",
												"m" => 3500,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 550,
												"buildtime" => 16,
												"trade_time" => 4,
												"value" => 0
											),
						"e83" => array(
												"type" => "zerstoerer",
												"name" => $tradeconfig_lang["vollstrecker"],
												"dbfield" => "e83",
												"m" => 15500,
												"d" => 4500,
												"i" => 1500,
												"e" => 0,
												"t" => 0,
												"points" => 2900,
												"buildtime" => 32,
												"trade_time" => 8,
												"value" => 0
											),
						"e84" => array(
												"type" => "kreuzer",
												"name" => $tradeconfig_lang["imperator"],
												"dbfield" => "e84",
												"m" => 32000,
												"d" => 8000,
												"i" => 1000,
												"e" => 2000,
												"t" => 0,
												"points" => 5900,
												"buildtime" => 64,
												"trade_time" => 16,
												"value" => 0
											),
						"e85" => array(
												"type" => "schlachtschiff",
												"name" => $tradeconfig_lang["excalibur"],
												"dbfield" => "e85",
												"m" => 45000,
												"d" => 30000,
												"i" => 2000,
												"e" => 2000,
												"t" => 2,
												"points" => 13900,
												"buildtime" => 96,
												"trade_time" => 24,
												"value" => 0
											),
						"e86" => array(
												"type" => "bomber",
												"name" => $tradeconfig_lang["phalanx"],
												"dbfield" => "e86",
												"m" => 1200,
												"d" => 400,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 200,
												"buildtime" => 10,
												"trade_time" => 2,
												"value" => 0
											),
						"e87" => array(
												"type" => "transmitter",
												"name" => $tradeconfig_lang["merlin"],
												"dbfield" => "e87",
												"m" => 2000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 400,
												"buildtime" => 12,
												"trade_time" => 3,
												"value" => 0
											),
						"e88" => array(
												"type" => "traeger",
												"name" => $tradeconfig_lang["colossus"],
												"dbfield" => "e88",
												"m" => 55000,
												"d" => 25000,
												"i" => 7000,
												"e" => 5000,
												"t" => 2,
												"points" => 16600,
												"buildtime" => 80,
												"trade_time" => 20,
												"value" => 0
											)
					);

	$fleet_3 = array(
						"race_id" 	=> "3",
						"race_name" => $tradeconfig_lang["ktharr"],
						"e81" 	=> array(
												"type" => "jaeger",
												"name" => $tradeconfig_lang["spider"],
												"dbfield" => "e81",
												"m" => 750,
												"d" => 500,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 175,
												"buildtime" => 8,
												"trade_time" => 2,
												"value" => 0
											),
						"e82" 	=> array(
												"type" => "jagdboot",
												"name" => $tradeconfig_lang["arcticspider"],
												"dbfield" => "e82",
												"m" => 4000,
												"d" => 1500,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 700,
												"buildtime" => 16,
												"trade_time" => 4,
												"value" => 0
											),
						"e83" => array(
												"type" => "zerstoerer",
												"name" => $tradeconfig_lang["werespider"],
												"dbfield" => "e83",
												"m" => 15000,
												"d" => 2500,
												"i" => 1500,
												"e" => 1000,
												"t" => 0,
												"points" => 2850,
												"buildtime" => 32,
												"trade_time" => 8,
												"value" => 0
											),
						"e84" => array(
												"type" => "kreuzer",
												"name" => $tradeconfig_lang["tarantula"],
												"dbfield" => "e84",
												"m" => 30000,
												"d" => 6500,
												"i" => 2000,
												"e" => 2200,
												"t" => 0,
												"points" => 5780,
												"buildtime" => 64,
												"trade_time" => 16,
												"value" => 0
											),
						"e85" => array(
												"type" => "schlachtschiff",
												"name" => $tradeconfig_lang["blackwidow"],
												"dbfield" => "e85",
												"m" => 45000,
												"d" => 25000,
												"i" => 2000,
												"e" => 3000,
												"t" => 1,
												"points" => 12300,
												"buildtime" => 96,
												"trade_time" => 24,
												"value" => 0
											),
						"e86" => array(
												"type" => "bomber",
												"name" => $tradeconfig_lang["hellspider"],
												"dbfield" => "e86",
												"m" => 1000,
												"d" => 500,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 200,
												"buildtime" => 10,
												"trade_time" => 2,
												"value" => 0
											),
						"e87" => array(
												"type" => "transmitter",
												"name" => $tradeconfig_lang["netzfaenger"],
												"dbfield" => "e87",
												"m" => 2000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 400,
												"buildtime" => 12,
												"trade_time" => 3,
												"value" => 0
											),
						"e88" => array(
												"type" => "traeger",
												"name" => $tradeconfig_lang["gigantula"],
												"dbfield" => "e88",
												"m" => 60000,
												"d" => 30000,
												"i" => 6000,
												"e" => 6000,
												"t" => 1,
												"points" => 17200,
												"buildtime" => 80,
												"trade_time" => 20,
												"value" => 0
											)
					);

	$fleet_4 = array(
						"race_id" 	=> "4",
						"race_name" => $tradeconfig_lang["zthaara"],
						"e81" 	=> array(
												"type" => "jaeger",
												"name" => $tradeconfig_lang["wespe"],
												"dbfield" => "e81",
												"m" => 500,
												"d" => 150,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 80,
												"buildtime" => 6,
												"trade_time" => 1,
												"value" => 0
											),
						"e82" 	=> array(
												"type" => "jagdboot",
												"name" => $tradeconfig_lang["feuerskorpion"],
												"dbfield" => "e82",
												"m" => 3000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 500,
												"buildtime" => 14,
												"trade_time" => 3,
												"value" => 0
											),
						"e83" => array(
												"type" => "zerstoerer",
												"name" => $tradeconfig_lang["geisterschrecke"],
												"dbfield" => "e83",
												"m" => 10000,
												"d" => 5000,
												"i" => 2000,
												"e" => 500,
												"t" => 0,
												"points" => 2800,
												"buildtime" => 30,
												"trade_time" => 7,
												"value" => 0
											),
						"e84" => array(
												"type" => "kreuzer",
												"name" => $tradeconfig_lang["skarabaeus"],
												"dbfield" => "e84",
												"m" => 25000,
												"d" => 10000,
												"i" => 1000,
												"e" => 500,
												"t" => 0,
												"points" => 5000,
												"buildtime" => 60,
												"trade_time" => 15,
												"value" => 0
											),
						"e85" => array(
												"type" => "schlachtschiff",
												"name" => $tradeconfig_lang["mantis"],
												"dbfield" => "e85",
												"m" => 50000,
												"d" => 15000,
												"i" => 3000,
												"e" => 3000,
												"t" => 2,
												"points" => 12100,
												"buildtime" => 90,
												"trade_time" => 22,
												"value" => 0
											),
						"e86" => array(
												"type" => "bomber",
												"name" => $tradeconfig_lang["hoellenkaefer"],
												"dbfield" => "e86",
												"m" => 1000,
												"d" => 750,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 250,
												"buildtime" => 8,
												"trade_time" => 2,
												"value" => 0
											),
						"e87" => array(
												"type" => "transmitter",
												"name" => $tradeconfig_lang["sammler"],
												"dbfield" => "e87",
												"m" => 2000,
												"d" => 1000,
												"i" => 0,
												"e" => 0,
												"t" => 0,
												"points" => 400,
												"buildtime" => 6,
												"trade_time" => 1,
												"value" => 0
											),
						"e88" => array(
												"type" => "traeger",
												"name" => $tradeconfig_lang["ekelbrueter"],
												"dbfield" => "e88",
												"m" => 50000,
												"d" => 25000,
												"i" => 7000,
												"e" => 6000,
												"t" => 1,
												"points" => 15500,
												"buildtime" => 76,
												"trade_time" => 19,
												"value" => 0
											)
					);

	$fleet_1["e81"]["value"] = ((1 * $fleet_1["e81"]["m"]) + (2 * $fleet_1["e81"]["d"]) + (3 * $fleet_1["e81"]["i"]) + (4 * $fleet_1["e81"]["e"]) + (10000 * $fleet_1["e81"]["t"]));
	$fleet_1["e82"]["value"] = ((1 * $fleet_1["e82"]["m"]) + (2 * $fleet_1["e82"]["d"]) + (3 * $fleet_1["e82"]["i"]) + (4 * $fleet_1["e82"]["e"]) + (10000 * $fleet_1["e82"]["t"]));
	$fleet_1["e83"]["value"] = ((1 * $fleet_1["e83"]["m"]) + (2 * $fleet_1["e83"]["d"]) + (3 * $fleet_1["e83"]["i"]) + (4 * $fleet_1["e83"]["e"]) + (10000 * $fleet_1["e83"]["t"]));
	$fleet_1["e84"]["value"] = ((1 * $fleet_1["e84"]["m"]) + (2 * $fleet_1["e84"]["d"]) + (3 * $fleet_1["e84"]["i"]) + (4 * $fleet_1["e84"]["e"]) + (10000 * $fleet_1["e84"]["t"]));
	$fleet_1["e85"]["value"] = ((1 * $fleet_1["e85"]["m"]) + (2 * $fleet_1["e85"]["d"]) + (3 * $fleet_1["e85"]["i"]) + (4 * $fleet_1["e85"]["e"]) + (10000 * $fleet_1["e85"]["t"]));
	$fleet_1["e86"]["value"] = ((1 * $fleet_1["e86"]["m"]) + (2 * $fleet_1["e86"]["d"]) + (3 * $fleet_1["e86"]["i"]) + (4 * $fleet_1["e86"]["e"]) + (10000 * $fleet_1["e86"]["t"]));
	$fleet_1["e87"]["value"] = ((1 * $fleet_1["e87"]["m"]) + (2 * $fleet_1["e87"]["d"]) + (3 * $fleet_1["e87"]["i"]) + (4 * $fleet_1["e87"]["e"]) + (10000 * $fleet_1["e87"]["t"]));
	$fleet_1["e88"]["value"] = ((1 * $fleet_1["e88"]["m"]) + (2 * $fleet_1["e88"]["d"]) + (3 * $fleet_1["e88"]["i"]) + (4 * $fleet_1["e88"]["e"]) + (10000 * $fleet_1["e88"]["t"]));

	$fleet_2["e81"]["value"] = ((1 * $fleet_2["e81"]["m"]) + (2 * $fleet_2["e81"]["d"]) + (3 * $fleet_2["e81"]["i"]) + (4 * $fleet_2["e81"]["e"]) + (10000 * $fleet_2["e81"]["t"]));
	$fleet_2["e82"]["value"] = ((1 * $fleet_2["e82"]["m"]) + (2 * $fleet_2["e82"]["d"]) + (3 * $fleet_2["e82"]["i"]) + (4 * $fleet_2["e82"]["e"]) + (10000 * $fleet_2["e82"]["t"]));
	$fleet_2["e83"]["value"] = ((1 * $fleet_2["e83"]["m"]) + (2 * $fleet_2["e83"]["d"]) + (3 * $fleet_2["e83"]["i"]) + (4 * $fleet_2["e83"]["e"]) + (10000 * $fleet_2["e83"]["t"]));
	$fleet_2["e84"]["value"] = ((1 * $fleet_2["e84"]["m"]) + (2 * $fleet_2["e84"]["d"]) + (3 * $fleet_2["e84"]["i"]) + (4 * $fleet_2["e84"]["e"]) + (10000 * $fleet_2["e84"]["t"]));
	$fleet_2["e85"]["value"] = ((1 * $fleet_2["e85"]["m"]) + (2 * $fleet_2["e85"]["d"]) + (3 * $fleet_2["e85"]["i"]) + (4 * $fleet_2["e85"]["e"]) + (10000 * $fleet_2["e85"]["t"]));
	$fleet_2["e86"]["value"] = ((1 * $fleet_2["e86"]["m"]) + (2 * $fleet_2["e86"]["d"]) + (3 * $fleet_2["e86"]["i"]) + (4 * $fleet_2["e86"]["e"]) + (10000 * $fleet_2["e86"]["t"]));
	$fleet_2["e87"]["value"] = ((1 * $fleet_2["e87"]["m"]) + (2 * $fleet_2["e87"]["d"]) + (3 * $fleet_2["e87"]["i"]) + (4 * $fleet_2["e87"]["e"]) + (10000 * $fleet_2["e87"]["t"]));
	$fleet_2["e88"]["value"] = ((1 * $fleet_2["e88"]["m"]) + (2 * $fleet_2["e88"]["d"]) + (3 * $fleet_2["e88"]["i"]) + (4 * $fleet_2["e88"]["e"]) + (10000 * $fleet_2["e88"]["t"]));

	$fleet_3["e81"]["value"] = ((1 * $fleet_3["e81"]["m"]) + (2 * $fleet_3["e81"]["d"]) + (3 * $fleet_3["e81"]["i"]) + (4 * $fleet_3["e81"]["e"]) + (10000 * $fleet_3["e81"]["t"]));
	$fleet_3["e82"]["value"] = ((1 * $fleet_3["e82"]["m"]) + (2 * $fleet_3["e82"]["d"]) + (3 * $fleet_3["e82"]["i"]) + (4 * $fleet_3["e82"]["e"]) + (10000 * $fleet_3["e82"]["t"]));
	$fleet_3["e83"]["value"] = ((1 * $fleet_3["e83"]["m"]) + (2 * $fleet_3["e83"]["d"]) + (3 * $fleet_3["e83"]["i"]) + (4 * $fleet_3["e83"]["e"]) + (10000 * $fleet_3["e83"]["t"]));
	$fleet_3["e84"]["value"] = ((1 * $fleet_3["e84"]["m"]) + (2 * $fleet_3["e84"]["d"]) + (3 * $fleet_3["e84"]["i"]) + (4 * $fleet_3["e84"]["e"]) + (10000 * $fleet_3["e84"]["t"]));
	$fleet_3["e85"]["value"] = ((1 * $fleet_3["e85"]["m"]) + (2 * $fleet_3["e85"]["d"]) + (3 * $fleet_3["e85"]["i"]) + (4 * $fleet_3["e85"]["e"]) + (10000 * $fleet_3["e85"]["t"]));
	$fleet_3["e86"]["value"] = ((1 * $fleet_3["e86"]["m"]) + (2 * $fleet_3["e86"]["d"]) + (3 * $fleet_3["e86"]["i"]) + (4 * $fleet_3["e86"]["e"]) + (10000 * $fleet_3["e86"]["t"]));
	$fleet_3["e87"]["value"] = ((1 * $fleet_3["e87"]["m"]) + (2 * $fleet_3["e87"]["d"]) + (3 * $fleet_3["e87"]["i"]) + (4 * $fleet_3["e87"]["e"]) + (10000 * $fleet_3["e87"]["t"]));
	$fleet_3["e88"]["value"] = ((1 * $fleet_3["e88"]["m"]) + (2 * $fleet_3["e88"]["d"]) + (3 * $fleet_3["e88"]["i"]) + (4 * $fleet_3["e88"]["e"]) + (10000 * $fleet_3["e88"]["t"]));

	$fleet_4["e81"]["value"] = ((1 * $fleet_4["e81"]["m"]) + (2 * $fleet_4["e81"]["d"]) + (3 * $fleet_4["e81"]["i"]) + (4 * $fleet_4["e81"]["e"]) + (10000 * $fleet_4["e81"]["t"]));
	$fleet_4["e82"]["value"] = ((1 * $fleet_4["e82"]["m"]) + (2 * $fleet_4["e82"]["d"]) + (3 * $fleet_4["e82"]["i"]) + (4 * $fleet_4["e82"]["e"]) + (10000 * $fleet_4["e82"]["t"]));
	$fleet_4["e83"]["value"] = ((1 * $fleet_4["e83"]["m"]) + (2 * $fleet_4["e83"]["d"]) + (3 * $fleet_4["e83"]["i"]) + (4 * $fleet_4["e83"]["e"]) + (10000 * $fleet_4["e83"]["t"]));
	$fleet_4["e84"]["value"] = ((1 * $fleet_4["e84"]["m"]) + (2 * $fleet_4["e84"]["d"]) + (3 * $fleet_4["e84"]["i"]) + (4 * $fleet_4["e84"]["e"]) + (10000 * $fleet_4["e84"]["t"]));
	$fleet_4["e85"]["value"] = ((1 * $fleet_4["e85"]["m"]) + (2 * $fleet_4["e85"]["d"]) + (3 * $fleet_4["e85"]["i"]) + (4 * $fleet_4["e85"]["e"]) + (10000 * $fleet_4["e85"]["t"]));
	$fleet_4["e86"]["value"] = ((1 * $fleet_4["e86"]["m"]) + (2 * $fleet_4["e86"]["d"]) + (3 * $fleet_4["e86"]["i"]) + (4 * $fleet_4["e86"]["e"]) + (10000 * $fleet_4["e86"]["t"]));
	$fleet_4["e87"]["value"] = ((1 * $fleet_4["e87"]["m"]) + (2 * $fleet_4["e87"]["d"]) + (3 * $fleet_4["e87"]["i"]) + (4 * $fleet_4["e87"]["e"]) + (10000 * $fleet_4["e87"]["t"]));
	$fleet_4["e88"]["value"] = ((1 * $fleet_4["e88"]["m"]) + (2 * $fleet_4["e88"]["d"]) + (3 * $fleet_4["e88"]["i"]) + (4 * $fleet_4["e88"]["e"]) + (10000 * $fleet_4["e88"]["t"]));

/*	print("<hr>");
	print_r($fleet_1);
	print("<hr>");
	print_r($fleet_2);
	print("<hr>");
	print_r($fleet_3);
	print("<hr>");
	print_r($fleet_4);
	print("<hr>");*/
?>