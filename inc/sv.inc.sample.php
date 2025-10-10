<?php
//hier befinden sich alle globalen servervariablen
//format: $GLOBALS['sv_description']=wert;

//gewinnpunktzahl
$GLOBALS['sv_winscore']=33333;

//ewige runde?
$GLOBALS['sv_ewige_runde']=0;

//hardcore
$GLOBALS['sv_hardcore']=0;
$GLOBALS['sv_hardcore_need_wins']=5;

//EH-Counter
$GLOBALS['sv_eh_counter']=960;

//100%-Schiffs/Turmrecycling-System?
$GLOBALS['sv_oscar']=0;

//zu haltende ticks
$GLOBALS['sv_benticks']=2;

//maximalzahl der sektoren
$GLOBALS['sv_maxsector']=20;

//anzahl der spieler pro sektor
$GLOBALS['sv_maxsystem']=10;

//l�schzeit bei inaktivit�t in tagen
$GLOBALS['sv_inactiv_deldays']=7;

//l�schzeit bei nicht erfolgter accountaktivierung
$GLOBALS['sv_not_activated_deldays']=2;

//l�schzeit der hf
$GLOBALS['sv_hf_deldays']=14;

//l�schzeit der nachrichten
$GLOBALS['sv_nachrichten_deldays']=7;

//maximalanzahl der efta-bewegungspunkte
$GLOBALS['sv_max_efta_bew_punkte']=5000;

//angriffsgrenze in hundertsteln
$GLOBALS['sv_attgrenze']=0.40;

//angriffsgrenzenbonus durch die whg in hundertsteln
$GLOBALS['sv_attgrenze_whg_bonus']=0.0;

//sektorangriffsmalus in hundertsteln
$GLOBALS['sv_sector_attmalus']=0.20;

//maximale angriffsgrenze für Kollektoren
$GLOBALS['sv_max_col_attgrenze']=0.35;

//minimale angriffsgrenze für Kollektoren
$GLOBALS['sv_min_col_attgrenze']=0.20;

//sektoren anzeigen bis sektor
$GLOBALS['sv_show_maxsector']=1999;

//ab welchem sektor sind die npc-accounts
$GLOBALS['sv_npc_minsector']=1500;

//bis wohin gehen die npc-sectoren
$GLOBALS['sv_npc_maxsector']=2000;

//anzahl der sektoren die vorne nicht von spielern belegt werden
$GLOBALS['sv_free_startsectors']=1;

//bei wieviel prozent leute beim rausvoten verschwinden
$GLOBALS['sv_voteoutgrenze']=40;

//maximal anzahl von sektorumzügen
$GLOBALS['sv_max_secmoves']=0;

//minimumanzahl beim reggen eines privatsektors
$GLOBALS['sv_min_user_per_regsector']=4;

//maximumanzahl beim reggen eines privatsektors
$GLOBALS['sv_max_user_per_regsector']=6;

//ab wo werden die Sphärensektoren eingebaut
$GLOBALS['sv_min_regsec']=501;

//server tag, z.b. nde sde usw.
$GLOBALS['sv_server_tag']='SDE';

$GLOBALS['sv_server_name']='Andromeda';

//bonus vom planetaren schild auf die hp der t�rme, wert in %
$GLOBALS['sv_ps_bonus']=10;

//recyclotronertrag mit und ohne whg in prozent
$GLOBALS['sv_recyclotron_bonus']=15;
$GLOBALS['sv_recyclotron_bonus_whg']=30;

//server id
$GLOBALS['sv_servid']=2;

//anzahl von rassen, schiffen/turmtypen
$GLOBALS['sv_anz_schiffe']=10;
$GLOBALS['sv_anz_tuerme']=5;
$GLOBALS['sv_anz_rassen']=5;

//klaubare kollies
$GLOBALS['sv_kollie_klaurate']=0.15;

//energieertrag der kollektoren mit und ohne pa
$GLOBALS['sv_kollieertrag']=100;
$GLOBALS['sv_kollieertrag_pa']=105;

