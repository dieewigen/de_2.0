<?php
//function get_player_advertised($werberid)
//function get_playername($uid)
//function get_skill($typ)
//function change_skill($typ, $value)
//function get_baonadaskala_different()
//function get_baonadaskala()
//function get_fracleader_id($fraction)
//function get_canmine($uid)
//function get_sum_hold($uid)
//function get_used_hold($uid)
//function get_free_hold($uid)
//function change_hold_amount($uid, $restyp, $amount)
//function has_hold_amount($uid, $res_id)
//function change_hold_amount($owner_id, $uid, $restyp, $amount)
//function get_bldg_level($owner_id, $bldg_id)
//function get_max_frac_bldg_level($bldg_id)
//function has_systemhold_amount($uid, $res_id)
//function change_systemhold_amount($uid, $restyp, $amount)
//function change_money($uid, $amount)
//function has_money($uid)
//function change_darkmatter($uid, $amount)
//function has_baosin($uid)
//function change_baosin($uid, $amount)
//function has_darkmatter($uid)
//function has_credits($uid)
//function change_credits($uid, $amount)
//function make_modul_info($row)
//function make_modul_name($row)
//function make_modul_name_js($row)
//function get_sector_owner($sbx, $sby)
//function insert_chat_msg($spielername, $message, $fraction, $channel)
//function xecho($str)
//function res_is_available($resid)
//function change_specialres($uid, $resid, $amount)
//function specialres_is_available($resid)
//rahmenfunktionen aller art

function has_specialres($uid, $resid)
{
  global $specialres_def, $soudb;
    
  $dbfeld=$specialres_def[$resid][0];
  
  $db_daten=mysql_query("SELECT $dbfeld FROM sou_user_data WHERE user_id='$uid'",$soudb);

  $row = mysql_fetch_array($db_daten);

  return($row[$dbfeld]);
}


function change_specialres($uid, $resid, $amount)
{
  global $specialres_def, $soudb;
    
  $dbfeld=$specialres_def[$resid][0];
  
  mysql_query("UPDATE sou_user_data SET $dbfeld=$dbfeld+$amount WHERE user_id='$uid'",$soudb);
}

function specialres_is_available($resid)
{
  global $srv_def, $player_x, $player_y;
  
  $available=0;
  
  //überprüfen welche rohstoffe hier möglich sind
  for($ri=0;$ri<count($srv_def[$resid]);$ri++)
  {
    if (($player_x*$player_x+$player_y*$player_y) <= ($srv_def[$resid][$ri][2]*$srv_def[$resid][$ri][2])) 
    {
      //echo '<br>'.$resid.' verfügbar';
      $available=1;
    }
    else 
    {
      //echo '<br><br>'.$resid.' nicht verfügbar';
    }
    //echo '<br>player: '.($player_x*$player_x+$player_y*$player_y);
    //echo '<br>radius: '.($rv_def[$resid][$ri][2]*$rv_def[$resid][$ri][2]);
  }
  return($available);
}

function get_player_advertised($werberid)
{
  global $soudb, $ums_premium;
  $num=0;
  if($werberid>0){
	//es zählen alle spieler, die in den letzten x tagen aktiv waren
	$time=time()-3600*24*3;

	$db_daten=mysql_query("SELECT user_id FROM sou_user_data WHERE werberid='$werberid' AND lastclick>'$time' GROUP BY owner_id",$soudb);
	$num = mysql_num_rows($db_daten);
  }
  
  if($ums_premium==1)$num++;
  
  return($num);
}


function get_playername($uid)
{
  global $soudb;

  $db_daten=mysql_query("SELECT spielername, sn_ext1 FROM `sou_user_data` WHERE user_id='$uid'",$soudb);
  $row = mysql_fetch_array($db_daten);

  return($row[spielername].' {'.$row[sn_ext1].'}');
}

function get_skill($typ)
{
  global $soudb, $player_user_id;
  
  $db_daten=mysql_query("SELECT value FROM `sou_user_skill` WHERE user_id='$player_user_id' AND typ='$typ'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    $row = mysql_fetch_array($db_daten);
    $value=$row[value];
  }
  else 
  {
    $value=0;
  }
  
  return($value);
}

