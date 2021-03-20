<?php
include "soudata/defs/resources.inc.php";

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// bergungseinheit anfordern
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/*
if($_REQUEST["do"]==1 AND $player_atimer1time<time())
{
  //zuerst pr�fen ob man evtl. schon in einem sonnensystem seiner fraktion ist
  $db_daten=mysql_query("SELECT id, sysname, fraction FROM sou_map WHERE x='$player_x' AND y='$player_y' AND fraction='$player_fraction'",$soudb);
  $num = mysql_num_rows($db_daten);
  if($num==0)
  {
	//das n�chstgelegene sonnensystem suchen
	$distance=999999;
	$target_x=$player_x;
	$target_y=$player_y;
	$db_daten=mysql_query("SELECT * FROM `sou_map` WHERE fraction='$player_fraction'",$soudb);
  	while($row = mysql_fetch_array($db_daten))
    {
      $tx=$row["x"];
      $ty=$row["y"];
      //entfernung berechnen
      $s1=$player_x-$tx;
      $s2=$player_y-$ty;
      if($s1<0)$s1=$s1*(-1);
      if($s2<0)$s2=$s2*(-1);
      $s1=pow($s1,2);
      $s2=pow($s2,2);
      $w1=$s1+$s2;
      $w3=sqrt($w1);
      
      //wenn die entfernung kleiner ist als die mininmaldistanz, dann die neuen daten verwenden
      if($w3<$distance)
      {
      	$distance=$w3;
		$target_x=$row["x"];
		$target_y=$row["y"];
      }
    } 	
	//reisezeit berechnen
	$rz=$distance*3600;
	if($rz>48*3600)$rz=3600;
	$time=time()+$rz;
    
	mysql_query("UPDATE sou_user_data SET atimer1typ=2, atimer1time='$time', x='$target_x', y='$target_y' WHERE user_id='$player_user_id'",$soudb);
   	header("Location: sou_main.php");
    
	//$msg=$distance.': '.$target_x.' : '.$target_y.' : '.$rz;
  }
  else $msg='<font color="#FF0000">Du befindest Dich bereits in einem Sonnensystem Deiner Fraktion.</font>';
}
*/
//daten zur ansicht
echo '<br><br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

//messages anzeigen
if($msg!='')
{
  rahmen2_oben();
  echo $msg;
  rahmen2_unten();
  echo '<br>';
}

//seitenteiler - anfang
echo '<table><tr><td width="60%">';

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// schiffs�bersicht
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Dein Raumschiff</b></div>');
echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td valign="top" align="right" width="50%"><img src="'.$gpfad.'v1.png"></td><td valign="top" align="left" width="50%">';
echo 'Raumschiffname: '.$_SESSION["sou_shipname"];
echo '<br>Schiffsgr&ouml;&szlig;e: '.number_format($player_ship_diameter, 0,"",".").' Meter';

echo '</td></tr></table>';

rahmen1_unten();

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//modul�bersicht
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

