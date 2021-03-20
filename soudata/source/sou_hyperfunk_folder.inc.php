<?php
//fix um das script von der botabfrage unabh‰ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;

include "inc/header.inc.php";

include "soudata/lib/sou_dbconnect.php";

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo '<br />';

// ‹bersicht Eingang, Archiv, Ausgang:
$mouseeffekt_a = 'onMouseover="this.style.border=\'1px solid #FFFFFF;\'" onMouseout="this.style.border=\'1px solid #0A1614;\'"';

$style1 = 'style="color: #ffcc00; border-bottom: 1px solid #DDDDDD; width: 175px; cursor: pointer;"';
$style2 = 'style="color: #ffcc00; border-bottom: 1px solid #DDDDDD; width: 75px; cursor: pointer;"';
$style3 = 'style="color: #ffcc00; border-bottom: 1px solid #DDDDDD; cursor: pointer;"';
$style4 = 'style="border-bottom: 1px solid #DDDDDD; width: 175px; cursor: pointer;';
$style5 = 'style="border-bottom: 1px solid #DDDDDD; width: 75px; cursor: pointer;';
$style6 = 'style="border-bottom: 1px solid #DDDDDD; cursor: pointer;';

// Welcher Ordern ?
if($_GET[id] == 1) { 
  echo '<b onClick="gotofolder(1);" style="color: #ffcc00; text-decoration: underline; cursor: pointer;">Eingang</b>&nbsp;|&nbsp;'; 
  $hfstatus = "_000_"; $hfdatafield = "empfaenger"; $adressat = "Absender";
} else {
  echo '<font onClick="gotofolder(1);" style="cursor: pointer;">Eingang</font>&nbsp;|&nbsp;';
}
if($_GET[id] == 2) { 
  echo '<b onClick="gotofolder(2);" style="color: #ffcc00; text-decoration: underline; cursor: pointer;">Ausgang</b>&nbsp;|&nbsp;';
  $hfstatus = "010__"; $hfdatafield = "absender"; $adressat = "Empf&auml;nger";
} else {
  echo '<font onClick="gotofolder(2);" style="cursor: pointer;">Ausgang</font>&nbsp;|&nbsp;';
}
if($_GET[id] == 3) { 
  echo '<b onClick="gotofolder(3);" style="color: #ffcc00; text-decoration: underline; cursor: pointer;">Archiv</b>&nbsp;|&nbsp;';
  $hfstatus = "001__"; $hfdatafield = "empfaenger";  $adressat = "Absender";
} else {
  echo '<font onClick="gotofolder(3);" style="cursor: pointer;">Archiv</font>&nbsp;|&nbsp;';
}
if($_GET[id] == 4) { 
  echo '<b onClick="gotofolder(4);" style="color: #ffcc00; text-decoration: underline; cursor: pointer;">Fraktion</b>&nbsp;|&nbsp;';
  $hfstatus = "__01_"; $hfdatafield = "empfaenger";  $adressat = "Absender";
} else {
  echo '<font onClick="gotofolder(4);" style="cursor: pointer;">Fraktion</font>&nbsp;|&nbsp;';
}
echo '
  <font onclick="newhf();" style="color: #cc6600; cursor: pointer;">Neue HF</font><br /><br />
';

echo '
  <table cellspacing="0" cellpadding="0" style="">
    <tr>
      <td '.$style1.'><b>'.$adressat.'</b></td>
      <td '.$style2.'><b>Datum</b></td>
      <td '.$style3.'><b>Uhrzeit</b></td>
    </tr><tr>
      <td style="height: 2px; font-size: 6px;">&nbsp;</td>
    </tr>
';

// Anzeigen des Posteingangs:
if($_GET[id]=="" or $_GET[id]< 5) {
  $query = "
    SELECT *, DATE_FORMAT(date, '%d.%m.%Y') as date, DATE_FORMAT(date, '%H:%i') as time
    FROM sou_user_hyper 
    WHERE $hfdatafield = '$_SESSION[sou_user_id]' 
    AND status like '$hfstatus' 
    ORDER BY id DESC
  "; 
  // RIGHT(date,10) DESC, time DESC
  
  $hf_inc_daten = mysql_query($query, $soudb);
  while ($row = mysql_fetch_array($hf_inc_daten)) {
    // Betreff evtl. k¸rzen:
    if(strlen($row[betreff])>30) {
      $row[betreff] = substr($row[betreff],0,30) . '...';
    }
    // Ungelesen ?
    if(substr($row[status],0,1)=="1") {
      $style7 = $style4 . ' font-weight: bold;"'; $style8 = $style5 . ' font-weight: bold;"'; $style9 = $style6 . ' font-weight: bold;"';
    } else {
      $style7 = $style4 . '"'; $style8 = $style5 . '"'; $style9 = $style6 . '"';
    }
    // OnClick definieren:
    $onclick = 'onClick="showmessage('.$row[id].')"';
    $mouseeffekt = 'onMouseover="overmessage('.$row[id].')" onMouseout="outmessage('.$row[id].')"';
    if($_GET[id]==2) {
      $empfaenger = @mysql_result(mysql_query("SELECT spielername FROM sou_user_data WHERE user_id = $row[empfaenger]", $soudb),0);
      if($empfaenger == "") { $empfaenger = "gel&ouml;schter Spieler"; }
      $zu_oder_von = $empfaenger;
    } else {
      $zu_oder_von = $row[fromnic];
    }
    echo '
      <tr>
        <td id="msga'.$row[id].'" '.$style7.' title="Betr.: '.$row[betreff].'" '.$mouseeffekt.' '.$onclick.'>'.$zu_oder_von.'</td>
        <td id="msgb'.$row[id].'" '.$style8.' title="Betr.: '.$row[betreff].'" '.$mouseeffekt.' '.$onclick.'>'.$row[date].'</td>
        <td id="msgc'.$row[id].'" '.$style9.' title="Betr.: '.$row[betreff].'" '.$mouseeffekt.' '.$onclick.'>'.$row[time].'</td>
      </tr>
    ';
  }
}

// linke Tabelle schlieﬂen:
echo '
  </table><br />
';



?>
