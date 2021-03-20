<?php

echo '<div align="center"><br />';

rahmen0_oben();
echo '<br />';

$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Sonnensystemvorgaben</b></td>
<td width="120">&nbsp;</td>
</tr></table>';
rahmen1_oben($routput);

//überprüfen ob es der vorsitzende ist
if(get_fracleader_id($player_fraction)==$player_user_id)
{
  //schauen ob man etwas ändern möchte
  
  if(isset($_REQUEST["systemid"]))
  {
  	$systemid=intval($_REQUEST["systemid"]);
  	$newmaxgeblevel=intval($_REQUEST["newmaxgeblevel"]);
  	
  	//neuen maximallevel setzen
  	mysql_query("UPDATE `sou_map` SET maxgeblevel='$newmaxgeblevel' WHERE id='$systemid' AND fraction='$player_fraction'",$soudb);
	$num = mysql_affected_rows();
    if($num==1) echo '<br><font color="#00FF00">Die neue Maximalstufe wurde gesetzt.</font><br><br>';
    else echo '<br><font color="#FF0000">Die Maximalstufe wurde nicht ge&auml;ndert.</font><br><br>';
  	
  }
	
  echo '<form action="sou_main.php" method="POST" name="f1">';
  echo '<input type="hidden" name="action" value="politicscolonypage">';
  echo '<input type="hidden" name="do" value="1">';
  
	// einen maximallevel der gebäude für das sonnensystem festlegen
  
  //zuerst alle systeme auslesen welche der fraktion gehören und in ein select-feld packen
      
  $selectauswahl='<select name="systemid">';
  $db_daten=mysql_query("SELECT * FROM `sou_map` WHERE fraction='$player_fraction' ORDER BY x ASC, y ASC",$soudb);
  while($row = mysql_fetch_array($db_daten))
  {
    $selectauswahl.='<option value="'.$row[id].'">'.$row[x].'/'.$row[y].' - '.$row[sysname].' ('.$row[maxgeblevel].')</option>';
  }  	
  $selectauswahl.='</select>';
	
	echo 'Setze die maximale Gebäudestufe für '.$selectauswahl.' auf Stufe <input type="text" name="newmaxgeblevel" size="6" maxlength="3" value=""> ';
	
  echo '</form>';
}
else echo 'Diese Funktion steht nur dem Fraktionsvorsitzendem zur Verf&uuml;gung.';

rahmen1_unten();

// Abschluss:

echo '<br />';

rahmen0_unten();

echo '<br />';

die('</body></html>');
?>
