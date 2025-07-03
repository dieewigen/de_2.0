<?php
//Missionsstatus Reminder anzeigen
$sql="SELECT COUNT(*) AS anzahl FROM de_user_mission WHERE end_time<'".time()."' AND get_reward=0 AND user_id='".$_SESSION['ums_user_id']."';";
$db_daten = mysqli_query($GLOBALS['dbi'], $sql);
$row = mysqli_fetch_array($db_daten);

if($row['anzahl']>0){
	echo '<div class="info_box text3" style="margin-top: 6px;">
	Es gibt abgeschlossene <a href="missions.php">Missionen</a>.<br>
	<span style="font-size: 10px;">Unter Optionen -> "Missionshilfe aktivieren" kann die Information eingeblendet/ausgeblendet werden.</span></div><br>';
}

?>