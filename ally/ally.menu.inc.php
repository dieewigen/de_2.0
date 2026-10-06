<?php
include 'inc/lang/'.$sv_server_lang.'_ally.menu.lang.php';

// Sicherheitsprüfung: Bestimme anhand des Dateinamens, ob es eine Leader-Seite ist
$script_name = basename($_SERVER['SCRIPT_NAME']);
$leader_pages = [
    'ally_message_leader.php',
    'ally_leader.php',
    'ally_coleader.php',
    'ally_kick.php',
    'ally_delete.php'
];

$leaderpage = in_array($script_name, $leader_pages);

$allys=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys where leaderid=?", [$_SESSION['ums_user_id']]);

// Analog zum Feststellen der Leaderbefugnis wird auch für die beiden Coleader eine Abfrage
// durchgeführt.
$coleader=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys where coleaderid1=? OR coleaderid2=? OR coleaderid3=?", [$_SESSION['ums_user_id'], $_SESSION['ums_user_id'], $_SESSION['ums_user_id']]);
// wird an dieser Stelle ein Resultset mit einem Datensatz zurückgegeben, ist der eingeloggte User
// ein Co-Leader der Allianz

if(mysqli_num_rows($allys)>=1){
	print_LEADER_ally_bar();
	$isleader = true;
	$iscoleader = false;
	$ismember = false;
}
// Erweiterung für Co-Leader Funktionen
elseif (mysqli_num_rows($coleader)>=1){
	print_COLEADER_ally_bar();
	$isleader = false;
	$iscoleader = true;
	$ismember = false;
}else{
	$hatereineally = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(*) as cnt FROM de_user_data WHERE user_id=? and status=1", [$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($hatereineally);
	if($row['cnt']==0){
		print_NOBODY_ally_bar();
		if ($leaderpage){
			die ('<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$allymenu_lang['accessdenied'].'</div></div>');
		}
		$isleader = false;
		$iscoleader = false;
		$ismember = false;
	}else{
		print_MEMBER_ally_bar();
		if ($leaderpage)
			die ('<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$allymenu_lang['accessdenied'].'</div></div>');
		$isleader = false;
		$iscoleader = false;
		$ismember = true;
	}
}

function has_position($position, $allytag, $userid){
	// Whitelist für erlaubte Spaltennamen
	$allowed_positions = array('leaderid', 'coleaderid1', 'coleaderid2', 'coleaderid3', 'fleetcommander1', 'fleetcommander2');

	if (!in_array($position, $allowed_positions)) {
		return false;
	}

	$has_position = false;
	$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT $position FROM de_allys WHERE allytag = ?", [$allytag]);
	$ally_data = mysqli_fetch_array($result);
	if ($ally_data[$position] == $userid) {
		$has_position = true;
	}

	return $has_position;
}

// Reiterleiste der Allianzseiten; Unterseiten (annehmen, entlassen, ...) markieren ihren Reiter,
// Austreten und Löschen sind rot abgesetzt
function print_ally_navi($eintraege, $klasse = ''){
	$zuordnung = array(
		'ally_annehmen.php' => 'ally_antrag.php',
		'ally_ablehnen.php' => 'ally_antrag.php',
		'ally_kick.php' => 'ally_members.php',
		'ally_leader.php' => 'ally_members.php',
		'ally_register2.php' => 'ally_register.php',
	);
	$aktiv = basename($_SERVER['SCRIPT_NAME']);
	if (isset($zuordnung[$aktiv])) {
		$aktiv = $zuordnung[$aktiv];
	}
	echo '<div class="mod ally-navi'.$klasse.'">';
	foreach ($eintraege as $datei => $text) {
		$css = 'ally-reiter';
		if ($datei == 'ally_delete.php' || $datei == 'ally_austritt.php') {
			$css .= ' ally-reiter-gefahr';
		}
		if ($datei == $aktiv) {
			$css .= ' ally-reiter-aktiv';
		}
		echo '<a href="'.$datei.'" class="'.$css.'">'.$text.'</a>';
	}
	echo '</div>';
}

function print_LEADER_ally_bar()
{
	global $allymenu_lang;
	print_ally_navi(array(
		'allymain.php' => $allymenu_lang['allgemein'],
		'ally_coleader.php' => $allymenu_lang['coleader'],
		'ally_members.php' => $allymenu_lang['mitglieder'],
		'ally_antrag.php' => $allymenu_lang['antraege'],
		'ally_partner.php' => $allymenu_lang['buendnis'],
		'ally_war.php' => $allymenu_lang['krieg'],
		'ally_finance.php' => $allymenu_lang['finanzen'],
		'ally_history.php' => $allymenu_lang['allianzhistory'],
		'ally_fleet.php' => $allymenu_lang['allianzflotten'],
		'ally_bldg.php' => 'Projekte',
		'ally_delete.php' => $allymenu_lang['loeschen'],
	));
}

// Ausgabe des Allianzenmenüs für Co-Leader
function print_COLEADER_ally_bar()
{
	global $allymenu_lang;
	print_ally_navi(array(
		'allymain.php' => $allymenu_lang['allgemein'],
		'ally_members.php' => $allymenu_lang['mitglieder'],
		'ally_antrag.php' => $allymenu_lang['antraege'],
		'ally_partner.php' => $allymenu_lang['buendnis'],
		'ally_war.php' => $allymenu_lang['krieg'],
		'ally_finance.php' => $allymenu_lang['finanzen'],
		'ally_history.php' => $allymenu_lang['allianzhistory'],
		'ally_bldg.php' => 'Projekte',
		'ally_fleet.php' => $allymenu_lang['allianzflotten'],
		'ally_austritt.php' => $allymenu_lang['austreten'],
	));
}

function print_MEMBER_ally_bar()
{
	global $allymenu_lang;
	print_ally_navi(array(
		'allymain.php' => $allymenu_lang['allgemein'],
		'ally_members.php' => $allymenu_lang['mitglieder'],
		'ally_partner.php' => $allymenu_lang['buendnis'],
		'ally_war.php' => $allymenu_lang['krieg'],
		'ally_finance.php' => $allymenu_lang['finanzen'],
		'ally_history.php' => $allymenu_lang['allianzhistory'],
		'ally_fleet.php' => $allymenu_lang['allianzflotten'],
		'ally_bldg.php' => 'Projekte',
		'ally_austritt.php' => $allymenu_lang['austreten'],
	));
}

function print_NOBODY_ally_bar(){
	global $allymenu_lang;
	print_ally_navi(array(
		'ally_register.php' => $allymenu_lang['gruenden'],
		'toplist.php?&s=3' => $allymenu_lang['beitreten'],
	), ' ally-navi-frei');

	//überprüfen ob evtl. ein allianzantrag vorliegt und diesen anzeigen/stornieren
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_antrag WHERE user_id=?", [$_SESSION['ums_user_id']]);
	$num = mysqli_num_rows($db_daten);
	if ($num>0)//man hat sich beworben
	{
		$row = mysqli_fetch_array($db_daten);
		$ally_id=$row['ally_id'];

		//überprüfen ob man die bewerbung stornieren möchte
		if(!empty($_REQUEST['stornobewerbung']))
		{
			//allianz informieren
			$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$ally_id]);
			$num = mysqli_num_rows($db_daten);
			if ($num>0)//allianz existiert
			{
				//infos anzeigen
				$row = mysqli_fetch_array($db_daten);

				$leaderid=$row['leaderid'];
				$coleaderid1=$row['coleaderid1'];
				$coleaderid2=$row['coleaderid2'];
				$coleaderid3=$row['coleaderid3'];

				notifyUser($leaderid, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$_SESSION['ums_spielername'], "6");
				if($coleaderid1>0)notifyUser($coleaderid1, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$_SESSION['ums_spielername'], "6");
				if($coleaderid2>0)notifyUser($coleaderid2, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$_SESSION['ums_spielername'], "6");
				if($coleaderid3>0)notifyUser($coleaderid3, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$_SESSION['ums_spielername'], "6");
			}

			//tronic gutschreiben
			$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'", [$_SESSION['ums_user_id']]);
			$row = mysqli_fetch_array($db_daten);
			$tronic=$row['amount'];
			$transactionid=$row['id'];

			if($tronic>0)mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $_SESSION['ums_user_id']]);
			mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_transactions WHERE id = ?", [$transactionid]);

			//bewerbung aus der db löschen
			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET allytag='', status=0 WHERE user_id = ?", [$_SESSION['ums_user_id']]);
			mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_antrag WHERE user_id = ?", [$_SESSION['ums_user_id']]);

			//info anzeigen
			echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-ok">Die Bewerbung wurde zur&uuml;ckgezogen. Tronicgutschrift: <b>'.$tronic.'</b></div></div>';

		}
		else //allianzinfos/abbrechen-link anzeigen
		{
			//daten der zielallianz auslesen
			$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE id=?", [$ally_id]);
			$num = mysqli_num_rows($db_daten);
			if ($num>0)//allianz existiert
			{
				//infos anzeigen
				$row = mysqli_fetch_array($db_daten);
				$GLOBALS['ally_bewerbung_offen'] = true;
				echo '
				<div class="mod ally-meldung">
					<div class="ally-bewerbung">
						<div class="ally-bewerbung-text">
							<span class="mod-typ">Offene Bewerbung</span>
							<span>Du hast Dich bei der Allianz <b>'.htmlspecialchars($row['allytag'], ENT_QUOTES, 'UTF-8').'</b> beworben und wartest auf die Entscheidung.</span>
						</div>
						<a href="allymain.php?stornobewerbung=1" class="mod-btn mod-btn-leise">Bewerbung zur&uuml;ckziehen</a>
					</div>
				</div>';

			}
			else //allianz existiert nicht mehr
			{
				//tronic gutschreiben
				$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'", [$_SESSION['ums_user_id']]);
				$row = mysqli_fetch_array($db_daten);
				$tronic=$row['amount'];
				$transactionid=$row['id'];

				if($tronic>0)mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $_SESSION['ums_user_id']]);
				mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_transactions WHERE id = ?", [$transactionid]);

				//bewerbung aus der db löschen
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET allytag='', status=0 WHERE user_id = ?", [$_SESSION['ums_user_id']]);
				mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_antrag WHERE user_id = ?", [$_SESSION['ums_user_id']]);
			}
		}

	}

}
?>