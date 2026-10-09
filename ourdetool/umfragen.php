<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Umfragen';
$active_nav = 'umfragen';
include "inc.layout.top.php";

$stimmengesamt = 0;
$prozentegesamt = 0;

$action = req_str('action');
$id = req_int('id');
$frage = trim(req_str('frage'));
$hinweis = req_str('hinweis');
$wantwort = trim(str_replace("|", "", req_str('wantwort')));
$anzahlantwort = req_int('anzahlantwort');

// Antwortfelder a1..a10 einsammeln: Trenner "|" entfernen, leere Felder ueberspringen
$antwortliste = array();
for ($n = 1; $n <= 10; $n++) {
	$a = trim(str_replace("|", "", req_str('a' . $n)));
	if ($a !== '') {
		$antwortliste[] = $a;
	}
}
$antworten_neu = implode('|', $antwortliste);

if ($action == "ak") {
	csrf_require();
	$time = date("Y-m-d H:i:s");
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_vote_umfragen SET status = 1, startdatum=? WHERE id=?", [$time, $id]);

	$anzahlantworten = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=?", [$id]);
	$riw = mysqli_fetch_assoc($anzahlantworten);

	$antworten = explode("|", $riw['antworten']);
	$i = 1;
	while ($i <= count($antworten)) {
		mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_vote_stimmen (user_id, vote_id, votefor) VALUES (?, ?, ?)", [0, $id, $i]);
		$i++;
	}
	echo '<div class="flash flash-ok">Umfrage aktiviert, sie ist jetzt im Spiel sichtbar.</div>';
}

if ($action == "deak") {
	csrf_require();
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
	$db_ergebnisse = '';
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

	$time = date("Y-m-d H:i:s");

	mysqli_execute_query(
		$GLOBALS['dbi'],
		"UPDATE de_vote_umfragen SET status=2, enddatum=?, ergebnisse=?, stimmen=? WHERE id=?",
		[$time, $db_ergebnisse, $stimmen, $id]
	);
	echo '<div class="flash flash-ok">Umfrage beendet: ' . (int)$abgegebenestimmen . ' von ' . (int)$gesamtuser . ' Spielern haben abgestimmt.</div>';
}

if (isset($_REQUEST['subentry'])) {
	csrf_require();
	if ($frage === '' || trim($hinweis) === '' || count($antwortliste) < 2) {
		echo '<div class="flash flash-danger">Umfrage nicht gespeichert: Frage, Beschreibung und mindestens zwei Antworten sind Pflicht.</div>';
	} else {
		mysqli_execute_query(
			$GLOBALS['dbi'],
			"INSERT INTO de_vote_umfragen(frage, antworten, hinweis, status) VALUES (?, ?, ?, 0)",
			[$frage, $antworten_neu, $hinweis]
		);
		echo '<div class="flash flash-ok">Umfrage erfolgreich erstellt. Sie wartet unten auf die Aktivierung.</div>';
	}
}

if (isset($_REQUEST['subedit'])) {
	csrf_require();
	if ($frage === '' || trim($hinweis) === '' || count($antwortliste) < 2) {
		echo '<div class="flash flash-danger">&Auml;nderungen nicht gespeichert: Frage, Beschreibung und mindestens zwei Antworten sind Pflicht.</div>';
	} else {
		mysqli_execute_query(
			$GLOBALS['dbi'],
			"UPDATE de_vote_umfragen SET frage=?, antworten=?, hinweis=? WHERE id=? AND status=0",
			[$frage, $antworten_neu, $hinweis, $id]
		);
		echo '<div class="flash flash-ok">Umfrage editiert.</div>';
	}
	$action = 'edit';
}

if (isset($_REQUEST['addant'])) {
	csrf_require();
	$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT antworten FROM de_vote_umfragen WHERE id=? AND status=0", [$id]);
	$row = mysqli_fetch_assoc($db_umfrage);

	if ($row === null || $wantwort === '') {
		echo '<div class="flash flash-danger">Keine Antwort hinzugef&uuml;gt.</div>';
	} elseif (count(explode("|", $row['antworten'])) >= 10) {
		echo '<div class="flash flash-danger">Es sind h&ouml;chstens zehn Antworten m&ouml;glich.</div>';
	} else {
		$antworten = $row['antworten'] . '|' . $wantwort;
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_vote_umfragen SET antworten=? WHERE id=?", [$antworten, $id]);
		echo '<div class="flash flash-ok">Weitere Antwort erfolgreich hinzugef&uuml;gt.</div>';
	}

	$action = 'edit';
}

