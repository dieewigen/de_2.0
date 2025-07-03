<?php
include "inc/header.inc.php";
include "cache/overview.inc.php"; //nachrichten für det, kp und galname
include "inc/lang/".$sv_server_lang."_sinfo.lang.php";
include 'functions.php';

/*
//temporärer Wechselbutton zwischen DESKTOP alt und neu
echo '
<br><br>Hier kann zwischen dem alten und dem neuen Desktop-Design gewechselt werden: 
<br>
<div style="display: flex;">
	<div><a href="de_frameset.php" class="btn">Classic</a></div>
	<div><a href="dm.php" class="btn">Standard</a></div>
</div>
';
*/

?>
<!doctype html>
<html>
<head>
<title><?=$sinfo_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<center><br>
<table width="580" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="500" align="center" class="ro"><?php echo $sinfo_lang['detmeldungen']?>
</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td class="rl">&nbsp;</td>
<td><div class="cell"><?php echo utf8_encode_fix($detmsg); ?></div></td>
<td class="rr">&nbsp;</td>
</tr>
<tr align="center">
<td height="37" class="rml">&nbsp;</td>
<td align="center" class="ro"><?php echo $sinfo_lang['informationenausdemkristallpalast']?></td>
<td class="rmr">&nbsp;</td>
</tr>
<tr>
<td class="rl">&nbsp;</td>
<td align="center"><div class="cell"><?php echo utf8_encode_fix($kpmsg); ?></div></td>
<td class="rr">&nbsp;</td>
</tr>
<tr>
<td class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur">&nbsp;</td>
</tr>
</table>

<?php 
rahmen_oben('Tickzeiten');
echo '<div class="cell" style="width: 554px;">';
echo 'Wirtschaftsticks (WT): ';
echo '<table style="width: 100%; text-align: center;"><tr><td>Stunde</td><td>Minute</td></tr>';
for($h=0;$h<=23;$h++){
	$first_minute=true;
	echo '<tr>';
	echo '<td style="width: 100px;">'.$h.'</td><td>';
	for($m=0;$m<=59;$m++){
		if(in_array(intval($m), $GLOBALS['wts'][$h])){
			if(!$first_minute){
				echo ',';
			}
			echo $m;
			$first_minute=false;
		}
	}
	echo '</td></tr>';
}
echo '</table>';

echo 'Kampfticks (KT): ';
echo '<table style="width: 100%; text-align: center;"><tr><td>Stunde</td><td>Minute</td></tr>';
for($h=0;$h<=23;$h++){
	if(count($GLOBALS['kts'][$h])>0){
		$first_minute=true;
		echo '<tr>';
		echo '<td style="width: 100px;">'.$h.'</td><td>';
		for($m=0;$m<=59;$m++){
			if(in_array(intval($m), $GLOBALS['kts'][$h])){
				if(!$first_minute){
					echo ',';
				}
				echo $m;
				$first_minute=false;
			}
		}
		echo '</td></tr>';
	}
}
echo '</table>';	
echo '</div>';

rahmen_unten();


include "fooban.php"; ?>
</body>
</html>

