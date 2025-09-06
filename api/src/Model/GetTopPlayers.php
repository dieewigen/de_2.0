<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\ToplistEntry;

class GetTopPlayers
{

    const string GET_TOPLIST_SQL = "SELECT user_id, sector, `system`, score, fleetscore, col, ehscore FROM de_user_data WHERE sector > 1";

    /**
     * Retrieve players from toplist.
     *
     * @param string $sortTypeParm The type of sorting to apply ('points', 'fleetpoints', 'collectors', 'ehpoints').
     * @return array An array of associative arrays, each representing a value.
     */
    public function getTopList(string $sortTypeParm)
    {
        $sortType = match ($sortTypeParm) {
            'points' => 'score',
            'fleetpoints' => 'fleetscore',
            'collectors' => 'col',
            'ehpoints' => 'ehscore',
            default => 'score',
        };
        $result = mysqli_execute_query($GLOBALS['dbi'], $this::GET_TOPLIST_SQL . " order by $sortType DESC");
        $rows = mysqli_fetch_all($result, MYSQLI_BOTH);
        $result = [];
        foreach ($rows as $row) {
            $result[] = new ToplistEntry($row['user_id'], $row['score'], $row['fleetscore'], $row['col'], $row['ehscore'],
            $row['sector'], $row['system']);
        }
        return $result;
    }
}
