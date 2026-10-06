<?php
include('inc/header.inc.php');
include('lib/basefunctions.lib.php');
include('inc/lang/'.$sv_server_lang.'_ally.join.lang.php');
include_once('functions.php');

$result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, `system`, newtrans, newnews, allytag, col, npc
     FROM de_user_data
     WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($result);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];$punkte=$row['score'];
$newtrans=$row['newtrans'];$newnews=$row['newnews'];$sector=$row['sector'];$system=$row['system'];
$col_count=$row['col'];$npc=$row['npc'];

$ally_id=intval($_REQUEST['ally_id']);
$ally_data=getAllyByID($ally_id);
//Allianzname stammt vom Gründer, nur escaped ausgeben
$a_name=html_text($ally_data['allyname']);
$a_tag=$ally_data['allytag'];

$t_tojoin = round(($col_count / 4) -1, 0);
if ($t_tojoin < 0)
{
	$t_tojoin = 0;
}
if ($col_count == 0)
{
	$t_tojoin = 0;
}
if ($t_tojoin > 200)
{
	$t_tojoin=200;
}
$row=0;
$sum=0;
$transaction_result = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'",
    [$_SESSION['ums_user_id']]);
if ($transaction_result){
	if (mysqli_num_rows($transaction_result)==1){
		$data = mysqli_fetch_assoc($transaction_result);
		$sum = $data['amount'];
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allyjoin_lang['title']?></title>
<?php include('cssinclude.php'); ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include('resline.php');
include('ally/ally.menu.inc.php');

//Kosten des Beitritts; ein Guthaben aus einer früheren Bewerbung wird angerechnet
$kosten = '<span class="mod-chip">'.$allyjoin_lang['msg_2_1'].' <b>'.$t_tojoin.'</b>'.$allyjoin_lang['msg_2_2'].'</span>';
if ($sum > 0){
	$kosten .= '<span class="mod-chip mod-chip-gruen">'.str_replace('{VALUE1}', $sum, $allyjoin_lang['msg_3']).'</span>';
}

$quit_script = false;
if (($restyp05 + $sum) < $t_tojoin){
	$t_missing = $t_tojoin - $restyp05 + $sum;
	echo '<div class="mod ally-meldung"><div class="mod-meldung mod-meldung-fehler">'.$allyjoin_lang['msg_5_1'].' '.$t_missing.' '.$allyjoin_lang['msg_5_2'].'</div></div>';
	$quit_script = true;
}

if ($quit_script && $npc != 2){
	die(include('ally/ally.footer.inc.php'));
}

$ok=$_POST['ok'] ?? false;
$warnung=$_POST['warnung'] ?? false;
if($ok || $warnung || $npc==2){
	//Ergebnisse in einem Kasten unter den Reitern; Abbrüche (die) schließen ihn selbst
	echo '<div class="mod ally-meldung">';
	$error=false;
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_user_data WHERE user_id=?",
		[$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);

	$user_ally_id = $row['ally_id'];
	$status = $row['status'];

	//Leader müssen ihr Amt immer erst abgeben, auch wenn die Rückfrage (warnung) bestätigt wurde:
	//sonst stünde ihre ally_id auf der neuen Allianz und ally_delete.php würde deren Mitglieder entfernen
	$leader_result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT id FROM de_allys WHERE leaderid=?",
		[$_SESSION['ums_user_id']]);
	if(mysqli_num_rows($leader_result))
	{
		die('<div class="mod-meldung mod-meldung-fehler">'.$allyjoin_lang['msg_4'].'</div></div>');
	}

	//Mitglieder einer Allianz müssen zuerst regulär austreten (ally_austritt.php): dort fallen die Austrittsgebühr,
	//das Räumen von Posten, der Historieneintrag und die Benachrichtigung der Allianzführung an.
	//NPCs (Typ 2) wechseln wie bisher nach Bestätigung (warnung).
	if($user_ally_id>0 && $status==1 && ($npc!=2 || !$warnung))
	{
		$error=true;
	}

	if($error){
		if($npc==2){
			echo '
				<form name="register" method="POST" action="ally_join.php">'.$allyjoin_lang['msg_6'].'
					<input type="hidden" name="ally_id" value="'.$ally_id.'">
					<input type="submit" value="'.$allyjoin_lang['fertig'].'" name="warnung" class="mod-btn"></form>';
		}else{
			echo '<div class="mod-meldung mod-meldung-fehler">'.$allyjoin_lang['msg_17'].'</div><div class="ally-aktionen"><a href="ally_austritt.php" class="mod-btn mod-btn-leise">'.$allyjoin_lang['zumaustritt'].'</a></div>';
		}
	}else{
		if($ally_id<1){
			echo '<div class="mod-meldung mod-meldung-fehler">'.$allyjoin_lang['msg_7'].'</div>';
		}else{
			//wenn der Spieler der sich bewirbt ein NPC Typ 2 ist, dann wird überprüft ob der Allianzleader auch NPC Typ 2 ist
			if($npc==2){
				$ally_leader_result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT leaderid FROM de_allys WHERE id=?",
					[$ally_id]);
				$ally_leader_row = mysqli_fetch_assoc($ally_leader_result);
				$ally_leader_id = $ally_leader_row['leaderid'];

				$leader_npc_result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT npc FROM de_user_data WHERE user_id=?",
					[$ally_leader_id]);
				$leader_npc_row = mysqli_fetch_assoc($leader_npc_result);
				$leader_npc = $leader_npc_row['npc'];

				if($leader_npc!=2){
					echo $allyjoin_lang['msg_15'];
					die();
				}else{
					//den Spieler in die Allianz aufnehmen, dafür de_user_data updaten, ally_id, status=1 und allytag setzen
					$sql="UPDATE de_user_data SET ally_id=?, allytag=?, status = 1 WHERE user_id=?";

					$result = mysqli_execute_query($GLOBALS['dbi'],
						$sql,
						[$ally_id, $a_tag, $_SESSION['ums_user_id']]);
					echo $allyjoin_lang['msg_16'];
					die();
				}

			}else{


				$result = mysqli_execute_query($GLOBALS['dbi'],
					"SELECT * FROM de_allys WHERE id=?",
					[$ally_id]);
				$nb = mysqli_num_rows($result);

				if($nb>0){
					$row = mysqli_fetch_assoc($result);

					$clanid = $row['id'];
					$allytag = $row['allytag'];
					$leaderid = $row['leaderid'];
					$coleaderid1 = $row['coleaderid1'];
					$coleaderid2 = $row['coleaderid2'];
					$coleaderid3 = $row['coleaderid3'];

					//wer aus einer Allianz wechselt, verliert dort seinen Co-Leader-Posten (wie beim Austritt)
					if($user_ally_id>0 && $status==1){
						for($c=1;$c<=3;$c++){
							mysqli_execute_query($GLOBALS['dbi'],
								"UPDATE de_allys SET coleaderid$c=-1 WHERE id=? AND coleaderid$c=?",
								[$user_ally_id, $_SESSION['ums_user_id']]);
						}
					}

					$result = mysqli_execute_query($GLOBALS['dbi'],
						"UPDATE de_user_data SET ally_id=?, allytag=?, status=0 WHERE user_id=?",
						[$ally_id, $allytag, $_SESSION['ums_user_id']]);

					$antrag= $_POST['antrag'];
					$antrag = htmlentities($antrag, ENT_QUOTES);
					$antrag = str_replace("\n","<br>",$antrag);
					//läuft schon eine Bewerbung bei einer anderen Allianz, erfährt deren Führung wie beim Zurückziehen davon
					$alt_antrag = mysqli_fetch_assoc(mysqli_execute_query($GLOBALS['dbi'],
						"SELECT a.ally_id, y.leaderid, y.coleaderid1, y.coleaderid2, y.coleaderid3 FROM de_ally_antrag a JOIN de_allys y ON y.id=a.ally_id WHERE a.user_id=?",
						[$_SESSION['ums_user_id']]));
					if ($alt_antrag && $alt_antrag['ally_id'] != $clanid) {
						foreach (array('leaderid', 'coleaderid1', 'coleaderid2', 'coleaderid3') as $posten) {
							if ($alt_antrag[$posten] > 0) {
								notifyUser($alt_antrag[$posten], 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$_SESSION['ums_spielername'], "6");
							}
						}
					}

					//nur eine Bewerbung je Spieler (eindeutiger Schlüssel user_id): eine neue ersetzt die laufende;
					//früher INSERT mit UPDATE als Ausweichweg, seit PHP 8.1 bricht der doppelte Schlüssel aber mit einer Exception ab,
					//nachdem de_user_data schon auf die neue Allianz zeigt
					$result = mysqli_execute_query($GLOBALS['dbi'],
						"INSERT into de_ally_antrag (user_id, ally_id, antrag) VALUES (?, ?, ?)
						 ON DUPLICATE KEY UPDATE ally_id=VALUES(ally_id), antrag=VALUES(antrag)",
						[$_SESSION['ums_user_id'], $clanid, $antrag]);

					notifyUser($leaderid, $allyjoin_lang['msg_8'], "6");
					notifyUser($coleaderid1, $allyjoin_lang['msg_8'], "6");
					notifyUser($coleaderid2, $allyjoin_lang['msg_8'], "6");
					notifyUser($coleaderid3, $allyjoin_lang['msg_8'], "6");

					$transaction_result = mysqli_execute_query($GLOBALS['dbi'],
						"SELECT * FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'",
						[$_SESSION['ums_user_id']]);
					if ($transaction_result){
						if (mysqli_num_rows($transaction_result)==1){
							$data = mysqli_fetch_assoc($transaction_result);
							$sum = $data['amount'];
							mysqli_execute_query($GLOBALS['dbi'],
								"UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?",
								[$sum, $_SESSION['ums_user_id']]);
							mysqli_execute_query($GLOBALS['dbi'],
								"UPDATE de_transactions SET amount=? WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'",
								[$t_tojoin, $_SESSION['ums_user_id']]);
							mysqli_execute_query($GLOBALS['dbi'],
								"UPDATE de_user_data SET restyp05=restyp05-? WHERE user_id=?",
								[$t_tojoin, $_SESSION['ums_user_id']]);
							echo '<div class="mod-meldung mod-meldung-ok">'.$allyjoin_lang['msg_9_1'].' '.$sum.' '.$allyjoin_lang['msg_9_2'].'</div>';
						}else{
							mysqli_execute_query($GLOBALS['dbi'],
								"INSERT INTO de_transactions (user_id, type, identifier, name, amount) VALUES(?, 'C.A.R.S.', 'reg_fee', 'Tronic', ?)",
								[$_SESSION['ums_user_id'], $t_tojoin]);
							mysqli_execute_query($GLOBALS['dbi'],
								"UPDATE de_user_data SET restyp05=restyp05-? WHERE user_id=?",
								[$t_tojoin, $_SESSION['ums_user_id']]);
						}
					}
					echo '<div class="mod-meldung mod-meldung-ok">'.$allyjoin_lang['msg_10_1'].' '.$t_tojoin.' '.$allyjoin_lang['msg_10_2'].'</div>';
				}
				else
				{
					echo '<div class="mod-meldung mod-meldung-fehler">'.$allyjoin_lang['msg_11'].' !</div>';
				} // else $nb>0
			}
		}  // else $clan


	} // $ok check
	echo '</div>';
}else{ // else $ok

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT allyname, antrag FROM de_ally_antrag antrag, de_allys allys WHERE allys.id=antrag.ally_id AND user_id=?",
		[$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($result);

	$antrag_allyname = html_text($row["allyname"] ?? '');
	$antrag_antrag 	 = $row["antrag"] ?? '';

	$result = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_allys ORDER BY allyname ASC");
	$nb = mysqli_num_rows($result);

	rahmen_oben($allyjoin_lang['neuebewerbung']);
	echo '<div class="ally mod">';

	//laufende Bewerbung
	echo '<div class="mod-typ ally-typ-abstand">'.$allyjoin_lang['aktivebewerbung'].'</div>';
	if ($antrag_allyname == "")
	{
		echo '<div class="ally-hinweis">'.$allyjoin_lang['msg_12'].'</div>';
	}
	else
	{
		echo '<div class="ally-text">'.$allyjoin_lang['msg_13_1'].' <strong>'.$antrag_allyname.'</strong> '.$allyjoin_lang['msg_13_2'].':<br /><br />'.$antrag_antrag.'</div>';
	}

	//neue Bewerbung
	echo '
		<form name="register" method="POST" action="ally_join.php" class="ally-abschnitt">
			<div class="mod-typ ally-typ-abstand">'.$allyjoin_lang['neuebewerbung'].'</div>
			<input type="hidden" name="ally_id" value="'.$ally_id.'">
			<label class="ally-feld">
				<span>'.str_replace('<br><br> ', ' ', $allyjoin_lang['msg_14_1'].' <strong>'.$a_name.'</strong> '.$allyjoin_lang['msg_14_2']).':</span>
				<textarea name="antrag" rows="7" wrap="virtual" class="mod-eingabe"></textarea>
			</label>
			<div class="ally-aktionen">
				<span class="ally-chips ally-aktionen-text">'.$kosten.'</span>
				<input type="submit" value="'.$allyjoin_lang['bewerbungsenden'].'" name="ok" class="mod-btn">
			</div>
		</form>';

	echo '</div>';
	rahmen_unten();

}

