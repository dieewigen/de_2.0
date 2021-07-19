<?php
include "../inccon.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Sektorforum</title>
<?php include "cssinclude.php";?>
<style>
A:visited {font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; text-decoration: none; color: #0055CC}
</style>
</head>
<body>
<br><br>
<?
include "det_userdata.inc.php";
$db_user=mysql_query("SELECT sector FROM de_user_data WHERE user_id='$uid'");
$user = mysql_fetch_array($db_user);
echo "<center><h1>Sektor: $user[sector]</h1></center>";
$db_daten=mysql_query("SELECT creator, id, lastposter, lastactive, threadname, anzposts FROM de_sectorforum_threads WHERE sector='$user[sector]' ORDER BY lastactive DESC");
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
<td class="cl"><a href="threadourdetool.php?uid=<?=$uid?>&id=<?=$row[id] ?>&f=<?=$row[anzposts]?>"><b><?=$row[threadname] ?></b></a></font></td>
<td class="tc"><?=$row[creator] ?></font></td>
<td class="tc"><?=$row[anzposts] ?></font></td>
<td class="tr"><p><font style="font-size: 8pt">gepostet
von <i> <?=$row[lastposter] ?></i><br>am <?=$posttime ?> Uhr</p></td>
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
</body>
</html>
