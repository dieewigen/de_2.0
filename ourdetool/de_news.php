<?php
include "../inccon.php";
include "det_userdata.inc.php";

$page_title = 'News';
$active_nav = 'news';
include "inc.layout.top.php";

$id = req_int('id', -1);
$action = req_str('action');

if (isset($_POST['absenden'])) {
    csrf_require();
    $time = date("Y-m-d H:i:s");
    $betreff = isset($_POST['betreff']) ? htmlspecialchars($_POST['betreff']) : '';
    $nachricht = isset($_POST['nachricht']) ? $_POST['nachricht'] : '';

    $result = mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_overview (typ, betreff, nachricht, time) VALUES (?, ?, ?, ?)",
                      [1, $betreff, $nachricht, $time]);

    if ($result) {
        echo '<div class="flash flash-ok">Nachricht erfolgreich eingetragen</div>';
    } else {
        echo '<div class="flash flash-danger">Fehler beim Eintragen der Nachricht: ' . mysqli_error($GLOBALS['dbi']) . '</div>';
    }
}

if (isset($_POST['edit'])) {
    csrf_require();
    $betreff = isset($_POST['betreff']) ? htmlspecialchars($_POST['betreff']) : '';
    $nachricht = isset($_POST['nachricht']) ? $_POST['nachricht'] : '';

    $result = mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_news_overview SET betreff=?, nachricht=?, time=time WHERE id=?",
                      [$betreff, $nachricht, $id]);

    if ($result) {
        echo '<div class="flash flash-ok">Nachricht erfolgreich editiert</div>';
    } else {
        echo '<div class="flash flash-danger">Fehler beim Editieren der Nachricht</div>';
    }
}

if ($action == "del") {
    csrf_require();
    $result = mysqli_execute_query($GLOBALS['dbi'], "DELETE FROM de_news_overview WHERE id=?", [$id]);

    if ($result) {
        echo '<div class="flash flash-ok">Nachricht erfolgreich gel&ouml;scht</div>';
    } else {
        echo '<div class="flash flash-danger">Fehler beim Löschen der Nachricht</div>';
    }
}

if ($action == "aendern") {

    $result_news_edit = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_news_overview WHERE id=?", [$id]);

    if (!$result_news_edit) {
        die("Fehler beim Abrufen der Nachricht zum Bearbeiten: " . mysqli_error($GLOBALS['dbi']));
    }

    $row = mysqli_fetch_assoc($result_news_edit);

    if (!$row) {
        die("Keine Nachricht mit dieser ID gefunden.");
    }

    $betreff = htmlspecialchars($row['betreff']);
    $nachricht = htmlspecialchars($row['nachricht']);
    $zeit = htmlspecialchars($row['time']);

    echo '<form action="de_news.php?id='.$id.'" method="post">
  '.csrf_field().'
  <table>
  <tr>
    <th colspan="2">News bearbeiten</th>
  </tr>
  <tr>
    <td>Betreff:</td>
    <td><input type="text" name="betreff" maxlength="50" size="40" value="'.$betreff.'"></td>
  </tr>
  <tr>
    <td valign="top">Nachricht:</td>
    <td><textarea name="nachricht" cols="50" rows="10">'.$nachricht.'</textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="edit" value="Nachricht speichern">&nbsp;&nbsp;&nbsp;<input type="reset" value="Felder leeren"></td>
  </tr>
</table>
<input type="hidden" name="time" value="'.$zeit.'">
</form>';

}

if ($action != "aendern") {
    ?>

<form action="de_news.php" method="post">
<?= csrf_field() ?>
<table>
  <tr>
    <th colspan="2">News eintragen</th>
  </tr>
  <tr>
    <td>Betreff:</td>
    <td><input type="text" name="betreff" maxlength="50" size="40"></td>
  </tr>
  <tr>
    <td valign="top">Nachricht:</td>
    <td><textarea name="nachricht" cols="100" rows="10"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="absenden" value="Nachricht eintragen">&nbsp;&nbsp;&nbsp;<input type="reset" value="Felder leeren"></td>
  </tr>
</table>
</form>
<h2>Nachrichten</h2>
<table border="0" width="750">
  <?php

      $result_news = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_news_overview WHERE typ=? ORDER BY id DESC", [1]);

    if (!$result_news) {
        echo '<tr><td>Fehler beim Abrufen der Nachrichten: ' . mysqli_error($GLOBALS['dbi']) . '</td></tr>';
    }

    while ($row = mysqli_fetch_assoc($result_news)) {

        $t = $row['time'];
        $time = $t[8].$t[9].'.'.$t[5].$t[6].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[11].$t[12].':'.$t[14].$t[15].':'.$t[17].$t[18];


        $nachricht = nl2br(htmlspecialchars($row['nachricht']));
        $betreff = htmlspecialchars($row['betreff']);
        $row_id = (int)$row['id'];
        $klicks = (int)$row['klicks'];

        echo '<tr><td>
  <table border="0" width="100%">
  <tr><td><b>Betreff:</b> '.$betreff.'</td><td align="center" width="170"><b>Zeit:</b> '.htmlspecialchars($time).'</td><td align="center" width="90">Klicks: '.$klicks.'</td><td align="center" width="160"><a href="'.csrf_url('de_news.php?id='.$row_id.'&action=del').'" data-confirm="Möchtest du die Nachricht wirklich löschen?" class="r">l&ouml;schen</a>&nbsp;&nbsp;&nbsp;<a href="de_news.php?id='.$row_id.'&action=aendern">bearbeiten</a></td></tr>
  <tr><td colspan="4"><hr>'.$nachricht.'</td></tr></table></td></tr>';
    }


    ?>

</table>
<?php
}

include "inc.layout.bottom.php";
