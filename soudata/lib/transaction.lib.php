<?php

	function isLocked($id)
	{
		$returnvalue = false;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("SELECT * FROM sou_user_locks WHERE id='$id'");
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
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an <a href=mailto:".$GLOBALS['env_admin_email'].">".$GLOBALS['env_admin_email']."</a> damit Ihr Problem behoben wird.");
				$returnvalue = true;
			}
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}

	function setLock($id)
	{	
		$returnvalue = false;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("SELECT * FROM sou_user_locks WHERE id='$id'");
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num == 1)
			{
				$locks=mysql_fetch_array($result);
				$val=$locks["locked"];
				if ($val != "1")
				{
					$setresult = mysql_query("UPDATE sou_user_locks SET locked='1' WHERE id='$id'");
					if ($setresult)
					{
						$returnvalue = true;
					}
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an <a href=mailto:".$GLOBALS['env_admin_email'].">".$GLOBALS['env_admin_email']."</a> damit Ihr Problem behoben wird.");
				$returnvalue = false;
			}
			elseif ($lockarray_num == 0)
			{
				$setresult = mysql_query("INSERT into sou_user_locks (id, locked) VALUES ($id, '1')");
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}
	
	function releaseLock($id)
	{
		$returnvalue = false;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("SELECT * FROM sou_user_locks WHERE id='$id'");
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num == 1)
			{
				$setresult = mysql_query("UPDATE sou_user_locks SET locked='0' WHERE id='$id'");
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
			elseif ($lockarray_num > 1)
			{
				print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Bitte wenden Sie sich an <a href=mailto:".$GLOBALS['env_admin_email'].">".$GLOBALS['env_admin_email']."</a> damit Ihr Problem behoben wird.");
				$returnvalue = false;
			}
			elseif ($lockarray_num == 0)
			{
				$setresult = mysql_query("INSERT into sou_user_locks (id, locked) VALUES ($id, '0')");
				if ($setresult)
				{
					$returnvalue = true;
				}
			}
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}
	
	function unlockAll()
	{
		$returnvalue = true;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("SELECT * FROM sou_user_locks");
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num > 0)
			{
				for($i;$i<$lockarray_num;$i++)
				{	
					$locks[i]=mysql_fetch_array($result);
					$lockid=$locks[i]["id"];
					$setresult = mysql_query("UPDATE sou_user_locks SET locked='0' WHERE id='$lockid'");
					if (!$setresult)
					{
						$returnvalue = false;
					}
				}
			}			
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}
	
	function lockAll()
	{
		$returnvalue = true;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("SELECT * FROM sou_user_locks");
		if ($result)
		{
			$lockarray_num=mysql_num_rows($result);
			if ($lockarray_num > 0)
			{
				for($i;$i<$lockarray_num;$i++)
				{	
					$locks[i]=mysql_fetch_array($result);
					$lockid=$locks[i]["id"];
					$setresult = mysql_query("UPDATE sou_user_locks SET locked='1' WHERE id='$lockid'");
					if (!$setresult)
					{
						$returnvalue = false;
					}
				}
			}			
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}
	
	function deleteAll()
	{
		$returnvalue = false;
		mysql_query("LOCK TABLES sou_user_locks WRITE");
		$result = mysql_query("DELETE FROM sou_user_locks");
		if ($result)
		{
			$returnvalue = true;
		}
		mysql_query("UNLOCK TABLES");
		return $returnvalue;
	}
?>