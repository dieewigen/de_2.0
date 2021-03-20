<?php
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
// fraktionsdatenmen�
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

echo '<div class="menurahmen" style="text-align: center; margin-top: 26px;">
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=systempage"><div class="b1">zur&uuml;ck</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=toplistpage"><div class="b1">Rangliste</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=statisticspage"><div class="b1">Statistiken</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=3"><div class="b1">Siegbedingungen</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=0"><div class="b1">Kolonien</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=1"><div class="b1">Geb&auml;ude</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=2"><div class="b1">Bao-Nada-Skala</div></a></div>
<div style="float: left; margin-top: 3px; margin-left: 2px;"><a href="sou_main.php?action=showdatapage&styp=4"><div class="b1">Systeminfo</div></a></div>
</div>';
rahmen0_unten();


//daten zur ansicht
echo '<br>';

echo '<div align="center">';

rahmen0_oben();
echo '<div class="cell1">';
echo '<br>';

unset($fraktionswerte);
$fraktionsstati[0]='Beh&uuml;tetes Volk';
$fraktionsstati[1]='Junges Volk';
$fraktionsstati[2]='Bewahrer des Gleichgewichts';
$fraktionsstati[3]='St&ouml;rer des Gleichgewichts';
$fraktionsstati[4]='Zerst&ouml;rer des Gleichgewichts';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   spielerstatistik
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
rahmen1_oben('<div align="center"><b>Aktive Spieler (innerhalb der letzten 7 Tage)</b></div>');

echo '<table>';
$gesamtuser=0;
$ausgabe='';
$array_data='';
$zeitgrenze=time()-24*3600*7;
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT user_id FROM `sou_user_data` WHERE fraction='$i' AND lastclick>'$zeitgrenze' AND owner_id > 0 GROUP BY owner_id",$soudb);
  $num = mysql_num_rows($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($num, 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$num;
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtuser+=$num;
}

//$array_data = "12328522*14509067*5236880*11149552";
$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtuser, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   spielerstatistik nach Aktivität
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Aktive Charaktere/Schiffe (innerhalb der letzten 7 Tage)</b></div>');

echo '<table>';
$gesamtuser=0;
$ausgabe='';
$array_data='';
$zeitgrenze=time()-24*3600*7;
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT count(*) AS fraction FROM `sou_user_data` WHERE fraction='$i' AND lastclick>'$zeitgrenze'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["fraction"], 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["fraction"];
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtuser+=$row["fraction"];
  $fraktionswerte[$i]+=$row["fraction"];
}

$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtuser, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   sonnensysteme der fraktion
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Kontrollierte Sonnensysteme</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';
$legende_data='';
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT count(*) AS wert FROM `sou_map` WHERE fraction='$i'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["wert"], 0,"",".").'</font><br>';
  //if($array_data!='')$array_data.=',';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["wert"];
  
  if($legende_data!='')$legende_data.=',';
  $legende_data.='"Fraktion '.$i.': '.$row["wert"].'"';
  
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtwert+=$row["wert"];
  $fraktionswerte[$i]+=$row["wert"];
}

//$array_data = "12328522*14509067*5236880*11149552";

$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
//echo '<tr align="left"><td width="800"></div></td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';


/*
echo '  <div id="holder"></div>
        <script src="js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/g.raphael-min.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/g.pie-min.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" charset="utf-8">
            window.onload = function () {
            //function draw_pie() {
                var r = Raphael("holder");
                r.g.txtattr.font = "12px Fontin Sans, Fontin-Sans, sans-serif";
                r.g.txtattr.legendcolor = "#00ff00";
               
                var pie = r.g.piechart(160, 120, 100, ['.$array_data.'], {colors: ["'.$colors_text[0].'","'.$colors_text[1].'","'.$colors_text[2].'","'.$colors_text[3].'","'.$colors_text[4].'","'.$colors_text[5].'"], legend: ['.$legende_data.'], legendcolor: "#FFFFFF", legendpos: "east", href: [""]});
                pie.hover(function () {
                    this.sector.stop();
                    this.sector.scale(1.1, 1.1, this.cx, this.cy);
                    if (this.label) {
                        this.label[0].stop();
                        this.label[0].scale(1.5);
                        this.label[1].attr({"font-weight": 800});
                    }
                }, function () {
                    this.sector.animate({scale: [1, 1, this.cx, this.cy]}, 500, "bounce");
                    if (this.label) {
                        this.label[0].animate({scale: 1}, 500, "bounce");
                        this.label[1].attr({"font-weight": 400});
                    }
                });
                
            };
            draw_pie();
        </script>
        
        ';
*/
//echo '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';

rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   bekannte sektoren
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Erkundete Sektoren</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT count(*) AS wert FROM `sou_map_known` WHERE fraction='$i'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["wert"], 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["wert"];
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtwert+=$row["wert"];
  $fraktionswerte[$i]+=$row["wert"];
}

//$array_data = "12328522*14509067*5236880*11149552";
$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   sektorraumbasen
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Sektorraumbasen</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT count(*) AS wert FROM `sou_map_base` WHERE fraction='$i'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["wert"], 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["wert"];
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtwert+=$row["wert"];
  $fraktionswerte[$i]+=$row["wert"];
}

$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';



///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   vollende forschungen
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Abgeschlossene Forschungsprojekte</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';
for($i=1;$i<=6;$i++)
{ 
  $feldname='f'.$i.'lvl';
  $db_daten=mysql_query("SELECT count(*) AS wert FROM `sou_frac_techs` WHERE $feldname='1'",$soudb);
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["wert"], 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["wert"];
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtwert+=$row["wert"];
  $fraktionswerte[$i]+=$row["wert"];
}

//$array_data = "12328522*14509067*5236880*11149552";
$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   vollendete geb�udestufen
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Vorhandene Geb&auml;udestufen</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';
for($i=1;$i<=6;$i++)
{ 
  $db_daten=mysql_query("SELECT SUM(sou_map_buildings.level) AS wert FROM `sou_map_buildings` 
  LEFT JOIN sou_map ON(sou_map.id = sou_map_buildings.owner_id) WHERE sou_map.fraction='$i'",$soudb);
  
  $row = mysql_fetch_array($db_daten);
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($row["wert"], 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$row["wert"];
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtwert+=$row["wert"];
  $fraktionswerte[$i]+=$row["wert"];
}

//$array_data = "12328522*14509067*5236880*11149552";
$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   zastariverteilung
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
/*
rahmen1_oben('<div align="center"><b>Zastariverteilung</b></div>');

echo '<table>';
$gesamtmoney=0;
$ausgabe='';
$array_data='';
//geld in der fraktionskasse
$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
$fkm[0]=$row["f1money"];
$fkm[1]=$row["f2money"];
$fkm[2]=$row["f3money"];
$fkm[3]=$row["f4money"];
$fkm[4]=$row["f5money"];
$fkm[5]=$row["f6money"];

for($i=1;$i<=6;$i++)
{ 
  //geld der b�rger
  $db_daten=mysql_query("SELECT SUM(money) AS money FROM `sou_user_data` WHERE fraction='$i'",$soudb);
  $row = mysql_fetch_array($db_daten);
  $money=$row["money"];
  $money+=$fkm[$i-1];
  
  //die hinteren stellen l�schen um weniger �berwachung zu erm�glchen
  $teiler='1';
  for($s=0;$s<strlen(strval($money))-2;$s++)
  {
  	$teiler.='0';
  }
  $money=round($money/$teiler);
  $money=$money*$teiler;
  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': ca. '.number_format($money, 0,"",".").'</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=$money;
  //echo '<tr align="center"><td>Fraktion '.$i.'</td><td>'.$row["fraction"].'</td></tr>';
  $gesamtmoney+=$money;
  $fraktionswerte[$i]+=round($money/1000000);
}

//$array_data = "12328522*14509067*5236880*11149552";
$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtmoney, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';

*/
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   upgrade-o-modul-upgrades
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Bergbaumodul-Upgrades (Upgrade-O-Modul) der aktiven Raumschiffe</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';

for($i=1;$i<=6;$i++)
{ 
	$zeitgrenze=time()-24*3600*7;
	
	//upgrades
	$db_daten=mysql_query("SELECT SUM(sou_ship_module.canmineuom) AS wert FROM `sou_ship_module` 
  		LEFT JOIN sou_user_data ON(sou_user_data.user_id = sou_ship_module.user_id) WHERE sou_user_data.fraction='$i' AND sou_user_data.lastclick>'$zeitgrenze' 
  		AND sou_ship_module.location=0",$soudb);
	$row = mysql_fetch_array($db_daten);
  	$wert=$row["wert"];
  
  	$ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.number_format($wert, 0,"",".").'</font><br>';
  	if($array_data!='')$array_data.='*';
  	$array_data.=($wert+1);
  	$gesamtwert+=$wert;
}

$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';


///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   Angriffe(Ansehen) auf das "eigene" Gebiet
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

rahmen1_oben('<div align="center"><b>Aktionen bzgl. Ansehen</b></div>');

echo 'Die Werte beziehen sich auf Rohstoffspenden, die das Ansehen von Sonnensystemen in der Ablyon-Galaxie (nicht aber von Raumbasen) beeinflussen. Als Basis wem das Sonnensystem von der Lage her geh&ouml;rt, wird die Distanz zu den Heimatsystemen herangezogen, wobei dieses nicht mit den von Spielern vereinbarten Grenzen &uuml;bereinstimmen muss. Gewertet werden nur Aktionen die das Ansehen des Angreifers/Angegriffenen betreffen, Aktionen die z.B. von einer anderen helfenden Fraktion gegen&uuml;ber einem Angreifer durchgef&uuml;hrt werden, z&auml;hlen nicht mit hinein.';

echo '<table>';
$gesamtwert=0;
$array_data='';

//alle Werte aus der DB holen und zusammenz�hlen
$db_daten=mysql_query("SELECT * FROM `sou_system`",$soudb);
$row = mysql_fetch_array($db_daten);
unset($wert);
for($i=1;$i<=6;$i++){ 
	
	//berechnen wie stark die Fraktion angegriffen wird
	for($r=1;$r<=6;$r++){ 
		$wert[$i]+=$row["a".$r.$i];
	}
	$gesamtwert+=$wert[$i];
	//echo '<br>'.$wert[$i];
}

$ausgabe='<table width="100%">';
$ausgabe.='<tr><td>Zielfraktion</td><td align="center">Verursacher</td></tr>';
for($i=1;$i<=6;$i++){
	//verursacher berechnen
	//unset($vwert);
	$verursacher='';
	for($r=1;$r<=6;$r++){ 
		//$vwert[$i]+=$row["a".$i.$r];
		if($row["a".$r.$i]*100/$gesamtwert>0)
			$verursacher.='<font color="#'.$colors_text[$r-1].'">F'.$r.': '.number_format($row["a".$r.$i]*100/$gesamtwert, 0,"",".").'%</font> ';
	}
	
  	$ausgabe.='<tr><td><font color="#'.$colors_text[$i-1].'">F'.$i.': '.number_format($wert[$i]*100/$gesamtwert, 0,"",".").'%</font></td><td align="center">'.$verursacher.'</td></tr>';
  	if($array_data!='')$array_data.='*';
  	$array_data.=($wert[$i]+0.0001);
}
$ausgabe.='</table>';
//$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400px">'.$link.'</td><td width="400px">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';


///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//   fraktionseinteilung da bao-nada
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
/*
rahmen1_oben('<div align="center"><b>Fraktionsstatus nach der Bao-Nada-Skala</b></div>');

echo '<table>';
$gesamtwert=0;
$ausgabe='';
$array_data='';

$gesamtwert=$fraktionswerte[1]+$fraktionswerte[2]+$fraktionswerte[3]+$fraktionswerte[4]+$fraktionswerte[5]+$fraktionswerte[6];

$mittelwert=intval($gesamtwert/6);

for($i=1;$i<=6;$i++)
{ 
  $fraktionsstatus=2;

  if($fraktionswerte[$i]*100/$mittelwert>=125)$fraktionsstatus=3;
  if($fraktionswerte[$i]*100/$mittelwert>=175)$fraktionsstatus=4;

  if($fraktionswerte[$i]*100/$mittelwert<=75)$fraktionsstatus=1;
  if($fraktionswerte[$i]*100/$mittelwert<=50)$fraktionsstatus=0;

  
  $ausgabe.='<font color="#'.$colors_text[$i-1].'">Fraktion '.$i.': '.$fraktionsstati[$fraktionsstatus].' '.number_format(intval($fraktionswerte[$i]*100/$mittelwert), 0,"",".").'%</font><br>';
  if($array_data!='')$array_data.='*';
  $array_data.=(intval($fraktionswerte[$i]*100/$mittelwert)+1);
}

//$ausgabe.='<br><b>Gesamt<b>: '.number_format($gesamtwert, 0,"",".").'<br>';
//echo '<tr align="center"><td width="200">Gesamt</td><td width="200">'.$gesamtuser.'</td></tr>';
$link='<img src="showpic.php?data='.$array_data.'">';
echo '<tr align="left"><td width="400">'.$link.'</td><td width="200">'.$ausgabe.'</td></tr>';
echo '</table>';

rahmen1_unten();

echo '<br>';


rahmen0_unten();

echo '<br>';
*/
echo '</div>';//cell-div
echo '</div>';//center-div

die('</body></html>');
?>