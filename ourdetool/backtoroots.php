<?PHP

include "../inccon.php";
?>

<html>
<head>
<title>backtoroots</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?
include "det_userdata.inc.php";

if($ressbtn)
{

        mysql_query("update de_user_data set sector=0, system=0 where sector='$sek' and system='$sys'");

        echo '<h1>Backtoroot successfull <br> ==> <a href="backtoroots.php">weiter</a> <== </h1>';
}
else
{
?>
<form action="backtoroots.php" method="post">
<br><b>Dem Spieler wird vom Server ein neues System zugewiesen.</b><br><br>
<table border="0">
<tr>
 <td align="right">Koords:&nbsp;&nbsp;</td>
 <td><input type="Text" name="sek" size="3">:<input type="Text" name="sys" size="3"></td>
</tr>
<tr>
 <td colspan="2"><input type="Submit" name="ressbtn" value="One way Ticket lösen"></td>
</tr>
</table>
</form>
<?
}
?>
</div>
</body>
</html>
