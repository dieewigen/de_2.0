<?php
include("lib/transaction.lib.php");

//stadtstufen definieren
//level 1
$htl=0;
$ht_req[$htl][0]='4845;1000';//lindenholzstämme
$ht_req[$htl][1]='4850;500';//sandstein
$ht_req[$htl][2]='4861;300';//brote
$ht_req[$htl][3]='4863;100';//oller fusel
$ht_req[$htl][4]='4855;50';//kupferbarren
//level 2
$htl++;
$ht_req[$htl][0]='4845;2000';//lindenholzstämme
$ht_req[$htl][1]='4850;1000';//sandstein
$ht_req[$htl][2]='4861;600';//brote
$ht_req[$htl][3]='4863;200';//oller fusel
$ht_req[$htl][4]='4855;100';//kupferbarren
//level 3
$htl++;
$ht_req[$htl][0]='4845;3000';//lindenholzstämme
$ht_req[$htl][1]='4850;1500';//sandstein
$ht_req[$htl][2]='4861;900';//brote
$ht_req[$htl][3]='4863;300';//oller fusel
$ht_req[$htl][4]='4855;150';//kupferbarren
//level 4
$htl++;
$ht_req[$htl][0]='4845;4000';//lindenholzstämme
$ht_req[$htl][1]='4850;2000';//sandstein
$ht_req[$htl][2]='4861;1200';//brote
$ht_req[$htl][3]='4863;400';//oller fusel
$ht_req[$htl][4]='4855;200';//kupferbarren

