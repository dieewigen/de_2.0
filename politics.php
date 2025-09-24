<?php
include "inc/header.inc.php";
include "inc/artefakt.inc.php";
include 'inc/lang/'.$sv_server_lang.'_politics.lang.php';
include_once "functions.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, techs, sector, `system`, score, newtrans, newnews, secmoves, secatt 
     FROM de_user_data 
     WHERE user_id=?", 
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$secmoves=$row["secmoves"];$secatt=$row["secatt"];

$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT name, url, bk, skmes, techs, ssteuer, e1, e2, pass, votecounter, col, ekey 
     FROM de_sector 
     WHERE sec_id=?",
    [$sector]
);
$row = mysqli_fetch_array($db_daten);
$url=$row["url"];$name=$row["name"];$bk=$row["bk"];$stext=$row["skmes"];
$sectechs=$row["techs"];
$ssteuer=$row["ssteuer"];
$dbsecpass=$row["pass"];
$secfleet=$row["e1"]+$row["e2"];
$votecounter=$row["votecounter"];
$seccol=$row["col"];
$secekey=$row["ekey"];


//maximalen tick auslesen
//$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data", []);
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//anzahl der spieler im sektor auslesen
$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT COUNT(*) AS wert FROM de_user_data WHERE sector=?",
    [$sector]
);
$row = mysqli_fetch_array($result);
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

<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";
echo '<form action="politics.php" method="post">';

//SK - sekstat sichtbar
if(isset($_REQUEST["do"]) && $_REQUEST["do"]==2 AND $system==issectorcommander()){
	
  $sys=intval($_REQUEST["sys"]);
  //daten des spielers auslesen
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT spielername, secstatdisable 
     FROM de_user_data 
     WHERE sector=? AND `system`=?",
    [$sector, $sys]
  );
  if(mysqli_num_rows($db_daten)==1)
  {
    $row = mysqli_fetch_array($db_daten);
    $spielername=$row["spielername"];
    $secstatdisable=$row["secstatdisable"];
    if($secstatdisable==0)$secstatdisable=1;
    elseif($secstatdisable==1)$secstatdisable=0;
    mysqli_execute_query($GLOBALS['dbi'], 
        "UPDATE de_user_data SET secstatdisable=? WHERE sector=? AND `system`=?",
        [$secstatdisable, $sector, $sys]
    );
    //info in die sektorhistorie packen - komplette spielerlöschung
    if($secstatdisable==0)
        mysqli_execute_query($GLOBALS['dbi'], 
            "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, ?, ?, ?)",
            [$maxtick, '5', $sector, $spielername]
        );
    if($secstatdisable==1)
        mysqli_execute_query($GLOBALS['dbi'], 
            "INSERT INTO de_news_sector(wt, typ, sector, text) VALUES (?, ?, ?, ?)",
            [$maxtick, '6', $sector, $spielername]
        );
  }
}

//für einen sk stimmen
$letsgo=isset($_POST['letsgo']) ? $_POST['letsgo'] : '';
if(!empty($letsgo) && $sector>1){
	$userslist=intval($_POST['userslist']);
	mysqli_execute_query($GLOBALS['dbi'], 
		"UPDATE de_user_data SET votefor=? WHERE user_id=?",
		[$userslist, $_SESSION['ums_user_id']]
	);
}

