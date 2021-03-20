<?php
include "soudata/defs/resources.inc.php";
include "soudata/defs/gitems.inc.php";

//creditangebote 
$ai=0;
$angebot[$ai][name]='Bergbaubonus I';
$angebot[$ai][grafikname]='sym1.png';
$angebot[$ai][buff]='1x5x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=1;
$angebot[$ai][creditpreis]=10;
$ai++;
$angebot[$ai][name]='Bergbaubonus II';
$angebot[$ai][grafikname]='sym1.png';
$angebot[$ai][buff]='1x10x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=2;
$angebot[$ai][creditpreis]=19;
$ai++;
$angebot[$ai][name]='Bergbaubonus III';
$angebot[$ai][grafikname]='sym1.png';
$angebot[$ai][buff]='1x20x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=3;
$angebot[$ai][creditpreis]=37;
/*
$ai++;
$angebot[$ai][name]='Frachtraumbonus I';
$angebot[$ai][grafikname]='sym2.png';
$angebot[$ai][buff]='3x5x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=1;
$angebot[$ai][creditpreis]=10;
$ai++;
$angebot[$ai][name]='Frachtraumbonus II';
$angebot[$ai][grafikname]='sym2.png';
$angebot[$ai][buff]='3x10x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=2;
$angebot[$ai][creditpreis]=19;
$ai++;
$angebot[$ai][name]='Frachtraumbonus III';
$angebot[$ai][grafikname]='sym2.png';
$angebot[$ai][buff]='3x20x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=3;
$angebot[$ai][creditpreis]=37;

$ai++;
$angebot[$ai][name]='Reaktorbonus I';
$angebot[$ai][grafikname]='sym3.png';
$angebot[$ai][buff]='5x5x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=1;
$angebot[$ai][creditpreis]=10;
$ai++;
$angebot[$ai][name]='Reaktorbonus II';
$angebot[$ai][grafikname]='sym3.png';
$angebot[$ai][buff]='5x10x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=2;
$angebot[$ai][creditpreis]=19;
$ai++;
$angebot[$ai][name]='Reaktorbonus III';
$angebot[$ai][grafikname]='sym3.png';
$angebot[$ai][buff]='5x20x7';
$angebot[$ai][mapbuff]='';
$angebot[$ai][quality]=3;
$angebot[$ai][creditpreis]=37;
*/

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
// baoson angebote
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

$bai=0;
$bangebot[$bai]['name']=$gitem[0]['name'];
$bangebot[$bai]['desc']=$gitem[0]['desc'];
$bangebot[$bai]['quality']=1;
$bangebot[$bai]['baosin']=1;

$bai=1;
$bangebot[$bai]['name']=$specialres_def[0][1];
$bangebot[$bai]['desc']=$specialres_def[0][1];
$bangebot[$bai]['quality']=1;
$bangebot[$bai]['baosin']=1;

$bai=2;
$bangebot[$bai]['name']=$specialres_def[1][1];
$bangebot[$bai]['desc']=$specialres_def[1][1];
$bangebot[$bai]['quality']=1;
$bangebot[$bai]['baosin']=2;

$bai=3;
$bangebot[$bai]['name']=$specialres_def[2][1];
$bangebot[$bai]['desc']=$specialres_def[2][1];
$bangebot[$bai]['quality']=1;
$bangebot[$bai]['baosin']=3;

$bai=4;
$bangebot[$bai]['name']=$specialres_def[3][1];
$bangebot[$bai]['desc']=$specialres_def[3][1];
$bangebot[$bai]['quality']=2;
$bangebot[$bai]['baosin']=4;

$bai=5;
$bangebot[$bai]['name']=$specialres_def[4][1];
$bangebot[$bai]['desc']=$specialres_def[4][1];
$bangebot[$bai]['quality']=2;
$bangebot[$bai]['baosin']=5;

$bai=6;
$bangebot[$bai]['name']=$specialres_def[5][1];
$bangebot[$bai]['desc']=$specialres_def[5][1];
$bangebot[$bai]['quality']=2;
$bangebot[$bai]['baosin']=6;

$bai=7;
$bangebot[$bai]['name']=$specialres_def[6][1];
$bangebot[$bai]['desc']=$specialres_def[6][1];
$bangebot[$bai]['quality']=3;
$bangebot[$bai]['baosin']=7;

