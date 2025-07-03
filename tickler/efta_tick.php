<?php
set_time_limit(240);
$directory="../";

include $directory."inccon.php";
include $directory."inc/sv.inc.php";
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
//    rohstoffwachstum
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll alle 10 minuten arbeiten
if((strftime("%M") % 10) ==0 OR 1==1)
{
  echo '<hr><br>Wald wachsen lassen<br>';
  //anzahl der gesamten wälder auslesen
  //10% des waldes hat die chance zu wachsen, wegen der performance nur wald bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=15 AND bldg=9 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }

  echo '<hr><br>Felder wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=5 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }

  echo '<hr><br>Mine wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=6 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }
  
  echo '<hr><br>Steinbruch wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=7 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }
  
  echo '<hr><br>Erzschmelze wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=10 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }

  echo '<hr><br>Windmühle wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=11 AND fieldamount < fieldlevel", $db);
  $num = mysql_num_rows($result);
  echo '<br>Windmühle gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }  
  
  echo '<hr><br>Brunnen wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=12 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }  

  echo '<hr><br>Bäckerei wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=13 AND fieldamount < fieldlevel", $db);
  $num = mysql_num_rows($result);
  echo '<br>Bäckerei gewachsen:'.$num;
  while($row = mysql_fetch_array($result))
  {
    $x=$row["x"];
    $y=$row["y"];
    $z=$row["z"];
    $fieldlevel=$row["fieldlevel"];
    $fieldamount=$row["fieldamount"];
    if($fieldamount<$fieldlevel AND mt_rand(1,100)<=20+$fieldlevel)
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }  
  
  echo '<hr><br>Destille wachsen lassen<br>';
  //10% der felder hat die chance zu wachsen, wegen der performance nur felder bearbeiten, wo was wachsen kann
  $result = mysql_query("SELECT * FROM de_cyborg_map WHERE groundtyp=1 AND bldg=14 AND fieldamount < fieldlevel", $db);
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
    mysql_query("UPDATE de_cyborg_map SET fieldamount=fieldamount + 1 WHERE x='$x' AND y='$y' AND z='$z'", $db);
  }    
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//    auktionshaus
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//soll minütlich arbeiten, also wie im cron
echo '<hr><br>Auktionen bearbeiten<br>';
//abgelaufene auktionen bearbeiten
$sql="SELECT * FROM de_cyborg_auktion WHERE time < UNIX_TIMESTAMP()";
$result = mysql_query($sql, $db);
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
    //dem verkäufer das geld gutschreiben, wenn price null ist, dann ist es ein sofortkauf
    if($price==0)$preis=$dprice;else $preis=$price;
    echo '<br>Verkäufer: '.$seller;
    echo '<br>Preis: '.$preis;
    $sql="UPDATE de_cyborg_item SET amount=amount+'$preis' WHERE equip=0 AND typ=20 AND id=1 AND user_id='$seller'";
    mysql_query($sql, $db);
    echo '<br>SQL: '.$sql;
  
  
    //dem käufer den gegenstand übertragen
    echo '<br>Käufer: '.$bidder;
    echo '<br>Item-ID: '.$itemid;
    echo '<br>Item-Typ: '.$itemtyp;
    $sql="INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$bidder', '$itemid', '$itemtyp', '1')";
    mysql_query($sql, $db);
    echo '<br>SQL: '.$sql;

    //die auktion löschen
    $sql="DELETE FROM de_cyborg_auktion WHERE id='$id'";
    mysql_query($sql, $db);
    echo '<br>SQL: '.$sql;
  }
  else
  {
    //es hat niemand geboten, also einfach wieder dem anbieter zurückgeben
    echo '<br>es wurde nicht geboten';
    //dem verkäufer den gegenstand zurückbuchen
    echo '<br>Verkäufer: '.$seller;
    echo '<br>Item-ID: '.$itemid;
    echo '<br>Item-Typ: '.$itemtyp;
    $sql="INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$seller', '$itemid', '$itemtyp', '1')";
    mysql_query($sql, $db);
    echo '<br>SQL: '.$sql;

    //die auktion löschen
    $sql="DELETE FROM de_cyborg_auktion WHERE id='$id'";
    mysql_query($sql, $db);
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

//$result = mysql_query("SELECT * FROM de_cyborg_data WHERE arena=1 ORDER BY exp DESC", $db);
$result = mysql_query("SELECT de_user_data.spielername, de_cyborg_data.* FROM de_user_data left join de_cyborg_data on(de_user_data.user_id = de_cyborg_data.user_id) WHERE arena=1 ORDER BY de_cyborg_data.exp DESC",$db);
$anz = mysql_num_rows($result);
//immer zwei kämpfen lassen
for($i=0;$i<$anz;$i=$i+2)
{
  //zuerst schauen ob es nen partner gibt
  if($i<$anz-1)
  {
    $user[0] = mysql_result($result, $i,"user_id");
    $user[1] = mysql_result($result, $i+1,"user_id");
    $userhp[0] = mysql_result($result, $i,"hp");
    $userhp[1] = mysql_result($result, $i+1,"hp");
    $usersname[0] = mysql_result($result, $i,"spielername");
    $usersname[1] = mysql_result($result, $i+1,"spielername");

    
    //ausrüstung spieler 1 laden
    for($c=0;$c<=1;$c++)
    {
      $uid=$user[$c];
      $resultc = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=1 AND user_id='$uid'", $db);
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
        $filename='../eftadata/items/'.$itemid.'.item';
        include($filename);
        //werte zusammenzählen
        $cyborg_armor[$c]+=$item_armor;
        $cyborg_mindmg[$c]+=$item_mindmg;
        $cyborg_maxdmg[$c]+=$item_maxdmg;
      }
    }
    //wer geschickter ist greift zuerst an, ansonsten per zufall
    if(mysql_result($result, $i,"dex")>mysql_result($result, $i+1,"dex"))$erstschlag=0;else $erstschlag=1;
    if(mysql_result($result, $i,"dex")==mysql_result($result, $i+1,"dex"))$erstschlag=mt_rand(0, 1);
    
    //da die rüstungen für npcs ausgelegt sind deren wert teilen
    $cyborg_armor[0]=$cyborg_armor[0]/8;
    $cyborg_armor[1]=$cyborg_armor[1]/8;
    
    /*echo $user[0].':'.$user[1].'<br>';
    echo $cyborg_mindmg[0].':'.$cyborg_maxdmg[0].'<br>';
    echo $cyborg_mindmg[1].':'.$cyborg_maxdmg[1].'<br><br>';*/
    //es werden maximal 10 runden gekämpft
    $maxrunden=50;$haswon=0;
    for($r=0;$r<$maxrunden;$r++)
    {
      //gegnerschaden berechnen
      for($c=0;$c<=1;$c++)
      {
        if ($trefferwahrscheinlichkeitliste[mysql_result($result, $i+$c,"level")-1]> mt_rand(0, 100))
        {

          $schaden[$c]=mt_rand($cyborg_mindmg[$c], $cyborg_maxdmg[$c])-$cyborg_armor[$c]+mysql_result($result, $i+$c,"str");
          if ($schaden[$c]<0)$schaden[$c]=0;
          //echo 'treffer'.$schaden[$c].'<br>';
        }
        else $schaden[$c]=0;
      }

      if($erstschlag==0)
      {
        //player 1 schlägt zu
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

        //player 2schlägt zu
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
        //player 2schlägt zu
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
        
        //player 1 schlägt zu
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
        //daten zurücksetzen
        mysql_query("UPDATE de_cyborg_data set bewpunkte = 0, hp=hpmax, map=1, x=3, y=3 WHERE user_id='$ums_user_id'",$db);
        //gegner löschen
        mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$ums_user_id'",$db);
        echo 'Sie wurden besiegt und der Nottransmitter bringt den Cyborg zum Startpunkt zurück.<br><br>';*/
      }
      //wenn jemand gewonnen hat kampf abbrechen
      if($haswon>0)$r=$maxrunden;

      if($r==$maxrunden AND $haswon==0)//unentschieden, gewinner wird ausgelost
      {
        $haswon=mt_rand(1, 2);
        //echo 'zufall';
      }
      //echo '<br>HP1: '.$userhp[0].'<br>';
      //echo 'HP2: '.$userhp[1].'<br>';
    }//ende maxrunden

    //kämpfer updaten
    echo '<br>haswon: '.$haswon.'<br>';
    //gewinner updaten
    if($haswon==1){$uid=$user[0];$hashp=$userhp[0];$sname=$usersname[1];}else {$uid=$user[1];$hashp=$userhp[1];$sname=$usersname[0];}
    $fame=2;$arenawon=1;$arenalost=0;
    if($hashp<=0)$hashp=1;
    $showmsg='Der Sieg ist dein, du hast beim Arenakampf &uuml;ber '.$sname.' triumphiert. Als Gewinner erh&auml;ltst du zwei Ruhmespunkte und einen Arenaheiltrank.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, hp='$hashp', fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$db);
    //heiltrank ins gepäck packen
    mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$uid', '4843', '21', '1')",$db);
    //verlierer updaten
    if($haswon==1){$uid=$user[1];$hashp=$userhp[1];$sname=$usersname[0];}else {$uid=$user[0];$hashp=$userhp[0];$sname=$usersname[1];}
    $fame=1;$arenawon=0;$arenalost=1;
    if($hashp<=0)$hashp=1;
    $showmsg='Der Sieg beim Arenakampf gehört deinem Gegner '.$sname.', jedoch erh&auml;ltst du einen Ruhmespunkt und einen Arenaheiltrank.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, hp='$hashp', fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$db);
    //heiltrank ins gepäck packen
    mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount) VALUES ('$uid', '4843', '21', '1')",$db);
  }
  else //freikampf
  {
    $uid = mysql_result($result, $i,"user_id");
    //spieler updaten
    $fame=1;$arenawon=0;$arenalost=0;
    $showmsg='Es konnte beim Arenakampf kein passender Gegner f&uuml;r dich gefunden werden, jedoch erh&auml;ltst du trotzdem einen Ruhmespunkt.';
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte-10, fame=fame+'$fame',
     arenawon=arenawon+'$arenawon', arenalost=arenalost+'$arenalost', showmsg='$showmsg', arena=0 WHERE user_id='$uid'",$db);
    mysql_query("UPDATE de_cyborg_data SET arena=0", $db);
  }
}
}//if cron-ende
?>
</body>
</html>
