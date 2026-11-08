<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'Server-Meldung/Information';
$active_nav = 'server';
include "inc.layout.top.php";

if (isset($_REQUEST['savemeldung'])) {
    csrf_require();
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET server_information=? LIMIT 1", [req_str('server_information')]);
    echo '<div class="flash flash-ok">Die Meldungen wurden gespeichert.</div>';
}

$deSystemResult = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system LIMIT 1");
$deSystem = mysqli_fetch_assoc($deSystemResult);

echo '<h4>Informationen zum Server</h4>';

echo '<form action="de_server_info.php" method="post">';
echo csrf_field();
echo '<textarea name="server_information" cols="100" rows="20">' . htmlspecialchars((string)$deSystem['server_information']) . '</textarea>';
echo '<br><br><input type="submit" name="savemeldung" value="Meldungen speichern">';
echo '</form>';

include "inc.layout.bottom.php";
