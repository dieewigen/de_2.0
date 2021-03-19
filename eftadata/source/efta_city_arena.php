<?php
//arena betreten
if (isset($_REQUEST["enterarena"]))
{
  $_REQUEST["showarena"]=1;
  //genug bewegungspunkte?
  if($bewpunkte>=100)
  {
    //genug hp?
    if($hp>=$hpmax*0.8)
    {
      //schauen ob man in der stadt ist
      if($inbldg==1)
      //for($ik=0;$ik<count($city_koord);$ik++)
      {
        //if ($x==$city_koord[$ik][0] AND $y==$city_koord[$ik][1] AND $_REQUEST["enterarena"]=$city_koord[$ik][2])
        $inbldg=1;//(int)$_REQUEST["enterarena"];
        $arena=1;//(int)$_REQUEST["enterarena"];
        //db updaten
        mysql_query("UPDATE de_cyborg_data SET arena='$inbldg', bewpunkte=bewpunkte-100 WHERE user_id='$efta_user_id';",$eftadb);
      }
    }else $arenamsg='<font color="#FF0000"><b>F&uuml;r einen Arenakampf ben&ouml;tigst du mindestens 80% deiner Lebensenergie.</b></font>';
  }else $arenamsg='<font color="#FF0000"><b>Du hast nicht genug Bewegungspunkte.</b></font>';
}

//arena von innen
if($arena>0)
{
  
  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Die glorreiche Stadt Waldmond&nbsp;</b></td></tr>';
  echo '</table><br>';*/
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Die glorreiche Stadt Waldmond</b></div>');

  echo '<table width="100%">
  <tr>
  <td class="ueber2" align="center"><b>Die Arena von Waldmond</td>
  </tr>';

  echo '
  <tr>
  <td class="cell1" align="center"><b>Du wartest auf den Kampf.<br>K&auml;mpfe finden alle 5 Minuten statt. Aktuelle Zeit: '.date("H:i:s", time()).'
  </td>
  </tr>';

  echo '
  <tr>
  <td class="cell" align="center">Ruhm: '.$player_fame.' - Gewonnen: '.$arenawon.' - Verloren: '.$arenalost.'</td>
  </tr>';

  echo '
  <tr>
  <td>&nbsp;</td>
  </tr>';

  //teilnehmerliste
  echo '
  <tr>
  <td class="cell1" align="center"><b>Teilnehmer</b> <a href="#" onClick="lnk(\'\');">(aktualisieren)</a></td>
  </tr>';
  $result = mysql_query("SELECT user_id, level FROM de_cyborg_data WHERE arena=1 ORDER BY exp DESC", $eftadb);
  while($row = mysql_fetch_array($result))
  {
    $uid=$row["user_id"];
    $plevel=$row["level"];
    //spielernamen laden

    $db_data = mysql_query("SELECT spielername FROM de_cyborg_data WHERE user_id='$uid'", $eftadb);
    $rowx = mysql_fetch_array($db_data);
    $spielername=$rowx["spielername"];

    if ($c1==0 OR $c1==1)
    {
      $bg='cell';
    }
    else
    {
      $bg='cell1';
    }
    $c1++;
    if($c1>3)$c1=0;

    echo '
    <tr>
    <td class="'.$bg.'" align="center">'.$spielername.'<i> (Level: '.$plevel.')</i></td>
    </tr>';
  }

  echo '</table>';
  
  rahmen1_unten();
  rahmen0_unten();
  
  //infoleiste anzeigen
  show_infobar();
  
  die('</body></html>');
}

//arena von auﬂen
if($_REQUEST["showarena"]==1)
{
  /*echo '<div id="ct_city">';
  echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
  echo '<tr><td align="center"><b class="ueber">&nbsp;Die glorreiche Stadt Waldmond&nbsp;</b></td></tr>';
  echo '</table><br>';*/
  echo '<br><br>';
  rahmen0_oben();
  rahmen1_oben('<div align="center"><b>Die glorreiche Stadt Waldmond</b></div>');

  echo '<table width="100%">
  <tr>
  <td class="ueber2" align="center"><b>Die Arena von Waldmond</td>
  </tr>';

  echo '
  <tr>
  <td class="cell1"><b>Dies ist die glorreiche Arena von Waldmond. Hier messen sich die Streiter des Landes und k&auml;mfen um Ruhm und Ehre.<br>
  Tretet auch Ihr ein und versucht euer Gl&uuml;ck. Es wird auch nicht zu Eurem Nachteil sein, denn der Kampf geht nicht bis auf Leben und Tod.<br>
  Es k&auml;mpfen immer 2 Gegner und der Gewinner des Kampfes erh&auml;lt den ausgelobten Gewinn und steigt im Ansehen des Reiches, wobei auch der Verlierer
  Anerkennung f&uuml;r seinen Kampf ernten wird.<br>
  Alle 5 Minuten findet eine Veranstaltung statt. Aktuelle Zeit: '.date("H:i:s", time()).'
  </td>
  </tr>';

  echo '
  <tr>
  <td class="cell" align="center">Ruhm: '.$player_fame.' - Gewonnen: '.$arenawon.' - Verloren: '.$arenalost.'</td>
  </tr>';

  if ($arenamsg!='')
  echo '
  <tr>
  <td class="text6" align="center">'.$arenamsg.'</td>
  </tr>';


  echo'
  <tr>
  <td align="center"><br><a class="gwaren" href="#" onClick="lnk(\'enterarena=1\')">&nbsp;Am Kampf teilnehmen (100 AP)&nbsp;</a>
    &nbsp;<a class="gwaren" href="#" onClick="lnk(\'\')">&nbsp;Die Arena verlassen&nbsp;</a></td>
  </tr>';

  echo '</table>';
  
  rahmen1_unten();
  rahmen0_unten();

  //infoleiste anzeigen
  show_infobar();
  
  die('</body></html>');
}

?>
