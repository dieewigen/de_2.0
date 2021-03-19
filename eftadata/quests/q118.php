<?php
$q_questname='Die Drachen';

$q_info[0]='Ohht Mitas bittet dich drei Drachen in der n&auml;heren Umgebung zu t&ouml;ten.';
$q_info[1]='Ohht Mitas bittet dich drei Drachen in der n&auml;heren Umgebung zu t&ouml;ten. 1/3 besiegt';
$q_info[2]='Ohht Mitas bittet dich drei Drachen in der n&auml;heren Umgebung zu t&ouml;ten. 2/3 besiegt';
$q_info[3]='Du hast alle drei Drachen besiegt und kannst zu Ohht Mitas zur&uuml;ckkehren.';
$q_info[4]='Ohht Mitas wird dir ewig daf&uuml;r dankbar sein, dass du die drei Drachen get&ouml;tet hast.';

if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Ohht Mitas bittet dich drei Drachen in der n&auml;heren Umgebung zu t&ouml;ten.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0://den gegner töten
    if($flag10=='1')
    {
      $q_map=0;
      $q_x=-16;
      $q_y=51;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(2,2);
      $enm["name"]='Drache';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
  break;
  case 1:
      if($flag10=='2')
    {
      $q_map=0;
      $q_x=-24;
      $q_y=66;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[2];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(2,2);
      $enm["name"]='Drache';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
  break;
  case 2:
      if($flag10=='3')
    {
      $q_map=0;
      $q_x=-31;
      $q_y=56;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[3];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(3,3);
      $enm["name"]='Drache';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
	break;
  case 3://belohnung abholen
    //quest abschließen
    mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
    //exp für den cyborg
    give_exp(300);
    $exp=$exp+300;
    
    $q_text=$q_questname.': Du &uuml;berbringst Ohht Mitas die Botschaft deines Erfolges und erh&auml;ltst 300 Erfahrungspunkte.';
  break;
  }//switch flag1 ende
}

?>
