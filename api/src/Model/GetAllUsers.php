<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\Player;
use DieEwigen\Api\Types\Resources;

class GetAllUsers
{
    /**
     * Retrieves all NPC users from the database.
     *
     * @return array An array of associative arrays, each representing a user.
     */
    public function getAllNpcUsers(): array
    {

        $result = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc = 2 ORDER BY sector, `system`;");

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $res = new Resources($row['restyp01'], $row['restyp02'], $row['restyp03'], $row['restyp04'], $row['restyp05']);
            $users[] = new Player($row['user_id'], $row['sector'], $row['system'], $row['spielername'], $row['score'],
                $row['fleetscore'], $res, $row['col'], $row['ally_id'], $row['rasse']);
        }

        return $users;
    }
}
