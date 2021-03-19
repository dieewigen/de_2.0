<?php
/*efta funktionen
function cyborg_die($user_id, $typ, $msg);
function enm_load();
function enm_add2player($uid, $row)
function get_player_flag($flag_id);
function set_player_flag($flag_id, $value);
function remove_item($id, $anz)
function add_item($id, $anz)
function get_item_anz($id)
function insert_msg($text, $reload)
function get_enm_level($x,$y)
function wuerfel($wurfanz, $augen)
function modify_player_money($uid, $amount)
function get_player_money()
function make_item_tooltipstring($itemid)
function make_moneystring($money)
function show_efta_resline()
function give_exp($exp)
function sec2min($sec)
function change_credits($uid, $amount, $reason)
function has_credits($uid)
*/

function umlaut($fieldname)
{
  global $soudb;
    $fieldname = str_replace ("ä", "&auml;", $fieldname);
    $fieldname = str_replace ("Ä", "&Auml;", $fieldname);
    $fieldname = str_replace ("ö", "&ouml;", $fieldname);
    $fieldname = str_replace ("Ö", "&Ouml;", $fieldname);
    $fieldname = str_replace ("ü", "&uuml;", $fieldname);
    $fieldname = str_replace ("Ü", "&Uuml;", $fieldname);
    $fieldname = str_replace ("ß", "&szlig;", $fieldname);
    $fieldname = str_replace ("Ã¤", "&auml;", $fieldname);
    $fieldname = str_replace ("Ã„", "&Auml;", $fieldname);
    $fieldname = str_replace ("Ã¶", "&ouml;", $fieldname);
    $fieldname = str_replace ("Ã–", "&Ouml;", $fieldname);
    $fieldname = str_replace ("Ã¼", "&uuml;", $fieldname);
    $fieldname = str_replace ("Ãœ", "&Uuml;", $fieldname);
    $fieldname = str_replace ("ÃŸ", "&szlig;", $fieldname);
    $fieldname = str_replace ("Â³", "&sup3;", $fieldname);
    $fieldname = str_replace ("Â²", "&sup2;", $fieldname);
    return $fieldname;
}

function cyborg_die($user_id, $typ, $msg)
{
  //typ 0=transport nach waldmond
  //typ 2=wiederbelebung bei der nahesten stadt
  
  global $eftadb;

  if($typ==0)//waldmond
  {
	$zielx=0;
	$ziely=0;
	$zielz=0;
  }
  elseif($typ==1)//nächstgelene stadt
  {
  	//koordinaten des spielers auslesen
  	$db_daten=mysql_query("SELECT * FROM de_cyborg_data WHERE user_id='$user_id'",$eftadb);
    $row = mysql_fetch_array($db_daten);
    $map=$row["map"];
    $x=$row["x"];
    $y=$row["y"];
  	
  	//feststellen in welcher stadt man wiederbelebt wird
  	$mindistance=9999999999;
  	$zielx=0;
  	$ziely=0;
  	$zielz=0;
	$resultcity = mysql_query("SELECT * FROM de_cyborg_struct WHERE bldgid=1", $eftadb);
	while($rowcity = mysql_fetch_array($resultcity))
	{
	  //entfernung zu jedem punkt berechnen
      $tx=$rowcity["x"];
      $ty=$rowcity["y"];
      //reisezeit berechnen
      $s1=$x-$tx;
      $s2=$y-$ty;
      if($s1<0)$s1=$s1*(-1);
      if($s2<0)$s2=$s2*(-1);
      $s1=pow($s1,2);
      $s2=pow($s2,2);
      $w1=$s1+$s2;
      $w3=sqrt($w1);
      
      if($w3<$mindistance)
      {
      	$mindistance=$w3;
      	$zielx=$rowcity["x"];
      	$ziely=$rowcity["y"];
      }
	  //echo $rowcity["x"].':'.$rowcity["y"].'-> '.$w3.'<br>';
	}
  }
  
  mysql_query("UPDATE de_cyborg_data SET bewpunkte = bewpunkte - 1000, hp=hpmax, map='$zielz', x='$zielx', y='$ziely', showmsg='$msg' WHERE user_id='$user_id'",$eftadb);
  
  echo '<script>lnk("");</script>';
  exit;
	
}

function enm_load($levelmin, $levelmax)
{
  global $db, $eftadb;
  
  $result = mysql_query("SELECT * FROM de_cyborg_enm_list WHERE level>='$levelmin' AND level<='$levelmax' ORDER BY RAND() LIMIT 0,1",  $eftadb);
  $num = mysql_num_rows($result);
  //echo $num;
  if($num==1)
  {
    //daten auslesen
    $row = mysql_fetch_array($result);
    return ($row);
  }
}