echo '<br>';

    //berechnen wieviele module man haben kann
    $module_max=6+round(sqrt($player_ship_diameter));

  	$gesamtneedspace=0;
	$gesamtneedenergy=0;
	$gesamtgiveenergy=0;
	$gesamthasspace=0;
	$gesamtcanmine=0;  	
	$gesamtgivelife=0;
	$gesamtgivesubspace=0;
	$gesamtgivecenter=0;
	$gesamtgiveresearch=0;
	$gesamtgiveweapon=0;
	$gesamtgiveshield=0;
	$gesamtcanrecover=0;
	$gesamtcanclone=0;
	
	
  	//alle module auslesen und auswerten
  	$output1='<table width="100%" border="0" cellpadding="1" cellspacing="0">';
  	$output2='<table width="100%" border="0" cellpadding="1" cellspacing="0">';
  	
  	$atipc=0;$modulkomplexc=0;$moduleinraumschiff=0;
    $db_daten=mysql_query("SELECT * FROM `sou_ship_module` WHERE user_id='$_SESSION[sou_user_id]' AND (location=0 OR location=1) ORDER BY name",$soudb);
    while($row = mysql_fetch_array($db_daten))
    {
      //js-tooltip bauen
      
      $atip[$atipc] = make_modul_name_js($row).'&'.make_modul_info($row);
      
      //link zur modull�schung
      $deletelink='';
      
      //link zur modulentfernung
      $removelink1='';
      $removelink2='';
    	
      if($row["location"]==0)//das modul ist im schiff
      {
        //daten f�r die schiffszusammenfassung
      	$gesamtneedspace+=$row["needspace"];
    	$gesamtneedenergy+=$row["needenergy"];
    	$gesamtgiveenergy+=$row["giveenergy"];
    	$gesamthasspace+=$row["hasspace"];
    	$gesamtcanmine+=$row["canmine"];
    	$gesamtgivelife+=$row["givelife"];
    	$gesamtgivesubspace+=$row["givesubspace"];
    	$gesamtgivecenter+=$row["givecenter"];
    	$gesamtgiveresearch+=$row["giveresearch"];
    	$gesamtgiveweapon+=$row["giveweapon"];
    	$gesamtgiveshield+=$row["giveshield"];
    	$gesamtcanrecover+=$row['canrecover'];
    	$gesamtcanclone+=$row['canclone'];    	
    	
    	if($row["givehyperdrive"]>0)
    	{
  		  $speed=calc_hyperdrive_speed($row["givehyperdrive"]);
  		  if($speed>$savespeed)$savespeed=$speed;    	
    	}
    	
      	//hintergrund bestimmen
        if ($c1a==0){$c1a=1;$bg='cell1';}else{$c1a=0;$bg='cell';}
  		$output1.='<tr align="left" title="'.$atip[$atipc].'")">';
  		//modulname
  		$output1.='<td class="'.$bg.'">'.$removelink1.' '.make_modul_name($row).' '.$deletelink.'</td>';
  		//modulinfo
  		//$output1.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		$output1.='</tr>';
  		$moduleinraumschiff++;
      }
      else //das modul ist im modulkomplex
      {
        //hintergrund bestimmen
        if ($c1b==0){$c1b=1;$bg='cell1';}else{$c1b=0;$bg='cell';}      	
  		$output2.='<tr align="left" title="'.$atip[$atipc].'")">';
  		//modulname
  		$output2.='<td class="'.$bg.'">'.$deletelink.' '.make_modul_name($row).' '.$removelink2.'</td>';
  		//modulinfo
  		//$output2.='<td class="'.$bg.'">'.make_modul_info($row).'</td>';
  		$output2.='</tr>';
  		$modulkomplexc++;
      }
      $atipc++;
    }
  	$output1.='</table>';
  	$output2.='</table>';
    echo '</script>';

    
//�berschrift ausgeben
rahmen1_oben('<div align="center"><b>Module im Raumschiff</b></div>');

echo $output1;

rahmen1_unten();

//seitenteiler
echo '</td><td width="40%" valign="top">';

//aktionsm�glichkeiten
rahmen1_oben('<div align="center"><b>Aktionsm&ouml;glichkeiten</b></div>');

echo '<div align="left"><a href="sou_main.php?action=systempage" class="btn">zur&uuml;ck</a></div>';

rahmen1_unten();

echo '<br>';
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//  spezialfrachtraum
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
rahmen1_oben('<div align="center"><b>Spezialfrachtraum</b></div>');

//rohstoffanzeige bauen
$output='<table width="100%" border="0" cellpadding="1" cellspacing="0">';

for($i=0;$i<count($specialres_def);$i++)
{
  if($player_specialres[$i+1]>0)
  {
    //hintergrund bestimmen
    if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

    $output.='<tr align="left">';
    //rohstoffname
    $output.='<td class="'.$bg.'">'.$specialres_def[$i][1].': '.$player_specialres[$i+1].'</td>';
    $output.='</tr>';
  }
}
$output.='</table>';

echo $output;

rahmen1_unten();

echo '<br><br><br><br><br><br><br><br><br><br>';

rahmen1_oben('<div align="center"><b>Zusammenfassung</b></div>');

