<?php
//create_daily_server_statistic();

//give_sector_bonus();

//give_ea_creditbonus();

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//  alte chateintr�ge l�schen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if((strftime("%M"))==0){
	$time=time()-3600*24*2;
	mysql_query("DELETE FROM de_chat_msg WHERE timestamp<'$time'",$db);
	mysql_query("DELETE FROM de_chat_msg WHERE timestamp<'$time'",$soudb);
}

//aktuelle stunde ermitteln
$time=(int)strftime("%H");

  if ($nachtcron!=$time){
    echo 'TIME: '.$time;
    //$time=1;
    switch ($time) {
    case 0:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
    
      //die t�gliche Serverstatistik erstellen
      create_daily_server_statistic();
    
    
      //die aktuellen pl�tze f�r die rangliste sichern - spieler
      mysql_query("UPDATE de_user_data SET platz_last_day = platz",$db);
      //die aktuellen pl�tze f�r die rangliste sichern - sektoren
      mysql_query("UPDATE de_sector SET platz_last_day = platz",$db);
      //die t�glichen boni zur�cksetzen
      mysql_query("UPDATE de_user_data SET dailygift=1, dailyallygift=1",$db);
      
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      //die tägliche statistik speichern, alte entfernen
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      $zeit=strftime("%Y-%m-%d");
      //daten für die userstatistik speichern
      $db_daten=mysql_query("SELECT user_id, score, col, efta_user_id FROM de_user_data WHERE npc=0",$db);
      echo "<br>$num Spieler für die tägliche Statistik geladen.<br>";
      while ($row = mysql_fetch_array($db_daten)){
        $uid=$row["user_id"];
        $score=$row["score"];
        $col=$row["col"];
        $efta_user_id=$row["efta_user_id"];

        //cyborgpunkte laden
        $db_datenx=mysql_query("SELECT exp FROM de_cyborg_data WHERE user_id='$efta_user_id'",$eftadb);
        $rowx = mysql_fetch_array($db_datenx);
        $cybexp=$rowx["exp"];
        mysql_query("INSERT INTO de_user_stat SET user_id='$uid', datum='$zeit', score='$score', col='$col', cybexp='$cybexp'",$db);
			}

			//maximal 1 Jahr speichern

			mysql_query("DELETE FROM de_user_stat WHERE datum < '".date("Y-m-d", time()-3600*24*360)."';",$db);
			
      //daten für die sektorstatistik speichern
      // punkte, kollektoren, platz
      $db_daten=mysql_query("SELECT sector, SUM(score) as score, SUM(col) AS cols FROM de_user_data WHERE npc=0 AND sector>1 GROUP BY sector ORDER BY score DESC",$db);
      $platz=1;
      while($row = mysql_fetch_array($db_daten))
      {
        $sec=$row["sector"];
        //daten in der db speichern
        $score=$row["score"];
        $cols=$row["cols"];
        mysql_query("INSERT INTO de_sector_stat SET sec_id='$sec', datum='$zeit', score='$score', col='$cols', platz='$platz'",$db);
        $platz++;
      }

      //daten f�r die allianz speichern
      //punkte, kollektoren, meber, platz
      $db_daten=mysql_query("SELECT allytag, SUM(score) as score, SUM(col) as col, COUNT(allytag) AS am FROM de_user_data WHERE allytag<>'' AND status=1 group by allytag order by score DESC",$db);
      $platz=1;
      while($row = mysql_fetch_array($db_daten))
      {
        $allytag=$row["allytag"];
        $score=$row["score"];
        $cols=$row["col"];
        $member=$row["am"];

        //allyid laden
        $db_datenx=mysql_query("SELECT id FROM de_allys WHERE allytag='$allytag'",$db);
        $rowx = mysql_fetch_array($db_datenx);
        $allyid=$rowx["id"];

        //daten in der db speichern
        mysql_query("INSERT INTO de_ally_stat SET id='$allyid', datum='$zeit', score='$score', col='$cols', platz='$platz', member='$member'",$db);
        $platz++;
      }
      
      break;

    case 1:
      mysql_query("DELETE FROM  de_user_locks",$db);
      
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      // den sou creditbonus geben
      if($sv_deactivate_sou==0){
        give_ea_creditbonus();
      }
      // den efta-bonus geben
      //if($sv_deactivate_efta==0)give_efta_bonus();
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////   
      
      break;

    case 2:
      mysql_query("DELETE FROM  de_user_locks",$db);
      include "wt_externe_topliste_alli.php";
      break;

    case 3:
      mysql_query("DELETE FROM  de_user_locks",$db);
      remove_sm_rboost_br();
      break;

    case 4:
      mysql_query("DELETE FROM  de_user_locks",$db);

      //die alten Login-Einträge löschen
      mysql_query("DELETE FROM de_user_ip WHERE time < '".date("Y-m-d H:i:s", time()-3600*24*90)."';",$db);

      break;
    case 5:
      mysql_query("DELETE FROM  de_user_locks",$db);
      break;

    case 6:
      mysql_query("DELETE FROM  de_user_locks",$db);
      remove_sm_rboost_br();
      include "wt_externe_topliste_alli.php";
      break;
    case 7:
      mysql_query("DELETE FROM  de_user_locks",$db);
      break;
    case 8:
      mysql_query("DELETE FROM  de_user_locks",$db);
      break;
    case 9:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
      break;

    case 10:
      mysql_query("DELETE FROM de_user_locks",$db);
      include "wt_externe_topliste_alli.php";
      //include "wt_externe_topliste_cyborg.php";
      include "wt_externe_topliste_sektor.php";
      include "wt_externe_topliste_spieler.php";
      break;

    case 11:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
    case 12:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
      break;
    case 13:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
    case 14:
      mysql_query("DELETE FROM de_user_locks",$db);
      include "wt_externe_topliste_alli.php";
      break;
    case 15:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
      break;
    case 16:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
    case 17:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
	case 18:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
	  include "wt_externe_topliste_alli.php";
	  
	  //Anzahl der Spieler im globalen Chat anzeigen
	  if($sv_server_tag=='SDE'){
		$db_daten_uc=mysqli_query($GLOBALS['dbi_ls'], "SELECT anzahl FROM ".$GLOBALS['env_db_loginsystem_database'].".ls_user_count ORDER BY datum DESC LIMIT 1;");
		$row_uc = mysqli_fetch_array($db_daten_uc);
		$anzahl_uc=$row_uc["anzahl"];

		$channel=0;$channeltyp=3;$spielername='[SYSTEM]'; $chat_message='<span style="font-weight: bold; color: #ffff00;">Es gibt aktuell '.$anzahl_uc.' aktive Spieler.</span>';
		insert_chat_msg_admin($channel, $channeltyp, $spielername, $chat_message, -1, 'DE');		  
	  }

      break;
    case 19:
      mysql_query("DELETE FROM de_user_locks",$db);
    give_sector_bonus();
      break;
    case 20:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
    case 21:
      mysql_query("DELETE FROM de_user_locks",$db);
      remove_sm_rboost_br();
      break;

    case 22:
      mysql_query("DELETE FROM de_user_locks",$db);
      include "wt_externe_topliste_alli.php";
      //include "wt_externe_topliste_cyborg.php";
      include "wt_externe_topliste_sektor.php";
      include "wt_externe_topliste_spieler.php";
      break;

    case 23:
      mysql_query("DELETE FROM de_user_locks",$db);
      break;
    }


    //cron aktualisieren
    mysql_query("UPDATE de_system SET nachtcron = '$time'",$db);
}

