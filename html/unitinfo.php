<?php
include "inc/header.inc.php";
include "inc/schiffsdaten.inc.php";
include "functions.php";
include "tickler/kt_einheitendaten.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, techs, newtrans, newnews, design3 AS design, sc2, spec1, spec3 FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
$row = mysqli_fetch_assoc($db_daten);
$restyp01=$row["restyp01"];$restyp02=$row["restyp02"];$restyp03=$row["restyp03"];$restyp04=$row["restyp04"];
$restyp05=$row["restyp05"];$punkte=$row["score"];$techs=$row["techs"];
$newtrans=$row["newtrans"];$newnews=$row["newnews"];$sector=$row["sector"];$system=$row["system"];
$design=$row["design"];$mysc2=$row["sc2"];
$gr01=$restyp01;$gr02=$restyp02;$gr03=$restyp03;$gr04=$restyp04;$gr05=$restyp05;
$spec1=$row['spec1'];$spec3=$row['spec3'];

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Einheiteninformationen</title>
<?php include "cssinclude.php";

echo '</head>';
echo '<body class="theme-rasse'.$_SESSION['ums_rasse'].' '.(($_SESSION['ums_mobi']==1) ? 'mobile' : 'desktop').'">';
//stelle die ressourcenleiste dar
include "resline.php";

echo einheiten_navi('unitinfo');


//zuerst mal alle Einheitennamen aus der DB laden und in ein Array packen
$techdata=array();
$sql="SELECT * FROM de_tech_data WHERE tech_id>=81 AND tech_id<=104 ORDER BY tech_id ASC";
$db_daten=mysqli_execute_query($GLOBALS['dbi'], $sql);
//echo $sql;
while($row = mysqli_fetch_assoc($db_daten)){
	$techdata[1][$row['tech_id']]['tech_name']=getTechNameByRasse($row['tech_name'],1);
	$techdata[2][$row['tech_id']]['tech_name']=getTechNameByRasse($row['tech_name'],2);
	$techdata[3][$row['tech_id']]['tech_name']=getTechNameByRasse($row['tech_name'],3);
	$techdata[4][$row['tech_id']]['tech_name']=getTechNameByRasse($row['tech_name'],4);
}


//Klassenname
$klassenname[]='J&auml;ger';
$klassenname[]='Jagdboot';
$klassenname[]='Zerst&ouml;rer';
$klassenname[]='Kreuzer';
$klassenname[]='Schlachtschiff';
$klassenname[]='Bomber';
$klassenname[]='Transmitterschiff';
$klassenname[]='Tr&auml;ger';
$klassenname[]='Frachter';
$klassenname[]='Titan';
$klassenname[]='Orbitalj&auml;ger-Basis';
$klassenname[]='Flugk&ouml;rper-Plattform';
$klassenname[]='Energiegeschoss-Plattform';
$klassenname[]='Materiegeschoss-Plattform';
$klassenname[]='Hochenergiegeschoss-Plattform';

//Reihenfolge aus Kampf- oder Blockmatrix: Zielklasse und Effizienz nacheinander;
//die Jäger-Klasse (0) erscheint nur einmal; $nach_anzeige: Merker erst nach einer Anzeige setzen (Blockreihenfolge)
function einheiten_reihenfolge($matrix, $unit_id, $klassenname, $nach_anzeige){
	$effizienz=100;
	$jaegerwar=false;
	$html='';
	for($x=0;$x<=14;$x++){
		$effizienz=$effizienz-($effizienz/100*$matrix[$unit_id][$x*2+1]);
		if($matrix[$unit_id][$x*2]!=0 || ($matrix[$unit_id][$x*2]==0 && $jaegerwar==false)){
			//eine Zeile je Zielklasse, der Balken zeigt die abnehmende Effizienz
			$html.='<div class="einh-folge"><span>'.$klassenname[$matrix[$unit_id][$x*2]].'</span><span class="einh-folge-balken"><span style="width: '.max(0, min(100, round($effizienz))).'%;"></span></span><b>'.number_format($effizienz, 0,",",".").' %</b></div>';
			if($nach_anzeige && $matrix[$unit_id][$x*2]==0){
				$jaegerwar=true;
			}
		}
		if(!$nach_anzeige && $matrix[$unit_id][$x*2]==0){
			$jaegerwar=true;
		}
	}
	return $html;
}

