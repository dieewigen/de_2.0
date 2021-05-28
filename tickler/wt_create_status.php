<?php
$filename = $directory."cache/loginstat.tmp";

$cachefile = fopen ($filename, 'w');

//spieleranzahl pro rasse
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE rasse=1 AND sector > 1",$db);
$rassenzahl[0] = mysql_result($db_daten,0,0);
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE rasse=2 AND sector > 1",$db);
$rassenzahl[1] = mysql_result($db_daten,0,0);
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE rasse=3 AND sector > 1",$db);
$rassenzahl[2] = mysql_result($db_daten,0,0);
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE rasse=4 AND sector > 1",$db);
$rassenzahl[3] = mysql_result($db_daten,0,0);
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE rasse=5",$db);
$rassenzahl[4] = mysql_result($db_daten,0,0);

//spiele die kein npc sind
$db_daten=mysql_query("SELECT count(user_id) FROM de_user_data WHERE npc=0 AND sector > 1",$db);
$pcspieler = mysql_result($db_daten,0,0);
if($pcspieler==0){
	$pcspieler=1;
}

//inaktivenscript aktiv
if ($dodel==1){
	$inaktivmsg=$wt_lang['aktiv'];
}else{
	$inaktivmsg=$wt_lang['inaktiv'];
}

//allianzen
//anzahl der allianzen
$source=mysql_query("SELECT count(id) FROM de_allys",$db);
$allianzen[0]=mysql_result($source,0,0);
//b�ndnisse
$source=mysql_query("SELECT count(ally_id_1) FROM de_ally_partner",$db);
$allianzen[1]=mysql_result($source,0,0);

//kriege
$source=mysql_query("SELECT count(ally_id_angreifer ) FROM de_ally_war",$db);
$allianzen[2]=mysql_result($source,0,0);

//kollektoren im spiel
$db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=1",$db);
$row = mysql_fetch_array($db_daten);
$col1=$row["sumcol"];
$db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=2",$db);
$row = mysql_fetch_array($db_daten);
$col2=$row["sumcol"];
$db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=3",$db);
$row = mysql_fetch_array($db_daten);
$col3=$row["sumcol"];
$db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=4",$db);
$row = mysql_fetch_array($db_daten);
$col4=$row["sumcol"];
$db_daten=mysql_query("SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=5",$db);
$row = mysql_fetch_array($db_daten);
$col5=$row["sumcol"];
$col=$col1+$col2+$col3+$col4+$col5;
if($col==0)$cold=1; else $cold=$col;

//anzahl nachrichten
$source=mysql_query("SELECT count(user_id) FROM de_user_news",$db);
$anznews=mysql_result($source,0,0);

//annzahl hyperfunknachrichten
$source=mysql_query("SELECT count(empfaenger) FROM de_user_hyper",$db);
$anzhyper=mysql_result($source,0,0);

//agentenanzahl
$source=mysql_query("SELECT SUM(agent) FROM de_user_data",$db);
$anzagent=mysql_result($source,0,0);

//gespielte wochen
$source=mysql_query("SELECT MAX(tick) FROM de_user_data",$db);
$anzticks=mysql_result($source,0,0);


