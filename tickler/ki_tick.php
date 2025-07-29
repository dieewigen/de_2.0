<?php
$directory='../';
include_once $directory."inc/sv.inc.php";

/*
(39149,'Naxtra-Txacol',8664,1629229,6800,166720,0,101143515.00,3986570.00,681745.00,342245.00,4,33,0,0,0,0,'s1000001100000110000000000000000000000001000010000000001000010000100000000000000000000000000000000000000000000',0,0,0,0,'100;0;0;0',28,5,'',0,0,1,0,9,53,189,46,28,2359,0,0,0,5,'0',0,0,0,0,0,0,0,0,0,9999,24,'',9999,0,0,'0','Naxtra-Txacol',5,50,0,0,1,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,237600,0,1,'1;2;3;4;5;6;7',0,1,0,0,0,0,0,0,0,0,60,0,0,0,0,0,0,3,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0,1,0,0,94138,0,0,0,0)
(39149,'Naxtra-Txacol',8704,15388259,490640,11463975,0,101890015.00,4002570.00,684745.00,343745.00,4,200,0,0,0,0,'s1111011111111111111111100011100000000001111111111111001111111111111100000000000000000000000000000000000000000',0,0,0,0,'100;0;0;0',28,5,'',0,0,1,0,857,9216,7987,3449,444,2359,0,0,0,5,'0',0,0,0,0,0,0,0,0,0,9999,24,'',9999,0,0,'0','Naxtra-Txacol',5,50,0,0,1,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,237600,0,1,'1;2;3;4;5;6;7',0,1,0,0,0,0,0,0,0,0,60,0,0,0,0,0,0,3,0,0,0,0,0,0,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0,1,0,0,94138,0,0,0,0)
*/
//anzeige in der logdatei
include_once "croninfo.inc.php";

if($sv_debug==0 && $sv_comserver==0){
	if(!in_array(intval(date("i")), $GLOBALS['wts'][date("G")])){
		die('<br>KI: NO TICK TIME<br>');
	}
}

$directory="../";

$disablegzip=1;
include_once $directory."inccon.php";
include_once $directory."inc/sv.inc.php";
include_once $directory."inc/schiffsdaten.inc.php";

echo '<html><head></head><body>';

mt_srand((double)microtime()*10000);

//Systemdaten
$result = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system", []);
$row = mysqli_fetch_array($result);
$doetick=$row["doetick"];
$npc_leader=$row['npcleader'];
$rundenalter_wt=$row['wt'];

//npc-leader festlegen
if($npc_leader==0){
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=1 LIMIT 1", []);
	$row     = mysqli_fetch_array($db_daten);
	$npc_leader = $row['user_id'];
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_system SET npcleader='$npc_leader'");
}

