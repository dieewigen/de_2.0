<?php
include "soudata/defs/resources.inc.php";

echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<br>';

//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1)
{
  //die id des sonnensystems auslesen
  $row = mysql_fetch_array($db_daten);
  $owner_id=$row["id"];
  $owner_sysname=$row["sysname"];
  $owner_fraction=$row["fraction"];
  
  //überprüfen ob das system zur eigenen fraktion gehört
  if($player_fraction!=$owner_fraction)
  {
  	rahmen2_oben();
  	echo 'Auf dieses Sonnensystem hat deine Fraktion keinen Anspruch. Versuche das Fraktionsansehen f&uuml;r deine Fraktion zu erh&ouml;hen.';
  	rahmen2_unten();
  	echo '<br>';

	echo '</div>';//center-div
	die('</body></html>');
  }
  
	
//der spieler möchte minen
if($_POST["miningtime"] AND $_POST["restyp"])
{
  $pagereload=0;
  //transaktionsbeginn
  if (setLock($_SESSION["sou_user_id"]))
  {

    $miningtime=intval($_POST["miningtime"]);
    $restyp=intval($_POST["restyp"]);
  
    //überprüfen ob die richtige zeit ausgewählt worden ist
    if($miningtime>0 AND $miningtime<=60)
    {
      //überprüfen ob es einen rohstoff mit der id gibt
      if($restyp<=count($r_def))
      {
      	//überprüfen ob der rohstoff hier verfügbar ist
      	if(res_is_available($restyp-1)==1)
      	{
      	  //überprüfe ob man ein bergbaumodul hat
  	      $canmine=get_canmine($_SESSION["sou_user_id"]);
  	      if($canmine>0)
  	      {
            //überprüfen ob man noch freien raum im lager hat
            $freehold=get_sum_hold($_SESSION["sou_user_id"]);
            if($freehold>0)
            {
          	  //berechnen wieviel rohstoffe man bekommen kann
          	  $getres=round($miningtime*($canmine/$r_def[$restyp-1][1])*(1+(get_skill($restyp-1)/500000)));
          	  //wenn das lager nicht ausreicht beschränken
          	  if($freehold<$getres)$getres=$freehold;
          	
          	  //die rohstoffe in der db hinterlegen
          	  change_hold_amount($_SESSION["sou_user_id"], $restyp-1, $getres);
          	
          	  //skill verbessern
          	  change_skill($restyp-1, $miningtime);
          	  
          	  //einen counter setzen, damit sonst nichts mehr zu machen geht
          	  $time=time()+60*$miningtime;
          	  mysql_query("UPDATE sou_user_data SET atimer1typ=1, atimer1time='$time' WHERE user_id='$_SESSION[sou_user_id]'",$soudb);
          	
          	  //script beenden und zurück zur counteransicht
          	  $pagereload=1;
          	
          	  //miningzeit speichern
          	  $_SESSION["sou_mining_time"]=$miningtime;
          	  $_SESSION["sou_mining_restyp"]=$restyp-1;
          	
            }
    	  	else $msg='<font color="#FF0000">Das Schiff verf&uuml;gt über keinen freien Platz im Frachtraum.</font>';
  	      }
  	      else $msg='<font color="#FF0000">Das Schiff verf&uuml;gt über kein funktionst&uuml;chtiges Bergbaumodul.</font>';
        }
        else $msg='<font color="#FF0000">Bitte &uuml;berpr&uuml;fe den ausgew&auml;hlen Rohstoff.</font>';
  	    
      }
      else $msg='<font color="#FF0000">Bitte &uuml;berpr&uuml;fe den ausgew&auml;hlen Rohstoff.</font>';
    }
    else $msg='<font color="#FF0000">Bitte &uuml;berpr&uuml;fe den ausgew&auml;hlen Zeitraum.</font>';

    //lock wieder entfernen
    $erg = releaseLock($_SESSION["sou_user_id"]); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("Datensatz Nr. ".$_SESSION["sou_user_id"]." konnte nicht entsperrt werden!<br><br><br>");
    }
  }//lock ende
  else $msg='<font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font>';
  if($pagereload==1)
  {
    header("Location: sou_main.php");
    exit;  	
  }
}

//daten zur ansicht
echo '<form action="sou_main.php" method="POST" name="f">';
echo '<input type="hidden" name="action" value="miningpage">';

if($msg!='')
{
  rahmen2_oben();
  echo $msg;
  rahmen2_unten();
  echo '<br>';
}

$output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Asteroidenfeld</b></td>
<td width="120">&nbsp;</td>
</tr></table>';
rahmen1_oben($output);

echo '<div style="background-image: url('.$gpfad.'bgpic3.jpg);"><div class="cell1"><div class="cell1">';

echo '<br>In diesem Asteroidenfeld kannst du mit einem Bergbaumodul auf die Suche nach wertvollen Rohstoffen gehen.<br><br>';
$_SESSION["sou_mining_time"]=intval($_SESSION["sou_mining_time"]);
if ($_SESSION["sou_mining_time"]<1)$_SESSION["sou_mining_time"]=1;
echo 'Wie viele Minuten m&ouml;chtest du auf die Suche gehen? <input type="text" name="miningtime" size="3" maxlength="2" value="'.$_SESSION["sou_mining_time"].'"> (1-60)<br><br>';
echo 'Nach welchem Rohstoff m&ouml;chtest du suchen? ';
echo '<select name="restyp">';
for($i=0;$i<count($r_def);$i++)
{
  //überprüfen ob der rohstoff möglich ist
 
  if(res_is_available($i)==1)
  {
    //festlegen des bereits gewählten rohstoffs
    $_SESSION["sou_mining_restyp"]=intval($_SESSION["sou_mining_restyp"]);
    if($_SESSION["sou_mining_restyp"]<0 OR $_SESSION["sou_mining_restyp"]>count($r_def)-1)$_SESSION["sou_mining_restyp"]=0;
    if($_SESSION["sou_mining_restyp"]==$i)$selected=' selected'; else $selected='';
  
    $prodmin=round(get_canmine($_SESSION["sou_user_id"])/$r_def[$i][1]*(1+(get_skill($i)/500000)));
    echo '<option value="'.($i+1).'" '.$selected.'>'.$r_def[$i][0].' - '.$prodmin.' m&sup3;/min</option>';
  }
}
echo '</select><br><br>';
echo 'Laderaum: '.number_format(get_sum_hold($_SESSION["sou_user_id"]), 0,",",".").' m&sup3;<br><br>';

echo '<a href="javascript:document.f.submit();"><div class="b1">START</div></a><br><br>';

echo '</div></div></div>';

rahmen1_unten();

}
else echo 'Hier gibt es kein Sonnensystem.<br>';

echo '<br>';
rahmen0_unten();
echo '<br>';

echo '</div>';//center-div
echo '</form>';
die('</body></html>');
?>