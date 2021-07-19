<?php
$eftachatbotdefensedisable=1;
include('inc/header.inc.php');
include('functions.php');
include('inc/lang/'.$sv_server_lang.'_eftastart.lang.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>EFTA</title>
<?php include('cssinclude.php'); ?>
</head>
<body>
<?php
echo '<div align="center">';

//efta aktivieren
if(isset($_POST['eftaactivate']))
{
  mysql_query("UPDATE de_user_data SET useefta=1 WHERE user_id = '$ums_user_id'",$db);
  $_SESSION['ums_useefta']=1;
}

//quicklink-string zusammenbauen
if($_SESSION['ums_chatoff']) $qlstr="top.document.getElementById('gf').cols = '205, *, 0, 0';top.document.getElementById('gf').rows = '*';";
else $qlstr="top.document.getElementById('gf').cols = '205, 630, *, 0, 0';top.document.getElementById('gf').rows = '*';";


//rahmen oben
echo '<br>';
rahmen_oben($eftastart_lang['willkommen']);

echo '<table width=580>';
//startlinks
$bg='cell';
echo '<tr align="center">
     <td class="'.$bg.'"><b><a href="eftaindex.php" >'.$eftastart_lang['eftastarten'].'</a> <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="Empfohlene Browser&F&uuml;r die Verwendung von EFTA werden folgende Browser in der jeweils aktuellen Version empfohlen:<br>Firefox, Chrome, Internet Explorer, Opera"></b></td>
     </tr>';

echo '<tr align="center">
     <td class="'.$bg.'"><b><a href="#" onclick="'.$qlstr.'">'.$eftastart_lang['kommandozentrale'].'</a></b></td>
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
