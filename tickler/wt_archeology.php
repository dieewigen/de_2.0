<?php
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 1 - der güldene kollektor

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//zuerst schauen ob ein spieler ihn hat und ggf. rohstoffe gutschreiben
$result = mysql_query("SELECT a1userid, a1npc, a1tick FROM de_system",$db);
$row = mysql_fetch_array($result);
$a1userid=$row["a1userid"];
$a1npc=$row["a1npc"];
$a1tick=$row["a1tick"];

//wenn den güldenen ein pc hat rohstoffe gutschreiben
if($a1userid>0 AND $a1npc==0)
{
  $result = mysql_query("SELECT ekey, techs FROM de_user_data WHERE user_id='$a1userid'",$db);
  $row = mysql_fetch_array($result);
  $ekey=$row["ekey"];
  $techs=$row["techs"];
  //ekey aufsplitten
  $hv=explode(";",$ekey);
  $keym=$hv[0];$keyd=$hv[1];$keyi=$hv[2];$keye=$hv[3];

  //schauen wieviel output der kollektor hat, grundmenge sind 100 kollektoren
  //zuerst rundendauer auslesen
  $db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
  $row = mysql_fetch_array($db_daten);
  $ticks=$row["tick"];
  //menge berechnen
  $ea=floor($sv_kollieertrag*(100+($ticks/4)));

  //energieinput pro rohstoff
  $em=(int)$ea/100*$keym;
  $ed=(int)$ea/100*$keyd;
  $ei=(int)$ea/100*$keyi;
  $ee=(int)$ea/100*$keye;

  //energie->materie verhaeltnis
  if ($techs[18]==1)$emvm=1; else $emvm=2;
  if ($techs[19]==1)$emvd=2; else $emvd=4;
  if ($techs[20]==1)$emvi=3; else $emvi=6;
  if ($techs[21]==1)$emve=4; else $emve=8;

  //rohstoffoutput
  $rm=$em/$emvm;
  $rd=$ed/$emvd;
  $ri=$ei/$emvi;
  $re=$ee/$emve;
  $rm=intval($rm);
  $rd=intval($rd);
  $ri=intval($ri);
  $re=intval($re);

  //falls es keine materieumwandler gibt, erhält man keine res
  if ($techs[14]==0)
  {
    $rm=0;
    $rd=0;
    $ri=0;
    $re=0;
  }

  //rohstoffe - ende
  mysql_query("UPDATE de_user_data SET restyp01 = restyp01 + $rm, restyp02 = restyp02 + $rd,
    restyp03 = restyp03 + $ri, restyp04 = restyp04 + $re WHERE user_id='$a1userid'",$db);
}

//haltedauer um eins erhöhen wenn es ein pc ist
if ($a1npc==0)mysql_query("UPDATE de_system SET a1tick=a1tick+1",$db);

//wenn die haltezeit um ist, oder niemand den güldenen hat ihm einen npc-system zuweisen
if($a1tick>19 OR $a1userid==0)
{
  //per zufall ein npc-system aussuchen
  $result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=1",$db);
  $anz = mysql_num_rows($result);
  $zufall=mt_rand(0,$anz-1);
  $uid=mysql_result($result, $zufall,0);
  //neue daten in de_system schreiben
  mysql_query("UPDATE de_system SET a1userid='$uid', a1npc=1, a1tick=0",$db);
}
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

//datenpaket 2 bis 11, npc-systeme vorbelegen

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
for($i=2;$i<=11;$i++)
{
  $tblfield='a'.$i.'userid';
  $result = mysql_query("SELECT $tblfield AS userid FROM de_system",$db);
  $row = mysql_fetch_array($result);
  $userid=$row["userid"];
  //wenn kein npc hinterlegt dann einen per  zufall auswählen
  if($userid==0)
  {
    //per zufall ein npc-system aussuchen
    $result = mysql_query("SELECT user_id FROM de_user_data WHERE npc=1 ORDER BY RAND() LIMIT 0,1",$db);
    $row = mysql_fetch_array($result);
    $uid=$row["user_id"];
    //neue daten in de_system schreiben
    mysql_query("UPDATE de_system SET $tblfield='$uid'",$db);
    echo "UPDATE de_system SET $tblfield='$uid'";
  }
}

?>
