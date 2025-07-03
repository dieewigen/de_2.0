<?php
include "inc/header.inc.php";
include "inc/artefakt.inc.php";
include "format_sammlung.php";
include 'inc/lang/'.$sv_server_lang.'_politics.lang.php';
include_once "functions.php";
include "issectork.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04,  restyp05, techs, sector, `system`, score, newtrans, newnews, secmoves, secatt FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$secmoves=$row["secmoves"];$secatt=$row["secatt"];

$db_daten=mysql_query("SELECT name, url, bk, skmes, techs, ssteuer, e1, e2, pass, votecounter, col, ekey FROM de_sector WHERE sec_id='$sector'",$db);
$row = mysql_fetch_array($db_daten);
$url=$row["url"];$name=$row["name"];$bk=$row["bk"];$stext=$row["skmes"];
$sectechs=$row["techs"];
$ssteuer=$row["ssteuer"];
$dbsecpass=$row["pass"];
$secfleet=$row["e1"]+$row["e2"];
$votecounter=$row["votecounter"];
$seccol=$row["col"];
$secekey=$row["ekey"];


//maximalen tick auslesen
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//anzahl der spieler im sektor auslesen
$result  = mysql_query("SELECT COUNT(*) AS wert FROM de_user_data WHERE sector='$sector'",$db);
$row     = mysql_fetch_array($result);
$spielerimsektor = $row['wert'];

?>
<!doctype html>
<html>
<head>
<title><?php echo $politics_lang["politik"]?></title>
<?php include "cssinclude.php"; ?>
<?php

if($system==issectorcommander()){
?>
<script language="JavaScript">
<!--
function checklaenge()
{
if(document.getElementById("newstext").value.length<=4000)
{
return true;
}
else
{
alert("<?php echo $politics_lang["msg_1"]?>");
return false;
}
}
function check()
{
 if(document.getElementById("newstext").value.length>=4000)
 {
 alert("<?php echo $politics_lang["msg_2"]?>");
 }
 else
 {
 alert("<?php echo $politics_lang["msg_3_1"]?> " + document.getElementById("newstext").value.length + " <?php echo $politics_lang["msg_3_2"]?> "+ (4000 - document.getElementById("newstext").value.length) +" <?php echo $politics_lang["msg_3_3"]?>");
 }
}

function leeren() {(document.getElementById("newstext").value) = "";document.getElementById("newstext").focus();}

function hilfe()
{window.open("hfnlegende.php","BitteBeachten","width=572,height=314,left=34,top=75");}

function cursor()
{
if ((navigator.appName=="Netscape")||(navigator.userAgent.indexOf("Opera") != -1)||(navigator.userAgent.indexOf("Netscape") != -1)) {
text_before = document.getElementById("newstext").value;
text_after = "";
} else {
document.getElementById("newstext").focus();
var sel = document.selection.createRange();
sel.collapse();
var sel_before = sel.duplicate();
var sel_after = sel.duplicate();
sel.moveToElementText(document.getElementById("newstext"));
sel_before.setEndPoint("StartToStart",sel);
sel_after.setEndPoint("EndToEnd",sel);
text_before = sel_before.text;
text_after = sel_after.text;
}
}
function insert(AddCode) {
cursor();
document.getElementById("newstext").value = text_before + AddCode + text_after;
document.getElementById("newstext").focus();
}

function init(thisCode) {
with ( document.getElementById("newstext").value ) {
switch(thisCode) {

case "fett":
insert("[b] [/b]");
break;

case "kursiv":
insert("[i] [/i]");
break;

case "under":
insert("[u] [/u]");
break;

case "center":
insert("[center] [/center]");
break;

case "mail":
insert("[email] [/email]");
break;

case "www":
insert("[url] [/url]");
break;

case "img":
insert("[img] [/img]");
break;

case "pre":
insert("[pre] [/pre]");
break;

case "rot":
insert("[CROT]");
break;

case "gelb":
insert("[CGELB]");
break;

case "gruen":
insert("[CGRUEN]");
break;

case "blau":
insert("[CDE]");
break;

case "weiss":
insert("[CW]");
break;

case "farbe":
insert("[color=#] [/color]");
break;

case "size":
insert("[size=] [/size]");
break;

case "smile1":
insert(":)");
break;

case "smile2":
insert(":D");
break;

case "smile3":
insert(";)");
break;

case "smile4":
insert(":x");
break;

case "smile5":
insert(":(");
break;

case "smile6":
insert("x(");
break;

case "smile7":
insert(":p");
break;

case "smile8":
insert("(?)");
break;

case "smile9":
insert("(!)");
break;

case "smile10":
insert(":{");
break;

case "smile11":
insert(":}");
break;

case "smile12":
insert(":L");
break;

case "smile13":
insert(":nene:");
break;

case "smile14":
insert(":eek:");
break;

case "smile15":
insert(":applaus:");
break;

case "smile16":
insert(":cry:");
break;

case "smile17":
insert(":sleep:");
break;

case "smile18":
insert(":rolleyes:");
break;

case "smile19":
insert(":wand:");
break;

case "smile20":
insert(":dead:");
break;
}
document.getElementById("newstext").focus();
}
}
//-->
</script>
<?php
}
?>
</head>
<body>
<?php

//stelle die ressourcenleiste dar

include "resline.php";
echo '<form action="politics.php" method="post">';
include('outputlib.php');

//SK - sekstat sichtbar
if(isset($_REQUEST["do"]) && $_REQUEST["do"]==2 AND $system==issectorcommander()){
	
  $sys=intval($_REQUEST["sys"]);
  //daten des spielers auslesen
  $db_daten=mysql_query("SELECT spielername, secstatdisable FROM de_user_data WHERE sector='$sector' AND `system`='$sys'",$db);
  if(mysql_num_rows($db_daten)==1)
  {
    $row = mysql_fetch_array($db_daten);
    $spielername=$row["spielername"];
    $secstatdisable=$row["secstatdisable"];
    if($secstatdisable==0)$secstatdisable=1;
    elseif($secstatdisable==1)$secstatdisable=0;
    mysql_query("UPDATE de_user_data SET secstatdisable='$secstatdisable' WHERE sector='$sector' AND `system`='$sys'",$db);
    //info in die sektorhistorie packen - komplette spielerlöschung
    if($secstatdisable==0)mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '5', '$sector', '$spielername');",$db);
    if($secstatdisable==1)mysql_query("INSERT INTO de_news_sector(wt, typ, sector, text) VALUES ('$maxtick', '6', '$sector', '$spielername');",$db);
  }
}


//für/gegen secatt stimmen
/*
if($_REQUEST["do"]==1)
{
  $secatt=intval($_REQUEST["secatt"]);
  if($secatt<0 OR $secatt>1)$secatt=0;
  mysql_query("UPDATE de_user_data SET secatt='$secatt' WHERE user_id='$ums_user_id'");
}
*/

//für einen sk stimmen
$letsgo=isset($_POST['letsgo']) ? $_POST['letsgo'] : '';
if(!empty($letsgo) && $sector>1){
	$userslist=intval($_POST['userslist']);
	mysql_query("UPDATE de_user_data SET votefor='$userslist' WHERE user_id='$ums_user_id'");
}

