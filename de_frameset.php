<?php
include "inc/header.inc.php";

$gamename='Die Ewigen';

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>'.$gamename.' - '.$sv_server_tag.' - '.$sv_server_name.'</title>';
 echo '</head>';

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//de-frameset ausgeben
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

$_SESSION["de_frameset"]=1;

if($_SESSION["ums_chatoff"]==1){ //ohne chat
	echo '<frameset ID="gf" framespacing="0" border="0" cols="209,*,0,0" frameborder="0">';
	echo '<frame name="Inhalt" target="h" src="menu.php" noresize marginwidth="0" marginheight="0">';
	echo '<frame name="h" src="overview.php" noresize target="_blank">';
	echo '</frameset>';
	}else{ //mit chat
	echo '<frameset ID="gf" framespacing="0" border="0" cols="209,620,*,0,0" frameborder="0">';
	echo '<frame name="Inhalt" target="h" src="menu.php" noresize marginwidth="0" marginheight="0">';
	echo '<frame name="h" src="overview.php" noresize target="_blank">';
	echo '<frame name="c" src="chat.php?frame=1" noresize target="_blank">';
	echo '</frameset>';	
}


echo '
<noframes>
<body>
<p>Frames werden nicht unterst&uuml;tz</p>
</body>
</noframes>
</html>';