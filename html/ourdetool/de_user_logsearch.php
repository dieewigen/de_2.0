<?php
include "../inccon.php";
include "../inc/sv.inc.php";
include "det_userdata.inc.php";

$uid = req_int('uid');
$searchtext = req_str('searchtext');

$page_title = 'Logsuche';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

echo '<form action="de_user_logsearch.php?uid=' . $uid . '" method="POST">';
echo '<br> Da die Logdatenbank sehr groß ist, kann die Suche sehr lange dauern. Einfach den gesuchten Text eingeben und mit Return/Enter best&auml;tigen.';
echo '<br>Suchtext: <input type="text" name="searchtext" value="' . htmlspecialchars($searchtext) . '">';
echo '</form>';

if ($searchtext)
{
	// Logging-DB (gameserverlogdata) zusätzlich zur Hauptverbindung aus inccon.php
	$dblog = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");
	$dblog->set_charset("utf8mb4");

	// Tabelle für die Ausgabe erstellen
	echo '<table>';
	echo '<tr><th>Zeit</th><th>IP</th><th>Datei</th><th>getpost</th></tr>';

	// Suchtext mit Prepared Statement
	$searchpattern = '%' . $searchtext . '%'; // LIKE-Pattern erstellen
	$query = "SELECT * FROM gameserverlogdata WHERE userid=? AND serverid=? AND getpost LIKE ?";
	$result = mysqli_execute_query($dblog, $query, [$uid, $GLOBALS['sv_servid'], $searchpattern]);

	// Anzahl der gefundenen Zeilen ermitteln
	$num = mysqli_num_rows($result);

	// Durch die Ergebnisse iterieren
	while ($row = mysqli_fetch_assoc($result))
	{
		echo '<tr>';
		echo '<td>' . htmlspecialchars((string)$row['time']) . '</td>';
		echo '<td>' . htmlspecialchars((string)$row['ip']) . '</td>';
		echo '<td>' . htmlspecialchars((string)$row['file']) . '.php</td>';
		echo '<td>' . htmlspecialchars((string)$row['getpost']) . '</td>';
		echo '</tr>';
	}
	echo '</table>';

	echo '<br>Gefundene Datens&auml;tze: ' . $num;
}

include "inc.layout.bottom.php";
