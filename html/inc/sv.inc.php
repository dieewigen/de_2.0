<?php
//Hier befinden sich alle globalen servervariablen. Die Defaultwerte gelten, wenn
//die Umgebungsvariablen nicht gesetzt sind; sie heißen wie der PHP-Schlüssel in
//Großbuchstaben (z. B. SV_WINSCORE) und kommen auf den Docker-Servern aus
//docker/sde/.env bzw. docker/xde/.env. Referenz: sv.inc.sample.php.
//Optionale Werte (botcheck, Siegel, Hekate/Thanatos, Feldzug, S666) werden nur
//gesetzt, wenn die Umgebungsvariable gesetzt ist, sonst gelten die
//Standardwerte im Spielcode (??-Fallback).

require_once __DIR__ . '/../src/Config/EnvLoader.php';

use DieEwigen\DE2\Config\EnvLoader;

//gewinnpunktzahl
$GLOBALS['sv_winscore'] = EnvLoader::int('SV_WINSCORE', 33333);

//ewige runde?
$GLOBALS['sv_ewige_runde'] = EnvLoader::int('SV_EWIGE_RUNDE', 0);

//hardcore
$GLOBALS['sv_hardcore'] = EnvLoader::int('SV_HARDCORE', 0);
$GLOBALS['sv_hardcore_need_wins'] = EnvLoader::int('SV_HARDCORE_NEED_WINS', 5);

//EH-Counter
$GLOBALS['sv_eh_counter'] = EnvLoader::int('SV_EH_COUNTER', 960);

//100%-Schiffs/Turmrecycling-System?
$GLOBALS['sv_oscar'] = EnvLoader::int('SV_OSCAR', 0);

//zu haltende ticks
$GLOBALS['sv_benticks'] = EnvLoader::int('SV_BENTICKS', 2);

//maximalzahl der sektoren
$GLOBALS['sv_maxsector'] = EnvLoader::int('SV_MAXSECTOR', 20);

//anzahl der spieler pro sektor
$GLOBALS['sv_maxsystem'] = EnvLoader::int('SV_MAXSYSTEM', 10);

//löschzeit bei inaktivität in tagen
$GLOBALS['sv_inactiv_deldays'] = EnvLoader::int('SV_INACTIV_DELAYS', 7);

//löschzeit bei nicht erfolgter accountaktivierung
$GLOBALS['sv_not_activated_deldays'] = EnvLoader::int('SV_NOT_ACTIVATED_DELAYS', 2);

//löschzeit der hf
$GLOBALS['sv_hf_deldays'] = EnvLoader::int('SV_HF_DELAYS', 14);

//löschzeit der nachrichten
$GLOBALS['sv_nachrichten_deldays'] = EnvLoader::int('SV_NACHRICHTEN_DELAYS', 7);

//Öffentliche Adresse dieses Spielservers ohne abschließenden Slash, z. B. https://sde.die-ewigen.com
//Wird für den Abmeldelink der Lageberichte benötigt, ohne Adresse werden keine Lageberichte verschickt.
$GLOBALS['sv_server_url'] = EnvLoader::str('SV_SERVER_URL', '');

//Lageberichte von Fluxurion an Spieler im Exil (Sektor 1)
$GLOBALS['sv_exile_report_days'] = EnvLoader::listInt('SV_EXILE_REPORT_DAYS', [3, 14, 45]); //Berichte nach x Tagen im Exil
$GLOBALS['sv_exile_newround_maxdays'] = EnvLoader::int('SV_EXILE_NEWROUND_MAXDAYS', 0); //Rundenbericht nur an Konten, die höchstens so viele Tage im Exil sind, 0 = ohne Grenze
$GLOBALS['sv_exile_mail_hours'] = EnvLoader::listInt('SV_EXILE_MAIL_HOURS', [9, 21]); //Versandfenster: ab 9:00 Uhr bis vor 21:00 Uhr
$GLOBALS['sv_exile_mails_per_hour'] = EnvLoader::int('SV_EXILE_MAILS_PER_HOUR', 10); //Drossel, höchstens eine Mail pro Wirtschaftstick

