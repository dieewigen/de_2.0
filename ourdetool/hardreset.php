<?PHP
include "../inccon.php";
?>

<html>
<head>
<title>Hardreset</title>
<?php include "cssinclude.php";?>
</head>
<body text="#000000" bgcolor="#FFFFFF" link="#FF0000" alink="#FF0000" vlink="#FF0000">
<div align="center">
<?
include "det_userdata.inc.php";
if($ressbtn)
{
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
TRUNCATE `de_login`;
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
TRUNCATE `de_user_data`;
TRUNCATE `de_user_fleet`;
TRUNCATE `de_user_hyper`;
TRUNCATE `de_user_info`;
TRUNCATE `de_user_ip`;
TRUNCATE `de_user_locks`;
TRUNCATE `de_user_news`;
TRUNCATE `de_user_scan`;
TRUNCATE `de_user_stat`;
TRUNCATE `de_vote_stimmen`;
TRUNCATE `de_vote_umfragen`;
");

  mysql_query("UPDATE de_sector set techs='s000000000', restyp01=0, restyp02=0, restyp03=0, restyp04=0, restyp05=0, buildgnr=0, buildgtime=0,
  e1=0, e2=0, zielsec=0, aktion=0, zeit=0, aktzeit=0, gesrzeit=0, ssteuer=5, skmes='', name='', url='', bk=0, pass='', platz=1, platz_last_day=0;");

  mysql_query("UPDATE de_artefakt set sector='-1'");

  echo '<h1>Alle Accounts wurden entfernt</h1>';
  exit;
}
?>
<form action="hardreset.php" method="post">
Alle Accounts werden entfernt.<br><br>
<input type="Submit" name="ressbtn" value="Accounts entfernen"></td>
</tr>
</table>
</form>

