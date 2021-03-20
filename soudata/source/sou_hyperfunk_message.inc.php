<?php
//fix um das script von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;

include "inc/header.inc.php";

include "soudata/lib/sou_dbconnect.php";

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//Funktionen:
function umlaut($fieldname)
{
  global $soudb;
    $fieldname = str_replace ("�", "&auml;", $fieldname);
    $fieldname = str_replace ("�", "&Auml;", $fieldname);
    $fieldname = str_replace ("�", "&ouml;", $fieldname);
    $fieldname = str_replace ("�", "&Ouml;", $fieldname);
    $fieldname = str_replace ("�", "&uuml;", $fieldname);
    $fieldname = str_replace ("�", "&Uuml;", $fieldname);
    $fieldname = str_replace ("�", "&szlig;", $fieldname);
    $fieldname = str_replace ("ä", "&auml;", $fieldname);
    $fieldname = str_replace ("Ä", "&Auml;", $fieldname);
    $fieldname = str_replace ("ö", "&ouml;", $fieldname);
    $fieldname = str_replace ("Ö", "&Ouml;", $fieldname);
    $fieldname = str_replace ("ü", "&uuml;", $fieldname);
    $fieldname = str_replace ("Ü", "&Uuml;", $fieldname);
    $fieldname = str_replace ("ß", "&szlig;", $fieldname);
    $fieldname = str_replace ("³", "&sup3;", $fieldname);
    $fieldname = str_replace ("²", "&sup2;", $fieldname);
    return $fieldname;
}
// ARCHIVIEREN einer HF:
if(is_numeric($_GET[hfarchiv])) {
  $hf_id = $_GET[hfarchiv];
  
  $query = "
    SELECT * FROM sou_user_hyper WHERE id = $hf_id
  ";
  $result = mysql_query($query, $soudb);
  $data_hf = mysql_fetch_array($result);
  
  if($_SESSION[sou_user_id] == $data_hf[empfaenger]) {
    $neuerstatus = "001" . substr($data_hf[status],3,2); 
    mysql_query("UPDATE sou_user_hyper SET status = '$neuerstatus' WHERE id = $hf_id", $soudb);
    echo 'Die Hyperfunknachricht wurde ins Archiv verschoben.';
    exit;
  } 
    
  // B�����SER BUBE versucht zu hacken:
  $text = 'Archivieren der HF-ID ('.$hf_id.') von sou_user_id ';
  if($_SESSION[sou_user_id]) {
    $text.= $_SESSION[sou_user_id].'\r\n';
  } else {
    $text.= 'unbekannt. \r\n';
  }
  $text.= "IP-Adresse lautet ".$_SERVER[REMOTE_ADDR];
  session_destroy();
  @mail($GLOBALS['env_admin_email'],"HYPERFUNK-HACK EA/SOU",$text,"FROM: ".$GLOBALS['env_admin_email']);
  //@include"iknowwhatyoudolastspring"; // spy die mail, isso in webstatistik die Anzahl der aufrufe f�r iknowwhatyoudolastspring ;-) 
  exit;
}

// L�SCHEN einer HF:
if(is_numeric($_GET[hfdelete])) {
  $hf_id = $_GET[hfdelete];
  
  $query = "
    SELECT * FROM sou_user_hyper WHERE id = $hf_id
  ";
  $result = mysql_query($query, $soudb);
  $data_hf = mysql_fetch_array($result);
  
  if($_SESSION[sou_user_id] == $data_hf[empfaenger]) {
    mysql_query("DELETE FROM sou_user_hyper WHERE id = $hf_id", $soudb);
    echo 'Die Hyperfunknachricht wurde gel&ouml;scht.';
    exit;
  } 
  
  if($_SESSION[sou_user_id] == $data_hf[absender]) {
    mysql_query("DELETE FROM sou_user_hyper WHERE id = $hf_id", $soudb);
    echo 'Die Hyperfunknachricht wurde gel&ouml;scht.';
    exit;
  }

  // B�����SER BUBE versucht zu hacken:
  $text = 'L�schen der HF-ID ('.$hf_id.') von sou_user_id ';
  if($_SESSION[sou_user_id]) {
    $text.= $_SESSION[sou_user_id].'\r\n';
  } else {
    $text.= 'unbekannt. \r\n';
  }
  $text.= "IP-Adresse lautet ".$_SERVER[REMOTE_ADDR];
  session_destroy();
  @mail($GLOBALS['env_admin_email'],"HYPERFUNK-HACK EA/SOU",$text,"FROM: ".$GLOBALS['env_admin_email']);
  exit;
}

