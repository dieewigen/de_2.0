<?php
include "../inccon.php";
include "../inc/lang/1_statistics.lang.php";
include "det_userdata.inc.php";

$uid = req_int('uid');
$mp = req_int('mp', 1);

// Historisches Session-Verhalten beibehalten: es wird kein session_start()
// aufgerufen, die Zuweisung gilt also nur für diesen Request.
$_SESSION['ums_user_id'] = $uid;

// Spielerdaten mit Prepared Statement abfragen
$result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, system, newtrans, newnews, status
     FROM de_user_data
     WHERE user_id = ?",
    [$_SESSION['ums_user_id']]
);

$row = mysqli_fetch_array($result, MYSQLI_BOTH);
$restyp01 = $row[0]; $restyp02 = $row[1]; $restyp03 = $row[2]; $restyp04 = $row[3];
$restyp05 = $row[4]; $punkte = $row["score"]; $newtrans = $row["newtrans"]; $newnews = $row["newnews"];
$sector = $row["sector"]; $system = $row["system"]; $hasally = $row["status"];

$page_title = 'Statistik';
$active_usertab = 'de_user_stat';
include "inc.layout.top.php";
include "inc.usertoolbar.php";

// Tab-Leiste (mp=1 Spieler, mp=2 Sektor, mp=3 Allianz)
$stat_tabs = [1 => $stat_lang['spieler'], 2 => $stat_lang['sektor'], 3 => $stat_lang['allianz']];
echo '<nav class="usertabs">';
foreach ($stat_tabs as $stat_mp => $stat_label) {
    echo '<a href="de_user_stat.php?uid=' . $uid . '&amp;mp=' . $stat_mp . '"' . ($mp == $stat_mp ? ' class="active"' : '') . '>' . $stat_label . '</a>';
}
echo '</nav>';

if ($mp == 1)
{
  echo '<h4>' . $stat_lang['punkteentwicklung'] . '</h4>
    <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=1">
    <h4>' . $stat_lang['kollektorentwicklung'] . '</h4>
    <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=2">';

  echo '<h4>' . $stat_lang['aktivitaet'] . '</h4>';
  echo '<table>';
  echo '<tr><th>' . $stat_lang['datum'] . '</th><th>00</th><th>01</th><th>02</th><th>03</th><th>04</th><th>05</th><th>06</th><th>07</th><th>08</th><th>09</th><th>10</th><th>11</th><th>12</th>
  <th>13</th><th>14</th><th>15</th><th>16</th><th>17</th><th>18</th><th>19</th><th>20</th><th>21</th><th>22</th><th>23</th>';
  echo '</tr>';

  //daten auslesen mit prepared statement
  $result = mysqli_execute_query(
    $GLOBALS['dbi'],
    "SELECT * FROM de_user_stat WHERE user_id = ? ORDER BY datum DESC",
    [$_SESSION['ums_user_id']]
  );

  //aktivität 0/1/2 auf css-klassen abbilden (Standard: inaktiv)
  $act_classes = [0 => 'act-none', 1 => 'act-chat', 2 => 'act-active'];

  while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
  {
    echo '<tr>';
    echo '<td>' . $row["datum"] . '</td>';
    for ($i = 0; $i <= 23; $i++)
    {
      $h_key = "h$i";
      $cls = 'act-none'; // Standard ist inaktiv
      if (isset($row[$h_key]) && isset($act_classes[$row[$h_key]])) {
        $cls = $act_classes[$row[$h_key]];
      }
      echo '<td class="' . $cls . '">&nbsp;</td>';
    }
    echo '</tr>';
  }
  //legende
  echo '<tr><td colspan="25">Legende: <span class="act-none">' . $stat_lang['legende1'] . '</span> <span class="act-chat">' . $stat_lang['legende2'] . '</span> <span class="act-active">' . $stat_lang['legende3'] . '</span></td></tr>';

  echo '</table>';
}
elseif ($mp == 2)
{
  echo '<h4>' . $stat_lang['punkteentwicklung'] . '</h4>
    <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=11">
    <h4>' . $stat_lang['kollektorentwicklung'] . '</h4>
    <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=12">
    <h4>' . $stat_lang['platzentwicklung'] . '</h4>
    <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=13">';
}
elseif ($mp == 3)
{
  //schauen ob man eine allianz hat
  if ($hasally == 1)
  {
    echo '<h4>' . $stat_lang['punkteentwicklung'] . '</h4>
      <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=21">
      <h4>' . $stat_lang['kollektorentwicklung'] . '</h4>
      <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=22">
      <h4>' . $stat_lang['platzentwicklung'] . '</h4>
      <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=23">
      <h4>' . $stat_lang['mitgliederentwicklung'] . '</h4>
      <img src="de_user_stat_genpic.php?uid=' . $uid . '&typ=24">';
  }
  else echo '<p>' . $stat_lang['noally'] . '</p>';
}

include "inc.layout.bottom.php";
