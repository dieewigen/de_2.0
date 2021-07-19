<?PHP
include "../inccon.php";
?>

<html>
<head>
<title>Resspush</title>
<?php include "cssinclude.php";?>
</head>
<body>
<div align="center">
<?
include "det_userdata.inc.php";
if($button)
{
  mysql_query("UPDATE de_user_data set tick=1, fixscore=0, score=0, restyp01=10000, restyp02=5000, restyp03=0, restyp04=0, restyp05=0, col=0, sonde=0, agent=0,
techs='s0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',
buildgnr=0, buildgtime=0, resnr=0, restime=0, ekey='100;0;0;0', newtrans=0, newnews=0, e100=0, e101=0, e102=0, e103=0, e104=0, tradescore=0, sells=0, allytag='', status=0, secmoves=0, votefor=0, secmoves=0, tcount=0, zcount=0, eartefakt=0, kartefakt=0, dartefakt=0, platz=1,
werberid=0, rang=1, scanhistory='', platz_last_day=1, trade_sell_sum=0, trade_buy_sum=0, trade_forbidden=0, spielername=nrspielername, rasse=nrrasse,
sm_rboost=0, actpoints=0, palenium=0, ally_tronic=0, sm_tronic=0, sm_kartefakt=0, sm_col=0, artbldglevel=1,
sm_art1=0, sm_art2=0, sm_art3=0, sm_art4=0, sm_art5=0, sm_art6=0, sm_art7=0, sm_art8=0, sm_art9=0, spend01=0, spend02=0, spend03=0, spend04=0, spend05=0;");

  mysql_query("UPDATE de_login set status=1, clicks=0, last_login=NOW()");

  mysql_query("UPDATE de_user_fleet set komatt=0, komdef=0, zielsec=hsec, zielsys=hsys, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, entdeckt=0,e81=0, e82=0, e83=0, e84=0, e85=0, e86=0, e87=0, e88=0, e89=0");

  mysql_query("UPDATE de_sector set techs='s000000000', restyp01=0, restyp02=0, restyp03=0, restyp04=0, restyp05=0, buildgnr=0, buildgtime=0,
e1=0, e2=0, zielsec=0, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, ssteuer=5, skmes='', name='', url='', bk=0, pass='', platz=0, platz_last_day=0;");

  mysql_query("UPDATE de_artefakt set sector='-1'");

    mysql_query("
TRUNCATE `de_alliforum_posts`;
TRUNCATE `de_alliforum_threads`;
TRUNCATE `de_ally_antrag`;
TRUNCATE `de_ally_buendniss_antrag`;
TRUNCATE `de_ally_history`;
TRUNCATE `de_ally_partner`;
TRUNCATE `de_ally_scans`;
TRUNCATE `de_ally_war`;
TRUNCATE `de_allys`;
TRUNCATE `de_cyborg_data`;
TRUNCATE `de_cyborg_enm`;
TRUNCATE `de_cyborg_item`;
TRUNCATE `de_cyborg_quest`;
TRUNCATE `de_hfn_buddy_ignore`;
TRUNCATE `de_hfn_usr_ally`;
TRUNCATE `de_sector_build`;
TRUNCATE `de_sector_umzug`;
TRUNCATE `de_sector_voteout`;
TRUNCATE `de_sectorforum_posts`;
TRUNCATE `de_sectorforum_threads`;
TRUNCATE `de_tauction`;
TRUNCATE `de_trade_artefact`;
TRUNCATE `de_trade_log`;
TRUNCATE `de_trade_resoffer`;
TRUNCATE `de_trade_resrequest`;
TRUNCATE `de_transactions`;
TRUNCATE `de_user_artefact`;
TRUNCATE `de_user_build`;
TRUNCATE `de_user_hyper`;
TRUNCATE `de_user_ip`;
TRUNCATE `de_user_locks`;
TRUNCATE `de_user_news`;
TRUNCATE `de_user_scan`;
TRUNCATE `de_user_stat`;
TRUNCATE `de_vote_stimmen`;
TRUNCATE `de_vote_umfragen`;
");

  die('Softreset ohne Reshuffle aktiviert.</body></html>');
}
?>
<form action="softresetos.php" method="post">
<br>
<input type="Submit" name="button" value="Softreset ohne Reshuffle durchführen">
</form>
</body>
</html>

