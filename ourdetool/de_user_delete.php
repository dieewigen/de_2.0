<?php
include "../inccon.php";
include "det_userdata.inc.php";

$uid = req_int('uid');

// POST-Zweig: Löschung ausführen (mit CSRF-Schutz), Logik wie bisher
$delete_done = false;
$delete_error = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    csrf_require();

    if ($uid > 0) {
        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_login SET status=2, last_login='0000-00-00 00:00:00' WHERE user_id=?",
            [$uid]
        );

        mysqli_execute_query(
            $GLOBALS['dbi'],
            "UPDATE de_user_data SET premium=0 WHERE user_id=?",
            [$uid]
        );

        $delete_done = true;
    } else {
        $delete_error = true;
    }
}

$page_title = 'Spieler löschen';
$active_usertab = 'de_user_delete';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

if ($delete_done) {
    echo '<div class="flash flash-ok">Der Spieler wurde dem Inaktivenscript zur L&ouml;schung &uuml;bergeben.</div>';
} elseif ($delete_error) {
    echo '<div class="flash flash-danger">Fehler: Keine g&uuml;ltige Benutzer-ID angegeben.</div>';
} else {
    // GET: Bestätigungsseite anzeigen
    echo '<form action="de_user_delete.php" method="post" data-confirm="Spieler ' . $uid . ' wirklich l&ouml;schen?">';
    echo csrf_field();
    echo '<p>Durch das Best&auml;tigen des Buttons wird der Spieler gel&ouml;scht. Voraussetzung f&uuml;r die L&ouml;schung ist ein aktives Inaktivenscript.</p>';
    echo '<input type="hidden" name="uid" value="' . $uid . '">';
    echo '<button type="submit" name="delete" value="1" class="btn-danger">Spieler l&ouml;schen</button>';
    echo '</form>';
}

include "inc.layout.bottom.php";
