<?PHP
include 'inc/lang/'.$sv_server_lang.'_trade.help.lang.php';

	$helpmodes = array("type_a", "menge_a", "handelstyp", "waehrung", "preis");
	$default	= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $help_id</h2><h3>$tradehelp_lang[msg_1].</h3>";
	$type_a 	= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $tradehelp_lang[gehandelteware]</h2><h3>$tradehelp_lang[msg_2].</h3>";
	$menge_a	= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $tradehelp_lang[gehandeltemenge]</h2><h3>$tradehelp_lang[msg_3].</h3>";
	$handelstyp	= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $tradehelp_lang[handelsoption]</h2><h3>$tradehelp_lang[msg_4]. </h3>";
	$waehrung	= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $tradehelp_lang[waehrung]</h2><h3>$tradehelp_lang[msg_5].</h3>";
	$preis 		= "<h1>$softwareversion - $tradehelp_lang[systemhilfe]</h1><h2>$tradehelp_lang[hilfebegriff]: $tradehelp_lang[ppe]</h2><h3>$tradehelp_lang[msg_6].</h3>";

	if (in_array($help_id, $helpmodes))
	{
		print($$help_id);
	}
	else
	{
		print($default);
	}
	print("<br><br><div align=center><a href=\"javascript:self.close()\">$tradehelp_lang[closewindow]</a></div>");
?>