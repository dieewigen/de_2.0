<?php
echo '<br><br>';

rahmen0_oben();

rahmen1_oben('<div align="center"><b>Rangliste</b></div>');

$output='<table width="100%" border="0" cellpadding="1" cellspacing="1">';
$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr align="center" class="'.$bg.'"><td><b>Platz</b></td><td><b>Name</b></td><td><b>Inkarnation</b></td><td><b>Stufe</b></td><td><b>Punkte</b></td><td><b>Ruhm</b></td></tr>';
$platz=1;
$db_daten=mysql_query("SELECT * FROM `de_cyborg_data` ORDER BY incarnation DESC, exp DESC LIMIT 100",$eftadb);
while($row = mysql_fetch_array($db_daten))
{
  //hintergrund bestimmen
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

  $output.='<tr class="'.$bg.'" align="center">';
  //platz
  $output.='<td>'.$platz.'</td>';
  //name
  $output.='<td>'.$row["spielername"].' {'.$row["sn_ext1"].'}</td>';
  //inkarnation
  $output.='<td>'.$row["incarnation"].'</td>';
  //stufe
  $output.='<td>'.$row["level"].'</td>';
  //punkte
  $output.='<td>'.number_format($row["exp"], 0,"",".").'</td>';
  //ruhm
  $output.='<td>'.$row["fame"].'</td>';
  
  $output.='</tr>';
  $platz++;
}

$output.='</table>';

echo $output;


rahmen1_unten();

rahmen0_unten();

//infoleiste anzeigen
show_infobar();

echo '</body></html>';
exit;
?>


