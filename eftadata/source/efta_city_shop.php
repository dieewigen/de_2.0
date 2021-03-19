<?php
//warenangebot
$shopangebot=array(4839,4840,4841,4842,4867,4844,4846,4847,4848,4852,4854,4859,4862,4864,4866,4869,4870,4871);

$shopmsg='';
//gegenstände kaufen
if($_GET["buy"]==1)
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    $id=(int)$_GET["id"];
    //schauen ob der gegenstand angeboten wird
    if (in_array($id, $shopangebot))
    {
      //schauen ob man genug geld hat
      $result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
      $row = mysql_fetch_array($result);
      $hasmoney=$row["amount"];
      //daten des items laden
      $item_durability=0;
      $filename='eftadata/items/'.$id.'.item';
      include($filename);
      $itemwert=$item_worth*10;
      if($hasmoney>=$itemwert)
      {
        //gold abziehen
        mysql_query("UPDATE de_cyborg_item SET amount=amount-'$itemwert' WHERE user_id='$efta_user_id' AND id=1 AND typ=20",$eftadb);
        //gegenstand in den rucksack packen
        mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$id', '$item_typ', '1', '$item_durability')",$eftadb);
        //$shopmsg='<font color="#00FF00">Du hast die Ware erworben.</font>';
        $shopmsg="<div id=\"meldung\"><br><b class=\"text2\">Du hast die Ware erworben.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
      }
      else $shopmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
    }
    else $shopmsg="<div id=\"meldung\"><br><b class=\"text6\">Diese Ware wird hier nicht gef&uuml;hrt.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
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
  else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}//gegenstand kaufen ende

//gegenstände verkaufen
if($_GET["se"]==1)
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    $id=intval($_GET["id"]);
    $did=intval($_GET["did"]);
    //schauen ob er den gegenstand auch im rucksack hat
    $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id'",$eftadb);
    $num = mysql_num_rows($result);
    if($num>0)
    {
      //daten des items laden
      $filename='eftadata/items/'.$id.'.item';
      include($filename);
      //schauen ob man so einen gegenstand verkaufen darf
      if($item_sell_lock!=1)
      {
        //gold gutschreiben
        mysql_query("UPDATE de_cyborg_item SET amount=amount+'$item_worth' WHERE user_id='$efta_user_id' AND id=1 AND typ=20",$eftadb);
        //gegenstand aus dem gepäck entfernen
        mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
        $shopmsg="<div id=\"meldung\"><br><b class=\"text2\">Du hast die Ware verkauft.</b><br><br>
         &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
      }else
        $shopmsg="<div id=\"meldung\"><br><b class=\"text2\">Diese Ware kann hier nicht verkauft werden.</b><br><br>
         &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
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
  else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}//gegenstand verkaufen ende

