<?PHP
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
  $db_daten=mysql_query("SELECT user_id, restyp01, restyp02, restyp03, restyp04, restyp05, col, agent, e100, e101, e102, e103, e104  FROM de_user_data");


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
    $kol = $row[col] + $kollie;
    $age = $row[agent] + $agent;
    $ndeff1= $row[e100]+$deff1;
    $ndeff2= $row[e101]+$deff2;
    $ndeff3= $row[e102]+$deff3;
    $ndeff4= $row[e103]+$deff4;
    $ndeff5= $row[e104]+$deff5;


    echo "<tr><td>$res01</td><td>$res02</td><td>$res03</td><td>$res04</td><td>$res05</td><td>$kollie</td></tr>";

    mysql_query("update de_user_data set restyp01 = $res01, restyp02 = $res02, restyp03 = $res03, restyp04 = $res04, restyp05 = $res05, col=$kol, agent=$age, e100=$ndeff1, e101=$ndeff2, e102=$ndeff3, e103=$ndeff4, e104=$ndeff5 where user_id='$uid'");

    if($gebaeude=="v")
    {
      mysql_query("update de_user_data set techs='s1111111111111111111111111100000000000001111111111111111111111111111111111110000000000000000000000000000000000'");
    }

    $schiffsid = $uid.'-0';

    $homeschiffe = mysql_query("select e81, e82, e83, e84, e85, e86, e87, e88 from de_user_fleet where user_id = '$schiffsid'");
    $rew=mysql_fetch_array($homeschiffe);

    $na = $rew[e81] + $n;
    $ja = $rew[e82] + $j;
    $za = $rew[e83] + $z;
    $ka = $rew[e84] + $k;
    $sa = $rew[e85] + $s;
    $ta = $rew[e87] + $t;
    $tr = $rew[e88] + $traeger;

    $fleet_id = $fleet_id.'-0';
    mysql_query("update de_user_fleet set e81 = $na, e82 = $ja, e83 = $za, e84 = $ka, e85 = $sa, e87 = $ta, e88 = '$tr' where user_id = '$schiffsid'");

    }
    echo "</table>";
    echo '<h1>Transfer successfull <br> ==> <a href="pushall.php">weiter</a> <== </h1>';
}
else
{
?>
<form action="pushall.php" method="post">
<table border="0">
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
 <td colspan="2"><input type="Submit" name="ressbtn" value="den ganzen Plunder gutschreiben"></td>
</tr>
</table>
</form>
<?
}
?>
</div>
</body>
</html>