function enm_add2player($uid, $row)
{
  global $db, $eftadb;
	
  $enm_id=$row["id"];
  $enm_level=$row["level"];
  $enm_hpmin=$row["hpmin"];
  $enm_hpmax=$row["hpmax"];
  $enm_attmin=$row["attmin"];
  $enm_attmax=$row["attmax"];
  $enm_armor=$row["armor"];
  $enm_lootid=$row["lootid"];
  $enm_name=$row["name"];
  $enm_questid=intval($row["questid"]);
  $enm_flagid=intval($row["flagid"]);

  //gegner in db packen
  $enm_hpmax=mt_rand($enm_hpmin, $enm_hpmax);
  $enm_hpakt=$enm_hpmax;
  $sql="INSERT INTO de_cyborg_enm (user_id, enm_id, level, name, hpakt, hpmax, attmin, attmax, armor, lootid, questid, flagid)
         VALUES ('$uid','$enm_id', '$enm_level', '$enm_name','$enm_hpakt','$enm_hpmax.','$enm_attmin','$enm_attmax', '$enm_armor','$enm_lootid',
         '$enm_questid','$enm_flagid')";
  mysql_query($sql, $eftadb);
}

function has_credits($uid)
{
  global $db, $eftadb;
  
  $db_daten=mysql_query("SELECT credits FROM de_user_data WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);

  return($row["credits"]);
}

function change_credits($uid, $amount, $reason)
{
  global $db, $eftadb, $sv_efta_in_de;

  //zuerst auslesen wieviel man hat
  $db_daten=mysql_query("SELECT credits FROM de_user_data WHERE user_id='$uid'",$db);
  $row = mysql_fetch_array($db_daten);
  $hascredits=$row["credits"];
  //wert in der db ändern
  $db_daten=mysql_query("UPDATE de_user_data SET credits=credits+'$amount' WHERE user_id='$uid'",$db);

  //die creditausgabe im billing-logfile hinterlegen
  $datum=date("Y-m-d H:i:s",time());
  $ip=getenv("REMOTE_ADDR");
  $clog="Zeit: $datum\nIP: $ip\n".$reason."- Neuer Creditstand: $hascredits ($amount)\n--------------------------------------\n";
  $fp=fopen("cache/creditlogs/$uid.txt", "a");
  fputs($fp, $clog);
  fclose($fp);
  
  //creditstatistik updaten
  if($sv_efta_in_de==1)
  {
  	$amount=$amount*-1;
  	mysql_query("UPDATE de_system SET creditefta=creditefta+'$amount'",$db);
  }

}

function get_player_flag($flag_id)
{
  global $efta_user_id, $eftadb;
  
  //schauen ob es dafür schon ein flag gibt
  $db_daten = mysql_query("SELECT * FROM de_cyborg_flags WHERE user_id = '$efta_user_id' AND flag_id='$flag_id'",$eftadb);
  $num = mysql_num_rows($db_daten);
      
  if ($num==0)//es gibt noch keinen eintrag
  {
    $wert=0;
  }
  else //es gibt schon einen eintrag, also updaten
  {
  	$row = mysql_fetch_array($db_daten);
  	$wert=$row["value"];
  }
  return($wert);
}

function set_player_flag($flag_id, $value)
{
  global $efta_user_id, $eftadb;
  
  //schauen ob es dafür schon ein flag gibt
  $db_daten = mysql_query("SELECT * FROM de_cyborg_flags WHERE user_id = '$efta_user_id' AND flag_id='$flag_id'",$eftadb);
  $num = mysql_num_rows($db_daten);
      
  if ($num==0)//es gibt noch keinen eintrag
  {
    mysql_query("INSERT INTO de_cyborg_flags (user_id, flag_id, value) VALUES ($efta_user_id, $flag_id, $value)",$eftadb);
  }
  else //es gibt schon einen eintrag, also updaten
  {
  	mysql_query("UPDATE de_cyborg_flags SET value='$value' WHERE user_id = '$efta_user_id' AND flag_id='$flag_id'",$eftadb);
  }
}


function give_exp($exp)
{
  global $efta_user_id, $eftadb, $maxplayerlevel, $level;
  
  //nur bis zum maximallevel exp verteilen
  if($maxplayerlevel>$level)
  mysql_query("UPDATE de_cyborg_data set exp=exp+'$exp' WHERE user_id='$efta_user_id'",$eftadb);
}

function remove_item($id, $anz)
{
  global $efta_user_id, $eftadb;

  mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND user_id='$efta_user_id' LIMIT $anz", $eftadb);
}

function add_item($id, $anz)
{
  global $efta_user_id, $eftadb;
  
  $filename='eftadata/items/'.$id.'.item';
  include($filename);
  
  for($i=0;$i<$anz;$i++)
  mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$id', '$item_typ', '1', '$item_durability')",$eftadb);

}

function get_item_anz($id)
{
  global $efta_user_id, $eftadb;
  
  $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$id' AND user_id='$efta_user_id'", $eftadb);
  $num = mysql_num_rows($result);
  return($num);
}

function insert_msg($text, $reload)
{
  global $efta_user_id, $eftadb;
  mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
  if($reload==1)echo '<script>lnk("");</script>';
  exit;
}

function get_enm_level($x,$y)
{
  /*
  //$x=10;$y=10;
  if($x<0)$x=$x*(-1);
  if($y<0)$y=$y*(-1);
  $level=1;
  $r=1;
  while(sqrt($x*$x+$y*$y)>($r*$r*2) AND $level<70)
  {
    $level++;
    $r=$r+1;
  }*/
  $level=ceil(sqrt($x*$x+$y*$y)/10);
  if($level>10)$level=10;
  return($level);
}

function wuerfel($wurfanz, $augen)
{
  for($i=1; $i<=$wurfanz; $i++)
  $wert=$wert+mt_rand(1,$augen);

  return($wert);
}

function modify_player_money($uid, $amount)
{
  global $eftadb;
  mysql_query("UPDATE de_cyborg_item SET amount=amount+'$amount' WHERE equip=0 AND typ=20 AND id=1 AND user_id='$uid'", $eftadb);
}

function get_player_money()
{
  global $efta_user_id, $eftadb;

  $result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
  $row = mysql_fetch_array($result);
  return($row["amount"]);
}

function make_item_tooltipstring($itemid, $durability, $uses)
{
  global $bp, $anzbp, $level;

  //erstmal alles nullen
  $item_armor=0;
  $item_durability=0;
  $item_mindmg=0;
  $item_maxdmg=0;
  $item_level=0;
  $item_desc='';

  //$itemid=$shopangebot[$i];
  $filename='eftadata/items/'.$itemid.'.item';
  include($filename);
  $bp[$anzbp][0]=umlaut($item_name);
  //id speichern
  $bp[$anzbp][1]=$itemid;
  //typ speichern
  $bp[$anzbp][2]=$item_typ;
  //level speichern
  $bp[$anzbp][3]=$item_level;
  //geldwert speichern
  $bp[$anzbp][4]=$item_worth*10;
  //tooltip bauen
  $ttext=umlaut($item_name).'&';
  //wo trägt man es
  if($item_typ==1)$ttext.='Art: Rechte Hand';
  if($item_typ==3)$ttext.='Art: Kopf';
  if($item_typ==5)$ttext.='Art: Brust';
  if($item_typ==11)$ttext.='Art: Beine';
  if($item_typ==12)$ttext.='Art: F&uuml;&szlig;e';
  if($item_typ==7)$ttext.='Art: H&auml;nde';
  if($item_typ==2)$ttext.='Art: Linke Hand';
  if($item_typ==21)$ttext.='Art: Trank';
  if($item_typ==22)$ttext.='Art: Werkzeug';
  if($item_typ==23)$ttext.='Art: Rohstoff';
  if($item_typ==24)$ttext.='Art: Nahrung';
  if($item_typ==25)$ttext.='Art: unbekannt';

  if($durability=='-1')$durability=$item_durability;
  if($item_durability>0)$ttext.='<br>'.$durability.'/'.$item_durability.' Haltbarkeit';
  //aktuelle haltbarkeit abspeichern
  $bp[$anzbp][5]=$durability;
  //speichern ob man es benutzen kann
  $bp[$anzbp][6]=$item_useable;
  //speichern wie oft man es benutzen kann
  //speichern ob man es benutzen kann
  $bp[$anzbp][7]=$uses;  
  //werte aufgrund geringer haltbarkeit verringern
  if($item_durability==0)$item_durability=1;
  $hw=$durability/$item_durability;
  if($hw<0.5)
  {
    $hw=$hw*2;	
  }
  else $hw=1;
  
  if($item_maxdmg>0)$ttext.='<br>'.round($item_mindmg*$hw).'-'.round($item_maxdmg*$hw).' Schaden';
  if($item_armor>0)$ttext.='<br>'.round($item_armor*$hw).' R&uuml;stung';

  if($item_level>0)
  {
    if ($item_level>$level){$col1='<font color=#FF000>';$col2='</font>';}else{$col1='';$col2='';}
    $ttext.='<br>'.$col1.'ab Level '.$item_level.$col2;
  }
  if($item_uses>0)
  {
    $ttext.='<br>benutzbar: '.$uses.'/'.$item_uses;
  }
  
  if($item_desc!='')$ttext.='<br><i>'.umlaut($item_desc).'</i>';
  return($ttext);
}

function make_moneystring($money)
{
  global $gpfad;

  $hasmoney=$money+2000000000;
  $hasmoney=strval($hasmoney);
  $kupfer='';$silber='';$gold='';$platin='';
  $kupfer.=$hasmoney[8];
  $kupfer.=$hasmoney[9];
  $silber.=$hasmoney[6];
  $silber.=$hasmoney[7];
  $gold.=$hasmoney[4];
  $gold.=$hasmoney[5];
  $platin.=$hasmoney[1];
  $platin.=$hasmoney[2];
  $platin.=$hasmoney[3];
  $platin=round($platin);
  $preis='';
  if($platin>0)$preis=$platin.' <img src="'.$gpfad.'co4.gif" title="Platin" border="0" align="top"> ';
  if(round($gold>0) OR $platin>0)$preis.=$gold.' <img src="'.$gpfad.'co3.gif" title="Gold" border="0" align="top"> ';
  if(round($silber>0) OR round($gold>0) OR $platin>0)$preis.=$silber.' <img src="'.$gpfad.'co2.gif" title="Silber" border="0" align="top"> ';
  if(round($kupfer>0) OR round($silber>0) OR round($gold>0) OR $platin>0)$preis.=$kupfer.' <img src="'.$gpfad.'co1.gif" title="Kupfer" border="0" align="top">';

  //schauen ob der wert größer 0 ist
  if($preis=='')$preis='0 <img src="'.$gpfad.'co1.gif" title="Kupfer" border="0" align="top">';

  return($preis);
}

function show_efta_resline()
{
  global $hp, $hpmax, $mp, $mpmax, $exp, $level, $bewpunkte, $x, $y, $map, $restyp05;

  return ('<table width="100%" border="0" cellpadding="0" cellspacing="1">
        <tr align="center">
        <td width="20%" class="cell">Lebensenergie</td>
        <td width="20%" class="cell">Psienergie</td>
        <td width="20%" class="cell">Erfahrung</td>
        <td width="10%" class="cell">Stufe</td>
        <td width="15%" class="cell">Aktionspunkte</td>
        <td width="15" class="cell">Tronic</td>
        </tr>
        <tr align="center">
        <td class="cell1">'.$hp.'/'.$hpmax.'</td>
        <td class="cell1">'.$mp.'/'.$mpmax.'</td>
        <td class="cell1">'.number_format($exp, 0,",",".").'</td>
        <td class="cell1">'.$level.'</td>
        <td class="cell1">'.(floor($bewpunkte)).'</td>
        <td class="cell1">'.number_format($restyp05, 0,",",".").'</td>
        </tr>
        </table>');
}

function rahmen0_oben()
{
  echo '<table width="98%" border="0" cellspacing="2" cellpadding="2" style=" border: 1px solid #CEEAF1; background-color: #0F363F;">
        <tr valign="top">
          <td valign="top">';
	
}

function rahmen0_unten()
{
  echo '</td>
      </tr>
      </table>';
}

function rahmen1_oben($text)
{
  echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr height="35">
        <td width="7" class="r1ol"></td>
        <td class="r1om">'.$text.'</td>
        <td width="10" class="r1or"></td>
        </tr>
        <tr>
        <td class="r1ml"></td><td align="center">';
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

function rahmen2_oben()
{
  echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
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

function linux_cpuload ()
{
  if ($fd = fopen('/proc/loadavg', 'r'))
  {
    $results = split(' ', fgets($fd, 4096));
    fclose($fd);
  }
  else
  {
    $results = array('N.A.', 'N.A.', 'N.A.');
  }
  return $results;
}

function winnt_cpuload ()
{
  $wmi = "";
  $this->wmi = new COM("WinMgmts:\\\\.");

  $objInstance = $this->wmi->InstancesOf("Win32_Processor");

  $cpuload = array();
  while ($obj = $objInstance->Next()) {
  $cpuload[] = $obj->LoadPercentage;
  }
  return $cpuload;
}

function sec2min($sec)
{
  $minuten=floor($sec/60);
  $sekunden=$sec%60;
  if($sekunden<10)$sekunden='0'.$sekunden;
  return $minuten.":".($sekunden);
}
?>
