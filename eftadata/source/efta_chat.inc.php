<?php
//fix um den chat von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;
include "inc/header.inc.php";
include "eftadata/source/efta_functions.php";
include "eftadata/lib/efta_dbconnect.php";

$refreshtime=5000;

//chck4new - id der gr��ten nachricht ausgeben
if ($_REQUEST["check4new"]==1){
	$db_daten=mysql_query("SELECT MAX(id) AS id FROM de_cyborg_chat_msg LIMIT 1",$eftadb);
	if(mysql_num_rows($db_daten)==1)
	{
	  $row = mysql_fetch_array($db_daten);
	  $id=$row["id"];
	}
	else $id=0;

	$data[] = array ('id' => $id);
	echo json_encode($data);  
	die();
  //die($id);
}

//msg in der db eintragen
//test auf comsperre
$akttime=date("Y-m-d H:i:s",time());
$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
if($row['com_sperre']>$akttime)$_REQUEST["chat_message"]='';

if ($_REQUEST["insert"]==1 AND $_REQUEST["chat_message"]!='' AND $_SESSION["efta_spielername"]!='')
{
  //filter laden
  include('outputlib.php');

  //fix f�r sonderzeichen
  $_REQUEST["chat_message"]=str_replace("&#43;", "+", $_REQUEST["chat_message"]);
  
  //nachricht filtern
  $_REQUEST["chat_message"] = trim($_REQUEST["chat_message"]);
  $chat_message = $_REQUEST["chat_message"];
  $chat_message = str_replace("<","&lt;",$chat_message);
  $chat_message = str_replace(">","&gt;",$chat_message);
  $chat_message = str_replace("\\","/",$chat_message);

  //nachricht in der db ablegen
  $time=time();
  $chat_spielername=$_SESSION["efta_spielername"].' {'.$sv_server_tag.'}';
  mysql_query("INSERT INTO de_cyborg_chat_msg (spielername, message, timestamp) VALUES ('$chat_spielername', '$chat_message', '$time')",$eftadb);
  //evtl. zuviel vorhandene nachrichten killen
  $id=mysql_insert_id()-100;
  mysql_query("DELETE FROM de_cyborg_chat_msg WHERE id<'$id'",$eftadb);
  //timestamp setzen, damit man nen schnelleren refresh bekommt
  $_SESSION["ums_efta_chat_timestamp"]=time();
}

//include "functions.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Die Ewigen - Efta</title>
<?php if ($_REQUEST["input"]==1 OR $_REQUEST["oben"]==1){$eftacss=1;include "cssinclude.php";}?>
<?
echo '<script type="text/javascript">';
echo 'self.offscreenBuffering = true;';
echo '</script>';

