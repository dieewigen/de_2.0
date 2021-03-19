<?php
//der fahrende magier zelsus
include("lib/transaction.lib.php");

//defs
$getexp=250;
$grundpreis=1000;
$levelpreis=100;
//wie oft hat man es schon gekauft
$gekauft=get_player_flag(1);

//schauen ob der wagen da ist
$istda=0;
$result = mysql_query("SELECT bldg, bldgpic FROM de_cyborg_map WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
$num = mysql_num_rows($result);
if($num==1)
{
  $row = mysql_fetch_array($result);
  if($row["bldg"]==15 AND $row["bldgpic"]==32)$istda=1;
}


//gegenstand kaufen
if($_GET["buy"]==1 AND $istda==1)
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    //schauen wie teuer der gegenstand ist
  	$kosten=$grundpreis+(get_player_flag(1)*$levelpreis);
  	
  	//schauen ob man genug geld hat
  	if(get_player_money()>=$kosten)
  	{
  	  //item geben, Tyran-Selting-Stein 
  	  add_item(4864, 1);
  	  
  	  //exp geben
  	  give_exp($getexp);
  	  
  	  //geld abziehen
  	  $kosten=$kosten*(-1);
  	  modify_player_money($efta_user_id, $kosten);

	  
	//flag setzen damit es teurer wird
	set_player_flag(1, get_player_flag(1)+1);
	$gekauft++;

          //den wagen an eine neue position verschieben
  	  //zuerst löschen
  	  mysql_query("UPDATE de_cyborg_map SET bldg = 0, bldgpic = 0 WHERE x = '$x' AND y= '$y' AND z='$map';",$eftadb);
  	  //neue koordinaten suchen
          $result = mysql_query("SELECT x,y FROM de_cyborg_map WHERE bldg=0 AND bldgpic=0 AND groundpicext=0 AND x > '-90' AND x < 120 AND y > '-100' AND y < 100 AND z=0 ORDER BY RAND() LIMIT 0,1", $eftadb);
          $row = mysql_fetch_array($result);
          $newx=$row["x"];
          $newy=$row["y"];
  	  //wagen an der neuen position setzen
  	  mysql_query("UPDATE de_cyborg_map SET bldg = 15, bldgpic = 32 WHERE x = '$newx' AND y= '$newy' AND z='$map';",$eftadb);
  	  
          //info was man bekommen hat
          $errmsg.='<font color="#00FF00">&nbsp;Du erh&auml;ltst einen Tyran-Selting-Stein und '.$getexp.' Erfahrungspunkte.</font>';
	  $errmsg.='<br>&nbsp;Zelsus verabschiedet sich und macht sich auf dem Weg zu einem neuen Ziel.';
          //meldung für den chat hinzufügen
          $text=$efta_spielername.' kauft zum '.$gekauft.'. Mal einen Tyran-Selting-Stein beim fahrenden Magier Zelsus.';
          $time=time();
          mysql_query("INSERT INTO de_cyborg_chat_msg (spielername, message, timestamp) VALUES ('^Der Herold^', '$text', '$time')",$eftadb);
      
          $erfolgreicherkauf=1;
  	}
  	else //man hat nicht genug geld
  	$errmsg.='<font color="#FF0000">Du hast nicht genug Geld dabei.</font>';
    
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

//wenn es erfolgreich gekauft wurde message in die db packen und redirekten
if($erfolgreicherkauf==1)
{
  insert_msg($errmsg, 1);
}

if ($istda==1)
{
//den wagen darstellen
/*
echo '<div id="ct_city">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Der fahrende Magier Zelsus&nbsp;</b></td></tr>';
echo '</table><br>';*/
echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Der fahrende Magier Zelsus</b></div>');

$bg='cell1';
    echo '<table width="100%" cellpadding="1" cellspacing="1">
	  	  <tr align="center">
		    <td class="'.$bg.'"><b>Der fahrende Magier Zelsus bereist das ganze Land und verkauft seine Waren dem, der in der Lage ist ihn zu finden.<br>
		    Seinen neuen Kunden berechnet er nicht viel, aber je &ouml;fters jemand vorbeischaut, desto h&ouml;her wird der Warenpreis.<br>
		    Aktuell verkauft er Tyran-Selting-Steine, die den K&auml;ufer gleichzeitig erfahrener machen.</b></td>
		  </tr>

          <tr align="left">
		    <td class="'.$bg.'"><b>&nbsp;Folgende Aktionen sind hier m&ouml;glich:</b></td>
		  </tr>';    
    
    $bg='cell';
    //stein kaufen
    echo '<tr align="left">
		    <td class="'.$bg.'"><b>&nbsp;<a href="#" onClick="lnk(\'buy=1\')">Tyran-Selting-Stein zu folgendem Preis kaufen:</a> '.
    		 make_moneystring($grundpreis+($gekauft*$levelpreis)).'</b></td>
		  </tr>';
    
    //gebäude verlassen
    echo '<tr align="left">
		    <td class="'.$bg.'"><b>&nbsp;<a href="#" onClick="lnk(\'leavebldg=1\')">Wagen verlassen</a></b></td>
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