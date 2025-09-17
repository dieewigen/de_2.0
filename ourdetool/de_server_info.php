<?php
include "../inccon.php";
?>
<html>

<head>
    <?php include "cssinclude.php"; ?>
</head>

<body>
    <form action="de_server_info.php" method="post">
        <div align="center">
            <?php

            include "det_userdata.inc.php";

            if (isset($_REQUEST['savemeldung'])) {
                mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET server_information=? LIMIT 1", [$_REQUEST['server_information'] ?? '']);
                echo '<div class="info_box text3">Die Meldungen wurden gespeichert.</div><br>';

            }

            $deSystemResult = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system LIMIT 1");
            $deSystem = mysqli_fetch_assoc($deSystemResult);

            echo '<br><br><h4>Informationen zum Server</h4>';

            echo '<textarea name="server_information" cols="100" rows="20">' . $deSystem['server_information'] . '</textarea>';

            echo '<br><br><input type="Submit" name="savemeldung" value="Meldungen speichern">';

            ?>
    </form>
</body>

</html>