//moduleanzeige bauen

  //buffs
  $addvaluecanmine_absolut=0;
  $addvaluecanmine_percentage=0;
  $addvaluehasspace_absolute=0;
  $addvaluehasspace_percentage=0;
  $addvaluegiveenergy_absolute=0;
  $addvaluegiveenergy_percentage=0;
  //buffs auslesen
  $time=time();
  $db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$player_user_id' AND time>'$time'",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    if($row[typ]==1) $addvaluecanmine_percentage+=$row[value];
    elseif($row[typ]==2) $addvaluecanmine_absolut+=$row[value];
    //elseif($row[typ]==3) $addvaluehasspace_percentage+=$row[value];
    elseif($row[typ]==3) $addvaluecanmine_percentage+=$row[value];
    elseif($row[typ]==4) $addvaluehasspace_absolute+=$row[value];
    elseif($row[typ]==5) $addvaluegiveenergy_percentage+=$row[value];
    elseif($row[typ]==6) $addvaluegiveenergy_absolute+=$row[value];  
  }

echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
echo '<tr align="left"><td class="cell1">';
//$volumen = floor(4/3 * pow(($player_ship_diameter*0.95) / 2, 3) * pi());
//echo 'Nutzvolumen: '.number_format($volumen, 0,",",".").' m&sup3;';
//echo '<br>Freies Nutzvolumen: '.number_format($volumen-$gesamtneedspace, 0,",",".").' m&sup3;';

if($gesamtgivelife<1){$str1='<font color="#FF0000">';$str2='</font>';}else{$str1='';$str2='';}
echo 'Lebenserhaltung (Personen): '.$str1.number_format($gesamtgivelife, 0,",",".").$str2;

echo '<br>Klonkapazit&auml;t: '.number_format($gesamtcanclone, 0,",",".").' ZQ';

if($gesamtgivecenter>0)$str='<font color="#00FF00">vorhanden</font>';else $str='<font color="#FF0000">nicht vorhanden</font>';
echo '<br>Raumschiffzentrale: '.$str;

if($gesamtgivesubspace>0)$str='<font color="#00FF00">vorhanden</font>';else $str='<font color="#FF0000">nicht vorhanden</font>';
echo '<br>Unterlichtantrieb: '.$str;

if($savespeed>0)echo '<br>&Uuml;berlichtgeschwindigkeit: '.number_format($savespeed, 0,",",".").' Sek/Lichtjahr';
else echo '<br>&Uuml;berlichtgeschwindigkeit: <font color="FF0000">Modul fehlt</font>';

echo '<br>Forschungskapazit&auml;t: '.number_format($gesamtgiveresearch, 0,",",".").' FP';
echo '<br>Waffenkapazit&auml;t: '.number_format($gesamtgiveweapon, 0,",",".").' EE';
echo '<br>Schildkapazit&auml;t: '.number_format($gesamtgiveshield, 0,",",".").' EE';

//echo '<br>Vorhandener Frachtraum: '.number_format($gesamthasspace, 0,",",".").' <font color="#00FF00">('.number_format($gesamthasspace+$addvaluehasspace_absolute+($gesamthasspace/100*$addvaluehasspace_percentage), 0,",",".").')</font> m&sup3;';

//bergbaukapazit�t
$tooltip='Rohstoffmenge inkl. Charakterbonus&';
for($resid=0;$resid<count($r_def);$resid++)
{
  $tooltip.=number_format(floor(($gesamtcanmine+$addvaluecanmine_absolut+($gesamtcanmine/100*$addvaluecanmine_percentage))/$r_def[$resid][1]*(1+(get_skill($resid)/500000))), 0,",",".").' '.$r_def[$resid][0].' m&sup3;/Min<br>';  
}

echo '<br>Bergbaukapazit&auml;t: '.number_format($gesamtcanmine, 0,",",".").' <font color="#00FF00">('.
number_format($gesamtcanmine+$addvaluecanmine_absolut+($gesamtcanmine/100*$addvaluecanmine_percentage), 0,",",".").
')</font> m&sup3;/Min <img id="tt1" border="0" style="vertical-align: middle;" src="'.$gpfad.'sym1.png"  width="16px" height="16px" title="'.$tooltip.'">';

echo '<br>Bergungskapazit&auml;t: '.number_format($gesamtcanrecover, 0,",",".").' BK';

echo '</td></tr></table> ';


rahmen1_unten();


//seitenteiler ende
echo '</td></tr></table>';

echo '<br>';


rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>