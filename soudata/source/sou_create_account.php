<?php
$fehlermsg='';
//spielernamen setzen
if($player_name=='' AND $_REQUEST["newspielername"])
{
  $spielername=$_REQUEST["newspielername"];
  if(strlen($_REQUEST["newspielername"])>2 AND strlen($_REQUEST["newspielername"])<=20)
  {
    //testen ob er nur aus buchstaben besteht
  	if(!preg_match ("/^[[:alpha:]0-9öäüÖÜÄ_=-]*$/i", $_REQUEST["newspielername"]))$fehlermsg.='Der Spielername ist ungültig.<br>';
  	else 
  	{
  	  //schauen ob er schon vergeben ist
      $db_daten=mysql_query("SELECT user_id FROM sou_user_data WHERE spielername='$_REQUEST[newspielername]'",$soudb);
      $vorhanden = mysql_num_rows($db_daten);
      if ($vorhanden>0)$fehlermsg.='Dieser Spielername ist bereits vergeben.<br>';
	  else
	  {
	  	//account anlegen
	  	mysql_query("INSERT INTO sou_user_data (ext_user_id, spielername, sn_ext1) VALUES ($ums_user_id, '$_REQUEST[newspielername]', '$sv_server_tag')",$soudb);
        //user_id in der session hinterlegen
	  	$_SESSION["sou_user_id"]=mysql_insert_id();
        
        //account mit dem de-account verknüpfen
        mysql_query("UPDATE de_user_data SET sou_user_id='$_SESSION[sou_user_id]' WHERE user_id = '$ums_user_id'",$db);
        header("Location: sou_main.php");
	  }
  	}
  }
  else $fehlermsg.='Der Spielername hat nicht die richtige Länge.<br>';
}

//schiffsname setzen
if($_SESSION["sou_shipname"]=='' AND $_REQUEST["newshipname"])
{
  $shipname=$_REQUEST["newshipname"];
  if(strlen($_REQUEST["newshipname"])>2 AND strlen($_REQUEST["newshipname"])<=20)
  {
    //testen ob er nur aus buchstaben besteht
  	if(!preg_match ("/^[[:alpha:]0-9öäüÖÜÄ_=-]*$/i", $_REQUEST["newshipname"]))$fehlermsg.='Der Name ist ungültig.<br>';
  	else 
  	{
  	  //schauen ob er schon vergeben ist
      $db_daten=mysql_query("SELECT user_id FROM sou_user_data WHERE shipname='$_REQUEST[newshipname]'",$soudb);
      $vorhanden = mysql_num_rows($db_daten);
      if ($vorhanden>0)$fehlermsg.='Dieser Name ist bereits vergeben.<br>';
	  else
	  {
	  	//schiffname hinterlegen
        mysql_query("UPDATE sou_user_data SET shipname='$shipname' WHERE user_id = '".$_SESSION["sou_user_id"]."'",$soudb);
        $_SESSION["sou_shipname"]=$shipname;
        header("Location: sou_main.php");
	  }
  	}
  }
  else $fehlermsg.='Der Name hat nicht die richtige Länge.<br>';
}

