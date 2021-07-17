<?php
$military_lang['aktbefehl']='Aktuelle Befehle';
$military_lang['aktion']='Aktion';
$military_lang['aktion2']='alle Schiffe';
$military_lang['alle']='Alle';
$military_lang['allgfehler']='Allgemeiner Fehler.';
$military_lang['allgfehler2']='Nur die Aufstellung von Flotten, die sich im Heimatsystem befinden, können geändert werden. überprüfen Sie bitte noch einmal Ihre Befehle.';
$military_lang['angriffsforma']='Angriffsformation';
$military_lang['attunwuerdig']='Diese geplante Kampfhandlung ist unehrenhaft und somit Deiner nicht würdig!';
$military_lang['basisschiffe']='Basisschiffe';
$military_lang['befehl']='Befehl';
$military_lang['befehl1']='Aktuelle Befehle beibehalten';
$military_lang['befehl2']='Heimkehr';
$military_lang['befehl3']='Aktuelle Befehle beibehalten';
$military_lang['befehl4']='Angreifen';
$military_lang['befehl5']='Verteidige 1 KT';
$military_lang['befehl6']='Verteidige 2 KTs';
$military_lang['befehl7']='Verteidige 3 KTs';
$military_lang['befehlefehlerhaft']='Die Flottenbefehle sind fehlerhaft.';
$military_lang['besonderheittransmitterschiff']='Besonderheit: Diese Einheit ist in der Lage Kollektoren zu erobern, wird dabei aber vernichtet.';

//besonderheit Jagdboote
switch($_SESSION['ums_rasse']){
	case 1:
		$military_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 10 Jagdboote.
		<br>1 Schlachtschiff unterstützt 22 Jagdboote.
		<br>1 Träger unterstützt 34 Jagdboote.';
		break;
	case 2:
		$military_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 12 Jagdboote.
		<br>1 Schlachtschiff unterstützt 28 Jagdboote.
		<br>1 Träger unterstützt 43 Jagdboote.';
		break;	
	case 3:
		$military_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 6 Jagdboote.
		<br>1 Schlachtschiff unterstützt 14 Jagdboote.
		<br>1 Träger unterstützt 26 Jagdboote.';
		break;		
	case 4:
		$military_lang['besonderheitjagdboot']='Besonderheit: Benötigt Unterstützung durch Kreuzer/Schlachtschiffe/Träger, sonst ist die Einheit geschwächt.
		<br>1 Kreuzer unterstützt 12 Jagdboote.
		<br>1 Schlachtschiff unterstützt 29 Jagdboote.
		<br>1 Träger unterstützt 48 Jagdboote.';
		break;		
}


//besonderheit Kreuzer
switch($_SESSION['ums_rasse']){
	case 1:
		$military_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit Geleitschutz durch 8 Jäger, sonst ist die Einheit geschwächt.';
		break;
	case 2:
		$military_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit  Geleitschutz durch 8 Jäger, sonst ist die Einheit geschwächt.';
		break;	
	case 3:
		$military_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit  Geleitschutz durch 7 Jäger, sonst ist die Einheit geschwächt.';
		break;		
	case 4:
		$military_lang['besonderheitkreuzer']='Besonderheit: Benötigt pro Einheit  Geleitschutz durch 12 Jäger, sonst ist die Einheit geschwächt.';
		break;		
}

//besonderheit Schlachtschiff
switch($_SESSION['ums_rasse']){
	case 1:
		$military_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 18 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;
	case 2:
		$military_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 18 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;	
	case 3:
		$military_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 14 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;		
	case 4:
		$military_lang['besonderheitschlachtschiff']='Besonderheit: 5 Zerstörer und 3 Kreuzer pro Schlachtschiff beschleunigen dieses um 1 KT.<br>Benötigt pro Einheit 30 Jäger als Geleitschutz , sonst ist die Einheit geschwächt.<br>Recycelt zerstörte eigene Jäger, Jagdboote und Bomber.';		
		break;		
}



