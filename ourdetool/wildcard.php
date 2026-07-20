<?php
include "../inccon.php";
include "det_userdata.inc.php";

// Suchstring; Präfixzeichen bestimmt das Suchfeld, de_user_search.php
// übersetzt vorab %->$ (Wildcard-Logik unverändert beibehalten)
$sstr = req_str('sstr');

$page_title = 'Suche';
include "inc.layout.top.php";

echo '<table>';
echo '<tr>';
echo '<th>UserID</th>';
echo '<th>Suche</th>';
echo '</tr>';

$UCount = 0;
if ($sstr != '') {
    switch ($sstr[0]) {
        case '-': //nic
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, nic FROM de_login WHERE nic LIKE ?",
                ['%' . $sstr . '%']
            );

            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["nic"]) . '</td></tr>';
                $UCount++;
            }

            break;
        case '*': //spielername
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, spielername FROM de_user_data WHERE spielername LIKE ?",
                ['%' . $sstr . '%']
            );

            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["spielername"]) . '</td></tr>';
                $UCount++;
            }

            break;
        case '$': //email-adresse
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, reg_mail FROM de_login WHERE reg_mail LIKE ?",
                ['%' . $sstr . '%']
            );

            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["reg_mail"]) . '</td></tr>';
                $UCount++;
            }

            break;

        case '~': //IP
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, last_ip FROM de_login WHERE last_ip LIKE ? ORDER BY last_ip",
                ['%' . $sstr . '%']
            );
            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["last_ip"]) . '</td></tr>';
                $UCount++;
            }

            break;

        case '|': //Vor-/Nachname
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, vorname, nachname FROM de_user_info WHERE vorname LIKE ? OR nachname LIKE ?",
                ['%' . $sstr . '%', '%' . $sstr . '%']
            );
            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["vorname"]) . ' ' . htmlspecialchars($UData["nachname"]) . '</td></tr>';
                $UCount++;
            }
            break;
        case 'ö': //Ort (korrigiertes Zeichen)
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query(
                $GLOBALS['dbi'],
                "SELECT user_id, ort FROM de_user_info WHERE ort LIKE ?",
                ['%' . $sstr . '%']
            );
            while ($UData = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr><td><a href="idinfo.php?UID=' . htmlspecialchars($UData["user_id"]) . '" target="_blank" rel="noopener">' . htmlspecialchars($UData["user_id"]) . '</a></td>';
                echo '<td>' . htmlspecialchars($UData["ort"]) . '</td></tr>';
                $UCount++;
            }

        default:
            break;
    }//switch sstr ende
}

echo '</table><br>' . $UCount . ' User gefunden<br>';

include "inc.layout.bottom.php";
