<?php
function isLocked($id){
	$id=intval($id);
	$returnvalue = false;
	mysqli_query($GLOBALS['dbi'],"LOCK TABLES de_user_locks WRITE");
	$result = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_locks WHERE id='$id'");
	if ($result){
		$lockarray_num=mysqli_num_rows($result);
		if ($lockarray_num == 1){
			$locks=mysqli_fetch_array($result);
			$val=$locks["locked"];
			if ($val == "1"){
				$returnvalue = true;
			}
		}elseif ($lockarray_num > 1){
			print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Please open a support ticket.");
			$returnvalue = true;
		}
	}
	mysqli_query($GLOBALS['dbi'],"UNLOCK TABLES");
	return $returnvalue;
}

function setLock($id){	
	$id=intval($id);
	$returnvalue = false;
	mysqli_query($GLOBALS['dbi'],"LOCK TABLES de_user_locks WRITE");
	$result = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_locks WHERE id='$id'");
	if ($result){
		$lockarray_num=mysqli_num_rows($result);
		if ($lockarray_num == 1){
			$locks=mysqli_fetch_array($result);
			$val=$locks["locked"];
			if ($val != "1"){
				$setresult = mysqli_query($GLOBALS['dbi'],"UPDATE de_user_locks SET locked='1' WHERE id='$id'");
				if ($setresult){
					$returnvalue = true;
				}
			}
		}
		elseif ($lockarray_num > 1)	{
			print("Transactionmanager: An internal error occured [#0001]: dublicate lock definition. Transaction has been cancelled.<br><br>Please open a support ticket.");
			$returnvalue = false;
		}elseif ($lockarray_num == 0){
			$setresult = mysqli_query($GLOBALS['dbi'],"INSERT into de_user_locks (id, locked) VALUES ($id, '1')");
			if ($setresult){
				$returnvalue = true;
			}
		}
	}
	mysqli_query($GLOBALS['dbi'],"UNLOCK TABLES");
	return $returnvalue;
}

function releaseLock($id){
	$id=intval($id);
	$returnvalue = false;
	mysqli_query($GLOBALS['dbi'],"LOCK TABLES de_user_locks WRITE");
	$result = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_locks WHERE id='$id'");
	if ($result){
		$lockarray_num=mysqli_num_rows($result);
		if ($lockarray_num == 1){
			$setresult = mysqli_query($GLOBALS['dbi'],"UPDATE de_user_locks SET locked='0' WHERE id='$id'");
			if ($setresult){
				$returnvalue = true;
			}
		}elseif ($lockarray_num > 1){
			print("Transactionmanager: An internal error occured [#01]: dublicate lock definition<br><br>Please open a support ticket.");
			$returnvalue = false;
		}elseif ($lockarray_num == 0){
			$setresult = mysqli_query($GLOBALS['dbi'],"INSERT into de_user_locks (id, locked) VALUES ($id, '0')");
			if ($setresult){
				$returnvalue = true;
			}
		}
	}
	mysqli_query($GLOBALS['dbi'],"UNLOCK TABLES");
	return $returnvalue;
}
?>