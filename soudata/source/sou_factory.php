<?php
include "soudata/defs/buildings.inc.php";
include "soudata/defs/resources.inc.php";

//auswahl der anzeige
$viewtypes[]='Bergbau';
$viewtypes[]='Forschung';
//$viewtypes[]='Lager';
$viewtypes[]='Raumschiffzentrale';
$viewtypes[]='Unterlichtantrieb';
$viewtypes[]='&Uuml;berlichtantrieb';
$viewtypes[]='Waffen';
$viewtypes[]='Schilde';
$viewtypes[]='Bergung';
$viewtypes[]='Klonmodul';

if(isset($_REQUEST["viewtyp"]))
{
  $_SESSION["sou_factory_viewtyp"]=intval($_REQUEST["viewtyp"]);
}

if($_SESSION["sou_factory_viewtyp"]=='' OR $_SESSION["sou_factory_viewtyp"]<0 OR $_SESSION["sou_factory_viewtyp"]>8)$_SESSION["sou_factory_viewtyp"]=0;

$searchvalue='needspace';

if($_SESSION["sou_factory_viewtyp"]==0)
{
  $searchvalue='canmine';
}
elseif($_SESSION["sou_factory_viewtyp"]==1)
{
  $searchvalue='giveresearch';
}
/*
elseif($_SESSION["sou_factory_viewtyp"]==2)
{
  $searchvalue='hasspace';
}
*/
elseif($_SESSION["sou_factory_viewtyp"]==2)
{
  $searchvalue='givelife';
}
elseif($_SESSION["sou_factory_viewtyp"]==3)
{
  $searchvalue='givesubspace';
}
elseif($_SESSION["sou_factory_viewtyp"]==4)
{
  $searchvalue='givehyperdrive';
}
elseif($_SESSION["sou_factory_viewtyp"]==5)
{
  $searchvalue='giveweapon';
}
elseif($_SESSION["sou_factory_viewtyp"]==6)
{
  $searchvalue='giveshield';
}
elseif($_SESSION["sou_factory_viewtyp"]==7)
{
  $searchvalue='canrecover';
}
elseif($_SESSION["sou_factory_viewtyp"]==8)
{
  $searchvalue='canclone';
}


