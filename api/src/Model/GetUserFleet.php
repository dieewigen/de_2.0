<?php
namespace DieEwigen\Api\Model;

class GetUserFleet
{
	/**
	 * Retrieves user fleets from the database.
	 *
	 * @return array An array of associative arrays, each representing a fleet.
	 */	
	public function getUserFleet($userId)
	{
		$userId=intval($userId);
		
		$result = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE user_id='$userId-0' OR user_id='$userId-1' OR  user_id='$userId-2' OR user_id='$userId-3' ORDER BY user_id;");

		$fleets = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$fleets[] = $row;
		}
		
		return $fleets;
	}

	/**
	 * Retrieves defender fleets for a specific target location.
	 * Finds all fleets that are defending the given sector/system coordinates.
	 *
	 * @param int $userId User ID of the defender
	 * @param int $targetSector Target sector coordinate
	 * @param int $targetSystem Target system coordinate
	 * @return array An array of fleet data with calculated information
	 */
	public function getDefferFleet($userId, $targetSector, $targetSystem)
	{
		$userId = intval($userId);
		$targetSector = intval($targetSector);
		$targetSystem = intval($targetSystem);

		//die Rasse des Users laden
		$sql = "SELECT rasse FROM de_user_data WHERE user_id = ?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$userId]);
		$row = mysqli_fetch_assoc($result);	
		$rasseId = $row['rasse'];

		// Alle Flotten des Users laden die deffen (Slots 1-3)
		$fleetSlots = [];
		for ($i = 1; $i <= 3; $i++) {
			$fleetId = $userId . '-' . $i;
			$sql = "SELECT * FROM de_user_fleet WHERE user_id = ? AND aktion = 2 AND zielsec = ? AND zielsys = ?";
			$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$fleetId, $targetSector, $targetSystem]);
			$fleet = mysqli_fetch_assoc($result);
			
			if ($fleet) {
				$flottenpunkte=$this->calculateFleetPoints($fleet, $rasseId);

				$fleetSlots[] = ['reisezeit' => $fleet['zeit'], 'flottenpunkte' => $flottenpunkte];
			}
		}

		return $fleetSlots;
	}

	/**
	 * Berechnet die Flottenpunkte
	 */
	private function calculateFleetPoints($fleet, $rasseId)
	{
		include __DIR__ . "/../../../tickler/kt_einheitendaten.php";

		$fp = 0;
		for ($s = 81;$s <= 90;$s++) {
			$fp = $fp + $unit[$rasseId - 1][$s - 81][4] * $fleet['e'.$s];
		}

		return $fp;
	}
}
