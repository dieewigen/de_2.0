<?php
include "soudata/defs/buildings.inc.php";
include "soudata/defs/resources.inc.php";
//daten zur ansicht
echo '<br><br>';

echo '<div align="center">';

rahmen0_oben();

$searchx=$player_x;
$searchy=$player_y;

//test auf sektorraumbasis
$db_daten=mysql_query("SELECT * FROM sou_map_base WHERE x='$searchx' AND y='$searchy'",$soudb);
$numsrb = mysql_num_rows($db_daten);
if($numsrb==1)
{
  $row = mysql_fetch_array($db_daten);
  $srb_fraction=$row["fraction"];
}


//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1 OR $numsrb==1)
{
  if($num==1)
  {
    $row = mysql_fetch_array($db_daten);
    $owner_id=$row["id"];
    $owner_sysname=$row["sysname"];
    $owner_fraction=$row["fraction"];
    $pirates=$row['pirates'];
  }
  
  //test auf piraten
  /*
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
      echo '<br>';
  	  rahmen2_oben();
  	  echo 'Dieses Sonnensystem wird von Piraten belagert. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	  rahmen2_unten();
  	  echo '<br>';

	  echo '</div>';//center-div
	  die('</body></html>');
    }
  }*/
  
  //�berpr�fen ob das sonnenystem zur eigenen fraktion geh�rt
  if($numsrb==0)//es ist ein sonnensystem
  {
  	if($player_fraction!=$owner_fraction)
  	{
  	  echo '<br>';
  	  rahmen2_oben();
  	  echo 'Hier hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	  rahmen2_unten();
  	  echo '<br>';

	  echo '</div>';//center-div
	  die('</body></html>');
    }
  }
  elseif($player_fraction!=$srb_fraction)//es ist eine srb
  {
  	if($player_fraction!=$owner_fraction)
  	{
  	  echo '<br>';
  	  rahmen2_oben();
  	  echo 'Hier hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	  rahmen2_unten();
  	  echo '<br>';

	  echo '</div>';//center-div
	  die('</body></html>');
    }
  }
  
  //buffs
  $addvaluecanmine_absolut=0;
  $addvaluecanmine_percentage=0;
  $addvaluehasspace_absolute=0;
  $addvaluehasspace_percentage=0;
  $addvaluegiveenergy_absolute=0;
  $addvaluegiveenergy_percentage=0;
  //buffs auslesen
  $time=time();
  $db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$player_user_id' AND time>'$time'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    if($row[typ]==1) $addvaluecanmine_percentage+=$row[value];
    elseif($row[typ]==2) $addvaluecanmine_absolut+=$row[value];
    
    //elseif($row[typ]==3) $addvaluehasspace_percentage+=$row[value];
    elseif($row[typ]==3) $addvaluecanmine_percentage+=$row[value];
    elseif($row[typ]==4) $addvaluehasspace_absolute+=$row[value];
    elseif($row[typ]==5) $addvaluegiveenergy_percentage+=$row[value];
    elseif($row[typ]==6) $addvaluegiveenergy_absolute+=$row[value];  
  }

  $geb_level=get_bldg_level($owner_id, 5);
  if($geb_level>0 OR $numsrb==1)
  {
    /*
  	//den preis f�r die versicherung berechnen
    $db_daten=mysql_query("SELECT MAX(shipdiameter) AS wert FROM `sou_user_data` WHERE fraction='$player_fraction'",$soudb);
    $row = mysql_fetch_array($db_daten);
    $insuranceprice=$row["wert"]*10000;*/
  	
  	//feststellen wieviel ins lager geht
	//$max_lager=get_bldg_level($owner_id, 5)*2;
	$max_lager=get_max_frac_bldg_level(5)*2;
  	
    //berechnen wieviele module man haben kann
    //$module_max=6+round(sqrt($player_ship_diameter));
    $module_normal_max=2+round(sqrt($player_ship_diameter));
    $module_fight_max=2+round(sqrt($player_ship_diameter));


  	//wenn ein submit gekommen ist, dann schauen ob die notfallmodule angefordert wurden
    if(isset($_REQUEST["sd"]) && $_REQUEST["sd"]==1 AND $numsrb==0)
    {
  	  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, location, canmine) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Bergbaumodul Standard', 1, 50)",$soudb);
  	  mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, location, canmine) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Bergbaumodul Standard', 1, 50)",$soudb);
  	  //mysql_query("INSERT INTO sou_ship_module (user_id, fraction, name, location, hasspace) VALUES ('$_SESSION[sou_user_id]', '$_SESSION[sou_fraction]', 'Lagermodul Standard', 1, 50)",$soudb);
    }
    
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
  	//wenn ein submit gekommen ist, dann schauen ob er ein modul l�schen m�chte
  	///////////////////////////////////////////////////////////////////////////////
  	///////////////////////////////////////////////////////////////////////////////
    if(isset($_REQUEST["do"]) && $_REQUEST["do"]==1)
    {
      $mid=intval($_REQUEST["di"]);
      mysql_query("DELETE FROM `sou_ship_module` WHERE user_id='$player_user_id' AND id='$mid' AND (location=0 OR location=1)",$soudb);
      $num = mysql_affected_rows();
      if($num==1)$msg='<font color="#00FF00">Das Modul wurde zerst&ouml;rt.</font>';
    }
    
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    //modul verschieben
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    if(isset($_REQUEST["do"]) && $_REQUEST["do"]==2)
    {
      //modul-id auslesen
      $mid=intval($_REQUEST["di"]);
      //feststellen, ob das modul einem geh�rt und an der richtigen position ist
      $db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) AND id='$mid' ORDER BY name",$soudb);
      $num = mysql_num_rows($db_daten);
      
      if($num==1)
      {
      	$row = mysql_fetch_array($db_daten);
      	//�berpr�fen ob es im schiff, oder im modulkomplex ist
      	if($row["location"]==0)//es ist im schiff
      	{
      	  //�berpr�fen ob ein forschungsmodul entfernt werden soll w�hrend es aktiv ist
      	  if($player_atimer2typ>0 AND $row["giveresearch"]>0)$msg='<font color="#FF0000">Aufgrund einer aktiven Forschung kann das Modul nicht entfernt werden. Der Forschungsfortschritt kann unter unter Schiff -> Forschung &uuml;berpr&uuml;ft werden. Fertige Forschungen k&ouml;nnen dort abgeschlossen werden.</font>';
      		
      	  //�berpr�fen ob im modulkomplex platz ist
          //zuerst die dortigen hinterlegen module z�hlen
		  $db_datenx=mysql_query("SELECT id FROM sou_ship_module WHERE user_id='$player_user_id' AND location=1",$soudb);
		  $num_lager = mysql_num_rows($db_datenx);
			
		  if($msg=='')
          if($num_lager < $max_lager)
          {
			mysql_query("UPDATE sou_ship_module SET location=1 WHERE user_id='$player_user_id' AND id='$mid'",$soudb);
			$msg='<font color="#00FF00">Das Modul wurde transferiert.</font>';
          }
          else $msg='<font color="#FF0000">Im Modulkomplex ist nicht genug Platz vorhanden.</font>';
      	}
      	else//es ist im modulkomplex
      	{
      	  //transaktionsbeginn
          if (setLock($_SESSION["sou_user_id"]))
          {
			
          	//daten des moduls auslesen, das eingebaut werden soll
          	$modul_giveresearch=$row["giveresearch"];
          	$modul_buff=$row[buff];
          	$modul_mapbuff=$row[mapbuff];
          	
          	//anzahl der verschiedenen modultypen auslesen
          	$db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0 AND (hasspace>0 OR canmine>0)",$soudb);
          	$anz_modul_normal=mysql_num_rows($db_daten);
          	$db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0 AND (giveweapon>0 OR giveshield>0)",$soudb);
          	$anz_modul_fight=mysql_num_rows($db_daten);
          	
          	//test auf bonusmodule
          	if($row['buff']=='' AND $row['mapbuff']=='')
          	{
          	  //bergbau/lager
          	  if($row['hasspace']>0 OR $row['canmine']>0)
          	  {
			  	if($anz_modul_normal<$module_normal_max)
			  	{
			  	  mysql_query("UPDATE sou_ship_module SET location=0 WHERE user_id='$player_user_id' AND id='$mid'",$soudb);
			  	  $msg='<font color="#00FF00">Das Modul wurde transferiert.</font>';
			  	}
			  	else $msg='<font color="#FF0000">Im Raumschiff ist kein freier Modulplatz vorhanden.</font>';
          	  }
          	  //waffen/schilde
          	  elseif($row['giveweapon']>0 OR $row['giveshield']>0)
          	  {
			  	if($anz_modul_fight<$module_fight_max)
			  	{
			  	  mysql_query("UPDATE sou_ship_module SET location=0 WHERE user_id='$player_user_id' AND id='$mid'",$soudb);
			  	  $msg='<font color="#00FF00">Das Modul wurde transferiert.</font>';
			  	}
			  	else $msg='<font color="#FF0000">Im Raumschiff ist kein freier Modulplatz vorhanden.</font>';
          	  }
          	  //sondermodule - werden getauscht
          	  else 
          	  {
          	    if($row["givesubspace"])$searchfield='givesubspace';
          	    if($row["givecenter"])$searchfield='givecenter';
          	    if($row["givelife"])$searchfield='givelife';
          	    if($row["givehyperdrive"])$searchfield='givehyperdrive';
          	    if($row["giveresearch"])$searchfield='giveresearch';
          	    if($row["canrecover"])$searchfield='canrecover';
          	    if($row["canclone"])$searchfield='canclone';
          	    //zuerst ein vorhandenes zielmodul aus dem schiff entfernen
				mysql_query("UPDATE sou_ship_module SET location=1 WHERE user_id='$player_user_id' AND location=0 AND $searchfield>0",$soudb);
				
          	    //dann das modul in das schiff packen
			  	mysql_query("UPDATE sou_ship_module SET location=0 WHERE user_id='$player_user_id' AND id='$mid'",$soudb);
			  	$msg='<font color="#00FF00">Das Modul wurde transferiert.</font>';          	  
          	  }
            }
          	else $msg='<font color="#FF0000">Diese Art von Modul kann nicht in das Raumschiff transferiert werden.</font>'; 
    		//lock wieder entfernen
    		$erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
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
      }
      else $msg='<font color="#FF0000">Das Modul kann nicht transferiert werden.</font>';
    }
    
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    //modul versichern
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    /*
    if($_REQUEST["do"]==3)
    {
      //modul-id auslesen
      $mid=intval($_REQUEST["di"]);
    	
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
    	//�berpr�fen ob er genug geld hat
    	$hasmoney=has_money($player_user_id);
      	if($hasmoney>=$insuranceprice)
      	{
      	  //geld abziehen
      	  change_money($player_user_id, $insuranceprice*(-1));
      	  
      	  //versicherung hinterlegen
      	  mysql_query("UPDATE sou_ship_module SET insurance=1 WHERE user_id='$player_user_id' AND id='$mid'",$soudb);
      	  
      	  //best�tigung ausgeben
      	  $msg='<font color="#00FF00">Das Modul wurde versichert.</font>';
      	}
      	else $msg='<font color="#FF0000">Du hast nicht genug Zastari.</font>';
      	
      	//lock wieder entfernen
    	$erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
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
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    //modul verwenden
    ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    if(isset($_REQUEST["do"]) && $_REQUEST["do"]==4)
    {
      //modul-id auslesen
      $mid=intval($_REQUEST["di"]);
    	
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
        //feststellen, ob das modul einem geh�rt
        $db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) AND id='$mid' ORDER BY name",$soudb);
        $num = mysql_num_rows($db_daten);
      
        if($num==1)
        {
          //modul verwenden
          //buffdaten zuerst auslesen
          $row = mysql_fetch_array($db_daten);
          
  		  if($row['buff']!='')
  		  {
  		    //wert parsen
    		$value=explode(";",$row[buff]);
    		for($n=0;$n<count($value);$n++)
    		{
      		  $einzelvalue=explode("x",$value[$n]);
      		  
      		  //typ 1 rohstoffbonus in prozent
      		  //typ 2 rohstoffbonus absolut
      		  if($einzelvalue[0]>0)
      		  {
      		    $btyp=$einzelvalue[0];
      		    $bvalue=$einzelvalue[1];
      		    $btime=$einzelvalue[2]*24*3600+time();
				
      		    //buff in der db hinterlegen, dazu überprüfen ob es schon einen buff dieser art gibt und den ggf. verl�ngern
      		    $db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$player_user_id' AND time>'$time' AND typ='$btyp' AND value='$bvalue'",$soudb);
      		    $num = mysql_num_rows($db_daten);
      		    if($num==1) //datensatz updaten
      		    {
      		    	mysql_query("UPDATE `sou_user_buffs` SET time=time+'".($einzelvalue[2]*24*3600)."' WHERE user_id='$player_user_id' AND time>'$time' AND typ='$btyp' AND value='$bvalue' LIMIT 1",$soudb);
      		    }
      		    else //neuen datensatz anlegen
      		    {
            	  mysql_query("INSERT INTO sou_user_buffs (user_id, typ, value, time) VALUES ('$player_user_id', '$btyp', '$bvalue', '$btime');",$soudb);
      		    } 
            	
            	//modul entfernen
            	mysql_query("DELETE FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND id='$mid';",$soudb);
      		  }
    		}
            
    		//best�tigung ausgeben
        	$msg='<font color="#00FF00">Das Modul wurde verwendet.</font>';
  		  }
  		  
  		  //mapbuffs
  		  if($row[mapbuff]!=''){
  		    //wert parsen
    		$value=explode(";",$row['mapbuff']);
    		for($n=0;$n<count($value);$n++){
      		  $einzelvalue=explode("x",$value[$n]);
      		  
      		  //typ 1 geistige stärke
			  //deaktiviert während der Apokalypse
      		  if($einzelvalue[0]==1 && 1==2){
				$btyp=$einzelvalue[0];
      		    $bvalue=$einzelvalue[1];
      		    $btime=$einzelvalue[2]*24*3600+time();
				
      		    //buff in der db hinterlegen, wenn er l�nger ist, als ein bestehende Buff
				$db_daten=mysql_query("SELECT * FROM `sou_map_buffs` WHERE owner_id='$owner_id'",$soudb);
				$possible=true;
				while($row = mysql_fetch_array($db_daten)){
					if($row['typ']==1 && $row['time']>$btime)$possible=false;
				}
				
				if($possible){
					mysql_query("INSERT INTO sou_map_buffs (owner_id, typ, value, time) VALUES ('$owner_id', '$btyp', '$bvalue', '$btime');",$soudb);

					//modul entfernen
					mysql_query("DELETE FROM `sou_ship_module` WHERE user_id=".$_SESSION['sou_user_id']." AND id='$mid';",$soudb);
					//best�tigung ausgeben
					$msg='<font color="#00FF00">Das Modul wurde verwendet.</font>';
					
				}else $msg='<font color="#FF0000">Es besteht bereits ein l&auml;nger anhaltender Schutz.</font>';
      		  }
    		}
  		  }
    	
      	}
      	else $msg='<font color="#FF0000">Dieses Modul geh&ouml;rt Dir nicht.</font>';
      	
      	//lock wieder entfernen
    	$erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
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
    
	$gesamthasspace=0;
	$gesamtcanmine=0;  	
	$gesamtgivelife=0;
	$gesamtgivesubspace=0;
	$gesamtgivecenter=0;
	$gesamtgiveresearch=0;
	$gesamtgiveweapon=0;
	$gesamtgiveshield=0;
	$gesamtcanrecover=0;
	$gesamtcanclone=0;
	
  	//alle module auslesen und auswerten
  	echo '<script language="javascript">';
 	echo 'var delmsg = "Soll das Modul wirklich gel�scht werden? Achtung: Bei unsachgem��er Handhabung kann das Schiff unbrauchbar werden.";';
 	//echo 'var insurancemsg = new Array("Versicherung","Hier kann das Modul versichert werden, um bei einer Zerst&ouml;rung des Raumschiffes wieder ersetzt zu werden. Bei einer Zerst&ouml;rung werden die Ersatzmodule von der Versicherung im Modulkomplex hinterlegt. Die Versicherung ist einmalig und mu� nach einer Inanspruchname erneuert werden.<br>Versicherungkosten: '.number_format($insuranceprice, 0,",",".").' Zastari");';
 	$modulusemsg = 'Modul verwenden&Anklicken um das Modul zu verwenden.';
  	$output2='<table width="100%" border="0" cellpadding="1" cellspacing="0">';
  	$output1='<table width="100%" border="0" cellpadding="1" cellspacing="0">
  	<tr><td align="center"><b>Bergbau ({REPLACE}/'.$module_normal_max.')</td></td></tr>';
  	$output1a='<tr><td align="center"><b>Sondermodule</td></td></tr>';
  	$output1b='<tr><td align="center"><b>Waffen und Schilde ({REPLACE}/'.$module_fight_max.')</td></td></tr>';
  	
  	$atipc=0;$modulkomplexc=0;$moduleinraumschiff=0;$moduleinraumschiff_sonder=0;$moduleinraumschiff_normal=0;
    $db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) ORDER BY name",$soudb);
    while($row = mysql_fetch_array($db_daten))
    {
      //js-tooltip bauen
      
      $atip[$atipc] = make_modul_name_js($row).'&'.make_modul_info($row);
      
      //link zur modull�schung
      $deletelink='<a href="sou_main.php?action=modulholdpage&do=1&di='.$row["id"].'" onClick="return confirm(delmsg)"><img border="0" 
      style="vertical-align: middle;" src="'.$gpfad.'abutton3.gif" alt="Modul zerst&ouml;ren"></a>';
      
      //link zum modultransfer
      $removelink1='<a href="sou_main.php?action=modulholdpage&do=2&di='.$row["id"].'"><img border="0" 
      style="vertical-align: middle;" src="'.$gpfad.'abutton5.gif" alt="Modul transferieren"></a>';
      $removelink2='<a href="sou_main.php?action=modulholdpage&do=2&di='.$row["id"].'"><img border="0" 
      style="vertical-align: middle;" src="'.$gpfad.'abutton4.gif" alt="Modul transferieren"></a>';
      
      //link zur modulversicherung
      if($row["insurance"]==0)
      //$insurancelink='<a href="sou_main.php?action=modulholdpage&do=3&di='.$row["id"].'" onMouseOver="stm(insurancemsg,Style[0])" onMouseOut="htold()"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'a17.gif" alt="Modul versichern"></a> ';
      $insurancelink='';
      else $insurancelink='';
      
      //bei buffmodulen stehen nicht alle funktionen zur verf�gung
      $uselink='';
      if($row[buff]!='' OR $row[mapbuff]!='')
      {
      	$removelink1='';
      	$removelink2='';
      	$insurancelink='';
      
        $uselink='<a href="sou_main.php?action=modulholdpage&do=4&di='.$row["id"].'" title="'.$moduleusemessage.'"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'a26.gif" alt="Modul verwenden"></a> ';
        echo '<br><br><br><br>'.$uselink;
      }
    	
      if($row["location"]==0)//das modul ist im schiff
      {
        //daten f�r die schiffszusammenfassung
      	$gesamtneedspace+=$row["needspace"];
    	$gesamtneedenergy+=$row["needenergy"];
    	$gesamtgiveenergy+=$row["giveenergy"];
    	$gesamthasspace+=$row["hasspace"];
    	$gesamtcanmine+=$row["canmine"];
    	$gesamtgivelife+=$row["givelife"];
    	$gesamtgivesubspace+=$row["givesubspace"];
    	$gesamtgivecenter+=$row["givecenter"];
    	$gesamtgiveresearch+=$row["giveresearch"];
    	$gesamtgiveweapon+=$row["giveweapon"];
    	$gesamtgiveshield+=$row["giveshield"];
    	$gesamtcanrecover+=$row['canrecover'];
    	$gesamtcanclone+=$row['canclone'];
    	
    	if($row["givehyperdrive"]>0)
    	{
  		  $speed=calc_hyperdrive_speed($row["givehyperdrive"]);
  		  if($speed>$savespeed)$savespeed=$speed;    	
    	}
    	
    	//unterscheidung in normale, kampfmodule und sondermodule
    	if($row["givesubspace"]>0 OR $row["givecenter"]>0 OR $row["givelife"]>0 OR $row["givehyperdrive"]>0 OR $row["giveresearch"]>0 OR $row["canrecover"]>0 OR $row["canclone"]>0 OR $row['canbldgupgrade']>0)
    	{
      	  //hintergrund bestimmen
          if ($c1c==0){$c1c=1;$bg='cell1';}else{$c1c=0;$bg='cell';}
  		  $output1a.='<tr align="left">';
  		  //modulname
  		  $output1a.='<td class="'.$bg.'"><span title="'.$atip[$atipc].'">'.make_modul_name($row).'</span></td>';
  		  //modulinfo
  		  //$output1.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		  $output1a.='</tr>';
  		  $moduleinraumschiff_sonder++;    	  
    	}
        elseif($row["giveweapon"]>0 OR $row["giveshield"]>0)//waffen
    	{
      	  //hintergrund bestimmen
          if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
  		  $output1b.='<tr align="left">';
  		  //modulname
  		  $output1b.='<td class="'.$bg.'">'.$removelink1.' <span title="'.$atip[$atipc].'">'.make_modul_name($row).'</span> '.$insurancelink.$deletelink.$uselink.'</td>';
  		  //modulinfo
  		  //$output1.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		  $output1b.='</tr>';
  		  $moduleinraumschiff_fight++;    	  
    	}    	
    	else //lager/bergbau
    	{
      	  //hintergrund bestimmen
          if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
  		  $output1.='<tr align="left">';
  		  //modulname
  		  $output1.='<td class="'.$bg.'">'.$removelink1.' <span title="'.$atip[$atipc].'">'.make_modul_name($row).'</span> '.$insurancelink.$deletelink.$uselink.'</td>';
  		  //modulinfo
  		  //$output1.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		  $output1.='</tr>';
  		  $moduleinraumschiff_normal++;
    	}
      }
      else //das modul ist im modulkomplex
      {
        //hintergrund bestimmen
        if ($c1b==0){$c1b=1;$bg='cell1';}else{$c1b=0;$bg='cell';}      	
  		$output2.='<tr align="left">';
  		//modulname
  		$output2.='<td class="'.$bg.'">'.$deletelink.' <span title="'.$atip[$atipc].'">'.make_modul_name($row).'</span> '.$insurancelink.$removelink2.$uselink.'</td>';
  		//modulinfo
  		//$output2.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		$output2.='</tr>';
  		$modulkomplexc++;
      }
      $atipc++;
    }
    
    //anzahl der module in der ausgabe hinterlegen
    //bergbau/lager
    $output1=str_replace('{REPLACE}', $moduleinraumschiff_normal, $output1);
    //kampf
    $output1b=str_replace('{REPLACE}', $moduleinraumschiff_fight, $output1b);
    
  	$output1.=$output1b.$output1a.'</table>';
  	$output2.='</table>';
    echo '</script>';

    //messages anzeigen
    if($msg!='')
    {
      echo '<br>';
      rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
    }
    
    
//seitenteiler
echo '<table border="0" cellpadding="1" cellspacing="0">';
echo '<tr><td width="300" valign="top"><br>';
rahmen1_oben('<div align="center"><b>'.$b_defs[5][0].' ('.$modulkomplexc.'/'.$max_lager.')</b></div>');

echo $output2;

rahmen1_unten();

//seitenteiler
echo '</td><td width="300" valign="top"><br>';
rahmen1_oben('<div align="center"><b>Module im Raumschiff</b></div>');

echo $output1;

rahmen1_unten();

//seitenteiler
echo '</td><td width="300" valign="top"><br>';

rahmen2_oben();
echo '<a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
rahmen2_unten();

echo '<br>';

rahmen1_oben('<div align="center"><b>Zusammenfassung</b></div>');

//moduleanzeige bauen

echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
echo '<tr align="left"><td class="cell1">';
//$volumen = floor(4/3 * pow(($player_ship_diameter*0.95) / 2, 3) * pi());
//echo 'Nutzvolumen: '.number_format($volumen, 0,",",".").' m&sup3;';
//echo '<br>Freies Nutzvolumen: '.number_format($volumen-$gesamtneedspace, 0,",",".").' m&sup3;';

if($gesamtgivelife<1){$str1='<font color="#FF0000">';$str2='</font>';}else{$str1='';$str2='';}
echo 'Lebenserhaltung (Personen): '.$str1.number_format($gesamtgivelife, 0,",",".").$str2;

echo '<br>Klonkapazit&auml;t: '.number_format($gesamtcanclone, 0,",",".").' ZQ';

if($gesamtgivecenter>0)$str='<font color="#00FF00">vorhanden</font>';else $str='<font color="#FF0000">nicht vorhanden</font>';
echo '<br>Raumschiffzentrale: '.$str;

if($gesamtgivesubspace>0)$str='<font color="#00FF00">vorhanden</font>';else $str='<font color="#FF0000">nicht vorhanden</font>';
echo '<br>Unterlichtantrieb: '.$str;

if($savespeed>0)echo '<br>&Uuml;berlichtgeschwindigkeit: '.number_format($savespeed, 0,",",".").' Sek/Lichtjahr';
else echo '<br>&Uuml;berlichtgeschwindigkeit: <font color="FF0000">Modul fehlt</font>';

echo '<br>Forschungskapazit&auml;t: '.number_format($gesamtgiveresearch, 0,",",".").' FP';
echo '<br>Waffenkapazit&auml;t: '.number_format($gesamtgiveweapon, 0,",",".").' EE';
echo '<br>Schildkapazit&auml;t: '.number_format($gesamtgiveshield, 0,",",".").' EE';

//echo 'A: '.$addvaluehasspace_absolute;

//echo '<br>Vorhandener Frachtraum: '.number_format($gesamthasspace, 0,",",".").' <font color="#00FF00">('.number_format($gesamthasspace+$addvaluehasspace_absolute+($gesamthasspace/100*$addvaluehasspace_percentage), 0,",",".").')</font> m&sup3;';

//bergbaukapazit�t
$tooltip='Rohstoffmenge&';
for($resid=0;$resid<count($r_def);$resid++)
{
  $tooltip.=number_format(floor(($gesamtcanmine+$addvaluecanmine_absolut+($gesamtcanmine/100*$addvaluecanmine_percentage))/$r_def[$resid][1]*(1+(get_skill($resid)/500000))), 0,",",".").' '.$r_def[$resid][0].' m&sup3;/Min<br>';
}

echo '<br>Bergbaukapazit&auml;t: '.number_format($gesamtcanmine, 0,",",".").' <font color="#00FF00">('.
number_format($gesamtcanmine+$addvaluecanmine_absolut+($gesamtcanmine/100*$addvaluecanmine_percentage), 0,",",".").
')</font> m&sup3;/Min <img id="tt1" border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px" title="'.$tooltip.'">';

echo '<br>Bergungskapazit&auml;t: '.number_format($gesamtcanrecover, 0,",",".").' BK';

echo '</td></tr></table> ';

//�berpr�fen ob das schiff funktionsf�hig ist
/*
if($gesamtgivelife<1 OR ($gesamtgiveenergy-$gesamtneedenergy)<0 OR $gesamtgivesubspace<=0 OR $freehold<0 OR $gesamtgivecenter<=0)
{
  mysql_query("UPDATE sou_user_data SET shipnotready=1 WHERE user_id='$player_user_id' AND shipnotready=0",$soudb);
}
else mysql_query("UPDATE sou_user_data SET shipnotready=0 WHERE user_id='$player_user_id' AND shipnotready=1",$soudb);
*/

rahmen1_unten();



//seitenteiler
echo '</td></tr></table>';


    //die m�glichkeit geben komplett neu anzufangen, nicht m�glich auf einer srb
	if($numsrb==0)
	{
      echo '<br>';
      
	  rahmen1_oben('<div align="center"><b>Notfallmodule</b></div>');
	
      echo 'Hier kannst Du im Notfall ein Bergbaumodul anfordern.<br><br>';

      echo '<a href="sou_main.php?action=modulholdpage&sd=1" onClick="return confirm(\'Module anfordern?\')"><div class="b1">Module anfordern</div></a><br>';

      rahmen1_unten();
      echo '<br>';

      rahmen0_unten();

      echo '<br>';

	}
  }
  else echo '<br>Fehlendes Geb&auml;ude: '.$b_defs[5][0].' <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';
}
else echo '<br>Hier gibt es kein Sonnensystem.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';


echo '</div>';//center-div

die('</body></html>');

//UPDATE sou_ship_module SET name=REPLACE( name, CHAR(13,10), '' );
?>