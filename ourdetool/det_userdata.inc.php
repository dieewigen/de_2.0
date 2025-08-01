<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

foreach ($_REQUEST as $key => $val){
  $$key = $_REQUEST[$key];
}

$disablegzip=1;
//aulesen der userdaten
$det_username=isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : '';
$det_username=isset($_SERVER["REMOTE_USER"]) ? $_SERVER["REMOTE_USER"] : '';

$filename='user/'.$det_username.'.txt';
$userfile = fopen ($filename, 'r');

$det_email=trim(fgets($userfile, 1024));
$det_userlevel=trim(fgets($userfile, 1024));
fclose($userfile);

if($_SERVER['SERVER_NAME']=='xde.bgam.es'){
	if($det_email=='cannopio@die-ewigen.com'){
		$det_userlevel=99;
	}
}

//rechtelevel der datei auslesen
//zuerstmal den scriptnamen rausfinden
$string=$_SERVER["SCRIPT_FILENAME"];

//wo ist der letzte / und der .
$spos=0;
$ppos=0;
for ($izz=0;$izz<strlen($string);$izz++)if($string[$izz]=="/")$spos=$izz+1;
for ($izz=0;$izz<strlen($string);$izz++)if($string[$izz]==".")$ppos=$izz;
$filename = substr($string, $spos, $ppos-$spos).'.lvl';
$userfile = fopen ($filename, 'r');
$file_userlevel=trim(fgets($userfile, 1024));
fclose($userfile);

//schauen ob der userlevel f�r das script ausreicht
if ($det_userlevel>$file_userlevel) die ('<font color="#FF0000"><b><br>Du hast nicht den n&ouml;tigen Userlevel f�r diese Seite.');

//speichern der aktion
//if ($ums_user_id>0) //post und get-variablen mitloggen
//{
$datenstring='';
$variableSets = array(
   "Post:" => $_POST,
   "Get:" => $_GET//,
   // "Session:" => $HTTP_SESSION_VARS,
   // "Cookies:" => $HTTP_COOKIE_VARS,
   // "Server:" => $HTTP_SERVER_VARS,
   // "Environment:" => $HTTP_ENV_VARS
);

function printElementHtml( $value, $key ) {
	global $datenstring;
   	$datenstring.=$key. " => ".$value."\n";
   	//echo $key . " => ";
   	//print_r( $value );
   	//echo "<br>";
}

foreach ( $variableSets as $setName => $variableSet ) {
	if ( isset( $variableSet ) ) {
		//echo "<br><br><hr size='1'>";
		//echo "$setName<br>";
		$datenstring.=$setName."\n";
		array_walk( $variableSet, 'printElementHtml' );
	}
}

$datum=date("Y-m-d H:i:s",time());
$ip=getenv("REMOTE_ADDR");
$datenstring="Zeit: $datum\nIP: $ip\nDatei: ".$_SERVER['PHP_SELF']."\n".$datenstring."\n--------------------------------------\n";
$fp234=fopen("logs/".$det_username.".txt", "a");
fputs($fp234, $datenstring);
fclose($fp234);
