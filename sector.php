<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_sector.lang.php';
include "functions.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, col, sector, `system`, newtrans, newnews, allytag, status, hide_secpics, platz, rang, secsort, secstatdisable FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
$restyp05=$row["restyp05"];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];$techs=$row["techs"];
$platz=$row["platz"];$erang_nr=$row["rang"];$secsort=$row["secsort"];$secstatdisable=$row["secstatdisable"];
$col=$row['col'];

$ownsector=$sector;$ownsystem=$system;
$hide_secpics=$row["hide_secpics"];

$secrelcounter=0;

if ($row["status"]==1){
	$ownally = $row["allytag"];
}else{
	$ownally='';
}

//schauen ob er die whg hat und dann die attgrenze anpassen
if ($techs[4]==0)$sv_attgrenze_whg_bonus=0;

//owner id auslesen
$sql = "SELECT owner_id FROM de_login WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$owner_id=intval($row["owner_id"]);

if(isset($_REQUEST["sso"])){
	$sso=intval($_REQUEST["sso"]);
	$sso--;
	$sql = "UPDATE de_user_data SET secsort=? WHERE user_id=?";
	mysqli_execute_query($GLOBALS['dbi'], $sql, [$sso, $_SESSION['ums_user_id']]);
	$secsort=$sso;
}

//spieler sortiert auslesen    
$orderby='`system`';
if($secsort=='1')$orderby='col';
elseif($secsort=='2')$orderby='score';