//maximallevel
$ht_maxlevel=$htl;
$maxspalten=5;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//item ins stadtlager transferieren
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["spendtocity"]) AND isset($_REQUEST["id"]))
{
  //turmdaten laden
  $itemid=(int)$_REQUEST["id"];
  if($itemid>$maxspalten-1)$itemid=$maxspalten-1;
  $indexp=$itemid+1;
  $need=explode(";",$ht_req[0][$itemid]);
  $itemid=$need[0];
  
  //schauen ob man es im rucksack hat
  $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
  $num = mysql_num_rows($result);
  if($num>0)
  {
    //item aus dem rucksack entfernen
    mysql_query("DELETE FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          
    //im lager hochzählen
    mysql_query("UPDATE de_cyborg_struct SET flag$indexp=flag$indexp+1 WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
  }
  else
  {
    $text='Das hast du nicht dabei.';
    mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
    echo '<script>lnk("");</script>';
  }
}


//in der stadt überprüfen welche sachen es gibt
$result = mysql_query("SELECT * FROM de_cyborg_struct WHERE x='$x' AND y='$y' AND z='$map'", $eftadb);
$num = mysql_num_rows($result);
$row = mysql_fetch_array($result);
$city_flag[0]=$row["flag1"];
$city_flag[1]=$row["flag2"];
$city_flag[2]=$row["flag3"];
$city_flag[3]=$row["flag4"];
$city_flag[4]=$row["flag5"];
$city_flag[5]=$row["flag6"];
$city_flag[6]=$row["flag7"];
$city_flag[7]=$row["flag8"];
$city_flag[8]=$row["flag9"];
$city_flag[9]=$row["flag10"];

if($x==0 AND $y==0 AND $map==0)
{
  $stadtname='Die glorreiche Stadt Waldmond';
  $citylevel=99;
}
else 
{
  //stadtlevel rausfinden
  //stufen auflisten
  $citylevel=0;
  for($s=0;$s<=$ht_maxlevel;$s++)
  {
    $haslevel=0;
  	for($i=0;$i<$maxspalten;$i++)
    {
      $need=explode(";",$ht_req[$s][$i]);
      if($city_flag[$i]>=$need[1])$haslevel++;
    }
    if($haslevel==5) $citylevel++;
  }
  
  if($citylevel==0)$stadtname='Ruinen';
  else $stadtname='Stadt';
  $stadtname.=' ('.$x.'/'.$y.') Stufe '.$citylevel;
}

if($citylevel>=1)include "eftadata/source/efta_city_shop.php";
if($citylevel>=2)include "eftadata/source/efta_city_arena.php";
if($citylevel>=3)include "eftadata/source/efta_city_auktion.php";
if($citylevel>=4 AND $_REQUEST[action]=='herotowerpage')
{
  $bldg=8;
  mysql_query("UPDATE de_cyborg_data SET inbldg='$bldg' WHERE user_id='$efta_user_id';",$eftadb);
  $inbldg=$bldg;
  //include "eftadata/source/efta_bldg.php";
  echo '<script>lnk("");</script>';
}

//mouseovertexte erstellen
$atip[0] = "Stadt verlassen&Hier kehrst du wieder in die Wildnis zur&uuml;ck. Sei achtsam und r&uuml;ste dich gut aus.";
$atip[1] = "Die Arena von Waldmond&In der Arena kannst du f&uuml;r Ruhm und Ehre k&auml;mpfen.";
$atip[2] = "Orlandos Gemischtwaren&Bei Orlando kannst du wichtigen Waren einkaufen und Sachen verkaufen, die du unterwegs gefunden hast.";
$atip[3] = "Das Auktionshaus&Im Auktionshaus kannst du Waren ersteigern und versteigern. Dort werden f&uuml;r gew&ouml;hnlich auch Waren feilgeboten, die man nicht im Laden kaufen kann.";
$atip[4] = "Der Heldenturm&Im Heldenturm kannst du Waren lagern.";
 

//die stadtübersicht darstellen
/*
echo '<div id="ct_city">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Die glorreiche Stadt Waldmond&nbsp;</b></td></tr>';
echo '</table><br>';*/

echo '<br><br>';

rahmen0_oben();

rahmen1_oben('<div align="center"><b>'.$stadtname.'</b></div>');

echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr align="center">';
//Orlandos Gemischtwaren
if($citylevel>=1)
{
echo '<td class="cell1" height="160">';
echo '<a href="#" onClick="lnk(\'bu=1\')" title="'.$atip[2].'"><img src="'.$gpfad.'cp3.gif" border="0"></a>';
echo '</td>';
}
//Die Arena von Waldmond
if($citylevel>=2)
{
echo '<td class="cell1" height="160">';
echo '<a href="#" onClick="lnk(\'showarena=1\')" title="'.$atip[1].'"><img src="'.$gpfad.'cp2.gif" border="0"></a>';
echo '</td>';
}
//Das Auktionshaus
if($citylevel>=3)
{
echo '<td class="cell1" height="160">';
echo '<a href="#" onClick="lnk(\'auktion=1\')" title="'.$atip[3].'"><img src="'.$gpfad.'cp4.gif" border="0"></a>';
echo '</td>';
}
//Der Heldenturm
if($citylevel>=4)
{
echo '<td class="cell1" height="160">';
echo '<a href="#" onClick="lnk(\'action=herotowerpage\')" title="'.$atip[4].'"><img src="'.$gpfad.'s8.gif" width="150" height="150" style="background-color: #000000;" border="0"></a>';
echo '</td>';
}
//Stadt verlassen
echo '<td class="cell1" height="160">';
echo '<a href="#" onClick="lnk(\'leavebldg=1\')" title="'.$atip[0].'"><img src="'.$gpfad.'cp1.gif" border="0"></a>';
echo '<td>';
echo '</tr>';

//platzhalter
/*
echo '<tr align="center">';
echo '<td height="40" colspan="4" class="cell">';
echo '&nbsp;';
echo '<td>';
echo '</tr>';
*/
echo '</table>';

rahmen1_unten();

echo '<br>';

if($x==0 AND $y==0 AND $map==0){}
else 
{
  rahmen1_oben('<div align="center"><b>Stadtausbau</b></div>');

  //einzahlmöglichkeit und anzahl der waren 
  echo '<table width="100%"><tr>
      <td colspan="'.$maxspalten.'" class="cell"><b>Vorhandene Waren:</td></tr>
      <tr>';
  for($i=0;$i<$maxspalten;$i++)
  {
    $need=explode(";",$ht_req[0][$i]);
    //itemname laden
    $filename='eftadata/items/'.($need[0]).'.item';
    include($filename);
  	
    echo '<td class="cell1" width="'.floor(100/$maxspalten).'%" align="center"><b>'.$item_name.'</b></td>';
  }
  echo '</tr><tr>';
  for($i=0;$i<$maxspalten;$i++)
  {
    echo '<td class="cell" align="center">'.$city_flag[$i].' <a href="#" onClick="lnk(\'spendtocity=1&id='.$i.'\')">[E]</a></td>';
  }
  echo '</tr>';      
  echo '</table><br>';
  
  //übersicht über die einzelnen stufen der stadt
  echo '<table width="100%"><tr>
      <td colspan="'.($maxspalten+1).'" class="cell"><b>Stufenvoraussetzungen:</td></tr>
      <tr>';
  echo '<td class="cell1" width="'.floor(100/($maxspalten+1)).'%" align="center">Stufe</td>';
  for($i=0;$i<$maxspalten;$i++)
  {
    $need=explode(";",$ht_req[0][$i]);
    //itemname laden
    $filename='eftadata/items/'.($need[0]).'.item';
    include($filename);
  	
    echo '<td class="cell1" width="'.floor(100/($maxspalten+1)).'%" align="center"><b>'.$item_name.'</b></td>';
  }
  echo '</tr><tr>';
  //stufen auflisten
  for($s=0;$s<=$ht_maxlevel;$s++)
  {
    echo '<td class="cell" width="'.floor(100/($maxspalten+1)).'%" align="center">'.($s+1).'</td>';
    for($i=0;$i<$maxspalten;$i++)
    {
      $need=explode(";",$ht_req[$s][$i]);
      //texfarbe festlegen
      if($city_flag[$i]>=$need[1])
      {
      	$color='#00FF00';
      }
      else
      {
      	$color='#FF0000';
      }
      echo '<td class="cell" align="center"><font color="'.$color.'">'.$need[1].'</font></td>';
    }
    echo '</tr>';      
  }
  
  echo '</table><br>';
  
  
  rahmen1_unten();
}

rahmen0_unten();
//infoleiste anzeigen
show_infobar();

//echo '</div>';
echo '</body></html>';
exit;
?>
