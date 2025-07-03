<?php
/*
//anzeige in der logdatei
include "croninfo.inc.php";

///////////////////////////////////////////////////////////////////////////////
//
// das script soll f�r etwas rp bei der ki sorgen und den spielern zuf�llige
// meldungen schicken
//
///////////////////////////////////////////////////////////////////////////////
$directory=str_replace("\\\\","/",$HTTP_SERVER_VARS["SCRIPT_FILENAME"]);
$directory=str_replace("/ki/rptick.php","/",$directory);
if ($directory=='')$directory='../';
$directory="../";

include $directory."inccon.php";
include $directory."inc/sv.inc.php";
include $directory."inc/lang/".$sv_server_lang."_ki_rptick.lang.php";

echo '<html><head></head><body>';

$result = mysql_query("SELECT doetick FROM de_system",$db);
$row = mysql_fetch_array($result);
$doetick=$row["doetick"];

if ($doetick==1)
{
  $rassen_id=5;
  //zuerstmal ne liste der spieler und der der npc laden
  //spielerdaten
  $spielerdaten = mysql_query("select de_user_data.user_id, de_user_data.sector, de_user_data.`system` FROM de_user_data where npc=0",$db);
  $spieleranzahl = mysql_num_rows($spielerdaten);
  //npc-daten
  $npcdaten = mysql_query("select de_user_data.user_id, de_user_data.spielername, de_user_data.sector, de_user_data.`system` FROM de_user_data where npc=1",$db);
  $npcanzahl = mysql_num_rows($npcdaten);

  //schauen ob es spieler gibt
  if($spieleranzahl==0 OR $npcanzahl==0) die('Zuwenig Spieler oder NPCs.');

  //anzahl der spieler bestimmen, die eine message bekommen sollen
  //sagen wir mal 0,01% pro scriptaufruf bei 5 minuten-aufrufen, das w�ren ca. 115 spieler pro tag bei 4000 spielern
  $anz=ceil($spieleranzahl/2000);
  
  for($i=0; $i<$anz; $i++) //$anz-spielern ne message schicken
  {
    $time=strftime("%Y%m%d%H%M%S");
    //den typ der meldung ausw�hlen
    $typ=rand(1,2);
    
    //ziel bestimmen
    $zielid=rand(0,$spieleranzahl-1);
    $zieluid = mysql_result($spielerdaten, $zielid, "user_id");
    $zielsec = mysql_result($spielerdaten, $zielid, "sector");
    $zielsys = mysql_result($spielerdaten, $zielid, "system");
    
    //herkunft bestimmen
    $herkid = rand(0,$npcanzahl-1);
    $herkuid = mysql_result($npcdaten, $herkid, "user_id");
    $herksec = mysql_result($npcdaten, $herkid, "sector");
    $herksys = mysql_result($npcdaten, $herkid, "system");
    $herkspielername = mysql_result($npcdaten, $herkid, "spielername");
    
    

    //sondenmeldungen an die accounts schicken
    if ($typ==1)
    {
      $msgtext=$kirptick_lang[sonde1].$herksec.$kirptick_lang[sonde2];
      mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($zieluid, 4,'$time','$msgtext')",$db);
      mysql_query("update de_user_data set newnews = 1 where user_id = $zieluid",$db);
    }
  
    //agentenmeldungen an die accounts schicken
    elseif ($typ==2)
    {
      $aze=rand(2,10000);//agentenzahl die auffliegt
      $msgtext=$kirptick_lang[agent1].$herkspielername.' ('.$herksec.':'.$herksys.') '.$kirptick_lang[agent2].' '.$aze.$kirptick_lang[agent3];
      mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($zieluid, 5,'$time','$msgtext')",$db);
      mysql_query("update de_user_data set newnews = 1 where user_id = $zieluid",$db);
    }
  }
  echo $anz.' Meldungen versendet.';
} else echo 'Ticks deaktiviert.'; //dorptick
mysql_close($db);
*/
?>
</body>
</html>
