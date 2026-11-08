<?php
namespace DieEwigen\DE2\Model;

//Spielersperre für Transaktionen (Tabelle de_user_locks), früher setLock()/releaseLock() in lib/transaction.lib.php:
//eine Seite sperrt den Spieler mit setzen(), arbeitet und gibt ihn mit loesen() frei. Die Tabelle wird je Aufruf kurz
//gesperrt, damit zwei gleichzeitige Anfragen sich nicht überholen. Beide geben false zurück, wenn die Sperre schon
//gesetzt war bzw. die Datenbank nicht mitspielt.
class Sperre
{
    public static function setzen(\mysqli $db, $id): bool
    {
        $id = intval($id);
        $ergebnis = false;
        mysqli_query($db, "LOCK TABLES de_user_locks WRITE");
        $result = mysqli_execute_query($db, "SELECT * FROM de_user_locks WHERE id=?", [$id]);
        if ($result) {
            $anzahl = mysqli_num_rows($result);
            if ($anzahl == 1) {
                $locks = mysqli_fetch_array($result);
                if ($locks["locked"] != "1") {
                    if (mysqli_execute_query($db, "UPDATE de_user_locks SET locked='1' WHERE id=?", [$id])) {
                        $ergebnis = true;
                    }
                }
            } elseif ($anzahl > 1) {
                print("Transactionmanager: An internal error occured [#0001]: dublicate lock definition. Transaction has been cancelled.<br><br>Please open a support ticket.");
            } elseif ($anzahl == 0) {
                if (mysqli_execute_query($db, "INSERT into de_user_locks (id, locked) VALUES (?, '1')", [$id])) {
                    $ergebnis = true;
                }
            }
        }
        mysqli_query($db, "UNLOCK TABLES");

        return $ergebnis;
    }

    public static function loesen(\mysqli $db, $id): bool
    {
        $id = intval($id);
        $ergebnis = false;
        mysqli_query($db, "LOCK TABLES de_user_locks WRITE");
        $result = mysqli_execute_query($db, "SELECT * FROM de_user_locks WHERE id=?", [$id]);
        if ($result) {
            $anzahl = mysqli_num_rows($result);
            if ($anzahl == 1) {
                if (mysqli_execute_query($db, "UPDATE de_user_locks SET locked='0' WHERE id=?", [$id])) {
                    $ergebnis = true;
                }
            } elseif ($anzahl > 1) {
                print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Please open a support ticket.");
            } elseif ($anzahl == 0) {
                if (mysqli_execute_query($db, "INSERT into de_user_locks (id, locked) VALUES (?, '0')", [$id])) {
                    $ergebnis = true;
                }
            }
        }
        mysqli_query($db, "UNLOCK TABLES");

        return $ergebnis;
    }
}