//Werte einer Zeile für die fünf Rassen, die eigene Rasse hervorgehoben
function einheiten_wertezeile($label, $werte){
	$html='<span class="einh-label">'.$label.'</span>';
	foreach($werte as $r => $wert){
		$html.='<span class="einh-wert'.($r+1==$_SESSION['ums_rasse'] ? ' einh-eigen' : '').'">'.$wert.'</span>';
	}
	return $html;
}

$rassenlogos='<span class="einh-label"></span>';
for($r=1;$r<=5;$r++){
	$rassenlogos.='<span class="einh-wert'.($r==$_SESSION['ums_rasse'] ? ' einh-eigen' : '').'"><img src="gp/g/derassenlogo'.$r.'.png" alt=""></span>';
}

$sprungleiste='';
$karten='';
for($i=81;$i<=104;$i++){
	if($i<100){
		$unit_id=$i-81;
	}else{
		$unit_id=$i-90;
	}

	if(($i>=81 & $i<=90) || ($i>=100 & $i<=104)){
		$sprungleiste.='<a href="#klasse'.$unit_id.'" class="mod-chip">'.$klassenname[$unit_id].'</a>';

		//Namen je Rasse, die DX61a23 haben keine eigenen
		$namen=array();
		$punkte=array();
		$treffer=array();
		$konv=array();
		$emp=array();
		for($r=0;$r<5;$r++){
			$namen[]=($r<4) ? $techdata[$r+1][$i]['tech_name'] : '&ndash;';
			$punkte[]=number_format($unit[$r][$unit_id][4], 0,",",".");
			$treffer[]=number_format($unit[$r][$unit_id][1], 0,",",".");
			$konv[]=number_format($unit[$r][$unit_id][2], 2,",",".");
			$emp[]=number_format($unit[$r][$unit_id][3], 2,",",".");
		}

		$reihenfolgen='';
		//Angriffsreihenfolge der konventionellen Waffen
		if($unit[0][$unit_id][2]>0 || $unit[1][$unit_id][2]>0 || $unit[2][$unit_id][2]>0 || $unit[3][$unit_id][2]>0 || $unit[4][$unit_id][2]>0){
			$reihenfolgen.='<div class="einh-reihe"><span class="mod-typ">Angriffsreihenfolge</span><span class="mod-typ">Effizienz</span>'.einheiten_reihenfolge($kampfmatrix, $unit_id, $klassenname, false).'</div>';
		}
		//Blockreihenfolge der EMP-Waffen
		if($unit[0][$unit_id][3]>0 || $unit[1][$unit_id][3]>0 || $unit[2][$unit_id][3]>0 || $unit[3][$unit_id][3]>0 || $unit[4][$unit_id][3]>0){
			$reihenfolgen.='<div class="einh-reihe"><span class="mod-typ">Blockreihenfolge</span><span class="mod-typ">Effizienz</span>'.einheiten_reihenfolge($blockmatrix, $unit_id, $klassenname, true).'</div>';
		}
		//Angriffs- und Blockreihenfolge nebeneinander
		if($reihenfolgen!=''){
			$reihenfolgen='<div class="einh-reihen">'.$reihenfolgen.'</div>';
		}

		$karten.='
		<div class="einh-karte" id="klasse'.$unit_id.'">
			<div class="einh-titel">'.$klassenname[$unit_id].'</div>
			<div class="einh-werte">
				'.$rassenlogos.'
				'.einheiten_wertezeile('Name', $namen).'
				'.einheiten_wertezeile('Punktewert', $punkte).'
				'.einheiten_wertezeile('Trefferpunkte', $treffer).'
				'.einheiten_wertezeile('Konventionelle Waffen', $konv).'
				'.einheiten_wertezeile('EMP-Waffen', $emp).'
			</div>
			'.$reihenfolgen.'
		</div>';
	}
}

rahmen_oben('Einheiteninformationen');
echo '<div class="einh mod">';
echo '<div class="einh-sprung">'.$sprungleiste.'</div>';
echo $karten;
echo '</div>';
rahmen_unten();

?>
<br>

</body>
</html>
