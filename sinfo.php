<?php
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_sinfo.lang.php";
include 'functions.php';

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, col, sector, `system`, newtrans, newnews, allytag, status, hide_secpics, platz, rang, secsort, secstatdisable FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
$restyp05=$row["restyp05"];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];

?>
<!doctype html>
<html>
<head>
<title><?php echo $sinfo_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

//stelle die ressourcenleiste dar
include "resline.php";

?>
<table width="580" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="500" align="center" class="ro">Informationen zum Server</td>
</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td class="rl">&nbsp;</td>
<td><div class="cell"><?php echo $deSystem['server_information']; ?></div></td>
<td class="rr">&nbsp;</td>
</tr>

<tr>
<td class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur">&nbsp;</td>
</tr>
</table>
<br>
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
?>

</body>
</html>