//einen neuen sektor beantragen
$getnewsec=isset($_REQUEST['getnewsec']) ?? '';
if(!empty($getnewsec) && $secmoves<$sv_max_secmoves && $techs[26]=='1'){
	//abfrage da die funktion  zum erstellen nur noch f�r premium accounts zul�ssig ist
	if (1==1){
		//schauen ob derjenige schon einen sektor beantragt hat
		$result1 = mysqli_execute_query($GLOBALS['dbi'], 
			"SELECT user_id FROM de_sector_umzug WHERE user_id=?",
			[$_SESSION['ums_user_id']]
		);
		$anz1 = mysqli_num_rows($result1);
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

				$result1 = mysqli_execute_query($GLOBALS['dbi'], 
					"SELECT user_id FROM de_sector_umzug WHERE pass=?",
					[$newpass]
				);
				$result2 = mysqli_execute_query($GLOBALS['dbi'], 
					"SELECT sec_id FROM de_sector WHERE pass=?",
					[$newpass]
				);

				if (mysqli_num_rows($result1)==0 AND mysqli_num_rows($result2)==0) $ok=1;
			}
			//eintrag in der db machen, dass er umzieht
			mysqli_execute_query($GLOBALS['dbi'], 
				"INSERT INTO de_sector_umzug (user_id, typ, sector, `system`, pass, ticks) VALUES (?, 1, 0, 0, ?, 192)",
				[$_SESSION['ums_user_id'], $newpass]
			);
		}
	}
	else echo '<br><font color="#FF0000"><b>'.$politics_lang["msg_4"].'<br><br></b></font>';
}

//einem bestehenden sektor joinen
$joinsec  = $_REQUEST['joinsec']  ?? '';
$secpass  = $_REQUEST['secpass']  ?? '';
if(!empty($joinsec) && $secmoves<$sv_max_secmoves && $secpass!='' && $techs[26]=='1'){
  //es gibt 2 m�glichkeiten zu joinen
  //1. es gibt den sektor schon
  //2. der sektor wird beantragt
  //art des joinens festellen

  //anzahl der leute die in den sektor ziehen wollen
  $result1 = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT user_id FROM de_sector_umzug WHERE pass=?",
    [$secpass]
  );
  $anz1=mysqli_num_rows($result1);

  //gibt es den sektor schon?
  $result2 = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT sec_id FROM de_sector WHERE pass=?",
    [$secpass]
  );
  $anz2=mysqli_num_rows($result2);

  //hat man evtl. schon was am laufen
  $result3 = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT user_id FROM de_sector_umzug WHERE user_id=?",
    [$_SESSION['ums_user_id']]
  );
  $anz3 = mysqli_num_rows($result3);

  if ($anz2>0 AND $anz3==0) //der sektor besteht schon, direkt reinmoven typ 2
  {
    //schauen ob noch platz im sektor ist
    $row2 = mysqli_fetch_array($result2);
    $zielsec=$row2["sec_id"];
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT user_id FROM de_user_data WHERE sector=?",
        [$zielsec]
    );
    $accanz=mysqli_num_rows($result);
    $result = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT user_id FROM de_sector_umzug WHERE typ=2 AND sector=?",
        [$zielsec]
    );
    $accanz+=mysqli_num_rows($result);

    if($accanz<$sv_max_user_per_regsector)
    {
      //es ist noch ein platz frei -> spieler zieht um
      $time=strftime("%Y%m%d%H%M%S");
      //account sperren
      mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_login SET status = ? WHERE user_id = ?",
        [4, $_SESSION['ums_user_id']]
      );
      
      //eintrag in der db machen, dass er umzieht
      mysqli_execute_query($GLOBALS['dbi'],
        "INSERT INTO de_sector_umzug (user_id, typ, sector) VALUES (?, ?, ?)",
        [$_SESSION['ums_user_id'], 2, $zielsec]
      );
      
      //nachricht an den account schicken
      mysqli_execute_query($GLOBALS['dbi'],
        "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
        [$_SESSION['ums_user_id'], 3, $time, $politics_lang["msg_25"]]
      );
      
      mysqli_execute_query($GLOBALS['dbi'],
        "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
        [1, $_SESSION['ums_user_id']]
      );
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
      mysqli_execute_query($GLOBALS['dbi'], 
        "INSERT INTO de_sector_umzug (user_id, typ, pass, ticks) VALUES (?, 1, ?, 192)",
        [$_SESSION['ums_user_id'], $secpass]
      );
    }
    else echo '<br><font color="FF0000">'.$politics_lang["msg_6"].'</font><br><br>';
  }
  else echo '<br><font color="FF0000">'.$politics_lang["msg_7"].'</font><br><br>';
}


