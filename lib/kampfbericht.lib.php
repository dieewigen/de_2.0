<?php
require_once __DIR__.'/../vendor/autoload.php';

//Haltung einer Seite unter dem Namen, nur in Berichten mit Haltungen (seit Okt. 2026) und nur bei echtem Kampf
function kampfberichtBG_haltung($paar, $seite){
	$eigene=intval($paar['haltung'.$seite] ?? 0);
	$gegner=intval($paar['haltung'.($seite==1 ? 2 : 1)] ?? 0);
	if($eigene<1 || $gegner<1){
		return '';
	}

	$text=\DieEwigen\DE2\Model\Battleground\Haltung::name($eigene);
	if(intval($paar['wahl'.$seite] ?? 0)==0){
		$text.=' (Zufall)';
	}
	if(\DieEwigen\DE2\Model\Battleground\Haltung::vorteil($eigene, $gegner)>0){
		$text.=' &middot; <span class="bg-vorteil">Vorteil</span>';
	}

	return '<small>'.$text.'</small>';
}

//Battleground-Bericht (de_user_news.typ 70): je Runde die Paarungen, Sieger grün, Verlierer rot, die eigene Paarung
//hervorgehoben; Stil im Block "kb"/"bg-" in gp/de-main.scss
function showkampfberichtBG($data){
	$kb=@unserialize(base64_decode($data));
	if(!is_array($kb)){
		return '<div class="kb"><div class="kb-keine">Dieser Bericht ist nicht lesbar.</div></div>';
	}
	$ich=$_SESSION['ums_spielername'] ?? '';
	$runden=count($kb);

	$html='<div class="kb kb-bg"><div class="kb-kopf"><div><span class="mod-typ">Battleground</span><b class="kb-ort">Ergebnis</b></div>'
		.($runden>1 ? '<span class="mod-chip">'.$runden.' Runden</span>' : '').'</div>';
	foreach($kb as $r=>$paare){
		if(!is_array($paare) || count($paare)==0){
			continue;
		}
		$html.='<div class="bg-runde">'.($runden>1 ? '<span class="mod-typ">Runde '.($r+1).'</span>' : '')
			.'<div class="bg-gewinn">Die Gewinner erhalten <b>'.($paare[0]['gewinn'] ?? '').'</b></div>';
		foreach($paare as $p){
			//wie bisher: gewinnt Seite 1, verliert Seite 2, sonst umgekehrt; ohne Gegner ist es ein Freilos
			$sieger1=(($p['winner_user_id'] ?? null)==($p['user_id1'] ?? null));
			$eigen=false;
			$seiten=array();
			foreach(array(1, 2) as $s){
				$name=(string)($p['spielername'.$s] ?? '');
				if($name===''){
					continue;
				}
				$teile=explode('&nbsp;', $name);
				if($ich!=='' && $teile[0]===$ich){
					$eigen=true;
				}
				$gewonnen=($s==1) ? $sieger1 : !$sieger1;
				$seiten[]='<span class="bg-spieler '.($gewonnen ? 'bg-sieger' : 'bg-verlierer').'">'.$name.kampfberichtBG_haltung($p, $s).'</span>';
			}
			if(count($seiten)==0){
				continue;
			}
			$html.='<div class="bg-paar'.($eigen ? ' bg-eigen' : '').'">'.$seiten[0]
				.(count($seiten)>1 ? '<span class="bg-vs">gegen</span>'.$seiten[1] : '<span class="bg-vs">Freilos</span><span></span>').'</div>';
		}
		$html.='</div>';
	}

	return $html.'</div>';
}

//Zahl mit Tausenderpunkt
function kb_zahl($n){
	return number_format((float)$n, 0, '', '.');
}

//Anteil in Prozent mit einer Nachkommastelle
function kb_prozent($teil, $ganz){
	return number_format($ganz>0 ? $teil*100/$ganz : 0, 1, ',', '.').' %';
}

//Teilnehmer "Name [E](3:12), Name [K](4:1)" als Abzeichen in der Rassenfarbe, der eigene Name hervorgehoben
function kb_namen($liste, $ich){
	$html='';
	foreach(explode(', ', $liste) as $name){
		if($name===''){
			continue;
		}
		$klasse='kb-name';
		if(preg_match('/\[([EIKZD])\]\(\d+:\d+\)$/', $name, $m)){
			$klasse.=' kb-r'.strpos('EIKZD', $m[1]);
		}
		if($name===$ich){
			$klasse.=' kb-ich';
		}
		$html.='<span class="'.$klasse.'">'.$name.'</span>';
	}
	return $html;
}

