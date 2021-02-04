<?php
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_techtree.lang.php';
include "functions.php";

$db_daten=mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, techs, sector, system, newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);
$restyp01=$row[0];$restyp02=$row[1];$restyp03=$row[2];$restyp04=$row[3];$restyp05=$row[4];
$techs=$row["techs"];$row["sector"];$system=$row["system"];
?>
<!doctype html>
<html>
<head>
<title><?=$techtree_lang[seitentitel]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<div align="center">
<?php
//nur für pa
//gilt jetzt für alle spieler
if($ums_premium==0 AND 1==2)
{
  //kein pa info
  rahmen_oben($techtree_lang[information]);
  echo '<table width="572">';
  echo '<tr align="center"><td class="cell">'.$techtree_lang[keinpa].'</td></tr>';
  echo '</table>';
  rahmen_unten();
}
//techtree ausgeben
else
{
  //alle techs die es von der rasse gibt auslesen und in nen array packen
  $maxid=0;$id=0;unset($ttext);
  if($sv_max_secmoves==0)$db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id<120 AND tech_id<>26 ORDER BY tech_id",$db);
  else $db_daten=mysql_query("SELECT * FROM de_tech_data$ums_rasse WHERE tech_id<120 ORDER BY tech_id",$db);
  while($row = mysql_fetch_array($db_daten))
  {
    $data[$row["tech_id"]][0]=$row["tech_name"];
    $data[$row["tech_id"]][1]=$row["restyp01"];
    $data[$row["tech_id"]][2]=$row["restyp02"];
    $data[$row["tech_id"]][3]=$row["restyp03"];
    $data[$row["tech_id"]][4]=$row["restyp04"];
    $data[$row["tech_id"]][5]=$row["restyp05"];
    $data[$row["tech_id"]][6]=$row["tech_ticks"];
    $data[$row["tech_id"]][7]=$row["score"];
    $data[$row["tech_id"]][8]=$row["tech_vor"];
    //$data[$row["tech_id"]][9]=$row["des"]);
    $data[$row["tech_id"]][10]=$id;
    $data[$row["tech_id"]][11]=0;//baustatus
    $data[$row["tech_id"]][12]=$row["tech_id"];
    //den größten tech_id wert speichern
    if($row["tech_id"]>$maxid)$maxid=$row["tech_id"];
    
    //////////////////////////////////////
    //////////////////////////////////////
    //tooltip bauen
    //////////////////////////////////////
    //////////////////////////////////////
    $i=$row['tech_id'];
    
    $ttext[$i] = $data[$i][0].'&';
    $ttext[$i].= $techtree_lang[typ].' ';
    if($i>=1 AND $i<40)$ttext[$i].= $techtree_lang[typ1];
    if($i>=40 AND $i<80)$ttext[$i].= $techtree_lang[typ2];
    if($i>=81 AND $i<100)$ttext[$i].= $techtree_lang[typ3];
    if($i>=100 AND $i<110)$ttext[$i].= $techtree_lang[typ4];
    if($i>=110 AND $i<120)$ttext[$i].= $techtree_lang[typ5];
      
    if($data[$i][11]==1)$status=$techtree_lang[status1];elseif($data[$i][11]==2)$status=$techtree_lang[status2];elseif($data[$i][11]==3)$status=$techtree_lang[status3];
    $ttext[$i].= '<br>'.$techtree_lang[status].' '.$status;
    $ttext[$i].= '<br>'.$techtree_lang[rohstoffkosten];
    if($data[$i][1]>0) $ttext[$i].= '<br>'.number_format( $data[$i][1], 0,"",".").' M';
    if($data[$i][2]>0) $ttext[$i].= '<br>'.number_format( $data[$i][2], 0,"",".").' D';
    if($data[$i][3]>0) $ttext[$i].= '<br>'.number_format( $data[$i][3], 0,"",".").' I';
    if($data[$i][4]>0) $ttext[$i].= '<br>'.number_format( $data[$i][4], 0,"",".").' E';
    if($data[$i][5]>0) $ttext[$i].= '<br>'.number_format( $data[$i][5], 0,"",".").' T';
    $ttext[$i].= '<br>'.$techtree_lang[bauzeit].' '.$data[$i][6];
    $ttext[$i].= '<br>'.$techtree_lang[punkte].' '.number_format( $data[$i][7], 0,"",".");
    //$ttext.= '<br>'.$techtree_lang[beschreibung].' '.$data[$i][9];
    //echo 'Text['.$data[$i][10].']=["","'.$ttext.'"];';
    
    
    
    
    $id++;
  }

  //zuerst alle voraussetzungen setzen gebaut/baubar/nicht gebaut
  for($i=1;$i<=$maxid;$i++)
  {
    //schauen ob die id belegt ist
    if($data[$i][0]!='')
    {
      $z1=0;$z2=0;
      $vorb=explode(";",$data[$i][8]);
      $v='';$fc='';
      foreach($vorb as $einzelb) //jede einzelne bedingung checken
      {
        $z1++;
        if ($techs[$einzelb]==1){$z2++;}
        if ($einzelb==0) {$z1=0;$z2=0;}
      }

      //echo "Vorbedingung erfüllt";
      //wenn alle voraussetzungen erfüllt sind, dann grün, ansonsten rot
      if ($z1==$z2){$bg='ccg';$data[$i][11]=1;$test=1;}else{$bg='ccr';$data[$i][11]=3;$test=3;}
      //bei gebäuden, forschungen,die "grün" sind, noch schauen, ob sie schon gebaut sind, falls nicht, gelb anzeigen
      if($bg=="ccg" AND $techs[$i]==0 AND $i<80){$bg='ccy';$data[$i][11]=2;$test=2;}
    }  
  }
  //daten auslesen
  $tablestring='';
  for($i=1;$i<=$maxid;$i++)
  {
    //schauen ob die id belegt ist
    if($data[$i][0]!='')
    {
      $z1=0;$z2=0;
      $vorb=explode(";",$data[$i][8]);
      $v='';$fc='';
      foreach($vorb as $einzelb) //jede einzelne bedingung checken
      {
        $z1++;
        if ($techs[$einzelb]==1){$z2++;}
        if ($einzelb==0) {$z1=0;$z2=0;}
        //if($v!='')$v.='<br>';
      }

      //echo "Vorbedingung erfüllt";
      //wenn alle voraussetzungen erfüllt sind, dann grün, ansonsten rot
      if ($z1==$z2){$bg='ccg';$data[$i][11]=1;$test=1;}else{$bg='ccr';$data[$i][11]=3;$test=3;}
      //bei gebäuden, forschungen,die "grün" sind, noch schauen, ob sie schon gebaut sind, falls nicht, gelb anzeigen
      if($bg=="ccg" AND $techs[$i]==0 AND $i<80){$bg='ccy';$data[$i][11]=2;$test=2;}
      
      //2. spalte
      //nochmal durchlaufen um die farbe festzustellen
      foreach($vorb as $einzelb) //jede einzelne bedingung checken
      {
        if($data[$einzelb][11]==1)$fc="ccg";elseif($data[$einzelb][11]==2)$fc="ccy";elseif($data[$einzelb][11]==3)$fc="ccr";
        $v.='<div title="'.$ttext[$data[$einzelb][12]].'" class="'.$fc.'">'.$data[$einzelb][0].'</div>';
      }
	  
      $tablestring.='
		<tr>
		  <td class="'.$bg.'" title="'.$ttext[$i].'" style="border: 1px solid #666666;">'.$data[$i][0].' <a href="help.php?t='.$i.'" class="link">['.$techtree_lang[beschreibung].']</a></td>
		  <td class="'.$bg.'" style="border: 1px solid #666666;">'.$v.'</td>
		</tr>';
    }
  }

  //rahmen oben
  echo '<br><table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro"><div class="cellu">'.$techtree_lang[seitentitel].'</div></td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table width="572">';
  //kopfzeile
  echo '<tr align="center"><td class="cell1" width="50%"><b>'.$techtree_lang[technologie].'</b></td><td class="cell1" width="50%"><b>'.$techtree_lang[voraussetzung].'</b></td></tr>';
  echo $tablestring;
  echo '</table>';


  //rahmen unten
  echo '</td><td width="13" class="rr">&nbsp;</td>
        </tr>
        <tr>
        <td width="13" class="rul">&nbsp;</td>
        <td class="ru">&nbsp;</td>
        <td width="13" class="rur">&nbsp;</td>
        </tr>
        </table><br><br><br><br><br>';
}
?>
<script>
$(document).ready(function () {
$("div, img, a, td").tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: "&",
    extraClass: "design1", 
    fixPNG: true,
    opacity: 0.15,
    left: 0
});
});
</script>
</div>
</body>
</html>
