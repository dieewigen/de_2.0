<?php
include "../inc/sv.inc.php";
include "../functions.php";
include "../inc/env.inc.php";

// Stelle sicher, dass eine Datenbankverbindung vorhanden ist
if (!isset($GLOBALS['dbi'])) {
    $GLOBALS['dbi'] = mysqli_connect(
        $GLOBALS['env_db_dieewigen_host'], 
        $GLOBALS['env_db_dieewigen_user'], 
        $GLOBALS['env_db_dieewigen_password'], 
        $GLOBALS['env_db_dieewigen_database']
    );
}
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

if(isset($_REQUEST['doit']) && $_REQUEST['doit']==1){
	
	$result = mysqli_execute_query($GLOBALS['dbi'],
		"UPDATE de_user_data SET 
			tick = tick + ?, 
			sm_rboost = 0, 
			restyp01 = restyp01 + ?, 
			restyp02 = restyp02 + ?, 
			restyp03 = restyp03 + ?, 
			restyp04 = restyp04 + ?, 
			restyp05 = restyp05 + ?, 
			col = col + ? 
		WHERE npc = 0 AND sector > 1",
		[2500000, 9000000000, 4500000000, 1000000000, 500000000, 100000, 10000]
	);

	if ($result) {
		echo 'Done. Ggf. m&uuml;ssen noch die Ticks gestartet werden.';
	} else {
		echo 'Fehler beim Ausführen der Abfrage: ' . mysqli_error($GLOBALS['dbi']);
	}
	
}else echo '<br><br><a href="startbr.php?doit=1">BR starten</a>';


?>
</body>
</html>
