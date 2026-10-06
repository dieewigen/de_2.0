<?php
//	--------------------------------- ally_coleader.php ---------------------------------
//	Funktion der Seite:		Festlegen und Anzeigen von Co-Leadern der Allianz
//	Letzte Änderung:		05.09.2002
//	Letzte Änderung von:	Ascendant
//
//	Änderungshistorie:
//
//	05.02.2002 (Ascendant)	- Seite erstellt.
//
//  --------------------------------------------------------------------------------
include('inc/header.inc.php');
include('inc/lang/'.$sv_server_lang.'_ally.coleader.lang.php');
include_once('functions.php');


$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector,
            `system`, newtrans, newnews, ally_id, allytag, status, spielername
     FROM de_user_data WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];
$restyp02=$row['restyp02'];
$restyp03=$row['restyp03'];
$restyp04=$row['restyp04'];
$restyp05=$row['restyp05'];
$punkte=$row['score'];
$newtrans=$row['newtrans'];
$newnews=$row['newnews'];
$sector=$row['sector'];
$system=$row['system'];
$spielername=$row['spielername'];

$ally_id=-1;
if($row['ally_id'] > 0 && $row['status'] == 1){
	$ally_id=$row['ally_id'];
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allycoleader_lang['title'];?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

/*
	Die Function getSelect($leaderid, $select_name, $co, $ally) erzeugt die zur
	Auswahl der Co-Leader benötigten Auswahlboxen. Der aktuell belegte Coleader
	wird automatisch vorselektiert. Ist kein Co-Leader belegt (Wert in Datenbank = -1)
	wird der Auswahlpunkt "Nicht belegt" vorselektiert.

	Parameterbeschreibung:

	$leaderid    : Id des Allianzleaders (Int)
	$select_name : Formularname, der für die Auswahlbox erzeugt werden soll (String)
	$co			 : Id des Users, der vorselektiert sein soll (aktueller Coleader) (Int)
	$ally		 : Allianz-Tag, für den die Auswahlbox erzeugt werden soll (String)

	Rückgabewert:

	$select		 : HTML-Definition der generierten Auswahlbox. Inhalt der Auswahlbox sind alle
				   Mitgleider der Allianz $ally. Spieler $co ist vorselektiert. Die Auswahlbox
				   trägt den Namen $select_name im Formular
*/
function getSelect($leaderid, $select_name, $co, $ally){
	global $allycoleader_lang;

	$coleader=false;
	//Erzeugen des öffnenden <select> - Tags
	$select = '<select name="'.$select_name.'" id="'.$select_name.'" class="mod-eingabe">';
	//Ermitteln aller Mitglieder der Allianz $ally
	$result_member = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT user_id, spielername FROM de_user_data WHERE ally_id = ? AND status = '1'",
		[$ally]
	);
	//Prüfen, ob ein gültiges Resultset vom Datenbankserver erzeugt wurde
	if ($result_member){
		//Ermitteln der Anzahl Datensätze im Resultset
		$numrows = mysqli_num_rows($result_member);
		//Schleife über alle Elemente des Resultsets
		for ($i=0;$i<$numrows;$i++){
			//Auslesen des aktuellen Datensatzes aus dem Resultset
			$ally_members[$i] = mysqli_fetch_assoc($result_member);
			//Ermitteln der user_id
			$uid = $ally_members[$i]['user_id'];
			//Ermitteln des Spielernamens
			$uname = $ally_members[$i]['spielername'];
			//Prüfen, ob der aktuelle Spieler der übergebene Coleader $co ist
			if ($uid == $co){
				//Der Spieler ist der aktuelle Coleader. Generieren der Vorbelegung der Auswahlbox
				$select = $select.'<option value="'.$uid.'" selected>'.$uname.'</option>';
				//Flag setzen, das die Position des Coleaders besetzt ist.
				$coleader = TRUE;
			}else{//Der aktuelle Spieler ist kein Coleader
				//Generieren des normalen <option> - Tags
				$select = $select.'<option value="'.$uid.'">'.$uname.'</option>';
			}
		}
	}
	//Der Datenbankserver hat kein gültiges Resultset zurückgegeben. Ausgabe einer Fehlermeldung.
	else
	{
		echo '<div class="mod-meldung mod-meldung-fehler">'.$allycoleader_lang['msg_2'].'</div>';
	}
	//abschliessende Prüfung, ob ein vorselektierter Eintrag generiert wurde (also ob schon ein Co-Leader
	//eingetragen ist).
	if (!$coleader)
	{
		//Falls kein Co-Leader im Datensatz der Allianz vorhanden ist, wird die Auswahloption
		//"Nicht belegt" vorselektiert
		$select = $select.'<option value="-1" selected>'.$allycoleader_lang['msg_1'].'</option>';
	}
	else
	{
		//Ansonsten wird die Auswahloption als nicht vorselektiert angefügt
		$select = $select.'<option value="-1">'.$allycoleader_lang['msg_1'].'</option>';
	}
	//Erzeugen des schliessenden Tags
	$select = $select.'</select>';
	//Rückgabe der fertigen Auswahlbox
	return $select;
}