//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

  //gebäudelevel auslesen
  $geb_level[6]=get_max_frac_bldg_level(6);
  $geb_level[7]=get_max_frac_bldg_level(7);
  $geb_level[8]=get_max_frac_bldg_level(8);
  $geb_level[9]=get_max_frac_bldg_level(9);

  //alle rohstoffe im lagerkomplex auslesen
  //$db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$_SESSION[sou_user_id]' AND owner_id='$owner_id' ORDER BY res_id",$soudb);
  $db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$_SESSION[sou_user_id]' ORDER BY res_id",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    $complex_has[$row["res_id"]]=$row["amount"];
  }
  
  //alle forschungsupdates auslesen
  $db_daten=mysql_query("SELECT * FROM `sou_user_tech_updates` WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    $resupdate_has[$row["tech_id"]]["hasspace"]=$row["hasspace"];
    $resupdate_has[$row["tech_id"]]["canmine"]=$row["canmine"];
    $resupdate_has[$row["tech_id"]]["giveweapon"]=$row["giveweapon"];
    $resupdate_has[$row["tech_id"]]["giveshield"]=$row["giveshield"];
    $resupdate_has[$row["tech_id"]]["canrecover"]=$row["canrecover"];
    $resupdate_has[$row["tech_id"]]["canclone"]=$row["canclone"];
  }
  
  //feststellen welche fabrik verwendet werden soll
  /*
  $b=intval($_REQUEST["b"]);
  if($b<1 OR $b>4)$b=1;
  if($b==1)$bid=6;
  elseif($b==2)$bid=7;
  elseif($b==3)$bid=8;
  elseif($b==4)$bid=9;
  */
  
 
    //transaktionsbeginn
    if (setLock($_SESSION["sou_user_id"]))
    {
 	
  	//techs darstellen
	echo '<br>';

  	$output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  	<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  	<td><b>Modulfabriken</b></td>
  	<td width="120"><a href="sou_main.php?action=systemholdpage"><div class="b1">Lagerkomplex</div></a> </td>
  	</tr></table>';
  	rahmen1_oben($output);

  	echo '<form action="sou_main.php" method="POST" name="f">';
	echo '<input type="hidden" name="action" value="factorypage">';
	echo '<input type="hidden" name="do" value="1">';

	
    //auswahl der sichtbaren module
    echo '<select name="viewtyp" onchange="document.f.submit();">';
    
    for($i=0;$i<count($viewtypes);$i++)
    {
      if($_SESSION["sou_factory_viewtyp"]==$i)$selected=' selected'; else $selected='';
      echo '<option value="'.$i.'" '.$selected.'>'.$viewtypes[$i].'</option>';
    
    }
    echo '</select><br>';
	
	
    //techs anzeigen

  	$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
  	$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  	$feldname='f'.$player_fraction.'lvl';
  	$output.='<tr align="center" class="'.$bg.'"><td><b>Technologie</b></td><td><b>Baukosten</b><td><b>Moduldaten</b></tr>';
  	$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE $searchvalue>0 AND $feldname>0 AND modulcost<>'' ORDER BY $searchvalue DESC",$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      $hasall=1;
      $i=$row["tech_id"];
  	  //hintergrund bestimmen
      if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

      $output.='<tr class="'.$bg.'" align="left">';
      //forschungsname
      $output.='<td>'.$row["tech_name"].'</td>';
      //voraussetzungen - anfang
      $ben='';
      //überprüfen, ob das gebäude eine ausreichende stufe hat
      if($geb_level[$row['bldg_build']+5]<$row["bldg_level"])
      {
        $gebname=$b_defs[$row['bldg_build']+5][0];
      	$ben.='<font color="#FF0000">Ab '.$gebname.' Stufe '.$row["bldg_level"].'</font><br>';
      	$hasall=0;
      }

      
        
		//baukosten durchparsen
        if($row["modulcost"]!='')
        {
      	  $need=explode(";",$row["modulcost"]);
      	  //kosten zurücksetzen
      	  $zastari=0;
      	  for($k=0;$k<count($r_def);$k++)$cost[$k]=0;
      	  for($k=0;$k<count($r_def);$k++)$cost[$k]=0;
      	  
          for($n=0;$n<count($need);$n++)
          {
            $einzelneed=explode("x",$need[$n]);
            //es kann verschiedene arten von kosten geben
    	    //zastari
    	    
    	    if($einzelneed[0][0]=='Z')
    	    {
        	  //farbe bestimmen
        	  $bcolor='#00FF00';
	    	  if($player_money<$einzelneed[1]){$bcolor='yellow';$hasall=0;}
	    	  if($player_money<$einzelneed[1]*0.66)$bcolor='orange';
	    	  if($player_money<$einzelneed[1]*0.33)$bcolor='red';
        	  $ben.='<font color="'.$bcolor.'">'.number_format($player_money, 0,",",".").' / '.number_format($einzelneed[1], 0,",",".").'</font>  Zastari';
	          $ben.='<br>';
	          
	          //kosten speichern
	          $zastari=$einzelneed[1]*-1;
    	    }
    	    //rohstoffe
    	    else
    	    {
    	      //farbe bestimmen
        	  $bcolor='#00FF00';
	    	  if($complex_has[$einzelneed[1]]<$einzelneed[0]){$bcolor='yellow';$hasall=0;}
	    	  if($complex_has[$einzelneed[1]]<$einzelneed[0]*0.66)$bcolor='orange';
	    	  if($complex_has[$einzelneed[1]]<$einzelneed[0]*0.33)$bcolor='red';
        	  $ben.='<font color="'.$bcolor.'">'.number_format($complex_has[$einzelneed[1]], 0,",",".").' / '.number_format($einzelneed[0], 0,",",".").'</font>  m&sup3; '.$r_def[$einzelneed[1]][0];
        	  $ben.='<br>';
        	  
        	  //kosten speichern
        	  $cost[$einzelneed[1]]=$einzelneed[0]*-1;
    	    }
    	  }
        }
      
	    //wenn der bauen button gedrückt wurde das modul bauen
	    if($hasall==1 AND $_REQUEST["do"]=="build" AND $_REQUEST["tid"]==$i)
	    {
            
          //überprüfen ob genug platz im modulkomplex ist
          //zuerst die dortigen hinterlegen module zählen
		  $db_datenx=mysql_query("SELECT id FROM sou_ship_module WHERE user_id='$player_user_id' AND location=1",$soudb);
		  $num_lager = mysql_num_rows($db_datenx);
			
		  //feststellen wieviel ins lager geht
		  //$max_lager=get_bldg_level($owner_id, 5)*2;
		  $max_lager=get_max_frac_bldg_level(5)*2;
            
          if($num_lager < $max_lager)
          {
            //wenn genug platz vorhanden ist, dann rohstoffe/geld abziehen
            //zastari
            change_money($player_user_id, $zastari);

            //rohstoffe
            for($i=0;$i<count($r_def);$i++)
            {
              if($cost[$i]<0) change_systemhold_amount($player_user_id, $i, $cost[$i]);
            }
              
            //und dann das modul im lagerkomplex hinterlegen
            $tech_name=$row["tech_name"];
            $needspace=$row["needspace"];
            $hasspace=$row["hasspace"];
            $needenergy=$row["needenergy"];
            $giveenergy=$row["giveenergy"];
            $canmine=$row["canmine"];
            $givelife=$row["givelife"];
            $givesubspace=$row["givesubspace"];
            $givecenter=$row["givecenter"];
            $givehyperdrive=$row["givehyperdrive"];
            $giveresearch=$row["giveresearch"];
            $giveweapon=$row["giveweapon"];
            $giveshield=$row["giveshield"];
            $canrecover=$row["canrecover"];
            $canclone=$row["canclone"];
            
            //auf forschungsupdates checken
            $updates=0;

  			if($resupdate_has[$row["tech_id"]]["hasspace"]>0)
  			{
  		  	  $hasspace=round($row["hasspace"]+$row["hasspace"]/100*$resupdate_has[$row["tech_id"]]["hasspace"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["hasspace"];
  			}
            
  			if($resupdate_has[$row["tech_id"]]["canmine"]>0)
  			{
  		  	  $canmine=round($row["canmine"]+$row["canmine"]/100*$resupdate_has[$row["tech_id"]]["canmine"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["canmine"];
  			}

  			if($resupdate_has[$row["tech_id"]]["giveweapon"]>0)
  			{
  		  	  $giveweapon=round($row["giveweapon"]+$row["giveweapon"]/100*$resupdate_has[$row["tech_id"]]["giveweapon"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["giveweapon"];
  			}
  			
  			if($resupdate_has[$row["tech_id"]]["giveshield"]>0)
  			{
  		  	  $giveshield=round($row["giveshield"]+$row["giveshield"]/100*$resupdate_has[$row["tech_id"]]["giveshield"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["giveshield"];
  			}
  			
  			if($resupdate_has[$row["tech_id"]]["canrecover"]>0)
  			{
  		  	  $canrecover=round($row["canrecover"]+$row["canrecover"]/100*$resupdate_has[$row["tech_id"]]["canrecover"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["canrecover"];
  			}

            if($resupdate_has[$row["tech_id"]]["canclone"]>0)
  			{
  		  	  $canrecover=round($row["canclone"]+$row["canclone"]/100*$resupdate_has[$row["tech_id"]]["canclone"]);
  		  	  $updates+=$resupdate_has[$row["tech_id"]]["canclone"];
  			}  			
  			
  			//die updates im namen hinterlegen
  			if($updates>0)
  			{
  			  $tech_name.=' ('.$updates.' FU)';
  			}
          
            
            $sql="INSERT INTO sou_ship_module (user_id, fraction, name, craftedby, hasspace, needspace, needenergy, giveenergy, canmine, 
            givelife, givesubspace, givecenter, givehyperdrive, giveresearch, giveweapon, giveshield, canrecover, canclone, location) VALUES 
            ('$player_user_id', '$player_fraction', '$tech_name', '$player_name', '$hasspace', '$needspace', '$needenergy', '$giveenergy', '$canmine', 
            '$givelife', '$givesubspace', '$givecenter', '$givehyperdrive', '$giveresearch', '$giveweapon','$giveshield', '$canrecover', '$canclone', 1)";
            mysql_query($sql,$soudb);
              
            //msg ausgeben
            echo '<font color="#00FF00"><br>Das Modul wurde im Modulkomplex hinterlegt.</font><br><br>
            <a href="sou_main.php?action=factorypage&do=build&tid='.$_REQUEST['tid'].'"><div class="b1">erneut bauen</div></a>&nbsp;
            <a href="sou_main.php?action=factorypage"><div class="b1">Fabrik</div></a>&nbsp;
            <a href="sou_main.php?action=modulholdpage"><div class="b1">Modulkomplex</div></a><br>';
            $dontshow=1;
          }
	      else echo '<font color="#FF0000">Es ist nicht genug Platz im Modulkomplex vorhanden.</font><br>';
	    }
     
        //wenn alles vorhanden ist, dann button für den ausbau anzeigen
        if($hasall==1)$ben.='<div align="center"><a href="sou_main.php?action=factorypage&b='.$b.'&do=build&tid='.($i).'"><div class="b1">bauen</div></a></div>';


      $output.='<td>'.$ben.'</td>';

      //voraussetzungen - ende 
     
      //moduldaten
  	  $moduloutput='';
  	  if($row["hasspace"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["hasspace"];
  		if($resupdate_has[$row["tech_id"]]["hasspace"]>0)
  		{
  		  $wert=round($row["hasspace"]+$row["hasspace"]/100*$resupdate_has[$row["tech_id"]]["hasspace"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["hasspace"].' FU)';
  		}
  		$moduloutput.='Lagerkapazit&auml;t: '.number_format($wert, 0,",",".").' m&sup3;'.$fu;
  	  }
  	  if($row["canmine"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["canmine"];
  		if($resupdate_has[$row["tech_id"]]["canmine"]>0)
  		{
  		  $wert=round($row["canmine"]+$row["canmine"]/100*$resupdate_has[$row["tech_id"]]["canmine"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["canmine"].' FU)';
  		}
  		$moduloutput.='F&ouml;rderkapazit&auml;t: '.number_format($wert, 0,",",".").' m&sup3;/Min bei Standarddichte'.$fu;
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
  	  //hier die baukosten für das modul auflisten
  	  if($row["giveweapon"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["giveweapon"];
  		if($resupdate_has[$row["tech_id"]]["giveweapon"]>0)
  		{
  		  $wert=round($row["giveweapon"]+$row["giveweapon"]/100*$resupdate_has[$row["tech_id"]]["giveweapon"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["giveweapon"].' FU)';
  		}
  		$moduloutput.='Waffenkapazit&auml;t: '.number_format($wert, 0,",",".").' EE'.$fu;
  	  }  
  	  if($row["giveshield"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["giveshield"];
  		if($resupdate_has[$row["tech_id"]]["giveshield"]>0)
  		{
  		  $wert=round($row["giveshield"]+$row["giveshield"]/100*$resupdate_has[$row["tech_id"]]["giveshield"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["giveshield"].' FU)';
  		}
  		$moduloutput.='Schildkapazit&auml;t: '.number_format($wert, 0,",",".").' EE'.$fu;
  	  }   	  
  	  if($row["canrecover"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["canrecover"];
  		if($resupdate_has[$row["tech_id"]]["canrecover"]>0)
  		{
  		  $wert=round($row["canrecover"]+$row["canrecover"]/100*$resupdate_has[$row["tech_id"]]["canrecover"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["canrecover"].' FU)';
  		}
  		$moduloutput.='Bergungskapazit&auml;t: '.number_format($wert, 0,",",".").' BK'.$fu;
  	  }
      if($row["canclone"]>0)
  	  {
  		if($moduloutput!='')$moduloutput.='<br>';
		$fu='';$wert=$row["canclone"];
  		if($resupdate_has[$row["tech_id"]]["canclone"]>0)
  		{
  		  $wert=round($row["canclone"]+$row["canclone"]/100*$resupdate_has[$row["tech_id"]]["canclone"]);
  		  $fu=' ('.$resupdate_has[$row["tech_id"]]["canclone"].' FU)';
  		}
  		$moduloutput.='Klonkapazit&auml;t: '.number_format($wert, 0,",",".").' ZQ'.$fu;
  	  }  	  
      $output.='<td align="left">'.$moduloutput.'</td>';
      $output.='</td>';
    
      $output.='</tr>';
    }

    $output.='</table>';

    if($dontshow!=1) echo $output;
  
    echo '<input type="image" src="'.$gpfad.'e.gif" style="width:0; height=0; border:0px;">';
    echo '</form>';

  	rahmen1_unten();
  	echo '<br>';
  	
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

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>