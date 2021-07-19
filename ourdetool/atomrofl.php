<?php
include "../inccon.php";
?>

<html>
<head>
<title>Resspush</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?
include "det_userdata.inc.php";

if($ressbtn)
{
echo "<h1>Resspush</h1>";
$db_daten=mysql_query("SELECT user_id, restyp01, restyp02, restyp03, restyp04, restyp05, col, agent, e100, e101, e102, e103, e104 FROM de_user_data where sector='$sek' and system='$sys'");


        echo "<table border=1>";
        while($row=mysql_fetch_array($db_daten))
        {
        echo "<tr><td>$row[restyp01]</td><td>$row[restyp02]</td><td>$row[restyp03]</td><td>$row[restyp04]</td><td>$row[restyp05]</td><td>$row[col]</td></tr>";
        $uid = $row[user_id];
        $res01 = $row[restyp01] + $m;
        $res02 = $row[restyp02] + $d;
        $res03 = $row[restyp03] + $i;
        $res04 = $row[restyp04] + $e;
        $res05 = $row[restyp05] + $tronic;
        $kollie = $row[col] + $kollie;
        $agent = $row[agent] + $agent;
        $deff1= $row[e100]+$deff1;
        $deff2= $row[e101]+$deff2;
        $deff3= $row[e102]+$deff3;
        $deff4= $row[e103]+$deff4;
        $deff5= $row[e104]+$deff5;
        echo "<tr><td>$res01</td><td>$res02</td><td>$res03</td><td>$res04</td><td>$res05</td><td>$kollie</td></tr>";

        mysql_query("update de_user_data set restyp01 = $res01, restyp02 = $res02, restyp03 = $res03, restyp04 = $res04, restyp05 = $res05, col=$kollie, agent=$agent, e100=$deff1, e101=$deff2, e102=$deff3, e103=$deff4, e104=$deff5 where user_id = $uid");

        if($gebaeude=="v")
        {
        mysql_query("update de_user_data set techs='s1111111111111111111111111100000000000001111111111111111111111111111111111110000000000000000000000000000000000' where user_id = $uid");
        }

        $fleet_id=$uid.'-0';
        $homeschiffe = mysql_query("select e81, e82, e83, e84, e85, e86, e87, e88 from de_user_fleet where user_id = '$fleet_id'");
        $rew=mysql_fetch_array($homeschiffe);

        $n = $rew[e81] + $n;
        $j = $rew[e82] + $j;
        $z = $rew[e83] + $z;
        $k = $rew[e84] + $k;
        $s = $rew[e85] + $s;
        $t = $rew[e87] + $t;
        $traeger = $rew[e88] + $traeger;


        mysql_query("update de_user_fleet set e81 = $n, e82 = $j, e83 = $z, e84 = $k, e85 = $s, e87 = $t, e88 = '$traeger' where user_id = '$fleet_id'");
        }
        echo "</table>";

        echo '<h1>Transfer successfull <br> ==> <a href="atomrofl.php">weiter</a> <== </h1>';
}
else
{
?>
<form action="atomrofl.php" method="post">
<table border="0">
<tr>
 <td align="right">Koords:&nbsp;&nbsp;</td>
 <td><input type="Text" name="sek" size="3">:<input type="Text" name="sys" size="3"></td>
</tr>
<tr>
 <td align="right">Multiplex:&nbsp;&nbsp;</td>
 <td><input type="Text" name="m" size="10"></td>
</tr>
<tr>
 <td align="right">Dhyarra:&nbsp;&nbsp;</td>
 <td><input type="Text" name="d" size="10"></td>
</tr>
<tr>
 <td align="right">Iradium:&nbsp;&nbsp;</td>
 <td><input type="Text" name="i" size="10"></td>
</tr>
<tr>
 <td align="right">Eternium:&nbsp;&nbsp;</td>
 <td><input type="Text" name="e" size="10"></td>
</tr>
<tr>
 <td align="right">Tronic:&nbsp;&nbsp;</td>
 <td><input type="Text" name="tronic" size="10"></td>
</tr>
<tr>
 <td align="right">Kollies:&nbsp;&nbsp;</td>
 <td><input type="Text" name="kollie" size="10"></td>
</tr>
<tr>
 <td align="right">Agent:&nbsp;&nbsp;</td>
 <td><input type="Text" name="agent" size="10"></td>
</tr>
<tr>
 <td align="right">Nissen:&nbsp;&nbsp;</td>
 <td><input type="Text" name="n" size="10"></td>
</tr>
<tr>
 <td align="right">Jagdboote:&nbsp;&nbsp;</td>
 <td><input type="Text" name="j" size="10"></td>
</tr>
<tr>
 <td align="right">Zerris:&nbsp;&nbsp;</td>
 <td><input type="Text" name="z" size="10"></td>
</tr>
<tr>
 <td align="right">Kreuzer:&nbsp;&nbsp;</td>
 <td><input type="Text" name="k" size="10"></td>
</tr>
<tr>
 <td align="right">Schlachter:&nbsp;&nbsp;</td>
 <td><input type="Text" name="s" size="10"></td>
</tr>
<tr>
 <td align="right">Transe:&nbsp;&nbsp;</td>
 <td><input type="Text" name="t" size="10"></td>
</tr>
<tr>
 <td align="right">Träger:&nbsp;&nbsp;</td>
 <td><input type="Text" name="traeger" size="10"></td>
</tr>
<tr>
 <td align="right">Jägergarnison:&nbsp;&nbsp;</td>
 <td><input type="Text" name="deff1" size="10"></td>
</tr>
<tr>
 <td align="right">Raketenturm:&nbsp;&nbsp;</td>
 <td><input type="Text" name="deff2" size="10"></td>
</tr>
<tr>
 <td align="right">Laserturm:&nbsp;&nbsp;</td>
 <td><input type="Text" name="deff3" size="10"></td>
</tr>
<tr>
 <td align="right">Autokanonenturm:&nbsp;&nbsp;</td>
 <td><input type="Text" name="deff4" size="10"></td>
</tr>
<tr>
 <td align="right">Plasmaturm:&nbsp;&nbsp;</td>
 <td><input type="Text" name="deff5" size="10"></td>
</tr>
<tr>
 <td align="right">Gebäude:&nbsp;&nbsp;</td>
 <td><input type="Checkbox" name="gebaeude" value="v"></td>
</tr>

<tr>
 <td colspan="2"><input type="Submit" name="ressbtn" value="Ressourcen gutschreiben"></td>
</tr>
</table>
</form>
<?
}
?>
</div>
</body>
</html>
