<?php
$q_questname='Eine Frage der Ehre';

$q_info[0]='Hepta Mera bittet dich einen Grabr&auml;uber zu verfolgen und den Helm Ihres verstorbenen Bruders zur&uuml;ckzuholen.';
$q_info[1]='Du hast den Grabr&auml;uber besiegt und den Helm gefunden. Kehre zu Hepta Mera zur&uuml;ck.';
$q_info[2]='Ehre macht einen Menschen aus.';

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
      $q_x=-54;
      $q_y=70;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
      //passenden gegner laden
      $enm=enm_load(3,3);
      $enm["name"]='Grabsch&auml;nder';
      $enm["questid"]=$questid;
      $enm["flagid"]=10;
      enm_add2player($efta_user_id, $enm);
      echo '<script>lnk("");</script>';
      exit;
    }
  break;
  case 1://belohnung abholen
  	//entscheidungsfrage bzgl. der ehrlichkeit
  	if(!isset($_REQUEST["qdc"]))
  	{
  	  rahmen0_oben();
  	  rahmen1_oben('<div align="center"><b>'.$q_questname.'</b></div>');
  	  echo '<div align="left"><br>';
	  echo 'Hepta Mera: Seid ihr erfolgreich gewesen?';
	  
	  //antwort 1
	  echo '<br><br><div> <a href="#" onClick="lnk(\'qdo='.$questid.'&qdc=1\')"><div class="b1">Antwort 1</div></a>&nbsp;
	  
	  Leider konnte ich den Grabsch&auml;nder nicht finden. (L&uuml;ge und behalte den Helm einfach f&uuml;r dich.)
	  
	  </div>';
	  //antwort 2 
	  echo '<br><div><a href="#" onClick="lnk(\'qdo='.$questid.'&qdc=2\')"><div class="b1">Antwort 2</div></a>&nbsp;
	  
	  Ja, ich konnte ihn stellen und habe hier den Helm Eures Bruders. (Sage die Wahrheit und &uuml;bergebe den Helm.)
	  
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
      if($_REQUEST["qdc"]==1)//lüge
      {
  	    //quest abschließen
        mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
        //exp für den cyborg
        $giveexp=100;
        give_exp($giveexp);
        $exp=$exp+$giveexp;
        //item/geld für den cyborg
        add_item( 93, 1);//helm
        $q_text=$q_questname.': Du l&uuml;gst Hepta Mera an und erh&auml;ltst '.$giveexp.' Erfahrungspunkte und beh&auml;ltst den Helm Ihres Bruders. Du f&uuml;hlst dich jedoch bei deiner L&uuml;ge ertappt.';
      }
      elseif($_REQUEST["qdc"]==2)//wahrheit
      {
  	    //quest abschließen
        mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
        //exp für den cyborg
        $giveexp=200;
        give_exp($giveexp);
        $exp=$exp+$giveexp;
        //item/geld für den cyborg
        add_item( 93, 1);//helm
        modify_player_money($efta_user_id, 100);//1 silber
        $q_text=$q_questname.': Du sagst Hepta Mera die Wahrheit und gibst ihr den Helm. Du und erh&auml;ltst '.$giveexp.' Erfahrungspunkte, 1 Silber und weil sie der Meinung ist, dass der Helm bei dir besser aufgehoben ist, auch den Helm Ihres Bruders.';     	
      }
  	}
  break;
  }//switch flag1 ende
}

?>
