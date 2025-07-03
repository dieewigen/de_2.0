<?php
//anzeige in der logdatei
include "croninfo.inc.php";

$directory=str_replace("\\\\","/",$_SERVER["SCRIPT_FILENAME"]);
$directory=str_replace("/tickler/register_user.php","/",$directory);
if ($directory=='')$directory='../';
$directory="../";

//include "../inccon.php";
$disablegzip=1;
include $directory."inc/sv.inc.php";
if($sv_comserver==1)include $directory.'inc/svcomserver.inc.php';
include $directory."inccon.php";

echo '<html><head></head><body>';

//gr��ter tick
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//status auslesen
$result = mysql_query("SELECT * FROM de_system",$db);
$row = mysql_fetch_array($result);
$dortick=$row["dortick"];
$reshuffle=$row["reshuffle"];

if($dortick==1){
	//dortag setzen
	mysql_query("update de_system set dortick=0",$db);
  
	//�berpr�fen ob ein reshuffle notwendig ist
	if($reshuffle==1){
		reshuffle();
		mysql_query("update de_system set reshuffle=0",$db);
	}

	//neue accounts an freie koordinaten verschieben
	//spieleraccounts

	//schauen welche sektoren den npcs geh�ren und welche durch den votetimer gesperrt sind
	$db_daten=mysql_query("SELECT sec_id, npc, votetimer FROM de_sector ORDER BY sec_id ASC",$db);
	while($row = mysql_fetch_array($db_daten)){
	  $sec_id=$row["sec_id"];
	  $npc=$row["npc"];
	  $npcsec[$sec_id]=$npc;
	  //gesperrte sektoren vermerken
	  if($row["votetimer"]>0)$blocked_sec[$sec_id]=1;else $blocked_sec[$sec_id]=0;
	}
	//gr��ten sektor in der db finden
	$maxsecindb=$sec_id;

	//zuerstmal alle belegten positionen auslesen
	$db_daten=mysql_query("SELECT sector, `system` FROM de_user_data WHERE sector > 0",$db);
	while($row = mysql_fetch_array($db_daten))
	{
	  $systeme[$row["sector"]][$row["system"]]=1;
	  //alle sektoren die bewohnt sind markieren
	  $bewsector[$row["sector"]]=1;
	}

	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	//alle npc-sektoren als bewohnt setzen, damit wird die erstauff�llung der sektoren ausgeklammert
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	for ($i=1;$i<=$maxsecindb;$i++)
	{
	  if($npcsec[$i]==1)$bewsector[$i]=1;
	}

	//schauen wieviele sektoren frei sind
	$freesectors=0;
	for ($i=2;$i<=$sv_maxsector;$i++)
	{
	if (!isset($bewsector[$i]) || $bewsector[$i]==0) $freesectors++;
	}
	echo 'Freesectors: '.$freesectors.'<br>';

	//////////////////////////////////////////////////////////////////////////////
	//Koordinaten 0:0, Spieler werden in Sektor 1 gepackt
	//////////////////////////////////////////////////////////////////////////////

	$result = mysql_query("SELECT user_id, spielername, npc FROM de_user_data WHERE sector=0 AND `system`=0",$db);
	$num = mysql_num_rows($result);
	while($res = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	{
	  $npc=$res["npc"];
	  //$spielername=$res["spielername"];
	  //freie position ermitteln - anfang
	  $gefunden=0;
	  $maxsector=$sv_maxsector;
	  $maxsystem=$sv_maxsystem;

	  //wenn es ein pc ist, dann mu� er in sektor 1
	  if($npc==0){
		$maxsector=1;
		$maxsystem=10000;
	  }

	  //startsektor bestimmen
	  //$sec=rand (1+$sv_free_startsectors, $maxsector);
	  if ($freesectors>0 AND $npc==1){
		//den x-ten freien platz nimmt man
		$xten=rand(1, $freesectors);
		$treffer=0;
		//freien sektor suchen
		for ($i=1;$i<=$sv_maxsector;$i++)
		{
		  if ($bewsector[$i]==0) //wenn er einen freien sektor hat dann
		  {
			$treffer++;
			if ($xten==$treffer)
			{
			  //sektor als belegt markieren
			  $bewsector[$i]=1;
			  //sektor merken
			  $sec=$i;
			  //schleife beenden
			  break;
			}
		  }
		}
		$freesectors--;
		//echo ' - xten '.$xten.' - treffer '.$treffer.' - sec '.$sec.' - freesectors '.$freesectors;
	  }
	  else //keine freien sektoren mehr vorhanden, also nen sektor so suchen, am besten einen wo wenig drin sind
	  {
		$sql="SELECT sector, count( `system` )  AS systeme FROM `de_user_data` WHERE npc='$npc' AND sector > $sv_free_startsectors AND sector <= $maxsector GROUP BY sector ORDER BY systeme ASC LIMIT 1";
		$rx=mysql_query($sql,$db);
		$rowx = mysql_fetch_array($rx);
		$sec=$rowx["sector"];
		if($npc==0)$sec=1;
		//echo $sql.'<br>'.$sec.'<br>'.$fullsec;
	  }

	  //if($npc==1)$sys=rand (1, $maxsystem);else $sys=1;
	  //von oben her auff�llen
	  $sys=1;

	  for ($secz=$sec;$secz<=$maxsector;$secz++){
		//nur dem sektor zuweisen wenn npc/npcsektor oder pc/pcsektor
		if($npc==$npcsec[$secz]){
			for ($sysz=$sys;$sysz<=$maxsystem;$sysz++)
			{
			  if ($systeme[$secz][$sysz]==0) $gefunden=1;
			  if ($gefunden==1) break;
			}
		}
		if ($gefunden==1) break;
		if ($gefunden==0 && $secz==$maxsector) {$secz=$sv_free_startsectors;$maxsector++;}
		$sys=1;
	  }

	  $systeme[$secz][$sysz]=1;


	  echo '<br>[1]Sektor: '.$secz.' System: '.$sysz;

	  //freie position ermitteln - ende

	  $uid=$res["user_id"];
	  mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid' AND status=0",$db);//status aktiv
	  //heimatsystem festlegen
	  mysql_query("UPDATE de_user_data SET sector='$secz', `system`='$sysz' WHERE user_id='$uid'",$db);
	  //flottenkoordinaten updaten
	  $fleet_id=$uid.'-0';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-1';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-2';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-3';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);

		if($secz>1){
			//mail($GLOBALS['env_admin_email'], $sv_server_tag.': '.$uid.'  hat Sektor 1 verlassen.', $sv_server_tag.': '.$uid.'  hat Sektor 1 verlassen.', 'FROM: '.$GLOBALS['env_admin_email']);
		}
	}

	echo "<br>$num Spieler-Systeme registriert.<br>";


	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	//spieler die per vote rausgevotet worden sind/geresettet haben/aus sektor 1 rausziehen
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	$result = mysql_query("SELECT user_id, sector, `system` FROM de_sector_umzug WHERE typ=0",$db);
	$num = mysql_num_rows($result);
	while($res = mysql_fetch_array($result)){ //jeder gefundene datensatz wird geprueft
		$uid=$res["user_id"];
		//herkunftssektor, da soll er auf keinen fall nochmal hin
		$herksec=$res["sector"];
		$herksys=$res["system"];
		//schauen ob pc oder npc
		$result2 = mysql_query("SELECT spielername, npc, last_sector FROM de_user_data WHERE user_id='$uid'",$db);
		$res2 = mysql_fetch_array($result2);
		$npc=$res2["npc"];
		$spielername=$res2["spielername"];
		//in der Ewigen Runde/Hardcore kommt man nicht in den vorherigen Sektor zur�ck
		if($sv_ewige_runde==1 || $sv_hardcore==1){
			$last_sector=$res2['last_sector'];
			echo '<br>Ewige Runde/Hardcore - last_sector: '. $last_sector;
		}else{
			$last_sector=$herksec;
		}

		//freie position ermitteln - anfang
		$gefunden=0;
		$maxsector=$sv_maxsector;
		$maxsystem=$sv_maxsystem;

		//startsektor bestimmen
		//$sec=rand (1+$sv_free_startsectors, $maxsector);
		if ($freesectors>0){
			echo '<br>freesector>0';
			//den x-ten freien platz nimmt man
			$xten=rand(1, $freesectors);
			$treffer=0;
			//freien sektor suchen
			for ($i=1;$i<=$sv_maxsector;$i++)
			{
			  if ($bewsector[$i]==0) //wenn er einen freien sektor hat dann
			  {
				$treffer++;
				if ($xten==$treffer)
				{
				  //sektor als belegt markieren
				  $bewsector[$i]=1;
				  //sektor merken
				  $sec=$i;
				  //schleife beenden
				  break;
				}
			  }
			}
			$freesectors--;
			//echo ' - xten '.$xten.' - treffer '.$treffer.' - sec '.$sec.' - freesectors '.$freesectors;
		}
		else //keine freien sektoren mehr vorhanden, also einen sektor so suchen, am besten einen wo wenig drin sind
		{
			echo '<br>freesector==0';
			//nur sektoren betrachten, die nicht zu den alien-sektoren geh�ren
			$sql="SELECT de_user_data.sector, count( de_user_data.`system` ) AS systeme FROM de_user_data, de_sector WHERE
			de_user_data.sector > $sv_free_startsectors AND de_user_data.sector <= $maxsector AND de_user_data.sector=de_sector.sec_id 
			AND de_user_data.sector<> '$last_sector' 
			AND de_sector.votetimer=0 AND de_sector.npc=0
			GROUP BY sector ORDER BY systeme, RAND() ASC LIMIT 1";
			$rx=mysql_query($sql,$db);
			$rowx = mysql_fetch_array($rx);
			$sec=$rowx["sector"];
			//echo $sql.'<br>'.$sec.'<br>'.$fullsec;
		}

		//$sys=rand (1, $maxsystem);
		$sys=1;

		for ($secz=$sec;$secz<=$maxsector;$secz++){
			//nur dem sektor zuweisen wenn npc/npcsektor oder pc/pcsektor
			if($npc==$npcsec[$secz])
			for ($sysz=$sys;$sysz<=$maxsystem;$sysz++){//alle systeme von 1 x durchgehen und einen platz suchen
				if ($systeme[$secz][$sysz]==0 AND $secz!=$herksec AND $secz!=$last_sector AND $blocked_sec[$secz]==0) $gefunden=1;
				if ($gefunden==1) break;
			}
			if ($gefunden==1) break;
			//wenn kein platz gefunden werden konnte
			//A: 1 sektor mehr nehmen
			//if ($gefunden==0 && $secz==$maxsector) {$secz=$sv_free_startsectors;$maxsector++;}

			//B: ein system mehr nehmen
			if ($gefunden==0 && $secz==$maxsector) {$secz=$sv_free_startsectors;$maxsystem++;}

			$sys=1;
		}


		$systeme[$secz][$sysz]=1;


		echo '<br>[2]Spielername: '.$spielername.' Sektor: '.$secz.' System: '.$sysz;
		//31588
		//freie position ermitteln - ende

		mysql_query("UPDATE de_login SET status=savestatus WHERE user_id='$uid'",$db);//status aktiv
		//heimatsystem festlegen
		mysql_query("UPDATE de_user_data SET sector=$secz, `system`=$sysz, votefor=0, last_sector='$secz' WHERE user_id='$uid'",$db);
		//info in der sektorhistorie hinterlegen
		mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '2', '$secz', '$spielername');",$db);

		mysql_query("UPDATE de_user_hyper SET fromsec=$secz,  fromsys=$sysz WHERE  absender='$uid' and sender=0",$db);
		//flottenkoordinaten updaten
		$fleet_id=$uid.'-0';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
		$fleet_id=$uid.'-1';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
		$fleet_id=$uid.'-2';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
		$fleet_id=$uid.'-3';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);

		//flottendaten von angreifenden/deffenden flotten auf das neue ziel umlegen
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE zielsec='$herksec' AND zielsys='$herksys' AND hsec<>$secz AND hsys<>$sysz",$db);

		//datensatz aus dem umzug entfernen
		mysql_query("DELETE FROM de_sector_umzug WHERE typ=0 AND user_id='$uid'",$db);

		/*
		if($secz>1){
			mail($GLOBALS['env_admin_email'], $sv_server_tag.': '.$uid.'  hat Sektor 1 verlassen.', $sv_server_tag.': '.$uid.'  hat Sektor 1 verlassen.', 'FROM: '.$GLOBALS['env_admin_email']);
		}
		*/		
	}


	echo "<br>$num Spieler-Systeme-Voteout verschoben.<br>";
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	//spieler die per sektorumzugsfunktion umziehen typ 2, also direkt rein
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////

	$result = mysql_query("SELECT user_id, sector FROM de_sector_umzug WHERE typ=2",$db);
	$num = mysql_num_rows($result);
	while($res = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	{
	  $uid=$res["user_id"];
	  $zielsec=$res["sector"];

	  //herkunftssektor und -system
	  $result1 = mysql_query("SELECT sector, `system`, techs FROM de_user_data WHERE user_id='$uid'",$db);
	  $res1 = mysql_fetch_array($result1);

	  $herksec=$res1["sector"];
	  $herksys=$res1["system"];
	  //technologie wieder entfernen
	  $techs=$res1["techs"];
	  $techs[26]='0';

	  //freie position ermitteln - anfang
	  $gefunden=0;
	  $maxsector=$zielsec;
	  $maxsystem=$sv_maxsystem+2;
	  $sec=$zielsec;
	  $sys=1;

	  for ($secz=$sec;$secz<=$maxsector;$secz++)
	  {
		for ($sysz=$sys;$sysz<=$maxsystem;$sysz++)
		{
		  if ($systeme[$secz][$sysz]==0) $gefunden=1;
		  if ($gefunden==1) break;
		}
		if ($gefunden==1) break;
		if ($gefunden==0 && $secz==$maxsector) {$secz=$sv_free_startsectors;$maxsector++;}
		$sys=1;
	  }

	  $systeme[$secz][$sysz]=1;

	  echo '<br>[3]Sektor: '.$secz.' System: '.$sysz;

	  //freie position ermitteln - ende
	  mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid'",$db);//status aktiv
	  mysql_query("UPDATE de_user_data SET sector=$secz, `system`=$sysz, votefor=0, secmoves=secmoves+1, techs='$techs', last_sector='$secz' WHERE user_id='$uid'",$db);//heimatsystem festlegen
	  //flottenkoordinaten updaten
	  $fleet_id=$uid.'-0';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-1';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-2';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-3';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);

	  //flottendaten von angreifenden/deffenden flotten auf das neue ziel umlegen
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE zielsec='$herksec' AND zielsys='$herksys' AND hsec<>$secz AND hsys<>$sysz",$db);

	  //evtl. laufende votes gegen den account l�schen
	  mysql_query("DELETE FROM de_sector_voteout WHERE user_id='$uid'",$db);
	}
	//alle daten aus de_sector_umzug entfernen
	mysql_query("DELETE FROM de_sector_umzug WHERE typ=2",$db);

	echo "<br>$num Spieler-Systeme Typ 2 verschoben.<br>";


	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// spieler die per sektorumzugsfunktion umziehen typ 1, also einen neuen sektor bekommen
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	//erstmal die gruppen anhand der passw�rter ausfindig machen
	$result1 = mysql_query("SELECT pass FROM de_sector_umzug WHERE typ=1 GROUP BY pass",$db);
	$num=0;
	while($res1 = mysql_fetch_array($result1)) //jeder gefundene datensatz wird geprueft
	{
	  $sektorpass=$res1["pass"];
	  //einen freien sektor suchen
	  $result = mysql_query("SELECT sec_id FROM de_sector WHERE sec_id>=$sv_min_regsec AND pass='' ORDER BY sec_id ASC",$db);
	  $res = mysql_fetch_array($result);
	  $zielsec=$res["sec_id"];

	  $result = mysql_query("SELECT user_id FROM de_sector_umzug WHERE typ=1 AND pass='$sektorpass'",$db);
	  $useranz = mysql_num_rows($result);
	  echo $sektorpass.'<br>';
	  echo $useranz.'<br>';
	  //das passwort im sektor hinterlegen
	  if($useranz>=$sv_min_user_per_regsector) mysql_query("UPDATE de_sector set pass='$sektorpass' WHERE sec_id='$zielsec'",$db);

	  if($useranz>=$sv_min_user_per_regsector)
	  while($res = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	  {

		$uid=$res["user_id"];
		//herkunftssektor und -system
		$result2 = mysql_query("SELECT sector, `system`, techs FROM de_user_data WHERE user_id='$uid'",$db);
		$res2 = mysql_fetch_array($result2);

		$herksec=$res2["sector"];
		$herksys=$res2["system"];
		//technologie wieder entfernen
		$techs=$res2["techs"];
		$techs[26]='0';

		//freie position ermitteln - anfang
		$gefunden=0;
		$maxsector=$zielsec;
		$maxsystem=$sv_max_user_per_regsector;
		$sec=$zielsec;
		$sys=rand(1, $sv_max_user_per_regsector);

		//for ($secz=$sec;$secz<=$maxsector;$secz++)
		//{
		  $secz=$sec;$vonanfang=0;
		  for ($sysz=$sys;$sysz<=$maxsystem;$sysz++)
		  {
			if ($systeme[$secz][$sysz]==0) $gefunden=1;
			if ($gefunden==1) break;
			if ($gefunden==0 && $sysz==$sv_max_user_per_regsector && $vonanfang==0) {$sysz=1;$vonanfang=1;}
			if ($gefunden==0 && $sysz==$sv_max_user_per_regsector && $vonanfang==1) {$sysz=1;$maxsystem++;}
		  }
		  //if ($gefunden==1) break;
		  //if ($gefunden==0 && $secz==$maxsector) {$secz=$sv_free_startsectors;$maxsector++;}
		  //$sys=1;
		//}

		$systeme[$secz][$sysz]=1;


		echo '<br>[4]Sektor: '.$secz.' System: '.$sysz;

		//freie position ermitteln - ende

		mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid'",$db);//status aktiv
		mysql_query("UPDATE de_user_data SET sector=$secz, `system`=$sysz, votefor=0, secmoves=secmoves+1, techs='$techs', last_sector='$secz' WHERE user_id='$uid'",$db);//heimatsystem festlegen
		//flottenkoordinaten updaten
	  $fleet_id=$uid.'-0';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-1';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-2';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);
	  $fleet_id=$uid.'-3';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
	  mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id' AND aktion=0",$db);

		//flottendaten von angreifenden/deffenden flotten auf das neue ziel umlegen
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE zielsec='$herksec' AND zielsys='$herksys' AND hsec<>$secz AND hsys<>$sysz",$db);
		//den umzugsdatensatz l�schen
		mysql_query("DELETE FROM de_sector_umzug WHERE user_id='$uid'",$db);
		//evtl. votes l�schen
		mysql_query("DELETE FROM de_sector_voteout WHERE user_id='$uid'",$db);

		$num++;
	  }
	}
	//die umzugsdaten 1 tick runterz�hlen und falls <=0 kicken
	mysql_query("UPDATE de_sector_umzug set ticks=ticks-1 WHERE typ=1",$db);
	mysql_query("DELETE FROM de_sector_umzug WHERE typ=1 AND ticks<=0",$db);

	echo "<br>$num Spieler-Systeme Typ 1 verschoben.<br>";


	//npc-accounts
	/*
	$result = mysql_query("SELECT user_id FROM de_user_data WHERE sector=0 AND `system`=2",$db);
	$num = mysql_num_rows($result);
	while($res = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	{
	  //freie position ermitteln - anfang
	  $gefunden=0;
	  $maxsector=$sv_npc_maxsector;
	  $maxsystem=$sv_maxsystem;
	  $sec=$sv_npc_maxsector;
	  $sys=1;

	  for ($secz=$sec;$secz>=$sv_npc_minsector; $secz--)
	  {

		for ($sysz=$sys;$sysz<=$maxsystem;$sysz++)
		{
		  if ($systeme[$secz][$sysz]==0) $gefunden=1;
		  if ($gefunden==1) break;
		}
		if ($gefunden==1) break;
		if ($gefunden==0 && $secz==$sv_npc_minsector) {$secz=0;$maxsector++;}
		$sys=1;
	  }

	  $systeme[$secz][$sysz]=1;


	  echo '<br>Sektor: '.$secz.' System: '.$sysz;

	  //freie position ermitteln - ende

	  $uid=$res["user_id"];
	  mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid'",$db);//status aktiv
	  mysql_query("UPDATE de_user_data SET sector=$secz, `system`=$sysz WHERE user_id='$uid'",$db);//heimatsystem festlegen
	  //flottenkoordinaten updaten

	  $fleet_id=$uid.'-0';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
	  $fleet_id=$uid.'-1';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
	  $fleet_id=$uid.'-2';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
	  $fleet_id=$uid.'-3';
	  mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);

	}

	echo "<br>$num NPC-Systeme registriert.<br>";
	*/

	//dortag wieder setzen
	mysql_query("update de_system set dortick=1",$db);
}
else echo 'Registrierung deaktiviert.';

