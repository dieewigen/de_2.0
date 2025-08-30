<?php
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Allianzaufgaben
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
echo '<br><hr>Allyaufgaben-START<br>';
//die zeit eins runterz�hlen
mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questtime=questtime-1 WHERE questtime>=1", []);

//noch nicht belegte, erledigte und abgelaufende quests laden 
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE questtime=0 OR questreach>=questgoal", []);
while($row = mysqli_fetch_array($db_daten)){
	
	$allyid=$row['id'];
	$allytag=$row['allytag'];
	$maxmembercount=$row['maxmembercount'];
	//Minimalwert von Maxmembercount prüfen
	if($maxmembercount<5)$maxmembercount=5;
	
	$maxcolcount=$row['maxcolcount'];
	$maxscorecount=$row['maxscorecount'];
	$col_erobert=$row['colstolen'];
	
	//feststellen wie weit die runde vorangeschritten ist
	$allyjobs_rundenfortschritt=$maxtick/$sv_winscore;
	
	//aufgabe ist fertig, belohnung hinterlegen
	if($row['questgoal']>0 AND $row['questreach']>0 AND $row['questreach']>=$row['questgoal']){
		$questpoints=$allyjobs[$row['questtyp']][4]+round($row['questtime']/10);
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questpoints=questpoints+?, artefacts=artefacts+1 WHERE id=?", [$questpoints, $allyid]);
	}

	//nächster questtyp
	$nexttyp=$row['questtyp']+1;
	//wenn es die allererste aufgabe ist, dann ist der questyp 0
	if($row['questgoal']==0)$nexttyp=0;
	//überprüfen ob der questtyp verfügbar ist, ansonsten typ 0 nehmen
	if($nexttyp>=count($allyjobs))$nexttyp=0;

	//neue aufgabe hinterlegen
	$questreach=$allyjobs[$nexttyp][1];//erreicht
	$questtime=$allyjobs[$nexttyp][3];
	
	//das Ziel wird dynamisch berechnet
	switch($nexttyp){
		case 0://Kollektoren bauen
			$questreach=0;
			$questgoal=25;
			$questgoal=$questgoal-round($questgoal*$allyjobs_rundenfortschritt);
			$questgoal=$questgoal*$maxmembercount;
			if($questgoal<25)$questgoal=25;
			break;
		case 1://Kollektoren von anderen Spielern erobern
			//x% vom max. kollektorenbesitz der Allianz m�ssen geholt werden
			$questreach=$col_erobert;
			$questgoal=$col_erobert+($maxcolcount*0.05);
			if($questgoal<5)$questgoal=5;
			break;
		case 2://erhöht eure punktezahl
			//x% vom der maximalen allianzpunkte
			$questreach=$maxscorecount;
			$questgoal=$maxscorecount+($maxscorecount*0.05);
			if($questgoal<100000)$questgoal=100000;
			break;
		case 3://dx-kollies erobern
			$questreach=0;
			$questgoal=5;
			$questgoal=$questgoal*$maxmembercount;
			break;
		case 4://Startet Missionen
			$questreach=0;
			$questgoal=7;
			$questgoal=$questgoal*$maxmembercount;
			break;
		case 5://Erhaltet Kriegsartefakte durch Kämpfe.
			$questreach=0;
			$questgoal=$maxscorecount*0.015/5000/100;
			if($questgoal<1)$questgoal=1;
			break;
		case 6://Führt Artefaktverschmelzungen und Artefaktupgrades durch
			$questreach=0;
			$questgoal=6;
			$questgoald=$questgoal*$maxmembercount;
			break;
		default:
		echo 'ERROR: ALLYJOBS ('.$nexttyp.')';
	}

	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET questtyp=?, questreach=?, questgoal=?, questtime=? WHERE id=?", [$nexttyp, $questreach, $questgoal, $questtime, $allyid]);
	//nachricht an den allianzchat
	insert_chat_msg($allyid, 1, '', '<font color="#802ec1">Eine neue '.$allytag.'-Allianzaufgabe steht zur Verf&uuml;gung.</font>');
}


echo 'Allyaufgaben-ENDE<br><hr>';

//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Allianzmissionen
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
echo '<br>Allianzmission-START<br>';