$bai=8;
$bangebot[$bai]['name']=$specialres_def[7][1];
$bangebot[$bai]['desc']=$specialres_def[7][1];
$bangebot[$bai]['quality']=3;
$bangebot[$bai]['baosin']=8;

$bai=9;
$bangebot[$bai]['name']=$specialres_def[8][1];
$bangebot[$bai]['desc']=$specialres_def[8][1];
$bangebot[$bai]['quality']=3;
$bangebot[$bai]['baosin']=9;

$bai=10;
$bangebot[$bai]['name']=$specialres_def[9][1];
$bangebot[$bai]['desc']=$specialres_def[9][1];
$bangebot[$bai]['quality']=4;
$bangebot[$bai]['baosin']=10;

$bai=11;
$bangebot[$bai]['name']='Geistige St&auml;rke (1 Tag)';
$bangebot[$bai]['desc']=$specialres_def[9][1];
$bangebot[$bai]['mapbuff']='1x0x1';
$bangebot[$bai]['quality']=4;
$bangebot[$bai]['baosin']=50;

$bai=12;
$bangebot[$bai]['name']='Geistige St&auml;rke (2 Tage)';
$bangebot[$bai]['desc']=$specialres_def[9][1];
$bangebot[$bai]['mapbuff']='1x0x2';
$bangebot[$bai]['quality']=4;
$bangebot[$bai]['baosin']=125;

$bai=13;
$bangebot[$bai]['name']='Geistige St&auml;rke (3 Tage)';
$bangebot[$bai]['desc']=$specialres_def[9][1];
$bangebot[$bai]['mapbuff']='1x0x3';
$bangebot[$bai]['quality']=4;
$bangebot[$bai]['baosin']=250;


echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

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
  
	
  $geb_level=get_bldg_level($owner_id, 5);
  if($geb_level>0 OR $numsrb==1)
  {
	
	
//daten zur ansicht
echo '<form action="sou_main.php" method="POST" name="f">';
echo '<input type="hidden" name="action" value="baonadapage">';

if($msg!='')
{
  rahmen2_oben();
  echo $msg;
  rahmen2_unten();
  echo '<br>';
}

$output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Station der Bao-Nada</b></td>
<td width="120"><a href="sou_main.php?action=modulholdpage"><div class="b1">Modulkomplex</div></a></td>
</tr></table>';
rahmen1_oben($output);

//bao-nada-skala auslesen
/*
$fraktionswerte=get_baonadaskala();

//fraktionsstatus festlegen
$fraktionsstati[0]='Beh&uuml;tetes Volk';
$fraktionsstati[1]='Junges Volk';
$fraktionsstati[2]='Bewahrer des Gleichgewichts';
$fraktionsstati[3]='St&ouml;rer des Gleichgewichts';
$fraktionsstati[4]='Zerst&ouml;rer des Gleichgewichts';
$fraktionsstatus=2;


if($fraktionswerte[$player_fraction-1]>=125)$fraktionsstatus=3;
if($fraktionswerte[$player_fraction-1]>=175)$fraktionsstatus=4;

if($fraktionswerte[$player_fraction-1]<=75)$fraktionsstatus=1;
if($fraktionswerte[$player_fraction-1]<=50)$fraktionsstatus=0;


//$fraktionsstatus=1;

//multiplikator laut status
$fm=0;
if($fraktionsstatus==0)$fm=3;
elseif($fraktionsstatus==1)$fm=1.5;

//preis für ein fraktionsstatusmodul
$fpreis=1000;
*/

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//fraktionsangebot-kauf
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/*
if($_REQUEST["do"]=='1' AND ($fraktionsstatus==0 OR $fraktionsstatus==1))
{
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
	//überprüfen ob platz im modulkomplex ist
    //zuerst die dortigen hinterlegen module zählen
	$db_datenx=mysql_query("SELECT id FROM sou_ship_module WHERE user_id='$player_user_id' AND location=1",$soudb);
	$num_lager = mysql_num_rows($db_datenx);
			
	//feststellen wieviel ins lager geht
	$max_lager=get_max_frac_bldg_level(5)*2;
            
    if($num_lager < $max_lager)
    {
      //überprüfen ob man genug geld hat
      $hasmoney=has_money($player_user_id);
      if($hasmoney>=$fpreis)
      {
      	//geld abziehen
      	change_money($player_user_id, $fpreis*(-1));
      	
      	//modul berechnen und im modulkomplex hinterlegen
  		$db_daten=mysql_query("SELECT MAX(giveenergy) AS giveenergy, MAX(canmine) AS canmine, MAX(hasspace) AS hasspace FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0",$soudb);
  	    $row = mysql_fetch_array($db_daten);

        $sql="INSERT INTO sou_ship_module (user_id, fraction, name, craftedby, lifetime, hasspace, needspace, needenergy, giveenergy, canmine, 
            givelife, givesubspace, givecenter, givehyperdrive, giveresearch, location, uomlock, quality) VALUES 
            ('$player_user_id', '$player_fraction', 'Multimodul SM-H-$fraktionsstatus', 'Bao-Nada', '".(time()+30*24*3600)."', '".round($row[hasspace]*$fm)."' , '0', $row[giveenergy], $row[giveenergy], '".round($row["canmine"]*$fm)."', '0', '0', '0', '0', '0', 1, 1, 1)";
      
         //echo '<br><br>'.$sql.'<br><br>';
        mysql_query($sql,$soudb);
        
        //msg ausgeben
      	echo '<font color="#00FF00">Das Modul ist jetzt im Modulkomplex verf&uuml;gbar.</font><br>';
      	
      }
      else echo '<font color="#FF0000">Es sind nicht genug Zastari vorhanden.</font><br>';
    }
	else echo '<font color="#FF0000">Es ist nicht genug Platz im Modulkomplex vorhanden.</font><br>';
 	
  	
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
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//credit angebot - kauf
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
if($_REQUEST["do"]=='2')
{
  $aid=intval($_REQUEST['aid']);
  $fpreis=$angebot[$aid][creditpreis];
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
	//überprüfen ob platz im modulkomplex ist
    //zuerst die dortigen hinterlegen module zählen
	$db_datenx=mysql_query("SELECT id FROM sou_ship_module WHERE user_id='$player_user_id' AND location=1",$soudb);
	$num_lager = mysql_num_rows($db_datenx);
			
	//feststellen wieviel ins lager geht
	$max_lager=get_max_frac_bldg_level(5)*2;
            
    if($num_lager < $max_lager)
    {
      //überprüfen ob man genug geld hat
      $hasmoney=has_credits($ums_user_id);
      if($hasmoney>=$fpreis)
      {
      	//überprüfen ob es das angebot auch wirklich gibt
      	if(isset($angebot[$aid][name]))
      	{
      	  //geld abziehen
      	  change_credits($ums_user_id, $fpreis*(-1), 'EA - Bao-Nada-Kauf '.$angebot[$aid][name]);
      	
      	  //modul im modulkomplex hinterlegen
          $sql="INSERT INTO sou_ship_module (user_id, fraction, name, craftedby, lifetime, hasspace, needspace, needenergy, giveenergy, canmine, 
            givelife, givesubspace, givecenter, givehyperdrive, giveresearch, location, uomlock, quality, buff, mapbuff) VALUES 
            ('$player_user_id', '$player_fraction', '".$angebot[$aid][name]."', 'Bao-Nada', '0', '0' , '0', '0', '0', '0', '0', '0', '0', '0', '0', 1, '1', '".$angebot[$aid][quality]."', '".$angebot[$aid][buff]."', '".$angebot[$aid][mapbuff]."')";
      
          //echo '<br><br>'.$sql.'<br><br>';
          mysql_query($sql,$soudb);
          
          //status	
          //mail('issomad@die-ewigen.com', 'EA Itemkauf '.$angebot[$aid][name].'('.$angebot[$aid][creditpreis].') - '.$_SESSION["sou_spielername"].' - Fraktion '.$player_fraction, '');
        
          //msg ausgeben
      	  echo '<font color="#00FF00">Das Modul ist jetzt im Modulkomplex verf&uuml;gbar.</font><br>';
      	}
      	else echo '<font color="#FF0000">Dieses Angebot existiert nicht.</font><br>';
      }
      else echo '<font color="#FF0000">Es sind nicht genug Credits vorhanden.</font><br>';
    }
	else echo '<font color="#FF0000">Es ist nicht genug Platz im Modulkomplex vorhanden.</font><br>';
 	
  	
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

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//baosin angebot - kauf
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
if($_REQUEST["do"]=='3')
{
  $baid=intval($_REQUEST['baid']);
  $fpreis=$bangebot[$baid]['baosin'];
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
    //überprüfen ob man genug geld hat
    $hasmoney=has_baosin($player_user_id);
    if($hasmoney>=$fpreis)
    {
      //überprüfen ob es das angebot auch wirklich gibt
      if(isset($bangebot[$baid]['name']))
      {
      	//geld abziehen
      	change_baosin($player_user_id, $fpreis*(-1));
      	
      	//mentalstick bergbau
        if($baid==0){
          for($i=0;$i<=9;$i++)change_skill($i, 60);
          echo '<font color="#00FF00">Der Mentalstick wurde verwendet.</font>';
        }
        //specialres
        if($baid>=1 AND $baid<=10)
        {
		  change_specialres($player_user_id,$baid-1, 1);
          echo '<font color="#00FF00">Die Ware befindet sich jetzt an Bord deines Raumschiffes.</font>';
        }
		//Geistige Stärke
        if($baid>=11 AND $baid<=13){
          $sql="INSERT INTO sou_ship_module (user_id, fraction, name, craftedby, lifetime, hasspace, needspace, needenergy, giveenergy, canmine, 
            givelife, givesubspace, givecenter, givehyperdrive, giveresearch, location, uomlock, quality, buff, mapbuff) VALUES 
            ('$player_user_id', '$player_fraction', '".$bangebot[$baid]['name']."', 'Bao-Nada', '0', '0' , '0', '0', '0', '0', '0', '0', '0', '0', '0', 1, '1', '".$bangebot[$baid]['quality']."', '".$bangebot[$baid]['buff']."', '".$bangebot[$baid]['mapbuff']."')";
			
		  mysql_query($sql,$soudb);
			
			echo '<font color="#00FF00">Die Ware befindet sich jetzt an Bord deines Raumschiffes.</font>';
        }		
      }
      else echo '<font color="#FF0000">Dieses Angebot existiert nicht.</font><br>';
    }
    else echo '<font color="#FF0000">Es sind nicht genug Baosin vorhanden.</font><br>';
  	
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



$colspan=3;

echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td colspan="'.$colspan.'" class="cell">Das Volk der Bao-Nada ist ein Dienervolk der W&auml;chter der Erbauer. Sie unterhalten auf vielen Welten Stationen und bieten dort Ihre Waren an.
<br>Dein Verm&ouml;gen: &nbsp;'.number_format($player_money, 0,"",".").' <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari"> / '.
number_format(has_credits($ums_user_id), 0,"",".").' <img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits"> /  '.
number_format(has_baosin($player_user_id), 0,"",".").' <img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin">
</td></tr>';
/*
echo '<tr><td colspan="'.$colspan.'" class="cell1">Fraktionsstatus: '.$fraktionsstati[$fraktionsstatus].' ('.number_format($fraktionswerte[$player_fraction-1], 2,",",".").'%)</td></tr>';
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//angebote aufgrund des fraktionsstatus
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

//um an anfang keine probleme zu bekommen wird es erst nach 100 tagen/jahren aktiv
$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
if($row["year"]>100)
{
  echo '<tr><td colspan="'.$colspan.'" class="cell" align="center"><b>Fraktionsstatus-Angebote</b></td></tr>';
  
  echo '<tr align="center" class="cell1"><td><b>Modul</b></td><td><b>Eigenschaften</b></td><td><b>Preis</b></td></tr>';
  
  //die items werden je nach fraktionsstatus angeboten
  //die 2 schlechtestens fraktionen bekommen module angeboten
  if($fraktionsstatus==0 OR $fraktionsstatus==1)
  {
  
  	//folgende module sollen es verbessert geben: reaktor, bergbaumodul, lagermodul
  	//echo '<tr><td><table width="100%" border="0" cellpadding="1" cellspacing="1">';
  	//reaktor
  	
  	//$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE giveenergy=(SELECT MAX(giveenergy) FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0)",$soudb);
  	$db_daten=mysql_query("SELECT MAX(giveenergy) AS giveenergy, MAX(canmine) AS canmine, MAX(hasspace) AS hasspace FROM `sou_frac_techs` WHERE f1lvl>0 OR f2lvl>0 OR f3lvl>0 OR f4lvl>0 OR f5lvl>0 OR f6lvl>0",$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      echo '<tr class="cell"><td>&nbsp;Multimodul SM-H-'.$fraktionsstatus.'</td><td>
      &nbsp;Ben&ouml;tiger Platz: 50 m&sup3;<br>
      &nbsp;Vorhandener Lagerplatz: '.number_format(round($row["hasspace"]*$fm), 0,",",".").' m&sup3;<br>
      &nbsp;Gelieferte Energie: '.number_format($row["giveenergy"], 0,",",".").' EE<br>
      &nbsp;Ben&ouml;tigte Energie: '.number_format($row["giveenergy"], 0,",",".").' EE<br>
      &nbsp;F&ouml;rderkapazit&auml;t: '.number_format(round($row["canmine"]*$fm), 0,",",".").' m&sup3;/Min bei Standarddichte<br>
      &nbsp;Aktiv bis zum '.date("d.m.Y H:i", time()+30*24*3600).'
</td>
	  <td align="center"><a href="sou_main.php?action=baonadastationpage&do=1"><div class="b1">'.number_format($fpreis, 0,",",".").' <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari"></div></a></td></tr>';
    }
  	
    //echo '</table></td></tr>';
  }
  else echo '<tr><td colspan="'.$colspan.'" class="cell" align="center">Es sind keine Angebote verf&uuml;gbar</td></tr>';
	
}
else echo '<tr><td colspan="'.$colspan.'" class="cell" align="center"><b>Fraktionsstatus-Angebote stehen leider noch nicht zur Verf&uuml;gung</b></td></tr>';
echo '</table>';
*/
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//angebote für credits
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td class="cell" align="center"><b>Angebote f&uuml;r Credits <img src="'.$gpfad.'a8.gif" alt="Credits" title="Credits"></b></td></tr>';

//angebote ausgeben

//tooltip bauen
echo '<script language="javascript">';
echo 'var atip = new Array();';
for($i=0;$i<count($angebot);$i++)
{
  $preis='<br>Preis: '.number_format($angebot[$i][creditpreis], 0,"",".").' <img src='.$gpfad.'a8.gif>';	
  $atip[$i] = make_modul_name_js($angebot[$i]).'&'.make_modul_info($angebot[$i]).$preis;
}
echo '</script>';

$angebote='';
for($i=0;$i<count($angebot);$i++)
{
  
  $angebote.='<a href="sou_main.php?action=baonadastationpage&do=2&aid='.$i.'"><img border="0" src="'.$gpfad.
  	$angebot[$i]['grafikname'].'" title="'.$atip[$i].'"></a>';
}

echo '<tr><td class="cell1" align="center">'.$angebote.'</td></tr>';

echo '</table>';
echo '<br>';
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//angebote für baosin
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td class="cell" align="center" colspan="2"><b>Angebote f&uuml;r Baosin <img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin"></b></td></tr>';
echo '<tr class="cell1" align="center"><td><b>Artikel</b></td><td><b>Preis</b></td></tr>';
$angebote='';
for($i=0;$i<count($bangebot);$i++)
{
  if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
  echo '<tr class="'.$bg.'"><td id="b'.$i.'" title="&'.$bangebot[$i]['desc'].'"><font color="'.$colors_items[$bangebot[$i]['quality']].'">'.
  $bangebot[$i]['name'].'</font></td><td align="right"><a href="sou_main.php?action=baonadastationpage&do=3&baid='.$i.'">'.
  $bangebot[$i]['baosin'].' <img src="'.$gpfad.'a28.gif" alt="Baosin" title="Baosin"></a></td></tr>';
}

echo '</table>';

rahmen1_unten();
  }
  else echo 'Ohne Modulkomplex ist kein Zugriff m&ouml;glich.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
}
else echo 'Hier gibt es kein Sonnensystem.<br><a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';
rahmen0_unten();
echo '<br>';

echo '</div>';//center-div
echo '</form>';

echo '<script language="JavaScript" type="text/javascript">';
echo 'for(i=0;i<=200;i++)
{
  $("#b"+i).tooltip({ 
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

die('</body></html>');
?>