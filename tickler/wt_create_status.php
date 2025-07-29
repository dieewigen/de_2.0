<?php
$filename = $directory."cache/loginstat.tmp";

$cachefile = fopen ($filename, 'w');

//spieleranzahl pro rasse
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE rasse=? AND sector > 1", [1]);
$row = mysqli_fetch_array($db_daten);
$rassenzahl[0] = $row['count'];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE rasse=? AND sector > 1", [2]);
$row = mysqli_fetch_array($db_daten);
$rassenzahl[1] = $row['count'];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE rasse=? AND sector > 1", [3]);
$row = mysqli_fetch_array($db_daten);
$rassenzahl[2] = $row['count'];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE rasse=? AND sector > 1", [4]);
$row = mysqli_fetch_array($db_daten);
$rassenzahl[3] = $row['count'];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE rasse=?", [5]);
$row = mysqli_fetch_array($db_daten);
$rassenzahl[4] = $row['count'];

//spiele die kein npc sind
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_data WHERE npc=0 AND sector > 1", []);
$row = mysqli_fetch_array($db_daten);
$pcspieler = $row['count'];
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
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(id) as count FROM de_allys", []);
$row = mysqli_fetch_array($source);
$allianzen[0]=$row['count'];
//b�ndnisse
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(ally_id_1) as count FROM de_ally_partner", []);
$row = mysqli_fetch_array($source);
$allianzen[1]=$row['count'];

//kriege
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(ally_id_angreifer) as count FROM de_ally_war", []);
$row = mysqli_fetch_array($source);
$allianzen[2]=$row['count'];

//kollektoren im spiel
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=?", [1]);
$row = mysqli_fetch_array($db_daten);
$col1=$row["sumcol"];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=?", [2]);
$row = mysqli_fetch_array($db_daten);
$col2=$row["sumcol"];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=?", [3]);
$row = mysqli_fetch_array($db_daten);
$col3=$row["sumcol"];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=?", [4]);
$row = mysqli_fetch_array($db_daten);
$col4=$row["sumcol"];
$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(col) as sumcol FROM de_user_data WHERE rasse=?", [5]);
$row = mysqli_fetch_array($db_daten);
$col5=$row["sumcol"];
$col=$col1+$col2+$col3+$col4+$col5;
if($col==0)$cold=1; else $cold=$col;

//anzahl nachrichten
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(user_id) as count FROM de_user_news", []);
$row = mysqli_fetch_array($source);
$anznews=$row['count'];

//annzahl hyperfunknachrichten
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT count(empfaenger) as count FROM de_user_hyper", []);
$row = mysqli_fetch_array($source);
$anzhyper=$row['count'];

//agentenanzahl
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT SUM(agent) as sum FROM de_user_data", []);
$row = mysqli_fetch_array($source);
$anzagent=$row['sum'];

//gespielte wochen
$source=mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) as max FROM de_user_data", []);
$row = mysqli_fetch_array($source);
$anzticks=$row['max'];


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


