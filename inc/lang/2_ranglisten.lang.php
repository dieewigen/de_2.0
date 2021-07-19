<?php
$ranglisten_lang['title']='Die Ewigen ranking list';
$ranglisten_lang['spieler'] = 'Player';
$ranglisten_lang['sektor'] = 'Sector';
$ranglisten_lang['allianz'] = 'Alliance';
$ranglisten_lang['cyborg'] = 'Cyborg';
$ranglisten_lang['platz'] = 'Pos.';
$ranglisten_lang['rang'] = 'Rank';
$ranglisten_lang['spielername'] = 'Player name';
$ranglisten_lang['punkte'] = 'Score';
$ranglisten_lang['rangnamen']=array('The Raisen One', 'Alpha','Beta','Gamma','Delta','Epsilon','Zeta','Eta','Theta','Iota','Kappa','Lambda','My','Ny','Xi','Omikron','Pi','Rho','Sigma','Tau','Ypsilon','Phi','Chi','Psi','Omega');
$ranglisten_lang['mitglieder'] = 'Member';
$ranglisten_lang['schnitt'] = 'Average';
$ranglisten_lang['kollektoren'] = 'Collectors';
$ranglisten_lang['name'] = 'Name';
$ranglisten_lang['level'] = 'Level';
$ranglisten_lang['questpunkte'] = 'Quest points';
$ranglisten_lang['stand'] = 'Last update';
$ranglisten_lang['download'] = 'Please click here to download the<br>raw data (archived in gzip format)';
$ranglisten_lang['faq'] = 'FAQ for the ranking lists';
$ranglisten_lang['faq_text'] = '<br><br><br><br>
<table border='0' cellpadding='0' cellspacing='0'>

<tr height='37' align='center'>
 <td width='13' height='37' class='rol'>&nbsp;</td>
 <td width='600' class='ro'>FAQ for the ranking lists</td>
 <td width='13' class='ror'>&nbsp;</td>
</tr>

<tr>
  <td width='13' class='rl'>&nbsp;</td>
  <td>
    <table border='0' cellpadding='5' cellspacing='2' width='100%'>
      <tr class='cc'>
         <td>What type is the data file?</td>
         <td align='left'>The data is packed in in gzip format.</td>
      </tr>
      <tr class='cc'>
         <td>Gzip? How to open the files with PHP?</td>
         <td align='left'>Very simple:<br><br>
             &#60;&#63;<br>
             $filename = 'spieler.txt.gz';<br>
             $data = gzopen($filename, 'r');<br>
             if ($data)<br>
             {<br>
             &nbsp;&nbsp;&nbsp;&nbsp;while (!gzeof($data))<br>
             &nbsp;&nbsp;&nbsp;&nbsp;{<br>
             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo gzgets($data, 4096);<br>
             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/*Your script that uses the extracted data*/ <br>
             &nbsp;&nbsp;&nbsp;&nbsp;}<br>
             }<br>
             gzclose($data);<br>
             ?><br>
         </td>
      </tr>
      <tr class='cc'>
         <td>How often will the files be updated?</td>
         <td align='left'>The files are updated every:<br><br>
         &nbsp;&nbsp;Player: 10 am & 10 pm<br>
         &nbsp;&nbsp;Sector: 10 am & 10 pm<br>
         &nbsp;&nbsp;Alliance: 2 am & 6 am & 10 am & 2 pm & 6 pm & 10 pm<br>
         &nbsp;&nbsp;Cyborg: 10 am & 10 pm<br><br>
         </td>
      </tr>
      <tr class='cc'>
         <td>How are the files structured?</td>
         <td align='left'>In EVERY file in first place the amount of datalines is listet
         <br>so that your script knows how many lines will follow<br><br>
         The structure of the files is as follows:<br><br>
         <font color='FF0000'>spieler.txt</font><br>
         <b>Pos.:</b> Position in player ranking list<br>
         <b>Rank number:</b> 1=Alpha, 2=Beta, ... <br>
         <b>Score:</b> the score of the player<br>
         <b>Tendency:</b> +up -down #unchanged<br>
         <b>Places:</b> Number if places that the player is up or down.<br>
         <b>Player name:</b> der Spielername<br><br>

         <b>Pos.|Rank_number|Score|Tendency~Places|Player_name</b><br><br><br>


         <font color='FF0000'>sektor.txt:</font><br>
         <b>Pos.:</b> Position in the sector ranking list<br>
         <b>Sector:</b> the sector number<br>
         <b>Score:</b> the score of the sector<br>
         <b>Tendency:</b> +up -down #unchanged<br>
         <b>Places:</b> Number if places that the sector is up or down.<br>
         <b>Sector name:</b> the name of the sector<br><br>

         <b>Pos.|Sector|Score|Tendency~Places|Sector_name</b><br><br><br>


         <font color='FF0000'>alli.txt:</font><br><br>
         The same as the lists above,<br>
         tendency is not yet available<br><br>
         <b>Pos.|Members|Score|Score_average|Collectors|Collector_average|Alliance_tag </b><br><br><br>

         <font color='FF0000'>cyborg.txt:</font><br><br>
         The same as the lists above. <br><br>

         <b>Pos.|Level|Quest_points|Experience|Playername</b><br><br><br>
         </td>
      </tr>
      <tr class='cc'>
         <td>Where is the trade ranking list?</td>
         <td align='left'>
         The trade ranking list is still under development<br>
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