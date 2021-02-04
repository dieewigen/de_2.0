<?php
die();
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include 'inc/lang/'.$sv_server_lang.'_functions.lang.php';
include("inc/sabotage.inc.php");
include "functions.php";

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$buildgnr=$row["buildgnr"];
$verbtime=$row["buildgtime"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];$tick=$row['tick'];
$tradesystemscore=$row['tradesystemscore'];$tradesystemtrades=$row['tradesystemtrades'];
$tradesystem_mb_uid=$row['tradesystem_mb_uid'];$tradesystem_mb_tick=$row['tradesystem_mb_tick'];
$lastpcatt=$row['lastpcatt'];$mysc4=$row["sc4"];$spec4=$row['spec4'];

///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
//alle einheitendaten f�r die ausgabe auslesen
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
unset($technames);
unset($techcost);
$db_daten=mysql_query("SELECT * FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id>80 AND tech_id<105 ORDER BY tech_id",$db);
while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
{
	$technames[$row['tech_id']]=$row['tech_name'];
	$techcost[$row['tech_id']][0]=$row['restyp01'];
	$techcost[$row['tech_id']][1]=$row['restyp02'];
	$techcost[$row['tech_id']][2]=$row['restyp03'];
	$techcost[$row['tech_id']][3]=$row['restyp04'];
	$techcost[$row['tech_id']][4]=$row['restyp05'];
}

//sektorsteuersatz auslesen
$db_daten=mysql_query("SELECT ssteuer FROM de_sector WHERE sec_id='$sector'",$db);
$sektorsteuersatz=mysql_result($db_daten, 0,0);

//allgemeiner steuersatz
if($_SESSION['ums_rasse']==2)$handelssteuersatz=4; else $handelssteuersatz=5;

//ishtar haben mehr handelsaktionen
if($ums_rasse==2) $max_trades=5;
else $max_trades=4;


$colors=array('#FFFFFF', '#00FF00', '#9f2ebd', '#2197bd');

//maximalen tick auslesen
$result  = mysql_query("SELECT kt AS tick FROM de_system LIMIT 1",$db);
$row     = mysql_fetch_array($result);
$maxtick = $row["tick"];

//maximale kollektoren auslesen
$result  = mysql_query("SELECT MAX(col) AS col FROM de_user_data",$db);
$row     = mysql_fetch_array($result);
$maxcol  = $row["col"];

//feststellen ob der handel sabotiert ist
$trade_sabotage = false;
if($maxtick<$mysc4+$sv_sabotage[10][0] AND $mysc4>$sv_sabotage[10][0])
{
	$trade_sabotage = true;
}

//zeitdauer der marktbeherrschung in wt
$mb_tick_time=188;

//�berpr�fen wie viele mb-angriffe auf einen laufen
$db_daten=mysql_query("SELECT user_id FROM de_user_data WHERE tradesystem_mb_uid='$ums_user_id' AND tradesystem_mb_tick>'$maxtick'",$db);
$mbatts = mysql_num_rows($db_daten);

