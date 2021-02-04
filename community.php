<?php
include "inc/header.inc.php";
//include 'inc/lang/'.$sv_server_lang.'_community.lang.php';
include "functions.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, allytag, status FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row["restyp05"];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
if ($row["status"]==1) $ownally = $row["allytag"];

//irc-name aus der db auslesen
$db_daten=mysql_query("SELECT ircname FROM de_user_info WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$db_ircname=$row["ircname"];


?>
<html>
<head>
<title>Community</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?php //stelle die ressourcenleiste dar
include "resline.php";

echo '<table border="0" cellpadding="5" cellspacing="0" width="600">';
echo '<tr align="center" class="cell">';
//echo '<td><a href="irc/irc.php" target="_blank" class="btn">IRC</a></td>';
echo '<td><a href="https://discord.gg/qBpCPx4" target="_blank" class="btn">DE-Discord</a></td>';
echo '<td><a href="vote.php?bar=yes" class="btn">Umfragen</a></td>';
//echo '<td><a href="dezindex.php" target="h" class="btn">DE-Zeitung</a></td>';
echo '</tr>';
echo '<tr align="center" class="cell">';
echo '<td><a href="http://forum.bgam.es" target="_blank" class="btn">Forum</a></td>';
echo '<td><a href="http://login.bgam.es/index.php?command=forum" target="_blank" class="btn">Forum/HA</a></td>';
//echo '<td>&nbsp;</td>';
echo '</tr>';

echo '</table>';
echo '<br>';

?>
</div>
<br>
<?php include "fooban.php"; ?>
</body>
</html>
