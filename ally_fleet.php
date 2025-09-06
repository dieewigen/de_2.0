<?php
//        --------------------------------- ally_members.php ---------------------------------
//        Funktion der Seite:                Anzeigen der Allianzmitglieder
//        Letzte &Auml;nderung:                05.09.2002
//        Letzte &Auml;nderung von:        Ascendant
//
//        &Auml;nderungshistorie:
//
//        05.02.2002 (Ascendant)        - Erweiterung der Adminrechte bis auf Leader ernennen
//                                                          auf Co-Leader
//  --------------------------------------------------------------------------------

include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.fleet.lang.php');
include_once('functions.php');

checkMissionEnd();

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, 
            newtrans, newnews, allytag 
     FROM de_user_data WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_array($result);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];
$sector=$row['sector'];$system=$row['system'];
$allytag=$row['allytag'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyfleet_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

$full_access = false;
if (has_position("leaderid", $allytag, $_SESSION['ums_user_id']) || has_position("coleaderid1", $allytag, $_SESSION['ums_user_id']) || has_position("coleaderid2", $allytag, $_SESSION['ums_user_id']) || has_position("coleaderid3", $allytag, $_SESSION['ums_user_id']) || has_position("fleetcommander1", $allytag, $_SESSION['ums_user_id']) || has_position("fleetcommander2", $allytag, $_SESSION['ums_user_id'])){
	$full_access = true;
}

//$full_access = true;

echo '
<table width="600" border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="*" align="center" class="ro">'.$allyfleet_lang['allianzflottenstatus'].'</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr><td width="13" class="rl">&nbsp;</td><td>
<table border="0" width="100%" cellspacing="1" cellpadding="0">';

if ($full_access){
	echo '<tr>'.
	        '<td class="tc">Name</td>'.
			'<td class="tc" title="Rasse">R</td>'.
	        '<td class="tc">'.$allyfleet_lang['koords'].'</td>'.
	        '<td class="tc">'.$allyfleet_lang['heimatflotte'].'</td>'.
	        '<td class="tc">'.$allyfleet_lang['flotte'].' I</td>'.
	        '<td class="tc">'.$allyfleet_lang['flotte'].' II</td>';
	echo    '<td class="tc">'.$allyfleet_lang['flotte'].' III</td>';
	echo    '<td class="tc">'.$allyfleet_lang['gesamt'].'</td>';
	echo '</tr>';
}

$result = mysqli_execute_query($GLOBALS['dbi'], 
    "SELECT user_id, spielername, sector, `system`, rasse  
     FROM de_user_data 
     WHERE status='1' AND allytag=? 
     ORDER BY sector, `system`",
    [$allytag]);
$numrows = mysqli_num_rows($result);

 $fleet_attack = 0;
 $fleet_deff = 0;
 $fleet_return = 0;
 $fleet_mission = 0;
 $fleet_home = 0;
 $f_all = 0;

for ($i=0; $i<$numrows;$i++){
	$values = mysqli_fetch_array($result);
    $userid = $values['user_id'];
    $spielername = $values['spielername'];
    $sector = $values['sector'];
    $system = $values['system'];


    $rasse='';
    if ($values['rasse'] == 1) {
        $rasse='<img src="'.'gp/'.'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
    } elseif ($values['rasse'] == 2) {
        $rasse='<img src="'.'gp/'.'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
    } elseif ($values['rasse'] == 3) {
        $rasse='<img src="'.'gp/'.'g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
    } elseif ($values['rasse'] == 4) {
        $rasse='<img src="'.'gp/'.'g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';
    }	

    $fleet_gesamt = 0;

    if ($full_access)
    {
	    print('<tr>');
	    print('<td class="cc">'.$spielername.'</td>');
		print('<td class="cc">'.$rasse.'</td>');
	    print('<td class="cr">'.$sector.':'.$system.'</td>');
	}
	
	for ($fnum = 0; $fnum<=3; $fnum++){
    	$user_fleet_id = $userid.'-'.$fnum;
    	$f_result = mysqli_execute_query($GLOBALS['dbi'], 
    	    "SELECT aktion, zeit, (e81+e82+e83+e84+e85+e86+e87+e88+e89+e90) as fsize 
    	     FROM de_user_fleet 
    	     WHERE user_id=?",
    	    [$user_fleet_id]);
    	$fleet = mysqli_fetch_array($f_result);
	    $fleet_aktion = $fleet['aktion'];
	    if ($fnum > 0)
	    {
    		$fleet_aktzeit = "(".$fleet['zeit'].")";
	    }
	    else
	    {
	    	$fleet_aktzeit="";
	    }
    	$fleet_fsize = $fleet['fsize'];
   		$fleet_gesamt = $fleet_gesamt + $fleet_fsize;

   		if ($fleet_aktion == "1"){//Angriff
   			$fleet_attack = $fleet_attack + $fleet_fsize;
   			if ($full_access)
    		{
   				print("<td class=\"cr\"><font color=\"red\">".number_format($fleet_fsize, 0,'','.')." $fleet_aktzeit</font></td>\n");
    		}
   		}
   		elseif ($fleet_aktion == "2"){//Verteidigung
   			$fleet_deff = $fleet_deff + $fleet_fsize;
   			if ($full_access)
    		{
   				print("<td class=\"cr\"><font color=\"green\">".number_format($fleet_fsize, 0,'','.')." $fleet_aktzeit</font></td>\n");
    		}
   		}
   		elseif ($fleet_aktion == "3"){//Rückflug
   			$fleet_return = $fleet_return + $fleet_fsize;
   			if ($full_access)
    		{
   				print("<td class=\"cr\"><font color=\"blue\">".number_format($fleet_fsize, 0,'','.')." $fleet_aktzeit</font></td>\n");
    		}
		}
		elseif ($fleet_aktion == "4"){//Mission
			$fleet_mission = $fleet_mission + $fleet_fsize;
			if ($full_access){
				print("<td class=\"cr\"><font color=\"#E238EC\">".number_format($fleet_fsize, 0,'','.')." $fleet_aktzeit</font></td>\n");
			}
		}
	
   		else
   		{
   			$fleet_home = $fleet_home + $fleet_fsize;
   			//print("<td class=\"cr\"><font color=\"white\">$fleet_fsize $fleet_aktzeit</font></td>\n");
   			if ($full_access)
    		{
   				print("<td class=\"cr\">".number_format($fleet_fsize, 0,'','.')." $fleet_aktzeit</td>\n");
    		}
   		}


	}
	if ($full_access)
    {
		print("<td class=\"cr\">".number_format($fleet_gesamt, 0,'','.')."</td>\n");
    }
	$f_all = $f_all + $fleet_gesamt;
}
print("<tr><td colspan=8 class=\"cr\">&nbsp;</td></tr>");
print("<tr><td colspan=5 class=\"cr\"></td><td class=\"cr\" colspan=3><font color=red>$allyfleet_lang[aflotten]: ".number_format($fleet_attack, 0,'','.')."</font></td></tr>\n");
print("<tr><td colspan=5 class=\"cr\"></td><td class=\"cr\" colspan=3><font color=green>$allyfleet_lang[vflotten]: ".number_format($fleet_deff, 0,'','.')."</font></td></tr>\n");
print("<tr><td colspan=5 class=\"cr\"></td><td class=\"cr\" colspan=3><font color=blue>$allyfleet_lang[zflotten]: ".number_format($fleet_return, 0,'','.')."</font></td></tr>\n");
print("<tr><td colspan=5 class=\"cr\"></td><td class=\"cr\" colspan=3><span style=\"color:#E238EC\">Auf Mission: ".number_format($fleet_mission, 0,'','.')."</span></td></tr>\n");
print("<tr><td colspan=5 class=\"cr\"></td><td class=\"cr\" colspan=3>$allyfleet_lang[sflotten]: ".number_format($fleet_home, 0,'','.')."</td></tr>\n");
print("<tr><td colspan=5 class=\"cl\"><strong>$allyfleet_lang[legende]: 
</strong>Stationiert <font color=\"red\">$allyfleet_lang[angriff]</font> <font color=\"green\">$allyfleet_lang[verteidigung]</font> <font color=\"blue\">$allyfleet_lang[rueckflug]</font> <span style=\"color:#E238EC\">Mission</span></td><td class=\"cr\" colspan=3><strong>$allyfleet_lang[fgesamt]: ".number_format($f_all, 0,'','.')."</strong></td></tr>\n");



echo "</table>".
        '</td><td width="13" class="rr">&nbsp;</td></tr>'.
        '<tr><td width="13" class="rul">&nbsp;</td>'.
        '<td width="*" class="ru">&nbsp;</td>'.
        '<td width="13" class="rur">&nbsp;</td>'.
        '</tr>'.
        '</table>';


?>
<br>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>