<?php
use DieEwigen\DE2\View\Einheit\Tooltip;
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
$orderby='`system`';$sort_sso=1;
if($secsort=='1'){$orderby='col';$sort_sso=2;}
elseif($secsort=='2'){$orderby='score';$sort_sso=3;}

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
	echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' mobile">';
}else{
	//der Cursor steht gleich im Sektorfeld, so lässt sich direkt eine Nummer eintippen
	echo '<body onload="document.secform.sf.focus();document.secform.sf.select();" class="theme-rasse'.$_SESSION['ums_rasse'].' desktop">';
}

//stelle die ressourcenleiste dar
include "resline.php";

echo '<div align="center">';

//Tooltip "Kollektoren-Wellenrechner": was in den nächsten fünf Angriffswellen erobert (grün) oder zerstört (rot) wird
function wellenrechner($kol, $maxcol, $npcsec){
	global $sec_lang, $col, $sv_min_col_attgrenze, $sv_max_col_attgrenze, $sv_kollie_klaurate;
	$zeilen=array(array($sec_lang['kollektoren'], number_format($kol, 0, ',' ,'.')));

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

		$klasse=($kol<$col_angriffsgrenze*$owncol) ? 'ei-nein' : 'ei-ja';
		$zeilen[]=array(($we+1).'. '.$sec_lang['welle'], "<span class='".$klasse."'>".number_format(floor($kol*$sv_kollie_klaurate), 0, ',','.')."</span>");
		$owncol=$owncol+floor($kol*$sv_kollie_klaurate);
		$kol=$kol-floor($kol*$sv_kollie_klaurate);
	}

	//info bzgl. erobern/zerstören von kollektoren
	return Tooltip::status(array(array('Kollektoren-Wellenrechner', $zeilen,
		'Gr&uuml;ner Kollektorenwert: Kollektoren liegen &uuml;ber der Kollektorenangriffsgrenze und werden erobert.<br><br>Roter Kollektorenwert: Kollektoren liegen unter der Kollektorenangriffsgrenze und werden zerst&ouml;rt.')));
}

//Tooltip der Punkte: was die Farbe bedeutet, bei ausgesetztem Kopfgeld zusätzlich die Beträge
function sector_punkte_tooltip($kopfgeld=null){
	$abschnitte=array(array('Punkte', array(),
		'Gr&uuml;ner Punktewert: Ziel ist angreifbar, da oberhalb der Punkteangriffsgrenze.<br><br>Roter Punktewert: Ziel ist nicht angreifbar, da unterhalb der Punkteangriffsgrenze.'));
	if($kopfgeld!==null){
		$abschnitte[]=array('Kopfgeld', array(
			array('M', number_format($kopfgeld['kg01'], 0,"",".")),
			array('D', number_format($kopfgeld['kg02'], 0,"",".")),
			array('I', number_format($kopfgeld['kg03'], 0,"",".")),
			array('E', number_format($kopfgeld['kg04'], 0,"","."))));
	}
	return Tooltip::status($abschnitte);
}

//Reisezeit-Kennzeichen im Sektorkopf
function sector_rz_badge($rzadd){
	return '<span class="sec-rz sec-rz'.($rzadd==0 ? '0' : '2').'" title="Reisezeitmalus&Eigener Sektor: kein Malus<br>Andere Sektoren: Reisezeit +2 Kampfticks">+'.$rzadd.'</span>';
}

//Sektorkopf: immer einzeilig, ein zu langer Sektorname wird am Ende gekürzt und steht vollständig im Tooltip
function sector_head($inhalt, $name=''){
	if($name!=''){
		//der Name ist beim Speichern schon escaped; für den Tooltip (rendert HTML) noch einmal escapen
		$inhalt.=' &middot; <span title="Sektorname&'.htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'">'.$name.'</span>';
	}
	return '<div class="sec-head">'.$inhalt.'</div>';
}

