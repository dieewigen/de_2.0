<?php
include "soudata/defs/resources.inc.php";
//daten zur ansicht
echo '<br><br>';

echo '<div align="center">';

rahmen0_oben();

//zuerst schauen ob der spieler sich in einem sonnensystem/bei einer sektorraumbasis befindet
$searchx=$player_x;
$searchy=$player_y;
$systemstatus=0;

//test auf sonnensystem
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1){
	$systemstatus=1;
	$row = mysql_fetch_array($db_daten);
	$owner_id=$row["id"];
	$owner_sysname=$row["sysname"];
	$owner_fraction=$row["fraction"];
	$sysprestige[0]=$row["prestige1"];
	$sysprestige[1]=$row["prestige2"];
	$sysprestige[2]=$row["prestige3"];
	$sysprestige[3]=$row["prestige4"];
	$sysprestige[4]=$row["prestige5"];
	$sysprestige[5]=$row["prestige6"];
	$pirates=$row['pirates'];
}else{
	//test auf sektorraumbasis
	$db_daten=mysql_query("SELECT * FROM sou_map_base WHERE special=0 AND x='$searchx' AND y='$searchy'",$soudb);
	$num = mysql_num_rows($db_daten);
	if($num==1){
		
		$systemstatus=2;
		$row = mysql_fetch_array($db_daten);
		$owner_id=$row["id"];
		$owner_sysname='Sektorraumbasis';
		$owner_fraction=$row["fraction"];
		$sysprestige[0]=$row["prestige1"];
		$sysprestige[1]=$row["prestige2"];
		$sysprestige[2]=$row["prestige3"];
		$sysprestige[3]=$row["prestige4"];
		$sysprestige[4]=$row["prestige5"];
		$sysprestige[5]=$row["prestige6"];
		$pirates=0;
	}
}

