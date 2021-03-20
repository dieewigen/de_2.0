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
  
  
  //geb�udelevel lagerkomplex auslesen
  //$geb_level=get_bldg_level($owner_id, 4);
  $geb_level=get_max_frac_bldg_level(4);
  //wenn es kein lokales gibt, dann weiterhin abbrechen
  if(get_bldg_level($owner_id, 4)==0)$geb_level=0;
  if($geb_level>0)
  {
    //alle schiffsgr��en in ein array packen
    unset($shipsizes);
	$ga=2;$d=-2;
    for($i=0;$i<=60;$i++)
	{
	  $d=$d+$ga;
	  $ga=$ga*1.1019;
	  $shipsizes[]=floor($d+10);
	}
	//die lagergr��e anhand der stufe in relation zur schiffsgr��e bestimmen
  	$d=$shipsizes[$geb_level];

  	//echo $d;
    //berechnen wie gro� das lager ist  	
  	$volumen1 = floor(4/3 * pow($d / 2, 3) * pi());
  	$volumen2 = floor(4/3 * pow(($d * 0.95) / 2, 3) * pi());
  	$volumen=$volumen1-$volumen2;

    //alle rohstoffe aus dem rohstoffkomplex auslesen
    unset($complex_has);
	//$db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$_SESSION[sou_user_id]' AND owner_id='$owner_id' ORDER BY res_id",$soudb);
	$db_daten=mysql_query("SELECT * FROM `sou_user_systemhold` WHERE user_id='$_SESSION[sou_user_id]' ORDER BY res_id",$soudb);
	while($row = mysql_fetch_array($db_daten))
    {
      $complex_has[$row["res_id"]]=$row["amount"];
    }	

	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	//  x minuten rohstoffe einlagern
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
    if($_REQUEST["do"]=='2'){
	    //transaktionsbeginn
		if (setLock($_SESSION["sou_user_id"])){
			//schauen was einlagern m�chte
			$rid=intval($_REQUEST["rid"]);
			if($rid>=count($r_def))$rid=0;
			$min=intval($_REQUEST["min"]);
			if($min<1 OR $min>480)$min=1;


			//�berpr�fen ob das geb�ude schon gut genug ist
			if($r_def[$rid][4]<=$geb_level)
			{
				//�berpr�fen ob man freien laderaum hat
				$freehold=1;//get_sum_hold($_SESSION["sou_user_id"]);
				if($freehold>0){
					//�berpr�fen ob man ein mining-modul hat
					$canmine=get_canmine($_SESSION["sou_user_id"]);
					if($canmine>0){
						//�berpr�fen ob man genug credits hat
						$needcredits=0;
						if($min>60)$needcredits=10;
						if($ums_premium==1)$needcredits=0;
						$has_credits=has_credits($ums_user_id);
						if($has_credits>=$needcredits){
							//�berpr�fen ob noch genug platz im lagerkomplex vorhanden ist
							$freeholdsystem=$volumen-has_systemhold_amount($_SESSION["sou_user_id"], $rid);
							if($freeholdsystem>0){
								//�berpr�fen ob der rohstoff dort vorkommt
								if(res_is_available($rid)==1){
									//berechnen wieviel rohstoffe man pro minute bekommen kann
									//miningmodul
									$getresmin=round($canmine/$r_def[$rid][1]*(1+(get_skill($rid)/500000)));
									//lagerraum
									//if($getresmin>$freehold)$getresmin=$freehold;

									//�berpr�fen wie oft man fliegen mu�
									$transfers=ceil($freeholdsystem/$getresmin);
									if($transfers>$min)$transfers=$min;

									//gesamterhalt der rohstoffe
									$getres=$getresmin*$transfers;
									if($getres>$freeholdsystem)$getres=$freeholdsystem;

									//lagerkkomplex f�llen
									//echo 'rid: '.$rid.' amount: '.$getres.' time'.$transfers;
									change_systemhold_amount($_SESSION["sou_user_id"], $rid, $getres);

									//credits abziehen
									if($needcredits>0)change_credits($ums_user_id, $needcredits*(-1), 'Lagerkomplex-Komfortfunktion'); 

									//einen counter setzen, damit sonst nichts mehr zu machen geht
									$time=time()+60*$transfers;

									//skill verbessern
									change_skill($rid, $transfers);          			  	

									mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
									//seite neu laden
									header("Location: sou_main.php");
								}
								else $msg='<font color="#FF0000">Diese Rohstoffart ist hier nicht vorhanden.</font>';
							}
							else $msg='<font color="#FF0000">Der Lagerkomplex ist bereits voll.</font>';	
						}
						else $msg='<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font>';	
					}
					else $msg='<font color="#FF0000">Es ist kein Bergbaumodul vorhanden.</font>';      	        
				}
				else $msg='<font color="#FF0000">Es ist kein freier Frachtraum vorhanden.</font>';
			}
			else $msg='<font color="#FF0000">Das Geb&auml;ude ist nicht weit genug ausgebaut.</font>';


			//lock wieder entfernen
			$erg = releaseLock($_SESSION["sou_user_id"]); //L�sen des Locks und Ergebnisabfrage
			if ($erg){
			  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
			}else{
			  print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
			}
		}//lock ende
		else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
	}    
    
  	//lagerkomplex darstellen
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
    <td><b>Der Lagerkomplex von '.$owner_sysname.' (Fraktionsstufe '.$geb_level.')</b></td>
    <td width="120">&nbsp;</td>
    </tr></table>';
    rahmen1_oben($output);  	

  	$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';

  	$bg='cell1';
  	$output.='<tr align="center"><td class="'.$bg.'" colspan="5	">Hier kannst du Rohstoffe f&uuml;r die Fabriken und die Raumwerft einlagern.<br>';
  	$output.='Maximale Lagerkapazit&auml;t pro Rohstoff:  '.number_format($volumen, 0,"",".").' m&sup3;<br>';
    //spalten�berschrift
  	$bg='cell';
    $output.='<tr align="center"><td class="'.$bg.'"><b>Rohstoff</b></td><td class="'.$bg.'"><b>Lagerkomplex</b></td><td class="'.$bg.'"><b>Bergbaudauer</b></td></tr>';
    //alle rohstoffe durchparsen
    $ttid=0;
    for($i=0;$i<count($r_def);$i++)
	{
  	  //hintergrund bestimmen
  	  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  	  $output.='<tr align="center">';
  	  //rohstoffname
  	  if($geb_level<$r_def[$i][4])$resname='<font color="#FF0000">'.$r_def[$i][0].' (ab Geb&auml;udestufe '.$r_def[$i][4].')</font>';
  	  else $resname=$r_def[$i][0];
  	  $output.='<td class="'.$bg.'">'.$resname.'</td>';
  	  //lagerkomplexmenge
  	  $output.='<td class="'.$bg.'" align="right">'.number_format($complex_has[$i], 0,",",".").'</td>';
  	  
  	  //bergbaudauer
  	  
  	  $output.='<td class="'.$bg.'" align="center">';

      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=5" title="Rohstoffeinlagerung&5 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=15" title="Rohstoffeinlagerung&15 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=30" title="Rohstoffeinlagerung&30 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=45" title="Rohstoffeinlagerung&45 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
      $ttid++;
      $output.='
      &nbsp;<a id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=60" title="Rohstoffeinlagerung&60 Minuten Rohstoffe sammeln">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px"></a>';
  	  $ttid++;
		//abfrage f�r non-pa accounts
		if($ums_premium!=1){
			$jssecure='onclick="return confirm(\'Diese Aktion kostet Credits. Durchf&uuml;hren?\')"';
		}else{
			$jssecure='';
		}	  
	  $output.='
      &nbsp;<a '.$jssecure.' id="ttid'.$ttid.'" href="sou_main.php?action=systemholdpage&do=2&rid='.$i.'&min=480" title="Power Rohstoffeinlagerung&480 Fl&uuml;ge<br>Preis: 10 Credits (F&uuml;r Premium-Account-Nutzer kostenlos)">
      <img border="0" style="vertical-align: middle;" src="'.$gpfad.'abutton2.gif"  width="16px" height="16px"></a>';
  	  $output.='</td></tr>';
  	  $ttid++;
	  
	}
	$output.='</table>';

	echo $output;
  	
  	rahmen1_unten();
  	echo '<br>';
/*
  echo '<script language="JavaScript" type="text/javascript">';
  
  echo 'for(i=0;i<=200;i++)
	{
	  $("#ttid"+i).tooltip({ 
		        track: true, 
		        delay: 0, 
		        showURL: false, 
		        showBody: "&",
		        extraClass: "design1", 
		        fixPNG: true, 
		        opacity: 0.95
		  	  });
	} ';
  
  echo '</script>';  	
*/  	
  	
  }
  else echo '<br>Hier gibt es keinen Lagerkomplex.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';
}
else echo 'Hier gibt es kein Sonnensystem.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>