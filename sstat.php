<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_sstat.lang.php";
?>
<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
<title><?php echo $sstat_lang['title']?></title>
<?php
$ums_rasse=1;
$ums_gpfad=$sv_image_server_list[0];
include "cssinclude.php";
?>
</head>
<body>
<center>
<table border="0" cellpadding="0" cellspacing="0">
<tr align="center">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="650" align="center" class="ro"><?php echo $sstat_lang['serverstatistik']?></td>
<td width="13" class="ror">&nbsp;</td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0">
<?php
include "cache/loginstat.tmp"
?>
<tr class="cellu">
<td width="13" class="rul">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td width="13" class="rur">&nbsp;</td>
</tr>
</table>
<br><a href="index.php"><?=$sstat_lang['zurueck']?></a>
</body>
</html>
