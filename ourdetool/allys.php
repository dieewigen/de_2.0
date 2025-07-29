<?php
include "../inccon.php";
include '../functions.php';

function getmicrotime(){ 
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
} 

$time_start = getmicrotime();

$Tab1 = ""; $Tab2 = "";
$FCounter = array(0, 0); // Initialisierung des Zähler-Arrays


$DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
		or die ("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

while($AData = mysqli_fetch_assoc($DBData)) {
	//Anzahl der Bündnisse
	$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_ally_partner.ally_id_1) AS count_allies FROM de_ally_partner WHERE de_ally_partner.ally_id_1=? OR de_ally_partner.ally_id_2=?", [$AData["id"], $AData["id"]]);
	$row_count = mysqli_fetch_assoc($result);
	$iAnz = $row_count['count_allies'];
	$allytag=$AData["allytag"];
	
	//Mitgliederanzahl
	$member_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_user_data.allytag) AS member_count FROM de_user_data WHERE allytag=? AND status=1", [$allytag]);
	$member_row = mysqli_fetch_assoc($member_result);
	$mAnz = $member_row['member_count'];
	if ($iAnz > 2) { $sC = ' class="r"'; } else { $sC = ''; }

	//Anzahl geworbener Spieler
	$geworben=0;
	$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE allytag=? AND status=1", [$allytag]);
	while($rowx = mysqli_fetch_assoc($result)){
		$uid=$rowx['user_id'];
		$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT owner_id FROM de_login WHERE user_id=?", [$uid]);
		$row = mysqli_fetch_assoc($db_daten);
		$owner_id=intval($row["owner_id"]);
		
		$geworben+=getAnzahlGeworbeneSpielerByOwnerid($owner_id);
	}		

	//Ausgabe zusammenbauen
	$Tab2 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td".$sC.">".$iAnz."</td><td>".$mAnz."</td><td>".$geworben."</td></tr>\r\n";
	$FCounter[1]++;
	$tagliste[]=$AData["allytag"];
} 
?>

