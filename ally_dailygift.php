<?php
$GLOBALS['deactivate_old_design']=true;

include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_ally.dailygift.lang.php'; 
include "inc/userartefact.inc.php";
include "lib/religion.lib.php";
include "lib/transaction.lib.php";
include_once 'functions.php';

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$dailyallygift=$row['dailyallygift'];$ally_id=$row['ally_id'];$allytag=$row['allytag'];$allystatus=$row['ally_status'];

//$allytag=utf8_decode($allytag);

//freien platz im Artefaktgebäude feststellen
$freeartefactplaces=get_free_artefact_places($ums_user_id);

/*
$allydailygift_lang['bonusdescription'][0]='<br>+ 1 Kriegsartefakt
<br>+ 1 Tronic
<br>+ 1.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][1]='<br>+ 1 Kriegsartefakt
<br>+ 2 Tronic
<br>+ 2.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][2]='<br>+ 1 Kriegsartefakt
<br>+ 3 Tronic
<br>+ 3.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][3]='<br>+ 1 Kriegsartefakt
<br>+ 4 Tronic
<br>+ 4.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][4]='<br>+ 1 Kriegsartefakt
<br>+ 5 Tronic
<br>+ 5.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][5]='<br>+ 1 Kriegsartefakt
<br>+6 Tronic
<br>+6.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';

$allydailygift_lang['bonusdescription'][6]='<br>+ 1 Kriegsartefakt
<br>+ 7 Tronic
<br>+ 7.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen
<br>+ 1 zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
<br>+ 1 gestartete Auktion im Auktionshaus mit einem Preisnachlass
';
*/

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $allydailygift_lang['title']?></title>
<?php include "cssinclude.php"; ?>
<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>
</head>
<body>
<div style="width: 600px; margin-left: auto; margin-right: auto;">
<?php

$allyrelverbreitung_need = array (0, 1, 3, 6, 10, 15, 25);

