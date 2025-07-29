<?php
ob_start();
include_once "inc/env.inc.php";
ignore_user_abort(true);

// Remove old mysql wrapper - using direct MySQLi connections

$GLOBALS['dbi'] = mysqli_connect($GLOBALS['env_db_dieewigen_host'], $GLOBALS['env_db_dieewigen_user'], $GLOBALS['env_db_dieewigen_password'], $GLOBALS['env_db_dieewigen_database']) or die("B: Keine Verbindung zur Datenbank möglich.");
$GLOBALS['dbi']->set_charset("utf8mb4");

//Accountverwaltung einbinden
$GLOBALS['dbi_ls'] = mysqli_connect($GLOBALS['env_db_loginsystem_host'], $GLOBALS['env_db_loginsystem_user'], $GLOBALS['env_db_loginsystem_password'], $GLOBALS['env_db_loginsystem_database']);
$GLOBALS['dbi_ls']->set_charset("utf8mb4");

array_walk_recursive($_GET, function (&$leaf) {
    if (is_string($leaf)) {
        $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
    }
});

array_walk_recursive($_POST, function (&$leaf) {
    if (is_string($leaf)) {
        $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
    }
});

array_walk_recursive($_COOKIE, function (&$leaf) {
    if (is_string($leaf)) {
        $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
    }
});

array_walk_recursive($_REQUEST, function (&$leaf) {
    if (is_string($leaf)) {
        $leaf = mysqli_real_escape_string($GLOBALS['dbi'], $leaf);
    }
});

foreach ($_GET as $key => $val) {
    $$key = $_GET[$key];
}

if (isset($_SESSION)) {
    foreach ($_SESSION as $key => $val) {
        if ($key != 'save_get' && $key != 'save_post' && $key != 'save_request') {
            $$key = $_SESSION[$key];
        }
    }
}

