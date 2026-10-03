<?php
$eftachatbotdefensedisable=1;
mb_internal_encoding("UTF-8");

include "inc/header.inc.php";
include 'functions.php';

//Mindestabstand zwischen zwei Chatnachrichten eines Spielers in Sekunden
$chat_min_interval=1;
//maximale Länge einer Chatnachricht in Zeichen
$chat_max_length=1000;

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// den chat-channel wechseln
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if(isset($_REQUEST['changechatchannel'])){
	$newchannel=intval($_REQUEST['changechatchannel'])-1;

	//ohne gültigen Token bleibt der bisherige Channel
	if(!chat_request_valid()){
		$newchannel=intval($_SESSION["de_chat_inputchannel"] ?? 0);
		$_SESSION['chat_hint']='Der Chat wurde aktualisiert. Bitte lade die Seite neu.';
	}

	if($newchannel<0 OR $newchannel>3)$newchannel=0;

	//wenn channel 2 gew�hlt wurde, allgemein, dann testen ob man den aktiv hat
	if($newchannel==2){
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT chatoffallg FROM de_user_data WHERE user_id=?",
			[$_SESSION['ums_user_id']]);
		$row = mysqli_fetch_assoc($db_daten);

		if($row['chatoffallg']==1)$newchannel=$_SESSION["de_chat_inputchannel"];
	}
	
	//wenn channel 3 gewählt wurde, allgemein, dann testen ob man den aktiv hat
	if($newchannel==3){
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT chatoffglobal FROM de_user_data WHERE user_id=?",
			[$_SESSION['ums_user_id']]);
		$row = mysqli_fetch_assoc($db_daten);

		if($row['chatoffglobal']==1)$newchannel=$_SESSION["de_chat_inputchannel"];
	}	

	//wenn channel 1 gew�hlt wurde, allianz, dann test ob man in einer ally ist
	if($newchannel==1)
	{
	  $db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT allytag, status FROM de_user_data WHERE user_id=?",
		[$_SESSION['ums_user_id']]);
	  $row = mysqli_fetch_assoc($db_daten);

	  if($row['allytag']=='' OR $row['status']==0)$newchannel=$_SESSION["de_chat_inputchannel"];
	}


	$_SESSION["de_chat_inputchannel"]=$newchannel;
	$data[] = array ('newchatchannel' => $newchannel);
	echo json_encode($data);
}