//laufende sektorbeantragung löschen
$cancelgetnewsec=isset($_POST['cancelgetnewsec']) ? $_POST['cancelgetnewsec'] : '';
if(!empty($cancelgetnewsec)){
	mysqli_execute_query($GLOBALS['dbi'], 
		"DELETE FROM de_sector_umzug WHERE user_id=? AND typ=1",
		[$_SESSION['ums_user_id']]
	);
}


$voteoutcancel=isset($_POST['voteoutcancel']) ? $_POST['voteoutcancel'] : '';
if(!empty($voteoutcancel) && $system==issectorcommander()){
  mysqli_execute_query($GLOBALS['dbi'], 
    "DELETE FROM de_sector_voteout WHERE sector_id=?",
    [$sector]
  );
  $time=strftime("%Y%m%d%H%M%S");
  //nachrichten an alle spieler schicken
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT user_id FROM de_user_data WHERE sector=?",
    [$sector]
  );
  while($row = mysqli_fetch_array($db_daten))
  {
    mysqli_execute_query($GLOBALS['dbi'], 
      "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
      [$row['user_id'], $time, $politics_lang["msg_26"]]
    );
    mysqli_execute_query($GLOBALS['dbi'], 
      "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
      [$row['user_id']]
    );
  }
}

//ein spieler gibt seine stimme zum rausvoten ab
$setvoteout=isset($_POST['setvoteout']) ? $_POST['setvoteout'] : '';
$vspielerwahl=isset($_POST['vspielerwahl']) ? $_POST['vspielerwahl'] : '';
if (!empty($setvoteout) && ($vspielerwahl==$politics_lang["ja"] || $vspielerwahl==$politics_lang["nein"] || $vspielerwahl==$politics_lang["egal"])){
  //erstmal schauen ob ein vote l�uft
  $result1 = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT user_id, votes FROM de_sector_voteout WHERE sector_id=?",
    [$sector]
  );
  $anz1 = mysqli_num_rows($result1);
  if ($anz1!=0){
    $row=mysqli_fetch_array($result1);
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
    //$result1 = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=?", [$sector]);
    $result1 = mysqli_execute_query($GLOBALS['dbi'], 
      "SELECT de_user_data.`system` FROM de_login 
       LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) 
       WHERE de_user_data.sector=? AND de_login.status=1",
      [$sector]
    );
    $anz1 = mysqli_num_rows($result1);
    $prozentwert=($jastimmen*100)/$anz1;
    if ($prozentwert>=$sv_voteoutgrenze){
      $result1 = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT `system` FROM de_user_data WHERE user_id=?",
        [$vuser_id]
      );
      $row1=mysqli_fetch_array($result1);
      $vsystem=$row1["system"];
      $time=strftime("%Y%m%d%H%M%S");
      //spieler wurde rausgevotet

      //gesperrte user und leute im umode kommen direkt in sektor 1
      //dazu erstmal accountstatus auslesen
      $result1 = mysqli_execute_query($GLOBALS['dbi'], 
        "SELECT status FROM de_login WHERE user_id=?",
        [$vuser_id]
      );
      $row1=mysqli_fetch_array($result1);
      $accstatus=$row1["status"];
      if($accstatus==2 OR $accstatus==3){
		//nachricht an den account schicken
		mysqli_execute_query($GLOBALS['dbi'], 
			"INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
			[$vuser_id, $time, $politics_lang["msg_25"]]
		);
		
		//auf 0:0 verschieben damit er in sektor 1 landet und einstellungen updaten
		mysqli_execute_query($GLOBALS['dbi'], 
			"UPDATE de_user_data 
			 SET sector=0, `system`=0, newnews=1, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, votefor=0, secstatdisable=0 
			 WHERE user_id = ?",
			[$vuser_id]
		);
		
		//voteumfrage aus der db l�schen
		mysqli_execute_query($GLOBALS['dbi'], 
			"DELETE FROM de_sector_voteout WHERE sector_id=?",
			[$sector]
		);
		//nachricht an alle im sektor schicken
		$db_daten = mysqli_execute_query($GLOBALS['dbi'], 
			"SELECT user_id FROM de_user_data WHERE sector=?",
			[$sector]
		);
		while($row = mysqli_fetch_array($db_daten))
		{
			mysqli_execute_query($GLOBALS['dbi'], 
				"INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
				[$row['user_id'], $time, $politics_lang["msg_27"]]
			);
			mysqli_execute_query($GLOBALS['dbi'], 
				"UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
				[$row['user_id']]
			);
		}
		
		//wenn er BK ist, den Posten auf 0 setzen
		mysqli_execute_query($GLOBALS['dbi'], 
			"UPDATE de_sector SET bk=0 WHERE sec_id=? AND bk=?",
			[$sector, $vsystem]
		);
		
		//votetimer/votecounter f�r den sektor setzen
		mt_srand((double)microtime()*10000);
		$votetimer=mt_rand(20,120);
		//$votetimer=0;
		$sv_sector_votetime_lock=0;
		//if($accstatus!=2)
		mysqli_execute_query($GLOBALS['dbi'], 
			"UPDATE de_sector SET votetimer=?, votecounter=? WHERE sec_id=?",
			[$votetimer, $sv_sector_votetime_lock, $sector]
		);

      }else{
        //accountstatus sichern und account sperren
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_login SET savestatus=status WHERE user_id = ?",
          [$vuser_id]
        );
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_login SET status = 4 WHERE user_id = ?",
          [$vuser_id]
        );
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_user_data SET spend01=0, spend02=0, spend03=0, spend04=0, spend05=0 WHERE user_id = ?",
          [$vuser_id]
        );
        //eintrag in der db machen, dass er umzieht
        mysqli_execute_query($GLOBALS['dbi'], 
          "INSERT INTO de_sector_umzug (user_id, typ, sector, `system`) VALUES (?, 0, ?, ?)",
          [$vuser_id, $sector, $vsystem]
        );
        //nachricht an den account schicken
        mysqli_execute_query($GLOBALS['dbi'], 
          "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)",
          [$vuser_id, $time, $politics_lang["msg_25"]]
        );
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?",
          [$vuser_id]
        );
        //voteumfrage aus der db l�schen
        mysqli_execute_query($GLOBALS['dbi'], 
          "DELETE FROM de_sector_voteout WHERE sector_id=?",
          [$sector]
        );
        //nachricht an alle im sektor schicken
        $result = mysqli_execute_query($GLOBALS['dbi'],
          "SELECT user_id FROM de_user_data WHERE sector = ?",
          [$sector]
        );
        
        while($row = $result->fetch_assoc())
        {
          mysqli_execute_query($GLOBALS['dbi'],
            "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
            [$row["user_id"], 3, $time, $politics_lang["msg_27"]]
          );
          
          mysqli_execute_query($GLOBALS['dbi'],
            "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
            [1, $row["user_id"]]
          );
        }
        //votetimer/votecounter f�r den sektor setzen
        mt_srand((double)microtime()*10000);
        $votetimer=mt_rand(16,96);
        //$votetimer=0;
        //$sv_sector_votetime_lock=0;
        mysqli_execute_query($GLOBALS['dbi'], 
          "UPDATE de_sector SET votetimer=?, votecounter=? WHERE sec_id=?",
          [$votetimer, $sv_sector_votetime_lock, $sector]
        );
      }
    }
    else
    {
      //spieler wurde noch nicht rausgevotet
      //db mit den stimmen updaten
      mysqli_execute_query($GLOBALS['dbi'], 
        "UPDATE de_sector_voteout SET votes = ? WHERE sector_id = ?",
        [$vvotes, $sector]);
    }
   //echo $prozentwert;
  }
}

