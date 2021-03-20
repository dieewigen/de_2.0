<?php
include_once "soudata/defs/boni.inc.php";
include_once "soudata/defs/resources.inc.php";



//wenn ein submit gekommen ist, dann schauen ob er zastari einzahlen m�chte
if($_REQUEST["do"]=='1')
{
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
    //auslesen wieviel er einzahlen m�chte
  	$spende=intval($_REQUEST["zs"]);
  	if($spende<0)$spende=0;
  	//�berpr�fen ob er soviel hat
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
		
	//die kasse vergr��ern
	$feldname='f'.$player_fraction.'money';
	mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$spende'",$soudb);
  	    
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

//wenn ein submit gekommen ist, dann schauen ob er einen bonus erwerben m�chte
if($_REQUEST["do"]=='2')
{
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {
    $bonus=intval($_REQUEST["b"]);
    if($bonus>=1 AND $bonus<=3)
    {
      //�berpr�fen ob er genug specialres hat
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
		else  //es gibt einen bonus, diesen verl�ngern
		{
		  $zeit=3600;
		  mysql_query("UPDATE `sou_system` SET $feldname=$feldname+'$zeit'",$soudb);
		}
      }
    }
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

echo '<div class="cell1">';

//formularstart
echo '<form action="sou_main.php" method="POST" name="f">';
echo '<input type="hidden" name="action" value="systempage">';
echo '<input type="hidden" name="underpage" value="3">';
echo '<input type="hidden" name="do" value="1">';

/*
rahmen2_oben();
echo '<font color="#FF0000"><b>Achtung</b></font><br>Die Erweiterte Arch�ologie befindet sich noch im Alpha-Stadium, was bedeutet, dass es h�ufiger zu 
Ver�nderungen und Resets/Teilresets kommen wird. Das bedeutet, dass Errungenschaften im Spiel verlorengehen k�nnen. Ich bitte alle Spieler, die nicht in 
der Lage sind bei einer Alphaversion mitzuspielen, sich zu �berlegen hier erst zu spielen, wenn eine stabile Version erreicht ist, welche weniger 
Ver�nderungen/Resets/Teilresets ben�tigt.
<br>Vielen Dank	';
rahmen2_unten();
echo '<br>';
*/

//auf ungelesene hyperfunknachrichten �berpr�fen
$hfstatus = "100__";
$query = "SELECT COUNT(*) AS anzahl  FROM sou_user_hyper WHERE empfaenger = '$_SESSION[sou_user_id]' AND status like '$hfstatus' ORDER BY date DESC , id DESC"; 
$db_daten = mysql_query($query, $soudb);
$row = mysql_fetch_array($db_daten);
if($row["anzahl"]>0)
{
  	rahmen2_oben();
    echo '<table width="100%" class="cell"><tr><td align="center"><a href="sou_main.php?action=hyperfunk"><font color="#00FF00">Es liegen ungelesene Hyperfunknachrichten vor. Diese kannst Du in der Hyperfunk-Verwaltung unter Eingang und/oder Fraktion finden.</font></a></td></tr></table>';
    rahmen2_unten();
    echo '<br>';
}

if($_REQUEST['underpage']==1 OR $_REQUEST['underpage']==3)
{

//charakter�bersicht
rahmen1_oben('<div align="center"><b>Informationen</b></div>');
echo '<div align="left">';

//spielerboni
$tooltip='&';
for($i=0;$i<count($r_def);$i++)
{
  $skill=get_skill($i);
  $tooltip.='Bergbaubonus '.$r_def[$i][0].': '.number_format($skill*100/500000, 4,",",".").'% ('.$skill.')<br>';
}

echo 'Spielerboni: <img title="'.$tooltip.'" border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif">';
echo ' - Schiffsboni: <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" title="Bao-Nada Station&Bonusmodule sind auch in den Bao-Nada Stationen zu finden."><br>';



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

if($tooltip[0]!='&')echo '<img src="'.$gpfad.'sym1.png" width="64px" height="64px" title="'.$tooltip[0].'">';
if($tooltip[1]!='&')echo '<img src="'.$gpfad.'sym2.png" width="64px" height="64px" title="'.$tooltip[1].'">';

//test auf maximale schiffsgr��e
$db_daten=mysql_query("SELECT MAX(shipdiameter) AS shipdiameter FROM `sou_user_data` WHERE fraction='$player_fraction'",$soudb);
$row = mysql_fetch_array($db_daten);
if($player_ship_diameter<$row['shipdiameter'])
{
	$achtungtooltip='Raumschiffgr&ouml;&szlig;e zu gering&In Deiner Fraktion sind bereits gr&ouml;&szlig;ere Raumschiffe m&ouml;glich. Das Raumschiff kann in der Raumwerft vergr&ouml;&szlig;ert werden.';
	echo '<img src="'.$gpfad.'sym37.png" width="64px" height="64px" title="'.$achtungtooltip.'">';
}

echo '</div>';
rahmen1_unten();
echo '<br>';
}

if($_REQUEST['underpage']==3 OR $_REQUEST['underpage']==1)
{
rahmen1_oben('<div align="center"><b>Allgemeine Informationen Fraktion '.$player_fraction.'</b></div>');
echo '<div align="left">';
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// jahr
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////

$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
echo 'Jahr: '.number_format($row["year"], 0,"","."),' <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" title="Zeitrechnung&Ein Tag Deiner eigenen Zeitrechnung entspricht hier einem Jahr.">';


////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// spenden�bersicht
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

if($sv_sou_in_de)$jsstr.='<br><br>Um den t&auml;glichen (01:00 Uhr) Bonus zu erhalten, mu� Dein heutiges Spendenvolumen, nach den aktuellen Werten, folgenden Wert erreichen: '.number_format(round($jahrespenden/$durchschnitt/2), 0,"",".");

$stip = 'Spenden&uuml;bersicht&'.$jsstr;


echo ' - Spenden&uuml;bersicht: <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" title="'.$stip.'">';

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
  //geb�ude
  if($row["typ"]==1)$grafik='<img src='.$gpfad.'a14.gif>';
  //forschung
  elseif($row["typ"]==2)$grafik='<img src='.$gpfad.'a12.gif>';
  //neue kolonie
  elseif($row["typ"]==3)$grafik='<img src='.$gpfad.'a13.gif>';
  //neuer sektor
  elseif($row["typ"]==4)$grafik='<img src='.$gpfad.'a11.gif>';
  //neue sektorraumbasis
  elseif($row["typ"]==5)$grafik='<img src='.$gpfad.'a11.gif>';
  
  $jsstr.= number_format($row["year"], 0,"",".").': '.$grafik.$row["message"];
}

$ntip = 'Fraktionsnachrichten&'.$jsstr;

echo '</script>';

echo ' - Fraktionsnachrichten: <img border="0" style="vertical-align: middle;" src="'.$gpfad.'a16.gif" title="'.$ntip.'">';



////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//fraktion
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
echo '<br>Dein Heimatsystem (X:Y): <a href="sou_main.php?action=sectorpage&smx='.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][0].'&smy='.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][1].'">'.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][0].':'.$sv_sou_startposition[$_SESSION["sou_fraction"]-1][1].'</a>';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//zastari in der fraktsionskasse
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
$feldname='f'.$player_fraction.'money';
echo '<br>Fraktionskasse: '.number_format($row[$feldname], 0,"",".").' <img src="'.$gpfad.'a9.gif" alt="Zastari" title="Zastari">';
echo ' (<input type="text" name="zs" size="18" maxlength="16" value=""> <input type="submit" value="einzahlen">)';

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
//letzter forenpost
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
$db_daten=mysql_query("SELECT id, lastactive, threadname FROM sou_forum_threads WHERE fraction='$player_fraction' ORDER BY lastactive DESC LIMIT 1", $soudb);
if(mysql_num_rows($db_daten)==1)
{
  $row = mysql_fetch_array($db_daten);	
  echo '<br><br>Letzter Forenbeitrag: <a href="sou_main.php?action=fracforumpage&id='.$row["id"].'">'.$row["threadname"]. ' - '.date ("d.m.Y \u\m H:i", $row[lastactive]).'</a>';
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

$atip[0] = 'Information&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 1% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=#00FF00>Kosten: 1 '.$specialres_def[0][1].'</font>';
$atip[1] = 'Information&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 3% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=#00FF00>Kosten: 1 '.$specialres_def[1][1].'</font>';
$atip[2] = 'Information&Hier kann ein einst&uuml;ndiger Fraktionsbonus von 6% auf die Preise im Handelskontor aktiviert werden. Alle Boni summieren sich auf und k&ouml;nnen beliebig verl&auml;ngert werden.<br><br><font color=#00FF00>Kosten: 1 '.$specialres_def[2][1].'</font>';

$links=array();

$links[0]='<a href="sou_main.php?action=systempage&underpage=3&do=2&b=1"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px" title="'.$atip[0].'"></a>';
$links[1]='<a href="sou_main.php?action=systempage&underpage=3&do=2&b=2"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px" title="'.$atip[1].'"></a>';
$links[2]='<a href="sou_main.php?action=systempage&underpage=3&do=2&b=3"><img border="0" style="vertical-align: middle;" src="'.$gpfad.'sym3.png" width="24px" height="24px" title="'.$atip[2].'"></a>';


echo '<br><br>Handelskontorbonus 1%: '.$links[0].' '.$aktiv[0];
echo '<br>Handelskontorbonus 3%: '.$links[1].' '.$aktiv[1];
echo '<br>Handelskontorbonus 6%: '.$links[2].' '.$aktiv[2];
//echo '<br>Handelskontorbonus durch Kolonien: '.$kolbonus.'%';


echo '</div>';
rahmen1_unten();
echo '<br>';
}