//////////////////////////////////////////////////////////////////////
// ARES
//////////////////////////////////////////////////////////////////////
// wird alle 672 WT aktiv
$trigger_intervall=672;
//$trigger_intervall=1;
if($maxtick % $trigger_intervall == 0){
	echo 'ARES:<br>';

	//alle Alianzen auslesen die an der Mission teilgenommen haben
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE mission_counter_1 > 0 ORDER BY mission_counter_1 DESC", []);
	$num = mysqli_num_rows($db_daten);
	if($num>0){
		$score_gesamt=0;
		$allys=array();
		while($row = mysqli_fetch_array($db_daten)){
			$allys[]=$row;
			$score_gesamt+=$row['mission_counter_1'];
		}

		echo '$score_gesamt: '.$score_gesamt.'<br>';

		//Gewinn bestimmen, erhöht sich mit Voranschreiten der Runde (WT)
		$maxres[0]=$maxtick*500;$maxres[1]=$maxtick*250;$maxres[2]=$maxtick*60;$maxres[3]=$maxtick*37;

		//alle Teilnehmen Allianzen durchgehen und ihren Prozentanteil berechnen
		$ergebnis_text='';
		for($i=0;$i<count($allys);$i++){
			$allytag=$allys[$i]['allytag'];
			echo 'Allytag: '.$allytag.'<br>';

			//Anteil
			$p=$allys[$i]['mission_counter_1']/$score_gesamt;
			echo 'Anteil: '.$p.'<br>';
			$ergebnis_text.=' '.$allytag.' ('.number_format($p*100, 2, ',' ,'.').'%)';

			$user_res[0]=$maxres[0]*$p;
			$user_res[1]=$maxres[1]*$p;
			$user_res[2]=$maxres[2]*$p;
			$user_res[3]=$maxres[3]*$p;

			//jeden aktiven Spieler der Allianz laden um ihm die Rohstoffe gutschreiben zu können
			$result=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE allytag=? AND status=1", [$allytag]);
			while($rowx = mysqli_fetch_array($result)){
				$uid=$rowx['user_id'];
				$sql="UPDATE de_user_data SET restyp01=restyp01+$user_res[0], restyp02=restyp02+$user_res[1], restyp03=restyp03+$user_res[2], restyp04=restyp04+$user_res[3] WHERE user_id = '$uid'";
				echo $sql.'<br>';
				//error_log($sql, 0);
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET restyp01=restyp01+?, restyp02=restyp02+?, restyp03=restyp03+?, restyp04=restyp04+? WHERE user_id=?", [$user_res[0], $user_res[1], $user_res[2], $user_res[3], $uid]);
			}

		}

		//Nachricht an den Server-Chat
		$text='<font color="#9f2ebd">ARES-Missionsergebnis (Gesamtsumme: '.number_format(floor($maxres[0]), 0,"",".").' M, '.number_format(floor($maxres[1]), 0,"",".").' D, '.number_format(floor($maxres[2]), 0,"",".").' I, '.number_format(floor($maxres[3]), 0,"",".").' E):'.$ergebnis_text.'</font>';
		$channel=0;$channeltyp=2;$spielername='[SYSTEM]'; $chat_message=$text;
		insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);

	}else{
		echo 'keine Ares-Teilnehmer<br>';
	}


	//Mission-Counter zurücksetzen
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET mission_counter_1=0", []);	
}

//////////////////////////////////////////////////////////////////////
// HEPHAISTOS
//////////////////////////////////////////////////////////////////////
// wird alle 672 WT aktiv
$trigger_intervall=222;
//$trigger_intervall=1;
if($maxtick % $trigger_intervall == 0){
	echo 'HEPHAISTOS:<br>';

	//alle Alianzen auslesen die an der Mission teilgenommen haben
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_allys WHERE mission_counter_2 > 0 ORDER BY mission_counter_2 DESC LIMIT 1", []);
	$num = mysqli_num_rows($db_daten);
	if($num>0){
		$row = mysqli_fetch_array($db_daten);

		$allytag=$row['allytag'];
		$allyid=$row['id'];

		echo $row['allytag'];

		//Gewinn: 1 Allianzartefakt, 1 Quantenglimmer
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET artefacts=artefacts+1 WHERE id=?", [$allyid]);	
		changeAllyStorageAmount($allyid, 13, 1, false);

		//Nachricht an den Server-Chat
		$text='<font color="#9f2ebd">HEPHAISTOS-Missionsgewinner (1 Allianzartefakt, 1 Quantenglimmer): '.$allytag.'</font>';
		$channel=0;$channeltyp=2;$spielername='[SYSTEM]'; $chat_message=$text;
		insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);

	}else{
		echo 'keine HEPHAISTOS-Teilnehmer<br>';
	}


	//Mission-Counter zurücksetzen
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_allys SET mission_counter_2=0", []);	
}



echo '<br>Allianzmission-ENDE<br><hr>';
?>