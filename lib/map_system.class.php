<?php
#[\AllowDynamicProperties]
class map_system{
	private $system_name;
	private $system_level;
	private $pos_x;
	private $pos_y;
	private $system_typ;
	private $system_subtyp;

	public $special_system;

	private $system_biohazard_level;
	private $system_radiation_level;

	private $system_loot=array();
	
	public function getSystemName(){
		return $this->system_name;
	}

	public function setSystemName($value){
		$this->system_name=$value;
	}

	public function getSystemLevel(){
		return $this->system_level;
	}

	public function setSystemLevel($value){
		$this->system_level=$value;
	}

	public function getSystemPosX(){
		return $this->pos_x;
	}

	public function setSystemPosX($value){
		$this->pos_x=$value;
	}

	public function getSystemPosY(){
		return $this->pos_y;
	}

	public function setSystemPosY($value){
		$this->pos_y=$value;
	}

	public function getSystemTyp(){
		return $this->system_typ;
	}

	public function setSystemTyp($value){
		$this->system_typ=$value;
	}

	public function getSystemSubTyp(){
		return $this->system_subtyp;
	}

	public function setSystemSubTyp($value){
		$this->system_subtyp=$value;
	}	

	public function generateFields(){
		if($this->system_typ==1){//bewohnbare Planeten
			//je nach Subtyp sind die Felder geblockt
			
			
			//array('Gaia', 'Eiswelt','W&uuml;stenwelt', 'Vulkanwelt', 'Eiswelt'),

			//$this->special_system

			$anzahl_felder=mt_rand(7,10);

			for($i=0;$i<$anzahl_felder;$i++){
				///////////////////////////////////////////////////
				//field_typ bestimmen
				///////////////////////////////////////////////////
				//nicht jedes Feld hat eine besondere Ressource
				if(mt_rand(0,100)>50 && $i>0){
					//der maximale Typ ist Feldabhängig, höhehe Feld-ID bedeutet eine Chance auf höhere Ressourcentypen
					$max_field_typ=count($GLOBALS['map_field_typ'])-1;
					if($max_field_typ>$i+5){
						$max_field_typ=$i;
					}

					$field_typ=mt_rand(0, $max_field_typ);

				}else{
					$field_typ=0;
				}

				$fields[$i][0]=$field_typ;

				///////////////////////////////////////////////////
				//Feldblocker bestimmen
				///////////////////////////////////////////////////
				$blocker_typ=0;
				$blocker_amount=0;

				/*
				//nicht jedes Feld hat einen Blocker, Feld 0 nie
				if(mt_rand(0,100)>80 && $i>0){
					//es gibt einen Blocker
					$max_blocker_id=count($GLOBALS['map_field_blocker'])-1;
					
					$blocker_typ=mt_rand(1, $max_blocker_id);
					$blocker_amount=mt_rand(1,10)*1000;

					$fields[$i][1][0]=$blocker_typ;//Typ
					$fields[$i][1][1]=$blocker_amount;//Menge
				}else{
					//kein Blocker
					$GLOBALS['anzahl_felder']++;
				}
				*/

				$GLOBALS['anzahl_felder']++;

				///////////////////////////////////////////////////
				// Loot bestimmen und ab welchen Gebäudeleveln:
				// Titanen-Energiekerne/Palenium/Bodenschätze,
				// Tronic, Artefakte
				///////////////////////////////////////////////////
				$loot_typ=0;
				//gibt es Loot?
				if(mt_rand(0,100)>30){

					//auf welchem Feldlevel gibt es Loot?
					$loot_level=mt_rand(2,10);

					//Chance auf Item aus der de_item_data Tabelle
					if(mt_rand(0,100)>25){
						$loot_typ=1;
						//welches Item?
						$loot_subtyp=mt_rand(1,12);

						//Menge bestimmen
						if(in_array($loot_subtyp, array(2))){//Titanen-Energiekern
							$loot_amount=mt_rand(2,6);
						}elseif(in_array($loot_subtyp, array(1))){//Palenium
							$loot_amount=mt_rand(20,30)*$loot_level;
						}else{//normale Rohstoffe
							$loot_amount=mt_rand(100,300)*$loot_level;
						}

					}elseif(mt_rand(0,100)>60){//Tronic
						$loot_typ=2;
						$loot_subtyp=0;
						$loot_amount=$loot_level;

					}else{//Artefakte
						$loot_typ=3;
						$loot_amount=1;
						$loot_subtyp=-1;
						//$not_allowed=array(10,11,18);
						$not_allowed=array(18);
				
						while($loot_subtyp==-1){
							$loot_subtyp=mt_rand(0,21);
							if(in_array($loot_subtyp, $not_allowed)){
								$loot_subtyp=-1;
							}
						}
						
					}

					$fields[$i][2][0]=$loot_typ;//Typ
					$fields[$i][2][1]=$loot_subtyp;//Subtyp
					$fields[$i][2][2]=$loot_amount;//Menge
					$fields[$i][2][3]=$loot_level;//in Level
				}
			}

			$this->fields=$fields;
		}
	}

