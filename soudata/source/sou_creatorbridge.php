<?php

//brückenkoordinaten definieren
$index=0;
$bp[$index]=array(0,2250);
$bpinfo[$index]='Omega I&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
$index++;
$bp[$index]=array(1949,1125);
$bpinfo[$index]='Omega II&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
$index++;
$bp[$index]=array(1949,-1125);
$bpinfo[$index]='Omega III&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
$index++;
$bp[$index]=array(0,-2250);
$bpinfo[$index]='Omega IV&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
$index++;
$bp[$index]=array(-1949,-1125);
$bpinfo[$index]='Omega V&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
$index++;
$bp[$index]=array(-1949,1125);
$bpinfo[$index]='Omega VI&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
//virtuelle brücke ins heimatsystem
//auslesen ob man die technologie hat
$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60008 AND f".$player_fraction."lvl>0",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $index++;
  $bp[$index]=array($sv_omega_position[0][$player_fraction-1][0], $sv_omega_position[0][$player_fraction-1][1]);
  $bpinfo[$index]='Virtuelle Omegabr&uuml;cke (Heimatsystem)&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
  if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
}

//virtuelle brücke deep fraction
//auslesen ob man die technologie hat
$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60010 AND f".$player_fraction."lvl>0",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $index++;
  $bp[$index]=array($sv_omega_position[1][$player_fraction-1][0], $sv_omega_position[1][$player_fraction-1][1]);
  $bpinfo[$index]='Virtuelle Omegabr&uuml;cke Deep Fraction&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
  if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
}

//virtuelle brücke punkt aganra
//auslesen ob man die technologie hat
$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60009 AND f".$player_fraction."lvl>0",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $index++;
  $bp[$index]=array(199995,0);
  $bpinfo[$index]='Virtuelle Omegabr&uuml;cke Punkt Aganra&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
  if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
}

//virtuelle brücke angus bar
//auslesen ob man die technologie hat
$db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE tech_id=60012 AND f".$player_fraction."lvl>0",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  $index++;
  $bp[$index]=array(-1000005,0);
  $bpinfo[$index]='Virtuelle Omegabr&uuml;cke Angus Bar&Koordinaten: '.$bp[$index][0].':'.$bp[$index][1].'<br><br>';
  if($bp[$index][0]==$player_x AND $bp[$index][1]==$player_y){$bpinfo[$index].='Du befindest dich hier.';}else{$bpinfo[$index].='Reisezeit: 5 Minuten';}
}



$atip = "Information&Die Omega-Br&uuml;cke wird auch als das Tor zu den Sternen bezeichnet. Durch eine Hyperraumfalte werden 2 Punkte verbunden und diese lassen sich innerhalb k&uuml;rzester Zeit erreichen.";

//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

//zuerst schauen ob der spieler sich bei einer brücke befindet
$isnot=1;
for($i=0;$i<count($bp);$i++)
{
  if($bp[$i][0]==$player_x AND $bp[$i][1]==$player_y)$isnot=0;
}

if($isnot==0)
{
  $row = mysql_fetch_array($db_daten);
  $srb_special=$row["special"];
  
  //überprüfen ob man die entsprechende quest schon hat
  $db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE id=5 AND done=1 AND fraction='$player_fraction'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num!=1)
  {
  	echo '<br>';
  	rahmen2_oben();
  	echo 'Deiner Fraktion fehlt der Omega-Key um auf die Omega-Br&uuml;cke zugreifen zu k&ouml;nnen.';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }
  
  ///////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////
  // benutzung der brücke
  ///////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////
  
  if($_REQUEST["gox"] OR $_REQUEST["goy"])
  {
  	//überprüfen ob es die zielkoordinaten gibt
  	$gox=intval($_REQUEST["gox"]);
  	$goy=intval($_REQUEST["goy"]);
	$isnot=1;
	for($i=0;$i<count($bp);$i++)
	{
  	  if($bp[$i][0]==$gox AND $bp[$i][1]==$goy)$isnot=0;
	}

	if($isnot==0)
	{
	  //nur durchführen, wenn die koordinaten anders sind
	  if($player_x!=$gox OR $player_y!=$goy)
	  {
	    //neue koordinaten setzen
	  	$player_x=$gox;
	  	$player_y=$goy;

	  	//überprüfen ob man den zielsektor sehen kann, falls nicht sichtbar machen
	  	$sbx=round($player_x/15);
  	  	$sby=round($player_y/15);
	  	$searchx=$sbx*15;
	  	$searchy=$sby*15;
	  	$db_daten=mysql_query("SELECT fraction FROM sou_map_known WHERE x='$searchx' AND y='$searchy' AND fraction='$fraction'",$soudb);
	  	$num = mysql_num_rows($db_daten);
	  	if($num==0)//das gebiet ist noch unbekannt, also karte hinterlegen
	  	{
	  	  $time=time();
	  	  mysql_query("INSERT INTO sou_map_known (fraction, x, y, expltime) VALUES ($player_fraction, '$searchx', '$searchy', '$time')",$soudb);
	  	}
	  
	  	//reise starten 
	  	$rz=300; 	
      	$time=time()+$rz;
      	mysql_query("UPDATE sou_user_data SET atimer1typ=3, atimer1time='$time', x='$gox', y='$goy' WHERE user_id='$player_user_id'",$soudb);
   	  	header("Location: sou_main.php");
	  
	  }  	  
	}
  }
  
  ///////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////
  // brücke darstellen
  ///////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////
  
  echo '<br>';
  $output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage" class="btn">System</a></td>
