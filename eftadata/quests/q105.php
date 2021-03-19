<?php
$q_questname='Der Pfad des Ruhmes';
if($q_questfeld==0)
{
  $q_zeit='-1';
  $q_text='Neue Quest: Seldon empfiehlt dir dem Pfad des Ruhmes zu folgen.';

  $q_info[0]='Folge den dir vorgegebenen Koordinaten.';
  $q_info[1]='Du hast den Pfad des Ruhmes bestanden.';
  
  if($flag1<70)$q_questinfo=$q_info[0];
  else $q_questinfo=$q_info[1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //koordinaten für die neuen ziele festlegen
  //map, x, y
  $q_koordinaten[]=array(  0,  -23,  19);//dieses ist der 1. punkt
  $q_koordinaten[]=array(  0,  -40, -83);
  $q_koordinaten[]=array(  0,  -15, -74);
  $q_koordinaten[]=array(  0,   31, -61);
  $q_koordinaten[]=array(  0,   49,  58);
  $q_koordinaten[]=array(  0,   76, -64);
  $q_koordinaten[]=array(  0,   57, -22);
  $q_koordinaten[]=array(  0,   -3,   3);
  $q_koordinaten[]=array(  0,   99,  44);
  $q_koordinaten[]=array(  0,    7, -89);
  // 2
  $q_koordinaten[]=array(  0,  270, 506);
  $q_koordinaten[]=array(  0,  255, 610);
  $q_koordinaten[]=array(  0,  298, 689);
  $q_koordinaten[]=array(  0,  432, 686);
  $q_koordinaten[]=array(  0,  491, 612);
  $q_koordinaten[]=array(  0,  461, 498);
  $q_koordinaten[]=array(  0,  361, 463);
  $q_koordinaten[]=array(  0,  365, 525);
  $q_koordinaten[]=array(  0,  386, 627);
  $q_koordinaten[]=array(  0,  482, 674);
  // 3
  $q_koordinaten[]=array(  0,  589,  46);
  $q_koordinaten[]=array(  0,  730,  25);
  $q_koordinaten[]=array(  0,  633, -84);
  $q_koordinaten[]=array(  0,  791,-129);
  $q_koordinaten[]=array(  0,  665,  82);
  $q_koordinaten[]=array(  0,  788, -33);
  $q_koordinaten[]=array(  0,  574, -90);
  $q_koordinaten[]=array(  0,  710, -25);
  $q_koordinaten[]=array(  0,  640,  89);
  $q_koordinaten[]=array(  0,  641,-147);
  // 4
  $q_koordinaten[]=array(  0,  251,-600);
  $q_koordinaten[]=array(  0,  360,-805);
  $q_koordinaten[]=array(  0,  254,-798);
  $q_koordinaten[]=array(  0,  423,-612);
  $q_koordinaten[]=array(  0,  242,-655);
  $q_koordinaten[]=array(  0,  454,-755);
  $q_koordinaten[]=array(  0,  347,-675);
  $q_koordinaten[]=array(  0,  256,-825);
  $q_koordinaten[]=array(  0,  273,-589);
  $q_koordinaten[]=array(  0,  344,-673);
  // 5
  $q_koordinaten[]=array(  0, -520,-433);
  $q_koordinaten[]=array(  0, -386,-454);
  $q_koordinaten[]=array(  0, -449,-603);
  $q_koordinaten[]=array(  0, -582,-602);
  $q_koordinaten[]=array(  0, -333,-613);
  $q_koordinaten[]=array(  0, -463,-661);
  $q_koordinaten[]=array(  0, -444,-774);
  $q_koordinaten[]=array(  0, -491,-855);
  $q_koordinaten[]=array(  0, -386,-406);
  $q_koordinaten[]=array(  0, -464,-664);
  // 6
  $q_koordinaten[]=array(  0, -696, 152);
  $q_koordinaten[]=array(  0, -703, -14);
  $q_koordinaten[]=array(  0, -815,  -5);
  $q_koordinaten[]=array(  0, -841,  93);
  $q_koordinaten[]=array(  0, -781, 169);
  $q_koordinaten[]=array(  0, -864,  24);
  $q_koordinaten[]=array(  0, -678,  12);
  $q_koordinaten[]=array(  0, -839, 163);
  $q_koordinaten[]=array(  0, -690, 144);
  $q_koordinaten[]=array(  0, -840,   8);
  // 7
  $q_koordinaten[]=array(  0, -410, 512);
  $q_koordinaten[]=array(  0, -264, 518);
  $q_koordinaten[]=array(  0, -276, 574);
  $q_koordinaten[]=array(  0, -466, 598);
  $q_koordinaten[]=array(  0, -379, 649);
  $q_koordinaten[]=array(  0, -234, 667);
  $q_koordinaten[]=array(  0, -312, 707);
  $q_koordinaten[]=array(  0, -434, 720);
  $q_koordinaten[]=array(  0, -365, 779);
  $q_koordinaten[]=array(  0, -225, 767);
  
  //koordinaten auf brauchbarkeit testen
  /*
  for($i=0;$i<count($q_koordinaten);$i++)
  {
    $q_map=$q_koordinaten[$i][0];
    $q_x=$q_koordinaten[$i][1];
    $q_y=$q_koordinaten[$i][2];
  
  	$db_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$q_x' AND y='$q_y' AND z='$q_map'",$db);
    $num = mysql_num_rows($db_daten);
  
    if($num==1)
    {
  	  $row = mysql_fetch_array($db_daten);
  	  echo '<br>'.$i.': '.$row["groundtyp"].' X: '.$q_x.' Y: '.$q_y;
    }
    else echo '<br>'.$i.' : fehler';
  	
  }*/
  
  //schauen ob man genug ruhmespunkte hat
  if($player_fame >= 5*($flag1+1))
  {
  
    //ertrag gutschreiben
    $exp=70 + $flag1;
    give_exp($exp);
    
    $q_text=$q_questname.': Du hast einen weiteren Abschnitt des Weges beschritten und bekommst '.$exp.' Erfahrungspunkte.';
    
    //schauen ob es noch weitere questpunkte gibt
    if(count($q_koordinaten)>$flag1+1)
    {
      //neue koordinaten vergeben
      $q_map=$q_koordinaten[$flag1+1][0];
      $q_x=$q_koordinaten[$flag1+1][1];
      $q_y=$q_koordinaten[$flag1+1][2];
      
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text.=' Folge weiter dem Weg.';
    }
    else //quest abschließen
    {
      mysql_query("UPDATE de_cyborg_quest SET erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text.=' Du hast den Pfad des Ruhmes bewältigt.';
    }
    
  }
  else $q_text=$q_questname.': Du benötigst '.(5*($flag1+1)).' Ruhmespunkte.';
}

?>
