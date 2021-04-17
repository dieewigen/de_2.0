<?php
$userartefact_lang[0]='Dieses Artefakt verringert die Baukosten von Schiffen. Max. 5% f&uuml;r alle Artefakte zusammen.';
$userartefact_lang[1]='Dieses Artefakt verringert die Baukosten von T&uuml;rmen. Max. 5% f&uuml;r alle Artefakte zusammen.';
$userartefact_lang[2]='Dieses Artefakt erh&ouml;ht die Chance einen Agenteneinsatz erfolgreich durchzuf&uuml;hren.';
$userartefact_lang[3]='Dieses Artefakt erh&ouml;ht die Chance einen feindlichen Agenteneinsatz abzuwehren.';
$userartefact_lang[4]='Dieses Artefakt verringert die Bau-/Ausbildungskosten im Geheimdienst. Max. 6% f&uuml;r alle Artefakte zusammen.';
$userartefact_lang[5]='Dieses Artefakt erh&ouml;ht die Feuerkraft der Schiffe und wird in einem Basisschiff verwendet.';
$userartefact_lang[6]='Dieses Artefakt erh&ouml;ht die L&auml;hmkraft der Schiffe und wird in einem Basisschiff verwendet.';
$userartefact_lang[7]='Dieses Artefakt erh&ouml;ht die Feuerkraft von T&uuml;rmen.';
$userartefact_lang[8]='Dieses Artefakt erh&ouml;ht die L&auml;hmkraft von T&uuml;rmen.';
if(isset($sv_oscar) && $sv_oscar==1){
	$userartefact_lang[9]='Dieses Artefakt ist auf diesem Server ohne Wirkung.';
}else{
	$userartefact_lang[9]='Dieses Artefakt erh&ouml;ht die Kraft mit der Schiffe nach Schlachten im Heimatsystem recycelt werden.';
}

/*
$userartefact_lang[10]='Dieses Artefakt erh&ouml;ht bei Benutzung die Angriffserfahrungspunkte der Flotte I, II und III um 10.000 Punkte. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[11]='Dieses Artefakt erh&ouml;ht bei Benutzung die Verteidigungserfahrungspunkte jeder Flotte um 10.000 Punkte. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
*/
$userartefact_lang[10]='Dieses Artefakt verringert die Dauer von Missionen. Max. 50% für alle Artefakte zusammen.';
$userartefact_lang[11]='Dieses Artefakt verringert die Baudauer von Gebäuden in den Vergessenen Systemen. Max. 50% für alle Artefakte zusammen.';;

$userartefact_lang[12]='Dieses Artefakt erh&ouml;ht die Leistungsf&auml;higkeit der Planetaren Schilderweiterung gegen EMP-Waffen.';
$userartefact_lang[13]='Dieses Artefakt verringert die Leistunsf&auml;higkeit der Planetaren Schilderweiterung des Ziels gegen EMP-Waffen und wird in einem Basisschiff verwendet.';

if(isset($sv_oscar) && $sv_oscar==1){
	$userartefact_lang[14]='Dieses Artefakt ist auf diesem Server ohne Wirkung.';
}else{
	$userartefact_lang[14]='Dieses Artefakt st&ouml;rt die Kraft des feindlichen Recyclotrons und wird in einem Basisschiff verwendet.';
}
$userartefact_lang[15]='Dieses Artefakt erzeugt 15 bis 20 Tronic. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[16]='Dieses Artefakt erh&ouml;ht die Artefaktgeb&auml;udestufe um 1, wobei die Maximalstufe &uuml;berschritten werden kann. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[17]='Dieses Artefakt erzeugt 1-3 Kriegsartefakte. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[18]='Dieses Artefakt erzeugt 6-8 Kollektoren, sollte man mehr als 5 Artefakte dieser Art haben, so erzeugt das Artefakt 200 Palenium. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[19]='Dieses Artefakt erzeugt 1-2 Sektorkollektoren. Nach Aktivierung des Artefaktes verliert es f&uuml;r immer seine Wirkung.';
$userartefact_lang[20]='Dieses Artefakt erzeugt einen gigantischen Hyperraumr&uuml;ssel, der dem gro&szlig;en Ishtarus 3-5 Credits aus seinem Geheimlager wegr&uuml;sselt.';
$userartefact_lang[21]='Dieses Artefakt verringert die Auktionspreise. Maximal 50% f&uuml;r alle Artefakte zusammen. Bei eigenen Auktionen kann man somit auf maximal 75% Preisnachlass kommen.';

//flottenbaukosten
$ua_index=0;
//artefaktname
$ua_name[$ua_index]='Pesara';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_color[$ua_index]='';
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.1, 1000);
$ua_werte[$ua_index][1]= array(0.2, 2000);
$ua_werte[$ua_index][2]= array(0.3, 3000);
$ua_werte[$ua_index][3]= array(0.4, 4000);
$ua_werte[$ua_index][4]= array(0.5, 5000);

//turmbaukosten
$ua_index=1;
//artefaktname
$ua_name[$ua_index]='Vakara';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//agenteneinsatz
$ua_index=2;
//artefaktname
$ua_name[$ua_index]='Geangrus';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//agentenabwehr
$ua_index=3;
//artefaktname
$ua_name[$ua_index]='Geabwus';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//Agsora, ex Nahelor
$ua_index=4;
//artefaktname
$ua_name[$ua_index]='Agsora';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.1, 1000);
$ua_werte[$ua_index][1]= array(0.3, 2000);
$ua_werte[$ua_index][2]= array(0.7, 3000);
$ua_werte[$ua_index][3]= array(1.5, 4000);
$ua_werte[$ua_index][4]= array(3.0, 5000);


