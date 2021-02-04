<?php
//Missionsstatus Reminder anzeigen, nur für pa
if($ums_premium==1){
	/*
	$resulttr  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
	$rowtr     = mysql_fetch_array($resulttr);
	$maxticktr = $rowtr["tick"];

	$showtradeinfo=0;
	$db_datentrade=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=1 ORDER BY deliverytime",$db);
	while($rowtrade = mysql_fetch_array($db_datentrade)){
		if($rowtrade['deliverytime']-$maxticktr<=0) $showtradeinfo=1;
	}
	*/

	$sql="SELECT COUNT(*) AS anzahl FROM de_user_mission WHERE end_time<'".time()."' AND get_reward=0 AND user_id='".$_SESSION['ums_user_id']."';";
	$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
	$row = mysqli_fetch_array($db_daten);

	if($row['anzahl']>0){
		echo '<div class="info_box text3" style="margin-top: 6px;">
		Es gibt abgeschlossene <a href="missions.php">Missionen</a>.<br>
		<span style="font-size: 10px;">Unter Optionen -> "Missionshilfe aktivieren" kann die Information eingeblendet/ausgeblendet werden.</span></div><br>';
	}
}else{
	echo '<div class="info_box text3">Mit einem Premium-Account werden hier erledigte Missionen angezeigt.<br>Unter Optionen -> "Missionshilfe aktivieren" kann die Information eingeblendet/ausgeblendet werden.</div><br>';
}

?>