//maximalanzahl der efta-bewegungspunkte
$GLOBALS['sv_max_efta_bew_punkte'] = EnvLoader::int('SV_MAX_EFTA_BEW_PUNKTE', 5000);

//angriffsgrenze in hundertsteln
$GLOBALS['sv_attgrenze'] = EnvLoader::float('SV_ATTGRENZE', 0.40);

//angriffsgrenzenbonus durch die whg in hundertsteln
$GLOBALS['sv_attgrenze_whg_bonus'] = EnvLoader::float('SV_ATTGRENZE_WHG_BONUS', 0.0);

//sektorangriffsmalus in hundertsteln
$GLOBALS['sv_sector_attmalus'] = EnvLoader::float('SV_SECTOR_ATTMALUS', 0.20);

//maximale angriffsgrenze für Kollektoren
$GLOBALS['sv_max_col_attgrenze'] = EnvLoader::float('SV_MAX_COL_ATTGRENZE', 0.35);

//minimale angriffsgrenze für Kollektoren
$GLOBALS['sv_min_col_attgrenze'] = EnvLoader::float('SV_MIN_COL_ATTGRENZE', 0.20);

//sektoren anzeigen bis sektor
$GLOBALS['sv_show_maxsector'] = EnvLoader::int('SV_SHOW_MAXSECTOR', 1999);

//ab welchem sektor sind die npc-accounts
$GLOBALS['sv_npc_minsector'] = EnvLoader::int('SV_NPC_MINSECTOR', 1500);

//bis wohin gehen die npc-sectoren
$GLOBALS['sv_npc_maxsector'] = EnvLoader::int('SV_NPC_MAXSECTOR', 2000);

//anzahl der sektoren die vorne nicht von spielern belegt werden
$GLOBALS['sv_free_startsectors'] = EnvLoader::int('SV_FREE_STARTSECTORS', 1);

//bei wieviel prozent leute beim rausvoten verschwinden
$GLOBALS['sv_voteoutgrenze'] = EnvLoader::int('SV_VOTEOUTGRENZE', 40);

//maximal anzahl von sektorumzügen
$GLOBALS['sv_max_secmoves'] = EnvLoader::int('SV_MAX_SECMOVES', 0);

//minimumanzahl beim reggen eines privatsektors
$GLOBALS['sv_min_user_per_regsector'] = EnvLoader::int('SV_MIN_USER_PER_REGSECTOR', 4);

//maximumanzahl beim reggen eines privatsektors
$GLOBALS['sv_max_user_per_regsector'] = EnvLoader::int('SV_MAX_USER_PER_REGSECTOR', 6);

//ab wo werden die Sphärensektoren eingebaut
$GLOBALS['sv_min_regsec'] = EnvLoader::int('SV_MIN_REGSEC', 501);

//server tag, z.b. nde sde usw.
$GLOBALS['sv_server_tag'] = EnvLoader::str('SV_SERVER_TAG', 'SDE');

$GLOBALS['sv_server_name'] = EnvLoader::str('SV_SERVER_NAME', 'Andromeda');

//bonus vom planetaren schild auf die hp der türme, wert in %
$GLOBALS['sv_ps_bonus'] = EnvLoader::int('SV_PS_BONUS', 10);

//recyclotronertrag mit und ohne whg in prozent
$GLOBALS['sv_recyclotron_bonus'] = EnvLoader::int('SV_RECYCLOTRON_BONUS', 15);
$GLOBALS['sv_recyclotron_bonus_whg'] = EnvLoader::int('SV_RECYCLOTRON_BONUS_WHG', 30);

//server id
$GLOBALS['sv_servid'] = EnvLoader::int('SV_SERVID', 2);

//anzahl von rassen, schiffen/turmtypen
$GLOBALS['sv_anz_schiffe'] = EnvLoader::int('SV_ANZ_SCHIFFE', 10);
$GLOBALS['sv_anz_tuerme'] = EnvLoader::int('SV_ANZ_TUERME', 5);
$GLOBALS['sv_anz_rassen'] = EnvLoader::int('SV_ANZ_RASSEN', 5);

