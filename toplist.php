<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_toplist.lang.php';
include "functions.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews, allytag, status FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ums_user_id]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];$restyp05=$row["restyp05"];
$punkte=$row["score"];$newtrans=$row["newtrans"];$newnews=$row["newnews"];
$sector=$row["sector"];$system=$row["system"];
if ($row["status"]==1) $ownally = $row["allytag"];
?>
<!doctype html>
<html>
<head>
<title>Rangliste</title>
<?php include "cssinclude.php";?>
</head>
<body>
<?php //stelle die ressourcenleiste dar
include "resline.php";


//Die Gewinner der alten Runden ansehen
if(isset($_GET['show_history']) && $_GET['show_history']==1){

	echo '<a href="toplist.php" class="btn">zur&uuml;ck</a><br><br>';

	
	rahmen_oben('Gewinner der vergangen Runden');
	echo '<div class="cell" style="width: 570px;">';
	$sql = "SELECT * FROM de_server_round_toplist ORDER BY round_id DESC";
	$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql);
	while($row = mysqli_fetch_assoc($db_daten)){
		echo '<div style="text-align: center; font-weight: bold;">Runde '.$row['round_id'].'</div>';
		echo '<div>ERHABENE/ERHABENER: '.$row['player_spielername'].'</div>';
		echo '<div>Koordinaten: '.$row['player_sector'].':'.$row['player_system'].'</div>';

		switch($row['player_rasse']){
			case 1:
				$rasse='Die Ewigen';
			break;

			case 2:
				$rasse='Ishtar';
			break;
			
			case 3:
				$rasse='K&#180;Tharr';
			break;
			
			case 4:
				$rasse='Z&#180;tah-ara';
			break;			

			default:
				$rasse='N/A';
			break;
		}

		echo '<div>Punkte: '.number_format($row['player_score'], 0,"",".").'</div>';
		echo '<div>Rasse: '.$rasse.'</div>';
		echo '<div>Wirtschaftsticks: '.number_format($row['round_wt'], 0,"",".").'</div>';

		echo '<div style="height: 20px;"></div>';

		echo '<div>Sektor: '.$row['sector_id'].'</div>';
		echo '<div>Sektorname: '.$row['sector_name'].'</div>';
		echo '<div>Sektorpunkte: '.number_format($row['sector_score'], 0,"",".").'</div>';

		if(!empty($row['ally_tag'])){
			echo '<div style="height: 20px;"></div>';

			echo '<div>Allianz: '.$row['ally_tag'].'</div>';
			echo '<div>Allianzrundensiegartefakte: '.number_format($row['ally_roundpoints'], 0,"",".").'</div>';		
		}

		echo '<div style="height: 40px;"></div>';
	}

	rahmen_unten();

	echo '</div>';

	die('<body></html>');
}