if($systemstatus==1 OR $systemstatus==2){
	//test auf piraten
	if($pirates>0){
		//stufe der piraten bestimmen
		// die entfernung zum zentrum berechnen
		$radius=sqrt(($player_x*$player_x)+($player_y*$player_y));
		if($radius>1250){
			$pirateslevel=0;
		}else{
			$pirateslevel=51-ceil($radius/50*2);
		}

		if($pirateslevel>0){
			rahmen2_oben();
			echo 'Dieses Sonnensystem wird von Piraten belagert. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
			rahmen2_unten();
			echo '<br>';

			echo '</div>';//center-div
			die('</body></html>');
		}
	}
  

	if($owner_fraction!=0) {
	//bao-nada-status auslesen
	$fraktionswerte=get_baonadaskala();

	//�berpr�fen ob ein schutzsiegel aktiv ist
	if($systemstatus==1){
		$db_daten=mysql_query("SELECT * FROM `sou_map_buffs` WHERE owner_id='$owner_id' AND time>'$time' AND typ=1",$soudb);
		$isprotected = mysql_num_rows($db_daten);
	}
		

  //man m�chte rohstoffe spenden
  if($_REQUEST["do"]=='1' OR $_REQUEST["do"]=='2')
  {
    //spendendauer
    $min=intval($_REQUEST["min"]);
    if($min<15 || $min>480)$min=15;
  
    $zielfraktion=intval($_REQUEST["f"]);
  	// auf passenden nachbarsektor �berpr�fen
	$sbx=round($player_x/15);
  	$sby=round($player_y/15);
	if(get_sector_owner($sbx, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==$player_fraction OR get_sector_owner($sbx+1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby-1)==$player_fraction OR get_sector_owner($sbx-1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==999 OR get_sector_owner($sbx+1, $sby)==999 OR get_sector_owner($sbx, $sby-1)==999 OR get_sector_owner($sbx-1, $sby)==999 OR $owner_fraction==$player_fraction)
	{
  	  //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
  	    //rohstoffart auf eisen festlegen
  	    $rid=0;
  	  
  	    //�berpr�fen ob man freien laderaum hat
  	    $freehold=1;//get_sum_hold($_SESSION["sou_user_id"]);
  	    if($freehold>0)
  	    {
   	  	  //�berpr�fen ob man ein mining-modul hat
  	  	  $canmine=get_canmine($_SESSION["sou_user_id"]);
  	  	  if($canmine>0)
  	  	  {
  	  	    //�berpr�fen ob man genug credits hat
			$needcredits=0;
  	  	    if($min>60)$needcredits=10;
  	  	    if($ums_premium==1)$needcredits=0;
  	  	    //bei spendenart 1 entstehen keine creditkosten
  	  	    //if($_REQUEST["do"]==1)$needcredits=0;
            $has_credits=has_credits($ums_user_id);
  	  	    if($has_credits>=$needcredits){
				
			  //schutzsiegel �berpr�fen
			  $allok=1;
  	  	      if($isprotected>0)
  	  	      {
  	  	      	if($owner_fraction!=$player_fraction)$allok=0;
  	  	      }
  	  	      
  	  	      if($allok==1){
  	  	        //berechnen wieviel rohstoffe man pro minute bekommen kann
          	    $getres=round($canmine/$r_def[$rid][1])*(1+(get_skill($rid)/500000));
          	  	//�berpr�fen ob das alles in den lagerraum pa�t
          	  	//if($getres>$freehold)$getres=$freehold;
          	  
          	  	$getres=$getres*$min;
				change_skill($rid, $min);

          	  	//ertrag ist in abh�ngigkeit der bao-nada-skala beim ansehen anderer fraktionen
          	  	$eigene_fraktion_bao=$fraktionswerte[$player_fraction-1];
          	  	$zielfraktion_bao=$fraktionswerte[$zielfraktion-1];
          	  
          	  	if($zielfraktion!=$player_fraction)//man greift eine andere fraktion an,bao-nada-skala beachten
          	  	{
          	      $prestige=round($getres+($getres*($zielfraktion_bao-$eigene_fraktion_bao)/100));
          	      //echo $prestige;
          	  	}
          	  	else //man lagert bei sich selbst ein, kein bonus/malus
          	  	{
          	  	  $prestige=round($getres);
          	  	  //echo $prestige;
          	  	}
          	  
				//wenn es nicht die eigene fraktion ist, dann ist der prestigewert negativ und wird dem gegner abgezogen
				$do_prestige_update=true;
          	  	if($zielfraktion!=$player_fraction){

					$prestige=$prestige*(-1);
					if($prestige>0){
						$do_prestige_update=false;
					}
				}
          	  
          	  	//echo '<br><br>'.$getres.' : '.$prestige;
          	
  			  	//credits abziehen
  			  	if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Prestige-Komfortfunktion');

				//die prestige�nderung durchf�hren
				if($do_prestige_update){
					$feldname='prestige'.$zielfraktion;
					if($systemstatus==1){
						$sql="UPDATE `sou_map` SET $feldname=$feldname+'$prestige' WHERE id='$owner_id'";
						mysql_query($sql,$soudb);
					}
					elseif($systemstatus==2){
						$sql="UPDATE `sou_map_base` SET $feldname=$feldname+'$prestige' WHERE id='$owner_id'";
						mysql_query($sql,$soudb);
					}
				}
				
				$sysprestige[$zielfraktion-1]+=$prestige;
				if($sysprestige[$zielfraktion-1]<0)$sysprestige[$zielfraktion-1]=0;
				
				//je nach position den agressionswert erh�hen, das sonnensystem "geh�rt" zu der fraktion wo es am nahesten dran ist
				//gilt nur im kernbereich und nicht in den fernen st�tten
				if($player_x>-2250 && $player_x<2250 && $player_y>-2250 && $player_y<2250){
					//alle startpositionen durchgehen um festzustellen wohin das system geh�rt
					$distance_owner_fraction=-1;
					$min_distance=999999;
					for($i=0;$i<6;$i++){
						//echo '<br>'.($i+1).': ';
						//echo sqrt(pow($player_x-$sv_sou_startposition[$i][0], 2)+pow($player_y-$sv_sou_startposition[$i][1], 2));
						$distance=sqrt(pow($player_x-$sv_sou_startposition[$i][0], 2)+pow($player_y-$sv_sou_startposition[$i][1], 2));
						if($distance<$min_distance){
							$min_distance=$distance;
							$distance_owner_fraction=$i+1;
						}
					}

					//echo '<br>OWNER: '.$distance_owner_fraction;
					//test ob ein system evtl. auf der grenze liegt, dann "geh�rt" es niemandem
					if($distance_owner_fraction!=-1){
						for($i=0;$i<6;$i++){
							//nur die anderen fraktionen vergleichen, sonst w�re es immer gleich
							if($distance_owner_fraction-1 != $i){
								$distance=sqrt(pow($player_x-$sv_sou_startposition[$i][0], 2)+pow($player_y-$sv_sou_startposition[$i][1], 2));
								//wenn es zwei gleiche gibt, dann ist er auf der grenze
								if($distance==$min_distance){
									$distance_owner_fraction=-1;
								}
							}
						}					
					}		
					
					//�berpr�fen ob man im "feindlichen" gebiet ist
					if($player_fraction!=$distance_owner_fraction){
						//nur beim eigenen ansehen und bei dem vom "owner" wirksam
						if($zielfraktion==$player_fraction || $zielfraktion==$distance_owner){
							//a angreifer ziel
							$fieldname='a'.$player_fraction.$distance_owner_fraction;
							mysql_query("UPDATE `sou_system` SET $fieldname=$fieldname+'$min'",$soudb);
						}
					}
				}

			  	//wenn das system jemand anderem gehört, dann das angriffs-flag setzen und überprüfen ob das system evtl. den eigentümer wechselt
			  	if($owner_fraction!=$player_fraction){
					//underattack
					$time=time();
					if($systemstatus==1)mysql_query("UPDATE `sou_map` SET underattack='$time' WHERE id='$owner_id'",$soudb);
					elseif($systemstatus==2)mysql_query("UPDATE `sou_map_base` SET underattack='$time' WHERE id='$owner_id'",$soudb);
					
					//feststellen, ob das system den eigent�mer wechselt
					$prestigeleader=$owner_fraction;
					$maxprestige=$sysprestige[$owner_fraction-1];
					for($i=0;$i<=5;$i++){
						if($sysprestige[$i]>$sysprestige[$owner_fraction-1] && $sysprestige[$i]>$maxprestige){
							$prestigeleader=$i+1;
							$maxprestige=$sysprestige[$i];
						}
					}
					
					if($prestigeleader!=$owner_fraction){
						
						if($systemstatus==1){
							//owner wechseln
							mysql_query("UPDATE `sou_map` SET fraction='$prestigeleader' WHERE id='$owner_id'",$soudb);
							//info f�r den chat
							$text='<font color="#FF0000">F'.$owner_fraction.' hat ein Sonnensystem an F'.$prestigeleader.' verloren. Koordinaten: '.$searchx.':'.$searchy.'</font>';
							insert_chat_msg('^Der Reporter^', $text, 0, 0);
							
							//meldung in der fractionsnews hinterlegen
							$text=$owner_sysname.': F'.$owner_fraction.' hat ein Sonnensystem an F'.$prestigeleader.' verloren.';
							mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$owner_fraction',6, '$text')",$soudb);
							
						}elseif($systemstatus==2){
							//owner wechseln
							mysql_query("UPDATE `sou_map_base` SET fraction='$prestigeleader' WHERE id='$owner_id'",$soudb);
							$text='<font color="#FF0000">F'.$owner_fraction.' hat eine Sektorraumbasis an F'.$prestigeleader.' verloren. Koordinaten: '.$searchx.':'.$searchy.'</font>';
							insert_chat_msg('^Der Reporter^', $text, 0, 0);							
						}
					}
			  	}
			  
			  	//dem spieler dieses als spende hinterlegen
			  	if($prestige<0)$prestige=$prestige*(-1);
			  	mysql_query("UPDATE `sou_user_data` SET donate=donate+'$prestige' WHERE user_id='$player_user_id'",$soudb);
  			   			
          	  	//einen counter setzen, damit sonst nichts mehr zu machen geht
          	  	$time=time()+60*$min;
          	  
          	  	mysql_query("UPDATE sou_user_data SET atimer1typ=6, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
			  	//seite neu laden
          	  	header("Location: sou_main.php");
  	  	      }
  	  	      else $msg='<font color="#FF0000">Dieses Sonnensystem ist gesch&uuml;tzt.</font>';
  	  	    }
  	  	    else $msg='<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font>';
  	      }
  	      else $msg='<font color="#FF0000">Es ist kein Bergbaumodul vorhanden.</font>';
  	    }
	    else $msg='<font color="#FF0000">Es ist kein freier Frachtraum vorhanden.</font>';

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
    else $msg='<font color="#FF0000">Du ben&ouml;tigst einen Nachbar-/Sektor deiner Fraktion, oder einen der ERBAUER.</font>';
  }
	
  	//men� darstellen

	if($msg!='')
	{
  	  echo '<br>';
	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
    }
	
	echo '<br>';
	
    $output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
    <td width="120"><a href="sou_main.php?action=systempage" class="btn">System</a></td>
    <td><b>Fraktionsansehen</b></td>
    <td width="120">&nbsp;</td>
    </tr></table>';
    rahmen1_oben($output);  	

  	echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';

  	$bg='cell';
	echo '<tr class="'.$bg.'"><td colspan="4" align="left">
	Hier kannst du Rohstoffe spenden, um das Ansehen deiner Fraktion zu vergr&ouml;&szlig;ern, oder einer anderen Fraktion zu verringern. Gespendet wird immer Eisen, welches bei dieser Spendenaktion im Asteroidenfeld geborgen werden darf. Diese Aktion ist nur m&ouml;glich, wenn das Sonnensystem/Sektorraumbasis deiner Fraktion geh&ouml;rt, oder ein benachbarter Sektor der Fraktion geh&ouml;rt, bzw. der Sektor neutral ist. Der Spendeneinflu&szlig; h&auml;ngt von der Bao-Nada-Skala ab und beg&uuml;nstigt die schw&auml;cheren Fraktionen. Die Fraktion, die das gr&ouml;&szlig;te Ansehen hat, der geh&ouml;rt das Sonnensystem/die Sektorraumbasis.</td></tr>';
	
	$bg='cell1';
	echo '<tr class="'.$bg.'" align="center"><td><b>Fraktion</b></td><td><b>Ansehen</b></td><td><b>Bao-Nada-Skala</b></td><td><b>Aktion</b></td></tr>';
	
	
	
	$ttid=0;$c1=1;
	for($i=1;$i<=6;$i++)
	{
	  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
	  echo '<tr class="'.$bg.'" align="center">';
	  
	  echo '<td><font color="#'.$colors_text[$i-1].'">'.$i.'</font></td>';
	  echo '<td>'.number_format($sysprestige[$i-1], 0,",",".").'</td>';
	  echo '<td>'.number_format($fraktionswerte[$i-1], 2,",",".").'%</td>';
	  
	  if($sysprestige[$i-1]>0 OR $i==$player_fraction)
	  {
	    //bonus malus berechnen und anzeigen
	    $bonmal='<br><br>';
	    if($i==$player_fraction)$bonmal.='Bei deiner eigenen Fraktion hast du weder einen Bonus, noch einen Malus.';
	    else
	    {
	      $unterschied=$fraktionswerte[$i-1]-$fraktionswerte[$player_fraction-1];
	      if($unterschied<0)$bonmal.='Malus: '.number_format(($unterschied*-1), 2,",",".").'%';
	      else $bonmal.='Bonus: '.number_format($unterschied, 2,",",".").'%';
	    }
	  
	    echo '<td>';
	    echo '<a href="sou_main.php?action=systemprestigepage&do=2&min=15&f='.$i.'" title="Information&Spendet Eisen um Einflu� auf das Ansehen zu nehmen. Das Raumschiff fliegt 15 Mal in das Asteriodenfeld und sammelt dort jeweils 1 Minute Rohstoffe und spendet diese dann.'.$bonmal.'">
	    <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png" width="16px" height="16px"></a>';
	    $ttid++;
	    echo '<a style="margin-left: 10px;" href="sou_main.php?action=systemprestigepage&do=2&min=60&f='.$i.'" title="Information&Spendet Eisen um Einflu&szlig; auf das Ansehen zu nehmen. Das Raumschiff fliegt 60 Mal in das Asteriodenfeld und sammelt dort jeweils 1 Minute Rohstoffe und spendet diese dann.'.$bonmal.'">
	    <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png" width="16px" height="16px"></a>';
	    $ttid++;
		echo '<a style="margin-left: 20px;" href="sou_main.php?action=systemprestigepage&do=2&min=480&f='.$i.'" title="Informationen&480 Fl&uuml;ge<br>Preis: 10 Credits (F&uuml;r Premium-Account-Nutzer kostenlos)">
        <img border="0" style="vertical-align: middle;" src="'.$gpfad.'abutton2.gif"  width="16px" height="16px"></a>&nbsp;';
		$ttid++;
	    
	    echo '</td>';
	  }
	  else echo '<td>-</td>';
	  
	  echo '</tr>';
	  
	}

	echo '</td></tr></table>';

  	rahmen1_unten();
  	echo '<br>';
  	
  echo '<script language="JavaScript" type="text/javascript">';
  

  
  echo '</script>';    	
  
  }
  else echo 'Dieses Sonnensystem geh&ouml;rt keiner Fraktion, das Ansehen kann daher nicht beeinflu&szlig;t werden. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';
}
else echo 'Hier gibt es kein Sonnensystem und keine Sektorraumbasis. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>