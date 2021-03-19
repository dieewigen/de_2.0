<?php
$q_questname='Die Familienbrosche';
if($q_questfeld==0)
{
  $q_zeit='-1';
  $q_text='Neue Quest: Die Familienbrosche';

$q_info[0]='Seid gegr&uuml;ßt, mein Name ist Alzusar und ich hoffe Ihr k&ouml;nnt mir helfen. Ich bin auf dem Weg zu einer Familienfeier und habe die Familienbrosche, die seit vielen Generationen in unserem Besitz ist, verloren. K&ouml;nnt Ihr mir bitte helfen sie wiederzufinden? Ich vermute, dass ich sie bei meiner letzten Rast an der K&uuml;ste im S&uuml;dosten verloren habe.';
$q_info[1]='Begebe dich nach Waldmond, dort solltest du weitere Informationen zum Verbleib der Brosche finden.';
$q_info[2]='Suche den Eremiten in der Wildnis.';
$q_info[3]='Besorge ein Brot f&uuml;r den Eremiten.';
$q_info[4]='Besorge eine Flasche Oller Fusel f&uuml;r den Eremiten.';
$q_info[5]='Kaufe dem Eremiten die Brosche f&uuml;r 50 Silberlinge ab.';
$q_info[6]='Bringe die Brosche ihrem Besitzer zur&uuml;ck.';
$q_info[7]='Du hast Alzusar geholfen und die Familienaura erhalten.';

$q_questinfo=$q_info[$flag1];
}
//wenn der spieler auf den richtigen koordinaten steht, kann er was unternehmen
else
{
  //je nach flag können dann sachen passieren
  switch($flag1){
    case 0://typen finden, der sie vertickt hat
      $q_map=0;
      $q_x=0;
      $q_y=0;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Am Strand triffst du einen kleinen Jungen, der gesehen hat, wie ein Wanderer die vermißte Brosche gefunden und eingesteckt hat. Der Wanderer trug ein Umhang mit dem Wappen von Waldmond.';
      
    break;
    case 1://die info erhalten, dass die brosche ein einsiedler hat
      $q_map=0;
      $q_x=110;
      $q_y=46;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      
      $q_text=$q_questname.': Endlich in Waldmond angekommen befragst du die Stadtwache nach dem Wanderer. Der Wanderer ist bekannt und die Stadtwache weist dir den Weg zu dessen Haus. Dort angekommen klopfst du und wartest einen Moment. Die T&uuml;r &ouml;ffnet sich und du stellst dich vor und schilderst dein Begehr. Brandon, der Wanderer, erz&auml;hlt dir, dass er die Brosche gefunden hat, sie aber einem Eremiten verkauft hat, der weit im Nordosten wohnt. Er beschreibt dir seinen ungef&auml;hren Wohnort und w&uuml;nscht dir viel Gl&uuml;ck bei deiner Suche.';
  	break;
    case 2://eremit gefunden und info über brot
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': Nach langer Suche findest du endlich die sch&auml;bige H&uuml;tte des Eremiten und fragst ihn nach der gesuchten Brosche. Er m&ouml;chte jedoch nichts mit dir zu tun haben und brummt nur er h&auml;tte hunger und schl&auml;gt dir die T&uuml;r vor der Nase zu. Vielleicht kann man ihn ja mit einem frischen Brot freundlicher stimmen?';
    break;
    case 3://brot abliefern und info über fusel
    if(get_item_anz(4861)>=1)
    {
      //benötigte items entfernen
      remove_item(4861, 1);
      
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': Du kommst mit dem Brot zur&uuml;ck zum Eremiten und &uuml;berreichst es ihm mit einem freundlichen L&auml;cheln. Der Eremit greift sich das Brot und knallt die T&uuml;r wieder zu und du h&ouml;rst sein Gemecker wie man Jemandem ein Brot ohne etwas zu trinken bringen kann. Ob man hier mit einer Flasche ollem Fusel zum Ziel kommt?';
    }    
    break;
    case 4:
    if(get_item_anz(4863)>=1)
    {
      //benötigte items entfernen
      remove_item(4863, 1);
      
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': Der Eremit greift sich die Flasche und sieht dich wie einen lange verschollenen Freund an. Jetzt wo du seine Aufmerksamkeit hast fragst du ihn nach der Brosche. Er hat diese bei sich, aber scheinbar bist du doch nicht sein Freund, denn er m&ouml;chte stolze 50 Silberlinge daf&uuml;r haben.';
    }    
    break;
    case 5:
    if(get_player_money($efta_user_id)>=5000)
    {
      //geld abziehen
      modify_player_money($efta_user_id, -5000);
      
      //neue koordinaten setzen
      $q_map=0;
      $q_x=291;
      $q_y=610;
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, map='$q_map', x='$q_x', y='$q_y' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
      $q_text=$q_questname.': Du hast die Brosche endlich bekommen und solltest sie Alzusar zur&uuml;ckbringen.';
    }
    break;
    case 6:
      $q_give_exp=1000;
      give_exp($q_give_exp);
      $exp=$exp+$q_give_exp;

      //quest abschließen
      mysql_query("UPDATE de_cyborg_quest SET flag1=flag1+1, erledigt=1 WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);

      //questmessage
      $q_text=$q_questname.': Du bringst Alzusar die Brosche zur&uuml;ck und er ist außer sich vor Freude. Er hat leider keine weiteren Wertsachen womit er dir danken kann. Da er jedoch tiefer in deiner Schuld steht als du jetzt ahnen kannst, macht er dir ein besonderes Geschenk. Alzusar ber&uuml;hrt dich an der Stirn und murmelt unverst&auml;ndliche Worte. Nach ein paar Sekunden ist er fertig und meint du w&uuml;rdest ehrenhalber in den Kreis seiner Familie geh&ouml;ren und jetzt die Familienaura tragen. Zus&auml;tzlich bekommst du 1000 Erfahrungspunkte.';
    break;
  }//switch flag1 ende
}

?>
