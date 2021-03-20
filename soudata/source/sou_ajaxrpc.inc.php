<?php
$eftachatbotdefensedisable=1;

include "soudata/lib/sou_functions.inc.php";
include "inc/header.inc.php";
include "soudata/defs/colors.inc.php";
include "soudata/lib/sou_dbconnect.php";
include 'soudata/defs/startpositionen.inc.php';
include "soudata/defs/boni.inc.php";
include "soudata/defs/resources.inc.php";
include "soudata/lib/transaction.lib.php";
mt_srand((double)microtime()*10000);

$date_format='d.m.Y - H:i';

//grafikpfad optimieren
$gpfad=$sv_image_server_list[0].'s/';

if(!isset($ums_user_id) OR $ums_user_id<1) die('no session');

  $db_daten=mysql_query("SELECT * FROM sou_user_data WHERE user_id='$_SESSION[sou_user_id]'",$soudb);  	
  $row = mysql_fetch_array($db_daten);
  $player_user_id=$_SESSION[sou_user_id];
  $_SESSION["sou_spielername"]=$row["spielername"].' {'.$row["sn_ext1"].'}';
  $player_name=$row["spielername"];
  $player_age=$row[playerage];
  $_SESSION["sou_fraction"]=$row["fraction"];
  $player_fraction=$row["fraction"];
  $_SESSION["sou_shipname"]=$row["shipname"];
  $player_ship_name=$row["shipname"];
  $player_x=$row["x"];
  $player_y=$row["y"];
  //$player_z=$row["z"];
  $player_rhx=$row["rhx"];
  $player_rhy=$row["rhy"];
  $player_rhuse=$row["rhuse"];
  $player_money=$row["money"];
  $player_darkmatter=$row['darkmatter'];
  $player_baosin=$row['baosin'];
  $player_destroy=$row["destroy"];
  $player_destroyed=$row["destroyed"];
  $player_ship_diameter=$row["shipdiameter"];
  $player_ship_material=$row["shipmaterial"];
  $player_shipnotready=$row["shipnotready"];
  $player_donate=$row["donate"];
  $player_donatelastday=$row["donatelastday"];
  $player_lastclick=$row["lastclick"];
  $player_in_hb=$row["inhb"];
  $player_atimer1typ=$row["atimer1typ"];//laufende aktion
  $player_atimer1time=$row["atimer1time"];
  $player_atimer2typ=$row["atimer2typ"];//laufende forschung
  $player_atimer2flag=$row["atimer2flag"];
  $player_atimer2time=$row["atimer2time"];
  $player_atimer3time=$row["atimer3time"];
  
  $player_specialres[1]=$row[specialres1];
  $player_specialres[2]=$row[specialres2];
  $player_specialres[3]=$row[specialres3];
  $player_specialres[4]=$row[specialres4];
  $player_specialres[5]=$row[specialres5];
  $player_specialres[6]=$row[specialres6];
  $player_specialres[7]=$row[specialres7];
  $player_specialres[8]=$row[specialres8];
  $player_specialres[9]=$row[specialres9];
  $player_specialres[10]=$row[specialres10];
  $player_werberid=$row[werberid];
  $player_dailygift=$row[dailygift];

  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  infocenter  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

include_once 'sou_ajax_infocenter.inc.php';

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  infocenter  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

