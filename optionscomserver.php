<?php
include "inc/header.inc.php";
include "outputlib.php";
include "functions.php";
$query = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tick, score, sector, system, newtrans, newnews, allytag, hide_secpics, nrrasse, nrspielername, ovopt, soundoff, credits, chatoff, chatoffallg, helper, patime FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];$restyp05=$row["restyp05"];$punkte=$row["score"];
$newtrans=$row["newtrans"];$allytag=$row["allytag"];$newnews=$row["newnews"];$hidepic=$row["hide_secpics"];
$sector=$row["sector"];$system=$row["system"];$nrrasse=$row["nrrasse"];$nrspielername=$row["nrspielername"];
$tick=$row["tick"];$ovopt=$row["ovopt"];$soundoff=$row["soundoff"];
$credits=$row["credits"];$chatoff=$row["chatoff"];$chatoffallg=$row["chatoffallg"];$helperon=$row['helper'];
$patime=$row['patime'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Community Server</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<div align="center">
<?php

//stelle die ressourcenleiste dar
include "resline.php";

//wenn es kein community-server ist, die seite nicht anzeigen
if($sv_comserver!=1)die('</body></html>');


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//das obere menü hilfe/einstellungen einblenden
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

$hs='<div style="width: 586px; border: 1px solid #666666; background-image:url('.$ums_gpfad.'g/bgpic4.jpg); color: #FFFFFF; padding: 5px;">';
   
//info
$hs.='<a href="optionscomserver.php?showhelp=1"><img id="comserv1" title="Community Server&Auf diesem Server bestimmen die Spieler die Regeln.<br><br>Klicke um mehr Informationen zu erhalten." src="'.$ums_gpfad.'g/symbol14.png" width="48px" height="48px"></a>';
//server konfigurieren
        
$hs.='<a href="optionscomserver.php"><img id="comserv1" title="Community Server konfigurieren&Klicke um Deine Einstellungen zu w&auml;hlen."src="'.$ums_gpfad.'g/symbol15.png" width="48px" height="48px"></a>';
$hs.='</div><br>';
      
echo ($hs);



//soll die hilfe angezeigt werden?
if(isset($_REQUEST['showhelp']))
{
  rahmen_oben('Informationen zum Community Server');
  echo '<div style="width: 572px;" class="cell">';
  echo 'Seid gegr&uuml;&szlig;t,<br><br>
auf diesem Server k&ouml;nnen die Spieler selbst die Einstellungen bestimmen.<br><br>  
Die Einstellungen k&ouml;nnen oben &uuml;ber die Zahnrad-Grafik vorgenommen werden.<br><br>
Einige Einstellungen k&ouml;nnen t&auml;glich, andere erst zur n&auml;chsten Runde ge&auml;ndert werden.<br>
Die Aktualisierung erfolgt bei den t&auml;glichen Einstellungen immer um 19 Uhr und ist dann f&uuml;r 24 Stunden g&uuml;ltig.<br>
Jeder Spieler, der innerhalb der letzten 72 Stunden online war, ist stimmberechtigt.

<br><br>Sollte man zu einem Punkt keine Meinung haben, so kann man das Feld einfach leer lassen, bzw. "nichts gew&auml;hlt" ausw&auml;hlen. 

<br><br>Nat&uuml;rlich stimmen nicht immer alle Spieler f&uuml;r die gleichen Einstellungen. Aus diesem Grund werden die Werte dementsprechend gemittelt. 
Dazu ein Beispiel: 50% der Spieler w&uuml;nschen eine Angriffspunktegrenze von 40% und 50% der Spieler lieber eine von 20%, daraus ergibt sich dann eine Angriffsgrenze von 30% 

<br><br>Wenn Euch die Runde zu langsam ist, dann w&auml;hlt schnellere Ticks, wenn die Runde zu lange dauert, dann senkt die Laufzeit...

<br><br>Die Einstellungsm&ouml;glichkeiten sind vielf&auml;ltig, schaut sie Euch einfach einmal an.

<br><br>Euer BGAM.ES-Team  
  ';
  echo '<div>';
  rahmen_Unten();
}
else //einstellungsmöglichkeiten anzeigen
{
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //überprüfen ob man daten speichern möchte
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  if(isset($_REQUEST['save'])){
    //wt
    if($_REQUEST['v1']!='')
    {
	  $v1=intval($_REQUEST['v1']);
	  if($v1<1 OR $v1>15)$v1=3;
    }
    else $v1='NULL';
	
	//kt
    if($_REQUEST['v2']!='')
    {
	  $v2=intval($_REQUEST['v2']);
	  if($v2<1 OR $v2>60)$v2=12;
    }
    else $v2='NULL';
	  
	//rundendauer wt
    if($_REQUEST['v3']!='')
    {
	  $v3=intval($_REQUEST['v3']);
	  if($v3<5000 OR $v3>70000)$v3=33333;
	}
    else $v3='NULL';
	
	
	//erhabenenhaltezeit
    if($_REQUEST['v4']!='')
    {
	  $v4=intval($_REQUEST['v4']);
	  if($v4<480 OR $v4>2880)$v4=960;
    }
    else $v4='NULL';
	  

	//inaktivenzeit
    if($_REQUEST['v5']!='')
    {
	  $v5=intval($_REQUEST['v5']);
	  if($v5<3 OR $v5>10)$v5=7;
    }
    else $v5='NULL';
	  
	//angriffsgrenze punkte
    if($_REQUEST['v6']!='')
    {
	  $v6=intval($_REQUEST['v6']);
	  if($v6<10 OR $v6>70)$v6=40;
    }
    else $v6='NULL';
	  
	
	//sektorangriffsgrenzenmalus
    if($_REQUEST['v7']!='')
    {
	  $v7=intval($_REQUEST['v7']);
	  if($v7<1 OR $v7>40)$v7=20;
    }
    else $v7='NULL';
	  

	//angriffskollektorengrenze max
    if($_REQUEST['v8']!='' OR $_REQUEST['v9']!='')
    {
	  $v8=intval($_REQUEST['v8']);
	  if($v8<1 OR $v8>70)$v8=35;
    }
    else $v8='NULL';
	  
	
	//angriffskollektorengrenze min
    if($_REQUEST['v8']!='' OR $_REQUEST['v9']!='')
    {
	  $v9=intval($_REQUEST['v9']);
	  if($v9<1 OR $v9>40)$v9=20;
    }
    else $v9='NULL';
	
	//check auf grenzen
	if($_REQUEST['v8']!='' OR $_REQUEST['v9']!='')if($v8<$v9)$v8=$v9;

	//planetares schild
    if($_REQUEST['v10']!='')
    {
	  $v10=intval($_REQUEST['v10']);
	  if($v10<1 OR $v10>20)$v10=10;
    }
    else $v10='NULL';
	  
	
	//recyclotron ohne whg
    if($_REQUEST['v12']!='' OR $_REQUEST['v11']!='')
    {
	  $v11=intval($_REQUEST['v11']);
	  if($v11<1 OR $v11>30)$v11=50;
    }
    else $v11='NULL';
	  

	//recyclotron mit whg
    if($_REQUEST['v12']!='' OR $_REQUEST['v11']!='')
    {
	  $v12=intval($_REQUEST['v12']);
	  if($v12<1 OR $v12>60)$v12=30;
    }
    else $v12='NULL';
	  
	//wertecheck mit/ohne whg	
	if($_REQUEST['v12']!='' OR $_REQUEST['v11']!='')if($v12<$v11)$v12=$v11;
	
	//kollektorklaurate
	if($_REQUEST['v13']!='')
    {	
	  $v13=intval($_REQUEST['v13']);
	  if($v13<5 OR $v13>25)$v13=15;
    }
    else $v13='NULL';

	//kollektorenergie
    if($_REQUEST['v14']!='' OR $_REQUEST['v15']!='')
    {
	  $v14=intval($_REQUEST['v14']);
	  if($v14<50 OR $v14>150)$v14=100;
    }
    else $v14='NULL';

	//kollektorenergie mit pa
    if($_REQUEST['v14']!='' OR $_REQUEST['v15']!='')
    {	
	  $v15=intval($_REQUEST['v15']);
	  if($v15<50 OR $v15>165)$v15=105;
    }
    else $v15='NULL';

	//wertecheck mit/ohne pa	
	if($_REQUEST['v14']!='' OR $_REQUEST['v15']!='')if($v15<$v14)$v15=$v14;
    
	
	//energie kriegsartefakt
    if($_REQUEST['v16']!='')
    {	
	  $v16=intval($_REQUEST['v16']);
	  if($v16<50 OR $v16>150)$v16=100;
    }
    else $v16='NULL';
	  
	
	//exp kampf atter -> kriegsartefakt
    if($_REQUEST['v17']!='')
    {	
	  $v17=intval($_REQUEST['v17']);
	  if($v17<3000 OR $v17>7000)$v17=5000;
    }
    else $v17='NULL';
	  
	
	//exp kampf deffer -> kriegsartefakt
    if($_REQUEST['v18']!='')
    {	
	  $v18=intval($_REQUEST['v18']);
	  if($v18<3000 OR $v18>7000)$v18=4500;
    }
    else $v18='NULL';
	  
	
	//paleniumlager
    if($_REQUEST['v19']!='')
    {	
	  $v19=intval($_REQUEST['v19']);
	  if($v19<10 OR $v19>400)$v19=100;
    }
    else $v19='NULL';
	  
	
	//kopfgeld prozent eroberbar
    if($_REQUEST['v20']!='')
    {	
	  $v20=intval($_REQUEST['v20']);
	  if($v20<1 OR $v20>20)$v20=10;
    }
    else $v20='NULL';
	  
	
	//normal oder br
    if($_REQUEST['v21']!='')
    {	
	  $v21=intval($_REQUEST['v21']);
	  if($v21<0 OR $v21>1)$v21=0;
    }
    else $v21='NULL';
	  

	//sektor zufall/wahl
    if($_REQUEST['v22']!='')
    {	
	  $v22=intval($_REQUEST['v22']);
	  if($v22<0 OR $v22>1)$v22=0;
    }
    else $v22='NULL';
	  
	
	//Sektorgröße
    if($_REQUEST['v23']!='')
    {	
	  $v23=intval($_REQUEST['v23']);
	  if($v23<1 OR $v23>20)$v23=10;
    }
    else $v23='NULL';
	  
	
	//handel deaktivieren
    if($_REQUEST['v24']!='')
    {
	  $v24=intval($_REQUEST['v24']);
	  if($v24<0 OR $v24>1)$v24=0;
    }
    else $v24='NULL';
	  
	
	//religion deaktivieren
    if($_REQUEST['v25']!='')
    {
	  $v25=intval($_REQUEST['v25']);
	  if($v25<0 OR $v25>1)$v25=0;
    }
    else $v25='NULL';
	  
	
	//geheimdienst deaktivieren
    if($_REQUEST['v26']!='')
    {
	  $v26=intval($_REQUEST['v26']);
	  if($v26<0 OR $v26>1)$v26=0;
    }
    else $v26='NULL';
	  
	
	//schwarzmarkt deaktivieren
    if($_REQUEST['v27']!='')    {	
	  $v27=intval($_REQUEST['v27']);
	  if($v27<0 OR $v27>1)$v27=0;
    }
    else $v27='NULL';
      
    //sektorartefakte deaktivieren
    if($_REQUEST['v28']!=''){	
	  $v28=intval($_REQUEST['v28']);
	  if($v28<0 OR $v28>1)$v28=0;
    }
    else $v28='NULL';

    //sektorartefakte deaktivieren
    if($_REQUEST['v29']!=''){	
        $v29=intval($_REQUEST['v29']);
        if($v29<0 OR $v29>1)$v29=0;
      }
      else $v29='NULL';    

	//Misionnen deaktivieren
    if($_REQUEST['v30']!='')    {	
        $v30=intval($_REQUEST['v30']);
        if($v30<0 OR $v30>1)$v30=0;
    }
    else $v30='NULL';
    
    //Flottenpunkte im Sektorstatus deaktivieren
    if($_REQUEST['v31']!='')    {	
        $v31=intval($_REQUEST['v31']);
        if($v31<0 OR $v31>1)$v30=0;
    }
    else $v31='NULL'; 
      
    //die Einstellungen speichern
	$query = "UPDATE de_user_comserver SET 
	v1=?, v2=?, v3=?, v4=?, v5=?, v6=?, v7=?, v8=?, v9=?, v10=?, 
	v11=?, v12=?, v13=?, v14=?, v15=?, v16=?, v17=?, v18=?, v19=?, v20=?,
	v21=?, v22=?, v23=?, v24=?, v25=?, v26=?, v27=?, v28=?, v29=?, v30=?,
    v31=?
	
	WHERE user_id=?";
	mysqli_execute_query($GLOBALS['dbi'], $query, [
	    $v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9, $v10,
	    $v11, $v12, $v13, $v14, $v15, $v16, $v17, $v18, $v19, $v20,
	    $v21, $v22, $v23, $v24, $v25, $v26, $v27, $v28, $v29, $v30,
	    $v31, $ums_user_id
	]);
	
	
  }

  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //die in der db hinterlegten einstellungen des spielers laden
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  $query = "SELECT * FROM de_user_comserver WHERE user_id=?";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$ums_user_id]);
  $num = mysqli_num_rows($db_daten);
  //wenn es noch keinen datensatz gibt, einen anlegen
  if($num==0)
  {
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_comserver SET user_id=?", [$ums_user_id]);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$ums_user_id]);
  }
  $playervalues = mysqli_fetch_assoc($db_daten);
  
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //die in der db hinterlegten einstellungen aller spieler laden
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  
  //daten über median ermitteln
  $time=time()-(3600*24*3);
  $query = "SELECT de_user_comserver.* FROM de_login LEFT JOIN de_user_comserver ON(de_login.user_id = de_user_comserver.user_id) 
  WHERE de_login.status=1 AND de_login.last_click > ? AND de_user_comserver.user_id>0";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $query, [$time]);
  
  if($_SESSION['ums_user_id']==1){
    //echo "SELECT de_user_comserver.* FROM de_login LEFT JOIN de_user_comserver ON(de_login.user_id = de_user_comserver.user_id) WHERE de_login.status=1 AND de_login.last_click > '$time' AND de_user_comserver.user_id>0";
  }

  //alle daten in ein array packen
  unset($votes);
  while($row = mysqli_fetch_assoc($db_daten))
  {
    for($i=1;$i<=40;$i++)if(!is_null($row['v'.$i]))$votes[$i][]=$row['v'.$i];
  }
  
  $server_v1=round(median($votes[1]));
  $server_v2=round(median($votes[2]));
  $server_v3=round(median($votes[3]));
  $server_v4=round(median($votes[4]));
  $server_v5=round(median($votes[5]));
  $server_v6=round(median($votes[6]));
  $server_v7=round(median($votes[7]));
  $server_v8=round(median($votes[8]));
  $server_v9=round(median($votes[9]));
  $server_v10=round(median($votes[10]));
  $server_v11=round(median($votes[11]));
  $server_v12=round(median($votes[12]));
  $server_v13=round(median($votes[13]));
  $server_v14=round(median($votes[14]));
  $server_v15=round(median($votes[15]));
  $server_v16=round(median($votes[16]));
  $server_v17=round(median($votes[17]));
  $server_v18=round(median($votes[18]));
  $server_v19=round(median($votes[19]));
  $server_v20=round(median($votes[20]));
  $server_v21=round(median($votes[21]));
  $server_v22=round(median($votes[22]));
  $server_v23=round(median($votes[23]));
  $server_v24=round(median($votes[24]));
  $server_v25=round(median($votes[25]));
  $server_v26=round(median($votes[26]));
  $server_v27=round(median($votes[27]));
  $server_v28=round(median($votes[28]));
  $server_v29=round(median($votes[29]));  
  $server_v30=round(median($votes[30]));  
  $server_v31=round(median($votes[31]));  
  $server_v32=round(median($votes[32]));  
  $server_v33=round(median($votes[33]));  
  $server_v34=round(median($votes[34]));  
  $server_v35=round(median($votes[35]));  
  
  //auswahloptionen anzeigen
  echo '<form action="optionscomserver.php" method="post">';
  rahmen_oben('Servereinstellungen');
  echo '<div style="width: 572px;" class="cell">';
  echo '<div style="width: 100%; text-align: center; font-size: 18px;">T&auml;gliche Einstellungen</div>';
  
  //wirtschaftstick
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Wirtschaftstick&Er wird immer zur vollen Stunde ausgef&uuml;hrt und dann, wie hier festgelegt, alle x Minuten bis zur n&auml;chsten vollen Stunde.
  <br><br>Erlaubte Werte: 1 bis 15 (Standard: 3)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Alle wie viele Minuten soll der Wirtschaftstick ausgef&uuml;hrt werden?
  <br>Tageswert: '.$sv_comserver_wt.' - Aktuelle Abstimmung: '.$server_v1.' - Deine Wahl: <input type="number" min="1" max="15" name="v1" value="'.$playervalues['v1'].'" size="10" maxlength="10">   
  </div>';
  
  //kampftick
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kampftick&Er wird immer zur vollen Stunde ausgef&uuml;hrt und dann, wie hier festgelegt, alle x Minuten bis zur n&auml;chsten vollen Stunde.
  <br><br>Erlaubte Werte: 1 bis 60 (Standard: 12)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Alle wie viele Minuten soll der Kampftick ausgef&uuml;hrt werden?
  <br>Tageswert: '.$sv_comserver_kt.' - Aktuelle Abstimmung: '.$server_v2.' - Deine Wahl: <input type="number" min="1" max="60" name="v2" value="'.$playervalues['v2'].'" size="10" maxlength="10">   
  </div>';  
  
  //rundendauer in wt
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Rundendauer&Sie wird in Wirtschaftsticks gemessen und sobald die Zeit um ist, beginnt die Erhabenenhaltezeit zu laufen.
  <br><br>Erlaubte Werte: 5000 bis 70000 (Standard: 33333)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie viele Wirtschaftsticks soll die Runde laufen?
  <br>Tageswert: '.$sv_winscore.' - Aktuelle Abstimmung: '.$server_v3.' - Deine Wahl: <input type="number" min="5000" max="70000" name="v3" value="'.$playervalues['v3'].'" size="10" maxlength="10">   
  </div>';  
  
  //erhabenenhaltezeit in wt
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Erhabenenhaltezeit&Sie wird in Wirtschaftsticks gemessen und sobald die Zeit um ist, ist die Runde vorbei.
  <br><br>Erlaubte Werte: 480 bis 2880 (Standard: 960)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie lang soll die Erhabenenhaltezeit sein?
  <br>Tageswert: '.$sv_benticks.' - Aktuelle Abstimmung: '.$server_v4.' - Deine Wahl: <input type="number" min="480" max="2880" name="v4" value="'.$playervalues['v4'].'" size="10" maxlength="10">   
  </div>';
  
  //inaktive spieler -> sektor 1
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Inaktivenentfernung&Inaktive Spieler werden nach der angegebenen Zeit in Sektor 1 verschoben.
  <br><br>Erlaubte Werte: 3 bis 10 (Standard: 7)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Nach wie vielen Tagen sollen inaktive Spieler in Sektor 1 verschoben werden?
  <br>Tageswert: '.$sv_inactiv_deldays.' - Aktuelle Abstimmung: '.$server_v5.' - Deine Wahl: <input type="number" min="3" max="10" name="v5" value="'.$playervalues['v5'].'" size="10" maxlength="10">   
  </div>';
  
  //punkteangriffsgrenze
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Angriffspunktegrenze&<br>Bis zu diesem Prozentwert der eigenen Punkte kann man Ziele angreifen.<br>Diese Grenze kann durch den Handel manipuliert werden und ist somit umgehbar und nicht sicher.
  <br><br>Erlaubte Werte: 10 bis 70 (Standard: 40)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die Angriffspunktegrenze sein?
  <br>Tageswert: '.($sv_attgrenze*100).'% - Aktuelle Abstimmung: '.$server_v6.'% - Deine Wahl: <input type="number" min="10" max="70" name="v6" value="'.$playervalues['v6'].'" size="10" maxlength="10">%   
  </div>';
  
  //sektorangriffspunktemalus
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Sektorangriffsgrenzenmalus (Punkte)&<br>F&uuml;r Angriffe auf Sektoren die im Sektorranking unter dem des eigenen Sektors stehen gilt dieser Malus auf die Angriffspunktegrenze. Diese Prozente verteilen sich &uuml;ber alle vorhandenen Spielersektoren, w&auml;ren also bei einer Sektoranzahl von 100 und 20% gleich 0,2% pro Sektor.<br>Diese Grenze kann durch den Handel manipuliert werden und ist somit umgehbar und nicht sicher.
  <br><br>Erlaubte Werte: 1 bis 40 (Standard: 20)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll der Sektorangriffsgrenzenmalus (Punkte) sein?
  <br>Tageswert: '.($sv_sector_attmalus*100).'% - Aktuelle Abstimmung: '.$server_v7.'% - Deine Wahl: <input type="number" min="1" max="40" name="v7" value="'.$playervalues['v7'].'" size="10" maxlength="10">%   
  </div>';  

  //maximale angriffsgrenze für kollektoren
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Maximimale Angriffskollektorengrenze&<br>Bis zu diesem Prozentwert (kann nicht unter die minimale Angriffskollektorgrenze fallen) der eigenen Kollektoren kann man Kollektoren von Zielen erhalten.<br>
  Je weniger Kollektoren man hat, desto geringer f&auml;llt auch die Grenze f&uuml;r einen selbst aus. Formel: Eigene Kollektorzahl / gr&ouml;te Kollektorzahl eines Spielers * Maximalwert der Kollektorangriffsgrenze 
  <br><br>Erlaubte Werte: 1 bis 70 (Standard: 35)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die Angriffskollektorengrenze maximal sein?
  <br>Tageswert: '.($sv_max_col_attgrenze*100).'% - Aktuelle Abstimmung: '.$server_v8.'% - Deine Wahl: <input type="number" min="1" max="70" name="v8" value="'.$playervalues['v8'].'" size="10" maxlength="10">%   
  </div>';  

  
  //minimale angriffsgrenze für kollektoren
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Minimale Angriffskollektorengrenze&<br>Bis zu diesem Prozentwert der eigenen Kollektoren kann man Kollektoren von Zielen erhalten.<br>
  <br><br>Erlaubte Werte: 1 bis 40 (Standard: 20)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die Angriffskollektorengrenze mindestens sein?
  <br>Tageswert: '.($sv_min_col_attgrenze*100).'% - Aktuelle Abstimmung: '.$server_v9.'% - Deine Wahl: <input type="number" min="1" max="40" name="v9" value="'.$playervalues['v9'].'" size="10" maxlength="10">%   
  </div>';  

  //bonus vom planetaren schild auf die hp der türme, wert in %
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Planetarer Schild&<br>Die T&uuml;rme erhalten die angegebenen Prozent mehr Stabilit&auml;t und es &uuml;berleben immer mindestens die angegebene Prozentzahl.<br>
  <br><br>Erlaubte Werte: 1 bis 20 (Standard: 10)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die St&auml;rke des Planetaren Schildes sein?
  <br>Tageswert: '.$sv_ps_bonus.'% - Aktuelle Abstimmung: '.$server_v10.'% - Deine Wahl: <input type="number" min="1" max="20" name="v10" value="'.$playervalues['v10'].'" size="10" maxlength="10">%   
  </div>';  

  //recyclotron ohne whg
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Recyclotronertrag&<br>Der Prozentwert gibt an, wie viel der eigenen Flotte bei K&auml;mpfen im Heimatsystem recycelt werden kann. Der maximale Wert Recyclotrons mit allen Boni liegt bei 80%.<br>
  <br><br>Erlaubte Werte: 1 bis 30 (Standard: 15)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die St&auml;rke des Recyclotrons ohne Weltraumhandelsgilde sein?
  <br>Tageswert: '.$sv_recyclotron_bonus.'% - Aktuelle Abstimmung: '.$server_v11.'% - Deine Wahl: <input type="number" min="1" max="30" name="v11" value="'.$playervalues['v11'].'" size="10" maxlength="10">%   
  </div>';  

  //recyclotron mit whg
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Recyclotronertrag&<br>Der Prozentwert gibt an, wie viel der eigenen Flotte bei K&auml;mpfen im Heimatsystem recycelt werden kann. Dieser Wert kann nicht kleiner sein als der Wert ohne Weltraumhandelsgilde. Der maximale Wert des Recyclotrons mit allen Boni liegt bei 80%.<br>
  <br><br>Erlaubte Werte: 1 bis 60 (Standard: 30)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll die St&auml;rke des Recyclotrons mit Weltraumhandelsgilde sein?
  <br>Tageswert: '.$sv_recyclotron_bonus_whg.'% - Aktuelle Abstimmung: '.$server_v12.'% - Deine Wahl: <input type="number" min="1" max="60" name="v12" value="'.$playervalues['v12'].'" size="10" maxlength="10">%   
  </div>';  

  //kollektorklaurate
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kollektoreroberungsrate&<br>Der Prozentwert gibt an, wie viele Kollektoren pro Angriffswelle erobert werden k&ouml;nnen.<br>
  <br><br>Erlaubte Werte: 5 bis 25 (Standard: 15)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie viel Prozent der Kollektoren sollen pro Angriff eroberbar sein?
  <br>Tageswert: '.($sv_kollie_klaurate*100).'% - Aktuelle Abstimmung: '.$server_v13.'% - Deine Wahl: <input type="number" min="5" max="25" name="v13" value="'.$playervalues['v13'].'" size="10" maxlength="10">%   
  </div>';
  
  //kollektorenergie
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kollektorenergie&<br>Der Wert gibt an, wie viele Energie ein Kollektor pro Wirtschaftstick liefert.<br>
  <br><br>Erlaubte Werte: 50 bis 150 (Standard: 100)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll der Energieertrag eines Kollektors sein?
  <br>Tageswert: '.$sv_kollieertrag.' - Aktuelle Abstimmung: '.$server_v14.' - Deine Wahl: <input type="number" min="50" max="150" name="v14" value="'.$playervalues['v14'].'" size="10" maxlength="10">   
  </div>';
  
  //kollektorenergie mit pa
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kollektorenergie-Premiumaccount&<br>Der Wert gibt an, wie viele Energie ein Kollektor pro Wirtschaftstick liefert, wenn der Spieler einen Premiumaccount hat. Der Wert kann nicht kleiner sein als der Wert ohne Premiumaccount.<br>
  <br><br>Erlaubte Werte: 50 bis 165 (Standard: 105)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll der Energieertrag eines Kollektors mit Premiumaccount sein?
  <br>Tageswert: '.$sv_kollieertrag_pa.' - Aktuelle Abstimmung: '.$server_v15.' - Deine Wahl: <input type="number" min="50" max="165" name="v15" value="'.$playervalues['v15'].'" size="10" maxlength="10">   
  </div>';  
  
  //kriegsartefakt energie
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kriegsartefaktenergie&<br>Der Wert gibt an, wie viele Energie ein Kriegsartefakt pro Wirtschaftstick liefert<br>
  <br><br>Erlaubte Werte: 50 bis 150 (Standard: 100)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie hoch soll der Energieertrag eines Kriegsartefakts sein?
  <br>Tageswert: '.$sv_kriegsartefaktertrag.' - Aktuelle Abstimmung: '.$server_v16.' - Deine Wahl: <input type="number" min="50" max="150" name="v16" value="'.$playervalues['v16'].'" size="10" maxlength="10">   
  </div>';

  //ben. punkte für kriegsartefakt - atter
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kriegsartefakte f&uuml;r Erfahrungspunkte bei K&auml;mpfen (Angreifer)&<br>Der Wert gibt an f&uuml;r wie viel Erfahrungspunkten bei K&auml;mpfen man jeweils ein Kriegsartefakt erh&auml;lt. Je kleiner der Wert, desto mehr Kriegsartefakte werden vergeben.<br>
  <br><br>Erlaubte Werte: 3000 bis 7000 (Standard: 5000)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  F&uuml;r wie viele Erfahrungspunkte bei K&auml;mpfen soll es jeweils ein Kriegsartefakt f&uuml;r Angreifer geben?
  <br>Tageswert: '.$sv_kartefakt_exp_atter.' - Aktuelle Abstimmung: '.$server_v17.' - Deine Wahl: <input type="number" min="3000" max="7000" name="v17" value="'.$playervalues['v17'].'" size="10" maxlength="10">   
  </div>';  

  //ben. punkte für kriegsartefakt - deffer
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Kriegsartefakte f&uuml;r Erfahrungspunkte bei K&auml;mpfen (Verteidiger)&<br>Der Wert gibt an f&uuml;r wie viel Erfahrungspunkten bei K&auml;mpfen man jeweils ein Kriegsartefakt erh&auml;lt. Je kleiner der Wert, desto mehr Kriegsartefakte werden vergeben.<br>
  <br><br>Erlaubte Werte: 3000 bis 7000 (Standard: 4500)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  F&uuml;r wie viele Erfahrungspunkte bei K&auml;mpfen soll es jeweils ein Kriegsartefakt f&uuml;r Verteidiger geben?
  <br>Tageswert: '.$sv_kartefakt_exp_deffer.' - Aktuelle Abstimmung: '.$server_v18.' - Deine Wahl: <input type="number" min="3000" max="7000" name="v18" value="'.$playervalues['v18'].'" size="10" maxlength="10">   
  </div>';  
  
  //max palenium
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Paleniumlagermenge&<br>Der Wert gibt an wie viel Palenium man maximal lagern kann.<br>
  <br><br>Erlaubte Werte: 10 bis 400 (Standard: 100)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie gro&szlig; soll das Paleniumlager maximal sein?
  <br>Tageswert: '.$sv_max_palenium.' - Aktuelle Abstimmung: '.$server_v19.' - Deine Wahl: <input type="number" min="10" max="400" name="v19" value="'.$playervalues['v19'].'" size="10" maxlength="10">   
  </div>';

  /////////////////////////////////////////////////////////////
  // einstellungen für die nächste runde
  /////////////////////////////////////////////////////////////
  
  echo '<br><div style="width: 100%; text-align: center; font-size: 18px;">Einstellungen f&uuml;r die n&auml;chste Runde</div>';
  
  //rundentyp - normal oder battleround
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v21<=0.5)$rundenwahl='Normal';else $rundenwahl='Battleround';
  if($sv_comserver_roundtyp==0)$rundentyp='Normal';else $rundentyp='Battleround';
  
  if(is_null($playervalues['v21'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v21']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}

  $dropdown='
   <select name="v21">
   	  <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>Normal</option>
      <option value="1"'.$checked[2].'>Battleround</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Rundenart (Normal oder Battleround)&In der normalen Runde baut und erforscht man alles und versucht gr&ouml;&szliger zu werden, in der Battleround starten alle Spieler mit tausenden von Kollektoren und extrem vielen Rohstoffen. Die Battleround ist auf den Kampf ausgerichtet.<br><br>
  Erlaubte Werte: Normal oder Battleround (Standard: Normal)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Soll die n&auml;chste Runde eine normale Runde oder eine Battleround sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  
  

  //sektortyp - zufall oder wahlsektoren
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v22<=0.5)$rundenwahl='Zufall';else $rundenwahl='Wahl';
  if($sv_max_secmoves==0)$rundentyp='Zufall';else $rundentyp='Wahl';
  if(is_null($playervalues['v22'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v22']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
    $dropdown='
   <select name="v22">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>Zufall</option>
      <option value="1"'.$checked[2].'>Wahl</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Sektorart (Zufalls- oder Wahlsektoren)&Bei Zufallssektoren werden die Spieler zuf&auml;llig einem Sektor zugewiesen, sobald sie aus Sektor 1 kommen. Bei Wahlsektoren kann ein Spieler einen Sektor gr&uuml;nden und dann dorthin weitere Spieler einladen.
  <br>Erlaubte Werte: Zufall oder Wahl (Standard: Zufall)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie sollen in der n&auml;chsten Runde die Sektoren sein (Zufall oder Wahl)?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';

  
  //anzahl der spieler pro sektor
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Sektorgr&ouml;&szlig;e&<br>Der Wert gibt an, wie viele Spieler sich in einem Sektor befinden k&ouml;nnen.<br>
  <br><br>Erlaubte Werte: 1 bis 20 (Standard: 10)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Wie soll die Sektorgr&ouml;&szlig;e sein?
  <br>Aktuelle Runde: '.$sv_maxsystem.' - Aktuelle Abstimmung: '.$server_v23.' - Deine Wahl: <input type="number" min="1" max="20" name="v23" value="'.$playervalues['v23'].'" size="10" maxlength="10">   
  </div>';
  
  //handel aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v24<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_trade==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v24'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v24']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
  $dropdown='
   <select name="v24">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Handel&&Uuml;ber den Handel k&ouml;nnen Raumschiffe und Rohstoffe gehandelt werden. Dar&uuml;ber ist auch eine Manipulation der Angriffspunktegrenze m&ouml;glich.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Soll der Handel aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  

  //religion aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v25<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_religion==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v25'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v25']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
    $dropdown='
   <select name="v25">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Bonuspunkte&&Uuuml;ber Bonuspunkte k&ouml;nnen verschiedenste Boni erhalten werden.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Sollen Bonuspunkte aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  

  //geheimdienst aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v26<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_secret==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v26'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v26']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
    $dropdown='
   <select name="v26">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Geheimdienst&Er dient dazu andere Spieler auszuspionieren und zu sabotieren.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Soll der Geheimdienst aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  
 
  //schwarzmarkt aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v27<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_blackmarket==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v27'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v27']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
  $dropdown='
   <select name="v27">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Schwarzmarkt&Dort k&ouml;nnen Ingamevorteile gegen Credits erworben werden.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Soll der Schwarzmarkt aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';

  //sektorartefakte aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v28<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_sectorartefacts==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v28'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v28']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
    $dropdown='
   <select name="v28">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Sektorartefakte&Sie geben einem Sektore bestimmte Vorteile.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Sollen Sektorartefakte aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';

  //Mission aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v29<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_missions==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v29'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v29']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
  $dropdown='
   <select name="v29">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Missionen&Dort k&ouml;nnen Missionen gestartet werden.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Sollen Missionen aktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  

  //Vergessene Systeme aktiv
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v30<=0.5)$rundenwahl='nein';else $rundenwahl='ja';
  if($sv_deactivate_missions==0)$rundentyp='nein';else $rundentyp='ja';
  if(is_null($playervalues['v30'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v30']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
  $dropdown='
   <select name="v30">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Vergessene Systeme&Dort k&ouml;nnen Geb&auml;ude gebaut und Rohstoffe gefunden werden. Zus&auml;tzlich gibt es die Battlegrounds.
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Sollen die Vergessenen Systeme inaktiv sein?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';

  //FP im Sektorstatus ausblenden
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  if($server_v31<=0.5)$rundenwahl='ja';else $rundenwahl='nein';
  if($sv_deactivate_missions==0)$rundentyp='ja';else $rundentyp='nein';
  if(is_null($playervalues['v31'])){$checked[0]=' selected';$checked[1]='';$checked[2]='';}
  elseif($playervalues['v31']==0){$checked[0]='';$checked[1]=' selected';$checked[2]='';}
  else{$checked[0]='';$checked[1]='';$checked[2]=' selected';}
  $dropdown='
   <select name="v31">
      <option value=""'.$checked[0].'>nichts gew&auml;hlt</option>
      <option value="0"'.$checked[1].'>ja</option>
      <option value="1"'.$checked[2].'>nein</option>
    </select>';  
  echo '<div style="width: 100%; padding: 5px;" class="'.$bg.'">
  <img title="Flottenpunkte im Sektorstatus&Sollen die Flottenpunkte im Sektorstatus ausgeblendet werden?
  <br>Erlaubte Werte: ja oder nein (Standard: ja)" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">
  Sollen die Flottenpunkte im Sektorstatus ausgeblendet werden?
  <br>Aktuelle Runde: '.$rundentyp.' - Aktuelle Abstimmung: '.$rundenwahl.' - Deine Wahl: '.$dropdown.'   
  </div>';  


  //Einstellungen speichern
  echo '<br><br><div style="width: 100%; text-align: center;"><input type="Submit" name="save" value="Einstellungen speichern"></div><br>';  
  echo rahmen_unten();
  echo '</form>';

}

//include "fooban.php"; 

?>
</body>
</html>