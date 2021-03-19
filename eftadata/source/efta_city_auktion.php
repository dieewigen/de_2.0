<?php
//sofortkauf
if($_REQUEST["skid"])
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    //schauen ob es die auktion auch gibt
    $auktionid=intval($_REQUEST["skid"]);
    $result = mysql_query("SELECT * FROM de_cyborg_auktion WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid' AND time>=UNIX_TIMESTAMP()+60", $eftadb);
    $num = mysql_num_rows($result);
    if($num==1)
    {
      //daten auslesen
      $row = mysql_fetch_array($result);
      $aktgebot=$row["price"];
      $sofortkauf=$row["dprice"];

      //geld auslesen
      $hasmoney=get_player_money();

      //schauen ob der preis groß genug ist und man auch das geld hat
      if($hasmoney>=$sofortkauf)
      {
          //auktionsdatensatz reservieren
          $result = mysql_query("UPDATE de_cyborg_auktion SET aupdate=1 WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid' AND aupdate=0", $eftadb);
          $num = mysql_affected_rows();
          if($num==1)
          {
            //auktionsdaten nochmal auslesen
            $result = mysql_query("SELECT * FROM de_cyborg_auktion WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid'", $eftadb);
            $row = mysql_fetch_array($result);
            $aktgebot=$row["price"];
            $sofortkauf=$row["dprice"];
            $aktbidder=$row["bidder"];

            //nochmal geld überprüfen
            if($hasmoney>=$sofortkauf)
            {
              //dem neuen bieter das geld abziehen
              modify_player_money($efta_user_id, $sofortkauf*(-1));

              //aktuelles gebot der auktion erhöhen und den neuen bieter hinterlegen
              //preis auf 0 setzen, dann ist es immer ein sofortkauf
              mysql_query("UPDATE de_cyborg_auktion SET bidder='$efta_user_id', price=0, aupdate=0, time=0 WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid'", $eftadb);

              //dem alten spieler das gold wieder gutschreiben
              modify_player_money($aktbidder, $aktgebot);

              //echo '<div align="left">A '.$gebot.' B '.$aktgebot.' C '.$hasmoney.'</div>';
              $auktionmsg="<div id=\"meldung\"><br><b class=\"text2\">Der Gegenstand wurde erworben und wird in K&uuml;rze zugestellt.</b><br><br>
              &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
            }
            else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          }
          else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Ein anderer Bieter hat gerade sein Gebot abgegeben.</b><br><br>
          &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
      }
      else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
    }
    else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Diese Auktion ist nicht mehr aktiv.</b><br><br>
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
}

//für eine auktion bieten
if($_REQUEST["bieten"])
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    //schauen ob es die auktion auch gibt
    $auktionid=intval($_REQUEST["auktionid"]);
    $result = mysql_query("SELECT * FROM de_cyborg_auktion WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid' AND time>=UNIX_TIMESTAMP()+60", $eftadb);
    $num = mysql_num_rows($result);
    if($num==1)
    {
      //daten auslesen
      $row = mysql_fetch_array($result);
      $aktgebot=$row["price"];
      $sofortkauf=$row["dprice"];

      //gebot des spielers
      $gebot=intval($_REQUEST["ppreis"]*1000000+$_REQUEST["gpreis"]*10000+$_REQUEST["spreis"]*100+$_REQUEST["kpreis"]);

      //geld auslesen
      $hasmoney=get_player_money();

      //schauen ob man auch das geld hat
      if($hasmoney>=$gebot)
      {
        //schauen ob der preis groß genug ist und man auch das geld hat
        if($gebot>$aktgebot)
        {
          //schauen, ob der preis evtl. über dem preis vom sofortkauf ist
          $flag=1;
          if($sofortkauf>0)if($gebot >= $sofortkauf)$flag=0;
          if($flag==1)
          {
            //auktionsdatensatz reservieren
            $result = mysql_query("UPDATE de_cyborg_auktion SET aupdate=1 WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid' AND aupdate=0", $eftadb);
            $num = mysql_affected_rows();
            if($num==1)
            {
              //auktionsdaten nochmal auslesen
              $result = mysql_query("SELECT * FROM de_cyborg_auktion WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid'", $eftadb);
              $row = mysql_fetch_array($result);
              $aktgebot=$row["price"];
              $sofortkauf=$row["dprice"];
              $aktbidder=$row["bidder"];
          
              //nochmal geld überprüfen
              if($gebot>=$aktgebot AND $hasmoney>=$gebot)
              {
                //dem neuen bieter das geld abziehen
                modify_player_money($efta_user_id, $gebot*(-1));

                //aktuelles gebot der auktion erhöhen und den neuen bieter hinterlegen
                mysql_query("UPDATE de_cyborg_auktion SET bidder='$efta_user_id', price='$gebot', aupdate=0 WHERE seller <> '$efta_user_id' AND bidder <> '$efta_user_id' AND id='$auktionid'", $eftadb);

                //dem alten spieler das gold wieder gutschreiben
                modify_player_money($aktbidder, $aktgebot);

                $auktionmsg="<div id=\"meldung\"><br><b class=\"text2\">F&uuml;r den Gegenstand wurde geboten.</b><br><br>
                &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
              }
              else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
              &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
            }
            else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Ein anderer Bieter hat gerade sein Gebot abgegeben.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          }
          else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Das Gebot muß unter dem Sofortkaufpreis liegen.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
        }
        else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug geboten.</b><br><br>
          &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
      }
      else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du hast nicht genug Geld.</b><br><br>
        &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
    }
    else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Diese Auktion ist nicht mehr aktiv.</b><br><br>
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
}//auktion bieten ende



