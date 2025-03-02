<?php
////////////////////////////////////////////////////////////
//Rohstoffertrag der Gebäude, nur für aktive Spieler
////////////////////////////////////////////////////////////
echo '<br>[MAP] - Gebäude Rohstoffertrag';

//alle Gebäude laden
$bldg_data=array();
$res = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_map_bldg WHERE bldg_time < ".time().";");
//echo '<br>A: '."SELECT * FROM de_user_map_bldg WHERE bldg_time < ".time().";";
while($row = mysqli_fetch_array($res)){
	
	$bldg_level=$row['bldg_level'];
	$bldg_data[$row['user_id']][]=array($row['bldg_id'],$bldg_level);

}

//print_r($bldg_data);

//bldg_id entspricht item_id
$valid_rohstoff_ids=array(
	array(3,3),
	array(4,4),
	array(5,5),
	array(6,6),
	array(7,7),
	array(8,8),
	array(9,9),
	array(10,10),
	array(11,11),
	array(12,12)
);

//die einzelnen User abarbeiten, die Gebäudedaten befinden sich alle im Array $bldg_data und für jeden aktiven Spieler wird dieses durchlaufen
$aktive_user=array();
$result = mysqli_query($GLOBALS['dbi'],"SELECT de_user_data.user_id FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.npc=0");
while($row = mysqli_fetch_array($result)){
	$uid=$row['user_id'];
	$aktive_user[]=$uid;
	$res=array();

	//Rohstoffbonus für eroberte Kollektoren
	$col_stolen=getStolenColByUID($uid);
	$prozentwert=$col_stolen*5;
	if($prozentwert>500){
		$prozentwert=500;
	}

	$prozentwert=1+($prozentwert/100);

	//echo '<br>user_id: '.$row['user_id'];
	$bldg_score=0;

	if(isset($bldg_data[$row['user_id']]) && is_array($bldg_data[$row['user_id']])){

		for($i=0;$i<count($bldg_data[$row['user_id']]);$i++){
			//echo '<br>bldg_id: '.$bldg_data[$row['user_id']][$i][0];
			//echo '<br>bldg_level: '.$bldg_data[$row['user_id']][$i][1];
			
			//Rohstoffertrag aufsummieren, wenn das Gebäude Rohstoffe produziert
			//$res[$bldg_data[$row['user_id']][$i][0]]+=$bldg_data[$row['user_id']][$i][1]*$bldg_data[$row['user_id']][$i][1];
			
			if(isset($GLOBALS['map_buildings'][$bldg_data[$row['user_id']][$i][0]]['production_amount'])){
				if(!isset($res[$bldg_data[$row['user_id']][$i][0]])){
					$res[$bldg_data[$row['user_id']][$i][0]]=0;
				}

				$res[$bldg_data[$row['user_id']][$i][0]]+=
				$GLOBALS['map_buildings']
					[$bldg_data[$row['user_id']][$i][0]]
					['production_amount']
					[$bldg_data[$row['user_id']][$i][1]-1]
					*$prozentwert;
			}
			//echo '<br>R: '.$GLOBALS['map_buildings'][$bldg_data[$row['user_id']][$i][0]]['production_amount'][$bldg_data[$row['user_id']][$i][1]];

			//Gebäudepunkte aufsummieren: Level ins Quadrant
			$bldg_score+=$bldg_data[$row['user_id']][$i][1]*$bldg_data[$row['user_id']][$i][1];
		}
	}


	//alle Rohstoffarten durchgehen und wenn Ertrag > 0, dann updaten
	for($i=0; $i<count($valid_rohstoff_ids);$i++){
		$bldg_id=$valid_rohstoff_ids[$i][0];
		$item_id=$valid_rohstoff_ids[$i][1];
		if(isset($res[$bldg_id]) && $res[$bldg_id]>0){
			//echo '<br>UPDATE: UID '.$row['user_id'].' ITEMID: '.$item_id.' AMOUNT '.$res[$bldg_id];
			change_storage_amount($row['user_id'], $item_id, $res[$bldg_id], true);
			
		
		}
		//print_r($res);
	}

	//Gebäudepunkte in der DB hinterlegen
	mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET pve_bldg_score='$bldg_score' WHERE user_id='$uid'");
	//echo '<br>bldg_score: '.$bldg_score;

}

/////////////////////////////////////////////////////
// specialsystem 2 shaker/agenten
/////////////////////////////////////////////////////
//wird alle 100 WT ausgeführt
if($rundenalter_wt % 100 == 0){
	//system-id auslesen
	$result = mysqli_query($GLOBALS['dbi'],"SELECT * FROM `de_map_objects` WHERE system_typ=5 and system_subtyp=2");
	$row = mysqli_fetch_array($result);
	$system_id=$row['id'];

	if($system_id>0){
		//alle Spielerdaten laden, die dort bereits etwas haben
		$result = mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_user_map WHERE map_id='$system_id' AND specialsystem_data<>'';");
		while($row = mysqli_fetch_array($result)){
			$user_id=$row['user_id'];
			if(in_array($user_id, $aktive_user)){
				$data=unserialize($row['specialsystem_data']);
				if($data['phase']==3){
					$sql="UPDATE de_user_data SET agent=agent+100 WHERE user_id='$user_id';";
					echo $sql;
					mysqli_query($GLOBALS['dbi'],$sql);
				}
			}
		}
	}
}


