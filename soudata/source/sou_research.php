<?php
include "soudata/defs/buildings.inc.php";
include "soudata/defs/resources.inc.php";

echo '<script language="javascript">';
echo 'var atip = new Array();';
echo 'atip[0] = ["Information","Hier kann die Komfortfunktion f&uuml;r die Rohstoffeinlagerung in Anspruch genommen werden. Das Raumschiff fliegt maximal 60 Mal in das Asteriodenfeld und sammelt dort jeweils 1 Minute Rohstoffe und stellt es dann der entsprechende Forschung zur Verf&uuml;gung. &Uuml;bersch&uuml;ssige Rohstoffe, die nicht mehr f&uuml;r die Forschung ben&ouml;tigt werden, gehen verloren. F&uuml;r den Transfer wird nur die freie Frachtraumkapazit&auml;t verwendet.<br><br><font color=\"#00FF00\">Credit-Kosten: 1 (für Premium-Account-Nutzer kostenlos)</font>"];';
echo 'atip[1] = ["Information","Transferiert den gesamten verf&uuml;gbaren und ben&ouml;tigten Frachtrauminhalt"];';
echo '</script>';

//auswahl der anzeige
$viewtypes[]='Anzeige: Alle erforschbaren Technologien';
$viewtypes[]='Anzeige: Alle erforschten Technologien';
$viewtypes[]='Anzeige: Alle erforschten und erforschbaren Technologien';
if(isset($_REQUEST["viewtyp"]))
{
  $_SESSION["sou_research_viewtyp"]=intval($_REQUEST["viewtyp"]);
}
if($_SESSION["sou_research_viewtyp"]=='' OR $_SESSION["sou_research_viewtyp"]<0 OR $_SESSION["sou_research_viewtyp"]>2)$_SESSION["sou_research_viewtyp"]=0;