//Zeile der Textfassung zum Kopieren
function kb_textzeile($name, $ein, $ver){
	return '  '.html_entity_decode($name, ENT_QUOTES, 'UTF-8').': '.kb_zahl($ein).' eingesetzt, '.kb_zahl($ver).' verloren'."\n";
}

//Zeile der Einheitentabelle: Name, dann je Gruppe eingesetzt, geblockt, überlebt; null statt Werten gibt Striche
function kb_zeile($klasse, $name, array $gruppen){
	$html='<tr class="'.$klasse.'"><td>'.$name.'</td>';
	foreach($gruppen as $g){
		if($g===null){
			$html.='<td class="kb-leer">-</td><td class="kb-leer">-</td><td class="kb-leer">-</td>';
		}else{
			$html.='<td>'.kb_zahl($g[0]).'</td><td>'.kb_zahl($g[1]).'</td><td>'.kb_zahl($g[0]-$g[2]).'</td>';
		}
	}
	return $html.'</tr>';
}

//Zeile der Bilanz: Angreifer links, Verteidiger rechts, dazwischen der Anteil beider Seiten, rechts das Verhältnis wie im alten Bericht (1 : x)
function kb_bilanz($label, $a, $v, $atext, $vtext){
	$summe=$a+$v;
	$pa=$summe>0 ? round($a*100/$summe) : 50;
	$verh=$a>0 ? '1 : '.number_format($v/$a, 2, ',', '.') : '-';
	return '<tr><td>'.$label.'</td><td>'.$atext.'</td><td><div class="kb-anteil"><span class="kb-anteil-a" style="width: '.$pa.'%"></span><span class="kb-anteil-v" style="width: '.(100-$pa).'%"></span></div></td><td>'.$vtext.'</td><td class="kb-verh">'.$verh.'</td></tr>';
}

