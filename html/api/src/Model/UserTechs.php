<?php

namespace DieEwigen\Api\Model;

include_once "../../../functions.php";
include_once "../../../tickler/kt_einheitendaten.php";

use DieEwigen\Api\Types\Resources;
use DieEwigen\Api\Types\Technology;

class UserTechs
{
    const string FINISHED_USER_TECHS_SQL = 'SELECT tech_id FROM de_user_techs WHERE user_id = ? and de_user_techs.time_finished < ?';
    const string ALL_TECHS_SQL = 'SELECT tech_id, tech_vor, tech_build_cost from de_tech_data';

    /**
     * Return a list of technologies (research + building) which can be built without any precondition
     * @param int $userId the user Id,
     * @return array a array of technologies containing the techId and the required resources.
     */
    public function getAvailableTechs(int $userId) : array
    {
        $now = time();
        $playerTechsStmt = mysqli_prepare($GLOBALS['dbi'], self::FINISHED_USER_TECHS_SQL);
        $playerTechsStmt->bind_param('ii', $userId, $now);
        $playerTechsStmt->execute();
        $playerTechsResult = $playerTechsStmt->get_result();
        $finishedTechs = [];
        while ($row = $playerTechsResult->fetch_assoc()) {
            $finishedTechs[] = intval($row['tech_id']);
        }
        $techsStmt = mysqli_query($GLOBALS['dbi'], self::ALL_TECHS_SQL);
        $availTechs = [];
        while ($tech = $techsStmt->fetch_assoc()) {
            if (!in_array(intval($tech['tech_id']), $finishedTechs, true)) {
                if (!empty($tech['tech_vor'])) {
                    $requirements = explode(';', $tech['tech_vor']);
                    $requiredTechIds = [];
                    for ($j=0; $j < sizeof($requirements); $j++) {
                        if ($requirements[$j][0] == 'T') {
                            $requiredTechId = intval(str_replace('T', '', $requirements[$j]));
                            if (!in_array($requiredTechId, $finishedTechs, true)) {
                                $requiredTechIds[] = $requiredTechId;
                            }
                        } //ignore all others
                    }
                    if (sizeof($requiredTechIds) != 0) {
                        $availTechs[] = $this->buildTech($tech);
                    }
                } else {
                    $availTechs[] = $this->buildTech($tech);
                }
            }
        }
        return $availTechs;
    }

    private function buildTech(array $tech) : Technology {
        $techBuildCost = $tech['tech_build_cost'];
        $costComponents = explode(';', $techBuildCost);
        $costM = 0;
        $costD = 0;
        $costI = 0;
        $costE = 0;
        $costT = 0;
        for ($i=0; $i<sizeof($costComponents); $i++) {
            $costComponent = $costComponents[$i];
            $resId = intval(substr($costComponent, 1, 1));
            $value = intval(substr($costComponent, strpos($costComponent, 'x') + 1));
            switch ($resId) {
               case 1: $costM = $value; break;
               case 2: $costD = $value; break;
               case 3: $costI = $value; break;
               case 4: $costE = $value; break;
               case 5: $costT = $value; break;
            }
        }
        return new Technology(intval($tech['tech_id']), new Resources($costM, $costD, $costI, $costE, $costT));
    }
}