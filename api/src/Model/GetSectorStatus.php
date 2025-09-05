<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\SectorSystemStatus;
use DieEwigen\Api\Types\SystemFleetStatus;

class GetSectorStatus
{
    const string GET_SECTOR_STATUS_SQL = "SELECT dud_source.user_id AS s_user_id, dud_source.rasse, dud_target.user_id AS t_user_id,
                                    duf.zielsys, duf.hsec, duf.hsys, duf.zeit, duf.fleetsize,
                                    duf.e81, duf.e82, duf.e83, duf.e83, duf.e84, duf.e85, duf.e86, duf.e87, duf.e88, 
                                    duf.e89, duf.e90, duf.aktion
                                   FROM de_user_fleet duf
                                   JOIN de_user_data dud_source on duf.hsec = dud_source.sector AND duf.hsys = dud_source.`system`
                                   JOIN de_user_data dud_target on duf.zielsec = dud_target.sector AND duf.zielsys = dud_target.`system`
                                   WHERE zielsec = ?
                                   AND entdecktsec = 1 AND (aktion = 1 OR aktion = 2)";

    /**
     * Retrieves sector status from the database.
     *
     * @return array the sector status array of given user, one item represent the status of one system.
     */
    public function getSectorStatus(int $userId) : array
    {
        $userService = new UserService();
        $coordinates = $userService->getCoordinates($userId);
        $stmt = mysqli_prepare($GLOBALS['dbi'], self::GET_SECTOR_STATUS_SQL);
        $stmt->bind_param("i", $coordinates[0]);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_BOTH);
        $groupedFleetsByTarget = $this->groupFleetByTarget($result);
        return $this->createSystemStatus($groupedFleetsByTarget, $coordinates[0]);
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

    private function createSystemStatus($sectorFleetStatusRows, $targetSector): array
    {
        $systemStatus = array();
        foreach ($sectorFleetStatusRows as $targetStr => $systemFleetStatusRow) {
            $attackFleetRows =  $this->filterByStatus($systemFleetStatusRow, 1);
            $defendFleetRows = $this->filterByStatus($systemFleetStatusRow, 2);
            $attackFleets = array_map(array($this, 'createFleetStatus'), $attackFleetRows);
            $defendFleets = array_map(array($this, 'createFleetStatus'), $defendFleetRows);
            $target = explode("-", $targetStr); //system-userId
            $systemStatus[] = new SectorSystemStatus($targetSector, intval($target[0]), intval($target[1]),
                $attackFleets, $defendFleets);
        }
        return $systemStatus;
    }

    private function createFleetStatus($fleetRow): SystemFleetStatus
    {
        return new SystemFleetStatus($fleetRow['s_user_id'], $fleetRow['hsec'], $fleetRow['hsys'], $fleetRow['zeit'],
            $fleetRow['fleetsize'], $this->calculateFp($fleetRow));

    }

    private function groupFleetByTarget(array $rows): array
    {
        $result = array();
        foreach ($rows as $row) {
            $result[$row['zielsys'] . '-' . $row['t_user_id']][] = $row;
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
