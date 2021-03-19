<?php
//transmitterquest

//anzahl der möglichen quests berechnen: maxwt-952 / 48
//$result  = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data",$db);
if($sv_server_tag=='123'){
	$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
	$row     = mysql_fetch_array($result);
	$maxtick = $row["tick"]-952;
	if($maxtick<0)$maxtick=0;
	$maxtquests = floor($maxtick / 48);
}else{
	$maxtquests=1000000000;
}

//koordinaten für quests u.ä. definieren
//map, x, y
//lm1
$efta_koord[]= array (  0,  -20, 13);
$efta_koord[]= array (  0, 84, -50);
$efta_koord[]= array (  0,  31, 36);
$efta_koord[]= array (  0,  -17, -58);
$efta_koord[]= array (  0, 54, -13);
$efta_koord[]= array (  0, 36, 54);
$efta_koord[]= array (  0,  -55, 69);
$efta_koord[]= array (  0,  47, 70);
$efta_koord[]= array (  0,  -16, -37);
$efta_koord[]= array (  0,   -63, 54);
$efta_koord[]= array (  0,  4, -76);
$efta_koord[]= array (  0,  17, 72);
$efta_koord[]= array (  0,  -28, 59);
$efta_koord[]= array (  0,  -22, 50);
$efta_koord[]= array (  0,  54,  34);
$efta_koord[]= array (  0,  -60,  12);
$efta_koord[]= array (  0,   69, 59);
$efta_koord[]= array (  0,  -77, 32);
$efta_koord[]= array (  0,  8, 35);
$efta_koord[]= array (  0,  -58, 24);
$efta_koord[]= array (  0,   -65, -14);
$efta_koord[]= array (  0,  26, -65);
$efta_koord[]= array (  0,   -7, -68);
$efta_koord[]= array (  0,  44, -39);
$efta_koord[]= array (  0,  110, 37);
//$efta_koord[]= array (   -17,  -38);
//erst später im spiel integriergen, abhängig von der verstrichenen rundenlaufzeit
if($maxtick>0)
{
if($sv_winscore/$maxtick<2)$efta_koord[]= array (  0,   -56,  -74);
//lm2
if($sv_winscore/$maxtick<1.6)$efta_koord[]= array (  0,  382, 690);
//lm3
if($sv_winscore/$maxtick<1.5)$efta_koord[]= array (  0,  660, -111);
//lm4
if($sv_winscore/$maxtick<1.2)$efta_koord[]= array (  0,  351, -680);
}

/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
// quest abbrechen
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
if($_REQUEST["tqcancel"]==1)
{
  //überprüfen ob man noch weitere quets machen darf
  $db_daten=mysql_query("SELECT * FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ=1",$eftadb);
  $row = mysql_fetch_array($db_daten);
  $flag2=$row["flag2"];

  if($flag2<$maxtquests)//es geht noch weiter
  {	
    //die quest abbrechen
    //die quest wieder auf den 1. status setzen und neue koordinaten finden
    $newplace=0;
  	while ($newplace==0)
  	{
  	  $i=mt_rand(0,count($efta_koord)-1);
  	  if($efta_koord[$i][0]!=$map OR $efta_koord[$i][1]!=$x OR $efta_koord[$i][2]!=$y)
  	  {
  	    $newplace=1;
  	    $newmap=$efta_koord[$i][0];
  	    $newx=$efta_koord[$i][1];
  	    $newy=$efta_koord[$i][2];
  	  }
  	}
  	//neue koordinaten in der db hinterlegen
  	mysql_query("UPDATE de_cyborg_quest SET flag1=0, flag2=flag2+1, map='$newmap', x='$newx', y='$newy' WHERE typ=1 AND user_id='$efta_user_id'",$eftadb);
  	
    //die info über den abbruch
  	$cancelmsg='<font color="#FF0000"><b>Die aktuelle Quest wurde abgebrochen und neue Koordinaten vergeben.</b></font><br><br>';
  }
  else $cancelmsg='<font color="#FF0000"><b>Im Moment sind keine weiteren Transmitterquests m&ouml;glich. Alle 48 Wirtschaftsticks kommt eine weitere Quest dazu, wobei die Quests erst nach 1000 Wirtschaftsticks beginnen.</b></font><br><br>';
	
}