if (isset($_SESSION['ums_user_id']) && $_SESSION['ums_user_id'] > 0) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $parts = explode(".", $ip);
    $ip = $parts[0].'.x.'.($parts[2] ?? '?').'.'.($parts[3] ?? '?'); //IP anonymisieren, damit nicht jeder die IP sieht

    $ums_user_id = $_SESSION['ums_user_id'];

    //post und get-variablen mitloggen, wenn das Logging aktiviert ist
    if (isset($GLOBALS['sv_log_player_actions']) && $GLOBALS['sv_log_player_actions'] == 1) {
        //noch neueres logging
        if (!isset($_REQUEST["check4new"]) and !isset($_REQUEST["managechat"])) {
            $datenstring = '';
            $variableSets = array(
            "P:" => $_POST,
            "G:" => $_GET);

            function printElementHtml($value, $key)
            {
                global $datenstring;
                //passwörter herausfiltern
                if ($key == 'pass') {
                    $value = '****';
                }
                if ($key == 'newpass') {
                    $value = '****';
                }
                if ($key == 'oldpass') {
                    $value = '****';
                }
                if ($key == 'pass1') {
                    $value = '****';
                }
                if ($key == 'pass2') {
                    $value = '****';
                }
                if ($key == 'delpass') {
                    $value = '****';
                }
                if ($key == 'urlpass') {
                    $value = '****';
                }

                $datenstring .= $key. ": ".$value."\n";
            }

            foreach ($variableSets as $setName => $variableSet) {
                if (isset($variableSet)) {
                    $datenstring .= $setName."\n";
                    array_walk($variableSet, 'printElementHtml');
                }
            }

            if (strlen($datenstring) == 6) {
                $datenstring = '';
            }

            $scriptname = $_SERVER['PHP_SELF'];
            //Dateiendung entfernen um Platz zu sparen
            if ($scriptname[0] == '/') {
                $scriptname = substr($scriptname, 1);
            }
            $scriptname = str_replace('.php', '', $scriptname);

            $db_log = mysqli_connect($GLOBALS['env_db_logging_host'], $GLOBALS['env_db_logging_user'], $GLOBALS['env_db_logging_password'], $GLOBALS['env_db_logging_database']) or die("C: Keine Verbindung zur Datenbank möglich.");
            mysqli_execute_query($db_log, "INSERT INTO gameserverlogdata (serverid, userid, time, ip, file, getpost) VALUES(?, ?, NOW(), ?, ?, ?)", [$sv_servid, $ums_user_id, $ip, $scriptname, $datenstring]);

            unset($datenstring);
        }
    } //logging - ende


    //Auf Accountstatus prüfen, dient z.b. fürs sperren, damit er sofort rausfliegt und auch zum moven der accounts
    $db_datens = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_login.status, de_login.blocktime, de_login.activetime, de_user_data.spielername, de_user_data.rasse, de_user_data.patime, de_user_data.credits FROM de_login LEFT JOIN de_user_data ON(de_login.user_id = de_user_data.user_id) WHERE de_user_data.user_id=?", [$ums_user_id]);
    $row = mysqli_fetch_array($db_datens);
    $user_accstatus = $row["status"];
    $ad_blocktime = $row["blocktime"];
    $ic_activetime = $row["activetime"];
    $user_patime = $row["patime"];
    $user_credits = $row["credits"];
    //rasse und spielername neu setzen, wenn nötig
    if ($row['rasse'] != $_SESSION['ums_rasse'] || $row['spielername'] != $_SESSION['ums_spielername']) {
        $_SESSION['ums_rasse'] = $row['rasse'];
        $ums_rasse = $_SESSION['ums_rasse'];
        $_SESSION['ums_spielername'] = $row['spielername'];
        $ums_spielername = $_SESSION['ums_spielername'];
    }

    //wenn der status ungleich 1 ist, dann sollte der spieler nicht eingeloggt sein
    if ($user_accstatus != 1) {
        session_destroy();
        header("Location: index.php");
    }

    //pa-status festlegen
    if ($user_patime > time()) {
        $ums_premium = 1;
    } else {
        $ums_premium = 0;
    }

    if (!isset($eftachatbotdefensedisable)) {
        $eftachatbotdefensedisable = 0;
    }

    if (!isset($givenocredit)) {
        $givenocredit = 0;
    }

    //aktivitätsbonus
    if (time() > $ic_activetime + $sv_activetime and $eftachatbotdefensedisable != 1 and $givenocredit != 1) {
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET activetime=? WHERE user_id=? AND status=1", [time(), $ums_user_id]);
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data SET actpoints=actpoints+1 WHERE user_id=?", [$ums_user_id]);
    }

    if (!isset($_SESSION["aktivitaet_time"])) {
        $_SESSION["aktivitaet_time"] = 0;
    }

    //die aktivität mitloggen
    //update aus performancegründen nur alle 5 minuten
    if ($eftachatbotdefensedisable != 1) { //kein chataufruf
        //update aus performancegründen nur alle 5 minuten
        if ($_SESSION["aktivitaet_time"] + 300 < time()) {
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_click=NOW(), last_ip=? WHERE user_id=? AND status=1", [$ip, $ums_user_id]);
            $time = (int)date("H");
            $zeit = date("Y-m-d");
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_stat SET h{$time}='2' WHERE user_id=? AND datum=? AND h{$time}<'2'", [$ums_user_id, $zeit]);
            $_SESSION["aktivitaet_time"] = time();
        }
    } else { //chataufruf
        //die aktivität mitloggen
        //update aus performancegründen nur alle 5 minuten
        if (!isset($_SESSION["aktivitaet_chat_time"])) {
            $_SESSION["aktivitaet_chat_time"] = 0;
        }

        if ($_SESSION["aktivitaet_chat_time"] + 300 < time()) {
            $time = intval(date("H"));
            $zeit = date("Y-m-d");
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_stat SET h{$time}='1' WHERE user_id=? AND datum=? AND h{$time}<'1'", [$ums_user_id, $zeit]);
            $_SESSION["aktivitaet_chat_time"] = time();
        }
    }

    if ($ums_zeitstempel + 300 < time() && $eftachatbotdefensedisable != 1) { //nur alle 5 minuten aktualisieren
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_login SET last_login=NOW() WHERE user_id=? AND status=1", [$ums_user_id]);
        $ums_zeitstempel = time();
    }

}
