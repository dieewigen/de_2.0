<?php
include "soudata/defs/resources.inc.php";

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
  
  //�berpr�fen ob das system zur eigenen fraktion geh�rt
  if($player_fraction!=$owner_fraction)
  {
    echo '<br>';
  	rahmen2_oben();
  	echo 'Auf dieses Sonnensystem hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }
  
  
  //geb�udelevel der raumwerft auslesen
  $geb_level=get_bldg_level($owner_id, 2);
  if($geb_level>0)
  {
    //alle schiffsgrößen in ein array packen
    unset($shipsizes);
	$ga=2;$d=-2;
    for($i=0;$i<=60;$i++)
	{
	  $d=$d+$ga;
	  $ga=$ga*1.1019;
	  $shipsizes[]=floor($d+10);
	}
  
	//feststellen welcher geb�udestufe die aktuelle schiffsgröße entspricht
	$shiplevel=0;
    for($i=0;$i<=count($shipsizes);$i++)
	{
	  if($player_ship_diameter==$shipsizes[$i])
	  {
	    $shiplevel=$i;
	    break;
	  }
	}

	//den durchmesser der n�chst h�heren stufe berechnen
  	$dnext=$shipsizes[$shiplevel+1];
	
    //den maximalen durchmesser berechnen der beim geb�udelevel von x m�glich ist
  	$dmax=$shipsizes[$geb_level];
  	
  	//if($dnext>$dmax)$dnext=$dmax;
  	
  	//ben�tigten rohstofftyp feststellen
  	$needrestyp=0;
  	if($shiplevel>10)$needrestyp=1;
  	if($shiplevel>15)$needrestyp=2;
  	if($shiplevel>20)$needrestyp=3;
  	if($shiplevel>25)$needrestyp=4;
  	if($shiplevel>30)$needrestyp=5;
  	if($shiplevel>35)$needrestyp=6;
  	if($shiplevel>40)$needrestyp=7;
  	if($shiplevel>45)$needrestyp=8;
  	if($shiplevel>49)$needrestyp=9;
  	
	//falls man das schiff vergr��ern m�chte hier pr�fen
    if($_REQUEST["do"]=='1')
    {
      //transaktionsbeginn
      if (setLock($_SESSION["sou_user_id"]))
      {
      	//�berpr�fen ob es gr��er ist
      	if($dnext>$player_ship_diameter AND $dnext<=$dmax)
      	{
		  	  //berechnen wieviel material man bereits hat
      	      $volumen1 = floor(4/3 * pow($player_ship_diameter / 2, 3) * pi());
      	      $volumen2 = floor(4/3 * pow(($player_ship_diameter*0.95) / 2, 3) * pi());	
      	      $hasres=$volumen1-$volumen2;
      	  
      	      //berechnen, wieviel man noch braucht
      	      $volumen1 = floor(4/3 * pow($dnext / 2, 3) * pi());
      	      $volumen2 = floor(4/3 * pow(($dnext *0.95) / 2, 3) * pi());	
      	      $needres=$volumen1-$volumen2-$hasres;
      	  
      	      //�berpr�fen ob man genug rohstoffe im lagerkomplex hat
      	      if($needres<=has_systemhold_amount($_SESSION["sou_user_id"], $needrestyp))
      	      {
      	        //rohstoffe abziehen
      	        change_systemhold_amount($_SESSION["sou_user_id"], $needrestyp, $needres*-1);
      	    
      	        //schiff vergr��ern
      	        mysql_query("UPDATE sou_user_data SET shipdiameter='$dnext' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
      	        $player_ship_diameter=$dnext;
      	        //daten neu berechnen
      	        $shiplevel++;
      	        $dnext=$shipsizes[$shiplevel+1];
  				//ben�tigten rohstofftyp feststellen
  				$needrestyp=0;
  				if($shiplevel>10)$needrestyp=1;
  				if($shiplevel>15)$needrestyp=2;
  				if($shiplevel>20)$needrestyp=3;
  				if($shiplevel>25)$needrestyp=4;
  				if($shiplevel>30)$needrestyp=5;
  				if($shiplevel>35)$needrestyp=6;
  				if($shiplevel>40)$needrestyp=7;
  				if($shiplevel>45)$needrestyp=8;
  				if($shiplevel>49)$needrestyp=9;
      	        
      	        //msg ausgeben
      	        $msg='<font color="#00FF00">Das Schiff wurde vergr&ouml;�ert.</font>';
      	      }
      	      else $msg='<font color="#FF0000">Es sind nicht genug Rohstoffe vorhanden.</font>';
      	}
      	else $msg='<font color="#FF0000">Mit dieser Geb&auml;udestufe kann das Raumschiff nicht weiter vergr&ouml;�ert werden.</font>';
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

	echo '<br>';
	
  	if($msg!='')
	{
  	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
  	  echo '<br>';
    }

    $output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
    <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
    <td><b>Die Raumwerft von '.$owner_sysname.' (Stufe '.$geb_level.')</b></td>
    <td width="120"><a href="sou_main.php?action=systemholdpage"><div class="b1">Lagerkomplex</div></a> </td>
    </tr></table>';
    rahmen1_oben($output);    

    echo '<form action="sou_main.php" method="POST" name="f1">';
	echo '<input type="hidden" name="action" value="shipyardpage">';
	echo '<input type="hidden" name="do" value="1">';
	
	//berechnen wieviel material man bereits hat
    $volumen1 = floor(4/3 * pow($player_ship_diameter / 2, 3) * pi());
    $volumen2 = floor(4/3 * pow(($player_ship_diameter*0.95) / 2, 3) * pi());	
    $hasres=$volumen1-$volumen2;
      	  
    //berechnen, wieviel man noch braucht
    $volumen1 = floor(4/3 * pow($dnext / 2, 3) * pi());
    $volumen2 = floor(4/3 * pow(($dnext*0.95) / 2, 3) * pi());	
    $needres=$volumen1-$volumen2-$hasres;
	
  	$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';

  	$bg='cell1';
  	$output.='<tr align="center"><td class="'.$bg.'" colspan="4">Hier kannst Du dein Raumschiff aufwerten.<br>';
	$output.='<br>Schiffsgr&ouml;&szlig;e: '.number_format($player_ship_diameter, 0,"",".").' Meter (Stufe '.$shiplevel.')';
	$output.='<br>Neue Schiffsgr&ouml;&szlig;e: '.$dnext;
    $output.='<br>Ben&ouml;tigte Rohstoffe: '.number_format($needres, 0,",",".").' m&sup3; '.$r_def[$needrestyp][0];
    $output.=' (Im Lagerkomplex: '.number_format(has_systemhold_amount($_SESSION["sou_user_id"], $needrestyp), 0,",",".").')';
	$output.='</td></tr>';

	$output.='</table>';

	echo $output;
	
	  	echo '<br><a href="javascript:document.f1.submit();"><div class="b1">vergr&ouml;&szlig;ern</div></a>';
  	echo '<input type="image" src="'.$gpfad.'e.gif" style="width:0px; height:0px; border:0px;">';

  	echo '</form>';
  	rahmen1_unten();
  	echo '<br>';
  	
  	
  	/////////////////////////////////////////////////
  	/////////////////////////////////////////////////
  	// module je schiffsstufe
  	/////////////////////////////////////////////////
  	/////////////////////////////////////////////////
  	rahmen1_oben('<div style="text-align: center; font-weight: bold;">Modulanzahl</dv>');
  	
  	echo '<table width="100%">';
  	
  	echo '<tr align="center"><td class="cell">Stufe</td><td class="cell1">Module</td><td class="cell">Stufe</td><td class="cell1">Module</td><td class="cell">Stufe</td><td class="cell1">Module</td><td class="cell">Stufe</td><td class="cell1">Module</td><td class="cell">Stufe</td><td class="cell1">Module</td></tr>';
  	
  	for($i=0;$i<10;$i++)
  	{
		echo '<tr align="center">';
		echo '<td class="cell">'.($i+1).'</td>';
		echo '<td class="cell1">'.(2+round(sqrt($shipsizes[$i+1]))).'</td>';
		
		echo '<td class="cell">'.($i+11).'</td>';
		echo '<td class="cell1">'.(2+round(sqrt($shipsizes[$i+11]))).'</td>';
		
		echo '<td class="cell">'.($i+21).'</td>';
		echo '<td class="cell1">'.(2+round(sqrt($shipsizes[$i+21]))).'</td>';
		
		echo '<td class="cell">'.($i+31).'</td>';
		echo '<td class="cell1">'.(2+round(sqrt($shipsizes[$i+31]))).'</td>';
		
		echo '<td class="cell">'.($i+41).'</td>';
		echo '<td class="cell1">'.(2+round(sqrt($shipsizes[$i+41]))).'</td>';
		echo '</tr>';
  	}
  	
  	echo '</table>'; 	
  	
  	rahmen1_unten();
  	echo '<br>';  	
  }
  else echo '<br>Hier gibt es keine Raumwerft. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
}
else echo 'Hier gibt es kein Sonnensystem. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>