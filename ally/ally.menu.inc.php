<?php
include 'inc/lang/'.$sv_server_lang.'_ally.menu.lang.php'; 
/*
print("
<div align=center><br>
		<img src=\"ally/cars.gif\" alt=\"Central Alliance Registration System\"><br><br>
</div>
");
*/
/*print("
<div align=center><br>
		<img src=".$ums_gpfad."g/ally/".$ums_rasse."_cars.gif alt=\"Central Alliance Registration System\"><br><br>
</div>
");*/

$allys=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys where leaderid=?", [$ums_user_id]);

// Analog zum Feststellen der Leaderbefugnis wird auch für die beiden Coleader eine Abfrage
// durchgeführt.
$coleader=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys where coleaderid1=? OR coleaderid2=? OR coleaderid3=?", [$ums_user_id, $ums_user_id, $ums_user_id]);
// wird an dieser Stelle ein Resultset mit einem Datensatz zur�ckgegeben, ist der eingeloggte User
// ein Co-Leader der Allianz
// ------------------------ �nderung Ende ---------------------------------------

if(mysqli_num_rows($allys)>=1){
	print_LEADER_ally_bar();
	$isleader = true;
	$iscoleader = false;  //Hinzugef�gt von Ascendant (01.09.2002)
	$ismember = false;
}
// Erweiterung für Co-Leader Funktionen------
elseif (mysqli_num_rows($coleader)>=1){
	print_COLEADER_ally_bar();
	$isleader = false;
	$iscoleader = true;  //Hinzugef�gt von Ascendant (01.09.2002)
	$ismember = false;
}else{
	$hatereineally = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(*) as cnt FROM de_user_data WHERE user_id=? and status=1", [$ums_user_id]);
	$row = mysqli_fetch_assoc($hatereineally);
	if($row['cnt']==0){
		print_NOBODY_ally_bar();
		if ($leaderpage){
			die ($allymenu_lang['accessdenied']);
		}
		$isleader = false;
		$iscoleader = false;   //Hinzugef�gt von Ascendant (01.09.2002)
		$ismember = false;
	}else{
		print_MEMBER_ally_bar();
		if ($leaderpage)
			die ($allymenu_lang['accessdenied']);
		$isleader = false;
		$iscoleader = false;   //Hinzugef�gt von Ascendant (01.09.2002)
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
	$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT ?? FROM de_allys WHERE allytag = ?", [$position, $allytag]);
	$ally_data = mysqli_fetch_array($result);
	if ($ally_data[$position] == $userid) {
		$has_position = true;
	}

	return $has_position;
}



function print_LEADER_ally_bar()
{
	global $ums_gpfad, $ums_rasse, $allymenu_lang;
	print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\" class=\"cell\">
				<tr align=\"center\">
					<td><a href=\"allymain.php\" class=\"btn\">".$allymenu_lang['allgemein']."</a></td>
					<td><a href=\"ally_coleader.php\" class=\"btn\">".$allymenu_lang['coleader']."</a></td>
					<td><a href=\"ally_members.php\" class=\"btn\">".$allymenu_lang['mitglieder']."</a></td>
					<td><a href=\"ally_antrag.php\" class=\"btn\">".$allymenu_lang['antraege']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_partner.php\" class=\"btn\">".$allymenu_lang['buendnis']."</a></td>
					<td><a href=\"ally_war.php\" class=\"btn\">".$allymenu_lang['krieg']."</a></td>
					<td><a href=\"ally_message_leader.php\" class=\"btn\">".$allymenu_lang['hfleader']."</a></td>
					<td><a href=\"ally_message.php\" class=\"btn\">".$allymenu_lang['hfmember']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_finance.php\" class=\"btn\">".$allymenu_lang['finanzen']."</td>
					<td><a href=\"ally_history.php\" class=\"btn\">".$allymenu_lang['allianzhistory']."</a></td>
					<td><a href=\"ally_search.php\" class=\"btn\">".$allymenu_lang['allianzsuche']."</a></td>
					<td><a href=\"ally_delete.php\" class=\"btn\">".$allymenu_lang['loeschen']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_fleet.php\" class=\"btn\">".$allymenu_lang['allianzflotten']."</a></td>
					<td><a href=\"ally_bldg.php\" class=\"btn\">Projekte</a></td>
					<td></td>
					<td></td>
				</tr>
			</table>");

			//<td><a href=\"ally_forum.php\" class=\"btn\">".$allymenu_lang[allianzforum]."</a></td>
}

// ------�nderung von Ascendant (01.09.2002) - Erweiterung f�r Co-Leader Funktionen------
// Ausgabe des Allianzenmen�s f�r Co-Leader
function print_COLEADER_ally_bar()
{
	global $ums_gpfad, $ums_rasse, $allymenu_lang;
	print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\" class=\"cell\">
				<tr align=\"center\">
					<td><a href=\"allymain.php\" class=\"btn\">".$allymenu_lang['allgemein']."</a></td>
					<td><a href=\"ally_members.php\" class=\"btn\">".$allymenu_lang['mitglieder']."</a></td>
					<td><a href=\"ally_antrag.php\" class=\"btn\">".$allymenu_lang['antraege']."</a></td>
					<td><a href=\"ally_partner.php\" class=\"btn\">".$allymenu_lang['buendnis']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_war.php\" class=\"btn\">".$allymenu_lang['krieg']."</a></td>
					<td><a href=\"ally_austritt.php\" class=\"btn\">".$allymenu_lang['austreten']."</a></td>
					<td><a href=\"ally_message.php\" class=\"btn\">".$allymenu_lang['hfmember']."</a></td>
					<td><a href=\"ally_message_leader.php\" class=\"btn\">".$allymenu_lang['hfleader']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_finance.php\" class=\"btn\">".$allymenu_lang['finanzen']."</td>
					<td><a href=\"ally_history.php\" class=\"btn\">".$allymenu_lang['allianzhistory']."</a></td>
					<td><a href=\"ally_search.php\" class=\"btn\">".$allymenu_lang['allianzsuche']."</a></td>
					<td><a href=\"ally_fleet.php\" class=\"btn\">".$allymenu_lang['allianzflotten']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_bldg.php\" class=\"btn\">Projekte</a></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>");
}
// --------------------- �nderung Ende -------------------------------------------

