<?php
if(!isset($_SESSION)){ 
	session_start(); 
} 
//sprachdatei laden
if(isset($session_subdir) && $session_subdir==1)$session_path='../';else $session_path='';
include_once $session_path."inc/lang/".$sv_server_lang."_session.lang.php";
include_once $session_path."inc/".$sv_server_lang."_links.inc.php";

if(isset($_SESSION['ums_user_id'])){
	$ums_user_id=$_SESSION['ums_user_id'];
}else{
	$ums_user_id=-1;
}

//wenn nötig, die get/post/request-daten restaurieren
if(isset($_SESSION['restore_botcheck_data'])){
	$_GET=		$_SESSION['save_get'];
	$_POST=		$_SESSION['save_post'];
	$_REQUEST=	$_SESSION['save_request'];
	
	//echo '<br>A:';
	//print_r($_SESSION['save_request']);
	//var_dump($_REQUEST);
	unset($_SESSION['restore_botcheck_data']);
	unset($_SESSION['save_get']);
	unset($_SESSION['save_post']);
	unset($_SESSION['save_request']);
}
//var_dump($_SESSION['save_request']);
//echo '<br>B:';
//var_dump($_REQUEST);
//array(1) { ["sf"]=> string(1) "9" } 

function fehlermsg($msg){
  global $thisisefta, $usestdtemplate;

  echo '<html><head>';
  echo '<script language="JavaScript">
		if(top.frames.length > 0)
		top.location.href=self.location;
		</script>';
  if($usestdtemplate!=1)include "cssinclude.php";
  echo '</head><body>';
  echo $msg;
  echo '</body></html>';
}

