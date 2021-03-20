<?php
include "soudata/defs/resources.inc.php";
include "soudata/defs/boni.inc.php";

//aktuellen hb punkte auslesen
$hbbonus=0;
$db_daten=mysql_query("SELECT * FROM `sou_hbpoints`",$soudb);
while($row = mysql_fetch_array($db_daten))
{
	if($row['owner']==$player_fraction)$hbbonus=$hbbonus+0.2;
}
//daten zur ansicht
echo '<br><br>';

echo '<div align="center">';

rahmen0_oben();

//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1){

	//die id des sonnensystems auslesen
	$row = mysql_fetch_array($db_daten);
	$owner_id=$row["id"];
	$owner_sysname=$row["sysname"];
	$owner_fraction=$row["fraction"];
	$pirates=$row['pirates'];

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
  
	//überprüfen ob das system zur eigenen fraktion gehört
	if($player_fraction!=$owner_fraction){
		rahmen2_oben();
		echo 'Auf dieses Sonnensystem hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
		rahmen2_unten();
		echo '<br>';

		echo '</div>';//center-div
		die('</body></html>');
	}
    
	//gebäudelevel des handelskontors auslesen
	//$geb_level=get_bldg_level($owner_id, 3);
	$geb_level=get_max_frac_bldg_level(3);
	//wenn es kein lokales gibt, dann weiterhin abbrechen
	if(get_bldg_level($owner_id, 3)==0)$geb_level=0;
	$bldgbonus=$geb_level;
	if($geb_level>0){
		//fraktionsboni auslesen
		$time=time();
		$feldname1='f'.$player_fraction.'bonus1';
		$feldname2='f'.$player_fraction.'bonus2';
		$feldname3='f'.$player_fraction.'bonus3';
		$db_daten=mysql_query("SELECT $feldname1 AS bonus1, $feldname2 AS bonus2, $feldname3 AS bonus3 FROM `sou_system`",$soudb);
		$row = mysql_fetch_array($db_daten);
		$gesbonus=0;

		if($time<$row["bonus1"])$gesbonus+=$boni_def[0][1];
		if($time<$row["bonus2"])$gesbonus+=$boni_def[1][1];
		if($time<$row["bonus3"])$gesbonus+=$boni_def[2][1];

		///////////////////////////////////////////////////////
		///////////////////////////////////////////////////////
		//  für x minuten im asteroidenfeld minen und das dann direkt verkaufen
		///////////////////////////////////////////////////////
		///////////////////////////////////////////////////////
		if($_REQUEST["do"]=='2'){
		  //transaktionsbeginn
		  if (setLock($_SESSION["sou_user_id"])){
			//rohstoffart auselsen
			//$rid=intval($_REQUEST["rid"]);
			$rid=0;
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
					  //überprüfen ob man diese rohstoffart dort bereits verkaufen kann
					  if($geb_level>=$r_def[$rid][3])
					  {
						//überprüfen ob der rohstoff dort vorkommt
						if(res_is_available($rid)==1)
						{
						  //berechnen wieviel rohstoffe man pro minute bekommen kann
						  $getres=round($canmine/$r_def[$rid][1]*(1+(get_skill($rid)/500000)));

						  //überprüfen ob das alles in den lagerraum paßt
						  //if($getres>$freehold)$getres=$freehold;
						  //ertrag in der zeit
						  $getres=$getres*$min;

						  //berechnen wieviel geld man für die rohstoffe bekommt und diese gutschreiben
						  $zastari=round($getres*(
						  $r_def[$rid][2]+($r_def[$rid][2]/100*$gesbonus)+($r_def[$rid][2]/100*$bldgbonus)+($r_def[$rid][2]/100*$hbbonus)
						  ));

						  change_money($player_user_id, $zastari);

						  //credits abziehen
						  if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Handelskontor-Komfortfunktion');

						  //skill verbessern
						  change_skill($rid, $min);  			      

						  //einen counter setzen, damit sonst nichts mehr zu machen geht
						  $time=time()+$min*60;
						  mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
						  //seite neu laden
						  header("Location: sou_main.php");
						}
						else $msg='<font color="#FF0000">Diese Rohstoffart ist hier nicht vorhanden.</font>';
					  }
					  else $msg='<font color="#FF0000">Diese Rohstoffe k&ouml;nnen hier nicht verkauft werden, die Geb&auml;udestufe ist zu klein.</font>';
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
    
	
  	//handelskontor darstellen

	if($msg!='')
	{
  	  echo '<br>';
	  rahmen2_oben();
  	  echo $msg;
  	  rahmen2_unten();
    }
  	
  	echo '<br>';
  	
    $output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
    <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
    <td><b>Der Handelskontor von '.$owner_sysname.' (Fraktionsstufe '.$geb_level.')</b></td>
    <td width="120">&nbsp;</td>
    </tr></table>';
    rahmen1_oben($output);  	

  	$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';

  	$bg='cell1';
  	$output.='<tr align="center"><td class="'.$bg.'" colspan="4">Hier kannst du Eisen aus dem Asteroidenfeld verkaufen um an mehr Zastari zu gelangen. Pro Geb&auml;udestufe erh&ouml;ht sich der Verkaufspreis um 1%.</td></tr>';
    //spaltenüberschrift
  	$bg='cell';
    $output.='<tr align="center"><td class="'.$bg.'"><b>Rohstoff</b></td><td class="'.$bg.'"><b>Verkaufspreis</b></td>
    <td class="'.$bg.'"><b>Bergbaudauer</b></td>';
	
    $ttid=0;
	//nur noch eisen
    //for($i=0;$i<count($r_def);$i++)
    for($i=0;$i<1;$i++)
	{
  	  //hintergrund bestimmen
  	  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  	  $output.='<tr align="center">';
  	  //rohstoffname
  	  if($geb_level<$r_def[$i][3])$resname='<font color="#FF0000">'.$r_def[$i][0].' (ab Gebäudestufe '.$r_def[$i][3].')</font>';
  	  else $resname=$r_def[$i][0];
  	  $output.='<td class="'.$bg.'">'.$resname.'</td>';
  	  $output.='<td class="'.$bg.'">'.number_format($r_def[$i][2]+($r_def[$i][2]/100*$gesbonus)+($r_def[$i][2]/100*$bldgbonus)+($r_def[$i][2]/100*$hbbonus), 4,",",".").'</td>';
  	  $output.='<td class="'.$bg.'">';
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=5" title="Rohstoffverkauf&5 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=15" title="Rohstoffverkauf&15 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=30" title="Rohstoffverkauf&30 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=45" title="Rohstoffverkauf&45 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=60" title="Rohstoffverkauf&60 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
  	  $ttid++;
		//abfrage für non-pa accounts
		if($ums_premium!=1){
			$jssecure='onclick="return confirm(\'Diese Aktion kostet Credits. Durchf&uuml;hren?\')"';
		}else{
			$jssecure='';
		}	  
	  
      $output.='
      &nbsp;<a '.$jssecure.' id="ttid'.$ttid.'" href="sou_main.php?action=tradepage&do=2&rid='.$i.'&min=480" title="Power Rohstoffverkauf&480 Fl&uuml;ge<br>Preis: 10 Credits (F&uuml;r Premium-Account-Nutzer kostenlos)">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'abutton2.gif"  width="16px" height="16px"></a>';
  	  $output.='</td></tr>';
  	  $ttid++;
	}
	$output.='</table>';

	echo $output;

  	rahmen1_unten();
  	echo '<br>';
  }
  else echo '<br>Hier gibt es keinen Handelskontor.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';
}
else echo 'Hier gibt es kein Sonnensystem.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>