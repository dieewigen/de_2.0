<?php
echo '<hr><br>AUTORESET';
//die datenbank zur�cksetzen

mysql_query("UPDATE de_user_data set tick=1, fixscore=0, score=0, restyp01=100000, restyp02=50000, restyp03=0, restyp04=0, restyp05=0, col=0, col_build=0, sonde=0, agent=0, agent_lost=0,
techs='s0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000', 
buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, tradescore=0, sells=0, allytag='', status=0, secmoves=0, votefor=0, secmoves=0, tcount=0, zcount=0, eartefakt=0, kartefakt=0, dartefakt=0, platz=1,
rang=1, scanhistory='', platz_last_day=1, trade_sell_sum=0, trade_buy_sum=0, trade_forbidden=0, spielername=nrspielername, rasse=nrrasse,
sm_rboost=0, actpoints=0, palenium=0, ally_tronic=0, sm_tronic=0, sm_kartefakt=0, sm_col=0, artbldglevel=1, 
sm_art1=0, sm_art2=0, sm_art3=0, sm_art4=0, sm_art5=0, sm_art6=0, sm_art7=0, sm_art8=0, sm_art9=0, sm_art10=0, sm_art11=0, sm_art12=0, sm_art13=0, sm_art14=0, sm_art15=0, 
spend01=0, spend02=0, spend03=0, spend04=0, spend05=0, npccol=0, archi=0, geworben=0, useefta=0, kg01=0, kg02=0, kg03=0, kg04=0, kgget=0, secatt=0, sc1=0, sc2=0, sc3=0, sc4=0, geteacredits=0,
ehlock=0, eftagetlastartefact=0, ehscore=0, defenseexp=0, geteftabonus=0, secstatdisable=0, dailygift=1, dailyallygift=1, helperprogress=0, 
npcartefact=0, specreset=0, spec1=0, spec2=0, spec3=0, spec4=0, spec5=0, tradesystemscore=0, tradesystemtrades=0, tradesystem_mb_uid=0, tradesystem_mb_tick=0, lastpcatt=0, fleetscore=0, eh_siege=0, eh_counter=0,
pve_score=0, pve_bldg_score=0, bgscore0=0, bgscore1=1, bgscore2=0, bgscore3=0, bgscore4=0;",$db);

mysql_query("UPDATE de_login set logins=2, clicks=0;",$db);
mysql_query("UPDATE de_login set status=1 WHERE (status > 2 AND delmode=0);",$db);

if(isset($GLOBALS['sv_autostart_move_player_to_sector_1']) && $GLOBALS['sv_autostart_move_player_to_sector_1']==1){
	mysql_query("UPDATE de_user_data SET sector=0, system=0 WHERE npc=0;",$db);
}else{
	mysql_query("UPDATE de_system SET reshuffle=1;",$db);
}

mysql_query("UPDATE de_user_fleet set komatt=0, komdef=0, zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0,
e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0, e90=0, artid1=0, artlvl1=0, artid2=0, artlvl2=0, artid3=0, artlvl3=0, artid4=0, artlvl4=0, artid5=0, artlvl5=0, artid6=0, artlvl6=0;",$db);

mysql_query("UPDATE de_sector set techs='s000000000', restyp01=0, restyp02=0, restyp03=0, restyp04=0, restyp05=0, buildgnr=0, buildgtime=0,
e1=0, e2=0, zielsec=0, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, ssteuer=5, skmes='', name='', url='', bk=0, pass='', platz=0, platz_last_day=0, votetimer=0, votecounter=0, ekey='100;0;0;0', col=0, arthold=0;",$db);

mysql_query("UPDATE de_artefakt SET sector='-1';",$db);

mysql_query("UPDATE de_trades SET e81=0, e82=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e100=0,  e101=0,  e102=0,  e103=0,  e104=0;",$db);

mysql_query("UPDATE de_system SET wt=1, kt=1, rundenstart_datum=CURDATE(), create_map_objects=1;",$db);

mysql_query("DELETE FROM `de_alliforum_posts`;",$db);
mysql_query("ALTER TABLE `de_alliforum_posts` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_alliforum_threads`;",$db);
mysql_query("ALTER TABLE `de_alliforum_threads` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_allys`;",$db);
mysql_query("ALTER TABLE `de_allys` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_ally_antrag`;",$db);
mysql_query("DELETE FROM `de_ally_buendniss_antrag`;",$db);
mysql_query("DELETE FROM `de_ally_history`;",$db);
mysql_query("ALTER TABLE `de_ally_history` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_ally_partner`;",$db);
mysql_query("DELETE FROM `de_ally_scans`;",$db);
mysql_query("ALTER TABLE `de_ally_scans` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_ally_stat`;",$db);
mysql_query("DELETE FROM `de_ally_storage`;",$db);
mysql_query("DELETE FROM `de_ally_war`;",$db);
mysql_query("DELETE FROM `de_auction`;",$db);
mysql_query("DELETE FROM `de_chat_msg`;",$db);
mysql_query("ALTER TABLE `de_chat_msg` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_hfn_buddy_ignore`;",$db);
mysql_query("DELETE FROM `de_hfn_usr_ally`;",$db);
mysql_query("DELETE FROM `de_news_server`;",$db);
mysql_query("DELETE FROM `de_news_sector`;",$db);
mysql_query("ALTER TABLE `de_news_sector` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_sectorforum_posts`;",$db);
mysql_query("ALTER TABLE `de_sectorforum_posts` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_sectorforum_threads`;",$db);
mysql_query("ALTER TABLE `de_sectorforum_threads` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_sector_build`;",$db);
mysql_query("DELETE FROM `de_sector_stat`;",$db);
mysql_query("DELETE FROM `de_tauction`;",$db);
mysql_query("ALTER TABLE `de_tauction` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_artefact`;",$db);
mysql_query("DELETE FROM `de_trade_depot`;",$db);
mysql_query("DELETE FROM `de_trade_fleetoffer`;",$db);
mysql_query("ALTER TABLE `de_trade_fleetoffer` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_fleetrequest`;",$db);
mysql_query("ALTER TABLE `de_trade_fleetrequest` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_fleettransit`;",$db);
mysql_query("ALTER TABLE `de_trade_fleettransit` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_log`;",$db);
mysql_query("ALTER TABLE `de_trade_log` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_resoffer`;",$db);
mysql_query("ALTER TABLE de_trade_resoffer AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_trade_resrequest`;",$db);
mysql_query("ALTER TABLE de_trade_resrequest AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_transactions`;",$db);
mysql_query("ALTER TABLE `de_transactions` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_user_achievement`;",$db);
mysql_query("DELETE FROM `de_user_artefact`;",$db);
mysql_query("ALTER TABLE `de_user_artefact` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_user_bg_register`;",$db);
mysql_query("DELETE FROM `de_user_build`;",$db);
mysql_query("DELETE FROM `de_user_getcol`;",$db);
mysql_query("DELETE FROM `de_user_hyper`;",$db);
mysql_query("DELETE FROM `de_user_ip`;",$db);
mysql_query("DELETE FROM `de_user_locks`;",$db);
mysql_query("ALTER TABLE `de_user_log` AUTO_INCREMENT = 1;",$db);
mysql_query("DELETE FROM `de_user_map`;",$db);
mysql_query("DELETE FROM `de_user_map_bldg`;",$db);
mysql_query("DELETE FROM `de_user_map_loot`;",$db);
mysql_query("DELETE FROM `de_user_mission`;",$db);
mysql_query("DELETE FROM `de_user_news`;",$db);
mysql_query("DELETE FROM `de_user_quest`;",$db);
mysql_query("DELETE FROM `de_user_scan`;",$db);
mysql_query("DELETE FROM `de_user_setbounty`;",$db);
mysql_query("DELETE FROM `de_user_special_ship`;",$db);
//mysql_query("DELETE FROM `de_user_stat`;",$db);
mysql_query("DELETE FROM `de_user_storage`;",$db);
mysql_query("DELETE FROM `de_user_techs`;",$db);
mysql_query("DELETE FROM `de_user_trade`;",$db);
mysql_query("ALTER TABLE `de_user_trade` AUTO_INCREMENT = 1;",$db);

echo '<hr>';
?>