//planetarer grundertrag, mit und ohne gilde
$GLOBALS['sv_plan_grundertrag'] = array (1000, 125, 75, 50);
$GLOBALS['sv_plan_grundertrag_whg'] = array (4000, 500, 200, 100);



//wahrscheinlichkeit, dass tronic verteilt wird pro tick
$GLOBALS['sv_globalw_tronic']=15;

//wahrscheinlichkeit, dass Zufälle verteilt werden pro tick
$GLOBALS['sv_globalw_zufall']=15;

//Anzahl der zu spielenden Ticks bevor die Zufallsereignisse beginnen
$GLOBALS['sv_global_start_zufall']=2000;

//lebenszeit der session in sekunden
$GLOBALS['sv_session_lifetime']=3700;
//$GLOBALS['sv_session_lifetime']=20;

//zeit f�r den aktivit�tsbonus in sekunden
$GLOBALS['sv_activetime']=3600;

//id für das pcs
$GLOBALS['sv_pcs_id']=11;

//das siegel von basranur: nach x ticks starten, ticklaufzeit, maxprozent
$GLOBALS['sv_siegel1'] = array (480, 4800, 0.03);

//serversprache
$GLOBALS['sv_server_lang']=1;

//punkte für kriegsartefakte
$GLOBALS['sv_kartefakt_exp_atter']=5000;
$GLOBALS['sv_kartefakt_exp_deffer']=4500;

//maximalzahl der credits, die man durch aktivit�t erhalten kann
$GLOBALS['sv_credits_max_collect']=1000000;

//zeitspanne zwischen den sektorvotes
$GLOBALS['sv_sector_votetime_lock']=3360;

//bis zu wievielen kollektoren hin kann man npc-accounts angreifen
$GLOBALS['sv_npcatt_col_grenze']=400;

//efta-kollektor-malus
$GLOBALS['sv_efta_col_malus']=5;

//planetare schilderweiterung: min./standard/max.
$GLOBALS['sv_planetshieldext']=array(0,0.05,1);

//max. reclyctronbonus nach sektorverteilung
$GLOBALS['sv_recyclotron_sector_bonus']=40;

//maximal m�glicher recyclotronbetrag
$GLOBALS['sv_recyclotron_max']=80;

//kleinster recyclotronwert
$GLOBALS['sv_recyclotron_min']=15;

//energieertrag durch kriegsartefakte
if($GLOBALS['sv_ewige_runde']==1){
	$GLOBALS['sv_kriegsartefaktertrag']=200;
}else{
	$GLOBALS['sv_kriegsartefaktertrag']=100;
}

//energieertrag durch Z�llner
if($GLOBALS['sv_ewige_runde']==1){
	$GLOBALS['sv_zoellnerertrag']=array(0.0125*2, 0.00625*2, 0.00417*2, 0.003125*2);
}else{
	$GLOBALS['sv_zoellnerertrag']=array(0.0125, 0.00625, 0.00417, 0.003125);
}

//wieviel man von dem kopfgeld bekommt
$GLOBALS['sv_bounty_rate']=0.10;

//autoreset auf dem server?
$GLOBALS['sv_auto_reset']=0;

//Flottenpunkte im Sektorstatus verstecken
$GLOBALS['sv_hide_fp_in_secstatus']=0;

$GLOBALS['sv_debug']=1;

//V-Systeme deaktivieren
$GLOBALS['sv_deactivate_vsystems']=0;

$GLOBALS['sv_max_alien_col']=400;
$GLOBALS['sv_max_alien_col_typ']=1;

//neue DE-Version
$GLOBALS['sv_ang']=1;

//Spieler beim Autoreset in Sektor 1 verschieben?
$GLOBALS['sv_autostart_move_player_to_sector_1']=0;

//Spieleraktionen mitloggen?
$GLOBALS['sv_log_player_actions']=0;

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