//einen neuen sektor beantragen
$getnewsec=isset($_REQUEST['getnewsec']) ? getnewsec : '';
if(!empty($getnewsec) && $secmoves<$sv_max_secmoves && $techs[26]=='1'){
	//abfrage da die funktion  zum erstellen nur noch f�r premium accounts zul�ssig ist
	if ($ums_premium>0 OR 1==1){
		//schauen ob derjenige schon einen sektor beantragt hat
		$result1 = mysql_query("SELECT user_id FROM de_sector_umzug WHERE user_id='$ums_user_id'",$db);
		$anz1 = mysql_num_rows($result1);
		if ($anz1==0)//es l�uft noch kein umzug
		{
			//pw generieren und umzug in der db eintragen
			//neues pw generieren
			$ok=0;
			while ($ok==0){
				$newpass='';
				$pwstring='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				$newpass=$pwstring[rand(0, strlen($pwstring)-1)];
				for($i=1; $i<=10; $i++) $newpass.=$pwstring[rand(0, strlen($pwstring)-1)];

				$result1 = mysql_query("SELECT user_id FROM de_sector_umzug WHERE pass='$newpass'",$db);
				$result2 = mysql_query("SELECT sec_id FROM de_sector WHERE pass='$newpass'",$db);

				if (mysql_num_rows($result1)==0 AND mysql_num_rows($result2)==0) $ok=1;
			}
			//eintrag in der db machen, dass er umzieht
			mysql_query("INSERT de_sector_umzug set user_id='$ums_user_id', typ=1, sector=0, `system`=0,pass='$newpass', ticks=192",$db);
		}
	}
	else echo '<br><font color="#FF0000"><b>'.$politics_lang["msg_4"].'<br><br></b></font>';
}

//einem bestehenden sektor joinen
$joinsec= isset($_REQUEST['joinsec']) ? $_REQUEST['joinsec'] : '';
if(!empty($joinsec) && $secmoves<$sv_max_secmoves && $secpass!='' && $techs[26]=='1'){
  //es gibt 2 m�glichkeiten zu joinen
  //1. es gibt den sektor schon
  //2. der sektor wird beantragt
  //art des joinens festellen

  //anzahl der leute die in den sektor ziehen wollen
  $result1 = mysql_query("SELECT user_id FROM de_sector_umzug WHERE pass='$secpass'",$db);
  $anz1=mysql_num_rows($result1);

  //gibt es den sektor schon?
  $result2 = mysql_query("SELECT sec_id FROM de_sector WHERE pass='$secpass'",$db);
  $anz2=mysql_num_rows($result2);

  //hat man evtl. schon was am laufen
  $result3 = mysql_query("SELECT user_id FROM de_sector_umzug WHERE user_id='$ums_user_id'",$db);
  $anz3 = mysql_num_rows($result3);

  if ($anz2>0 AND $anz3==0) //der sektor besteht schon, direkt reinmoven typ 2
  {
    //schauen ob noch platz im sektor ist
    $row2 = mysql_fetch_array($result2);
    $zielsec=$row2["sec_id"];
    $result = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zielsec'",$db);
    $accanz=mysql_num_rows($result);
    $result = mysql_query("SELECT user_id FROM de_sector_umzug WHERE typ=2 AND sector='$zielsec'",$db);
    $accanz+=mysql_num_rows($result);

    if($accanz<$sv_max_user_per_regsector)
    {
      //es ist noch ein platz frei -> spieler zieht um
      $time=strftime("%Y%m%d%H%M%S");
      //account sperren
      mysql_query("UPDATE de_login set status = 4 where user_id = '$ums_user_id'",$db);
      //eintrag in der db machen, dass er umzieht
      mysql_query("INSERT de_sector_umzug set user_id='$ums_user_id', typ=2, sector='$zielsec'",$db);
      //nachricht an den account schicken
      mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$ums_user_id', 3,'$time','".$politics_lang["msg_25"]."')",$db);
      mysql_query("update de_user_data set newnews = 1 where user_id = '$ums_user_id'",$db);
      echo die($politics_lang["msg_5"]);

    }
    else echo '<br><font color="FF0000">'.$politics_lang["msg_6"].'</font><br><br>';


  }
  elseif ($anz1>0 AND $anz2==0 AND $anz3==0) //ein weiterer spieler der sich der beantragung anschlie�t -> typ 1
  {
    //schauen ob noch platz im sektor ist
    if($anz1<$sv_max_user_per_regsector)
    {
      //es ist noch ein platz frei -> spieler zieht um
      //eintrag in der db machen, dass er umzieht
      mysql_query("INSERT de_sector_umzug set user_id='$ums_user_id', typ=1, pass='$secpass', ticks=192",$db);
    }
    else echo '<br><font color="FF0000">'.$politics_lang["msg_6"].'</font><br><br>';
  }
  else echo '<br><font color="FF0000">'.$politics_lang["msg_7"].'</font><br><br>';
}


//laufende sektorbeantragung löschen
$cancelgetnewsec=isset($_POST['cancelgetnewsec']) ? $_POST['cancelgetnewsec'] : '';
if(!empty($cancelgetnewsec)){
	mysql_query("DELETE FROM de_sector_umzug WHERE user_id='$ums_user_id' AND typ=1",$db);
}


$voteoutcancel=isset($_POST['voteoutcancel']) ? $_POST['voteoutcancel'] : '';
if(!empty($voteoutcancel) && $system==issectorcommander()){
  mysql_query("DELETE FROM de_sector_voteout where sector_id='$sector'");
  $time=strftime("%Y%m%d%H%M%S");
  //nachrichten an alle spieler schicken
  $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector'",$db);
  while($row = mysql_fetch_array($db_daten))
  {
    mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($row[user_id], 3,'$time','".$politics_lang["msg_26"]."')",$db);
    mysql_query("update de_user_data set newnews = 1 where user_id = $row[user_id]",$db);
  }
}