//klaubare kollies
$GLOBALS['sv_kollie_klaurate'] = EnvLoader::float('SV_KOLLIE_KLAURATE', 0.15);

//energieertrag der kollektoren mit und ohne pa
$GLOBALS['sv_kollieertrag'] = EnvLoader::int('SV_KOLLIEERTRAG', 100);
$GLOBALS['sv_kollieertrag_pa'] = EnvLoader::int('SV_KOLLIEERTRAG_PA', 105);

//planetarer grundertrag, mit und ohne gilde
$GLOBALS['sv_plan_grundertrag'] = EnvLoader::listInt('SV_PLAN_GRUNDERTRAG', [1000, 125, 75, 50]);
$GLOBALS['sv_plan_grundertrag_whg'] = EnvLoader::listInt('SV_PLAN_GRUNDERTRAG_WHG', [4000, 500, 200, 100]);

//wahrscheinlichkeit, dass tronic verteilt wird pro tick
$GLOBALS['sv_globalw_tronic'] = EnvLoader::int('SV_GLOBALW_TRONIC', 15);

//wahrscheinlichkeit, dass Zufälle verteilt werden pro tick
$GLOBALS['sv_globalw_zufall'] = EnvLoader::int('SV_GLOBALW_ZUFALL', 15);

//Anzahl der zu spielenden Ticks bevor die Zufallsereignisse beginnen
$GLOBALS['sv_global_start_zufall'] = EnvLoader::int('SV_GLOBAL_START_ZUFALL', 2000);

//lebenszeit der session in sekunden
$GLOBALS['sv_session_lifetime'] = EnvLoader::int('SV_SESSION_LIFETIME', 3700);

//zeit für den aktivitätsbonus in sekunden
$GLOBALS['sv_activetime'] = EnvLoader::int('SV_ACTIVETIME', 3600);

//id für das pcs
$GLOBALS['sv_pcs_id'] = EnvLoader::int('SV_PCS_ID', 11);

//serversprache
$GLOBALS['sv_server_lang'] = EnvLoader::int('SV_SERVER_LANG', 1);

//punkte für kriegsartefakte
$GLOBALS['sv_kartefakt_exp_atter'] = EnvLoader::int('SV_KARTEFAKT_EXP_ATTER', 5000);
$GLOBALS['sv_kartefakt_exp_deffer'] = EnvLoader::int('SV_KARTEFAKT_EXP_DEFFER', 4500);

//maximalzahl der credits, die man durch aktivität erhalten kann
$GLOBALS['sv_credits_max_collect'] = EnvLoader::int('SV_CREDITS_MAX_COLLECT', 1000000);

//zeitspanne zwischen den sektorvotes
$GLOBALS['sv_sector_votetime_lock'] = EnvLoader::int('SV_SECTOR_VOTETIME_LOCK', 3360);

//bis zu wievielen kollektoren hin kann man npc-accounts angreifen
$GLOBALS['sv_npcatt_col_grenze'] = EnvLoader::int('SV_NPCATT_COL_GRENZE', 400);

//efta-kollektor-malus
$GLOBALS['sv_efta_col_malus'] = EnvLoader::int('SV_EFTA_COL_MALUS', 5);

//planetare schilderweiterung: min./standard/max.
$GLOBALS['sv_planetshieldext'] = EnvLoader::listFloat('SV_PLANETSHIELDEXT', [0, 0.05, 1]);

//max. reclyctronbonus nach sektorverteilung
$GLOBALS['sv_recyclotron_sector_bonus'] = EnvLoader::int('SV_RECYCLOTRON_SECTOR_BONUS', 40);

//maximal möglicher recyclotronbetrag
$GLOBALS['sv_recyclotron_max'] = EnvLoader::int('SV_RECYCLOTRON_MAX', 80);

//kleinster recyclotronwert
$GLOBALS['sv_recyclotron_min'] = EnvLoader::int('SV_RECYCLOTRON_MIN', 15);

//energieertrag durch kriegsartefakte (verdoppelt in der ewigen Runde)
$GLOBALS['sv_kriegsartefaktertrag'] = EnvLoader::int('SV_KRIEGSARTEFAKTERTRAG', $GLOBALS['sv_ewige_runde'] == 1 ? 200 : 100);

