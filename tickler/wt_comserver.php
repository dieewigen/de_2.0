<?php
if(intval(date("i"))==0 && intval(date("H"))==19 || $erhabenenstop==1)
{
  //anzahl der stimmen auslesen
  $time=time()-(3600*24*3);
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE status=1 AND last_click > ?", [$time]);
  $anz = mysqli_num_rows($db_daten);
  
  //daten aus der db auslesen und werte berechnen
  //daten über median ermitteln
  $time=time()-(3600*24*3);
  $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_comserver.* FROM de_login LEFT JOIN de_user_comserver ON(de_login.user_id = de_user_comserver.user_id) 
  WHERE de_login.status=1 AND de_login.last_click > ? AND de_user_comserver.user_id>0", [$time]);
  
  //alle daten in ein array packen
  unset($votes);
  while($row = mysqli_fetch_array($db_daten))
  {
    for($i=1;$i<=40;$i++)if(!is_null($row['v'.$i]))$votes[$i][]=$row['v'.$i];
  }
  
  if($anz>0)
  {
  
  $server_v1=round(median($votes[1]));
  $server_v2=round(median($votes[2]));
  $server_v3=round(median($votes[3]));
  $server_v4=round(median($votes[4]));
  $server_v5=round(median($votes[5]));
  $server_v6=round(median($votes[6]));
  $server_v7=round(median($votes[7]));
  $server_v8=round(median($votes[8]));
  $server_v9=round(median($votes[9]));
  $server_v10=round(median($votes[10]));
  $server_v11=round(median($votes[11]));
  $server_v12=round(median($votes[12]));
  $server_v13=round(median($votes[13]));
  $server_v14=round(median($votes[14]));
  $server_v15=round(median($votes[15]));
  $server_v16=round(median($votes[16]));
  $server_v17=round(median($votes[17]));
  $server_v18=round(median($votes[18]));
  $server_v19=round(median($votes[19]));
  $server_v20=round(median($votes[20]));
  $server_v21=round(median($votes[21]));
  $server_v22=round(median($votes[22]));
  $server_v23=round(median($votes[23]));
  $server_v24=round(median($votes[24]));
  $server_v25=round(median($votes[25]));
  $server_v26=round(median($votes[26]));
  $server_v27=round(median($votes[27]));
  $server_v28=round(median($votes[28]));  
  $server_v29=round(median($votes[29]));  
  $server_v30=round(median($votes[30]));  
  $server_v31=round(median($votes[31]));  
  $server_v32=round(median($votes[32]));  
  $server_v33=round(median($votes[33]));  
  $server_v34=round(median($votes[34]));  
  $server_v35=round(median($votes[35]));  

  
    //daten in die entsprechende datei schreiben
    $filename="../inc/svcomserver.inc.php";
    $cachefile = fopen ($filename, 'w');

    $str="<?php\n\n";

    //daten die täglich/zur neuen runde aktualisiert werden
    $str.='$sv_comserver_wt='.$server_v1.';'."\n\n";
    $str.='$sv_comserver_kt='.$server_v2.';'."\n\n";
    settickzeiten($server_v1,$server_v2);
    $str.='$sv_winscore='.$server_v3.';'."\n\n";
    $str.='$sv_benticks='.$server_v4.';'."\n\n";
    $str.='$sv_inactiv_deldays='.$server_v5.';'."\n\n";
	  $str.='$sv_attgrenze='.($server_v6/100).';'."\n\n";
    $str.='$sv_sector_attmalus='.($server_v7/100).';'."\n\n";
    $str.='$sv_max_col_attgrenze='.($server_v8/100).';'."\n\n";
    $str.='$sv_min_col_attgrenze='.($server_v9/100).';'."\n\n";
    $str.='$sv_ps_bonus='.$server_v10.';'."\n\n";
    $str.='$sv_recyclotron_bonus='.$server_v11.';'."\n\n";
    $str.='$sv_recyclotron_bonus_whg='.$server_v12.';'."\n\n";
    $str.='$sv_kollie_klaurate='.($server_v13/100).';'."\n\n";
    $str.='$sv_kollieertrag='.$server_v14.';'."\n\n";
    $str.='$sv_kollieertrag_pa='.$server_v15.';'."\n\n";
    $str.='$sv_kriegsartefaktertrag='.$server_v16.';'."\n\n";
    $str.='$sv_kartefakt_exp_atter='.$server_v17.';'."\n\n";
    $str.='$sv_kartefakt_exp_deffer='.$server_v18.';'."\n\n";
    $str.='$sv_max_palenium='.$server_v19.';'."\n\n";
    $str.='$sv_bounty_rate='.($server_v20/100).';'."\n\n";
    
    
	//variablen �berpr�fen	
    if(!isset($sv_comserver_roundtyp))$sv_comserver_roundtyp=0;
    if(!isset($sv_deactivate_secret))$sv_deactivate_secret=0;
    if(!isset($sv_deactivate_religion))$sv_deactivate_religion=0;
    if(!isset($sv_deactivate_blackmarket))$sv_deactivate_blackmarket=0;
    if(!isset($sv_deactivate_sectorartefacts))$sv_deactivate_sectorartefacts=0;
    
    //daten die nur zur neuen runde aktiv werden
    if($erhabenenstop==1){

      //rundentyp normal/br
      
	    $str.='$sv_comserver_roundtyp='.$server_v21.';'."\n\n";
	  
      //sektoren zufall/wahl
      if($server_v22==0)//zufall
      {
        $str.='$sv_deactivate_sec1moveout=0;'."\n\n";
        $str.='$sv_max_secmoves=0;'."\n\n";
      }
      else //wahl
      {
        $str.='$sv_deactivate_sec1moveout=1;'."\n\n";
        $str.='$sv_max_secmoves=999;'."\n\n";
      }
      
      //spieler pro sektor
      $str.='$sv_maxsystem='.$server_v23.';'."\n\n";
      $str.='$sv_max_user_per_regsector='.$server_v23.';'."\n\n";
      
      //maximale sektoranzahl
      //dafür wird die anzahl von aktiven spielern für die berechnung benötigt
      $str.='$sv_maxsector='.ceil(($anz*1.1/$server_v23)+1).';'."\n\n";

      $str.='$sv_deactivate_trade='.$server_v24.';'."\n\n";
      $str.='$sv_deactivate_religion='.$server_v25.';'."\n\n";
      $str.='$sv_deactivate_secret='.$server_v26.';'."\n\n";
      $str.='$sv_deactivate_blackmarket='.$server_v27.';'."\n\n";
      $str.='$sv_deactivate_sectorartefacts='.$server_v28.';'."\n\n";
      $str.='$sv_deactivate_missions='.$server_v29.';'."\n\n";
      $str.='$sv_deactivate_vsystems='.$server_v30.';'."\n\n";
      $str.='$sv_hide_fp_in_secstatus='.$server_v31.';'."\n\n";

    }else{
      //kein Rundenende, also die bestehenden Daten übernehmen
      $str.='$sv_comserver_roundtyp='.$sv_comserver_roundtyp.';'."\n\n";
      $str.='$sv_deactivate_sec1moveout='.$sv_deactivate_sec1moveout.';'."\n\n";
      $str.='$sv_max_secmoves='.$sv_max_secmoves.';'."\n\n";
      $str.='$sv_maxsystem='.$sv_maxsystem.';'."\n\n";
      $str.='$sv_max_user_per_regsector='.$sv_max_user_per_regsector.';'."\n\n";
      $str.='$sv_maxsector='.$sv_maxsector.';'."\n\n";
      

      $str.='$sv_deactivate_trade='.$sv_deactivate_trade.';'."\n\n";
      $str.='$sv_deactivate_religion='.$sv_deactivate_religion.';'."\n\n";
      $str.='$sv_deactivate_secret='.$sv_deactivate_secret.';'."\n\n";
      $str.='$sv_deactivate_blackmarket='.$sv_deactivate_blackmarket.';'."\n\n";
      $str.='$sv_deactivate_sectorartefacts='.$sv_deactivate_sectorartefacts.';'."\n\n";
      $str.='$sv_deactivate_missions='.$sv_deactivate_missions.';'."\n\n";
      $str.='$sv_deactivate_vsystems='.$sv_deactivate_vsystems.';'."\n\n";
      $str.='$sv_hide_fp_in_secstatus='.$sv_hide_fp_in_secstatus.';'."\n\n";
	  
    }
    
    $str.="\n\n?>";

    if ($cachefile) fwrite ($cachefile, $str);
    fclose($cachefile);

  }
}