function print_MEMBER_ally_bar()
{
	global $ums_gpfad, $ums_rasse, $allymenu_lang;
	print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\" class=\"cell\">
				<tr align=\"center\">
					<td><a href=\"allymain.php\" class=\"btn\">".$allymenu_lang['allgemein']."</a></td>
					<td><a href=\"ally_members.php\" class=\"btn\">".$allymenu_lang['mitglieder']."</a></td>
					<td><a href=\"ally_partner.php\" class=\"btn\">".$allymenu_lang['buendnis']."</a></td>
					<td><a href=\"ally_war.php\" class=\"btn\">".$allymenu_lang['krieg']."</a></td>
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_austritt.php\" class=\"btn\">".$allymenu_lang['austreten']."</a></td>
					<td><a href=\"ally_finance.php\" class=\"btn\">".$allymenu_lang['finanzen']."</td>
					<td><a href=\"ally_history.php\" class=\"btn\">".$allymenu_lang['allianzhistory']."</a></td>
					<td><a href=\"ally_search.php\" class=\"btn\">".$allymenu_lang['allianzsuche']."</a></td>					
				</tr>
				<tr align=\"center\">
					<td><a href=\"ally_fleet.php\" class=\"btn\">".$allymenu_lang['allianzflotten']."</a></td>
					<td><a href=\"ally_bldg.php\" class=\"btn\">Projekte</a></td>
					<td></td>
					<td></td>
				</tr>
			</table>");
}

function print_NOBODY_ally_bar(){
	global $db, $ums_gpfad, $ums_rasse, $allymenu_lang, $ums_user_id, $ums_spielername;
	print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\" class=\"cell\">
				<tr align=\"center\">
				<td><a href=\"ally_register.php\" class=\"btn\">".$allymenu_lang['gruenden']."</a></td>
				<td><a href=\"ally_search.php\" class=\"btn\">".$allymenu_lang['beitreten']."</a></td>
			</tr>
			</table>");
	
	//�berpr�fen ob evtl. ein allianzantrag vorliegt und diesen anzeigen/stornieren
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_ally_antrag WHERE user_id=?", [$ums_user_id]);
	$num = mysqli_num_rows($db_daten);
	if ($num>0)//man hat sich beworben
	{
		$row = mysqli_fetch_array($db_daten);
		$ally_id=$row['ally_id'];
		
		//�berpr�fen ob man die bewerbung stornieren m�chte
		if($_REQUEST['stornobewerbung'])
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
			
				notifyUser($leaderid, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$ums_spielername, "6");
				if($coleaderid1>0)notifyUser($coleaderid1, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$ums_spielername, "6");
				if($coleaderid2>0)notifyUser($coleaderid2, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$ums_spielername, "6");
				if($coleaderid3>0)notifyUser($coleaderid3, 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$ums_spielername, "6");
			}
			
			//tronic gutschreiben
			$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'", [$ums_user_id]);
			$row = mysqli_fetch_array($db_daten);
			$tronic=$row['amount'];
			$transactionid=$row['id'];
			
			if($tronic>0)mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $ums_user_id]);
			mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_transactions WHERE id = ?", [$transactionid]);
			
			//bewerbung aus der db l�schen
			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET allytag='', status=0 WHERE user_id = ?", [$ums_user_id]);
			mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_antrag WHERE user_id = ?", [$ums_user_id]);
			
			//info anzeigen
			echo '<div class="info_box text1">Die Bewerbung wurde zur&uuml;ckgezogen.';
			echo '<br>Tronicgutschrift: '.$tronic;
			echo '</div><br>';
			
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
				echo '<div class="info_box text1" style="margin-top: 20px;">Du hast Dich bei folgender Allianz beworben und wartest auf die Entscheidung: '.$row['allytag'];
				echo '<br><br><a href="allymain.php?stornobewerbung=1">Bewerbung zur&uuml;ckziehen</a>';
				echo '</div><br>';
			
			}
			else //allianz existiert nicht mehr
			{
				//tronic gutschreiben
				$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'", [$ums_user_id]);
				$row = mysqli_fetch_array($db_daten);
				$tronic=$row['amount'];
				$transactionid=$row['id'];
				
				if($tronic>0)mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $ums_user_id]);
				mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_transactions WHERE id = ?", [$transactionid]);
				
				//bewerbung aus der db l�schen
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET allytag='', status=0 WHERE user_id = ?", [$ums_user_id]);
				mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_ally_antrag WHERE user_id = ?", [$ums_user_id]);
			}
		}
	
	}
	
}

//echo '<div class="cell">';
?>