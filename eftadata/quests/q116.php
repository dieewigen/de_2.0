<?php
$q_questname='Der Bergl&ouml;we';

$q_info[0]='Hir Sevar bittet dich einen Bergl&ouml;wen bei einem westlichen Berg zu t&ouml;ten.';
$q_info[1]='Du hast den Bergl&ouml;wen besiegt und kannst zu Hir Sevar zur&uuml;ckkehren.';
$q_info[2]='Hir Sevar wird dir ewig daf&uuml;r dankbar sein, dass du den Bergl&ouml;wen am Berg get&ouml;tet hast.';

if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Hir Sevar bittet dich einen Bergl&ouml;wen bei einem westlichen Berg zu t&ouml;ten.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0://den gegner töten
    if($flag10>0)
    {
      $q_map=0;
      $q_x=-19;
      $q_y=48;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(2,2);
      $enm["name"]='Bergl&ouml;we';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
  break;
  case 1://belohnung abholen
    //quest abschließen
    mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
    //exp für den cyborg
    give_exp(250);
    $exp=$exp+250;
    
    $q_text=$q_questname.': Du &uuml;berbringst Hir Sevar die Botschaft deines Erfolges und erh&auml;ltst 250 Erfahrungspunkte.';
  break;
  }//switch flag1 ende
}

?>
