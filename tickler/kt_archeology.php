<?php
/*
//zuerst rundendauer auslesen
$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$row = mysql_fetch_array($db_daten);
$ticks=$row["tick"];    
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 1 - der g�ldene kollektor

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//schauen ob flotten in dem sytem mit dem g�ldenen kollektor sind
//userid des besitzers laden
$result = mysql_query("SELECT a1userid, a1npc FROM de_system",$db);
$row = mysql_fetch_array($result);
$a1userid=$row["a1userid"];
$a1npc=$row["a1npc"];
//nur durchf�hren, wenn der g�ldene bei einem npc ist
if($a1npc==1)
{
  //koordinaten auslesen
  $result = mysql_query("SELECT sector, `system` FROM de_user_data WHERE user_id='$a1userid'",$db);
  $row = mysql_fetch_array($result);
  $sector=$row["sector"];
  $system=$row["system"];

  //flotten laden die in dem moment dort sind
  $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zeit = 1 AND zielsec='$sector' AND zielsys='$system'",$db);
  while ($row = mysql_fetch_array($db_daten))
  {
    //user_id extrahieren
    $hv=explode("-",$row["user_id"]);
    $uid=$hv[0];
    //schauen ob man die voraussetzungen erf�llt
    $db_data = mysql_query("SELECT user_id FROM de_user_quest WHERE user_id='$uid' AND pid=1 AND flag1=0",$db);
    $num = mysql_num_rows($db_data);
    if($num==1)$bewerber[]=$uid;
  }
  //jetzt aus der liste der bewerber per zufall einen rausholen
  if(count($bewerber)>0)
  {
    $zufall=mt_rand(0,count($bewerber)-1);
    $uid=$bewerber[$zufall];
    //den g�ldenen transferieren
    mysql_query("UPDATE de_system SET a1userid='$uid', a1npc=0, a1tick=0",$db);

    //flag setzten, damit man es nicht �fters bekommen kann
    mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid=1",$db);

    //dem spieler eine info schicken
    $time=strftime("%Y%m%d%H%M%S");

    $text=$kt_lang[archeology1].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
    
    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
    mysql_query("UPDATE de_user_data SET newnews = 1 WHERE user_id = $uid",$db);
    //der allianz des spielers ggf. ein allianzartefakt gutschreiben
    $allyid=get_player_allyid($uid);
    if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
  }
}

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 2 - die sondenhalde

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//schauen ob flotten in dem sytem sind
$pid=2;
unset($bewerber);
//userid des besitzers laden
$result = mysql_query("SELECT a2userid FROM de_system",$db);
$row = mysql_fetch_array($result);
$userid=$row["a2userid"];

//koordinaten auslesen
$result = mysql_query("SELECT sector, `system` FROM de_user_data WHERE user_id='$userid'",$db);
$row = mysql_fetch_array($result);
$sector=$row["sector"];
$system=$row["system"];

//flotten laden die in dem moment dort sind
$db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zeit = 1 AND zielsec='$sector' AND zielsys='$system'",$db);
while ($row = mysql_fetch_array($db_daten))
{
  //user_id extrahieren
  $hv=explode("-",$row["user_id"]);
  $uid=$hv[0];
  //schauen ob man die voraussetzungen erf�llt
  $db_data = mysql_query("SELECT user_id FROM de_user_quest WHERE user_id='$uid' AND pid='$pid' AND flag1=0",$db);
  $num = mysql_num_rows($db_data);
  if($num==1)$bewerber[]=$uid;
}

//jetzt aus der liste der bewerber per zufall einen rausholen
if(count($bewerber)>0)
{
  $zufall=mt_rand(0,count($bewerber)-1);
  $uid=$bewerber[$zufall];
  //flag setzten, damit man es nicht �fters bekommen kann
  mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);

  //dem spieler eine info schicken und sonden updaten
  $time=strftime("%Y%m%d%H%M%S");

  $text=$kt_lang[archeology2].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
  mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
  mysql_query("UPDATE de_user_data SET newnews = 1, sonde=sonde+50 WHERE user_id = $uid",$db);

  //neues npc-system w�hlen
  $result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=1 AND sector<>'$sector' AND `system`<>'$system'",$db);
  $anz = mysql_num_rows($result);
  $zufall=mt_rand(0,$anz-1);
  $uid=mysql_result($result, $zufall,0);
  //neue daten in de_system schreiben
  mysql_query("UPDATE de_system SET a2userid='$uid'",$db);
  //der allianz des spielers ggf. ein allianzartefakt gutschreiben
  $allyid=get_player_allyid($uid);
  if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
}

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 3 - die kollektorenhalden

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//schauen ob flotten in dem sytem sind
$pid=3;
unset($bewerber);
//userid des besitzers laden
$result = mysql_query("SELECT a3userid FROM de_system",$db);
$row = mysql_fetch_array($result);
$userid=$row["a3userid"];

//koordinaten auslesen
$result = mysql_query("SELECT sector, `system` FROM de_user_data WHERE user_id='$userid'",$db);
$row = mysql_fetch_array($result);
$sector=$row["sector"];
$system=$row["system"];

//flotten laden die in dem moment dort sind
$db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zeit = 1 AND zielsec='$sector' AND zielsys='$system'",$db);
while ($row = mysql_fetch_array($db_daten))
{
  //user_id extrahieren
  $hv=explode("-",$row["user_id"]);
  $uid=$hv[0];
  //schauen ob man die voraussetzungen erf�llt
  $db_data = mysql_query("SELECT user_id FROM de_user_quest WHERE user_id='$uid' AND pid='$pid' AND flag1=0",$db);
  $num = mysql_num_rows($db_data);
  if($num==1)$bewerber[]=$uid;
}

//jetzt aus der liste der bewerber per zufall einen rausholen
if(count($bewerber)>0)
{
  $zufall=mt_rand(0,count($bewerber)-1);
  $uid=$bewerber[$zufall];
  //flag setzten, damit man es nicht �fters bekommen kann
  mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);

  //dem spieler eine info schicken und sonden updaten
  $time=strftime("%Y%m%d%H%M%S");

  $text=$kt_lang[archeology3].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
  mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
  mysql_query("UPDATE de_user_data SET newnews = 1, col=col+25 WHERE user_id = $uid",$db);

  //neues npc-system w�hlen
  $result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=1 AND sector<>'$sector' AND `system`<>'$system'",$db);
  $anz = mysql_num_rows($result);
  $zufall=mt_rand(0,$anz-1);
  $uid=mysql_result($result, $zufall,0);
  //neue daten in de_system schreiben
  mysql_query("UPDATE de_system SET a3userid='$uid'",$db);
  $allyid=get_player_allyid($uid);
  if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
}


//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 4 - 11 die M-E-Minen, kriegsartefakte, spielerartefakt, Paleniumvortex

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//schauen ob flotten in dem sytem sind
for ($pid=4;$pid<=11;$pid++)
{
  unset($bewerber);
  $bewerber=array ();
  
  $tblfield='a'.$pid.'userid';
  
  //userid des besitzers laden
  $result = mysql_query("SELECT $tblfield AS userid FROM de_system",$db);
  $row = mysql_fetch_array($result);
  $userid=$row["userid"];

  //koordinaten auslesen
  $result = mysql_query("SELECT sector, `system` FROM de_user_data WHERE user_id='$userid'",$db);
  $row = mysql_fetch_array($result);
  $sector=$row["sector"];
  $system=$row["system"];

  //flotten laden die in dem moment dort sind
  $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zeit = 1 AND zielsec='$sector' AND zielsys='$system'",$db);
  while ($row = mysql_fetch_array($db_daten))
  {
    //user_id extrahieren
    $hv=explode("-",$row["user_id"]);
    $uid=$hv[0];
    //schauen ob man die voraussetzungen erf�llt
    $db_data = mysql_query("SELECT user_id FROM de_user_quest WHERE user_id='$uid' AND pid='$pid' AND flag1=0",$db);
    $num = mysql_num_rows($db_data);
    if($num==1)$bewerber[]=$uid;
  }

  //jetzt aus der liste der bewerber per zufall einen rausholen
  if(count($bewerber)>0)
  {
    $zufall=mt_rand(0,count($bewerber)-1);
    $uid=$bewerber[$zufall];

    //dem spieler eine info schicken und den gewinn gutschreiben
    $time=strftime("%Y%m%d%H%M%S");
    
    switch($pid)
    {
      case 4: //m-mine
        $restyp='restyp01';
        $energie=round($ticks*100);
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_1].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		$allyid=get_player_allyid($uid);
  		if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
      break;
      case 5: //d-mine
        $restyp='restyp02';
        $energie=round($ticks*100/2);
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_2].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		$allyid=get_player_allyid($uid);
  		if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
      break;
      case 6: //i-mine
        $restyp='restyp03';
        $energie=round($ticks*100/3);
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_3].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		$allyid=get_player_allyid($uid);
  		if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
      break;
      case 7: //e-mine
        $restyp='restyp04';
        $energie=round($ticks*100/4);
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_4].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		$allyid=get_player_allyid($uid);
  		if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
      break;
      case 8: //t-mine
        $restyp='restyp05';
        $energie=round($ticks/250);
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_5].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		$allyid=get_player_allyid($uid);
  		if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
      break;
      case 9: //kriegsartefatke
        $restyp='kartefakt';
        $energie=5;
        $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$kt_lang[archeologyfound_6].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        mysql_query("UPDATE de_user_data SET newnews = 1, $restyp=$restyp+'$energie' WHERE user_id = $uid",$db);      
        //flag setzten, damit man es nicht �fters bekommen kann
        mysql_query("UPDATE de_user_quest SET flag1=1 WHERE user_id='$uid' AND pid='$pid'",$db);
      break;

      case 10: //spielerartefakt
        //feststellen ob platz im artefaktgeb�ude ist
        //schauen ob man das artefaktgeb�ude hat
		$db_techs=mysql_query("SELECT techs, tick, eftagetlastartefact FROM de_user_data WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_techs);
		$techs=$row["techs"];
		$playertick=$row["tick"];
		$eftagetlastartefact=$row["eftagetlastartefact"];//wird nicht mehr in EFTA gesetzt
        echo 'AAAAAAAAAAAA';
        if ($techs[28]==1)
        {
          echo 'BBBBBBBBBBBBBBBBBB';
          //schauen ob man noch platz im artefaktgeb�ude hat
          if(get_free_artefact_places($uid)>0)
          {
        	echo 'CCCCCCCCCCCCC';
        	if($playertick>$eftagetlastartefact+96)
        	{
        	  echo 'DDDDDDDDDDDDDD';
          	  //artefakt per zufall aussuchen
        	  $ai=mt_rand(1,$ua_index+1);
        	  $energie=1;
        	  //artefakt dem spieler im geb�ude hinterlegen
        	  mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$uid', '$ai', '1')",$db);
        	  $text=$kt_lang[archeologysuccess].': '.number_format($energie, 0,"",".").' '.$ua_name[$ai-1].'-'.$kt_lang[archeologyfound_7].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
        	  mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
        	  mysql_query("UPDATE de_user_data SET newnews = 1 WHERE user_id = $uid",$db);      
        	  //flag setzten, damit man in den errungenschaften sieht, dass man es hat
        	  mysql_query("UPDATE de_user_quest SET flag2=1, anzahl=anzahl+1 WHERE user_id='$uid' AND pid='$pid'",$db);
  			  $allyid=get_player_allyid($uid);
  			  if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
        	}
          }
        }
      break;      
      
      case 11: //paleniumvortex
        //schauen ob man den paleniumverst�rker hat
		$db_techs=mysql_query("SELECT techs, palenium FROM de_user_data WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_techs);
		$techs=$row["techs"];
		$palenium=$row["palenium"];
        echo 'AAAAAAAAAAAA';
        if ($techs[27]==1)
        {
          echo 'BBBBBBBBBBBBBBBBBB';
       	  $energie=$sv_max_palenium;
          //palenium gutschreiben
          mysql_query("UPDATE de_user_data SET newnews = 1, palenium='$energie' WHERE user_id = $uid",$db);      
                  $text=$kt_lang[archeologysuccess].': '.number_format($energie-$palenium, 0,"",".").' '.$kt_lang[archeologyfound_8].'<br>'.$kt_lang[koordinaten].': '.$sector.':'.$system;
          mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);

       	  //flag setzten, damit man in den errungenschaften sieht, dass man es hat
          mysql_query("UPDATE de_user_quest SET flag2=1, anzahl=anzahl+1 WHERE user_id='$uid' AND pid='$pid'",$db);
  		  $allyid=get_player_allyid($uid);
  		  if($allyid>0)mysql_query("UPDATE de_allys SET artefacts=artefacts+1 WHERE id = $allyid",$db);
        }
      break;
            
      default:
      break;
    }

    //neues npc-system w�hlen
    $result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=1 ORDER BY RAND() LIMIT 0,1",$db);
    $row = mysql_fetch_array($result);
    $uid=$row["user_id"];
    //neue daten in de_system schreiben
    mysql_query("UPDATE de_system SET $tblfield='$uid'",$db);
  }
}
*/
?>