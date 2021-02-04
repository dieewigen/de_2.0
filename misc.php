<?php

function html_form_start($action="",$method="POST")
{
	global $html_form_formcnt;
	$html_form_formcnt++;

	return "<FORM Name='form".$html_form_formcnt."' ".($action!=""? "Action='".$action."' ": "")."Method='".$method."' EncType='multipart/form-data'>";
}

function html_form_end($submittext="Daten übertragen")
{
	global $html_form_formcnt;
	return "<P Align='Center'><CENTER><INPUT Type='Submit'></CENTER></P></FORM>";
}

function html_input_text($varname, $name, $value="", $disabled=false, $xtra="")
{
	return "<TR><TD Align='Right' Style='width:5%'><FONT Size=2>$name&nbsp;</FONT></TD><TD><INPUT ".($varname!=""? "Name='".$varname."' ": "")."Type='Text' Value='".htmlspecialchars($value, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')."' Style='width: 5%'".($disabled? " OnFocus='blur()'": "").($xtra!=""? " ".$xtra: "")."></TD></TR>";
}

function html_input_hidden($varname, $val, $xtra="")
{
	return "<INPUT Width='100%' Name='".$varname."' Type='Hidden' Value=\"".htmlspecialchars($val, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')."\"".($xtra!=''? " ".$xtra: "").">";
}

function StandardLayout()
{
	echo "<!DOCTYPE HTML><html><head><title>Tronic Versteigerung</title><link rel=\"stylesheet\" type=\"text/css\" href=\"f.css\"></head><body>";
}

function GetUserIdFromSID($id)
{
	return 1;
}

?>
