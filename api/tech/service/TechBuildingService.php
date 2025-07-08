<?php
include PROJECT_ROOT_PATH.'/api/tech/model/TechData.php';
include PROJECT_ROOT_PATH . '/api/tech/model/TechDataResponse.php';

/**
 * Service to provide and change all buildings and researches (techs).
 */
class TechBuildingService
{
    const ALL_TECHS_SQL = "SELECT tech_id, tech_vor FROM de_tech_data ORDER BY tech_level ASC, tech_sort_id ASC";

    /**
     * Get full tech tree including techId and direct dependencies.
     * @return TechDataResponse
     */
    public function getTechTree(): TechDataResponse
    {
        $stmt = mysqli_query($GLOBALS['dbi'], self::ALL_TECHS_SQL);
        $result = $stmt->fetch_all();
        $techs = array_map(array($this,'createTechDataFromDBEntry'), $result);
        return new TechDataResponse($techs);
  }

    private function createTechDataFromDBEntry($dbEntry) : TechData
    {
        $techVor = $dbEntry[1];
        $deps = [];
        if ($techVor && $techVor != '') {
            $techVor = str_replace('T', '', $techVor);
            $deps = explode(';', $techVor);
        }
        return new TechData(intval($dbEntry[0]), $deps);
    }
}
