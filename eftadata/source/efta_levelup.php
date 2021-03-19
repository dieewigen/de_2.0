<?php
//spieler levelup

//werte für levelanstieg berechnet
$level++;
//hp
$konst=$konst+1;
//$hpmax = $hpmax + 8 + $hpkonstmod[$konst-10];//alter wert + 8 + modifikator (konstitution)
//$hpmax = $hpmax + 90 + $konst;
$hpmax=$level*100+(($konst-10)*20);

mysql_query("UPDATE de_cyborg_data SET hp = '$hpmax', hpmax = '$hpmax', mp = '$mpmax'+10, mpmax = mpmax + 10,
    str=str+1, dex=dex+1, konst=konst+1, level=level+1 WHERE user_id='$efta_user_id'",$eftadb);
  
//textinfo in der db hinterlegen
$text='Du bist eine Stufe aufgestiegen und Deine F&auml;higkeiten haben sich verbessert.';
mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
echo '<script>lnk("");</script>';
exit;


//echo '<script language="javascript">disablekeys=1;</script>';

//zu verteilende punkte pro level
//$hpkonstmod = array (0, 3, 3, 4, 4, 5, 5, 5, 6, 6, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16);
//$pointsperlevelliste = array (0, 1, 1, 1, 2, 1, 1, 1, 1, 2, 1, 1, 1, 1, 2, 1, 1, 1, 1, 3);
//$pointsperlevel=$pointsperlevelliste[$level];
/*
if(fmod($level+1, 10)==0)$pointsperlevel=2;else $pointsperlevel=1;
$w1=(int)$w1;
$w2=(int)$w2;
$w3=(int)$w3;
$w4=(int)$w4;

if (($w1+$w2+$w3+$w4)==$pointsperlevel AND $w1>=0 AND $w2>=0 AND $w3>=0 AND $w4>=0) //schauen ob die richtige anzahl von punkten verteilt worden ist
{
  //werte für levelanstieg berechnet
  //hp
  $konst=$konst+$w3;
  //$hpmax = $hpmax + 8 + $hpkonstmod[$konst-10];//alter wert + 8 + modifikator (konstitution)
  //$hpmax = $hpmax + 90 + $konst;
  $level++;
  $hpmax=$level*100+(($konst-10)*20);

  $w4=$w4*10;
  mysql_query("UPDATE de_cyborg_data SET hp = '$hpmax', hpmax = '$hpmax', mp = '$mpmax', mpmax = mpmax + '$w4',
    str=str+'$w1', dex=dex+'$w2', konst=konst+'$w3', level=level+1 WHERE user_id='$efta_user_id'",$eftadb);
  

  //textinfo in der db hinterlegen
  $text='Der Stufenaufstieg wurde vollzogen.';
  mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
  echo '<script>lnk("");</script>';
  exit;
}

echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Stufenaufstieg</b></div>');

//anzeige der alten charkaterdaten
echo '<table width="100%" cellpadding="1" cellspacing="1">
<tr align="center">
<td colspan="2" class="cell"><b>Charakterdaten</b></td>
</tr>
<tr align="center">
<td width="200" class="cell1"><b>Lebensenergie</td>
<td width="200" class="cell1"><b>'.$hp.'/'.$hpmax.'</td>
</tr>
<tr align="center">
<td class="cell"><b>Psienergie</td>
<td class="cell"><b>'.$mp.'/'.$mpmax.'</td>
</tr>
<tr align="center">
<td class="cell1"><b>St&auml;rke</td>
<td class="cell1"><b>'.$str.'</td>
</tr>
<tr align="center">
<td class="cell"><b>Geschick</td>
<td class="cell"><b>'.$dex.'</td>
</tr>
<tr align="center">
<td class="cell1"><b>Konstitution</td>
<td class="cell1"><b>'.$konst.'</td>
</tr>
<tr align="center">
<td class="cell"><b>Alte Stufe</td>
<td class="cell"><b>'.$level.'</td>
</tr>
<tr align="center">
<td class="cell1"><b>Erfahrungspunkte</td>
<td class="cell1"><b>'.number_format($exp, 0,",",".").'</td>
</tr>
</tr>
<tr align="center">
<td class="cell"><b>N&auml;chster Level</td>
<td class="cell"><b>'.number_format($nextlevelexp, 0,",",".").'</td>
</tr>
</table>';
echo '<div class="text1"><b>';
echo '<br>Neue Stufe: '.($level+1);
echo '<br>Erfahrungspunkte: '.$exp;
echo '<br>Verteilbare Punkte: '.$pointsperlevel.'<br>';
echo '<form name="l1" action="eftamain.php" method="POST">';
echo 'Stärke: <input type="text" name="w1" value="" size="4" maxlength="5"> ';
echo 'Geschick: <input type="text" name="w2" value="" size="4" maxlength="5"> ';
echo 'Konstitution: <input type="text" name="w3" value="" size="4" maxlength="5"> ';
echo 'Energie: <input type="text" name="w4" value="" size="4" maxlength="5"><br><br>';
echo '<a href="javascript:document.l1.submit()">Werte übernehmen</a>';
echo '</form>';
echo '</b></div>';

rahmen1_unten();
rahmen0_unten();

echo '</body></html>';
exit;
*/
?>