if($_REQUEST['underpage']==4)
{
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//fraktionsaufgaben
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

//�berpr�fen ob er eine fraktionsaufgabe erf�llen m�chte

if($_REQUEST["doquest"]>0 AND $player_atimer1time<time())
{
  $quest_id=intval($_REQUEST["doquest"]);
  
  //�berpr�fen ob man sich an den passenden koordinaten befindet
  $db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE id='$quest_id' AND (fraction='$player_fraction' OR fraction=0) AND done=0 AND x='$player_x' AND y='$player_y'",$soudb);
  $num = mysql_num_rows($db_daten);
  //quest laden
  if($num==1)
  {
    
    include "soudata/questdata/sq".$quest_id.".php";
  }
      
  //questtext ausgeben
  if($quest_text!='')
  {
    //echo '<br>';
    rahmen2_oben();
    echo $quest_text;
    
    echo '<br><br><a href="sou_main.php?action=systempage&underpage=4" class="btn">weiter</a><br>';
    
    rahmen2_unten();

    echo '<br>';
  }
}
else
{

//liste der aufgaben ausgeben
rahmen1_oben('<div align="center"><b>Fraktionsaufgaben</b></div>');
echo '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr class="'.$bg.'"><td><b>Aufgabe</b></td><td align="center"><b>Koordinaten</b></td><td align="center"><b>Stufe</b></td></tr>';


//m�gliche quests aus der datenbank auslesen
$qstr='';
$i=0;
$db_daten=mysql_query("SELECT * FROM `sou_frac_quests` WHERE (fraction='$player_fraction' OR fraction=0) AND done=0 ORDER BY id",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  $qstr.='<tr class="'.$bg.'">';
  
  //questnamen auslesen
  include "soudata/questdata/questname".$row["id"].".php";
  
  //�berpr�fen, ob man sich evtl. auf den koordinaten befindet
  if($row['x']==$player_x AND $row['y']==$player_y)$questname='<a href="sou_main.php?action=systempage&underpage=4&doquest='.$row['id'].'">'.$questname.'</a>';
  
  //echo $questname.' - Koordinaten: '.$row["x"].':'.$row["y"];
  if($row[fraction]==0)$hstr=' <img border="0" style="vertical-align: middle;" 
  src="'.$gpfad.'a15.gif" alt="fraktions&uuml;bergreifend" title="fraktions&uuml;bergreifend - die schnellste Fraktion wird siegen">';else {$hstr='';}
  $qstr.='
  <td>'.$questname.$hstr.'</td>';
  if($row[hidexy]==0)
  {
    //tooltip bauen
    //entfernung berechnen
    $s1=$player_x-$row["x"];
    $s2=$player_y-$row["y"];
    if($s1<0)$s1=$s1*(-1);
    if($s2<0)$s2=$s2*(-1);
    $s1=pow($s1,2);
    $s2=pow($s2,2);
    $w1=$s1+$s2;
    $w3=sqrt($w1);

	$tooltip = 'Reiseinformationen&Zielkoordinaten: '.$row["x"].':'.$row["y"].'<br>Entfernung zur aktuellen Position:<br>X: '.($row["x"]-$player_x).'<br>Y: '.($row["y"]-$player_y).'<br>Lichtjahre: '.number_format($w3, "2",",",".").'<br>Zusatzinformationen: '.$questinfo;
  	
  	$qstr.='<td align="center"><a href="sou_main.php?action=sectorpage&smx='.$row["x"].'&smy='.$row["y"].'">'.$row["x"].':'.$row["y"].'&nbsp;<img src="'.$gpfad.'a16.gif" width="16" height="16" border="0" title="'.$tooltip.'"></a></td>';
  }
  else 
  {
    $tooltip= 'Reiseinformationen&Zielkoordinaten: unbekannt<br>Zusatzinformationen: '.$questinfo;
    $qstr.='<td align="center">unbekannt&nbsp;<img src="'.$gpfad.'a16.gif" width="16" height="16" border="0" title="'.$tooltip.'"></td>';
  }
  $qstr.='<td align="center">'.($row["questlevel"]+1).'</td>';
  
  $qstr.='</tr>';
  $i++;
}
$jsstr.='</script>';
echo $jsstr;
echo $qstr;

echo '</table>';
rahmen1_unten();
echo '<br>';
}

}//ende liste fraktionsaufgaben

echo '</div>';//abdunkler

echo '<input type="image" src="'.$gpfad.'e.gif" style="width:0; height:0; border:0px;">';
echo '</form>';

?>