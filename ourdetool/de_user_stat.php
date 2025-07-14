<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "../inc/lang/1_statistics.lang.php";

$ums_user_id=$uid;

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, status FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];
$restyp05=$row[4];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];$hasally=$row["status"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?=$stat_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?//stelle die ressourcenleiste dar
include "det_userdata.inc.php";

if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;
if($_REQUEST["mp"]==1)
{
  echo '<table width=600><tr>
    <td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=1"><b>>> '.$stat_lang['spieler'].'</b></a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=2">'.$stat_lang['sektor'].'</a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=3">'.$stat_lang['allianz'].'</a></td>
	</tr>
    </table><br>';
  echo'
    <h1>'.$stat_lang['punkteentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=1">
    <br><br>'.$stat_lang['kollektorentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=2">
    <br><br>'.$stat_lang['cybentwexp'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=3"><br><br>';
    
  echo '<table width="580" border="0" cellpadding="0" cellspacing="1">';
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td>'.$stat_lang['datum'].'</td><td>00</td><td>01</td><td>02</td><td>03</td><td>04</td><td>05</td><td>06</td><td>07</td><td>08</td><td>09</td><td>10</td><td>11</td><td>12</td>
  <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>';
  echo '</tr>';
  
  //daten auslesen
  $db_daten=mysql_query("SELECT * FROM de_user_stat WHERE user_id='$ums_user_id' ORDER BY datum DESC",$db);


  //daten ausgeben
  $bgcolors[0]='FF0000';
  $bgcolors[1]='F88017';
  $bgcolors[2]='00FF00';
  
  while($row = mysql_fetch_array($db_daten))
  {
  	$bg='cell';
  	echo '<tr class="'.$bg.'" align="center">';
  	echo '<td>'.$row["datum"].'</td>';
  	for($i=0;$i<=23;$i++)
  	{
  	  if($row["h$i"]==0)$bgcolor=$bgcolors[0];
  	  if($row["h$i"]==1)$bgcolor=$bgcolors[1];
  	  if($row["h$i"]==2)$bgcolor=$bgcolors[2];
  	  
  	  echo '<td style="background-color: #'.$bgcolor.';">&nbsp;</td>';
  	}
  	echo '</tr>';
  }  
  //legende
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td colspan="25">Legende: <font color="#'.$bgcolors[0].'">'.$stat_lang['legende1'].'</font> <font color="#'.$bgcolors[1].'">'.$stat_lang['legende2'].'</font> <font color="#'.$bgcolors[2].'">'.$stat_lang['legende3'].'</font></td></tr>';
  
  echo '</table>';
  //rahmen unten
  //rahmen_unten();
  
  
}
elseif($_REQUEST["mp"]==2)
{
  echo '<table width=600><tr>
    <td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=1">'.$stat_lang['spieler'].'</b></a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=2"><b>>> '.$stat_lang['sektor'].'</a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=3">'.$stat_lang['allianz'].'</a></td>
	</tr>
    </table><br>';
    
  echo'
    <h1>'.$stat_lang['punkteentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=11">
    <br><br>'.$stat_lang['kollektorentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=12">
    <br><br>'.$stat_lang['platzentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=13">';
}
elseif($_REQUEST["mp"]==3)
{
  echo '<table width=600><tr>
    <td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=1">'.$stat_lang['spieler'].'</b></a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=2">'.$stat_lang['sektor'].'</a></td>
	<td width="33%\" class="cl"><a href="de_user_stat.php?uid='.$uid.'&mp=3"><b>>> '.$stat_lang['allianz'].'</a></td>
	</tr>
    </table><br>';
  //schauen ob man eine allianz hat
  if ($hasally==1)
  echo'
    <h1>'.$stat_lang['punkteentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=21">
    <br><br>'.$stat_lang['kollektorentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=22">
    <br><br>'.$stat_lang['platzentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=23">
    <br><br>'.$stat_lang['mitgliederentwicklung'].'<br><br>
    <img src="de_user_stat_genpic.php?uid='.$uid.'&typ=24">';
  else echo '<h1>'.$stat_lang['noally'];
}
?>
</body>
</html>
