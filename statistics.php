<?php
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_statistics.lang.php";
include "functions.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, status FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];
$restyp05=$row['restyp05'];$punkte=$row['score'];$newtrans=$row['newtrans'];$newnews=$row['newnews'];
$sector=$row['sector'];$system=$row['system'];$hasally=$row['status'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $stat_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";
if(!isset($_REQUEST['mp'])){
  $_REQUEST['mp']=1;
}

if($_REQUEST['mp']==1)
{
  echo '<table width="600"><tr>
    <td width="25%\" class="cl"><a href="statistics.php?mp=1"><b>>> '.$stat_lang['spieler'].'</b></a></td>
	<td width="25%\" class="cl"><a href="statistics.php?mp=2">'.$stat_lang['sektor'].'</a></td>
	<td width="25%\" class="cl"><a href="statistics.php?mp=3">'.$stat_lang['allianz'].'</a></td>
	<td width="25%\" class="cl"><a href="statistics.php?mp=4">'.$stat_lang['server'].'</a></td>
	</tr>
    </table><br>';
  echo'
    <div class="cellu" style="width: 600px;">'.$stat_lang['punkteentwicklung'].'</div>
    <img src="statistics_genpic.php?typ=1" width="600">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['kollektorentwicklung'].'</div>
    <img src="statistics_genpic.php?typ=2" width="600">
    <br><br>';
  //aktivit�t
  rahmen_oben($stat_lang['aktivitaet']);
  //tabellenkopf
  echo '<table width="580" border="0" cellpadding="0" cellspacing="1">';
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td width="100">'.$stat_lang['datum'].'</td><td>00</td><td>01</td><td>02</td><td>03</td><td>04</td><td>05</td><td>06</td><td>07</td><td>08</td><td>09</td><td>10</td><td>11</td><td>12</td>
  <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>';
  echo '</tr>';
  
  //daten auslesen
  $sql = "SELECT * FROM de_user_stat WHERE user_id=? ORDER BY datum DESC LIMIT 7";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);


  //daten ausgeben
  $bgcolors[0]='FF0000';
  $bgcolors[1]='F88017';
  $bgcolors[2]='00FF00';
  
  while($row = mysqli_fetch_assoc($db_daten))
  {
  	$bg='cell';
  	echo '<tr align="center">';
  	echo '<td class="'.$bg.'">'.$row['datum'].'</td>';
  	for($i=0;$i<=23;$i++)
  	{
  	  if($row['h'.$i]==0)$bgcolor=$bgcolors[0];
  	  if($row['h'.$i]==1)$bgcolor=$bgcolors[1];
  	  if($row['h'.$i]==2)$bgcolor=$bgcolors[2];
  	  
  	  echo '<td bgcolor="#'.$bgcolor.'">&nbsp;</td>';
  	}
  	echo '</tr>';
  }  
  //legende
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td colspan="25">'.$stat_lang['legende'].' <font color="#'.$bgcolors[0].'">'.$stat_lang['legende1'].'</font> <font color="#'.$bgcolors[1].'">'.$stat_lang['legende2'].'</font> <font color="#'.$bgcolors[2].'">'.$stat_lang['legende3'].'</font></td></tr>';
  
  echo '</table>';
  //rahmen unten
  rahmen_unten();
  
  //kollektoren die dir gestohlen worden sind
  $sql = "SELECT SUM(CASE WHEN colanz < 0 THEN colanz * -1 ELSE colanz END) AS colanz FROM de_user_getcol WHERE zuser_id=?";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  
  $row = mysqli_fetch_assoc($db_daten);
  rahmen_oben($stat_lang['verlorenekollektoren'].' ('.number_format($row['colanz'], 0, ',' ,'.').')');
  //tabellenkopf
  echo '<table width="580px">';
  echo '<tr align="center" class="cell"><td>Zeitpunkt</td><td>Kollektoren</td><td>Spielername</td></tr>';
  $sql = "SELECT * FROM de_user_getcol WHERE zuser_id=?";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  while($row = mysqli_fetch_assoc($db_daten)){
    $time=date("Y-m-d H:i:s", $row['time']);
  	//spielername des diebes
  	$duid=$row['user_id'];
  	$sql2 = "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id=?";
  	$result = mysqli_execute_query($GLOBALS['dbi'], $sql2, [$duid]);
  	$num = mysqli_num_rows($result);
  	if($num==1)
  	{
  	  $rowx = mysqli_fetch_assoc($result);
  	  $spielername='<a href="details.php?se='.$rowx['sector'].'&sy='.$rowx['system'].'">'.$rowx['spielername'].' ('.$rowx['sector'].':'.$rowx['system'].')</a>';
  	}
  	else $spielername=$stat_lang['geloeschterspieler'];
    echo '<tr align="center" class="cell"><td>'.$time.'</td><td>'.$row['colanz'].'</td><td>'.$spielername.'</td></tr>';
  }
  echo '<tr class="cell"><td colspan="3">Negative Kollektorenzahlen bedeuten, dass die Kollektoren beim Angriff zerst&ouml;rt worden sind.</td></tr>';
  echo '</table>';  
  rahmen_unten();
  
  //kollektoren die du erobert hast
  $sql = "SELECT SUM(colanz) AS colanz FROM de_user_getcol WHERE user_id=? AND colanz>0";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  $row = mysqli_fetch_assoc($db_daten);
  rahmen_oben($stat_lang['erobertekollektoren'].' ('.number_format($row['colanz'], 0, ',' ,'.').')');
  
  echo '<table width="580px">';
  echo '<tr align="center" class="cell"><td>Zeitpunkt</td><td>Kollektoren</td><td>Spielername</td></tr>';
  $sql = "SELECT * FROM de_user_getcol WHERE user_id=?";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  while($row = mysqli_fetch_assoc($db_daten))
  {
    $time=date("Y-m-d H:i:s", $row['time']);
  	//spielername des diebes
  	$duid=$row['zuser_id'];
  	$sql2 = "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id=?";
  	$result = mysqli_execute_query($GLOBALS['dbi'], $sql2, [$duid]);
  	$num = mysqli_num_rows($result);
  	if($num==1)
  	{
  	  $rowx = mysqli_fetch_assoc($result);
  	  $spielername='<a href="details.php?se='.$rowx['sector'].'&sy='.$rowx['system'].'">'.$rowx['spielername'].' ('.$rowx['sector'].':'.$rowx['system'].')</a>';
  	}
  	else $spielername=$stat_lang['geloeschterspieler'];
    echo '<tr align="center" class="cell"><td>'.$time.'</td><td>'.$row['colanz'].'</td><td>'.$spielername.'</td></tr>';
  }
  echo '<tr class="cell"><td colspan="3">Negative Kollektorenzahlen bedeuten, dass die Kollektoren beim Angriff zerst&ouml;rt worden sind.</td></tr>';
  echo '</table>';
  
  
  rahmen_unten();
  
  
}
elseif($_REQUEST['mp']==2)
{
  echo '<table width=600><tr>
    <td width="25%" class="cl"><a href="statistics.php?mp=1">'.$stat_lang['spieler'].'</b></a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=2"><b>>> '.$stat_lang['sektor'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=3">'.$stat_lang['allianz'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=4">'.$stat_lang['server'].'</a></td>
	</tr>
    </table><br>';
    
  echo'
    <div class="cellu" style="width: 600px;">'.$stat_lang['punkteentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=11">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['kollektorentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=12">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['platzentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=13">';
  
  rahmen_oben($stat_lang['sektorereignisse']);  
  echo '<table width=570><tr align="center">
    <td width="40" class="cell">'.$stat_lang['wt'].'</td>
	<td width="530" class="cell">'.$stat_lang['ereignis'].'</td>
	</tr>';
  
  //$sector=137;
  $sql="SELECT * FROM de_news_sector WHERE sector=? ORDER BY wt DESC";
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
  
  if($_SESSION['ums_user_id']==1){
	  //echo $sql;
  }
 
  //typdefinitionen:
  //2: ein spieler kommt
  //3: ein spieler geht
  //4: sektorkollektoren
  //5: sektorstatus sichtbar
  //6: sektorstatus unsichtbar
  $c1=0;
  while($row = mysqli_fetch_assoc($db_daten))
  {
  	if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';} 
  	
  	if($row['typ']==2)//ein spieler kommt
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
  	  $wert2=$stat_lang['spielerkommt'].': '.$row['text'];
  	}
  	elseif($row['typ']==3)//ein spieler geht
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
	  $wert2=$stat_lang['spielerverloren'].': '.$row['text'];
  	}
  	elseif($row['typ']==4)//sektorkollektoren
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
	  $wert2=$stat_lang['sektorkollektoren'].': '.$row['text'];
  	}
  	elseif($row['typ']==5)//sektorstatus sichtbar
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
	  $wert2=$stat_lang['sektorstatussichtbar'].': '.$row['text'];
  	}  	
  	elseif($row['typ']==6)//sektostatus unsichtbar
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
	    $wert2=$stat_lang['sektorstatusversteckt'].': '.$row['text'];
  	}elseif($row['typ']==7)//sektorgebäudebau
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
	    $wert2='Bauauftrag: '.$row['text'];
  	}  
  	
  	//echo $row['id'].'<br>';
  	//ausgabe
  	echo '<tr class="'.$bg.'" align="center">';
  	echo '<td>'.$wert1.'</td>';
  	echo '<td align="left">'.$wert2.'</td>';
  	echo '</tr>';
  	
  	
  }  
  echo '</table>';
  rahmen_unten();
  
  
}
elseif($_REQUEST['mp']==3)
{
  echo '<table width=600><tr>
    <td width="25%" class="cl"><a href="statistics.php?mp=1">'.$stat_lang['spieler'].'</b></a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=2">'.$stat_lang['sektor'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=3"><b>>> '.$stat_lang['allianz'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=4">'.$stat_lang['server'].'</a></td>
	</tr>
    </table><br>';
  //schauen ob man eine allianz hat
  if ($hasally==1)
  echo'
    <div class="cellu" style="width: 600px;">'.$stat_lang['punkteentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=21">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['kollektorentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=22">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['platzentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=23">
    <br><br><div class="cellu" style="width: 600px;">'.$stat_lang['mitgliederentwicklung'].'</div><br><br>
    <img src="statistics_genpic.php?typ=24">';
  else echo '<div class="cellu" style="width: 600px;">'.$stat_lang['noally'].'</div>';
}
elseif($_REQUEST['mp']==4)
{
  echo '<table width=600><tr>
    <td width="25%" class="cl"><a href="statistics.php?mp=1">'.$stat_lang['spieler'].'</b></a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=2">'.$stat_lang['sektor'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=3">'.$stat_lang['allianz'].'</a></td>
	<td width="25%" class="cl"><a href="statistics.php?mp=4"><b>>> '.$stat_lang['server'].'</a></td>
	</tr>
    </table><br>';
  //gr��ter tick
  $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data");
  $row = mysqli_fetch_assoc($result);
  $wt = $row['tick']-96;
  
  //artefaktnamen auslesen
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, artname FROM `de_artefakt`");
  while($row = mysqli_fetch_assoc($db_daten))
  {
    $artdata[$row['id']]=$row['artname'];
  }	  

  //Serveralter
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system");
  $row = mysqli_fetch_assoc($db_daten);
  echo '<div>Server-Wirtschaftsticks: '.number_format($row['wt']).'</div>';
  echo '<div>Server-Kampfticks: '.number_format($row['kt']).'</div>';  

  //Serverevents
  rahmen_oben($stat_lang['serverereignisse']);
  echo '<table width=570><tr align="center">
    <td width="40" class="cell">'.$stat_lang['wt'].'</td>
	<td width="530" class="cell">'.$stat_lang['ereignis'].'</td>
	</tr>';


  
  $sql = "SELECT * FROM de_news_server WHERE wt<=? ORDER BY id DESC";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$wt]);
  //typdefinitionen:
  //0: sektorartefakt hypersturm
  //1: sektorartefakt angriff
  $c1=0;
  while($row = mysqli_fetch_assoc($db_daten))
  {
  	if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';} 
  	
  	if($row['typ']==0)//sektorartefakt hypersturm
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
  	  $wert2=$stat_lang['sektorartefakt1'];
  	  $daten=explode(';', $row['text']);
  	  $wert2.='<br>'.$stat_lang['sektorartefakt'].': '.$artdata[$daten[0]];
  	  if($daten[1]>0)$wert2.='<br>'.$stat_lang['herkunftssektor'].': '.$daten[1];
  	  $wert2.='<br>'.$stat_lang['zielsektor'].': '.$daten[2];
  	}
  	elseif($row['typ']==1)//sektorartefakt angriff
  	{
  	  $wert1=number_format($row['wt'], 0,"",".");
  	  $wert2=$stat_lang['sektorartefakt2'];
  	  $daten=explode(';', $row['text']);
  	  $wert2.='<br>'.$stat_lang['sektorartefakt'].': '.$artdata[$daten[0]];
  	  if($daten[1]>0)$wert2.='<br>'.$stat_lang['herkunftssektor'].': '.$daten[1];
  	  $wert2.='<br>'.$stat_lang['zielsektor'].': '.$daten[2];
  	}
  	
  	//echo $row['id'].'<br>';
  	//ausgabe
  	echo '<tr class="'.$bg.'" align="center">';
  	echo '<td>'.$wert1.'</td>';
  	echo '<td align="left">'.$wert2.'</td>';
  	echo '</tr>';
  	
  	
  }  
  echo '</table>';
  rahmen_unten();

}
?>
</body>
</html>