//höhere feuerkraft
$ua_index=5;
//artefaktname
$ua_name[$ua_index]='Feuroka';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_bs[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.1, 1000);
$ua_werte[$ua_index][1]= array(0.3, 2000);
$ua_werte[$ua_index][2]= array(0.7, 3000);
$ua_werte[$ua_index][3]= array(1.5, 4000);
$ua_werte[$ua_index][4]= array(3.0, 5000);

//höhere blockkraft
$ua_index=6;
//artefaktname
$ua_name[$ua_index]='Bloroka';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_bs[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.1, 1000);
$ua_werte[$ua_index][1]= array(0.3, 2000);
$ua_werte[$ua_index][2]= array(0.7, 3000);
$ua_werte[$ua_index][3]= array(1.5, 4000);
$ua_werte[$ua_index][4]= array(3.0, 5000);

//turmfeuerkraft
$ua_index=7;
//artefaktname
$ua_name[$ua_index]='Turak';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//turmblockkraft
$ua_index=8;
//artefaktname
$ua_name[$ua_index]='Turla';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//verbesserung des recyclotrons
$ua_index=9;
//artefaktname
$ua_name[$ua_index]='Recarion';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);


//verkürzte Missionsdauer ehemals angriffserfahrung absolut
$ua_index=10;
//artefaktname
$ua_name[$ua_index]='Pekasch';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_useable[$ua_index]=0;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.5, 1000);
$ua_werte[$ua_index][1]= array(1.1, 2000);
$ua_werte[$ua_index][2]= array(2.3, 3000);
$ua_werte[$ua_index][3]= array(4.8, 4000);
$ua_werte[$ua_index][4]= array(10, 5000);

//verkürzter VS-Gebäudebau ehemals verteidigungserfahrung absolut
$ua_index=11;
//artefaktname
$ua_name[$ua_index]='Pekek';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_useable[$ua_index]=0;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.5, 1000);
$ua_werte[$ua_index][1]= array(1.1, 2000);
$ua_werte[$ua_index][2]= array(2.3, 3000);
$ua_werte[$ua_index][3]= array(4.8, 4000);
$ua_werte[$ua_index][4]= array(10, 5000);

//st�rke planetare schilderweiterungsst�rke
$ua_index=12;
//artefaktname
$ua_name[$ua_index]='Empala';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.10, 1000);
$ua_werte[$ua_index][1]= array(0.22, 2000);
$ua_werte[$ua_index][2]= array(0.46, 3000);
$ua_werte[$ua_index][3]= array(0.96, 4000);
$ua_werte[$ua_index][4]= array(2.00, 5000);

//schw�chere planetare schilderweiterungsst�rke
$ua_index=13;
//artefaktname
$ua_name[$ua_index]='Empdestro';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_bs[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.2, 1000);
$ua_werte[$ua_index][1]= array(0.4, 2000);
$ua_werte[$ua_index][2]= array(0.6, 3000);
$ua_werte[$ua_index][3]= array(0.8, 4000);
$ua_werte[$ua_index][4]= array(1.0, 5000);

//verschlechterung des recyclotrons
$ua_index=14;
//artefaktname
$ua_name[$ua_index]='Recadesto';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_bs[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.2, 1000);
$ua_werte[$ua_index][1]= array(0.4, 2000);
$ua_werte[$ua_index][2]= array(0.6, 3000);
$ua_werte[$ua_index][3]= array(0.8, 4000);
$ua_werte[$ua_index][4]= array(1.0, 5000);

//artefakt gibt x tronic
$ua_index=15;
//artefaktname
$ua_name[$ua_index]='Tronicar';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//artefakt erh�ht geb�udestufe um 1
$ua_index=16;
//artefaktname
$ua_name[$ua_index]='Artascendus';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//gibt x kriegsartefakte
$ua_index=17;
//artefaktname
$ua_name[$ua_index]='Waringa';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//gibt x kollektoren
$ua_index=18;
//artefaktname
$ua_name[$ua_index]='Kollimania';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//gibt 1-2 sektorkollektoren
$ua_index=19;
//artefaktname
$ua_name[$ua_index]='Sekkollus';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//anderem spieler einen kollektor nehmen und daraus f�r sich selbst ein kriegsartefakt machen
$ua_index=20;
//artefaktname
$ua_name[$ua_index]='Creditr&uuml;ssel';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=1;
$ua_useable[$ua_index]=1;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(-1, 1000);

//Auktion starten
$ua_index=21;
//artefaktname
$ua_name[$ua_index]='Auctacon';
$ua_desc[$ua_index]=$userartefact_lang[$ua_index];
$ua_maxlvl[$ua_index]=5;
$ua_useable[$ua_index]=0;
//2. index ist der level des artefakts (1. wert ist der multiplikator, 2. wert ist der grundpreis)
$ua_werte[$ua_index][0]= array(0.5, 1000);
$ua_werte[$ua_index][1]= array(1.1, 2000);
$ua_werte[$ua_index][2]= array(2.3, 3000);
$ua_werte[$ua_index][3]= array(4.8, 4000);
$ua_werte[$ua_index][4]= array(10, 5000);
?>