include_once 'sou_ajax_quests.inc.php';

  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  die daten f�r infobar1 zur�ckliefern  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['timeboost']==1){
	//�berpr�fen ob etwas l�uft
	if($player_atimer1time>time()){
		//transaktionsbeginn
		if (setLock($_SESSION["sou_user_id"]))
		{
		  //�berpr�fen ob es der richtige typ ist. geht nur bei folgenden typen
		  if($player_atimer1typ>=1 AND $player_atimer1typ<=5 AND $player_atimer1typ!=2 AND $player_atimer1typ!=4)
		  {
			//�berpr�fen wieviel credits es kosten wird
			$needcredits=ceil(($player_atimer1time-time())/60/5);

			//�berpr�fen ob man genug credits hat
			$has_credits=has_credits($ums_user_id);
			if($has_credits>=$needcredits)
			{
			  //timer auf 0 setzen
			  mysql_query("UPDATE sou_user_data SET atimer1typ='0', atimer1time='0' WHERE user_id='$player_user_id'",$soudb);
			  $player_atimer1time=0;
			  $player_atimer1typ=0;

			  //msg ausgeben 'Die Aktion wurde abgeschlossen. Creditkosten: '.
			  $output=$needcredits;

			  //credits abziehen
			  change_credits($ums_user_id, $needcredits*(-1), 'EA-Aktionsbeschleunigung');

			  //den kauf im logfile hinterlegen
			  //@mail($GLOBALS['env_admin_email'], 'EA Beschleunigung: '.$needcredits.' - '.$_SESSION["sou_spielername"].' - Fraktion '.$player_fraction, '');
			  if($ums_user_id>1)
			  {
				$datum=date("Y-m-d H:i:s",time());
				$clog="EA Beschleunigung ($player_atimer1typ): ".$needcredits." - ".$_SESSION[sou_spielername]." - Fraktion ".$player_fraction." - $datum\n";
				$fp=fopen("soudata/cron/logs/creditbuy.txt", "a");
				fputs($fp, $clog);
				fclose($fp);
			  }
			}
			else $output=-2;//nicht genug credits
		  }
		  //lock wieder entfernen
		  $erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
		  if ($erg)
		  {
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		  }
		  else
		  {
			//print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
		  }
		}//lock ende
		//else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
	}
	else 
	{
		//es gib keine aktive aktion
		$output=-1;
	}

	$data[] = array ('output' => $output);
	echo json_encode($data);
}  
  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  die daten f�r infobar1 zur�ckliefern  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['loadinfobar1']==1)
{
  $credits=has_credits($ums_user_id);
  $output='<span style="position: absolute; color: #111111; margin-top: 3px; left: 3px;">
  <a href="index.php?logout=1"><img src="'.$gpfad.'abutton3.gif" alt="Logout" title="Logout" border="0"></a>
  <b>'
  
  .$sv_server_tag.' - F'.$player_fraction.' - '.umlaut($player_name).' ('.$player_x.':'.$player_y.')'
  .'&nbsp;<span id="agedesc" title="Charakteralter (max. 120)&Gibt an, wie weit Dein K&ouml;rper gealtert ist.<br><br>Die Alterung verl&auml;uft nicht nur linear, sondern kann auch von besonderen Faktoren beschleunigt, verlangsamt, oder wie Legenden behaupten, komplett gestoppt werden.<br><br>Beim Erreichen des Maximalalters wird im Klonmodul ein neuer K&ouml;rper erzeugt. Je besser das Klonmodul ist, desto weniger Sch&auml;den treten dabei auf."><img src="'.$gpfad.'dns.png" alt="Alter" width="16px" height="16px">'.$player_age.'</span>'  
  .'&nbsp;<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits"> '.number_format($credits, 0,"",".")
  .'&nbsp;<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari"> '.number_format($player_money, 0,"",".")
  .'&nbsp;<img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin"> '.number_format($player_baosin, 0,"",".")
  .'&nbsp;<img src="'.$gpfad.'a27.gif" alt="Dunkle Materie (cm&sup3;)" title="Dunkle Materie (cm&sup3;)"> '.number_format($player_darkmatter, 0,"",".")
  .'</b></span>';

  if($player_atimer1time>time()+1)
  {
    //sekunden in stunden/minuten/sekunden umrechnen
    $zeit=$player_atimer1time-time();
	$canboost=0;

	if($player_atimer1typ==1)//mining
    {
      $text.='Du suchst im Asteroidenfeld nach Rohstoffen.';
      //creditbeschleuniger
      $text.='<br>F&uuml;r 1 Credit pro verbleibenden 5 Minuten kann die Aktion sofort abgeschlossen werden. Klicken zur Beschleunigung.';
      $canboost=1;      
    }
    elseif($player_atimer1typ==2)//hyperraumflug
    { 
		$text.='Das Raumschiff befindet sich gerade im Hyperraumflug.<br>Zielkoordinaten: '.$player_x.':'.$player_y;
		$text.='<br>Die Reisezeit berechnet sich aus Beschleunigungsphase, &Uuml;berlichtflug und Verz&ouml;gerungsphase. F&uuml;r die Beschleunigung und Verz&ouml;gerung werden jeweils 120 Sekunden ben&ouml;tigt.';
		$canboost=0;
	}
    elseif($player_atimer1typ==3)//br�cke der erbauer
    { 
      $text.='Das Raumschiff befindet sich gerade im Hyperraumflug zwischen 2 Stationen einer Br&uuml;cke der ERBAUER.<br>Zielkoordinaten: '.$player_x.':'.$player_y;
	  $canboost=1;
    }
    elseif($player_atimer1typ==4)//hyperraumtunnel
    { 
      $text.='Das Raumschiff durchfliegt aktuell einen Hyperraumtunnel.';
	  $canboost=0;
    }
    elseif($player_atimer1typ==5)//schiffsreparatur
    { 
      $text.='Das Raumschiff wird aktuell repariert.';
      //creditbeschleuniger
      $text.='<br>F&uuml;r 1 Credit pro verbleibenden 5 Minuten kann die Aktion sofort abgeschlossen werden. Klicken zur Beschleunigung.';
      $canboost=1;
    }
    elseif($player_atimer1typ==6)//ansehen im sonnensystem erringen
    { 
      $text.='Das Raumschiff sammelt Rohstoffe und spendet diese um das Ansehen deiner Fraktion zu erh&ouml;hen.';
    }
    else $text='Was machst Du da nur?';

    if($canboost==1)
    {
      $styleext='cursor: pointer;';
      $javascript='onClick="if(confirm(\'Diese Aktion kostet Credits. Durchf&uuml;hren?\')){timeboost();}"';
    }
    else 
    {
      $styleext='';
      $javascript='';
    }
    $output.='<span id="ibcounterbox" title="Laufende Aktion&'.$text.'" style="'.$styleext.'position: absolute; margin-top: 3px; right: 40px;" '.$javascript.'>
    <img src="'.$gpfad.'clock.png"> <span id="ibcounter" style="color: #ba5002; font-weight:bold;"></span></span>';
      
  }
  if($sv_sou_in_de==1)$output.='<span style="position: absolute; margin-top: 3px; right: 3px; cursor: pointer;" onClick="btde();"><img src="'.$gpfad.'backtode.png" alt="zur&uuml;ck zu DE" title="zur&uuml;ck zu DE"></span>';    
  
  $data[] = array ('output' => $output, 'zeit' => $zeit);
  echo json_encode($data);
}


