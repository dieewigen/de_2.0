<?php
function showkampfberichtBG($data){
	$content='';

	$kb=unserialize(base64_decode($data));

	$content.='<div>BATTLEGROUND-ERGEBNIS</div>';
	

	for($runde=0;$runde<count($kb);$runde++){
		$content.='<div>Die <span style="color: #00FF00;">Gewinner</span> erhalten: '.$kb[$runde][0]['gewinn'].'</div><br>';

		//die einzelnen Paare durchgehen
		for($p=0;$p<count($kb[$runde]);$p++){
			

			if($kb[$runde][$p]['winner_user_id']==$kb[$runde][$p]['user_id1']){
				$css1='color: #00FF00;';
				$css2='color: #FF0000;';
			}else{
				$css1='color: #FF0000;';
				$css2='color: #00FF00;';
			}

			//echo 'A: '.$kb[$runde][$p]['spielername1'].'/'.$_SESSION['ums_spielername'];
			

			//eigenen Namen in fett
			//Zeile in der man selbst ist hervorheben
			$bg_color='none';
			$parts=explode("&nbsp;",$kb[$runde][$p]['spielername1']);
			if($parts[0]==($_SESSION['ums_spielername'] ?? '') && !empty($parts[0])){
				$css1.='font-weight: bold;';
				$bg_color='#222222';
			}
			
			$parts=explode("&nbsp;",$kb[$runde][$p]['spielername2']);
			if($parts[0]==($_SESSION['ums_spielername'] ?? '') && !empty($parts[0])){
				$css2.='font-weight: bold;';
				$bg_color='#222222';
			}

			

			$content.='<div style="display: flex; background-color: '.$bg_color.'">';

			$content.='<div style="width: 260px; text-align: center;'.$css1.'">'.$kb[$runde][$p]['spielername1'].'</div>';
			if(!empty($kb[$runde][$p]['spielername2'])){
				$content.='<div style="flex-grow: 1; text-align: center;">:</div>';
				$content.='<div style="width: 260px; text-align: center;'.$css2.'">'.$kb[$runde][$p]['spielername2'].'</div>';
			}

			$content.='</div>';
		}

	}

	//$content.=print_r($kb, true);

	return $content;
}

