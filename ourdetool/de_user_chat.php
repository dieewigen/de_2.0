<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "../functions.php";

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
  $validchars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ß?!+-/<>()[].,;_:"%&@=# ';

  //spielerdaten auslesen
  $db_daten=mysql_query("SELECT sector, chatclear, chatoffallg FROM de_user_data WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);
  $cleartime=0;
  $sector=$row['sector'];
  $chatoffallg=0;
  
  //sql-befehl zusammenbauen
  $sql="SELECT * FROM de_chat_msg WHERE ((channel=$sector AND channeltyp=0) ";
  
  //allyid herausfinden
  $allyid=get_player_allyid($uid);
  //sql-befehl für allychat und bündnispartner
  if($allyid>0)
  {
    //eigene ally
    $sql.=" OR (channel=$allyid AND channeltyp=1)";
    //test auf allianzbündnis um deren chat auch mit anzuzeigen
    $db_daten=mysql_query("SELECT * FROM de_ally_partner WHERE ally_id_1=$allyid OR ally_id_2=$allyid",$db);
    $num = mysql_num_rows($db_daten);

    if($num==1)
    {  
      $row = mysql_fetch_array($db_daten);
      if($row['ally_id_1']==$allyid)$allyidpartner=$row['ally_id_2'];
      else $allyidpartner=$row['ally_id_1'];
      $sql.=" OR (channel=$allyidpartner AND channeltyp=1)";
    }
  }
  
  //allgemeiner channel
  if($chatoffallg==0)$sql.=' OR channeltyp=2';
  
  $sql.=") AND timestamp > '$cleartime' AND timestamp > 0 ORDER BY timestamp ASC";

  //$output=$sql;
  
  //daten aus der db holen
  $db_daten=mysql_query($sql,$db);
  //ausgeben
  //$first=1;
  while ($row = mysql_fetch_array($db_daten))
  {
    //if($first==1){$first=0;}else echo '<br>';
    $zeit=strftime ("%H:%M", $row["timestamp"]);
    $datum=strftime ("%d.%m.%Y", $row["timestamp"]);
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
