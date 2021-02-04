<?php
include "inc/header.inc.php";
include "lib/transaction.lib.php";
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_archeology.lang.php';

$pt=loadPlayerTechs($_SESSION['ums_user_id']);
$pd=loadPlayerData($_SESSION['ums_user_id']);
$row=$pd;
$restyp01=$row['restyp01'];$restyp02=$row['restyp02'];$restyp03=$row['restyp03'];$restyp04=$row['restyp04'];$restyp05=$row['restyp05'];
$restyp05=$row[4];$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$npccol=0;$archi=$row["archi"];$tick=$row["tick"];

//zuerst rundendauer auslesen
$db_daten=mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
$row = mysql_fetch_array($db_daten);
$ticks=$row["tick"];    
$tick=$ticks;

//$tick=250000000;

//ausbildungskosten/zeit
$ausbildungskosten = array (100,250,500,100);
$ausbildungszeit = 10;

//aktivierungszeit für die missioonen in wt
$activate=array (0, 500, 1000, 1500, 2000, 2500, 3000, 3500, 0, 750, 1000);

//benötigte arhäologen für die mission
$needarchi=array(
100, //Der güldene Kollektor
200, //Die Sondenlager
300, //Die Kollektorenhalden
310, //M-Mine
320, //D-Mine
330, //I-Mine
340, //E-Mine
350, //T-Lager
100, //Kriegsartefakte
400, //Spielerartefakte
100  //Palenium
);

////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//archäologen ausbilden
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
/*
if ($techs[29]==1 AND $_POST["ausbanz"]>0)//ja, es wurde ein button gedrueckt
{
  $ausbauanz=intval($_POST["ausbanz"]);

  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //kosten/zeiten zuweisen
    $benrestyp01=$ausbildungskosten[0];
    $benrestyp02=$ausbildungskosten[1];
    $benrestyp03=$ausbildungskosten[2];
    $benrestyp04=$ausbildungskosten[3];
    $tech_ticks=$ausbildungszeit;

    $z=0;
    for ($k=1; $k<=$ausbauanz; $k++)
    {
      if ($fehlermsg=='' && $benrestyp01<=$restyp01 && $benrestyp02<=$restyp02 &&$benrestyp03<=$restyp03 &&$benrestyp04<=$restyp04)
      {
        $restyp01=$restyp01-$benrestyp01;
        $restyp02=$restyp02-$benrestyp02;
        $restyp03=$restyp03-$benrestyp03;
        $restyp04=$restyp04-$benrestyp04;
        $z++;
      }
      else break;
    }

    //gibt $z einheiten in auftrag
    $result = mysql_query("SELECT anzahl FROM de_user_build WHERE user_id = '$ums_user_id' AND tech_id=1001 AND verbzeit='$tech_ticks'",$db);
    $row = mysql_fetch_array($result);
    if ($z>0)
    if ($row[0]==0) //es gibt keine schiffe mit tech_ticks laenge in der queue
      mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, 1001, $z, $tech_ticks)",$db);
    else mysql_query("update de_user_build set anzahl = anzahl + '$z' WHERE user_id = '$ums_user_id' AND tech_id=1001 AND verbzeit='$tech_ticks'",$db);
    //echo "Schiffe in Auftrag gegeben: ".$z."<br>";

    //aktualisiert die rohstoffe
    $gr01=$gr01-$restyp01;
    $gr02=$gr02-$restyp02;
    $gr03=$gr03-$restyp03;
    $gr04=$gr04-$restyp04;
    mysql_query("update de_user_data set restyp01 = restyp01 - $gr01,
     restyp02 = restyp02 - $gr02, restyp03 = restyp03 - $gr03,
     restyp04 = restyp04 - $gr04 WHERE user_id = '$ums_user_id'",$db);

    //transaktionsende
    $erg = releaseLock($ums_user_id); //Lösen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
        print("$secret_lang[transerror1]<br><br><br>");
    }
  }// if setlock-ende
}*/