function showkampfberichtV0($text,$rasse, $spielername, $sector, $system, $schiffspunkte){
	global $sv_anz_rassen, $sv_anz_schiffe, $sv_anz_tuerme, $sv_server_lang;

	$rassenklassen[0] = array ('k1', 'k2');
	$rassenklassen[1] = array ('k3', 'k4');
	$rassenklassen[2] = array ('k5', 'k6');
	$rassenklassen[3] = array ('k7', 'k8');
	$rassenklassen[4] = array ('k9', 'k10');

	//kb in seine bestandteile zerlegen
	$kbd=explode(";",$text);

	//daten aus dem kb holen
	$grundindex=284;
	$kkollies=$kbd[$grundindex];
	$ksec=$kbd[$grundindex+1];
	$ksys=$kbd[$grundindex+2];
	$krassenvorhanden[0]=$kbd[$grundindex+3];
	$krassenvorhanden[1]=$kbd[$grundindex+4];
	$krassenvorhanden[2]=$kbd[$grundindex+5];
	$krassenvorhanden[3]=$kbd[$grundindex+6];
	$krassenvorhanden[4]=$kbd[$grundindex+7];
	$kturmrasse=$kbd[$grundindex+8];
	$atterliste=$kbd[$grundindex+9];
	$defferliste=$kbd[$grundindex+10];

	$grundindex=279;
	$kollieserbeutet=$kbd[$grundindex+0];
	$exp=$kbd[$grundindex+1];
	$kartefakte=$kbd[$grundindex+2];
	$srec1=$kbd[$grundindex+3];
	$srec2=$kbd[$grundindex+4];

	//eigenen spielername fett darstellen
	if($_SESSION['ums_rasse']==1)$rflag='E';
	elseif($_SESSION['ums_rasse']==2)$rflag='I';
	elseif($_SESSION['ums_rasse']==3)$rflag='K';
	elseif($_SESSION['ums_rasse']==4)$rflag='Z';
	elseif($_SESSION['ums_rasse']==5)$rflag='D';


	$username=$_SESSION['ums_spielername'].' ['.$rflag.']('.$sector.':'.$system.')';
	$atterliste=str_replace($username, '<b>'.$username.'</b>', $atterliste);
	$defferliste=str_replace($username, '<b>'.$username.'</b>', $defferliste);

	$exp=number_format($exp, 0,"",".");

	//sprachdatei einbinden
	unset($kbl_lang);
	include 'inc/lang/'.$sv_server_lang.'_kampfbericht.lib.lang.php';

	//meldung f�r kollies zusammenbauen
	if ($kkollies==-1)$kolliesatz=$kbl_lang['abgewehrt'];
	else
	{
	  if ($kkollies==1)$kolliesatz=$kbl_lang['deffercollosts']; else $kolliesatz=$kbl_lang['deffercollostp'];

	  if ($kollieserbeutet>0)
	  {
		if ($kollieserbeutet==1)$kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwins']); 
		else $kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinp']);
	  }
	  elseif($kollieserbeutet<0)
	  {
		$kollieserbeutet=$kollieserbeutet*(-1);
		if ($kollieserbeutet==1)$kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinsdestroy']); 
		else $kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinpdestroy']);
	  }
	  //$kolliesatz=$kbl_lang[deffercollost];
	  //$kolliesatz.=$kbl_lang[attercolwin];
	}

	//zuerst den header
	$kbstring='
		ALT<br>
  <table cellSpacing=0 cellPadding=2 width=555 border=1>
  <tr align="center">
  <td class="k1" width="15%"><b>'.$kbl_lang['angreifer'].':</b></td>
  <td class="k1" width="85%">'.$atterliste.'</td>
  </tr>
  <tr align="center">
  <td class="k1""><b>'.$kbl_lang['verteidiger'].':</b></td>
  <td class="k1"">'.$defferliste.'</td>
  </tr>
  <tr align="center">
  <td class="k1" colspan="2" align="left"><b>'.$kolliesatz.
  '<br>'.$kbl_lang['admiralexp'].'<br>'.
  $kbl_lang['erhaltenekartefakt'].': '.$kartefakte.'<br>'.
  $rassennamen[$rasse-1][4].'-'.$kbl_lang['recycling'].': '.number_format((double)$srec1, 0,"",".").' M - '.number_format((double)$srec2, 0,"",".").' D</b></td>
  </tr>
  </table>
  <br>
  <TABLE cellSpacing=0 cellPadding=2 width=555 border=1>
  <tr align="center">
  <td class="k1" width="14%"><b>'.$ksec.':'.$ksys.'</font></b></td>
  <td class="k1" width="28%" colSpan=3><u>'.$kbl_lang['angreifer'].'</u></td>
  <td class="k1" width="28%" colSpan=3><u>'.$kbl_lang['verteidiger'].'</u></td>
  <td class="k1" width="30%" colSpan=3><u>'.$kbl_lang['eigene'].'</u></td>
  </tr>
  <tr align="center" width="14%">
  <td class="k2">'.$kbl_lang['einheit'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  </tr>';

  $geseinheiten_atter_anz=0;
  $geseinheiten_deffer_anz=0;
  $geseinheiten_atter_anz_verloren=0;
  $geseinheiten_deffer_anz_verloren=0;
  $geseinheiten_atter_score=0;
  $geseinheiten_deffer_score=0;
  $geseinheiten_atter_score_lost=0;
  $geseinheiten_deffer_score_lost=0;

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  //schiffe
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
	$grundindex1=0;
	for ($aktrasse=0;$aktrasse<$sv_anz_rassen;$aktrasse++)
	{
	  if ($krassenvorhanden[$aktrasse]>0)
	  {
		$schiffsnamen = $rassennamen[$aktrasse];
		$c1=0;$c2=0;
		for ($i=0;$i<$sv_anz_schiffe;$i++)
		{
		  if ($schiffsnamen[$i]!='NA')
		  {
			if ($c1==0){$c1=1; $klasse=$rassenklassen[$aktrasse][0];}
			else {$c1=0; $klasse=$rassenklassen[$aktrasse][1];}

			$grundindex2=$grundindex1+40;
			$grundindex3=$grundindex2+40;
			$grundindex4=$grundindex3+40;
			$grundindex5=$grundindex4+40;
			$grundindex6=$grundindex5+40;

			if ($rasse-1!=$aktrasse)//wenn es nicht die eigene rasse ist leer lassen
			{
			  // Platzhalter dynamisch auf Anzahl der Schiffe setzen (vorher nur 8, führte zu Undefined array key Warnungen bei Index >=8)
			  $keigene1 = array_fill(0, $sv_anz_schiffe, '&nbsp;');
			  $keigene2 = $keigene1;
			  $keigene3 = $keigene1;
			}
			else //ansonsten variablen in die array packen
			{
			  $grundindex=255;
			  for ($j=0;$j<$sv_anz_schiffe;$j++)
			  $keigene1[$j] = number_format($kbd[$grundindex+$j], 0,"",".");

			  for ($j=0;$j<$sv_anz_schiffe;$j++)
			  $keigene2[$j] = number_format($kbd[$grundindex+8+$j], 0,"",".");

			  for ($j=0;$j<$sv_anz_schiffe;$j++)
			  $keigene3[$j] = number_format($kbd[$grundindex+16+$j], 0,"",".");
			}

			$kbstring.='
  <TR align="center">
  <TD class="'.$klasse.'">'.$schiffsnamen[$i].'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex1+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex2+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex3+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex4+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex5+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex6+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.$keigene1[$i].'</TD>
  <TD class="'.$klasse.'">'.$keigene2[$i].'</TD>
  <TD class="'.$klasse.'">'.$keigene3[$i].'</TD>
  </TR>';
			//statistik erstellen
			$geseinheiten_atter_anz+=$kbd[$grundindex1+$i];
			$geseinheiten_deffer_anz+=$kbd[$grundindex4+$i];

			$geseinheiten_atter_anz_verloren+=($kbd[$grundindex1+$i]-$kbd[$grundindex3+$i]);
			$geseinheiten_deffer_anz_verloren+=($kbd[$grundindex4+$i]-$kbd[$grundindex6+$i]);

			$geseinheiten_atter_score+=$kbd[$grundindex1+$i]*$schiffspunkte[$aktrasse][$i];
			$geseinheiten_deffer_score+=$kbd[$grundindex4+$i]*$schiffspunkte[$aktrasse][$i];

			$geseinheiten_atter_score_lost+=($kbd[$grundindex1+$i]-$kbd[$grundindex3+$i])*$schiffspunkte[$aktrasse][$i];
			$geseinheiten_deffer_score_lost+=($kbd[$grundindex4+$i]-$kbd[$grundindex6+$i])*$schiffspunkte[$aktrasse][$i];

		  }
		}
	  }
	  $grundindex1=$grundindex1+8;
	}

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  //t�rme
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////

  $grundindex1=240;
  if (($kbd[$grundindex1]+$kbd[$grundindex1+1]+$kbd[$grundindex1+2]+$kbd[$grundindex1+3]+$kbd[$grundindex1+4])>0)
  {
  $kbstring.='
  <TR align="center">
  <TD class="k1" colSpan=10><u>'.$kbl_lang['tuerme'].'</u></TD>
  </TR>
  ';
	$aktrasse=$kturmrasse;

	$schiffsnamen = $turmnamen[$aktrasse];
	$c1=0;$c2=0;
	for ($i=0;$i<$sv_anz_tuerme;$i++)
	{
	  if ($schiffsnamen[$i]!='NA')
	  {
		if ($c1==0){$c1=1; $klasse=$rassenklassen[$aktrasse][0];}
		else {$c1=0; $klasse=$rassenklassen[$aktrasse][1];}

		$grundindex2=$grundindex1+5;
		$grundindex3=$grundindex2+5;

		$kbstring.='
  <TR align="center">
  <TD class="'.$klasse.'">'.$schiffsnamen[$i].'</TD>
  <TD class="'.$klasse.'">-</TD>
  <TD class="'.$klasse.'">-</TD>
  <TD class="'.$klasse.'">-</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex1+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex2+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">'.number_format($kbd[$grundindex3+$i], 0,"",".").'</TD>
  <TD class="'.$klasse.'">-</TD>
  <TD class="'.$klasse.'">-</TD>
  <TD class="'.$klasse.'">-</TD>
  </TR>';

		//statistik erstellen
		$geseinheiten_deffer_anz+=$kbd[$grundindex1+$i];
		$geseinheiten_deffer_anz_verloren+=($kbd[$grundindex1+$i]-$kbd[$grundindex3+$i]);

		$geseinheiten_deffer_score+=$kbd[$grundindex1+$i]*$schiffspunkte[$aktrasse][$i+8];
		$geseinheiten_deffer_score_lost+=($kbd[$grundindex1+$i]-$kbd[$grundindex3+$i])*$schiffspunkte[$aktrasse][$i+8];
	  }
	  //$grundindex1=$grundindex1+5;
	}
  }

  $kbstring.=
  '</TABLE>';
  //////////////////////////////////////////////////////
  //////////////////////////////////////////////////////
  //  statistik
  //////////////////////////////////////////////////////
  //////////////////////////////////////////////////////
  $kbstring.='
  <br><table cellSpacing=0 cellPadding=2 width=555 border=1>
  <tr align="center"><td colspan="4" class="k1"><b>'.$kbl_lang['statistik'].'</b></td></tr>
  <tr align="center">
  <td  class="k2"><b>'.$kbl_lang['typ'].'</b></td>
  <td  class="k2"><b>'.$kbl_lang['angreifer'].'</b></td>
  <td  class="k2"><b>'.$kbl_lang['verteidiger'].'</b></td>
  <td  class="k2"><b>'.$kbl_lang['verhaeltnis'].'</b></td>
  </tr>';

  if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
  //das verh�ltnis berechnen
  if ($geseinheiten_atter_anz>0)
  $verhaeltnis=$geseinheiten_deffer_anz/$geseinheiten_atter_anz;
  else $verhaeltnis=0;
  $kbstring.='
  <tr align="center">
  <td  class="'.$bg.'">'.$kbl_lang['einheitenanzahl'].'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_atter_anz, 0,"",".").'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_deffer_anz, 0,"",".").'</td>
  <td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
  </tr>';

  if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
  //das verh�ltnis berechnen
  if ($geseinheiten_atter_anz_verloren>0)
  $verhaeltnis=$geseinheiten_deffer_anz_verloren/$geseinheiten_atter_anz_verloren;
  else $verhaeltnis=0;
  //den prozentwert berechnen
  $prozent_atter=$geseinheiten_atter_anz_verloren*100/$geseinheiten_atter_anz;
  if($geseinheiten_deffer_anz>0)$prozent_deffer=$geseinheiten_deffer_anz_verloren*100/$geseinheiten_deffer_anz;
  else $prozent_deffer=0;
  $kbstring.='
  <tr align="center">
  <td  class="'.$bg.'">'.$kbl_lang['verloreneeinheiten'].'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_atter_anz_verloren, 0,"",".").' ('.number_format($prozent_atter, 2,",",".").'%)</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_deffer_anz_verloren, 0,"",".").' ('.number_format($prozent_deffer, 2,",",".").'%)</td>
  <td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
  </tr>';
  if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
  //das verh�ltnis berechnen
  if ($geseinheiten_atter_score>0)
  $verhaeltnis=$geseinheiten_deffer_score/$geseinheiten_atter_score;
  else $verhaeltnis=0;
  $kbstring.='
  <tr align="center">
  <td  class="'.$bg.'">'.$kbl_lang['einheitenpunktewert'].'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_atter_score, 0,"",".").'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_deffer_score, 0,"",".").'</td>
  <td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
  </tr>';

  if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
  //das verh�ltnis berechnen
  if ($geseinheiten_atter_score_lost>0)
  $verhaeltnis=$geseinheiten_deffer_score_lost/$geseinheiten_atter_score_lost;
  else $verhaeltnis=0;
  //den prozentwert berechnen
  $prozent_atter=$geseinheiten_atter_score_lost*100/$geseinheiten_atter_score;
  if($geseinheiten_deffer_score>0)$prozent_deffer=$geseinheiten_deffer_score_lost*100/$geseinheiten_deffer_score;
  else $prozent_deffer=0;
  $kbstring.='
  <tr align="center">
  <td  class="'.$bg.'">'.$kbl_lang['verlorenepunkte'].'</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_atter_score_lost, 0,"",".").' ('.number_format($prozent_atter, 2,",",".").'%)</td>
  <td  class="'.$bg.'">'.number_format($geseinheiten_deffer_score_lost, 0,"",".").' ('.number_format($prozent_deffer, 2,",",".").'%)</td>
  <td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
  </tr>';

  $kbstring.='</table>&nbsp;</CENTER>';

	return($kbstring);
}

