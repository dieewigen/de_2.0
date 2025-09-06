<?php
namespace DieEwigen\Api\Model;

class GetServerData
{
	/**
	 * Retrieves server data from the database/filesystem.
	 *
	 * @return array An array of associative arrays, each representing a value.
	 */	
	public function getServerData()
	{
		$data=array();

		$result = mysqli_query($GLOBALS['dbi'], "SELECT * FROM de_system;");
        $row = mysqli_fetch_assoc($result);
        $maxColsResult = mysqli_query($GLOBALS['dbi'], "SELECT max(col) AS max_cols, max(score) as max_score FROM de_user_data where npc != 2;");
        $maxColsRow = mysqli_fetch_assoc($maxColsResult);
        $data['kt']=$row['kt'];
        $data['wt']=$row['wt'];
        $data['max_cols']=$maxColsRow['max_cols'];
        $data['max_score']=$maxColsRow['max_score'];

		return $data;
	}
}