////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//die analyse von datenpaket x starten
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
/*
if($_POST["analyse1"] OR $_POST["analyse2"] OR $_POST["analyse3"] OR $_POST["analyse4"] OR $_POST["analyse5"] OR $_POST["analyse6"]
OR $_POST["analyse7"] OR $_POST["analyse8"] OR $_POST["analyse9"] OR $_POST["analyse10"] OR $_POST["analyse11"])
{
  $aid=0;
  if($_POST["analyse1"])$aid=0;
  if($_POST["analyse2"])$aid=1;
  if($_POST["analyse3"])$aid=2;
  if($_POST["analyse4"])$aid=3;
  if($_POST["analyse5"])$aid=4;
  if($_POST["analyse6"])$aid=5;
  if($_POST["analyse7"])$aid=6;
  if($_POST["analyse8"])$aid=7;
  if($_POST["analyse9"])$aid=8;
  if($_POST["analyse10"])$aid=9;
  if($_POST["analyse11"])$aid=10;
  
  $tech_ticks=10;
  //schauen ob man genug archäologen hat
  $zfp=$needarchi[$aid];
  $fp=($archi/($npccol+1))*100/$zfp;
  if($fp>=100 AND $tick>=$activate[$aid])
  {
  	//schauen ob evtl. schon ein datensatz in der db liegt
    $spid=$aid+1;
    $db_data = mysql_query("SELECT user_id FROM de_user_quest WHERE user_id='$ums_user_id' AND pid='$spid'",$db);
    $num1 = mysql_num_rows($db_data);
    $db_data = mysql_query("SELECT user_id FROM de_user_build WHERE user_id='$ums_user_id' AND tech_id>=2001 AND tech_id<=2050",$db);
    $num2 = mysql_num_rows($db_data);
    if($num1==0 AND $num2==0)
    $spid=$aid+2001;
    mysql_query("INSERT INTO de_user_build (user_id, tech_id, anzahl, verbzeit) VALUES ($ums_user_id, '$spid', 1, $tech_ticks)",$db);
  }
}
*/
?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$archeology_lang[archaeologie]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?//stelle die ressourcenleiste dar
include "resline.php";
echo '<form action="archeology.php" method="POST">';
if ($errmsg!='')echo $errmsg;

