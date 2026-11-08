<?php
/**
 * User-Suche: reiner Router ohne HTML-Ausgabe.
 * Die Suchleiste selbst liefert inc.usertoolbar.php auf jeder User-Seite.
 */
include "../inccon.php";
include "det_userdata.inc.php";

$sstr = req_str('sstr');

//schauen wonach gesucht wird
//+ user_id
//- nic
//* spielername
//% Mail
//k Koords
//~ IP
//| Vor-/Nachname
//? Ort

if ($sstr != '') {
    switch ($sstr[0]) {
        case '+': //user_id
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=?", [$sstr]);
            $row = mysqli_fetch_array($result);
            $sstr = $row["user_id"] ?? '';
            break;
        case '*': //spielername
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE spielername=?", [$sstr]);
            $row = mysqli_fetch_array($result);
            $sstr = $row["user_id"] ?? '';
            break;
        case '%': //email-adresse
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE reg_mail=?", [$sstr]);
            $row = mysqli_fetch_array($result);
            $sstr = $row["user_id"] ?? '';
            break;
        case 'k': //koordinaten
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            list($sec, $sys) = explode(":", $sstr);
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? AND system=?", [$sec, $sys]);
            $row = mysqli_fetch_array($result);
            $sstr = $row["user_id"] ?? '';
            break;
        case '?': //wildcard suche
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $sstr = str_replace("%", "$", $sstr);
            header('Location: wildcard.php?sstr=' . urlencode($sstr));
            exit;
        default: //user_id
            $sstr = str_replace($sstr[0] . $sstr[1], $sstr[1], $sstr);
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE user_id=?", [$sstr]);
            $row = mysqli_fetch_array($result);
            $sstr = $row["user_id"] ?? '';
            break;
    }//switch sstr ende
}

if ($sstr == '') {
    if (req_str('sstr') === '') {
        header('Location: index.php');
    } else {
        header('Location: index.php?notfound=1&q=' . urlencode(req_str('sstr')));
    }
    exit;
}

header('Location: info.php?uid=' . (int)trim($sstr));
exit;
