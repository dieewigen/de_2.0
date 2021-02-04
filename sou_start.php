<?php
$eftachatbotdefensedisable=1;
include "inc/header.inc.php";
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_sou_start.lang.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Site</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<div align="center">
<?php
$btip1 = $soustart_lang[start4a].'&'.$soustart_lang[start4b];

//efta aktivieren
if ($_POST["eftaactivate"])
{
  mysql_query("UPDATE de_user_data SET useefta=1 WHERE user_id = '$ums_user_id'",$db);
  $_SESSION["ums_useefta"]=1;
}

echo '<form action="eftastart.php" method="POST">';

//echo '<h1>'.$soustart_lang[willkommen].'</h1>';

//quicklink-string zusammenbauen
if($_SESSION["ums_chatoff"]) $qlstr="top.document.getElementById('gf').cols = '205, *, 0, 0';top.document.getElementById('gf').rows = '*';";
else $qlstr="top.document.getElementById('gf').cols = '205, 630, *, 0, 0';top.document.getElementById('gf').rows = '*';";


//rahmen oben
echo '<br>';
rahmen_oben($soustart_lang[willkommen].' <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$btip1.'">');

echo '<table width=580>';
//startlinks
$bg='cell';
echo '<tr align="center">
        <td class="'.$bg.'"><b><a href="sou_index.php" >'.$soustart_lang[starten].'</a></b></td>
      </tr>';
echo '<tr align="center">
        <td class="'.$bg.'"><b><a href="#" onclick="'.$qlstr.'">'.$soustart_lang[kommandozentrale].'</a></b></td>
     </tr>';
echo '<tr align="center">
        <td class="'.$bg.'"><font color="#FF0000"><b>Achtung</b></font><br>Die Erweiterte Arch&auml;ologie (EA) befindet sich noch im Beta-Stadium, was bedeutet, dass es h&auml;ufiger zu Ver&auml;nderungen und Resets/Teilresets kommen wird. Das bedeutet, dass Errungenschaften im Spiel verlorengehen k&ouml;nnen. Ich bitte alle Spieler, die nicht in der Lage sind bei einer Beta-Version mitzuspielen, sich zu &uuml;berlegen hier erst zu spielen, wenn eine stabile Version erreicht ist, welche weniger Ver&auml;nderungen/Resets/Teilresets ben&ouml;tigt.<br>Vielen Dank</td>
     </tr>';
echo '</table>';

//rahmen unten
rahmen_unten();

echo '</form>';

?>
</div>
<script>
$(document).ready(function () {
$("div, img, a, td").tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: "&",
    extraClass: "design1", 
    fixPNG: true,
    opacity: 0.15,
    left: 0
});
});
</script>
</body>
</html>
