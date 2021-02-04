<?php
	function writeHistory($allytag, $entry, $insert_chat=false){

		$entry = html_entity_decode($entry);
		$timestamp = time();
		$datum = date("d.m.Y - H:i", $timestamp);
		$result = mysql_query("SELECT id from de_allys WHERE allytag='$allytag'");
		$data = mysql_fetch_array($result);
		$ally_id = $data["id"];

		mysql_query("INSERT INTO de_ally_history SET allytag='$allytag', allyid='$ally_id', entry='$entry', timestamp='$timestamp', displaydate='$datum'");

		if($insert_chat){
			include_once 'functions.php';
			insert_chat_msg($ally_id, 1, '', $allytag.'-'.$entry);
		}
	}
	
	function getAllyId($allytag)
	{
		$result = mysql_query("SELECT id from de_allys WHERE allytag='$allytag'");
		$data = mysql_fetch_array($result);
		$ally_id = $data["id"];
		return $ally_id;
	}
	
	function getAllyTag($allyid)
	{
		$result = mysql_query("SELECT allytag from de_allys WHERE id='$allyid'");
		$data = mysql_fetch_array($result);
		$ally_tag = $data["allytag"];
		return $ally_tag;
	}
?>