/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
//questmenü
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
if($sv_efta_in_de==1 AND $showmenu==1)
{
  //überprüfen ob man bereits einen passenden db-eintrag hat
  $db_daten=mysql_query("SELECT * FROM de_cyborg_quest WHERE user_id='$efta_user_id' AND typ=1",$eftadb);
  $anz = mysql_num_rows($db_daten);
  //falls es keinen gibt, diesen anlegen
  if ($anz==0)
  {
  	mysql_query("INSERT INTO de_cyborg_quest SET user_id='$efta_user_id', typ=1, map=0, x=-20, y=13, erledigt=0",$eftadb);
  	$zielmap=0;
  	$zielx=-20;
  	$ziely=13;
  	$questpart=0;
  	$anzquests=0;
  	$anzquestswin=0;
  }
  else 
  {
  	$row = mysql_fetch_array($db_daten);
  	$zielmap=$row["map"];
  	$zielx=$row["x"];
  	$ziely=$row["y"];
  	$questpart=$row["flag1"];
  	$anzquests=$row["flag2"];
  	$anzquestswin=$row["flag3"];
  }
  

  
  echo '<tr><td colspan="4">&nbsp;</td></tr>';
  $tq_text=$cancelmsg;
  $tq_text.='<b>Erledigte Transmitterquests: '.$anzquests.'/'.$maxtquests.'</b>';
  if($sv_server_tag=='123'){
	$tq_text.='<br>Erfolgreiche Transmitterquests: '.$anzquestswin.' ('.number_format($anzquestswin*200000, 0,"",".").' Dyharra)';
  }else{
	$tq_text.='<br>Erfolgreiche Transmitterquests: '.$anzquestswin;
  }
  
  $tq_text.='<br>Transmitterquest-Koordinaten: '.$zielx.':'.$ziely;
  if($questpart==0)$tq_ziel='Finde ein sph&auml;risches Wesen, welches die Endkoordinaten kennt.'; else $tq_ziel='Besiege den Transmitterw&auml;chter und erf&uuml;lle die Aufgabe.';
  $tq_text.='<br>Aktuelles Ziel: '.$tq_ziel;
  $tq_text.='<br><a href="#" onClick="lnk(\'qdo=1\')"><div class="b1">erledigen</div></a>';
  //bereich für den sprung zur nächsten quest
  $tq_text.='<br><br>Hier hast Du die M&ouml;glichkeit die aktuelle Quest abzubrechen und neue Koordinaten zu erhalten. Bei einem Abbruch gilt die aktuelle Quest als verloren.';
  
  $tq_text.='<br><a href="#" onClick="lnk(\'q=1&tqcancel=1\')"><div class="b1">abbrechen</div></a>';
  
  echo '<tr align="center"><td class="cell" colspan="5">'.$tq_text.'</td></tr>';

}


