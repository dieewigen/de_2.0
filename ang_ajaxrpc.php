<?php
$eftachatbotdefensedisable=1;
mb_internal_encoding("UTF-8");
session_start();
include 'inc/sv.inc.php';
include 'inc/lang/'.$sv_server_lang.'_sector.lang.php';
include 'inccon.php';
include "functions.php";
include "issectork.php";


mt_srand((double)microtime()*10000);

$date_format='d.m.Y - H:i';

//�berpr�fen ob es eine aktive session gibt
if($sv_debug!=1 AND $_REQUEST['unityeditor']!=1){
	if(!isset($_SESSION['ums_user_id']) OR $_SESSION['ums_user_id']<1){
		$status='-1';
		$data[0] = array ('status'=> $status);
		echo json_encode($data); 
		exit;
	}
}

// wenn man im unity-editor ist, dann erh�lt man die user_id 1
if($sv_debug==1 AND $_REQUEST['unityeditor']==1){
	$_SESSION['ums_user_id']=1;
	$ums_user_id=$_SESSION['ums_user_id'];
}

//db_user_data komplett auslesen
$db_daten = mysqli_execute_query($GLOBALS['dbi'],
    "SELECT * FROM de_user_data WHERE user_id=?",
    [$_SESSION['ums_user_id']]);
$playerdata = mysqli_fetch_assoc($db_daten);

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//wenn es keinen fehler gab, dann status 0 zur�ckliefern
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

