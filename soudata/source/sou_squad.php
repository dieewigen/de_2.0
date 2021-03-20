<?php
//pfad vom grafikpack auslesen
$db_daten=mysql_query("SELECT ircname, gpfad, transparency FROM de_user_info WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$gpfaddb=$row["gpfad"];

//squad gründen
if (isset($_REQUEST['createsquad']) AND $player_squad==0)
{
	//squadkey erzeugen
    $pwstring='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $newpass=$pwstring[rand(0, strlen($pwstring)-1)];
    for($i=1; $i<=11; $i++) $newpass.=$pwstring[rand(0, strlen($pwstring)-1)];

    //squad in der db anlegen
    mysql_query("INSERT INTO sou_squad SET fraction='$player_fraction', squadkey='$newpass'",$soudb);
    $squad_id=mysql_insert_id();
    $player_squad=$squad_id;
    
    //alle spielerschiffe ins squad eingliedern
    mysql_query("UPDATE sou_user_data SET squad= '$squad_id' WHERE owner_id = '$player_owner_id'",$soudb);
}

//squad beitreten
if (isset($_REQUEST['joinsquad']) AND $player_squad==0)
{
	$squadkey=trim($_REQUEST['squadkey']);
	
	//überprüfen ob es so ein squad gibt
	$db_daten=mysql_query("SELECT * FROM sou_squad WHERE squadkey='$squadkey'",$soudb);
	$num = mysql_num_rows($db_daten);
	if($num==1)	
	{
		$row = mysql_fetch_array($db_daten);
		$squad_id=$row['id'];
		$squad_fraction=$row['fraction'];
		
		//test ob das squad schon voll ist
		$db_daten=mysql_query("SELECT owner_id FROM sou_user_data WHERE squad='$squad_id' GROUP BY owner_id",$soudb);
		$num = mysql_num_rows($db_daten);
		if($num<5)	
		{
			//test ob die squad zur eigenen fraktion gehört
			if($squad_fraction==$player_fraction)
			{
				//alle spielerschiffe ins squad eingliedern
	    		mysql_query("UPDATE sou_user_data SET squad= '$squad_id' WHERE owner_id = '$player_owner_id'",$soudb);
				$player_squad=$squad_id;
				// O3Jj7dOkYDUo
			}
			else $errmsg='Diese Squad geh&ouml;rt zu einer anderen Fraktion.';
		}
		else $errmsg='Diese Squad hat bereits die maximale Anzahl an Mitgliedern.';
	}
	else $errmsg='Es gibt keine Squad mit diesem Zugangsschl&uuml;ssel.';
}

//squad verlassen
if (isset($_REQUEST['leavesquad']) AND $player_squad>0)
{
	//alle spielerschiffe ins squad eingliedern
	mysql_query("UPDATE sou_user_data SET squad=0 WHERE owner_id = '$player_owner_id'",$soudb);
	$player_squad=0;
}


//////////////////////////////////////////////////
//////////////////////////////////////////////////
// einstellungen
//////////////////////////////////////////////////
//////////////////////////////////////////////////

echo '<br>';
echo '<br>';
rahmen0_oben();
echo '<br>';

//wenn man in einer squad ist, den zugangsschlüssel anzeigen
if($player_squad>0)
{
	$db_daten=mysql_query("SELECT * FROM sou_squad WHERE id='$player_squad'",$soudb);
	$row = mysql_fetch_array($db_daten);
	$squadinfo=' (Zugangsschl&uuml;ssel: '.$row['squadkey'].')';
}
else $squadinfo='';


$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Squad'.$squadinfo.'</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
rahmen1_oben($routput);

//////////////////////////////////////////////////
//////////////////////////////////////////////////
//anzeige wenn man in keinem squad ist
//////////////////////////////////////////////////
//////////////////////////////////////////////////

  
if($player_squad==0)
{    
	echo 'Du hast die M&ouml;glichkeit Dich mit bis zu 4 weiteren Spielern in einer Squad zu organisieren. In dieser Squad befinden sich dann alle 
  Raumschiffe der beteiligten Spieler und jeder kann die Koordinaten einsehen. Die Mitgliedschaft ist keine Pflicht.';
	
	if($errmsg!='')echo '<br><font color="#FF0000">'.$errmsg.'</font>';
	
	//squad gründen
	echo '<form action="sou_main.php?action=squad" method="POST">';
	echo '<br><input type="Submit" name="createsquad" value="Squad gr&uuml;nden">';
	echo '</form><hr>';
	
	//squad beitreten
	echo '<br><form action="sou_main.php?action=squad" method="POST">';
	echo 'Zugangsschl&uuml;ssel <input type="input" name="squadkey" value=""> (diesen kannst Du von einem Mitglied einer bestehenden Squad erhalten)';
	echo '<br><br><input type="Submit" name="joinsquad" value="Squad beitreten">';
	echo '</form>';
}
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//anzeige wenn man in einem squad ist
//////////////////////////////////////////////////
//////////////////////////////////////////////////
else 
{
  	$c1=1;if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
	//mitgliederliste anzeigen
	$oldownerid=-1;
	echo '<table width="100%">';
	echo '<tr class="'.$bg.'"><td><b>Spielername</b></td><td><b>Koordinaten</b></td></tr>';
	$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE squad='$player_squad' ORDER BY owner_id",$soudb);
	while($row = mysql_fetch_array($db_daten))
	{
		//pro user anderer hintergrund
		if($row['owner_id']!=$oldownerid)
		{
			if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
			$oldownerid=$row['owner_id'];
		}		
		
		echo '<tr class="'.$bg.'">';
		echo '<td>'.$row['spielername'].'</td>';
		echo '<td><a href="sou_main.php?action=sectorpage&smx='.$row['x'].'&smy='.$row['y'].'">'.$row['x'].'/'.$row['y'].'</td>';
		echo '</tr>';
		
	}
	echo '</table>';

	//squad verlassen
	echo '<form action="sou_main.php?action=squad" method="POST">';
	echo '<br><input type="Submit" name="leavesquad" value="Squad verlassen">';
	echo '</form>';	
}


rahmen1_unten();

echo '<br>';

rahmen0_unten();
die('</body></html>');
?>