function settickzeiten($wt,$kt){
  //zuerst das verzeichnis auslesen wohin man mu�
  $filename="runtick.sh";
  $cachefile = fopen ($filename, 'r');
  $xticks=trim(fgets($cachefile, 1024));
  $xticks=trim(fgets($cachefile, 1024));
  $wticks = str_replace("#","",$wticks);
  $kticks = str_replace("#","",$kticks);
  $cdpfad=trim(fgets($cachefile, 1024));
  //echo $wticks;
  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);

  $filename="runtick.sh";
  $cachefile = fopen ($filename, 'w');

  //hier die einzelnen ticks zu den passenden minuten hinterlegen
  //wt
  $wtcounter=0;
  for ($i=0;$i<=59;$i++)
  {
    if($i==$wtcounter)
    {
      $wticks[$i]=1;
      $wtcounter+=$wt;
    }
    else $wticks[$i]=0;
  }
  
  //kt
  $ktcounter=0;
  for ($i=0;$i<=59;$i++)
  {
    if($i==$ktcounter)
    {
      $kticks[$i]=1;
      $ktcounter+=$kt;
    }
    else $kticks[$i]=0;
  }
  
  $str='#'.implode("", $wticks)."\n";
  $str.='#'.implode("", $kticks)."\n";
  $str.=$cdpfad."\n";
  $str.='minute=`date "+%M"`'."\n";
  //immer user registrieren
  $str.="./register.sh\n";
  //if-abfragen bauen die die ticks starten
  for($i=0;$i<=59;$i++)
  {
    if($i<10)$w="0".$i;else $w=$i;
    if($wticks[$i]=="1" AND $kticks[$i]=="0"){$str.="if [ \$minute = \"$w\" ]; then\n./wt.sh\nfi\n";}
    if($wticks[$i]=="0" AND $kticks[$i]=="1"){$str.="if [ \$minute = \"$w\" ]; then\n./kt.sh\nfi\n";}
    if($wticks[$i]=="1" AND $kticks[$i]=="1"){$str.="if [ \$minute = \"$w\" ]; then\n./kt_wt.sh\nfi\n";}
  }

  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);
}
?>