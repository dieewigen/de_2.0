<?php
include "inc/header.inc.php";
include "inc/artefakt.inc.php";
include "inc/lang/".$sv_server_lang."_help.lang.php";

$db_daten = mysqli_execute_query($GLOBALS['dbi'],
  "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans FROM de_user_data WHERE user_id=?",
  [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$sector = $row["sector"];
$system = $row["system"];

include "functions.php";
?>
<!doctype html>
<html>
<head>
<title><?php echo $help_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
include "resline.php";?><br>
<?php
if (isset($_GET["t"])) {
    $t = intval($_GET["t"]);
    $db_daten = mysqli_execute_query($GLOBALS['dbi'],
      "SELECT tech_name, des FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=?",
      [$t]);
    $row = mysqli_fetch_assoc($db_daten);
    $tech_name = $row["tech_name"];
    $des = $row["des"];

    echo '
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="500" align="center" class="ro"><div class="cellu">'.$tech_name.'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td width="500" align="center"><div class="cell">'.$des.'</div></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>';
}

if ($_GET["a"]) {
    //echo '<a href="javascript:history.back();">'.$help_lang['zurueck'].'</a><br><br>';
    $a = (int)$_GET["a"];
    $artresult = mysqli_execute_query($GLOBALS['dbi'], 
      "SELECT id, artname, artdesc, color FROM de_artefakt ORDER by id");
    while ($row = mysqli_fetch_assoc($artresult)) {

        $desc = $row["artdesc"];
        $desc = str_replace("{WERT1}", number_format($sv_artefakt[$row["id"] - 1][0], 2, ",", "."), $desc);
        $desc = str_replace("{WERT2}", number_format($sv_artefakt[$row["id"] - 1][1], 0, "", "."), $desc);
        $desc = str_replace("{WERT3}", number_format($sv_artefakt[$row["id"] - 1][2], 0, "", "."), $desc);
        $desc = str_replace("{WERT4}", number_format($sv_artefakt[$row["id"] - 1][3], 0, "", "."), $desc);
        $desc = str_replace("{WERT5}", number_format($sv_artefakt[$row["id"] - 1][4], 0, "", "."), $desc);
        $desc = str_replace("{WERT6}", number_format($sv_artefakt[$row["id"] - 1][5], 2, ",", "."), $desc);


        echo '
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="500" align="center" class="ro"><div class="cellu">'.$row["artname"].'</div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td width="500" align="center"><div class="cell"><font color="#'.$row["color"].'">'.$desc.'</div></td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table><br>';
    }
}
?>
<br><br>

</body>
</html>