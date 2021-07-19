<?php
require 'det_userdata.inc.php';
require '../inc/sv.inc.php';

$uid = intval($_REQUEST["uid"]);
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

echo '<br>Suchtext: <input type="text" name="searchtext" value="'.$_REQUEST['searchtext'].'">';

echo '</form>';

if($_REQUEST['searchtext'])
{
	//db-verbindung aufbauen
	$targetdata = array('localhost', 'dbuser', 'c0j9XIrL5Rwm', 'gameserverlogdata', 'gameserverlogdata');
	$dblog = @mysql_connect($targetdata[0], $targetdata[1], $targetdata[2]);
	if (!$dblog) 
	{
  		echo 'keine Verbindung möglich: ' . mysql_error();
	}
	mysql_select_db($targetdata[3], $dblog);
	
	echo '<table>';
	echo '<tr><td>Zeit</td><td>IP</td><td>Datei</td><td>getpost</td></tr>';
	//gewünschten text suchen
	$db_daten=mysql_query("SELECT *  FROM ".$targetdata[4]." WHERE userid='$uid' AND serverid='$sv_servid' AND getpost LIKE '%".mysql_real_escape_string($_REQUEST['searchtext'])."%';",$dblog);
	$num = mysql_num_rows($db_daten);
	while($row = mysql_fetch_array($db_daten))
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