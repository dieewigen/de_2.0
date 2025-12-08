<?php
include "../inccon.php";

function skmesaufbereitung($skmes)
{
$skmes=preg_replace("/\\[img\\]([^\\[]*)\\[\/img\\]/i","<img src=\"\\1\" border=0>",$skmes);

$skmes= preg_replace("/\[b\]/i", "<b>",$skmes);
$skmes= preg_replace("/\[\/b\]/i", "</b>",$skmes);

$skmes= preg_replace("/\[i\]/i", "<i>",$skmes);
$skmes= preg_replace("/\[\/i\]/i", "</i>",$skmes);

$skmes= preg_replace("/\[u\]/i", "<u>",$skmes);
$skmes= preg_replace("/\[\/u\]/i", "</u>",$skmes);

$skmes= preg_replace("/\[center\]/i", "<center>",$skmes);
$skmes= preg_replace("/\[\/center\]/i", "</center>",$skmes);

$skmes= preg_replace("/\[pre\]/i", "<pre>",$skmes);
$skmes= preg_replace("/\[\/pre\]/i", "</pre>",$skmes);

$skmes = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$skmes);
$skmes = str_replace("[CROT]","<font color=\"#F10505\">",$skmes);
$skmes = str_replace("[CDE]","<font color=\"#3399FF\">",$skmes);
$skmes = str_replace("[CGELB]","<font color=\"#FDFB59\">",$skmes);