$military_lang['dererhabene']='Der Erhabene';
$military_lang['dobefehl']='Befehle erteilen';
$military_lang['eineflotteziehtsichzurueck']='Eine Flotte zieht sich zurück.';
$military_lang['error']='Zu einer Mission darf nur das Basisschiff aufbrechen.';
$military_lang['error10']='Es befinden sich keine Schiffe in der Flotte.';
$military_lang['error11']='Man kann Flotten nur im Heimatsystem Einsatzbefehle erteilen.';
$military_lang['error2']='Entferne bitte die Einheiten aus der Flotte.';
$military_lang['error3']='Man kann Flotten nur im Heimatsystem Einsatzbefehle erteilen.';
$military_lang['fernesek']='Ferne Sektoren';
$military_lang['feindlich']='feindlich';
$military_lang['feindliche']='feindliche';
$military_lang['fleetaufstellung']='Flottenaufstellung';
$military_lang['fleetrausack']='Das Basisschiff ist zu einer Mission aufgebrochen.';
$military_lang['fleetrausnack']='Missionen sind nur in Systeme der DX61a23 möglich.';
$military_lang['fleetumgestellt']='Die Flottenumstellung wurde durchgeführt.';
$military_lang['flotte']='Flotte';
$military_lang['flotte1']='Flotte I';
$military_lang['flotte2']='Flotte II';
$military_lang['flotte3']='Flotte III';
$military_lang['flottenbefehle']='Flottenbefehle erteilen';
$military_lang['flottengesinnung']='Flottengesinnung';
$military_lang['flottenumstellen']='Flotten umstellen';
$military_lang['flottenpunktewert']='Flottenpunktewert';
$military_lang['fehlendesgebaeude']='Fehlendes Gebäude';
$military_lang['gebaeudeinfo']='Du benötigst folgendes Gebäude, welches Du links unter dem Menüpunkt Technologien->Gebäude bauen kannst';
$military_lang['heimatflotte']='Heimatflotte';
$military_lang['hflotte']='HFlotte';
$military_lang['kapazitaet']='Transportkapazität';
$military_lang['kapazitaet2']='benötigte Transportkapazität';
$military_lang['klasse']='Klasse';
$military_lang['klassennamen']=array('Jäger','Jagdboot','Zerstörer','Kreuzer','Schlachtschiff','Bomber','Transmitterschiff','Träger',
'Orbitaljäger-Basis','Flugkörper-Plattform','Energiegeschoss-Plattform','Materiegeschoss-Plattform','Hochenergiegeschoss-Plattform');
$military_lang['klasseziel1']='Primärziel';
$military_lang['klasseziel2']='Sekundärziel';
$military_lang['nahesek']='Nahe Sektoren';
$military_lang['notranseninfo']='Achtung: Der Angriff erfolgt ohne passende Schiffe um Kollektoren zu erbeuten.';
$military_lang['npcattinfo']='Durch diesen Angriff wird der Nichtangriffspakt mit den DX61a23 verletzt. Diese Aktion erfolgt auf eigene Gefahr, denn es werden mehr Archäologen zur Erforschung der Missionen benötigt.';
$military_lang['punkte']='Punkte';
$military_lang['reisezeit']='Reisezeit';
$military_lang['releaselock']='Datensatz Nr. ';
$military_lang['releaselock2']=' konnte nicht entsperrt werden!';
$military_lang['request']='Es ist ein Fehler aufgetreten.';
$military_lang['setlock']='Es ist zur Zeit bereits eine Transaktion aktiv. Bitte warten Sie, bis die Transaktion abgeschlossen ist.';
$military_lang['schiff']='Schiff';
$military_lang['schiffe']='Schiffe';
$military_lang['schiffen']='Schiffen';
$military_lang['sendmsg1']='Die Scanner haben eine ';
$military_lang['sendmsg2']='Flotte von';
$military_lang['sendmsg3']='entdeckt. Ursprung';
$military_lang['status']='Systemverteidigung';
$military_lang['status2']='Angriff';
$military_lang['status3']='Verteidigung';
$military_lang['status4']='Rückflug';
$military_lang['status5']='Mission';
$military_lang['status6']='Verteidige';
$military_lang['sysreisezeit']='Systemreisezeit';
$military_lang['traegerkapa']='Trägerkapazität';
$military_lang['ursprung']='Ursprung';
$military_lang['verbuendet']='verbündet';
$military_lang['verbuendete']='verbündete';
$military_lang['verteidiforma']='Verteidigungsformation';
$military_lang['waffengattung1']='Konventionelle Waffen';
$military_lang['waffengattung2']='EMP-Waffen';
$military_lang['waffenvorhandenja']='ja';
$military_lang['waffenvorhandennein']='nein';
$military_lang['zeit']='Zeit';
$military_lang['zielkoords']='Zielkoordinaten';
$military_lang['zuheimatflotte']='zu Heimatflotte';
?>
