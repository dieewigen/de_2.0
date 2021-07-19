<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>Die Ewigen - Admintool</title>
</head>

<frameset framespacing="0" border="0" rows="60,*" frameborder="0">
  <frame name="de_user" src="de_user_search.php<?php if (isset($_REQUEST['UID'])) { echo "?sstr=+".$_REQUEST['UID']; } ?>" noresize marginwidth="0" marginheight="0">
  <frame name="de_user_anzeige" src="empty.php" target="_blank">
</frameset>

<noframes>
<body>

<p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht unterstützt.</p>
</body>
</noframes>
</html>
