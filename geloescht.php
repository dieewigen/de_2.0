<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_geloescht.lang.php";
?>
<!doctype html>
<html>
<script language="JavaScript">
if(top.frames.length > 0)
top.location.href=self.location;
</script>
<head>
<title><?php echo $gel_lang['title'];?></title>
<link href="https://www.die-ewigen.com/default.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
	@import url("https://www.die-ewigen.com/layout.css");
-->
</style>
</head>
<body>
<div align="center"><br>
<br>
<b><?php echo $gel_lang['accountloeschung']?></b>
<table border="0" cellpadding="0" cellspacing="0" width="600">

<tr align="center"><td colspan="4" align="center"><h1 id="title5">&nbsp;</h1></td></tr>
<tr align="center"><td>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr align="center"><td><br><br>
<font color="#00FF00"><?php echo $gel_lang['msg1']?><br><br><br><?php echo $gel_lang['msg2']?><br><br><br><br><font color="#FF0000"><?php echo $gel_lang['msg3']?><br><br><br><br><br>
</td></tr>
</table>

</td></tr>
<tr>
<td><div class="hr1"><div><hr></div></div></td></tr>
</table>

</div>
</body>
</html>