if ($_REQUEST["input"]==1)
{
?>
<script type="text/javascript">
 <!--
  function empty_field_and_submit()
  {
   var loctarget=document.f.chat_message.value.replace("+", "&#43;");
   loctarget="efta_chat.php?insert=1&chat_message="+escape(loctarget);
   document.f.chat_message.value='';
   document.f.chat_message.focus();
   parent.chat_mitte.location.href = loctarget;   
   return false;
  }
 // -->
</script>
<?php
}
elseif($_REQUEST["frame"]==1)
{
  //erzeuge den chatbereich
  /*echo '
  <frameset framespacing="0" border="0" frameborder="0" rows="10,*,18">
    <frame name="chat_oben" src="efta_chat.php?oben=1" scrolling="no" marginwidth=0 marginheight=0 noresize>
    <frame name="chat_mitte" src="efta_chat.php" scrolling="auto" marginwidth=0 marginheight=0 noresize>
    <frame name="chat_unten" src="efta_chat.php?input=1" scrolling="no" marginwidth=0 marginheight=0 noresize>
  </frameset>';*/
  echo '
  <frameset framespacing="0" border="0" frameborder="0" rows="*,18">
    <frame name="chat_mitte" src="efta_chat.php" scrolling="auto" marginwidth=0 marginheight=0 noresize>
    <frame name="chat_unten" src="efta_chat.php?input=1" scrolling="no" marginwidth=0 marginheight=0 noresize>
  </frameset>';  
  
  exit;
}
elseif($_REQUEST["oben"]==1)//stelle den titel dar
{
  //echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  //echo '<tr><td align="center"><b class="ueber">&nbsp;Chat&nbsp;</b></td></tr>';
  //echo '</table>';
echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr height="10">
        <td width="7" class="r2ol"></td>
        <td class="r2om"></td>
        <td width="10" class="r2or"></td>
        </tr></table>';  
 exit;
}
else//metarefresh
{
  //refreshwert berechnen
  if($_SESSION["ums_efta_chat_timestamp"]+300>time())$refreshtime=5000; else $refreshtime=60000;
  //echo '<meta http-equiv="refresh" content="'.$time.';URL=efta_chat.php">';
  echo '<META HTTP-EQUIV="Cache-Control" content="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="Sat, 06 Apr 2002 08:58:45 GMT">';
  //runterscrollen
  echo '<script type="text/javascript">
 <!--
  function gd()
  {
    self.scrollTo(0, 100000);
  }
 // -->
</script>';
  //style
  $eftacss=1;
  /*
  echo '<style type="text/css">
  body {scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
 scrollbar-arrow-color: #8CA0B4; padding:0px; color: #FFFFFF; margin-left:0px; margin-top:0px; margin-right:0px; margin-bottom:0px;
 font-family: helvetica, arial,geneva, sans-serif;  font-size:10px;}
  </style>';*/
  include "cssinclude.php";
}
?>
</head>
<?php
if ($_REQUEST["input"]==1 OR $_REQUEST["frame"]==1 OR $_REQUEST["oben"]==1)echo '<body bgcolor="#000000">';
else
echo '<body bgcolor="#000000" onLoad="gd()">';
//inputbereich ausgeben
if ($_REQUEST["input"]==1)
{
  //bereich definieren
  echo '<form action="efta_chat.php" method="GET" target="chat_mitte" name="f" OnSubmit="return empty_field_and_submit()">';
  echo '
  <input type="hidden" name="insert" value="1">

  <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  <td><input class="chatinput" type="text" name="chat_message" maxlength="400" value="" style="width:100%;"></td>
  <td width="10"><input type="Submit" name="send" value="Senden"></td></tr></table>';
  echo '</form>';
  echo '</body></html>';
  
  //script beenden
  exit;
}
//chat anzeigen

//rahmen0_oben();

echo '<div class="chatmain">';
//daten aus der db laden
$db_daten=mysql_query("SELECT id, spielername, message, timestamp FROM de_cyborg_chat_msg ORDER BY timestamp ASC",$eftadb);
//ausgeben
$first=1;
while ($row = mysql_fetch_array($db_daten))
{
  if($first==1){$first=0;}else echo '<br>';
  $zeit=strftime ("%H:%M", $row["timestamp"]);
  $datum=strftime ("%d.%m.%Y", $row["timestamp"]);
  //schauen ob es einen nachricht vom herold ist
  if($row["spielername"]=='^Der Herold^')$row["spielername"]='<font color="#FDFB59">'.$row["spielername"].'</font>';
  //schauen ob es ein emote ist
  if($row["message"][0]=='/' AND $row["message"][1]=='m' AND $row["message"][2]=='e')
  {
    //me entfernen
    $row["message"] = str_replace("/me","",$row["message"]);
    echo '<font color="#00FF00" title="'.$datum.'">['.$zeit.']</font> <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>';
  }
  else
  echo '<font color="#00FF00" title="'.$datum.'">['.$zeit.']</font> '.utf8_encode($row["spielername"]).': '.utf8_encode($row["message"]);
  //die gr��te id speichern
  $maxid=$row["id"];
}

//einen div f�r ne meldung definieren
echo '<div id="chatmeldung"></div></div>';

//rahmen0_unten();

//ajax-�berpr�fung im hintergrund auf neuen eintrag
echo "
<script type=\"text/javascript\" language=\"javascript\">
var http_request = false;
var url = 'efta_chat.php';

var worker = new Worker('efta_chat.js');

worker.addEventListener('message', function(e) {
    if (parseInt(e.data)>=0){
		if(".$maxid."<e.data){
			location.href='efta_chat.php';
		}
    }else{
      document.getElementById('chatmeldung').innerHTML = '<font color=\'#FF0000\'>Auf den Chat konnte nicht zugegriffen werden. �berpr�fe bitte ob Du richtig eingeloggt bist.';
      self.scrollTo(0, 100000);
    }
	//alert('Worker said: '+e.data);
}, false);


function check4new(){
	worker.postMessage('check4new'); // Send data to our worker.
	setTimeout(\"check4new()\", ".$refreshtime.");
}

setTimeout(\"check4new()\", ".$refreshtime.");
</script>";

echo '</font>';
?>
</body>
</html>