//maximale anzahl von kollektoren auslesen
$sql = "SELECT MAX(col) AS maxcol FROM de_user_data WHERE npc=0";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql);
$row = mysqli_fetch_assoc($db_daten);
$maxcol=$row['maxcol'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Sektor</title>
<?php include "cssinclude.php";
echo '
</head>';
if($_SESSION['ums_mobi']==1){
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
}else{
	echo '<body onload="javascript:document.secform.sf.focus();document.secform.sf.select();" class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<div align="center">';

function wellenrechner($kol, $maxcol, $npcsec){
	global $sec_lang, $col, $sv_min_col_attgrenze, $sv_max_col_attgrenze, $sv_kollie_klaurate;
	$str='Kollektoren-Wellenrechner';
	$str.="<table width=200px border=0 cellpadding=0 cellspacing=1><tr align=center><td width=15%>&nbsp</td><td width=17%>".$sec_lang['kollektoren']."</td></tr>";
	$str.="<tr align=center><td>&nbsp;</td><td>".number_format($kol, 0, ',' ,'.')."</td></tr>";

	$owncol=$col;
	if($maxcol==0)$maxcol=1;
	for($we=0; $we<5; $we++){
		if($owncol>$maxcol)$maxcol=$owncol;
	
		if($npcsec==0){
			$col_angriffsgrenze=$owncol*100/$maxcol;
			$col_angriffsgrenze=$col_angriffsgrenze/100*$sv_max_col_attgrenze;
			if($col_angriffsgrenze>$sv_max_col_attgrenze){
				$col_angriffsgrenze=$sv_max_col_attgrenze;
			}
			if($col_angriffsgrenze<$sv_min_col_attgrenze){
				$col_angriffsgrenze=$sv_min_col_attgrenze;
			}
		}else{
			$col_angriffsgrenze=$sv_min_col_attgrenze;
		}
		
		if ($kol<$col_angriffsgrenze*$owncol){$colclass="text2";}else{$colclass="text3";}
		
		$str.="<tr align=center><td nowrap>".($we+1).". ".$sec_lang['welle']."</td><td class=".$colclass.">".
		number_format(floor($kol*$sv_kollie_klaurate), 0, ',','.')."</td></tr>";
		$owncol=$owncol+floor($kol*$sv_kollie_klaurate);
		$kol=$kol-floor($kol*$sv_kollie_klaurate);
	}
		

	//info bzgl. erobern/zerst�ren von kollektoren
	$str.="</table><br>Gr&uuml;ner Kollektorenwert: Kollektoren liegen &uuml;ber der Kollektorenangriffsgrenze und werden erobert.<br><br>Roter Kollektorenwert: Kollektoren liegen unter der Kollektorenangriffsgrenze und werden zerst&ouml;rt.";

	return ($str);
}

if(isset($_REQUEST['sf'])){
	$sf=intval($_REQUEST['sf']);
}else{
	$sf=$sector;
}

if ($sf>$sv_show_maxsector or $sf<1){
	$sf=$ownsector;
}
$sector=$sf;

//den button fürs blättern durch die sektoren darstellen
echo '<form action="sector.php" name="secform" method="POST">';

echo '<table><tr align="center"><td>';

if($_SESSION['ums_mobi']==1){
	echo '<a style="float:left;" href="sector.php?sf='.($sf-1).'"><div class="mobilebtn" style="float:left; margin-bottom: 5px; margin-left: 10px; width: 80px; margin-top: 5px;">&lt;</div></a>';
	echo '<input class="mobilebtn" style="float:left; margin-bottom: 5px; margin-left: 10px; width: 80px; margin-top: 5px;" type="text" name="sf" value="'.$sf.'" size="4" maxlength="5" autocomplete="off">';
	echo '<a class="mobilebtn" style="float:left; margin-bottom: 5px; margin-left: 10px; width: 80px; margin-top: 5px;" href="javascript:document.secform.submit();">OK</a>';
	echo '<a style="float:left;" href="sector.php?sf='.($sf+1).'"><div class="mobilebtn" style="float:left; margin-bottom: 5px; margin-left: 10px; width: 80px; margin-top: 5px;">&gt;</div></a>';
}else{
	echo '<a href="sector.php?sf='.($sf-2).'" class="secbutton1"></a><a href="sector.php?sf='.($sf-1).'" class="secbutton2">&nbsp;</a><span class="secbutton3"><input type="text" name="sf" value="'.$sf.'" size="4" maxlength="5" class="secbutton3" autocomplete="off"></span><a href="javascript:document.secform.submit();" class="secbutton4"></a><a href="sector.php?sf='.($sf+1).'" class="secbutton5"></a><a href="sector.php?sf='.($sf+2).'" class="secbutton6"></a>';	
}
echo '</td></tr></table>';
echo '</form><br>';

//schauen ob nah oder fern
$rzadd=0;
if ($ownsector<>$sf){
	$rzadd=2;
}

//die daten des sektors auslesen
$sql = "SELECT * FROM de_sector WHERE sec_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
$sec_data = mysqli_fetch_assoc($db_daten);

//Hinweis in Sektor 666
if($sf==666){
    //hinweistext für npc-sektoren
	echo '
	<div class="cell" style="display: flex; width: 597px; border: 1px solid #333333; padding: 4px; margin-bottom: 20px;">
    	<div style="background-color: #000000; width: 50px; height: 50px;">
			<img src="'.'gp/'.'g/symbol12.png" border="0" title="Info">
		</div>
    
		<div style="flex-grow: 1; padding: 10px;">
			In Sektor 666 regten sich vor Äonen die größten Übel der Zeit. Heute schlafen sie und man kann nur hoffen, dass sie nie wieder erwachen. 
		</div>
    </div>';
}


if($sec_data['npc']==1){
	////////////////////////////////////////////////////////////////////////	
	//npc anzeigen
	////////////////////////////////////////////////////////////////////////
	
	//kein malus bei aliens
  	$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus;
  	$col_angriffsgrenze_final=$sv_min_col_attgrenze;
  	$rec_bonus=0;
  	
	//sektoransicht darstellen
	//reisezeit
	if($rzadd==0){
		$style='border: 1px solid #444444; background-color: #00DD00; color: #000000; width: 16px; display: inline-block; text-align: center;';
	}else{
		$style='border: 1px solid #444444; background-color: #f05a00; color: #000000; width: 16px; display: inline-block; text-align: center;';
	}
	
	$sektorinfo='<span title="Reisezeitmalus&Eigener Sektor: kein Malus<br>Anderer Sektor: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
	$sektorinfo.='';
	//rahmen
	rahmen_oben('<div style="text-align: left">'.$sektorinfo.' '.$sec_data['name'].'</div>');
    //tabellen�berschrift
	echo '<table border="0" cellpadding="0" cellspacing="1" width="100%">
	<tr>
	<td width="50" class="cell tac"><a href="sector.php?sso=1&sf='.$sf.'"><font size="1">'.$sec_lang['sys'].'</font></a></td>
	<td width="246" class="cell tac"><font size="1">'.$sec_lang['name'].'</font></td>
	<td width="150" class="cell tac"><a href="sector.php?sso=3&sf='.$sf.'"><font size="1">'.$sec_lang['punkte'].'</font></a></td>
	<td width="60" class="cell tac"><a href="sector.php?sso=2&sf='.$sf.'"><font size="1">'.$sec_lang['kollektoren'].'</font></a></td>
	<td width="70" class="cell tac"><font size="1">'.$sec_lang['aktion'].'</font></td>
	</tr>';
	$sql = "SELECT * FROM de_user_data WHERE sector=? ORDER BY $orderby ASC LIMIT 300";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
	$anz = mysqli_num_rows($db_daten);
	while($row = mysqli_fetch_assoc($db_daten)){
		echo '<tr>';
		//system
		echo '<td class="cell tac" style="font-size: 10pt;">'.$row['system'].'</td>';
		//spielername
		echo '<td class="cell tac" style="font-size: 10pt;">'.$row['spielername'].'</td>';
		//punkte
		if ($punkte*$sec_angriffsgrenze<=$row['score']) $csstag=' text3'; else $csstag=' text2';
		$tooltip='Gr&uuml;ner Punktewert: Ziel ist angreifbar, da oberhalb der Punkteangriffsgrenze.<br><br>Roter Punktewert: Ziel ist nicht angreifbar, da unterhalb der Punkteangriffsgrenze.';
		echo '<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['score'], 0,"",".").'</div></td>';
		
		//kollektoren
		if ($col*$col_angriffsgrenze_final<=$row['col']) $csstag=' text3'; else $csstag=' text2';
		$tooltip=wellenrechner($row['col'], $maxcol, 1);
		echo '<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['col'], 0,"",".").'</div></td>';
		
		//aktion
		echo '<td class="cell tac"><a href="military.php?se='.$row['sector'].'&sy='.$row['system'].'">F</a></td>';
	
		echo '</tr>';
	}
	
	//sektorendaten
	echo '<tr><td colspan="5" class="cell" height="24px">&nbsp;'.$sec_lang['angriffsgrenze'].': '.number_format($sec_angriffsgrenze*100, 2,",",".").'% / '.number_format($col_angriffsgrenze_final*100, 2,",",".").'%</td></tr>';
	
	echo '</table>';
	rahmen_unten();
	
    //hinweistext für npc-sektoren
	echo '<div class="cell" style="position: relative; width: 590px; border: 1px solid #333333; padding: 4px;">';
    echo '<div style="background-color: #000000; width: 50px; height: 50px; position: relative; float: left;">
      <img src="'.'gp/'.'g/symbol12.png" border="0" title="Info">
    </div>';    
    
    echo '<div style="width: 540px; height: 70px; position: relative;">'.
    $sec_lang['npsecinfo1'].' '.$sec_lang['npsecinfo2'].get_free_artefact_places($_SESSION['ums_user_id']).'<br>'.$sec_lang['npsecinfo3'].
    '<br>'.$sec_lang['npsecinfo4'].'
    </div>';    
    echo '</div><br>';
	
}else{
	////////////////////////////////////////////////////////////////////////	
	//spieler anzeigen
	////////////////////////////////////////////////////////////////////////
	//sektorkommandant feststellen
	$sksystem=issectorcommander();
	
	//die scandaten des spielers auslesen
	unset($scandaten);
    $sql = "SELECT zuser_id, rasse, allytag, ps FROM de_user_scan WHERE user_id=?";
    $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
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
	$sql = "SELECT id FROM de_allys WHERE allytag=?";
	$allyresult = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ownally]);
	$at=mysqli_num_rows($allyresult);
	if ($at!=0)
	{
	  $allyrow = mysqli_fetch_assoc($allyresult);
	  $allyid = $allyrow["id"];
	
	  $sql = "SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=? or ally_id_2=?) and (ally_id_1=id or ally_id_2=id)";
	  $allyresult = mysqli_execute_query($GLOBALS['dbi'], $sql, [$allyid, $allyid]);
	  while($row = mysqli_fetch_assoc($allyresult))
	  {
	        if ($ownally != $row["allytag"])
	                $allypartner[] = $row["allytag"];
	  }
	
	  $sql = "SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=? or ally_id_angegriffener=?) and (ally_id_angreifer=id or ally_id_angegriffener=id)";
	  $allyresult = mysqli_execute_query($GLOBALS['dbi'], $sql, [$allyid, $allyid]);
	  while($row = mysqli_fetch_assoc($allyresult))
	  {
	        if ($ownally != $row["allytag"])
	                $allyfeinde[] = $row["allytag"];
	  }
	}
	
	//sektormalus bei der attgrenze berechnen
	//zuerst anzahl der pc-sektoren auslesen
	$sql = "SELECT sec_id FROM de_sector WHERE npc=0 AND platz>1";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql);
	$num = mysqli_num_rows($db_daten);
	if($num<1)$num=1;
	
	//eigenen sektorplatz auslesen
	$sql = "SELECT platz FROM de_sector WHERE sec_id=?";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ownsector]);
	$row = mysqli_fetch_assoc($db_daten);
	$ownsectorplatz=$row["platz"];
	  
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
	if($secstatdisable==1 AND $ownsector==$sf) echo '<div class="info_box text2">'.$sec_lang['secstatdisable'].'</div><br>';
	
	$gesamtpunkte=0;$anz=0;
	$output='<table border="0" cellpadding="0" cellspacing="1" width="100%">
	<tr>
	<td width="36" class="cell tac"><a href="sector.php?sso=1&sf='.$sf.'"><font size="1">'.$sec_lang['sys'].'</font></a></td>
	<td width="32" class="cell tac"><font size="1">'.$sec_lang['rang'].'</font></td>
	<td width="199" class="cell tac"><font size="1">'.$sec_lang['name'].'</font></td>
	<td width="30" class="cell tac"><font size="1">'.$sec_lang['rasse'].'</font></td>
	<td width="55" class="cell tac"><font size="1">'.$sec_lang['allianz'].'</font></td>
	<td width="90" class="cell tac"><a href="sector.php?sso=3&sf='.$sf.'"><font size="1">'.$sec_lang['punkte'].'</font></a></td>
	<td width="60" class="cell tac"><a href="sector.php?sso=2&sf='.$sf.'"><font size="1">'.$sec_lang['kollektoren'].'</font></a></td>
	<td width="70" class="cell tac"><font size="1">'.$sec_lang['aktion'].'</font></td>
	</tr>';
	
	//die spielerdaten laden
	if($sf==1 && !isset($_REQUEST['showall'])){
		$sql = "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode, 
		de_login.last_login, de_login.last_click, de_login.user_id, de_user_data.score, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag, 
		de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.npc, 
		de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04 
		FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_login.status=1 AND de_user_data.sector=? ORDER BY $orderby ASC";
		$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
	}else{
		//alles anzeigen
		$sql = "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode, 
		de_login.last_login, de_login.last_click, de_login.user_id, de_user_data.score, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag, 
		de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.npc, 
		de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04 
		FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_user_data.sector=? ORDER BY $orderby ASC";
		$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
	}	
	
	$anz = mysqli_num_rows($db_daten);
	$gescol=0;
	while($row = mysqli_fetch_assoc($db_daten)){
		$gesamtpunkte+=$row['score'];
		$gescol+=$row['col'];
		
		$output.='<tr>';
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//system inkl sk/bk und accountstatus
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		$systemstr=$row['system'];
	    if ($row["lstatus"]==2)$systemstr='['.$systemstr.']';//gesperrt
	    if ($row["lstatus"]==3 AND $row["delmode"]>0) $systemstr='{'.$systemstr.'}';//l�schmode
	    if ($row["lstatus"]==3 AND $row["delmode"]==0) $systemstr='('.$systemstr.')';//umode
	    
	    if($sksystem==$row['system']){
	      	$systemstr='<span class="tc3">^'.$systemstr.'^</span>';
		}
		
		/*
	    if($row['system']==$sec_data['bk']){
			$systemstr='<span class="tc2">&deg;'.$systemstr.'&deg;</span>';
		}*/
		
		$output.='<td class="cell tac" style="font-size: 10pt;">'.$systemstr.'</td>';
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//rang
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		$rang='';
		if($row['npc']==0){
			$rang="<img src='".'gp/'.'g/r/'.$row['rang']."_g.gif' title='".$rangnamen1[$row['rang']]."'>";
		}
    	
		$output.='<td class="cell tac">'.$rang.'</td>';
		
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//spielername, Spielertitel, details, im sektor online
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
    	$playername=utf8_encode_fix(umlaut($row['spielername']));
    	if(strtotime($row["last_click"])+1800 > time() AND $row["lstatus"]==1) $os=' *';else $os='';
    	if ($ownsector==$sf AND $secstatdisable==0) $osown=$os;else $osown='';
    	$csstag='tc1';
		
		$userTitle='';
		$sql="SELECT * FROM ls_user_title LEFT JOIN ls_title ON (ls_user_title.title_id=ls_title.title_id) WHERE ls_user_title.user_id = '".$row['owner_id']."' ORDER BY ls_title.title ASC";
		$db_datenx=mysqli_query($GLOBALS['dbi_ls'], $sql);
		if(mysqli_num_rows($db_datenx) > 0){
			
			while ($rowx = mysqli_fetch_assoc($db_datenx)){
				$userTitle.=$rowx['title'].'<br>';
			}
			$userTitle='<div class="user_title" title="'.$userTitle.'"></div>';
		}

		$output.='<td class="cell tac" style="font-size: 10pt;"><a href="details.php?se='.$sector.'&sy='.$row['system'].'">
		<span class="'.$csstag.'">'.$playername.$osown.$userTitle.'</span></a></td>';
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//rasse
		////////////////////////////////////////////////////////////////////////
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
		
		
		if($knowrasse==1)
		{
			if($row['rasse']==1)$rasse='<img src="'.'gp/'.'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
			if($row['rasse']==2)$rasse='<img src="'.'gp/'.'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
			if($row['rasse']==3)$rasse='<img src="'.'gp/'.'g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
			if($row['rasse']==4)$rasse='<img src="'.'gp/'.'g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';
			if($row['rasse']==5)$rasse='<img src="'.'gp/'.'g/r/raceD.png" title="Z&#180;tah-ara" width="16px" height="16px">';
		}
		
		$output.='<td class="cell tac">'.$rasse.'</td>';
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//allianz, sichtbarkeit durch ally, allyb�ndnis, scandaten, sektor
		////////////////////////////////////////////////////////////////////////
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
	    if($showallytag=='') 
	    {
	    	$showallytag='&nbsp;';
	    } 
        
        if($showallytag!='&nbsp;'){
			$showallytag='<a href="ally_detail.php?allytag='.urlencode($showallytag).'"><span class="'.$csstag.'">'.$showallytag.'</span></a>';
		}

        $output.='<td class="cell tac" style="font-size: 10pt;">'.$showallytag.'</td>';
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//punkte
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		if ($punkte*$sec_angriffsgrenze<=$row['score']) $csstag=' text3'; else $csstag=' text2';
		
		$tooltip='Gr&uuml;ner Punktewert: Ziel ist angreifbar, da oberhalb der Punkteangriffsgrenze.<br><br>Roter Punktewert: Ziel ist nicht angreifbar, da unterhalb der Punkteangriffsgrenze.';
		
		//fett darstellen, wenn es Kopfgeld gibt
		if($sv_oscar!=1){
			if($row['kg01']>0 || $row['kg02']>0 || $row['kg03']>0 || $row['kg04']>0){
				$csstag.=' fett';
				$tooltip.='<br><br>Kopfgeld:';
				$tooltip.='<br>M: '.number_format($row['kg01'], 0,"",".");
				$tooltip.='<br>D: '.number_format($row['kg02'], 0,"",".");
				$tooltip.='<br>I: '.number_format($row['kg03'], 0,"",".");
				$tooltip.='<br>E: '.number_format($row['kg04'], 0,"",".");
			}
		}
		
		$output.='<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['score'], 0,"",".").'</div></td>';
		
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//kollektoren
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		if ($col*$col_angriffsgrenze_final<=$row['col']) $csstag=' text3'; else $csstag=' text2';
		$tooltip=wellenrechner($row['col'], $maxcol, 0);
		$output.='<td class="cell tac'.$csstag.'" style="text-align: right;"><div title="'.$tooltip.'">'.number_format($row['col'], 0,"",".").'</div></td>';
			
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		//aktion
		////////////////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////////////
		
		$aktion='<a href="secret.php?a=s&zsec1='.$sector.'&zsys1='.$row['system'].'" title="Sonde starten">S</a> <a href="secret.php?a=a&zsec2='.$sector.'&zsys2='.$row['system'].'" title="Agenteneinsatz">A</a> <a href="military.php?se='.$sector.'&sy='.$row['system'].'" title="Flotteneinsatz">F</a> ';
		$aktion.='<a href="secret.php?a=d&zsec1='.$sector.'&zsys1='.$row['system'].'"><img src="'.'gp/'.'g/ps_'.$playerstatus.'.gif" border="0" title="Geheimdienstinformationen"></a>';
		
		$output.='<td class="cell tac" style="font-size: 10pt;">'.$aktion.'</td>';
		
		$output.='</tr>';
	}
	
	
	$output.='</table>';
	

	//die sektor�berschrift zusammenbauen
	$sektorinfo='<div style="text-align: left;">';
	//reisezeit
	if($rzadd==0){
		$style='border: 1px solid #444444; background-color: #00DD00; color: #000000; width: 16px; display: inline-block; text-align: center;';
	}else{
		$style='border: 1px solid #444444; background-color: #f05a00; color: #000000; width: 16px; display: inline-block; text-align: center;';
	}
	
	$sektorinfo.='<span title="Reisezeitmalus&Eigener Sektor: kein Malus<br>Andere Sektoren: Reisezeit +2 Kampftick" style="'.$style.'">'.$rzadd.'</span>';
	$sektorinfo.=' <span title="Platz in der Sektorwertung">Platz: '.$sec_data['platz'].'</span>';
	$sektorinfo.=' Punkte: '.number_format($gesamtpunkte, 0,",",".");
	if($sec_data['name']!='')$sektorinfo.=' - '.$sec_data['name'];
	$sektorinfo.='</div>';
	
	//Sektor 1 soll einen festen Namen haben
	if($sf==1){
		$sektorinfo='Sektor 1 - Startsektor';
	}
	
	rahmen_oben($sektorinfo);
	
	echo $output;
	
	//rahmen_unten();
	
	////////////////////////////////////////////////////////////////////////
	// alter bereich
	////////////////////////////////////////////////////////////////////////

	//bild von der sternenbasis anzeigen
  $bed=$sec_data['techs'][1].$sec_data['techs'][2].$sec_data['techs'][3];
  $std=date("H");
  //1=sternenbasis 2=begrenzer 3=werft

  if ($bed=='100') {if ($std>6 && $std<20 )$bn='sbtag.gif'; else $bn='sbnacht.gif';}
  elseif ($bed=='110') {if ($std>6 && $std<20 )$bn='sbtagsfb.gif'; else $bn='sbnachtsfb.gif';}
  elseif ($bed=='101') {if ($std>6 && $std<20 )$bn='sbtagw.gif'; else $bn='sbnachtw.gif';}
  elseif ($bed=='111') {if ($std>6 && $std<20 )$bn='sbtagsfbw.gif'; else $bn='sbnachtsfbw.gif';}
  //artefakte anzeigen

  //schauen ob es artefakte gibt
  $sql = "SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector=?";
  $res = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
  $artnum = mysqli_num_rows($res);
  //if ($artnum>0 OR $bed!='000')//artefakt vorhanden, oder raumbasis gebaut
  //{
    
  $artstr='';
  $c=0;

  if($artnum>0)
  {
    include "inc/artefakt.inc.php";
  }
  	
  	
  while($row = mysqli_fetch_assoc($res))
  {
      //artefakttooltip bauen
	$desc=$row["artdesc"];
	$desc=str_replace("{WERT1}", number_format($sv_artefakt[$row["id"]-1][0], 2,",",".") ,$desc);
	$desc=str_replace("{WERT2}", number_format($sv_artefakt[$row["id"]-1][1], 0,"",".") ,$desc);
	$desc=str_replace("{WERT3}", number_format($sv_artefakt[$row["id"]-1][2], 0,"",".") ,$desc);
	$desc=str_replace("{WERT4}", number_format($sv_artefakt[$row["id"]-1][3], 0,"",".") ,$desc);
	$desc=str_replace("{WERT5}", number_format($sv_artefakt[$row["id"]-1][4], 0,"",".") ,$desc);
	$desc=str_replace("{WERT6}", number_format($sv_artefakt[$row["id"]-1][5], 2,",",".") ,$desc);
      
      
    $atip[$c] = '<font color=#'.$row["color"].'>'.$row["artname"].'</font>&'.$desc;
      
    $artstr.='<a href="help.php?a=1" target="_blank" title="'.$atip[$c].'"><img src="'.'gp/'.'g/sa'.$row["picid"].'.gif" border="0"></a>&nbsp;';
    $c++;
  }
  if($artstr=='')$artstr='&nbsp;';
  
  //sektorraumbasistooltip erzeugen
  $basestr='';
  if($bed!='000'){
  	$srbstr=$sec_lang['sekerweiterungen'].':';
  	if($sec_data['techs'][2]>0)$srbstr.='<br>- '.$sec_lang['sekbldg1'];
  	if($sec_data['techs'][3]>0)$srbstr.='<br>- '.$sec_lang['sekbldg2'];
  	if($sec_data['techs'][4]>0)$srbstr.='<br>- '.$sec_lang['sekbldg3'];
  	if($sec_data['techs'][5]>0)$srbstr.='<br>- '.$sec_lang['sekbldg4'];
  	
  	$stip = $sec_lang['sektorraumbasis'].'&'.$srbstr;
    $basestr='<a href="'.'gp/'.'g/big/'.strtoupper($bn).'" target="_blank"><img border="0" src="'.'gp/'.'g/'.$bn.'" name="sb" title="'.$stip.'"></a>';
    //wenn es keine sektorraumbasis gibt string mit einem leerzeichen belegen
    if($bed=='000')$basestr='&nbsp;';
   }
  
	//infostring zusammenbauen
	if($sector>1 AND $anz>0){  
		if($sector==1)$sec_angriffsgrenze='-';
		else $sec_angriffsgrenze=number_format($sec_angriffsgrenze*100, 2,",",".").'%';
		if($sector==1)$rec_bonus='-';
		else $rec_bonus=number_format($rec_bonus, 2,",",".").'%';  
		$infostr=
		$sec_lang['angriffsgrenze'].': '.$sec_angriffsgrenze.' / '.number_format($col_angriffsgrenze_final*100, 2,",",".").'%<br>'.
		$sec_lang['kollektoren'].': '.number_format($gescol, 0,"",".").'<br>'.
		$sec_lang['kollektorendurchschnitt'].': '.number_format($gescol/$anz, 2,",",".").'<br>'.
		$sec_lang['punktedurchschnitt'].': '.number_format($gesamtpunkte/$anz, 0,",",".").'<br>'.
		$sec_lang['platz'].' ('.$sec_lang['jetzt'].'): '.number_format($secplatz, 0,"",".").'<br>'.
		$sec_lang['platz'].' ('.$sec_lang['gestern'].'): '.number_format($sec_data['platz_last_day'], 0,"",".").'<br>'.
		$sec_lang['bewohntesysteme'].': '.number_format($anz, 0,"",".").'<br>'.
		$sec_lang['recyclingbonus'].': '.$rec_bonus.'<br>'.
		$sec_lang['sektorartefakthaltezeit'].': '.number_format($sec_data['arthold'], 0,"",".").'<br>';
		//$sec_lang[relverbreitung].': '.number_format($secrelcounter*100/($sv_maxsystem*10), 2,",",".").'%';


		//daten ausgeben
		//rahmen_oben($sec_lang[sektordaten]);
		echo '<table width="580" border="0" cellpadding="0" cellspacing="1">';
		$bg='cell';
		echo '<tr align="center">';
		echo '<td class="'.$bg.'" width="33%">'.$sec_lang['informationen'].'</td>';
		echo '<td class="'.$bg.'" width="34%">'.$sec_lang['sektorraumbasis'].'</td>';
		echo '<td class="'.$bg.'" width="33%">'.$sec_lang['sektorartefakte'].'</td>';
		echo '</tr>';
		
		echo '<tr align="center">';
		echo '<td align="left" valign="top" class="'.$bg.'">'.$infostr.'</td>';
		echo '<td align="center" valign="middle" class="'.$bg.'">'.$basestr.'</td>';
		echo '<td align="center" valign="middle" class="'.$bg.'">'.$artstr.'</td>';
		echo '</tr>';

		if ($ownsector==$sf){
			echo '<tr align="center">';
			if($_SESSION['ums_mobi']==0){
				echo '<td colspan="3"><a href="politics.php" target="h" class="btn">Sektorpolitik</a></td>';
			}else{
				echo '<td colspan="3"><a href="politics.php" class="btn">Sektorpolitik</a></td>';
			}
			
			echo '</tr>';
		}

		echo '</table>';
	}
	
	rahmen_unten();
  
  //Link um in Sektor 1 alle Spieler anzuzeigen
  if($sf==1){
		//f�r die mobile Seite einen refresh-Button
		if($_SESSION['ums_mobi']==1){
			echo '<a href="sector.php?sf=1&showall=1"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Alle Spieler anzeigen</div></a>';
		}else{
			echo '<a href="sector.php?sf=1&showall=1" class="btn">alles anzeigen</a><br><br>';
		}
  }

}
?>
</div>
</form>

</body>
</html>