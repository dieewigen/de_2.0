<?php 
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  infocenter  
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

if($_REQUEST['action']=='infocenter')
{
  $output='<div style="height: 2px; width: 100%;"></div>';
  $output.='<span style="position: absolute; top: 1px; right: 1px;">
  <a onclick="hide_mainarea();"><img src="'.$gpfad.'abutton3.gif" alt="Fenster schliessen" title="Fenster schliessen"></a>
  </span>';
  
if($_REQUEST["do"]=='1')
{
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
    //auslesen wieviel er einzahlen möchte
  	$spende=intval($_REQUEST["zs"]);
  	if($spende<0)$spende=0;
  	//überprüfen ob er soviel hat
  	$hasmoney=has_money($player_user_id);
  	if($hasmoney<$spende)$spende=$hasmoney;
  	    
	//geld vom konto abziehen
	$player_money-=$spende;
	change_money($_SESSION["sou_user_id"], $spende*-1);
		
	//dieses als spende notieren
	$player_donate+=$spende;
	mysql_query("UPDATE `sou_user_data` SET donate=donate+'$spende' WHERE user_id='$player_user_id'",$soudb);
	
	//diese spende dem ansehen im heimatsystem zurechnen
    $sx=$sv_sou_startposition[$player_fraction-1][0];
    $sy=$sv_sou_startposition[$player_fraction-1][1];
    $feldname='prestige'.$player_fraction;
	mysql_query("UPDATE sou_map SET $feldname=$feldname+'$spende' WHERE x='$sx' AND y='$sy'", $soudb);	
		
	//die kasse vergrößern
	$feldname='f'.$player_fraction.'money';
	mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$spende'",$soudb);

	//refresh der satusleiste
	$output.='<script>load_infobar();</script>';
	
    //lock wieder entfernen
    $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      //print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    }
  }//lock ende
  //else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
}
  
//wenn ein submit gekommen ist, dann schauen ob er einen bonus erwerben möchte
if($_REQUEST["do"]=='2')
{
  
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
    $bonus=intval($_REQUEST["b"]);
    if($bonus>=1 AND $bonus<=3)
    {
    
      //überprüfen ob er genug specialres hat
      $specialres=has_specialres($player_user_id, $bonus-1);
      
      if($boni_def[$bonus-1][0]<=$specialres)
      {
      	//specialres abziehen
		change_specialres($player_user_id, $bonus-1, $boni_def[$bonus-1][0]*-1);
      	
      	//aktuellen bonus auslesen
        $feldname='f'.$player_fraction.'bonus'.$bonus;
		$db_daten=mysql_query("SELECT $feldname AS zeit FROM `sou_system`",$soudb);
		$row = mysql_fetch_array($db_daten);
		
		$time=time();
		//bonuszeit berechnen und aktualisieren
		if($row["zeit"]<$time)//es gibt keine aktuellen bonus
		{
		  $zeit=time()+3600;
		  mysql_query("UPDATE `sou_system` SET $feldname='$zeit'",$soudb);
		}
		else  //es gibt einen bonus, diesen verlängern
		{
		  $zeit=3600;
		  mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$zeit'",$soudb);
		}
      }
    }
    //lock wieder entfernen
    $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      //print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    }
  }//lock ende
  //else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
}


//charakterübersicht
$output.=rahmen1a_oben('<div align="center"><b>Charakter- und Schiffsboni</b></div>');


$output.='<div align="left">';

//spielerboni
$tooltip='&';
for($i=0;$i<count($r_def);$i++)
{
  $skill=get_skill($i);
  $tooltip.='Bergbaubonus '.$r_def[$i][0].': '.number_format($skill*100/500000, 4,",",".").'% ('.$skill.')<br>';
}

$output.='Spielerboni: <img id="playerboni" title="'.$tooltip.'" border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif">';
$output.=' - Schiffsboni: <img border="0" title="Tipp&Bonusmodule sind auch in den Bao-Nada Stationen zu finden." style="vertical-align: middle;" src="'.$gpfad.'a16.gif"><br>';

