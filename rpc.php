<?php
$disablegzip=1;
include "inccon.php";
include "inc/sv.inc.php";
include "inc/env.inc.php";
include 'functions.php';
include "inc/lang/".$sv_server_lang."_rpc.lang.php";

//jeden aufruf �ber einen key checken
if(!isset($_REQUEST["authcode"]) || $_REQUEST["authcode"]!=$GLOBALS['env_rpc_authcode']){
	exit;
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//daten für die rangliste der accountverwaltung zurückliefern
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["gettlscore"]) && $_REQUEST["gettlscore"]==1){
	//zuerst die anzahl der aktiven spieler feststellen
	//bei non-bezahlservern alle spieler werten, auf bezahlservern nur die pa-user
	if($sv_payserver==0){
		$db_daten=mysql_query("SELECT de_login.owner_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.owner_id>0 AND de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY score DESC",$db);
	}else{
		$db_daten=mysql_query("SELECT de_login.owner_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.owner_id>0 AND de_login.status=1 AND de_user_data.npc=0 AND de_user_data.premium=1 AND de_user_data.sector>1 ORDER BY score DESC",$db);  	
	}
	
	$anzspieler = mysql_num_rows($db_daten);
	//alle aktiven spieler erhalten punkte
	while($row = mysql_fetch_array($db_daten)){
		echo $row["owner_id"].'@'.($anzspieler).';';
		$anzspieler--;
	}
}
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//feststellen wieviele user es auf dem server gibt
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["getaccountanz"]) && $_REQUEST["getaccountanz"]==1){
	//$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector>1 AND npc=0",$db);
	$db_daten=mysql_query("SELECT de_login.user_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1",$db);

	$num = mysql_num_rows($db_daten);
	echo $num;
}