//Kampfbericht (de_user_news.typ 57): Kopf mit Ergebnis, beide Seiten, eigenes Ergebnis, dann die Einheiten wie im alten
//Bericht mit Angreifer, Verteidiger und eigenen Einheiten nebeneinander (alle Einheiten der beteiligten Rassen, damit die
//Klassen zeilenweise vergleichbar bleiben), zum Schluss die Statistik. Spielerwunsch vom 2026-10-15.
//$text ist der serialisierte Bericht aus tickler/kt_systemkampf.php, $schiffspunkte[rasse][index] die Punkte je Einheit (Türme ab 10).
//Die Rassenfarben der früheren Zellklassen k1 bis k10 leben als .kb-r0 bis .kb-r4 in gp/de-main.scss weiter.
function showkampfberichtV1($text, $rasse, $spielername, $sector, $system, $schiffspunkte){
	global $sv_anz_rassen, $sv_anz_schiffe, $sv_anz_tuerme, $sv_server_lang, $sv_oscar;

	$kbd=@unserialize($text);
	if(!is_array($kbd) || !isset($kbd['daten'])){
		return '<div class="kb"><div class="kb-keine">Dieser Kampfbericht ist nicht lesbar.</div></div>';
	}
	$daten=$kbd['daten'];
	$ds=(isset($kbd['daten_spieler']) && is_array($kbd['daten_spieler'])) ? $kbd['daten_spieler'] : array();
	$rassen=(isset($daten['rassen']) && is_array($daten['rassen'])) ? $daten['rassen'] : array();
	$turmrasse=(int)($daten['target_rasse'] ?? 0);
	$eigene_rasse=max(0, min($sv_anz_rassen-1, (int)$rasse-1));
	$ort=(int)($daten['sector'] ?? 0).':'.(int)($daten['system'] ?? 0);

	//die Sprachdatei baut einige Sätze beim Einbinden aus $kkollies und $exp
	$kkollies=(int)($daten['colstolen'] ?? 0);
	$kollieserbeutet=(int)($ds['colstolen'] ?? 0);
	$exp=kb_zahl($ds['exp'] ?? 0);
	unset($kbl_lang);
	include 'inc/lang/'.$sv_server_lang.'_kampfbericht.lib.lang.php';

	//eigene Seite: der eigene Name steht in der Angreifer- oder der Verteidigerliste
	$flaggen=array(1=>'E', 2=>'I', 3=>'K', 4=>'Z', 5=>'D');
	$ich=$spielername.' ['.($flaggen[$rasse] ?? 'E').']('.$sector.':'.$system.')';
	$atterliste=(string)($daten['atterliste'] ?? '');
	$defferliste=(string)($daten['defferliste'] ?? '');
	//im Geheimdienst ist es der Bericht eines anderen Spielers, dann steht sein Name statt "dein"
	$du=($spielername==($_SESSION['ums_spielername'] ?? ''));
	$seite='';
	if(strpos($atterliste, $ich)!==false){
		$seite='a';
	}elseif(strpos($defferliste, $ich)!==false){
		$seite='v';
	}

	//Ergebnis: abgewehrt oder Kollektoren haben den Besitzer gewechselt
	if($kkollies==-1){
		$satz=$kbl_lang['abgewehrt'];
		$chip='Angriff abgewehrt';
		$ton=$seite=='a' ? 'kb-schlecht' : ($seite=='v' ? 'kb-gut' : '');
	}else{
		$satz=$kkollies==1 ? $kbl_lang['deffercollosts'] : $kbl_lang['deffercollostp'];
		if($kollieserbeutet>0){
			$satz.=str_replace('{WERT1}', $kollieserbeutet, $kollieserbeutet==1 ? $kbl_lang['attercolwins'] : $kbl_lang['attercolwinp']);
		}elseif($kollieserbeutet<0){
			$satz.=str_replace('{WERT1}', -$kollieserbeutet, $kollieserbeutet==-1 ? $kbl_lang['attercolwinsdestroy'] : $kbl_lang['attercolwinpdestroy']);
		}
		$chip=kb_zahl($kkollies).' '.($kkollies==1 ? $kbl_lang['col'] : $kbl_lang['cols']).($seite=='a' ? ' erobert' : ' verloren');
		$ton=$seite=='a' ? 'kb-gut' : ($seite=='v' ? 'kb-schlecht' : '');
	}

	//eigene Einheiten: die dritte Gruppe gibt es nur, wenn welche im Kampf waren
	$spieler=(isset($kbd['einheiten_spieler']) && is_array($kbd['einheiten_spieler'])) ? $kbd['einheiten_spieler'] : array();
	$eigene_da=false;
	for($j=0;$j<$sv_anz_schiffe;$j++){
		if((int)($spieler[0][$j] ?? 0)>0){
			$eigene_da=true;
		}
	}
	$eig=array(0, 0, 0, 0);
	$eigentext='';

	//Einheiten je beteiligter Rasse, Angreifer und Verteidiger nebeneinander; Summen wie im alten Bericht:
	//Anzahl, verloren, Punktewert, verlorene Punkte
	$ges=array('a'=>array(0, 0, 0, 0), 'v'=>array(0, 0, 0, 0));
	$texte=array('a'=>'', 'v'=>'');
	$zeilen='';
	$spalten=$eigene_da ? 10 : 7;
	for($r=0;$r<$sv_anz_rassen;$r++){
		if(empty($rassen[$r])){
			continue;
		}
		for($i=0;$i<$sv_anz_schiffe;$i++){
			if(($rassennamen[$r][$i] ?? 'NA')=='NA'){
				continue;
			}
			$punkte=(float)($schiffspunkte[$r][$i] ?? 0);
			$gruppen=array();
			foreach(array('a'=>'einheiten_atter', 'v'=>'einheiten_deffer') as $s=>$key){
				$g=array((int)($kbd[$key][0][$r][$i] ?? 0), (int)($kbd[$key][1][$r][$i] ?? 0), (int)($kbd[$key][2][$r][$i] ?? 0));
				$gruppen[]=$g;
				if($g[0]>0){
					$ges[$s][0]+=$g[0];
					$ges[$s][1]+=$g[2];
					$ges[$s][2]+=$g[0]*$punkte;
					$ges[$s][3]+=$g[2]*$punkte;
					$texte[$s].=kb_textzeile($rassennamen[$r][$i], $g[0], $g[2]);
				}
			}
			if($eigene_da){
				if($r==$eigene_rasse){
					$g=array((int)($spieler[0][$i] ?? 0), (int)($spieler[1][$i] ?? 0), (int)($spieler[2][$i] ?? 0));
					$gruppen[]=$g;
					if($g[0]>0){
						$eig[0]+=$g[0];
						$eig[1]+=$g[2];
						$eig[2]+=$g[0]*$punkte;
						$eig[3]+=$g[2]*$punkte;
						$eigentext.=kb_textzeile($rassennamen[$r][$i], $g[0], $g[2]);
					}
				}else{
					$gruppen[]=null;
				}
			}
			$zeilen.=kb_zeile('kb-r'.$r, $rassennamen[$r][$i], $gruppen);
		}
	}

	//Türme des Verteidigers, in der Farbe der angegriffenen Rasse
	$tuerme=(isset($kbd['tuerme']) && is_array($kbd['tuerme'])) ? $kbd['tuerme'] : array();
	$turmsumme=0;
	for($i=0;$i<$sv_anz_tuerme;$i++){
		$turmsumme+=(int)($tuerme[0][$i] ?? 0);
	}
	if($turmsumme>0){
		$zeilen.='<tr class="kb-trenner"><td colspan="'.$spalten.'">'.$kbl_lang['tuerme'].'</td></tr>';
		$turmtext='';
		for($i=0;$i<$sv_anz_tuerme;$i++){
			if(($turmnamen[$turmrasse][$i] ?? 'NA')=='NA'){
				continue;
			}
			$g=array((int)($tuerme[0][$i] ?? 0), (int)($tuerme[1][$i] ?? 0), (int)($tuerme[2][$i] ?? 0));
			$punkte=(float)($schiffspunkte[$turmrasse][$i+10] ?? 0);
			$ges['v'][0]+=$g[0];
			$ges['v'][1]+=$g[2];
			$ges['v'][2]+=$g[0]*$punkte;
			$ges['v'][3]+=$g[2]*$punkte;
			if($g[0]>0){
				$turmtext.=kb_textzeile($turmnamen[$turmrasse][$i], $g[0], $g[2]);
			}
			$zeilen.=kb_zeile('kb-r'.$turmrasse, $turmnamen[$turmrasse][$i], $eigene_da ? array(null, $g, null) : array(null, $g));
		}
		$texte['v'].='  '.$kbl_lang['tuerme'].":\n".$turmtext;
	}

	//Hitpoints entscheiden den Sieg: weniger anteilige Verluste gewinnen
	$hp=array('a'=>(float)($daten['atter_hp'] ?? 0), 'v'=>(float)($daten['deffer_hp'] ?? 0));
	$hpv=array('a'=>(float)($daten['atter_hp_lost'] ?? 0), 'v'=>(float)($daten['deffer_hp_lost'] ?? 0));
	$quote=array('a'=>($hp['a']>0 ? $hpv['a']*100/$hp['a'] : 0), 'v'=>($hp['v']>0 ? $hpv['v']*100/$hp['v'] : 0));
	$besser='';
	if($hp['a']>0 && $hp['v']>0 && $quote['a']!=$quote['v']){
		$besser=$quote['a']<$quote['v'] ? 'a' : 'v';
	}

	//Kopf: Ort und Ergebnis
	$html='<div class="kb">';
	$html.='<div class="kb-kopf"><div><span class="mod-typ">Kampf im System</span><b class="kb-ort">'.$ort.'</b></div><span class="kb-ergebnis '.$ton.'">'.$chip.'</span></div>';

	//beide Seiten mit Teilnehmern
	$html.='<div class="kb-seiten">';
	foreach(array('a'=>array($kbl_lang['angreifer'], $atterliste), 'v'=>array($kbl_lang['verteidiger'], $defferliste)) as $s=>$p){
		$html.='<div class="kb-seite kb-seite-'.$s.'"><div class="kb-seite-kopf"><span class="mod-typ">'.$p[0].'</span>'
			.($besser==$s ? '<span class="mod-chip mod-chip-gruen" title="Diese Seite hat anteilig weniger Hitpoints verloren.">weniger Verluste</span>' : '')
			.'</div><div class="kb-namen">'.kb_namen($p[1], $ich).'</div></div>';
	}
	$html.='</div>';
	$html.='<p class="kb-text">'.$satz.'</p>';

	//eigenes Ergebnis wie im Kopf des alten Berichts: Admiral, Kriegsartefakte, Recycling, Kopfgeld
	$chips='<span class="mod-chip" title="'.$kbl_lang['admiralexp'].'">Admiral <b>+'.$exp.' EP</b></span>';
	$chips.='<span class="mod-chip">'.$kbl_lang['erhaltenekartefakt'].' <b>'.kb_zahl($ds['kartefakt'] ?? 0).'</b></span>';
	$chips.='<span class="mod-chip">'.($rassennamen[$eigene_rasse][4] ?? '').'-'.$kbl_lang['recycling'].' <b>'.kb_zahl($ds['recycling1'] ?? 0).' M</b> <b>'.kb_zahl($ds['recycling2'] ?? 0).' D</b></span>';
	$kg=array((float)($ds['kg_set_01'] ?? 0), (float)($ds['kg_set_02'] ?? 0), (float)($ds['kg_set_03'] ?? 0), (float)($ds['kg_set_04'] ?? 0));
	if($sv_oscar!=1 && array_sum($kg)>0){
		$chips.='<span class="mod-chip mod-chip-warn">Ausgesetztes Kopfgeld <b>'.kb_zahl($kg[0]).' M</b> <b>'.kb_zahl($kg[1]).' D</b> <b>'.kb_zahl($kg[2]).' I</b> <b>'.kb_zahl($kg[3]).' E</b></span>';
	}
	$html.='<div class="kb-dein"><span class="mod-typ">'.($du ? 'Dein Ergebnis' : 'Ergebnis von '.$spielername).'</span><div class="kb-chips">'.$chips.'</div></div>';

	//Einheiten: eine Tabelle mit Angreifer, Verteidiger und eigenen Einheiten nebeneinander, Spalten wie im alten Bericht
	$html.='<table class="kb-einheiten"><thead><tr class="kb-gruppen"><th></th><th colspan="3" class="kb-ga">'.$kbl_lang['angreifer'].'</th><th colspan="3" class="kb-gv">'.$kbl_lang['verteidiger'].'</th>'
		.($eigene_da ? '<th colspan="3" class="kb-ge">'.$kbl_lang['eigene'].'</th>' : '').'</tr>'
		.'<tr class="kb-unter"><th>'.$kbl_lang['einheit'].'</th>'.str_repeat('<th>'.$kbl_lang['eingesetzt'].'</th><th>'.$kbl_lang['geblockt'].'</th><th>'.$kbl_lang['ueberlebt'].'</th>', $eigene_da ? 3 : 2).'</tr></thead>'
		.'<tbody>'.$zeilen.'</tbody></table>';

	//Statistik am Ende wie im alten Bericht: Bilanz beider Seiten, eigener Anteil
	$html.='<div class="kb-statistik"><span class="mod-typ">'.$kbl_lang['statistik'].'</span>'
		.'<table class="kb-bilanz"><thead><tr><th></th><th>'.$kbl_lang['angreifer'].'</th><th></th><th>'.$kbl_lang['verteidiger'].'</th><th>'.$kbl_lang['verhaeltnis'].'</th></tr></thead><tbody>'
		.kb_bilanz($kbl_lang['einheitenanzahl'], $ges['a'][0], $ges['v'][0], kb_zahl($ges['a'][0]), kb_zahl($ges['v'][0]))
		.kb_bilanz($kbl_lang['verloreneeinheiten'], $ges['a'][1], $ges['v'][1], kb_zahl($ges['a'][1]).' <small>'.kb_prozent($ges['a'][1], $ges['a'][0]).'</small>', kb_zahl($ges['v'][1]).' <small>'.kb_prozent($ges['v'][1], $ges['v'][0]).'</small>')
		.kb_bilanz($kbl_lang['einheitenpunktewert'], $ges['a'][2], $ges['v'][2], kb_zahl($ges['a'][2]), kb_zahl($ges['v'][2]))
		.kb_bilanz($kbl_lang['verlorenepunkte'], $ges['a'][3], $ges['v'][3], kb_zahl($ges['a'][3]).' <small>'.kb_prozent($ges['a'][3], $ges['a'][2]).'</small>', kb_zahl($ges['v'][3]).' <small>'.kb_prozent($ges['v'][3], $ges['v'][2]).'</small>')
		.kb_bilanz('Verlorene Hitpoints', $hpv['a'], $hpv['v'], kb_zahl($hpv['a']).' <small>'.number_format($quote['a'], 1, ',', '.').' %</small>', kb_zahl($hpv['v']).' <small>'.number_format($quote['v'], 1, ',', '.').' %</small>')
		.'</tbody></table>';
	if($seite!='' && $eig[0]>0){
		$html.='<div class="kb-chips"><span class="mod-chip" title="Anteil deiner Einheiten an der eigenen Seite">'.($du ? 'Dein Anteil' : 'Anteil').' <b>'.kb_prozent($eig[0], $ges[$seite][0]).'</b></span>'
			.'<span class="mod-chip">Eigene Verluste <b>'.kb_zahl($eig[1]).'</b> Einheiten, <b>'.kb_zahl($eig[3]).'</b> Punkte</span></div>';
	}
	$html.='</div>';

	//Textfassung zum Kopieren (Chat, Discord): Kopf, Einheiten je Seite, Statistik
	static $kb_nr=0;
	$kb_nr++;
	$klar=function($s){
		return trim(html_entity_decode(strip_tags((string)$s), ENT_QUOTES, 'UTF-8'));
	};
	$text='Kampfbericht '.$ort."\n"
		.$kbl_lang['angreifer'].': '.$klar($atterliste)."\n".$kbl_lang['verteidiger'].': '.$klar($defferliste)."\n".$klar($satz)."\n\n"
		.$kbl_lang['angreifer'].":\n".$texte['a'].$kbl_lang['verteidiger'].":\n".$texte['v']
		.($eigentext!=='' ? ($du ? 'Deine Einheiten' : 'Einheiten von '.$spielername).":\n".$eigentext : '')
		."\n".$kbl_lang['statistik'].":\n"
		.'  '.$kbl_lang['einheitenanzahl'].': '.kb_zahl($ges['a'][0]).' / '.kb_zahl($ges['v'][0])."\n"
		.'  '.$kbl_lang['verloreneeinheiten'].': '.kb_zahl($ges['a'][1]).' ('.kb_prozent($ges['a'][1], $ges['a'][0]).') / '.kb_zahl($ges['v'][1]).' ('.kb_prozent($ges['v'][1], $ges['v'][0]).")\n"
		.'  '.$kbl_lang['einheitenpunktewert'].': '.kb_zahl($ges['a'][2]).' / '.kb_zahl($ges['v'][2])."\n"
		.'  '.$kbl_lang['verlorenepunkte'].': '.kb_zahl($ges['a'][3]).' ('.kb_prozent($ges['a'][3], $ges['a'][2]).') / '.kb_zahl($ges['v'][3]).' ('.kb_prozent($ges['v'][3], $ges['v'][2]).")\n"
		.'  Verlorene Hitpoints: '.number_format($quote['a'], 1, ',', '.').' % / '.number_format($quote['v'], 1, ',', '.')." %\n";

	//Siegregel wie im alten Bericht, daneben die Knöpfe zum Kopieren und Teilen (data-kopieren/data-teilen, js/de_fn.js)
	$html.='<div class="kb-fuss"><details class="kb-regel"><summary>Wie wird der Sieger bestimmt?</summary><p>Es gewinnt die Seite, die weniger Hitpoints im Verhältnis zu ihren gesamten Hitpoints verloren hat. In den Hitpoints sind alle Boni eingerechnet.<br>'
		.'Verluste '.$kbl_lang['angreifer'].': '.number_format($quote['a'], 2, ',', '.').' %<br>Verluste '.$kbl_lang['verteidiger'].': '.number_format($quote['v'], 2, ',', '.').' %</p></details>'
		.'<div class="kb-knoepfe"><button type="button" class="mod-btn mod-btn-leise kb-knopf" data-kopieren="kb-quelle-'.$kb_nr.'">Als Text kopieren</button>'
		.'<button type="button" class="mod-btn mod-btn-leise kb-knopf" data-teilen="kb-quelle-'.$kb_nr.'">WhatsApp</button></div>'
		.'<textarea class="kb-quelle" id="kb-quelle-'.$kb_nr.'" hidden readonly>'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'</textarea></div>';
	$html.='</div>';

	return $html;
}
