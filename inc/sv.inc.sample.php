<?php
//hier befinden sich alle globalen servervariablen
//format: $sv_description=wert;

//datenbankdefinitionen
$sv_database_de="de2bugtest";
$sv_database_sou="sou_server_main";

//server auf dem die grafiken liegen
$sv_image_server='';
//$sv_image_server_list[]='http://grafik-de.bgam.es/degp3v2/';
$sv_image_server_list[]='https://www.die-ewigen.com/degp3v2/';
//gewinnpunktzahl
$sv_winscore=33333;
//$sv_winscore=15000;

//ewige runde?
$sv_ewige_runde=0;

//hardcore
$sv_hardcore=0;
$sv_hardcore_need_wins=5;

//EH-Counter
$sv_eh_counter=960;

//100%-Schiffs/Turmrecycling-System?
$sv_oscar=1;

//zu haltende ticks
$sv_benticks=2;

//maximalzahl der spieler
$sv_maxuser=10000;

//maximalzahl der sektoren
$sv_maxsector=20;

//anzahl der spieler pro sektor
$sv_maxsystem=10;

//l�schzeit bei inaktivit�t in tagen
$sv_inactiv_deldays=7;

//l�schzeit bei nicht erfolgter accountaktivierung
$sv_not_activated_deldays=2;

//l�schzeit der hf
$sv_hf_deldays=14;

//l�schzeit der nachrichten
$sv_nachrichten_deldays=7;

//maximalanzahl der efta-bewegungspunkte
$sv_max_efta_bew_punkte=5000;

//angriffsgrenze in hundertsteln
$sv_attgrenze=0.40;

//angriffsgrenzenbonus durch die whg in hundertsteln
$sv_attgrenze_whg_bonus=0.0;

//sektorangriffsmalus in hundertsteln
$sv_sector_attmalus=0.20;

//maximale angriffsgrenze für kollektoren
$sv_max_col_attgrenze=0.35;

//minimale angriffsgrenze f�r kollektoren
$sv_min_col_attgrenze=0.20;

//sektoren anzeigen bis sektor
$sv_show_maxsector=1999;

//ab welchem sektor sind die npc-accounts
$sv_npc_minsector=1500;

//bis wohin gehen die npc-sectoren
$sv_npc_maxsector=2000;

//anzahl der sektoren die vorne nicht von spielern belegt werden
$sv_free_startsectors=1;

//bei wieviel prozent leute beim rausvoten verschwinden
$sv_voteoutgrenze=40;

//maximal anzahl von sektorumz�gen
$sv_max_secmoves=0;

//minimumanzahl beim reggen eines privatsektors
$sv_min_user_per_regsector=4;

//maximumanzahl beim reggen eines privatsektors
$sv_max_user_per_regsector=6;

//ab wo werden die regsektoren eingebaut
$sv_min_regsec=501;

//server tag, z.b. nde sde usw.
$sv_server_tag='SDE';

$sv_server_name='Andromeda';

//bonus vom planetaren schild auf die hp der t�rme, wert in %
$sv_ps_bonus=10;

//recyclotronertrag mit und ohne whg in prozent
$sv_recyclotron_bonus=15;
$sv_recyclotron_bonus_whg=30;

//server id
$sv_servid=2;

//anzahl von rassen, schiffen/turmtypen
$sv_anz_schiffe=10;
$sv_anz_tuerme=5;
$sv_anz_rassen=5;

//Max Anzahl dir HFN's  im Archiv | Eintr�ge in der Buddy/Ignoreliste
$sv_hf_buddie = 10;
$sv_hf_ignore = 10;
$sv_hf_archiv = 10;

//s.o. im bezug auf premium accounts!
$sv_hf_buddie_p = 20;
$sv_hf_ignore_p = 20;
$sv_hf_archiv_p = 20;

//klaubare kollies
$sv_kollie_klaurate=0.15;

//energieertrag der kollektoren mit und ohne pa
$sv_kollieertrag=100;
$sv_kollieertrag_pa=105;

//planetarer grundertrag, mit und ohne gilde
//$sv_plan_grundertrag = array (1000, 100, 0, 0);
//$sv_plan_grundertrag_whg = array (4000, 500, 100, 50);
$sv_plan_grundertrag = array (1000, 125, 75, 50);
$sv_plan_grundertrag_whg = array (4000, 500, 200, 100);



//wahrscheinlichkeit, dass tronic verteilt wird pro tick
$sv_globalw_tronic=15;

//wahrscheinlichkeit, dass Zuf�lle verteilt werden pro tick
$sv_globalw_zufall=15;

//Anzahl der zu spielenden Ticks bevor die Zufallsereignisse beginnen
$sv_global_start_zufall=2000;

//Mod-Ids f�rs SK forum
$mods = array(1);

//lebenszeit der session in sekunden
$sv_session_lifetime=3700;
//$sv_session_lifetime=20;

//zeit f�r den aktivit�tsbonus in sekunden
$sv_activetime=3600;

//maximalzahl f�r diplomatieartefakte
$sv_max_dartefakt=3;

//preise für den schwarzmarkt
$sv_sm_preisliste = array (50, 8, 10, 300, 175, 20, 5);

//max palenium
$sv_max_palenium=100;

//id f�r das pcs
$sv_pcs_id=11;

