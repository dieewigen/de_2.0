<?php 
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//  quests
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

if($_REQUEST['action']=='fracquests')
{
  $output='<div style="height: 2px; width: 100%;"></div>';
  $output.='<span style="position: absolute; top: 1px; right: 1px;">
  <a onclick="hide_mainarea();"><img src="'.$gpfad.'abutton3.gif" alt="Fenster schliessen" title="Fenster schliessen"></a>
  </span>';

  $output.=rahmen1a_oben('<div align="center"><b>Fraktionsaufgaben</b></div>');


  
  
  
//überprüfen ob er eine fraktionsaufgabe erfüllen möchte

if($_REQUEST["doquest"]>0 AND $player_atimer1time<time())
{
  $quest_id=intval($_REQUEST["doquest"]);
  
  //überprüfen ob man sich an den passenden koordinaten befindet
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
    $output.=$quest_text;
    $output.='<br><br><a onclick="(lnk(\'action=fracquests\'));"><span class="btn">weiter</span></a><br>';
    $output.= '<br>';
  }
}
else
{

//liste der aufgaben ausgeben

$output.='<table width="100%" border="0" cellpadding="0" cellspacing="1">';
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
$output.='<tr class="'.$bg.'"><td><b>Aufgabe</b></td><td align="center"><b>Koordinaten</b></td><td align="center"><b>Stufe</b></td></tr>';


//mögliche quests aus der datenbank auslesen
$i=0;
$db_daten=mysql_query("SELECT * FROM `sou_frac_quests` WHERE (fraction='$player_fraction' OR fraction=0) AND done=0 ORDER BY id",$soudb);
while($row = mysql_fetch_array($db_daten))
{
  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
  $output.='<tr class="'.$bg.'">';
  
  //questnamen auslesen
  include "soudata/questdata/questname".$row["id"].".php";
  
  //überprüfen, ob man sich evtl. auf den koordinaten befindet
  if($row['x']==$player_x AND $row['y']==$player_y)$questname='<a class="link1" onclick="(lnk(\'action=fracquests&doquest='.$row['id'].'\'));">'.$questname.'</a>';
  
  //echo $questname.' - Koordinaten: '.$row["x"].':'.$row["y"];
  if($row[fraction]==0)$hstr=' <img border="0" style="vertical-align: middle;" 
  src="'.$gpfad.'a15.gif" alt="fraktions&uuml;bergreifend" title="fraktions&uuml;bergreifend - die schnellste Fraktion wird siegen">';else {$hstr='';}
  $output.='
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

	$jsstr = 'Reiseinformationen&Zielkoordinaten: '.$row["x"].':'.$row["y"].'<br>Entfernung zur aktuellen Position:<br>X: '.($row["x"]-$player_x).'<br>Y: '.($row["y"]-$player_y).'<br>Lichtjahre: '.number_format($w3, "2",",",".").'<br>Zusatzinformationen: '.$questinfo;

  	$output.='<td align="center"><a onclick=\'show_map('.(round($row['x']/15)*15).', '.(round($row['y']/15)*15).');\'><span class="link1">'.$row["x"].':'.$row["y"].'</span></a>&nbsp;<img title="'.$jsstr.'" src="'.$gpfad.'a16.gif" width="16" height="16" border="0"></td>';
  }
  else 
  {
    $jsstr = 'Reiseinformationen&Zielkoordinaten: unbekannt<br>Zusatzinformationen: '.$questinfo;
    $output.='<td align="center">unbekannt&nbsp;<img title="'.$jsstr.'" src="'.$gpfad.'a16.gif" width="16" height="16" border="0"></td>';
  }
  $output.='<td align="center">'.($row["questlevel"]+1).'</td>';
  
  $output.='</tr>';
  $i++;
}

$output.= '</table>';
}



  $output.=rahmen1a_unten();
  $output.='<br>';

  $data[] = array ('output' => $output);
  echo json_encode($data);



}

?>