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
<br><br>
<?

if($reply)
{
$now=time();
mysql_query("INSERT INTO de_sectorforum_posts (poster,post,time,thread,title) VALUES ('Die Ewigen Team','$post[text]','$now','$threadid','$post[title]')");
mysql_query("UPDATE de_sectorforum_threads set lastposter='Die Ewigen Team',lastactive='$now', anzposts=anzposts+1 WHERE id='$threadid'");
}

$temporary=mysql_query("SELECT threadname, sector, open FROM de_sectorforum_threads WHERE id='$id'");
$temporary = mysql_fetch_array($temporary);
echo "<div align=center><font size=2 face=arial><b><a href=\"sekforumourdetool.php?uid=$uid\">Sektorforum</a></b> -> <b>THEMA: $temporary[threadname]</b><br><br></div>";



$db_daten=mysql_unbuffered_query("SELECT poster, post, time, title FROM de_sectorforum_posts WHERE thread='$id' ORDER BY  time ASC");
while($row = mysql_fetch_array($db_daten)) {

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
        - gepostet von $row[poster] am $posttime Uhr</font></td>
    </tr>
    <tr>
      <td width=\"100%\" bgcolor=#202020 align=left><font color=#E0E0E0 face=\"Arial\" size=\"2\">$row[post]</font></td>
    </tr>
  </table>
</div><br>";

}
?>
<div align="center">
<form method="POST" action="threadourdetool.php?id=<?=$id?>&threadid=<?=$id?>">
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
</div>
<?
echo "<div align=center><font size=2 face=arial><b><a href=\"sekforumourdetool.php?uid=$uid\">Sektorforum</a></b> -> <b>THEMA: $temporary[threadname]</b><br></div>";
?>



</body>
</html>
