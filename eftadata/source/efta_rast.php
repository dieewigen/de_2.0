<?php
//das script fürs rasten und für die ausrüstung usw.
echo '<br><br>';
switch($r){
  case 2: //regenerieren
    //benötige bewegungspunkte für das regenerieren berechnen
    $benbewp=ceil(($hpmax-$hp)/$level/10*10);
    if ($benbewp>$bewpunkte) $benbewp=$bewpunkte;
    if ($benbewp>0)
    {
      $hp=$hp+($benbewp*$level*10/10);
      if($hp>$hpmax)$hp=$hpmax;
      //neue bewgungspunkte
      $bewpunkte = $bewpunkte - $benbewp;
      mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte - '$benbewp', hp = '$hp' WHERE user_id='$efta_user_id'",$eftadb);
      $msg='<font color="FDFB59">Regeneration abgeschlossen.</font>';
    }else $msg='<font color="FDFB59">Keine Regeneration m&ouml;glich, der Cyborg ist entweder nicht defekt, oder es gibt keine Aktionspunkte.</font>';
    break;
  case 3: //energietransfer
    if($sv_efta_in_de==1)
    {
      $db_daten=mysql_query("SELECT restyp05 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
      $row = mysql_fetch_array($db_daten);
      $tronic=$row["restyp05"];
      if($bewpunkte<=($sv_max_efta_bew_punkte-500) && $tronic>0)
      {
        $bewpunkte=$bewpunkte+500;
        $restyp05--;
        mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte + 500 WHERE user_id='$efta_user_id'",$eftadb);
        mysql_query("UPDATE de_user_data set restyp05=restyp05-1 WHERE user_id='$ums_user_id'",$db);
        $msg='<font color="FDFB59">Energietransfer abgeschlossen.</font>';
      }
      else $msg='<font color="FDFB59">Kein Energietransfer m&ouml;glich, entweder es ist kein Tronic vorhanden, oder der Cyborg ist vollst&auml;ndig aufgeladen.</font>';
    }
    break;
  case 4: //psienergie regenerieren
    $benbewp=($mpmax-$mp)*10;
    $benbewp=floor($benbewp);
    if ($benbewp>$bewpunkte)$benbewp=floor($bewpunkte);
    if ($benbewp>0)
    {
      $mp=$mp+($benbewp/10);
      if($mp>$mpmax)$mp=$mpmax;
      //neue bewgungspunkte
      $bewpunkte = $bewpunkte - $benbewp;
      mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte - '$benbewp', mp = '$mp' WHERE user_id='$efta_user_id'",$eftadb);
      $msg='<font color="FDFB59">Regeneration abgeschlossen.</font>';
    }else $msg='<font color="FDFB59">Keine Regeneration m&ouml;glich, es fehlt keine Psienergie, oder es gibt keine Aktionspunkte.</font>';
    break;
    case 5: //heilige kraft
      $hascredits=has_credits($ums_user_id);
      if($bewpunkte<=($sv_max_efta_bew_punkte-1000) && $hascredits>0)
      {
		//credits abziehen
  	  	change_credits($ums_user_id, -1, 'EFTA - Heilige Kraft');
        $bewpunkte=$bewpunkte+1000;
        mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte + 1000 WHERE user_id='$efta_user_id'",$eftadb);
        $msg='<font color="FDFB59">Die Heilige Kraft hat die verbrauchte Energie erneuert.</font>';
      }
      else $msg='<font color="FDFB59">Die Heilige Kraft kann nicht eingesetzt werden, entweder ist kein Credit vorhanden, oder der Charakter ist nicht so stark geschw&auml;cht.</font>';
    
  default:
    break;
}

/*
echo '<div id="ct_city">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Rastplatz&nbsp;</b></td></tr>';
echo '</table><br>';*/

rahmen0_oben();

if($msg!='')
{
  rahmen2_oben();
  echo '<div align="center">'.$msg.'</div>';
  rahmen2_unten();
  echo '<br>';
}

//seitenteiler
echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
echo '<tr><td width="33%" valign="top">';

rahmen1_oben('<div align="center"><b>Rastplatz</b></div>');

echo '<table width="100%" cellpadding="1" cellspacing="1">
<tr align="center">
<td width="20%" class="cell"><b>Aktion</b></td>
<td width="80%" class="cell"><b>Beschreibung</b></td>
</tr>';
//regeneration: aktionspunkte -> hitpoints
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '
<tr align="center">
<td class="'.$bg.'" style="cursor: pointer;" onClick="lnk(\'r=2\')">Regeneration<br>(Stufe '.$level.')</td>
<td class="'.$bg.'">Startet den Prozess, der aus der vorhanden Aktionspunkten die K&ouml;rperstruktur regeneriert. Dabei wird soviel Energie wie m&ouml;glich verwendet, wobei 10 Aktionspunkte '.($level*10).' Lebenspunkte ergeben.</td>
</tr>';
//energietransfer: tronic -> aktionspunkte
if($sv_efta_in_de==1)
{
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  echo '
<tr align="center">
<td class="'.$bg.'" style="cursor: pointer;" onClick="lnk(\'r=3\')">Energietransfer</td>
<td class="'.$bg.'">Startet den Energietransfer &uuml;ber das Virtuelle Transmitterfeld. Bei diesem Vorgang wird die Energie eines Tronic in 500 Aktionspunkte umgewandelt.</td>
</tr>';
}
//heilige kraft: credits -> aktionspunkte
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '
<tr align="center">
<td class="'.$bg.'" style="cursor: pointer;" onClick="lnk(\'r=5\')">Heilige Kraft</td>
<td class="'.$bg.'">Wandelt die heilige Energie eines Credits in 1.000 Aktionspunkte um.</td>
</tr>';

//meditation: aktionspunkte -> psienergie
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '
<tr align="center">
<td class="'.$bg.'" style="cursor: pointer;" onClick="lnk(\'r=4\')">Meditation</td>
<td class="'.$bg.'">Durch Meditation wird Psienergie regeneriert. Dabei wird soviel Energie wie m&ouml;glich verwendet, wobei 10 Aktionsspunkte einen Punkt Psienergie ergeben.</td>
</tr>';
echo '</table>';

rahmen1_unten();

//seitenteiler - mitte
echo '</td><td width="34%" valign="top" align="center">';


//cyborgdaten anzeigen
//show_efta_resline();
//farbe für die bewegungspunkte bestimmen
$bcolor='#00FF00';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.75)$bcolor='yellow';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.50)$bcolor='orange';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.25)$bcolor='red';
//farbe für die lebensenergie bestimmen
$hpcolor='#00FF00';
if($hp<=$hpmax*0.75)$hpcolor='yellow';
if($hp<=$hpmax*0.50)$hpcolor='orange';
if($hp<=$hpmax*0.25)$hpcolor='red';

