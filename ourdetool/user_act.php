<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "../inc/lang/1_statistics.lang.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo $stat_lang['title']; ?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php //stelle die ressourcenleiste dar
include "det_userdata.inc.php";

//farben definieren
$bgcolors[0]='FF0000';
$bgcolors[1]='F88017';
$bgcolors[2]='00FF00';

//daten auslesen
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_stat WHERE (h0+h1+h2+h3+h4+h5+h6+h7+h8+h9+h10+h11+h12+h13+h14+h15+h16+h17+h18+h19+h20+h21+h22+h23)>=42
 ORDER BY user_id, datum DESC");

$olduid=0;

  //daten ausgeben
  while($row = mysqli_fetch_assoc($db_daten))
  {
    if($olduid!=$row['user_id'] AND $olduid!=0) echo '</table>';
    
    if($olduid!=$row['user_id'])
    {
	  //tabellenkopf
	  //userdaten auslesen
	  $result=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id=?", [$row['user_id']]);
      $rowx = mysqli_fetch_assoc($result);
	  echo '<br>User-ID: <a href="idinfo.php?UID='.$row['user_id'].'">'.$row['user_id'].'</a> Spielername: '.$rowx['spielername'];
	  $result=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_login WHERE user_id=?", [$row['user_id']]);
      $de_login = mysqli_fetch_assoc($result);	  
      if ($de_login["status"]==0)$status='Inaktiv';
  	  elseif ($de_login["status"]==1)$status='Aktiv';
  	  elseif ($de_login["status"]==2)$status='Gesperrt';
  	  elseif ($de_login["status"]==3)$status='Urlaub';
	  echo ' Status: '.$status.'<br>';
      
  	  echo '<table width="580" border="0" cellpadding="0" cellspacing="1">';
  	  $bg='cell';
  	  echo '<tr class="'.$bg.'" align="center"><td>'.$stat_lang['datum'].'</td><td>00</td><td>01</td><td>02</td><td>03</td><td>04</td><td>05</td><td>06</td><td>07</td><td>08</td><td>09</td><td>10</td><td>11</td><td>12</td>
  	  <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>';
  	  echo '</tr>';
    }
  
  
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
  	$olduid=$row['user_id'];
  }  
  //legende
  echo '</table><br>Legende: <font color="#'.$bgcolors[0].'">'.$stat_lang['legende1'].'</font> <font color="#'.$bgcolors[1].'">'.$stat_lang['legende2'].'</font> <font color="#'.$bgcolors[2].'">'.$stat_lang['legende3'].'</font>';
  
  
  //rahmen unten
  //rahmen_unten();

?>
</body>
</html>