//Einbinden der Ressourcenanzeige
include('resline.php');
//Einbinden des Allianzmenüs
include('ally/ally.menu.inc.php');

//Meldungen ohne die angehängten Zeilenumbrüche aus den Sprachdateien
function posten_meldung($text, $art)
{
	return '<div class="mod-meldung mod-meldung-'.$art.'">'.preg_replace('/(<br>)+$/i', '', $text).'</div>';
}

//Prüfen, ob der aktuelle User Leaderbefugnisse hat
if ($isleader && $ally_id > 0 ){
	$meldung='';
	$coleader1=isset($_POST['coleader1']) ? $_POST['coleader1'] : null;
	$coleader2=isset($_POST['coleader2']) ? $_POST['coleader2'] : null;
	$coleader3=isset($_POST['coleader3']) ? $_POST['coleader3'] : null;

	if (isset($coleader1) && isset($coleader2)){
		if (($coleader1 == $coleader2) && (($coleader1 != -1) && ($coleader2 != -1))){
			$meldung=posten_meldung($allycoleader_lang['msg_3'], 'fehler');
		}elseif (($_SESSION['ums_user_id'] == $coleader1) || ($_SESSION['ums_user_id'] == $coleader2) || ($_SESSION['ums_user_id'] == $coleader3)){
			$meldung=posten_meldung($allycoleader_lang['msg_4'], 'fehler');
		}else{
			$leadername=$_POST['leadername'];

			$coleadername1=$_POST['coleadername1'];
			$coleadername2=$_POST['coleadername2'];
			$coleadername3=$_POST['coleadername3'];

			$fc1=$_POST['fc1'];
			$fc2=$_POST['fc2'];

			$fcname1=$_POST['fcname1'];
			$fcname2=$_POST['fcname2'];

			$tactic1=$_POST['tactic1'];
			$tactic2=$_POST['tactic2'];

			$tacticname1=$_POST['tacticname1'];
			$tacticname2=$_POST['tacticname2'];

			$member1=$_POST['member1'];
			$member2=$_POST['member2'];

			$membername1=$_POST['membername1'];
			$membername2=$_POST['membername2'];

			//Posten nur an Mitglieder der eigenen Allianz (oder -1 = unbesetzt)
			$own_members=array();
			$res_members=mysqli_execute_query($GLOBALS['dbi'],
				"SELECT u.user_id FROM de_user_data u JOIN de_allys a ON a.id=u.ally_id WHERE a.leaderid=? AND u.status=1",
				[$_SESSION['ums_user_id']]);
			while($row_member=mysqli_fetch_assoc($res_members)){
				$own_members[]=(int)$row_member['user_id'];
			}
			$valid_post=function($id) use ($own_members){
				$id=intval($id);
				return ($id==-1 || in_array($id, $own_members, true)) ? $id : -1;
			};
			$coleader1=$valid_post($coleader1);
			$coleader2=$valid_post($coleader2);
			$coleader3=$valid_post($coleader3);
			$fc1=$valid_post($fc1);
			$fc2=$valid_post($fc2);
			$tactic1=$valid_post($tactic1);
			$tactic2=$valid_post($tactic2);
			$member1=$valid_post($member1);
			$member2=$valid_post($member2);

			$result_update = mysqli_execute_query($GLOBALS['dbi'],
				"UPDATE de_allys SET
					coleaderid1 = ?, coleaderid2 = ?, coleaderid3 = ?,
					fleetcommander1 = ?, fleetcommander2 = ?,
					tacticalofficer1 = ?, tacticalofficer2 = ?,
					memberofficer1 = ?, memberofficer2 = ?,
					leadername = ?, coleadername1 = ?, coleadername2 = ?, coleadername3 = ?,
					fcname1 = ?, fcname2 = ?,
					toname1 = ?, toname2 = ?,
					moname1 = ?, moname2 = ?
				WHERE leaderid = ?",
				[
					$coleader1, $coleader2, $coleader3,
					$fc1, $fc2,
					$tactic1, $tactic2,
					$member1, $member2,
					$leadername, $coleadername1, $coleadername2, $coleadername3,
					$fcname1, $fcname2,
					$tacticname1, $tacticname2,
					$membername1, $membername2,
					$_SESSION['ums_user_id']
				]
			);
			if ($result_update){
				$meldung=posten_meldung($allycoleader_lang['msg_5'], 'ok');
			}else{
				$meldung=posten_meldung($allycoleader_lang['msg_6'], 'fehler');
			}
		}
	}

	//Allianz-Datensatz laden und Daten anzeigen
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT coleaderid1, coleaderid2, coleaderid3,
				fleetcommander1, fleetcommander2,
				tacticalofficer1, tacticalofficer2,
				memberofficer1, memberofficer2,
				leadername, coleadername1, coleadername2, coleadername3,
				fcname1, fcname2, toname1, toname2, moname1, moname2
		FROM de_allys WHERE id = ?",
		[$ally_id]
	);

	$row = mysqli_fetch_assoc($result);

	//Ermitteln der neuen Coleader
	$coleaderid1 = $row["coleaderid1"];
	$coleaderid2 = $row["coleaderid2"];
	$coleaderid3 = $row["coleaderid3"];
	$fleetcommander1 = $row["fleetcommander1"];
	$fleetcommander2 = $row["fleetcommander2"];
	$tacticalofficer1 = $row["tacticalofficer1"];
	$tacticalofficer2 = $row["tacticalofficer2"];
	$memberofficer1 = $row["memberofficer1"];
	$memberofficer2 = $row["memberofficer2"];

	$leadername = $row["leadername"];
	$coleadername1 = $row["coleadername1"];
	$coleadername2 = $row["coleadername2"];
	$coleadername3 = $row["coleadername3"];
	$fcname1 = $row["fcname1"];
	$fcname2 = $row["fcname2"];
	$tacticname1 = $row["toname1"];
	$tacticname2 = $row["toname2"];
	$membername1 = $row["moname1"];
	$membername2 = $row["moname2"];


	//Generieren der Auswahlboxen
	$select_coleader1 = getSelect($_SESSION['ums_user_id'], "coleader1", $coleaderid1, $ally_id);
	$select_coleader2 = getSelect($_SESSION['ums_user_id'], "coleader2", $coleaderid2, $ally_id);
	$select_coleader3 = getSelect($_SESSION['ums_user_id'], "coleader3", $coleaderid3, $ally_id);
	$select_fc1 = getSelect($_SESSION['ums_user_id'], "fc1", $fleetcommander1, $ally_id);
	$select_fc2 = getSelect($_SESSION['ums_user_id'], "fc2", $fleetcommander2, $ally_id);
	$select_tactic1 = getSelect($_SESSION['ums_user_id'], "tactic1", $tacticalofficer1, $ally_id);
	$select_tactic2 = getSelect($_SESSION['ums_user_id'], "tactic2", $tacticalofficer2, $ally_id);
	$select_member1 = getSelect($_SESSION['ums_user_id'], "member1", $memberofficer1, $ally_id);
	$select_member2 = getSelect($_SESSION['ums_user_id'], "member2", $memberofficer2, $ally_id);

	//eine Zeile je Posten: Funktion, Rechte, eigene Bezeichnung, Mitglied
	$rechte_leader='<span class="mod-chip mod-chip-gruen">alle Rechte</span>';
	$rechte_co='<span class="mod-chip" title="Antr&auml;ge, Mitglieder entlassen, B&uuml;ndnisse, Kriege, Zahlungsziel, Allianzdaten">Verwaltung</span>';
	$rechte_fc='<span class="mod-chip" title="Allianzflotten im Detail">Flotten&uuml;bersicht</span>';
	$rechte_titel='<span class="mod-chip">nur Titel</span>';
	$posten=array(
		array($allycoleader_lang['allianzleader'], $rechte_leader, 'leadername', $leadername, '<span class="ally-posten-leader">'.$spielername.'</span>'),
		array($allycoleader_lang['coleader'], $rechte_co, 'coleadername1', $coleadername1, $select_coleader1),
		array($allycoleader_lang['coleader'], $rechte_co, 'coleadername2', $coleadername2, $select_coleader2),
		array($allycoleader_lang['coleader'], $rechte_co, 'coleadername3', $coleadername3, $select_coleader3),
		array($allycoleader_lang['fleetcommander'], $rechte_fc, 'fcname1', $fcname1, $select_fc1),
		array($allycoleader_lang['fleetcommander'], $rechte_fc, 'fcname2', $fcname2, $select_fc2),
		array($allycoleader_lang['tofficer'], $rechte_titel, 'tacticname1', $tacticname1, $select_tactic1),
		array($allycoleader_lang['tofficer'], $rechte_titel, 'tacticname2', $tacticname2, $select_tactic2),
		array($allycoleader_lang['mofficer'], $rechte_titel, 'membername1', $membername1, $select_member1),
		array($allycoleader_lang['mofficer'], $rechte_titel, 'membername2', $membername2, $select_member2),
	);
	$zeilen='';
	foreach($posten as $p){
		$zeilen.='
				<div class="ally-postenzeile">
					<span class="ally-posten-funktion">'.$p[0].'</span>
					<span>'.$p[1].'</span>
					<input type="text" name="'.$p[2].'" value="'.html_text($p[3]).'" class="mod-eingabe" placeholder="'.$p[0].'">
					'.$p[4].'
				</div>';
	}

	//Ausgabe des Formulars
	rahmen_oben('Postenvergabe');
	echo '
		<form action="ally_coleader.php" name="coleader" id="coleader" method="post" class="ally mod">
			'.$meldung.'
			<div class="mod-hinweis">Hier vergibst Du die Posten Deiner Allianz und kannst ihnen eigene Bezeichnungen geben. Die Rechte h&auml;ngen an der Funktion: Co-Leader verwalten unter anderem Antr&auml;ge, B&uuml;ndnisse und Kriege, Fleetcommander sehen die Allianzflotten im Detail. Tactical und Member Officer sind Titel ohne eigene Rechte.</div>
			<div class="ally-postenzeile ally-zeilenkopf">
				<span>'.$allycoleader_lang['funktion'].'</span>
				<span>'.$allycoleader_lang['besondererechte'].'</span>
				<span>'.$allycoleader_lang['postenbezeichnung'].'</span>
				<span>'.$allycoleader_lang['vergebenanmitglied'].'</span>
			</div>
			<div class="ally-zeilen">'.$zeilen.'</div>
			<div class="ally-aktionen">
				<input type="submit" name="submit" value="&Auml;nderungen speichern" class="mod-btn">
			</div>
		</form>
	';
	rahmen_unten();

}
else
{
	//Ausgabe einer Fehlermeldung, falls der aktuelle User keine Leaderbefugnis hat
	echo '<div class="mod ally-meldung">'.posten_meldung($allycoleader_lang['msg_8'], 'fehler').'</div>';
}


?>
<?php include('ally/ally.footer.inc.php'); ?>


</body>
</html>
