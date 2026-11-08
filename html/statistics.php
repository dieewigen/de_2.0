<?php
include "inc/header.inc.php";
include "inc/lang/".$sv_server_lang."_statistics.lang.php";
include "functions.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, status, ally_id FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];
$restyp05=$row['restyp05'];$punkte=$row['score'];$newtrans=$row['newtrans'];$newnews=$row['newnews'];
$sector=$row['sector'];$system=$row['system'];$hasally=$row['status'];$ally_id=$row['ally_id'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $stat_lang['title']?></title>
<?php include "cssinclude.php"; ?>
</head>
<?php
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';

include "resline.php";

//Reiter wie auf den Allianzseiten
function statistik_menu($aktiv){
	global $stat_lang;
	$punkte=array(1=>$stat_lang['spieler'], 2=>$stat_lang['sektor'], 3=>$stat_lang['allianz'], 4=>$stat_lang['server']);
	$html='<div class="mod ally-navi stat-navi">';
	foreach($punkte as $mp=>$name){
		$html.='<a href="statistics.php?mp='.$mp.'" class="ally-reiter'.($mp==$aktiv ? ' ally-reiter-aktiv' : '').'">'.$name.'</a>';
	}
	return $html.'</div>';
}

function stat_zahl($wert){
	return number_format($wert, 0, ',', '.');
}

//eine Spalte einer Statistik-Abfrage als Werteliste (älteste zuerst)
function stat_werte($sql, $parameter){
	$werte=array();
	$db_daten=mysqli_execute_query($GLOBALS['dbi'], $sql, $parameter);
	while($row = mysqli_fetch_row($db_daten)){
		$werte[]=(float)$row[0];
	}
	return $werte;
}

//runder, ganzzahliger Abstand der Achsenlinien; die Achse reicht bis zum doppelten Abstand
function stat_schritt($max){
	$halb=$max/2;
	if($halb<=1){
		return 1;
	}
	$p=pow(10, floor(log10($halb)));
	foreach(array(1, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10) as $f){
		$s=$f*$p;
		if($s >= $halb && $s==floor($s)){
			return $s;
		}
	}
	return 10*$p;
}

//Achsenbeschriftung kurz halten: 12,5 Mio. statt 12.500.000
function stat_achse($wert){
	if($wert>=1000000){
		return str_replace(',0', '', number_format($wert/1000000, 1, ',', '.')).' Mio.';
	}
	return stat_zahl($wert);
}

//Liniendiagramm als SVG: eine Linie in der Rassenfarbe, Fläche darunter, aktueller Wert am Ende;
//$platz: kleiner ist besser, Platz 1 steht oben. Die Werte liegen zusätzlich als Liste darunter.
function stat_diagramm($titel, $werte, $platz=false){
	if(count($werte)==0){
		return '<div class="stat-karte"><div class="stat-kopf"><span class="mod-typ">'.$titel.'</span></div><div class="mod-leer">Noch keine Daten.</div></div>';
	}

	$breite=552; $hoehe=170;
	$links=50; $rechts=72; $oben=12; $unten=24;
	$pb=$breite-$links-$rechts;
	$ph=$hoehe-$oben-$unten;
	$n=count($werte);

	$schritt=stat_schritt(max($werte));
	$grenze=2*$schritt;
	if($platz){
		//Platz 1 oben, schlechtester Platz unten
		$y=function($w) use ($grenze, $oben, $ph){ return $oben+($w-1)/($grenze-1)*$ph; };
		$ticks=array_unique(array(1, $schritt, $grenze));
	}else{
		$y=function($w) use ($grenze, $oben, $ph){ return $oben+$ph-$w/$grenze*$ph; };
		$ticks=array(0, $schritt, $grenze);
	}
	$x=function($i) use ($n, $links, $pb){ return ($n==1) ? $links+$pb : $links+$i*$pb/($n-1); };

	$pos=array();
	$linie='';
	foreach($werte as $i => $w){
		$px=round($x($i), 1);
		$py=round($y($w), 1);
		$pos[]=array($px, $py);
		$linie.=($i==0 ? 'M' : 'L').$px.' '.$py;
	}
	$boden=$oben+$ph;
	$flaeche=$linie.'L'.$pos[$n-1][0].' '.$boden.'L'.$pos[0][0].' '.$boden.'Z';

	$svg='<svg viewBox="0 0 '.$breite.' '.$hoehe.'" role="img" aria-label="'.$titel.'">';
	//Raster: dünne Linien, Werte links
	foreach($ticks as $t){
		$ty=round($y($t), 1);
		$svg.='<line class="stat-raster" x1="'.$links.'" x2="'.($links+$pb).'" y1="'.$ty.'" y2="'.$ty.'"/>';
		$svg.='<text class="stat-achse" x="'.($links-6).'" y="'.($ty+3).'" text-anchor="end">'.($platz ? stat_zahl($t).'.' : stat_achse($t)).'</text>';
	}
	$svg.='<text class="stat-achse" x="'.$links.'" y="'.($hoehe-6).'">Beginn</text>';
	$svg.='<text class="stat-achse" x="'.($links+$pb).'" y="'.($hoehe-6).'" text-anchor="end">aktuell</text>';
	//beim Platz keine Fläche: dort ist unten schlechter, nicht weniger
	if(!$platz){
		$svg.='<path class="stat-flaeche" d="'.$flaeche.'"/>';
	}
	$svg.='<path class="stat-linie" d="'.$linie.'"/>';
	$letzter=$pos[$n-1];
	$svg.='<circle class="stat-punkt" cx="'.$letzter[0].'" cy="'.$letzter[1].'" r="4"/>';
	$svg.='<text class="stat-endwert" x="'.($letzter[0]+9).'" y="'.($letzter[1]+4).'">'.($platz ? stat_zahl(end($werte)).'.' : stat_zahl(end($werte))).'</text>';
	//Fadenkreuz für das Überfahren
	$svg.='<line class="stat-fadenkreuz" x1="0" x2="0" y1="'.$oben.'" y2="'.$boden.'" visibility="hidden"/>';
	$svg.='<circle class="stat-punkt stat-hover" cx="0" cy="0" r="4" visibility="hidden"/>';
	$svg.='</svg>';

	//Kopf: aktueller Wert und Veränderung seit der ersten Messung
	$erster=$werte[0];
	$aktuell=end($werte);
	if($platz){
		$diff=$erster-$aktuell;
		$text=($diff==0) ? 'unver&auml;ndert' : (abs($diff)==1 ? '1 Platz ' : stat_zahl(abs($diff)).' Pl&auml;tze ').($diff>0 ? 'besser' : 'schlechter');
	}else{
		$diff=$aktuell-$erster;
		$text=($diff==0) ? 'unver&auml;ndert' : ($diff>0 ? '+' : '&minus;').stat_zahl(abs($diff));
	}
	$chip=($diff>0) ? 'mod-chip mod-chip-gruen' : (($diff<0) ? 'mod-chip stat-chip-rot' : 'mod-chip');

	//alle Werte als Liste (lesbar ohne Überfahren)
	$liste='';
	foreach($werte as $i => $w){
		$liste.='<span><small>'.($i+1).'</small>'.($platz ? stat_zahl($w).'.' : stat_zahl($w)).'</span>';
	}

	return '
	<div class="stat-karte">
		<div class="stat-kopf">
			<span class="mod-typ">'.$titel.'</span>
			<span class="stat-wert">'.($platz ? 'Platz ' : '').stat_zahl($aktuell).'</span>
			<span class="'.$chip.'">'.$text.' seit Beginn</span>
		</div>
		<div class="stat-diagramm" tabindex="0" data-werte="'.htmlspecialchars(json_encode($werte), ENT_QUOTES).'" data-pos="'.htmlspecialchars(json_encode($pos), ENT_QUOTES).'" data-platz="'.($platz ? 1 : 0).'" data-breite="'.$breite.'">
			'.$svg.'
			<div class="stat-tip" hidden><b></b><small></small></div>
		</div>
		<details class="stat-tabelle"><summary>Alle '.$n.' Messwerte</summary><div class="stat-werteliste">'.$liste.'</div></details>
	</div>';
}

//Liste der Kollektorenverluste bzw. -eroberungen; negative Anzahl = beim Angriff zerstört
function stat_kollektoren($sql, $user_feld){
	global $stat_lang;
	$zeilen='';
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
	while($row = mysqli_fetch_assoc($db_daten)){
		$time=date("d.m.Y H:i", $row['time']);
		//spielername des gegners
		$duid=$row[$user_feld];
		$sql2 = "SELECT spielername, sector, `system` FROM de_user_data WHERE user_id=?";
		$result = mysqli_execute_query($GLOBALS['dbi'], $sql2, [$duid]);
		$num = mysqli_num_rows($result);
		if($num==1){
			$rowx = mysqli_fetch_assoc($result);
			$spielername='<a href="details.php?se='.$rowx['sector'].'&sy='.$rowx['system'].'" class="stat-spieler">'.$rowx['spielername'].' <small>'.$rowx['sector'].':'.$rowx['system'].'</small></a>';
		}
		else $spielername='<span class="stat-leise">'.$stat_lang['geloeschterspieler'].'</span>';
		$art=($row['colanz']<0) ? '<span class="mod-chip stat-chip-rot">zerst&ouml;rt</span>' : '<span class="mod-chip">erobert</span>';
		$zeilen.='<div class="stat-zeile stat-kol"><span class="stat-leise">'.$time.'</span><span class="stat-zahl"><b>'.stat_zahl(abs($row['colanz'])).'</b></span><span>'.$art.'</span><span>'.$spielername.'</span></div>';
	}
	return ($zeilen=='') ? '<div class="stat-hinweis">Keine Eintr&auml;ge.</div>' : '<div class="stat-liste">'.$zeilen.'</div>';
}

//Ereignisliste mit WT
function stat_ereignisse($zeilen){
	if(count($zeilen)==0){
		return '<div class="mod-leer">Es gibt noch keine Ereignisse.</div>';
	}
	$html='<div class="stat-zeile stat-ereignis stat-kopfzeile"><span>WT</span><span>Ereignis</span></div><div class="stat-liste">';
	foreach($zeilen as $z){
		$html.='<div class="stat-zeile stat-ereignis"><span class="stat-zahl stat-leise">'.$z[0].'</span><span>'.$z[1].'</span></div>';
	}
	return $html.'</div>';
}

if(!isset($_REQUEST['mp'])){
  $_REQUEST['mp']=1;
}

if($_REQUEST['mp']==1)
{
  echo statistik_menu(1);

  //rundenstart auslesen
  $db_datenx = mysqli_execute_query($GLOBALS['dbi'], "SELECT rundenstart_datum FROM de_system");
  $rowx = mysqli_fetch_assoc($db_datenx);
  $rundenstart_datum = $rowx["rundenstart_datum"];

  rahmen_oben($stat_lang['spieler']);
  echo '<div class="stat mod">';
  echo stat_diagramm($stat_lang['punkteentwicklung'], stat_werte("SELECT score FROM de_user_stat WHERE user_id=? AND datum>? ORDER BY datum ASC", [$_SESSION['ums_user_id'], $rundenstart_datum]));
  echo stat_diagramm($stat_lang['kollektorentwicklung'], stat_werte("SELECT col FROM de_user_stat WHERE user_id=? AND datum>? ORDER BY datum ASC", [$_SESSION['ums_user_id'], $rundenstart_datum]));
  echo '</div>';
  rahmen_unten();

  //aktivität: je Tag und Stunde inaktiv / nur Chat / aktiv, abgestuft in der Rassenfarbe
  rahmen_oben($stat_lang['aktivitaet']);
  echo '<div class="stat mod">';
  $stufen=array(0 => $stat_lang['legende1'], 1 => $stat_lang['legende2'], 2 => $stat_lang['legende3']);
  $sql = "SELECT * FROM de_user_stat WHERE user_id=? ORDER BY datum DESC LIMIT 7";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  $tage='';
  while($row = mysqli_fetch_assoc($db_daten))
  {
	$datum=date('d.m.', strtotime($row['datum']));
	$tage.='<span class="stat-tag">'.$datum.'</span>';
	for($i=0;$i<=23;$i++)
	{
	  $stufe=(int)$row['h'.$i];
	  $tage.='<span class="stat-zelle stat-stufe'.$stufe.'" title="'.$datum.' '.sprintf('%02d', $i).' Uhr&amp;'.($stufen[$stufe] ?? '').'"></span>';
	}
  }
  if($tage==''){
	echo '<div class="mod-leer">Noch keine Daten.</div>';
  }else{
	$kopf='<span></span>';
	for($i=0;$i<=23;$i++){
	  $kopf.='<span class="stat-stunde">'.($i%3==0 ? sprintf('%02d', $i) : '').'</span>';
	}
	echo '<div class="stat-aktivitaet">'.$kopf.$tage.'</div>';
	echo '<div class="stat-legende"><span><i class="stat-zelle stat-stufe0"></i>'.$stufen[0].'</span><span><i class="stat-zelle stat-stufe1"></i>'.$stufen[1].'</span><span><i class="stat-zelle stat-stufe2"></i>'.$stufen[2].'</span></div>';
  }
  echo '</div>';
  rahmen_unten();

  //kollektoren: verloren und erobert
  $sql = "SELECT SUM(CASE WHEN colanz < 0 THEN colanz * -1 ELSE colanz END) AS colanz FROM de_user_getcol WHERE zuser_id=?";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  $verloren = mysqli_fetch_assoc($db_daten);
  $sql = "SELECT SUM(colanz) AS colanz FROM de_user_getcol WHERE user_id=? AND colanz>0";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
  $erobert = mysqli_fetch_assoc($db_daten);

  rahmen_oben('Kollektoren');
  echo '<div class="stat mod">';
  echo '
	<div class="stat-kacheln">
		<div class="ov-wert"><span class="mod-typ">'.$stat_lang['verlorenekollektoren'].'</span><b>'.stat_zahl($verloren['colanz'] ?? 0).'</b></div>
		<div class="ov-wert"><span class="mod-typ">'.$stat_lang['erobertekollektoren'].'</span><b>'.stat_zahl($erobert['colanz'] ?? 0).'</b></div>
	</div>';
  echo '<div class="stat-abschnitt"><div class="mod-typ">'.$stat_lang['verlorenekollektoren'].'</div>';
  echo stat_kollektoren("SELECT * FROM de_user_getcol WHERE zuser_id=? ORDER BY time DESC", 'user_id');
  echo '</div>';
  echo '<div class="stat-abschnitt"><div class="mod-typ">'.$stat_lang['erobertekollektoren'].'</div>';
  echo stat_kollektoren("SELECT * FROM de_user_getcol WHERE user_id=? ORDER BY time DESC", 'zuser_id');
  echo '</div>';
  echo '<div class="stat-hinweis">Zerst&ouml;rt hei&szlig;t: Die Kollektoren wurden beim Angriff vernichtet statt erobert.</div>';
  echo '</div>';
  rahmen_unten();
}
elseif($_REQUEST['mp']==2)
{
  echo statistik_menu(2);

  rahmen_oben($stat_lang['sektor'].' '.$sector);
  echo '<div class="stat mod">';
  echo stat_diagramm($stat_lang['punkteentwicklung'], stat_werte("SELECT score FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC", [$sector]));
  echo stat_diagramm($stat_lang['kollektorentwicklung'], stat_werte("SELECT col FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC", [$sector]));
  echo stat_diagramm($stat_lang['platzentwicklung'], stat_werte("SELECT platz FROM de_sector_stat WHERE sec_id=? ORDER BY datum ASC", [$sector]), true);
  echo '</div>';
  rahmen_unten();

  rahmen_oben($stat_lang['sektorereignisse']);
  echo '<div class="stat mod">';
  $sql="SELECT * FROM de_news_sector WHERE sector=? ORDER BY wt DESC";
  $db_daten=mysqli_execute_query($GLOBALS['dbi'], $sql, [$sector]);

  //typdefinitionen:
  //2: ein spieler kommt
  //3: ein spieler geht
  //4: sektorkollektoren
  //5: sektorstatus sichtbar
  //6: sektorstatus unsichtbar
  //7: sektorgebäudebau
  $texte=array(
	2 => $stat_lang['spielerkommt'],
	3 => $stat_lang['spielerverloren'],
	4 => $stat_lang['sektorkollektoren'],
	5 => $stat_lang['sektorstatussichtbar'],
	6 => $stat_lang['sektorstatusversteckt'],
	7 => 'Bauauftrag',
  );
  $zeilen=array();
  while($row = mysqli_fetch_assoc($db_daten))
  {
	if(isset($texte[$row['typ']])){
	  $zeilen[]=array(number_format($row['wt'], 0,"","."), $texte[$row['typ']].': '.$row['text']);
	}
  }
  echo stat_ereignisse($zeilen);
  echo '</div>';
  rahmen_unten();
}
elseif($_REQUEST['mp']==3)
{
  echo statistik_menu(3);
  rahmen_oben($stat_lang['allianz']);
  echo '<div class="stat mod">';
  //schauen ob man eine allianz hat
  if ($hasally==1){
	echo stat_diagramm($stat_lang['punkteentwicklung'], stat_werte("SELECT score FROM de_ally_stat WHERE id=? ORDER BY datum ASC", [$ally_id]));
	echo stat_diagramm($stat_lang['kollektorentwicklung'], stat_werte("SELECT col FROM de_ally_stat WHERE id=? ORDER BY datum ASC", [$ally_id]));
	echo stat_diagramm($stat_lang['platzentwicklung'], stat_werte("SELECT platz FROM de_ally_stat WHERE id=? ORDER BY datum ASC", [$ally_id]), true);
	echo stat_diagramm($stat_lang['mitgliederentwicklung'], stat_werte("SELECT member FROM de_ally_stat WHERE id=? ORDER BY datum ASC", [$ally_id]));
  }
  else echo '<div class="mod-leer">'.$stat_lang['noally'].'</div>';
  echo '</div>';
  rahmen_unten();
}
elseif($_REQUEST['mp']==4)
{
  echo statistik_menu(4);
  //größter tick
  $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data");
  $row = mysqli_fetch_assoc($result);
  $wt = $row['tick']-96;

  //artefaktnamen auslesen
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, artname FROM `de_artefakt`");
  while($row = mysqli_fetch_assoc($db_daten))
  {
    $artdata[$row['id']]=$row['artname'];
  }

  //Serveralter
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_system");
  $row = mysqli_fetch_assoc($db_daten);

  rahmen_oben($stat_lang['server']);
  echo '<div class="stat mod">';
  echo '
	<div class="stat-kacheln">
		<div class="ov-wert"><span class="mod-typ">Wirtschaftsticks</span><b>'.stat_zahl($row['wt']).'</b><small>WT</small></div>
		<div class="ov-wert"><span class="mod-typ">Kampfticks</span><b>'.stat_zahl($row['kt']).'</b><small>KT</small></div>
	</div>';
  echo '</div>';
  rahmen_unten();

  //Serverevents
  rahmen_oben($stat_lang['serverereignisse']);
  echo '<div class="stat mod">';
  $sql = "SELECT * FROM de_news_server WHERE wt<=? ORDER BY id DESC";
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$wt]);
  //typdefinitionen:
  //0: sektorartefakt hypersturm
  //1: sektorartefakt angriff
  $zeilen=array();
  while($row = mysqli_fetch_assoc($db_daten))
  {
	if($row['typ']==0 || $row['typ']==1){
	  $daten=explode(';', $row['text']);
	  $details=array($stat_lang['sektorartefakt'].': <b>'.($artdata[$daten[0]] ?? '').'</b>');
	  if($daten[1]>0)$details[]=$stat_lang['herkunftssektor'].': <b>'.$daten[1].'</b>';
	  $details[]=$stat_lang['zielsektor'].': <b>'.$daten[2].'</b>';
	  $text=$row['typ']==0 ? $stat_lang['sektorartefakt1'] : $stat_lang['sektorartefakt2'];
	  $text.='<span class="stat-details">'.implode(' &middot; ', $details).'</span>';
	  $zeilen[]=array(number_format($row['wt'], 0,"","."), $text);
	}
  }
  echo stat_ereignisse($zeilen);
  echo '</div>';
  rahmen_unten();
}
?>
<script>
//Diagramme: Fadenkreuz folgt dem Zeiger und rastet am nächsten Messwert ein; Pfeiltasten bei Fokus
(function(){
	document.querySelectorAll('.stat-diagramm').forEach(function(d){
		var svg=d.querySelector('svg'), werte=JSON.parse(d.getAttribute('data-werte')), pos=JSON.parse(d.getAttribute('data-pos'));
		var platz=d.getAttribute('data-platz')=='1', breite=parseFloat(d.getAttribute('data-breite'));
		var linie=svg.querySelector('.stat-fadenkreuz'), punkt=svg.querySelector('.stat-hover'), tip=d.querySelector('.stat-tip');
		var aktiv=-1;
		function zeige(i){
			aktiv=Math.max(0, Math.min(werte.length-1, i));
			var p=pos[aktiv];
			linie.setAttribute('x1', p[0]); linie.setAttribute('x2', p[0]); linie.setAttribute('visibility', 'visible');
			punkt.setAttribute('cx', p[0]); punkt.setAttribute('cy', p[1]); punkt.setAttribute('visibility', 'visible');
			tip.querySelector('b').textContent=(platz ? 'Platz ' : '')+werte[aktiv].toLocaleString('de-DE');
			tip.querySelector('small').textContent='Messung '+(aktiv+1)+' von '+werte.length;
			tip.hidden=false;
			//links oder rechts vom Fadenkreuz, damit der Hinweis im Diagramm bleibt
			var rel=p[0]/breite;
			tip.style.left=(rel*100)+'%';
			tip.style.transform=rel>0.6 ? 'translateX(calc(-100% - 10px))' : 'translateX(10px)';
		}
		function verstecke(){
			linie.setAttribute('visibility', 'hidden');
			punkt.setAttribute('visibility', 'hidden');
			tip.hidden=true;
			aktiv=-1;
		}
		svg.addEventListener('pointermove', function(e){
			var r=svg.getBoundingClientRect(), x=(e.clientX-r.left)/r.width*breite, best=0;
			for(var i=1;i<pos.length;i++){
				if(Math.abs(pos[i][0]-x)<Math.abs(pos[best][0]-x)){ best=i; }
			}
			zeige(best);
		});
		svg.addEventListener('pointerleave', verstecke);
		d.addEventListener('blur', verstecke);
		d.addEventListener('keydown', function(e){
			if(e.key=='ArrowLeft'){ zeige(aktiv<0 ? werte.length-1 : aktiv-1); e.preventDefault(); }
			if(e.key=='ArrowRight'){ zeige(aktiv<0 ? 0 : aktiv+1); e.preventDefault(); }
		});
		d.addEventListener('focus', function(){ zeige(werte.length-1); });
	});
})();
</script>
</body>
</html>
