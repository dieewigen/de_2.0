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

//die Erklärungen zur Oberfläche (Schritte 1-6) überspringen und direkt zur ersten Aufgabe (Schritt 7) gehen;
//die übersprungenen Schritte bleiben über "zurück" erreichbar
$helper_erste_aufgabe=7;
if(isset($_REQUEST['helperskip']) && $helper_progress<$helper_erste_aufgabe){
	$sql = "UPDATE de_user_data SET helperprogress=? WHERE user_id=? AND helperprogress<?";
	mysqli_execute_query($GLOBALS['dbi'], $sql, [$helper_erste_aufgabe, $_SESSION['ums_user_id'], $helper_erste_aufgabe]);
	$helper_progress=$helper_erste_aufgabe;
	$_SESSION['helperid']=$helper_erste_aufgabe;
}

//welche Ansicht sieht der Spieler? gleiche Bedingung wie die Rohstoffleiste in resline.php
if($_SESSION['ums_mobi']==1){
	$helper_ui='mobile';
}elseif($_SESSION['de_frameset']==1){
	$helper_ui='classic';
}else{
	$helper_ui='standard';
}

//Wege zu den Spielbereichen, je nach Ansicht (Classic: Menü links, Standard: Leiste oben, Mobil: Menü-Button)
$helper_wege=array(
	'ressourcen' => array(
		'classic'  => 'links im Men&uuml; auf <b>Ressourcen</b>',
		'standard' => 'oben in der Leiste auf eines der Rohstoffsymbole (das &ouml;ffnet die <b>Ressourcen</b>)',
		'mobile'   => 'im Men&uuml; auf <b>Ressourcen</b>'),
	'vsysteme' => array(
		'classic'  => 'links im Men&uuml; auf <b>V-Systeme</b>',
		'standard' => 'rechts oben auf der Karte auf das Symbol f&uuml;r die <b>VS-&Uuml;bersicht</b>',
		'mobile'   => 'im Men&uuml; auf <b>V-Systeme</b>'),
	'sektor' => array(
		'classic'  => 'links im Men&uuml; auf <b>Sektor</b>',
		'standard' => 'ganz oben links &uuml;ber das Rassenlogo auf <b>Sektor</b>',
		'mobile'   => 'im Men&uuml; auf <b>Sektor</b>'),
	'sektorstatus' => array(
		'classic'  => 'links im Men&uuml; auf <b>Sektorstatus</b>',
		'standard' => 'auf eines der Flottensymbole in der zweiten Zeile der oberen Leiste',
		'mobile'   => 'im Men&uuml; auf <b>Sektorstatus</b>'),
	'optionen' => array(
		'classic'  => 'links im Men&uuml; unter <b>Optionen</b>',
		'standard' => 'ganz oben links &uuml;ber das Rassenlogo unter <b>Optionen</b>',
		'mobile'   => 'im Men&uuml; unter <b>Optionen</b>'),
);
//Spielbereiche, die in allen drei Menüs als eigener Punkt vorhanden sind
$helper_menuepunkte=array('technologien'=>'Technologien', 'produktion'=>'Produktion', 'geheimdienst'=>'Geheimdienst',
	'flotten'=>'Flotten', 'artefakte'=>'Artefakte', 'allianz'=>'Allianz', 'auktion'=>'Auktion', 'missionen'=>'Missionen',
	'spezialisierung'=>'Spezialisierung');
//Variablen mit helper_-Präfix, da diese Datei im globalen Bereich der Seite eingebunden wird
foreach($helper_menuepunkte as $helper_ziel => $helper_name){
	$helper_wege[$helper_ziel]=array(
		'classic'  => 'links im Men&uuml; auf <b>'.$helper_name.'</b>',
		'standard' => 'oben in der Men&uuml;leiste auf <b>'.$helper_name.'</b>',
		'mobile'   => 'im Men&uuml; auf <b>'.$helper_name.'</b>');
}
$helper_weg=array();
foreach($helper_wege as $helper_ziel => $helper_ziel_wege){
	$helper_weg[$helper_ziel]=$helper_ziel_wege[$helper_ui];
}

//Tooltips: am Handy durch Antippen, sonst mit dem Mauszeiger
$helper_mobil=($helper_ui=='mobile');

//Schritte zu Spielbereichen, die auf diesem Server deaktiviert sind, überspringen
$helper_skip=array();
if(($sv_deactivate_vsystems ?? 0)==1){
	$helper_skip=array_merge($helper_skip, array(29, 30, 32, 33)); //V-Systeme, Basisstern, Battlegrounds
}
if(($sv_deactivate_missions ?? 0)==1){
	$helper_skip[]=31;
}
$helper_richtung=(isset($_REQUEST['helperdo']) && intval($_REQUEST['helperdo'])<0) ? -1 : 1;
while(in_array($_SESSION['helperid'], $helper_skip)){
	if($helper_progress==$_SESSION['helperid']){
		$sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
		mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
		$helper_progress++;
	}
	$_SESSION['helperid']+=$helper_richtung;
}

