<?php
$q_questname='Der alte Mann und das Holz';
if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Ein alter Mann bittet dich ihm etwas Brennholz zu besorgen.';

$q_info[0]='Der alte Mann benötigt etwas Brennholz für seine Hütte, im Osten (X: 17 Y: 6) sollte etwas zu finden sein.';
$q_info[1]='Da du das Brennholz gefunden hast, kannst du zu dem alten Mann (X: 2 Y: 6) zurückkehren.';
$q_info[2]='Du hast dem alten Mann sein Brennholz gebracht und er ist sehr zufrieden.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0://holz einpacken
    $q_map=0;
    $q_x=2;
    $q_y=6;
    mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
    $q_text=$q_questname.': Du findest etwas Brennholz und kannst zu dem alten Mann zur&uuml;ckkehren.';
  break;
  case 1://holz abliefern
    //quest abschließen
    mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
    //exp für den cyborg
    mysql_query("UPDATE de_cyborg_data set exp=exp+150 WHERE user_id='$efta_user_id'",$eftadb);
    $exp=$exp+150;
    $q_text=$q_questname.': Du bringst dem alten Mann das Brennholz und erh&auml;ltst 150 Erfahrungspunkte.';
  break;
  }//switch flag1 ende
}

?>
