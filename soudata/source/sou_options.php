<?php
//pfad vom grafikpack auslesen
$db_daten=mysql_query("SELECT ircname, gpfad, transparency FROM de_user_info WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$gpfaddb=$row["gpfad"];

//einstellungen speichern
if ($_REQUEST['togglesound']==1)
{
  $newsound=intval($_REQUEST['newsound']);
  $player_sound=$newsound;
  
  mysql_query("UPDATE sou_user_data SET soundenable= '$newsound' WHERE user_id = '$player_user_id'",$soudb);
  ?>
  <script type="text/javascript">
  jQuery.data(document.body, 'player_soundenable', <?php echo $player_sound; ?>);
  </script>
  <?php
}

if ($_REQUEST['toggleanimation']==1)
{
  $newanimation=intval($_REQUEST['newanimation']);
  $player_animation=$newanimation;
  
  mysql_query("UPDATE sou_user_data SET animationenable= '$newanimation' WHERE user_id = '$player_user_id'",$soudb);
  
  ?>
  <script type="text/javascript">
  jQuery.data(document.body, 'player_animationenable', <?php echo $player_animation; ?>);
  </script>
  <?php
}

if ($delacc AND $sv_sou_in_de==0) //account l�schen
{
  $db_daten=mysql_query("SELECT user_id FROM de_login WHERE user_id = '$ums_user_id' AND pass=MD5('$delpass')", $db);
  $num = mysql_num_rows($db_daten);
  if ($num==1) //oldpass ist korrekt
  if ($delcheck1=="1" and $delcheck2=="1")//l�sche
  {
    $uid=$ums_user_id;
    
	//grunddaten entfernen
    mysql_query("DELETE FROM de_login WHERE user_id=$uid",$db);
    mysql_query("DELETE FROM de_user_data WHERE user_id=$uid",$db);
    mysql_query("DELETE FROM de_user_info WHERE user_id=$uid",$db);

    //sou-daten entfernen
    mysql_query("DELETE FROM sou_ship_module WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_buffs WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_data WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_enm WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_hyper WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_politics WHERE user_id='$player_user_id' OR wahlstimme='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_skill WHERE user_id='$player_user_id' OR wahlstimme='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_systemhold WHERE user_id='$player_user_id'",$soudb);
    mysql_query("DELETE FROM sou_user_tech_updates WHERE user_id='$player_user_id'",$soudb);
  
    
    session_destroy();
    header("Location: index.php");
  }
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


$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Einstellungen</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
  rahmen1_oben($routput);

  if($player_sound==1)
  {
    $soundstatus='an';
    $newsound=0;
  }
  else
  { 
    $soundstatus='aus';
    $newsound=1;
  }

  if($player_animation==1)
  {
    $animationstatus='an';
    $newanimation=0;
  }
  else
  { 
    $animationstatus='aus';
    $newanimation=1;
  }
  
  echo '<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="280">Sound</td>
<td width="280"><a href="sou_main.php?action=optionspage&togglesound=1&newsound='.$newsound.'">'.$soundstatus.'</td>
</tr>
<tr align="center">
<td width="280">Animationen</td>
<td width="280"><a href="sou_main.php?action=optionspage&toggleanimation=1&newanimation='.$newanimation.'">'.$animationstatus.'</td>
</tr>
</table>';
  
  
  /*
echo '
<form action="sou_main.php?action=optionspage" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="280">Grafikpackpfad</td>
<td width="280"><input type="Text" name="gpfad" value="'.$gpfaddb.'" maxlength="255"></td>
</tr>
<tr align="center">
<td colspan="2"><br><input type="Submit" name="saveoptions" value="Einstellungen &uuml;bernehmen"></td>
</tr>
</table>
</form>
';
*/
rahmen1_unten();

echo '<br>';

//////////////////////////////////////////////////
//////////////////////////////////////////////////
// account l�schen
//////////////////////////////////////////////////
//////////////////////////////////////////////////
if($sv_sou_in_de==0)
{
$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
  <td><b>Account l&ouml;schen</b></td>
  <td width="120">&nbsp;</td>
  </tr></table>';
rahmen1_oben($routput);

echo '
<form action="sou_main.php?action=optionspage" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="760" colspan="2">Um den Account zu l&ouml;schen das Passwort eingeben, beide Best&auml;tigungen anklicken und dann mit "Account l&ouml;schen" best&auml;tigen.</td>
</tr>
<tr align="center">
<td width="280">Passwort</td>
<td width="280"><input type="password" name="delpass" value=""></td>
</tr>
<tr align="center">
<td><input name="delcheck1" type="checkbox" value="1"> Best&auml;tigung 1</td>
<td><input name="delcheck2" type="checkbox" value="1"> Best&auml;tigung 2</td>
</tr>
<tr align="center">
<td colspan="2"><input type="Submit" name="delacc" value="Account l&ouml;schen"></td>
</tr>
</table>
</form>
';
rahmen1_unten();
echo '<br>';
}

rahmen0_unten();
die('</body></html>');
?>