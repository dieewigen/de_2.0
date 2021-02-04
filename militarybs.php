<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_userartefact.inc.lang.php';
include "inc/userartefact.inc.php";
include 'inc/lang/'.$sv_server_lang.'_militarybs.lang.php';

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04,  restyp05, score, sector, system, newtrans, newnews, techs, artbldglevel FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$techs=$row["techs"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
$artbldglevel=$row["artbldglevel"];

$rangnamen=array("Der Erhabene", "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");

$tcost1=5;
$tcost2=10;

//artefaktupgrade
if($au AND $id AND $lvl)
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //rohstoffe auslesen
	$db_daten=mysql_query("SELECT restyp05 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
    $restyp05=$row['restyp05'];    
    $id=(int)$id;$lvl=(int)$lvl;
    //schauen ob man 2 artefakte hat
    $db_daten=mysql_query("SELECT id FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl'",$db);
    $num = mysql_num_rows($db_daten);

    if($num>=2)
    {
      if($restyp05>=$tcost1)
      {
        //rohstoffe abziehen
        $restyp05=$restyp05-$tcost1;
        mysql_query("UPDATE de_user_data SET restyp05=restyp05-'$tcost1' WHERE user_id='$ums_user_id'",$db);
    
        //schauen ob die artefakte schon auf dem maxlevel sind
        if($lvl<$ua_maxlvl[$id-1])
        {
          $errmsg.='<table width=600><tr><td class="ccg">'.$militarybs_lang[fehler].'</table>';
          //ein artefakt upgraden
          mysql_query("UPDATE de_user_artefact SET level=level+1 WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl' LIMIT 1",$db);
          //und ein artefakt entfernen
          mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl' LIMIT 1",$db);
        }
        else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[fehler2].'</table>';
      }
	  else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[fehler4].' ('.$tcost1.')</table>';
    }
    else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[fehler3].'</table>';
    

    //transaktionsende
    $erg = releaseLock($ums_user_id); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print($militarybs_lang[releaselock].$ums_user_id.$militarybs_lang[releaselock2]."<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$militarybs_lang[setlock].'</font><br><br>';
}//ende submit1

//artefakte verschieben
if ($_GET["a"]==1)
{
  //artefakt einfügen
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //flotten id festlegen
    $flotte=0;
    if($_GET["fid"]==1)$flotte=0;
    if($_GET["fid"]==2)$flotte=1;
    if($_GET["fid"]==3)$flotte=2;
    if($_GET["fid"]==4)$flotte=3;

    $id=(int)$_GET["id"];$lvl=(int)$_GET["lvl"];

    //ist das artefakt ein gültiges flottenartefakt?
    if($id==6 OR $id==7 OR $id==14 OR $id==15)
    {
      //schauen ob man das artefakte hat
      $db_daten=mysql_query("SELECT id FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl'",$db);
      $num = mysql_num_rows($db_daten);

      if($num>=1)
      {
        //flottendaten laden
        $fleetid=$ums_user_id.'-'.$flotte;
        $result=mysql_query("SELECT aktion, artid1, artid2, artid3 FROM de_user_fleet WHERE user_id='$fleetid'",$db);
        $row = mysql_fetch_array($result);

        //schauen ob die flotte daheim ist
        if($row["aktion"]==0)
        {
          //schauen ob ein artefaktplatz frei ist
          if($row["artid1"]==0 OR $row["artid2"]==0 OR $row["artid3"]==0)
          {
            //schauen welcher slot frei ist
            $useslot=3;
            if($row["artid3"]==0)$useslot=3;
            if($row["artid2"]==0)$useslot=2;
            if($row["artid1"]==0)$useslot=1;

            //flotte upadten
            mysql_query("UPDATE de_user_fleet SET artid$useslot='$id', artlvl$useslot='$lvl' WHERE user_id='$fleetid'",$db);
            //artefakt aus dem gebäude entfernen
            mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl' LIMIT 1",$db);
            $errmsg.='<table width=600><tr><td class="ccg">'.$militarybs_lang[error].'</td></tr></table>';
          }
          else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error2].'</table>';
        }
        else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error3].'</table>';
      }
      else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error4].'</table>';
    }
    else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error5].'</table>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print($militarybs_lang[releaselock].$ums_user_id.$militarybs_lang[releaselock2]."<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$militarybs_lang[setlock].'</font><br><br>';
}
elseif ($_GET["a"]==2)
{
  //artefakt entfernen
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //flotten id festlegen
    $flotte=0;
    if($_GET["fid"]==1)$flotte=0;
    if($_GET["fid"]==2)$flotte=1;
    if($_GET["fid"]==3)$flotte=2;
    if($_GET["fid"]==4)$flotte=3;

    $id=(int)$_GET["id"];
    if($id<1 OR $id>3)$id=1;

    //flottendaten laden
    $fleetid=$ums_user_id.'-'.$flotte;
    $result=mysql_query("SELECT aktion, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3 FROM de_user_fleet WHERE user_id='$fleetid'",$db);
    $row = mysql_fetch_array($result);

    //ist das artefakt im basisschiff vorhanden
    if($row["artid$id"]>0)
    {
      //schauen ob im artefakggebäude platz ist
      $db_datenx=mysql_query("SELECT user_id FROM de_user_artefact WHERE user_id='$ums_user_id'",$db);
      $numx = mysql_num_rows($db_datenx);
      if($numx<$artbldglevel) //es gibt noch platz
      {

        //schauen ob die flotte daheim ist
        if($row["aktion"]==0)
        {
          //flotte upadten
          mysql_query("UPDATE de_user_fleet SET artid$id=0, artlvl$id=0 WHERE user_id='$fleetid'",$db);
          //artefakt in das gebäude transferieren
          $artid=$row["artid$id"];$artlvl=$row["artlvl$id"];
          mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '$artlvl')",$db);
          $errmsg.='<table width=600><tr><td class="ccg">'.$militarybs_lang[error6].'</td></tr></table>';
        }
        else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error7].'</table>';
      }
      else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error8].'</table>';
    }
    else $errmsg.='<table width=600><tr><td class="ccr">'.$militarybs_lang[error9].'</table>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print($militarybs_lang[releaselock].$ums_user_id.$militarybs_lang[releaselock2]."<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$militarybs_lang[setlock].'</font><br><br>';
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$militarybs_lang[title]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?//stelle die ressourcenleiste dar
include "resline.php";
if ($errmsg!='')echo $errmsg;

