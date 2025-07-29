<?php
function create_bknachricht(){
	global $kt_lang, $sumatter, $ueatter, $sumdeffer, $uedeffer, $eigeninsg, $eigenue, $zsec, $astr, $dstr, $sec_id, $stolestr, $db, $sv_maxsystem;

	//kampfberichte anfertigen
	$nachricht="-- ".$kt_lang['sektorkampfbericht']." --<br>".$kt_lang['angreifer'].": $astr<br>".$kt_lang['verteidiger'].": $dstr<br>$stolestr<CENTER>
	<TABLE cellSpacing=0 cellPadding=2 width=400 border=1>
	<TBODY>
	<TR align=\"center\">
	<TD class=\"k1\" width=\"14%\"><b>$zsec</font></b></TD>
	<TD class=\"k1\" width=\"28%\" colSpan=2><u>".$kt_lang['angreifer']."</u></TD>
	<TD class=\"k1\" width=\"28%\" colSpan=2><u>".$kt_lang['verteidiger']."</u></TD>
	<TD class=\"k1\" width=\"30%\" colSpan=2><u>".$kt_lang['eigene']."</u></font></TD>
	</TR>

	<TR align=\"center\">
	<TD class=\"k2\" width=\"14%\">".$kt_lang['einheit']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['eingesetzt']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['ueberlebt']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['eingesetzt']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['ueberlebt']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['eingesetzt']."</TD>
	<TD class=\"k2\" width=\"14%\">".$kt_lang['ueberlebt']."</TD>
	</TR>

	<TR align=\"left\">
	<TD class=\"k1\" width=\"14%\"><u>".$kt_lang['sektorschiffe']."</u></FONT></TD>
	<TD class=\"k1\" width=\"14%\">$sumatter</TD>
	<TD class=\"k1\" width=\"14%\">$ueatter</TD>
	<TD class=\"k1\" width=\"14%\">$sumdeffer</TD>
	<TD class=\"k1\" width=\"14%\">$uedeffer</TD>
	<TD class=\"k1\" width=\"14%\">$eigeninsg</TD>
	<TD class=\"k1\" width=\"15%\">$eigenue</TD>
	</TBODY></TABLE>&nbsp;</CENTER>";

	//an den bk ne info schicken
	///zuerst schauen wer bk ist
	$bk=getSKSystemBySecID($sec_id);
  
	if ($bk>0){ //bk vorhanden, dann dessen daten raussuchen und nachricht einfügen
	  	$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND `system`=?", [$sec_id, $bk]);
    	$anz = mysqli_num_rows($db_daten);
	  	if ($anz>0){//bk-system ist auch besetzt
			$row = mysqli_fetch_array($db_daten);
			$uid = $row[0];
			$time=strftime("%Y%m%d%H%M%S");
			mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 56, ?, ?)", [$uid, $time, $nachricht]);
			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET newnews = 1 WHERE user_id = ?", [$uid]);
	  	}
	}
}

$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT zielsec FROM de_sector WHERE aktion = 1 AND zeit = 1 ORDER BY zielsec", []);

$num = mysqli_num_rows($res);
//echo '<br>'.$num.' Kampfsysteme<br>';
$z=0;$oldsec=0; $oldsys=0;
$kampfsec=array();
$all_rows = [];
while($row = mysqli_fetch_array($res)) {
    $all_rows[] = $row;
}
for ($i=0; $i<$num; $i++){ //kampfsysteme auslesen und gleich verdichten
	$zielsec = $all_rows[$i]['zielsec'];
	if ($oldsec<>$zielsec)
	{
		$kampfsec[$z]=$zielsec;
		//echo $zielsec.'<br>';
		$z++;
	}
	$oldsec=$zielsec;
}
//kampsysteme wurden ermittelt

