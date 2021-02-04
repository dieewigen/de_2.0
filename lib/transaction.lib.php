<?php

	function isLocked($id)
	{
		global $db;
		$returnvalue = false;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("SELECT * FROM de_user_locks WHERE id='$id'", $db);
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num == 1)
			{
				$locks=mysql_fetch_array($result);
				$val=$locks["locked"];
				if ($val == "1")
				{
					$returnvalue = true;
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an <a href=mailto:ascendant@bense.com>ascendant@bense.com</a> damit Ihr Problem behoben wird.");
				$returnvalue = true;
			}
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}

	function setLock($id)
	{	
		global $db;
		$returnvalue = false;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("SELECT * FROM de_user_locks WHERE id='$id'", $db);
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num == 1)
			{
				$locks=mysql_fetch_array($result);
				$val=$locks["locked"];
				if ($val != "1")
				{
					$setresult = mysql_query("UPDATE de_user_locks SET locked='1' WHERE id='$id'", $db);
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
				$setresult = mysql_query("INSERT into de_user_locks (id, locked) VALUES ($id, '1')", $db);
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}
	
	function releaseLock($id)
	{
		global $db;
		$returnvalue = false;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("SELECT * FROM de_user_locks WHERE id='$id'", $db);
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num == 1)
			{
				$setresult = mysql_query("UPDATE de_user_locks SET locked='0' WHERE id='$id'", $db);
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an <a href=mailto:ascendant@bense.com>ascendant@bense.com</a> damit Ihr Problem behoben wird.");
				$returnvalue = false;
			}
			elseif ($lockarray_num == 0)
			{
				$setresult = mysql_query("INSERT into de_user_locks (id, locked) VALUES ($id, '0')", $db);
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}
	
	function unlockAll()
	{
		global $db;
		$returnvalue = true;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("SELECT * FROM de_user_locks", $db);
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num > 0)
			{
				for($i;$i<$lockarray_num;$i++)
				{	
					$locks[i]=mysql_fetch_array($result);
					$lockid=$locks[i]["id"];
					$setresult = mysql_query("UPDATE de_user_locks SET locked='0' WHERE id='$lockid'", $db);
					if (!$setresult)
					{
						$returnvalue = false;
					}
				}
			}			
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}
	
	function lockAll()
	{
		global $db;
		$returnvalue = true;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("SELECT * FROM de_user_locks", $db);
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num > 0)
			{
				for($i;$i<$lockarray_num;$i++)
				{	
					$locks[i]=mysql_fetch_array($result);
					$lockid=$locks[i]["id"];
					$setresult = mysql_query("UPDATE de_user_locks SET locked='1' WHERE id='$lockid'", $db);
					if (!$setresult)
					{
						$returnvalue = false;
					}
				}
			}			
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}
	
	function deleteAll()
	{
		global $db;
		$returnvalue = false;
		mysql_query("LOCK TABLES de_user_locks WRITE", $db);
		$result = mysql_query("DELETE FROM de_user_locks", $db);
		if ($result)
		{
			$returnvalue = true;
		}
		mysql_query("UNLOCK TABLES", $db);
		return $returnvalue;
	}
?>