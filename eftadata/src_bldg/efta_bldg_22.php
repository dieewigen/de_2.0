<?php
//inkarnationssschrein
include("lib/transaction.lib.php");

if($_REQUEST["do"]==1 AND $level>=$maxplayerlevel){
	//setze die werte zurück	
	mysql_query("UPDATE de_cyborg_data SET str=10, dex=10, konst=10, inbldg=0, x=0, y=0, map=0, oldx=0, oldy=0, hp=100, hpmax=100, mp=0, mpmax=0, bewpunkte='$sv_max_efta_bew_punkte', exp=0, explastday=0, incarnation=incarnation+1, level=1, backpacksize=10, buff1=0, cooldown1=0 WHERE user_id='$efta_user_id'",$eftadb);

	//quests enfternen
	mysql_query("DELETE FROM de_cyborg_quest WHERE typ>=100 AND user_id='$efta_user_id'",$eftadb);

	//items entfernen
	mysql_query("DELETE FROM de_cyborg_item WHERE typ<>20 AND user_id='$efta_user_id'",$eftadb);

	//anfängerausrüstung verpassen
	//helm 3
	add_item(2, 1);
	//brust 5
	add_item(3, 1);
	//hose 11
	add_item(4, 1);
	//stiefel 12
	add_item(5, 1);
	//handschuhe 7
	add_item(6, 1);
	//schild 2
	add_item(7, 1);
	//waffenhand 1
	add_item(8, 1);
	//nachdem die gegenstände hinterlegt wurden diese auch anlegen
	mysql_query("UPDATE de_cyborg_item SET equip=1 WHERE typ<>20 AND user_id='$efta_user_id'",$eftadb);

	echo '<script>lnk("");</script>';
	exit;
}

echo '<br><br>';
rahmen0_oben();
rahmen1_oben('<div align="center"><b>Schrein der Inkarnation</b></div>');

$bg='cell1';
echo '<table width="100%" cellpadding="1" cellspacing="1">
	  <tr align="center"><td class="'.$bg.'"><b>Im Schrein der Inkarnation kann der Charakter, der die maximale Stufe erreicht hat, sein altes Leben hinter sich lassen und neu beginnen. Er verliert dabei alles bis auf seinen Ruhm, sein Geld und seinen Heldenturm. Das Lager im Heldenturm geht hierbei auch verloren.</b></td></tr>';

$bg='cell';
echo '<tr align="left"><td class="'.$bg.'"><b>&nbsp;Folgende Aktionen sind hier m&ouml;glich:</b></td></tr>';    


$bg='cell1';

//neues leben beginnen, wenn der passende level erreicht ist
if($level>=$maxplayerlevel)
echo '<tr align="left"><td class="'.$bg.'">&nbsp;<a href="#" onClick="lnk(\'do=1\')">Beginne ein neues Leben</a></td></tr>';
else echo '<tr align="left"><td class="'.$bg.'">&nbsp;Du hast noch nicht die maximale Stufe erreicht.</td></tr>';

//gebäude verlassen
$bg='cell';
echo '<tr align="left"><td class="'.$bg.'">&nbsp;<a href="#" onClick="lnk(\'leavebldg=1\')">Schrein verlassen</a></td></tr>';

//evtl. fehlermeldungen ausgeben
if($errmsg!='')
{
  $bg='cell1';
  echo '<tr align="left"><td class="'.$bg.'"><b>&nbsp;Achtung: '.$errmsg.'</b></td></tr>';
}
   
echo '</table>';


rahmen1_unten();
rahmen0_unten();

echo '</body></html>';
exit;
?>