$mpcolor='#00FF00';
if($mp<=$mpmax*0.75)$mpcolor='yellow';
if($mp<=$mpmax*0.50)$mpcolor='orange';
if($mp<=$mpmax*0.25)$mpcolor='red';

//charkaterdaten
rahmen1_oben('<div align="center"><b>Charakterdaten</b></div>');

echo '<table width="100%" cellpadding="1" cellspacing="1">';
echo '<tr align="center">
<td class="'.$bg.'"><b>Aktionspunkte</td>
<td class="'.$bg.'"><b><font color="'.$bcolor.'">'.(floor($bewpunkte)).'</td>
</tr>';
if($sv_efta_in_de==1)
{
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  echo '<tr align="center">
<td class="'.$bg.'"><b>Tronic</td>
<td class="'.$bg.'"><b>'.$restyp05.'</td>
</tr>';
}
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$hascredits=has_credits($ums_user_id);
echo '<tr align="center">
<td width="50%" class="'.$bg.'"><b>Credits</td>
<td width="50%" class="'.$bg.'"><b>'.number_format($hascredits, 0,",",".").'</b></td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td width="50%" class="'.$bg.'"><b>Lebensenergie</td>
<td width="50%" class="'.$bg.'"><b><font color="'.$hpcolor.'">'.$hp.'/'.$hpmax.'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Psienergie</td>
<td class="'.$bg.'"><b><font color="'.$mpcolor.'">'.$mp.'/'.$mpmax.'</font></td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>St&auml;rke</td>
<td class="'.$bg.'"><b>'.$str.'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Geschick</td>
<td class="'.$bg.'"><b>'.$dex.'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Konstitution</td>
<td class="'.$bg.'"><b>'.$konst.'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Stufe</td>
<td class="'.$bg.'"><b>'.$level.' / '.$maxplayerlevel.'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Erfahrungspunkte</td>
<td class="'.$bg.'"><b>'.number_format($exp, 0,",",".").'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>N&auml;chster Level</td>
<td class="'.$bg.'"><b>'.number_format($nextlevelexp, 0,",",".").'</td>
</tr>';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo '<tr align="center">
<td class="'.$bg.'"><b>Ruhm</td>
<td class="'.$bg.'"><b>'.number_format($player_fame, 0,",",".").'</td>
</tr>';
echo '</table>';
rahmen1_unten();



