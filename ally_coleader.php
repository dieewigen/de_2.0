<?php
//	--------------------------------- ally_coleader.php ---------------------------------
//	Funktion der Seite:		Festlegen und Anzeigen von Co-Leadern der Allianz
//	Letzte �nderung:		05.09.2002
//	Letzte �nderung von:	Ascendant
//
//	�nderungshistorie:
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

	R�ckgabewert:

	$select		 : HTML-Definition der generierten Auswahlbox. Inhalt der Auswahlbox sind alle
				   Mitgleider der Allianz $ally. Spieler $co ist vorselektiert. Die Auswahlbox
				   trägt den Namen $select_name im Formular
*/
function getSelect($leaderid, $select_name, $co, $ally){
	global $allycoleader_lang;

	$coleader=false;
	//Erzeugen des öffnenden <select> - Tags
	$select = '<select name="'.$select_name.'" id="'.$select_name.'" style="width:150px;height:18;font-size:8pt;font-family:Tahoma">';
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
		echo $allycoleader_lang['msg_2'];
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
//Einbinden des Allianzmen�s
include('ally/ally.menu.inc.php');

//Prüfen, ob der aktuelle User Leaderbefugnisse hat
if ($isleader && $ally_id > 0 ){
	$coleader1=isset($_POST['coleader1']) ? $_POST['coleader1'] : null;
	$coleader2=isset($_POST['coleader2']) ? $_POST['coleader2'] : null;
	$coleader3=isset($_POST['coleader3']) ? $_POST['coleader3'] : null;
	
	if (isset($coleader1) && isset($coleader2)){
		if (($coleader1 == $coleader2) && (($coleader1 != -1) && ($coleader2 != -1))){
			echo $allycoleader_lang['msg_3'];
		}elseif (($_SESSION['ums_user_id'] == $coleader1) || ($_SESSION['ums_user_id'] == $coleader2) || ($_SESSION['ums_user_id'] == $coleader3)){
			echo $allycoleader_lang['msg_4'];
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
				echo '<br><div class="info_box text3">'.$allycoleader_lang['msg_5'].'</div>';
			}else{
				echo $allycoleader_lang['msg_6'];
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


	//Ausgabe des Formulars

	echo '
		<form action="ally_coleader.php" name="coleader" id="coleader" method="post">
			<div align="center" class="cell" style="width: 600px"><table width="100%">
				<tr><td><h2>'.$allycoleader_lang['msg_7'].', '.$spielername.':</h2></td></tr>
				<tr><td><hr></td></tr>
				<tr><td>
					<table width="600">
						<tr><td width="100" align="center" bgcolor="#1c1c1c"><strong>'.$allycoleader_lang['funktion'].'</strong></td><td width="200" align="center" bgcolor="#1c1c1c"><strong>'.$allycoleader_lang['besondererechte'].'</strong></td><td width="150" align="center" bgcolor="#1c1c1c"><strong>'.$allycoleader_lang['postenbezeichnung'].'</strong></td><td width="150" align="center" bgcolor="#1c1c1c"><strong>'.$allycoleader_lang['vergebenanmitglied'].'</strong></td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['allianzleader'].'</td><td align="center" bgcolor="#222222">Leader</td><td align="center" bgcolor="#222222"><input type="text" name="leadername" value="'.$leadername.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$spielername.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['coleader'].'</td><td align="center" bgcolor="#222222">Co-Leader</td><td align="center" bgcolor="#222222"><input type="text" name="coleadername1" value="'.$coleadername1.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_coleader1.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['coleader'].'</td><td align="center" bgcolor="#222222">Co-Leader</td><td align="center" bgcolor="#222222"><input type="text" name="coleadername2" value="'.$coleadername2.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_coleader2.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['coleader'].'</td><td align="center" bgcolor="#222222">Co-Leader</td><td align="center" bgcolor="#222222"><input type="text" name="coleadername3" value="'.$coleadername3.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_coleader3.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['fleetcommander'].'</td><td align="center" bgcolor="#222222">Fleetcommander</td><td align="center" bgcolor="#222222"><input "type"=text name="fcname1" value="'.$fcname1.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_fc1.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['fleetcommander'].'</td><td align="center" bgcolor="#222222">Fleetcommander</td><td align="center" bgcolor="#222222"><input "type"=text name="fcname2" value="'.$fcname2.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_fc2.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['tofficer'].'</td><td align="center" bgcolor="#222222">Tactical Officer</td><td align="center" bgcolor="#222222"><input type="text" name="tacticname1" value="'.$tacticname1.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_tactic1.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['tofficer'].'</td><td align="center" bgcolor="#222222">Tactical Officer</td><td align="center" bgcolor="#222222"><input type="text" name="tacticname2" value="'.$tacticname2.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_tactic2.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['mofficer'].'</td><td align="center" bgcolor="#222222">Member Officer</td><td align="center" bgcolor="#222222"><input type="text" name="membername1" value="'.$membername1.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_member1.'</td></tr>
						<tr><td align="center" bgcolor="#222222">'.$allycoleader_lang['mofficer'].'</td><td align="center" bgcolor="#222222">Member Officer</td><td align="center" bgcolor="#222222"><input type="text" name="membername2" value="'.$membername2.'" style="width:150px;height:20px"></td><td align="center" bgcolor="#222222">'.$select_member2.'</td></tr>
					</table>
				</td></tr>
				<tr><td align="right"><input type="submit" name="submit" value="'.$allycoleader_lang['saveedit'].'"></td></tr>
			</table>
		</form>
	';

}
else
{
	//Ausgabe einer Fehlermeldung, falls der aktuelle User keine Leaderbefugnis hat
	echo $allycoleader_lang['msg_8'];
}


?>
<?php include('ally/ally.footer.inc.php'); ?>


</body>
</html>