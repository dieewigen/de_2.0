#RUNDENSTART
UPDATE de_user_data set tick=1, fixscore=0, score=0, restyp01=100000, restyp02=50000, restyp03=0, restyp04=0, restyp05=0, col=0, col_build=0, sonde=0, agent=0, agent_lost=0,
techs='s0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000', 
buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, tradescore=0, sells=0, allytag='', status=0, secmoves=0, votefor=0, secmoves=0, tcount=0, zcount=0, eartefakt=0, kartefakt=0, dartefakt=0, platz=1,
rang=1, scanhistory='', platz_last_day=1, trade_sell_sum=0, trade_buy_sum=0, trade_forbidden=0, spielername=nrspielername, rasse=nrrasse,
sm_rboost=0, actpoints=0, palenium=0, ally_tronic=0, sm_tronic=0, sm_kartefakt=0, sm_col=0, artbldglevel=1, 
sm_art1=0, sm_art2=0, sm_art3=0, sm_art4=0, sm_art5=0, sm_art6=0, sm_art7=0, sm_art8=0, sm_art9=0, sm_art10=0, sm_art11=0, sm_art12=0, sm_art13=0, sm_art14=0, sm_art15=0, 
spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, npccol=0, archi=0, geworben=0, useefta=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0,
ehlock=0, eftagetlastartefact=0, ehscore=0, defenseexp=0, geteftabonus=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, npcartefact=0, 
specreset=0, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0, tradesystemscore=0, tradesystemtrades=0, tradesystem_mb_uid=0, tradesystem_mb_tick=0, lastpcatt=0, fleetscore=0, eh_siege=0, eh_counter=0,
pve_score=0, pve_bldg_score=0, bgscore0=0, bgscore1=1, bgscore2=0, bgscore3=0, bgscore4=0;



UPDATE de_login set logins=2, clicks=0;
UPDATE de_login set status=1 WHERE (status > 2 AND delmode=0);

UPDATE de_user_fleet set komatt=0, komdef=0, zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0,
e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0, e90=0, artid1=0, artlvl1=0, artid2=0, artlvl2=0, artid3=0, artlvl3=0, artid4=0, artlvl4=0, artid5=0, artlvl5=0, artid6=0, artlvl6=0;

UPDATE de_sector set techs='s000000000', restyp01=0, restyp02=0, restyp03=0, restyp04=0, restyp05=0, buildgnr=0, buildgtime=0,
e1=0, e2=0, zielsec=0, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, ssteuer=5, skmes='', name='', url='', bk=0, pass='', platz=0, platz_last_day=0, votetimer=0, votecounter=0, ekey='100;0;0;0', col=0, arthold=0;

UPDATE de_artefakt SET sector='-1';

#UPDATE de_trades SET e81=0, e82=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0, e90=0, e100=0,  e101=0,  e102=0,  e103=0,  e104=0;  

#zuerst gesperrte killen
#update de_login set last_login='0000-00-00 00:00:00' where status=2

UPDATE de_system SET wt=1, kt=1, rundenstart_datum=CURDATE(), reshuffle=1, create_map_objects=1;

#ki-sektorn neu sk/bk usw. setzen
DELETE FROM `de_alliforum_posts`;
ALTER TABLE `de_alliforum_posts` AUTO_INCREMENT = 1;
DELETE FROM `de_alliforum_threads`;
ALTER TABLE `de_alliforum_threads` AUTO_INCREMENT = 1;
DELETE FROM `de_allys`;
ALTER TABLE `de_allys` AUTO_INCREMENT = 1;
DELETE FROM `de_ally_antrag`;
DELETE FROM `de_ally_buendniss_antrag`;
DELETE FROM `de_ally_history`;
ALTER TABLE `de_ally_history` AUTO_INCREMENT = 1;
DELETE FROM `de_ally_partner`;
DELETE FROM `de_ally_scans`;
ALTER TABLE `de_ally_scans` AUTO_INCREMENT = 1;
DELETE FROM `de_ally_stat`;
DELETE FROM `de_ally_storage`;
DELETE FROM `de_ally_war`;
DELETE FROM `de_auction`;
ALTER TABLE `de_auction` AUTO_INCREMENT = 1;
DELETE FROM `de_chat_msg`;
ALTER TABLE `de_chat_msg` AUTO_INCREMENT = 1;
DELETE FROM `de_hfn_buddy_ignore`;
DELETE FROM `de_hfn_usr_ally`;
DELETE FROM `de_news_server`;
DELETE FROM `de_news_sector`;
ALTER TABLE `de_news_sector` AUTO_INCREMENT = 1;
DELETE FROM `de_sectorforum_posts`;
ALTER TABLE `de_sectorforum_posts` AUTO_INCREMENT = 1;
DELETE FROM `de_sectorforum_threads`;
ALTER TABLE `de_sectorforum_threads` AUTO_INCREMENT = 1;
DELETE FROM `de_sector_build`;
DELETE FROM `de_sector_stat`;
DELETE FROM `de_tauction`;
ALTER TABLE `de_tauction` AUTO_INCREMENT = 1;
DELETE FROM `de_trade_artefact`;
DELETE FROM `de_trade_depot`;
DELETE FROM `de_trade_fleetoffer`;
ALTER TABLE `de_trade_fleetoffer` AUTO_INCREMENT = 1;
DELETE FROM `de_trade_fleetrequest`;
ALTER TABLE `de_trade_fleetrequest` AUTO_INCREMENT = 1;
DELETE FROM `de_trade_fleettransit`;
ALTER TABLE `de_trade_fleettransit` AUTO_INCREMENT = 1;
DELETE FROM `de_trade_log`;
ALTER TABLE `de_trade_log` AUTO_INCREMENT = 1;
DELETE FROM `de_trade_resoffer`;
ALTER TABLE de_trade_resoffer AUTO_INCREMENT = 1;
DELETE FROM `de_trade_resrequest`;
ALTER TABLE de_trade_resrequest AUTO_INCREMENT = 1;
DELETE FROM `de_transactions`;
ALTER TABLE `de_transactions` AUTO_INCREMENT = 1;
DELETE FROM `de_user_achievement`;
DELETE FROM `de_user_artefact`;
ALTER TABLE `de_user_artefact` AUTO_INCREMENT = 1;
DELETE FROM `de_user_bg_register`;
DELETE FROM `de_user_build`;
DELETE FROM `de_user_getcol`;
DELETE FROM `de_user_hyper`;
DELETE FROM `de_user_ip`;
DELETE FROM `de_user_locks`;
ALTER TABLE `de_user_log` AUTO_INCREMENT = 1;
DELETE FROM `de_user_map`;
DELETE FROM `de_user_map_bldg`;
DELETE FROM `de_user_map_loot`;
DELETE FROM `de_user_mission`;
DELETE FROM `de_user_news`;
DELETE FROM `de_user_quest`;
DELETE FROM `de_user_scan`;
DELETE FROM `de_user_setbounty`;
DELETE FROM `de_user_special_ship`;
DELETE FROM `de_user_storage`;
DELETE FROM `de_user_techs`;
DELETE FROM `de_user_trade`;
ALTER TABLE `de_user_trade` AUTO_INCREMENT = 1;