//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $row = mysql_fetch_array($db_daten);
  $owner_id=$row["id"];
  $owner_sysname=$row["sysname"];
  $owner_fraction=$row["fraction"];
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
      echo '<br>';
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
  	echo 'Dieses Sonnensystem ist umk&auml;mpft und es k&ouml;nnen keine Forschungen durchgef&uuml;hrt werden. Fr&uuml;hestm&ouml;glicher Endzeitpunkt der K&auml;mpfe: '.date("H:i:s d.m.Y", $owner_underattack+3600*24*2).'<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }
  
  
  //gebäudelevel auslesen
  //$geb_level=get_bldg_level($owner_id, 1);
  $geb_level=get_max_frac_bldg_level(1);
  if($geb_level>0)
  {
  	/////////////////////////////////////////////////
  	/////////////////////////////////////////////////
  	//alle erforschten technologien auslesen
  	/////////////////////////////////////////////////
  	/////////////////////////////////////////////////
  	unset($has_techs);
  	$search='f'.$player_fraction.'lvl';
  	$sql="SELECT tech_id FROM `sou_frac_techs` WHERE $search>0 ORDER BY sort_id ASC";
  	$db_daten=mysql_query($sql,$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      $has_techs[]=$row["tech_id"];
    }
    //print_r($has_techs);
 	
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////
    //alle erledigten quests auslesen
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////
  	unset($has_quests);
  	$sql="SELECT id FROM `sou_frac_quests` WHERE done>0 AND fraction=$player_fraction";
  	$db_daten=mysql_query($sql,$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      $has_quests[]=$row["id"];
    }
    //print_r($has_quests);
    
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////
    //wenn ein submit gekommen ist, dann schauen ob er rohstoffe einzahlen möchte
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////
    if($_REQUEST["do"]=='1')
    {
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
  	    //alle forschungen durchlaufen und überprüfen ob es eine einzahlung gibt
  	    //for($i=0;$i<count($b_defs);$i++)
		$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` ORDER BY sort_id ASC",$soudb);
  		while($row = mysql_fetch_array($db_daten))  	    
    	{
      	  //die vorbedingungen parsen und überprüfen ob jemand etwas davon einzahlen möchte
          if($row["tech_vor"]!='')
          {
            $ben_id=1;
            $i=$row["tech_id"];
            
      	    $need_vor=explode(";",$row["tech_vor"]);
            for($n=0;$n<count($need_vor);$n++)
            {
              $einzelneed=explode("x",$need_vor[$n]);
              //es kann verschiedene arten von bedingungen geben
              if($einzelneed[0][0]=='B')
              {
				//bei gebäuden nichts machen
              }
    	      //zastari
    	      elseif($einzelneed[0][0]=='Z')
    	      {
    	      	$search='f'.$player_fraction.'b'.$ben_id;
   	      	
    	      	//feststellen wieviel zastari man benötigt
    	      	$need=$einzelneed[1]-$row[$search];
    	      	if($need<0)$need=0;
    	      	
    	      	//feststellen wieviel man geben möchte
    	      	$searchv='b'.$i.'n'.$ben_id;
    			$spende=intval($_REQUEST[$searchv]);
    			
    			if($spende>0)
    			{
    			  //überprüfen ob man soviel hat
				  $has_need=has_money($_SESSION["sou_user_id"]);
				  if($spende>$has_need)$spende=$has_need;

				  //überprüfen ob man soviel braucht
				  if($spende>$need)$spende=$need;
				
				  //umbuchen, wenn möglich
				  if($spende>0)
				  {
				    //geld vom konto abziehen
				    change_money($_SESSION["sou_user_id"], $spende*-1);
				    //dieses als spende notieren
				    $donate=$spende;
				    if($donate<0)$donate=0;
				    mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				    //fraktionsansehen vergrößern
					$feldname='prestige'.$player_fraction;
					mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate' WHERE id='$owner_id'",$soudb);
				    //geld der forschung gutschreiben
				    mysql_query("UPDATE `sou_frac_techs` SET $search=$search+'$donate' WHERE tech_id='$i'",$soudb);
				  }
    			}
	            $ben_id++;
    	      }
    	      //dunkle materie
    	      elseif($einzelneed[0][0]=='D')
    	      {
    	      	$search='f'.$player_fraction.'b'.$ben_id;
   	      	
    	      	//feststellen wieviel dunkle materie man benötigt
    	      	$need=$einzelneed[1]-$row[$search];
    	      	if($need<0)$need=0;
    	      	
    	      	//feststellen wieviel man geben möchte
    	      	$searchv='b'.$i.'n'.$ben_id;
    			$spende=intval($_REQUEST[$searchv]);
    			
    			if($spende>0)
    			{
    			  //überprüfen ob man soviel hat
				  $has_need=has_darkmatter($_SESSION["sou_user_id"]);
				  if($spende>$has_need)$spende=$has_need;

				  //überprüfen ob man soviel braucht
				  if($spende>$need)$spende=$need;
				
				  //umbuchen, wenn möglich
				  if($spende>0)
				  {
				    //dunkle materie abziehen
				    change_darkmatter($_SESSION["sou_user_id"], $spende*-1);
				    //dieses als spende notieren
				    $donate=$spende;
				    if($donate<0)$donate=0;
				    mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				    //fraktionsansehen vergrößern
					$feldname='prestige'.$player_fraction;
					mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate' WHERE id='$owner_id'",$soudb);
				    //DM der forschung gutschreiben
				    mysql_query("UPDATE `sou_frac_techs` SET $search=$search+'$donate' WHERE tech_id='$i'",$soudb);
				  }
    			}
	            $ben_id++;
    	      }
    	      //credits
    	      elseif($einzelneed[0][0]=='C')
    	      {
    	      	$search='f'.$player_fraction.'b'.$ben_id;
   	      	
    	      	//feststellen wieviel dunkle materie man benötigt
    	      	$need=$einzelneed[1]-$row[$search];
    	      	if($need<0)$need=0;
    	      	
    	      	//feststellen wieviel man geben möchte
    	      	$searchv='b'.$i.'n'.$ben_id;
    			$spende=intval($_REQUEST[$searchv]);
    			
    			if($spende>0)
    			{
    			  //überprüfen ob man soviel hat
				  $has_need=has_credits($ums_user_id);
				  if($spende>$has_need)$spende=$has_need;

				  //überprüfen ob man soviel braucht
				  if($spende>$need)$spende=$need;
				
				  //umbuchen, wenn möglich
				  if($spende>0)
				  {
				    //credits abziehen
				    change_credits($ums_user_id, $spende*-1, 'Fraktionsforschung');
				    //dieses als spende notieren
				    $donate=$spende;
				    if($donate<0)$donate=0;
				    mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				    //fraktionsansehen vergrößern
					$feldname='prestige'.$player_fraction;
					mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate' WHERE id='$owner_id'",$soudb);
				    //DM der forschung gutschreiben
				    mysql_query("UPDATE `sou_frac_techs` SET $search=$search+'$donate' WHERE tech_id='$i'",$soudb);
				  }
    			}
	            $ben_id++;
    	      }
    	      //rohstoffe
    	      else
    	      {
    	      /*
    	      	$search='f'.$player_fraction.'b'.$ben_id;
   	      	
    	      	//feststellen wieviel rohstoffe man benötigt
    	      	$need=$einzelneed[0]-$row[$search];
    	      	if($need<0)$need=0;
    	      	
    	      	//feststellen wieviel man geben möchte
    	      	$searchv='b'.$i.'n'.$ben_id;

    			$spende=intval($_REQUEST[$searchv]);
				
    			if($spende>0)
    			{
    			  //überprüfen ob man soviel hat
				  $has_need=has_hold_amount($_SESSION["sou_user_id"], $einzelneed[1]);
				  
				  if($spende>$has_need)$spende=$has_need;

				  //überprüfen ob man soviel braucht
				  if($spende>$need)$spende=$need;
				
				  //umbuchen, wenn möglich
				  if($spende>0)
				  {
				    //rohstoffe aus dem frachtraum entfernen
				    change_hold_amount($_SESSION["sou_user_id"], $einzelneed[1],$spende*-1);
   				    //dieses als spende notieren
				    $donate=$spende*$r_def[$einzelneed[1]][2];
				    if($donate<0)$donate=0;
				    mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				    //fraktionsansehen vergrößern
					$feldname='prestige'.$player_fraction;
					mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate' WHERE id='$owner_id'",$soudb);
		  
				    //rohstoffe der forschung gutschreiben
				    mysql_query("UPDATE `sou_frac_techs` SET $search=$search+'$spende' WHERE tech_id='$i'",$soudb);
				  }
				}
	            $ben_id++;
	            //echo 'b';
	             */
    	      }
    	    }
          }
    	}    		
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
  	
    
    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    //  x minuten rohstoffe einzahlen
    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    if($_REQUEST["do"]=='2')
    {
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
  	    $tid=intval($_REQUEST["tid"]);
  	    //spendenpunkt
  	    $rid=intval($_REQUEST["rid"]);
  	    //bergbaudauer
  	    $min=intval($_REQUEST["min"]);
  	    if($min<5 OR $min>480)$min=5;
  	    

		//schauen ob es die forschung gibt
  	    $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id='$tid'",$soudb);
		$num = mysql_num_rows($db_daten);
		if($num==1)
		{
		  $row=mysql_fetch_array($db_daten);
    	  //die vorbedingungen parsen und überprüfen ob jemand etwas davon einzahlen möchte
          if($row["tech_vor"]!='')
          {
            $ben_id=1;
            
      	    $need_vor=explode(";",$row["tech_vor"]);
            for($n=0;$n<count($need_vor);$n++)
            {
              $einzelneed=explode("x",$need_vor[$n]);
              //es kann verschiedene arten von bedingungen geben
              if($einzelneed[0][0]=='B')
              {
				//bei gebäuden nichts machen
              }
    	      //zastari
    	      elseif($einzelneed[0][0]=='Z')
    	      {
	            //bei zastari nichts machen, außer die resplatz-id zu erhöhen
    	      	$ben_id++;
    	      }
    	      //rohstoffe
    	      else
    	      {
  	  			if($rid==$ben_id)
  	  			{
    	      	  $search='f'.$player_fraction.'b'.$ben_id;
  	  			
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
  	  			      if($min>60)$needcredits=10;
  	  	  			  if($ums_premium==1)$needcredits=0;
          			  $has_credits=has_credits($ums_user_id);
  	  	  			  if($has_credits>=$needcredits)
  	  	  		      {
  	  	      			//überprüfen ob der rohstoff dort vorkommt
      					if(res_is_available($einzelneed[1])==1)
      					{
          		          //berechnen wieviel rohstoffe man pro minute bekommen kann
          			      $getres=round($canmine/$r_def[$einzelneed[1]][1]*(1+(get_skill($einzelneed[1])/500000)));
          			      //überprüfen ob das alles in den lagerraum paßt
          			      //if($getres>$freehold)$getres=$freehold;
          			      //gesamtertrag
          			      $getres=$getres*$min;
          			      
          			      //feststellen wieviel man noch benötigt
          			      //gesamtbedarf: $einzelneed[0]
          			      //bereits eingezahlt: $row[$search]
          			      $benres=$einzelneed[0]-$row[$search];
          			      
  						  //überprüfen wie oft er fliegen muß
  						  $anz_fluege=ceil($benres/($getres/$min));
  				   	      if($anz_fluege>$min)$anz_fluege=$min;
  				   	      
   				          //dieses als spende notieren
				          $donate=($getres/$min)*$r_def[$einzelneed[1]][2]*$anz_fluege;
				          if($donate<0)$donate=0;
				          mysql_query("UPDATE `sou_user_data` SET donate=donate+'$donate' WHERE user_id='$player_user_id'",$soudb);
				          //fraktionsansehen vergrößern
					      $feldname='prestige'.$player_fraction;
					      mysql_query("UPDATE `sou_map` SET $feldname=$feldname+'$donate' WHERE id='$owner_id'",$soudb);
          			  
				          //rohstoffe der forschung gutschreiben
				          mysql_query("UPDATE `sou_frac_techs` SET $search=$search+'$getres' WHERE tech_id='$tid'",$soudb);
				        
  						  //credits abziehen
  			    		  if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Forschung-Komfortfunktion'); 
          	
				          //einen counter setzen, damit sonst nichts mehr zu machen geht
          			      $time=time()+$anz_fluege*60;
          			      
          				  //skill verbessern
          				  change_skill($einzelneed[1], $anz_fluege);          			      
          			      
          			      mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
					      //seite neu laden
					      
          			      header("Location: sou_main.php");
      					}
      					else $msg='<font color="#FF0000">Diese Rohstoffart ist hier nicht vorhanden.</font>';          			      
 	  	  		      }
 	  	  		      else $msg='<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font>';	
  	  	  		    }
  	  	  		    else $msg='<font color="#FF0000">Es ist kein Bergbaumodul vorhanden.</font>';
  	  			  }
  	  			  else $msg='<font color="#FF0000">Es ist kein freier Frachtraum vorhanden.</font>';
  	  		    }    	      	
	            $ben_id++;
    	      }
    	    }
          }
    	}    		
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
    
  	//techs darstellen
	echo '<br>';
	
  	if($msg!='')
	{
  	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
  	  echo '<br>';
    }

    echo '<form action="sou_main.php" method="POST" name="f">';
	echo '<input type="hidden" name="action" value="researchpage">';
	echo '<input type="hidden" name="do" value="1">';
    
    //techs anzeigen

  	$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
  	$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  	$output.='<tr align="center" class="'.$bg.'"><td><b>Technologie</b></td><td><b>Voraussetzungen</b><td><b>Informationen</b></tr>';
  	//levelfeld definieren
  	$search='f'.$player_fraction.'lvl';
  	//viewtype beachten
  	if($_SESSION["sou_research_viewtyp"]==0) $sql="SELECT * FROM `sou_frac_techs` WHERE $search=0 ORDER BY sort_id ASC";
  	elseif($_SESSION["sou_research_viewtyp"]==1) $sql="SELECT * FROM `sou_frac_techs` WHERE $search>0 ORDER BY sort_id ASC";
  	else $sql="SELECT * FROM `sou_frac_techs` ORDER BY sort_id ASC";
  	
  	$ttid=0;
  	$db_daten=mysql_query($sql,$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      //überprüfen ob die benötigte forschung vorhanden ist
      if(in_array($row["need_tech"], $has_techs) OR $row["need_tech"]==0)
      {
      $hasall=1;
      $i=$row["tech_id"];
  	  //hintergrund bestimmen
      if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

      $output.='<tr class="'.$bg.'" align="left">';
      //forschungsname
      $output.='<td>'.$row["tech_name"].'</td>';
      //voraussetzungen - anfang
      //zuerst schauen ob man evtl. die forschung fertig hat
      $search='f'.$player_fraction.'lvl';
      $ben='';
      if($row[$search]==0)
      {
        //voraussetzungen durchparsen
        if($row["tech_vor"]!='')
        {
          $ben_id=1;
      	  $need=explode(";",$row["tech_vor"]);
          for($n=0;$n<count($need);$n++)
          {
            $einzelneed=explode("x",$need[$n]);
            //es kann verschiedene arten von bedingungen geben
            //gebäude
            if($einzelneed[0][0]=='B')
            {
              $geb_id=str_replace('B', '',$einzelneed[0]);
			  if($geb_id!=1){
					$geb_level=get_bldg_level($owner_id, $geb_id);
			  }else{
				  $geb_level=get_max_frac_bldg_level($geb_id);
			  }
              //textfarbe bestimmen, rot wenn das konstruktionszentraum noch nicht fertig ist
              $font1='<font color="#00FF00">';$font2='</font>';
              if($einzelneed[1]>$geb_level){$font1='<font color="#FF0000">';$font2='</font>';$hasall=0;}
    	      $ben.=$font1.$b_defs[$geb_id][0].' Stufe '.($einzelneed[1]).$font2.'<br>';
            }
    	    //zastari
    	    elseif($einzelneed[0][0]=='Z')
    	    {
        	  $search='f'.$player_fraction.'b'.$ben_id;
    	  	  if($row[$search]<$einzelneed[1]) $ben.='Einlagern: <input type="text" name="b'.$i.'n'.$ben_id.'" size="8" maxlength="10" value=""> -&gt; ';
        	  //farbe bestimmen
        	  $bcolor='#00FF00';
	    	  if($row[$search]<$einzelneed[1]){$bcolor='yellow';$hasall=0;}
	    	  if($row[$search]<$einzelneed[1]*0.66)$bcolor='orange';
	    	  if($row[$search]<$einzelneed[1]*0.33)$bcolor='red';
        	  $ben.='<font color="'.$bcolor.'">'.number_format($row[$search], 0,",",".").' / '.number_format($einzelneed[1], 0,",",".").'</font> <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
	          $ben.='<br>';
	          $ben_id++;
    	    }
    	    //dunkle materie
    	    elseif($einzelneed[0][0]=='D')
    	    {
        	  $search='f'.$player_fraction.'b'.$ben_id;
    	  	  if($row[$search]<$einzelneed[1]) $ben.='Einlagern: <input type="text" name="b'.$i.'n'.$ben_id.'" size="8" maxlength="10" value=""> -&gt; ';
        	  //farbe bestimmen
        	  $bcolor='#00FF00';
	    	  if($row[$search]<$einzelneed[1]){$bcolor='yellow';$hasall=0;}
	    	  if($row[$search]<$einzelneed[1]*0.66)$bcolor='orange';
	    	  if($row[$search]<$einzelneed[1]*0.33)$bcolor='red';
        	  $ben.='<font color="'.$bcolor.'">'.number_format($row[$search], 0,",",".").' / '.number_format($einzelneed[1], 0,",",".").'</font> cm&sup3; <img src="'.$gpfad.'a27.gif" alt="Dunkle Materie" title="Dunkle Materie">';
	          $ben.='<br>';
	          $ben_id++;
    	    }
    	    //credits
    	    elseif($einzelneed[0][0]=='C')
    	    {
        	  $search='f'.$player_fraction.'b'.$ben_id;
    	  	  if($row[$search]<$einzelneed[1]) $ben.='Einlagern: <input type="text" name="b'.$i.'n'.$ben_id.'" size="8" maxlength="10" value=""> -&gt; ';
        	  //farbe bestimmen
        	  $bcolor='#00FF00';
	    	  if($row[$search]<$einzelneed[1]){$bcolor='yellow';$hasall=0;}
	    	  if($row[$search]<$einzelneed[1]*0.66)$bcolor='orange';
	    	  if($row[$search]<$einzelneed[1]*0.33)$bcolor='red';
        	  $ben.='<font color="'.$bcolor.'">'.number_format($row[$search], 0,",",".").' / '.number_format($einzelneed[1], 0,",",".").'</font> <img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits">';
	          $ben.='<br>';
	          $ben_id++;
    	    }    	    
    	    //fraktionsaufgabe
    	    elseif($einzelneed[0][0]=='Q')
    	    {
			  $quest_id=str_replace('Q', '',$einzelneed[0]);
			  if(count($has_quests)>0)
			  {
    	        if(in_array($quest_id, $has_quests)){$bcolor='#00FF00';}else{$bcolor='#FF0000';$hasall=0;}
			  }
			  else
			  {$bcolor='#FF0000';$hasall=0;}
    	      
			  include "soudata/questdata/questname".$quest_id.".php";
			  
			  $ben.='<font color="'.$bcolor.'">Fraktionsaufgabe: '.$questname.'</font><br>';
    	    }
    	    //rohstoffe
    	    else
    	    {
    	      $search='f'.$player_fraction.'b'.$ben_id;
    	  	  if($row[$search]<$einzelneed[0])
    	  	  {
      			
      $ben.='      
      <a id="ttid'.$ttid.'" href="sou_main.php?action=researchpage&do=2&tid='.$i.'&rid='.$ben_id.'&min=15" title="Rohstoffeinlagerung&15 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png" width="16px" height="16px"></a>';
      $ttid++;
      $ben.='
      <a id="ttid'.$ttid.'" href="sou_main.php?action=researchpage&do=2&tid='.$i.'&rid='.$ben_id.'&min=60" title="Rohstoffeinlagerung&60 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png" width="16px" height="16px"></a>';
      $ttid++;
		//abfrage für non-pa accounts
		if($ums_premium!=1){
			$jssecure='onclick="return confirm(\'Diese Aktion kostet Credits. Durchf&uuml;hren?\')"';
		}else{
			$jssecure='';
		}	  
      $ben.='
      <a '.$jssecure.' id="ttid'.$ttid.'" href="sou_main.php?action=researchpage&do=2&tid='.$i.'&rid='.$ben_id.'&min=480" title="Rohstoffeinlagerung&480 Minuten Rohstoffe sammeln<br>Preis: 10 Credits (F&uuml;r Premium-Account-Nutzer kostenlos)">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'abutton2.gif" width="16px" height="16px"></a>';
      $ttid++;
    	  	  }
        	  
    	  	  //farbe bestimmen
        	  $bcolor='#00FF00';
        	  if($row[$search]>$einzelneed[0])$row[$search]=$einzelneed[0];
	    	  if($row[$search]<$einzelneed[0]){$bcolor='yellow';$hasall=0;}
	    	  if($row[$search]<$einzelneed[0]*0.66)$bcolor='orange';
	    	  if($row[$search]<$einzelneed[0]*0.33)$bcolor='red';
        	  $ben.='&nbsp;<font color="'.$bcolor.'">'.number_format($row[$search], 0,",",".").' / '.number_format($einzelneed[0], 0,",",".").'</font>  m&sup3; '.$r_def[$einzelneed[1]][0];
        	  $ben.='<br>';
        	  $ben_id++;
    	    }
    	  }
        }
      
	    //wenn der erfoschen gedrückt wurde die forschung direkt verfügbar machen
	    if($hasall==1 AND $_REQUEST["do"]=="build" AND $_REQUEST["bid"]==($i+1))
	    {
            //upgraden und eingezahlte res löschen
            $f_level='f'.$player_fraction.'lvl';
            $f_b1='f'.$player_fraction.'b1';
            $f_b2='f'.$player_fraction.'b2';
            $f_b3='f'.$player_fraction.'b3';
            $f_b4='f'.$player_fraction.'b4';
            $f_b5='f'.$player_fraction.'b5';
            $f_b6='f'.$player_fraction.'b6';
            $f_b7='f'.$player_fraction.'b7';
            $f_b8='f'.$player_fraction.'b8';
            $f_b9='f'.$player_fraction.'b9';
            $f_b10='f'.$player_fraction.'b10';
            
            mysql_query("UPDATE sou_frac_techs SET $f_level=1, $f_b1=0, $f_b2=0, $f_b3=0, $f_b4=0, $f_b5=0, $f_b6=0, $f_b7=0, $f_b8=0, $f_b9=0, $f_b10=0 WHERE tech_id='$i'", $soudb);
        
            //meldung für den chat hinzufügen
            $text='<font color="#00FF00">Fraktion '.$_SESSION["sou_fraction"].' hat folgende Technologie erforscht: '.$row["tech_name"].'</font>';
      	    $time=time();
      	    insert_chat_msg('^Der Reporter^', $text, 0, 0);
            //mysql_query("INSERT INTO sou_chat_msg (spielername, message, timestamp) VALUES ('^Der Reporter^', '$text', '$time')",$soudb);
			//meldung in der fractionsnews hinterlegen
			$text=$row["tech_name"];
			mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$player_fraction',2, '$text')",$soudb);
        
            header("Location: sou_main.php?action=researchpage");

	    }
     
        //wenn alles vorhanden ist, dann button für den ausbau anzeigen
        if($hasall==1)$ben.='<div align="center"><a href="sou_main.php?action=researchpage&do=build&bid='.($i+1).'"><div class="b1">erforschen</div></a></div>';
      }
      else $ben.='Erforscht.';

      $output.='<td>'.$ben.'</td>';

      //voraussetzungen - ende 
     
      //moduldaten
  	  $moduloutput='';
  	  if($row["needspace"]>0) $moduloutput.='Ben&ouml;tiger Platz: '.number_format($row["needspace"], 0,",",".").' m&sup3;';
  	  if($row["hasspace"]>0)
  	  {
  	    if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Vorhandener Lagerplatz: '.number_format($row["hasspace"], 0,",",".").' m&sup3;';
  	  }
  	  if($row["needenergy"]>0)
      {
  		if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Ben&ouml;tigte Energie: '.number_format($row["needenergy"], 0,",",".").' EE';
  	  }
  	  if($row["giveenergy"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Gelieferte Energie: '.number_format($row["giveenergy"], 0,",",".").' EE';
  	  }  
  	  if($row["canmine"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='F&ouml;rderkapazit&auml;t: '.number_format($row["canmine"], 0,",",".").' m&sup3;/Min bei Standarddichte';
  	  }
  	  
  	  if($row["givehyperdrive"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
  		$speed=calc_hyperdrive_speed($row["givehyperdrive"]);
  		$moduloutput.='&Uuml;berlichtgeschwindigkeit: '.number_format($speed, 0,",",".").' Sek/Lichtjahr';
  	  }
  	  if($row["giveresearch"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Forschungskapazit&auml;t: '.number_format($row["giveresearch"], 0,",",".").' FP';
  	  }
  	  if($row["giveweapon"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
    	$moduloutput.='Waffenkapazit&auml;t: '.number_format($row["giveweapon"], 0,",",".").' EE';
  	  }
  	  if($row["giveshield"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Schildkapazit&auml;t: '.number_format($row["giveshield"], 0,",",".").' EE';
  	  }
  	  if($row["canrecover"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Bergungskapazit&auml;t: '.number_format($row["canrecover"], 0,",",".").' BK';
  	  }
  	  if($row["canclone"]>0)
  	  {
    	if($moduloutput!='')$moduloutput.='<br>';
  		$moduloutput.='Klonkapazit&auml;t: '.number_format($row["canclone"], 0,",",".").' ZQ';
  	  }
  	  
  	  //hier die baukosten für das modul auflisten
  	  //wenn es nichts gibt, dann ist es eine fraktionsforschung
  	  if($moduloutput=='')$moduloutput='Fraktionstechnologie.';else $moduloutput='Moduldaten:<br>'.$moduloutput;
      $output.='<td align="left">'.$moduloutput.'</td>';
      $output.='</td>';
    
      $output.='</tr>';
    }//ende in_array-test auf benötigte forschung
    }
    $output.='</table>';

	$geb_level=get_max_frac_bldg_level(1);
    $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
    <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
    <td><b>Forschungszentrum (Fraktionsstufe '.$geb_level.')</b></td>
    <td width="120">&nbsp;</td>
    </tr></table>';
    rahmen1_oben($routput);
	
    //auswahl der sichtbaren technologien
    echo '<select name="viewtyp" onchange="document.f.submit();">';
    
    for($i=0;$i<count($viewtypes);$i++)
    {
      if($_SESSION["sou_research_viewtyp"]==$i)$selected=' selected'; else $selected='';
      echo '<option value="'.$i.'" '.$selected.'>'.$viewtypes[$i].'</option>';
    
    }
    echo '</select><br>';
        
    echo $output;
  
    echo '<input type="image" src="'.$gpfad.'e.gif" style="width:0; height:0; border:0px;">';

  	rahmen1_unten();
  	echo '</form>';
  	echo '<br>';
  }
  else echo '<br>Hier gibt es kein Forschungszentrum. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br><br>';
}
else echo 'Hier gibt es kein Sonnensystem. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>