//ein spieler gibt seine stimme zum rausvoten ab
$setvoteout=isset($_POST['setvoteout']) ? $_POST['setvoteout'] : '';
$vspielerwahl=isset($_POST['vspielerwahl']) ? $_POST['vspielerwahl'] : '';
if (!empty($setvoteout) && ($vspielerwahl==$politics_lang["ja"] || $vspielerwahl==$politics_lang["nein"] || $vspielerwahl==$politics_lang["egal"])){
  //erstmal schauen ob ein vote l�uft
  $result1 = mysql_query("SELECT user_id, votes FROM de_sector_voteout WHERE sector_id='$sector'",$db);
  $anz1 = mysql_num_rows($result1);
  if ($anz1!=0){
    $row=mysql_fetch_array($result1);
    $vvotes=$row["votes"];
    $vuser_id=$row["user_id"];
    //wie hat der spieler abgestimmt?
    if ($vspielerwahl==$politics_lang["nein"]) $vsvote=0;
    elseif ($vspielerwahl==$politics_lang["ja"]) $vsvote=1;
    elseif ($vspielerwahl==$politics_lang["egal"]) $vsvote=2;
    //stimme z�hlen
    $vvotes[$system]=$vsvote;
    //schaue ob evtl. schon das ziel des votes erreicht ist
    $jastimmen=0;
    for ($i=1; $i<=($sv_maxsystem+5); $i++)if ($vvotes[$i]=='1')$jastimmen++;
    //anzahl der aktiven spieler im sektor bestimmen
    //$result1 = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector'",$db);
    $result1 = mysql_query("SELECT de_user_data.`system` FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) 
    WHERE de_user_data.sector='$sector' AND de_login.status=1",$db);
    $anz1 = mysql_num_rows($result1);
    $prozentwert=($jastimmen*100)/$anz1;
    if ($prozentwert>=$sv_voteoutgrenze){
      $result1 = mysql_query("SELECT `system` FROM de_user_data WHERE user_id='$vuser_id'",$db);
      $row1=mysql_fetch_array($result1);
      $vsystem=$row1["system"];
      $time=strftime("%Y%m%d%H%M%S");
      //spieler wurde rausgevotet

      //gesperrte user und leute im umode kommen direkt in sektor 1
      //dazu erstmal accountstatus auslesen
      $result1 = mysql_query("SELECT status FROM de_login WHERE user_id='$vuser_id'",$db);
      $row1=mysql_fetch_array($result1);
      $accstatus=$row1["status"];
      if($accstatus==2 OR $accstatus==3){
		//nachricht an den account schicken
		mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$vuser_id', 3,'$time','".$politics_lang["msg_25"]."')",$db);
		
		//auf 0:0 verschieben damit er in sektor 1 landet und einstellungen updaten
		mysql_query("UPDATE de_user_data SET sector=0, `system`=0, newnews=1, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, votefor=0, secstatdisable=0 WHERE user_id = '$vuser_id'",$db);
		
		//voteumfrage aus der db l�schen
		mysql_query("DELETE FROM de_sector_voteout WHERE sector_id='$sector'",$db);
		//nachricht an alle im sektor schicken
		$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector'",$db);
		while($row = mysql_fetch_array($db_daten))
		{
			mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($row[user_id], 3,'$time','".$politics_lang["msg_27"]."')",$db);
			mysql_query("update de_user_data set newnews = 1 where user_id = $row[user_id]",$db);
		}
		
		//wenn er BK ist, den Posten auf 0 setzen
		mysql_query("UPDATE de_sector SET bk=0 WHERE sec_id='$sector' AND bk='$vsystem'",$db);
		
		//votetimer/votecounter f�r den sektor setzen
		mt_srand((double)microtime()*10000);
		$votetimer=mt_rand(20,120);
		//$votetimer=0;
		$sv_sector_votetime_lock=0;
		//if($accstatus!=2)
		mysql_query("UPDATE de_sector SET votetimer='$votetimer', votecounter='$sv_sector_votetime_lock' WHERE sec_id='$sector'",$db);

      }else{
        //accountstatus sichern und account sperren
        mysql_query("UPDATE de_login set savestatus=status where user_id = '$vuser_id'",$db);
        mysql_query("UPDATE de_login set status = 4 where user_id = '$vuser_id'",$db);
        mysql_query("UPDATE de_user_data SET spend01=0, spend02=0, spend03=0, spend04=0, spend05=0 WHERE user_id = '$vuser_id'",$db);
        //eintrag in der db machen, dass er umzieht
        mysql_query("INSERT de_sector_umzug set user_id='$vuser_id', typ=0, sector='$sector', `system`='$vsystem'",$db);
        //nachricht an den account schicken
        mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$vuser_id', 3,'$time','".$politics_lang["msg_25"]."')",$db);
        mysql_query("update de_user_data set newnews = 1 where user_id = '$vuser_id'",$db);
        //voteumfrage aus der db l�schen
        mysql_query("DELETE FROM de_sector_voteout WHERE sector_id='$sector'",$db);
        //nachricht an alle im sektor schicken
        $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector'",$db);
        while($row = mysql_fetch_array($db_daten))
        {
          mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($row[user_id], 3,'$time','".$politics_lang["msg_27"]."')",$db);
          mysql_query("update de_user_data set newnews = 1 where user_id = $row[user_id]",$db);
        }
        //votetimer/votecounter f�r den sektor setzen
        mt_srand((double)microtime()*10000);
        $votetimer=mt_rand(16,96);
        //$votetimer=0;
        //$sv_sector_votetime_lock=0;
        mysql_query("UPDATE de_sector SET votetimer='$votetimer', votecounter='$sv_sector_votetime_lock' WHERE sec_id='$sector'",$db);
      }
    }
    else
    {
      //spieler wurde noch nicht rausgevotet
      //db mit den stimmen updaten
      mysql_query("UPDATE de_sector_voteout set votes = '$vvotes' where sector_id = '$sector'",$db);
    }
   //echo $prozentwert;
  }
}

//startet ein rausvoten
$voteout=isset($_POST['voteout']) ? $_POST['voteout'] : '';
$voteoutlist=isset($_POST['voteoutlist']) ? $_POST['voteoutlist'] : '';
if(!empty($voteout) && $system==issectorcommander() && $sector > 1){
  //beim reinsetzen des votes erstmal schauen ob nicht schon eins existiert
  $result1 = mysql_query("SELECT sector_id FROM de_sector_voteout WHERE sector_id='$sector'",$db);
  $anz1 = mysql_num_rows($result1);
  
  //anhand des spielernamens die user_id und den sector rausfinden
  $result2 = mysql_query("SELECT user_id, sector FROM de_user_data WHERE spielername='$voteoutlist'",$db);
  $row = mysql_fetch_array($result2);
  //id des zu votenden auslesen
  $vuser_id=$row["user_id"];
  //schaue ob der spieler auch wirklich in dem sektor ist
  $vsector=$row["sector"];
  $anz2 = mysql_num_rows($result2);
  
  //�berpr�fen ob der spieler gesperrt ist, falls ja ist es kostenlos und es gibt keinen counter
  $db_daten=mysql_query("SELECT status, delmode FROM de_login WHERE user_id='$vuser_id'",$db);
  $row = mysql_fetch_array($db_daten);
  if($row["status"]==2){$gesperrt=1;$votecounter=0;}else $gesperrt=0;
  
  //�berpr�fen ob er l�nger als 7 tage offline war
  
  
/*  
  //schauen ob genug rohstoffe vorhanden sind
  $db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_sector WHERE sec_id='$sector'",$db);
  $row = mysql_fetch_array($db_daten);
  $srestyp01=$row[0];$srestyp02=$row[1];$srestyp03=$row[2];$srestyp04=$row[3];
  $srestyp05=$row[4];
  if (($srestyp01>=100000 AND $srestyp02>=100000 AND $srestyp03>=100000 AND $srestyp04>=100000 AND $srestyp05>=10) OR $gesperrt==1)
*/
  if ($gesperrt==1)
  {
    if($votecounter==0)
    {
  	  if ($anz1==0 AND $anz2>0)
      {
        //$row = mysql_fetch_array($result2);
        
        if ($vsector!=$sector) die($politics_lang["msg_8"]);
        $vticks=96*3;
        //votes erstmal auf 3 setzen, d.h. noch nicht gew�hlt, daf�r 1 oder dagegen 0, 2 egal
        $vvotes='s';
        for ($i=1; $i<=($sv_maxsystem+5); $i++) $vvotes.='3';

        //eintrag in die db packen
        mysql_query("INSERT INTO de_sector_voteout SET sector_id='".$sector."',user_id='".$vuser_id."',votes='".$vvotes."',ticks='".$vticks."'");
        //rohstoffe vom sektor abziehen
        //if($gesperrt==0)
        //mysql_query("update de_sector set restyp01 = restyp01 - 100000, restyp02 = restyp02 - 100000, restyp03 = restyp03 - 100000, restyp04 = restyp04 - 100000, restyp05 = restyp05 - 10 WHERE sec_id = '$sector'",$db);
        //nachricht an alle im sektor schicken, dass es ein vote gibt
        $time=strftime("%Y%m%d%H%M%S");
        $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector'",$db);
        while($row = mysql_fetch_array($db_daten))
        {
          mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($row[user_id], 3,'$time','".$politics_lang["msg_24_1"]." $voteoutlist ".$politics_lang["msg_24_2"]."')",$db);
          mysql_query("update de_user_data set newnews = 1 where user_id = $row[user_id]",$db);
        }
      }
    }
  }
  //else echo '<br><font color="FF0000">'.$politics_lang["msg_9"].'</font><br>';
  else echo '<br><table width=600><tr><td class="ccr">'.$politics_lang["votedescription"].'</td></tr></table><br>';
  //$userlist=(int)$userlist;
  //mysql_query("UPDATE de_user_data SET votefor='$userslist' WHERE user_id='$ums_user_id'");
}

$sec_btn=isset($_POST['sec_btn']) ? $_POST['sec_btn'] : '';
//$newurl=isset($_POST['newurl']) ? $_POST['newurl'] : '';
$newname=isset($_POST['newname']) ? $_POST['newname'] : '';
//$newstext=isset($_POST['newstext']) ? $_POST['newstext'] : '';
//$newbk=isset($_POST['newbk']) ? $_POST['newbk'] : '';
$seksteuer=isset($_POST['seksteuer']) ?  $_POST['seksteuer'] : 0;
if(!empty($sec_btn) && $system==issectorcommander()){
	//if (($url<>$newurl) || ($name<>$newname) || ($stext<>$newstext) || ($ssteuer<>$seksteuer)){
		if (($name<>$newname) || ($ssteuer<>$seksteuer)){

		/*
		if($url<>$newurl){
			$url = trim($newurl);
			$fp = @fopen($url,"r");
			if($fp!=false){

			}else{
				if($url!=""){
					echo '<br>'.$politics_lang["msg_10"].'<br><br>';
					$url='';
				}
			}
			$ext =  $url[strlen($url)-3];
			$ext .= $url[strlen($url)-2];
			$ext .= $url[strlen($url)-1];

			if($url!='' and $ext!='jpg' and $ext!='gif' and $ext!='png'){//falsche extension
				echo $politics_lang["msg_11"];
				$url='';
			}else{
				if($url==''){
					$url=='';
				}else{
					$imagesize = @getimagesize($url);
					if($imagesize[0]>"500" or $imagesize[1]>"500"){
						echo $politics_lang["msg_12"];
						$url='';
					}
				}
			}
		}
		*/

		$name = htmlspecialchars(stripslashes($newname), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
		
		/*
		$stext = str_replace('\r\n', "\r\n", $newstext);
		$stext = htmlspecialchars(stripslashes($stext), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
		$stext = str_replace('\"', '&quot;', $stext);
		$stext = str_replace('\'', '&acute;', $stext);
		$stext = nl2br($stext);
		*/

		//mysql_query("UPDATE de_sector set name = '$name' , url = '$url', skmes = '$stext'  WHERE sec_id='$sector'",$db);
		mysql_query("UPDATE de_sector set name = '$name' WHERE sec_id='$sector'",$db);

		if($sectechs[4]==1){
			//testen ob sektorsteuer einen wert hat, der ok ist
			if (($seksteuer>1) AND ($seksteuer<6)){
				mysql_query("UPDATE de_sector set ssteuer = '$seksteuer'  WHERE sec_id='$sector'",$db);
			}
		}
		$ssteuer = $seksteuer;
	}

	/*
	if (($newbk<>$bk) AND validDigit($newbk)) //neuer bk soll eingesetzt werden
	{

		//if($newbk==$system) echo $politics_lang["msg_13"];
		//else
		//{
		$result=mysql_query("SELECT * FROM de_user_data WHERE sector='$sector' AND system='$newbk'");
		$num=mysql_num_rows($result);
		if ($num==1) {mysql_query("UPDATE de_sector set bk = '$newbk' WHERE sec_id='$sector'",$db);$bk=$newbk;};

		//}
	}
	*/
}

//echo '<td><a href="secforum.php" class="btn">'.$politics_lang["sektorforum"].'</a></td>';

if($system==issectorcommander()){
	echo '<table border="0" cellpadding="0" cellspacing="2" width="600">';
	echo '<tr align="center">';
	echo '<td><a href="politics.php?s=1" class="btn">'.$politics_lang["allgemein"].'</a></td>';	
	echo '<td><a href="politics.php?s=2" class="btn">SK-Politik</a></td>';
	echo '<td><a href="bkmenu.php" class="btn">SK-Bau/Flotte</a></td>';
	echo '</tr>';
	echo '</table>';
	echo '<br>';	
}

/*
if($spielerimsektor>1)
{
  if($system==issectorcommander())echo '<td><a href="politics.php?s=2" class="btn">'.$politics_lang["skmenu"].'</a></td>';
  elseif($system==$bk)echo '<td><a href="bkmenu.php" class="btn">'.$politics_lang["bkmenu"].'</a></td>';
}
else  //in 1-mann-sektoren kann der sk auch der bk sein 
{
  if($system==issectorcommander())echo '<td><a href="politics.php?s=2" class="btn">'.$politics_lang["skmenu"].'</a></td>';
  if($system==$bk)echo '<td><a href="bkmenu.php" class="btn">'.$politics_lang["bkmenu"].'</a></td>';
}
*/



$s=isset($_REQUEST['s']) ? $_REQUEST['s'] : 1;

/*
if(isset($_POST['vorschau'])){
	$newstext=$_POST['newstext'];
	//$stext =str_replace('\r\n', "\r\n", $newstext);
	$newstext =str_replace('\r\n', "\r\n", $newstext);
	$newstext=htmlspecialchars($newstext, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
	$newstext = nl2br_pre($newstext);
	$newstext = formatierte_anzeige($newstext,$ums_gpfad);
	
	$newstext = preg_replace("/\[img\]([^[]*)\[\/img\]/","<img src=\"\\1\" border=0>",$newstext);
	$newstext = stripslashes($newstext);

	?>
	<table width="586" border="0" cellpadding="0" cellspacing="0" class="cell">
	<tr>
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td class="ro" align="center"><?php echo $politics_lang["vorschausknews"]?></td>
	<td width="13" height="37" class="ror">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" height="37" class="rl">&nbsp;</td>
	<td align="left"><div class="ov_skinfo"><?php echo $newstext; ?></div></td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" class="rul">&nbsp;</td>
	<td class="ru">&nbsp;</td>
	<td width="13" class="rur">&nbsp;</td>
	</tr>
	</table><br>
	<?php
	$s=2;
}
*/

if ($s==2){
	if($system==issectorcommander()) {

	/*
	<tr align="center" class="cell">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td width="550"><div class="cell"><a href="skforum.php"><?=$politics_lang["skforum"]?></a></div></td>
	<td width="13" class="rr">&nbsp;</td>
	*/

	?>
	<input type="hidden" name="s" value="2">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr align="center">
	<td width="13" height="37" class="rml">&nbsp;</td>
	<td width="550" align="center" class="ro"><div class="cellu">Sektorname</div></td>
	<td width="13" class="rmr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td width="550">
	<div class="cell">
	<?php
	echo $politics_lang["msg_14_1"].': <input type="text" name="newname" value="'.$name.'" size="40" maxlength="30"><br>';
	//echo $politics_lang["msg_14_2"].': <input type="text" name="newurl" value="'.$url.'" size="60" maxlength="250"><br>';
	echo '<br>Beachte bei der Namensvergabe bitte auf die Netiquette.<br><br>';

	?>
	</div>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>

	<?php
	/*
	<tr align="center">
	<td width="13" height="37" class="rml">&nbsp;</td>
	<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["ernennungbk"]?></div></td>
	<td width="13" class="rmr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td width="550">
	<div class="cell">
	<?php
	echo '<font size=2 face=arial><b>'.$politics_lang["bksys"].':</b><br>';
	echo '<input type="text" name="newbk" value="'.$bk.'" size="4" maxlength="2"> ('.$politics_lang["system"].' 1-'.$sv_maxsystem.')<br><br>';
	?>
	</div>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="37" class="rml">&nbsp;</td>
	<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["infofuersek"]?></div></td>
	<td width="13" class="rmr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td width="550">
	<div class="cell">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm1.gif" onclick="init('smile1')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm2.gif" onclick="init('smile2')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm3.gif" onclick="init('smile3')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm4.gif" onclick="init('smile4')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm5.gif" onclick="init('smile5')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm6.gif" onclick="init('smile6')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm7.gif" onclick="init('smile7')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm8.gif" onclick="init('smile8')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm9.gif" onclick="init('smile9')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm10.gif" onclick="init('smile10')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm11.gif" onclick="init('smile11')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm12.gif" onclick="init('smile12')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm13.gif" onclick="init('smile13')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm14.gif" onclick="init('smile14')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm15.gif" onclick="init('smile15')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm16.gif" onclick="init('smile16')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm17.gif" onclick="init('smile17')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm18.gif" onclick="init('smile18')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm19.gif" onclick="init('smile19')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<img src="<?php echo $ums_gpfad; ?>g/smilies/sm20.gif" onclick="init('smile20')" alt="<?php echo $politics_lang["altsmilie"]?>">
	<br>
	<input type="button" value="&nbsp;b&nbsp;"  onclick="init('fett')">
	<input type="button" value="&nbsp;u&nbsp;" class="button" onclick="init('under')">
	<input type="button" value="&nbsp;i&nbsp;" class="button" onclick="init('kursiv')">
	<input type="button" value="<?php echo $politics_lang["panel1"]?>" class="button" onclick="init('rot')">
	<input type="button" value="<?php echo $politics_lang["panel2"]?>" class="button" onclick="init('gelb')">
	<input type="button" value="<?php echo $politics_lang["panel3"]?>" class="button" onclick="init('gruen')">
	<input type="button" value="<?php echo $politics_lang["panel4"]?>" class="button" onclick="init('weiss')">
	<input type="button" value="<?php echo $politics_lang["panel5"]?>" class="button" onclick="init('blau')">
	<input type="button" value="<?php echo $politics_lang["panel6"]?>"  onclick="init('farbe')">
	<br>
	<input type="button" value="<?php echo $politics_lang["panel7"]?>"  onclick="init('size')">
	<input type="button" value="<?php echo $politics_lang["panel8"]?>" class="button" onclick="init('center')">
	<input type="button" value="pre" class="button"s onclick="init('pre')">
	<input type="button" value="<?php echo $politics_lang["panel9"]?>" class="button" onclick="init('www')">
	<input type="button" value="@" class="button"s onclick="init('mail')">
	<input type="button" value="<?php echo $politics_lang["panel10"]?>" class="button" onclick="init('img')">
	<input type="button" value="&nbsp;?&nbsp;" class="button" onclick="hilfe()">
	<input type="button" value="<?php echo $politics_lang["panel11"]?>" class="button" onclick="leeren()">
	<?php
	echo '<textarea name="newstext" id="newstext" cols="65" rows="20">'.str_replace("<br />", "", "$stext").'</textarea><br><input type="button"  value="'.$politics_lang["checklaenge"].'" onClick="check()">&nbsp;&nbsp;&nbsp;<input type="Submit" name="vorschau"  value="'.$politics_lang["vorschau"].'">';
	?>
	</div>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	*/
	?>
	<tr align="center">
	<td width="13" height="37" class="rml">&nbsp;</td>
	<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["sektax"]?></div></td>
	<td width="13" class="rmr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td>
	<div class="cell">
	<?php
	if($sectechs[4]==1)
	{
		echo '<br>Sektorsteuersatz <select name="seksteuer" size="1" ><option name="2"';
		if("$ssteuer"=="2")echo " selected";
		echo '>2</option><option name="3"';
		if("$ssteuer"=="3"){echo " selected";}
		echo '>3</option><option name="4"';
		if("$ssteuer"=="4"){echo " selected";}
		echo '>4</option><option name="5"';
		if("$ssteuer"=="5"){echo " selected";}
		echo '>5</option></select>%<p>';

		echo '<br>';
	}//Ende des Menus f&uuml;r die Sektorsteuer
	else echo 'F&uuml;r diese Funktion wird das Sektorhandelszentrum ben&ouml;tigt.';
	}
	?>
	</div>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	<tr align="center">
	<td width="13" height="37" class="rml">&nbsp;</td>
	<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["markvoteout"]?></div></td>
	<td width="13" class="rmr">&nbsp;</td>
	</tr>
	<tr align="center">
	<td width="13" height="25" class="rl">&nbsp;</td>
	<td width="550">
	<div class="cell">
	<?php
	//beim reinsetzen des votes erstmal schauen ob nicht schon eins existiert
	$result1 = mysql_query("SELECT sector_id, user_id, ticks FROM de_sector_voteout WHERE sector_id='$sector'",$db);
	$anz1 = mysql_num_rows($result1);
	if ($anz1==0){
		//schauen ob man zu dem zeitpunkt voten kann
		if($votecounter==0 OR 1==1){
			$result = mysql_query("SELECT de_user_data.spielername FROM de_user_data WHERE de_user_data.sector=$sector ORDER BY `system` ASC",$db);
			$anz = mysql_num_rows($result);
			echo '<br><br><select size="1" name="voteoutlist">';
			while($row=mysql_fetch_array($result))
			{
			$vspielername=$row["spielername"];

			echo '<option value="'.utf8_encode($vspielername).'">'.utf8_encode($vspielername).'</option>';
			}
			echo '</select><input type="submit" value="'.$politics_lang["startvote"].'" name="voteout" onclick="return confirm(\''.$politics_lang["warnexec"].'?\')"><br><br>';
			//kosten anzeigen
			//echo $politics_lang["kosten"];
		}
		else echo $politics_lang["msg_15_1"].' '.$votecounter.' '.$politics_lang["msg_15_2"];
	}
	else  //es l�uft bereits ein vote
	{
		//sektorvotedaten auslesen
		$row1 = mysql_fetch_array($result1);
		$vuser_id=$row1["user_id"];
		$vticks=$row1["ticks"];
		//anhand der  user_id den spielernamen rausfinden
		$result2 = mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$vuser_id'",$db);
		$anz2 = mysql_num_rows($result2);

		if ($anz2>0)//spielername gefunden
		{
			$row2 = mysql_fetch_array($result2);
			$vspielername=$row2["spielername"];
			//eintrag in die db packen
			//mysql_query("INSERT INTO de_sector_voteout SET sector_id='".$sector."',user_id='".$vuser_id."',ticks='".$vticks."'");
		}

		echo '<br>'.$politics_lang["msg_16_1"].' '.$vspielername.'. '.$politics_lang["msg_16_2"].' '.$vticks.' '.$politics_lang["msg_16_3"].'.<br><br>';
		echo '<input type="submit" value="'.$politics_lang["cancelvote"].'" name="voteoutcancel"><br><br>';
		//echo $politics_lang["noressoncancel"];
	}
	?>
	</div>
	</td>
	<td width="13" class="rr">&nbsp;</td>
	</tr>
	<tr>
	<td width="13" class="rul">&nbsp;</td>
	<td class="ru">&nbsp;</td>
	<td width="13" class="rur">&nbsp;</td>
	</tr>
	</table>
	<?php
	echo '<br><input type="Submit" name="sec_btn" value="'.$politics_lang["savesekinfos"].'" onclick="return checklaenge()"><br><br>';
	//Menu f&uuml;r die Sektorsteuer mit Abfrage ob SekHandelsZentrum vorhanden ist
}//skmenu ende

//men� f�r den sektor, wie sk-wahl und vote f�r exilanden
if ($s==1 OR !isset($s)){
echo '<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu">'.$politics_lang["wahldessk"].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>';	
echo '<td width="550">';

echo '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
$bg='cell1';
echo '<tr align="center">';
echo '<td class="'.$bg.'">';

echo '<br><font size=2 face=arial>'.$politics_lang["actualsk"].': ';
echo '<input type="hidden" name="s" value="1">';
//alle user des sektors auslesen
$result = mysql_query("SELECT de_user_data.spielername, de_user_data.votefor, de_user_data.`system` FROM de_user_data WHERE de_user_data.sector='$sector' ORDER BY `system` ASC",$db);
$anz = mysql_num_rows($result);
while($row=mysql_fetch_array($result)){
	//�berpr�fen, ob es den Account f�r den man votet noch gibt
	if($row["votefor"]!=0){
		$db_daten = mysql_query("SELECT user_id FROM de_user_data WHERE sector='$sector' AND `system`='".$row["votefor"]."' ORDER BY `system` ASC",$db);
		$anzv = mysql_num_rows($db_daten);
		if($anzv!=1){
			//wenn es den Spieler im Sektor nicht gibt, dann den Vote entfernen
			//echo $row["votefor"];
			mysql_query("UPDATE de_user_data SET votefor=0 WHERE sector='$sector' AND votefor='".$row["votefor"]."'",$db);
		}
	}
	
	$su[$row["system"]][0]=$row["spielername"];
	$su[$row["system"]][1]=$row["votefor"];
	$su[$row["system"]][2]=$row["system"];
}

$ska=array(0,0,0,0,0,0,0,0,0,0,0);
//alle stimmen z&auml;hlen
for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
	//echo 'System: '.$i.' Nic: '.$su[$i][0].' Votefor: '.$su[$i][1].'<br>';
	if(!isset($su[$i][1])){
		$su[$i][1]=0;
	}

	$ska[$su[$i][1]]++;
}

//maximalwert suchen
$mw=0;
for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
	if(isset($ska[$i]) && $ska[$i]>$mw){
		$mw=$ska[$i];
	}
}

//schauen ob wert doppelt vorhanden, wenn kleiner X
$anzahl=0;
if ($mw<8){
	for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
		if(isset($ska[$i]) && $ska[$i]==$mw){
			$anzahl++;
		}
	}
}
else $anzahl=1;


