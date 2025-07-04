<?php
ob_start();
include_once "inc/env.inc.php";
ignore_user_abort(true);
//if($disablegzip!=1)ob_start("ob_gzhandler");

/*
if($disablegzip!=1){ob_start("compress_output"); header("Content-Encoding: gzip");}

function compress_output($output) { 
$code=gzencode($output, 9);
return $code;
}
*/

//ob_start("ob_gzhandler");

include_once('lib/mysql_wrapper.inc.php');

$db = @mysql_connect($GLOBALS['env_db_dieewigen_host'], $GLOBALS['env_db_dieewigen_user'], $GLOBALS['env_db_dieewigen_password'], true) or die("A: Keine Verbindung zur Datenbank möglich.");
mysql_select_db($GLOBALS['env_db_dieewigen_database'], $db);
mysql_set_charset("utf8mb4", $db);

$GLOBALS['dbi'] = mysqli_connect($GLOBALS['env_db_dieewigen_host'], $GLOBALS['env_db_dieewigen_user'], $GLOBALS['env_db_dieewigen_password'], $GLOBALS['env_db_dieewigen_database']) or die("B: Keine Verbindung zur Datenbank möglich.");
$GLOBALS['dbi']->set_charset("utf8mb4");

//Accountverwaltung einbinden
$GLOBALS['dbi_ls'] = mysqli_connect($GLOBALS['env_db_loginsystem_host'], $GLOBALS['env_db_loginsystem_user'], $GLOBALS['env_db_loginsystem_password'], $GLOBALS['env_db_loginsystem_database']);
$GLOBALS['dbi_ls']->set_charset("utf8mb4");

array_walk_recursive($_GET, function(&$leaf) {
  if (is_string($leaf)){
     $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
  }
});

array_walk_recursive($_POST, function(&$leaf) {
  if (is_string($leaf)){
     $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
  }
});

array_walk_recursive($_COOKIE, function(&$leaf) {
  if (is_string($leaf)){
     $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
  }
});

array_walk_recursive($_REQUEST, function(&$leaf) {
  if (is_string($leaf)){
     $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
  }
});

foreach ($_GET as $key => $val){
  $$key = $_GET[$key];
}

if(isset($_SESSION)){
  foreach ($_SESSION as $key => $val){
  	if($key!='save_get' && $key!='save_post' && $key!='save_request'){
  		$$key = $_SESSION[$key];
  	}
  }
}

$GLOBALS['dbi']->set_charset("utf8");

/*
foreach ($_REQUEST as $key => $val){
		$$key = $_REQUEST[$key];
}

if(isset($_REQUEST)){
	foreach ($_REQUEST as $key => $val){
		$_REQUEST[$key] = mysql_real_escape_string($val);
		$$key = $_REQUEST[$key];
	}
}

if(isset($_SESSION))
foreach ($_SESSION as $key => $val){
	if($key!='save_get' && $key!='save_post' && $key!='save_request'){
		$_SESSION[$key] = mysql_real_escape_string($val);
		$$key = $_SESSION[$key];
	}
}
*/