//schaue ob man eingeloggt ist
if (!isset($_SESSION['ums_user_id'])){
	$usestdtemplate=1; 

	$topban_votebutton='';

	fehlermsg('
	<link href="https://www.die-ewigen.com/default.css" rel="stylesheet" type="text/css">
	<style type="text/css">
	<!--
		@import url("https://www.die-ewigen.com/layout.css");
	-->
	</style>
	
	<br><center>

	<table border="0" cellpadding="0" cellspacing="0" width="600">

	<tr align="center"><td colspan="4" align="center"><h1 id="title5">&nbsp;</h1></td></tr>

	<tr align="center"><td><br><br>
	<font size="2" color="FF0000">'.$session_lang['error1'].'<br><br><br><font color="00FF00">'.
	  $session_lang['error3'].' <a href="'.$sv_link[1].'">'.$session_lang['error4'].'</a><br><br><br><br><font size="1">
	  <br><br><br><br>
	</td></tr>
	<tr align="center"><td><br><br>
	'.$topban_votebutton.'<br><br><br><br><br>
	</td></tr>

	<tr>
	<td><div class="hr1"><div><hr></div></div></td></tr>
	</table>');
	  exit;
}

//'.$session_lang['error5'].' <a href="index.php">'.$session_lang['error4'].'</a>
//speziallogging
/*
if($ums_user_id==1840 OR $ums_user_id==1){  
	$variableSets = array(
	"Post:" => $_POST,
	"Get:" => $_GET,
	"Session:" => $_SESSION
	// "Cookies:" => $HTTP_COOKIE_VARS,
	// "Server:" => $HTTP_SERVER_VARS,
	// "Environment:" => $HTTP_ENV_VARS
	);

	function printElementHtml2( $value, $key ) {
		global $datenstring;

		if(is_array($value)){
			$datenstring.=$key. " => ".print_r($value,true)."\n";
		}else{
			$datenstring.=$key. " => ".$value."\n";
		}

		
		//echo $key . " => ";
		//print_r( $value );
		//echo "<br>";
	}

	foreach ( $variableSets as $setName => $variableSet ) {
		if ( isset( $variableSet ) ) {
			//echo "<br><br><hr size='1'>";
			//echo "$setName<br>";
			$datenstring.=$setName."\n";
			array_walk( $variableSet, 'printElementHtml2' );
		}
	}

	$datum=date("Y-m-d H:i:s",time());
	$ip=getenv("REMOTE_ADDR");
	$datenstring="Zeit: $datum\nIP: $ip\nDatei: $PHP_SELF\n".$datenstring."\n--------------------------------------\n";
	$fp234=fopen("cache/logs/".$ums_user_id."_slog.txt", "a");
	fputs($fp234, $datenstring);
	fclose($fp234);  
}
//spezial logging - ende
*/

//ip-test
/*if ($_SESSION['ums_user_ip']!=$_SERVER['REMOTE_ADDR'])
{
  fehlermsg('<br><center><font size="2" color="FF0000">IP-Fehler, deine IP stimmt nicht mit der Sitzungs-IP �berein.<br>Bitte logge dich neu ein:<br><br><a href="index.php">Login</a>');
  session_destroy();
  exit;
}*/

//schaue ob auch die richtige server-id verwendet wird
if ($_SESSION['ums_servid']!=$sv_servid)
{
  fehlermsg('<br><center>
  <font size="2" color="FF0000">'.$session_lang['error2'].'<br><br><font color="00FF00">'.
  $session_lang['error3'].' <a href="'.$sv_link[1].'">'.$session_lang['error4'].'</a><br><br><br><font size="1">'.
  $session_lang['error5'].' <a href="index.php">'.$session_lang['error4'].'</a>');
  session_destroy();
  exit;
}

//session nach maximal einer zeit X durch den botschutz unterbrechen
//globale sessiondatei auslesen
$botfilename='../botcheck/'.$_SESSION["ums_owner_id"].'.txt';
if(file_exists($botfilename)){
	$botfile = fopen ($botfilename, 'r'); 
	$bottime=trim(fgets($botfile, 1000));
	fclose($botfile);
	if($bottime>$_SESSION['ums_session_start']){
		$_SESSION['ums_session_start']=$bottime;
		//$_SESSION['ums_one_way_bot_protection']=0;
	}
}

if(!isset($eftachatbotdefensedisable)){
	$eftachatbotdefensedisable=0;
}

if ((($_SESSION['ums_session_start']+$sv_session_lifetime)<time()) && ($eftachatbotdefensedisable!=1)){
	echo '<html><head>';
	include "cssinclude.php";

	//mitloggen wie oft die botschutzgrafik hintereinander neu geladen wird um scripter zu erkennen
	if(isset($_SESSION['botaccesscounter'])){
		$_SESSION['botaccesscounter']++;
	}else{
		$_SESSION['botaccesscounter']=1;
	}
	

	if($_SESSION['botaccesscounter']>10){
		@mail($GLOBALS['env_admin_email'], $sv_server_tag.'botaccesscounter '.$_SESSION['botaccesscounter'].' user_id '.$ums_user_id, time(), 'FROM: '.$GLOBALS['env_admin_email']);
	}

	//dateiname speichern um sp�ter darauf weiterleiten zu k�nnen
	$_SESSION['ums_bot_protection_filename']=$_SERVER['PHP_SELF'];
	
	//beim ersten erscheinen des Botschutzes die $_GET/$_POST/$_REQUEST-Daten zwischenspeichern
	/*
	if(!isset($_SESSION['save_get']))		{$_SESSION['save_get']=		serialize($_GET);}
	if(!isset($_SESSION['save_post']))		{$_SESSION['save_post']=	serialize($_POST);}
	if(!isset($_SESSION['save_request']))	{$_SESSION['save_request']=	serialize($_REQUEST);}
	 */
	//unset($_SESSION['save_request']);
	if(!isset($_SESSION['save_get']))		{$_SESSION['save_get']=		$_GET;}
	if(!isset($_SESSION['save_post']))		{$_SESSION['save_post']=	$_POST;}
	if(!isset($_SESSION['save_request']))	{$_SESSION['save_request']=	$_REQUEST;}
	
	/*
	echo '<br>C:';
	print_r($_REQUEST);
	echo '<br>D:';
	print_r($_SESSION['save_request']);
	*/
	
	if((isset($thisisefta) && $thisisefta==1) || (isset($thisissou) && $thisissou==1)){//bereich im efta/sou-style ausgeben

		echo '<meta http-equiv="expires" content="0"></head><body><script src="js/'.$sv_server_lang.'_jssammlung.js" type="text/javascript"></script>';
		
		echo '<div align="center">';
		rahmen0_oben();
		echo '<br>';
		rahmen1_oben('<div align="center" style="color: #FF0000;"><b>'.$session_lang['botschutzabfrage'].': '.$session_lang['botschutzinfo'].'</b></div>');

		echo '
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr align="center">
		<td height="25" class="rl">&nbsp;</td>
		<td colspan="4"><div class="cell" style="width: 500px; color: #FF0000;"><a href="'.$_SESSION['ums_bot_protection_filename'].'"><img src="imagegenerator.php?dummy='.time().'" alt="Bild" border="0"></a></td>
		<td class="rr">&nbsp;</td>
		</tr>
		<tr align="center">
		<td height="25" class="rl">&nbsp;</td>
		<td><div style="width: 500px;">';

		for($botschutz_c=1;$botschutz_c<=100;$botschutz_c++){
			echo '<a href="botcheck.php?nummer='.$botschutz_c.'">
			<div style="float:left; width: 44px; 
			border: 2px solid #666666; padding: 0px; margin-top: 3px; margin-left: 1px; margin-right: 1px; font-size: 26px; background-color: #111111; color: #FFFFFF; text-decoration: none; white-space:nowrap;
			">'.$botschutz_c.'</div></a>';	
		}

		echo '</div>

		<td class="rr">&nbsp;</td>
		</tr>
		</table>
		</form>';
		rahmen1_unten();
		echo '<br>';
		rahmen0_unten();
		echo '</div>';

		echo '</body></html>';
	}else{ //bereich im de-style ausgeben
		echo '<meta http-equiv="expires" content="0">
		</head><body><script src="js/'.$sv_server_lang.'_jssammlung.js" type="text/javascript"></script>
		<div align="center">';

		if($GLOBALS['sv_ang']==1){
			echo '
			<script type="text/javascript">
			$( document ).ready(function() {
				$("#iframe_main_container", window.parent.document).css("display", "");
			});
			</script>
			';
		}		
		
		echo'
		<table border="0" cellpadding="0" cellspacing="0" class="cell">
		<tr align="center">
		<td width="13" height="37" class="rol">&nbsp;</td>
		<td colspan="4" align="center" class="ro text2">'.$session_lang['botschutzabfrage'].': '.$session_lang['botschutzinfo'].'</td>
		<td width="13" class="ror">&nbsp;</td>
		</tr>
		<tr align="center">
		<td height="25" class="rl">&nbsp;</td>
		<td colspan="4"><a href="'.$_SESSION['ums_bot_protection_filename'].'"><img src="imagegenerator.php?dummy='.time().'" alt="Bild" border="0"></a></td>
		<td class="rr">&nbsp;</td>
		</tr>
		<tr align="center">
		<td height="25" class="rl">&nbsp;</td>
		<td colSpan="4">
		<div style="width: 500px;">';

		for($botschutz_c=1;$botschutz_c<=100;$botschutz_c++){
			echo '<a href="botcheck.php?nummer='.$botschutz_c.'">
			<div style="float:left; width: 44px; 
			border: 2px solid #666666; padding: 0px; margin-top: 3px; margin-left: 1px; margin-right: 1px; font-size: 26px; background-color: #111111; color: #FFFFFF; text-decoration: none; white-space:nowrap;
			">'.$botschutz_c.'</div></a>';	
		}

		echo '</div>
		</td>
		<td class="rr">&nbsp;</td>
		</tr>
		<tr>
		<td class="rul">&nbsp;</td>
		<td class="ru" colspan="4">&nbsp;</td>
		<td class="rur">&nbsp;</td>
		</tr>
		</table>
		</body></html>';
	}
	exit();
}
/*
elseif(($_SESSION['ums_session_start']+$sv_session_lifetime)<time())
{
  $_SESSION['ums_one_way_bot_protection']=1;
}*/
?>
