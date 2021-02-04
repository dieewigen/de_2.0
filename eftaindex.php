<?php
$thisisefta=1;
$eftacss=1;
include "eftadata/source/efta_functions.php";
include "inc/header.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Die Ewigen </title>
</head>
<?php
echo '
<frameset framespacing="0" border="0" frameborder="0" rows="*,141">
  <frame name="e" src="eftamain.php" target="e" marginwidth=0 marginheight=0 noresize>
  <frame name="eftachat" src="efta_chat.php?frame=1" marginwidth=0 marginheight=0 noresize>
</frameset>';

?>
<noframes>
<body>
<p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht unterstützt.</p>
</body>
</noframes>
</html>
