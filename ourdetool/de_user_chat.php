<?php
include "../inc/sv.inc.php";
include "../functions.php";
include "../inc/env.inc.php";

// Stelle sicher, dass eine Datenbankverbindung vorhanden ist
if (!isset($GLOBALS['dbi'])) {
    $GLOBALS['dbi'] = mysqli_connect(
        $GLOBALS['env_db_dieewigen_host'], 
        $GLOBALS['env_db_dieewigen_user'], 
        $GLOBALS['env_db_dieewigen_password'], 
        $GLOBALS['env_db_dieewigen_database']
    );
}

include "det_userdata.inc.php";

echo '<html><head>';
include "cssinclude.php";
echo '</head><body>';

$uid=(int)$uid;

$chat_sectorcolor='#FFFFFF';
$chat_allycolor='#00FF00';
$chat_allgemeincolor='#4a91fc';

$output='';

if(!isset($_SESSION['de_chat_lastaccess']))$_SESSION['de_chat_lastaccess']=0;

//gültige zeichen
$validchars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789?!+-/<>()[].,;_:"%&@=# ';

//spielerdaten auslesen mit prepared statement
$result = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT sector, chatclear, chatoffallg FROM de_user_data WHERE user_id = ?",
  [$uid]
);

if (!$result || mysqli_num_rows($result) == 0) {
  die('Fehler beim Abrufen der Benutzerdaten');
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$cleartime = 0;
$sector = $row['sector'];
$chatoffallg = 0;

//sql-befehl zusammenbauen
$sql="SELECT * FROM de_chat_msg WHERE ((channel=".$sector." AND channeltyp=0) ";

//allyid herausfinden
$allyid=get_player_allyid($uid);
//sql-befehl für allychat und bündnispartner
if($allyid>0)
{
  //eigene ally
  $sql.=" OR (channel=".$allyid." AND channeltyp=1)";
  //test auf allianzbündnis um deren chat auch mit anzuzeigen
  $result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT * FROM de_ally_partner WHERE ally_id_1 = ? OR ally_id_2 = ?", 
    [$allyid, $allyid]
  );
  $num = mysqli_num_rows($result);

  if($num==1)
  {  
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if($row['ally_id_1']==$allyid)$allyidpartner=$row['ally_id_2'];
    else $allyidpartner=$row['ally_id_1'];
    $sql.=" OR (channel=".$allyidpartner." AND channeltyp=1)";
  }
}

//allgemeiner channel
if($chatoffallg==0)$sql.=' OR channeltyp=2';

$sql.=") AND timestamp > '".$cleartime."' AND timestamp > 0 ORDER BY timestamp ASC";

//$output=$sql;

//daten aus der db holen
$result = mysqli_query($GLOBALS['dbi'], $sql);
//ausgeben
//$first=1;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
  //if($first==1){$first=0;}else echo '<br>';
  // Alternative zu strftime() und IntlDateFormatter
  $timestamp = $row["timestamp"];
  $dateTime = new DateTime("@$timestamp");
  $dateTime->setTimezone(new DateTimeZone('Europe/Berlin'));
  
  $zeit = $dateTime->format('H:i');
  $datum = $dateTime->format('d.m.Y');
  //schauen ob es einen nachricht vom herold ist
  if($row["spielername"]=='^Der Herold^')$row["spielername"]='<font color="#FDFB59">'.$row["spielername"].'</font>';
  //schauen ob es ein emote ist
  if($row["message"][0]=='/' AND $row["message"][1]=='m' AND $row["message"][2]=='e')
  {
    if($row["channeltyp"]==0)$color=$chat_sectorcolor;elseif($row["channeltyp"]==1)$color=$chat_allycolor;elseif($row["channeltyp"]==2)$color=$chat_allgemeincolor;
    //me entfernen
    $row["message"] = str_replace("/me","",$row["message"]);
    $output.='<font color="'.$color.'" title="'.$datum.'">['.$zeit.']</font> <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>';
  }
  else
  {
   if($row["spielername"]!='')$spielername='<i>'.$row["spielername"].': </i>';else $spielername='';
   if($row["channeltyp"]==0)$color=$chat_sectorcolor;elseif($row["channeltyp"]==1)$color=$chat_allycolor;elseif($row["channeltyp"]==2)$color=$chat_allgemeincolor;
     $output.='<font color="'.$color.'" title="'.$datum.'">['.$zeit.']</font> <font color="'.$color.'">'.$spielername.'
     </font><font color="'.$color.'">'.$row["message"].'</font>';
  }
  $output.='<br>';
}

//den output auf sonderzeichen abchecken, die das js-system stören
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

//chat wurde zum letzten mal ausgelesen
//$_SESSION['de_chat_lastaccess']=time();  

echo $output;
?>
</body>
</html>
