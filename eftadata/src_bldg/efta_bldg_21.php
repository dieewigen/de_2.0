<?php
//der taschenh‰ndler bis stufe 20
include("lib/transaction.lib.php");

//warenangebot
//$shopangebot=array(4876, 4877, 4878, 4879, 4880, 4881, 4882, 4883, 4884, 4885, 4886, 4887, 4888, 4889);
$shopangebot=array(4876, 4877, 4878, 4879);


//schauen ob der wagen da ist
$istda=0;
$result = mysql_query("SELECT bldg, bldgpic FROM de_cyborg_map WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
$num = mysql_num_rows($result);
if($num==1)
{
  $row = mysql_fetch_array($result);
  if($row["bldg"]==21 AND ($row["bldgpic"]==32 OR $row["bldgpic"]==40))$istda=1;
}

if ($istda==1)
{
$shopmsg='';
//gegenst‰nde kaufen
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
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlieﬂen</a>&nbsp;</div>";
      }
      else $shopmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlieﬂen</a>&nbsp;</div>";
    }
    else $shopmsg="<div id=\"meldung\"><br><b class=\"text6\">Diese Ware wird hier nicht gef&uuml;hrt.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlieﬂen</a>&nbsp;</div>";
    //transaktionsende
    $erg = releaseLock($efta_user_id); //Lˆsen des Locks und Ergebnisabfrage
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



    if($shopmsg!='')
    {
      echo $shopmsg;
      ?>
      <script language="JavaScript">
      setTimeout("document.getElementById('meldung').style.visibility='hidden'",10000);
      </script>
      <?php
    }

	
  //geld laden
  $result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
  $row = mysql_fetch_array($result);
  $preis=make_moneystring($row["amount"]);

  
//den wagen darstellen
echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Der Taschenh&auml;ndler Mel Bar</b></div>');

$bg='cell1';
    echo '<table width="100%" cellpadding="1" cellspacing="1">
	  	  <tr align="center">
		    <td class="'.$bg.'"><b>Der fahrende Taschenh&auml;ndler Mel Bar bereist das ganze Land und verkauft seine Waren dem, der in der Lage ist ihn zu finden.
';
	$bg='cell';
    echo '</b></td>
		  </tr>

          <tr align="center">
		    <td class="'.$bg.'"><b>&nbsp;<a href="#" onClick="lnk(\'leavebldg=1\')"><div class="b1">Wagen verlassen</div></a></b></td>
		  </tr>';    
    
    //evtl. fehlermeldungen ausgeben
    if($errmsg!='')
    {
  	  $bg='cell';
  	  echo '<tr align="left">
		   <td class="'.$bg.'"><b>&nbsp;Achtung: '.$errmsg.'</b></td>
		   </tr>';
    }
   
    echo '</table>';

    echo'
    <table width="100%">
    <tr>
    <td class="text4">Hier kannst du Waren kaufen.</td>
    <td class="text3" align="right">Geldbeutel: '.$preis.'</td>
    </tr>';
    echo '</table><table width="100%" border="0" cellpadding="0" cellspacing="1">';
    //kopfzeile
    echo '<tr><td class="cell" align="center"><b>Artikel</b></td><td class="cell" align="center"><b>Preis</b></td></tr>';


//artikel laden und tooltip generieren
$anzbp=0;
for($i=0;$i<=count($shopangebot)-1;$i++)
{
  $BPText[$anzbp]=make_item_tooltipstring($shopangebot[$i], '-1', '-1');
  //counter erhˆhen
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


echo '</body></html>';
exit;
}//ende $istda
else  //der wagen ist schon wieder weg, also das inbldgflag auf 0 setzen
{
  mysql_query("UPDATE de_cyborg_data SET inbldg=0 WHERE user_id='$efta_user_id';",$eftadb);
}
?>