<?php
include "../inccon.php";
//sv.inc.php includen
include "../inc/sv.inc.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_server_einstellungen.php" method="post">
<div align="center">
<?php

include "det_userdata.inc.php";

if ($savedata)
{
  $filename="../inc/sv.inc.php";
  $cachefile = fopen ($filename, 'w');

  $str="<?php\n\n";

  $str.='$sv_image_server="'.$sv_image_server.'";'."\n";

  for ($i=0;$i<count($sv_image_server_list);$i++)
  $str.='$sv_image_server_list[]="'.$sv_image_server_list[$i].'";'."\n";

  if(!validDigit($gewinnpunktzahl))$gewinnpunktzahl=0;
  if(!validDigit($gewinnhaltezeit))$gewinnhaltezeit=0;
  $str.='$sv_winscore='.$gewinnpunktzahl.';'."\n\n";
  $str.='$sv_benticks='.$gewinnhaltezeit.';'."\n\n";
  
  $str.='$sv_maxuser='.$sv_maxuser.';'."\n\n";

  if(!validDigit($maxsec))$maxsec=0;
  if(!validDigit($maxsys))$maxsys=0;
  $str.='$sv_maxsector='.$maxsec.';'."\n\n";
  $str.='$sv_maxsystem='.$maxsys.';'."\n\n";
  
  if(!validDigit($inaktdeltime))$inaktdeltime=1;
  if ($inaktdeltime<1)$inaktdeltime=1;
  $str.='$sv_inactiv_deldays='.$inaktdeltime.';'."\n\n";
  $str.='$sv_not_activated_deldays='.$sv_not_activated_deldays.';'."\n\n";

  $str.='$sv_hf_deldays='.$sv_hf_deldays.';'."\n\n";
  $str.='$sv_nachrichten_deldays='.$sv_nachrichten_deldays.';'."\n\n";
  $str.='$sv_max_efta_bew_punkte='.$sv_max_efta_bew_punkte.';'."\n\n";
  
  if(!validDigit($attgrenze))$attgrenze=0;
  if(!validDigit($attgrenzewhg))$attgrenzewhg=0;
  $attgrenze=$attgrenze/100;
  $attgrenzewhg=$attgrenzewhg/100;
  $str.='$sv_attgrenze='.$attgrenze.';'."\n\n";
  $str.='$sv_attgrenze_whg_bonus='.$attgrenzewhg.';'."\n\n";

  $str.='$sv_show_maxsector='.$sv_show_maxsector.';'."\n\n";
  $str.='$sv_npc_minsector='.$sv_npc_minsector.';'."\n\n";
  $str.='$sv_npc_maxsector='.$sv_npc_maxsector.';'."\n\n";
  $str.='$sv_free_startsectors='.$sv_free_startsectors.';'."\n\n";

  if(!validDigit($votegrenze))$votegrenze=0;
  $str.='$sv_voteoutgrenze='.$votegrenze.';'."\n\n";
  
  if(!validDigit($maxsecmoves))$maxsecmoves=0;
  if(!validDigit($minsecmovesmember))$minsecmovesmember=0;
  if(!validDigit($maxsecmovesmember))$maxsecmovesmember=0;
  $str.='$sv_max_secmoves='.$maxsecmoves.';'."\n\n";
  $str.='$sv_min_user_per_regsector='.$minsecmovesmember.';'."\n\n";
  $str.='$sv_max_user_per_regsector='.$maxsecmovesmember.';'."\n\n";
  $str.='$sv_min_regsec='.$sv_min_regsec.';'."\n\n";
  
  //$str.='$sv_server_tag="'.$sv_server_tag.'";'."\n\n";
  $str.='$sv_server_name="'.$servername.'";'."\n\n";
  
  if(!validDigit($schildbonus))$schildbonus=0;
  $str.='$sv_ps_bonus='.$schildbonus.';'."\n\n";
  
  if(!validDigit($recyclotron))$recyclotron=0;
  if(!validDigit($recyclotronwhg))$recyclotronwhg=0;
  $str.='$sv_recyclotron_bonus='.$recyclotron.';'."\n\n";
  $str.='$sv_recyclotron_bonus_whg='.$recyclotronwhg.';'."\n\n";
  
  $str.='$sv_servid='.$sv_servid.';'."\n\n";
  $str.='$sv_anz_schiffe='.$sv_anz_schiffe.';'."\n";
  $str.='$sv_anz_tuerme='.$sv_anz_tuerme.';'."\n";
  $str.='$sv_anz_rassen='.$sv_anz_rassen.';'."\n";
  $str.='$sv_hf_buddie='.$sv_hf_buddie.';'."\n";
  $str.='$sv_hf_ignore='.$sv_hf_ignore.';'."\n";
  $str.='$sv_hf_archiv='.$sv_hf_archiv.';'."\n\n";
  $str.='$sv_hf_buddie_p='.$sv_hf_buddie_p.';'."\n";
  $str.='$sv_hf_ignore_p='.$sv_hf_ignore_p.';'."\n";
  $str.='$sv_hf_archiv_p='.$sv_hf_archiv_p.';'."\n\n";

  if(!validDigit($klaurate))$klaurate=0;
  $klaurate=$klaurate/100;
  $str.='$sv_kollie_klaurate='.$klaurate.';'."\n\n";

  if(!validDigit($kollieertrag))$kollieertrag=0;
  if(!validDigit($kollieertragpa))$kollieertragpa=0;
  $str.='$sv_kollieertrag='.$kollieertrag.';'."\n\n";
  $str.='$sv_kollieertrag_pa='.$kollieertragpa.';'."\n\n";

  if(!validDigit($pga1))$pga1=0;
  if(!validDigit($pga2))$pga2=0;
  if(!validDigit($pga3))$pga3=0;
  if(!validDigit($pga4))$pga4=0;
  if(!validDigit($pga1whg))$pga1whg=0;
  if(!validDigit($pga2whg))$pga2whg=0;
  if(!validDigit($pga3whg))$pga3whg=0;
  if(!validDigit($pga4whg))$pga4whg=0;

  $str.='$sv_plan_grundertrag=array('.$pga1.','.$pga2.','.$pga3.','.$pga4.');'."\n\n";
  $str.='$sv_plan_grundertrag_whg=array('.$pga1whg.','.$pga2whg.','.$pga3whg.','.$pga4whg.');'."\n\n";

  $str.='$mods=array(0);'."\n\n";
  $str.='$sv_session_lifetime='.$sv_session_lifetime.';'."\n\n";

  if(!validDigit($tronicw))$tronicw=0;
  if(!validDigit($zufallw))$zufallw=0;
  if(!validDigit($zufallstart))$zufallstart=0;
  $str.='$sv_globalw_tronic='.$tronicw.';'."\n\n";
  $str.='$sv_globalw_zufall='.$zufallw.';'."\n\n";
  $str.='$sv_global_start_zufall='.$zufallstart.';'."\n\n";

  if(!validDigit($artefaktstart))$artefaktstart=0;
  $str.='$sv_artefaktstart='.$artefaktstart.';'."\n\n";

  if(!validDigit($activetime))$activetime=0;
  $str.='$sv_activetime='.$activetime.';'."\n\n";
  
  if(!validDigit($maxdartefakt))$maxdartefakt=0;
  $str.='$sv_max_dartefakt='.$maxdartefakt.';'."\n\n";
  
  if(!validDigit($maxpalenium))$maxpalenium=0;
  $str.='$sv_max_palenium='.$maxpalenium.';'."\n\n";

  if(!validDigit($kartepunkte))$kartepunkte=0;
  $str.='$sv_kartepunkte='.($kartepunkte/100).';'."\n\n";

  $str.='$sv_sm_preisliste=array (50, 40, 100, 300, 175, 20, 50);'."\n\n";
  
  $str.='$sv_pcs_id='.$sv_pcs_id.';'."\n\n";



  $str.="\n\n?>";

  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);
  echo '<br><a href="de_server_einstellungen.php">Die Daten wurden gespeichert.<br>Zur Aktualisierung der Anzeige hier klicken.</a>';
  die('</body></html>');
}
?>
<?php
echo '<br><b>Servereinstellungen<b><br><br>';
echo '<table cellpadding="3" cellspacing="4">';
echo '<tr><td><b>Art</b></td> <td><b>Wert</b></td><td><b>Info</b></td></tr>';
echo '<tr><td>Gewinnpunktzahl</td> <td><input type="text" name="gewinnpunktzahl" value="'.$sv_winscore.'"></td><td>Die Punktzahl die erreicht werden muß um die Runde zu gewinnen.</td></tr>';
echo '<tr><td>Gewinnhaltezeit</td> <td><input type="text" name="gewinnhaltezeit" value="'.$sv_benticks.'"></td><td>Anzahl der Wirtschaftsticks, die nötig sind um Erhabener zu werden.</td></tr>';