if ($techs[13]==0)
{
$techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=13";
$db_tech=mysql_query($techcheck,$db);
$row_techcheck = mysql_fetch_array($db_tech);
echo "Es wird ein(e) ".$row_techcheck[tech_name]." benötigt.";
}
else
{
  echo '<form action="militarybs.php" method="POST" name="milform">';

  //oberes menü darstellen
  $flotte=0;$fname='Heimatflotte';
  if($_GET["fid"]==1){$hs[0]='<b>>> ';$flotte=0;$fname=$militarybs_lang[heimatflotte];}
  if($_GET["fid"]==2){$hs[1]='<b>>> ';$flotte=1;$fname=$militarybs_lang[flotte1];}
  if($_GET["fid"]==3){$hs[2]='<b>>> ';$flotte=2;$fname=$militarybs_lang[flotte2];}
  if($_GET["fid"]==4){$hs[3]='<b>>> ';$flotte=3;$fname=$militarybs_lang[flotte3];}

  echo '<table width=600><tr>
	<td width="25%" class="cl"><a href="militarybs.php?fid=1">'.$hs[0].$militarybs_lang[heimatflotte].'</a></td>
	<td width="25%" class="cl"><a href="militarybs.php?fid=2">'.$hs[1].$militarybs_lang[flotte1].'</a></td>
	<td width="25%" class="cl"><a href="militarybs.php?fid=3">'.$hs[2].$militarybs_lang[flotte2].'</a></td>
	<td width="25%" class="cl"><a href="militarybs.php?fid=4">'.$hs[3].$militarybs_lang[flotte3].'</a></td>
	</tr>
    </table><br>';

  //flottendaten laden
  $fleetid=$ums_user_id.'-'.$flotte;
  $result=mysql_query("SELECT komatt, komdef, aktion, artid1, artlvl1, artid2, artlvl2, artid3, artlvl3  FROM de_user_fleet WHERE user_id='$fleetid'",$db);
  $row = mysql_fetch_array($result);

  if ($row["aktion"]!=0)$hsz=' (Einsatz)';else $hsz='';

  //flottendaten ausgeben
  //rahmen öffnen
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro">'.$militarybs_lang[basisschiff].' - '.$fname.$hsz.'</td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  //rangwerte-liste erstellen
  $ranginfo='&';
  for($i=0;$i<25;$i++)
  {
    $counter=($i*($i-1)*30000)+30000;
    if($i==0)$counter=0;
    $ranginfo.=$rangnamen[24-$i].': '.number_format($counter, 0,",",".").'<br>';
  }
  
  
  
  //flottendaten ausgeben
  echo '<table width=580>';
  echo '<tr class="cell1" align="center"><td width="160">&nbsp;</td><td width="100"><b>'.$militarybs_lang[rang].' <img title="'.$ranginfo.'" src="'.
  $ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td><td width="100"><b>'.$militarybs_lang[erfahrung].'</td><td width="220"><b>'.$militarybs_lang[rangbonus].'</td></tr>';
  
  $bonus ='+ '.number_format(((24-getfleetlevel($row["komatt"]))*0.4), 2,",",".").'% '.$militarybs_lang[angriffskraft].'<br>';
  $bonus.='+ '.number_format(((24-getfleetlevel($row["komatt"]))*0.4), 2,",",".").'% '.$militarybs_lang[laehmkraft];
  echo '<tr class="cell" align="center"><td><b>'.$militarybs_lang[aformation].'</td><td>'.$rangnamen[getfleetlevel($row["komatt"])].'</td><td>'.number_format($row["komatt"], 0,"",".").'</td><td>'.$bonus.'</td></tr>';
  $bonus ='+ '.number_format(((24-getfleetlevel($row["komdef"]))*0.4), 2,",",".").'% '.$militarybs_lang[angriffskraft].'<br>';
  $bonus.='+ '.number_format(((24-getfleetlevel($row["komdef"]))*0.4), 2,",",".").'% '.$militarybs_lang[laehmkraft];
  echo '<tr class="cell1" align="center"><td><b>'.$militarybs_lang[vformation].'</td><td>'.$rangnamen[getfleetlevel($row["komdef"])].'</td><td>'.number_format($row["komdef"], 0,"",".").'</td><td>'.$bonus.'</td></tr>';
  echo '</table>';
  //artefaktslots
  echo '<table width=580>';
  echo '<tr class="cell" align="center"><td colspan="6"><b>'.$militarybs_lang[eingelagerte].'</td></tr>';
  echo '<tr class="cell1" align="center"><td width="50">&nbsp;</td><td width="50"><b>'.$militarybs_lang[arti].'</td><td width="50"><b>'.$militarybs_lang[level].'</td><td width="50"><b>'.$militarybs_lang[bonus].'</td><td width="410"><b>'.$militarybs_lang[info].'</td><td width="80"><b>'.$militarybs_lang[aktion].'</td></tr>';
  //slot 1
  $fn1='artid1';$fn2='artlvl1';
  if($row[$fn1]>0)$feld1='<img src="'.$ums_gpfad.'g/arte'.$row[$fn1].'.gif" border="0" alt="'.$ua_name[$row[$fn1]-1].'" title="'.$ua_name[$row[$fn1]-1].'">';else $feld1='-';
  if($row[$fn1]>0)$feld2=$row[$fn2].' ('.$ua_maxlvl[$row[$fn1]-1].')';else $feld2='-';
  if($row[$fn1]>0)$feld3=number_format($ua_werte[$row[$fn1]-1][$row[$fn2]-1][0], 2,",",".").'%';else $feld3='-';
  if($row[$fn1]>0)$feld4=$ua_desc[$row[$fn1]-1];else $feld4='freier Slot';
  if($row[$fn1]>0)$feld5='<a href="militarybs.php?fid='.($flotte+1).'&id=1&a=2">'.$militarybs_lang[entfernen].'</a>';else $feld5='-';
  echo '<tr class="cell" align="center"><td>'.$militarybs_lang[slot1].'</td><td>'.$feld1.'</td><td>'.$feld2.'</td><td>'.$feld3.'</td><td>'.$feld4.'</td><td>'.$feld5.'</td></tr>';
  //slot 2
  $fn1='artid2';$fn2='artlvl2';
  if($row[$fn1]>0)$feld1='<img src="'.$ums_gpfad.'g/arte'.$row[$fn1].'.gif" border="0" alt="'.$ua_name[$row[$fn1]-1].'">';else $feld1='-';
  if($row[$fn1]>0)$feld2=$row[$fn2].' ('.$ua_maxlvl[$row[$fn1]-1].')';else $feld2='-';
  if($row[$fn1]>0)$feld3=number_format($ua_werte[$row[$fn1]-1][$row[$fn2]-1][0], 2,",",".").'%';else $feld3='-';
  if($row[$fn1]>0)$feld4=$ua_desc[$row[$fn1]-1];else $feld4='freier Slot';
  if($row[$fn1]>0)$feld5='<a href="militarybs.php?fid='.($flotte+1).'&id=2&a=2">'.$militarybs_lang[entfernen].'</a>';else $feld5='-';
  echo '<tr class="cell1" align="center"><td>'.$militarybs_lang[slot2].'</td><td>'.$feld1.'</td><td>'.$feld2.'</td><td>'.$feld3.'</td><td>'.$feld4.'</td><td>'.$feld5.'</td></tr>';
  //slot 3
  $fn1='artid3';$fn2='artlvl3';
  if($row[$fn1]>0)$feld1='<img src="'.$ums_gpfad.'g/arte'.$row[$fn1].'.gif" border="0" alt="'.$ua_name[$row[$fn1]-1].'">';else $feld1='-';
  if($row[$fn1]>0)$feld2=$row[$fn2].' ('.$ua_maxlvl[$row[$fn1]-1].')';else $feld2='-';
  if($row[$fn1]>0)$feld3=number_format($ua_werte[$row[$fn1]-1][$row[$fn2]-1][0], 2,",",".").'%';else $feld3='-';
  if($row[$fn1]>0)$feld4=$ua_desc[$row[$fn1]-1];else $feld4='freier Slot';
  if($row[$fn1]>0)$feld5='<a href="militarybs.php?fid='.($flotte+1).'&id=3&a=2">'.$militarybs_lang[entfernen].'</a>';else $feld5='-';
  echo '<tr class="cell" align="center"><td>'.$militarybs_lang[slot3].'</td><td>'.$feld1.'</td><td>'.$feld2.'</td><td>'.$feld3.'</td><td>'.$feld4.'</td><td>'.$feld5.'</td></tr>';

  echo '</table>';
  
  //rahmen schließen
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table>';

  //hier die artefakte ausgeben, die man einsetzen kann
  //rahmen öffnen
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table>';

  //artefakte ausgeben
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro">'.$militarybs_lang[flottenartis].'</td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table width=580>';
  echo '<tr class="cc"><td width="50"><b>'.$militarybs_lang[arti].'</td><td width="50"><b>'.$militarybs_lang[level].'</td><td width="50"><b>'.$militarybs_lang[bonus].'</td><td width="460"><b>'.$militarybs_lang[info].'</td><td width="80"><b>'.$militarybs_lang[aktion].'</td></tr>';
  //artefakte aus der db holen und darstellen
  $db_daten=mysql_query("SELECT id, level FROM de_user_artefact WHERE user_id='$ums_user_id' AND (id=6 OR id=7 OR id=14 OR id=15) ORDER BY id, level",$db);
  while($row = mysql_fetch_array($db_daten))
  {
    //schauen ob der upgrade-button angezeigt werden soll
    if($row["level"]>=$ua_maxlvl[$row["id"]-1])$str='&nbsp;';else $str='<br><a href="militarybs.php?fid='.($flotte+1).'&au=1&id='.$row["id"].'&lvl='.$row["level"].'" title="'.$tcost1.' Tronic">'.$militarybs_lang[upgrade].'</a>';
    
    //wenn noch nicht maxlevel anzeigen wieviel man nach dem update hat
    if($row["level"]>=$ua_maxlvl[$row["id"]-1])$str1='&nbsp;';else $str1='<br><b>'.$militarybs_lang[upinfo5].' '.number_format($ua_werte[$row["id"]-1][$row["level"]][0], 2,",",".").'%';

  	echo '<tr>
     <td><img src="'.$ums_gpfad.'g/arte'.$row["id"].'.gif" border="0" alt="'.$ua_name[$row["id"]-1].'"></td>
     <td class="cc">'.$row["level"].' ('.$ua_maxlvl[$row["id"]-1].')</td>
     <td class="cc">'.number_format($ua_werte[$row["id"]-1][$row["level"]-1][0], 2,",",".").'%</td>
     <td class="cc">'.$ua_desc[$row["id"]-1].$str1.'</td>
     <td class="cc"><a href="militarybs.php?fid='.($flotte+1).'&id='.$row["id"].'&lvl='.$row["level"].'&a=1">'.$militarybs_lang[einfuegen].$str.'</a></td>
     </tr>';
  }
  echo '</table>';
  //rahmen schließen
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';



echo '</form>';
} //raumwerftbedinung ende
?>
<?php include "fooban.php"; ?>
</body>
</html>

