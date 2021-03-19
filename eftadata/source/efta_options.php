<?php
//pfad vom grafikpack auslesen
$db_daten=mysql_query("SELECT ircname, gpfad, transparency FROM de_user_info WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$gpfaddb=$row["gpfad"];

//einstellungen speichern
if ($_REQUEST["saveoptions"])
{
  include_once('outputlib.php');
  $gpfad = $_REQUEST["gpfad"];
  if ($_REQUEST["gpfad"]!=''){$ums_gpfad=$gpfad;$gpfaddb=$gpfad;}
  else {$ums_gpfad=$sv_image_server_list[0];$gpfaddb='';}
  mysql_query("UPDATE de_user_info SET gpfad = '$gpfad' WHERE user_id = '$ums_user_id'",$db);
}

if ($_REQUEST['delacc']) //account l�schen
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

    //cyborg entfernen
    mysql_query("DELETE FROM de_cyborg_data WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_flags WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_ht WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_item WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_quest WHERE user_id='$efta_user_id'",$eftadb);

    session_destroy();
    echo '<script>lnk("");</script>';
    exit;
  }
}

//tastatursteuerung deaktivieren
echo '<script language="javascript">disablekeys=1;</script>';  	  


//////////////////////////////////////////////////
//////////////////////////////////////////////////
// einstellungen
//////////////////////////////////////////////////
//////////////////////////////////////////////////

        ?>
        <script>
    	function save_options()
    	{
    		var v1=$("#gpfad").val();
    		
    		lnk('action=optionspage&saveoptions=1&gpfad='+v1);
        }
    	</script>
    	<?php


echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Einstellungen</b></div>');
echo '

<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="280">Grafikpackpfad</td>
<td width="280"><input type="Text" name="gpfad" id="gpfad" value="'.$gpfad.'" maxlength="255"></td>
</tr>
<tr align="center">
<td colspan="2"><br><a class="gwaren" href="#" onClick="save_options()">&nbsp;Einstellungen speichern&nbsp;</a></td>
</tr>
</table>

';
rahmen1_unten();

echo '<br>';

//////////////////////////////////////////////////
//////////////////////////////////////////////////
// account l�schen
//////////////////////////////////////////////////
//////////////////////////////////////////////////

        ?>
        <script>
    	function delete_account()
    	{
    		var v1=$("#delpass").val();
    		var v2=$("#delcheck1:checked").val();
    		var v3=$("#delcheck2:checked").val();
    		
    		lnk('action=optionspage&delacc=1&delpass='+v1+'&delcheck1='+v2+'&delcheck2='+v3);
        }
    	</script>
    	<?php


rahmen1_oben('<div align="center"><b>Account l&ouml;schen</b></div>');
echo '
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="760" colspan="2">Um den Account zu l&ouml;schen das Passwort eingeben, beide Best&auml;tigungen anklicken und dann mit "Account l&ouml;schen" best&auml;tigen.</td>
</tr>
<tr align="center">
<td width="280">Passwort</td>
<td width="280"><input type="password" name="delpass" id="delpass" value=""></td>
</tr>
<tr align="center">
<td><input name="delcheck1" id="delcheck1" type="checkbox" value="1"> Best&auml;tigung 1</td>
<td><input name="delcheck2" id="delcheck2" type="checkbox" value="1"> Best&auml;tigung 2</td>
</tr>
<tr align="center">
<td colspan="2"><br><a class="gwaren" href="#" onClick="delete_account()">&nbsp;Account l&ouml;schen&nbsp;</a></td>
</tr>
</table>';

rahmen1_unten();
rahmen0_unten();

//infoleiste anzeigen
show_infobar();

die('</body></html>');
?>