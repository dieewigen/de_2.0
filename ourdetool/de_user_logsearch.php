<?php
require 'det_userdata.inc.php';
require '../inc/sv.inc.php';
require '../inc/env.inc.php';

$uid = intval($_REQUEST["uid"]);
$searchtext = isset($_REQUEST['searchtext']) ? $_REQUEST['searchtext'] : '';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Logsuche</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php 

echo '<form action="de_user_logsearch.php?uid='.$uid.'" method="POST">';
echo '<br> Da die Logdatenbank sehr groß ist, kann die Suche sehr lange dauern. Einfach den gesuchten Text eingeben und mit Return/Enter best&auml;tigen.';

echo '<br>Suchtext: <input type="text" name="searchtext" value="'.$searchtext.'">';

echo '</form>';

if($searchtext)
{
	$dblog = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");

	
	// Tabelle für die Ausgabe erstellen
	echo '<table>';
	echo '<tr><td>Zeit</td><td>IP</td><td>Datei</td><td>getpost</td></tr>';
	
	// Suchtext mit Prepared Statement
	$searchtext = '%' . $_REQUEST['searchtext'] . '%'; // LIKE-Pattern erstellen
	$query = "SELECT * FROM gameserverlogdata WHERE userid=? AND serverid=? AND getpost LIKE ?";
	$result = mysqli_execute_query($dblog, $query, [$uid, $sv_servid, $searchtext]);
	
	// Anzahl der gefundenen Zeilen ermitteln
	$num = mysqli_num_rows($result);
	
	// Durch die Ergebnisse iterieren
	while($row = mysqli_fetch_assoc($result))
	{
		echo '<tr>';
		echo '<td>'.$row['time'].'</td>';
		echo '<td>'.$row['ip'].'</td>';
		echo '<td>'.$row['file'].'.php</td>';
		echo '<td>'.$row['getpost'].'</td>';
		echo '</tr>';
	}	
	echo '</table>';
	
	echo '<br>Gefundene Datens&auml;tze: '.$num;
	
}


?>
</body>
</html>