<?php
namespace DieEwigen\Api\Model;

class GetAllUsers
{
	/**
	 * Retrieves all NPC users from the database.
	 *
	 * @return array An array of associative arrays, each representing a user.
	 */	
	public function getAllNpcUsers()
	{
		
		$result = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc = 2 ORDER BY sector, `system`;");

		$users = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$users[] = $row;
		}
		
		return $users;
	}
}
