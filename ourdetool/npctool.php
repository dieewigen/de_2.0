<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>T</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
mb_internal_encoding("iso-8859-1");

include "det_userdata.inc.php";
include '../inc/env.inc.php';
include '../functions.php';
include "../soudata/lib/sou_dbconnect.php";

$result = mysql_query("SELECT * FROM de_system",$db);
$row = mysql_fetch_array($result);
$doetick=$row["doetick"];
$npc_leader_id=$row['npcleader'];

$result = mysql_query("SELECT * FROM de_user_data WHERE user_id='$npc_leader_id'",$db);
$row = mysql_fetch_array($result);
$npc_leader_spielername=$row['spielername'];

$aktion=intval($_REQUEST['aktion']);

if($aktion>0){
	switch($aktion){	
		case 1://msg
			//nachricht in der db eintragen
			$text='/me '.umlaut(utf8_encode(trim($_REQUEST["message"])));

			//insert_chat_msg('', $text, 0, 0);
			$channel=0;$channeltyp=2;$spielername=$npc_leader_spielername; $chat_message=$text;
			insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);

			//mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('', '$text', '$time')",$db);
		break;
		
		case 2://der npcleader
			//nachricht in der db eintragen
			//$text='<font color="#fc7b3c">'.$_REQUEST["message"].'</font>';
			$text='<font color="#9f2ebd">'.umlaut(utf8_encode(trim($_REQUEST["message"]))).'</font>';
			$channel=0;$channeltyp=2;$spielername=$npc_leader_spielername; $chat_message=$text;
			insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);	  
			//insert_chat_msg('Der Reporter', '<font color="#00ff00">'.$text.'</font>', 0, 0);
			//mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('^Der Reporter^', '$text', '$time')",$db);
		break;

		case 3://SYSTEM: chatnachricht an alle über globalen chat
			$channel=0;$channeltyp=3;$spielername='[SYSTEM]'; $chat_message='<span style="font-weight: bold; color: #ffff00;">'.umlaut(utf8_encode(trim($_REQUEST["message"]))).'</span>';
			insert_chat_msg_admin($channel, $channeltyp, $spielername, $chat_message, -1, 'DE');

			postToDiscord($_REQUEST['message']);

		break;
	}
}

///////////////////////////////////////////////////////////////
//chatnachricht im namen des npc-leaders hinterlegen
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
//aktion im hiddenfeld
echo 'F&uuml; den NPC-Leader im Chat eine Nachricht hinterlegen:';

echo '<input type="hidden" name="aktion" value="1">';

echo '<br>/msg: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form><br>';

///////////////////////////////////////////////////////////////
//der reporter
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="2">';

echo $npc_leader_spielername.': <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form>';

///////////////////////////////////////////////////////////////
//chatnachricht im globalen Chat von [SYSTEM]
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
//aktion im hiddenfeld
echo 'F&uuml;r [SYSTEM] im Chat eine Nachricht hinterlegen:';

echo '<input type="hidden" name="aktion" value="3">';

echo '<br><input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form><br>';

function postToDiscord($message){
    $data = array("content" => $message, "username" => "Der Reporter");
	$curl = curl_init($GLOBALS['webhooks']['der_reporter']);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);	
	
	
	if(curl_exec($curl) === false)
	{
		echo 'Curl-Fehler: ' . curl_error($curl);
	}
	else
	{
		echo 'Operation ohne Fehler vollständig ausgeführt';
	}

	//Kopie in global
	$data = array("content" => $message, "username" => "Der Reporter");
	$curl = curl_init($GLOBALS['webhooks']['global']);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);	
	
	
	if(curl_exec($curl) === false)
	{
		echo 'Curl-Fehler: ' . curl_error($curl);
	}
	else
	{
		echo 'Operation ohne Fehler vollständig ausgeführt';
	}

}

?>
</body>
</html>