if (isset($_REQUEST['delvote'])) {
	csrf_require();
	mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_vote_umfragen WHERE id=?", [$id]);
	echo '<div class="flash flash-ok">Umfrage erfolgreich gel&ouml;scht.</div>';
}

/* ---------- Umfrage anlegen / bearbeiten (oben, damit man gleich loslegen kann) ---------- */

$row = null;
if ($action == "edit") {
	$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, hinweis, antworten, status FROM de_vote_umfragen WHERE id=?", [$id]);
	$row = mysqli_fetch_assoc($db_umfrage);
}

if ($action == "edit" && $row) {
	$gesperrt = ($row['status'] != 0) ? ' disabled' : '';
	$antworten = explode("|", $row['antworten']);
?>
	<form action="umfragen.php" method="post" class="umf-form">
		<?= csrf_field() ?>
		<table>
			<tr>
				<th colspan="2">Umfrage #<?= (int)$row['id'] ?> <?= $row['status'] == 0 ? 'bearbeiten' : 'ansehen (nach dem Start nicht mehr editierbar)' ?></th>
			</tr>
			<tr>
				<td>Frage</td>
				<td><input type="text" name="frage" maxlength="75" required value="<?= htmlspecialchars($row['frage']) ?>"<?= $gesperrt ?>>
					<div class="dim">H&ouml;chstens 75 Zeichen, erscheint als &Uuml;berschrift der Umfrage.</div></td>
			</tr>
			<tr>
				<td>Beschreibung</td>
				<td><textarea name="hinweis" rows="14" required<?= $gesperrt ?>><?= htmlspecialchars($row['hinweis']) ?></textarea>
					<div class="dim">Hinweistext unter der Frage, Zeilenumbr&uuml;che bleiben erhalten.</div></td>
			</tr>
			<?php
			foreach ($antworten as $i => $antwort) {
				$zaehler = $i + 1;
				echo '<tr><td>Antwort ' . $zaehler . '</td><td><input type="text" name="a' . $zaehler . '" value="' . htmlspecialchars($antwort) . '"' . $gesperrt . '></td></tr>';
			}
			?>
			<tr>
				<td colspan="2"><input type="submit" name="subedit" value="&Auml;nderungen speichern"<?= $gesperrt ?>></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?= (int)$id ?>">
	</form>
	<?php
	if (count($antworten) < 10 && $row['status'] == 0) {
	?>
	<form action="umfragen.php" method="post" class="umf-form">
		<?= csrf_field() ?>
		<table>
			<tr>
				<th colspan="2">Antwort hinzuf&uuml;gen</th>
			</tr>
			<tr>
				<td>Antwort <?= count($antworten) + 1 ?></td>
				<td><input type="text" name="wantwort" required></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="addant" value="Antwort hinzuf&uuml;gen"></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?= (int)$id ?>">
	</form>
	<?php
	}
	?>
	<form action="umfragen.php" method="post" data-confirm="M&ouml;chtest du diese Umfrage wirklich l&ouml;schen?">
		<?= csrf_field() ?>
		<input type="submit" class="btn-danger" name="delvote" value="Umfrage l&ouml;schen">
		<input type="hidden" name="id" value="<?= (int)$id ?>">
	</form>
	<p><a href="umfragen.php">Neue Umfrage anlegen</a></p>
<?php
} elseif (isset($_REQUEST['anzant']) && $anzahlantwort >= 2) {
	if ($anzahlantwort > 10) {
		$anzahlantwort = 10;
	}
?>
	<form action="umfragen.php" method="post" class="umf-form">
		<?= csrf_field() ?>
		<table>
			<tr>
				<th colspan="2">Umfrage erstellen</th>
			</tr>
			<tr>
				<td>Frage</td>
				<td><input type="text" name="frage" maxlength="75" required autofocus>
					<div class="dim">H&ouml;chstens 75 Zeichen, erscheint als &Uuml;berschrift der Umfrage.</div></td>
			</tr>
			<tr>
				<td>Beschreibung</td>
				<td><textarea name="hinweis" rows="14" required></textarea>
					<div class="dim">Hinweistext unter der Frage, Zeilenumbr&uuml;che bleiben erhalten. Das Zeichen | ist in Antworten nicht erlaubt.</div></td>
			</tr>
			<?php
			for ($zaehler = 1; $zaehler <= $anzahlantwort; $zaehler++) {
				echo '<tr><td>Antwort ' . $zaehler . '</td><td><input type="text" name="a' . $zaehler . '"' . ($zaehler <= 2 ? ' required' : '') . '></td></tr>';
			}
			?>
			<tr>
				<td colspan="2"><input type="submit" name="subentry" value="Umfrage eintragen"> <a href="umfragen.php">Abbrechen</a></td>
			</tr>
		</table>
	</form>
<?php
} else {
?>
	<form action="umfragen.php" method="post" class="umf-form">
		<table>
			<tr>
				<th>Neue Umfrage anlegen</th>
			</tr>
			<tr>
				<td>Anzahl der Antworten:
					<select name="anzahlantwort" size="1">
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4" selected>4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
					<input type="submit" name="anzant" value="Umfragemaske laden">
					<span class="dim">Weitere Antworten lassen sich sp&auml;ter beim Bearbeiten erg&auml;nzen.</span>
				</td>
			</tr>
		</table>
	</form>
<?php
}

