<?php

	function isLocked($id)
	{
		$returnvalue = false;
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "SELECT * FROM de_user_locks WHERE id=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
		if ($result)
		{
			$lockarray_num=mysqli_num_rows($result);
			if ($lockarray_num == 1)
			{
				$locks=mysqli_fetch_assoc($result);
				$val=$locks["locked"];
				if ($val == "1")
				{
					$returnvalue = true;
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an den Support damit Ihr Problem behoben wird.");
				$returnvalue = true;
			}
		}
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $returnvalue;
	}

	function setLock($id)
	{	
		$returnvalue = false;
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "SELECT * FROM de_user_locks WHERE id=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
		if ($result)
		{
			$lockarray_num=mysqli_num_rows($result);
			if ($lockarray_num == 1)
			{
				$locks=mysqli_fetch_assoc($result);
				$val=$locks["locked"];
				if ($val != "1")
				{
					$sql = "UPDATE de_user_locks SET locked='1' WHERE id=?";
					$setresult = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
					if ($setresult)
					{
						$returnvalue = true;
					}
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#0001]: dublicate lock definition. Transaction has been cancelled.<br><br>Bitte wenden Sie sich an <a href=mailto:ascendant@bense.com>ascendant@bense.com</a> damit Ihr Problem behoben wird.");
				$returnvalue = false;
			}
			elseif ($lockarray_num == 0)
			{
				$sql = "INSERT into de_user_locks (id, locked) VALUES (?, '1')";
				$setresult = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
		}
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $returnvalue;
	}
	
	function releaseLock($id)
	{
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "UPDATE de_user_locks SET locked='0' WHERE id=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$id]);
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $result;
	}
	
	function unlockAll()
	{
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "UPDATE de_user_locks SET locked='0'";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql);
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $result;
	}
	
	function lockAll()
	{
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "UPDATE de_user_locks SET locked='1'";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql);
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $result;
	}
	
	function deleteAll()
	{
		mysqli_execute_query($GLOBALS['dbi'], "LOCK TABLES de_user_locks WRITE");
		$sql = "DELETE FROM de_user_locks";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql);
		mysqli_execute_query($GLOBALS['dbi'], "UNLOCK TABLES");
		return $result;
	}
?>