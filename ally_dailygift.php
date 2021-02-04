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
$dailyallygift=$row['dailyallygift'];$allytag=$row['allytag'];$allystatus=$row['ally_status'];

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
if($allytag!='' && $allystatus==1){

	$ally_id=getAllyIDByAllytag($allytag);

  //die religiöse verbreitung in der allianz berechnen
  $db_daten=mysql_query("SELECT memberlimit, id FROM de_allys WHERE allytag='$allytag'",$db);
  $row = mysql_fetch_array($db_daten);
  $memberlimit=$row['memberlimit'];
  $allyid=$row['id'];
  
  $allyrelcounter=0;

  //alle Allianzmitglieder durchgehen und deren owner_id in ein array packen
  $result=mysql_query("SELECT * FROM de_user_data WHERE allytag='$allytag' AND status=1",$db);

 
  if($_SESSION['ums_user_id']==1){
    //echo "SELECT * FROM de_user_data WHERE allytag='$allytag' AND status=1";
    
    //mysql_query("UPDATE de_user_data SET allytag='gc2' WHERE allytag='gc²';",$db);
    //mysql_query("UPDATE de_user_data SET allytag='gc2' WHERE allytag='gcÂ²';",$db);
    
    //mysql_query("UPDATE de_user_data SET allytag='gc²' WHERE allytag='gc2';");
  }
  

  while($rowx = mysql_fetch_array($result)){
    $uid=$rowx['user_id'];
    $db_daten=mysql_query("SELECT owner_id FROM de_login WHERE user_id='$uid'",$db);
    $row = mysql_fetch_array($db_daten);
    $owner_id=intval($row["owner_id"]);

    //$relrang=get_religion_level($owner_id);
    //$allyrelcounter+=$relrang;
    $allyrelcounter+=getAnzahlGeworbeneSpielerByOwnerid($owner_id);
  }
  //$allyrelverbreitung=$allyrelcounter*100/($memberlimit*10);
  
  $allyrelverbreitung=$allyrelcounter;
  
  /////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////
  //überprüfen ob man einen bonus abholen möchte
  /////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////

  if($_REQUEST['getdailybonus']==1){
    //transaktionsbeginn
    if (setLock($_SESSION["ums_user_id"])){
	  //auslesen ob er das geschenk schon bekommen hat
	  $db_daten=mysql_query("SELECT dailyallygift FROM de_user_data WHERE user_id='$ums_user_id'",$db);  	
      $row = mysql_fetch_array($db_daten);
      if($row[dailyallygift]==1){

        //in der db und session den bonus f�r den tag deaktivieren
        $dailyallygift=0;  
        mysql_query("UPDATE de_user_data SET dailyallygift=0 WHERE user_id='$ums_user_id'",$db);
   
	    //feststellen welchen bonus man bekommt
        for($i=0;$i<count($allydailygift_lang[bonusname]);$i++)
  		{
    	  if($allyrelverbreitung>=$allyrelverbreitung_need[$i])
    	  {
			if($allyrelverbreitung>=$allyrelverbreitung_need[$i+1] AND $i<count($allydailygift_lang[bonusname])-1) //grau
      	 	{
			}
      		else //grün
      		{
    	      //schleife beenden, da das ziel gefunden worden ist
    	      break;
      		} 
    	  }
    	  else  //rot 
    	  {
    	  }
		}

        //bonus hinterlegen
		$bonusstr='';
      	switch($i){
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
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+2, kartefakt=kartefakt+1, defenseexp=defenseexp+2000, col=col+1 WHERE user_id='$ums_user_id'",$db);
            
            $bonusstr='<br>1 Kriegsartefakt<br>1 Kollektor<br>2 Tronic<br>2.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';
			for($i=0;$i<2;$i++){
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
			$amount=2;
			change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

			changeAllyStorageAmount($ally_id, 13, 1, false);
			$bonusstr.='<br>Allianz: 1 Quantenglimmer';
          break;
        
          case 2:
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+3, kartefakt=kartefakt+1, defenseexp=defenseexp+3000, col=col+2 WHERE user_id='$ums_user_id'",$db);

            $bonusstr='<br>1 Kriegsartefakt<br>2 Kollektoren<br>3 Tronic<br>3.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>2 Titanen-Energiekerne';
			
			for($i=0;$i<2;$i++){
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
			$amount=2;
			change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

			changeAllyStorageAmount($ally_id, 13, 2, false);
			$bonusstr.='<br>Allianz: 2 Quantenglimmer';
          break;

          case 3:
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+4, kartefakt=kartefakt+1, defenseexp=defenseexp+4000, col=col+2 WHERE user_id='$ums_user_id'",$db);

            $bonusstr='<br>1 Kriegsartefakt<br>2 Kollektoren<br>4 Tronic<br>4.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';
			
			for($i=0;$i<2;$i++){
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
			$amount=3;
			change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

			changeAllyStorageAmount($ally_id, 13, 3, false);
			$bonusstr.='<br>Allianz: 3 Quantenglimmer';
          break;

          case 4:
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+5, kartefakt=kartefakt+1, defenseexp=defenseexp+5000, col=col+3 WHERE user_id='$ums_user_id'",$db);
            
            $bonusstr='<br>1 Kriegsartefakt<br>3 Kollektoren<br>5 Tronic<br>5.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>3 Titanen-Energiekerne';
			
			for($i=0;$i<3;$i++){
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
			$amount=3;
			change_storage_amount($_SESSION['ums_user_id'], 2, $amount, false);			

			changeAllyStorageAmount($ally_id, 13, 4, false);
			$bonusstr.='<br>Allianz: 4 Quantenglimmer';
          break;

          case 5:
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+6, kartefakt=kartefakt+1, defenseexp=defenseexp+6000, col=col+3 WHERE user_id='$ums_user_id'",$db);
            
            $bonusstr='<br>1 Kriegsartefakt<br>3 Kollektoren<br>6 Tronic<br>6.000 Erfahrungspunkte f&uuml;r Verteidigungsanlagen<br>4 Titanen-Energiekern';

			for($i=0;$i<3;$i++){
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

			changeAllyStorageAmount($ally_id, 13, 5, false);
			$bonusstr.='<br>Allianz: 1 Quantenglimmer';
          break;

          case 6:
            mysql_query("UPDATE de_user_data SET restyp05=restyp05+7, kartefakt=kartefakt+1, defenseexp=defenseexp+7000, col=col+4 WHERE user_id='$ums_user_id'",$db);
            
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
        
          default:
            echo 'Error 1';
          break;
        }

    
        //info an den spieler, dass er den bonus erhalten hat
        $msg='<div class="info_box"><span class="text3">'.$allydailygift_lang[bonuserhalten].$bonusstr.'</span></div><br><br>';
        //info für den allychat
        $allydailygift_lang[bonuserhaltenchat]='<font color="#802ec1">'.str_replace("{WERT1}", $ums_spielername, $allydailygift_lang[bonuserhaltenchat]).'</font>';
        insert_chat_msg($allyid, 1, '', $allydailygift_lang[bonuserhaltenchat]);
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
	
	//altes Design
	/*
	echo '<div style="display: flex; width: 100%; text-align: center;">';
	for($i=0;$i<count($allydailygift_lang[bonusname]);$i++){
		if($allyrelverbreitung>=$allyrelverbreitung_need[$i]){
			if($allyrelverbreitung>=$allyrelverbreitung_need[$i+1] AND $i<count($allydailygift_lang[bonusname])-1){ //grau
				$grafik='symbol3.png';
				$title=$allydailygift_lang[bonusname][$i].' (Ben&ouml;tigte geworbene Spieler: '.$allyrelverbreitung_need[$i].')<br>Dir steht ein besserer Bonus zu.'.$allydailygift_lang[bonusdescription][$i];
			}else{//grün
				$grafik='symbol4.png';
				//überprüfen ob man es evtl. schon geholt hatte
				if($dailyallygift==1){
					$title=$allydailygift_lang[bonusname][$i].' (Ben&ouml;tigte geworbene Spieler: '.$allyrelverbreitung_need[$i].')<br>Dieser Bonus ist f&uuml;r Dich. Klicke ihn einfach an, um ihn zu erhalten.'.$allydailygift_lang[bonusdescription][$i];
				}
				else $title=$allydailygift_lang[bonusname][$i].' (Ben&ouml;tigte geworbene Spieler: '.$allyrelverbreitung_need[$i].')<br>Du hast heute schon Deinen Bonus erhalten. Morgen kannst Du 
				Dir einen neuen Bonus abholen kommen.'.$allydailygift_lang[bonusdescription][$i];
			} 
		}else{  //rot 
			$grafik='symbol5.png';
			$title=$allydailygift_lang[bonusname][$i].' (Ben&ouml;tigte geworbene Spieler: '.$allyrelverbreitung_need[$i].')<br>F&uuml;r diesen Bonus seid ihr noch nicht gut genug.'.$allydailygift_lang[bonusdescription][$i];
		}
		echo '<div id="g'.$i.'" title="'.$title.'" rel="tooltip" style="background-image: url('.$ums_gpfad.'/g/'.$grafik.'); 
		width: 64px; height: 64px;"></div>';
	}

	echo '</div><br>';

	if($dailyallygift==0){
		echo '<div class="info_box"><span class="text">Der Bonus wurde heute bereits abgeholt.</span></div><br>';	
	}else{
		echo '<a class="btn" href="?getdailybonus=1">abholen</a>';
	}
	*/

	//die Hintergrundfarbe bestimmen

	//$allyrelverbreitung=5;
	//$dailyallygift=1;

	$css=array();
	for($i=0;$i<=6;$i++){
		if($allyrelverbreitung>=$allyrelverbreitung_need[$i]){
			if($allyrelverbreitung>=$allyrelverbreitung_need[$i+1] AND $i<count($allydailygift_lang[bonusname])-1){ 
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
			<td'.$css[6].'>'.$allyrelverbreitung.'/25</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Kriegsartefakt:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>1</td>
			<td'.$css[3].'>1</td>
			<td'.$css[4].'>1</td>
			<td'.$css[5].'>1</td>
			<td'.$css[6].'>1</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Kollektor:</td>
			<td'.$css[0].'>0</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>2</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>3</td>
			<td'.$css[6].'>4</td>
		</tr>		

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Tronic:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>3</td>
			<td'.$css[3].'>4</td>
			<td'.$css[4].'>5</td>
			<td'.$css[5].'>6</td>
			<td'.$css[6].'>7</td>
		</tr>
		
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Verteidigungsanlagen-XP:</td>
			<td'.$css[0].'>1.000</td>
			<td'.$css[1].'>2.000</td>
			<td'.$css[2].'>3.000</td>
			<td'.$css[3].'>4.000</td>
			<td'.$css[4].'>5.000</td>
			<td'.$css[5].'>6.000</td>
			<td'.$css[6].'>7.000</td>
		</tr>

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Zufallsartefakt<sup>1</sup>:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>2</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>3</td>
			<td'.$css[6].'>4</td>
		</tr>
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Auktion<sup>2</sup>:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>1</td>
			<td'.$css[3].'>1</td>
			<td'.$css[4].'>1</td>
			<td'.$css[5].'>1</td>
			<td'.$css[6].'>1</td>
		</tr>		
		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Titanen-Energiekern:</td>
			<td'.$css[0].'>1</td>
			<td'.$css[1].'>2</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>3</td>
			<td'.$css[4].'>3</td>
			<td'.$css[5].'>4</td>
			<td'.$css[6].'>4</td>
		</tr>		

		<tr style="text-align: center;" class="cell">
			<td style="text-align: left;">Allianz-Quantenglimmer:</td>
			<td'.$css[0].'>0</td>
			<td'.$css[1].'>1</td>
			<td'.$css[2].'>2</td>
			<td'.$css[3].'>3</td>
			<td'.$css[4].'>4</td>
			<td'.$css[5].'>5</td>
			<td'.$css[6].'>6</td>
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