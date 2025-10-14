<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\PlayerAttackInfo;

class GetPlayerAttackInfo
{
    const string GET_PLAYER_INFO_SQL = "SELECT sector, `system`, score, fleetscore, col, rasse, ally_id FROM de_user_data where user_id = ?";
    const string GET_PLAYER_INFO_BY_COORDS_SQL = "SELECT user_id, sector, `system`, score, fleetscore, col, rasse, ally_id FROM de_user_data where sector = ? and  `system` = ?";
    const string GET_SEC_RANK_SQL = "SELECT platz FROM de_sector where sec_id = ?";
    const string GET_PlAYER_SECTORS_SQL = "SELECT sec_id FROM de_sector WHERE npc = 0 AND platz > 0";
    const string GET_MAX_COLLECTORS = "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc = 0";

    /**
     * Retrieves player info from the database.
     *
     * @return PlayerAttackInfo the basic player information
     */
    public function getPlayerAttackInfo(int $npcId, int $playerId): PlayerAttackInfo
    {
        $npcRow = $this->getPlayerInfo($npcId);
        $playerRow = $this->getPlayerInfo($playerId);
        $canBeAttacked = $this->canBeAttacked($npcRow['sector'], $npcRow['col'], $npcRow['score'],$npcRow['ally_id'], $playerRow['sector'], $playerRow['col'], $playerRow['score'], $playerRow['ally_id']);
        return new PlayerAttackInfo($playerId, $playerRow['sector'], $playerRow['system'], $playerRow['score'], $playerRow['fleetscore'], $playerRow['col'], $playerRow['rasse'], $playerRow['ally_id'], $canBeAttacked);
    }

    public function getPlayerAttackInfoByCoords(int $npcId, int $sector, int $system): PlayerAttackInfo
    {
        $npcRow = $this->getPlayerInfo($npcId);
        $playerRow = $this->getPlayerInfoByCoords($sector, $system);
        $canBeAttacked = $this->canBeAttacked($npcRow['sector'], $npcRow['col'], $npcRow['score'], $npcRow['ally_id'], $playerRow['sector'], $playerRow['col'], $playerRow['score'], $playerRow['ally_id']);
        return new PlayerAttackInfo($playerRow['user_id'], $playerRow['sector'], $playerRow['system'], $playerRow['score'], $playerRow['fleetscore'], $playerRow['col'], $playerRow['rasse'], $playerRow['ally_id'], $canBeAttacked);
    }

    public function getSectorRank(int $sector): int
    {
        $stmt = mysqli_prepare($GLOBALS['dbi'], self::GET_SEC_RANK_SQL);
        $stmt->bind_param("i", $sector);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['platz'];
    }

    private function getPlayerSectorCount(): int {
        $mysqli_result = mysqli_execute_query($GLOBALS['dbi'], self::GET_PlAYER_SECTORS_SQL);
        $sectorCount = mysqli_num_rows($mysqli_result);
        if ($sectorCount < 1) {
            return 1;
        } else {
            return $sectorCount;
        }
    }

    private function canBeAttacked(int $npcSector, int $npcCollectors, int $npcPoints, int $npcAlly,
                                   int $playerSector, int $playerCollectors, int $playerPoints, int $playerAlly): bool
    {
        global $sv_sector_attmalus, $sv_attgrenze, $sv_attgrenze_whg_bonus, $sv_max_col_attgrenze, $sv_min_col_attgrenze;
        if ($npcSector == $playerSector || $npcAlly != 0 && $npcAlly == $playerAlly) {
            return false;
        }
        $npcSectorRank = $this->getSectorRank($npcSector);
        $playerSectorRank = $this->getSectorRank($playerSector);
        $rankDifference =  $playerSectorRank - $npcSectorRank;
        if ($rankDifference < 0) {
            $rankDifference = 0;
        }
        $playerSectorCount = $this->getPlayerSectorCount();
        $sec_malus = $sv_sector_attmalus / $playerSectorCount * $rankDifference;
        if ($sec_malus>$sv_sector_attmalus) {
            $sec_malus=$sv_sector_attmalus;
        }
        $sec_angriffsgrenze = $sv_attgrenze - $sv_attgrenze_whg_bonus + $sec_malus;

        $mysqli_result = mysqli_execute_query($GLOBALS['dbi'], self::GET_MAX_COLLECTORS);
        $row = mysqli_fetch_assoc($mysqli_result);
        $maxCollectors = $row['maxcol'];
        if ($maxCollectors == 0) {
            $maxCollectors = 1;
        }
        $col_angriffsgrenze = $npcCollectors * 100 / $maxCollectors;
        $col_angriffsgrenze_final = $col_angriffsgrenze / 100 * $sv_max_col_attgrenze;
        if($col_angriffsgrenze_final > $sv_max_col_attgrenze) {
            $col_angriffsgrenze_final = $sv_max_col_attgrenze;
        }
        if ($col_angriffsgrenze_final < $sv_min_col_attgrenze) {
            $col_angriffsgrenze_final = $sv_min_col_attgrenze;
        }
        if ($npcPoints * $sec_angriffsgrenze <= $playerPoints && $npcCollectors * $col_angriffsgrenze_final <= $playerCollectors) {
            return true;
        } else {
            return false;
        }
    }

    public function getPlayerInfo(int $id): array
    {
        $stmt = mysqli_prepare($GLOBALS['dbi'], self::GET_PLAYER_INFO_SQL);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?? [];
    }

    public function getPlayerInfoByCoords(int $sector, int $system): array
    {
        $stmt = mysqli_prepare($GLOBALS['dbi'], self::GET_PLAYER_INFO_BY_COORDS_SQL);
        $stmt->bind_param("ii", $sector, $system);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?? [];
    }

}
