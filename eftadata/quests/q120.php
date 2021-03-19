<?php
$q_questname='Die Heuschrecken';

$q_info[0]='Bauer :&quot;Bitte hellft mir. Ich k&auml;mpfe seit Wochen gegen eine Heuschreckenplage. Vertreibt sie bevor sie meine ganze Ernte vernichten.&quot;';
$q_info[1]='Du hast die Heuschrecken vertrieben.';
$q_info[2]='Der Bauer wird dir ewig daf&uuml;r dankbar sein, dass du die Heuschreckenplage beseitigt und ihm somit seine Ernte gerettet hast.';

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
      $q_x=-57;
      $q_y=67;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': '.$q_info[1];
    }
    else //gegner hinzufügen
    {
		// gegner soll stärker vom user haben, aber mind stufe 3
		if ($level < 3) {
			$itemlvl = 3;
		}
		else {
			$itemlvl = $level;
		}
      //passenden gegner laden
      $enm=enm_load($itemlvl,$itemlvl);
      $enm["name"]='Heuschrecken';
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
    give_exp(300);
    $exp=$exp+300;
    
    $q_text=$q_questname.': Du &uuml;berbringst dem Bauern die Botschaft deines Erfolges und erh&auml;ltst 300 Erfahrungspunkte.';
  break;
  }//switch flag1 ende
}

?>
