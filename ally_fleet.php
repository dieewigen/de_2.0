<?php
//        --------------------------------- ally_fleet.php ---------------------------------
//        Funktion der Seite:                Flottenstatus der Allianzmitglieder
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

//Flottengröße mit Farbe je Auftrag, darunter die Zeit des Auftrags
function allyflotte_zelle($klasse, $fsize, $aktzeit)
{
	return '<span class="ally-zahl ally-fl'.$klasse.'">'.number_format($fsize, 0,'','.').(($aktzeit != '' && $aktzeit != '(0)') ? '<small>'.$aktzeit.'</small>' : '').'</span>';
}

$zeilen = '';
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
    }elseif ($values['rasse'] == 5) {
        $rasse='<img src="'.'gp/'.'g/r/raceD.png" title="DX61a23" width="16px" height="16px">';
    }

    $fleet_gesamt = 0;

    $zeile = '';
    if ($full_access)
    {
	    $zeile .= '<span class="ally-flotte-name">'.$spielername.'</span>';
		$zeile .= '<span class="ally-rasse">'.$rasse.'</span>';
	    $zeile .= '<span class="ally-zahl">'.$sector.':'.$system.'</span>';
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
   				$zeile .= allyflotte_zelle('-angriff', $fleet_fsize, $fleet_aktzeit);
    		}
   		}
   		elseif ($fleet_aktion == "2"){//Verteidigung
   			$fleet_deff = $fleet_deff + $fleet_fsize;
   			if ($full_access)
    		{
   				$zeile .= allyflotte_zelle('-verteidigung', $fleet_fsize, $fleet_aktzeit);
    		}
   		}
   		elseif ($fleet_aktion == "3"){//Rückflug
   			$fleet_return = $fleet_return + $fleet_fsize;
   			if ($full_access)
    		{
   				$zeile .= allyflotte_zelle('-rueckflug', $fleet_fsize, $fleet_aktzeit);
    		}
		}
		elseif ($fleet_aktion == "4"){//Mission
			$fleet_mission = $fleet_mission + $fleet_fsize;
			if ($full_access){
				$zeile .= allyflotte_zelle('-mission', $fleet_fsize, $fleet_aktzeit);
			}
		}

   		else
   		{
   			$fleet_home = $fleet_home + $fleet_fsize;
   			if ($full_access)
    		{
   				$zeile .= allyflotte_zelle('', $fleet_fsize, $fleet_aktzeit);
    		}
   		}


	}
	if ($full_access)
    {
		$zeile .= '<span class="ally-zahl ally-flotte-gesamt">'.number_format($fleet_gesamt, 0,'','.').'</span>';
		$zeilen .= '<div class="ally-flotte">'.$zeile.'</div>';
    }
	$f_all = $f_all + $fleet_gesamt;
}

rahmen_oben($allyfleet_lang['allianzflottenstatus']);
echo '<div class="ally mod">';

//Summen nach Auftrag
echo '
	<div class="ally-flottensummen">
		<div class="ov-wert ally-fl-angriff"><span class="mod-typ">'.$allyfleet_lang['aflotten'].'</span><b>'.number_format($fleet_attack, 0,'','.').'</b></div>
		<div class="ov-wert ally-fl-verteidigung"><span class="mod-typ">'.$allyfleet_lang['vflotten'].'</span><b>'.number_format($fleet_deff, 0,'','.').'</b></div>
		<div class="ov-wert ally-fl-rueckflug"><span class="mod-typ">'.$allyfleet_lang['zflotten'].'</span><b>'.number_format($fleet_return, 0,'','.').'</b></div>
		<div class="ov-wert ally-fl-mission"><span class="mod-typ">Auf Mission</span><b>'.number_format($fleet_mission, 0,'','.').'</b></div>
		<div class="ov-wert"><span class="mod-typ">'.$allyfleet_lang['sflotten'].'</span><b>'.number_format($fleet_home, 0,'','.').'</b></div>
		<div class="ov-wert ally-flotte-summe"><span class="mod-typ">'.$allyfleet_lang['fgesamt'].'</span><b>'.number_format($f_all, 0,'','.').'</b></div>
	</div>';

//Einzelne Flotten nur für Leader, Co-Leader und Fleetcommander
if ($full_access){
	echo '
	<div class="ally-abschnitt">
		<div class="ally-flotte ally-zeilenkopf">
			<span>Name</span>
			<span class="ally-rasse" title="Rasse">R</span>
			<span class="ally-rechts">'.$allyfleet_lang['koords'].'</span>
			<span class="ally-rechts" title="'.$allyfleet_lang['heimatflotte'].'">Heimat</span>
			<span class="ally-rechts">'.$allyfleet_lang['flotte'].' I</span>
			<span class="ally-rechts">'.$allyfleet_lang['flotte'].' II</span>
			<span class="ally-rechts">'.$allyfleet_lang['flotte'].' III</span>
			<span class="ally-rechts">'.$allyfleet_lang['gesamt'].'</span>
		</div>
		<div class="ally-zeilen">'.$zeilen.'</div>
		<div class="ally-fuss ally-legende">
			<span class="mod-typ">'.$allyfleet_lang['legende'].'</span>
			<span class="mod-chip">Stationiert</span>
			<span class="mod-chip ally-fl-angriff">'.$allyfleet_lang['angriff'].'</span>
			<span class="mod-chip ally-fl-verteidigung">'.$allyfleet_lang['verteidigung'].'</span>
			<span class="mod-chip ally-fl-rueckflug">'.$allyfleet_lang['rueckflug'].'</span>
			<span class="mod-chip ally-fl-mission">Mission</span>
			<span class="mod-chip">(Zeit des Auftrags)</span>
		</div>
	</div>';
}

echo '</div>';
rahmen_unten();

?>
<?php include("ally/ally.footer.inc.php") ?>

</body>
</html>