$status='0';

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// getsystemdata
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
if(isset($_REQUEST['getsectordata'])){
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, col, sector, system, newtrans, newnews, allytag, status, hide_secpics, platz, rang, secsort, secstatdisable 
		 FROM de_user_data 
		 WHERE user_id=?",
		[$_SESSION['ums_user_id']]);
	$row = mysqli_fetch_assoc($db_daten);
	$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
	$restyp05=$row["restyp05"];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
	$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
	$platz=$row["platz"];$erang_nr=$row["rang"];$secsort=$row["secsort"];$secstatdisable=$row["secstatdisable"];
	$col=$row['col'];

	$ownsector=$sector;$ownsystem=$system;
	$hide_secpics=$row["hide_secpics"];

	$secrelcounter=0;
	
	$senddata=array();
	
	$senddata['col_own']=$col;
	$senddata['score_own']=$punkte;
	$senddata['sector_own']=$sector;

	//die Reihenfolge der Spieleraccounts im Sektor
	$player_std_pos[0]=array(0,0);
	$player_std_pos[1]=array(2,0);
	$player_std_pos[2]=array(4,0);
	$player_std_pos[3]=array(0,1);
	$player_std_pos[4]=array(4,1);
	$player_std_pos[5]=array(0,2);
	$player_std_pos[6]=array(2,2);
	$player_std_pos[7]=array(4,2);
	$player_std_pos[8]=array(1,0);
	$player_std_pos[9]=array(3,0);
	$player_std_pos[10]=array(1,2);
	$player_std_pos[11]=array(3,2);


	//kopfgeldprozentsatz kann nicht h�her als kollektorklaurate sein
	//if($sv_bounty_rate>$sv_kollie_klaurate)$sv_bounty_rate=$sv_kollie_klaurate;

	if ($row["status"]==1) $ownally = $row["allytag"];
	//schauen ob er die whg hat und dann die attgrenze anpassen
	if ($techs[4]==0)$sv_attgrenze_whg_bonus=0;

	//owner id auslesen
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT owner_id FROM de_login WHERE user_id=?",
		[$ums_user_id]);
	$row = mysqli_fetch_assoc($db_daten);
	$owner_id=intval($row["owner_id"]);

	//spieler sortiert auslesen    
	$orderby='system';
	if($secsort=='1')$orderby='col';
	elseif($secsort=='2')$orderby='score';

	//maximale anzahl von kollektoren auslesen
	$db_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0");
	$row = mysqli_fetch_assoc($db_daten);
	$maxcol=$row['maxcol'];

	//die Anzahl der "bewohnten" Sektoren auslesen
	$sec_daten = mysqli_execute_query($GLOBALS['dbi'],
		"SELECT * FROM de_user_data WHERE sector>1 GROUP BY sector ORDER BY npc DESC, sector ASC");
	$sec_anzahl=mysqli_num_rows($sec_daten);

	$need_x_y=ceil(sqrt($sec_anzahl));

	$sektoren_x=$need_x_y;
	$sektoren_y=$need_x_y;
	$sektor_width=500;
	$sektor_height=500;

	$npc_sec_counter=0;
	$pc_sec_counter=0;

	//die einzelnen Sektoren-Container erstellen
	$divid=0;

	while($temp = mysqli_fetch_assoc($sec_daten)){
		$sf=$temp['sector'];
		//echo '<br>Sektor: '.$sf;

		//die daten des sektors auslesen
		$db_daten = mysqli_execute_query($GLOBALS['dbi'],
			"SELECT * FROM de_sector WHERE sec_id=?",
			[$sf]);
		$sec_data = mysqli_fetch_assoc($db_daten);

		$rzadd=0;
		if ($ownsector<>$sf){
			$rzadd=2;
		}


		if($sec_data['npc']==1){
			$output='';
			$npc_sec_counter++;
			////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////	
			//npc anzeigen
			////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////

			//kein malus bei aliens
			$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus;
			$col_angriffsgrenze_final=$sv_min_col_attgrenze;
			$rec_bonus=0;
			
			$senddata['sec'.$sf]['sec_angriffsgrenze']=$sec_angriffsgrenze;
			$senddata['sec'.$sf]['col_angriffsgrenze']=$col_angriffsgrenze_final;
			//reisezeit
			$senddata['sec'.$sf]['rz_add']=$rzadd;
			$senddata['sec'.$sf]['npc']=1;

			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT * FROM de_user_data WHERE sector=? ORDER BY $orderby ASC LIMIT 150",
				[$sf]);
			$anz = mysqli_num_rows($db_daten);
			while($row = mysqli_fetch_assoc($db_daten)){
				$system=$row['system'];
				$planet_id=$system;
				if($npc_sec_counter % 2==0){
					$planet_id*=2;
				}
				$system='a'.$row['system'];
				
				$senddata['sec'.$sf][$system]['spielername']=utf8_encode($row['spielername']);
				$senddata['sec'.$sf][$system]['score']=$row['score'];
				$senddata['sec'.$sf][$system]['col']=$row['col'];
			}
		}else{
			$output='';
			$npc_sec_counter++;
			////////////////////////////////////////////////////////////////////////	
			//spieler anzeigen
			////////////////////////////////////////////////////////////////////////
			//sektorkommandant feststellen
			$sector=$sf;
			if(!empty($sector)){
				$sksystem=issectorcommander();
			}else{
				$sksystem=-1;
			}
			$senddata[$sf]['sk']=$sksystem;
			$senddata[$sf]['bk']=$sec_data['bk'];

			//die scandaten des spielers auslesen
			unset($scandaten);
			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT zuser_id, rasse, allytag, ps FROM de_user_scan WHERE user_id=?",
				[$ums_user_id]);
			$index=0;
			while($row = mysqli_fetch_assoc($db_daten))
			{
				$scandaten[$index]['zuser_id']=$row['zuser_id'];
				$scandaten[$index]['rasse']=$row['rasse'];
				$scandaten[$index]['allytag']=$row['allytag'];
				$scandaten[$index]['ps']=$row['ps'];
				$index++;
			}

			//----------- Ally Feine/Freunde
			$allypartner = array();
			$allyfeinde = array();
			$allyresult = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT id FROM de_allys WHERE allytag=?",
				[$ownally]);
			$at = mysqli_num_rows($allyresult);
			if ($at!=0)
			{
			  $row = mysqli_fetch_assoc($allyresult);
			  $allyid = $row["id"];

			  $allyresult = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=? or ally_id_2=?) and (ally_id_1=id or ally_id_2=id)",
				[$allyid, $allyid]);
			  while($row = mysqli_fetch_assoc($allyresult))
			  {
					if ($ownally != $row["allytag"])
							$allypartner[] = $row["allytag"];
			  }

			  $allyresult = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=? or ally_id_angegriffener=?) and (ally_id_angreifer=id or ally_id_angegriffener=id)",
				[$allyid, $allyid]);
			  while($row = mysqli_fetch_assoc($allyresult))
			  {
					if ($ownally != $row["allytag"])
							$allyfeinde[] = $row["allytag"];
			  }
			}

			//sektormalus bei der attgrenze berechnen
			//zuerst anzahl der pc-sektoren auslesen
			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT sec_id FROM de_sector WHERE npc=0 AND platz>1");
			$num = mysqli_num_rows($db_daten);
			if($num<1)$num=1;

			//eigenen sektorplatz auslesen
			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT name, platz FROM de_sector WHERE sec_id=?",
				[$ownsector]);
			$row = mysqli_fetch_assoc($db_daten);
			$ownsectorplatz=$row["platz"];
			$sectorname=utf8_encode($row["name"]);

			//sektorplatzunterschied berechnen
			$secplatz=$sec_data['platz'];
			$secplatzunterschied=$secplatz-$ownsectorplatz;
			if($secplatzunterschied<0)$secplatzunterschied=0;

			//secmalus berechnen
			$sec_malus=$sv_sector_attmalus/$num*$secplatzunterschied;

			//secmalus darf nicht gr��er als maximum sein
			if($sec_malus>$sv_sector_attmalus)$sec_malus=$sv_sector_attmalus;
			$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus+$sec_malus;

			//recyclotronbonus berechnen
			$rec_bonus=$sv_recyclotron_sector_bonus/$num*($secplatz-1);
			//recyclotronbonus darf nicht gr��er als das maximum sein
			if($rec_bonus>$sv_recyclotron_sector_bonus)$rec_bonus=$sv_recyclotron_sector_bonus;

			//angriffsgrenze f�r die kollektoren berechnen
			if($maxcol==0)$maxcol=1;
			$col_angriffsgrenze=$col*100/$maxcol;
			$col_angriffsgrenze_final=$col_angriffsgrenze/100*$sv_max_col_attgrenze;
			if($col_angriffsgrenze_final>$sv_max_col_attgrenze)$col_angriffsgrenze_final=$sv_max_col_attgrenze;
			if($col_angriffsgrenze_final<$sv_min_col_attgrenze)$col_angriffsgrenze_final=$sv_min_col_attgrenze;

			//anzeige ob der sektorstatus deaktiviert worden ist
			//if($secstatdisable==1 AND $ownsector==$sf) $datenstring.= '<div class="info_box text2">'.$sec_lang['secstatdisable'].'</div><br>';
			$senddata[$sf]['sec_angriffsgrenze']=$sec_angriffsgrenze;
			$senddata[$sf]['col_angriffsgrenze']=$col_angriffsgrenze;
			$senddata[$sf]['rec_bonus']=$col_angriffsgrenze;
			$senddata[$sf]['name']=$sectorname;
			//reisezeit
			$senddata[$sf]['rz_add']=$rzadd;
			$senddata[$sf]['npc']=0;
			
			$gesamtpunkte=0;$anz=0;

			//die spielerdaten laden
			$db_daten = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode, 
				de_login.last_login, de_login.user_id, de_user_data.score, de_user_data.col, de_user_data.system, de_user_data.rasse, de_user_data.allytag, 
				de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.werberid, 
				de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04 
				FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_user_data.sector=? ORDER BY $orderby ASC",
				[$sf]);

			$anz = mysqli_num_rows($db_daten);
			$gescol=0;
			$gesamtpunkte=0;
			while($row = mysqli_fetch_assoc($db_daten)){
				$gesamtpunkte+=$row['score'];
				$gescol+=$row['col'];

				//$output.='<tr>';
				$system='s'.$row['system'];
				$planet_id=$row['system'];
				if($pc_sec_counter % 2==0){
					$planet_id*=2;
				}
				
			
				$senddata[$sf][$system]['spielername']=utf8_encode($row['spielername']);

				////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
				//system inkl sk/bk und accountstatus
				////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
				$systemstr=$row['system'];
				$senddata[$sf][$system]['system_status']=1;
				if ($row["lstatus"]==2) $senddata[$sf][$system]['system_status']=2;//$systemstr='['.$systemstr.']';//gesperrt
				if ($row["lstatus"]==3 AND $row["delmode"]>0) $senddata[$sf][$system]['system_status']=5;//$systemstr='{'.$systemstr.'}';//l�schmode
				if ($row["lstatus"]==3 AND $row["delmode"]==0) $senddata[$sf][$system]['system_status']=3;//$systemstr='('.$systemstr.')';//umode

				
				if($sksystem==$row['system']){
					//�berpr�fen ob der sk auch bk ist, ist in 1-mann-sektoren m�glich
					if($row['system']==$sec_data['bk'] AND $anz>1){
						mysqli_execute_query($GLOBALS['dbi'],
							"UPDATE de_sector set bk = 0 WHERE sec_id=?",
							[$sector]);
						$sec_data['bk']=0;
					}
					//$systemstr='<span class="tc3">^'.$systemstr.'^</span>';
				}

				////////////////////////////////////////////////////////////////////////
				//rang
				////////////////////////////////////////////////////////////////////////
				$senddata[$sf][$system]['rang']=$row['rang'];

				////////////////////////////////////////////////////////////////////////
				//spielername, geworben, details, im sektor online
				////////////////////////////////////////////////////////////////////////
				$playername=umlaut($row['spielername']);
				if(strtotime($row["last_login"])+1800 > time() AND $row["lstatus"]==1) $os=1;else $os=0;
				if ($ownsector==$sf AND $secstatdisable==0) $osown=$os;else $osown=0;
				
				$senddata[$sf][$system]['online']=$osown;
				
				$csstag='tc1';
				$playertooltip='';
				if($row["werberid"]==$owner_id){
					$senddata[$sf][$system]['geworben']=1;
				}else{
					$senddata[$sf][$system]['geworben']=0;
				}

				////////////////////////////////////////////////////////////////////////
				//rasse
				////////////////////////////////////////////////////////////////////////
				$knowrasse=0;
				$playerstatus=0;
				$rasse='';
				//hat man scandaten �ber die rasse/allianz?
				$allytagscan='';
				if (isset($scandaten))
				{
					for($i=0;$i<count($scandaten);$i++)
					{
						if($scandaten[$i]['zuser_id']==$row['user_id'])
						{
							if($scandaten[$i]['rasse']>0)$knowrasse=1;
							$playerstatus=$scandaten[$i]['ps'];
							$allytagscan=$scandaten[$i]['allytag'];
						}
					}
				}

				//im eigenen sektor sieht man alle rassen, au�er in sektor 1
				if($sector==$ownsector AND $ownsector>1)$knowrasse=1;


				if($knowrasse==1){
					if($row['rasse']==1)$rasse='<img style="margin-bottom: -4px" src="'.($ums_gpfad ?? '').'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
					if($row['rasse']==2)$rasse='<img style="margin-bottom: -4px" src="'.($ums_gpfad ?? '').'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
					if($row['rasse']==3)$rasse='<img style="margin-bottom: -4px" src="'.($ums_gpfad ?? '').'g/r/raceK.png" title="K�Tharr" width="16px" height="16px">';
					if($row['rasse']==4)$rasse='<img style="margin-bottom: -4px" src="'.($ums_gpfad ?? '').'g/r/raceZ.png" title="Z�tah-ara" width="16px" height="16px">';
					$senddata[$sf][$system]['rasse']=$row['rasse'];
				}else{
					$senddata[$sf][$system]['rasse']=-1;
				}

				////////////////////////////////////////////////////////////////////////
				//allianz, sichtbarkeit durch ally, allyb�ndnis, scandaten, sektor
				////////////////////////////////////////////////////////////////////////
				if($row['status']==1)$allytag=$row['allytag'];else $allytag='';
				$showallytag='';
				$csstag='';

				//festellen welche farbe das allytag hat
				//allypartner
				if (in_array($allytag, $allypartner)){
					$csstag='tc3';
					$showallytag=$allytag;
				}
				//allyfeinde
				elseif (in_array($allytag, $allyfeinde)){
					$csstag='tc2';
					$showallytag=$allytag;
				}
				//ganze andere ally, nichts anzeigen, au�er es gibt scandaten, oder es ist der eigene sektor
				elseif (($ownally != $allytag) OR ($allytag=='') OR ($ownally=='')){
					//anzeige im eigenen sektor
					if($ownsector==$sf AND $ownsector>1)
					{
						$csstag='tc4';
						$showallytag=$allytag;
					}
					//allytag aus den scandaten
					elseif ($allytagscan!='')
					{
						//es gibt scandaten der ally
						$csstag='tc4';
						$showallytag=$allytagscan;
					}
					else
					{
					  //es gibt keine daten vom allytag
					  $showallytag='&nbsp;';
					}
				}
				//eigene ally, tag anzeigen
				else {
					$csstag='tc1';
					$showallytag=$allytag;
				}

				//allytag
				if($showallytag==''){
					$showallytag='&nbsp;';
				} 

				/*
				if($showallytag!='&nbsp;'){
					$showallytag='<a href="ally_detail.php?allytag='.urlencode($showallytag).'"><span class="'.$csstag.'">'.$showallytag.'</span></a>';
				}
				*/

				//$output.='<td class="cell tac" style="font-size: 10pt;">'.$showallytag.'</td>';
				if($showallytag!='&nbsp;'){
					$senddata[$sf][$system]['allytag']=utf8_encode($showallytag);
					$senddata[$sf][$system]['allytag_color']=$csstag;
					
					//$showallytag=' <span class="'.$csstag.'">('.$showallytag.')</span>';
				}else{
					$showallytag='';
					$senddata[$sf][$system]['allytag']='';
					$senddata[$sf][$system]['allytag_color']='';
				}

				////////////////////////////////////////////////////////////////////////
				//punkte
				////////////////////////////////////////////////////////////////////////
				$senddata[$sf][$system]['score']=$row['score'];

				////////////////////////////////////////////////////////////////////////
				//kollektoren
				////////////////////////////////////////////////////////////////////////
				$senddata[$sf][$system]['col']=$row['col'];
						
				$output.='</div>';//player
			}
		
			$senddata[$sf]['platz']=$secplatz;
			$senddata[$sf]['platzgestern']=$sec_data['platz_last_day'];


			//bild von der sternenbasis anzeigen
			$senddata[$sf]['techs']=$sec_data['techs'];
			//Artefakte im Sektor

			//schauen ob es artefakte gibt
			$res = mysqli_execute_query($GLOBALS['dbi'],
				"SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector=?",
				[$sf]);
			$artnum = mysqli_num_rows($res);
			//if ($artnum>0 OR $bed!='000')//artefakt vorhanden, oder raumbasis gebaut
			//{

			$artstr='';
			$c=0;

			if($artnum>0){
				include_once "inc/artefakt.inc.php";
			}


			while($row = mysqli_fetch_assoc($res)){
				//artefakttooltip bauen
				$desc=$row["artdesc"];
				$desc=str_replace("{WERT1}", number_format($sv_artefakt[$row["id"]-1][0], 2,",",".") ,$desc);
				$desc=str_replace("{WERT2}", number_format($sv_artefakt[$row["id"]-1][1], 0,"",".") ,$desc);
				$desc=str_replace("{WERT3}", number_format($sv_artefakt[$row["id"]-1][2], 0,"",".") ,$desc);
				$desc=str_replace("{WERT4}", number_format($sv_artefakt[$row["id"]-1][3], 0,"",".") ,$desc);
				$desc=str_replace("{WERT5}", number_format($sv_artefakt[$row["id"]-1][4], 0,"",".") ,$desc);
				$desc=str_replace("{WERT6}", number_format($sv_artefakt[$row["id"]-1][5], 2,",",".") ,$desc);


				$atip[$c] = '<font color=#'.$row["color"].'>'.$row["artname"].'</font><br>'.$desc;

				$artstr.='<a href="help.php?a=1" target="_blank" title="'.$atip[$c].'"><img src="'.($ums_gpfad ?? '').'g/sa'.$row["picid"].'.gif" border="0"></a>&nbsp;';
				$c++;
			}

		}
	}

	$data = array (
	'status'=> $status, 
	'data' => $senddata
	);
	
	echo json_encode($data);
	exit;
}

//////////////////////////////////////////////////////////
// wenn gar nichts abgefragt wird nur den status ausgeben
//////////////////////////////////////////////////////////

$data = array ('status'=> $status);
echo json_encode($data); 

?>