	public function showFields(){
		$content='<div style="display: flex; margin-top: 16px;">';

		//linke Spalte
		$content.='<div style="width: 400px;">';

		//print_r($this->playerBldg);

		///////////////////////////////////////////
		//Felder durchgehen und anzeigen
		///////////////////////////////////////////
		$content.='<div style="display: flex;">';
		for($i=0;$i<count($this->fields);$i++){

			//maximal X in einer Zeile

			if($i % 4==0){
				$content.='</div>';
				$content.='<div style="display: flex;">';
			}

			$bldg_level=0;

			///////////////////////////////////////////
			//Rahmenfarbe definieren
			///////////////////////////////////////////
			/*
			$bordercolor='#FFFFFF';
			//Blocker
			if(isset($this->fields[$i][1])){
				$bordercolor='#FF0000';
			}
			*/

			///////////////////////////////////////////
			//Feld anzeigen
			///////////////////////////////////////////
			
			//$content.='<div style="height: 50px; width: 300px; border: 1px solid '.$bordercolor.'; margin-bottom: 10px; box-sizing: border-box; padding: 5px; cursor: pointer;" onclick="location.href=\'map_system.php?id='.$this->system_id.'&fieldid='.$i.'\'">';
			$content.='<div style="width: 40px; box-sizing: border-box; cursor: pointer; text-align: center;" onclick="location.href=\'map_system.php?id='.$this->system_id.'&fieldid='.$i.'\'">';

			///////////////////////////////////////////
			//Rahmenfarbe definieren
			///////////////////////////////////////////
			//Blocker
			if(isset($this->fields[$i][1])){
				$border='border: 1px solid #FF0000;';
			}else{
				$border='';
			}

			///////////////////////////////////////////
			//Feld-Ressource anzeigen
			///////////////////////////////////////////
			$stufeninfo ='<div id="build_level'.$i.'"></div>';
			$stufeninfo.='<div id="build_counter'.$i.'" style="font-size: 12px;"></div>';
			//testen ob es gerade im Bau ist, dann die Farbe ändern
			$factory_id=-1;
			$bldg_id=-1;
			for($b=0;$b<count($this->playerBldg);$b++){
				if($this->playerBldg[$b]['field_id']==$i){
					$factory_id=isset($GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['factory_id']) ? $GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['factory_id'] : -1;
					$bldg_id=$this->playerBldg[$b]['bldg_id'];
					$bldg_level=$this->playerBldg[$b]['bldg_level'];
					//wird das Gebäude gerade ausgebaut?
					if(time()<$this->playerBldg[$b]['bldg_time']){
						//Ausbau läuft
						//$content.=$GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['name'].' (Ausbau auf Stufe '.($this->playerBldg[$b]['bldg_level']).': <span id="build_counter'.$i.'"></span>)';
						//$content.='<script type="text/javascript">ang_countdown('.($this->playerBldg[$b]['bldg_time']-time()).',"build_counter'.$i.'",0)</script>';
						//$content.='<br>';
						$stufeninfo ='<div style="color: yellow;" id="build_level'.$i.'">'.$this->playerBldg[$b]['bldg_level'].'</div>';
						$stufeninfo.='<div id="build_counter'.$i.'" style="font-size: 12px;">&nbsp;</div>';
						$stufeninfo.='<script>ang_countdown('.($this->playerBldg[$b]['bldg_time']-time()).',"build_counter'.$i.'",0)</script>';
					}else{
						//wird nicht ausgebaut
						//$content.=$GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['name'].' (Stufe '.$this->playerBldg[$b]['bldg_level'].')<br>';
						$stufeninfo ='<div id="build_level'.$i.'">'.$this->playerBldg[$b]['bldg_level'].'</div>';
						$stufeninfo.='<div id="build_counter'.$i.'" style="font-size: 12px;"></div>';
					}
				}
			}

			/*
			$stufeninfo='<br>'.$bldg[$row['id']][$i]['bldg_level'];
			//testen ob es gerade im Bau ist, dann die Farbe ändern
			if($bldg[$row['id']][$i]['bldg_time']>time()){
				$stufeninfo='<span style="color: yellow;">'.$stufeninfo.'</span>';
			}
			*/

			if($i>0){
				if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
					//Gebäudestufe bestimmen
					//Grafik bestimmen
					$filename_nr=$this->fields[$i][0];
					if($filename_nr<10){
						$filename_nr='0'.$filename_nr;
					}
					$content.='
					<div style="text-align:center; font-size: 20px;">
						<img style="width: 40px; border-radius: 5px;'.$border.'" src="g/ele'.$filename_nr.'.gif" title="'.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'].'">
						'.$stufeninfo.
					'</div>';
				}else{
					//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
					if($factory_id>-1){

						$content.='
						<div style="font-size: 20px; text-align:center;">
							<div style="display: inline-block; margin-bottom: 4px; line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;" title="'.$GLOBALS['map_buildings'][$bldg_id]['name'].'">'.$GLOBALS['greek_chars'][$factory_id].'</div>
							'.$stufeninfo.'
						</div>';


					}else{
						$content.='
						<div style="font-size: 20px; text-align:center;">
							<div title="keine Rohstoffe" style="line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; border-radius: 5px;'.$border.'">-</div>
							'.$stufeninfo.'
						</div>
						';
					}

				}
			}else{

				//Außenposten
				$content.='
				<div style="font-size: 20px; text-align:center;">
					<div style="display: inline-block; margin-bottom: 3px; line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; box-sizing: border-box; border-radius: 5px;" title="Au&szlig;enposten">A</div>
					'.$stufeninfo.'
				</div>';


			}

			/*

			///////////////////////////////////////////
			//Blocker anzeigen
			///////////////////////////////////////////
			if(isset($this->fields[$i][1])){
				$content.='Feldblocker: '.$this->fields[$i][1][1].'x '.$GLOBALS['map_field_blocker'][$this->fields[$i][1][0]]['name'].'<br>';
			}

			///////////////////////////////////////////
			//Gebäude anzeigen
			///////////////////////////////////////////
			for($b=0;$b<count($this->playerBldg);$b++){
				if($this->playerBldg[$b]['field_id']==$i){
					//wird das Gebäude gerade ausgebaut?
					if(time()<$this->playerBldg[$b]['bldg_time']){
						//Ausbau läuft
						$content.=$GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['name'].' (Ausbau auf Stufe '.($this->playerBldg[$b]['bldg_level']).': <span id="build_counter'.$i.'"></span>)';
						$content.='<script type="text/javascript">ang_countdown('.($this->playerBldg[$b]['bldg_time']-time()).',"build_counter'.$i.'",0)</script>';
						$content.='<br>'; 
					}else{
						//wird nicht ausgebaut
						$content.=$GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['name'].' (Stufe '.$this->playerBldg[$b]['bldg_level'].')<br>';
					}
				}
			}
			

			///////////////////////////////////////////
			//Feld-Ressource anzeigen
			///////////////////////////////////////////
			if($i>0){
				if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
					$content.='Feldressource: '.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'].'<br>';
				}
			}
			*/

			$content.='</div>';
			
			//Upgrade-Pfeil
			if($bldg_level>0){
				$content.='
				<div style="width: 40px; height: 40px; box-sizing: border-box; cursor: pointer; text-align: center; margin-right: 20px;" title="upgrade">
					<form method="post">
						<input name="id" value="'.$this->system_id.'" type="hidden">
						<input name="fieldid" value="'.$i.'" type="hidden">
						<input name="upgrade" value="1" type="hidden">
						<img src="/g/icon12.png" style="width: 100%; height: 100%;" onclick="$(this).parents(\'form:first\').submit();">
					</form>			
				</div>
				';
			}else{
				$content.='<div style="width: 40px; height: 40px; box-sizing: border-box; cursor: pointer; text-align: center; margin-right: 20px;"></div>';

			}



		}

