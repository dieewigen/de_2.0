<?php
include "../inccon.php";

function skmesaufbereitung($skmes)
{
$skmes=eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<img src=\"\\1\" border=0>",$skmes);

$skmes= eregi_replace("\[b\]", "<b>",$skmes);
$skmes= eregi_replace("\[/b\]", "</b>",$skmes);

$skmes= eregi_replace("\[i\]", "<i>",$skmes);
$skmes= eregi_replace("\[/i\]", "</i>",$skmes);

$skmes= eregi_replace("\[u\]", "<u>",$skmes);
$skmes= eregi_replace("\[/u\]", "</u>",$skmes);

$skmes= eregi_replace("\[center\]", "<center>",$skmes);
$skmes= eregi_replace("\[/center\]", "</center>",$skmes);

$skmes= eregi_replace("\[pre\]", "<pre>",$skmes);
$skmes= eregi_replace("\[/pre\]", "</pre>",$skmes);

$skmes = str_replace("[CGRUEN]","<font color=\"#28FF50\">",$skmes);
$skmes = str_replace("[CROT]","<font color=\"#F10505\">",$skmes);
$skmes = str_replace("[CDE]","<font color=\"#3399FF\">",$skmes);
$skmes = str_replace("[CGELB]","<font color=\"#FDFB59\">",$skmes);


$skmes=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$skmes);
$skmes=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$skmes);
$skmes=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$skmes);
$skmes=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$skmes);

$skmes = eregi_replace("\\[color=#([^\\[]+)\\]([^\\[]*)\\[/color\\]","<font color=\"#\\1\" >\\2</font>",$skmes);
$skmes = eregi_replace("\\[size=([^\\[]+)\\]([^\\[]*)\\[/size\\]","<font size=\"\\1\" >\\2</font>",$skmes);


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
<form method="post" action="<? echo $PHP_SELF; ?>">
Sektornummer oder Sektorname (%):
<input type="text" name="sektor" size="15" value=""><br><br>
<input type="submit" name="search" value="Suchen">
</form>

<?php
include "det_userdata.inc.php";
 if ($_REQUEST["savedata"]) {
   mysql_query("UPDATE de_sector SET name = '".$_REQUEST["sektorname"]."', url = '".$_REQUEST["sektorbild"]."' WHERE sec_id = ".$_REQUEST["savesek"],$db);
                         if (mysql_errno()) { echo '<font color="#FF0000">Error '.mysql_errno().'</font>: '.mysql_error().'<br>'; }
   echo "Daten zu Sektor ".$_REQUEST["savesek"]." gespeichert.";
   $showsek = $_REQUEST["savesek"];
 }

 if ($_REQUEST["showskvotes"]) { $showsek = $_REQUEST["savesek"]; }

 if ($_REQUEST["search"]) {
   switch($_REQUEST["sektor"][0]){
     case '%':
       $DBData = mysql_query("SELECT sec_id, name FROM de_sector WHERE name LIKE '%".$sektor."%'",$db);

       echo '<table border="0" cellpadding="5" cellspacing="0">';
       echo '<tr><td>Sektor</td><td>Name</td><td>&nbsp;</td></tr>';
       while($SData = mysql_fetch_array($DBData)) {
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
   mysql_query("UPDATE de_user_data SET votefor = 0 WHERE sector = ".$showsek." AND system = ".$delvote,$db);
                         if (mysql_errno()) { echo '<font color="#FF0000">Error '.mysql_errno().'</font>: '.mysql_error().'<br>'; }
   echo "Vote von ".$showsek.":".$delvote." gelöscht.";
 }

 if ((isset($showsek)) AND ($showsek != "")) {
   if (is_numeric($showsek) == true) {
     $spieler = mysql_result(mysql_query("SELECT count(user_id) FROM de_user_data WHERE sector = ".$showsek,$db),0);
     if ($spieler > 0) {
       $DBData = mysql_query("SELECT sec_id, name, url, bk, skmes, e1, e2 FROM de_sector WHERE sec_id = ".$showsek,$db);

       echo '<form method="post" action="'.$PHP_SELF.'">';
       echo '<input type="hidden" name="savesek" size="4" value="'.$showsek.'">';
       echo '<table border="0" cellpadding="5" cellspacing="0">';
       $SData = mysql_fetch_array($DBData);

       echo '<tr><td>Sektor</td><td>'.$SData["sec_id"].'</td></tr>';
       echo '<tr><td>Spieler</td><td>'.$spieler.'</td></tr>';
       echo '<tr><td>Sektorflotte Heimat</td><td>'.$SData["e1"].'</td></tr>';
       echo '<tr><td>Sektorflotte aktiv</td><td>'.$SData["e2"].'</td></tr>';

       echo '<tr><td>Name</td><td><input type="text" name="sektorname" size="50" value="'.$SData["name"].'"></td></tr>';

       if ($SData["url"] != "") { $ShowPic = '[<a href="'.$SData["url"].'" target="_blank">ANZEIGEN</a>]'; } else { $ShowPic = ""; }
       echo '<tr><td>Sekbild</td><td><input type="text" name="sektorbild" size="50" value="'.$SData["url"].'"> '.$ShowPic.'</td></tr>';

       $SKData = mysql_query("SELECT user_id, spielername, votefor, system FROM de_user_data WHERE sector = ".$SData["sec_id"]." ORDER BY system",$db);
       $SKVotes = '<tr><td>&nbsp;</td><td>';
       while($SKInfo = mysql_fetch_array($SKData)) {
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

         list ($Sys1, $Anz1) = each ($Votes);
         list ($Sys2, $Anz2) = each ($Votes);

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

       $BKInfo = mysql_fetch_array(mysql_query("SELECT user_id, spielername FROM de_user_data WHERE sector = ".$SData["sec_id"]." AND system = ".$SData["bk"],$db));
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
 else { echo "Es wurde kein Sektor gewählt."; }
?>

</center>
</body>
</html>