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

	/**
	 * Dauer eines Bauauftrags in Echtzeit, z. B. "45 Sekunden" oder "12 Minuten".
	 */
	private function msDauer($sekunden){
		$sekunden=(int)$sekunden;
		if($sekunden<120){
			return $sekunden.' Sekunden';
		}
		if($sekunden<7200){
			return round($sekunden/60).' Minuten';
		}
		return floor($sekunden/3600).' Std. '.round(($sekunden%3600)/60).' Min.';
	}

	/**
	 * Werte je Stufe (Produktionsmenge, Fertigungskapazität) als kleines Raster, die aktuelle Stufe hervorgehoben.
	 */
	private function msStufenliste($titel, $werte, $anzahl, $aktuell){
		$html='<div class="ms-abschnitt"><span class="mod-typ">'.$titel.'</span><div class="ms-stufen">';
		for($p=0;$p<$anzahl;$p++){
			$html.='<span'.($p+1 == $aktuell ? ' class="ms-stufe-aktiv"' : '').'><small>'.($p+1).'</small>'.$werte[$p].'</span>';
		}
		return $html.'</div></div>';
	}

	public function showFields(){
		//für alle Gebäude ein Upgradeauftrag starten
		if(isset($_POST['upgradeallbuildings']) && $_POST['upgradeallbuildings']==1){
			$this->upgradeAllBuildings();
			vs_redirect($this->system_id, $_REQUEST['fieldid'] ?? null);
		}

		//gewähltes Feld nur zum Hervorheben, geprüft wird es unten in der rechten Spalte
		$feld_gewaehlt=isset($_REQUEST['fieldid']) ? intval($_REQUEST['fieldid']) : -1;

		$content='<div class="ms-system">';

		//linke Spalte: die Felder, je mit Upgrade-Pfeil
		$content.='<div class="ms-links"><div class="ms-felder">';

		///////////////////////////////////////////
		//Felder durchgehen und anzeigen
		///////////////////////////////////////////
		for($i=0;$i<count($this->fields);$i++){
			$bldg_level=0;

			//Blocker
			$blockiert=isset($this->fields[$i][1]);

			///////////////////////////////////////////
			//Gebäude auf dem Feld: Stufe als Abzeichen, im Ausbau gelb mit der Uhrzeit, zu der er fertig ist
			///////////////////////////////////////////
			$stufeninfo='';
			$fertig='';
			$titel_stufe='';
			$factory_id=-1;
			$bldg_id=-1;
			for($b=0;$b<count($this->playerBldg);$b++){
				if($this->playerBldg[$b]['field_id']==$i){
					$factory_id=isset($GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['factory_id']) ? $GLOBALS['map_buildings'][$this->playerBldg[$b]['bldg_id']]['factory_id'] : -1;
					$bldg_id=$this->playerBldg[$b]['bldg_id'];
					$bldg_level=$this->playerBldg[$b]['bldg_level'];
					//wird das Gebäude gerade ausgebaut?
					if(time()<$this->playerBldg[$b]['bldg_time']){
						$bis=$this->playerBldg[$b]['bldg_time'];
						$stufeninfo='<span class="vs-stufe vs-stufe-bau" id="build_level'.$i.'">'.$this->playerBldg[$b]['bldg_level'].'</span>';
						$fertig='<span class="ms-fertig" id="build_counter'.$i.'">'.date(date('Y-m-d', $bis)==date('Y-m-d') ? 'H:i' : 'd.m.', $bis).'</span>';
						$titel_stufe='&Ausbau auf Stufe '.$this->playerBldg[$b]['bldg_level'].' '.\DieEwigen\DE2\View\RealTime::until($bis);
					}else{
						$stufeninfo='<span class="vs-stufe" id="build_level'.$i.'">'.$this->playerBldg[$b]['bldg_level'].'</span>';
						$titel_stufe='&Stufe '.$this->playerBldg[$b]['bldg_level'];
					}
				}
			}

			///////////////////////////////////////////
			//Feld-Ressource anzeigen
			///////////////////////////////////////////
			if($i>0){
				if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
					//Grafik bestimmen
					$filename_nr=$this->fields[$i][0];
					if($filename_nr<10){
						$filename_nr='0'.$filename_nr;
					}
					$titel=$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'];
					$inhalt='<img src="gp/g/ele'.$filename_nr.'.gif" alt="">';
					$klasse='vs-feld'.($blockiert ? ' vs-feld-blockiert' : '');
				}else{
					//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
					if($factory_id>-1){
						$titel=$GLOBALS['map_buildings'][$bldg_id]['name'];
						$inhalt=$GLOBALS['greek_chars'][$factory_id];
						$klasse='vs-feld vs-feld-box';
					}else{
						$titel='keine Rohstoffe';
						$inhalt='&ndash;';
						$klasse='vs-feld vs-feld-leer'.($blockiert ? ' vs-feld-blockiert' : '');
					}
				}
			}else{
				//Außenposten
				$titel='Au&szlig;enposten';
				$inhalt='A';
				$klasse='vs-feld vs-feld-box';
			}

			$content.='<div class="ms-feld'.($feld_gewaehlt==$i ? ' ms-feld-aktiv' : '').'">';
			$content.='<a href="map_system.php?id='.$this->system_id.'&amp;fieldid='.$i.'" class="'.$klasse.'" title="'.$titel.$titel_stufe.'">'.$inhalt.$stufeninfo.'</a>';

			//Upgrade-Pfeil
			if($bldg_level>0){
				$content.='
				<form method="post" class="ms-up-form">
					<input name="id" value="'.$this->system_id.'" type="hidden">
					<input name="fieldid" value="'.$i.'" type="hidden">
					<input name="upgrade" value="1" type="hidden">
					<button type="submit" class="ms-up" title="Upgrade&'.$titel.' eine Stufe ausbauen"><img src="gp/g/icon12.png" alt="Upgrade"></button>
				</form>';
			}
			$content.=$fertig;
			$content.='</div>';
		}

		$content.='</div>';

		$content.='
		<form method="post" class="ms-alle">
			<input name="id" value="'.$this->system_id.'" type="hidden">
			<input name="upgradeallbuildings" value="1" type="hidden">
			<button type="submit" id="upgrade_all" class="mod-btn" title="Alle Geb&auml;ude upgraden&Hotkey: Leertaste">Alle Geb&auml;ude upgraden</button>
		</form>';

		///////////////////////////////////////////
		//rechte Spalte
		///////////////////////////////////////////
		$content.='</div><div class="ms-detail">';

		//nur Felder, die es in diesem System gibt: sonst ließen sich auf erfundenen Feld-IDs beliebig viele Gebäude bauen
		if(isset($_REQUEST['fieldid']) && isset($this->fields[intval($_REQUEST['fieldid'])])){
			$fieldid=intval($_REQUEST['fieldid']);

			if(isset($this->fields[$fieldid][1])){
				$content.='<div class="ms-titel">Feld '.$fieldid.'</div>';
				$content.='<div class="mod-meldung mod-meldung-warn">Feldblocker: '.$this->fields[$fieldid][1][1].'x '.$GLOBALS['map_field_blocker'][$this->fields[$fieldid][1][0]]['name'].'<br>';
				$content.='Dieses Feld ist blockiert und mu&szlig; erst nutzbar gemacht werden. (das wird erst mit einem Folgeupdate m&ouml;glich sein)</div>';
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
					$content.='<div class="ms-titel">'.$GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['name'].'</div>';

					//wird das Gebäude gerade ausgebaut?
					if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
						//Ausbau läuft
						$content.='<div class="ms-zeile"><span class="mod-chip mod-chip-warn">Ausbau auf Stufe '.($this->playerBldg[$bldg_index]['bldg_level']).'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']).'</span>';
						$content.=' <span class="bk-leise">'.\DieEwigen\DE2\View\RealTime::until($this->playerBldg[$bldg_index]['bldg_time']).'</span></div>';
						$akt_level=$this->playerBldg[$bldg_index]['bldg_level']-1;
					}else{
						//wird nicht ausgebaut
						$akt_level=$this->playerBldg[$bldg_index]['bldg_level'];
						$content.='<div class="ms-zeile"><span class="mod-chip">Stufe '.$this->playerBldg[$bldg_index]['bldg_level'].'/'.count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']).'</span></div>';
					}

					//////////////////////////////
					//Gebäudeupgrade
					//////////////////////////////
					if(!$destroyed && $this->playerBldg[$bldg_index]['bldg_level'] < count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'])){
						//Baukosten laden
						$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

							//check für Hauptgebäude auf dem richtigen Level
							if($fieldid>0){
								$mainbuilding_level=$this->playerBldg[0]['bldg_level'];

								if($mainbuilding_level > $this->playerBldg[$bldg_index]['bldg_level']){
									$mainbuilding_ok=true;
								}else{
									$mainbuilding_ok=false;
									$content.='<div class="mod-hinweis ms-voraussetzung">Voraussetzung: '.($GLOBALS['map_buildings'][$this->playerBldg[0]['bldg_id']]['name']).' Stufe '.($this->playerBldg[$bldg_index]['bldg_level']+1).'</div>';
								}
							}else{
								$mainbuilding_ok=true;
							}

						//man will es bauen und man hat alles (upgrade angefordert)
						if($baukosten['has_all'] && isset($_POST['upgrade']) && $_POST['upgrade']==1 && $mainbuilding_ok){
							//Bauauftrag in der DB hinterlegen, dazu unterscheiden zwischen laufendem Upgrader oder auch nicht
							if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
								//es läuft ein Upgrade
								$upgrade_time=$this->playerBldg[$bldg_index]['bldg_time']+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
							}else{
								//es läuft kein Upgrade
								$upgrade_time=time()+(round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1)));
							}

							setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $this->playerBldg[$bldg_index]['bldg_id'], $this->playerBldg[$bldg_index]['bldg_level']+1, $upgrade_time);

							//Rohstoffe abziehen
							$this->doBaukosten($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost'][$this->playerBldg[$bldg_index]['bldg_level']]);

							//neu laden, das Feld bleibt ausgewählt
							vs_redirect($this->system_id, $fieldid);

						}else{
							//Bauzeit ausgeben; ein neuer Ausbau beginnt nach einem laufenden
							$dauer=round($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($this->playerBldg[$bldg_index]['bldg_level']+1));
							$content.='<div class="ms-abschnitt"><span class="mod-typ">N&auml;chste Stufe</span>';
							$content.='<div class="ms-zeile">Bauzeit: <b>'.$this->msDauer($dauer).'</b> <span class="bk-leise">&middot; fertig '.\DieEwigen\DE2\View\RealTime::at(max(time(), $this->playerBldg[$bldg_index]['bldg_time'])+$dauer).'</span></div>';

							//Baukosten ausgeben
							$content.='<div class="ms-kosten">'.$baukosten['kosten'].'</div>';

							//wenn man die Rohstoffe hat, den upgrade-link anzeigen
							if($baukosten['has_all'] && $mainbuilding_ok){
								$content.='
								<form method="post" class="ms-aktion">
									<input name="id" value="'.$this->system_id.'" type="hidden">
									<input name="fieldid" value="'.$fieldid.'" type="hidden">
									<input name="upgrade" value="1" type="hidden">
									<button type="submit" class="mod-btn">Upgraden</button>
								</form>';
							}else{
								$content.='<div class="ms-fehlt">Es sind nicht alle Voraussetzungen erf&uuml;llt.</div>';
							}
							$content.='</div>';
						}
					}

					//////////////////////////////
					//Produktionsmenge
					//////////////////////////////
					if(!$destroyed){
						if(!in_array($bldg_index, (array(0)))){
							//Poduktionsmenge anzeigen, wenn vorhnaden
							if(isset($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_amount'])){
								$content.=$this->msStufenliste('Produktionsmenge', $GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_amount'], count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']), $akt_level);
							}

							//Fertigungskapazität anzeigen, wenn vorhnaden
							if(isset($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_capacity'])){
								$content.=$this->msStufenliste('Fertigungskapazit&auml;t', $GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['production_capacity'], count($GLOBALS['map_buildings'][$this->playerBldg[$bldg_index]['bldg_id']]['bldg_cost']), $akt_level);
							}

						}
					}

				}else{//wenn es kein Gebäude gibt, dann den Bau anbieten
					$feldname=$GLOBALS['map_field_typ'][$this->fields[$fieldid][0]]['name'];
					$content.='<div class="ms-titel">Feld '.$fieldid.' &middot; '.($feldname!='-' ? $feldname : 'keine Rohstoffe').'</div>';
					$content.='<div class="ms-zeile bk-leise">Folgendes kann hier gebaut werden:</div>';

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
									$content.='<div class="ms-bau">';
									//Name rot, wenn die Technologie fehlt
									if(!isset($GLOBALS['map_buildings'][$g]['need_tech']) || hasTech($GLOBALS['pt'], $GLOBALS['map_buildings'][$g]['need_tech'])){
										$has_tech=true;
									}else{
										$has_tech=false;
									}

									$content.='<div class="ms-bau-name'.($has_tech ? '' : ' ms-fehlt').'">'.$GLOBALS['map_buildings'][$g]['name'].($has_tech ? '' : ' <small>(Technologie fehlt)</small>').'</div>';

									//Baukosten laden
									$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$g]['bldg_cost'][0]);

											//man will es bauen und man hat alles
									if($baukosten['has_all'] && $has_tech && isset($_POST['build']) && $_POST['build']==$g){
										//Bauauftrag in der DB hinterlegen
										setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $g, 1, time()+($GLOBALS['map_buildings'][$g]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']));

										//Rohstoffe abziehen
										$this->doBaukosten($GLOBALS['map_buildings'][$g]['bldg_cost'][0]);

										//neu laden, das Feld bleibt ausgewählt
										vs_redirect($this->system_id, $fieldid);
									}else{

										if(isset($GLOBALS['map_buildings'][$g]['production_amount'])){
											//Produktionsmenge
											$content.=$this->msStufenliste('Produktionsmenge', $GLOBALS['map_buildings'][$g]['production_amount'], count($GLOBALS['map_buildings'][$g]['bldg_cost']), 0);
										}

										if(isset($GLOBALS['map_buildings'][$g]['production_capacity'])){
											//Fertigungskapazität
											$content.=$this->msStufenliste('Fertigungskapazit&auml;t', $GLOBALS['map_buildings'][$g]['production_capacity'], count($GLOBALS['map_buildings'][$g]['bldg_cost']), 0);
										}

										//Bauzeit ausgeben
										$dauer=round($GLOBALS['map_buildings'][$g]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']);
										$content.='<div class="ms-zeile">Bauzeit: <b>'.$this->msDauer($dauer).'</b> <span class="bk-leise">&middot; fertig '.\DieEwigen\DE2\View\RealTime::at(time()+$dauer).'</span></div>';

										//Baukosten ausgeben
										$content.='<div class="ms-kosten">'.$baukosten['kosten'].'</div>';

										//wenn man die Rohstoffe/Technologie hat, den bauen-link anzeigen
										if($baukosten['has_all'] && $has_tech){
											$content.='
											<form method="post" class="ms-aktion">
												<input name="id" value="'.$this->system_id.'" type="hidden">
												<input name="fieldid" value="'.$fieldid.'" type="hidden">
												<input name="build" value="'.$g.'" type="hidden">
												<button type="submit" class="mod-btn">Bauen</button>
											</form>';
										}else{
											$content.='<div class="ms-fehlt">Es sind nicht alle Voraussetzungen erf&uuml;llt.</div>';

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
			$content.='<div class="mod-leer ms-leer">W&auml;hle links ein Feld f&uuml;r weitere Informationen aus.</div>';
		}


		$content.='</div>';//close rechte Spalte

		$content.='</div>';//close ms-system

		return $content;
	}

	/**
	 * Startet für alle Gebäude des Systems ein Upgrade, sofern Rohstoffe und Hauptgebäude-Stufe es erlauben.
	 * Erwartet $this->playerBldg sowie $GLOBALS['ps'], $GLOBALS['pd'] und $GLOBALS['duration_factor'].
	 * Gibt die Anzahl der gestarteten Upgrades zurück.
	 */
	public function upgradeAllBuildings(){
		$started=0;

		for($fieldid=0;$fieldid<count($this->fields);$fieldid++){
			//blockierte Felder überspringen
			if(isset($this->fields[$fieldid][1])){
				continue;
			}

			$bldg_exist=false;
			for($b=0;$b<count($this->playerBldg);$b++){
				if($this->playerBldg[$b]['field_id']==$fieldid){
					$bldg_exist=true;
					$bldg_index=$b;
				}
			}

			if(!$bldg_exist){
				continue;
			}

			$bldg_id=$this->playerBldg[$bldg_index]['bldg_id'];
			$bldg_level=$this->playerBldg[$bldg_index]['bldg_level'];

			//maximale Stufe erreicht?
			if($bldg_level >= count($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'])){
				continue;
			}

			//Baukosten laden
			$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'][$bldg_level]);

			//check für Hauptgebäude auf dem richtigen Level
			if($fieldid>0){
				$mainbuilding_ok=$this->playerBldg[0]['bldg_level'] > $bldg_level;
			}else{
				$mainbuilding_ok=true;
			}

			if($baukosten['has_all'] && $mainbuilding_ok){
				$dauer=round($GLOBALS['map_buildings'][$bldg_id]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']*($bldg_level+1));

				//Bauauftrag in der DB hinterlegen, dazu unterscheiden zwischen laufendem Upgrade oder auch nicht
				if(time()<$this->playerBldg[$bldg_index]['bldg_time']){
					$upgrade_time=$this->playerBldg[$bldg_index]['bldg_time']+$dauer;
				}else{
					$upgrade_time=time()+$dauer;
				}

				setBldgByFieldID($_SESSION['ums_user_id'], $this->system_id, $fieldid, $bldg_id, $bldg_level+1, $upgrade_time);

				//Rohstoffe abziehen
				$this->doBaukosten($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'][$bldg_level]);

				//Gebäudelevel erhöhen, dass darauf aufbauende Gebäude darauf reagieren können
				$this->playerBldg[$bldg_index]['bldg_level']++;

				$started++;
			}
		}

		return $started;
	}

	/**
	 * Kann in der Übersicht für dieses System "alle upgraden" angeboten werden?
	 * Nur normale, erforschte Planeten mit fertigem Außenposten; besondere Systeme haben ihre eigene Logik.
	 * $bldg: Gebäude des Spielers in diesem System, Index = field_id
	 */
	public function canUpgradeAllFromOverview($bldg, $is_explored, $is_always_visible){
		if(!$is_explored || $is_always_visible || $this->special_system > 0 || $this->system_typ != 1){
			return false;
		}

		if(!isset($bldg[0])){
			return false;
		}

		//Außenposten fertig? (wie checkForOutpost)
		return $bldg[0]['bldg_time'] <= time() || $bldg[0]['bldg_level'] > 1;
	}

	/**
	 * Zeilen eines Systems für die VS-Übersicht (map_mobile.php), eingepackt in ein eigenes tbody,
	 * damit die Zeile nach "alle upgraden" per AJAX ersetzt werden kann.
	 * $bldg: Gebäude des Spielers in diesem System, Index = field_id
	 */
	public function showOverviewRows($bldg, $is_explored, $is_always_visible){
		$system_id=$this->system_id;
		$show_details=$this->special_system<1 && $is_explored && !$is_always_visible;

		if($is_explored || $is_always_visible){
			$system_name='#'.$system_id.' - '.$this->getSystemName();

			if($is_always_visible){
				$bg_image='gp/g/s/sym3.png';
			}else{
				$bg_image='gp/g/s/p1.png';
			}
			$filter_class_unsy='';
		}else{
			$bg_image='gp/g/derassenlogo0.png';
			$system_name='Unerforschtes System (#'.$system_id.')';
			$filter_class_unsy=' f_unsy';
		}

		//die Filterklassen zusammenbauen; Sonder-Systeme bekommen f_spez, aber nur wenn der Spieler sie schon kennt
		//(ein unerforschtes System soll nicht verraten, dass es ein besonderes ist)
		$filter_class=' f_system'.$filter_class_unsy;
		if($this->special_system>0 && ($is_explored || $is_always_visible)){
			$filter_class.=' f_spez';
		}
		if($show_details){
			for($i=0;$i<count($this->fields);$i++){
				$stufe=$bldg[$i]['bldg_level'] ?? 0;

				if($i>0){
					if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
						//bei den Rohstoffen gibt es evtl. kein Gebäude, dann wird trotzdem das Feld mit Stufe 0 angezeigt
						if($stufe>0){
							$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
						}else{
							$filter_class.=' '.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['filter_tag'].'_0';
						}
					}else{
						//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
						if(isset($bldg[$i]['bldg_id']) && isset($GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['factory_id']) && $stufe > 0){
							$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
						}
					}
				}else{
					//Außenposten
					if($stufe > 0){
						$filter_class.=' '.$GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['bldg_filter_tag'].'_'.$stufe;
					}else{
						$filter_class.=' f_plau_0';
					}
				}
			}
		}

		//eine Karte je System; die Filterklassen sitzen am tbody, damit der Filter (vs_filter in ang_fn.js) die ganze Karte
		//ein- und ausblendet. vs_upgrade_row() ersetzt das tbody und hängt seine Meldung an die erste Zelle an.
		//Feldzug: Brennpunkte mit dem Kürzel des Halters markieren (hier, damit es "alle upgraden" übersteht)
		$brennpunkte=\DieEwigen\DE2\Model\Feldzug\FeldzugService::getMarker($GLOBALS['dbi']);
		if(isset($brennpunkte[$system_id])){
			$system_name.=' <span class="mod-chip mod-chip-warn vs-brennpunkt">Brennpunkt'.($brennpunkte[$system_id]!=='' ? ' &middot; '.html_text($brennpunkte[$system_id]) : '').'</span>';
		}

		$output='<tbody id="vsrow'.$system_id.'" class="vs-system'.$filter_class.'">';
		$output.='<tr class="vs-kopf"><td class="vs-name"><img id="sysid'.$system_id.'" src="'.$bg_image.'" class="vs-symbol" alt=""> '.$system_name.'</td>';
		$output.='<td class="vs-aktion"><a href="map_system.php?id='.$system_id.'" class="mod-btn mod-btn-leise ally-btn-klein">Zum System</a></td></tr>';

		///////////////////////////////////////////
		//Felder durchgehen und anzeigen
		///////////////////////////////////////////
		if($show_details){
			$output.='<tr class="vs-felder"><td colspan="2"><div class="vs-feldliste">';
			for($i=0;$i<count($this->fields);$i++){
				//Stufe als Abzeichen, gelb wenn gerade im Bau
				$stufe=$bldg[$i]['bldg_level'] ?? 0;
				$im_bau=isset($bldg[$i]['bldg_time']) && $bldg[$i]['bldg_time'] > time();
				$stufeninfo='<span class="vs-stufe'.($im_bau ? ' vs-stufe-bau' : '').'">'.$stufe.'</span>';
				$stufentitel='&Stufe '.$stufe.($im_bau ? ', im Ausbau' : '');

				if($i>0){
					if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
						//Grafik bestimmen
						$filename_nr=$this->fields[$i][0];
						if($filename_nr<10){
							$filename_nr='0'.$filename_nr;
						}
						//Blocker: rot umrandet
						$output.='<div class="vs-feld'.(isset($this->fields[$i][1]) ? ' vs-feld-blockiert' : '').'" title="'.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'].$stufentitel.'"><img src="gp/g/ele'.$filename_nr.'.gif" alt="">'.$stufeninfo.'</div>';
					}else{
						//Keine Rohstoffe, es könnte aber eine Fabrik&Co vorhanden sein
						if(isset($bldg[$i]['bldg_id']) && isset($GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['factory_id'])){
							$output.='<div class="vs-feld vs-feld-box" title="'.$GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['name'].$stufentitel.'">'.$GLOBALS['greek_chars'][$GLOBALS['map_buildings'][$bldg[$i]['bldg_id']]['factory_id']].$stufeninfo.'</div>';
						}else{
							$output.='<div class="vs-feld vs-feld-leer" title="keine Rohstoffe">&ndash;</div>';
						}
					}
				}else{
					//Außenposten
					$output.='<div class="vs-feld vs-feld-box" title="Au&szlig;enposten'.$stufentitel.'">A'.$stufeninfo.'</div>';
				}
			}

			//alle upgraden, gleiches Symbol wie die Upgrade-Pfeile auf der Systemseite
			if($this->canUpgradeAllFromOverview($bldg, $is_explored, $is_always_visible)){
				$output.='<img class="vs-upgrade-all" src="gp/g/icon12.png" onclick="vs_upgrade_row('.$system_id.', this);" title="Alle upgraden&Startet f&uuml;r alle Geb&auml;ude in diesem System ein Upgrade, soweit Rohstoffe und Au&szlig;enposten es erlauben." alt="Alle upgraden">';
			}
			$output.='</div></td></tr>';
		}

		$output.='</tbody>';

		return $output;
	}

	public function showSpecialSystem($ps){
		$content='';
		include 'special_system_'.$this->special_system.'.inc.php';
		return $content;
	}

	public function showSystem($ps){
		include_once('lib/map_system_defs.inc.php');
		$content='';

		//Kopfzeile: Name im Rahmentitel, darunter die Navigation zwischen den Systemen
		//////////////////////////////////////////////////////////////
		$sonder=isset($this->special_system) && $this->special_system>0;
		$content.=rahmen_oben($this->getSystemName().' <span class="ms-nummer">#'.$this->system_id.'</span>',false);

		$content.='<div class="mod ms'.($sonder ? ' ms-sonder' : '').'">';
		$content.=generate_vsystem_kopfzeile($this->system_id, $this->getSystemName());

		//////////////////////////////////////////////////////////////
		//Test auf besonderes System
		//////////////////////////////////////////////////////////////
		if($sonder){
			$content.='<div class="ms-sonder-inhalt">'.$this->showSpecialSystem($this->system_id,$ps).'</div>';
		}else{

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
				$content.='<div class="mod-leer">in Vorbereitung</div>';
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
					$content.='<div class="mod-hinweis ms-hinweis">Durch den Weltraumhafen hast Du Zugriff auf dieses Battleground-System.<br>Deinen Basisstern erreichst Du auf der Produktionsseite &uuml;ber das Symbol "Basisstern".</div>';
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


		$content.='<div class="ms-titel">'.$GLOBALS['map_buildings'][$bldg_id]['name'].'</div>';
		$content.='<div class="ms-zeile bk-leise">Hier kann ein '.$GLOBALS['map_buildings'][$bldg_id]['name'].' errichtet werden, danach lassen sich die Felder nutzen.</div>';

		//zuerst checken ob man die Technologie erforscht hat
		if(hasTech($GLOBALS['pt'],$tech_id)){

			//kosten
			$baukosten=$this->formatBaukosten($GLOBALS['map_buildings'][$bldg_id]['bldg_cost'][0]);
			$dauer=round($GLOBALS['map_buildings'][$bldg_id]['bldg_time']*$GLOBALS['tech_build_time_faktor']*$GLOBALS['duration_factor']);
			$content.='<div class="ms-abschnitt"><span class="mod-typ">Daf&uuml;r ben&ouml;tigt</span>';
			$content.='<div class="ms-kosten">'.$baukosten['kosten'].'</div>';
			$content.='<div class="ms-zeile">Flotten-Frachtkapazit&auml;t: <b>'.$GLOBALS['map_buildings'][$bldg_id]['bldg_need_fk'].'</b></div>';

			//dauer
			$content.='<div class="ms-zeile">Flotten-Missionsdauer: <b>'.$this->msDauer($dauer).'</b></div>';
			$content.='</div>';

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
					$content.='<div class="ms-abschnitt ms-flotten"><span class="mod-typ">Mission zur Errichtung starten</span>';
					for($f=1;$f<=3;$f++){
						$content.='<div class="ms-flotte"><span>Flotte '.$f.'</span>';
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

									$content.='<span class="mod-chip mod-chip-gruen">Mission gestartet</span> <a href="?id='.$this->system_id.'" class="mod-btn mod-btn-leise ally-btn-klein">Weiter</a>';
								}else{
									$content.='<a href="?id='.$this->system_id.'&amp;action=createoutpost&amp;fleet_id='.$f.'" class="mod-btn ally-btn-klein">Mission starten</a>';
								}

							}else{
								$content.='<span class="ms-fehlt">Frachtkapazit&auml;t zu gering ('.$fleet_fk[$f].'/'.$GLOBALS['map_buildings'][$bldg_id]['bldg_need_fk'].')</span>';
							}
						}else{
							$content.='<span class="bk-leise">hat bereits einen Auftrag</span>';
						}
						$content.='</div>';
					}
					$content.='</div>';
					//auf freie flotte mit frachttkapazität checken

				}else{
					//Info bzgl. fehlender Rohstoffe
					$content.='<div class="ms-fehlt">Es sind nicht alle ben&ouml;tigten Rohstoffe vorhanden.</div>';

				}

				$content.='<div class="mod-meldung mod-meldung-warn ms-warnung">ACHTUNG: Missionen k&ouml;nnen nicht abgebrochen werden.</div>';

				//Die Felder anzeigen
				$content.='<div class="ms-abschnitt"><span class="mod-typ">Felder des Systems</span><div class="vs-feldliste">';
				for($i=0;$i<count($this->fields);$i++){

					///////////////////////////////////////////
					//Feld-Ressource anzeigen
					///////////////////////////////////////////
					if($i>0){
						$blockiert=isset($this->fields[$i][1]) ? ' vs-feld-blockiert' : '';

						if($GLOBALS['map_field_typ'][$this->fields[$i][0]]['name']!='-'){
							//Grafik bestimmen
							$filename_nr=$this->fields[$i][0];
							if($filename_nr<10){
								$filename_nr='0'.$filename_nr;
							}
							$content.='<div class="vs-feld'.$blockiert.'" title="'.$GLOBALS['map_field_typ'][$this->fields[$i][0]]['name'].'"><img src="gp/g/ele'.$filename_nr.'.gif" alt=""></div>';
						}else{
							$content.='<div class="vs-feld vs-feld-leer'.$blockiert.'" title="keine Rohstoffe">&ndash;</div>';
						}
					}

				}

				$content.='</div></div>';

			}else{
				//die Mission läuft schon, daher die Uhrzeit angeben, zu der sie endet
				$content.='<div class="mod-meldung mod-meldung-ok ms-warnung">Die Mission l&auml;uft bereits, '.\DieEwigen\DE2\View\RealTime::until(time()+$mission_time).'.</div>';
			}

		}else{
			//Info bzgl. fehlender Technologie
			$content.='<div class="ms-fehlt">Die Technologie wurde noch nicht erforscht.</div>';

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

		//je Posten eine Zeile, rot wenn er nicht reicht
		$einzelkosten=explode(';', $baukosten);
		foreach ($einzelkosten as $value) {
			$parts=explode("x", $value);

			//5 Grundrohstoffe
			if($value[0]=='R'){
				$kurz=array(1 => 'M', 2 => 'D', 3 => 'I', 4 => 'E', 5 => 'T');
				if(isset($kurz[$value[1]])){
					$reicht=!($pd['restyp0'.$value[1]]<$parts[1]);
					if(!$reicht){$has_all=false;}
					$kosten.='<span class="ms-posten'.($reicht ? '' : ' ms-fehlt').'">'.number_format($parts[1],0,",",".").' '.$kurz[$value[1]].'</span>';
				}
			}elseif($value[0]=='I'){
				$value1=str_replace('I','',$parts[0]);
				$reicht=!($ps[$value1]['item_amount']<$parts[1]);
				if(!$reicht){$has_all=false;}
				$kosten.='<span class="ms-posten'.($reicht ? '' : ' ms-fehlt').'">'.number_format($parts[1],0,",",".").' '.$ps[$value1]['item_name'].' <small>(Lager: '.number_format($ps[$value1]['item_amount'],0,",",".").')</small></span>';
			}
		}

		return array('kosten' => $kosten, 'has_all' => $has_all);
	}

	public function showLoot(){
		include 'inc/userartefact.inc.php';

		$content='';

		//alle geborgenen Items aus der DB holen
		$looted=getUserLootByMapID($_SESSION['ums_user_id'],$this->system_id);

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
						$db_data=mysqli_query($GLOBALS['dbi'], $sql);
						$num = mysqli_num_rows($db_data);
						if($num>0){
							$ally_know_it=true;
						}
					}
				}

				if($inlevel<=$bldg_level || $ally_know_it){
					$content.='<div class="ms-fund"><span class="bk-leise">Feld '.$i.'</span><span>';

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
						break;

						case 4:
							$content.=$amount.'x Credit';
							$loot_msg=$amount.'x Credit';
						break;

						default:
							$content.='ERROR A28';
						break;
					}

					$content.=' <small>ab Stufe '.$inlevel.'</small></span><span class="ms-fund-status">';

					//wurde es schon geborgen?
					if(in_array($i,$looted)){
						//ja, also Info ausgeben
						$content.='<span class="mod-chip">bereits geborgen</span>';
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
									mysqli_query($GLOBALS['dbi'], $sql);
									$geborgen=true;
								break;

								case 3://Spielerartefakt
									if(get_free_artefact_places($_SESSION['ums_user_id'])>0){
										mysqli_query($GLOBALS['dbi'], "INSERT INTO de_user_artefact (user_id, id, level) VALUES ('".$_SESSION['ums_user_id']."', '".($subtyp+1)."', '1')");
										$geborgen=true;
									}else{
										$content.='<span class="ms-fehlt">Im Artefaktgeb&auml;ude ist kein freier Platz.</span> ';
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
							}
						}

						//noch nicht geborgen, also Link anzeigen
						if(!$geborgen && $inlevel<=$bldg_level){
							$content.='<a href="?id='.$this->system_id.'&amp;collectid='.$i.'" class="mod-btn ally-btn-klein">Bergen</a>';
						}else{
							if($ally_know_it){
								$content.='<span class="ms-fehlt">Bergung noch nicht m&ouml;glich</span>';
							}else{
								$content.='<span class="mod-chip mod-chip-gruen">geborgen</span>';
							}
						}

					}

					$content.='</span></div>';

				}

			}
		}

		if(!empty($content)){
			$content='<div class="ms-abschnitt ms-funde"><span class="mod-typ">Fundst&uuml;cke</span>'.$content.'</div>';

		}

		return $content;
	}
}
?>
