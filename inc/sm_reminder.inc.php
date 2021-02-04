<?php
include 'inc/lang/'.$sv_server_lang.'_sm_reminder.lang.php';
//zeigt den schwarzmarkt reminder an, wenn die zeit um ist
if(time()>($_SESSION['ums_sm_remtimer']+$_SESSION['ums_sm_remtime']*60) AND $_SESSION['ums_sm_remtime']>0){
	//den maximalen tick auslesen
	if($sv_ewige_runde==1 || $sv_hardcore==1){
		$db_daten=mysql_query("SELECT tick FROM de_user_data WHERE user_id='".$_SESSION['ums_user_id']."'",$db);
		$row = mysql_fetch_array($db_daten);
		$smround_tick=$row["tick"];	
	}else{
		$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
		$row = mysql_fetch_array($db_daten);
		$smround_tick=$row["tick"];	
	}
  
	//lieferzeiten definieren
	$artefaktlz=array(1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200);
	$sm_col_lz=550;
	$sm_kartefakt_lz=66;
	$sm_tronic_lz=240;

	//spielerartefakte die im angebot sind
	$artefaktangebot=array(100, 104, 105, 106);


	//artefaktdaten lade
	include_once "inc/userartefact.inc.php";

	//flags und accountalter laden
	$result = mysql_query("SELECT tick, credits, patime, sm_rboost, sm_tronic, sm_kartefakt, sm_col,
	 sm_rboost_rem, sm_tronic_rem, sm_kartefakt_rem, sm_col_rem,
	 sm_art1, sm_art2, sm_art3, sm_art4, sm_art5, sm_art6, sm_art7, sm_art8, sm_art9, sm_art10, sm_art11, sm_art12, sm_art13, sm_art14, sm_art15,
	 sm_art1rem, sm_art2rem, sm_art3rem, sm_art4rem, sm_art5rem, sm_art6rem, sm_art7rem, sm_art8rem, sm_art9rem, sm_art10rem, sm_art11rem, sm_art12rem,
	 sm_art13rem, sm_art14rem, sm_art15rem
	 FROM de_user_data WHERE user_id='$ums_user_id'", $db);
	$row = mysql_fetch_array($result);
	$smtick=$row["tick"];

	//rahmen oben
	echo '<table width="590px" border="0" cellpadding="0" cellspacing="0">
		  <tr>
		  <td width="13" height="37" class="rol">&nbsp;</td>
		  <td align="center" class="ro"><div class="cellu">'.$smreminder_lang['schwarzmarktreminder'].' ('.$smreminder_lang['creditstand'].': '.number_format($row["credits"], 0,"",".").')</div></td>
		  <td width="13" class="ror">&nbsp;</td>
		  </tr>
		  <tr>
		  <td class="rl">&nbsp;</td><td>';

	//die tabelle darstellen
	echo ('<table>');
	//premiumaccount
	if($row["patime"]>time())
	{
	  //pa ist noch aktiv
	  $palz=date("d.m.Y - G:i", $row["patime"]);
	  $palz=$smreminder_lang['gueltigbis'].': '.$palz;
	}
	else
	{
	  //pa ist nicht aktiv
	  $palz=$smreminder_lang['keinpavorhanden'];
	}

	echo('<tr class="cell" align="center"><td width="230"><a href="blackmarket.php">'.$smreminder_lang['premiumaccount'].'</a></td><td width="340">'.$palz.'</td></tr>');
	echo('<tr class="cell1" align="center"><td><b>'.$smreminder_lang['artikel'].'</td><td><b>'.$smreminder_lang['lieferzeit'].' / '.$smreminder_lang['genutztelieferungen'].'</td></tr>');
	$c1=1;

	//rohstofflieferung
	if($row["sm_rboost_rem"]==1)
	{
	  if ($c1==0)
	  {
		$c1=1;
		$bg='cell1';
	  }
	  else
	  {
		$c1=0;
		$bg='cell';
	  }

	  if($smtick>$row["sm_rboost"]+1000)
	  {
		//es ist lieferbar
		$lzp='<i><font color="#00FF00">'.$smreminder_lang['lieferungsofortmoeglich'].'</font></i>';
	  }
	  else
	  {
		$lzp='<i>'.$smreminder_lang['liefer1'].' <b>'.(($smtick-$row["sm_rboost"]-1000)*(-1)+1).'</b> '.$smreminder_lang['liefer2'].'</i>';
	  }

	  echo('<tr class="'.$bg.'" align="center"><td><a href="blackmarket.php">'.$smreminder_lang['rohstofflieferung'].'</a></td><td><a href="blackmarket.php">'.$lzp.'</a></td></tr>');
	}

	//tronic
	if($row["sm_tronic_rem"]==1)
	{
	  if ($c1==0)
	  {
		$c1=1;
		$bg='cell1';
	  }
	  else
	  {
		$c1=0;
		$bg='cell';
	  }

	  if($row["sm_tronic"] < floor($smround_tick/$sm_tronic_lz)){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
	  $lzp=$str1.number_format($row["sm_tronic"], 0,"",".").'/'.number_format(floor($smround_tick/$sm_tronic_lz), 0,"",".").$str2;


	  echo('<tr class="'.$bg.'" align="center"><td><a href="blackmarket.php">'.$smreminder_lang['tronic'].'</a></td><td><a href="blackmarket.php">'.$lzp.'</a></td></tr>');
	}

	//kriegsartefakt
	if($row["sm_kartefakt_rem"]==1)
	{
	  if ($c1==0)
	  {
		$c1=1;
		$bg='cell1';
	  }
	  else
	  {
		$c1=0;
		$bg='cell';
	  }

	  if($row["sm_kartefakt"] < floor($smround_tick/$sm_kartefakt_lz)){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
	  $lzp=$str1.number_format($row["sm_kartefakt"], 0,"",".").'/'.number_format(floor($smround_tick/$sm_kartefakt_lz), 0,"",".").$str2;


	  echo('<tr class="'.$bg.'" align="center"><td><a href="blackmarket.php">'.$smreminder_lang['kriegsartefakt'].'</a></td><td><a href="blackmarket.php">'.$lzp.'</a></td></tr>');
	}


	//artefakte
	/*
	$submit=100;
	for($i=0;$i<=$ua_index;$i++){
	  $ai=$i+1;
	  if($row["sm_art".$ai."rem"]==1)
	  {
		if(in_array($submit,$artefaktangebot))
		{
		  if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}

		  if($row["sm_art$ai"] < floor($smround_tick/$artefaktlz[$i])){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
		  $lzart=$str1.number_format($row["sm_art$ai"], 0,"",".").'/'.number_format(floor($smround_tick/$artefaktlz[$i]), 0,"",".").$str2;      

		  echo('<tr class="'.$bg.'" align="center"><td><a href="blackmarket.php">'.$ua_name[$i].'-'.$smreminder_lang[artefakt].'</a></td><td><a href="blackmarket.php">'.$lzart.'</a></td></tr>');
		}
	  }
	  $submit++;
	}
	//gebrauchter kollektor
	if($row["sm_col_rem"]==1)
	{
	  if ($c1==0)
	  {
		$c1=1;
		$bg='cell1';
	  }
	  else
	  {
		$c1=0;
		$bg='cell';
	  }

	  if($row["sm_col"] < floor($smround_tick/$sm_col_lz)){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
	  $lzp=$str1.number_format($row["sm_col"], 0,"",".").'/'.number_format(floor($smround_tick/$sm_col_lz), 0,"",".").$str2;    

	  echo('<tr class="'.$bg.'" align="center"><td><a href="blackmarket.php">'.$smreminder_lang[gebrauchterkollektor].'</a></td><td><a href="blackmarket.php">'.$lzp.'</a></td></tr>');
	}
	*/

	//info �ber einstellungen
	  if ($c1==0)
	  {
		$c1=1;
		$bg='cell1';
	  }
	  else
	  {
		$c1=0;
		$bg='cell';
	  }
	echo('<tr class="'.$bg.'" align="left"><td colspan="2"><i>'.$smreminder_lang['beschreibung'].'</i></td></tr>');

	echo '</table>';
	//rahmen unten
	echo '</td><td width="13" class="rr">&nbsp;</td>
		  </tr>
		  <tr>
		  <td width="13" class="rul">&nbsp;</td>
		  <td class="ru">&nbsp;</td>
		  <td width="13" class="rur">&nbsp;</td>
		  </tr>
		  </table><br>';

	//zeit neu setzen
	$_SESSION['ums_sm_remtimer']=time();
}
?>