//energieertrag durch Zöllner (verdoppelt in der ewigen Runde)
if ($GLOBALS['sv_ewige_runde'] == 1) {
    $sv_zoellnerertrag_default = array(0.0125 * 2, 0.00625 * 2, 0.00417 * 2, 0.003125 * 2);
} else {
    $sv_zoellnerertrag_default = array(0.0125, 0.00625, 0.00417, 0.003125);
}
$GLOBALS['sv_zoellnerertrag'] = EnvLoader::listFloat('SV_ZOELLNERERTRAG', $sv_zoellnerertrag_default);

//wieviel man von dem kopfgeld bekommt
$GLOBALS['sv_bounty_rate'] = EnvLoader::float('SV_BOUNTY_RATE', 0.10);

//autoreset auf dem server?
$GLOBALS['sv_auto_reset'] = EnvLoader::int('SV_AUTO_RESET', 0);

//Flottenpunkte im Sektorstatus verstecken
$GLOBALS['sv_hide_fp_in_secstatus'] = EnvLoader::int('SV_HIDE_FP_IN_SECSTATUS', 0);

$GLOBALS['sv_debug'] = EnvLoader::int('SV_DEBUG', 1);

//V-Systeme deaktivieren
$GLOBALS['sv_deactivate_vsystems'] = EnvLoader::int('SV_DEACTIVATE_VSYSTEMS', 0);

$GLOBALS['sv_max_alien_col'] = EnvLoader::int('SV_MAX_ALIEN_COL', 400);
$GLOBALS['sv_max_alien_col_typ'] = EnvLoader::int('SV_MAX_ALIEN_COL_TYP', 1);

//neue DE-Version
$GLOBALS['sv_ang'] = EnvLoader::int('SV_ANG', 1);

//Spieler beim Autoreset in Sektor 1 verschieben?
$GLOBALS['sv_autostart_move_player_to_sector_1'] = EnvLoader::int('SV_AUTOSTART_MOVE_PLAYER_TO_SECTOR_1', 0);

//Spieleraktionen mitloggen?
$GLOBALS['sv_log_player_actions'] = EnvLoader::int('SV_LOG_PLAYER_ACTIONS', 0);

$GLOBALS['tech_build_time_faktor'] = EnvLoader::float('TECH_BUILD_TIME_FAKTOR', 1);

$GLOBALS['sv_show_ally_secstatus'] = EnvLoader::int('SV_SHOW_ALLY_SECSTATUS', 3600);

//NPC config
$GLOBALS['sv_npc_base_url'] = EnvLoader::str('SV_NPC_BASE_URL', 'http://localhost:8880/de-api');
$GLOBALS['sv_npc_api_key'] = EnvLoader::str('SV_NPC_API_KEY', 'xxx');

//Tickplan: $wts/$kts je Stunde die Minuten, zu denen ein Tick läuft (Serveruhr).
//WTS ist eine Liste für alle 24 Stunden; KTS_HH je Stunde, leer = Default unten.
$wts_minuten = EnvLoader::listInt('WTS', array(0, 3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36, 39, 42, 45, 48, 51, 54, 57));
$GLOBALS['wts'] = array_fill(0, 24, $wts_minuten);

$kts_default = array(
    0 => array(), 1 => array(0), 2 => array(), 3 => array(0), 4 => array(), 5 => array(0),
    6 => array(), 7 => array(0),
    8 => array(0, 20, 40), 9 => array(0, 20, 40), 10 => array(0, 20, 40), 11 => array(0, 20, 40),
    12 => array(0, 20, 40), 13 => array(0, 20, 40), 14 => array(0, 20, 40), 15 => array(0, 20, 40),
    16 => array(0, 20, 40), 17 => array(0, 20, 40), 18 => array(0, 20, 40), 19 => array(0, 20, 40),
    20 => array(0, 20, 40, 14), 21 => array(0, 20, 40), 22 => array(0, 20, 40), 23 => array(0),
);
foreach ($kts_default as $stunde => $minuten) {
    $GLOBALS['kts'][$stunde] = EnvLoader::listInt('KTS_' . $stunde, $minuten);
}