echo '<tr><td>Max. Sektor</td> <td><input type="text" name="maxsec" value="'.$sv_maxsector.'"></td><td>Anzahl der Sektoren, auf welche die Spieler verteilt werden.<br>Standard: 20</td></tr>';
echo '<tr><td>Max. System</td> <td><input type="text" name="maxsys" value="'.$sv_maxsystem.'"></td><td>Maximalanzahl der Spieler pro Sektor.<br>Standard: 10</td></tr>';

echo '<tr><td>Inaktivenlöschzeit</td> <td><input type="text" name="inaktdeltime" value="'.$sv_inactiv_deldays.'"></td><td>Die Zeit nach der inaktive Spieler gelöscht werden.<br>Standard: 4</td></tr>';

echo '<tr><td>Angriffsgrenze</td> <td><input type="text" name="attgrenze" value="'.($sv_attgrenze*100).'"></td><td>Bis wieviel Prozent der eigenen Punkte kann man angreifen.<br>Standard: 60</td></tr>';
echo '<tr><td>Angriffsgrenze WHG-Bonus</td> <td><input type="text" name="attgrenzewhg" value="'.($sv_attgrenze_whg_bonus*100).'"></td><td>Durch den Bau der Weltraumhandelsgilde kann sich die Angriffsgrenze weiter senken, dieser hier angegebene Wert verringert die Angriffsgrenze.<br>Standard: 20<br>Angriffsgrenze (60) - Angriffsgrenze WHG-Bonus (20) = Endangriffsgrenze (40)</td></tr>';

