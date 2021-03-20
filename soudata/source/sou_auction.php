<?php
//daten zur ansicht
echo '<br>';

echo '<div align="center">';

//die('under construction');

rahmen0_oben();

  //geb�udelevel des auktionszentrums auslesen
  $geb_level=get_max_frac_bldg_level(11);
  if($geb_level>0 OR $numsrb==1)
  {
  	echo '<form action="sou_main.php" method="POST" name="f">';
	echo '<input type="hidden" name="action" value="tradepage">';
	echo '<input type="hidden" name="do" value="1">';

	//session-variablen um bei erneuten auktionen schon die werte vordefiniert zu haben
	//wenn es sie nicht gibt, vorbelegen
	if(!isset($_SESSION['sou_auction_v1']))$_SESSION['sou_auction_v1']=1;
	if(!isset($_SESSION['sou_auction_v2']))$_SESSION['sou_auction_v2']=0;
	if(!isset($_SESSION['sou_auction_v3']))$_SESSION['sou_auction_v3']=48;
	
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	

//sofortkauf
if($_REQUEST["skid"]){
	//transaktionsbeginn
	if (setLock($player_user_id))
	{
	  //schauen ob es die auktion auch gibt
	  $auktionid=intval($_REQUEST["skid"]);
	  $result = mysql_query("SELECT * FROM sou_ship_module WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND time>=UNIX_TIMESTAMP()+10 AND location=2 AND (fraction='$player_fraction' OR fraction=0)", $soudb);
	  $num = mysql_num_rows($result);
	  if($num==1)
	  {
		//daten auslesen
		$row = mysql_fetch_array($result);
		$aktgebot=$row["price"];
		$sofortkauf=$row["dprice"];
		$auctioncurrency=$row["auctioncurrency"];

		//geld/credits auslesen
		if($auctioncurrency==0)$hasmoney=has_money($player_user_id);
		elseif($auctioncurrency==1)$hasmoney=has_credits($ums_user_id);
		elseif($auctioncurrency==2)$hasmoney=has_darkmatter($player_user_id);
		elseif($auctioncurrency==3)$hasmoney=has_baosin($player_user_id);

		//schauen ob der preis gro� genug ist und man auch das geld hat
		if($hasmoney>=$sofortkauf)
		{
			//auktionsdatensatz reservieren
			$result = mysql_query("UPDATE sou_ship_module SET aupdate=1 WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND aupdate=0 AND location=2 AND (fraction='$player_fraction' OR fraction=0)", $soudb);            

			$num = mysql_affected_rows();
			if($num==1)
			{
			  //auktionsdaten nochmal auslesen
			  $result = mysql_query("SELECT * FROM sou_ship_module WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND location=2 AND (fraction='$player_fraction' OR fraction=0)", $soudb);
			  $row = mysql_fetch_array($result);
			  $aktgebot=$row["price"];
			  $sofortkauf=$row["dprice"];
			  $aktbidder=$row["bidder"];
			  $modulname=$row["name"];

			  //nochmal geld/credits �berpr�fen
			  if($hasmoney>=$sofortkauf)
			  {
				//dem neuen bieter das geld/credits abziehen
				if($auctioncurrency==0)change_money($player_user_id, $sofortkauf*(-1));
				elseif($auctioncurrency==1)change_credits($ums_user_id, $sofortkauf*(-1), 'EA Auktionshaus Sofortkauf f�r '.$modulname);
				elseif($auctioncurrency==2)change_darkmatter($player_user_id, $sofortkauf*(-1));
				elseif($auctioncurrency==3)change_baosin($player_user_id, $sofortkauf*(-1));

				//aktuelles gebot der auktion erh�hen und den neuen bieter hinterlegen
				//preis auf 0 setzen, dann ist es immer ein sofortkauf
				mysql_query("UPDATE sou_ship_module SET bidder='$player_user_id', price=0, aupdate=0, time=0 WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid'", $soudb);

				//dem alten spieler das geld/credits wieder gutschreiben

				mysql_query("INSERT INTO sou_auction_payback SET user_id='$aktbidder', auctioncurrency='$auctioncurrency', amount='$aktgebot', modulname='$modulname'", $soudb);

				/*
				if($auctioncurrency==0)change_money($aktbidder, $aktgebot);
				else 
				{
				  //ext_user_id auslesen
				  $resultx = mysql_query("SELECT * FROM sou_user_data WHERE user_id='$aktbidder'", $soudb);
				  $rowx = mysql_fetch_array($resultx);
				  $ext_user_id=$rowx["ext_user_id"];
				  change_credits($ext_user_id, $aktgebot, 'EA Auktionshaus R�ckbuchung f�r '.$modulname);
				}
				*/

				//echo '<div align="left">A '.$gebot.' B '.$aktgebot.' C '.$hasmoney.'</div>';
				$auktionmsg="Der Gegenstand wurde erworben und wird in K&uuml;rze zugestellt.";
			  }
			  else $auktionmsg="Du hast nicht genug Geld.";
			}
			else $auktionmsg="Ein anderer Bieter hat gerade sein Gebot abgegeben.";
		}
		else $auktionmsg="Du hast nicht genug Geld.";
	  }
	  else $auktionmsg="Diese Auktion ist nicht mehr aktiv.";

	  //transaktionsende
	  $erg = releaseLock($player_user_id); //L�sen des Locks und Ergebnisabfrage
	  if ($erg)
	  {
		  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
	  }
	  else
	  {
		  print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
	  }
	}// if setlock-ende
	else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}

//f�r eine auktion bieten
if($_REQUEST["bieten"]){
  //transaktionsbeginn
  if (setLock($player_user_id))  {
    //schauen ob es die auktion auch gibt
    $auktionid=intval($_REQUEST["auktionid"]);
    $result = mysql_query("SELECT * FROM sou_ship_module WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND time>=UNIX_TIMESTAMP()+10 AND location=2 AND (fraction='$player_fraction' OR fraction=0)", $soudb);
    $num = mysql_num_rows($result);
    if($num==1){
      //daten auslesen
      $row = mysql_fetch_array($result);
      $aktgebot=$row["price"];
      $sofortkauf=$row["dprice"];
      $auctioncurrency=$row["auctioncurrency"];

      //gebot des spielers
      $gebot=intval($_REQUEST["preis"]);

      //geld/credits auslesen
      if($auctioncurrency==0)$hasmoney=has_money($player_user_id);
      elseif($auctioncurrency==1)$hasmoney=has_credits($ums_user_id);
      elseif($auctioncurrency==2)$hasmoney=has_darkmatter($player_user_id);
	  elseif($auctioncurrency==3)$hasmoney=has_baosin($player_user_id);
      
      //schauen ob man auch das geld hat
      if($hasmoney>=$gebot)
      {
        //schauen ob der preis gro� genug ist und man auch das geld hat
        if($gebot>$aktgebot)
        {
          //schauen, ob der preis evtl. �ber dem preis vom sofortkauf ist
          $flag=1;
          if($sofortkauf>0)if($gebot >= $sofortkauf)$flag=0;
          if($flag==1)
          {
            //auktionsdatensatz reservieren
            $result = mysql_query("UPDATE sou_ship_module SET aupdate=1 WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND aupdate=0 AND location=2 AND (fraction='$player_fraction' OR fraction=0)", $soudb);
            $num = mysql_affected_rows();
            if($num==1)
            {
              //auktionsdaten nochmal auslesen
              $result = mysql_query("SELECT * FROM sou_ship_module WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid' AND location=2", $soudb);
              $row = mysql_fetch_array($result);
              $aktgebot=$row["price"];
              $sofortkauf=$row["dprice"];
              $aktbidder=$row["bidder"];
              $modulname=$row["name"];
          
              //nochmal geld �berpr�fen
              if($gebot>=$aktgebot AND $hasmoney>=$gebot)
              {
                //dem neuen bieter das geld abziehen
      		  	if($auctioncurrency==0)change_money($player_user_id, $gebot*(-1));
      		  	elseif($auctioncurrency==1)change_credits($ums_user_id, $gebot*(-1), 'EA Auktionshaus Gebot f�r '.$modulname);
              	elseif($auctioncurrency==2)change_darkmatter($player_user_id, $gebot*(-1));
				elseif($auctioncurrency==3)change_baosin($player_user_id, $gebot*(-1));

                //aktuelles gebot der auktion erh�hen und den neuen bieter hinterlegen
                mysql_query("UPDATE sou_ship_module SET bidder='$player_user_id', price='$gebot', aupdate=0 WHERE user_id <> '$player_user_id' AND bidder <> '$player_user_id' AND id='$auktionid'", $soudb);

                //dem alten spieler das geld wieder gutschreiben
                
                mysql_query("INSERT INTO sou_auction_payback SET user_id='$aktbidder', auctioncurrency='$auctioncurrency', amount='$aktgebot', modulname='$modulname'", $soudb);
                
                /*
      			if($auctioncurrency==0)change_money($aktbidder, $aktgebot);
      			else 
      		    {
      		  	  //ext_user_id auslesen
            	  $resultx = mysql_query("SELECT * FROM sou_user_data WHERE user_id='$aktbidder'", $soudb);
            	  $rowx = mysql_fetch_array($resultx);
            	  $ext_user_id=$rowx["ext_user_id"];
      		      change_credits($ext_user_id, $aktgebot, 'EA Auktionshaus R�ckbuchung f�r '.$modulname);
      		    }
      			*/
                
                $auktionmsg="F&uuml;r den Gegenstand wurde geboten.";
              }
              else $auktionmsg="Du hast nicht genug Geld.";
            }
            else $auktionmsg="Ein anderer Bieter hat gerade sein Gebot abgegeben.";
          }
          else $auktionmsg="Das Gebot mu� unter dem Sofortkaufpreis liegen.";
        }
        else $auktionmsg="Du hast nicht genug geboten.";
      }
      else $auktionmsg="Du hast nicht genug Geld.";
    }
    else $auktionmsg="Diese Auktion ist nicht mehr aktiv.";

    //transaktionsende
    $erg = releaseLock($player_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}//auktion erstellen ende



//auktion erstellen
if($_REQUEST["startauktion"])
{
  //transaktionsbeginn
  if (setLock($player_user_id))
  {
    //preis festlegen
    $preis=intval($_REQUEST["startpreis"]);
    $skpreis=intval($_REQUEST["skpreis"]);
    //schauen ob man die geb�hr bezahlen kann
    //die geb�hr betr�gt 1% vom startpreis/sofortkaufpreis (je nachdem was gr��er ist) und ist nicht kleiner als 10 kupfer
    $gebuehr=round($preis/100);
    if($gebuehr<round($skpreis/100))$gebuehr=round($skpreis/100);
    if($gebuehr<10)$gebuehr=10;
    
    $hasmoney=has_money($player_user_id);
    //hat man genug geld f�r die geb�hr?
    if($hasmoney>=$gebuehr)
    {
      //ist der sofortkaufpreis gr��er als der startpreis?
      $startauktion=1;
      if($skpreis>0)if($skpreis<$preis)$startauktion=0;
      if($startauktion==1)
      {
        $id=(int)$_REQUEST["itemid"];
        //schauen ob er das modul auch im mkomplex hat
        $result = mysql_query("SELECT id FROM sou_ship_module WHERE id='$id' AND location=1 AND user_id='$player_user_id'", $soudb);
        $num = mysql_num_rows($result);
        if($num>0)
        {
          $row = mysql_fetch_array($result);
          //schauen ob dieser gegenstand im ah gehandelt werden darf
          if($item_auction_lock!=1)
          {
            //schauen ob der gegensand besch�digit ist, dann darf er nicht gehandelt werden
          	if($item_durability==$row["durability"])
          	{
          	  //schauen ob man noch eine auktion einstellen darf, abh. von der gebäudegröße
              $result = mysql_query("SELECT id FROM sou_ship_module WHERE user_id='$player_user_id' AND location=2", $soudb);
              $anz = mysql_num_rows($result);
          	  if($geb_level>$anz)
          	  {
          		//Gebühr abziehen
          	    change_money($player_user_id, $gebuehr*-1);

                //auktions-db-eintrag erzeugen
                $zeit=intval($_REQUEST["atime"]);
                if($zeit!=2 AND $zeit!=4 AND $zeit!=6 AND $zeit!=8 AND $zeit!=12 AND $zeit!=16 AND $zeit!=24 AND $zeit!=48 AND $zeit!=96 AND $zeit!=168 AND $zeit!=336)$zeit=48;
                 
                //sessionvariablen setzen
                $_SESSION['sou_auction_v1']=$preis;
                $_SESSION['sou_auction_v2']=$skpreis;
                $_SESSION['sou_auction_v3']=$zeit;         
                 
                $zeit=time()+$zeit*3600;
              
                mysql_query("UPDATE sou_ship_module SET price='$preis', dprice='$skpreis', time='$zeit', location=2 WHERE id='$id' AND location=1 AND user_id='$player_user_id'", $soudb);


                
                
                //meldung dass alles geklappt hat
                $auktionmsg="Die Auktion wurde gestartet.";
          	  }else 
      	    $auktionmsg="Du kannst nich mehr Auktionen starten. Pro Geb&auml;udestufe ist nur eine laufende Auktion m&ouml;glich.";
                
          
          	}else 
      	  $auktionmsg="Besch&auml;digte Ware kann nicht im Auktionshaus gehandelt werden.";
          }
          else 
      	  $auktionmsg="Diese Ware kann nicht im Auktionshaus gehandelt werden.";
        }
      }
      else $auktionmsg="Der Startpreis darf nicht gr&ouml;&szlig;er als der Sofortkaufpreis sein.";
    }
    else $auktionmsg="Du kannst die Geb&uuml;hr nicht bezahlen.";
    
    //transaktionsende
    $erg = releaseLock($player_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}//auktion erstellen ende


///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
// �berpr�fen ob der spieler r�ckzahlungen erh�lt
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////

$db_daten=mysql_query("SELECT * FROM sou_auction_payback WHERE user_id='$player_user_id'",$soudb);
$num = mysql_num_rows($db_daten);

if($num>0)
{
  //tabelle locken und die nutzdaten auslesen
  //transaktionsbeginn
  if (setLock($player_user_id))
  {
  	echo '<br>';
	rahmen2_oben();
   echo '<table width="100%">
    <tr>
    <td class="cell1"><b>Du wurdest in folgenden Auktionen &uuml;berboten und erh&auml;ltst deine Einzahlung zur&uuml;ck:</b><br>';
  	
  	//eintr�ge auslesen und abarbeiten
	$db_daten=mysql_query("SELECT * FROM sou_auction_payback WHERE user_id='$player_user_id'",$soudb);  	
	while($row = mysql_fetch_array($db_daten))
    {
      $user_id=$row["user_id"];
      $auctioncurrency=$row["auctioncurrency"];
      $amount=$row["amount"];
      $modulname=$row["modulname"];
      
      if($auctioncurrency==0)$showcurrency='<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
      elseif($auctioncurrency==1)$showcurrency='<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
      elseif($auctioncurrency==2)$showcurrency='<img src="'.$gpfad.'a27.gif" alt="Dunkle Materie" title="Dunkle Materie">';
	  elseif($auctioncurrency==3)$showcurrency='<img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin">';
      
      
      echo '<br>'.$modulname.': '.number_format($amount, 0,"",".").' '.$showcurrency;
      
      //zastari/credits gutschreiben
      
  	  if($auctioncurrency==0)change_money($player_user_id, $amount);
      elseif($auctioncurrency==1)change_credits($ums_user_id, $amount, 'EA Auktionshaus R�ckbuchung f�r '.$modulname);
      elseif($auctioncurrency==2)change_darkmatter($player_user_id, $amount);
	  elseif($auctioncurrency==3)change_baosin($player_user_id, $amount);
      
      //datensatz l�schen
      mysql_query("DELETE FROM sou_auction_payback WHERE user_id='$player_user_id' AND auctioncurrency='$auctioncurrency' AND amount='$amount' AND modulname='$modulname' LIMIT 1",$soudb);

    }
	echo '<br><br></td></tr></table>';
  	rahmen2_unten();
  	      
    //transaktionsende
    $erg = releaseLock($player_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende
}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
//das auktionshaus anzeigen
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////

$auktion=1;
if($_REQUEST["auktion"]==1 OR $auktion==1)
{
  echo '<form action="sou_main.php" method="post">';
  echo '<input type="hidden" name="action" value="auctionpage">';

  if($auktionmsg!='')
  {
  	  echo '<br>';
	  rahmen2_oben();
  	  echo $auktionmsg;
  	  rahmen2_unten();
  }

  echo '<br>';
  $output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
    <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
    <td><b>Auktionszentrum (Fraktionsstufe '.$geb_level.')</b> <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" 
    title="Information&Das Auktionszentrum erm&ouml;glicht das Handeln mit anderen B&uuml;rgern. Es basiert auf dem altgedienten System der Versteigerung, 
    auch ein Sofortkauf ist m&ouml;glich. Pro Geb&auml;udestufe ist eine laufende Auktion m&ouml;glich.<br><br>
    Die Farbgebung der Waren richtet sich nach ihrer Qualit&auml;t:<br>
    <font color=#'.$colors_items[0].'>Massenware</font><br><font color=#'.$colors_items[1].'>Gute Ware</font><br><font color=#'.$colors_items[2].'>Seltene Ware</font><br><font color=#'.$colors_items[3].'>Mystische Ware</font><br><font color=#'.$colors_items[4].'>Artefakte</font>"></td>
    <td width="120"><a href="sou_main.php?action=modulholdpage"><div class="b1">Modulkomplex</div></a> </td>
    </tr></table>';
    rahmen1_oben($output);  	

  
  echo '<table width="100%">';
  /*
  <tr>
  <td class="cell1"><b>
  Dein Verm&ouml;gen: &nbsp;'.number_format($player_money, 0,"",".").' <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari"> / '.number_format(has_credits($ums_user_id), 0,"",".").' <img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">
   </td>
  </tr>';
  */

  if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //f�r eine auktion bieten
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  if($_REQUEST["mp"]==1)
  {
    echo'
    <tr>
    <td align="center">
      <table border="0" cellpadding="0" cellspacing="2"><tr align="center">
      <td><a href="sou_main.php?action=auctionpage&mp=1"><div class="b1"><b>Bieten</b></div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=3"><div class="b1">Meine Gebote</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=4"><div class="b1">Meine Auktionen</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=2"><div class="b1">Auktion erstellen</div></a></td>
      </tr></table>
    </td>
	</tr>';
	
	//hier kann man f�r ein item bieten
    if($_REQUEST["gid"])
    {
      $auktionsid=intval($_REQUEST["gid"]);
      //item und preis aus der db holen
      $result = mysql_query("SELECT * FROM sou_ship_module WHERE user_id<>'$player_user_id' AND id='$auktionsid' AND location=2", $soudb);
      $num = mysql_num_rows($result);
      if($num==1)
      {
        $row = mysql_fetch_array($result);

        echo '<tr><td class="cell">Auktionsgegenstand: '.make_modul_name($row).'</td></tr>';
        //startpreis
        echo '<tr><td class="cell1">Aktuelles Gebot: '.number_format($row["price"], 0,"",".").'</td></tr>';
        if($row["dprice"]>0)echo '<tr><td class="cell">Sofortkauf: '.number_format($row["dprice"], 0,"",".").'</td></tr>';
        else echo '<tr><td class="cell">Sofortkauf: - </td></tr>';
        //sofortkauf
        if($row["auctioncurrency"]==0)$showcurrency='<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
        elseif($row["auctioncurrency"]==1)$showcurrency='<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
        elseif($row["auctioncurrency"]==2)$showcurrency='<img src="'.$gpfad.'a27.gif" alt="Dunkle Materie" title="Dunkle Materie">';
		elseif($row["auctioncurrency"]==3)$showcurrency='<img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin">';
        
        echo '<tr><td class="cell1">Dein Gebot:
              <input type="text" name="preis" size="10" maxlength="10" value=""> '.$showcurrency.'</td></tr>';

        //hiddenfeld f�r die item-id
        echo '<input type="hidden" name="auktionid" value="'.$auktionsid.'">';
        //hiddenfeld f�r die seitenanzeige
        echo '<input type="hidden" name="mp" value="1">';

        //start/abbrechen-button
        echo '<tr align="center"><td>
              <input type="submit" name="bieten" value="bieten">
              <input type="submit" name="cancelbieten" value="abbrechen">
              </td></tr>';

        
      }
      echo '</table>';	
      
      rahmen2_unten();
      echo '<br>';
      rahmen0_unten();
      
	  echo '</div>';
      echo '</form>';
      die('</body></html>');
    }

	//sofortkauf f�r ein item
	
	//hier ist der suchbereich
    //suchfeld, name (min. zeichen), minlevel, maxlevel
    echo '
    <tr>
    <td class="cell" align="center">
    Suchen: <input type="text" name="asb" size="25" maxlength="100" value="'.$_REQUEST["asb"].'">';
    
    //Min. Level <input type="text" name="minlvl" size="1" maxlength="2" value="'.$_REQUEST["minlvl"].'">
    //Max. Level <input type="text" name="maxlvl" size="1" maxlength="2" value="'.$_REQUEST["maxlvl"].'">';
    ?>
    Art:
    <select name="searchtyp">
    <option value="0" <?php if($_REQUEST["searchtyp"]==0) echo 'selected';?>>alles</option>
    <option value="1" <?php if($_REQUEST["searchtyp"]==1) echo 'selected';?>>Bergung</option>
    <option value="2" <?php if($_REQUEST["searchtyp"]==2) echo 'selected';?>>Klonmodul</option>
    <option value="3" <?php if($_REQUEST["searchtyp"]==3) echo 'selected';?>>Bergbau</option>
    <option value="4" <?php if($_REQUEST["searchtyp"]==4) echo 'selected';?>>Lebenserhaltung</option>
    <option value="5" <?php if($_REQUEST["searchtyp"]==5) echo 'selected';?>>Unterlichtantrieb</option>
    <option value="6" <?php if($_REQUEST["searchtyp"]==6) echo 'selected';?>>&Uuml;berlichtantrieb</option>
    <option value="7" <?php if($_REQUEST["searchtyp"]==7) echo 'selected';?>>Raumschiffzentrale</option>
    <option value="8" <?php if($_REQUEST["searchtyp"]==8) echo 'selected';?>>Waffen</option>
    <option value="9" <?php if($_REQUEST["searchtyp"]==9) echo 'selected';?>>Schilde</option>
    <option value="10" <?php if($_REQUEST["searchtyp"]==10) echo 'selected';?>>Forschung</option>
    </select>

    <?php
    echo '
    <input type="submit" name="searchbutton" value="Suche starten">
    </td>
    </tr></table>';

    //ausgabe der suchergebnisse
    $asb=trim($_REQUEST["asb"]);
    if($_REQUEST["searchtyp"]!='' OR 1==1)
    {
      $itemsperpage=916;
      //sonderzeichen umwandeln
      $asb = str_replace("�","&auml;",$asb);
      $asb = str_replace("�","&ouml;",$asb);
      $asb = str_replace("�","&uuml;",$asb);
      $asb = str_replace("�","&Auml;",$asb);
      $asb = str_replace("�","&Ouml;",$asb);
      $asb = str_replace("�","&Uuml;",$asb);

      //$sql="SELECT * FROM de_cyborg_auktion WHERE seller<>'$ums_user_id' AND bidder<>'$ums_user_id' AND itemname LIKE '%$asb%'";
      $sql="SELECT * FROM sou_ship_module WHERE (fraction='$player_fraction' OR fraction=0) AND time>=UNIX_TIMESTAMP()+10 AND user_id<>'$player_user_id' AND bidder<>'$player_user_id' AND location=2 AND name LIKE '%$asb%'";
    
      //lager
      if($_REQUEST["searchtyp"]==1)
      {
        $sql.=" AND canrecover > 0";
      }
      
      //reaktor
      if($_REQUEST["searchtyp"]==2)
      {
        $sql.=" AND canclone > 0";
      }

      //bergbau
      if($_REQUEST["searchtyp"]==3)
      {
        $sql.=" AND canmine > 0";
      }

      //lebenserhaltung
      if($_REQUEST["searchtyp"]==4)
      {
        $sql.=" AND givelife > 0";
      }    
      
      //unterlichtantrieb
      if($_REQUEST["searchtyp"]==5)
      {
        $sql.=" AND givesubspace > 0";
      }      

      //�berlichtantrieb
      if($_REQUEST["searchtyp"]==6)
      {
        $sql.=" AND givehyperdrive > 0";
      }            
      
      //raumschiffzentrale
      if($_REQUEST["searchtyp"]==7)
      {
        $sql.=" AND givecenter > 0";
      }            

      //waffen
      if($_REQUEST["searchtyp"]==8)
      {
        $sql.=" AND giveweapon > 0";
      }
      
      //schilde
      if($_REQUEST["searchtyp"]==9)
      {
        $sql.=" AND giveshield > 0";
      }

      //forschung
      if($_REQUEST["searchtyp"]==10)
      {
        $sql.=" AND giveresearch > 0";
      }

      
      //$sql.=" AND time > UNIX_TIMESTAMP() ORDER BY itemlevel, price, id ASC";
      $sql.=" AND time > UNIX_TIMESTAMP() ORDER BY quality DESC, price, id ASC";
      
      $result = mysql_query($sql, $soudb);
      $itemmenge = mysql_num_rows($result);

      //daten mit itemmengenbeschr�nkung aus der db holen
      $sp=$_REQUEST["sp"];
      if($sp<=1)$sp=1;
      if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
      $sp=(int)$sp;
      if($sp<=1)$sp=1;
      $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
      $showmenge=$itemsperpage;
      $sql.=" LIMIT $showstart,$showmenge";
      $result = mysql_query($sql, $soudb);

      $num = mysql_num_rows($result);
      if($num>0)
      {
        $anzbp=0;
        while($row = mysql_fetch_array($result))
        {
          //preise auslesen
          $price[]=$row["price"];
          $dprice[]=$row["dprice"];
          //auktionsid auslesen
          $aid[]=$row["id"];
          $modulfraction[]=$row["fraction"];
          //w�hrung auslesen
          $currency[]=$row["auctioncurrency"];

          //zeit auslesen

          $time=$row["time"]-time();
          if($time>(3600*168))$azeit[]='> 7 Tage';
          elseif($time>(3600*96))$azeit[]='> 4 Tage';
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

          //erstelle den string f�r die mouse-over-box

          $ttext=make_modul_info($row);
          $BPText[$anzbp]=make_modul_name_js($row).'&'.$ttext;
          $bp[$anzbp][0]=make_modul_name($row);
          $bp[$anzbp][1]=$row["id"];
          //counter erh�hen
          $anzbp++;        
        }

        echo '<br><table width="100%">';
        //kopfzeile ausgeben
        echo '<tr align="center">
        <td class="cell1"><b>Gegenstand</b></td>
        <td class="cell1" width="30"><b>Zeit</b></td>
        <td class="cell1" width="150"><b>akt. Preis</b></td>
        <td class="cell1" width="150"><b>Sofortkauf</b></td>
        </tr>';
        

        for($i=0;$i<$anzbp;$i++)
        {
          //preis designen
          if($dprice[$i]==0)$sofortkauf='-';
          else 
          {
            if($currency[$i]==0)$showcurrency='<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
            else $showcurrency='<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
            
          	$sofortkauf='<a href="sou_main.php?action=auctionpage&mp=1&skid='.$aid[$i].'"'." onclick=\"return confirm('".strip_tags($bp[$i][0])." per Sofortkauf erwerben?')\">".number_format($dprice[$i], 0,"",".").'</a> '.$showcurrency;
          }
          //�berpr�fen ob es eine fraktions�bergreifende auktion ist
          if($modulfraction[$i]==0)$hstr=' <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a15.gif" alt="fraktions&uuml;bergreifend" title="fraktions&uuml;bergreifend">';else {$hstr='';}
          
          echo '<tr>
          <td nowrap class="cell" title="'.$BPText[$i].'"><b>'.$bp[$i][0].$hstr.'</b></td>
          <td nowrap class="cell" align="center">'.$azeit[$i].'</td>';
          
          if($currency[$i]==0)$showcurrency='<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
          elseif($currency[$i]==1)$showcurrency='<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
          elseif($currency[$i]==2)$showcurrency='<img src="'.$gpfad.'a27.gif" alt="Dunkle Materie" title="Dunkle Materie">';
		  elseif($currency[$i]==3)$showcurrency='<img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin">';
                    
          echo '<td nowrap class="cell" align="center"><a href="sou_main.php?action=auctionpage&mp=1&gid='.$aid[$i].'">'.number_format($price[$i], 0,"",".").'</a> '.$showcurrency.'</td>';
          echo '<td nowrap class="cell" align="center">'.$sofortkauf.'</td>
          </tr>';
        }
        //evtl. untere leiste zum bl�ttern anzeigen
        echo '<tr align="center"><td colspan="5">';
        echo '<table><tr>';
        //zur�ck
        if($sp>1)echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=1&searchtyp='.$_REQUEST["searchtyp"].
        '&asb='.$_REQUEST["asb"].'&minlvl='.$_REQUEST["minlvl"].'&maxlvl='.$_REQUEST["maxlvl"].'&sp='.($sp-1).'">zur&uuml;ck</a></td>';
        else echo '<td width="100" align="center">&nbsp;</td>';
        //itemzahl
        $bis=$showstart+$showmenge;
        if($bis>$itemmenge)$bis=$itemmenge;
        echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
        //weiter
        if(($bis<$itemmenge))
        echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=1&searchtyp='.$_REQUEST["searchtyp"].
        '&asb='.$_REQUEST["asb"].'&minlvl='.$_REQUEST["minlvl"].'&maxlvl='.$_REQUEST["maxlvl"].'&sp='.($sp+1).'">weiter</a></td>';
        else echo '<td width="100" align="center">&nbsp;</td>';
        echo '</tr></table></td></tr>';
        echo '</table>';
      }
      else echo '<br><b class="text1">Es konnte keine passende Auktion gefunden werden.</b><br><br>';
    }
  }
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //neue auktion starten
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  elseif($_REQUEST["mp"]==2){
    echo'
    <tr>
    <td align="center">
      <table border="0" cellpadding="0" cellspacing="2"><tr align="center">
      <td><a href="sou_main.php?action=auctionpage&mp=1"><div class="b1">Bieten</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=3"><div class="b1">Meine Gebote</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=4"><div class="b1">Meine Auktionen</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=2"><div class="b1"><b>Auktion erstellen</b></div></a></td>
      </tr></table>
    </td>
	</tr>';

    //wenn eine id �bergeben wird, dann will er eine auktion erstellen
    if($_REQUEST["id"])
    {
      //�berpr�fen ob er das item auch hat
      $id=(int)$_REQUEST["id"];
      $did=(int)$_REQUEST["did"];
      //schauen ob er das modul auch im mkomplex hat
      $result = mysql_query("SELECT id, name FROM sou_ship_module WHERE id='$id' AND location=1 AND user_id='$player_user_id'", $soudb);
      $num = mysql_num_rows($result);
      if($num>0)
      {
      	$row = mysql_fetch_array($result);

        //erstellungsmaske ausgeben
        //wenn man es nicht handeln kann, das dort direkt anzeigen
        if($item_auction_lock!=1)$auctionlock='';else $auctionlock=' (kann nicht gehandelt werden)';
        if($auctionlock=='')
        {
          if($item_durability==$row["durability"])$auctionlock='';else $auctionlock=' (kann nicht gehandelt werden)';
        }
        echo '<tr><td class="cell">Auktionsgegenstand: '.make_modul_name($row).'</td></tr>';
        //startpreis
        echo '<tr><td class="cell1">Startpreis:
              <input type="text" name="startpreis" size="10" maxlength="10" value="'.$_SESSION['sou_auction_v1'].'"> Zastari
              </td></tr>';
        //sofortkauf
        echo '<tr><td class="cell1">Sofortkaufpreis:
              <input type="text" name="skpreis" size="10" maxlength="10" value="'.$_SESSION['sou_auction_v2'].'"> Zastari
              &nbsp;(optional)</td></tr>';
        //auktionsdauer
                //sessionvariablen setzen

        $zeiten=array(2,4,6,8,12,16,24,48,96,168,336);
        echo '<tr><td class="cell">Auktionsdauer in Stunden:';
        for($i=0;$i<count($zeiten);$i++)
        {
        	echo '<input type="radio" name="atime" value="'.$zeiten[$i].'"';
        	if($zeiten[$i]==$_SESSION['sou_auction_v3'])echo ' checked="checked"';
        	echo '> '.$zeiten[$i];
        }
        echo '</td></tr>';
        
        //auktionsgeb�hr
        echo '<tr><td class="cell1">Die Auktionsgeb&uuml;hr betr&auml;gt 1% vom Startpreis/Sofortkaufpreis (richtet sich nach dem gr&ouml;&szlig;eren Wert),
              jedoch mindestens 10 Zastari.</td></tr>';
        //hiddenfeld f�r die item-id
        echo '<input type="hidden" name="itemid" value="'.$id.'">';
        //hiddenfeld f�r die haltbarkeit
        echo '<input type="hidden" name="itemdid" value="'.$did.'">';        

        //hiddenfeld f�r die seitenanzeige
        echo '<input type="hidden" name="mp" value="2">';

        //start/abbrechen-button
        echo '<tr align="center"><td>
              <input type="submit" name="startauktion" value="Auktion starten">
              <input type="submit" name="cancelauktion" value="abbrechen">
              </td></tr>';
        echo '</table>';
        //die('</table></div></body></html>');
      }//ende if has item
      else
      {
        echo '</table>';
      	//die('</table></div></body></html>');
      }
    }
    else //ansonsten Auktionsseite anzeigen
    {
      echo '<table width="100%">';
      //rucksackinhalt darstellen
      //ausr�stung auslesen und tooltip generieren
      //restlichen items laden und tooltips generieren
      $anzbp=0;
      //schauen wieviel items man bei hat
      $itemsperpage=919;
      $result = mysql_query("SELECT id FROM sou_ship_module WHERE location=1 AND user_id='$player_user_id'", $soudb);
      $itemmenge = mysql_num_rows($result);

      $sp=$_REQUEST["sp"];
      if($sp<=1)$sp=1;
      if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
      $sp=(int)$sp;
      $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
      $showmenge=$itemsperpage;

      if($itemmenge==0){
        $showstart=0;
        $showmenge=0;
        echo '<b>Du hast keine Module im Modulkomplex.</b>';
      }

      $result = mysql_query("SELECT * FROM sou_ship_module WHERE location=1 AND user_id='$player_user_id' ORDER BY name LIMIT $showstart,$showmenge", $soudb);
      //echo "SELECT * FROM sou_ship_module WHERE location=1 AND user_id='$player_user_id' ORDER BY name LIMIT $showstart,$showmenge";
      while($row = mysql_fetch_array($result)){
        $ttext=make_modul_info($row);
        $BPText[$anzbp]=make_modul_name_js($row).'&'.$ttext;
        $bp[$anzbp][0]=make_modul_name($row);
        $bp[$anzbp][1]=$row["id"];
        //counter erh�hen
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
        echo '<td class="'.$bg.'" title="'.$BPText[$i].'"><b>'.$bp[$i][0].' <a href="sou_main.php?action=auctionpage&mp=2&id='.$bp[$i][1].'" title="Auktion starten">[A]</a></td>';
        echo '</tr>';
      }
      if($anzbp>0)
      {
      //evtl. untere leiste zum bl�ttern anzeigen
      echo '<tr align="center"><td colspan="2">';
      echo '<table><tr>';
      //zur�ck
      if($sp>1)echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=2&sp='.($sp-1).'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=2&sp='.($sp+1).'">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
      }

      echo'</table>';
    }//ende if id
  }
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  //auktionen f�r die man bietet
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  elseif($_REQUEST["mp"]==3)
  {
    echo'
    <tr>
    <td align="center">
      <table border="0" cellpadding="0" cellspacing="2"><tr align="center">
      <td><a href="sou_main.php?action=auctionpage&mp=1"><div class="b1">Bieten</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=3"><div class="b1"><b>Meine Gebote</b></div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=4"><div class="b1">Meine Auktionen</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=2"><div class="b1">Auktion erstellen</div></a></td>
      </tr></table>
    </td>
	</tr>';
	
    //ausgabe der eigenen gebote
    $itemsperpage=918;
    //schauen wieviel es gibt
    $sql="SELECT * FROM sou_ship_module WHERE time > UNIX_TIMESTAMP() AND bidder='$player_user_id' ORDER BY time, price ASC";
    $result = mysql_query($sql, $soudb);
    $itemmenge = mysql_num_rows($result);

    //daten mit itemmengenbeschr�nkung aus der db holen
    $sp=$_REQUEST["sp"];
    if($sp<=1)$sp=1;
    if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
    $sp=(int)$sp;
    if($sp<=1)$sp=1;
    $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
    $showmenge=$itemsperpage;
    $sql.=" LIMIT $showstart,$showmenge";
    $result = mysql_query($sql, $soudb);

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
          elseif($time>(3600*96))$azeit[]='> 4 Tage';
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
        
        //erstelle den string f�r die mouse-over-box

        $ttext=make_modul_info($row);
        $BPText[$anzbp]=make_modul_name_js($row).'&'.$ttext;
        $bp[$anzbp][0]=make_modul_name($row);
        $bp[$anzbp][1]=$row["id"];
        $bp[$anzbp][2]=$row["auctioncurrency"];
        //counter erh�hen
        $anzbp++;
      }

      echo '<tr><td>';
      echo '<table width="100%">';
      //kopfzeile ausgeben
      echo '<tr align="center">
      <td class="cell1"><b>Gegenstand</b></td>
      <td class="cell1" width="50"><b>Zeit</b></td>
      <td class="cell1" width="100"><b>Gebot</b></td>
      </tr>';

      for($i=0;$i<$anzbp;$i++)
      {
        //preis designen
        echo '<tr>
        <td class="cell" title="'.$BPText[$i].'"><b>'.$bp[$i][0].'</b></td>
        <td class="cell" align="center">'.$azeit[$i].'</td>';

        if($bp[$i][2]==0)$showcurrency='<img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
        elseif($bp[$i][2]==1)$showcurrency='<img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
        elseif($bp[$i][2]==2)$showcurrency='<img src="'.$gpfad.'a27.gif" alt="Dunkle Materie" title="Dunkle Materie">';
		    elseif($bp[$i][2]==3)$showcurrency='<img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin">';
        
        echo '<td class="cell" align="center">'.number_format($price[$i], 0,"",".").' '.$showcurrency.'</td>';
        echo '</tr>';
      }
      echo '</table></td></tr>';
      //evtl. untere leiste zum bl�ttern anzeigen
      echo '<tr align="center"><td colspan="3">';
      echo '<table><tr>';
      //zur�ck
      if($sp>1)echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=3&sp='.($sp-1).'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=3&sp='.($sp+1).'">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
      //echo '</table>';
    }
    else echo '<tr><td align="center" class="text1"><b>Es gibt keine aktiven Gebote.</b></td></tr>';
    echo '</table>';
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
      <table border="0" cellpadding="0" cellspacing="2"><tr align="center">
      <td><a href="sou_main.php?action=auctionpage&mp=1"><div class="b1">Bieten</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=3"><div class="b1">Meine Gebote</div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=4"><div class="b1"><b>Meine Auktionen</b></div></a></td>
      <td><a href="sou_main.php?action=auctionpage&mp=2"><div class="b1">Auktion erstellen</div></a></td>
      </tr></table>
    </td>
	</tr>';

    //ausgabe der eigenen auktionen
    $itemsperpage=918;
    //schauen wieviel es gibt
    $sql="SELECT * FROM sou_ship_module WHERE time > UNIX_TIMESTAMP() AND user_id='$player_user_id' AND location=2 ORDER BY time, price ASC";
    $result = mysql_query($sql, $soudb);
    $itemmenge = mysql_num_rows($result);

    //daten mit itemmengenbeschr�nkung aus der db holen
    $sp=$_REQUEST["sp"];
    if($sp<=1)$sp=1;
    if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
    $sp=(int)$sp;
    if($sp<=1)$sp=1;
    $showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
    $showmenge=$itemsperpage;
    $sql.=" LIMIT $showstart,$showmenge";
    $result = mysql_query($sql, $soudb);

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
          elseif($time>(3600*96))$azeit[]='> 4 Tage';
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
        
        //erstelle den string f�r die mouse-over-box

        $ttext=make_modul_info($row);
        $BPText[$anzbp]=make_modul_name_js($row).'&'.$ttext;
        $bp[$anzbp][0]=make_modul_name($row);
        $bp[$anzbp][1]=$row["id"];
        //counter erh�hen
        $anzbp++;
      }

      echo '<table width="100%">';
      //kopfzeile ausgeben
      echo '<tr align="center">
      <td class="cell1"><b>Gegenstand</b></td>
      <td class="cell1" width="50"><b>Zeit</b></td>

      <td class="cell1" width="100"><b>akt. Gebot</b></td>
      <td class="cell1" width="100"><b>Sofortkauf</b></td>
      </tr>';

      for($i=0;$i<$anzbp;$i++)
      {
        //preis designen
        if($dprice[$i]==0)$sofortkauf='-';
        else $sofortkauf=number_format($dprice[$i], 0,"",".");
        echo '<tr align="center">
        <td class="cell" align="left" title="'.$BPText[$i].'"><b>'.$bp[$i][0].'</b></td>
        <td class="cell">'.$azeit[$i].'</td>';
        if($bidder[$i]>0)
          echo '<td class="cell">'.number_format($price[$i], 0,"",".").'</td>';
        else
          echo '<td class="cell">kein Gebot</td>';
        echo '<td class="cell">'.$sofortkauf.'</td></tr>';
      }
      //evtl. untere leiste zum bl�ttern anzeigen
      echo '<tr align="center"><td colspan="4">';
      echo '<table><tr>';
      //zur�ck
      if($sp>1)echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=4&sp='.($sp-1).'">zur&uuml;ck</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      //itemzahl
      $bis=$showstart+$showmenge;
      if($bis>$itemmenge)$bis=$itemmenge;
      echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
      //weiter
      if(($bis<$itemmenge))
      echo '<td width="100" align="center"><a href="sou_main.php?action=auctionpage&mp=4&sp='.($sp+1).'">weiter</a></td>';
      else echo '<td width="100" align="center">&nbsp;</td>';
      echo '</tr></table></td></tr>';
    }
    else echo '<tr><td align="center" class="text1"><b>Es gibt keine aktiven Aktionen.</b></td></tr>';
    echo '</table>';
  }

  //echo '</div>';
  
  rahmen1_unten();
  echo '<br>';
  rahmen0_unten();
  
  //echo '</form>';
  //die('</body></html>');
}


	
	
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

  	
	echo '</form>';
  }
  else echo '<br>Deine Fraktion verf&uuml;gt noch &uuml;ber kein Auktionszentrum. <a href="sou_main.php?action=systempage" class="btn">System</a><br><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>