//Spaltenkopf der Spielertabelle; sortierbare Spalten sind Links, die aktive Sortierung ist unterstrichen
function sector_th($label, $klasse, $sso=0, $sort_sso=0, $sf=0){
	if($sso==0){
		return '<th class="'.$klasse.'">'.$label.'</th>';
	}
	$aktiv=($sso==$sort_sso) ? ' sec-sortiert' : '';
	return '<th class="'.$klasse.$aktiv.'"><a href="sector.php?sso='.$sso.'&amp;sf='.$sf.'" title="Sortieren nach: '.$label.'">'.$label.'</a></th>';
}

//Zeile im Infoblock, optional mit Erklärung als Tooltip am Namen
function sector_inforow($label, $wert, $tooltip=''){
	if($tooltip!=''){
		$label='<span class="sec-hint" title="'.$tooltip.'">'.$label.'</span>';
	}
	return '<tr><td>'.$label.'</td><td>'.$wert.'</td></tr>';
}

//Legende unter der Spielertabelle, zugeklappt; ob sie offen ist, merkt sich der Browser
function sector_legende($kopfgeld){
	$punkte='<span class="sec-gruen">gr&uuml;n</span> angreifbar &middot; <span class="sec-rot">rot</span> unter der Angriffsgrenze';
	if($kopfgeld){
		$punkte.=' &middot; <b>fett</b>: Kopfgeld ausgesetzt';
	}

	return '
	<details class="sec-legend" id="sec_legend">
	<summary>Legende</summary>
	<table>
	<tr><td>Reisezeit</td><td><span class="sec-rz sec-rz0">+0</span>eigener Sektor &middot; <span class="sec-rz sec-rz2">+2</span>andere Sektoren: Flugzeit +2 Kampfticks</td></tr>
	<tr><td>System</td><td>^5^ Sektorkommandant &middot; [5] gesperrt &middot; (5) Urlaubsmodus &middot; {5} L&ouml;schmodus</td></tr>
	<tr><td>Name</td><td>* in den letzten 30 Minuten aktiv (nur im eigenen Sektor) &middot; <span class="user_title"></span> Titel (Maus dar&uuml;ber)</td></tr>
	<tr><td>Rasse</td><td>sichtbar im eigenen Sektor (au&szlig;er Sektor 1) und nach einem Scan</td></tr>
	<tr><td>Allianz</td><td><span class="sec-weiss">wei&szlig;</span> eigene &middot; <span class="sec-gruen">gr&uuml;n</span> Partner &middot; <span class="sec-rot">rot</span> Kriegsgegner &middot; <span class="sec-gelb">gelb</span> andere</td></tr>
	<tr><td>Punkte</td><td>'.$punkte.'</td></tr>
	<tr><td>Kollektoren</td><td><span class="sec-gruen">gr&uuml;n</span> werden erobert &middot; <span class="sec-rot">rot</span> werden zerst&ouml;rt &middot; Maus dar&uuml;ber: Wellenrechner</td></tr>
	<tr><td>Aktion</td><td>S Sonde &middot; A Agenten &middot; F Flotte &middot; Punkt: deine Einstufung im Geheimdienst (<img src="gp/g/ps_0.gif" alt=""> neutral, <img src="gp/g/ps_1.gif" alt=""> freundlich, <img src="gp/g/ps_2.gif" alt=""> feindlich)</td></tr>
	</table>
	</details>
	<script>
	(function(){
		var d=document.getElementById("sec_legend");
		try{ if(localStorage.getItem("sec_legend")==="1"){ d.open=true; } }catch(e){}
		d.addEventListener("toggle", function(){ try{ localStorage.setItem("sec_legend", d.open ? "1" : "0"); }catch(e){} });
	})();
	</script>';
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

//Blättern zwischen den Sektoren: einer zurück, Nummer, einer vor (Knöpfe wie bei den Vergessenen Systemen), kompakt in der
//Mitte, damit die Wege zwischen den Pfeilen kurz bleiben. Die früheren Sprünge um zwei Sektoren gab es für die
//Alien-Sektoren an jeder zweiten Stelle, die heute nicht mehr so verteilt sind.
echo '<form action="sector.php" name="secform" method="POST" class="mod sec-navi">
	<span class="sec-navi-titel">Sektor</span>
	<a href="sector.php?sf='.($sf-1).'" class="ms-nav">&lsaquo;</a>
	<input type="text" name="sf" value="'.$sf.'" maxlength="5" inputmode="numeric" autocomplete="off" class="mod-eingabe">
	<button type="submit" class="mod-btn mod-btn-leise ally-btn-klein">OK</button>
	<a href="sector.php?sf='.($sf+1).'" class="ms-nav">&rsaquo;</a>
</form>';

//schauen ob nah oder fern
$rzadd=0;
if ($ownsector<>$sf){
	$rzadd=2;
}

//die daten des sektors auslesen
$sql = "SELECT * FROM de_sector WHERE sec_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
$sec_data = mysqli_fetch_assoc($db_daten);

//Geisterschiff zu Halloween: treibt durch die Sektoren und stört Sektor 666 (src/Model/GhostShip/GhostShipEvent.php)
$geist = new \DieEwigen\DE2\Model\GhostShip\GhostShipEvent($GLOBALS['dbi']);

//Sektor 666: Zustand der Schläfer (src/Model/Sektor666/Sektor666Service.php), schlafend nur der Kulissentext
if($sf==666){
	include 'inc/lang/'.$sv_server_lang.'_sektor666.lang.php';
	$s666 = new \DieEwigen\DE2\Model\Sektor666\Sektor666Service($GLOBALS['dbi']);
	$geist_funk = \DieEwigen\DE2\View\GhostShip\GhostShipView::sector666($geist, $s666->isWach());
	echo \DieEwigen\DE2\View\Sektor666\Karte::render($s666, $s666_lang, (int)$_SESSION['ums_user_id'], (int)$ownsector, $geist_funk);
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
	$sektorinfo=sector_rz_badge($rzadd);
	rahmen_oben(sector_head($sektorinfo.$sec_data['name']));
	echo '<div class="mod sec">';
	echo '<table class="sec-table"><thead><tr>'.
	sector_th($sec_lang['sys'], 'sec-sys', 1, $sort_sso, $sf).
	sector_th($sec_lang['name'], 'sec-name').
	sector_th($sec_lang['punkte'], 'sec-zahl', 3, $sort_sso, $sf).
	sector_th('FP', 'sec-zahl sec-fp').
	sector_th($sec_lang['kollektoren'], 'sec-zahl sec-kol', 2, $sort_sso, $sf).
	sector_th($sec_lang['aktion'], 'sec-aktion').'
	</tr></thead><tbody>';
	$sql = "SELECT * FROM de_user_data WHERE sector=? ORDER BY $orderby ASC LIMIT 300";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
	$anz = mysqli_num_rows($db_daten);
	while($row = mysqli_fetch_assoc($db_daten)){
		echo '<tr>';
		//system
		echo '<td>'.$row['system'].'</td>';
		//spielername
		echo '<td class="sec-wrap">'.$row['spielername'].'</td>';
		//punkte
		$csstag=($punkte*$sec_angriffsgrenze<=$row['score']) ? 'sec-gruen' : 'sec-rot';
		echo '<td class="sec-zahl '.$csstag.'"><span title="'.sector_punkte_tooltip().'">'.number_format($row['score'], 0,"",".").'</span></td>';

		//Flottenpunkte
		echo '<td class="sec-zahl sec-fp"><span title="Flottenpunkte&Punktewert aller Schiffe und Verteidigungsanlagen.<br>'.number_format($row['fleetscore'] ?? 0, 0,"",".").'">'.formatMasseinheit($row['fleetscore'] ?? 0).'</span></td>';

		//kollektoren
		$csstag=($col*$col_angriffsgrenze_final<=$row['col']) ? 'sec-gruen' : 'sec-rot';
		echo '<td class="sec-zahl '.$csstag.'"><span title="'.wellenrechner($row['col'], $maxcol, 1).'">'.number_format($row['col'], 0,"",".").'</span></td>';

		//aktion
		echo '<td class="sec-aktion"><a href="military.php?se='.$row['sector'].'&amp;sy='.$row['system'].'">F</a></td>';

		echo '</tr>';
	}

	//sektorendaten
	echo '</tbody><tfoot><tr><td colspan="6">'.$sec_lang['angriffsgrenze'].': '.number_format($sec_angriffsgrenze*100, 2,",",".").'% / '.number_format($col_angriffsgrenze_final*100, 2,",",".").'%</td></tr></tfoot>';
	echo '</table>';

	//hinweistext für npc-sektoren
	echo '<div class="mod-hinweis sec-npc"><img src="gp/g/symbol12.png" alt="" title="Info"><div>'.
	$sec_lang['npsecinfo1'].' '.$sec_lang['npsecinfo2'].get_free_artefact_places($_SESSION['ums_user_id']).'<br>'.$sec_lang['npsecinfo3'].
	'<br>'.$sec_lang['npsecinfo4'].'</div></div>';
	echo '</div>';
	rahmen_unten();

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

	//secmalus darf nicht größer als maximum sein
	if($sec_malus>$sv_sector_attmalus)$sec_malus=$sv_sector_attmalus;
	$sec_angriffsgrenze=$sv_attgrenze-$sv_attgrenze_whg_bonus+$sec_malus;

	//recyclotronbonus berechnen
	$rec_bonus=$sv_recyclotron_sector_bonus/$num*($secplatz-1);
	//recyclotronbonus darf nicht größer als das maximum sein
	if($rec_bonus>$sv_recyclotron_sector_bonus)$rec_bonus=$sv_recyclotron_sector_bonus;

	//angriffsgrenze für die kollektoren berechnen
	if($maxcol==0)$maxcol=1;
	$col_angriffsgrenze=$col*100/$maxcol;
	$col_angriffsgrenze_final=$col_angriffsgrenze/100*$sv_max_col_attgrenze;
	if($col_angriffsgrenze_final>$sv_max_col_attgrenze)$col_angriffsgrenze_final=$sv_max_col_attgrenze;
	if($col_angriffsgrenze_final<$sv_min_col_attgrenze)$col_angriffsgrenze_final=$sv_min_col_attgrenze;

	//Hinweis, wenn der Sektorstatus deaktiviert ist; steht oben im Rahmen
	$sec_meldung='';
	if($secstatdisable==1 AND $ownsector==$sf) $sec_meldung='<div class="mod-meldung mod-meldung-warn sec-meldung">'.$sec_lang['secstatdisable'].'</div>';

	$gesamtpunkte=0;$anz=0;
	//Spalten wie bisher; feste Breiten stehen im Stylesheet (Klassen am Spaltenkopf), die Namensspalte nimmt den Rest
	$output='<table class="sec-table"><thead><tr>'.
	sector_th($sec_lang['sys'], 'sec-sys', 1, $sort_sso, $sf).
	sector_th($sec_lang['rang'], 'sec-rang').
	sector_th($sec_lang['name'], 'sec-name').
	sector_th($sec_lang['rasse'], 'sec-rasse').
	sector_th($sec_lang['allianz'], 'sec-ally').
	sector_th($sec_lang['punkte'], 'sec-zahl', 3, $sort_sso, $sf).
	sector_th('FP', 'sec-zahl sec-fp').
	sector_th($sec_lang['kollektoren'], 'sec-zahl sec-kol', 2, $sort_sso, $sf).
	sector_th($sec_lang['aktion'], 'sec-aktion').'
	</tr></thead><tbody>';

	//die spielerdaten laden
	if($sf==1 && !isset($_REQUEST['showall'])){
		$sql = "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode,
		de_login.last_login, de_login.last_click, de_login.user_id, de_user_data.score, de_user_data.fleetscore, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag,
		de_user_data.status, de_user_data.votefor, de_user_data.rang, de_user_data.npc,
		de_user_data.kg01, de_user_data.kg02,  de_user_data.kg03,  de_user_data.kg04
		FROM de_login left join de_user_data on(de_login.user_id = de_user_data.user_id)WHERE de_login.status=1 AND de_user_data.sector=? ORDER BY $orderby ASC";
		$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sf]);
	}else{
		//alles anzeigen
		$sql = "SELECT de_user_data.spielername, de_login.owner_id, de_login.status AS lstatus, de_login.delmode,
		de_login.last_login, de_login.last_click, de_login.user_id, de_user_data.score, de_user_data.fleetscore, de_user_data.col, de_user_data.`system`, de_user_data.rasse, de_user_data.allytag,
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

		//eigene Zeile hervorheben
		$output.=($row['user_id']==$_SESSION['ums_user_id']) ? '<tr class="sec-eigen">' : '<tr>';
		////////////////////////////////////////////////////////////////////////
		//system inkl sk/bk und accountstatus
		////////////////////////////////////////////////////////////////////////
		$systemstr=$row['system'];
		if ($row["lstatus"]==2)$systemstr='['.$systemstr.']';//gesperrt
		if ($row["lstatus"]==3 AND $row["delmode"]>0) $systemstr='{'.$systemstr.'}';//löschmode
		if ($row["lstatus"]==3 AND $row["delmode"]==0) $systemstr='('.$systemstr.')';//umode

		if($sksystem==$row['system']){
			$systemstr='<span class="sec-gruen">^'.$systemstr.'^</span>';
		}

		$output.='<td>'.$systemstr.'</td>';

		////////////////////////////////////////////////////////////////////////
		//rang
		////////////////////////////////////////////////////////////////////////
		$rang='';
		if($row['npc']==0){
			$rang="<img src='gp/g/r/".$row['rang']."_g.gif' alt='' title='".$rangnamen1[$row['rang']]."'>";
		}

		$output.='<td>'.$rang.'</td>';

		////////////////////////////////////////////////////////////////////////
		//spielername, Spielertitel, details, im sektor online
		////////////////////////////////////////////////////////////////////////
		$playername=utf8_encode_fix(umlaut($row['spielername']));
		if(strtotime($row["last_click"])+1800 > time() AND $row["lstatus"]==1) $os=' *';else $os='';
		if ($ownsector==$sf AND $secstatdisable==0) $osown=$os;else $osown='';

		//Titel aus der Accountverwaltung; die Raute am Namen zeigt sie im Tooltip
		$userTitle='';
		$sql="SELECT * FROM ls_user_title LEFT JOIN ls_title ON (ls_user_title.title_id=ls_title.title_id) WHERE ls_user_title.user_id = ? ORDER BY ls_title.title ASC";
		$db_datenx=mysqli_execute_query($GLOBALS['dbi_ls'], $sql, [$row['owner_id']]);
		if(mysqli_num_rows($db_datenx) > 0){

			while ($rowx = mysqli_fetch_assoc($db_datenx)){
				$titleId=0; //Standardgrafik
				if(strpos($rowx['title'], "ERHABEN")!==false){
					$titleId=1;
				}

				$userTitle.="<div class='sec-titel'><img src='/gp/g/title".$titleId.".png' alt=''><span>".$rowx['title']."</span></div>";
			}
			$userTitle='<span class="user_title" title="'.$userTitle.'"></span>';
		}

		$output.='<td class="sec-wrap"><a href="details.php?se='.$sector.'&amp;sy='.$row['system'].'">'.$playername.$osown.$userTitle.'</a></td>';

		////////////////////////////////////////////////////////////////////////
		//rasse
		////////////////////////////////////////////////////////////////////////
		$knowrasse=0;
		$playerstatus=0;
		$rasse='';
		//hat man scandaten über die rasse/allianz?
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

		//im eigenen sektor sieht man alle rassen, außer in sektor 1
		if($sector==$ownsector AND $ownsector>1)$knowrasse=1;


		if($knowrasse==1)
		{
			if($row['rasse']==1)$rasse='<img src="gp/g/r/raceE.png" title="Die Ewigen" alt="" width="16" height="16">';
			if($row['rasse']==2)$rasse='<img src="gp/g/r/raceI.png" title="Ishtar" alt="" width="16" height="16">';
			if($row['rasse']==3)$rasse='<img src="gp/g/r/raceK.png" title="K&#180;Tharr" alt="" width="16" height="16">';
			if($row['rasse']==4)$rasse='<img src="gp/g/r/raceZ.png" title="Z&#180;tah-ara" alt="" width="16" height="16">';
			if($row['rasse']==5)$rasse='<img src="gp/g/r/raceD.png" title="DX61a23" alt="" width="16" height="16">';
		}

		$output.='<td>'.$rasse.'</td>';

		////////////////////////////////////////////////////////////////////////
		//allianz, sichtbarkeit durch ally, allybündnis, scandaten, sektor
		////////////////////////////////////////////////////////////////////////
		if($row['status']==1)$allytag=$row['allytag'];else $allytag='';
		$showallytag='';
		$csstag='';

		//festellen welche farbe das allytag hat
		//allypartner
		if (in_array($allytag, $allypartner)){
			$csstag='sec-gruen';
			$showallytag=$allytag;
		}
		//allyfeinde
		elseif (in_array($allytag, $allyfeinde)){
			$csstag='sec-rot';
			$showallytag=$allytag;
		}
		//ganze andere ally, nichts anzeigen, außer es gibt scandaten, oder es ist der eigene sektor
		elseif (($ownally != $allytag) OR ($allytag=='') OR ($ownally=='')){
			//anzeige im eigenen sektor
			if($ownsector==$sf AND $ownsector>1)
			{
				$csstag='sec-gelb';
				$showallytag=$allytag;
			}
			//allytag aus den scandaten
			elseif ($allytagscan!='')
			{
				//es gibt scandaten der ally
				$csstag='sec-gelb';
				$showallytag=$allytagscan;
			}
			else
			{
				//es gibt keine daten vom allytag
				$showallytag='';
			}
		}
		//eigene ally, tag anzeigen
		else {
			$csstag='sec-weiss';
			$showallytag=$allytag;
		}

		if($showallytag!=''){
			$showallytag='<a href="ally_detail.php?allytag='.urlencode($showallytag).'"><span class="'.$csstag.'">'.$showallytag.'</span></a>';
		}

		$output.='<td class="sec-wrap">'.$showallytag.'</td>';

		////////////////////////////////////////////////////////////////////////
		//punkte, fett bei ausgesetztem Kopfgeld
		////////////////////////////////////////////////////////////////////////
		$csstag=($punkte*$sec_angriffsgrenze<=$row['score']) ? 'sec-gruen' : 'sec-rot';
		$kopfgeld=null;
		if($sv_oscar!=1){
			if($row['kg01']>0 || $row['kg02']>0 || $row['kg03']>0 || $row['kg04']>0){
				$csstag.=' sec-kopfgeld';
				$kopfgeld=$row;
			}
		}

		$output.='<td class="sec-zahl '.$csstag.'"><span title="'.sector_punkte_tooltip($kopfgeld).'">'.number_format($row['score'], 0,"",".").'</span></td>';

		////////////////////////////////////////////////////////////////////////
		//Flottenpunkte: Punktewert aller Schiffe und Verteidigungsanlagen (fleetscore aus dem WT)
		////////////////////////////////////////////////////////////////////////
		$output.='<td class="sec-zahl sec-fp"><span title="Flottenpunkte&Punktewert aller Schiffe und Verteidigungsanlagen.<br>'.number_format($row['fleetscore'] ?? 0, 0,"",".").'">'.formatMasseinheit($row['fleetscore'] ?? 0).'</span></td>';

		////////////////////////////////////////////////////////////////////////
		//kollektoren
		////////////////////////////////////////////////////////////////////////
		$csstag=($col*$col_angriffsgrenze_final<=$row['col']) ? 'sec-gruen' : 'sec-rot';
		$output.='<td class="sec-zahl '.$csstag.'"><span title="'.wellenrechner($row['col'], $maxcol, 0).'">'.number_format($row['col'], 0,"",".").'</span></td>';

		////////////////////////////////////////////////////////////////////////
		//aktion
		////////////////////////////////////////////////////////////////////////
		$aktion='<a href="secret.php?a=s&amp;zsec1='.$sector.'&amp;zsys1='.$row['system'].'" title="Sonde starten">S</a> <a href="secret.php?a=a&amp;zsec2='.$sector.'&amp;zsys2='.$row['system'].'" title="Agenteneinsatz">A</a> <a href="military.php?se='.$sector.'&amp;sy='.$row['system'].'" title="Flotteneinsatz">F</a> ';
		$aktion.='<a href="secret.php?a=d&amp;zsec1='.$sector.'&amp;zsys1='.$row['system'].'"><img src="gp/g/ps_'.$playerstatus.'.gif" alt="" title="Geheimdienstinformationen"></a>';

		$output.='<td class="sec-aktion">'.$aktion.'</td>';

		$output.='</tr>';
	}

	//Geisterschiff, falls es gerade durch diesen Sektor treibt
	$output.=\DieEwigen\DE2\View\GhostShip\GhostShipView::sectorRow($geist, (int)$sf);

	$output.='</tbody></table>';
	$output.=sector_legende($sv_oscar!=1);


	//die sektorüberschrift zusammenbauen
	$sektorinfo=sector_rz_badge($rzadd);
	$sektorinfo.='<span title="Platz in der Sektorwertung">Platz: '.$sec_data['platz'].'</span>';
	$sektorinfo.=' &middot; Punkte: '.number_format($gesamtpunkte, 0,",",".");
	$sektorinfo=sector_head($sektorinfo, $sec_data['name']);

	//Sektor 1 soll einen festen Namen haben
	if($sf==1){
		$sektorinfo=sector_head(sector_rz_badge($rzadd).'Sektor 1 - Startsektor');
	}

	rahmen_oben($sektorinfo);
	echo '<div class="mod sec">'.$sec_meldung.$output;

	////////////////////////////////////////////////////////////////////////
	// Sektorraumbasis und Sektorartefakte
	////////////////////////////////////////////////////////////////////////

	//bild von der sternenbasis anzeigen
	$bed=$sec_data['techs'][1].$sec_data['techs'][2].$sec_data['techs'][3];
	$std=date("H");
	//1=sternenbasis 2=begrenzer 3=werft

	if ($bed=='100') {if ($std>6 && $std<20 )$bn='sbtag.gif'; else $bn='sbnacht.gif';}
	elseif ($bed=='110') {if ($std>6 && $std<20 )$bn='sbtagsfb.gif'; else $bn='sbnachtsfb.gif';}
	elseif ($bed=='101') {if ($std>6 && $std<20 )$bn='sbtagw.gif'; else $bn='sbnachtw.gif';}
	elseif ($bed=='111') {if ($std>6 && $std<20 )$bn='sbtagsfbw.gif'; else $bn='sbnachtsfbw.gif';}

	//schauen ob es artefakte gibt
	$sql = "SELECT id, artname, artdesc, color, picid FROM de_artefakt WHERE sector=?";
	$res = mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);
	$artnum = mysqli_num_rows($res);

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

		//die Farbe des Namens steht beim Artefakt in der Datenbank
		$atip[$c] = '<span style=\'color: #'.$row["color"].';\'>'.$row["artname"].'</span>&'.$desc;

		$artstr.='<a href="help.php?a=1" target="_blank" title="'.$atip[$c].'"><img src="gp/g/sa'.$row["picid"].'.gif" alt=""></a>';
		$c++;
	}
	if($artstr=='')$artstr='<span class="sec-none">keine</span>';

	//sektorraumbasistooltip erzeugen
	$basestr='';
	if($bed!='000'){
		$srbstr=$sec_lang['sekerweiterungen'].':';
		if($sec_data['techs'][2]>0)$srbstr.='<br>- '.$sec_lang['sekbldg1'];
		if($sec_data['techs'][3]>0)$srbstr.='<br>- '.$sec_lang['sekbldg2'];
		if($sec_data['techs'][4]>0)$srbstr.='<br>- '.$sec_lang['sekbldg3'];
		if($sec_data['techs'][5]>0)$srbstr.='<br>- '.$sec_lang['sekbldg4'];
		if($sec_data['techs'][6]>0)$srbstr.='<br>- '.$sec_lang['sekbldg5'];

		$stip = $sec_lang['sektorraumbasis'].'&'.$srbstr;
		$basestr='<a href="gp/g/big/'.strtoupper($bn).'" target="_blank"><img src="gp/g/'.$bn.'" alt="" name="sb" title="'.$stip.'"></a>';
	}
	if($basestr=='')$basestr='<span class="sec-none">nicht gebaut</span>';

	//Informationen, Sektorraumbasis und Sektorartefakte nebeneinander
	if($sector>1 AND $anz>0){
		$infostr='<table class="sec-info">'.
		sector_inforow('Angriffsgrenze Punkte', number_format($sec_angriffsgrenze*100, 2,",",".").'%',
			'Punkte-Angriffsgrenze&Ziele mit weniger als diesem Anteil deiner Punkte kannst du nicht angreifen. Steht dieser Sektor in der Sektorwertung hinter deinem, steigt die Grenze (Sektormalus).').
		sector_inforow('Angriffsgrenze Kollektoren', number_format($col_angriffsgrenze_final*100, 2,",",".").'%',
			'Kollektoren-Angriffsgrenze&Hat das Ziel mindestens diesen Anteil deiner Kollektoren, werden seine Kollektoren erobert, sonst zerst&ouml;rt.').
		sector_inforow($sec_lang['kollektoren'], number_format($gescol, 0,"",".")).
		sector_inforow($sec_lang['kollektorendurchschnitt'], number_format($gescol/$anz, 2,",",".")).
		sector_inforow($sec_lang['punktedurchschnitt'], number_format($gesamtpunkte/$anz, 0,",",".")).
		sector_inforow($sec_lang['platz'].' ('.$sec_lang['jetzt'].' / '.$sec_lang['gestern'].')', number_format($secplatz, 0,"",".").' / '.number_format($sec_data['platz_last_day'], 0,"",".")).
		sector_inforow($sec_lang['bewohntesysteme'], number_format($anz, 0,"",".")).
		sector_inforow($sec_lang['recyclingbonus'], number_format($rec_bonus, 2,",",".").'%',
			'Recyclingbonus&Zusatz f&uuml;r das Recyclotron der Verteidiger in diesem Sektor. Je weiter hinten der Sektor in der Sektorwertung steht, desto h&ouml;her, h&ouml;chstens '.$sv_recyclotron_sector_bonus.'%.').
		sector_inforow('Sektorartefakt-Haltezeit', number_format($sec_data['arthold'], 0,"",".").' WT',
			'Sektorartefakt-Haltezeit&Wirtschaftsticks, in denen der Sektor Sektorartefakte gehalten hat (je Artefakt und Tick +1).').
		'</table>';

		echo '<div class="sec-unten">
			<div class="sec-karte"><span class="mod-typ">'.$sec_lang['informationen'].'</span>'.$infostr.'</div>
			<div class="sec-karte sec-bild"><span class="mod-typ">'.$sec_lang['sektorraumbasis'].'</span><div>'.$basestr.'</div></div>
			<div class="sec-karte sec-bild"><span class="mod-typ">'.$sec_lang['sektorartefakte'].'</span><div>'.$artstr.'</div></div>
		</div>';

		if ($ownsector==$sf){
			echo '<div class="sec-knoepfe"><a href="politics.php"'.($_SESSION['ums_mobi']==0 ? ' target="h"' : '').' class="mod-btn">Sektorpolitik</a></div>';
		}
	}

	//im Startsektor stehen zunächst nur aktive Spieler, auf Wunsch alle
	if($sf==1){
		echo '<div class="sec-knoepfe"><a href="sector.php?sf=1&amp;showall=1" class="mod-btn mod-btn-leise">Alle Spieler anzeigen</a></div>';
	}

	echo '</div>';
	rahmen_unten();
}
?>
</div>

</body>
</html>