//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  die daten f�r die karte zur�ckliefern  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['loadmapdata'])
{
  $sbx=round($_REQUEST['xpos']/15);
  $sby=round($_REQUEST['ypos']/15);

  //zuerst die sektoren �berpr�fen, ob sie erforscht worden sind
  unset($knownflag, $knownx, $knowny, $knowncost, $systemx, $systemy, $systemname, $systemfraction, $systempic, $systemhb, $systemua, 
  $srb_pic, $srb_fraction, $srb_x, $srb_y, $srb_special, $find_x, $find_y, $quest_x, $quest_y, $fraction_money);
  
  //kosten f�r die sektorerforschung
  $ekosten = array (244400,306802,359255,417052,600925,688646,785306,1070179,1211014,1366201,1793402,2013231,2577670,2882713,3621196,4037873,4996676,5558812,6796051,7546840,9135420,10974045,12161167,14505352,17204583,19037192,22460312,26385887,30880233,36017965,41882995,48569662,56183973,64845010,74686495, 85858527,98529544,117069547,133759663,157718804,179566008,210410382,245680212,278430892,323593544,375070444,433678404,500335139,588328184,675555314);
  
  //fraktionskasse auslesen
  $db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
  $row = mysql_fetch_array($db_daten);
  $feldname='f'.$player_fraction.'money';
  $fraction_money=$row[$feldname];
  
  
  for($y=$sby+3;$y>=$sby-3;$y--)
  {
	for($x=$sbx-7;$x<=$sbx+7;$x++)
	{
	  $searchx=$x*15;
	  $searchy=$y*15;
	  $fraction=$player_fraction;
	  
      //berechnen wie teuer die expedition ist
	  //entfernung zum n�chsten nullpunkt berechnen
      $s1=$sv_sou_galcenter[0][0]-$searchx;
      $s2=$sv_sou_galcenter[0][1]-$searchy;
      if($s1<0)$s1=$s1*(-1);
      if($s2<0)$s2=$s2*(-1);
      $s1=pow($s1,2);
      $s2=pow($s2,2);
      $w1=$s1+$s2;
      $entfernung=sqrt($w1);
    
      //die kosten in abh�ngigkeit zur entfernung berechnen
      $teiler=$sv_sou_galcenter[0][2]/50;
    
      $kostenstelle=round($entfernung/$teiler);
      if($kostenstelle>49)$kostenstelle=49;
      $kostenstelle=49-$kostenstelle;
    
      $gesamtkosten=round($ekosten[$kostenstelle]/2);
	    
	  $knowncost[]=$gesamtkosten;	  
	  
      $db_daten=mysql_query("SELECT fraction FROM sou_map_known WHERE x='$searchx' AND y='$searchy' AND fraction='$fraction'",$soudb);
	  $num = mysql_num_rows($db_daten);
	  $knownx[]=$searchx;
	  $knowny[]=$searchy;
	  if($num==1)//bekannt
	  {
	    //da bekannt noch �berpr�fen wem der sektor geh�rt
	    $knownflag[]=get_sector_owner($x, $y);
	    
	    //die sonnensysteme auslesen
		$brangexa=$x*15-7;
		$brangexe=$x*15+7;
		$brangeya=$y*15-7;
		$brangeye=$y*15+7;
  		$db_daten=mysql_query("SELECT * FROM sou_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
  		while($row = mysql_fetch_array($db_daten))
  		{
    	  $systemx[]=$row["x"];
    	  $systemy[]=$row["y"];
    	  $systempic[]=$row["pic"];
    	  $systemname[]=$row["sysname"];
    	  $systemfraction[]=$row["fraction"];
    	  if($row['underattack']>time()-3600*24*2)$systemua[]=1;else $systemua[]=0;

    	  //auslesen ob es einen hyperraumaufrissprojektor gibt
  		  if($row["fraction"]==$player_fraction)$systemhb[]=get_bldg_level($row['id'], 13);
  		  else $systemhb[]=0;
  		
  		}
  		
  		//sektorraumbasis auslesen
		$db_daten=mysql_query("SELECT * FROM sou_map_base WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
  		$anzahl_srb = mysql_num_rows($db_daten);
		if($anzahl_srb>0)
  		{
    	  while($row = mysql_fetch_array($db_daten))
    	  {
    	  $srb_pic[]=$row["pic"];
    	  $srb_special[]=$row["special"];
  		  $srb_fraction[]=$row["fraction"];
  		  $srb_x[]=$row["x"];
  		  $srb_y[]=$row["y"];
    	  }
        }  		

        //fundstellen auslesen
		$db_daten=mysql_query("SELECT * FROM sou_map_find WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
		while($row = mysql_fetch_array($db_daten))
		{
  		  $find_x[]=$row["x"];
  		  $find_y[]=$row["y"];
        }

        //quests
        $db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE (fraction='$player_fraction' OR fraction=0) AND done=0 AND x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye'",$soudb);
        while($row = mysql_fetch_array($db_daten))
	  	{
  		  $quest_x[]=$row["x"];
  		  $quest_y[]=$row["y"];
        }	    
	  }
	  else $knownflag[]=-1; 	
	
	}
  }
  
  $data[] = array ('knownflag' => $knownflag, 'knownx' => $knownx, 'knowny' => $knowny, 'knowncost' => $knowncost, 'systemx' => $systemx, 
  'systemy' => $systemy, 'systemname' => $systemname, 'systemfraction' => $systemfraction, 'systempic' => $systempic, 'systemhb' => $systemhb,
  'systemua' => $systemua, 'srbpic' => $srb_pic, 
  'srbfraction' => $srb_fraction, 'srbx' => $srb_x, 'srby' => $srb_y, 'srbspecial' =>  $srb_special,'findx' => $find_x, 'findy' => $find_y,
  'questx' => $quest_x, 'questy' => $quest_y, 'fractionmoney' => $fraction_money);
  echo json_encode($data);

}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// daten f�r showdata sonnensystem�bersicht
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['showdataload'])
{
  //sonnensystem-id
  $id=intval($_REQUEST['id']);
  
  $output='';
  
  //daten auslesen
  $db_daten=mysql_query("SELECT * FROM sou_map WHERE id='$id'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  if($row['fraction']==$player_fraction)
  {
    $output.='Rohstoffvorkommen: ';
    $resstring='';
	for($i=0;$i<count($r_def);$i++)
	{
  	  //�berpr�fen ob der rohstoff m�glich ist
 
  	  if(res_is_availableXY($row['x'], $row['y'], $i)==1)
  	  {
  	    if($resstring!='')$resstring.=', ';
  	    $resstring.=$r_def[$i][0];
  	  }
	}
	$output.=$resstring;
    
  }
  else $output.='Deine Fraktion hat keinen Anspruch auf dieses Sonnensystem.'; 
  
  $data[] = array ('output' => $output);
  echo json_encode($data);
}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// den chat-channel wechseln
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['changechatchannel'])
{
  if($_SESSION["sou_chat_inputchannel"]>0)$_SESSION["sou_chat_inputchannel"]=0;
  elseif($_SESSION["sou_fraction"]>0)$_SESSION["sou_chat_inputchannel"]=$_SESSION["sou_fraction"];
    

  $data[] = array ('newchatchannel' => $_SESSION["sou_chat_inputchannel"]);
  echo json_encode($data);
}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// im chat eine nachricht vom spieler hinterlegen
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if($_REQUEST['chatinsert']){
	session_write_close();

	$insert=trim($_REQUEST['insert']);
	$insert = umlaut($insert);  

	$time=time();

	$chat_message=$insert;
	$chan_frac=$_SESSION["sou_fraction"];

	$channel=$_SESSION["sou_chat_inputchannel"];
  
	//test auf comsperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime)$chat_message='';
  
	if($chat_message!='' AND $player_name!='')insert_chat_msg($_SESSION[sou_spielername], $chat_message, $chan_frac, $channel);

	$data[] = array ('data' => 0);
	echo json_encode($data);
}


//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// manage chat
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//Im Laufe des Tages wird vermutlich ein neues Chatsystem online gestellt. W�hrend der Wartungsarbeiten steht der Chat nicht zur Verf�gung und es k�nnen Fehlermeldungen erscheinen. Es wird notwendig sein, das Spiel komplett neu zu laden um auf den neuen Chat zugreifen zu k�nnen.
if($_REQUEST['managechat']){
	//die PHP-Session aus Performancegr�nden schlie�en
	session_write_close();
	$chatid=intval($_REQUEST['chatid']);
	
	$output[0]='';
	$output[1]='';

	$tbl_chat='sou_chat_msg';

	$chan_global=0;
	$chan_frac=$_SESSION["sou_fraction"];

	//nur etwas machen, wenn die lastid gr��er ist als beim letzten aufruf, sonst werden zuviele daten �bertragen
	$db_daten=mysql_query("SELECT MAX(id) AS wert FROM $tbl_chat WHERE (channel='$chan_global' OR channel='$chan_frac') ORDER BY timestamp DESC",$soudb);
	$row = mysql_fetch_array($db_daten);
	$maxid=$row['wert'];

	if($maxid > $chatid){


	  $maxlines=100;
	  $lines[0]=0;
	  $lines[1]=0;

	  $chat_sectorcolor='#FFFFFF';
	  $chat_allycolor='#4a91fc';


	  $validchars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789�?!+-/<>()[].,;_:"%&@=#* ';

	  //$db_daten=mysql_query("SELECT * FROM $tbl_chat WHERE (channel='$chan_global' OR channel='$chan_frac') AND id>'$lastid' ORDER BY timestamp ASC",$soudb);
	  $db_daten=mysql_query("SELECT * FROM $tbl_chat WHERE (channel='$chan_global' OR channel='$chan_frac') AND id>'".$chatid."'ORDER BY timestamp DESC",$soudb);
	  $num = mysql_num_rows($db_daten);

	  $chatid=$maxid;
	  while($row = mysql_fetch_array($db_daten))
	  {
		if($row["fraction"]>0){$fraction=$row["fraction"];}else {$fraction='?';}
		$zeit=strftime ("%H:%M", $row["timestamp"]);
		$datum=strftime ("%d.%m.%Y", $row["timestamp"]);

		//schauen ob es einen nachricht vom reporter ist
		if($row["spielername"]=='^Der Reporter^')
		{
		  $row["spielername"]='<font color="#FDFB59">'.$row["spielername"].'</font>';
		  $outputid=0;
		  $lines[$outputid]++;
		}
		else
		{
		  $outputid=1;
		  $lines[$outputid]++;
		};

		//zeilenumbruch
		//if($first==1){$first=0;}else $output[$outputid].='<br>';
		//schauen ob es ein emote ist
		if($row["message"][0]=='/' AND $row["message"][1]=='m' AND $row["message"][2]=='e')
		{
		  //me entfernen
		  $row["message"] = str_replace("/me","",$row["message"]);

		  if($row["channel"]>0)//fraktionschat
		  {
			$color=$chat_allycolor;
			if($lines[$outputid]<=$maxlines)$output[$outputid]='<br><font color="'.$color.'"><span title="'.$datum.'">'.$zeit.'</span> <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>'.$output[$outputid];
		  }
		  else //allgemeiner chat
		  {
			$color=$chat_sectorcolor;
			if($lines[$outputid]<=$maxlines)$output[$outputid]='<br><font color="'.$color.'"><span title="'.$datum.'">'.$zeit.'</span> ['.$fraction.'] <font color="#FF771D">'.$row["spielername"].' '.$row["message"].'</font>'.$output[$outputid];
		  }
		}
		else
		{
		  if($row["channel"]>0)//fraktionschat
		  {
			$color=$chat_allycolor;
			if($lines[$outputid]<=$maxlines)$output[$outputid]='<br><font color="'.$color.'"><span title="'.$datum.'">'.$zeit.'</span> '.$row["spielername"].': '.$row["message"].'</font>'.$output[$outputid];
		  }
		  else //allgemeiner chat
		  {
			$color=$chat_sectorcolor;
			//ist es ein logeintrag f�r geb�ude/forschungen/usw?
			if($outputid==0)
			{
			  if($lines[$outputid]<=$maxlines)$output[$outputid]='<br><font color="'.$color.'"><span title="'.$datum.'">'.$zeit.'</span> '.$row["message"].'</font>'.$output[$outputid];
			}
			else 
			{
			  if($lines[$outputid]<=$maxlines)$output[$outputid]='<br><font color="'.$color.'"><span title="'.$datum.'">'.$zeit.'</span> ['.$fraction.'] '.$row["spielername"].': '.$row["message"].'</font>'.$output[$outputid];
			}
		  }
		}
	  }

	  //den output auf sonderzeichen abchecken, die das js-system st�ren
	  $output[0]=umlaut($output[0]);
	  $output[1]=umlaut($output[1]);
	  for($c=0;$c<=1;$c++)
	  {
		$ws='';
		for($i=0;$i<strlen($output[$c]);$i++)
		{
		  if(strpos($validchars, $output[$c][$i])===FALSE)
		  {}
		  else 
		  {
			$ws.=$output[$c][$i];
		  }
		}
		$output[$c]=$ws;
	  }  
	}

	$data[] = array ('output1' => $output[0], 'output2' => $output[1], 'chatid' => $chatid);
	echo json_encode($data);  
}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// funktionen
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

function umlaut($fieldname)
{
    $fieldname = str_replace ("�", "&auml;", $fieldname);
    $fieldname = str_replace ("�", "&Auml;", $fieldname);
    $fieldname = str_replace ("�", "&ouml;", $fieldname);
    $fieldname = str_replace ("�", "&Ouml;", $fieldname);
    $fieldname = str_replace ("�", "&uuml;", $fieldname);
    $fieldname = str_replace ("�", "&Uuml;", $fieldname);
    $fieldname = str_replace ("�", "&szlig;", $fieldname);
    $fieldname = str_replace ("ä", "&auml;", $fieldname);
    $fieldname = str_replace ("Ä", "&Auml;", $fieldname);
    $fieldname = str_replace ("ö", "&ouml;", $fieldname);
    $fieldname = str_replace ("Ö", "&Ouml;", $fieldname);
    $fieldname = str_replace ("ü", "&uuml;", $fieldname);
    $fieldname = str_replace ("Ü", "&Uuml;", $fieldname);
    $fieldname = str_replace ("ß", "&szlig;", $fieldname);
    $fieldname = str_replace ("³", "&sup3;", $fieldname);
    $fieldname = str_replace ("²", "&sup2;", $fieldname);
    $fieldname = str_replace ("^", "&#94;", $fieldname);
    $fieldname = str_replace ("?", "&#063;", $fieldname);
    $fieldname = str_replace ("+", "&#043;", $fieldname);
     
    return $fieldname;
}

function reumlaut($fieldname)
{
    $fieldname = str_replace ("&auml;", "�", $fieldname);
    $fieldname = str_replace ("&Auml;", "�", $fieldname);
    $fieldname = str_replace ("&ouml;", "�", $fieldname);
    $fieldname = str_replace ("&Ouml;", "�", $fieldname);
    $fieldname = str_replace ("&uuml;", "�", $fieldname);
    $fieldname = str_replace ("&Uuml;", "�", $fieldname);
    $fieldname = str_replace ("&szlig;", "�", $fieldname);
    return $fieldname;
}
?>