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

//überprüfen ob man bereits in einer allianz ist, bzw. eine bewerbung offen ist

if($allytag==''){
	rahmen_oben($allyregister_lang['newreg']);
?>
<form name="register" method="POST" action="ally_register2.php" class="ally mod">
	<input type="hidden" name="leaderid" value="<?php echo $_SESSION['ums_user_id']?>">
	<div class="ally-formular">
		<label class="ally-feld">
			<span class="mod-typ"><?php echo $allyregister_lang['kuerzel']?></span>
			<input name="clankuerzel" maxlength="7" class="mod-eingabe">
			<span class="ally-feld-hinweis">h&ouml;chstens 7 Zeichen, nur Buchstaben und Ziffern</span>
		</label>
		<label class="ally-feld">
			<span class="mod-typ"><?php echo $allyregister_lang['allianzname']?></span>
			<input type="text" name="clanname" maxlength="50" class="mod-eingabe">
		</label>
		<label class="ally-feld">
			<span class="mod-typ"><?php echo $allyregister_lang['regform']?></span>
			<select name="regierungsform" class="mod-eingabe">
				<option value="Demokratie"><?php echo $allyregister_lang['demokratie']?></option>
				<option value="Diktatur"><?php echo $allyregister_lang['diktatur']?></option>
				<option value="Monarchie"><?php echo $allyregister_lang['monarchie']?></option>
				<option value="Ratsregierung"><?php echo $allyregister_lang['ratsregierung']?></option>
				<option value="Kollektiv"><?php echo $allyregister_lang['kollektiv']?></option>
				<option value="Militärregime"><?php echo $allyregister_lang['militaerregime']?></option>
				<option value="Anarchie"><?php echo $allyregister_lang['anarchie']?></option>
				<option value="Andere"><?php echo $allyregister_lang['andere']?></option>
			</select>
		</label>
		<label class="ally-feld">
			<span class="mod-typ"><?php echo $allyregister_lang['polausrichtung']?></span>
			<select name="ausrichtung" class="mod-eingabe">
				<option value="Neutral"><?php echo $allyregister_lang['neutral']?></option>
				<option value="Aggressiv"><?php echo $allyregister_lang['aggressiv']?></option>
				<option value="Defensiv"><?php echo $allyregister_lang['defensiv']?></option>
				<option value="Ritter"><?php echo $allyregister_lang['ritter']?></option>
			</select>
		</label>
		<label class="ally-feld ally-feld-breit">
			<span class="mod-typ"><?php echo $allyregister_lang['url']?></span>
			<input name="hp" maxlength="50" value="http://" class="mod-eingabe">
		</label>
		<label class="ally-feld ally-feld-breit">
			<span class="mod-typ"><?php echo $allyregister_lang['allianzinformation']?></span>
			<textarea rows="7" name="bio" class="mod-eingabe"></textarea>
		</label>
	</div>
	<div class="mod-hinweis ally-abstand"><?php echo $allyregister_lang['msg_1']?></div>
	<div class="ally-aktionen">
		<input type="reset" value="<?php echo $allyregister_lang['zurueck']?>" name="B2" class="mod-btn mod-btn-leise">
		<input type="submit" value="<?php echo $allyregister_lang['abschicken']?>" name="B1" class="mod-btn">
	</div>
</form>
<?php
	rahmen_unten();
	}
	else //man ist bereits in einer allianz bzw. hat sich beworben
	{
		echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">Du kannst keine Allianz gr&uuml;nden, da Du Dich bereits in einer Allianz befindest bzw. beworben hast.</div></div>';

	}
	include('ally/ally.footer.inc.php');
?>