/* ---------- Listen ---------- */
?>
<h2>Umfragen die auf die Aktivierung warten</h2>
<table>
	<tr>
		<th class="num">Nr.</th>
		<th>Frage</th>
		<th>Status</th>
	</tr>

	<?php
	$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=0 ORDER BY id");
	while ($row = mysqli_fetch_assoc($db_umfrage)) {
		echo '<tr>';
		echo '<td class="num">' . $row['id'] . '</td><td><a href="umfragen.php?action=edit&id=' . $row['id'] . '">' . htmlspecialchars($row['frage']) . '</a></td><td>';
		echo '<a href="' . csrf_url('umfragen.php?action=ak&id=' . $row['id']) . '" data-confirm="M&ouml;chtest du diese Umfrage wirklich aktivieren?"><span class="badge badge-warn">inaktiv</span></a></td></tr>';
	}
	?>

</table>
<h2>Offene Umfragen</h2>
<table>
	<tr>
		<th class="num">Nr.</th>
		<th>Frage</th>
		<th>Status</th>
	</tr>

	<?php
	$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=1 ORDER BY id DESC");
	while ($row = mysqli_fetch_assoc($db_umfrage)) {
		echo '<tr>';
		echo '<td class="num">' . $row['id'] . '</td><td><a href="umfragen.php?action=tendenz&id=' . $row['id'] . '">' . htmlspecialchars($row['frage']) . '</a></td><td>';
		echo '<a href="' . csrf_url('umfragen.php?action=deak&id=' . $row['id']) . '" data-confirm="M&ouml;chtest du diese Umfrage wirklich beenden?"><span class="badge badge-ok">offen</span></a></td></tr>';
	}
	?>

</table>
<h2>Geschlossene Umfragen</h2>
<table>
	<tr>
		<th class="num">Nr.</th>
		<th>Frage</th>
		<th>Status</th>
	</tr>

	<?php
	$db_umfrage = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, frage, status FROM de_vote_umfragen WHERE status=2 ORDER BY id DESC");
	while ($row = mysqli_fetch_assoc($db_umfrage)) {
		echo '<tr>';
		echo '<td class="num">' . $row['id'] . '</td><td><a href="umfragen.php?action=show&id=' . $row['id'] . '">' . htmlspecialchars($row['frage']) . '</a></td><td>';
		echo '<span class="badge badge-danger">geschlossen</span></td></tr>';
	}
	?>

</table>

<?php
if ($action == "show") {
	$db_checkobende = mysqli_execute_query($GLOBALS['dbi'], "SELECT frage, antworten, hinweis, stimmen, status, startdatum, enddatum, ergebnisse FROM de_vote_umfragen WHERE status=2 AND id=?", [$id]);

	if (mysqli_num_rows($db_checkobende) > 0) {
		$row = mysqli_fetch_assoc($db_checkobende);
	?>
		<br><br>
		<table border="0" cellpadding="0" cellspacing="0" width="600">
			<tr height="37" align="center">
				<th colspan="1"><?php echo $row['frage']; ?></th>
			</tr>
			<tr height="20">
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
	<br><br>
	<table border="0" cellpadding="0" cellspacing="0" width="600">
		<tr height="37" align="center">
			<th colspan="1"><?php echo $row['frage']; ?></th>
		</tr>
		<tr height="20">
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
							der Spieler (<?php echo $gesamtuser; ?>) an der Umfrage teilgenommen!</td>
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
		</tr>
	</table>
<?php
}

include "inc.layout.bottom.php";