function reshuffle(){
	global $db, $sv_maxsector;

	$daten=array();
	//alle pc-spieler �ber sektor 1 auslesen
	$db_daten = mysql_query("SELECT * FROM de_user_data WHERE sector>1 AND npc=0",$db);
	$anz = mysql_num_rows($db_daten);
	$akti_gesamt=0;
	$akti_gewertet=0;
	while($row = mysql_fetch_array($db_daten)){
		$uid=$row['user_id'];
		$sector_aktuell=$row['sector'];
				
		echo '<br>Spielername: '.$row['spielername'];
		$daten[$uid]['uid']=$uid;
		
		//aktivit�t innerhalb der letzten 60 Tage feststellen und daraus den Mittelwert bilden
		$db_datenx = mysql_query("SELECT * FROM de_user_stat WHERE user_id='$uid' ORDER BY datum DESC LIMIT 60",$db);
		$anzx = mysql_num_rows($db_datenx);
		echo '<br>statistische Daten Anzahl: '.$anzx;
		$daten[$uid]['sektor_aktuell']=$sector_aktuell;
		//es gibt genug Daten
		if($anzx>30){
			$akti=0;
			while($rowx = mysql_fetch_array($db_datenx)){
				//h0-h23 mit status = 2 z�hlen als aktive stunden
				for($i=0;$i<=23;$i++){
					if($rowx['h'.$i]==2){
						$akti++;
					}
				}
			}
			
			$daten[$uid]['akti']=$akti/$anzx;
			$akti_gesamt+=$daten[$uid]['akti'];
			$akti_gewertet++;
			
		}else{
			//es gibt nicht genug Daten, daher erstmal Wert auf -1 setzen
			$daten[$uid]['akti']=-1;
		}
		
		echo '<br>Akti: '.$daten[$uid]['akti'];
	}
	
	//die durschnittliche aktivit�t berechnen und diese dann den -1 Spielern zuweisen, da diese neu sind, erhalten sie den Durchschnittswert
	//dies dient im Endeffet dazu neue Fakeaccounts nicht den ganz guten Sektoren zuzuweisen
	$akti_durchschnitt=$akti_gesamt/$akti_gewertet;
	echo '<br>Akti Durchschnitt: '. $akti_durchschnitt;
	
	foreach($daten as $user){
		if($user['akti']==-1){
			$daten[$user['uid']]['akti']=$akti_durchschnitt;
		}
	}

	foreach($daten as $user){
		//echo '<br>UID: '.print_r($user,true);
	}
	
	//schauen welche pc-sektoren es gibt
	$sektoren=array();
	
	$db_daten=mysql_query("SELECT sec_id FROM de_sector WHERE npc=0 AND sec_id>1 AND sec_id<='".$sv_maxsector."'ORDER BY sec_id ASC",$db);
	while($row = mysql_fetch_array($db_daten)){
		$sektoren[]=$row['sec_id'];
	}
	
	echo '<br>Sektoren: '.print_r($sektoren, true);
	
	
	//die Accounts nach Aktivit�t zuweisen
	//dazu erstmal absteigend sortieren
	function cmp($a, $b){
		//return strcmp($a["akti"], $b["akti"]);
		if($a["akti"]==$b["akti"]) return 0;
		if($a["akti"]> $b["akti"]) return 1;
		if($a["akti"]< $b["akti"]) return -1;
	}
	usort($daten, "cmp");

	//die Spieler einem Sektor zuweisen
	$sector_pos=0;;
	$system=1;
			
	foreach($daten as $key => $user){
		$daten[$key]['sector']=$sektoren[$sector_pos];
		$daten[$key]['system']=$system;
		
		//echo '<br>'.$daten[$key]['sector'].':'.$daten[$key]['system'];
		
		//ungerade von vorne nach hinten, gerade von hinten nach vorne
		if($system %2 != 0){
			$sector_pos++;
			//ende erreicht
			if($sector_pos>count($sektoren)-1){
				$sector_pos=count($sektoren)-1;
				$system++;
				//echo 'Richtungswechsel 1';
			}
			
			
			
		}else{
			$sector_pos--;
			//ende erreicht
			if($sector_pos<0){
				$system++;
				$sector_pos=0;
				//echo 'Richtungswechsel 2';
			}
		}
		
	}	
	
	//akti pro sektor
	$sektor_akti=array();
	foreach($daten as $user){
		$sektor_akti[$user['sector']]['akti']+=$user['akti'];
		//$sektor_akti[$user['sektor_aktuell']]['akti']+=$user['akti'];
		
		//echo '<br>Se: '.$user['sector'];
		//echo '<br>Ak: '.$user['akti'];
	}
	
	print_r($sektor_akti);
	
	echo '<br><br><br><br>';

	//Daten nochmal zur Kontrolle anzeigen
	foreach($daten as $user){
		echo '<br>UID: '.print_r($user,true);
	}
	
	foreach($daten as $user){
		//heimatsystem festlegen
		$uid=$user['uid'];
		$secz=$user['sector'];
		$sysz=$user['system'];
		echo("<br>UPDATE de_user_data SET sector='$secz', `system`='$sysz' WHERE user_id='$uid'");
		//flottenkoordinaten updaten
		$fleet_id=$uid.'-0';
		echo("<br>UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'");
		echo("<br>UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'");
		$fleet_id=$uid.'-1';
		echo("<br>UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'");
		echo("<br>UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'");
		$fleet_id=$uid.'-2';
		echo("<br>UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'");
		echo("<br>UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'");
		$fleet_id=$uid.'-3';
		echo("<br>UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'");
		echo("<br>UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'");


		mysql_query("UPDATE de_login SET status=1 WHERE user_id='$uid' AND status=0",$db);//status aktiv
		mysql_query("UPDATE de_login SET last_login='".date("Y-m-d H:i:s")."', last_click='".date("Y-m-d H:i:s")."' 
			WHERE user_id='$uid' AND (status=1 OR status=3)",$db);//status aktiv

		//heimatsystem festlegen
		mysql_query("UPDATE de_user_data SET sector='$secz', `system`='$sysz' WHERE user_id='$uid'",$db);
		//flottenkoordinaten updaten
		$fleet_id=$uid.'-0';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
		$fleet_id=$uid.'-1';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
		$fleet_id=$uid.'-2';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
		$fleet_id=$uid.'-3';
		mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz WHERE user_id='$fleet_id'",$db);
		mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
	}
	

}

?>