function showmenu($menuid, $menupos){
	global $toplist_lang, $sv_ewige_runde, $sv_oscar, $sv_hardcore;
	//men�s definieren

	/////////////////////////////
	//spieler
	/////////////////////////////

	//ewige runde?
	if($sv_ewige_runde==1){
		$index=0;
		//punkte
		$menudata[0][$index]['name']=$toplist_lang['punkte'];
		$menudata[0][$index]['dateiname']='top1a.tmp';
		//Kollektoren
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['kollektoren'];
		$menudata[0][$index]['dateiname']='top1b.tmp';
		//Türme
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['tuerme'];
		$menudata[0][$index]['dateiname']='top1c.tmp';
		//Errungenschaften
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['errungenschaften'];
		$menudata[0][$index]['dateiname']='top1g.tmp';
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]['name']='Kopfgeld';
			$menudata[0][$index]['dateiname']='top1e.tmp';				
			//Kopfgeldjänger
			$index++;
			$menudata[0][$index]['name']='Kopfgeldj&auml;ger';
			$menudata[0][$index]['dateiname']='top1f.tmp';		
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['erhabenenpunkte'];
		$menudata[0][$index]['dateiname']='top1h.tmp';		
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren * 0,75 + Einheitenpunkte / 250.000 + Errungenschaften + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000 + Z&ouml;llner / 10.000';
		//Erhabenencounter
		$index++;
		$menudata[0][$index]['name']='Erhabenencounter';
		$menudata[0][$index]['dateiname']='top1i.tmp';		
		//Erhabenensiege
		$index++;
		$menudata[0][$index]['name']='Erhabenensiege';
		$menudata[0][$index]['dateiname']='top1j.tmp';
		//Executor-Punkte
		$index++;
		$menudata[0][$index]['name']='Executorpunkte';
		$menudata[0][$index]['dateiname']='top1k.tmp';
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}elseif($sv_hardcore==1){
		$index=0;
		//punkte
		$menudata[0][$index]['name']=$toplist_lang['punkte'];
		$menudata[0][$index]['dateiname']='top1a.tmp';
		//Kollektoren
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['kollektoren'];
		$menudata[0][$index]['dateiname']='top1b.tmp';
		//T�rme
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['tuerme'];
		$menudata[0][$index]['dateiname']='top1c.tmp';
		//Rundenpunkte
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['rundenpunkte'];
		$menudata[0][$index]['dateiname']='top1d.tmp';
		//Errungenschaften
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['errungenschaften'];
		$menudata[0][$index]['dateiname']='top1g.tmp';
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]['name']='Kopfgeld';
			$menudata[0][$index]['dateiname']='top1e.tmp';				
			//Kopfgeldj�nger
			$index++;
			$menudata[0][$index]['name']='Kopfgeldj&auml;ger';
			$menudata[0][$index]['dateiname']='top1f.tmp';
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['erhabenenpunkte'];
		$menudata[0][$index]['dateiname']='top1h.tmp';		
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren + Einheitenpunkte / 250.000 + Errungenschaften + Rundenpunkte + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000';
		//Erhabenencounter
		$index++;
		$menudata[0][$index]['name']='Erhabenencounter';
		$menudata[0][$index]['dateiname']='top1i.tmp';		
		//Erhabenensiege
		$index++;
		$menudata[0][$index]['name']='Erhabenenteilsiege';
		$menudata[0][$index]['dateiname']='top1j.tmp';
		//Executor-Punkte
		$index++;
		$menudata[0][$index]['name']='Executorpunkte';
		$menudata[0][$index]['dateiname']='top1k.tmp';
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}else{ //normale runde
		$index=0;
		//punkte
		$menudata[0][$index]['name']=$toplist_lang['punkte'];
		$menudata[0][$index]['dateiname']='top1a.tmp';
		//Kollektoren
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['kollektoren'];
		$menudata[0][$index]['dateiname']='top1b.tmp';
		//T�rme
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['tuerme'];
		$menudata[0][$index]['dateiname']='top1c.tmp';
		//Rundenpunkte
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['rundenpunkte'];
		$menudata[0][$index]['dateiname']='top1d.tmp';
		//Errungenschaften
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['errungenschaften'];
		$menudata[0][$index]['dateiname']='top1g.tmp';
		if($sv_oscar!=1){
			//Kopfgeld
			$index++;
			$menudata[0][$index]['name']='Kopfgeld';
			$menudata[0][$index]['dateiname']='top1e.tmp';				
			//Kopfgeldj�nger
			$index++;
			$menudata[0][$index]['name']='Kopfgeldj&auml;ger';
			$menudata[0][$index]['dateiname']='top1f.tmp';		
		}
		//Erhabenenpunkte
		$index++;
		$menudata[0][$index]['name']=$toplist_lang['erhabenenpunkte'];
		$menudata[0][$index]['dateiname']='top1h.tmp';		
		$menudata[0][$index]['hinweis']='Die Erhabenenpunkte berechnen sich nach folgender Formel: Kollektoren + Einheitenpunkte / 250.000 + Errungenschaften + Rundenpunkte + Flottenangriffserfahrung / 12.500 + Flottenverteidigungserfahrung / 10.000';
		//Executor-Punkte
		$index++;
		$menudata[0][$index]['name']='Executorpunkte';
		$menudata[0][$index]['dateiname']='top1k.tmp';
		$menudata[0][$index]['hinweis']='Die Executorpunkte berechnen sich nach folgender Formel: Geb&auml;udepunkte (Vergessene-Systeme) + Handelspunkte / 100 (vorl&auml;ufig, wird noch ge&auml;ndert)';
	}

	/////////////////////////////
	//allianz
	/////////////////////////////
	if($sv_ewige_runde==1){
		$index=0;
		//punkte
		$menudata[2][$index]['name']=$toplist_lang['punkte'];
		$menudata[2][$index]['dateiname']='top3.tmp';
		//Siegartefakte
		$index++;
		$menudata[2][$index]['name']=$toplist_lang['siegartefakte'];
		$menudata[2][$index]['dateiname']='top3a.tmp';
		//B�ndnisse
		$index++;
		$menudata[2][$index]['name']=$toplist_lang['buendnisse'];
		$menudata[2][$index]['dateiname']='top3b.tmp';
		//Erhabene
		$index++;
		$menudata[2][$index]['name']='Erhabene';
		$menudata[2][$index]['dateiname']='top3c.tmp';
	}else{
		$index=0;
		//punkte
		$menudata[2][$index]['name']=$toplist_lang['punkte'];
		$menudata[2][$index]['dateiname']='top3.tmp';
		//Siegartefakte
		$index++;
		$menudata[2][$index]['name']=$toplist_lang['siegartefakte'];
		$menudata[2][$index]['dateiname']='top3a.tmp';
		//B�ndnisse
		$index++;
		$menudata[2][$index]['name']=$toplist_lang['buendnisse'];
		$menudata[2][$index]['dateiname']='top3b.tmp';
	}

	echo '<div class="menu_box">';
	echo '<ul id="menu">';
	//////////////////////////////////////////////////////////
	//die einzelnen men�punkte ausgeben
	//////////////////////////////////////////////////////////
	for($i=0;$i<count($menudata[$menuid]);$i++){
		if($menupos==$i){$str1='<b>';$str2='</b>';}else{$str1='';$str2='';}
		echo '<li><a href="toplist.php?s='.($menuid+1).'&mp='.($i).'">'.$str1.$menudata[$menuid][$i]['name'].$str2.'</a></li>';
	}
	echo '</ul>';
	echo '</div><br>';
	
	//ggf. einen Hinweis ausgeben
	if(!empty($menudata[$menuid][$menupos]['hinweis'])){

	  echo '<table border="0" cellpadding="0" cellspacing="1" width="600">';
	  echo '<tr class="cell" align="center"><td>'.$menudata[$menuid][$menupos]['hinweis'].'</td></tr>';
	  echo '</table><br>';
	}
	
	//Datei mit den Daten einbinden
	$filename = "cache/toplist/".$menudata[$menuid][$menupos]['dateiname'];
	include $filename;
}