		$content.='</div>';

		$content.='
		<form method="post">
			<input name="id" value="'.$this->system_id.'" type="hidden">
			<input name="upgradeallbuildings" value="1" type="hidden">
	
			<div style="margin-top: 30px; margin-bottom: 20px; width: 100%; text-align: center;">
			<a id="upgrade_all" href="javascript: void(0);" onclick="$(this).parents(\'form:first\').submit();" style="background-color: #FFFFFF; color: #000000; text-decoration: none; text-align: center; border: 1px solid #888888; box-sizing: border-box; padding: 8px;" title="Hotkey: Leertaste">Alle Geb&auml;ude upgraden</a>
			</div>
		</form>';


		///////////////////////////////////////////
		//recht Spalte
		///////////////////////////////////////////
		$content.='</div><div style="flex-grow: 1; padding-left: 20px;">';

		if(isset($_REQUEST['fieldid'])){
			$fieldid=intval($_REQUEST['fieldid']);

			if(isset($this->fields[$fieldid][1])){
				$content.='Feldblocker: '.$this->fields[$fieldid][1][1].'x '.$GLOBALS['map_field_blocker'][$this->fields[$fieldid][1][0]]['name'].'<br>';
				$content.='Dieses Feld ist blockiert und mu&szlig; erst nutzbar gemacht werden. (das wird erst mit einem Folgeupdate m&ouml;glich sein)';
			}else{
				//wenn es ein Gebäude gibt, dann dieses anzeigen und die weiteren Möglichkeiten anbieten
				$bldg_exist=false;
				for($b=0;$b<count($this->playerBldg);$b++){
					if($this->playerBldg[$b]['field_id']==$fieldid){
						$bldg_exist=true;
						$bldg_index=$b;
					}
				}

				if($bldg_exist){
					$destroyed=false;
					$content.=$GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['name'];

					//Gebäudestufe Aktuell/Maximum
					//$content.='<br>Stufe: '.$this->playerBldg[$bldg_index]['bldg_level'].'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);

					//wird das Gebäude gerade ausgebaut?
					if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
						//Ausbau läuft
						$content.='<br>Ausbau auf Stufe '.($this->playerBldg[$bldg_index]['bldg_level']).'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);
						$akt_level=$this->playerBldg[$bldg_index]['bldg_level']-1;
					}else{
						//wird nicht ausgebaut
						$akt_level=$this->playerBldg[$bldg_index]['bldg_level'];
						$content.='<br>Stufe: '.$this->playerBldg[$bldg_index]['bldg_level'].'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);
					}

					//////////////////////////////
					//läuft ein Bau/Upgrade
					//////////////////////////////
					/*
					if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
						$content.='<br><br>Verbleibende Bauzeit: <span id="build_counter"></span>';
						$content.='<script type="text/javascript">ang_countdown('.($this->playerBldg[$bldg_index]['bldg_time']-time()).',"build_counter",0)</script>';
					}
					*/

					////////////////////////////////////////////////////////////
					//Gebäude abreißen, geht erst ab Feld 1, Feld 0 ist fix
					////////////////////////////////////////////////////////////
					//Abreißbefehl wurde erteilt
					/*
					if($_REQUEST['destroy']==1){
						$content.='<br><br>Der Abri&szlig; wurde durchgef&uuml;hrt.';
						$content.='<br><a href="?id='.$this->system_id.'&fieldid='.$fieldid.'">weiter</a>';
						$destroyed=true;
						removeBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid);

						$content.='
						<script>
						location.href=\'map_system.php?id='.$this->system_id.'\';
						</script>';						
					}

					//Abreißbefehl anbieten
					if(!$destroyed && $fieldid>0){
						$content.='<br><br><a href="?id='.$this->system_id.'&fieldid='.$fieldid.'&destroy=1" onclick="return confirm(\'Wirklich abrei&szlig;en? Es werden keine Rohstoffe erstattet.\')">abrei&szlig;en</a>';
					}

					*/

					//////////////////////////////
					//Gebäudeupgrade
					//////////////////////////////
					//if(!$destroyed && time()>=$this->playerBldg[$bldg_index]['bldg_time'] && $this->playerBldg[$bldg_index]['bldg_level'] < count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'])){
					if(!$destroyed && $this->playerBldg[$bldg_index]['bldg_level'] < count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'])){
						//Baukosten laden
						$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

							//check für Hauptgebäude auf dem richtigen Level
							if($fieldid>0){
								$mainbuilding_level=$this->playerBldg[0]['bldg_level'];

								/*
								if($this->playerBldg[0]['bldg_time'] > time()){
									$mainbuilding_level--;
								}
								*/

								if($mainbuilding_level > $this->playerBldg[$bldg_index]['bldg_level']){
									$mainbuilding_ok=true;
								}else{
									$mainbuilding_ok=false;
									$content.='<br><br>Voraussetzung:';
									$content.='<br>'.($GLOBALS['map_buildings'][$this->playerBldg[0]['bldg_id']]['name']).' Stufe '.($this->playerBldg[$bldg_index]['bldg_level']+1);
								}
							}else{
								$mainbuilding_ok=true;
							}						

						//man will es bauen und man hat alles
						if($baukosten['has_all'] && $_POST['upgrade']==1 && $mainbuilding_ok){
							//Bauauftrag in der DB hinterlegen, dazu unterscheiden zwischen laufendem Upgrader oder auch nicht
							if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
								//es läuft ein Upgrade
								$upgrade_time=$this->playerBldg[$bldg_index]['bldg_time']+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
								//die('AAAAAAAAA');
							}else{
								//es läuft kein Upgrade
								$upgrade_time=time()+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
								//die('BBBBBBBBB');
							}

							setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $this->playerBldg[$bldg_index]['bldg_id'], $this->playerBldg[$bldg_index]['bldg_level']+1, $upgrade_time);

							//Rohstoffe abziehen
							$this->doBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

							//Info an Spieler mit laufendem Bau
							//$content.='<br><br>Verbleibende Bauzeit: <span id="build_counter"></span>';
							//$content.='<script>ang_countdown('.(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*($this->playerBldg[$bldg_index]['bldg_level']+1))).',"build_counter",0)</script>';
							$content.='
							<script>
							location.href=\'map_system.php?id='.$this->system_id.'\';
							</script>';

							/*
							ang_countdown('.(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*($this->playerBldg[$bldg_index]['bldg_level']+1))).',"build_counter'.$fieldid.'",0);
							$("build_level'.$fieldid.'").html("'.($this->playerBldg[$bldg_index]['bldg_level']+1).'");
							$("build_level'.$fieldid.'").css("color","yellow");
							*/

						}else{
							//Bauzeit ausgeben
							$content.='<div><br>Bauzeit: '.round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)).' Sekunden</div>';
							//$content.='<br>A: '.$GLOBALS['duration_factor'];

