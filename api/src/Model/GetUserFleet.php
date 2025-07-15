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
}