//wenn nicht 1, dann gibts mehrere mit der stimmenanzahl
if ($anzahl!=1) $mw=0;


if($mw!=0){
	//noch den namen des sk auslesen
	for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
		if(isset($ska[$i]) && $ska[$i]==$mw){
			$sksys=$i;
		}
	}
	//echo $sksys;
	for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
		if(isset($su[$i][2]) && $su[$i][2]==$sksys){
			$skname=$su[$i][0];
		}
	}

	echo '<b>'.$skname.'</b> ('.$politics_lang["stimmen"].': '.$mw.')';
}else{
	echo $politics_lang["msg_17"];
}

echo '<br><br><select size="1" name="userslist">';
for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
	if(isset($su[$i][0]) && $su[$i][0]!=''){
		echo '<option value="'.utf8_encode($su[$i][2]).'">'.utf8_encode($su[$i][0]).'</option>';
	}
}


echo '</select><input type="submit" value="'.$politics_lang["fuerspielerstimmen"].'" name="letsgo"><br><br>';
echo '</td></tr>';

if ($anz==0) exit;
$bg='cell';
echo '<tr align="center"><td class="'.$bg.'"><b>'.$politics_lang["wahlentscheidungen"].'</b></td></tr>';
//alle namen und wof�r sie gevotet haben ausgeben
$c1=0;
for ($j = 1; $j <= ($sv_maxsystem+5); $j++){

	if(isset($su[$j][0]) && $su[$j][0]!=''){

		$votename='';
		$name=$su[$j][0];
		for ($i = 1; $i <= ($sv_maxsystem+5); $i++){
			if(isset($su[$i][2]) && isset($su[$j][1]) && $su[$i][2]==$su[$j][1]){
				$votename=$su[$i][0];
			}
		}
		if ($votename=='')$votename='-';
		if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
		
		echo '<tr><td class="'.$bg.'" align="center">'.utf8_encode($name).' '.$politics_lang["votefor"].' '.utf8_encode($votename).'</td></tr>';
	}
}
echo '</table>';
?>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["allgemeineinfos"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<?php
echo '<td width="550">';
echo '<table width="100%" border="0" cellpadding="0" cellspacing="1">';

//sektorsteuersatz/sektorkollektoren    
$bg='cell1';
echo '<tr height="30" align="center">';
echo '<td class="'.$bg.'">'.$politics_lang["sektorsteuersatz"].': '.$ssteuer.'%</td>';
echo '<td class="'.$bg.'">'.$politics_lang["sektorkollektoren"].': '.number_format($seccol, 0,",",".").'</td>';
echo '</tr>';

//Kostenfaktor
$avg_player=getAveragePlayerAmountInSectorOnServer();
$kostenfaktor=10-$avg_player;

//kosten sektorgeb�ude
//sektorgeb�udekosten auslesen
$btipstr='<table width=475px border=0 cellpadding=0 cellspacing=1><tr align=center><td>&nbsp;</td><td>M</td><td>D</td><td>I</td><td>E</td><td>T</td><tr>';
//geb�ude
$db_daten=mysql_query("SELECT tech_name, restyp01, restyp02, restyp03, restyp04, restyp05 FROM de_tech_data1 WHERE tech_id>119 AND tech_id<130 ORDER BY tech_id",$db);
while($row = mysql_fetch_array($db_daten)){
	$btipstr.= '<tr align=center>';
	$btipstr.= '<td align=left>'.$row[0].'</td>';
	$btipstr.= '<td>'.number_format($row[1]/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row[2]/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row[3]/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row[4]/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row[5]/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '</tr>';
}
//raumschiff
$btipstr.= '<tr align=center>';
$btipstr.= '<td align=left>'.$politics_lang['sektorraumschiff'].'</td>';
$btipstr.= '<td>'.number_format(2000, 0,",",".").'</td>';
$btipstr.= '<td>'.number_format(500, 0,",",".").'</td>';
$btipstr.= '<td>'.number_format(500, 0,",",".").'</td>';
$btipstr.= '<td>'.number_format(2000, 0,",",".").'</td>';
$btipstr.= '<td>'.number_format(0, 0,",",".").'</td>';
$btipstr.= '</tr>';

$btipstr.= '</table>';
$btip=$politics_lang['sektorkosten'].'&'.$btipstr;  



//schiffe in der sektorflotte
$bg='cell';
echo '<tr height="30" align="center">';
echo '<td class="'.$bg.'">'.$politics_lang["sumsekfleet"].': '.number_format($secfleet, 0,",",".").'</td>';
echo '<td class="'.$bg.'">'.$politics_lang["sektorkosten"].': <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$btip.'"></td>';
echo '</tr>';    

//sektorpasswort für die sphäre benötigt
if ($dbsecpass!=''){
  $bg='cell1';
  echo '<tr height="30" align="center">';
  echo '<td class="'.$bg.'">'.$politics_lang["sektorpasswort"].': '.$dbsecpass.'</td>';
  echo '</tr>';    
}

echo '</table>';
?>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["sektorlagereinzahlungen"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550">
<?php
$c1="";
$result = mysql_query("SELECT spielername, spend01, spend02, spend03, spend04, spend05 FROM de_user_data WHERE sector = '$sector' ORDER BY `system`",$db);
$num = mysql_num_rows($result);
if ($num>0)
{
  $bg='cell1';
 
  $output='
  <table border="0" cellpadding="0" cellspacing="1">
  <tr height="35" align="center">
  <td width="220" class="'.$bg.'"><b>'.$politics_lang["spieler"].'</td>
  <td width="110" class="'.$bg.'"><b>'.$politics_lang["multiplex"].'</td>
  <td width="110" class="'.$bg.'"><b>'.$politics_lang["dyharra"].'</td>
  <td width="110" class="'.$bg.'"><b>'.$politics_lang["iradium"].'</td>
  <td width="110" class="'.$bg.'"><b>'.$politics_lang["eternium"].'</td>
  <td width="110" class="'.$bg.'"><b>'.$politics_lang["tronic"].'</td>
  </tr>';

  //tabellenheader
  $sc=0;
  while($row = mysql_fetch_array($result))
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell';
    }
    else
    {
      $c1=0;
      $bg='cell1';
    }
    
	//javascript info punkte bauen
    $spendtip[$sc] = $politics_lang['punktewertderspende'].'&'.$politics_lang['punktewertderspende'].': '.number_format(($row["spend01"]+$row["spend02"]*2+$row["spend03"]*3+$row["spend04"]*4)/10+$row["spend05"]*1000, 0,",",".").'<br><br>'.$politics_lang['punktewertformel1'].':<br>'.$politics_lang['punktewertformel2'];

    $output.= '<tr height="30" align="center" title="'.$spendtip[$sc].'">';
    

    $output.= '<td class="'.$bg.'">'.utf8_encode($row["spielername"]).'</td>';
    $output.= '<td class="'.$bg.'">'.number_format($row["spend01"], 0,",",".").'</td>';
    $output.= '<td class="'.$bg.'">'.number_format($row["spend02"], 0,",",".").'</td>';
    $output.= '<td class="'.$bg.'">'.number_format($row["spend03"], 0,",",".").'</td>';
    $output.= '<td class="'.$bg.'">'.number_format($row["spend04"], 0,",",".").'</td>';
    $output.= '<td class="'.$bg.'">'.number_format($row["spend05"], 0,",",".").'</td>';

    $output.= '</tr>';
    $sc++;
  }
  //tabellenfu�
  $output.= '</table>';
  echo $output;
}
?>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["spielerinformationen"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>

<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550">
<?php
$result = mysql_query("SELECT de_user_data.spielername, de_login.last_login,  de_login.last_click, de_user_data.`system`, de_user_data.chatoff, de_user_data.secstatdisable FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) WHERE de_user_data.sector='$sector' ORDER BY `system` ASC LIMIT 200",$db);
$num = mysql_num_rows($result);
if ($num>0)
{
  $bg='cell1';
  $c1=0;
  echo'
  <table border="0" cellpadding="0" cellspacing="1">
  <tr height="35" align="center">
  <td width="34%" class="'.$bg.'"><b>'.$politics_lang["spieler"].'</td>
  <td width="33%" class="'.$bg.'"><b>'.$politics_lang["on12h"].'</td>
  <td width="33%" class="'.$bg.'"><b>'.$politics_lang["sektorstatuseinsicht"].'</td>
  </tr>';

  //tabellenheader
  while($row = mysql_fetch_array($result))
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell';
    }
    else
    {
      $c1=0;
      $bg='cell1';
    }


    echo '<tr height="30" align="center">';

    //spielername
    echo '<td class="'.$bg.'">'.utf8_encode($row["spielername"]).'</td>';
    //online innerhalb der letzten 12 stunden
    echo '<td class="'.$bg.'">';
    if(strtotime($row["last_click"])+43200 > time()) echo $politics_lang["ja"];else echo $politics_lang["nein"];
    echo'</td>';
	//sektorchat aktiviert
	/*
	  <td width="25%" class="'.$bg.'"><b>'.$politics_lang["acsekchat"].'</td>
    echo '<td class="'.$bg.'">';
    if($row["chatoff"]==0) echo $politics_lang["ja"];else echo $politics_lang["nein"];
	echo '</td>';
	*/
    //sektorstatus sichtbar, als sk kann man das umstellen
    echo '<td class="'.$bg.'">';
    if($row["secstatdisable"]==0){
      if($system==issectorcommander())echo '<a href="politics.php?s=1&do=2&sys='.$row["system"].'">';
      echo $politics_lang["ja"];
      if($system==issectorcommander())echo '</a>';
    }
    else 
    {
      if($system==issectorcommander())echo '<a href="politics.php?s=1&do=2&sys='.$row["system"].'">';    	
      echo $politics_lang["nein"];
      if($system==issectorcommander())echo '</a>';
    }
    echo '</td>';    
    
    //EFTA aktiviert
    /*echo '<td class="'.$bg.'">';
    if($row["useefta"]==1) echo $politics_lang["ja"];else echo $politics_lang["nein"];
    echo '</td>';*/

    echo '</tr>';
  }
  //tabellenfu�
  echo '</table>';
}
?>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
// sektorinterne angriffe
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
/*
echo '<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu">'.$politics_lang["sektorinterneangriffe"].'</div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550">';

echo'
<table width="100%"border="0" cellpadding="0" cellspacing="0">
<tr align="center"><td class="cell">'.$politics_lang["sektorinterneangriffe1"].
'<br>'.$politics_lang["sektorinterneangriffedafuer"].': '.
(mysql_result(mysql_query("SELECT count(*) FROM de_user_data WHERE secatt=1 AND sector='$sector'", $db),0)).
'<br>'.$politics_lang["sektorinterneangriffedagegen"].': '.
(mysql_result(mysql_query("SELECT count(*) FROM de_user_data WHERE secatt=0 AND sector='$sector'", $db),0)).'<br>';
//link zum w�hlen
if($secatt==0)echo '<a href="politics.php?s=1&do=1&secatt=1">'.$politics_lang["sektorinterneangriffewahl2"].'</a>';
else echo '<a href="politics.php?s=1&do=1&secatt=0">'.$politics_lang["sektorinterneangriffewahl1"].'</a>';

echo '</td></tr>';
echo '</table>';

echo '</td>
<td width="13" class="rr">&nbsp;</td>
</tr>';
*/
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
//wenn man die unendlichkeitssph�re hat, dann kann man umziehen
////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////

