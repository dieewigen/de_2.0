<?php
include "../inccon.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?php

include "det_userdata.inc.php";

echo '<br><h4>Battleround starten</h4>';
echo '<div>&Uuml;ber diese Funktion werden die Rohstoffe/Werte für die BR gesetzt.</div>';
echo '<div style="font-size: 20px; color: #FF0000;">ACHTUNG: Diese Funktion nur nach R&uuml;cksprache nutzen und dann auch nur einmalig aufrufen.</div>';

if($_REQUEST['doit']==1){
	
	mysql_query("UPDATE de_user_data SET tick=tick+2500000, sm_rboost=0, restyp01=restyp01+9000000000, restyp02=restyp02+4500000000, restyp03=restyp03+1000000000, restyp04=restyp04+500000000, restyp05=restyp05+100000, col=col+10000 WHERE npc=0 AND sector>1;");
	mysql_query("UPDATE de_user_data SET techs='s1111111111111111111111111111110000000001111111111111111111111111111111111110000000000000000000000000000000000' WHERE npc=0;");

	echo 'Done. Ggf. m&uuml;ssen noch die Ticks gestartet werden.';
	
}else echo '<br><br><a href="startbr.php?doit=1>">BR starten</a>';


?>
</body>
</html>