							//Baukosten ausgeben
							$content.='<br>Baukosten:';
							$content.=$baukosten['kosten'];



							//wenn man die Rohstoffe hat, den upgrade-link anzeigen
							if($baukosten['has_all'] && $mainbuilding_ok){
								//$content.='<br><a href="?id='.$this->system_id.'&fieldid='.$fieldid.'&upgrade=1&fastbuild=2">upgraden</a>';
								$content.='<br>
								<form method="post">
									<input name="id" value="'.$this->system_id.'" type="hidden">
									<input name="fieldid" value="'.$fieldid.'" type="hidden">
									<input name="upgrade" value="1" type="hidden">
							
									<a href="javascript: void(0);" onclick="$(this).parents(\'form:first\').submit();">upgraden</a>
								</form>';
							}else{
								$content.='<div style="color: #FF0000;">Es sind nicht alle Voraussetzungen erf&uuml;llt.</div>';

							}
						}
					}

					//////////////////////////////
					//Produktionsmenge
					//////////////////////////////
					if(!$destroyed){
						if(!in_array($bldg_index, (array(0)))){
							//$content.=$bldg_index;
							//Poduktionsmenge anzeigen, wenn vorhnaden
							if(isset($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_amount'])){
								$content.='<br><br><div>Produktionsmenge:</div>';
								for($p=0;$p<count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);$p++){
									//die aktuelle Stufe hervorheben
									if($p+1 == $akt_level){
										$style='color: #00FF00;';
									}else{
										$style='';
									}
									//$produktionsmenge=(($p+1)*($p+1));
									
									$produktionsmenge=$GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_amount'][$p];
									$content.='<div style="'.$style.'">Stufe '.($p+1).': '.$produktionsmenge.'</div>';
								}
							}

							//Fertigungskapazität anzeigen, wenn vorhnaden
							if(isset($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_capacity'])){
								$content.='<br><br><div>Fertigungskapazit&auml;t:</div>';
								for($p=0;$p<count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);$p++){
									//die aktuelle Stufe hervorheben
									if($p+1 == $akt_level){
										$style='color: #00FF00;';
									}else{
										$style='';
									}
									//$produktionsmenge=(($p+1)*($p+1));
									
									$produktionsmenge=$GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_capacity'][$p];
									$content.='<div style="'.$style.'">Stufe '.($p+1).': '.$produktionsmenge.'</div>';
								}
							}

						}
					}

				}else{//wenn es kein Gebäude gibt, dann den Bau anbieten
					$content.='<div>Folgendes kann hier gebaut werden:</div><br>';

					//alle vorhandenen Gebäudetypen durchgehen und deren Voraussetzungen checken
					$ignore_bids=array(0,1,2);

					for($g=0;$g<count($GLOBALS['map_buildings']);$g++){
						//nicht alle IDS sind für den direkten Bau erlaubt
						if(!in_array($g, $ignore_bids)){

							//Welttyp checken
							if(in_array($this->system_typ, $GLOBALS['map_buildings'][$g]['bldg_in_type']) //Welttyp
							&& in_array($this->fields[$fieldid][0], $GLOBALS['map_buildings'][$g]['need_field_typ'])
							){
								if(!isset($_POST['build']) || $_POST['build']==$g){
									//Technologie anzeigen
									$content.='<div style="width: 100%; border: 1px solid #FFFFFF;padding: 5px; box-sizing: border-box; margin-bottom: 8px;">';
									//Textfarbe für Tech-Name, damit erkennbar ist, ob man sie erforscht hat
									if(hasTech($GLOBALS['pt'],$GLOBALS['map_buildings'][$g]['need_tech']) || !isset($GLOBALS['map_buildings'][$g]['need_tech'])){
										$tech_name_color='#FFFFFF';
										$has_tech=true;
									}else{
										$tech_name_color='#FF0000';
										$has_tech=false;
									}
									
									$content.='<div style="color: '.$tech_name_color.';">'.$GLOBALS['map_buildings'][$g]['name'].'</div>';

									//Baukosten laden
									$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$g]['bldg_cost'][0]);

									//man will es bauen und man hat alles
									if($baukosten['has_all'] && $has_tech && $_POST['build']==$g){
										//Bauauftrag in der DB hinterlegen
										setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $g, 1, time()+($GLOBALS['map_buildings'][$g]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']));

										//Rohstoffe abziehen
										$this->doBaukosten($GLOBALS['map_buildings'][$g]['bldg_cost'][0]);

										//Info an Spieler mit laufendem Bau
										//$content.='<br><br>Verbleibende Bauzeit: <span id="build_counter"></span>';
										//$content.='<script type="text/javascript">ang_countdown('.($GLOBALS['map_buildings'][$g]['bldg_time']*$GLOBALS['tech_build_time_faktor']).',"build_counter",0)</script>';
										$content.='
										<script>
										location.href=\'map_system.php?id='.$this->system_id.'\';
										</script>';										
									}else{

										if(isset($GLOBALS['map_buildings'][$g]['production_amount'])){
											//Produktionsmenge
											$content.='<br><div>Produktionsmenge:</div>';
											for($p=0;$p<count($GLOBALS['map_buildings'][$g]['bldg_cost']);$p++){
												//$produktionsmenge=(($p+1)*($p+1));
												$produktionsmenge=$GLOBALS['map_buildings'][$g]['production_amount'][$p];
												$content.='<div>Stufe '.($p+1).': '.$produktionsmenge.'</div>';
											}
										}

										if(isset($GLOBALS['map_buildings'][$g]['production_capacity'])){
											//Fertigungskapazität
											$content.='<br><div>Fertigungskapazit&auml;t:</div>';
											for($p=0;$p<count($GLOBALS['map_buildings'][$g]['bldg_cost']);$p++){
												//$produktionsmenge=(($p+1)*($p+1));
												$produktionsmenge=$GLOBALS['map_buildings'][$g]['production_capacity'][$p];
												$content.='<div>Stufe '.($p+1).': '.$produktionsmenge.'</div>';
											}
										}


										//Bauzeit ausgeben
										$content.='<div><br>Bauzeit: '.round($GLOBALS['map_buildings'][$g]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']).' Sekunden</div>';
										//$content.='<br>B: '.$GLOBALS['duration_factor'];

										//Baukosten ausgeben
										$content.='<br>Baukosten:';
										$content.=$baukosten['kosten'];

										//wenn man die Rohstoffe/Technologie hat, den bauen-link anzeigen
										if($baukosten['has_all'] && $has_tech){
											//$content.='<br><a href="?id='.$this->system_id.'&fieldid='.$fieldid.'&build='.$g.'&fastbuild=1">bauen</a>';
											$content.='<br>
											<form method="post">
												<input name="id" value="'.$this->system_id.'" type="hidden">
												<input name="fieldid" value="'.$fieldid.'" type="hidden">
												<input name="build" value="'.$g.'" type="hidden">
										
												<a href="javascript: void(0);" onclick="$(this).parents(\'form:first\').submit();">bauen</a>
											</form>';											
										}else{
											$content.='<div style="color: #FF0000;">Es sind nicht alle Voraussetzungen erf&uuml;llt.</div>';

										}

									}

									$content.='</div>';
								}
							}
						}
					}
				}
			}
		}else{
			$content.='W&auml;hle links ein Feld f&uuml;r weitere Informationen aus.';
		}

		//für alle Gebäude ein Upgradeauftrag starten
		if(isset($_POST['upgradeallbuildings']) && $_POST['upgradeallbuildings']==1){
			for($fieldid=0;$fieldid<count($this->fields);$fieldid++){
				if(isset($this->fields[$fieldid][1])){
					$content.='Feldblocker: '.$this->fields[$fieldid][1][1].'x '.$GLOBALS['map_field_blocker'][$this->fields[$fieldid][1][0]]['name'].'<br>';
					$content.='Dieses Feld ist blockiert und mu&szlig; erst nutzbar gemacht werden. (das wird erst mit einem Folgeupdate m&ouml;glich sein)';
				}else{
					//wenn es ein Gebäude gibt, dann dieses anzeigen und die weiteren Möglichkeiten anbieten
					$bldg_exist=false;
					for($b=0;$b<count($this->playerBldg);$b++){
						if($this->playerBldg[$b]['field_id']==$fieldid){
							$bldg_exist=true;
							$bldg_index=$b;
						}
					}

					if($bldg_exist){
						$destroyed=false;
						//$content.=$GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['name'];

						//Gebäudestufe Aktuell/Maximum
						//$content.='<br>Stufe: '.$this->playerBldg[$bldg_index]['bldg_level'].'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);

						//wird das Gebäude gerade ausgebaut?
						if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
							//Ausbau läuft
							//$content.='<br>Ausbau auf Stufe '.($this->playerBldg[$bldg_index]['bldg_level']).'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);
							$akt_level=$this->playerBldg[$bldg_index]['bldg_level']-1;
						}else{
							//wird nicht ausgebaut
							$akt_level=$this->playerBldg[$bldg_index]['bldg_level'];
							//$content.='<br>Stufe: '.$this->playerBldg[$bldg_index]['bldg_level'].'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']);
						}


						//////////////////////////////
						//Gebäudeupgrade
						//////////////////////////////
						//if(!$destroyed && time()>=$this->playerBldg[$bldg_index]['bldg_time'] && $this->playerBldg[$bldg_index]['bldg_level'] < count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'])){
						if(!$destroyed && $this->playerBldg[$bldg_index]['bldg_level'] < count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'])){
							//Baukosten laden
							$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

							//check für Hauptgebäude auf dem richtigen Level
							if($fieldid>0){
								$mainbuilding_level=$this->playerBldg[0]['bldg_level'];

								/*
								if($this->playerBldg[0]['bldg_time'] > time()){
									$mainbuilding_level--;
								}
								*/

								if($mainbuilding_level > $this->playerBldg[$bldg_index]['bldg_level']){
									$mainbuilding_ok=true;
								}else{
									$mainbuilding_ok=false;
									//$content.='<br><br>Voraussetzung:';
									//$content.='<br>'.($GLOBALS['map_buildings'][$this->playerBldg[0]['bldg_id']]['name']).' Stufe '.($this->playerBldg[$bldg_index]['bldg_level']+1);
								}
							}else{
								$mainbuilding_ok=true;
							}						

							//man will es bauen und man hat alles
							if($baukosten['has_all'] && $mainbuilding_ok){
								//Bauauftrag in der DB hinterlegen, dazu unterscheiden zwischen laufendem Upgrader oder auch nicht
								if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
									//es läuft ein Upgrade
									$upgrade_time=$this->playerBldg[$bldg_index]['bldg_time']+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
									//die('AAAAAAAAA');
								}else{
									//es läuft kein Upgrade
									$upgrade_time=time()+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
									//die('BBBBBBBBB');
								}
								
								setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $this->playerBldg[$bldg_index]['bldg_id'], $this->playerBldg[$bldg_index]['bldg_level']+1, $upgrade_time);

								//Rohstoffe abziehen
								$this->doBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

								//Gebäudelevel erhöhen, dass darauf aufbauende Gebäude darauf reagieren können
								$this->playerBldg[$bldg_index]['bldg_level']++;									

							}
						}
					}
				}

			}
			$content.='
			<script>
			location.href=\'map_system.php?id='.$this->system_id.'\';
			</script>';
		}


		$content.='</div>';//close rechte Spalte

		$content.='</div>';//close flex

		return $content;
	}

	public function showSpecialSystem($ps){
		$content='';
		include 'special_system_'.$this->special_system.'.inc.php';
		return $content;
	}

	public function showSystem($ps){
		include_once('lib/map_system_defs.inc.php');
		$content='';

		//Kopfzeile ausgeben
		//////////////////////////////////////////////////////////////
		$content.=rahmen_oben(generate_vsystem_kopfzeile($this->system_id, $this->getSystemName()),false);

		$content.='<div class="cell" style="width: 576px;">';

		//////////////////////////////////////////////////////////////
		//Test auf besonderes System
		//////////////////////////////////////////////////////////////
		if(isset($this->special_system) && $this->special_system>0){
			$content.=$this->showSpecialSystem($this->system_id,$ps);
		}else{

			/*
			$content.='<div style="width: 572px;">Systemtyp: ';
			$content.=$map_system_typen[$this->getSystemTyp()];
			if(isset($map_system_subtypen[$this->getSystemTyp()])){
				$content.=' ('.$map_system_subtypen[$this->getSystemTyp()][$this->getSystemSubTyp()].')';
			}
			$content.='</div>';
			*/

			//vorhandene Gebäude laden
			$this->playerBldg=loadPlayerBuildings($_SESSION['ums_user_id'], $this->system_id);
		

			$hasOutpost=false;

			//zwischen den System-Typen unterscheiden
			if(in_array($this->system_typ,array(1,4))){//bewohnbare Welt, Battleground
				//gibt es schon einen Außenposten?
				$hasOutpost=$this->checkForOutpost();

				//falls es keinen Außenposten gib, den Bau anbieten
				if(!$hasOutpost){
					$content.=$this->buildOutpost($this->system_id);
				}
			

			}else{
				$content.='in Vorbereitung';
			}

			//gibt es bereits einen Außenposten/Botschaft
			if($hasOutpost){
				if(in_array($this->system_typ,array(1))){//bewohnbare Welt
					//ggf. Felder anzeigen
					$content.=$this->showFields();

					//gibt es etwas zu looten?
					$content.=$this->showLoot();
				}

				if(in_array($this->system_typ,array(4))){//Battleground
					$content.='Durch den Weltraumhafen hast Du Zugriff auf dieses Battleground-System.<br>&Uuml;ber den Men&uuml;punkt "Basisstern" sind weitere Aktionen m&ouml;glich.';
				}

			}
		}
		


		$content.='</div>';//hintergrund
		
		$content.=rahmen_unten(false);

		return $content;
	}

	public function buildOutpost(){
		$content='';

		//Gebäude ID nach System-Typ bestimmen
		if($this->system_typ==0){
			//Botschaft
			$bldg_id=2;
			$tech_id=142;
		}elseif($this->system_typ==1){
			//planetare außenposten
			$bldg_id=1;
			$tech_id=144;
		}else{
			//weltraumhafen
			$bldg_id=0;
			$tech_id=142;
		}


		$content.='<br>Folgendes kann hier errichtet werden: '.$GLOBALS['map_buildings'][$bldg_id]['name'];

		//zuerst checken ob man die Technologie erforscht hat
		if(hasTech($GLOBALS['pt'],$tech_id)){

			//kosten
			$content.='<br>Daf&uuml;r benötigt wird:';
			$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'][0]);
			$content.=$baukosten['kosten'];
			$content.='<br>Flotten-Frachtkapazit&auml;t: '.$GLOBALS['map_buildings'][$bldg_id]['bldg_need_fk'];

			//dauer
			$content.='<br><br>Flotten-Missionsdauer: '.round($GLOBALS['map_buildings'][$bldg_id]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']).' Sekunden';

			//Flotten-Aktionen laden
			$fleet_data=getFleetData($_SESSION['ums_user_id']);

			//überprüfen ob evtl. schon eine Mission hierhin unterwegs ist
			$mission_active=false;
			for($f=1;$f<=3;$f++){
				$mission_data=unserialize($fleet_data[$f]['mission_data']);
				if((isset($mission_data['action_typ']) && $mission_data['action_typ']==1) && (isset($mission_data['system_id']) && $mission_data['system_id']==$this->system_id)){
					$mission_active=true;
					$mission_time=$fleet_data[$f]['mission_time']-time();
				}
			}

			if(!$mission_active){

				if($baukosten['has_all']){

					//Flotten-Frachtkapazität laden
					$fleet_fk=getFleetFK($_SESSION['ums_user_id']);
					
					//Flotten durchgehen
					for($f=1;$f<=3;$f++){
						$content.='<br>Flotte '.$f.': ';
						//geht nur wenn aktion=0 ist, sonst hat die Flotte schon einen Auftrag
						if($fleet_data[$f]['aktion']==0){
							//geht nur, wenn genug Frachkapazität vorhanden ist
							if($fleet_fk[$f]>=$GLOBALS['map_buildings'][$bldg_id]['bldg_need_fk']){
								if(isset($_REQUEST['action']) && $_REQUEST['action']=='createoutpost' && isset($_REQUEST['fleet_id']) && $_REQUEST['fleet_id']==$f){
									//Rohstoffe abziehen
									$this->doBaukosten($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'][0]);

									//Gebäude hinterlegen
									setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, 0, $bldg_id, 1, time()+round($GLOBALS['map_buildings'][$bldg_id]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']));

									//Flotte updaten
									$time=round(time()+$GLOBALS['map_buildings'][$bldg_id]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']);
									unset($mission_data);
									$mission_data['action_typ']=1;
									$mission_data['system_id']=$this->system_id;
									startFleetMission($_SESSION['ums_user_id'].'-'.$f, $time, $mission_data);

									$content.='die Mission wurde gestartet <a href="?id='.$this->system_id.'">weiter</a>';
								}else{
									$content.='<a href="?id='.$this->system_id.'&action=createoutpost&fleet_id='.$f.'">Mission zur Errichtung eines Au&szlig;enpostens starten</a>';
								}

							}else{
								$content.='die Frachtkapazit&auml;t ist zu gering ('.$fleet_fk[$f].'/'.$GLOBALS['map_buildings'][$bldg_id]['bldg_need_fk'].')';
							}
						}else{
							$content.='hat bereits einen Auftrag';
						}
					}
					//auf freie flotte mit frachttkapazität checken

				}else{
					//Info bzgl. fehlender Rohstoffe
					$content.='<div style="color: red;">Es sind nicht alle ben&ouml;tigten Rohstoffe vorhanden.</div>';
				
				}

				$content.='<br><br><div style="color: red;">ACHTUNG: Missionen k&ouml;nnen nicht abgebrochen werden.</div>';

				//Die Felder anzeigen
				$content.='<br><div style="display: flex;">';
				for($i=0;$i<count($this->fields);$i++){
					
					///////////////////////////////////////////
					//Rahmenfarbe definieren
					///////////////////////////////////////////
					if(isset($this->fields[$i][1])){
						$border='border: 1px solid #FF0000;';
					}else{
						$border='';
					}
	
					///////////////////////////////////////////
					//Feld-Ressource anzeigen
					///////////////////////////////////////////
					if($i>0){
	
						if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
							//Gebäudestufe bestimmen
							$stufeninfo='';

							if(isset($row['id']) && $bldg[$row['id']][$i]>0){
								$stufeninfo='<br>'.$bldg[$row['id']][$i];
							}

	
							//Grafik bestimmen
							$filename_nr=$this->fields[$i][0];
							if($filename_nr<10){
								$filename_nr='0'.$filename_nr;
							}
							$content.='<div style="text-align:center; padding-left: 10px; font-weight: bold; font-size: 20px;"><img style="width: 40px; border-radius: 5px;'.$border.'" src="g/ele'.$filename_nr.'.gif" title="'.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'].'">'.$stufeninfo.'</div>';
						}else{
							$content.='<div title="keine Rohstoffe" style="margin-left: 10px; line-height: 40px; width: 40px; height: 40px; background-color: #666666; text-align: center; border-radius: 5px;'.$border.'">-</div>';
						}
					}
	
				}

				$content.='</div>';

			}else{
				//die Mission läuft schon, daher Restzeit angeben
				$content.='<br><br><div style="color: green;">Die Mission l&auml;uft bereits.</div>';
				$content.='<br><br>Verbleibende Zeit: <span id="explore_counter"></span>';
				$content.='<script type="text/javascript">ang_countdown('.$mission_time.',"explore_counter",0)</script>';
			}

		}else{
			//Info bzgl. fehlender Technologie
			$content.='<div style="color: red;">Die Technologie wurde noch nicht erforscht.</div>';

		}


		return $content;
	}

	public function checkForOutpost(){
		//Gebäude auf Platz 0 überprüfen, das ist immer der Außenposten

		$fd=getBldgByFieldID($this->playerBldg, 0);
		//Test auf Außenposten
		if($fd['bldg_id']==-1){
			//Außenposten noch nicht vorhanden
			return false;
		}else{
			//Außenposten vorhanden
			return true;
		}
	}

	public function doBaukosten($baukosten){
		$ps=$GLOBALS['ps'];
		$pd=$GLOBALS['pd'];

		$need_storage_res=array();

		$has_all=true;
		
		$einzelkosten=explode(';', $baukosten);

		//test auf ausreichende Rohstoffe
		//print_r($einzelkosten);
		$ben_restyp01=0;
		$ben_restyp02=0;
		$ben_restyp03=0;
		$ben_restyp04=0;
		$ben_restyp05=0;
		foreach ($einzelkosten as $value) {
			$parts=explode("x", $value);

			//5 Grundrohstoffe
			if($value[0]=='R'){
				if($value[1]==1){
					if($pd['restyp01']<$parts[1]){$has_all=false;}
					$ben_restyp01=$parts[1];
				}elseif($value[1]==2){
					if($pd['restyp02']<$parts[1]){$has_all=false;}
					$ben_restyp02=$parts[1];
				}elseif($value[1]==3){
					if($pd['restyp03']<$parts[1]){$has_all=false;}
					$ben_restyp03=$parts[1];
				}elseif($value[1]==4){
					if($pd['restyp04']<$parts[1]){$has_all=false;}
					$ben_restyp04=$parts[1];
				}elseif($value[1]==5){
					if($pd['restyp05']<$parts[1]){$has_all=false;}
					$ben_restyp05=$parts[1];
				}
			}
			//Storage-Res
			elseif($value[0]=='I'){
				//genug im storage vorhanden?
				$value1=str_replace('I','',$parts[0]);
				if($ps[$value1]['item_amount']<$parts[1]){$has_all=false;}
				//speichern wie viel man aus dem storage benötigt
				$need_storage_res[$value1]=$parts[1];
			}
		}

		//test auf benötigte Technologien

		if($has_all){
			//Rohstoff-Kosten abziehen
			$sql="UPDATE de_user_data SET 
				restyp01=restyp01-'".$ben_restyp01."',
				restyp02=restyp02-'".$ben_restyp02."',
				restyp03=restyp03-'".$ben_restyp03."',
				restyp04=restyp04-'".$ben_restyp04."',
				restyp05=restyp05-'".$ben_restyp05."'
				WHERE user_id='".$_SESSION['ums_user_id']."';";

			//echo $sql;
			mysqli_query($GLOBALS['dbi'], $sql);
			
			//Item-Kosten abziehen
			foreach ($need_storage_res as $key => $value){
				change_storage_amount($_SESSION['ums_user_id'], $key, $value*-1);
			}

		}

	}

	public function formatBaukosten($baukosten){
		//$ps=$GLOBALS['ps'];
		//$pd=$GLOBALS['pd'];

		//Daten neu auslesen, ob man auch wirklich alles hat
		$ps=loadPlayerStorage($_SESSION['ums_user_id']);
		$pd=loadPlayerData($_SESSION['ums_user_id']);

		$has_all=true;
		$kosten='';

		$einzelkosten=explode(';', $baukosten);
		foreach ($einzelkosten as $value) {
			$parts=explode("x", $value);

			$kosten.='<br>';

			//5 Grundrohstoffe
			if($value[0]=='R'){
				if($value[1]==1){
					if($pd['restyp01']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' M';
					if($pd['restyp01']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==2){
					if($pd['restyp02']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' D';
					if($pd['restyp02']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==3){
					if($pd['restyp03']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' I';
					if($pd['restyp03']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==4){
					if($pd['restyp04']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' E';
					if($pd['restyp04']<$parts[1]){$kosten.='</span>';}
				}elseif($value[1]==5){
					if($pd['restyp05']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>';$has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' T';
					if($pd['restyp05']<$parts[1]){$kosten.='</span>';}
				}
			}elseif($value[0]=='I'){
				//if($value[1]==1){
					$value1=str_replace('I','',$parts[0]);
					if($ps[$value1]['item_amount']<$parts[1]){$kosten.='<span style=\'color: #AA0000;\'>'; $has_all=false;}
					$kosten.=number_format($parts[1],0,",",".");
					$kosten.=' '.$ps[$value1]['item_name'].' (Lager: '.number_format($ps[$value1]['item_amount'],0,",",".").')';
					if($ps[$value1]['item_amount']<$parts[1]){$kosten.='</span>';}
				//}
			}
		}


				
		return array('kosten' => $kosten, 'has_all' => $has_all);
	}

	public function showLoot(){
		include 'inc/userartefact.inc.php';

		$content='';

		//alle geborgenen Items aus der DB holen
		$looted=getUserLootByMapID($_SESSION['ums_user_id'],$this->system_id);

		//echo 'A: ';
		//print_r($looted);

		//Loot ist in in Feldern hinterlegt
		$fields=$this->fields;


		//Loot
		//Typ 1: Loot aus de_item_data
		//Subtypen siehe DB
		//Typ 2: Tronic
		//Typ 3: Spielerartefakt
		//Typ 4: Credits

		/*
		$fields[$i][2][0]=$loot_typ;//Typ
		$fields[$i][2][1]=$loot_subtyp;//Subtyp
		$fields[$i][2][2]=$loot_amount;//Menge
		$fields[$i][2][3]=$loot_level;//in Level
		*/

		//alle Felder durchgehen
		for($i=0;$i<20;$i++){
			if(isset($fields[$i][2][0])){
				$typ=$fields[$i][2][0];
				$subtyp=$fields[$i][2][1];
				$amount=$fields[$i][2][2];
				$inlevel=$fields[$i][2][3];

				//checken ob der Gebäudelevel hoch genug ist um es zu sehen
				$bldg_level=0;
				for($b=0;$b<count($this->playerBldg);$b++){
					if($this->playerBldg[$b]['field_id']==$i){
						$bldg_level=$this->playerBldg[$b]['bldg_level'];
					}
				}

				//wenn die Allianz ein Fundbüro hat, dann hat man es evtl. schon gefunden
				$ally_know_it=false;
				//falls das eigene Gebäude vom Level her zu niedrig ist, ist evtl. das Gebäude von jemandem in der Allianz groß genug
				if($amount>0 && $inlevel>$bldg_level){
					if($GLOBALS['allyid']>0 && $GLOBALS['ally_fundbuero_level']>=$inlevel){
						$sql="SELECT de_user_data.user_id FROM de_user_data LEFT JOIN de_user_map_loot ON (de_user_data.user_id=de_user_map_loot.user_id) WHERE de_user_data.allytag='".$GLOBALS['pd']['allytag']."' and de_user_data.status=1 AND de_user_map_loot.map_id=".$this->system_id." AND de_user_map_loot.field_id=".$i;
						//echo $sql;
						$db_data=mysqli_query($GLOBALS['dbi'], $sql);
						$num = mysqli_num_rows($db_data);
						//echo 'A: '.$num;
						if($num>0){
							$ally_know_it=true;
						}
					}
				}

				//$content.='<br>A: '.$inlevel.'/'.$bldg_level;

				if($inlevel<=$bldg_level || $ally_know_it){
					$content.='<div>Feld '.$i.': ';

					switch($typ){
						
						case 1:
							$content.=$amount.'x '.$GLOBALS['ps'][$subtyp]['item_name'];
							$loot_msg=$amount.'x '.$GLOBALS['ps'][$subtyp]['item_name'];
						break;
		
						case 2:
							$content.=$amount.'x Tronic';
							$loot_msg=$amount.'x Tronic';
						break;
							
						case 3:
							$content.=$amount.'x '.$ua_name[$subtyp].'-Artefakt';
							$loot_msg=$amount.'x '.$ua_name[$subtyp].'-Artefakt';
							//echo $subtyp;
						break;
							
						case 4:
							$content.=$amount.'x Credit';
							$loot_msg=$amount.'x Credit';
						break;

						default;
							$content.='ERROR A28';
						break;
					}

					$content.=' auf Level '.$inlevel;

					//wurde es schon geborgen?
					if(in_array($i,$looted)){
						//ja, also Info ausgeben
						$content.=' (bereits geborgen)';
					}else{
						//möchte man es bergen?
						$geborgen=false;
						if(isset($_REQUEST['collectid']) && $_REQUEST['collectid']==$i && $inlevel<=$bldg_level){
							//die Sachen in der DB hinterlegen
							switch($typ){
						
								case 1://Itemdata
									change_storage_amount($_SESSION['ums_user_id'], $subtyp, $amount);
									$geborgen=true;
								break;
				
								case 2: //Tronic
									$sql="UPDATE de_user_data SET restyp05=restyp05+'".$amount."' WHERE user_id='".$_SESSION['ums_user_id']."';";
									//echo $sql;
									mysqli_query($GLOBALS['dbi'], $sql);
									$geborgen=true;
								break;
									
								case 3://Spielerartefakt
									if(get_free_artefact_places($_SESSION['ums_user_id'])>0){
										mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_artefact (user_id, id, level) VALUES ('".$_SESSION['ums_user_id']."', '".($subtyp+1)."', '1')");
										$geborgen=true;
									}else{
										$content.='<span style="color: #FF0000;"> (im Artefaktgeb&auml;ude ist kein freier Platz) </span>';
									}
								break;
									
								case 4://Credits
									changeCredits($_SESSION['ums_user_id'], $amount, 'VS Loot System '.$this->system_id.' -  field_id: '.$i);
									$geborgen=true;
								break;
							}

							if($geborgen){
								//Flag setzen, dass man es geborgen hat
								setUserLoot($_SESSION['ums_user_id'], $this->system_id, $i);

								//temporär msg an chat
								/*
								$text='<font color="#9f2ebd">'.$_SESSION['ums_spielername'].' lootet '.$loot_msg.'</font>';
								$channel=0;$channeltyp=2;$spielername='[SYSTEM]'; $chat_message=$text;
								insert_chat_msg($channel, $channeltyp, $spielername, $chat_message);	
								*/
							}
						}

						//noch nicht geborgen, also Link anzeigen
						if(!$geborgen && $inlevel<=$bldg_level){
							$content.=' <a href="?id='.$this->system_id.'&collectid='.$i.'">bergen</a>';
						}else{
							if($ally_know_it){
								$content.=' <span style="color: #FF0000;">(Bergung noch nicht m&ouml;glich)</span>';
							}else{
								$content.=' (bereits geborgen)';
							}
						}

					}

					$content.='</div>';

				}

			}
		}

		if(!empty($content)){
			$content='<br><div style="font-weight: bold;">Fundst&uuml;cke:</div>'.$content;

		}

		return $content;
	}
}
?>