//startet ein rausvoten
$voteout=isset($_POST['voteout']) ? $_POST['voteout'] : '';
$voteoutlist=isset($_POST['voteoutlist']) ? $_POST['voteoutlist'] : '';
if(!empty($voteout) && $system==issectorcommander() && $sector > 1){
  //beim reinsetzen des votes erstmal schauen ob nicht schon eins existiert
  $result1 = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT sector_id FROM de_sector_voteout WHERE sector_id = ?",
    [$sector]
  );
  $anz1 = $result1->num_rows;
  
  //anhand des spielernamens die user_id und den sector rausfinden
  $result2 = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id, sector FROM de_user_data WHERE spielername = ?",
    [$voteoutlist]
  );
  $row = $result2->fetch_assoc();
  //id des zu votenden auslesen
  $vuser_id=$row["user_id"];
  //schaue ob der spieler auch wirklich in dem sektor ist
  $vsector=$row["sector"];
  $anz2 = $result2->num_rows;
  
  //überprüfen ob der spieler gesperrt ist, falls ja ist es kostenlos und es gibt keinen counter
  $db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT status, delmode FROM de_login WHERE user_id = ?",
    [$vuser_id]
  );
  $row = $db_daten->fetch_assoc();
  
  //�berpr�fen ob er l�nger als 7 tage offline war
  
  if ($gesperrt==1)
  {
    if($votecounter==0)
    {
  	  if ($anz1==0 AND $anz2>0)
      {
        
        if ($vsector!=$sector) die($politics_lang["msg_8"]);
        $vticks=96*3;
        //votes erstmal auf 3 setzen, d.h. noch nicht gew�hlt, daf�r 1 oder dagegen 0, 2 egal
        $vvotes='s';
        for ($i=1; $i<=($sv_maxsystem+5); $i++) $vvotes.='3';

        //eintrag in die db packen
        mysqli_execute_query($GLOBALS['dbi'],
          "INSERT INTO de_sector_voteout (sector_id, user_id, votes, ticks) VALUES (?, ?, ?, ?)",
          [$sector, $vuser_id, $vvotes, $vticks]
        );
        //rohstoffe vom sektor abziehen

        //nachricht an alle im sektor schicken, dass es ein vote gibt
        $time=strftime("%Y%m%d%H%M%S");
        $result = mysqli_execute_query($GLOBALS['dbi'],
          "SELECT user_id FROM de_user_data WHERE sector = ?",
          [$sector]
        );
        
        while($row = $result->fetch_assoc())
        {
          $message = $politics_lang["msg_24_1"]." ".$voteoutlist." ".$politics_lang["msg_24_2"];
          mysqli_execute_query($GLOBALS['dbi'],
            "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)",
            [$row["user_id"], 3, $time, $message]
          );
          
          mysqli_execute_query($GLOBALS['dbi'],
            "UPDATE de_user_data SET newnews = ? WHERE user_id = ?",
            [1, $row["user_id"]]
          );
        }
      }
    }
  }
  else echo '<br><table width=600><tr><td class="ccr">'.$politics_lang["votedescription"].'</td></tr></table><br>';

}

