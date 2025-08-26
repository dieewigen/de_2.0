<?php
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.register.lang.php');
include('functions.php');

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag 
     FROM de_user_data 
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];$allytag=$row['allytag'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyregister_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

//�berpr�fen ob man bereits in einer allianz ist, bzw. eine bewerbung offen ist
 
if($allytag==''){
	echo '<br>';
	rahmen_oben($allyregister_lang['newreg']);
?>
<form name="register" method="POST" action="ally_register2.php">
<input type="hidden" name="leaderid" value="<?php echo $_SESSION['ums_user_id']?>">
  <table border="0" class="cell" width="574" cellspacing="0" cellpadding="0">
  	<tr>
  		<td colspan="2"><br></td>
  	</tr>
  	<tr>
      <td width="150" valign="top"><?php echo $allyregister_lang['kuerzel']?>:</font></td>
      <td width="450" valign="top"><input name="clankuerzel" size="10" maxlength="8"></font></td>
    </tr>
    <tr>
      <td width="150" valign="top"><?=$allyregister_lang['regform']?>:</font></td>
      <td width="450" valign="top">
      		<select name="regierungsform">
      			<option value="Demokratie"><?php echo $allyregister_lang['demokratie']?></option>
      			<option value="Diktatur"><?php echo $allyregister_lang['diktatur']?></option>
      			<option value="Monarchie"><?php echo $allyregister_lang['monarchie']?></option>
      			<option value="Ratsregierung"><?php echo $allyregister_lang['ratsregierung']?></option>
      			<option value="Kollektiv"><?php echo $allyregister_lang['kollektiv']?></option>
      			<option value="Militärregime"><?php echo $allyregister_lang['militaerregime']?></option>
      			<option value="Anarchie"><?php echo $allyregister_lang['anarchie']?></option>
      			<option value="Andere"><?php echo $allyregister_lang['andere']?></option>
      		</select>
      </td>
    </tr>
    <tr>
      <td width="150" valign="top"><?php echo $allyregister_lang['polausrichtung']?>:</font></td>
      <td width="450" valign="top">
      		<select name="ausrichtung">
      			<option value="Neutral"><?php echo $allyregister_lang['neutral']?></option>
      			<option value="Aggressiv"><?php echo $allyregister_lang['aggressiv']?></option>
      			<option value="Defensiv"><?php echo $allyregister_lang['defensiv']?></option>
      			<option value="Ritter"><?php echo $allyregister_lang['ritter']?></option>
      		</select>
      </td>
    </tr>
    <tr>
      <td width="150" valign="top"><?php echo $allyregister_lang['allianzname']?>:</font></td>
      <td width="450" valign="top"><input type="text" name="clanname" size="50" maxlength=50></font></td>
    </tr>
    <tr>
      <td width="150" valign="top"><?php echo $allyregister_lang['url']?>:</font></td>
      <td width="450" valign="top"><input name="hp" size="50" maxlength="50" value="http://"></font></td>
    </tr>
    <tr>
      <td width="150" valign="top"><?php echo $allyregister_lang['allianzinformation']?></font></td>
      <td width="450" valign="top"><textarea rows="7" name="bio" cols="50"></textarea></td>
    </tr>
  	 <tr>
  	 	<td>&nbsp;</td>
  		<td valign="top"><input type="submit" value="<?php echo $allyregister_lang['abschicken']?>" name="B1"><input type="reset" value="<?php echo $allyregister_lang['zurueck']?>" name="B2">
  		</td>
  	</tr>
  	<tr>
  		<td colspan=2 valign="top"><br>
  			<font size=1><?php echo $allyregister_lang['msg_1']?></font>
  		</td>
  	</tr>
  </table>
 </form>
<?php
	rahmen_unten();
	}
	else //man ist bereits in einer allianz bzw. hat sich beworben 
	{
		echo '<div class="info_box text2">Du kannst keine Allianz gr&uuml;nden, da Du Dich bereits in einer Allianz befindest bzw. beworben hast.</div>';
		
	}
	include('ally/ally.footer.inc.php');
?>