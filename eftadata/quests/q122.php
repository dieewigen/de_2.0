<?php

//Bruder bittet den 2ten bruder auszuspionieren.
//beim 2ten bruder angekommen dialog >> hilfe ja|nein >>
//ja >> holz sammeln 15 stk
//nein >> ???
//holz abgeben >> steine sammeln 8 stk
//

$q_questname='Familienstreit';

$q_info[0]='Tito Hudson und sein Bruder Wid hegen seit Jahren einen Streit. Um endlich Ruhe zu haben will Tito`s Bruder Wid die Insel verlassen. Dieses kommt Tito ganz recht. Deshalb bittet dich Tito seinem Bruder Wid zu helfen.';
$q_info[1]='Wid Hudson muß jetzt alleine zusehen wie er von der Insel kommt.';
$q_info[2]='Besorge 15 Lindenholzst&auml;mme und bringe diese zu Wid Hudson.';
$q_info[3]='Besorge 8 Sandsteine und bringe diese zu Wid Hudson.';
$q_info[4]='Wid Hudson konnte durch deine Hilfe die Insel verlassen. Gehe zu Tito Hudson und berichte.';
$q_info[5]='Du hast Tito Hudson die Nachricht &uuml;berbracht.';

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
    if(!isset($_REQUEST["qdc"]))
  	{
  	  rahmen0_oben();
  	  rahmen1_oben('<div align="center"><b>'.$q_questname.'</b></div>');
  	  echo '<div align="left"><br>';
	  echo 'Wid Hudson: Seit Jahren habe ich Streit mit meinem Bruder. Mir reichts. Ich will hier ganz schnell weg. Kannst du mir dabei behilflich sein?';
	  //antwort 1
	  echo '<br><br><div> <a a href="#" onClick="lnk(\'qdo='.$questid.'&qdc=1\')"><div class="b1">Antwort 1</div></a>&nbsp;
	  
	  Nein, f&uuml;r Familienstreitigkeiten habe ich keine Zeit.
	  
	  </div>';
	  //antwort 2 
	  echo '<br>    <div> <a a href="#" onClick="lnk(\'qdo='.$questid.'&qdc=2\')"><div class="b1">Antwort 2</div></a>&nbsp;
	  
	  Ja, sag mir wie ich dir helfen kann!
	  
	  </div>';
	  
	  echo '<br><br></div>';
  	  rahmen1_unten();
  	  rahmen0_unten();
      echo '<br><br>';
      //questseite ausblenden
      $q_dontshowquestpage=1;
  	}
  	else
  	{
      if($_REQUEST["qdc"]==1)//nein >> quest wird beendet und user geht leer aus
      {
  	  $q_map=0;
      $q_x=-61;
      $q_y=52;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      	$q_text=$q_questname.': Dann vergeude bitte nicht meine Zeit';
      }
      elseif($_REQUEST["qdc"]==2)//ja
      {
  	  mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+2 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': Wid Hudson ben&ouml;tigt 15 Lindenholzst&auml;mme. Besorge sie und bringe sie bitte zu ihm.';
      }
  	}
  break;
  
  case 1:
  //bleibt leer da quest schon beendet
  break;

case 2:
  	  if(get_item_anz(4845)>=15)
    {
      //benötigte items entfernen
      remove_item(4845, 15);

      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);

      //questmessage
      $q_text=$q_questname.': Danke f&uuml;r die Lindenholzst&auml;mme. Allerdings habe ich vergessen dir zu sagen das ich noch 8 Sandsteine ben&ouml;tige.';
    }
    else $q_text=$q_questname.': Du ben&ouml;tigst 15 Lindenholzst&auml;mme, komme sp&auml;ter wieder.';
      
  break;
  
case 3:
  	  if(get_item_anz(4850)>=8)
    {
      //benötigte items entfernen
      remove_item(4850, 8);

      $q_map=0;
      $q_x=-61;
      $q_y=52;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);      

      //questmessage
      $q_text=$q_questname.': &quot;Danke f&uuml;r die Sandsteine. Du sollst mir auch nicht umsonst geholfen haben. Leider kann ich dir nicht sehr viel bieten. Vielleicht kannst du damit etwas anfangen.&quot<br />Du erh&auml;lst einen schweren Sack mit irgendwelchem Plunder. Bei n&auml;herem Untersuchen findest du darin einen Kupferbarren.';
	  
	  // kupferbarren gutschreiben
	  add_item( 4855, 1);//kupferbarren
	}
    else $q_text=$q_questname.': Du ben&ouml;tigst 8 Sandsteine, komme sp&auml;ter wieder.';
      
  break;
  
case 4:
  	 //exp für den cyborg
      $q_give_exp=250;
      give_exp($q_give_exp);
      $exp=$exp+$q_give_exp;

      //geld geben
      modify_player_money($efta_user_id, 100);

      //quest abschließen
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);

      //questmessage
      $q_text=$q_questname.': Du &uuml;berbringst Tito Hudson die Nachricht &uuml;ber seinen Bruder und erh&auml;ltst 250 Erfahrungspunkte und eine Silberm&uuml;nze.';
      
  break;
  }//switch flag1 ende
}

?>