////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//test auf vorhandenes gebäude
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
if (!hasTech($pt,29)){
	$techcheck="SELECT tech_name FROM de_tech_data".$ums_rasse." WHERE tech_id=29";
	$db_tech=mysql_query($techcheck,$db);
	$row_techcheck = mysql_fetch_array($db_tech);

	//echo $archeology_lang[eswirdeine].$row_techcheck[tech_name].$archeology_lang[benoetigt];  

	echo '<br>';
	rahmen_oben($archeology_lang[fehlendesgebaeude]);
	echo '<table width="572" border="0" cellpadding="0" cellspacing="0">';
	echo '<tr align="left" class="cell">
	<td width="100"><a href="'.$sv_link[0].'?r='.$ums_rasse.'&t=29" target="_blank"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_29.jpg" border="0"></a></td>
	<td valign="top">'.$archeology_lang[gebaeudeinfo].': '.$row_techcheck[tech_name].'</td>
	</tr>';
	echo '</table>';
	rahmen_unten();  
}else{
	/*
  //menü darstellen
  if($_REQUEST["mp"]=='')$_REQUEST["mp"]=1;
  if($_REQUEST["mp"]==1)
  {
    echo '<br><table width=600><tr>
    <td width="50%\" class="cl"><a href="archeology.php?mp=1"><b>>> '.$archeology_lang[datenanalyse].'</b></a></td>
	<td width="50%\" class="cl"><a href="archeology.php?mp=2">'.$archeology_lang[archaeologen].'</a></td>
	</tr>
    <tr>
    <td colspan="2" class="cl">'.$archeology_lang[einleitung].'</td>
    </tr>
    </table><br>';
*/
    //rahmen oben
    echo '<table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro"><div class="cellu">Missionen</div></td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

    //die einzelnen projekte anzeigen
    echo '<table width="570px">';
	//Tabellenkopf
	echo '<tr class="cellu"><td>Mission</td><td>Koordinaten</td><td>Aktive Flotten</td><td>Bereits&nbsp;erledigt?</td></tr>';
	$c1=1;//Starthintergrundfarbe
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //   projekt 1
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    $aid=0;
    //überprüfen, ob die mission schon aktiv 1
    if($tick>=$activate[$aid])
    {
    	//überprüfen ob es schon einen datensatz dazu gibt, wenn nicht, einen anlegen
    	$db_daten=mysql_query("SELECT * FROM de_user_quest WHERE pid='".($aid+1)."' AND user_id='$ums_user_id'",$db);
    	$num = mysql_num_rows($db_daten);
    	if($num==0)
    	{
    		//datensatz anlegen
    		mysql_query("INSERT INTO de_user_quest SET pid='".($aid+1)."', user_id='$ums_user_id'",$db);
    		$flag1=0;$flag2=0;
    	}
    	else
    	{
      		$row = mysql_fetch_array($db_daten);
			$flag1=$row['flag1'];
			$flag2=$row['flag2'];
    	}
     
	    //schauen wieviel output der kollektor hat, grundmenge sind 100 kollektoren
    	//menge berechnen
    	$energie=floor($sv_kollieertrag*(100+($ticks/4)));
    	$energie=number_format($energie, 0,",",".");

        if($flag1==1) $status='<img title="ja" style="vertical-align: middle;" src="'.$ums_gpfad.'g/symbol6.png" border="0">';//$archeology_lang[status3];
        else $status='<img title="nein" style="vertical-align: middle;" src="'.$ums_gpfad.'s/abutton3.gif" border="0">';//$archeology_lang[status6];

        //koordinaten auslesen
        $result = mysql_query("SELECT a1userid, a1npc, a1tick FROM de_system",$db);
        $row = mysql_fetch_array($result);
        $a1userid=$row["a1userid"];
        $a1npc=$row["a1npc"];

        if($a1npc==1)
        {
        	$result = mysql_query("SELECT sector, system FROM de_user_data WHERE user_id='$a1userid'",$db);
            $row = mysql_fetch_array($result);
            $sector=$row["sector"];
            $system=$row["system"];
            $koordinaten='<a href="military.php?se='.$sector.'&sy='.$system.'">'.$sector.':'.$system.'</a>';
                
                
            //flotten auslesen die auf dem weg dorthin sind
            $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zielsec='$sector' AND zielsys='$system'",$db);
            $fleetanz=mysql_num_rows($db_daten);
            $aktive_flotten=$fleetanz;
        }else{
			//$koordinaten=$archeology_lang[artiistweg];
			$koordinaten='?:? <img id="dpe'.$aid.'" title="'.$archeology_lang[artiistweg].'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">';
			$aktive_flotten='';
		}

      if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
      echo '<tr class="'.$bg.'" style="text-align: center;"><td style="text-align: left;">';
      echo '<img id="dp'.$aid.'" title="'.$archeology_lang[paket_1_2].$archeology_lang[paket_1_3].$archeology_lang[paket_1_4].$archeology_lang[paket_1_5].$archeology_lang[paket_1_6].'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">';
      echo ' <b>'.$archeology_lang[paket_1_1].'</b>';
      echo '<br>'.$archeology_lang[energieguelden].$energie.'</td>';
      echo '<td>'.$koordinaten.'</td>';
      echo '<td>'.$aktive_flotten.'</td>';
      echo '<td>'.$status.'</td>';
      echo '</tr>';
    }//ende ticküberprüfung
    
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //   projekt 2 - sonden
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    $aid=1;
    $num1=0;$num2=0;$button='';$koordinaten='';$status='';
    if($tick>=$activate[$aid])
    {
       	//überprüfen ob es schon einen datensatz dazu gibt, wenn nicht, einen anlegen
    	$db_daten=mysql_query("SELECT * FROM de_user_quest WHERE pid='".($aid+1)."' AND user_id='$ums_user_id'",$db);
    	$num = mysql_num_rows($db_daten);
    	if($num==0)
    	{
    		//datensatz anlegen
    		mysql_query("INSERT INTO de_user_quest SET pid='".($aid+1)."', user_id='$ums_user_id'",$db);
    		$flag1=0;$flag2=0;
    	}
    	else
    	{
      		$row = mysql_fetch_array($db_daten);
			$flag1=$row['flag1'];
			$flag2=$row['flag2'];
    	}    	

        if($flag1==1) $status='<img title="ja" style="vertical-align: middle;" src="'.$ums_gpfad.'g/symbol6.png" border="0">';//$archeology_lang[status3];
        else $status='<img title="nein" style="vertical-align: middle;" src="'.$ums_gpfad.'s/abutton3.gif" border="0">';//$archeology_lang[status6];
              
        //koordinaten auslesen
        $result = mysql_query("SELECT a2userid FROM de_system",$db);
        $row = mysql_fetch_array($result);
        $a2userid=$row["a2userid"];

        $result = mysql_query("SELECT sector, system FROM de_user_data WHERE user_id='$a2userid'",$db);
        $row = mysql_fetch_array($result);
        $sector=$row["sector"];
        $system=$row["system"];
        $koordinaten='<a href="military.php?se='.$sector.'&sy='.$system.'">'.$sector.':'.$system.'</a>';
              
        //flotten auslesen die auf dem weg dorthin sind
        $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zielsec='$sector' AND zielsys='$system'",$db);
        $fleetanz=mysql_num_rows($db_daten);
        $fleetanz;

      	if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
      	echo '<tr class="'.$bg.'" style="text-align: center;"><td  style="text-align: left;">';
      	echo '<img id="dp'.$aid.'" title="'.$archeology_lang[paket_2_2].'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">';
      	echo ' <b>'.$archeology_lang[paket_2_1].'</b><br>Sondenlager: 50</td>';
      
      	echo '<td>'.$koordinaten.'</td>';
		echo '<td>'.$fleetanz.'</td>';

      	echo '<td>'.$status.'</td>';

      	echo '</tr>';
    }//ende ticküberprüfung

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //   projekt 3
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    $aid=2;
    $num1=0;$num2=0;$button='';$koordinaten='';$status='';
    if($tick>=$activate[$aid])
    {
       	//überprüfen ob es schon einen datensatz dazu gibt, wenn nicht, einen anlegen
    	$db_daten=mysql_query("SELECT * FROM de_user_quest WHERE pid='".($aid+1)."' AND user_id='$ums_user_id'",$db);
    	$num = mysql_num_rows($db_daten);
    	if($num==0)
    	{
    		//datensatz anlegen
    		mysql_query("INSERT INTO de_user_quest SET pid='".($aid+1)."', user_id='$ums_user_id'",$db);
    		$flag1=0;$flag2=0;
    	}
    	else
    	{
      		$row = mysql_fetch_array($db_daten);
			$flag1=$row['flag1'];
			$flag2=$row['flag2'];
    	}    	

        if($flag1==1) $status='<img title="ja" style="vertical-align: middle;" src="'.$ums_gpfad.'g/symbol6.png" border="0">';//$archeology_lang[status3];
        else $status='<img title="nein" style="vertical-align: middle;" src="'.$ums_gpfad.'s/abutton3.gif" border="0">';//$archeology_lang[status6];
    	
        //koordinaten auslesen
        $result = mysql_query("SELECT a3userid FROM de_system",$db);
        $row = mysql_fetch_array($result);
        $a3userid=$row["a3userid"];

        $result = mysql_query("SELECT sector, system FROM de_user_data WHERE user_id='$a3userid'",$db);
        $row = mysql_fetch_array($result);
        $sector=$row["sector"];
        $system=$row["system"];
        $koordinaten='<a href="military.php?se='.$sector.'&sy='.$system.'">'.$sector.':'.$system.'</a>';
        //flotten auslesen die auf dem weg dorthin sind
        $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zielsec='$sector' AND zielsys='$system'",$db);
        $fleetanz=mysql_num_rows($db_daten);

      	if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
      	echo '<tr class="'.$bg.'" style="text-align: center;"><td style="text-align: left;">';
      	echo '<img id="dp'.$aid.'" title="'.$archeology_lang[paket_3_2].'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">';
      	echo ' <b>'.$archeology_lang[paket_3_1].'</b><br>Kollektorenlager: 25</td>';
            
      	echo '<td>'.$koordinaten.'</td>';
		echo '<td>'.$fleetanz.'</td>';

      	echo '<td>'.$status.'</td>';
	    echo '</tr>';
    }//ende ticküberprüfung

    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    //   projekt 4-11
    //////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////
    for($aid=3;$aid<11;$aid++)
    {
      $tblfield='a'.($aid+1).'userid';
      $num1=0;$num2=0;$koordinaten='';
      if($tick>=$activate[$aid])
      {
		  $wiederholbar=false;
        //gewinn ermitteln
        switch($aid+1)
        {
        case 4: //m-mine
          $energie=round($ticks*100);
          $aname=$archeology_lang[paket_4_1];
          $atext=$archeology_lang[paket_4_2];
          $lager=$archeology_lang[minenlager];
        break;
        case 5: //d-mine
          $energie=round($ticks*100/2);
          $aname=$archeology_lang[paket_5_1];
          $atext=$archeology_lang[paket_5_2];
          $lager=$archeology_lang[minenlager];
        break;
        case 6: //i-mine
          $energie=round($ticks*100/3);
          $aname=$archeology_lang[paket_6_1];
          $atext=$archeology_lang[paket_6_2];
          $lager=$archeology_lang[minenlager];
        break;
        case 7: //e-mine
          $energie=round($ticks*100/4);
          $aname=$archeology_lang[paket_7_1];
          $atext=$archeology_lang[paket_7_2];
          $lager=$archeology_lang[minenlager];
        break;
        case 8: //t-mine
          $energie=round($ticks/250);
          $aname=$archeology_lang[paket_8_1];
          $atext=$archeology_lang[paket_8_2];
          $lager=$archeology_lang[minenlager];
        break;
        case 9: //kriegsartefakte
          $energie=5;
          $aname=$archeology_lang[paket_9_1];
          $atext=$archeology_lang[paket_9_2];
          $lager=$archeology_lang[raumschifflager];
        break;
        case 10: //spielerartefakt
			$wiederholbar=true;
          $energie=1;
          $aname=$archeology_lang[paket_10_1];
          $atext=$archeology_lang[paket_10_2];
          //if($sv_server_lang==1)$atext = $atext.' '.$archeology_lang[paket_10_3];
		  $atext.='<br>Die Mission ist wiederholbar.';
          $lager=$archeology_lang[raumschifflager];
        break;
        case 11: //palenium
			$wiederholbar=true;
          $energie=$sv_max_palenium;
          $aname=$archeology_lang[paket_11_1];
          $atext=$archeology_lang[paket_11_2];
          if($sv_server_lang==1)$atext = $atext.' '.$archeology_lang[paket_11_3];
		  $atext.='<br>Die Mission ist wiederholbar.';
          $lager=$archeology_lang[vortexlager];
        break;        
        }
        $energie=number_format($energie, 0,",",".");
        
       	//überprüfen ob es schon einen datensatz dazu gibt, wenn nicht, einen anlegen
    	$db_daten=mysql_query("SELECT * FROM de_user_quest WHERE pid='".($aid+1)."' AND user_id='$ums_user_id'",$db);
    	$num = mysql_num_rows($db_daten);
    	if($num==0)
    	{
    		//datensatz anlegen
    		mysql_query("INSERT INTO de_user_quest SET pid='".($aid+1)."', user_id='$ums_user_id'",$db);
    		$flag1=0;$flag2=0;
    	}
    	else
    	{
      		$row = mysql_fetch_array($db_daten);
			$flag1=$row['flag1'];
			$flag2=$row['flag2'];
			$anzahl=$row['anzahl'];
    	}    	

		if($flag2==0){
			if($flag1==1) $status='<img title="ja" style="vertical-align: middle;" src="'.$ums_gpfad.'g/symbol6.png" border="0">';//$archeology_lang[status3];
			else $status='<img title="nein" style="vertical-align: middle;" src="'.$ums_gpfad.'s/abutton3.gif" border="0">';//$archeology_lang[status6];
		}else{
			$status='<img title="ja" style="vertical-align: middle;" src="'.$ums_gpfad.'g/symbol6.png" border="0"> '.$anzahl.'x';
		}
		
        //koordinaten auslesen
        $result = mysql_query("SELECT $tblfield AS user_id FROM de_system",$db);
        $row = mysql_fetch_array($result);
        $userid=$row["user_id"];

        $result = mysql_query("SELECT sector, system FROM de_user_data WHERE user_id='$userid'",$db);
        $row = mysql_fetch_array($result);
        $sector=$row["sector"];
        $system=$row["system"];
        $koordinaten='<a href="military.php?se='.$sector.'&sy='.$system.'">'.$sector.':'.$system.'</a>';
                
        //flotten auslesen die auf dem weg dorthin sind
        $db_daten = mysql_query("SELECT user_id FROM de_user_fleet WHERE aktion = 4 AND zielsec='$sector' AND zielsys='$system'",$db);
        $fleetanz=mysql_num_rows($db_daten);
        
        if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
        echo '<tr class="'.$bg.'" style="text-align: center;"><td style="text-align: left;">';
        echo '<img id="dp'.$aid.'" title="'.$atext.'" style="vertical-align: middle;" src="'.$ums_gpfad.'g/'.$ums_rasse.'_hilfe.gif" border="0">';
        echo ' <b>'.$aname.'</b>';
		echo '<br>'.$lager.': '.$energie;
		if($wiederholbar)echo '<br>Die Mission ist wiederholbar.';
		echo '</td>';
        
        echo '<td>'.$koordinaten.'</td>';
		echo '<td>'.$fleetanz.'</td>';
        
        echo '<td>'.$status.'</td>';
        echo '</tr>';
      }//ende ticküberprüfung
    }

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
/*
  }//ende seite mit den datenpaketen
  elseif($_REQUEST["mp"]==2)
  {
    echo '<br><table width=600><tr>
    <td width="50%\" class="cl"><a href="archeology.php?mp=1">'.$archeology_lang[datenanalyse].'</a></td>
	<td width="50%\" class="cl"><a href="archeology.php?mp=2"><b>>> '.$archeology_lang[archaeologen].'</b></a></td>
	</tr>
    <tr>
    <td colspan="2" class="cl">'.$archeology_lang[archisbauen].'</td>
    </tr>
    </table><br>';
    
    //baumenü anzeigen
    echo '<input type="hidden" name="mp" value="2">';
    echo'
    <table border="0" cellpadding="0" cellspacing="0">
    <tr height="37">
    <td width="13" height="37" class="rol">&nbsp;</td>
    <td colspan="8" align="center" class="ro"><div class="cellu">'.$archeology_lang[archaeologen].'</div></td>
    <td width="13" class="ror">&nbsp;</td>
    </tr>
    <tr>
    <td width="13" class="rl">&nbsp;</td>
    <td colspan="8">
    <table border="0" cellpadding="0" cellspacing="1" width="100%">
    <colgroup>
    <col width="100">
    <col width="50">
    <col width="50">
    <col width="50">
    <col width="50">
    <col width="65">
    <col width="50">
    <col width="75">
    </colgroup>';


    echo "<tr>";
    echo '<td class="tc">'.$archeology_lang[einheit].':</td>';
    echo '<td class="tc">M</td>';
    echo '<td class="tc">D</td>';
    echo '<td class="tc">I</td>';
    echo '<td class="tc">E</td>';
    echo '<td class="tc">'.$archeology_lang[wochen].'</td>';
    echo '<td class="tc">'.$archeology_lang[stueck].'</td>';
    echo '<td class="tc">'.$archeology_lang[ausbilden].'</td>';
    echo "</tr>";

    echo "<tr>";
    echo '<td class="cc">'.$archeology_lang[archaeologe].'</td>';
    echo '<td class="cc">'.$ausbildungskosten[0]."</td>";
    echo '<td class="cc">'.$ausbildungskosten[1]."</td>";
    echo '<td class="cc">'.$ausbildungskosten[2]."</td>";
    echo '<td class="cc">'.$ausbildungskosten[3]."</td>";
    echo '<td class="cc">'.$ausbildungszeit."</td>";
    echo '<td class="cc">'.$archi."</td>";
    echo '<td class="cc"><input type="text" name="ausbanz" value="" size="3" maxlength="5"></td>';
    echo "</tr>";

    echo '</table>
    </td>
    <td width="13" class="rr">&nbsp;</td>
    </tr>';

    echo '
    <tr height="37">
    <td width="13" height="37" class="rl">&nbsp;</td>
    <td align="center" colspan="8"><input type="Submit" name="ausbilden" value="'.$archeology_lang[ausbilden2].'"></td>
    <td width="13" class="rr">&nbsp;</td>
    <tr>
    <td width="13" class="rul">&nbsp;</td>
    <td class="ru" colspan="8">&nbsp;</td>
    <td width="13" class="rur">&nbsp;</td>
    </tr>
    </table>
    <br>';

    //aktive bauaufträge
    $result=mysql_query("SELECT anzahl, verbzeit FROM de_user_build WHERE user_id='$ums_user_id' AND tech_id=1001 ORDER BY verbzeit ASC",$db);
    $num = mysql_num_rows($result);

    if ($num>0)
    {
      echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
      echo '<tr>';
      echo '<td class="tc" width="100%">'.$archeology_lang[aktive].'</td>';
      echo '</tr>';
      echo '</table>';
      echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
      echo '<tr>';
      echo '<td class="tc" width="60%">'.$archeology_lang[einheit].'</td>';
      echo '<td class="tc" width="20%">'.$archeology_lang[anzahl].'</td>';
      echo '<td class="tc" width="20%">'.$archeology_lang[wochen].'</td>';
      echo '</tr>';
      echo '</table>';

      while($row = mysql_fetch_array($result)) //jeder gefundene datensatz wird geprueft
      {
        echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
        echo '<tr>';
        echo '<td class="cc" width="60%" align="center">'.$archeology_lang[archaeologe].'</td>';
        echo '<td class="cc" width="20%" align="center">'.number_format($row["anzahl"], 0,"",".").'</td>';
        echo '<td class="cc" width="20%" align="center">'.$row["verbzeit"].'</td>';
        echo '</tr>';
        echo '</table>';
      }
    }
  }//ende seite archäologenausbildung
*/
}
echo '</form>';
?>
</body>
</html>
<?php
/*
INSERT INTO `de_tech_data1` VALUES (29, 'Archäologiezentrum', 20000, 10000, 50000, 6000, 2, 28, 21400, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.');
INSERT INTO `de_tech_data2` VALUES (29, 'Archäologiehort', 24000, 8000, 54000, 4000, 2, 24, 21400, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.');
INSERT INTO `de_tech_data3` VALUES (29, 'Archäologiebau', 28000, 5000, 42000, 10000, 2, 28, 21400, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.');
INSERT INTO `de_tech_data4` VALUES (29, 'Archäologiestock', 16000, 12000, 46000, 8000, 2, 30, 21400, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.');
INSERT INTO `de_tech_data5` VALUES (29, 'Archäologie-KT-TCK', 22000, 9000, 52000, 5000, 2, 10, 21400, '4', 'Dieses Geb&auml;ude dient zur Ausbildung von Arch&auml;ologen und zur Analyse der DX61a23-Datenpakete.');
*/
?>
