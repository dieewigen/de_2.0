<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "inc/userartefact.inc.php";
include "functions.php";
include "tickler/kt_einheitendaten.php";
include 'inc/lang/'.$sv_server_lang.'_defense.lang.php';
include 'inc/'.$sv_server_lang.'_links.inc.php';
include 'inc/sabotage.inc.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, defenseexp, newtrans, newnews, design4 AS design, sc3, spec1, spec3 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];$techs=$row["techs"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];$mysc3=$row["sc3"];$defenseexp=$row["defenseexp"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;
$spec1=$row['spec1'];$spec3=$row['spec3'];


$rangnamen=array("Der Erhabene", "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");

//maximalen tick auslesen
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//feststellen ob die werft sabotiert ist
if($maxtick<$mysc3+$sv_sabotage[9][0] AND $mysc3>$sv_sabotage[9][0])$sabotage=1;else $sabotage=0;


//userartefakte auslesen
//baukostenreduzierung 2
//turmfeuerkraft 8
//turmblockkraft 9
$db_daten=mysql_query("SELECT id, level FROM de_user_artefact WHERE (id=2 OR id=8 OR id=9) AND user_id='$ums_user_id'",$db);
$artbonus=0;$artbonus2=0;$artbonus3=0;
while($row = mysql_fetch_array($db_daten))
{
  if($row["id"]==2)$artbonus=$artbonus+$ua_werte[$row["id"]-1][$row["level"]-1][0];
  elseif($row["id"]==8)$artbonus2=$artbonus2+$ua_werte[$row["id"]-1][$row["level"]-1][0];
  elseif($row["id"]==9)$artbonus3=$artbonus3+$ua_werte[$row["id"]-1][$row["level"]-1][0];
}
if($artbonus>5)$artbonus=5;