//botschutz-antwortfenster in sekunden (optional, defaults stehen in botcheck.php:
//schneller als minsec = botverdacht, aelter als maxsec = straffrei verfallen)
EnvLoader::optional('sv_botcheck_minsec', 'SV_BOTCHECK_MINSEC', 'int');
EnvLoader::optional('sv_botcheck_maxsec', 'SV_BOTCHECK_MAXSEC', 'int');

//botschutz-schwierigkeit: 1=leicht (einstellig ohne zehneruebergang, default),
//2=mittel (einstellig mit uebertrag), 3=schwer (voller bereich bis 100)
EnvLoader::optional('sv_botcheck_schwierigkeit', 'SV_BOTCHECK_SCHWIERIGKEIT', 'int');

//Das Siegel von Basranur (Spezialsystem 5 in den Vergessenen Systemen), optional, ohne Eintrag gelten diese Werte
EnvLoader::optional('sv_siegel_dauer', 'SV_SIEGEL_DAUER', 'int'); //Länge einer Aufladeperiode in Wirtschaftsticks
EnvLoader::optional('sv_siegel_mission_zeit', 'SV_SIEGEL_MISSION_ZEIT', 'int'); //Dauer des Agenteneinsatzes BASRANUR in Sekunden
EnvLoader::optional('sv_siegel_anteil', 'SV_SIEGEL_ANTEIL', 'int'); //Resonanzkristalle pro Spieler und Periode, um als Mitwirkender zu zählen
EnvLoader::optional('sv_siegel_spieler_pro_stufe', 'SV_SIEGEL_SPIELER_PRO_STUFE', 'int'); //Mitwirkende je Stufe, jede Stufe gibt +2 % planetaren Grundertrag
EnvLoader::optional('sv_siegel_max_stufe', 'SV_SIEGEL_MAX_STUFE', 'int'); //höchste Stufe

//Hekate und Thanatos (Spezialsysteme 6 und 7 in den Vergessenen Systemen), optional, ohne Eintrag gelten diese Werte
EnvLoader::optional('sv_hekate_dauer', 'SV_HEKATE_DAUER', 'int'); //Länge einer Auftragsperiode bei Hekate in Wirtschaftsticks
EnvLoader::optional('sv_hekate_auftraege', 'SV_HEKATE_AUFTRAEGE', 'json'); //eigene Auftragsvorlagen (JSON), Aufbau siehe HekateService::AUFTRAEGE
EnvLoader::optional('sv_vs_bonus_industrie_prozent', 'SV_VS_BONUS_INDUSTRIE_PROZENT', 'int'); //Hekates Gunst: Industrie-Ertrag in den VS
EnvLoader::optional('sv_vs_bonus_bauzeit_prozent', 'SV_VS_BONUS_BAUZEIT_PROZENT', 'int'); //Hekates Gunst: kürzere Bauzeit in den VS (max. 50)
EnvLoader::optional('sv_vs_bonus_max_vorlauf', 'SV_VS_BONUS_MAX_VORLAUF', 'int'); //Hekates Gunst läuft höchstens so viele Wirtschaftsticks im Voraus
EnvLoader::optional('sv_thanatos_max_stufe', 'SV_THANATOS_MAX_STUFE', 'int'); //höchste Stufe auf dem Pfad des Thanatos
EnvLoader::optional('sv_thanatos_kosten_faktor', 'SV_THANATOS_KOSTEN_FAKTOR', 'int'); //Stufe L kostet L x Faktor Handelswaren V
EnvLoader::optional('sv_thanatos_palenium_pro_stufe', 'SV_THANATOS_PALENIUM_PRO_STUFE', 'int'); //Stufe L zahlt einmalig L x Wert Palenium
EnvLoader::optional('sv_thanatos_industrie_pro_stufe', 'SV_THANATOS_INDUSTRIE_PRO_STUFE', 'int'); //Industrie-Ertrag in den VS je Stufe in Prozent, bis Rundenende
EnvLoader::optional('sv_thanatos_bauzeit_pro_stufe', 'SV_THANATOS_BAUZEIT_PRO_STUFE', 'int'); //kürzere Bauzeit in den VS je Stufe in Prozent, bis Rundenende

