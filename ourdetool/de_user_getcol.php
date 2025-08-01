<?php
include "../inccon.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Kollektoren</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php
$uid = isset($_REQUEST["uid"]) ? $_REQUEST["uid"] : '';

echo '<div align="center">';

echo '<br><a href="de_user_getcol.php?uid='.$uid.'&show=1">zeige alle Kollektoren die dem Spieler gestohlen wurden</a><br>
<a href="de_user_getcol.php?uid='.$uid.'&show=2">zeige alle Kollektoren die der Spieler gestohlen hat</a>
';

//alle kollektoren die dem spieler gestohlen worden sind
if(isset($_REQUEST["show"]) && $_REQUEST["show"]==1 && isset($_REQUEST["uid"]) && $_REQUEST["uid"]){
	echo '<br><br><table width=600>';
	echo '<tr align="center"><td>Zeitpunkt</td><td>Kollektoren</td><td>Diebes-User-ID</td><td>Spielername</td><td>Aktuelle Allianz</td></tr>';
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_getcol WHERE zuser_id=?", [$uid]);
	while($row = mysqli_fetch_array($db_daten)){
		$time=strftime("%Y-%m-%d %H:%M:%S", $row["time"]);
		//spielername des diebes
		$duid=$row["user_id"];
		$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, status, allytag FROM de_user_data WHERE user_id=?", [$duid]);
		$num = mysqli_num_rows($result);
		if($num==1){
			$rowx = mysqli_fetch_array($result);
			$spielername=$rowx["spielername"];
			if($rowx['status']==1)$allytag=$rowx['allytag'];else $allytag='';
		}
		else $spielername='gelöscht';
		echo '<tr align="center"><td>'.$time.'</td><td>'.$row["colanz"].'</td><td><a href="idinfo.php?UID='.$duid.'" target="_blank">'.$duid.'</a></td><td>'.$spielername.'</td><td>'.$allytag.'</td></tr>';
	}
	echo '</table>';
}

//alle kollektoren die er selbst gestohlen hat
if(isset($_REQUEST["show"]) && $_REQUEST["show"]==2 && isset($_REQUEST["uid"]) && $_REQUEST["uid"]){
	echo '<br><br><table width=600>';
	echo '<tr align="center"><td>Zeitpunkt</td><td>Kollektoren</td><td>Erfahrungspunkte</td><td>Bestohlener-User-ID</td><td>Spielername</td><td>Aktuelle Allianz</td></tr>';
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_getcol WHERE user_id=?", [$uid]);
	while($row = mysqli_fetch_array($db_daten)){
		$time=strftime("%Y-%m-%d %H:%M:%S", $row["time"]);
		//spielername des diebes
		$duid=$row["zuser_id"];
		$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT spielername, status, allytag FROM de_user_data WHERE user_id=?", [$duid]);
		$num = mysqli_num_rows($result);
		if($num==1)		{
			$rowx = mysqli_fetch_array($result);
			$spielername=$rowx["spielername"];
			if($rowx['status']==1)$allytag=$rowx['allytag'];else $allytag='';
		}
		else $spielername='gelöscht';
		echo '<tr align="center"><td>'.$time.'</td><td>'.$row["colanz"].'</td><td>'.$row["getexp"].'</td><td><a href="idinfo.php?UID='.$duid.'" target="_blank">'.$duid.'</a></td><td>'.$spielername.'</td><td>'.$allytag.'</td></tr>';
	}
	echo '</table>';
}

echo '</div>';
?>
</body>
</html>
