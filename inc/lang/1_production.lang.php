<?php
$production_lang[aktiveauftraege]='Aktive Bauauftr&auml;ge';
$production_lang[artefakte]='-Artefakte: ';
$production_lang[bauen]='Bauen';
$production_lang[baukostenreduz]='Baukostenreduzierung durch ';
$production_lang[beschreibung]='Durch die Erforschung neuer Technologien ist es m&ouml;glich, bessere Schiffe herzustellen. Zur Orientierung dient der Technologiebaum, den man hier finden kann.';
$production_lang[besonderheittransmitterschiff]='Besonderheit: Diese Einheit ist in der Lage Kollektoren zu erobern, wird dabei aber vernichtet.';
//besonderheit Jagdboote
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang[besonderheitjagdboot]='Besonderheit: Ben&ouml;tigt Unterst&uuml;tzung durch Kreuzer/Schlachtschiffe/Tr&auml;ger, sonst ist die Einheit geschw&auml;cht.
		<br>1 Kreuzer unterst&uuml;tzt 10 Jagdboote.
		<br>1 Schlachtschiff unterst&uuml;tzt 22 Jagdboote.
		<br>1 Tr&auml;ger unterst&uuml;tzt 34 Jagdboote.';
		break;
	case 2:
		$production_lang[besonderheitjagdboot]='Besonderheit: Ben&ouml;tigt Unterst&uuml;tzung durch Kreuzer/Schlachtschiffe/Tr&auml;ger, sonst ist die Einheit geschw&auml;cht.
		<br>1 Kreuzer unterst&uuml;tzt 12 Jagdboote.
		<br>1 Schlachtschiff unterst&uuml;tzt 28 Jagdboote.
		<br>1 Tr&auml;ger unterst&uuml;tzt 43 Jagdboote.';
		break;	
	case 3:
		$production_lang[besonderheitjagdboot]='Besonderheit: Ben&ouml;tigt Unterst&uuml;tzung durch Kreuzer/Schlachtschiffe/Tr&auml;ger, sonst ist die Einheit geschw&auml;cht.
		<br>1 Kreuzer unterst&uuml;tzt 6 Jagdboote.
		<br>1 Schlachtschiff unterst&uuml;tzt 14 Jagdboote.
		<br>1 Tr&auml;ger unterst&uuml;tzt 26 Jagdboote.';
		break;		
	case 4:
		$production_lang[besonderheitjagdboot]='Besonderheit: Ben&ouml;tigt Unterst&uuml;tzung durch Kreuzer/Schlachtschiffe/Tr&auml;ger, sonst ist die Einheit geschw&auml;cht.
		<br>1 Kreuzer unterst&uuml;tzt 12 Jagdboote.
		<br>1 Schlachtschiff unterst&uuml;tzt 29 Jagdboote.
		<br>1 Tr&auml;ger unterst&uuml;tzt 48 Jagdboote.';
		break;		
}


//besonderheit Kreuzer
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang[besonderheitkreuzer]='Besonderheit: Ben&ouml;tigt pro Einheit Geleitschutz durch 8 J&auml;ger, sonst ist die Einheit geschw&auml;cht.';
		break;
	case 2:
		$production_lang[besonderheitkreuzer]='Besonderheit: Ben&ouml;tigt pro Einheit Geleitschutz durch 8 J&auml;ger, sonst ist die Einheit geschw&auml;cht.';
		break;	
	case 3:
		$production_lang[besonderheitkreuzer]='Besonderheit: Ben&ouml;tigt pro Einheit Geleitschutz durch 7 J&auml;ger, sonst ist die Einheit geschw&auml;cht.';
		break;		
	case 4:
		$production_lang[besonderheitkreuzer]='Besonderheit: Ben&ouml;tigt pro Einheit Geleitschutz durch 12 J&auml;ger, sonst ist die Einheit geschw&auml;cht.';
		break;		
}

//besonderheit Schlachtschiff
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang[besonderheitschlachtschiff]='Besonderheit: 5 Zerst&ouml;rer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Ben&ouml;tigt pro Einheit 18 J&auml;ger als Geleitschutz , sonst ist die Einheit geschw&auml;cht.<br>Recycelt zerst&ouml;rte eigene J&auml;ger, Jagdboote und Bomber.';		
		break;
	case 2:
		$production_lang[besonderheitschlachtschiff]='Besonderheit: 5 Zerst&ouml;rer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Ben&ouml;tigt pro Einheit 18 J&auml;ger als Geleitschutz , sonst ist die Einheit geschw&auml;cht.<br>Recycelt zerst&ouml;rte eigene J&auml;ger, Jagdboote und Bomber.';		
		break;	
	case 3:
		$production_lang[besonderheitschlachtschiff]='Besonderheit: 5 Zerst&ouml;rer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Ben&ouml;tigt pro Einheit 14 J&auml;ger als Geleitschutz , sonst ist die Einheit geschw&auml;cht.<br>Recycelt zerst&ouml;rte eigene J&auml;ger, Jagdboote und Bomber.';		
		break;		
	case 4:
		$production_lang[besonderheitschlachtschiff]='Besonderheit: 5 Zerst&ouml;rer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Ben&ouml;tigt pro Einheit 30 J&auml;ger als Geleitschutz , sonst ist die Einheit geschw&auml;cht.<br>Recycelt zerst&ouml;rte eigene J&auml;ger, Jagdboote und Bomber.';		
		break;		
}

$production_lang[design]='Design';
$production_lang[einheit]='Einheit';
$production_lang[fehlendesgebaeude]='Fehlendes Geb&auml;ude';
$production_lang[gebaeudeinfo]='Du ben&ouml;tigst folgendes Geb&auml;ude, welches Du links unter dem Men&uuml;punkt Technologien->Geb&auml;ude bauen kannst';
$production_lang[fehler]='Es ist ein Fehler aufgetreten.';
$production_lang[information]='Information';
$production_lang[kapazitaet]='Kapazit&auml;t';
$production_lang[kapazitaet1]='Transportkapazit&auml;t';
$production_lang[kapazitaet2]='ben&ouml;tigte Transportkapazit&auml;t';
$production_lang[klasse]='Klasse';
$production_lang[klassennamen]=array('J&auml;ger','Jagdboot','Zerst&ouml;rer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Tr&auml;ger',
'Orbitalj&auml;ger-Basis','Flugk&ouml;rper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');
$production_lang[klasseziel1]='Prim&auml;rziel';
$production_lang[klasseziel2]='Sekund&auml;rziel';
$production_lang[kostenfuerfleet]='Kosten f&uuml;r deine ausgew&auml;hlten Schiffseinheiten:';
$production_lang[produktion]='Produktion';
$production_lang[punkte]='Punkte';
$production_lang[reisezeit]='Reisezeit';
$production_lang[releaselock]='Datensatz Nr. ';
$production_lang[releaselock2]=' konnte nicht entsperrt werden!';
$production_lang[releaselock3]='Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.';
$production_lang[sabotage_aktiv]='Durch eine Sabotageaktion ist keine Produktion m&ouml;glich. Mehr Informationen sind im Geheimdienst abrufbar.';
$production_lang[stueck]='St&uuml;ck';
$production_lang[summe]='Summe';
$production_lang[vorbedingung]='Vorbedingung ist nicht erf&uuml;llt.';
$production_lang[waffengattung1]='Konventionelle Waffen';
$production_lang[waffengattung2]='EMP-Waffen';
$production_lang[waffenvorhandenja]='ja';
$production_lang[waffenvorhandennein]='nein';
$production_lang[wochen]='WT';
?>
