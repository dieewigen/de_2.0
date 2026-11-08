<?php

namespace DieEwigen\Api\Model;

class SetPlayerActivity
{

	const string SET_PLAYER_ACTIVITY_SQL = "UPDATE de_login SET last_login = NOW(), last_click = NOW() WHERE user_id=?";

	/**
	 * Set the player's last activity timestamp to the current time.
	 *
	 * @param int $userId The ID of the user whose activity is to be updated.
	 * @return void
	 */

	public function setPlayerActivity(int $userId)
	{
		mysqli_execute_query($GLOBALS['dbi'], $this::SET_PLAYER_ACTIVITY_SQL, [$userId]);
	}
}