//auktion erstellen
if($_REQUEST["startauktion"])
{
  //transaktionsbeginn
  if (setLock($efta_user_id))
  {
    //fix für zu große zahlen
    if($_REQUEST["pstartpreis"]>9)$_REQUEST["pstartpreis"]=9;
    if($_REQUEST["pskpreis"]>9)$_REQUEST["pstartpreis"]=9;
    //preis festlegen
    $preis=intval($_REQUEST["pstartpreis"]*1000000+$_REQUEST["gstartpreis"]*10000+$_REQUEST["sstartpreis"]*100+$_REQUEST["kstartpreis"]);
    $skpreis=intval($_REQUEST["pskpreis"]*1000000+$_REQUEST["gskpreis"]*10000+$_REQUEST["sskpreis"]*100+$_REQUEST["kskpreis"]);
    //schauen ob man die gebühr bezahlen kann
    //die gebühr beträgt 1% vom startpreis/sofortkaufpreis (je nachdem was größer ist) und ist nicht kleiner als 10 kupfer
    $gebuehr=round($preis/100);
    if($gebuehr<round($skpreis/100))$gebuehr=round($skpreis/100);
    if($gebuehr<10)$gebuehr=10;
    
    $result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
    $row = mysql_fetch_array($result);
    $hasmoney=$row["amount"];
    //hat man genug geld für die gebühr?
    if($hasmoney>=$gebuehr)
    {
      //ist der sofortkaufpreis größer als der startpreis?
      $startauktion=1;
      if($skpreis>0)if($skpreis<$preis)$startauktion=0;
      if($startauktion==1)
      {
        $id=(int)$_REQUEST["itemid"];
        $did=(int)$_REQUEST["itemdid"];
        //schauen ob er den gegenstand auch im rucksack hat
        $result = mysql_query("SELECT id, durability FROM de_cyborg_item WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
        $num = mysql_num_rows($result);
        if($num>0)
        {
          $row = mysql_fetch_array($result);
          //daten des items laden
          $filename='eftadata/items/'.$id.'.item';
          $item_durability=0;
          include($filename);
          //schauen ob dieser gegenstand im ah gehandelt werden darf
          if($item_auction_lock!=1)
          {
            //schauen ob der gegensand beschädigit ist, dann darf er nicht gehandelt werden
          	if($item_durability==$row["durability"])
          	{
          	  //gebühr abziehen
              mysql_query("UPDATE de_cyborg_item SET amount=amount-'$gebuehr' WHERE user_id='$efta_user_id' AND id=1 AND typ=20",$eftadb);
              //gegenstand aus dem gepäck entfernen
              mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
              //auktions-db-eintrag erzeugen
              $zeit=intval($_REQUEST["atime"]);
                if($zeit!=2 AND $zeit!=4 AND $zeit!=6 AND $zeit!=8 AND $zeit!=12 AND $zeit!=16 AND $zeit!=24 AND $zeit!=48
                 AND $zeit!=96 AND $zeit!=168 AND $zeit!=336)$zeit=48;
              $zeit=time()+$zeit*3600;
              mysql_query("INSERT INTO de_cyborg_auktion (seller, itemid, itemname, itemlevel, itemtyp, price, dprice, time)
               VALUES ('$efta_user_id', '$id', '$item_name', '$item_level', '$item_typ', '$preis', '$skpreis', '$zeit')", $eftadb);

              //meldung dass alles geklappt hat
              $auktionmsg="<div id=\"meldung\"><br><b class=\"text2\">Die Auktion wurde gestartet.</b><br><br>
               &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          
          	}else 
      	  $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Besch&auml;digte Ware kann nicht im Auktionshaus gehandelt werden.</b><br><br>
           &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          }
          else 
      	  $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Diese Ware kann nicht im Auktionshaus gehandelt werden.</b><br><br>
           &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
        }
      }
      else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Der Startpreis darf nicht größer als der Sofortkaufpreis sein.</b><br><br>
       &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldung').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
    }
    else $auktionmsg="<div id=\"meldung\"><br><b class=\"text6\">Du kannst die Geb&uuml;hr nicht bezahlen.</b><br><br>
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
}//auktion erstellen ende


//das auktionshaus anzeigen
if($_REQUEST["auktion"]==1 OR $auktion==1)
{
  echo '<script language="javascript">disablekeys=1;</script>';
  //echo '<form action="eftamain.php" method="post">';
  //echo '<input type="hidden" name="auktion" value="1">';

  //echo '<div id="ct_city">';
  
  if($auktionmsg!='')
  {
    echo $auktionmsg;
    ?>
    <script language="JavaScript">
    setTimeout("document.getElementById('meldung').style.visibility='hidden'",10000);
    </script>
    <?php
  }
  
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Die glorreiche Stadt Waldmond</b></div>');
  
  echo '<table width="100%">
  <tr>
  <td class="ueber2" align="center"><b>Das Auktionshaus</b></td>
  </tr>';

  echo '
  <tr>
  <td class="cell1"><b>Das Auktionshaus erm&ouml;glicht das Handeln mit anderen B&uuml;rgern. Es basiert auf dem altgedienten System der Versteigerung,
  auch ein Sofortkauf ist m&ouml;glich. Dein Geldvorrat betr&auml;gt: &nbsp;&nbsp;'.make_moneystring(get_player_money()).'
  </td>
  </tr>';

  if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //für eine auktion bieten
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  if($_REQUEST["mp"]==1)
  {
    echo'
    <tr>
    <td align="center">
    <b class="gwaren">&nbsp;Bieten&nbsp;</b>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=3\')">&nbsp;Meine Gebote&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=4\')">&nbsp;Meine Auktionen&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=2\')">&nbsp;Auktion erstellen&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Auktionshaus verlassen&nbsp;</a>
    </td>
	</tr>';
	
	//hier kann man für ein item bieten
    if($_REQUEST["gid"])
    {
      $auktionsid=intval($_REQUEST["gid"]);
      //item und preis aus der db holen
      $result = mysql_query("SELECT * FROM de_cyborg_auktion WHERE seller<>'$efta_user_id' AND id='$auktionsid'", $eftadb);
      $num = mysql_num_rows($result);
      if($num==1)
      {
        $row = mysql_fetch_array($result);

        echo '<tr><td class="cell">Auktionsgegenstand: '.$row["itemname"].'</td></tr>';
        //startpreis
        echo '<tr><td class="cell1">Aktuelles Gebot: '.make_moneystring($row["price"]).'</td></tr>';
        if($row["dprice"]>0)echo '<tr><td class="cell">Sofortkauf: '.make_moneystring($row["dprice"]).'</td></tr>';
        else echo '<tr><td class="cell">Sofortkauf: - </td></tr>';
        //sofortkauf
        echo '<tr><td class="cell1">Dein Gebot:
              <input id="v1" type="text" name="ppreis" size="4" maxlength="4" value=""> <img src="'.$gpfad.'co4.gif" alt="Platin">
              <input id="v2" type="text" name="gpreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co3.gif" alt="Gold">
              <input id="v3" type="text" name="spreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co2.gif" alt="Silber">
              <input id="v4" type="text" name="kpreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co1.gif" alt="Kupfer">
              &nbsp;</td></tr>';
        ?>
        <script>
    	function auktion_bieten()
    	{
    		var v1=$("#v1").val();
    		var v2=$("#v2").val();
    		var v3=$("#v3").val();
    		var v4=$("#v4").val();
    		
    		lnk('auktion=1&mp=1&bieten=1&auktionid=<?php echo $auktionsid;?>'+
    	    	'&ppreis='+v1+'&gpreis='+v2+'&spreis='+v3+'&kpreis='+v4);

        }
    	</script>
    	<?php
        
        //start/abbrechen-button
        echo '<tr align="center"><td>
              <a class="gwaren" href="#" onClick="auktion_bieten()">&nbsp;bieten&nbsp;</a>
              <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=1\')">&nbsp;abbrechen&nbsp;</a>
              </td></tr>';

        
      }
      echo '</table>';

      rahmen1_unten();
	  echo '</div>';
      //echo '</form>';
      
		//infoleiste anzeigen
		show_infobar();
	  
	  
      die('</body></html>');
    }

	//sofortkauf für ein item
	
	//hier ist der suchbereich
    //suchfeld, name (min. zeichen), minlevel, maxlevel
    echo '
    <tr>
    <td class="cell" align="center">
    Suchen: <input id="field1" class="chatinput" type="text" name="asb" size="25" maxlength="100" value="'.$_REQUEST["asb"].'">
    Min. Level <input id="field2" type="text" name="minlvl" size="1" maxlength="2" value="'.$_REQUEST["minlvl"].'">
    Max. Level <input id="field3" type="text" name="maxlvl" size="1" maxlength="2" value="'.$_REQUEST["maxlvl"].'">';
    ?>
    Art:
    <select id="searchtyp" name="searchtyp">
    <option value="0" <?php if($_REQUEST["searchtyp"]==0) echo 'selected';?>>alles</option>
    <option value="1" <?php if($_REQUEST["searchtyp"]==1) echo 'selected';?>>Rechte Hand</option>
    <option value="2" <?php if($_REQUEST["searchtyp"]==2) echo 'selected';?>>Linke Hand</option>
    <option value="3" <?php if($_REQUEST["searchtyp"]==3) echo 'selected';?>>Kopf</option>
    <option value="5" <?php if($_REQUEST["searchtyp"]==5) echo 'selected';?>>Brust</option>
    <option value="11" <?php if($_REQUEST["searchtyp"]==11) echo 'selected';?>>Beine</option>
    <option value="12" <?php if($_REQUEST["searchtyp"]==12) echo 'selected';?>>F&uuml;&szlig;e</option>
    <option value="7" <?php if($_REQUEST["searchtyp"]==7) echo 'selected';?>>H&auml;nde</option>
    <option value="21" <?php if($_REQUEST["searchtyp"]==21) echo 'selected';?>>Tr&auml;nke</option>
    <option value="22" <?php if($_REQUEST["searchtyp"]==21) echo 'selected';?>>Werkzeug</option>
    <option value="23" <?php if($_REQUEST["searchtyp"]==21) echo 'selected';?>>Rohstoff</option>
    <option value="24" <?php if($_REQUEST["searchtyp"]==21) echo 'selected';?>>Nahrung</option>
    </select>

	<script>
	function start_search()
	{
		var v1=$("#field1").val();
		var v2=$("#field2").val();
		var v3=$("#field3").val();
		var v4=$("#searchtyp").val();
		lnk('auktion=1&asb='+v1+'&minlvl='+v2+'&maxlvl='+v3+'&searchtyp='+v4);
	}
	</script>

    <?php
    echo '
    <a class="gwaren" href="#" onClick="start_search()">&nbsp;Suche starten&nbsp;</a>
    </td>
    </tr></table>';

    //ausgabe der suchergebnisse
    $asb=trim($_REQUEST["asb"]);
    if($_REQUEST["searchtyp"]!='')
    {
      $itemsperpage=916;
      //sonderzeichen umwandeln
      $asb = str_replace("ä","&auml;",$asb);
      $asb = str_replace("ö","&ouml;",$asb);
      $asb = str_replace("ü","&uuml;",$asb);
      $asb = str_replace("Ä","&Auml;",$asb);
      $asb = str_replace("Ö","&Ouml;",$asb);
      $asb = str_replace("Ü","&Uuml;",$asb);
      
      $sql="SELECT * FROM de_cyborg_auktion WHERE seller<>'$efta_user_id' AND bidder<>'$efta_user_id' AND itemname LIKE '%$asb%'";
      if($_REQUEST["minlvl"])
      {
        $lvl=(int)$_REQUEST["minlvl"];
        $sql.=" AND itemlevel >= '$lvl'";
      }
      if($_REQUEST["maxlvl"])
      {
        $lvl=(int)$_REQUEST["maxlvl"];
        $sql.=" AND itemlevel <= '$lvl'";
      }
      if($_REQUEST["searchtyp"]>0)
      {
        $st=$_REQUEST["searchtyp"];
        $sql.=" AND itemtyp = '$st'";
      }

      $sql.=" AND time > UNIX_TIMESTAMP() ORDER BY itemlevel, price, id ASC";
      $result = mysql_query($sql, $eftadb);
      $itemmenge = mysql_num_rows($result);

      //daten mit itemmengenbeschränkung aus der db holen
      $sp=$_REQUEST["sp"];
      if($sp<=1)$sp=1;
      if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
      $sp=(int)$sp;
      if($sp<=1)$sp=1;
      $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
      $showmenge=$itemsperpage;
      $sql.=" LIMIT $showstart,$showmenge";
      $result = mysql_query($sql, $eftadb);

      $num = mysql_num_rows($result);
      if($num>0)
      {
        //tooltip generieren
        $anzbp=0;
        while($row = mysql_fetch_array($result))
        {
          //preise auslesen
          $price[]=$row["price"];
          $dprice[]=$row["dprice"];
          //auktionsid auslesen
          $aid[]=$row["id"];

          //zeit auslesen

          $time=$row["time"]-time();
          if($time>(3600*168))$azeit[]='> 7 Tage';
          if($time>(3600*96))$azeit[]='> 2 Tage';
          elseif($time>(3600*48))$azeit[]='> 48h';
          elseif($time>(3600*24))$azeit[]='> 24h';
          elseif($time>(3600*16))$azeit[]='> 16h';
          elseif($time>(3600*12))$azeit[]='> 12h';
          elseif($time>(3600*8))$azeit[]='> 8h';
          elseif($time>(3600*6))$azeit[]='> 6h';
          elseif($time>(3600*4))$azeit[]='> 4h';
          elseif($time>(3600*2))$azeit[]='> 2h';
          elseif($time>(3600))$azeit[]='> 1h';
          elseif($time>(1800))$azeit[]='> 30m';
          elseif($time>(1200))$azeit[]='> 20m';
          elseif($time>(600))$azeit[]='> 10m';
          else $azeit[]='< 10m';
          

          //erstelle den string für die mouse-over-box
          $BPText[$anzbp]=make_item_tooltipstring($row["itemid"],'-1','-1');

          //counter erhöhen
          $anzbp++;
        }

        echo '<br><table width="100%">';
        //kopfzeile ausgeben
        echo '<tr align="center">
        <td class="cell1"><b>Gegenstand</b></td>
        <td class="cell1" width="30"><b>Zeit</b></td>
        <td class="cell1" width="30"><b>Level</b></td>
        <td class="cell1" width="150"><b>akt. Preis</b></td>
        <td class="cell1" width="150"><b>Sofortkauf</b></td>
        </tr>';
        

        for($i=0;$i<$anzbp;$i++)
        {
          //preis designen
          if($dprice[$i]==0)$sofortkauf='-';
          else $sofortkauf='<a href="#" onClick="lnk(\'auktion=1&mp=1&skid='.$aid[$i].'\')">'.make_moneystring($dprice[$i]).'</a>';

          echo '<tr>
          <td nowrap class="cell" title="'.$BPText[$i].'"><b>'.$bp[$i][0].'</b></td>
          <td nowrap class="cell" align="center">'.$azeit[$i].'</td>
          <td class="cell" align="center">'.$bp[$i][3].'</td>';
          echo '<td nowrap class="cell" align="center"><a href="#" onClick="lnk(\'auktion=1&mp=1&gid='.$aid[$i].'\')">'.make_moneystring($price[$i]).'</a></td>';
          echo '<td nowrap class="cell" align="center">'.$sofortkauf.'</td>
          </tr>';
        }
        //evtl. untere leiste zum blättern anzeigen
        echo '<tr align="center"><td colspan="5">';
        echo '<table><tr>';
        //zurück
        if($sp>1)echo '<td width="100" align="center"><a href="#" onClick="lnk(\'auktion=1&mp=1&searchtyp='.$_REQUEST["searchtyp"].
        '&asb='.$_REQUEST["asb"].'&minlvl='.$_REQUEST["minlvl"].'&maxlvl='.$_REQUEST["maxlvl"].'&sp='.($sp-1).'\')">zur&uuml;ck</a></td>';
        else echo '<td width="100" align="center">&nbsp;</td>';
        //itemzahl
        $bis=$showstart+$showmenge;
        if($bis>$itemmenge)$bis=$itemmenge;
        echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
        //weiter
        if(($bis<$itemmenge))
        echo '<td width="100" align="center"><a href="#" onClick="lnk(\'auktion=1&mp=1&searchtyp='.$_REQUEST["searchtyp"].
        '&asb='.$_REQUEST["asb"].'&minlvl='.$_REQUEST["minlvl"].'&maxlvl='.$_REQUEST["maxlvl"].'&sp='.($sp+1).'\')">weiter</a></td>';
        else echo '<td width="100" align="center">&nbsp;</td>';
        echo '</tr></table></td></tr>';
        echo '</table>';
      }
      else echo '<br><b class="text1">Es konnte kein passender Gegenstand gefunden werden.</b>';
    }
  }
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //neue auktion starten
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  elseif($_REQUEST["mp"]==2)
  {
    echo'
    <tr>
    <td align="center">
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=1\')">&nbsp;Bieten&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=3\')">&nbsp;Meine Gebote&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=4\')">&nbsp;Meine Auktionen&nbsp;</a>
    <b class="gwaren">&nbsp;Auktion erstellen&nbsp;</b>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Auktionshaus verlassen&nbsp;</a>
    </td>
	</tr>';

    //wenn eine id übergeben wird, dann will er eine auktion erstellen
    if($_REQUEST["id"])
    {
      //überprüfen ob er das item auch hat
      $id=(int)$_REQUEST["id"];
      $did=(int)$_REQUEST["did"];
      //schauen ob er den gegenstand auch im rucksack hat
      $result = mysql_query("SELECT id, durability FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
      $num = mysql_num_rows($result);
      if($num>0)
      {
      	$row = mysql_fetch_array($result);
      	//daten des items laden
        $item_durability=0;
        $filename='eftadata/items/'.$id.'.item';
        include($filename);
        //verkaufswert
        $hasmoney=$item_worth+2000000000;
        $hasmoney=strval($hasmoney);
        $kupfer='';$silber='';$gold='';$platin='';
        $kupfer.=$hasmoney[8];
        $kupfer.=$hasmoney[9];
        $silber.=$hasmoney[6];
        $silber.=$hasmoney[7];
        $gold.=$hasmoney[4];
        $gold.=$hasmoney[5];
        $platin.=$hasmoney[1];
        $platin.=$hasmoney[2];
        $platin.=$hasmoney[3];
        $platin=round($platin);
        $preis='';
        if($platin>0)$preis=$platin.' <img src="'.$gpfad.'co4.gif" alt="Platin"> ';
        if(round($gold>0) OR $platin>0)$preis.=$gold.' <img src="'.$gpfad.'co3.gif" alt="Gold"> ';
        if(round($silber>0) OR round($gold>0) OR $platin>0)$preis.=$silber.' <img src="'.$gpfad.'co2.gif" alt="Silber"> ';
        if(round($kupfer>0) OR round($silber>0) OR round($gold>0) OR $platin>0)$preis.=$kupfer.' <img src="'.$gpfad.'co1.gif" alt="Kupfer">';

        //erstellungsmaske ausgeben
        //wenn man es nicht handeln kann, das dort direkt anzeigen
        if($item_auction_lock!=1)$auctionlock='';else $auctionlock=' (kann nicht gehandelt werden)';
        if($auctionlock=='')
        {
          if($item_durability==$row["durability"])$auctionlock='';else $auctionlock=' (kann nicht gehandelt werden)';
        }
        echo '<tr><td class="cell">Auktionsgegenstand: '.$item_name.$auctionlock.'</td></tr>';
        echo '<tr><td class="cell">Verkaufspreis beim H&auml;ndler: '.$preis.'</td></tr>';
        //startpreis
        echo '<tr><td class="cell1">Startpreis:
              <input id="v1" type="text" name="pstartpreis" size="4" maxlength="4" value="'.$platin.'"> <img src="'.$gpfad.'co4.gif" alt="Platin">
              <input id="v2" type="text" name="gstartpreis" size="2" maxlength="2" value="'.$gold.'"> <img src="'.$gpfad.'co3.gif" alt="Gold">
              <input id="v3" type="text" name="sstartpreis" size="2" maxlength="2" value="'.$silber.'"> <img src="'.$gpfad.'co2.gif" alt="Silber">
              <input id="v4" type="text" name="kstartpreis" size="2" maxlength="2" value="'.$kupfer.'"> <img src="'.$gpfad.'co1.gif" alt="Kupfer">
              </td></tr>';
        //sofortkauf
        echo '<tr><td class="cell1">Sofortkaufpreis:
              <input id="v5" type="text" name="pskpreis" size="4" maxlength="4" value=""> <img src="'.$gpfad.'co4.gif" alt="Platin">
              <input id="v6" type="text" name="gskpreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co3.gif" alt="Gold">
              <input id="v7" type="text" name="sskpreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co2.gif" alt="Silber">
              <input id="v8" type="text" name="kskpreis" size="2" maxlength="2" value=""> <img src="'.$gpfad.'co1.gif" alt="Kupfer">
              &nbsp;(optional)</td></tr>';
        //auktionsdauer
        echo '<tr><td class="cell">Auktionsdauer in Stunden:
              <input type="radio" name="atime" value="2"> 2
              <input type="radio" name="atime" value="4"> 4
              <input type="radio" name="atime" value="6"> 6
              <input type="radio" name="atime" value="8"> 8
              <input type="radio" name="atime" value="12"> 12
              <input type="radio" name="atime" value="16"> 16
              <input type="radio" name="atime" value="24"> 24
              <input type="radio" name="atime" value="48" checked="checked"> 48
              <input type="radio" name="atime" value="96"> 96
              <input type="radio" name="atime" value="168"> 168
              <input type="radio" name="atime" value="336"> 336
              </td></tr>';
        
        //auktionsgebühr
        echo '<tr><td class="cell1">Die Auktionsgeb&uuml;hr betr&auml;gt 1% vom Startpreis/Sofortkaufpreis (richtet sich nach dem gr&ouml;&szlig;eren Wert),
              jedoch mindestens 10 <img src="'.$gpfad.'co1.gif" alt="Kupfer"></td></tr>';
        ?>
        <script>
    	function start_auktion()
    	{
    		var v1=$("#v1").val();
    		var v2=$("#v2").val();
    		var v3=$("#v3").val();
    		var v4=$("#v4").val();
    		var v5=$("#v5").val();
    		var v6=$("#v6").val();
    		var v7=$("#v7").val();
    		var v8=$("#v8").val();
   			var atime=$("input[name='atime']:checked").val();    		
    		
    		lnk('auktion=1&mp=2&startauktion=1&itemid=<?php echo $id;?>&itemdid=<?php echo $did;?>'+
    	    	'&pstartpreis='+v1+'&gstartpreis='+v2+'&sstartpreis='+v3+'&kstartpreis='+v4+
    	    	'&pskpreis='+v5+'&gskpreis='+v6+'&sskpreis='+v7+'&kskpreis='+v8+
    	    	'&atime='+atime);
    	}
        </script>
        <?php
        
        //start/abbrechen-button
        echo '<tr align="center"><td>
              <a class="gwaren" href="#" onClick="start_auktion()">&nbsp;Auktion starten&nbsp;</a>
              <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=2\')">&nbsp;abbrechen&nbsp;</a>
              </td></tr></table>';
		
        rahmen1_unten();
		
		//infoleiste anzeigen
		show_infobar();
        
        die('</div></body></html>');
      }//ende if has item
      else
      die('</table></div></body></html>');
    }
    else //ansonsten rucksackinhalt anzeigen
    {
      echo '<table width="100%">';
      //rucksackinhalt darstellen
      //ausrüstung auslesen und tooltip generieren
      $anzbp=0;
      //schauen wieviel items man bei hat
      $itemsperpage=919;
      $result = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id'", $eftadb);
      $itemmenge = mysql_num_rows($result);

      $sp=$_REQUEST["sp"];
      if($sp<=1)$sp=1;
      if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
      $sp=(int)$sp;
      $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
      $showmenge=$itemsperpage;
      $result = mysql_query("SELECT id, durability, uses FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id' ORDER BY typ LIMIT $showstart,$showmenge", $eftadb);
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
        echo '<tr align="center">';
        echo '<td class="'.$bg.'" title="'.$BPText[$i].'"><b>'.$bp[$i][0].' <a href="#" onClick="lnk(\'auktion=1&mp=2&id='.$bp[$i][1].'&did='.$bp[$i][5].'\')" title="Auktion starten">[A]</a></td>';
        echo '</tr>';
      }
      if($anzbp>0)
      {
      //evtl. untere leiste zum blättern anzeigen
      echo '<tr align="center"><td colspan="2">';
      echo '<table><tr>';
      //zurück
      if($sp>1)echo '<td width="100" align="center"><a href="#" onClick="lnk(\'auktion=1&mp=2&sp='.($sp-1).'\'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="#" onClick="lnk(\'auktion=1&mp=2&sp='.($sp+1).'\')">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
      }

      echo'</table>';
    }//ende if id
  }
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //auktionen für die man bietet
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  elseif($_REQUEST["mp"]==3)
  {
    echo'
    <tr>
    <td align="center">
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=1\')">&nbsp;Bieten&nbsp;</a>
    <b class="gwaren">&nbsp;Meine Gebote&nbsp;</b>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=4\')">&nbsp;Meine Auktionen&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=2\')">&nbsp;Auktion erstellen&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Auktionshaus verlassen&nbsp;</a>
    </td>
	</tr></table>';
	
    //ausgabe der eigenen gebote
    $itemsperpage=918;
    //schauen wieviel es gibt
    $sql="SELECT * FROM de_cyborg_auktion WHERE time > UNIX_TIMESTAMP() AND bidder='$efta_user_id' ORDER BY time, itemlevel, price ASC";
    $result = mysql_query($sql, $eftadb);
    $itemmenge = mysql_num_rows($result);

    //daten mit itemmengenbeschränkung aus der db holen
    $sp=$_REQUEST["sp"];
    if($sp<=1)$sp=1;
    if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
    $sp=(int)$sp;
    if($sp<=1)$sp=1;
    $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
    $showmenge=$itemsperpage;
    $sql.=" LIMIT $showstart,$showmenge";
    $result = mysql_query($sql, $eftadb);

    $num = mysql_num_rows($result);
    if($num>0)
    {
      //tooltip generieren
      $anzbp=0;
      while($row = mysql_fetch_array($result))
      {
        //preise auslesen
        $price[]=$row["price"];
        $dprice[]=$row["dprice"];
        //auktionsid auslesen
        $aid[]=$row["id"];

        //zeit auslesen

        $time=$row["time"]-time();
          if($time>(3600*168))$azeit[]='> 7 Tage';
          if($time>(3600*96))$azeit[]='> 2 Tage';
          elseif($time>(3600*48))$azeit[]='> 48h';
          elseif($time>(3600*24))$azeit[]='> 24h';
          elseif($time>(3600*16))$azeit[]='> 16h';
          elseif($time>(3600*12))$azeit[]='> 12h';
          elseif($time>(3600*8))$azeit[]='> 8h';
          elseif($time>(3600*6))$azeit[]='> 6h';
          elseif($time>(3600*4))$azeit[]='> 4h';
          elseif($time>(3600*2))$azeit[]='> 2h';
          elseif($time>(3600))$azeit[]='> 1h';
          elseif($time>(1800))$azeit[]='> 30m';
          elseif($time>(1200))$azeit[]='> 20m';
          elseif($time>(600))$azeit[]='> 10m';
          else $azeit[]='< 10m';
        
        //erstelle den string für die mouse-over-box
        $BPText[$anzbp]=make_item_tooltipstring($row["itemid"],'-1', '-1');

        //counter erhöhen
        $anzbp++;
      }

      echo '<table width="100%">';
      //kopfzeile ausgeben
      echo '<tr align="center">
      <td class="cell1"><b>Gegenstand</b></td>
      <td class="cell1" width="50"><b>Zeit</b></td>
      <td class="cell1" width="50"><b>Level</b></td>
      <td class="cell1" width="100"><b>Gebot</b></td>
      </tr>';

      for($i=0;$i<$anzbp;$i++)
      {
        //preis designen
        echo '<tr>
        <td class="cell" title="'.$BPText[$i].'"><b>'.$bp[$i][0].'</b></td>
        <td class="cell" align="center">'.$azeit[$i].'</td>
        <td class="cell" align="center">'.$bp[$i][3].'</td>';
        echo '<td class="cell" align="center">'.make_moneystring($price[$i]).'</td>';
        echo '</tr>';
      }
      echo '</table>';
      //evtl. untere leiste zum blättern anzeigen
      
      //echo '<tr align="center"><td colspan="4">';
      /*
      echo '<table><tr>';
      //zurück
      if($sp>1)echo '<td width="100" align="center"><a href="eftamain.php?auktion=1&mp=3&sp='.($sp-1).'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="eftamain.php?auktion=1&mp=3&sp='.($sp+1).'">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
      echo '</table>';*/
    }
    else echo '<table width="100%"><tr><td align="center" class="text1"><b>Es gibt keine aktiven Gebote.</b></td></tr></table>';
    //echo '</table>';
  }
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //eigene auktionen
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  elseif($_REQUEST["mp"]==4)
  {
    echo'
    <tr>
    <td align="center">
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=1\')">&nbsp;Bieten&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=3\')">&nbsp;Meine Gebote&nbsp;</a>
    <b class="gwaren">&nbsp;Meine Auktionen&nbsp;</b>
    <a class="gwaren" href="#" onClick="lnk(\'auktion=1&mp=2\')">&nbsp;Auktion erstellen&nbsp;</a>
    <a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Auktionshaus verlassen&nbsp;</a>
    </td>
	</tr>';

    //ausgabe der eigenen auktionen
    $itemsperpage=918;
    //schauen wieviel es gibt
    $sql="SELECT * FROM de_cyborg_auktion WHERE time > UNIX_TIMESTAMP() AND seller='$efta_user_id' ORDER BY time, itemlevel, price ASC";
    $result = mysql_query($sql, $eftadb);
    $itemmenge = mysql_num_rows($result);

    //daten mit itemmengenbeschränkung aus der db holen
    $sp=$_REQUEST["sp"];
    if($sp<=1)$sp=1;
    if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
    $sp=(int)$sp;
    if($sp<=1)$sp=1;
    $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
    $showmenge=$itemsperpage;
    $sql.=" LIMIT $showstart,$showmenge";
    $result = mysql_query($sql, $eftadb);

    $num = mysql_num_rows($result);
    if($num>0)
    {
      //tooltip generieren
      $anzbp=0;
      while($row = mysql_fetch_array($result))
      {
        //preise auslesen
        $price[]=$row["price"];
        $dprice[]=$row["dprice"];
        //auktionsid auslesen
        $aid[]=$row["id"];
        $bidder[]=$row["bidder"];
        //zeit auslesen

        $time=$row["time"]-time();
          if($time>(3600*168))$azeit[]='> 7 Tage';
          if($time>(3600*96))$azeit[]='> 2 Tage';
          elseif($time>(3600*48))$azeit[]='> 48h';
          elseif($time>(3600*24))$azeit[]='> 24h';
          elseif($time>(3600*16))$azeit[]='> 16h';
          elseif($time>(3600*12))$azeit[]='> 12h';
          elseif($time>(3600*8))$azeit[]='> 8h';
          elseif($time>(3600*6))$azeit[]='> 6h';
          elseif($time>(3600*4))$azeit[]='> 4h';
          elseif($time>(3600*2))$azeit[]='> 2h';
          elseif($time>(3600))$azeit[]='> 1h';
          elseif($time>(1800))$azeit[]='> 30m';
          elseif($time>(1200))$azeit[]='> 20m';
          elseif($time>(600))$azeit[]='> 10m';
          else $azeit[]='< 10m';
        
        //erstelle den string für die mouse-over-box
        $BPText[$anzbp]=make_item_tooltipstring($row["itemid"],'-1', '-1');

        //counter erhöhen
        $anzbp++;
      }

      echo '<table width="100%">';
      //kopfzeile ausgeben
      echo '<tr align="center">
      <td class="cell1"><b>Gegenstand</b></td>
      <td class="cell1" width="50"><b>Zeit</b></td>
      <td class="cell1" width="50"><b>Level</b></td>
      <td class="cell1" width="100"><b>akt. Gebot</b></td>
      <td class="cell1" width="100"><b>Sofortkauf</b></td>
      </tr>';

      for($i=0;$i<$anzbp;$i++)
      {
        //preis designen
        if($dprice[$i]==0)$sofortkauf='-';
        else $sofortkauf=make_moneystring($dprice[$i]);
        echo '<tr align="center">
        <td class="cell" align="left" title="'.$BPText[$i].'"><b>'.$bp[$i][0].'</b></td>
        <td class="cell">'.$azeit[$i].'</td>
        <td class="cell">'.$bp[$i][3].'</td>';
        if($bidder[$i]>0)
          echo '<td class="cell">'.make_moneystring($price[$i]).'</td>';
        else
          echo '<td class="cell">kein Gebot</td>';
        echo '<td class="cell">'.$sofortkauf.'</td></tr>';
      }
      //evtl. untere leiste zum blättern anzeigen
      echo '<tr align="center"><td colspan="5">';
      echo '<table><tr>';
      //zurück
      if($sp>1)echo '<td width="100" align="center"><a href="eftamain.php?auktion=1&mp=4&sp='.($sp-1).'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="eftamain.php?auktion=1&mp=4&sp='.($sp+1).'">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
      //echo '</table>';
    }
    else echo '<tr><td align="center" class="text1"><b>Es gibt keine aktiven Aktionen.</b></td></tr>';
    echo '</table>';
  }

  //echo '</div>';
  
  rahmen1_unten();
  rahmen0_unten();
  
	//infoleiste anzeigen
	show_infobar();
  
  //echo '</form>';
  die('</body></html>');
}

?>