//jetzt f�r jedes system die flotten auslesen und sie k�mpfen lassen
$num = count($kampfsec);
//echo '<br>insg:'.$num.'<br>';
for ($c=0; $c<$num; $c++){
	//echo '<br>'.$c.'<br><br>';
	$zsec=$kampfsec[$c];
	$dstr=$kampfsec[$c];

	//angreifer laden
	$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, e2 FROM de_sector WHERE zielsec = ? AND aktion = 1 AND zeit = 1 ORDER BY sec_id", [$zsec]);
	$z=0;
	while($row = mysqli_fetch_array($res)){//f�r jede flotte die daten auslesen
		$asecid[$z] = $row["sec_id"];
		$atter[$z][0] = $row["e2"];
		$z++;
	}
	$z=0;
	
	$sumdeffer=0;
	$sumatter=0;


	// Flotten des Angegriffenen Laden

	$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, e1, e2, aktion, zeit FROM de_sector WHERE sec_id = ?", [$zsec]);
	$row = mysqli_fetch_array($res);//heimatflotte auslesen
	$dsecid[$z] = $row["sec_id"];
	$deffer[$z][0] = $row["e1"];
	$z++;
	if ($row["aktion"] == 0 OR ($row["aktion"] == 3 AND $row["zeit"] == 1)){
		echo 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
		$dsecid[$z] = $row["sec_id"];
		$deffer[$z][0] = $row["e2"];
		$z++;
	}


	//verteidigerhilfsflotten laden
	$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, e2 FROM de_sector WHERE zielsec = ?
	AND aktion = 2 AND (zeit = 1 OR (zeit = 0 AND aktzeit > 0))", [$zsec]);


	while($row = mysqli_fetch_array($res)){//f�r jede flotte die daten auslesen
		$dsecid[$z] = $row["sec_id"];
		$deffer[$z][0] = $row["e2"];
		$dstr=$dstr.', '.$dsecid[$z];
		$z++;
	}

	$anzatter=count($atter);
	$astr='';$maxfleet=0;
	for ($i=0; $i<$anzatter; $i++){
		//angreifer addieren
		$sumatter=$sumatter+$atter[$i][0];
		$astr=$astr.$asecid[$i];
		if ($i<$anzatter-1)$astr=$astr.', ';
		//st�rksten angreifer speichern
		if ($atter[$i][0]>$maxfleet){
			$artziel=$asecid[$i];
			$maxfleet=$atter[$i][0];
		}
		//echo 'Angreifer '.$i.': sec_id='.$asecid[$i].' - schiffe='.$atter[$i][0].'<br>';
	}

	$anzdeffer=count($deffer);
	for ($i=0; $i<$anzdeffer; $i++){
		//verteidiger addieren
		$sumdeffer=$sumdeffer+$deffer[$i][0];
		//echo 'Verteidiger '.$i.': sec_id='.$dsecid[$i].' - schiffe='.$deffer[$i][0].'<br>';
	}
	//echo '<br> '.$sumatter.' Angreifer gegen '.$sumdeffer.' Verteidiger. <br>';

	if ($sumdeffer>0){
		$klauw=$sumatter / $sumdeffer * 100;
	}else{
		$klauw=100;
	}

	//echo 'Klau-W: '.(int)$klauw.'<br>';

	$r=rand (1, 100);
	$stolestr='';
	if ($r<=$klauw){ //ein artefakt wird mitgenommen
	//schauen ob es artefakte gibt
	$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT id FROM de_artefakt WHERE sector=? AND (id<'11' OR id >'20')", [$zsec]);
	$artnum = mysqli_num_rows($res);
	if ($artnum>0){//artefakt vorhanden
		//schauen welches transferiert wird
		$r=rand (1, $artnum);
		$all_art_rows = [];
		while($art_row = mysqli_fetch_array($res)) {
		    $all_art_rows[] = $art_row;
		}
		$id = $all_art_rows[$r-1]['id'];
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_artefakt SET sector=? WHERE id=?", [$artziel, $id]);
		//artefaktnamen laden
		$db_daten_artefakt = mysqli_execute_query($GLOBALS['dbi'], "SELECT artname FROM de_artefakt WHERE id=?", [$id]);
		$rowartefakt = mysqli_fetch_array($db_daten_artefakt);
		//meldung über das eroberte artefakt
		$stolestr=$kt_lang['sektor'].' '.$artziel.' '.$kt_lang['konnteartefakterobern'].$rowartefakt["artname"].'<br>';
		//echo 'AAA'.$artziel.'BBB';
		
		//artefaktumzug in der db hinterlegen
		//typ: 0 = hypersturm, 1 = angriff
		$text=$id.';'.$zsec.';'.$artziel;
		mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_server(wt, typ, text) VALUES (?, '1', ?)", [$maxtick, $text]);
	}
	}

	$destroyedatter=$sumdeffer / 4;
	$destroyedatter=(int)$destroyedatter;
	if ($destroyedatter>$sumatter)$destroyedatter=$sumatter;

	$destroyeddeffer=$sumatter / 4;
	$destroyeddeffer=(int)$destroyeddeffer;
	if ($destroyeddeffer>$sumdeffer)$destroyeddeffer=$sumdeffer;

	//prozentualen verlust der angreifer berechnen
	for ($i=0; $i<$anzatter; $i++){
		if ($sumatter>0){
			$atter[$i][1]=$atter[$i][0] / $sumatter;
			$atter[$i][1]=$destroyedatter * $atter[$i][1];
			$atter[$i][1]=(int)$atter[$i][1];
			$vertdestroyedatter=$vertdestroyedatter+$atter[$i][1];
		}
	}

	//prozentualen verlust der verteidiger berechnen
	for ($i=0; $i<$anzdeffer; $i++){
		if ($sumdeffer>0){
			$deffer[$i][1]=$deffer[$i][0] / $sumdeffer;
			$deffer[$i][1]=$destroyeddeffer * $deffer[$i][1];
			$deffer[$i][1]=(int)$deffer[$i][1];
			$vertdestroyeddeffer=$vertdestroyeddeffer+$deffer[$i][1];
		}
	}

	/*
	//schauen ob noch einheiten wegen rundungsfehlern vergessen worden sind
	$vergdesatter  = $destroyedatter-$vertdestroyedatter;
	$vergdesdeffer = $destroyeddeffer-$vertdestroyeddeffer;

	//vergessene einheiten verteilen
	while ($vergdesatter>0)
	{
	for ($i=0; $i<$anzatter; $i++)
	{
		if ($vergdesatter>0 AND $atter[$i][1]>0)
		{
		$atter[$i][1]=$atter[$i][1]+1;
		$vergdesatter=$vergdesatter-1;
		}
	}
	}
	echo ' gna '.$vergdesdeffer.':'.$zsec.' gna ';
	while ($vergdesdeffer>0)
	{
	for ($i=0; $i<$anzdeffer; $i++)
	{
		if ($vergdesdeffer>0 AND $deffer[$i][1]>0)
		{
		$deffer[$i][1]=$deffer[$i][1]+1;
		$vergdesdeffer=$vergdesdeffer-1;
		}
	}
	}
	*/
	/*for ($i=0; $i<$anzdeffer; $i++)
	{
	echo 'Defferverluste: '.$deffer[$i][1].'<br>';
	}

	for ($i=0; $i<$anzatter; $i++)
	{
	echo 'Atterverluste: '.$atter[$i][1].'<br>';
	}*/

	$ueatter=$sumatter-$destroyedatter;
	$uedeffer=$sumdeffer-$destroyeddeffer;

	//nachrichten an die bks senden
	//zuerst die angreifer
	if ($sumatter>0)//nur kampfbericht ausgeben, wenn angreifer da sind
	for ($i=0; $i<$anzatter; $i++){
		$eigeninsg=$atter[$i][0];
		$eigenue=$eigeninsg-$atter[$i][1];
		$subeinheiten=$atter[$i][1];
		$sec_id=$asecid[$i];
		create_bknachricht();
		//flotte updaten
		mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET e2 = e2 - ? WHERE sec_id = ?", [$subeinheiten, $sec_id]);

		//falls keine schiffe �berlebt haben direkt flotte nach hause
		if ($eigenue==0){
			mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET aktion = 0, zeit = 0, aktzeit = 0, zielsec = 0, aktzeit = 0, gesrzeit=0 WHERE sec_id = ?", [$asecid[$i]]);
		}
	}

	//danach die deffer
	if ($sumatter>0){//nur kampfbericht ausgeben, wenn angreifer da sind
		for ($i=0; $i<$anzdeffer; $i++){
			//evtl. wachflotte und sektorflotte verdichten
			$sec_id=$dsecid[$i];
			if ($i==0 AND $dsecid[0]==$dsecid[1]){//beide gleicher sektor, d.h. man mu� verdichten
				$eigeninsg=$deffer[$i][0]+$deffer[$i+1][0];
				$eigenue=$eigeninsg-$deffer[$i][1]-$deffer[$i+1][1];
				$subeinheiten1=$deffer[$i][1];
				$subeinheiten2=$deffer[$i+1][1];

				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET e1 = e1 - ?, e2 = e2 - ? WHERE sec_id = ?", [intval($subeinheiten1), intval($subeinheiten2), $sec_id]);
				$i++;
				create_bknachricht();
			}else{
				$eigeninsg=$deffer[$i][0];
				$eigenue=$eigeninsg-$deffer[$i][1];
				$subeinheiten=$deffer[$i][1];
				create_bknachricht();
				if ($sec_id==$zsec)//kann nur die wachflotte sein, daher auch von dort abziehen
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET e1 = e1 - ? WHERE sec_id = ?", [intval($subeinheiten), $sec_id]);
				else
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET e2 = e2 - ? WHERE sec_id = ?", [intval($subeinheiten), $sec_id]);

				if ($eigenue==0 AND $sec_id!=$zsec)//falls keine schiffe überlebt haben direkt flotte nach hause
				mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET aktion = 0, zeit = 0, aktzeit = 0,
				zielsec = 0, aktzeit = 0, gesrzeit=0 WHERE sec_id = ?", [$dsecid[$i]]);
			}
		}
	}
	unset($asecid);
	unset($dsecid);
	unset($atter);
	unset($deffer);

}
?>
