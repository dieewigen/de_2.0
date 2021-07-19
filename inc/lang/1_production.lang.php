<?php
$production_lang['aktiveauftraege']='Aktive Bauaufträge';
$production_lang['artefakte']='-Artefakte: ';
$production_lang['bauen']='Bauen';
$production_lang['baukostenreduz']='Baukostenreduzierung durch ';
$production_lang['beschreibung']='Durch die Erforschung neuer Technologien ist es möglich, bessere Schiffe herzustellen. Zur Orientierung dient der Technologiebaum, den man hier finden kann.';
$production_lang['besonderheittransmitterschiff']='Besonderheit: Diese Einheit ist in der Lage Kollektoren zu erobern, wird dabei aber vernichtet.';
//besonderheit Jagdboote
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 10 Jagdboote.
		<br>1 Schlachtschiff unterstützt 22 Jagdboote.
		<br>1 Träger unterstützt 34 Jagdboote.';
		break;
	case 2:
		$production_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 12 Jagdboote.
		<br>1 Schlachtschiff unterstützt 28 Jagdboote.
		<br>1 Träger unterstützt 43 Jagdboote.';
		break;	
	case 3:
		$production_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 6 Jagdboote.
		<br>1 Schlachtschiff unterstützt 14 Jagdboote.
		<br>1 Träger unterstützt 26 Jagdboote.';
		break;		
	case 4:
		$production_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 12 Jagdboote.
		<br>1 Schlachtschiff unterstützt 29 Jagdboote.
		<br>1 Träger unterstützt 48 Jagdboote.';
		break;		
}


//besonderheit Kreuzer
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit Geleitschutz durch 8 Jäger, sonst ist die Einheit geschwächt.';
		break;
	case 2:
		$production_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit Geleitschutz durch 8 Jäger, sonst ist die Einheit geschwächt.';
		break;	
	case 3:
		$production_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit Geleitschutz durch 7 Jäger, sonst ist die Einheit geschwächt.';
		break;		
	case 4:
		$production_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit Geleitschutz durch 12 Jäger, sonst ist die Einheit geschwächt.';
		break;		
}

//besonderheit Schlachtschiff
switch($_SESSION['ums_rasse']){
	case 1:
		$production_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 18 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;
	case 2:
		$production_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 18 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;	
	case 3:
		$production_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 14 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;		
	case 4:
		$production_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 30 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;		
}

$production_lang['design']='Design';
$production_lang['einheit']='Einheit';
$production_lang['fehlendesgebaeude']='Fehlendes Gebäude';
$production_lang['gebaeudeinfo']='Du benötigst folgendes Gebäude, welches Du links unter dem Menüpunkt Technologien->Gebäude bauen kannst';
$production_lang['fehler']='Es ist ein Fehler aufgetreten.';
$production_lang['information']='Information';
$production_lang['kapazitaet']='Kapazität';
$production_lang['kapazitaet1']='Transportkapazität';
$production_lang['kapazitaet2']='benötigte Transportkapazität';
$production_lang['klasse']='Klasse';
$production_lang['klassennamen']=array('Jäger','Jagdboot','Zerstörer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Träger',
'Orbitaljäger-Basis','Flugkörper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');
$production_lang['klasseziel1']='Primärziel';
$production_lang['klasseziel2']='Sekundärziel';
$production_lang['kostenfuerfleet']='Kosten für deine ausgewählten Schiffseinheiten:';
$production_lang['produktion']='Produktion';
$production_lang['punkte']='Punkte';
$production_lang['reisezeit']='Reisezeit';
$production_lang['releaselock']='Datensatz Nr. ';
$production_lang['releaselock2']=' konnte nicht entsperrt werden!';
$production_lang['releaselock3']='Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.';
$production_lang['sabotage_aktiv']='Durch eine Sabotageaktion ist keine Produktion möglich. Mehr Informationen sind im Geheimdienst abrufbar.';
$production_lang['stueck']='Stück';
$production_lang['summe']='Summe';
$production_lang['vorbedingung']='Vorbedingung ist nicht erfüllt.';
$production_lang['waffengattung1']='Konventionelle Waffen';
$production_lang['waffengattung2']='EMP-Waffen';
$production_lang['waffenvorhandenja']='ja';
$production_lang['waffenvorhandennein']='nein';
$production_lang['wochen']='WT';
?>