function change_skill($typ, $value)
{
  global $soudb, $player_user_id;

  //feststellen ob es schon einen datensatz gibt, falls nicht, diesen anlegen
  $db_daten=mysql_query("SELECT value FROM `sou_user_skill` WHERE user_id='$player_user_id' AND typ='$typ'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num<1)
  {
    mysql_query("INSERT INTO sou_user_skill SET user_id='$player_user_id', typ='$typ', value=0",$soudb);
  }
  
  mysql_query("UPDATE sou_user_skill SET value=value+'$value' WHERE user_id='$player_user_id' AND typ='$typ'",$soudb);
}

function get_baonadaskala_different()
{
  global $player_fraction;
  
  $values=get_baonadaskala();
  //maxwert bestimmen
  $maxwert=0;
  for($i=0;$i<6;$i++)if($values[$i]>$maxwert)$maxwert=$values[$i];
  
  $differenz=$maxwert-$values[$player_fraction-1];
  return($differenz);
}

function get_baonadaskala()
{
  	global $soudb;
  
  	unset($fraktionswerte);
  	$gesamtwert=0;
  	$gesamtcanmine=0;
  	$zeitgrenze=time()-24*3600*7;
  	for($i=1;$i<=6;$i++)
  	{ 
		//aktive spieler und deren ausrüstung für den bonus berechnen
  		$db_daten=mysql_query("SELECT f".$i."canminehasspace AS wert FROM sou_system",$soudb);
  
  		$row = mysql_fetch_array($db_daten);
  		$gesamtcanmine+=$row['wert'];
  		$einzelcanmine[$i]=$row['wert'];
  
  		//sonnensystemwert
	  	$db_daten=mysql_query("SELECT SUM(worth) AS wert FROM `sou_map` WHERE worth>10000000 AND fraction='$i'",$soudb);
  		$row = mysql_fetch_array($db_daten);

	  	$fraktionswerte[$i]+=$row["wert"]/1000000;
  		$gesamtwert+=$row["wert"]/1000000;
  		$einzelwerte[$i][]=$row["wert"]/1000000;
  	}

	for($i=1;$i<=6;$i++)
	{
		$fraktionswerte[$i]+=($einzelwerte[$i][0]*$einzelcanmine[$i]/$gesamtcanmine/1);	
	}
  	
  	$gesamtwert=$fraktionswerte[1]+$fraktionswerte[2]+$fraktionswerte[3]+$fraktionswerte[4]+$fraktionswerte[5]+$fraktionswerte[6];
  	
	$mittelwert=intval($gesamtwert/6);
	$mittelwertcanmine=intval($gesamtcanmine/6);


  	$returnwert= array ($fraktionswerte[1]*100/$mittelwert, $fraktionswerte[2]*100/$mittelwert, $fraktionswerte[3]*100/$mittelwert , $fraktionswerte[4]*100/$mittelwert, $fraktionswerte[5]*100/$mittelwert, $fraktionswerte[6]*100/$mittelwert);
  
  	return($returnwert);
	
}

function get_fracleader_id($fraction)
{
  global $soudb;
  
  //stimmberechtigt sind alle spieler die in den letzten 3 wochen aktiv waren
  $time=time()-21*24*3600;
  $stimmberechtigt = @mysql_result(mysql_query("SELECT count(user_id) FROM sou_user_data WHERE lastclick >='$time' AND fraction = '$fraction'", $soudb),0);
  
  //notwendige stimmen die man braucht um vorsitzender zu werden: x% aller stimmberechtigten stimmen
  $notwendige_stimmen=ceil($stimmberechtigt/100*25);
  if($notwendige_stimmen<10)$notwendige_stimmen=10;
  $abgegebene_stimmen = @mysql_result(mysql_query("SELECT count(sou_user_data.user_id) FROM
  sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id) WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$fraction' AND sou_user_politics.wahlstimme > 0", $soudb),0);
  
  if(!$abgegebene_stimmen) { $abgegebene_stimmen = "0"; }
  
  if($abgegebene_stimmen < $notwendige_stimmen) 
  {
    return(0);
  } 
  else 
  {
    $query_welche_userid = @mysql_query("
      SELECT wahlstimme , count(wahlstimme) as anzahl 
      FROM sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id)
      WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$fraction' 
      GROUP BY wahlstimme 
      ORDER BY anzahl DESC, wahlstimme ASC LIMIT 1
    ", $soudb);
    $data_gewinner = @mysql_fetch_array($query_welche_userid);
    return($data_gewinner[wahlstimme]);
  }
}

function res_is_available($resid)
{
  global $rv_def, $player_x, $player_y;
  
  $available=0;
  
  //überprüfen welche rohstoffe hier möglich sind
  for($ri=0;$ri<count($rv_def[$resid]);$ri++)
  {
    if (($player_x*$player_x+$player_y*$player_y) <= ($rv_def[$resid][$ri][2]*$rv_def[$resid][$ri][2])) 
    {
      //echo '<br>'.$resid.' verfügbar';
      $available=1;
    }
    else 
    {
      //echo '<br><br>'.$resid.' nicht verfügbar';
    }
    //echo '<br>player: '.($player_x*$player_x+$player_y*$player_y);
    //echo '<br>radius: '.($rv_def[$resid][$ri][2]*$rv_def[$resid][$ri][2]);
  }
  return($available);
}

function res_is_availableXY($player_x, $player_y, $resid)
{
  global $rv_def;
  
  $available=0;
  
  //überprüfen welche rohstoffe hier möglich sind
  for($ri=0;$ri<count($rv_def[$resid]);$ri++)
  {
    if (($player_x*$player_x+$player_y*$player_y) <= ($rv_def[$resid][$ri][2]*$rv_def[$resid][$ri][2])) 
    {
      //echo '<br>'.$resid.' verfügbar';
      $available=1;
    }
    else 
    {
      //echo '<br><br>'.$resid.' nicht verfügbar';
    }
    //echo '<br>player: '.($player_x*$player_x+$player_y*$player_y);
    //echo '<br>radius: '.($rv_def[$resid][$ri][2]*$rv_def[$resid][$ri][2]);
  }
  return($available);
	
}

function insert_chat_msg($spielername, $chat_message, $fraction, $channel)
{
  global $soudb;
  $time=time();

  /*
    //counter auslesen
    $db_daten=mysql_query("SELECT MAX(counter) AS counter FROM sou_chat_msg WHERE channel='$channel'",$soudb);
    if(mysql_num_rows($db_daten)==1)
    {
      $row = mysql_fetch_array($db_daten);
      $counter=$row["counter"]+1;
    }
    else $counter=0;
  */
  mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp, fraction, counter, channel) VALUES ('$spielername', '$chat_message', '$time', '$fraction','$counter', '$channel')",$soudb);
  /*
    //für check4new nen file anlegen mit id und timestampt
    $filename="soudata/cache/chat/chan$channel.tmp";
    if(is_file($filename))
    {
      $fp=fopen($filename, "w");
    }
    else 
    {
      $filename="../cache/chat/chan$channel.tmp";    	
      $fp=fopen($filename, "w");
    }
    
    $str=$counter.';'.$time;
    fputs($fp, $str);
    fclose($fp);
    
    //evtl. zuviel vorhandene nachrichten killen
    $counter=$counter-100;
    mysql_query("DELETE FROM sou_chat_msg WHERE counter<'$counter' AND channel='$channel'",$soudb);
	*/
}

function get_sector_owner($sbx, $sby)
{
  global $soudb;

  $brangexa=$sbx*15-7;
  $brangexe=$sbx*15+7;

  $brangeya=$sby*15-7;
  $brangeye=$sby*15+7;
  
  
  $db_daten=mysql_query("SELECT fraction FROM sou_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==0)//der sektor ist leer
  {
  	//nach raumstation schauen
    $db_daten=mysql_query("SELECT fraction FROM sou_map_base WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
    $num = mysql_num_rows($db_daten);
    
    if($num>0)
    {
      $row = mysql_fetch_array($db_daten);
      return($row["fraction"]);
    }
  	else return(0);
  }
  else //es gibt sonnensysteme
  {
  	while($row = mysql_fetch_array($db_daten))
    {
      //alle ss durchgehen und schauen welcher fraktion es gehört
      $fraction=$row["fraction"];
      if($fraction>0)
      {
      	$f[$fraction-1]++;
      }
    }
    //schauen welche fraktion die meisten ss hat, bzw. ob es eine mehrheit gibt
    //zuerst den maxwert suchen
    $max=0;
    for($i=0;$i<=5;$i++)
    {
      if($f[$i]>$max)
      {
        $max=$f[$i];
        $owner=$i+1;
      }
    }
    //maxwert auf einzigartigkeit überprüfen, d.h. wenn größer null
    if($max>0)
    {
      $anzmax=0;
      for($i=0;$i<=5;$i++)
      {
        if($f[$i]==$max)$anzmax++;
      }
      //wenn es nur einen maxwert gibt, dann den zurückgeben
      if($anzmax==1)
      {
        return($owner);
      }
	  else return(0);
    }
    else return(0);
  }
}

function calc_hyperdrive_speed($givehyperdrive)
{
  $time=605-$givehyperdrive*10;
  return($time);
}

function make_modul_name($row)
{
  global $colors_items;
  
  //farben hinterlegen
  if($row["quality"]==0){$f1='';$f2='';}
  elseif($row["quality"]==1){$f1='<font color="#'.$colors_items[1].'">';$f2='</font>';}
  elseif($row["quality"]==2){$f1='<font color="#'.$colors_items[2].'">';$f2='</font>';}
  elseif($row["quality"]==3){$f1='<font color="#'.$colors_items[3].'">';$f2='</font>';}
  elseif($row["quality"]==4){$f1='<font color="#'.$colors_items[4].'">';$f2='</font>';}
  
  return($f1.$row["name"].$f2);
}

function make_modul_name_js($row)
{
  global $colors_items;
  
  //farben hinterlegen
  if($row["quality"]==0){$f1='';$f2='';}
  elseif($row["quality"]==1){$f1='<font color=#'.$colors_items[1].'>';$f2='</font>';}
  elseif($row["quality"]==2){$f1='<font color=#'.$colors_items[2].'>';$f2='</font>';}
  elseif($row["quality"]==3){$f1='<font color=#'.$colors_items[3].'>';$f2='</font>';}
  elseif($row["quality"]==4){$f1='<font color=#'.$colors_items[4].'>';$f2='</font>';}
  
  return($f1.$row["name"].$f2);
}

function make_modul_info($row)
{
  $moduloutput='';
  if($row["needspace"]>0) $moduloutput.='Ben&ouml;tiger Platz: '.number_format($row["needspace"], 0,",",".").' m&sup3;';
  if($row["hasspace"]>0)
  {
  	if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Vorhandener Lagerplatz: '.number_format($row["hasspace"], 0,",",".").' m&sup3;';
  	if($row["hasspaceuom"]>0)$moduloutput.=' (Upgrades: '.$row["hasspaceuom"].')';
  }
  if($row["needenergy"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
    $moduloutput.='Ben&ouml;tigte Energie: '.number_format($row["needenergy"], 0,",",".").' EE';
    if($row["needenergyuom"]>0)$moduloutput.=' (Upgrades: '.$row["needenergyuom"].')';
  }
  if($row["giveenergy"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
    $moduloutput.='Gelieferte Energie: '.number_format($row["giveenergy"], 0,",",".").' EE';
    if($row["giveenergyuom"]>0)$moduloutput.=' (Upgrades: '.$row["giveenergyuom"].')';
  }  
  if($row["canmine"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
    $moduloutput.='F&ouml;rderkapazit&auml;t: '.number_format($row["canmine"], 0,",",".").' m&sup3;/Min bei Standarddichte';
    if($row["canmineuom"]>0)$moduloutput.=' (Upgrades: '.$row["canmineuom"].')';
  }  
  if($row["givehyperdrive"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$speed=calc_hyperdrive_speed($row["givehyperdrive"]);
  	$moduloutput.='&Uuml;berlichtgeschwindigkeit: '.number_format($speed, 0,",",".").' Sek/Lichtjahr';
  }
  if($row["giveresearch"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Forschungskapazit&auml;t: '.number_format($row["giveresearch"], 0,",",".").' FP';
  }
  if($row["giveweapon"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Waffenkapazit&auml;t: '.number_format($row["giveweapon"], 0,",",".").' EE';
  }  
  if($row["giveshield"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Schildkapazit&auml;t: '.number_format($row["giveshield"], 0,",",".").' EE';
  }
  if($row["insurance"]==1)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Dieses Modul ist versichert.';
  }  
  
  if($row["canbldgupgrade"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Dieses Modul kann ein Geb&auml;ude der Stufe '.$row["canbldgupgrade"].' zu Stufe '.($row["canbldgupgrade"]+1).' aufwerten, wobei nat&uuml;rlich die Voraussetzungen f&uuml;r die n&auml;chste Geb&auml;udestufe erf&uuml;llt sein m&uuml;ssen. Bereits eingezahlte Rohstoffe werden verwendet. Das Modul wird bei der Verwendung leider zerst&ouml;rt. Zur Verwendung muß sich das Modul an Bord eines Raumschiffes befinden.';
  }

  if($row["canrecover"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Bergungskapazit&auml;t: '.number_format($row["canrecover"], 0,",",".").' BK';
  }

  if($row["canclone"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Klonkapazit&auml;t: '.number_format($row["canclone"], 0,",",".").' ZQ';
  }
  
  //spielerbuffs
  if($row[buff]!='')
  {
  	//wert parsen
    $value=explode(";",$row[buff]);
    for($n=0;$n<count($value);$n++)
    {
      $einzelvalue=explode("x",$value[$n]);
      if($moduloutput!='' AND $einzelvalue[0]>0)$moduloutput.='<br>';
      
      if($einzelvalue[0]==1)
      {
        $moduloutput.=$einzelvalue[1].'% h&ouml;here F&ouml;rderungskapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }
 	  elseif($einzelvalue[0]==2)
      {
        $moduloutput.='F&ouml;rderungskapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage um '.$einzelvalue[1].' erh&ouml;ht (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }
      elseif($einzelvalue[0]==3)
      {
        $moduloutput.=$einzelvalue[1].'% h&ouml;here Frachtraumkapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }
 	  elseif($einzelvalue[0]==4)
      {
        $moduloutput.='Frachtraumkapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage um '.$einzelvalue[1].' erh&ouml;ht (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }
      elseif($einzelvalue[0]==5)
      {
        $moduloutput.=$einzelvalue[1].'% h&ouml;here Reaktorkapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }
 	  elseif($einzelvalue[0]==6)
      {
        $moduloutput.='Reaktorkapazit&auml;t f&uuml;r '.$einzelvalue[2].' Tage um '.$einzelvalue[1].' erh&ouml;ht (ein bereits bestehender Bonus gleicher H&ouml;he wird um die angegebene Dauer verl&auml;ngert)';
      }      
    }
  }
  
  if($row[mapbuff]!='')
  {
  	//wert parsen
    $value=explode(";",$row[mapbuff]);
    for($n=0;$n<count($value);$n++)
    {
      $einzelvalue=explode("x",$value[$n]);
      if($moduloutput!='' AND $einzelvalue[0]>0)$moduloutput.='<br>';
      
      if($einzelvalue[0]==1)
      {
        $moduloutput.='Feindliche Fraktionen haben in diesem Sonnensystem keinen Einfluss auf das Ansehen. Wirkt nicht bei Sektorraumbasen. Wirkungsdauer '.$einzelvalue[2].' Tage ab Einsatz.';
      }
    }
  }
  
  if($row["craftedby"]!='')
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Hergestellt von '.$row["craftedby"];
  }
  
  if($row["lifetime"]>0)
  {
    if($moduloutput!='')$moduloutput.='<br>';
  	$moduloutput.='Aktiv bis zum '.date("d.m.Y H:i", $row["lifetime"]);
  }
  
  return ($moduloutput);
}

function has_credits($uid)
{
  global $db;
  
  $db_daten=mysql_query("SELECT credits FROM de_user_data WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);

  return($row["credits"]);
  
}

function change_credits($uid, $amount, $reason)
{
  global $db, $sv_sou_in_de;

  //zuerst auslesen wieviel man hat
  $db_daten=mysql_query("SELECT credits FROM de_user_data WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);
  $hascredits=$row["credits"];
  //wert in der db ändern
  $db_daten=mysql_query("UPDATE de_user_data SET credits=credits+'$amount' WHERE user_id='$uid'",$db);
  
  //creditanzahl ändern
  $hascredits=$hascredits+$amount;
  
  //die creditausgabe im billing-logfile hinterlegen
  $datum=date("Y-m-d H:i:s",time());
  $ip=getenv("REMOTE_ADDR");
  $clog="Zeit: $datum\nIP: $ip\n".$reason."- Neuer Creditstand: $hascredits ($amount)\n--------------------------------------\n";
  $fp=fopen("cache/creditlogs/$uid.txt", "a");
  fputs($fp, $clog);
  fclose($fp);
  
  //creditstatistik updaten
  if($sv_sou_in_de==1 AND $uid > 1)
  {
  	$amount=$amount*-1;
  	mysql_query("UPDATE de_system SET creditea=creditea+'$amount'",$db);
  }
}

//geldbestand abfragen
function has_money($uid)
{
  global $soudb;
  
  $db_daten=mysql_query("SELECT money FROM sou_user_data WHERE user_id='$uid'",$soudb);
  $row = mysql_fetch_array($db_daten);
  return($row["money"]);
}

//geldbestand ändern
function change_money($uid, $amount)
{
  global $soudb;
  
  mysql_query("UPDATE sou_user_data SET money=money+'$amount' WHERE user_id='$uid'",$soudb);
	
}

//dunkle materie abfragen
function has_darkmatter($uid)
{
  global $soudb;
  
  $db_daten=mysql_query("SELECT darkmatter FROM sou_user_data WHERE user_id='$uid'",$soudb);
  $row = mysql_fetch_array($db_daten);
  return($row["darkmatter"]);
}

//dunkle materie ändern
function change_darkmatter($uid, $amount)
{
  global $soudb;
  
  mysql_query("UPDATE sou_user_data SET darkmatter=darkmatter+'$amount' WHERE user_id='$uid'",$soudb);
}

//baosin abfragen
function has_baosin($uid)
{
  global $soudb;
  
  $db_daten=mysql_query("SELECT baosin FROM sou_user_data WHERE user_id='$uid'",$soudb);
  $row = mysql_fetch_array($db_daten);
  return($row['baosin']);
}

//baosin ändern
function change_baosin($uid, $amount)
{
  global $soudb;
  
  mysql_query("UPDATE sou_user_data SET baosin=baosin+'$amount' WHERE user_id='$uid'",$soudb);
	
}

function get_bldg_level($owner_id, $bldg_id)
{
  global $soudb;
  
  //überprüfen ob es bereits einen datensatz von dem gebäude gibt
  $db_daten=mysql_query("SELECT level FROM `sou_map_buildings` WHERE owner_id='$owner_id' AND bldg_id='$bldg_id'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==1) //es gibt einen datensatz
  {
  	//datensatz auslesen
  	$row = mysql_fetch_array($db_daten);
  	$return=$row["level"];
  }
  else //es gibt keinen datensatz
  {
  	$return=0;
  }
  return($return);
}

function get_max_frac_bldg_level($bldg_id)
{
  global $soudb, $player_fraction;
  
  //überprüfen ob es bereits einen datensatz von dem gebäude gibt
  $sql="SELECT MAX(sou_map_buildings.level) AS level FROM `sou_map` LEFT JOIN `sou_map_buildings` ON(sou_map.id = sou_map_buildings.owner_id) WHERE sou_map_buildings.bldg_id='$bldg_id' AND sou_map.fraction='$player_fraction'";
  $db_daten=mysql_query($sql,$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==1) //es gibt einen datensatz
  {
  	//datensatz auslesen
  	$row = mysql_fetch_array($db_daten);
  	$return=$row["level"];
  }
  else //es gibt keinen datensatz
  {
  	$return=0;
  }
  return($return);
}

//lagerkomplex bestand feststellen
//function has_systemhold_amount($owner_id, $uid, $res_id)
function has_systemhold_amount($uid, $res_id)
{
  global $soudb;
  
  //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
  //$db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE owner_id='$owner_id' AND user_id='$uid' AND res_id='$res_id'",$soudb);
  $db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$uid' AND res_id='$res_id'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==1) //es gibt einen datensatz
  {
  	//datensatz auslesen
  	$row = mysql_fetch_array($db_daten);
  	$return=$row["amount"];
  }
  else //es gibt keinen datensatz
  {
  	$return=0;
  }
  return($return);
}

//lagerkomplexbestand ändern
//function change_systemhold_amount($owner_id, $uid, $restyp, $amount)
function change_systemhold_amount($uid, $restyp, $amount)
{
  global $soudb;
	
  //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
  //$db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE owner_id='$owner_id' AND user_id='$uid' AND res_id='$restyp'",$soudb);
  $db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$uid' AND res_id='$restyp'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==0) //es gibt noch keinen datensatz
  {
  	//neuen datensatz anelgen
  	//mysql_query("INSERT INTO sou_user_systemhold (owner_id, user_id, res_id, amount) VALUES ('$owner_id','$uid', '$restyp', '$amount')",$soudb);
  	mysql_query("INSERT INTO sou_user_systemhold (user_id, res_id, amount) VALUES ('$uid', '$restyp', '$amount')",$soudb);
  	
  }
  else //es gibt bereits einen datensatz
  {
  	//db updaten
	//mysql_query("UPDATE sou_user_systemhold SET amount=amount+'$amount' WHERE owner_id='$owner_id' AND user_id='$uid' AND res_id='$restyp'",$soudb);
	mysql_query("UPDATE sou_user_systemhold SET amount=amount+'$amount' WHERE user_id='$uid' AND res_id='$restyp'",$soudb);
  }
}

/*
//schiffslagerbestand feststellen
function has_hold_amount($uid, $res_id)
{
  global $soudb;
  
  //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
  $db_daten=mysql_query("SELECT * FROM `sou_ship_hold` WHERE user_id='$uid' AND res_id='$res_id'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==1) //es gibt einen datensatz
  {
  	//datensatz auslesen
  	$row = mysql_fetch_array($db_daten);
  	$return=$row["amount"];
  }
  else //es gibt keinen datensatz
  {
  	$return=0;
  }
  return($return);
}


//schiffs-lagerbestand ändern
function change_hold_amount($uid, $restyp, $amount)
{
  global $soudb;
	
  //überprüfen ob es bereits einen datensatz mit der rohstoff-id gibt
  $db_daten=mysql_query("SELECT * FROM `sou_ship_hold` WHERE user_id='$uid' AND res_id='$restyp'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==0) //es gibt noch keinen datensatz
  {
  	//neuen datensatz anelgen
  	mysql_query("INSERT INTO sou_ship_hold (user_id, res_id, amount) VALUES ('$uid', '$restyp', '$amount')",$soudb);
  	
  }
  else //es gibt bereits einen datensatz
  {
  	//db updaten
  	mysql_query("UPDATE sou_ship_hold SET amount=amount+'$amount' WHERE user_id='$uid' AND res_id='$restyp'",$soudb);
  }
}
*/

//gesamte laderaumkapazität auslesen
function get_sum_hold($uid)
{
  global $soudb;

  $db_daten=mysql_query("SELECT SUM(hasspace) AS hasspace FROM `sou_ship_module` WHERE user_id='$uid' AND location=0",$soudb);
  $row = mysql_fetch_array($db_daten);
  $groundvalue=$row["hasspace"];
  
  //buffs auslesen
  $addvalue=0;
  $time=time();
  $db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$uid' AND time>'$time'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    if($row[typ]==3) $addvalue+=$groundvalue/100*$row[value];
    elseif($row[typ]==4) $addvalue+=$row[value];;
  }
  
  //boni dazurechnen
  $groundvalue+=$addvalue;
  return($groundvalue);
  
  
  
}

/*
//belegten laderaum auslesen
function get_used_hold($uid)
{
  global $soudb;

  $db_daten=mysql_query("SELECT SUM(amount) AS amount FROM `sou_ship_hold` WHERE user_id='$uid'",$soudb);
  $row = mysql_fetch_array($db_daten);
  return($row["amount"]);
}

function get_free_hold($uid)
{
  return(get_sum_hold($uid)-get_used_hold($uid));
}
*/

//überprüfen wie groß die förderkapazität des schiffes ist
function get_canmine($uid)
{
  global $soudb;
  
  //grundmodulertrag
  $db_daten=mysql_query("SELECT SUM(canmine) AS canmine FROM `sou_ship_module` WHERE user_id='$uid' AND location=0",$soudb);
  $row = mysql_fetch_array($db_daten);
  $groundvalue=$row["canmine"];
  
  //buffs auslesen
  $addvalue=0;
  $time=time();
  $db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$uid' AND time>'$time'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    if($row[typ]==1) $addvalue+=$groundvalue/100*$row[value];
    elseif($row[typ]==2) $addvalue+=$row[value];
    elseif($row[typ]==3) $addvalue+=$groundvalue/100*$row[value];//tempfix lagerbuff
  }
  
  //boni dazurechnen
  $groundvalue+=$addvalue;
  return($groundvalue);
}

function rahmen0_oben()
{
  /*echo '<table width="98%" border="0" cellspacing="2" cellpadding="2" style=" border: 1px solid #CEEAF1; background-color: #0F363F;">
        <tr valign="top">
          <td valign="top">';*/
  echo '<div class="rahmen0">';
	
}

function rahmen0_unten()
{
  /*echo '</td>
      </tr>
      </table>';*/
  echo '</div>';
}

function rahmen1_oben($text)
{
  echo '<table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr height="35">
        <td width="7" class="r1ol"></td>
        <td class="r1om">'.$text.'</td>
        <td width="10" class="r1or"></td>
        </tr>
        <tr>
        <td class="r1ml"></td><td align="center">';
}

function rahmen1a_oben($text)
{
  return ('<table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr height="35">
        <td width="7" class="r1ol"></td>
        <td class="r1om">'.$text.'</td>
        <td width="10" class="r1or"></td>
        </tr>
        <tr>
        <td class="r1ml"></td><td align="center">');
}


function rahmen1_unten()
{
  echo '</td><td width="10" class="r1mr"></td>
        </tr>
        <tr height="10">
        <td width="7" class="r1ul"></td>
        <td class="r1um"></td>
        <td width="10" class="r1ur"></td>
        </tr>
        </table>';
}

function rahmen1a_unten()
{
  return('</td><td width="10" class="r1mr"></td>
        </tr>
        <tr height="10">
        <td width="7" class="r1ul"></td>
        <td class="r1um"></td>
        <td width="10" class="r1ur"></td>
        </tr>
        </table>');
}


function rahmen2_oben()
{
  echo '<table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr height="10">
        <td width="7" class="r2ol"></td>
        <td class="r2om"></td>
        <td width="10" class="r2or"></td>
        </tr>
        <tr>
        <td class="r1ml"></td><td align="center">';
}

function rahmen2_unten()
{
  rahmen1_unten();
}

function xecho($str)
{
        global $cachefile;
        echo $str;
        if ($cachefile) fwrite ($cachefile, $str);
}?>