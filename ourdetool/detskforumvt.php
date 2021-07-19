<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Threadview</title>
<?php include "cssinclude.php";?>
</head>
<body>
<br>
<center>
<?
include "det_userdata.inc.php";

$threadid=(int)$threadid;

$temporary=mysql_query("SELECT threadname, sector, open FROM de_sectorforum_threads WHERE id='$id'");
$temporary = mysql_fetch_array($temporary);
echo "<div align=center><font size=2 face=arial><b><a href=\"detskforum.php?SID=$SID\">SK-Forum</a></b> -> <b>THEMA: $temporary[threadname]</b><br></div>";
if("0"<>"$temporary[sector]")
{
echo 'Leider haben Sie keinen Zugriff auf dieses Dokument.';
exit();
}


$db_daten=mysql_query("SELECT poster, post, time, title FROM de_sectorforum_posts WHERE thread='$id' ORDER BY time ASC");
while($row = mysql_fetch_array($db_daten))
{

//sektor des posters feststellen
$result = mysql_query("SELECT sector FROM de_user_data WHERE spielername='$row[poster]'");
//$result = mysql_fetch_array($result);
//$result = mysql_query("SELECT sector FROM de_user_data WHERE user_id='$result[user_id]'");
$result = mysql_fetch_array($result);

$posttime =date ("d.m.Y u\m H:i", $row[time]);

$row[post]=htmlspecialchars($row[post]);
$row[post]=nl2br($row[post]);

// spezielle code tags
$row[post]=eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<img src=\"\\1\" border=0>",$row[post]);
$row[post]=eregi_replace("\\[b\\]([^\\[]*)\\[/b\\]","<b>\\1</b>",$row[post]);
$row[post]=eregi_replace("\\[i\\]([^\\[]*)\\[/i\\]","<i>\\1</i>",$row[post]);
$row[post]=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$row[post]);
$row[post]=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$row[post]);
$row[post]=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$row[post]);
$row[post]=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$row[post]);


//smilies
$row[post] = str_replace(":)","<img src=\"../smilies/a1.gif\">",$row[post]);
$row[post] = str_replace(":(","<img src=\"../smilies/a2.gif\">",$row[post]);
$row[post] = str_replace(";)","<img src=\"../smilies/a3.gif\">",$row[post]);
$row[post] = str_replace(":o","<img src=\"../smilies/a4.gif\">",$row[post]);
$row[post] = str_replace(":D","<img src=\"../smilies/a5.gif\">",$row[post]);
$row[post] = str_replace(":p","<img src=\"../smilies/a6.gif\">",$row[post]);
$row[post] = str_replace(":P","<img src=\"../smilies/a7.gif\">",$row[post]);

if($row[title]=="")
{
$row[title]="Re:";
$row[title].=$temporary[threadname];
}
echo "
<div align=\"center\">
  <table border=\"0\" width=\"500\" bgcolor=\"black\">
    <tr>
      <td width=\"100%\" bgcolor=#202020 align=left><font color=#E0E0E0 face=\"Arial\" size=\"2\"><b>$row[title]</font></b>
        <font face=\"Arial\" size=\"2\">
        - gepostet von $row[poster] (Sektor: $result[sector]) am $posttime Uhr</font></td>
    </tr>
    <tr>
      <td width=\"100%\" bgcolor=#202020 align=left><font color=#E0E0E0 face=\"Arial\" size=\"2\">$row[post]</font></td>
    </tr>
  </table>
</div><br>";

}
echo "<div align=center><font size=2 face=arial><b><a href=\"detskforum.php?SID=$SID\">SK-Forum</a></b> -> <b>THEMA: $temporary[threadname]</b><br></div>";

if($temporary[open]==1) {
?>
<form method="POST" action="detskforum.php?threadid=<?=$id ?>&SID=<?=$SID ?>">
<INPUT  type="hidden" name="SID" value="<?=$SID ?>">
  <table width="500" bgcolor=#000000>
  <input type="hidden" name="action" value="newpost">
    <tr>
      <td width="20%" bgcolor=#202020><font face=arial size=2 color=#ffffff>Thema:</td>
      <td width="80%" bgcolor=#202020><input type="text" name="post[title]" size="25"></td>
    </tr>
    <tr>
      <td width="20%" bgcolor=#202020><font face=arial size=2 color=#ffffff>Message:</td>
      <td width="80%" bgcolor=#202020><textarea rows="6" name="post[text]" cols="47"></textarea></td>
    </tr>
  </table>
  <p><input type="submit" value="Antworten" name="reply"></p>
</form>

<a href="detskforum.php?SID=<?=$SID ?>&a=c&threadid=<?=$id ?>">Thread schlie&szlig;en</a> -
<a href="detskforum.php?SID=<?=$SID ?>&a=d&threadid=<?=$id ?>">Thread l&ouml;schen</a>
<?
}
?>

<br><br>
</body>
</html>
