<?php
$sql="SELECT * FROM de_system LIMIT 1";
$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
$row = mysqli_fetch_array($db_daten);

//Debug
//$row['create_map_objects']=1;

if($row['create_map_objects']==1){
	//FLAG zurücksetzen
	$sql="UPDATE de_system SET create_map_objects=0;";
	mysqli_query($GLOBALS['dbi'],$sql);
	
	//alte Daten löschen
	mysqli_query($GLOBALS['dbi'], "DELETE FROM `de_map_objects`;");
	mysqli_query($GLOBALS['dbi'], "ALTER TABLE `de_map_objects` AUTO_INCREMENT = 1;");
	mysqli_query($GLOBALS['dbi'], "DELETE FROM `de_map_kanten`;");
	mysqli_query($GLOBALS['dbi'], "DELETE FROM `de_user_map`;");
	mysqli_query($GLOBALS['dbi'], "DELETE FROM `de_user_map_bldg`;");
	mysqli_query($GLOBALS['dbi'], "DELETE FROM `de_user_map_loot`;");
	mysqli_query($GLOBALS['dbi'], "UPDATE `de_user_storage` SET item_wt_change=0;");

	$GLOBALS['anzahl_felder']=0;

	/*
	//Anzahl der Systeme pro Stufe
	$as=array(100,100,100,100,100,100,100,100,100,100);
	
	//gesamtanzahl berechnen
	$anzahl_systeme=0;
	for($i=0;$i<count($as);$i++){
		$anzahl_systeme+=$as[$i];
	}
	*/

	$anzahl_systeme=2000;

	//eine Liste mit einmaligen Namen generieren
	$namensliste=array();	
	while(count($namensliste)<$anzahl_systeme){
		$neuer_name=generierename();
		if(!in_array($neuer_name, $namensliste)){
			$namensliste[]=$neuer_name;
		}
	}
	
	///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	// 1. Zufallsknoten im Kreis ohne Kanten
	///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	/*
	//die Koordinaten generieren, diese müssen einmalig sein
	$coord=array();
	$abweichler=array();
	while(count($coord)<$anzahl_systeme){
		$maxx=53;//mt_rand (0, 750);
		$maxy=53;//$maxx;
		$rx=mt_rand (0, $maxx);
		$fl=mt_rand (1,2);
		if($fl==1)$rx=$rx*(-1);
		$ry=mt_rand (0, $maxy);
		$fl=mt_rand (1,2);
		if($fl==1)$ry=$ry*(-1);

		//absoluter Starpunkt
		$absx=0;
		$absy=0;
		if ($rx*$rx+$ry*$ry <= ($maxx*$maxy)){
			$abweichler_x=mt_rand (0, 50)/150;
			$abweichler_y=mt_rand (0, 50)/150;
			
			$new_coord=($absx+$rx).';'.($absy+$ry);
			$abweichler_werte=($abweichler_x).';'.($abweichler_y);
			if(!in_array($new_coord, $coord)){
				$coord[]=$new_coord;
				$abweichler[]=$abweichler_werte;
			}
		}
	}
	*/

	///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	// 2. Zufallsknoten im Kreis-Raster mit Kanten
	///////////////////////////////////////////////////////////

	//die vorgebaute Sektorliste laden
	//Knoten
	$sql="SELECT * FROM de_basedata_map_knoten LEFT JOIN de_basedata_map_sector ON (de_basedata_map_knoten.sec_id=de_basedata_map_sector.sec_id) ORDER BY de_basedata_map_knoten.sec_id ASC";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$c=0;
	$old_sec_id=-1;
	while($row = mysqli_fetch_array($db_daten)){
		if($old_sec_id!=$row['sec_id']){
			$c=0;
			$old_sec_id=$row['sec_id'];
		}
		$basedata_knoten[$row['sec_id']][$c]['pos_x']=$row['pos_x'];
		$basedata_knoten[$row['sec_id']][$c]['pos_y']=$row['pos_y'];
		$basedata_knoten[$row['sec_id']][$c]['interne_knoten_id']=$row['knoten_id'];
		$c++;
	}

	//print_r($basedata_knoten);

	//Kanten
	$sql="SELECT * FROM de_basedata_map_kanten LEFT JOIN de_basedata_map_sector ON (de_basedata_map_kanten.sec_id=de_basedata_map_sector.sec_id) ORDER BY de_basedata_map_kanten.sec_id ASC";
	$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
	$c=0;
	$old_sec_id=-1;
	while($row = mysqli_fetch_array($db_daten)){
		if($old_sec_id!=$row['sec_id']){
			$c=0;
			$old_sec_id=$row['sec_id'];
		}
		$basedata_kanten[$row['sec_id']][$c]['knoten_id1']=$row['knoten_id1'];
		$basedata_kanten[$row['sec_id']][$c]['knoten_id2']=$row['knoten_id2'];
		$c++;
	}

	print_r($basedata_kanten);

	//die Koordinaten generieren, diese müssen einmalig sein
	$coord=array();
	$abweichler=array();
	$kanten=array();
	$anzahl_systeme=0;

	$x_max=6;//geht von -x bis +x
	$y_max=6;//geht von -y bis +y
	

	$count_sector=0;
	$id=1;
	$level=1;

	//$knoten_teiler=1500;
	$knoten_teiler=1000;

	//die Sektoren werden von rechts unten aus aufgebaut

	for($y=$y_max;$y>=$y_max*-1;$y--){

		for($x=$x_max;$x>=$x_max*-1;$x--){

			//überprüfen ob der Quadrant innerhalb des Kreises ist
			if ($x*$x+$y*$y <= ($x_max*$y_max)){

				//sec_id per Zufall festlegen
				$sec_id=mt_rand(1,count($basedata_knoten)-1);

				//Zentrum hat immer sec_id 0
				if($x==0 && $y==0){
					$sec_id=0;
					echo 'SECCCCCCCCCCCCCCCCCCCCCCCCCCCC 0';
				}

				echo '<br>sec_id: '.$sec_id.' ('.$x.'/'.$y.')<br>';
				
				$sector=array();

				//Knoten
				for($i=0;$i<count($basedata_knoten[$sec_id]);$i++){

					

					//$sector[$sec_id]['coord'][$i]=($x+$basedata_knoten[$sec_id][$i]['pos_x']/$knoten_teiler).';'.($y+$basedata_knoten[$sec_id][$i]['pos_y']/$knoten_teiler);
					$pos_x=$x+$basedata_knoten[$sec_id][$i]['pos_x']/$knoten_teiler;
					$pos_y=$y+$basedata_knoten[$sec_id][$i]['pos_y']/$knoten_teiler;

					
					//System erstellen				
					$name=$namensliste[$id-1];

					/////////////////////////////////////////////////
					//besonderes System?
					/////////////////////////////////////////////////
					$special_system=0;

					//immer sichtbar? z.B. Startpunkt im Zentrum
					if($sec_id==0 && $basedata_knoten[$sec_id][$i]['interne_knoten_id']==9){
						$special_system=1;
						$always_visible=1;
						$name='DER EINGANG';
					}else{
						$always_visible=0;
					}

					echo '<br>'.$level.': #'.$id.' ('.$pos_x.':'.$pos_y.') | '.$name;
					//echo '<br>'.$level.': #'.$id.' ('.($pos_x+$abweichler_x).':'.($pos_y+$abweichler_y).') | '.$name;
					/////////////////////////////////////////////////
					//Klasse erzeugen und mit Daten befüllen
					/////////////////////////////////////////////////
					$new_system = NEW map_system;
					$new_system->setSystemName($name);
					$new_system->setSystemLevel($level);
					$new_system->setSystemPosX($pos_x);
					$new_system->setSystemPosY($pos_y);		
					$system_typ=mt_rand(0,7);
					$system_typ=1;
					$new_system->setSystemTyp($system_typ);
					$system_subtyp=0;
					switch($system_typ){
						case 0://bewohnter planet
							//$system_subtyp=mt_rand(0,5);
						break;

						case 1://unbewohnter planet
							$system_subtyp=mt_rand(0,4);
						break;
						
						case 3:
							$system_subtyp=mt_rand(0,3);
						break;
			
						default:
						break;
					}

					$new_system->setSystemSubTyp($system_subtyp);
					//die einzelnen Geländefelder generieren
					$new_system->generateFields();
					//Loot generieren							
					//$new_system->generateLoot();

					if($special_system>0){
						$new_system->special_system=$special_system;
					}
					
					//print_r($new_system);

					
					//System in die DB schreiben
					$data=serialize($new_system);
					$sql="INSERT INTO de_map_objects SET data='$data', system_typ='$system_typ', system_subtyp='$system_subtyp', always_visible='$always_visible', pos_x='$pos_x', pos_y='$pos_y', cluster_x='$x', cluster_y='$y'";
					mysqli_query($GLOBALS['dbi'],$sql);
					
					$interne_knoten_id=$basedata_knoten[$sec_id][$i]['interne_knoten_id'];
					$sector['interne_knoten_ids'][$interne_knoten_id]=mysqli_insert_id($dbi);


					//$coord[]=($x+$basedata_knoten[$sec_id][$i]['pos_x']/$knoten_teiler).';'.($y+$basedata_knoten[$sec_id][$i]['pos_y']/$knoten_teiler);
					//$coord[]=($x).';'.($y);
					$anzahl_systeme++;
					$id++;
				}

				//print_r($sector);

				//Kanten
				//nachdem die Knoten in der DB sind diese mit Kanten verbinden
				for($i=0;$i<count($basedata_kanten[$sec_id]);$i++){
					echo '<br>Kanten: '.$basedata_kanten[$sec_id][$i]['knoten_id1'].':'.$basedata_kanten[$sec_id][$i]['knoten_id2'];
					$knoten_id1=$basedata_kanten[$sec_id][$i]['knoten_id1'];
					$knoten_id2=$basedata_kanten[$sec_id][$i]['knoten_id2'];
					echo '<br>Knoten-IDs: '.$sector['interne_knoten_ids'][$knoten_id1].':'.$sector['interne_knoten_ids'][$knoten_id2];

					$knoten_id1=$sector['interne_knoten_ids'][$knoten_id1];
					$knoten_id2=$sector['interne_knoten_ids'][$knoten_id2];

					$sql="INSERT INTO de_map_kanten SET knoten_id1='$knoten_id1', knoten_id2='$knoten_id2'";
					mysqli_query($GLOBALS['dbi'],$sql);
				}



				/*

				$data=array();

				//Zufallsquadrant bauen (max 0.4)
				for($k=0;$k<mt_rand(2,5);$k++){
					$wert_x=mt_rand(0,2)/5;
					$wert_y=mt_rand(0,2)/5;

					$data[]=$wert_x;
					$data[]=$wert_y;
				}						
				
				for($i=0;$i<count($data)/2;$i++){
					$abweichler_x=mt_rand (0, 5)/100;
					$abweichler_y=mt_rand (0, 5)/100;
					
					$new_coord=($x+$data[$i*2]).';'.($y+$data[$i*2+1]);
					$abweichler_werte=($abweichler_x).';'.($abweichler_y);


					if(!in_array($new_coord, $coord)){
						$coord[]=$new_coord;
						$abweichler[]=$abweichler_werte;

						$anzahl_systeme++;
					}
				}*/

				$count_sector++;

				/////////////////////////////////////////////////////////////////
				//den Sektor mit den nebenliegenden Sektoren per Kante verbinden
				/////////////////////////////////////////////////////////////////
				//gilt nicht in der untersten Zeile und in der Spalte ganz rechts, da es dort keinen passenden Nachbar geben kann
				if($y<$y_max  && $x<$x_max){

					/////////////////////////////////////////////////////////
					//nach rechts
					/////////////////////////////////////////////////////////

					//das system, dass am weitesten rechts im aktuellen sektor steht
					$sql="SELECT MAX(pos_x) AS search_value FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$y' LIMIT 1";
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$search_value=$row['search_value'];
					$sql="SELECT id FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$y' AND pos_x='$search_value' LIMIT 1";
					//echo $sql;
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$knoten_id1=$row['id'];
					//echo '<br>ID: '.$row['id'];

					//nach rechts verbinden, dazu deren system laden, das am weitesten links steht
					$xn=$x+1;
					$sql="SELECT MIN(pos_x) AS search_value FROM de_map_objects WHERE cluster_x='$xn' AND cluster_y='$y' LIMIT 1";
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$search_value=$row['search_value'];
					$sql="SELECT id FROM de_map_objects WHERE cluster_x='$xn' AND cluster_y='$y' AND pos_x='$search_value' LIMIT 1";
					//echo $sql;
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$knoten_id2=$row['id'];

					//wir haben beide knoten, können also eine kante einfügen
					if($knoten_id1>0 && $knoten_id2>0){
						$sql="INSERT INTO de_map_kanten SET knoten_id1='$knoten_id1', knoten_id2='$knoten_id2'";
						echo $sql;
						mysqli_query($GLOBALS['dbi'],$sql);
					}

					/////////////////////////////////////////////////////////
					//nach unten
					/////////////////////////////////////////////////////////

					//das system, dass am weitesten unten im aktuellen sektor steht
					$sql="SELECT MAX(pos_y) AS search_value FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$y' LIMIT 1";
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$search_value=$row['search_value'];
					$sql="SELECT id FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$y' AND pos_y='$search_value' LIMIT 1";
					//echo $sql;
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$knoten_id1=$row['id'];
					//echo '<br>ID: '.$row['id'];

					//nach unten verbinden, dazu deren system laden, das am weitesten oben steht
					$yn=$y+1;
					$sql="SELECT MIN(pos_y) AS search_value FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$yn' LIMIT 1";
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$search_value=$row['search_value'];
					//echo 'A:'.$search_value;
					$sql="SELECT id FROM de_map_objects WHERE cluster_x='$x' AND cluster_y='$yn' AND pos_y='$search_value' LIMIT 1";
					//echo $sql;
					$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
					$row = mysqli_fetch_array($db_daten);
					$knoten_id2=$row['id'];

					//wir haben ggf. beide knoten, können also eine kante einfügen
					if($knoten_id1>0 && $knoten_id2>0){
						$sql="INSERT INTO de_map_kanten SET knoten_id1='$knoten_id1', knoten_id2='$knoten_id2'";
						echo '<br>IK:'.$sql;
						echo '<br>('.$x.'/'.$y.')';
						mysqli_query($GLOBALS['dbi'],$sql);
					}
				}				

			}
		}
	}

	//nachdem die Sektoren angelegt

	echo '<br>Sektoranzahl: '.$count_sector.'<br>';

	///////////////////////////////////////////
	//jetzt die Battlegrounds erstellen
	///////////////////////////////////////////
	$anz_bg=3;
	$koord_min[0]=1;
	$koord_max[0]=1;
	$koord_min[1]=2;
	$koord_max[1]=2;				
	$koord_min[2]=3;
	$koord_max[2]=3;
	/*				
	$koord_min[3]=6;
	$koord_max[3]=6;				
	$koord_min[4]=7;
	$koord_max[4]=7;
	*/

	$bg_names[0]='BATTLEGROUND ALPHA';
	$bg_names[1]='BATTLEGROUND BETA';
	$bg_names[2]='BATTLEGROUND GAMMA';
	//$bg_names[3]='BATTLEGROUND DELTA';
	//$bg_names[4]='BATTLEGROUND EPSILON';

	for($i=0;$i<$anz_bg;$i++){
		//passendes System aud der DB holen
		$cluster_x=mt_rand($koord_min[$i],$koord_max[$i]);
		if(mt_rand(1,100)>50){
			$cluster_x=$cluster_x*-1;
		}

		$cluster_y=mt_rand($koord_min[$i],$koord_max[$i]);
		if(mt_rand(1,100)>50){
			$cluster_y=$cluster_y*-1;
		}					

		//$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 AND cluster_x='$cluster_x' AND cluster_y='$cluster_y' ORDER BY RAND() LIMIT 1;";
		//$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 ORDER BY RAND() LIMIT 1;";
		$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 AND (cluster_x < -1 OR cluster_x > 1) AND (cluster_y < -1 OR cluster_y > 1) ORDER BY RAND() LIMIT 1;";
		$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
		$row = mysqli_fetch_array($db_daten);
		$system_id=$row['id'];

		//Daten anpassen
		$system_subtyp=$i;

		$new_system=unserialize($row['data']);

		$new_system->setSystemTyp(4);
		$new_system->setSystemSubTyp($system_subtyp);
		$new_system->setSystemName($bg_names[$i]);

		$system_data=serialize($new_system);

		
		$sql="UPDATE de_map_objects SET data='$system_data', system_typ=4, system_subtyp='$system_subtyp' WHERE id='$system_id';";
		mysqli_query($GLOBALS['dbi'],$sql);
	}


	////////////////////////////////////////////////
	//jetzt die weiteren Special Systeme erstellen
	////////////////////////////////////////////////
	$koord_min=array();
	$koord_max=array();

	$koord_min[0]=3;
	$koord_max[0]=3;

	$koord_min[1]=3;
	$koord_max[1]=3;
	
	$koord_min[2]=3;
	$koord_max[2]=3;
	
	$koord_min[3]=3;
	$koord_max[3]=3;
	
	$koord_min[4]=3;
	$koord_max[4]=3;
	
	$koord_min[5]=3;
	$koord_max[5]=3;	

	/*				
	$koord_min[1]=2;
	$koord_max[1]=2;				
	$koord_min[2]=3;
	$koord_max[2]=3;
	$koord_min[3]=6;
	$koord_max[3]=6;				
	$koord_min[4]=7;
	$koord_max[4]=7;
	*/

	//das eigentlich Systme 0 ist der Eingang, aber das wird an anderer Stelle erzeugt
	$specialsystem_names[0]='shaKer';
	$specialsystem_names[1]='Ares';
	$specialsystem_names[2]='Hephaistos';
	$specialsystem_names[3]='Hades';
	$specialsystem_names[4]='Hekate';
	$specialsystem_names[5]='Thanatos';

	for($i=0;$i<count($specialsystem_names);$i++){
		//passendes System aud der DB holen
		$cluster_x=mt_rand($koord_min[$i],$koord_max[$i]);
		if(mt_rand(1,100)>50){
			$cluster_x=$cluster_x*-1;
		}

		$cluster_y=mt_rand($koord_min[$i],$koord_max[$i]);
		if(mt_rand(1,100)>50){
			$cluster_y=$cluster_y*-1;
		}					

		//$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 AND system_typ<>5 AND cluster_x='$cluster_x' AND cluster_y='$cluster_y' ORDER BY RAND() LIMIT 1;";
		//$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 AND system_typ<>5 ORDER BY RAND() LIMIT 1;";
		$sql="SELECT * FROM de_map_objects WHERE system_typ<>4 AND system_typ<>5 AND (cluster_x < -1 OR cluster_x > 1) AND (cluster_y < -1 OR cluster_y > 1) ORDER BY RAND() LIMIT 1;";
		$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
		$row = mysqli_fetch_array($db_daten);
		$system_id=$row['id'];

		//Daten anpassen
		$system_subtyp=$i+2;

		$new_system=unserialize($row['data']);

		$new_system->setSystemTyp(4);

		$new_system->special_system=$system_subtyp;

		$new_system->setSystemName($specialsystem_names[$i]);

		$system_data=serialize($new_system);

		
		$sql="UPDATE de_map_objects SET data='$system_data', system_typ=5, system_subtyp='$system_subtyp' WHERE id='$system_id';";
		echo $sql;
		mysqli_query($GLOBALS['dbi'],$sql);
	}	

	//Info an den Chat über Neugenerierung
	$text='<font color="#9f2ebd">Die vergessenen Systeme wurden geresettet. Neue Systeme: '.$anzahl_systeme.' (Felder: '.$GLOBALS['anzahl_felder'].')</font>';
	$channel=0;$channeltyp=2;$spielername='[SYSTEM]'; $chat_message=$text;
	insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);

	echo 'AAAA:'.$text;



	/*
	while(count($coord)<$anzahl_systeme){
		$maxx=53;//mt_rand (0, 750);
		$maxy=53;//$maxx;
		$rx=mt_rand (0, $maxx);
		$fl=mt_rand (1,2);
		if($fl==1)$rx=$rx*(-1);
		$ry=mt_rand (0, $maxy);
		$fl=mt_rand (1,2);
		if($fl==1)$ry=$ry*(-1);

		//absoluter Starpunkt
		$absx=0;
		$absy=0;
		if ($rx*$rx+$ry*$ry <= ($maxx*$maxy)){
			$abweichler_x=mt_rand (0, 50)/150;
			$abweichler_y=mt_rand (0, 50)/150;
			
			$new_coord=($absx+$rx).';'.($absy+$ry);
			$abweichler_werte=($abweichler_x).';'.($abweichler_y);
			if(!in_array($new_coord, $coord)){
				$coord[]=$new_coord;
				$abweichler[]=$abweichler_werte;
			}
		}
	}*/



	///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	//Systeme generieren
	///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	/*
	$id=1;
	$level=1;
	for($anzahl=0;$anzahl<$anzahl_systeme;$anzahl++){

		$name=$namensliste[$id-1];
		$h=explode(";",$coord[$id-1]);
		$pos_x=$h[0];
		$pos_y=$h[1];
		echo '<br>'.$level.': #'.$id.' ('.$pos_x.':'.$pos_y.') | '.$name;
		//echo '<br>'.$level.': #'.$id.' ('.($pos_x+$abweichler_x).':'.($pos_y+$abweichler_y).') | '.$name;
		
		//Klasse erzeugen und mit Daten bef�llen
		$new_system = NEW map_system;
		$new_system->setSystemName($name);
		$new_system->setSystemLevel($level);
		$new_system->setSystemPosX($pos_x);
		$new_system->setSystemPosY($pos_y);		
		//$system_typ=mt_rand(0,6);
		$system_typ=2;
		$new_system->setSystemTyp($system_typ);
		$system_subtyp=0;
		switch($system_typ){
			case 0:
				$system_subtyp=mt_rand(0,5);
			break;

			case 2:
				$system_subtyp=mt_rand(0,3);
			break;

			default:
			break;
		}
		$new_system->setSystemSubTyp($system_subtyp);
		$new_system->generateLoot();
		
		//print_r($new_system);
		
		//System in die DB schreiben
		$data=serialize($new_system);
		$sql="INSERT INTO de_map_objects SET data='$data'";
		$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
		
		$id++;
	}
	*/
}