if(isset($_SESSION['ums_user_id']) && $_SESSION['ums_user_id']>0){ 
	$ip=$_SERVER['REMOTE_ADDR'];
	$parts=explode(".",$ip);
	$ip=$parts[0].'.x.'.($parts[2] ?? '?').'.'.($parts[3] ?? '?'); //IP anonymisieren, damit nicht jeder die IP sieht

	$ums_user_id=$_SESSION['ums_user_id'];

	//post und get-variablen mitloggen, wenn das Logging aktiviert ist
	if(isset($GLOBALS['sv_log_player_actions']) && $GLOBALS['sv_log_player_actions']==1){
		//noch neueres logging
		if(!isset($_REQUEST["check4new"]) AND !isset($_REQUEST["managechat"])){
			$datenstring=''; 
			$variableSets = array(
			"P:" => $_POST,
			"G:" => $_GET);

			function printElementHtml( $value, $key ) 
			{
			global $datenstring;
			//passwrter rausfiltern
			if($key=='pass')$value='****';
			if($key=='newpass')$value='****';
			if($key=='oldpass')$value='****';
			if($key=='pass1')$value='****';
			if($key=='pass2')$value='****';
			if($key=='delpass')$value='****';
			if($key=='urlpass')$value='****';
		
			$datenstring.=$key. ": ".$value."\n";
			//echo $key . " => ";
			//print_r( $value );
			//echo "<br>";
			}

			foreach ( $variableSets as $setName => $variableSet ) 
			{
			if ( isset( $variableSet ) ) 
			{
				//echo "<br><br><hr size='1'>";
				//echo "$setName<br>";
				$datenstring.=$setName."\n";
				array_walk( $variableSet, 'printElementHtml' );
			}
			}

			if(strlen($datenstring)==6)$datenstring='';
			
			//mysql_select_db("logging",$dblog);
			$scriptname=$_SERVER['PHP_SELF'];
			//Dateiendung entfernen um Platz zu sparen
			if($scriptname[0]=='/')$scriptname = substr($scriptname,1);
			$scriptname=str_replace('.php','',$scriptname);

			//mysql_query("INSERT INTO de_user_log (serverid, userid, time, ip, file, getpost) VALUES('$sv_servid','$ums_user_id',NOW(), '$ip', '$scriptname', '$datenstring')",$db);

			$db_log = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");
			mysqli_query($db_log, "INSERT INTO gameserverlogdata (serverid, userid, time, ip, file, getpost) VALUES('$sv_servid','$ums_user_id',NOW(), '$ip', '$scriptname', '$datenstring')"); 
			
			unset($datenstring);
		}
	}
	//noch neueres logging - ende
  
  										
	//Auf Accountstatus prüfen, dient z.b. fürs sperren, damit er sofort rausfliegt und auch zum moven der accounts
	//dazu pa-daten auslesen, damit der status immer aktuell ist
	//$db_datens=mysql_query("SELECT status, blocktime, activetime from de_login WHERE user_id='$ums_user_id'",$db);
	$db_datens=mysql_query("SELECT de_login.status, de_login.blocktime, de_login.activetime, de_user_data.spielername, de_user_data.rasse, de_user_data.patime, de_user_data.credits FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_user_data.user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_datens);
	$user_accstatus=$row["status"];
	$ad_blocktime=$row["blocktime"];
	$ic_activetime=$row["activetime"];
	$user_patime=$row["patime"];
	$user_credits=$row["credits"];
	//rasse und spielername neu setzen, wenn nötig
	if($row['rasse']!=$_SESSION['ums_rasse'] OR $row['spielername']!=$_SESSION['ums_spielername']){
		$_SESSION['ums_rasse']=$row['rasse'];
		$ums_rasse=$_SESSION['ums_rasse'];
		$_SESSION['ums_spielername']=$row['spielername'];
		$ums_spielername=$_SESSION['ums_spielername'];
	}
		
	//wenn der status ungleich 1 ist, dann sollte der spieler nicht eingeloggt sein
	if ($user_accstatus!=1){
		session_destroy();
		header("Location: index.php");
	}
	
	//pa-status festlegen
	if($user_patime>time())$ums_premium=1;else $ums_premium=0;
	
	if(!isset($eftachatbotdefensedisable)){
		$eftachatbotdefensedisable=0;
	}

	if(!isset($givenocredit)){
		$givenocredit=0;
	}

	//aktivitätsbonus
	if(time()>$ic_activetime+$sv_activetime AND $eftachatbotdefensedisable!=1 AND $givenocredit!=1){
		mysql_query("UPDATE de_login SET activetime=".time()." WHERE user_id='$ums_user_id'  AND status=1",$db);
		if($user_credits<$sv_credits_max_collect)
		{
		mysql_query("UPDATE de_user_data SET credits=credits+1, actpoints=actpoints+1 WHERE user_id='$ums_user_id'",$db);
		$show_activetime_msg=1;
		}
		else
		{
		mysql_query("UPDATE de_user_data SET actpoints=actpoints+1 WHERE user_id='$ums_user_id'",$db);
		}
	}

	if(!isset($_SESSION["aktivitaet_time"])){
		$_SESSION["aktivitaet_time"]=0;
	}

	//die aktivität mitloggen
	//update aus performancegründen nur alle 5 minuten
	if($eftachatbotdefensedisable!=1) //kein chataufruf
	{
		//update aus performancegründen nur alle 5 minuten
		if($_SESSION["aktivitaet_time"]+300<time())
		{
		mysql_query("UPDATE de_login SET last_click=NOW(), last_ip='$ip' WHERE user_id='$ums_user_id' AND status=1",$db);
		$time=(int)date("H");
		$zeit=date("Y-m-d");
		mysql_query("UPDATE de_user_stat SET h$time='2' WHERE user_id='$ums_user_id' AND datum='$zeit' AND h$time<'2'",$db);
		$_SESSION["aktivitaet_time"]=time();
		}
	}
	else //chataufruf
	{
		//die aktivität mitloggen
		//update aus performancegründen nur alle 5 minuten
		if(!isset($_SESSION["aktivitaet_chat_time"])){
		$_SESSION["aktivitaet_chat_time"]=0;
		}

		if($_SESSION["aktivitaet_chat_time"]+300<time())
		{
		$time=intval(date("H"));
		$zeit=date("Y-m-d");
		mysql_query("UPDATE de_user_stat SET h$time='1' WHERE user_id='$ums_user_id' AND datum='$zeit' AND h$time<'1'",$db);
		$_SESSION["aktivitaet_chat_time"]=time();
		}
	}

	if ($ums_zeitstempel+300<time() && $eftachatbotdefensedisable!=1) //nur alle 5 minuten aktualisieren
	{
		mysql_query("UPDATE de_login SET last_login=NOW() WHERE user_id='$ums_user_id'  AND status=1",$db);
		$ums_zeitstempel=time();
	}

} 
?>