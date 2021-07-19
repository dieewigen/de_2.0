<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_server.php" method="post">
<div align="center">
<?php
include "det_userdata.inc.php";

if ($action=="usergeb" && $status=="1")
{
mysql_query("UPDATE de_user_data set techs='s1111111111111111111111111111000000000001111111111111111111111111111111111110000000000000000000000000000000000',buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, tradescore=0, sells=0,allytag='', status=0");
echo "<h1>Alle Geb&auml;ude gebaut & Forschungen entwickelt</h1>";
}

if ($action=="usergeb" && $status=="0")
{
mysql_query("UPDATE de_user_data set techs='s0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, tradescore=0, sells=0,allytag='', status=0");
echo "<h1>Alle Geb&auml;ude zerst&ouml;rt & Forschungen verkauft</h1>";
}

if ($action=="sekgeb" && $status=="1")
{
mysql_query("UPDATE de_sector set techs='s111111111'");
echo "<h1>Alle Sektor-Geb&auml;ude gebaut</h1>";
}

if ($action=="sekgeb" && $status=="0")
{
mysql_query("UPDATE de_sector set techs='s000000000'");
echo "<h1>Alle Sektor-Geb&auml;ude zerst&ouml;rt</h1>"; 
}
?>
<div align="center">
<br>
<br>
<br>
<br>
<br>
<table border="1" width="400">
<tr>
 <td align="center">Alle Geb&auml;ude & Forschungen</td>
 <td align="center"><a href="commandcenter.php?action=usergeb&status=1">ja</a>&nbsp;/&nbsp;<a href="commandcenter.php?action=usergeb&status=0">nein</a></td>
</tr>
<tr>
 <td align="center">Alle Sektor-Geb&auml;ude?</td>
 <td align="center"><a href="commandcenter.php?action=sekgeb&status=1">ja</a>&nbsp;/&nbsp;<a href="commandcenter.php?action=sekgeb&status=0">nein</a></td>
</tr>
</table>
</div>
</body>
</html>
