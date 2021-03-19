<?php
############################################
####									####
####	Bert Pufahl 					####
####	Dorfstr. 3 						####
####	19071 Groß Brütz 				####
####	kleines_etwas@die-ewigen.com	####
####									####
############################################
$q_questname='Die Hochzeit';
if($q_questfeld==0)
{
$q_zeit='-1';
$q_text='Neue Quest: Die Hochzeit.';

$q_info[0]='Seid gegr&uuml;&szlig;t, mein Name ist Emilia und ich hoffe Ihr k&ouml;nnt mir helfen. Morgen ist mein gro&szlig;er Tag. Ich werde heiraten. Allerdings hat sich der Termin verschoben und ich kann meinen Gatten nicht erreichen. Kannst du bitte helfen ihn zu suchen? Zuletzt sah ich ihn in der N&auml;he des Hafens von Waldmond.';
$q_info[1]='Frage Emilia nach dem Namen ihres Gatten.';
$q_info[2]='Suche in der N&auml;he des Hafens nach Tim Bugdu.';
$q_info[3]='Suche nach der Behausung des alten Mannes und fragte dort nach Tim Bugdu.';
$q_info[4]='Gehe nach S&uuml;den und suche dort den lahmen Holzf&auml;ller und fragte dort nach Tim Bugdu.';
$q_info[5]='Gehe s&uuml;dlich zum Ufer und suche dort nach Tim Bugdu.';
$q_info[6]='Gehe zu Emilia und berichte ihr was du in Erfahrung bringen konntest.';
$q_info[7]='Du hast Emilia &uuml;ber ihren Gatten aufgekl&auml;rt und f&uuml;r deine Bem&uuml;hungen eine Entsch&auml;digung erhalten.';


$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
    case 0://zurück zu Emilia um nach dem Namen des Gatten zu fragen
      $q_map=0;
      $q_x=-20;
      $q_y=86;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Jetzt stehst du am Hafen und wei&szlig;t nicht nach wem du suchen sollst. Frage Emilia nach dem Namen.';
    break;
    
	case 1://suche am Hafen nach Tim Bugdu
      $q_map=0;
      $q_x=1;
      $q_y=9;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Oh, Entschuldigung, mein Gatte hei&szlig;t Tim Bugdu.';
  	break;
    
	case 2://gehe zum alten Mann (der alte Mann und das Holz)
      $q_map=0;
      $q_x=2;
      $q_y=6;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Wieder am Hafen angelangt suchst du vergebens nach Tim Bugdu. Aber du erh&auml;lst Informationen &uuml;ber ihn. Er fragte nach einer M&ouml;glichkeit der &Uuml;bernachtung. Empfohlen wurde ihm die Behausung eines alten Mannes.';
    break;
    
	case 3://gehe zum lahmen Holzfäller
      $q_map=0;
      $q_x=-4;
      $q_y=-91;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Der alte Mann erz&auml;hlt dir, Tim Bugdu habe ihn gefragt wo man g&uuml;nstig Holz erwerben k&ouml;nne. Der alte Mann empfahl ihm zum lahmen Holzf&auml;ller im S&uuml;den zu gehen. Dann habe er eine Nacht hier geschlafen und mehrfach Alptr&auml;ume gehabt. W&auml;hrend dieser seuselte er was wie &quot;Ich will sie nicht heiraten!&quot; und &quot;Ich will nur ihr Geld!&quot;.';
    break;
    
	case 4://gehe zum Ufer
      $q_map=0;
      $q_x=-4;
      $q_y=-94;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Der lahme Holzf&auml;ller erz&auml;hlt dir es sei ein komischer Kauz hier gewesen, hektisch und unh&ouml;flich. Er fragte nach Holz um sich darau&szlig; ein Flo&szlig; bauen zu k&ouml;nnen um etwas s&uuml;dlicher damit in See zu stechen und zu verschwinden. Er hatte es sehr eilig. Wenn du dich beeilst, erreichst du ihn vielleicht noch!';
    break;
    
	case 5://berichte Emilia
      $q_map=0;
      $q_x=-20;
      $q_y=86;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Angekommen am Ufer siehst du in der Ferne einen Mann auf einem Flo&szlig; stehen. Warst du doch zu langsam. Gehe zu Emilia und berichte ihr was du in Erfahrung bringen konntest.';
    break;
    
	case 6://### ENDE ### 250 Erfahrungspunkte und 50 Kupfer gutschreiben
      $q_give_exp=250; // +250 Erfahrungspunkte
      give_exp($q_give_exp);
      $exp=$exp+$q_give_exp;
	  modify_player_money($efta_user_id, +50); // +50 Kupfer

      //quest abschließen
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);

      //questmessage
      $q_text=$q_questname.': Du erz&auml;hlst Emilia alles was du &uuml;ber Tim Bugdu in Erfahrung bringen konntest und sie wohl einem Heiratsschwindler aufgesessen sei. F&uuml;r deine Bem&uuml;hungen erh&auml;lst du 50 Kupfer und 250 Erfahrungspunkte.';
    break;
  }//switch flag1 ende
}

?>
