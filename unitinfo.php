<?php
include "inc/header.inc.php";
include "inc/schiffsdaten.inc.php";
include "functions.php";
include "tickler/kt_einheitendaten.php";

$sql = "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, techs, newtrans, newnews, design3 AS design, sc2, spec1, spec3 FROM de_user_data WHERE user_id=?";
$db_daten = mysqli_execute_query($GLOBALS['dbi'], $sql, [$ums_user_id]);
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
//stelle die ressourcenleiste dar
include "resline.php";

echo '
<a href="production.php" title="Einheitenproduktion"><img src="'.$ums_gpfad.'g/symbol19.png" border="0" width="64px" heigth="64px"></a> 
<a href="recycling.php" title="Recycling&Hier k&ouml;nnen Einheiten der Heimatflotte und Verteidigungseinheiten recycelt werden."><img src="'.$ums_gpfad.'g/symbol24.png" border="0" width="64px" heigth="64px"></a>';
if($sv_deactivate_vsystems ?? 0 !=1){
	echo '<a href="specialship.php" title="Basisstern"><img src="'.$ums_gpfad.'g/symbol27.png" border="0" width="64px" heigth="64px"></a>';
}
echo'
<a href="unitinfo.php" title="Einheiteninformationen"><img src="'.$ums_gpfad.'g/symbol26.png" border="0" width="64px" heigth="64px"></a>
';


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

hmen_oben('Einheiteninformationen');
echo '<div style="width: 576px; position: relative; font-size: 10px; text-align: center;">';
echo '<table style="width: 100%; font-size: 10px;">';
for($i=81;$i<=104;$i++){
	if($i<100){
		$unit_id=$i-81;
	}else{
		$unit_id=$i-90;
	}		
	
	if(($i>=81 & $i<=90) || ($i>=100 & $i<=104)){
		//Klassenname
		$c1=0;
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}		
		echo '<tr class="'.$bg.'"><td colspan="5" style="font-weight: bold; font-size: 24px;">'.$klassenname[$unit_id].'</td></tr>';
		
		//�berschrift
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}		
		echo '
			<tr class="'.$bg.'">
				<td></td>
				<td><img src="'.$ums_gpfad.'g/derassenlogo1.png" border="0" width="16px" heigth="16px"></td>
				<td><img src="'.$ums_gpfad.'g/derassenlogo2.png" border="0" width="16px" heigth="16px"></td>
				<td><img src="'.$ums_gpfad.'g/derassenlogo3.png" border="0" width="16px" heigth="16px"></td>
				<td><img src="'.$ums_gpfad.'g/derassenlogo4.png" border="0" width="16px" heigth="16px"></td>
			</tr>';
		
		//Punkte
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
		echo '
			<tr class="'.$bg.'">
				<td>Punktewert</td>
				<td>'.number_format($unit[0][$unit_id][4], 0,",",".").'</td>
				<td>'.number_format($unit[1][$unit_id][4], 0,",",".").'</td>
				<td>'.number_format($unit[2][$unit_id][4], 0,",",".").'</td>
				<td>'.number_format($unit[3][$unit_id][4], 0,",",".").'</td>
			</tr>';
		
		//Trefferpunkte
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
		echo '
			<tr class="'.$bg.'">
				<td>Trefferpunkte</td>
				<td>'.number_format($unit[0][$unit_id][1], 0,",",".").'</td>
				<td>'.number_format($unit[1][$unit_id][1], 0,",",".").'</td>
				<td>'.number_format($unit[2][$unit_id][1], 0,",",".").'</td>
				<td>'.number_format($unit[3][$unit_id][1], 0,",",".").'</td>
			</tr>';

		//EMP-Waffen
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
		echo '
			<tr class="'.$bg.'">
				<td>EMP-Waffen</td>
				<td>'.number_format($unit[0][$unit_id][3], 2,",",".").'</td>
				<td>'.number_format($unit[1][$unit_id][3], 2,",",".").'</td>
				<td>'.number_format($unit[2][$unit_id][3], 2,",",".").'</td>
				<td>'.number_format($unit[3][$unit_id][3], 2,",",".").'</td>
			</tr>';
		
		//Blockreihenfolge
		if($unit[0][$unit_id][3]>0 || $unit[1][$unit_id][3]>0 || $unit[2][$unit_id][3]>0 || $unit[3][$unit_id][3]>0){
			echo '
				<tr class="'.$bg.'" style="font-style:italic;">
					<td colspan="3">Blockreihenfolge</td>
					<td colspan="2">Effizienz</td>
				</tr>';		
			$effizienz=100;
			$jaegerwar=false;
			for($x=0;$x<=14;$x++){
				$effizienz=$effizienz-($effizienz/100*$blockmatrix[$unit_id][$x*2+1]);
				if($blockmatrix[$unit_id][$x*2]!=0 || ($blockmatrix[$unit_id][$x*2]==0 && $jaegerwar==false)){
					echo '
						<tr class="'.$bg.'">
							<td colspan="3">'.$klassenname[$blockmatrix[$unit_id][$x*2]].'</td>
							<td colspan="2">'.number_format($effizienz, 0,",",".").'%</td>
						</tr>';
					if($blockmatrix[$unit_id][$x*2]==0){
						$jaegerwar=true;
					}
				}
			}
		}
		
		//Konventionelle Waffen
		if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
		echo '
			<tr class="'.$bg.'">
				<td>Konventionelle Waffen</td>
				<td>'.number_format($unit[0][$unit_id][2], 2,",",".").'</td>
				<td>'.number_format($unit[1][$unit_id][2], 2,",",".").'</td>
				<td>'.number_format($unit[2][$unit_id][2], 2,",",".").'</td>
				<td>'.number_format($unit[3][$unit_id][2], 2,",",".").'</td>
			</tr>';

		//Angriffsreihenfolgen konv. Waffen
		if($unit[0][$unit_id][2]>0 || $unit[1][$unit_id][2]>0 || $unit[2][$unit_id][2]>0 || $unit[3][$unit_id][2]>0){
			echo '
				<tr class="'.$bg.'" style="font-style:italic;">
					<td colspan="3">Angriffsreihenfolge</td>
					<td colspan="2">Effizienz</td>
				</tr>';		
			$effizienz=100;
			$jaegerwar=false;
			for($x=0;$x<=14;$x++){
				$effizienz=$effizienz-($effizienz/100*$kampfmatrix[$unit_id][$x*2+1]);
				if($kampfmatrix[$unit_id][$x*2]!=0 || ($kampfmatrix[$unit_id][$x*2]==0 && $jaegerwar==false)){
					echo '
						<tr class="'.$bg.'">
							<td colspan="3">'.$klassenname[$kampfmatrix[$unit_id][$x*2]].'</td>
							<td colspan="2">'.number_format($effizienz, 0,",",".").'%</td>
						</tr>';		
				}
				
				if($kampfmatrix[$unit_id][$x*2]==0){
					$jaegerwar=true;
				}
			}
		}		
		
	}
}
echo '</table></div>';
rahmen_unten();

?>
</div>
<br>
<?php include "fooban.php"; ?>
</body>
</html>