/*
if ($nachtcron==1 AND $time>=3)
{
  echo '<br>DER NACHTCRON WURDE GESTARTET<br>';
  //die aktuellen pl�tze f�r die rangliste sichern - spieler
  mysql_query("UPDATE de_user_data SET platz_last_day = platz",$db);
  //die aktuellen pl�tze f�r die rangliste sichern - sektoren
  mysql_query("UPDATE de_sector SET platz_last_day = platz",$db);
  //nachtcron deaktivieren
  mysql_query("UPDATE de_system SET nachtcron = 0",$db);
}
//um 1 uhr wieder aktivieren, damit es sp�ter wieder ausgef�hrt wird
if ($time>=1 AND $time<2)
*/
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////

function give_sector_bonus(){
	
	global $db, $maxtick;
	
	//erst nach 2000 ticks
	if($maxtick>=2000){
		
		$time=strftime("%Y%m%d%H%M%S");
    
    //ticks pro tag auslesen
    /*
		$filename="runtick.sh";
		$cachefile = fopen ($filename, 'r');
		$wticks=trim(fgets($cachefile, 1024));
		$anzwticksprostunde=0;
    for($i=1;$i<=60;$i++)if($wticks[$i]==1)$anzwticksprostunde++;		
    */

    $anzwticksprostunde=count($GLOBALS['wts'][12]);

    //echo 'AnzStunden: '.$anzwticksprostunde;

		
		//alle sektoren nach kollektoren geordnet auslesen
		unset($secdata);
		$result  = mysql_query("SELECT sec_id, tempcol FROM `de_sector` WHERE npc=0 AND sec_id > 1 AND platz>0 ORDER BY tempcol ASC",$db);
		$c=0;
		while($row = mysql_fetch_array($result))
		{
			$secdata[$c]['sec_id']=$row['sec_id'];
			$secdata[$c]['col']=$row['tempcol'];
			$c++;
		}
		
		//alle sektoren durchgehen und die rohstoffe verteilen
		for($i=0;$i<count($secdata);$i++){
			//echo '<br>Sektor '.$secdata[$i]['sec_id'].' hat '.($secdata[$i]['col']*$anzwticksprostunde*24*100/10).' bekommt '.($secdata[count($secdata)-1-$i]['col']*$anzwticksprostunde*24*100/10);
			
			$sektorgesamtstunden=0;
			
			//spieler des sektors auslesen und deren aktivität feststellen
			
			$result  = mysql_query("SELECT user_id, ekey FROM `de_user_data` WHERE sector='".$secdata[$i]['sec_id']."';",$db);
			$cp=0;unset($playerdata);
			while($row = mysql_fetch_array($result)){
				$playerdata[$cp]['user_id']=$row['user_id'];
				
				$hv=explode(";",$row["ekey"]);
				if ($hv[0]=='') $hv[0]=0;
				if ($hv[1]=='') $hv[1]=0;
				if ($hv[2]=='') $hv[2]=0;
				if ($hv[3]=='') $hv[3]=0;
				$playerdata[$cp]['keym']=$hv[0];
				$playerdata[$cp]['keyd']=$hv[1];
				$playerdata[$cp]['keyi']=$hv[2];
				$playerdata[$cp]['keye']=$hv[3];
        
				//aktivität auslesen, maximalaktivität pro tag max 12h
				$db_daten=mysql_query("SELECT * FROM de_user_stat WHERE user_id='".$playerdata[$cp]['user_id']."' ORDER BY datum DESC LIMIT 7",$db);
				while($rowx = mysql_fetch_array($db_daten)){
					$tagesstunden=0;
					for($s=0;$s<=23;$s++){
						if($rowx['h'.$s]==2)$tagesstunden++;
						if($tagesstunden>12)$tagesstunden=12;
					}
					$playerdata[$cp]['gesamtstunden']+=$tagesstunden;
					$sektorgesamtstunden+=$tagesstunden;
					
					
				}
				
				$cp++;
			}
      
      $sektor_rohstoffe_ingesamt=($secdata[count($secdata)-1-$i]['col']*$anzwticksprostunde*24*100/10);

			//die rohstoffe nach der aktivit�t der spieler verteilen
			for($p=0;$p<count($playerdata);$p++){

				//berechnen wie viele rohstoffe der spieler bekommt
				$anteilige_rohstoffe_insgesamt=($secdata[count($secdata)-1-$i]['col']*$anzwticksprostunde*24*100/10)/100*($playerdata[$p]['gesamtstunden']*100/$sektorgesamtstunden);
        
        $spieler_anteil[1]=$anteilige_rohstoffe_insgesamt/100*$playerdata[$p]['keym'];
        $spieler_anteil[2]=$anteilige_rohstoffe_insgesamt/100*$playerdata[$p]['keyd']/2;
        $spieler_anteil[3]=$anteilige_rohstoffe_insgesamt/100*$playerdata[$p]['keyi']/3;
        $spieler_anteil[4]=$anteilige_rohstoffe_insgesamt/100*$playerdata[$p]['keye']/4;
        
				//news hinterlegen
        $newstext='
        Dein Sektor erh&auml;lt eine Aufbauhilfe im Werte von '.number_format($sektor_rohstoffe_ingesamt, 0, ',' ,'.').' Multiplex.
        <br><br>Dein Anteil daran betr&auml;gt:
        <br>M: '.number_format($spieler_anteil[1], 0, ',' ,'.').'
        <br>D: '.number_format($spieler_anteil[2], 0, ',' ,'.').'
        <br>I: '.number_format($spieler_anteil[3], 0, ',' ,'.').'
        <br>E: '.number_format($spieler_anteil[4], 0, ',' ,'.').'

				<br><br>Dein Anteil berechnet sich aus der Gesamtaktivit&auml;t aller Spieler des Sektors innerhalb der letzten sieben Tage, wobei das t&auml;gliche Maximum pro Spieler bei 12 Stunden liegt.';
				mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('".$playerdata[$p]['user_id']."', 60,'$time','$newstext')",$db);
				
				//dem spieler die rohstoffe gutschreiben
				mysql_query("UPDATE de_user_data SET newnews=1, 
        restyp01=restyp01+'".$spieler_anteil[1]."', 
        restyp02=restyp02+'".$spieler_anteil[2]."', 
        restyp03=restyp03+'".$spieler_anteil[3]."', 
        restyp04=restyp04+'".$spieler_anteil[4]."' 
        
        WHERE user_id='".$playerdata[$p]['user_id']."'",$db);
				
		
			}
			
		}
	}
}

function give_efta_bonus(){
	global $db, $eftadb, $wt_lang;

	echo '<hr><br>EFTA Bonus gutschreiben:<br>';

	//alle accounts auslesen
	//$db_daten=mysql_query("SELECT de_user_data.user_id, de_user_data.sou_user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0",$db);
	//$db_daten=mysql_query("SELECT de_cyborg_data.user_id FROM de_login left join de_cyborg_data on(de_login.user_id = de_cyborg_data.user_id) WHERE de_login.status=1",$db);
	$db_daten=mysql_query("SELECT de_user_data.user_id, de_user_data.efta_user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.efta_user_id>0",$db);  
	$index=0; 
	while($row = mysql_fetch_array($db_daten))
	{
	  $user[$index][0]=$row["user_id"];
	  $user[$index][1]=$row["efta_user_id"];

	  $index++;
	}

	//zuerstmal den durchschnitt berechnen
	//spenden im vergleich zum "vorjahr"
	$db_daten=mysql_query("SELECT SUM(exp)-SUM(explastday) AS wert FROM `de_cyborg_data`",$eftadb);
	$row = mysql_fetch_array($db_daten);
	$tageswachstum=$row["wert"];
	//spendendurchschnitt
	$db_daten=mysql_query("SELECT COUNT(*) AS wert FROM `de_cyborg_data` WHERE exp<>explastday",$eftadb);
	$row = mysql_fetch_array($db_daten);
	$durchschnitt=$row["wert"];
	echo '<br>Durchschnitt: '.$durchschnitt;
	if($durchschnitt<1)$durchschnitt=1;
	//vom durschnitt mu� man x% haben
	$p=20;
	$durchschnitt=$tageswachstum/$durchschnitt/100*$p;
	echo '<br>Tageswachstum: '.$tageswachstum;
	echo '<br>Durchschnitt: '.$durchschnitt;

	$eftaartefakte=5;
	unset($sqlcommand);
	$textzuinaktiv=$wt_lang[eftazuinaktiv].'<br>'.$wt_lang[eftazuerreichen].' '.$p.'%';
	$textaktiv=$wt_lang[eftaaktiv].'<br>'.$wt_lang[eftaartefakte].': '.$eftaartefakte;
	$time=strftime("%Y%m%d%H%M%S");
	//jetzt alle efta_user_ids durchgehen und schauen was man bekommt
	for($i=0;$i<count($user);$i++){
		$de_uid=$user[$i][0];
		$efta_uid=$user[$i][1];
		//efta_user_id = 0 -> man bekommt gar nichts
		if($efta_uid==0){
			//nachricht, dass man keinen account hat und somit auch nichts bekommt
			$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$wt_lang[eftakeinaccount]')";
		}else{ //man hat einen account
			//auslesen wie aktiv man war
			$db_daten=mysql_query("SELECT SUM(exp)-SUM(explastday) AS wert FROM `de_cyborg_data` WHERE user_id='$efta_uid'",$eftadb);
			$row = mysql_fetch_array($db_daten);
			if($row["wert"]>=$durchschnitt && $durchschnitt>0){//man bekommt einen bonus
				$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$textaktiv')";
				$sqlcommand[]="UPDATE de_user_data SET eartefakt=eartefakt+'$eftaartefakte', geteftabonus=geteftabonus+1 WHERE user_id='$de_uid'";
			}else{ //man ist zu inaktiv und bekommt nur eine info
				$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$textzuinaktiv')";
			}
		}
		$sqlcommand[]="UPDATE de_user_data SET newnews = 1 WHERE user_id = '$de_uid'";
	}

	//alle befehle durchf�hren
	for($i=0;$i<count($sqlcommand);$i++){
		echo '<br>'.$sqlcommand[$i];
		mysql_query($sqlcommand[$i], $db);
	}
}

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////

function give_ea_creditbonus(){
	global $db, $sv_database_de, $sv_database_sou, $wt_lang, $soudb;
	
	echo '<hr><br>EA Credits gutschreiben:<br>';
	
	//alle de-accounts inkl. sou-id auslesen
	$db_daten=mysql_query("SELECT de_user_data.user_id, de_user_data.sou_user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0",$db);
	$index=0; 
	while($row = mysql_fetch_array($db_daten))
	{
		$user[$index][0]=$row["user_id"];
		$user[$index][1]=$row["sou_user_id"];
		
		$index++;
	}
	
	//sou db w�hlen
	//mysql_select_db($sv_database_sou,$db);
	
	//zuerstmal den durchschnitt berechnen
	//spenden im vergleich zum "vorjahr"
	$db_daten=mysql_query("SELECT SUM(donate)-SUM(donatelastday) AS wert FROM `sou_user_data`",$soudb);
	$row = mysql_fetch_array($db_daten);
	$jahrespenden=$row["wert"];
	//spendendurchschnitt
	$db_daten=mysql_query("SELECT COUNT(*) AS wert FROM `sou_user_data` WHERE donate<>donatelastday",$soudb);
	$row = mysql_fetch_array($db_daten);
	$durchschnitt=$row["wert"];
	if($durchschnitt<1)$durchschnitt=1;
	//vom durschnitt mu� man x% haben
	$p=50;
	$durchschnitt=round($jahrespenden/$durchschnitt/100*$p);
	
	$credits=1;
	unset($sqlcommand);
	$textzuinaktiv=$wt_lang[souzuinaktiv].'<br>'.$wt_lang[souzuerreichen].' '.$p.'%';
	$textaktiv=$wt_lang[souaktiv].'<br>'.$wt_lang[soucredits].': '.$credits;
	$time=strftime("%Y%m%d%H%M%S");
	//jetzt alle sou_user_ids durchgehen und schauen was man bekommt
	for($i=0;$i<count($user);$i++){
		$de_uid=$user[$i][0];
		$sou_uid=$user[$i][1];
		//sou_user_id = 0 -> man bekommt gar nichts
		if($sou_uid==0){
			//nachricht, dass man keinen account hat und somit auch nichts bekommt
			$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$wt_lang[soukeinaccount]')";
		}
		else //man hat einen account
		{
		//auslesen wie aktiv man war
		$db_daten=mysql_query("SELECT SUM(donate)-SUM(donatelastday) AS wert FROM `sou_user_data` WHERE user_id='$sou_uid'",$soudb);
		$row = mysql_fetch_array($db_daten);
		if($row['wert']>=$durchschnitt && $durchschnitt>0)//man bekommt einen credit
		{
			$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$textaktiv')";
			$sqlcommand[]="UPDATE de_user_data SET credits=credits+'$credits', geteacredits=geteacredits+'$credits' WHERE user_id='$de_uid'";
		}
		else //man ist zu inaktiv und bekommt nur eine info
		{
			$msg=$textzuinaktiv.
			'<br>Zu erreichender Spendenwert: '.number_format($durchschnitt, 0, ',' ,'.').
			'<br>Erreichter Spendenwert: '.number_format($row['wert'], 0, ',' ,'.');
			$sqlcommand[]="INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$de_uid', 60,'$time','$msg')";
			
		}
		}
		$sqlcommand[]="UPDATE de_user_data SET newnews = 1 WHERE user_id = '$de_uid'";
	}

	
	//sou db abw�hlen
	//mysql_select_db($sv_database_de,$db);
	
	//alle befehle durchf�hren
	for($i=0;$i<count($sqlcommand);$i++){
		echo '<br>'.$sqlcommand[$i];
		mysql_query($sqlcommand[$i], $db);
	}
}

function remove_sm_rboost_br(){
	global $db, $sv_ewige_runde, $sv_hardcore;
	if($sv_ewige_runde!=1 && $sv_hardcore!=1){
		//erst ab 2500000 ticks aktiv werden
		//maximale tickgr��e laden
		$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
		$row = mysql_fetch_array($db_daten);
		if ($row["tick"]>2500000)
		{
		  //es ist br also sm-sperre entfernen
		  mysql_query("UPDATE de_user_data SET sm_rboost=0",$db);
		}
	}
}


function gzcompressfile($source,$level=false)
{
  $dest=$source.'.gz';
  $mode='w'.$level;
  $error=false;
  if($fp_out=gzopen($dest,$mode))
  {
    if($fp_in=fopen($source,'rb'))
    {
      while(!feof($fp_in))
        gzputs($fp_out,fread($fp_in,1024*512));
      fclose($fp_in);
    }
    else
      $error=true;
    gzclose($fp_out);
  }
  else
    $error=true;
  if($error)
    return false;
  else
    return $dest;
}

function create_daily_server_statistic(){
	global $db;
	//die ben�tigten Daten f�r die Statistikwerte aus der DB holen
    $db_daten=mysql_query("SELECT 
		COUNT(user_id) AS active_player, 
		SUM(score) AS gesamt_score,
		MAX(score) AS max_score,
		SUM(ehscore) AS gesamt_eh_score,
		MAX(ehscore) AS max_eh_score,
		SUM(col) AS gesamt_col, 
		MAX(col) AS max_col,
		SUM(agent) AS gesamt_agent,
		MAX(agent) AS max_agent,
		SUM(agent_lost) AS gesamt_agent_lost,
		MAX(agent_lost) AS max_agent_lost,
		SUM(col_build) AS gesamt_col_build,
		MAX(col_build) AS max_col_build,
		SUM(kartefakt) AS gesamt_kartefakt,
		MAX(kartefakt) AS max_kartefakt 
		FROM de_user_data WHERE npc=0 AND sector>1",$db);
	
	//Sektorkollektoren
	
	
    $row = mysql_fetch_array($db_daten);
	$active_player=		$row['active_player'];
	$gesamt_score=		$row['gesamt_score'];
	$max_score=			$row['max_score'];
	$gesamt_eh_score=	$row['gesamt_eh_score'];
	$max_eh_score=		$row['max_eh_score'];
	$gesamt_col=		$row['gesamt_col'];
	$max_col=			$row['max_col'];
	$gesamt_agent=		$row['gesamt_agent'];
	$max_agent=			$row['max_agent'];
	$gesamt_agent_lost=	$row['gesamt_agent_lost'];
	$max_agent_lost=	$row['max_agent_lost'];
	$gesamt_col_build=	$row['gesamt_col_build'];
	$max_col_build=		$row['max_col_build'];
	$gesamt_kartefakt=	$row['gesamt_kartefakt'];
	$max_kartefakt=		$row['max_kartefakt'];	
	
	$sql="INSERT INTO de_server_stat SET 
		datum=NOW()- INTERVAL 1 DAY,
		active_player='$active_player',
		gesamt_score='$gesamt_score',
		max_score='$max_score',
		gesamt_eh_score='$gesamt_eh_score',
		max_eh_score='$max_eh_score',
		gesamt_col='$gesamt_col',
		max_col='$max_col',
		gesamt_agent='$gesamt_agent',
		max_agent='$max_agent',
		gesamt_agent_lost='$gesamt_agent_lost',
		max_agent_lost='$max_agent_lost',
		gesamt_col_build='$gesamt_col_build',
		max_col_build='$max_col_build',
		gesamt_kartefakt='$gesamt_kartefakt',
		max_kartefakt='$max_kartefakt'
			";
			 
	//echo $sql;
	mysql_query($sql,$db);
}

/*
die folgenden dateien liegen im Tickordner und m�ssen zu den angezeigten Zeiten ausgef�hrt werden!

wt_externe_topliste_alli.php <==  2 Uhr & 6 Uhr & 10 Uhr & 14 Uhr & 18 Uhr & 22 Uhr
wt_externe_topliste_cyborg.php <== 10 Uhr & 22 Uhr
wt_externe_topliste_sektor.php <== 10 Uhr & 22 Uhr
wt_externe_topliste_spieler.php <== 10 Uhr & 22 Uhr
*/
?>
