<?php
include "inc/header.inc.php";

echo '<!DOCTYPE html>
<html lang="de">
<head>
<title>'.$sv_server_tag.' - DIE EWIGEN - '.$sv_server_name.'</title>';
 echo '</head>';

//////////////////////////////////////////////////////////////
//de-frameset ausgeben
//////////////////////////////////////////////////////////////

$_SESSION["de_frameset"]=1;

echo '<frameset ID="gf" framespacing="0" border="0" cols="209,620,*,0,0" frameborder="0">';
echo '<frame name="Inhalt" target="h" src="menu.php" noresize marginwidth="0" marginheight="0">';
echo '<frame name="h" src="overview.php" noresize target="_blank">';
echo '<frame name="c" src="chat.php?frame=1" noresize target="_blank">';
echo '</frameset>';	

/*
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
*/
echo '
</html>';
