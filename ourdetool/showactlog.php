<?php
include "../inc/sv.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Gleiche IP</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
include "det_userdata.inc.php";

echo '<table width="580" border="0" cellpadding="0" cellspacing="1">';
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td width="100">'.$stat_lang[datum].'</td><td>00</td><td>01</td><td>02</td><td>03</td><td>04</td><td>05</td><td>06</td><td>07</td><td>08</td><td>09</td><td>10</td><td>11</td><td>12</td>
  <td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>';
  echo '</tr>';
  
//db konnekten
if(!$db)include "../inccon.php";
//richtige tabelle auswählen
mysql_select_db('de_supporttool', $db);

$username=$_REQUEST["id"];
  
  //daten auslesen
  $db_daten=mysql_query("SELECT * FROM de_user_stat WHERE username='$username' ORDER BY datum DESC LIMIT 7",$db);

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
  	  if($row["h$i"]>0)$bgcolor=$bgcolors[2];
  	  
  	  echo '<td bgcolor="#'.$bgcolor.'">'.$row["h$i"].'</td>';
  	}
  	echo '</tr>';
  }  
  //legende
  $bg='cell';
  echo '<tr class="'.$bg.'" align="center"><td colspan="25">'.$stat_lang[legende].' <font color="#'.$bgcolors[0].'">'.$stat_lang[legende1].'</font> <font color="#'.$bgcolors[1].'">'.$stat_lang[legende2].'</font> <font color="#'.$bgcolors[2].'">'.$stat_lang[legende3].'</font></td></tr>';
  
  echo '</table>';
?>
</body>
</html>