//die PHP-Session aus Performancegründen schließen
//session_write_close();

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// im chat eine nachricht vom spieler hinterlegen
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if(isset($_REQUEST['chatinsert'])){

	$return=0; //0 alles ok, 1=clear, 2=abgelehnt (der Grund erscheint mit der nächsten Chat-Abfrage)
	$insert=$_POST['insert'] ?? '';

	if(!chat_request_valid()){
		//z.B. ein Chatfenster, das schon vor einem Update offen war
		$_SESSION['chat_hint']='Der Chat wurde aktualisiert. Bitte lade die Seite neu, um wieder schreiben zu können.';
		$return=2;
	}elseif(microtime(true)-($_SESSION['chat_last_insert'] ?? 0)<$chat_min_interval){
		$_SESSION['chat_hint']='Bitte nicht so schnell schreiben.';
		$return=2;
	}elseif(is_string($insert)){
		$_SESSION['chat_last_insert']=microtime(true);

		$chat_message=mb_substr(trim($insert), 0, $chat_max_length);
		$chat_message=htmlspecialchars($chat_message, ENT_QUOTES, 'UTF-8');

		//Maruh Joke
		$chat_message=str_replace('Maruh', 'Maruh (gepriesen sei der DE-Auserwählte)', $chat_message);
		$chat_message=str_replace('maruh', 'maruh (gepriesen sei der DE-Auserwählte)', $chat_message);

		$chat_message=strip_tags($chat_message);

		$time=time();

		$channeltyp=$_SESSION["de_chat_inputchannel"];

		if($chat_message=='/clear'){
		  //db updaten
		  mysqli_execute_query($GLOBALS['dbi'],
		    "UPDATE de_user_data SET chatclear=? WHERE user_id = ?",
		    [$time, $_SESSION['ums_user_id']]);
		  $chat_message='';
		  $return=1;
		}

		//channel bestimmen
		if($channeltyp==0){//sektor
			$db_daten=mysqli_execute_query($GLOBALS['dbi'],
			  "SELECT sector, chatclear, chatoffallg FROM de_user_data WHERE user_id=?",
			  [$_SESSION['ums_user_id']]);
			$row = mysqli_fetch_assoc($db_daten);
			$channel=$row['sector'];
		}elseif($channeltyp==1){//allianz
			$channel=get_player_allyid($_SESSION['ums_user_id']);
		}elseif($channeltyp==2){//allgemein
			$channel=0;
		}elseif($channeltyp==3){//global
			$channel=0;
		}

		//test auf comsperre
		$akttime=date("Y-m-d H:i:s",time());
		$db_daten=mysqli_execute_query($GLOBALS['dbi'],
		  "SELECT com_sperre FROM de_login WHERE user_id=?",
		  [$_SESSION['ums_user_id']]);
		$row = mysqli_fetch_assoc($db_daten);
		if($row['com_sperre']>$akttime){
			$chat_message='';
		}

		if($chat_message!=''){
			insert_chat_msg($channel, $channeltyp, $_SESSION['ums_spielername'], $chat_message);
		}
	}

	$data[] = array ('data' => $return);
	echo json_encode($data);
}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// managechat
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if(isset($_REQUEST['managechat']) && $_REQUEST['managechat']){
	
	//sleep(8);
	$output='';

	$chatdata=array();

	$chatid=intval($_REQUEST['chatid']);
	$chatidallg=intval($_REQUEST['chatidallg']);
	//if(!isset($_SESSION['de_chat_lastid']))$_SESSION['de_chat_lastid']=0;

	//gültige zeichen
	$validchars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ß?!+-/<>()[].,;_:"%&@=#* ';

	//spielerdaten auslesen
	$db_daten=mysqli_execute_query($GLOBALS['dbi'],
	  "SELECT sector, chatclear, chatoffallg, chatoffglobal FROM de_user_data WHERE user_id=?",
	  [$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($db_daten);
	$cleartime=$row['chatclear'];
	$sector=$row['sector'];
	$chatoffallg=$row['chatoffallg'];
	$chatoffglobal=$row['chatoffglobal'];

	//sql-befehl zusammenbauen
	//Sektor
	$conds=array('(channel=? AND channeltyp=0)');
	$params=array($sector);

	//Allianz
	//allyid herausfinden
	$allyid=get_player_allyid($_SESSION['ums_user_id']);
	//sql-befehl für allychat und bündnispartner
	if($allyid>0){
		//eigene ally
		$conds[]='(channel=? AND channeltyp=1)';
		$params[]=$allyid;
		//test auf allianzbündnis um deren chat auch mit anzuzeigen
		$db_daten=mysqli_execute_query($GLOBALS['dbi'],
		  "SELECT * FROM de_ally_partner WHERE ally_id_1=? OR ally_id_2=?",
		  [$allyid, $allyid]);
		$num = mysqli_num_rows($db_daten);

		if($num==1){
			$row = mysqli_fetch_assoc($db_daten);
			if($row['ally_id_1']==$allyid)$allyidpartner=$row['ally_id_2'];
			else $allyidpartner=$row['ally_id_1'];
			$conds[]='(channel=? AND channeltyp=1)';
			$params[]=$allyidpartner;
		}
	}

	//allgemeiner channel
	if($chatoffallg==0){
		$conds[]='channeltyp=2';
	}

	$sql='SELECT * FROM de_chat_msg WHERE ('.implode(' OR ', $conds).') AND timestamp > ? AND id > ? ORDER BY timestamp ASC, id ASC';
	$params[]=$cleartime;
	$params[]=$chatid;

	//daten aus der db holen
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], $sql, $params);
	//ausgeben
	//$first=1;
	while ($row = mysqli_fetch_assoc($db_daten)){
		$row['server_tag']='';
		$chatdata[]=$row;
	}
	
	//globaler channel
	if($chatoffglobal==0){
		//server�bergreifend
		if(isset($_REQUEST['chatidallg'])){
			$sqlallg="SELECT * FROM de_chat_msg WHERE channeltyp=3 AND id > ? AND timestamp > ? ORDER BY timestamp ASC, id ASC";
			$db_daten=mysqli_execute_query($GLOBALS['dbi_ls'], $sqlallg, [$chatidallg, $cleartime]);
			//ausgeben
			//$first=1;
			while ($row = mysqli_fetch_assoc($db_daten)){
				$chatdata[]=$row;
			}
		}
	}else{//nur die Meldungen von [SYSTEM] auslesen
		if(isset($_REQUEST['chatidallg'])){
			$sqlallg="SELECT * FROM de_chat_msg WHERE owner_id=-1 AND channeltyp=3 AND id > ? AND timestamp > ? ORDER BY timestamp ASC, id ASC";
			$db_daten=mysqli_execute_query($GLOBALS['dbi_ls'], $sqlallg, [$chatidallg, $cleartime]);
			//ausgeben
			//$first=1;
			while ($row = mysqli_fetch_assoc($db_daten)){
				$chatdata[]=$row;
			}
		}
	}
	
	//chatdata nach Zeit sortieren; usort ist stabil, Nachrichten aus derselben Sekunde behalten ihre Reihenfolge
	usort($chatdata, function($a, $b){
		return (int)$a['timestamp'] <=> (int)$b['timestamp'];
	});
	$sorted=$chatdata;
	
	////////////////////////////////////////////////////////////////
	// Liste der Spieler laden, die man selbst ignoriert
	// gilt f�r: Global, Allgemein, Sektor
	////////////////////////////////////////////////////////////////
	$ignore_self=array();
	//nur die Ignore-Liste laden, wenn es etwas neues im Chat gibt
	if(count($sorted)>0 && $_SESSION['ums_owner_id']!=1){
		$sql="SELECT * FROM de_chat_ignore WHERE owner_id=? AND ignore_until>?";
		$db_daten=mysqli_execute_query($GLOBALS['dbi_ls'], $sql, [$_SESSION['ums_owner_id'], time()]);
		while($row = mysqli_fetch_assoc($db_daten)){
			//sich selbst kann man nicht ignorieren
			if($row['owner_id_ignore']!=$_SESSION['ums_owner_id'] && $row['owner_id_ignore']!=1){
				$ignore_self[]=$row['owner_id_ignore'];
			}
		}
	}
	
	////////////////////////////////////////////////////////////////
	// Liste der Spieler laden, die wegen zuvielen "Ignores" ignoriert 
	// werden
	// gilt für: Global, Allgemein
	////////////////////////////////////////////////////////////////	
	$ignore_global=array();
	if(count($sorted)>0 && $_SESSION['ums_owner_id']!=1){
		$sql="SELECT * FROM de_chat_ignore WHERE owner_id=0";
		$db_daten=mysqli_execute_query($GLOBALS['dbi_ls'], $sql);
		while($row = mysqli_fetch_assoc($db_daten)){
			if($row['owner_id_ignore']!=$_SESSION['ums_owner_id'] && $row['owner_id_ignore']!=1){
				$ignore_global[]=$row['owner_id_ignore'];
			}
		}
	}	
	
	////////////////////////////////////////////////////////////////	
	// Chat ausgeben
	////////////////////////////////////////////////////////////////	
	for($i=0;$i<count($sorted);$i++){
		$row=$sorted[$i];
		
		//je nach Channel kommen verschiede Filter zur Auswahl
		if($row['channeltyp']==0){//Sektor
			if(!in_array($row['owner_id'], $ignore_self)){
				$output.=format_chat_output($row);
			}
		}elseif($row['channeltyp']==1){//Allianz
			$output.=format_chat_output($row);
		}elseif($row['channeltyp']==2 || $row['channeltyp']==3){//Server && Global
			if(!in_array($row['owner_id'], $ignore_self) && !in_array($row['owner_id'], $ignore_global)){
				$output.=format_chat_output($row);
			}
		}

		//globale Nachrichten liegen in der DB der Accountverwaltung und haben eigene IDs
		if($row['channeltyp']==3){
			if($row['id']>$chatidallg)$chatidallg=$row['id'];
		}else{
			if($row['id']>$chatid)$chatid=$row['id'];
		}
	}

	//Hinweis aus chatinsert/changechatchannel, z.B. zu schnell geschrieben
	if(!empty($_SESSION['chat_hint'])){
		$output.='<div class="chatline chat-error">'.htmlspecialchars($_SESSION['chat_hint'], ENT_QUOTES, 'UTF-8').'</div>';
		unset($_SESSION['chat_hint']);
	}



	//den output auf sonderzeichen abchecken, die das js-system stören
	/*
	$output=umlaut($output);
	$ws='';
	for($i=0;$i<strlen($output);$i++)
	{
	  if(strpos($validchars, $output[$i])===FALSE)
	  {}
	  else 
	  {
		$ws.=$output[$i];
	  }
	}
	$output=$ws;
	*/
	
	//echo $output;
	//die();

	//ggf. die Daten vom Infocenter dranhängen
	$infocenter='';
	if(isset($_SESSION['new_desktop_version']) && $_SESSION['new_desktop_version']==1){
		if(!isset($_SESSION['ic_last_refresh'])){
			$_SESSION['ic_last_refresh']=0;
		}

		if($_SESSION['ic_last_refresh']+20<time()){
			$infocenter.=getInfocenter();

			$_SESSION['ic_last_refresh']=time();
		}
	}

	$data[] = array ('output' => $output, 'chatid' => $chatid, 'chatidallg' => $chatidallg, 'infocenter' => $infocenter);
	echo json_encode($data);
	//print_r($data);
}