$sec_btn=isset($_POST['sec_btn']) ? $_POST['sec_btn'] : '';
$newname=isset($_POST['newname']) ? $_POST['newname'] : '';
$seksteuer=isset($_POST['seksteuer']) ?  $_POST['seksteuer'] : 0;
if(!empty($sec_btn) && $system==issectorcommander()){
	if (($name<>$newname) || ($ssteuer<>$seksteuer)){

		$name = htmlspecialchars(stripslashes($newname), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

		mysqli_execute_query($GLOBALS['dbi'],
		  "UPDATE de_sector SET name = ? WHERE sec_id = ?",
		  [$name, $sector]
		);

		if(issectorcommander() && $sector!=1)//nur wenn man sk ist kann man die steuer ändern
		{
			if($sv_deactivate_vsystems!=1)
				mysqli_execute_query($GLOBALS['dbi'],
				  "UPDATE de_sector SET ssteuer = ? WHERE sec_id = ?",
				  [$seksteuer, $sector]
				);
		}
		$ssteuer = $seksteuer;
	}
}


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

$s=isset($_REQUEST['s']) ? $_REQUEST['s'] : 1;

if ($s==2){
	if($system==issectorcommander()) {

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
	else {
    echo 'F&uuml;r diese Funktion wird das Sektorhandelszentrum ben&ouml;tigt.';
  }

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
	$result1 = mysqli_execute_query($GLOBALS['dbi'],
	  "SELECT sector_id, user_id, ticks FROM de_sector_voteout WHERE sector_id = ?",
	  [$sector]
	);
	$anz1 = $result1->num_rows;
	
	if ($anz1==0) {
        //echo $politics_lang["msg_16"].'<br><br>';
    } else {
		if ($status!=1)  //ausgabe der spielerliste, wenn noch keine auswertung erfolgt ist.
		{
			$result = mysqli_execute_query($GLOBALS['dbi'],
			  "SELECT de_user_data.spielername FROM de_user_data WHERE de_user_data.sector = ? ORDER BY `system` ASC",
			  [$sector]
			);
			$anz = $result->num_rows;
			echo '
			<select name="voteoutlist">';
			while($row = $result->fetch_assoc())
			{
			$vspielername=$row["spielername"];

			echo '<option value="'.$vspielername.'">'.$vspielername.'</option>';
			}
			echo '</select><input type="submit" value="'.$politics_lang["startvote"].'" name="voteout" onclick="return confirm(\''.$politics_lang["warnexec"].'?\')"><br><br>';
			//kosten anzeigen
			//echo $politics_lang["kosten"];
		}
		else echo $politics_lang["msg_15_1"].' '.$votecounter.' '.$politics_lang["msg_15_2"];
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

//menü für den sektor, wie sk-wahl und vote für exilanden
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
$result = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT de_user_data.spielername, de_user_data.votefor, de_user_data.`system` 
   FROM de_user_data 
   WHERE de_user_data.sector = ? 
   ORDER BY `system` ASC",
  [$sector]
);
$anz = $result->num_rows;
while($row = $result->fetch_assoc()){
	//�berpr�fen, ob es den Account f�r den man votet noch gibt
	if($row["votefor"]!=0){
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		  "SELECT user_id FROM de_user_data WHERE sector = ? AND `system` = ? ORDER BY `system` ASC",
		  [$sector, $row["votefor"]]
		);
		$anzv = $db_daten->num_rows;
		if($anzv==0)
		{
			mysqli_execute_query($GLOBALS['dbi'],
			  "UPDATE de_user_data SET votefor = ? WHERE sector = ? AND votefor = ?",
			  [0, $sector, $row["votefor"]]
			);
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
		echo '<option value="'.$su[$i][2].'">'.$su[$i][0].'</option>';
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
		
		echo '<tr><td class="'.$bg.'" align="center">'.$name.' '.$politics_lang["votefor"].' '.$votename.'</td></tr>';
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
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT tech_name, restyp01, restyp02, restyp03, restyp04, restyp05 
   FROM de_tech_data1 
   WHERE tech_id > ? AND tech_id < ? 
   ORDER BY tech_id",
  [119, 130]
);
while($row = $db_daten->fetch_assoc()){
	$btipstr.= '<tr align=center>';
	$btipstr.= '<td align=left>'.$row['tech_name'].'</td>';
	$btipstr.= '<td>'.number_format($row['restyp01']/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row['restyp02']/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row['restyp03']/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row['restyp04']/$kostenfaktor, 0,",",".").'</td>';
	$btipstr.= '<td>'.number_format($row['restyp05']/$kostenfaktor, 0,",",".").'</td>';
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
echo '<td class="'.$bg.'">'.$politics_lang["sektorkosten"].': <img style="vertical-align: middle;" src="'.'gp/'.'g/'.$_SESSION['ums_rasse'].'_hilfe.gif" border="0" title="'.$btip.'"></td>';
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
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, spend01, spend02, spend03, spend04, spend05 FROM de_user_data WHERE sector = '$sector' ORDER BY `system`");
$num = $result->num_rows;
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
  while($row = $result->fetch_assoc())
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
    $tip = $politics_lang['punktewertderspende'].'&'.$politics_lang['punktewertderspende'].': '.
           number_format(($row["spend01"]+$row["spend02"]*2+$row["spend03"]*3+$row["spend04"]*4)/10+$row["spend05"]*1000, 0,",",".").'<br><br>'.
           $politics_lang['punktewertformel1'].':<br>'.$politics_lang['punktewertformel2'];

    $output .= '<tr height="30" align="center" title="'.$tip.'">';
    

    $output.= '<td class="'.$bg.'">'.$row["spielername"].'</td>';
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
$result = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT de_user_data.spielername, de_login.last_login, de_login.last_click, 
          de_user_data.`system`, de_user_data.chatoff, de_user_data.secstatdisable 
   FROM de_login 
   LEFT JOIN de_user_data ON (de_login.user_id = de_user_data.user_id) 
   WHERE de_user_data.sector = ? 
   ORDER BY `system` ASC LIMIT 200",
  [$sector]
);
$num = $result->num_rows;
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
  while($row = $result->fetch_assoc())
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
    echo '<td class="'.$bg.'">'.$row["spielername"].'</td>';
    //online innerhalb der letzten 12 stunden
    echo '<td class="'.$bg.'">';
    if(strtotime($row["last_click"])+43200 > time()) echo $politics_lang["ja"];else echo $politics_lang["nein"];
    echo'</td>';
		
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
//////////////////////////////////////////////////////////////
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
$result1 = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT user_id, pass, ticks FROM de_sector_umzug WHERE user_id = ? AND typ = 1",
  [$_SESSION['ums_user_id']]
);
$anz1 = $result1->num_rows;
if($anz1>0) {
  $row1 = $result1->fetch_assoc();
  
  $result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT user_id FROM de_sector_umzug WHERE pass = ?",
    [$row1["pass"]]
  );
  while($row = $result->fetch_assoc()) {
    $result1 = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id = ?",
      [$row["user_id"]]
    );
    $row1 = $result1->fetch_assoc();
    // ...existing code...
  }
}
else
{
  echo '<br>'.$politics_lang["msg_18_1"].': <input type="submit" value="'.$politics_lang["getsektor"].'" name="getnewsec"><br><br>';
  echo '<hr><br>'.$politics_lang["msg_18_2"].': <input type="text" name="secpass" value=""><br><br>
    <input type="submit" value="'.$politics_lang["selsek"].'" name="joinsec"><br><br>';

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

$result1 = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT sector_id, user_id, votes, ticks FROM de_sector_voteout WHERE sector_id=?",
  [$sector]
);
$anz1 = $result1->num_rows;

$showexilvote=0;
if ($anz1!=0)//es gibt nen vote
{
  $showexilvote=1;
  $row = mysqli_fetch_array($result1);
  $vuser_id=$row["user_id"];
  $vvotes=$row["votes"];
  $vticks=$row["ticks"];

  //sollte anz2==0 sein, dann gibts den spieler nicht mehr, wahrscheinlich gel�scht, dann sofort den datensatz entfernen
  $result2 = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT spielername FROM de_user_data WHERE user_id=?",
    [$vuser_id]
  );
  $anz2 = $result2->num_rows;
  if ($anz2==0)
  {
    mysqli_execute_query($GLOBALS['dbi'], 
      "DELETE FROM de_sector_voteout where sector_id=?",
      [$sector]
    );
    $showexilvote=0;
  }
  else
  {
    $row = $result2->fetch_assoc();
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
$result = mysqli_execute_query($GLOBALS['dbi'], 
  "SELECT de_user_data.spielername, de_user_data.`system` FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) 
  WHERE de_user_data.sector=? AND de_login.status=1 ORDER BY `system` ASC ",
  [$sector]
);

$anz = $result->num_rows;
while($row = $result->fetch_assoc())
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
$artresult = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT id, artname, sector, color FROM de_artefakt WHERE sector > 0 ORDER BY id"
);
$num = $artresult->num_rows;
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
  while($row = $artresult->fetch_assoc())
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

</body>
</html>