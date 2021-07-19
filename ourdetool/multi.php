<?php
include "../inccon.php";
?>
<html>
<head>
<title>Multiliste</title>
<?php include "cssinclude.php";?>
<style >
 body { scrollbar-face-color: #000000;scrollbar-shadow-color: #000000;scrollbar-highlight-color: #333333; scrollbar-3dlight-color: #8CA0B4;scrollbar-darkshadow-color: #333333;scrollbar-track-color: #000000;
  scrollbar-arrow-color: #8CA0B4; padding: 0px; color: #3399FF; margin-left: 0px; margin-top: 0px; margin-right: 0px; margin-bottom: 0px;
  font-family: helvetica, arial,geneva, sans-serif;  font-size: 10pt;}
 table { border: 1px solid #00366C; }
 td { font-family: helvetica, arial, geneva, sans-serif; font-size: 10pt; white-space: nowrap; border: 1px solid #00366C; }
 td.r { color: #ff0000; }
 a { color: #3399ff; text-decoration: underline }
 a:hover { color: #3399ff; text-decoration: none }
</style>

</head>
<body>
<?php

$show_statistic=isset($_GET['statistic']) ? $_GET['statistic'] : 0;

/*
echo'
IP Adressen des gleichen Oktetts:
<a href="multi.php?okt=1">[ 1 ]</a> -
<a href="multi.php?okt=2">[ 2 ]</a> -
<a href="multi.php?okt=3">[ 3 ]</a> -
<a href="multi.php?okt=4">[ 4 ]</a> <br><br><br>';
if(!isset($okt)){
	$okt=4;
}
*/

$okt=4;

include "det_userdata.inc.php";

$time_start = getmicrotime();
$gesuser=0;

if($show_statistic==1 || $show_statistic==2){
	//alle verdächtigen IP-Adressen auslesen, letzte IP
	$sql="SELECT SUBSTRING_INDEX(last_ip, '.', $okt) AS last_ip, COUNT(last_ip) 'zaehler' FROM de_login WHERE last_ip<>'127.0.0.1' GROUP BY SUBSTRING_INDEX(last_ip, '.', $okt) ORDER BY `zaehler` DESC, `last_ip` ASC" ;
	//echo $sql;

	$db_daten=mysql_query($sql,$db);

	

	while($row = mysql_fetch_array($db_daten)){
		//echo '<br>'.$row['last_ip'].'/'.$row['zaehler'];
		if (($row["zaehler"]>1)&&($row["last_ip"]<>'')){
			$z=$row["zaehler"]; $ip=$row["last_ip"];
			$ipz=$ip;
			if ($ipz=='212.227.110.246') $ipz='!!! 1&1 !!!';
			//kopf mit ip und anzahl
			echo '<table border="0" cellpadding="2" cellspacing="0" width="200">';
			echo '<tr>';
			echo '<td align="center">IP: '.$ipz.' Anzahl: '.$z.'</td>';
			echo '</tr>';
			echo '</table>';

			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<tr>';
			echo '<td width="50">UserID</td>';
			echo '<td width="150">Name</td>';
			echo '<td width="200">E-Mail</td>';
			echo '<td width="150">Passwort</td>';
			echo '<td width="140">Registriert</td>';
			echo '<td width="140">Letzter Login</td>';
			echo '<td width="70">Status</td>';
			echo '<td width="40">Logins</td>';
			echo '<td width="40">Sektor</td>';
			echo '<td width="40">Ort</td>';
			echo '<td width="40">IP</td>';
			echo '</tr>';


			$result=mysql_query("select de_login.last_ip, de_login.user_id, de_login.nic, de_login.reg_mail, de_login.pass, de_login.register, de_login.last_login, de_login.logins, de_user_data.sector, de_login.status, de_user_info.ort from de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) left join de_user_info on(de_login.user_id = de_user_info.user_id) where last_ip like '$ip%' order by pass",$db);

			$oldpass='';
			while($user = mysql_fetch_array($result)){
				if ($oldpass==$user["pass"]) $str=' class="r"'; else $str='';
				$oldpass=$user["pass"];

				if ($user["status"]==0) $status='Inaktiv';
				if ($user["status"]==1) $status='Aktiv';
				if ($user["status"]==2) $status='Gesperrt';
				if ($user["status"]==3) $status='Urlaub';

				if($show_statistic==2) {
					if ($user["status"]!=2) {
						echo '<tr>';
						echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
						echo '<td>'.$user["nic"].'</td>';
						echo '<td>'.$user["reg_mail"].'</td>';
						echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
						echo '<td>'.$user["register"].'</td>';
						echo '<td>'.$user["last_login"].'</td>';
						$status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
						echo '<td>'.$status.'</td>';
						echo '<td>'.$user["logins"].'</td>';
						echo '<td>'.$user["sector"].'</td>';
						echo '<td>'.$user["ort"].'</td>';
						echo '<td>'.$user["last_ip"].'</td>';
						echo '</tr>';
						$gesuser++;
					}
				}
				else {
					echo '<tr>';
					echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
					echo '<td>'.$user["nic"].'</td>';
					echo '<td>'.$user["reg_mail"].'</td>';
					echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
					echo '<td>'.$user["register"].'</td>';
					echo '<td>'.$user["last_login"].'</td>';
					$status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
					echo '<td>'.$status.'</td>';
					echo '<td>'.$user["logins"].'</td>';
					echo '<td>'.$user["sector"].'</td>';
					echo '<td>'.$user["ort"].'</td>';
					echo '<td>'.$user["last_ip"].'</td>';
					echo '</tr>';
					$gesuser++;
				}
			}
			echo '</table><br><br>';
		}
	}

	echo 'Verd&auml;chtige: '.$gesuser;

}elseif($show_statistic==3){
	
	$tage=isset($_GET['tage']) ? $_GET['tage'] : 14;
	
	//die IP-Adressen der letzten X Tage auswerten
	$time=date("Y-m-d H:i:s", time()-3600*24*$tage);

	//alle vorhandenen IP-Adressen in ein Array packen
	$sql="SELECT * FROM de_user_ip WHERE time>'$time' GROUP BY ip" ;
	//echo '<br>'.$sql.'<br>';

	$ip_adressen=array();

	$db_daten=mysql_query($sql,$db);
	while($row = mysql_fetch_array($db_daten)){
		$ip_adressen[]=$row['ip'];
	}

	//print_r($ip_adressen);

	//für jede IP-Adresse überprüfen ob es mehrere user_id gibt, was normal nicht sein sollte

	echo '<h1>IP-Adressen der letzten '.$tage.' Tage die in mehreren Accounts auftreten.</h1>';

	$tage_array=array(3,7,14,30,50,100);

	echo '<br>';
	for($i=0;$i<count($tage_array); $i++){
		
		echo '<a href="multi.php?statistic=3&tage='.$tage_array[$i].'">'.$tage_array[$i].' Tage</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	echo '<br><br>';

	for($i=0;$i<count($ip_adressen);$i++){
		$ip=$ip_adressen[$i];

		$sql="SELECT COUNT(DISTINCT user_id) AS anzahl FROM de_user_ip WHERE time>'$time' AND ip='$ip';";
		//echo '<br>'.$sql.'<br>';
		$result=mysql_query($sql,$db);
		$rowx = mysql_fetch_array($result);
		$anzahl=$rowx['anzahl'];

		if($rowx['anzahl']>1){

			//die beteiligten Spieler ausgeben
			$sql="SELECT * FROM de_user_ip LEFT JOIN de_login ON(de_user_ip.user_id=de_login.user_id) WHERE time>'$time' AND ip='$ip' GROUP BY de_user_ip.user_id;";
			//echo '<br>'.$sql.'<br>';
			$result=mysql_query($sql,$db);
	
			echo '<table border="0" cellpadding="2" cellspacing="0" width="200">';
			echo '<tr>';
			echo '<td align="center">IP: '.$ip.' Anzahl: '.$anzahl.'</td>';
			echo '</tr>';
			echo '</table>';

			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<tr>';
			echo '<td width="50">UserID</td>';
			echo '<td width="150">Name</td>';
			echo '<td width="200">E-Mail</td>';
			echo '<td width="150">Passwort</td>';
			echo '<td width="140">Registriert</td>';
			echo '<td width="140">Letzter Login</td>';
			echo '<td width="70">Status</td>';
			echo '<td width="40">Logins</td>';
			echo '</tr>';

			$oldpass='';
			while($user = mysql_fetch_array($result)){
				if ($oldpass==$user["pass"]) $str=' class="r"'; else $str='';
				$oldpass=$user["pass"];

				if ($user["status"]==0) $status='Inaktiv';
				if ($user["status"]==1) $status='Aktiv';
				if ($user["status"]==2) $status='Gesperrt';
				if ($user["status"]==3) $status='Urlaub';

				echo '<tr>';
				echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
				echo '<td>'.$user["nic"].'</td>';
				echo '<td>'.$user["reg_mail"].'</td>';
				echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
				echo '<td>'.$user["register"].'</td>';
				echo '<td>'.$user["last_login"].'</td>';
				$status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
				echo '<td>'.$status.'</td>';
				echo '<td>'.$user["logins"].'</td>';
				echo '</tr>';
			}
			echo '</table><br>';

			//eine Liste aller Logins 
			echo '
			<details>
				<summary>Liste der Logins</summary>
				<p>
					<table border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="50">UserID</td>
							<td width="150">Zeit</td>
							<td width="200">Browser</td>
							<td width="150">Cookie</td>
						</tr>
			';

			$sql="SELECT * FROM de_user_ip WHERE time>'$time' AND ip='$ip' ORDER BY time;";
			//echo '<br>'.$sql.'<br>';
			$result=mysql_query($sql,$db);
			while($rowx = mysql_fetch_array($result)){
				echo '<tr>';

				echo '<td>'.$rowx['user_id'].'</td>';
				echo '<td>'.$rowx['time'].'</td>';
				echo '<td>'.$rowx['browser'].'</td>';
				echo '<td>'.$rowx['loginhelp'].'</td>';

				echo '</tr>';
			}
						
			  

			echo '
					</table>
				</p>
			</details>';
			

			echo '<br><br>';
		}

	}
	
	echo '<br><br>';

	/*
	while($row = mysql_fetch_array($db_daten)){
		//echo '<br>'.$row['last_ip'].'/'.$row['zaehler'];
		if (($row["zaehler"]>1)&&($row["last_ip"]<>'')){
			$z=$row["zaehler"]; $ip=$row["last_ip"];
			$ipz=$ip;
			if ($ipz=='212.227.110.246') $ipz='!!! 1&1 !!!';
			//kopf mit ip und anzahl
			echo '<table border="0" cellpadding="2" cellspacing="0" width="200">';
			echo '<tr>';
			echo '<td align="center">IP: '.$ipz.' Anzahl: '.$z.'</td>';
			echo '</tr>';
			echo '</table>';

			echo '<table border="0" cellpadding="2" cellspacing="0">';
			echo '<tr>';
			echo '<td width="50">UserID</td>';
			echo '<td width="150">Name</td>';
			echo '<td width="200">E-Mail</td>';
			echo '<td width="150">Passwort</td>';
			echo '<td width="140">Registriert</td>';
			echo '<td width="140">Letzter Login</td>';
			echo '<td width="70">Status</td>';
			echo '<td width="40">Logins</td>';
			echo '<td width="40">Sektor</td>';
			echo '<td width="40">Ort</td>';
			echo '<td width="40">IP</td>';
			echo '</tr>';


			$result=mysql_query("select de_login.last_ip, de_login.user_id, de_login.nic, de_login.reg_mail, de_login.pass, de_login.register, de_login.last_login, de_login.logins, de_user_data.sector, de_login.status, de_user_info.ort from de_login left join de_user_data on(de_login.user_id = de_user_data.user_id) left join de_user_info on(de_login.user_id = de_user_info.user_id) where last_ip like '$ip%' order by pass",$db);

			$oldpass='';
			while($user = mysql_fetch_array($result)){
				if ($oldpass==$user["pass"]) $str=' class="r"'; else $str='';
				$oldpass=$user["pass"];

				if ($user["status"]==0) $status='Inaktiv';
				if ($user["status"]==1) $status='Aktiv';
				if ($user["status"]==2) $status='Gesperrt';
				if ($user["status"]==3) $status='Urlaub';

				echo '<tr>';
				echo '<td><a href="idinfo.php?UID='.$user["user_id"].'" target="_blank">'.$user["user_id"].'</a></td>';
				echo '<td>'.$user["nic"].'</td>';
				echo '<td>'.$user["reg_mail"].'</td>';
				echo '<td'.$str.'>'.modpass($user["pass"]).'</td>';
				echo '<td>'.$user["register"].'</td>';
				echo '<td>'.$user["last_login"].'</td>';
				$status.=' <a href="de_set_user_status.php?uid='.$user["user_id"].'&status=2" target="setuserstatus">[S]</a>';
				echo '<td>'.$status.'</td>';
				echo '<td>'.$user["logins"].'</td>';
				echo '<td>'.$user["sector"].'</td>';
				echo '<td>'.$user["ort"].'</td>';
				echo '<td>'.$user["last_ip"].'</td>';
				echo '</tr>';
				$gesuser++;

			}
			echo '</table><br><br>';
		}
	}
	*/


}

/*
select last_ip, count(last_ip) "zaehler" from de_login group by last_ip ORDER BY `zaehler` DESC LIMIT 0, 30
update de_login set status=2 where last_ip='217.225.120.26'
select * from de_login where last_ip= '217.225.120.26'*/

$time_end = getmicrotime();
$ltime = number_format($time_end - $time_start,2,".","");

function modpass($pass){
	$pass[0]="*";
	$pass[1]="*";
	$pass[2]="*";
	$pass[3]="*";
	return($pass);
}

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
  	return ((float)$usec + (float)$sec);
}
?>

 <br>
 Seite in <?php echo $ltime; ?> Sekunden erstellt.
</body>
</html>