echo '<tr><td>Rausvotegrenze</td> <td><input type="text" name="votegrenze" value="'.$sv_voteoutgrenze.'"></td><td>Prozentzahl der nötigen Stimmen um einen Spieler aus dem Sektor zu voten.<br>Standard: 60</td></tr>';

echo '<tr><td>Max. Sphärensprünge</td> <td><input type="text" name="maxsecmoves" value="'.$sv_max_secmoves.'"></td><td>Anzahl der Umzüge welche der Spieler mit der Unendlichkeitssphäre durchführen darf. Der Wert 0 deaktiviert die Sphäre.<br>Standard: 0</td></tr>';
echo '<tr><td>Min. Sphärenmitglieder</td> <td><input type="text" name="minsecmovesmember" value="'.$sv_min_user_per_regsector.'"></td><td>Minimalanzahl der Mitglieder beim Sphärenumzug pro Sektor.<br>Standard: 4</td></tr>';
echo '<tr><td>Max. Sphärenmitglieder</td> <td><input type="text" name="maxsecmovesmember" value="'.$sv_max_user_per_regsector.'"></td><td>Maximalanzahl der Mitglieder beim Sphärenumzug pro Sektor.<br>Standard: 6</td></tr>';

//echo '<tr><td>Servertag</td> <td><input type="text" name="servertag" value="'.$sv_server_tag.'"></td><td>1</td></tr>';
echo '<tr><td>Servername</td> <td><input type="text" name="servername" value="'.$sv_server_name.'"></td><td>Der gewählte Name für den Server, z.B. Orion Centauri</td></tr>';

echo '<tr><td>Planetarer Schildbonus</td> <td><input type="text" name="schildbonus" value="'.$sv_ps_bonus.'"></td><td>Gibt die Stärke des Planetaren Schildes in Prozent an. Die Prozentzahl der Türme überlebt einen Angriff.<br>Standard: 10</td></tr>';

echo '<tr><td>Recyclotronertrag</td> <td><input type="text" name="recyclotron" value="'.$sv_recyclotron_bonus.'"></td><td>Größe des recyclebaren Materials in Prozent.<br>Standard: 15</td></tr>';
echo '<tr><td>Recyclotronertrag mit WHG</td> <td><input type="text" name="recyclotronwhg" value="'.$sv_recyclotron_bonus_whg.'"></td><td>Größe des Recyclebaren Materials wenn die WHG vorhanden ist.<br>Standard: 30</td></tr>';

echo '<tr><td>Kollektorklaurate</td> <td><input type="text" name="klaurate" value="'.($sv_kollie_klaurate*100).'"></td><td>Gibt an, wieviel Prozent der Kollektoren pro Angriff gestohlen werden können.<br>Standard: 15</td></tr>';

