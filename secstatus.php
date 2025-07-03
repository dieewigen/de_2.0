<?php
$GLOBALS['deactivate_old_design']=true;
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_secstatus.lang.php";
include "functions.php";
include "tickler/kt_einheitendaten.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, secstatdisable, status, allytag FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];$punkte=$row["score"];
$newtrans=$row["newtrans"];$allytag=$row["allytag"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];$secstatdisable=$row["secstatdisable"];

if ($row["status"]==1){
	$ownally=$row["allytag"];
}else{
	$ownally='';
}

//----------- Ally Feinde/Freunde
$allypartner = array();
$allyfeinde = array();
$query = "SELECT id FROM de_allys WHERE allytag='$ownally'";
$allyresult = mysql_query($query);
$at=mysql_num_rows($allyresult);
if ($at!=0){
	$allyid = mysql_result($allyresult,0,"id");

	$allyresult = mysql_query("SELECT allytag FROM de_ally_partner, de_allys where (ally_id_1=$allyid or ally_id_2=$allyid) and (ally_id_1=id or ally_id_2=id)",$db);
	while($row = mysql_fetch_array($allyresult)){
		if ($ownally != $row["allytag"]){
			$allypartner[] = $row["allytag"];
		}
	}

	$allyresult = mysql_query("SELECT allytag FROM de_ally_war, de_allys where (ally_id_angreifer=$allyid or ally_id_angegriffener=$allyid) and (ally_id_angreifer=id or ally_id_angegriffener=id)",$db);
	while($row = mysql_fetch_array($allyresult)){
		if ($ownally != $row["allytag"]){
			$allyfeinde[] = $row["allytag"];
		}
	}
}
//------------

//die länge eines KT in Minuten berechnen
//tickgeschwindigkeit auslesen
$filename="tickler/runtick.sh";
$cachefile = fopen ($filename, 'r');
$wticks=trim(fgets($cachefile, 1024));
$kticks=trim(fgets($cachefile, 1024));
$anzkticksprostunde=0;
for($i=1;$i<=60;$i++)if($kticks[$i]==1)$anzkticksprostunde++;

//Missionsende checken
checkMissionEnd();

//beim aufruf der seite alle sichtbaren flotten automatisch für den gesamten sektor sichtbar machen
mysql_query("UPDATE de_user_fleet SET entdecktsec = 1 WHERE zielsec='$sector' AND zielsys='$system' AND entdeckt=1 AND entdecktsec=0",$db);

?>
<!doctype html>
<html>
<head>
<title><?php echo $ss_lang['title']?></title>
<?php include "cssinclude.php"; ?>
<script type="text/javascript" src="js/de_fn.js?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/js/de_fn.js');?>"></script>
<script>
<!--
<?php 
//farben c0=schwarz, c1=koordinaten, c2=eta, c3=atter, c4=deffer 
//t=t+c0+"ETA:"+c2+" "+a1[j]+" "+c0+"INC:"+c3+" "+a1[j+1]+" "+c0+"DEF:"+c4+" "+a1[j+2]+" "+a1[j+3]+" "+c0+"DEF(3):"+c4+" "+a1[j+4]+" "+a1[j+5]+lb;
?>
function deirc(f,se,sy,a1,a2,a3) {
	var lb="\n";
	var t="<?php echo $ss_lang['systemstatusvon']?> "+se+":"+sy+lb;var j=0;
	for (i=0; i<a1.length/8; i++){
		t=t+"ETA: "+a1[j]+" INC: "+a1[j+1]+" (FP: "+a1[j+6]+") DEF: "+a1[j+4]+" (FP: "+a1[j+7]+") "+lb;
		var j=j+8;
	}
	if(document.getElementsByName("a"+se+"_"+sy)[0].checked == true){t=t+"<?php echo $ss_lang['angreifer']?>: "+a2+lb; i++;}
	if(document.getElementsByName("d"+se+"_"+sy)[0].checked == true){t=t+"<?php echo $ss_lang['verteidiger']?>: "+a3+lb; i++;}

	if(f==3){
		window.location.href="whatsapp://send?text="+encodeURIComponent(t);
	}else{
		document.getElementById("s"+se+"_"+sy).innerHTML = "<textarea id='k"+se+"_"+sy+"' rows='"+(i+1)+"' wrap='off' style='overflow:hidden;width:558px'>"+t+"</textarea>";
		document.getElementById("k"+se+"_"+sy).select();
	}
}
//-->
</script>
</head>
<body>
<?php

//wurde ein button gedrueckt??
//stelle die ressourcenleiste dar
include "resline.php";

if($secstatdisable==1) echo '<table width=600><tr><td class="ccr">'.$ss_lang['secstatdisable'].'</td></tr></table><br>';