$skmes=preg_replace("/\\[email\\]([^\\[]*)\\[\/email\\]/i","<a href=\"mailto:\\1\">\\1</a>",$skmes);
$skmes=preg_replace("/\\[url\\]www.([^\\[]*)\\[\/url\\]/i","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$skmes);
$skmes=preg_replace("/\\[url\\]([^\\[]*)\\[\/url\\]/i","<a href=\"\\1\" target=\"_blank\">\\1</a>",$skmes);
$skmes=preg_replace("/\\[url=http:\/\/([^\\[]+)\\]([^\\[]*)\\[\/url\\]/i","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$skmes);

$skmes = preg_replace("/\\[color=#([^\\[]+)\\]([^\\[]*)\\[\/color\\]/i","<font color=\"#\\1\" >\\2</font>",$skmes);
$skmes = preg_replace("/\\[size=([^\\[]+)\\]([^\\[]*)\\[\/size\\]/i","<font size=\"\\1\" >\\2</font>",$skmes);


if ($skmes=='') $skmes='&nbsp;';

return $skmes;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Sektor bearbeiten</title>
<?php include "cssinclude.php";?>
<style >
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 a { color: #3399ff; text-decoration: underline }
 a:hover { color: #3399ff; text-decoration: none }
</style>
</head>
<body>
<center>
<form method="post" action="sektor.php">
Sektornummer oder Sektorname (%):
<input type="text" name="sektor" size="15" value=""><br><br>
<input type="submit" name="search" value="Suchen">
</form>

<?php
include "det_userdata.inc.php";
 if ($_REQUEST["savedata"]) {
   mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET name = ?, url = ? WHERE sec_id = ?", 
                      [$_REQUEST["sektorname"], $_REQUEST["sektorbild"], $_REQUEST["savesek"]]);
                         if (mysqli_errno($GLOBALS['dbi'])) { echo '<font color="#FF0000">Error '.mysqli_errno($GLOBALS['dbi']).'</font>: '.mysqli_error($GLOBALS['dbi']).'<br>'; }
   echo "Daten zu Sektor ".$_REQUEST["savesek"]." gespeichert.";
   $showsek = $_REQUEST["savesek"];
 }

 if ($_REQUEST["showskvotes"]) { $showsek = $_REQUEST["savesek"]; }

 if ($_REQUEST["search"]) {
   switch($_REQUEST["sektor"][0]){
     case '%':
       $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, name FROM de_sector WHERE name LIKE ?", ['%' . $sektor . '%']);

       echo '<table border="0" cellpadding="5" cellspacing="0">';
       echo '<tr><td>Sektor</td><td>Name</td><td>&nbsp;</td></tr>';
       while($SData = mysqli_fetch_assoc($DBData)) {
         echo '<tr><td>'.$SData["sec_id"].'</td><td>'.$SData["name"].'</td><td><a href="'.$PHP_SELF.'?showsek='.$SData["sec_id"].'">Anzeigen</a></td></tr>';
       }
       echo '</table><br><br>';

       $showsek = "";
       break;
     default:
       $showsek = $_REQUEST["sektor"];
       break;
   }
 }

 if ((isset($delvote)) AND ($delvote != "")) {
   mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET votefor = 0 WHERE sector = ? AND system = ?", [$showsek, $delvote]);
                         if (mysqli_errno($GLOBALS['dbi'])) { echo '<font color="#FF0000">Error '.mysqli_errno($GLOBALS['dbi']).'</font>: '.mysqli_error($GLOBALS['dbi']).'<br>'; }
   echo "Vote von ".$showsek.":".$delvote." gelöscht.";
 }

 if ((isset($showsek)) AND ($showsek != "")) {
   if (is_numeric($showsek) == true) {
     $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) FROM de_user_data WHERE sector = ?", [$showsek]);
     $row = mysqli_fetch_row($result);
     $spieler = $row[0];
     if ($spieler > 0) {
       $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, name, url, bk, skmes, e1, e2 FROM de_sector WHERE sec_id = ?", [$showsek]);

       echo '<form method="post" action="'.$PHP_SELF.'">';
       echo '<input type="hidden" name="savesek" size="4" value="'.$showsek.'">';
       echo '<table border="0" cellpadding="5" cellspacing="0">';
       $SData = mysqli_fetch_assoc($DBData);

       echo '<tr><td>Sektor</td><td>'.$SData["sec_id"].'</td></tr>';
       echo '<tr><td>Spieler</td><td>'.$spieler.'</td></tr>';
       echo '<tr><td>Sektorflotte Heimat</td><td>'.$SData["e1"].'</td></tr>';
       echo '<tr><td>Sektorflotte aktiv</td><td>'.$SData["e2"].'</td></tr>';

       echo '<tr><td>Name</td><td><input type="text" name="sektorname" size="50" value="'.$SData["name"].'"></td></tr>';

       $SKData = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, spielername, votefor, system FROM de_user_data WHERE sector = ? ORDER BY system", [$SData["sec_id"]]);
       $SKVotes = '<tr><td>&nbsp;</td><td>';
       $Votes = []; // Initialisierung des Votes-Arrays
       $nicks = []; // Initialisierung des nicks-Arrays
       $userids = []; // Initialisierung des userids-Arrays
       while($SKInfo = mysqli_fetch_assoc($SKData)) {
         if ($SKInfo["votefor"] != 0) { $Votes[$SKInfo["votefor"]]++; }
         $nicks[$SKInfo["system"]] = $SKInfo["spielername"];
         $userids[$SKInfo["system"]] = $SKInfo["user_id"];

         $SKVotes .= $SKInfo["system"].' votes for '.$SKInfo["votefor"];
         if ($SKInfo["votefor"] != 0) { $SKVotes .= ' [<a href="'.$PHP_SELF.'?showsek='.$SData["sec_id"].'&delvote='.$SKInfo["system"].'">löschen</a>]'; }
         $SKVotes .= '<br>';
       }
       $SKVotes .= '</td></tr>';

       if (count($Votes) > 0) {
         arsort ($Votes);
         reset ($Votes);

         $keys = array_keys($Votes);
         $Sys1 = isset($keys[0]) ? $keys[0] : null;
         $Anz1 = ($Sys1 !== null) ? $Votes[$Sys1] : 0;
         $Sys2 = isset($keys[1]) ? $keys[1] : null;
         $Anz2 = ($Sys2 !== null) ? $Votes[$Sys2] : 0;

         if ($Anz1 > $Anz2) {
           if ($userids[$Sys1] == "") { echo '<tr><td>SK</td><td>[<font color="#FF0000">NA</font>] - '.$Anz1.' Vote(s)</td></tr>'; }
            else { echo '<tr><td>SK</td><td><a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank">'.$nicks[$Sys1].'</a></td></tr>'; }
         }
         elseif ($Anz1 == $Anz2) {
           if (($userids[$Sys1] == "") AND ($userids[$Sys2] == "")) { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ [<font color="#FF0000">NA</font>] = [<font color="#FF0000">NA</font>] ]</td></tr>'; }
           elseif ($userids[$Sys1] == "") { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ [<font color="#FF0000">NA</font>] = <a href="idinfo.php?UID='.$userids[$Sys2].'" target="_blank">'.$nicks[$Sys2].'</a> ]</td></tr>'; }
           elseif ($userids[$Sys2] == "") { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ <a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank">'.$nicks[$Sys1].'</a> = [<font color="#FF0000">NA</font>] ]</td></tr>'; }
           else { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ <a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank">'.$nicks[$Sys1].'</a> = <a href="idinfo.php?UID='.$userids[$Sys2].'" target="_blank">'.$nicks[$Sys2].'</a> ]</td></tr>'; }
         }
       }
       else { echo '<tr><td>SK</td><td>---</td></tr>'; }

       if ($_REQUEST["showskvotes"]) { echo $SKVotes; }

       $BKResult = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, spielername FROM de_user_data WHERE sector = ? AND system = ?", [$SData["sec_id"], $SData["bk"]]);
       $BKInfo = mysqli_fetch_assoc($BKResult);
       if ($BKInfo == false) { echo '<tr><td>BK</td><td>---</td></tr>'; }
        else { echo '<tr><td>BK</td><td><a href="idinfo.php?UID='.$BKInfo["user_id"].'" target="_blank">'.$BKInfo["spielername"].'</a></td></tr>'; }

       echo '<tr><td colspan="2">Informationen vom SK</td></tr><tr><td colspan="2">'.skmesaufbereitung($SData["skmes"]).'</td></tr>';

       echo '<tr><th colspan="2"><input type="submit" name="savedata" value="Speichern"> <input type="submit" name="showskvotes" value="SK-Votes anzeigen"></th></tr>';

       echo '</table>';
       echo '</form>';



     }
     else { echo "Keine Spieler in dem angegebenen Sektor gefunden"; }
   }
   else { echo "Fehlerhafte Sektorangabe!"; }
 }
 else { echo "Es wurde kein Sektor gew�hlt."; }
?>

</center>
</body>
</html>