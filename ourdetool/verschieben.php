<?php
include "../inccon.php";
include "../inc/sv.inc.php";
?>
<html>
<head>
<title>Move</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
include "det_userdata.inc.php";

if ($button)
{
  $hsector=(int)$hsector;
  $hsystem=(int)$hsystem;
  $zsector=(int)$zsector;
  $zsystem=(int)$zsystem;

  $fehlermsg='';
  //schauen ob die werte ok sind
  if ($zsector<1 OR $zsector>$sv_show_maxsector)$fehlermsg.='<font color="#FF0000">Die Sektorgröße ist ungültig.</font><br>';
  if ($zsystem<1 OR $zsystem>99)$fehlermsg.='<font color="#FF0000">Die Systemgröße ist ungültig.</font><br>';

  //schauen ob es das herkunftssystem gibt
  $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$hsector' AND system='$hsystem'",$db);
  $anz1 = mysql_num_rows($db_daten);

  //schauen ob bei den zielkoordinaten platz ist
  $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$zsector' AND system='$zsystem'",$db);
  $anz2 = mysql_num_rows($db_daten);


  if ($anz1!=1)$fehlermsg.='<font color="#FF0000">Kein System zum verschieben gefunden.</font><br>';
  if ($anz2!=0)$fehlermsg.='<font color="#FF0000">An den Zielkoordinaten befindet sich bereits ein System.</font><br>';

  if ($fehlermsg=='')//system verschieben
  {
    $db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE sector='$hsector' AND system='$hsystem'",$db);
    $row = mysql_fetch_array($db_daten);

    $secz=$zsector;
    $sysz=$zsystem;

    $uid=$row["user_id"];
    mysql_query("UPDATE de_user_data SET sector=$secz, system=$sysz, votefor=0 WHERE user_id='$uid'",$db);//heimatsystem festlegen
    //flottenkoordinaten updaten

    $fleet_id=$uid.'-0';
    mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
    $fleet_id=$uid.'-1';
    mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
    $fleet_id=$uid.'-2';
    mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);
    $fleet_id=$uid.'-3';
    mysql_query("UPDATE de_user_fleet SET hsec=$secz, hsys=$sysz, zielsec=$secz, zielsys=$sysz WHERE user_id='$fleet_id'",$db);

    //flottendaten von angreifenden/deffenden flotten auf das neue ziel umlegen
    //mysql_query("UPDATE de_user_fleet SET zielsec=$secz, zielsys=$sysz WHERE zielsec='$herksec' AND zielsys='$herksys' AND hsec<>$secz AND hsys<>$sysz",$db);
    $fehlermsg.='<font color="#FF0000">Der Spieler wurde verschoben.</font><br>';
  }
  echo '<center><b>'.$fehlermsg.'</b></center>';
}

?>
<br>
<div align="center">
<form action="verschieben.php" method="post">
<b>Spieler verschieben</b><br><br>
<table >
<tr>
 <td>Aktuelle Koordinaten</td>
 <td><input type="Text" name="hsector" size="5" maxlength="5">:<input type="Text" name="hsystem" size="5" maxlength="5"></td>
</tr>
<tr>
 <td>Zielkoordinaten</td>
 <td><input type="Text" name="zsector" size="5" maxlength="5">:<input type="Text" name="zsystem" size="5" maxlength="5"></td>
</tr>
<tr>
 <td colspan="2" align="center"><input type="Submit" name="button" value="Spieler verschieben"></td>
</tr>
</table>
</form>
</div>
</body>
</html>
