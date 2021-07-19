<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_server_info.php" method="post">
<div align="center">
<?php

include "det_userdata.inc.php";

if ($savemeldung)
{
  $filename="../cache/overview.inc.php";
  $cachefile = fopen ($filename, 'w');

  $str='<?php $detmsg="'.$dettext.'";$kpmsg="'.$kptext.'";$galname="'.$galtext.'";?>';

  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);
}
?>
<?php


echo '<br><br><h4>Server-Meldung</h4>';

include "../cache/overview.inc.php";

echo '<textarea name="dettext" cols="70" rows="10">'.$detmsg.'</textarea>';
echo '<br><br><h4>Kristallpalast-Meldung</h4><textarea name="kptext" cols="70" rows="10">'.$kpmsg.'</textarea><br>';
//echo '<br><br><h4>Name der Galaxie</h4><input type="text" name="galtext" value="'.$galname.'"><br><br>';
echo '<input type="Submit" name="savemeldung" value="Meldungen speichern">';

?>
</form>
</body>
</html>