//buffs laden und anzeigen
$time=time();
unset($tooltip);
$tooltip[0]='&';
$tooltip[1]='&';
$db_daten=mysql_query("SELECT * FROM `sou_user_buffs` WHERE user_id='$player_user_id' AND time>'$time' ORDER BY typ, value",$soudb);
while($row = mysql_fetch_array($db_daten))
{
	if($row[typ]==1) {$tooltip[0].='+'.$row[value].'% F&ouml;rderkapazit&auml;t - Aktiv bis: '.date ("d.m.Y H:i", $row[time]).'<br>';}
	elseif($row[typ]==2) {$tooltip[0].='+'.$row[value].' F&ouml;rderkapazit&auml;t - Aktiv bis: '.date ("d.m.Y H:i", $row[time]).'<br>';}
	//elseif($row[typ]==3) {$tooltip[1].='+'.$row[value].' % Frachtraumkapazit&auml;t - Aktiv bis: '.date ("d.m.Y H:i", $row[time]).'<br>';}
	//elseif($row[typ]==4) {$tooltip[1].='+'.$row[value].' Frachtraumkapazit&auml;t - Aktiv bis: '.date ("d.m.Y H:i", $row[time]).'<br>';}
	elseif($row[typ]==3) {$tooltip[0].='+'.$row[value].'% F&ouml;rderkapazit&auml;t - Aktiv bis: '.date ("d.m.Y H:i", $row[time]).'<br>';}
}

if($tooltip[0]!='&')$output.='<img id="playerboni1" src="'.$gpfad.'sym1.png" width="64px" height="64px" title="'.$tooltip[0].'">';
if($tooltip[1]!='&')$output.='<img id="playerboni2" src="'.$gpfad.'sym2.png" width="64px" height="64px" title="'.$tooltip[1].'">';

$output.='</div>';
$output.=rahmen1a_unten();
$output.='<br>';




$output.=rahmen1a_oben('<div align="center"><b>Allgemeine Informationen Fraktion '.$player_fraction.'</b></div>');
$output.='<div align="left">';
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// jahr
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////

$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
$output.='Jahr: '.number_format($row["year"], 0,"",".").' <img title="Zeitrechnung&Ein Tag Deiner eigenen Zeitrechnung entspricht hier einem Jahr." style="vertical-align: middle;" src="'.$gpfad.'a16.gif">';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// spendenübersicht
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$jsstr='';
//spielerspenden
$jsstr.= 'Deine gesamten Spenden: '.number_format($player_donate, 0,"",".");
$jsstr.= '<br>Deine heutigen Spenden: '.number_format($player_donate-$player_donatelastday, 0,"",".");

//fraktion
//spenden im vergleich zum "vorjahr"
$db_daten=mysql_query("SELECT SUM(donate)-SUM(donatelastday) AS wert FROM `sou_user_data` WHERE fraction='$player_fraction'",$soudb);
$row = mysql_fetch_array($db_daten);
$jahrespenden=$row["wert"];
$jsstr.= '<br><br>Heutige Spenden deiner Fraktion: '.number_format($jahrespenden, 0,"",".");
//spendendurchschnitt
$db_daten=mysql_query("SELECT COUNT(*) AS wert FROM `sou_user_data` WHERE donate<>donatelastday AND fraction='$player_fraction'",$soudb);
$row = mysql_fetch_array($db_daten);
$durchschnitt=$row["wert"];
if($durchschnitt<1)$durchschnitt=1;
$jsstr.= ' (&#216; '.number_format(round($jahrespenden/$durchschnitt), 0,"",".").')';

//server
//spenden im vergleich zum "vorjahr"
$db_daten=mysql_query("SELECT SUM(donate)-SUM(donatelastday) AS wert FROM `sou_user_data`",$soudb);
$row = mysql_fetch_array($db_daten);
$jahrespenden=$row["wert"];
$jsstr.= '<br>Heutige Spenden aller Fraktionen: '.number_format($jahrespenden, 0,"",".");
//spendendurchschnitt
$db_daten=mysql_query("SELECT COUNT(*) AS wert FROM `sou_user_data` WHERE donate<>donatelastday",$soudb);
$row = mysql_fetch_array($db_daten);
$durchschnitt=$row["wert"];
if($durchschnitt<1)$durchschnitt=1;
$jsstr.= ' (&#216; '.number_format(round($jahrespenden/$durchschnitt), 0,"",".").')';

$jsstr.='<br>Die Durchschnittswerte beziehen sich auf die Spenden pro Spieler.';

if($sv_sou_in_de)$jsstr.='<br><br>Um den t&auml;glichen (01:00 Uhr) Bonus zu erhalten, mu&szlig; Dein heutiges Spendenvolumen, nach den aktuellen Werten, folgenden Wert erreichen: '.number_format(round($jahrespenden/$durchschnitt/2), 0,"",".");

