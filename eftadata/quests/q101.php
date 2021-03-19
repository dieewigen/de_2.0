<?php
$q_questname='Der Pfad der Pr&uuml;fung';
if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Eine verwitterte Steintafel wei&szlig;t auf eine alte Ruine im Nordosten hin, &uuml;ber die seit ewigen Zeiten ein W&auml;chter seine Hand h&auml;lt.';

$q_info[0]='Du hast Informationen &uuml;ber eine alte Ruine im Nordosten (X: 9 Y: 3).';
$q_info[1]='Du warst w&uuml;rdig und hast einen Jalenar-Diamanten bekommen.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
  case 0://abfragen der bedingungen
    $q_hasall=1;
	  
	/*
    //schauen wieviele kriegsartefakte er hat
    $db_daten=mysql_query("SELECT kartefakt FROM de_user_data WHERE user_id='$ums_user_id'",$db);
    $row = mysql_fetch_array($db_daten);
    $q_kartefakt=$row["kartefakt"];

    if($sv_efta_in_de==0)$q_kartefakt=9;
	*/
	  
    if ($exp<150)
    {
      $q_hasall=0;
      $q_text=$q_questname.': Du bist nicht erfahren genug, komme sp&auml;ter wieder, wenn du mindestens 150 Erfahrungspunkte hast.';
      mysql_query("UPDATE de_cyborg_data SET x=oldx, y=oldy WHERE user_id='$efta_user_id'",$eftadb);
    }
	/*
    elseif($q_kartefakt<2)
    {
      $q_hasall=0;
      $q_text=$q_questname.': Du ben&ouml;tigst 2 Kriegsartefakte, komme sp&auml;ter wieder.';
      mysql_query("UPDATE de_cyborg_data SET x=oldx, y=oldy WHERE user_id='$efta_user_id'",$eftadb);
    }
	*/
	
    if($q_hasall==1)//er hat alles, also belohnung geben
    {
      //quest abschließen
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      //exp für den cyborg
      mysql_query("UPDATE de_cyborg_data set exp=exp+200 WHERE user_id='$efta_user_id'",$eftadb);
      $exp=$exp+200;
      $q_text=$q_questname.': Du erweist dich als w&uuml;rdig und erh&auml;ltst einen Jalenar-Diamanten.<br>Zus&auml;tzlich bekommst du 200 Erfahrungspunkte.';

    }


  break;
  }//switch flag1 ende
}

?>