// VERSENDEN einer HF:
if($_REQUEST[hfnempf]!="") {
	//test auf com-sperre
	$akttime=date("Y-m-d H:i:s",time());
	$db_daten=mysql_query("SELECT com_sperre FROM de_login WHERE user_id='$ums_user_id'",$db);
	$row = mysql_fetch_array($db_daten);
	if($row['com_sperre']>$akttime){
		$sperrtime=strtotime($row['com_sperre']);
		echo('<div style="margin-bottom: 5px; font-size: 16px; font-weight: bold; color: #FF0000;">Account: Sperre f&uuml;r ausgehende Kommunikation bis: '.date("d.m.Y - G:i", $sperrtime).'</div>');
	}else{
	
		$spielerid = $_REQUEST[hfnempf];
		$betreff = substr($_REQUEST[hfnbetr],0,50);
		$text = $_REQUEST['hfntext'];

		$text=htmlspecialchars($text, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');

		$betreff = umlaut($betreff);
		$text = umlaut($text);
		//$betreff = umlaut($betreff);
		if($betreff=='')$betreff='[kein Betreff]';
		if($text=='') { $fehler = 1; }
		$hfndate = date("Y-m-d H:i:s");
		$array = explode(" ",$_SESSION[sou_spielername]);
		$fromnic = $array[0];
		if($_REQUEST[hfnempf]=="Fraktions-HF") {
		  // FRAKTIONS-HF:
		  $status = "10010";
		  $boese_woerter = substr_count (strtolower($text),"arschloch");
		  if($boese_woerter > 0) {
			$status = "00011"; 
		  }
		  $query = "
			SELECT user_id FROM sou_user_data WHERE fraction = $_SESSION[sou_fraction]
		  ";
		  $result = mysql_query($query, $soudb);
		  while ($data = mysql_fetch_array($result)) {
			// f�r empf�nger:
			$query = "
			  INSERT INTO sou_user_hyper 
			  SET empfaenger = '$data[user_id]' , 
			  absender = '$_SESSION[sou_user_id]' , 
			  fromnic = '$fromnic' , 
			  status = '$status' , 
			  date = '$hfndate' , 
			  betreff = '$betreff' , 
			  text = '$text'
			";
			//echo $query;
			mysql_query($query, $soudb);
		  }
		} else {
		  // SPIELER-HF:
		  $status = "10000"; $status2 = "01000";
		  $boese_woerter = substr_count (strtolower($text),"arschloch");
		  if($boese_woerter > 0) {
			$status = "10001"; $status2 = "01001";
		  }
		  $query3 = "SELECT user_id FROM sou_user_data WHERE spielername = '$_REQUEST[hfnempf]'";
		  $fuerspieler = mysql_result(mysql_query($query3, $soudb),0);
		  // f�r empf�nger:
		  $query = "
			INSERT INTO sou_user_hyper 
			SET empfaenger = '$fuerspieler' , 
			absender = '$_SESSION[sou_user_id]' , 
			fromnic = '$fromnic' , 
			status = '$status' , 
			date = '$hfndate' , 

			betreff = '$betreff' , 
			text = '$text'
		  ";
		  //echo $query;
		  mysql_query($query, $soudb);
		  // f�r postausgang absender:
		  $query = "
			INSERT INTO sou_user_hyper 
			SET empfaenger = '$fuerspieler' , 
			absender = '$_SESSION[sou_user_id]' , 
			fromnic = '$fromnic' , 
			status = '$status2' , 
			date = '$hfndate' , 
			betreff = '$betreff' , 
			text = '$text'
		  ";
		  //echo $query;
		  mysql_query($query, $soudb);
		}
		// Best�tigung:
		echo '
		  <b>Die Hyperfunknachricht wurde &uuml;bertragen.</b>
		';
		exit;
	}
}

if(is_numeric($_GET[takeempf])) { // �bername Spielername in HFN
  if($_GET[takeempf]==-13) { echo 'Fraktions-HF'; exit; } // Falls nur die Frakion angeschrieben wird ;-)
  $spielername = mysql_result(mysql_query("SELECT spielername from sou_user_data WHERE user_id = '$_GET[takeempf]'", $soudb),0);
  if($spielername != "") { echo $spielername; }
  exit;
}
if($_GET[ilde]=="empf") { // automatische-auswahl
  echo '
    Auswahl: <select name="empfaenger_auswahl" size="1" onChange="takeadress(this.options[this.selectedIndex].value);  document.getElementById(\'hfnsenden\').disabled = false; ">
      <option value=""> <-- ausw&auml;hlen --> </option>
  ';
  $suchtext = trim($_GET[empf]) . "%";
  $db_daten=mysql_query("SELECT user_id, spielername, fraction FROM `sou_user_data`  WHERE fraction > 0 and spielername like '$suchtext' ORDER BY spielername", $soudb);
  while ($row = mysql_fetch_array($db_daten)) {
    echo '
      <option value="'.$row[user_id].'"> '.umlaut($row[spielername]).' ['.$row[fraction].']</option>
    ';
  }
  echo '
    </select>
  ';
  exit;
}

// Auf eine HF antworten:
if(is_numeric($_GET[hfreplay]) and $_GET[hfreplay] > 0) {
  if(!$_SESSION[sou_user_id]) { echo "b�ser bub"; exit; }
  // Legitimation pr�fen:
  $query = "
    SELECT empfaenger
    FROM sou_user_hyper 
    WHERE id = '$_GET[hfreplay]'
  ";
  $result = mysql_query($query, $soudb);
  $legitimation = mysql_result($result, 0);
  if($legitimation == $_SESSION[sou_user_id]) {
    $legitimiert = "yes_sir";
  } else {
    // B�����SER BUBE versucht zu hacken:
    $text = 'Antworten der HF-ID ('.$_GET[hfreplay].') von sou_user_id ';
    if($_SESSION[sou_user_id]) {
      $text.= $_SESSION[sou_user_id].'\r\n';
    } else {
      $text.= 'unbekannt. \r\n';
    }
    $text.= "IP-Adresse lautet ".$_SERVER[REMOTE_ADDR];
    session_destroy();
    mail($GLOBALS['env_admin_email'],"HYPERFUNK-HACK EA/SOU",$text,"FROM: ".$GLOBALS['env_admin_email']);
    //@include"iknowwhatyoudolastmonth"; // spy die mail, isso in webstatistik die Anzahl der aufrufe f�r iknowwhatyoudolastmonth ;-)
    exit;
  }
  if($legitimiert == "yes_sir") {
    // Datenabfrage:
    $query = "
      SELECT *, DATE_FORMAT(date, '%d.%m.%Y') as datum , DATE_FORMAT(date, '%H:%i') as time     
      FROM sou_user_hyper 
      WHERE id = '$_GET[hfreplay]'
    ";
    $hfmessagedaten = mysql_query($query, $soudb);
    while ($row = mysql_fetch_array($hfmessagedaten)) {
      // Existiert der Spieler noch ?
      $query_existent = "SELECT count(user_id) AS vorhanden FROM sou_user_data WHERE spielername = '$row[fromnic]'";
      $result_existent = mysql_query($query_existent, $soudb);
      $existent = mysql_fetch_array($result_existent);
      if($existent[vorhanden] == 0) {
        echo '
          <b>Das Hyperfunksystem meldet, dass "'.$row[fromnic].'" durch die DX61a23 eliminiert wurde!<br /><br />
          Eine neue Hyperfunknachricht ist daher nicht zustellbar.</b><br />
        ';
        exit;
      }
      
      /* italics */
      $row[text] = str_replace('[i]', '<i>', $row[text]);
      $row[text] = str_replace('[/i]', '</i>', $row[text]);
      /* bold */
      $row[text] = str_replace('[b]', '<b>', $row[text]);
      $row[text] = str_replace('[/b]', '</b>', $row[text]);
      // Umformatierungen des Textes:
      $text = umlaut($row[text]);
      $text = str_replace("<","[",$text);
      $text = str_replace(">","]",$text);
      $text = str_replace("[br]","",$text);
      
      echo '
        <table cellspacing="2" cellpadding="2">
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr><tr>
          <td><b>An:</b></td>
          <td><span id="hf_empf">'.umlaut($row[fromnic]).'</span></td>
        </tr><tr>
          <td><b>Betreff:</b></td>
          <td><input type="text" name="hf_betr" id="hf_betr" value="AW: '.umlaut($row[betreff]).'" style="width: 400px;" /></td>
        </tr><tr>
          <td valign="top"><b>Nachricht:</b></td>
          <td><textarea name="hf_mess" id="hf_mess" style="height: 200px; width: 400px; background-color: #000000; color: #FFFFFF; border: 1px solid #FFFFFF;">';
      echo '---------------------------
Urspr&uuml;ngliche HF von: 
'.umlaut($row[fromnic]).' ('.$row[datum].' '.substr($row[time],0,5).' Uhr)
---------------------------
';
      echo '[i]'.$text.'[/i]</textarea>';
        echo '
            </td>
          </tr><tr>
            <td>&nbsp;</td>
          </tr><tr>
            <td><input type="submit" id="hfnsenden" value="senden" onclick="message_senden();" style="cursor: pointer;" /></td>
          </tr>
        </table>
      ';
    }
  }
  exit;
}

if($_GET['id'] > 0 and is_numeric($_GET['id'])) {
  // HF anzeigen lassen:
  $query = "
    SELECT empfaenger
    FROM sou_user_hyper 
    WHERE id = '$_GET[id]'
  ";
  $result = mysql_query($query, $soudb);
  $legitimation = mysql_result($result, 0);
  if($legitimation == $_SESSION[sou_user_id]) {
    $legitimiert = "yes_sir";
    $empfaenger = mysql_result(mysql_query("SELECT spielername FROM sou_user_data WHERE user_id = $legitimation", $soudb),0);
  } else {
    $query = "
      SELECT absender
      FROM sou_user_hyper 
      WHERE id = '$_GET[id]'
    ";
    $result = mysql_query($query, $soudb);
    $legitimation = mysql_result($result, 0);
    if($legitimation == $_SESSION[sou_user_id]) {
      $legitimiert = "yes_sir";
    }
  }
  
  // Hat der user die Legitimation zum Lesen der HF ?
  if($legitimiert == "yes_sir") {
    $query = "
      SELECT *, DATE_FORMAT(date, '%d.%m.%Y') as datum , DATE_FORMAT(date, '%H:%i') as time  
      FROM sou_user_hyper 
      WHERE id = '$_GET[id]'
    ";
    $hfmessagedaten = mysql_query($query, $soudb);
    while ($row = mysql_fetch_array($hfmessagedaten)) {
      if(substr($row[status],0,1)=="1") {
        // ungelesene HF (=1....) als gelesen in DB speichern (=2....):
        $newstatus = "2" . substr($row[status],1,4);
        mysql_query("
          UPDATE sou_user_hyper 
          SET status = '$newstatus' 
          WHERE id = '$_GET[id]'
        ", $soudb);
      }
      /* italics */
      $row[text] = str_replace('[i]', '<i>', $row[text]);
      $row[text] = str_replace('[/i]', '</i>', $row[text]);
      /* bold */
      $row[text] = str_replace('[b]', '<b>', $row[text]);
      $row[text] = str_replace('[/b]', '</b>', $row[text]);
		$row[text]=nl2br($row[text]);
      echo '<input style="width: 400px;" readonly value="An: '.umlaut($empfaenger).'" /><br /><br />';
      echo '<input style="width: 400px;" readonly value="Von: '.umlaut($row[fromnic]).' ('.$row[datum].' '.substr($row[time],0,5).' Uhr)" /><br /><br />';
      echo '<input type="text" style="width:400px;" readonly value="'.umlaut($row[betreff]).'" /><br /><br />';
      echo '
        <table style="height: 200px; width:400px; background-color: #000000; color: #ffffff; border:1px solid #FFFFFF">
          <tr>
            <td valign="top">'.umlaut($row[text]).'</td>
          </tr>
        </table><br /><br />
      ';
      // Antworten nur Eingang/Archiv/Fraktion:
      if(substr($row[status],1,1) == 0) {
        echo '
          <input type="submit" value="Antworten" onClick="hf_antworten('.$_GET[id].')" style="cursor: pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        ';
      }
      // Archivieren nur Eingang:
      if(substr($row[status],0,1) > 0) {
        echo '
          <input type="submit" value="Archivieren" onClick="hf_archivieren('.$_GET[id].')" style="cursor: pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        ';
      }
      // L�schen f�r alle:
      echo '  
        <input type="submit" value="L&ouml;schen" onClick="hf_loeschen('.$_GET[id].')" style="cursor: pointer;" />
      ';
    }
  } else {
    // B�����SER BUBE versucht zu hacken:
    $text = 'Lesen der HF-ID ('.$_GET[id].') von sou_user_id ';
    if($_SESSION[sou_user_id]) {
      $text.= $_SESSION[sou_user_id].'\r\n';
    } else {
      $text.= 'unbekannt. \r\n';
    }
    $text.= "IP-Adresse lautet ".$_SERVER[REMOTE_ADDR];
    session_destroy();
    mail($GLOBALS['env_admin_email'],"HYPERFUNK-HACK EA/SOU",$text,"FROM: ".$GLOBALS['env_admin_email']);
    @include"iknowwhatyoudolastyear"; // spy die mail, isso in webstatistik die Anzahl der aufrufe f�r iknowwhatyoudolastyear ;-)
    exit;
  }
} else {
  // Neue HF schreiben lassen:
  echo '
    <table cellspacing="2" cellpadding="2">
      <tr>
        <td><u>Empf&auml;ngerauswahl:</u></td>
                <td>Eingabe: 
          <input type="text" maxlength="20" id="empfaenger_eingabe" onChange="empfeingabe();" style="text-align: center; width: 150px;" />
          <input type="submit" value="go" />
        </td>
      </tr><tr>
        <td>&nbsp;</td>
        <td><span id="empfauswahl">
          Auswahl: <select name="empfaenger_auswahl" size="1" onChange="takeadress(this.options[this.selectedIndex].value); document.getElementById(\'hfnsenden\').disabled = false; ">
            <option value=""> <-- ausw&auml;hlen --> </option>
            <option value="-13"> An alle meiner Fraktion </option>
  ';
  
  $db_daten=mysql_query("SELECT spielername, user_id, fraction FROM `sou_user_data` WHERE fraction > 0 ORDER BY spielername", $soudb);
  while ($row = mysql_fetch_array($db_daten)) {
    echo '
      <option value="'.$row[user_id].'"> '.umlaut($row[spielername]).' ['.$row[fraction].']</option>
    ';
  }
  
  echo '
          </select></span>
        </td>
      </tr>
    </table>
    <table cellspacing="2" cellpadding="2">
      <tr>
        <td colspan="2">&nbsp;<hr />&nbsp;</td>
      </tr><tr>
        <td><b>An:</b></td>
        <td><span id="hf_empf"></span></td>
      </tr><tr>
        <td><b>Betreff:</b></td>
        <td><input type="text" name="hf_betr" id="hf_betr" style="width: 400px;" /></td>
      </tr><tr>
        <td><b>Nachricht:</b></td>
        <td><textarea name="hf_mess" id="hf_mess" style="height: 200px; width: 400px; background-color: #000000; color: #FFFFFF; border: 1px solid #FFFFFF;"></textarea></td>
      </tr><tr>
        <td>&nbsp;</td>
      </tr><tr>
        <td><input type="submit" id="hfnsenden" value="senden" onclick="message_senden();" disabled="true" style="cursor: pointer;" /></td>
        <td align="right"><input type="submit" value="l&ouml;schen" onClick="" style="cursor: pointer;" /></td>
      </tr>
    </table>
  ';
}

?>
