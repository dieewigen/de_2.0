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

include "det_userdata.inc.php";

$page_title = 'Sektor bearbeiten';
$active_nav = 'sektor';
include "inc.layout.top.php";

$sektor = req_str('sektor');
$delvote = req_str('delvote');
$showsek = req_str('showsek');
?>
<form method="post" action="sektor.php">
Sektornummer oder Sektorname (%):
<input type="text" name="sektor" size="15" value="">
<input type="submit" name="search" value="Suchen">
</form>

<?php
 if (isset($_REQUEST["savedata"])) {
   csrf_require();
   mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET name = ?, url = ? WHERE sec_id = ?",
                      [req_str('sektorname'), req_str('sektorbild'), req_str('savesek')]);
                         if (mysqli_errno($GLOBALS['dbi'])) { echo '<span class="r">Error '.mysqli_errno($GLOBALS['dbi']).'</span>: '.mysqli_error($GLOBALS['dbi']).'<br>'; }
   echo "Daten zu Sektor ".htmlspecialchars(req_str('savesek'))." gespeichert.";
   $showsek = req_str('savesek');
 }

 if (isset($_REQUEST["showskvotes"])) { $showsek = req_str('savesek'); }

 if (isset($_REQUEST["search"])) {
   switch($sektor !== '' ? $sektor[0] : ''){
     case '%':
       $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, name FROM de_sector WHERE name LIKE ?", ['%' . $sektor . '%']);

       echo '<table cellpadding="5" cellspacing="0">';
       echo '<tr><th>Sektor</th><th>Name</th><th>&nbsp;</th></tr>';
       while($SData = mysqli_fetch_assoc($DBData)) {
         echo '<tr><td class="num">'.$SData["sec_id"].'</td><td>'.htmlspecialchars($SData["name"]).'</td><td><a href="sektor.php?showsek='.$SData["sec_id"].'">Anzeigen</a></td></tr>';
       }
       echo '</table><br><br>';

       $showsek = "";
       break;
     default:
       $showsek = $sektor;
       break;
   }
 }

 if ($delvote != "") {
   csrf_require();
   mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET votefor = 0 WHERE sector = ? AND system = ?", [$showsek, $delvote]);
                         if (mysqli_errno($GLOBALS['dbi'])) { echo '<span class="r">Error '.mysqli_errno($GLOBALS['dbi']).'</span>: '.mysqli_error($GLOBALS['dbi']).'<br>'; }
   echo "Vote von ".htmlspecialchars($showsek).":".htmlspecialchars($delvote)." gelöscht.";
 }

 if ($showsek != "") {
   if (is_numeric($showsek) == true) {
     $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) FROM de_user_data WHERE sector = ?", [$showsek]);
     $row = mysqli_fetch_row($result);
     $spieler = $row[0];
     if ($spieler > 0) {
       $DBData = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, name, url, bk, skmes, e1, e2 FROM de_sector WHERE sec_id = ?", [$showsek]);

       echo '<form method="post" action="sektor.php">';
       echo csrf_field();
       echo '<input type="hidden" name="savesek" size="4" value="'.htmlspecialchars($showsek).'">';
       echo '<table cellpadding="5" cellspacing="0">';
       $SData = mysqli_fetch_assoc($DBData);

       echo '<tr><td>Sektor</td><td>'.$SData["sec_id"].'</td></tr>';
       echo '<tr><td>Spieler</td><td>'.$spieler.'</td></tr>';
       echo '<tr><td>Sektorflotte Heimat</td><td>'.$SData["e1"].'</td></tr>';
       echo '<tr><td>Sektorflotte aktiv</td><td>'.$SData["e2"].'</td></tr>';

       echo '<tr><td>Name</td><td><input type="text" name="sektorname" size="50" value="'.htmlspecialchars((string)$SData["name"]).'"></td></tr>';

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
         if ($SKInfo["votefor"] != 0) { $SKVotes .= ' [<a href="'.csrf_url('sektor.php?showsek='.$SData["sec_id"].'&delvote='.$SKInfo["system"]).'" data-confirm="Vote von System '.$SKInfo["system"].' wirklich löschen?">löschen</a>]'; }
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
           if ($userids[$Sys1] == "") { echo '<tr><td>SK</td><td>[<span class="r">NA</span>] - '.$Anz1.' Vote(s)</td></tr>'; }
            else { echo '<tr><td>SK</td><td><a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$nicks[$Sys1]).'</a></td></tr>'; }
         }
         elseif ($Anz1 == $Anz2) {
           if (($userids[$Sys1] == "") AND ($userids[$Sys2] == "")) { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ [<span class="r">NA</span>] = [<span class="r">NA</span>] ]</td></tr>'; }
           elseif ($userids[$Sys1] == "") { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ [<span class="r">NA</span>] = <a href="idinfo.php?UID='.$userids[$Sys2].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$nicks[$Sys2]).'</a> ]</td></tr>'; }
           elseif ($userids[$Sys2] == "") { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ <a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$nicks[$Sys1]).'</a> = [<span class="r">NA</span>] ]</td></tr>'; }
           else { echo '<tr><td>SK</td><td>Votegleichstand [ '.$Anz1.' = '.$Anz2.' ] - [ <a href="idinfo.php?UID='.$userids[$Sys1].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$nicks[$Sys1]).'</a> = <a href="idinfo.php?UID='.$userids[$Sys2].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$nicks[$Sys2]).'</a> ]</td></tr>'; }
         }
       }
       else { echo '<tr><td>SK</td><td>---</td></tr>'; }

       if (isset($_REQUEST["showskvotes"])) { echo $SKVotes; }

       $BKResult = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id, spielername FROM de_user_data WHERE sector = ? AND system = ?", [$SData["sec_id"], $SData["bk"]]);
       $BKInfo = mysqli_fetch_assoc($BKResult);
       if ($BKInfo == false) { echo '<tr><td>BK</td><td>---</td></tr>'; }
        else { echo '<tr><td>BK</td><td><a href="idinfo.php?UID='.$BKInfo["user_id"].'" target="_blank" rel="noopener">'.htmlspecialchars((string)$BKInfo["spielername"]).'</a></td></tr>'; }

       echo '<tr><td colspan="2">Informationen vom SK</td></tr><tr><td colspan="2">'.skmesaufbereitung($SData["skmes"]).'</td></tr>';

       echo '<tr><th colspan="2"><input type="submit" name="savedata" value="Speichern"> <input type="submit" name="showskvotes" value="SK-Votes anzeigen"></th></tr>';

       echo '</table>';
       echo '</form>';



     }
     else { echo "Keine Spieler in dem angegebenen Sektor gefunden"; }
   }
   else { echo "Fehlerhafte Sektorangabe!"; }
 }
 else { echo "Es wurde kein Sektor gewählt."; }

include "inc.layout.bottom.php";