xecho ('
<tr class="cellu">
<td width="13" height="25" class="rl">&nbsp;</td>

<td width="275" align="center" colspan="2">'.$wt_lang['accounts'].'</td>
<td style="width: 50px;">&nbsp;</td>
<td width="275" align="center" colspan="2">'.$wt_lang['kollektoren'].'</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">'.$wt_lang['gesamt'].':</td>
<td align="center">'.number_format($pcspieler, 0,"",".").' (au&szlig;erhalb von Sektor 1)</td>
<td>&nbsp;</td>
<td align="left">'.$wt_lang['gesamt'].':</td>
<td align="center">'.number_format($col, 0,"",".").'</td>
<td class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">- Ewige</td>
<td align="center">'.number_format($rassenzahl[0], 0,"",".").' ('.number_format($rassenzahl[0]*100/$pcspieler, 2,",",".").'%)</td>
<td>&nbsp;</td>
<td align="left">- Ewige</td>
<td align="center">'.number_format($col1, 0,"",".").' ('.number_format($col1*100/$cold, 2,",",".").'%)</td>
<td class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">- Ishtar</td>
<td align="center">'.number_format($rassenzahl[1], 0,"",".").' ('.number_format($rassenzahl[1]*100/$pcspieler, 2,",",".").'%)</td>
<td>&nbsp;</td>
<td align="left">- Ishtar</td>
<td align="center">'.number_format($col2, 0,"",".").' ('.number_format($col2*100/$cold, 2,",",".").'%)</td>
<td class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">- K&#180;Tharr</td>
<td align="center">'.number_format($rassenzahl[2], 0,"",".").' ('.number_format($rassenzahl[2]*100/$pcspieler, 2,",",".").'%)</td>
<td>&nbsp;</td>
<td align="left">- K&#180;Tharr</td>
<td align="center">'.number_format($col3, 0,"",".").' ('.number_format($col3*100/$cold, 2,",",".").'%)</td>
<td class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">- Z&#180;tah-ara</td>
<td align="center">'.number_format($rassenzahl[3], 0,"",".").' ('.number_format($rassenzahl[3]*100/$pcspieler, 2,",",".").'%)</td>
<td>&nbsp;</td>
<td align="left">- Z&#180;tah-ara</td>
<td align="center">'.number_format($col4, 0,"",".").' ('.number_format($col4*100/$cold, 2,",",".").'%)</td>
<td class="rr">&nbsp;</td>
</tr>
<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">- DX61a23</td>
<td align="center">'.number_format($rassenzahl[4], 0,"",".").'</td>
<td>&nbsp;</td>
<td align="left">- DX61a23</td>
<td align="center">'.number_format($col5, 0,"",".").' ('.number_format($col5*100/$cold, 2,",",".").'%)</td>
<td class="rr">&nbsp;</td>
</tr>

<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="center" colspan="2">'.$wt_lang['allianzen'].'</td>
<td>&nbsp;</td>
<td align="center" colspan="2">'.$wt_lang['sonstiges'].'</td>
<td class="rr">&nbsp;</td>
</tr>

<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">'.$wt_lang['allianzanzahl'].':</td>
<td align="center">'.number_format($allianzen[0], 0,"",".").'</td>
<td>&nbsp;</td>
<td align="left">'.$wt_lang['inaktivenloeschung'].':</td>
<td align="center">'.$inaktivmsg.'</td>
<td class="rr">&nbsp;</td>
</tr>

<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">'.$wt_lang['buendnisse'].':</td>
<td align="center">'.number_format($allianzen[1], 0,"",".").'</td>
<td>&nbsp;</td>
<td align="left">'.$wt_lang['gespielteticks'].':</td>
<td align="center">'.number_format($anzticks, 0,"",".").'</td>
<td class="rr">&nbsp;</td>
</tr>

<tr class="cellu">
<td height="25" class="rl">&nbsp;</td>
<td align="left">'.$wt_lang['kriege'].':</td>
<td align="center">'.number_format($allianzen[2], 0,"",".").'</td>
<td>&nbsp;</td>
<td align="left"></td>
<td align="center"></td>
<td class="rr">&nbsp;</td>
</tr>
');

/*

Vorhandene Artefakte

Cyborg mit der meisten erfahrung
Cyborg mit der meisten st�rke
Cyborg mit der meisten geschick

Summe der Sektoren mit Sekbasis
gr��te Sek - Flotte
Druchschnitt der Flotten pro Sek

Posts Sekforum & SK Forum
Threads Sekforum & SK Forum

T in der Auktion


Anzahl der gespielten Ticks

Durchschnitt der Kollies pro Spieler

Summe aller agenten
Durchschnitt der Kollies pro Spieler

Summe aller Sonden
Durchschnitt der Sonden pro Spieler

M D I E T gesamt
Durchschnitt M D I E T pro spieler

Summe der HFN's
Durchschnitt HFN's pro spieler

Nachrichten
Durchschnitt Nachrichten pro spieler

Summe der einzelnen schiffe
Durchschnitt der schiffe pro spieler
*/

?>


