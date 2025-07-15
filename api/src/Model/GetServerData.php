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
		$data['kt']=$row['kt'];
		$data['wt']=$row['wt'];
	
		return $data;
	}
}
