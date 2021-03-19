<?php
echo '<br>';
rahmen1_oben('<div align="center"><b>Admintool</b></div>');
//gebirge setzen
echo '<a href="eftamain.php?admin=1&setmountain=1"><div class="b1">Gebirge erzeugen</div></a><br>';
echo '<a href="eftamain.php?admin=1&deletefield=1"><div class="b1">Feld löschen</div></a><br>';
echo '<a href="eftamain.php?admin=1&go1=1"><div class="b1">Norden</div></a><br>';
echo '<a href="eftamain.php?admin=1&go2=1"><div class="b1">Osten</div></a><br>';
echo '<a href="eftamain.php?admin=1&go3=1"><div class="b1">Süden</div></a><br>';
echo '<a href="eftamain.php?admin=1&go4=1"><div class="b1">Westen</div></a><br>';

rahmen1_unten();

if($admin==1)
{
  //gebirge auf dem feld setzen
  if($setmountain==1)
  {
    $grafik=rand(16,25);
  	$sql="UPDATE de_cyborg_map SET groundpicext='$grafik', groundtyp=14, bldg=0, bldgpic=0, fieldlevel=0, fieldamount=0 WHERE x='$x' AND y='$y' AND z='$map';";
  	mysql_query($sql, $efadb);
    $datenstring="$sql\n";
    $fp234=fopen("cache/eftaedit.sql", "a");
    fputs($fp234, $datenstring);
    fclose($fp234);
  }
  //feld löschen
  if($deletefield==1)
  {
    $grafik=rand(16,25);
  	$sql="DELETE FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map';";
  	mysql_query($sql, $eftadb);
    $datenstring="$sql\n";
    $fp234=fopen("cache/eftaedit.sql", "a");
    fputs($fp234, $datenstring);
    fclose($fp234);
  }
  //sich direkt bewegen
  if($go1==1)
  {
  	$sql="UPDATE de_cyborg_data SET y=y+1 WHERE user_id='$efta_user_id';";
  	mysql_query($sql, $eftadb);
  }
  if($go3==1)
  {
  	$sql="UPDATE de_cyborg_data SET y=y-1 WHERE user_id='$efta_user_id';";
  	mysql_query($sql, $eftadb);
  }
  if($go2==1)
  {
  	$sql="UPDATE de_cyborg_data SET x=x+1 WHERE user_id='$efta_user_id';";
  	mysql_query($sql, $eftadb);
  }
  if($go4==1)
  {
  	$sql="UPDATE de_cyborg_data SET x=x-1 WHERE user_id='$efta_user_id';";
  	mysql_query($sql, $eftadb);
  }

}

?>