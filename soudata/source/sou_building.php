<?php
//ressourcenfiles einbinden
include "soudata/defs/buildings.inc.php";
include "soudata/defs/resources.inc.php";

//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  
  //die id des sonnensystems auslesen
  $row = mysql_fetch_array($db_daten);
  $owner_id=$row["id"];
  $owner_sysname=$row["sysname"];
  $owner_fraction=$row["fraction"];
  $maxgeblevel=$row["maxgeblevel"];
  $owner_underattack=$row["underattack"];
  $pirates=$row['pirates'];
  
  //test auf piraten
  if($pirates>0)
  {
    //stufe der piraten bestimmen
    // die entfernung zum zentrum berechnen
    $radius=sqrt(($player_x*$player_x)+($player_y*$player_y));
    if($radius>1250)
    {
      $pirateslevel=0;
    }
    else 
    {
      $pirateslevel=51-ceil($radius/50*2);
    }
    
    if($pirateslevel>0)
    {
      rahmen2_oben();
  	  echo 'Dieses Sonnensystem wird von Piraten belagert. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	  rahmen2_unten();
  	  echo '<br>';

	  echo '</div>';//center-div
	  die('</body></html>');
    }
  }
  
  //überprüfen ob das system zur eigenen fraktion gehört
  if($player_fraction!=$owner_fraction)
  {
  	rahmen2_oben();
  	echo 'Auf dieses Sonnensystem hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }

  //überprüfen ob das system umkämpft ist
  if($owner_underattack+3600*24*2>time())
  {
  	rahmen2_oben();
  	echo 'Dieses Sonnensystem ist umk&auml;mpft und es k&ouml;nnen keine Geb&auml;ude ausgebaut werden. Fr&uuml;hestm&ouml;glicher Endzeitpunkt der K&auml;mpfe: '.date("H:i:s d.m.Y", $owner_underattack+3600*24*2).'<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }

  //alle gebäude im system auslesen
  unset($b_has);
  $geb_durchschnitt=0;
  $db_daten=mysql_query("SELECT * FROM `sou_map_buildings` WHERE owner_id='$owner_id'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    $geb_durchschnitt+=$row["level"];
    $b_has[$row["bldg_id"]][0]=$row["level"];
    $b_has[$row["bldg_id"]][1][0]=$row["b1"];
    $b_has[$row["bldg_id"]][1][1]=$row["b2"];
    $b_has[$row["bldg_id"]][1][2]=$row["b3"];
    $b_has[$row["bldg_id"]][1][3]=$row["b4"];
    $b_has[$row["bldg_id"]][1][4]=$row["b5"];
    $b_has[$row["bldg_id"]][1][5]=$row["b6"];
    $b_has[$row["bldg_id"]][1][6]=$row["b7"];
    $b_has[$row["bldg_id"]][1][7]=$row["b8"];
    $b_has[$row["bldg_id"]][1][8]=$row["b9"];
    $b_has[$row["bldg_id"]][1][9]=$row["b10"];
  }
  $geb_durchschnitt=$geb_durchschnitt/count($b_defs);
  
  //alle gebäude-upgrade-module auslesen
  unset($um_has);
  $db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE location=0 AND canbldgupgrade>0 AND user_id='$player_user_id'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    $um_has[$row["canbldgupgrade"]][0]++;
  }
  
  //feststellen wieviele sonnensysteme man hat
  $db_daten=mysql_query("SELECT count(*) AS wert FROM `sou_map` WHERE fraction='$player_fraction'",$soudb);
  $row = mysql_fetch_array($db_daten);
  $has_starsystems=$row["wert"];
  
  
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //levelrohstoffeinzahlung für max 60/480 minuten
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  if($_REQUEST["do"]=='1')
  {
    //transaktionsbeginn
    if (setLock($_SESSION["sou_user_id"]))
    {
  	  //gebäude auslesen
  	  $bid=intval($_REQUEST["bid"]);
  	  //rohstoffart auselsen
  	  $rid=intval($_REQUEST["rid"]);
  	  //bergbaudauer je nach pa-status
	  if($ums_premium==1)$min=480;else $min=60;
  	  
  	  //überprüfen ob man freien laderaum hat
  	  $freehold=1;//get_sum_hold($_SESSION["sou_user_id"]);
  	  if($freehold>0)
  	  {
  	  	//überprüfen ob man ein mining-modul hat
  	  	$canmine=get_canmine($_SESSION["sou_user_id"]);
  	  	if($canmine>0)
  	  	{
  	  	  //überprüfen ob man genug credits hat
  	  	  $needcredits=0;
  	  	  if($has_credits>=$needcredits)
  	  	  {
			$i=$bid;
			$r=$rid;
			$a=$r+1;			  
			
			$db_daten=mysql_query("SELECT * FROM `sou_map_buildings` WHERE owner_id='$owner_id' AND bldg_id='$i'",$soudb);
			$num = mysql_num_rows($db_daten);
			if($num==1) //es gibt einen datensatz
			{
			  //feststellen wieviel schon vorhanden ist
			  $row = mysql_fetch_array($db_daten);
			  $hasbenres=$row["b$a"];
			  $level=$row["level"];
			}
			else //es gibt keinen datensatz
			{
			  //datensatz anlegen
			  mysql_query("INSERT INTO sou_map_buildings (owner_id, bldg_id) VALUES ('$owner_id', '$i')",$soudb);
			  $hasbenres=0;
			  $level=0;
			}
				
  			//feststellen wieviel er noch braucht
    		$need=explode(";",$b_defs[$i][1][$level]);
    		$einzelneed=array(0,0);
    		for($n=0;$n<count($need);$n++)
    		{
      		  if($n==$r)$einzelneed=explode("x",$need[$n]);
            }
  			$benres=$einzelneed[0]-$hasbenres;
  			if($benres<0)$benres=0;
		
 			if($benres>0){	
				//dem gebäude die rohstoffe gutschreiben
				//berechnen wieviel rohstoffe man pro minute bekommen kann
				$getres=round($canmine/$r_def[$rid][1]*(1+(get_skill($rid)/500000)));
				//bao-nada-mine-bonus
				$baonada_different=get_baonadaskala_different();
				if($baonada_different>10)$getres=$getres*$baonada_different/10;

				//überprüfen wie oft er fliegen muß
				$anz_fluege=ceil($benres/$getres);
				if($anz_fluege>$min)$anz_fluege=$min;
				
				//gesamtertrag
				$getres=$getres*$anz_fluege;
				if($getres>$benres)$getres=$benres;
			  
				//rohstoffe gutschreiben
				mysql_query("UPDATE `sou_map_buildings` SET b$a=b$a+'$getres' WHERE owner_id='$owner_id' AND bldg_id='$i'",$soudb);


				//credits abziehen
				if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Gebäude-Komfortfunktion');

				//dieses als spende notieren
				$donate=$getres*$r_def[$r][2];
				if($donate<0)$donate=0;
				mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				//fraktionsansehen und systemwert vergrößern
				$feldname='prestige'.$player_fraction;
				mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate', worth=worth+'$donate' WHERE id='$owner_id'",$soudb);

				//einen counter setzen, damit sonst nichts mehr zu machen geht
				//$time=time()+($anz_fluege*60)-($anz_fluege*60)/100*get_baonadaskala_different();
				$time=time()+($anz_fluege*60);

				//skill verbessern
				change_skill($rid, $anz_fluege);

				mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
				//seite neu laden
				header("Location: sou_main.php");
 			}
 			else $msg='<font color="#FF0000">Dieser Rohstoff wird nicht ben&ouml;tigt.</font>';
  	  	  }
  	  	  else $msg='<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font>';
  	    }
  	    else $msg='<font color="#FF0000">Es ist kein Bergbaumodul vorhanden.</font>';
  	  }
	  else $msg='<font color="#FF0000">Es ist kein freier Frachtraum vorhanden.</font>';

      //lock wieder entfernen
      $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
      if ($erg)
      {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
      }
      else
      {
        print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
      }
    }//lock ende
    else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
  }
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //rohstoffeinzahlung für x minuten
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  if($_REQUEST["do"]=='2'){
    //transaktionsbeginn
    if (setLock($_SESSION["sou_user_id"])){
  	  //gebäude auslesen
  	  $bid=intval($_REQUEST["bid"]);
  	  //rohstoffart auselsen
  	  $rid=intval($_REQUEST["rid"]);
  	  //bergbaudauer
  	  $min=intval($_REQUEST["min"]);
  	  if($min<1 OR $min>480)$min=1;
  	  
  	  //überprüfen ob man freien laderaum hat
  	  $freehold=1;//get_sum_hold($_SESSION["sou_user_id"]);
  	  if($freehold>0){
  	  	//überprüfen ob man ein mining-modul hat
  	  	$canmine=get_canmine($_SESSION["sou_user_id"]);
  	  	if($canmine>0){
  	  	  //überprüfen ob man genug credits hat
  	  	  $needcredits=0;
  	  	  if($min>60)$needcredits=10;
  	  	  if($ums_premium==1)$needcredits=0;
          $has_credits=has_credits($ums_user_id);
  	  	  if($has_credits>=$needcredits){
  	  	    //mitloggen wie oft die funktion verwendet wurde
  	  	    //mysql_query("UPDATE `sou_system` SET statbldg$min=statbldg$min+1",$soudb);
  	  	    
          	//berechnen wieviel rohstoffe man pro minute bekommen kann
          	$getres=round($canmine/$r_def[$rid][1]*(1+(get_skill($rid)/500000)));
          	//bao-nada-mine-bonus
			//echo '<br>1. '.$getres;
          	$baonada_different=get_baonadaskala_different();
          	if($baonada_different>10)$getres=$getres*$baonada_different/10;
          	//echo '<br>2. '.$getres;
          	//gesamtertrag berechnen
          	$getres=$getres*$min;
          	
          	//dem gebäude die rohstoffe gutschreiben
      	    //gebäudedatensatz auslesen
      	    $i=$bid;
      	    $r=$rid;
      	    $a=$r+1;
      	    $db_daten=mysql_query("SELECT * FROM `sou_map_buildings` WHERE owner_id='$owner_id' AND bldg_id='$i'",$soudb);
      	    $num = mysql_num_rows($db_daten);
      	    if($num==1) //es gibt einen datensatz
  			{
  			  //feststellen wieviel schon vorhande ist
  			  $row = mysql_fetch_array($db_daten);
  			  $hasbenres=$row["b$a"];
  			  $level=$row["level"];
  				
  			  //rohstoffe gutschreiben
			  mysql_query("UPDATE `sou_map_buildings` SET b$a=b$a+'$getres' WHERE owner_id='$owner_id' AND bldg_id='$i'",$soudb);
  			}
  			else //es gibt keinen datensatz
  			{
  		 	  //datensatz anlegen
  		 	  mysql_query("INSERT INTO sou_map_buildings (owner_id, bldg_id) VALUES ('$owner_id', '$i')",$soudb);
   			  //rohstoffe gutschreiben
			  mysql_query("UPDATE `sou_map_buildings` SET b$a=b$a+'$getres' WHERE owner_id='$owner_id' AND bldg_id='$i'",$soudb);
  			  $hasbenres=0;
  			  $level=0;
  			}
			
  			//feststellen wie viel er noch braucht
    		$need=explode(";",$b_defs[$i][1][$level]);
    		$einzelneed=array(0,0);
    		for($n=0;$n<count($need);$n++)
    		{
      		  if($n==$r)$einzelneed=explode("x",$need[$n]);
            }
  			$benres=$einzelneed[0]-$hasbenres;
  			if($benres<0)$benres=0;
		
 			if($benres>0){
   			  
			  //überprüfen wie oft er fliegen muß, er fliegt jetzt immer solange, wie angegeben
  			  //$anz_fluege=ceil($benres/($getres/$min));
  			  //if($anz_fluege>$min)$anz_fluege=$min;
				$anz_fluege=$min;
          	
  			  //credits abziehen
  			  if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Gebäude-Komfortfunktion');
  			
 			  //dieses als spende notieren
			  $donate=($getres/$min)*$r_def[$r][2]*$anz_fluege;
			  if($donate<0)$donate=0;
			  mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
			  //fraktionsansehen und systemwert vergrößern
			  $feldname='prestige'.$player_fraction;
			  mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate', worth=worth+'$donate' WHERE id='$owner_id'",$soudb);
  			
          	  //einen counter setzen, damit sonst nichts mehr zu machen geht
          	  //$time=time()+($anz_fluege*60)-($anz_fluege*60)/100*get_baonadaskala_different();
          	  $time=time()+($anz_fluege*60);
          	 
          	  //skill verbessern
          	  change_skill($rid, $anz_fluege);
          	
          	  mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
			  //seite neu laden
          	  header("Location: sou_main.php");
 			}
 			else $msg='<font color="#FF0000">Dieser Rohstoff wird nicht ben&ouml;tigt.</font>';
  	  	  }
  	  	  else $msg='<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font>';
  	    }
  	    else $msg='<font color="#FF0000">Es ist kein Bergbaumodul vorhanden.</font>';
  	  }
	  else $msg='<font color="#FF0000">Es ist kein freier Frachtraum vorhanden.</font>';

      //lock wieder entfernen
      $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
      if ($erg)
      {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
      }
      else
      {
        print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
      }
    }//lock ende
    else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
  }

  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  //gebäudeupgrademodul verwenden
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  /*
  if($_REQUEST["do"]=='3'){
    //transaktionsbeginn
    if (setLock($_SESSION["sou_user_id"])){
  	  //gebäude auslesen
  	  //id
  	  $bid=intval($_REQUEST["bid"]-1);
  	  //level
	  $blevel=$b_has[$bid][0];  	  
  	  
	  //überprüfen ob man das passende modul auch hat
  	  if($um_has[$blevel]>0)
  	  {
  	    //spendenwert berechnen
  	    $donate=0;
    	$need=explode(";",$b_defs[$bid][1][$blevel]);
    	for($n=0;$n<count($need);$n++)
    	{
      	  $einzelneed=explode("x",$need[$n]);
      	  if($b_has[$bid][1][$n]>$einzelneed[0])$b_has[$bid][1][$n]=$einzelneed[0];
      	  //echo $einzelneed[0].'<br>';
      	  //echo $b_has[$bid][1][$n].'<br>';
      	  //echo (($einzelneed[0]-$b_has[$bid][1][$n])*$r_def[$einzelneed[1]-1][2]).'<br><br>';
      	  $donate+=($einzelneed[0]-$b_has[$bid][1][$n])*$r_def[$einzelneed[1]-1][2];
        }
        
        //die spende gutschreiben
        if($donate<0)$donate=0;
		mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
		//fraktionsansehen und systemwert vergrößern
		$feldname='prestige'.$player_fraction;
		mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate', worth=worth+'$donate' WHERE id='$owner_id'",$soudb);
        
		//das modul entfernen
		mysql_query("DELETE FROM `sou_ship_module` WHERE location=0 AND user_id='$player_user_id' AND canbldgupgrade='$blevel' LIMIT 1",$soudb);
		
		//das gebäude aufwerten
        mysql_query("UPDATE sou_map_buildings SET b1=99999999999, b2=9999999999 WHERE owner_id='$owner_id' AND bldg_id='$bid' AND level='$blevel'", $soudb);
        
        //seite neu laden
        header("Location: sou_main.php?action=buildingpage");
  	  }
  	  else $msg='<font color="#FF0000">Es ist kein passendes Modul vorhanden.</font>';

      //lock wieder entfernen
      $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
      if ($erg)
      {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
      }
      else
      {
        print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
      }
    }//lock ende
    else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
  }  
  */
  //formularstart
  //echo '<form action="sou_main.php" method="POST" name="f">';
  //echo '<input type="hidden" name="action" value="buildingpage">';
  //echo '<input type="hidden" name="do" value="1">';
  
  //messages anzeigen
  if($msg!='')
  {
    rahmen2_oben();
  	echo $msg;
  	rahmen2_unten();
  	echo '<br>';
  }
  
  //gebäude anzeigen
  $ttid=0;
  $spaltenflag=0;
  $output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
  $c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  //$output.='<tr align="center" class="'.$bg.'"><td width="100px"><b>Geb&auml;ude</b></td><td width="50px"><b>Stufe</b></td><td><b>Fehlende Voraussetzungen</b></td><td width="100px"><b>Geb&auml;ude</b></td><td width="50px"><b>Stufe</b></td><td><b>Voraussetzungen</b></td></tr>';
  for($i=0;$i<count($b_defs);$i++)
  {
    $hasall=1;
    $hasallres=1;
  	//hintergrund bestimmen
    

    if($spaltenflag==0)
    {
      //if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
      $bg='cell';
      $output.='<tr class="'.$bg.'" align="center"><td colspan="2"><b>'.$b_defs[$i][0].' ('.intval($b_has[$i][0]).')</b></td>
      <td colspan="2"><b>'.$b_defs[$i+1][0].' ('.intval($b_has[$i+1][0]).')</b></td></tr>';
      $bg='cell1';
      $output.='<tr class="'.$bg.'" align="left">';
   	}
    //level
    $level=$b_has[$i][0];
    if($level=='')$level=0;
    //voraussetzungen - anfang
    $benhas='';$benhasnot='';
    
    
    //freigabe vom vorsitzendem
    /*
    if($level>=$maxgeblevel AND $maxgeblevel>0)
    {
      $font1='<font color=#FF0000>';$font2='</font>';
      $benhasnot.=$font1.'Maximal erlaubte Stufe durch den Fraktionsvorsitzenden: '.$maxgeblevel.$font2.'<br>';
      
      $hasall=0;
    }
    */

    //konstruktionszentrum
    if($i>0){
      //textfarbe bestimmen, rot wenn das konstruktionszentraum noch nicht fertig ist
      $font1='<font color=#00FF00>';$font2='</font>';
      if($b_has[0][0]<$level+1){
        $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
        $benhasnot.=$font1.$b_defs[0][0].' (Geb&auml;ude) Stufe '.($level+1).$font2.'<br>';
      }
      else $benhas.=$font1.$b_defs[0][0].' (Geb&auml;ude) Stufe '.($level+1).$font2.'<br>';
    
    }
    //ab level 10 benötigt man x gebäude in der gesamten fraktion
    //ab level 10 benötigt man x sonnensysteme
    if($level>=10){
      /////////////////////////////////////////////////////
      /////////////////////////////////////////////////////
      //gebäude
      /////////////////////////////////////////////////////
      /////////////////////////////////////////////////////
      
      //$needgebs=($level-9)*2+1;//+1 für das gebäude im system selbst
      $needgebs=ceil(($level-9)*($level-9)/4)+1;//+1 für das gebäude im system selbst

  	  $db_datenx=mysql_query("SELECT COUNT(sou_map_buildings.level) AS wert FROM `sou_map_buildings` 
  		LEFT JOIN sou_map ON(sou_map.id = sou_map_buildings.owner_id) WHERE sou_map.fraction='$player_fraction' 
  		AND sou_map_buildings.level>='$level' AND sou_map_buildings.bldg_id='$i'",$soudb);
  
  	  $rowx = mysql_fetch_array($db_datenx);
 	  $font1='<font color=#00FF00>';$font2='</font>';
      if($needgebs>$rowx["wert"])
      {
        $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
        $benhasnot.=$font1.$b_defs[$i][0].' Stufe >= '.$level.' - Fraktion: '.$rowx["wert"].'/'.$needgebs.$font2.'<br>';
      }      
      else $benhas.=$font1.$b_defs[$i][0].' Stufe >= '.$level.' - Fraktion: '.$rowx["wert"].'/'.$needgebs.$font2.'<br>';
      
      /////////////////////////////////////////////////////
      /////////////////////////////////////////////////////
      //sonnensysteme, gilt nur für konstruktionszentrum
      /////////////////////////////////////////////////////
      /////////////////////////////////////////////////////
      if($i==0)
      {
        //liste der voraussetzungen
        $need_starsystems_list = array (3,25,100,200,300,400,500,600,700,800,900,1000,1250,1500,1750,2000,2250,2500,2750,3000,3500,4000,4500,5000,5500,6000,6500,7000,8000,9000,10000,11000,12000,13000,14000,15000,20000,25000,30000,35000,40000);
            
        $need_starsystems=$need_starsystems_list[$level-10];

 	    $font1='<font color=#00FF00>';$font2='</font>';
        if($need_starsystems>$has_starsystems)
        {
          $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
          $benhasnot.=$font1.'Fraktionssysteme: '.$has_starsystems.'/'.$need_starsystems.$font2.'<br>';
        }      
        else $benhas.=$font1.'Fraktionssysteme: '.$has_starsystems.'/'.$need_starsystems.$font2.'<br>';
      }
    }
    
    //koloniedurchschnitt, gilt nur für konstruktionszentrum
    if($i==0)
    {
      $need_gebaudedurchschnitt=$b_has[0][0]-3;
      if($need_gebaudedurchschnitt<0)$need_gebaudedurchschnitt=0;
 	  $font1='<font color=#00FF00>';$font2='</font>';
      if($need_gebaudedurchschnitt>$geb_durchschnitt)
      {
          $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
          $benhasnot.=$font1.'Koloniegeb&auml;udedurchschnitt: '.number_format($geb_durchschnitt, 2,",",".").'/'.number_format($need_gebaudedurchschnitt, 0,",",".").$font2.'<br>';
      }      
      else $benhas.=$font1.'Koloniegeb&auml;udedurchschnitt: '.number_format($geb_durchschnitt, 2,",",".").'/'.number_format($need_gebaudedurchschnitt, 0,",",".").$font2.'<br>';
    }

      
    //gebäude
    if($b_defs[$i][2][$level]!='')
    {
      $need=explode(";",$b_defs[$i][2][$level]);
      for($n=0;$n<count($need);$n++)
      {
        $einzelneed=explode("x",$need[$n]);
        
        $geb_level=get_bldg_level($owner_id, $einzelneed[0]);
        //textfarbe bestimmen, rot wenn das konstruktionszentraum noch nicht fertig ist
        $font1='<font color=#00FF00>';$font2='</font>';
        if($einzelneed[1]>$geb_level)
        {
          $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
          $benhasnot.=$font1.$b_defs[$einzelneed[0]][0].' (Geb&auml;ude) Stufe '.($einzelneed[1]).$font2.'<br>';
        }
    	else $benhas.=$font1.$b_defs[$einzelneed[0]][0].' (Geb&auml;ude) Stufe '.($einzelneed[1]).$font2.'<br>';
      }
    }

    //forschung
    if($b_defs[$i][3][$level]!='')
    {
      $need=explode(";",$b_defs[$i][3][$level]);
  	  $feldname='f'.$player_fraction.'lvl';
      
      for($n=0;$n<count($need);$n++)
      {
      	$tid=$need[$n];
        $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id='$tid'",$soudb);
  	    $row = mysql_fetch_array($db_daten);

        //textfarbe bestimmen, rot wenn das konstruktionszentraum noch nicht fertig ist
        $font1='<font color=#00FF00>';$font2='</font>';
        if($row[$feldname]<1)
        {
          $font1='<font color=#FF0000>';$font2='</font>';$hasall=0;
          $benhasnot.=$font1.$row["tech_name"].' (Forschung)'.$font2.'<br>';
        }
    	else $benhas.=$font1.$row["tech_name"].' (Forschung)'.$font2.'<br>';
      }
    }
    
    
    //rohstoffe
	unset($res_abzug);
    $need=explode(";",$b_defs[$i][1][$level]);
    for($n=0;$n<count($need);$n++){
      $einzelneed=explode("x",$need[$n]);
	  
	  //die aktuellen kosten für den späteren Abzug speichern
	  $res_abzug[$n]=$einzelneed[0];
	  
      //fix für die komfortfunktion, falls zuviele rohstoffe vorhanden sind
      //if($b_has[$i][1][$n]>$einzelneed[0])$b_has[$i][1][$n]=$einzelneed[0];
      
      //eingabefeld für rohstoffe, falls man nicht am max ist
      if($b_has[$i][1][$n]<$einzelneed[0]){
        $benhasnot.='
        <a id="ttid'.$ttid.'" href="sou_main.php?action=buildingpage&do=2&bid='.$i.'&rid='.$n.'&min=15" title="Rohstoffeinlagerung&15 Fl&uuml;ge">
        <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
        $ttid++;
        $benhasnot.='
        <a style="margin-left: 10px;" id="ttid'.$ttid.'" href="sou_main.php?action=buildingpage&do=2&bid='.$i.'&rid='.$n.'&min=60" title="Rohstoffeinlagerung&60 Fl&uuml;ge">
        <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
        $ttid++;
        $benhasnot.='
        <a style="margin-left: 10px;" id="ttid'.$ttid.'" href="sou_main.php?action=buildingpage&do=1&bid='.$i.'&rid='.$n.'" title="Stufenrohstoffeinlagerung&Es wird maximal so oft geflogen bis alle ben&ouml;tigten Rohstoffe vorhanden sind.<br>Ohne Premiumaccount maximal 60 Fl&uuml;ge.<br>Mit Premiumaccount maximal 480 Fl&uuml;ge.">
        <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
        $ttid++;
		//abfrage für non-pa accounts
		if($ums_premium!=1){
			$jssecure='onclick="return confirm(\'Diese Aktion kostet Credits. Durchf&uuml;hren?\')"';
		}else{
			$jssecure='';
		}
		
        $benhasnot.='
        <a '.$jssecure.' style="margin-left: 20px;" id="ttid'.$ttid.'" href="sou_main.php?action=buildingpage&do=2&bid='.$i.'&rid='.$n.'&min=480" title="Power Rohstoffeinlagerung&480 Fl&uuml;ge<br>Preis: 10 Credits (F&uuml;r Premium-Account-Nutzer kostenlos)">
        <img border="0" style="vertical-align: middle;" src="'.$gpfad.'abutton2.gif"  width="16px" height="16px"></a>&nbsp;';
        $ttid++;		
      }            
      //farbe bestimmen
      $bcolor='#00FF00';
	  if($b_has[$i][1][$n]<$einzelneed[0]){$bcolor='yellow';$hasall=0;$hasallres=0;}
	  if($b_has[$i][1][$n]<=$einzelneed[0]*0.66)$bcolor='orange';
	  if($b_has[$i][1][$n]<=$einzelneed[0]*0.33)$bcolor='red';
	  if($b_has[$i][1][$n]>=$einzelneed[0]){
        $benhas.='<font color='.$bcolor.'>'.number_format($b_has[$i][1][$n], 0,",",".").' / '.
        number_format($einzelneed[0], 0,",",".").'</font>  m&sup3; '.$r_def[$einzelneed[1]-1][0].'<br>';
	  }else{
        $benhasnot.='<font color='.$bcolor.'>'.number_format($b_has[$i][1][$n], 0,",",".").' / '.
        number_format($einzelneed[0], 0,",",".").'</font>  m&sup3; '.$r_def[$einzelneed[1]-1][0].'<br>';
      }
    }
	//wenn der ausbaubutton gedrückt wurde das gebäude direkt ausbauen
	if($hasall==1 AND $_REQUEST["do"]=="build" AND $_REQUEST["bid"]==($i+1))
	{
	  //datensatz reservieren
	  $time=time()+2;
	  $result = mysql_query("UPDATE sou_map_buildings SET build=1 WHERE owner_id='$owner_id' AND bldg_id='$i' AND level='$level' AND build=0 
	   AND structtime<'$time'", $soudb);
	  $num = mysql_affected_rows();
      if($num==1){
        //upgraden und die benötigten Rohstoffe abziehen
        $time=time();

		$sql="UPDATE sou_map_buildings SET build=0, level=level+1, 
		b1=b1-".intval($res_abzug[0]).", 
		b2=b2-".intval($res_abzug[1]).", 
		b3=b3-".intval($res_abzug[2]).", 
		b4=b4-".intval($res_abzug[3]).", 
		b5=b5-".intval($res_abzug[4]).", 
		b6=b6-".intval($res_abzug[5]).", 
		b7=b7-".intval($res_abzug[6]).", 
		b8=b8-".intval($res_abzug[7]).", 
		b9=b9-".intval($res_abzug[8]).", 
		b10=b10-".intval($res_abzug[9]).", 
        structtime='$time' WHERE owner_id='$owner_id' AND bldg_id='$i' AND level='$level'";
		
		//echo $sql;
		
        mysql_query($sql, $soudb);
        
        //tempfile löschen
  		$filename='soudata/cache/showdata1_'.$player_fraction.'.tmp';
  		if (file_exists($filename))unlink($filename);  
        
        //meldung für den chat hinzufügen
        //$text='<font color="#00FF00">F'.$_SESSION["sou_fraction"].' Geb&auml;udeausbau auf '.$owner_sysname.' ('.$player_x.':'.$player_y.'): '.$b_defs[$i][0].' (Stufe '.($level+1).')</font>';
  		$text='<font color="#00FF00">F'.$_SESSION["sou_fraction"].' '.$owner_sysname.' ('.$player_x.':'.$player_y.'): <img src="'.$gpfad.'t'.$i.'.jpg" width="16px" height="16px" title="'.$b_defs[$i][0].'"> (Stufe '.($level+1).')</font>';
      	$time=time();
        insert_chat_msg('^Der Reporter^', $text, 0, 0);
      	//mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('^Der Reporter^', '$text', '$time')",$soudb);
        
		//meldung in der fractionsnews hinterlegen
		$text=$owner_sysname.': '.$b_defs[$i][0].' ('.($level+1).')';
		mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$player_fraction',1, '$text')",$soudb);
        
        
        header("Location: sou_main.php?action=buildingpage");
      }
	}
    
    //wenn alles vorhanden ist, dann button für den ausbau anzeigen
    if($hasall==1)$benhasnot.='<div align="center"><a href="sou_main.php?action=buildingpage&do=build&bid='.($i+1).'"><div class="b1">ausbauen</div></a></div>';
    
    //wenn nicht alles vorhanden ist überprüfen ob er evtl. nen gebäudeupgrademodul hat
    if($hasallres!=1 AND $um_has[$level]>0)
    {
	  $benhasnot.='<div align="center"><a href="sou_main.php?action=buildingpage&do=3&bid='.($i+1).'"><div class="b1">Modul einsetzen</div></a></div>';
    }

    //voraussetzungen - ende 

    //gebäudename
    $output.='<td width="100px"><img id="tttid'.$i.'" src="'.$gpfad.'t'.$i.'.jpg" title="Vorhandene Voraussetzungen&'.$benhas.'"></td>';
    
    $output.='<td width="375px">'.$benhasnot.'</td>';
    
    if($spaltenflag==1)$output.='</tr>';
    $spaltenflag++;
    if($spaltenflag==2)$spaltenflag=0;
  }

  $output.='</table>';

  $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Geb&auml;udeverwaltung</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
  rahmen1_oben($routput);
    
  echo $output;
  
  //echo '<input type="submit" value="einlagern">';
  rahmen1_unten();
  //echo '<input type="image" src="'.$gpfad.'e.gif" style="width:0; height:0; border:0px;">';
  //echo '</form>';
}
else echo 'Hier gibt es kein Sonnensystem. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';
rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>