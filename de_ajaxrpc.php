<?php
$eftachatbotdefensedisable=1;
mb_internal_encoding("UTF-8");

include "inc/header.inc.php";
include 'functions.php';

$chat_sectorcolor='#FFFFFF';
$chat_allycolor='#00FF00';
$chat_allgemeincolor='#4a91fc';
$chat_globalcolor='#ffad5d';

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// den chat-channel wechseln
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if(isset($_REQUEST['changechatchannel'])){
	$newchannel=intval($_REQUEST['changechatchannel'])-1;

	if($newchannel<0 OR $newchannel>3)$newchannel=0;

	//wenn channel 2 gew�hlt wurde, allgemein, dann testen ob man den aktiv hat
	if($newchannel==2){
		$db_daten=mysql_query("SELECT chatoffallg FROM de_user_data WHERE user_id='$ums_user_id'",$db);
		$row = mysql_fetch_array($db_daten);

		if($row['chatoffallg']==1)$newchannel=$_SESSION["de_chat_inputchannel"];
	}
	
	//wenn channel 3 gewählt wurde, allgemein, dann testen ob man den aktiv hat
	if($newchannel==3){
		$db_daten=mysql_query("SELECT chatoffglobal FROM de_user_data WHERE user_id='$ums_user_id'",$db);
		$row = mysql_fetch_array($db_daten);

		if($row['chatoffglobal']==1)$newchannel=$_SESSION["de_chat_inputchannel"];
	}	

	//wenn channel 1 gew�hlt wurde, allianz, dann test ob man in einer ally ist
	if($newchannel==1)
	{
	  $db_daten=mysql_query("SELECT allytag, status FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	  $row = mysql_fetch_array($db_daten);

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

	//outputlib soll keine [tags] umschreiben
	//$outputlib_dontchangetags=1;
	//include_once('outputlib.php');
  
	$chat_message=trim($_REQUEST['insert']);
	$chat_message=htmlspecialchars($chat_message, ENT_QUOTES, 'UTF-8');

	//Maruh Joke
	$chat_message=str_replace('Maruh', 'Maruh (gepriesen sei der DE-Auserwählte)', $chat_message);
	$chat_message=str_replace('maruh', 'maruh (gepriesen sei der DE-Auserwählte)', $chat_message);

	$chat_message=strip_tags($chat_message);
	
	

	$return=0; //0 alles ok, 1=clear

	$time=time();


	$channeltyp=$_SESSION["de_chat_inputchannel"];

	if($chat_message=='/clear'){
	  //db updaten
	  mysql_query("UPDATE de_user_data SET chatclear='$time' WHERE user_id = '$ums_user_id'",$db);
	  $chat_message='';
	  $return=1;
	}


	//channel bestimmen
	if($channeltyp==0){//sektor
		$db_daten=mysql_query("SELECT sector, chatclear, chatoffallg FROM de_user_data WHERE user_id='$ums_user_id'",$db);
		$row = mysql_fetch_array($db_daten);
		$channel=$row['sector'];
	}elseif($channeltyp==1){//allianz
		$channel=get_player_allyid($ums_user_id);
	}elseif($channeltyp==2){//allgemein
		$channel=0;
	}elseif($channeltyp==3){//global
		$channel=0;
	}
	
	//test auf comsperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime){
		$chat_message='';
	}
	
	if($chat_message!=''){
		insert_chat_msg($channel, $channeltyp, $ums_spielername, $chat_message);
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
	$db_daten=mysql_query("SELECT sector, chatclear, chatoffallg, chatoffglobal FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	$cleartime=$row['chatclear'];
	$sector=$row['sector'];
	$chatoffallg=$row['chatoffallg'];
	$chatoffglobal=$row['chatoffglobal'];

	//sql-befehl zusammenbauen
	//Sektor
	$sql="SELECT * FROM de_chat_msg WHERE ((channel='$sector' AND channeltyp=0) ";

	//Allianz
	//allyid herausfinden
	$allyid=get_player_allyid($ums_user_id);
	//sql-befehl f�r allychat und b�ndnispartner
	if($allyid>0){
		//eigene ally
		$sql.=" OR (channel=$allyid AND channeltyp=1)";
		//test auf allianzb�ndnis um deren chat auch mit anzuzeigen
		$db_daten=mysql_query("SELECT * FROM de_ally_partner WHERE ally_id_1=$allyid OR ally_id_2=$allyid",$db);
		$num = mysql_num_rows($db_daten);

		if($num==1){  
			$row = mysql_fetch_array($db_daten);
			if($row['ally_id_1']==$allyid)$allyidpartner=$row['ally_id_2'];
			else $allyidpartner=$row['ally_id_1'];
			$sql.=" OR (channel=$allyidpartner AND channeltyp=1)";
		}
	}

	//allgemeiner channel
	if($chatoffallg==0){
		//alt
		$sql.=' OR channeltyp=2';
	}

	//$sql.=") AND timestamp > '$cleartime' AND id > '".$_SESSION['de_chat_lastid']."' ORDER BY timestamp ASC";
	$sql.=") AND timestamp > '$cleartime' AND id > '".$chatid."' ORDER BY timestamp ASC";

	//$output=$sql;

	//daten aus der db holen
	$db_daten=mysql_query($sql,$db);
	//ausgeben
	//$first=1;
	while ($row = mysql_fetch_array($db_daten)){
		$row['server_tag']='';
		$chatdata[]=$row;
	}
	
	//globaler channel
	if($chatoffglobal==0){
		//server�bergreifend
		if(isset($_REQUEST['chatidallg'])){
			$sqlallg="SELECT * FROM de_chat_msg WHERE channeltyp=3 AND id > '".$chatidallg."' AND timestamp > '$cleartime' ORDER BY timestamp ASC";
			$db_daten=mysqli_query($GLOBALS['dbi_ls'], $sqlallg);
			//ausgeben
			//$first=1;
			while ($row = mysqli_fetch_array($db_daten)){
				$chatdata[]=$row;
			}
		}
	}else{//nur die Meldungen von [SYSTEM] auslesen
		if(isset($_REQUEST['chatidallg'])){
			$sqlallg="SELECT * FROM de_chat_msg WHERE owner_id=-1 AND channeltyp=3 AND id > '".$chatidallg."' AND timestamp > '$cleartime' ORDER BY timestamp ASC";
			$db_daten=mysqli_query($GLOBALS['dbi_ls'], $sqlallg);
			//ausgeben
			//$first=1;
			while ($row = mysqli_fetch_array($db_daten)){
				$chatdata[]=$row;
			}
		}
	}
	
	//chatdata sortieren und ausgeben
	//solange es Elemente gibt alles immer wieder durchgehen
	$sorted=array();
	while(count($chatdata)>0){
		$index=-1;
		$timestamp=999999999999999999;
		for($i=0;$i<count($chatdata);$i++){
			if($chatdata[$i]['timestamp']<=$timestamp){
				$index=$i;
				$timestamp=$chatdata[$i]['timestamp'];
			}
		}
		
		$sorted[]=$chatdata[$index];
		array_splice($chatdata, $index, 1);
	}

	//$sorted = array_orderby($chatdata, 'timestamp', SORT_ASC);
	/*
	$sorted = array_msort($chatdata, array('timestamp'=>SORT_ASC));
	*/
	
	////////////////////////////////////////////////////////////////
	// Liste der Spieler laden, die man selbst ignoriert
	// gilt f�r: Global, Allgemein, Sektor
	////////////////////////////////////////////////////////////////
	$ignore_self=array();
	//nur die Ignore-Liste laden, wenn es etwas neues im Chat gibt
	if(count($sorted)>0 && $_SESSION['ums_owner_id']!=1){
		$sql="SELECT * FROM de_chat_ignore WHERE owner_id='".$_SESSION['ums_owner_id']."' AND ignore_until>'".time()."';";
		$db_daten=mysqli_query($GLOBALS['dbi_ls'], $sql);
		while($row = mysqli_fetch_array($db_daten)){
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
		$sql="SELECT * FROM de_chat_ignore WHERE owner_id=0;";
		$db_daten=mysqli_query($GLOBALS['dbi_ls'], $sql);
		while($row = mysqli_fetch_array($db_daten)){
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

		if(!empty($row['server_tag'])){
			if($row['id']>$chatidallg)$chatidallg=$row['id'];
		}else{
			if($row['id']>$chatid)$chatid=$row['id'];
		}
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
	global 	$chat_sectorcolor, $chat_allycolor, $chat_allgemeincolor, $chat_globalcolor, $sv_server_tag;

	$output='';

	/*
	$row["message"]=utf8_decode_fix($row["message"]);

	$row["message"]=umlaut($row["message"]);

	//Emoticons
	$row["message"]=str_replace('%u', "\u", $row["message"]);
	$row["message"] = preg_replace_callback('/\\\\u(d[89ab][0-9a-f]{2})\\\\u(d[c-f][0-9a-f]{2})/i', function ($matches) {
		$first = $matches[1];
		$second = $matches[2];
		$value = ((eval("return 0x$first;") & 0x3ff) << 10) | (eval("return 0x$second;") & 0x3ff);
		$value += 0x10000;
		return "&#$value;";
	}, $row["message"]);
	*/

	$zeit=date("H:i", $row["timestamp"]);
	$datum=date("d.m.Y", $row["timestamp"]);

	$row["spielername"]=$row["spielername"];

	//schauen ob es einen nachricht vom herold ist
	if($row["spielername"]=='^Der Herold^'){
		$row["spielername"]='<font color="#FDFB59">'.$row["spielername"].'</font>';
	}
	
	//schauen ob es ein servertag gibt
	if(!empty($row['server_tag'])){
		$server_tag=' '.$row['server_tag'];
	}else{
		$server_tag='';
	}
	
	if($row["channeltyp"]==0){
		$color=$chat_sectorcolor;}
	elseif($row["channeltyp"]==1){
		$color=$chat_allycolor;
	}elseif($row["channeltyp"]==2){
		$color=$chat_allgemeincolor;
	}elseif($row["channeltyp"]==3){
		$color=$chat_globalcolor;
	}

	//$row['message']=umlaut($row['message']);
	
	//schauen ob es ein emote ist
	if($row["message"][0]=='/' AND $row["message"][1]=='m' AND $row["message"][2]=='e'){
		//me entfernen
		$row["message"] = str_replace("/me","",$row["message"]);
		$output.='<font color="'.$color.'" title="'.$datum.'">['.$zeit.']'.$server_tag.'</font> <font color="#FF771D"><a href="details.php?sn='.$row["spielername"].'" target="h" style="color: #FF771D;"><u>'.$row["spielername"].'</u></a> '.$row["message"].'</font>';
	}else{
		if($row["spielername"]!=''){
			if($sv_server_tag==$row['server_tag'] || $row['server_tag']==''){
				$spielername='<a href="details.php?sn='.$row["spielername"].'" target="h" style="color: '.$color.';"><i><u>'.$row["spielername"].'</u></i></a>';
			}else{
				$spielername='<a href="details.php?sn='.$row["spielername"].'&ctyp='.$row['channeltyp'].'&cid='.$row['id'].'" target="h" style="color: '.$color.';"><i><u>'.$row["spielername"].'</u></i></a>';
			}

			if($row['spielername']=='odo'){
				$spielername.='&#x1f37a;';
			}			

		}else{
			$spielername='';
		}
		$output.='<font color="'.$color.'" title="'.$datum.'">['.$zeit.']'.$server_tag.'</font> '.$spielername.'<font color="'.$color.'">: '.$row["message"].'</font>';
	}
	$output.="<br>";
	
	return $output;
}
?>
