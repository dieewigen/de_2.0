<?php
/*
//das siegel von basranur
//iradium oder prozente der kollies weg, rasse gegen rasse
$db_daten=mysql_query("SELECT siegel1, s1res1, s1res2, s1res3, s1res4, s1res5, s1history FROM de_system",$db);
$row = mysql_fetch_array($db_daten);
$siegel1=$row["siegel1"];
$res1=$row["s1res1"];
$res2=$row["s1res2"];
$res3=$row["s1res3"];
$res4=$row["s1res4"];
$res5=$row["s1res5"];
$history=$row["s1history"];
//schauen wie lange der längste spieler dabei ist
$db_daten=mysql_query("SELECT MAX(tick) as maxtick FROM de_user_data",$db);
$row = mysql_fetch_array($db_daten);
$maxtick=$row["maxtick"];

//wenn die ticks rum sind siegelfunktion durchführen und kollektoren abziehen
if ($siegel1>=$sv_siegel1[1]-1)
{
  //kollektoren der rassen auslesen
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=1",$db);
  $row = mysql_fetch_array($db_daten);
  $col1=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=2",$db);
  $row = mysql_fetch_array($db_daten);
  $col2=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=3",$db);
  $row = mysql_fetch_array($db_daten);
  $col3=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=4",$db);
  $row = mysql_fetch_array($db_daten);
  $col4=$row["sumcol"];
  $db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=5",$db);
  $row = mysql_fetch_array($db_daten);
  $col5=$row["sumcol"];
  
  $gesamtcol=$col1+$col2+$col3+$col4+$col5;

  //schaue welche rasse es erwischt
  $q1=$col1/$gesamtcol;
  $q2=$col2/$gesamtcol;
  $q3=$col3/$gesamtcol;
  $q4=$col4/$gesamtcol;
  $q5=$col5/$gesamtcol;
  
  $res1=$res1-($res1*$q1);
  $res2=$res2-($res2*$q2);
  $res3=$res3-($res3*$q3);
  $res4=$res4-($res4*$q4);
  $res5=$res5-($res5*$q5);

  $rasse=5;
  if($res4<=$res5)$rasse=4;
  if($res3<=$res5 AND $res3<=$res4)$rasse=3;
  if($res2<=$res5 AND $res2<=$res4 AND $res2<=$res3)$rasse=2;
  if($res1<=$res5 AND $res1<=$res4 AND $res1<=$res3 AND $res1<=$res2)$rasse=1;
  
  echo "Siegelrasse: $rasse<br>";

  //kollektoren abziehen und message an den account schicken
  $db_daten=mysql_query("SELECT user_id, col FROM de_user_data WHERE rasse=$rasse",$db);
  $time=strftime("%Y%m%d%H%M%S");
  while ($row = mysql_fetch_array($db_daten))
  {
    //kollektoren
    $uid=$row["user_id"];
    $col=$row["col"];

    $p=$col/10000;
    if($p>$sv_siegel1[2])$p=$sv_siegel1[2];

    $colverlust=round($col*$p);
    $col=$col-$colverlust;

    if ($colverlust>0)
    {
      mysql_query("UPDATE de_user_data SET col=col-'$colverlust' WHERE user_id='$uid'",$db);
      //message wenn kollektorverlust
      if($colverlust==1) $text="Die Schockwelle, ausgelöst durch das Siegel von Basranur, durcheilt den Hyperraum und trifft auf Ihr System. Sie verlieren einen Kollektor.";
      else $text="Die Schockwelle, ausgelöst durch das Siegel von Basranur, durcheilt den Hyperraum und trifft auf Ihr System. Sie verlieren $colverlust Kollektoren.";
      
      mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 60,'$time','$text')",$db);
      mysql_query("update de_user_data set newnews = 1 where user_id = $uid",$db);
    }
  }

  //rohstoffe auf null setzen und siegel neu starten, dazu noch history erweitern
  if ($rasse==1)$r='Die Ewigen';
  elseif ($rasse==2)$r='Ishtar';
  elseif ($rasse==3)$r='K´Tharr';
  elseif ($rasse==4)$r='Z´tah-ara';
  elseif ($rasse==5)$r='DX61a23';

  $htime=$time[6].$time[7].'.'.$time[4].$time[5].'.'.$time[0].$time[1].$time[2].$time[3].' - '.$time[8].$time[9].':'.$time[10].$time[11].':'.$time[12].$time[13];
  $history="$htime: $r<br>".$history;
  mysql_query("UPDATE de_system SET siegel1=1, s1res1=0, s1res2=0, s1res3=0, s1res4=0, s1res5=0, s1history='$history'",$db);
}
//schauen ob man das siegel hochzählen muß
elseif($maxtick>$sv_siegel1[0])
{
  //siegel hochzählen
  mysql_query("UPDATE de_system SET siegel1=siegel1+1",$db);
}

*/

//systemquests, pc vs npc
//schauen ob es genug npcs gibt
/*$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE npc=0",$db);
$pc = mysql_result($db_daten,0,0);
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE npc=1",$db);
$npc = mysql_result($db_daten,0,0);

//wenn es spieler gibt und für jeden genug npcs vorhanden sind die quest in der db eintragen
if ($pc>0 AND $npc>=$pc)
{
  //als allererstes aber mal die alten einträge löschen
  mysql_query("TRUNCATE de_user_quest",$db);

  //daten auslesen
  $db_pc=mysql_query("SELECT user_id, score, fixscore FROM de_user_data WHERE npc=0 ORDER BY score DESC",$db);
  $db_npc=mysql_query("SELECT user_id, score, fixscore FROM de_user_data WHERE npc=1 ORDER BY score DESC",$db);

  while ($pc = mysql_fetch_array($db_pc))
  {
    //daten des npcs auslesen
    $npc = mysql_fetch_array($db_npc)
    
    //quest nur dann ins system einfügen, wenn pc und npc über eine gewisse non-fixpunktzahl verfügen
    //aber genaues weiß man da eh erst später, daher erstmal einach simpel die sachen einfügen


    //evtl nur dann ne quest starten, wenn alle npcs nen bestimmten softscore wert haben, wobei das auch vieles wieder verzögern kann
    
  }

}
*/

?>
