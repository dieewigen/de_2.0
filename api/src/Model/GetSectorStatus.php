<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\SectorFleet;
use DieEwigen\Api\Types\SectorStatus;
use DieEwigen\Api\Types\SectorSystemStatus;
use DieEwigen\Api\Types\SystemFleetStatus;

class GetSectorStatus
{
    const string GET_SECTOR_STATUS_SQL = "SELECT dud_source.user_id AS s_user_id, dud_source.rasse, dud_target.user_id AS t_user_id,
                                    dud_source.ally_id AS s_allyId, dud_target.ally_id AS t_allyId,
                                    duf.zielsec, duf.zielsys, duf.hsec, duf.hsys, duf.zeit, duf.fleetsize,
                                    duf.e81, duf.e82, duf.e83, duf.e83, duf.e84, duf.e85, duf.e86, duf.e87, duf.e88, 
                                    duf.e89, duf.e90, duf.aktion
                                   FROM de_user_fleet duf
                                   JOIN de_user_data dud_source on duf.hsec = dud_source.sector AND duf.hsys = dud_source.`system`
                                   JOIN de_user_data dud_target on duf.zielsec = dud_target.sector AND duf.zielsys = dud_target.`system`
                                   WHERE (zielsec = ? OR (dud_target.ally_id != 0 AND dud_target.ally_id = ? AND dud_target.show_ally_secstatus > ?))
                                   AND entdecktsec = 1 AND (aktion = 1 OR aktion = 2)";

    const string GET_SECTOR_FLEETS_SQL = "SELECT duf.hsec, duf.hsys, duf.aktion, duf.zeit, duf.e81, duf.e82, duf.e83, duf.e83,
                                   duf.e84, duf.e85, duf.e86, duf.e87, duf.e88, duf.e89, duf.e90, dud_source.rasse
                                   FROM de_user_fleet duf
                                   JOIN de_user_data dud_source on duf.hsec = dud_source.sector AND duf.hsys = dud_source.`system`
                                   WHERE duf.hsec = ? AND (aktion = 1 OR aktion = 2 OR aktion = 3)";

    /**
     * Retrieves sector status from the database.
     *
     * @return SectorStatus the sector status array of given user, one item represent the status of one system + sector member fleets.
     */
    public function getSectorStatus(int $userId): SectorStatus
    {
        $userService = new UserService();
        $requestingNpcData = $userService->getPlayerData($userId);
        $stmt = mysqli_prepare($GLOBALS['dbi'], self::GET_SECTOR_STATUS_SQL);
        //bind sector and allyId of requesting player
        $now = time();
        $stmt->bind_param("iii", $requestingNpcData[0], $requestingNpcData[2], $now);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_BOTH);
        $groupedFleetsByTarget = $this->groupFleetByTarget($result);
        $systemStatus = $this->createSystemStatus($groupedFleetsByTarget);
        //fetch outgoing and returning fleets from sector mates.
        $secFleetsStmt = mysqli_prepare($GLOBALS['dbi'], self::GET_SECTOR_FLEETS_SQL);
        $secFleetsStmt->bind_param("i", $requestingNpcData[0]);
        $secFleetsStmt->execute();
        $secFleetsResult = $secFleetsStmt->get_result()->fetch_all(MYSQLI_BOTH);
        $sectorFleets = $this->createSectorFleets($secFleetsResult);
        return new SectorStatus($systemStatus, $sectorFleets);
    }

    private function createSectorFleets($rows): array
    {
        $fleetStatuses = array();
        foreach ($rows as $row) {
            $fleetPoints = $this->calculateFp($row);
            $fleetEntry = new SectorFleet($row['hsec'], $row['hsys'], $row['zeit'], $fleetPoints, $row['aktion']);
            $fleetStatuses[] = $fleetEntry;
        }
        return $fleetStatuses;
    }

    private function calculateFp($row): int
    {
        $race = $row['rasse'];
        $fp = 0;
        for ($s = 81; $s <= 90; $s++) {
            $fp = $fp + $GLOBALS['unit'][$race - 1][$s - 81][4] * $row['e' . $s];
        }
        return $fp;
    }

    private function createSystemStatus($sectorFleetStatusRows): array
    {
        $systemStatus = array();
        foreach ($sectorFleetStatusRows as $targetStr => $systemFleetStatusRow) {
            $attackFleetRows = $this->filterByStatus($systemFleetStatusRow, 1);
            $defendFleetRows = $this->filterByStatus($systemFleetStatusRow, 2);
            $attackFleets = array_map(array($this, 'createFleetStatus'), $attackFleetRows);
            $defendFleets = array_map(array($this, 'createFleetStatus'), $defendFleetRows);
            $target = explode("-", $targetStr); //combined key of the target system-userId-allyId-sector
            $systemStatus[] = new SectorSystemStatus(intval($target[3]), intval($target[0]), intval($target[1]),
                intval($target[2]), $attackFleets, $defendFleets);
        }
        return $systemStatus;
    }

    private function createFleetStatus($fleetRow): SystemFleetStatus
    {
        return new SystemFleetStatus($fleetRow['s_user_id'], $fleetRow['hsec'], $fleetRow['hsys'],
            $fleetRow['s_allyId'], $fleetRow['zeit'],
            $fleetRow['fleetsize'], $this->calculateFp($fleetRow));

    }

    private function groupFleetByTarget(array $rows): array
    {
        $result = array();
        foreach ($rows as $row) {
            $result[$row['zielsys'].'-'.$row['t_user_id'].'-'.$row['t_allyId'].'-'.$row['zielsec']][] = $row;
        }
        return $result;
    }

    private function filterByStatus($rows, int $status): array
    {
        $result = array();
        foreach ($rows as $row) {
            if ($row['aktion'] === $status) {
                $result[] = $row;
            }
        }
        return $result;
    }
}
