<?php

namespace DieEwigen\DE2\Model\Npc;
include('tickler/kt_einheitendaten.php');

class FleetControl
{

    private \mysqli $dbi;

    public function __construct()
    {
        $this->dbi = $GLOBALS['dbi'];
    }

    /**
     * Check if the NPC fleets 1,2,3 are at home
     * @param int $npcId
     * @return bool true if all 3 fleets are at home
     */
    public function isFleetHome(int $npcId): bool
    {
        $fleetIds = [$npcId.'-'.'1', $npcId.'-'.'2', $npcId.'-'.'3'];
        $mysqli_result = mysqli_execute_query($this->dbi, "SELECT * FROM de_user_fleet WHERE user_id in (?,?,?) and aktion = 0", $fleetIds);
        return mysqli_num_rows($mysqli_result) == 3;
    }

    /**
     * Move all ships from fleets 1,2,3 to fleet 0 (home check not included)
     * @param int $npcId
     * @return void
     */
    public function moveAllShipsToHomeFleet(int $npcId): void
    {
        global $sv_anz_schiffe;
        if(setLock($npcId)) {
            $fleetIds = [$npcId.'-'.'0', $npcId.'-'.'1', $npcId.'-'.'2', $npcId.'-'.'3'];
            $units=array();
            $unitsResult = mysqli_execute_query($this->dbi, "SELECT * FROM de_user_fleet WHERE user_id in (?,?,?,?)", $fleetIds);
            while($row = mysqli_fetch_array($unitsResult)){
                $units[]=$row;
            }
            for ($i=81; $i<=80+$sv_anz_schiffe; $i++){
                $fleet[0]=$units[0]['e'.$i];
                $fleet[1]=$units[1]['e'.$i];
                $fleet[2]=$units[2]['e'.$i];
                $fleet[3]=$units[3]['e'.$i];
                mysqli_execute_query($this->dbi,"UPDATE de_user_fleet SET e$i = ? WHERE user_id = ?", [$fleet[0]+$fleet[1]+$fleet[2]+$fleet[3], $npcId.'-'.'0']);
                mysqli_execute_query($this->dbi,"UPDATE de_user_fleet SET e$i = ? WHERE user_id in (?,?,?)", [0,$npcId.'-'.'1', $npcId.'-'.'2', $npcId.'-'.'3']);
            }
            if(!releaseLock($npcId)) {
                error_log("FleetControl::moveAllShipsToHomeFleet - could not release lock for npcId ".$npcId);
            }
        } else {
            error_log("FleetControl::moveAllShipsToHomeFleet - could not set lock for npcId " . $npcId);
        }
    }
}