//feststellen ob es einen account mit der owner_id gibt
if(isset($_REQUEST["isaccount"]) && $_REQUEST["isaccount"]==1){
	$id=intval($_REQUEST["id"]);
	if($id==0)$id='-1';
	$db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id='$id'",$db);
	$num = mysql_num_rows($db_daten);
	if($num==1){
		$row = mysql_fetch_array($db_daten);
		$user_id=$row["user_id"];
		echo $user_id;
	}
	else echo '0';
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//feststellen ob es einen account mit der user_id gibt
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["isaccount_user_id"]) && $_REQUEST["isaccount_user_id"]==1)
{
  $id=intval($_REQUEST["id"]);
  if($id==0)$id='-1';
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE user_id='$id'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    echo '1';
  }
  else echo '0';
}
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//accountdaten abfragen
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["getaccountdata"]) && $_REQUEST["getaccountdata"]==1)
{
  $id=intval($_REQUEST["id"]);
  if($id==0)$id='-1';
  $db_daten=mysql_query("SELECT de_login.user_id, de_login.supporter, de_login.last_login, de_login.delmode, de_login.status AS astatus, de_user_data.spielername, de_user_data.tick, de_user_data.score, de_login.status AS lstatus, de_login.last_login, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.credits, de_user_data.patime, de_user_data.efta_user_id, de_user_data.sou_user_id FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.owner_id='$id'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1){
    $row = mysql_fetch_array($db_daten);
    $user_id=$row["user_id"];
    $accstatus=$row["astatus"];
    $delmode=$row["delmode"];
    $efta_user_id=$row['efta_user_id'];
    $sou_user_id=$row['sou_user_id'];
    $last_login=$row['last_login'];
    $last_login=strtotime($last_login);
    $last_login=date("d.m.Y - G:i", $last_login);
    
    
    $supporter=$row['supporter'];
    if($supporter=='')$supporter='support@die-ewigen.com';
            
    //spielername auslesen, kann nach gametyp variieren
    if($sv_efta_in_de==1 AND $sv_sou_in_de==1)
    {
      $spielername=$row["spielername"];
    }
    else 
    {
      //efta steht allein
      if($sv_efta_in_de==0)
      {
        //spielername aus der efta-db auslesen
        include_once "eftadata/lib/efta_dbconnect.php";
        $db_datenx=mysql_query("SELECT * FROM de_cyborg_data WHERE user_id='$efta_user_id'",$eftadb);
  	    $num = mysql_num_rows($db_datenx);
  	    if($num==1)
  	    {
  	      $rowx = mysql_fetch_array($db_datenx);
          $spielername=$rowx['spielername'];
  	    }
  	    else $spielername='N/A';
      }
      //sou steht allein
      if($sv_sou_in_de==0)
      {
        //spielername aus der efta-db auslesen
        include "soudata/lib/sou_dbconnect.php";
        $db_datenx=mysql_query("SELECT * FROM sou_user_data WHERE user_id='$sou_user_id'",$soudb);
  	    $num = mysql_num_rows($db_datenx);
  	    if($num==1)
  	    {
  	      $rowx = mysql_fetch_array($db_datenx);
          $spielername=$rowx['spielername'];
  	    }
  	    else $spielername='N/A';
      }
    }
    
    if($row["status"]==1 AND $row["allytag"]!='')$allytag=$row["allytag"];else $allytag='-';
    echo $user_id.';'.$accstatus.';'.$spielername.';'.number_format($row["score"], 0,"",".").';'.number_format($row["tick"], 0,"",".").';'.
    $allytag.';'.$row["credits"].';'.$row["patime"];
    //spielerbeschreibung
    echo ';';
    echo $rpc_lang['serverspielerid'].': '.$sv_server_tag.$user_id.'<br>';
    echo $rpc_lang['spielername'].': '.urlencode($spielername).'<br>'; 
    if($sv_sou_in_de!=0 AND $sv_efta_in_de!=0)echo $rpc_lang['punkte'].': '.number_format($row["score"], 0,"",".").'<br>';
    if($sv_sou_in_de!=0 AND $sv_efta_in_de!=0)echo $rpc_lang['accountalter'].': '.number_format($row["tick"], 0,"",".").'<br>';
    if($sv_sou_in_de!=0 AND $sv_efta_in_de!=0)echo $rpc_lang['allianz'].': '.urlencode($allytag).'<br>';
    echo $rpc_lang['credits'].': '.number_format($row["credits"], 0,"",".").'<br>';
    
    if($accstatus!=3)echo $rpc_lang['lastactive'].': '.$last_login.'<br>';
    
    if($accstatus==3 AND $delmode==0)echo $rpc_lang['urlaubinfo'].': '.$last_login;
    if($accstatus==3 AND $delmode==1)echo $rpc_lang['loeschinfo'].': '.$last_login;
    if($accstatus==2)echo $rpc_lang['sperrinfo'].': '.$supporter;
    
  }
  else echo '0';
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//einen loginkey setzen
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["setloginkey"]) && $_REQUEST["setloginkey"]==1)
{
  $id=intval($_REQUEST["id"]);
  $ip=$_REQUEST["ip"];
  $pass=$_REQUEST["pass"];
  if($id==0)$id='-1';
  //schauen ob es den account gibt
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id='$id'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    //falls ja user id auslesen und per zufall den key generieren
    $row = mysql_fetch_array($db_daten);
    $user_id=$row["user_id"];
    //key generieren
    $pwstring='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $loginkey=$pwstring[rand(0, strlen($pwstring)-1)];
    for($i=1; $i<16; $i++) $loginkey.=$pwstring[rand(0, strlen($pwstring)-1)];
    //den key und den zeitpunkt der generierung in der datenbank hinterlegen
    mysql_query("UPDATE de_login SET loginkey='$loginkey', loginkeytime=UNIX_TIMESTAMP( ), loginkeyip='$ip', pass='$pass' WHERE owner_id = '$id'",$db);
    //den key zur�ckgeben
    echo $loginkey;
  }
  else echo 'error';
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//credittransfer
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["credittransfer"]) && $_REQUEST["credittransfer"]==1){
	$id=intval($_REQUEST["id"]);
	$credits=intval($_REQUEST["credits"]);
	if($id==0)$id='-1';
	//schauen ob es den account gibt
	$db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id='$id'",$db);
	$num = mysql_num_rows($db_daten);
	if($num==1){
		//falls ja user id auslesen und die credits gutschreiben
		$row = mysql_fetch_array($db_daten);
		$user_id=$row["user_id"];
		mysql_query("UPDATE de_user_data SET credits=credits+'$credits', credittransfer=credittransfer+'$credits' WHERE user_id = '$user_id'",$db);
		//transfer mitloggen
		$datum=date("Y-m-d H:i:s",time());
		$ip=getenv("REMOTE_ADDR");
		$clog="Zeit: $datum\n".$credits.' Credit(s) wurden vom Hauptaccount (ID '.$id.') transferiert'."\n--------------------------------------\n";
		$fp=fopen("cache/creditlogs/$user_id.txt", "a");
		fputs($fp, $clog);
		fclose($fp);    
		//status 1 = alles ok
		echo '1';
	}
	else echo '2'; //status 2 = fehler
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//credittransfer durchs billingsystem
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["credittransfer_billing"]) && $_REQUEST["credittransfer_billing"]==1)
{
  $user_id=intval($_REQUEST["id"]);
  $credits=intval($_REQUEST["credits"]);
  if($id==0)$id='-1';

  //credits gutschreiben  
  mysql_query("UPDATE de_user_data SET credits=credits+'$credits' WHERE user_id = '$user_id'",$db);
  //transfer mitloggen
  $datum=date("Y-m-d H:i:s",time());
  $ip=getenv("REMOTE_ADDR");
  $clog="Zeit: $datum\n".$credits.' Credit(s) wurden durch das Billing-System gutgeschrieben'."\n--------------------------------------\n";
  $fp=fopen("cache/creditlogs/$user_id.txt", "a");
  fputs($fp, $clog);
  fclose($fp);    
  echo '1'; //status 1 = fertig
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//spielzeitbuchung / PA
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["getplaytime"]) && $_REQUEST["getplaytime"]==1)
{
  $id=intval($_REQUEST["id"]);
  $tage=intval($_REQUEST["tage"]);
  if($id==0)$id='-1';
  //schauen ob es den account gibt
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id='$id'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)
  {
    //falls ja user id auslesen und die pa-zeit gutschreiben
    $row = mysql_fetch_array($db_daten);
    $user_id=$row["user_id"];
    
    //aktuelle laufzeit auslesen
    $db_daten=mysql_query("SELECT patime FROM de_user_data WHERE user_id='$user_id'",$db);
    $row = mysql_fetch_array($db_daten);
    $palaufzeit=$row["patime"];
    
    if ($palaufzeit<time())
    {
      //er hat aktuell keinen pa, also neue gesamtlaufzeit setzen
      $patime=time()+(3600*24*$tage);
      mysql_query("UPDATE de_user_data SET premium=1, patime='$patime' WHERE user_id = '$user_id'",$db);
    }
    else
    {
      //er hat einen pa, also zeit dazuaddieren
      $patime=(3600*24*$tage);
      mysql_query("UPDATE de_user_data SET patime=patime+'$patime' WHERE user_id = '$user_id'",$db);
    }
    //transfer mitloggen
	$datum=date("Y-m-d H:i:s",time());
  	$ip=getenv("REMOTE_ADDR");
  	$clog="Zeit: $datum\n".$tage.' Tage Premiumaccount wurden �ber den Hauptaccount (ID '.$id.')gebucht'."\n--------------------------------------\n";
  	$fp=fopen("cache/creditlogs/$user_id.txt", "a");
    fputs($fp, $clog);
    fclose($fp);    
    //status 1 = alles ok
    echo '1';
  }
  else echo '2'; //status 2 = fehler
}
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//einen neuen account anlegen
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($_REQUEST["createaccount"]) && $_REQUEST["createaccount"]==1){
  $id=intval($_REQUEST["id"]);
  if($id==0)$id='-1';
  $rasse=$_REQUEST["rasse"];
  $spielername=$_REQUEST["spielername"];
  $email=$_REQUEST["email"];
  $vorname=$_REQUEST["vorname"];
  $nachname=$_REQUEST["nachname"];
  $strasse=$_REQUEST["strasse"];
  $plz=$_REQUEST["plz"];
  $ort=$_REQUEST["ort"];
  $land=$_REQUEST["land"];
  $telefon=$_REQUEST["telefon"];
  $tag=$_REQUEST["tag"];
  $monat=$_REQUEST["monat"];
  $jahr=$_REQUEST["jahr"];
  $geschl=$_REQUEST["geschlecht"];
  $patime=$_REQUEST["patime"];
  $werberid=$_REQUEST['werberid'];


  //schauen ob der spielername bereits verwendet wird
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE nic='$spielername'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)die('2');
  $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE spielername='$spielername' OR nrspielername='$spielername'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)die('2');

  //schauen ob die e-mail bereits verwendet wird
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE reg_mail='$email'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1)die('3');
  
  //schauen ob die id bereits vergeben ist, bzw. g�ltig
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id='$id'",$db);
  $num = mysql_num_rows($db_daten);
  if($num==1 AND $id>0)die('4');

  //wenn soweit alles ok ist den account anlegen
  //de_login
  //wenn efta/sou allein steht, dann ist der account direkt aktiv
  //if($sv_efta_in_de==1 AND $sv_sou_in_de==1)$status=0;else $status=1;

  //der Account ist jetzt immer direkt aktiv
  $status=1;

  $sql="INSERT INTO de_login (owner_id, nic, reg_mail, register, last_login, status)
  VALUES ('$id', '$spielername', '$email', NOW(), NOW(), '$status')";
  mysql_query($sql, $db);

  $user_id=mysql_insert_id();
  
  if($patime>time())$premium=1; else $premium=0;
  
  //de_user_data
  $sql="INSERT INTO de_user_data (user_id, spielername, tick, restyp01, restyp02, techs,
    sector, `system`, rasse, nrspielername, nrrasse, ovopt, hide_secpics, premium, patime, werberid)
    VALUES ($user_id , '$spielername' , 1, 100000, 50000, 's0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',
    0, 0, '$rasse', '$spielername', '$rasse', '1;2;3;4;5;6;7','0', '$premium', '$patime', '$werberid')";
    
  mysql_query($sql, $db);
  //de_user_data fix f�r ekey in der dedv-version
  mysql_query("UPDATE de_user_data SET ekey='100;0;0;0' WHERE user_id='$user_id';", $db);

  //de_user_info
  mysql_query("INSERT INTO de_user_info (user_id, vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar, ud_all, ud_sector, ud_ally)
    VALUES ('$user_id', '$vorname', '$nachname', '$strasse', '$plz', '$ort', '$land', '$telefon', '$tag', '$monat', '$jahr', '$geschl', '', '', '', '')", $db);

  if($sv_efta_in_de==1 AND $sv_sou_in_de==1)
  {
    //de_user_fleet
    $fleet_id=$user_id.'-0';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);

    $fleet_id=$user_id.'-1';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);
    $fleet_id=$user_id.'-2';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);

    $fleet_id=$user_id.'-3';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);
   
    $time=strftime("%Y%m%d%H%M%S");
    /* Begrüßungs HFN Start*/
    mysql_query("update de_user_data set newtrans=1 where user_id=$user_id");
    $body=str_replace("{SPIELERNAME}", $spielername , $rpc_lang['begrbody']);
    mysql_query("INSERT INTO de_user_hyper (empfaenger,absender,fromsec,fromsys,fromnic,time,betreff,text,archiv,sender,gelesen)VALUES ('$user_id', '0', '0', '1', '$rpc_lang[begrabs]', '$time' , '$rpc_lang[begrbetreff]','$body', '0', '0', '0')");
    mysql_query("INSERT INTO de_user_hyper (empfaenger,absender,fromsec,fromsys,fromnic,time,betreff,text,archiv,sender,gelesen)VALUES ('$user_id', '0', '0', '1', '$rpc_lang[begrabs]', '$time' , '$rpc_lang[begrbetreff]','$body', '1', '0', '0')");
    /* Begrüßungs HFN Ende */


    //späteinsteigerhilfe, gilt nicht in der ewigen runde
    if($sv_ewige_runde!=1){
      //zuerst schauen wieviel ticks bereits vergangen sind
      //$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
      $db_daten  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
      $row = mysql_fetch_array($db_daten);
      $maxtick=$row["tick"];
      if($maxtick>1){
        //fix f�r br
        if($maxtick>40000)$maxtick=40000;
        //rohstoffe berechnen
        $m=round($sv_plan_grundertrag[0]*($maxtick-1)/5);
        $d=round($sv_plan_grundertrag[1]*($maxtick-1)/5);
        //rohstoffe gutschreiben und nachrichten auf new setzen
        mysql_query("UPDATE de_user_data SET restyp01 = restyp01 + '$m', restyp02 = restyp02 + '$d', newnews=1 WHERE user_id='$user_id'",$db);
        //nachricht hinterlegen
        $nachricht=$rpc_lang['spaet1'].number_format($maxtick, 0,"",".").$rpc_lang['spaet2'].
        number_format($m, 0,"",".").$rpc_lang['spaet3'].number_format($d, 0,"",".").$rpc_lang['spaet4'];
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$user_id', 3,'$time','$nachricht')",$db);
      }
	  }
  }

  echo '1';
}
?>
