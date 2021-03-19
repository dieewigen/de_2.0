<?php
include("lib/transaction.lib.php");

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//turmlevelvoraussetzungen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//$ht_req[level][pos]='itemid;itemmenge'
//level 1
$htl=0;
$ht_req[$htl][0]='4853;1';//heldengrundstein
$ht_req[$htl][1]='4845;5';//lindenholzst�mme
$ht_req[$htl][2]='4850;20';//sandstein
$ht_req[$htl][3]='4851;30';//getreide
//level 2
$htl++;
$ht_req[$htl][0]='4845;10';//lindenholzst�mme
$ht_req[$htl][1]='4850;40';//sandstein
$ht_req[$htl][2]='4851;60';//getreide
//level 3
$htl++;
$ht_req[$htl][0]='4845;15';//lindenholzst�mme
$ht_req[$htl][1]='4850;60';//sandstein
$ht_req[$htl][2]='4851;90';//getreide
//level 4
$htl++;
$ht_req[$htl][0]='4845;20';//lindenholzst�mme
$ht_req[$htl][1]='4850;70';//sandstein
$ht_req[$htl][2]='4851;100';//getreide
$ht_req[$htl][3]='4855;10';//kupferbarren
//level 5
$htl++;
$ht_req[$htl][0]='4845;10';//lindenholzst�mme
$ht_req[$htl][1]='4850;35';//sandstein
$ht_req[$htl][2]='4851;50';//getreide
$ht_req[$htl][3]='4855;10';//kupferbarren
$ht_req[$htl][4]='4861;8';//brot
$ht_req[$htl][5]='4863;5';//oller fusel
//level 6
$htl++;
$ht_req[$htl][0]='4845;15';//lindenholzst�mme
$ht_req[$htl][1]='4850;40';//sandstein
$ht_req[$htl][2]='4851;30';//getreide
$ht_req[$htl][3]='4855;20';//kupferbarren
$ht_req[$htl][4]='4861;10';//brot
$ht_req[$htl][5]='4863;10';//oller fusel
$ht_req[$htl][6]='4864;5';//Tyran-Selting-Stein
//level 7
$htl++;
$ht_req[$htl][0]='4845;15';//lindenholzst�mme
$ht_req[$htl][1]='4850;50';//sandstein
$ht_req[$htl][2]='4851;30';//getreide
$ht_req[$htl][3]='4855;30';//kupferbarren
$ht_req[$htl][4]='4861;20';//brot
$ht_req[$htl][5]='4863;20';//oller fusel
$ht_req[$htl][6]='4864;2';//Tyran-Selting-Stein
$ht_req[$htl][7]='4866;5';//Sol-Altair-Stein
//maximallevel
$ht_maxlevel=$htl;
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//item ins baulager transferieren
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["mil"]) AND isset($_REQUEST["id"]))
{
  //turmdaten laden
  $itemid=(int)$_REQUEST["id"];
  $db_daten=mysql_query("SELECT * FROM de_cyborg_ht WHERE user_id='$efta_user_id'",$eftadb);
  $anz = mysql_num_rows($db_daten);
  if($anz==1)
  {
    $row = mysql_fetch_array($db_daten);
    $htlevel=$row["level"];
    unset($hasbres);
    for($i=1;$i<=10;$i++)$hasbres[]=$row["b$i"];
    //wird die item-id im turm ben�tigt
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
    //wenn es ben�tigt wird weitermachen
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
          
          //im lager hochz�hlen
          $indexp=$index+1;
          mysql_query("UPDATE de_cyborg_ht SET b$indexp=b$indexp+1 WHERE user_id='$efta_user_id';",$eftadb);
          
          //nach dem hochz�hlen schauen ob man evtl. alle items zusammen hat und der level fertig ist
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
          //wenn man alles hat, turm auf den n�chsten level upgraden
          if($hasall==1)
          {
            //db-update
            mysql_query("UPDATE de_cyborg_ht SET level=level+1, b1=0, b2=0, b3=0, b4=0, b5=0, b6=0, b7=0, b8=0, b9=0, b10=0 WHERE user_id='$efta_user_id';",$eftadb);
            //erfahrungspunkte gutschreiben
            $expgew=($htlevel+1)*100;
            give_exp($expgew);
            
            insert_msg('Der Heldenturm wurde um eine Etage erweitert. Du erh&auml;ltst '.$expgew.' Erfahrungspunkte.',1);
          }
        }
        else
        {
          $text='Das hast du nicht dabei.';
          mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
          echo '<script>lnk("");</script>';
          exit;
        }
      }
      else
      {
        $text='Davon hast du bereits genug.';
        mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
        echo '<script>lnk("");</script>';
        exit;
      }
    }
    else
    {
      $text='Dieser Gegenstand wird hier nicht ben&ouml;tigt.';
      mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
      echo '<script>lnk("");</script>';
      exit;
    }
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//item ins permanente lager einlager/auslagern
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST["transfer"]) AND isset($_REQUEST["id"]) AND isset($_REQUEST["did"]))
{
  //turmdaten laden
  $itemid=(int)$_REQUEST["id"];
  $db_daten=mysql_query("SELECT * FROM de_cyborg_ht WHERE user_id='$efta_user_id'",$eftadb);
  $anz = mysql_num_rows($db_daten);
  if($anz==1)
  {
    $id=intval($_REQUEST["id"]);
    $did=intval($_REQUEST["did"]);

  	//turmelvel auslesen
  	$row = mysql_fetch_array($db_daten);
    $htlevel=$row["level"];
    
    //auslagern
    if($_REQUEST["transfer"]==2)
    {
      mysql_query("UPDATE de_cyborg_item SET equip=0 WHERE id='$id' AND durability='$did' AND equip=2 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
    }
    else
    //einlagern
    {
      //schauen ob noch genug platz im turm ist
      $result = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=2 AND user_id='$efta_user_id'", $eftadb);
      $num = mysql_num_rows($result);      
      
      if($num<$htlevel)
      {
      	 mysql_query("UPDATE de_cyborg_item SET equip=2 WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
      }
      /*
      else
      {
        $text='Das Lager ist voll.';
        mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
        echo '<script>lnk("");</script>';
        exit;
      }*/
    }
  }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//land kaufen
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST["htbl"]))
{
  //schauen ob man schon land hat
  $db_daten=mysql_query("SELECT user_id FROM de_cyborg_ht WHERE user_id='$efta_user_id'",$eftadb);
  $anz = mysql_num_rows($db_daten);
  if($anz==0)//man hat noch kei land
  {
    //schauen ob man das geld hat
    if(get_player_money()>=5000)
    {
      //turm-db-eintrag anlegen
      mysql_query("INSERT INTO de_cyborg_ht (user_id) VALUES ('$efta_user_id')",$eftadb);
      //geld abziehen
      modify_player_money($efta_user_id, -5000);
    }
    else
    {
      $text='Du hast nicht genug Geld.';
      mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
      echo '<script>lnk("");</script>';
      exit;
    }
  }
}

//die turm�bersicht darstellen
/*echo '<div id="ct_city">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Die Heldent�rme von Nel-Axul&nbsp;</b></td></tr>';
echo '</table><br>';*/

echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Die Heldent&uuml;rme von Nel-Axul</b></div>');

//begr��ung
echo '<table width="100%"><tr>
<td class="ueber2" align="center"><b>Der Heldenturm von '.$efta_spielername.'</b></td>
</tr></table>';

//schauen ob man bereits land hat
$db_daten=mysql_query("SELECT * FROM de_cyborg_ht WHERE user_id='$efta_user_id'",$eftadb);
$anz = mysql_num_rows($db_daten);
if($anz==1)//man hat land, also turm darstellen
{
  $row = mysql_fetch_array($db_daten);
  $htlevel=$row["level"]; 
  
  if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;  
  //turmausbau darstellen
  if($_REQUEST["mp"]==1)
  {
    //oberes men� mit turmausbau, lager
    echo'<table width="100%">
    <tr>
    <td align="center">
    <b class="gwaren">&nbsp;Turmausbau&nbsp;</b></a>
    <a class="gwaren" href="#" onClick="lnk(\'mp=2\')">&nbsp;Lager&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'leavebldg=1\')">&nbsp;verlassen&nbsp;</a>
    </td>
	</tr>
	</table>';
    unset($hasbres);
    for($i=1;$i<=10;$i++)$hasbres[]=$row["b$i"];

    $showlevel=$htlevel+1;
    //nur einen level mehr zum bauen anzeigen
    if($showlevel>$htlevel+1)$showlevel=$htlevel+1;
    //kleinster level ist 1
    if($showlevel<1)$showlevel=1;

    //wenn showlevel gr��er level ist, dann kann man den level ausbauen
    if($showlevel>$htlevel AND $showlevel<$ht_maxlevel+2)
    {
      //kopfzeile
      echo '<table width="100%"><tr>
      <td colspan="4" class="cell"><b>Turmetage: '.($showlevel).' (im Bau)</td></tr>
      <tr><td class="cell1" width="55%"><b>ben&ouml;tigte Gegenst&auml;nde</td>
      <td class="cell1" width="15%" align="center"><b>im Rucksack</td>
      <td class="cell1" width="15%" align="center"><b>eingelagert</td>
      <td class="cell1" width="15%" align="center"><b>ben&ouml;tigt</td></tr>
      ';

      //ben�tigte items
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
          echo '<td class="'.$bg.'"><a href="#" onClick="lnk(\'mil=1&id='.$need[0].'\')">[E]</a>'.umlaut($item_name).'</td>';
          //schauen wieviel man davon dabei hat
          $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$need[0]' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
          $num = mysql_num_rows($result);
          echo '<td class="'.$bg.'" align="center">'.$num.'</td>';

          echo '<td class="'.$bg.'" align="center">'.$hasbres[$i].'</td>';
          echo '<td class="'.$bg.'" align="center">'.$need[1].'</td>';
          echo '</tr>';
        }
      }
    }
    else
    {
      //kopfzeile
      echo '<table width="100%">
          <tr><td class="cell"><b>Turmetage: '.($showlevel).'</b> (Ausbau noch nicht m&ouml;glich)</td></tr>
          <tr><td class="cell">Maximallevel erreicht.</td></tr>';
    }

    echo '</table>';
    echo '<br><br>';
  }
  elseif($_REQUEST["mp"]==2) //lager darstellen
  {
  	//oberes men� mit turmausbau, lager
    echo'<table width="100%">
    <tr>
    <td align="center">
    <a class="gwaren" href="#" onClick="lnk(\'mp=1\')">&nbsp;Turmausbau&nbsp;</a>
    <b class="gwaren">&nbsp;Lager&nbsp;</b></a>
    <a class="gwaren" href="#" onClick="lnk(\'leavebldg=1\')">&nbsp;verlassen&nbsp;</a>
    </td>
	</tr>
	</table>';
	//ausr�stung auslesen und tooltip generieren
	//restlichen items laden und tooltips generieren
	$anzbp=0;
    $result = mysql_query("SELECT id, durability, equip, uses FROM de_cyborg_item WHERE (equip=0 OR equip=2 ) AND typ<>20 AND user_id='$efta_user_id' ORDER BY equip, id ASC", $eftadb);
	while($row = mysql_fetch_array($result))
	{
  	  $BPText[$anzbp]=make_item_tooltipstring($row["id"], $row["durability"], $row["uses"]);
  	  //equiptyp speichern
  	  $bp[$anzbp]['equip']=$row["equip"];
  	  if($row["equip"]==2)$imlager++;
      //counter erh�hen
      $anzbp++;
    }

    echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
    //kopfzeile
    
    //die einzelnen spalten generieren
    $spalte[0]='<table width="100%" border="0" cellpadding="0" cellspacing="1"><tr><td class="cell" align="center"><b>Rucksack</b></td></tr>';
    $spalte[1]='<table width="100%" border="0" cellpadding="0" cellspacing="1"></tr><td class="cell" align="center"><b>Lagerplatz ('.round($imlager).'/'.
    $htlevel.')</b></td></tr>';
    $oldequip=0;
    for($i=0;$i<$anzbp;$i++)
    {
      if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
      //beim spaltenwechsel spaltenhintergrund resetten
      if($bp[$i]['equip']==2 AND $oldequip==0){$c1=1;$bg='cell1';}
      
      //linkname und betreff 
      if($bp[$i]['equip']==0)
      {
      	$alink[0]='[E]';
      	$alink[1]='einlagern';
      	$alink[2]='transfer=1';
      }
      else
      { 
      	$alink[0]='[A]';
      	$alink[1]='auslagern';      
        $alink[2]='transfer=2';
      }
      
      $hs = '<tr align="left">';
      $hs.= '<td class="'.$bg.'" title="'.$BPText[$i].'"><b>'.$bp[$i][0].
      ' <a href="#" onClick="lnk(\''.$alink[2].'&mp=2&id='.$bp[$i][1].'&did='.$bp[$i][5].
      '\')" title="'.$alink[1].'">'.$alink[0].'</a>
      </td>
    
      <td class="'.$bg.'" align="right">'.$preis.'</td>';

      $hs.= '</tr>';
    
      if($bp[$i]['equip']==0){$spalte[0].=$hs;} else {$spalte[1].=$hs;}
      //echo '</table>';
      //equiptyp speichern
      $oldequip=$bp[$i]['equip'];
    }
    $spalte[0].='</table>';$spalte[1].='</table>';
  	
  	//spalten ausgeben
  	echo '<tr valign="top"><td width="50%">'.$spalte[0].'</td><td width="50%">'.$spalte[1].'</td></tr></table>';
  	
  	
  }
}
else //man hat noch keinen turm, also info ausgeben
{
  echo '<table width="100%"><tr>
  <td class="cell1"><b>
  Jeder B&uuml;rger der sich f&uuml;r ein h&ouml;heres Ziel berufen f&uuml;hlt und als Held durch die Lande zieht, sollte sein K&ouml;nnen durch einen Heldenturm verk&ouml;rpern.
  Seit &Auml;onen schon wird diesem Brauch nachgegangen und ohne Heldenturm kann ein Held in der harten Welt nicht bestehen.<br><br>
  Der erste Schritt zu einem Heldenturm ist der Erwerb eines St&uuml;ck Landes, auf welchem der Turm errichtet werden kann.<br><br>
  Ist dies vollbracht ben&ouml;tigt der Held einen Heldengrundstein, welcher auf ihn geeicht und in das Fundament eingelassen wird.
  </td>
  </tr></table>';
  echo '<br><a class="gwaren" href="#" onClick="lnk(\'htbl=1\')">&nbsp;Land f&uuml;r 50 Silberlinge erwerben&nbsp;</a><br>&nbsp;';
  echo '<br><br>';
  echo '<a href="#" onClick="lnk(\'leavebldg=1\')"><div class="b1">verlassen</div></a><br>&nbsp;';
}
//echo '</div>';

rahmen1_unten();
rahmen0_unten();

//infoleiste anzeigen
show_infobar();

echo '</body></html>';
exit;
?>