function showkampfberichtV1($text,$rasse, $spielername, $sector, $system, $schiffspunkte){

	global $sv_anz_rassen, $sv_anz_schiffe, $sv_anz_tuerme, $sv_server_lang, $sv_oscar;

	$rassenklassen[0] = array ('k1', 'k2');
	$rassenklassen[1] = array ('k3', 'k4');
	$rassenklassen[2] = array ('k5', 'k6');
	$rassenklassen[3] = array ('k7', 'k8');
	$rassenklassen[4] = array ('k9', 'k10');

	//kb in seine bestandteile zerlegen
	$kbd=unserialize($text);
	//print_r($kbd);

	//daten aus dem kb holen
	$kkollies=$kbd['daten']['colstolen'];
	$ksec=$kbd['daten']['sector'];;
	$ksys=$kbd['daten']['system'];;
	$krassenvorhanden[0]=$kbd['daten']['rassen'][0];
	$krassenvorhanden[1]=$kbd['daten']['rassen'][1];
	$krassenvorhanden[2]=$kbd['daten']['rassen'][2];
	$krassenvorhanden[3]=$kbd['daten']['rassen'][3];
	$krassenvorhanden[4]=$kbd['daten']['rassen'][4];
	$kturmrasse=$kbd['daten']['target_rasse'];
	$atterliste=$kbd['daten']['atterliste'];
	$defferliste=$kbd['daten']['defferliste'];

	// Sicherer Zugriff auf Spieler-Daten, Defaults verhindern PHP-Warnungen bei fehlenden Keys
	$daten_spieler = [];
	if(isset($kbd['daten_spieler']) && is_array($kbd['daten_spieler'])){
		$daten_spieler = $kbd['daten_spieler'];
	}
	$kollieserbeutet = isset($daten_spieler['colstolen']) ? (int)$daten_spieler['colstolen'] : 0;
	$exp            = isset($daten_spieler['exp']) ? (int)$daten_spieler['exp'] : 0;
	$kartefakte     = isset($daten_spieler['kartefakt']) ? (int)$daten_spieler['kartefakt'] : 0;
	$srec1          = isset($daten_spieler['recycling1']) ? (int)$daten_spieler['recycling1'] : 0;
	$srec2          = isset($daten_spieler['recycling2']) ? (int)$daten_spieler['recycling2'] : 0;
	$kg_set_01      = isset($daten_spieler['kg_set_01']) ? (int)$daten_spieler['kg_set_01'] : 0;
	$kg_set_02      = isset($daten_spieler['kg_set_02']) ? (int)$daten_spieler['kg_set_02'] : 0;
	$kg_set_03      = isset($daten_spieler['kg_set_03']) ? (int)$daten_spieler['kg_set_03'] : 0;
	$kg_set_04      = isset($daten_spieler['kg_set_04']) ? (int)$daten_spieler['kg_set_04'] : 0;

	//eigenen spielername fett darstellen
	if(!isset($_SESSION['ums_rasse']) || $_SESSION['ums_rasse']==1)$rflag='E';
	elseif($_SESSION['ums_rasse']==2)$rflag='I';
	elseif($_SESSION['ums_rasse']==3)$rflag='K';
	elseif($_SESSION['ums_rasse']==4)$rflag='Z';
	elseif($_SESSION['ums_rasse']==5)$rflag='D';


	$username=($_SESSION['ums_spielername'] ?? '').' ['.$rflag.']('.$sector.':'.$system.')';
	$atterliste=str_replace($username, '<b>'.$username.'</b>', $atterliste);
	$defferliste=str_replace($username, '<b>'.$username.'</b>', $defferliste);

	$exp=number_format($exp, 0,"",".");

	//sprachdatei einbinden
	unset($kbl_lang);
	include 'inc/lang/'.$sv_server_lang.'_kampfbericht.lib.lang.php';

	//meldung f�r kollies zusammenbauen
	if ($kkollies==-1){
		$kolliesatz=$kbl_lang['abgewehrt'];
	}else{
		if ($kkollies==1)$kolliesatz=$kbl_lang['deffercollosts']; else $kolliesatz=$kbl_lang['deffercollostp'];

		if ($kollieserbeutet>0)
		{
		  if ($kollieserbeutet==1)$kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwins']); 
		  else $kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinp']);
		}
		elseif($kollieserbeutet<0)
		{
		  $kollieserbeutet=$kollieserbeutet*(-1);
		  if ($kollieserbeutet==1)$kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinsdestroy']); 
		  else $kolliesatz.=str_replace("{WERT1}", $kollieserbeutet,$kbl_lang['attercolwinpdestroy']);
		}
		//$kolliesatz=$kbl_lang[deffercollost];
		//$kolliesatz.=$kbl_lang[attercolwin];
	}

	//Kopfgeldhinweis
	//auf den Atter ausgesetzt
	$kg_set_hinweis='';
	if($sv_oscar!=1){
		if($kg_set_01>0 || $kg_set_02>0 || $kg_set_03>0 || $kg_set_04>0){
			$kg_set_hinweis='<br><b>Ausgesetztes Kopfgeld: '
				.number_format((double)$kg_set_01, 0,"","."). ' M '
				.number_format((double)$kg_set_02, 0,"","."). ' D '
				.number_format((double)$kg_set_03, 0,"","."). ' I '
				.number_format((double)$kg_set_04, 0,"","."). ' E</b>';
		}
	}
	
	//zuerst den header
	$kbstring='
  <table cellSpacing=0 cellPadding=2 width=555 border=1>
  <tr align="center">
  <td class="k1" width="15%"><b>'.$kbl_lang['angreifer'].':</b></td>
  <td class="k1" width="85%">'.$atterliste.'</td>
  </tr>
  <tr align="center">
  <td class="k1"><b>'.$kbl_lang['verteidiger'].':</b></td>
  <td class="k1">'.$defferliste.'</td>
  </tr>
  <tr align="center">
	<td class="k1" colspan="2" align="left"><b>'.$kolliesatz.
	'<br>'.$kbl_lang['admiralexp'].'<br>'.
	$kbl_lang['erhaltenekartefakt'].': '.$kartefakte.'<br>'.
	$rassennamen[$rasse-1][4].'-'.$kbl_lang['recycling'].': '.number_format((double)$srec1, 0,"",".").' M - '.number_format((double)$srec2, 0,"",".").' D</b>
	'.$kg_set_hinweis.'  
	  </td>
  </tr>
  </table>
  <br>
  <TABLE cellSpacing=0 cellPadding=2 width=555 border=1>
  <tr align="center">
  <td class="k1" width="14%"><b>'.$ksec.':'.$ksys.'</font></b></td>
  <td class="k1" width="28%" colSpan=3><u>'.$kbl_lang['angreifer'].'</u></td>
  <td class="k1" width="28%" colSpan=3><u>'.$kbl_lang['verteidiger'].'</u></td>
  <td class="k1" width="30%" colSpan=3><u>'.$kbl_lang['eigene'].'</u></td>
  </tr>
  <tr align="center" width="14%">
  <td class="k2">'.$kbl_lang['einheit'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  <td class="k2">'.$kbl_lang['eingesetzt'].'</td>
  <td class="k2">'.$kbl_lang['geblockt'].'</td>
  <td class="k2">'.$kbl_lang['ueberlebt'].'</td>
  </tr>';

  $geseinheiten_atter_anz=0;
  $geseinheiten_deffer_anz=0;
  $geseinheiten_atter_anz_verloren=0;
  $geseinheiten_deffer_anz_verloren=0;
  $geseinheiten_atter_score=0;
  $geseinheiten_deffer_score=0;
  $geseinheiten_atter_score_lost=0;
  $geseinheiten_deffer_score_lost=0;

	////////////////////////////////////////////////
	////////////////////////////////////////////////
	//schiffe
	////////////////////////////////////////////////
	////////////////////////////////////////////////  
	for ($aktrasse=0;$aktrasse<$sv_anz_rassen;$aktrasse++){
		if ($krassenvorhanden[$aktrasse]>0){
			$schiffsnamen = $rassennamen[$aktrasse];
			$c1=0;$c2=0;
			for ($i=0;$i<$sv_anz_schiffe;$i++){
			  if ($schiffsnamen[$i]!='NA'){
					if ($c1==0){$c1=1; $klasse=$rassenklassen[$aktrasse][0];}
					else {$c1=0; $klasse=$rassenklassen[$aktrasse][1];}

					//eigene Einheiten
					if ($rasse-1!=$aktrasse){//wenn es nicht die eigene rasse ist leer lassen
						  // Platzhalter dynamisch auf Anzahl der Schiffe setzen (vorher nur 8, führte zu Undefined array key Warnungen bei Index >=8)
						  $keigene1 = array_fill(0, $sv_anz_schiffe, '&nbsp;');
						  $keigene2 = $keigene1;
						  $keigene3 = $keigene1;
					}else{ //ansonsten variablen in die array packen
						for ($j=0;$j<$sv_anz_schiffe;$j++){
							$keigene1[$j] = number_format($kbd['einheiten_spieler'][0][$j], 0,"",".");
							$keigene2[$j] = number_format($kbd['einheiten_spieler'][1][$j], 0,"",".");
							$keigene3[$j] = number_format($kbd['einheiten_spieler'][0][$j]-$kbd['einheiten_spieler'][2][$j], 0,"",".");
						}
					}
					
					

					$kbstring.='
		  <TR align="center">
		  <TD class="'.$klasse.'">'.$schiffsnamen[$i].'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_atter'][0][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_atter'][1][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_atter'][0][$aktrasse][$i]-$kbd['einheiten_atter'][2][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_deffer'][0][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_deffer'][1][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.number_format($kbd['einheiten_deffer'][0][$aktrasse][$i]-$kbd['einheiten_deffer'][2][$aktrasse][$i], 0,"",".").'</TD>
		  <TD class="'.$klasse.'">'.$keigene1[$i].'</TD>
		  <TD class="'.$klasse.'">'.$keigene2[$i].'</TD>
		  <TD class="'.$klasse.'">'.$keigene3[$i].'</TD>
		  </TR>';
					//statistik erstellen
					$geseinheiten_atter_anz+=$kbd['einheiten_atter'][0][$aktrasse][$i];
					$geseinheiten_deffer_anz+=$kbd['einheiten_deffer'][0][$aktrasse][$i];

					$geseinheiten_atter_anz_verloren+=$kbd['einheiten_atter'][2][$aktrasse][$i];
					$geseinheiten_deffer_anz_verloren+=$kbd['einheiten_deffer'][2][$aktrasse][$i];

					$geseinheiten_atter_score+=$kbd['einheiten_atter'][0][$aktrasse][$i]*$schiffspunkte[$aktrasse][$i];
					$geseinheiten_deffer_score+=$kbd['einheiten_deffer'][0][$aktrasse][$i]*$schiffspunkte[$aktrasse][$i];

					$geseinheiten_atter_score_lost+=$kbd['einheiten_atter'][2][$aktrasse][$i]*$schiffspunkte[$aktrasse][$i];
					$geseinheiten_deffer_score_lost+=$kbd['einheiten_deffer'][2][$aktrasse][$i]*$schiffspunkte[$aktrasse][$i];

				}
			}
		}
	}

	////////////////////////////////////////////////
	////////////////////////////////////////////////
	//türme
	////////////////////////////////////////////////
	////////////////////////////////////////////////
	//print_r($kbd['tuerme']);
	if(($kbd['tuerme'][0][0]+$kbd['tuerme'][0][1]+$kbd['tuerme'][0][2]+$kbd['tuerme'][0][3]+$kbd['tuerme'][0][4])>0){
		$kbstring.='
		<TR align="center">
		<TD class="k1" colSpan=10><u>'.$kbl_lang['tuerme'].'</u></TD>
		</TR>
		';
		  $aktrasse=$kturmrasse;
		  $schiffsnamen = $turmnamen[$aktrasse];
		  $c1=0;$c2=0;
		  for ($i=0;$i<$sv_anz_tuerme;$i++)
		  {
			if ($schiffsnamen[$i]!='NA')
			{
			  if ($c1==0){$c1=1; $klasse=$rassenklassen[$aktrasse][0];}
			  else {$c1=0; $klasse=$rassenklassen[$aktrasse][1];}

			  $kbstring.='
		<TR align="center">
		<TD class="'.$klasse.'">'.$schiffsnamen[$i].'</TD>
		<TD class="'.$klasse.'">-</TD>
		<TD class="'.$klasse.'">-</TD>
		<TD class="'.$klasse.'">-</TD>
		<TD class="'.$klasse.'">'.number_format($kbd['tuerme'][0][$i], 0,"",".").'</TD>
		<TD class="'.$klasse.'">'.number_format($kbd['tuerme'][1][$i], 0,"",".").'</TD>
		<TD class="'.$klasse.'">'.number_format($kbd['tuerme'][0][$i]-$kbd['tuerme'][2][$i], 0,"",".").'</TD>
		<TD class="'.$klasse.'">-</TD>
		<TD class="'.$klasse.'">-</TD>
		<TD class="'.$klasse.'">-</TD>
		</TR>';

			  //statistik erstellen
			  $geseinheiten_deffer_anz+=$kbd['tuerme'][0][$i];
			  $geseinheiten_deffer_anz_verloren+=$kbd['tuerme'][2][$i];

			  $geseinheiten_deffer_score+=$kbd['tuerme'][0][$i]*$schiffspunkte[$aktrasse][$i+10];
			  $geseinheiten_deffer_score_lost+=$kbd['tuerme'][2][$i]*$schiffspunkte[$aktrasse][$i+10];
			}
			//$grundindex1=$grundindex1+5;
		  }
	}

	$kbstring.=
	'</TABLE>';
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
	//  statistik
	//////////////////////////////////////////////////////
	//////////////////////////////////////////////////////
	$kbstring.='
	<br><table cellSpacing=0 cellPadding=2 width=555 border=1>
	<tr align="center"><td colspan="4" class="k1"><b>'.$kbl_lang['statistik'].'</b></td></tr>
	<tr align="center">
	<td  class="k2"><b>'.$kbl_lang['typ'].'</b></td>
	<td  class="k2"><b>'.$kbl_lang['angreifer'].'</b></td>
	<td  class="k2"><b>'.$kbl_lang['verteidiger'].'</b></td>
	<td  class="k2"><b>'.$kbl_lang['verhaeltnis'].'</b></td>
	</tr>';

	if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
	//das verh�ltnis berechnen
	if ($geseinheiten_atter_anz>0)
	$verhaeltnis=$geseinheiten_deffer_anz/$geseinheiten_atter_anz;
	else $verhaeltnis=0;
	$kbstring.='
	<tr align="center">
	<td  class="'.$bg.'">'.$kbl_lang['einheitenanzahl'].'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_atter_anz, 0,"",".").'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_deffer_anz, 0,"",".").'</td>
	<td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
	</tr>';

	if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
	//das verh�ltnis berechnen
	if ($geseinheiten_atter_anz_verloren>0)
	$verhaeltnis=$geseinheiten_deffer_anz_verloren/$geseinheiten_atter_anz_verloren;
	else $verhaeltnis=0;
	//den prozentwert berechnen
	@$prozent_atter=$geseinheiten_atter_anz_verloren*100/$geseinheiten_atter_anz;
	if($geseinheiten_deffer_anz>0)$prozent_deffer=$geseinheiten_deffer_anz_verloren*100/$geseinheiten_deffer_anz;
	else $prozent_deffer=0;
	$kbstring.='
	<tr align="center">
	<td  class="'.$bg.'">'.$kbl_lang['verloreneeinheiten'].'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_atter_anz_verloren, 0,"",".").' ('.number_format($prozent_atter, 2,",",".").'%)</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_deffer_anz_verloren, 0,"",".").' ('.number_format($prozent_deffer, 2,",",".").'%)</td>
	<td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
	</tr>';
	if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
	//das verh�ltnis berechnen
	if ($geseinheiten_atter_score>0)
	$verhaeltnis=$geseinheiten_deffer_score/$geseinheiten_atter_score;
	else $verhaeltnis=0;
	$kbstring.='
	<tr align="center">
	<td  class="'.$bg.'">'.$kbl_lang['einheitenpunktewert'].'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_atter_score, 0,"",".").'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_deffer_score, 0,"",".").'</td>
	<td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
	</tr>';

	if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}
	//das verh�ltnis berechnen
	if ($geseinheiten_atter_score_lost>0)
	$verhaeltnis=$geseinheiten_deffer_score_lost/$geseinheiten_atter_score_lost;
	else $verhaeltnis=0;
	//den prozentwert berechnen
	@$prozent_atter=$geseinheiten_atter_score_lost*100/$geseinheiten_atter_score;
	if($geseinheiten_deffer_score>0)$prozent_deffer=$geseinheiten_deffer_score_lost*100/$geseinheiten_deffer_score;
	else $prozent_deffer=0;
	$kbstring.='
	<tr align="center">
	<td  class="'.$bg.'">'.$kbl_lang['verlorenepunkte'].'</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_atter_score_lost, 0,"",".").' ('.number_format($prozent_atter, 2,",",".").'%)</td>
	<td  class="'.$bg.'">'.number_format($geseinheiten_deffer_score_lost, 0,"",".").' ('.number_format($prozent_deffer, 2,",",".").'%)</td>
	<td  class="'.$bg.'">1:'.number_format($verhaeltnis, 2,",",".").'</td>
	</tr>';

	///////////////////////////////////////////////////
	// Erklärung warum wer gewonnen hat
	///////////////////////////////////////////////////
	if ($c1==0){$c1=1;$bg='k2';}else{$c1=0;$bg='k1';}

	$vernichtete_deffer=$kbd['daten']['deffer_hp_lost'];
	$vernichtete_atter=$kbd['daten']['atter_hp_lost'];

	$deffer_allhp=$kbd['daten']['deffer_hp'];
	$atter_allhp=$kbd['daten']['atter_hp'];

	if($vernichtete_deffer==0)$vernichtete_deffer = 0.000001;
	if($vernichtete_atter==0)$vernichtete_atter   = 0.000001;
	$zrd=$deffer_allhp/$vernichtete_deffer;
	$zra=$atter_allhp/$vernichtete_atter;	

	//wenn $zra>$zrd also der atter weniger einheiten verloren hat, als der deffer auf die gesamtflotte gerechnet
	/*
	if($zra>=$zrd){
		//Atter hat gewonnen
		$win_explain='Es gewinnt derjenige, der weniger Hitpoints im Verhältnis zur Gesamthitpointzahl verloren hat.';
	}else{
		//Deffer hat gewonnen
		$win_explain='deffer';
	}
	*/

	if($atter_allhp == 0){
		$atter_allhp=1;
	}

	if($deffer_allhp == 0){
		$deffer_allhp=1;
	}

	$win_explain='Es gewinnt derjenige, der weniger Hitpoints im Verh&auml;ltnis zur Gesamthitpointzahl verloren hat. In den Hitpoints sind alle Boni eingerechnet.';	
	$win_explain.='<br>Verluste Angreifer: '.number_format($vernichtete_atter*100/$atter_allhp, 2,",",".").'%';
	$win_explain.='<br>Verluste Verteidiger: '.number_format($vernichtete_deffer*100/$deffer_allhp, 2,",",".").'%';

	$kbstring.='
	<tr align="center">
	<td  class="'.$bg.'">Gewinner</td>
	<td  class="'.$bg.'" colspan="3">'.$win_explain.'</td>
	</tr>';



	$kbstring.='</table>&nbsp;</CENTER>';

	return($kbstring);
}
?>
