<?php
namespace DieEwigen\DE2\Model;

//Nachricht an einen Spieler (de_user_news) mit Hinweis auf neue Nachrichten (newnews); früher notifyUser() in
//lib/basefunctions.lib.php. $typ ist die Nachrichtenart aus sysnews.php (6 = Allianz).
class Nachricht
{
    public static function anSpieler(\mysqli $db, $id, $text, $typ): void
    {
        if ($id > 0) {
            mysqli_execute_query($db, "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, ?, ?, ?)", [$id, $typ, date("YmdHis"), $text]);
            mysqli_execute_query($db, "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$id]);
        }
    }
}