//Feldzug um die Vergessenen Systeme (Wettstreit der Allianzen, FeldzugService, Regelwerk docs/feldzug.md)
//nur zu einem Rundenstart einschalten: der Feldzug vergibt Rundensiegartefakte
EnvLoader::optional('sv_feldzug_aktiv', 'SV_FELDZUG_AKTIV', 'int'); //1 schaltet den Feldzug ein (aus, wenn sv_deactivate_vsystems=1)
EnvLoader::optional('sv_feldzug_zug_wt', 'SV_FELDZUG_ZUG_WT', 'int'); //Länge eines Zuges in WT
EnvLoader::optional('sv_feldzug_start_zuege', 'SV_FELDZUG_START_ZUEGE', 'int'); //Rundenlaufzeit in Zügen bis zum ersten Aufruf
EnvLoader::optional('sv_feldzug_aufruf_zuege', 'SV_FELDZUG_AUFRUF_ZUEGE', 'int'); //Länge des Aufrufs in Zügen
EnvLoader::optional('sv_feldzug_kampf_zuege', 'SV_FELDZUG_KAMPF_ZUEGE', 'int'); //Länge der Kampfphase in Zügen
EnvLoader::optional('sv_feldzug_legionen', 'SV_FELDZUG_LEGIONEN', 'int'); //Legionen je Allianz und Zug
EnvLoader::optional('sv_feldzug_extra_brennpunkte', 'SV_FELDZUG_EXTRA_BRENNPUNKTE', 'int'); //Brennpunkte = Teilnehmer + dieser Wert
EnvLoader::optional('sv_feldzug_grundpreis', 'SV_FELDZUG_GRUNDPREIS', 'int'); //Eintrittspreis je VS-Rohstoffart beim ersten Aufruf, wächst mit der VS-Produktion

//Sektor 666 erwacht (Server-Boss für die Sektorflotten, Sektor666Service), optional, ohne Eintrag gelten diese Werte
EnvLoader::optional('sv_s666_aktiv', 'SV_S666_AKTIV', 'bool'); //false schaltet das Ereignis ab
EnvLoader::optional('sv_s666_start', 'SV_S666_START', 'int'); //erstes Erwachen bei diesem Rundenfortschritt in Prozent (WT / sv_winscore)
EnvLoader::optional('sv_s666_pause', 'SV_S666_PAUSE', 'int'); //Pause nach einer Niederlage in Prozent des Rundenfortschritts
EnvLoader::optional('sv_s666_hp_je_schiff', 'SV_S666_HP_JE_SCHIFF', 'float'); //Hülle je Sektorschiff (Wach- und Sektorflotte) der teilnehmenden Sektoren beim Erwachen
EnvLoader::optional('sv_s666_hp_je_sektor', 'SV_S666_HP_JE_SEKTOR', 'int'); //Mindesthülle je teilnehmendem Sektor, falls die Sektorflotten noch klein sind
EnvLoader::optional('sv_s666_stufenfaktor', 'SV_S666_STUFENFAKTOR', 'float'); //jede weitere Stufe hat so viel mehr Hülle
EnvLoader::optional('sv_s666_abwehr', 'SV_S666_ABWEHR', 'int'); //Abwehrfeuer: so viel Prozent der angreifenden Sektorschiffe gehen verloren
EnvLoader::optional('sv_s666_ausgleich', 'SV_S666_AUSGLEICH', 'int'); //so viel Prozent der Kosten verlorener Schiffe gehen beim Sieg zurück ins Sektorlager
EnvLoader::optional('sv_s666_mindestanteil', 'SV_S666_MINDESTANTEIL', 'int'); //Mindestanteil am Schaden in Prozent für Beute
EnvLoader::optional('sv_s666_beute', 'SV_S666_BEUTE', 'json'); //einzelne Werte der Beute (JSON), Aufbau siehe Sektor666Service::BEUTE
