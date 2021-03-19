<?php
set_time_limit(240);
include 'mysql_wrapper.inc.php';
include "croninfo.inc.php";

include "../lib/efta_dbconnect.php";
?>
<html>
<head>
</head>
<body>
<?php
//startet den zufallsgenerator
srand((double)microtime()*1000000);
mt_srand((double)microtime()*10000);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    tagesspendenwert sichern
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//jede nacht um 1 uhr 10, von den anderen servern kommt die abfrage um 1 uhr bzgl. der leistung
if(intval(strftime("%M"))==10 AND intval(strftime("%H"))==1)
{
  echo '<br>donatelastday';
  mysql_query("UPDATE de_cyborg_data SET explastday=exp",$eftadb);
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    monsterspawnpunkte bearbeiten
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll min�tlich arbeiten, also wie im cron

  $result = mysql_query("SELECT * FROM de_cyborg_enm_spawn WHERE 1", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Spawnpunkte insgesamt: '.$num;
  //alle spawn-ids durchgehen
  while($row = mysql_fetch_array($result))
  {
  	$spawn_id=$row["id"];
    $xvon=$row["xvon"];
    $xbis=$row["xbis"];
    $yvon=$row["yvon"];
    $ybis=$row["ybis"];
    $lvlmin=$row["lvlmin"];
    $lvlmax=$row["lvlmax"];
    $anzmin=$row["anzmin"];
    $anzmax=$row["anzmax"];
    $z=$row["z"];

  	//schauen ob gegner platziert werden m�ssen, dazu auslesen wieviele monster es mit der spawn-id gibt
    $eftadb_data = mysql_query("SELECT * FROM de_cyborg_enm_map WHERE spawn_id='$spawn_id'", $eftadb);
    $anz_enm = mysql_num_rows($eftadb_data);
  	
    //wenn es weniger als das untere limit gibt bis zu diesem auff�llen
    if($anz_enm<$anzmin)
    {
      $anz=$anzmin-$anz_enm;
      //position per zufall bestimmen
      $eftadb_data = mysql_query("SELECT x,y FROM de_cyborg_map WHERE x>='$xvon' AND x<='$xbis' AND y>='$yvon' AND y<='$ybis' AND z='$z' ORDER BY RAND() LIMIT $anz", $eftadb);
      while($rowx = mysql_fetch_array($eftadb_data))
      {
      	//zielkoordinaten auslesen
      	$xpos=$rowx["x"];
      	$ypos=$rowx["y"];
      	//gegner-id per zufall bestimmen
        $enm_res = mysql_query("SELECT * FROM de_cyborg_enm_list WHERE level>='$lvlmin' AND level<='$lvlmax' ORDER BY RAND() LIMIT 0,1", $eftadb);
        $num = mysql_num_rows($enm_res);
        //echo $num;
        if($num==1)
        {
          //daten auslesen
          $enm_row = mysql_fetch_array($enm_res);
          $enm_id=$enm_row["id"];
          
          //gegner in der db hinterlegen
      	  mysql_query("INSERT INTO de_cyborg_enm_map (x, y, z, spawn_id, enm_id) VALUES ('$xpos', '$ypos', '$z', '$spawn_id', '$enm_id')",$eftadb);
      	  echo "INSERT INTO de_cyborg_enm_map (x, y, z, spawn_id, enm_id) VALUES ('$xpos', '$ypos', '$z', '$spawn_id', '$enm_id')<br>";
        }      	
      }
    }
    elseif($anz_enm<$anzmax)//das minimum ist erreicht, schauen ob man unterm maximum ist und ggf. einen gegner platzieren
    {
      $eftadb_data = mysql_query("SELECT x,y FROM de_cyborg_map WHERE x>='$xvon' AND x<='$xbis' AND y>='$yvon' AND y<='$ybis' AND z='$z' ORDER BY RAND() LIMIT 1", $eftadb);
      while($rowx = mysql_fetch_array($eftadb_data))
      {
      	//zielkoordinaten auslesen
      	$xpos=$rowx["x"];
      	$ypos=$rowx["y"];
      	//gegner-id per zufall bestimmen
        $enm_res = mysql_query("SELECT * FROM de_cyborg_enm_list WHERE level>='$lvlmin' AND level<='$lvlmax' ORDER BY RAND() LIMIT 0,1", $eftadb);
        $num = mysql_num_rows($enm_res);
        //echo $num;
        if($num==1)
        {
          //daten auslesen
          $enm_row = mysql_fetch_array($enm_res);
          $enm_id=$enm_row["id"];
          
          //gegner in der db hinterlegen
      	  mysql_query("INSERT INTO de_cyborg_enm_map (x, y, z, spawn_id, enm_id) VALUES ('$xpos', '$ypos', '$z', '$spawn_id', '$enm_id')",$eftadb);
      	  echo "INSERT INTO de_cyborg_enm_map (x, y, z, spawn_id, enm_id) VALUES ('$xpos', '$ypos', '$z', '$spawn_id', '$enm_id')<br>";
        }      	
      }
    }
  }


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    auf veraltete geb�ude testen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll alle 10 minuten arbeiten
if((strftime("%M") % 10)==0)
{
  echo '<hr><br>Geb�ude altern lassen<br>';
  //festlegen wie schnell ein geb�ude verf�llt
  $time=time()-21*24*3600;
  
  $result = mysql_query("SELECT * FROM de_cyborg_struct WHERE bldgtime>0 AND bldgtime<$time AND (bldgid=5 OR bldgid=6 OR bldgid=7 OR bldgid=9 OR bldgid=10 OR bldgid=11 OR bldgid=12 OR bldgid=13)", $eftadb);
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    
    //die kartendaten auslesen
    $resultmap = mysql_query("SELECT * FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
    $rowmap = mysql_fetch_array($resultmap);
    $fieldlevel=$rowmap["fieldlevel"];
    $fieldamount=$rowmap["fieldamount"];
    
    echo $fieldlevel;
    //echo $rowmap["bldg"];
	
    //�berpr�fen ob es das geb�ude schlechter wird, oder ganz zerst�rt
    if($fieldlevel>1)//es wird kleiner
    {
      $fieldlevel--;
      if($fieldamount>$fieldlevel)$fieldamount=$fieldlevel;
      mysql_query("UPDATE de_cyborg_map SET fieldamount='$fieldamount', fieldlevel='$fieldlevel' WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
      //zeit updaten, damit man wieder eine zeitlang hat, bevor es down geht
      $time=time();
      mysql_query("UPDATE de_cyborg_struct SET bldgtime='$time' WHERE x='$x' AND y='$y' AND z='$z'",$eftadb);
      echo 'A';
    }
    else //es wird zerst�rt
    {
      //das geb�ude auf der karte entfernen
      mysql_query("UPDATE de_cyborg_map SET bldg=0, bldgpic=0, fieldamount=0, fieldlevel=0,  WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);	
      //den eintrag aus de_cyborg_struct l�schen
      mysql_query("DELETE FROM de_cyborg_struct WHERE x='$x' AND y='$y' AND z='$z' LIMIT 1",$eftadb);
      echo 'B';
    }
    
    
  }
  
  
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    rohstoffwachstum
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll alle 10 minuten arbeiten
if((strftime("%M") % 10) ==0)
{
  echo '<hr><br>Wald wachsen lassen<br>';
  //anzahl der gesamten w�lder auslesen
  //10% des waldes hat die chance zu wachsen, wegen der performance nur wald bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=15 AND bldg=9 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Wald gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=50+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }

  echo '<hr><br>Felder wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=5 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Felder gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }

  echo '<hr><br>Mine wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=6 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Mine gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }
  
  echo '<hr><br>Steinbruch wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=7 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Steinbruch gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }
  
  echo '<hr><br>Erzschmelze wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=10 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Erzschmelze gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }

  echo '<hr><br>Windm�hle wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=11 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Windm�hle gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }  
  
  echo '<hr><br>Brunnen wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=12 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Brunnen gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }  

  echo '<hr><br>B�ckerei wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=13 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>B�ckerei gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }  
  
  echo '<hr><br>Destille wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=14 AND fieldamount < fieldlevel", $eftadb);
  $num = mysql_num_rows($result);
  echo '<br>Destille gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $eftadb);
  }
}
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    dornschlingenpflanze soll wachsen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll alle 10 minuten arbeiten
if((strftime("%M") % 15)==0)
{
$num=0;
echo '<hr><br>Dornenschlinenpflanze wachsen lassen<br>';
$result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=18 ORDER BY RAND() LIMIT 2", $eftadb);
while($row = mysql_fetch_array($result))
{
  $x=$row["x"];
  $y=$row["y"];
  $z=$row["z"];
  $fieldlevel=$row["fieldlevel"];

   echo 'a'.$x.':'.$y.':';
  
  //schauen, ob es ein freies nachbarfeld gibt
  $brangexa=$x-1;
  $brangexe=$x+1;
  $brangeya=$y-1;
  $brangeye=$y+1;
  //daten aus der db holen
  $found=0;
  $eftadb_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x>='$brangexa' AND x<='$brangexe' AND y>='$brangeya' AND y<='$brangeye' AND z='$map' ORDER BY RAND()",$eftadb);
  while($rowx = mysql_fetch_array($eftadb_daten))
  {
    //schauen ob im nachbarfeld platz f�r eine weitere pflanze ist
    if($found==0 AND $rowx["groundtyp"]==1 AND $rowx["groundpicext"]==0 AND $rowx["bldg"]==0 AND $rowx["bldgpic"]==0)
    {
      $rx=$rowx["x"];
      $ry=$rowx["y"];
      $rz=$rowx["z"];

      //die schlingpflanze setzen
      mysql_query("UPDATE de_cyborg_map SET bldg=18, bldgpic=34 WHERE x='$rx' AND y='$ry' AND z='$rz' AND bldg=0", $eftadb);
      echo "UPDATE de_cyborg_map SET bldg=18, bldgpic=34 WHERE x='$rx' AND y='$ry' AND z='$rz' AND bldg=0<br><br>";
      $num++;
      $found=1;
    }
  }
  if($found==0) //es ist kein platz vorhanden, also pflanze st�rker machen
  {
    //schauen ob es schon auf dem maxlevel ist und nur dann wachen lassen
    if($fieldlevel<3)
    {
      mysql_query("UPDATE de_cyborg_map SET fieldlevel=fieldlevel+1, bldgpic=bldgpic+1 WHERE x='$x' AND y='$y' AND z='$z' AND bldg=18", $eftadb);
      echo "UPDATE de_cyborg_map SET fieldlevel=fieldlevel+1, bldgpic=bldgpic+1 WHERE x='$x' AND y='$y' AND z='$z' AND bldg=18<br>";
    }
    else //es ist max und sie verdorrt wieder
    {
      mysql_query("UPDATE de_cyborg_map SET bldg=0, bldgpic=0, fieldlevel=0 WHERE x='$x' AND y='$y' AND z='$z' AND bldg=18", $eftadb);
      echo "UPDATE de_cyborg_map SET bldg=0, bldgpic=0, fieldlevel=0 WHERE x='$x' AND y='$y' AND z='$z' AND bldg=18<br>";
    }
  }
}  
echo '<br>Dornschlingenpflanze gewachsen:'.$num;
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    auktionshaus
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll min�tlich arbeiten, also wie im cron
echo '<hr><br>Auktionen bearbeiten<br>';
//abgelaufene auktionen bearbeiten
$sql="SELECT * FROM de_cyborg_auktion WHERE time < UNIX_TIMESTAMP()";
$result = mysql_query($sql, $eftadb);
$itemmenge = mysql_num_rows($result);
echo 'Auktionen: '.$itemmenge.'<br><br>';
while($row = mysql_fetch_array($result))
{
  $id=$row["id"];
  $seller=$row["seller"];
  $bidder=$row["bidder"];
  $price=$row["price"];
  $dprice=$row["dprice"];
  $itemid=$row["itemid"];
  $itemtyp=$row["itemtyp"];

  //nur was machen, wenn jemand geboten hat
  if($bidder>0)
  {
    echo '<br>Auktions-ID: '.$id;
    echo '<br>es wurde geboten';
    //dem verk�ufer das geld gutschreiben, wenn price null ist, dann ist es ein sofortkauf
    if($price==0)$preis=$dprice;else $preis=$price;
    echo '<br>Verk�ufer: '.$seller;
    echo '<br>Preis: '.$preis;
    $sql="UPDATE de_cyborg_item SET amount=amount+'$preis' WHERE equip=0 AND typ=20 AND id=1 AND user_id='$seller'";
    mysql_query($sql, $eftadb);
    echo '<br>SQL: '.$sql;
    

    //dem k�ufer den gegenstand �bertragen
    $item_durability=0;
    //$itemid=$shopangebot[$i];
    $filename='../items/'.$itemid.'.item';
    include($filename);

    echo '<br>K�ufer: '.$bidder;
    echo '<br>Item-ID: '.$itemid;
    echo '<br>Item-Typ: '.$itemtyp;
    $sql="INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$bidder', '$itemid', '$itemtyp', '1', '$item_durability')";
    mysql_query($sql, $eftadb);
    echo '<br>SQL: '.$sql;

    //die auktion l�schen
    $sql="DELETE FROM de_cyborg_auktion WHERE id='$id'";
    mysql_query($sql, $eftadb);
    echo '<br>SQL: '.$sql;
  }
  else
  {
    //es hat niemand geboten, also einfach wieder dem anbieter zur�ckgeben
    echo '<br>es wurde nicht geboten';
    //dem verk�ufer den gegenstand zur�ckbuchen
    $item_durability=0;
    //$itemid=$shopangebot[$i];
    $filename='../items/'.$itemid.'.item';
    include($filename);
    
    echo '<br>Verk�ufer: '.$seller;
    echo '<br>Item-ID: '.$itemid;
    echo '<br>Item-Typ: '.$itemtyp;
    $sql="INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$seller', '$itemid', '$itemtyp', '1', '$item_durability')";
    mysql_query($sql, $eftadb);
    echo '<br>SQL: '.$sql;

    //die auktion l�schen
    $sql="DELETE FROM de_cyborg_auktion WHERE id='$id'";
    mysql_query($sql, $eftadb);
    echo '<br>SQL: '.$sql;
  }

  echo '<br><br>';
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    arenakampf
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll alle 5 minuten laufen
if((strftime("%M") % 5) ==0)
{
echo '<hr><br>Arenakampf<br>';
$trefferwahrscheinlichkeitliste = array(50,55,60,62,64,67,70,72,74,75,76,77,78,79,80,81,82,83,84,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85
,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85);
$trefferwahrscheinlichkeit=$trefferwahrscheinlichkeitliste[$level-1];

$result = mysql_query("SELECT * FROM de_cyborg_data WHERE arena=1 ORDER BY exp DESC",$eftadb);
$anz = mysql_num_rows($result);
//immer zwei k�mpfen lassen
for($i=0;$i<$anz;$i=$i+2)
{
  //zuerst schauen ob es nen partner gibt
  if($i<$anz-1)
  {
    $user[0] = mysql_result($result, $i,"user_id");
    $user[1] = mysql_result($result, $i+1,"user_id");
    $userhp[0] = mysql_result($result, $i,"hp");
    $userhp[1] = mysql_result($result, $i+1,"hp");
    $userlevel[0] = mysql_result($result, $i,"level");
    $userlevel[1] = mysql_result($result, $i+1,"level");
    $usersname[0] = mysql_result($result, $i,"spielername");
    $usersname[1] = mysql_result($result, $i+1,"spielername");

    //ausr�stung spieler 1 und 2 laden
    for($c=0;$c<=1;$c++)
    {
      $uid=$user[$c];
      $resultc = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=1 AND user_id='$uid'", $eftadb);
      $cyborg_armor[$c]=0;
      $cyborg_mindmg[$c]=0;
      $cyborg_maxdmg[$c]=0;
      while($row = mysql_fetch_array($resultc))
      {
        //erstmal alles nullen
        $item_armor=0;
        $item_mindmg=0;
        $item_maxdmg=0;

        $itemid=$row["id"];
        $filename='../items/'.$itemid.'.item';
        include($filename);
        //werte zusammenz�hlen
        $cyborg_armor[$c]+=$item_armor;
        $cyborg_mindmg[$c]+=$item_mindmg;
        $cyborg_maxdmg[$c]+=$item_maxdmg;
      }
    }
    //wer geschickter ist greift zuerst an, ansonsten per zufall
    if(mysql_result($result, $i,"dex")>mysql_result($result, $i+1,"dex"))$erstschlag=0;else $erstschlag=1;
    if(mysql_result($result, $i,"dex")==mysql_result($result, $i+1,"dex"))$erstschlag=mt_rand(0, 1);
    
    //da die r�stungen f�r npcs ausgelegt sind deren wert teilen
    //$cyborg_armor[0]=$cyborg_armor[0];
    //$cyborg_armor[1]=$cyborg_armor[1];
    
	//zuerst schauen wie der standardr�stungswert ist, den der gegner erwartet
	$standardruestwert[0]=$userlevel[1]*50*2;
	$standardruestwert[1]=$userlevel[0]*50*2;
    //die eigene/fremde r�stung in relation dazu setzen
    //user 0
    $schadenssenkung[0]=$cyborg_armor[0]*100/$standardruestwert[0];
    if($userlevel[1]>$userlevel[0])$schadenssenkung[0]=$schadenssenkung[0]-(($userlevel[1]-$userlevel[0])*10);
    if($schadenssenkung[0]<0)$schadenssenkung[0]=0;
    if($schadenssenkung[0]>100)$schadenssenkung[0]=100;
	//user 1
    $schadenssenkung[1]=$cyborg_armor[1]*100/$standardruestwert[1];
    if($userlevel[0]>$userlevel[1])$schadenssenkung[1]=$schadenssenkung[1]-(($userlevel[0]-$userlevel[1])*10);
    if($schadenssenkung[1]<0)$schadenssenkung[1]=0;
    if($schadenssenkung[1]>100)$schadenssenkung[1]=100;

    
    /*echo $user[0].':'.$user[1].'<br>';
    echo $cyborg_mindmg[0].':'.$cyborg_maxdmg[0].'<br>';
    echo $cyborg_mindmg[1].':'.$cyborg_maxdmg[1].'<br><br>';*/
    //es werden maximal 10 runden gek�mpft
    $maxrunden=50;$haswon=0;
    for($r=0;$r<$maxrunden;$r++)
    {
      //gegnerschaden berechnen
      for($c=0;$c<=1;$c++)
      {
        if ($trefferwahrscheinlichkeitliste[mysql_result($result, $i+$c,"level")-1]> mt_rand(0, 100))
        {

          //$schaden[$c]=mt_rand($cyborg_mindmg[$c], $cyborg_maxdmg[$c])-$cyborg_armor[$c]+mysql_result($result, $i+$c,"str");
          $schaden[$c]=round((mysql_result($result, $i+$c,"str")*1.5)+mt_rand($cyborg_mindmg[$c], $cyborg_maxdmg[$c]));
          if($c==0)$schadenssenkung_enm=$schadenssenkung[1];
          elseif($c==1)$schadenssenkung_enm=$schadenssenkung[0];
          $schaden[$c]=round($schaden[$c]*(100-$schadenssenkung_enm)/100);
          
          if ($schaden[$c]<0)$schaden[$c]=0;
          //echo 'treffer'.$schaden[$c].'<br>';
        }
        else $schaden[$c]=0;
      }

      if($erstschlag==0)
      {
        //player 1 schl�gt zu
        if($userhp[1]-$schaden[0]<=0)
        {
          //player 2 hat verloren
          $haswon=1;
        }
        else
        {
          //player 2 hp abziehen
          $userhp[1]-=$schaden[0];
        }

        //player 2schl�gt zu
        if($userhp[0]-$schaden[1]<=0 AND $haswon==0)
        {
          //player 1 hat verloren
          $haswon=2;
        }
        else
        {
          //player 2 hp abziehen
          $userhp[0]-=$schaden[1];
        }
      }
      else
      {
        //player 2schl�gt zu
        if($userhp[0]-$schaden[1]<=0)
        {
          //player 1 hat verloren
          $haswon=2;
        }
        else
        {
          //player 2 hp abziehen
          $userhp[0]-=$schaden[1];
        }
        
        //player 1 schl�gt zu
        if($userhp[1]-$schaden[0]<=0 AND $haswon==0)
        {
          //player 2 hat verloren
          $haswon=1;
        }
        else
        {
          //player 2 hp abziehen
          $userhp[1]-=$schaden[0];
        }
      }
      
      if ($hp<=0) //man ist tot
      {
        /*
        //daten zur�cksetzen
        mysql_query("UPDATE de_cyborg_data set bewpunkte = 0, hp=hpmax, map=1, x=3, y=3 WHERE user_id='$ums_user_id'",$eftadb);
        //gegner l�schen
        mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$ums_user_id'",$eftadb);
        echo 'Sie wurden besiegt und der Nottransmitter bringt den Cyborg zum Startpunkt zur�ck.<br><br>';*/
      }
      //wenn jemand gewonnen hat kampf abbrechen
      if($haswon>0)$r=$maxrunden;

      if($r==($maxrunden-1) AND $haswon==0)//unentschieden, gewinner wird ausgelost
      {
        $haswon=mt_rand(1, 2);
        //echo 'zufall';
      }
      //echo '<br>HP1: '.$userhp[0].'<br>';
      //echo 'HP2: '.$userhp[1].'<br>';
    }//ende maxrunden

    //k�mpfer updaten
    echo '<br>haswon: '.$haswon.'<br>';
    //gewinner updaten
    if($haswon==1){$uid=$user[0];$hashp=$userhp[0];$sname=$usersname[1];}else {$uid=$user[1];$hashp=$userhp[1];$sname=$usersname[0];}
    $fame=2;$arenawon=1;$arenalost=0;
    if($hashp<=0)$hashp=1;
    $showmsg='Der Sieg ist dein, du hast beim Arenakampf &uuml;ber '.$sname.' triumphiert. Als Gewinner erh&auml;ltst du zwei Ruhmespunkte und einen Arenaheiltrank.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, hp='$hashp', fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$eftadb);
    //heiltrank ins gep�ck packen
    mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$uid', '4843', '21', '1')",$eftadb);
    //verlierer updaten
    if($haswon==1){$uid=$user[1];$hashp=$userhp[1];$sname=$usersname[0];}else {$uid=$user[0];$hashp=$userhp[0];$sname=$usersname[1];}
    $fame=1;$arenawon=0;$arenalost=1;
    if($hashp<=0)$hashp=1;
    $showmsg='Der Sieg beim Arenakampf geh�rt deinem Gegner '.$sname.', jedoch erh&auml;ltst du einen Ruhmespunkt und einen Arenaheiltrank.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, hp='$hashp', fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$eftadb);
    //heiltrank ins gep�ck packen
    mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$uid', '4843', '21', '1')",$eftadb);
  }
  else //freikampf
  {
    $uid = mysql_result($result, $i,"user_id");
    //spieler updaten
    $fame=1;$arenawon=0;$arenalost=0;
    $showmsg='Es konnte beim Arenakampf kein passender Gegner f&uuml;r dich gefunden werden, jedoch erh&auml;ltst du trotzdem einen Ruhmespunkt.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$eftadb);
    mysql_query("UPDATE de_cyborg_data SET arena=0", $eftadb);
  }
}
}
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
// karte erzeugen
/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
if((strftime("%M") % 17)==0)
{
$startzeit=time();

//bild im speicher anlegen
$im = imagecreatefrompng("blue2000x2000.png");

//farben definieren
$farbe[0]=imagecolorallocate($im, 0, 0, 0);//schwarz
$farbe[1]=imagecolorallocate($im, 255, 255, 255);//wei�
$farbe[2]=imagecolorallocate($im, 51, 255, 89);//hellgr�n
$farbe[3]=imagecolorallocate($im, 14, 148, 46);//dunkelgr�n
$farbe[4]=imagecolorallocate($im, 128, 128, 128);//grau
$farbe[5]=imagecolorallocate($im, 255, 0, 0);//rot portal
$farbe[6]=imagecolorallocate($im, 255, 255, 64);//gelb h�fen
$farbe[7]=imagecolorallocate($im, 255, 64, 255);//lila transmitterpunkt
$farbe[8]=imagecolorallocate($im, 0, 0, 0);//schwarz lebenssaugende pflanze

//$farbe[]=imagecolorallocate($im, , , );//

//mapdaten aus der db holen und auswerten
$result = mysql_query("SELECT * FROM de_cyborg_map WHERE z=0", $eftadb);
while($row = mysql_fetch_array($result))
{
  //grundtyp ist normal immer hellgr�n
  $f=2;
  
  //daten auslesen
  $x=$row["x"]+1000;
  $y=$row["y"]*-1+1000;
  $groundtyp=$row["groundtyp"];
  $bldg=$row["bldg"];
  
  
  if($groundtyp==14){$f=4;$gebirge++;} //gebirge
  if($groundtyp==15){$f=3;$wald++;} //wald
  
  //wenn es normaler boden ist, dann k�nnte da ein geb�ude stehen
  if($f==2)
  {
  	if($bldg==3)$f=7; //transmitter
  	elseif($bldg==16)$f=6; //hafen
  	elseif($bldg==2)$f=5; //portal
  	elseif($bldg==1)$f=1; //stadt
  	//elseif($bldg==18)$f=8; //lebenssaugende pflanze
  }
  
  $starcounter++;
  imagesetpixel($im, $x, $y, $farbe[$f]);  
}

imagepng($im, "eftamap2000x2000.png");
echo 'Gesamt: '.$starcounter.' Gebirge: '.$gebirge.' Wald: '.$wald;
echo '<br>Laufzeit '.(time()-$startzeit).'Sekunden.';

}
?>
</body>
</html>