$output.= ' - Spenden&uuml;bersicht: <img title="Spenden&uuml;bersicht&'.$jsstr.'" style="vertical-align: middle;" src="'.$gpfad.'a16.gif">';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// fraktionsnachrichten
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$jsstr='';
//die letzten x fraktionsnews ansehen
$db_daten=mysql_query("SELECT * FROM `sou_frac_news` WHERE fraction='$player_fraction' ORDER BY id DESC LIMIT 10",$soudb);
$first=1;
while($row = mysql_fetch_array($db_daten))
{
  if($first==1){$first=0;}
  else {$jsstr.= '<br>';}
  $grafik='';
  //gebäude
  if($row["typ"]==1)$grafik='<img src=\''.$gpfad.'a14.gif\'>';
  //forschung
  elseif($row["typ"]==2)$grafik='<img src=\''.$gpfad.'a12.gif\'>';
  //neue kolonie
  elseif($row["typ"]==3)$grafik='<img src=\''.$gpfad.'a13.gif\'>';
  //neuer sektor
  elseif($row["typ"]==4)$grafik='<img src=\''.$gpfad.'a11.gif\'>';
  //neue sektorraumbasis
  elseif($row["typ"]==5)$grafik='<img src=\''.$gpfad.'a11.gif\'>';
  
  $jsstr.= number_format($row["year"], 0,"",".").': '.$grafik.$row["message"];
}

$output.=' - Fraktionsnachrichten: <img title="Fraktionsnachrichten&'.$jsstr.'" style="vertical-align: middle;" src="'.$gpfad.'a16.gif">';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//fraktion
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$output.='<br>Dein Heimatsystem (X:Y): <a href="sou_main.php?action=sectorpage&smx='.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][0].'&smy='.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][1].'">'.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][0].':'.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][1].'</a>';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//zastari in der fraktsionskasse
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
$feldname='f'.$player_fraction.'money';
$output.='<form OnSubmit="return pay_frac_z()">';
$output.='<br>Fraktionskasse: '.number_format($row[$feldname], 0,"",".").' <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
$output.=' (<input id="zs" type="text" name="zs" size="18" maxlength="16" value=""> <input type="submit" value="einzahlen">)';
$output.='</form>';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//letzter forenpost
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$db_daten=mysql_query("SELECT id, lastactive, threadname FROM sou_forum_threads WHERE fraction='$player_fraction' ORDER BY lastactive DESC LIMIT 1", $soudb);
if(mysql_num_rows($db_daten)==1)
{
  $row = mysql_fetch_array($db_daten);	
  $output.='<br>Letzter Forenbeitrag: <a href="sou_main.php?action=fracforumpage&id='.$row["id"].'">'.utf8_encode($row["threadname"]). ' - '.date ("d.m.Y \u\m H:i", $row[lastactive]).'</a>';
}

//eine liste der boni
$time=time();
$feldname1='f'.$player_fraction.'bonus1';
$feldname2='f'.$player_fraction.'bonus2';
$feldname3='f'.$player_fraction.'bonus3';
$db_daten=mysql_query("SELECT $feldname1 AS bonus1, $feldname2 AS bonus2, $feldname3 AS bonus3 FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
if($time<$row["bonus1"])$aktiv[0]=date("d.m.Y - G:i", $row["bonus1"]);else $aktiv[0]='kein Bonus aktiv';
if($time<$row["bonus2"])$aktiv[1]=date("d.m.Y - G:i", $row["bonus2"]);else $aktiv[1]='kein Bonus aktiv';
if($time<$row["bonus3"])$aktiv[2]=date("d.m.Y - G:i", $row["bonus3"]);else $aktiv[2]='kein Bonus aktiv';
$link[0]='<a onclick="lnk(\'action=infocenter&do=2&b=1\');"><img title="Fraktionsbonus&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 1% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=\'#00FF00\'>Kosten: 1 '.$specialres_def[0][1].'</font>" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px"></a>';
$link[1]='<a onclick="lnk(\'action=infocenter&do=2&b=2\');"><img title="Fraktionsbonus&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 3% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=\'#00FF00\'>Kosten: 1 '.$specialres_def[1][1].'</font>" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px"></a>';
$link[2]='<a onclick="lnk(\'action=infocenter&do=2&b=3\');"><img title="Fraktionsbonus&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 6% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=\'#00FF00\'>Kosten: 1 '.$specialres_def[2][1].'</font>" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px"></a>';

$output.='<br><br>Handelskontorbonus 1%: '.$link[0].' '.$aktiv[0];
$output.='<br>Handelskontorbonus 3%: '.$link[1].' '.$aktiv[1];
$output.='<br>Handelskontorbonus 6%: '.$link[2].' '.$aktiv[2];
//echo '<br>Handelskontorbonus durch Kolonien: '.$kolbonus.'%';


$output.='</div>';
$output.=rahmen1a_unten();
$output.='<br>';


  $data[] = array ('output' => $output);
  echo json_encode($data);
}
?>