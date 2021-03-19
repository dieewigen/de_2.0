<?php

############################################
####									####
####	Bert Pufahl 					####
####	Rosa-Luxemburg-Str 45			####
####	19205 Gadebusch 				####
####	kleines_etwas@die-ewigen.com	####
####									####
############################################

$q_questname='Die Schatzsuche';

$q_info[0]='Du findest eine Karte aus grauer Vorzeit und nimmst diese an dich. Es f&uuml;hr eine Linie zu einem geheimnissvollen Punkt an dem ein Schatz vergraben sein soll.';
$q_info[1]='Du bist an dem Punkt angekommen und beginnst mit deinen H&auml;nden zu graben. Nach kurzer Zeit findest du ein altes Leinentuch in das ein alter Schl&uuml;ssel und eine weitere Karte eingewickelt ist. Die Karte ist schon recht vermodert und schlecht lesbar. Zu erkennen ist dort nur einen weiteren Punkt und drei h&auml;&szlig;liche Kreaturen.';
$q_info[2]='Du hast den Schatz geborgen.';

if($q_questfeld==0){
	$q_zeit='-1';
	$q_text='Neue Quest: Du findest eine Karte aus grauer Vorzeit und nimmst diese an dich. Es f&uuml;hrt eine Linie zu einem geheimnissvollen Punkt an dem ein Schatz vergraben sein soll.';
	$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0:
      $q_map=0;
      $q_x=-21;
      $q_y=65;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
	  $q_text=$q_questname.': '.$q_info[1];
	  
	  break;
	  case 1://den gegner töten
	  if($flag10>2) {
		  //quest abschließen
		  mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
	      //Schatz bergen
	      add_item(178, 1);//Plattenbrustpanzer des Hirsches
	      add_item(162, 1);//Rapier des Leoparden
	      add_item(175, 1);//Langspitzschild des Gromen
	      //exp für den cyborg
          $giveexp=200;
          give_exp($giveexp);
          $exp=$exp+$giveexp;
          //geld für den cyborg
          modify_player_money($efta_user_id, 100);//1 silber
	      $q_text=$q_questname.': Du &ouml;ffnest die Schatztruhe und findest darin drei gute R&uuml;stungsteile und ein Silber. Zus&auml;tzlich erh&auml;ltst du 200 Erfahrungspunkte.';
	   
	      break;
	  }
	  
	  else {
		  //passende gegner laden
          $enm=enm_load(3,3);
          $enm["name"]='h&auml;&szlig;liche Kreatur';
          $enm["questid"]=$questid;
          $enm["flagid"]=10;
          enm_add2player($efta_user_id, $enm);
          echo '<script>lnk("");</script>';
          exit;
	  }
  }//switch flag1 ende
}

?>
