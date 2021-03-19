<?php
$q_questname='Die gestohlene Kette';

$q_info[0]='Jungfer Alonora bittet dich einen R&auml;uber zu verfolgen und ihre Kette wieder zur&uuml;ckzuholen.';
$q_info[1]='Du hast den R&auml;uber besiegt und die Kette gefunden. Kehre zu Jungfer Alonora zur&uuml;ck.';
$q_info[2]='Jungfer Alonora wird dir ewig daf&uuml;r dankbar sein, dass du ihre Kette zur&uuml;ckgeholt hast.';

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
      $q_x=-13;
      $q_y=77;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(3,3);
      $enm["name"]='Sch&auml;ndlicher R&auml;uber';
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
    //item/geld für den cyborg
    modify_player_money($efta_user_id, 100);
    $q_text=$q_questname.': Du &uuml;berbringst die Botschaft deines Erfolges und erh&auml;ltst '.$giveexp.' Erfahrungspunkte und 1 Silber.';
  break;
  }//switch flag1 ende
}

?>