//laden anzeigen
if($_REQUEST["bu"]==1)
{
  //geld laden
  $result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
  $row = mysql_fetch_array($result);
  $preis=make_moneystring($row["amount"]);

  if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;
  if($_REQUEST["mp"]==1)
  {
    if($shopmsg!='')
    {
      echo $shopmsg;
      ?>
      <script language="JavaScript">
      setTimeout("document.getElementById('meldung').style.visibility='hidden'",10000);
      </script>
      <?php
    }

  	//waren kaufen
  	
    echo '<br><br>';
    
    rahmen0_oben();
    rahmen1_oben('<div align="center"><b>Die glorreiche Stadt Waldmond</b></div>');
    
  	/*echo '<div id="ct_city">';
    echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr><td align="center"><b class="ueber">&nbsp;Die glorreiche Stadt Waldmond&nbsp;</b></td></tr>';
    echo '</table><br>';*/

    echo '<table width="100%">
    <tr>
    <td class="ueber2" align="center"><b>Orlandos Gemischtwaren</td>
    </tr>';

    echo'
    <tr>
    <td align="center">
    <b class="gwaren">&nbsp;Verkauf&nbsp;</b></a>
    <a class="gwaren" href="#" onClick="lnk(\'bu=1&mp=2\')">&nbsp;Ankauf&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Laden verlassen&nbsp;</a>
    </td>
	</tr>
	</table>

    <table width="100%">
    <tr>
    <td class="text4">Hier kannst du Waren kaufen.</td>
    <td class="text3" align="right">Geldbeutel: '.$preis.'</td>
    </tr>';
    echo '</table><table width="100%" border="0" cellpadding="0" cellspacing="1">';
    //kopfzeile
    echo '<tr><td class="cell" align="center"><b>Artikel</b></td><td class="cell" align="center"><b>Preis</b></td></tr>';


//artikel laden und tooltip generieren
//restlichen items laden und tooltips generieren
$anzbp=0;
for($i=0;$i<=count($shopangebot)-1;$i++)
{
  $BPText[$anzbp]=make_item_tooltipstring($shopangebot[$i], '-1', '-1');
  //counter erhöhen
  $anzbp++;
}

  for($i=0;$i<$anzbp;$i++)
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell1';
    }
    else
    {
      $c1=0;
      $bg='cell';
    }
    //verkaufswert
    $preis=make_moneystring($bp[$i][4]);

    echo '<tr align="left">';
    echo '<td class="'.$bg.'" title="'.$BPText[$i].'"><b>'.$bp[$i][0].
    ' <a href="#" onClick="lnk(\'bu=1&mp=1&buy=1&id='.$bp[$i][1].
    '\')" title="kaufen">[K]</a>
    </td><td class="'.$bg.'" align="right">'.$preis.'</td>';
    echo '</tr>';
  }

    echo'</table>';
    
    rahmen1_unten();
    
    rahmen0_unten();
  }
  elseif($_REQUEST["mp"]==2)
  {
    if($shopmsg!='')
    {
      echo $shopmsg;
      ?>
      <script language="JavaScript">
      setTimeout("document.getElementById('meldung').style.visibility='hidden'",10000);
      </script>
      <?php
    }

  	//waren an orlando verkaufen
  	echo '<br><br>';
    rahmen0_oben();
    rahmen1_oben('<div align="center"><b>Die glorreiche Stadt Waldmond</b></div>');

  	/*echo '<div id="ct_city">';
    echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
    echo '<tr><td align="center"><b class="ueber">&nbsp;Die glorreiche Stadt Waldmond&nbsp;</b></td></tr>';
    echo '</table><br>';*/

    echo '<table width="100%">
    <tr>
    <td class="ueber2" align="center"><b>Orlandos Gemischtwaren</td>
    </tr>';

    echo'
    <tr>
    <td align="center">
    <a class="gwaren" href="#" onClick="lnk(\'bu=1&mp=1\')">&nbsp;Verkauf&nbsp;</a>
    <b class="gwaren">&nbsp;Ankauf&nbsp;</b></a>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Laden verlassen&nbsp;</a>
    </td>
	</tr>
	</table>

    <table width="100%">
    <tr>
    <td class="text4">Hier kannst du deine Waren verkaufen.</td>
    <td class="text3" align="right">Geldbeutel: '.$preis.'</td>
    </tr>';
    echo '</table><table width="100%" border="0" cellpadding="0" cellspacing="1">';
    //kopfzeile
    echo '<tr><td class="cell" align="center"><b>Artikel</b></td><td class="cell" align="center"><b>Preis</b></td></tr>';

//rucksackinhalt darstellen
//ausrüstung auslesen und tooltip generieren
//restlichen items laden und tooltips generieren
$anzbp=0;
//schauen wieviel items man bei hat
$itemsperpage=922;
$result = mysql_query("SELECT id, durability FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id'", $eftadb);
$itemmenge = mysql_num_rows($result);

$sp=$_REQUEST["sp"];
if($sp<=1)$sp=1;
if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
$sp=(int)$sp;
if($sp<=1)$sp=1;
$showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
$showmenge=$itemsperpage;
$result = mysql_query("SELECT id, durability, uses FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id' ORDER BY typ,id LIMIT $showstart,$showmenge", $eftadb);
while($row = mysql_fetch_array($result))
{
  $BPText[$anzbp]=make_item_tooltipstring($row["id"], $row["durability"], $row["uses"]);
  //counter erhöhen
  $anzbp++;
}

  for($i=0;$i<$anzbp;$i++)
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell1';
    }
    else
    {
      $c1=0;
      $bg='cell';
    }
    //verkaufswert
    $preis=make_moneystring(round($bp[$i][4]/10));

    echo '<tr align="left">';
    echo '<td class="'.$bg.'" title="'.$BPText[$i].'"><b>'.$bp[$i][0].
    ' <a href="#" onClick="lnk(\'bu=1&mp=2&se=1&sp='.$sp.'&id='.$bp[$i][1].'&did='.$bp[$i][5].
    '\')" title="verkaufen" onclick="return confirm(\'M&ouml;chtest du den Gegenstand wirklich verkaufen?\')">[V]</a>
    </td><td class="'.$bg.'" align="right">'.$preis.'</td>';

    echo '</tr>';
  }
    //evtl. untere leiste zum blättern anzeigen
    if($anzbp>0)
    {
    echo '<tr align="center"><td colspan="2">';
    echo '<table><tr>';
    //zurück
    if($sp>1)echo '<td width="100" align="center"><a href="#" onClick="lnk(\'bu=1&mp=2&sp='.($sp-1).'\')">zur&uuml;ck</a></td>';
    else echo '<td width="100" align="center">&nbsp;</td>';
    //itemzahl
    $bis=$showstart+$showmenge;
    if($bis>$itemmenge)$bis=$itemmenge;
    echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
    //weiter
    if(($bis<$itemmenge))
    echo '<td width="100" align="center"><a href="#" onClick="lnk(\'bu=1&mp=2&sp='.($sp+1).'\')">weiter</a></td>';
    else echo '<td width="100" align="center">&nbsp;</td>';
    echo '</tr></table></td></tr>';
    }

    echo'</table><br>';
  }
  //echo '</div>';
  
  rahmen1_unten();
  
  rahmen0_unten();
  
  //infoleiste anzeigen
  show_infobar();
  
  die('</body></html>');
}

?>