//f�r die mobile Seite einen refresh-Button
if($_SESSION['ums_mobi']==1){
	echo '<a href="secstatus.php"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Sektorstatusansicht aktualisieren</div></a>';
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//angreifer - verteidiger
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

?>

<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td colspan="8" class="ro" align="center"><div class="cellu"><b><?php echo $ss_lang['angreifer']?> - <?php echo $ss_lang['verteidiger']?></b></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>

<td width="13" class="rl">&nbsp;</td>
<td colspan="8">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="50">
<col width="50">
<col width="16">
<col width="50">
<col width="100">
<col width="30">
<col width="100">
<col width="85">
<col width="80">
</colgroup>

<tr style="text-align: center; line-height: 20px; font-weight: bold;">
<td><div class="cell"><?php echo $ss_lang['ziel'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['herkunft'];?></div></td>
<td><div class="cell" title="Rasse">R</div></td>
<td><div class="cell"><?php echo $ss_lang['allianz'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['status'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['zeit'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['schiffe'];?></div></td>
<td title="Dieser Wert sind die Flottenpunkte. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet."><div class="cell">FP <img id="info" style="vertical-align: middle;" src="<?php echo $ums_gpfad.'g/'.$ums_rasse;?>_hilfe.gif"></div></td>
<td><div class="cell"><?php echo $ss_lang['aktion'];?></div></td>
</tr>

<?php
$eta=-1;
$ssc=0;
if($secstatdisable==1){
	$flotten=mysql_query("SELECT * FROM de_user_fleet WHERE zielsec = '$sector' AND zielsys= '$system' AND (aktion = 1 OR aktion = 2) AND entdeckt > 0 AND entdecktsec > 0 ORDER BY zielsys, zeit, hsec, hsys ASC",$db);
}else{
  	$flotten=mysql_query("SELECT * FROM de_user_fleet WHERE zielsec = '$sector' AND (aktion = 1 OR aktion = 2) AND entdeckt > 0 AND entdecktsec > 0 ORDER BY zielsys, zeit, hsec, hsys ASC",$db);
}
$zsecold=0;$zsysold=0;$sc=array();
$fa = mysql_num_rows($flotten);

//alle gefunden Atter/Deffer-Flotten durchgehen
for ($i=0; $i<$fa; $i++){

	//$zsec1=mysql_result($flotten, $i, "zielsec");
	$user_id=mysql_result($flotten, $i, "user_id");
	$zsec1=$sector;
	$zsys1=mysql_result($flotten, $i, "zielsys");
	$a1=mysql_result($flotten, $i, "aktion");
	$t1=mysql_result($flotten, $i, "zeit");
	$at1=mysql_result($flotten, $i, "aktzeit");
	$hsec=mysql_result($flotten, $i, "hsec");
	$hsys=mysql_result($flotten, $i, "hsys");
	$ge=mysql_result($flotten, $i, "fleetsize");

	if ($zsec1==$zsecold and $zsys1==$zsysold){
		//es ist noch das gleiche system
		$sss='&nbsp;';
		$eta=$t1;
		if(isset($sc[$ssc][1][1]) && $eta>$sc[$ssc][1][1]){
			$sc[$ssc][1][1]=$eta;//maxeta
		}

		//angreiferliste
		if ($a1==1){
			if(isset($sc[$ssc][0][$eta][0])){
				$sc[$ssc][0][$eta][0]+=$ge;//atter
			}else{
				$sc[$ssc][0][$eta][0]=$ge;//atter
			}
		
			if(!isset($sc[$ssc][0][$eta][2])){
				$sc[$ssc][0][$eta][2]='';
			}

			$pos = strpos ($sc[$ssc][0][$eta][2], $hsec.':'.$hsys);
			if ($pos === false){// nicht gefunden...
				if ($sc[$ssc][0][$eta][2]!=''){
					$sc[$ssc][0][$eta][2].=', ';
				}
				$sc[$ssc][0][$eta][2].=$hsec.':'.$hsys;
			}

		}
		else{
			//defferliste

			//Einheitenanzahl
			if(isset($sc[$ssc][0][$eta][1])){
				$sc[$ssc][0][$eta][1]+=$ge;//deffer
			}else{
				$sc[$ssc][0][$eta][1]=$ge;
			}
			

			//deffer3 liste
			for ($j=0;$j<=$at1;$j++){
				if(isset($sc[$ssc][0][$eta+$j][4])){
					$sc[$ssc][0][$eta+$j][4]+=$ge;
				}else{
					$sc[$ssc][0][$eta+$j][4]=$ge;
				}
				
				
				if ($eta+$j>$sc[$ssc][1][1]){
					$sc[$ssc][1][1]=$eta+$j;
				}
			}

			if(!isset($sc[$ssc][0][$eta][3])){
				$sc[$ssc][0][$eta][3]='';
			}

			$pos = strpos ($sc[$ssc][0][$eta][3], $hsec.':'.$hsys);
			if ($pos === false)// nicht gefunden...
			{
				if ($sc[$ssc][0][$eta][3]!=''){
					$sc[$ssc][0][$eta][3].=', ';
				}
				$sc[$ssc][0][$eta][3].=$hsec.':'.$hsys;
			}
		}
	}else{
		//es ist ein neues system
		//counter für die anzahl der angegriffenen systeme im sektor
		if($zsecold>0){
			$ssc++;
		}

		//$sss=$zsec1.':'.$zsys1;
		$sss='<a href="military.php?se='.$zsec1.'&sy='.$zsys1.'" title="Flotten">'.$zsec1.':'.$zsys1.'</a>';
		$eta=$t1;
		if ($a1==1)$sc[$ssc][0][$eta][0]=$ge;//atter
		if ($a1==2)$sc[$ssc][0][$eta][1]=$ge;//deffer
		$sc[$ssc][1][0]=$zsys1;//system
		
		if(!isset($sc[$ssc][1][1]) || $eta>$sc[$ssc][1][1]){
			$sc[$ssc][1][1]=$eta;//maxeta
		}
		
		if ($a1==1){
			$sc[$ssc][0][$eta][2]=$hsec.':'.$hsys;
		}else{ //defferliste
			$sc[$ssc][0][$eta][3]=$hsec.':'.$hsys;

			for ($j=0;$j<=$at1;$j++){

				if(isset($sc[$ssc][0][$eta+$j][4])){
					$sc[$ssc][0][$eta+$j][4]+=$ge;
				}else{
					$sc[$ssc][0][$eta+$j][4]=$ge;
				}
				
				
				if (isset($sc[$ssc][1][1]) && ($eta+$j)>$sc[$ssc][1][1]){
					$sc[$ssc][1][1]=$eta+$j;
				}
			}
		}
	}

	$as1=$a1;
	if ($a1==0) $a1=$ss_lang['systemverteidigung'];
	elseif ($a1==1) {$a1=$ss_lang['angriff']; $cl='ccr';}
	elseif ($a1==2) {$a1=$ss_lang['verteidigung'].' ('.$at1.')'; $cl='ccg';}
	elseif ($a1==3) {$a1=$ss_lang['rueckflug']; $cl='cc';}
	elseif ($a1==4) {$a1=$ss_lang['archaeologie']; $cl='cc';}

	if ($a1[0]==$ss_lang['verteidigung'][0] && $t1==0){
		$a1=$ss_lang['verteidige'];$t1=$at1;$cl='ccy';
	}

	///////////////////////////////////////////////
	///////////////////////////////////////////////
	//rasse und allytag auslesen
	///////////////////////////////////////////////
	///////////////////////////////////////////////
	
	$allytagscan='';
	$zally='';
	$hv=explode("-",$user_id);
	$uid=$hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
	if ($uid!=$ums_user_id){
		//allygegner/-verb�ndete
		//allytag des deffers/atters auslesen
		$db_daten=mysql_query("SELECT allytag, rasse, status FROM de_user_data WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_daten);
		if ($row["status"]==1) $zally = $row["allytag"];  	
		$rasse_id=$row['rasse'];

		
		if (in_array($zally, $allyfeinde) OR in_array($zally, $allypartner)) $allytagscan=$zally;
		
		//eigene ally
		if($zally==$ownally){
			$allytagscan=$zally;
		}
		
		//geheimdienst
		//daten aus der db holen, wenn es nicht der spieler selbst ist
		$db_daten=mysql_query("SELECT rasse, allytag FROM de_user_scan WHERE user_id='$ums_user_id' AND zuser_id='$uid'",$db);
		$scan_vorhanden = mysql_num_rows($db_daten);
		if ($scan_vorhanden==1){
			$row = mysql_fetch_array($db_daten);
			//allytag zuweisen, wenn noch nichts vorliegt, sonst sind die daten veraltet
			if($allytagscan=='')$allytagscan=$row["allytag"];
		}
	}else{//der spieler selbst soll angezeigt werden
		$rasse_id=$_SESSION['ums_rasse'];
		$allytagscan=$ownally;
	}

	$rasse='&nbsp;';
	if($rasse_id==1)$rasse='<img src="'.$ums_gpfad.'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
	if($rasse_id==2)$rasse='<img src="'.$ums_gpfad.'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
	if($rasse_id==3)$rasse='<img src="'.$ums_gpfad.'g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
	if($rasse_id==4)$rasse='<img src="'.$ums_gpfad.'g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';

	//die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
	$fp=0;
	for($s=81;$s<=90;$s++){
		
		if($as1==1){ //Atter
			if($rasse_id==4 && $s==83){
				//gatarnte Einheiten
				//$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
			}else{
				$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
			}
		}else{ //Deffer
			$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
		}
	}

	//die Flottenpunkte für die Einzelausgabe sichern
	if ($as1==1){
		//Angriff
		if(!isset($sc[$ssc][0][$eta]['fp_atter'])){
			$sc[$ssc][0][$eta]['fp_atter']=0;
		}

		$sc[$ssc][0][$eta]['fp_atter']+=$fp;
	}elseif ($as1==2) {
		//Verteidigung
		if(!isset($sc[$ssc][0][$eta]['fp_deffer'])){
			$sc[$ssc][0][$eta]['fp_deffer']=0;
		}

		$sc[$ssc][0][$eta]['fp_deffer']+=$fp;

		//evtl. bleibt er ein paar KT vor Ort beim Deffen, also ggf. die Folgeticks berechnen
		for ($j=1;$j<=$at1;$j++){

			if(isset($sc[$ssc][0][$eta+$j]['fp_deffer_3'])){
				$sc[$ssc][0][$eta+$j]['fp_deffer_3']+=$fp;
			}else{
				$sc[$ssc][0][$eta+$j]['fp_deffer_3']=$fp;
			}
		}

	}


	echo '<tr>';
	echo '<td class="cc"><b>'.$sss.'</b></td>';
	echo '<td class="'.$cl.'">'.$hsec.':'.$hsys.'</td>';
	echo '<td class="'.$cl.'">'.$rasse.'</td>';
	echo '<td class="'.$cl.'">'.utf8_encode_fix($allytagscan).'</td>';
	echo '<td class="'.$cl.'">'.$a1.'</td>';
	echo '<td class="'.$cl.'">'.$t1.'</td>';
	echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
	if($sv_hide_fp_in_secstatus!=1){
		echo '<td class="'.$cl.'" title="'.number_format($fp, 0,"",".").'">'.formatMasseinheit($fp, 2).'</td>';
	}else{
		echo '<td class="'.$cl.'">N/A</td>';
	}
	//SAMH
	echo '<td class="'.$cl.'">
		<a href="secret.php?a=s&zsec1='.$hsec.'&zsys1='.$hsys.'" title="Sonde">S</a>
		&nbsp;<a href="secret.php?a=a&zsec2='.$hsec.'&zsys2='.$hsys.'" title="Agenteneinsatz">A</a>
		&nbsp;<a href="military.php?se='.$hsec.'&sy='.$hsys.'" title="Flotte">F</a>
		&nbsp;<a href="details.php?se='.$hsec.'&sy='.$hsys.'" title="Hyperfunk">H</a></td>';
	echo '</tr>';
	
	//schauen ob eine neue eta kommt, bzw. ob es der letzte datensatz ist
	if($t1!=$eta || $i==$fa-1){
		//neue eta, schauen ob man eine vorhergehende zusammenrechnen mu�
		//if ($eta!=-1)
	}
	$zsecold=$zsec1;$zsysold=$zsys1;
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//ankommende sektorflotten (BK) anzeigen
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

$flotten=mysql_query("SELECT sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE zielsec = '$sector' AND (aktion = 1 OR aktion = 2)",$db);
$fa = mysql_num_rows($flotten);
	for ($i=0; $i<$fa; $i++){
	//$zsec1=mysql_result($flotten, $i, "zielsec");
	$sec_id=mysql_result($flotten, $i, "sec_id");
	$a1=mysql_result($flotten, $i, "aktion");
	$t1=mysql_result($flotten, $i, "zeit");
	$at1=mysql_result($flotten, $i, "aktzeit");

	$as1=$a1;
	if ($a1==0) $a1=$ss_lang['systemverteidigung'];
	elseif ($a1==1) {$a1=$ss_lang['angriff']; $cl='ccr';}
	elseif ($a1==2) {$a1=$ss_lang['verteidigung'].' ('.$at1.')'; $cl='ccg';}
	elseif ($a1==3) {$a1=$ss_lang['rueckflug']; $cl='cc';}
	elseif ($a1==4) {$a1=$ss_lang['archaeologie']; $cl='cc';}

	if ($a1[0]==$ss_lang['verteidigung'][0] && $t1==0) {$a1=$ss_lang['verteidige'];$t1=$at1;$cl='ccy';}

	//einheiten z�hlen
	$ge=mysql_result($flotten, $i, "e2");

	echo '<tr>';
	echo '<td class="'.$cl.'">'.$ss_lang['sektor'].'</td>';
	echo '<td class="'.$cl.'">['.$sec_id.']</td>';
	echo '<td class="'.$cl.'">-</td>';
	echo '<td class="'.$cl.'">-</td>';
	echo '<td class="'.$cl.'">'.$a1.'</td>';
	echo '<td class="'.$cl.'">'.$t1.'</td>';
	echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
	echo '<td class="'.$cl.'">-</td>';
	echo '</tr>';
}
//echo '</table><br><br>';
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td colspan="8" class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>

<?php
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//jetzt den status der einzelnen system ausgeben
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
if(count($sc) > 0){
	for ($i=0;$i<=$ssc;$i++){
		$jsirc1='';
		$jsirc2='';
		$jsirc3='';
		echo '<br><table border="0" cellpadding="0" cellspacing="0">';
		echo '<tr align="center">';
		echo '<td width="13" height="37" class="rol">&nbsp;</td>';
		echo '<td align="center" class="ro"><div class="cellu">'.$ss_lang['systemstatus'].' '.$sector.':'.$sc[$i][1][0].
			' - <a href="secret.php?a=s&zsec1='.$sector.'&zsys1='.$sc[$i][1][0].'" title="Sonde">S</a>
			&nbsp;<a href="secret.php?a=a&zsec2='.$sector.'&zsys2='.$sc[$i][1][0].'" title="Agenteneinsatz">A</a>
			&nbsp;<a href="military.php?se='.$sector.'&sy='.$sc[$i][1][0].'" title="Flotte">F</a>
			&nbsp;<a href="details.php?se='.$sector.'&sy='.$sc[$i][1][0].'" title="Hyperfunk">H</a>';
		
			//inc soll an die allianz meldbar sein, wenn der spieler in einer allianz ist und es noch nicht gemeldet worden ist
			//test auf ally
			$db_daten=mysql_query("SELECT allytag, ally_id, status, show_ally_secstatus FROM de_user_data WHERE sector='".$sector."' AND system='".$sc[$i][1][0]."';",$db);
			$row = mysql_fetch_array($db_daten);
			if ($row["status"]==1){
				$ally_id=$row['ally_id'];
				$allytag=$row['allytag'];
			}else{
				$ally_id='';
				$allytag='';
			}
			$show_ally_secstatus=$row['show_ally_secstatus'];
			if($ally_id>0){

				//test ob der status aktuell bereits übermittelt wird
				if($show_ally_secstatus>time()){//wird übermittelt
					//anzeigen bis wann es übermittelt wird
					echo '&nbsp;(Allianzeinsicht bis: '.date("H:i:s d.m.Y", $show_ally_secstatus).')';
				}else{//wird nicht übermittelt, melden link einblenden/überprüfen
					//test auf aktivierung
					if(isset($_REQUEST['sassys']) && $_REQUEST['sassys']==$sc[$i][1][0]){
						//Sichtbarkeit berechnen
						//$sichtbarkeit=3600/$anzkticksprostunde;
						$sichtbarkeit=$GLOBALS['sv_show_ally_secstatus'];
						//Sichtbarkeit um Allianzgebäude verlängern
						$allybldg=get_allybldg($ally_id);
						$geb_stufe=$allybldg[7];
						//echo '<br>AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA: '.$allybldg[0].'<br>';
						//print_r($allybldg);
						//boni durch allianzpartner
						$allyidpartner=get_allyid_partner($ally_id);
						if($allyidpartner>0){
							$allybldgpartner=get_allybldg($allyidpartner);
							$geb_stufe+=$allybldgpartner[7]/100*$allybldgpartner[1];
						}
			
						$sichtbarkeit=$sichtbarkeit+($sichtbarkeit/100*$geb_stufe);
						
						//echo 'AAA:'.$sichtbarkeit;
						$show_ally_secstatus=time()+$sichtbarkeit;

						//anzeigen bis wann es übermittelt wird
						echo '&nbsp;(Allianzeinsicht bis: '.date("H:i:s d.m.Y", $show_ally_secstatus).')';
						//db updaten
						$db_daten=mysql_query("UPDATE de_user_data SET show_ally_secstatus='$show_ally_secstatus' WHERE sector='".$sector."' AND `system`='".$sc[$i][1][0]."';",$db);
						//eintrag im allianzchat
						$chattext='<font color="#ff0101">Status&uuml;bermittlung von ('.$sector.':'.$sc[$i][1][0].') durch '.$ums_spielername.'</font>';
						insert_chat_msg($ally_id, 1, '', $chattext);
						
						
					}else{
						//aktivierungslink anzeigen
						echo '&nbsp;<a href="secstatus.php?sassys='.$sc[$i][1][0].'" 
							title="Die Allianz '.utf8_encode_fix($allytag).' f&uuml;r die Dauer eines KT &uuml;ber den Status ihres Mitgliedes informieren.">AI</a>';
					}
				}
			}
		
		echo '</div></td>';
		echo '<td width="13" class="ror">&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td width="13" class="rl">&nbsp;</td>';
		echo '<td colspan="1">';

		echo '<table width="570" border="0" cellpadding="0" cellspacing="1">';
		echo '<tr>';
		echo '<td width="30" class="tc"><b>'.$ss_lang['eta'].'</td>';
		echo '<td width="100" class="tc"><b>'.$ss_lang['inc'].'</td>';
		echo '<td width="100" class="tc"><b>'.$ss_lang['def'].'</td>';
		//echo '<td width="90" class="tc"><b>'.$ss_lang['def'].'(3)</td>';
		echo '<td width="180" colspan="2" class="tc" title="Dieser Wert sind die Flottenpunkte. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet.">FP <img id="info" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
		echo '<td width="80" class="tc"><b>'.$ss_lang['angreifer'].'</td>';
		echo '<td width="80" class="tc"><b>'.$ss_lang['verteidiger'].'</td>';
		echo '</tr>';
		//die einzelne etas ausgeben
		//print_r($sc);
		for ($j=0; $j<=$sc[$i][1][1];$j++){
			//wenn es schiffe bei der eta gibt, dann eine zeile ausgeben
			if ((isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0]>0) || (isset($sc[$i][0][$j][1]) && $sc[$i][0][$j][1]>0) || (isset($sc[$i][0][$j][4]) && $sc[$i][0][$j][4]>0) || (isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][2]>0) || (isset($sc[$i][0][$j][3]) && $sc[$i][0][$j][3]>0)){
				//verhältniss atter/deffer berechnen
				//nur berechnen, wenn es atter gibt

				if(!isset($sc[$i][0][$j][1])){
					$sc[$i][0][$j][1]=0;
				}

				if(!isset($sc[$i][0][$j][4])){
					$sc[$i][0][$j][4]=0;
				}

				if(!isset($sc[$i][0][$j][6])){
					$sc[$i][0][$j][6]=0;
				}

				if (isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0]>0){
					$sc[$i][0][$j][5]=$sc[$i][0][$j][1]/$sc[$i][0][$j][0];
					$sc[$i][0][$j][6]=$sc[$i][0][$j][4]/$sc[$i][0][$j][0];
				}else{
					$sc[$i][0][$j][5]=0;//deffer
					$sc[$i][0][$j][6]=0;//deffer3
				}

				if ($sc[$i][0][$j][5]>0)$v1=' (1:'.number_format($sc[$i][0][$j][5], 1,",",".").')';else $v1='';
				if ($sc[$i][0][$j][6]>0)$v3=' (1:'.number_format($sc[$i][0][$j][6], 1,",",".").')';else $v3='';

				if(!isset($sc[$i][0][$j][0])){
					$sc[$i][0][$j][0]=0;
				}

				if(!isset($sc[$i][0][$j][2])){
					$sc[$i][0][$j][2]='';
				}

				if(!isset($sc[$i][0][$j][3])){
					$sc[$i][0][$j][3]='';
				}

				if(!isset($sc[$i][0][$j]['fp_atter'])){
					$sc[$i][0][$j]['fp_atter']=0;
				}

				if(!isset($sc[$i][0][$j]['fp_deffer'])){
					$sc[$i][0][$j]['fp_deffer']=0;
				}

				if(!isset($sc[$i][0][$j]['fp_deffer_3'])){
					$sc[$i][0][$j]['fp_deffer_3']=0;
				}				
 
				echo '<tr>';
				echo '<td class="cc">'.$j.'</td>'; //ETA
				echo '<td class="ccr">'.number_format($sc[$i][0][$j][0], 0,"",".").'</td>';//INC
				//echo '<td class="ccg">'.number_format($sc[$i][0][$j][1], 0,"",".").'</td>';//DEFF
				//echo '<td class="cc">'.$v1.'</td>';//Verhältnis Atter/Deffer in der ETA
				echo '<td class="ccg">'.number_format($sc[$i][0][$j][4], 0,"",".").'</td>';//DEFF3
				//echo '<td class="cc">'.$v3.'</td>';//Verhältnis Atter/Deffer in der 3er ETA
				echo '<td class="ccr" title="'.number_format($sc[$i][0][$j]['fp_atter'], 0,"",".").'">'.formatMasseinheit($sc[$i][0][$j]['fp_atter'],2).'</td>';
				echo '<td class="ccg" title="'.number_format($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'], 0,"",".").'">'.formatMasseinheit($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'],2).'</td>';

				echo '<td class="ccr">'.$sc[$i][0][$j][2].'</td>';
				echo '<td class="ccg">'.$sc[$i][0][$j][3].'</td>';
				echo '</tr>';

				//javascript fürs irc
				//if ($v1=='')$v1='(1:0,0)';
				//if ($v3=='')$v3='(1:0,0)';

				//////////////////////////////////////////////
				//Text/WA JS-Daten
				//////////////////////////////////////////////
				
				//Array: ETA,Atter, Deffer, Einheiten-Verhältnis, Deffer3, Einheiten3-Verhältnis
				if ($sc[$i][0][$j][0]>0 || $sc[$i][0][$j][2]>0 || $sc[$i][0][$j][3]>0){
					$gesamt_fp=$sc[$i][0][$j]['fp_atter']+$sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'];
					
					if($gesamt_fp>0){
						$atter_percent=$sc[$i][0][$j]['fp_atter']*100/$gesamt_fp;
						$deffer_percent=($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'])*100/$gesamt_fp;
					}else{
						$atter_percent='0.00';
						$deffer_percent='0.00';
					}

					$fp_atter=formatMasseinheit($sc[$i][0][$j]['fp_atter'],2).' / '.number_format($atter_percent, 2,",",".").'%';

					$fp_deffer=formatMasseinheit($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'],2).' / '.number_format($deffer_percent, 2,",",".").'%';
					
					if ($jsirc1!='')$jsirc1.=",";
					$jsirc1.="'".$j."','".number_format($sc[$i][0][$j][0], 0,"",".")."','".number_format($sc[$i][0][$j][1], 0,"",".")."','".
					$v1."','".number_format($sc[$i][0][$j][4], 0,"",".")."','".$v3."','".$fp_atter."','".$fp_deffer."'";
				}

				//String: Atter
				if ($sc[$i][0][$j][2]>0)
				{
					if ($jsirc2!='')$jsirc2.=",";
					$jsirc2.="'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][2]."'";
				}

				//String: Deffer
				if ($sc[$i][0][$j][3]>0){
					if ($jsirc3!='')$jsirc3.=",";
					$jsirc3.="'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][3]."'";
				}


			}
		}
		
		echo '</table>';
		echo '</td>';
		echo '<td width="13" class="rr">&nbsp;</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td width="13" class="rl">&nbsp;</td>';
		echo '<td align="center" id="s'.$sector.'_'.$sc[$i][1][0].'">';
		echo '<table border="0" cellpadding="0" cellspacing="1" width="100%">';
		echo '<tr>';
		$hzsys=$sc[$i][1][0];
		//echo "<td class=\"cc\"><input type=\"button\" value=\"".$ss_lang['irc']."\" onclick=\"deirc(0,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
		echo "<td class=\"cc\"><input type=\"button\" value=\"".$ss_lang['text']."\" onclick=\"deirc(1,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
		echo "<td class=\"cc\"><input type=\"button\" value=\"WhatsApp\" onclick=\"deirc(3,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
		//echo "<td class=\"cc\"><input type=\"button\" value=\"einzeilig\" onclick=\"deirc(2,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
		echo '<td class="cc"><input type="checkbox" name="a'.$sector.'_'.$sc[$i][1][0].'" checked> '.$ss_lang['angreiferanzeigen'].'</td>';
		echo '<td class="cc"><input type="checkbox" name="d'.$sector.'_'.$sc[$i][1][0].'"> '.$ss_lang['verteidigeranzeigen'].'</td>';
		echo '</tr>';
		echo '</table>';
		echo '</td>';
		echo '<td width="13" class="rr">&nbsp;</td>';
		echo '</tr>';

		echo '<tr height="20">';
		echo '<td class="rul" width="13">&nbsp;</td>';
		echo '<td class="ru">&nbsp;</td>';
		echo '<td class="rur" width="13">&nbsp;</td>';
		echo '</tr>';
		echo '</table>';
	}
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//alle flotten des sektors selbst anzeigen
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
?>
<br>
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td colspan="5" class="ro" align="center"><div class="cellu"><b><?php echo $ss_lang['sektorflotten']?></b></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>

<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="5">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="60">
<col width="60">
<col width="140">
<col width="50">
<col width="100">
<col width="85">
</colgroup>

<tr style="text-align: center; line-height: 20px; font-weight: bold;">
<td><div class="cell"><?php echo $ss_lang['ziel']?></div></td>
<td><div class="cell"><?php echo $ss_lang['herkunft']?></div></td>
<td><div class="cell"><?php echo $ss_lang['status']?></div></td>
<td><div class="cell"><?php echo $ss_lang['zeit']?></div></td>
<td><div class="cell"><?php echo $ss_lang['schiffe']?></div></td>
<td title="Dieser Wert sind die Flottenpunkte. Getarnte Einheiten werden mit eingerechnet."><div class="cell">FP <img id="info" style="vertical-align: middle;" src="<?php echo $ums_gpfad.'g/'.$ums_rasse;?>_hilfe.gif"></div></td>
</tr>


<?php
if($secstatdisable==1){
  $flotten=mysql_query("SELECT * FROM de_user_fleet WHERE hsec='$sector' AND hsys='$system' AND aktion>0 ORDER BY hsys, aktion, zeit ASC",$db);
}else{
  $flotten=mysql_query("SELECT * FROM de_user_fleet WHERE hsec='$sector' AND aktion>0 ORDER BY hsys, aktion, zeit ASC",$db);
}

$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++){
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $zsys1=mysql_result($flotten, $i, "zielsys");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");
  $hsec=mysql_result($flotten, $i, "hsec");
  $hsys=mysql_result($flotten, $i, "hsys");
  $showft=mysql_result($flotten, $i, "showfleettarget");
  $mission_time=mysql_result($flotten, $i, "mission_time");

  //wenn man es nicht selbst ist, dann sieht man die koordinaten von anderen spielern die angreifen nicht mehr, wenn sie versteckt sind
  if($hsys!=$system)
  {
  	//nur bei angriff/verteidigung/mission bei bedarf ausblenden
  	if($a1==1 OR $a1==2 OR $a1==4)
  	{
  		if($showft==0)
  		{
  			$zsec1='?';
  			$zsys1='?';
  		}
  	}
  }
  
  $mission_aktiv=false;
  $as1=$a1;
  if ($a1==0) $a1=$ss_lang['systemverteidigung'];
  elseif ($a1==1) {$a1=$ss_lang['angriff']; $cl='ccr';}
  elseif ($a1==2) {$a1=$ss_lang['verteidigung'].' ('.$at1.')'; $cl='ccg';}
  elseif ($a1==3) {$a1=$ss_lang['rueckflug']; $cl='cc';}
  elseif ($a1==4) {$a1=$ss_lang['archaeologie']; $cl='cc';$mission_aktiv=true;}

  if ($a1[0]==$ss_lang['verteidigung'][0] && $t1==0) {$a1=$ss_lang['verteidige'];$t1=$at1;$cl='ccy';}
  

  //rasse auslesen
  /*$result = mysql_query("SELECT rasse FROM de_user_data WHERE sector = '$hsec' and system = '$hsys'",$db);
  $db_data = mysql_fetch_array($result);
  $rasse = $db_data["rasse"];*/

  //einheiten z�hlen
  $ge=0;
  for ($z=81;$z<=90;$z++){
    $erg=mysql_result($flotten, $i, "e$z");
    $ez[$z-81]=$erg;
    //fix um die zerst�rer der 4. rasse unsichtbar zu machen
    //if($rasse==4 AND $z==83)$erg=0;
    $ge=$ge+$erg;
  }

	$hv=explode("-",mysql_result($flotten, $i, "user_id"));
	$uid=$hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
	if ($uid!=$ums_user_id){
		$db_daten=mysql_query("SELECT allytag, rasse, status FROM de_user_data WHERE user_id='$uid'",$db);
		$row = mysql_fetch_array($db_daten);
		if ($row["status"]==1) $zally = $row["allytag"];  	
    $rasse_id=$row['rasse'];
  }else{
    $rasse_id=$_SESSION['ums_rasse'];
  }

	//die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
	$fp=0;
	for($s=81;$s<=90;$s++){
		
		if($as1==1){ //Atter
			if($rasse_id==4 && $s==83){
				//$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
			}else{
        $fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
      }
		}else{ //Deffer
			$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
		}
	}  
  
  if($mission_aktiv){
    echo '<tr>';
    echo '<td class="'.$cl.'">-</td>';
    echo '<td class="'.$cl.'">'.$hsec.':'.$hsys.'</td>';
    echo '<td class="'.$cl.'">'.$a1.'</td>';
    echo '<td class="'.$cl.'">'.date("H:i:s d.m.Y",$mission_time).'</td>';
    echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
    if($sv_hide_fp_in_secstatus!=1){
      echo '<td class="'.$cl.'" title="'.number_format($fp, 0,"",".").'">'.formatMasseinheit($fp, 2).'</td>';
    }else{
      echo '<td class="'.$cl.'">N/A</td>';
    }
    echo '</tr>';
  }else{
    //bei angriffen nicht mehr die zielkoordinaten anzeigen
    echo '<tr>';
    echo '<td class="'.$cl.'">'.$zsec1.':'.$zsys1.'</td>';
    if ($a1==$ss_lang['rueckflug'])echo '<td class="'.$cl.'">&nbsp;</td>';
    else echo '<td class="'.$cl.'">'.$hsec.':'.$hsys.'</td>';
    echo '<td class="'.$cl.'">'.$a1.'</td>';
    echo '<td class="'.$cl.'">'.$t1.'</td>';
    echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
    if($sv_hide_fp_in_secstatus!=1){
      echo '<td class="'.$cl.'" title="'.number_format($fp, 0,"",".").'">'.formatMasseinheit($fp, 2).'</td>';
    }else{
      echo '<td class="'.$cl.'">N/A</td>';
    }
    echo '</tr>';
  }

}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//sektorflotte in bewegung anzeigen
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

$flotten=mysql_query("SELECT zielsec, sec_id, aktion, aktzeit, zeit, e2 FROM de_sector WHERE aktion<>0 AND sec_id=$sector",$db);
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++)
{
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $sec_id=mysql_result($flotten, $i, "sec_id");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");

  $as1=$a1;
  if ($a1==0) $a1=$ss_lang['systemverteidigung'];
  elseif ($a1==1) {$a1=$ss_lang['angriff']; $cl='ccr';}
  elseif ($a1==2) {$a1=$ss_lang['verteidigung'].' ('.$at1.')'; $cl='ccg';}
  elseif ($a1==3) {$a1=$ss_lang['rueckflug']; $cl='cc';}
  elseif ($a1==4) {$a1=$ss_lang['archaeologie']; $cl='cc';}

  if ($a1[0]==$ss_lang['verteidigung'][0] && $t1==0) {$a1=$ss_lang['verteidige'];$t1=$at1;$cl='ccy';}

  //einheiten zählen
  $ge=mysql_result($flotten, $i, "e2");

  echo '<tr>';
  echo '<td class="'.$cl.'" width="14%">['.$zsec1.']</td>';
  echo '<td class="'.$cl.'" width="16%">'.$ss_lang['sektor'].'</td>';
  echo '<td class="'.$cl.'" width="40%">'.$a1.'</td>';
  echo '<td class="'.$cl.'" width="10%">'.$t1.'</td>';
  echo '<td class="'.$cl.'" width="20%">'.number_format($ge, 0,"",".").'</td>';
  echo '</tr>';
  $zsecold=$zsec1;$zsysold=$zsys1;
}

//echo '</table>';
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>
<br>
<?php




////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//der sektorstatus der allianz(bündnis)mitglieder
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//nur anzeigen, wenn man selbst in einer allianz ist
if($ownally!=''){
?>
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td colspan="8" class="ro" align="center"><div class="cellu"><b>Allianzmitglieder</b></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>

<td width="13" class="rl">&nbsp;</td>
<td colspan="8">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="50">
<col width="50">
<col width="16">
<col width="50">
<col width="100">
<col width="30">
<col width="100">
<col width="85">
<col width="80">
</colgroup>

<tr style="text-align: center; line-height: 20px; font-weight: bold;">
<td><div class="cell"><?php echo $ss_lang['ziel'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['herkunft'];?></div></td>
<td><div class="cell" title="Rasse">R</div></td>
<td><div class="cell"><?php echo $ss_lang['allianz'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['status'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['zeit'];?></div></td>
<td><div class="cell"><?php echo $ss_lang['schiffe'];?></div></td>
<td title="Dieser Wert sind die Flottenpunkte. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet."><div class="cell">FP <img id="info" style="vertical-align: middle;" src="<?php echo $ums_gpfad.'g/'.$ums_rasse;?>_hilfe.gif"></div></td>
<td><div class="cell"><?php echo $ss_lang['aktion'];?></div></td>
</tr>


<?php
$eta=-1;
$ssc=0;
unset($sc);

/////////////////////////////////////////////////////////////////
//allytag von einer evtl. partnerallianz auslesen
/////////////////////////////////////////////////////////////////
$allypartnertag=isset($allypartner[0])?$allypartner[0]:'';
$time=time();
$sql="SELECT *, de_user_fleet.user_id, de_user_fleet.zielsec, de_user_fleet.zielsys, de_user_fleet.aktion, de_user_fleet.aktzeit, de_user_fleet.hsec, 
de_user_fleet.hsys, de_user_fleet.zeit, de_user_fleet.fleetsize, de_user_data.show_ally_secstatus
FROM de_user_fleet LEFT JOIN de_user_data ON(de_user_data.sector=de_user_fleet.zielsec AND de_user_data.`system`=de_user_fleet.zielsys) 
WHERE de_user_fleet.zielsec != '$sector' AND (de_user_fleet.aktion = 1 OR de_user_fleet.aktion = 2) AND de_user_fleet.entdeckt > 0 
AND de_user_fleet.entdecktsec > 0 AND de_user_data.show_ally_secstatus>'$time' AND de_user_data.status=1 AND de_user_data.allytag<>'' AND 
(de_user_data.allytag='$ownally'". ($allypartnertag!=''? "OR de_user_data.allytag='$allypartnertag'" : "") .")
ORDER BY de_user_fleet.zielsec, de_user_fleet.zielsys, de_user_fleet.zeit, de_user_fleet.hsec, de_user_fleet.hsys ASC";

//echo $sql;

$flotten=mysql_query($sql,$db);
$zsecold=0;$zsysold=0;
$fa = mysql_num_rows($flotten);
for ($i=0; $i<$fa; $i++){

  //$zsec1=mysql_result($flotten, $i, "zielsec");
  $user_id=mysql_result($flotten, $i, "user_id");
  $zsec1=mysql_result($flotten, $i, "zielsec");
  $zsys1=mysql_result($flotten, $i, "zielsys");
  $a1=mysql_result($flotten, $i, "aktion");
  $t1=mysql_result($flotten, $i, "zeit");
  $at1=mysql_result($flotten, $i, "aktzeit");
  $hsec=mysql_result($flotten, $i, "hsec");
  $hsys=mysql_result($flotten, $i, "hsys");
  $ge=mysql_result($flotten, $i, "fleetsize");
  $show_ally_secstatus=mysql_result($flotten, $i, "show_ally_secstatus");


  if ($zsec1==$zsecold and $zsys1==$zsysold)
  {
    //es ist noch das gleiche system
    $sss='&nbsp;';
    $eta=$t1;
    if ($eta>$sc[$ssc][1][1])$sc[$ssc][1][1]=$eta;//maxeta

    //angreiferliste
    if ($a1==1)
    {
      $sc[$ssc][0][$eta][0]+=$ge;//atter
      
      $pos = strpos ($sc[$ssc][0][$eta][2], $hsec.':'.$hsys);
      if ($pos === false)// nicht gefunden...
      {
        if ($sc[$ssc][0][$eta][2]!='')$sc[$ssc][0][$eta][2].=' - ';
        $sc[$ssc][0][$eta][2].=$hsec.':'.$hsys;
      }
    }
    else
    //defferliste
    {
      $sc[$ssc][0][$eta][1]+=$ge;//deffer

      //deffer3 liste
      for ($j=0;$j<=$at1;$j++)
      {
        $sc[$ssc][0][$eta+$j][4]+=$ge;
        if ($eta+$j>$sc[$ssc][1][1])$sc[$ssc][1][1]=$eta+$j;
      }

      $pos = strpos ($sc[$ssc][0][$eta][3], $hsec.':'.$hsys);
      if ($pos === false)// nicht gefunden...
      {
        if ($sc[$ssc][0][$eta][3]!='')$sc[$ssc][0][$eta][3].=' - ';
        $sc[$ssc][0][$eta][3].=$hsec.':'.$hsys;
      }
    }
  }
  else
  {
    //es ist ein neues system
    //counter f�r die anzahl der angegriffenen systeme im sektor
    if($zsecold>0)$ssc++;

    //$sss=$zsec1.':'.$zsys1;
	$sss='<a href="military.php?se='.$zsec1.'&sy='.$zsys1.'" title="Milit&auml;r">'.$zsec1.':'.$zsys1.'</a>';
    $eta=$t1;
    if ($a1==1)$sc[$ssc][0][$eta][0]=$ge;//atter
    if ($a1==2)$sc[$ssc][0][$eta][1]=$ge;//deffer
    $sc[$ssc][1][0]=$zsys1;//system
	$sc[$ssc][1]['sector']=$zsec1;//sector
	$sc[$ssc][1]['show_ally_secstatus']=$show_ally_secstatus;//show_ally_secstatus
	
	if(!isset($sc[$ssc][1][1])){
		$sc[$ssc][1][1]=0;
	}

    if ($eta>$sc[$ssc][1][1]){
		$sc[$ssc][1][1]=$eta;//maxeta
	}
    if ($a1==1)
    {
      $sc[$ssc][0][$eta][2]=$hsec.':'.$hsys;
    }
    else
    //defferliste
    {
      $sc[$ssc][0][$eta][3]=$hsec.':'.$hsys;
      for ($j=0;$j<=$at1;$j++)
      {
        $sc[$ssc][0][$eta+$j][4]+=$ge;
        if ($eta+$j>$sc[$ssc][1][1])$sc[$ssc][1][1]=$eta+$j;
      }
    }
  }

  $as1=$a1;
  if ($a1==0) $a1=$ss_lang['systemverteidigung'];
  elseif ($a1==1) {$a1=$ss_lang['angriff']; $cl='ccr';}
  elseif ($a1==2) {$a1=$ss_lang['verteidigung'].' ('.$at1.')'; $cl='ccg';}
  elseif ($a1==3) {$a1=$ss_lang['rueckflug']; $cl='cc';}
  elseif ($a1==4) {$a1=$ss_lang['archaeologie']; $cl='cc';}

  if ($a1[0]==$ss_lang['verteidigung'][0] && $t1==0) {$a1=$ss_lang['verteidige'];$t1=$at1;$cl='ccy';}

  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  //rasse und allytag auslesen
  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  
  $allytagscan='';
  $zally='';
  $hv=explode("-",$user_id);
  $uid=$hv[0]; //so stellt man die user_id der flotte fest, einfach splitten
  if ($uid!=$ums_user_id){
  	//allygegner/-verb�ndete
  	//allytag des deffers/atters auslesen
	$db_daten=mysql_query("SELECT allytag, rasse, status FROM de_user_data WHERE user_id='$uid'",$db);
    $row = mysql_fetch_array($db_daten);
	if ($row["status"]==1) $zally = $row["allytag"];

	$rasse_id=$row['rasse'];
		
	if (in_array($zally, $allyfeinde) OR in_array($zally, $allypartner)) $allytagscan=$zally;
	
	//eigene ally
	if($zally==$ownally)$allytagscan=$zally;
	
    //geheimdienst
  	//daten aus der db holen, wenn es nicht der spieler selbst ist
    $db_daten=mysql_query("SELECT rasse, allytag FROM de_user_scan WHERE user_id='$ums_user_id' AND zuser_id='$uid'",$db);
    
    $scan_vorhanden = mysql_num_rows($db_daten);
    if ($scan_vorhanden==1){
      $row = mysql_fetch_array($db_daten);
      //allytag zuweisen, wenn noch nichts vorliegt, sonst sind die daten veraltet
  	  if($allytagscan=='')$allytagscan=$row["allytag"];
    }
  }
  else//der spieler selbst soll angezeigt werden
  {
	  $rasse_id=$_SESSION['ums_rasse'];
    $allytagscan=$ownally;
  }

  $rasse='&nbsp;';
  if($rasse_id==1)$rasse='<img src="'.$ums_gpfad.'g/r/raceE.png" title="Die Ewigen" width="16px" height="16px">';
  if($rasse_id==2)$rasse='<img src="'.$ums_gpfad.'g/r/raceI.png" title="Ishtar" width="16px" height="16px">';
  if($rasse_id==3)$rasse='<img src="'.$ums_gpfad.'g/r/raceK.png" title="K&#180;Tharr" width="16px" height="16px">';
  if($rasse_id==4)$rasse='<img src="'.$ums_gpfad.'g/r/raceZ.png" title="Z&#180;tah-ara" width="16px" height="16px">';


//die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
/*
$fp=0;
for($s=81;$s<=90;$s++){
	
  if($as1==1){ //Atter
    if($rasse_id==4 && $s==83){
      //$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
    }else{
      $fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
    }
  }else{ //Deffer
    $fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
  }
}
*/
	//die Flottenpunkte zusammenrechnen, wobei feindliche Z-Zerren nicht erkannt werden können
	$fp=0;
	for($s=81;$s<=90;$s++){
		
		if($as1==1){ //Atter
			if($rasse_id==4 && $s==83){
				//gatarnte Einheiten
				//$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
			}else{
				$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
			}
		}else{ //Deffer
			$fp=$fp+$unit[$rasse_id-1][$s-81][4]*mysql_result($flotten, $i, 'e'.$s);
		}
	}

	//die Flottenpunkte für die Einzelausgabe sichern
	if ($as1==1){
		//Angriff
		if(!isset($sc[$ssc][0][$eta]['fp_atter'])){
			$sc[$ssc][0][$eta]['fp_atter']=0;
		}

		$sc[$ssc][0][$eta]['fp_atter']+=$fp;
	}elseif ($as1==2) {
		//Verteidigung
		if(!isset($sc[$ssc][0][$eta]['fp_deffer'])){
			$sc[$ssc][0][$eta]['fp_deffer']=0;
		}

		$sc[$ssc][0][$eta]['fp_deffer']+=$fp;

		//evtl. bleibt er ein paar KT vor Ort beim Deffen, also ggf. die Folgeticks berechnen
		for ($j=1;$j<=$at1;$j++){

			if(isset($sc[$ssc][0][$eta+$j]['fp_deffer_3'])){
				$sc[$ssc][0][$eta+$j]['fp_deffer_3']+=$fp;
			}else{
				$sc[$ssc][0][$eta+$j]['fp_deffer_3']=$fp;
			}
		}

	}
















  echo '<tr>';
  echo '<td class="cc"><b>'.$sss.'</b></td>';
  echo '<td class="'.$cl.'">'.$hsec.':'.$hsys.'</td>';
  echo '<td class="'.$cl.'">'.$rasse.'</td>';
  echo '<td class="'.$cl.'">'.utf8_encode_fix($allytagscan).'</td>';
  echo '<td class="'.$cl.'">'.$a1.'</td>';
  echo '<td class="'.$cl.'">'.$t1.'</td>';
  echo '<td class="'.$cl.'">'.number_format($ge, 0,"",".").'</td>';
  if($sv_hide_fp_in_secstatus!=1){
    echo '<td class="'.$cl.'" title="'.number_format($fp, 0,"",".").'">'.formatMasseinheit($fp, 2).'</td>';
  }else{
    echo '<td class="'.$cl.'">N/A</td>';
  }
  //SAMH
  echo '<td class="'.$cl.'">
	<a href="secret.php?a=s&zsec1='.$hsec.'&zsys1='.$hsys.'" title="Sonde">S</a>
	&nbsp;<a href="secret.php?a=a&zsec2='.$hsec.'&zsys2='.$hsys.'" title="Agenteneinsatz">A</a>
	&nbsp;<a href="military.php?se='.$hsec.'&sy='.$hsys.'" title="Flotte">F</a>
	&nbsp;<a href="details.php?se='.$hsec.'&sy='.$hsys.'" title="Hyperfunk">H</a></td>';
  echo '</tr>';
  
  //schauen ob eine neue eta kommt, bzw. ob es der letzte datensatz ist
  if($t1!=$eta OR $i==$fa-1)
  {
    //neue eta, schauen ob man eine vorhergehende zusammenrechnen muß
    //if ($eta!=-1)
  }
  $zsecold=$zsec1;$zsysold=$zsys1;
}

//echo '</table><br><br>';
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td colspan="8" class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>

<?php
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//jetzt die Übersicht der einzelnen system ausgeben
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
if (isset($sc) && $sc!='')
for ($i=0;$i<=$ssc;$i++){
  $jsirc1='';
  $jsirc2='';
  $jsirc3='';
  echo '<br><table border="0" cellpadding="0" cellspacing="0">';
  echo '<tr align="center">';
  echo '<td width="13" height="37" class="rol">&nbsp;</td>';
  echo '<td align="center" class="ro"><div class="cellu">'.$ss_lang['systemstatus'].' '.$sc[$i][1]['sector'].':'.$sc[$i][1][0].
	' - <a href="secret.php?a=s&zsec1='.$sc[$i][1]['sector'].'&zsys1='.$sc[$i][1][0].'" title="Sonde">S</a>
	&nbsp;<a href="secret.php?a=a&zsec2='.$sc[$i][1]['sector'].'&zsys2='.$sc[$i][1][0].'" title="Agenteneinsatz">A</a>
	&nbsp;<a href="military.php?se='.$sc[$i][1]['sector'].'&sy='.$sc[$i][1][0].'" title="Flotte">F</a>
	&nbsp;<a href="details.php?se='.$sc[$i][1]['sector'].'&sy='.$sc[$i][1][0].'" title="Hyperfunk">H</a>';

  echo '&nbsp;(Allianzeinsicht bis: '.date("H:i:s d.m.Y", $sc[$i][1]['show_ally_secstatus']).')';
  
  echo '</div></td>';
  echo '<td width="13" class="ror">&nbsp;</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<td width="13" class="rl">&nbsp;</td>';
  echo '<td colspan="1">';

  echo '<table width="560" border="0" cellpadding="0" cellspacing="1">';

  echo '<tr>';
  echo '<td width="30" class="tc"><b>'.$ss_lang['eta'].'</td>';
  echo '<td width="100" class="tc"><b>'.$ss_lang['inc'].'</td>';
  echo '<td width="100" class="tc"><b>'.$ss_lang['def'].'</td>';
  //echo '<td width="90" class="tc"><b>'.$ss_lang['def'].'(3)</td>';
  echo '<td width="180" colspan="2" class="tc" title="Dieser Wert sind die Flottenpunkte. Getarnte Einheiten der Angreifer, wie die Z-Zerst&ouml;rer, werden nicht mit eingerechnet.">FP <img id="info" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif"></td>';
  echo '<td width="80" class="tc"><b>'.$ss_lang['angreifer'].'</td>';
  echo '<td width="80" class="tc"><b>'.$ss_lang['verteidiger'].'</td>';
  echo '</tr>';  

/*
  echo '<tr>';
  echo '<td width="30" class="tc"><b>'.$ss_lang['eta'].'</td>';
  echo '<td width="70" class="tc"><b>'.$ss_lang['inc'].'</td>';
  echo '<td width="130" class="tc" colspan="2"><b>'.$ss_lang['def'].'</td>';
  echo '<td width="130" class="tc" colspan="2"><b>'.$ss_lang['def'].'(3)</td>';
  echo '<td width="100" class="tc"><b>'.$ss_lang['angreifer'].'</td>';
  echo '<td width="100" class="tc"><b>'.$ss_lang['verteidiger'].'</td>';
  echo '</tr>';
*/

  //die einzelne etas ausgeben
  for ($j=0; $j<=$sc[$i][1][1];$j++)  {
    //wenn es schiffe bei der eta gibt, dann eine zeile ausgeben
	if(	(isset($sc[$i][0][$j][0]) && $sc[$i][0][$j][0]>0) || 
		(isset($sc[$i][0][$j][1]) && $sc[$i][0][$j][1]>0) || 
		(isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][2]>0) || 
		(isset($sc[$i][0][$j][2]) && $sc[$i][0][$j][3]>0) || 
		(isset($sc[$i][0][$j][4]) && $sc[$i][0][$j][4]>0)
		){
      //verh�ltniss atter/deffer berechnen
	  //nur berechnen, wenn es atter gibt

	  if(!isset($sc[$i][0][$j][1])){
		$sc[$i][0][$j][1]=0;
	  }

	  if(!isset($sc[$i][0][$j][4])){
		$sc[$i][0][$j][4]=0;
	  }

	  if(!isset($sc[$i][0][$j][5])){
		$sc[$i][0][$j][5]=0;
	  }

	  if(!isset($sc[$i][0][$j][6])){
		$sc[$i][0][$j][6]=0;
	  }


      if ($sc[$i][0][$j][0]>0){
        $sc[$i][0][$j][5]=$sc[$i][0][$j][1]/$sc[$i][0][$j][0];
        $sc[$i][0][$j][6]=$sc[$i][0][$j][4]/$sc[$i][0][$j][0];
      }
      else
      {
        $sc[$i][0][$j][5]=0;//deffer
        $sc[$i][0][$j][6]=0;//deffer3
      }

      if ($sc[$i][0][$j][5]>0)$v1=' (1:'.number_format($sc[$i][0][$j][5], 1,",",".").')';else $v1='';
      if ($sc[$i][0][$j][6]>0)$v3=' (1:'.number_format($sc[$i][0][$j][6], 1,",",".").')';else $v3='';

	  if(!isset($sc[$i][0][$j][3])){
		$sc[$i][0][$j][3]=0;
	  }


	  /*
      echo '<tr>';
      echo '<td class="cc">'.$j.'</td>';
      echo '<td class="ccr">'.number_format($sc[$i][0][$j][0], 0,"",".").'</td>';
      echo '<td class="ccg">'.number_format($sc[$i][0][$j][1], 0,"",".").'</td>';
      echo '<td class="cc">'.$v1.'</td>';
      echo '<td class="ccg">'.number_format($sc[$i][0][$j][4], 0,"",".").'</td>';
      echo '<td class="cc">'.$v3.'</td>';
      echo '<td class="ccr">'.$sc[$i][0][$j][2].'</td>';
      echo '<td class="ccg">'.$sc[$i][0][$j][3].'</td>';
	  echo '</tr>';
	  */

	if(!isset($sc[$i][0][$j][0])){
		$sc[$i][0][$j][0]=0;
	}

	if(!isset($sc[$i][0][$j][2])){
		$sc[$i][0][$j][2]='';
	}

	if(!isset($sc[$i][0][$j][3])){
		$sc[$i][0][$j][3]='';
	}

	if(!isset($sc[$i][0][$j]['fp_atter'])){
		$sc[$i][0][$j]['fp_atter']=0;
	}

	if(!isset($sc[$i][0][$j]['fp_deffer'])){
		$sc[$i][0][$j]['fp_deffer']=0;
	}

	if(!isset($sc[$i][0][$j]['fp_deffer_3'])){
		$sc[$i][0][$j]['fp_deffer_3']=0;
	}	  
	  
	  echo '<tr>';
	  echo '<td class="cc">'.$j.'</td>'; //ETA
	  echo '<td class="ccr">'.number_format($sc[$i][0][$j][0], 0,"",".").'</td>';//INC
	  //echo '<td class="ccg">'.number_format($sc[$i][0][$j][1], 0,"",".").'</td>';//DEFF
	  //echo '<td class="cc">'.$v1.'</td>';//Verhältnis Atter/Deffer in der ETA
	  echo '<td class="ccg">'.number_format($sc[$i][0][$j][4], 0,"",".").'</td>';//DEFF3
	  //echo '<td class="cc">'.$v3.'</td>';//Verhältnis Atter/Deffer in der 3er ETA
	  echo '<td class="ccr" title="'.number_format($sc[$i][0][$j]['fp_atter'], 0,"",".").'">'.formatMasseinheit($sc[$i][0][$j]['fp_atter'],2).'</td>';
	  echo '<td class="ccg" title="'.number_format($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'], 0,"",".").'">'.formatMasseinheit($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'],2).'</td>';

	  echo '<td class="ccr">'.$sc[$i][0][$j][2].'</td>';
	  echo '<td class="ccg">'.$sc[$i][0][$j][3].'</td>';
	  echo '</tr>';	  


      //javascript f�rs irc
      //if ($v1=='')$v1='(1:0,0)';
      //if ($v3=='')$v3='(1:0,0)';
      if ($sc[$i][0][$j][0]>0 || $sc[$i][0][$j][2]>0 || $sc[$i][0][$j][3]>0){

		/*
        if ($jsirc1!='')$jsirc1.=",";
        $jsirc1.="'".$j."','".number_format($sc[$i][0][$j][0], 0,"",".")."','".number_format($sc[$i][0][$j][1], 0,"",".")."','".
		 $v1."','".number_format($sc[$i][0][$j][4], 0,"",".")."','".$v3."'";
		*/

		$gesamt_fp=$sc[$i][0][$j]['fp_atter']+$sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'];
					
		if($gesamt_fp>0){
			$atter_percent=$sc[$i][0][$j]['fp_atter']*100/$gesamt_fp;
			$deffer_percent=($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'])*100/$gesamt_fp;
		}else{
			$atter_percent='0.00';
			$deffer_percent='0.00';
		}

		$fp_atter=formatMasseinheit($sc[$i][0][$j]['fp_atter'],2).' / '.number_format($atter_percent, 2,",",".").'%';

		$fp_deffer=formatMasseinheit($sc[$i][0][$j]['fp_deffer']+$sc[$i][0][$j]['fp_deffer_3'],2).' / '.number_format($deffer_percent, 2,",",".").'%';
		
		if ($jsirc1!='')$jsirc1.=",";
		$jsirc1.="'".$j."','".number_format($sc[$i][0][$j][0], 0,"",".")."','".number_format($sc[$i][0][$j][1], 0,"",".")."','".
		$v1."','".number_format($sc[$i][0][$j][4], 0,"",".")."','".$v3."','".$fp_atter."','".$fp_deffer."'";		

      }
	 
	  //String: Atter
      if ($sc[$i][0][$j][2]>0)
      {
        if ($jsirc2!='')$jsirc2.=",";
        $jsirc2.="'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][2]."'";
      }

	  //string: Deffer
      if ($sc[$i][0][$j][3]>0)
      {
        if ($jsirc3!='')$jsirc3.=",";
        $jsirc3.="'(".$ss_lang['eta'].$j.") ".$sc[$i][0][$j][3]."'";
      }

    }
  }
  
  echo '</table>';
  echo '</td>';
  echo '<td width="13" class="rr">&nbsp;</td>';
  echo '</tr>';

$hzsys=$sc[$i][1][0];
$sector=$sc[$i][1]['sector'];  
echo '<tr>';
echo '<td width="13" class="rl">&nbsp;</td>';
echo '<td align="center" id="s'.$sector.'_'.$sc[$i][1][0].'">';
echo '<table border="0" cellpadding="0" cellspacing="1" width="100%">';
echo '<tr>';


echo "<td class=\"cc\"><input type=\"button\" value=\"".$ss_lang['text']."\" onclick=\"deirc(1,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
echo "<td class=\"cc\"><input type=\"button\" value=\"WhatsApp\" onclick=\"deirc(3,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";

/*
echo "<td class=\"cc\"><input type=\"button\" value=\"".$ss_lang['irc']."\" onclick=\"deirc(0,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
echo "<td class=\"cc\"><input type=\"button\" value=\"".$ss_lang['text']."\" onclick=\"deirc(1,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
echo "<td class=\"cc\"><input type=\"button\" value=\"einzeilig\" onclick=\"deirc(2,$sector,$hzsys,new Array($jsirc1),new Array($jsirc2),new Array($jsirc3))\"></td>";
*/

echo '<td class="cc"><input type="checkbox" name="a'.$sector.'_'.$sc[$i][1][0].'" checked> '.$ss_lang['angreiferanzeigen'].'</td>';
echo '<td class="cc"><input type="checkbox" name="d'.$sector.'_'.$sc[$i][1][0].'"> '.$ss_lang['verteidigeranzeigen'].'</td>';
echo '</tr>';
echo '</table>';
echo '</td>';
echo '<td width="13" class="rr">&nbsp;</td>';
echo '</tr>';

  echo '<tr height="20">';
  echo '<td class="rul" width="13">&nbsp;</td>';
  echo '<td class="ru">&nbsp;</td>';
  echo '<td class="rur" width="13">&nbsp;</td>';
  echo '</tr>';
  echo '</table>';
	}
}
?>
</div>
<?php include "fooban.php"; ?>
</body>
</html>