/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
// questablauf
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
if($sv_efta_in_de==1 AND !isset($_REQUEST["tqcancel"]) AND isset($questid) AND !isset($showmenu))
{
  //questname
  $q_questname='Transmitterquest';
  
  //flagdefinition
  //flag 1: fortschritt der quest auf der suche nach koordinaten
  //flag 2: anzahl der quests insgesamt
  //flag 3: anzahl der erfolgreichen quests
    
  //flag 10: transmitterwächter getötet
  
  //überprüfen ob man das eftaprojekt hat
  //if ($techs[25]!=0){//efta-projekt

  //überprüfen ob man noch weitere quets machen darf
  if($flag2<$maxtquests){//es geht noch weiter
  	//überprüfen ob man gerade bei der 1. station, oder schon weiter ist
  	if($flag1==0) //1. station: neue koordinaten vergeben
  	{
  	  $newplace=0;
  	  while ($newplace==0)
  	  {
  	  	$i=mt_rand(0,count($efta_koord)-1);
  	  	if($efta_koord[$i][0]!=$map OR $efta_koord[$i][1]!=$x OR $efta_koord[$i][2]!=$y)
  	  	{
  	  	  $newplace=1;
  	  	  $newmap=$efta_koord[$i][0];
  	  	  $newx=$efta_koord[$i][1];
  	  	  $newy=$efta_koord[$i][2];
  	  	}
  	  }
  	  //erfahrungspunkte gutschreiben
  	  $expgew=100;
  	  give_exp($expgew);
  	  
  	  //neue koordinaten in der db hinterlegen
  	  mysql_query("UPDATE de_cyborg_quest SET flag1=1, map='$newmap', x='$newx', y='$newy' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);
  	  $q_text=$q_questname.': Ein sph&auml;risches Energiewesen &uuml;bermittelt dem Cyborg Daten &uuml;ber einen Tempor&auml;rtransmitter, den man mit 5 Tronic aktivieren kann. Die Koordinaten sind: X: '.$newx.' Y: '.$newy.'<br>Der Cyborg erh&auml;lt '.$expgew.' Erfahrungspunkte.';
  	  
  	}
  	else //man ist am zielpunkt angekommen 
  	{
      if($flag10>0)
      {
        //kein kampf wenn quest
        $dokampf=1;
        //schauen ob er die benötigten x tronic hat
        $db_daten=mysql_query("SELECT restyp05 FROM de_user_data WHERE user_id='$ums_user_id'",$db);
        $row = mysql_fetch_array($db_daten);
        $tronic=$row["restyp05"];

        if ($tronic >=5){
          $expgew=100;
          if($level>=$maxplayerlevel)$expgew=0;
          if($sv_server_tag=='123'){
			$text='Der Tempor&auml;rtransmitter wurde durch 5 Tronic aktiviert und 200.000 Dyharra wurden aus dem Transfer gewonnen.<br>Der Cyborg erh&auml;lt '.$expgew.' Erfahrungspunkte.<br>Der Cyborg erh&auml;lt einen Questpunkt.';
		  }else{
			$text='Der Tempor&auml;rtransmitter wurde durch 5 Tronic aktiviert.<br>Der Cyborg erh&auml;lt '.$expgew.' Erfahrungspunkte.<br>Der Cyborg erh&auml;lt einen Questpunkt.';  
		  }
          
		  mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);

          //erfahrungspunkte gutschreiben
          mysql_query("UPDATE de_cyborg_data SET exp = exp + '$expgew', questpoints=questpoints+1 WHERE user_id='$efta_user_id'",$eftadb);
          //rohstoffe gutschreiben, abziehen
		  if($sv_server_tag=='123'){
			mysql_query("UPDATE de_user_data SET restyp02 = restyp02 + 200000, restyp05=restyp05-5 WHERE user_id='$ums_user_id'",$db);
		  }else{
			mysql_query("UPDATE de_user_data SET restyp05=restyp05-5 WHERE user_id='$ums_user_id'",$db);
		  }
          
          //die quest wieder auf den 1. status setzen und neue koordinaten finden
          //dazu mitloggen was man bekommen hat
  	  	  $newplace=0;
  	  	  while ($newplace==0)
  	  	  {
  	  		$i=mt_rand(0,count($efta_koord)-1);
  	  		if($efta_koord[$i][0]!=$map OR $efta_koord[$i][1]!=$x OR $efta_koord[$i][2]!=$y)
  	  		{
  	  	  	  $newplace=1;
  	  	  	  $newmap=$efta_koord[$i][0];
  	  	  	  $newx=$efta_koord[$i][1];
  	  	  	  $newy=$efta_koord[$i][2];
  	  		}
  	  	  }
  	  	  //neue koordinaten in der db hinterlegen
  	  	  mysql_query("UPDATE de_cyborg_quest SET flag1=0, flag2=flag2+1, flag3=flag3+1, flag10=0, map='$newmap', x='$newx', y='$newy' WHERE typ='$questid' AND user_id='$efta_user_id'",$eftadb);


          echo '<script>lnk("");</script>';
          exit;
        }
        else
        {
          $text='Es ist nicht genug Tronic zur Aktivierung des Tempor&auml;rtransmitters vorhanden.';
          mysql_query("UPDATE de_cyborg_data SET showmsg='$text' WHERE user_id='$efta_user_id';",$eftadb);
          echo '<script>lnk("");</script>';
          exit;
        }
      }
      else //transmitterwächter
      {
        //gegnerlevel in dem gebiet feststellen
        $db_datenx=mysql_query("SELECT lvlmax FROM de_cyborg_enm_spawn WHERE xvon<='$x' AND xbis>='$x' AND yvon<='$y' AND ybis>='$y'",$eftadb);
        $rowx = mysql_fetch_array($db_datenx);
        $enm_level=intval($rowx["lvlmax"]);
        //wenn der gegner kleiner als der spieler ist, dann den gegner auf die stufe des spielers anheben
        if($enm_level<$level)$enm_level=$level;
        if($enm_level<1)$enm_level=1;
        //passenden gegner laden
      	$enm=enm_load($enm_level,$enm_level);
      	$enm["name"]='Transmitterw&auml;chter';
      	$enm["questid"]=1;
      	$enm["flagid"]=10;
      	enm_add2player($efta_user_id, $enm);
      	echo '<script>lnk("");</script>';
      	exit;
      }
    }
  	//$q_text=$q_questname.': Im Moment sind keine weiteren Transmitterquests m&ouml;glich. Alle 48 Wirtschaftsticks kommt eine weitere Quest dazu.';
  }
  else //keine weitere quest möglich
  {
	$q_text=$q_questname.': Im Moment sind keine weiteren Transmitterquests m&ouml;glich. Alle 48 Wirtschaftsticks kommt eine weitere Quest dazu, wobei die Quests erst nach 1000 Wirtschaftsticks beginnen.';
  }
  /*
  }
  else 
  {
  	$q_text=$q_questname.': F&uuml;r diese Aufgabe wird das Geb&auml;ude "Efta-Projekt" ben&ouml;tigt.';
  }
  */
  
  
  
  
  
}//questende  
?>