//seitenteiler - rechts
echo '</td><td width="33%" valign="top" align="right">';

rahmen1_oben('<div align="center"><b>Statistik</b></div>');

//expgewinn zum letzten tag
$db_daten=mysql_query("SELECT SUM(exp)-SUM(explastday) AS wert FROM `de_cyborg_data` WHERE user_id='$efta_user_id'",$eftadb);
$row = mysql_fetch_array($db_daten);
$tageswachstumchar=$row["wert"];

//expgewinn im vergleich zum "vorjahr" serverbasierend
$db_daten=mysql_query("SELECT SUM(exp)-SUM(explastday) AS wert FROM `de_cyborg_data`",$eftadb);
$row = mysql_fetch_array($db_daten);
$tageswachstumserver=$row["wert"];
//expdurchschnitt
$db_daten=mysql_query("SELECT COUNT(*) AS wert FROM `de_cyborg_data` WHERE exp<>explastday",$eftadb);
$row = mysql_fetch_array($db_daten);
$durchschnittserver=$row["wert"];
if($durchschnittserver<1)$durchschnittserver=1;



echo '<table width="100%" cellpadding="1" cellspacing="1">
<tr align="center">
<td colspan="2" class="cell"><b>Charakter</b></td>
</tr>
<tr align="center">
<td width="50%" class="cell1">Erfahrungsgewinn dieser Tag</td>
<td width="50%" class="cell1">'.number_format($tageswachstumchar, 0,"",".").'</td>
</tr>

<tr align="center">
<td colspan="2" class="cell"><b>Server</b></td>
</tr>
<tr align="center">
<td width="50%" class="cell1">Erfahrungsgewinn dieser Tag</td>
<td width="50%" class="cell1">'.number_format($tageswachstumserver, 0,"",".").'</td>
</tr>
<tr align="center">
<td class="cell">Erfahrungsgewinn &#216</td>
<td class="cell">'.number_format(round($tageswachstumserver/$durchschnittserver), 0,"",".").'</td>
</tr>

<tr align="center">
<td colspan="2" class="cell1"><a href="#" onClick="lnk(\'action=statisticpage\')"><div class="b1">Rangliste</div></a></td>
</tr>
<tr align="center">
<td colspan="2" class="cell"><a href="#" onClick="lnk(\'action=showmappage\')"><div class="b1">Karte</div></a></td>
</tr>

</table>';

rahmen1_unten();


//seitenteiler ende
echo '</td></tr></table>';

rahmen0_unten();



//infoleiste anzeigen
show_infobar();

echo '</body></html>';

exit;
?>