if ($techs[26]=='1'  AND $secmoves<$sv_max_secmoves)
{
?>
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["umzugmsg"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550">
<div class="cell">
<?php
//schauen ob derjenige schon einen sektor beantragt hat
$result1 = mysql_query("SELECT user_id, pass, ticks FROM de_sector_umzug WHERE user_id='$ums_user_id' AND typ=1",$db);
$anz1 = mysql_num_rows($result1);
if ($anz1==0)//es l�uft noch kein umzug
{

  echo '<br>'.$politics_lang["msg_18_1"].': <input type="submit" value="'.$politics_lang["getsektor"].'" name="getnewsec"><br><br>';
  echo '<hr><br>'.$politics_lang["msg_18_2"].': <input type="text" name="secpass" value=""><br><br>
    <input type="submit" value="'.$politics_lang["selsek"].'" name="joinsec"><br><br>';

}
else
{
  //passwort der beantragung ausgeben
  $row1=mysql_fetch_array($result1);
  echo '<br>'.$politics_lang["msg_19_1"].': '.$row1["pass"];
  echo '<br>'.$politics_lang["msg_19_2"].':<br>';
  //alle spieler ausgeben die dran teilnehmen
  $useranz=0;
  $result = mysql_query("SELECT user_id FROM de_sector_umzug WHERE pass='".$row1["pass"]."'",$db);
  while($row=mysql_fetch_array($result))
  {
    $result1 = mysql_query("SELECT spielername, sector, `system` FROM de_user_data WHERE user_id='".$row["user_id"]."'",$db);
    $row1=mysql_fetch_array($result1);
    echo $row1["spielername"].' ['.$row1["sector"].':'.$row1["system"].']<br>';
    $useranz++;
  }
  echo '<br>'.$politics_lang["msg_20_1"].' '.$useranz.' '.$politics_lang["msg_20_2"].' '.$sv_min_user_per_regsector.' '.$politics_lang["msg_20_3"].'.<br>';
  echo '<br><input type="submit" value="'.$politics_lang["cancelgetsek"].'" name="cancelgetnewsec"><br><br>';
}
?>
</div>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php
}//ende umziehen

//anfang rausvoten
//schauen ob ein vote l�uft

$result1 = mysql_query("SELECT sector_id, user_id, votes, ticks FROM de_sector_voteout WHERE sector_id='$sector'",$db);
$anz1 = mysql_num_rows($result1);

$showexilvote=0;
if ($anz1!=0)//es gibt nen vote
{
  $showexilvote=1;
  $row = mysql_fetch_array($result1);
  $vuser_id=$row["user_id"];
  $vvotes=$row["votes"];
  $vticks=$row["ticks"];

  //sollte anz2==0 sein, dann gibts den spieler nicht mehr, wahrscheinlich gel�scht, dann sofort den datensatz entfernen
  $result2 = mysql_query("SELECT spielername FROM de_user_data WHERE user_id='$vuser_id'",$db);
  $anz2 = mysql_num_rows($result2);
  if ($anz2==0)
  {
    mysql_query("DELETE FROM de_sector_voteout where sector_id='$sector'");
    $showexilvote=0;
  }
  else
  {
    $row = mysql_fetch_array($result2);
    $vspielername=$row["spielername"];
  }
}

if($showexilvote==1){
?>
<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["exilvote"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550" class="cell">
<div class="cell">
<?php
//alle user des sektors auslesen die aktiv sind
echo '<b>'.$politics_lang["msg_21_1"].' '.$vspielername.' '.$politics_lang["msg_21_2"].'?</b><br>';
//$result = mysql_query("SELECT spielername, `system` FROM de_user_data WHERE sector='$sector' ORDER BY `system` ASC",$db);
$result = mysql_query("SELECT de_user_data.spielername, de_user_data.`system` FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) 
  WHERE de_user_data.sector='$sector' AND de_login.status=1 ORDER BY `system` ASC ",$db);

$anz = mysql_num_rows($result);
while($row=mysql_fetch_array($result))
{
  if ($vvotes[$row["system"]]==3)
  {
    $msg=$politics_lang["nichtgevotet"];
    $tc='<font color="3399FF">';
    $vw[3]++;
  }
  elseif ($vvotes[$row["system"]]==2)
  {
    $msg=$politics_lang["nichtentscheiden"];
    $tc='<font color="DDDDDD">';
    $vw[2]++;
  }
  elseif ($vvotes[$row["system"]]==1)
  {
    $msg=$politics_lang["spielersollgehen"];
    $tc='<font color="00FF00">';
    $vw[1]++;
  }
  elseif ($vvotes[$row["system"]]==0)
  {
    $msg=$politics_lang["spielersollbleiben"];
    $tc='<font color="FF0000">';
    $vw[0]++;
  }
  //echo $tc.$row["spielername"].' '.$msg.'</font><br>';
}
//zusammenfassung ausgeben
echo '<br>'.$politics_lang["msg_22_1"].': '.$vw[3].' - <font color="DDDDDD">'.$politics_lang["msg_22_2"].': '.$vw[2].
 ' - </font><font color="00FF00">'.$politics_lang["msg_22_3"].': '.$vw[1].' - </font><font color="FF0000">'.$politics_lang["msg_22_4"].': '.$vw[0].'</font><br><br>';
//prozentwert ausgeben
$prozentwert=$vw[1]/($vw[0]+$vw[1]+$vw[2]+$vw[3])*100;
$veigenewahl=$vvotes[$system];
echo $politics_lang["msg_23_1"].' '.number_format($prozentwert, 2,",",".").'% '.$politics_lang["msg_23_2"].' '.$sv_voteoutgrenze.'%, '.$politics_lang["msg_23_3"].'.<br>';
echo $politics_lang["msg_23_4"].': '.$vticks.'<br><br>';
echo $politics_lang["msg_23_5"].'? ';

echo '<select name="vspielerwahl" size="1" ><option name="0"';
if($veigenewahl=="0")echo " selected";
echo '>'.$politics_lang["nein"].'</option><option name="1"';
if($veigenewahl=="1"){echo " selected";}
echo '>'.$politics_lang["ja"].'</option><option name="2"';
if($veigenewahl=="2"){echo " selected";}
echo '>'.$politics_lang["egal"].'</option></select> <input type="submit" value="'.$politics_lang["stimmeabgeben"].'" name="setvoteout"><br><br>';
?>
</div>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php
}//ende rausvoten
?>

<tr align="center">
<td width="13" height="37" class="rml">&nbsp;</td>
<td width="550" align="center" class="ro"><div class="cellu"><?php echo $politics_lang["knownartis"]?></div></td>
<td width="13" class="rmr">&nbsp;</td>
</tr>
</tr>
<tr align="center">
<td width="13" height="25" class="rl">&nbsp;</td>
<td width="550">
<?php
$artresult = mysql_query("SELECT id, artname, sector, color FROM de_artefakt where sector > 0 order by id",$db);
$num = mysql_num_rows($artresult);
if ($num>0){
  $bg='cell1';
  echo'
  <table border="0" cellpadding="0" cellspacing="1">
  <tr height="35" align="center">
  <td width="200" class="'.$bg.'"><b>'.$politics_lang["artefakt"].'</td>
  <td width="50" class="'.$bg.'"><b>'.$politics_lang["sektor"].'</td>
  <td width="60" class="'.$bg.'"><b>'.$politics_lang["keob"].'</td>
  <td width="100" class="'.$bg.'"><b>'.$politics_lang["m"].'</td>
  <td width="100" class="'.$bg.'"><b>'.$politics_lang["d"].'</td>
  <td width="100" class="'.$bg.'"><b>'.$politics_lang["i"].'</td>
  <td width="100" class="'.$bg.'"><b>'.$politics_lang["e"].'</td>
  <td width="60" class="'.$bg.'"><b>'.$politics_lang["w"].'</td>
  </tr>';

  //tabellenheader
  $c1=0;
  while($row = mysql_fetch_array($artresult))
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell';
    }
    else
    {
      $c1=0;
      $bg='cell1';
    }


    echo '<tr height="30" align="center">';

    echo '<td class="'.$bg.'"><font color="#'.$row["color"].'">'.$row["artname"].'</td>';
    echo '<td class="'.$bg.'">'.$row["sector"].'</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][0], 2,",",".").' %</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][1], 0,"",".").'</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][2], 0,"",".").'</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][3], 0,"",".").'</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][4], 0,"",".").'</td>';
    echo '<td class="'.$bg.'">'.number_format($sv_artefakt[$row["id"]-1][5], 2,",",".").' %</td>';

    echo '</tr>';
  }
  //tabellenfu�
  echo '</table>';
  echo '<div class="cell">'.$politics_lang["msg_24"].'</div>';
}
?>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
<?php
} //sektor ende
?>
</div>
</form>
<br>
<?php include "fooban.php"; ?>
</body>
</html>