<?php
$q_questname='Das Monster am Ufer';

$q_info[0]='Der alte Molta bittet dich ein Monster am &ouml;stlichen Ufer zu t&ouml;ten.';
$q_info[1]='Du hast das Monster besiegt und kannst zum alten Molta zur&uuml;ckkehren.';
$q_info[2]='Der alte Molta wird dir ewig daf&uuml;r dankbar sein, dass du das Monster am Ufer get&ouml;tet hast.';

if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Der alte Molta bittet dich ein Monster am &ouml;stlichen Ufer zu t&ouml;ten.';

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
      $q_x=17;
      $q_y=0;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(1,1);
      $enm["name"]='Ufermonster';
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
    give_exp(100);
    $exp=$exp+100;
    //item für den cyborg
    add_item( 17, 1);//brustpanzer
    $q_text=$q_questname.': Du &uuml;berbringst dem alten Molta die Botschaft deines Erfolges und erh&auml;ltst 100 Erfahrungspunkte und eine gutes R&uuml;stungsteil.';
  break;
  }//switch flag1 ende
}

?>
