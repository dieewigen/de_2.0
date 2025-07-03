<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_userartefact.inc.lang.php';
include "inc/userartefact.inc.php";
include 'inc/lang/'.$sv_server_lang.'_artefacts.lang.php';
include "functions.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$artbldglevel=$row["artbldglevel"];

$maxlevel=30;
$ausbaukosten=($artbldglevel+1)*20000;
$ausbauzeit=$artbldglevel+1;
$tcost1=5;
$tcost2=10;

$errmsg='';

//Maximale Tickanzahl auslesen
$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//die anzahl von allianzgebäudeupgrades auslesen
$allyid=$allyid=get_player_allyid($ums_user_id);
$ally_geb_bonus=0;
if($allyid>0){
	$db_daten=mysql_query("SELECT * FROM de_allys WHERE id='$allyid'", $db);
	$row = mysql_fetch_array($db_daten);
	$ally_geb_bonus=$row['bldg5'];
}

//Artefakt in ein Basisschiff verschieben
if (isset($_GET["a"]) && $_GET["a"]==1){
	//artefakt einf�gen
	//transaktionsbeginn
	if (setLock($ums_user_id)){
		//flotten id festlegen
		$flotte=0;
		if($_GET["fid"]==1)$flotte=0;
		if($_GET["fid"]==2)$flotte=1;
		if($_GET["fid"]==3)$flotte=2;
		if($_GET["fid"]==4)$flotte=3;

		$id=(int)$_GET["id"];$lvl=(int)$_GET["lvl"];

		//ist das artefakt ein g�ltiges flottenartefakt?
		if($id==6 OR $id==7 OR $id==14 OR $id==15){
		  //schauen ob man das artefakte hat
		  $db_daten=mysql_query("SELECT id FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl'",$db);
		  $num = mysql_num_rows($db_daten);

		  if($num>=1) {
			//flottendaten laden
			$fleetid=$ums_user_id.'-'.$flotte;
			$result=mysql_query("SELECT * FROM de_user_fleet WHERE user_id='$fleetid'",$db);
			$row = mysql_fetch_array($result);

			//schauen ob die flotte daheim ist
			if($row["aktion"]==0){
			  //schauen ob ein artefaktplatz frei ist
				if( ($row["artid1"]==0 && hasTech($pt,133)) OR 
					($row["artid2"]==0 && hasTech($pt,134)) OR 
					($row["artid3"]==0 && hasTech($pt,135)) OR 
					($row["artid4"]==0 && hasTech($pt,136)) OR 
					($row["artid5"]==0 && hasTech($pt,137)) OR 
					($row["artid6"]==0 && hasTech($pt,138))){
				//schauen welcher slot frei ist
				$useslot=6;
				if($row["artid6"]==0)$useslot=6;
				if($row["artid5"]==0)$useslot=5;
				if($row["artid4"]==0)$useslot=4;
				if($row["artid3"]==0)$useslot=3;
				if($row["artid2"]==0)$useslot=2;
				if($row["artid1"]==0)$useslot=1;

				//flotte upadten
				mysql_query("UPDATE de_user_fleet SET artid$useslot='$id', artlvl$useslot='$lvl' WHERE user_id='$fleetid'",$db);
				//artefakt aus dem geb�ude entfernen
				mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND id='$id' AND level='$lvl' LIMIT 1",$db);
				$errmsg.='<span class="ccg">Das Artefakt wurde in das Basisschiff transferiert.</span>';
			  }
			  else $errmsg.='<span class="ccr">Es ist kein freier Slot vorhanden.</span>';
			}
			else $errmsg.='<span class="ccr">Die Flotte befindet sich in einem Einsatz.</span>';
		  }
		  else $errmsg.='<span class="ccr">Du hast kein Artefakte dieser Art.</span>';
		}
		else $errmsg.='<table width=600><tr><td class="ccr">Dieses Artefakt kann nicht in einem Basisschiff verwendet werden.</span>';

		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
		  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
		  print('Datensatz Nr. '.$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}
elseif (isset($_GET["a"]) && $_GET["a"]==2)
{
  //Artefakt aus einem Basisschiff entfernen
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //flotten id festlegen
    $flotte=0;
    if($_GET["fid"]==1)$flotte=0;
    if($_GET["fid"]==2)$flotte=1;
    if($_GET["fid"]==3)$flotte=2;
    if($_GET["fid"]==4)$flotte=3;
	if($_GET["fid"]==5)$flotte=4;
	if($_GET["fid"]==6)$flotte=5;
	if($_GET["fid"]==7)$flotte=6;

    $id=(int)$_GET["id"];
    if($id < 1 || $id > 6)$id=1;

    //flottendaten laden
    $fleetid=$ums_user_id.'-'.$flotte;
    $result=mysql_query("SELECT * FROM de_user_fleet WHERE user_id='$fleetid'",$db);
    $row = mysql_fetch_array($result);

    //ist das artefakt im basisschiff vorhanden
    if($row["artid$id"]>0) {
      //schauen ob im artefakggeb�ude platz ist
      $db_datenx=mysql_query("SELECT user_id FROM de_user_artefact WHERE user_id='$ums_user_id'",$db);
      $numx = mysql_num_rows($db_datenx);
      if($numx<$artbldglevel+$ally_geb_bonus) //es gibt noch platz
      {

        //schauen ob die flotte daheim ist
        if($row["aktion"]==0)
        {
          //flotte upadten
          mysql_query("UPDATE de_user_fleet SET artid$id=0, artlvl$id=0 WHERE user_id='$fleetid'",$db);
          //artefakt in das geb�ude transferieren
          $artid=$row["artid$id"];$artlvl=$row["artlvl$id"];
          mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '$artlvl')",$db);
          $errmsg.='<span class="ccg">Das Artefakt wurde in das Artefaktgeb&auml;ude transferiert.</span>';
        }
        else $errmsg.='<span class="ccr">Die Flotte befindet sich in einem Einsatz.</span>';
      }
      else $errmsg.='<span class="ccr">Im Artefaktgeb&auml;ude ist kein Platz mehr frei.</span>';
    }
    else $errmsg.='<span class="ccr">Dieses Artefakt befindet sich nicht auf dem Basisschiff.</span>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print('Datensatz Nr. '.$ums_user_id." konnte nicht entsperrt werden!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.</font><br><br>';
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Artefakte</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php
//artefakt zerst�ren
if(isset($_REQUEST['destroyartefact']) && $_REQUEST['destroyartefact']==1){
	//transaktionsbeginn
	if (setLock($ums_user_id)){
		$lid=intval($_REQUEST['lid']);
		//schauen ob man das artefakte hat
		$db_daten=mysql_query("SELECT * FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid'",$db);
		$num = mysql_num_rows($db_daten);

		if($num>0)//man hat das artefakt
		{
		  //id auslesen
		  $row = mysql_fetch_array($db_daten);

		  //ist jetzt immer zerstörbar
		  //if($ua_useable[$row["id"]-1]!=1){
			//artefakt löschen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

			//palenium gutschreiben
			change_storage_amount($_SESSION['ums_user_id'],1,100*$row['level']);

			//message ausgeben
			$errmsg.=$artefacts_lang['fehler8'];
		  //}
		}
		else $errmsg.='<font color="#FF0000">'.$artefacts_lang['fehler6'].'</font><br><br>';

		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
		  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
		  print($artefacts_lang['error'].$ums_user_id.$artefacts_lang['error2']."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
}//ende submit1

//artefakt benutzen
	if(isset($_REQUEST['useartefact']) && $_REQUEST['useartefact']==1){
	//transaktionsbeginn
	if (setLock($ums_user_id)){
		$lid=intval($_REQUEST['lid']);
		//schauen ob man das artefakte hat
		$db_daten=mysql_query("SELECT id FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid'",$db);
		$num = mysql_num_rows($db_daten);

		if($num>0){
		$row = mysql_fetch_array($db_daten);
		$id=$row['id'];
		//angriffserfahrungspunkte
		if($id==11){
			/*
			$exp=10000;
			//exp verteilen
			//$fleet_id=$ums_user_id.'-0';
			//mysql_query("UPDATE de_user_fleet SET komatt=komatt+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-1';
			mysql_query("UPDATE de_user_fleet SET komatt=komatt+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-2';
			mysql_query("UPDATE de_user_fleet SET komatt=komatt+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-3';
			mysql_query("UPDATE de_user_fleet SET komatt=komatt+'$exp' WHERE user_id='$fleet_id'",$db);

			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);
			
			//message ausgeben
			$errmsg.=$artefacts_lang['fehler7'];
			*/
		}
		//verteidigungserfahrungspunkte
		elseif($id==12){
			/*
			$exp=10000;
			//exp verteilen
			$fleet_id=$ums_user_id.'-0';
			mysql_query("UPDATE de_user_fleet SET komdef=komdef+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-1';
			mysql_query("UPDATE de_user_fleet SET komdef=komdef+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-2';
			mysql_query("UPDATE de_user_fleet SET komdef=komdef+'$exp' WHERE user_id='$fleet_id'",$db);

			$fleet_id=$ums_user_id.'-3';
			mysql_query("UPDATE de_user_fleet SET komdef=komdef+'$exp' WHERE user_id='$fleet_id'",$db);

			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

			//message ausgeben
			$errmsg.=$artefacts_lang['fehler7'];
			*/
		}
		//tronicar 15-20 tronic
		elseif($id==16)
		{
			$tronic=mt_rand(15,20);

			mysql_query("UPDATE de_user_data SET restyp05=restyp05+'$tronic' WHERE user_id='$ums_user_id'",$db);
			$restyp05+=$tronic;
			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

			//message ausgeben
			$errmsg.=$artefacts_lang['fehler7'].' '.$artefacts_lang['tronic'].': '.$tronic;
		}
		//Artefaktgebäudestufe erhöhen
		elseif($id==17)
		{
			//das artefakt kann nur verwendet werden, wenn aktuell kein ausbau l�uft
			$db_daten = mysql_query("SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id='$ums_user_id'",$db);
			$gebinbau = mysql_num_rows($db_daten);
			if($gebinbau==0)
			{
				mysql_query("UPDATE de_user_data SET artbldglevel=artbldglevel+1 WHERE user_id='$ums_user_id'",$db);
				$artbldglevel++;
				//artefakt l�schen
				mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

				//message ausgeben
				$errmsg.=$artefacts_lang['fehler7'];
			}
			else $errmsg.='<font color="FF0000">W&auml;hrend der Geb&auml;udeausbau l&auml;uft, kann dieses Artefakt nicht verwendet werden.</font>';
		}
		//waringa 1-3 kriegsartefakte
		elseif($id==18)
		{
			$kartefakt=mt_rand(1,3);
			mysql_query("UPDATE de_user_data SET kartefakt=kartefakt+'$kartefakt' WHERE user_id='$ums_user_id'",$db);
			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

			//message ausgeben
			$errmsg.=$artefacts_lang['fehler7'].' '.$artefacts_lang['kriegsartefakte'].': '.$kartefakt;
		}      
		//kollimania 6-8 kollektoren, oder 200 Palenium
		elseif($id==19)
		{
			$result=mysql_query("SELECT id FROM de_user_artefact WHERE user_id='$ums_user_id' AND id=19",$db);
			$num = mysql_num_rows($result);
			if($num<=5){
				//Kollektoren
				$value=mt_rand(6,8);
				mysql_query("UPDATE de_user_data SET col=col+'$value' WHERE user_id='$ums_user_id'",$db);

				//message ausgeben
				$errmsg.=$artefacts_lang['fehler7'].' '.$artefacts_lang['kollektoren'].': '.$value;
			}else{
				//palenium gutschreiben
				$value=200;
				change_storage_amount($_SESSION['ums_user_id'],1,$value);

				//message ausgeben
				$errmsg.=$artefacts_lang['fehler7'].' Palenium: '.$value;
			}

			//artefakt löschen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);
		}
		//sekkollus 1-2 sektorkollektoren
		elseif($id==20)
		{
			$value=mt_rand(1,2);
			mysql_query("UPDATE de_sector SET col=col+'$value' WHERE sec_id='$sector'",$db);
			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);

			//message ausgeben
			$errmsg.=$artefacts_lang['fehler7'].' '.$artefacts_lang['sektorkollektoren'].': '.$value;
		}      
		//creditrüssel
		elseif($id==21){
			//überprüfen wie viel credits man bekommt
			$amount=mt_rand(3,5);
			
			//credit gutschreiben
			//mysql_query("UPDATE de_user_data SET credits=credits+'$amount' WHERE user_id = '$ums_user_id'",$db);
			changeCredits($_SESSION['ums_user_id'], $amount, 'Creditruessel');
			$errmsg.='<font color="#00FF00">Du hast dem gro&szlig;en Ishtarus '.$amount.' Credits wegger&uuml;sselt.</font>';
			
			//artefakt entfernen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);
		}elseif($id==22){
			////////////////////////////////////////////////////////////
			// neue Auktion erstellen
			////////////////////////////////////////////////////////////
			/*
			$errmsg.='<font color="#00FF00">Die Auktion wurde gestartet.</font>';

			createAuction($_SESSION['ums_user_id']);
		
			//artefakt entfernen
			mysqli_query($GLOBALS['dbi'], "DELETE FROM de_user_artefact WHERE lid='$lid'");
			*/
		}

			
			/*
			//�berpr�fen ob dem spieler schonmal kollektoren gestohlen worden sind und ob davon noch jemand dabei ist
			unset($atter);
			$ac=0;
			$db_daten=mysql_query("SELECT * FROM de_user_getcol WHERE zuser_id='$ums_user_id'",$db);
			while($row = mysql_fetch_array($db_daten))
			{
				//spielername des atters feststellen
				$duid=$row["user_id"];
				$result=mysql_query("SELECT spielername, col, sector, system FROM de_user_data WHERE user_id='$duid'",$db);
				$num = mysql_num_rows($result);
				if($num==1)
				{
					$rowx = mysql_fetch_array($result);
					if($rowx['col']>0)//nur ziele mit kollektor gehen
					{
						$atter[$ac]['user_id']=$duid;
						$atter[$ac]['spielername']=$rowx["spielername"];
						$ac++;
					}
				}
			}
			
			if(count($atter)>0)//es gibt einen atter
			{
				//per zufall jemanden ausw�hlen
				$w=mt_rand(0,count($atter)-1);
				
				$zuid=$atter[$w]['user_id'];
				$zspielername=$atter[$w]['spielername'];
				$time=strftime("%Y%m%d%H%M%S");
				
				//dem ziel den kollektor entfernen
				mysql_query("UPDATE de_user_data SET col=col-1, newnews=1, wurdegeruesselt=wurdegeruesselt+1 WHERE user_id='$zuid'",$db);

				//info an das ziel bzgl. r�sselung
				mysql_query("INSERT INTO de_user_news (user_id, typ, time, text) VALUES ('$zuid', '60','$time','Ein anderer Spieler hat Dir einen Kollektor wegger&uuml;sselt.')",$db);
				
				//dem spieler das kriegsartefakt gutschreiben
				mysql_query("UPDATE de_user_data SET kartefakt=kartefakt+1 WHERE user_id='$ums_user_id'",$db);      		
				
				$errmsg.='<font color="#00FF00">Du hast '.$zspielername.' einen Kollektor wegger&uuml;sselt.</font>';
			}
			else //es trifft einen dx
			{
				//dem spieler das kriegsartefakt gutschreiben
				mysql_query("UPDATE de_user_data SET kartefakt=kartefakt+1 WHERE user_id='$ums_user_id'",$db);      		
							
				$errmsg.='<font color="#00FF00">Du hast einem DX61a23 einen Kollektor wegger&uuml;sselt.</font>';
			}

			//artefakt l�schen
			mysql_query("DELETE FROM de_user_artefact WHERE lid='$lid'",$db);
		}      
		*/
		}
		else $errmsg.='<font color="#FF0000">'.$artefacts_lang['fehler6'].'</font><br><br>';

		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg)
		{
		//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}
		else
		{
		print($artefacts_lang['error'].$ums_user_id.$artefacts_lang['error2']."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
}//ende submit1


//artefaktupgrade
if(isset($_REQUEST['mergeartefacts']) && $_REQUEST['mergeartefacts']==1)
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //rohstoffe auslesen
	$db_daten=mysql_query("SELECT restyp05 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
    $restyp05=$row['restyp05'];    
    
    //artefakt-ids
    $lid1=intval($_REQUEST['lid1']);
    $lid2=intval($_REQUEST['lid2']);
    //schauen ob man beide artefakte hat
    $db_daten1=mysql_query("SELECT * FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid1'",$db);
    $num1 = mysql_num_rows($db_daten1);
    
    $db_daten2=mysql_query("SELECT * FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid2'",$db);
    $num2 = mysql_num_rows($db_daten2);
    
    if($num1==1 AND $num2==1)//man hat beide artefakte
    {
      //artefaktdaten auslesen
      $row1 = mysql_fetch_array($db_daten1);
      $id1=$row1['id'];
      $lvl1=$row1['lvl'];
      $row2 = mysql_fetch_array($db_daten2);
      $id2=$row2['id'];
      $lvl2=$row2['lvl'];  
      
      //�berpr�fen ob die artefakte unterschiedlicher art sind
      if($id1!=$id2)//wenn sie unterschiedlich sind, dann muss ein neues artefakt erzeugt werden und die beiden alten gel�scht werden
      {
        //�berpr�fen ob man genug tronic hat
        if($restyp05>=$tcost2)
        {
          //rohstoffe abziehen
          $restyp05=$restyp05-$tcost2;
          mysql_query("UPDATE de_user_data SET restyp05=restyp05-'$tcost2' WHERE user_id='$ums_user_id'",$db);
          
          //neues artefakt erzeugen, muss anderer art als die quellartefakte sein
          $artid=0;
          while($artid==0 OR $artid==$id1 OR $artid==$id2)
          {
            $artid=mt_rand(1,$ua_index+1);
          }
        
          //neues artefakt hinterlegen
          mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
      
          //alte l�schen
          mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid1'",$db);
          mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid2'",$db);

		  //allyid auslesen
		  $allyid=get_player_allyid($ums_user_id);

		  //allyaufgabe kriegsartefakte
		  if($allyid>0)mysql_query("UPDATE de_allys SET questreach = questreach + 1 WHERE id='$allyid' AND questtyp=6",$db);

		  
          //info dass alles geklappt hat
          $errmsg.='<font color="#00FF00">Die Artefakte wurden zu einem neuen Artefakt verschmolzen: '.$ua_name[$artid-1].'</font><br><br>';
        }
        else $errmsg.='<font color="#FF0000">Du ben&ouml;tigst f&uuml;r den Vorgang '.$tcost2.' Tronic.</font><br><br>';
      }
      else
      {
        //schauen ob die artefakte schon auf dem maxlevel sind
        if($lvl1==$lvl2 AND $lvl1<$ua_maxlvl[$id1-1])
        {
          if($restyp05>=$tcost1)
          {
            //rohstoffe abziehen
            $restyp05=$restyp05-$tcost1;
            mysql_query("UPDATE de_user_data SET restyp05=restyp05-'$tcost1' WHERE user_id='$ums_user_id'",$db);
        
            $errmsg.='<font color="#00FF00">'.$artefacts_lang['fehler'].'</font>';
          
            //ein artefakt upgraden lid1
            mysql_query("UPDATE de_user_artefact SET level=level+1 WHERE user_id='$ums_user_id' AND lid='$lid1'",$db);
     
            //alte l�schen lid2
            mysql_query("DELETE FROM de_user_artefact WHERE user_id='$ums_user_id' AND lid='$lid2'",$db);

			//allyid auslesen
			$allyid=get_player_allyid($ums_user_id);

			//allyaufgabe kriegsartefakte
			if($allyid>0)mysql_query("UPDATE de_allys SET questreach = questreach + 1 WHERE id='$allyid' AND questtyp=6",$db);			
			
          }
          else $errmsg.='<font color="#FF0000">Du ben&ouml;tigst f�r den Vorgang '.$tcost1.' Tronic.</font><br><br>';
        }
        else $errmsg.='<font color="#FF0000">'.$artefacts_lang['fehler2'].'</font><br><br>';
      }
    }
    else $errmsg.='<font color="#FF0000">'.$artefacts_lang['fehler3'].'</font>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print($artefacts_lang['error'].$ums_user_id.$artefacts_lang['error2']."<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
}//ende submit1


//geb�udeupgrade
if (isset($_REQUEST["bupgrade"]) AND hasTech($pt,28) AND $artbldglevel<$maxlevel){
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
  $benrestyp01=0;$benrestyp02=0;$benrestyp03=$ausbaukosten;$benrestyp04=0;$benrestyp05=0;
  $tech_ticks=$ausbauzeit;

  //schauen ob man es bauen kann, oder ob schon ein upgrade l�uft
  $db_daten = mysql_query("SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id='$ums_user_id'",$db);
  $gebinbau = mysql_num_rows($db_daten);
  if($gebinbau!=0) $fehlermsg='<font color="FF0000">'.$artefacts_lang['fehler4'];

  //genug ressourcen vorhanden?
  if ($fehlermsg=='' && $errmsg=='' && $restyp01>=$benrestyp01 && $restyp02>=$benrestyp02 && $restyp03>=$benrestyp03 && $restyp04>=$benrestyp04 && $restyp05>=$benrestyp05){
    $restyp01=$restyp01-$benrestyp01;
    $restyp02=$restyp02-$benrestyp02;
    $restyp03=$restyp03-$benrestyp03;
    $restyp04=$restyp04-$benrestyp04;
    $restyp05=$restyp05-$benrestyp05;
    $gr01=$gr01-$restyp01;
    $gr02=$gr02-$restyp02;
    $gr03=$gr03-$restyp03;
    $gr04=$gr04-$restyp04;
     $gr05=$gr05-$restyp05;
    //rohstoffe abziehen
    mysql_query("update de_user_data set restyp01 = restyp01 - $gr01,
     restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
     restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE user_id = '$ums_user_id'",$db);
    //upgrade in der db hinterlegen
    //mysql_query("update de_user_data set buildgnr = $t WHERE user_id='$ums_user_id'",$db);
    mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, 1000, 1, $tech_ticks)",$db);
    //meldung an den account �ber den ausbau
    $errmsg.='<font color="#00FF00">Der Geb&auml;udeausbau wurde gestartet.</font>';
  }else $errmsg.='<font color="#FF0000">'.$artefacts_lang['fehler5'].'</font>';
  
  //transaktionsende
  $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
  if ($erg)
  {
	//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
  }
  else
  {
	print($artefacts_lang['error'].$ums_user_id.$artefacts_lang['error2']."<br><br><br>");
  }
}// if setlock-ende
else echo '<br><font color="#FF0000">'.$artefacts_lang['error3'].'</font><br><br>';
}


//stelle die ressourcenleiste dar
include "resline.php";

if (isset($errmsg) && $errmsg!='')echo '<div class="info_box">'.$errmsg.'</div><br>';

if(!hasTech($pt,28)){
	$techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=28";
	$db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
	$row_techcheck = mysqli_fetch_array($db_tech);


	echo '<br>';
	rahmen_oben('Fehlende Technologie');
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=28" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_28.jpg" border="0"></a></td>
	<td valign="top">Du ben&ouml;tigst folgende Technogie: '.getTechNameByRasse($row_techcheck['tech_name'],$_SESSION['ums_rasse']).'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();  
}else{
	//schauen ob schon ein gebäudeupgrade läuft
	$db_daten = mysql_query("SELECT user_id, verbzeit FROM de_user_build WHERE tech_id=1000 AND user_id='$ums_user_id'",$db);
	$gebinbau = mysql_num_rows($db_daten);

	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	//artefakte/geb�ude darstellen
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////

	//rahmen öffnen
	$title=$artefacts_lang['upinfo'].'&Der Geb&auml;udeausbau erfolgt &uuml;ber das Anklicken der Geb&auml;udegrafik.<br><br>'.$artefacts_lang['upinfo1'].'<br>'.$artefacts_lang['upinfo2'].'<br><br>'.
	$artefacts_lang['upinfo3'].$tcost1.' Tronic<br>'.
	$artefacts_lang['upinfo4'].$tcost2.' Tronic<br><br>'.
	'Das Artefakt kann auch zerst&ouml;rt und in Palenium (100 pro Artefaktstufe) umgewandelt werden.<br><br>'.
	$artefacts_lang['upinfo5'];

	$palenium=get_storage_amount($_SESSION['ums_user_id'], 1); 

	$ueberschrift='
	<div style="display: flex;">
		<div style="width: 145px;"></div>
		<div style="flex-grow: 1;">'.$artefacts_lang['artefaktgebaeude'].' <img id="info" title="'.$title.'" style="vertical-align: middle; margin-top: -4px;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0"></div>
		<div style="width: 145px; text-align: right;"><img id="info" title="Palenium" style="vertical-align: middle; width: 16px; height: auto; margin-top: -4px;" src="g/item1.png" border="0"> '.number_format($palenium, 0,",",".").'&nbsp;</div>
		
	</div>
	';

	rahmen_oben($ueberschrift);

	$cssheight=143+(ceil($artbldglevel/10)-1)*70;

	echo '<div class="cell" style="width: 576px; height: '.$cssheight.'px; top: 0px; position: relative; font-size: 10px; text-align: center;">';

	$title=$artefacts_lang['geblevel'].$artbldglevel.'/'.$maxlevel.'&'.$artefacts_lang['geblevel2'];
	if ($gebinbau!=0){
		$row = mysql_fetch_array($db_daten);  
		//geb�ude wird bereits geupgraded
		$title.='<br>'.$artefacts_lang['gebwirdgebaut'].$row["verbzeit"].$artefacts_lang['woche'].'<br>';
		$showbldglink=0;
	}
	elseif($artbldglevel<$maxlevel)//das geb�ude kann noch weiter ausgebaut werden
	{
		$title.='<br>'.$artefacts_lang['kosten'].': '.number_format($ausbaukosten, 0,",",".").' Iradium';
		$title.='<br>'.$artefacts_lang['upgradedauer'].': '.$ausbauzeit;
		$title.='<br><br>Klicken um das Geb&auml;ude auszubauen.';
		$showbldglink=1;
	}
	else//das geb�ude ist auf maximum
	{
		$title.='<br>'.$artefacts_lang['gebistmax'];
		$showbldglink=0;
	}
	//ally-geb�ude-bonus
	if($allyid>0)$title.='<br>Zus&auml;tzliche Artefaktpl&auml;tze durch Allianzprojekte: '.$ally_geb_bonus;
  
	//<input type="submit" name="bupgrade" value="'.$artefacts_lang[zulevel].($artbldglevel+1).$artefacts_lang[zulevel2].'">'
	//.$artefacts_lang[kosten].number_format($ausbaukosten, 0,",",".").$artefacts_lang[iradiumdauer].($ausbauzeit).'<br>';

	//Artefaktgebäude
	echo '<div id="bldginfo" title="'.$title.'" style="position: relative; float: left; margin-left: 5.5px; margin-top: 4px; width: 50px; height: 64px; border: 1px solid #333333; background-color: #000000;">';
	if($showbldglink==1){
		echo '<a href="artefacts.php?bupgrade=1" style="font-size: 10px; color: #FFFFFF">';
	}

	echo '<span style="position: absolute; left: 0px; top: 0px;"><img src="'.$ums_gpfad.'g/t/1_28.jpg" width="50px" height="50px" border="0"></span>';
	echo '<span style="position: absolute; left: 0px; top: 50px; width: 100%;">'.$artbldglevel.'/'.$maxlevel.'</span>';
	if($showbldglink==1)echo '</a>';
	echo '</div>';

	//msg-area
	echo '<div id="msgarea" style="position: relative; padding: 3px; float: left; margin-left: 5.5px; margin-top: 4px; width: 504px; height: 58px; border: 1px solid #333333; background-color: #000000; font-size: 12px;">';
	echo '</div>';  
  
	//artefakte aus der db holen
	$db_daten=mysql_query("SELECT * FROM de_user_artefact WHERE user_id='$ums_user_id' ORDER BY id, level",$db);
	$anz_artefakte = mysql_num_rows($db_daten);
  
	//artefakte
	$ac=0;
	unset($artefacts);
	while($row = mysql_fetch_array($db_daten)){
		//title/tooltip festlegen
		$title=$ua_name[$row["id"]-1].'&'.$ua_desc[$row["id"]-1];
		if($ua_werte[$row["id"]-1][$row["level"]-1][0]>0){
			//einleitung
			$title.='<br>'.$artefacts_lang['bonusderstufe'];
			//die einzelnen bonusstufen
			for($i=0;$i<count($ua_werte[$row["id"]-1]);$i++){
				if($i==$row['level']-1){$fc[0]='<font color=#00FF00>';$fc[1]='</font>';}else{$fc[0]='';$fc[1]='';}
				$title.='<br>'.$fc[0].($i+1).': '.number_format($ua_werte[$row["id"]-1][$i][0], 2,",",".").'%'.$fc[1];
			}
		}
		//if(isset($ua_werte[$row["id"]-1][$row["level"]][0]))$title.='<br>'.$artefacts_lang['upinfo6'].number_format($ua_werte[$row["id"]-1][$row["level"]][0], 2,",",".").'%';
    
		echo '<div id="ac'.$ac.'" title="'.$title.'" onClick="ca(\'ac'.$ac.'\')" style="position: relative; margin-left: 5.5px; margin-top: 4px; width: 50px; height: 64px; border: 1px solid #333333; float: left; background-color: #000000; cursor: pointer;">';
		echo '<span style="position: absolute; left: 0px; top: 0px;"><img src="'.$ums_gpfad.'g/arte'.$row["id"].'.gif" border="0" alt="'.$ua_name[$row["id"]-1].'"></span>';
		echo '<span style="position: absolute; left: 0px; top: 50px; width: 100%;">'.$row["level"].'/'.$ua_maxlvl[$row["id"]-1].'</span>';
		echo '</div>';
		//daten f�r json zusammenfassen
		$artefacts[$ac]['lid']=$row['lid'];
		$artefacts[$ac]['id']=$row['id'];
		$artefacts[$ac]['level']=$row['level'];
		$artefacts[$ac]['maxlevel']=$ua_maxlvl[$row["id"]-1];
		$artefacts[$ac]['useable']=$ua_useable[$row["id"]-1] ?? 0;
		$artefacts[$ac]['bs']=$ua_bs[$row["id"]-1] ?? 0;
		$artefacts[$ac]['select']=0;

		//id-counter erh�hen
		$ac++;
	}
  
	$title='Freier Artefaktplatz&Dies ist ein freier Platz f&uuml;r ein Artefakt.';
	for($i=$ac;$i<$artbldglevel+$ally_geb_bonus;$i++){
		  echo '<div id="ac'.$ac.'" title="'.$title.'" onClick="ca(\'ac'.$ac.'\')" style="position: relative; margin-left: 5.5px; margin-top: 4px; width: 50px; height: 64px; border: 1px solid #333333; float: left; background-color: #000000;">';
		  echo '<span style="position: absolute; left: 0px; top: 0px;">&nbsp;</span>';
		  echo '<span style="position: absolute; left: 0px; top: 50px; width: 100%;">'.$artefacts_lang['frei'].'</span>';
		  echo '</div>';
		  $ac++;
	}

	echo '</div>';
	rahmen_unten();
  
  ///////////////////////////////////////////////////
  ///////////////////////////////////////////////////
  // basisschiffe
  ///////////////////////////////////////////////////
  ///////////////////////////////////////////////////
  //echo '<br>';
  $title='Jede Flotte wird von einem Basisschiff angef&uuml;hrt. In diesem k&ouml;nnen 3 Artefakte eingesetzt werden um ihre Wirksamkeit zu verbessern.<br>Ein Austausch der Artefakte ist nur im Heimatsystem m&ouml;glich.';
  rahmen_oben('<img id="info" title="'.$title.'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0"> Basisschiffartefakte');
  echo '<div class="cell" style="width: 576px; height: 300px; top: 0px; position: relative; font-size: 10px; text-align: center;">';
  
  
  //flottendaten laden
  $fleetnames=array('Heimatflotte', 'Flotte I', 'Flotte II', 'Flotte III');
  echo '<table style="width: 100%; font-size: 10px;">';
  //alle 4 flotten durchgehen
  for($flotte=0;$flotte<=3;$flotte++){
		$fleetid=$ums_user_id.'-'.$flotte;
		$result=mysql_query("SELECT * FROM de_user_fleet WHERE user_id='$fleetid'",$db);
		$row = mysql_fetch_array($result);

		echo '<tr>';

		//flottenbezeichnung
		echo '<td>';
		echo $fleetnames[$flotte];
		echo '</td>';


		//alle artefakte der flotte durchgehen
		for($artplace=1;$artplace<=6;$artplace++){
			echo '<td style="font-size: 10px;">';
			//title/tooltip festlegen
			
			//if($ua_werte[$row["artid".$artplace]-1][$row["artlvl".$artplace]-1][0]>0){
			if(!empty($ua_name[$row["artid".$artplace]-1])){
				$title=$ua_name[$row["artid".$artplace]-1].'&'.$ua_desc[$row["artid".$artplace]-1];
				//einleitung
				$title.='<br>'.$artefacts_lang['bonusderstufe'];
				//die einzelnen bonusstufen
				for($i=0;$i<count($ua_werte[$row["artid".$artplace]-1]);$i++)
				{
					if($i==$row['artlvl'.$artplace]-1){$fc[0]='<font color=#00FF00>';$fc[1]='</font>';}else{$fc[0]='';$fc[1]='';}
					$title.='<br>'.$fc[0].($i+1).': '.number_format($ua_werte[$row["artid".$artplace]-1][$i][0], 2,",",".").'%'.$fc[1];
				}
				$title.='<br><br>Anklicken um das Artefakt ins Artefaktgeb&auml;ude zu transferieren.';

				//if(isset($ua_werte[$row["id"]-1][$row["level"]][0]))$title.='<br>'.$artefacts_lang['upinfo6'].number_format($ua_werte[$row["id"]-1][$row["level"]][0], 2,",",".").'%';
				echo '<a href="artefacts.php?a=2&fid='.($flotte+1).'&id='.$artplace.'" style="font-size: 10px; color: #FFFFFF;">';
				echo '<div id="ac'.$ac.'" title="'.$title.'" onClick="ca(\'ac'.$ac.'\')" style="position: relative; margin-left: 5.5px; margin-top: 4px; width: 50px; height: 64px; border: 1px solid #333333; float: left; background-color: #000000; cursor: pointer;">';
				echo '<span style="position: absolute; left: 0px; top: 0px;"><img src="'.$ums_gpfad.'g/arte'.$row["artid".$artplace].'.gif" border="0" alt="'.$ua_name[$row["artid".$artplace]-1].'"></span>';
				echo '<span style="position: absolute; left: 0px; top: 50px; width: 100%;">'.$row["artlvl".$artplace].'/'.$ua_maxlvl[$row["artid".$artplace]-1].'</span>';
				echo '</div>';
				echo '</a>';
				//daten f�r json zusammenfassen
				/*
				$artefacts[$ac]['lid']=$row['lid'];
				$artefacts[$ac]['id']=$row['id'];
				$artefacts[$ac]['level']=$row['level'];
				$artefacts[$ac]['maxlevel']=$ua_maxlvl[$row["id"]-1];
				$artefacts[$ac]['useable']=$ua_useable[$row["id"]-1];
				$artefacts[$ac]['select']=0;
				*/
			}
			else //nicht erforschter/leerer platz
			{
				if($artplace==1){
					$need_tech_id=133;
				}elseif($artplace==2){
					$need_tech_id=134;
				}elseif($artplace==3){
					$need_tech_id=135;
				}elseif($artplace==4){
					$need_tech_id=136;
				}elseif($artplace==5){
					$need_tech_id=137;
				}elseif($artplace==6){
					$need_tech_id=138;
				}
				
				if(hasTech($pt,$need_tech_id)){
					$title='Freier Artefaktplatz&Dies ist ein freier Platz f&uuml;r ein Artefakt.';
					$bezeichnung='frei';
					$onclick='onClick="ca(\'ac'.$ac.'\')"';
				}else{
					$title='Fehlende Technologie&Diese Technologie muss erst noch erschlossen werden.';
					$bezeichnung='N/A';					
					$onclick='';
				}
				echo '<div id="ac'.$ac.'" title="'.$title.'" '.$onclick.' style="position: relative; margin-left: 5.5px; margin-top: 4px; width: 50px; height: 64px; border: 1px solid #333333; float: left; background-color: #000000;">';
				echo '<span style="position: absolute; left: 0px; top: 0px;">&nbsp;</span>';
				echo '<span style="position: absolute; left: 0px; top: 50px; width: 100%;">'.$bezeichnung.'</span>';
				echo '</div>';
			}
			echo '</td>';
		}
		echo '</tr>';
  }
  echo '</table>';
  
  
  echo '</div>';
  rahmen_unten();

	rahmen_oben('Informationen zu den Artefakten');
	echo '<div class="cell" style="width: 576px; top: 0px; position: relative; font-size: 10px; text-align: left;">';
	if(isset($_REQUEST['showinfo']) && $_REQUEST['showinfo']==1)
	{
		echo '<b>Woher bekomme ich Artefakte?</b>
		<br>- Du kannst diese durch Angriffe auf NPC-Systeme der DX61a23 bekommen.
		<br>- Im Handel gibt es bei Lieferungen die Chance Artefakte zu bekommen.
		<br>- Im Schwarzmarkt gibt es eine Auswahl von Artefakten, die zur Finanzierung von Die Ewigen dienen.
		<br>- Beim t&auml;glichen Allianzgeschenk ist ein Artefakt enthalten.
		<br>- Bei der wiederholbaren Mission "Das Basisraumschiffwrack"
		<br><br>
		<b>Welche Artefakte gibt es?</b>';
		echo '<table width="100%">';
		for($i=0;$i<=$ua_index;$i++)
  		{
    		if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
    		$ai=$i+1;
    	
	      	echo '<tr class="'.$bg.'" align="center">
    	  <td align="left">
        	<div style="background-color: #000000; width: 50px; height: 50px;">
        	<img src="'.$ums_gpfad.'g/arte'.$ai.'.gif" border="0" title="'.$ua_name[$i].'">
        	</div>
      	</td>
      	<td align="left"><u><b>'.$ua_name[$i].'</b></u><br>'.$ua_desc[$i].'</td></tr>';
  		}
  		echo '</table>';
	}
	else 
	{
		echo '<a href="artefacts.php?showinfo=1">Hier klicken um weitere Informationen zu den Artefakten zu erhalten.</a>';
	}
  		
  
	echo '</div>';
	rahmen_unten();
}

echo '<script language="javascript">';
$data= array ('a' => $artefacts);
echo 'var a = '.json_encode($artefacts).';';
?>

function ca(id)
{
  
  aid=id.replace(/ac/g, '');
  if(a!=null)
  if(a[aid].select==0)
  {
	document.getElementById(id).style.borderColor = "#00FF00";
	a[aid].select=1;
  }
  else 
  {
	document.getElementById(id).style.borderColor = "#333333";
	a[aid].select=0;
  }
  ca_showmsg();
}

function ca_showmsg()
{
  var select=0;
  for(i=0; i<500; i++)
  {
    if(a!=null)if(a[i]!=undefined)if(a[i].select==1)select++;
  }
  
  if(select==0)
  {
    $('#msgarea').html('<font color="#FFFFFF">Du hast die M&ouml;glichkeit Artefakte zu benutzen, oder sie zu kombinieren um ein neues Artefakt zu erhalten. Klicke dazu das gew&uuml;nschte Artefakt an.<br>Das Geb&auml;ude kannst du mit einem Klick auf die Geb&auml;udegrafik ausbauen.</font>');
  }
  else if(select==1)
  {
    for(i=0; i<500; i++)
    {
      id=i;
      if(a[i]!=undefined)if(a[i].select==1)break;
    }
    
    if(a[i].useable==1){
		$('#msgarea').html('<font color="#00FF00">Dieses Artefakt kann benutzt werden.</font><br><a href="artefacts.php?useartefact=1&lid='+a[i].lid+'">Artefakt benutzen</a><br><a style="display: inline-block; margin-top: 10px;" href="artefacts.php?destroyartefact=1&lid='+a[i].lid+'" onclick="return confirm(\'Artefakt vernichten?\')">Artefakt zerst&ouml;ren und in Palenium umwandeln</a>');
	}
    else if(a[i].bs==1)
    {
		$('#msgarea').html('<font color="#00FF00">Dieses Artefakt kann in einem Basisschiff verwendet werden.</font><br><a href="artefacts.php?a=1&fid=1&id='+(a[i].id)+'&lvl='+(a[i].level)+'">Heimatflotte</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="artefacts.php?a=1&fid=2&id='+(a[i].id)+'&lvl='+(a[i].level)+'">Flotte I</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="artefacts.php?a=1&fid=3&id='+(a[i].id)+'&lvl='+(a[i].level)+'">Flotte II</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="artefacts.php?a=1&fid=4&id='+(a[i].id)+'&lvl='+(a[i].level)+'">Flotte III</a><div style="height: 12px;">&nbsp;</div></div><a href="artefacts.php?destroyartefact=1&lid='+a[i].lid+'" onclick="return confirm(\'Artefakt vernichten?\')">Artefakt zerst&ouml;ren und in Palenium umwandeln</a>');
    }
    else 
    $('#msgarea').html('<font color="#FF0000">Dieses Artefakt kann nicht benutzt werden.</font><br><a href="artefacts.php?destroyartefact=1&lid='+a[i].lid+'" onclick="return confirm(\'Artefakt vernichten?\')">Artefakt zerst&ouml;ren und in Palenium umwandeln</a>');
  }
  else if(select==2)
  {
    for(i=0; i<500; i++)
    {
      id1=i;
      if(a[i]!=undefined)if(a[i].select==1)break;
    }
    
    for(i=0; i<500; i++)
    {
      id2=i;
      if(a[i]!=undefined)if(a[i].select==1 && id1!=id2)break;
    }    
    
    if(a[id1].id!=a[id2].id)$('#msgarea').html('<font color="#00FF00">Diese beiden Artefakte k&ouml;nnen in ein zuf&auml;lliges Artefakt der Stufe 1 verschmolzen werden. Das Zielartefakt wird anderer Art als die Quellartefakte sein.</font><br><a href="artefacts.php?mergeartefacts=1&lid1='+a[id1].lid+'&lid2='+a[id2].lid+'" onclick="return confirm(\'Neues Artefakt erzeugen?\')">neues Artefakt erzeugen</a>');
    else if(a[id1].level!=a[id2].level)$('#msgarea').html('<font color="#FF0000">Artefakte der gleichen Art, aber mit unterschiedlicher Stufe k&ouml;nnen nicht verschmolzen werden.</font>'); 
    else if(a[id1].level==a[id1].maxlevel)$('#msgarea').html('<font color="#FF0000">Diese Artefakte befinden sich bereits auf der h&ouml;chsten Stufe.</font>');
    else
    $('#msgarea').html('<font color="#00FF00">Diese Artefakte k&ouml;nnen zu einem Artefakt der gleichen Art mit einer h&ouml;heren Stufe verschmolzen werden.</font><br><a href="artefacts.php?mergeartefacts=1&lid1='+a[id1].lid+'&lid2='+a[id2].lid+'">Artefakte fusionieren</a>');
  }
  else if(select>2)
  {
    $('#msgarea').html('<font color="#FF0000">Es k&ouml;nnen nicht mehr als 2 Artefakte kombiniert werden.</font>');  
  }  
}

ca_showmsg();

</script>
</body>
</html>