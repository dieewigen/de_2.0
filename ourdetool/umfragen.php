<?php
include "../inccon.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

<head>
	<title>Umfragen</title>
	<?php include "cssinclude.php"; ?>
	<style type="text/css">
		.buttons {
			background-color: #000000;
			border: 1;
			border-color: #3399FF;
			border-style: solid;
			color: #3399FF
		}

		.fett {
			color: #FFFFFF;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<br><br>
	<div style="width:100%; font-size:40pt; color:#FF0000;font-family: Impact;
    filter:Glow(color=#00FF00, strength=12)">Umfragen</div>

	<?php
	include "det_userdata.inc.php";

	$stimmengesamt=0;
	$prozentegesamt=0;

	// Sicherstellen, dass die Variable $action definiert ist
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;

	if ($action == "ak") {
		$time = strftime("%Y-%m-%d %H:%M:%S");
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_vote_umfragen SET status = 1, startdatum=? WHERE id=?", [$time, $id]);
		echo "<h2>Vote aktiviert</h2>";

		$anzahlantworten = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=?", [$id]);
		$riw = mysqli_fetch_assoc($anzahlantworten);

		$antworten = explode("|", $riw['antworten']);
		$i = 1;
		while ($i <= count($antworten)) {
			mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_vote_stimmen (user_id, vote_id, votefor) VALUES (?, ?, ?)", [0, $id, $i]);
			$i++;
		}
	}

	if ($action == "deak") {
		$endstimmen = array();
		$counter = 0;
		$db_endstimmen = mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(votefor) AS count, votefor FROM de_vote_stimmen WHERE vote_id=? GROUP BY votefor", [$id]);
		while ($stimmen = mysqli_fetch_assoc($db_endstimmen)) {
			$endstimmen[$counter] = $stimmen['count'];
			$counter++;
		}

		$anzahlantworten = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=?", [$id]);
		$riw = mysqli_fetch_assoc($anzahlantworten);
		$antworten = explode("|", $riw['antworten']);

		$z = 0;
		$abgegebenestimmen = 0;
		$db_ergebnisse='';
		while ($z < count($endstimmen)) {
			$db_ergebnisse = $db_ergebnisse . '|' . ($endstimmen[$z] - 1);
			$abgegebenestimmen = $abgegebenestimmen + $endstimmen[$z];

			$z++;
		}
		$db_ergebnisse = trim(substr($db_ergebnisse, 1, 100));

		$abgegebenestimmen = $abgegebenestimmen - count($antworten);

		$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) FROM de_user_data WHERE npc=0 AND sector > 1", []);

		$row = mysqli_fetch_array($db_daten);
		$gesamtuser = $row[0];
		if ($gesamtuser == 0) {
			$gesamtuser = 1;
		}

		$stimmen = $abgegebenestimmen . '|' . $gesamtuser;

		echo $stimmen;


		$time = strftime("%Y-%m-%d %H:%M:%S");

		mysqli_execute_query(
			$GLOBALS['dbi'],
			"UPDATE de_vote_umfragen SET status=2, enddatum=?, ergebnisse=?, stimmen=? WHERE id=?",
			[$time, $db_ergebnisse, $stimmen, $id]
		);
		echo "<h2>Vote deaktiviert</h2>";
	}

	$a1 = isset($_REQUEST['a1']) ? $_REQUEST['a1'] : '';
	$a2 = isset($_REQUEST['a2']) ? $_REQUEST['a2'] : '';
	$a3 = isset($_REQUEST['a3']) ? $_REQUEST['a3'] : '';
	$a4 = isset($_REQUEST['a4']) ? $_REQUEST['a4'] : '';
	$a5 = isset($_REQUEST['a5']) ? $_REQUEST['a5'] : '';
	$a6 = isset($_REQUEST['a6']) ? $_REQUEST['a6'] : '';
	$a7 = isset($_REQUEST['a7']) ? $_REQUEST['a7'] : '';
	$a8 = isset($_REQUEST['a8']) ? $_REQUEST['a8'] : '';
	$a9 = isset($_REQUEST['a9']) ? $_REQUEST['a9'] : '';
	$a10 = isset($_REQUEST['a10']) ? $_REQUEST['a10'] : '';


	if (isset($_REQUEST['subentry']) && !empty($_REQUEST['frage']) && !empty($_REQUEST['hinweis'])) {
		$hinweis = $_REQUEST['hinweis'];
		$a1 = str_replace("|", "", $a1);
		$a2 = str_replace("|", "", $a2);
		$a3 = str_replace("|", "", $a3);
		$a4 = str_replace("|", "", $a4);
		$a5 = str_replace("|", "", $a5);
		$a6 = str_replace("|", "", $a6);
		$a7 = str_replace("|", "", $a7);
		$a8 = str_replace("|", "", $a8);
		$a9 = str_replace("|", "", $a9);
		$a10 = str_replace("|", "", $a10);

		$a1 = trim($a1);
		$a2 = trim($a2);
		$a3 = trim($a3);
		$a4 = trim($a4);
		$a5 = trim($a5);
		$a6 = trim($a6);
		$a7 = trim($a7);
		$a8 = trim($a8);
		$a9 = trim($a9);
		$a10 = trim($a10);

		if ($a1 != "") $antworten = $a1;
		if ($a2 != "") $antworten = "$antworten|$a2";
		if ($a3 != "") $antworten = "$antworten|$a3";
		if ($a4 != "") $antworten = "$antworten|$a4";
		if ($a5 != "") $antworten = "$antworten|$a5";
		if ($a6 != "") $antworten = "$antworten|$a6";
		if ($a7 != "") $antworten = "$antworten|$a7";
		if ($a8 != "") $antworten = "$antworten|$a8";
		if ($a9 != "") $antworten = "$antworten|$a9";
		if ($a10 != "") $antworten = "$antworten|$a10";

		mysqli_execute_query(
			$GLOBALS['dbi'],
			"INSERT INTO de_vote_umfragen(frage, antworten, hinweis, status) VALUES (?, ?, ?, 0)",
			[$frage, $antworten, $hinweis]
		);

		echo "<h2>Umfrage erfolgreich erstellt</h2>";
	}

	if (isset($_REQUEST['subedit'])) {
		$hinweis = $_REQUEST['hinweis'];
		$a1 = str_replace("|", "", $a1);
		$a2 = str_replace("|", "", $a2);
		$a3 = str_replace("|", "", $a3);
		$a4 = str_replace("|", "", $a4);
		$a5 = str_replace("|", "", $a5);
		$a6 = str_replace("|", "", $a6);
		$a7 = str_replace("|", "", $a7);
		$a8 = str_replace("|", "", $a8);
		$a9 = str_replace("|", "", $a9);
		$a10 = str_replace("|", "", $a10);

		$a1 = trim($a1);
		$a2 = trim($a2);
		$a3 = trim($a3);
		$a4 = trim($a4);
		$a5 = trim($a5);
		$a6 = trim($a6);
		$a7 = trim($a7);
		$a8 = trim($a8);
		$a9 = trim($a9);
		$a10 = trim($a10);

		if ($a1 != "") $antworten = $a1;
		if ($a2 != "") $antworten = "$antworten|$a2";
		if ($a3 != "") $antworten = "$antworten|$a3";
		if ($a4 != "") $antworten = "$antworten|$a4";
		if ($a5 != "") $antworten = "$antworten|$a5";
		if ($a6 != "") $antworten = "$antworten|$a6";
		if ($a7 != "") $antworten = "$antworten|$a7";
		if ($a8 != "") $antworten = "$antworten|$a8";
		if ($a9 != "") $antworten = "$antworten|$a9";
		if ($a10 != "") $antworten = "$antworten|$a10";

		mysqli_execute_query(
			$GLOBALS['dbi'],
			"UPDATE de_vote_umfragen SET frage=?, antworten=?, hinweis=? WHERE id=?",
			[$frage, $antworten, $hinweis, $id]
		);

		echo "<h2>Umfrage editiert</h2>";

		$action = 'edit';
	}

	if (isset($_REQUEST['addant'])) {
		$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=?", [$id]);
		$row = mysqli_fetch_assoc($db_umfrage);

		$antworten = $row['antworten'] . '|' . $wantwort;

		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_vote_umfragen SET antworten=? WHERE id=?", [$antworten, $id]);
		echo "<h2>weitere Antwort erfolgreich hinzugef&uuml;gt</h2>";

		$action = 'edit';
	}

	if (isset($_REQUEST['delvote'])) {
		mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_vote_umfragen WHERE id=?", [$id]);
		echo "<h2>Umfrage erfolgreich gel&ouml;scht</h2>";
	}




	?>
	<br><br>
	<h1>Umfragen die auf die Aktivierung warten</h1>
	<table border="0" cellpadding="0" cellspacing="0" width="500">
		<tr height="37" align="center">
			<td class="rol">&nbsp;</td>
			<td class="ro" width="30">Nr.</td>
			<td class="ro">Frage</td>
			<td class="ro" width="80">Status</td>
			<td class="ror">&nbsp;</td>
		</tr>

		<?php
		$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=0 ORDER BY id");
		while ($row = mysqli_fetch_assoc($db_umfrage)) {
			echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
			echo '<td>' . $row['id'] . '</td><td><a href="umfragen.php?action=edit&id=' . $row['id'] . '">' . $row['frage'] . '</a></td><td>';
			echo '<a href="umfragen.php?action=ak&id=' . $row['id'] . '" onclick="return confirm(\'M&ouml;chtes du diese Umfrage wirklich aktivieren?\')"><font color="#FF8000">inaktiv</font></a> </td><td class="rr" width="13">&nbsp;</td></tr>';
		}
		?>


		<tr height="20">
			<td class="rul" width="13">&nbsp;</td>
			<td class="ru" colspan="3">&nbsp;</td>
			<td class="rur" width="13">&nbsp;</td>
		</tr>
	</table>
	<br><br><br>
	<h1>Offene Umfragen</h1>
	<table border="0" cellpadding="0" cellspacing="0" width="500">
		<tr height="37" align="center">
			<td class="rol">&nbsp;</td>
			<td class="ro" width="30">Nr.</td>
			<td class="ro">Frage</td>
			<td class="ro" width="80">Status</td>
			<td class="ror">&nbsp;</td>
		</tr>

		<?php
		$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=1 ORDER BY id DESC");
		while ($row = mysqli_fetch_assoc($db_umfrage)) {
			echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
			echo '<td>' . $row['id'] . '</td><td><a href="umfragen.php?action=tendenz&id=' . $row['id'] . '">' . $row['frage'] . '</a></td><td>';
			echo '<a href="umfragen.php?action=deak&id=' . $row['id'] . '" onclick="return confirm(\'M&ouml;chtes du diese Umfrage wirklich beenden?\')"><font color="#00FF00">offen</font></a> </td><td class="rr" width="13">&nbsp;</td></tr>';
		}
		?>


		<tr height="20">
			<td class="rul" width="13">&nbsp;</td>
			<td class="ru" colspan="3">&nbsp;</td>
			<td class="rur" width="13">&nbsp;</td>
		</tr>
	</table>
	<br><br><br>
	<h1>Geschlossene Umfragen</h1>
	<table border="0" cellpadding="0" cellspacing="0" width="500">
		<tr height="37" align="center">
			<td class="rol">&nbsp;</td>
			<td class="ro" width="30">Nr.</td>
			<td class="ro">Frage</td>
			<td class="ro" width="80">Status</td>
			<td class="ror">&nbsp;</td>
		</tr>

		<?php
		$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=2 ORDER BY id DESC");
		while ($row = mysqli_fetch_assoc($db_umfrage)) {
			echo '<tr align="center"><td class="rl" width="13">&nbsp;</td>';
			echo '<td>' . $row['id'] . '</td><td><a href="umfragen.php?action=show&id=' . $row['id'] . '">' . $row['frage'] . '</a></td><td>';
			echo '<font color="#FF0000">geschlossen</font></td><td class="rr" width="13">&nbsp;</td></tr>';
		}
		?>


		<tr height="20">
			<td class="rul" width="13">&nbsp;</td>
			<td class="ru" colspan="3">&nbsp;</td>
			<td class="rur" width="13">&nbsp;</td>
		</tr>
	</table>
	<br><br><br>


	<?php
	if ($action == "edit") {
		$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, hinweis, antworten, status FROM de_vote_umfragen WHERE id=?", [$id]);
		$row = mysqli_fetch_assoc($db_umfrage);

	?>
		<form action="umfragen.php" methode="post">
			<table border="1" width="600px" bordercolor="#3399FF">
				<tr>
					<td colspan="2" class="fett" align="center">Umfrage <?php if ($row['status'] == 0) echo 'editieren';
																		else echo 'nicht editierbar';  ?></td>
				</tr>
				<tr>
					<td>Frage:</td>
					<td><input type="text" name="frage" size="48" value="<?php echo $row['frage'] . '"';
																			if ($row['status'] != 0) echo 'disabled'; ?>></td>
	</tr>
	<tr>
	<td valign=" top">Hinweis:</td>
					<td><textarea name="hinweis" cols="70" rows="20" <?php if ($row['status'] != 0) echo 'disabled'; ?>><?php echo $row['hinweis']; ?></textarea></td>
				</tr>
				<?php
				$i = 0;

				$antworten = explode("|", $row['antworten']);

				while ($i < count($antworten)) {
					$zaehler = $i + 1;
					echo '<tr align="center"><td>' . $zaehler . '</td><td><input type="text" name="a' . $zaehler . '" value="' . $antworten[$i] . '" size="48"';
					if ($row['status'] != 0) {
						echo "disabled";
					}
					echo '></td></tr>';
					$i++;
				}
				?>
				<tr>
					<td colspan="2" align="center"><input type="submit" <?php if ($row['status'] != 0) echo 'disabled'; ?> class="buttons" name="subedit" value="&Auml;nderungen speichern"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</form>
		<form action="umfragen.php?action=edit" methode="post">
			<?php
			if (count($antworten) < 10 && $row['status'] == 0) {
			?>

				<table border="1" width="325" bordercolor="#3399FF">
					<tr>
						<td colspan="2" class="fett" align="center">Antwort hinzuf&uuml;gen</td>
					</tr>
					<tr>
						<td>Antwort:</td>
						<td><input type="text" name="wantwort" size="48"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" class="buttons" name="addant" value="&Auml;nderungen speichern"></td>
					</tr>
				</table>
				<br><br>

			<?php
			}
			?>
			<table border="1" width="325" bordercolor="#3399FF">
				<tr>
					<td class="fett" align="center">Umfrage l&ouml;schen?</td>
				</tr>
				<tr>
					<td align="center"><input type="submit" class="buttons" name="delvote" value="Umfrage l&ouml;schen" onclick="return confirm('M&ouml;chtes du diese Umfrage wirklich l&ouml;schen?')"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</form>
	<?php
	} elseif(isset($_REQUEST['anzant'])) {
	?>
		<form action="umfragen.php" methode="post">
			<table border="1" width="325" bordercolor="#3399FF">
				<tr>
					<td colspan="2" class="fett" align="center">Umfrage erstellen</td>
				</tr>
				<tr>
					<td>Frage:</td>
					<td><input type="text" name="frage" size="48"></td>
				</tr>
				<tr>
					<td valign="top">Hinweis:</td>
					<td><textarea name="hinweis" cols="30" rows="8"></textarea></td>
				</tr>
				<?php
				$i = 0;



				while ($i < $anzahlantwort) {
					$zaehler = $i + 1;
					echo '<tr align="center"><td>' . $zaehler . '</td><td><input type="text" name="a' . $zaehler . '" size="48"></td></tr>';
					$i++;
				}
				?>
				<tr>
					<td colspan="2" align="center"><input type="submit" class="buttons" name="subentry" value="Umfrage eintragen"></td>
				</tr>
			</table>

		</form>
	<?php
	} else {
	?>
		<form action="umfragen.php" methode="post">
			<table border="1" width="325" bordercolor="#3399FF">
				<tr>
					<td class="fett" align="center">Anzahl der Antworten f&uuml;r eine neue Umfrage</td>
				</tr>
				<tr>
					<td align="center">Antworten:&nbsp;&nbsp;
						<select name="anzahlantwort" size="1">
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="center"><input type="submit" class="buttons" name="anzant" value="Umfragemaske laden"></td>
				</tr>
			</table>
		</form>
		<?php
	}


	if ($action == "show") {
		$db_checkobende = mysqli_execute_query($GLOBALS['dbi'], "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?", [$id]);

		if (mysqli_num_rows($db_checkobende) > 0) {
			$row = mysqli_fetch_assoc($db_checkobende);
		?>
			<br><br><br><br>
			<table border="0" cellpadding="0" cellspacing="0" width="600">
				<tr height="37" align="center">
					<td class="rol">&nbsp;</td>
					<td class="ro" colspan="1"><?php echo $row['frage']; ?></td>
					<td class="ror">&nbsp;</td>
				</tr>
				<tr height="20">
					<td class="rl" width="13">&nbsp;</td>
					<td>


						<table border="0" width="100%" cellspacing="2" cellpadding="0">

							<tr height="20">
								<td class="cell" cellspacing="2">&nbsp;Start: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['startdatum']); ?></td>
								<td class="cell" cellspacing="2" align="center">Es haben

									<?php
									//aktive Spieler auslesen
									$db_spieleranz = mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(user_id) AS anzahl FROM de_user_data WHERE npc=0 AND sector>1");
									$rows = mysqli_fetch_assoc($db_spieleranz);
									$spieler_anz = $rows['anzahl'];

									//Prozentwert berechnen
									$stimmen = explode("|", $row['stimmen']);
									echo number_format(($stimmen[0] * 100) / $spieler_anz, 2, ",", ".") . "%";
									?>
									der Spieler an der Umfrage teilgenommen!</td>
							</tr>

							<tr height="25">
								<td class="cell">&nbsp;Ende: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['enddatum']); ?></td>
								<td cellspacing="2" class="cell">&nbsp;</td>
							</tr>
							<tr height="25">
								<td colspan="4" class="cell">Hinweis: <?php echo nl2br($row['hinweis']); ?></td>
							</tr>
							<tr>
								<td colspan="4">
									<table border="0" width="100%" cellspacing="2" cellpadding="0">
										<?php

										$antworten = explode("|", $row['antworten']);
										$ergebnisse = explode("|", $row['ergebnisse']);
										$i = 0;
										$farbe = 0;
										while ($i < count($antworten)) {
											echo "
						<tr  class=\"cell\">
						<td height=\"25\">&nbsp;" . $antworten[$i] . "</td><td>&nbsp;";
											$prozente = number_format(($ergebnisse[$i] * 100) / $stimmen[0], 2, ",", ".");
										?>
											<img src="g/vote/l<?php echo $farbe; ?>.gif" border="0"><img src="g/vote/m<?php echo $farbe; ?>.gif" border="0" width="<?php echo $prozente; ?>" height="9"><img src="g/vote/r<?php echo $farbe; ?>.gif">
										<?php
											echo "</td><td width=\"40\" nowrap>&nbsp;$ergebnisse[$i]</td><td width=\"50\" nowrap>";



											echo "&nbsp;" . $prozente . "%</td></tr>";

											$stimmengesamt = $stimmengesamt + $ergebnisse[$i];

											$prozentegesamt = $prozentegesamt + (($ergebnisse[$i] * 100) / $stimmen[0]);

											if ($farbe == 0) {
												$farbe = 1;
											} else {
												$farbe = 0;
											}

											$i++;
										}

										echo '<tr class="cell"  height="25"><td colspan="2" align="right"><b>Insgesamt:</b>&nbsp;&nbsp;</td><td>&nbsp;' . $stimmengesamt . '</td><td>&nbsp;' . $prozentegesamt . '%</td></tr>';

										?>
									</table>
								</td>
							</tr>

						</table>


					</td>
					<td class="rr" width="13">&nbsp;</td>
				</tr>
				<tr height="20">
					<td class="rul" width="13">&nbsp;</td>
					<td class="ru" colspan="1">&nbsp;</td>
					<td class="rur" width="13">&nbsp;</td>
				</tr>
			</table>
		<?php
		}
	} // Ende des if($action=="show") Blocks


	if ($action == "tendenz") { // Start des tendenz-Blocks
		$db_vorabdaten = mysqli_execute_query($GLOBALS['dbi'], "SELECT frage, antworten, hinweis, stimmen, status, startdatum, ergebnisse FROM de_vote_umfragen WHERE status=1 AND id=?", [$id]);
		$row = mysqli_fetch_assoc($db_vorabdaten);


		$vorabstimmen = array();

		$counter = 0;
		$db_stimmen = mysqli_execute_query($GLOBALS['dbi'], "SELECT COUNT(votefor) AS count, votefor FROM de_vote_stimmen WHERE vote_id=? GROUP BY votefor", [$id]);
		while ($stimmen = mysqli_fetch_assoc($db_stimmen)) {
			$vorabstimmen[$counter] = $stimmen['count'];
			$counter++;
		} // Ende der while($stimmen=mysqli_fetch_assoc($db_stimmen)) Schleife



		?>
		<br><br><br><br>
		<table border="0" cellpadding="0" cellspacing="0" width="600">
			<tr height="37" align="center">
				<td class="rol">&nbsp;</td>
				<td class="ro" colspan="1"><?php echo $row['frage']; ?></td>
				<td class="ror">&nbsp;</td>
			</tr>
			<tr height="20">
				<td class="rl" width="13">&nbsp;</td>
				<td>


					<table border="0" width="100%" cellspacing="2" cellpadding="0">

						<tr height="20">
							<td class="cell" cellspacing="2">&nbsp;Start: <?php echo str_replace(" ", "&nbsp;&nbsp;/&nbsp;&nbsp;", $row['startdatum']); ?></td>
							<td class="cell" cellspacing="2" align="center">Es haben bis jetzt

								<?php
								$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE npc=0 AND sector>1");
								$gesamtuser = mysqli_num_rows($db_daten);


								$anzahlantworten = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=?", [$id]);
								$riw = mysqli_fetch_assoc($anzahlantworten);
								$antworten = explode("|", $riw['antworten']);


								$temp = 0;
								$abgegebenestimmen = 0;
								while ($temp < (count($vorabstimmen))) {
									$abgegebenestimmen = $abgegebenestimmen + $vorabstimmen[$temp];
									$temp++;
								} // Ende der while($temp<(count($vorabstimmen))) Schleife
								echo number_format((($abgegebenestimmen - count($antworten)) * 100) / $gesamtuser, 2, ",", ".") . "%";
								?>
								der Spieler (<?php echo $gesamtuser;?>) an der Umfrage teilgenommen!</td>
						</tr>

						<tr height="25">
							<td colspan="4" class="cell">&nbsp;Hinweis: <?php echo nl2br($row['hinweis']); ?></td>
						</tr>
						<tr>
							<td colspan="4">
								<table border="0" width="100%" cellspacing="2" cellpadding="0">
									<?php

									$antworten = explode("|", $row['antworten']);

									$i = 0;
									$farbe = 0;
									while ($i < count($antworten)) {
										echo "
		<tr  class=\"cell\">
		<td height=\"25\">&nbsp;" . $antworten[$i] . "</td><td>&nbsp;";
										$prozente = number_format((($vorabstimmen[$i] - 1) * 100) / ($abgegebenestimmen - count($antworten)), 2, ",", ".");
									?>
										<img src="../g/vote/l<?php echo $farbe; ?>.gif" border="0"><img src="../g/vote/m<?php echo $farbe; ?>.gif" border="0" width="<?php echo $prozente; ?>" height="9"><img src="../g/vote/r<?php echo $farbe; ?>.gif">
									<?php
										echo '</td><td width="40" nowrap>&nbsp;' . ($vorabstimmen[$i] - 1) . '</td><td width="50" nowrap>';



										echo "&nbsp;" . $prozente . "%</td></tr>";

										$stimmengesamt = $stimmengesamt + $vorabstimmen[$i];

										$prozentegesamt = $prozentegesamt + (($vorabstimmen[$i] * 100) / $abgegebenestimmen);

										if ($farbe == 0) {
											$farbe = 1;
										} else {
											$farbe = 0;
										}

										$i = $i + 1;
									} // Ende der while($i<count($antworten)) Schleife

									echo '<tr class="cell"  height="25"><td colspan="2" align="right"><b>Insgesamt:</b>&nbsp;&nbsp;</td><td>&nbsp;' . ($stimmengesamt - count($antworten)) . '</td><td>&nbsp;' . $prozentegesamt . '%</td></tr>';

									?>
								</table>
							</td>
						</tr>

					</table>


				</td>
				<td class="rr" width="13">&nbsp;</td>
			</tr>
			<tr height="20">
				<td class="rul" width="13">&nbsp;</td>
				<td class="ru" colspan="1">&nbsp;</td>
				<td class="rur" width="13">&nbsp;</td>
			</tr>
		</table>
	<?php
	}
	?>

</body>

</html>