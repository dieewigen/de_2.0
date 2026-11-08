<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Battleround starten';
$active_nav = 'server';
include "inc.layout.top.php";

$doit = req_int('doit');

echo '<div>&Uuml;ber diese Funktion werden die Rohstoffe/Werte für die BR gesetzt.</div>';
echo '<div class="flash flash-danger">ACHTUNG: Diese Funktion nur nach R&uuml;cksprache nutzen und dann auch nur einmalig aufrufen.</div>';

if ($doit == 1) {
    csrf_require();

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
        echo '<div class="flash flash-ok">Done. Ggf. m&uuml;ssen noch die Ticks gestartet werden.</div>';
    } else {
        echo '<div class="flash flash-danger">Fehler beim Ausführen der Abfrage: ' . mysqli_error($GLOBALS['dbi']) . '</div>';
    }
} else {
    echo '<form method="post" action="startbr.php" data-confirm="Battleround wirklich starten?">'
        . csrf_field()
        . '<input type="hidden" name="doit" value="1">'
        . '<button type="submit" class="btn-danger">BR starten</button>'
        . '</form>';
}

include "inc.layout.bottom.php";
