<?php
//dise Funktion gibt das System des SK zurück, wenn es keinen SK gibt, dann ist der Wert 0
function issectorcommander() {
	global $sector, $sv_maxsystem;

	//global $system;
	//alle user des sektors auslesen
	if($sector>0){
		$sql="SELECT votefor, `system` FROM de_user_data WHERE sector=$sector";
		//echo $sql;
		$resultsk = mysqli_query($GLOBALS['dbi'], $sql);
		$anz = mysqli_num_rows($resultsk);

		while($row=mysqli_fetch_array($resultsk)){
			//$su[$row["system"]][0]=$row["nic"];
			$su[$row["system"]][1]=$row["votefor"];
			$su[$row["system"]][2]=$row["system"];
		}
		$ska=array(0,0,0,0,0,0,0,0,0,0,0);
		//alle stimmen zählen
		for ($i = 1; $i <= $sv_maxsystem; $i++){
			if(isset($su[$i][1])){
				$ska[$su[$i][1]]++;
			}
		}
		//maximalwert suchen
		$mw=0;
		for ($i = 1; $i <= $sv_maxsystem; $i++){
			if ($ska[$i]>$mw)$mw=$ska[$i];
		}

		//schauen ob wert doppelt vorhanden, wenn kleiner 6
		$anzahl=0;
		if ($mw<8)
		{for ($i = 1; $i <= $sv_maxsystem; $i++)if($ska[$i]==$mw)$anzahl++;}
		else $anzahl=1;


		//wenn nicht 1, dann gibts mehrere mit der stimmenanzahl
		if ($anzahl!=1) $mw=0;
		$sksys=0;

		if($mw!=0){
			//noch das system des sk auslesen
			for ($i = 1; $i <= $sv_maxsystem; $i++){
				if($ska[$i]==$mw)$sksys=$i;
			}
		}
	}else{
		$sksys=0;
	}

	return $sksys;
}

function getSKSystemBySecID($sec_id){
	global $sv_maxsystem;

	$sector=$sec_id;

	//echo '<br>IS_SK: '.$sector.'/'.$sv_maxsystem;

	//global $system;
	//alle user des sektors auslesen
	if($sector>0){
		$sql="SELECT votefor, `system` FROM de_user_data WHERE sector=$sector";
		//echo $sql;
		$resultsk = mysqli_query($GLOBALS['dbi'], $sql);
		$anz = mysqli_num_rows($resultsk);

		while($row=mysqli_fetch_array($resultsk)){
			//$su[$row["system"]][0]=$row["nic"];
			$su[$row["system"]][1]=$row["votefor"];
			$su[$row["system"]][2]=$row["system"];
		}
		$ska=array(0,0,0,0,0,0,0,0,0,0,0);
		//alle stimmen zählen
		for ($i = 1; $i <= $sv_maxsystem; $i++){
			if(isset($su[$i][1])){
				$ska[$su[$i][1]]++;
			}
		}
		//maximalwert suchen
		$mw=0;
		for ($i = 1; $i <= $sv_maxsystem; $i++){
			if ($ska[$i]>$mw)$mw=$ska[$i];
		}

		//schauen ob wert doppelt vorhanden, wenn kleiner 6
		$anzahl=0;
		if ($mw<8)
		{for ($i = 1; $i <= $sv_maxsystem; $i++)if($ska[$i]==$mw)$anzahl++;}
		else $anzahl=1;


		//wenn nicht 1, dann gibts mehrere mit der stimmenanzahl
		if ($anzahl!=1) $mw=0;
		$sksys=0;

		if($mw!=0){
			//noch das system des sk auslesen
			for ($i = 1; $i <= $sv_maxsystem; $i++){
				if($ska[$i]==$mw)$sksys=$i;
			}
		}
	}else{
		$sksys=0;
	}

	return $sksys;
}

?>