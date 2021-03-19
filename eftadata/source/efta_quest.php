<?php
if(isset($_REQUEST["qdo"]))
{
  
  if($_REQUEST["qdo"]>0)
  {
    //schauen, ob man eine quest erfüllen/weiterführen kann
    $q_questfeld=0;
    $qtyp=intval($_REQUEST["qdo"]);
    $db_daten=mysql_query("SELECT * FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ='$qtyp' AND erledigt=0 AND map='$map' AND x='$x' AND y='$y'",$eftadb);
    $anz = mysql_num_rows($db_daten);
    if($anz>0)
    {
      $row = mysql_fetch_array($db_daten);
      $questid=$row["typ"];
      $flag1=$row["flag1"];
      $flag2=$row["flag2"];
      $flag3=$row["flag3"];
      $flag4=$row["flag4"];
      $flag5=$row["flag5"];
      $flag6=$row["flag6"];
      $flag7=$row["flag7"];
      $flag8=$row["flag8"];
      $flag9=$row["flag9"];
      $flag10=$row["flag10"];
      $filename="eftadata/quests/q$questid.php";
      $q_questfeld=1;
      //kein kampf wenn quest
      $dokampf=1;
      include_once($filename);
    }
    else $q_text='Du bist nicht an den richtigen Koordinaten';
  }
}
//listet die quests des cyborgs auf

if($q_dontshowquestpage!=1)
{
echo '<br><br>';
rahmen0_oben();

if ($q_text!='')
{
  rahmen2_oben();
  echo '<div align="center">'.$q_text.'</div>';
  rahmen2_unten();
  echo '<br>';
}


rahmen1_oben('<div align="center"><b>Quest&uuml;bersicht (X: '.$x.' Y: '.$y.')</b></div>');
    
echo '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
echo '<tr align="center">';
echo '<td width="40%" class="cell"><b>Quest</b></td>';
echo '<td width="15%" class="cell"><b>Koordinaten</b></td>';
echo '<td width="15%" class="cell"><b>Zeit</b></td>';
echo '<td width="15%" class="cell"><b>Status</b></td>';
echo '<td width="15%" class="cell"><b>Aktion</b></td>';
echo '</tr>';

//quests aus der datenbank holen
$db_daten=mysql_query("SELECT * FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ!=1 ORDER BY typ",$eftadb);
$anzquests = mysql_num_rows($db_daten);
if ($anzquests==0) {echo '<tr><td colspan="5" class="cell1" align="center"><b>Keine Quests vorhanden.</b></td></tr>';}
else
while($row = mysql_fetch_array($db_daten))
{
  	$q_questfeld=0;
  	$typ=$row["typ"];
  	$ziel=$row["ziel"];
  	$zeit=$row["zeit"];
	$erledigt=$row["erledigt"];
  	$flag1=$row["flag1"];

  	if ($erledigt==1)$status='erledigt'; else $status='offen';
  	$filename="eftadata/quests/q$typ.php";
  	include($filename);
  	$quest=$q_questname;
	//$koords='unbekannt';
  	if($erledigt==0)
  	{
  		//koordinatenfarbe
  		if($row['x']==$x AND $row['y']==$y){$font[0]='<font color="#00EE00"><b>';$font[1]='</b></font>';}else{$font[0]='';$font[1]='';}
  		$koords=$font[0].$row["x"].':'.$row["y"].$font[1];
  	}else $koords='-';
  	if($q_zeit='-1')$zeit='ewig';

  
  	echo '<tr align="center">';
  	echo '<td class="cell1"><a href="#" onClick="lnk(\'q='.$typ.'\')">'.$quest.'</a></td>';
  	echo '<td class="cell1">'.$koords.'</td>';
  	echo '<td class="cell1">'.$zeit.'</td>';
  	echo '<td class="cell1">'.$status.'</td>';
  	if($erledigt==0) $linktext='<a href="#" onClick="lnk(\'qdo='.$typ.'\')"><div class="b1">erledigen&nbsp;</div></a>';
  	else $linktext='&nbsp';
  	echo '<td class="cell1">'.$linktext.'</td>';
  	echo '</tr>';
	//wenn q = typ, dann die questbeschreibung anzeigen
  	if($q==$typ AND $typ!=1)
  	echo '<tr><td class="cell" colspan="5">'.$q_questinfo.'</td></tr>';
	}

	//die ewigen efta transmitterquest
	if($sv_efta_in_de==1)
	{
  		$showmenu=1;
  		$filename="eftadata/quests/q1.php";
  		include($filename);
	}

	echo '</table></div>';

	rahmen1_unten();
	rahmen0_unten();
}

//infoleiste anzeigen
show_infobar();


echo '</body></html>';
exit;
?>