<?php
include "../inccon.php";
include "../functions.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Info</title>
<?php include "cssinclude.php";?>
</head>
<body>
<br><center>
<?php
include "det_userdata.inc.php";
if ($uid>0){
  /*
  $query="SELECT techs FROM de_user_data WHERE user_id='$uid'";
  $db_daten=mysql_query($query,$db);
  $row = mysql_fetch_array($db_daten);
  $ztechs=$row["techs"];
  */

  echo '<h4>Schiffs&uuml;bersicht</h4>';
  //zaehle alle schiffe, die schon vorhanden sind - anfang
  $fid0=$uid.'-0';$fid1=$uid.'-1';$fid2=$uid.'-2';$fid3=$uid.'-3';
  $db_daten=mysql_query("SELECT e81, e82, e83, e84, e85, e86, e87, e88, e89, e90 FROM de_user_fleet WHERE user_id='$fid0' OR user_id='$fid1' OR user_id='$fid2' OR user_id='$fid3'ORDER BY user_id ASC",$db);
  while($row = mysql_fetch_array($db_daten))
          {
            $str='';
            for ($i=81;$i<=99;$i++) $str = $str."\$ec$i=\$ec$i+\$row[\"e$i\"];";
            eval ($str); //variablen -> ec81, ec82...
          }
          //zaehle alle schiffe, die schon vorhanden sind - ende

          //ueberschrift ausgeben
          echo '</table>';
          //lade einheitentypen
          $db_daten=mysql_query("SELECT  tech_id, tech_name, tech_vor FROM de_tech_data1 WHERE tech_id>80 AND tech_id<100",$db);
          while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
          {
            if ($row["tech_id"]<>86) //echo "Vorbedingung erf�llt";
            {

              $str='$ec=$ec'.$row["tech_id"].';';
              eval ($str);
              if ($ec=='')$ec=0;
              //showeinheit($row["tech_name"], $row["tech_id"], $row["restyp01"], $row["restyp02"], $row["restyp03"], $row["restyp04"], $row["tech_ticks"], $ec);
              echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
              echo '<tr>';
              echo '<td class="c" width="70%" align="left">'.$row["tech_name"]."</td>";
              echo '<td class="c" width="30%" align="right">'.$ec."</td>";
              echo "</tr>";
              echo "</table>";
            }
          }
  echo '<h4>Verteidigungsanlagen</h4>';
          //zaehle alle verteidigungsanlagen, die schon vorhanden sind - anfang
          $str='';
          for ($i=100;$i<=109;$i++) $str = $str."\$ec$i=0;";
          eval($str); //variablen -> ec100, ec101,...
          $db_daten=mysql_query("SELECT e100, e101, e102, e103, e104 FROM de_user_data WHERE user_id=$uid",$db);
          while($row = mysql_fetch_array($db_daten))
          {
            $str='';
            for ($i=100;$i<=109;$i++) $str = $str."\$ec$i=\$ec$i+\$row[\"e$i\"];";
            eval ($str);
          }
          //zaehle alle verteidigungsanlagen, die schon vorhanden sind - ende

          //ueberschrift ausgeben
          //lade einheitentypen
          $db_daten=mysql_query("SELECT  tech_id, tech_name, tech_vor FROM de_tech_data1 WHERE tech_id>99 AND tech_id<110 ORDER BY tech_ticks",$db);
          while($row = mysql_fetch_array($db_daten)) //jeder gefundene datensatz wird geprueft
          {
            //zerlege vorbedinguns-string
            $z1=0;$z2=0;
            $vorb=explode(";",$row["tech_vor"]);
            foreach($vorb as $einzelb) //jede einzelne bedingung checken
            {
              $z1++;
              if ($ztechs[$einzelb]==1) $z2++;
              if ($einzelb==0) {$z1=0;$z2=0;}
            }
            if ($z1==$z2) //echo "Vorbedingung erf�llt";
            {
              $str='$ec=$ec'.$row["tech_id"].';';
              eval ($str);
              echo '<table border="0" cellpadding="0" cellspacing="1" width="310" bgcolor="#000000">';
              echo '<tr>';
              echo '<td class="c" width="70%" align="left">'.$row["tech_name"]."</td>";
              echo '<td class="c" width="30%" align="right">'.$ec."</td>";
              echo "</tr>";
              echo "</table>";
            }
          }
  echo '<h4>Entwicklungen</h4>';
  $ztechs=loadPlayerTechs($uid);
  echo '<table border="0" cellpadding="0" cellspacing="1" width="310px" bgcolor="#000000">';
  for($i=1; $i<500;$i++){
    if(hasTech($ztechs,$i)){
      //print_r($ztechs[$i]);
      $techcheck="SELECT tech_name FROM de_tech_data WHERE tech_id=$i";
      $db_tech=mysqli_query($GLOBALS['dbi'],$techcheck);
      $row_techcheck = mysqli_fetch_array($db_tech);
    
     echo '<tr>';
      echo '<td>ID '.$i.': '.str_replace(";","<br>",utf8_decode($row_techcheck['tech_name'])).'</td><td>'.date("H:i:s d.m.Y",$ztechs[$i]['time_finished']).'</td>';
      echo '</tr>';
    }
  }
  echo '</table>';
}
else echo 'Kein User ausgew&auml;hlt.';
?>
</body>
</html>
