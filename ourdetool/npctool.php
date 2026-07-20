<?php
include "../inccon.php";
include "../functions.php";
include "det_userdata.inc.php";

$page_title = 'NPC-Tool';
$active_nav = 'npctool';
include "inc.layout.top.php";

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system");
$row = mysqli_fetch_array($result);
$doetick = $row["doetick"];
$npc_leader_id = $row['npcleader'];

$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$npc_leader_id]);
$row = mysqli_fetch_array($result);
$npc_leader_spielername = $row['spielername'];

echo '<div>NPC-Leader: '.htmlspecialchars((string)$npc_leader_spielername).'</div>';

$aktion = req_int('aktion');
$message = req_str('message');

if ($aktion > 0) {
	csrf_require();
	switch ($aktion) {
		case 1://msg
			//nachricht in der db eintragen
			$text = '/me '.trim($message);

			$channel = 0;
			$channeltyp = 2;
			$spielername = $npc_leader_spielername;
			$chat_message = $text;
			insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);
		break;

		case 2://der npcleader
			$text = '<font color="#9f2ebd">'.trim($message).'</font>';
			$channel = 0;
			$channeltyp = 2;
			$spielername = $npc_leader_spielername;
			$chat_message = $text;
			insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);
		break;

		case 3://SYSTEM: chatnachricht an alle über globalen chat
			$channel = 0;
			$channeltyp = 3;
			$spielername = '[SYSTEM]';
			$chat_message = '<span style="font-weight: bold; color: #ffff00;">'.trim($message).'</span>';
			insert_chat_msg_admin($channel, $channeltyp, $spielername, $chat_message, 0, 'DE');

			postToDiscord(trim($message));

		break;
	}
}

///////////////////////////////////////////////////////////////
//chatnachricht im namen des npc-leaders hinterlegen
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
echo csrf_field();
//aktion im hiddenfeld
echo '<br>Für den NPC-Leader im Chat eine Nachricht hinterlegen:';

echo '<input type="hidden" name="aktion" value="1">';

echo '<br>/msg: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="b1" value="eintragen">';
echo '</form><br>';

///////////////////////////////////////////////////////////////
//der reporter
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
echo csrf_field();
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="2">';

echo htmlspecialchars((string)$npc_leader_spielername).': <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="b1" value="eintragen">';
echo '</form>';

///////////////////////////////////////////////////////////////
//chatnachricht im globalen Chat von [SYSTEM]
///////////////////////////////////////////////////////////////
echo '<form action="npctool.php" method="post">';
echo csrf_field();
//aktion im hiddenfeld
echo '<br>Für [SYSTEM] im Chat eine Nachricht hinterlegen:';

echo '<input type="hidden" name="aktion" value="3">';

echo '<br><input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="submit" name="b1" value="eintragen">';
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

include "inc.layout.bottom.php";