//fraktion setzen
if($_SESSION["sou_fraction"]==0 AND $_REQUEST["fraction"])
{
  //fraktion überprüfen
  switch($_REQUEST["fraction"]){
    case 'Fraktion 1':
      $gewfraktion=1;
    break;
    case 'Fraktion 2':
      $gewfraktion=2;
    break;
    case 'Fraktion 3':
      $gewfraktion=3;
    break;
    case 'Fraktion 4':
      $gewfraktion=4;
    break;
    case 'Fraktion 5':
      $gewfraktion=5;
    break;
    case 'Fraktion 6':
      $gewfraktion=6;
    break;                              
  	default:
      //zufallsauswahl
      //in dem fall die fraktion nehmen, die am wenigsten spieler hat
      $gewfraktion=1;
      $maxfractionanz=999999999;
      $zeitgrenze=time()-24*3600*14;
      for($i=1;$i<=6;$i++)
      {
        $db_daten=mysql_query("SELECT count(*) AS counter FROM `sou_user_data` WHERE fraction='$i' AND lastclick>'$zeitgrenze'",$soudb);
        $row = mysql_fetch_array($db_daten);
        if($maxfractionanz>$row["counter"])
        {
      	  $gewfraktion=$i;
      	  $maxfractionanz=$row["counter"];
        }
      }
    break;
  }
  //gewählte fraktion und die startkoordinaten in die db schreiben in die db schreiben
  $player_x=$sv_sou_startposition[$gewfraktion-1][0];
  $player_y=$sv_sou_startposition[$gewfraktion-1][1];
  mysql_query("UPDATE sou_user_data SET fraction='$gewfraktion', x='$player_x' ,y='$player_y' WHERE user_id = '$sou_user_id'",$soudb);
  header("Location: sou_main.php");
}
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//auf spielernamen überprüfen
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
if($player_name=='')
{
  echo '<br>';  
  rahmen0_oben();
  rahmen2_oben();
  
  echo '<form action="sou_main.php" method="POST">';
  echo '<br><br><table width="600" border="0"> ';
  echo '<tr><td width="30%">Spielername vergeben:</td><td width="70%"><input type="text" name="newspielername" size="25" maxlength="20" value="'.$spielername.'"></td></tr>';
  echo '<tr><td colspan="2"><font color="#FF0000">'.$fehlermsg.'</font>
  Hier kannst du den Namen des Kapit&auml;ns vergeben, den Du auf die Reise in unbekannte Galaxien schickst. Beachte bitte, 
  dass der Name nicht mehr ge&auml;ndert werden kann. F&uuml;r den Namen gelten die folgende Regeln:<br>
  - erlaubt sind nur Buchstaben<br>
  - erlaubt sind 3-20 Buchstaben<br>
  - er muß zu einem Weltraumspiel passen<br>
  - er darf nicht durch ein Copyright geschützt sein<br>
  - er darf nicht rassistisch/diskriminierend/pornografisch sein<br>
  - er darf kein Stafflername sein, außer man ist selbst der entsprechende Staffler<br><br>
  Eine Chatnutzung ist erst nach Vergabe des Spielernamens möglich.
  </td></tr>';
  echo '<tr align="center"><td colspan="2"><input type="Submit" name="setspielername" value="weiter"></td></tr>';
  
  echo '</table>';
  
  echo '<form>';

  rahmen2_unten();
  rahmen0_unten();
  
  die('</body></html>');
}
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//auf raumschiffnamen überprüfen
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
if($_SESSION["sou_shipname"]=='')
{
  echo '<br>';  
  rahmen0_oben();
  rahmen2_oben();
  
  echo '<form action="sou_main.php" method="POST">';
  echo '<br><br><table width="600" border="0"> ';
  echo '<tr><td width="30%">Raumschiffname vergeben:</td><td width="70%"><input type="text" name="newshipname" size="25" maxlength="20" value="'.$shipname.'"></td></tr>';
  echo '<tr><td colspan="2"><font color="#FF0000">'.$fehlermsg.'</font>
  Hier kannst du den Namen des Raumschiffs vergeben, mit dem Du Dich den Herausforderungen stellst. Beachte bitte, 
  dass der Name nicht mehr geändert werden kann. Für den Namen gelten die folgende Regeln:<br>
  - erlaubt sind nur Buchstaben, Zahlen und folgende Sonderzeichen: _-=<br>
  - erlaubt sind 3-20 Buchstaben<br>
  - er muß zu einem Weltraumspiel passen<br>
  - er darf nicht durch ein Copyright geschützt sein<br>
  - er darf nicht rassistisch/diskriminierend/pornografisch sein<br>
  - er darf kein Stafflername sein, außer man ist selbst der entsprechende Staffler<br>
  </td></tr>';
  echo '<tr align="center"><td colspan="2"><input type="Submit" name="setsshipname" value="weiter"></td></tr>';
  
  echo '</table>';
  
  echo '<form>';

  rahmen2_unten();
  rahmen0_unten();
  
  die('</body></html>');
}
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//auf fraktion/squad überprüfen
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
if($_SESSION["sou_fraction"]==0)
{
  //überprüfen ob man evtl. schon ein schiff hat und daraus die fraktion verwenden
  if($_SESSION["ums_owner_id"]>0)
  {
    $db_daten=mysql_query("SELECT * FROM sou_user_data WHERE owner_id='".$_SESSION["ums_owner_id"]."' AND fraction>0 LIMIT 1",$soudb);
    $num = mysql_num_rows($db_daten);
    if($num==1)
    {    
      $row = mysql_fetch_array($db_daten);
      $gewfraktion=$row['fraction'];
      $squad=$row['squad'];
  
	  //gewählte fraktion und die startkoordinaten in die db schreiben in die db schreiben
  	  $player_x=$sv_sou_startposition[$gewfraktion-1][0];
  	  $player_y=$sv_sou_startposition[$gewfraktion-1][1];
  	  mysql_query("UPDATE sou_user_data SET fraction='$gewfraktion', x='$player_x' ,y='$player_y', squad='$squad' WHERE user_id = '$sou_user_id'",$soudb);
      $_SESSION["sou_fraction"]=$gewfraktion;
      $player_fraction=$gewfraktion;
    }
  }  

  if($_SESSION["sou_fraction"]==0)
  {
    echo '<br>';
    rahmen0_oben();
    echo '<br>';
    rahmen2_oben();

    echo '<form action="sou_main.php" method="POST">';
    echo '<br><br><table width="600" border="0"> ';
    echo '<tr><td width="30%">Fraktion wählen:</td>
        <td width="70%"><select name="fraction">';
            if ($fraction=='')$fraction='Zufallsauswahl';;
            echo '<option selected>'.$fraction.'</option>';
            echo'
            <option>Zufallsauswahl</option>
            <option>Fraktion 1</option>
            <option>Fraktion 2</option>
            <option>Fraktion 3</option>
            <option>Fraktion 4</option>
            <option>Fraktion 5</option>
            <option>Fraktion 6</option>
            </select>
        </td>
      </tr>';
    echo '<tr><td colspan="2"><font color="#FF0000">'.$fehlermsg.'</font>
    Hier kannst du Deine Fraktion ausw&auml;hlen, oder sie per Zufall zuweisen lassen. Die Fraktionen haben unterschiedliche Startpunkte und vertreten jeweils 
    ihre eigenen Interessen. M&ouml;chtest du mit Deinen Freunden spielen, dann sprich Dich bitte vorher mit ihnen ab, denn die Fraktion ist nachtr&auml;glich nicht 
    mehr wechselbar und gilt f&uuml;r alle Schiffe.
  
    </td></tr>';
    echo '<tr align="center"><td colspan="2"><input type="Submit" name="setfraction" value="weiter"></td></tr>';
  
    echo '</table>';
    echo '<form>';
  
    rahmen2_unten();
    echo '<br>';
    rahmen0_unten();
	
    die('</body></html>');
  }
}

