<?php
//21.01.2005 link zum schwarzmarkt eingebaut, Isso
//28.07.2005 link zum artefakthandel eingebaut und design auf eine html-tabelle umgestellt
//08.02.2010 link zum konfigurationsmenü ausgebaut, da ca. 5 jahre ohne funktion
//16.02.2010 html-code überarbeitet und neues buttondesign integriert

include 'inc/lang/'.$sv_server_lang.'_trade.menu.lang.php';

//gildenlogo anzeigen

echo '<img src="'.$ums_gpfad.'g/trade/'.$ums_rasse.'_tradelogo.gif" "alt=Gildenlogo">';

echo '
<table width="600">
<tr align="center">
	<td align="center"><a href="trade.php?viewmode=overview" class="btn">'.$trademenu_lang[uebersicht].'</a></td>
	<td><a href="trade.php?viewmode=m_res" class="btn">'.$trademenu_lang[traderess].'</a></td>
	<td><a href="trade.php?viewmode=m_fleet" class="btn">'.$trademenu_lang[tradefleet].'</a></td>
<td><a href="trade.php?viewmode=view_own" class="btn">'.$trademenu_lang[verwaltungeigenerangebote].'</a></td>
</tr>
<tr align="center">							
	<td><a href="trade.php?viewmode=depot" class="btn">'.$trademenu_lang[ihandelsdepot].'</a></td>
</tr>
</table>';

//entfernt da ohne funktion
//<td><a href=trade.php?viewmode=config alt=\"$trademenu_lang[konfigurationsoptionen]\"><img border=0 src=".$ums_gpfad."g/trade/".$ums_rasse."_konfiguration.gif></a></td>
?>