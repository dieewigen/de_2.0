<?php
include 'inc/sv.inc.php';
include 'inccon.php';
include "functions.php";
include 'inc/lang/'.$sv_server_lang.'_techtree.lang.php';

$sv_max_secmoves=99;

$ums_gpfad=$sv_image_server_list[0];

$ums_rasse=intval($_REQUEST['rasse']);
if($ums_rasse<1 OR $ums_rasse>4)$ums_rasse=1;

?>
<!DOCTYPE HTML>
<html>
<head>
<title><?=$techtree_lang[seitentitel]?></title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<div align="center">
<?php


echo '<a title="Die Ewigen" href="techtree_all.php?rasse=1"><img src="'.$ums_gpfad.'g/derassenlogo1.png" border="0"></a>';
echo '<a title="Ishtar" href="techtree_all.php?rasse=2"><img src="'.$ums_gpfad.'g/derassenlogo2.png" border="0"></a>';
echo '<a title="K´Tharr" href="techtree_all.php?rasse=3"><img src="'.$ums_gpfad.'g/derassenlogo3.png" border="0"></a>';
echo '<a title="Z´tah-ara" href="techtree_all.php?rasse=4"><img src="'.$ums_gpfad.'g/derassenlogo4.png" border="0"></a>';

  //alle techs die es von der rasse gibt auslesen und in nen array packen
  $maxid=0;$id=0;
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
    $data[$row["tech_id"]][9]=str_replace('"','', $row["des"]);
    $data[$row["tech_id"]][10]=$id;
    $data[$row["tech_id"]][11]=0;//baustatus
    //den größten tech_id wert speichern
    if($row["tech_id"]>$maxid)$maxid=$row["tech_id"];
    $id++;
  }

  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////
  //title-tooltip
  /////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////
  unset($tooltip);
  for($i=1;$i<=$maxid;$i++)
  {
      $ttext = $data[$i][0];
      $ttext.= '<br>'.$techtree_lang[typ].' ';
      if($i>=1 AND $i<40)$ttext.= $techtree_lang[typ1];
      if($i>=40 AND $i<80)$ttext.= $techtree_lang[typ2];
      if($i>=81 AND $i<100)$ttext.= $techtree_lang[typ3];
      if($i>=100 AND $i<110)$ttext.= $techtree_lang[typ4];
      if($i>=110 AND $i<120)$ttext.= $techtree_lang[typ5];
      
      $ttext.= '<br>'.$techtree_lang[rohstoffkosten];
      if($data[$i][1]>0) $ttext.= '<br>'.number_format( $data[$i][1], 0,"",".").' M';
      if($data[$i][2]>0) $ttext.= '<br>'.number_format( $data[$i][2], 0,"",".").' D';
      if($data[$i][3]>0) $ttext.= '<br>'.number_format( $data[$i][3], 0,"",".").' I';
      if($data[$i][4]>0) $ttext.= '<br>'.number_format( $data[$i][4], 0,"",".").' E';
      if($data[$i][5]>0) $ttext.= '<br>'.number_format( $data[$i][5], 0,"",".").' T';
      $ttext.= '<br>'.$techtree_lang[bauzeit].' '.$data[$i][6];
      $ttext.= '<br>'.$techtree_lang[punkte].' '.number_format( $data[$i][7], 0,"",".");
      $ttext.= '<br><br>Beschreibung:<br>'.$data[$i][9];
      $tooltip[$i]=$ttext;  
  }
  
  //daten auslesen
  $tablestring='';
  for($i=1;$i<=$maxid;$i++)
  {
    //schauen ob die id belegt ist
    if($data[$i][0]!='' AND $i!=80)
    {
      $z1=0;$z2=0;
      $vorb=explode(";",$data[$i][8]);
      $v='';$fc='';

      //2. spalte
      //nochmal durchlaufen um die farbe festzustellen
      foreach($vorb as $einzelb) //jede einzelne bedingung checken
      {
        $v.='<div style="color: #FFFFFF;" title="'.$tooltip[$einzelb].'">'.$data[$einzelb][0].'</div>';
      }
  	
      if ($c1==0){$c1=1;$bg='cell';}else{$c1=0;$bg='cell1';}
      
      //linke spalte mit bild und name
      $tablestring.='
      <tr>
        <td title="'.$tooltip[$i].'" class="'.$bg.'">
        

  <table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="110"><img src="'.$ums_gpfad.'g/t/'.$ums_rasse.'_'.$i.'.jpg" border="0"></td>
    <td align="center"><span style="color: #FFFFFF;">'.$data[$i][0].'</span></td>
  </tr>
  </table>        
     	  
      	</td>
      	
      	<td class="'.$bg.'">'.$v.'</td>
     </tr>';
      $id++;
    }
  }

  //rassenname
  if($ums_rasse==1)$rassenname='Die Ewigen';
  if($ums_rasse==2)$rassenname='Ishtar';
  if($ums_rasse==3)$rassenname='K´Tharr';
  if($ums_rasse==4)$rassenname='Z´tah-ara';
  

  //rahmen oben
  echo '<br><table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td width="13" height="37" class="rol">&nbsp;</td>
        <td align="center" class="ro"><div class="cellu">'.$techtree_lang[seitentitel].' - '.$rassenname.'</div></td>
        <td width="13" class="ror">&nbsp;</td>
        </tr>
        <tr>
        <td class="rl">&nbsp;</td><td>';

  echo '<table width="780">';
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


?>

</div>

<script type="text/javascript">

	$('div, img, a, td, span').tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: "&",
    extraClass: "design1", 
    fixPNG: true,
    opacity: 0.15,
    left: 0
});
	</script> 

</body>
</html>