//überprüfen ob man in einer allianz ist
if($ally_id>0 && $allystatus==1){

	$allyrelcounter=0;

	//alle Allianzmitglieder durchgehen und deren owner_id in ein array packen
	$result=mysql_query("SELECT * FROM de_user_data WHERE ally_id='$ally_id' AND status=1",$db);

	while($rowx = mysql_fetch_array($result)){
		$uid=$rowx['user_id'];
		$db_daten=mysql_query("SELECT owner_id FROM de_login WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_daten);
		$owner_id=intval($row["owner_id"]);

		$allyrelcounter+=getAnzahlGeworbeneSpielerByOwnerid($owner_id);
	}
	
	$allyrelverbreitung=$allyrelcounter;
	
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	//überprüfen ob man einen bonus abholen möchte
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	if(isset($_REQUEST['getdailybonus']) && $_REQUEST['getdailybonus']==1){
		//transaktionsbeginn
		if (setLock($_SESSION["ums_user_id"])){
		//auslesen ob er das geschenk schon bekommen hat
		$db_daten=mysql_query("SELECT dailyallygift FROM de_user_data WHERE user_id='$ums_user_id'",$db);  	
		$row = mysql_fetch_array($db_daten);
		if($row['dailyallygift']==1){

			//in der db und session den bonus für den tag deaktivieren
			$dailyallygift=0;  
			mysql_query("UPDATE de_user_data SET dailyallygift=0 WHERE user_id='$ums_user_id'",$db);
	
			//feststellen welchen bonus man bekommt
			$bonus_anzahl=6;
			for($bonus=0;$bonus<$bonus_anzahl;$bonus++){
				if($allyrelverbreitung>=$allyrelverbreitung_need[$bonus]){
					if($allyrelverbreitung>=$allyrelverbreitung_need[$bonus+1] && $bonus<$bonus_anzahl-1){
						//grau
					}else{
						//grün
						//schleife beenden, da das ziel gefunden worden ist
						break;
					} 
				}else{
					//rot 
				}
			}

			//bonus hinterlegen
			$bonusstr='';
			switch($bonus){
			case 0:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+1, kartefakt=kartefakt+1, defenseexp=defenseexp+1000 WHERE user_id='$ums_user_id'",$db);
			
				$bonusstr='<br>1 Kriegsartefakt<br>1 Tronic<br>1.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>1 Titanen-Energiekern';
				
				for($i=0;$i<1;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}
				}

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+1 WHERE id='$ally_id'");
				$bonusstr.='<br>1 Allianzartefakt';

				//Titanen-Energiekern
				$amount=1;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);

				createAuction($_SESSION['ums_user_id']);
				//Auctacon-Artefakt
				/*
				$freeartefactplaces--;
				if($freeartefactplaces>0)mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', 22, '1')",$db);
				if($freeartefactplaces>0)$bonusstr.='<br>1 Auctacon-Artefakt';
				*/
				
			break;

			case 1:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+2, kartefakt=kartefakt+1, defenseexp=defenseexp+2000 WHERE user_id='$ums_user_id'",$db);
				
				$bonusstr='<br>1 Kriegsartefakt<br>2 Tronic<br>2.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';
				for($i=0;$i<2;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}

					//createAuction($_SESSION['ums_user_id']);
				}

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+2 WHERE id='$ally_id'");
				$bonusstr.='<br>2 Allianzartefakte';

				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=2;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 1, false);
				$bonusstr.='<br>Allianz: 1 Quantenglimmer';
			break;
			
			case 2:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+3, kartefakt=kartefakt+1, defenseexp=defenseexp+3000 WHERE user_id='$ums_user_id'",$db);

				$bonusstr='<br>1 Kriegsartefakt<br>3 Tronic<br>3.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';
				
				for($i=0;$i<2;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}

					//createAuction($_SESSION['ums_user_id']);
				}

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+2 WHERE id='$ally_id'");
				$bonusstr.='<br>2 Allianzartefakte';				

				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=2;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 2, false);
				$bonusstr.='<br>Allianz: 2 Quantenglimmer';
			break;

			case 3:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+4, kartefakt=kartefakt+1, defenseexp=defenseexp+4000 WHERE user_id='$ums_user_id'",$db);

				$bonusstr='<br>1 Kriegsartefakt<br>4 Tronic<br>4.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';
				
				for($i=0;$i<2;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}				

					//createAuction($_SESSION['ums_user_id']);
				}			

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+3 WHERE id='$ally_id'");
				$bonusstr.='<br>3 Allianzartefakte';

				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=3;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 3, false);
				$bonusstr.='<br>Allianz: 3 Quantenglimmer';
			break;

			case 4:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+5, kartefakt=kartefakt+1, defenseexp=defenseexp+5000 WHERE user_id='$ums_user_id'",$db);
				
				$bonusstr='<br>1 Kriegsartefakt<br>5 Tronic<br>5.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';
				
				for($i=0;$i<3;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}

					//createAuction($_SESSION['ums_user_id']);
				}			

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+3 WHERE ally_id='$ally_id'");
				$bonusstr.='<br>3 Allianzartefakte';				

				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=3;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 4, false);
				$bonusstr.='<br>Allianz: 4 Quantenglimmer';
			break;

			case 5:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+6, kartefakt=kartefakt+1, defenseexp=defenseexp+6000 WHERE user_id='$ums_user_id'",$db);
				
				$bonusstr='<br>1 Kriegsartefakt<br>6 Tronic<br>6.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>4 Titanen-Energiekern';

				for($i=0;$i<3;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}

					//createAuction($_SESSION['ums_user_id']);
				}

				//Allianzartefakt
				mysqli_query($GLOBALS['dbi'] , "UPDATE de_allys SET artefacts=artefacts+4 WHERE id='$ally_id'");
				$bonusstr.='<br>4 Allianzartefakte';				

				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=4;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 5, false);
				$bonusstr.='<br>Allianz: 1 Quantenglimmer';
			break;

			/*
			case 6:
				mysql_query("UPDATE de_user_data SET restyp05=restyp05+7, kartefakt=kartefakt+1, defenseexp=defenseexp+7000 WHERE user_id='$ums_user_id'",$db);
				
				$bonusstr='<br>1 Kriegsartefakt<br>4 Kollektoren<br>7 Tronic<br>7.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>4 Titanen-Energiekerne';

				for($i=0;$i<4;$i++){
					if($freeartefactplaces>0){
						$artid=mt_rand(1,15);
						mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
						$bonusstr.='<br>1 '.$ua_name[$artid-1].'-Artefakt';
						$freeartefactplaces--;
					}

					//createAuction($_SESSION['ums_user_id']);
				}			
				
				createAuction($_SESSION['ums_user_id']);

				//Titanen-Energiekern
				$amount=4;
				change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

				changeAllyStorageAmount($ally_id, 13, 6, false);
				$bonusstr.='<br>Allianz: 6 Quantenglimmer';
			break;
			*/
			
			default:
				echo 'Error 1';
			break;
			}

		
			//info an den spieler, dass er den bonus erhalten hat
			$msg='<div class="info_box"><span class="text3">'.$allydailygift_lang['bonuserhalten'].$bonusstr.'</span></div><br><br>';
			//info für den allychat
			$allydailygift_lang['bonuserhaltenchat']='<font color="#802ec1">'.str_replace("{WERT1}", $ums_spielername, $allydailygift_lang['bonuserhaltenchat']).'</font>';
			insert_chat_msg($ally_id, 1, '', $allydailygift_lang['bonuserhaltenchat']);
		}

		//lock wieder entfernen
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
			print("Datensatz Nr. ".$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
		}
		}//lock ende
	}

	include "resline.php";
	
	if(!empty($msg)){
		echo $msg;
	}
	
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	// boni darstellen
	/////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////
	//$allydailygift_lang[description]=str_replace("{WERT1}", number_format($allyrelverbreitung, 2,",","."), $allydailygift_lang[description]);

	echo '<div class="info_box"><span class="text1" style=" font-size: 14px;">Die Gr&ouml;&szlig;e des t&auml;glichen Allianz-Bonus h&auml;ngt von der Anzahl der von Deiner Allianz geworbenen Spielern ab (aktuell: '.$allyrelverbreitung.'). Diese m&uuml;ssen au&szlig;erhalb von Sektor 1 sein, m&uuml;ssen jedoch nicht unbedingt in Deiner Allianz sein.<br>'.$allydailygift_lang['freieartefaktplaetze'].': '.
	$freeartefactplaces.'</span>
	</div><br>';
	
	//die Hintergrundfarbe bestimmen

	//$allyrelverbreitung=5;
	//$dailyallygift=1;

	$css=array();
	for($i=0;$i<=6;$i++){
		if($allyrelverbreitung>=$allyrelverbreitung_need[$i]){
			if($allyrelverbreitung>=$allyrelverbreitung_need[$i+1] AND $i<count($allydailygift_lang['bonusname'])-1){ 
				//grau
				$css[]=' style="background-color: rgba(50,50,50, 0.5);"';
			}else{
				//grün
				$css[]=' style="background-color: rgba(0,210,0, 0.5);"';
			} 
		}else{  
			//rot 
			$css[]=' style="background-color: rgba(230,0,0, 0.5);"';

		}
	}


	//neues Design
	rahmen_oben('Allianzbonus');
	echo '
	<table style="width: 560px" cellspacing="1">
		<tr style="font-weight: bold; text-align: center;" class="cell">
			<td style="text-align: left;">Geworbene Spieler:</td>
			<td'.$css[0].'>'.$allyrelverbreitung.'/0</td>
			<td'.$css[1].'>'.$allyrelverbreitung.'/1</td>
			<td'.$css[2].'>'.$allyrelverbreitung.'/3</td>
			<td'.$css[3].'>'.$allyrelverbreitung.'/6</td>
			<td'.$css[4].'>'.$allyrelverbreitung.'/10</td>
			<td'.$css[5].'>'.$allyrelverbreitung.'/15</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Kriegsartefakt:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>1</td>
			<td'.$css[3].'>1</td>
			<td'.$css[4].'>1</td>
			<td'.$css[5].'>1</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Allianzartefakt:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>3</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>4</td>
		</tr>		

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Tronic:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>3</td>
			<td'.$css[3].'>4</td>
			<td'.$css[4].'>5</td>
			<td'.$css[5].'>6</td>
		</tr>
		
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Verteidigungsanlagen-XP:</td>
			<td'.$css[0].'>1.000</td>
			<td'.$css[1].'>2.000</td>
			<td'.$css[2].'>3.000</td>
			<td'.$css[3].'>4.000</td>
			<td'.$css[4].'>5.000</td>
			<td'.$css[5].'>6.000</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Zufallsartefakt<sup>1</sup>:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>2</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>3</td>
		</tr>
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Auktion<sup>2</sup>:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>1</td>
			<td'.$css[3].'>1</td>
			<td'.$css[4].'>1</td>
			<td'.$css[5].'>1</td>
		</tr>		
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Titanen-Energiekern:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>3</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>4</td>
		</tr>		

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Allianz-Quantenglimmer:</td>
			<td'.$css[0].'>0</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>3</td>
			<td'.$css[4].'>4</td>
			<td'.$css[5].'>5</td>
		</tr>		

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;" colspan="8">
				<sup>1</sup> zuf&auml;lliges Stufe-1-Artefakt aus folgender Liste: Pesara, Vakara, Geangrus, Geabwus, Agsora, Feuroka, Bloroka, Turak, Turla, Recarion, Pekasch, Pekek, Empala, Empdestro, Recadesto (Es wird der Artefakthort mit einem freien Platz ben&ouml;tigt, ansonsten wird das Artefakt nicht gutgeschrieben)
				<br><sup>2</sup> gestartete Auktion im Auktionshaus mit einem Preisnachlass		
			</td>
		</tr>
		';

		//

		if($dailyallygift==0){
			echo '
			<tr style="text-align: center;" class="cell">
				<td style="text-align: center; vertical-align; center; height: 50px;" colspan="8">
					Der Bonus wurde heute bereits abgeholt.
				</td>
			</tr>
			';	
		}else{
			echo '
			<tr style="text-align: center;" class="cell">
				<td style="text-align: center; vertical-align; center; height: 50px;" colspan="8">
					<a class="btn" style="display: inline-block;" href="?getdailybonus=1">abholen</a>
				</td>
			</tr>
			';				
		}			
		
	echo '</table>';





	rahmen_unten();


}else{
  include "resline.php";
  echo '<div class="info_box"><span class="text2">'.$allydailygift_lang[keineally].'</span></div><br>';
}

?>
<br>
</div>
<?php include "fooban.php"; ?>
</body>
</html>