function generierename(){
	//namen zusammenbauen
	//struktur: 1-4 silben bindestrich 1-5 silben
	$buchstaben='ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$silben=array('ar','xa','xo','na','an','ra','ox','ax','yn','ny','za','az',
	'zy','yz','ka','ak','as','sa','co','oc','ac','ca','te','et','tz','zt','it','ti',
	'tx','xt','lo','ol','yl','ly','ay','ya','ry','yr','no','na','ne','ni','nu',
	'so','si','se','sa','su','mo','mi','me','mu','ma','ka','ke','ko','ki','ku',
	'ta','te','ti','tu','to','la','le','lu','li','lo','pa','pe','po','pi','pe','pu',
	'ja','jo','je','ji','ju','da','de','du','di','di','ra','re','ro','ri','ru');
	
	$zusatz=array('End', 'Proxima', 'Prime', 'Gate', 'Doom', 'Eta', 'North', 'East', 'South', 'West',
	'Ash','Cat', 'Tor', 'Pre', 'Sun', 'Star','Hell', 'Ether', 'Door', 'Eternal', 'Final',
	'Heaven', 'Ocean', 'Pelar', 'Katez', 'Figar', 'Alron', 'Melis', 'Hope', 'Pain', 'New', 
	'Klash', 'Fall', 'War', 'Peace',
	"Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda",
	"My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");

	$anzsilben=count($silben);
	$anzzusatz=count($zusatz);

	$name='';
	
	$art=mt_rand(1,8);
	
	switch($art){
	case 1:
	//silben-silben
	$csilben=mt_rand(1,4);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$csilben=mt_rand(1,5);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	break;
	case 2:
	//silben zusatz
	$csilben=mt_rand(3,6);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if(mt_rand(1,100)<=10)
	{
	 if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	 $name=$name.$zusatz[mt_rand(0,$anzzusatz-1)];
	}
	break;
	case 3:
	//silben zahl
	$csilben=mt_rand(2,3);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$name=$name.mt_rand(1,1000);
	break;

	case 4:
	//zahl silbe
	$name=$name.mt_rand(1,100);
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$csilben=mt_rand(2,3);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	break;

	case 5:
	//silbe zahl silbe
	$csilben=mt_rand(1,3);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$name=$name.mt_rand(1,500);
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$csilben=mt_rand(1,3);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	break;

	case 6:
	//zahl silbe zahl
	$name=$name.mt_rand(1,400);
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$csilben=mt_rand(1,3);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	$name=$name.mt_rand(1,200);
	break;

	case 7:
	//silben buchstabe
	$csilben=mt_rand(3,6);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	if(mt_rand(1,100)<=10)
	{
	 if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	 $name=$name.$buchstaben[mt_rand(0,25)];
	}
	break;

	case 8:
	//buchstabe silben
	if(mt_rand(1,100)<=10)
	{
	 $name=$name.$buchstaben[mt_rand(0,25)];
	 if (mt_rand(1,2)==1)$name.='-';else $name.=' ';
	}
	$csilben=mt_rand(3,6);
	for ($i=1; $i<=$csilben; $i++)
	{
		$suchsilbe=$silben[mt_rand(0,$anzsilben-1)];
		if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
		$name.=$suchsilbe;
	}
	break;


	}//switch ende

	
	
	//ergebnis zur�ckliefern
	return $name;
}

?>