mt_srand((double)microtime()*10000);

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//  rohstoffhandel
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
if(intval($_REQUEST['rh_amount'])>0 AND intval($_REQUEST['rh_cost']>0) AND hasTech($pt,4) AND $trade_sabotage == false)
{
	//transaktionsbeginn
	if (setLock($ums_user_id))
	{
		//zielrohstoff auslesen
		$res_target=intval($_REQUEST['rh_v1']);
		if($res_target<1 OR $res_target>5)$res_target=1;
		
		//quellrohstoff auslesen
		$res_source=intval($_REQUEST['rh_v2']);
		if($res_source<1 OR $res_source>5)$res_source=1;
		
		//menge die man bezahlt
		$res_cost=intval($_REQUEST['rh_cost']);
		
		//test ob quelle und ziel unterschiedlich sind
		if($res_target!=$res_source)
		{
			//aktuellen rohstoffstand auslesen
			$db_daten=mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'",$db);
			$row = mysql_fetch_array($db_daten);
			$hasres[1]=$row['restyp01'];
			$hasres[2]=$row['restyp02'];
			$hasres[3]=$row['restyp03'];
			$hasres[4]=$row['restyp04'];
			$hasres[5]=$row['restyp05'];
			
			//reichen die rohstoffe die man hat aus?
			if($res_cost>$hasres[$res_source])$res_cost=$hasres[$res_source];
			
			if($res_cost>0)
			{
				$uv=array(1,2,3,4,10000);
				$resnames=array('Multiplex', 'Dyharra', 'Iradium', 'Eternium', 'Tronic');	
				
				//berechnen wie viel rohstoffe man bekommt
				//steuer berechnen
				//$steueranteil=$res_cost-($res_cost*100/(100+$handelssteuersatz+$sektorsteuersatz));
				
				$steueranteil=$res_cost/100*($handelssteuersatz+$sektorsteuersatz);
				
				$steueranteil_sektor=$steueranteil*$sektorsteuersatz/($handelssteuersatz+$sektorsteuersatz);
				
				$res_get=($res_cost-$steueranteil)*$uv[$res_source-1];
				$res_get=$res_get/$uv[$res_target-1];
				
				//bei tronic als ziel immer abrunden
				if($res_target==5)$res_get=floor($res_get);
				
				if($res_get>0)
				{
					$trademsg='Erworben: '.number_format($res_get, 0, ",",".").' '.$resnames[$res_target-1].'<br>
					Bezahlt (gesamt): '.number_format($res_cost, 0, ",",".").' '.$resnames[$res_source-1].'<br>
					beinhaltete Handelssteuer: '.number_format($steueranteil-$steueranteil_sektor, 0, ",",".").' '.$resnames[$res_source-1].'<br>
					beinhaltete Sektorsteuer: '.number_format($steueranteil_sektor, 0, ",",".").' '.$resnames[$res_source-1].'<br>';
				
					//sektorsteuer in der sektorkasse gutschreiben
					mysql_query("UPDATE de_sector SET restyp0$res_source=restyp0$res_source+$steueranteil_sektor WHERE sec_id='$sector'", $db);
				
					//rohstoffe und sektorspende gutschreiben
					mysql_query("UPDATE de_user_data SET 
					restyp0$res_source=restyp0$res_source-$res_cost, 
					restyp0$res_target=restyp0$res_target+$res_get,  
					spend0$res_source=spend0$res_source+$steueranteil_sektor WHERE user_id='$ums_user_id'", $db);
				
				
					//aktuellen rohstoffwert f�r die resline auslesen
					$db_daten=mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'",$db);
					$row = mysql_fetch_array($db_daten);
					$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
				}
				else $fehlermsg='Du bezahlst nicht genug.';				
			}
		}
		else $fehlermsg='Die Rohstoffarten d&uuml;rfen nicht gleich sein.';
		
		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg)
		{
     			//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}
		else
		{
       		echo 'Fehler bei der Transaktion.';
		}
	}// if setlock-ende
	else echo 'Fehler bei der Transaktion.';
			
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// marktbeherrschung starten
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['mbbutton']) AND hasTech($pt,4) AND $trade_sabotage == false AND $ums_rasse==2 AND $maxtick>$tradesystem_mb_tick){
	$zsec=intval($_REQUEST['mbsec']);
	$zsys=intval($_REQUEST['mbsys']);
	
	//�berpr�fen ob das ziel existiert
	$db_daten=mysql_query("SELECT * FROM de_user_data WHERE sector='$zsec' AND system='$zsys' AND npc=0 AND sector>0",$db);
	$num = mysql_num_rows($db_daten);
	if($num==1)
	{
		$row = mysql_fetch_array($db_daten);
		$zuid=$row['user_id'];
		$zmbtick=$maxtick+$mb_tick_time;
		//auf sich selbst darf man es nicht anwenden
		if($ums_user_id!=$zuid)
		{
			//mb in der db hinterlegen
			mysql_query("UPDATE de_user_data SET tradesystem_mb_uid='$zuid', tradesystem_mb_tick='$zmbtick' WHERE user_id='$ums_user_id'",$db);
			$trademsg='Die Marktbeherrschung hat begonnen.';
			$tradesystem_mb_uid=$zuid;
			$tradesystem_mb_tick=$zmbtick;
		}
		else $fehlermsg='Du kannst Dich nicht selbst als Ziel ausw&auml;hlen.';
	}
	else $fehlermsg='Unter den Koordinaten konnte kein Spieler gefunden werden.';
	
}

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// lieferung annehmen
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['acceptdelivery']) AND hasTech($pt,4) AND $trade_sabotage == false)
{
	//transaktionsbeginn
	if (setLock($ums_user_id))
	{
	
		$trade_id=intval($_REQUEST['acceptdelivery']);

		//die daten des angebots auslesen
		$db_daten=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=1 AND trade_id='$trade_id' AND deliverytime<='$maxtick'",$db);
   		$num = mysql_num_rows($db_daten);
		if($num==1)
		{
			//tradedaten auslesen
			$tradedata = mysql_fetch_array($db_daten);
			
			//rohstoffe gutschreiben und tradescore aktualisieren
			$addtradescore=$tradedata['tradescore'];
			$tradesystemscore+=$addtradescore;
			$tradesystemtrades++;
			mysql_query("UPDATE de_user_data SET restyp0".($tradedata['selltyp'])."=restyp0".($tradedata['selltyp'])."+'".$tradedata['sellamount']."', 
			tradesystemscore=tradesystemscore+'$addtradescore', tradesystemtrades=tradesystemtrades+1 WHERE user_id='$ums_user_id'",$db);						
			if($tradedata['selltyp']==1)$restyp01+=$tradedata['sellamount'];
			if($tradedata['selltyp']==2)$restyp02+=$tradedata['sellamount'];
			if($tradedata['selltyp']==3)$restyp03+=$tradedata['sellamount'];
			if($tradedata['selltyp']==4)$restyp04+=$tradedata['sellamount'];
				
			//handel l�schen
			mysql_query("DELETE FROM de_user_trade WHERE user_id='$ums_user_id' AND trade_id='$trade_id'",$db);
		
			//nachricht an den spieler
			$trademsg='Die Rohstoffe wurden geliefert.';
			
			///////////////////////////////////////////////	
			///////////////////////////////////////////////
			// ggf. bonus gutschreiben
			///////////////////////////////////////////////
			///////////////////////////////////////////////
			$bonuschance=array(10,25,60,100);
			if(mt_rand(0,100)<=$bonuschance[$tradedata['quality']])
			{
				//auf freie artefaktpl�tze pr�fen
				if(get_free_artefact_places($ums_user_id)>0)
				{
					$w=mt_rand(1,13);
					//bonusarten: rohstoffe, kartefakt, kollieartefakt, tronicartefakt, exp-fleet, exp-defense
					switch($w)
					{
						case 1:  //tronic
							$artid=16;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Tronicar-Artefakt';			
						break;
						
						case 2: //vakara-turmbaukosten
							$artid=2;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Vakara-Artefakt';
						break;
						
						case 3:
							$artid=4;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Geabwus-Artefakt';
						break;
						
						case 4:
							$artid=8;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Turak-Artefakt';
						break;
	
						case 5:
							$artid=9;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Turla-Artefakt';
						break;
						
						case 6:
							$artid=10;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Recarion-Artefakt';
						break;
						
						case 7:
							$artid=12;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Pekek-Artefakt';
						break;
						
						case 8:
							$artid=13;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Empala-Artefakt';
						break;
						
						case 9:
							$artid=18;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Waringa-Artefakt';
						break;
						
						case 10:
							$artid=19;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Kollimania-Artefakt';
						break;
						
						case 11:
							$artid=20;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Sekkollus-Artefakt';
						break;
	
						case 12:
							$artid=21;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Creditr&uuml;ssel-Artefakt';
						break;
						
						case 13:
							$artid=17;
							mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$artid', '1')",$db);
							$trademsg.='<br>Der H&auml;ndler sch�tzt Deine Dienste und Du erh&auml;ltst als Bonus: 1 Artascendus';
						break;
						
					}
				}
				else $fehlermsg='Da kein freier Platz im Artefaktgeb&auml;ude vorhanden ist, konnte der Bonus nicht gutgeschrieben werden und ist verfallen.';
			}
		}
		else $fehlermsg='Diese  Lieferung steht nicht zur Verf&uuml;gung.';
		//}
		//else $fehlermsg='Lieferungen k&ouml;nnen nur angenommen werden, wenn ein freier Platz im Artefaktgeb&auml;ude vorhanden ist, da man als Bonus ein Artefakt bekommen kann.';
	
		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg)
		{
     		//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}
		else
		{
       		echo 'Fehler bei der Transaktion.';
		}
	}// if setlock-ende
	else echo 'Fehler bei der Transaktion.';
}	

////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// angebot akzeptieren
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['acceptoffer']) AND hasTech($pt,4) AND $trade_sabotage == false)
{
	$trade_id=intval($_REQUEST['acceptoffer']);

	//transaktionsbeginn
	if (setLock($ums_user_id))
	{
		//�berpr�fen ob er schon die maximale anzahl von trades aktiv hat
		$db_daten=mysql_query("SELECT user_id FROM de_user_trade WHERE user_id='$ums_user_id' AND active=1",$db);
    	$num = mysql_num_rows($db_daten);
		if($num<$max_trades)
		{
			//die daten des angebots auslesen
			$db_daten=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=0 AND trade_id='$trade_id'",$db);
	    	$num = mysql_num_rows($db_daten);
			if($num==1)
			{
				//tradedaten auslesen
				$tradedata = mysql_fetch_array($db_daten);
				
				unset($einheiten);
				$fleetid=$ums_user_id.'-0';
				$db_daten=mysql_query("SELECT * FROM de_user_fleet WHERE user_id='$fleetid'",$db);
				$row = mysql_fetch_array($db_daten);
				for($i=81;$i<=88;$i++)
				{
					$einheiten[$i]=$row['e'.$i];
				}
				$db_daten=mysql_query("SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
				$row = mysql_fetch_array($db_daten);
				for($i=100;$i<=104;$i++)
				{
					$einheiten[$i]=$row['e'.$i];
				}

				//�berpr�fen ob man genug der gew�nschten einheiten hat
				if($tradedata['buyamount']<=$einheiten[$tradedata['buytyp']])
				{
					//npc-leader feststellen
					$result = mysql_query("SELECT * FROM de_system",$db);
					$rowx = mysql_fetch_array($result);
					$npc_leader=$rowx['npcleader'];
					
					//schiffe/t�rme abziehen
					if($tradedata['buytyp']<100)//schiffe
					{
						//spieler
						mysql_query("UPDATE de_user_fleet SET e".($tradedata['buytyp'])."=e".($tradedata['buytyp'])."-'".$tradedata['buyamount']."' WHERE user_id='$fleetid'",$db);
						//dem npc-leader gutschreiben
						$npc_fleetid=$npc_leader.'-0';
						mysql_query("UPDATE de_user_fleet SET e".($tradedata['buytyp'])."=e".($tradedata['buytyp'])."+'".($tradedata['buyamount']/10)."' WHERE user_id='$npc_fleetid'",$db);
						
					}
					else //verteidigungseinheiten
					{
						//spieler
						mysql_query("UPDATE de_user_data SET e".($tradedata['buytyp'])."=e".($tradedata['buytyp'])."-'".$tradedata['buyamount']."' WHERE user_id='$ums_user_id'",$db);
						
						//dem npc-leader gutschreiben
						mysql_query("UPDATE de_user_data SET e".($tradedata['buytyp'])."=e".($tradedata['buytyp'])."+'".($tradedata['buyamount']/10)."' WHERE user_id='$npc_leader'",$db);
					}
					//handel aktivieren
					mysql_query("UPDATE de_user_trade SET active=1, deliverytime=deliverytime+'$maxtick' WHERE user_id='$ums_user_id' AND trade_id='$trade_id'",$db);
					
					//globalen marktcounter updaten
					 mysql_query("UPDATE de_trades SET e".($tradedata['buytyp'])."=e".($tradedata['buytyp'])."+1",$db);
					
					 //ggf. allyaufgabe updaten
					$allyid=$allyid=get_player_allyid($ums_user_id);
					 
					if($allyid>0)mysql_query("UPDATE de_allys SET questreach = questreach + 1 WHERE id='$allyid' AND questtyp=4",$db);
					 
					//nachricht an den spieler
					$trademsg='Das Angebot wurde akzeptiert und die Lieferung wird durchgef&uuml;hrt.';
				}
				else $fehlermsg='Es sind nicht genug Einheiten vorhanden.';

			}
			else $fehlermsg='Dieses Angebot steht nicht zur Verf&uuml;gung.';
		}
		else $fehlermsg='Die maximale Anzahl von gleichzeitigen Warenlieferungen wurde erreicht.';
		
		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg)
		{
     		//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}
		else
		{
       		echo 'Fehler bei der Transaktion.';
		}
	}// if setlock-ende
	else echo 'Fehler bei der Transaktion.';
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Handel</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<script>
<?php 

echo 'var p='.($sektorsteuersatz+$handelssteuersatz).';';
?>
</script>
<?php

//stelle die ressourcenleiste dar
include "resline.php";

if ($fehlermsg!='')echo '<div class="info_box text2">'.$fehlermsg.'</div><br>';

if ($trademsg!='')echo '<div class="info_box text1">'.$trademsg.'</div><br>';

//test auf aktiven handel
if($sv_deactivate_trade==1)
{
	echo '<div class="info_box text2">Der Handel ist deaktiviert.</div><br>';
	die('</body></html>');
}

//test auf whg

if (!hasTech($pt,4)){
  $techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=4";
  $db_tech=mysql_query($techcheck,$db);
  $row_techcheck = mysql_fetch_array($db_tech);
  
  echo '<br>';
  rahmen_oben('Fehlendes Geb&auml;ude');
  echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr align="left" class="cell">
  <td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=4" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_4.jpg" border="0"></a></td>
  <td valign="top">Du ben&ouml;tigst folgendes Geb&auml;ude, welches Du links unter dem Men&uuml;punkt Technologien->Geb&auml;ude bauen kannst: '.$row_techcheck['tech_name'].'</td>
  </tr>';
  echo '</table>';
  rahmen_unten();  
}
else
{
	//sabotage?
	if($trade_sabotage == true)
	{
		echo '<div class="info_box text2">Feindliche Agenten haben den Warenhandel sabotiert. Mehr Informationen k&ouml;nnen beim Geheimdienst eingesehen werden.</div><br>';
		die('</body></html>');
	}
	
	
	//	'<br>Letzter Angriff auf einen Spieler vor '.number_format($maxtick-$lastpcatt, 0, ",", ".").' WTs.
	$tooltip='&Handelspunkte: '.number_format($tradesystemscore, 0, ",", ".").
	'<br>Durchgef&uuml;hrte Handelsaktionen: '.number_format($tradesystemtrades, 0, ",", ".").
	'<br>Marktbeherrschungsziel: '.$mbatts.'
	<br><br>Hier k&ouml;nnen Flotten- und Verteidigungseinheiten verkauft werden. Die Lieferzeit sagt aus, wie viele WTs man auf die Ankunft der Lieferung warten muss. Die Angebotsdauer sagt aus, wie viele WTs das Angebot noch zur Verf&uuml;gung steht, bis ein neues Angebot erscheint.
	<br><br>Bei jeder Lieferung besteht die Chance, dass der H&auml;ndler noch einen Bonus draufpackt. Die Wahrscheinlichkeit daf&uuml;r gibt die Farbe an: weiss=normal, gr&uuml;n=gut, lila=hoch, blau=unschlagbar
	<br>Die Chance auf h&ouml;herwertige Angebote steigt mit der Anzahl der get&auml;tigten Lieferungen, wobei sich andererseits die Chance verringert je mehr Kollektoren man erobert hat.
	<br><br>Wie Handelt man?
	<br>- Die Angebote vergleichen und das Angebot mit dem gr&ouml;&szlig;ten Gewinn annehmen.
	<br>- Die Lieferung abwarten und danach neue Angebote annehmen.
	<br><br>Wie berechnen sich die Handelspunkte?
	<br>- F&uuml;r erhaltene Rohstoffe, die &uuml;ber dem Baupreis liegen, erh&auml;lt man Punkte.
	';
	
	$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
  <td width="16"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$tooltip.'"></td>
  <td>Einheitenhandel</td>
  <td width="16">&nbsp;</td>
  </tr></table>';
	
	rahmen_oben($routput);
	
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	// abgelaufende inaktive angebote l�schen
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	mysql_query("DELETE FROM de_user_trade WHERE active=0 AND offertime<='$maxtick'", $db);
	
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	// �berpr�fen ob es genug angebote in der liste gibt und ggf. anlegen
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	
    //einheitengesuche von npcs
	$db_daten=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=0 AND selltyp<10",$db);
    $num = mysql_num_rows($db_daten);

    //sind alle vorhanden, oder m�ssen welche eingef�gt werden?
    if($num<13)// es sind welche notwendig
    {
    	unset($vorhanden);
    	$tech_ids=array(81,82,83,84,85,86,87,88,100,101,102,103,104);
    	
    	//zuerst alle vorhandenen auslesen
		while($row = mysql_fetch_array($db_daten))
		{
			$vorhanden[$row['buytyp']]=1;
		}
    	
		//jetzt alle tech_ids durchgehen
	    for($i=0;$i<count($tech_ids);$i++)
	    {
	    	$tech_id=$tech_ids[$i];
	    	
	    	if($vorhanden[$tech_id]!=1)
	    	{
		    	$deliverytime=mt_rand(26,34);
		    	//spezialisierung
		    	if($spec4==2)$deliverytime=$deliverytime-3;
		    	if($ums_rasse==2)$deliverytime--;
		    	$offertime=$maxtick+mt_rand(6,24);
		    	//sie bieten rohstoffe an, m bis e
		    	$selltyp=mt_rand(1,4);
		    	
		    	//sie m�chten schiffe oder verteidigungsanlagen haben
		    	/*
		    	if(mt_rand(0,1)==0)//schiffe
		    	{
		    		$buytyp=mt_rand(81,88);	
		    	}   
		    	else //verteidigungsanlagen
		    	{
		    		$buytyp=mt_rand(100,104);
		    	} */
		    	$buytyp=$tech_id;	
		    	
		    	$buyamount=mt_rand(46,87);
		    	
		    	//$maxcol*100(energie pro kollektor)/10 (10% soll der gewinn sein * $deliverytime (lieferzeit) / $buytyp (nach rohstoffwertigkeit verteilen) / 2 (50% der handelswege)
		    	//gewinn = $maxcol*100 (energie pro kollektor) / 10 (10% soll der gewinn sein * $deliverytime (um die lieferzeit einzubeziehen) / $buytyp (nach rohstoffwertigkeit verteilen) / 4 (standardhandelswege)
		    	//auf die herstellungskosten den gewinn schlagen
		    	$buildcost=($techcost[$buytyp][0]+$techcost[$buytyp][1]*2+$techcost[$buytyp][2]*3+$techcost[$buytyp][3]*4+$techcost[$buytyp][4]*10000)*$buyamount;
		    	//es gibt normale und gute angebote vom gewinn her 0-12%, je nach h�ufigkeit der nutzung
		    	//preisbonus berechnen, dazu zuerst den tradecounter auslesen
				$result  		= mysql_query("SELECT * FROM de_trades",$db);
				$globalsells    = mysql_fetch_array($result);
				//�berpr�fen wie viele waren einen h�heren wert haben
				$position=0;
	    		for($j=0;$j<count($tech_ids);$j++)
	    		{
	    			if($globalsells["e".$tech_ids[$j]]>$globalsells["e".$tech_id]) $position++;
	    		}
				
	    		//echo '<br><br>A: '.$position.'';
		    	
		    	//$preisbonus=(1+(mt_rand(0,$position)/100));
		    	$preisbonus=1+($position/100);
		    	$sellamount=($buildcost/$selltyp)+(round($maxcol*100/10*$deliverytime/$selltyp/4)*$preisbonus);
		    	$tradescore=($sellamount-($buildcost/$selltyp))*$selltyp;
		    	
	
		    	//echo '<br>BC: '.$buildcost.' SA: '.$sellamount;
		    	//echo '<br>Gewinn M: '.(round($maxcol*100/10*$deliverytime/$selltyp/4));
		    	//qualit�t der lieferung berechnen, je besser desto h�her die chance auf zugaben
		    	//der $uw untere wert gibt an ab wo der zufallsgenerator startet, max 50 und zu je 25 von aktivit�t im handel und nichtangriffe auf andere spieler
		    	
				/*
		    	//nicht-angriffs-bonus/h�ndler-bonus: 25% der gesamtrundenzeit nicht angegriffen = max
		    	//durch 4 teilen um auf den 25er-anteil zu kommen, 100% sind 25 einheiten
		    	$uwatt=($maxtick-$lastpcatt)*100/($sv_winscore/4)/4;
		    	if($uwatt>25)$uwatt=25;
		    	*/
				//nicht-angriffs-bonus f�r h�ndler: abh�ngig von den kollektoren die man gestohlen hat
				$db_daten = mysql_query("SELECT user_id, SUM(colanz) AS colanz FROM de_user_getcol GROUP BY user_id ORDER by colanz DESC",$db);
				$anzatter = mysql_num_rows($db_daten);
				if($anzatter==0)$anzatter=1;
				$ownattpos=1;
				while($row = mysql_fetch_array($db_daten))
				{
					if($row['user_id']==$ums_user_id)break;
					$ownattpos++;
				}
	
				$uwatt=$ownattpos/$anzatter/4;
						
		    	//handelsaktivit�tsbonus
		    	//grobe anzahl der m�glichen handelsaktionen berechnen
		    	//maxtrades = maximaler tick / 24 (durschnittliche lieferzeit) * 4 (handelswege) 
		    	$maxtrades=$maxtick/24*4;
				if($sv_ewige_runde==1 && $maxtick > $sv_winscore){
					//$maxtrades=$sv_winscore/24*4;
				}
		    	//durch 4 teilen um auf den 25er-anteil zu kommen, 100% sind 25 einheiten
		    	$uwtrades=$tradesystemtrades*100/$maxtrades/4;
		    	if($uwtrades>25)$uwtrades=25;
		    	
		    	$uw=$uwatt+$uwtrades;
		    	$w=mt_rand(round($uw),100);
		    	if($w<70)$quality=0;
		    	elseif($w<90)$quality=1;
		    	elseif($w<100)$quality=2;
		    	else $quality=3;
	    	
	    	
	    		mysql_query("INSERT INTO de_user_trade SET user_id='$ums_user_id', deliverytime='".($deliverytime+$mbatts)."', offertime='$offertime', 
	    		selltyp='$selltyp', sellamount='$sellamount', buytyp='$buytyp', buyamount='$buyamount', quality='$quality', active=0, tradescore='$tradescore' ", $db);
	    	}
    	}
    }
    echo '<div style="width: 570px;" class="cell">';
    
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	//  aktive trades anzeigen
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	$db_daten=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=1 ORDER BY deliverytime",$db);
	$num = mysql_num_rows($db_daten);
	echo '<table border="0" width="100%">';
	echo '<tr class="cell1"><td colspan="3" align="center" style="font-weight: bold; font-size: 24px;">Lieferungen in Bearbeitung ('.$num.'/'.$max_trades.')</td></tr>';
	echo '<tr style="font-weight: bold;" class="cell1" align="center"><td colspan="3">Freie Pl&auml;tze im Artefaktgeb&auml;ude: '.get_free_artefact_places($ums_user_id).'</td></tr>';
	echo '<tr style="font-weight: bold;" class="cell1" align="center"><td>Artikel</td><td>Lieferzeit</td><td>Aktion</td></tr>';
	while($row = mysql_fetch_array($db_daten))
	{
		$color=$colors[$row['quality']];
		echo '<tr align="center" class="cell" style="color: '.$color.';">';
		if($row['selltyp']==1)$reschar='M';
		if($row['selltyp']==2)$reschar='D';
		if($row['selltyp']==3)$reschar='I';
		if($row['selltyp']==4)$reschar='E';
		echo '<td>'.(number_format($row['sellamount'],0, ",",".")).' '.$reschar.'</td>';
		
		if($row['deliverytime']-$maxtick<=0)$lieferzeit='angekommen';else $lieferzeit=$row['deliverytime']-$maxtick;
		echo '<td>'.$lieferzeit.'</td>';
		
		if($row['deliverytime']-$maxtick<=0) echo '<td><a href="tradesystem.php?acceptdelivery='.$row['trade_id'].'">Lieferung annehmen</td>';
		else echo '<td>warten auf Lieferung</td>';
		
		echo '</tr>';
	} 
	echo '</table>';
    
    
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	//eine liste der angebote bei denen man handelswaren verkaufen kann
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	//vorhandene einheiten auslesen
	unset($einheiten);
	$fleetid=$ums_user_id.'-0';
	$db_daten=mysql_query("SELECT * FROM de_user_fleet WHERE user_id='$fleetid'",$db);
	$row = mysql_fetch_array($db_daten);
	for($i=81;$i<=88;$i++)
	{
		$einheiten[$i]=$row['e'.$i];
	}
	$db_daten=mysql_query("SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	for($i=100;$i<=104;$i++)
	{
		$einheiten[$i]=$row['e'.$i];
	}
	
	
	echo '<table border="0" width="100%">';
	echo '<tr class="cell1"><td colspan="6" align="center" style="font-weight: bold; font-size: 24px;">Aktuelle Handelsm&ouml;glichkeiten</td></tr>';
	echo '<tr style="font-weight: bold;" class="cell1" align="center"><td>Gesucht wird</td><td>Lagerbestand</td><td>Geboten wird</td><td title="Lieferzeit">LZ</td><td title="Angebotsdauer">AD</td><td>Aktion</td></tr>';
    $db_daten=mysql_query("SELECT * FROM de_user_trade WHERE user_id='$ums_user_id' AND active=0 AND selltyp<10 ORDER BY buytyp",$db);
	while($row = mysql_fetch_array($db_daten))
	{
		$buytyp=$row['buytyp'];
		$buyamount=$row['buyamount'];
		$buildcost=($techcost[$buytyp][0]+$techcost[$buytyp][1]*2+$techcost[$buytyp][2]*3+$techcost[$buytyp][3]*4+$techcost[$buytyp][4]*10000)*$buyamount;
		
		$color=$colors[$row['quality']];
		if($row['selltyp']==1)
		{
			$mgewinn=$row['sellamount']-$buildcost;
			$reschar='M';
			$gewinn=$mgewinn;
		}
		if($row['selltyp']==2)
		{
			$mgewinn=round($row['sellamount'])*2-$buildcost;
			$reschar='D';
			$gewinn=round($mgewinn/2);
			$buildcost=round($buildcost/2);
		}
		if($row['selltyp']==3)
		{
			$mgewinn=round($row['sellamount'])*3-$buildcost;
			$reschar='I';
			$gewinn=round($mgewinn/3);
			$buildcost=round($buildcost/3);
		}
		if($row['selltyp']==4)
		{
			$mgewinn=round($row['sellamount'])*4-$buildcost;
			$reschar='E';
			$gewinn=round($mgewinn/4);
			$buildcost=round($buildcost/4);
		}
		
		
		echo '<tr align="center" class="cell" style="color: '.$color.';">';
		//tooltip f�r alle infos zum angebot
		$tooltip='Herstellungskosten (ohne Artefaktbonus) in '.$reschar.': '.number_format($buildcost,0, ",",".").'
		<br>Gewinn in '.$reschar.': '.number_format($gewinn,0, ",",".");
		if($row['selltyp']!=1)$tooltip.='<br>Gewinn umgerechnet in M: '.number_format($mgewinn,0, ",",".");
		echo '<td title="'.$tooltip.'">'.$row['buyamount'].'x '.$technames[$row['buytyp']].'</td>';
		//lagerbestand
		echo '<td>'.(number_format($einheiten[$row['buytyp']],0, ",",".")).'</td>';
		
		echo '<td>'.(number_format($row['sellamount'],0, ",",".")).' '.$reschar.'</td>';
		echo '<td>'.($row['deliverytime']).'</td>';
		echo '<td>'.($row['offertime']-$maxtick).'</td>';
		echo '<td><a href="tradesystem.php?acceptoffer='.$row['trade_id'].'">Annehmen</td>';
		echo '</tr>';
	} 
	echo '</table>';
	
	rahmen_unten();
	
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
// rohstoffhandel
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
	rahmen_oben('Rohstoffhandel <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" 
title="Hier k&ouml;nnen Rohstoffe gehandelt werden.&Steuersatz: '.($handelssteuersatz+$sektorsteuersatz).'% ('.$handelssteuersatz.'% Handelssteuer + '.$sektorsteuersatz.'% Sektorsteuer)">');
	echo '<div class="cell" style="width: 570px; text-align: center;">';
	echo '<form action="tradesystem.php" method="POST">';
	echo 'Ich ben&ouml;tige ';
	
	echo '<input type="text" id="rh_amount" name="rh_amount" value="" size="12" maxlength="16" onkeyup="javascript: rh_calc(0);"> ';
	
	echo '<select name="rh_v1" id="rh_v1" onchange="javascript: rh_calc(0);">
      <option value="1" selected>Multiplex</option>
      <option value="2">Dyharra</option>
      <option value="3">Iradium</option>
      <option value="4">Eternium</option>
      <option value="5">Tronic</option>
    </select>';  
	
	echo ' und bezahle mit ';

	echo '<input type="text" id="rh_cost" name="rh_cost" value="" size="12" maxlength="16" onkeyup="javascript: rh_calc(1);"> ';	
	
	echo '<select name="rh_v2" id="rh_v2" onchange="javascript: rh_calc(0);">
      <option value="1">Multiplex</option>
      <option value="2" selected>Dyharra</option>
      <option value="3">Iradium</option>
      <option value="4">Eternium</option>
      <option value="5">Tronic</option>
    </select>.';  	
	
	echo '<br><br><input type="Submit" name="startrestrade" value="Handel durchf&uuml;hren">'; 
	echo '</form>';
	echo '</div>';
	rahmen_unten();
	
	
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	// ishtar-marktbeherrschung
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	if($ums_rasse==2)
	{
		//dauer
		$duration=188;
		
		$tooltip='&Dauer der Marktbeherrschung: '.$duration.' Wirtschaftsticks<br><br>Deine Rasse beherrscht die Kunst der Marktbeherrschung, welche Sie dazu einsetzen kann, die Handelspartner der Konkurrenz zu beeinflussen.<br><br>Durch diese Beeinflussung erh&ouml;hen sich bei dem Ziel die Lieferzeiten um einen Wirtschaftstick.<br><br>Diese F&auml;higkeit wirkt kumulativ mit der anderer Ishtar beim selben Ziel.<br><br>Nach Ablauf kann die F&auml;higkeit sofort wieder eingesetzt werden. Die F&auml;higkeit kann nicht abgebrochen werden.';
		
		$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
	  <td width="16"><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$tooltip.'"></td>
	  <td>Marktbeherrschung</td>
	  <td width="16">&nbsp;</td>
	  </tr></table>';
		
		rahmen_oben($routput);
		echo '<div style="width: 570px;" class="cell">';

		//test ob aktuell bereits ine mb l�uft
		if($tradesystem_mb_tick>=$maxtick)// es l�uft noch was
		{
			//�berpr�fen ob das ziel noch existiert
			$db_daten=mysql_query("SELECT * FROM de_user_data WHERE user_id='$tradesystem_mb_uid'",$db);
			$num = mysql_num_rows($db_daten);
			if($num==1)
			{
				$row = mysql_fetch_array($db_daten);

				//spielerinfo ausgeben
				echo 'Die Marktbeherrschung ist aktiv. Ziel: '.$row['spielername'].' ('.$row['sector'].':'.$row['system'].')';
				echo '<br>Verbleibende Wirtschaftsticks: '.($tradesystem_mb_tick-$maxtick);
				
			}
			else //spieler wurde gel�scht
			{
				//db zur�cksetzen
				mysql_query("UPDATE de_user_data SET tradesystem_mb_uid=0, tradesystem_mb_tick=0 WHERE user_id='$ums_user_id'",$db);
				echo 'Das Ziel der Marktbeherrschung konnte nicht gefunden werden.';
			}
			
		}		
		else //es l�uft nichts
		{
			//zielauswahl anzeigen
			echo '<form action="tradesystem.php" method="POST">';
			echo 'W&auml;hle das Ziel aus (Sektor:System): <input name="mbsec" type="text" size="4" maxlength="4"> <input name="mbsys" type="text" size="4" maxlength="4"> <input type="Submit" name="mbbutton" value="Marktbeherrschung anwenden">';
			echo '</form>';
		}
		
		echo '</div>';
		rahmen_unten();
	}
}

?>
<?php include "fooban.php"; ?>
<script>
$(document).ready(function () {
$("td").tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: "&",
    extraClass: "design1", 
    fixPNG: true,
    opacity: 1.00,
    left: 0
});
});

function rh_calc(pos)
{
	uv=new Array(1,2,3,4,10000);
	
	if(pos==0)
	{
		target='#rh_cost';
		value=$("#rh_amount").val();
		rc1=($('#rh_v1 option:selected').val());
		rc2=($('#rh_v2 option:selected').val());
	}
	else 
	{
		target='#rh_amount';
		value=$("#rh_cost").val();
		rc2=($('#rh_v1 option:selected').val());
		rc1=($('#rh_v2 option:selected').val());
	}

	value=value*uv[rc1-1];
	value=value/uv[rc2-1];

	if(pos==0)
	{
		value=value*100/(100-p);
		value=Math.ceil(value);
	}
	else
	{
		value=value-(value/100*p);
		value=Math.floor(value);
	}
	
	$(target).val(value);
}

</script>
</body>
</html>