/////////////////////////////////////////////////////
// PVE-Gesamtpunkte berechnen
/////////////////////////////////////////////////////
mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET pve_score=tradesystemscore / 100 + pve_bldg_score WHERE npc=0");

/////////////////////////////////////////////////////
// VS automatisches Erkunden
/////////////////////////////////////////////////////
$time=date("YmdHis");
//$kosten_zeit=15*60*$GLOBALS['tech_build_time_faktor']-5;
$kosten_zeit=15*60*$GLOBALS['tech_build_time_faktor']*2;
$result = mysqli_query($GLOBALS['dbi'],"SELECT de_user_data.user_id, de_user_data.sonde FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_login.status=1 AND de_user_data.vs_auto_explore=1");
while($row = mysqli_fetch_array($result)){
	$user_id=$row['user_id'];

	//testen ob evtl. schon etwas erkundet wird
	$db_daten = mysqli_query($GLOBALS['dbi'],"SELECT COUNT(*) AS anzahl FROM de_user_map WHERE user_id='$user_id' AND known_since > ".(time()));
	$rowx = mysqli_fetch_array($db_daten);

	if($rowx['anzahl']>0){
		//aussetzen, da bereits etwas läuft
		//echo 'erkunden läuft';
		mysqli_query($GLOBALS['dbi'],"UPDATE de_user_map SET known_since=1000 WHERE user_id='".$user_id."';");
	}else{
		//echo 'erkunden läuft nicht';
		//hat man genug Sonden, wenn nicht wird die automatische Erkundung abgebrochen
		if($row['sonde']>=10){
			//schauen ob es noch ein unerkundetes System gibt und wenn es mehrere gibt davon eins per Zufall wählen

			$sichtbare_systeme=array();
			$immer_sichtbare_systeme=array();
			$erforschte_systeme=array();
			$erforschte_systeme_koordinaten=array();
			
			//Kanten laden
			$kanten=array();
			$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_kanten");
			while($row = mysqli_fetch_array($db_daten)){
				$kanten[]=array($row['knoten_id1'],$row['knoten_id2']);
			}
			
			//die erforschten Systeme laden
			$sql="SELECT map_id FROM de_user_map WHERE user_id='".$user_id."' AND known_since>0 AND known_since<'".(time())."';";
			$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
			while($row = mysqli_fetch_array($db_daten)){
				//sie sind sichtbar und erforscht
				$sichtbare_systeme[]=$row['map_id'];
				$erforschte_systeme[]=$row['map_id'];
			}
			
			//die sichtbaren Systeme um die Systeme ergänzen, die über Kanten mit erforschten Systemen verknüpft sind
			for($i=0;$i<count($erforschte_systeme);$i++){
				//für jedes System alle Kanten durchgehen
				$map_id=$erforschte_systeme[$i];
				//echo 'map_id: '.$map_id;
				for($k=0; $k<count($kanten);$k++){
					//echo ' kanten_ids: '.$kanten[$k][0].'/'.$kanten[$k][1];
					//knoten1 testen
					if($map_id==$kanten[$k][0]){
						//echo 'gefunden 1';
						if(!in_array($kanten[$k][1],$sichtbare_systeme)){
							$sichtbare_systeme[]=$kanten[$k][1];
							//echo 'gefunden 1a';
						}
					}
			
					//knoten2 testen
					if($map_id==$kanten[$k][1]){
						//echo 'gefunden 2';
						if(!in_array($kanten[$k][0],$sichtbare_systeme)){
							$sichtbare_systeme[]=$kanten[$k][0];
							//echo 'gefunden 2a';
						}
					}
				}
			}

			//Systeme laden
			$esysteme=array();
			$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_map_objects");
			while($row = mysqli_fetch_array($db_daten)){
				if(!in_array($row['id'], $erforschte_systeme) && in_array($row['id'], $sichtbare_systeme)){
					$esysteme[]=$row['id'];
				}
			}


			//print_r($esysteme);

			if(!empty($esysteme)){
				//per Zufall eine id auslesen
				$map_id=$esysteme[mt_rand(0, count($esysteme)-1)];

				//erkundetes System hinterlegen
				$sql="INSERT INTO de_user_map SET user_id='".$user_id."', map_id='".$map_id."', known_since='".(time()+$kosten_zeit)."';";
				mysqli_query($GLOBALS['dbi'],$sql);

				//sonden abziehen
				mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET sonde=sonde-10 WHERE user_id=$user_id");
			}else{
				//es gibt keine Systeme mehr zu erforschen

				//automatische Erkundung deaktivieren
				mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET vs_auto_explore=0 WHERE user_id=$user_id");

				//Meldung in die News packen
				$newstext='Die automatische Erkundung der Vergessenen Systeme wurde beendet, da alle Systeme erkundet worden sind.';
				mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($user_id, 60,'$time','$newstext')");
			}

		}else{
			//automatische Erkundung deaktivieren
			mysqli_query($GLOBALS['dbi'],"UPDATE de_user_data SET vs_auto_explore=0 WHERE user_id=$user_id");
			
			//Meldung in die News packen
			$newstext='Die automatische Erkundung der Vergessenen Systeme wurde beendet, da nicht genug Sonden vorhanden sind.';
			mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($user_id, 60,'$time','$newstext')");
		}
	}
}
