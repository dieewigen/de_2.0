<?php
include "inc/sv.inc.php";
include "inc/lang/".$sv_server_lang."_urlaub.lang.php";
?>
<!doctype html>
<html>
<script>
if(top.frames.length > 0)
top.location.href=self.location;
</script>
<head>
<title><?=$urlaub_lang['title']?></title>
<?php
//$ums_rasse=1;
//$ums_gpfad=$sv_image_server_list[0];
//include "cssinclude.php";
?>
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
<b><?=$urlaub_lang['urlaubsmodus']?></b>
<table border="0" cellpadding="0" cellspacing="0" width="600">

<tr align="center"><td colspan="4" align="center"><h1 id="title5">&nbsp;</h1></td></tr>
<tr align="center"><td>
<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr align="center"><td>
<br><br><br><br><br><br><br><br><br><br>
<b><?=$urlaub_lang['msg1']?></b>
<br><br><br><br><br><br><br><br><br><br><br>
</td></tr>
</table>

</td></tr>
<tr>
<td><div class="hr1"><div><hr></div></div></td></tr>
</table>

</div>

</body>
</html>
