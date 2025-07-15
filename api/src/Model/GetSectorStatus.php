<?php
namespace DieEwigen\Api\Model;

class GetSectorStatus
{
	/**
	 * Retrieves sector status from the database.
	 *
	 * @return array An array of associative arrays, each representing a fleet.
	 */	
	public function getSectorStatus($userId)
	{
		$userId=intval($userId);
		
		//get sector from de_user_data
		$result = mysqli_query($GLOBALS['dbi'], "SELECT sector FROM de_user_data WHERE user_id='$userId';");
		$row = mysqli_fetch_assoc($result);
		$sector=$row['sector'];

		
		$result=mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_fleet WHERE zielsec = '$sector' AND (aktion = 1 OR aktion = 2) AND entdeckt > 0 AND entdecktsec > 0 ORDER BY zielsys, zeit, hsec, hsys ASC");

		$fleets = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$fleets[] = $row;
		}
		
		return $fleets;
	}
}