function format_chat_output($row){
	global $sv_server_tag;

	$zeit=date("H:i", $row["timestamp"]);
	$datum=date("d.m.Y", $row["timestamp"]);

	//schauen ob es ein servertag gibt
	if(!empty($row['server_tag'])){
		$server_tag=' '.$row['server_tag'];
	}else{
		$server_tag='';
	}

	//Link zum Spieler; der Name ist HTML-escaped gespeichert
	$link='details.php?sn='.rawurlencode(html_entity_decode($row["spielername"], ENT_QUOTES, 'UTF-8'));
	//Spieler anderer Server werden über die Chat-ID gefunden (Ignore-Liste)
	if($row['server_tag']!='' && $row['server_tag']!=$sv_server_tag){
		$link.='&ctyp='.intval($row['channeltyp']).'&cid='.intval($row['id']);
	}
	$link=htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

	$spielername=$row["spielername"];
	//schauen ob es einen nachricht vom herold ist
	if($spielername=='^Der Herold^'){
		$spielername='<span class="chat-herold">'.$spielername.'</span>';
	}

	//die Farbe kommt über die Klasse des Channels (gp/de-chat.scss)
	$output='<div class="chatline chat-ch'.intval($row["channeltyp"]).'"><span title="'.$datum.'">['.$zeit.']'.$server_tag.'</span> ';

	//schauen ob es ein emote ist
	if(substr($row["message"], 0, 3)==='/me'){
		$output.='<span class="chat-emote"><a href="'.$link.'" target="h"><u>'.$spielername.'</u></a> '.substr($row["message"], 3).'</span>';
	}else{
		if($row["spielername"]!=''){
			$output.='<a href="'.$link.'" target="h"><i><u>'.$spielername.'</u></i></a>';

			if($row['spielername']=='odo'){
				$output.='&#x1f37a;';
			}
		}
		$output.=': '.$row["message"];
	}
	$output.='</div>';

	return $output;
}

//Chat-Aktionen nur per POST mit dem Token aus chat.php, damit fremde Seiten/Links nichts im Namen des Spielers auslösen
function chat_request_valid(){
	return $_SERVER['REQUEST_METHOD']==='POST'
		&& !empty($_SESSION['chat_token'])
		&& is_string($_POST['token'] ?? null)
		&& hash_equals($_SESSION['chat_token'], $_POST['token']);
}
?>