echo '<tr><td>Kollektorertrag</td> <td><input type="text" name="kollieertrag" value="'.$sv_kollieertrag.'"></td><td>Energieertrag pro Kollektor.<br>Standard: 100</td></tr>';
echo '<tr><td>Kollektorertrag PA</td> <td><input type="text" name="kollieertragpa" value="'.$sv_kollieertrag_pa.'"></td><td>Energieertrag pro Kollektor bei einem Premium-Account.<br>Standard: 100</td></tr>';

echo '<tr><td>Planetarer Grundertrag</td> <td>
<input type="text" name="pga1" value="'.$sv_plan_grundertrag[0].'">M<br>
<input type="text" name="pga2" value="'.$sv_plan_grundertrag[1].'">D<br>
<input type="text" name="pga3" value="'.$sv_plan_grundertrag[2].'">I<br>
<input type="text" name="pga4" value="'.$sv_plan_grundertrag[3].'">E</td><td>Der Rohstoffertrag den jeder Account pro Wirtschaftstick bekommt.<br>Standard: 1000/100/0/0</td></tr>';

echo '<tr><td>Planetarer Grundertrag mit WHG</td> <td>
<input type="text" name="pga1whg" value="'.$sv_plan_grundertrag_whg[0].'">M<br>
<input type="text" name="pga2whg" value="'.$sv_plan_grundertrag_whg[1].'">D<br>
<input type="text" name="pga3whg" value="'.$sv_plan_grundertrag_whg[2].'">I<br>
<input type="text" name="pga4whg" value="'.$sv_plan_grundertrag_whg[3].'">E</td><td>Der Rohstoffertrag den jeder Account pro Wirtschaftstick bekommt, sobald die WHG vorhanden ist.<br>Standard: 4000/500/100/50</td></tr>';


echo '<tr><td>Wahrscheinlichkeit für Tronicverteilung</td> <td><input type="text" name="tronicw" value="'.$sv_globalw_tronic.'"></td><td>Der Prozentsatz mit dessen Wahrscheinlichkeit man Tronic bekommt.<br>Standard: 15</td></tr>';
echo '<tr><td>Wahrscheinlichkeit für Zufallsevent</td> <td><input type="text" name="zufallw" value="'.$sv_globalw_zufall.'"></td><td>Der Prozentsatz mit dessen Wahrscheinlichkeit ein Zufallsereignis stattfindet bekommt.<br>Standard: 15</td></tr>';
echo '<tr><td>Tickgrenze Zufallsevent</td> <td><input type="text" name="zufallstart" value="'.$sv_global_start_zufall.'"></td><td>Anzahl der Wirtschaftsticks ab denen die Zufallsergnisse beginnen.<br>Standard: 2000</td></tr>';
echo '<tr><td>Artefaktgrenze</td> <td><input type="text" name="artefaktstart" value="'.$sv_artefaktstart.'"></td><td>Anzahl der Sektorraumbasen, die vorhanden sein müssen, damit die Artefaktverteilung beginnt.<br>Standard: 10</td></tr>';

echo '<tr><td>Creditausschüttung</td> <td><input type="text" name="activetime" value="'.$sv_activetime.'"></td><td>Gibt an, alle wieviel Sekunden man durch Aktivität ein Credit bekommt.<br>Standard: 3600</td></tr>';
echo '<tr><td>Max. Diplomatieartefakte</td> <td><input type="text" name="maxdartefakt" value="'.$sv_max_dartefakt.'"></td><td>Gibt an, wieviele Diplomatieartefakte man maximal haben kann.<br>Standard: 3</td></tr>';
echo '<tr><td>Max. Palenium</td> <td><input type="text" name="maxpalenium" value="'.$sv_max_palenium.'"></td><td>Gibt an, wieviel Palenium man maximal haben kann.<br>Standard: 400</td></tr>';

echo '<tr><td>Kriegsartefaktpunkte</td> <td><input type="text" name="kartepunkte" value="'.($sv_kartepunkte*100).'"></td><td>Gibt an, bei wievielen Punkten Flottenverlust man ein Kriegsartefakt bekommt.<br>Standard: 500000</td></tr>';

echo '</table>';
echo '<br><br><input type="Submit" name="savedata" value="Einstellungen speichern"><br><br><br>';

function validDigit($digit) {
    $isavalid = 1;
    for ($i=0; $i<strlen($digit); $i++)
    {
      if (!ereg("[0-9]",$digit[$i]))
      {
        $isavalid = 0;
        break;
      }
    }
    if($digit=='')$isavalid=0;
    //echo $isavalid;
    return($isavalid);
}
?>
</form>
</body>
</html>
