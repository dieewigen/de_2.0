<?php
include "soudata/defs/buildings.inc.php";
include_once "soudata/defs/resources.inc.php";

//upgradefaktor für die verbesserung
$upgradefaktor=1.0065;

//daten zur ansicht
echo '<br>';

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
  
  //überprüfen ob das sonnenystem zur eigenen fraktion gehört
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
  
  //überprüfen ob es das gebäude gibt
  $geb_level=get_bldg_level($owner_id, 12);
  if($geb_level>0 OR $numsrb==1)
  {
  	$geb_level=get_max_frac_bldg_level(12);
  	//mögliche updates auf der gebäudestufe
  	$maxupgrades=$geb_level*3;
  	
	//auswahl der anzeige
	//$viewtypes[]='Verbesserung: Energieverbrauch';
	//$viewtypes[]='Verbesserung: Energieerzeugung';
	$viewtypes[]='Verbesserung: Bergbauf&ouml;rderkapazit&auml;t';
	//$viewtypes[]='Verbesserung: Lagerkapazit&auml;t';
	$viewtypes[]='Verbesserung: Waffenkapazit&auml;t';
	$viewtypes[]='Verbesserung: Schildkapazit&auml;t';
	$viewtypes[]='Verbesserung: Bergungskapazit&auml;t';
	if(isset($_REQUEST["viewtyp"]))
	{
  	  $_SESSION["sou_upgradeomodul_viewtyp"]=intval($_REQUEST["viewtyp"]);
	}
	if($_SESSION["sou_upgradeomodul_viewtyp"]=='' OR $_SESSION["sou_upgradeomodul_viewtyp"]<0 OR $_SESSION["sou_upgradeomodul_viewtyp"]>3)$_SESSION["sou_upgradeomodul_viewtyp"]=0;
	

  	if($_SESSION["sou_upgradeomodul_viewtyp"]==0)
  	{
  	  $updatefeld='canmine';
  	  $updatefelduom='canmineuom';
  	}
  	/*
  	elseif($_SESSION["sou_upgradeomodul_viewtyp"]==1)
  	{
  	  $updatefeld='hasspace';
  	  $updatefelduom='hasspaceuom';
    }*/
  	elseif($_SESSION["sou_upgradeomodul_viewtyp"]==1)
  	{
  	  $updatefeld='giveweapon';
  	  $updatefelduom='giveweaponuom';
    }
  	elseif($_SESSION["sou_upgradeomodul_viewtyp"]==2)
  	{
  	  $updatefeld='giveshield';
  	  $updatefelduom='giveshielduom';
    }
  	elseif($_SESSION["sou_upgradeomodul_viewtyp"]==3)
  	{
  	  $updatefeld='canrecover';
  	  $updatefelduom='canrecoveruom';
    }
    
    
    //wenn ein submit gekommen ist, dann soll das modul aufgewertet werden
    if($_REQUEST["do"]=='1')
    {
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
      	//modulid checken
      	$id=intval($_REQUEST["id"]);
      	
      	//zuerst schauen ob man das modul im schiff, oder im modulkomplex hat und ob es einem gehört
      	$sql="SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) AND id='$id' AND uomlock=0";
    	$db_daten=mysql_query($sql,$soudb);
    	$num=mysql_num_rows($db_daten);
    	if($num==1)
    	{
    	  //moduldaten auslesen
    	  $row = mysql_fetch_array($db_daten);
    	  $quality=$row['quality'];
    	  if($quality<1)$quality=1;
    	  
    	  //überprüfen wieviele updates es schon hat und ob noch eins möglich ist
  	  	  
  	  	  $istwert=$row[$updatefeld];
  	  	  $istwertneu=ceil($istwert*$upgradefaktor);
  	  	  $hasupdates=$row[$updatefelduom];  	  	  

  	  	  if($hasupdates<$maxupgrades)
  	  	  {
    	    //überprüfen ob er genug specialres hat
    	    $upgradekosten=1;
    	    $specialres=has_specialres($player_user_id, floor($hasupdates/15));
    	    if($specialres>=$upgradekosten)
    	    {
    	      //specialres abziehen
    	      change_specialres($player_user_id, floor($hasupdates/15), $upgradekosten*-1);  
    	    
    	      //alles ok, also das modul aufwerten
			  $hasupdates++;
    	      
    	      mysql_query("UPDATE sou_ship_module SET quality='$quality', $updatefeld='$istwertneu', $updatefelduom='$hasupdates' WHERE user_id='$player_user_id' AND id='$id'",$soudb);
    	      
    	      //info ausgeben
    	      $msg='<font color="#00FF00">Das Modul wurde aufgewertet.</font>';
    	    }
    	    else $msg='<font color="#FF0000">Es stehen nicht genug Spezialrohstoffe zur Verf&uuml;gung.</font>';
  	  	  }
  	  	  else $msg='<font color="#FF0000">Bei diesem Modul sind nicht mehr Upgrades m&ouml;glich.</font>';
    	}
      	else $msg='<font color="#FF0000">Dieses Modul steht nicht zur Verf&uuml;gung.</font>';

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
	

  	
    echo '<br>';
    
  	if($msg!='')
	{
  	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
  	  echo '<br>';
    }
    
    
    echo '<form action="sou_main.php" method="POST" name="f">';
	echo '<input type="hidden" name="action" value="upgradeomodulpage">';
    
    
  	$output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  	<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  	<td><b>'.$b_defs[12][0].' (Fraktionsstufe '.$geb_level.')</b></td>
  	<td width="120"><a href="sou_main.php?action=modulholdpage"><div class="b1">Modulkomplex</div></a> </td>
  	</tr></table>';
  	rahmen1_oben($output);	
	
	//erklärung
	if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
	echo '<div class="'.$bg.'">In diesem Geb&auml;ude k&ouml;nnen Module veredelt werden. Pro Update wird der Istwert um 0,65% erh&ouml;ht. Pro Geb&auml;udefraktionsstufe sind 3 Updates pro Eigenschaft m&ouml;glich. Die Bezahlung erfolgt mit den besonders wertvollen Rohstoffen.';
	
	echo ' W&auml;hle bitte die Moduleigenschaft aus, die verbessert werden soll:<br><br>';
	
	//auswahlbereich
    //auswahl der sichtbaren technologien
    echo '<select name="viewtyp" onchange="document.f.submit();">';
    
    for($i=0;$i<count($viewtypes);$i++)
    {
      if($_SESSION["sou_upgradeomodul_viewtyp"]==$i)$selected=' selected'; else $selected='';
      echo '<option value="'.$i.'" '.$selected.'>'.$viewtypes[$i].'</option>';
    
    }
    echo '</select><br><br></div>';
	
  	$sql="SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) AND $updatefeld>0 AND uomlock=0 ORDER BY name"; 

	//tooltip bauen
	$atipc=0;
	    
  	$output2='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
  	if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
  	$output2.='<tr class="'.$bg.'" align="center"><td align="left"><b>Modul</b></td><td><b>Istwert</b></td><td><b>Upgradewert</b></td><td><b>Upgrades</b></td><td><b>Kosten</b></td></tr>';
    $db_daten=mysql_query($sql,$soudb);
    while($row = mysql_fetch_array($db_daten))
    {
  	  $atip[$atipc] = make_modul_name_js($row).'&'.make_modul_info($row);
      
  	  //überprüfen ob das modul im lager oder im schiff ist
      if($row["location"]==0)$hstr=' <img border="0" style="vertical-align: middle;" src="'.$gpfad.'v1.png" width="16" height="16" alt="im Raumschiff" title="im Raumschiff">';else {$hstr='';}   	 
  	  
  	  //hintergrund bestimmen
      if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
  	
  	  $output2.='<tr class="'.$bg.'" align="center">';
  	  //modulname
  	  $output2.='<td align="left" title="'.$atip[$atipc].'">'.make_modul_name($row).$hstr.'</td>';
  	  //istwert

  	  $istwert=$row[$updatefeld];
  	  $istwertneu=ceil($istwert*$upgradefaktor);
  	  $hasupdates=$row[$updatefelduom];
  	  	  
      $output2.='<td>'.number_format($istwert, 0,"",".").'</td>';
      
      //updatewert
      $output2.='<td>'.number_format($istwertneu, 0,"",".").'</td>';
      
      //updates
      //zuerst textfarbe bestimmen
      $bcolor='#00FF00';
	  if($hasupdates<$maxupgrades){$bcolor='yellow';}
	  if($hasupdates<$maxupgrades*0.66)$bcolor='orange';
	  if($hasupdates<$maxupgrades*0.33)$bcolor='red';

      $output2.='<td><font color="'.$bcolor.'">'.$hasupdates.' / '.$maxupgrades.'</font>';
	  
      //updatelink nur anzeigen, wenn ein update möglich ist
      if($hasupdates < $maxupgrades)
      $output2.=' <a href="sou_main.php?action=upgradeomodulpage&do=1&id='.$row["id"].'&viewtyp='.$_SESSION["sou_upgradeomodul_viewtyp"].'"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym3.png"  width="24px" height="24px" title="Information&Klicken, um das Modul zu den angegebenen Konditionen aufzuwerten."></a></td>';
      
      //kosten
      if($hasupdates < $maxupgrades)
      {
        $specialres=$specialres_def[floor($hasupdates/15)][1];
        //$output2.='<td>1 '.$specialres.' (Im Lager: '.$player_specialres[floor($hasupdates/15)+1].')</td>';
        
        $output2.='<td>1 '.$specialres.' (Im Lager: '.has_specialres($player_user_id, floor($hasupdates/15)).')</td>';
      }
      else $output2.='<td>-</td>';
  	  $output2.='</tr>';
  	  $atipc++;
    }
	$output2.='</table>';

	echo $output2;
	
	rahmen1_unten();
	echo '</form>';

  }
  else echo '<br>Fehlendes Geb&auml;ude: '.$b_defs[12][0].'<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
}
else echo 'Hier gibt es kein Sonnensystem.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>