//das siegel von basranur: nach x ticks starten, ticklaufzeit, maxprozent
$sv_siegel1 = array (480, 4800, 0.03);

//serversprache
$sv_server_lang=1;

//punkte für kriegsartefakte
$sv_kartefakt_exp_atter=5000;
$sv_kartefakt_exp_deffer=4500;

//maximalzahl der credits, die man durch aktivit�t erhalten kann
$sv_credits_max_collect=1000000;

//zeitspanne zwischen den sektorvotes
$sv_sector_votetime_lock=3360;

//bis zu wievielen kollektoren hin kann man npc-accounts angreifen
$sv_npcatt_col_grenze=400;

//efta-kollektor-malus
$sv_efta_col_malus=5;

//planetare schilderweiterung: min./standard/max.
$sv_planetshieldext=array(0,0.05,1);

//max. reclyctronbonus nach sektorverteilung
$sv_recyclotron_sector_bonus=40;

//maximal m�glicher recyclotronbetrag
$sv_recyclotron_max=80;

//kleinster recyclotronwert
$sv_recyclotron_min=15;

//energieertrag durch kriegsartefakte
if($sv_ewige_runde==1){
	$sv_kriegsartefaktertrag=200;
}else{
	$sv_kriegsartefaktertrag=100;
}

//energieertrag durch Z�llner
if($sv_ewige_runde==1){
	$sv_zoellnerertrag=array(0.0125*2, 0.00625*2, 0.00417*2, 0.003125*2);
}else{
	$sv_zoellnerertrag=array(0.0125, 0.00625, 0.00417, 0.003125);
}

//energieertrag durch eftaartefakte
$sv_eftaartefaktertrag=10;

//wieviel man von dem kopfgeld bekommt
$sv_bounty_rate=0.10;

//ist es ein bezahlserver
$sv_payserver=0;

//ist efta in de integriert
$sv_efta_in_de=1;

//ist sou in de integriert
$sv_sou_in_de=1;

//flags zur deaktivierung einzelner spielelemente
$sv_deactivate_efta=0;
$sv_deactivate_sou=0;
$sv_deactivate_trade=0;
$sv_deactivate_sec1moveout=0;
$sv_deactivate_kiatt=1;

//creditgewinne
//punkte
$sv_credit_win[0][0]=75;
$sv_credit_win[0][1]=25;
$sv_credit_win[0][2]=10;
//executorpunkte
$sv_credit_win[1][0]=75;
$sv_credit_win[1][1]=25;
$sv_credit_win[1][2]=10;
//kopfgeldjäger
$sv_credit_win[2][0]=0;
$sv_credit_win[2][1]=0;
$sv_credit_win[2][2]=0;
//eftapunkte
$sv_credit_win[3][0]=0;
$sv_credit_win[3][1]=0;
$sv_credit_win[3][2]=0;
//erhabener
$sv_credit_win[4][0]=200;


//community server
$sv_comserver=0;

//autoreset auf dem server?
$sv_auto_reset=0;

//Flottenpunkte im Sektorstatus verstecken
$sv_hide_fp_in_secstatus=0;

$sv_debug=1;

//V-Systeme deaktivieren
//$sv_deactivate_vsystems=1;

$sv_max_alien_col=400;
$sv_max_alien_col_typ=1;

//neue DE-Version
$GLOBALS['sv_ang']=1;

//Spieler beim Autoreset in Sektor 1 verschieben?
$GLOBALS['sv_autostart_move_player_to_sector_1']=0;

$GLOBALS['tech_build_time_faktor']=1;
//$GLOBALS['tech_build_time_faktor']=0.05;
//$GLOBALS['tech_build_time_faktor']=0.001;

$GLOBALS['sv_show_ally_secstatus']=3600;

$GLOBALS['wts'][0]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][1]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][2]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][3]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][4]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][5]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][6]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][7]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][8]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][9]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][10]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][11]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][12]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][13]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][14]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][15]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][16]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][17]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][18]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][19]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][20]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][21]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][22]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);
$GLOBALS['wts'][23]=array(0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57);

$GLOBALS['kts'][0]=array();
$GLOBALS['kts'][1]=array(0);
$GLOBALS['kts'][2]=array();
$GLOBALS['kts'][3]=array(0);
$GLOBALS['kts'][4]=array();
$GLOBALS['kts'][5]=array(0);
$GLOBALS['kts'][6]=array();
$GLOBALS['kts'][7]=array(0);
$GLOBALS['kts'][8]=array(0,20,40);
$GLOBALS['kts'][9]=array(0,20,40);
$GLOBALS['kts'][10]=array(0,20,40);
$GLOBALS['kts'][11]=array(0,20,40);
$GLOBALS['kts'][12]=array(0,20,40);
$GLOBALS['kts'][13]=array(0,20,40);
$GLOBALS['kts'][14]=array(0,20,40);
$GLOBALS['kts'][15]=array(0,20,40);
$GLOBALS['kts'][16]=array(0,20,40);
$GLOBALS['kts'][17]=array(0,20,40);
$GLOBALS['kts'][18]=array(0,20,40);
$GLOBALS['kts'][19]=array(0,20,40);
$GLOBALS['kts'][20]=array(0,20,40,14);
$GLOBALS['kts'][21]=array(0,20,40);
$GLOBALS['kts'][22]=array(0,20,40);
$GLOBALS['kts'][23]=array(0);

?>