//Name einer Technologie in der Rasse des Spielers, wie auf der Technologieseite
if(!function_exists('helper_tech')){
	function helper_tech($tech_id){
		$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT tech_name FROM de_tech_data WHERE tech_id=?", [$tech_id]);
		$row = mysqli_fetch_assoc($res);
		return $row ? getTechNameByRasse($row['tech_name'], $_SESSION['ums_rasse']) : '';
	}
}

//for($i=0;$i<=100;$i++){$_SESSION['helperid']=$i;

switch($_SESSION['helperid']){
  case 0:
    $helper_msg='Willkommen bei Die Ewigen, mein Name ist Fluxurion und ich stehe Dir mit meinem Rat zur Seite. Wenn Du meine Dienste nicht mehr ben&ouml;tigst, 
    kannst Du mich '.$helper_weg['optionen'].' bei "Berater aktivieren" entlassen. Nat&uuml;rlich kannst Du mich sp&auml;ter jederzeit wieder einstellen.<br><br>Mit "weiter" erkl&auml;re ich Dir zuerst die Oberfl&auml;che.';
    if($helper_progress<$helper_erste_aufgabe){
      $helper_msg.=' Willst Du gleich loslegen, <a href="'.htmlspecialchars(basename($_SERVER['SCRIPT_NAME']), ENT_QUOTES, 'UTF-8').'?helperskip=1">springe direkt zur ersten Aufgabe</a>. Die Erkl&auml;rungen findest Du danach jederzeit &uuml;ber "zur&uuml;ck".';
    }
    $helper_picid=1;
    
    if($helper_progress==0){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 1:
    if($helper_ui=='standard'){
      $helper_msg='Zum Einstieg ein paar n&uuml;tzliche Erkl&auml;rungen.<br><br>Ganz oben im Fenster siehst Du eine Leiste mit Deinen Rohstoffen, die aber noch mehr Informationen als nur die Rohstoffe enth&auml;lt.<br>Es gibt 5 Rohstoffarten (Multiplex, Dyharra, Iradium, Eternium und Tronic) und wenn Du den Mauszeiger &uuml;ber die Symbole h&auml;ltst, erh&auml;ltst Du eine Beschreibung von ihnen. Ein Klick auf einen Rohstoff &ouml;ffnet die Ressourcen.';
    }else{
      $helper_msg='Zum Einstieg ein paar n&uuml;tzliche Erkl&auml;rungen.<br><br>Direkt &uuml;ber mir siehst Du die sogenannte Rohstoffleiste, die aber noch mehr Informationen als nur die Rohstoffe enth&auml;lt.<br>Es gibt 5 Rohstoffarten (Multiplex, Dyharra, Iradium, Eternium und Tronic) und wenn Du '.($helper_mobil ? 'auf die Symbole tippst' : 'den Mauszeiger &uuml;ber die Symbole h&auml;ltst').', erh&auml;ltst Du eine Beschreibung von ihnen.';
    }
    $helper_picid=2;
    
    if($helper_progress==1){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 2:
    $helper_msg=($helper_ui=='standard' ? 'Ganz rechts in der oberen Leiste' : 'Rechts von den Rohstoffen').' werden 3 Uhrzeiten angezeigt. Von oben nach unten:<br>- <b>Serverzeit</b>: Die aktuelle Uhrzeit des Servers.
    <br>- <b>Letzter Wirtschaftstick</b>: Sie sind f&uuml;r den &ouml;konomischen Teil wichtig, also f&uuml;r Bau, Forschungen und Rohstoffgewinnung. 
	<br>-<b>Letzter Kampftick</b>: Sie sind f&uuml;r den Kampf und das Versenden von Flotten zust&auml;ndig.
	<br><br>Zwischen zwei Ticks vergehen mehrere Minuten Echtzeit. Wann die Ticks laufen, steht in der Serverinfo, die Du &uuml;ber den Button <b>Serverinfos</b> auf der &Uuml;bersicht erreichst.';
    if($helper_ui=='standard'){
      $helper_msg.=' Auch ein Klick auf die Uhrzeiten &ouml;ffnet die Serverinfo.';
    }
    $helper_picid=3;
    
    if($helper_progress==2)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 3:
    if($helper_ui=='standard'){
      $helper_msg='In der zweiten Zeile der oberen Leiste zeigen 2 Symbole Flotten an, die Dich angreifen (rot) oder verteidigen (gr&uuml;n). Solange keine Flotten unterwegs sind, bleiben sie grau. Ein Klick darauf f&uuml;hrt zum Sektorstatus.<br><br>Rechts daneben siehst Du Deine aktuellen Punkte.<br><br>
      Weiter rechts gibt es noch 2 Symbole f&uuml;r Hyperfunk und Nachrichten, die aufleuchten, wenn neue Nachrichten eintreffen.';
    }else{
      $helper_msg='Oben auf der Seite, unterhalb der Rohstoffe, erscheinen 2 Signalleuchten, sobald Flotten zu Dir unterwegs sind: Sie zeigen Dich angreifende (rot) und verteidigende (gr&uuml;n) Flotten an.<br><br>Rechts davon siehst Du Deine aktuellen Punkte.<br><br>
      Unter den Uhrzeiten erscheinen noch 2 Symbole, wenn neue Hyperfunknachrichten oder Nachrichten eintreffen.';
    }
    $helper_picid=4;
    
    if($helper_progress==3)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 4:
    if($helper_ui=='standard'){
      $helper_msg='Unten rechts siehst Du das Chatfenster.';
    }elseif($helper_ui=='mobile'){
      $helper_msg='Den Chat erreichst Du im Men&uuml; unter <b>DE-Chat</b>.';
    }else{
      $helper_msg='Rechts von mir siehst Du den Chatbereich.';
    }
    $helper_msg.=' Unten w&auml;hlst Du den Channel:
    <br>- <b>Global (orange)</b>: alle Spieler, server&uuml;bergreifend
    <br>- <b>Server (blau)</b>: alle Spieler dieses Servers, hier kannst Du auch nach einer Allianz fragen
    <br>- <b>Sektor (wei&szlig;)</b>: Deine Sektorkollegen
    <br>- <b>Allianz (gr&uuml;n)</b>: Deine Allianzkollegen
    <br><br>Global und Server kannst Du in den Optionen abschalten, wenn Dir das zu viele Nachrichten sind. Es lohnt sich, einer Allianz beizutreten, denn dort gibt es wichtige Boni.';
    $helper_picid=5;
    
    if($helper_progress==4)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 5:
    if($helper_ui=='standard'){
      $helper_msg='Oben in der Leiste findest Du das Hauptmen&uuml;. Dort kannst Du die einzelnen Spielbereiche erreichen.<br><br>Ein Klick auf das Rassenlogo ganz oben links bringt Dich zur &Uuml;bersicht. H&auml;ltst Du den Mauszeiger &uuml;ber das Logo, erreichst Du au&szlig;erdem Sektor und Optionen. Die Ressourcen &ouml;ffnest Du &uuml;ber die Rohstoffsymbole.';
    }elseif($helper_ui=='mobile'){
      $helper_msg='Das Hauptmen&uuml; &ouml;ffnest Du &uuml;ber den Button <b>Men&uuml;</b> ganz oben oder indem Du von links nach rechts wischst. Dort kannst Du die einzelnen Spielbereiche erreichen.';
    }else{
      $helper_msg='Links von mir siehst Du das Hauptmen&uuml;. Dort kannst Du die einzelnen Spielbereiche erreichen.';
    }
    $helper_picid=6;
    
    if($helper_progress==5){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 6:
    $helper_msg='Jeder Spieler befindet sich beim Start im Anfangsektor 1. Diesen verl&auml;&szlig;t man, sobald man mindestens 5 Millionen Punkte oder 10 Kollektoren hat. Solange Du in Sektor 1 bist, kannst Du weder angreifen noch angegriffen werden. Kollektoren sind die Hauptquelle der von Dir ben&ouml;tigten Rohstoffe.<br><br>';
    $helper_picid=7;
    
    if($helper_progress==6)
    {
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;
  case 7:
    if($_SESSION['ums_rasse']==1)$helper_hs='Konstruktionszentrum I';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Werkstatt I';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Zentralbau I';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Stock I';
    $helper_msg='Kommen wir nun zur ersten Aufgabe f&uuml;r Dich. Gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>Geb&auml;ude</b> und erteile den Bauauftrag f&uuml;r: <b>'.$helper_hs.'</b><br><br>Die Rohstoffe werden abgezogen und oben auf der Technologieseite siehst Du das Geb&auml;ude im Bau mit der verbleibenden Bauzeit. Den genauen Zeitpunkt der Fertigstellung siehst Du, wenn Du '.($helper_mobil ? 'auf den Namen des Geb&auml;udes tippst' : 'den Mauszeiger &uuml;ber den Namen des Geb&auml;udes h&auml;ltst').'.<br><br>Bis das Geb&auml;ude fertig ist, kannst Du Dich ja einmal durch das Men&uuml; klicken, um eine kleine &Uuml;bersicht zu bekommen.';
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
  //Schritte 9-13 folgen den Voraussetzungen im Technologiebaum: Forschungszentrum I (8) -> Forschung Kollektoren (80)
  //-> Kollektorenfabrik (7); Materieumwandler D (15) braucht Konstruktionszentrum II (2), die Weltraumhandelsgilde (4)
  //Planetare Börse (3) und Konstruktionszentrum IV (113). Früher kam die Kollektorenfabrik vor dem Forschungszentrum.
  case 9:
    $helper_msg='F&uuml;r Kollektoren brauchst Du zuerst eine Forschung, und forschen kannst Du erst mit dem richtigen Geb&auml;ude. Baue als n&auml;chstes: <b>'.helper_tech(8).'</b><br><br>Ein Geb&auml;ude und eine Forschung k&ouml;nnen gleichzeitig laufen, so kommst Du schneller voran.';
    $helper_picid=10;

    if($helper_progress==9)
    {
      //test ob das Forschungszentrum I fertig ist
      if(hasTech($pt,8))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 10:
    $helper_msg='Jetzt kannst Du forschen. Gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>Forschungen</b> und starte: <b>'.helper_tech(80).'</b><br><br>Ist die Forschung fertig, baue unter <b>Geb&auml;ude</b>: <b>'.helper_tech(7).'</b><br><br>Die Kollektoren sind Deine wichtigste Energiequelle und wecken schnell die Gier der anderen Spieler. Wichtig ist eine ausgewogene Anzahl der Kollektoren zu Deinen Schiffen und Verteidigungsanlagen.';
    $helper_picid=11;

    if($helper_progress==10){
      //test ob die Kollektorenfabrik fertig ist
      if(hasTech($pt,7)){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 11:
    $helper_msg='Gut gemacht, jetzt fehlt nur noch ein Energie-Materie-Wandler. Baue als n&auml;chstes folgendes Geb&auml;ude: <b>'.helper_tech(14).'</b><br><br>Gehe jetzt '.$helper_weg['ressourcen'].' und baue 17 Kollektoren.<br><br>Damit kommst Du raus aus Sektor 1 und in einen Spielersektor. Bei dem Umzug kannst Du kurz ausgeloggt werden. Logge Dich nach dem Umzug einfach wieder ein.';
    $helper_picid=1;

    if($helper_progress==11)
    {
      //test ob der Materieumwandler M fertig ist und Kollektoren da sind
      if(hasTech($pt,14) AND $helper_col>0)
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 12:
    $helper_msg='Gut, jetzt bekommst Du bei jedem Wirtschaftstick mehr Multiplex.<br><br>Gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>Forschungen</b> und starte: <b>'.helper_tech(65).'</b><br>Diese Forschung ist eine Grundlage f&uuml;r den Handel und den Geheimdienst.';
    $helper_msg.='<br><br>Neben Multiplex ben&ouml;tigst Du noch Dyharra. Baue daf&uuml;r unter <b>Geb&auml;ude</b> zuerst <b>'.helper_tech(2).'</b> und dann <b>'.helper_tech(15).'</b>.<br><br>Mit dem Energieverteilungsschl&uuml;ssel auf der Seite <b>Ressourcen</b> legst Du fest, wie die Energie auf die Rohstoffe verteilt wird.';
    $helper_picid=2;

    if($helper_progress==12)
    {
      //test ob Transmitterfeld und Materieumwandler D fertig sind
      if(hasTech($pt,65) AND hasTech($pt,15))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 13:
    $helper_msg='Sehr gut, jetzt bekommst Du auch Dyharra.<br><br>Der n&auml;chste Schritt ist die Anbindung Deines Sonnensystems an das intergalaktische Handelssystem, um den planetaren Rohstoffertrag zu erh&ouml;hen. Baue dazu als Grundlage: <b>'.helper_tech(3).'</b>, <b>'.helper_tech(112).'</b> und <b>'.helper_tech(113).'</b>';
    $helper_msg.='<br><br>Damit kannst Du auch <b>'.helper_tech(16).'</b> und <b>'.helper_tech(17).'</b> f&uuml;r Iradium und Eternium bauen. Au&szlig;erdem kannst Du mit der B&ouml;rse Rohstoffe f&uuml;r die Sektorgeb&auml;ude Deines Sektors spenden. Baue Dich aber erstmal auf, bevor Du Rohstoffe spendest.';

    $helper_picid=3;

    if($helper_progress==13)
    {
      //test ob Planetare Börse und Konstruktionszentrum IV fertig sind (Voraussetzungen der Weltraumhandelsgilde)
      if(hasTech($pt,3) AND hasTech($pt,113))
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
    $helper_msg='Mit dem n&auml;chsten Geb&auml;ude bindest Du Dein Sonnensystem an das intergalaktische Handelssystem an. Dadurch steigt der planetare Rohstoffertrag stark an.<br><br>Au&szlig;erdem kannst Du damit an Auktionen teilnehmen, und es ist die Voraussetzung f&uuml;r Missionen und das Artefaktgeb&auml;ude.<br><br>Baue jetzt folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
    $helper_picid=4;
    
    if($helper_progress==14)
    {
      //test ob das geb�ude fertig ist (Weltraumhandelsgilde)
      if(hasTech($pt,4))
      {
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;
  case 15:
    if($_SESSION['ums_rasse']==1)$helper_hs='Raumwerft I und Raumwerft II';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Raumschmiede I und Raumschmiede II';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Schwarmstock I und Schwarmstock II';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Drohnenwabe I und Drohnenwabe II';
    $helper_msg='Bevor wir die Wirtschaft weiter st&auml;rken, sollten wir uns um den milit&auml;rischen Bereich k&uuml;mmern, denn die Kollektoren ben&ouml;tigen einen guten Schutz.<br><br>Baue daher folgende Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
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
    $helper_msg='Perfekt, Du kannst jetzt Deine ersten Raumschiffe bauen. Gehe dazu einfach '.$helper_weg['produktion'].' und baue 50 J&auml;ger.<br><br>Wenn die J&auml;ger im Bau sind, kannst Du auf der Seite <b>Ressourcen</b> noch weitere Kollektoren bauen, um auf 25 St&uuml;ck zu kommen.<br><br>Du kannst auch parallel weitere Forschungen durchf&uuml;hren, um bessere Schiffe zu erhalten. Welche Forschungen Du im Detail ben&ouml;tigst, siehst Du, wenn Du '.$helper_weg['technologien'].' gehst und dort <b>Forschungen</b> w&auml;hlst. Dort sind auch die Schiffe mit ihren Voraussetzungen aufgef&uuml;hrt.';
   
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
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Gro&szlig;e Raffinerie M,D';
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
    $helper_msg='Je mehr Kollektoren Du hast, desto eher wirst Du angegriffen. Um die Angreifer rechtzeitig zu sehen, ben&ouml;tigst Du passende Scanner. Baue daher folgende Geb&auml;ude: <b>'.$helper_hs.'</b><br><br>Mit jedem Geb&auml;ude steigt die Chance, Angreifer zu entdecken. Mit allen dreien wird jede angreifende Flotte angezeigt.<br><br>Angriffe auf Dich und Deine Sektorkollegen sowie deren Flottenbewegungen siehst Du im <b>Sektorstatus</b>. Dorthin kommst Du, wenn Du '.$helper_weg['sektorstatus'].' gehst.';
   
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
    $helper_msg='Das Geheimdienstgeb&auml;ude ist fertig. Gehe '.$helper_weg['geheimdienst'].' und baue 20 Sonden und 100 Agenten.<br><br>
    Mehr Informationen zu den Einheiten erh&auml;ltst Du, wenn Du '.($helper_mobil ? 'auf die Einheitenbilder tippst' : 'den Mauszeiger &uuml;ber die Einheitenbilder h&auml;ltst').'. Je mehr Agenten Du hast, desto schwerer haben es Gegner mit Eins&auml;tzen gegen Dich.<br><br>
    Versuche doch einmal eine Sonde zu schicken. Gehe '.$helper_weg['sektor'].', suche Dir einen Spieler aus und klicke unter "Aktion" das "S" an. Danach siehst Du dann den Sondenbericht.';
   
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
  
    $helper_msg='Zur&uuml;ck zu den K&auml;mpfen, wenn Du angegriffen wirst und Schiffe verlierst, entsteht viel Raumschrott. Mit den passenden Anlagen kann dieser recycelt werden. Baue daher folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
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
  
    $helper_msg='Des Weiteren gibt es viele unterschiedliche Artefakte, die Dir helfen k&ouml;nnen. Du kannst sie durch Missionen, beim t&auml;glichen Allianzbonus, bei K&auml;mpfen gegen die NPC-Gegner, in der Auktion und in den Vergessenen Systemen erhalten. Um sie lagern zu k&ouml;nnen, baue folgendes Geb&auml;ude: <b>'.$helper_hs.'</b>';
   
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
    $helper_msg='Zeit, Deine Raumschiffe einzusetzen. Gehe '.$helper_weg['flotten'].'. Neben der Heimatflotte hast Du die Flotten I bis III.<br><br>Unter <b>Flottenaufstellung</b> verteilst Du Deine Schiffe auf die Flotten und best&auml;tigst mit <b>Flotten umstellen</b>. Unter <b>Flottenbefehle erteilen</b> gibst Du einer Flotte ein Ziel (Sektor und System) und einen Befehl: <b>Angreifen</b> oder <b>Verteidige</b> f&uuml;r 1 bis 3 Kampfticks.<br><br>Alle Schiffe, die zu Hause sind, verteidigen Dein System automatisch. Kleine Schiffe wie J&auml;ger ben&ouml;tigen die Tr&auml;gerkapazit&auml;t gr&ouml;&szlig;erer Schiffe, sonst ist die ganze Flotte sehr langsam.';
    $helper_picid=2;

    if($helper_progress==23){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 24:
    if($_SESSION['ums_rasse']==1)$helper_hs='Transmitterschiffen';
    elseif($_SESSION['ums_rasse']==2)$helper_hs='Merlins';
    elseif($_SESSION['ums_rasse']==3)$helper_hs='Netzf&auml;ngern';
    elseif($_SESSION['ums_rasse']==4)$helper_hs='Sammlern';
    $helper_klaurate=round(($sv_kollie_klaurate ?? 0.15)*100);
    $helper_msg='Angreifen kannst Du Spieler au&szlig;erhalb Deines Sektors, deren Punkte nicht zu weit unter Deinen liegen. Auf der Seite Sektor zeigt ein gr&uuml;ner Punktewert, dass ein Ziel angreifbar ist.<br><br>Gewinnst Du einen Angriff, erbeutest Du '.$helper_klaurate.'% der Kollektoren des Ziels, aber nur mit <b>'.$helper_hs.'</b> in der Flotte: Jeder erbeutete Kollektor ben&ouml;tigt ein &uuml;berlebendes Schiff dieser Art. Umgekehrt kannst Du so auch Kollektoren verlieren.<br><br>Ein guter Anfang ist es, Deine Sektorkollegen zu verteidigen: Im Sektorstatus kommst Du &uuml;ber das <b>F</b> neben einem System direkt zum Flotteneinsatz.';
    $helper_picid=3;

    if($helper_progress==24){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 25:
    $helper_msg='Gemeinsam ist man st&auml;rker. Gehe '.$helper_weg['allianz'].'. &Uuml;ber <b>Beitreten</b> kommst Du zur Rangliste der Allianzen. Auf der Seite einer Allianz bewirbst Du Dich &uuml;ber <b>Bei dieser Allianz bewerben...</b> Im Server-Chat findest Du oft Allianzen, die Mitglieder suchen. &Uuml;ber <b>Gr&uuml;nden</b> kannst Du auch selbst eine Allianz ins Leben rufen.<br><br>Als Mitglied kannst Du jeden Tag den <b>t&auml;glichen Allianzbonus</b> abholen. Das Symbol daf&uuml;r erscheint '.($helper_ui=='standard' ? 'in der oberen Leiste' : 'in der Rohstoffleiste').'. Der Bonus enth&auml;lt unter anderem Tronic und Artefakte. Au&szlig;erdem gibt es einen eigenen Allianz-Chat und gemeinsame Allianzprojekte.';
    $helper_picid=4;

    if($helper_progress==25){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 26:
    $helper_msg='Mit Deinem Handelsgeb&auml;ude kannst Du an Auktionen teilnehmen. Gehe '.$helper_weg['auktion'].'. Dort werden Artefakte, Tronic, Palenium und Titanen-Energiekerne angeboten. Neue Auktionen entstehen, wenn Spieler den t&auml;glichen Allianzbonus abholen.<br><br>Es wird nicht gegeneinander geboten: Wer zuerst auf <b>bieten</b> klickt und den Preis bezahlen kann, erh&auml;lt den Artikel sofort. Der Preis sinkt &uuml;ber 1.000 Wirtschaftsticks immer weiter, Warten kann sich also lohnen, solange niemand schneller ist. Auf selbst gestartete Auktionen erh&auml;ltst Du 25% Nachlass, und jeder Kauf bringt Handelspunkte.';
    $helper_picid=5;

    if($helper_progress==26){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 27:
    $helper_msg='Jede Flotte wird von einem Basisschiff angef&uuml;hrt, in das Du Artefakte einsetzen kannst. Daf&uuml;r ben&ouml;tigst Du Artefaktpl&auml;tze: Gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>Basisschiffe</b> und erforsche <b>BS-Artefaktplatz I</b>.<br><br>Danach gehst Du '.$helper_weg['artefakte'].', w&auml;hlst im Artefaktgeb&auml;ude ein passendes Artefakt aus (Feuroka, Bloroka, Empdestro oder Recadesto) und dann die Flotte. Artefakte im Basisschiff gehen im Kampf nicht verloren. Einsetzen und Austauschen kannst Du sie nur, wenn die Flotte zu Hause ist.';
    $helper_picid=6;

    if($helper_progress==27){
      //test ob der erste Artefaktplatz erforscht ist
      if(hasTech($pt,133)){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;

  case 28:
    $helper_msg='Gehe '.$helper_weg['spezialisierung'].'. Dort kannst Du Dein Volk in 5 Bereichen spezialisieren, zum Beispiel f&uuml;r k&uuml;rzere Bauzeiten, mehr Erfahrung f&uuml;r Deine Flotten oder k&uuml;rzere Missionen.<br><br>Die Bereiche werden mit Errungenschaftspunkten freigeschaltet. Diese erh&auml;ltst Du f&uuml;r die Aufgaben, die unten auf der &Uuml;bersicht unter <b>Errungenschaften</b> stehen. In jedem freigeschalteten Bereich w&auml;hlst Du eine von drei M&ouml;glichkeiten: Ein Klick auf ein Symbol zeigt die Beschreibung, gew&auml;hlt wird mit dem Button darunter. Zur&uuml;cksetzen kannst Du die Auswahl kostenlos alle 480 Wirtschaftsticks.';
    $helper_picid=7;

    if($helper_progress==28){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 29:
    $helper_msg='Abseits der Sektoren liegen die <b>Vergessenen Systeme</b> mit neuen Rohstoffen, Fabriken und Fundst&uuml;cken. Um eine Verbindung dorthin aufbauen zu k&ouml;nnen, gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>V-Systeme</b> und erforsche den <b>Hyperraumpfad-Stabilisator</b>.<br><br>F&uuml;r jede Erkundung eines Systems ben&ouml;tigst Du 10 Sonden. Baue also schon einmal Sonden im Geheimdienst.';
    $helper_picid=8;

    if($helper_progress==29){
      //test ob der Hyperraumpfad-Stabilisator erforscht ist
      if(hasTech($pt,25)){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;

  case 30:
    $helper_msg='Gehe '.$helper_weg['vsysteme'].' und beginne mit dem System <b>DER EINGANG</b>: W&auml;hle <b>zum System</b> und dann <b>System erkunden</b>. Die Erkundung ist im n&auml;chsten Wirtschaftstick abgeschlossen. Danach kannst Du dort kostenlos einen Au&szlig;enposten, Eisen-Minen und Omega-Fabriken aktivieren und anschlie&szlig;end die Nachbarsysteme erkunden.<br><br>Achtung: W&auml;hrend einer Erkundung liefern Deine Kollektoren in diesem Wirtschaftstick keine Energie. Die <b>Automatische Erkundung</b> erkundet in jedem Wirtschaftstick ein System, Deine Kollektoren liefern dann aber die ganze Zeit keine Energie.';
    $helper_picid=9;

    if($helper_progress==30){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 31:
    if($_SESSION['ums_rasse']==1){$helper_hs='Missionszentrale';$helper_hs2='Konstruktionszentrum VII';}
    elseif($_SESSION['ums_rasse']==2){$helper_hs='Missionshort';$helper_hs2='Werkstatt VII';}
    elseif($_SESSION['ums_rasse']==3){$helper_hs='Missionsbau';$helper_hs2='Zentralbau VII';}
    elseif($_SESSION['ums_rasse']==4){$helper_hs='Missionsstock';$helper_hs2='Stock VII';}
    $helper_msg='Mit Missionen kannst Du Agenten und Frachter gewinnbringend einsetzen. Daf&uuml;r ben&ouml;tigst Du folgendes Geb&auml;ude: <b>'.$helper_hs.'</b> (Voraussetzung: '.$helper_hs2.').<br><br>Gehe dann '.$helper_weg['missionen'].'. <b>Agenteneins&auml;tze</b> ben&ouml;tigen nur Agenten und bringen Artefakte, Tronic oder Titanen-Energiekerne. Beim <b>Rohstoff-Handel</b> und <b>Waren-Handel</b> schickst Du eine Flotte mit Frachtern los. Missionen laufen in Echtzeit und k&ouml;nnen nicht abgebrochen werden, eine Flotte auf Mission verteidigt Dein System nicht. Ist die Zeit abgelaufen, holst Du die Belohnung mit <b>Abholen</b> ab.';
    $helper_picid=10;

    if($helper_progress==31){
      //test ob das geb�ude fertig ist
      if(hasTech($pt,29)){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;

  case 32:
    $helper_msg='F&uuml;r die Battlegrounds ben&ouml;tigst Du einen <b>Basisstern</b>. Gehe '.$helper_weg['technologien'].', w&auml;hle dort <b>V-Systeme</b> und erforsche ihn. Neben Rohstoffen kostet er 100 Palenium, 10 Titanen-Energiekerne und 1.000 Eisen aus den Vergessenen Systemen.<br><br>Deinen Basisstern findest Du danach auf der Produktionsseite oben &uuml;ber das Symbol <b>Basisstern</b>. Seine Stufe erh&ouml;hst Du mit Palenium. Palenium erh&auml;ltst Du zum Beispiel, wenn Du auf der Seite Artefakte ein Artefakt zerst&ouml;rst, oder als Fundst&uuml;ck in den Vergessenen Systemen.';
    $helper_picid=11;

    if($helper_progress==32){
      //test ob der Basisstern erforscht ist
      if(hasTech($pt,159)){
        $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
        $helper_progress++;
      }
    }
  break;

  case 33:
    $helper_msg='In den Vergessenen Systemen liegen drei Battlegrounds, in denen die Basissterne gegeneinander k&auml;mpfen: <b>ALPHA</b> alle 8 Kampfticks (Gewinn: Tronic), <b>BETA</b> alle 24 Kampfticks (Gewinn: ein Kriegsartefakt) und <b>GAMMA</b> alle 24 Kampfticks f&uuml;r Allianzen (Gewinn: Quantenglimmer f&uuml;r die Allianz).<br><br>Teilnehmen kannst Du, sobald Du im jeweiligen Battleground-System einen <b>Weltraumhafen</b> errichtet hast. Daf&uuml;r erkundest Du Dich bis dorthin vor, erforschst den Weltraumhafen und schickst eine Flotte mit gen&uuml;gend Frachtkapazit&auml;t. Danach k&auml;mpft Dein Basisstern automatisch, die h&ouml;here Stufe entscheidet. Die Ergebnisse findest Du in den Nachrichten unter [BG].';
    $helper_picid=2;

    if($helper_progress==33){
      $sql = "UPDATE de_user_data SET helperprogress=helperprogress+1 WHERE user_id=?";
      mysqli_execute_query($GLOBALS['dbi'], $sql, [$_SESSION['ums_user_id']]);
      $helper_progress++;
    }
  break;

  case 34:
    //letzter Schritt, danach gibt es keinen Fortschritt mehr
    $helper_msg='Das waren alle Hinweise die ich f&uuml;r Dich habe, Du kannst das Spiel jetzt auf eigene Faust weiter erforschen.<br><br>Wenn Du meine Dienste nicht mehr ben&ouml;tigst, kannst Du mich '.$helper_weg['optionen'].' bei "Berater aktivieren" entlassen.<br><br><br><br>Bitte macht Vorschl&auml;ge um den Berater zu verbessern.';
    $helper_picid=1;
  break;
  
  default:
    //wenn nichts pa�t, dann den helper gar nicht anzeigen
    $helper_dontshow=1;
  break;
}

if($helper_dontshow==0)
{
  rahmen_oben('Fluxurion der Berater');

  //min-height statt fester Höhe, damit lange Texte nicht unter die Buttons laufen; unten Platz für die Buttons
  echo '<div class="cell" style="width: 570px; min-height: 256px; padding-bottom: 46px; box-sizing: border-box; font-size: 14px; position: relative;">';
  echo '<div style="float: left;"><img src="gp/g/berater'.$helper_picid.'.png" border="0"></div>';
  echo $helper_msg;

  //zurück/weiter als Buttons (gut zu treffen, auch am Handy); deaktiviert, wenn es in die Richtung nicht weitergeht
  echo '<div class="helper-nav" style="position: absolute; bottom: 6px; right: 0px; width: 442px; text-align: center;">';
  //SCRIPT_NAME statt PHP_SELF, das über PATH_INFO fremdes HTML enthalten kann
  $helper_self = htmlspecialchars(basename($_SERVER['SCRIPT_NAME']), ENT_QUOTES, 'UTF-8');
  if($_SESSION['helperid']>0){
    echo '<a href="'.$helper_self.'?helperdo=-1" class="helper-btn">&lsaquo; zur&uuml;ck</a>';
  }else{
    echo '<span class="helper-btn helper-btn-aus">&lsaquo; zur&uuml;ck</span>';
  }
  echo '<span class="helper-zaehler">'.($_SESSION['helperid']+1).'/'.($helper_progress+1).'</span>';
  if($_SESSION['helperid']<$helper_progress){
    echo '<a href="'.$helper_self.'?helperdo=1" class="helper-btn">weiter &rsaquo;</a>';
  }else{
    //Schritt 34 ist der letzte Hinweis, davor fehlt noch die erledigte Aufgabe
    $helper_titel=($_SESSION['helperid']>=34) ? 'Das war der letzte Hinweis.' : 'Erledige zuerst die Aufgabe, dann geht es weiter.';
    echo '<span class="helper-btn helper-btn-aus" title="'.$helper_titel.'">weiter &rsaquo;</span>';
  }
  echo '</div>';
  
  echo '</div>';
  rahmen_unten();
}

//}die('</body></html>');//debug
?>