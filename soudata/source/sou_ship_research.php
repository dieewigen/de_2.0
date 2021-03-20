<?php
//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

//überprüfen ob ein forschungsmodul im schiff ist
$db_daten=mysql_query("SELECT MAX(giveresearch) AS giveresearch FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND location=0",$soudb);
$row = mysql_fetch_array($db_daten);

if($row["giveresearch"]>0)
{
  $researchpower=$row["giveresearch"];
  $choice=intval($_REQUEST["choice"]);

  echo '<form action="sou_main.php" method="POST" name="f">';
  echo '<input type="hidden" name="action" value="shipresearchpage">';
  //echo '<input type="hidden" name="do" value="1">';
  
  //überprüfen ob man etwas erforschen möchte
  if($_REQUEST["do"]==1)  
  {
    $flag=intval($_REQUEST["flag"]);
  	
    //überprüfen, ob man schon an etwas forscht
    if($player_atimer2typ==0)
    {
      //flag überprüfen
      if($flag<1 OR $flag>5)$flag=1;
      if($flag==1)$spalte='hasspace';
      elseif($flag==2)$spalte='canmine';
      elseif($flag==3)$spalte='giveweapon';
      elseif($flag==4)$spalte='giveshield';
      elseif($flag==5)$spalte='canrecover';
      
      //überprüfen ob der fraktion die forschung bereits zur verfügung steht
      $search='f'.$player_fraction.'lvl';
      $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE $search>0 AND bldg_build>0 AND tech_id='$choice'",$soudb);    	
      $num = mysql_num_rows($db_daten);
      if($num==1)
      {
        //überprüfen ob der wert auch erhöht werden kann
        $row = mysql_fetch_array($db_daten);
        if($row[$spalte]>0)
        {
      	  //bestehenden forschungslevel auslesen
		  $db_daten=mysql_query("SELECT * FROM `sou_user_tech_updates` WHERE user_id='$player_user_id' AND tech_id='$choice'",$soudb);
		  $num = mysql_num_rows($db_daten);
    	  if($num==1)
    	  {
	        $row = mysql_fetch_array($db_daten);
	        $updates=$row[$spalte];
	        $haszeile=1;
    	  }
    	  else
    	  {
      	    $updates=0;
      	    $haszeile=0;
    	  }
          
          //forschungszeit berechnen
          $fz=1;
          for($i=0;$i<$updates;$i++)
          {
          	$fz=$fz*1.25;
          }
          //zeit in sekunden abzgl. modulbonus
          $fz=$fz*3600-$researchpower+3600;
          //es dauert aber immer mindestens 1 stunde
          if($fz<3600)$fz=3600;
          
          //echo $fz.'<br>';
      	  //echo $choice.'<br>';
  	      //echo $flag;
  	      
  	      //das forschungsupdate in der db ablegen
  	      if($haszeile==0)mysql_query("INSERT INTO sou_user_tech_updates (user_id, tech_id) VALUES ('$player_user_id', '$choice')",$soudb);
		  $time=time()+$fz;
  	      mysql_query("UPDATE sou_user_data SET atimer2typ='$choice', atimer2flag='$flag', atimer2time='$time' WHERE user_id='$player_user_id'",$soudb);
  	      $player_atimer2typ=$choice;
  	      $player_atimer2flag=$flag;
  	      $player_atimer2time=$time;
        }
      }
    }
  }
  
  //aktuelle forschung abbrechen
  if($_REQUEST["rb"]==1)
  {
  	mysql_query("UPDATE sou_user_data SET atimer2typ='0', atimer2flag='0', atimer2time='0' WHERE user_id='$player_user_id'",$soudb);
  	$player_atimer2time=0;
  	$player_atimer2flag=0;
  }
  
  //überprüfen ob gerade eine forschung aktiv ist
  if($player_atimer2time>0)
  {
  	//worum geht es bei der forschung
    $flag=$player_atimer2flag;
    if($flag==1)$spalte='hasspace';
    elseif($flag==2)$spalte='canmine';
    elseif($flag==3)$spalte='giveweapon';
    elseif($flag==4)$spalte='giveshield';
    elseif($flag==5)$spalte='canrecover';
  	
  	//überprüfen ob sie schon abgelaufen/erforscht ist
  	if($player_atimer2time<time())
  	{
      //es ist erforscht, also db updaten

  	  mysql_query("UPDATE sou_user_tech_updates SET $spalte=$spalte+1 WHERE user_id='$player_user_id' AND tech_id='$player_atimer2typ'",$soudb);
      
  	  mysql_query("UPDATE sou_user_data SET atimer2typ='0', atimer2flag='0', atimer2time='0' WHERE user_id='$player_user_id'",$soudb);
  	  $player_atimer2typ=0;
  	  $player_atimer2flag=0;
  	  $player_atimer2time=0;  	  
  	}
  	else 
  	{
  	  //es wird noch zeit benötigt:
  	  //techname auslesen
      $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id='$player_atimer2typ'",$soudb);    	
      $row = mysql_fetch_array($db_daten);
      
        $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Forschungsmodul</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
  rahmen1_oben($routput);
          
  	  echo '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center" class="cell"><td>';

  	  echo '<br>Aktive Forschung: '.$row["tech_name"].'<br>';
  	  $fziel='';
  	  if($player_atimer2flag==1)$fziel='Lagerkapazit&auml;t';
  	  elseif($player_atimer2flag==2)$fziel='Bergbauf&ouml;rderkapazit&auml;t';
  	  elseif($player_atimer2flag==3)$fziel='Waffenkapazit&auml;t';
  	  elseif($player_atimer2flag==4)$fziel='Schildkapazit&auml;t';
  	  elseif($player_atimer2flag==5)$fziel='Bergungskapazit&auml;t';
  	  
  	  //forschungsstufe auslesen
	  $db_daten=mysql_query("SELECT $spalte AS wert FROM sou_user_tech_updates WHERE user_id='$player_user_id' AND tech_id='$player_atimer2typ'",$soudb);  	  
  	  $row = mysql_fetch_array($db_daten);
  	  $researchlevel=$row['wert'];
  	  
  	  echo 'Forschungsziel: '.$fziel.' - Forschungsupdate (FU) '.($researchlevel+1).'<br>';
  	  echo 'Verbleibende Zeit (Sekunden): '.number_format($player_atimer2time-time(), 0,",",".").'<br>';
  	  echo 'Fertigstellung Echtzeit: '.date("G:i d.m.Y", time()+($player_atimer2time-time())).'<br><br>';
  	  echo 'Es ist m&ouml;glich die Forschung abzubrechen, wobei die aktuellen Ergebnisse verloren gehen.<br><br>';
  	  echo '<a href="sou_main.php?action=shipresearchpage&rb=1" onClick="return confirm(\'Forschung wirklich abbrechen?\')"><div class="b1">abbrechen</div></a><br>'; 
  	  echo '</td></tr></table>';
  	  
  	  rahmen1_unten();
  	  echo '<br>';
  	  rahmen0_unten();
  	  die('</body></html>');	
  	}
  }
  
  //alle erforschten technologien auslesen, die verbesserbar sind
  unset($has_techs);
  $search='f'.$player_fraction.'lvl';
  $sql="SELECT * FROM `sou_frac_techs` WHERE $search>0 AND bldg_build>0 AND (hasspace>0 OR canmine>0 OR giveweapon>0 OR giveshield>0
   OR canrecover>0) ORDER BY sort_id ASC";
  $db_daten=mysql_query($sql,$soudb);
  $auswahlmenu='<select name="choice" onchange="document.f.submit();">';
  while($row = mysql_fetch_array($db_daten))
  {
    if($choice=='')$choice=$row["tech_id"];
    
  	$has_techs[]=$row["tech_id"];
    //auswahlmenü designen
    if($row["tech_id"]==$choice)//überprüfen ob es gerade das ausgewählte modul ist
    {
      $selected=' selected';
      $tech_daten=$row;
    }
    else $selected='';
    $auswahlmenu.='<option value="'.$row["tech_id"].'" '.$selected.'>'.$row["tech_name"].'</option>';
  }
  $auswahlmenu.='</select>';
  
  //echo $auswahlmenu;
  
  //überprüfen ob das ausgewählte auch wirklich vorhanden ist
  if(in_array($choice, $has_techs))
  {
  	//vorhandene user-updates auslesen
	$db_daten=mysql_query("SELECT * FROM `sou_user_tech_updates` WHERE user_id='$_SESSION[sou_user_id]' AND tech_id='$choice'",$soudb);
	$num = mysql_num_rows($db_daten);
    if($num==1)
    {
	  $rowx = mysql_fetch_array($db_daten);
    }
    else
    {
      $rowx["giveenergy"]=0;
    }
  	
        $routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td>'.$auswahlmenu.'</td>
  <td width="120">&nbsp;</td>
  </tr></table>';
  rahmen1_oben($routput);
  	
  	$moduloutput='<table width="100%" cellpadding="0" cellspacing="0" border="0">';
  	$moduloutput.='<tr align="center" class="cell"><td>Eigenschaft</td><td>Standardwert</td><td>Updates</td><td>Updatewert</td><td>Forschungszeit</td><td>Aktion</td></tr>';
  	
  	  $row=$tech_daten;

  	  if($row["hasspace"]>0)
  	  {
  		$moduloutput.='<tr align="center">';
  	  	$moduloutput.='<td>Lagerkapazit&auml;t</td>';
  	  	$moduloutput.='<td>'.number_format($row["hasspace"], 0,",",".").'</td>';
  	  	$moduloutput.='<td>'.intval($rowx["hasspace"]).'</td>';
  	  	$moduloutput.='<td>'.number_format(round($row["hasspace"]+($row["hasspace"]/100*$rowx["hasspace"])), 0,",",".").'</td>';
        //forschungszeit berechnen
        $fz=calculate_fz($rowx["hasspace"]);
  	  	$moduloutput.='<td>'.number_format($fz, 0,",",".").'</td>';
  	  	$moduloutput.='<td><a href="sou_main.php?action=shipresearchpage&do=1&choice='.$choice.'&flag=1">Update</td>';
  	  	$moduloutput.='</tr>';  	  	
  	  }
      

  	  if($row["canmine"]>0)
  	  {
  		$moduloutput.='<tr align="center">';
  	  	$moduloutput.='<td>F&ouml;rderkapazit&auml;t</td>';
  	  	$moduloutput.='<td>'.number_format($row["canmine"], 0,",",".").'</td>';
  	  	$moduloutput.='<td>'.intval($rowx["canmine"]).'</td>';
  	  	$moduloutput.='<td>'.number_format(round($row["canmine"]+($row["canmine"]/100*$rowx["canmine"])), 0,",",".").'</td>';
        //forschungszeit berechnen
        $fz=calculate_fz($rowx["canmine"]);
  	  	$moduloutput.='<td>'.number_format($fz, 0,",",".").'</td>';
  	  	$moduloutput.='<td><a href="sou_main.php?action=shipresearchpage&do=1&choice='.$choice.'&flag=2">Update</td>';
  	  	$moduloutput.='</tr>';  	  	
  	  }

      if($row["giveweapon"]>0)
  	  {
  		$moduloutput.='<tr align="center">';
  	  	$moduloutput.='<td>Waffenkapazit&auml;t</td>';
  	  	$moduloutput.='<td>'.number_format($row["giveweapon"], 0,",",".").'</td>';
  	  	$moduloutput.='<td>'.intval($rowx["giveweapon"]).'</td>';
  	  	$moduloutput.='<td>'.number_format(round($row["giveweapon"]+($row["giveweapon"]/100*$rowx["giveweapon"])), 0,",",".").'</td>';
        //forschungszeit berechnen
        $fz=calculate_fz($rowx["giveweapon"]);
  	  	$moduloutput.='<td>'.number_format($fz, 0,",",".").'</td>';
  	  	$moduloutput.='<td><a href="sou_main.php?action=shipresearchpage&do=1&choice='.$choice.'&flag=3">Update</td>';
  	  	$moduloutput.='</tr>';
  	  }

      if($row["giveshield"]>0)
  	  {
  		$moduloutput.='<tr align="center">';
  	  	$moduloutput.='<td>Schildkapazit&auml;t</td>';
  	  	$moduloutput.='<td>'.number_format($row["giveshield"], 0,",",".").'</td>';
  	  	$moduloutput.='<td>'.intval($rowx["giveshield"]).'</td>';
  	  	$moduloutput.='<td>'.number_format(round($row["giveshield"]+($row["giveshield"]/100*$rowx["giveshield"])), 0,",",".").'</td>';
        //forschungszeit berechnen
        $fz=calculate_fz($rowx["giveshield"]);
  	  	$moduloutput.='<td>'.number_format($fz, 0,",",".").'</td>';
  	  	$moduloutput.='<td><a href="sou_main.php?action=shipresearchpage&do=1&choice='.$choice.'&flag=4">Update</td>';
  	  	$moduloutput.='</tr>';
  	  }

      if($row["canrecover"]>0)
  	  {
  		$moduloutput.='<tr align="center">';
  	  	$moduloutput.='<td>Bergungskapazit&auml;t</td>';
  	  	$moduloutput.='<td>'.number_format($row["canrecover"], 0,",",".").'</td>';
  	  	$moduloutput.='<td>'.intval($rowx["canrecover"]).'</td>';
  	  	$moduloutput.='<td>'.number_format(round($row["canrecover"]+($row["canrecover"]/100*$rowx["canrecover"])), 0,",",".").'</td>';
        //forschungszeit berechnen
        $fz=calculate_fz($rowx["canrecover"]);
  	  	$moduloutput.='<td>'.number_format($fz, 0,",",".").'</td>';
  	  	$moduloutput.='<td><a href="sou_main.php?action=shipresearchpage&do=1&choice='.$choice.'&flag=5">Update</td>';
  	  	$moduloutput.='</tr>';
  	  }  	  
  	  
  	  /*
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
  	  }*/
  	  
  	$moduloutput.='</table>';
  	    
  	echo $moduloutput;
  	
  	rahmen1_unten();
  }
  else echo 'Es ist ein Fehler aufgetreten.';

  echo '</form>';
}
else //er hat kein forschungsmodul
{

  rahmen2_oben();
  echo '<br>Es ist kein Forschungsmodul vorhanden. Zur&uuml;ck zum <a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br><br>';
  rahmen2_unten();
}

echo '<br>';
rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');

function calculate_fz($updates)
{
  global $researchpower;
  
  $fz=1;
  for($i=0;$i<$updates;$i++)
  {
    $fz=$fz*1.25;
  }
  //zeit in sekunden abzgl. modulbonus
  $fz=$fz*3600-$researchpower+3600;
  //es dauert aber immer mindestens 1 stunde
  if($fz<3600)$fz=3600;	
  return($fz);
}
?>