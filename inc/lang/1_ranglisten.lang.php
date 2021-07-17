<?php
$ranglisten_lang['title']='Die Ewigen Ranglisten';
$ranglisten_lang['spieler'] = 'Spieler';
$ranglisten_lang['sektor'] = 'Sektor';
$ranglisten_lang['allianz'] = 'Allianz';
$ranglisten_lang['cyborg'] = 'Cyborg';
$ranglisten_lang['platz'] = 'Platz';
$ranglisten_lang['rang'] = 'Rang';
$ranglisten_lang['spielername'] = 'Spielername';
$ranglisten_lang['punkte'] = 'Punkte';
$ranglisten_lang['rangnamen']=array('Der Erhabene', 'Alpha','Beta','Gamma','Delta','Epsilon','Zeta','Eta','Theta','Iota','Kappa','Lambda','My','Ny','Xi','Omikron','Pi','Rho','Sigma','Tau','Ypsilon','Phi','Chi','Psi','Omega');
$ranglisten_lang['mitglieder'] = 'Mitglieder';
$ranglisten_lang['schnitt'] = 'Schnitt';
$ranglisten_lang['kollektoren'] = 'Kollektoren';
$ranglisten_lang['name'] = 'Name';
$ranglisten_lang['level'] = 'Level';
$ranglisten_lang['questpunkte'] = 'Questpunkte';
$ranglisten_lang['stand'] = 'Stand';
$ranglisten_lang['download'] = 'Hier klicken um die kompletten Rohdaten <br>(gepackt im gzip Format) herunterzuladen';
$ranglisten_lang['faq'] = 'FAQ zu den Daten';
$ranglisten_lang['faq_text'] = '<br><br><br><br>
<table border='0' cellpadding='0' cellspacing='0'>

<tr height='37' align='center'>
 <td width='13' height='37' class='rol'>&nbsp;</td>
 <td width='600' class='ro'>FAQ zu den Daten</td>
 <td width='13' class='ror'>&nbsp;</td>
</tr>

<tr>
  <td width='13' class='rl'>&nbsp;</td>
  <td>
    <table border='0' cellpadding='5' cellspacing='2' width='100%'>
      <tr class='cc'>
         <td>Wie liegen die Dateien vor?</td>
         <td align='left'>Sie liegen gepackt in gzip-Dateien vor.</td>
      </tr>
      <tr class='cc'>
         <td>Gzip? Wie öffne ich das mit PHP?</td>
         <td align='left'>ganz einfach:<br><br>
             &#60;&#63;<br>
             $dateiname = 'spieler.txt.gz';<br>
             $daten = gzopen($dateiname, 'r');<br>
             if ($daten)<br>
             {<br>
             &nbsp;&nbsp;&nbsp;&nbsp;while (!gzeof($daten))<br>
             &nbsp;&nbsp;&nbsp;&nbsp;{<br>
             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo gzgets($daten, 4096);<br>
             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/*Dein Script das die Daten verarbeitet*/ <br>
             &nbsp;&nbsp;&nbsp;&nbsp;}<br>
             }<br>
             gzclose($daten);<br>
             ?><br>
         </td>
      </tr>
      <tr class='cc'>
         <td>Wie oft werden die Daten aktualisiert?</td>
         <td align='left'>Die Daten werden jeweils um:<br><br>
         &nbsp;&nbsp;Spieler: 10 Uhr & 22 Uhr<br>
         &nbsp;&nbsp;Sektor: 10 Uhr & 22 Uhr<br>
         &nbsp;&nbsp;Allianz: 2 Uhr & 6 Uhr & 10 Uhr & 14 Uhr & 18 Uhr & 22 Uhr<br>
         &nbsp;&nbsp;Cyborg: 10 Uhr & 22 Uhr<br><br>
         aktualisiert.<br>
         </td>
      </tr>
      <tr class='cc'>
         <td>Wie sind die Dateien aufgebaut?</td>
         <td align='left'>In JEDER Datei steht an erster stelle die
         Anzahl der Datensätze,<br>damit ihr die Daten (je nach Wissenstand)
         leichter verarbeiten könnt<br><br>
         Der Aufbau der einzelnen Daten sieht wie folgt aus:<br><br>
         <font color='FF0000'>spieler.txt</font><br>
         <b>Platz:</b> Platz in der Spieler-Rangliste<br>
         <b>RangNummer:</b> 1=Alpha, 2=Beta, ... <br>
         <b>Punkte:</b> die Punkte des Spielers<br>
         <b>Tendenz:</b> +hoch -runter #unverändert<br>
         <b>Plätze:</b> Anzahl der Plätze um die man gestiegen oder gefallen ist.<br>
         <b>Spielername:</b> der Spielername<br><br>

         <b>Platz|RangNummer|Punkte|Tendenz~Plätze|Spielername</b><br><br><br>


         <font color='FF0000'>sektor.txt:</font><br>
         <b>Platz:</b> Platz in der Sektor-Rangliste<br>
         <b>Sektor:</b> die Sektornummer<br>
         <b>Punkte:</b> die Punkte des Sektors<br>
         <b>Tendenz:</b> +hoch -runter #unverändert<br>
         <b>Plätze:</b> Anzahl der Plätze um die man gestiegen oder gefallen ist.<br>
         <b>Sektorname:</b> der Sektorname<br><br>

         <b>Platz|Sektor|Punkte|Tendenz~Plätze|Sektorname</b><br><br><br>


         <font color='FF0000'>alli.txt:</font><br><br>
         Ist eigentlich selbsterklärend. Sobald C.A.R.S verfübar ist,<br>
         wir es auch ne Tendenz für die Allianzen geben<br><br>
         <b>Platz|Mitglieder|Punkte|Punkteschnitt|Kollektoren|Kollektorschnitt|Allianz-Tag </b><br><br><br>

         <font color='FF0000'>cyborg.txt:</font><br><br>
         Ist auch selbsterklärend. <br><br>

         <b>Platz|Level|Questpunkte|Erfahrung|Spielername</b><br><br><br>
         </td>
      </tr>
      <tr class='cc'>
         <td>Wo ist die Rangliste für den Handel?</td>
         <td align='left'>
         Sie kommt sobald der Schiffshandel fertig<br>
         ist und die Handelspunkte integriert sind!</td>
      </tr>
</table>
</td>
<td width='13' class='rr'>&nbsp;</td>
</tr>
<tr height='20'>
<td height='20' class='rul' width='13'>&nbsp;</td>
<td class='ru'>&nbsp;</td>
<td class='rur' width='13'>&nbsp;</td>
</tr>
</table>';
?>