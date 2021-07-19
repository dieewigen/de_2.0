<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "../soudata/lib/sou_functions.inc.php";
include "../soudata/lib/sou_dbconnect.php";
include "../soudata/defs/colors.inc.php";

?>
<html>
<head>
<title>EA Tool</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<h1>EA Tools</h1>
<?
include "det_userdata.inc.php";

if($_REQUEST[showchat])
{
  $tbl_chat='sou_chat_msg';

  //daten aus der db laden
  $db_daten=mysql_query("SELECT * FROM $tbl_chat WHERE spielername!='^Der Reporter^' ORDER BY timestamp ASC",$soudb);
  //ausgeben
  $first=1;
  echo '<div align="left">';
  while ($row = mysql_fetch_array($db_daten))
  {
    if($first==1){$first=0;}else echo '<br>';
    if($row["fraction"]>0){$fraction=$row["fraction"];}else {$fraction='?';}
    $zeit=strftime ("%H:%M", $row["timestamp"]);
    $datum=strftime ("%d.%m.%Y", $row["timestamp"]);
    //schauen ob es einen nachricht vom reporter ist
    if($row["spielername"]=='^Der Reporter^')$row["spielername"]='<font color="#FDFB59">'.$row["spielername"].'</font>';
    //schauen ob es ein emote ist
    if($row["message"][0]=='/' AND $row["message"][1]=='m' AND $row["message"][2]=='e')
    {
      //me entfernen
  	  $row["message"] = str_replace("/me","",$row["message"]);
    
  	  if($row["channel"]>0)//fraktionschat
  	  {
  	    $color=$colors_text[$row["channel"]-1];
  	    echo '<font color="'.$color.'" title="'.$datum.'">['.$fraction.']'.$zeit.' <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>';
  	
  	  }
  	  else //allgemeiner chat
  	  {
  	    $color='#FFFFFF';
  	    echo '<font color="'.$color.'" title="'.$datum.'">['.$fraction.']'.$zeit.' <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>';
  	  }
    }
    else
    {
      if($row["channel"]>0)//fraktionschat
      {
        $color=$colors_text[$row["channel"]-1];
        echo '<font color="'.$color.'" title="'.$datum.'">['.$fraction.']'.$zeit.' '.$row["spielername"].': '.$row["message"].'</font>';
      }
      else //allgemeiner chat
      {
        $color='#FFFFFF';
        echo '<font color="'.$color.'" title="'.$datum.'">['.$fraction.']'.$zeit.' '.$row["spielername"].': '.$row["message"].'</font>';
      }
    

    }
  }
  echo '</div>';
}

if($aktion>0)
{
  switch($aktion)  
  {	
    case 1://msg
	  //nachricht in der db eintragen
	  $text='/me '.$_REQUEST["message"];
	  insert_chat_msg('', $text, 0, 0);
      //mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('', '$text', '$time')",$db);
    break;
    case 2://der reporter
	  //nachricht in der db eintragen
	  //$text='<font color="#fc7b3c">'.$_REQUEST["message"].'</font>';
	  $text='<font color="#00FF00">'.$_REQUEST["message"].'</font>';
	  insert_chat_msg('Der Reporter', '<font color="#00ff00">'.$text.'</font>', 0, 0);
      //mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('^Der Reporter^', '$text', '$time')",$db);
    break;
    case 3://artefakt einbauen
    
    //$time=time()+3600*24*2;
    $time=strtotime($_REQUEST["aauctiontime"]);
    //lebenszeit
    if(intval($_REQUEST["alifetime"])==0)$lifetime=0;else $lifetime=time()+(intval($_REQUEST["alifetime"])*3600*24);
    
    if($_REQUEST["oneartefact"]==1)
    {
      mysql_query("INSERT INTO `sou_ship_module` (`fraction` , `name` , `craftedby`, `lifetime`, `needspace` , `hasspace` , `needenergy` , `giveenergy` , `canmine` , `givelife` , `givesubspace` , `givecenter` , `givehyperdrive` , `canbldgupgrade` , `location` , `time` , `price`, `auctioncurrency`, `quality`, `buff`, `mapbuff`) VALUES ('0', '$_REQUEST[aname]', 'ERBAUER', '$lifetime','$_REQUEST[aneedspace]', '$_REQUEST[ahasspace]', '$_REQUEST[aneedenergy]', '$_REQUEST[agiveenergy]', '$_REQUEST[acanmine]', '$_REQUEST[agivelife]', '$_REQUEST[agivesubspace]', '$_REQUEST[agivecenter]', '$_REQUEST[agivehyperdrive]', '$_REQUEST[acanbldgupgrade]', '2', '$time', '1', '$_REQUEST[aauctioncurrency]', '$_REQUEST[aquality]', '$_REQUEST[abuff]', '$_REQUEST[amapbuff]');", $soudb);
      
      echo 'Artefakt hinzugefügt.';
    }
    else
    for($i=1;$i<=6;$i++)
    {
      mysql_query("INSERT INTO `sou_ship_module` (`fraction` , `name` , `craftedby`, `lifetime`, `needspace` , `hasspace` , `needenergy` , `giveenergy` , `canmine` , `givelife` , `givesubspace` , `givecenter` , `givehyperdrive` , `canbldgupgrade`, `location` , `time` , `price`, `auctioncurrency`, `quality`, `buff`, `mapbuff`) VALUES ('$i', '$_REQUEST[aname]', 'ERBAUER','$lifetime','$_REQUEST[aneedspace]', '$_REQUEST[ahasspace]', '$_REQUEST[aneedenergy]', '$_REQUEST[agiveenergy]', '$_REQUEST[acanmine]', '$_REQUEST[agivelife]', '$_REQUEST[agivesubspace]', '$_REQUEST[agivecenter]', '$_REQUEST[agivehyperdrive]', '$_REQUEST[acanbldgupgrade]', '2', '$time', '1', '$_REQUEST[aauctioncurrency]', '$_REQUEST[aquality]', '$_REQUEST[abuff]', '$_REQUEST[amapbuff]');", $soudb);
      echo 'Artefakt hinzugefügt.';
    }
    break;
    case 4://Sabotage
      $spielername=trim($_REQUEST[spielername]);
      $minuten=intval($_REQUEST[minuten]);
      $time=time()+60*$minuten;
      $sql="UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time' WHERE spielername='$spielername'";
      mysql_query($sql,$db);
    break;
  }
}

