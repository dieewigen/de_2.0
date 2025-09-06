<?php 
include_once('functions.php');

if(!isset($pt)){
	$pt=loadPlayerTechs($_SESSION['ums_user_id']);
}

/*
if($_SESSION['ums_user_id']==1){
	print_r($pt);
}
*/

if(!isset($_SESSION['helperid'])){
  $_SESSION['helperid']=$helper_progress;
}

//anhand vom $helper_progress die passenden infos zur verf�gung stellen
$helper_dontshow=0;
if(isset($_REQUEST['helperdo'])){
	$_SESSION['helperid']=$_SESSION['helperid']+intval($_REQUEST['helperdo']);
	
	if($_SESSION['helperid']<0){
		$_SESSION['helperid']=0;
	}
	if($_SESSION['helperid']>$helper_progress){
		$_SESSION['helperid']=$helper_progress;
	}
}

//for($i=0;$i<=100;$i++){$_SESSION['helperid']=$i;

switch($_SESSION['helperid']){
  case 0:
    $helper_msg='Willkommen bei Die Ewigen, mein Name ist Fluxurion und ich stehe Dir mit meinem Rat zur Seite. Wenn Du meine Dienste nicht mehr ben&ouml;tigst, 
    kannst Du mich im Men&uuml; unter Optionen bei "Berater aktivieren" entlassen. Nat&uuml;rlich kannst Du mich sp&auml;ter jederzeit wieder einstellen.<br><br>F&uuml;r mehr Informationen kannst Du einfach auf "weiter" klicken.';
    $helper_picid=1;
    
    if($helper_progress==0){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 1:
    $helper_msg='Zum Einstieg ein paar n&uuml;tzliche Erkl&auml;rungen.<br><br>Direkt &uuml;ber mir siehst Du die sogenannte Rohstoffleiste, die aber noch mehr Informationen als nur die Rohstoffe enth&auml;lt.<br>Es gibt 5 Rohstoffarten (Multiplex, Dyharra, Iradium, Eternium und Tronic) und wenn Du den Mauszeiger &uuml;ber die Symbole h&auml;ltst, erh&auml;ltst Du eine Beschreibung von ihnen.';
    $helper_picid=2;
    
    if($helper_progress==1){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 2:
    $helper_msg='Rechts von den Rohstoffen werden 3 Uhrzeiten angezeigt. Von oben nach unten:<br>- <b>Serverzeit</b>: Die aktuelle Uhrzeit des Servers.
    <br>- <b>Letzter Wirtschaftstick</b>: Sie sind f&uuml;r den &ouml;konomischen Teil wichtig, also f&uuml;r Bau, Forschungen und Rohstoffgewinnung. 
	<br>-<b>Letzter Kampftick</b>: Sie sind f&uuml;r den Kampf und das Versenden von Flotten zust&auml;ndig.
	<br><br>Ein Tick kann mehrere Minuten Echtzeit betragen, wie lange das ist wird im Hauptaccount bei der jeweiligen Serverinformation angezeigt.';
    $helper_picid=3;
    
    if($helper_progress==2)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 3:
    $helper_msg='Oben auf der Seite, unterhalb der Rohstoffe, sind 2 Signalleuchten, die Dich angreifende (rot) und verteidigende (gr&uuml;n) Flotten anzeigen.<br><br>Rechts davon siehst du Deine aktuellen Punkte und noch weiter rechts siehst Du die Anzahl Deiner Credits.<br><br>
    Unter den Uhrzeiten gibt es noch 2 Symbole die aufleuchten, wenn neue Nachrichten eintreffen.';
    $helper_picid=4;
    
    if($helper_progress==3)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 4:
    $helper_msg='Rechts von mir siehst Du den Chatbereich. Es gibt dort verschiedene Channel, die man unten w&auml;hlen kann:
	<br>- <b>Global (orange)</b>: Hier k&ouml;nnen alle Spieler server&uuml;bergreifend miteinander chatten und sich gegenseitig helfen. Sollten Dir das zuviele Nachrichten sein, so kann der Channel in den Optionen deaktiviert werden.</b> 	
    <br>- <b>Server (blau)</b>: Hier k&ouml;nnen alle Spieler miteinander chatten und sich gegenseitig helfen. Hier kannst Du auch nach einer Allianz fragen, die dich aufnimmt. Sollten Dir das zuviele Nachrichten sein, so kann der Channel in den Optionen deaktiviert werden.</b> 
    <br>- <b>Sektor (wei&szlig;)</b>: Hier kannst Du mit Deinen Sektorkollegen chatten.</b> 
    <br>- <b>Allianz (gr&uuml;n)</b>: Hier kannst Du mit Deinen Allianzkollegen chatten, wenn Du in einer Allianz bist.<br>Es wird empfohlen einer Allianz beizutreten, denn dort kann man wichtige Boni erhalten.';
    $helper_picid=5;
    
    if($helper_progress==4)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 5:
    $helper_msg='Links von mir siehst Du das Hauptmen&uuml;. Dort kannst Du die einzelnen Spielbereiche erreichen.';
    $helper_picid=6;
    
    if($helper_progress==5){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 6:
    $helper_msg='Jeder Spieler befindet sich beim Start im Anfangsektor 1. Diesen verl&auml;&szlig;t man, sobald man mehr als 5 Million Punkte, oder 10 Kollektoren hat. Kollektoren sind die Hauptquelle der von Dir ben&ouml;tigten Rohstoffe.<br><br>';
    $helper_picid=7;
    
    if($helper_progress==6)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 7:
    if($_SESSION['ums_rasse']==1)$helper_hs='Konstruktionszentrum';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Werkstatt';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Zentralbau';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Kleiner Stock';
    $helper_msg='Kommen wir nun zur ersten Aufgabe f&uuml;r Dich. Gehe links im Men&uuml; auf den Punkt Technologien -> Geb&auml;ude und erteile den Bauauftrag f&uuml;r: <b>'.$helper_hs.'</b><br><br>Die Rohstoffe f&szlig;r das Geb&auml;ude werden abgezogen und Du siehst unter der Rohstoffleiste das Geb&auml;ude, das im Bau ist.<br><br>Bei "Fertigstellungszeitpunkt Echtzeit" findest Du den Zeitpunkt der Fertigstellung des Geb&auml;udes.<br><br>Bis das Geb&auml;ude fertig ist, kannst Dich ja einmal links durch das Men&uuml; klicken, um eine kleine &Uuml;bersicht zu bekommen.<br><br>';
    $helper_picid=8;
    
    if($helper_progress==7)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,1))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 8:
    $helper_msg='Meinen Gl&uuml;ckwunsch, Du hast das erste Geb&auml;ude fertiggestellt, welches die Grundlage f&uuml;r weitere Geb&auml;ude ist.<br><br>
    Der Bau weitere Geb&auml;ude ben&ouml;tigt viele Rohstoffe, die der planetare Grundrohstoffertrag auf Dauer nicht decken kann. Um die Versorgung mit ausreichend Ressourcen sicher zu stellen ben&ouml;tigst Du Energiekollektoren und Energie-Materie-Wandler.
    
    ';
    $helper_picid=9;
    
    if($helper_progress==8)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 9:
    if($_SESSION['ums_rasse']==1)$helper_hs='Kollektorenfabrik';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Sonnenschildfabrik';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Zentrum der Wandler';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Arbeiterwabe';
    $helper_msg='Baue, um damit Kollektoren produzieren zu k&ouml;nnen, als n&auml;chstes folgendes Geb&auml;ude: '.$helper_hs.'<br><br>Die Kollektoren sind Deine wichtigste Energiequelle und wecken schnell die Gier der anderen Spieler. Wichtig ist eine ausgewogene Anzahl der Kollektoren zu Deinen Schiffen und Verteidungsanlagen.';
    $helper_picid=10;
    
    if($helper_progress==9)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,7))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;    
  case 10:
    if($_SESSION['ums_rasse']==1)$helper_hs='Materieumwandler M';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Raffinerie M';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Wandlerkammer M';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Arbeiterlager M';
    $helper_msg='Gut gemacht, jetzt fehlt nur noch ein Energie-Materie-Wandler. Baue als n&auml;chstes folgendes Geb&auml;ude: <b>'.$helper_hs.'</b><br><br>Die Kollektoren sind Deine wichtigste Energiequelle und wecken schnell die Gier der anderen Spieler. Wichtig ist eine ausgewogene Anzahl der Kollektoren zu Deinen Schiffen und Verteidungsanlagen.<br><br>Gehe jetzt links auf den Punkt Ressourcen und baue 17 Kollektoren.<br><br>Damit kommst Du raus aus Sektor 1 und in einen Spielersektor. Bei dem Umzug kannst Du kurz ausgeloggt werden. Logge Dich nach dem Umzug einfach wieder ein.';
    $helper_picid=11;
    
    if($helper_progress==10){
      //test ob das geb�ude fertig ist
      if(hasTech($pt,14) AND $helper_col>0){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 11:
    if($_SESSION['ums_rasse']==1)$helper_hs='Forschungszentrum';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Alchemielabor';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Kammer der Evolution';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Netzwerk des Denkens';
    $helper_msg='Gut, jetzt bekommst Du bei jedem Wirtschaftstick mehr Multiplex.<br><br>Um bessere Einheiten und Geb&auml;ude bauen zu k&ouml;nnen, ben&ouml;tigt Du die passenden Forschungen daf&uuml;r. Um forschen zu k&ouml;nnen, ben&ouml;tigst du folgendes Geb&auml;ude: <b>'.$helper_hs.'</b><br><br>Baue jetzt dieses Geb&auml;ude.';
    $helper_picid=1;
    
    if($helper_progress==11)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,8))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 12:
    if($_SESSION['ums_rasse']==1)$helper_hs='Transmitterfeld';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Dimensionsfeld';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Netzevolution';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Transwabenfeld';
    
    $helper_msg='Jetzt ist es soweit, Deine erste Forschung steht an. Forschungen und der Geb&auml;udebau laufen parallel. Gehe links im Men&uuml; auf Technologien -> Forschung und starte dort folgende Forschung: <b>'.$helper_hs.'</b><br><br>Diese Forschung ist eine der Grundlagen f&uuml;r den Handel und den Geheimdienst.';

    if($_SESSION['ums_rasse']==1)$helper_hs='Materieumwandler D,I,E';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Raffinerie D,I,E';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Wandlerkammer D,I,E';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Arbeiterlager D,I,E';
    $helper_msg.='<br><br>Neben Multiplex ben&ouml;tigst Du noch Dyharra, Iradium und Eternium. Baue daf&uuml;r auch noch die passenden Geb&auml;ude: <b>'.$helper_hs.'</b><br><br>Wenn Du mehr als einen Energie-Materie-Wander hast, kannst Du unter Ressourcen beim Energieverteilungsschl&uuml;ssel festlegen wie viel Prozent der Energie in den jeweiligen Rohstoff umgewandelt werden soll.';
    
    
    $helper_picid=2;
    
    if($helper_progress==12)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,65) AND hasTech($pt,14))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;    
  case 13:
    if($_SESSION['ums_rasse']==1)$helper_hs='Planetare B&ouml;rse und Erweitertes Konstruktionszentrum';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Planetarer Markt und Erweiterte Werkstatt';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Platz des Sektortausches und Gro&szlig;er Zentralbau';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Planetare Handelswabe und Stock';
    $helper_msg='Sehr gut, du verf&uuml;gst jetzt &uuml;ber eine gute Grundproduktion aller Rohstoffe.<br><br>Der n&auml;chste Schritt ist die Anbindung Deines Sonnensystems an das intergalaktische Handelssystem um den planetaren Rohstoffertrag zu erh&ouml;hen. Baue dazu zuerst folgende Geb&auml;ude als Grundlage: <b>'.$helper_hs.'</b><br><br>Damit erh&auml;ltst Du die Voraussetzung f&uuml;r gr&ouml;&szlig;ere Geb&auml;ude und die M&ouml;glichkeit Rohstoffe f&uuml;r die Sektorgeb&auml;ude Deines Sektors zu spenden. Baue Dich aber erstmal auf, bevor Du Rohstoffe spendest.';
   
    $helper_picid=3;
    
    if($helper_progress==13)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,3) AND hasTech($pt,2))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 14:
    if($_SESSION['ums_rasse']==1)$helper_hs='Weltraumhandelsgilde';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Galaktische Handelszunft';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Platz des Raumtausches';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Handelswabe des Universums';
    $helper_msg='Mit dem n&auml;chsten Geb&auml;ude erschlie&szlig;t Du Dir das Handelssystem, &uuml;ber das Du mit anderen Spielern Raumschiffe und Rohstoffe handeln kannst.<br><br>Durch den Handel steigt gleichzeitig der Planetaren Rohstoffertrag stark an, was der Energie von 47 Kollektoren entspricht.<br><br>Baue jetzt folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=4;
    
    if($helper_progress==14)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,14))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 15:
    if($_SESSION['ums_rasse']==1)$helper_hs='Raumwerft';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Raumschmiede';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Schwarmstock';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Drohnenwabe';
    $helper_msg='Bevor wir die Wirtschaft weiter st&auml;rken, sollten wir uns um den milit&auml;rischen Bereich k&uuml;mmern, denn die Kollektoren ben&ouml;tigen einen guten Schutz.<br><br>Baue daher folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=5;
    
    if($helper_progress==15)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,13))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 16:
    $helper_msg='Perfekt, Du kannst jetzt Deine ersten Raumschiffe bauen. Gehe dazu einfach links auf Produktion und baue 50 J&auml;ger.<br><br>Wenn die J&auml;ger im Bau sind, kannst Du unter Ressourcen noch weitere Kollektoren bauen um auf 25 St&uuml;ck zu kommen.<br><br>Du kannst auch parallel weitere Forschungen durchf&uuml;hren um bessere Schiffe zu erhalten. Welche Forschungen Du im Detail ben&ouml;tigst, siehst Du unten auf der Produktionsseite unter dem Punkt "Informationen".';
   
    $helper_picid=6;
    
    if($helper_progress==16)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 17:
    if($_SESSION['ums_rasse']==1)$helper_hs='Hochleistungsumwandler M,D';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='gr. Raffinerie M,D';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Gro&szlig;e Wandlerkammer M,D';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Arbeitergrosslager M,D';
    $helper_msg='Aufbauend auf den kleinen Materie-Energie-Wandlern gibt es noch die gro&szlig;en Ausf&uuml;hrungen, die doppelt soviel Materie aus der gleichen Energiemenge gewinnen k&ouml;nnen. Baue daher die besseren Umwandler f&uuml;r Multiplex und Dyharra. Die weiteren Umwandler kannst Du direkt, oder auch sp&auml;ter bauen, wenn Dein Rohstoffbedarf f&uuml;r Iradium und Eternium ansteigt.<br><br>Baue folgende Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=7;
    
    if($helper_progress==17)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,18) AND hasTech($pt,19))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 18:
    if($_SESSION['ums_rasse']==1)$helper_hs='Weltraumscanner, Tachyonscanner, Neutronenscanner';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Weltraumsonar, Elektronensonar, Photonensonar';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Kammer des Raumblickes, Erweiterung des Raumblickes, Kammer des Tiefraumblickes';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Augen der Arbeiterin, Augen der Drohne, Augen der Koenigin';
    $helper_msg='Je mehr Kollektoren Du hast, desto eher wirst Du angegriffen. Um die Angreifer rechtzeitig zu sehen, ben&ouml;tigst Du passende Scanner. Bauer daher folgende Geb&auml;ude: <b>'.$helper_hs.'</b><br><br>Mit jedem Geb&auml;ude steigt die Chance die Angreifer zu entdecken. Wenn Du alle Geb&auml;ude hast, dann wird auch jede angreifende Flotte angezeigt. Flotten die Dich, oder jemanden aus Deinem Sektor angreifen werden f&uuml;r alle Sektormitglieder links unter dem Punkt Sektorstatus angezeigt.<br><br>Im Sektorstatus siehst Du auch die Bewegungen der Flotten Deiner Sektormitspieler.';
   
    $helper_picid=8;
    
    if($helper_progress==18)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,10) AND hasTech($pt,11) AND hasTech($pt,12))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;

  
  case 19:
    if($_SESSION['ums_rasse']==1)$helper_hs='Geheimdienst, Tarnfeld,Fusionsantrieb und Ionenantrieb';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Spionageabteilung, Dimensionsverschiebung, Magmaantrieb und Impulsantrieb';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Zentrum der Unterwanderung, Schattenfeld, Trochanterd&uuml;se und Femurd&uuml;se';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Aufkl&auml;rerwabe, Schwarzfeld, Strukturfl&uuml;gel und Chitinfl&uuml;gel';
  
    $helper_msg='Ein weiterer wichtiger Punkt ist der Geheimdienst. Dort kann man durch Sonden und Agenten Informationen &uuml;ber andere Spieler in Erfahrung bringen.<br><br>
    Sonden bringen ein paar Grundinformationen, wie den Onlinestatus, die Rasse und die Anzahl der Einheiten/Geb&auml;ude/Rohstoffe.<br><br>
    F&uuml;r besser gesch&uuml;tzte Informationen ist der Einsatz von Agenten n&ouml;tig.<br><br>Baue und erforsche folgendes: <b>'.$helper_hs.'</b>';
   
    $helper_picid=9;
    
    if($helper_progress==19)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,9) AND hasTech($pt,66) AND hasTech($pt,62))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;  

  case 20:
    $helper_msg='Das Geheimdienstgeb&auml;ude ist fertig. Baue links im Men&uuml; unter Geheimdienst 20 Sonden und 100 Agenten.<br><br>
    Mehr Informationen zu den Einheiten erh&auml;ltst du als Tooltip, wenn Du die Maus &uuml;ber die Einheitenbilder h&auml;ltst.<br><br>
    Je mehr Agenten Du hast, desto schwerer f&auml;llt es Gegnern erfolgreich Eins&auml;tze bei Dir durchzuf&uuml;hren.<br><br>
    Versuche doch einmal eine Sonde zu schicken. Gehe auf Sektor und suche Dir einen Spieler aus und klicke unter "Aktion" das "S" an. Danach siehst Du dann den Sondenbericht.';
   
    $helper_picid=10;
    
    if($helper_progress==20)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  
  case 21:
    if($_SESSION['ums_rasse']==1)$helper_hs='Recyclotron';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Schrottschmelze';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Bau der Verwertung';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Extraktorwabe';
  
    $helper_msg='Zur&uuml;ck zu den K&auml;mpfen, wenn Du angegriffen wirst und Schiffe verlierst, entsteht viel Raumschrott. Mit den passenen Anlagen kann dieser recycelt werden. Baue daher folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=11;
    
    if($helper_progress==21)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,6))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
    break;

  case 22:
    if($_SESSION['ums_rasse']==1)$helper_hs='Artefaktzentrum';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Artefakthort';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Artefaktbau';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Artefaktstock';
  
    $helper_msg='Des Weiteren gibt es viele unterschiedliche Artefakte, die Dir helfen k&ouml;nnen. Du kannst sie auf Missionen, als t&auml;glichen Allianzbonus, bei K&auml;mpfen gegen die NPC-Gegner oder im Schwarzmarkt erhalten. Bauher daher folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=1;
    
    if($helper_progress==22)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,28))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  
  case 23:
    $helper_msg='Das waren alle Hinweise die ich f&uuml;r Dich habe, Du kannst das Spiel jetzt auf eigene Faust weiter erforschen.<br><br>Wenn Du meine Dienste nicht mehr ben&ouml;tigst, kannst Du mich in den Optionen bei "Berater aktivieren" entlassen.<br><br><br><br>Bitte macht Vorschl&auml;ge um den Berater zu verbessern.</a>';
   
    $helper_picid=1;
    
    if($helper_progress==22)
    {
      //test ob das geb�ude fertig ist
      if(hasTech($pt,6) AND 1==2)
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;  
  
  default:
    //wenn nichts pa�t, dann den helper gar nicht anzeigen
    $helper_dontshow=1;
  break;
}

if($helper_dontshow==0)
{
  rahmen_oben('Fluxurion der Berater');

  echo '<div class="cell" style="width: 570px; height: 256px; font-size: 14px; position: relative;">';
  echo '<div style="float: left;"><img src="gp/g/berater'.$helper_picid.'.png" border="0"></div>';
  echo $helper_msg;

  //zur�ck/weiter-buttons
  echo '<div style="position: absolute; bottom: 0px; right: 0px; width: 442px; text-align: center;">';
  echo '<a href="'.$_SERVER['PHP_SELF'].'?helperdo=-1">zur&uuml;ck</a> '.($_SESSION['helperid']+1).'/'.($helper_progress+1).' <a href="'.$_SERVER['PHP_SELF'].'?helperdo=1">weiter</a>';
  echo '</div>';
  
  echo '</div>';
  rahmen_unten();
}

//}die('</body></html>');//debug
?>