echo '<div class="cell" style="width: 600px;">';
echo '<table border="0" cellpadding="0" cellspacing="2" width="500">';
echo '<tr align="center">';
echo '<td width="50%"><a href="toplist.php?s=1"><img src="'.$ums_gpfad.'g/'.$sv_server_lang.'_tl1.gif" border="0"></a></td>';
echo '<td width="50%"><a href="toplist.php?&s=2"><img src="'.$ums_gpfad.'g/'.$sv_server_lang.'_tl2.gif" border="0"></a></td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td><a href="toplist.php?&s=3"><img src="'.$ums_gpfad.'g/'.$sv_server_lang.'_tl3.gif" border="0"></a></td>';
echo '<td><a href="toplist.php?&s=4"><img src="'.$ums_gpfad.'g/'.$sv_server_lang.'_tl4.gif" border="0"></a></td>';
echo '</tr>';
echo '</table>';
echo '</div><br>';

if(!isset($_REQUEST["mp"])){
	$_REQUEST["mp"]=0;
}

$s = $_REQUEST["s"] ?? 1;

if ($s==1){
	echo '<script language="JavaScript">
	<!--
	var gpfad="'.$ums_gpfad.'";
	//-->
	</script>';
	
	showmenu(0,$_REQUEST["mp"]);
}

///////////////////////////////////
//sektorenrangliste
///////////////////////////////////
if ($s==2){
  $filename = "cache/toplist/top2.tmp";
  include $filename;
}

///////////////////////////////////
//allianz
///////////////////////////////////
if ($s==3){
	showmenu(2,$_REQUEST["mp"]);
}

///////////////////////////////////
//handel
///////////////////////////////////
if ($s==4){
	$filename = "cache/toplist/top4a.tmp";
	include $filename;
}

?>
<div class="info_box text3" style="margin-bottom: 5px; font-size: 14px;">
	<a href="toplist.php?show_history=1">Daten der vergangenen Runden</a>
</div>

</div>

<br>
<?php include "fooban.php"; ?>
</body>
</html>