//auf destroy überprüfen und auf standard zurücksetzen
if($player_destroy==1)
{
  //zuerst alle versicherten module in dem modulkomplex verschieben
  //mysql_query("UPDATE sou_ship_module SET insurance=0, location=1 WHERE user_id='$_SESSION[sou_user_id]' AND location=0 AND insurance=1",$soudb);
	
  //dann alle noch vorhandenen module entfernen
  mysql_query("DELETE FROM sou_ship_module WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
  
  //dann die startmodule in die db packen
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, givecenter) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Zentralmodul Standard', 1)",$soudb);
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, canmine) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Bergbaumodul Standard', 50)",$soudb);
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, canmine) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Bergbaumodul Standard', 50)",$soudb);
  //mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, hasspace) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Lagermodul Standard', 50)",$soudb);
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, givelife) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Lebenserhaltungsmodul Standard', 1)",$soudb);
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, givesubspace) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Kurzstreckenantrieb Standard', 1)",$soudb);
  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, canclone) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Klonmodul Standard', 100)",$soudb);
  
  mysql_query("UPDATE sou_user_data SET destroy=0, shipdiameter=10 WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
  
  //nachricht an den account, dass das schiff zerstört worden ist
  
  //flag für weiter bearbeitung setzen und durchmesser zurücksetzen
  $player_destroy=0;
  /*
  mysql_query("UPDATE sou_user_data SET destroy=0, shipdiameter=10, shipmaterial=0 WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
  //mysql_query("UPDATE sou_user_data SET destroy=0 WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
  
  //aktive forschung löschen
  mysql_query("UPDATE sou_user_data SET atimer2typ='0', atimer2flag='0', atimer2time='0' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
  $player_atimer2typ=0;
  $player_atimer2flag=0;
  $player_atimer2time=0;
  */  	  
}

?>