if($_REQUEST["setdesign"])
{
  $design=intval($_REQUEST["setdesign"]);
  mysql_query("UPDATE de_user_data SET design4='$design' WHERE user_id = '$ums_user_id'",$db);	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Verteidigung</title>
<?php include "cssinclude.php";
echo '<script language="javascript">';
//flotten umstellen
  $db_daten=mysql_query("SELECT  tech_id, tech_name, tech_vor, score FROM de_tech_data$ums_rasse WHERE tech_id>99 AND tech_id<110 ORDER BY tech_id",$db);
  $c1=0;$i=81+$sv_anz_schiffe;unset($tooltips);
  while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
  {
    //zerlege vorbedinguns-string
    $z1=0;$z2=0;
    $vorb=explode(";",$row["tech_vor"]);
    foreach($vorb as $einzelb) //jede einzelne bedingung checken
    {
      $z1++;
      if ($techs[$einzelb]==1) $z2++;
      if ($einzelb==0) {$z1=0;$z2=0;}
    }
    if ($z1==$z2) //echo "Vorbedingung erf�llt";
    {
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      //tooltip-daten generieren
      //klasse
      $zstr='<font color=#D265FF>'.$defense_lang[klasse].': '.$defense_lang[klassennamen][$i-81].'</font>';
      //punkte
      $zstr.='<br><font color=#FFFA65>'.$defense_lang[punkte].': '.number_format($row["score"], 0,"",".").'</font>';

      //waffenarten
      //konventionell
      if($unit[$ums_rasse-1][$i-81][2]>0)$wv='<font color=#2DFF11>'.$defense_lang[waffenvorhandenja].'</font>';
       else $wv='<font color=#ED0909>'.$defense_lang[waffenvorhandennein].'</font>';
      $zstr.='<br><br><font color=#9D4B15>'.$defense_lang[waffengattung1].':</font> '.$wv;

      //klassenziel
      if($unit[$ums_rasse-1][$i-81][2]>0)
      {
        $zstr.='<br><font color=#ED9409>-'.$defense_lang[klasseziel1].': '.$defense_lang[klassennamen][$kampfmatrix[$i-81][0]].'</font>';
        $zstr.='<br><font color=#F0BA66>-'.$defense_lang[klasseziel2].': '.$defense_lang[klassennamen][$kampfmatrix[$i-81][2]].'</font>';
      }

      //emp
      if($unit[$ums_rasse-1][$i-81][3]>0)$wv='<font color=#2DFF11>'.$defense_lang[waffenvorhandenja].'</font>';
       else $wv='<font color=#ED0909>'.$defense_lang[waffenvorhandennein].'</font>';
      $zstr.='<br><br><font color=#15629D>'.$defense_lang[waffengattung2].':</font> '.$wv;

      //klassenziel
      if($unit[$ums_rasse-1][$i-81][3]>0)
      {
        $zstr.='<br><font color=#ED9409>-'.$defense_lang[klasseziel1].': '.$defense_lang[klassennamen][$blockmatrix[$i-81][0]].'</font>';
        $zstr.='<br><font color=#F0BA66>-'.$defense_lang[klasseziel2].': '.$defense_lang[klassennamen][$blockmatrix[$i-81][2]].'</font>';
      }

      $tooltips[$c1]=$row['tech_name'].'&'.$zstr;
      $c1++;
    }
    $i++;
  }
?>
</script>
<?php
echo '<script type="text/javascript">var ab='.$artbonus.';</script>';
echo '<script src="produktion'.$ums_rasse.'.js" type="text/javascript"></script>';
?>
</head>
<body>
<?php
//defenseboni berechnen
$defense_level=24-getfleetlevel($defenseexp);
include 'lib/defenseboni.lib.php';

//test auf spezialisierung bauzeit
if($spec1==1)$defense_bonus_buildtime+=50;

//namen des planetaren schildes aus der db auslesen
$db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id=24",$db);
$row = mysql_fetch_array($db_daten);
$ps_name=$row[tech_name];

//spezialisierung schildst�rke
if($spec3==1)$defense_bonus_ps+=10;

$infostring=$defense_lang[kostenreduz].$ua_name[1].$defense_lang[artefakte].number_format($artbonus, 2,",",".").'% (max. 5,00%)<br>'.
$ua_name[7].'-'.$defense_lang[artefakt].'-'.$defense_lang[angriffskraftbonus].': '.number_format($artbonus2, 2,",",".").'%<br>'.
$ua_name[8].'-'.$defense_lang[artefakt].'-'.$defense_lang[laehmkraftbonus].': '.number_format($artbonus3, 2,",",".").'%<br>'.
$defense_lang[erfahrungspunkte].': '.number_format($defenseexp, 0,",",".").' ('.$rangnamen[getfleetlevel($defenseexp)].')<br>- '.
$ps_name.'-'.$defense_lang[bonus].': '.$defense_bonus_ps.'%<br>- '.
$defense_lang[bauzeitreduzierung].': '.$defense_bonus_buildtime.'%<br>'.
$defense_lang[bauzeitreduzierung1].'<br>- '.		
$defense_lang[erfahrungspunkte].'-'.$defense_lang[angriffskraftbonus].'/'.$defense_lang[laehmkraftbonus].': '.
number_format((24-getfleetlevel($defenseexp))*0.4, 2,",",".").'%<br><font color=#00FF00>'.$defense_lang[spezialfaehigkeiten].':</font><br>';

//angriffskraft
if($defense_bonus_feuerkraft[0]>0)$infostring.='- '.$defense_lang[angriffskraftbonus].': '.$defense_bonus_feuerkraft[0].'% '.$defense_lang[wahrscheinlichkeit].': '.$defense_bonus_feuerkraft[1].'%';

$defstatus = $defense_lang['statusinformationen'].'&'.$infostring;


if ($submit AND $sabotage==0 AND $techs[22]==1)//ja, es wurde ein button gedrueckt
{
  //test auf korrekten scriptaufruf
  if ($HTTP_SERVER_VARS["REQUEST_METHOD"]=='GET')
  {
    @mail('issomad@die-ewigen.com', 'GET-Scriptfehler Verteidigung', $ums_user_id.' '.$ums_nic.' '.$ums_spielername);
    die('<font color="FF0000"><br>'.$defense_lang[fehler].'<br>');
  }

  //transaktionsbeginn
  if (setLock($ums_user_id))
  {

  for ($i=100; $i<=109; $i++)
  {
    $h=0;
    $str="if (\$b$i<>'') \$h= \$b$i;";
    eval($str);
    $h=(int)$h;
    if ($h>=1) //es wurde ein wert eingegeben und er ist ok h=anzahl des auftrags
    {
      //hole die schiffsdaten
      $db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor, score FROM de_tech_data$ums_rasse WHERE tech_id=$i",$db);
      $row = mysql_fetch_array($db_daten);
      //ben�tigte rohstoffe, abzgl. artefaktbonus
      $benrestyp01=$row[0]-$row[0]*$artbonus/100;
      $benrestyp02=$row[1]-$row[1]*$artbonus/100;
      $benrestyp03=$row[2]-$row[2]*$artbonus/100;
      $benrestyp04=$row[3]-$row[3]*$artbonus/100;
      $benrestyp05=$row[4]-$row[4]*$artbonus/100;
      
      $tech_ticks=ceil($row["tech_ticks"]-($row["tech_ticks"]*$defense_bonus_buildtime/100));
      if($tech_ticks<1)$tech_ticks=1;
      $tech_vor=$row["tech_vor"];
      
      $tech_score=$row['score'];
      
      //schauen obn man ihn bauen darf
      $z1=0;$z2=0;
      $vorb=explode(";",$tech_vor);
      foreach($vorb as $einzelb) //jede einzelne bedingung checken
      {
        $z1++;
        if ($techs[$einzelb]==1) $z2++;
        if ($einzelb==0) {$z1=0;$z2=0;}
      }
      if ($z1==$z2) $fehlermsg='';//echo "Vorbedingung erf�llt";
      else $fehlermsg='<font color="FF0000">'.$defense_lang[vorbedingung];

      //festellen wieviele schiffe man bauen kann
      $z=0;$z1=0;
      //test auf M
      if($benrestyp01>0)
      {
        $maxschiffe=floor($restyp01/$benrestyp01);
        if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
        $z=$z1;
      }
      //test auf D
      if($benrestyp02>0)
      {
        $maxschiffe=floor($restyp02/$benrestyp02);
        if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
        if ($z1<$z)$z=$z1;
      }
      //test auf I
      if($benrestyp03>0)
      {
        $maxschiffe=floor($restyp03/$benrestyp03);
        if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
        if ($z1<$z)$z=$z1;
      }
      //test auf E
      if($benrestyp04>0)
      {
        $maxschiffe=floor($restyp04/$benrestyp04);
        if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
        if ($z1<$z)$z=$z1;
      }
      //test auf T
      if($benrestyp05>0)
      {
        $maxschiffe=floor($restyp05/$benrestyp05);
        if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
        if ($z1<$z)$z=$z1;
      }
      //echo 'z: '.$z;

      //rohstoffabzug berechnen
      $restyp01=$restyp01-($z*$benrestyp01);
      $restyp02=$restyp02-($z*$benrestyp02);
      $restyp03=$restyp03-($z*$benrestyp03);
      $restyp04=$restyp04-($z*$benrestyp04);
      $restyp05=$restyp05-($z*$benrestyp05);

    /*$z=0;
    for ($k=1; $k<=$h; $k++)
    {
      if ($fehlermsg=='' && $benrestyp01<=$restyp01 && $benrestyp02<=$restyp02 &&$benrestyp03<=$restyp03 &&$benrestyp04<=$restyp04)
      {
        $restyp01=$restyp01-$benrestyp01;
        $restyp02=$restyp02-$benrestyp02;
        $restyp03=$restyp03-$benrestyp03;
        $restyp04=$restyp04-$benrestyp04;
        $z++;
      }
      else break;
    }*/

    //gibt $z verteidigungsanlangen in auftrag
    /*
    $result = mysql_query("SELECT anzahl FROM de_user_build WHERE user_id = '$ums_user_id' AND tech_id=$i AND verbzeit='$tech_ticks'",$db);
    $row = mysql_fetch_array($result);
    if ($z>0)
    if ($row[0]==0) //es gibt keine verteidigungsanlangen mit tech_ticks laenge in der queue
      mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, $i, $z, $tech_ticks)",$db);
    else mysql_query("update de_user_build set anzahl = anzahl + $z WHERE user_id = '$ums_user_id' AND tech_id=$i AND verbzeit='$tech_ticks'",$db);
    */
    if($z>0){
      $buildscore=$z*$tech_score;
      mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score) VALUES ($ums_user_id, $i, $z, $tech_ticks, $buildscore)",$db);
    }
  }
}
  //aktualisiert die rohstoffe
  $gr01=$gr01-$restyp01;
  $gr02=$gr02-$restyp02;
  $gr03=$gr03-$restyp03;
  $gr04=$gr04-$restyp04;
    mysql_query("update de_user_data set restyp01 = restyp01 - $gr01,
     restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
     restyp04 = restyp04 - $gr04 WHERE user_id = '$ums_user_id'",$db);
  //transaktionsende
  $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
  if ($erg)
  {
        //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
  }
  else
  {
        print($defense_lang[releaselock].$ums_user_id.$defense_lang[releaselock2]."<br><br><br>");
  }
}// if setlock-ende
else echo '<br><font color="#FF0000">'.$defense_lang[releaselock3].'</font><br><br>';

}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

echo '
<a href="production.php" title="Flotteneinheiten&Diese Einheiten k&ouml;nnen in Flotten verwendet werden und das Sonnensystem verlassen."><img src="'.$ums_gpfad.'g/symbol19.png" border="0" width="64px" heigth="64px"></a> 
<a href="defense.php" title="Verteidigungseinheiten&Diese Einheiten dienen nur zur Verteidigung und k&ouml;nnen das Sonnensystem nicht verlassen."><img src="'.$ums_gpfad.'g/symbol20.png" border="0" width="64px" heigth="64px"></a> 
<a href="recycling.php" title="Recycling&Hier k&ouml;nnen Einheiten der Heimatflotte und Verteidigungseinheiten recycelt werden."><img src="'.$ums_gpfad.'g/symbol24.png" border="0" width="64px" heigth="64px"></a>
<a href="techtree.php" target="'.$sv_server_tag.'techtree" title="Technologiebaum"><img src="'.$ums_gpfad.'g/symbol14.png" border="0" width="64px" heigth="64px"></a>
<a href="unitinfo.php" title="Einheiteninformationen"><img src="'.$ums_gpfad.'g/symbol26.png" border="0" width="64px" heigth="64px"></a>	
	';


//feststellen ob eine sabotage vorliegt und dann abbrechen
if($sabotage==1)
{
  $emsg.='<table width=600><tr><td class="ccr">';
  $emsg.=$defense_lang[sabotage_aktiv];
  $emsg.='</td></tr></table>';
  echo $emsg;
  
  die('</body></html>');
}

if ($techs[22]==0)
{
  $techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=22";
  $db_tech=mysql_query($techcheck,$db);
  $row_techcheck = mysql_fetch_array($db_tech);
  //echo $defense_lang[eswirdeine].$row_techcheck[tech_name].$defense_lang[benoetigt];
  
  echo '<br>';
  rahmen_oben($defense_lang[fehlendesgebaeude]);
  echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr align="left" class="cell">
  <td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=22" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_22.jpg" border="0"></a></td>
  <td valign="top">'.$defense_lang[gebaeudeinfo].': '.$row_techcheck[tech_name].'</td>
  </tr>';
  echo '</table>';
  rahmen_unten();
}
else
{
$str=''; //zaehle alle t�rme, die schon vorhanden sind
for ($i=100;$i<=109;$i++) $str = $str."\$ec$i=0;";
eval($str);
$db_daten=mysql_query("SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
while($row = mysql_fetch_array($db_daten))
{
$str='';
for ($i=100;$i<=109;$i++) $str = $str."\$ec$i=\$ec$i+\$row[\"e$i\"];";
eval ($str);
}

echo '<div align="center">';
?>
<form action="defense.php" method="POST" name="produktion">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="208" class="ro"><div class="cellu">&nbsp;&nbsp;<?=$defense_lang[einheit]?>:</div></td>
<td width="50" align="center" class="ro"><div class="cellu">M</div></td>
<td width="50" align="center" class="ro"><div class="cellu">D</div></td>
<td width="50" align="center" class="ro"><div class="cellu">I</div></td>
<td width="50" align="center" class="ro"><div class="cellu">E</div></td>
<td width="27" align="center" class="ro"><div class="cellu">T</div></td>
<td width="33" align="center" class="ro"><div class="cellu"><?=$defense_lang[wochen]?></div></td>
<td width="50" align="center" class="ro"><div class="cellu"><?=$defense_lang[stueck]?></div></td>
<td width="50" align="center" class="ro"><div class="cellu"><?=$defense_lang[bauen]?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="9">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="">
<col width="50">
<col width="50">
<col width="50">
<col width="50">
<col width="20">
<col width="30">
<col width="50">
<col width="50">
</colgroup>
<?php
//info mit artefaktbonus ausgeben
echo '<tr valign="middle" align="center" height="25"><td class="cell1" height="25" colspan="9" align="left"><b>
&nbsp;'.$defense_lang[statusinformationen].': <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$defstatus.'">
</b></td></tr>';

//$db_daten=mysql_query("SELECT e81, e82, e83, e84, e85, e86, e87 FROM de_user_fleet WHERE user_id='$ums_user_id'",$db);
//$einheiten = mysql_fetch_array($db_daten);
$c1=0;$c2=0;$z=0;
$db_daten=mysql_query("SELECT  tech_id, tech_name, restyp01, restyp02, restyp03, restyp04, restyp05, tech_ticks, tech_vor FROM de_tech_data$ums_rasse WHERE tech_id>99 AND tech_id<110 ORDER BY tech_id",$db);
while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
{
  //zerlege vorbedinguns-string
  $z1=0;$z2=0;
  $vorb=explode(";",$row["tech_vor"]);
  foreach($vorb as $einzelb) //jede einzelne bedingung checken
  {
    $z1++;
    if ($techs[$einzelb]==1) $z2++;
    if ($einzelb==0) {$z1=0;$z2=0;}
  }
  if ($z1==$z2) //echo "Vorbedingung erf�llt";
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell';
    }
    else
    {
      $c1=0;
      $bg='cell1';
    }
    $str='$ec=$ec'.$row["tech_id"].';';
    eval ($str);
    //bauzeit, boni mit einrechnen
    $tech_ticks=ceil($row["tech_ticks"]-($row["tech_ticks"]*$defense_bonus_buildtime/100));
    if($tech_ticks<1)$tech_ticks=1;
    if($design==1)
    {
      
      showeinheit2($row["tech_name"], $row["tech_id"], $row["restyp01"]-round($row["restyp01"]*$artbonus/100),
      $row["restyp02"]-round($row["restyp02"]*$artbonus/100), $row["restyp03"]-round($row["restyp03"]*$artbonus/100),
      $row["restyp04"]-round($row["restyp04"]*$artbonus/100), $row["restyp05"]-round($row["restyp05"]*$artbonus/100), $tech_ticks, $ec, $bg,$z);
    }
    else 
    {
      showeinheit($row["tech_name"], $row["tech_id"], $row["restyp01"]-round($row["restyp01"]*$artbonus/100),
      $row["restyp02"]-round($row["restyp02"]*$artbonus/100), $row["restyp03"]-round($row["restyp03"]*$artbonus/100),
      $row["restyp04"]-round($row["restyp04"]*$artbonus/100), $row["restyp05"]-round($row["restyp05"]*$artbonus/100), $tech_ticks, $ec, $bg,$z);
    }
    $z++;
  }
}
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php
//oberer rahmen von der echtzeitrechnung
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rml">&nbsp;</td>
        <td align="left" class="ro"><div class="cellu">&nbsp;'.$defense_lang[kostenvanlagen].'</div></td>
        <td width="13" class="rmr">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';
?>
<table border="0" cellpadding="1" cellspacing="1">
<tr height="20" align="center">
<td class="cell" align="left" width="50">&nbsp;</td>
<td width="76" class="cell">M</td>
<td width="76" class="cell">D</td>
<td width="76" class="cell">I</td>
<td width="76" class="cell">E</td>
<td width="43" class="cell">T</td>
<td width="71" class="cell"><?=$defense_lang[kapazitaet]?></td>
<td width="75" class="cell"><?=$defense_lang[punkte]?></td>
</tr>
<tr height="20" align="center">
<td class="cell1" align="left">&nbsp;<?=$defense_lang[summe]?>:</td>
<td class="cell1" id="m">0</td>
<td class="cell1" id="d">0</td>
<td class="cell1" id="i">0</td>
<td class="cell1" id="e">0</td>
<td class="cell1" id="t">0</td>
<td class="cell1" id="k">0</td>
<td class="cell1" id="p">0</td>
</tr>
<tr height="20">
<td class="cell" colspan="8" align="center"><input type="Submit" name="submit" value="<?=$defense_lang[bauen]?>"></td>
</tr>
</table>
<?php

//zeige aktive bauauftr�ge an
//$result=mysql_query("SELECT  de_user_build.anzahl, de_user_build.verbzeit, de_tech_data.tech_name FROM de_tech_data, de_user_build WHERE user_id=$ums_user_id AND de_user_build.tech_id = de_tech_data.tech_id AND de_user_build.tech_id > 99 AND de_user_build.tech_id < 110 ORDER BY de_user_build.verbzeit ASC",$db);
//$result=mysql_query("SELECT de_user_build.anzahl, de_user_build.verbzeit, de_tech_data$ums_rasse.tech_name, de_tech_data$ums_rasse.score FROM de_user_build left join de_tech_data$ums_rasse on(de_user_build.tech_id = de_tech_data$ums_rasse.tech_id) WHERE user_id=$ums_user_id AND de_user_build.tech_id > 80 AND de_user_build.tech_id < 110 ORDER BY de_user_build.verbzeit ASC",$db);
unset($technames);
$techselect='<option value="0">Bitte w&auml;hlen</option>';
$db_daten=mysql_query("SELECT * FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id>80 AND tech_id<105 ORDER BY tech_id",$db);
while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
{
	$technames[$row['tech_id']]=$row['tech_name'];
}

$result=mysql_query("SELECT tech_id, SUM(anzahl) AS anzahl, verbzeit, SUM(score) AS score FROM `de_user_build` 
	WHERE user_id='$ums_user_id' AND tech_id>80 AND tech_id<110 GROUP BY tech_id, verbzeit ORDER BY verbzeit, tech_id ASC",$db);
$num = mysql_num_rows($result);
if ($num>0)
{
  echo '</td><td width="13" class="rr">&nbsp;</td></tr></table>
        <table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rml">&nbsp;</td>
        <td align="left" class="ro"><div class="cellu">&nbsp;'.$defense_lang[aktivebauauftr].'</div></td>
        <td width="13" class="rmr">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table border="0" cellpadding="0" cellspacing="1">';
  echo '<tr align="center">';
  echo '<td class="cell" width="300"><b>'.$defense_lang[einheit].'</b></td>';
  echo '<td class="cell" width="80"><b>'.$defense_lang[stueck].'</b></td>';
  echo '<td class="cell" width="105"><b>'.$defense_lang[punkte].'</b></td>';
  echo '<td class="cell" width="78"><b>'.$defense_lang[wochen].'</b></td>';
  echo '</tr>';


	while($row = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
	{
	    echo '<tr align="center">';
	    echo '<td class="cell">'.$technames[$row["tech_id"]].'</td>';
	    echo '<td class="cell">'.number_format($row["anzahl"], 0,"",".").'</td>';
	    echo '<td class="cell">'.number_format($row["score"], 0,"",".").'</td>';
	    echo '<td class="cell">'.$row["verbzeit"].'</td>';
	    echo '</tr>';
	}
	
  echo '</table>';
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';
}
else  //nur unteren rahmen von der echtzeitrechnung
{
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';
}

  //designauswahl
  
  echo '<div class="cellu" style="width: 250px;">';
  if($design==1){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="defense.php?setdesign=1">'.$defense_lang[design].' A</a>'.$str2.' - ';
  if($design==2){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
  echo $str1.'<a href="defense.php?setdesign=2">'.$defense_lang[design].' B</a>'.$str2.'<br><br>';
  echo '</div>'; 
}
?>
</div>
<br>
<?php include "fooban.php"; ?>
</body>
</html>