<td><b>Omega-Br&uuml;cke</b> <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" title="'.$atip.'"></td>
<td width="120"><a href="sou_main.php?action=sectorpage" class="btn">Sektor</a></td>
</tr></table>';
  rahmen1_oben($output); 
 
  echo '<div style="width: 100%; height: 400px; position: relative;">';
  
  //die hauptbrückenelemente darstellen
  echo '<span style="width: 400px; height: 400px; position: relative; float: left;">';
  
  echo '<span style="position: absolute; top:0px; left: 0px;"><img src="'.$gpfad.'bgpic2.jpg"></span>';
  
  echo '<span id="cb0" style="position: absolute; top:0px; left: 184px; border: 1px solid #357a80;" title="'.$bpinfo[0].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[0][0].'&goy='.$bp[0][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '<span id="cb1" style="position: absolute; top:80px; left: 336px; border: 1px solid #357a80;" title="'.$bpinfo[1].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[1][0].'&goy='.$bp[1][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '<span id="cb2" style="position: absolute; top:288px; left: 336px; border: 1px solid #357a80;" title="'.$bpinfo[2].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[2][0].'&goy='.$bp[2][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '<span id="cb3" style="position: absolute; top:368px; left: 184px; border: 1px solid #357a80;" title="'.$bpinfo[3].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[3][0].'&goy='.$bp[3][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '<span id="cb4" style="position: absolute; top:288px; left: 32px; border: 1px solid #357a80;" title="'.$bpinfo[4].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[4][0].'&goy='.$bp[4][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '<span id="cb5" style="position: absolute; top:80px; left: 32px; border: 1px solid #357a80;" title="'.$bpinfo[5].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[5][0].'&goy='.$bp[5][1].'"><img src="'.$gpfad.'ssrb2.gif" width="32" height="32" border="0"></a></span>';
  echo '</span>';
  
  //ggf. weitere brückenelemente darstellen
  if(count($bp)>6)
  {
  	echo '<div style="width: 200px; height: 400px; position: relative; margin-left: 150px; background-color: #000000;">';
  	echo '<b>Omega-Sonderziele</b><br><br>';
    for($i=6;$i<count($bp);$i++)
    {
  	  echo '<span id="cb'.$i.'" title="'.$bpinfo[$i].'"><a href="sou_main.php?action=creatorbridgepage&gox='.$bp[$i][0].'&goy='.$bp[$i][1].'"><img src="'.$gpfad.'ssrb2.gif" width="64" height="64" border="0"></a></span>';
    }
    echo '</div>';
  }
  
  echo '</div>';
  
  rahmen1_unten();
  
  echo '<br>';
}
else echo '<br>Hier gibt es die gew&uuml;nschte Konstruktion nicht. <a href="sou_main.php?action=systempage&cboost=1" class="btn">System</a><br><br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');

/*
INSERT INTO `sou_server_main`.`sou_frac_techs` (`tech_id`, `tech_name`, `tech_vor`, `need_tech`, `bldg_build`, `bldg_level`, `modulcost`, `sort_id`, `f1lvl`, `f2lvl`, `f3lvl`, `f4lvl`, `f5lvl`, `f6lvl`, `f1b1`, `f1b2`, `f1b3`, `f1b4`, `f1b5`, `f1b6`, `f1b7`, `f1b8`, `f1b9`, `f1b10`, `f2b1`, `f2b2`, `f2b3`, `f2b4`, `f2b5`, `f2b6`, `f2b7`, `f2b8`, `f2b9`, `f2b10`, `f3b1`, `f3b2`, `f3b3`, `f3b4`, `f3b5`, `f3b6`, `f3b7`, `f3b8`, `f3b9`, `f3b10`, `f4b1`, `f4b2`, `f4b3`, `f4b4`, `f4b5`, `f4b6`, `f4b7`, `f4b8`, `f4b9`, `f4b10`, `f5b1`, `f5b2`, `f5b3`, `f5b4`, `f5b5`, `f5b6`, `f5b7`, `f5b8`, `f5b9`, `f5b10`, `f6b1`, `f6b2`, `f6b3`, `f6b4`, `f6b5`, `f6b6`, `f6b7`, `f6b8`, `f6b9`, `f6b10`, `needspace`, `hasspace`, `needenergy`, `giveenergy`, `canmine`, `givelife`, `givesubspace`, `givecenter`, `givehyperdrive`, `giveresearch`, `giveweapon`, `giveshield`, `canrecover`, `canclone`) VALUES ('60012', 'Virtuelle Omega-Brücke Angus Bar', 'B1x25;Zx100000000;30000000x5;25000000x6', '60008', '0', '0', '', '13', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
 */

?>