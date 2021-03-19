<?php
$q_questname='Der Bergyaeti';

$q_info[0]='Der Hirte Zauselus bittet dich einen aggressiven Bergyaeti im Westen zu t&ouml;ten.';
$q_info[1]='Du hast den Bergyaeti besiegt und kannst dir deine Belohnung abholen.';
$q_info[2]='Der Hirte Zauselus wird dir ewig daf&uuml;r dankbar sein, dass du den Bergyaeti get&ouml;tet hast.';

if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: '.$q_info[0];

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
      $q_x=-20;
      $q_y=12;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(2,2);
      $enm["name"]='Bergyaeti';
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
    $giveexp=150;
    give_exp($giveexp);
    $exp=$exp+$giveexp;
    //item für den cyborg
    add_item( 80, 1);//brustpanzer
    $q_text=$q_questname.': Du &uuml;berbringst die Botschaft deines Erfolges und erh&auml;ltst '.$giveexp.' Erfahrungspunkte und eine gutes R&uuml;stungsteil.';
  break;
  }//switch flag1 ende
}

?>
