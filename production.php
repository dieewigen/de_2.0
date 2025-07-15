<?php
include('inc/header.inc.php');
include('lib/transactioni.lib.php');
include('inc/userartefact.inc.php');
include('inc/schiffsdaten.inc.php');
include('inc/lang/'.$sv_server_lang.'_production.lang.php');
include('inc/lang/'.$sv_server_lang.'_defense.lang.php');
include('inc/'.$sv_server_lang.'_links.inc.php');
include('inc/sabotage.inc.php');
include('functions.php');
include('tickler/kt_einheitendaten.php');
include('lib/map_system_defs.inc.php');

$production_lang['klassennamen']=array('J&auml;ger','Jagdboot','Zerst&ouml;rer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Tr&auml;ger',
'Frachter','Titan','Orbitalj&auml;ger-Basis','Flugk&ouml;rper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');

//$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, techs, newtrans, newnews, design3 AS design, sc2, spec1, spec3 FROM de_user_data WHERE user_id='$ums_user_id'");
//$row = mysqli_fetch_array($db_daten);
$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$ps=loadPlayerStorage($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$punkte=$row["score"];$techs=$row["techs"];$defenseexp=$row["defenseexp"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$mysc2=$row["sc2"];$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$spec1=$row['spec1'];$spec3=$row['spec3'];



//Design ist jetzt fix
$design=0;

//maximalen tick auslesen
$result  = mysqli_query($GLOBALS['dbi'],"SELECT wt AS tick FROM de_system LIMIT 1");
$row     = mysqli_fetch_array($result);
$maxtick = $row['tick'];

//spezialisierung trägerkapazität
if($spec3==2){
	for($i=0;$i<count($sv_schiffsdaten[$ums_rasse]);$i++){
		$sv_schiffsdaten[$ums_rasse-1][$i][1]= floor($sv_schiffsdaten[$ums_rasse-1][$i][1] * 1.2);
	}	
}

////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT id, level FROM de_user_artefact WHERE id=1 AND user_id='$ums_user_id'");
$artbonus_fleet=0;
while($row = mysqli_fetch_array($db_daten)){
  $artbonus_fleet=$artbonus_fleet+$ua_werte[$row['id']-1][$row['level']-1][0];
}

if($artbonus_fleet>5){
	$artbonus_fleet=5;
}

$db_daten=mysql_query("SELECT id, level FROM de_user_artefact WHERE (id=2 OR id=8 OR id=9) AND user_id='$ums_user_id'",$db);
$artbonus_def=0;$artbonus2=0;$artbonus3=0;
while($row = mysql_fetch_array($db_daten)){
  if($row['id']==2)$artbonus_def=$artbonus_def+$ua_werte[$row['id']-1][$row['level']-1][0];
  elseif($row['id']==8)$artbonus2=$artbonus2+$ua_werte[$row['id']-1][$row['level']-1][0];
  elseif($row['id']==9)$artbonus3=$artbonus3+$ua_werte[$row['id']-1][$row['level']-1][0];
}
if($artbonus_def>5)$artbonus_def=5;


////////////////////////////////////////////////////////////////////////////////
//defenseboni berechnen
////////////////////////////////////////////////////////////////////////////////
$rangnamen=array("Der Erhabene", "Alpha","Beta","Gamma","Delta","Epsilon","Zeta","Eta","Theta","Iota","Kappa","Lambda","My","Ny","Xi","Omikron","Pi","Rho","Sigma","Tau","Ypsilon","Phi","Chi","Psi","Omega");
$defense_level=24-getfleetlevel($defenseexp);
include 'lib/defenseboni.lib.php';

//test auf spezialisierung bauzeit
if($spec1==1)$defense_bonus_buildtime+=50;

//namen des planetaren schildes aus der db auslesen
$db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id=24",$db);
$row = mysql_fetch_array($db_daten);
$ps_name=$row['tech_name'];

//spezialisierung schildst�rke
if($spec3==1)$defense_bonus_ps+=10;

$infostring = $defense_lang['kostenreduz'].$ua_name[1].$defense_lang['artefakte'].number_format($artbonus_def, 2,",",".").'% (max. 5,00%)<br>'.
$ua_name[7].'-'.$defense_lang['artefakt'].'-'.$defense_lang['angriffskraftbonus'].': '.number_format($artbonus2, 2,",",".").'%<br>'.
$ua_name[8].'-'.$defense_lang['artefakt'].'-'.$defense_lang['laehmkraftbonus'].': '.number_format($artbonus3, 2,",",".").'%<br>'.
$defense_lang['erfahrungspunkte'].': '.number_format($defenseexp, 0,",",".").' ('.$rangnamen[getfleetlevel($defenseexp)].')<br>- '.
$ps_name.'-'.$defense_lang['bonus'].': '.$defense_bonus_ps.'%<br>- '.
$defense_lang['bauzeitreduzierung'].': '.$defense_bonus_buildtime.'%<br>'.
$defense_lang['bauzeitreduzierung1'].'<br>- '.		
$defense_lang['erfahrungspunkte'].'-'.$defense_lang['angriffskraftbonus'].'/'.$defense_lang['laehmkraftbonus'].': '.
number_format((24-getfleetlevel($defenseexp))*0.4, 2,",",".").'%<br><font color=#00FF00>'.$defense_lang['spezialfaehigkeiten'].':</font><br>';

//angriffskraft
if($defense_bonus_feuerkraft[0]>0)$infostring.='- '.$defense_lang['angriffskraftbonus'].': '.$defense_bonus_feuerkraft[0].'% '.$defense_lang['wahrscheinlichkeit'].': '.$defense_bonus_feuerkraft[1].'%';

$defstatus = $defense_lang['statusinformationen'].'&'.$infostring;

////////////////////////////////////////////////////////////////////////////////
//feststellen ob die raumwerft sabotiert ist
////////////////////////////////////////////////////////////////////////////////
if($maxtick<$mysc2+$sv_sabotage[8][0] AND $mysc2>$sv_sabotage[8][0])$sabotage=1;else $sabotage=0;

/*
if($_REQUEST["setdesign"]){
  $design=intval($_REQUEST["setdesign"]);
  mysql_query("UPDATE de_user_data SET design3='$design' WHERE user_id = '$ums_user_id'",$db);	
}*/


?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$production_lang['produktion']?></title>
<?php include "cssinclude.php";
echo '<script language="javascript">';
//Trägerbonus
if($spec3==2) echo 'var traegerbonus=1.2;'; else  echo 'var traegerbonus=1;';  

//////////////////////////////////////////////////////////////////////////
//tooltip-daten generieren
//////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT * FROM de_tech_data WHERE tech_id>80 AND tech_id<110 ORDER BY tech_id");
$c1=0;$i=81;unset($tooltips);
while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
	$i=$row['tech_id'];
	
	if($row['tech_id']<100){
		$unit_index=$row['tech_id']-81;
	}else{
		$unit_index=$row['tech_id']-90;
	}
	
	//klasse
	$zstr='<font color=#D265FF>'.$production_lang['klasse'].': '.$production_lang['klassennamen'][$unit_index].'</font>';
	//punkte
	$zstr.='<br><font color=#FFFA65>'.$production_lang['punkte'].': '.number_format($unit[$_SESSION['ums_rasse']-1][$unit_index][4], 0,"",".").'</font>';

	
	if($i<100){
		//reisezeit
		$zstr.='<br><br>'.$production_lang['reisezeit'].': '.$sv_schiffsdaten[$ums_rasse-1][$unit_index][0];
		//transportkapazität
		if ($sv_schiffsdaten[$ums_rasse-1][$unit_index][1]>0)$zstr.='<br>'.$production_lang['kapazitaet1'].': '.$sv_schiffsdaten[$ums_rasse-1][$unit_index][1];
		//ben. transportkapazität
		if ($sv_schiffsdaten[$ums_rasse-1][$unit_index][2]>0)$zstr.='<br>'.$production_lang['kapazitaet2'].': '.$sv_schiffsdaten[$ums_rasse-1][$unit_index][2];
		//frachtkapazität
		if (isset($unit[$ums_rasse-1][$unit_index]['fk']) && $unit[$ums_rasse-1][$unit_index]['fk']>0)$zstr.='<br>Frachtkapazit&auml;t: '.$unit[$ums_rasse-1][$unit_index]['fk'];
	}

	//waffenarten
	//konventionell
	if($unit[$ums_rasse-1][$unit_index][2]>0)$wv='<font color=#2DFF11>'.$production_lang['waffenvorhandenja'].'</font>';
	 else $wv='<font color=#ED0909>'.$production_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#9D4B15>'.$production_lang['waffengattung1'].':</font> '.$wv;

	//klassenziel
	if($unit[$ums_rasse-1][$unit_index][2]>0){
	  $zstr.='<br><font color=#ED9409>-'.$production_lang['klasseziel1'].': '.$production_lang['klassennamen'][$kampfmatrix[$unit_index][0]].'</font>';
	  $zstr.='<br><font color=#F0BA66>-'.$production_lang['klasseziel2'].': '.$production_lang['klassennamen'][$kampfmatrix[$unit_index][2]].'</font>';
	}

	//emp
	if($unit[$ums_rasse-1][$unit_index][3]>0){
		$wv='<font color=#2DFF11>'.$production_lang['waffenvorhandenja'].'</font>';
	}else{
		$wv='<font color=#ED0909>'.$production_lang['waffenvorhandennein'].'</font>';
	}
	$zstr.='<br><br><font color=#15629D>'.$production_lang['waffengattung2'].':</font> '.$wv;

	//klassenziel
	if($unit[$ums_rasse-1][$unit_index][3]>0){
		$zstr.='<br><font color=#ED9409>-'.$production_lang['klasseziel1'].': '.$production_lang['klassennamen'][$blockmatrix[$unit_index][0]].'</font>';
		$zstr.='<br><font color=#F0BA66>-'.$production_lang['klasseziel2'].': '.$production_lang['klassennamen'][$blockmatrix[$unit_index][2]].'</font>';
	}

	//besonderheiten
	if($unit_index==1)$zstr.='<br><br><font color=#2DFF11>'.$production_lang['besonderheitjagdboot'].'</font>';
	if($unit_index==3)$zstr.='<br><br><font color=#2DFF11>'.$production_lang['besonderheitkreuzer'].'</font>';
	if($unit_index==4)$zstr.='<br><br><font color=#2DFF11>'.$production_lang['besonderheitschlachtschiff'].'</font>';
	if($unit_index==6)$zstr.='<br><br><font color=#2DFF11>'.$production_lang['besonderheittransmitterschiff'].'</font>';


	$tooltips[$c1]=getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'&'.$zstr;
	$c1++;

    //$i++;
  }
?>
</script>

<?php
echo '<script type="text/javascript">var abf='.$artbonus_fleet.';var abd='.$artbonus_def.';var ab=0;</script>';
echo '<script src="js/produktion'.$ums_rasse.'.js?'.filemtime($_SERVER['DOCUMENT_ROOT'].'/js/produktion'.$ums_rasse.'.js').'" type="text/javascript"></script>';
?>
</head>
<body>
<?php
if(isset($_POST['submit']) && $sabotage==0){//ja, es wurde ein button gedrueckt
	//transaktionsbeginn
	if (setLock($ums_user_id)){
		//$need_storage_res=array();
		//nochmal die vorandenen Rohstoffe laden
		$row=loadPlayerData($_SESSION['ums_user_id']);
		$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];		
		for ($i=81; $i<=109; $i++){
			$h=intval($_POST['b'.$i] ?? 0);
			if ($h>=1){ //es wurde ein wert eingegeben und er ist ok h=anzahl des auftrags
				if($i<100){
					$unit_index=$i-81;
					$artbonus=$artbonus_fleet;
				}else{
					$unit_index=$i-90;
					$artbonus=$artbonus_def;
				}
				
				//ben�tigte rohstoffe, abzgl. artefaktbonus
				$benrestyp01=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][0]-$unit[$_SESSION['ums_rasse']-1][$unit_index][5][0]*$artbonus/100;
				$benrestyp02=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][1]-$unit[$_SESSION['ums_rasse']-1][$unit_index][5][1]*$artbonus/100;
				$benrestyp03=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][2]-$unit[$_SESSION['ums_rasse']-1][$unit_index][5][2]*$artbonus/100;
				$benrestyp04=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][3]-$unit[$_SESSION['ums_rasse']-1][$unit_index][5][3]*$artbonus/100;
				$benrestyp05=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][4]-$unit[$_SESSION['ums_rasse']-1][$unit_index][5][4]*$artbonus/100;
				
				$tech_score=$unit[$_SESSION['ums_rasse']-1][$unit_index][4];
				
				$tech_ticks=$unit[$_SESSION['ums_rasse']-1][$unit_index]['bz'];

				//bauzeitverringerung durch spezialisierung
				$tech_ticks=$unit[$_SESSION['ums_rasse']-1][$unit_index]['bz'];
				if($i<100 && $spec1==2){
					$tech_ticks=round($tech_ticks/2);
				}elseif($i>=100){
					$tech_ticks=ceil($tech_ticks-($tech_ticks*$defense_bonus_buildtime/100));
					if($tech_ticks<1)$tech_ticks=1;
				}

				if(hasTech($pt, $i)){
					$fehlermsg='';
				}else{
					$h=0;
					$fehlermsg='<font color="FF0000">'.$production_lang['vorbedingung'];
				}
				
				//festellen wieviele schiffe man bauen kann
				$z=0;$z1=0;
				//test auf M
				if($benrestyp01>0){
				  $maxschiffe=floor($restyp01/$benrestyp01);
				  if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
				  $z=$z1;
				}
				//test auf D
				if($benrestyp02>0){
				  $maxschiffe=floor($restyp02/$benrestyp02);
				  if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
				  if ($z1<$z)$z=$z1;
				}
				//test auf I
				if($benrestyp03>0){
				  $maxschiffe=floor($restyp03/$benrestyp03);
				  if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
				  if ($z1<$z)$z=$z1;
				}
				//test auf E
				if($benrestyp04>0){
				  $maxschiffe=floor($restyp04/$benrestyp04);
				  if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
				  if ($z1<$z)$z=$z1;
				}
				//test auf T
				if($benrestyp05>0){
				  $maxschiffe=floor($restyp05/$benrestyp05);
				  if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
				  if ($z1<$z)$z=$z1;
				}

				//test auf Items, wenn vorhanden
				if(!empty($unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost'])){
					$einzelkosten=explode(';', $unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost']);
					foreach ($einzelkosten as $value) {
						$parts=explode("x", $value);
						
						$maxschiffe=floor($ps[$value[1]]['item_amount']/$parts[1]);
						if ($maxschiffe>$h)$z1=$h;else $z1=$maxschiffe;
						if ($z1<$z)$z=$z1;						
						
					}
				}

				//rohstoffabzug berechnen
				$restyp01=$restyp01-($z*$benrestyp01);
				$restyp02=$restyp02-($z*$benrestyp02);
				$restyp03=$restyp03-($z*$benrestyp03);
				$restyp04=$restyp04-($z*$benrestyp04);
				$restyp05=$restyp05-($z*$benrestyp05);
				
				if($z>0){
					//items abziehen
					if(!empty($unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost'])){
						$einzelkosten=explode(';', $unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost']);
						foreach ($einzelkosten as $value) {
							$parts=explode("x", $value);
							change_storage_amount($_SESSION['ums_user_id'], $value[1], $parts[1]*$z*-1);
							//Lageranzahl neu berechnen
							$ps[$value[1]]['item_amount']-=$parts[1]*$z;
						}
					}
					
					$buildscore=$z*$tech_score;
					mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score) VALUES ($ums_user_id, $i, $z, $tech_ticks, $buildscore)");
				}
			}
		}
		//aktualisiert die rohstoffe
		$gr01=$gr01-$restyp01;
		$gr02=$gr02-$restyp02;
		$gr03=$gr03-$restyp03;
		$gr04=$gr04-$restyp04;
		$gr05=$gr05-$restyp05;
		mysqli_query($GLOBALS['dbi'],"update de_user_data set restyp01 = restyp01 - $gr01,
		 restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
		 restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE user_id = '$ums_user_id'");

		//transaktionsende
		$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
			  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
			print($production_lang['releaselock'].$ums_user_id.$production_lang['releaselock2']."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$production_lang['releaselock3'].'</font><br><br>';

}//submit ende

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

echo '
<a href="production.php" title="Einheitenproduktion"><img src="'.$ums_gpfad.'g/symbol19.png" border="0" width="64px" heigth="64px"></a> 
<a href="recycling.php" title="Recycling&Hier k&ouml;nnen Einheiten der Heimatflotte und Verteidigungseinheiten recycelt werden."><img src="'.$ums_gpfad.'g/symbol24.png" border="0" width="64px" heigth="64px"></a>';
if(!isset($sv_deactivate_vsystems) || $sv_deactivate_vsystems != 1){
	echo '<a href="specialship.php" title="Basisstern"><img src="'.$ums_gpfad.'g/symbol27.png" border="0" width="64px" heigth="64px"></a>';
}
echo'
<a href="unitinfo.php" title="Einheiteninformationen"><img src="'.$ums_gpfad.'g/symbol26.png" border="0" width="64px" heigth="64px"></a>
';

//feststellen ob eine sabotage vorliegt und dann abbrechen
if($sabotage==1){
  $emsg.='<table width="600px"><tr><td class="ccr">';
  $emsg.=$production_lang['sabotage_aktiv'];
  $emsg.='</td></tr></table>';
  echo $emsg;
  
  die('</body></html>');
}

/*
if ($techs[13]==0){
	$techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=13";
	$db_tech=mysql_query($techcheck,$db);
	$row_techcheck = mysql_fetch_array($db_tech);

	//echo $production_lang[eswirdeine].$row_techcheck[tech_name].$production_lang[benoetigt];

	echo '<br>';
	rahmen_oben($production_lang[fehlendesgebaeude]);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=13" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_13.jpg" border="0"></a></td>
	<td valign="top">'.$production_lang[gebaeudeinfo].': '.$row_techcheck[tech_name].'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{
*/

echo '<div align="center">';
?>
<form action="production.php" method="POST" name="produktion">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37px">
<td width="13px"class="rol">&nbsp;</td>
<td width="200px" class="ro">&nbsp;&nbsp;<?php echo $production_lang['einheit'];?>:</td>
<td width="51px" align="center" class="ro"><img src="g/icon1.png" style="width: 20px; height: auto;" title="Multiplex"></td>
<td width="51px" align="center" class="ro"><img src="g/icon2.png" style="width: 20px; height: auto;" title="Dyharra"></td>
<td width="51px" align="center" class="ro"><img src="g/icon3.png" style="width: 20px; height: auto;" title="Iradium"></td>
<td width="51px" align="center" class="ro"><img src="g/icon4.png" style="width: 20px; height: auto;" title="Eternium"></td>
<td width="31px" align="center" class="ro"><img src="g/icon5.png" style="width: 20px; height: auto;" title="Tronic"></td>
<td width="31px" align="center" class="ro"><?php echo $production_lang['wochen'];?></td>
<td width="55px" align="center" class="ro"><?php echo $production_lang['stueck'];?></td>
<td width="55px" align="center" class="ro"><?php echo $production_lang['bauen'];?></td>
<td width="13px" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13px" class="rl">&nbsp;</td>
<td colspan="9">
<table border="0" cellpadding="0" cellspacing="1" width="100%">
<colgroup>
<col width="198px">
<col width="50px">
<col width="50px">
<col width="50px">
<col width="50px">
<col width="30px">
<col width="30px">
<col width="50px">
<col width="50px">
</colgroup>
<?php
/////////////////////////////////////////////////////////////////////////////
//info mit artefaktbonus ausgeben
/////////////////////////////////////////////////////////////////////////////
echo '<tr valign="middle" align="center" height="25"><td class="cell1" height="25" colspan="9" align="left"><b>&nbsp;Flotteneinheiten: '.
	$production_lang['baukostenreduz'].$ua_name[0].$production_lang['artefakte'].number_format($artbonus_fleet, 2,",",".").'% (max. 5,00%)</b></td></tr>';

/////////////////////////////////////////////////////////////////////////////
//Einheiten zählen
/////////////////////////////////////////////////////////////////////////////
$ec=array();
$fid0=$ums_user_id.'-0';$fid1=$ums_user_id.'-1';$fid2=$ums_user_id.'-2';$fid3=$ums_user_id.'-3';
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT aktion, e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC");
while($row = mysqli_fetch_array($db_daten)){
	for ($i=81;$i<=90;$i++){
		if(!isset($ec[$i])){
			$ec[$i]=0;
		}
		$ec[$i]+=$row['e'.$i];
	}
}
for($i=100;$i<=104;$i++){
	$ec[$i]=$pd['e'.$i];
}

//print_r($unit);
/////////////////////////////////////////////////////////////////////////////
// Einheiten ausgeben
/////////////////////////////////////////////////////////////////////////////
$c1=0;$c2=0;$z=0;
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT  * FROM de_tech_data WHERE tech_id>80 AND tech_id<110 ORDER BY tech_id");
while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft

	if($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
	
	if($row['tech_id']<100){
		$unit_index=$row['tech_id']-81;
		$artbonus=$artbonus_fleet;
	}else{
		$unit_index=$row['tech_id']-90;
		$artbonus=$artbonus_def;
	}
	
	//bauzeit, boni mit einrechnen
	$tech_ticks=$unit[$_SESSION['ums_rasse']-1][$unit_index]['bz'];
	if($row['tech_id']<100 && $spec1==2){
		$tech_ticks=round($tech_ticks/2);
	}elseif($row['tech_id']>=100){
		$tech_ticks=ceil($tech_ticks-($tech_ticks*$defense_bonus_buildtime/100));
		if($tech_ticks<1)$tech_ticks=1;
	}
	
	
	//die Baukosten extrahieren
	//print_r($einzelkosten);
	$ben_restyp01=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][0];
	$ben_restyp02=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][1];
	$ben_restyp03=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][2];
	$ben_restyp04=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][3];
	$ben_restyp05=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][4];
	
	//zwischen Flotte und Verteidigung eine Zeile einf�gen
	if($row['tech_id']==100){
		echo '<tr valign="middle" align="center" height="25"><td class="'.$bg.'" height="25" colspan="9" align="left"><b>
		&nbsp;Verteidigungseinheiten: '.$defense_lang['statusinformationen'].' <img style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0" title="'.$defstatus.'">
		&nbsp;-&nbsp;Baukostenreduzierung: '.number_format($artbonus_def, 2,",",".").'% (max. 5,00%)</b></td></tr>';
		if($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
	}
	
	//kostet es besondere items?
	if(!empty($unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost'])){
		$item_kosten='<br>&nbsp;Zusatzkosten: ';
		$einzelkosten=explode(';', $unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost']);
		foreach ($einzelkosten as $value) {
			$parts=explode("x", $value);
			$item_kosten.='<br>&nbsp;'.$parts[1].' '.$ps[$value[1]]['item_name'];
			$item_kosten.=' - Lager: '.$ps[$value[1]]['item_amount'];
		}
	}else{
		$item_kosten='';
	}

	showeinheit_ang(getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']), $row['tech_id'], $ben_restyp01-round($ben_restyp01*$artbonus/100),
	$ben_restyp02-round($ben_restyp02*$artbonus/100), $ben_restyp03-round($ben_restyp03*$artbonus/100),
	$ben_restyp04-round($ben_restyp04*$artbonus/100), $ben_restyp05-round($ben_restyp05*$artbonus/100), $tech_ticks, $ec[$row['tech_id']], $bg,$z, hasTech($pt, $row['tech_id']), $item_kosten);


	/*
	if($design==1){
	  showeinheit2($row["tech_name"], $row["tech_id"], $row["restyp01"]-round($row["restyp01"]*$artbonus/100),
	  $row["restyp02"]-round($row["restyp02"]*$artbonus/100), $row["restyp03"]-round($row["restyp03"]*$artbonus/100),
	  $row["restyp04"]-round($row["restyp04"]*$artbonus/100), $row["restyp05"]-round($row["restyp05"]*$artbonus/100), $tech_ticks, $ec, $bg,$z);
	}else{
	  showeinheit($row["tech_name"], $row["tech_id"], $row["restyp01"]-round($row["restyp01"]*$artbonus/100),
	  $row["restyp02"]-round($row["restyp02"]*$artbonus/100), $row["restyp03"]-round($row["restyp03"]*$artbonus/100),
	  $row["restyp04"]-round($row["restyp04"]*$artbonus/100), $row["restyp05"]-round($row["restyp05"]*$artbonus/100), $tech_ticks, $ec, $bg,$z);
	}
	*/
	$z++;

}
?>
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<?php

//oberer rahmen von der echtzeitrechnung
  echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rml">&nbsp;</td>
        <td align="left" class="ro"><div class="cellu">&nbsp;Baukosten:</div></td>
        <td width="13" class="rmr">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';
?>
<table border="0" cellpadding="1" cellspacing="1">
<tr height="20" align="center">
<td class="cell" align="left" width="58">&nbsp;</td>
<td width="76" class="cell">M</td>
<td width="76" class="cell">D</td>
<td width="76" class="cell">I</td>
<td width="76" class="cell">E</td>
<td width="43" class="cell">T</td>
<td width="71" class="cell"><?=$production_lang['kapazitaet']?></td>
<td width="75" class="cell"><?=$production_lang['punkte']?></td>
</tr>
<tr height="20" align="center">
<td class="cell1" align="left">&nbsp;<?=$production_lang['summe']?>:</td>
<td class="cell1" id="m">0</td>
<td class="cell1" id="d">0</td>
<td class="cell1" id="i">0</td>
<td class="cell1" id="e">0</td>
<td class="cell1" id="t">0</td>
<td class="cell1" id="k">0</td>
<td class="cell1" id="p">0</td>
</tr>
<tr height="20">
<td class="cell" colspan="8" align="center"><input type="Submit" name="submit" value="<?=$production_lang['bauen']?>"></td>
</tr>
</table>
<?php


//zeige aktive bauauftr�ge an
//$result=mysql_query("SELECT de_user_build.anzahl, de_user_build.verbzeit, de_tech_data$ums_rasse.tech_name, de_tech_data$ums_rasse.score FROM de_user_build left join de_tech_data$ums_rasse on(de_user_build.tech_id = de_tech_data$ums_rasse.tech_id) WHERE user_id=$ums_user_id AND de_user_build.tech_id > 80 AND de_user_build.tech_id < 110 ORDER BY de_user_build.verbzeit ASC",$db);
/*
unset($technames);
$techselect='<option value="0">Bitte w&auml;hlen</option>';
$db_daten=mysql_query("SELECT * FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id>80 AND tech_id<105 ORDER BY tech_id",$db);
while($row = mysql_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
	$technames[$row['tech_id']]=$row['tech_name'];
}*/

$techdata=array();
$sql="SELECT * FROM de_tech_data WHERE tech_id>=81 AND tech_id<=104 ORDER BY tech_id ASC";
$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
//echo $sql;
while($row = mysqli_fetch_array($db_daten)){
	$technames[$row['tech_id']]=getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']);
}

$result=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, SUM(anzahl) AS anzahl, verbzeit, SUM(score) AS score FROM `de_user_build` 
	WHERE user_id='$ums_user_id' AND tech_id>80 AND tech_id<110 GROUP BY tech_id, verbzeit ORDER BY verbzeit, tech_id ASC");
$num = mysqli_num_rows($result);
if ($num>0){
	echo '</td><td width="13" class="rr">&nbsp;</td></tr></table>
        <table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rml">&nbsp;</td>
        <td align="left" class="ro"><div class="cellu">&nbsp;'.$production_lang['aktiveauftraege'].':</div></td>
        <td width="13" class="rmr">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

	echo '<table border="0" cellpadding="0" cellspacing="1">';
	echo '<tr align="center">';
	echo '<td class="cell" width="300"><b>'.$production_lang['einheit'].'</b></td>';
	echo '<td class="cell" width="88"><b>'.$production_lang['stueck'].'</b></td>';
	echo '<td class="cell" width="105"><b>'.$production_lang['punkte'].'</b></td>';
	echo '<td class="cell" width="78"><b>'.$production_lang['wochen'].'</b></td>';
	echo '</tr>';


	while($row = mysqli_fetch_array($result)){ //jeder gefundene datensatz wird geprueft
	    echo '<tr align="center">';
	    echo '<td class="cell">'.$technames[$row['tech_id']].'</td>';
	    echo '<td class="cell">'.number_format($row['anzahl'], 0,"",".").'</td>';
	    echo '<td class="cell">'.number_format($row['score'], 0,"",".").'</td>';
	    echo '<td class="cell">'.$row['verbzeit'].'</td>';
	    echo '</tr>';
	}
  
  echo '</table>';
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';
}
else  //nur unteren rahmen von der echtzeitrechnung
{
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br>';
}

	//designauswahl
	/*
	  echo '<div class="cellu" style="width: 250px;">'; 
	  if($design==1){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
	  echo $str1.'<a href="production.php?setdesign=1">'.$production_lang[design].' A</a>'.$str2.' - ';
	  if($design==2){$str1="<b>";$str2="</b>";}else{$str1='';$str2='';}
	  echo $str1.'<a href="production.php?setdesign=2">'.$production_lang[design].' B</a>'.$str2.'<br><br>';
	  echo '</div>';
	*/
//}

echo '</form>';


/////////////////////////////////////////////////
// Waren/Handelsgüter/Itemproduktion
/////////////////////////////////////////////////
if(!isset($sv_deactivate_vsystems) || $sv_deactivate_vsystems!=1){
	$factory_max_capacity=array();
	for($g=0;$g<count($GLOBALS['map_buildings']);$g++){
		if(isset($GLOBALS['map_buildings'][$g]['factory_id'])){
			$result  = mysqli_query($GLOBALS['dbi'],"SELECT SUM(bldg_level) AS anzahl FROM de_user_map_bldg WHERE user_id='".$_SESSION['ums_user_id']."' AND bldg_id='".$g."';");
			$row     = mysqli_fetch_array($result);
			$anzahl = $row["anzahl"];

			$factory_max_capacity[$GLOBALS['map_buildings'][$g]['factory_id']]=intval($anzahl);
		}
	}

	//print_r($factory_max_capacity);

	//möchte man ein Item bauen?
	if(isset($_REQUEST['build_item']) && $sabotage==0){
		if (setLock($ums_user_id)){
			for($i=0;$i<200;$i++){
				if(isset($_POST['item_id_'.$i]) && $_POST['item_id_'.$i]>0){
					//item_id auswerten
					$item_id=$i;
					$want_build_amount=intval($_POST['item_id_'.$i]);
					
					//hat die item_id einen Bauplan?
					if(!empty($ps[$item_id]['item_blueprint'])){
						//nochmal die vorandenen Rohstoffe laden
						$ps=loadPlayerStorage($_SESSION['ums_user_id']);

						//Standard ist 1, falls es mal nicht gesetzt sein sollte
						$tech_ticks=1;

						//Baukosten
						$need_storage_res=array();

						///////////////////////////////////////////////////////
						//festellen wieviel man bauen kann
						///////////////////////////////////////////////////////
						$z=$want_build_amount;$z1=0; 

						$parts=explode(";", $ps[$item_id]['item_blueprint']);

						

						foreach ($parts as $einzel) {
							if($einzel[0]=='I'){

								//checken ob man genug vom benötigtem Item hat
								//echo '<br>E: '.$einzel;
								
								$values=explode("x", str_replace('I', '', $einzel));

								$item_need_id=$values[0];
								$item_need_amount=$values[1];

								$maxamount=floor($ps[$item_need_id]['item_amount']/$item_need_amount);

								$need_storage_res[$item_need_id]=$item_need_amount;

								//echo '<br>'.$item_need_id.'/'.$item_need_amount.'/'.$ps[$item_need_id]['item_amount'].'/'.$maxamount;							

								if ($maxamount>$want_build_amount){
									$z1=$want_build_amount;
								}else{
									$z1=$maxamount;
								}

								if ($z1<$z){
									$z=$z1;
								}
				
							}elseif($einzel[0]=='Z'){
								//echo '<br>Z: '.$einzel;
								$tech_ticks=str_replace("Z", "", $einzel);
				
							}elseif($einzel[0]=='P'){
								//echo '<br>P: '.$einzel;
								$values=explode("x", $einzel);
								$factory_id=str_replace("P", "", $values[0]);

								$need_factory_capacity=$values[1];

								//reicht die Kapazität der Fabriken? also überprüfen wie weit die Fabrik ausgelastet ist
								$factory_capacity_available=$factory_max_capacity[$factory_id]-getUsedFactoryCapacity($_SESSION['ums_user_id'], $factory_id);

								//echo 'P: '.$factory_capacity_available;

								$maxamount=floor($factory_capacity_available/$need_factory_capacity);

								if ($maxamount>$want_build_amount){
									$z1=$want_build_amount;
								}else{
									$z1=$maxamount;
								}

								if ($z1<$z){
									$z=$z1;
								}
							}
						}

						///////////////////////////////////////////////////////
						// Datenbank updaten
						///////////////////////////////////////////////////////
						//echo '<br>$z: '.$z;
						if($z>0){
							//benötigte Fabrikkapazität berechnen
							$factory_used_capacity=$need_factory_capacity*$z;

							//itemkosten abziehen
							foreach ($need_storage_res as $key => $value){
								change_storage_amount($_SESSION['ums_user_id'], $key, $value*$z*-1);
							}

							$item_build_id=10000+$item_id;
								
							$sql="INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, factory_id, factory_used_capacity) VALUES ($ums_user_id, $item_build_id, $z, $tech_ticks, $factory_id, $factory_used_capacity)";
							//echo $sql;
							mysqli_query($GLOBALS['dbi'],$sql);
						}
					}
				}
			}




			//transaktionsende
			$erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
			if ($erg){
				//print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
			}else{
				print("ERROR 17<br><br><br>");
			}
		}// if setlock-ende
		else echo '<br><font color="#FF0000">ERROR 18</font><br><br>';

	}

	echo '<form action="production.php" method="POST" name="item_build">';
	rahmen_oben('Waren/Handelsg&uuml;ter');
	echo '
	<table width="572" border="0" cellpadding="0" cellspacing="0">
		<tr align="left" class="cell">
			<td valign="top">';

	//die genutzte/maximale Produktionskapazität anzeigen
	echo '<div style="display: flex;">';
	for($g=0;$g<count($GLOBALS['map_buildings']);$g++){
		if(isset($GLOBALS['map_buildings'][$g]['factory_id'])){
			/*
			$result  = mysqli_query($GLOBALS['dbi'],"SELECT SUM(bldg_level) AS anzahl FROM de_user_map_bldg WHERE user_id='".$_SESSION['ums_user_id']."' AND bldg_id='".$g."';");
			$row     = mysqli_fetch_array($result);
			$anzahl = $row["anzahl"];
			*/

			$max=$factory_max_capacity[$GLOBALS['map_buildings'][$g]['factory_id']];

			//die genutze Kapazität aus der DB holen

			echo '<div style="flex-grow: 1; font-size: 16px;"><img src="'.$ums_gpfad.'g/r/'.$GLOBALS['map_buildings'][$g]['factory_id'].'_g.gif" title="'.$GLOBALS['map_buildings'][$g]['name'].'"> '.intval(getUsedFactoryCapacity($_SESSION['ums_user_id'], $GLOBALS['map_buildings'][$g]['factory_id'])).'/'.intval($max).'</div>';
		}
	}

	echo '</div>';

	//die Baumöglichkeiten anzeigen
	$c1=1;
	foreach ($ps as $item_id_product => $item) {
		if(!empty($item['item_blueprint'])){
			if($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
			
			echo '<div class="'.$bg.'" style="padding: 10px;">';

			//Item-Name
			echo '<div class="fett">'.$item['item_name'].'</div>';

			//Item-Bestandteile/Voraussetzungen/Bauzeit
			$parts=explode(";", $item['item_blueprint']);
			
			$baukosten='';
			$bauzeit='';
			$fabrikkosten='';
			foreach ($parts as $einzel) {
				if($einzel[0]=='I'){
					$values=explode("x", $einzel);
					$item_id=str_replace("I", "", $values[0]);

					$baukosten.='<br>'.$values[1].' '.$ps[$item_id]['item_name'].' (Lager: '.number_format($ps[$item_id]['item_amount'], 0,",",".").')';

				}elseif($einzel[0]=='Z'){
					$bauzeit=str_replace("Z", "", $einzel).' WT';

				}elseif($einzel[0]=='P'){
					$values=explode("x", $einzel);
					$factory_id=str_replace("P", "", $values[0]);

					$fabrikkosten.='<br>'.$values[1].' <img src="'.$ums_gpfad.'g/r/'.$factory_id.'_g.gif">';
				}
			}

			//Baukosten
			echo '<br>Baukosten pro St&uuml;ck: '.$baukosten;

			//Bauzeit
			echo '<br><br>Bauzeit: '.$bauzeit;

			//Benötigte Fabrikkapazität
			echo '<br><br>Ben&ouml;tigte Fabrikkapazit&auml;t pro St&uuml;ck: '.$fabrikkosten;

			//Baumenge
			echo '<br><br>Baumenge: <input type="text" name="item_id_'.$item_id_product.'" value="" size="3" maxlength="9""> (Im Bau: '.intval(getItemBuildAmount($_SESSION['ums_user_id'], $item_id_product)).')';

			echo '</div>';
		}
	}

	echo '<div class="cell" style="margin-top: 4px; padding: 5px; text-align: center;"><input type="Submit" name="build_item" value="Bauen"></div>';
			
	echo '		
			</td>
		</tr>
	</table>';

	rahmen_unten();

	echo '</form>';

}

?>
</div>
<br>
<?php include "fooban.php"; ?>
</body>
</html>
