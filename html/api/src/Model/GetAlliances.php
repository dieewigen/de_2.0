<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\AllianceEntry;

class GetAlliances
{

    const string GET_ALLIANCES = "SELECT id, allytag, allyname, leaderid, memberlimit, artefacts, t_depot, bldg0, bldg1, bldg2, bldg3, bldg4, bldg5, bldg6, bldg7, bldg8, bldg9 FROM de_allys";
    const string GET_MEMBER_COUNT = "SELECT user_id FROM de_user_data WHERE ally_id = ? AND status = 1";
    const string GET_LEADER = "SELECT user_id FROM de_user_data WHERE user_id = ? and npc = 2";

    /**
     * Retrieve alliances.
     *
     * @return array an array of alliances.
     */
    public function getAlliances()
    {
        $allyQuery = mysqli_execute_query($GLOBALS['dbi'], $this::GET_ALLIANCES);
        $rows = mysqli_fetch_all($allyQuery, MYSQLI_BOTH);
        $result = [];
        foreach ($rows as $row) {
            $membersQuery = mysqli_execute_query($GLOBALS['dbi'], $this::GET_MEMBER_COUNT, [$row['id']]);
            $membercount = mysqli_num_rows($membersQuery);
            $leaderQuery = mysqli_execute_query($GLOBALS['dbi'], $this::GET_LEADER, [$row['leaderid']]);
            $leaderCount = mysqli_num_rows($leaderQuery);
            $openSlots = $row['memberlimit'] - $membercount;
            if ($leaderCount > 0) {
                // return internal information of npc alliances only
                $artefacts = $row['artefacts'];
                $tronics = $row['t_depot'];
                $buildings = array($row['bldg0'], $row['bldg1'], $row['bldg2'], $row['bldg3'], $row['bldg4'],
                    $row['bldg5'], $row['bldg6'], $row['bldg7'], $row['bldg8'], $row['bldg9']);
                $result[] = new AllianceEntry($row['id'], $row['allytag'], $row['allyname'], true,
                    $openSlots, $row['leaderid'], $artefacts, $tronics, $buildings);
            } else {
                $result[] = new AllianceEntry($row['id'], $row['allytag'], $row['allyname'], false, $openSlots,
                    $row['leaderid']);
            }
        }
        return $result;
    }
}
