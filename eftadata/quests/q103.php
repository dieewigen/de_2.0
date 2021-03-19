<?php
$q_questname='Lieferung f&uuml;r Nesa Calas';
if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Nesa Calas ben&ouml;tigt einige Waren.';

$q_info[0]='Besorge 10 Lindenholzst&auml;mme und kehre zu Nesa Calas (X: 6 Y: 3) zur&uuml;ck.';
$q_info[1]='Du hast Nesa Calas das Holz gebracht.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0://holz abgeben
    //schauen ob er genug getreide hat
    if(get_item_anz(4845)>=10)
    {
      //exp für den cyborg
      $q_give_exp=100;
      give_exp($q_give_exp);
      $exp=$exp+$q_give_exp;

      //benötigte items entfernen
      remove_item(4845, 10);

      //geld geben
      modify_player_money($efta_user_id, 50);

      //quest abschließen
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);

      //questmessage
      $q_text=$q_questname.': Du bringst Nesa Calas die Lindenholzst&auml;mme und erh&auml;ltst 100 Erfahrungspunkte und 50 Kupferm&uuml;nzen.';
    }
    else $q_text=$q_questname.': Du ben&ouml;tigst 10 Lindenholzst&auml;mme, komme sp&auml;ter wieder.';
  break;
  }//switch flag1 ende
}

?>
