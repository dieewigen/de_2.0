<?php

echo '<div align="center"><br />';

rahmen0_oben();
echo '<br />';

$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
<td><b>Politik</b></td>
<td width="120">&nbsp;</td>
</tr></table>';
rahmen1_oben($routput);

// Wahlmöglichkeit:
if($_SESSION[sou_fraction] > 0) {
  // was hat der Spieler gewählt:
  $gewaehltwurde = @mysql_result(mysql_query("SELECT wahlstimme FROM sou_user_politics WHERE user_id = $_SESSION[sou_user_id]", $soudb),0);

  // Hat der Spieler gewählt ?
  if($_POST[ibelievein] and is_numeric($_POST[ibelievein])) {
    echo '<br /><b><u>Hinweis:</u> Ihre Stimmabgabe war erfolgreich!</b><br /><br />';
    $datum = date("Y-m-d H:i:s");
    if($gewaehltwurde) {
      mysql_query("
        UPDATE sou_user_politics 
        SET wahlstimme = $_POST[ibelievein] , 
        date = '$datum'  
        WHERE user_id = $_SESSION[sou_user_id]
      ");
      $gewaehltwurde = $_POST[ibelievein];
    } else {
      mysql_query("
        INSERT INTO sou_user_politics 
        SET wahlstimme = $_POST[ibelievein] ,  
        user_id = $_SESSION[sou_user_id] , 
        date = '$datum' , 
        fraction = '$_SESSION[sou_fraction]'
      ");
      $gewaehltwurde = $_POST[ibelievein];
    }
  }
  
  echo '
    <span id="politics"><form action="sou_main.php?action=politics" method="post">
    <br />
    <u style="color: #ffcc33;">Wahl des Fraktions-Vorsitzenden</u><br /><br />
    Ihre aktuelle Stimme gilt f&uuml;r:&nbsp; 
      <select name="ibelievein">
        <option value=""> <-- noch nicht gew&auml;hlt --> </option>
  ';
  $query = "
    SELECT user_id, spielername, fraction  
    FROM sou_user_data 
    WHERE fraction = '$_SESSION[sou_fraction]' 
    ORDER BY spielername 
  ";
  $result = mysql_query($query);
  while ($data = mysql_fetch_array($result)) {
    echo '
      <option value="'.$data[user_id].'" 
    ';
    if($gewaehltwurde == $data[user_id]) { echo 'selected '; }  
    echo '
      > '.$data[spielername].' </option>
    ';
  }
  
  echo '
      </select>
    <br /><br />
    <input type="submit" value="Stimme abgeben" /><br /><br />
    </span></form>
  ';
} else {
  echo '
    <br /><b>Sie geh&ouml;hren noch keiner Fraktion an, daher besteht keine Teilnahmeberechtigung an den Wahlen!</b><br /><br />
  ';
}

rahmen1_unten();

// Nun teilen wir die Ansicht in 2 nebeneinanderliegenden Tabellen:
if($_SESSION[sou_fraction] > 0) {

  echo '<br /><div align="center">';
  rahmen1_oben('<div align="center"><b>Wahlergebnis</b></div>');
  echo '<br /><div align="center">';

  //alle bürger der fraktion
  $buerger = @mysql_result(mysql_query("SELECT count(user_id) FROM sou_user_data WHERE fraction = '$_SESSION[sou_fraction]'", $soudb),0);
  
  // Abfrage der Wahlbeteiligung
  //stimmberechtigt sind alle spieler die in den letzten 3 wochen aktiv waren
  $time=time()-21*24*3600;
  $stimmberechtigt = @mysql_result(mysql_query("SELECT count(user_id) FROM sou_user_data WHERE lastclick >='$time' AND fraction = '$_SESSION[sou_fraction]'", $soudb),0);
  
  //notwendige stimmen die man braucht um vorsitzender zu werden: x% aller stimmberechtigten stimmen
  $notwendige_stimmen=ceil($stimmberechtigt/100*25);
  if($notwendige_stimmen<10)$notwendige_stimmen=10;
  
  /*
  $loginseit = mktime(0, 0, 0, date(m), date(d) - 7, date(Y));
  $notwendige_stimmen = @mysql_result(mysql_query("SELECT count(lastclick) FROM sou_user_data WHERE lastclick >= '$loginseit'  and fraction = '$_SESSION[sou_fraction]'" , $db),0);
  if(!$notwendige_stimmen) { $notwendige_stimmenmein = "0"; }
  
  */  
  $abgegebene_stimmen = @mysql_result(mysql_query("SELECT count(sou_user_data.user_id) FROM
  sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id) WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$player_fraction' AND sou_user_politics.wahlstimme > 0", $soudb),0);
  
  if(!$abgegebene_stimmen) { $abgegebene_stimmen = "0"; }

  // Anzeige:
  $style = 'style="border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF;"';
  $style_2 = 'style="border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-right: 1px solid #FFFFFF;"';
  $style_3 = 'style="border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-bottom: 1px solid #FFFFFF;"';
  $style_4 = 'style="border: 1px solid #FFFFFF;"';
  echo '
    <table cellspacing="0" cellpadding="5" style="font-family: Verdana; font-size: 12px; ">
      <tr>
        <td '.$style.'>B&uuml;rger</td>
        <td '.$style_2.' align="right">'.$buerger.'</td>
      </tr>    
      <tr>
        <td '.$style.'>Wahlberechtigt</td>
        <td '.$style_2.' align="right">'.$stimmberechtigt.'</td>
      </tr>
      <tr>
        <td '.$style.'>Notwendige Stimmen</td>
        <td '.$style_2.' align="right">'.$notwendige_stimmen.'</td>
      </tr>
      <tr>
        <td '.$style.'>Abgegebene Stimmen</td>
        <td '.$style_2.' align="right">'.$abgegebene_stimmen.'</td>
      </tr>
	  <tr>
        <td '.$style_3.'>Wahlbeteiligung</td>
        <td '.$style_4.' align="right">'.number_format($abgegebene_stimmen*100/$stimmberechtigt, 2,",",".").'%</td>
      </tr>      
    </table>
  ';
  
  //$notwendige_stimmen=0;
  
  if($abgegebene_stimmen < $notwendige_stimmen) {
    echo '
      <br /><br /><b>Ein g&uuml;ltiges Wahlergebnis erfordert eine Wahlbeteiligung vom mindestens '.$notwendige_stimmen.' Stimmen!</b><br /><br />
    ';
  } 
  else 
  {
  	
    //$abgegebene_stimmen = @mysql_result(mysql_query("SELECT count(sou_user_data.user_id) FROM sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id) WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$player_fraction' AND sou_user_politics.wahlstimme > 0", $db),0);
  	
    $query_welche_userid = @mysql_query("
      SELECT wahlstimme , count(wahlstimme) as anzahl 
      FROM sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id)
      WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$_SESSION[sou_fraction]' 
      GROUP BY wahlstimme 
      ORDER BY anzahl DESC, wahlstimme ASC LIMIT 1
    ", $soudb);
    $data_gewinner = @mysql_fetch_array($query_welche_userid);
    $welche_userid = $data_gewinner[wahlstimme];
    $soviel_stimmen = $data_gewinner[anzahl];
    $fraktionsvorsitzender = mysql_result(mysql_query("SELECT spielername FROM sou_user_data WHERE user_id = $welche_userid", $soudb),0);
    echo '
      <br /><br /><br />
      <u>Fraktions-Vorsitzender</u>: '.$fraktionsvorsitzender.' mit '.$soviel_stimmen.' Stimmen ('.number_format($soviel_stimmen*100/$abgegebene_stimmen, 2,",",".").'%)<br /><br /><br />
    ';
    
    //wenn der vorsitzende auf die seite geht, dann ein menüpunkt für ihn anzeigen
    /*
    if($player_user_id==$welche_userid)
    {
    	echo '<a href="sou_main.php?action=politicscolonypage"><div class="b1">Geb&auml;udestufen</div></a><br>';
    }
    */
    
  }
  echo '</div>';
  rahmen1_unten();
  echo '</div>';

  // Anzeige der Top10:
  echo '<br /><div align="center">';
  rahmen1_oben('<div align="center"><b>Top 10</b></div>');
  echo '<div align="center">';
  
  echo '
    <br />
    <table border="0" cellspacing="0" cellpadding="5" style="font-family: Verdana; font-size: 12px; ">
      <tr>
        <td '.$style.'><font style="color: #ffcc33;">Spieler</font></td>
        <td '.$style.'><font style="color: #ffcc33;">Stimmen</font></td>
        <td '.$style_2.'><font style="color: #ffcc33;">Anteil</font></td>
      </tr>
  ';
  
  //die top 10 der gewählten ausgeben
  $query_wahlergebnis = @mysql_query("
      SELECT wahlstimme , count(wahlstimme) as anzahl 
      FROM sou_user_data LEFT JOIN sou_user_politics ON(sou_user_data.user_id = sou_user_politics.user_id)
      WHERE sou_user_data.lastclick >='$time' AND sou_user_data.fraction = '$_SESSION[sou_fraction]' 
      GROUP BY wahlstimme 
      ORDER BY anzahl DESC, wahlstimme ASC, sou_user_data.user_id ASC LIMIT 10
    ", $soudb);  
  /*
  $query_wahlergebnis = mysql_query("
    SELECT count(A.fraction) as stimmen, A.wahlstimme, B.spielername
    FROM sou_user_politics AS A
    LEFT JOIN sou_user_data as B on A.wahlstimme = B.user_id 
    WHERE B.lastclick >='$time' AND A.fraction = '$player_fraction' 
    GROUP BY A.wahlstimme 
    ORDER BY stimmen DESC, A.wahlstimme ASC 
    LIMIT 10
  ", $soudb);*/
  while ($row = mysql_fetch_array($query_wahlergebnis)) 
  {
  	//spielername auslesen
  	$uid=$row["wahlstimme"];
  	$query_spielername = @mysql_query("SELECT spielername, fraction FROM sou_user_data WHERE user_id='$uid';", $soudb);
    $rowx = mysql_fetch_array($query_spielername);
 	
    $platz++;
    if($rowx["spielername"]!='' AND $rowx['fraction']==$player_fraction)
    echo '
      <tr>
        <td '.$style.' align="left">'.$rowx["spielername"].'</td>
        <td '.$style.' align="right">'.$row[anzahl].'</td>
        <td '.$style_2.' align="right">&nbsp;'.number_format($row["anzahl"]*100/$abgegebene_stimmen, 2,",",".").'%</td>
      </tr>
    ';
  }
  echo '
      <tr>
        <td colspan="3" style="border-top: 1px solid #FFFFFF;">&nbsp;</td>
      </tr>
    </table>
  ';
  
  echo '</div>';
  rahmen1_unten();
  echo '</div>';   

}

///////////////////////////////////////////////////
///////////////////////////////////////////////////
// eine liste der fraktionsvorsitzenden
///////////////////////////////////////////////////
///////////////////////////////////////////////////

echo '<br>';

rahmen1_oben('<div align="center"><b>Liste der Fraktionsvorsitzenden</b></div>');

for($i=1;$i<=6;$i++)
{
  //leaderid auslesen
  $flid=get_fracleader_id($i);
  $flname='-';
  
  //ggf. spielernamen auslesen
  if($flid>0)
  {
    $flname=get_playername($flid);
  }
  
  echo 'F'.$i.': '.$flname.'<br>';

}


rahmen1_unten();



// Abschluss:

echo '<br />';

rahmen0_unten();

echo '<br />';

echo '</div>';//center-div
/*
echo '
  <p style="font-family: Verdana; font-size: 10px;">
  ea-Politik Version 8.04.23a &copy; Die Ewigen - Bearbeiter:  SPY  
  </p>
';
*/

die('</body></html>');
?>
