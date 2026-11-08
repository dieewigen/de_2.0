<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\NewsEntry;

class GetAttackNews
{

    const string GET_FLEET_STATUS = "SELECT user_id FROM de_user_fleet WHERE user_id like ? and zielsec = ? and zielsys = ? and aktion = 1";
    const string GET_NEWS = "SELECT typ, time, text FROM de_user_news WHERE user_id like ? and typ in (51, 52, 53, 54)";

    /**
     * Fetch news of a targeting player.
     *
     * @param int $playerId the ID of the player which news should be retrieved.
     * @param int $npcId the ID of the NPC attacking the player.
     * @return array an array of news entry containing attacking, defending and recalling fleets
     */
    public function getAttackNews(int $playerId, int $npcId): array
    {
        $userService = new UserService();
        $playerCoords = $userService->getPlayerData($playerId);
        $playerCheckResult = mysqli_execute_query($GLOBALS['dbi'], $this::GET_FLEET_STATUS,["$npcId-%", $playerCoords[0], $playerCoords[1]]);
        $isAttackedByNpc = mysqli_num_rows($playerCheckResult);
        if ($isAttackedByNpc < 1) {
            throw new \Error("NPC not attacking player.");
        }

        $result = mysqli_execute_query($GLOBALS['dbi'], $this::GET_NEWS, [$playerId]);
        $rows = mysqli_fetch_all($result, MYSQLI_BOTH);
        $result = [];
        foreach ($rows as $row) {
            $result[] = new NewsEntry($row['typ'], $row['time'], $row['text']);
        }
        return $result;
    }
}
