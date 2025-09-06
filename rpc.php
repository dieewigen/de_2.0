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
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.owner_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.owner_id>0 AND de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1 ORDER BY score DESC", []);
	
	$anzspieler = mysqli_num_rows($db_daten);
	//alle aktiven spieler erhalten punkte
	while($row = mysqli_fetch_array($db_daten)){
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
	//$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector>1 AND npc=0", []);
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.user_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0 AND de_user_data.sector>1", []);

	$num = mysqli_num_rows($db_daten);
	echo $num;
}

//feststellen ob es einen account mit der owner_id gibt
if(isset($_REQUEST["isaccount"]) && $_REQUEST["isaccount"]==1){
	$id=intval($_REQUEST["id"]);
	if($id==0)$id='-1';
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE owner_id=?", [$id]);
	$num = mysqli_num_rows($db_daten);
	if($num==1){
		$row = mysqli_fetch_array($db_daten);
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
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=?", [$id]);
  $num = mysqli_num_rows($db_daten);
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
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.user_id, de_login.supporter, de_login.last_login, de_login.delmode, de_login.status AS astatus, de_user_data.spielername, de_user_data.tick, de_user_data.score, de_login.status AS lstatus, de_login.last_login, de_user_data.sector, de_user_data.`system`, de_user_data.allytag, de_user_data.status, de_user_data.credits, de_user_data.patime FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_login.owner_id=?", [$id]);
  $num = mysqli_num_rows($db_daten);
  if($num==1){
    $row = mysqli_fetch_array($db_daten);
    $user_id=$row["user_id"];
    $accstatus=$row["astatus"];
    $delmode=$row["delmode"];
    $last_login=$row['last_login'];
    $last_login=strtotime($last_login);
    $last_login=date("d.m.Y - G:i", $last_login);
    $spielername=$row["spielername"];
    
    $supporter=$row['supporter'];
    if($supporter=='')$supporter='support@die-ewigen.com';
            
    if($row["status"]==1 AND $row["allytag"]!='')$allytag=$row["allytag"];else $allytag='-';
    echo $user_id.';'.$accstatus.';'.$spielername.';'.number_format($row["score"], 0,"",".").';'.number_format($row["tick"], 0,"",".").';'.
    $allytag.';'.$row["credits"].';'.$row["patime"];
    //spielerbeschreibung
    echo ';';
    echo $rpc_lang['serverspielerid'].': '.$sv_server_tag.$user_id.'<br>';
    echo $rpc_lang['spielername'].': '.urlencode($spielername).'<br>'; 
    echo $rpc_lang['punkte'].': '.number_format($row["score"], 0,"",".").'<br>';
    echo $rpc_lang['accountalter'].': '.number_format($row["tick"], 0,"",".").'<br>';
    echo $rpc_lang['allianz'].': '.urlencode($allytag).'<br>';
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
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE owner_id=?", [$id]);
  $num = mysqli_num_rows($db_daten);
  if($num==1)
  {
    //falls ja user id auslesen und per zufall den key generieren
    $row = mysqli_fetch_array($db_daten);
    $user_id=$row["user_id"];
    //key generieren
    $pwstring='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $loginkey=$pwstring[rand(0, strlen($pwstring)-1)];
    for($i=1; $i<16; $i++) $loginkey.=$pwstring[rand(0, strlen($pwstring)-1)];
    //den key und den zeitpunkt der generierung in der datenbank hinterlegen
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET loginkey=?, loginkeytime=UNIX_TIMESTAMP( ), loginkeyip=?, pass=? WHERE owner_id = ?", [$loginkey, $ip, $pass, $id]);
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
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE owner_id=?", [$id]);
	$num = mysqli_num_rows($db_daten);
	if($num==1){
		//falls ja user id auslesen und die credits gutschreiben
		$row = mysqli_fetch_array($db_daten);
		$user_id=$row["user_id"];
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET credits=credits+?, credittransfer=credittransfer+? WHERE user_id = ?", [$credits, $credits, $user_id]);
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
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET credits=credits+? WHERE user_id = ?", [$credits, $user_id]);
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
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE owner_id=?", [$id]);
  $num = mysqli_num_rows($db_daten);
  if($num==1)
  {
    //falls ja user id auslesen und die pa-zeit gutschreiben
    $row = mysqli_fetch_array($db_daten);
    $user_id=$row["user_id"];
    
    //aktuelle laufzeit auslesen
    $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT patime FROM de_user_data WHERE user_id=?", [$user_id]);
    $row = mysqli_fetch_array($db_daten);
    $palaufzeit=$row["patime"];
    
    if ($palaufzeit<time())
    {
      //er hat aktuell keinen pa, also neue gesamtlaufzeit setzen
      $patime=time()+(3600*24*$tage);
      mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET premium=1, patime=? WHERE user_id = ?", [$patime, $user_id]);
    }
    else
    {
      //er hat einen pa, also zeit dazuaddieren
      $patime=(3600*24*$tage);
      mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET patime=patime+? WHERE user_id = ?", [$patime, $user_id]);
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
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE nic=?", [$spielername]);
  $num = mysqli_num_rows($db_daten);
  if($num==1)die('2');
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE spielername=? OR nrspielername=?", [$spielername, $spielername]);
  $num = mysqli_num_rows($db_daten);
  if($num==1)die('2');

  //schauen ob die e-mail bereits verwendet wird
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE reg_mail=?", [$email]);
  $num = mysqli_num_rows($db_daten);
  if($num==1)die('3');
  
  //schauen ob die id bereits vergeben ist, bzw. g�ltig
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE owner_id=?", [$id]);
  $num = mysqli_num_rows($db_daten);
  if($num==1 AND $id>0)die('4');

  //wenn soweit alles ok ist den account anlegen
  //de_login
  //der Account ist jetzt immer direkt aktiv
  $status=1;

  $sql="INSERT INTO de_login (owner_id, nic, reg_mail, register, last_login, status)
  VALUES (?, ?, ?, NOW(), NOW(), ?)";
  mysqli_execute_query($GLOBALS['dbi'], $sql, [$id, $spielername, $email, $status]);

  $user_id=mysqli_insert_id($GLOBALS['dbi']);
  
  $premium=0;
  
  //de_user_data
  $sql="INSERT INTO de_user_data (user_id, spielername, tick, restyp01, restyp02, techs,
    sector, `system`, rasse, nrspielername, nrrasse, ovopt, hide_secpics, premium, patime, werberid)
    VALUES (?, ?, 1, 100000, 50000, 's0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',
    0, 0, ?, ?, ?, '1;2;3;4;5;6;7','0', ?, ?, ?)";
    
  mysqli_execute_query($GLOBALS['dbi'], $sql, [$user_id, $spielername, $rasse, $spielername, $rasse, $premium, $patime, $werberid]);
  //de_user_data fix f�r ekey in der dedv-version
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET ekey='100;0;0;0' WHERE user_id=?", [$user_id]);

  //de_user_info
  mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_info (user_id, vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht, kommentar, ud_all, ud_sector, ud_ally)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', '', '', '')", [$user_id, $vorname, $nachname, $strasse, $plz, $ort, $land, $telefon, $tag, $monat, $jahr, $geschl]);

  //de_user_fleet
  $fleet_id=$user_id.'-0';
  mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);

  $fleet_id=$user_id.'-1';
  mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);
  $fleet_id=$user_id.'-2';
  mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);

  $fleet_id=$user_id.'-3';
  mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);
  
  $time=strftime("%Y%m%d%H%M%S");
  //späteinsteigerhilfe, gilt nicht in der ewigen runde
  if($sv_ewige_runde!=1){
    //zuerst schauen wieviel ticks bereits vergangen sind
    $db_daten  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
    $row = mysqli_fetch_array($db_daten);
    $maxtick=$row["tick"];
    if($maxtick>1){
      //fix f�r br
      if($maxtick>40000)$maxtick=40000;
      //rohstoffe berechnen
      $m=round($sv_plan_grundertrag[0]*($maxtick-1)/5);
      $d=round($sv_plan_grundertrag[1]*($maxtick-1)/5);
      //rohstoffe gutschreiben und nachrichten auf new setzen
      mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, newnews=1 WHERE user_id=?", [$m, $d, $user_id]);
      //nachricht hinterlegen
      $nachricht=$rpc_lang['spaet1'].number_format($maxtick, 0,"",".").$rpc_lang['spaet2'].
      number_format($m, 0,"",".").$rpc_lang['spaet3'].number_format($d, 0,"",".").$rpc_lang['spaet4'];
      mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$user_id, $time, $nachricht]);
    }
  }


  echo '1';
}
?>
