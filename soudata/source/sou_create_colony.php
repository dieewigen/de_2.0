<?php
//pop transfermenge
//$poptransfer=1000000;

//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();

//sonnensysteme auslesen
//zuerst schauen ob der spieler sich in einem sonnensystem befindet
$searchx=$player_x;
$searchy=$player_y;
$db_daten=mysql_query("SELECT * FROM sou_map WHERE x='$searchx' AND y='$searchy'",$soudb);
$num = mysql_num_rows($db_daten);
if($num==1){
	//die id des sonnensystems auslesen
	$row = mysql_fetch_array($db_daten);

	$owner_id=$row["id"];
	$owner_sysname=$row["sysname"];
	$fraction=$row["fraction"];
	$pirates=$row['pirates'];

	//test auf piraten
	if($pirates>0){
		//stufe der piraten bestimmen
		// die entfernung zum zentrum berechnen
		$radius=sqrt(($player_x*$player_x)+($player_y*$player_y));
		if($radius>1250){
		  $pirateslevel=0;
		}else{
			$pirateslevel=51-ceil($radius/50*2);
		}

		if($pirateslevel>0){
			rahmen2_oben();
			echo 'Dieses Sonnensystem wird von Piraten belagert. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
			rahmen2_unten();
			echo '<br>';

			echo '</div>';//center-div
			die('</body></html>');
		}
	}
  
	//überprüfen ob es bereits bewohnt ist, indem man feststellt ob es bereits einer fraktion gehört
	if($fraction==0){
	  
		//überprüfen ob der nachbarsektor einen bau ermöglicht
		$sbx=round($player_x/15);
		$sby=round($player_y/15);
		if(get_sector_owner($sbx, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==$player_fraction OR get_sector_owner($sbx+1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby-1)==$player_fraction OR get_sector_owner($sbx-1, $sby)==$player_fraction OR get_sector_owner($sbx, $sby+1)==999 OR get_sector_owner($sbx+1, $sby)==999 OR get_sector_owner($sbx, $sby-1)==999 OR get_sector_owner($sbx-1, $sby)==999){
		  //überprüfen ob die passende technologie vorhanden ist
		  $search='f'.$player_fraction.'lvl';
		  $db_daten=mysql_query("SELECT * FROM `sou_frac_techs` WHERE $search>0 AND tech_id=60001",$soudb);
		  $num = mysql_num_rows($db_daten);
		  if($num==1){ //es gibt einen datensatz
			$hasall=1;
			echo '<br>';
			$output='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
			<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
			<td><b>Kolonisation von '.$owner_sysname.'</b></td>
			<td width="120">&nbsp;</td>
			</tr></table>';
			rahmen1_oben($output);
			echo '<div align="left">';
			//die kosten für die besiedlung berechnen
			$sbx=round($player_x/15);
			$sby=round($player_y/15);

			//berechnen wie teuer die expedition ist
			$ekosten = array (244400,306802,359255,417052,600925,688646,785306,1070179,1211014,1366201,1793402,2013231,2577670,2882713,3621196,4037873,4996676,5558812,6796051,7546840,9135420,10974045,12161167,14505352,17204583,19037192,22460312,26385887,30880233,36017965,41882995,48569662,56183973,64845010,74686495, 85858527,98529544,117069547,133759663,157718804,179566008,210410382,245680212,278430892,323593544,375070444,433678404,500335139,588328184,675555314);

			//entfernung zum nächsten nullpunkt berechnen
			$s1=$sv_sou_galcenter[0][0]-$sbx*15;
			$s2=$sv_sou_galcenter[0][1]-$sby*15;
			if($s1<0)$s1=$s1*(-1);
			if($s2<0)$s2=$s2*(-1);
			$s1=pow($s1,2);
			$s2=pow($s2,2);
			$w1=$s1+$s2;
			$entfernung=sqrt($w1);

			//die kosten in abhängigkeit zur entfernung berechnen
			$teiler=$sv_sou_galcenter[0][2]/50;

			$kostenstelle=round($entfernung/$teiler);
			if($kostenstelle>49)$kostenstelle=49;
			$kostenstelle=49-$kostenstelle;

			$kosten=round($ekosten[$kostenstelle]/2);
   
			echo '<br>Die Kosten für die Kolonisation werden aus der Fraktionskasse gedeckt.';
			echo '<br>Kolonisationskosten: '.number_format($kosten, 0,"",".").' Zastari';
			//fraktionskasse auslesen
			$feldname='f'.$player_fraction.'money';
			$db_daten=mysql_query("SELECT $feldname AS wert FROM `sou_system`",$soudb);
			$row = mysql_fetch_array($db_daten);
			if($kosten>$row["wert"]){$str1='<font color="#FF000">';$str2='</font>';$hasall=0;}else{$str1='';$str2='';}
			echo '<br>Fraktionskasse: '.$str1.number_format($row["wert"], 0,"",".").$str2.' Zastari<br><br>';
			echo '</div>';
			if($hasall==1)echo '<a href="sou_main.php?action=createcolonypage&do=1"><div class="b1">Start</div></a><br>';
			else echo 'Es sind nicht alle Voraussetzungen erf&uuml;llt.<br><br>';

			//überprüfen ob man kolonisieren will
			if($hasall==1 AND $_REQUEST["do"]==1){
				//ansehen und systemwert setzen und die kolonie der richtigen fraktion zuweisen
				$feldname='prestige'.$player_fraction;
				//in der DB den neuen Inhaber hinterlegen
				$result = mysql_query("UPDATE sou_map SET $feldname='$kosten', fraction='$player_fraction' WHERE id='$owner_id' AND fraction=0", $soudb);
				$num = mysql_affected_rows();
				if($num==1){
					//bei der Kolonisation werden automatisch Konstruktionszentrum und Hyperraumaufrissprojektor Stufe 1 geschenkt
					mysql_query("INSERT INTO sou_map_buildings (owner_id, bldg_id, level) VALUES ('$owner_id', 0, 1)",$soudb);
					mysql_query("INSERT INTO sou_map_buildings (owner_id, bldg_id, level) VALUES ('$owner_id', 13, 1)",$soudb);
					
					//zastari in der fraktionskasse abziehen
					$feldname='f'.$player_fraction.'money';
					mysql_query("UPDATE `sou_system` SET $feldname=$feldname-'$kosten'",$soudb);		

					//meldung für den chat hinzufügen
					$text='<font color="#00FF00">Fraktion '.$_SESSION["sou_fraction"].' hat '.$owner_sysname.' ('.$player_x.':'.$player_y.') kolonisiert.</font>';
					$time=time();
					insert_chat_msg('^Der Reporter^', $text, 0, 0);

					//meldung in der fractionsnews hinterlegen
					$text='Neue Kolonie: '.$owner_sysname;
					mysql_query("INSERT INTO sou_frac_news (year, fraction, typ, message) VALUES ((SELECT year FROM sou_system), '$player_fraction',3, '$text')",$soudb);

					//seite neu laden
					header("Location: sou_main.php");
				}
			}
			rahmen1_unten();
		  }
		  else echo '<br>Folgende Technologie wird ben&ouml;tigt: Kolonisation <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
		}
		else echo 'Du ben&ouml;tigst einen Nachbar-/Sektor deiner Fraktion, oder einen der ERBAUER. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
	  }
	  else echo '<br>Dieses Sonnensystem ist bereits bewohnt. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';
}
else echo '<br>Hier gibt es kein Sonnensystem. <a href="sou_main.php?action=systempage"><div class="b1">System</div></a>';

echo '<br>';

rahmen0_unten();

echo '<br>';

echo '</div>';//center-div

die('</body></html>');
?>