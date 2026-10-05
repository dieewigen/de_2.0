<?php
include('inc/header.inc.php');
include('lib/transaction.lib.php');
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

//$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, techs, newtrans, newnews, design3 AS design, sc2, spec1, spec3 FROM de_user_data WHERE user_id='$_SESSION['ums_user_id']'");
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
	for($i=0;$i<count($sv_schiffsdaten[$_SESSION['ums_rasse']]);$i++){
		$sv_schiffsdaten[$_SESSION['ums_rasse']-1][$i][1]= floor($sv_schiffsdaten[$_SESSION['ums_rasse']-1][$i][1] * 1.2);
	}	
}

////////////////////////////////////////////////////////////////////////////////
//userartefakte auslesen
////////////////////////////////////////////////////////////////////////////////
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT id, level FROM de_user_artefact WHERE id=1 AND user_id='".$_SESSION['ums_user_id']."';");
$artbonus_fleet=0;
while($row = mysqli_fetch_array($db_daten)){
  $artbonus_fleet=$artbonus_fleet+$ua_werte[$row['id']-1][$row['level']-1][0];
}

if($artbonus_fleet>5){
	$artbonus_fleet=5;
}

$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT id, level FROM de_user_artefact WHERE (id=2 OR id=8 OR id=9) AND user_id=?", [$_SESSION['ums_user_id']]);
$artbonus_def=0;$artbonus2=0;$artbonus3=0;
while($row = mysqli_fetch_array($db_daten)){
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
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_tech_data{$_SESSION['ums_rasse']} WHERE tech_id=24", []);
$row = mysqli_fetch_array($db_daten);
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
  mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET design3=? WHERE user_id = ?", [$design, $_SESSION['ums_user_id']]);	
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
		$zstr.='<br><br>'.$production_lang['reisezeit'].': '.$sv_schiffsdaten[$_SESSION['ums_rasse']-1][$unit_index][0];
		//transportkapazität
		if ($sv_schiffsdaten[$_SESSION['ums_rasse']-1][$unit_index][1]>0)$zstr.='<br>'.$production_lang['kapazitaet1'].': '.$sv_schiffsdaten[$_SESSION['ums_rasse']-1][$unit_index][1];
		//ben. transportkapazität
		if ($sv_schiffsdaten[$_SESSION['ums_rasse']-1][$unit_index][2]>0)$zstr.='<br>'.$production_lang['kapazitaet2'].': '.$sv_schiffsdaten[$_SESSION['ums_rasse']-1][$unit_index][2];
		//frachtkapazität
		if (isset($unit[$_SESSION['ums_rasse']-1][$unit_index]['fk']) && $unit[$_SESSION['ums_rasse']-1][$unit_index]['fk']>0)$zstr.='<br>Frachtkapazit&auml;t: '.$unit[$_SESSION['ums_rasse']-1][$unit_index]['fk'];
	}

	//waffenarten
	//konventionell
	if($unit[$_SESSION['ums_rasse']-1][$unit_index][2]>0)$wv='<font color=#2DFF11>'.$production_lang['waffenvorhandenja'].'</font>';
	 else $wv='<font color=#ED0909>'.$production_lang['waffenvorhandennein'].'</font>';
	$zstr.='<br><br><font color=#9D4B15>'.$production_lang['waffengattung1'].':</font> '.$wv;

	//klassenziel
	if($unit[$_SESSION['ums_rasse']-1][$unit_index][2]>0){
	  $zstr.='<br><font color=#ED9409>-'.$production_lang['klasseziel1'].': '.$production_lang['klassennamen'][$kampfmatrix[$unit_index][0]].'</font>';
	  $zstr.='<br><font color=#F0BA66>-'.$production_lang['klasseziel2'].': '.$production_lang['klassennamen'][$kampfmatrix[$unit_index][2]].'</font>';
	}

	//emp
	if($unit[$_SESSION['ums_rasse']-1][$unit_index][3]>0){
		$wv='<font color=#2DFF11>'.$production_lang['waffenvorhandenja'].'</font>';
	}else{
		$wv='<font color=#ED0909>'.$production_lang['waffenvorhandennein'].'</font>';
	}
	$zstr.='<br><br><font color=#15629D>'.$production_lang['waffengattung2'].':</font> '.$wv;

	//klassenziel
	if($unit[$_SESSION['ums_rasse']-1][$unit_index][3]>0){
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
// Validierung der Rasse (1-4 erlaubt)
$rasse = isset($_SESSION['ums_rasse']) && $_SESSION['ums_rasse'] >= 1 && $_SESSION['ums_rasse'] <= 4 ? $_SESSION['ums_rasse'] : 1;
$js_file = $_SERVER['DOCUMENT_ROOT'].'/js/produktion'.$rasse.'.js';
if (file_exists($js_file)) {
    echo '<script src="js/produktion'.$rasse.'.js?'.filemtime($js_file).'" type="text/javascript"></script>';
}
?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

if(isset($_POST['submit']) && $sabotage==0){//ja, es wurde ein button gedrueckt
	//transaktionsbeginn
	if (setLock($_SESSION['ums_user_id'])){
		//$need_storage_res=array();
		//nochmal die vorandenen Rohstoffe laden
		$row=loadPlayerData($_SESSION['ums_user_id']);
		$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
		//auch das Lager (z. B. Titanen-Energiekerne) innerhalb der Sperre neu laden
		$ps=loadPlayerStorage($_SESSION['ums_user_id']);
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
					mysqli_query($GLOBALS['dbi'],"INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, score) VALUES (".$_SESSION['ums_user_id'].", $i, $z, $tech_ticks, $buildscore)");
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
		 restyp04 = restyp04 - $gr04, restyp05 = restyp05 - $gr05 WHERE user_id = '".$_SESSION['ums_user_id']."';");

		//transaktionsende
		$erg = releaseLock($_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
		if ($erg){
			  //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
		}else{
			print($production_lang['releaselock'].$_SESSION['ums_user_id'].$production_lang['releaselock2']."<br><br><br>");
		}
	}// if setlock-ende
	else echo '<br><font color="#FF0000">'.$production_lang['releaselock3'].'</font><br><br>';

}//submit ende

//stelle die ressourcenleiste dar
include "resline.php";

echo '<script language="javascript">var hasres = new Array('.$restyp01.','.$restyp02.','.$restyp03.','.$restyp04.','.$restyp05.');</script>';

echo einheiten_navi('production');

//feststellen ob eine sabotage vorliegt und dann abbrechen
if($sabotage==1){
	echo '<div class="prod mod prod-sabotage"><div class="mod-meldung mod-meldung-fehler">'.$production_lang['sabotage_aktiv'].'</div></div>';
	die('</body></html>');
}

/*
if ($techs[13]==0){
	$techcheck="SELECT tech_name FROM de_tech_data".$_SESSION['ums_rasse']." WHERE tech_id=13";
	$db_tech=mysqli_execute_query($GLOBALS['dbi'], $techcheck, []);
	$row_techcheck = mysqli_fetch_array($db_tech);

	//echo $production_lang[eswirdeine].$row_techcheck[tech_name].$production_lang[benoetigt];

	echo '<br>';
	rahmen_oben($production_lang[fehlendesgebaeude]);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$_SESSION['ums_rasse'].'&t=13" target="_blank"><img src="'.'gp/'.'g/t/'.$_SESSION['ums_rasse'].'_13.jpg" border="0"></a></td>
	<td valign="top">'.$production_lang[gebaeudeinfo].': '.$row_techcheck[tech_name].'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();
}else{
*/

echo '<div>';

/////////////////////////////////////////////////////////////////////////////
//Einheiten zählen
/////////////////////////////////////////////////////////////////////////////
$ec=array();
$fid0=$_SESSION['ums_user_id'].'-0';$fid1=$_SESSION['ums_user_id'].'-1';$fid2=$_SESSION['ums_user_id'].'-2';$fid3=$_SESSION['ums_user_id'].'-3';
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

/////////////////////////////////////////////////////////////////////////////
// Einheiten ausgeben; js/produktion*.js braucht das Formular "produktion",
// die Felder b81-b104 und die Summenfelder #m #d #i #e #t #k #p
/////////////////////////////////////////////////////////////////////////////
$prod_resnamen=array('Multiplex','Dyharra','Iradium','Eternium','Tronic');
$einheiten=array('flotte' => '', 'verteidigung' => '');
$z=0;
$db_daten=mysqli_query($GLOBALS['dbi'],"SELECT  * FROM de_tech_data WHERE tech_id>80 AND tech_id<110 ORDER BY tech_id");
while($row = mysqli_fetch_array($db_daten)){ //jeder gefundene datensatz wird geprueft
	$tech_id=$row['tech_id'];
	if($tech_id<100){
		$unit_index=$tech_id-81;
		$artbonus=$artbonus_fleet;
		$gruppe='flotte';
	}else{
		$unit_index=$tech_id-90;
		$artbonus=$artbonus_def;
		$gruppe='verteidigung';
	}

	//bauzeit, boni mit einrechnen
	$tech_ticks=$unit[$_SESSION['ums_rasse']-1][$unit_index]['bz'];
	if($tech_id<100 && $spec1==2){
		$tech_ticks=round($tech_ticks/2);
	}elseif($tech_id>=100){
		$tech_ticks=ceil($tech_ticks-($tech_ticks*$defense_bonus_buildtime/100));
		if($tech_ticks<1)$tech_ticks=1;
	}

	//Baukosten abzüglich Artefaktbonus, je Rohstoff eine Spalte; Nullwerte blass
	$kosten='';
	for($r=0;$r<5;$r++){
		$betrag=$unit[$_SESSION['ums_rasse']-1][$unit_index][5][$r];
		$betrag=$betrag-round($betrag*$artbonus/100);
		$kosten.='<span class="prod-zahl'.($betrag>0 ? '' : ' prod-null').'">'.number_format($betrag, 0,"",".").'</span>';
	}

	//kostet es besondere items? klein unter dem Namen
	$zusatz='';
	if(!empty($unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost'])){
		$einzelkosten=explode(';', $unit[$_SESSION['ums_rasse']-1][$unit_index]['item_cost']);
		foreach ($einzelkosten as $value) {
			$parts=explode("x", $value);
			$zusatz.='<span class="prod-zusatz" title="Zusatzkosten&Lager: '.number_format($ps[$value[1]]['item_amount'], 0,"",".").'">+ '.$parts[1].' '.$ps[$value[1]]['item_name'].'</span>';
		}
	}

	$hat_tech=hasTech($pt, $tech_id);
	if($hat_tech){
		$eingabe='<input type="text" name="b'.$tech_id.'" id="b'.$tech_id.'" value="" maxlength="9" autocomplete="off" inputmode="numeric" onKeyUp="berechnepreise();" class="mod-eingabe">';
	}else{
		$eingabe='<span class="prod-fehlt-tech" title="Fehlende Technologie">Tech. fehlt</span>';
	}

	$einheiten[$gruppe].='
	<div class="prod-zeile'.($hat_tech ? '' : ' prod-gesperrt').'">
		<span class="prod-name"><span rel="tooltip" title="'.$tooltips[$z].'">'.getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']).'</span>'.$zusatz.'</span>
		'.$kosten.'
		<span class="prod-zahl">'.$tech_ticks.'</span>
		<span class="prod-zahl prod-bestand">'.number_format($ec[$tech_id], 0,"",".").'</span>
		<span class="prod-eingabe">'.$eingabe.'</span>
	</div>';

	$z++;
}

//Summe der ausgewählten Einheiten, füllt js/produktion*.js
$summe='';
foreach(array('m','d','i','e','t') as $r => $feld){
	$summe.='<span class="geh-kosten" title="'.$prod_resnamen[$r].'"><img src="gp/g/icon'.($r+1).'.png" alt=""><b id="'.$feld.'">0</b></span>';
}
$summe.='<span class="geh-kosten">'.$production_lang['kapazitaet'].' <b id="k">0</b></span>';
$summe.='<span class="geh-kosten">'.$production_lang['punkte'].' <b id="p">0</b></span>';

echo '<form action="production.php" method="POST" name="produktion">';
rahmen_oben($production_lang['produktion']);
//Kopfzeile: Rohstoff-Icons über den Kostenspalten
$kopf='';
for($r=0;$r<5;$r++){
	$kopf.='<span class="prod-zahl"><img src="gp/g/icon'.($r+1).'.png" alt="" title="'.$prod_resnamen[$r].'"></span>';
}

echo '
<div class="prod mod">
	<div class="prod-zeile prod-kopf">
		<span class="prod-name">'.$production_lang['einheit'].'</span>
		'.$kopf.'
		<span class="prod-zahl">'.$production_lang['wochen'].'</span>
		<span class="prod-zahl prod-bestand">'.$production_lang['stueck'].'</span>
		<span class="prod-eingabe">'.$production_lang['bauen'].'</span>
	</div>
	<div class="prod-gruppe">
		<span class="mod-typ">Flotteneinheiten</span>
		<span class="mod-chip">'.$production_lang['baukostenreduz'].$ua_name[0].$production_lang['artefakte'].' <b>'.number_format($artbonus_fleet, 2,",",".").' %</b> (max. 5,00 %)</span>
	</div>
	<div class="prod-liste">'.$einheiten['flotte'].'</div>
	<div class="prod-gruppe">
		<span class="mod-typ">Verteidigungseinheiten</span>
		<span class="mod-chip prod-status" rel="tooltip" title="'.$defstatus.'">'.$defense_lang['statusinformationen'].'</span>
		<span class="mod-chip">Baukostenreduzierung <b>'.number_format($artbonus_def, 2,",",".").' %</b> (max. 5,00 %)</span>
	</div>
	<div class="prod-liste">'.$einheiten['verteidigung'].'</div>
	<div class="geh-summe">
		<div class="geh-summe-text"><span class="mod-typ">Baukosten</span><div class="geh-summe-werte">'.$summe.'</div></div>
		<input type="Submit" name="submit" value="'.$production_lang['bauen'].'" class="mod-btn">
	</div>';

//zeige aktive bauaufträge an
$technames=array();
$sql="SELECT * FROM de_tech_data WHERE tech_id>=81 AND tech_id<=104 ORDER BY tech_id ASC";
$db_daten=mysqli_query($GLOBALS['dbi'],$sql);
while($row = mysqli_fetch_array($db_daten)){
	$technames[$row['tech_id']]=getTechNameByRasse($row['tech_name'],$_SESSION['ums_rasse']);
}

$result=mysqli_query($GLOBALS['dbi'],"SELECT tech_id, SUM(anzahl) AS anzahl, verbzeit, SUM(score) AS score FROM `de_user_build`
	WHERE user_id='".$_SESSION['ums_user_id']."' AND tech_id>80 AND tech_id<110 GROUP BY tech_id, verbzeit ORDER BY verbzeit, tech_id ASC");
if(mysqli_num_rows($result)>0){
	echo '<div class="geh-produktion"><div class="mod-typ">'.$production_lang['aktiveauftraege'].'</div>';
	while($row = mysqli_fetch_array($result)){
		echo '<div class="geh-auftrag"><span>'.$technames[$row['tech_id']].'</span><b>'.number_format($row['anzahl'], 0,"",".").'</b><span class="prod-punkte">'.number_format($row['score'], 0,"",".").' '.$production_lang['punkte'].'</span><span class="mod-chip">noch '.$row['verbzeit'].' WT</span></div>';
	}
	echo '</div>';
}

echo '</div>';
rahmen_unten();
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
		if (setLock($_SESSION['ums_user_id'])){
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
								
							$sql="INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit, factory_id, factory_used_capacity) VALUES (".$_SESSION['ums_user_id'].", $item_build_id, $z, $tech_ticks, $factory_id, $factory_used_capacity)";
							//echo $sql;
							mysqli_query($GLOBALS['dbi'],$sql);

							//Lageranzahl neu laden, damit die Anzeige aktuell ist
							$ps=loadPlayerStorage($_SESSION['ums_user_id']);
						}
					}
				}
			}




			//transaktionsende
			$erg = releaseLock($_SESSION['ums_user_id']); //L�sen des Locks und Ergebnisabfrage
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
	echo '<div class="prod mod">';

	//genutzte/maximale Kapazität der Fabriken
	echo '<div class="prod-fabriken">';
	for($g=0;$g<count($GLOBALS['map_buildings']);$g++){
		if(isset($GLOBALS['map_buildings'][$g]['factory_id'])){
			$factory_id=$GLOBALS['map_buildings'][$g]['factory_id'];
			echo '<span class="prod-fabrik" rel="tooltip" title="'.$GLOBALS['map_buildings'][$g]['name'].'&Genutzte / maximale Fabrikkapazit&auml;t"><img src="gp/g/r/'.$factory_id.'_g.gif" alt=""><b>'.intval(getUsedFactoryCapacity($_SESSION['ums_user_id'], $factory_id)).'</b> / '.intval($factory_max_capacity[$factory_id]).'</span>';
		}
	}
	echo '</div>';

	//die Baumöglichkeiten anzeigen
	foreach ($ps as $item_id_product => $item) {
		if(!empty($item['item_blueprint'])){
			//Bestandteile, Bauzeit und Fabrikkapazität aus dem Bauplan
			$parts=explode(";", $item['item_blueprint']);
			$baukosten='';
			$bauzeit='';
			$fabrikkosten='';
			foreach ($parts as $einzel) {
				if($einzel[0]=='I'){
					$values=explode("x", $einzel);
					$item_id=str_replace("I", "", $values[0]);
					//rot, wenn es nicht einmal für ein Stück reicht
					$fehlt=$ps[$item_id]['item_amount']<$values[1];
					$baukosten.='<div class="prod-zutat'.($fehlt ? ' prod-fehlt' : '').'"><b>'.$values[1].'</b> '.$ps[$item_id]['item_name'].' <span>(Lager: '.number_format($ps[$item_id]['item_amount'], 0,",",".").')</span></div>';
				}elseif($einzel[0]=='Z'){
					$bauzeit=str_replace("Z", "", $einzel).' WT';
				}elseif($einzel[0]=='P'){
					$values=explode("x", $einzel);
					$factory_id=str_replace("P", "", $values[0]);
					$fabrikkosten.=' <b>'.$values[1].'</b> <img src="gp/g/r/'.$factory_id.'_g.gif" alt="">';
				}
			}

			echo '
			<div class="prod-ware">
				<div class="prod-ware-kopf"><span class="prod-name">'.$item['item_name'].'</span><span class="mod-chip">Lager <b>'.number_format($ps[$item_id_product]['item_amount'], 0,",",".").'</b></span></div>
				<div class="prod-ware-inhalt">
					<div class="prod-ware-spalte" title="Baukosten pro St&uuml;ck">'.$baukosten.'</div>
					<div class="prod-ware-spalte">
						<div class="prod-zutat"><span>Bauzeit</span> <b>'.$bauzeit.'</b></div>
						<div class="prod-zutat" title="Ben&ouml;tigte Fabrikkapazit&auml;t pro St&uuml;ck"><span>Fabrik pro St&uuml;ck</span>'.$fabrikkosten.'</div>
					</div>
					<div class="prod-ware-menge">
						<input type="text" name="item_id_'.$item_id_product.'" value="" maxlength="9" autocomplete="off" inputmode="numeric" placeholder="Menge" class="mod-eingabe">
						<span class="prod-klein">Im Bau: '.intval(getItemBuildAmount($_SESSION['ums_user_id'], $item_id_product)).'</span>
					</div>
				</div>
			</div>';
		}
	}

	echo '<div class="prod-ware-los"><input type="Submit" name="build_item" value="Bauen" class="mod-btn"></div>';
	echo '</div>';
	rahmen_unten();

	echo '</form>';

}

?>
</div>
<br>

</body>
</html>
