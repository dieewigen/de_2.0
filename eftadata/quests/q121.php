<?php
$q_questname='Die R&auml;uber';

$q_info[0]='Nach einem langen Tag entschlie&szlig;t du dich hier Rast zu machen um Kraft f&uuml;r den n&auml;chsten Tag zu sammeln. Auf mal wirst du aber bei deinen Vorbereitungen f&uuml;r die Nacht durch Hilfeschreie in der N&auml;he gest&ouml;rt und gehst um nach dem Rechten zu schauen.';
$q_info[1]='Du suchst vergebens nach der Quelle der Schreie. Du bist m&uuml;de und kehrst zu deinem Lager zur&uuml;ck.';
$q_info[2]='Du hast die R&auml;ber vertrieben.';

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
  case 0:
    
      $q_map=0;
      $q_x=-65;
      $q_y=55;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
  break;

case 1:
  	if($flag10>2) //3 kämpfe
    {
	//belohnung abholen
    //quest abschließen
    mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
    //exp für den cyborg
    give_exp(300);
    $exp=$exp+300;
    
    $q_text=$q_questname.': Da wollten diese R&auml;uber dein Lager pl&uuml;ndern. Doch du hast sie erfolgreich in die Flucht geschlagen und erh&auml;ltst 300 Erfahrungspunkte.';
	}
    else //gegner hinzufügen
    {
		// gegner soll stärker vom user haben, aber mind stufe 4
		if ($level < 4) {
			$itemlvl = 4;
		}
		else {
			$itemlvl = $level;
		}
      //passenden gegner laden
      $enm=enm_load($itemlvl,$itemlvl);
      $enm["name"]='R&auml;uber';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
  break;
  }//switch flag1 ende
}

?>
