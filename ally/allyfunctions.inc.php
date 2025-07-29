<?php
	function writeHistory($allytag, $entry, $insert_chat=false){

		$entry = html_entity_decode($entry);
		$timestamp = time();
		$datum = date("d.m.Y - H:i", $timestamp);
		$sql = "SELECT id from de_allys WHERE allytag=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$allytag]);
		$data = mysqli_fetch_assoc($result);
		$ally_id = $data["id"];

		$sql = "INSERT INTO de_ally_history SET allytag=?, allyid=?, entry=?, timestamp=?, displaydate=?";
		mysqli_execute_query($GLOBALS['dbi'], $sql, [$allytag, $ally_id, $entry, $timestamp, $datum]);

		if($insert_chat){
			include_once 'functions.php';
			insert_chat_msg($ally_id, 1, '', $allytag.'-'.$entry);
		}
	}
	
	function getAllyId($allytag)
	{
		$sql = "SELECT id from de_allys WHERE allytag=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$allytag]);
		$data = mysqli_fetch_assoc($result);
		$ally_id = $data["id"];
		return $ally_id;
	}
	
	function getAllyTag($allyid)
	{
		$sql = "SELECT allytag from de_allys WHERE id=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql, [$allyid]);
		$data = mysqli_fetch_assoc($result);
		$ally_tag = $data["allytag"];
		return $ally_tag;
	}
