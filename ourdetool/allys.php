<?
include "../inccon.php";
include '../functions.php';

function getmicrotime(){ 
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
} 

$time_start = getmicrotime();

$Tab1 = ""; $Tab2 = "";

/*
$DBData = mysql_query("SELECT de_allys.* FROM de_allys LEFT JOIN de_login ON (de_allys.leaderid = de_login.user_id) WHERE (de_login.nic) Is Null ORDER BY de_allys.id") or die ("Fehler beim Auslesen der Daten: " . mysql_error());
while($AData = mysql_fetch_array($DBData)) {
	$iAnz = mysql_result(mysql_query("SELECT Count(de_user_data.allytag) FROM de_user_data WHERE de_user_data.allytag='".$AData["allytag"]."'"),0);
	if ($iAnz == 0) { $sC = ' class="r"'; } else { $sC = ''; }
	if ($AData["coleaderid1"] == -1) { $Co1 = "---"; } else { $Co1 = $AData["coleaderid1"]; }
	if ($AData["coleaderid2"] == -1) { $Co2 = "---"; } else { $Co2 = $AData["coleaderid2"]; }
	$Tab1 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td>".$Co1."</td><td>".$Co2."</td><td".$sC.">".$iAnz."</td></tr>\r\n";
	$FCounter[0]++;
} 
*/

$DBData = mysql_query("SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
		or die ("Fehler beim Auslesen der Daten: " . mysql_error());

while($AData = mysql_fetch_array($DBData)) {
	//Anzahl der Bündnisse
	$iAnz = mysql_result(mysql_query("SELECT Count(de_ally_partner.ally_id_1) FROM de_ally_partner WHERE de_ally_partner.ally_id_1='".$AData["id"]."' OR de_ally_partner.ally_id_2='".$AData["id"]."'"),0);
	$allytag=$AData["allytag"];
	
	//Mitgliederanzahl
	$mAnz = mysql_result(mysql_query("SELECT Count(de_user_data.allytag) FROM de_user_data WHERE allytag='$allytag' AND status=1"),0);
	if ($iAnz > 2) { $sC = ' class="r"'; } else { $sC = ''; }

	//Anzahl geworbener Spieler
	$geworben=0;
	$result=mysql_query("SELECT * FROM de_user_data WHERE allytag='$allytag' AND status=1",$db);
	while($rowx = mysql_fetch_array($result)){
		$uid=$rowx[user_id];
		$db_daten=mysql_query("SELECT owner_id FROM de_login WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_daten);
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
 <b>DE-Allianzen</b> (Gefunden: <? echo intval($FCounter[1]); ?>) <br><br>
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

	$query = "SELECT * FROM de_user_data WHERE status='1' AND allytag='$clankuerzel' ORDER BY sector, system";

	$result = mysql_query($query);

	$nb = mysql_num_rows($result);

	$row = 0;


	while ($row < $nb){
		
			$userid = mysql_result($result,$row,"user_id");
			$sector = mysql_result($result,$row,"sector");
			$system = mysql_result($result,$row,"system");
			$score = mysql_result($result,$row,"score");
			$kollies = mysql_result($result,$row,"col");
			$spielername = mysql_result($result,$row,"spielername");
			$sectorjump = explode(":",$sector);
			$sectorjump = $sectorjump[0];

			$logindata= mysql_fetch_array(mysql_query("SELECT * FROM de_login where user_id='$userid'"));
			
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

 $DBData = mysql_query("SELECT de_allys.* FROM de_allys LEFT JOIN de_login ON (de_allys.leaderid = de_login.user_id) WHERE (de_login.nic) Is Null ORDER BY de_allys.id")
           or die ("Fehler beim Auslesen der Daten: " . mysql_error());

 while($AData = mysql_fetch_array($DBData)) {
  $iAnz = mysql_result(mysql_query("SELECT Count(de_user_data.allytag) FROM de_user_data WHERE de_user_data.allytag='".$AData["allytag"]."'"),0);
  if ($iAnz == 0) { $sC = ' class="r"'; } else { $sC = ''; }
  if ($AData["coleaderid1"] == -1) { $Co1 = "---"; } else { $Co1 = $AData["coleaderid1"]; }
  if ($AData["coleaderid2"] == -1) { $Co2 = "---"; } else { $Co2 = $AData["coleaderid2"]; }
  $Tab1 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td>".$Co1."</td><td>".$Co2."</td><td".$sC.">".$iAnz."</td></tr>\r\n";
  $FCounter[0]++;
 }

 $DBData = mysql_query("SELECT de_allys.*, de_login.user_id FROM de_allys, de_login WHERE de_allys.leaderid = de_login.user_id ORDER BY de_allys.id")
           or die ("Fehler beim Auslesen der Daten: " . mysql_error());

 while($AData = mysql_fetch_array($DBData)) {
  $iAnz = mysql_result(mysql_query("SELECT Count(de_ally_partner.ally_id_1) FROM de_ally_partner WHERE de_ally_partner.ally_id_1='".$AData["id"]."' OR de_ally_partner.ally_id_2='".$AData["id"]."'"),0);
  if ($iAnz > 2) { $sC = ' class="r"'; } else { $sC = ''; }
  $Tab2 .= "  <tr><td>".$AData["id"]."</td><td>".$AData["allyname"]."</td><td>".$AData["allytag"]."</td><td".$sC.">".$iAnz."</td></tr>\r\n";
  $FCounter[1]++;
 }
?>

<b>DE-Allianzen ohne Leader</b> (Gefunden: <? echo intval($FCounter[0]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Allyname</td><td>AllyTag</td><td>Co1</td><td>Co2</td><td>Member</td></tr>
<? echo $Tab1; ?>
 </table>
 <br><br>

 <b>DE-Allianzen mit Leader</b> (Gefunden: <? echo intval($FCounter[1]); ?>) <br><br>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr><td>ID</td><td>Allyname</td><td>AllyTag</td><td>B�ndnisse</td></tr>
<? echo $Tab2; ?>
 </table>
 <br><br>


 <?
  $time_end = getmicrotime();
  $ltime = number_format($time_end - $time_start,2,".","");
 ?>

 <br>
 Seite in <? echo $ltime; ?> Sekunden erstellt.

</body>
</html>