if ($doetick==1){
	//anzahl der pc-spieler außerhalb von sektor 1 auslesen
	$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(*) AS wert FROM de_user_data WHERE npc=0 AND sector>1");
	$row     = mysqli_fetch_array($result);
	$playeractive = $row["wert"];
		
	$rassen_id=5;
	
	//alle spieler denen man folgen kann auslesen und deren user_ids in ein array packen
	unset($player);
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE sector>1 AND npc=0 ORDER BY col DESC");
	while($row = mysqli_fetch_array($db_daten))
	{
		$player[]=$row['user_id'];
	}
	
	//alle spieler auslesen denen bereits gefolgt wird
	unset($npc_follow_list);
	$npc_follow_list[]=0;
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=1 AND npcfollow>0");
	while($row = mysqli_fetch_array($db_daten))
	{
		$npc_follow_list[]=$row['npcfollow'];
	}
	
	//print_r($npc_follow_list);
		
	//jeder npc, bis auf den leader, bekommt einen pc dem er von seinen werten her folgen kann
	//anpassungen erfolgen nur dann, wenn der npc gerade nicht angegriffen wird
	//der npc folgt nur spielern au�erhalb von sektor 1
	//der npc erh�lt x % der sachen von dem spieler dem er folgt, damit es keine 1:1 kopie ist
	//kollektoren: x%
	//raumschiffe/t�rme: x%
	
	//erstmal die daten der npc-accounts laden
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE npc=1");
	while($row = mysqli_fetch_array($db_daten))	{
		//damit die accounts echter wirken, wird nicht bei jedem WT etwas gemacht, nur bei jedem x-ten
  		if (mt_rand(1,100)<=7){
			$sector=$row['sector'];
			$system=$row['system'];
			$npc_uid=$row['user_id'];
			$npc_follow=$row['npcfollow'];
			
			echo '<hr>user_id: '.$npc_uid;
			echo '<br>er folgt: '.$npc_follow;
			
			//nur etwas machen, wenn der npc nicht gerade angeflogen wird
			$sql="SELECT * FROM de_user_fleet WHERE hsec<>'$sector' AND zielsec='$sector' AND zielsys='$system' AND aktion>0;";
			$db_datenx=mysqli_execute_query($GLOBALS['dbi'], $sql);
			$num = mysqli_num_rows($db_datenx);
			if($num==0){
				//test auf leader, dieser erhält die Handelslieferungen
				//if($npc_uid!=$npc_leader){
					//wenn er jemandem folgt, dann dessen daten auslesen
					if($npc_follow>0){
						//die daten des spielers auslesen dem er folgt und ggf. jemanden f�r ihn finden
						$db_datenx=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_user_data WHERE user_id='$npc_follow' AND sector>1;");				
						$num = mysqli_num_rows($db_datenx);
						if($num==1){
							$rowx = mysqli_fetch_array($db_datenx);
							echo '<br>Folgt Player: '.$rowx['spielername'];
							$fixscore=$rowx['fixscore'];
							$fhsec=$rowx['sector'];
							$fhsys=$rowx['system'];
							
							//entscheidende punkte des pc berechnen: fleetscore + score der rohstoffe im ausgebauten zustand
							$playerscore=round($rowx['fleetscore']+
								($rowx['restyp01']+$rowx['restyp02']*2+$rowx['restyp03']*3+$rowx['restyp04']*4+$rowx['restyp05']*1000)/10);
							
							echo '<br>Playerscore: '.$playerscore;								

							/*
							//platz des aliens nach kollektoren des pcs berechnen um dar�ber die st�rke feintunen zu k�nnen
							$db_datenk=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE col>".$rowx['col']." AND sector>1 AND npc=0;");				
							$platz = mysqli_num_rows($db_datenk);

							$regulator=1+(100-$platz)/100;
							if($regulator<0.55)$regulator=0.55;
							
							echo '<br>Regulator: '.$regulator;
							*/
							//regulator ist jetzt immer X
							$regulator=0.5;
							
							$techs=$rowx['techs'];
							
							//kollektoren berechnen
							//$newcol=round($rowx['col']*($sv_min_col_attgrenze+0.01));
							//$newcol=round($rowx['col']*($sv_min_col_attgrenze+(($platz+1)/100)));
							$newcol=round($rowx['col']*1.2);

							//$GLOBALS['sv_max_alien_col']=400;
							//$GLOBALS['sv_max_alien_col_typ']=1;

							
							//die Zahl der Kollektoren steigt über die Runde bis zum Maximum
							if($GLOBALS['sv_max_alien_col_typ']==1){
								$maxcol=round($GLOBALS['sv_max_alien_col']*($rundenalter_wt/$sv_winscore));
							}else{
								//die Zahl der maximalan Kollektoren ist fix
								$maxcol=$GLOBALS['sv_max_alien_col'];
							}

							if($newcol>$maxcol){
								$newcol=$maxcol;
							}							

							/*
							if($sv_server_tag!='RDE'){
								$maxcol=round(200*($rundenalter_wt/$sv_winscore));
							}else{
								//RDE
								$maxcol=400;
							}

							if($sv_server_tag!='xDE' && $sv_server_tag!='SDE' && $sv_server_tag!='RDE'){
								if($maxcol>200){
									$maxcol=200;
								}
							}else{
								if($maxcol>600){
									$maxcol=600;
								}						
							}

							if($sv_server_tag=='RDE'){
								if($maxcol>400){
									$maxcol=400;
								}
							}

							if($newcol>$maxcol){
								$newcol=$maxcol;
							}

							if($sv_oscar==1 && $newcol > 50){
								$newcol=50;
							}
							*/
							
							echo '<br>Kollektoren: '.$newcol;							
							
							///////////////////////////////////////////////////////////////
							///////////////////////////////////////////////////////////////
							// einheitenanzahl berechnen
							///////////////////////////////////////////////////////////////
							///////////////////////////////////////////////////////////////
							//die berechnungsgrundlage ist $playerscore, diese werden auf die kampfeinheiten verteilt, keine transen und transporter
							
							//zufallswerte f�r jede einheit vergeben
							$gesamtanteil=0;
							for($i=0;$i<=10;$i++)
							{
								$anteil[$i]=mt_rand(1,100);	
								$gesamtanteil+=$anteil[$i];	
							}
							
							//die anteile jetzt auf prozente runterrechnen
							for($i=0;$i<=10;$i++)
							{
								$anteil[$i]=$anteil[$i]/$gesamtanteil;
							}
							
							
							//t�rme berechnen
							$e100=round(($playerscore*$anteil[0]/1550)*$regulator);
							$e101=round(($playerscore*$anteil[1]/161)*$regulator);
							$e102=round(($playerscore*$anteil[2]/110)*$regulator);
							$e103=round(($playerscore*$anteil[3]/300)*$regulator);
							$e104=round(($playerscore*$anteil[4]/615)*$regulator);
							
							//datensatz updaten
							mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET fixscore='$fixscore', col='$newcol', 
								restyp01=0, restyp02=0, restyp03=0, restyp04=0, restyp05=0, 
								techs='$techs', e100='$e100', e101='$e101', e102='$e102', e103='$e103', e104='$e104' 
							WHERE user_id='$npc_uid';");
							
							echo "<br>UPDATE de_user_data SET col='$newcol', techs='$techs', e100='$e100', e101='$e101', e102='$e102', e103='$e103', e104='$e104' 
							WHERE user_id='$npc_uid';";
							
							//schiffe berechnen
							$e81=0;$e82=0;$e83=0;$e84=0;$e85=0;$e86=0;$e87=0;$e88=0;$e89=0;$e90=0;

							$e81=round(($playerscore*$anteil[5]/160)*$regulator);
							$e82=round(($playerscore*$anteil[6]/625)*$regulator);
							$e83=round(($playerscore*$anteil[7]/2840)*$regulator);
							$e84=round(($playerscore*$anteil[8]/5525)*$regulator);
							$e85=round(($playerscore*$anteil[9]/12600)*$regulator);
							$e86=round(($playerscore*$anteil[10]/225)*$regulator);

							//schiffsdatensatz updaten
							$fleet_id=$npc_uid.'-0';
							mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_fleet SET e81='$e81', e82='$e82', e83='$e83', e84='$e84', e85='$e85', 
							e86='$e86', e87='$e87', e88='$e88', e89='$e89', e90='$e90' WHERE user_id='$fleet_id'");
							
							echo "<br>UPDATE de_user_fleet SET e81='$e81', e82='$e82', e83='$e83', e84='$e84', e85='$e85', 
							e86='$e86', e87='$e87', e88='$e88', e89='$e89', e90='$e90' WHERE user_id='$fleet_id'";
							
							//es kann passieren, dass der NPC per Zufall seinen Spieler verliert und sich ein neues Ziel dem er folgen kann suchen muss
							if(mt_rand(0,100)>99){
								mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET npcfollow=0 WHERE user_id='$npc_uid';");
							}
							
							
						}else{ //der spieler existiert nicht mehr bzw. ist in sektor 1
							//neuen spieler finden
							find_player($npc_uid);
						}
						
					
					}else{ // er folgt niemandem, also jemanden f�r ihn suchen
						find_player($npc_uid);
					}
				//}else echo '<br>Es ist der Leader.';
			}else echo '<br>Wird angeflogen.';
		}
	}
} 
else echo 'Ticks deaktiviert.'; //dokitick

function find_player($npc_uid)
{
	global $db, $npc_follow_list, $player;	
	
	$newplayer=0;
	//alle player durchlaufen und schauen ob sie schon vergeben worden sind
	for($i=0;$i<count($player);$i++){
		if(!in_array($player[$i], $npc_follow_list)){
			$newplayer=$player[$i];
			$npc_follow_list[]=$player[$i];
		}
		if($newplayer>0)break;
	}
	
	//wenn bereits allen spielern gefolgt wird, dann einen per Zufall ausw�hlen
	if($newplayer<1){
		$newplayer=$player[mt_rand(0,count($player)-1)];
	}

	//neuen player dem er folgt hinterlegen
	mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET npcfollow='$newplayer' WHERE user_id='$npc_uid';");
	
	echo '<br>Neuer Spieler: '.$newplayer;
}

?>
</body>
</html>