<?php
// Funktionen einbinden:
include "soudata/source/sou_hyperfunk.inc.php";

//daten zur ansicht
echo '<span id="hfsys"><br /></span>';

echo '<div align="center">';

rahmen0_oben();

echo '<br />';

echo '
  <table width="99%">
    <tr>
      <td valign="top" width="330">
';

rahmen1_oben('<div align="center"><b>Hyperfunk-Verwaltung</b></div>');

// linke Tabelle öffnen:
echo '
  <span id="hyperfunk_folder">
  </span>
';

  echo '
    <script language="javascript">
    gotofolder(1);
    </script>
  ';


rahmen1_unten();

echo '
      </td><td valign="top">
';

// Anzeige der Hyperfunknachricht:
$routput='<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr align="center">
<td width="120">&nbsp;</td>
<td><b>Hyperfunknachricht</b></td>
<td width="120"><a href="sou_main.php?action=systempage"><div class="b1">System</div></a></td>
</tr></table>';
rahmen1_oben($routput);

echo '
  <br /><span id="hyperfunk_box"></span><br /><br />
';

rahmen1_unten();

echo '
      </td>
    </tr>
  </table>
';   

/* 
    <tr>
      <td colspan="2" width="100%">


// Hyperfunknachricht schreiben:
rahmen1_oben('<div align="center"><b>Hyperfunk</b></div>');

echo '
  <table width="100%">
    <tr>
      <td align="center"><u>Empf&auml;nger</u></td>
    </tr>
    <tr>
      <td>Eingabe: <input type="text" maxlength="20" id="empfaenger_eingabe" onChange="" style="text-align: center; width: 150px;" /></td>
    </tr>
    <tr>
      <td>
        Auswahl: <select name="empfaenger_auswahl" size="1">
';

$db_daten=mysql_query("SELECT spielername FROM `sou_user_data` ORDER BY spielername", $soudb);
while ($row = mysql_fetch_array($db_daten)) {
  echo '
    <option value="'.$row[spielername].'"> '.$row[spielername].' </option>
  ';
}

echo '
        </select>
      </td>
    </tr>
';    

rahmen1_unten();
*/

echo '
      </td>
    </tr>
  </table>
';


// Abschluss:

echo '<br />';

rahmen0_unten();

echo '<br />';

echo '</div>';//center-div

/*
echo '
  <p style="font-family: Verdana; font-size: 10px;">
  ea-HF Version 8.04.20a &copy; Die Ewigen - Bearbeiter:  SPY  
  </p>
';
*/

die('</body></html>');
?>
