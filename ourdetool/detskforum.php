<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>SK-Forum</title>
<?php include "cssinclude.php";?>
</head>
<body>
<center>
<h1>SK-Forum</h1>
<?
include "det_userdata.inc.php";
$threadid=(int)$threadid;
//neuen thread erstellen?
if($newthread)
{
  if(!$post[text])$error.="Keine Nachricht eingetragen.<br>";
  if(!$post[title])$error.="Leider ist ein Fehler aufgetreten[Kein Thema angegeben].<br>";

    $post[title]=htmlspecialchars($post[title]);
    $now=time();

    //thread eintragen
    mysql_query("INSERT INTO de_sectorforum_threads (threadname, creator, sector, lastposter, lastactive, anzposts) VALUES ('$post[title]','Die Ewigen Team',0,'Die Ewigen Team','$now',0)");
    $threadid=mysql_insert_id();

    //posting eintragen
    mysql_query("INSERT INTO de_sectorforum_posts (poster,post,time,thread,title) VALUES ('Die Ewigen Team','$post[text]','$now','$threadid','$post[title]')");

}

//im thread antworten
elseif($reply)
{
  if(!$post[text])$error.="Keine Nachricht eingetragen.<br>";
  if(!$post[title])$error.="Leider ist ein Fehler aufgetreten[Kein Thema angegeben].<br>";

  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);

    $post[title]=htmlspecialchars($post[title]);
    $now=time();

    //posting eintragen
    mysql_query("INSERT INTO de_sectorforum_posts (poster,post,time,thread,title) VALUES ('Die Ewigen Team','$post[text]','$now','$threadid','$post[title]')");

    //thread mit den neuen daten updaten
    mysql_query("UPDATE de_sectorforum_threads set lastposter='Die Ewigen Team',lastactive='$now', anzposts=anzposts+1 WHERE id='$threadid'");

}

//close thread
elseif ($a=="c")
{
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ("0"!=$row[sector] && in_array($ums_user_id, $mods))$error.="Du hast keine Berechtigung diesen Thread zu schlie&szlig;en.<br>";

  if(!$error)
  {
    mysql_query("UPDATE de_sectorforum_threads SET open=0 WHERE id='$threadid'");
    echo "Thread geschlossen.";
  }else echo $error;
}

//delete thread
elseif ($a=="d")
{
  //schaue ob die threadid und der sektor g&uuml;ltig sind
  $db_daten=mysql_query("SELECT sector FROM de_sectorforum_threads WHERE id='$threadid'");
  $num = mysql_num_rows($db_daten);
  if ($num==0)$error.="Dieser Thread existiert leider nicht.<br>";
  $row = mysql_fetch_array($db_daten);
  if ("0"!=$row[sector] && in_array($ums_user_id, $mods))$error.="Du hast keine Berechtigung diesen Thread zu l&ouml;schen.<br>";

  if(!$error)
  {
    mysql_query("DELETE FROM de_sectorforum_threads WHERE id='$threadid'");
    mysql_query("DELETE FROM de_sectorforum_posts WHERE thread='$threadid'");
    echo "Thread gel&ouml;scht.";
  }else echo $error;
}


//alle threads anzeigen
$db_daten=mysql_query("SELECT creator, id, lastposter, lastactive, threadname, anzposts FROM de_sectorforum_threads WHERE sector=0 ORDER BY lastactive DESC");
$num = mysql_num_rows($db_daten);
if ($num!=0)
{
?><br>
<div align="center">

<table border="0" cellpadding="0" cellspacing="0">
<tr height="37" align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="180" class="ro">Threadname</td>
<td width="120" class="ro">gestartet von</td>
<td width="60" class="ro">Antworten</td>
<td width="170" class="ro">Letzte Nachricht von</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="4">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="180">
<col width="120">
<col width="60">
<col width="170">
</colgroup>
<?

while($row = mysql_fetch_array($db_daten))
{
$posttime =date ("d.m.Y u\m H:i", $row[lastactive]);
?>
<tr>
<td align="left" bgColor="#202020"><font face="Arial" size="2" color="#FFFFFF"><a href="detskforumvt.php?id=<?=$row[id] ?>&SID=<?=$SID?>"><b><?=$row[threadname] ?></b></a></font></td>
<td align="center" bgColor="#202020"><font face="Arial" size="2" color="#FFFFFF"><?=$row[creator] ?></font></td>
<td align="center" bgColor="#202020"><font size="2" color="#FFFFFF" face="Arial"><?=$row[anzposts] ?></font></td>
<td align="left" bgColor="#202020"><p align="right"><font style="font-size: 8pt" face="Arial" color="#FFFFFF">gepostet
von <i> <?=$row[lastposter] ?></i><br>am <?=$posttime ?> Uhr</font></p></td>
</tr>
<?
}
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>

<?php
}//ende if $num!=0
?>

<form action="detskforum.php?<?=SID?>" method="POST">
<INPUT  type="hidden" name="SID" value="<?=$SID ?>">
<input type="hidden" name="action" value="newthread">
<table width="500" bgcolor=#000000>
<tr>
<td width="20%" bgcolor=#202020><font face=arial size=2 color=#ffffff>Threadtitel:</td>
<td width="80%" bgcolor=#202020><input type="text" name="post[title]" size="25"></td>
</tr>
<tr>
<td width="20%" bgcolor=#202020><font face=arial size=2 color=#ffffff>Message:</td>
<td width="80%" bgcolor=#202020><textarea rows="5" name="post[text]" cols="47"></textarea></td>
</tr>
</table>
<p><input type="submit" value="Antworten" name="newthread"></p>
</form>
<br>

</body>
</html>
