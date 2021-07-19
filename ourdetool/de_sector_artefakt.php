<?php
include "../inccon.php";
//sv.inc.php includen
include "../inc/artefakt.inc.php";
?>
<html>
<head>
<?php include "cssinclude.php";?>
</head>
<body>
<form action="de_sector_artefakt.php" method="post">
<div align="center">
<?php

include "det_userdata.inc.php";

if ($savedata)
{
  $filename="../inc/artefakt.inc.php";
  $cachefile = fopen ($filename, 'w');

  $str="<?php\n\n";

  if(!validDigit($a1))$a1=0;
  if(!validDigit($a2))$a2=0;
  if(!validDigit($a3))$a3=0;
  if(!validDigit($a4))$a4=0;
  if(!validDigit($a5))$a5=0;
  if(!validDigit($a6))$a6=0;
  if(!validDigit($a7))$a7=0;
  if(!validDigit($a8))$a8=0;
  if(!validDigit($a9))$a9=0;
  if(!validDigit($a10))$a10=0;
  
  $str.='$sv_artefakt[0] = array ('.$a1.',     0,     0,     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[1] = array (0,     0,     0,     0,     0,     '.$a2.');'."\n\n";
  $str.='$sv_artefakt[2] = array (0,     0,     0,     0,     0,     '.$a3.');'."\n\n";
  $str.='$sv_artefakt[3] = array ('.$a4.',     0,     0,     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[4] = array ('.$a5.',     0,     0,     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[5] = array ('.$a6.',     0,     0,     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[6] = array (0,     '.$a7.',     0,     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[7] = array (0,     0,     '.$a8.',     0,     0,     0);'."\n\n";
  $str.='$sv_artefakt[8] = array (0,     0,     0,     '.$a9.',     0,     0);'."\n\n";
  $str.='$sv_artefakt[9] = array (0,     0,     0,     0,     '.$a10.',     0);'."\n\n";

  $str.="\n\n?>";

  if ($cachefile) fwrite ($cachefile, $str);
  fclose($cachefile);
  echo '<br><a href="de_sector_artefakt.php">Die Daten wurden gespeichert.<br>Zur Aktualisierung der Anzeige hier klicken.</a>';
  die('</body></html>');
}
?>
<?php
echo '<br><b>Sektorartefakteinstellungen<b><br><br>';
echo '<table cellpadding="3" cellspacing="4">';
echo '<tr><td><b>Art</b></td> <td><b>Wert</b></td><td><b>Info</b></td></tr>';
echo '<tr><td>Die Schale von Sabrulia</td> <td><input type="text" name="a1" value="'.$sv_artefakt[0][0].'"></td><td>Gibt an, um wieviel Prozent der Rohstoffoutput erhöht wird.<br>Standard: 2</td></tr>';
echo '<tr><td>Der Spiegel von Calderan</td> <td><input type="text" name="a2" value="'.$sv_artefakt[1][5].'"></td><td>Gibt an, wie hoch die Wahrscheinlichkeit ist einen Bonuskollektor zu bekommen.<br>Standard: 1</td></tr>';
echo '<tr><td>Der Spiegel von Coltassa</td> <td><input type="text" name="a3" value="'.$sv_artefakt[2][5].'"></td><td>Gibt an, wie hoch die Wahrscheinlichkeit ist einen Bonuskollektor zu bekommen.<br>Standard: 2</td></tr>';
echo '<tr><td>Die Schale von Kesh-Ha</td> <td><input type="text" name="a4" value="'.$sv_artefakt[3][0].'"></td><td>Gibt an, um wieviel Prozent der Rohstoffoutput erhöht wird.<br>Standard: 1</td></tr>';
echo '<tr><td>Die Schale von Kesh-Na</td> <td><input type="text" name="a5" value="'.$sv_artefakt[4][0].'"></td><td>Gibt an, um wieviel Prozent der Rohstoffoutput erhöht wird.<br>Standard: 1</td></tr>';
echo '<tr><td>Die Schale von Kesh-Za</td> <td><input type="text" name="a6" value="'.$sv_artefakt[5][0].'"></td><td>Gibt an, um wieviel Prozent der Rohstoffoutput erhöht wird.<br>Standard: 1</td></tr>';
echo '<tr><td>Der Strom von Kiz-Murat</td> <td><input type="text" name="a7" value="'.$sv_artefakt[6][1].'"></td><td>Gibt an, wieviel M man pro Tick bekommt.<br>Standard: 1000</td></tr>';
echo '<tr><td>Der Strom von Kiz-Joar</td> <td><input type="text" name="a8" value="'.$sv_artefakt[7][2].'"></td><td>Gibt an, wieviel D man pro Tick bekommt.<br>Standard: 500</td></tr>';
echo '<tr><td>Der Strom von Kiz-Benir</td> <td><input type="text" name="a9" value="'.$sv_artefakt[8][3].'"></td><td>Gibt an, wieviel I man pro Tick bekommt.<br>Standard: 400</td></tr>';
echo '<tr><td>Der Strom von Kiz-Vokl</td> <td><input type="text" name="a10" value="'.$sv_artefakt[9][4].'"></td><td>Gibt an, wieviel E man pro Tick bekommt.<br>Standard: 200</td></tr>';

echo '</table>';
echo '<br><br><input type="Submit" name="savedata" value="Einstellungen speichern"><br><br><br>';

function validDigit($digit) {
    $isavalid = 1;
    for ($i=0; $i<strlen($digit); $i++)
    {
      if (!ereg("[0-9]",$digit[$i]))
      {
        $isavalid = 0;
        break;
      }
    }
    if($digit=='')$isavalid=0;
    //echo $isavalid;
    return($isavalid);
}
?>
</form>
</body>
</html>
