<?php
//hafen
include("lib/transaction.lib.php");

//x,y,z, name, ab level, kosten
$port[]= array (2,9,0, 'Waldmond', 1, 0);
$port[]= array (263,463,0, 'Falgon', 10, 1000);
$port[]= array (548,55,0, 'Degar', 20, 2000);
$port[]= array (302,-595,0, 'Zulak', 30, 3000);
$port[]= array (-455,-332,0, 'Ra', 40, 4000);
$port[]= array (-718,185,0, 'Kratau', 50, 5000);
$port[]= array (-489,524,0, 'Olymp', 60, 6000);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//reisefunktion
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["travel"]))
{
  $travel=intval($_REQUEST["travel"]);
  //id auf gültigkeit überprüfen
  if($travel>count($port)-1)$travel=0;

  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
  	//überprüfen ob man auf einem hafenfeldsteht
    $onport=0;
	for($i=0;$i<count($port);$i++)
    if($x==$port[$i][0] AND $y==$port[$i][1] AND $map==$port[$i][2])$onport=1;
    
    //wenn man sich im hafen befindet weitermachen
    if($onport==1)
    {
	  
      //schauen ob man den passenden level für den hafen hat
      if($level>=$port[$travel][4])
      {
      	//schauen ob man genug geld hat
        if(get_player_money()>=$port[$travel][5])
        {
      	  //geld abziehen
      	  modify_player_money($efta_user_id, $port[$travel][5]*(-1));
      	  
      	  //neue koordinaten setzen
      	  $zielx=$port[$travel][0];
      	  $ziely=$port[$travel][1];
      	  $zielmap=$port[$travel][2];
      	  mysql_query("UPDATE de_cyborg_data SET map='$zielmap', x='$zielx', y='$ziely' WHERE user_id='$efta_user_id'",$eftadb);
      	  
      	  echo '<script>lnk("");</script>';
      	}
      }
    }
    
    
    //transaktionsende
    $erg = releaseLock($efta_user_id); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("Datensatz Nr. ".$efta_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende  
  
}


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//hafenlevelvoraussetzungen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//$ht_req[level][pos]='itemid;itemmenge'
//level 1
$htl=0;
$ht_req[$htl][0]='4865;2500';//fetische
$ht_req[$htl][1]='4850;5000';//sandstein
$ht_req[$htl][2]='4863;1000';//oller fusel
$ht_req[$htl][3]='4861;3000';//brote
$ht_req[$htl][4]='4855;500';//kupferbarren
$ht_req[$htl][5]='4864;250';//tyr-selting-stein
$ht_req[$htl][6]='4845;10000';//lindenholzstämme
//maximallevel
$ht_maxlevel=$htl;


//schauen ob man auf dem hafenfeld steht
$istda=0;
$result = mysql_query("SELECT bldg, bldgpic, fieldlevel FROM de_cyborg_map WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
$num = mysql_num_rows($result);
if($num==1)
{
  $row = mysql_fetch_array($result);
  if($row["bldg"]==16 AND $row["bldgpic"]==33)$istda=1;
  $fieldlevel=$row["fieldlevel"];
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//item ins lager transferieren
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["mil"]) AND isset($_REQUEST["id"]))
{
  //turmdaten laden
  $itemid=(int)$_REQUEST["id"];
  $db_daten=mysql_query("SELECT * FROM de_cyborg_struct WHERE x = '$x' AND y= '$y' AND z='$map'",$eftadb);
  $anz = mysql_num_rows($db_daten);
  if($anz==1)
  {
    $row = mysql_fetch_array($db_daten);
    $htlevel=$fieldlevel;
    unset($hasbres);
    for($i=1;$i<=10;$i++)$hasbres[]=$row["flag$i"];
    //wird die item-id im turm benötigt
    $needit=0;
    for($i=0;$i<10;$i++)
    {
      //wenn man einen rohstoff braucht diesen ausgeben und wieviel man schon hat
      if($ht_req[$htlevel][$i]!='')
      {
        $need=explode(";",$ht_req[$htlevel][$i]);
        if($itemid==$need[0]){$found=1;$index=$i;$itemmenge=$need[1];}
      }
      if($found==1)break;
    }
    //wenn es benötigt wird weitermachen
    if($found==1)
    {
      //schauen ob man es von der menge her noch braucht
      if($hasbres[$index]<$itemmenge)
      {
        //schauen ob man es im rucksack hat
        $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
        $num = mysql_num_rows($result);
        if($num>0)
        {
          //item aus dem rucksack entfernen
          mysql_query("DELETE FROM de_cyborg_item WHERE id='$itemid' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          
          //im lager hochzählen
          $indexp=$index+1;
          mysql_query("UPDATE de_cyborg_struct SET flag$indexp=flag$indexp+1 WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);

          
          //nach dem hochzählen schauen ob man evtl. alle items zusammen hat und der level fertig ist
          //zuerst noch den lager array aktualisieren
          $hasbres[$index]++;
          $hasall=1;
          for($i=0;$i<10;$i++)
          {
            if($ht_req[$htlevel][$i]!='')
            {
              $need=explode(";",$ht_req[$htlevel][$i]);
              if($need[1]>$hasbres[$i])$hasall=0;
            }
          }
          //wenn man alles hat, hafen auf den nächsten level upgraden
          if($hasall==1)
          {
            //db-update
            mysql_query("UPDATE de_cyborg_map SET fieldlevel=1 WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
          }
        }
        else
        {
          $text='Das hast du nicht dabei.';
          mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
          echo '<script>lnk("");</script>';
        }
      }
      else
      {
        $text='Davon hast du bereits genug.';
        mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
        echo '<script>lnk("");</script>';
      }
    }
    else
    {
      $text='Dieser Gegenstand wird hier nicht ben&ouml;tigt.';
      mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
      echo '<script>lnk("");</script>';
    }
  }
}

if($istda==1)//hafen ist da, also darstellen
{
  //die hafenbauansicht darstellen
  
  //hafennamen auslesen
  for($i=0;$i<count($port);$i++)
  if($x==$port[$i][0] AND $y==$port[$i][1] AND $map==$port[$i][2])$hafenname=$port[$i][3];
  
  
  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Hafen - '.$hafenname.'&nbsp;</b></td></tr>';
  echo '</table><br>';*/
  
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Hafen - '.$hafenname.'</b></div>');

  
  //daten des hafens auslesen
  //zuerst den level
  $htlevel=$fieldlevel;
  
  //die eingeladgerten rohstoffe
  $db_daten=mysql_query("SELECT * FROM de_cyborg_struct WHERE x = '$x' AND y= '$y' AND z='$map'",$eftadb);
  $row = mysql_fetch_array($db_daten);
  
  unset($hasbres);
  for($i=1;$i<=10;$i++)$hasbres[]=$row["flag$i"];

  $showlevel=$htlevel+1;
  //nur einen level mehr zum bauen anzeigen
  if($showlevel>$htlevel+1)$showlevel=$htlevel+1;
  //kleinster level ist 1
  if($showlevel<1)$showlevel=1;

  //wenn showlevel größer level ist, dann kann man den level ausbauen
  if($showlevel>$htlevel AND $showlevel<$ht_maxlevel+2)
  {
    //begrüßung
    echo '<table width="100%"><tr>
    <td class="cell"><b>Der Hafen von Waldmond wurde vor vielen Jahren von wandernden Horden vollkommen zerst&ouml;rt und mit einem Fluch belegt.<br>
    Mit den passenden magischen Fetischen und ausreichend Rohstoffen sollte er jedoch wieder aufgebaut werden k&ouml;nnen.<br>
    Am Aufbau des Hafens k&ouml;nnen sich alle Bürger gemeinsam beteiligen.
    </b></td>
    </tr></table>';

  	//kopfzeile
    echo '<table width="100%"><tr>';
    //<td colspan="4" class="cell"><b>Turmetage: '.($showlevel).' (im Bau)</td></tr>
    echo '
    <tr><td class="cell1" width="55%"><b>ben&ouml;tigte Gegenst&auml;nde</td>
    <td class="cell1" width="15%" align="center"><b>im Rucksack</td>
    <td class="cell1" width="15%" align="center"><b>eingelagert</td>
    <td class="cell1" width="15%" align="center"><b>ben&ouml;tigt</td></tr>
    ';

    //benötigte items
    $c1=1;
    $bg='cell';
    for($i=0;$i<10;$i++)
    {
      if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}//hintergrundfarbe switchen

      //wenn man einen rohstoff braucht diesen ausgeben und wieviel man schon hat
      if($ht_req[$htlevel][$i]!='')
      {
        $need=explode(";",$ht_req[$htlevel][$i]);
        //itemname laden
        $filename='eftadata/items/'.($need[0]).'.item';
        include($filename);

      
        echo '<tr>';
        echo '<td class="'.$bg.'"><a href="#" onClick="lnk(\'mil=1&id='.$need[0].'\')">[E]</a>'.$item_name.'</td>';
        //schauen wieviel man davon dabei hat
        $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$need[0]' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
        $num = mysql_num_rows($result);
        echo '<td class="'.$bg.'" align="center">'.$num.'</td>';

        echo '<td class="'.$bg.'" align="center">'.number_format($hasbres[$i], 0,"",".").'</td>';
        echo '<td class="'.$bg.'" align="center">'.number_format($need[1], 0,"",".").'</td>';
        echo '</tr>';
      }
    }
  }
  else
  {
    echo '<table width="100%"><tr>
    <td class="cell"><b>Der Hafen erm&ouml;glicht das Reisen in ferne L&auml;nder.
    </b></td>
    </tr></table>';
        
  	//kopfzeile
    echo '<table width="100%">
          <tr align="center">
            <td class="cell1"><b>Zielhafen</b></td>
            <td class="cell1"><b>Koordinaten</b></td>
            <td class="cell1"><b>ab Level</b></td>
            <td class="cell1"><b>Preis</b></td>
          </tr>';
    //häfen ausgeben
    
    for($i=0;$i<count($port);$i++)
    {
      //den hafen, wo man sich befindet, ausblenden
      if($x!=$port[$i][0] AND $y!=$port[$i][1])
      {
      	if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
        
      	//levelfarbe
      	if($level<$port[$i][4]){$hstr1='<font color="#FF0000">';$hstr2='</font>';}else{$hstr1='';$hstr2='';}
        echo '<tr align="center">
              <td class="'.$bg.'">'.$port[$i][3].'</td>
              <td class="'.$bg.'">'.$port[$i][0].':'.$port[$i][1].'</td>
              <td class="'.$bg.'">'.$hstr1.$port[$i][4].$hstr2.'</td>
              <td class="'.$bg.'" align="right"><a href="#" onClick="lnk(\'travel='.$i.'\')">'. make_moneystring($port[$i][5]).'</a></td>
            </tr>';
      }
    }
  }

  echo '</table>';
  echo '<br><br>';
  echo '<a class="gwaren" href="#" onClick="lnk(\'leavebldg=1\')">&nbsp;verlassen&nbsp;</a><br>&nbsp;';
  //echo '</div>';
  rahmen1_unten();
  rahmen0_unten();
  
  echo '</body></html>';
  exit;
}
?>
