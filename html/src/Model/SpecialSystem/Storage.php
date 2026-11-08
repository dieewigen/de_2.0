<?php
namespace DieEwigen\DE2\Model\SpecialSystem;

/**
 * Lagerzugriffe für die Spezialsysteme. Abbuchen nur bedingt (wie im SiegelService), damit der Bestand
 * auch bei gleichzeitigen Anfragen nie negativ wird.
 */
class Storage
{
    public static function getStock(\mysqli $db, int $uid, int $itemId): int
    {
        $res = mysqli_execute_query($db, "SELECT SUM(item_amount) AS anz FROM de_user_storage WHERE user_id = ? AND item_id = ?", [$uid, $itemId]);
        return (int)floor((float)(mysqli_fetch_assoc($res)['anz'] ?? 0));
    }

    /**
     * Bucht ab, wenn genug vorhanden ist.
     */
    public static function take(\mysqli $db, int $uid, int $itemId, int $amount): bool
    {
        if ($amount <= 0) {
            return true;
        }
        mysqli_execute_query($db, "UPDATE de_user_storage SET item_amount = item_amount - ? WHERE user_id = ? AND item_id = ? AND item_amount >= ? LIMIT 1", [$amount, $uid, $itemId, $amount]);
        return mysqli_affected_rows($db) === 1;
    }

    public static function give(int $uid, int $itemId, int $amount): void
    {
        if ($amount > 0) {
            \change_storage_amount($uid, $itemId, $amount);
        }
    }

    /**
     * @return array<int,string> item_id => Name
     */
    public static function getItemNames(\mysqli $db): array
    {
        $res = mysqli_execute_query($db, "SELECT item_id, item_name FROM de_item_data", []);
        $names = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $names[(int)$row['item_id']] = (string)$row['item_name'];
        }
        return $names;
    }
}