echo '<hr><a href="soutool.php?showchat=1">Chat</a><hr>';
//msg
echo '<form action="soutool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="1">';

echo '/msg: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form><br><br><br>';

//der reporter
echo '<form action="soutool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="2">';

echo 'Der Reporter: <input type="text" name="message" size="50" value="">&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form>';

//artefakt reinbringen
echo '<hr><form action="soutool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="3">';

echo 'Artefaktname: <input type="text" name="aname" size="50" value=""><br>';
echo 'Laufzeit des Artefaktes in Tagen (0 = unbegrenzt): <input type="text" name="alifetime" size="50" value=""><br>';
echo 'Qualit&auml;t: <input type="text" name="aquality" size="50" value="2"><br>';
echo 'needspace: <input type="text" name="aneedspace" size="50" value=""><br>';
echo 'hasspace: <input type="text" name="ahasspace" size="50" value=""><br>';
echo 'needenergy: <input type="text" name="aneedenergy" size="50" value=""><br>';
echo 'giveenergy: <input type="text" name="agiveenergy" size="50" value=""><br>';
echo 'canmine: <input type="text" name="acanmine" size="50" value=""><br>';
echo 'givelife: <input type="text" name="agivelife" size="50" value=""><br>';
echo 'givesubspace: <input type="text" name="agivesubspace" size="50" value=""><br>';
echo 'givecenter: <input type="text" name="agivecenter" size="50" value=""><br>';
echo 'givehyperdrive: <input type="text" name="agivehyperdrive" size="50" value=""><br>';
echo 'canbldgupgrade: <input type="text" name="acanbldgupgrade" size="50" value=""><br>';
echo 'buff: <input type="text" name="abuff" size="50" value=""><br>';
echo 'mapbuff: <input type="text" name="amapbuff" size="50" value=""><br>';
echo 'auctioncurrency: <input type="text" name="aauctioncurrency" size="50" value=""> (0=Z/1=C)<br>';
$tis=time()+3600*24*2;
$datum=date("Y-m-d H:i:s",$tis);
echo 'Laufzeit der Auktion: <input type="text" name="aauctiontime" size="50" value="'.$datum.'"><br>';
echo '<input type="Checkbox" name="oneartefact" value="1"> globale Auktion<br>';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form>';

echo '<hr><form action="soutool.php" method="post">';
//aktion im hiddenfeld
echo '<input type="hidden" name="aktion" value="4">';

echo 'Sabotiere Spielername: <input type="text" name="spielername" size="50" value=""><br>';
echo 'Sabotiere Minuten: <input type="text" name="minuten" size="50" value="120"><br>';
echo '<input type="Submit" name="b1" value="eintragen">';
echo '</form>';

?>
</div>
</body>
</html>