<html>
<head>
<?php include "cssinclude.php";?>
<style >
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 td.r { color: #ff0000; }
 a { color: #3399ff; text-decoration: underline }
 a:hover { color: #3399ff; text-decoration: none }
</style>
</head>
<body>
<?php
include "det_userdata.inc.php";
?>
 <b>DE-Allianzen</b> (Gefunden: <?php echo intval($FCounter[1]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Allyname</td><td>AllyTag</td><td>B&uuml;ndnisse</td><td>Mitglieder</td><td>geworben</td></tr>
<? echo $Tab2; ?>
 </table>
 <br><br>

<?php
for ($i=0;$i<count($tagliste);$i++){
	$clankuerzel=$tagliste[$i];

	echo '
	<table border="0" cellpadding="0" cellspacing="0">
	<tr align="center">
	<td width="13" height="37" class="rol">&nbsp;</td>
	<td width="800" align="center" class="ro">Mitgliederliste der Allianz: '.$tagliste[$i].'</td>
	<td width="13" class="ror">&nbsp;</td>
	</tr>
	<tr><td width="13" class="rl">&nbsp;</td><td>
	<table border="0" width="100%" cellspacing="1" cellpadding="0">';
	echo '<tr>'.
			'<td class="tc">User-ID</td>'.
			'<td class="tc">Name</td>'.
			'<td class="tc">Status</td>'.
			'<td class="tc">Letzte Aktivit&auml;t</td>'.
			'<td class="tc">Kollektoren</td>'.
			'<td class="tc">Punkte</td>'.
			'<td class="tc">Koordinaten</td>';
	echo '</tr>';

	$query = "SELECT * FROM de_user_data WHERE status='1' AND allytag=? ORDER BY sector, system";

	$result = mysqli_execute_query($GLOBALS['dbi'], $query, [$clankuerzel]);

	$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
	$nb = count($rows);

	$row = 0;


	while ($row < $nb){
		
			$userid = $rows[$row]["user_id"];
			$sector = $rows[$row]["sector"];
			$system = $rows[$row]["system"];
			$score = $rows[$row]["score"];
			$kollies = $rows[$row]["col"];
			$spielername = $rows[$row]["spielername"];
			$sectorjump = explode(":",$sector);
			$sectorjump = $sectorjump[0];

			$login_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_login WHERE user_id=?", [$userid]);
			$logindata = mysqli_fetch_assoc($login_result);
			
			echo"<tr>".
				"<td class=\"cl\"><a href=\"idinfo.php?UID=".$logindata['user_id']."\" target=\"_blank\">".$logindata['user_id']."</a></td>".
				"<td class=\"cl\">$spielername</td>".
				"<td class=\"cl\">".$logindata['status']."</td>".
				"<td class=\"cl\">".$logindata['last_click']."</td>".
				"<td class=\"cr\">$kollies</td>".
				"<td class=\"cr\">".number_format($score, 0,'','.')."</td>".
				"<td class=\"cc\" width=\"10%\">".$sector.":".$system."</td>";
			echo"</tr>";
			$row++;
	}



	echo "</table>".
			'<td width="13" class="rr">&nbsp;</td></tr>'.
			'<tr><td width="13" class="rul">&nbsp;</td>'.
			'<td width="13" class="ru">&nbsp;</td>'.
			'<td width="13" class="rur">&nbsp;</td>'.
			'</tr>'.
			'</table>';
}


$Tab1 = ""; $Tab2 = "";

 $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.* FROM de_allys LEFT JOIN de_login ON (de_allys.leaderid = de_login.user_id) WHERE (de_login.nic) Is Null ORDER BY de_allys.id")
           or die ("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

 while($AData = mysqli_fetch_assoc($DBData)) {
  $count_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_user_data.allytag) AS count_tags FROM de_user_data WHERE de_user_data.allytag=?", [$AData["allytag"]]);
  $count_row = mysqli_fetch_assoc($count_result);
  $iAnz = $count_row['count_tags'];
  if ($iAnz == 0) { $sC = ' class="r"'; } else { $sC = ''; }
  if ($AData["coleaderid1"] == -1) { $Co1 = "---"; } else { $Co1 = $AData["coleaderid1"]; }
  if ($AData["coleaderid2"] == -1) { $Co2 = "---"; } else { $Co2 = $AData["coleaderid2"]; }
  $Tab1 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td>".$Co1."</td><td>".$Co2."</td><td".$sC.">".$iAnz."</td></tr>\r\n";
  $FCounter[0]++;
 }

 $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
           or die ("Fehler beim Auslesen der Daten: " . mysqli_error($GLOBALS['dbi']));

 while($AData = mysqli_fetch_assoc($DBData)) {
  $ally_result = mysqli_execute_query($GLOBALS['dbi'], "SELECT Count(de_ally_partner.ally_id_1) AS count_partners FROM de_ally_partner WHERE de_ally_partner.ally_id_1=? OR de_ally_partner.ally_id_2=?", [$AData["id"], $AData["id"]]);
  $ally_row = mysqli_fetch_assoc($ally_result);
  $iAnz = $ally_row['count_partners'];
  if ($iAnz > 2) { $sC = ' class="r"'; } else { $sC = ''; }
  $Tab2 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td".$sC.">".$iAnz."</td></tr>\r\n";
  $FCounter[1]++;
 }
?>

<b>DE-Allianzen ohne Leader</b> (Gefunden: <?php echo intval($FCounter[0]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Allyname</td><td>AllyTag</td><td>Co1</td><td>Co2</td><td>Member</td></tr>
<?php echo $Tab1; ?>
 </table>
 <br><br>

 <b>DE-Allianzen mit Leader</b> (Gefunden: <?php echo intval($FCounter[1]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Allyname</td><td>AllyTag</td><td>Bündnisse</td></tr>
<?php echo $Tab2; ?>
 </table>
 <br><br>


 <?php
  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");
 ?>

 <br>
 Seite in <?php echo $ltime; ?> Sekunden erstellt.

</body>
</html>
