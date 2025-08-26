<?php 
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_hfnlegende.lang.php';
?>
<html>
<head>
<title><?php print($legende_lang['eintrag_1']); ?></title>
<?php include "cssinclude.php";?>
<style type="text/css">
<!--
.tab {border-style:none;}
-->
</style>
<script>window.resizeTo(580,540);</script>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
?>
<center>
<table bordercolor="#3399FF" width="570" class="tab">
<tr class="cell">
 <td width=240 align=center><font color=#FFFFFF><b><?php print($legende_lang['eintrag_2']); ?></b></font></td>
 <td width=160 align=center><font color=#FFFFFF><b><?php print($legende_lang['eintrag_3']); ?></b></font></td>
 <td width=155 align=center><font color=#FFFFFF><b><?php print($legende_lang['eintrag_4']); ?></b></font></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[b]<?php print($legende_lang['eintrag_5']); ?>[/b]</td>
 <td><b>&nbsp;<?php print($legende_lang['eintrag_5']); ?></b></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_6']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[u]<?php print($legende_lang['eintrag_5']); ?>[/u]</td>
 <td><u>&nbsp;<?php print($legende_lang['eintrag_5']); ?></u></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_7']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[i]<?php print($legende_lang['eintrag_5']); ?>[/i]</td>
 <td><i>&nbsp;<?php print($legende_lang['eintrag_5']); ?></i></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_8']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[center]<?php print($legende_lang['eintrag_5']); ?>[/center]</td>
 <td><center>&nbsp;<?php print($legende_lang['eintrag_5']); ?></center></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_9']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[pre]Die&nbsp;&nbsp;&nbsp;Ewigen[/pre]</td>
 <td>&nbsp;Die&nbsp;&nbsp;&nbsp;&nbsp;Ewigen</td>
 <td>&nbsp;<?php print($legende_lang['eintrag_10_3']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[link]www.Die-Ewigen.com[/link]</td>
 <td>&nbsp;<a href=http://www.die-ewigen.com>www.Die-Ewigen.com</a></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_12']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[email]support@die-ewigen.com[/email] </td>
 <td>&nbsp;<a href=mailto:support@die-ewigen.com>support@die-ewigen.com</a></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_14']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[img]<?php print($legende_lang['eintrag_15']); ?>[/img]</td>
 <td>&nbsp;<image src=http://forum.bgam.es/DENeu/on.gif></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_16']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[CROT]<?php print($legende_lang['eintrag_5']); ?></td>
 <td>&nbsp;<font color="#FF0000"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_17']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[CGELB]<?php print($legende_lang['eintrag_5']); ?></td>
 <td>&nbsp;<font color="#FFFF00"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_18']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[CGRUEN]<?php print($legende_lang['eintrag_5']); ?></td>
 <td>&nbsp;<font color="#00FF00"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_19']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[CW]<?php print($legende_lang['eintrag_5']); ?></td>
 <td>&nbsp;<font color="#FFFFFF"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_20']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;[color=#XXXXXX]<?php print($legende_lang['eintrag_5']); ?>[/color]</td>
 <td>&nbsp;<font color="#33FFFF"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_21']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;[size=X]<?php print($legende_lang['eintrag_5']); ?>[/size]</td>
 <td>&nbsp;<font size="4"><?php print($legende_lang['eintrag_5']); ?></font></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_22']); ?></td>
</tr>

<tr class="cell1">
 <td>&nbsp;?</td>
 <td>&nbsp;<?php print($legende_lang['eintrag_23']); ?></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_24']); ?></td>
</tr>

<tr class="cell">
 <td>&nbsp;<?php print($legende_lang['eintrag_25']); ?></td>
 <td>&nbsp;<?php print($legende_lang['eintrag_26']); ?></td>
 <td>&nbsp;</td>
</tr>

<tr class="cell1">
 <td colspan="3"><?php print($legende_lang['eintrag_27']); ?></td>
</tr>
</table>
</center>
</body>
</html>